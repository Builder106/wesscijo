const assetRoot = './assets/';
const stories = [
  { id: 'flight', title: 'What a wing can teach us about the world', type: 'Research & Reviews', field: 'Physics', division: 'research', image: 'flight.svg', alt: 'A diagram of a bird with curved lines tracing its flight.', deck: 'Following the questions at the intersection of biology, physics, and the everyday miracle of flight.', body: '<p>A bird crosses the sky, and a familiar sight becomes a question: how does a living wing turn motion into lift?</p><p>To study flight is to look across disciplines. The shape of a feather, the movement of a joint, and the flow of air all belong to the same story. A useful scientific model begins by deciding which of these details it needs to capture.</p><h2>Looking closely at motion</h2><p>This sample research layout gives an abstract, figures, and the main argument distinct places on the page. The illustration below demonstrates how a figure can sit inside the article without interrupting its reading order.</p><figure><img src="' + assetRoot + 'flight.svg" width="600" height="400" alt="Stylized bird and curved flight paths."><figcaption>Figure 1. Concept illustration of flight. The lines are illustrative, not measured aerodynamic data. Artwork created for this website preview.</figcaption></figure><h2>A question worth following</h2><p>Research articles can continue here with methods, results, and discussion. The final text, data, author credits, and bibliography will come from the editorial team.</p><h2>References</h2><p>This is a layout sample, not a published research article. No scientific findings or citations are being attributed to a researcher.</p>' },
  { id: 'cells', title: 'Small worlds. Endless possibilities.', type: 'News, Features & Perspectives', field: 'Life sciences', division: 'features', image: 'cells.svg', alt: 'Red and pink cellular forms on a black field.', deck: 'An invitation to look closer, from the scale of a campus laboratory to the worlds within a single cell.', body: '<p>Some stories start with a wide view. Others begin by leaning toward a microscope and learning to notice what was invisible a moment before.</p><h2>Making room for discovery</h2><p>A science feature can bring readers into a laboratory, introduce a question, or follow the people behind an experiment. This sample page shows the space available for that kind of storytelling.</p><p>Its opening artwork is an original illustration for this preview. It is not a microscopy image or a representation of experimental results.</p><h2>Science in conversation</h2><p>The journal connects research with readers across disciplines. Approved interviews, reported details, and image credits can appear here when the story is ready for publication.</p>' },
  { id: 'questions', title: 'Who gets to ask the next big question?', type: 'Perspective', field: 'Science & society', division: 'features', deck: 'On curiosity, belonging, and the value of bringing more voices into scientific conversations.', body: '<p>Before there is an experiment, there is a question. Who has the time, support, and confidence to ask it matters.</p><p>This image-free perspective layout puts the argument first. A generous reading column and a clear type hierarchy give essays a place in the journal without requiring a cover image.</p><h2>The space to be curious</h2><p>Science writing can consider the institutions and communities around research as carefully as it considers research itself. A perspective invites readers to follow an argument and examine its assumptions.</p><p>This is demonstration copy for the website. An approved essay, its author, and any supporting references will replace it before publication.</p>' }
];
const main = document.querySelector('main');
const home = main.innerHTML;
const menu = document.querySelector('.journal-menu');
const smallScreen = window.matchMedia('(max-width: 980px)');

function escapeText(value) {
  const element = document.createElement('span');
  element.textContent = value;
  return element.innerHTML;
}

function card(story) {
  return `<article class="story${story.image ? '' : ' story--text'}">${story.image ? `<a class="story-image" href="#article/${story.id}" tabindex="-1" aria-hidden="true"><img src="${assetRoot}${story.image}" alt="" width="600" height="400" loading="lazy"></a>` : ''}<a class="story-type" href="#${story.division}">${story.field} / ${story.type === 'Perspective' ? 'Perspective' : story.division === 'research' ? 'Research' : 'Feature'}</a><h3><a href="#article/${story.id}">${story.title}</a></h3><p>${story.deck}</p><div class="story-meta"><span>Sample article</span><a href="#article/${story.id}" aria-label="Read ${story.title}">Read story ↗</a></div></article>`;
}

function listing(title, entries, filters = false) {
  main.innerHTML = `<section class="division"><h1 class="division__title">${escapeText(title)}</h1>${filters ? '<div class="filter-bar" role="group" aria-label="Filter articles"><button type="button" data-filter="all" aria-pressed="true">All stories</button><button type="button" data-filter="research" aria-pressed="false">Research & Reviews</button><button type="button" data-filter="features" aria-pressed="false">News, Features & Perspectives</button></div>' : ''}<div class="story-grid">${entries.map(card).join('')}</div>${entries.length ? '' : '<p class="empty">No stories found. Try “flight”, “cells”, or “curiosity”.</p><a class="round-link" href="#archive">Browse all stories <span aria-hidden="true">↗</span></a>'}</section>`;
  for (const button of main.querySelectorAll('[data-filter]')) {
    button.addEventListener('click', () => {
      for (const sibling of main.querySelectorAll('[data-filter]')) {
        sibling.setAttribute('aria-pressed', String(sibling === button));
      }
      const filtered = stories.filter(story => button.dataset.filter === 'all' || story.division === button.dataset.filter);
      main.querySelector('.story-grid').innerHTML = filtered.map(card).join('');
    });
  }
}

function info(title, body) {
  main.innerHTML = `<article class="article"><h1 class="article__title">${title}</h1><div class="prose">${body}</div></article>`;
}

function render(focus = false) {
  const route = location.hash.slice(1) || 'home';
  if (route === 'home' || route === 'latest') {
    main.innerHTML = home;
    document.querySelector('#home-stories').innerHTML = stories.map(card).join('');
  } else if (route === 'archive') {
    listing('The archive', stories, true);
  } else if (route === 'research' || route === 'features') {
    listing(route === 'research' ? 'Research & Reviews' : 'News, Features & Perspectives', stories.filter(story => story.division === route));
  } else if (route.startsWith('search/')) {
    let query;
    try { query = decodeURIComponent(route.slice(7)); } catch { query = route.slice(7); }
    document.querySelector('#journal-query').value = query;
    listing(`Search: ${query}`, stories.filter(story => `${story.title} ${story.deck} ${story.field} ${story.type}`.toLowerCase().includes(query.toLowerCase())));
  } else if (route.startsWith('article/')) {
    const story = stories.find(entry => entry.id === route.slice(8));
    if (story) {
      main.innerHTML = `<article class="article"><a class="tag" href="#${story.division}">${story.field} / ${story.type}</a><h1 class="article__title">${story.title}</h1><p class="lead__excerpt">${story.deck}</p><p class="meta" style="margin-top:24px">Sample article for design review</p>${story.image ? `<figure class="article__figure"><img src="${assetRoot}${story.image}" alt="${story.alt}" width="600" height="400"><figcaption class="wp-caption-text">Original concept illustration for the website preview.</figcaption></figure>` : ''}<div class="prose">${story.body}</div><a class="round-link" href="#archive">More from the journal <span aria-hidden="true">↗</span></a></article>`;
    } else {
      info('Story not found', '<p>This story is not in the preview. <a href="#archive">Browse the archive.</a></p>');
    }
  } else if (route === 'about') {
    info('Curiosity connects us.', '<p>The Wesleyan Science Journal is a home for research and science writing for science and nonscience readers alike.</p><h2>More than one way to see science</h2><p>Research & Reviews makes space for sustained scientific inquiry. News, Features & Perspectives explores discoveries, people, and the place of science in everyday life.</p><h2>Made together</h2><p>Writers, researchers, editors, illustrators, and designers all contribute to the journal. This preview leaves the team roster to the existing WordPress About page, where the editors can maintain names and roles.</p><p><a href="#submit">Learn about contributing</a></p>');
  } else if (route === 'calendar') {
    info('In good company.', '<p>Journal meetings, conversations, and events will appear here once their details are confirmed.</p><h2>No confirmed events in this preview</h2><p>There are no sample dates or event registrations. <a href="#about">Get to know the journal</a> while the calendar takes shape.</p>');
  } else if (route === 'submit') {
    info('Bring your curiosity.', '<p>A question you cannot stop thinking about. An experiment worth explaining. An illustration that makes something click.</p><h2>There is room for your perspective</h2><p>The journal brings together research, reviews, news, features, perspectives, interviews, and essays, with space for original scientific graphics.</p><h2>Submission details are coming</h2><p>The editors are confirming the process and timing for community submissions. This preview does not collect manuscripts or personal information.</p><p><a href="#archive">Explore the journal’s story formats</a></p>');
  } else {
    info('A little off course.', '<p>We could not find that page. <a href="#home">Return to the journal.</a></p>');
  }
  document.title = `${main.querySelector('h1')?.textContent || 'WesSciJo'} — Wesleyan Science Journal`;
  for (const link of document.querySelectorAll('.journal-nav > a')) {
    if (link.hash === `#${route}`) { link.setAttribute('aria-current', 'page'); }
    else { link.removeAttribute('aria-current'); }
  }
  if (focus) {
    if (smallScreen.matches) { menu.open = false; }
    main.focus({ preventScroll: true });
    if (route === 'latest') { document.querySelector('#latest').scrollIntoView(); }
    else { window.scrollTo({ top: 0, behavior: 'instant' }); }
  }
}

document.querySelector('.journal-search').addEventListener('submit', event => {
  event.preventDefault();
  const query = document.querySelector('#journal-query').value.trim();
  if (query) { location.hash = `search/${encodeURIComponent(query)}`; }
});
window.addEventListener('hashchange', () => render(true));
render();
