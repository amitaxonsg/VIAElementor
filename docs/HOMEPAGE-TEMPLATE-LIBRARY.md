# VIA Frontiers — Reusable Homepage Flexbox Templates (v1 candidate)

**Status:** GitHub code prepared; **not deployed, previewed, or visually approved** in WordPress.  
**Architecture:** WordPress-first content, GitHub-reviewed generator; **Elementor Free**, no Pro license required for saved Container templates.

## Templates to create

| Saved Template name | Live source on homepage 21 | Design plan |
| --- | --- | --- |
| `Sylvia counter Flexbox` | Hero's current credentials/stats strip | **Already created by the user**. Keep unchanged. |
| `homepage-03-Pathways` | Section `000002c` — 03 Pathways | Intro in two columns on desktop, three responsive white cards with accents, existing descriptions/media/buttons. |
| `homepage-04-FIRE-Approach` | Section `000003f` — 04 FIRE approach | Native two-colour "THE VIA" + "APPROACH" headings, four horizontal F/I/R/E steps stacked vertically, mobile-friendly. |
| `homepage-05-Client-Voices` | Section `000004c` — 05 Testimonials | Editorial intro beside three quotation cards, responsive rows on tablet/mobile, original quotes/authors retained. |
| `homepage-06-Start-a-Conversation` | Unique section containing the title "Where would you like to start?" | Modern three-part CTA: title, body, existing button; stacks on mobile. |

### Design rules

- **No hardcoded font-family names**. Connect typography to Elementor Site Kit **19** Global Fonts.
- **No hardcoded corporate hex codes**. Use Elementor Site Kit 19 Global Colours (e.g. `primary`, `accent`, `via_cream`, `via_stone`, `via_white`).
- Approved sizes: small section/eyebrow **12px**, main heading **36px**, card heading **25px**. Preserve source text, testimonials, legal/credential claims, actual buttons/links, and images; editorial approval is separate.
- All new styling is **native Elementor Container/Flexbox settings**, not PHP-injected CSS or stylesheets.
- Fully responsive data is explicitly included for tablet/mobile; appearance is **not verified** until desktop, tablet and phone inspection.
- Rebuilt components use fresh Elementor element IDs, while keeping content and Global Style references from the approved homepage.

## Safety

The generator clones current WordPress homepage sections. The complete source Elementor JSON is **not** in this public GitHub repo; code cannot assert pixel-perfect output until the current server layout is checked. It does **not** modify live homepage 21, Header 57, Footer 58, Site Kit 19, nav menu, or Typography Studio. It only inserts new `elementor_library` saved templates.

It validates source IDs, section headings, expected widget structures, Elementor Template Library registration and Global Kit, and refuses to overwrite existing template names. On partial failure it removes only templates created within the same run. Do not run while editing homepage 21 in another session.

Prior site-local verified recovery snapshot: `VIA-FULL-20261013-120355`. Keep that private. The new templates are created **after** that snapshot; capture a fresh snapshot when they are approved.

## SSH — fetch and test code (do not skip dry-run)

```bash
set -e
WP=/home/amitaxon/public_html
SOURCE=/home/amitaxon/build-homepage-templates.php
URL=https://raw.githubusercontent.com/amitaxonsg/VIAElementor/main/wp-cli/build-homepage-templates.php

test "$(runuser -u amitaxon -- wp --path="$WP" option get siteurl)" = "https://axonserver.com"
curl -fLsS --max-time 30 "$URL" -o "$SOURCE"
chown amitaxon:amitaxon "$SOURCE"
chmod 600 "$SOURCE"
php -l "$SOURCE"
runuser -u amitaxon -- wp --path="$WP" eval-file "$SOURCE" dry-run
```

When **all four** source checks report success, run:

```bash
WP=/home/amitaxon/public_html
SOURCE=/home/amitaxon/build-homepage-templates.php
runuser -u amitaxon -- wp --path="$WP" eval-file "$SOURCE" apply
runuser -u amitaxon -- wp --path="$WP" elementor flush-css
runuser -u amitaxon -- wp --path="$WP" post list --post_type=elementor_library --fields=ID,post_title,post_status
```

**If dry-run fails**, stop and send the error. Never force it or modify the live section just to satisfy a template builder. Adapt the GitHub script to the current WordPress source structure after a read-only audit.

## Testing and approval

Open Elementor → Templates → Saved Templates → **Container** and edit each new `homepage-…` template. Test widths ~1440px desktop, 768px tablet, and 390px mobile, as well as real links, original media, counter/animation behaviour, no horizontal overflow, readable quote cards, navigation unaffected. The reference source material may have proprietary/unapproved client text; do not publish Elementor JSON or screenshots to this public repo without review.

**Do not insert anything into the published homepage yet.** Once each template is approved, add to a draft page first. Standard Saved Templates insert **independent copies** and do not automatically synchronize existing inserted sections. Shared live references need a separate controlled mechanism.

## Later improvements

Once user approves each template:
1. Record its ID, name, before/after screenshots and approval.
2. Commit any tested revisions to the generator.
3. Optionally create one staging page containing all four templates for visual QA.
4. Plan the page-section replacement independently, with a pre-change snapshot and rollback, rather than rebuilding the homepage wholesale.

**User's workflow:** Build and test via SSH → confirm visuals → update GitHub with approved implementation. The user has expressly authorised this initial generator to be put in GitHub before server deployment.

## Safety correction before first server import

- Commit `1f3c43380f2c096f73b68531ac83084214a7352b` fixes Client Voices extraction to use the section's `elements` array rather than the section object itself.
- Older downloaded copies of `build-homepage-templates.php` should be discarded. Re-download the pinned corrected commit, run `php -l`, then `wp eval-file ... dry-run`.
- An all-black SSH screenshot is not proof the website is down. Check site HTTP, active WP-CLI processes and `elementor_library` contents read-only before rerunning anything.
- Do not run `apply` unless all source sections validate and all four proposed template names are absent. No live homepage changes are intended.

## Current live FIRE section mapping — verified with WP-CLI audit (2026-10-13)

The 04 FIRE approach section ID `000003f` was **replaced** on the development homepage during an earlier redesign. The current source is the top-level container `b8800dd` with title `VIA FIRE Native Rows`. Its children are heading row `b5bb7c9` (heading widget IDs `f5391fc`, `931821d`) and steps container `eb885d3` (four row IDs `9c53df3`, `937ef3f`, `de631fa`, `cb93c4a`).

- Older builder runs failed safely on `Homepage source section not uniquely available: 04 FIRE approach`. No template creation was confirmed.
- New pinned commit `e1fb73d272627dc32d9900c22e4f7e8ebca0406c` makes the builder source FIRE from the **current** native row hierarchy, preserving the existing heading and row widgets rather than rebuilding the obsolete 000003f card format.
- Corrected builder still requires full `dry-run` validation of **Pathways, FIRE, Client Voices, and Consultation**, including the existing Sylvia counter template. A later error may indicate other content/structure changes; do not force through it.
- **Not confirmed applied** in WordPress. SSH-first dry run remains the next action.

## 2026-10-13 — Actual Consultation structure detected during dry run

The corrected FIRE mapping allowed the dry run to progress to Consultation, which returned `CTA content structure differs: {"heading":1,"text-editor":1,"button":1}`. That result means the live section has **three** Elementor widgets, not the legacy four-widget expectation. No templates or page edits were made during the dry-run failure.

Pinned builder revision `0a9c471657d69c1bc90a26584d9eca415ec1fa95` now preserves the existing three source widgets and native column subtrees rather than adding an invented label widget. It checks that there are exactly three direct child containers, each containing one of heading/text editor/button, retains complete widget settings and links, and changes only copied responsive container widths. Any further structure mismatch aborts without writes.

**Next:** replace the downloaded `/home/amitaxon/build-homepage-templates.php` with the pinned revision and execute `php -l` and `dry-run`. Do not apply unless it reports `DRY RUN PASSED — zero writes` and lists all four templates. Client preview/approval still pending.
