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

$site_url = ! empty( $site_url ) ? $site_url : home_url( '/' );
?>
</td></tr>
<tr><td class="wessci-pad" bgcolor="#070304" style="background-color: #070304; padding: 24px 28px 32px; border-top: 4px solid #e9192d; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #d9d4d6;">
<a href="<?php echo esc_url( $site_url ); ?>" style="color: #ff8a98; text-decoration: underline;">The Wesleyan Science Journal</a><br>
Wesleyan University
<?php if ( ! empty( $footer_note ) ) : ?>
<p style="margin: 8px 0 0;"><?php echo esc_html( $footer_note ); ?></p>
<?php endif; ?>
</td></tr>
</table>
<!--[if mso]></td></tr></table><![endif]-->
</td></tr></table>
</body>
</html>
