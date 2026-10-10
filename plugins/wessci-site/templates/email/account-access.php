<?php
/**
 * Template: Editorial Board Onboarding & Password Reset
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $user_name (string)
 * - $user_login (string)
 * - $user_role (string, optional - e.g., "Life Sciences Section Editor")
 * - $reset_url (string)
 * - $is_new_user (bool, optional)
 * - $expiration_hours (int, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reset_url = ! empty( $reset_url ) ? $reset_url : wp_login_url();
$heading_text = ! empty( $is_new_user ) ? 'Welcome to WesSciJo' : 'Reset your password';
?>
<h1 style="margin: 0 0 24px; font-size: 27px; line-height: 34px; font-weight: normal;"><?php echo esc_html( $heading_text ); ?></h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $user_name ) ? $user_name : 'Colleague' ); ?>,</p>
<?php if ( ! empty( $is_new_user ) ) : ?>
<p style="margin: 0 0 18px;">Your account for The Wesleyan Science Journal is ready. Set a password to sign in.</p>
<?php else : ?>
<p style="margin: 0 0 18px;">We received a request to reset the password for your WesSciJo account.</p>
<?php endif; ?>
<?php if ( ! empty( $user_login ) || ! empty( $user_role ) ) : ?>
<p class="wessci-text-muted" style="margin: 0 0 18px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 22px; color: #625b5f;">
<?php if ( ! empty( $user_login ) ) : ?>Username: <?php echo esc_html( $user_login ); ?><br><?php endif; ?>
<?php if ( ! empty( $user_role ) ) : ?>Role: <?php echo esc_html( $user_role ); ?><?php endif; ?>
</p>
<?php endif; ?>
<p style="margin: 8px 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $reset_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;"><?php echo ! empty( $is_new_user ) ? 'Set your password' : 'Reset password'; ?></a></p>
<?php if ( ! empty( $expiration_hours ) ) : ?>
<p style="margin: 0 0 18px;">This link expires in <?php echo esc_html( (int) $expiration_hours ); ?> hours.</p>
<?php endif; ?>
<p style="margin: 0 0 24px;">If you did not request this, you can ignore this email. Do not share the password link.</p>
<p style="margin: 0;">The Wesleyan Science Journal</p>
