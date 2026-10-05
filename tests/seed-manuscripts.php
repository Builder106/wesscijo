<?php
$cats = array( 'life-sciences' => 'Life Sciences', 'neuroscience' => 'Neuroscience', 'physical-sciences' => 'Physical Sciences', 'earth-environmental' => 'Earth & Environmental', 'quantitative-computational' => 'Quantitative & Computational', 'sts' => 'Science, Technology & Society' );
$ids = array();
foreach ( $cats as $slug => $name ) {
	$t = wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
	$ids[ $slug ] = is_wp_error( $t ) ? get_category_by_slug( $slug )->term_id : $t['term_id'];
}
$old = wp_insert_term( 'Volume 13, Issue 2', 'wessci_issue' );
$new = wp_insert_term( 'Volume 14, Issue 1', 'wessci_issue' );
$rows = array(
	array( 'Quantum error mitigation in near-term devices', 'in_review', 'quantitative-computational', 'research', '', 'Maya Okafor', $new ),
	array( 'Avian neurobiology in Connecticut', 'copyediting', 'neuroscience', '', 'Songbird navigation under urban light.', 'Daniel Reyes', $new ),
	array( 'Microplastics in Long Island Sound', 'ready_for_issue', 'earth-environmental', 'research', 'Sediment cores show rising particle counts.', 'Priya Shah', $new ),
	array( 'Algorithmic bias in healthcare triage', 'in_review', 'sts', 'feature', '', 'Jordan Lee', $new ),
	array( 'Why the tree of life keeps changing', 'draft', 'life-sciences', 'feature', 'A short history of phylogenetics.', 'Amara Nwosu', 0 ),
	array( 'Untitled pitch on dark matter', 'draft', '', '', '', 'Sam Ortiz', 0 ),
	array( 'Coral bleaching, two decades on', 'publish', 'earth-environmental', 'research', 'A review of reef survey data.', 'Priya Shah', $old ),
);
$by_title = array();
foreach ( $rows as $r ) {
	$id = wp_insert_post( array( 'post_title' => $r[0], 'post_status' => $r[1], 'post_content' => 'Sample manuscript text.', 'post_category' => $r[2] ? array( $ids[ $r[2] ] ) : array() ) );
	$by_title[ $r[0] ] = $id;
	if ( $r[3] ) { update_post_meta( $id, '_wessci_format', $r[3] ); }
	if ( $r[4] ) { update_post_meta( $id, '_wessci_abstract', $r[4] ); }
	update_post_meta( $id, '_wessci_authors', array( array( 'name' => $r[5] ) ) );
	if ( $r[6] ) { wp_set_object_terms( $id, (int) ( is_array( $r[6] ) ? $r[6]['term_id'] : $r[6] ), 'wessci_issue' ); }
}
foreach ( array( 'author1' => 'author', 'editor1' => 'editor' ) as $login => $role ) {
	$uid = wp_insert_user( array( 'user_login' => $login, 'user_pass' => 'password', 'user_email' => $login . '@example.test', 'role' => $role ) );
	if ( 'author1' === $login ) {
		foreach ( array( 'Why the tree of life keeps changing', 'Algorithmic bias in healthcare triage' ) as $title ) {
			wp_update_post( array( 'ID' => $by_title[ $title ], 'post_author' => $uid ) );
		}
	}
}
