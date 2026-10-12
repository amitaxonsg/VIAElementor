# Syncing the actual WordPress design into this public GitHub repository

**GitHub currently contains implementation notes, guarded migration scripts and reference MU-plugin code. It does not automatically contain the live WordPress database or the exact currently running MU-plugin files.**

## Export from development WordPress (private staging only)

The exporter does **not** push to GitHub and does not export the database, passwords or server configuration. It produces Elementor JSON for pages **21–49**, templates **57/58**, Site Kit **19**, and menu **3** in a private folder.

```bash
WP=/home/amitaxon/public_html
REPO=/home/amitaxon/VIAElementor
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com" || exit 1
runuser -u amitaxon -- wp --path="$WP" eval-file "$REPO/wp-cli/export-review-snapshot.php"
```

Example output directory:
`/home/amitaxon/via-fullsite-deployment/exports/review-YYYYMMDD-HHMMSS-XXXXXXX/`

**DO NOT commit this folder blindly.** All 28 inner pages and some legal/partner material may still be draft or unapproved. Generated JSON can include text, links, email addresses or tokens embedded by a past editor. Carefully inspect and redact the files before publishing to this **public repository**. Approved content and licensed assets can be staged after review.

## Compare live MU-plugin files before tracking exact sources

Current dev server reportedly contains:
- `wp-content/mu-plugins/via-dev-header-fix.php` — local logo crop/geometry workaround;
- `wp-content/mu-plugins/via-uae-global-bridge.php` — menu font/color bridge;
- `wp-content/mu-plugins/via-shared-template-assets.php` — loads Elementor styles for UAE templates.

The `mu-plugins` directory in this repository contains **reference implementations only**. The final current server source has **not** been retrieved directly in this session. Compare any deployed files line-by-line before treating repository copies as canonical. Do not commit anything containing secrets or server-private addresses not intended for the public.

## Establishing the production pipeline

After client approval and a reviewed export, choose one explicit source-of-truth policy:
1. **WordPress-first:** client edits Elementor; an administrator periodically exports, reviews and commits approved JSON.
2. **GitHub-first:** developer modifies approved JSON/CSS in Git; a tested importer deploys to WordPress, and clients avoid editing the same artifacts at the same time.

Do not turn on two-way blind synchronization: Elementor database writes and Git file changes could overwrite each other.

## Before client deployment

Confirm functional dropdowns and mobile hamburger; shared Header 57/Footer 58 on all pages; original logo and local image assets; forms; legal text; client-approved palette; global font/color bindings; link targets; and responsive layouts. Keep a separate WordPress database/files backup, because Git does not back up the complete website.
