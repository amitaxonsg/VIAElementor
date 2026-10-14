# VIA Frontiers — H1 Typography Ownership and Regression Prevention

Approved: 2026-10-14. Applies to the live VIA Frontiers account `viafront` at `/home/viafront/public_html` on `server.axonserver.com`. Do **not** confuse this with the separate `amitaxon` development account described in older documents.

## Live H1 default (verified by user SSH output)

- Elementor active Site Kit: **19**
- `h1_typography_font_family`: **Inter**
- `h1_typography_font_size`: **40px** (desktop default; responsive editor adjustments permitted)
- `h1_typography_font_weight`: **700**
- `h1_color`: **#262A63**
- Generated `uploads/elementor/css/post-19.css` showed `.elementor-kit-19 h1{color:#262A63;font-family:"Inter", Sans-serif;font-size:40px;font-weight:700;}`.
- This **verifies the default**, not individual widget edit/save success.

## Permanent ownership rule

**Elementor Site Settings control default H1 typography; individual Heading widgets may intentionally override font family, size, weight, and color.** These must visibly apply and persist after saving.

Never add a global typography lock in Custom CSS, Typography Studio, theme CSS, MU plugins, template CSS, or generated Elementor CSS. Prohibited patterns include `h1 { font-family: ... !important; }`, `.elementor-heading-title { font-size: ... !important; }`, and `.elementor-widget-heading h1 { font-weight: ... !important; }` when they enforce site-wide fonts or sizes. Rules without `!important` can also win through specificity or load order, so test the actual cascade.

Custom CSS may define layout, spacing, effects, or *explicitly scoped* exceptions, but never silently defeat a widget typography choice. Avoid hardcoded `font-family`, `font-size`, `font-weight`, or `color` for generic H1/heading selectors. When typography exceptions are approved, scope them to a uniquely named component and document the rationale and impact.

Keep H2–H6, other preset fonts (including Cormorant in the prior brand system), existing colors, header/footer, responsive layout, and content unchanged unless separately approved. **Do not bulk-reset Typography Studio's saved rules**: inspect and back up only confirmed conflicts before targeted adjustments.

## Mandatory review/test before CSS deployment

1. Read the current active CSS plugin and Typography Studio rules and back up the affected original rule(s). Do not assume GitHub contains live server/plugin CSS.
2. Search for `h1`, `.elementor-heading-title`, `.elementor-widget-heading`, `font-family`, `font-size`, `font-weight`, `color`, and `!important`. Inspect selectors and cascade, including inline/widget styles.
3. On a representative H1 Heading widget, temporarily change its font to another installed font; confirm editor preview changes. Save on a controlled test page; verify the public view, then restore the original choice. Also check desktop, tablet, and mobile.
4. If the change saves but is invisible, use browser Computed Styles to identify winning stylesheet/selector. Check generated Elementor CSS and cache layers only after confirming source-of-truth settings.
5. Avoid manual edits to generated `post-19.css` or repeated blanket `elementor flush-css` as a substitute for resolving an override. If CSS is regenerated, verify stylesheet availability and page output before concluding success.
6. Use account-scoped WP-CLI with PHP 8.3:
   `runuser -u viafront -- /opt/cpanel/ea-php83/root/usr/bin/php /usr/local/bin/wp-cli.phar --path=/home/viafront/public_html ...`.
   Do not use the `/usr/local/bin/wp` launcher for this account; it invokes PHP 7.4 in the verified server output.
7. Backup -> dry run -> targeted change -> verify -> rollback path. No recursive permissions changes and no unrelated cPanel modifications.

## Deployment status

**Policy documented in GitHub only.** There has been no authenticated SSH change to the live Typography Studio or Custom CSS plugin in this documentation commit. A full audit of live plugin rules and Elementor editor-save behavior is still required before declaring the conflict permanently eliminated.

Related documentation: `docs/APPROVED-WORKING-BASELINE-20261013.md` (development baseline), `docs/BRAND-GUIDE.md` (historical broader typography directions), and `docs/EDITOR-GUIDE.md`.
