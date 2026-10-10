<?php
/**
 * WesSciJo Design System & Transactional Email Service
 *
 * Implements branded HTML email rendering, template partials,
 * and core WordPress email overrides for the Wesleyan Science Journal.
 *
 * @package WesSciJo_Site
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WesSci_Email {

	/**
	 * Template directory path.
	 *
	 * @var string
	 */
	private static $template_dir = '';

	/**
	 * Initialize hooks and template paths.
	 */
	public static function init() {
		self::$template_dir = dirname( dirname( __FILE__ ) ) . '/templates/email';

		// Hook core WordPress notification emails into the WesSciJo design system.
		add_filter( 'retrieve_password_notification_email', array( __CLASS__, 'filter_password_reset_email' ), 10, 4 );
		add_filter( 'wp_new_user_notification_email', array( __CLASS__, 'filter_new_user_email' ), 10, 3 );
	}

	/**
	 * Get the absolute directory path for email templates.
	 *
	 * @return string
	 */
	public static function get_template_dir() {
		if ( empty( self::$template_dir ) ) {
			self::$template_dir = dirname( dirname( __FILE__ ) ) . '/templates/email';
		}
		return self::$template_dir;
	}

	/**
	 * Render an email template wrapped in the WesSciJo base header and footer.
	 *
	 * @param string $template_name Template slug (e.g. 'editorial-decision').
	 * @param array  $data          Associative array of variables for the template.
	 * @return string Rendered HTML.
	 */
	public static function render( $template_name, $data = array() ) {
		$dir = self::get_template_dir();
		$template_path = $dir . '/' . sanitize_key( $template_name ) . '.php';

		if ( ! file_exists( $template_path ) ) {
			return '<p>Template not found: ' . esc_html( $template_name ) . '</p>';
		}

		// Ensure default values are populated if omitted.
		if ( ! isset( $data['site_url'] ) ) {
			$data['site_url'] = home_url( '/' );
		}
		if ( ! isset( $data['year'] ) ) {
			$data['year'] = date( 'Y' );
		}

		// Extract variables in a clean isolated scope.
		extract( $data, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

		ob_start();

		// Header partial.
		if ( file_exists( $dir . '/header.php' ) ) {
			include $dir . '/header.php';
		}

		// Main template body.
		include $template_path;

		// Footer partial.
		if ( file_exists( $dir . '/footer.php' ) ) {
			include $dir . '/footer.php';
		}

		return ob_get_clean();
	}

	/**
	 * Dispatch an email formatted with the WesSciJo design system.
	 *
	 * @param string|array $to          Recipient email address(es).
	 * @param string       $subject     Email subject line.
	 * @param string       $template    Template slug.
	 * @param array        $data        Template context data.
	 * @param array        $headers     Optional additional headers.
	 * @param array        $attachments Optional file attachments.
	 * @return bool True if mail was sent, false otherwise.
	 */
	public static function send( $to, $subject, $template, $data = array(), $headers = array(), $attachments = array() ) {
		// Populate recipient email in data if not specified so footer note can display it.
		if ( empty( $data['recipient_email'] ) && is_string( $to ) ) {
			$data['recipient_email'] = $to;
		}

		// Ensure preheader and email_title match the subject if not specified.
		if ( empty( $data['email_title'] ) ) {
			$data['email_title'] = $subject;
		}
		if ( empty( $data['preheader'] ) ) {
			$data['preheader'] = $subject . ' | The Wesleyan Science Journal';
		}

		$html_message = self::render( $template, $data );

		// Set HTML content type.
		$has_content_type = false;
		foreach ( $headers as $header ) {
			if ( false !== stripos( $header, 'Content-Type:' ) ) {
				$has_content_type = true;
				break;
			}
		}

		if ( ! $has_content_type ) {
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
		}

		return wp_mail( $to, $subject, $html_message, $headers, $attachments );
	}

	/**
	 * Dispatch an Editorial Decision notice.
	 *
	 * @param string $to   Recipient email.
	 * @param array  $data Data fields (author_name, manuscript_title, decision_label, etc.).
	 * @return bool
	 */
	public static function send_editorial_decision( $to, $data = array() ) {
		$manuscript_title = ! empty( $data['manuscript_title'] ) ? $data['manuscript_title'] : 'Your Manuscript';
		$subject          = 'Editorial Decision: ' . wp_strip_all_tags( $manuscript_title );
		return self::send( $to, $subject, 'editorial-decision', $data );
	}

	/**
	 * Dispatch a Peer Reviewer Invitation.
	 *
	 * @param string $to   Reviewer email.
	 * @param array  $data Data fields (reviewer_name, manuscript_title, division, abstract, etc.).
	 * @return bool
	 */
	public static function send_reviewer_invitation( $to, $data = array() ) {
		$manuscript_title = ! empty( $data['manuscript_title'] ) ? $data['manuscript_title'] : 'Submitted Manuscript';
		$subject          = 'Invitation to Review: ' . wp_strip_all_tags( $manuscript_title );
		return self::send( $to, $subject, 'reviewer-invitation', $data );
	}

	/**
	 * Dispatch a Manuscript Submission Confirmation.
	 *
	 * @param string $to   Author email.
	 * @param array  $data Data fields (author_name, manuscript_title, manuscript_id, division, etc.).
	 * @return bool
	 */
	public static function send_submission_confirmation( $to, $data = array() ) {
		$manuscript_title = ! empty( $data['manuscript_title'] ) ? $data['manuscript_title'] : 'Your Submission';
		$subject          = 'Submission Received: ' . wp_strip_all_tags( $manuscript_title );
		return self::send( $to, $subject, 'submission-confirmation', $data );
	}

	/**
	 * Dispatch an Article Publication Notice.
	 *
	 * @param string $to   Author email.
	 * @param array  $data Data fields (author_name, manuscript_title, volume_number, doi, etc.).
	 * @return bool
	 */
	public static function send_publication_notice( $to, $data = array() ) {
		$volume  = ! empty( $data['volume_number'] ) ? 'Volume ' . $data['volume_number'] : 'The Wesleyan Science Journal';
		$subject = 'Article Published in ' . $volume . ' | The Wesleyan Science Journal';
		return self::send( $to, $subject, 'publication-notice', $data );
	}

	/**
	 * Dispatch an Editorial Account Access / Onboarding email.
	 *
	 * @param string $to   User email.
	 * @param array  $data Data fields (user_name, user_login, user_role, reset_url, etc.).
	 * @return bool
	 */
	public static function send_account_access( $to, $data = array() ) {
		$subject = 'Your WesSciJo account';
		return self::send( $to, $subject, 'account-access', $data );
	}

	/**
	 * Filter WordPress core password reset email to use the WesSciJo design system.
	 *
	 * @param array   $defaults   Notification parameters (to, subject, message, headers).
	 * @param string  $key        Password reset key.
	 * @param string  $user_login User login.
	 * @param WP_User $user_data  User object.
	 * @return array Modified notification parameters.
	 */
	public static function filter_password_reset_email( $defaults, $key, $user_login, $user_data ) {
		$locale    = get_user_locale( $user_data );
		$reset_url = network_site_url( 'wp-login.php?login=' . rawurlencode( $user_login ) . "&key=$key&action=rp", 'login' ) . '&wp_lang=' . $locale;

		$user_name = $user_data->display_name ? $user_data->display_name : $user_login;

		// Map roles to a friendly editorial title if applicable.
		$roles = (array) $user_data->roles;
		$role_label = 'Editorial Contributor';
		if ( in_array( 'administrator', $roles, true ) ) {
			$role_label = 'Administrator';
		} elseif ( in_array( 'editor', $roles, true ) ) {
			$role_label = 'Section Editor';
		} elseif ( in_array( 'author', $roles, true ) ) {
			$role_label = 'Author';
		}

		$html_message = self::render(
			'account-access',
			array(
				'user_name'        => $user_name,
				'user_login'       => $user_login,
				'user_role'        => $role_label,
				'reset_url'        => $reset_url,
				'is_new_user'      => false,
				'preheader'        => 'Reset your WesSciJo password',
				'email_title'      => 'Password Reset | The Wesleyan Science Journal',
				'recipient_email'  => $user_data->user_email,
			)
		);

		$defaults['message'] = $html_message;
		$defaults['subject'] = 'Password Reset | The Wesleyan Science Journal';
		$defaults['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );

		return $defaults;
	}

	/**
	 * Filter WordPress core new user notification email to use the WesSciJo design system.
	 *
	 * @param array   $notification Notification parameters (to, subject, message, headers).
	 * @param WP_User $user         User object.
	 * @param string  $blogname     Site name.
	 * @return array Modified notification parameters.
	 */
	public static function filter_new_user_email( $notification, $user, $blogname ) {
		$key = get_password_reset_key( $user );
		if ( is_wp_error( $key ) ) {
			return $notification;
		}

		$reset_url = network_site_url( 'wp-login.php?login=' . rawurlencode( $user->user_login ) . "&key=$key&action=rp", 'login' );

		$roles = (array) $user->roles;
		$role_label = 'Editorial board member';
		if ( in_array( 'administrator', $roles, true ) ) {
			$role_label = 'Administrator';
		} elseif ( in_array( 'editor', $roles, true ) ) {
			$role_label = 'Section Editor';
		} elseif ( in_array( 'author', $roles, true ) ) {
			$role_label = 'Contributing Author';
		}

		$html_message = self::render(
			'account-access',
			array(
				'user_name'        => $user->display_name ? $user->display_name : $user->user_login,
				'user_login'       => $user->user_login,
				'user_role'        => $role_label,
				'reset_url'        => $reset_url,
				'is_new_user'      => true,
				'preheader'        => 'Set a password for your WesSciJo account',
				'email_title'      => 'Welcome to WesSciJo',
				'recipient_email'  => $user->user_email,
			)
		);

		$notification['message'] = $html_message;
		$notification['subject'] = 'Welcome to WesSciJo';
		$notification['headers'] = array( 'Content-Type: text/html; charset=UTF-8' );

		return $notification;
	}
}
