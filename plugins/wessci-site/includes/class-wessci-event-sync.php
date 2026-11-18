<?php
/**
 * Import science events from the Wesleyan events calendar into the Events post type.
 *
 * Imported events arrive as drafts. Editors publish, edit, or trash them in
 * wp-admin. A field an editor changes is kept on later syncs, and a trashed
 * event is not imported again.
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WesSci_Event_Sync {

	const HOOK          = 'wessci_event_sync';
	const FEED          = 'https://events.wesleyan.edu/live/json/events';
	const SOURCE_KEY    = '_wessci_event_source_id';
	const SYNCED_KEY    = '_wessci_event_synced';
	const OVERRIDES_KEY = '_wessci_event_overrides';
	const MAX_PAGES     = 10;
	const FIELDS        = array( 'title', 'content', 'start', 'end', 'department', 'location', 'url' );
	// Feed tag => label shown as the event's department.
	const TAGS          = array(
		'Astronomy'                                                   => 'Astronomy',
		'Planetary Science'                                           => 'Planetary Science',
		'College of the Environment Bailey College of the Environment' => 'College of the Environment',
	);

	private static $syncing = false;

	public static function init() {
		add_action( 'init', array( __CLASS__, 'schedule' ) );
		add_action( self::HOOK, array( __CLASS__, 'sync' ) );
		add_action( 'save_post_wessci_event', array( __CLASS__, 'record_overrides' ), 20 );
	}

	public static function schedule() {
		if ( ! wp_next_scheduled( self::HOOK ) ) {
			wp_schedule_event( time(), 'twicedaily', self::HOOK );
		}
	}

	/**
	 * Fetch every configured tag, then create, update, or retire events.
	 *
	 * @return int|WP_Error Number of feed events handled, or the fetch error.
	 */
	public static function sync() {
		$events = self::fetch();
		if ( is_wp_error( $events ) ) {
			error_log( 'WesSci event sync failed: ' . $events->get_error_message() );
			return $events;
		}
		self::$syncing = true;
		$active        = array();
		foreach ( $events as $event ) {
			$item = self::map_event( $event );
			if ( ! $item ) {
				continue;
			}
			if ( $item['active'] ) {
				$active[] = $item['source_id'];
			}
			self::upsert( $item );
		}
		self::retire_missing( $active );
		self::$syncing = false;
		return count( $events );
	}

	/**
	 * @return array|WP_Error Feed events keyed by feed ID, so an event under two tags is kept once.
	 */
	public static function fetch() {
		$from   = wp_date( 'Y-m-d' );
		$to     = wp_date( 'Y-m-d', strtotime( '+1 year' ) );
		$fields = 'id,title,url,date_iso,date2_iso,is_canceled,status,location,summary,tags';
		$found  = array();
		foreach ( array_keys( self::TAGS ) as $tag ) {
			$url = sprintf( '%s/tag/%s/response_fields/%s/max/100/start_date/%s/end_date/%s', self::FEED, rawurlencode( $tag ), $fields, $from, $to );
			for ( $page = 0; $url && $page < self::MAX_PAGES; $page++ ) {
				$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
				if ( is_wp_error( $response ) ) {
					return $response;
				}
				$body = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) || ! is_array( $body ) || ! isset( $body['data'] ) || ! is_array( $body['data'] ) ) {
					return new WP_Error( 'wessci_event_feed', 'Unexpected response for tag ' . $tag );
				}
				foreach ( $body['data'] as $event ) {
					if ( isset( $event['id'] ) ) {
						$found[ (int) $event['id'] ] = $event;
					}
				}
				$next = isset( $body['links']['next'] ) ? $body['links']['next'] : '';
				$url  = is_string( $next ) && 0 === strpos( $next, self::FEED . '/' ) ? $next : '';
			}
		}
		return $found;
	}

	/**
	 * Convert one feed event to the plugin's event fields.
	 *
	 * @return array|null Null when the event has no ID or no valid start.
	 */
	public static function map_event( array $event ) {
		$start = isset( $event['date_iso'] ) ? self::to_utc( $event['date_iso'] ) : '';
		if ( empty( $event['id'] ) || '' === $start ) {
			return null;
		}
		$end = isset( $event['date2_iso'] ) ? self::to_utc( $event['date2_iso'] ) : '';
		if ( $end && $end < $start ) {
			$end = '';
		}
		$tags       = isset( $event['tags'] ) && is_array( $event['tags'] ) ? $event['tags'] : array();
		$department = '';
		foreach ( self::TAGS as $tag => $label ) {
			if ( in_array( $tag, $tags, true ) ) {
				$department = $label;
				break;
			}
		}
		$title = isset( $event['title'] ) ? wp_strip_all_tags( html_entity_decode( $event['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) : '';
		$url   = isset( $event['url'] ) ? esc_url_raw( $event['url'] ) : '';
		return array(
			'source_id' => 'livewhale:' . (int) $event['id'],
			'active'    => empty( $event['is_canceled'] ) && 1 === (int) ( isset( $event['status'] ) ? $event['status'] : 1 ),
			'fields'    => array(
				'title'      => trim( $title ),
				'content'    => isset( $event['summary'] ) ? trim( wp_kses_post( $event['summary'] ) ) : '',
				'start'      => $start,
				'end'        => $end,
				'department' => $department,
				'location'   => isset( $event['location'] ) ? sanitize_text_field( $event['location'] ) : '',
				'url'        => $url,
			),
		);
	}

	/**
	 * Convert one WesWAVES (EventPrime) export event to the plugin's event fields.
	 * The export has no venue field, so the location stays in the description.
	 *
	 * @return array|null Null when the event has no ID or no valid start.
	 */
	public static function map_weswaves_event( array $event ) {
		$start = isset( $event['em_start_date_time'] ) && ctype_digit( (string) $event['em_start_date_time'] ) ? gmdate( 'Y-m-d H:i:s', (int) $event['em_start_date_time'] ) : '';
		if ( empty( $event['id'] ) || '' === $start ) {
			return null;
		}
		$end = isset( $event['em_end_date_time'] ) && ctype_digit( (string) $event['em_end_date_time'] ) ? gmdate( 'Y-m-d H:i:s', (int) $event['em_end_date_time'] ) : '';
		if ( $end && $end < $start ) {
			$end = '';
		}
		$organizer  = isset( $event['organizer_object'][0]['name'] ) ? (string) $event['organizer_object'][0]['name'] : '';
		$department = sanitize_text_field( '' !== $organizer ? $organizer : ( isset( $event['event_type'] ) ? (string) $event['event_type'] : '' ) );
		$title      = isset( $event['title'] ) ? wp_strip_all_tags( html_entity_decode( $event['title'], ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) : '';
		return array(
			'source_id' => 'weswaves:' . (int) $event['id'],
			'active'    => true,
			'fields'    => array(
				'title'      => trim( $title ),
				'content'    => isset( $event['desc'] ) ? trim( wp_kses_post( $event['desc'] ) ) : '',
				'start'      => $start,
				'end'        => $end,
				'department' => $department,
				'location'   => '',
				'url'        => isset( $event['link'] ) ? esc_url_raw( $event['link'] ) : '',
			),
		);
	}

	/**
	 * Import upcoming events from a WesWAVES export as drafts. There is no live
	 * feed, so nothing is retired. An event already imported from another source
	 * (same title on the same New York day) is skipped.
	 *
	 * @return array Counts: imported, skipped_past, skipped_duplicate, skipped_invalid.
	 */
	public static function import_weswaves( array $events, $now = null ) {
		$now    = null === $now ? gmdate( 'Y-m-d H:i:s' ) : $now;
		$counts = array( 'imported' => 0, 'skipped_past' => 0, 'skipped_duplicate' => 0, 'skipped_invalid' => 0 );
		self::$syncing = true;
		foreach ( $events as $event ) {
			$item = is_array( $event ) ? self::map_weswaves_event( $event ) : null;
			if ( ! $item ) {
				$counts['skipped_invalid']++;
				continue;
			}
			if ( $item['fields']['start'] < $now ) {
				$counts['skipped_past']++;
				continue;
			}
			if ( self::duplicate_elsewhere( $item ) ) {
				$counts['skipped_duplicate']++;
				continue;
			}
			self::upsert( $item );
			$counts['imported']++;
		}
		self::$syncing = false;
		return $counts;
	}

	private static function duplicate_elsewhere( array $item ) {
		$key = self::duplicate_key( $item['fields']['title'], $item['fields']['start'] );
		$ids = get_posts( array(
			'post_type'      => 'wessci_event',
			'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private' ),
			'meta_key'       => self::SOURCE_KEY,
			'posts_per_page' => -1,
			'fields'         => 'ids',
		) );
		foreach ( $ids as $id ) {
			if ( get_post_meta( $id, self::SOURCE_KEY, true ) === $item['source_id'] ) {
				continue;
			}
			if ( self::duplicate_key( get_post_field( 'post_title', $id ), (string) get_post_meta( $id, '_wessci_event_start', true ) ) === $key ) {
				return true;
			}
		}
		return false;
	}

	private static function duplicate_key( $title, $utc_start ) {
		$title = strtolower( preg_replace( '/[^a-z0-9]+/i', '', html_entity_decode( (string) $title, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) ) );
		try {
			$day = ( new DateTimeImmutable( $utc_start, new DateTimeZone( 'UTC' ) ) )->setTimezone( new DateTimeZone( 'America/New_York' ) )->format( 'Y-m-d' );
		} catch ( Exception $error ) {
			$day = $utc_start;
		}
		return $title . '|' . $day;
	}

	private static function to_utc( $iso ) {
		if ( ! is_string( $iso ) || '' === $iso ) {
			return '';
		}
		try {
			$date = new DateTimeImmutable( $iso );
		} catch ( Exception $error ) {
			return '';
		}
		return $date->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' );
	}

	private static function find( $source_id ) {
		$posts = get_posts( array(
			'post_type'      => 'wessci_event',
			'post_status'    => array( 'publish', 'draft', 'pending', 'future', 'private', 'trash' ),
			'meta_key'       => self::SOURCE_KEY,
			'meta_value'     => $source_id,
			'posts_per_page' => 1,
			'fields'         => 'ids',
		) );
		return $posts ? (int) $posts[0] : 0;
	}

	private static function upsert( array $item ) {
		$post_id = self::find( $item['source_id'] );
		if ( $post_id && 'trash' === get_post_status( $post_id ) ) {
			return;
		}
		if ( ! $item['active'] ) {
			if ( $post_id ) {
				self::unpublish( $post_id );
			}
			return;
		}
		if ( ! $post_id ) {
			$post_id = wp_insert_post( array(
				'post_type'    => 'wessci_event',
				'post_status'  => 'draft',
				'post_title'   => $item['fields']['title'],
				'post_content' => $item['fields']['content'],
			) );
			if ( ! $post_id || is_wp_error( $post_id ) ) {
				return;
			}
			update_post_meta( $post_id, self::SOURCE_KEY, $item['source_id'] );
			$overrides = array();
			$snapshot  = array();
		} else {
			$overrides = get_post_meta( $post_id, self::OVERRIDES_KEY, true );
			$snapshot  = get_post_meta( $post_id, self::SYNCED_KEY, true );
			$overrides = is_array( $overrides ) ? $overrides : array();
			$snapshot  = is_array( $snapshot ) ? $snapshot : array();
		}
		self::apply( $post_id, $item['fields'], $overrides );
		$current = self::current_fields( $post_id );
		foreach ( self::FIELDS as $key ) {
			if ( ! in_array( $key, $overrides, true ) ) {
				$snapshot[ $key ] = $current[ $key ];
			}
		}
		update_post_meta( $post_id, self::SYNCED_KEY, $snapshot );
		update_post_meta( $post_id, self::OVERRIDES_KEY, $overrides );
	}

	private static function apply( $post_id, array $fields, array $overrides ) {
		$post = array( 'ID' => $post_id );
		if ( ! in_array( 'title', $overrides, true ) ) {
			$post['post_title'] = $fields['title'];
		}
		if ( ! in_array( 'content', $overrides, true ) ) {
			$post['post_content'] = $fields['content'];
		}
		if ( count( $post ) > 1 ) {
			wp_update_post( $post );
		}
		foreach ( array( 'start', 'end', 'department', 'location', 'url' ) as $key ) {
			if ( in_array( $key, $overrides, true ) ) {
				continue;
			}
			'' === $fields[ $key ] ? delete_post_meta( $post_id, '_wessci_event_' . $key ) : update_post_meta( $post_id, '_wessci_event_' . $key, $fields[ $key ] );
		}
	}

	private static function current_fields( $post_id ) {
		$fields = array(
			'title'   => (string) get_post_field( 'post_title', $post_id ),
			'content' => (string) get_post_field( 'post_content', $post_id ),
		);
		foreach ( array( 'start', 'end', 'department', 'location', 'url' ) as $key ) {
			$fields[ $key ] = (string) get_post_meta( $post_id, '_wessci_event_' . $key, true );
		}
		return $fields;
	}

	private static function unpublish( $post_id ) {
		if ( in_array( get_post_status( $post_id ), array( 'publish', 'pending', 'future' ), true ) ) {
			wp_update_post( array( 'ID' => $post_id, 'post_status' => 'draft' ) );
		}
	}

	/**
	 * Hide upcoming imported events that the feed no longer lists.
	 * Past events stay as they are.
	 */
	private static function retire_missing( array $active ) {
		$posts = get_posts( array(
			'post_type'      => 'wessci_event',
			'post_status'    => array( 'publish', 'pending', 'future' ),
			'meta_key'       => self::SOURCE_KEY,
			'posts_per_page' => -1,
		) );
		$now = gmdate( 'Y-m-d H:i:s' );
		foreach ( $posts as $post ) {
			$start = (string) get_post_meta( $post->ID, '_wessci_event_start', true );
			if ( $start >= $now && ! in_array( get_post_meta( $post->ID, self::SOURCE_KEY, true ), $active, true ) ) {
				self::unpublish( $post->ID );
			}
		}
	}

	/**
	 * After an editor saves an imported event, remember which fields differ
	 * from the last synced values so the next sync leaves them alone.
	 */
	public static function record_overrides( $post_id ) {
		$snapshot = get_post_meta( $post_id, self::SYNCED_KEY, true );
		if ( self::$syncing || wp_is_post_revision( $post_id ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! is_array( $snapshot ) ) {
			return;
		}
		$current   = self::current_fields( $post_id );
		$overrides = array();
		foreach ( self::FIELDS as $key ) {
			if ( isset( $snapshot[ $key ] ) && $current[ $key ] !== $snapshot[ $key ] ) {
				$overrides[] = $key;
			}
		}
		update_post_meta( $post_id, self::OVERRIDES_KEY, $overrides );
	}
}
