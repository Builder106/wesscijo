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
	<div class="hero__top">
		<a class="hero__wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">Wesleyan Science Journal</a>

		<form class="hero__search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="u-visually-hidden" for="s">Search the journal</label>
			<input class="hero__search-input" type="search" id="s" name="s" placeholder="Search" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button class="hero__search-submit" type="submit">Go</button>
		</form>
	</div>

	<nav class="hero__index" aria-label="Sections">
		<?php foreach ( wessci_divisions() as $div ) : ?>
			<div class="index-group">
				<a class="index-group__title" href="<?php echo esc_url( get_term_link( $div['term'] ) ); ?>">
					<?php echo wessci_term_name( $div['term'] ); ?>
				</a>
				<ul class="index-group__list">
					<?php foreach ( $div['children'] as $child ) : ?>
						<li>
							<a href="<?php echo esc_url( get_term_link( $child ) ); ?>"><?php echo wessci_term_name( $child ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endforeach; ?>

		<div class="index-group index-group--end">
			<span class="index-group__title">Journal</span>
			<ul class="index-group__list">
				<li><a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
				<li><a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a></li>
			</ul>
		</div>
	</nav>
</header>
