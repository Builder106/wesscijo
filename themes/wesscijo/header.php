<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="color-scheme" content="light dark">
	<script>
	(function() {
		try {
			var t = localStorage.getItem('theme');
			if (t === 'dark' || t === 'light') {
				document.documentElement.setAttribute('data-theme', t);
				var m = document.querySelector('meta[name="color-scheme"]');
				if (m) m.content = t;
			}
		} catch(e) {}
	})();
	</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">Skip to content</a>

<header class="hero">
	<div class="hero__band">
		<?php
		$custom_logo_id      = (int) get_theme_mod( 'custom_logo' );
		$custom_logo         = $custom_logo_id && wp_attachment_is_image( $custom_logo_id ) ? wp_get_attachment_image( $custom_logo_id, 'full', false, array( 'alt' => '' ) ) : '';
		$logo_position       = get_theme_mod( 'wessci_logo_position', 'left' );
		$brand_class         = 'hero__brand';
		$brand_title         = wesscijo_brand_title();
		$brand_class        .= 'right' === $logo_position ? ' hero__brand--logo-right' : '';
		?>
		<div class="<?php echo esc_attr( $brand_class ); ?>">
			<?php if ( $custom_logo ) : ?>
				<span class="hero__logo-slot">
					<?php echo $custom_logo; ?>
				</span>
			<?php endif; ?>
			<a class="hero__title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $brand_title ); ?></a>
		</div>
	</div>

	<nav class="hero__index" aria-label="Sections">
		<ul class="hero__nav-list">
			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<?php foreach ( wessci_divisions() as $div ) : ?>
				<li class="hero__nav-item hero__nav-item--has-panel">
					<details class="panel" name="nav-panel">
						<summary class="panel__summary hero__nav-link">
						<?php echo wessci_term_name( $div['term'] ); ?>
						</summary>
						<ul class="panel__list">
							<li>
								<a class="panel__link panel__link--all" href="<?php echo esc_url( get_term_link( $div['term'] ) ); ?>">All <?php echo wessci_term_name( $div['term'] ); ?></a>
							</li>
							<?php foreach ( $div['children'] as $child ) : ?>
								<li>
									<a class="panel__link" href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo wessci_term_name( $child ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</details>
				</li>
			<?php endforeach; ?>

			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
			<li class="hero__nav-item hero__nav-item--has-panel">
				<details class="panel" name="nav-panel">
					<summary class="panel__summary hero__nav-link">About Us</summary>
					<ul class="panel__list">
						<li><a class="panel__link" href="<?php echo esc_url( home_url( '/about/#letter' ) ); ?>">Letter from the Editorial Board</a></li>
						<li><a class="panel__link" href="<?php echo esc_url( home_url( '/about/#team' ) ); ?>">Our Team</a></li>
					</ul>
				</details>
			</li>
			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a></li>
		</ul>

		<div class="hero__controls">
			<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="u-visually-hidden" for="s">Search the journal</label>
				<input class="search__input" type="search" id="s" name="s" placeholder="Search" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button class="search__submit" type="submit">Go</button>
			</form>

			<button type="button" class="theme-toggle" id="theme-toggle" aria-label="Toggle color theme" title="Toggle theme">
				<svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
				</svg>
				<svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<circle cx="12" cy="12" r="5"></circle>
					<line x1="12" y1="1" x2="12" y2="3"></line>
					<line x1="12" y1="21" x2="12" y2="23"></line>
					<line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
					<line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
					<line x1="1" y1="12" x2="3" y2="12"></line>
					<line x1="21" y1="12" x2="23" y2="12"></line>
					<line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
					<line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
				</svg>
				<span class="u-visually-hidden">Toggle theme</span>
			</button>
		</div>
	</nav>
</header>
