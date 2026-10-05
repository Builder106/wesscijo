<?php
/**
 * The main template file / fallback archive
 *
 * @package WesSciJo_Cardinal
 */

get_header();
?>

<main id="main" tabindex="-1" class="utility-page" data-od-id="main-content">
	<div class="utility-inner">
		<h1>The Wesleyan Science Journal</h1>
		<p>Published undergraduate research, field features, and interdisciplinary scientific perspectives.</p>

		<?php if ( have_posts() ) : ?>
			<ul class="result-list">
				<?php
				while ( have_posts() ) :
					the_post();
					$type = wessci_article_type( get_the_ID() );
					?>
					<li class="result-item" data-od-id="post-<?php the_ID(); ?>">
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="result-meta">
							<?php if ( $type ) : ?>
								<span class="meta-tag"><?php echo wessci_term_name( $type ); ?></span>
								<span class="meta-sep" aria-hidden="true">&bull;</span>
							<?php endif; ?>
							<span class="meta-date"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></span>
							<span class="meta-sep" aria-hidden="true">&bull;</span>
							<span class="meta-time"><?php echo esc_html( wessci_read_time( get_the_ID() ) ); ?> min read</span>
						</div>
						<p class="result-excerpt"><?php echo esc_html( get_the_excerpt() ); ?></p>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php the_posts_pagination(); ?>
		<?php else : ?>
			<section class="empty-state">
				<h2>No articles found</h2>
				<p>Browse our sections or try a keyword search.</p>
				<a class="read-link" href="<?php echo esc_url( home_url( '/?s=' ) ); ?>"><span class="btn-text">Search the journal</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
			</section>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>
