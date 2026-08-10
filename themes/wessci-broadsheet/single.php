<?php get_header(); ?>

<div class="shell">

	<aside class="rail" aria-label="Browse by section">
		<h2 class="rail__heading">Index</h2>
		<?php foreach ( wessci_divisions() as $div ) : ?>
			<div class="rail__group">
				<a class="rail__division" href="<?php echo esc_url( get_term_link( $div['term'] ) ); ?>">
					<?php echo wessci_term_name( $div['term'] ); ?>
				</a>
				<ul class="rail__list">
					<?php foreach ( $div['children'] as $child ) : ?>
						<li>
							<a href="<?php echo esc_url( get_term_link( $child ) ); ?>">
								<?php echo wessci_term_name( $child ); ?>
								<span class="rail__count"><?php echo esc_html( $child->count ); ?></span>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>
	</aside>

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
					<a class="kicker" href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo wessci_term_name( $type ); ?></a>
				<?php endif; ?>

				<h1 class="article__title"><?php the_title(); ?></h1>

				<p class="byline">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
					<span class="byline__sep" aria-hidden="true">—</span>
					<span><?php echo esc_html( wessci_read_time( $post_id ) ); ?> min read</span>
				</p>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="article__figure">
						<?php the_post_thumbnail( 'wessci-lead', array( 'alt' => '' ) ); ?>
						<figcaption>Cover art commissioned for this article.</figcaption>
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
						<h2 class="division__title">
							<span>More from <?php echo wessci_term_name( $division ); ?></span>
						</h2>
						<div class="columns">
							<?php
							while ( $related_q->have_posts() ) :
								$related_q->the_post();
								$related_type = wessci_article_type( get_the_ID() );
								?>
								<article class="story">
									<?php if ( has_post_thumbnail() ) : ?>
										<a class="story__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
											<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
										</a>
									<?php endif; ?>
									<?php if ( $related_type ) : ?>
										<span class="kicker kicker--static"><?php echo wessci_term_name( $related_type ); ?></span>
									<?php endif; ?>
									<h3 class="story__title">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h3>
									<p class="story__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
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
</div>

<?php get_footer(); ?>
