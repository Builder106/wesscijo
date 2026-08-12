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
	<div class="bar bar--top">
		<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span class="wordmark__mark" aria-hidden="true"></span>
			<span class="wordmark__text">The Wesleyan Science Journal</span>
		</a>

		<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="u-visually-hidden" for="s">Search the journal</label>
			<input class="search__input" type="search" id="s" name="s" placeholder="SEARCH" value="<?php echo esc_attr( get_search_query() ); ?>">
			<button class="search__submit" type="submit">Go</button>
		</form>
	</div>

	<nav class="bar bar--index" aria-label="Sections">
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
