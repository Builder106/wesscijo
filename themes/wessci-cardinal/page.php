<?php
/**
 * Default Page Template
 *
 * @package WesSciJo_Cardinal
 */

get_header();
?>

<main class="site-main" id="main" tabindex="-1">
	<section class="utility-page">
		<div class="utility-inner">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<h1><?php the_title(); ?></h1>
				<div class="prose">
					<?php the_content(); ?>
				</div>
				<?php
			endwhile;
			?>
		</div>
	</section>
</main>

<?php
get_footer();
