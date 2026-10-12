<?php
/**
 * Read-only inventory of the VIA Elementor development installation.
 *
 * wp eval-file audit-via.php
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit("WP-CLI only\n");
}
if (home_url() !== 'https://axonserver.com' || site_url() !== 'https://axonserver.com') {
    WP_CLI::error('Wrong WordPress website');
}
WP_CLI::log('=== VIA FRONTIERS DEVELOPMENT AUDIT ===');
WP_CLI::log('Site URL: ' . site_url());
WP_CLI::log('Site Kit: ' . get_option('elementor_active_kit'));
WP_CLI::log('Homepage: ' . get_option('page_on_front'));
WP_CLI::log('Front mode: ' . get_option('show_on_front'));
WP_CLI::log('Theme: ' . get_stylesheet());
WP_CLI::log('VIA Branding plugin: ' .
    (in_array('via-frontiers-branding/via-frontiers-branding.php',
        (array)get_option('active_plugins', []), true) ? 'active' : 'inactive'));

$totals = ['widgets'=>0, 'globals'=>0, 'embedded_headers'=>0, 'embedded_footers'=>0];
$walk = static function(array $nodes) use (&$walk, &$totals): void {
    foreach ($nodes as $node) {
        if (($node['elType'] ?? '') === 'widget') {
            $totals['widgets']++;
        }
        $globals = $node['settings']['__globals__'] ?? [];
        if (is_array($globals)) {
            $totals['globals'] += count($globals);
        }
        if (!empty($node['elements']) && is_array($node['elements'])) {
            $walk($node['elements']);
        }
    }
};

foreach (range(21, 49) as $id) {
    $post = get_post($id);
    if (!$post || $post->post_type !== 'page') {
        WP_CLI::warning("Page {$id} is missing");
        continue;
    }
    $nodes = json_decode((string)get_post_meta($id, '_elementor_data', true), true);
    if (!is_array($nodes)) {
        WP_CLI::warning("Invalid Elementor data: {$id}");
        continue;
    }
    if (($nodes[0]['settings']['_title'] ?? '') === '01 Header') {
        $totals['embedded_headers']++;
    }
    if (($nodes[count($nodes) - 1]['settings']['_title'] ?? '') === '07 Footer') {
        $totals['embedded_footers']++;
    }
    WP_CLI::log(
        "Page {$id}: {$post->post_status}; slug={$post->post_name}; " .
        "layout=" . get_post_meta($id, '_wp_page_template', true) .
        "; top-level sections=" . count($nodes)
    );
}
foreach ([57 => 'Header', 58 => 'Footer'] as $id => $label) {
    $post = get_post($id);
    if (!$post) {
        WP_CLI::warning("Missing {$label} {$id}");
        continue;
    }
    WP_CLI::log("{$label} {$id}: {$post->post_status}; type=" .
        get_post_meta($id, 'ehf_template_type', true));
    WP_CLI::log('  includes=' .
        wp_json_encode(get_post_meta($id, 'ehf_target_include_locations', true)));
}
$cssLoader = WP_CONTENT_DIR . '/mu-plugins/via-shared-template-assets.php';
WP_CLI::log('Template CSS loader installed: ' . (is_file($cssLoader) ? 'yes' : 'no'));
WP_CLI::log('Global references total: ' . $totals['globals']);
WP_CLI::log('Widgets counted: ' . $totals['widgets']);
WP_CLI::log('Embedded header sections: ' . $totals['embedded_headers']);
WP_CLI::log('Embedded footer sections: ' . $totals['embedded_footers']);
WP_CLI::success('Read-only VIA audit completed.');
