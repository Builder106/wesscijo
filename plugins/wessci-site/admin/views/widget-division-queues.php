<?php
/**
 * Widget View: Division Editorial Queues
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$recent_posts = get_posts( array(
	'post_type'      => 'post',
	'posts_per_page' => 15,
	'post_status'    => array( 'publish', 'ready_for_issue', 'copyediting', 'in_review', 'draft' ),
	'orderby'        => 'modified',
	'order'          => 'DESC',
) );

function wessci_get_post_division_group( $post_id ) {
	$cats = wp_get_post_categories( $post_id, array( 'fields' => 'slugs' ) );
	if ( array_intersect( $cats, array( 'biology', 'neuroscience', 'psychology', 'life-sciences' ) ) ) {
		return 'life';
	}
	if ( array_intersect( $cats, array( 'astronomy', 'physics', 'chemistry', 'earth-environmental', 'physical-sciences' ) ) ) {
		return 'phys';
	}
	if ( array_intersect( $cats, array( 'math', 'computer-science', 'quantitative-computational' ) ) ) {
		return 'quant';
	}
	if ( array_intersect( $cats, array( 'science-technology-society', 'sts' ) ) ) {
		return 'sts';
	}
	return 'other';
}
?>

<div class="wessci-tabs-nav" role="tablist">
	<button type="button" class="wessci-tab-btn is-active" data-tab="all" role="tab" aria-selected="true"><?php esc_html_e( 'All Manuscripts', 'wessci' ); ?></button>
	<button type="button" class="wessci-tab-btn" data-tab="life" role="tab" aria-selected="false"><?php esc_html_e( 'Life Sciences', 'wessci' ); ?></button>
	<button type="button" class="wessci-tab-btn" data-tab="phys" role="tab" aria-selected="false"><?php esc_html_e( 'Physical Sciences', 'wessci' ); ?></button>
	<button type="button" class="wessci-tab-btn" data-tab="quant" role="tab" aria-selected="false"><?php esc_html_e( 'Quantitative & Comp', 'wessci' ); ?></button>
	<button type="button" class="wessci-tab-btn" data-tab="sts" role="tab" aria-selected="false"><?php esc_html_e( 'STS', 'wessci' ); ?></button>
</div>

<?php if ( ! empty( $recent_posts ) ) : ?>
	<div style="overflow-x: auto;">
		<table class="wessci-queue-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Manuscript Title', 'wessci' ); ?></th>
					<th><?php esc_html_e( 'Division', 'wessci' ); ?></th>
					<th><?php esc_html_e( 'Author(s)', 'wessci' ); ?></th>
					<th><?php esc_html_e( 'Status', 'wessci' ); ?></th>
					<th style="text-align: right;"><?php esc_html_e( 'Actions', 'wessci' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $recent_posts as $p ) : 
					$div_group = wessci_get_post_division_group( $p->ID );
					$authors = get_post_meta( $p->ID, '_wessci_authors', true );
					$author_str = '';
					if ( is_array( $authors ) && ! empty( $authors ) ) {
						$names = array_filter( array_column( $authors, 'name' ) );
						$author_str = implode( ', ', $names );
					}
					if ( ! $author_str ) {
						$author_str = get_the_author_meta( 'display_name', $p->post_author );
					}

					$status_obj = get_post_status_object( $p->post_status );
					$status_label = $status_obj ? $status_obj->label : ucfirst( $p->post_status );
					$status_class = 'status-' . str_replace( '_', '-', $p->post_status );
				?>
					<tr class="wessci-queue-row" data-division="<?php echo esc_attr( $div_group ); ?>">
						<td>
							<strong>
								<a href="<?php echo esc_url( get_edit_post_link( $p->ID ) ); ?>">
									<?php echo esc_html( get_the_title( $p->ID ) ? get_the_title( $p->ID ) : __( '(Untitled Manuscript)', 'wessci' ) ); ?>
								</a>
							</strong>
						</td>
						<td>
							<span style="font-size: 11px; text-transform: uppercase; color: var(--wes-text-muted); font-weight: 600;">
								<?php echo esc_html( strtoupper( $div_group ) ); ?>
							</span>
						</td>
						<td><?php echo esc_html( $author_str ); ?></td>
						<td>
							<span class="wessci-status-badge <?php echo esc_attr( $status_class ); ?>">
								<?php echo esc_html( $status_label ); ?>
							</span>
						</td>
						<td style="text-align: right; white-space: nowrap;">
							<a href="<?php echo esc_url( get_edit_post_link( $p->ID ) ); ?>" class="wessci-btn-action"><?php esc_html_e( 'Review', 'wessci' ); ?></a>
							<a href="<?php echo esc_url( get_permalink( $p->ID ) ); ?>" target="_blank" class="wessci-btn-action"><?php esc_html_e( 'Preview', 'wessci' ); ?></a>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<p style="padding: 16px; text-align: center; color: var(--wes-text-muted);">
		<?php esc_html_e( 'No manuscripts currently in the editorial queue.', 'wessci' ); ?>
	</p>
<?php endif; ?>
