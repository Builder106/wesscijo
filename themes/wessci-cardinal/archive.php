<?php
/**
 * Archive Template (Volume & Divisions)
 *
 * @package WesSciJo_Cardinal
 */

get_header();

$queried       = get_queried_object();
$archive_title = ( $queried instanceof WP_Term ) ? wessci_term_name( $queried ) : 'Archives / Issue';
?>

<main class="site-main" id="main" tabindex="-1">
	<section class="utility-page" data-od-id="archive-page">
		<div class="utility-inner">
			<h1 data-od-id="archive-title"><?php echo esc_html( $archive_title ); ?></h1>

			<div class="archive-volume-header">
				<img class="volume-seal" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/logo-192.png" width="44" height="44" alt="Volume 14 Seal">
				<div>
					<h2>Current Issue: Volume 14</h2>
					<p>Articles published in the current volume and division archives are indexed below.</p>
				</div>
			</div>

			<nav class="filter-nav" aria-label="Archive divisions">
				<a href="<?php echo esc_url( home_url( '/archive/' ) ); ?>" <?php echo ( ! is_category() ) ? 'aria-current="page"' : ''; ?>>All</a>
				<?php
				$div_slugs = array(
					'research-reviews'            => 'Research & Reviews',
					'news-features-perspectives' => 'News, Features & Perspectives',
				);
				foreach ( $div_slugs as $slug => $label ) :
					$cat = get_category_by_slug( $slug );
					if ( $cat ) :
						?>
						<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" <?php echo ( is_category( $cat->term_id ) ) ? 'aria-current="page"' : ''; ?>>
							<?php echo esc_html( $label ); ?>
						</a>
						<?php
					endif;
				endforeach;
				?>
			</nav>

			<?php if ( have_posts() ) : ?>
				<ul class="result-list">
					<?php
					while ( have_posts() ) :
						the_post();
						$post_id   = get_the_ID();
						$type      = wessci_article_type( $post_id );
						$type_name = $type ? wessci_term_name( $type ) : 'Article';
						$read_time = wessci_read_time( $post_id );
						?>
						<li class="result-item">
							<span class="directory-meta">
								<?php echo esc_html( $type_name ); ?> • <?php echo esc_html( $read_time ); ?> min read • <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date( 'F Y' ) ); ?></time>
							</span>
							<h2>
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<p class="result-excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 28 ) ); ?></p>
						</li>
					<?php endwhile; ?>
				</ul>

				<?php the_posts_pagination(); ?>
			<?php else : ?>
				<section class="empty-state">
					<h2>No articles found</h2>
					<p>No published articles in this archive division yet.</p>
				</section>
			<?php endif; ?>

			<nav class="filter-nav" aria-label="Journal pages" style="margin-top: 48px;">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
				<a href="<?php echo esc_url( home_url( '/archive/' ) ); ?>" aria-current="page">Archives / issue</a>
				<a href="<?php echo esc_url( home_url( '/search/' ) ); ?>">Search</a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
				<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a>
				<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a>
			</nav>
		</div>
	</section>
</main>

<?php
get_footer();
