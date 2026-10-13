# VIA Frontiers — Client Voices Carousel v3 (latest requested design)

**Status:** User explicitly asked to return to a one-at-a-time **automatic animated carousel with Pause / Play** on 2026-10-13. This **supersedes the previous request** for three cards visible with floating hover, and the older Slider v2 proposal. The goal is a **separate new** Elementor Free Saved Container, **not** overwriting Template 198. PHP implementation is prepared for upload but **has not passed on-server dry run or installation yet**.

## Approved behavior

- One testimony displayed at a time, gentle **450ms fade + vertical entrance**, automatically advances **every 7 seconds**.
- Clearly visible **Pause / Play**, previous/next arrow controls, and navigation dots, with selected dot state.
- Supports mobile touch swipe, desktop keyboard Left/Right, pausing automatically on hover, keyboard focus and when offscreen/hidden.
- Honors `prefers-reduced-motion`: animations and autoplay disabled initially for affected users, with manual navigation still available.
- No Elementor Pro, no new WP plugin, no server modules/packages.
- The three source testimonials, author names and attributions are copied as **native Elementor editable widgets**, with the source template's actual content preserved at install time. The source Saved Template is **ID 198**, `homepage-05-Client-Voices`. Users can edit quotes, author names and role/organisation within each card.
- **Easy to add a fourth testimonial:** open this new Saved Template in Elementor, duplicate any existing Slide container under `Three editable testimonial slides`, edit its existing quote/author/attribution widgets. Controls build navigation dots from the actual slide count on page load. No JS editing is required.
- Slider behavior lives in **one HTML widget** with strictly scoped CSS and JS. That is the only reason non-native CSS/JS is used. Global Brand styles are never modified. If JavaScript fails to load, the first card remains visible rather than showing a blank area.
- Preserves Elementor Kit 19 Global Colours/Fonts and copies outer width, padding and responsive settings from Saved Template **178 Sylvia counter Flexbox**.
- No hardcoded brand hex or font-family strings, no `wp elementor flush-css` during library import.

## Scope

**New target title:** `homepage-05-Client-Voices-Carousel-v3`

The script only creates a **new** `elementor_library` Saved Container. It does not edit:
- Homepage 21, Header 57, Footer 58, Elementor Kit 19
- Existing testimonial Saved Template 198 (which may have three-visible-card hover styling)
- Typography Studio or any approved page copies
- Other Saved Templates, Media Library or plugins

It refuses to overwrite the new target if it already exists. Checks real current source testimonial author names (Marc Hoo, Rebecca Osmond and Antoinette Biehlmeier); aborts if source structure is unexpected. Creates a private manifest in `/home/amitaxon/via-fullsite-deployment/backups`.

The current latest **previously verified** site recovery snapshot is `VIA-FULL-20261013-140913`. Verify the actual backup files still exist before applying.

## SSH — dry run FIRST

Upload the file linked in chat to the user's standard cPanel folder:

`/home/amitaxon/create-via-client-voices-carousel-v3.php`

```bash
bash -Eeuo pipefail <<'BASH'
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-via-client-voices-carousel-v3.php
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"
test -s "$SCRIPT"
php -l "$SCRIPT"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
BASH
```

The dry run must verify three original authors and finish **`Success: DRY RUN PASSED — zero writes`**. Do NOT use `apply` if it fails.

## SSH — create one separate Saved Template

Only after successful dry run, user permission, and confirmation that recovery snapshot files still exist:

```bash
bash -Eeuo pipefail <<'BASH'
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-via-client-voices-carousel-v3.php
BACKUP=/home/amitaxon/via-fullsite-deployment/backups/full-site/VIA-FULL-20261013-140913
test -s "$BACKUP/database.sql"
test -s "$BACKUP/wordpress-files.tar.gz"
test -s "$BACKUP/SHA256SUMS.txt"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" apply
runuser -u amitaxon -- wp --path="$WP" post list --post_type=elementor_library --fields=ID,post_title,post_status
BASH
```

**No global Elementor CSS flush** and **no old template replacement**.

## Visual QA

Preview **the new Carousel v3 Saved Template** in Elementor and test its frontend on a *draft* page, at desktop, tablet and 390px mobile. Check:

- First Marc, then Rebecca, then Antoinette, with complete original quote text and attributions.
- Pausing holds the current slide until Play, manual arrows/dots still work while paused, swipe works on mobile, keyboard focus pauses playback.
- Duplicate one testimonial card in the saved template to verify slide count/dots automatically update; then delete that temporary test or save approved fourth real quote.
- Source Template 198 and Homepage 21 still display their original styles. Check `post-19.css` global definitions remain available.

**GitHub source policy:** commit the exact tested PHP implementation only **after the user's SSH run and visual confirmation**, per the project workflow. This documentation is a candidate spec and does not assert the new template is installed.

[Approved project baseline](APPROVED-WORKING-BASELINE-20261013.md) · [Brand Guide](BRAND-GUIDE.md) · [Previously superseded three-card hover proposal](CLIENT-VOICES-THREE-CARD-HOVER.md)
