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
		if (!toggle.querySelector('.theme-toggle__icon--system')) {
			var systemSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
			systemSvg.setAttribute('class', 'theme-toggle__icon theme-toggle__icon--system');
			systemSvg.setAttribute('viewBox', '0 0 24 24');
			systemSvg.setAttribute('fill', 'none');
			systemSvg.setAttribute('stroke', 'currentColor');
			systemSvg.setAttribute('stroke-width', '2');
			systemSvg.setAttribute('stroke-linecap', 'round');
			systemSvg.setAttribute('stroke-linejoin', 'round');
			systemSvg.setAttribute('aria-hidden', 'true');
			systemSvg.innerHTML = '<rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line>';
			toggle.insertBefore(systemSvg, toggle.firstChild);
		}

		function getStoredPreference() {
			var stored = localStorage.getItem('theme');
			if (stored === 'light' || stored === 'dark') return stored;
			return 'system';
		}

		function getNextTheme(currentPref) {
			if (currentPref === 'system') return 'light';
			if (currentPref === 'light') return 'dark';
			return 'system';
		}

		function updateLabel() {
			var pref = getStoredPreference();
			var next = getNextTheme(pref);
			var label = next === 'system' ? 'Switch to system theme' : 'Switch to ' + next + ' theme';
			toggle.setAttribute('aria-label', label);
			toggle.setAttribute('title', label);
		}

		function applyTheme(targetPref) {
			var meta = document.querySelector('meta[name="color-scheme"]');
			if (targetPref === 'system') {
				localStorage.removeItem('theme');
				document.documentElement.removeAttribute('data-theme');
				if (meta) meta.content = 'light dark';
			} else {
				localStorage.setItem('theme', targetPref);
				document.documentElement.setAttribute('data-theme', targetPref);
				if (meta) meta.content = targetPref;
			}
			updateLabel();
		}

		toggle.addEventListener('click', function() {
			var currentPref = getStoredPreference();
			var nextPref = getNextTheme(currentPref);
			var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

			if (!document.startViewTransition || prefersReducedMotion) {
				applyTheme(nextPref);
				return;
			}

			document.startViewTransition(function() {
				applyTheme(nextPref);
			});
		});

		window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function() {
			updateLabel();
		});

		window.addEventListener('storage', function(e) {
			if (e.key === 'theme') {
				if (e.newValue === 'dark' || e.newValue === 'light') {
					document.documentElement.setAttribute('data-theme', e.newValue);
					var meta = document.querySelector('meta[name="color-scheme"]');
					if (meta) meta.content = e.newValue;
				} else {
					document.documentElement.removeAttribute('data-theme');
					var meta = document.querySelector('meta[name="color-scheme"]');
					if (meta) meta.content = 'light dark';
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
