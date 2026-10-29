<?php
/**
 * Template: WordPress system notice (updates, password changes, alerts)
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $heading (string)   Subject without the site-name prefix.
 * - $body_text (string) The plain-text message WordPress built.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<h1 style="margin: 0 0 24px; font-size: 32px; line-height: 38px; font-weight: normal; letter-spacing: -0.01em;"><?php echo esc_html( ! empty( $heading ) ? $heading : 'Notice from the journal site' ); ?></h1>
<?php echo WesSci_Email::paragraphs( ! empty( $body_text ) ? $body_text : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in paragraphs(). ?>
