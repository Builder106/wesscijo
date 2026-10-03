# WesSciJo Hybrid A Design

## Status

This document records a design plan for review. Homepage implementation is blocked pending accepted live captures and Open Design handoff. It is not a content approval or launch approval.

## Reference provenance

Three references passed the design-inspiration verifier as dated Awwwards Site of the Day winners. The award tier confirms eligibility; each page was compared for relevance to a student science publication.

| Reference | Award marker | Role and adaptation |
| --- | --- | --- |
| [Emergence Magazine](https://www.awwwards.com/sites/emergence-magazine) and [live publication](https://emergencemagazine.org/) | Site of the Day, Aug 11, 2018 | Primary. Its homepage distinguishes feature, poem, essay, interview, film, and event pathways. Adapt the clear story-format hierarchy and different reading pathways. Keep WesSciJo's masthead, divisions, actual article records, and plain navigation. |
| [Atmos](https://www.awwwards.com/sites/atmos) and [live publication](https://atmos.earth/) | Site of the Day, Jul 15, 2022 | Supporting. It combines field/topic navigation with a stream of environmental reporting and an explicit volume identity. Adapt browsable topic links and separated story lanes. WesSciJo will link only to its existing divisions and categories. |
| [Into the Amazon](https://www.awwwards.com/sites/into-the-amazon) and [National Geographic project](https://www.nationalgeographic.com/into-the-amazon/) | Site of the Day, Apr 21, 2025 | Supporting. Its case-study page describes a research-led, image-rich science narrative using maps and satellite data. Adapt the principle that imagery should explain the science. Do not reproduce its WebGL journey or use its imagery in WesSciJo. |

Emergence is the primary reference because its format distinctions and discovery pathways map well to the existing journal taxonomy. Atmos supports section browsing. Into the Amazon is useful for future approved, story-specific art direction. It is not a publication homepage pattern.

The contracted SOTD searches for editorial, magazine, typography, and restrained interaction returned no results. Searches remained within the requested SOTD tier. Broader live-reference preparation accepted three independently verified SOTD records above; failed records (Noema, The Pudding, and Ink) were excluded. The search did not establish those failed nominees as non-winners; their Awwwards URLs did not pass verification.

### Measured tokens

The extraction request used the Awwwards Emergence listing URL. The extractor confirmed that its token measurements describe that listing page, not the live Emergence publication. The saved output is preserved in `artifacts/design-inspiration/emergence-listing-tokens.json` for provenance and is not treated as a measurement of the reference publication.

Examples reported for the listing include #222222 text, #f8f8f8 background, a #fff083 accent, and an Inter Tight display sample measured at 170px. Those are listing-page measurements. They do not inform WesSciJo's selected palette or type scale.

### Live-site observation boundary

Public page text and hierarchy were reviewed for the live Emergence and Atmos sites. The Into the Amazon page title was resolved and its studio case study describes science storytelling, maps, photography, and a WebGL expedition. No accepted GPU-backed recording was produced. Live animation, timing, scroll behavior, and reduced-motion behavior remain unobserved for all three references.

## WesSciJo system

### Atmosphere and identity

Keep Hybrid A's black masthead, Wesleyan-red hinge, white navigation index, and text-left/image-right lead. Let the opening read as an editorial cover: an unmistakable publication name, compact navigation and search, then one large story split between a quiet text block and its matched lead image. Use a warm paper field and strong ink contrast below the masthead.

The current source defines paper, ink, Wesleyan red, a 1400px shell, Libre Franklin interface/body text, and Cormorant Garamond for hero display. These are observed source tokens. Extend those existing roles; do not claim reference-derived color or font measurements. Use the serif sparingly for the lead title or a single section-level display moment. Keep labels, navigation, captions, and metadata in Libre Franklin.

### Type roles

| Role | Treatment |
| --- | --- |
| Publication masthead | Existing configurable Libre Franklin brand title in the black band |
| Lead title | Cormorant Garamond, large and editorial; retain readable line breaks and sufficient contrast |
| Story titles | Libre Franklin, weight and size scaled by editorial priority |
| Reading copy and excerpts | Libre Franklin with generous line height and the existing readable measure |
| Navigation and controls | Libre Franklin, clear sentence case, visible focus |
| Section and article type | Existing category terms rendered as plain text links or labels; no fabricated genres |
| Date and reading time | Existing post date and calculated reading time, secondary to title and deck |
| Captions | Only render when a real caption exists; use body font at a smaller readable size |

The live baseline image shows the Biology mock cover paired with the Computer Science reproducibility title. This is an editorial mismatch already named in the roadmap. Keep it recorded as a blocker. Do not generate, select, or publish substitute lead artwork without editorial approval. A deliberate no-image lead treatment is safer than a mismatched cover if the template supports that fallback.

## Homepage sequence

1. **Masthead, navigation, and search.** Retain the existing brand, Home, division disclosures, Archives, Calendar, About, and Submit links. Preserve the labeled native search form and the keyboard-operable disclosure panels.
2. **Lead story.** Keep one full-width text-left/image-right story. Its section/category, title, existing excerpt, date, calculated reading time, and article link establish what the journal covers and what to read first.
3. **Latest stories.** Add a direct, clearly named route into the newest articles after the lead. Keep the lead out of the list. Make the pathway useful without adding an unsupported Editor's Picks claim.
4. **Research and Reviews.** Give research a quieter, evidence-led composition with a larger first story and a short ruled list or smaller supporting stories. Use the existing division and child categories; do not infer peer-review status or researcher affiliation.
5. **News, Features, and Perspectives.** Change the rhythm: one broader feature with adjacent compact story links or a staggered editorial list. Preserve each title, excerpt, taxonomy, and URL. Do not use a repeated three-card row.
6. **Browse fields.** Present the existing category names as a compact typographic index grouped under their current division. This uses current WordPress taxonomy and does not imply a new field database.
7. **Issue and archive pathway.** The homepage currently hard-codes “Vol. 1 — No. 1 — October 2026,” while the roadmap describes an inaugural-issue placeholder. No verified issue record is present in the homepage data query. Until the editors verify the issue title, number, date, and cover, omit an issue identity rather than asserting this line as current. Retain the Archives link and its existing page state.
8. **Audience pathways.** Keep the existing submission prompt and Submit route. Keep Calendar and About reachable through navigation and footer. Do not add subscription controls; no subscription functionality is present in the theme.
9. **Colophon.** Close on a dark footer that repeats the publication name and description, division and journal links, and the existing Wesleyan views disclaimer. Keep link contrast and section names legible.

This order identifies the publication and lead first, gives readers an immediate next-story route, then separates the two editorial divisions before offering taxonomy, archive, and contribution paths. It makes reading the default action without crowding the opening with every destination.

## Spacing, rules, and composition

Use the existing 8px-based spacing tokens and 1400px shell. Preserve large vertical gaps around the lead and division breaks. Use full-width black or gray rules to mark editorial transitions. Keep Wesleyan red for the masthead hinge, navigation or category states, and strong actions. Do not add status dots, eyebrow headings, left accent rails, or middle-dot separators.

Avoid equal repeated card grids. Use type size, rule length, whitespace, text width, and changing image ratios to distinguish a research list from feature coverage. Let content count set each layout. If a division has no records, render the existing deliberate empty state or omit the section; never add filler stories.

## Image treatment

A real story image should illustrate its own title or topic. Use the post's approved featured image, alt text, generated WordPress responsive candidates, and source dimensions. Keep the lead eager with high fetch priority and accurate `sizes`; lazy-load below-fold media. Use a neutral no-image treatment when no approved image exists. Do not reuse the generic Biology cover as decoration across divisions. Do not commission bespoke art until editorial review resolves the current lead mismatch.

## Responsive behavior and accessibility

At 1280px, use a roomy text/image lead and asymmetrical story arrangements. At 768px, reduce gutters and let division stories rebalance without squeezing captions or titles. At 390px, stack the lead text before its image, retain the native compact navigation behavior, and keep the search field reachable without horizontal overflow.

Use one main `h1`, sequential section and story headings, semantic landmarks, the visible-on-focus skip link, clear link names, keyboard-openable disclosures, visible focus, and useful alt text. Preserve all reading content and controls under reduced motion. Prefer no nonessential motion until live captures show a specific useful interaction. Any later proposed motion must document its trigger, duration, easing, mobile behavior, and static reduced-motion state.

## Interaction and motion

Do not add motion based on the reference text or listing tokens alone. The reference capture gate failed before a recording. No live motion was observed. The current direction is static HTML and CSS; transitions may be considered only after the approved capture stage. Reading, navigation, search, and article links must remain fully available with reduced motion enabled and when animation is unsupported.

## Content and editorial limits

Preserve placeholder article and event copy byte-for-byte. Do not invent authors, affiliations, biographies, story dates, issue details, awards, readership, newsletter functionality, or endorsement. The homepage's issue line needs editorial verification. The Biology/Computer Science pairing needs editorial review. Submission copy, Calendar entries, Archive content, and about-page roster remain source-controlled and must retain their existing wording.

## Workflow evidence and blockers

Current workflow evidence and failure records live under the ignored `artifacts/design-inspiration/` directory:

- `preflight.json` records the active Codex context, available capabilities, plugin listing, and unknown grants.
- `references.json` records dated SOTD verification, roles, failed candidates, and searches.
- `emergence-listing-tokens.json` retains listing-only extraction with its scope caveat.
- `site-motion-capture/blocked.json` records the refused runner connection and all four unrecorded cells.
- `workflow-manifest.candidate.json` is an explicitly incomplete draft. It is not valid completed workflow evidence.

The capture runner refused SSH during the GPU precheck; no `gpu_check_id`, WebM, jank report, capture manifest, motion analysis, or frame was produced. The workflow host also rejects the project path because its configured root is the parent `CS` workspace. Its project-scoped run and validation calls cannot start. Open Design lists the required effect-extractor plugin, but this execution did not verify its grants. Codex exposes the Gemini video-analysis tool, but no provider approval was established and no recording was uploaded.

Separate live-site baseline screenshots and page measurements are saved under `artifacts/design-inspiration/baseline/`. Playwright completed one six-view capture at 390px, 768px, and 1280px, with full and reduced motion preferences. After scrolling to load deferred media, all seven story images loaded in each view. The page had one `h1` and no horizontal overflow at each width. Navigation Timing recorded a `responseStart` between 50 and 71.8ms and an HTML `transferSize` of 8,143 bytes. These are baseline browser readings, not Web Vitals, full-page transfer totals, or redesigned-page measurements. The full and reduced screenshots match pixel-for-pixel after media loading; no visible motion was observed in this static capture.

These missing stages prevent the required Open Design handoff. Per the workflow contract, do not replace live captures with screenshots or silently switch capture backends. Continue implementation only after the accepted runner returns a fresh GPU check ID and the configured workflow host accepts this project workspace. Motion analysis also needs an explicitly approved provider.

## Review plan

No visual revision cycle has begun because no editable prototype could pass the required handoff. If the handoff becomes available, allow up to three measured cycles. Define the rubric as editorial clarity, recognizable Hybrid A identity, varied composition, mobile usability, accessibility, and measured page weight/response. Budget 90 minutes and three cycles. At each cycle inspect 390px, 768px, and 1280px screenshots, compare reduced-motion behavior, and record each rubric rating from 1 to 5 with one defect-based change. Finish at all scores of at least 4 with no horizontal overflow, one main `h1`, and working keyboard and search paths. Stop earlier after two cycles with no measured improvement. This is a future evaluation plan, not a score for an unimplemented design.

## Readiness

- **Design review:** the direction and references are documented, but there is no editable homepage artifact or rendered design to review.
- **Technical implementation:** blocked at the required capture and Open Design gates.
- **Content approval:** required for issue identity and lead art pairing.
- **Launch readiness:** not assessed; no implementation, editorial package, or deployment review has passed.
