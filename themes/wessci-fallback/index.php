<?php get_header(); ?>

<main class="site-main" id="main" tabindex="-1">

	<?php
	$lead_args = array(
		'posts_per_page'      => 1,
		'ignore_sticky_posts' => true,
	);
	$sticky    = get_option( 'sticky_posts' );
	if ( $sticky ) {
		$lead_args['post__in'] = $sticky;
	}
	$lead_q  = new WP_Query( $lead_args );
	$lead_id = 0;

	if ( $lead_q->have_posts() ) :
		while ( $lead_q->have_posts() ) :
			$lead_q->the_post();
			$lead_id   = get_the_ID();
			$type      = wessci_article_type( $lead_id );
			$has_cover = has_post_thumbnail();
			?>
			<article class="lead<?php echo $has_cover ? '' : ' lead--no-figure'; ?>">
				<div class="lead__text">
					<?php if ( $type ) : ?>
						<a class="tag" href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo wessci_term_name( $type ); ?></a>
					<?php endif; ?>

					<h1 class="lead__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h1>

					<p class="lead__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
					<?php if ( wesscijo_byline( $lead_id ) ) : ?>
						<p class="byline"><?php echo esc_html( wesscijo_byline( $lead_id ) ); ?></p>
					<?php endif; ?>

					<p class="meta">
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
						<span class="meta__sep" aria-hidden="true"></span>
						<span><?php echo esc_html( wessci_read_time( $lead_id ) ); ?> min read</span>
					</p>

					<a class="btn" href="<?php the_permalink(); ?>">Read the article</a>
				</div>

				<?php if ( $has_cover ) : ?>
					<figure class="lead__figure">
						<?php the_post_thumbnail( 'wessci-lead', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'sizes' => '(max-width: 1080px) 100vw, 50vw' ) ); ?>
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
				'posts_per_page' => 4,
				'post__not_in'   => array( $lead_id ),
			)
		);
		if ( ! $q->have_posts() ) {
			continue;
		}
		$index = 0;
		?>
		<section class="division">
			<h2 class="division__title">
				<?php
				$div_slug = $div['term']->slug;
				$div_icon = ( strpos( $div_slug, 'news' ) !== false || strpos( $div_slug, 'perspectives' ) !== false )
					? get_theme_file_uri( '/assets/division-perspectives.svg' )
					: get_theme_file_uri( '/assets/division-life-science.svg' );
				?>
				<img src="<?php echo esc_url( $div_icon ); ?>" class="division__icon" alt="" width="56" height="56" aria-hidden="true">
				<?php echo wessci_term_name( $div['term'] ); ?>
			</h2>

			<div class="cards">
				<?php
				while ( $q->have_posts() ) :
					$q->the_post();
					$index++;
					$type = wessci_article_type( get_the_ID() );
					?>
					<article class="card<?php echo 1 === $index ? ' card--wide' : ''; ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<figure class="card__figure">
								<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
									<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 720px) 100vw, (max-width: 1080px) 50vw, 25vw' ) ); ?>
								</a>
							</figure>
						<?php endif; ?>
						<?php if ( $type ) : ?>
							<span class="card__type"><?php echo wessci_term_name( $type ); ?></span>
						<?php endif; ?>
						<h3 class="card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h3>
						<p class="card__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
					</article>
					<?php
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</section>
	<?php endforeach; ?>

	<section class="submit">
		<div class="submit__header">
			<img src="<?php echo esc_url( get_theme_file_uri( '/assets/submission-packet.svg' ) ); ?>" class="submit__icon" alt="" width="44" height="44" aria-hidden="true">
			<h2 class="submit__title">Write for us</h2>
		</div>
		<a class="btn btn--invert" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submission guidelines</a>
	</section>

</main>

<?php get_footer(); ?>
