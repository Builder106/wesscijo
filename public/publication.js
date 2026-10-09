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

document.querySelectorAll('.panel').forEach((panel) => {
  panel.addEventListener('toggle', () => {
    if (panel.open) {
      document.querySelectorAll('.panel[open]').forEach((other) => {
        if (other !== panel) other.open = false;
      });
    }
  });
});

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  const open = document.querySelector('.panel[open]');
  if (!open) return;
  open.open = false;
  open.querySelector('summary').focus();
});

document.addEventListener('click', (event) => {
  if (event.target.closest('.panel')) return;
  document.querySelectorAll('.panel[open]').forEach((panel) => { panel.open = false; });
});

function initReadingProgress() {
  const bar = document.getElementById('reading-progress');
  if (!bar) return;
  function update() {
    const total = document.documentElement.scrollHeight - window.innerHeight;
    const progress = total > 0 ? Math.min(1, Math.max(0, window.scrollY / total)) : 0;
    bar.style.transform = 'scaleX(' + progress + ')';
  }
  window.addEventListener('scroll', update, { passive: true });
  window.addEventListener('resize', update);
  update();
}
initReadingProgress();

function initThemeToggle() {
  const toggle = document.getElementById('theme-toggle');
  if (!toggle) return;

  // Dynamically inject the system (display monitor) icon if not already present
  if (!toggle.querySelector('.theme-toggle__icon--system')) {
    const systemSvg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
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
    const stored = localStorage.getItem('theme');
    if (stored === 'light' || stored === 'dark') return stored;
    return 'system';
  }

  function getNextTheme(currentPref) {
    if (currentPref === 'system') return 'light';
    if (currentPref === 'light') return 'dark';
    return 'system';
  }

  function updateLabel() {
    const pref = getStoredPreference();
    const next = getNextTheme(pref);
    const label = next === 'system' ? 'Switch to system theme' : 'Switch to ' + next + ' theme';
    toggle.setAttribute('aria-label', label);
    toggle.setAttribute('title', label);
  }

  function applyTheme(targetPref) {
    const meta = document.querySelector('meta[name="color-scheme"]');
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

  toggle.addEventListener('click', () => {
    const currentPref = getStoredPreference();
    const nextPref = getNextTheme(currentPref);
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!document.startViewTransition || prefersReducedMotion) {
      applyTheme(nextPref);
      return;
    }

    document.startViewTransition(() => {
      applyTheme(nextPref);
    });
  });

  window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
    updateLabel();
  });

  window.addEventListener('storage', (e) => {
    if (e.key === 'theme') {
      if (e.newValue === 'dark' || e.newValue === 'light') {
        document.documentElement.setAttribute('data-theme', e.newValue);
        const meta = document.querySelector('meta[name="color-scheme"]');
        if (meta) meta.content = e.newValue;
      } else {
        document.documentElement.removeAttribute('data-theme');
        const meta = document.querySelector('meta[name="color-scheme"]');
        if (meta) meta.content = 'light dark';
      }
      updateLabel();
    }
  });

  updateLabel();
}
initThemeToggle();

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
