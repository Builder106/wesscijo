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
		$div = get_post_meta( $post_id, '_wessci_division', true );
		if ( $div && in_array( $div, array( 'life', 'phys', 'quant', 'sts' ), true ) ) {
			return $div;
		}
		$cats   = wp_get_post_categories( $post_id, array( 'fields' => 'slugs' ) );
		$groups = array(
			'life'  => array( 'biology', 'neuroscience', 'psychology', 'life-sciences', 'life-science' ),
			'phys'  => array( 'astronomy', 'physics', 'chemistry', 'earth-environmental', 'physical-sciences', 'physical-science' ),
			'quant' => array( 'math', 'computer-science', 'quantitative-computational', 'quantitative-and-computational-science' ),
			'sts'   => array( 'science-technology-society', 'sts', 'science-technology-and-society' ),
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
		add_action( 'init', array( __CLASS__, 'rename_posts_to_manuscripts' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'configure_dashboard_widgets' ) );
		add_filter( 'admin_body_class', array( __CLASS__, 'body_class' ) );
		add_action( 'admin_menu', array( __CLASS__, 'register_admin_menus' ) );
		add_filter( 'gettext', array( __CLASS__, 'welcome_heading' ), 10, 2 );
		add_action( 'admin_init', array( __CLASS__, 'disable_comments_admin' ) );
		add_action( 'wp_before_admin_bar_render', array( __CLASS__, 'cleanup_admin_bar' ) );
		add_filter( 'admin_footer_text', array( __CLASS__, 'custom_admin_footer' ) );
		add_filter( 'update_footer', array( __CLASS__, 'custom_update_footer' ), 11 );
	}

	/**
	 * Relabel core post type to Manuscripts for academic realism.
	 */
	public static function rename_posts_to_manuscripts() {
		global $wp_post_types;
		if ( isset( $wp_post_types['post'] ) ) {
			$labels = &$wp_post_types['post']->labels;
			$labels->name               = _x( 'Manuscripts', 'post type general name', 'wessci' );
			$labels->singular_name      = _x( 'Manuscript', 'post type singular name', 'wessci' );
			$labels->add_new            = _x( 'New Manuscript', 'manuscript', 'wessci' );
			$labels->add_new_item       = __( 'Add New Manuscript', 'wessci' );
			$labels->edit_item          = __( 'Edit Manuscript', 'wessci' );
			$labels->new_item           = __( 'New Manuscript', 'wessci' );
			$labels->view_item          = __( 'View Manuscript', 'wessci' );
			$labels->view_items         = __( 'View Manuscripts', 'wessci' );
			$labels->search_items       = __( 'Search Manuscripts', 'wessci' );
			$labels->not_found          = __( 'No manuscripts found.', 'wessci' );
			$labels->not_found_in_trash = __( 'No manuscripts found in Trash.', 'wessci' );
			$labels->all_items          = __( 'All Manuscripts', 'wessci' );
			$labels->menu_name          = _x( 'Manuscripts', 'admin menu', 'wessci' );
			$labels->name_admin_bar     = _x( 'Manuscript', 'add new on admin bar', 'wessci' );
		}
	}

	public static function welcome_heading( $translation, $text ) {
		$replacements = array(
			'Welcome to WordPress!'                                                                                                                       => 'Welcome to The Wesleyan Science Journal',
			'Author rich content with blocks and patterns'                                                                                                => 'Manuscript Authoring & Review',
			'Block patterns are pre-configured block layouts. Use them to get inspired or create new pages in a flash.'                                   => 'Coordinate article peer review, copyediting, division assignments, and issue assembly.',
			'Add a new page'                                                                                                                              => 'Submit New Manuscript',
			'Start Customizing'                                                                                                                           => 'Authoring & Figure Guidelines',
			'Configure your site’s logo, header, menus, and more in the Customizer.'                                                                     => 'Verify citation standards, student graduation years, laboratory affiliations, and DOI hyperlinks.',
			'Open the Customizer'                                                                                                                         => 'Editorial Board Directory',
			'Discover a new way to build your site.'                                                                                                      => 'Volume & Issue Assembly',
			'There is a new kind of WordPress theme, called a block theme, that lets you build the site you’ve always wanted — with blocks and styles.' => 'Organize ready articles into publication editions, sequence the Table of Contents, and track issue progress.',
			'Learn about block themes'                                                                                                                    => 'Manage Volumes & Issues',
		);
		return $replacements[ $text ] ?? $translation;
	}

	/**
	 * Custom administration footer attribution.
	 */
	public static function custom_admin_footer( $text ) {
		return '<span id="footer-thankyou">' . esc_html__( 'The Wesleyan Science Journal — Student-run undergraduate STEM journal at Wesleyan University', 'wessci' ) . '</span>';
	}

	/**
	 * Custom administration update footer links.
	 */
	public static function custom_update_footer( $text ) {
		return '<a href="' . esc_url( admin_url( 'admin.php?page=wessci-masthead' ) ) . '">' . esc_html__( 'Editorial Board', 'wessci' ) . '</a> | <a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=wessci_issue' ) ) . '">' . esc_html__( 'Volumes & Issues', 'wessci' ) . '</a>';
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
	 * Configure dashboard widgets.
	 */
	public static function configure_dashboard_widgets() {
		// Remove core "WordPress Events and News", "Quick Draft", and "Recent Comments" widgets for all users.
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

		// For editors without full site administration, hide technical debugging widgets.
		if ( ! current_user_can( 'manage_options' ) ) {
			remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
			remove_meta_box( 'fluentsmtp_reports_widget', 'dashboard', 'normal' );
		}

		wp_add_dashboard_widget( 'wessci_dashboard_division_queues', __( 'Manuscripts', 'wessci' ), array( __CLASS__, 'render_widget_division_queues' ) );
		wp_add_dashboard_widget( 'wessci_dashboard_issue_progress', __( 'Issue assembly', 'wessci' ), array( __CLASS__, 'render_widget_issue_progress' ), null, null, 'side' );
		wp_add_dashboard_widget( 'wessci_dashboard_authoring_guide', __( 'Authoring & Figure Guidelines', 'wessci' ), array( __CLASS__, 'render_widget_authoring_guide' ), null, null, 'side' );
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
		remove_menu_page( 'edit-comments.php' );

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
	 * Disable comments in admin: redirect comment screens and remove post type support.
	 */
	public static function disable_comments_admin() {
		global $pagenow;
		if ( 'edit-comments.php' === $pagenow || 'comment.php' === $pagenow ) {
			wp_safe_redirect( admin_url( 'index.php' ) );
			exit;
		}

		remove_post_type_support( 'post', 'comments' );
		remove_post_type_support( 'page', 'comments' );
		remove_post_type_support( 'post', 'trackbacks' );
		remove_post_type_support( 'page', 'trackbacks' );
	}

	/**
	 * Remove the comments icon and core WordPress logo from the admin bar.
	 */
	public static function cleanup_admin_bar() {
		global $wp_admin_bar;
		if ( $wp_admin_bar ) {
			$wp_admin_bar->remove_menu( 'comments' );
			$wp_admin_bar->remove_node( 'wp-logo' );
		}
	}

}

