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
echo "Rendered five templates and password reset. Minimal-data and escaping checks passed. No mail sent.\n";
