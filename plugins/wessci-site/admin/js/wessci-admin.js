document.addEventListener('DOMContentLoaded', function () {
	const filters = document.querySelectorAll('[data-filter]');
	const rows = document.querySelectorAll('.wessci-queue-row');
	const count = document.querySelector('[data-queue-count]');
	const empty = document.querySelector('[data-queue-empty]');
	function applyFilters() {
		let visible = 0;
		rows.forEach(function (row) {
			row.hidden = Array.from(filters).some(function (filter) {
				if (filter.dataset.filter === 'search') {
					return !row.textContent.toLowerCase().includes(filter.value.trim().toLowerCase());
				}
				if (filter.value === 'active') return row.dataset.stage === 'publish';
				return filter.value !== 'all' && row.dataset[filter.dataset.filter] !== filter.value;
			});
			if (!row.hidden) visible += 1;
		});
		if (count) count.textContent = visible + ' manuscripts shown';
		if (empty) empty.hidden = visible !== 0;
	}
	filters.forEach(function (filter) { filter.addEventListener('input', applyFilters); });
	applyFilters();

	const welcomePanel = document.querySelector('#welcome-panel');
	if (welcomePanel) {
		const actionLinks = welcomePanel.querySelectorAll('.welcome-panel-column a, .welcome-panel-content a');
		if (actionLinks.length >= 3) {
			actionLinks[0].href = 'post-new.php';
			actionLinks[1].href = 'admin.php?page=wessci-masthead';
			actionLinks[2].href = 'edit-tags.php?taxonomy=wessci_issue';
		}
	}
});
