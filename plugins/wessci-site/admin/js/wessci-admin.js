/**
 * WesSciJo Admin Interactive Scripts
 * Handles Division Queue tab switching and interactive dashboard filtering.
 */

document.addEventListener('DOMContentLoaded', function () {
	// Division Queue Tab Switching
	var tabButtons = document.querySelectorAll('.wessci-tab-btn');
	var queueRows = document.querySelectorAll('.wessci-queue-row');

	if (tabButtons.length > 0) {
		tabButtons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var targetDivision = btn.getAttribute('data-tab');

				// Update active state on buttons
				tabButtons.forEach(function (b) {
					b.classList.remove('is-active');
					b.setAttribute('aria-selected', 'false');
				});
				btn.classList.add('is-active');
				btn.setAttribute('aria-selected', 'true');

				// Filter rows
				queueRows.forEach(function (row) {
					var rowDivision = row.getAttribute('data-division');
					if (targetDivision === 'all' || rowDivision === targetDivision) {
						row.style.display = '';
					} else {
						row.style.display = 'none';
					}
				});
			});
		});
	}
});
