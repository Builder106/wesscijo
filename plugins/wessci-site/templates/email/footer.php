<?php
/**
 * WesSciJo Email Base Footer Template
 *
 * @package WesSciJo_Site
 *
 * Variables expected:
 * - $footer_note (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
</td></tr>
<tr><td class="wessci-pad" style="padding: 0 28px 32px;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"><tr>
<td class="wessci-rule wessci-text-muted" style="border-top: 1px solid #dedadc; padding-top: 16px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #625b5f;">
<a class="wessci-link" href="<?php echo esc_url( $site_url ); ?>" style="color: #a80f29; text-decoration: underline;">The Wesleyan Science Journal</a><br>
Wesleyan University
<?php if ( ! empty( $footer_note ) ) : ?>
<p style="margin: 8px 0 0;"><?php echo esc_html( $footer_note ); ?></p>
<?php endif; ?>
</td></tr></table>
</td></tr>
</table>
<!--[if mso]></td></tr></table><![endif]-->
</td></tr></table>
</body>
</html>
