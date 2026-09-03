# Recent Posts Grid — a custom Gutenberg block

A small custom WordPress plugin built to demonstrate a complete, current block-development workflow: `block.json`-driven registration, a dynamic (server-rendered) block, a real editor experience backed by live PHP preview, and the `@wordpress/scripts` build toolchain.

It adds one block, **Recent Posts Grid**, that renders a responsive grid of the site's most recent posts, with editor controls for how many to show, which category, sort order, column count, and which fields (thumbnail, excerpt, date, author) to display.

## Why this exists

This repo is a portfolio piece, sized to be read end to end in a few minutes rather than a production plugin. It's meant to show the pieces that come up in real custom-block work:

- **`block.json` (API version 3)** as the single source of truth for attributes, supports, and asset registration — read by both the JS build and PHP.
- **A dynamic block.** There's no `save()` output stored in post content; `src/render.php` builds the markup from `WP_Query` on every request via block.json's `"render"` field (WP 6.5+), so the block always reflects live data.
- **`InspectorControls` + `ServerSideRender`**, so the sidebar settings panel and the editor canvas both talk to the same PHP renderer the front end uses — no separate JS/PHP markup to keep in sync.
- **Live taxonomy data in the editor** — the category dropdown is populated from `@wordpress/core-data`, not hardcoded.
- **Block Supports** for wide/full alignment and color, so it behaves inside block themes and Global Styles rather than fighting them.
- **A standard toolchain**: `@wordpress/scripts` for the build, `.phpcs.xml.dist` for WordPress Coding Standards, and a GitHub Actions workflow that builds the block and checks every PHP file on each push.

## Project structure

```
recent-posts-grid-block/
├── recent-posts-grid-block.php   # Plugin bootstrap — registers the block from build/
├── src/                          # Source (edit this)
│   ├── block.json                # Block metadata: attributes, supports, asset wiring
│   ├── index.js                  # Registers the block, imports styles
│   ├── edit.js                   # Editor UI: InspectorControls + ServerSideRender
│   ├── render.php                # Front-end + editor-preview markup (PHP, dynamic)
│   ├── editor.scss                # Editor-only styles
│   └── style.scss                # Front-end + editor styles
├── build/                        # Compiled output (checked in so the plugin runs as-is)
├── .github/workflows/ci.yml      # Build + PHP lint on every push
├── .phpcs.xml.dist               # WordPress Coding Standards config
└── readme.txt                    # WordPress.org-style plugin readme
```

## Using it

The compiled `build/` directory is committed, so you can use the plugin without a Node toolchain at all:

1. Copy (or clone) this folder into `wp-content/plugins/`.
2. Activate **Recent Posts Grid Block** under **Plugins**.
3. Add the **Recent Posts Grid** block from the inserter (search "Recent Posts Grid," or find it under the Widgets category).

Requires WordPress 6.5+ (for `block.json`'s `render` field) and PHP 7.4+.

## Developing

```bash
npm install
npm run start   # watch mode for local development
npm run build   # production build into build/
npm run lint:css
npm run plugin-zip   # produces a WordPress.org-ready zip using .distignore
```

`npm run lint:js` is also wired up via `@wordpress/scripts`, but is left out of CI here: at the time of writing there's an unresolved version conflict a few layers deep in `@wordpress/eslint-plugin`'s own dependency tree (`jsx-a11y` → `array.prototype.flat` → `es-abstract`) that breaks ESLint's plugin loading regardless of this project's code. Nothing in `src/` is affected — `npm run build` and `npm run lint:css` both run clean — but it's worth knowing about if you try `lint:js` locally and hit an `es-abstract/2025/ToString` error; that's upstream, not this repo.

## License

GPLv2 or later, in keeping with WordPress plugin conventions. See [LICENSE](LICENSE).
