# WesSciJo design history

## Status

This file records an earlier site design exploration. Treat it as an archive, not current implementation guidance. See [ROADMAP.md](ROADMAP.md) for the current direction. The references, workflow notes, and readiness assessment below describe that earlier work; they do not set current requirements or approve content or a launch. Later project changes are recorded in [JOURNAL.md](JOURNAL.md).

## Reference provenance

Three references passed the design-inspiration check as dated Awwwards Site of the Day winners. The award confirmed that each reference was eligible. We then checked how well each page fit a student science publication.

| Reference | Award marker | Role and adaptation |
| --- | --- | --- |
| [Emergence Magazine](https://www.awwwards.com/sites/emergence-magazine) and [live publication](https://emergencemagazine.org/) | Site of the Day, Aug 11, 2018 | Primary. Its homepage distinguishes feature, poem, essay, interview, film, and event pathways. Adapt the clear story-format hierarchy and different reading pathways. Keep WesSciJo's masthead, divisions, actual article records, and plain navigation. |
| [Atmos](https://www.awwwards.com/sites/atmos) and [live publication](https://atmos.earth/) | Site of the Day, Jul 15, 2022 | Supporting. It combines field/topic navigation with a stream of environmental reporting and an explicit volume identity. Adapt browsable topic links and separated story lanes. WesSciJo will link only to its existing divisions and categories. |
| [Into the Amazon](https://www.awwwards.com/sites/into-the-amazon) and [National Geographic project](https://www.nationalgeographic.com/into-the-amazon/) | Site of the Day, Apr 21, 2025 | Supporting. Its case-study page describes a research-led, image-rich science narrative using maps and satellite data. Adapt the principle that imagery should explain the science. Do not reproduce its WebGL journey or use its imagery in WesSciJo. |

Emergence is the main reference because its story formats and ways to browse fit the journal's existing categories. Atmos is useful for section browsing. Into the Amazon may inform future, approved art direction for specific stories, but it is not a homepage template.

The requested Site of the Day searches for editorial, magazine, typography, and restrained interaction returned no results. The searches stayed within that award tier. A broader search found the three verified references above. We left out Noema, The Pudding, and Ink because their Awwwards pages did not pass verification. That does not establish whether they won an award.

### Measured tokens

The token extraction used the Awwwards Emergence listing page. The extractor confirmed that the results describe the listing, not the live publication. We kept the output in `artifacts/design-inspiration/emergence-listing-tokens.json` as a record of the extraction, not as a measurement of the publication.

The listing measurements included #222222 text, a #f8f8f8 background, a #fff083 accent, and an Inter Tight display sample at 170px. These measurements describe the listing only. They do not set WesSciJo's palette or type scale.

### Live-site observation boundary

We reviewed public page text and hierarchy on Emergence and Atmos. We also confirmed the Into the Amazon page title and read its studio case study about science storytelling, maps, photography, and a WebGL expedition. We did not produce an accepted recording, so live animation, timing, scrolling, and reduced-motion behavior remain unobserved for all three references.

## WesSciJo system

### Atmosphere and identity

The earlier direction used a black masthead, Wesleyan-red hinge, white navigation index, and text-left/image-right lead. The opening read as an editorial cover: an unmistakable publication name, compact navigation and search, then one large story split between a quiet text block and its matched lead image. It used a warm paper field and strong ink contrast below the masthead.

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
7. **Issue and archive pathway.** The homepage hard-codes a label for volume 1, issue 1, dated October 2026, while the roadmap describes an inaugural-issue placeholder. The homepage data query has no verified issue record. Until the editors confirm the issue title, number, date, and cover, do not present an issue identity as current. Keep the Archives link and its existing page state.
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

## Earlier workflow evidence and blockers

The records from this design exploration are in the ignored `artifacts/design-inspiration/` directory:

- `preflight.json` lists the active Codex context, available capabilities, plugin listing, and grants that were not verified.
- `references.json` records the award checks, reference roles, excluded candidates, and searches.
- `emergence-listing-tokens.json` preserves the listing-only measurements and their scope note.
- `site-motion-capture/blocked.json` records the failed runner connection and the four capture stages that did not run.
- `workflow-manifest.candidate.json` is an incomplete draft, not a completed workflow record.

The GPU precheck could not connect to the capture runner. As a result, no `gpu_check_id`, WebM, jank report, capture manifest, motion analysis, or frame was produced. The workflow service rejected this project workspace, so its project-specific run and validation did not start. Open Design listed the required effect-extractor plugin, but its grants were not checked. The video-analysis provider required approval, and no recording was uploaded.

Baseline screenshots and page measurements are saved in `artifacts/design-inspiration/baseline/`. Playwright captured six views at 390px, 768px, and 1280px, using full and reduced motion. After scrolling to load deferred media, all seven story images appeared at every width. Each view had one `h1` and no horizontal overflow. Navigation Timing showed a `responseStart` of 50 to 71.8 ms and an HTML `transferSize` of 8,143 bytes. These are browser readings from the baseline, not Web Vitals, full-page transfer totals, or measurements of a redesign. After the media loaded, the full and reduced-motion screenshots matched pixel for pixel. The static capture showed no visible motion.

At the time, these gaps blocked the required Open Design handoff. The workflow required a fresh GPU check ID and an accepted project workspace before implementation could continue. It also required approval for motion analysis. Screenshots could not replace the live capture, and the capture backend could not be changed without review.

## Proposed review plan for that exploration

No visual review cycle began because there was no editable prototype. The proposed plan allowed up to three cycles within a 90-minute budget. Each cycle would inspect screenshots at 390px, 768px, and 1280px, compare reduced-motion behavior, rate editorial clarity, journal identity, composition, mobile use, accessibility, page weight, and response from 1 to 5, and record one defect to fix. The review would finish when every score reached at least 4, with no horizontal overflow, one main `h1`, and working keyboard and search paths. It would stop after two cycles without measurable improvement. These are proposed review criteria, not results from an implemented design.

## Readiness at the time of this exploration

- **Design review:** The direction and references were documented, but there was no editable homepage or rendered design to review.
- **Technical implementation:** The work was blocked at the required capture and Open Design gates.
- **Content approval:** Editors still needed to confirm the issue identity and lead image pairing.
- **Launch readiness:** Not assessed. The design, editorial package, and deployment had not passed review.
