<?php
/**
 * WesSciJo Cardinal Theme Functions
 *
 * @package WesSciJo_Cardinal
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function wessci_cardinal_setup() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 120,
			'width'       => 120,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	add_image_size( 'wessci-lead', 1200, 800, true );
	add_image_size( 'wessci-card', 800, 533, true );
	add_image_size( 'wessci-thumb', 400, 267, true );
}
add_action( 'after_setup_theme', 'wessci_cardinal_setup' );

function wessci_cardinal_scripts() {
	// Google Fonts
	wp_enqueue_style(
		'wessci-fonts',
		'https://fonts.googleapis.com/css2?family=Barlow+Condensed:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400&family=JetBrains+Mono:wght@400;500;700&family=Libre+Franklin:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap',
		array(),
		null
	);

	// Theme Stylesheet
	wp_enqueue_style(
		'wessci-cardinal-style',
		get_stylesheet_uri(),
		array( 'wessci-fonts' ),
		(string) filemtime( get_stylesheet_directory() . '/style.css' )
	);

	// Application Microinteractions & Theme Toggle
	wp_enqueue_script(
		'wessci-cardinal-app',
		get_stylesheet_directory_uri() . '/assets/app.js',
		array(),
		(string) filemtime( get_stylesheet_directory() . '/assets/app.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'wessci_cardinal_scripts' );

/**
 * Filter search queries to only return published posts.
 */
function wessci_cardinal_search_filter( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		$query->set( 'post_type', 'post' );
	}
}
add_action( 'pre_get_posts', 'wessci_cardinal_search_filter' );

/**
 * Top-level divisions and child taxonomies.
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
 * Properly decode & escape term names.
 */
function wessci_term_name( $term ) {
	if ( ! $term || is_wp_error( $term ) ) {
		return '';
	}
	return esc_html( html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ) );
}

/**
 * Return specific article sub-type (child category) or primary category.
 */
function wessci_article_type( $post_id ) {
	$cats = get_the_category( $post_id );
	if ( empty( $cats ) ) {
		return null;
	}
	foreach ( $cats as $c ) {
		if ( $c->parent ) {
			return $c;
		}
	}
	return $cats[0];
}

/**
 * Real reading time calculated from word count (approx 200 wpm).
 */
function wessci_read_time( $post_id ) {
	$content = get_post_field( 'post_content', $post_id );
	preg_match_all( '/\p{L}+/u', wp_strip_all_tags( $content ), $matches );
	$words = count( $matches[0] );
	return max( 1, (int) round( $words / 200 ) );
}

/**
 * Initials for student masthead avatar monograms.
 */
function wessci_initials( $name ) {
	$parts    = preg_split( '/\s+/', trim( $name ) );
	$initials = '';
	foreach ( array_slice( $parts, 0, 2 ) as $part ) {
		$initials .= mb_substr( $part, 0, 1 );
	}
	return mb_strtoupper( $initials );
}

/**
 * Get the 25-person student Editorial Board Masthead data.
 */
function wessci_editorial_board() {
	return array(
		'executive' => array(
			'title'   => 'Executive Leadership',
			'members' => array(
				array( 'name' => 'Aryia Banihashem-Ahmad', 'role' => 'Editor-in-Chief', 'initials' => 'AB' ),
				array( 'name' => 'Shriya Sakalkale', 'role' => 'Editor-in-Chief', 'initials' => 'SS' ),
				array( 'name' => 'Elena Mente', 'role' => 'Editor-in-Chief', 'initials' => 'EM' ),
			),
		),
		'lifesci' => array(
			'title'   => 'Life Sciences Division',
			'members' => array(
				array( 'name' => 'Lorraine Hillgen-Santa', 'role' => 'Lead Life Science Editor', 'initials' => 'LH' ),
				array( 'name' => 'Maia Feik Reinhart', 'role' => 'Lead Life Science Editor', 'initials' => 'MF' ),
				array( 'name' => 'Feyza Horuz', 'role' => 'Biology Editor', 'initials' => 'FH' ),
				array( 'name' => 'Kitty Edwards', 'role' => 'Biology Editor', 'initials' => 'KE' ),
				array( 'name' => 'Claire Farina', 'role' => 'Biology Editor', 'initials' => 'CF' ),
				array( 'name' => 'Sophie Lambert', 'role' => 'Assistant Biology Editor', 'initials' => 'SL' ),
				array( 'name' => 'Saara Saini', 'role' => 'Assistant Biology Editor', 'initials' => 'SS' ),
				array( 'name' => 'Hannah Zullow', 'role' => 'Neuroscience & Psychology Editor', 'initials' => 'HZ' ),
				array( 'name' => 'Gaby Sorin', 'role' => 'Neuroscience & Psychology Editor', 'initials' => 'GS' ),
				array( 'name' => 'Olivia Oliveira', 'role' => 'Assistant Neuroscience & Psychology Editor', 'initials' => 'OO' ),
				array( 'name' => 'Rhea Ashish Kothari', 'role' => 'Assistant Neuroscience & Psychology Editor', 'initials' => 'RA' ),
			),
		),
		'physical' => array(
			'title'   => 'Physical Sciences Division',
			'members' => array(
				array( 'name' => 'Natalie Price', 'role' => 'Lead Physical Science Editor', 'initials' => 'NP' ),
				array( 'name' => 'Hamza Habib', 'role' => 'Lead Physical Science Editor', 'initials' => 'HH' ),
				array( 'name' => 'Zesun Hossain', 'role' => 'Astronomy Editor', 'initials' => 'ZH' ),
				array( 'name' => 'Ella Stricker', 'role' => 'Physics Editor', 'initials' => 'ES' ),
				array( 'name' => 'Rhea Ashish Kothari', 'role' => 'Assistant Physics Editor', 'initials' => 'RA' ),
				array( 'name' => 'Ellen Gudiksen', 'role' => 'Chemistry Editor', 'initials' => 'EG' ),
				array( 'name' => 'Saara Saini', 'role' => 'Assistant Chemistry Editor', 'initials' => 'SS' ),
				array( 'name' => 'Aniana Garciano', 'role' => 'Earth & Environmental Editor', 'initials' => 'AG' ),
				array( 'name' => 'Ella Stricker', 'role' => 'Earth & Environmental Editor', 'initials' => 'ES' ),
			),
		),
		'quant' => array(
			'title'   => 'Quantitative & Computational Science',
			'members' => array(
				array( 'name' => 'Shloka Bhattacharyya', 'role' => 'Lead Quantitative & Computational Editor', 'initials' => 'SB' ),
				array( 'name' => 'Gillian Churchland', 'role' => 'Math Editor', 'initials' => 'GC' ),
				array( 'name' => 'Calvin Chiu', 'role' => 'Math Editor', 'initials' => 'CC' ),
				array( 'name' => 'Sangye Sherpa', 'role' => 'Math Editor', 'initials' => 'SS' ),
				array( 'name' => 'Samantha Sheahan', 'role' => 'Assistant Computer Science Editor', 'initials' => 'SS' ),
			),
		),
		'sts' => array(
			'title'   => 'Science, Technology & Society',
			'members' => array(
				array( 'name' => 'Sangye Sherpa', 'role' => 'Lead Science, Technology & Society Editor', 'initials' => 'SS' ),
				array( 'name' => 'Tessa Higgins', 'role' => 'Lead Science, Technology & Society Editor', 'initials' => 'TH' ),
				array( 'name' => 'Dahlia Cedarbaum', 'role' => 'Science, Technology & Society Editor', 'initials' => 'DC' ),
				array( 'name' => 'Sarah Toolan', 'role' => 'Science, Technology & Society Editor', 'initials' => 'ST' ),
			),
		),
	);
}
