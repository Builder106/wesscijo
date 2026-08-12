<?php get_header(); ?>

<main class="site-main" id="main">

	<article class="article">
		<h1 class="article__title">Page not found</h1>

		<div class="prose">
			<p>The page you&#8217;re looking for doesn&#8217;t exist. It may have been moved or removed.</p>
		</div>

		<a class="btn" href="<?php echo esc_url( home_url( '/' ) ); ?>">Back to the homepage</a>
	</article>

</main>

<?php get_footer(); ?>
