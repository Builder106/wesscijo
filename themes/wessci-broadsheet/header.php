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
	<div class="masthead__rule masthead__rule--top" aria-hidden="true"></div>

	<a class="masthead__title" href="<?php echo esc_url( home_url( '/' ) ); ?>">
		The Wesleyan Science Journal
	</a>

	<p class="masthead__folio">
		<span>Vol.&nbsp;I</span>
		<span class="masthead__dot" aria-hidden="true"></span>
		<span>No.&nbsp;1</span>
		<span class="masthead__dot" aria-hidden="true"></span>
		<span>October&nbsp;2026</span>
	</p>

	<div class="masthead__rule" aria-hidden="true"></div>

	<div class="utility">
		<nav class="utility__nav" aria-label="Journal">
			<a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a>
			<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
			<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a>
		</nav>

		<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="u-visually-hidden" for="s">Search the journal</label>
			<input class="search__input" type="search" id="s" name="s" placeholder="Search the journal" value="<?php echo esc_attr( get_search_query() ); ?>">
		</form>
	</div>

	<div class="masthead__rule masthead__rule--close" aria-hidden="true"></div>
</header>
