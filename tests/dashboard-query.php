<?php
// Standalone query checks; no WordPress database or network calls.
define( 'ABSPATH', __DIR__ );
function absint( $value ) { return abs( (int) $value ); }
function current_user_can( $capability ) { return $GLOBALS['can_edit_others']; }
function get_current_user_id() { return 7; }
require __DIR__ . '/../plugins/wessci-site/admin/class-wessci-admin.php';
function check( $condition, $message ) { if ( ! $condition ) { throw new RuntimeException( $message ); } }
$GLOBALS['can_edit_others'] = false;
$_GET = array( 'wessci_issue' => '12' );
$args = WesSci_Admin::manuscript_args();
check( 7 === $args['author'], 'Authors must see their own manuscripts.' );
check( 12 === $args['tax_query'][0]['terms'], 'Counts and queue must use the selected issue.' );
check( false === $args['tax_query'][0]['include_children'], 'An issue must not count child issues.' );
$GLOBALS['can_edit_others'] = true;
$_GET = array();
$args = WesSci_Admin::manuscript_args();
check( ! isset( $args['author'] ), 'Editors may see the shared queue.' );
check( ! isset( $args['tax_query'] ), 'All issues must not impose an issue filter.' );
$_GET = array( 'wessci_issue' => array( '12' ) );
check( 0 === WesSci_Admin::selected_issue(), 'Reject non-scalar issue input.' );
function __( $text, $domain ) { return $text; }
function wp_add_dashboard_widget( ...$args ) { $GLOBALS['widgets'][] = $args; }
function remove_meta_box( ...$args ) { throw new RuntimeException( 'Native widgets must remain available.' ); }
function remove_action( ...$args ) { throw new RuntimeException( 'Native welcome panel must remain available.' ); }
$GLOBALS['widgets'] = array();
WesSci_Admin::configure_dashboard_widgets();
check( 2 === count( $GLOBALS['widgets'] ), 'Add editorial widgets alongside native widgets.' );
check( 'side' === $GLOBALS['widgets'][1][5], 'Issue assembly belongs in a native side column.' );
$args = WesSci_Admin::manuscript_args( 31 );
check( 31 === $args['tax_query'][0]['terms'], 'The issue widget must query its own issue.' );
check( 'Add format, division' === WesSci_Admin::next_step( 'draft', array( 'format', 'division' ), false ), 'Missing details come first.' );
check( 'Finish review' === WesSci_Admin::next_step( 'in_review', array(), false ), 'Each stage names one next step.' );
check( 'Assign to an issue' === WesSci_Admin::next_step( 'ready_for_issue', array(), false ), 'Ready articles need an issue.' );
check( 'No action needed' === WesSci_Admin::next_step( 'ready_for_issue', array(), true ), 'Assigned ready articles need no action.' );
echo "Dashboard query checks passed.\n";
