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

## Continue in another chat

**[Latest project handoff and next steps](docs/NEXT-CHAT-HANDOFF.md)** — current hero with supplied background photograph, animation counters, approved typography, pending visual checks and SSH workflow. Read this first before further changes.

## Edit and administer

- [Elementor editor handbook](docs/EDITOR-GUIDE.md) — global colors and fonts, buttons, navigation dropdowns, page titles, header/footer, mobile.
- [WordPress page inventory](docs/PAGES-AND-STATUS.md) — document IDs, route mappings, alternate Lovable source warnings.
- [Deployment and rollback](docs/OPERATIONS.md) — guarded WP-CLI operations, backup and validation.
- [Reviewed WordPress-to-GitHub export](docs/SYNC-TO-GITHUB.md) — capture actual Elementor JSON without publishing unreviewed content.
- [Implementation changelog](CHANGELOG.md).

## Scripts

- `wp-cli/audit-via.php` — read-only audit of page status, template scope, global references, and overrides.
- `wp-cli/unify-shared-layout.php` — **dry-run by default**, guarded migration to shared Header 57/Footer 58 for pages 21–49; backs up data and removes only duplicate header/footer sections on home.
- `wp-cli/rollback-shared-layout.php` — restore snapshot created by that migration.

**Scripts are committed but not yet confirmed executed on the server.** Run via `runuser -u amitaxon -- wp --path=/home/amitaxon/public_html eval-file ...` only after dry-run verification.

## Public repository safety

Never commit `wp-config.php`, secrets, private backups, database dumps, user records, or unpublished private content. The actual Elementor JSON, images and MU-plugins need an explicit **reviewed export** from the development server before they can be represented as synchronized copies here.

## Current migration checkpoint

Prior server logs reported 29 VIA pages with Elementor content and **2,341** Global Style references across 31 documents (pages plus Header 57 / Footer 58). The latest screenshot shows the **approved photographic homepage hero** with lower Sylvia/counter strip. Exact server status of the most recent typography cleanup and shared-header/footer deployment still requires fresh WordPress verification; see [handoff](docs/NEXT-CHAT-HANDOFF.md). The repository contains guarded scripts but does **not** claim they have all been deployed.
## Verified recovery checkpoint (2026-10-13)

A private full WordPress files + database snapshot was completed on the development server and both SHA-256 checksum checks passed. **Do not upload the backup archives to this public GitHub repository.** Read [Backup & Recovery — 2026-10-13](docs/BACKUP-RECOVERY-20261013.md) before risky changes. GitHub is documentation, **not** the backup itself.

## Reusable homepage Flexbox templates (candidate)

Four new **native Elementor Free Container-template builders** are documented in [Homepage Template Library](docs/HOMEPAGE-TEMPLATE-LIBRARY.md), with guarded dry-run/apply code in [build-homepage-templates.php](wp-cli/build-homepage-templates.php). They clone the latest approved Pathways, FIRE, Client Voices and Start a Conversation sections from WordPress page 21 into **separate library templates**, without modifying the live homepage. The existing Sylvia counter Flexbox template remains in place. **The user subsequently confirmed creating templates 196–199 and 218, with previews.** This repository's original builder code does not necessarily include later Elementor edits saved directly in WordPress. No private page exports are committed.


## Inner-page hero template and shared spacing rule

- [Inner Pages Hero Template](docs/INNER-PAGES-HERO-TEMPLATE.md) is a proposed **full-width native Elementor Free** hero for pages 22–49: editable background photograph, breadcrumb, H1 title, introduction, CTA button and optional description. Minimum desktop height 35vh (about half the approved 70vh homepage hero); tablet/mobile content can expand. Its outer padding is read from `Sylvia counter Flexbox`. [Guarded creation script](wp-cli/create-inner-pages-hero-template.php) is **committed but not yet confirmed imported/tested** on WordPress.
- **All future Saved Container templates** should follow the approved Sylvia outer width/padding reference. The [opt-in generic spacing updater](wp-cli/via-template-sync-sylvia-spacing.php) accepts exact Saved Template titles and supports dry-run, backup, apply and rollback. Do not apply it to all templates blindly, or to live pages. A separate [four-homepage-template updater](wp-cli/via-template-match-sylvia-spacing.php) is preserved for the previously approved four-template batch.
- Keep title/copy/buttons editable in Elementor; Elementor Free saved templates insert independent copies and do not automatically update previous page insertions.


## Current approved working baseline (2026-10-13)

**The user confirmed the homepage branding is working again** after Elementor Global Kit 19 CSS was regenerated. The approved design must remain unchanged during the next 30–40 reusable template builds. Read the [Frozen Working Baseline & Do-Not-Touch Rules](docs/APPROVED-WORKING-BASELINE-20261013.md) before changing any page/section CSS, Kit colours, Header 57, Footer 58 or page 21.

The project has **six user-confirmed Saved Templates** (`Sylvia counter Flexbox` ID 178, homepage section templates IDs 196–199, and `Inner Pages Hero Template` ID 218), plus an existing `mainpage` library entry ID 183. Older “not yet imported” notes in historical sections of this repository refer to an earlier stage; the latest approved baseline supersedes them.

[Plan for about 36 Saved Container templates](docs/TEMPLATE-ROADMAP-30-40.md) — six existing plus 30 proposed concepts, built in staged groups with the Sylvia template as the outer width/padding reference. This is a **plan, not an assertion the remaining templates have been created**.

**Latest verified private server backup:** `VIA-FULL-20261013-140913` (~108 MB), WordPress database and file archive checksums verified by the user's SSH output. It is a newer checkpoint than `VIA-FULL-20261013-120355` and includes the server's state at 14:09:13 (+08:00). See [Recovery and snapshots](docs/BACKUP-RECOVERY-20261013.md). Do not upload SQL archives, configuration, or private recovery files to GitHub; an off-server copy and test restore are still recommended.


## Ten common responsive Elementor templates (candidate)

Prepared [ten reusable Elementor Free Container templates](docs/TEN-COMMON-ELEMENTOR-TEMPLATES.md): FAQ accordion, two-column editorial, three-column highlights, editable flip cards, circular track record, profile biography, services overview, coaching journey, classes/programmes, and a contact/enquiry layout with **two independently editable CTA links**. The guarded source generator is [`wp-cli/create-ten-common-templates.php`](wp-cli/create-ten-common-templates.php).

**Status:** Committed to GitHub for SSH pull, but **not yet server linted, dry-run, imported, or visually approved**. Creates ten new Saved Template records only; neither the homepage nor any published inner pages change. Templates use Elementor Site Kit 19 Global Fonts/Colours and Sylvia counter Flexbox's current outer width/padding. The Flip Boxes template alone includes template-scoped HTML/CSS/JavaScript because Elementor Free's native Flip Box is a Pro feature; its text stays editable as native Elementor widgets.

## Reusable FIRE timeline template (candidate)

A new *non-destructive* Elementor Free timeline design, **`inner-20-FIRE-Timeline`**, is available as a [guarded WP-CLI Saved Container creator](wp-cli/create-via-fire-timeline-template.php) with [design/installation guide](docs/FIRE-TIMELINE-ELEMENTOR-TEMPLATE.md). Based on the user-approved F/I/R/E screenshot: four numbered timeline steps, editable navy circles, orange dividers and individual Heading/Text widgets. Uses Global Kit 19 fonts/colours and existing Sylvia counter template (ID 178) for outer width/padding. **GitHub source committed; WordPress installation and visual QA not yet confirmed.** Existing live FIRE section `b8800dd` and Saved Template 197 are left untouched.

## Pathways Image Cards v2 (candidate)

The selected Pathways redesign (three **top-image** cards with editable icon, title, tagline, original text and CTAs) is documented in [Pathways Image Cards v2](docs/PATHWAYS-IMAGE-CARDS-V2.md). A new `homepage-03-Pathways-Image-Cards-v2` Saved Container is proposed, without overwriting the existing `homepage-03-Pathways` template or any live page. The code/assets are supplied privately to the administrator for server dry-run and visual approval before the final implementation commit; image crops are not stored on public GitHub. Uses Site Kit 19 Global Styles and Sylvia 178 outer padding.


## Client Voices one-at-a-time slider (SSH candidate)

A new separate Elementor Free Saved Template proposal, `homepage-05-Client-Voices-Slider-v2`, is documented at [Client Voices Slider v2](docs/CLIENT-VOICES-SLIDER-V2.md). It will clone actual approved quote/author widgets from existing Template **198**, keeping their editing controls intact, and show one quote at a time with 7-second autoplay, arrows, dots and mobile swipe. One template-scoped HTML widget provides the animation (no Pro or extra plugin), with Global Kit 19 styles, outer spacing from Sylvia 178, reduced-motion support and zero global CSS flush. **PHP is prepared for cPanel upload, but not yet committed or deployed** under our SSH-test-first source policy. Live homepage, Header 57/Footer 58 and existing Template 198 remain unchanged.
