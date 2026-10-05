<?php
/**
 * Template Name: Submit
 *
 * @package WesSciJo_Cardinal
 */

get_header();
?>

<main class="site-main" id="main" tabindex="-1">
	<section class="utility-page" data-od-id="submit-page">
		<div class="utility-inner">
			<h1 data-od-id="submit-title">Submit to WesSciJo</h1>

			<div class="submit-imprint">
				<img class="submit-seal" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/logo-192.png" width="44" height="44" alt="Wesleyan Science Journal Seal">
				<p>We welcome original research papers, literature reviews, scientific essays, and reporting from all Wesleyan undergraduate students.</p>
			</div>

			<h2>Editorial Workflow</h2>
			<ol class="plain-list">
				<li>Prepare your manuscript in standard format (Research Article, Field Feature, or Perspective Essay).</li>
				<li>Provide high-resolution figures and data tables with complete captions, attribution, and methodological details.</li>
				<li>Submit manuscripts and supporting files through the student portal or by contacting the editorial board.</li>
				<li>Submissions undergo peer review by student editors and faculty mentors prior to layout and publication.</li>
			</ol>

			<h2>Figure &amp; Media Guidelines</h2>
			<p>Research articles support full-width data plots and in-line figures adjacent to relevant discussion. All images must be accompanied by comprehensive descriptive captions and appropriate permissions.</p>

			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					if ( get_the_content() ) :
						?>
						<div class="prose"><?php the_content(); ?></div>
						<?php
					endif;
				endwhile;
			endif;
			?>

			<nav class="filter-nav" aria-label="Journal pages" style="margin-top: 48px;">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
				<a href="<?php echo esc_url( home_url( '/archive/' ) ); ?>">Archives / issue</a>
				<a href="<?php echo esc_url( home_url( '/search/' ) ); ?>">Search</a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
				<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a>
				<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>" aria-current="page">Submit</a>
			</nav>
		</div>
	</section>
</main>

<?php
get_footer();
