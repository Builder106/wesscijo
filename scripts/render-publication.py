import html
import json
from pathlib import Path
import re
import shutil


ROOT = Path(__file__).resolve().parents[1]
CONTENT = ROOT / 'content/issue-2026'
PUBLIC = ROOT / 'public'
NAME = 'The Wesleyan Science Journal'
FORM = 'https://docs.google.com/forms/d/e/1FAIpQLSf3YVPrAoa6FFr3QyJYtGbWm5aDzRgoVsL6EjiwLnagwW63cA/viewform'
SECTIONS = {
    'life-science': 'Life Science',
    'physical-science': 'Physical Science',
    'quantitative-computational-science': 'Quantitative & Computational Science',
    'science-technology-society': 'Science, Technology & Society',
}
GROUPS = {
    'research-reviews': 'Research & Reviews',
    'news-features-perspectives': 'News, Features & Perspectives',
}


def escape(value):
    return html.escape(str(value), quote=True)


def article_url(article):
    return '/articles/' + article['slug'] + '/'


def section_slug(name):
    normalized = name.replace(' and ', ' & ').lower()
    for slug, label in SECTIONS.items():
        if normalized == label.lower():
            return slug
    raise ValueError('Unknown section: ' + name)


def type_slug(name):
    return re.sub(r'[^a-z0-9]+', '-', name.lower()).strip('-')


def group_slug(article):
    if article['slug'] in {'bile-salt-mixed-micelles', 'gmos-applications-and-controversy', 'math-behind-llms', 'quantum-voting'}:
        return 'research-reviews'
    return 'news-features-perspectives'


def figure(article, classname, eager=False):
    image = article.get('thumbnail')
    if not image:
        return ''
    dimensions = ''
    if image.get('width') and image.get('height'):
        dimensions = f' width="{int(image["width"])}" height="{int(image["height"])}"'
    loading = 'fetchpriority="high"' if eager else 'loading="lazy"'
    is_card = classname == 'card__figure'
    link_class = ' class="card__media"' if is_card else ''
    caption = f'<figcaption>{escape(image["credit"])}</figcaption>' if (classname == 'article__figure' and image.get('credit')) else ''
    return (f'<figure class="{classname}"><a{link_class} href="{article_url(article)}" tabindex="-1" aria-hidden="true">'
            f'<img src="{escape(image["src"])}" alt=""{dimensions} {loading}></a>{caption}</figure>')


def card(article, wide=False, level=3):
    classname = 'card card--wide' if wide else 'card'
    return (f'<article class="{classname}">{figure(article, "card__figure")}'
            f'<h{level} class="card__title"><a href="{article_url(article)}">{escape(article["title"])}</a></h{level}>'
            f'<p class="meta">{escape(article["type"])}</p>'
            f'<p class="card__excerpt">{escape(article["excerpt"])}</p></article>')


def header(articles):
    menus = []
    for slug, label in GROUPS.items():
        types = sorted({a['type'] for a in articles if group_slug(a) == slug} | ({'News'} if slug == 'news-features-perspectives' else set()))
        children = ''.join(f'<li><a class="panel__link" href="/category/{slug}/{type_slug(t)}/">{escape(t)}</a></li>' for t in types)
        menus.append(f'<li class="hero__nav-item"><details class="panel" name="sections">'
                     f'<summary class="panel__summary hero__nav-link">{escape(label)}</summary>'
                     f'<ul class="panel__list"><li><a class="panel__link panel__link--all" href="/category/{slug}/">All {escape(label)}</a></li>{children}</ul></details></li>')
    return ('<a class="skip-link" href="#main">Skip to content</a><header class="hero">'
            '<div class="hero__band"><div class="hero__brand">'
            '<a href="/" class="hero__logo-slot" aria-label="Journal home">'
            '<img src="/assets/logo.png" class="hero__logo hero__logo--light" alt="" width="1024" height="1024">'
            '<img src="/assets/logo-dark.png" class="hero__logo hero__logo--dark" alt="" width="1024" height="1024"></a>'
            f'<a class="hero__title" href="/">{NAME}</a></div></div>'
            '<nav class="hero__index" aria-label="Sections"><ul class="hero__nav-list">'
            '<li><a class="hero__nav-link" href="/">Home</a></li>' + ''.join(menus) +
            '<li><a class="hero__nav-link" href="/archives/">Archives</a></li>'
            '<li><a class="hero__nav-link" href="/about/">About Us</a></li>'
            '<li><a class="hero__nav-link" href="/submit/">Submit</a></li></ul>'
            '<div class="hero__controls"><search><form class="search" action="/search/" method="get">'
            '<label class="u-visually-hidden" for="q">Search the journal</label>'
            '<input class="search__input" id="q" name="q" type="search" placeholder="Search">'
            '<button class="search__submit" type="submit">Go</button></form></search>'
            '<button type="button" class="theme-toggle" id="theme-toggle" aria-label="Toggle color theme" title="Toggle theme">'
            '<svg class="theme-toggle__icon theme-toggle__icon--moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            '<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>'
            '<svg class="theme-toggle__icon theme-toggle__icon--sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
            '<circle cx="12" cy="12" r="5"></circle>'
            '<line x1="12" y1="1" x2="12" y2="3"></line>'
            '<line x1="12" y1="21" x2="12" y2="23"></line>'
            '<line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>'
            '<line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>'
            '<line x1="1" y1="12" x2="3" y2="12"></line>'
            '<line x1="21" y1="12" x2="23" y2="12"></line>'
            '<line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>'
            '<line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>'
            '<span class="u-visually-hidden">Toggle theme</span></button></div></nav></header>')


def document(title, body, articles, description='', canonical=''):
    page_title = NAME if title == NAME else title + ' | ' + NAME
    return ('<!doctype html><html lang="en"><head><meta charset="utf-8">'
            '<meta name="viewport" content="width=device-width, initial-scale=1">'
            '<meta name="color-scheme" content="light dark">'
            '<script>(function(){try{var t=localStorage.getItem("theme");if(t==="dark"||t==="light"){document.documentElement.setAttribute("data-theme",t);var m=document.querySelector(\'meta[name="color-scheme"]\');if(m)m.content=t;}}catch(e){}})();</script>'
            f'<title>{escape(page_title)}</title><meta name="description" content="{escape(description)}">'
            f'<link rel="canonical" href="https://thewesleyansciencejournal.com{canonical}">'
            '<link rel="icon" href="/assets/favicon.png">'
            '<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Libre+Franklin:wght@400;500;600;700;800;900&family=Cormorant+Garamond:wght@600&display=swap">'
            '<link rel="stylesheet" href="/publication.css">'
            '<link rel="stylesheet" href="/publication-extra.css"><script src="/publication.js" defer></script></head><body>'
            + header(articles) + '<main class="site-main" id="main" tabindex="-1">' + body + '</main>'
            f'<footer class="publication-footer"><a href="/">{NAME}</a><nav aria-label="Footer"><a href="/about/">About Us</a><a href="/submit/">Submission guidelines</a></nav></footer></body></html>')


def write_page(route, title, body, articles, description=''):
    path = PUBLIC / route.strip('/') / 'index.html'
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(document(title, body, articles, description, route), encoding='utf-8')


def masthead():
    source = (ROOT / 'themes/wesscijo/template-about.php').read_text()
    groups = re.split(r"'section'\s*=>\s*'", source)[1:]
    result = ''
    for group in groups:
        label = group.split("'", 1)[0]
        people = re.findall(r"array\( 'name' => '([^']+)', 'role' => '([^']+)' \)", group)
        result += f'<section class="division"><h2 class="division__title">{escape(label)}</h2><div class="masthead-grid">'
        for name, role in people:
            initials = ''.join(part[0] for part in name.split()[:2])
            result += f'<div class="person"><div class="person__avatar" aria-hidden="true">{escape(initials)}</div><p class="person__name">{escape(name)}</p><p class="person__role">{escape(role)}</p></div>'
        result += '</div></section>'
    return result


def main():
    articles = json.loads((CONTENT / 'articles.json').read_text())
    slugs = [a['slug'] for a in articles]
    if len(articles) != 9 or len(set(slugs)) != len(slugs):
        raise ValueError('The issue requires nine articles with unique slugs.')
    for a in articles:
        if not re.fullmatch(r'[a-z0-9]+(?:-[a-z0-9]+)*', a['slug']):
            raise ValueError('Invalid article slug.')
        section_slug(a['section'])
        if not a['bodyHtml'] or len(a['bodyHtml']) < 1000:
            raise ValueError('Missing article body: ' + a['slug'])
    lead = next(a for a in articles if a['slug'] == 'birds-of-wesleyan')
    homepage = '<div class="issuebar"><span>Current issue</span><span>Fall 2026</span><a href="/archives/">All issues</a></div>'
    homepage += (f'<article class="lead"><div class="lead__text"><h1 class="lead__title"><a href="{article_url(lead)}">{escape(lead["title"])}</a></h1>'
                 f'<p class="meta">{escape(lead["section"])} / {escape(lead["type"])}</p>'
                 f'<p class="lead__excerpt">{escape(lead["excerpt"])}</p><p class="byline">{escape(lead["byline"])}</p>'
                 f'<a class="btn" href="{article_url(lead)}">Read the article</a></div>{figure(lead, "lead__figure", True)}</article>')
    for slug, label in GROUPS.items():
        selected = [a for a in articles if group_slug(a) == slug and a != lead]
        homepage += f'<section class="division"><h2 class="division__title"><a href="/category/{slug}/">{escape(label)}</a></h2><div class="cards">' + ''.join(card(a, index == 0) for index, a in enumerate(selected)) + '</div></section>'
    homepage += '<section class="submit"><h2 class="submit__title">Write for us</h2><a class="btn btn--invert" href="/submit/">Submission guidelines</a></section>'
    write_page('/', NAME, homepage, articles, 'Read the Fall 2026 edition of The Wesleyan Science Journal.')
    for a in articles:
        related = [other for other in articles if other['section'] == a['section'] and other != a]
        body = ('<div id="reading-progress" class="reading-progress" aria-hidden="true"></div>'
                f'<article class="article"><h1 class="article__title">{escape(a["title"])}</h1>'
                f'<p class="byline">{escape(a["byline"])}</p><p class="meta"><a href="/category/{section_slug(a["section"])}/">{escape(a["section"])}</a> / {escape(a["type"])}</p>'
                + figure(a, 'article__figure', True) + f'<div class="prose">{a["bodyHtml"]}</div></article>')
        if related:
            body += '<section class="division"><h2 class="division__title">More from this section</h2><div class="cards">' + ''.join(card(other) for other in related) + '</div></section>'
        write_page(article_url(a), a['title'], body, articles, a['excerpt'])
    for slug, label in SECTIONS.items():
        selected = [a for a in articles if section_slug(a['section']) == slug]
        write_page(f'/category/{slug}/', label, f'<h1 class="archive-title">{escape(label)}</h1><div class="cards">' + ''.join(card(a, level=2) for a in selected) + '</div>', articles)
        for kind in sorted({a['type'] for a in selected}):
            write_page(f'/category/{slug}/{type_slug(kind)}/', kind, f'<h1 class="archive-title">{escape(label)}: {escape(kind)}</h1><div class="cards">' + ''.join(card(a, level=2) for a in selected if a['type'] == kind) + '</div>', articles)
    for slug, label in GROUPS.items():
        selected = [a for a in articles if group_slug(a) == slug]
        write_page(f'/category/{slug}/', label, f'<h1 class="archive-title">{escape(label)}</h1><div class="cards">' + ''.join(card(a, level=2) for a in selected) + '</div>', articles)
        kinds = sorted({a['type'] for a in selected} | ({'News'} if slug == 'news-features-perspectives' else set()))
        for kind in kinds:
            kind_selected = [a for a in selected if a['type'] == kind]
            content = f'<h1 class="archive-title">{escape(kind)}</h1>'
            if kind_selected:
                content += '<div class="cards">' + ''.join(card(a, level=2) for a in kind_selected) + '</div>'
            else:
                content += '<p class="empty">News articles coming soon.</p>'
            write_page(f'/category/{slug}/{type_slug(kind)}/', kind, content, articles)
    write_page('/archives/', 'Archives', '<h1 class="archive-title">Archives</h1><h2>Fall 2026</h2><div class="cards">' + ''.join(card(a) for a in articles) + '</div>', articles)
    letter = (CONTENT / 'letter.html').read_text()
    about_html = (
        '<article class="article"><h1 class="article__title">About Us</h1>'
        '<div class="about-tabs" role="tablist" aria-label="About navigation">'
        '<a href="#editorial-letter" class="about-tab is-active" id="tab-btn-letter" role="tab" aria-selected="true" aria-controls="editorial-letter">Letter from the Editorial Board</a>'
        '<a href="#masthead" class="about-tab" id="tab-btn-team" role="tab" aria-selected="false" aria-controls="masthead">Our Team</a>'
        '</div>'
        '<div class="about-panel is-active" id="editorial-letter" role="tabpanel" aria-labelledby="tab-btn-letter">'
        '<section class="prose"><h2>Letter from the Editorial Board</h2>' + letter + '</section></div>'
        '<div class="about-panel" id="masthead" role="tabpanel" aria-labelledby="tab-btn-team">' + masthead() + '</div></article>'
        '<script>'
        '(function(){'
        'function setTab(name){'
        'var isTeam=(name==="masthead"||name==="team");'
        'var tabLetter=document.getElementById("tab-btn-letter");'
        'var tabTeam=document.getElementById("tab-btn-team");'
        'var pLetter=document.getElementById("editorial-letter");'
        'var pTeam=document.getElementById("masthead");'
        'if(!tabLetter||!tabTeam||!pLetter||!pTeam)return;'
        'tabLetter.classList.toggle("is-active",!isTeam);'
        'tabLetter.setAttribute("aria-selected",!isTeam?"true":"false");'
        'tabTeam.classList.toggle("is-active",isTeam);'
        'tabTeam.setAttribute("aria-selected",isTeam?"true":"false");'
        'pLetter.classList.toggle("is-active",!isTeam);'
        'pTeam.classList.toggle("is-active",isTeam);'
        '}'
        'window.addEventListener("hashchange",function(){'
        'if(location.hash==="#masthead"||location.hash==="#team")setTab("team");'
        'else if(location.hash==="#editorial-letter"||location.hash==="#letter")setTab("letter");'
        '});'
        'if(location.hash==="#masthead"||location.hash==="#team")setTab("team");'
        'document.addEventListener("click",function(e){'
        'var btn=e.target.closest(".about-tab");'
        'if(btn){e.preventDefault();var target=btn.getAttribute("href").replace("#","");history.replaceState(null,"","#"+target);setTab(target);}'
        '});'
        '})();'
        '</script>'
    )
    write_page('/about/', 'About Us', about_html, articles)
    guidelines = (CONTENT / 'guidelines.html').read_text()
    write_page('/submit/', 'Submission guidelines', '<article class="article"><h1 class="article__title">Submission guidelines</h1><p class="submission-link"><a class="btn" href="' + FORM + '">Open the article submission form</a></p><div class="prose">' + guidelines + '</div></article>', articles)
    write_page('/search/', 'Search', '<h1 class="archive-title" id="search-title">Search the journal</h1><p id="search-status" role="status"></p><div class="cards" id="search-results"></div><noscript><p>Enable JavaScript to search, or <a href="/archives/">browse all articles</a>.</p></noscript>', articles)
    (PUBLIC / '404.html').write_text(document('Page not found', '<h1 class="archive-title">Page not found</h1><p><a href="/">Return to the journal</a></p>', articles))
    shutil.copyfile(ROOT / 'themes/wesscijo/style.css', PUBLIC / 'publication.css')
    index = [{k: a[k] for k in ('slug', 'title', 'byline', 'section', 'type', 'excerpt', 'thumbnail')} | {'text': html.unescape(re.sub('<[^>]+>', ' ', a['bodyHtml']))} for a in articles]
    (PUBLIC / 'search-index.json').write_text(json.dumps(index, ensure_ascii=False), encoding='utf-8')
    print(f'Rendered {len(articles)} articles, their sections, About, submissions, archives, and search.')


if __name__ == '__main__':
    main()
