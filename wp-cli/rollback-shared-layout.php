<?php
/**
 * Restore the specific snapshot created by unify-shared-layout.php.
 *
 * wp eval-file rollback-shared-layout.php /absolute/path/to/snapshot.json dry-run
 * wp eval-file rollback-shared-layout.php /absolute/path/to/snapshot.json apply
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit("WP-CLI only\n");
}
if (home_url() !== 'https://axonserver.com' || site_url() !== 'https://axonserver.com' ||
    (int)get_option('elementor_active_kit') !== 19 ||
    (int)get_option('page_on_front') !== 21) {
    WP_CLI::error('Wrong development site or Site Kit');
}

$backupDir = '/home/amitaxon/via-fullsite-deployment/backups';
$path = $args[0] ?? '';
$mode = $args[1] ?? 'dry-run';
if (!in_array($mode, ['dry-run', 'apply'], true)) {
    WP_CLI::error('Use dry-run or apply');
}
$real = $path ? realpath($path) : false;
$realDir = realpath($backupDir);
if (!$real || !$realDir || dirname($real) !== $realDir ||
    !preg_match('/^via-shared-unify-[0-9]{8}-[0-9]{6}-[a-zA-Z0-9]+\.json$/', basename($real))) {
    WP_CLI::error('Provide a valid absolute unify backup path under private backups directory');
}

$snapshot = json_decode((string)file_get_contents($real), true);
if (!is_array($snapshot) || ($snapshot['kind'] ?? '') !== 'via-shared-layout-v1' ||
    ($snapshot['site_url'] ?? '') !== 'https://axonserver.com' ||
    count($snapshot['pages'] ?? []) !== 29 ||
    !isset($snapshot['templates'][57], $snapshot['templates'][58])) {
    WP_CLI::error('Invalid migration snapshot');
}

foreach ($snapshot['pages'] as $id => $state) {
    $id = (int)$id;
    if ($id < 21 || $id > 49) {
        WP_CLI::error('Unexpected page ID in snapshot');
    }
    $post = get_post($id);
    if (!$post || $post->post_type !== 'page' ||
        $post->post_name !== $state['slug'] ||
        $post->post_status !== $state['post_status']) {
        WP_CLI::error("Page identity/status changed since migration: {$id}");
    }
}
foreach ([57, 58] as $id) {
    $post = get_post($id);
    if (!$post || $post->post_type !== 'elementor-hf') {
        WP_CLI::error("Shared template missing: {$id}");
    }
}
$oldHome = json_decode((string)$snapshot['pages'][21]['elementor_data'], true);
if (!is_array($oldHome) || !$oldHome) {
    WP_CLI::error('Snapshot homepage data invalid');
}

WP_CLI::log('Verified snapshot: ' . basename($real));
WP_CLI::log('Target: 29 VIA page layout/page settings, homepage content, template statuses/conditions.');
WP_CLI::log('Global Site Kit, navigation menu and inner-page content remain unchanged.');

if ($mode !== 'apply') {
    WP_CLI::success('ROLLBACK DRY RUN PASSED. No changes.');
    return;
}
$current = [
    'created_from' => basename($real),
    'pages' => [], 'templates' => []
];
foreach ($snapshot['pages'] as $id => $state) {
    $id = (int)$id;
    $current['pages'][$id] = [
        'layout' => get_post_meta($id, '_wp_page_template', true),
        'page_settings' => get_post_meta($id, '_elementor_page_settings', true),
        'elementor_data' => $id === 21 ? get_post_meta($id, '_elementor_data', true) : null
    ];
}
foreach ([57, 58] as $id) {
    $current['templates'][$id] = [
        'status' => get_post_status($id),
        'include' => get_post_meta($id, 'ehf_target_include_locations', true),
        'exclude' => get_post_meta($id, 'ehf_target_exclude_locations', true)
    ];
}
$rescue = $backupDir . '/before-via-shared-rollback-' . gmdate('Ymd-His') .
    '-' . wp_generate_password(8, false, false) . '.json';
$encoded = wp_json_encode($current, JSON_PRETTY_PRINT);
if (!$encoded || file_put_contents($rescue, $encoded) === false) {
    WP_CLI::error('STOP: Cannot back up current state before rollback');
}
chmod($rescue, 0600);

foreach ($snapshot['pages'] as $id => $state) {
    $id = (int)$id;
    update_post_meta($id, '_wp_page_template', $state['layout']);
    if (empty($state['page_settings'])) {
        delete_post_meta($id, '_elementor_page_settings');
    } else {
        update_post_meta($id, '_elementor_page_settings', $state['page_settings']);
    }
    if ($id === 21) {
        update_post_meta($id, '_elementor_data', wp_slash($state['elementor_data']));
    }
    clean_post_cache($id);
}

foreach ($snapshot['templates'] as $id => $state) {
    $id = (int)$id;
    foreach ([
        'ehf_target_include_locations' => 'include',
        'ehf_target_exclude_locations' => 'exclude'
    ] as $key => $field) {
        if (empty($state[$field])) {
            delete_post_meta($id, $key);
        } else {
            update_post_meta($id, $key, $state[$field]);
        }
    }
    if (get_post_status($id) !== $state['post_status']) {
        $result = wp_update_post(['ID' => $id, 'post_status' => $state['post_status']], true);
        if (is_wp_error($result)) {
            WP_CLI::error("Could not restore template status: {$id}");
        }
    }
    clean_post_cache($id);
}
if (get_post_meta(21, '_elementor_data', true) !== $snapshot['pages'][21]['elementor_data']) {
    WP_CLI::error('Homepage verification failed; rescue backup: ' . $rescue);
}
WP_CLI::success('Shared-template migration rolled back. Pre-rollback state: ' . $rescue);
