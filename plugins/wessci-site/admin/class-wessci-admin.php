<?php
/**
 * WesSciJo Admin Dashboard Orchestration
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WesSci_Admin {

	public static function body_class( $classes ) {
		$screen = get_current_screen();
		$classes .= in_array( get_user_option( 'admin_color' ), array( false, '', 'fresh', 'modern' ), true ) ? ' wessci-branded' : '';
		return $classes . ( $screen && in_array( $screen->id, array( 'dashboard', 'toplevel_page_wessci-masthead' ), true ) ? ' wessci-workspace' : '' );
	}

	public static function selected_issue() {
		return isset( $_GET['wessci_issue'] ) && is_scalar( $_GET['wessci_issue'] ) ? absint( $_GET['wessci_issue'] ) : 0;
	}

	public static function current_issue_id() {
		if ( self::selected_issue() ) {
			return self::selected_issue();
		}
		$latest = get_terms( array( 'taxonomy' => 'wessci_issue', 'hide_empty' => false, 'orderby' => 'id', 'order' => 'DESC', 'number' => 1, 'fields' => 'ids' ) );
		return is_wp_error( $latest ) || ! $latest ? 0 : (int) $latest[0];
	}

	public static function division_group( $post_id ) {
		$cats   = wp_get_post_categories( $post_id, array( 'fields' => 'slugs' ) );
		$groups = array(
			'life'  => array( 'biology', 'neuroscience', 'psychology', 'life-sciences' ),
			'phys'  => array( 'astronomy', 'physics', 'chemistry', 'earth-environmental', 'physical-sciences' ),
			'quant' => array( 'math', 'computer-science', 'quantitative-computational' ),
			'sts'   => array( 'science-technology-society', 'sts' ),
		);
		foreach ( $groups as $group => $slugs ) {
			if ( array_intersect( $cats, $slugs ) ) {
				return $group;
			}
		}
		return 'other';
	}

	public static function missing_details( $post_id ) {
		$missing = array();
		if ( ! get_post_meta( $post_id, '_wessci_format', true ) ) {
			$missing[] = 'format';
		}
		if ( ! get_post_meta( $post_id, '_wessci_abstract', true ) ) {
			$missing[] = 'abstract or deck';
		}
		if ( 'other' === self::division_group( $post_id ) ) {
			$missing[] = 'division';
		}
		return $missing;
	}

	public static function next_step( $status, $missing, $has_issue ) {
		if ( $missing ) {
			return 'Add ' . implode( ', ', $missing );
		}
		$steps = array(
			'draft'           => 'Submit for review',
			'pending'         => 'Start review',
			'in_review'       => 'Finish review',
			'copyediting'     => 'Finish copyedit',
			'ready_for_issue' => $has_issue ? 'No action needed' : 'Assign to an issue',
			'publish'         => 'No action needed',
		);
		return $steps[ $status ] ?? 'Open manuscript';
	}

	public static function manuscript_args( $issue_id = null ) {
		$issue_id = null === $issue_id ? self::selected_issue() : $issue_id;
		$args = array( 'post_type' => 'post', 'posts_per_page' => -1, 'post_status' => array( 'draft', 'pending', 'in_review', 'copyediting', 'ready_for_issue', 'publish' ), 'orderby' => 'modified', 'order' => 'DESC' );
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			$args['author'] = get_current_user_id();
		}
		if ( $issue_id ) {
			$args['tax_query'] = array( array( 'taxonomy' => 'wessci_issue', 'field' => 'term_id', 'terms' => $issue_id, 'include_children' => false ) );
		}
		return $args;
	}

	/**
	 * Initialize admin hooks.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'configure_dashboard_widgets' ) );
		add_filter( 'admin_body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_menus' ) );
		add_filter( 'gettext', array( __CLASS__, 'welcome_heading' ), 10, 2 );
	}

	public static function welcome_heading( $translation, $text ) {
		return 'Welcome to WordPress!' === $text ? 'Welcome to The Wesleyan Science Journal' : $translation;
	}

	/**
	 * Enqueue admin styles and scripts.
	 *
	 * @param string $hook_suffix Current admin page hook.
	 */
	public static function enqueue_admin_assets( $hook_suffix ) {
		$plugin_dir_url = plugin_dir_url( dirname( __FILE__ ) );

		// Custom Admin Theme
		wp_enqueue_style(
			'wessci-admin-theme',
			$plugin_dir_url . 'admin/css/wessci-admin.css',
			array(),
			(string) filemtime( dirname( __FILE__ ) . '/css/wessci-admin.css' )
		);

		// Dashboard JavaScript on the main index.php page
		if ( 'index.php' === $hook_suffix ) {
			wp_enqueue_script(
				'wessci-admin-js',
				$plugin_dir_url . 'admin/js/wessci-admin.js',
				array(),
				(string) filemtime( dirname( __FILE__ ) . '/js/wessci-admin.js' ),
				true
			);
		}
	}

	/**
	 * Add editorial widgets alongside the native WordPress dashboard widgets.
	 */
	public static function configure_dashboard_widgets() {
		wp_add_dashboard_widget( 'wessci_dashboard_division_queues', __( 'Manuscripts', 'wessci' ), array( __CLASS__, 'render_widget_division_queues' ) );
		wp_add_dashboard_widget( 'wessci_dashboard_issue_progress', __( 'Issue assembly', 'wessci' ), array( __CLASS__, 'render_widget_issue_progress' ), null, null, 'side' );
	}

	/**
	 * Render Volume Issue Progress widget.
	 */
	public static function render_widget_issue_progress() {
		require_once dirname( __FILE__ ) . '/views/widget-issue-progress.php';
	}

	/**
	 * Render Division Editorial Queues widget.
	 */
	public static function render_widget_division_queues() {
		require_once dirname( __FILE__ ) . '/views/widget-division-queues.php';
	}

	/**
	 * Render Scientific Authoring & Figure Guidelines widget.
	 */
	public static function render_widget_authoring_guide() {
		require_once dirname( __FILE__ ) . '/views/widget-authoring-guide.php';
	}

	/**
	 * Register custom administrative pages and submenus.
	 */
	public static function register_admin_menus() {
		add_menu_page(
			__( 'Editorial Board', 'wessci' ),
			__( 'Editorial Board', 'wessci' ),
			'edit_posts',
			'wessci-masthead',
			array( __CLASS__, 'render_masthead_page' ),
			'dashicons-groups',
			25
		);
	}

	/**
	 * Render Masthead Management page.
	 */
	public static function render_masthead_page() {
		require_once dirname( __FILE__ ) . '/views/masthead-management.php';
	}

}
