# NEXT CHAT HANDOFF — VIA Frontiers Elementor development

**Updated:** 2026-10-13

Use this document first when continuing the project in another chat. Project GitHub: [amitaxonsg/VIAElementor](https://github.com/amitaxonsg/VIAElementor).

## Environment / safety

- `https://axonserver.com` is **development**, NOT the client's production domain.
- Only WordPress cPanel account `amitaxon`, path `/home/amitaxon/public_html`. Do not affect any other site or root settings.
- SSH operations: `runuser -u amitaxon -- wp --path=/home/amitaxon/public_html ...`.
- WordPress theme Astra; Elementor Free; Ultimate Addons Header Footer Builder.
- Global Elementor Kit ID **19** is the **only active branding source**; retired VIA Branding plugin is inactive. Don't reactivate it casually.
- Homepage post ID **21**; VIA inner pages **22–49**; shared UAE header **57**, footer **58**; WordPress menu **3**.
- Private work folder `/home/amitaxon/via-fullsite-deployment/`, backups under `.../backups/` **outside** document root.
- Public GitHub repository: do not commit private backups, complete DB dumps, tokens, unapproved client content, or uploaded image files without approval.
- GitHub scripts are committed, but **GitHub is not a live WordPress database backup**. A commit does not prove it was deployed.

## Approved client branding (authoritative six-swatch reference)

- Navy `#262A63` — primary text, headings, navigation.
- Orange `#F1592D` — CTA, Sylvia's name, slogan emphasis.
- Apricot `#EE8F50` — supporting accents.
- Blue `#7499D0` — “Lasting impact..” and statistic subtitles.
- Stone `#E6E3DE` — borders/dividers.
- Cream `#DFD1B3` — warm supporting surface.
- White `#FFFFFF` as neutral.
- **ONLY two active font families:** **Cormorant Garamond** for large headings and numbers; **Inter** for body, credentials, slogan, navigation, buttons, footer. VIA approved logo is supplied artwork, not a new typeface.
- Elementor Site Kit 19 includes Globals `primary`, `accent`, `via_blue`, `via_stone`, etc. Keep typography/colors linked to global presets. Optional inactive modern choices: Fraunces, Manrope, DM Sans, Plus Jakarta Sans.

## Latest homepage design

Current screenshots show full-width approved background image of Sylvia standing in a bright boardroom (source uploaded as `/home/amitaxon/894bb258-1b36-4047-b698-1e98075683c6.png`, imported into Media Library), with **headline + buttons on left**, and a **single information strip BELOW**. No floating standalone portrait or third credentials card.

The strip contains:
- **Sylvia Fernandes** — **bold Cormorant Garamond, Global Orange**.
- Credentials “Behavioural Change · Leadership Development · Practitioner Development” — **regular Inter**, Navy.
- Native Elementor Free animated **35+** (Years Experience) and **3500+** (Clients), with large Cormorant numbers and Inter/Blue subtitles.
- Large static **APAC** in Cormorant, “Experience” directly below in Inter/Blue, visually matching the counters.
- Hero slogan: **“unearth unconscious drivers.”** Orange and **“Lasting impact..”** Blue, using **Inter ExtraBold**.
- Approved photo background retained. Last requested patch removed the left card's white background/border and the statistics strip's top divider. **The patch was committed and commands given; final on-server execution/visual verification was NOT supplied after that step.** Check before assuming it's installed.

Relevant committed scripts (run dry-run before apply):
- `wp-cli/hero-background-strip.php` — photo hero and lower stats strip, already shown working in user screenshots.
- `wp-cli/apac-stat-display.php` — APAC display, confirmed visually.
- `wp-cli/hero-typography-apac.php` — orange/blue slogan and APAC font sizing, confirmed visually.
- **`wp-cli/hero-clean-typography.php`** — latest patch for no text box, no divider, Sylvia name bold Global Orange, credentials regular Global Inter. Commit to consult: `aecbbece33482cdfced6fd76c95052c14c70ca11`. Confirm the dry run / apply status next time.
- Do **not** rerun old `hero-two-cards.php` or `hero-editorial-cards.php`: user specifically rejected those layouts.

## Shared site structure and prior migration work

- Header **57** includes VIA logo, responsive UAE menu with dropdowns and Orange LET'S TALK CTA. Footer **58** uses approved **white four-column** layout (Brand / Pathways / Explore / Information, Stone divider, copyright/links below).
- Menu 3 includes Individuals → Bye Bye Black Cat; Corporate → Executive Coaching & Leadership Training; Accreditation → Certification Pathway; About / Insights / Contact.
- Elementor Free/UAE menu has plugin-specific control exceptions. Scoped MU-plugin `via-uae-global-bridge.php` reads Elementor Global CSS variables for menu typography and brand colors; another `via-dev-header-fix.php` handled logo crop/height, and `via-shared-template-assets.php` enqueues shared Elementor template styles. **Review active server files before removing/replacing them.**
- Earlier report: 29 VIA pages imported and 31 Elementor docs had **2,341 global style references**, zero unlinked standard font controls; 4 UAE menu controls remained as compatibility exceptions. Those counts are historical; rerun read-only audit.
- About **26** successfully used shared Header 57/Footer 58; homepage **21** underwent shared-template migration iterations. Verify current live page templates/display conditions with `wp-cli/audit-via.php`, not assumptions.
- Four early inner pages used alternate Lovable source exports (22 Individuals, 23 Corporate, 24 Accreditation, 25 Leadership Training). Need client/content and responsive review before production.
- The Lovable project is `a36bb602-17de-434e-9c8c-69b9d89b2551`; source `src/vf/css/home-exact-sample.css`. WordPress editing is the current focus.

## Remaining next steps

1. **Confirm actual current hero** after latest `hero-clean-typography.php`; inspect at desktop/mobile. The user preferred text directly on photo, Sylvia Orange, no divider.
2. Verify Counter and APAC on mobile and scrolling; counters should animate when visible and be editable in Elementor.
3. Run safe audit of shared header/footer conditions and compare homepage/About; preserve current working menu.
4. Refine inner pages 22–49 against Lovable mockups, one by one; pay special attention to alternate source mappings, media URLs, forms, legal copy and responsive layouts.
5. Consider reviewed export of approved Elementor JSON and current MU-plugin sources using `wp-cli/export-review-snapshot.php`. Review/redact before committing to the **public** GitHub repo.
6. Keep all future code in this repo, syntax-check in GitHub, retrieve exact commit via SSH `curl` or `git pull`, run WP-CLI dry-run, take private backup, then apply and flush Elementor CSS.

## Quick SSH commands

```bash
WP=/home/amitaxon/public_html
runuser -u amitaxon -- wp --path="$WP" option get siteurl
runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit
runuser -u amitaxon -- wp --path="$WP" option get page_on_front
# once repo is cloned/pulled:
runuser -u amitaxon -- wp --path="$WP" eval-file /home/amitaxon/VIAElementor/wp-cli/audit-via.php
runuser -u amitaxon -- wp --path="$WP" elementor flush-css
```

## Suggested new-chat opener

> Continue VIA Frontiers Elementor Free development. Read `https://github.com/amitaxonsg/VIAElementor/blob/main/docs/NEXT-CHAT-HANDOFF.md` and latest GitHub scripts. Development site is `axonserver.com`, WordPress account `amitaxon`, Site Kit 19, homepage 21, header 57, footer 58. Confirm whether `hero-clean-typography.php` was applied, then refine the hero/mobile layouts and remaining pages. Approved fonts Cormorant Garamond + Inter only, six-color palette as documented; use direct SSH via GitHub scripts, dry runs and private backups. Do not touch other cPanel sites.

## October 13 follow-up — Pathways heading layout recovery

**Read first:** [Pathways recovery and standards](PATHWAYS-RECOVERY-20261013.md). The user's latest screenshot shows an incorrect left-shift/large whitespace after v1.4/v1.5 parent-level CSS layout experiments. The v1.5 rollback restored v1.4 and therefore did not remove all problematic rules. **GitHub v1.6** (`5634ef80f26bdcebb4c709bfd051a34b40235d36`) is now a clean CSS baseline built from the previously working card design, with 12px small heading, 36px main headings and NO hardcoded font-family names. It has **not** been confirmed deployed yet. Preserve the existing heading split (IDs `0000017`, `a71c9f2`) and do not keep layering grid/flex fixes onto Pathways container `000002c`. Future one-line heading should be created with a native nested Elementor heading row after validating the layout. See recovery document for exact IDs/backups.


## October 13 reference-copy update (not yet deployed)

The user supplied a reference screenshot with the Pathways copy: "**Three paths. One approach.**", Individuals describing "eradicate negativity", Corporate subtitle "Adopt intuitive leadership." and shorter description, Coaches subtitle "Build your practice." and certification statement "Get certified by the Society of NLP, Orlando, USA." The screenshot remains an approved content reference, but the certification claim should be verified with the client.

- Committed guarded copy-only WP-CLI script `wp-cli/update-pathways-reference-copy.php`, commit `19233cbdd8521833291b8d90b1974692ab1f3e03`. Requires Site Kit 19, page 21, exact Pathways structure and known source wording. Dry-run first. Creates private Elementor JSON backup on apply. **Not yet confirmed deployed.**
- The user confirms the clean **v1.6** Pathways card layout is visually good. Do not overwrite its installed stylesheet with experimental v1.4/v1.5 rules. A later repository-only v1.7 stylesheet commit (`fbbe35d6f7e4954bb45ef8b6767059d68739fecc`) references a new heading row `b7621a0` that has **not** been created in Elementor; do not deploy that stylesheet alone.
- Existing split heading widgets are `0000017` (Three paths.) and `a71c9f2` (One approach. after copy script). Their independently editable colours must remain. The requested same-line native Elementor row is still pending; do not force the full Pathways parent into grid/flex again.
- Small eyebrow 12px; large section heading 36px; card heading desired 25px. All font families inherited from Elementor globals. Preserve images and CTA links.

## 2026-10-13 — FINISH outstanding Pathways design (pending SSH installation)

Latest user screenshot confirms: reference COPY change was deployed successfully; **v1.6 card/image layout is working**. Two outstanding visual tasks remain: "Three paths. One approach." on ONE desktop line in TWO separately editable coloured Elementor headings, and three card titles at **25px** (without hardcoded font families). Latest screenshot still displays separate lines and larger card headings.

- **New guarded native row migration**, `wp-cli/group-pathways-headline.php`, commit `51897cfccbf07164458c41021ca4cc625a8617ba`; adds inner Elementor container ID `b7621a0` wrapping EXISTING widgets `0000017` / `a71c9f2` only. DRY RUN FIRST. Requires current title `One approach.`. Does NOT change content, link targets, colours, cards, or Site Kit.
- **Matching CSS**, `mu-plugins/via-pathways-modern.php` **v1.7.0**, commit `fbbe35d6f7e4954bb45ef8b6767059d68739fecc`: based on the working v1.6 layout, adds isolated row styling, makes card-title size exactly 25px, and inherits font families from Elementor Global Typography variables.
- **Reversible structural rollback**, `wp-cli/rollback-pathways-headline.php`, commit `60a161fe8f367149d1196a855a667b88a938c83a`. Ungroups ONLY the two headings. Restore backed-up v1.6 CSS separately if necessary.
- **Deployment NOT yet verified on server**. Send pinned curl commands, `php -l`, WP-CLI `dry-run`, CSS backup, WP-CLI apply, install scoped CSS, `elementor flush-css`, screenshot desktop/mobile. Stop if any guard fails. NEVER reuse prior v1.4/v1.5 parent grid/flex attempts.
- **Remember typography contract**: section eyebrow 12px; main heading 36px; card titles 25px; NO literal font-family declarations; Site Kit 19 owns font family. Keep the two headings independently Elementor-editable and preserve their different corporate colours.

## MOST RECENT VERIFIED RECOVERY CHECKPOINT — 2026-10-13 (supersedes older backup-status notes)

A full VIA development WordPress files + database snapshot completed at `/home/amitaxon/via-fullsite-deployment/backups/full-site/VIA-FULL-20261013-120355/`. The user verified `database.sql: OK` and `wordpress-files.tar.gz: OK` using SHA-256. Snapshot disk usage was about 106 MB; the WordPress root was about 353 MB. A previous incomplete 23 MB attempt is NOT recoverable as a full-site backup. This verified snapshot is **server-local only**, not committed to GitHub, and has **not** been test-restored. Preserve it; arrange an off-server encrypted copy. See [Backup and Recovery](BACKUP-RECOVERY-20261013.md) for exact read-only verification and cautious recovery instructions. Never blindly restore the whole homepage, Header 57 or Footer 58 when a scoped fix is possible. Continue using SSH-first, visual approval second, GitHub commit only for approved changes.

## 2026-10-13 — Modular homepage template creation (newest user request)

The user approved creating **reusable native Elementor Free Flexbox/Container saved templates** for all homepage sections except the shared Header 57 / Footer 58 and photo Hero. Existing `Sylvia counter Flexbox` was already created in the WordPress Elementor Library. New `homepage-` names: `homepage-03-Pathways`, `homepage-04-FIRE-Approach`, `homepage-05-Client-Voices`, `homepage-06-Start-a-Conversation`. GitHub contains the new guarded `wp-cli/build-homepage-templates.php` generator and [template workflow](HOMEPAGE-TEMPLATE-LIBRARY.md). It **has not yet been run** on the server. The generator copies current page content/images/links into new standalone Elementor Library containers and applies responsive Flexbox settings with Site Kit 19 globals, keeping the live site untouched. Use dry-run first; the currently published page data may differ from historical section structure and the script must safely abort. No hardcoded font-family or brand hex values, 12px/36px/25px sizing standards. Validate desktop/tablet/mobile before inserting into any page. User explicitly requested GitHub preparation **before** pulling/importing to server for this batch, an exception to their usual SSH-first workflow.

## 2026-10-13 — Current template builder dry-run status

Current site audit: FIRE top-level section is `b8800dd`, `VIA FIRE Native Rows`; Consultation live layout has exactly 1 heading, 1 text-editor, 1 button widget (not four). Previous importer failed safely on these mismatches. Newest pinned importer code: `0a9c471657d69c1bc90a26584d9eca415ec1fa95`, `wp-cli/build-homepage-templates.php`. It matches the current FIRE section and preserves Consultation's three existing widgets, checking content/settings invariance. **GitHub committed; no successful server dry run or import confirmed yet.** Re-download, PHP lint and dry-run first. Do not change published page 21. Only approve creation after the full dry run validates all four proposed `homepage-` templates.


## 2026-10-13 — New INNER PAGE HERO and Sylvia spacing standard (newest)

The user requested all VIA inner pages (22–49) to have a full-width photographic hero with **editable breadcrumb, page title, introduction, CTA button and optional supporting description** at roughly **half the approved homepage hero height**. New named Saved Container template is `Inner Pages Hero Template`. GitHub creator: `wp-cli/create-inner-pages-hero-template.php` (revision `f477218be96d74c6fae80e33b4f471625a8eca47`). Design: Elementor Free Flexbox, global fonts/colours from Site Kit 19, desktop 35vh minimum, 360px tablet / 420px mobile minimum, and outer padding copied from `Sylvia counter Flexbox`. Background defaults to approved homepage photograph as an **editable placeholder**—replace image and breadcrumb/text for each inner page. No automatic per-page breadcrumb/title dynamic binding in standard Elementor Free saved templates. **Not run/tested on WordPress yet; do dry-run first.** Documentation: [Inner Pages Hero Template](INNER-PAGES-HERO-TEMPLATE.md). Do not automatically insert into published pages yet or overwrite existing heroes.

For future pages, use **Sylvia counter Flexbox outer width/padding as a common design standard**. Previously prepared exact-title script `wp-cli/via-template-match-sylvia-spacing.php` (four homepage templates) and new generic opt-in script `wp-cli/via-template-sync-sylvia-spacing.php` (exact titles specified on CLI) are both tracked. No evidence in this chat that they have been deployed or visually tested; keep as code pending SSH dry run and approval. No hardcoded font-family names, existing branding from Kit 19, 12px small section headings, 36px main headings, 25px card headings where appropriate. Never touch shared Header 57/Footer 58 or live page 21 to create a template.
