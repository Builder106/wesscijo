<?php
/** Issue totals for the selected issue, or the newest issue when none is selected. */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$issue_id = WesSci_Admin::current_issue_id();
$issue = $issue_id ? get_term( $issue_id, 'wessci_issue' ) : null;
if ( ! $issue || is_wp_error( $issue ) ) {
	echo '<p>No issues yet. <a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=wessci_issue' ) ) . '">Create an issue</a> to start assembling articles.</p>';
	return;
}
$posts = array_filter( get_posts( WesSci_Admin::manuscript_args( $issue_id ) ), function ( $manuscript ) { return current_user_can( 'edit_post', $manuscript->ID ); } );
$counts = array();
$open = array();
foreach ( $posts as $manuscript ) {
	$counts[ $manuscript->post_status ] = ( $counts[ $manuscript->post_status ] ?? 0 ) + 1;
	$missing = WesSci_Admin::missing_details( $manuscript->ID );
	if ( $missing ) {
		$open[] = array( $manuscript, $missing );
	}
}
$ready = ( $counts['ready_for_issue'] ?? 0 ) + ( $counts['publish'] ?? 0 );
?>
<h3><?php echo esc_html( $issue->name ); ?></h3>
<p><?php echo (int) $ready; ?> of <?php echo (int) count( $posts ); ?> articles ready for issue or published<?php echo current_user_can( 'edit_others_posts' ) ? '' : ' (your manuscripts)'; ?>.</p>
<dl class="wessci-issue-counts">
<?php foreach ( array( 'draft', 'pending', 'in_review', 'copyediting', 'ready_for_issue', 'publish' ) as $stage ) : if ( empty( $counts[ $stage ] ) ) { continue; } $status = get_post_status_object( $stage ); ?>
<div><dt><?php echo esc_html( $status ? $status->label : $stage ); ?></dt><dd><?php echo (int) $counts[ $stage ]; ?></dd></div>
<?php endforeach; ?>
</dl>
<?php if ( $open ) : ?>
<h4>Missing details</h4>
<ul class="wessci-open-items">
<?php foreach ( array_slice( $open, 0, 5 ) as $item ) : ?>
<li><a href="<?php echo esc_url( get_edit_post_link( $item[0]->ID ) ); ?>"><?php echo esc_html( get_the_title( $item[0]->ID ) ? get_the_title( $item[0]->ID ) : __( '(Untitled manuscript)', 'wessci' ) ); ?></a> <span class="wessci-secondary"><?php echo esc_html( implode( ', ', $item[1] ) ); ?></span></li>
<?php endforeach; ?>
</ul>
<?php if ( count( $open ) > 5 ) : ?><p class="wessci-secondary"><?php echo (int) ( count( $open ) - 5 ); ?> more in the manuscript queue.</p><?php endif; ?>
<?php endif; ?>
<p><a href="<?php echo esc_url( admin_url( 'edit.php?wessci_issue=' . $issue->slug ) ); ?>">Open issue manuscripts</a></p>
