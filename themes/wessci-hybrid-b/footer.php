<footer class="colophon">
	<div class="colophon__main">
		<div class="colophon__brand">
			<p class="colophon__name">The Wesleyan<br>Science Journal</p>
			<p class="colophon__tag"><?php bloginfo( 'description' ); ?></p>
		</div>

		<nav class="colophon__index" aria-label="Footer">
			<?php foreach ( wessci_divisions() as $div ) : ?>
				<div class="index-group">
					<span class="index-group__title"><?php echo wessci_term_name( $div['term'] ); ?></span>
					<ul class="index-group__list">
						<?php foreach ( $div['children'] as $child ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo wessci_term_name( $child ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
			<div class="index-group">
				<span class="index-group__title">Journal</span>
				<ul class="index-group__list">
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
					<li><a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
					<li><a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a></li>
				</ul>
			</div>
		</nav>
	</div>

	<div class="colophon__legal">
		<p>Views expressed belong solely to individual authors and do not necessarily reflect the positions of Wesleyan University.</p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
