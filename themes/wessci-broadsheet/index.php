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
		$lead_q  = new WP_Query(
			array(
				'posts_per_page'      => 1,
				'ignore_sticky_posts' => true,
			)
		);
		$lead_id = 0;

		if ( $lead_q->have_posts() ) :
			while ( $lead_q->have_posts() ) :
				$lead_q->the_post();
				$lead_id = get_the_ID();
				$type    = wessci_article_type( $lead_id );
				?>
				<article class="lead">
					<div class="lead__text">
						<?php if ( $type ) : ?>
							<a class="kicker" href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo wessci_term_name( $type ); ?></a>
						<?php endif; ?>
						<h1 class="lead__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h1>
						<p class="lead__deck"><?php echo esc_html( get_the_excerpt() ); ?></p>
						<p class="byline">
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
							<span class="byline__sep" aria-hidden="true">—</span>
							<span><?php echo esc_html( wessci_read_time( $lead_id ) ); ?> min read</span>
						</p>
						<a class="readon" href="<?php the_permalink(); ?>">Continue reading</a>
					</div>

					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="lead__figure">
							<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
							<figcaption>Cover art commissioned for this article.</figcaption>
						</figure>
					<?php endif; ?>
				</article>
				<?php
			endwhile;
			wp_reset_postdata();
		endif;
		?>

		<?php foreach ( wessci_divisions() as $div ) : ?>
			<?php
			$q = new WP_Query(
				array(
					'cat'            => $div['term']->term_id,
					'posts_per_page' => 3,
					'post__not_in'   => array( $lead_id ),
				)
			);
			if ( ! $q->have_posts() ) {
				continue;
			}
			?>
			<section class="division">
				<h2 class="division__title">
					<span><?php echo wessci_term_name( $div['term'] ); ?></span>
				</h2>

				<div class="columns">
					<?php
					while ( $q->have_posts() ) :
						$q->the_post();
						$type = wessci_article_type( get_the_ID() );
						?>
						<article class="story">
							<?php if ( has_post_thumbnail() ) : ?>
								<a class="story__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
								</a>
							<?php endif; ?>
							<?php if ( $type ) : ?>
								<span class="kicker kicker--static"><?php echo wessci_term_name( $type ); ?></span>
							<?php endif; ?>
							<h3 class="story__title">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h3>
							<p class="story__text"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 26 ) ); ?></p>
							<p class="byline">
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j M Y' ) ); ?></time>
							</p>
						</article>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</section>
		<?php endforeach; ?>

		<section class="notice">
			<h2 class="notice__title">Submitting to the journal</h2>
			<p class="notice__copy">The inaugural issue is written by our editorial staff. Submissions open to the wider Wesleyan community in a future issue.</p>
			<a class="readon" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submission guidelines</a>
		</section>

	</main>
</div>

<?php get_footer(); ?>
