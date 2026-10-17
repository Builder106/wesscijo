# WesSciJo website redesign roadmap

## Status and scope

The editors' October 5 emails set the scope for this release and replace the broader redesign brief. Keep the current dropdown navigation, article layout, and About structure. Use simpler white and red styling, show the official logo and full name "The Wesleyan Science Journal", and make the current edition and outside submissions easy to find. Save further design exploration for an in-person meeting after the edition is released.

The release must include the nine articles on the final Fall 2026 list, labeled thumbnails, inline graphics, and both GMO comic pages. About must include the Editorial Board letter and the requested editorial, graphic design, and web design credits. Use the approved article content document and the form from Shriya's September 27 email for the submission guidelines. Remove the Calendar, placeholder stories, and unsolicited About content. Keep photo credits and author references. Check that every title opens its own article and that search works.

The owner approved publishing the reviewed release on October 6. The rest of this roadmap records earlier design research that has been deferred. It does not add work to this release.

## Desired experience

The site should feel like a student science publication and make its stories easy to read. First-time visitors should be able to identify the journal, find stories by field or format, and tell whether an article is research, a review, news, a feature, a perspective, an interview, or an essay. They should also be able to get to the current issue, archive, events, and submission information. Article pages need room for scientific figures, captions, credits, and references.

The design can change, but keep the journal's content, article categories, accessible reading paths, and working URLs. Don't keep a component just because it's already there.

## Identity and art direction

The supplied [logo](brand/logo.png) is a circular Wesleyan Science Journal seal. It shows a red cardinal in lab goggles perched on a lab instrument, surrounded by black serif lettering on white. Use it as the source for the site's identity. Check that the full seal reads in the header, on a phone, and at small sizes. Review any simplified mark or redraw against the supplied artwork before using it.

Use Wesleyan red and black as the main site colors. White or a light neutral can provide reading space and contrast. The logo's small accessory colors don't need to become interface colors. Check the production red against the university's current brand guidance before setting final color tokens. Every concept should show the cardinal and connect it to science. Bird forms, feathers, nests, flight paths, and field imagery can appear in section art or transitions. Article graphics must match their subjects.

Explore the proposed 3D bird nest with beakers and test tubes as a signature opening image. First make a clear storyboard and a 2D composition so the idea can be judged without animation. If the 3D version works better on the page, document its source files, still image, mobile crop, loading budget, and reduced-motion state. Keep the full seal legible even if the nest becomes the homepage's main illustration.

## Direction selection

Create and compare at least three distinct visual routes before choosing a theme. Each route must use the red-and-black identity, show the cardinal, and connect birds with science:

1. **Editorial publication:** Use the seal and cardinal, strong typography, thoughtful image crops, varied story layouts, and a clear issue identity. The current site can inform this route, but its exact band, rule, index, and split lead are optional.
2. **Science as visual material:** Bring bird anatomy, flight, field observation, and approved scientific diagrams or figures into a red-and-black editorial system. Design an image-free state for stories without licensed or approved artwork.
3. **Distinctive journal world:** Put the cardinal in a nest of beakers and test tubes, with article subjects branching out from it. Compare a still illustration with a 3D version before choosing one.

Treat these as prompts, not three themes that all need to be finished. Consider a fourth route if it is stronger. Compare the routes with the same real or editor-approved stories: a research article with figures, a news or feature article, and a story without a hero image. Show the homepage, article page, and mobile navigation at desktop and phone widths. Compare visual quality, fit with the logo and palette, editorial fit, story differences, readability, navigation, accessibility, production effort, and handling of missing content. Record why the team chose one route and rejected the others.

**Direction gate:** Editors and the designer choose a route after reviewing rendered pages, not a mood board alone. If none works, revise the strongest route before building it in WordPress.

## Product and content contract

Before building templates, list the current routes, post types, categories, URLs, content fields, and plugin-owned data. Confirm which files and Drive documents hold approved copy and artwork. Resolve the homepage issue line and the mismatch between the Biology cover and Computer Science lead. Confirm the issue model, publication schedule, article credits, and image rights with the editors. The source notes contain working dates and different print-section lengths.

The content model should support the two divisions and their article formats, plus bylines and affiliations when supplied. It should also support a deck or abstract, field, issue, figures, captions and credits, references, related stories, and event details. Keep publication data separate from the theme. Preserve post IDs, slugs, and public permalinks during a visual rebuild unless the team reviews a migration.

The team asked to place graphics within articles and wrap text around them as in Word. Treat both as authoring requirements. Prototype drag-and-drop placement and text wrapping with real WordPress content before choosing a block or layout. A public submission system is a separate product decision. Define its intake, permissions, review, spam handling, and editorial owner before exposing a form. Until then, the Submit page can explain the approved process.

The screenshot marks a subject tree as a stretch feature. Its Chemistry, Technology, and Physics branches would lead to article links. Prototype it as an optional way to browse only after the main category and search paths work. Make every branch and article a real link, and provide a plain list or equivalent navigation for keyboard and small-screen readers.

## Implementation sequence

### 1. Capture the baseline and choose a direction

- List every public route and its content source. Capture desktop and phone views, including search results, empty states, and an article with media.
- Build comparable, rendered concepts for the direction review. Use approved journal material where possible, and clearly label temporary text or art.
- Decide whether to update an existing theme or build a new one. Base the choice on the selected design and the work needed to preserve WordPress behavior.

### 2. Build the publication system

- Apply the red-and-black visual language to type, spacing, images, figures, captions, controls, and responsive layouts. Support both long-form reading and quick scanning.
- Create layouts for research, reviews, news, features, and perspectives. Use composition and metadata to distinguish them without making every article unique.
- Design clear states for missing images, bios, dates, abstracts, and issue details. Don't invent information to fill a component.
- Give navigation, search, and issue browsing distinct roles. Compare any tree or immersive navigation with a simple accessible route to the same content.

### 3. Complete the reader journeys

- **Home:** Identify the publication and current editorial focus. Give readers clear ways to read and browse the issue or archive. Avoid repeated grids that give every story equal weight.
- **Article:** Support readable prose, inline figures, captions, credits, relevant references, and a useful path to another story or section.
- **Browse and search:** Make divisions, article types, archives, results, and empty states clear on phones and desktops.
- **About, Calendar, Submit, and 404:** Make each page useful with approved roster, event, and submission information only.

### 4. Verify and prepare release

- Compare the new site with the route inventory. Keep article, category, search, About, Calendar, Submit, and 404 paths working.
- Check keyboard access, visible focus, heading order, contrast, responsive layouts, image alternatives, and reduced-motion behavior. If you use motion or 3D, keep every content path available without it.
- Check page weight and image loading with real media. Confirm article and figure credits, image-story pairings, issue details, and public copy with the editors.
- Run the relevant syntax, style, security, and browser checks. Keep editorial approval and technical verification as separate release gates.

## Review criteria

The redesign is ready for editorial review when desktop and phone pages use red and black, show the cardinal, connect birds with science, explain the journal's formats and divisions, and handle stories with and without images. Readers must be able to complete the main tasks without visual effects. The team must be able to maintain the design with its available editorial capacity.

Release needs approved copy and art, accurate issue and event details, working public URLs, and a separate deployment decision. This roadmap alone does not approve a launch.
