<?php
/**
 * Widget View: Vercel Production & Deployment Monitor
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$deploy_hook = defined( 'WESSCI_VERCEL_DEPLOY_HOOK' ) ? WESSCI_VERCEL_DEPLOY_HOOK : '';
$deploy_nonce = wp_create_nonce( 'wessci_deploy_vercel' );
$deploy_url   = admin_url( 'admin-post.php?action=wessci_trigger_deploy&_wpnonce=' . $deploy_nonce );
?>

<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
	<div style="display: flex; align-items: center; gap: 8px;">
		<span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></span>
		<strong style="font-size: 14px;"><?php esc_html_e( 'Vercel Edge Production', 'wessci' ); ?></strong>
	</div>
	<span style="font-size: 11px; background: #e0f2fe; color: #0369a1; padding: 2px 6px; border-radius: 3px; font-weight: 600;">ACTIVE</span>
</div>

<p style="font-size: 13px; color: var(--wes-text-muted); margin-bottom: 14px;">
	Public journal URL: <a href="https://wessci.yinkavaughan.me/" target="_blank" rel="noopener noreferrer" style="color: var(--wes-red); font-weight: 600;">wessci.yinkavaughan.me ↗</a>
</p>

<div style="background: var(--wes-bg-subtle); border: 1px solid var(--wes-border); padding: 12px; border-radius: 4px; margin-bottom: 14px;">
	<p style="font-size: 12px; color: var(--wes-text-muted); margin: 0 0 8px;">
		Automated deployment triggers on all published post, page, and event updates. Use the manual rebuild trigger to force refresh static caches:
	</p>
	<a href="<?php echo esc_url( $deploy_url ); ?>" class="button button-primary" style="background: var(--wes-red); border-color: var(--wes-red); font-weight: 600;">
		🚀 <?php esc_html_e( 'Trigger Immediate Rebuild', 'wessci' ); ?>
	</a>
</div>
