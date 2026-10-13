# VIA Frontiers — Pathways Image Cards v2 (candidate)

**Status:** User-selected concept converted into an Elementor Free Saved Container creator, awaiting **on-server dry run and visual approval**. Existing Homepage 21 and Saved Template 196 must remain unchanged.

## Approved concept

Reference supplied 2026-10-13: 3 horizontally aligned white cards, each with its own **image at the top**, overlapping orange-on-cream circular icon badge, separately editable title, 12px eyebrow/strapline, orange divider, description and orange rounded CTA. Card titles:

1. Individuals — label: `GREATER AWARENESS. A BRIGHTER YOU.`
2. Leaders & Organisations — label: `STRONGER PEOPLE. BRIGHTER CULTURES.`
3. Coaches & Practitioners — label: `DEEPER SKILLS. GREATER IMPACT.`

All other approved card text and existing CTA destinations are copied from the currently published Saved Container **`homepage-03-Pathways` (ID 196)** at import time rather than invented. Editors can change every image, icon, text, button and destination in Elementor afterward.

## Template data and assets

- **Create-only new name:** `homepage-03-Pathways-Image-Cards-v2` (not overwriting the original)
- **Outer spacing reference:** Saved Template **`Sylvia counter Flexbox` ID 178**, using its actual current width/padding and responsive values
- **Branding:** Site Kit **19** Global Fonts `primary`, `text`, `via_small_labels`, `via_buttons`; Global Colours `primary`, `accent`, `via_cream`, `via_white`, `via_stone`
- **Native styling:** Elementor Free Containers/Headings/Text Editors/Buttons/Icons/Images, no website-level CSS patch or Kit changes. Native image/button hover animation settings are editable.
- **Responsive:** desktop 3 columns, tablet 2+1 where space permits, mobile one card per row, full-width cards and images; requires visual testing at ~390px
- **Photos:** private `via-pathways-v2-images.zip` uploaded to `/home/amitaxon/`, containing three editable starter JPEGs **cropped from the supplied reference screenshot**. These are small mockup crops upscaled for convenience—not independent high-resolution licensed originals. Replace them with approved original photography for production. **No media binaries are stored in public GitHub.**
- **Permissions:** Script writes only a new `elementor_library` template and three new Media Library images when run with `apply`, with creation-failure cleanup. It doesn't change Site Kit 19, homepage 21, Header 57, Footer 58, or existing template 196. A private creation manifest is written under `/home/amitaxon/via-fullsite-deployment/backups/`.
- **Backups:** Last user-verified complete backup before this work: `VIA-FULL-20261013-140913`, database/files/checksums verified. Preserve it.

## Installation route

The initial *local PHP creator*, `create-via-pathways-image-cards-v2.php`, is supplied to the site administrator with the private image ZIP, for cPanel File Manager upload into `/home/amitaxon/`. It should be **reviewed and dry-run on the target WordPress environment before a GitHub implementation commit**, following the current SSH-test-first approval workflow. This document tracks the proposal and rules; it does not assert the code has been deployed.

After upload:

```bash
set -e
WP=/home/amitaxon/public_html
PHP=/home/amitaxon/create-via-pathways-image-cards-v2.php
ZIP=/home/amitaxon/via-pathways-v2-images.zip
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"
test -s "$PHP"
test -s "$ZIP"
php -l "$PHP"
runuser -u amitaxon -- wp --path="$WP" eval-file "$PHP" dry-run
```

**Only after** a passing `DRY RUN PASSED — zero writes`, ensure the existing backup remains available and apply:

```bash
WP=/home/amitaxon/public_html
PHP=/home/amitaxon/create-via-pathways-image-cards-v2.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$PHP" apply
runuser -u amitaxon -- wp --path="$WP" post list --post_type=elementor_library --fields=ID,post_title,post_status
```

Do **not** run `wp elementor flush-css` unless a documented CSS regeneration need is demonstrated. VIA's Global Kit 19 stylesheet previously went missing after cache regeneration, and is part of the frozen baseline.

## QA / approval

Preview the new Saved Template on a draft page (not homepage) in Elementor. Verify each image is correctly attached and replaceable; titles/taglines/body text are separately editable; all three original CTA links are preserved and work; mobile images do not crop faces/text; no horizontal scrolling; Global Fonts and Colours resolve; hover effects don't interfere with touch.

Once visually approved and tested, commit the reviewed **same PHP source** to GitHub and note the created template/media IDs. It is a **new** Saved Template: existing inserted copies of the original Pathways section do not change automatically.

Related: [Approved Working Baseline](APPROVED-WORKING-BASELINE-20261013.md) · [Brand Guide](BRAND-GUIDE.md) · [Template Roadmap](TEMPLATE-ROADMAP-30-40.md).
