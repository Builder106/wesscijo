<?php
/**
 * Submit page (applied automatically to the page with the slug "submit").
 *
 * Intro copy comes from the page's own editor content. The article content
 * guidelines default to the approved PDF in assets/; that link and the
 * submission form can be changed in Appearance -> Customize -> Submissions
 * without editing the page.
 */
get_header();

$wessci_form_url       = get_theme_mod( 'wessci_submission_form_url', '' );
$wessci_form_embed     = $wessci_form_url ? wessci_submission_form_embed_url( $wessci_form_url ) : '';
$wessci_guidelines_url = wessci_submission_guidelines_url();
?>

<main class="site-main" id="main" tabindex="-1">

	<article class="article">
		<h1 class="article__title"><?php the_title(); ?></h1>

		<?php
		while ( have_posts() ) :
			the_post();
			if ( get_the_content() ) :
				?>
				<div class="prose"><?php the_content(); ?></div>
				<?php
			endif;
		endwhile;
		?>

		<?php if ( $wessci_guidelines_url || ( $wessci_form_url && ! $wessci_form_embed ) ) : ?>
			<div class="submit-actions">
				<?php if ( $wessci_guidelines_url ) : ?>
					<a class="btn" href="<?php echo esc_url( $wessci_guidelines_url ); ?>" target="_blank" rel="noopener noreferrer">Article content guidelines</a>
				<?php endif; ?>
				<?php if ( $wessci_form_url && ! $wessci_form_embed ) : ?>
					<a class="btn btn--invert" href="<?php echo esc_url( $wessci_form_url ); ?>" target="_blank" rel="noopener noreferrer">Open the submission form</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</article>

	<?php if ( $wessci_form_embed ) : ?>
		<section class="division">
			<h2 class="division__title">Submission form</h2>
			<iframe class="submit-form" src="<?php echo esc_url( $wessci_form_embed ); ?>" title="Wesleyan Science Journal submission form" loading="lazy"></iframe>
			<p class="submit-form__fallback">
				Trouble with the form? <a href="<?php echo esc_url( $wessci_form_url ); ?>" target="_blank" rel="noopener noreferrer">Open it in a new tab</a>.
			</p>
		</section>
	<?php endif; ?>

</main>

<?php get_footer(); ?>
