<?php
/**
 * Template: Article Publication & Volume Release Announcement
 *
 * @package WesSciJo_Site
 *
 * Expected variables:
 * - $author_name (string)
 * - $manuscript_title (string)
 * - $volume_number (string)
 * - $issue_title (string)
 * - $division (string)
 * - $article_url (string)
 * - $doi (string, optional)
 * - $citation_text (string, optional)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$article_url = ! empty( $article_url ) ? $article_url : home_url();
?>
<h1 style="margin: 0 0 24px; font-size: 27px; line-height: 34px; font-weight: normal;">Your article is published</h1>
<p style="margin: 0 0 18px;">Dear <?php echo esc_html( ! empty( $author_name ) ? $author_name : 'Author' ); ?>,</p>
<p style="margin: 0 0 18px;"><em><?php echo esc_html( ! empty( $manuscript_title ) ? $manuscript_title : 'Your article' ); ?></em> is now published in The Wesleyan Science Journal. Thank you for contributing to the journal.</p>
<?php if ( ! empty( $volume_number ) || ! empty( $issue_title ) || ! empty( $division ) ) : ?>
<p class="wessci-text-muted" style="margin: 0 0 18px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #625b5f;">
<?php if ( ! empty( $volume_number ) ) : ?>Volume <?php echo esc_html( $volume_number ); ?><br><?php endif; ?>
<?php if ( ! empty( $issue_title ) ) : ?><?php echo esc_html( $issue_title ); ?><br><?php endif; ?>
<?php if ( ! empty( $division ) ) : ?><?php echo esc_html( $division ); ?><?php endif; ?>
</p>
<?php endif; ?>
<p style="margin: 8px 0 18px;"><a class="wessci-link" href="<?php echo esc_url( $article_url ); ?>" style="display: inline-block; padding: 8px 0; font-family: Arial, Helvetica, sans-serif; font-size: 15px; line-height: 28px; color: #a80f29; text-decoration: underline;">Read your article</a></p>
<?php if ( ! empty( $citation_text ) ) : ?>
<h2 style="margin: 26px 0 8px; font-size: 17px; line-height: 27px;">Citation</h2>
<p style="margin: 0 0 18px;"><?php echo esc_html( $citation_text ); ?></p>
<?php endif; ?>
<?php if ( ! empty( $doi ) ) : ?>
<p class="wessci-text-muted" style="margin: 0 0 18px; font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 20px; color: #625b5f;">DOI: <?php echo esc_html( $doi ); ?></p>
<?php endif; ?>
<p style="margin: 0 0 24px;">You are welcome to share the article with colleagues and friends.</p>
<p style="margin: 0;">Congratulations,<br>The Editors-in-Chief</p>
