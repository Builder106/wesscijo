<?php
/**
 * WesSciJo — Hybrid A
 * Concept theme for The Wesleyan Science Journal.
 */

function wessci_hybrid_a_setup() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_image_size( 'wessci-lead', 800, 600, true );
	add_image_size( 'wessci-card', 800, 600, true );
}
add_action( 'after_setup_theme', 'wessci_hybrid_a_setup' );

function wessci_hybrid_a_assets() {
	wp_enqueue_style(
		'wessci-fonts',
		'https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@400;500;600;700;800;900&family=Cormorant+Garamond:wght@600&display=swap',
		array(),
		null
	);
	wp_enqueue_style(
		'wessci-hybrid-a',
		get_stylesheet_uri(),
		array( 'wessci-fonts' ),
		(string) filemtime( get_stylesheet_directory() . '/style.css' )
	);
}
add_action( 'wp_enqueue_scripts', 'wessci_hybrid_a_assets' );

/**
 * The journal's two top-level divisions, each with its article types.
 * Returns [ term, children[] ] so nav and section heads share one source.
 */
function wessci_divisions() {
	$out = array();
	foreach ( array( 'research-reviews', 'news-features-perspectives' ) as $slug ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( ! $term ) {
			continue;
		}
		$out[] = array(
			'term'     => $term,
			'children' => get_terms(
				array(
					'taxonomy'   => 'category',
					'parent'     => $term->term_id,
					'hide_empty' => false,
				)
			),
		);
	}
	return $out;
}

/**
 * Term names are stored HTML-escaped ("News &amp; Views"), so esc_html alone
 * would double-encode the ampersand. Decode first, then escape.
 */
function wessci_term_name( $term ) {
	return esc_html( html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ) );
}

/** Article type = the most specific (child) category on the post. */
function wessci_article_type( $post_id ) {
	$cats = get_the_category( $post_id );
	foreach ( $cats as $c ) {
		if ( $c->parent ) {
			return $c;
		}
	}
	return ! empty( $cats ) ? $cats[0] : null;
}

/** Reading time from word count — real number, not invented. */
function wessci_read_time( $post_id ) {
	preg_match_all( '/\p{L}+/u', wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), $m );
	$words = count( $m[0] );
	return max( 1, (int) round( $words / 200 ) );
}

/** Up to two initials for a masthead placeholder avatar, until real headshots exist. */
function wessci_initials( $name ) {
	$parts    = preg_split( '/\s+/', trim( $name ) );
	$initials = '';
	foreach ( array_slice( $parts, 0, 2 ) as $part ) {
		$initials .= mb_substr( $part, 0, 1 );
	}
	return mb_strtoupper( $initials );
}

/**
 * Events for the Calendar page. No custom fields yet — client asked only
 * for a place this can live once real events exist, not the feature itself.
 */
function wessci_hybrid_a_register_event_cpt() {
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
add_action( 'init', 'wessci_hybrid_a_register_event_cpt' );
