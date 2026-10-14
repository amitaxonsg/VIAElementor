# VIA Frontiers — ALL Typography Ownership and Regression Prevention

**Revised 2026-10-14 at user's instruction:** this policy covers ALL typography, not only H1. It supersedes the H1-only scope of this file's original version.

## One source of truth

**Elementor-managed typography is the authoritative editable source for all Elementor content.** This includes H1–H6, body text, text-editor widgets, buttons/CTAs, labels, card titles, testimonials, counters, headers, footers, mobile/tablet variants, and typography on saved templates. VIA Typography Studio and the separate Custom CSS plugin must not silently override Elementor widget or global typography settings.

**Important architectural exceptions:** the Astra theme and UAE Header/Footer navigation widget can provide non-Elementor-native controls. Inventory these, preserve approved appearances, and use their documented control surfaces or an approved scoped compatibility rule until they can be safely migrated. Do not claim Elementor controls every menu/dropdown element without checking.

## Verified live starting point

Live WordPress: `https://viafrontiers.com`, cPanel `viafront`, `/home/viafront/public_html`; distinct from `amitaxon` development. Active Elementor Kit 19. User's SSH check confirmed H1 default Inter / 40px / 700 / #262A63 and regenerated Kit CSS. **Other typography defaults and saved plugin overrides have NOT been fully audited or migrated. Do not overwrite them.**

## Permanent CSS policy

- New generic typography enforcement is prohibited across plugins, theme CSS, MU plugins, templates and site Custom CSS. In particular, avoid generic selectors for `h1` through `h6`, `p`, `.elementor-heading-title`, `.elementor-widget-heading`, `.elementor-widget-text-editor`, `.elementor-button`, and broad header/footer text classes that assign font-family, font-size, weight, line-height, letter spacing, text transform or text colour against Elementor's settings.
- `!important` is especially risky but **not the only issue**; specificity, inline rules and load order can cause a mismatch without `!important`.
- Retain truly layout-specific CSS and carefully documented third-party compatibility styles. Exception rules must be narrowly scoped, justified and tested against editor controls.
- Typography Studio may remain available for inspection or diagnostics. Do not disable the plugin wholesale or wipe its saved rules. Migrate approved individual styles into Elementor first, then selectively retire only confirmed conflicting override rules after backup.
- Do not manually modify Elementor-generated CSS files. Do not make Kit-wide changes to conceal a CSS-cache problem.

## Safe migration and acceptance tests

1. Inventory **all** Typography Studio saved rules, Custom CSS, theme settings, plugin typography, Elementor Kit tokens, header/footer templates and page/widget styles. Capture screenshots plus original rule exports/backups.
2. Establish a mapping for each typography category from current approved rendered frontend appearance to Elementor global preset, widget setting, or documented third-party native control. Preserve branding, colors and responsive values; avoid mass replacement.
3. Use a staged dry-run and account-scoped backup; migrate one class at a time. Do not delete source rules before the equivalent native styles display correctly.
4. For representative examples of headings H1–H6, body paragraphs, CTA, labels, counters, navigation/dropdowns, header/footer, and mobile/tablet text: modify font, size, weight and color in the appropriate editor; verify preview, save, frontend computed styles and refresh; restore approved appearance after test.
5. For any mismatch, determine the exact winning selector using browser Computed Styles and fix the source instead of adding stronger CSS. Check load/cache effects separately, and verify editor saves independently.
6. Preserve approved global colours and existing content/layout. Do not reset all Typography Studio rules, delete Elementor Kit 19, or modify unrelated cPanel accounts.
7. Account-scoped WP-CLI uses `runuser -u viafront -- /opt/cpanel/ea-php83/root/usr/bin/php /usr/local/bin/wp-cli.phar --path=/home/viafront/public_html ...`. The `/usr/local/bin/wp` launcher was observed executing PHP 7.4 and must not be used for this account.

## Status

**GitHub policy updated only.** Live Typography Studio / Custom CSS and the site's other font controls have not yet been migrated or audited in this turn. Real-world editor and frontend parity is **pending**.

## Original H1 verification context



- Elementor active Site Kit: **19**
- `h1_typography_font_family`: **Inter**
- `h1_typography_font_size`: **40px** (desktop default; responsive editor adjustments permitted)
- `h1_typography_font_weight`: **700**
- `h1_color`: **#262A63**
- Generated `uploads/elementor/css/post-19.css` showed `.elementor-kit-19 h1{color:#262A63;font-family:"Inter", Sans-serif;font-size:40px;font-weight:700;}`.
- This **verifies the default**, not individual widget edit/save success.


