<?php
// Exercise rebuild authorization and responses with a stubbed HTTP boundary.
define( 'ABSPATH', __DIR__ );
define( 'WESSCI_VERCEL_DEPLOY_HOOK', 'https://example.invalid/rebuild' );
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function is_admin() { return false; }
function current_user_can( $capability ) { return $GLOBALS['authorized']; }
function wp_die( $message ) { throw new RuntimeException( $message ); }
function check_admin_referer( $action ) { if ( ! $GLOBALS['nonce_valid'] ) { wp_die( 'Invalid nonce' ); } }
class WP_Error {}
function is_wp_error( $result ) { return $result instanceof WP_Error; }
function wp_remote_post( $url, $args ) { $GLOBALS['requests']++; return $GLOBALS['response']; }
function wp_remote_retrieve_response_code( $response ) { return $response; }
function admin_url( $path ) { return $path; }
function add_query_arg( $key, $value, $url ) { return $url . '&' . $key . '=' . $value; }
function wp_safe_redirect( $url ) { throw new RuntimeException( $url ); }
require __DIR__ . '/../plugins/wessci-site/wessci-site.php';
function expect_result( $message, $requests ) {
    $GLOBALS['requests'] = 0;
    try { wessci_site_handle_manual_deploy(); }
    catch ( RuntimeException $error ) {
        if ( $message !== $error->getMessage() || $requests !== $GLOBALS['requests'] ) {
            throw new RuntimeException( 'Unexpected rebuild behavior: ' . $error->getMessage() );
        }
        return;
    }
    throw new RuntimeException( 'Expected request rejection or redirect' );
}
$GLOBALS['authorized'] = false;
$GLOBALS['nonce_valid'] = true;
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array( 'confirm_rebuild' => '1' );
expect_result( 'Unauthorized', 0 );
$GLOBALS['authorized'] = true;
$GLOBALS['nonce_valid'] = false;
expect_result( 'Invalid nonce', 0 );
$GLOBALS['nonce_valid'] = true;
$_SERVER['REQUEST_METHOD'] = 'GET';
expect_result( 'Confirm the rebuild on the publishing screen.', 0 );
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = array();
expect_result( 'Confirm the rebuild on the publishing screen.', 0 );
$_POST = array( 'confirm_rebuild' => '1' );
$GLOBALS['response'] = 202;
expect_result( 'index.php?page=wessci-publishing&wessci_deployed=1', 1 );
$GLOBALS['response'] = 500;
expect_result( 'index.php?page=wessci-publishing&wessci_deployed=0', 1 );
$GLOBALS['response'] = new WP_Error();
expect_result( 'index.php?page=wessci-publishing&wessci_deployed=0', 1 );
echo "Dashboard rebuild checks passed.\n";
