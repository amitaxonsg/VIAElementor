# VIA Frontiers — Client Voices Slider v2 (obsolete; replaced by Carousel v3)

**Latest user decision:** Create the [Client Voices Carousel v3](CLIENT-VOICES-CAROUSEL-V3.md), which includes Pause/Play and dynamically generated dots when additional testimonial cards are duplicated. Do not install v2, which predates these requirements. Older v2/hover reversals below are historical.

**Latest user decision:** Do **not** use slides. Keep all three testimonial cards visible and use only a subtle floating hover effect. See the current [three-card hover specification](CLIENT-VOICES-THREE-CARD-HOVER.md). The older PHP slideshow creator described below should **not** be uploaded or executed. The remaining content is retained only for historical traceability.

**Source status:** PHP file prepared for administrator upload; **server dry run and visual approval pending**. The PHP implementation is intentionally **not yet committed** to public GitHub under the established SSH-test-first rule.

## User request and source

Convert the current three-column testimonial display into an accessible animated slideshow presenting **one testimonial at a time**. Read the existing real testimonial content from approved Saved Container Template **198**, `homepage-05-Client-Voices`, rather than generating testimonials or changing testimonials in public Git.

The three current author names checked by the creator are `MARC HOO`, `REBECCA OSMOND`, and `ANTOINETTE BIEHLMEIER`. All quoted text, attribution widgets and source widget settings are deep-copied from the CURRENT WordPress database.

## Template implementation

- **New title:** `homepage-05-Client-Voices-Slider-v2`.
- **Never overwrite** existing Saved Template 198 or homepage 21, shared Header 57/Footer 58, Elementor Kit 19, or any other post.
- **One quote visible at a time**, gentle 450ms fade / vertical entrance; switches every 7 seconds.
- Previous/next arrows, position dots, keyboard Left/Right navigation and mobile swipe gesture.
- Pauses autoplay when the pointer is over the section, the carousel contains keyboard focus, or the page is hidden/off screen. Disables autoplay and animation for prefers-reduced-motion.
- Slider is implemented using editable native Elementor Free Containers/quote widgets plus **one HTML widget with strictly template-scoped CSS and JavaScript**, not Elementor Pro, a new plugin, or a global stylesheet. Cards remain visible in the Elementor editor where possible for individual editing.
- **Global styles:** Kit 19 `primary`, `accent`, `via_stone`, `via_white`, `via_cream` and existing source widget font bindings; **no hardcoded font-family or corporate hex values**. Outer widths and responsive padding are taken from the current `Sylvia counter Flexbox` (ID 178), not guessed.
- No Elementor CSS cache flush; previous generated `post-19.css` outage must not recur.
- Elementor Free Saved Templates create copies when inserted. Later editing this Saved Template does not automatically replace an older inserted copy.

## Installation sequence

A file named `create-via-testimonial-slider-v2.php` has been prepared for download in the chat. Upload it via cPanel to `/home/amitaxon/create-via-testimonial-slider-v2.php`, **outside the webroot**.

Run dry run FIRST:

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-via-testimonial-slider-v2.php
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"
test -s "$SCRIPT"
php -l "$SCRIPT"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
```

If, and only if, the result is `DRY RUN PASSED — zero writes`, the current private recovery snapshot `VIA-FULL-20261013-140913` has been verified/preserved, and the user approves, then create the new saved template:

```bash
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-via-testimonial-slider-v2.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" apply
runuser -u amitaxon -- wp --path="$WP" post list --post_type=elementor_library --fields=ID,post_title,post_status
```

The script refuses to overwrite its target name and writes a private creation manifest to `/home/amitaxon/via-fullsite-deployment/backups/`.

## Mandatory QA before use

Check editor and frontend on a **draft page** at desktop, tablet and 390px mobile; all three exact authors/quotes; one-at-a-time carousel, next/prev/dot actions, pause on hover/focus, touch swipe, keyboard navigation and reduced-motion; correct font and colour variables; no horizontal overflow. Verify old template 198 and Homepage 21 are unchanged. After successful on-server deployment and visual approval, **commit the exact tested PHP implementation** to GitHub and record new template ID/screenshots.

[Approved Baseline](APPROVED-WORKING-BASELINE-20261013.md) · [Brand Guide](BRAND-GUIDE.md) · [Template Roadmap](TEMPLATE-ROADMAP-30-40.md).
