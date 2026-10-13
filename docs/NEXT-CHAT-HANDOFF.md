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
