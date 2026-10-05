<?php
/** Publishing controls. Deployment health is not inferred from a configured hook. */
if ( ! defined( 'ABSPATH' ) || ! current_user_can( 'manage_options' ) ) { return; }
$configured = defined( 'WESSCI_VERCEL_DEPLOY_HOOK' ) && WESSCI_VERCEL_DEPLOY_HOOK;
?>
<p><a href="https://thewesleyansciencejournal.com/" target="_blank" rel="noopener">Open public journal</a></p>
<p>Deployment status unavailable. A rebuild request does not confirm that the public site has updated.</p>
<?php if ( $configured ) : ?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
<input type="hidden" name="action" value="wessci_trigger_deploy">
<?php wp_nonce_field( 'wessci_deploy_vercel' ); ?>
<p><label><input type="checkbox" name="confirm_rebuild" value="1" required> I want to request a production rebuild.</label></p>
<button class="button button-primary">Request rebuild</button>
</form>
<?php else : ?><p>Manual rebuilding is not configured.</p><?php endif; ?>
