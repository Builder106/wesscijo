const legacyViews = {
  research: '/articles/bile-salt-mixed-micelles/',
  feature: '/articles/birds-of-wesleyan/',
  nohero: '/articles/math-behind-llms/',
  about: '/about/',
  submit: '/submit/',
  archives: '/archives/',
  search: '/search/',
};

const params = new URLSearchParams(location.search);
const previousView = params.get('view');
if (location.pathname === '/' && previousView) {
  location.replace(legacyViews[previousView] || '/404.html');
}
if (location.pathname === '/' && params.has('s')) {
  location.replace('/search/?q=' + encodeURIComponent(params.get('s') || ''));
}

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  const open = document.querySelector('.panel[open]');
  if (!open) return;
  open.open = false;
  open.querySelector('summary').focus();
});
document.addEventListener('click', (event) => {
  if (event.target.closest('.panel')) return;
  document.querySelectorAll('.panel[open]').forEach(panel => { panel.open = false; });
});

async function showSearch() {
  const results = document.getElementById('search-results');
  if (!results) return;
  const status = document.getElementById('search-status');
  const query = (params.get('q') || '').trim();
  document.getElementById('q').value = query;
  document.getElementById('search-title').textContent = query ? 'Search: ' + query : 'Search the journal';
  if (!query) {
    status.textContent = 'Enter a title, author, or topic in the search bar.';
    return;
  }
  try {
    const response = await fetch('/search-index.json');
    if (!response.ok) throw new Error('Search index unavailable');
    const articles = await response.json();
    const terms = query.toLocaleLowerCase().split(/\s+/u);
    const matches = articles.filter(article => {
      const text = [article.title, article.byline, article.section, article.type, article.text].join(' ').toLocaleLowerCase();
      return terms.every(term => text.includes(term));
    });
    status.textContent = matches.length ? `${matches.length} ${matches.length === 1 ? 'article' : 'articles'} found.` : 'No articles found. Try another title, author, or topic.';
    for (const article of matches) {
      const card = document.createElement('article');
      card.className = 'card';
      if (article.thumbnail) {
        const figure = document.createElement('figure');
        figure.className = 'card__figure';
        const image = document.createElement('img');
        image.src = article.thumbnail.src;
        image.alt = article.thumbnail.alt || '';
        image.loading = 'lazy';
        if (article.thumbnail.width && article.thumbnail.height) {
          image.width = article.thumbnail.width;
          image.height = article.thumbnail.height;
        }
        figure.append(image);
        if (article.thumbnail.credit) {
          const caption = document.createElement('figcaption');
          caption.textContent = article.thumbnail.credit;
          figure.append(caption);
        }
        card.append(figure);
      }
      const heading = document.createElement('h2');
      heading.className = 'card__title';
      const link = document.createElement('a');
      link.href = '/articles/' + article.slug + '/';
      link.textContent = article.title;
      heading.append(link);
      const excerpt = document.createElement('p');
      excerpt.className = 'card__excerpt';
      excerpt.textContent = article.excerpt;
      card.append(heading, excerpt);
      results.append(card);
    }
  } catch {
    status.textContent = 'Search is unavailable. Browse the articles in Archives or try again.';
    const link = document.createElement('a');
    link.href = '/archives/';
    link.textContent = 'Browse all articles';
    results.append(link);
  }
}

showSearch();
