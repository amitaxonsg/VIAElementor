# Changelog

## 2026-10-13 — Reusable inner-page hero and template spacing standard

- Added `wp-cli/create-inner-pages-hero-template.php` to create a separate full-width, 35vh desktop native Elementor Container template named **Inner Pages Hero Template**, with editable background, breadcrumb, heading, intro, button and optional copy, following Sylvia's outer padding and Site Kit 19 Global Styles.
- Added `docs/INNER-PAGES-HERO-TEMPLATE.md` documenting manual per-page insertion, page-specific image/breadcrumb editing, mobile layout and safe dry-run/apply procedure. **The new template has not yet been created or visually verified on the WordPress server.**
- Tracked the existing four-homepage-template Sylvia spacing tool and prepared an opt-in generic saved-template spacing updater for future VIA pages. Neither script changes live page content automatically; server-side testing pending.

## 2026-10-13 — Four modular homepage templates prepared for testing

- Added guarded Elementor Free saved-Container builder for Pathways, FIRE Approach, Client Voices, and Start a Conversation, using existing page 21 content instead of recreating copy or assets.
- Preserved Site Kit 19 global fonts/colours with native responsive Flexbox layout. The existing Sylvia counter Flexbox template remains untouched.
- Source is reviewed construction code; templates have **not yet been imported, visually validated or published** on the development site. Live homepage, Header 57 and Footer 58 remain unchanged by this repository commit.

## 2026-10-13 — Verified server-local recovery checkpoint

- Documented successful private backup `VIA-FULL-20261013-120355` (approximately 106 MB) with both SQL and WordPress file archive SHA-256 checks reported OK.
- Added [recovery checkpoint manifest and safe verification notes](docs/BACKUP-RECOVERY-20261013.md). The archive, SQL database, credentials and any sensitive data **were not** uploaded to GitHub. A full restore test is still pending.

## 2026-10-13 — Latest photographic homepage hero and handoff

- Added native Elementor hero tools for the approved photo background with headline/buttons on the left and **one lower Sylvia/credential/animated-statistics strip**. The previous card layouts were rejected.
- Added editor-controlled animated **35+** and **3500+** counters, large APAC + Experience and Global-font/color styling.
- Added slogan update: **unearth unconscious drivers.** in Global Orange, **Lasting impact..** in Global Blue.
- Added latest `wp-cli/hero-clean-typography.php` patch: remove left editorial box and statistics divider; style **Sylvia Fernandes** bold Global Orange, credentials regular Inter/Navy. Its GitHub source is committed; last on-server execution is not independently verified.
- Added [NEXT-CHAT-HANDOFF](docs/NEXT-CHAT-HANDOFF.md) with environment, source-of-truth boundaries, precise status and next steps.
- No new claim that the actual WordPress database or private image assets have been uploaded to this public repository.

## 2026-10-13 — Repository initialization

- Documented the VIA Frontiers development environment, approved six-color palette and font pairing.
- Recorded the 29-page map, shared Header 57, Footer 58, navigation menu 3 and Site Kit 19.
- Added editor guidance for typography, colors, button styles, page titles, dropdowns, footer and responsive views.
- Added guarded, backup-based WP-CLI tooling to audit and unify Elementor Full Width pages with shared header/footer templates.
- **No WordPress server write is implied by this commit.** This repo does not yet include a verified export of the server's full Elementor JSON or active MU-plugin source.

## Previous development work — based on server logs

- Installed Astra and Elementor Free; tested homepage 21.
- Imported first-pass Elementor layouts for inner pages 22–49.
- Built native header/footer drafts 57/58, the UAE navigation dropdowns, and a four-column footer.
- Created Elementor Site Kit 19 Global Colors and Fonts, including client-approved fonts/colors and optional modern Google Fonts.
- Reported migration of 31 documents to 2,341 Elementor Global Style references with no unlinked standard fonts; UAE-specific menu styles use a compatibility bridge.
- Verified shared header/footer visually on About 26; homepage still needed shared-template unification in last screenshots.

These historical changes occurred on WordPress and are **documented**, not represented as full files in this repository until an explicit, reviewed server export.
