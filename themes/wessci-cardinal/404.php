<?php
/**
 * 404 Not Found Template
 *
 * @package WesSciJo_Cardinal
 */

get_header();
?>

<main class="site-main" id="main" tabindex="-1">
	<section class="utility-page">
		<div class="utility-inner">
			<h1>Page Not Found</h1>
			<p>The page you requested could not be located. It may have been moved, renamed, or archived.</p>
			<p style="margin-top: 24px;">
				<a class="read-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<span class="btn-text">Return to the homepage</span> <span class="btn-arrow" aria-hidden="true">↗</span>
				</a>
			</p>
		</div>
	</section>
</main>

<?php
get_footer();
