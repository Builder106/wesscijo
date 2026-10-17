# Motion strategy for The Wesleyan Science Journal (WesSciJo)

## Summary

This document sets the motion rules for the journal site and lists the animations that follow them. Motion should help readers understand an action or a change without interrupting reading. The site must work just as well when motion is reduced or unavailable.

Use these five rules:

1. **Make motion useful:** Use animation to show an action result, a state change, or reading progress. Skip decorative loops.
2. **Keep it light:** Prefer CSS transforms and scroll timelines. Avoid extra runtime libraries, such as Lottie players and canvas libraries, to keep pages light and limit battery use.
3. **Keep it restrained:** Keep transitions between 120 and 240 ms and use `cubic-bezier(0.16, 1, 0.3, 1)`. Use Wesleyan Cardinal Red (`#a6192e`) and Carbon Black for accents.
4. **Respect reduced motion:** Keep decorative animation out of the accessibility tree. Honor `prefers-reduced-motion: reduce` and remove transitions when it is active.
5. **Keep every state clear without motion:** Readers must get the same information when animation is off.

---

## 1. Product context and page review

WesSciJo is a student science publication for the Wesleyan community. Its pages include long-form articles, scientific figures, reviews, student research, and the editorial masthead.

### Main reader paths

1. **Browse the homepage and archives:** Find articles by division (*Research & Reviews* or *News, Features & Perspectives*), browse categories, and scan article cards.
2. **Read an article (`/articles/<slug>/`):** Read research articles of about 2,000 to 4,000 words, with figures and citations.
3. **Visit About (`/about/`):** Read the *Letter from the Editorial Board* and browse the division rosters under *Our Team*.
4. **Submit work (`/submit/`):** Check submission requirements and download the author guidelines.
5. **Search (`/search/`):** Search the publication and filter the article cards.

### Design tokens

- **Transitions**: `--dur-fast: 120ms`, `--dur-mid: 240ms`, `--ease-out: cubic-bezier(0.16, 1, 0.3, 1)`.
- **Palette**: Paper (`#fff`), Carbon Ink (`oklch(12% 0 0)`), Wesleyan Cardinal Red (`#a6192e` / `#841424`), Neutral Hairline Rule (`oklch(86% 0.004 70)`).
- **Typography**: Display/Serif (`Cormorant Garamond`), Body/Interface (`Libre Franklin`).
- **UI rules**: No flashing status dots, accent rails, or middle-dot separators.

---

## 2. Motion choices

| Role | Where it appears | Technique | Why it helps |
| --- | --- | --- | --- |
| **Reading progress** | Article progress indicator | CSS scroll timeline (`animation-timeline: scroll()`) | Tracks reading progress without scroll event listeners or layout reflow. |
| **About tabs** | Editorial letter and team roster | CSS opacity and Y transform | Shows which panel opened without an abrupt layout change. |
| **Navigation disclosure** | Header menu marker | CSS transform (`rotate(45deg)`) | Shows the open state by rotating the marker instead of swapping `+` and `−`. |
| **Button feedback** | Buttons and submission actions | CSS `:active` transform (`translateY(1px)`) | Confirms a click or tap. |
| **Search results** | Filtered article cards | CSS `@keyframes` fade and slide | Softens the appearance of cards when the search results change. |

### Lottie and decorative motion

**Decision: defer.** The journal is built for reading. Don't add a Lottie runtime or autoplaying mascot loops; they add page weight and compete with article text. Keep a still version of SVG artwork available. `JOURNAL.md` records the editors' earlier decision against unsolicited decorative logo animation and 3D loops.

---

## 3. Motion details and no-motion behavior

| Surface | Purpose | Priority | Format | Motion | Without motion | Implementation |
| --- | --- | --- | --- | --- | --- | --- |
| **About tabs**<br>`/about/`<br>`.about-panel` | Shows the switch between the Editorial Board letter and the 25-person masthead. | **Now** | CSS | On activation, change `opacity: 0 → 1` and `translateY(6px → 0)` over 240 ms with ease-out. | Switch immediately with `display: block / none`. Disable animation under `prefers-reduced-motion`. | Uses `.about-panel.is-active` in `style.css` and `publication.css`. Unit and visual tests can cover it. |
| **Reading progress bar**<br>`/articles/<slug>/`<br>`.article::before` | Helps readers track progress through long scientific articles. | **Now** | CSS | Add a 2.5px Cardinal Red rule at the top of the viewport. Scale it with `scaleX(0 → 1)` as the reader scrolls. | Keep it hidden when the page is static or at the top. Disable it under `prefers-reduced-motion`. | A CSS `@supports (animation-timeline: scroll())` pseudo-element on `.article`. No JavaScript. |
| **Section disclosure marker**<br>Header nav<br>`.panel__summary::after` | Shows whether a navigation section is open. | **Now** | CSS | Rotate the marker from `0deg` to `45deg` over 120 ms when `<details>` opens. | Show a static `+`. Disable animation under `prefers-reduced-motion`. | Uses `.panel__summary::after` in `style.css` and `publication.css`. |
| **Buttons and download action**<br>Global<br>`.btn`, `.search__submit` | Confirms that a button or download link was pressed. | **Now** | CSS | On hover use `translateY(-1px)`; on activation use `translateY(1px)`, over 120 ms with ease-out. | Keep the hover color change. Remove transforms under `prefers-reduced-motion`. | Uses `.btn`, `.btn--invert`, and `.search__submit`. |
| **Search results**<br>`/search/`<br>`#search-results .card` | Makes filtered article cards appear less abruptly. | **Now** | CSS | Fade cards in and move them up 6px over 240 ms with ease-out. | Show cards immediately at full opacity. Disable animation under `prefers-reduced-motion`. | Scoped to `#search-results .card` in the stylesheets. |

---

## 4. Accessibility and reduced motion

Use this reduced-motion rule to shorten or remove animation for readers who request less motion:

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

- **WCAG 2.2**: The strategy follows Success Criterion 2.2.2 (Pause, Stop, Hide) and 2.3.3 (Animation from Interactions).
- **Screen readers**: The reading progress indicator uses `pointer-events: none` on a CSS pseudo-element. It doesn't cover text or add a control for screen readers.
- **No background motion**: Avoid continuous loops, flashing, and bouncing.

