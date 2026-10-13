# VIA Frontiers — Inner Pages Hero Template

**Status:** GitHub source prepared. **Not yet deployed or visually tested on WordPress.**  
**New Elementor Saved Container name:** `Inner Pages Hero Template`

## Design / use

- Intended for VIA inner pages **22–49** below the shared Header **57** and above each page's content/templates. Shared Footer **58** remains unchanged.
- **Full-width hero background photograph**, copied as an initial *replaceable placeholder* from the approved Homepage 21 photo hero. Each inner page should use its own approved, relevant image; don't assume the default is automatically suitable everywhere.
- **Breadcrumb**, Page Title (H1), introductory description, **Start a Conversation** button linking to `/contact`, and optional supporting description. These are separate **native editable Elementor Free widgets**.
- **Minimum desktop height 35vh**, about half of the approved Homepage Hero **70vh**. Mobile and tablet minimums are 420px and 360px so text is not clipped; intrinsic content may increase the final height.
- Root: **full width, 100%**, with **outer padding copied from the existing Saved Template `Sylvia counter Flexbox`**, preserving the same page alignment standard as other VIA saved templates. Image is set using native Elementor Background controls, not custom CSS.
- Text appears on an editable white Elementor content panel so it remains readable over the image.
- VIA Elementor Site Kit **19** remains the design source of truth: Global Fonts `primary`, `text`, `via_small_labels`, `via_buttons`, and Global Colours `primary`, `accent`, `via_white`. **No literal font-family names or brand hex values in the builder**.
- Desktop 36px main title and 12px breadcrumbs; tablet/mobile responsiveness is available and needs testing.

## How it works with Elementor Free

This is an **ordinary Saved Container template**, not a Pro Template Widget. Insert it via Elementor's Template Library → **My Templates** → `Inner Pages Hero Template`. Each insertion creates an **independently editable copy**. The template does *not* automatically populate the current page title or breadcrumb, and modifications to the Saved Template do *not* retroactively update all existing copies.

For each inner page, edit the inserted copy's:
1. Hero Background image.
2. Breadcrumb path, replacing the static `Home / Page Title` placeholder.
3. H1 Page Title.
4. Description and optional supporting text.
5. Button label/link if the page needs a different CTA.

Use `Edit with Elementor` on a **draft page first**. Do not insert the template into every published page in bulk until the design has passed desktop/tablet/mobile review and its breadcrumb, CTA and page-specific content have been populated.

## Safe creation — SSH (read-only dry run then apply)

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-inner-pages-hero-template.php

test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"

curl -fLsS --max-time 30 "https://raw.githubusercontent.com/amitaxonsg/VIAElementor/f477218be96d74c6fae80e33b4f471625a8eca47/wp-cli/create-inner-pages-hero-template.php" -o "$SCRIPT"
chown amitaxon:amitaxon "$SCRIPT"
chmod 600 "$SCRIPT"
php -l "$SCRIPT"

runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
```

Only if this finishes with `DRY RUN PASSED — zero writes`:

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-inner-pages-hero-template.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" apply
runuser -u amitaxon -- wp --path="$WP" elementor flush-css
runuser -u amitaxon -- wp --path="$WP" post list --post_type=elementor_library --fields=ID,post_title,post_status
```

The script is **idempotency-guarded**: it refuses to overwrite any existing Saved Template with the same title. It logs a private manifest with the ID after successful creation. It does not change the homepage, inner pages, Header/Footer or Site Kit 19.

## Before publishing inner pages

- Preview at desktop (~1440px), tablet (~768px), mobile (~390px).
- Verify the background crop, button behaviour, readability of text, absence of horizontal scrolling, and whether the hero grows naturally for longer descriptions.
- Change placeholders, check breadcrumbs, and confirm the button target.
- Confirm the template does not duplicate a preexisting hero on the page.
- Preserve the previous **server-local full WordPress backup** `VIA-FULL-20261013-120355` and take a new backup before any mass insertion.
- Continue the established **SSH test → visual approval → GitHub record** workflow.

## Related standards

[Homepage Template Library](HOMEPAGE-TEMPLATE-LIBRARY.md)  
[Approved 70vh Homepage Hero](APPROVED-HERO-FOOTER-WIDTH-20261013.md)  
[2026-10-13 Recovery Checkpoint](BACKUP-RECOVERY-20261013.md)
