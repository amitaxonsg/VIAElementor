# VIA Frontiers — Ten Common Elementor Free Templates (candidate v1)

**GitHub source committed:** [create-ten-common-templates.php](../wp-cli/create-ten-common-templates.php)  
**Status:** Source is in GitHub; **server PHP lint, dry-run, creation and visual/mobile QA are still pending**. This does not alter any existing webpage by itself.

## Ten independently editable Saved Container templates

| Planned Saved Template title | Purpose and what editors change |
| --- | --- |
| `inner-10-FAQ-Accordion` | Editable, native Elementor Free accordion with four example questions and answers |
| `inner-11-Two-Column-Editorial` | Left copy and CTA, right editable blank Image widget and caption |
| `inner-12-Three-Column-Highlights` | Three bordered feature/benefit cards, headings, descriptions and links |
| `inner-13-Interactive-Flip-Boxes` | Three reversible service cards: front/back content are native editable Elementor widgets, with a self-contained, **template-scoped HTML widget** for flip interaction |
| `inner-14-Track-Record-Circles` | Three circular metric examples and explanations. **XX+/XXX+/X are placeholders, not company statistics.** |
| `inner-15-Profile-Biography` | Editable portrait Image widget, heading, professional role, short biography and CTA |
| `inner-16-Services-Overview` | Three service offerings, captions and independent link buttons |
| `inner-17-Coaching-Journey` | Four steps for coaching/service workflow; all language is a sample |
| `inner-18-Classes-Programs` | Three class/programme cards with dates/duration placeholders and enquiry buttons |
| `inner-19-Contact-Enquiry` | Contact intro + two **separately editable** action buttons: `Send an Enquiry →` and `Ask About Coaching →`, both initially pointing to the sample `/contact` URL |

All names begin with `inner-` in accordance with the project's template naming rules. These are **new templates**, not replacements for six existing approved Saved Templates.

## VIA brand and design contract

- Every template's **outer width, content-width and responsive padding** are derived from the actual Saved Template `Sylvia counter Flexbox` (WordPress Library ID **178**). No fixed corporate page margins are baked in.
- Every colour and font references **Elementor Global Kit 19** using Global Color and Global Typography bindings. The generator does **not** change Site Kit 19, or hardcode brand hex/family values. The style convention is **12px eyebrow, 36px main section heading, 25px cards** (responsive reductions where appropriate).
- Styling uses **native Elementor Free Flexbox Containers** and native Elementor Widgets. The only exception is the interactive flip behaviour, whose HTML widget contains **scoped CSS and JavaScript**. Elementor Free does not require a Pro Flip Box widget. Verify this in the frontend because HTML widgets may behave differently inside the editor preview. Check keyboard, touch and reduced-motion behaviour.
- At ~390px mobile width, columns stack; at tablet width, 2-column variants wrap. Actual appearance **not yet visually verified**.
- **Blank Image widgets intentionally have no default photo** to prevent publishing unrelated images on services/profile/course pages. Choose approved licensed assets in Elementor before using those sections.
- All placeholder questions, copy, services, credentials, timings and metrics must be replaced with page-specific, reviewed text. **No unverified claims or invented results should be published.**
- Contact/enquiry template provides two working **link buttons**, not a fake email-sending form. Elementor Free's Pro Form widget is not used. If a real enquiry form is required, embed a verified existing form plugin or shortcode separately with permission.

## What the script does and does NOT do

**Creates only ten `elementor_library` posts** with type `container`. No WordPress pages, theme templates, homepage layouts, menus, headers, footers, plugins, files or Site Kit settings are modified. The script:
- Checks site `https://axonserver.com`, active Kit **19**, front page **21**, approved reference template **178**, available Elementor Free widgets and required Global Style tokens.
- Aborts before writing if *any* proposed new template title is already in the library.
- Builds ten editable Element trees with fresh IDs and validates JSON.
- In apply mode inserts only new library posts and writes a private manifest under `/home/amitaxon/via-fullsite-deployment/backups/`. If saving one fails, it deletes only posts created during that particular failed batch.
- Does **not** flush Elementor's generated CSS; we previously experienced missing Kit 19 CSS after cache regeneration. Do not flush CSS as a reflex.

## SSH: Pull pinned reviewed source and dry-run

**Latest verified private recovery point:** `VIA-FULL-20261013-140913` (~108 MB). User SSH output confirms the backup completed and its database, files and checksums were verified. This is the preferred checkpoint ahead of the previously verified `VIA-FULL-20261013-120355`. Confirm the backup still exists and no backup process is running before applying the ten-template batch; a full staging restore has not been tested.

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-ten-common-templates.php

test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"

curl -fLsS --max-time 30   "https://raw.githubusercontent.com/amitaxonsg/VIAElementor/6b08d13390ae9d9d7769d2078ff3c1866d5a1052/wp-cli/create-ten-common-templates.php"   -o "$SCRIPT"
chown amitaxon:amitaxon "$SCRIPT"
chmod 600 "$SCRIPT"
php -l "$SCRIPT"

runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
```

The dry run must list ten `CREATE` lines and finish `DRY RUN PASSED — zero writes`. If it fails, **stop and share the output**, don't force apply or alter live pages.

## SSH: Apply only after dry run and backup confirmation

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-ten-common-templates.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" apply
runuser -u amitaxon -- wp --path="$WP" post list --post_type=elementor_library --fields=ID,post_title,post_status
```

**Do not run the script again after a successful apply**—its duplicate-name guard will intentionally stop. No generated CSS flush is needed for the library creation operation.

## QA after creating the Saved Templates

1. Go to WordPress → Elementor → Templates → Saved Templates, then open each `inner-` template with Elementor.
2. Test in a **draft page**, NOT an existing published page, at desktop, tablet and 390px mobile widths.
3. Confirm responsive columns, no horizontal scroll, proper CTA button colours, and global font rendering.
4. Test interactive FAQ and flip card with mouse, keyboard, touch, and reduced-motion.
5. Replace sample titles, metrics, images, descriptions and link URLs with approved actual content before publishing.
6. Insert approved containers via **My Templates → Insert**. These are *independent copies*; editing a saved template later does not update copies already inserted.
7. Verify `post-19.css` and `post-21.css` remain present and loaded on the unchanged homepage, without unnecessary cache flushes.

## Existing site protection / rollback

If a new template appears wrong, edit or remove **only that new Saved Template**. Do not restore the full WordPress site or replace existing Homepage 21 / Header 57 / Footer 58. Use the verified private full backup only for serious data loss, and never silently overwrite work done after the snapshot. For the baseline and strict rules, see [Approved Working Baseline](APPROVED-WORKING-BASELINE-20261013.md).

This batch contributes ten candidates toward the broader [36-template roadmap](TEMPLATE-ROADMAP-30-40.md). The roadmap's earlier `inner-02`–`inner-09` names are *planning placeholders*, not additional templates automatically created by this generator. Avoid duplicate functions when planning the remaining work.
