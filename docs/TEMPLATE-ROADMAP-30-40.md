# VIA Frontiers — 30–40 Elementor Free Container Template Delivery Plan

**Target:** approximately **36 reusable Saved Container templates** (working range 30–40).  
**Status:** Proposal only; do not bulk-create or insert into published pages without content and visual approval.

## Existing Saved Template library (six core items)

User's WP-CLI snapshot confirmed the following **published** Elementor library posts:

| ID | Title | Notes |
| ---: | --- | --- |
| 178 | `Sylvia counter Flexbox` | Approved **outer width/padding reference** for all future section templates |
| 196 | `homepage-03-Pathways` | Three Pathways |
| 197 | `homepage-04-FIRE-Approach` | Dynamic FIRE presentation; test interactive state |
| 198 | `homepage-05-Client-Voices` | Testimonials; test one-by-one slider |
| 199 | `homepage-06-Start-a-Conversation` | CTA |
| 218 | `Inner Pages Hero Template` | Full-width, background photo, editable breadcrumb, title, intro, CTA and supporting copy |

`mainpage` (ID 183) is another existing library item but **not counted** in the planned 36 until its purpose is audited. Elementor `Default Kit` ID 19 is **not a section template**. Shared Header 57 and Footer 58 remain separate site templates, outside the 36 count.

## Proposed 30 additional template *concepts* (not yet created)

These names are planning handles, not permission to publish unreviewed content. A page can use multiple templates; a template can be inserted on multiple pages. Before creating each one, inspect that page's current Elementor content and confirm client-approved wording.

| Group | Proposed Saved Container template titles | Count | Likely pages |
| --- | --- | ---: | --- |
| Shared inner-page building blocks | `inner-02-Intro-Split`, `inner-03-Services-Cards`, `inner-04-Benefit-Grid`, `inner-05-Process-Steps`, `inner-06-Credential-Highlight`, `inner-07-Proof-Points`, `inner-08-FAQ`, `inner-09-CTA-Band` | 8 | Reused across pages 22–49 as approved |
| Individuals and personal change | `individuals-01-Overview`, `individuals-02-Programs`, `individuals-03-Bye-Bye-Black-Cat`, `individuals-04-Coach-Licensing`, `individuals-05-Who-We-Help` | 5 | 22, 27, 34, 35, 36 (as relevant; check mental health wording) |
| Corporate/leadership | `corporate-01-Overview`, `corporate-02-Executive-Coaching`, `corporate-03-Leadership-Training`, `corporate-04-Culture-Transformation`, `corporate-05-Organisations` | 5 | 23, 25, 28, 29, 30, 31 |
| Certification/practitioners | `certification-01-Pathways`, `certification-02-Curriculum`, `certification-03-Practitioner-Development` | 3 | 24, 32, 33 |
| About, partners, social proof | `about-01-Sylvia-Story`, `about-02-Testimonials`, `about-03-Partners` | 3 | 26, 37, 38–40 |
| Resources/insights | `resources-01-Overview`, `resources-02-Programs`, `resources-03-Webinars`, `resources-04-Insights-Cards` | 4 | 41–46 |
| Contact/legal | `contact-01-Contact-Form`, `legal-01-Policy-Copy` | 2 | 47–49 |

**Total: 6 existing + 30 proposed = 36 section templates.** Actual count can be adjusted when the current page content is audited; do not create duplicate sections just to reach a numerical target.

## Frozen global rules

1. **Header 57, Footer 58 and Homepage 21:** preserve existing working approved design. New sections are created as separate `elementor_library` **Container** items, not by mutating live pages.
2. **Source of spacing:** the *current saved settings* of `Sylvia counter Flexbox`, not hardcoded widths. New templates match its root content-width, width and responsive padding. Review against full-width hero exceptions rather than blindly overwriting the hero.
3. **Colours & fonts:** Elementor **Kit 19** Global Style bindings. Do not change kit presets to fix a local element. No literal `font-family` declarations or hardcoded corporate colour values for new templates.
4. **Typography:** 12px small labels, 36px main section headings and typically 25px card headings, unless a specifically approved component requires another size.
5. **Native Elementor Free Flexbox** first. Use JS interactions only for real functionality (slider, tabs) and only within template scope; test keyboard, pointer, pause behaviour and mobile swipe where applicable.
6. **Content remains authoritative:** use each page's existing approved text, images, links, quotes and names. Do not make up certification/health claims, testimonials, prices, URLs or background images.
7. **Mobile first-pass:** no horizontal overflow at 390px, readable heading wrapping, mobile CTA full width when necessary, reasonable tap target spacing, no clipped content, reduced-motion support for sliders.
8. **Saved Template behaviour:** standard insertion creates an independent **copy**, not a synchronised global section. To update a copied section, edit that page or deliberately reinsert the approved version.
9. **Naming:** `homepage-`, `inner-`, `individuals-`, `corporate-`, `certification-`, `about-`, `resources-`, `contact-`, `legal-` prefixes. Include version suffix only if an approved older version is being preserved; never silently overwrite a saved template.

## Delivery pipeline — small batches

- **Batch 0:** Fresh, verified complete WordPress files+database backup **after the current six templates**, plus read-only Kit 19 / generated CSS baseline check. No more mass changes until this succeeds.
- **Batch 1:** Shared inner-page components (up to 5). Save as Container; preview separate draft page at desktop/tablet/mobile; keep source page unchanged.
- **Batch 2:** Remaining shared components + Individuals (up to 5).
- **Batch 3:** Corporate/leadership (up to 5).
- **Batch 4:** Certification and About/Partners (up to 6).
- **Batch 5:** Resources and Contact/legal (remaining items).
- **Before every batch:** Save revision snapshot of existing targeted templates and reread source Elementor JSON; run guarded dry run; compare against approved spacing; visually approve.
- **After every batch:** Check Homepage 21 remains the same (including `post-19.css` present and loaded), record template IDs/versions/status, and commit tested implementation details to GitHub.
- **Never** bulk-insert templates into all 28 inner pages in one unattended action. Test on a draft page and start with one approved inner page.
- Target final completion only when all used templates pass content review, responsive tests, accessibility checks and functional link tests.

## Progress / evidence

- User has **confirmed creation** of the six Saved Templates with IDs listed above, and has shown previews. This is not proof every modern slider/tab works at all breakpoints.
- `Inner Pages Hero Template` inserted copies require an individually updated background, breadcrumb, title, text, CTA.
- User last confirmed a restored homepage styling baseline after Kit 19 CSS regeneration; protect it from future template work.
- Most recent **verified historical backup** is `VIA-FULL-20261013-120355` (integrity OK). New after-template snapshot is **pending user SSH execution and confirmation**. Documentation must not label it complete before seeing the actual backup log and SHA-256 results.

## Source of truth / rollback

[Approved working baseline](APPROVED-WORKING-BASELINE-20261013.md)  
[Recovery snapshot instructions](BACKUP-RECOVERY-20261013.md)  
[Template build guide](HOMEPAGE-TEMPLATE-LIBRARY.md)  
[Inner Pages Hero usage](INNER-PAGES-HERO-TEMPLATE.md)

Do not commit exported WordPress DB, private site archives, configuration secrets or bulk client content to the public GitHub repository.


## 2026-10-13 — New batch of ten standard templates (code committed, installation pending)

The new [Ten Common Template Generator](../wp-cli/create-ten-common-templates.php) contains these **ten** new Saved Container candidates, not yet confirmed created:

1. `inner-10-FAQ-Accordion`
2. `inner-11-Two-Column-Editorial`
3. `inner-12-Three-Column-Highlights`
4. `inner-13-Interactive-Flip-Boxes`
5. `inner-14-Track-Record-Circles`
6. `inner-15-Profile-Biography`
7. `inner-16-Services-Overview`
8. `inner-17-Coaching-Journey`
9. `inner-18-Classes-Programs`
10. `inner-19-Contact-Enquiry`

**Count and overlap policy:** The 6 earlier Saved Templates remain confirmed. After this new batch is *actually imported and approved*, the library would contain **16 counted section templates**, plus any independently confirmed Footer CTA or other templates; the aspirational 36-template plan would have roughly 20 more to build. The earlier roadmap's `inner-02`–`inner-09` ideas overlap in function with some of these new titles. **Do not produce duplicates just to satisfy a numeric target.** Reconcile the final backlog against the actual WordPress Saved Template list before further batches.

[Template details, editing notes and QA](TEN-COMMON-ELEMENTOR-TEMPLATES.md). The current project CSS/Kit 19 freeze applies to every new candidate. Do not insert sample text/metrics/images onto published pages without approval.
