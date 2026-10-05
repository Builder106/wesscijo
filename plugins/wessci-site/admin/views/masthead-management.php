<?php
/**
 * Admin View: Editorial Board roster
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$board = function_exists( 'wessci_editorial_board' ) ? wessci_editorial_board() : array();
?>

<div class="wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Editorial board', 'wessci' ); ?></h1>
	<hr class="wp-header-end">

	<?php if ( ! $board ) : ?>
		<p><?php esc_html_e( 'The roster comes from the active theme. Activate the WesSciJo theme to list the editorial board here.', 'wessci' ); ?></p>
	<?php else : ?>
		<p class="wessci-secondary"><?php esc_html_e( 'Editorial roster from the site configuration. Review names and roles with the editorial team before publication.', 'wessci' ); ?></p>
		<div class="wessci-board-grid">
			<?php foreach ( $board as $section ) : ?>
				<section>
					<h2><?php echo esc_html( $section['title'] ); ?></h2>
					<p class="wessci-secondary"><?php echo (int) count( $section['members'] ); ?> positions</p>
					<table class="widefat striped">
						<thead>
							<tr>
								<th scope="col"><?php esc_html_e( 'Name', 'wessci' ); ?></th>
								<th scope="col"><?php esc_html_e( 'Editorial role', 'wessci' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $section['members'] as $member ) : ?>
								<tr>
									<td><strong><?php echo esc_html( $member['name'] ); ?></strong></td>
									<td><?php echo esc_html( $member['role'] ); ?></td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</section>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>
