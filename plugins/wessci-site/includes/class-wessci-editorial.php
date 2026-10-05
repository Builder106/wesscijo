<?php
/**
 * WesSciJo Editorial Pipeline & Taxonomies
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WesSci_Editorial {

	/**
	 * Initialize editorial hooks.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_editorial_statuses' ) );
		add_action( 'init', array( __CLASS__, 'register_issue_taxonomy' ) );
		add_filter( 'display_post_states', array( __CLASS__, 'display_custom_post_states' ), 10, 2 );
		add_action( 'admin_footer-post.php', array( __CLASS__, 'inject_post_status_options' ) );
		add_action( 'admin_footer-post-new.php', array( __CLASS__, 'inject_post_status_options' ) );
		add_action( 'admin_footer-edit.php', array( __CLASS__, 'inject_quick_edit_statuses' ) );
	}

	/**
	 * Register custom editorial workflow statuses.
	 */
	public static function register_editorial_statuses() {
		$statuses = array(
			'in_review'        => array(
				'label'                     => _x( 'In Review', 'post status', 'wessci' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: post count */
				'label_count'               => _n_noop( 'In Review <span class="count">(%s)</span>', 'In Review <span class="count">(%s)</span>', 'wessci' ),
			),
			'copyediting'      => array(
				'label'                     => _x( 'Copyediting', 'post status', 'wessci' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: post count */
				'label_count'               => _n_noop( 'Copyediting <span class="count">(%s)</span>', 'Copyediting <span class="count">(%s)</span>', 'wessci' ),
			),
			'ready_for_issue'  => array(
				'label'                     => _x( 'Ready for Issue', 'post status', 'wessci' ),
				'public'                    => false,
				'exclude_from_search'       => true,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: post count */
				'label_count'               => _n_noop( 'Ready for Issue <span class="count">(%s)</span>', 'Ready for Issue <span class="count">(%s)</span>', 'wessci' ),
			),
		);

		foreach ( $statuses as $slug => $args ) {
			register_post_status( $slug, $args );
		}
	}

	/**
	 * Register Issues & Volumes taxonomy for organizing articles into publication releases.
	 */
	public static function register_issue_taxonomy() {
		$labels = array(
			'name'              => _x( 'Volumes & Issues', 'taxonomy general name', 'wessci' ),
			'singular_name'     => _x( 'Issue', 'taxonomy singular name', 'wessci' ),
			'search_items'      => __( 'Search Issues', 'wessci' ),
			'all_items'         => __( 'All Issues', 'wessci' ),
			'parent_item'       => __( 'Parent Volume', 'wessci' ),
			'parent_item_colon' => __( 'Parent Volume:', 'wessci' ),
			'edit_item'         => __( 'Edit Issue', 'wessci' ),
			'update_item'       => __( 'Update Issue', 'wessci' ),
			'add_new_item'      => __( 'Add New Volume / Issue', 'wessci' ),
			'new_item_name'     => __( 'New Issue Title', 'wessci' ),
			'menu_name'         => __( 'Volumes & Issues', 'wessci' ),
		);

		register_taxonomy(
			'wessci_issue',
			array( 'post' ),
			array(
				'hierarchical'      => true,
				'labels'            => $labels,
				'show_ui'           => true,
				'show_admin_column' => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'volume' ),
				'show_in_rest'      => true,
			)
		);
	}

	/**
	 * Display custom editorial states in the post list table.
	 *
	 * @param array   $post_states Existing post states.
	 * @param WP_Post $post Current post object.
	 * @return array Modified post states.
	 */
	public static function display_custom_post_states( $post_states, $post ) {
		if ( 'in_review' === $post->post_status ) {
			$post_states['in_review'] = '<span class="wessci-status-badge status-review">' . _x( 'In Review', 'post status', 'wessci' ) . '</span>';
		} elseif ( 'copyediting' === $post->post_status ) {
			$post_states['copyediting'] = '<span class="wessci-status-badge status-copyedit">' . _x( 'Copyediting', 'post status', 'wessci' ) . '</span>';
		} elseif ( 'ready_for_issue' === $post->post_status ) {
			$post_states['ready_for_issue'] = '<span class="wessci-status-badge status-ready">' . _x( 'Ready for Issue', 'post status', 'wessci' ) . '</span>';
		}
		return $post_states;
	}

	/**
	 * Inject custom post statuses into the post edit screen Status dropdown.
	 */
	public static function inject_post_status_options() {
		global $post;
		if ( ! $post || 'post' !== $post->post_type ) {
			return;
		}

		$current_status = $post->post_status;
		$statuses = array(
			'in_review'       => _x( 'In Review', 'post status', 'wessci' ),
			'copyediting'     => _x( 'Copyediting', 'post status', 'wessci' ),
			'ready_for_issue' => _x( 'Ready for Issue', 'post status', 'wessci' ),
		);
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var select = document.getElementById('post_status');
			if (!select) return;

			var statuses = <?php echo wp_json_encode( $statuses ); ?>;
			var current = <?php echo wp_json_encode( $current_status ); ?>;

			Object.keys(statuses).forEach(function(key) {
				var opt = document.createElement('option');
				opt.value = key;
				opt.text = statuses[key];
				if (current === key) {
					opt.selected = true;
					var label = document.getElementById('post-status-display');
					if (label) label.textContent = statuses[key];
				}
				select.appendChild(opt);
			});
		});
		</script>
		<?php
	}

	/**
	 * Inject custom post statuses into Quick Edit dropdowns on edit.php.
	 */
	public static function inject_quick_edit_statuses() {
		global $post_type;
		if ( 'post' !== $post_type ) {
			return;
		}
		?>
		<script>
		document.addEventListener('DOMContentLoaded', function() {
			var select = document.querySelector('select[name="_status"]');
			if (!select) return;

			var statuses = {
				'in_review': '<?php echo esc_js( _x( 'In Review', 'post status', 'wessci' ) ); ?>',
				'copyediting': '<?php echo esc_js( _x( 'Copyediting', 'post status', 'wessci' ) ); ?>',
				'ready_for_issue': '<?php echo esc_js( _x( 'Ready for Issue', 'post status', 'wessci' ) ); ?>'
			};

			Object.keys(statuses).forEach(function(key) {
				var opt = document.createElement('option');
				opt.value = key;
				opt.text = statuses[key];
				select.appendChild(opt);
			});
		});
		</script>
		<?php
	}
}
