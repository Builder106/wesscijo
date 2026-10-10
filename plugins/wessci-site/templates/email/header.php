<?php
/**
 * WesSciJo Email Base Header Template
 *
 * @package WesSciJo_Site
 *
 * Variables expected:
 * - $preheader (string, optional)
 * - $email_title (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_url = ! empty( $site_url ) ? $site_url : home_url( '/' );
$logo_url = plugins_url( 'admin/images/wessci-logo.png', dirname( __DIR__, 2 ) . '/wessci-site.php' );
?>
<!DOCTYPE html>
<html lang="en" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="color-scheme" content="light dark">
<meta name="supported-color-schemes" content="light dark">
<title><?php echo esc_html( ! empty( $email_title ) ? $email_title : 'The Wesleyan Science Journal' ); ?></title>
<!--[if mso]><noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript><![endif]-->
<style>
body, table, td { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
table { border-collapse: collapse; }
img { border: 0; -ms-interpolation-mode: bicubic; }
@media screen and (max-width: 480px) {
 .wessci-pad { padding-left: 20px !important; padding-right: 20px !important; }
 .wessci-masthead { font-size: 20px !important; line-height: 25px !important; }
}
@media (prefers-color-scheme: dark) {
 .wessci-paper { background-color: #181617 !important; color: #e9e6e7 !important; }
 .wessci-text-muted { color: #b9b4b7 !important; }
 .wessci-link { color: #ff8b9c !important; }
 .wessci-rule { border-color: #514b4e !important; }
}
</style>
</head>
<body class="wessci-paper" style="margin: 0; padding: 0; background-color: #ffffff; color: #262123;">
<?php if ( ! empty( $preheader ) ) : ?>
<div style="display: none; max-height: 0; max-width: 0; overflow: hidden; opacity: 0; mso-hide: all; font-size: 1px; line-height: 1px;" aria-hidden="true"><?php echo esc_html( $preheader ); ?></div>
<?php endif; ?>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" class="wessci-paper" style="width: 100%; background-color: #ffffff;">
<tr><td align="center">
<!--[if mso]><table role="presentation" width="620" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 620px; table-layout: fixed;">
<tr><td class="wessci-pad" style="padding: 36px 28px 0;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<td width="56" valign="middle" style="width: 56px;"><img src="<?php echo esc_url( $logo_url ); ?>" width="44" height="44" alt="" style="display: block; width: 44px; height: 44px;"></td>
<td valign="middle" style="font-family: Georgia, 'Times New Roman', serif;">
<a class="wessci-link wessci-masthead" href="<?php echo esc_url( $site_url ); ?>" style="color: #a80f29; font-size: 22px; line-height: 28px; text-decoration: none;">The Wesleyan<br>Science Journal</a>
</td></tr></table>
</td></tr>
<tr><td class="wessci-pad wessci-paper" style="padding: 32px 28px 28px; font-family: Georgia, 'Times New Roman', serif; font-size: 17px; line-height: 27px; color: #262123; overflow-wrap: break-word; word-wrap: break-word;">
