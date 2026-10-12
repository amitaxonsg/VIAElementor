# VIA Frontiers — WordPress page map

Development WordPress at `https://axonserver.com`. These IDs were verified in the user-provided WP-CLI logs. **Status may have changed since the last audit; query WordPress before writing.**

| ID | Slug / purpose |
|---:|---|
| 21 | via-frontiers-homepage-draft — assigned homepage |
| 22 | individuals |
| 23 | corporate |
| 24 | get-certified |
| 25 | leadership-training |
| 26 | about |
| 27 | who-we-help |
| 28 | leadership-consulting |
| 29 | executive-coaching |
| 30 | culture-transformation |
| 31 | organisations |
| 32 | coaches-practitioners |
| 33 | nlp-certifications |
| 34 | bye-bye-black-cat |
| 35 | bye-bye-black-cat-coach-licensing |
| 36 | mental-health |
| 37 | testimonials |
| 38 | partners |
| 39 | partners-bharati-jagdish |
| 40 | partners-float-your-boat-therapy |
| 41 | resources |
| 42 | resources-programs |
| 43 | resources-webinars |
| 44 | resources-media |
| 45 | resources-create-your-future-now |
| 46 | insights |
| 47 | contact |
| 48 | via-privacy |
| 49 | via-terms |

Home 21 originally used an embedded header and footer; **About 26 was the successful shared Header 57/Footer 58 pilot**. Do not assume the shared-home migration has been applied until verified.

### Content caveats

The inner-page first-pass converter staged 28 pages from Lovable HTML. Four mapping substitutions require review before client launch:

- Individuals (22) used Who We Help source.
- Corporate (23) used Organisations source.
- Accreditation (24) used NLP Certifications source.
- Leadership Training (25) used Leadership Consulting source.

Privacy/Terms and any claims, certification/accreditation statements or testimonials require client approval. The imported layouts are native Elementor widgets, **not proven pixel-perfect**.

### Menu IDs

Navigation menu 3: parent menu items 51 Individuals, 52 Corporate, 53 Accreditation, 54 About, 55 Insights, 56 Contact. Child items 59 Bye Bye Black Cat, 60 Executive Coaching, 61 Leadership Training, 62 Certification Pathway.

### Checklist when adding pages

Create the new page, build it with Elementor, use Elementor Full Width, link fonts/colors to Site Kit globals, and update both Header 57 and Footer 58 display locations to include that new page (unless an approved site-wide VIA-only condition is in place). Verify mobile and whether its menu link is published.
