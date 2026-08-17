<?php
/**
 * Template Name: Calendar
 *
 * Assign this to a "Calendar" page in wp-admin. Lists wessci_event posts
 * (registered in functions.php) if any exist, otherwise shows an empty
 * state — client asked for a place this can live, not the feature itself.
 */
get_header();
?>

<main class="site-main" id="main">

	<article class="article">
		<h1 class="article__title"><?php the_title(); ?></h1>

		<?php if ( have_posts() ) : ?>
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
		<?php endif; ?>
	</article>

	<?php
	$wessci_events = new WP_Query(
		array(
			'post_type'      => 'wessci_event',
			'posts_per_page' => -1,
			'orderby'        => 'date',
			'order'          => 'ASC',
		)
	);
	?>

	<section class="division">
		<?php if ( $wessci_events->have_posts() ) : ?>
			<div class="cards">
				<?php
				while ( $wessci_events->have_posts() ) :
					$wessci_events->the_post();
					?>
					<article class="card">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
							</a>
						<?php endif; ?>
						<h3 class="card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		<?php else : ?>
			<p class="empty">No upcoming events yet — check back soon.</p>
		<?php endif; ?>
	</section>

</main>

<?php get_footer(); ?>
