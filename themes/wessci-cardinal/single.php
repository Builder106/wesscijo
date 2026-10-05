<?php
/**
 * Single Article Spread Template
 *
 * @package WesSciJo_Cardinal
 */

get_header();

while ( have_posts() ) :
	the_post();
	$post_id   = get_the_ID();
	$type      = wessci_article_type( $post_id );
	$type_slug = $type ? $type->slug : 'article';
	$type_name = $type ? wessci_term_name( $type ) : 'Article';
	$read_time = wessci_read_time( $post_id );
	$division  = ( $type && $type->parent ) ? get_term( $type->parent, 'category' ) : null;
	if ( is_wp_error( $division ) ) {
		$division = null;
	}
	$div_name    = $division ? wessci_term_name( $division ) : 'Research & Reviews';
	$is_research = ( $type_slug === 'research' || ( $division && $division->slug === 'research-reviews' ) );
	?>

	<div class="reading-progress-track" aria-hidden="true">
		<div id="reading-progress" class="reading-progress-bar"></div>
	</div>

	<main class="site-main" id="main" tabindex="-1">
		<header class="article-cover <?php echo $is_research ? 'research-cover' : ''; ?>" data-od-id="article-cover">
			<nav class="breadcrumb" aria-label="Breadcrumb">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
				<?php if ( $division ) : ?>
					<span class="sep" aria-hidden="true">/</span>
					<a href="<?php echo esc_url( get_term_link( $division ) ); ?>"><?php echo esc_html( $div_name ); ?></a>
				<?php endif; ?>
				<?php if ( $type ) : ?>
					<span class="sep" aria-hidden="true">/</span>
					<span aria-current="page"><?php echo esc_html( $type_name ); ?></span>
				<?php endif; ?>
			</nav>

			<div class="article-publication-imprint">
				<img class="imprint-seal" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/logo-192.png" alt="" width="26" height="26" aria-hidden="true">
				<span>Wesleyan Science Journal • Volume 14</span>
			</div>

			<h1><?php the_title(); ?></h1>
			<p class="article-context">
				<?php echo esc_html( $type_name ); ?> • <?php echo esc_html( $read_time ); ?> min read • <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></time>
			</p>
		</header>

		<div class="reading-surface" data-od-id="reading-surface">
			<div class="reading-grid">
				<nav class="chapter-map" aria-labelledby="contents-title" data-od-id="chapter-map">
					<h2 id="contents-title">Contents</h2>
					<ol id="chapter-list">
						<li><a href="#main"><span aria-hidden="true">01</span>Article</a></li>
					</ol>
				</nav>

				<article class="article-body" data-od-id="article-body">
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="evidence full-evidence article-lead-figure">
							<?php the_post_thumbnail( 'wessci-lead', array( 'loading' => 'eager', 'fetchpriority' => 'high' ) ); ?>
							<?php if ( get_the_post_thumbnail_caption() ) : ?>
								<figcaption><?php echo wp_kses_post( get_the_post_thumbnail_caption() ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<div class="prose-content">
						<?php the_content(); ?>
					</div>

					<section class="reading-section article-end" id="continue-reading">
						<h2>Other branches</h2>
						<nav aria-label="Other stories">
							<?php
							$related = new WP_Query(
								array(
									'post__not_in'   => array( $post_id ),
									'posts_per_page' => 3,
									'orderby'        => 'rand',
								)
							);
							if ( $related->have_posts() ) :
								while ( $related->have_posts() ) :
									$related->the_post();
									?>
									<p>
										<a class="read-link return-link" href="<?php the_permalink(); ?>">
											<span class="btn-text"><?php the_title(); ?></span> <span class="btn-arrow" aria-hidden="true">↗</span>
										</a>
									</p>
									<?php
								endwhile;
								wp_reset_postdata();
							endif;
							?>
							<p>
								<a class="read-link return-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
									<span class="btn-text">Return to the homepage</span> <span class="btn-arrow" aria-hidden="true">↗</span>
								</a>
							</p>
						</nav>
					</section>
				</article>
			</div>
		</div>
	</main>

	<?php
endwhile;

get_footer();
