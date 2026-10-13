# VIA Frontiers — Approved baseline and do-not-change rules
**Checkpoint:** 2026-10-13, user confirmed homepage colours and layout back to normal.  
**This is the current policy for the next 30–40 Elementor templates.**

> **Source of truth:** The live WordPress database + files on `axonserver.com` are authoritative. GitHub stores reviewed source/scripts/documentation and is **not** a live database mirror. This record reflects user screenshots and SSH logs, not an independent live-server dump. For restore, use the dated private snapshot.

## Verified development environment

| Setting | Confirmed value |
| --- | --- |
| Development URL | `https://axonserver.com` |
| cPanel account, WordPress root | `amitaxon`; `/home/amitaxon/public_html` |
| Normal cPanel PHP-script upload folder | `/home/amitaxon/` (outside webroot) |
| Elementor Site Kit | `19` |
| Homepage | Page `21` |
| Shared header / shared footer | `57` / `58` |
| Inner pages | `22–49` |
| WordPress primary navigation | Menu `3` |
| Typography Studio | v1.3.1 reported working; **24 saved rules** at last inspection; do not bulk-reset |
| Latest verified private full-site snapshot | `VIA-FULL-20261013-140913`; completed at 14:09 (+08:00), ~108 MB, database/files/checksums verified by supplied SSH output |

### Brand settings: preserve existing Elementor Globals

| Token | Approved value / meaning |
| --- | --- |
| `primary` | Navy `#262A63` |
| `accent`, `via_orange` | Orange `#F1592D` |
| `via_blue` | Blue `#7499D0` |
| `via_white` | `#FFFFFF` |
| Additional swatches | Stone `#E6E3DE`, Apricot `#EE8F50`, Cream `#DFD1B3` as described in Brand Guide |
| Font families | Elementor Kit 19 Global Typography controls them. Approved pair Cormorant Garamond / Inter. **No hardcoded CSS font-family in new work.** |
| Size standard | 12px eyebrow/section label; 36px main section heading; 25px card title where appropriate |

**Preserve** original VIA logo, full-width Header 57/nav/LET'S TALK; approved boardroom photograph/left hero copy and CTAs; orange/blue slogan; separate Sylvia credentials/35+/3500+/APAC strip; full-width Footer 58; Elementor saved templates.

### Generated Elementor CSS outage — recovered

User reported the slogan (normally Orange/Blue), buttons, Sylvia name and statistic subtitles appearing Navy or transparent. The WordPress database still had correct Global Colour tokens and widget `__globals__` references.

- Elementor CSS `wp-content/uploads/elementor/css/post-19.css` was **absent during initial diagnosis**. It was regenerated via Elementor's Kit CSS generation; afterward the user observed it present (4,556 bytes, as checked then) and `post-21.css` at 17,028 bytes.
- User's HTTP/HTML diagnostic showed `post-19.css` and `post-21.css` both linked from the homepage, with correct variables: `primary`, `accent`, `via_blue`, `via_white`.
- User later stated, **“now it s ok”**. **Treat this as approved / frozen.**
- **Unknown root cause:** there is no conclusive log proving which prior cache flush or edit made the CSS disappear. Do not attribute it to Typography Studio or a particular migration without evidence.
- The absence of optional filenames `kit-19.css` / `global.css` is not a defect when the installation successfully loads `post-19.css`.

### Current Elementor section/template architecture — confirmed

- Home 21 original approved photo hero: section `0000015`, image stage `0000014`, desktop minimum 70vh user-approved earlier (actual values should be freshly read before edits).
- Sylvia credentials/stats currently appear as **separate** homepage section `529bad6f` in recent WP-CLI audit; use the current element IDs, **not obsolete** `000000e` etc. Native counters and APAC remain separate editable widgets.
- Pathways section `000002c`, live copy approved; FIRE is **`b8800dd` / VIA FIRE Native Rows**, not old `000003f`. Testimonials section `000004c`; Consultation `0000050` contains one heading, one text-editor and one button.
- Existing Elementor Saved Container templates, user confirmed:
  - `178` — `Sylvia counter Flexbox` (outer width/padding reference)
  - `196` — `homepage-03-Pathways`
  - `197` — `homepage-04-FIRE-Approach`
  - `198` — `homepage-05-Client-Voices`
  - `199` — `homepage-06-Start-a-Conversation`
  - `218` — `Inner Pages Hero Template` (full-width background, breadcrumb/H1/intro/CTA/support copy, desktop min 35vh in initial build)
  - `183` — `mainpage` (existing separate saved template)
  - `19` — `Default Kit` (global settings, not a normal section template)
- The user created/edited the templates and requested consistent outer width/padding matching `Sylvia counter Flexbox`. New templates must inherit **actual current** Sylvia outer settings, not arbitrary margin/padding values.
- For existing inner pages, saved-template insertion creates an **independent copy**; editing a saved template **does not** automatically update already-inserted copies. Do not imply live synchronisation.
- Template modernizer and spacing scripts are tracked for selective use; do not assume an unverified script is fully deployed just because uploaded to `/home/amitaxon/`.

## Non-negotiable rules — what to avoid

1. **Never change Kit 19 global colours/typography as a workaround** for a missing generated CSS file. Never delete Kit 19.
2. **Never manually edit generated `post-19.css`, `post-21.css`**, or create global overrides to hide a cache problem. Generate Kit CSS only when genuinely required, then **verify its presence and HTML inclusion** before declaring success.
3. Don't repeatedly run `wp elementor flush-css` after copy-only changes without a reason. It may briefly remove generated styles; always inspect loaded Kit CSS afterward if you regenerate.
4. Do not reset Typography Studio's saved overrides or deactivate its plugin wholesale; target a proven conflicting rule and back it up.
5. Do not apply legacy FIRE, old two-/three-card hero, old Pathways parent grid/flex patches, or whole-page JSON restores over currently approved work.
6. Use **native Elementor Free Flexbox Container settings** for sections and padding; no add-on CSS unless explicitly agreed for an interaction that cannot be represented natively. Keep scripts scoped to named Saved Templates, not published pages.
7. Do not change approved copy, credentials, client testimonials, images, Media Library IDs or CTA destinations as part of a design pass. Verify licensing and accuracy before publicly launching claims.
8. New inner page hero is an **editable copy**: update breadcrumb, title, background, descriptions and CTA individually. Do not put the same Sylvia image or placeholder text into every page by bulk insert.
9. Never write to unrelated cPanel accounts or root/system configuration. WP-CLI **as `amitaxon`**, explicit `--path`.
10. **No blind `apply`:** PHP syntax → WP-CLI dry run → private original-data backup → apply → verification (desktop/tablet/mobile). After approval, commit reviewed code/docs in GitHub.
11. Do not confuse GitHub with the server snapshot. Never put DB dumps, `wp-config.php`, private site backups, customer/private content, tokens or full site archives into the public repository.
12. Do not declare 30–40 templates “done” until content, responsive layout, links, accessibility and client approvals are verified one-by-one.

## Baseline verification (read only)

Use [`wp-cli/audit-approved-via-baseline.php`](../wp-cli/audit-approved-via-baseline.php) before and after major changes. It checks Kit values, current Saved Templates, critical page/template IDs and the generated Kit CSS. Confirm the homepage HTTP response and stylesheet inclusion as needed.

## Recovery and next backup

- **Previously verified**: `/home/amitaxon/via-fullsite-deployment/backups/full-site/VIA-FULL-20261013-120355/` (~106MB compressed); `database.sql: OK`, `wordpress-files.tar.gz: OK`. SHA-256 validated; **no actual restoration test yet**.
- **A fresh snapshot is required now** before the 30–40-template work: run existing `/home/amitaxon/via-fullsite-deployment/backup-via-wordpress.sh` detached as `amitaxon`, watch its log, wait for completion, and validate both hashes. Record its actual timestamp after SSH confirms the result. **Do not invent a completed new backup in GitHub.**
- Backups are stored outside `public_html` and are private. Keep a separately secured off-server copy; on-server backups alone do not protect against disk/server loss.
- Recover the smallest affected saved template or widget first, not a full-site restore. A full restore overwrites changes after the selected snapshot, including newly-created templates. Require an explicit choice of snapshot and impact analysis.

**Next:** Build approximately **30–40** reusable sections across inner pages, starting with the [template delivery plan](TEMPLATE-ROADMAP-30-40.md). Do it in small tested batches; no mass updates to published pages.
