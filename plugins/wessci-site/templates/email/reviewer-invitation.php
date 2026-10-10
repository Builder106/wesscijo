<?php
/**
 * Template: Peer Review Assignment & Invitation
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $reviewer_name (string)
 * - $manuscript_title (string)
 * - $manuscript_id (string)
 * - $division (string)
 * - $format (string, optional - e.g. "Research Article", "Perspective")
 * - $abstract (string)
 * - $review_deadline (string)
 * - $accept_url (string)
 * - $decline_url (string)
 * - $editor_name (string, optional)
 * - $editor_title (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<h1 style="margin: 0 0 24px; font-size: 27px; line-height: 34px; font-weight: normal;">Invitation to review</h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $reviewer_name ) ? $reviewer_name : 'Colleague' ); ?>,</p>
<p style="margin: 0 0 18px;">Would you be available to review <em><?php echo esc_html( ! empty( $manuscript_title ) ? $manuscript_title : 'a manuscript' ); ?></em> for The Wesleyan Science Journal?</p>
<?php if ( ! empty( $division ) || ! empty( $format ) || ! empty( $manuscript_id ) ) : ?>
<p class="wessci-text-muted" style="margin: 0 0 18px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #625b5f;">
<?php if ( ! empty( $division ) ) : ?><?php echo esc_html( $division ); ?><br><?php endif; ?>
<?php if ( ! empty( $format ) ) : ?><?php echo esc_html( $format ); ?><br><?php endif; ?>
<?php if ( ! empty( $manuscript_id ) ) : ?>Manuscript <?php echo esc_html( $manuscript_id ); ?><?php endif; ?>
</p>
<?php endif; ?>
<?php if ( ! empty( $review_deadline ) ) : ?>
<p style="margin: 0 0 18px;">We would need your review by <strong><?php echo esc_html( $review_deadline ); ?></strong>.</p>
<?php endif; ?>
<?php if ( ! empty( $abstract ) ) : ?>
<h2 style="margin: 26px 0 8px; font-size: 17px; line-height: 27px;">Abstract</h2>
<p style="margin: 0 0 18px;"><?php echo nl2br( esc_html( $abstract ) ); ?></p>
<?php endif; ?>
<?php if ( ! empty( $accept_url ) ) : ?>
<p style="margin: 8px 0;"><a class="wessci-link" href="<?php echo esc_url( $accept_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">Accept invitation</a></p>
<?php endif; ?>
<?php if ( ! empty( $decline_url ) ) : ?>
<p style="margin: 0 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $decline_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">Decline invitation</a></p>
<?php endif; ?>
<p style="margin: 0 0 24px;">Please let us know if you have a conflict of interest or cannot meet the deadline.</p>
<p style="margin: 0;">Thank you,<br><?php echo esc_html( ! empty( $editor_name ) ? $editor_name : 'The Editorial Board' ); ?><br><?php echo esc_html( ! empty( $editor_title ) ? $editor_title : 'The Wesleyan Science Journal' ); ?></p>
