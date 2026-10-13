# VIA Pathways — Recovery and Future-Update Rules (2026-10-13)

## Current verified limits

- Development site: `https://axonserver.com`; cPanel user `amitaxon`; WordPress `/home/amitaxon/public_html`; homepage **page ID 21**; Site Kit **19**.
- Pathways parent Elementor container: `000002c` (**03 Pathways**).
- Eyebrow: `0000016` — "CHOOSE YOUR STARTING POINT".
- Heading A: `0000017` — "Three paths.".
- Heading B: `a71c9f2` — "One human approach.".
- Supporting copy: `0000018`; cards parent: `000002b`; individual cards: `000001e`, `0000024`, `000002a`.
- Image widgets: `000001c`, `0000022`, `0000028`; buttons remain their existing native Elementor widgets.
- Headings A and B are **separate native Elementor widgets** after the successfully applied `wp-cli/split-pathways-heading.php` migration. Backup from the user command log: `/home/amitaxon/via-fullsite-deployment/backups/pathways-title-21-20261013-001710-6U0yWVrK.json`. Do NOT rerun that script unless it says already split.
- The user reports the headings/section shifted left and too much blank space after the experimental v1.4/v1.5 stylesheet deployments. A rollback from v1.5 restored **v1.4**, which still included bad `display: grid` section layout. Not a complete rollback.

## What was implemented and what failed

1. A scoped `mu-plugins/via-pathways-modern.php` gives three equal cards, image panels on top, existing links, responsive columns.
2. A standalone database migration split the title into two Elementor Heading widgets for separate colours.
3. v1.2, v1.4 and v1.5 appended competing `display:flex`, `display:grid`, `grid-column`, `order` and `flex-wrap` rules onto the **parent section**. The screenshots proved this approach unstable.
4. The prior v1.5 rollback command used `via-pathways-before-v15-*.php` — that backup was v1.4, already problematic.
5. **v1.6 repository commit `5634ef80f26bdcebb4c709bfd051a34b40235d36`** rebuilds the stylesheet from the pre-experiment v1.1 cards, keeps 148px image panels, adopts 12px eyebrow/36px headline sizes, replaces all literal font-family names with Elementor Global Typography variables, and drops *all* experiments that force parent heading grid/flex placement.
6. v1.6 was **committed to GitHub**, but deployment / visual verification on WordPress has **not** been confirmed. User must run guarded SSH instructions and check.

## Non-negotiable design contract

- Small section/eyebrow headings **12px**; main section headings **36px** (desktop; adjust mobile only with approval).
- **Never hardcode font-family names** in CSS. Inherit Elementor Site Kit 19 global typography: `--e-global-typography-primary-font-family` for headings, `--e-global-typography-text-font-family` for body. Maintain approved branding via Elementor Global Styles.
- Keep VIA approved Navy and Orange colour **separately editable** for the two headings.
- The desired final desktop wording is **on one line**: `Three paths. One human approach.`, while `CHOOSE YOUR STARTING POINT` appears on the line above. It is **not yet reliably implemented** after the failed layout CSS attempts; v1.6 prioritizes recovery.
- Preserve client-approved imagery, 3 card destinations, hero, counters, shared Header 57/Footer 58 and all unrelated page sections.
- **No more append-only CSS patches to the Pathways parent.** First inspect current Elementor JSON settings, stylesheet and rendered DOM. For a one-line title, use a dedicated **native Elementor nested container** housing the two heading widgets. Do not apply CSS grid/flex to the whole Pathways section, which also contains cards/description.

## Deployment guardrails

1. Read the current page 21 Elementor section and Site Kit 19. Confirm IDs and layout first.
2. Use read-only `wp` audit for structural changes; use `dry-run` for any JSON migration.
3. Back up the existing MU-plugin outside public_html and exact `_elementor_data` meta when changing JSON.
4. Install the reviewed, pinned GitHub commit (not an unpinned `main` URL) as account owner, then flush Elementor CSS. Validate desktop and mobile.
5. If v1.6 also fails, restore the saved MU-plugin backup, do not stack additional CSS.
6. When a new change is verified, update THIS document with the exact commit, screenshot result and backup path. Distinguish GitHub commit from actual deployment.

## Known SSH target

```bash
WP=/home/amitaxon/public_html
FILE="$WP/wp-content/mu-plugins/via-pathways-modern.php"
BACKUP=/home/amitaxon/via-fullsite-deployment/backups
runuser -u amitaxon -- wp --path="$WP" option get siteurl
runuser -u amitaxon -- wp --path="$WP" elementor flush-css
```

Never touch other cPanel accounts, shared server settings or production domains.
