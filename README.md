# WesSciJo

This is the codebase for The Wesleyan Science Journal, Wesleyan University's student-run science publication. The live site runs on WordPress. This repo holds its theme and editorial tools, a static preview, and the source content for the Fall 2026 issue.

## Live site

See the current edition at [thewesleyansciencejournal.com](https://thewesleyansciencejournal.com/).

### Homepage
![The Wesleyan Science Journal homepage](screenshots/live-site/01-homepage.png)

[View the full homepage capture](screenshots/live-site/01-homepage-full.png).

### Article reading view
![Article reading view for Birds of Wesleyan](screenshots/live-site/02-article-birds-of-wesleyan.png)

### Submission guidelines and about page
![Submission guidelines](screenshots/live-site/04-submit.png)
![About page and editorial board letter](screenshots/live-site/03-about.png)

### Archives
![Journal archives](screenshots/live-site/05-archives.png)

## How this repo works

WordPress runs the public site and gives editors a place to manage publishing. The repo also includes a static build for previews and content checks.

### Static preview
The `public/` directory holds a generated edition of the journal. The Python renderer (`scripts/render-publication.py`) uses the standard library to read articles and issue notes from `content/issue-2026/`, then writes static HTML, CSS, and search data. It updates the preview only; it doesn't change the production WordPress site.

### WordPress site
The `plugins/wessci-site/` plugin adds these editorial tools to WordPress:

- Custom workflow statuses: `in_review`, `copyediting`, and `ready_for_issue`.
- Article metadata fields for author credentials, figures, captions, licenses, and references.
- An issue assembly widget that tracks published, ready, and in-progress articles.
- Masthead management for the editorial roster.

The active theme is in `themes/wesscijo/`. Its backup, `themes/wessci-fallback/`, stays in sync for disaster recovery.

### Keep the preview in sync
A change in WordPress won't appear in the static preview on its own. Once an editorial change is approved for the preview, update `content/issue-2026/`, rebuild `public/`, and check the result. The WordPress theme is deployed separately.

## Quick start

To build and serve the static preview:

```sh
python3 scripts/render-publication.py
python3 -m http.server 8000 -d public
```

Open `http://localhost:8000` in your browser.

### Run browser checks

The Playwright specs cover article routes, navigation, search filtering, and responsive layouts:

```sh
PUBLICATION_BASE_URL=http://localhost:8000 npx playwright test tests/editorial-publication.spec.js
WORDPRESS_PREVIEW_URL=http://localhost:8888 npx playwright test tests/editorial-wordpress.spec.js
```

### Keep the backup theme current

After changing templates or styles in `themes/wesscijo/`, run this script to copy them to `themes/wessci-fallback/`:

```sh
bash scripts/promote-to-fallback.sh
```

## Fall 2026 issue contents

The snapshot in `content/issue-2026/` has nine articles across the journal's two sections:

- **Research & Reviews:** *GMOs: Applications and Controversy*; *Hydroxylation Pattern of Bile Salt Governs Mixed Micelle Architecture and Peptide Association*; *Can Quantum Physics Secure Our Votes?*; and *The Math Behind LLMS*.
- **News, Features & Perspectives:** *The Birds of Wesleyan and How To Find Them*; *Psychology in the Age of TikTok*; *Liberal Arts to Scientific Academia: A Road Less Traveled*; *The ChatGPT Moment for Robotics*; and *Scientific Paradigms: A Case for Science and Technology Studies*.

The issue also includes the Editorial Board letter, division landing pages, site-wide search, and submission guidelines linked to the official Google Form.

## What's in the repo

- `preview/`: Design previews, including the cardinal entrance animation sources and its [MP4 recording](preview/logo-entrance-recording.mp4). The separate Cardinal site concept uses sample articles.
- `brand/`: Source assets for the official journal seal, which shows a goggled cardinal perched on a syringe.
- `tests/`: End-to-end browser specs, WordPress Playground blueprints, and syntax checks.

## Image credits

A few image attributions in `content/issue-2026/source-notes.json` still need confirmation. Leave the supplied credits in place until the editors confirm the missing ones.
