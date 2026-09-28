<?php
/**
 * WesSciJo — Hybrid A
 * Concept theme for The Wesleyan Science Journal.
 */

function wessci_hybrid_a_setup() {
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

/** Return the configurable masthead title used by the header and footer. */
function wessci_hybrid_a_brand_title() {
	$title = get_theme_mod( 'wessci_masthead_title', 'Wesleyan Science Journal' );
	return '' !== trim( $title ) ? $title : 'Wesleyan Science Journal';
}

/** Keep the logo placement easy to change when the final logo is ready. */
function wessci_hybrid_a_sanitize_logo_position( $value ) {
	return in_array( $value, array( 'left', 'right' ), true ) ? $value : 'left';
}

function wessci_hybrid_a_customize_register( $wp_customize ) {
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
			'default'           => 'Wesleyan Science Journal',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'wessci_masthead_title',
		array(
			'label'       => 'Masthead title',
			'description' => 'Text shown in the black banner and footer.',
			'section'     => 'wessci_masthead',
			'type'        => 'text',
		)
	);

	$wp_customize->add_setting(
		'wessci_logo_position',
		array(
			'default'           => 'left',
			'sanitize_callback' => 'wessci_hybrid_a_sanitize_logo_position',
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

	$wp_customize->add_setting(
		'wessci_show_calendar',
		array(
			'default'           => false,
			'sanitize_callback' => 'rest_sanitize_boolean',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'wessci_show_calendar',
		array(
			'label'       => 'Show the Calendar page',
			'description' => 'Hidden until department events are ready. While hidden, /calendar/ returns a 404 and the nav links are removed.',
			'section'     => 'wessci_masthead',
			'type'        => 'checkbox',
		)
	);

	$wp_customize->add_section(
		'wessci_submissions',
		array(
			'title'    => 'Submissions',
			'priority' => 31,
		)
	);

	$wp_customize->add_setting(
		'wessci_submission_form_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'wessci_submission_form_url',
		array(
			'label'       => 'Submission form URL',
			'description' => 'A Google Form link. It is embedded on the Submit page; other links are shown as a button.',
			'section'     => 'wessci_submissions',
			'type'        => 'url',
		)
	);

	$wp_customize->add_setting(
		'wessci_submission_guidelines_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'wessci_submission_guidelines_url',
		array(
			'label'       => 'Article content guidelines URL',
			'description' => 'Leave blank to use the approved guidelines PDF bundled with the theme, or link a newer version (Google Doc, or a PDF in the Media Library).',
			'section'     => 'wessci_submissions',
			'type'        => 'url',
		)
	);
}
add_action( 'customize_register', 'wessci_hybrid_a_customize_register' );

/** The article content guidelines: the Customizer link when set, otherwise the approved PDF bundled with the theme. */
function wessci_submission_guidelines_url() {
	$url = get_theme_mod( 'wessci_submission_guidelines_url', '' );
	return $url ? $url : get_theme_file_uri( 'assets/wessci-article-content-guidelines.pdf' );
}

/** The journal seal: the Customizer logo when set, otherwise the approved logo bundled with the theme. */
function wessci_hybrid_a_logo( $class = '' ) {
	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $custom_logo_id && wp_attachment_is_image( $custom_logo_id ) ) {
		return wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'alt' => '', 'class' => $class ) );
	}
	return sprintf(
		'<img class="%s" src="%s" width="640" height="632" alt="" decoding="async">',
		esc_attr( $class ),
		esc_url( get_theme_file_uri( 'assets/wessci-logo.png' ) )
	);
}

/** Fall back to the journal seal as the favicon until a Site Icon is set in wp-admin. */
function wessci_hybrid_a_favicon() {
	if ( has_site_icon() ) {
		return;
	}
	echo '<link rel="icon" href="' . esc_url( get_theme_file_uri( 'assets/icon-32.png' ) ) . '" sizes="32x32">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( get_theme_file_uri( 'assets/icon-180.png' ) ) . '">' . "\n";
}
add_action( 'wp_head', 'wessci_hybrid_a_favicon' );

/** The Calendar stays hidden until the editors are ready to list department events. */
function wessci_hybrid_a_calendar_enabled() {
	return (bool) get_theme_mod( 'wessci_show_calendar', false );
}

function wessci_hybrid_a_hide_calendar() {
	if ( wessci_hybrid_a_calendar_enabled() || ! ( is_page( 'calendar' ) || is_singular( 'wessci_event' ) ) ) {
		return;
	}
	global $wp_query;
	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
}
add_action( 'template_redirect', 'wessci_hybrid_a_hide_calendar' );

/**
 * Photo credit for a post's featured image, taken from the image's Caption
 * field in the Media Library (e.g. "c/o Jane Doe '27"), the same way The
 * Argus credits its images. Empty when no credit has been entered.
 */
function wessci_photo_credit( $post_id ) {
	$thumbnail_id = get_post_thumbnail_id( $post_id );
	return $thumbnail_id ? trim( (string) wp_get_attachment_caption( $thumbnail_id ) ) : '';
}

/** The EICs' letter, once a page with the slug letter-from-the-editors is published. */
function wessci_editors_letter() {
	$letter = get_page_by_path( 'letter-from-the-editors' );
	return $letter && 'publish' === $letter->post_status ? $letter : null;
}

/**
 * Google Forms only render inside an iframe from their full docs.google.com
 * URL with embedded=true. Short forms.gle links and other hosts get a button.
 */
function wessci_submission_form_embed_url( $url ) {
	$parts = wp_parse_url( $url );
	if ( empty( $parts['host'] ) || 'docs.google.com' !== $parts['host'] || empty( $parts['path'] ) || 0 !== strpos( $parts['path'], '/forms/' ) ) {
		return '';
	}
	return add_query_arg( 'embedded', 'true', $url );
}

/** Keep search results on the article card system instead of mixing page types. */
function wessci_hybrid_a_search_query( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
		return;
	}

	$query->set( 'post_type', 'post' );
}
add_action( 'pre_get_posts', 'wessci_hybrid_a_search_query' );

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

/** The print journal's fields (Life Science, …), owned by the site plugin. */
function wessci_fields() {
	return function_exists( 'wessci_site_get_fields' ) ? wessci_site_get_fields() : array();
}

/** The article's field, in the same print order when a post is filed under more than one. */
function wessci_article_field( $post_id ) {
	$terms = taxonomy_exists( 'wessci_field' ) ? get_the_terms( $post_id, 'wessci_field' ) : false;
	if ( ! $terms || is_wp_error( $terms ) ) {
		return null;
	}
	foreach ( wessci_fields() as $field ) {
		foreach ( $terms as $term ) {
			if ( $term->term_id === $field->term_id ) {
				return $term;
			}
		}
	}
	return $terms[0];
}

/** Card kicker: article type, plus field when known ("Features · Life Science"). Escaped. */
function wessci_card_kicker( $post_id ) {
	$parts = array();
	foreach ( array( wessci_article_type( $post_id ), wessci_article_field( $post_id ) ) as $term ) {
		if ( $term ) {
			$parts[] = wessci_term_name( $term );
		}
	}
	return implode( ' &middot; ', $parts );
}

/** "Written by" / "Edited by" credits entered in the post's Article credits box. */
function wessci_article_credits( $post_id ) {
	$credits = array();
	foreach ( array( 'writers' => 'By', 'editors' => 'Edited by' ) as $key => $label ) {
		$names = trim( (string) get_post_meta( $post_id, '_wessci_' . $key, true ) );
		if ( '' !== $names ) {
			$credits[ $key ] = array( 'label' => $label, 'names' => $names );
		}
	}
	return $credits;
}

/** Byline under an article or lead headline; prints nothing until credits are entered. */
function wessci_the_byline( $post_id ) {
	$credits = wessci_article_credits( $post_id );
	if ( ! $credits ) {
		return;
	}
	echo '<p class="byline">';
	foreach ( $credits as $key => $credit ) {
		printf( '<span class="byline__%1$s">%2$s <span class="byline__names">%3$s</span></span>', esc_attr( $key ), esc_html( $credit['label'] ), esc_html( $credit['names'] ) );
	}
	echo '</p>';
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
