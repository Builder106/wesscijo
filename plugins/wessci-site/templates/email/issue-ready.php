<?php
/**
 * Template: Issue Ready to Publish (editors-in-chief)
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $editor_name (string, optional)
 * - $issue_name (string)
 * - $total (int)
 * - $divisions (array, optional - division label => manuscript count)
 * - $toc_url (string, optional)
 * - $issue_url (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$issue_name  = ! empty( $issue_name ) ? $issue_name : 'The issue';
$total       = ! empty( $total ) ? (int) $total : 0;
$divisions   = ! empty( $divisions ) && is_array( $divisions ) ? array_filter( $divisions ) : array();
$issue_url   = ! empty( $issue_url ) ? $issue_url : admin_url( 'edit.php' );
$footer_note = ! empty( $footer_note ) ? $footer_note : 'Sent from the WesSciJo editorial dashboard.';
?>
<h1 style="margin: 0 0 24px; font-size: 27px; line-height: 34px; font-weight: normal;"><?php echo esc_html( $issue_name ); ?> is ready</h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $editor_name ) ? $editor_name : 'Editor' ); ?>,</p>
<p style="margin: 0 0 18px;">All <?php echo (int) $total; ?> <?php echo 1 === $total ? 'manuscript' : 'manuscripts'; ?> in <?php echo esc_html( $issue_name ); ?> are ready, and none are missing details.</p>
<?php if ( $divisions ) : ?>
<p class="wessci-text-muted" style="margin: 0 0 18px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 22px; color: #625b5f;">
<?php foreach ( $divisions as $label => $count ) : ?><?php echo esc_html( $label ); ?>: <?php echo (int) $count; ?><br><?php endforeach; ?>
</p>
<?php endif; ?>
<p style="margin: 0 0 18px;">Check the table of contents order, then publish the manuscripts when the edition is released.</p>
<?php if ( ! empty( $toc_url ) ) : ?>
<p style="margin: 8px 0;"><a class="wessci-link" href="<?php echo esc_url( $toc_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">Check the table of contents</a></p>
<?php endif; ?>
<p style="margin: 0 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $issue_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">Open issue manuscripts</a></p>
<p style="margin: 16px 0 0;">The Wesleyan Science Journal</p>
