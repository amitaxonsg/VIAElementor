# VIA Frontiers — Native Elementor FIRE Timeline (candidate)

**Source:** [wp-cli/create-via-fire-timeline-template.php](../wp-cli/create-via-fire-timeline-template.php)  
**Template title:** `inner-20-FIRE-Timeline`  
**Status:** GitHub creator committed, PHP syntax checked and simulated dry-run tested; actual WordPress dry-run, creation and desktop/mobile visual approval still **pending**.

## Design from user-approved visual

- Desktop layout follows the approved FIRE four-step timeline screenshot: four white rounded process cards, navy circular F/I/R/E badges, orange divider, and pale numbered circles connected vertically by a thin accent line.
- Editable introduction: eyebrow `THE VIA APPROACH`, heading `A Clear Path to Lasting Change`, short supporting paragraph. These are **sample widget contents** and can be edited in Elementor.
- Four steps retain the user's exact approved words, separated into individual **editable** Action Heading and Explanation Text Editor widgets:
  - **F — Find:** the pattern and understand what’s happening and why.
  - **I — Interrupt:** the cycle and act on the pattern by changing the response.
  - **R — Replace:** the response and choose a new response.
  - **E — Embed:** the change and reinforce the new behavior until it becomes lasting.
- Every visual element (step number, letter badge, orange divider, card, intro, text) is a **native Elementor Free widget/container**, not an image or HTML widget.
- The live **homepage FIRE section** `b8800dd` and existing Saved Container `homepage-04-FIRE-Approach` (reported ID 197) **are not modified**. This creates a **new** Saved Container template only.

## Brand and responsive contract

- Elementor Global Kit **19** handles actual typography and brand colour variables. No literal font-family values or hexadecimal colours and no injected CSS, JavaScript or CSS cache regeneration.
- Outer width and padding are cloned from the currently-approved Saved Container **Sylvia counter Flexbox** (ID 178). Do not impose new global widths.
- Heading sizes: small text **12px**, main heading **36px desktop / 30px mobile**, action headings **25px desktop / 23px mobile**, descriptions **16px**. All size and colour controls remain independently editable in Elementor.
- Circle backgrounds bind to `primary` and `via_stone`; line dividers bind to `accent`; card backgrounds bind to `via_white`; borders bind to `via_stone`.
- Native Flexbox responsive controls keep the numbered timeline on the left. On narrow mobile, each card switches to vertically stacked letter badge, orange horizontal divider and editable action/body text, with a reduced card width to avoid horizontal overflow. **Actual rendering must still be checked at desktop, tablet and 390px mobile** before insertion on a published page.

## Installation

**Preferred cPanel script upload location:** `/home/amitaxon/`. Download the pinned GitHub file below to this path if not already present.

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-via-fire-timeline-template.php
test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
test "$(runuser -u amitaxon -- wp --path="$WP" option get elementor_active_kit)" = "19"
curl -fLsS --max-time 30  "https://raw.githubusercontent.com/amitaxonsg/VIAElementor/93422bf5e5a6812962a041de2b3c2e7533264b8c/wp-cli/create-via-fire-timeline-template.php"  -o "$SCRIPT"
chown amitaxon:amitaxon "$SCRIPT"
chmod 600 "$SCRIPT"
php -l "$SCRIPT"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" dry-run
```

Expected end: `DRY RUN PASSED — zero writes`. **If any error occurs, stop.** This creator refuses to overwrite a template with the same name.

After confirming the latest private full-site backup remains available (last verified `VIA-FULL-20261013-140913`), and only following a passing dry run:

```bash
set -e
WP=/home/amitaxon/public_html
SCRIPT=/home/amitaxon/create-via-fire-timeline-template.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$SCRIPT" apply
runuser -u amitaxon -- wp --path="$WP" post list  --post_type=elementor_library --fields=ID,post_title,post_status
```

No `wp elementor flush-css` is requested because the approved Kit 19 styles had previously disappeared during cache regeneration. Creating a Saved Template does not require flushing the homepage.

## Testing

In Elementor → Saved Templates, find `inner-20-FIRE-Timeline`, then preview/edit as usual. Check:
- Text and accent colours follow Kit 19 Global Styles.
- Card borders, number markers and letter circles show correctly.
- The timeline is readable on desktop, tablet and narrow mobile; no horizontal overflow or clipping.
- Every part can be clicked and edited in Elementor, including the F/I/R/E letter, step number, action heading and explanation.
- No published homepage change occurs. Standard Elementor Free Saved Template insertion creates an **independent copy**, not an automatically synchronised global section.

**Rollback:** Before anyone inserts this template on a page, simply remove the newly created Library template by its **confirmed ID** (after reviewing its contents); do not roll back the homepage or Site Kit. This operation has no original template to restore because the new named Library item did not exist beforehand. For any future template revisions, make an ID-scoped backup and use dry-run before apply.

[Approved project baseline](APPROVED-WORKING-BASELINE-20261013.md) · [Brand guide](BRAND-GUIDE.md) · [Template roadmap](TEMPLATE-ROADMAP-30-40.md) · [Latest recovery note](BACKUP-RECOVERY-20261013.md)
