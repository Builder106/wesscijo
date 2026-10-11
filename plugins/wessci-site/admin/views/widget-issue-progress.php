<?php
/**
 * Issue assembly and progress widget.
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$issue_id = WesSci_Admin::current_issue_id();
$issue    = $issue_id ? get_term( $issue_id, 'wessci_issue' ) : null;
if ( ! $issue || is_wp_error( $issue ) ) {
	echo '<div class="wessci-issue-empty">';
	echo '<p>No issues active yet. Create an issue to assemble and organize manuscripts.</p>';
	echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=wessci_issue' ) ) . '">Create an issue</a></p>';
	echo '</div>';
	return;
}

$posts = array_filter(
	get_posts( WesSci_Admin::manuscript_args( $issue_id ) ),
	function ( $manuscript ) {
		return current_user_can( 'edit_post', $manuscript->ID );
	}
);

$counts    = array();
$open      = array();
$divisions = array(
	'life'  => array( 'label' => 'Life Sciences', 'count' => 0 ),
	'phys'  => array( 'label' => 'Physical Sciences', 'count' => 0 ),
	'quant' => array( 'label' => 'Quantitative', 'count' => 0 ),
	'sts'   => array( 'label' => 'STS', 'count' => 0 ),
);

foreach ( $posts as $manuscript ) {
	$counts[ $manuscript->post_status ] = ( $counts[ $manuscript->post_status ] ?? 0 ) + 1;
	$missing                            = WesSci_Admin::missing_details( $manuscript->ID );
	if ( $missing ) {
		$open[] = array( $manuscript, $missing );
	}
	$grp = WesSci_Admin::division_group( $manuscript->ID );
	if ( isset( $divisions[ $grp ] ) ) {
		$divisions[ $grp ]['count']++;
	}
}

$total   = count( $posts );
$ready   = ( $counts['ready_for_issue'] ?? 0 ) + ( $counts['publish'] ?? 0 );
$percent = $total > 0 ? (int) round( ( $ready / $total ) * 100 ) : 0;
?>
<div class="wessci-issue-header">
	<h3><?php echo esc_html( $issue->name ); ?></h3>
	<span class="wessci-badge <?php echo $ready === $total && $total > 0 ? 'wessci-badge--live' : ''; ?>">
		<?php echo $ready === $total && $total > 0 ? 'Edition Ready' : 'In Assembly'; ?>
	</span>
</div>

<div class="wessci-progress-wrap">
	<div class="wessci-progress-label">
		<span><strong><?php echo (int) $ready; ?></strong> of <strong><?php echo (int) $total; ?></strong> manuscripts ready</span>
		<span><strong><?php echo (int) $percent; ?>%</strong></span>
	</div>
	<div class="wessci-progress-bar" role="progressbar" aria-valuenow="<?php echo (int) $percent; ?>" aria-valuemin="0" aria-valuemax="100">
		<div class="wessci-progress-fill" style="width: <?php echo (int) $percent; ?>%;"></div>
	</div>
</div>

<div class="wessci-stats-grid">
	<div class="wessci-stat-card">
		<div class="wessci-stat-num"><?php echo (int) $ready; ?></div>
		<div class="wessci-stat-label">Published / Ready</div>
	</div>
	<div class="wessci-stat-card">
		<div class="wessci-stat-num"><?php echo (int) ( $total - $ready ); ?></div>
		<div class="wessci-stat-label">In Pipeline</div>
	</div>
	<div class="wessci-stat-card">
		<div class="wessci-stat-num"><?php echo (int) $total; ?></div>
		<div class="wessci-stat-label">Total Articles</div>
	</div>
</div>

<div class="wessci-division-summary">
	<h4>Division Distribution</h4>
	<div class="wessci-division-pills">
		<?php foreach ( $divisions as $div_info ) : ?>
			<span class="wessci-div-pill">
				<strong><?php echo esc_html( $div_info['label'] ); ?></strong>: <?php echo (int) $div_info['count']; ?>
			</span>
		<?php endforeach; ?>
	</div>
</div>

<?php if ( $open ) : ?>
<h4>Missing details</h4>
<ul class="wessci-open-items">
	<?php foreach ( array_slice( $open, 0, 5 ) as $item ) : ?>
		<li>
			<a href="<?php echo esc_url( get_edit_post_link( $item[0]->ID ) ); ?>">
				<?php echo esc_html( get_the_title( $item[0]->ID ) ? get_the_title( $item[0]->ID ) : __( '(Untitled manuscript)', 'wessci' ) ); ?>
			</a>
			<span class="wessci-secondary"><?php echo esc_html( implode( ', ', $item[1] ) ); ?></span>
		</li>
	<?php endforeach; ?>
</ul>
	<?php if ( count( $open ) > 5 ) : ?>
		<p class="wessci-secondary"><?php echo (int) ( count( $open ) - 5 ); ?> more in the manuscript queue.</p>
	<?php endif; ?>
<?php endif; ?>

<div class="wessci-action-bar">
	<a class="button button-primary" href="<?php echo esc_url( admin_url( 'edit.php?wessci_issue=' . $issue->slug ) ); ?>">Open issue manuscripts</a>
	<a class="button" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=wessci_issue' ) ); ?>">Manage issues</a>
</div>
