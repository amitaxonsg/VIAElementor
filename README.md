# VIA Frontiers — Elementor Free

Version-controlled implementation documentation and WordPress migration tools for VIA Frontiers, developed at **https://axonserver.com** (development site, **not** the client's live site).

> **Important:** WordPress stores the current Elementor pages and Site Kit in its database. This repository tracks documentation and reviewed maintenance scripts. GitHub is **not automatically synchronized** with the WordPress database or server MU-plugins.

## Verified WordPress architecture

| Component | Identifier | Where to edit |
| --- | --- | --- |
| Theme | Astra 4.14.0 | Appearance → Customize |
| Page builder | Elementor Free 4.3.4 | Edit with Elementor |
| Global design | Elementor Site Kit **19** | Elementor → Site Settings |
| Home | Page **21** | Pages → Edit with Elementor |
| Inner VIA pages | Pages **22–49** | Pages → Edit with Elementor |
| Shared header | UAE header template **57** | UAE / Elementor Header Footer Builder |
| Shared footer | UAE footer template **58** | UAE / Elementor Header Footer Builder |
| Navigation | WordPress menu **3** | Appearance → Menus |

**Design contract:** single shared header/footer on all VIA pages; Elementor Global Fonts and Global Colors supply the branding; individual containers retain responsive widths, padding and gaps. Avoid duplicating header/footer in page content.

## Approved brand defaults

**Colors:** Navy `#262A63`, Orange `#F1592D`, Apricot `#EE8F50`, Blue `#7499D0`, Stone `#E6E3DE`, Cream `#DFD1B3`; white is a neutral surface.

**Fonts:** Cormorant Garamond (headings), Inter (body, navigation, dropdowns, buttons, footer). Optional presets for evaluation only: Fraunces, Manrope, DM Sans, Plus Jakarta Sans. Preserve the client-approved pair until instructed otherwise.

See [Brand Guide](docs/BRAND-GUIDE.md).

## Edit and administer

- [Elementor editor handbook](docs/EDITOR-GUIDE.md) — global colors and fonts, buttons, navigation dropdowns, page titles, header/footer, mobile.
- [WordPress page inventory](docs/PAGES-AND-STATUS.md) — document IDs, route mappings, alternate Lovable source warnings.
- [Deployment and rollback](docs/OPERATIONS.md) — guarded WP-CLI operations, backup and validation.
- [Implementation changelog](CHANGELOG.md).

## Scripts

- `wp-cli/audit-via.php` — read-only audit of page status, template scope, global references, and overrides.
- `wp-cli/unify-shared-layout.php` — **dry-run by default**, guarded migration to shared Header 57/Footer 58 for pages 21–49; backs up data and removes only duplicate header/footer sections on home.
- `wp-cli/rollback-shared-layout.php` — restore snapshot created by that migration.

**Scripts are committed but not yet confirmed executed on the server.** Run via `runuser -u amitaxon -- wp --path=/home/amitaxon/public_html eval-file ...` only after dry-run verification.

## Public repository safety

Never commit `wp-config.php`, secrets, private backups, database dumps, user records, or unpublished private content. The actual Elementor JSON, images and MU-plugins need an explicit **reviewed export** from the development server before they can be represented as synchronized copies here.

## Current migration checkpoint

The prior server logs reported 29 VIA pages with Elementor content and **2,341** Global Style references across 31 documents (pages plus Header 57 / Footer 58). About page **26** was previewed successfully with shared templates. The homepage **21** still appeared to use its own embedded header/footer in the last screenshots. This repository's migration script addresses that difference but does **not** claim it has been deployed.