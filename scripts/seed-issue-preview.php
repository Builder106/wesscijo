<?php

if ( ! defined( 'WESSCI_ISSUE_PREVIEW' ) || ! WESSCI_ISSUE_PREVIEW ) {
	throw new RuntimeException( 'This script requires the isolated issue preview.' );
}

require_once '/wordpress/wp-load.php';

$articles = json_decode( file_get_contents( '/wordpress/issue/articles.json' ), true, 512, JSON_THROW_ON_ERROR );
if ( 9 !== count( $articles ) ) {
	throw new RuntimeException( 'The preview requires nine issue articles.' );
}
wp_set_current_user( 1 );

function wessci_preview_attachment( $source, $caption = '', $alt = '' ) {
	$relative = str_replace( '/assets/', 'issue/', $source );
	$file = '/wordpress/wp-content/uploads/' . $relative;
	if ( ! is_file( $file ) ) {
		throw new RuntimeException( 'A preview image is missing: ' . $relative );
	}
	$mime = wp_check_filetype( $file );
	$id = wp_insert_attachment(
		array(
			'post_title' => pathinfo( $file, PATHINFO_FILENAME ),
			'post_excerpt' => $caption,
			'post_mime_type' => $mime['type'],
			'post_status' => 'inherit',
		),
		$file
	);
	if ( ! $id || is_wp_error( $id ) ) {
		throw new RuntimeException( 'Could not import a preview image.' );
	}
	$size = wp_getimagesize( $file );
	if ( $size ) {
		wp_update_attachment_metadata( $id, array( 'width' => $size[0], 'height' => $size[1], 'file' => $relative ) );
	}
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	return $id;
}

update_option( 'blogname', 'The Wesleyan Science Journal' );
update_option( 'blogdescription', '' );
update_option( 'timezone_string', 'America/New_York' );
update_option( 'permalink_structure', '/%postname%/' );
update_option( 'blog_public', 0 );
switch_theme( 'wesscijo' );
set_theme_mod( 'custom_logo', wessci_preview_attachment( '/assets/logo.png', '', 'The Wesleyan Science Journal logo' ) );
set_theme_mod( 'wessci_masthead_title', 'The Wesleyan Science Journal' );

foreach ( get_posts( array( 'posts_per_page' => -1, 'post_status' => 'any' ) ) as $post ) {
	wp_delete_post( $post->ID, true );
}

$groups = array();
foreach ( array( 'research-reviews' => 'Research & Reviews', 'news-features-perspectives' => 'News, Features & Perspectives' ) as $slug => $name ) {
	$term = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
	$groups[ $slug ] = is_wp_error( $term ) ? (int) get_term_by( 'slug', $slug, 'category' )->term_id : $term['term_id'];
}

foreach ( $articles as $article ) {
	$group = in_array( $article['slug'], array( 'bile-salt-mixed-micelles', 'gmos-applications-and-controversy', 'math-behind-llms', 'quantum-voting' ), true ) ? 'research-reviews' : 'news-features-perspectives';
	$slug = $group . '-' . sanitize_title( $article['type'] );
	$term = wp_insert_term( $article['type'], 'category', array( 'slug' => $slug, 'parent' => $groups[ $group ] ) );
	$type_id = is_wp_error( $term ) ? (int) get_term_by( 'slug', $slug, 'category' )->term_id : $term['term_id'];
	$id = wp_insert_post(
		wp_slash( array(
			'post_title' => $article['title'],
			'post_name' => $article['slug'],
			'post_content' => str_replace( '/assets/', '/wp-content/uploads/issue/', $article['bodyHtml'] ),
			'post_excerpt' => $article['excerpt'],
			'post_status' => 'publish',
			'post_category' => array( $groups[ $group ], $type_id ),
			'comment_status' => 'closed',
		) ),
		true
	);
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	update_post_meta( $id, '_wessci_authors', array( array( 'name' => $article['byline'] ) ) );
	if ( ! empty( $article['thumbnail'] ) ) {
		$image = $article['thumbnail'];
		set_post_thumbnail( $id, wessci_preview_attachment( $image['src'], $image['credit'] ?? '', $image['alt'] ?? '' ) );
	}
	if ( 'birds-of-wesleyan' === $article['slug'] ) {
		stick_post( $id );
	}
}

$form = 'https://docs.google.com/forms/d/e/1FAIpQLSf3YVPrAoa6FFr3QyJYtGbWm5aDzRgoVsL6EjiwLnagwW63cA/viewform';
$pages = array(
	'about' => array( 'About', '<h2>Letter from the Editorial Board</h2>' . file_get_contents( '/wordpress/issue/letter.html' ) ),
	'submit' => array( 'Submission guidelines', '<p><a href="' . esc_url( $form ) . '">Open the article submission form</a></p>' . file_get_contents( '/wordpress/issue/guidelines.html' ) ),
	'archives' => array( 'Archives', '<h2>Fall 2026</h2><ul>' ),
);
foreach ( $articles as $article ) {
	$pages['archives'][1] .= '<li><a href="' . esc_url( home_url( '/' . $article['slug'] . '/' ) ) . '">' . esc_html( $article['title'] ) . '</a></li>';
}
$pages['archives'][1] .= '</ul>';
foreach ( $pages as $slug => $page ) {
	$id = wp_insert_post( array( 'post_title' => $page[0], 'post_name' => $slug, 'post_type' => 'page', 'post_content' => $page[1], 'post_status' => 'publish' ), true );
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() );
	}
	if ( 'about' === $slug ) {
		update_post_meta( $id, '_wp_page_template', 'template-about.php' );
	}
}
flush_rewrite_rules();
echo 'Prepared nine articles and editorial pages in the isolated preview.';
