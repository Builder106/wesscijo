<?php
/**
 * Plugin Name: WesSciJo Site Core
 * Description: Durable site content, event fields, and conservative metadata for WesSciJo.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wessci_site_register_event_cpt() {
	register_post_type(
		'wessci_event',
		array(
			'labels'      => array(
				'name'          => 'Events',
				'singular_name' => 'Event',
			),
			'public'      => true,
			'has_archive' => false,
			'rewrite'     => array( 'slug' => 'events' ),
			'supports'    => array( 'title', 'editor', 'thumbnail' ),
			'menu_icon'   => 'dashicons-calendar-alt',
		)
	);
}
add_action( 'init', 'wessci_site_register_event_cpt' );

function wessci_site_add_event_meta_box() {
	add_meta_box( 'wessci-event-details', 'Event details', 'wessci_site_render_event_meta_box', 'wessci_event', 'side' );
}
add_action( 'add_meta_boxes', 'wessci_site_add_event_meta_box' );

function wessci_site_render_event_meta_box( $post ) {
	wp_nonce_field( 'wessci_event_details', 'wessci_event_nonce' );
	$timezone = wp_timezone();
	$fields   = array(
		'start'    => array( 'label' => 'Start', 'type' => 'datetime-local' ),
		'end'      => array( 'label' => 'End', 'type' => 'datetime-local' ),
		'location' => array( 'label' => 'Location', 'type' => 'text' ),
		'url'      => array( 'label' => 'External URL', 'type' => 'url' ),
	);

	foreach ( $fields as $key => $field ) {
		$value = get_post_meta( $post->ID, '_wessci_event_' . $key, true );
		if ( in_array( $key, array( 'start', 'end' ), true ) && $value ) {
			$date  = new DateTimeImmutable( $value, new DateTimeZone( 'UTC' ) );
			$value = $date->setTimezone( $timezone )->format( 'Y-m-d\\TH:i' );
		}
		printf( '<p><label for="wessci-event-%1$s">%2$s</label><input class="widefat" id="wessci-event-%1$s" name="wessci_event_%1$s" type="%3$s" value="%4$s"%5$s></p>', esc_attr( $key ), esc_html( $field['label'] ), esc_attr( $field['type'] ), esc_attr( $value ), 'url' === $field['type'] ? ' maxlength="2048"' : '' );
	}
	echo '<p class="description">Dates use the WordPress site timezone while editing and are stored in UTC.</p>';
}

function wessci_site_parse_local_datetime( $value ) {
	if ( ! is_string( $value ) || '' === trim( $value ) ) {
		return '';
	}
	$date   = DateTimeImmutable::createFromFormat( '!Y-m-d\\TH:i', $value, wp_timezone() );
	$errors = DateTimeImmutable::getLastErrors();
	return $date && ( false === $errors || ( 0 === $errors['warning_count'] && 0 === $errors['error_count'] ) ) ? $date->setTimezone( new DateTimeZone( 'UTC' ) )->format( 'Y-m-d H:i:s' ) : '';
}

function wessci_site_save_event_meta( $post_id ) {
	if ( ! isset( $_POST['wessci_event_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wessci_event_nonce'] ) ), 'wessci_event_details' ) || ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) || 'wessci_event' !== get_post_type( $post_id ) ) {
		return;
	}
	$start = wessci_site_parse_local_datetime( isset( $_POST['wessci_event_start'] ) ? sanitize_text_field( wp_unslash( $_POST['wessci_event_start'] ) ) : '' );
	$end   = wessci_site_parse_local_datetime( isset( $_POST['wessci_event_end'] ) ? sanitize_text_field( wp_unslash( $_POST['wessci_event_end'] ) ) : '' );
	if ( $start && $end && $end < $start ) {
		$end = '';
	}
	$values = array(
		'_wessci_event_start'    => $start,
		'_wessci_event_end'      => $end,
		'_wessci_event_location' => isset( $_POST['wessci_event_location'] ) ? sanitize_text_field( wp_unslash( $_POST['wessci_event_location'] ) ) : '',
		'_wessci_event_url'      => isset( $_POST['wessci_event_url'] ) ? esc_url_raw( wp_unslash( $_POST['wessci_event_url'] ) ) : '',
	);
	foreach ( $values as $key => $value ) {
		'' === $value ? delete_post_meta( $post_id, $key ) : update_post_meta( $post_id, $key, $value );
	}
}
add_action( 'save_post_wessci_event', 'wessci_site_save_event_meta' );

function wessci_site_get_event_datetime( $post_id, $which ) {
	$value = get_post_meta( $post_id, '_wessci_event_' . $which, true );
	if ( ! $value ) {
		return null;
	}
	$date = DateTimeImmutable::createFromFormat( 'Y-m-d H:i:s', $value, new DateTimeZone( 'UTC' ) );
	return $date ? $date->setTimezone( wp_timezone() ) : null;
}

function wessci_site_get_upcoming_events() {
	$events = get_posts( array( 'post_type' => 'wessci_event', 'posts_per_page' => -1, 'post_status' => 'publish', 'orderby' => 'ID', 'order' => 'ASC' ) );
	$now    = new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	$events = array_filter( $events, function ( $event ) use ( $now ) {
		$start = wessci_site_get_event_datetime( $event->ID, 'start' );
		return ! $start || $start->getTimestamp() >= $now->getTimestamp();
	} );
	usort( $events, function ( $left, $right ) {
		$left_start  = wessci_site_get_event_datetime( $left->ID, 'start' );
		$right_start = wessci_site_get_event_datetime( $right->ID, 'start' );
		if ( ! $left_start && ! $right_start ) {
			return $left->ID <=> $right->ID;
		}
		if ( ! $left_start ) {
			return 1;
		}
		if ( ! $right_start ) {
			return -1;
		}
		return $left_start->getTimestamp() <=> $right_start->getTimestamp();
	} );
	return $events;
}

function wessci_site_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || class_exists( 'The_SEO_Framework\\Load' );
}

function wessci_site_is_fixture() {
	$content = strtolower( wp_strip_all_tags( get_the_title() . ' ' . get_post_field( 'post_content', get_queried_object_id() ) ) );
	return false !== strpos( $content, 'placeholder' ) || false !== strpos( $content, 'future issue' ) || false !== strpos( $content, 'will be listed here' ) || wessci_site_calendar_is_fixture() || ( is_singular( 'wessci_event' ) && ( false !== strpos( $content, 'tbd' ) || false !== strpos( $content, 'once scheduled' ) ) );
}

function wessci_site_calendar_is_fixture() {
	if ( ! is_page( 'calendar' ) ) {
		return false;
	}
	foreach ( get_posts( array( 'post_type' => 'wessci_event', 'posts_per_page' => 1, 'post_status' => 'publish' ) ) as $event ) {
		if ( wessci_site_get_event_datetime( $event->ID, 'start' ) ) {
			return false;
		}
	}
	return true;
}

function wessci_site_description() {
	if ( is_front_page() ) {
		return 'The Wesleyan Science Journal publishes science research, reviews, news, features, and perspectives from the Wesleyan community.';
	}
	if ( is_search() ) {
		return 'Search results from The Wesleyan Science Journal.';
	}
	if ( is_category() ) {
		return 'Browse ' . single_cat_title( '', false ) . ' from The Wesleyan Science Journal.';
	}
	if ( is_page( 'about' ) ) {
		return 'Meet the editors and learn about The Wesleyan Science Journal.';
	}
	if ( is_page( 'calendar' ) ) {
		return 'Upcoming events from The Wesleyan Science Journal.';
	}
	if ( is_page( 'archives' ) ) {
		return 'Past issues of The Wesleyan Science Journal.';
	}
	if ( is_page( 'submit' ) ) {
		return 'Submission information for The Wesleyan Science Journal.';
	}
	if ( is_singular() ) {
		return wp_trim_words( wp_strip_all_tags( get_the_excerpt() ), 30, '…' );
	}
	return '';
}

function wessci_site_metadata() {
	if ( is_admin() || is_404() || wessci_site_seo_plugin_active() ) {
		return;
	}
	$is_fixture  = wessci_site_is_fixture();
	$description = $is_fixture ? '' : wessci_site_description();
	$title = wp_get_document_title();
	$url   = is_singular() ? get_permalink() : ( is_category() ? get_category_link( get_queried_object_id() ) : home_url( '/' ) );
	if ( is_search() ) {
		$url = add_query_arg( 's', get_search_query(), home_url( '/' ) );
	}
	if ( is_front_page() || is_search() ) {
		echo '<link rel="canonical" href="' . esc_url( $url ) . '">' . "\n";
	}
	if ( ! $description ) {
		return;
	}
	echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
	foreach ( array( 'og:title' => $title, 'og:description' => $description, 'og:url' => $url, 'og:type' => is_singular( 'post' ) ? 'article' : 'website', 'twitter:card' => 'summary' ) as $property => $content ) {
		$attribute = 0 === strpos( $property, 'twitter:' ) ? 'name' : 'property';
		echo '<meta ' . $attribute . '="' . esc_attr( $property ) . '" content="' . esc_attr( $content ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'wessci_site_metadata', 1 );

function wessci_site_filter_canonical_url( $canonical_url ) {
	if ( wessci_site_seo_plugin_active() ) {
		return $canonical_url;
	}
	if ( is_search() ) {
		return add_query_arg( 's', get_search_query(), home_url( '/' ) );
	}
	if ( is_category() ) {
		return get_category_link( get_queried_object_id() );
	}
	if ( is_singular() ) {
		return get_permalink();
	}
	return $canonical_url;
}
add_filter( 'get_canonical_url', 'wessci_site_filter_canonical_url' );

function wessci_site_filter_robots( $robots ) {
	if ( wessci_site_seo_plugin_active() ) {
		return $robots;
	}
	if ( is_search() || wessci_site_is_fixture() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
	}
	return $robots;
}
add_filter( 'wp_robots', 'wessci_site_filter_robots' );

// No favicon is fabricated here. WordPress will emit an approved Site Icon when configured.
