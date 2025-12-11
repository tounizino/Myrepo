# FMHY Atlas WordPress Theme

This repository contains a custom WordPress theme that recreates [fmhy.net](https://fmhy.net) inside WordPress while staying 100% file-based. It automatically pulls every public Markdown document from the `fmhy/edit` GitHub repository, renders it with heading anchors, and exposes a bilingual UI (English ↔ Arabic) with RTL-friendly layouts.

## Key features

- **Remote doc ingestion** – Any request such as `/privacy/` or `/posts/dec-2025/` is converted to `docs/privacy.md` (or nested equivalents) inside the FMHY GitHub repo. Responses are cached for six hours using WordPress transients.
- **Arabic localisation** – Theme strings ship with an Arabic translation (`languages/fmhy-ar.*`). Visitors can switch languages via the header toggle which stores a cookie and updates the WordPress locale. You can drop fully translated Markdown files into `content/ar/...` to override specific sections.
- **Dynamic layout** – A bespoke homepage mirrors the current fmhy.net hero, CTA buttons, feature matrix, ecosystem links and social section with the same emoji/colour system.
- **Automatic table of contents** – Remote Markdown is parsed with Parsedown, sanitized, and enriched with `id` attributes on every `h2–h4`, enabling sticky ToCs on doc pages.
- **Dark/light theming** – Visitors can switch modes; the choice is persisted via cookie and applied on first paint.

## Repository structure

```
wp-content/
  themes/
    fmhy/
      assets/
        hero-placeholder.svg
        js/theme.js
      content/
        ar/privacy.md
      inc/
        docs.php        # Remote fetching + Markdown rendering
        helpers.php     # Shared helpers (locale, cookies, kses, etc.)
        theme-data.php  # Hero, nav, features, ecosystem data
        vendor/Parsedown.php
      languages/
        fmhy-ar.po / fmhy-ar.mo
      templates/
        doc.php
      front-page.php, header.php, footer.php, ...
```

## Installation

1. Copy `wp-content/themes/fmhy` into your WordPress installation (or add this repo as a submodule inside `wp-content/themes`).
2. From `wp-admin → Appearance → Themes`, activate **FMHY Atlas**.
3. Ensure pretty permalinks are enabled (`Settings → Permalinks → Post name`).
4. Visit the homepage – WordPress will automatically load `front-page.php`. Any other slug will fall back to the remote Markdown renderer.

### Optional GitHub token

Unauthenticated requests are limited to ~60/hour. To raise the ceiling, add this constant to `wp-config.php`:

```php
define( 'FMHY_GITHUB_TOKEN', 'ghp_XXXXXXXXXXXXXXXXXXXX' );
```

### Arabic content overrides

If a translated Markdown file exists under `wp-content/themes/fmhy/content/ar/<path>.md`, it will be served instead of the GitHub version whenever the visitor switches to Arabic. A starter translation for `/privacy/` is included as a reference.

## Updating translations

WordPress loads translations from `languages/fmhy-ar.mo`. To refresh them:

```bash
cd wp-content/themes/fmhy/languages
# edit fmhy-ar.po
msgfmt fmhy-ar.po -o fmhy-ar.mo
```

## Troubleshooting

| Issue | Fix |
| --- | --- |
| Docs show a 404 | The slug may not exist upstream. Confirm the Markdown file is present under `fmhy/edit/docs` (case-sensitive). |
| Rate limited by GitHub | Define `FMHY_GITHUB_TOKEN` as described above or raise the transient TTL. |
| Outdated cache | Clear the `fmhy_doc_*` and `fmhy_doc_tree` transients (`wp transient delete --all` via WP-CLI). |
| Arabic UI only | Use the language toggle in the header or delete the `fmhy_lang` cookie to return to English. |

## Requirements

- WordPress 6.4+
- PHP 8.0+
- CURL + DOM extensions enabled (for remote fetch + HTML parsing)

Enjoy the bilingual, auto-synced FMHY experience! 🎉
