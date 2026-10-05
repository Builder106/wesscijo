# WesSciJo

WordPress themes and a standalone browser preview for The Wesleyan Science Journal. The repository includes three themes, a site plugin, editorial review captures, and browser checks.

The `wessci-cardinal` theme and its preview are a work in progress. The preview uses example article copy and concept artwork, and it does not add content to WordPress. Check [ROADMAP.md](ROADMAP.md) for current redesign scope and [JOURNAL.md](JOURNAL.md) for dated decisions.

## Repository map

| Path | Contents |
| --- | --- |
| `themes/wessci-cardinal/` | Current black and red theme concept. |
| `themes/wessci-hybrid-a/` | Hybrid A theme, including its article, archive, About, and Calendar templates. |
| `themes/wessci-hybrid-b/` | Alternate hybrid theme concept. |
| `plugins/wessci-site/` | Site-specific event content and metadata. |
| `preview/` | Standalone Cardinal preview and sample story art in `preview/assets/`. Open `index.html` in a browser. |
| `tests/` | Playwright checks for the preview and public site. |
| `screenshots/design-directions/` | Earlier visual direction comparisons. |
| `screenshots/cardinal/` | Cardinal artwork and preview captures. |
| `screenshots/client-reply/` | Screens prepared for client feedback. |
| `artifacts/svgator/cardinal/` | Editable SVGator projects and export records. |
| `artifacts/design-inspiration/` | Local-only design research and capture evidence, ignored by Git. |
| `logo.pdf` | Supplied journal logo. |

## Browser checks

Install the declared dependencies and Chromium, then run the browser checks:

```sh
npm ci
npx playwright install chromium
npm run test:browser
```

The public-site checks use `BASE_URL` when set and otherwise target the configured public site. The Cardinal preview checks run from the local `preview/index.html` file and save captures under `screenshots/cardinal/`.
