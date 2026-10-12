<?php
/**
 * Export a REVIEWABLE snapshot of VIA Elementor content into a PRIVATE folder.
 * Does not upload anywhere. May include unpublished copy, contact links and PII:
 * REVIEW before selecting anything for a PUBLIC repository.
 *
 * wp eval-file export-review-snapshot.php
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit("WP-CLI only\n");
}
if (site_url() !== 'https://axonserver.com' ||
    home_url() !== 'https://axonserver.com' ||
    (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Unexpected WordPress site or Site Kit');
}
$base = '/home/amitaxon/via-fullsite-deployment/exports';
if (!is_dir($base) && !wp_mkdir_p($base)) {
    WP_CLI::error('Cannot create private export directory');
}
chmod($base, 0700);
$dir = $base . '/review-' . gmdate('Ymd-His') . '-' .
    wp_generate_password(7, false, false);
if (!mkdir($dir, 0700) || !mkdir($dir . '/pages', 0700) ||
    !mkdir($dir . '/templates', 0700)) {
    WP_CLI::error('Cannot initialize reviewed export staging folder');
}
$write = static function(string $path, array $value): void {
    $data = wp_json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    if (!$data || file_put_contents($path, $data) === false) {
        WP_CLI::error('Cannot write export file ' . basename($path));
    }
    chmod($path, 0600);
};
$manifest = [
    'warning' => 'PRIVATE REVIEW REQUIRED before publishing. Contains real content.',
    'site' => 'https://axonserver.com',
    'kit_id' => 19, 'page_ids' => [], 'template_ids' => [57, 58],
    'images_included' => false,
    'mu_plugin_source_included' => false,
    'wp_config_included' => false,
];
foreach (range(21, 49) as $id) {
    $post = get_post($id);
    $data = json_decode((string)get_post_meta($id, '_elementor_data', true), true);
    if (!$post || $post->post_type !== 'page' || !is_array($data)) {
        WP_CLI::error("Cannot export page {$id}");
    }
    $write($dir . '/pages/page-' . $id . '.json', [
        'post_id' => $id, 'post_title' => $post->post_title,
        'post_name' => $post->post_name, 'post_status' => $post->post_status,
        'wp_page_template' => get_post_meta($id, '_wp_page_template', true),
        'page_settings' => get_post_meta($id, '_elementor_page_settings', true),
        'elementor_data' => $data,
    ]);
    $manifest['page_ids'][] = $id;
}
foreach ([57, 58] as $id) {
    $post = get_post($id);
    $data = json_decode((string)get_post_meta($id, '_elementor_data', true), true);
    if (!$post || $post->post_type !== 'elementor-hf' || !is_array($data)) {
        WP_CLI::error("Cannot export shared template {$id}");
    }
    $write($dir . '/templates/template-' . $id . '.json', [
        'post_id' => $id, 'post_title' => $post->post_title,
        'post_status' => $post->post_status,
        'template_type' => get_post_meta($id, 'ehf_template_type', true),
        'include_locations' => get_post_meta($id, 'ehf_target_include_locations', true),
        'exclude_locations' => get_post_meta($id, 'ehf_target_exclude_locations', true),
        'elementor_data' => $data,
    ]);
}
$kit = get_post_meta(19, '_elementor_page_settings', true);
if (!is_array($kit)) {
    WP_CLI::error('Site Kit configuration invalid');
}
$write($dir . '/elementor-kit-19.json', ['kit_id' => 19, 'settings' => $kit]);
$menu = wp_get_nav_menu_object(3);
$items = [];
if ($menu) {
    foreach (wp_get_nav_menu_items(3) ?: [] as $item) {
        $items[] = [
            'id' => (int)$item->ID, 'title' => $item->title,
            'parent' => (int)$item->menu_item_parent,
            'object_id' => (int)$item->object_id, 'url' => $item->url
        ];
    }
}
$write($dir . '/menu-3.json', ['menu_name' => $menu->name ?? null, 'items' => $items]);
$write($dir . '/manifest.json', $manifest);
WP_CLI::success('Private Elementor export staged for review at: ' . $dir);
