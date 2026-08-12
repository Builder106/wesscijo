<?php get_header(); ?>

<main class="site-main" id="main">

	<?php
	while ( have_posts() ) :
		the_post();
		$post_id  = get_the_ID();
		$type     = wessci_article_type( $post_id );
		$division = $type && $type->parent ? get_term( $type->parent, 'category' ) : null;
		?>

		<article class="article">
			<?php if ( $type ) : ?>
				<a class="tag" href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo wessci_term_name( $type ); ?></a>
			<?php endif; ?>

			<h1 class="article__title"><?php the_title(); ?></h1>

			<p class="meta">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
				<span class="meta__sep" aria-hidden="true"></span>
				<span><?php echo esc_html( wessci_read_time( $post_id ) ); ?> min read</span>
			</p>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="article__figure">
					<?php the_post_thumbnail( 'wessci-lead', array( 'alt' => '' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="prose">
				<?php the_content(); ?>
			</div>
		</article>

		<?php if ( $division ) : ?>
			<?php
			$related_q = new WP_Query(
				array(
					'cat'            => $division->term_id,
					'posts_per_page' => 3,
					'post__not_in'   => array( $post_id ),
				)
			);
			if ( $related_q->have_posts() ) :
				?>
				<section class="division">
					<h2 class="division__title">More from <?php echo wessci_term_name( $division ); ?></h2>
					<div class="cards">
						<?php
						while ( $related_q->have_posts() ) :
							$related_q->the_post();
							$related_type = wessci_article_type( get_the_ID() );
							?>
							<article class="card">
								<?php if ( has_post_thumbnail() ) : ?>
									<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
										<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
									</a>
								<?php endif; ?>
								<?php if ( $related_type ) : ?>
									<span class="card__type"><?php echo wessci_term_name( $related_type ); ?></span>
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
				</section>
				<?php
			endif;
			?>
		<?php endif; ?>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
