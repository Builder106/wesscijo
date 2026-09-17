<?php get_header(); ?>

<main class="site-main" id="main" tabindex="-1">

	<section class="division division--search">
		<h1 class="division__title">Search results for &ldquo;<?php echo esc_html( get_search_query() ); ?>&rdquo;</h1>

		<?php if ( have_posts() ) : ?>
			<div class="cards">
				<?php
				while ( have_posts() ) :
					the_post();
					$type = wessci_article_type( get_the_ID() );
					?>
					<article class="card">
						<?php if ( has_post_thumbnail() ) : ?>
							<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
								<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
							</a>
						<?php endif; ?>
						<?php if ( $type ) : ?>
							<span class="card__type"><?php echo wessci_term_name( $type ); ?></span>
						<?php endif; ?>
						<h3 class="card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
					</article>
					<?php
				endwhile;
				?>
			</div>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<p class="empty">No articles matched your search.</p>
		<?php endif; ?>
	</section>

</main>

<?php get_footer(); ?>
