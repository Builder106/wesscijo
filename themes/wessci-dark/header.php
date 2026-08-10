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

<header class="masthead">
	<div class="masthead__inner">
		<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="wordmark__mark" aria-hidden="true"></span>
			<span class="wordmark__text">Wesleyan Science Journal</span>
		</a>

		<nav class="nav" aria-label="Sections">
			<ul class="nav__list">
				<?php foreach ( wessci_divisions() as $div ) : ?>
					<li class="nav__item nav__item--has-panel">
						<a class="nav__link" href="<?php echo esc_url( get_term_link( $div['term'] ) ); ?>">
							<?php echo wessci_term_name( $div['term'] ); ?>
						</a>
						<div class="panel">
							<ul class="panel__list">
								<?php foreach ( $div['children'] as $child ) : ?>
									<li>
										<a class="panel__link" href="<?php echo esc_url( get_term_link( $child ) ); ?>">
											<?php echo wessci_term_name( $child ); ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</li>
				<?php endforeach; ?>
				<li class="nav__item">
					<a class="nav__link" href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a>
				</li>
				<li class="nav__item">
					<a class="nav__link" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
				</li>
			</ul>
		</nav>

		<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="u-visually-hidden" for="s">Search the journal</label>
			<svg class="search__icon" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
				<circle cx="7" cy="7" r="4.5" fill="none" stroke="currentColor" stroke-width="1.5"/>
				<path d="M10.5 10.5 L14 14" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
			</svg>
			<input class="search__input" type="search" id="s" name="s" placeholder="Search" value="<?php echo esc_attr( get_search_query() ); ?>">
		</form>
	</div>
</header>
