# JOURNAL - WesSciJo

> Dated log of design decisions, pivots, incidents, and project context for the Wesleyan Science Journal website concepts. This backfill was assembled from the repository history, current source, and committed screenshots on 2026-08-28.

## 2026-09-14 - Client feedback was implemented in Hybrid A #feedback #milestone

The Hybrid A theme now uses native disclosure controls for section navigation: each dropdown can be opened by click or keyboard, includes an explicit link to the full division, and collapses into the same disclosure pattern on small screens. Search requests are limited to article posts so result pages stay within the article-card layout. The masthead title and custom-logo side are now configurable in the WordPress Customizer, while the title uses the same sans-serif brand family as the rest of the theme. The About roster now follows the five requested section names and removes Maddy Marx; no biography copy was invented.

## 2026-09-17 - Hybrid A became the canonical live site #deployment #decision

The reviewed Hybrid A theme is now deployed at the public WesSciJo site under the final `wessci` Compose project identity. Persistent WordPress data was preserved during the rename. Public verification confirmed the requested About roster, the removal of Maddy Marx, working mobile disclosure menus, and the earlier search and Home-link fixes. The Calendar route is live, but its four existing entries still contain placeholder event copy; no dates or event details were invented.

## 2026-08-28 - The repository is still a prototype handoff #feedback #incident

The [README](README.md) describes five custom WordPress theme concepts, not a launched publication site. The committed screenshots use placeholder articles and mock covers. A source audit also found that route coverage is not uniform: `wessci-hybrid-a` and `wessci-hybrid-b` contain custom `archive.php`, `search.php`, `page.php`, and `404.php` templates, while the original dark, brutal, and broadsheet themes contain only `index.php` and `single.php`; only hybrid A contains the custom About and Calendar templates. On a WordPress install, the first three themes therefore fall back to their homepage-style index loop for routes that the hybrid themes handle explicitly. Hybrid A's [About page](themes/wessci-hybrid-a/template-about.php) still uses names and roles instead of bios, and its [Calendar support](themes/wessci-hybrid-a/template-calendar.php) intentionally stops at a `wessci_event` post type with title, editor, and thumbnail support, no custom date fields, and an empty state. The current [CI workflow](.github/workflows/ci.yml) checks PHP syntax on PHP 8.5, Stylelint, and Gitleaks, but the repository has no package manifest, content fixture, application test suite, build step, or deployment workflow.

## 2026-08-27 - Keep private work out of the repository #decision #incident

By the final commit, `.gitignore` contained only `notes/` and `.worktrees/`; the final cleanup removed a duplicate macOS ignore rule. The intent is worth retaining because the project uses local working notes and isolated feature worktrees during design review. Those files must remain local, while the repository keeps the source themes, review screenshots, and public-facing documentation.

## 2026-08-26 - CI was tightened around read access and valid action versions #decision #milestone

CI started on 2026-08-17 with PHP syntax linting, CSS validation, and Gitleaks. On 2026-08-22, Stylelint was made a zero-warning gate with `--max-warnings 0` and an explicit `.stylelintrc.json`. The workflow was then brought into line with the shared CI policy by adding top-level `contents: read` permissions to the main [CI workflow](.github/workflows/ci.yml), keeping cancellation for superseded runs, and standardizing action versions to stable major releases. The separate [Dependabot workflow](.github/workflows/dependabot-auto-merge.yml) keeps write permissions because it enables automatic squash merges for non-major Dependabot updates. Ordinary validation has read-only access, while the narrowly scoped merge job retains the authority it needs.

## 2026-08-16 - The client-facing surface moved beyond the homepage #feedback #milestone

The hybrid-A concept gained the page surfaces needed for a client review: a full About page, a Calendar page, a navigation entry for each, and five screenshots covering the homepage, article view, navigation dropdown, About page, and Calendar page. The About template records the Fall 2026 masthead supplied for review across Editorial, Life Sciences, Physical Science, Quantitative & Computational Science, Science, Technology & Society, Graphic Design, and Web Design, then renders initials as temporary avatars. The implementation deliberately does not invent biographies. The Calendar template provides a place for future events and renders an empty state when no `wessci_event` posts exist; it is a layout surface, not a finished event-management feature.

## 2026-08-12 - WordPress route fallbacks exposed a real product bug #incident

Before the hybrid route work, `index.php` caught every non-single request, so archive links, category links, About, Submit, and search results could render the homepage query instead of the requested view. The fix added `archive.php`, `page.php`, `search.php`, and `404.php` to both hybrid themes and added the CSS needed for empty states and pagination. The new templates keep the same card and article language as the homepages, so category pages, search results, ordinary pages, and missing pages now have explicit presentation boundaries in the hybrid concepts.

## 2026-08-12 - Rebuilding the hybrids was a deliberate design pivot #pivot #decision

The first hybrid A and B implementations were removed so the two directions could be redone with a new design pass; their code and README entries were removed from the branch before the rebuild. Hybrid A was then rebuilt around a poster-scale black title band, an 8px Wesleyan-red hinge, a white navigation index, a text-left and image-right lead, and an asymmetric card grid. Hybrid B kept the black title band but made the index a white panel that overlaps the band, then used quieter type and an even ruled grid. The distinction survived into the current CSS and screenshots: A gets its energy from scale and the black-red-white transition, while B relies on spacing, rules, and restrained typography.

## 2026-08-12 - Cache and footer bugs changed the verification workflow #incident

The rebuilt hybrid A initially enqueued its stylesheet as `style.css?ver=1.0.0`, which allowed the browser to reuse the previous theme's CSS. A screenshot then showed an unstyled hero and an old 4:5 lead image even though the new source used the intended layout. Versioning the stylesheet with `filemtime()` made each deploy produce a fresh URL. A second visual defect came from reusing the header's index-group markup in the dark footer: links inherited near-black text from the white header and disappeared against the dark colophon. Scoping a `.colophon` override restored the links, and later captures were used to verify the footer as part of the whole page rather than treating the hero as the only visual gate.

## 2026-08-11 - Screenshot capture became part of implementation evidence #incident #feedback

The first hybrid captures surfaced two different media problems. Hybrid A's card thumbnails were blank until the regenerated media was present, while hybrid B's scroll-based lazy-loading trigger left thumbnails empty in the capture. The reliable capture path forced eager image loading and waited for `img.decode()` before saving the screenshot. The committed concept previews now show the intended article imagery and preserve the comparison set at a common 1440px width, while the client-reply captures document the hybrid-A route and content surfaces at their taller page lengths.

## 2026-08-11 - Two hybrids tested different ways to carry the same editorial system #decision #pivot

Hybrid A began as a fork of the brutalist direction, then moved toward a Swiss text-left and image-right lead with a black hero, red hinge, white index, and less aggressive heading typography. Hybrid B forked from that work to test a different rhythm: a floating index panel, quiet Libre Franklin headings, IBM Plex Mono metadata, and equal cards separated by rules instead of an asymmetric first-card span. Both variants kept the shared content model: a latest-post lead, the `research-reviews` and `news-features-perspectives` divisions, child-category navigation, three supporting cards per division, related stories on article pages, and a submission prompt.

## 2026-08-10 - Article navigation stopped being a fake success #incident

The first three themes initially had only homepage templates. Clicking a story did not open an article view; WordPress fell back to the homepage loop and made the interaction look successful while showing the wrong content. Adding a `single.php` to each theme established a distinct article layout in each visual language: the dark theme kept its hover-panel navigation, the brutalist theme kept its permanent section strip, and the broadsheet theme added a drop cap. Each article view also received a `More from <division>` block that reused the existing card component.

## 2026-08-10 - The project started as three measured visual directions #decision #milestone

The initial commit established the product as three from-scratch WordPress themes with no page builder: `wessci-dark` as a near-black laboratory interface with red accents and technical metadata, `wessci-brutal` as a bold editorial poster with red slabs and open indexes, and `wessci-broadsheet` as a warm archival newspaper with Caslon typography and column rules. The themes share a publication model rather than sharing a visual treatment. Their helpers discover the two top-level divisions, select the most specific child category as an article type, calculate reading time from post content at roughly 200 words per minute, and keep image, title, search, and HTML5 theme support in one predictable baseline. The palette was measured from Wesleyan's site, with Wesleyan red reserved as the common accent.
