<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">Skip to content</a>

<header class="hero">
	<div class="hero__band">
		<?php
		$logo_position = get_theme_mod( 'wessci_logo_position', 'left' );
		$brand_class   = 'hero__brand';
		$brand_title   = wessci_hybrid_a_brand_title();
		$brand_class  .= 'right' === $logo_position ? ' hero__brand--logo-right' : '';
		?>
		<div class="<?php echo esc_attr( $brand_class ); ?>">
			<span class="hero__logo-slot">
				<?php echo wessci_hybrid_a_logo(); ?>
			</span>
			<a class="hero__title" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo esc_html( $brand_title ); ?></a>
		</div>
	</div>

	<nav class="hero__index" aria-label="Sections">
		<ul class="hero__nav-list">
			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<?php foreach ( wessci_divisions() as $div ) : ?>
				<li class="hero__nav-item hero__nav-item--has-panel">
					<details class="panel">
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

			<?php $fields = wessci_fields(); ?>
			<?php if ( $fields ) : ?>
				<li class="hero__nav-item hero__nav-item--has-panel">
					<details class="panel">
						<summary class="panel__summary hero__nav-link">Fields</summary>
						<ul class="panel__list">
							<?php foreach ( $fields as $field ) : ?>
								<li>
									<a class="panel__link" href="<?php echo esc_url( get_term_link( $field ) ); ?>"><?php echo wessci_term_name( $field ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</details>
				</li>
			<?php endif; ?>

			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
			<?php if ( wessci_hybrid_a_calendar_enabled() ) : ?>
				<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a></li>
			<?php endif; ?>
			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a></li>
		</ul>

		<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="u-visually-hidden" for="s">Search the journal</label>
			<input class="search__input" type="search" id="s" name="s" placeholder="Search" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button class="search__submit" type="submit">Go</button>
		</form>
	</nav>
</header>
