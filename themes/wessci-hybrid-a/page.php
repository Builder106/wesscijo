<?php get_header(); ?>

<main class="site-main" id="main">

	<?php
	while ( have_posts() ) :
		the_post();
		?>

		<article class="article">
			<h1 class="article__title"><?php the_title(); ?></h1>

			<div class="prose">
				<?php the_content(); ?>
			</div>
		</article>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>
