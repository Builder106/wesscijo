<?php get_header(); ?>

<main class="site-main" id="main">

	<div class="issuebar">
		<span class="issuebar__slab">Current issue</span>
		<span class="issuebar__meta">Vol. 1 — No. 1 — October 2026</span>
		<a class="issuebar__link" href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">All issues</a>
	</div>

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
						<a class="slab" href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo wessci_term_name( $type ); ?></a>
					<?php endif; ?>

					<h1 class="lead__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h1>

					<p class="lead__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>

					<p class="meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
						<span><?php echo esc_html( wessci_read_time( $lead_id ) ); ?> min read</span>
					</p>

					<a class="btn" href="<?php the_permalink(); ?>">Read the article</a>
				</div>

				<?php if ( has_post_thumbnail() ) : ?>
					<figure class="lead__figure">
						<?php the_post_thumbnail( 'wessci-lead', array( 'alt' => '' ) ); ?>
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
		$i = 0;
		?>
		<section class="division">
			<h2 class="division__title"><?php echo wessci_term_name( $div['term'] ); ?></h2>

			<div class="cards">
				<?php
				while ( $q->have_posts() ) :
					$q->the_post();
					$i++;
					$type = wessci_article_type( get_the_ID() );
					?>
					<article class="card">
						<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '' ) ); ?>
							<?php endif; ?>
						</a>
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
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endforeach; ?>

	<section class="submit">
		<h2 class="submit__title">Write for us</h2>
		<p class="submit__copy">The inaugural issue is written by our editorial staff. Submissions open to the wider Wesleyan community in a future issue.</p>
		<a class="btn btn--invert" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submission guidelines</a>
	</section>

</main>

<?php get_footer(); ?>
