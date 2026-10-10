# WesSciJo

WordPress themes and a static publication for The Wesleyan Science Journal. The Fall 2026 edition is the current WesSciJo site, with white and red styling and the editors' article content.

`public/` is the static publication served by the website. Its real article routes, search index, About page, and submission guidelines are rendered from `content/issue-2026/` by `scripts/render-publication.py`. The separate Cardinal concept in `preview/` uses sample stories and is deferred. Check [ROADMAP.md](ROADMAP.md) for the editors' current requirements and [JOURNAL.md](JOURNAL.md) for dated decisions.

## Repository map

| Path | Contents |
| --- | --- |
| `public/` | Static publication with unique article URLs. |
| `content/issue-2026/` | Article content, source records, artwork credits, editorial letter, and guidelines. |
| `scripts/render-publication.py` | Standard-library renderer for the static publication. |
| `scripts/seed-issue-preview.php` | Seeder for an isolated WordPress issue preview. |
| `themes/wesscijo/` | Current WesSciJo theme, with article, archive, and About templates. |
| `plugins/wessci-site/` | Site-specific event content and metadata. |
| `preview/` | Cardinal design preview with local styles in `preview.css` and artwork, fonts, and menu code in `assets/`. Open `index.html` in a browser. |
| `tests/` | Playwright checks for the preview and public site. |
| `screenshots/design-directions/` | Earlier visual direction comparisons. |
| `screenshots/cardinal/` | Cardinal artwork and preview captures. |
| `screenshots/client-reply/` | Screens prepared for client feedback. |
| `artifacts/` | Local-only design research, SVGator projects, and capture evidence (Git-ignored). |
| `brand/` | Supplied high-resolution journal logo files (`logo.pdf`, `logo.png`). |

## Browser checks

Run the publication checks on the development host against an isolated preview:

```sh
python3 scripts/render-publication.py
PUBLICATION_BASE_URL=http://127.0.0.1:59090 npx playwright test tests/editorial-publication.spec.js
```

Serve `public/` on the selected preview port before the browser check. Install dependencies and browsers on the development host. Publication captures go in the ignored `artifacts/editorial-review/` folder. The older public-site and Cardinal checks cover historical previews and are separate from this release's gate.

Article imports are a dated source snapshot. Changes in WordPress do not automatically update this static edition; update the content snapshot and rerender before releasing an editorial change.

The WordPress preview uses `tests/editorial-preview-blueprint.json` and `scripts/seed-issue-preview.php` in an isolated Playground installation. The seeder requires the preview flag and replaces the preview's posts; use it only for the disposable review environment. Run `tests/editorial-wordpress.spec.js` with `WORDPRESS_PREVIEW_URL` pointing to that installation.

Some image credits are still missing from the supplied material. `content/issue-2026/source-notes.json` lists the affected files and the three articles whose first inline figure supplies a thumbnail. These credits are follow-up work, as directed by the owner; retain the supplied credits and references.
