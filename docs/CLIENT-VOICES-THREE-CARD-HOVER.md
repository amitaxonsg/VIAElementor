# VIA Frontiers — Client Voices: three visible cards, gentle floating hover

**Latest request (2026-10-13):** Keep **all three testimonial boxes visible**. Add only a subtle desktop hover lift/shadow. **No carousel, autoplay, arrows, or dots.** This decision **supersedes** [Client Voices Slider v2](CLIENT-VOICES-SLIDER-V2.md). **Do NOT deploy** the old `create-via-testimonial-slider-v2.php` script.

## Scope and source of truth

- **Single approved Saved Container to update:** `homepage-05-Client-Voices`, **ID 198** (from WordPress Elementor Saved Templates).
- The PHP file supplied privately for initial SSH test is `via-client-voices-three-card-hover.php`; upload to `/home/amitaxon/` using cPanel File Manager.
- Script will discover the current three testimonial cards by their existing authors **Marc Hoo, Rebecca Osmond, Antoinette Biehlmeier**, rejecting ambiguity. It does not recreate, edit or expose their quotation contents.
- Original testimonial text, author, professional attribution, links, widget types, fonts, colours and entire three-column layout remain untouched.
- Adds a root CSS class, one CSS class to each card, and **one HTML widget containing CSS scoped only to this saved template**. No CSS in Site Kit 19, global `style.css`, MU-plugin, or Elementor generated files. The CSS uses a neutral shadow, 9px hover lift, ~300ms transition; active only for hover-capable devices. Reduced-motion disables the lift/transition.
- Elementor Free is sufficient; no new plugin or server packages, and no JavaScript/autoplay. Mobile/touch keeps the same three cards, stacking only as already controlled by the existing template's responsive settings.
- No `wp elementor flush-css` after this content-only template update. Existing Kit 19 CSS was previously regenerated after a missing stylesheet issue.
- **Do not edit** Homepage 21, Header 57, Footer 58, Elementor Kit 19, Typography Studio or other templates. Changes are to Saved Template 198 only. Any already-inserted copies on pages remain independent and unchanged.

## SSH — backup-guarded dry run

First ensure the latest known private full-site snapshot `VIA-FULL-20261013-140913` remains available; this script additionally creates a **fresh template-specific before snapshot** when applying.

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/via-client-voices-three-card-hover.php
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"
test -s "$SCRIPT"
php -l "$SCRIPT"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
```

**Do not apply** if it cannot uniquely identify all three authors, or fails any other assertion.

## SSH — apply to Saved Template 198, nothing else

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/via-client-voices-three-card-hover.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" apply
runuser -u amitaxon -- wp --path="$WP" post get 198 --fields=ID,post_title,post_status
```

This prints the exact private backup file path. **Do not run apply twice.** The script refuses to stack a duplicate hover effect. If the prior slider was already installed into a **different** Saved Template, this script leaves that different template untouched.

## Rollback

Only if visual testing reveals a problem:

```bash
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/via-client-voices-three-card-hover.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" rollback "/exact/private/backup/from/apply.json"
```

Rollback verifies that the current Saved Template still matches the saved post-apply hash, so it **refuses to overwrite any later Elementor edits**. No full-site restore should be needed for this small styling change.

## Visual testing

- Preview Template 198 in Elementor; all three cards remain present and editable.
- Test a draft-page frontend (not merely Elementor canvas) on desktop; hover one card and confirm a gentle lift and shadow without moving the others.
- Test tablet and narrow mobile: original layout/wrapping remains readable, no hover effect forced on touch, no horizontal overflow or clipping.
- Test reduced-motion preference (no transform/transition) and confirm existing fonts/colours still inherit Kit 19.
- Record success; then **commit the exact PHP code that passed SSH** to VIAElementor. GitHub does not contain an independent copy of the live Elementor JSON.

[Approved baseline](APPROVED-WORKING-BASELINE-20261013.md) · [Brand guide](BRAND-GUIDE.md) · [Recovery](BACKUP-RECOVERY-20261013.md)
