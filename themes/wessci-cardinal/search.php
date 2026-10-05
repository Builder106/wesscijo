<?php
/**
 * Search Results Template
 *
 * @package WesSciJo_Cardinal
 */

get_header();
?>

<main class="site-main" id="main" tabindex="-1">
	<section class="utility-page" data-od-id="search-page">
		<div class="utility-inner">
			<h1 data-od-id="search-title">Search</h1>

			<form class="search-form" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" role="search" data-od-id="search-form">
				<label for="story-query">Article title or keyword</label>
				<input id="story-query" name="s" type="search" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="Search articles, topics, authors..." data-od-id="search-query">
				<button class="primary-button" type="submit" data-od-id="search-submit">
					<span class="btn-text">Search</span> <span class="btn-arrow" aria-hidden="true">↗</span>
				</button>
			</form>

			<?php if ( have_posts() ) : ?>
				<p id="search-status" role="status" aria-live="polite" data-od-id="search-status">
					<?php
					global $wp_query;
					printf(
						esc_html( _n( '%d article found for &ldquo;%s&rdquo;', '%d articles found for &ldquo;%s&rdquo;', (int) $wp_query->found_posts, 'wessci' ) ),
						(int) $wp_query->found_posts,
						esc_html( get_search_query() )
					);
					?>
				</p>

				<ul class="result-list" data-od-id="search-results">
					<?php
					while ( have_posts() ) :
						the_post();
						$post_id   = get_the_ID();
						$type      = wessci_article_type( $post_id );
						$type_name = $type ? wessci_term_name( $type ) : 'Article';
						$read_time = wessci_read_time( $post_id );
						$division  = ( $type && $type->parent ) ? get_term( $type->parent, 'category' ) : null;
						$div_name  = ( $division && ! is_wp_error( $division ) ) ? wessci_term_name( $division ) : '';
						?>
						<li class="result-item">
							<span class="directory-meta">
								<?php echo esc_html( $type_name ); ?>
								<?php if ( $div_name ) : ?> • <?php echo esc_html( $div_name ); ?><?php endif; ?>
								• <?php echo esc_html( $read_time ); ?> min read
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
				<section class="empty-state" data-od-id="search-empty">
					<h2>No matching articles</h2>
					<p>Try a shorter search keyword or choose a category below to explore the archive.</p>
					<a class="read-link" href="<?php echo esc_url( home_url( '/archive/' ) ); ?>" data-od-id="reset-search">
						<span class="btn-text">Browse full archive</span> <span class="btn-arrow" aria-hidden="true">↗</span>
					</a>
				</section>
			<?php endif; ?>

			<nav class="filter-nav" aria-label="Journal pages" style="margin-top: 48px;">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
				<a href="<?php echo esc_url( home_url( '/archive/' ) ); ?>">Archives / issue</a>
				<a href="<?php echo esc_url( home_url( '/search/' ) ); ?>" aria-current="page">Search</a>
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
				<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a>
				<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a>
			</nav>
		</div>
	</section>
</main>

<?php
get_footer();
