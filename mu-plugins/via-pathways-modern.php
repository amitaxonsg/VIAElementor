<?php
/**
 * VIA Pathways Modern — homepage section-only presentation layer.
 * Targets existing Elementor section 000002c and its original three cards.
 * Existing copy, images, widgets and links remain untouched/editable.
 * Disable instantly: rename/remove this one MU-plugin.
 */
if (!defined('ABSPATH')) { exit; }
add_action('wp_enqueue_scripts', function () {
    if (!is_front_page() || (int)get_queried_object_id() !== 21) { return; }
    wp_register_style('via-pathways-modern', false, [], '1.7.0');
    wp_enqueue_style('via-pathways-modern');
    $css = <<<'CSS'
/* VIA Frontiers homepage / 03 Pathways. Deliberately scoped to this Elementor ID. */
body.page-id-21 .elementor-element-000002c {
  --vpm-navy: #262A63;
  --vpm-orange: #F1592D;
  --vpm-blue: #7499D0;
  --vpm-stone: #E6E3DE;
  background: #faf6f0 !important;
  padding: clamp(50px, 5vw, 82px) clamp(20px, 5vw, 78px) !important;
  align-items: stretch;
  gap: 12px;
}
body.page-id-21 .elementor-element-000002c > .e-con-inner {
  max-width: 1320px;
  margin-inline: auto;
}
/* Section eyebrow. */
body.page-id-21 .elementor-element-0000016 p {
  color: #a9481d !important;
  font-family: var(--e-global-typography-text-font-family, inherit);
  font-weight: 700;
  letter-spacing: .10em;
  text-transform: uppercase;
  font-size: 12px !important;
  margin: 0;
}
body.page-id-21 .elementor-element-0000017 .elementor-heading-title {
  /* Heading colour is controlled by its Elementor Global Color. */
  font-family: var(--e-global-typography-primary-font-family, inherit);
  font-size: 36px !important;
  line-height: 1.06 !important;
  font-weight: 500;
  margin: 0 0 4px;
}
body.page-id-21 .elementor-element-0000018 {
  max-width: 520px;
  margin-bottom: 26px;
}
body.page-id-21 .elementor-element-0000018 p {
  font-family: var(--e-global-typography-text-font-family, inherit);
  color: var(--vpm-navy);
  font-size: clamp(15px, 1.12vw, 17px);
  line-height: 1.55;
  margin: 0;
}
/* Three equal height cards. */
body.page-id-21 .elementor-element-000002b {
  display: grid !important;
  grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
  align-items: stretch !important;
  gap: clamp(16px, 1.55vw, 24px) !important;
  width: 100%;
  max-width: 1320px;
  margin-inline: auto;
}
body.page-id-21 .elementor-element-000001e,
body.page-id-21 .elementor-element-0000024,
body.page-id-21 .elementor-element-000002a {
  width: 100% !important;
  min-width: 0 !important;
  max-width: none !important;
  min-height: 340px;
  box-sizing: border-box;
  background: #fff !important;
  border: 1px solid var(--vpm-stone) !important;
  border-top: 3px solid var(--vpm-orange) !important;
  border-radius: 12px !important;
  box-shadow: 0 5px 24px rgba(38, 42, 99, .035);
  padding: clamp(22px, 2vw, 32px) !important;
  display: flex !important;
  flex-direction: column !important;
  align-items: stretch !important;
  gap: 0 !important;
  transition: box-shadow .2s ease, transform .2s ease;
}
@media (hover:hover) {
  body.page-id-21 .elementor-element-000001e:hover,
  body.page-id-21 .elementor-element-0000024:hover,
  body.page-id-21 .elementor-element-000002a:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 32px rgba(38,42,99,.10);
  }
}
/* Card headings: unobtrusive numbered identity marks (no icon font dependency). */
body.page-id-21 :is(.elementor-element-0000019,.elementor-element-000001f,.elementor-element-0000025) {
  order: 0;
  margin-bottom: 16px;
}
body.page-id-21 :is(.elementor-element-0000019,.elementor-element-000001f,.elementor-element-0000025) .elementor-heading-title {
  display: flex;
  align-items: center;
  gap: 12px;
  font-family: var(--e-global-typography-primary-font-family, inherit);
  font-size: 25px !important;
  font-weight: 600;
  line-height: 1.13;
  color: var(--vpm-navy) !important;
}
body.page-id-21 .elementor-element-0000019 .elementor-heading-title::before { content: "01"; }
body.page-id-21 .elementor-element-000001f .elementor-heading-title::before { content: "02"; }
body.page-id-21 .elementor-element-0000025 .elementor-heading-title::before { content: "03"; }
body.page-id-21 :is(.elementor-element-0000019,.elementor-element-000001f,.elementor-element-0000025) .elementor-heading-title::before {
  display: inline-grid;
  flex: 0 0 48px;
  place-items: center;
  height: 48px;
  border-radius: 50%;
  background: #fff3e9;
  color: var(--vpm-orange);
  font: 700 14px/1 var(--e-global-typography-text-font-family, inherit);
  letter-spacing: .02em;
}
body.page-id-21 :is(.elementor-element-000001a,.elementor-element-0000020,.elementor-element-0000026) p {
  font-family: var(--e-global-typography-text-font-family, inherit);
  color: var(--vpm-navy);
  font-size: 16px;
  font-weight: 700;
  margin: 0 0 8px;
}
body.page-id-21 :is(.elementor-element-000001b,.elementor-element-0000021,.elementor-element-0000027) p {
  font-family: var(--e-global-typography-text-font-family, inherit);
  color: var(--vpm-navy);
  line-height: 1.55;
  font-size: 15px;
  margin: 0;
}
/* Keep the three original images proportionate and undistorted. */
body.page-id-21 :is(.elementor-element-000001c,.elementor-element-0000022,.elementor-element-0000028) {
  align-self: flex-end;
  width: 108px !important;
  max-width: 35%;
  margin: 16px 0 12px !important;
}
body.page-id-21 :is(.elementor-element-000001c,.elementor-element-0000022,.elementor-element-0000028) img {
  max-width: 100%;
  width: auto;
  max-height: 94px;
  object-fit: contain;
  border-radius: 5px;
}
/* CTA links remain the existing Elementor buttons and preserve destinations. */
body.page-id-21 :is(.elementor-element-000001d,.elementor-element-0000023,.elementor-element-0000029) {
  margin-top: auto;
  align-self: flex-start;
}
body.page-id-21 :is(.elementor-element-000001d,.elementor-element-0000023,.elementor-element-0000029) .elementor-button {
  background: transparent !important;
  border: 0 !important;
  box-shadow: none !important;
  color: #b3481d !important;
  padding: 7px 0 !important;
  border-radius: 0 !important;
  font: 700 13px/1.4 var(--e-global-typography-text-font-family, inherit) !important;
  text-transform: uppercase;
  letter-spacing: .035em;
}
body.page-id-21 :is(.elementor-element-000001d,.elementor-element-0000023,.elementor-element-0000029) .elementor-button:hover {
  color: var(--vpm-navy) !important;
  text-decoration: underline;
  text-underline-offset: 4px;
}
body.page-id-21 :is(.elementor-element-000001d,.elementor-element-0000023,.elementor-element-0000029) .elementor-button:focus-visible {
  outline: 2px solid var(--vpm-orange);
  outline-offset: 4px;
}
@media (max-width: 1024px) {
  body.page-id-21 .elementor-element-000002b { grid-template-columns: repeat(2,minmax(0,1fr)) !important; }
  body.page-id-21 .elementor-element-000002a { grid-column: 1 / -1; }
  body.page-id-21 :is(.elementor-element-000001e,.elementor-element-0000024,.elementor-element-000002a) { min-height: 305px; }
}
@media (max-width: 767px) {
  body.page-id-21 .elementor-element-000002c { padding: 42px 18px !important; }
  body.page-id-21 .elementor-element-000002b { grid-template-columns: minmax(0,1fr) !important; }
  body.page-id-21 .elementor-element-000002a { grid-column: auto; }
  body.page-id-21 :is(.elementor-element-000001e,.elementor-element-0000024,.elementor-element-000002a) { min-height: 0; padding: 22px !important; }
  body.page-id-21 .elementor-element-0000017 .elementor-heading-title { font-size: 36px !important; }
}
@media (prefers-reduced-motion:reduce) {
  body.page-id-21 :is(.elementor-element-000001e,.elementor-element-0000024,.elementor-element-000002a) { transition: none; }
}

/* v1.1: card images become full-width editorial visual strips above card titles. */
body.page-id-21 :is(.elementor-element-000001c,.elementor-element-0000022,.elementor-element-0000028) {
  order: -2 !important;
  width: 100% !important;
  max-width: none !important;
  align-self: stretch !important;
  height: 148px !important;
  min-height: 148px;
  margin: -8px 0 20px !important;
  padding: 14px !important;
  box-sizing: border-box;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #faf6f0;
  border-radius: 9px;
  overflow: hidden;
}
body.page-id-21 :is(.elementor-element-000001c,.elementor-element-0000022,.elementor-element-0000028) .elementor-widget-container {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}
body.page-id-21 :is(.elementor-element-000001c,.elementor-element-0000022,.elementor-element-0000028) img {
  display: block;
  width: 100% !important;
  height: 100% !important;
  max-width: 100% !important;
  max-height: none !important;
  object-fit: contain !important;
  object-position: center;
}
body.page-id-21 :is(.elementor-element-0000019,.elementor-element-000001f,.elementor-element-0000025) {
  order: -1;
}
body.page-id-21 .elementor-element-a71c9f2 .elementor-heading-title {
  /* Elementor widget Global Accent colour is authoritative. */
  font-family: var(--e-global-typography-primary-font-family, inherit);
  font-size: 36px !important;
  font-weight: 500;
  line-height: 1.06;
}
/* Do not offset independently editable Elementor heading positions. */
@media(max-width:767px) {
 body.page-id-21 :is(.elementor-element-000001c,.elementor-element-0000022,.elementor-element-0000028) { height: 128px !important; min-height: 128px; }
}

/* Clean stable typography baseline. No parent section display/grid/flex override. */
body.page-id-21 .elementor-element-000002c .elementor-element-0000016 p {
  font-size: 12px !important;
  font-family: var(--e-global-typography-text-font-family, inherit);
}
body.page-id-21 .elementor-element-000002c :is(.elementor-element-0000017,.elementor-element-a71c9f2) .elementor-heading-title {
  font-size: 36px !important;
  font-family: var(--e-global-typography-primary-font-family, inherit) !important;
  line-height: 1.12 !important;
}
/* Deliberately DO NOT manipulate how the two heading widgets sit
   in relation to one another. Future one-line change requires
   a native Elementor inner container, not grid/flex rules on 03 Pathways. */

/* v1.7: single isolated native Elementor heading row. Parent 03 Pathways untouched.
   The two original widgets remain independently editable in Elementor. */
body.page-id-21 .elementor-element-b7621a0 {
  display: flex !important;
  flex-direction: row !important;
  flex-wrap: wrap !important;
  justify-content: flex-start !important;
  align-items: baseline !important;
  align-content: flex-start !important;
  gap: 0 12px !important;
  width: 100% !important;
  max-width: 100% !important;
  min-width: 0 !important;
  margin: 0 0 20px !important;
  padding: 0 !important;
}
body.page-id-21 .elementor-element-b7621a0 > :is(.elementor-element-0000017,.elementor-element-a71c9f2) {
  display: block !important;
  flex: 0 0 auto !important;
  width: auto !important;
  max-width: 100% !important;
  min-width: 0 !important;
  align-self: baseline !important;
  margin: 0 !important;
  padding: 0 !important;
}
body.page-id-21 .elementor-element-b7621a0 :is(.elementor-element-0000017,.elementor-element-a71c9f2) .elementor-heading-title {
  display: block;
  margin: 0 !important;
  padding: 0 !important;
  font-size: 36px !important;
  line-height: 1.12 !important;
  font-family: var(--e-global-typography-primary-font-family, inherit) !important;
}
/* Natural wrapping on narrow screens; never shift the entire section. */
@media(max-width:767px) {
  body.page-id-21 .elementor-element-b7621a0 { column-gap: 8px !important; row-gap: 2px !important; }
}

CSS;
    wp_add_inline_style('via-pathways-modern', $css);
}, 90);
