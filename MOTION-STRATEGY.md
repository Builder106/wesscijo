# Motion Strategy & Inventory — The Wesleyan Science Journal (WesSciJo)

## Executive Summary

This strategy defines purposeful, accessible, and dignified motion for *The Wesleyan Science Journal* (WesSciJo). It balances the gravity of an academic publication with responsive, tactile interaction feedback. 

All motion adheres to the five core tenets of the `motion-strategy` skill:
1. **Purpose-driven**: Every animation clarifies an action result, state transition, or reading orientation. Zero purely decorative motion loops.
2. **Minimal format**: Lightweight CSS transforms and scroll-driven timelines are utilized. Heavy runtime dependencies (e.g. Lottie players, canvas libraries) are avoided to preserve page load speed and battery life.
3. **Restrained aesthetic**: Movements are crisp, short (120ms–240ms), and smoothly damped (`cubic-bezier(0.16, 1, 0.3, 1)`), reflecting Wesleyan Cardinal Red (`#a6192e`) and Carbon Black accents.
4. **Accessible by default**: Decorative animations are kept out of the accessibility tree, and all transitions strictly nullify under `prefers-reduced-motion: reduce`.
5. **Static parity**: Every feedback state retains 100% informational equivalence without motion.

---

## 1. Product Context & Surface Audit

WesSciJo is a hybrid science journal and magazine serving the Wesleyan University academic community. The publication features dense scientific prose, peer-reviewed figures, literature reviews, student research archives, and an editorial masthead.

### Key User Journeys
1. **Discovery & Exploration (Homepage & Archives)**: Browsing articles by academic division (*Research & Reviews*, *News, Features & Perspectives*), scanning categories, and inspecting article cards.
2. **Deep Academic Reading (`/articles/<slug>/`)**: Long-form engagement with dense scientific research (2,000–4,000 words), complex data figures, and citation registers.
3. **Governance & Masthead Inspection (`/about/`)**: Toggling between executive leadership statements (*Letter from the Editorial Board*) and division editorial rosters (*Our Team*).
4. **Manuscript Submission Journey (`/submit/`)**: Navigating submission requirements and downloading author guideline documentation.
5. **Literature Search (`/search/`)**: Querying the publication search index and receiving dynamically filtered article card results.

### Existing System Tokens
- **Transitions**: `--dur-fast: 120ms`, `--dur-mid: 240ms`, `--ease-out: cubic-bezier(0.16, 1, 0.3, 1)`.
- **Palette**: Paper (`#fff`), Carbon Ink (`oklch(12% 0 0)`), Wesleyan Cardinal Red (`#a6192e` / `#841424`), Neutral Hairline Rule (`oklch(86% 0.004 70)`).
- **Typography**: Display/Serif (`Cormorant Garamond`), Body/Interface (`Libre Franklin`).
- **Policy Restrictions**: Banned UI tropes (no flashing status dots, no accent rails, no middle-dot separators).

---

## 2. Motion Role & Format Selection

| Role | Target Behavior | Chosen Format | Rationale |
| --- | --- | --- | --- |
| **State Orientation** | Reading progress indicator | CSS Scroll-Driven (`animation-timeline: scroll()`) | Native GPU-accelerated scroll tracking without scroll event listeners or layout reflow. |
| **Context Switch** | About tab panel transition | CSS Opacity & Y-Transform | Eliminates abrupt layout snaps between editorial letter and team roster. |
| **Affordance & Expansion** | Navigation disclosure marker | CSS Transform (`rotate(45deg)`) | Replaces abrupt character swaps (`+` to `−`) with fluid glyph rotation. |
| **Action Feedback** | Button & submit tactile press | CSS `:active` Transform (`translateY(1px)`) | Provides immediate physical confirmation of click/tap. |
| **Content Arrival** | Dynamic search results entrance | CSS `@keyframes` Fade & Slide | Smooths client-side DOM insertion so results feel cohesive rather than jarring. |

### Evaluation of Lottie & 2D Vector Animations
- **Recommendation**: **Deferred**.
- **Rationale**: An academic journal's primary mission is legibility and rigor. The editors previously scoped out unsolicited decorative logo animations and 3D visual loops (recorded in `JOURNAL.md`). Introducing third-party Lottie runtimes or autoplaying mascot loops would degrade performance and distract from primary manuscript text. Vector SVG assets remain crisp and static.

---

## 3. Motion Inventory & Contracts

| Event and surface | User purpose | Priority | Format | Motion outline | Static contract | Technical fit |
| --- | --- | --- | --- | --- | --- | --- |
| **About Tab Switching**<br>`/about/`<br>`.about-panel` | Clarifies the switch between the Editorial Board Letter and the 25-person masthead without disorienting layout snaps. | **Now** | CSS | `opacity: 0 → 1`, `translateY(6px → 0)` over 240ms ease-out upon panel activation. | Instant display toggle (`display: block / none`). Nullified under `prefers-reduced-motion`. | Injected on `.about-panel.is-active` in `style.css` and `publication.css`. Unit & visual testable. |
| **Reading Progress Bar**<br>`/articles/<slug>/`<br>`.article::before` | Gives readers immediate visual orientation on reading progress through dense 2,000+ word scientific manuscripts. | **Now** | CSS | Slim 2.5px Cardinal Red rule anchored at viewport top. `scaleX(0 → 1)` linearly tracking scroll position. | Hidden when static or top of page. Completely disabled under `prefers-reduced-motion`. | Pure CSS `@supports (animation-timeline: scroll())` pseudo-element on `.article`. Zero JS overhead. |
| **Section Disclosure Marker**<br>Header nav<br>`.panel__summary::after` | Communicates expansion state intuitively by smoothly pivoting the disclosure glyph rather than abruptly jumping text. | **Now** | CSS | `transform: rotate(0deg → 45deg)` over 120ms ease-out when `<details>` opens. | Static `+` glyph. No animation under `prefers-reduced-motion`. | Styled on `.panel__summary::after` in `style.css` and `publication.css`. |
| **Primary Buttons & Download Action**<br>Global<br>`.btn`, `.search__submit` | Delivers immediate tactile confirmation on press, reinforcing outbound link or PDF download actions. | **Now** | CSS | Hover lifts 1px (`translateY(-1px)`), active press drops 1px (`translateY(1px)`) over 120ms ease-out. | Standard hover color change. Transform nullified under `prefers-reduced-motion`. | Styled on `.btn`, `.btn--invert`, and `.search__submit`. |
| **Search Result Ingestion**<br>`/search/`<br>`#search-results .card` | Softens result card appearance as the client-side search index filters articles, preventing jarring DOM pop-in. | **Now** | CSS | Cards smoothly fade in (`opacity: 0 → 1`, `translateY(6px → 0)`) over 240ms ease-out. | Cards render immediately at full opacity. Animation disabled under `prefers-reduced-motion`. | Scoped to `#search-results .card` in stylesheets. |

---

## 4. Accessibility & Reduced-Motion Contract

All motion rules are strictly governed by the following reduced-motion contract:

```css
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
    .article::before {
        display: none !important;
    }
    .card:hover .card__media img,
    .btn:hover, .btn:active,
    .search__submit:active,
    .panel[open] > .panel__summary::after {
        transform: none !important;
    }
    .about-panel.is-active,
    #search-results .card {
        animation: none !important;
    }
}
```

- **WCAG 2.2 Compliance**: Conforms to Success Criterion 2.2.2 (Pause, Stop, Hide) and 2.3.3 (Animation from Interactions).
- **Assistive Technology Safety**: The reading progress indicator uses `pointer-events: none` and is isolated on a pseudo-element. It does not obscure text or create accessibility tree noise.
- **Cognitive Hygiene**: No continuous loops, no ambient flashing, and no spatial bouncing.
