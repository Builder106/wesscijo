<?php
/**
 * Template Name: Calendar
 *
 * @package WesSciJo_Cardinal
 */

get_header();
?>

<main class="site-main" id="main" tabindex="-1">
	<section class="utility-page" data-od-id="calendar-page">
		<div class="utility-inner">
			<h1 data-od-id="calendar-title">Calendar &amp; Deadlines</h1>
			<p class="utility-lead">Important dates for submissions, editorial cycles, and campus research symposia.</p>

			<ul class="plain-list">
				<li><strong>Fall Submission Deadline:</strong> November 15</li>
				<li><strong>Peer Review &amp; Revisions:</strong> December 1 – January 15</li>
				<li><strong>Spring Print Edition:</strong> April 20</li>
				<li><strong>Wesleyan Undergraduate Research Symposium:</strong> May 1</li>
			</ul>

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
				<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>" aria-current="page">Calendar</a>
				<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a>
			</nav>
		</div>
	</section>
</main>

<?php
get_footer();
