<?php
/**
 * Widget View: Volume & Issue Progress
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Query post counts across the 4 major divisions
$divisions = array(
	'life'  => array( 'label' => 'Life Sciences', 'slugs' => array( 'biology', 'neuroscience', 'psychology' ), 'color' => '#059669', 'count' => 0 ),
	'phys'  => array( 'label' => 'Physical Sciences', 'slugs' => array( 'astronomy', 'physics', 'chemistry', 'earth-environmental' ), 'color' => '#2563eb', 'count' => 0 ),
	'quant' => array( 'label' => 'Quantitative & Comp', 'slugs' => array( 'math', 'computer-science' ), 'color' => '#7c3aed', 'count' => 0 ),
	'sts'   => array( 'label' => 'Science, Tech & Society', 'slugs' => array( 'science-technology-society', 'sts' ), 'color' => '#d97706', 'count' => 0 ),
);

$total_articles = 0;
foreach ( $divisions as $key => &$div ) {
	$posts = get_posts( array(
		'post_type'      => 'post',
		'posts_per_page' => -1,
		'post_status'    => array( 'publish', 'ready_for_issue', 'copyediting', 'in_review' ),
		'category_name'  => implode( ',', $div['slugs'] ),
		'fields'         => 'ids',
	) );
	$div['count'] = count( $posts );
	$total_articles += $div['count'];
}
unset( $div );

// Fallback baseline if no posts exist in development
$display_total = max( $total_articles, 1 );
$target_issue_size = 14;
$progress_pct = min( 100, round( ( $total_articles / $target_issue_size ) * 100 ) );
?>

<div class="wessci-card-header">
	<h3 class="wessci-card-title">
		<span>Volume 14 Issue Assembly</span>
		<span class="wessci-volume-pill">Target: Fall 2026</span>
	</h3>
	<div style="font-size: 13px; color: var(--wes-text-muted);">
		<strong><?php echo (int) $total_articles; ?></strong> / <?php echo (int) $target_issue_size; ?> Manuscripts Active (<?php echo (int) $progress_pct; ?>%)
	</div>
</div>

<p style="font-size: 13px; color: var(--wes-text-muted); margin: 0 0 8px;">
	Visual distribution of accepted and reviewing manuscripts across the four scientific divisions:
</p>

<div class="wessci-progress-track">
	<?php foreach ( $divisions as $key => $div ) : 
		$pct = round( ( $div['count'] / $display_total ) * 100 );
		if ( $pct > 0 ) :
	?>
		<div class="wessci-progress-segment wessci-seg-<?php echo esc_attr( $key ); ?>" style="width: <?php echo (int) $pct; ?>%;" title="<?php echo esc_attr( $div['label'] . ': ' . $div['count'] ); ?>"></div>
	<?php 
		endif;
	endforeach; 
	if ( 0 === $total_articles ) : ?>
		<div class="wessci-progress-segment" style="width: 100%; background: #e2e2e5;" title="No manuscripts active yet"></div>
	<?php endif; ?>
</div>

<div class="wessci-progress-legend">
	<?php foreach ( $divisions as $key => $div ) : ?>
		<div class="wessci-legend-item">
			<span class="wessci-legend-dot" style="background-color: <?php echo esc_attr( $div['color'] ); ?>;"></span>
			<span><strong><?php echo esc_html( $div['label'] ); ?>:</strong> <?php echo (int) $div['count']; ?></span>
		</div>
	<?php endforeach; ?>
</div>
