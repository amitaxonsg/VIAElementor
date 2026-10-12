# VIA Frontiers — Approved brand guide

## Client-supplied colors (latest six-swatch reference)

| Token | Hex | Intended role |
|---|---|---|
| Navy | `#262A63` | Primary heading/text, navigation, selective authority panels |
| Orange | `#F1592D` | CTA buttons, hover/accent and approved orange page-title blocks |
| Apricot | `#EE8F50` | Supporting highlight |
| Blue | `#7499D0` | Supporting visual accent |
| Stone | `#E6E3DE` | Dividers, borders, quiet backgrounds |
| Cream | `#DFD1B3` | Warm background accent |

Neutral white `#FFFFFF` is allowed for light page backgrounds and the approved header/footer. The earlier alternate values `#282865` and `#F46714` are **superseded**. Do not introduce unrelated colors.

## Typography — client-approved defaults

| Usage | Default | Elementor global preset |
|---|---|---|
| Main & secondary headings | **Cormorant Garamond** | Primary / Secondary; VIA Page Titles |
| Body | **Inter**, regular | Text |
| Navigation | **Inter**, semibold | VIA Navigation |
| Dropdowns | **Inter**, medium/semibold | VIA Dropdown |
| Buttons | **Inter**, semibold | VIA Buttons |
| Footer | **Inter** | VIA Footer |
| Small labels | **Inter** | VIA Labels |

Alternative Google Font styles **available but not active**: Fraunces (editorial), Manrope (navigation), DM Sans (body), Plus Jakarta Sans (labels). A customer may select these in Global Fonts; doing so is a design change and should be reviewed.

## Elementor Global Style reference

Active development Site Kit: **post ID 19**. System palette: Primary Navy, Secondary Blue, Text Navy, Accent Orange. Additional custom global color tokens: `via_navy`, `via_orange`, `via_apricot`, `via_blue`, `via_stone`, `via_cream`, plus neutral/compatibility tokens. Additional typography presets include `via_navigation`, `viadrop1`, `via_buttons`, `via_footer`, `via_page_titles`, and `via_small_labels`.

Elementor references colors and fonts through `settings.__globals__`, not by pasting fixed hex codes or font-family names onto every widget. Local responsive font sizes, gaps, margins and content widths may still be defined per element.

**Known exception:** the installed UAE Navigation Menu widget exposes some nonstandard color/typography controls. A scoped compatibility MU-plugin reads Elementor CSS variables for normal/hover menu styling. Test the site after any UAE or Elementor upgrade.

## Client design directions

- Lighter, cleaner website; **do not** make most pages dark.
- Apply dark Navy selectively to authority sections, especially Sylvia's credentials.
- Inner page-title blocks may use Orange with strong contrast; preserve the supplied page mockup hierarchy.
- Use **only** supplied, approved original logo files; do not redraw/recreate a logo with AI.
- Header is compact and white; dropdowns and mobile hamburger must work.
- Footer reference: white, four columns (brand; Pathways; Explore; Information), Stone divider, copyright and links in separate bottom row.

## Known source files / dependencies

Lovable source project: `a36bb602-17de-434e-9c8c-69b9d89b2551` (Pixel Perfect Pixels). Relevant design sources include `src/vf/css/home-exact-sample.css`, `src/vf/css/corporate-branding.css`, `src/vf/sections/home-exact-sample.html`. The WordPress development site is a separate copy, **not automatically fed by Lovable**.
