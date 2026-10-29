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
	 * Plus tags that sort replies in the journal's Google Group by template.
	 * Templates not listed reply to the plain group address.
	 */
	const REPLY_TAGS = array(
		'publication-notice' => 'authors',
		'account-access'     => 'web',
	);

	/**
	 * Primary action as a bulletproof button.
	 *
	 * @param string $url   Destination.
	 * @param string $label Button text.
	 * @return string HTML.
	 */
	public static function button( $url, $label ) {
		return '<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 8px 0 18px;"><tr>'
			. '<td bgcolor="#b3112a" style="background-color: #b3112a;">'
			. '<a href="' . esc_url( $url ) . '" style="display: inline-block; padding: 13px 26px; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 20px; font-weight: bold; color: #ffffff; text-decoration: none;">'
			. esc_html( $label ) . '</a></td></tr></table>';
	}

	/**
	 * Replies go to the journal's Google Group, not to whoever owns the SMTP account.
	 *
	 * @param string $template Template slug.
	 * @return string Reply-To header.
	 */
	public static function reply_to( $template = '' ) {
		$tag = self::REPLY_TAGS[ $template ] ?? '';
		return 'Reply-To: The Wesleyan Science Journal <wesscijo' . ( $tag ? '+' . $tag : '' ) . '@wesleyan.edu>';
	}

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

		// Wrap every other plain-text message (core, plugin and theme notices).
		add_filter( 'wp_mail', array( __CLASS__, 'filter_plain_mail' ), 10, 1 );
		add_filter( 'phpmailer_init', array( __CLASS__, 'keep_plain_alternative' ), 10, 1 );
	}

	/**
	 * Plain-text original of the message being wrapped, for the text part.
	 *
	 * @var string|null
	 */
	private static $plain_text = null;

	/**
	 * Wrap a plain-text message in the system-message template.
	 * Messages that already declare an HTML content type pass through untouched.
	 *
	 * @param array $args wp_mail arguments (to, subject, message, headers, attachments).
	 * @return array
	 */
	public static function filter_plain_mail( $args ) {
		$headers = $args['headers'] ?? array();
		if ( ! is_array( $headers ) ) {
			$headers = array_filter( explode( "\n", str_replace( "\r\n", "\n", (string) $headers ) ) );
		}
		foreach ( $headers as $header ) {
			if ( 0 === stripos( trim( $header ), 'content-type:' ) && false !== stripos( $header, 'text/html' ) ) {
				return $args;
			}
		}

		$text    = (string) ( $args['message'] ?? '' );
		$subject = (string) ( $args['subject'] ?? '' );
		$heading = trim( preg_replace( '/^\[[^\]]+\]\s*/', '', $subject ) );

		$args['message'] = self::render(
			'system-message',
			array(
				'heading'     => $heading,
				'body_text'   => $text,
				'email_title' => $subject,
				'preheader'   => $heading . ' | The Wesleyan Science Journal',
			)
		);

		$names = array_map(
			function ( $header ) {
				return strtolower( trim( strtok( $header, ':' ) ) );
			},
			$headers
		);
		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		if ( ! in_array( 'reply-to', $names, true ) ) {
			$headers[] = self::reply_to( 'system-message' );
		}
		$args['headers'] = $headers;

		self::$plain_text = $text;
		return $args;
	}

	/**
	 * Send the original text as the plain-text part of a wrapped message.
	 *
	 * @param object $phpmailer PHPMailer instance.
	 */
	public static function keep_plain_alternative( $phpmailer ) {
		if ( null !== self::$plain_text && 'text/html' === $phpmailer->ContentType ) {
			$phpmailer->AltBody = self::$plain_text;
		}
		self::$plain_text = null;
	}

	/**
	 * Turn plain text into escaped paragraphs with clickable links.
	 *
	 * @param string $text Plain text; blank lines separate paragraphs.
	 * @return string HTML.
	 */
	public static function paragraphs( $text ) {
		$text = trim( str_replace( "\r\n", "\n", (string) $text ) );
		if ( '' === $text ) {
			return '';
		}
		$html = '';
		foreach ( preg_split( '/\n{2,}/', $text ) as $block ) {
			$parts = preg_split( '~(https?://[^\s<>"]+)~', $block, -1, PREG_SPLIT_DELIM_CAPTURE );
			$out   = '';
			foreach ( $parts as $index => $part ) {
				if ( 1 === $index % 2 ) {
					$trail = '';
					if ( preg_match( '/[.,;:!?)]+$/', $part, $m ) ) {
						$trail = $m[0];
						$part  = substr( $part, 0, -strlen( $trail ) );
					}
					$url  = esc_url( $part );
					$out .= $url
						? '<a class="wessci-link" href="' . $url . '" style="color: #a80f29; text-decoration: underline;">' . esc_html( $part ) . '</a>' . esc_html( $trail )
						: esc_html( $part . $trail );
				} else {
					$out .= esc_html( $part );
				}
			}
			$html .= '<p style="margin: 0 0 18px;">' . str_replace( "\n", '<br>', $out ) . "</p>\n";
		}
		return $html;
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

		// Add the HTML content type and journal Reply-To unless the caller set its own.
		$names = array_map(
			function ( $header ) {
				return strtolower( trim( strtok( $header, ':' ) ) );
			},
			$headers
		);
		if ( ! in_array( 'content-type', $names, true ) ) {
			$headers[] = 'Content-Type: text/html; charset=UTF-8';
		}
		if ( ! in_array( 'reply-to', $names, true ) ) {
			$headers[] = self::reply_to( $template );
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
		$manuscript_title = ! empty( $data['manuscript_title'] ) ? $data['manuscript_title'] : 'Your article';
		$subject          = 'Published: ' . wp_strip_all_tags( $manuscript_title );
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
	 * Tell an editor that a manuscript was submitted for review.
	 *
	 * @param string|array $to   Editor email(s).
	 * @param array        $data Data fields (submitter_name, manuscript_title, division, edit_url, etc.).
	 * @return bool
	 */
	public static function send_review_request( $to, $data = array() ) {
		$manuscript_title = ! empty( $data['manuscript_title'] ) ? $data['manuscript_title'] : 'New manuscript';
		$subject          = 'Ready for review: ' . wp_strip_all_tags( $manuscript_title );
		return self::send( $to, $subject, 'review-request', $data );
	}

	/**
	 * Send the weekly list of overdue and incomplete manuscripts.
	 *
	 * @param string|array $to   Editor email(s).
	 * @param array        $data Data fields (issue_name, overdue, incomplete, dashboard_url, etc.).
	 * @return bool
	 */
	public static function send_queue_digest( $to, $data = array() ) {
		$subject = ! empty( $data['issue_name'] ) ? wp_strip_all_tags( $data['issue_name'] ) . ': manuscripts that need attention' : 'Manuscripts that need attention';
		return self::send( $to, $subject, 'queue-digest', $data );
	}

	/**
	 * Tell the editors-in-chief that every manuscript in an issue is ready.
	 *
	 * @param string|array $to   Editor email(s).
	 * @param array        $data Data fields (issue_name, total, divisions, toc_url, issue_url, etc.).
	 * @return bool
	 */
	public static function send_issue_ready( $to, $data = array() ) {
		$issue_name = ! empty( $data['issue_name'] ) ? wp_strip_all_tags( $data['issue_name'] ) : 'Issue';
		$subject    = $issue_name . ' is ready to publish';
		return self::send( $to, $subject, 'issue-ready', $data );
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

		$html_message = self::render(
			'account-access',
			array(
				'user_name'        => $user_name,
				'user_login'       => $user_login,
				'user_role'        => self::role_label( $user_data->roles ),
				'reset_url'        => $reset_url,
				'is_new_user'      => false,
				'preheader'        => 'Reset your WesSciJo password',
				'email_title'      => 'Password Reset | The Wesleyan Science Journal',
				'recipient_email'  => $user_data->user_email,
			)
		);

		$defaults['message'] = $html_message;
		$defaults['subject'] = 'Password Reset | The Wesleyan Science Journal';
		$defaults['headers'] = array( 'Content-Type: text/html; charset=UTF-8', self::reply_to( 'account-access' ) );

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

		$html_message = self::render(
			'account-access',
			array(
				'user_name'        => $user->display_name ? $user->display_name : $user->user_login,
				'user_login'       => $user->user_login,
				'user_role'        => self::role_label( $user->roles ),
				'reset_url'        => $reset_url,
				'is_new_user'      => true,
				'preheader'        => 'Set a password for your WesSciJo account',
				'email_title'      => 'Welcome to WesSciJo',
				'recipient_email'  => $user->user_email,
			)
		);

		$notification['message'] = $html_message;
		$notification['subject'] = 'Welcome to WesSciJo';
		$notification['headers'] = array( 'Content-Type: text/html; charset=UTF-8', self::reply_to( 'account-access' ) );

		return $notification;
	}

	/**
	 * Name a user's WordPress role as the journal does. Editors are the
	 * editors-in-chief; contributors are division leads.
	 *
	 * @param array $roles WordPress role slugs.
	 * @return string
	 */
	private static function role_label( $roles ) {
		$labels = array(
			'administrator' => 'Administrator',
			'editor'        => 'Editor-in-Chief',
			'author'        => 'Author',
			'contributor'   => 'Division Lead',
		);
		foreach ( $labels as $role => $label ) {
			if ( in_array( $role, (array) $roles, true ) ) {
				return $label;
			}
		}
		return 'Editorial board member';
	}
}
