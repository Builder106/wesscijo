<?php
/**
 * Template: Manuscript Submission Confirmation
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $author_name (string)
 * - $manuscript_title (string)
 * - $manuscript_id (string)
 * - $submission_date (string)
 * - $division (string)
 * - $author_roster (string, optional)
 * - $portal_url (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$portal_url = ! empty( $portal_url ) ? $portal_url : admin_url();
?>
<h1 style="margin: 0 0 24px; font-size: 27px; line-height: 34px; font-weight: normal;">Submission received</h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $author_name ) ? $author_name : 'Author' ); ?>,</p>
<p style="margin: 0 0 18px;">Thank you for submitting <em><?php echo esc_html( ! empty( $manuscript_title ) ? $manuscript_title : 'your manuscript' ); ?></em> to The Wesleyan Science Journal. We have received your submission.</p>
<p style="margin: 0 0 24px;">The editors will review it and contact you about next steps.</p>
<p class="wessci-text-muted" style="margin: 0 0 18px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 22px; color: #625b5f;">
<?php if ( ! empty( $manuscript_id ) ) : ?>Manuscript: <?php echo esc_html( $manuscript_id ); ?><br><?php endif; ?>
<?php if ( ! empty( $division ) ) : ?>Division: <?php echo esc_html( $division ); ?><br><?php endif; ?>
<?php if ( ! empty( $author_roster ) ) : ?>Authors: <?php echo esc_html( $author_roster ); ?><br><?php endif; ?>
<?php if ( ! empty( $submission_date ) ) : ?>Received: <?php echo esc_html( $submission_date ); ?><?php endif; ?>
</p>
<p style="margin: 8px 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $portal_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">View submission</a></p>
<?php if ( ! empty( $manuscript_id ) ) : ?>
<p style="margin: 0 0 24px;">Please include <?php echo esc_html( $manuscript_id ); ?> when contacting us about this submission.</p>
<?php endif; ?>
<p style="margin: 0;">Thank you,<br>The Editorial Board</p>
