<?php
/**
 * Editorial-team notifications from the dashboard workflow.
 *
 * Users with the Editor role are the editors-in-chief and receive these.
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WesSci_Notifications {

	const DIGEST_HOOK    = 'wessci_queue_digest';
	const REVIEW_HOOK    = 'wessci_review_request';
	const PUBLISHED_HOOK = 'wessci_publication_notice';
	const NOTIFIED_KEY   = '_wessci_ready_notified';
	const PUBLISHED_KEY  = '_wessci_publication_notified';
	const DIVISIONS    = array(
		'life'  => 'Life Sciences',
		'phys'  => 'Physical Sciences',
		'quant' => 'Quantitative & Computational',
		'sts'   => 'Science, Technology & Society',
	);

	public static function init() {
		add_action( 'init', array( __CLASS__, 'schedule_digest' ) );
		add_action( self::DIGEST_HOOK, array( __CLASS__, 'send_queue_digest' ) );
		// Runs after terms and meta are saved, including block-editor saves.
		add_action( 'wp_after_insert_post', array( __CLASS__, 'check_issues' ), 10, 2 );
		add_action( 'transition_post_status', array( __CLASS__, 'queue_status_emails' ), 10, 3 );
		add_action( self::REVIEW_HOOK, array( __CLASS__, 'send_review_request' ), 10, 2 );
		add_action( self::PUBLISHED_HOOK, array( __CLASS__, 'send_publication_notice' ) );
	}

	/**
	 * Send status emails a minute later. The block editor saves the Manuscript
	 * details box in a second request, so authors and details are only
	 * complete after the status change.
	 */
	public static function queue_status_emails( $new_status, $old_status, $post ) {
		if ( 'post' !== $post->post_type || $new_status === $old_status ) {
			return;
		}
		if ( 'pending' === $new_status ) {
			wp_schedule_single_event( time() + MINUTE_IN_SECONDS, self::REVIEW_HOOK, array( $post->ID, get_current_user_id() ) );
		} elseif ( 'publish' === $new_status ) {
			wp_schedule_single_event( time() + MINUTE_IN_SECONDS, self::PUBLISHED_HOOK, array( $post->ID ) );
		}
	}

	private static function authors( $post_id ) {
		$authors = get_post_meta( $post_id, '_wessci_authors', true );
		return is_array( $authors ) ? $authors : array();
	}

	/**
	 * Tell the editors, except whoever submitted it, that a manuscript is waiting for review.
	 */
	public static function send_review_request( $post_id, $submitter_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'pending' !== $post->post_status ) {
			return;
		}
		$submitter = get_userdata( $submitter_id );
		$data      = array(
			'submitter_name'   => $submitter ? $submitter->display_name : '',
			'manuscript_title' => get_the_title( $post ),
			'division'         => self::DIVISIONS[ WesSci_Admin::division_group( $post_id ) ] ?? '',
			'author_roster'    => implode( ', ', array_filter( array_column( self::authors( $post_id ), 'name' ) ) ),
			'submission_date'  => wp_date( 'F j, Y' ),
			'missing_details'  => implode( ', ', WesSci_Admin::missing_details( $post_id ) ),
			'edit_url'         => self::edit_url( $post_id ),
			'preview_url'      => get_preview_post_link( $post ),
		);
		foreach ( self::editors() as $editor ) {
			if ( $submitter && $submitter->user_email === $editor->user_email ) {
				continue;
			}
			WesSci_Email::send_review_request( $editor->user_email, $data + array( 'editor_name' => $editor->display_name ) );
		}
	}

	/**
	 * Email each author who has an address, once per manuscript.
	 */
	public static function send_publication_notice( $post_id ) {
		$post = get_post( $post_id );
		if ( ! $post || 'publish' !== $post->post_status || get_post_meta( $post_id, self::PUBLISHED_KEY, true ) ) {
			return;
		}
		$issues = wp_get_post_terms( $post_id, 'wessci_issue', array( 'fields' => 'names' ) );
		$data   = array(
			'manuscript_title' => get_the_title( $post ),
			'issue_title'      => ! is_wp_error( $issues ) && $issues ? $issues[0] : '',
			'division'         => self::DIVISIONS[ WesSci_Admin::division_group( $post_id ) ] ?? '',
			'article_url'      => get_permalink( $post ),
		);
		$sent = false;
		foreach ( self::authors( $post_id ) as $author ) {
			if ( empty( $author['email'] ) ) {
				continue;
			}
			WesSci_Email::send_publication_notice( $author['email'], $data + array( 'author_name' => $author['name'] ?? '' ) );
			$sent = true;
		}
		if ( $sent ) {
			update_post_meta( $post_id, self::PUBLISHED_KEY, 1 );
		}
	}

	public static function schedule_digest() {
		if ( ! wp_next_scheduled( self::DIGEST_HOOK ) ) {
			wp_schedule_event( time(), 'weekly', self::DIGEST_HOOK );
		}
	}

	private static function editors() {
		return get_users( array( 'role' => 'editor', 'fields' => array( 'user_email', 'display_name' ) ) );
	}

	/**
	 * Every manuscript, or every manuscript in one issue, regardless of who is signed in.
	 */
	private static function manuscripts( $issue_id ) {
		$args = WesSci_Admin::manuscript_args( $issue_id );
		unset( $args['author'] );
		return get_posts( $args );
	}

	private static function edit_url( $post_id ) {
		// get_edit_post_link() returns nothing when no user is signed in, as in cron.
		return admin_url( 'post.php?post=' . (int) $post_id . '&action=edit' );
	}

	public static function check_issues( $post_id, $post ) {
		if ( 'post' !== $post->post_type ) {
			return;
		}
		$issues = wp_get_post_terms( $post_id, 'wessci_issue', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $issues ) ) {
			return;
		}
		foreach ( $issues as $issue_id ) {
			self::check_issue( (int) $issue_id );
		}
	}

	/**
	 * Email the editors once when every manuscript in an issue is ready and complete,
	 * matching the "Edition Ready" state of the Issue assembly widget.
	 */
	private static function check_issue( $issue_id ) {
		$posts    = self::manuscripts( $issue_id );
		$statuses = wp_list_pluck( $posts, 'post_status' );
		$ready    = $posts && ! array_diff( $statuses, array( 'ready_for_issue', 'publish' ) ) && ! array_filter(
			$posts,
			function ( $manuscript ) {
				return WesSci_Admin::missing_details( $manuscript->ID );
			}
		);

		if ( ! $ready ) {
			delete_term_meta( $issue_id, self::NOTIFIED_KEY );
			return;
		}
		// An issue whose manuscripts are all published is already out.
		if ( ! in_array( 'ready_for_issue', $statuses, true ) || get_term_meta( $issue_id, self::NOTIFIED_KEY, true ) ) {
			return;
		}
		$editors = self::editors();
		$issue   = get_term( $issue_id, 'wessci_issue' );
		if ( ! $editors || ! $issue || is_wp_error( $issue ) ) {
			return;
		}

		$divisions = array();
		foreach ( $posts as $manuscript ) {
			$label               = self::DIVISIONS[ WesSci_Admin::division_group( $manuscript->ID ) ];
			$divisions[ $label ] = ( $divisions[ $label ] ?? 0 ) + 1;
		}
		foreach ( $editors as $editor ) {
			WesSci_Email::send_issue_ready(
				$editor->user_email,
				array(
					'editor_name' => $editor->display_name,
					'issue_name'  => $issue->name,
					'total'       => count( $posts ),
					'divisions'   => $divisions,
					'toc_url'     => admin_url( 'edit.php?wessci_issue=' . $issue->slug . '&orderby=menu_order&order=ASC' ),
					'issue_url'   => admin_url( 'edit.php?wessci_issue=' . $issue->slug ),
				)
			);
		}
		update_term_meta( $issue_id, self::NOTIFIED_KEY, 1 );
	}

	/**
	 * Weekly list of unpublished manuscripts that sat in review or copyediting for
	 * more than 14 days or are missing details. Nothing is sent in a quiet week.
	 */
	public static function send_queue_digest() {
		$editors = self::editors();
		if ( ! $editors ) {
			return;
		}

		$overdue    = array();
		$incomplete = array();
		foreach ( self::manuscripts( 0 ) as $manuscript ) {
			if ( 'publish' === $manuscript->post_status ) {
				continue;
			}
			$item = array(
				'title'    => get_the_title( $manuscript ),
				'edit_url' => self::edit_url( $manuscript->ID ),
			);
			$days = (int) floor( ( time() - (int) get_post_modified_time( 'U', true, $manuscript ) ) / DAY_IN_SECONDS );
			if ( in_array( $manuscript->post_status, array( 'in_review', 'copyediting' ), true ) && $days > 14 ) {
				$status    = get_post_status_object( $manuscript->post_status );
				$overdue[] = $item + array(
					'stage_label' => $status ? $status->label : $manuscript->post_status,
					'days'        => $days,
				);
			}
			$missing = WesSci_Admin::missing_details( $manuscript->ID );
			if ( $missing ) {
				$incomplete[] = $item + array( 'missing' => implode( ', ', $missing ) );
			}
		}
		if ( ! $overdue && ! $incomplete ) {
			return;
		}

		foreach ( $editors as $editor ) {
			WesSci_Email::send_queue_digest(
				$editor->user_email,
				array(
					'editor_name'   => $editor->display_name,
					'overdue'       => $overdue,
					'incomplete'    => $incomplete,
					'dashboard_url' => admin_url( 'index.php' ),
				)
			);
		}
	}
}
