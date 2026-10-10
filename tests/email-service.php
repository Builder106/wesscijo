<?php
/** Check mail dispatch and WordPress notification callbacks without sending mail. */
require __DIR__ . '/../scripts/render-email-preview.php';

$filters = array();
$mail = null;
$key_error = false;
function add_filter( $name, $callback, $priority, $arguments ) {
	$GLOBALS['filters'][ $name ] = array( $callback, $arguments );
}
function wp_strip_all_tags( $text ) { return strip_tags( $text ); }
function wp_mail( $to, $subject, $message, $headers, $attachments ) {
	$GLOBALS['mail'] = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
	return true;
}
function get_user_locale( $user ) { return 'en_US'; }
function network_site_url( $path, $scheme ) { return home_url( '/' . $path ); }
function get_password_reset_key( $user ) { return $GLOBALS['key_error'] ? new Exception( 'Test error' ) : 'sample-key'; }
function is_wp_error( $value ) { return $value instanceof Exception; }
function check_email( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

WesSci_Email::init();
check_email( isset( $filters['retrieve_password_notification_email'] ) && $filters['retrieve_password_notification_email'][1] === 4, 'Password-reset callback not registered.' );
check_email( isset( $filters['wp_new_user_notification_email'] ) && $filters['wp_new_user_notification_email'][1] === 3, 'New-account callback not registered.' );
foreach ( array( 'send_editorial_decision', 'send_reviewer_invitation', 'send_submission_confirmation', 'send_publication_notice', 'send_account_access' ) as $method ) {
	check_email( WesSci_Email::$method( 'author@example.org', array( 'manuscript_title' => '<b>Sample title</b>' ) ), 'Dispatch failed: ' . $method );
	check_email( $mail['to'] === 'author@example.org' && strpos( $mail['message'], '</html>' ) !== false, 'Invalid recipient or incomplete mail.' );
	check_email( in_array( 'Content-Type: text/html; charset=UTF-8', $mail['headers'], true ), 'Missing HTML content type.' );
	check_email( strpos( $mail['subject'], '<b>' ) === false, 'Unfiltered subject.' );
}
WesSci_Email::send( 'author@example.org', 'Sample', 'editorial-decision', array(), array( 'Reply-To: editor@example.org', 'Content-Type: text/html; charset=UTF-8' ), array( 'sample.pdf' ) );
check_email( count( $mail['headers'] ) === 2 && $mail['headers'][0] === 'Reply-To: editor@example.org' && $mail['attachments'] === array( 'sample.pdf' ), 'Dispatch changed headers or attachments.' );

$user = (object) array( 'display_name' => 'Alex <script>alert(1)</script>', 'user_login' => 'alex editor', 'user_email' => 'alex@example.org', 'roles' => array( 'editor' ) );
$original = array( 'to' => $user->user_email, 'subject' => 'Original', 'message' => 'Original', 'headers' => array() );
$reset = call_user_func( $filters['retrieve_password_notification_email'][0], $original, 'sample-key', $user->user_login, $user );
check_email( strpos( $reset['message'], 'login=alex%20editor&amp;key=sample-key&amp;action=rp&amp;wp_lang=en_US' ) !== false, 'Password-reset destination lost login, key or locale.' );
check_email( strpos( $reset['message'], '<script>' ) === false && strpos( $reset['message'], 'Reset password</a>' ) !== false, 'Password-reset content unsafe or missing action.' );
check_email( $reset['to'] === $original['to'], 'Password reset changed recipient.' );
$welcome = call_user_func( $filters['wp_new_user_notification_email'][0], $original, $user, 'WesSciJo' );
check_email( strpos( $welcome['message'], 'Set your password</a>' ) !== false && strpos( $welcome['message'], 'key=sample-key' ) !== false, 'New-account email lost password setup.' );
check_email( strpos( $welcome['message'], '48 hours' ) === false, 'Unsupported expiry claim.' );
$key_error = true;
check_email( call_user_func( $filters['wp_new_user_notification_email'][0], $original, $user, 'WesSciJo' ) === $original, 'Password-key error did not retain original notification.' );
echo "Mail dispatch and both notification callbacks passed. No mail sent.\n";
