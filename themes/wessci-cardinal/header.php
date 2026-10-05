<!doctype html>
<html <?php language_attributes(); ?> data-theme="dark">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#100e0f">
	<link rel="icon" type="image/png" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/favicon.png' ); ?>">
	<link rel="apple-touch-icon" href="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo-192.png' ); ?>">
	<script>
		(function() {
			try {
				var saved = localStorage.getItem('wesscijo-theme') || 'system';
				var theme = saved;
				if (saved === 'system') {
					theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
				}
				document.documentElement.setAttribute('data-theme', theme);
				document.documentElement.setAttribute('data-theme-setting', saved);
				var meta = document.querySelector('meta[name="theme-color"]');
				if (meta) {
					meta.setAttribute('content', theme === 'light' ? '#ffffff' : '#100e0f');
				}
			} catch (e) {}
		})();
	</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="skip-link" href="#main">Skip to content</a>

<header class="site-header" data-od-id="site-header">
	<a class="wordmark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="WesSciJo home" data-od-id="home-link">
		<img class="header-logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo-192.png' ); ?>" alt="" width="42" height="42" aria-hidden="true">
		<span class="wordmark-text">WES<span>SCI</span>JO<span class="wordmark-period" aria-hidden="true">.</span></span>
	</a>
	<nav class="primary-nav" aria-label="Primary" data-od-id="primary-navigation">
		<a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives / issue</a>
		<a href="<?php echo esc_url( home_url( '/category/research-reviews/' ) ); ?>">Research &amp; Reviews</a>
		<a href="<?php echo esc_url( home_url( '/category/news-features-perspectives/' ) ); ?>">News / Features / Perspectives</a>
	</nav>
	<div class="header-actions">
		<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>" class="utility" data-od-id="search-link"><span class="btn-text">Search</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
		<button class="utility theme-toggle" id="theme-toggle" aria-label="Theme: System (matches device). Click to switch to Light theme." title="Theme: System (matches device). Click to switch to Light theme." data-od-id="theme-toggle"><span class="theme-icon" aria-hidden="true"><svg class="icon-system" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span></button>
		<button class="utility" id="menu-toggle" aria-expanded="false" aria-controls="site-menu" data-od-id="menu-toggle"><span class="btn-text">Menu</span> <span class="btn-arrow" aria-hidden="true">+</span></button>
	</div>
</header>

<div class="issue-bar" data-od-id="issue-identity-bar">
	<div class="issue-bar-inner">
		<div class="issue-bar-status">
			<span class="issue-badge">Current Issue</span>
			<span class="issue-volume">Volume 14 — Inaugural Edition (October 2026)</span>
		</div>
		<a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>" class="issue-bar-link" data-od-id="issue-identity-link"><span class="btn-text">All Volumes &amp; Issues</span> <span class="btn-arrow" aria-hidden="true">↗</span></a>
	</div>
</div>

<nav class="site-menu" id="site-menu" aria-label="All pages" hidden data-od-id="expanded-menu">
	<div class="menu-brand">
		<img class="menu-logo" src="<?php echo esc_url( get_stylesheet_directory_uri() . '/assets/logo-192.png' ); ?>" alt="Wesleyan Science Journal Seal" width="46" height="46">
		<div>
			<span class="menu-title">Wesleyan Science Journal</span>
			<span class="menu-sub">Undergraduate Research &amp; Scholarly Publishing</span>
		</div>
	</div>
	<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a>
	<a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives / issue</a>
	<a href="<?php echo esc_url( home_url( '/category/research-reviews/' ) ); ?>">Research &amp; Reviews</a>
	<a href="<?php echo esc_url( home_url( '/category/news-features-perspectives/' ) ); ?>">News / Features / Perspectives</a>
	<a href="<?php echo esc_url( home_url( '/?s=' ) ); ?>">Search</a>
	<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
	<a href="<?php echo esc_url( home_url( '/calendar/' ) ); ?>">Calendar</a>
	<a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a>
</nav>
