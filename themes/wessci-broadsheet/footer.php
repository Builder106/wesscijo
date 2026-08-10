<footer class="colophon">
	<div class="colophon__rule" aria-hidden="true"></div>

	<div class="colophon__inner">
		<p class="colophon__name">The Wesleyan Science Journal</p>
		<p class="colophon__tag"><?php bloginfo( 'description' ); ?></p>

		<nav class="colophon__nav" aria-label="Footer">
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
			<a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a>
			<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a>
		</nav>
	</div>

	<div class="colophon__legal">
		<p>Views expressed belong solely to individual authors and do not necessarily reflect the positions of Wesleyan University.</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
