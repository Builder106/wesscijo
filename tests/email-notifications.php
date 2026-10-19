<?php
/** Check editorial-team notification triggers and recipients without sending mail. */
require __DIR__ . '/../scripts/render-email-preview.php';

define( 'DAY_IN_SECONDS', 86400 );
define( 'MINUTE_IN_SECONDS', 60 );
$single = array();
$posts_by_id = array();
$actions = array();
$sent = array();
$scheduled = array();
$term_meta = array();
$issue_posts = array();
$queue_posts = array();
$post_meta = array();
$editors = array(
	(object) array( 'user_email' => 'aryia@example.org', 'display_name' => 'Aryia' ),
	(object) array( 'user_email' => 'shriya@example.org', 'display_name' => 'Shriya' ),
);
$queries = array();

function add_action( $name, $callback, $priority = 10, $arguments = 1 ) { $GLOBALS['actions'][ $name ] = $callback; }
function add_filter( $name, $callback, $priority = 10, $arguments = 1 ) {}
function wp_next_scheduled( $hook ) { return isset( $GLOBALS['scheduled'][ $hook ] ); }
function wp_schedule_event( $time, $recurrence, $hook ) { $GLOBALS['scheduled'][ $hook ] = $recurrence; }
function get_users( $args ) { $GLOBALS['queries'][] = $args; return $GLOBALS['editors']; }
function get_posts( $args ) {
	$GLOBALS['queries'][] = $args;
	return isset( $args['tax_query'] ) ? $GLOBALS['issue_posts'] : $GLOBALS['queue_posts'];
}
function current_user_can( $capability ) { return false; }
function get_current_user_id() { return 3; }
function wp_get_post_terms( $post_id, $taxonomy, $args ) { return 'names' === $args['fields'] ? array( 'Spring 2027' ) : array( 7 ); }
function wp_schedule_single_event( $time, $hook, $args ) { $GLOBALS['single'][] = array( $hook, $args ); }
function get_post( $id ) { return $GLOBALS['posts_by_id'][ $id ] ?? null; }
function get_userdata( $id ) { return 3 === $id ? (object) array( 'display_name' => 'Shriya', 'user_email' => 'shriya@example.org' ) : false; }
function wp_date( $format ) { return 'October 19, 2026'; }
function get_preview_post_link( $post ) { return home_url( '/?p=' . $post->ID . '&preview=true' ); }
function get_permalink( $post ) { return home_url( '/articles/manuscript-' . $post->ID . '/' ); }
function update_post_meta( $id, $key, $value ) { $GLOBALS['post_meta'][ $id ][ $key ] = $value; }
function is_wp_error( $value ) { return false; }
function get_term( $id, $taxonomy ) { return (object) array( 'term_id' => $id, 'name' => 'Spring 2027', 'slug' => 'spring-2027' ); }
function get_term_meta( $id, $key, $single ) { return $GLOBALS['term_meta'][ $id ][ $key ] ?? ''; }
function update_term_meta( $id, $key, $value ) { $GLOBALS['term_meta'][ $id ][ $key ] = $value; }
function delete_term_meta( $id, $key ) { unset( $GLOBALS['term_meta'][ $id ][ $key ] ); }
function get_post_meta( $id, $key, $single ) { return $GLOBALS['post_meta'][ $id ][ $key ] ?? ''; }
function wp_get_post_categories( $id, $args ) { return array(); }
function wp_list_pluck( $list, $field ) { return array_map( function ( $item ) use ( $field ) { return $item->$field; }, $list ); }
function get_the_title( $post ) { return $post->post_title; }
function get_post_modified_time( $format, $gmt, $post ) { return $post->modified; }
function get_post_status_object( $status ) { return (object) array( 'label' => 'In Review' ); }
function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
function wp_mail( $to, $subject, $message, $headers, $attachments ) {
	$GLOBALS['sent'][] = compact( 'to', 'subject', 'message' );
	return true;
}
function check( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}
function manuscript( $id, $status, $complete = true, $days = 0 ) {
	$GLOBALS['post_meta'][ $id ] = $complete ? array( '_wessci_format' => 'feature', '_wessci_abstract' => 'Deck', '_wessci_division' => 'life' ) : array();
	return (object) array( 'ID' => $id, 'post_type' => 'post', 'post_status' => $status, 'post_title' => 'Manuscript ' . $id, 'modified' => time() - $days * DAY_IN_SECONDS );
}

require __DIR__ . '/../plugins/wessci-site/admin/class-wessci-admin.php';
require __DIR__ . '/../plugins/wessci-site/includes/class-wessci-notifications.php';
WesSci_Notifications::init();

call_user_func( $actions['init'] );
call_user_func( $actions['init'] );
check( $scheduled === array( 'wessci_queue_digest' => 'weekly' ), 'Digest not scheduled weekly, once.' );

$check = $actions['wp_after_insert_post'];
$check( 1, (object) array( 'post_type' => 'page' ) );
check( ! $sent && ! $queries, 'Pages must not trigger issue checks.' );

$issue_posts = array( manuscript( 1, 'ready_for_issue' ), manuscript( 2, 'copyediting' ) );
$check( 1, $issue_posts[0] );
check( ! $sent, 'Issue with a manuscript in copyediting sent mail.' );
check( ! isset( $queries[0]['author'] ) && 7 === $queries[0]['tax_query'][0]['terms'], 'Issue query was scoped to the signed-in user or the wrong issue.' );

$issue_posts = array( manuscript( 1, 'ready_for_issue' ), manuscript( 2, 'ready_for_issue', false ) );
$check( 1, $issue_posts[0] );
check( ! $sent, 'Issue with missing details sent mail.' );

$issue_posts = array( manuscript( 1, 'ready_for_issue' ), manuscript( 2, 'publish' ) );
$check( 1, $issue_posts[0] );
check( 2 === count( $sent ) && 'aryia@example.org' === $sent[0]['to'] && 'shriya@example.org' === $sent[1]['to'], 'Ready issue did not email each editor.' );
check( 'Spring 2027 is ready to publish' === $sent[0]['subject'], 'Wrong issue-ready subject.' );
check( strpos( $sent[0]['message'], 'Dear Aryia' ) !== false && strpos( $sent[0]['message'], 'Life Sciences: 2' ) !== false, 'Issue-ready email lost name or division count.' );
check( strpos( $sent[0]['message'], 'wessci_issue=spring-2027&amp;orderby=menu_order' ) !== false, 'Issue-ready email lost the table of contents link.' );
$check( 1, $issue_posts[0] );
check( 2 === count( $sent ), 'Issue-ready email sent twice.' );

$issue_posts = array( manuscript( 1, 'ready_for_issue' ), manuscript( 2, 'copyediting' ), manuscript( 3, 'ready_for_issue' ) );
$check( 1, $issue_posts[0] );
$issue_posts = array( manuscript( 1, 'ready_for_issue' ), manuscript( 2, 'ready_for_issue' ), manuscript( 3, 'ready_for_issue' ) );
$check( 1, $issue_posts[0] );
check( 4 === count( $sent ), 'Issue that fell back and became ready again was not re-announced.' );

$sent = array();
$term_meta = array();
$issue_posts = array( manuscript( 1, 'publish' ), manuscript( 2, 'publish' ) );
$check( 1, $issue_posts[0] );
check( ! $sent, 'Fully published issue was announced.' );

$queries = array();
$queue_posts = array( manuscript( 4, 'publish', false ), manuscript( 5, 'in_review', true, 3 ) );
call_user_func( $actions['wessci_queue_digest'] );
check( ! $sent, 'Quiet week sent a digest.' );
check( ! isset( $queries[1]['author'] ) && ! isset( $queries[1]['tax_query'] ), 'Digest query was scoped.' );

$queue_posts = array( manuscript( 5, 'in_review', true, 20 ), manuscript( 6, 'draft', false ), manuscript( 7, 'copyediting', true, 2 ) );
call_user_func( $actions['wessci_queue_digest'] );
check( 2 === count( $sent ) && 'Manuscripts that need attention' === $sent[0]['subject'], 'Digest not sent to each editor.' );
$digest = $sent[0]['message'];
check( strpos( $digest, 'Manuscript 5' ) !== false && strpos( $digest, 'In Review for 20 days' ) !== false, 'Overdue manuscript missing from digest.' );
check( strpos( $digest, 'Manuscript 6' ) !== false && strpos( $digest, 'Needs format, abstract or deck, division' ) !== false, 'Incomplete manuscript missing from digest.' );
check( strpos( $digest, 'Manuscript 7' ) === false, 'Recent manuscript listed in digest.' );
check( strpos( $digest, 'post.php?post=5&amp;action=edit' ) !== false, 'Digest lost the edit link.' );

$sent = array();
$editors = array();
call_user_func( $actions['wessci_queue_digest'] );
check( ! $sent, 'Digest sent with no editors.' );
$editors = array(
	(object) array( 'user_email' => 'aryia@example.org', 'display_name' => 'Aryia' ),
	(object) array( 'user_email' => 'shriya@example.org', 'display_name' => 'Shriya' ),
);
$transition = $actions['transition_post_status'];
$transition( 'pending', 'draft', (object) array( 'ID' => 8, 'post_type' => 'page' ) );
$transition( 'pending', 'pending', manuscript( 8, 'pending' ) );
check( ! $single, 'Pages or unchanged statuses scheduled email.' );
$transition( 'pending', 'draft', manuscript( 8, 'pending' ) );
check( array( array( 'wessci_review_request', array( 8, 3 ) ) ) === $single, 'Submit for review did not schedule the review request.' );

$posts_by_id[8] = manuscript( 8, 'draft' );
call_user_func( $actions['wessci_review_request'], 8, 3 );
check( ! $sent, 'Review request sent after the manuscript returned to draft.' );
$posts_by_id[8] = manuscript( 8, 'pending' );
$post_meta[8]['_wessci_authors'] = array( array( 'name' => 'Alex', 'email' => 'alex@example.org' ), array( 'name' => 'Sam' ) );
call_user_func( $actions['wessci_review_request'], 8, 3 );
check( 1 === count( $sent ) && 'aryia@example.org' === $sent[0]['to'], 'Review request not sent to the other editors only.' );
check( 'Ready for review: Manuscript 8' === $sent[0]['subject'], 'Wrong review-request subject.' );
foreach ( array( 'Dear Aryia', 'Shriya submitted', 'Division: Life Sciences', 'Authors: Alex, Sam', 'post.php?post=8&amp;action=edit', '?p=8&amp;preview=true' ) as $text ) {
	check( strpos( $sent[0]['message'], $text ) !== false, 'Review request lost: ' . $text );
}
check( strpos( $sent[0]['message'], 'alex@example.org' ) === false, 'Review request exposed an author email.' );

$sent = array();
$single = array();
$transition( 'publish', 'ready_for_issue', manuscript( 9, 'publish' ) );
check( array( array( 'wessci_publication_notice', array( 9 ) ) ) === $single, 'Publishing did not schedule the publication notice.' );
$posts_by_id[9] = manuscript( 9, 'publish' );
call_user_func( $actions['wessci_publication_notice'], 9 );
check( ! $sent && empty( $post_meta[9]['_wessci_publication_notified'] ), 'Publication notice sent with no author emails.' );
$post_meta[9]['_wessci_authors'] = array( array( 'name' => 'Alex', 'email' => 'alex@example.org' ), array( 'name' => 'Sam', 'email' => '' ) );
call_user_func( $actions['wessci_publication_notice'], 9 );
check( 1 === count( $sent ) && 'alex@example.org' === $sent[0]['to'] && 'Published: Manuscript 9' === $sent[0]['subject'], 'Publication notice not sent to the author with an email.' );
foreach ( array( 'Dear Alex', 'Spring 2027', 'Life Sciences', '/articles/manuscript-9/' ) as $text ) {
	check( strpos( $sent[0]['message'], $text ) !== false, 'Publication notice lost: ' . $text );
}
call_user_func( $actions['wessci_publication_notice'], 9 );
check( 1 === count( $sent ), 'Publication notice sent twice.' );
echo "Issue-ready, digest, review-request, and publication notifications passed. No mail sent.\n";
