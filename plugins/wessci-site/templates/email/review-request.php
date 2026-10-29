<?php
/**
 * Template: Manuscript Submitted for Review (editorial team)
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $editor_name (string, optional)
 * - $submitter_name (string, optional)
 * - $manuscript_title (string)
 * - $division (string, optional)
 * - $format (string, optional)
 * - $author_roster (string, optional)
 * - $submission_date (string, optional)
 * - $missing_details (string, optional - e.g. "format, abstract or deck")
 * - $edit_url (string)
 * - $preview_url (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$edit_url    = ! empty( $edit_url ) ? $edit_url : admin_url( 'edit.php?post_status=pending' );
$footer_note = ! empty( $footer_note ) ? $footer_note : 'Sent from the WesSciJo editorial dashboard.';
?>
<h1 style="margin: 0 0 24px; font-size: 32px; line-height: 38px; font-weight: normal; letter-spacing: -0.01em;">New manuscript for review</h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $editor_name ) ? $editor_name : 'Editor' ); ?>,</p>
<p style="margin: 0 0 18px;"><?php echo esc_html( ! empty( $submitter_name ) ? $submitter_name : 'An author' ); ?> submitted <em><?php echo esc_html( ! empty( $manuscript_title ) ? $manuscript_title : 'a manuscript' ); ?></em> for review.</p>
<?php if ( ! empty( $division ) || ! empty( $format ) || ! empty( $author_roster ) || ! empty( $submission_date ) ) : ?>
<p class="wessci-text-muted" style="margin: 0 0 18px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 22px; color: #625b5f;">
<?php if ( ! empty( $division ) ) : ?>Division: <?php echo esc_html( $division ); ?><br><?php endif; ?>
<?php if ( ! empty( $format ) ) : ?>Format: <?php echo esc_html( $format ); ?><br><?php endif; ?>
<?php if ( ! empty( $author_roster ) ) : ?>Authors: <?php echo esc_html( $author_roster ); ?><br><?php endif; ?>
<?php if ( ! empty( $submission_date ) ) : ?>Submitted: <?php echo esc_html( $submission_date ); ?><?php endif; ?>
</p>
<?php endif; ?>
<?php if ( ! empty( $missing_details ) ) : ?>
<p style="margin: 0 0 18px;">Before review starts, it still needs: <?php echo esc_html( $missing_details ); ?>.</p>
<?php endif; ?>
<?php echo WesSci_Email::button( $edit_url, 'Start review' ); ?>
<?php if ( ! empty( $preview_url ) ) : ?>
<p style="margin: 0 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $preview_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">Preview manuscript</a></p>
<?php endif; ?>
<p style="margin: 16px 0 0;">The Wesleyan Science Journal</p>
