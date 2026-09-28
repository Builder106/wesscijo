<footer class="colophon">
	<div class="colophon__main">
		<div class="colophon__brand">
			<?php echo wessci_hybrid_a_logo( 'colophon__logo' ); ?>
			<p class="colophon__name"><?php echo esc_html( wessci_hybrid_a_brand_title() ); ?></p>
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
			<?php if ( wessci_fields() ) : ?>
				<div class="index-group">
					<span class="index-group__title">Fields</span>
					<ul class="index-group__list">
						<?php foreach ( wessci_fields() as $field ) : ?>
							<li><a href="<?php echo esc_url( get_term_link( $field ) ); ?>"><?php echo wessci_term_name( $field ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
			<div class="index-group">
				<span class="index-group__title">Journal</span>
				<ul class="index-group__list">
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
					<li><a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
					<?php if ( wessci_hybrid_a_calendar_enabled() ) : ?>
						<li><a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a></li>
					<?php endif; ?>
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
