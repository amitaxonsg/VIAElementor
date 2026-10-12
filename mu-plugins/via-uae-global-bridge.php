<?php
/**
 * Plugin Name: VIA UAE Menu / Elementor Global Bridge
 * Description: Connects the installed UAE Navigation Menu output to Elementor Site Kit global CSS variables.
 *
 * Reference implementation. Compare against the currently working dev-server
 * via-uae-global-bridge.php before deploying; menu DOM/CSS selectors can change
 * with plugin upgrades. No hardcoded corporate colors or font names.
 */
add_action('wp_head', static function (): void {
    if (!is_singular('page')) {
        return;
    }
    $id = (int)get_queried_object_id();
    if ($id < 21 || $id > 49) {
        return;
    }
    ?>
<style id="via-uae-global-bridge">
/* Existing embedded-home menu 21; shared header 57. */
:is(.elementor-21 .elementor-element-0000002,
    .elementor-57 .elementor-element-d4c15dd)
:is(.hfe-nav-menu > li > a,
    .hfe-nav-menu .hfe-menu-item,
    .hfe-nav-menu .menu-item-has-children > a) {
  font-family: var(--e-global-typography-via_navigation-font-family,
                   var(--e-global-typography-text-font-family, sans-serif)) !important;
  font-size: var(--e-global-typography-via_navigation-font-size, inherit) !important;
  font-weight: var(--e-global-typography-via_navigation-font-weight, inherit) !important;
  color: var(--e-global-color-primary) !important;
}
:is(.elementor-21 .elementor-element-0000002,
    .elementor-57 .elementor-element-d4c15dd)
:is(.hfe-nav-menu > li:hover > a,
    .hfe-nav-menu > li.current-menu-item > a,
    .hfe-nav-menu > li.current-menu-ancestor > a) {
  color: var(--e-global-color-accent) !important;
}
:is(.elementor-21 .elementor-element-0000002,
    .elementor-57 .elementor-element-d4c15dd)
:is(.hfe-sub-menu a, .hfe-sub-menu-item, .sub-menu a) {
  font-family: var(--e-global-typography-viadrop1-font-family,
                   var(--e-global-typography-text-font-family, sans-serif)) !important;
  font-size: var(--e-global-typography-viadrop1-font-size, inherit) !important;
  font-weight: var(--e-global-typography-viadrop1-font-weight, inherit) !important;
  color: var(--e-global-color-primary) !important;
}
:is(.elementor-21 .elementor-element-0000002,
    .elementor-57 .elementor-element-d4c15dd)
:is(.hfe-sub-menu a:hover,
    .hfe-sub-menu-item:hover,
    .sub-menu a:hover) {
  color: var(--e-global-color-accent) !important;
}
</style>
    <?php
}, 1100);
