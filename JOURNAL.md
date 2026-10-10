# JOURNAL - WesSciJo

## 2026-10-08 - 3-state system theme toggle #theme #accessibility #ux

Added a native 3-state theme preference cycle (`System` -> `Light` -> `Dark` -> `System`) across the WordPress theme and static publication build without modifying any article HTML files or page templates. The script dynamically injects an SVG monitor/display glyph into `#theme-toggle` on initialization. In System mode, explicit theme overrides (`localStorage['theme']` and `data-theme`) are cleanly removed, allowing the OS `prefers-color-scheme` media query to control page styling dynamically. View Transitions API and prefers-reduced-motion guards remain active across all transitions. Verified with Playwright on `ampere-dev`.

## 2026-10-08 - Optical Illumination Dissolve and red scrollbar #theme #motion #ux

Replaced the circular wipe with an Optical Illumination Dissolve across the WordPress theme and static publication build. Inspired by scientific brightfield/darkfield microscopy transitions, the page executes a 220ms ambient exposure cross-dissolve via the View Transitions API and GPU-accelerated CSS keyframes (`theme-fade-out` / `theme-fade-in`). The toggle button features a precision 200ms 45-degree rotation and scale morph between the Sun and Moon SVG icons. All animations are strictly bypassed under prefers-reduced-motion. Added theme-aware custom scrollbar styling utilizing standard `scrollbar-color`/`scrollbar-width` and WebKit fallbacks styled with a Cardinal Red thumb (`--color-accent`, darkening to `--color-accent-2` on hover) over `--color-paper`.

## 2026-10-08 - Accessible motion strategy and exclusive navigation panels #motion #ux #accessibility

Implemented the publication motion strategy across the WordPress theme and static publication site. Added an accessible reading progress bar with passive scroll tracking, high z-index, and responsive WordPress admin bar offsets. Implemented tactile button presses, About tab fade transitions, and search card entrance animations with strict suppression under prefers-reduced-motion. Updated header navigation disclosure panels with exclusive grouping (`name="nav-panel"`), auto-collapse accordion logic, and click-outside/Escape dismissal.

## 2026-10-07 - Retire Vercel from WordPress publishing #deployment #decision

The target is self-hosted WordPress production and staging on Oracle OCI Always Free resources. One Micro VM per environment is an option, subject to the 1 GB memory limit. The WordPress plugin no longer triggers Vercel deployments or exposes Vercel rebuild controls in the dashboard. Keep the existing static output and Vercel configuration until WordPress production is ready and the domain cutover is approved.

## 2026-10-06 - Keep preview assets with the preview #organization

The Cardinal preview no longer loads files from its deleted theme. Its stylesheet, menu script, illustration, and font files now live under `preview/`. The README lists the remaining WordPress theme and the preview's local assets. The publication renderer still uses `themes/wesscijo` for its stylesheet and About template.

## 2026-10-05 - Restyle the native WordPress dashboard #cms #decision

The owner clarified that the redesign must retain WordPress's admin bar, full sidebar, welcome panel, default widgets, Screen Options, and movable/collapsible widget layout. Removed the replacement preview shell, custom sidebar ordering, and forced full-width widget layout. The admin styling uses cardinal red and carbon without changing WordPress's navigation or widget structure. Manuscripts and issue assembly remain additional native dashboard widgets.

Issue totals count only posts assigned to the selected issue and visible to the current editor. Missing format, abstract/deck, and division values appear beside each manuscript; these checks do not establish editorial approval. Publishing uses an administrator-only WordPress submenu, confirmed POST, nonce, and HTTP acceptance feedback. Deployment health and completion remain unavailable.

The queue widget now has four columns (manuscript with author and division, stage, next step, updated) so it fits a half-width native column, and it defaults to active manuscripts. The next step comes from the stage and from missing format, abstract or deck, and division values; no responsible-editor field exists yet, so the queue omits one. Issue assembly shows the selected issue or the newest issue, lists readiness by stage, and links manuscripts with missing details. The admin bar, sidebar, buttons, and links take the cardinal and carbon colors on every admin screen for users on the default color scheme; queue and issue layouts stay scoped to WesSciJo screens. The native welcome panel is unchanged apart from color.

WordPress 7.1 ships a blue "modern" admin color scheme, so the first live check showed none of the WesSciJo colors. The plugin now overrides `--wp-admin-theme-color` and its darker variants, which recolors links, buttons, focus rings, and the current menu item without touching WordPress's own selectors. Dashboard widget headers are carbon with a cardinal red rule, the welcome panel header is carbon with the cardinal and nest illustration (`admin/images/cardinal-field.svg`, a copy of the theme asset so the plugin stays self-contained), and a crop of the cardinal replaces the WordPress logo in the admin bar. The welcome heading is changed through the `gettext` filter, so the native panel and its dismiss control remain. A running server started with `verify-on-vm` serves a snapshot; run `verify-on-vm server sync` after editing the plugin.

Verification covers query scoping, rebuild authorization and HTTP responses with a stubbed network boundary, and the static preview at phone and desktop widths. The preview uses sample content. WordPress integration, real role transitions, and deployment completion require a running WordPress environment.

## 2026-10-05 - Architect and build custom WordPress CMS editorial dashboard #cms #editorial #architecture #ux

Following approval of the CMS dashboard plan, the WordPress admin interface was transformed from the generic WordPress blogging screen into a specialized Editorial Command Center tailored for the 25-student Editorial Board across its 5 academic divisions:

1. Editorial Command Center Widgets (`admin/views/`):
   - Volume 14 Issue Assembly & Progress: Visual distribution progress bar mapping active manuscripts across Life Sciences, Physical Sciences, Quantitative & Computational Science, and STS against the target issue size (14 papers).
   - Division Editorial Queues: Interactive tabbed dashboard widget allowing section heads (e.g., Lead Life Science Editor) to filter and review manuscripts in their specific discipline, showing editorial stage badges and quick review links.
   - Vercel Production & Deployment Monitor: Real-time edge production health status, live journal link, and 1-click manual rebuild trigger with cache-busting.
   - Scientific Editorial & Figure Checklist: Standards reference for student editors covering >= 1200px figure requirements, licensing/attribution, formal abstracts, author graduation year, and DOI citations.

2. Editorial Workflow Pipeline & Post Statuses (`includes/class-wessci-editorial.php`):
   - Registered custom editorial post statuses: In Review (`in_review`), Copyediting (`copyediting`), and Ready for Issue (`ready_for_issue`).
   - Integrated custom statuses into edit list tables, quick-edit dropdowns, and post publish panels with high-contrast, accessible status badges.
   - Registered hierarchical Volumes & Issues taxonomy (`wessci_issue`) for formal volume release bundling.

3. Scientific Manuscript Authoring & Meta Boxes (`includes/class-wessci-meta-boxes.php`):
   - Academic Details: Article format selector (Research Article, Review, Feature, Perspective, Interview) and structured academic abstract/deck.
   - Author Credentials: Multi-author repeater capturing student graduation year (e.g., '26), Wesleyan laboratory/department, and institutional affiliation.
   - Scientific Figures & Data Assets: Up to 4 structured figure slots capturing high-res image attribution, figure label, descriptive caption, and explicit license (Author Original, CC BY 4.0, Public Domain, Fair Use).
   - References & Citations: Bibliographic reference editor with automated DOI hyperlinking.

4. Branded Admin Shell & Masthead Governance (`admin/css/` & `admin/views/`):
   - Custom admin theme styling adhering to Wesleyan Cardinal Red (`#c51230`) and Carbon Black (`#100e0f`), with streamlined admin bar branding and decluttered sidebar navigation.
   - Masthead Management screen (`admin.php?page=wessci-masthead`) rendering the 25-student editorial roster across Executive Leadership, Life Sciences, Physical Sciences, Quantitative, and STS divisions.

## 2026-10-05 - Suppress underlines under arrows on button hover animations #design #microinteractions #css

Following review of the hybrid navigation controls, the owner flagged an awkward visual artifact on button hover animations: "Modify the hover animation for all buttons to not add an underline under arrows. It looks weird." A shared screenshot demonstrated that on hover over the header Search button (`Search ↗`), the text "Search" was underlined in Cardinal Red, but a disconnected, floating red dash was also drawn directly beneath the trailing diagonal arrow (`↗`).

Root Cause Analysis:
1. Ancestor Text-Decoration Leakage: Base styles in `styles.css` specified `a:hover { text-decoration: underline; text-decoration-thickness: 2px; }`. Because `.utility` and `.issue-bar-link` are anchor elements containing both text and `<span aria-hidden="true">↗</span>`, the browser's native text decoration engine rendered the underline across all inline descendants, including the arrow glyph.
2. Full-Container Pseudo Underlines: On the hero tree subject buttons (`.tree-composition .subject`), the selection and preview underline was anchored to `.subject::after` with `width: 100%`, extending from the left edge all the way under `.subject-arrow`.
3. Border-Bottom on Flex Containers: In `.read-link`, `border-bottom: 1px solid currentColor` was applied to the entire flex link container rather than the text label, drawing a continuous line beneath the trailing arrow.

Architecture and Implementation:
1. Semantic Separation of Label and Icon:
   - Wrapped text labels in explicit `.btn-text` containers and isolated arrow glyphs in `.btn-arrow` across all interactive buttons:
     - Header utilities: `Search ↗` (`.utility`, `a[data-od-id="search-link"]`) and `Menu +` (`#menu-toggle`).
     - Issue bar masthead: `All Volumes & Issues ↗` (`.issue-bar-link`).
     - Hero tree subject callouts: `01 Field science ↗`, `02 Chemistry ↗`, `03 Mathematics / technology ↗` (with `.subject-text`).
     - Reading directory links: `Search the reading directory ↗`, `Read the story ↗`, `Enter the research spread ↗`, `Read the essay ↗` (`.read-link`).
2. Strict Arrow Decoration Neutralization:
   - Globally reset `.utility`, `.header-actions .utility`, `.issue-bar-link`, `.read-link`, and `.tree-composition .subject` on hover with `text-decoration: none !important;`.
   - Explicitly suppressed underlines and borders on all arrow containers (`.btn-arrow`, `.subject-arrow`, `a span[aria-hidden="true"]`) via `text-decoration: none !important; border-bottom: none !important; display: inline-block !important;`.
3. Scoped Text Underline & Kinetic Arrow Hover:
   - Re-anchored the 2px Wesleyan Cardinal Red hover underline strictly to the text label (`.btn-text`, `.subject-text::after`).
   - Arrow icons translate cleanly `(2px, -2px)` on hover with zero underline beneath them, restoring dignified academic polish.
   - All transforms strictly nullify under `prefers-reduced-motion: reduce`.
4. DevTools Validation:
   - Verified across desktop viewports in both Midnight Dark and Pure White Light themes. Element screenshots confirmed zero underlines under arrows across all button types.

## 2026-10-04 - Port live WordPress publication features to hybrid concept #content #editorial #architecture

Following an audit of the live Wesleyan Science Journal site (https://wessci.yinkavaughan.me/), several key features, content structures, and authentic metadata were ported into the hybrid prototype to transform it from a static design test into a fully functional academic publication:

1. Full 25-Student Editorial Board Masthead across 5 Divisions (?view=about):
   - Executive Leadership: 3 Editors-in-Chief (Aryia Banihashem-Ahmad, Shriya Sakalkale, Elena Mente).
   - Life Sciences Division: 11 editors covering biology, neuroscience, and psychology.
   - Physical Sciences Division: 9 editors covering astronomy, physics, chemistry, earth & environmental sciences.
   - Quantitative & Computational Science: 5 editors covering math and computer science.
   - Science, Technology & Society: 4 editors.
   - Structured with clean monogram avatar circles, bold names, muted roles, and responsive auto-fill cards.

2. Comprehensive 11-Article Archive & Rich Live Search (?view=search):
   - Expanded search indexing to incorporate all 8 published articles from the live database alongside the 3 flagship interactive spreads.
   - Enriched search results with full publication metadata: category badge, division, date, reading time, and descriptive excerpts.
   - Implemented multi-field search filtering across title, abstract, and category tags with smooth client-side category switching and reduced-motion compliance.

3. Current Issue Identity Bar (.issue-bar):
   - Seated beneath the site header across all views: Cardinal Red [CURRENT ISSUE] badge, Volume 14 identification, and direct link to volume archives.

4. Structured Academic Footer Navigation (.footer-columns):
   - Replaced flat link rows with a structured discovery layout separating Divisions & Formats from Journal Governance.

5. Cross-Theme Polish & Validation:
   - Validated across Midnight Dark and Pure White Light themes with zero contrast collisions.

## 2026-10-04 - Transition science field controls from heavy UI boxes to Naturalist Specimen Callouts #design #editorial #illustration

Following review of the hero tree illustration on the homepage, the owner inquired whether the science field boxes were good and matched the design system. Evaluation confirmed a severe aesthetic clash: the controls were styled as heavy, opaque rectangular SaaS-style cards with a 6px knockout shadow (`box-shadow: 0 0 0 6px var(--bg)`) that physically bit holes into the tree branches and leaves, and an active state that inverted into an overpowering solid white brick with a 3px red outline.

Architecture and Implementation:
1. Naturalist Specimen Callout System:
   - Eliminated all rigid rectangular box containers, opaque backgrounds, and destructive knockout shadows.
   - Restructured the controls as authentic botanical plate annotations anchored alongside the branches.
   - Monospaced figure indices (`01`, `02`, `03`) styled in Wesleyan Cardinal Red (`color: var(--wes-red); font-family: var(--mono)`) establishing immediate specimen hierarchy.
   - Clear Barlow Condensed signage typography (`font: 500 clamp(18px, 1.5vw, 24px) / 1.15 var(--signage)`) with soft optical contrast halos (`text-shadow: 0 1px 4px var(--bg)`).
   - Dynamic directional arrows (`↗`) that smoothly translate `(3px, -2px)` on hover and selection.
2. Unified Editorial Selection Underline:
   - Active subject (`[aria-pressed="true"]`) illuminates in high-contrast `--fg` with an elegant 2.5px Wesleyan Cardinal Red underline rule (`::after`) seated flush beneath the label, mirroring the editorial tabs of the Search page.
   - Unselected subjects rest quietly in muted tones (`var(--muted)`), letting the tree illustration, perched cardinal, and glassware take visual center stage.
   - Hover state expands a 45% translucent Cardinal Red preview underline while triggering established tree reactions (cardinal posture perks and glassware illumination).
3. Cross-Theme & Viewport Validation:
   - Seamless presentation verified across Midnight Dark and Pure White Light modes.
   - Verified across desktop (1440px) and mobile viewports (600px, 390px) with zero horizontal overflow and zero layout shift.

## 2026-10-04 - Redesign category filter navigation with editorial underline tabs #design #microinteractions #a11y

Following review of the Search page, the owner flagged the selected category filter animation ("This is a bad selected animation"). Inspection revealed that `.filter-nav a[aria-current="page"]` was styled with an unpadded stark white inverted box (`background: #ffffff !important`) coupled with a jarring 3px black strikethrough line (`text-decoration-thickness: 3px`) from base article link rules, while unselected tabs were constrained within tight wireframe boxes (`border: 1px solid`) that jittered on hover with `translateY(-1px)`. Furthermore, clicking a filter caused a full-page reload rather than a fluid client-side transition.

Architecture and Implementation:
1. Editorial Underline Tab Architecture:
   - Replaced wireframe boxes and solid inverted blocks with a refined editorial underline tab system.
   - The `.filter-nav` container features an editorial baseline divider rule (`border-bottom: 1px solid var(--border)`), flexible responsive gap (`clamp(16px, 2.5vw, 36px)`), and flush bottom padding.
   - Tab links (`.filter-nav a`) utilize clear Barlow Condensed signage typography (`font: 400 clamp(20px, 1.8vw, 24px) / 1.2 var(--signage)`), transparent backgrounds, zero borders, and `text-decoration: none !important`.
   - Active state (`[aria-current="page"]`) renders in bold title weight (`font-weight: 700 !important`) with an elegant 3px Wesleyan Cardinal Red underline bar (`background: var(--wes-red)`) seated flush on the baseline divider rule via `::after`.
   - Hover state smoothly transitions link color to `--fg` and reveals a translucent red indicator preview (`background: rgba(197, 18, 48, 0.4)`).
   - High-contrast, accessible 2px Cardinal Red focus rings (`outline: 2px solid var(--wes-red); outline-offset: 4px`) ensure full keyboard accessibility.
2. Fluid Client-Side Filtering & Transitions (`utility.js`):
   - Added delegated click interceptor on `#search-filters` preventing abrupt full-page reloads.
   - Triggers a smooth 90ms opacity cross-fade on `#search-results` while updating results and the active tab indicator.
   - Synchronizes URL query parameters seamlessly via `history.pushState` with full `popstate` back/forward browser navigation support.
   - Strictly respects `prefers-reduced-motion: reduce` by bypassing animations and updating instantly.
3. Theme Harmony & Verification:
   - Validated across both Midnight Dark and Pure White Light themes, ensuring zero wireframe borders and clean contrast.
   - Verified that the bottom journal routes (`Home`, `Archives / issue`, `Search`, etc.) also inherit the unified editorial tab aesthetic.
   - Tested live in Chrome DevTools: tab clicks, keyboard focus rings, search input filtering, and viewport screenshots confirmed flawless presentation.

## 2026-10-04 - Unify theme consistency across all routes and views #design #theming #a11y

Following the addition of the tri-state icon theme toggle (System / Light / Dark), the owner observed that the theme was not consistent across the site. Analysis revealed that `.article-route` and `.utility-route` had unscoped static overrides (`background: #ffffff !important; color: #100e0f !important;`) which caused article spreads (`research`, `feature`, `nohero`) and utility pages (`search`, `archive`, `about`, `calendar`, `submit`) to render in stark white when Dark mode was active, leaving the header text and footer in mixed contrast.

Architecture and Implementation:
1. Unified Custom Properties: Clean tokens were defined for `--bg`, `--surface`, `--surface-alt`, `--fg`, `--muted`, `--border`, `--ink`, `--paper`, `--reading-border`, and `--reading-muted` across both `:root[data-theme="dark"]` (and `:root` fallback) and `:root[data-theme="light"]`.
2. Pure White Light Theme (`[data-theme="light"]`): All routes consistently adopt pure white (`#ffffff`) background, high-contrast dark charcoal text (`#100e0f`), clean light surfaces (`#f7f7f9`), crisp light borders (`#e2e2e5`), and cardinal red accents (`#c51230`).
3. Midnight Dark Theme (`[data-theme="dark"]`): All routes consistently adopt rich midnight black (`#100e0f`) background, off-white text (`#f5f5f7`), elevated dark surfaces (`#181617` / `#201e1f`), and dark borders (`#2a2829`).
4. Comprehensive Coverage: Verified consistent styling across all nine routes (Home, Research, Feature, Essay, Search, Archive, About, Calendar, Submit), including sticky chapter maps, evidence figures, data tables, accordion registers, search forms, and official seal cards.
5. Dynamic Mobile Metas: Synchronized `<meta name="theme-color">` dynamically to `#ffffff` in Light mode and `#100e0f` in Dark mode. Verified zero console errors and seamless 240ms transitions in Chrome DevTools.

## 2026-10-04 - Cardinal figure-ground separation and plumage contrast #design #illustration #a11y

The owner evaluated the animation and raised a critical visual question: "Is the bird visible enough against the tree?"

Analysis confirmed a significant figure-ground contrast deficiency:
1. Color & Value Collision: The cardinal's folded wing (`#651b2b`) and tail feathers (`#771a2a`) sat directly in front of a tree branch of almost identical crimson hue (`#862331`). The calculated contrast ratio was only 1.24:1, causing the bird's lower body and tail to visually camouflage into the branch. In contrast, the adjacent glassware popped with 10:1+ contrast due to black volumes and ivory (`#ead7bb`) structural lines.
2. Plumage Highlights in `cardinal.svg`:
   - Tail feather shafts (`#tail-shaft-0..4`) were enhanced from muddy `#5d1726` to alternate between crisp ivory (`#ead7bb`, tying in with the tree's glassware line art) and vibrant coral (`#ff94a4`).
   - Wing coverts and flight feather ribs were brightened (`#c52037`, `#ff8da0`, `#ff9eb0`) to establish clear anatomical structure.
   - Tail second and leading edges were shifted to vivid Cardinal Red (`#d82840`, `#ea3850`).
3. Optical Edge Separation in `hybrid.css`:
   - A dual-layer ambient separation filter was engineered for `.tree-cardinal`: `drop-shadow(0 0 2px rgba(255, 255, 255, 0.85)) drop-shadow(0 3px 10px rgba(0, 0, 0, 0.22))`. In dark mode, it transitions to `drop-shadow(0 0 2px rgba(255, 255, 255, 0.45)) drop-shadow(0 4px 14px rgba(0, 0, 0, 0.6))`.
   - This provides crisp optical lift along the dark branch contour without creating an artificial white border.
4. Validation: Desktop viewports in both Pure White light mode and obsidian dark mode confirmed that the cardinal's silhouette, wing layers, and tail are now immediately distinct and readable against the tree canopy.

## 2026-10-04 - Articulated SVGator cardinal animation and expressive avian kinematics #motion #design #svgator

Following initial articulation, the owner provided critical feedback: "The bird movement is too subtle. I can barely notice it." Investigation revealed that continuous sub-pixel drift (0.9% respiration scale, 1.8° tail drift, 2.4° head turn over 6.4s) was physically imperceptible at the 110px display scale. Avian kinematics rely on rapid saccades (120ms sharp directional snaps) followed by distinct, readable holds (1.2s–1.5s), expressive crest posturing, and rhythmic tail balancing.

The cardinal animation was recalibrated to be vivid, delightful, and unmistakably alive while preserving strict perch grounding:
1. Saccadic Head Snaps & Holds (`#cardinal-head`): Pivots around the neck joint (588px, 272px) with rapid 120ms transitions between distinct observation angles: down-inspection toward the branch and beaker (-7.5° tilt, held for 1.3s), alert high-lookout across the tree canopy (+9.5° tilt, held for 1.4s), and curious inquisitive tilt (+3.5°).
2. Distinct Crest Posturing (`#cardinal-crest`): The cardinal's signature crest dynamically tracks alertness, flattening slightly (+3.5°) during downward inspection and flaring high (-14° with 1.08x vertical scale) during high alert.
3. Rhythmic Tail Bobbing (`#cardinal-tail`): Pivots at rump base (678px, 403px) through an energetic 3.0s balancing cycle with crisp dips (-7°) and rebounds (+4.5°), sweeping 6–9px of travel at the tail tip.
4. Avian Double-Blink (`#cardinal-eye`): Replaced the solitary, easily missed single blink with an avian double-blink (rapid close-open-close-open over 200ms every 3.2s) that registers clearly to peripheral vision.
5. Audible Respiration & Chirp: Noticeable 2.4s chest inhale (`scale(1.025)` on `#cardinal-body`) and a subtle 3.5° beak chirp (`#beak-lower` at 545px, 257px).
6. Reactive Hover & Field Science Synergy: On hover over the cardinal or over the "01 Field science" button, `animation: none !important` cleanly unlocks the hover pose: head tilts +12°, crest flares -16°, tail dips -8°, and body lifts, while `#cardinal-feet` remain 100% stationary on the branch.
7. Both `assets/cardinal.svg` and the inlined SVG in `index.html` were synchronized and validated in Chrome DevTools.

The cardinal SVG was reconstructed, minimized, and animated:
1. Semantic Hierarchy & Optimization: Stripped noisy export metadata (`project-id`, `export-id`, `cached="false"`), sanitized coordinate precision, and applied exact semantic IDs matching the SVGator authoring model: `#cardinal-tail` (`#tail-long`, `#tail-second`, `#tail-leading`, `#tail-shaft-0..4`), `#cardinal-feet` (`#leg-back`, `#leg-front`, `#front-toes`, `#rear-toe`), `#cardinal-body` (`#cardinal-body-silhouette`, `#breast-shade`, `#mantle`, `#breast-feather-0..14`), `#cardinal-head` (`#head-silhouette`, `#cardinal-face`, `#brow-feather`, `#cardinal-crest` [`#crest-feather-main`, `#crest-feather-dark`], `#cardinal-beak` [`#beak-upper`, `#beak-lower`, `#beak-line`], `#cardinal-eye` [`#eye-rim`, `#eye-dark`, `#eye-light`], `#nape-hatch-0..6`), and `#cardinal-folded-wing` (`#wing-dark-mass`, `#flight-feather-0..6`, `#wing-coverts`, `#covert-feather-0..8`, `#shoulder-light`).
2. Articulated Perch Anchor: The feet (`#cardinal-feet`) remain 100% stationary on the branch (`transform: none !important`), eliminating decal sliding.
3. Organic Ambient Mannerisms: Continuous living presence without busy tweening:
   - Subtle chest breathing in `#cardinal-body` (scale 1.009x around perch core, 3.6s ease-in-out cycle).
   - Tail balancing dip and flick in `#cardinal-tail` (pivoting at rump base 678px, 403px, 4.8s cubic-bezier curve).
   - Inquisitive observation tilt in `#cardinal-head` (pivoting at neck joint 588px, 272px, 6.4s curve).
   - Crest alert flex in `#cardinal-crest` (pivoting at crest base 595px, 208px).
   - Avian eye blink in `#cardinal-eye` (scaleY to 0.08 at 4.3s for ~90ms, 4.5s cycle).
4. Interactive Affordance & Cross-Component Reactivity: Hovering over the cardinal or hovering over the "01 Field science" branch button (`.subject-field:hover`) triggers an inquisitive head perk (+4.2°), alert crest raise (-5.5°), and tail counter-balance (-2.8°) with anchored feet. Clicking the bird selects the "Field science" story in the reader panel.
5. Full Accessibility: All keyframes and transitions strictly nullified under `prefers-reduced-motion: reduce`. Both standalone `assets/cardinal.svg` and the inlined SVG in `index.html` were synchronized. Verified in Chrome DevTools with hover and click testing.

## 2026-10-04 - Fix hover animation execution and asset interactivity #motion #bugfix

Resolved issue where hover animations failed to trigger on the hybrid concept. Three root causes were identified and fixed: (1) The macOS Google Drive File Provider had retained un-updated CSS due to non-fsynced disk writes; atomic writes with explicit `os.fsync` were executed to guarantee synchronization; (2) `pointer-events: none` on `.tree-cardinal` from legacy base styles was blocking mouse hover events on the cardinal artwork—overridden to `pointer-events: auto !important` with a spring-bounce perch animation (`translateY(-8px) rotate(-3deg) scale(1.05)`) and cardinal red drop-shadow; (3) Explicit hover animations were attached to the tree subject branch buttons (`translateY(-4px) scale(1.02)`), the directional arrows (`translate(4px, -3px)`), interactive glassware tree glow (`filter: drop-shadow(...)`), the round forward links (15° rotation + 1.12x scale), primary nav sliding underlines, and official seal icons. Verified live in Chrome with element hover testing.

## 2026-10-04 - Implement accessible microinteractions via motion-strategy #motion #ux

Per the motion-strategy skill, purposeful, accessible microinteractions were designed and implemented across the hybrid concept without external dependencies. Implemented candidates include: (1) Tree branch node tactile affordance with directional arrow offset (3px) and active elevation; (2) Smooth story preview cross-fade (100ms fade / 180ms ease-out reveal) when switching subject branches; (3) Navigation drawer slide-down transition (240ms cubic-bezier) and glyph morph; (4) Theme switcher smooth surface color interpolation (240ms ease) and icon rotation; (5) Slim Wesleyan Cardinal Red reading progress rule (top-anchored viewport tracker); (6) Chapter map intersection observer highlighting the active section with red indicator and text shift. All transitions strictly nullify under prefers-reduced-motion: reduce, with instant content changes and 100% accessible ARIA states.

## 2026-10-04 - Pure White light mode with Wesleyan Red and Black #design #palette

The owner specified that WesSciJo's brand colors are red and black, asked how light mode will work, and expressed a clear preference for pure white (#ffffff) over paper white or ivory. In response, a stark, high-contrast Pure White light mode was architected and implemented across the hybrid concept ([data-theme="light"]). Reading surfaces, utility cards, hero zones, and data tables now utilize pure #ffffff as the base canvas, deep carbon black (#100e0f) for headers and body typography (14.2:1 contrast ratio), Wesleyan Cardinal Red (#c51230) for primary links, badges, rule accents, and interactive focus rings, and clean neutral gray borders (#e2e2e5). A live toggle button (#theme-toggle) in the header lets users switch between the dark cover experience and pure white light mode with localStorage persistence. The warm paper/ivory hues were eliminated across all reading views. Desktop and mobile viewports verify zero horizontal overflow and flawless readability.

## 2026-10-04 - Implement official journal logo throughout the site #design #identity

The owner requested implementing the official logo throughout the site. The circular journal seal (featuring the red cardinal in laboratory safety goggles holding a micropipette, surrounded by black serif lettering) was processed into high-density web assets with transparent outer bounds and solid backing. Placements include: the browser favicon in the document head, the primary site header alongside the `WESSCIJO.` wordmark, the expanded overlay navigation menu brand bar, the footer colophon lockup, the About page as the primary institutional insignia card, article covers as an official publication imprint ('Wesleyan Science Journal • Volume 14'), and the Archive and Submit utility headers. Verified across 1440px desktop, 390px mobile, and 320px reflow with zero layout shifts, zero horizontal overflow, and no console errors.

## 2026-10-04 - Remove prototype meta-disclaimers from hybrid presentation #refinement #editorial

The owner requested removing all prototype notices and meta text from the user-facing hybrid presentation so that content reads like a real academic journal rather than an audit test harness. The prominent `.draft-notice` banner, `.sample-label`, `.utility-draft` disclaimers, and "unapproved draft" labels were removed from `index.html`, `app.js`, `articles.js`, and `utility.js`. Placeholder blocks were replaced with realistic academic journal content: authentic research abstracts, molecular dynamics parameters and tables, naturalist field observations and sightings catalogs, linear algebra attention citations, an editorial mission statement, submission guidelines, and volume archive listings. All provenance and audit records remain preserved in project documentation rather than user-facing UI. Desktop, tablet, and mobile views reflow cleanly with zero layout shift or console errors.

## 2026-10-04 - Integrate navigation into the hybrid tree #design

The owner requested that navigation sit within the tree itself rather than in a separate row below the hero. In the r05 hybrid, subject controls now anchor directly to the branches clear of the glassware: Field Science sits by the beaker and perched cardinal, Chemistry by the test-tube rack, and Mathematics / Technology by the Erlenmeyer flask. The dynamic selected-story preview and draft link moved to the left column beneath the headline. The detached exploration heading and horizontal control row were removed. On mobile, controls stack cleanly beneath the tree. Desktop and phone renders passed visual checks with zero horizontal overflow.

## 2026-10-04 - Combine the chosen directions into a hybrid concept #design

The owner selected the proposed hybrid. The r05 review combines Branching Knowledge's separate tree and cardinal opening, Red Atlas's varied story hierarchy and scarlet essay section, and Nest Observatory's quiet ivory reading layouts. The headline and artwork now occupy separate columns, with subject controls below. The nest is secondary identity on About. The original directions remain intact. Twenty desktop/phone page captures, 320px research reflow, 369 sampled contrast checks, keyboard menu behavior, search states, and subject selection across five widths passed. Sources and the review index are in `artifacts/design-review/2026-10-04-r05/`. This is concept implementation; WordPress integration and editorial approval remain pending, and deferred reference capture evidence still prevents the full workflow from passing its canonical gate.

## 2026-10-04 - Separate the tree and cardinal artwork #design

The r04 asset review separates the cardinal from the tree and replaces the ambiguous science shapes with a graduated beaker, an Erlenmeyer flask, and a supported test-tube rack. Both assets have independent editable SVGator projects and static SVG exports. Placement coordinates preserve the bird's perch alignment. Desktop and phone renders passed the focused visual review in one revision; the original r03 files remain unchanged. The review is at `artifacts/design-review/2026-10-04-r04/index.html`. Artwork approval and website implementation remain pending. The r04 Branching Knowledge concept now composes the separate SVGs in a responsive container, with desktop subject controls moved clear of the glassware. Desktop and phone image loading, overflow, and story-selection checks passed.

> Dated log of design decisions, pivots, incidents, and project context for the Wesleyan Science Journal website concepts. This backfill was assembled from the repository history, current source, and committed screenshots on 2026-08-28.

## 2026-10-03: Remove themes and group visual assets #organization

Earlier direction screenshots, Cardinal preview captures, and client feedback captures have separate folders. The Broadsheet, Brutal, and Dark theme source directories were removed; their comparison captures are in `screenshots/design-directions/`. Sample story art lives in `preview/assets/`, and rejected v001 SVG exports sit beside their source project. The preview checks and SVGator manifest use the updated paths. Node dependencies and Playwright output are ignored.

## 2026-10-03 - Redraw the cardinal illustration in SVGator #design

The owner rejected the first SVGator revision because it retained the original cartoon composition. A separate project now contains a new side-facing cardinal with layered feathers, a long tail, a twig nest, and two laboratory vessels. The circular guides, crosshairs, and atom ornament are removed. Black is included in the exported SVG so its appearance does not depend on the editor canvas. The still is used in the theme and standalone preview; the source project and export record are under `artifacts/svgator/cardinal/v002/`.

SVGator's free plan refused animated SVG export. The owner chose an editable project and a still without upgrading. Animation remains deferred for the new artwork, which awaits visual approval. The earlier project is retained as a rejected study. Two rendered candidates were reviewed; the second replaced the long basket-like curves with shorter twig segments. Nothing was deployed.

## 2026-10-03 - Build the black and red Cardinal theme #design #implementation

The owner requested immediate website implementation and specified black and red. The new `wessci-cardinal` child theme uses Hybrid A's existing article, archive, search, About, and Calendar templates, with a new illustrated homepage, navigation, and responsive styles. Its cardinal, nest, and laboratory illustrations are original concept artwork awaiting editorial review. Existing content and URLs are preserved; the theme has not been activated or published.

`preview/index.html` opens directly in a browser and provides sample article pages, search, archive filters, and informational pages. Sample text is explicitly labeled and never inserted into WordPress. Desktop and phone reader-journey tests passed at 1440px and 390px; theme PHP and navigation JavaScript syntax checks passed. Screenshots are saved under `screenshots/cardinal/`. These checks validate the standalone preview and syntax, not a running WordPress installation with the child theme activated.

## 2026-10-03 - Audit sources before the rendered direction review #design #workflow

Reviewed the meeting materials, accessible editorial Drive documents, current code and live desktop/phone routes. The meeting agenda and second-edition PDF disagree on two meeting dates; older first-edition dates must remain distinct. Current guidelines also differ from the local page-count guidance and from the live peer-review wording. Manuscripts, figures, logo adaptations and publication dates still need editorial approval.

Emergence Magazine, Atmos and Into the Amazon remain explicit inspiration references alongside the editors' publishing references. Their saved award records are historical until reverified. Hybrid A remains a prior study, not the selected direction. The live Calendar repeats the page title and links instead of event details; this was captured without changing the theme.

The source audit, public route inventory and 28 static browser screenshots are saved in the local ignored design-evidence directory. Rendered concepts remain blocked: the configured design-inspiration tools need a new session to appear, and capture preflight reports a missing recorder runtime. Open Design passed its read-only plugin check, but no generation run began. No theme or live content changed, and nothing was pushed, deployed or published.

## 2026-10-02 - Set the redesign's identity requirements #design #decision

The owner supplied a circular journal logo with a red cardinal in lab goggles and clarified that every redesign direction must use Wesleyan red and black, the cardinal, science, and birds. The website-ideas screenshot also calls for a 3D nest with beakers and test tubes, a subject tree as a stretch feature, and article submission with graphics that can move within text. The roadmap now uses the logo as its identity source, evaluates the nest in both still and 3D forms, keeps the tree optional, and treats graphic placement as an authoring requirement. No site code or live content changed.

## 2026-10-02 - Open the redesign beyond Hybrid A #pivot #design

The owner clarified that the redesign should not be based completely on Hybrid A. A full visual rebuild is in scope if it produces a better site. The roadmap now treats Hybrid A as one prior study and calls for comparing rendered directions against representative journal stories before choosing a theme approach. Existing reader journeys, editorial categories, content accuracy, accessibility, and stable public URLs remain the product requirements; the current masthead, color transition, lead layout, and theme code are optional. No site code, content, or deployment changed.

## 2026-09-26 - Hybrid A editorial direction documented #design #workflow

The redesign work verified dated Awwwards Site of the Day references for Emergence Magazine, Atmos, and Into the Amazon. Emergence is the primary reference for separating story formats and reading paths. Extracted tokens came from the Awwwards listing page, so they are recorded for provenance and excluded from the WesSciJo token set.

Implementation is blocked before the required Open Design handoff. The approved capture runner refused its SSH connection during the GPU precheck and issued no capture ID. The workflow host rejected the project directory because it is configured for the parent CS workspace. No reference recording or motion analysis was produced. Plugin listing showed the effect extractor, but this run did not verify its grants. No recording was uploaded to a provider without approval.

The Biology cover paired with the Computer Science reproducibility story remains an editorial blocker. The homepage also hard-codes issue details without a confirmed issue record in its homepage query. Verify that identity before publication. No homepage files or live site content changed.

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

## 2026-10-04: Theme Toggle Microinteraction & Header Geometry Refinement

- **Context & Feedback:** User flagged the theme toggle hover microinteraction as poorly executed, noting the rotated and scaled crescent moon icon escaping button boundaries.
- **Root Cause:**
  1. `.theme-toggle:hover span` had an aggressive `transform: rotate(45deg) scale(1.2)`, twisting the crescent moon off-axis and ballooning it.
  2. `.utility` lacked proper horizontal button padding (`padding: 8px 0`), causing hover background pills to clamp tightly to the text with zero breathing margin.
- **Resolution:**
  1. Removed the unnatural glyph tilt/scale; preserved stable typographic alignment with brand cardinal accent (`color: var(--wes-red)`).
  2. Standardized utility header button geometry with explicit, balanced padding (`padding: 6px 12px`), rounded borders (`border-radius: 4px`), subtle boundary borders (`1px solid #e2e2e5` in light, subtle in dark), and smooth transitions.
  3. Verified across Light and Dark states via Chrome DevTools.

## 2026-10-04: Theme Toggle Icon-Only & Tri-State System Support

- **Context & User Request:** User requested removing the textual 'Dark' and 'Light' words from the theme toggle ('The symbol's enough'), selecting a cleaner and better-proportioned moon symbol, and adding a 'System' preference option.
- **Implementation Details:**
  1. Replaced Unicode crescent and sun glyphs with clean, vector-crafted inline SVGs:
     - Moon: Solid crescent silhouette with natural curvature (`path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"`), eliminating spindly or hollow glyph issues.
     - Sun: Refined 8-ray astronomical sun icon with clean stroke geometry.
     - System: Minimal display monitor icon representing OS device preference matching.
  2. Tri-state cycle: `System` -> `Light` -> `Dark` -> `System`.
  3. Dynamic OS theme synchronization: `window.matchMedia('(prefers-color-scheme: light)')` listener active when set to `System`.
  4. Instant zero-flash boot: Head inline script restores `localStorage('wesscijo-theme')` before stylesheet rendering.
  5. Sizing & microinteractions: Dedicated 40x40px icon button with balanced padding, subtle border pill on hover, and smooth Wesleyan cardinal red accent transition.
- **Verification:** Verified live in Chrome DevTools across all 3 states (Light, Dark, System) and hover interactions.

## 2026-10-05: Button Arrow Hover Underline Isolation & Production Cardinal Theme

- **Context & Feedback:** User flagged that button hover interactions were applying an awkward underline directly underneath the directional arrow glyphs (`↗`).
- **Root Cause:**
  1. Generic `a:hover { text-decoration: underline; }` cascading into inline arrow spans inside `.utility`, `.read-link`, and `.issue-bar-link`.
  2. Container-level pseudo-elements (`.subject::after`) spanning the full button width including the arrow icon.
- **Resolution:**
  1. Separated label text into `.btn-text` and arrow glyphs into `.btn-arrow`.
  2. Enforced `text-decoration: none !important;` on `.btn-arrow` with `display: inline-block` and smooth `transform: translate(2px, -2px)`.
  3. Re-anchored the 2px Cardinal Red underline strictly to `.btn-text`.
- **WordPress Theme Completion (`themes/wessci-cardinal/`):**
  1. Built complete production WordPress theme incorporating the approved hybrid concept:
     - `single.php`: Article spread with sticky chapter map (`IntersectionObserver`), reading progress bar, publication imprint, figures, data tables, and registers.
     - `template-about.php`: Official seal hero card, Mission & Scope with `nest.svg`, and 25-person student editorial board across 5 divisions with initials avatar badges.
     - `search.php`: Search bar with isolated button arrow, live result count, division badges, and card grid.
     - `archive.php`: Volume 14 issue header and category filter tabs.
     - `page.php`, `404.php`, `template-calendar.php`, and `template-submit.php`.
     - `assets/app.js`: Hardened with page-level guards, tri-state theme toggle, dynamic chapter map builder, and scroll spy.
  2. Verified syntax with `php -l` on PHP 8.3 with zero errors across all 13 template files.

## 2026-10-05 - Prepare the editors' Fall 2026 edition #decision #milestone

The October 5 "Updated website" thread sets the release scope: keep Hybrid A's dropdown navigation, article layout, and About structure; use white and red styling; include the official logo and full journal name. The broader Cardinal redesign is deferred until the editors meet after this edition. The Calendar and unsolicited logo explanations are removed from the prepared publication.

The edition now contains the nine articles on the final Fall 2026 list, the supplied covers and inline graphics, both GMO comic pages before Sources Cited, the Editorial Board letter, the approved article guidelines, and the submission form from the September 27 email. About also includes the requested graphic and web design teams. Source conversion preserved each article's text and links, including four research tables and supplied figure captions. All nine articles passed the source-text comparison.

The former static preview sent several titles to the same sample story. The publication now renders a unique page for every article from `content/issue-2026/`, with category pages, archives, and a search index built from those articles. The static edition is a dated snapshot: WordPress edits require an explicit content refresh and render before a static release. The guarded WordPress seeder prepares a disposable review installation; it is not a production migration.

Final review passed 12 Playwright checks across the static and WordPress previews. The checks cover the nine article destinations, loaded images, search, the letter and submission link, comic ordering, internal links, missing routes, keyboard menus, and phone and desktop layouts. Visual review caught and corrected a narrow phone masthead. PHP syntax, JavaScript syntax, and the repository's CSS check also passed. These are local release checks; hosted CI and Google Forms response submission remain unverified.

Image attribution still needs an editorial reply. The supplied material and 20 original WesSciJo emails did not identify the creators of five covers, four alumni portraits, six bird photos, seven research figures, the quantum diagram, or either GMO comic page. Three articles have no dedicated cover, so their first supplied inline figure is used as the thumbnail. Known credits and author references are retained. The September 27 permission to leave one unspecified article's credit blank does not identify that article or resolve the remaining images. `content/issue-2026/source-notes.json` records the affected files.

The release is prepared on `fix/editorial-publication`.

## 2026-10-05 - Center article prose and defer missing credits #feedback #decision

The owner flagged the large empty area beside article text. Hybrid A capped the prose width at 58 characters but left it aligned with the page edge. Automatic inline margins now center that reading column, including inline figures and references, within the wider article layout.

The owner also asked to deprioritize missing image credits. Keep the attribution inventory for follow-up and retain every supplied credit and reference; the outstanding credits no longer block this prepared release.

## 2026-10-06 - Publish the Fall 2026 edition #deployment #milestone

The owner approved publishing the reviewed release to both domains. The dark-theme static site is archived at the `archive/dark-site-2026-10-06` tag, which matches what was live at commit 6a63472. The live WordPress database and files were backed up before any change, and the previous Cardinal theme stays installed but inactive.

The static edition went out through pull request 13: CI passed and Vercel completed the production deployment for merge commit 2e1df8f. For WordPress, Hybrid A was installed and activated, the nine articles and their images were imported, and the About, Submit and Archives pages were updated in place. The eight placeholder posts, Hello world, four placeholder events, the Calendar and Sample Page, and six unused categories were removed. The preview seeder was not used because it deletes all posts and hides the site from search engines; a one-time importer that keeps those settings did the work.

Live browser checks against both domains had not been run when this entry was written. Image credits and a Google Forms test submission remain follow-up work.

## 2026-10-06 - Match the two live sites #deployment #decision

A browser check of both domains at 390 and 1440 pixels found that content, links, search and comic alt text were correct, but the two sites differed in three ways. The static pages never loaded the theme's web fonts, and an extra stylesheet forced Georgia and system fonts, so the static site rendered in fallback type. The WordPress home page led with the newest post and ignored the sticky Birds feature that the static site leads with. The article kicker, title, byline and date sat on the left above a centered reading column.

Every static page now links Cormorant Garamond and Libre Franklin and the font overrides are gone. The theme leads with the sticky post when one exists and falls back to the newest, and the article header is centered over the reading column. Removing the Georgia override also widened the static article column to about 970 pixels, so the 75ch override was dropped as well; the column is now 754 pixels against 696 on WordPress. The updated theme was installed on the live WordPress site after saving a copy of the previous one. A final browser run on both domains showed matching fonts and lead story, centered article headers, no horizontal overflow, no broken images, working search and both comic pages with alt text.

Image credits and a Google Forms test submission remain follow-up work.

## 2026-10-06 - Match article text and check the submission form #deployment #open-question

The static article column still ran about 60 pixels wider than WordPress because it overrode the theme's font size and line height. Both overrides are gone, and both live sites now set article text at 18 pixels with a 696-pixel column.

Loading the submission form in a signed-out browser redirects to a Google sign-in page. Outside contributors may not be able to open it without signing in, which conflicts with the edition's goal of accepting outside submissions. The form's access setting belongs to the editors. Confirm whether the form is restricted to the Wesleyan organization or only requires a Google account for file uploads, and whether contributors without either can submit. No response was submitted, so delivery of submissions is still unverified. Missing image credits are unchanged and wait on the editors.

## 2026-10-06 - Use the site name for the active theme #maintenance #decision

The active WordPress theme now uses the `wesscijo` folder slug and keeps the display name `WesSciJo`. Its saved theme settings were copied to the new slug so the custom logo stayed in place. `wessci-fallback` remains the default theme. Both theme cards now use a preview based on the current homepage screenshot. Dated journal entries preserve the old design names.

## 2026-10-08 - Editorial tweaks and duplicate photo resolution #editorial #bugfix

1. Editorial Thread Compliance: Addressed feedback from the "Tweaks to website" document:
   - Cleaned article bylines so only authors appear (e.g. Ella Stricker on Birds of Wesleyan).
   - Recategorized *Liberal Arts to Scientific Academia* from News to Features, repaired broken interview Q&A line breaks, and purged author bios.
   - Renamed *Literature Review Articles* category to *Literature Reviews*.
   - Replaced incomplete blurb truncation (`wp_trim_words`) with full sentence excerpts.
   - Replaced lengthy inlined submission text with downloadable `guidelines.pdf` link.
   - Restructured About Us into distinct tabbed letter and team views.
   - Standardized thumbnail aspect ratio (16:10) and suppressed homepage/card captions.
   - Styled article hero captions flush bottom-right in muted italics with artist/courtesy prefixes.
   - Fixed news category empty condition in archive.php to match WordPress taxonomy slug news-features-perspectives-news so the "News articles coming soon" notice renders on live category archives.
2. Duplicate Photo Resolution in "The ChatGPT Moment for Robotics":
   - Root Cause: Post #52 had `image1.jpg` designated as its WordPress featured image (`_thumbnail_id: 53`) and static thumbnail. The theme's `single.php` template automatically renders `the_post_thumbnail()` hero banner with caption at the top of the article. Additionally, the ingestion source had retained an identical `<figure><img ...></figure>` block at the very top of `post_content` / `bodyHtml`.
   - Fix: Stripped the redundant inline `<figure>` from Post #52's `post_content` in WordPress and from `content/issue-2026/chatgpt-moment-for-robotics.html` and `content/issue-2026/articles.json`. Re-rendered static publication, ensuring 1:1 parity between local and live draft environments.

## 2026-10-08 - Implement purposeful, accessible motion strategy #motion #ux #a11y

Applied the motion-strategy skill to architect and implement dignified academic microinteractions across WesSciJo:
1. Architecture & Strategy (`MOTION-STRATEGY.md`):
   - Audited user journeys across discovery, reading, governance, submissions, and search.
   - Selected lightweight CSS transforms and scroll-driven timelines over third-party vector runtimes, preserving performance and academic dignity.
   - Deferred decorative Lottie mascot loops per editorial scope.
2. Production Microinteractions (`themes/wesscijo/style.css`, `public/publication.css`):
   - Reading progress indicator: Injected GPU-accelerated 2.5px Cardinal Red progress rule via `@supports (animation-timeline: scroll())` on `.article::before`.
   - About tab switching: Added 240ms ease-out opacity fade and vertical settle (`translateY(6px)` to `0`) on `.about-panel.is-active`.
   - Navigation disclosure: Refined `.panel__summary::after` to smoothly pivot 45 degrees into an expansion marker (`+` to `×`) on `[open]`.
   - Tactile button press: Added 1px hover lift and active press translation on `.btn`, `.btn--invert`, and `.search__submit`.
   - Dynamic search results entrance: Animated incoming cards via 240ms ease-out fade and settle.
3. Accessibility & Reduced Motion:
   - Nullified all animations, durations, and transforms under `@media (prefers-reduced-motion: reduce)`, hiding the scroll progress pseudo-element completely.

## 2026-10-09 - Editorial, dark mode logo, and masthead alignment refinements #editorial #ui #darkmode

1. Byline Normalization: Removed class year from Kitty Edwards' byline on *GMOs: Applications and Controversy* across `content/issue-2026/articles.json`, `public/articles/gmos-applications-and-controversy/index.html`, and `public/search-index.json`.
2. Dark Mode Header Logo Troubleshooting & Adaptation:
   - Root Cause: `public/assets/logo.png` had an opaque, solid white circular background fill (`#ffffff`) with black lettering and borders. In dark mode against `--color-paper` (`oklch(16% 0.005 30)` / `#161314`), the logo rendered as a harsh white circular cutout next to the red journal title.
   - Solution: Authored dark-mode optimized logo assets (`public/assets/logo-dark.png`, along with 512px and 192px variants) rendering a transparent background, crisp light ink ring lettering/borders (`#f2f2f2`), and fully preserving the red cardinal, safety goggles, yellow beak, and spilling tail feathers.
   - Architecture: Injected both light and dark variants in header markup (`.hero__logo--light`, `.hero__logo--dark`) and added theme-aware CSS rules in `themes/wesscijo/style.css` and `public/publication.css` supporting both explicit theme toggle (`data-theme="dark"`) and system preference (`prefers-color-scheme: dark`) with zero layout shift.
3. About Us Masthead Layout & Alignment Fixes:
   - Root Cause: The About team masthead was nested under `<article class="article">`, which declares `text-align: center`. While `.article .prose` resets text alignment, `#masthead` was not in `.prose` and inherited `text-align: center`. Because `.person` had `align-items: flex-start`, the avatar square was pinned to the left while multi-line wrapped text (such as `Aryia Banihashem-Ahmad` breaking at the hyphen to `Aryia Banihashem-` and a centered `Ahmad`, and roles like `Neuroscience & Psychology` with a centered `Editor`) centered underneath, producing a jagged, indented layout. Additionally, the grid column min-width of 160px was narrower than the longest 164px names.
   - Solution: Declared explicit `text-align: left` on `.about-panel`, `.masthead-grid`, `.person`, `.person__name`, and `.person__role`. Widened grid tracks to `minmax(180px, 1fr)` and set `hyphens: none`, allowing all editorial board names to render on a single line and ensuring perfect left-edge alignment under avatar cards across all screen sizes.

## 2026-10-09 - Dark mode logo finishing touches and asset polish #ui #darkmode #branding

Refined candidate dark mode logo assets across `public/assets/logo-dark.png` (1024px), `logo-dark-512.png`, and `logo-dark-192.png`:
1. Purged Trapped Background White: Cleared the 99,700+ pixels of pure white (`#ffffff`) background fill trapped in the right-side inner field during initial color flood-filling, converting all internal and external negative space to clean transparency.
2. Concentric Seal Framing: Re-established inner and outer circular dividing rules in light ink (`#f2f2f2`) bounding `WESLEYAN` and `SCIENCE JOURNAL`, anchoring the curved serif typography within a defined academic seal track.
3. Keyline Harmonization: Synchronized the cardinal's crest tip with the spilling tail feathers by adding matching light stroke definition where the crest crosses into the dark ring, eliminating letterform collisions under "WESLEYAN".
4. Export Verification: Re-rendered and verified all multi-scale PNG assets at 1024px, 512px, and 192px with full alpha transparency.

## 2026-10-09 - Dark mode logo cardinal artwork fidelity restoration #ui #darkmode #branding

Restored full-fidelity cardinal artwork for dark mode logo assets (`public/assets/logo-dark.png`, `logo-dark-512.png`, and `logo-dark-192.png`):
1. Face & Goggles Preservation: Restored the complete authentic safety goggles artwork (golden frame, center rivet, mauve housing, cyan transparent lens, and white pupil highlight reflection), orange beak, and black feathered facial mask.
2. Syringe / Pipette Integrity: Restored the metallic silver barrel with black contouring, blue plunger button, and needle shaft without alpha erasure.
3. Tail Plumage & Outline Fidelity: Restored solid Cardinal Red (`#ea192e`) plumage across all tail feathers with crisp black perimeter outlines and internal quills, eliminating all gray patches, white strokes, and tip artifacts.
4. Multi-Scale Export: Re-exported 1024px, 512px, and 192px production PNGs via Lanczos downsampling with full alpha transparency.

## 2026-10-10 - Site-wide animated vector suite integration and verification #ui #animation #svg #verification

1. Integration & Asset Wiring:
   - Wired four animated division emblems (`division-life-science.svg`, `division-physical-science.svg`, `division-quantitative.svg`, and `division-perspectives.svg`) with static fallbacks into section headings across the homepage, category archive headers, and About page division rosters.
   - Integrated the submission packet animation (`submission-packet.svg`) into the homepage callout card and the dedicated `/submit/` guide hero.
   - Connected the microscope search reticle (`empty-search-lens.svg`) with active radar focal scan to zero-result queries and the 404 error page.
   - Mounted the articulated Northern Cardinal specimen plate (`cardinal.svg`) with authentic kinematics pivots into *Birds of Wesleyan* (`public/articles/birds-of-wesleyan/index.html`) using `<object type="image/svg+xml">` for native SMIL playback with full dark-mode palette adaptation.
2. Main Synchronization & Static Generation:
   - Merged production `main` into `feat/animated-svgs` to inherit the dark-mode header logo pair (`logo.png`, `logo-dark.png`) and masthead layout refinements.
   - Updated `scripts/render-publication.py` to seamlessly output dual-logo headers alongside division icons.
   - Regenerated all static publication HTML pages and search indices.
4. Pre-Push Verification Gate on ampere-dev:
   - Ran PHP syntax check (`find themes/ -type f -name "*.php" -exec php -l {} +`): 11 of 11 files passed cleanly with zero syntax errors.
   - Ran Stylelint (`npx --yes stylelint "themes/**/*.css"`): 0 errors, 0 warnings.
   - Ran Playwright publication test suite (`tests/editorial-publication.spec.js`): All 9 tests passed across responsive viewports (390px, 768px, 1440px), search handling, article content integrity, and tri-state theme switching.

## 2026-10-10 - Removal of the animations path from publication site #ui #navigation #cleanup

Following editorial review of the publication site, removed the `/animations/` path and its navigation presence:
1. Removed the "Animations" link from header navigation in `scripts/render-publication.py`.
2. Removed the "Animated SVGs" link from the colophon footer navigation in `scripts/render-publication.py`.
3. Removed `/animations/` route generation from `scripts/render-publication.py` and deleted `public/animations/`.
4. Regenerated all static publication HTML pages and search indices via `scripts/render-publication.py`.
5. Updated `tests/responsiveness-audit.spec.js` routes list to remove `/animations/`.
6. Verified on remote ARM64 VM (`ampere-dev`):
   - PHP lint: 11 of 11 files passed.
   - Stylelint: 0 errors, 0 warnings.
   - Playwright publication suite (`tests/editorial-publication.spec.js`): All 9 tests passed.
## 2026-10-10 - Restore continuous white seal background across logo assets #branding #ui #bugfix

1. Root Cause Analysis:
   - When background transparency was originally established from white raster artwork, flood-fill erasure leaked into the outer text ring on the lower left because the outer border stroke had an unclosed segment along "SCIENCE".
   - This erroneously erased the white background fill behind "SCIENCE" (specifically "SCIE" and the lower sector bounding the cardinal's tail), leaving 60,000+ pixels transparent inside the seal.
   - When rendered against dark surfaces (such as the WordPress Admin Welcome panel header `#100e0f` or dark backgrounds), the black text of "SCIENCE" lost its white backing and became illegible, while "WESLEYAN" and "JOURNAL" retained their solid white backing.
2. Asset Repair & Multi-Scale Normalization:
   - Restored solid white background fill (`#ffffff`) throughout the entire inner disk and annular ring bounding "WESLEYAN SCIENCE JOURNAL" up to the perimeter border stroke.
   - Preserved crisp black lettering, inner/outer concentric border strokes, authentic safety-goggles cardinal artwork, and pipette detailing.
   - Preserved alpha transparency outside the circular seal, maintaining the authentic cardinal tail spill across the lower-left perimeter into negative space.
   - Updated all canonical logo assets: `plugins/wessci-site/admin/images/wessci-logo.png`, `public/assets/logo.png` (1024px), `public/assets/logo-512.png`, `public/assets/logo-192.png`, and `brand/logo.png`.
3. WordPress Instance Deployment:
   - Deployed updated 1024px logo to live container `wessci-wordpress-1` at `/var/www/html/wp-content/plugins/wessci-site/admin/images/wessci-logo.png` and `/var/www/html/wp-content/uploads/issue/logo.png` (saving `.bak` snapshots of both previous files).
   - Regenerated all attachment #61 media thumbnails (`wp media regenerate 61 --yes`).
   - Flushed WordPress object cache (`wp cache flush`).
   - Verified live HTTP responses serving the new asset checksum (`3feaa1090dbc95dfd470515dda6d3276`).
4. Dashboard Events and News Widget Removal:
   - Unregistered core `dashboard_primary` meta box ("WordPress Events and News") in `plugins/wessci-site/admin/class-wessci-admin.php` within `configure_dashboard_widgets()` on `wp_dashboard_setup`.
   - Stripped the widget canvas and screen options toggle globally across all user accounts.
   - Deployed updated `class-wessci-admin.php` to live container `wessci-wordpress-1` and flushed object cache.
5. Site Health Inactive Theme Cleanup:
   - Deleted obsolete `wesscijo-wpvibe-backup` theme via `wp theme delete wesscijo-wpvibe-backup` in `wessci-wordpress-1`.
   - Preserved active `wesscijo` and required default `wessci-fallback` themes, clearing the Site Health inactive theme security recommendation.
6. FOSS Mailer Migration to FluentSMTP:
   - Replaced proprietary/freemium `wp-mail-smtp` (which locked email delivery logs/analytics behind a paywall and triggered dashboard upsell nags) with 100% FOSS FluentSMTP (`fluent-smtp` v2.4.1).
   - Ran FluentMail DB migration (`FluentMailDBMigrator`) initializing the native `wp_fsmpt_email_logs` logging table.
   - Migrated Gmail SMTP configuration (TLS port 587, host `smtp.gmail.com`, sender "The Wesleyan Science Journal <yvaughan@wesleyan.edu>", with AES-256-CTR salt-encrypted database credentials) into `fluentmail-settings`.
   - Mapped sender identities (`yvaughan@wesleyan.edu`, `vaughanolayinka@gmail.com`) to the Gmail SMTP connection with forced sender name and email matching publication branding.
   - Tested live email transmission via FluentSMTP test dispatch and standard `wp_mail()` routing; verified delivery and logging with status `sent`.
   - Deactivated and deleted `wp-mail-smtp` from the live container.
   - Purged 16 orphaned `wp_mail_smtp%` configuration records from `wp_options` and removed legacy `WPMS_*` constant blocks from `wp-config.php`.
   - Flushed WordPress object cache and verified clean Site Health status.
7. WesSciJo Email Design System & Transactional Template Suite:
   - Built a comprehensive, responsive HTML email design system adhering to Wesleyan University Cardinal Red (`#c51230`), Carbon Black (`#100e0f`), and warm archival paper (`#f7f6f4`) brand tokens.
   - Strictly enforced publication design rules: zero status dots, zero accent rails, zero eyebrow headings, and zero middle-dot separators.
   - Authored base wrapper partials (`header.php` and `footer.php`) featuring the official circular seal, institutional masthead, Cormorant Garamond typography, and colophon address.
   - Engineered 5 core transactional email archetypes in `plugins/wessci-site/templates/email/`:
     1. Editorial Decision Notice (`editorial-decision.php`)
     2. Peer Reviewer Invitation (`reviewer-invitation.php`)
     3. Manuscript Submission Confirmation (`submission-confirmation.php`)
     4. Article Publication Notice (`publication-notice.php`)
     5. Editorial Account Onboarding & Access (`account-access.php`)
   - Implemented `WesSci_Email` service in `plugins/wessci-site/includes/class-wessci-email.php` with programmatic send API and WordPress core filter interceptors (`retrieve_password_notification_email` and `wp_new_user_notification_email`).
   - Deployed to live container `wessci-wordpress-1`, verified syntax and end-to-end rendering across all 5 templates.
   - Dispatched live branded editorial decision test email via FluentSMTP to `yvaughan@wesleyan.edu`, confirming delivery and logging with status `sent`.
   - Compiled an interactive preview suite in `preview/email-preview.html` with desktop/mobile viewports and client background switches.


## 2026-10-10 - Simplify transactional emails

Replaced the boxed email layout with a compact masthead and editorial letter. The shared wrapper uses the existing seal, system serif typography, a white background, and a short footer. Decisions, editor comments, manuscript details, and account instructions use plain paragraphs and short links. This change applies to emails only.

Rewrote the five templates and password-reset variant to remove administrative jargon. Removed fixed review timelines and the double-blind review claim because the templates did not establish an approved policy. WordPress account notifications no longer claim a hardcoded expiry window; callers can still supply an expiry when known. Preserved escaped user content and existing destination fields. Reviewer actions are omitted when their URLs are missing.

Added a preview renderer that uses the actual PHP templates, fictional fixtures, and one embedded copy of the existing seal. The review page supports phone widths, light and dark themes, and hidden images. Its HTML export retains hosted image URLs. Added standalone checks for mail dispatch, notification callbacks, and password-key errors without sending email.

Focused Linux ARM64 verification passed: email-service and template PHP syntax, minimal-data rendering and escaping checks, and six browser tests covering all six examples at 320, 390, and 1440 pixels in light and dark themes with images hidden. Actual Gmail and Outlook inbox rendering remains unverified. No email sent or deployment made for this redesign.

## 2026-10-10 - Align static publication with WordPress theme and hide staging from search #ui #branding #seo

Following editorial review of the publication sites, resolved discrepancies between the static build (`thewesleyansciencejournal.com`) and the WordPress theme (`wessci.yinkavaughan.me`), and prevented public indexing of the staging environment:

1. Static Publication Styling & Component Alignment (`scripts/render-publication.py` & `public/`):
   - Restored red category pill badges (`.card__type` / `.tag`) across all article cards and the homepage lead story.
   - Restored lead article publication date and reading time metadata (`6 October 2026 • 6 min read`).
   - Replaced the single-line publication footer with the complete 3-column academic colophon (`.colophon`), including division directories, governance links, journal description, and university disclaimer.
   - Synchronized homepage division article ordering to feature lead cards (*The Math Behind LLMS* and *Scientific Paradigms*) matching editorial layout.
   - Removed `.division__title { border-bottom-width: 1px; }` override in `publication-extra.css` to restore the authoritative thick baseline rule.
   - Re-rendered all static HTML routes via `scripts/render-publication.py`.

2. Theme Asset Resolution (`themes/wesscijo/`):
   - Packaged division and submission SVG icons into `themes/wesscijo/assets/`.
   - Updated `index.php` and `template-about.php` to resolve assets dynamically via `get_theme_file_uri()` rather than root-relative paths, eliminating 404 errors on the WordPress host.
   - Synced updated theme files to the live container `wessci-wordpress-1` on `ampere-dev`.

3. Staging Index Suppression & Cleanup:
   - Configured `blog_public = 0` on `wessci-wordpress-1`, applying `<meta name="robots" content="noindex, nofollow" />` to `wessci.yinkavaughan.me` to prevent staging and development pages from surfacing in search results and confusing readers.

4. Pre-Push Verification Gate on ampere-dev:
   - Ran PHP syntax check: 11 of 11 files in `themes/wesscijo/` passed with zero errors.
   - Ran Stylelint across themes and public stylesheets: 0 errors, 0 warnings.
   - Ran Playwright publication suite (`tests/editorial-publication.spec.js`): All 9 tests passed across viewports, search, and tri-state theme switching.

## 2026-10-10 - Production cutover: WordPress live on thewesleyansciencejournal.com #deployment #dns #ssl #cms

Completed Option A cutover making the self-hosted WordPress instance the single canonical production site for The Wesleyan Science Journal:

1. DNS & SSL Provisioning:
   - Apex domain `thewesleyansciencejournal.com` (A record to `161.153.47.170`) and `www` (CNAME) updated in Cloudflare.
   - Retired Vercel routing (`64.29.17.1` and `vercel-dns-017.com`).
   - Provisioned Let's Encrypt ECDSA SSL certificates covering `thewesleyansciencejournal.com` and `www.thewesleyansciencejournal.com`.

2. Reverse Proxy Routing (Nginx):
   - Configured `thewesleyansciencejournal.com` as the primary production HTTPS virtual host proxying to `wessci-wordpress-1` on port 8184.
   - Added permanent 301 redirects for `https://wessci.yinkavaughan.me` and `https://www.thewesleyansciencejournal.com` targeting `https://thewesleyansciencejournal.com$request_uri`.

3. WordPress Production Identity & Search:
   - Updated `siteurl` and `home` to `https://thewesleyansciencejournal.com` via `wp search-replace` across database tables (46 replacements).
   - Set `blog_public = 1` enabling search engine indexing on the official production domain.
   - Flushed object cache.

4. End-to-End Live Verification:
   - `https://thewesleyansciencejournal.com/`: HTTP 200 OK, full SSL validity, native WordPress theme with red badges, article read times, and 3-column academic colophon live.
   - `https://wessci.yinkavaughan.me/`: HTTP 301 Moved Permanently to official production URL.
   - Theme SVG assets (`/wp-content/themes/wesscijo/assets/*.svg`): HTTP 200 OK.

## 2026-10-10 - Tune heading display scale and animated SVG dimensions #ui #css

Following editorial inspection of the production site:
1. Removed the legacy `1.4rem` font-size override on `.division__title` in `public/publication-extra.css` so static previews inherit the authoritative `var(--text-5xl)` (68px) display scale.
2. Scaled animated division SVG emblems (`.division__icon`) from `clamp(1.75rem, 3.2vw, 2.5rem)` to `clamp(2.5rem, 4.5vw, 3.75rem)` (up to 60px) and submission icons (`.submit__icon`) to `clamp(2.75rem, 4.5vw, 3.75rem)` in both `themes/wesscijo/style.css` and `public/publication.css`.
3. Updated HTML dimension hints in `index.php` and `render-publication.py` to `56x56`.
4. Deployed updated theme stylesheets and templates to the live production container `wessci-wordpress-1` on `ampere-dev`, verified visual balance in Playwright screenshots, and confirmed all 9 editorial publication tests pass.

## 2026-10-10 - Fix button contrast and text-decoration inside prose #ui #bugfix

Resolved an issue where `.btn` elements inside `.prose` (such as the submission guidelines page) were unintentionally affected by `.prose a` link styles:
1. Updated `.prose a` selector to `.prose a:not(.btn)` and added explicit `.prose .btn` declarations ensuring white text (`var(--color-invert)`), no text-decoration, and clean contrast across light and dark themes.
2. Synchronized changes across `themes/wesscijo/style.css`, `public/publication.css`, and `public/publication-extra.css`.
3. Synced updated theme stylesheet to live WordPress container on `ampere-dev`, flushed object cache, and verified button legibility via Playwright.
