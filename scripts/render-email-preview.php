<?php
/**
 * Render fictional email examples without WordPress or mail delivery.
 * Run with PHP from the repository root.
 */
if ( defined( 'ABSPATH' ) ) {
	exit( "Use this script outside WordPress.\n" );
}
define( 'ABSPATH', __DIR__ );
set_error_handler( function ( $severity, $message, $file, $line ) {
	throw new ErrorException( $message, 0, $severity, $file, $line );
} );
function esc_html( $text ) { return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' ); }
function esc_url( $url ) {
	return preg_match( '~^https?://~', $url ) ? esc_html( $url ) : '';
}
function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $key ) ); }
function home_url( $path = '/' ) { return 'https://example.org' . $path; }
function admin_url( $path = '' ) { return home_url( '/wp-admin/' . $path ); }
function wp_login_url() { return home_url( '/wp-login.php' ); }
function plugins_url( $path, $plugin ) { return home_url( '/wp-content/plugins/wessci-site/' . $path ); }
require __DIR__ . '/../plugins/wessci-site/includes/class-wessci-email.php';

$common = array(
	'author_name' => 'Alex',
	'reviewer_name' => 'Alex',
	'user_name' => 'Alex',
	'manuscript_title' => 'Optogenetic Control of Dopaminergic Neurons in Drosophila',
	'manuscript_id' => 'WES-DEMO-042',
	'division' => 'Life Sciences',
	'editor_name' => 'Sample editor',
	'editor_title' => 'Life Sciences Editor',
);
$fixtures = array(
	'editorial-decision' => array(
		'decision_label' => 'Revisions requested',
		'decision_note' => 'Please add the additional trials for the 470 nm optical stimulus experiment and revise the Figure 3 captions.',
		'editor_comments' => "Please clarify the photostimulation duration in the methods section.\n\nThe bibliography also needs to include the sources cited in Figure 3.",
		'action_deadline' => 'November 6, 2026',
		'action_url' => home_url( '/manuscript/demo/' ),
		'action_button_text' => 'Submit revisions',
	),
	'reviewer-invitation' => array(
		'format' => 'Research article',
		'abstract' => 'This study examines the locomotor response to optical stimulation of dopaminergic neurons in Drosophila. Responses are compared across stimulation durations and control conditions.',
		'review_deadline' => 'November 6, 2026',
		'accept_url' => home_url( '/review/demo/accept/' ),
		'decline_url' => home_url( '/review/demo/decline/' ),
	),
	'submission-confirmation' => array(
		'submission_date' => 'October 10, 2026',
		'author_roster' => 'Alex, Sam',
		'portal_url' => home_url( '/manuscript/demo/' ),
	),
	'publication-notice' => array(
		'volume_number' => '14',
		'issue_title' => 'Fall 2026',
		'article_url' => home_url( '/articles/demo/' ),
		'citation_text' => 'Alex and Sam. Optogenetic Control of Dopaminergic Neurons in Drosophila. The Wesleyan Science Journal, Volume 14 (2026).',
	),
	'account-access' => array(
		'user_login' => 'sample-editor',
		'user_role' => 'Life Sciences Editor',
		'is_new_user' => true,
		'reset_url' => home_url( '/password/demo/' ),
		'expiration_hours' => 24,
	),
	'review-request' => array(
		'editor_name' => 'Sample editor',
		'submitter_name' => 'Alex',
		'format' => 'Research article',
		'author_roster' => 'Alex, Sam',
		'submission_date' => 'October 18, 2026',
		'missing_details' => 'abstract or deck',
		'edit_url' => admin_url( 'post.php?post=42&action=edit' ),
		'preview_url' => home_url( '/?p=42&preview=true' ),
	),
	'queue-digest' => array(
		'editor_name' => 'Sample editor',
		'issue_name' => 'Fall 2026',
		'overdue' => array(
			array( 'title' => 'Optogenetic Control of Dopaminergic Neurons in Drosophila', 'edit_url' => admin_url( 'post.php?post=42&action=edit' ), 'stage_label' => 'In review', 'days' => 18 ),
			array( 'title' => 'Sediment Cores from the Connecticut River', 'edit_url' => admin_url( 'post.php?post=43&action=edit' ), 'stage_label' => 'Copyediting', 'days' => 15 ),
		),
		'incomplete' => array(
			array( 'title' => 'Error Correction in Small Quantum Devices', 'edit_url' => admin_url( 'post.php?post=44&action=edit' ), 'missing' => 'format, abstract or deck' ),
		),
		'dashboard_url' => admin_url( 'index.php' ),
	),
	'system-message' => array(
		'heading' => 'Your site has updated to WordPress 7.1.4',
		'body_text' => "Howdy! Your site at https://example.org has been updated automatically to WordPress 7.1.4.\n\nNo further action is needed on your part. For more on version 7.1.4, see the About WordPress screen:\nhttps://example.org/wp-admin/about.php\n\nIf you experience any issues or need support, the volunteers in the WordPress.org support forums may be able to help.\nhttps://wordpress.org/support/forums/\n\nThe WordPress Team",
	),
	'issue-ready' => array(
		'editor_name' => 'Sample editor',
		'issue_name' => 'Fall 2026',
		'total' => 9,
		'divisions' => array( 'Life Sciences' => 3, 'Physical Sciences' => 2, 'Quantitative & Computational' => 2, 'Science, Technology & Society' => 2 ),
		'toc_url' => admin_url( 'edit.php?wessci_issue=fall-2026&orderby=menu_order&order=ASC' ),
		'issue_url' => admin_url( 'edit.php?wessci_issue=fall-2026' ),
	),
);
$emails = array();
foreach ( $fixtures as $name => $data ) {
	// Exercise missing optional fields and escaped content in every template.
	$minimal = WesSci_Email::render( $name );
	$hostile = WesSci_Email::render( $name, array_fill_keys( array_keys( $common ), '<script>alert(1)</script>' ) + $data );
	if ( strpos( $minimal, '</html>' ) === false || strpos( $hostile, '<script>' ) !== false ) {
		throw new RuntimeException( 'Email rendering check failed: ' . $name );
	}
	$emails[ $name ] = WesSci_Email::render( $name, $data + $common );
}
$emails['password-reset'] = WesSci_Email::render( 'account-access', array(
	'user_name' => 'Alex', 'user_login' => 'sample-editor', 'is_new_user' => false,
	'reset_url' => home_url( '/password/demo/' ), 'expiration_hours' => 24,
) );
$shell = file_get_contents( __DIR__ . '/../preview/email-preview-shell.html' );
$json = json_encode( $emails, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR );
$logo = 'data:image/png;base64,' . base64_encode( file_get_contents( __DIR__ . '/../plugins/wessci-site/admin/images/wessci-logo.png' ) );
file_put_contents( __DIR__ . '/../preview/email-preview.html', str_replace( array( '__EMAILS__', '__LOGO__' ), array( $json, json_encode( $logo, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR ) ), $shell ) );
echo "Rendered nine templates and password reset. Minimal-data and escaping checks passed. No mail sent.\n";
