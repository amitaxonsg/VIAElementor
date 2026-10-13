# Changelog

## 2026-10-13 — Client Voices Carousel v3 candidate

- Revised the testimonial concept to one-at-a-time automatic slides with explicit Pause/Play, arrows, dynamic dots for duplicated slides, mobile swipe, keyboard and reduced-motion support.
- Prepared new **create-only** `homepage-05-Client-Voices-Carousel-v3` installer to clone real existing testimonials from Saved Template ID 198, preserving editable quote/name/attribution Elementor widgets.
- Local PHP syntax, embedded JavaScript syntax and mocked read-only WP-CLI dry-run passed. **Server dry run / Saved Template creation / mobile visual approval have not yet been confirmed.** PHP implementation remains private pending SSH-first approval; [GitHub guide](docs/CLIENT-VOICES-CAROUSEL-V3.md).
- Earlier 3-visible-card hover-only direction and v2 slider are superseded, retained only as historical proposals.

## 2026-10-13 — FIRE timeline template candidate

- Added `wp-cli/create-via-fire-timeline-template.php` for new `inner-20-FIRE-Timeline`, a separate native Elementor Free vertical numbered FIRE-process template matching the approved four-row concept.
- Global Kit 19 colours and typography, Sylvia reference spacing, completely editable text and markers; no hardcoded fonts, injected CSS, sitewide changes, cache flush, or existing page modifications.
- Local PHP syntax and simulated read-only dry-run passed; server creation and responsive approval **still pending**. [Editing and SSH guide](docs/FIRE-TIMELINE-ELEMENTOR-TEMPLATE.md).

## 2026-10-13 — Latest full-site backup verified (14:09 local)

- User SSH output confirms private full WordPress recovery snapshot `VIA-FULL-20261013-140913` (~108 MB), with database, website archive and checksum verification reported successful.
- This is the preferred newest recovery checkpoint ahead of older `VIA-FULL-20261013-120355`; no backup archives or SQL files were committed. Historical pending-backup wording in earlier entries is superseded by this verified status.
- The previous incomplete `120013` folder remains and is not considered a valid backup. Full staging restore and independent off-server storage are not yet confirmed.


## 2026-10-13 — Ten common editable responsive templates prepared

- Added [ten new candidate Saved Container template builder](wp-cli/create-ten-common-templates.php) and [editing/QA guide](docs/TEN-COMMON-ELEMENTOR-TEMPLATES.md).
- Layouts: FAQ accordion, 2-column editorial, 3-column cards, flip boxes, circular track record, profile, services, coaching, classes/programmes, and contact/enquiry with two editable CTAs.
- All fonts/colours inherit Elementor Site Kit 19 globals, outer spacing from Sylvia Counter Flexbox ID 178, tablet/mobile stacking, and sample text/counter values explicitly marked as placeholders. Flip Cards use a template-scoped HTML widget for the only custom interaction. No Elementor Pro required for saved templates.
- **GitHub source prepared only**; server lint/dry-run/import and responsive visual approval not yet recorded. No existing templates, header, footer, homepage or published pages were altered by these commits.

## 2026-10-13 — APPROVED styling freeze, current Saved Templates, and 36-template roadmap

- User confirmed the Homepage 21 branding and layout are **working again** after Elementor Global Kit 19 generated stylesheet `post-19.css` was restored/relinked. Site Kit 19 retained the approved Orange/Navy/Blue variables; no template data or Global Colour values were deleted. The initiating event is not conclusively known. **Do not flush CSS, edit generated stylesheets, reset Typography Studio, or replace approved pages blindly.**
- Established [approved working baseline](docs/APPROVED-WORKING-BASELINE-20261013.md), with current real Elementor IDs and “do not change” rules for future work.
- Confirmed six Saved Container templates with IDs **178, 196, 197, 198, 199, 218**, including `Inner Pages Hero Template`. See [30–40 Template Roadmap](docs/TEMPLATE-ROADMAP-30-40.md) proposing **36** total templates (six existing + thirty future concepts), delivered in small verified batches.
- Added [read-only baseline auditor](wp-cli/audit-approved-via-baseline.php) to check the live Kit 19 globals, generated CSS and Saved Template records before/after major operations.
- Documented [detached after-template backup procedure](docs/OPERATIONS.md), running the pre-existing server-local full backup creator under `amitaxon`, with completion/log validation and both SHA-256 checks. **This new after-template backup is still pending SSH execution and verification; do not call it complete.**
- The previous verified private snapshot `VIA-FULL-20261013-120355` remains available but predates the newer Saved Templates. Do not put any SQL, backups or sensitive data into this public repo.
- Existing scripts and historical migration notes are kept for traceability; current approved pages and template IDs have precedence over older not-yet-deployed text.


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
