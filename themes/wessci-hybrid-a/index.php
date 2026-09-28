<?php get_header(); ?>

<main class="site-main" id="main" tabindex="-1">

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
			$lead_id   = get_the_ID();
			$type      = wessci_article_type( $lead_id );
			$has_cover = has_post_thumbnail();
			?>
			<article class="lead<?php echo $has_cover ? '' : ' lead--no-figure'; ?>">
				<div class="lead__text">
					<?php $lead_field = wessci_article_field( $lead_id ); ?>
					<?php if ( $type || $lead_field ) : ?>
						<p class="tags">
							<?php if ( $type ) : ?>
								<a class="tag" href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo wessci_term_name( $type ); ?></a>
							<?php endif; ?>
							<?php if ( $lead_field ) : ?>
								<a class="tag tag--field" href="<?php echo esc_url( get_term_link( $lead_field ) ); ?>"><?php echo wessci_term_name( $lead_field ); ?></a>
							<?php endif; ?>
						</p>
					<?php endif; ?>

					<h1 class="lead__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h1>

					<?php wessci_the_byline( $lead_id ); ?>

					<p class="lead__excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>

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

	<?php $letter = wessci_editors_letter(); ?>
	<?php if ( $letter ) : ?>
		<section class="letter">
			<p class="letter__kicker">From the Editors-in-Chief</p>
			<h2 class="letter__title">
				<a href="<?php echo esc_url( get_permalink( $letter ) ); ?>"><?php echo esc_html( get_the_title( $letter ) ); ?></a>
			</h2>
			<p class="letter__excerpt"><?php echo esc_html( has_excerpt( $letter ) ? get_the_excerpt( $letter ) : wp_trim_words( wp_strip_all_tags( $letter->post_content ), 60 ) ); ?></p>
			<a class="btn" href="<?php echo esc_url( get_permalink( $letter ) ); ?>">Read the letter</a>
		</section>
	<?php endif; ?>

	<?php foreach ( wessci_divisions() as $div ) : ?>
		<?php
		$q = new WP_Query(
			array(
				'cat'            => $div['term']->term_id,
				'posts_per_page' => 12,
				'post__not_in'   => array( $lead_id ),
			)
		);
		if ( ! $q->have_posts() ) {
			continue;
		}

		// Group the division by field, in print order; unfiled articles go last, without a heading.
		$groups = array();
		foreach ( wessci_fields() as $field ) {
			$groups[ $field->term_id ] = array( 'field' => $field, 'posts' => array() );
		}
		$groups['none'] = array( 'field' => null, 'posts' => array() );
		foreach ( $q->posts as $division_post ) {
			$field = wessci_article_field( $division_post->ID );
			$key   = $field && isset( $groups[ $field->term_id ] ) ? $field->term_id : 'none';
			$groups[ $key ]['posts'][] = $division_post;
		}
		$groups       = array_filter( $groups, function ( $group ) {
			return ! empty( $group['posts'] );
		} );
		$has_headings = count( $groups ) > 1 || ! isset( $groups['none'] );
		$card_heading = $has_headings ? 'h4' : 'h3';
		?>
		<section class="division">
			<h2 class="division__title"><?php echo wessci_term_name( $div['term'] ); ?></h2>

			<?php foreach ( $groups as $group ) : ?>
				<div class="field-group">
					<?php if ( $group['field'] ) : ?>
						<h3 class="field-group__title">
							<a href="<?php echo esc_url( get_term_link( $group['field'] ) ); ?>"><?php echo wessci_term_name( $group['field'] ); ?></a>
						</h3>
					<?php elseif ( $has_headings ) : ?>
						<h3 class="field-group__title">More from <?php echo wessci_term_name( $div['term'] ); ?></h3>
					<?php endif; ?>

					<div class="cards">
						<?php
						foreach ( $group['posts'] as $index => $post ) :
							setup_postdata( $post );
							$type = wessci_article_type( get_the_ID() );
							?>
							<article class="card<?php echo 0 === $index ? ' card--wide' : ''; ?>">
								<?php if ( has_post_thumbnail() ) : ?>
									<a class="card__media" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
										<?php the_post_thumbnail( 'wessci-card', array( 'alt' => '', 'loading' => 'lazy', 'sizes' => '(max-width: 720px) 100vw, (max-width: 1080px) 50vw, 25vw' ) ); ?>
									</a>
								<?php endif; ?>
								<?php if ( $type ) : ?>
									<span class="card__type"><?php echo wessci_term_name( $type ); ?></span>
								<?php endif; ?>
								<<?php echo $card_heading; ?> class="card__title">
									<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
								</<?php echo $card_heading; ?>>
								<?php $credits = wessci_article_credits( get_the_ID() ); ?>
								<?php if ( isset( $credits['writers'] ) ) : ?>
									<p class="card__byline">By <?php echo esc_html( $credits['writers']['names'] ); ?></p>
								<?php endif; ?>
								<p class="card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 24 ) ); ?></p>
							</article>
							<?php
						endforeach;
						wp_reset_postdata();
						?>
					</div>
				</div>
			<?php endforeach; ?>
		</section>
	<?php endforeach; ?>

	<section class="submit">
		<h2 class="submit__title">Write for us</h2>
		<?php if ( get_theme_mod( 'wessci_submission_form_url' ) ) : ?>
			<p class="submit__copy">We welcome submissions from the wider Wesleyan community. Read our article content guidelines, then send us your work.</p>
			<a class="btn btn--invert" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit your work</a>
		<?php else : ?>
			<p class="submit__copy">The inaugural issue is written by our editorial staff. Submissions open to the wider Wesleyan community in a future issue.</p>
			<a class="btn btn--invert" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submission guidelines</a>
		<?php endif; ?>
	</section>

</main>

<?php get_footer(); ?>
