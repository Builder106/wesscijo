<?php
/** Check the events importer against canned feed pages and an in-memory post store. */
if ( defined( 'ABSPATH' ) ) {
	exit( "Use this script outside WordPress.\n" );
}
define( 'ABSPATH', __DIR__ );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );

$posts = array();
$meta = array();
$next_id = 100;
$scheduled = array();
$requests = array();
$pages = array();
$actions = array();

class WP_Error {
	private $message;
	public function __construct( $code = '', $message = '' ) { $this->message = $message; }
	public function get_error_message() { return $this->message; }
}
function is_wp_error( $value ) { return $value instanceof WP_Error; }
function add_action( $name, $callback, $priority = 10 ) { $GLOBALS['actions'][ $name ] = array( $callback, $priority ); }
function wp_next_scheduled( $hook ) { return isset( $GLOBALS['scheduled'][ $hook ] ); }
function wp_schedule_event( $time, $recurrence, $hook ) { $GLOBALS['scheduled'][ $hook ] = $recurrence; }
function wp_date( $format, $timestamp = null ) { return gmdate( $format, null === $timestamp ? time() : $timestamp ); }
function wp_strip_all_tags( $text ) { return trim( strip_tags( $text ) ); }
function wp_kses_post( $text ) { return strip_tags( $text, '<p><a><em><strong>' ); }
function esc_url_raw( $url ) { return preg_match( '~^https?://~', $url ) ? $url : ''; }
function sanitize_text_field( $text ) { return trim( strip_tags( $text ) ); }
function wp_is_post_revision( $id ) { return false; }
function wp_remote_get( $url, $args = array() ) {
	$GLOBALS['requests'][] = $url;
	$page = array_shift( $GLOBALS['pages'] );
	return $page instanceof WP_Error ? $page : array( 'code' => $page['code'], 'body' => $page['body'] );
}
function wp_remote_retrieve_body( $response ) { return $response['body']; }
function wp_remote_retrieve_response_code( $response ) { return $response['code']; }
function get_post_status( $id ) { return $GLOBALS['posts'][ $id ]['post_status'] ?? false; }
function get_post_field( $field, $id ) { return $GLOBALS['posts'][ $id ][ $field ] ?? ''; }
function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['meta'][ $id ][ $key ] ?? ''; }
function update_post_meta( $id, $key, $value ) { $GLOBALS['meta'][ $id ][ $key ] = $value; }
function delete_post_meta( $id, $key ) { unset( $GLOBALS['meta'][ $id ][ $key ] ); }
function wp_insert_post( $post ) {
	$id = $GLOBALS['next_id']++;
	$GLOBALS['posts'][ $id ] = $post + array( 'ID' => $id );
	return $id;
}
function wp_update_post( $post ) {
	$GLOBALS['posts'][ $post['ID'] ] = $post + $GLOBALS['posts'][ $post['ID'] ];
	return $post['ID'];
}
function get_posts( $args ) {
	$found = array();
	foreach ( $GLOBALS['posts'] as $id => $post ) {
		$source = $GLOBALS['meta'][ $id ]['_wessci_event_source_id'] ?? null;
		if ( null === $source || ! in_array( $post['post_status'], $args['post_status'], true ) ) {
			continue;
		}
		if ( isset( $args['meta_value'] ) && $source !== $args['meta_value'] ) {
			continue;
		}
		$found[] = 'ids' === ( $args['fields'] ?? '' ) ? $id : (object) $post;
	}
	return $found;
}
function check_event( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}
function feed_page( array $events, $next = null ) {
	return array( 'code' => 200, 'body' => json_encode( array( 'data' => $events, 'links' => array( 'next' => $next ) ) ) );
}
function feed_event( $id, array $over = array() ) {
	return $over + array(
		'id'          => $id,
		'title'       => 'Space Night &amp; Stars',
		'url'         => 'https://events.wesleyan.edu/event/' . $id . '-space-night',
		'date_iso'    => '2099-11-04T20:00:00-05:00',
		'date2_iso'   => '2099-11-04T22:00:00-05:00',
		'is_canceled' => null,
		'status'      => 1,
		'location'    => 'Van Vleck Observatory',
		'summary'     => '<p>Look up.</p><script>x</script>',
		'tags'        => array( 'Planetary Science', 'Astronomy' ),
	);
}
function run_sync( array $tag_pages ) {
	$GLOBALS['pages'] = $tag_pages;
	$GLOBALS['requests'] = array();
	return WesSci_Event_Sync::sync();
}

require __DIR__ . '/../plugins/wessci-site/includes/class-wessci-event-sync.php';

WesSci_Event_Sync::init();
check_event( isset( $actions['wessci_event_sync'] ) && isset( $actions['save_post_wessci_event'] ) && 20 === $actions['save_post_wessci_event'][1], 'Hooks not registered.' );
WesSci_Event_Sync::schedule();
check_event( 'twicedaily' === $scheduled['wessci_event_sync'], 'Sync not scheduled twice daily.' );

$mapped = WesSci_Event_Sync::map_event( feed_event( 7 ) );
check_event( 'livewhale:7' === $mapped['source_id'] && $mapped['active'], 'Source ID or active flag wrong.' );
check_event( 'Space Night & Stars' === $mapped['fields']['title'], 'Title entities not decoded.' );
check_event( '2099-11-05 01:00:00' === $mapped['fields']['start'] && '2099-11-05 03:00:00' === $mapped['fields']['end'], 'Dates not converted to UTC.' );
check_event( 'Astronomy' === $mapped['fields']['department'], 'Department should follow the configured tag order.' );
check_event( 'College of the Environment' === WesSci_Event_Sync::map_event( feed_event( 12, array( 'tags' => array( 'College of the Environment Bailey College of the Environment' ) ) ) )['fields']['department'], 'Long environment tag not relabeled.' );
check_event( false === strpos( $mapped['fields']['content'], '<script>' ), 'Summary not sanitized.' );
check_event( null === WesSci_Event_Sync::map_event( feed_event( 8, array( 'date_iso' => null ) ) ), 'Event without a start was mapped.' );
check_event( '' === WesSci_Event_Sync::map_event( feed_event( 9, array( 'date2_iso' => '2099-11-04T10:00:00-05:00' ) ) )['fields']['end'], 'End before start was kept.' );
check_event( ! WesSci_Event_Sync::map_event( feed_event( 10, array( 'is_canceled' => 1 ) ) )['active'], 'Canceled event marked active.' );
check_event( ! WesSci_Event_Sync::map_event( feed_event( 11, array( 'status' => 0 ) ) )['active'], 'Unapproved event marked active.' );

check_event( 3 === run_sync( array( feed_page( array( feed_event( 1 ), feed_event( 2 ) ), 'https://events.wesleyan.edu/live/json/events/page2' ), feed_page( array( feed_event( 3 ) ) ), feed_page( array( feed_event( 1 ) ) ), feed_page( array() ) ) ), 'Pagination or de-duplication across tags failed.' );
check_event( 4 === count( $requests ) && false !== strpos( $requests[0], '/tag/Astronomy/' ) && false !== strpos( $requests[1], 'page2' ) && false !== strpos( $requests[2], '/tag/Planetary%20Science/' ), 'Unexpected feed requests.' );
check_event( 3 === count( $posts ) && 'draft' === $posts[100]['post_status'], 'New events should arrive as drafts.' );
check_event( 'Space Night & Stars' === $posts[100]['post_title'] && 'livewhale:1' === $meta[100]['_wessci_event_source_id'], 'Imported fields missing.' );

$posts[100]['post_status'] = 'publish';
$posts[100]['post_title'] = 'Edited by an editor';
$meta[100]['_wessci_event_location'] = 'Exley 058';
call_user_func( $actions['save_post_wessci_event'][0], 100 );
check_event( array( 'title', 'location' ) === $meta[100]['_wessci_event_overrides'], 'Editor changes not recorded as overrides.' );

check_event( 3 === run_sync( array( feed_page( array( feed_event( 1, array( 'title' => 'Renamed upstream', 'location' => 'Moved', 'date_iso' => '2099-11-11T20:00:00-05:00' ) ), feed_event( 2 ), feed_event( 3 ) ) ), feed_page( array() ), feed_page( array() ) ) ), 'Second sync failed.' );
check_event( 'Edited by an editor' === $posts[100]['post_title'] && 'Exley 058' === $meta[100]['_wessci_event_location'], 'Editor changes were overwritten.' );
check_event( '2099-11-12 01:00:00' === $meta[100]['_wessci_event_start'], 'Untouched field was not updated.' );
check_event( 'publish' === $posts[100]['post_status'], 'Sync changed a published event.' );
check_event( 3 === count( $posts ), 'Second sync created duplicates.' );

$posts[101]['post_status'] = 'trash';
run_sync( array( feed_page( array( feed_event( 1 ), feed_event( 2 ), feed_event( 3 ) ) ), feed_page( array() ), feed_page( array() ) ) );
check_event( 'trash' === $posts[101]['post_status'] && 3 === count( $posts ), 'Trashed event came back.' );

$posts[102]['post_status'] = 'publish';
run_sync( array( feed_page( array( feed_event( 1, array( 'is_canceled' => 1 ) ), feed_event( 2 ) ) ), feed_page( array() ), feed_page( array() ) ) );
check_event( 'draft' === $posts[100]['post_status'], 'Canceled event stayed published.' );
check_event( 'draft' === $posts[102]['post_status'], 'Event missing from the feed stayed visible.' );
$posts[102]['post_status'] = 'publish';
$meta[102]['_wessci_event_start'] = '2000-01-01 00:00:00';
run_sync( array( feed_page( array() ), feed_page( array() ), feed_page( array() ) ) );
check_event( 'publish' === $posts[102]['post_status'], 'Past event was hidden.' );

$before = $posts;
check_event( is_wp_error( run_sync( array( feed_page( array( feed_event( 1 ) ) ), new WP_Error( 'http', 'timeout' ) ) ) ), 'Fetch error not returned.' );
check_event( $before === $posts, 'A failed fetch changed events.' );
check_event( is_wp_error( run_sync( array( array( 'code' => 500, 'body' => 'oops' ) ) ) ), 'Bad response not reported.' );
run_sync( array( feed_page( array( feed_event( 5, array( 'url' => 'javascript:alert(1)' ) ) ), 'https://evil.example/next' ), feed_page( array() ), feed_page( array() ) ) );
check_event( 3 === count( $requests ), 'Next link left the events host.' );
check_event( 'livewhale:5' === $meta[103]['_wessci_event_source_id'] && ! isset( $meta[103]['_wessci_event_url'] ), 'Unsafe event URL was stored.' );

echo "Event sync checks passed.\n";
