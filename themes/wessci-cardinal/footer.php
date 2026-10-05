<?php
/**
 * WesSciJo Cardinal Theme Footer
 *
 * @package WesSciJo_Cardinal
 */
?>
<footer class="site-footer" data-od-id="site-footer">
	<div class="footer-brand-wrap">
		<a class="footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="WesSciJo home">
			<img class="footer-logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo-192.png' ); ?>" alt="Wesleyan Science Journal Seal" width="52" height="52" loading="lazy">
			<span class="footer-brand-text">WesSciJo</span>
		</a>
		<p class="footer-seal-name">Wesleyan Science Journal</p>
	</div>

	<p class="footer-slogan">Science has many branches.<br>Reading is where they meet.</p>

	<div class="footer-columns">
		<div class="footer-col">
			<h4 class="footer-col-title">Divisions &amp; Formats</h4>
			<nav aria-label="Divisions and article formats">
				<a href="<?php echo esc_url( home_url( '/category/research-reviews/' ) ); ?>">Journal Articles</a>
				<a href="<?php echo esc_url( home_url( '/category/research-reviews/' ) ); ?>">Literature Reviews</a>
				<a href="<?php echo esc_url( home_url( '/category/news-features-perspectives/' ) ); ?>">Field Features</a>
				<a href="<?php echo esc_url( home_url( '/category/news-features-perspectives/' ) ); ?>">Perspective Essays &amp; Op-Eds</a>
			</nav>
		</div>

		<div class="footer-col">
			<h4 class="footer-col-title">Journal</h4>
			<nav aria-label="Journal governance and pages">
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About WesSciJo</a>
				<a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives &amp; Issues</a>
				<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Editorial Calendar</a>
				<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit a Manuscript</a>
			</nav>
		</div>
	</div>

	<span class="footer-colophon">Wesleyan Science Journal &bull; Published by Wesleyan University Undergraduates</span>
</footer>

<?php wp_footer(); ?>
</body>
</html>
