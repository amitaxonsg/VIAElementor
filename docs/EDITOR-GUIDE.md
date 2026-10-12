# VIA Elementor editor guide (Elementor Free + UAE)

The client edits content inside WordPress at **https://axonserver.com/wp-admin/** during development. Production will have separate credentials and domain.

## One-time shared design system

From any Elementor page: open the Elementor menu → **Site Settings**.

- **Global Colors:** change the token for Navy, Orange, Blue, Stone, Apricot or Cream. A widget changes automatically **only if its color is linked using Elementor's globe icon**.
- **Global Fonts:** Primary/Secondary are Cormorant Garamond; Text is Inter. Custom styles include VIA Navigation, VIA Dropdown, VIA Buttons, VIA Footer and VIA Page Titles. Use the globe icon under the widget's Typography control to link a global style.
- Optional fonts are Fraunces, Manrope, DM Sans and Plus Jakarta Sans. They should remain inactive until the client chooses them.
- **Site Settings → Typography / Buttons:** adjusts general defaults. An individually overridden widget may not follow that default.
- **Astra Customizer:** manages theme-level settings that are not controlled by Elementor. Avoid styling the same element in multiple places.

## Edit a page

Pages → select page → **Edit with Elementor**. Click a heading, button, image, card or text block. Set **Content**, **Style**, and **Advanced / Responsive**. Keep section widths and responsive settings locally editable; global colors and fonts do not control geometry. Test desktop, tablet and phone before updating.

## Edit site navigation

**Appearance → Menus → VIA Frontiers Main Menu (menu ID 3)**. Parent items: Individuals (child Bye Bye Black Cat), Corporate (children Executive Coaching, Leadership Training), Accreditation (child Certification Pathway), plus About, Insights, Contact. Drag items to reorder and indent to create a submenu. The responsive display is controlled by the UAE `navigation-menu` widget in shared Header **57**.

**Important:** Some navigation links originally targeted draft pages. Confirm the destination page is published before a client launch.

## Edit shared header once

Open **UAE / Header Footer Builder → VIA Frontiers - Header (57) → Edit with Elementor**. Main logo, UAE menu widget and LET'S TALK CTA are editable. Use the VIA Navigation typography preset for menu and VIA Buttons for button. Confirm tablet/mobile hamburger and submenu behavior.

Do not add another header section to a page. A page must use **Elementor Full Width**, not Elementor Canvas, for the shared template to appear, and the template's **Display On** condition must include that page.

## Edit shared footer once

Open **UAE / Header Footer Builder → VIA Frontiers - Footer (58)**. Footer v7 reference:

1. Brand: supplied VIA logo + description;
2. Pathways: Individuals / Corporate / Accreditation;
3. Explore: About / Insights / Contact;
4. Information: Privacy / Terms.

Below: Stone divider, copyright, and Contact / Privacy / Terms shortcuts. Normal text uses the VIA Footer global style. The legal URLs on the development site are `/via-privacy/` and `/via-terms/`; review and replace them for the client domain.

Avoid copying a footer into individual Elementor pages. Check mobile stacking.

## Change CTA buttons and page titles

Use the **VIA Buttons** preset for typography and Orange Global Color for backgrounds. For an Orange page-title block, build an Elementor Container with a Global Orange background and a heading linked to VIA Page Titles, then use White for contrast. Keep other pages predominantly light.

## Diagnose a mismatch

1. Verify the page uses shared header/footer templates 57/58 and is **Elementor Full Width** (not Canvas).
2. Look for duplicate embedded page header/footer blocks.
3. Confirm `__globals__` bindings on widgets and that Site Kit **19** is active.
4. Check UAEs widget styling and responsive breakpoint controls.
5. Review temporary MU-plugins `via-dev-header-fix.php`, `via-uae-global-bridge.php`, `via-shared-template-assets.php` — they may affect appearance. Do not remove without browser testing.
6. Regenerate Elementor CSS through Elementor Tools or WP-CLI; check mobile at 390px and tablet at 768px.

**Do not** activate the retired `via-frontiers-branding` plugin without intentionally reviewing its competing CSS.
