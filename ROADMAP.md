# WesSciJo Frontend Redesign Roadmap

> Directional plan for a Hybrid A editorial redesign. This document is intentionally isolated from `main` until the editorial team approves the direction.

## Status

- Branch: `docs/frontend-redesign-roadmap`
- Scope: `themes/wessci-hybrid-a/` and frontend-facing publication architecture
- Other concept themes: unchanged
- Current production site: `https://wessci.yinkavaughan.me/`
- Content state: still a prototype handoff with placeholder articles, event entries, and mock covers
- Release state: content-blocked until approved copy, event details, archive material, submission guidelines, and replacement lead artwork are available

This roadmap describes the work needed to make the site feel like a mature science publication. It does not authorize a deploy, a content edit, or a change to the live WordPress site.

## Product outcome

WesSciJo should feel like an active, distinctive publication rather than a collection of attractive templates. A visitor should be able to answer these questions quickly:

1. What is the current issue or editorial focus?
2. Is this a research article, review, news story, interview, or essay?
3. What scientific field and Wesleyan community context does it belong to?
4. Who wrote or edited it, and how should it be read or cited?
5. What should I read next, submit, attend, or subscribe to?

The redesign should keep Hybrid A's black masthead, Wesleyan-red hinge, white index, and text-left/image-right lead. The improvement should come from a stronger editorial system, better media, and clearer discovery paths—not from decorative effects.

## Design principles

- Content leads the visual treatment. Do not use animation or decoration to compensate for missing editorial material.
- Preserve the existing placeholder copy byte-for-byte until approved replacement content is supplied.
- Make every module conditional. Missing bylines, dates, bios, images, abstracts, and links should produce a deliberate fallback, not an invented value or an empty-looking card.
- Separate publication types visibly: research, reviews, news, features, perspectives, interviews, and essays should not all look interchangeable.
- Use approved imagery, diagrams, and illustrations that match the story. The current Biology cover paired with the CS reproducibility lead remains an editorial QA blocker.
- Keep keyboard access, visible focus, skip-link behavior, native disclosure menus, responsive layout, and reduced-motion behavior intact.
- Prefer fast, resilient HTML and CSS over a JavaScript-heavy “award site” treatment.

## Roadmap phases

### Phase 0 — Direction and content contract

Goal: agree on the visual and editorial direction before implementation work expands.

Deliverables:

- Confirm Hybrid A as the starting point for the redesign.
- Approve a small design system: type roles, color roles, spacing, rules, image ratios, card variants, metadata labels, and focus states.
- Define the publication content model for article type, field, author, affiliation, issue, abstract/deck, keywords, figures, references, related stories, and event details.
- Inventory approved editorial copy and media separately from placeholders.
- Decide whether the publication needs a newsletter, RSS emphasis, social links, or another recurring audience channel.
- Capture desktop and mobile reference screenshots for team review.

Exit gate: editorial and design stakeholders agree on the direction and identify which content fields are required at launch.

### Phase 1 — Editorial foundation

Goal: make the WordPress presentation layer capable of expressing a real publication without requiring content to be invented now.

Work:

- Keep durable site data in the site plugin rather than coupling it to the theme.
- Add conditional presentation components for article type, author/byline, affiliation, deck or abstract, keywords, figure captions, references, related stories, issue information, and event metadata.
- Keep existing article and event post-type keys, IDs, slugs, and permalinks stable.
- Add issue and field relationships only where the editorial model confirms they are needed.
- Provide intentional empty states for Archives, Calendar, About bios, and future metadata.
- Ensure SEO and social metadata have one owner and do not duplicate an active SEO plugin.

Exit gate: existing routes and placeholder records render unchanged, while newly supplied fields can appear without template rewrites.

### Phase 2 — Visual system upgrade

Goal: give the site a recognizable publication voice.

Work:

- Introduce clear display, reading, interface, and metadata type roles. Use the serif display treatment selectively for major editorial headlines or long-form reading, while retaining a sans-serif UI.
- Strengthen the hierarchy between the current issue, lead story, section headings, cards, captions, and metadata.
- Create card variants for research, review, news, feature, and perspective content.
- Replace the repeated generic-cover feel with a documented image system: approved photography, scientific diagrams, illustrations, and field-specific visual cues.
- Add active navigation and current-section states.
- Make search a more visible publication tool without adding speculative popular searches before real usage or content data exists.
- Keep the current mobile navigation structure unless testing identifies an actual overflow or keyboard failure.

Exit gate: a visitor can distinguish content type, importance, and section at a glance on desktop and mobile.

### Phase 3 — Homepage and discovery

Goal: make the homepage feel alive and useful after the first lead story.

Work:

- Retain one strong lead story, but add a clearer latest-stories path beneath it.
- Add a curated lane such as Editor's Picks only if the editorial team can maintain it.
- Add field or division browsing without turning the header into a crowded menu.
- Add a compact issue identity and a route to the complete issue/archive index.
- Add related-story and “continue reading” patterns that are driven by real taxonomy.
- Use varied editorial compositions instead of repeating the same six-card grid for every section.

Exit gate: the homepage offers at least three clear next actions—read, browse by field/type, and explore the issue/archive—without relying on placeholder copy to create false activity.

### Phase 4 — Article, archive, and calendar surfaces

Goal: make secondary routes feel like first-class publication experiences.

Article page:

- Show article type, title, deck or abstract, author and affiliation when supplied, date, reading time, and approved lead media.
- Support figure captions, pull quotes, references, downloadable material, and a reading outline only when the content requires them.
- Add related stories and a clear return path to the relevant division or issue.

Archive page:

- Display issue cards with volume, number, publication date, cover, and included sections when approved data exists.
- Provide field/type filtering only when it improves discovery and remains usable on mobile.
- Keep the inaugural-issue placeholder intact until archive material is supplied.

Calendar page:

- Render real start/end dates, location, external details, and calendar actions only when those fields exist.
- Preserve existing undated placeholder entries and their copy until the editorial team supplies replacements.

About page:

- Replace initials-only presentation with approved portraits and short bios when available.
- Keep the current roster and requested section names accurate; do not infer credentials or biographies.

Exit gate: each route has a clear purpose, a useful empty state, and no placeholder-looking controls that imply unavailable functionality.

### Phase 5 — Quality, performance, and release preparation

Goal: protect the experience while visual richness increases.

Checks:

- Verify the single `h1`, landmark structure, heading order, labels, skip link, keyboard menus, focus visibility, contrast, and reduced-motion behavior.
- Keep the lead image eager and high priority; lazy-load below-fold media.
- Use explicit image dimensions, correct `srcset`/`sizes`, and modern formats where the approved media pipeline supports them.
- Check 390px, 768px, and 1280px layouts for overflow and awkward wrapping.
- Test search, archives, article links, calendar links, 404 behavior, and WordPress cache interactions.
- Run PHP lint, Stylelint, secret scanning, and the Playwright smoke suite from a clean checkout.
- Perform a manual editorial QA pass for every image/title pairing and every public placeholder.
- Treat Lighthouse or WCAG certification as a later formal audit, not as a substitute for content approval.

Exit gate: technical checks pass, approved media matches its stories, and the editorial team signs off on the release content.

## Workstreams and ownership

Implementation can be split into isolated, reviewable workstreams after Phase 0:

1. Site architecture: plugin-owned fields, issue/event data, metadata ownership, and stable routes.
2. Theme system: typography, cards, homepage compositions, article/archive/calendar templates, and responsive behavior.
3. Media and content integration: approved image treatment, captions, alt text, issue covers, and editorial QA.
4. Browser verification: Playwright route, keyboard, responsive, metadata, and image-policy checks.
5. Deployment hardening: caching, compression, headers, and cautious CSP work only after the canonical infrastructure configuration is located and separately approved.

Each workstream should return changed paths, tests run, assumptions, and blockers. Keep commits atomic and merge only reviewable slices.

## Branch and release strategy

- Keep this roadmap on `docs/frontend-redesign-roadmap` while the team reviews the direction.
- Do not modify the other concept themes as part of this redesign.
- After approval, create an implementation branch from `main`, for example `feat/hybrid-a-editorial-v2`.
- Keep implementation, content entry, and deployment as separate approval points.
- Do not push, merge, deploy, alter WordPress content, or modify host configuration from this roadmap branch without explicit approval.
- Preserve the existing uncommitted `JOURNAL.md` change when moving between branches.
- Flush WordPress rewrite rules once during an approved deployment, not on every request.

## Inspiration set

These are references for website and publication design, not claims about editorial awards:

- [Quanta Magazine — 2025 Webby People's Voice Winner for Science](https://winners.webbyawards.com/2025/websites-and-mobile-sites/general-desktop-mobile-sites/science/324307/quanta-magazine): use its topic taxonomy, archive/search, bylines, decks, newsletters, podcasts, video, and multiple homepage lanes as a direct science-publication reference. See the [current Quanta site](https://www.quantamagazine.org/).
- [Emergence Magazine — 2024 Webby and People's Voice Winner](https://winners.webbyawards.com/2024/websites-and-mobile-sites/general-desktop-mobile-sites/magazine/277387/emergence-magazine): use its flexible story system, filterable library, persistent search, and restrained motion. Its [Webby case study](https://www.webbyawards.com/crafted-with-code/emergence-magazine/) documents the WordPress-backed approach.
- [The Marshall Project — Society for News Design “World's Best Designed Website”](https://www.themarshallproject.org/2016/04/12/the-marshall-project-named-world-s-best-designed-website): use its audience-first wayfinding, newsletter pathway, information graphics, and cross-channel design discipline.
- [The New Yorker — 2025 Webby People's Voice Winner for Magazine or Publication](https://winners.webbyawards.com/winners/websites-and-mobile-sites/general-desktop-mobile-sites/magazine-or-publication): use its mature separation of editorial sections and formats, not its subscription machinery.
- [HHMI's Beautiful Biology — 2025 Webby Winner for Science](https://winners.webbyawards.com/2025/websites-and-mobile-sites/general-desktop-mobile-sites/science/326158/hhmis-beautiful-biology): use it as a visual science-communication reference for illustration and imagery.

Quanta and Emergence are the closest benchmarks for WesSciJo. The Marshall Project is the strongest reference for audience pathways, while HHMI is the strongest adjacent reference for making science visually distinctive.

## Acceptance criteria

The redesign is ready for team review when:

- Hybrid A feels like one coherent publication system rather than repeated concept cards.
- Article type, issue context, section, and next actions are clear.
- Archives, articles, Calendar, About, search, and 404 routes have deliberate layouts and empty states.
- Approved media is editorially matched, has purposeful alt text, and loads according to the image policy.
- Existing placeholder copy remains unchanged until approved content is supplied.
- Existing article/event URLs, post IDs, event records, navigation, search, skip link, keyboard menus, and responsive behavior continue to work.
- The team has approved the content, artwork, and submission/event details required for launch.

## Explicit non-goals

- Rewriting placeholder articles or event copy
- Guessing a replacement for the current lead artwork
- Redesigning the mobile navigation without evidence of a failure
- Changing `wessci-hybrid-b`, `wessci-dark`, `wessci-brutal`, or `wessci-broadsheet`
- Deploying or changing server configuration as part of frontend design work
- Claiming launch readiness before editorial approval
