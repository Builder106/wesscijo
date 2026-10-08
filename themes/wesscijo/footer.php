<footer class="colophon">
	<div class="colophon__main">
		<div class="colophon__brand">
			<p class="colophon__name"><?php echo esc_html( wesscijo_brand_title() ); ?></p>
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
			<div class="index-group">
				<span class="index-group__title">Journal</span>
				<ul class="index-group__list">
					<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About Us</a></li>
					<li><a href="<?php echo esc_url( home_url( '/archives/' ) ); ?>">Archives</a></li>
					<li><a href="<?php echo esc_url( home_url( '/submit/' ) ); ?>">Submit</a></li>
				</ul>
			</div>
		</nav>
	</div>

	<div class="colophon__legal">
		<p>Views expressed belong solely to individual authors and do not necessarily reflect the positions of Wesleyan University.</p>
	</div>
</footer>

<script>
(function() {
	var panels = document.querySelectorAll('.panel');
	panels.forEach(function(panel) {
		panel.addEventListener('toggle', function() {
			if (panel.open) {
				panels.forEach(function(other) {
					if (other !== panel && other.open) other.open = false;
				});
			}
		});
	});
	document.addEventListener('click', function(e) {
		if (!e.target.closest('.panel')) {
			panels.forEach(function(panel) { panel.open = false; });
		}
	});
	document.addEventListener('keydown', function(e) {
		if (e.key === 'Escape') {
			var openPanel = document.querySelector('.panel[open]');
			if (openPanel) {
				openPanel.open = false;
				var sum = openPanel.querySelector('summary');
				if (sum) sum.focus();
			}
		}
	});

	var toggle = document.getElementById('theme-toggle');
	if (toggle) {
		function getCurrentTheme() {
			var stored = localStorage.getItem('theme');
			if (stored === 'dark' || stored === 'light') return stored;
			return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
		}
		function updateLabel() {
			var current = getCurrentTheme();
			var next = current === 'dark' ? 'light' : 'dark';
			toggle.setAttribute('aria-label', 'Switch to ' + next + ' theme');
			toggle.setAttribute('title', 'Switch to ' + next + ' theme');
		}
		function applyTheme(targetTheme) {
			var systemDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
			var systemTheme = systemDark ? 'dark' : 'light';

			if (targetTheme === systemTheme) {
				localStorage.removeItem('theme');
				document.documentElement.removeAttribute('data-theme');
				var meta = document.querySelector('meta[name="color-scheme"]');
				if (meta) meta.content = 'light dark';
			} else {
				localStorage.setItem('theme', targetTheme);
				document.documentElement.setAttribute('data-theme', targetTheme);
				var meta = document.querySelector('meta[name="color-scheme"]');
				if (meta) meta.content = targetTheme;
			}
			updateLabel();
		}
		toggle.addEventListener('click', function() {
			var current = getCurrentTheme();
			var next = current === 'dark' ? 'light' : 'dark';
			var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

			if (!document.startViewTransition || prefersReducedMotion) {
				applyTheme(next);
				return;
			}

			document.startViewTransition(function() {
				applyTheme(next);
			});
		});
		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
			updateLabel();
		});
		window.addEventListener('storage', function(e) {
			if (e.key === 'theme') {
				if (e.newValue === 'dark' || e.newValue === 'light') {
					document.documentElement.setAttribute('data-theme', e.newValue);
				} else {
					document.documentElement.removeAttribute('data-theme');
				}
				updateLabel();
			}
		});
		updateLabel();
	}
})();
</script>

<?php wp_footer(); ?>
</body>
</html>
