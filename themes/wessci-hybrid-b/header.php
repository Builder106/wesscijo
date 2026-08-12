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
		<p class="hero__descriptor">A hybrid journal and science magazine for the Wesleyan community</p>
	</div>

	<nav class="hero__index" aria-label="Sections">
		<div class="hero__index-inner">
			<?php foreach ( wessci_divisions() as $div ) : ?>
				<div class="index-group">
					<a class="index-group__title" href="<?php echo esc_url( get_term_link( $div['term'] ) ); ?>">
						<?php echo wessci_term_name( $div['term'] ); ?>
					</a>
					<ul class="index-group__list">
						<?php foreach ( $div['children'] as $child ) : ?>
							<li>
								<a href="<?php echo esc_url( get_term_link( $child ) ); ?>">
									<?php echo wessci_term_name( $child ); ?>
									<span class="index-group__count"><?php echo esc_html( $child->count ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>

			<div class="index-group">
				<span class="index-group__title">Journal</span>
				<ul class="index-group__list">
					<li><a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a></li>
					<li><a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a></li>
				</ul>
			</div>

			<form class="search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="u-visually-hidden" for="s">Search the journal</label>
				<input class="search__input" type="search" id="s" name="s" placeholder="Search" value="<?php echo esc_attr( get_search_query() ); ?>">
				<button class="search__submit" type="submit">Go</button>
			</form>
		</div>
	</nav>
</header>
