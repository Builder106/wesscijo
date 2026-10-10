<?php
/**
 * Template: Editorial Decision / Manuscript Status Update
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $author_name (string)
 * - $manuscript_title (string)
 * - $manuscript_id (string)
 * - $division (string)
 * - $decision_label (string, e.g., "Revisions Requested", "Accepted for Publication", "Under Review")
 * - $decision_note (string)
 * - $editor_comments (string, optional)
 * - $action_deadline (string, optional)
 * - $action_url (string)
 * - $action_button_text (string)
 * - $editor_name (string, optional)
 * - $editor_title (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$decision_label = ! empty( $decision_label ) ? $decision_label : 'Editorial decision';
$action_button_text = ! empty( $action_button_text ) ? $action_button_text : 'View manuscript';
$action_url = ! empty( $action_url ) ? $action_url : admin_url( 'admin.php?page=wessci-manuscript' );
?>
<h1 style="margin: 0 0 24px; font-family: Georgia, 'Times New Roman', serif; font-size: 27px; line-height: 34px; font-weight: normal;"><?php echo esc_html( $decision_label ); ?></h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $author_name ) ? $author_name : 'Author' ); ?>,</p>
<p style="margin: 0 0 18px;">We have reviewed your manuscript, <em><?php echo esc_html( ! empty( $manuscript_title ) ? $manuscript_title : 'Untitled manuscript' ); ?></em>.</p>
<?php if ( ! empty( $decision_note ) ) : ?>
<p style="margin: 0 0 18px;"><?php echo nl2br( esc_html( $decision_note ) ); ?></p>
<?php endif; ?>
<?php if ( ! empty( $action_deadline ) ) : ?>
<p style="margin: 0 0 18px;"><strong>Due date: <?php echo esc_html( $action_deadline ); ?></strong></p>
<?php endif; ?>
<?php if ( ! empty( $editor_comments ) ) : ?>
<h2 style="margin: 26px 0 8px; font-size: 17px; line-height: 27px;">Editor's comments</h2>
<p style="margin: 0 0 18px;"><?php echo nl2br( esc_html( $editor_comments ) ); ?></p>
<?php endif; ?>
<p style="margin: 8px 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $action_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;"><?php echo esc_html( $action_button_text ); ?></a></p>
<p style="margin: 0 0 24px;">If you have questions or need more time, contact your division editor.</p>
<p style="margin: 0 0 24px;">Sincerely,<br><?php echo esc_html( ! empty( $editor_name ) ? $editor_name : 'The Editorial Board' ); ?><br><?php echo esc_html( ! empty( $editor_title ) ? $editor_title : 'The Wesleyan Science Journal' ); ?></p>
<?php if ( ! empty( $manuscript_id ) || ! empty( $division ) ) : ?>
<p class="wessci-text-muted" style="margin: 0; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #625b5f;">
<?php if ( ! empty( $manuscript_id ) ) : ?>Manuscript <?php echo esc_html( $manuscript_id ); ?><br><?php endif; ?>
<?php if ( ! empty( $division ) ) : ?><?php echo esc_html( $division ); ?><?php endif; ?>
</p>
<?php endif; ?>
