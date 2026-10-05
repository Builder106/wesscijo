<?php
/**
 * Admin View: Editorial Board Masthead Management
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Retrieve custom or fallback masthead
$board = function_exists( 'wessci_editorial_board' ) ? wessci_editorial_board() : array();
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Editorial board', 'wessci' ); ?></h1>
	<hr class="wp-header-end">

	<p class="description" style="margin: 12px 0 20px; font-size: 14px;">
		<?php esc_html_e( 'Editorial roster from the site configuration. Review names and roles with the editorial team before publication.', 'wessci' ); ?>
	</p>

	<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 360px), 1fr)); gap: 20px;">
		<?php foreach ( $board as $div_key => $section ) : ?>
			<div class="wessci-dashboard-card" style="margin-bottom: 0;">
				<div class="wessci-card-header" style="margin-bottom: 12px; padding-bottom: 8px;">
					<h3 class="wessci-card-title"><?php echo esc_html( $section['title'] ); ?></h3>
					<span class="wessci-volume-pill"><?php echo count( $section['members'] ); ?> Editors</span>
				</div>

				<table class="widefat striped" style="box-shadow: none; border: 1px solid #e2e2e5;">
					<thead>
						<tr>
							<th style="width: 44px;"></th>
							<th><?php esc_html_e( 'Name', 'wessci' ); ?></th>
							<th><?php esc_html_e( 'Editorial Role', 'wessci' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $section['members'] as $member ) : ?>
							<tr>
								<td>
									<div style="width: 28px; height: 28px; border-radius: 50%; background: #c51230; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700;">
										<?php echo esc_html( $member['initials'] ?? 'W' ); ?>
									</div>
								</td>
								<td><strong><?php echo esc_html( $member['name'] ); ?></strong></td>
								<td style="color: #646063; font-size: 12px;"><?php echo esc_html( $member['role'] ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endforeach; ?>
	</div>
</div>
