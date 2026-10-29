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
check_email( isset( $filters['wp_mail'] ) && 1 === $filters['wp_mail'][1], 'Plain-mail wrapper not registered.' );
check_email( isset( $filters['wp_new_user_notification_email'] ) && $filters['wp_new_user_notification_email'][1] === 3, 'New-account callback not registered.' );
foreach ( array( 'send_editorial_decision', 'send_reviewer_invitation', 'send_submission_confirmation', 'send_publication_notice', 'send_account_access', 'send_review_request', 'send_queue_digest', 'send_issue_ready' ) as $method ) {
	check_email( WesSci_Email::$method( 'author@example.org', array( 'manuscript_title' => '<b>Sample title</b>' ) ), 'Dispatch failed: ' . $method );
	check_email( $mail['to'] === 'author@example.org' && strpos( $mail['message'], '</html>' ) !== false, 'Invalid recipient or incomplete mail.' );
	check_email( in_array( 'Content-Type: text/html; charset=UTF-8', $mail['headers'], true ), 'Missing HTML content type.' );
	check_email( 2 === count( $mail['headers'] ) && 0 === strpos( $mail['headers'][1], 'Reply-To: The Wesleyan Science Journal <wesscijo' ), 'Missing journal Reply-To or extra headers.' );
	check_email( strpos( $mail['subject'], '<b>' ) === false, 'Unfiltered subject.' );
}
$replies = array(
	'send_publication_notice' => 'wesscijo+authors@wesleyan.edu',
	'send_account_access'     => 'wesscijo+web@wesleyan.edu',
	'send_issue_ready'        => 'wesscijo@wesleyan.edu',
	'send_editorial_decision' => 'wesscijo@wesleyan.edu',
);
foreach ( $replies as $method => $address ) {
	WesSci_Email::$method( 'author@example.org' );
	check_email( 'Reply-To: The Wesleyan Science Journal <' . $address . '>' === $mail['headers'][1], 'Wrong Reply-To for ' . $method );
}
WesSci_Email::send_issue_ready( 'editor@example.org', array( 'issue_name' => '<b>Fall</b>' ) );
check_email( $mail['subject'] === 'Fall is ready to publish', 'Unfiltered issue subject.' );
$digest = WesSci_Email::render( 'queue-digest', array( 'overdue' => array( array( 'title' => '<script>x</script>', 'edit_url' => 'javascript:alert(1)', 'stage_label' => '<i>In review</i>', 'days' => '18 days' ) ) ) );
check_email( strpos( $digest, '<script>' ) === false && strpos( $digest, '<i>' ) === false && strpos( $digest, 'javascript:' ) === false && strpos( $digest, 'for 18 days' ) !== false, 'Digest items unsafe or malformed.' );
check_email( strpos( WesSci_Email::render( 'queue-digest' ), 'Nothing in the queue needs attention this week.' ) !== false, 'Empty digest lost its empty state.' );
WesSci_Email::send( 'author@example.org', 'Sample', 'editorial-decision', array(), array( 'Reply-To: editor@example.org', 'Content-Type: text/html; charset=UTF-8' ), array( 'sample.pdf' ) );
check_email( count( $mail['headers'] ) === 2 && $mail['headers'][0] === 'Reply-To: editor@example.org' && $mail['attachments'] === array( 'sample.pdf' ), 'Dispatch changed headers or attachments.' );
WesSci_Email::send( 'author@example.org', 'Sample', 'editorial-decision', array(), array( 'reply-to: editor@example.org' ) );
check_email( 2 === count( $mail['headers'] ) && 'reply-to: editor@example.org' === $mail['headers'][0], 'Lower-case Reply-To was duplicated.' );

$user = (object) array( 'display_name' => 'Alex <script>alert(1)</script>', 'user_login' => 'alex editor', 'user_email' => 'alex@example.org', 'roles' => array( 'editor' ) );
$original = array( 'to' => $user->user_email, 'subject' => 'Original', 'message' => 'Original', 'headers' => array() );
$reset = call_user_func( $filters['retrieve_password_notification_email'][0], $original, 'sample-key', $user->user_login, $user );
check_email( strpos( $reset['message'], 'login=alex%20editor&amp;key=sample-key&amp;action=rp&amp;wp_lang=en_US' ) !== false, 'Password-reset destination lost login, key or locale.' );
check_email( strpos( $reset['message'], '<script>' ) === false && strpos( $reset['message'], 'Reset password</a>' ) !== false, 'Password-reset content unsafe or missing action.' );
check_email( $reset['to'] === $original['to'], 'Password reset changed recipient.' );
check_email( in_array( 'Reply-To: The Wesleyan Science Journal <wesscijo+web@wesleyan.edu>', $reset['headers'], true ), 'Password reset lost the journal Reply-To.' );
check_email( strpos( $reset['message'], 'Role: Editor-in-Chief' ) !== false, 'Editor role not labelled Editor-in-Chief.' );
$lead = (object) array( 'display_name' => 'Sam', 'user_login' => 'sam', 'user_email' => 'sam@example.org', 'roles' => array( 'contributor' ) );
check_email( strpos( call_user_func( $filters['wp_new_user_notification_email'][0], $original, $lead, 'WesSciJo' )['message'], 'Role: Division Lead' ) !== false, 'Contributor role not labelled Division Lead.' );
$welcome = call_user_func( $filters['wp_new_user_notification_email'][0], $original, $user, 'WesSciJo' );
check_email( strpos( $welcome['message'], 'Set your password</a>' ) !== false && strpos( $welcome['message'], 'key=sample-key' ) !== false, 'New-account email lost password setup.' );
check_email( strpos( $welcome['message'], '48 hours' ) === false, 'Unsupported expiry claim.' );
check_email( in_array( 'Reply-To: The Wesleyan Science Journal <wesscijo+web@wesleyan.edu>', $welcome['headers'], true ), 'Welcome email lost the journal Reply-To.' );
$key_error = true;
check_email( call_user_func( $filters['wp_new_user_notification_email'][0], $original, $user, 'WesSciJo' ) === $original, 'Password-key error did not retain original notification.' );
echo "Mail dispatch and both notification callbacks passed. No mail sent.\n";

$wrap = $filters['wp_mail'][0];
$plain = call_user_func( $wrap, array( 'to' => 'a@example.org', 'subject' => '[The Wesleyan Science Journal] Update <b>done</b>', 'message' => "See https://example.org/a?x=1&y=2.\n\n<script>alert(1)</script>\nline two", 'headers' => '', 'attachments' => array() ) );
check_email( strpos( $plain['message'], '<h1' ) !== false && strpos( $plain['message'], '>Update &lt;b&gt;done&lt;/b&gt;</h1>' ) !== false, 'Subject prefix not stripped or heading not escaped.' );
check_email( strpos( $plain['message'], '<script>' ) === false && strpos( $plain['message'], 'line two' ) !== false && strpos( $plain['message'], '<br>' ) !== false, 'Plain-text body unsafe or lost line breaks.' );
check_email( strpos( $plain['message'], 'href="https://example.org/a?x=1&amp;y=2"' ) !== false && strpos( $plain['message'], '</a>.</p>' ) !== false, 'Link not wrapped or trailing period swallowed.' );
check_email( in_array( 'Content-Type: text/html; charset=UTF-8', $plain['headers'], true ) && count( $plain['headers'] ) === 2 && 0 === strpos( $plain['headers'][1], 'Reply-To:' ), 'Wrapped mail headers wrong.' );
$mailer = (object) array( 'ContentType' => 'text/html', 'AltBody' => '' );
call_user_func( $filters['phpmailer_init'][0], $mailer );
check_email( strpos( $mailer->AltBody, 'line two' ) !== false && strpos( $mailer->AltBody, '<h1' ) === false, 'Plain-text part missing or HTML.' );
$mailer->AltBody = '';
call_user_func( $filters['phpmailer_init'][0], $mailer );
check_email( '' === $mailer->AltBody, 'Plain-text part reused for a later message.' );
$html_mail = array( 'to' => 'a@example.org', 'subject' => 'S', 'message' => '<p>Hi</p>', 'headers' => array( 'Content-Type: text/html; charset=UTF-8' ), 'attachments' => array() );
check_email( call_user_func( $wrap, $html_mail ) === $html_mail, 'HTML mail was wrapped twice.' );
$own = call_user_func( $wrap, array( 'to' => 'a@example.org', 'subject' => 'S', 'message' => 'Hi', 'headers' => "reply-to: x@example.org\r\n", 'attachments' => array() ) );
check_email( 2 === count( $own['headers'] ), 'Wrapped mail replaced the caller Reply-To.' );
echo "Plain-text mail wrapping passed. No mail sent.\n";
