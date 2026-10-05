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

	/**
	 * Initialize admin hooks.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'configure_dashboard_widgets' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_menus' ) );
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', array( __CLASS__, 'custom_admin_menu_order' ) );
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
	 * Configure dashboard: remove noisy default widgets and register WesSciJo editorial widgets.
	 */
	public static function configure_dashboard_widgets() {
		// Remove generic WordPress clutter
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'normal' );

		// Register WesSciJo custom editorial widgets
		wp_add_dashboard_widget(
			'wessci_dashboard_issue_progress',
			__( '📈 Volume 14 Issue Assembly & Progress', 'wessci' ),
			array( __CLASS__, 'render_widget_issue_progress' )
		);

		wp_add_dashboard_widget(
			'wessci_dashboard_division_queues',
			__( '🔬 Division Editorial Queues', 'wessci' ),
			array( __CLASS__, 'render_widget_division_queues' )
		);

		wp_add_dashboard_widget(
			'wessci_dashboard_vercel_deploy',
			__( '🚀 Production & Vercel Deployment', 'wessci' ),
			array( __CLASS__, 'render_widget_vercel_deploy' )
		);

		wp_add_dashboard_widget(
			'wessci_dashboard_authoring_guide',
			__( '📋 Scientific Editorial & Figure Checklist', 'wessci' ),
			array( __CLASS__, 'render_widget_authoring_guide' )
		);
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
	 * Render Vercel Production & Deployment Monitor widget.
	 */
	public static function render_widget_vercel_deploy() {
		require_once dirname( __FILE__ ) . '/views/widget-vercel-deploy.php';
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

	/**
	 * Reorganize sidebar menu order for optimal editorial workflow.
	 *
	 * @param array $menu_order Default menu order.
	 * @return array Reordered menu array.
	 */
	public static function custom_admin_menu_order( $menu_order ) {
		if ( ! $menu_order ) {
			return true;
		}

		return array(
			'index.php',                  // Editorial Command Center (Dashboard)
			'edit.php',                   // Articles & Manuscripts
			'edit-tags.php?taxonomy=wessci_issue', // Volumes & Issues
			'edit.php?post_type=wessci_event',     // Events
			'wessci-masthead',            // Editorial Board
			'upload.php',                 // Media
			'edit.php?post_type=page',    // Pages
			'separator1',
			'themes.php',                 // Appearance
			'plugins.php',                // Plugins
			'users.php',                  // Users
			'tools.php',                  // Tools
			'options-general.php',        // Settings
			'separator-last',
		);
	}
}
