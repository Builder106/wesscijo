<?php
/**
 * Template: Weekly Manuscript Queue Digest (editorial team)
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $editor_name (string, optional)
 * - $issue_name (string, optional)
 * - $overdue (array, optional - items with title, edit_url, stage_label, days)
 * - $incomplete (array, optional - items with title, edit_url, missing)
 * - $dashboard_url (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$overdue       = ! empty( $overdue ) && is_array( $overdue ) ? $overdue : array();
$incomplete    = ! empty( $incomplete ) && is_array( $incomplete ) ? $incomplete : array();
$dashboard_url = ! empty( $dashboard_url ) ? $dashboard_url : admin_url( 'index.php' );
$scope         = ! empty( $issue_name ) ? $issue_name : 'the queue';
$footer_note   = ! empty( $footer_note ) ? $footer_note : 'Sent weekly from the WesSciJo editorial dashboard.';
?>
<h1 style="margin: 0 0 24px; font-size: 27px; line-height: 34px; font-weight: normal;">Manuscripts that need attention</h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $editor_name ) ? $editor_name : 'Editor' ); ?>,</p>
<?php if ( ! $overdue && ! $incomplete ) : ?>
<p style="margin: 0 0 18px;">Nothing in <?php echo esc_html( $scope ); ?> needs attention this week.</p>
<?php else : ?>
<p style="margin: 0 0 18px;">These manuscripts in <?php echo esc_html( $scope ); ?> are waiting on the editorial team.</p>
<?php endif; ?>
<?php if ( $overdue ) : ?>
<h2 style="margin: 26px 0 8px; font-size: 17px; line-height: 27px;">Waiting more than 14 days</h2>
<?php foreach ( $overdue as $item ) : ?>
<p style="margin: 0 0 14px;"><a class="wessci-link" href="<?php echo esc_url( $item['edit_url'] ?? $dashboard_url ); ?>" style="color: #a80f29; text-decoration: underline;"><?php echo esc_html( ! empty( $item['title'] ) ? $item['title'] : 'Untitled manuscript' ); ?></a><br>
<span class="wessci-text-muted" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #625b5f;"><?php echo esc_html( $item['stage_label'] ?? 'In stage' ); ?> for <?php echo (int) ( $item['days'] ?? 0 ); ?> days</span></p>
<?php endforeach; ?>
<?php endif; ?>
<?php if ( $incomplete ) : ?>
<h2 style="margin: 26px 0 8px; font-size: 17px; line-height: 27px;">Missing details</h2>
<?php foreach ( $incomplete as $item ) : ?>
<p style="margin: 0 0 14px;"><a class="wessci-link" href="<?php echo esc_url( $item['edit_url'] ?? $dashboard_url ); ?>" style="color: #a80f29; text-decoration: underline;"><?php echo esc_html( ! empty( $item['title'] ) ? $item['title'] : 'Untitled manuscript' ); ?></a><br>
<span class="wessci-text-muted" style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #625b5f;">Needs <?php echo esc_html( $item['missing'] ?? 'details' ); ?></span></p>
<?php endforeach; ?>
<?php endif; ?>
<p style="margin: 8px 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $dashboard_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">Open the dashboard</a></p>
<p style="margin: 16px 0 0;">The Wesleyan Science Journal</p>
