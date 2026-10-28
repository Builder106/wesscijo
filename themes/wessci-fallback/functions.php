<?php
/**
 * WesSciJo
 * Site theme for The Wesleyan Science Journal.
 */

function wesscijo_setup() {
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 160,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	add_image_size( 'wessci-lead', 800, 500, true );
	add_image_size( 'wessci-card', 800, 500, true );
	add_image_size( 'wessci-wide', 1200, 750, true );
}
add_action( 'after_setup_theme', 'wesscijo_setup' );

function wesscijo_assets() {
	wp_enqueue_style(
		'wessci-fallback',
		get_stylesheet_uri(),
		array(),
		(string) filemtime( get_stylesheet_directory() . '/style.css' )
	);
	if ( is_front_page() || is_singular( 'post' ) ) {
		wp_enqueue_script(
			'wessci-cardinal-watch',
			get_theme_file_uri( '/assets/cardinal-watch.js' ),
			array(),
			(string) filemtime( get_stylesheet_directory() . '/assets/cardinal-watch.js' ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
	}
}
add_action( 'wp_enqueue_scripts', 'wesscijo_assets' );

// The masthead and body text need these files on every page, but the stylesheet would only reveal them after it loads.
function wesscijo_preload_fonts( $resources ) {
	foreach ( array( 'librefranklin-latin.woff2', 'cormorantgaramond-600-latin.woff2' ) as $file ) {
		$resources[] = array(
			'href'        => get_theme_file_uri( '/assets/fonts/' . $file ),
			'as'          => 'font',
			'type'        => 'font/woff2',
			'crossorigin' => 'anonymous',
		);
	}
	return $resources;
}
add_filter( 'wp_preload_resources', 'wesscijo_preload_fonts' );

// Every supported browser renders emoji natively, so the detection script is dead weight.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );

/** Return the configurable masthead title used by the header and footer. */
function wesscijo_brand_title() {
	$title = trim( get_theme_mod( 'wessci_masthead_title', 'The Wesleyan Science Journal' ) );
	return '' === $title || 'Wesleyan Science Journal' === $title ? 'The Wesleyan Science Journal' : $title;
}

/** Keep the logo placement easy to change when the final logo is ready. */
function wesscijo_sanitize_logo_position( $value ) {
	return in_array( $value, array( 'left', 'right' ), true ) ? $value : 'left';
}

function wesscijo_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'wessci_masthead',
		array(
			'title'    => 'Journal masthead',
			'priority' => 30,
		)
	);

	$wp_customize->add_setting(
		'wessci_masthead_title',
		array(
			'default'           => 'The Wesleyan Science Journal',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'wessci_masthead_title',
		array(
			'label'       => 'Masthead title',
			'description' => 'Text shown in the masthead and footer.',
			'section'     => 'wessci_masthead',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'wessci_logo_position',
		array(
			'default'           => 'left',
			'sanitize_callback' => 'wesscijo_sanitize_logo_position',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'wessci_logo_position',
		array(
			'label'       => 'Logo position',
			'description' => 'Choose which side of the masthead title the custom logo uses.',
			'section'     => 'wessci_masthead',
			'type'        => 'select',
			'choices'     => array(
				'left'  => 'Left of title',
				'right' => 'Right of title',
			),
		)
	);
}
add_action( 'customize_register', 'wesscijo_customize_register' );

/** Keep search results on the article card system instead of mixing page types. */
function wesscijo_search_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$query->set( 'post_type', 'post' );
}
add_action( 'pre_get_posts', 'wesscijo_search_query' );

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

/** The logo cardinal, inlined so assets/cardinal-watch.js can turn its head. */
function wessci_cardinal_svg() {
	static $svg = null;
	if ( null === $svg ) {
		$svg = (string) file_get_contents( get_theme_file_path( '/assets/cardinal-perch.svg' ) );
	}
	return $svg;
}

/** End an article's text with the cardinal, before any Works Cited heading. */
function wessci_article_end_mark( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}
	$text_end = preg_match( '/<h[2-6][^>]*>\s*(?:<[^>]+>\s*)*(?:works cited|references|sources|bibliography)\b/i', $content, $heading, PREG_OFFSET_CAPTURE ) ? $heading[0][1] : strlen( $content );
	preg_match_all( '/<p\b[^>]*>(.*?)<\/p>/is', substr( $content, 0, $text_end ), $paragraphs, PREG_SET_ORDER | PREG_OFFSET_CAPTURE );
	foreach ( array_reverse( $paragraphs ) as $paragraph ) {
		if ( ! preg_match( '/[^\s\x{00A0}]/u', html_entity_decode( wp_strip_all_tags( $paragraph[1][0] ), ENT_QUOTES, 'UTF-8' ) ) ) {
			continue;
		}
		$inner = preg_replace( '/(?:\s|<br\s*\/?>|&nbsp;)+$/i', '', $paragraph[1][0] );
		return substr_replace( $content, '<span class="end-mark">' . wessci_cardinal_svg() . '</span>', $paragraph[1][1] + strlen( $inner ), 0 );
	}
	return $content;
}
add_filter( 'the_content', 'wessci_article_end_mark', 20 );

function wesscijo_byline( $post_id ) {
	$authors = get_post_meta( $post_id, '_wessci_authors', true );
	if ( ! is_array( $authors ) ) {
		return '';
	}
	$names = array();
	foreach ( $authors as $author ) {
		if ( is_array( $author ) && isset( $author['name'] ) && is_string( $author['name'] ) && '' !== trim( $author['name'] ) ) {
			$name    = preg_replace( '/,\s*Class of \d{4}/i', '', trim( $author['name'] ) );
			$names[] = $name;
		}
	}
	return implode( ', ', $names );
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
 * Return the editorial board and masthead roster.
 *
 * Exposes the structured roster for both theme views and the
 * wessci-site companion plugin admin screen.
 *
 * @return array Sections containing title and members.
 */
function wessci_editorial_board() {
	return array(
		array(
			'title'   => 'Editorial Board',
			'members' => array(
				array( 'name' => 'Aryia Banihashem-Ahmad', 'role' => 'Editor-in-Chief' ),
				array( 'name' => 'Shriya Sakalkale', 'role' => 'Editor-in-Chief' ),
				array( 'name' => 'Elena Mente', 'role' => 'Editor-in-Chief' ),
			),
		),
		array(
			'title'   => 'Life Science',
			'members' => array(
				array( 'name' => 'Lorraine Hillgen-Santa', 'role' => 'Lead Life Science Editor' ),
				array( 'name' => 'Maia Feik Reinhart', 'role' => 'Lead Life Science Editor' ),
				array( 'name' => 'Feyza Horuz', 'role' => 'Biology Editor' ),
				array( 'name' => 'Kitty Edwards', 'role' => 'Biology Editor' ),
				array( 'name' => 'Claire Farina', 'role' => 'Biology Editor' ),
				array( 'name' => 'Sophie Lambert', 'role' => 'Assistant Biology Editor' ),
				array( 'name' => 'Saara Saini', 'role' => 'Assistant Biology Editor' ),
				array( 'name' => 'Hannah Zullow', 'role' => 'Neuroscience & Psychology Editor' ),
				array( 'name' => 'Gaby Sorin', 'role' => 'Neuroscience & Psychology Editor' ),
				array( 'name' => 'Olivia Oliveira', 'role' => 'Assistant Neuroscience & Psychology Editor' ),
				array( 'name' => 'Rhea Ashish Kothari', 'role' => 'Assistant Neuroscience & Psychology Editor' ),
			),
		),
		array(
			'title'   => 'Physical Science',
			'members' => array(
				array( 'name' => 'Natalie Price', 'role' => 'Lead Physical Science Editor' ),
				array( 'name' => 'Hamza Habib', 'role' => 'Lead Physical Science Editor' ),
				array( 'name' => 'Zesun Hossain', 'role' => 'Astronomy Editor' ),
				array( 'name' => 'Ella Stricker', 'role' => 'Physics Editor' ),
				array( 'name' => 'Rhea Ashish Kothari', 'role' => 'Assistant Physics Editor' ),
				array( 'name' => 'Ellen Gudiksen', 'role' => 'Chemistry Editor' ),
				array( 'name' => 'Saara Saini', 'role' => 'Assistant Chemistry Editor' ),
				array( 'name' => 'Aniana Garciano', 'role' => 'Earth & Environmental Editor' ),
				array( 'name' => 'Ella Stricker', 'role' => 'Earth & Environmental Editor' ),
			),
		),
		array(
			'title'   => 'Quantitative and Computational Science',
			'members' => array(
				array( 'name' => 'Shloka Bhattacharyya', 'role' => 'Lead Quantitative and Computational Editor' ),
				array( 'name' => 'Gillian Churchland', 'role' => 'Math Editor' ),
				array( 'name' => 'Calvin Chiu', 'role' => 'Math Editor' ),
				array( 'name' => 'Sangye Sherpa', 'role' => 'Math Editor' ),
				array( 'name' => 'Samantha Sheahan', 'role' => 'Assistant Computer Science Editor' ),
			),
		),
		array(
			'title'   => 'Science, Technology and Society',
			'members' => array(
				array( 'name' => 'Sangye Sherpa', 'role' => 'Lead Science, Technology & Society Editor' ),
				array( 'name' => 'Tessa Higgins', 'role' => 'Lead Science, Technology & Society Editor' ),
				array( 'name' => 'Dahlia Cedarbaum', 'role' => 'Science, Technology & Society Editor' ),
				array( 'name' => 'Sarah Toolan', 'role' => 'Science, Technology & Society Editor' ),
			),
		),
		array(
			'title'   => 'Graphic Design',
			'members' => array(
				array( 'name' => 'Sorielis Paulino Polanco', 'role' => 'Graphic Designer' ),
				array( 'name' => 'Minaal Khwaja', 'role' => 'Graphic Designer' ),
				array( 'name' => 'Hannah Russak', 'role' => 'Graphic Designer' ),
				array( 'name' => 'Ali Eckstein', 'role' => 'Graphic Designer' ),
				array( 'name' => 'Olivia Oliveira', 'role' => 'Graphic Designer' ),
				array( 'name' => 'Kitty Edwards', 'role' => 'Graphic Designer' ),
			),
		),
		array(
			'title'   => 'Web Design',
			'members' => array(
				array( 'name' => 'Olayinka Vaughan', 'role' => 'Lead Web Designer' ),
				array( 'name' => 'Giancarlo Fedolfi', 'role' => 'Web Designer' ),
			),
		),
	);
}

