import html
import hashlib
import json
from pathlib import Path
import re
import shutil


ROOT = Path(__file__).resolve().parents[1]
CONTENT = ROOT / 'content/issue-2026'
PUBLIC = ROOT / 'public'
NAME = 'The Wesleyan Science Journal'
STYLESHEET_VERSION = hashlib.sha256((ROOT / 'themes/wesscijo/style.css').read_bytes()).hexdigest()[:12]
ENTRANCE_VERSION = hashlib.sha256((ROOT / 'themes/wesscijo/assets/logo-entrance.js').read_bytes()).hexdigest()[:12]
FLIGHT_VERSION = hashlib.sha256((ROOT / 'themes/wesscijo/assets/logo-flight.svg').read_bytes()).hexdigest()[:12]
WATCH_VERSION = hashlib.sha256((ROOT / 'themes/wesscijo/assets/cardinal-watch.js').read_bytes()).hexdigest()[:12]
DARK_FLIGHT_VERSION = hashlib.sha256((ROOT / 'themes/wesscijo/assets/logo-flight-dark.svg').read_bytes()).hexdigest()[:12]
CARDINAL_SVG = (ROOT / 'themes/wesscijo/assets/cardinal-perch.svg').read_text(encoding='utf-8').strip()
REFERENCES_HEADING = re.compile(r'<h[2-6][^>]*>\s*(?:<[^>]+>\s*)*(?:works cited|references|sources|bibliography)\b', re.I)
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
DIVISION_ICONS = {
    'research-reviews': '/assets/division-life-science.svg',
    'news-features-perspectives': '/assets/division-perspectives.svg',
    'life-science': '/assets/division-life-science.svg',
    'physical-science': '/assets/division-physical-science.svg',
    'quantitative-computational-science': '/assets/division-quantitative.svg',
    'science-technology-society': '/assets/division-perspectives.svg',
}


def division_icon(slug):
    src = DIVISION_ICONS.get(slug)
    if not src:
        return ''
    return f'<img src="{src}" class="division__icon" alt="" width="56" height="56" aria-hidden="true">'


def escape(value):
    return html.escape(str(value), quote=True)


def with_end_mark(body):
    """End the article text with the cardinal, as the theme's the_content filter does."""
    heading = REFERENCES_HEADING.search(body)
    text_end = heading.start() if heading else len(body)
    for paragraph in reversed(list(re.finditer(r'<p\b[^>]*>(.*?)</p>', body[:text_end], re.I | re.S))):
        if not html.unescape(re.sub('<[^>]+>', '', paragraph.group(1))).strip():
            continue
        inner = re.sub(r'(?:\s|<br\s*/?>|&nbsp;)+$', '', paragraph.group(1), flags=re.I)
        at = paragraph.start(1) + len(inner)
        return body[:at] + f'<span class="end-mark">{CARDINAL_SVG}</span>' + body[at:]
    return body


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
            f'<span class="card__type">{escape(article["type"])}</span>'
            f'<h{level} class="card__title"><a href="{article_url(article)}">{escape(article["title"])}</a></h{level}>'
            f'<p class="card__excerpt">{escape(article["excerpt"])}</p></article>')


def footer():
    return (
        '<footer class="colophon">'
        '<div class="colophon__main">'
        '<div class="colophon__brand">'
        f'<p class="colophon__name">{escape(NAME)}</p>'
        '<p class="colophon__tag">A hybrid journal and science magazine for the Wesleyan community</p>'
        '</div>'
        '<nav class="colophon__index" aria-label="Footer">'
        '<div class="index-group">'
        '<span class="index-group__title">Research &amp; Reviews</span>'
        '<ul class="index-group__list">'
        '<li><a href="/category/research-reviews/journal-articles/">Journal Articles</a></li>'
        '<li><a href="/category/research-reviews/literature-reviews/">Literature Reviews</a></li>'
        '</ul>'
        '</div>'
        '<div class="index-group">'
        '<span class="index-group__title">News, Features &amp; Perspectives</span>'
        '<ul class="index-group__list">'
        '<li><a href="/category/news-features-perspectives/features/">Features</a></li>'
        '<li><a href="/category/news-features-perspectives/news/">News</a></li>'
        '<li><a href="/category/news-features-perspectives/op-ed/">Op/Ed</a></li>'
        '</ul>'
        '</div>'
        '<div class="index-group">'
        '<span class="index-group__title">Journal</span>'
        '<ul class="index-group__list">'
        '<li><a href="/about/">About Us</a></li>'
        '<li><a href="/archives/">Archives</a></li>'
        '<li><a href="/submit/">Submit</a></li>'
        '</ul>'
        '</div>'
        '</nav>'
        '</div>'
        '<div class="colophon__legal">'
        '<p>Views expressed belong solely to individual authors and do not necessarily reflect the positions of Wesleyan University.</p>'
        '</div>'
        '</footer>'
    )


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
    entrance = canonical == '/'
    entrance_bootstrap = (
        '<script>(function(){'
        'if(location.hash||location.search||matchMedia("(prefers-reduced-motion: reduce)").matches||!Element.prototype.animate)return;'
        'document.documentElement.classList.add("logo-entrance");'
        'window.logoEntranceWatchdog=setTimeout(function(){'
        'document.documentElement.classList.remove("logo-entrance","logo-entrance-revealing");},5500);'
        'var theme=document.documentElement.dataset.theme;'
        'var dark=theme==="dark"||(!theme&&matchMedia("(prefers-color-scheme: dark)").matches);'
        'var controller=new AbortController();'
        f'window.logoFlight={{controller:controller,blob:fetch(dark?"/assets/logo-flight-dark.svg?v={DARK_FLIGHT_VERSION}":"/assets/logo-flight.svg?v={FLIGHT_VERSION}",{{signal:controller.signal}})'
        '.then(function(response){if(!response.ok)throw new Error(response.statusText);return response.blob();})};'
        '})();</script>'
    ) if entrance else ''
    entrance_stage = (
        '<div class="logo-entrance__stage" id="logo-entrance" style="display:none">'
        '<div class="logo-entrance__backdrop"></div>'
        '<button class="logo-entrance__skip" type="button">Skip animation</button>'
        '<div class="logo-entrance__mark"><img class="logo-entrance__flight" width="1478" height="988" '
        'alt="The Wesleyan Science Journal cardinal landing on a syringe"></div></div>'
    ) if entrance else ''
    entrance_script = f'<script src="/assets/logo-entrance.js?v={ENTRANCE_VERSION}" defer></script>' if entrance else ''
    stylesheet_url = f'/publication.css?v={STYLESHEET_VERSION}' if entrance else '/publication.css'
    watch_script = f'<script src="/assets/cardinal-watch.js?v={WATCH_VERSION}" defer></script>' if 'class="cardinal"' in body else ''
    return ('<!doctype html><html lang="en"><head><meta charset="utf-8">'
            '<meta name="viewport" content="width=device-width, initial-scale=1">'
            '<meta name="color-scheme" content="light dark">'
            '<script>(function(){try{var t=localStorage.getItem("theme");if(t==="dark"||t==="light"){document.documentElement.setAttribute("data-theme",t);var m=document.querySelector(\'meta[name="color-scheme"]\');if(m)m.content=t;}}catch(e){}})();</script>'
            + entrance_bootstrap +
            f'<title>{escape(page_title)}</title><meta name="description" content="{escape(description)}">'
            f'<link rel="canonical" href="https://thewesleyansciencejournal.com{canonical}">'
            '<link rel="icon" href="/assets/favicon.png">'
            '<link rel="preload" href="/assets/fonts/librefranklin-latin.woff2" as="font" type="font/woff2" crossorigin>'
            '<link rel="preload" href="/assets/fonts/cormorantgaramond-600-latin.woff2" as="font" type="font/woff2" crossorigin>'
            f'<link rel="stylesheet" href="{stylesheet_url}">'
            '<link rel="stylesheet" href="/publication-extra.css"><script src="/publication.js" defer></script>'
            + entrance_script + watch_script + '</head><body>' + entrance_stage
            + header(articles) + '<main class="site-main" id="main" tabindex="-1">' + body + '</main>'
            + footer() + '</body></html>')


def write_page(route, title, body, articles, description=''):
    path = PUBLIC / route.strip('/') / 'index.html'
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(document(title, body, articles, description, route), encoding='utf-8')


def masthead():
    source = (ROOT / 'themes/wesscijo/template-about.php').read_text()
    groups = re.split(r"'section'\s*=>\s*'", source)[1:]
    result = ''
    icon_map = {
        'Editorial Board': '/assets/division-editorial.svg',
        'Life Science': '/assets/division-life-science.svg',
        'Physical Science': '/assets/division-physical-science.svg',
        'Quantitative and Computational Science': '/assets/division-quantitative.svg',
        'Science, Technology and Society': '/assets/division-perspectives.svg',
        'Graphic Design': '/assets/division-graphic-design.svg',
        'Web Design': '/assets/division-web-design.svg',
    }
    for group in groups:
        label = group.split("'", 1)[0]
        icon = f'<img src="{icon_map[label]}" class="division__icon" alt="" width="28" height="28" aria-hidden="true">' if label in icon_map else ''
        people = re.findall(r"array\( 'name' => '([^']+)', 'role' => '([^']+)' \)", group)
        result += f'<section class="division"><h2 class="division__title">{icon}{escape(label)}</h2><div class="masthead-grid">'
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
    order = {
        'research-reviews': [
            'math-behind-llms',
            'quantum-voting',
            'bile-salt-mixed-micelles',
            'gmos-applications-and-controversy',
        ],
        'news-features-perspectives': [
            'scientific-paradigms',
            'chatgpt-moment-for-robotics',
            'liberal-arts-to-scientific-academia',
            'psychology-in-the-age-of-tiktok',
        ],
    }
    homepage += (f'<article class="lead"><div class="lead__text">'
                 f'<a class="tag" href="/category/news-features-perspectives/features/">{escape(lead["type"])}</a>'
                 f'<h1 class="lead__title"><a href="{article_url(lead)}">{escape(lead["title"])}</a></h1>'
                 f'<p class="lead__excerpt">{escape(lead["excerpt"])}</p><p class="byline">{escape(lead["byline"])}</p>'
                 f'<p class="meta"><time datetime="2026-10-06T04:16:47+00:00">6 October 2026</time>'
                 f'<span class="meta__sep" aria-hidden="true"></span><span>6 min read</span></p>'
                 f'<a class="btn" href="{article_url(lead)}">Read the article</a></div>{figure(lead, "lead__figure", True)}</article>')
    for slug, label in GROUPS.items():
        order_slugs = order.get(slug, [])
        selected = [next(a for a in articles if a['slug'] == s) for s in order_slugs if any(a['slug'] == s for a in articles)]
        bird = f'<span class="division__bird">{CARDINAL_SVG}</span>' if slug == 'research-reviews' else ''
        homepage += f'<section class="division"><h2 class="division__title">{division_icon(slug)}<a href="/category/{slug}/">{escape(label)}</a>{bird}</h2><div class="cards">' + ''.join(card(a, index == 0) for index, a in enumerate(selected)) + '</div></section>'
    homepage += ('<section class="submit"><div class="submit__header">'
                 '<img src="/assets/submission-packet.svg" class="submit__icon" alt="" width="44" height="44" aria-hidden="true">'
                 '<h2 class="submit__title">Write for us</h2></div>'
                 '<a class="btn btn--invert" href="/submit/">Submission guidelines</a></section>')
    write_page('/', NAME, homepage, articles, 'Read the Fall 2026 edition of The Wesleyan Science Journal.')
    for a in articles:
        related = [other for other in articles if other['section'] == a['section'] and other != a]
        body = ('<div id="reading-progress" class="reading-progress" aria-hidden="true"></div>'
                f'<article class="article"><h1 class="article__title">{escape(a["title"])}</h1>'
                f'<p class="byline">{escape(a["byline"])}</p><p class="meta"><a href="/category/{section_slug(a["section"])}/">{escape(a["section"])}</a> / {escape(a["type"])}</p>'
                + figure(a, 'article__figure', True) + f'<div class="prose">{with_end_mark(a["bodyHtml"])}</div></article>')
        if related:
            body += '<section class="division"><h2 class="division__title">More from this section</h2><div class="cards">' + ''.join(card(other) for other in related) + '</div></section>'
        write_page(article_url(a), a['title'], body, articles, a['excerpt'])
    for slug, label in SECTIONS.items():
        selected = [a for a in articles if section_slug(a['section']) == slug]
        write_page(f'/category/{slug}/', label, f'<h1 class="archive-title">{division_icon(slug)}{escape(label)}</h1><div class="cards">' + ''.join(card(a, level=2) for a in selected) + '</div>', articles)
        for kind in sorted({a['type'] for a in selected}):
            write_page(f'/category/{slug}/{type_slug(kind)}/', kind, f'<h1 class="archive-title">{division_icon(slug)}{escape(label)}: {escape(kind)}</h1><div class="cards">' + ''.join(card(a, level=2) for a in selected if a['type'] == kind) + '</div>', articles)
    for slug, label in GROUPS.items():
        selected = [a for a in articles if group_slug(a) == slug]
        write_page(f'/category/{slug}/', label, f'<h1 class="archive-title">{division_icon(slug)}{escape(label)}</h1><div class="cards">' + ''.join(card(a, level=2) for a in selected) + '</div>', articles)
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
    submit_content = (
        '<article class="article"><h1 class="article__title">Submission guidelines</h1>'
        '<figure class="submission-hero">'
        '<img src="/assets/submission-packet.svg" class="submission-hero__icon" alt="" width="56" height="56" aria-hidden="true">'
        '<figcaption>Official WesSciJo Manuscript Guidelines Packet</figcaption></figure>'
        '<p class="submission-link"><a class="btn" href="' + FORM + '">Open the article submission form</a></p>'
        '<div class="prose">' + guidelines + '</div></article>'
    )
    write_page('/submit/', 'Submission guidelines', submit_content, articles)
    write_page('/search/', 'Search', '<h1 class="archive-title" id="search-title">Search the journal</h1><p id="search-status" role="status"></p><div class="cards" id="search-results"></div><noscript><p>Enable JavaScript to search, or <a href="/archives/">browse all articles</a>.</p></noscript>', articles)
    (PUBLIC / '404.html').write_text(document('Page not found', '<div class="search-empty"><img src="/assets/empty-search-lens.svg" class="search-empty__icon" alt="" width="72" height="72" aria-hidden="true"><h1 class="archive-title">Page not found</h1><p><a href="/">Return to the journal</a></p></div>', articles))
    shutil.copyfile(ROOT / 'themes/wesscijo/style.css', PUBLIC / 'publication.css')
    shutil.copyfile(ROOT / 'themes/wesscijo/assets/logo-flight.svg', PUBLIC / 'assets/logo-flight.svg')
    shutil.copyfile(ROOT / 'themes/wesscijo/assets/logo-flight-dark.svg', PUBLIC / 'assets/logo-flight-dark.svg')
    shutil.copyfile(ROOT / 'themes/wesscijo/assets/logo-entrance.js', PUBLIC / 'assets/logo-entrance.js')
    shutil.copyfile(ROOT / 'themes/wesscijo/assets/cardinal-watch.js', PUBLIC / 'assets/cardinal-watch.js')
    for svg_file in (ROOT / 'themes/wesscijo/assets').glob('division-*.svg'):
        shutil.copyfile(svg_file, PUBLIC / 'assets' / svg_file.name)
    shutil.copytree(ROOT / 'themes/wesscijo/assets/fonts', PUBLIC / 'assets/fonts', dirs_exist_ok=True)
    index = [{k: a[k] for k in ('slug', 'title', 'byline', 'section', 'type', 'excerpt', 'thumbnail')} | {'text': html.unescape(re.sub('<[^>]+>', ' ', a['bodyHtml']))} for a in articles]
    (PUBLIC / 'search-index.json').write_text(json.dumps(index, ensure_ascii=False), encoding='utf-8')
    print(f'Rendered {len(articles)} articles, their sections, About, submissions, archives, and search.')


if __name__ == '__main__':
    main()
