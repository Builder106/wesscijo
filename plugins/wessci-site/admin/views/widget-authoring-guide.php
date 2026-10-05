<?php
/**
 * Widget View: Scientific Authoring & Figure Guidelines
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<ul class="wessci-checklist">
	<li>
		<span class="wessci-check-icon">✓</span>
		<span><strong><?php esc_html_e( 'Figures & Images:', 'wessci' ); ?></strong> <?php esc_html_e( 'Lead figures must be >= 1200px wide. Always include descriptive caption, creator attribution, and license.', 'wessci' ); ?></span>
	</li>
	<li>
		<span class="wessci-check-icon">✓</span>
		<span><strong><?php esc_html_e( 'Abstract / Deck:', 'wessci' ); ?></strong> <?php esc_html_e( 'Research papers require a 150–250 word formal abstract. Feature articles require a concise narrative deck.', 'wessci' ); ?></span>
	</li>
	<li>
		<span class="wessci-check-icon">✓</span>
		<span><strong><?php esc_html_e( 'Author Credentials:', 'wessci' ); ?></strong> <?php esc_html_e( 'Include student graduation year (e.g. \'26) and primary Wesleyan laboratory or academic department.', 'wessci' ); ?></span>
	</li>
	<li>
		<span class="wessci-check-icon">✓</span>
		<span><strong><?php esc_html_e( 'Citations & DOIs:', 'wessci' ); ?></strong> <?php esc_html_e( 'Format references with full bibliographic data and active https://doi.org/... hyperlinks.', 'wessci' ); ?></span>
	</li>
	<li>
		<span class="wessci-check-icon">✓</span>
		<span><strong><?php esc_html_e( 'Division Tagging:', 'wessci' ); ?></strong> <?php esc_html_e( 'Assign both primary division (Life, Physical, Quant, STS) and specific child discipline (e.g. Neuroscience).', 'wessci' ); ?></span>
	</li>
</ul>
