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
		<a class="hero__title" href="<?php echo esc_url( home_url( '/' ) ); ?>">Wesleyan Science Journal</a>
	</div>

	<nav class="hero__index" aria-label="Sections">
		<ul class="hero__nav-list">
			<?php foreach ( wessci_divisions() as $div ) : ?>
				<li class="hero__nav-item hero__nav-item--has-panel">
					<a class="hero__nav-link" href="<?php echo esc_url( get_term_link( $div['term'] ) ); ?>">
						<?php echo wessci_term_name( $div['term'] ); ?>
					</a>
					<div class="panel">
						<ul class="panel__list">
							<?php foreach ( $div['children'] as $child ) : ?>
								<li>
									<a class="panel__link" href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo wessci_term_name( $child ); ?></a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</li>
			<?php endforeach; ?>

			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
			<li class="hero__nav-item"><a class="hero__nav-link" href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a></li>
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
