<?php
/**
 * Widget View: Division Editorial Queues
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$recent_posts = array_filter( get_posts( WesSci_Admin::manuscript_args() ), function ( $manuscript ) { return current_user_can( 'edit_post', $manuscript->ID ); } );
?>

<p class="wessci-intro">Review manuscripts, check their details, and prepare the next issue.</p>
<p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>">New manuscript</a> <a href="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>">Open all manuscripts</a></p>
<form method="get" class="wessci-issue-filter">
<label for="wessci-issue">Issue</label>
<select id="wessci-issue" name="wessci_issue"><option value="0">All issues</option>
<?php $issues = get_terms( array( 'taxonomy' => 'wessci_issue', 'hide_empty' => false ) );
if ( ! is_wp_error( $issues ) ) : foreach ( $issues as $issue ) : ?>
<option value="<?php echo (int) $issue->term_id; ?>" <?php selected( WesSci_Admin::selected_issue(), $issue->term_id ); ?>><?php echo esc_html( $issue->name ); ?></option>
<?php endforeach; endif; ?>
</select><button class="button">Apply issue</button>
</form>
<div class="wessci-filters">
<label>Division <select data-filter="division"><option value="all">All divisions</option><option value="life">Life Sciences</option><option value="phys">Physical Sciences</option><option value="quant">Quantitative &amp; Computational</option><option value="sts">Science, Technology &amp; Society</option><option value="other">Unassigned</option></select></label>
<label>Stage <select data-filter="stage"><option value="active">Active (not published)</option><option value="all">All stages</option><option value="draft">Draft</option><option value="pending">Pending review</option><option value="in_review">In review</option><option value="copyediting">Copyediting</option><option value="ready_for_issue">Ready for issue</option><option value="publish">Published</option></select></label>
<label>Search manuscripts <input type="search" data-filter="search"></label>
</div>
<p data-queue-count aria-live="polite"></p>
<p data-queue-empty hidden>No manuscripts match these filters.</p>

<?php if ( ! empty( $recent_posts ) ) : ?>
	<div class="wessci-table-scroll" tabindex="0" role="region" aria-label="Manuscript queue">
		<table class="wessci-queue-table">
			<caption class="screen-reader-text">Manuscripts in the selected issue</caption>
			<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Manuscript', 'wessci' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Stage', 'wessci' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Next step', 'wessci' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Updated', 'wessci' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $recent_posts as $p ) :
					$div_group = WesSci_Admin::division_group( $p->ID );
					$authors   = get_post_meta( $p->ID, '_wessci_authors', true );
					$author_str = is_array( $authors ) ? implode( ', ', array_filter( array_column( $authors, 'name' ) ) ) : '';
					if ( ! $author_str ) {
						$author_str = get_the_author_meta( 'display_name', $p->post_author );
					}
					$status_obj   = get_post_status_object( $p->post_status );
					$status_label = $status_obj ? $status_obj->label : ucfirst( $p->post_status );
					$next_step    = WesSci_Admin::next_step( $p->post_status, WesSci_Admin::missing_details( $p->ID ), has_term( '', 'wessci_issue', $p ) );
					$divisions    = array( 'life' => 'Life Sciences', 'phys' => 'Physical Sciences', 'quant' => 'Quantitative & Computational', 'sts' => 'Science, Technology & Society', 'other' => 'Division not set' );
				?>
					<tr class="wessci-queue-row" data-division="<?php echo esc_attr( $div_group ); ?>" data-stage="<?php echo esc_attr( $p->post_status ); ?>">
						<td>
							<strong><a href="<?php echo esc_url( get_edit_post_link( $p->ID ) ); ?>"><?php echo esc_html( get_the_title( $p->ID ) ? get_the_title( $p->ID ) : __( '(Untitled manuscript)', 'wessci' ) ); ?></a></strong>
							<span class="wessci-secondary"><?php echo esc_html( $author_str ); ?></span>
							<span class="wessci-secondary"><?php echo esc_html( $divisions[ $div_group ] ); ?></span>
						</td>
						<td><?php echo esc_html( $status_label ); ?></td>
						<td>
							<a href="<?php echo esc_url( get_edit_post_link( $p->ID ) ); ?>"><?php echo esc_html( $next_step ); ?></a>
							<a class="wessci-secondary" href="<?php echo esc_url( get_preview_post_link( $p->ID ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Preview', 'wessci' ); ?></a>
						</td>
						<td><?php echo esc_html( get_the_modified_date( 'M j, Y', $p->ID ) ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php else : ?>
	<p class="wessci-secondary">
		<?php esc_html_e( 'No manuscripts currently in the editorial queue.', 'wessci' ); ?>
	</p>
<?php endif; ?>
