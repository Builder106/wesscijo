# WesSciJo Website Redesign Roadmap

## Status and scope

This is the working brief for a site-wide redesign. Hybrid A is the current implementation and one design reference, not the visual baseline. The next design may replace its layout, typography, navigation, artwork, and theme code if that produces a better publication site. Red and black, the cardinal, science, and birds are requirements for every direction. The earlier [Hybrid A design study](DESIGN.md) remains available as evidence of work already tried.

The repository contains a WordPress prototype with article, category, search, archive, About, Calendar, Submit, and 404 routes. The editorial materials describe a hybrid science journal and magazine for both science and nonscience Wesleyan readers. They distinguish Research & Reviews from News, Features & Perspectives, and call for original graphics in articles, issue browsing, and eventually a submission path. The current approved content, issue details, image rights, event details, and launch date must be checked with the editors before publication. Working drafts and mock covers are not publication approval.

Planning does not change the live site or authorize publication. Implementation should use a short-lived topic branch from `main` after a direction is chosen.

## Desired experience

The site should feel like a distinctive student science publication, with enough visual ambition to make its stories memorable and enough clarity to make them easy to read. A first-time visitor should be able to identify the journal, find a story by field or format, understand whether it is research, review, news, feature, perspective, interview, or essay, and continue to the current issue, archive, events, or submission information. Article pages should give scientific figures, captions, credits, and references room to work.

Visual continuity with Hybrid A is optional. Continuity of the journal's actual content, editorial taxonomy, accessible reading paths, and working URLs matters. Avoid carrying over a component simply because it already exists.

## Identity and art direction

The supplied [logo](logo.pdf) is a circular Wesleyan Science Journal seal. Its central image is a red cardinal wearing lab goggles and perched on a lab instrument, surrounded by black serif lettering on white. Use this as the source for the site's identity, while checking how well the full seal reads in the header, on a phone, and at small sizes. Any simplified mark or redraw needs review against the supplied artwork before use.

Use Wesleyan red and black as the dominant site colors. White or a light neutral can provide reading space and contrast; the logo's small accessory colors need not become interface colors. Confirm the production red against the university's current brand guidance before setting final color tokens. Every concept should show the cardinal clearly and tie it to science. Bird forms, feathers, nests, flight paths, and field imagery can recur in section art or transitions, but article graphics must still match their subjects.

Explore the proposed 3D bird nest with beakers and test tubes as a signature opening asset. First make a clear storyboard and a 2D composition so the idea can be judged without relying on animation. If the 3D version improves the page, define its source files, still image, mobile crop, loading budget, and reduced-motion state. The full seal should remain legible and identifiable even if the nest becomes the homepage's main illustration.

## Direction selection

Create and compare at least three genuinely different visual routes before committing to theme implementation. Each route must use the red-and-black identity, the cardinal, and a visible connection between birds and science:

1. **Editorial publication:** use the seal and cardinal with strong typography, purposeful image crops, varied story layouts, and a clear issue identity. Hybrid A can inform this route, but its exact band, rule, index, and split lead are optional.
2. **Science as visual material:** weave bird anatomy, flight, field observation, and approved scientific diagrams or figures into a red-and-black editorial system. Design an image-free state for stories without licensed or approved artwork.
3. **Distinctive journal world:** give the cardinal a prominent role in a nest of beakers and test tubes, with article subjects branching out from that world. Compare a strong still illustration with a 3D treatment before choosing one.

These are exploration prompts, not three required finished themes. A stronger fourth direction is welcome. Compare the routes using the same real or editor-approved representative stories, including a research article with figures, a news or feature article, and a story without a hero image. Show the homepage, article page, and mobile navigation at desktop and phone widths. Judge each route for visual quality, fidelity to the logo and palette, editorial fit, story differentiation, readability, navigation, accessibility, production effort, and how well it handles missing content. Record the reasons for the selected direction and what the team rejected.

**Direction gate:** editors and the designer choose a rendered direction after seeing representative pages, not a mood board alone. If none works, revise the strongest route before building it into WordPress.

## Product and content contract

Before new templates are built, inventory the current routes, post types, categories, URLs, content fields, and plugin-owned data. Confirm which files and Drive documents contain approved publication copy and artwork. Resolve the current homepage issue line and the mismatched Biology cover and Computer Science lead. Confirm the issue model, publication schedule, article credits, and image rights with the editorial team; the source notes contain working dates and differing print-section lengths.

The content model should support the two main divisions and their article formats, byline and affiliation when supplied, deck or abstract, field, issue, figures, captions and credits, references, related stories, and event details. Keep durable content data independent of a replaceable theme. Preserve post IDs, slugs, and public permalinks during a visual rebuild unless a reviewed migration is needed.

The team's request for graphics within articles and Word-like text wrapping is an authoring requirement. Prototype drag-and-drop placement and text wrapping with actual WordPress editing content before choosing a block or layout approach. A public submission system is a separate product decision: define the intake, permissions, review, spam handling, and editorial owner before exposing a form. Until then, the existing Submit route can explain the approved process.

The screenshot labels a literal subject tree as a stretch feature: Chemistry, Technology, and Physics branches would lead to article leaves. Prototype it as an optional browse experience only after the main category and search paths work. Its branches and leaves must be real links, with a plain list or equivalent navigation available to keyboard and small-screen readers.

## Implementation sequence

### 1. Capture the baseline and choose a direction

- Inventory every public route and its content source. Capture desktop and phone views, including search results, empty states, and an article with media.
- Build comparable rendered concepts for the direction gate above. Use the journal's own approved material where possible; label any temporary content and art clearly.
- Decide whether to evolve an existing theme or build a new one from scratch. Choose based on the selected design and the cost of preserving working WordPress behavior.

### 2. Build the publication system

- Establish the chosen red-and-black visual language across typography, spacing, imagery, figures, captions, controls, and responsive behavior. Make room for both long-form reading and quick story scanning.
- Build purposeful variants for research, reviews, news, features, and perspectives. Use composition and metadata to distinguish them without making every article layout unique.
- Make missing images, bios, dates, abstracts, and issue metadata produce deliberate states. Do not invent information to fill a component.
- Give navigation, search, and issue browsing clear roles. Test any tree or immersive navigation against a simple accessible route to the same content.

### 3. Complete the reader journeys

- **Home:** identify the publication and current editorial focus, then offer clear paths to read, browse, and explore an issue or archive. Avoid repeated grids that make every story look equally important.
- **Article:** support readable prose, inline figures, captions, credits, references when relevant, and a useful next-story or section path.
- **Browse and search:** make divisions, article types, archives, results, and empty states understandable on phone and desktop.
- **About, Calendar, Submit, and 404:** make each route useful with only approved roster, event, and submission information.

### 4. Verify and prepare release

- Compare the new experience with the route inventory so existing article, category, search, About, Calendar, Submit, and 404 journeys remain functional.
- Check keyboard access, visible focus, heading structure, readable contrast, responsive layouts, image alternatives, and reduced-motion behavior. If motion or 3D is used, retain complete static paths to the same content.
- Review page weight and image loading with actual media. Verify article and figure credits, image-story pairings, issue metadata, and all public copy with the editors.
- Run the repository's applicable syntax, style, security, and browser checks during implementation. Treat editorial approval and technical verification as separate release gates.

## Review criteria

The redesign is ready for editorial review when rendered desktop and phone pages use red and black, show the cardinal clearly, connect birds with science, make the journal's formats and divisions clear, and handle both image-rich and image-poor stories well. Readers must be able to complete the core journeys without relying on visual effects. The team should be able to maintain the chosen system with its real editorial capacity.

Release requires approved copy and art, accurate issue and event details, working public URLs, and a separate deployment decision. No design document by itself establishes launch readiness.
