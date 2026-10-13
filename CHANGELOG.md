# Changelog

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
