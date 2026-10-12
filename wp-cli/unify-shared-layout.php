<?php
/**
 * VIA Frontiers: migrate all VIA pages to shared Header 57/Footer 58.
 *
 * Dry run: wp eval-file unify-shared-layout.php dry-run
 * Apply:   wp eval-file unify-shared-layout.php apply
 * NO changes to published status of ordinary pages.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit("WP-CLI only\n");
}

$base = '/home/amitaxon/via-fullsite-deployment';
$backupDir = $base . '/backups';
$expectedSite = 'https://axonserver.com';
$mode = $args[0] ?? 'dry-run';

if (!in_array($mode, ['dry-run', 'apply'], true)) {
    WP_CLI::error('Use dry-run or apply');
}
if (home_url() !== $expectedSite || site_url() !== $expectedSite ||
    (int)get_option('page_on_front') !== 21 ||
    (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Unexpected site URL, front page or Elementor Site Kit');
}
if (!class_exists('\Elementor\Plugin')) {
    WP_CLI::error('Elementor not loaded');
}

$pageSlugs = [
    21 => 'via-frontiers-homepage-draft',
    22 => 'individuals', 23 => 'corporate', 24 => 'get-certified',
    25 => 'leadership-training', 26 => 'about', 27 => 'who-we-help',
    28 => 'leadership-consulting', 29 => 'executive-coaching',
    30 => 'culture-transformation', 31 => 'organisations',
    32 => 'coaches-practitioners', 33 => 'nlp-certifications',
    34 => 'bye-bye-black-cat',
    35 => 'bye-bye-black-cat-coach-licensing', 36 => 'mental-health',
    37 => 'testimonials', 38 => 'partners',
    39 => 'partners-bharati-jagdish',
    40 => 'partners-float-your-boat-therapy',
    41 => 'resources', 42 => 'resources-programs',
    43 => 'resources-webinars', 44 => 'resources-media',
    45 => 'resources-create-your-future-now',
    46 => 'insights', 47 => 'contact',
    48 => 'via-privacy', 49 => 'via-terms',
];

// About was the successful pilot. Do not accidentally revert it to Canvas.
if (get_post_meta(26, '_wp_page_template', true) !== 'elementor_header_footer') {
    WP_CLI::error('About page 26 is not using Elementor Full Width');
}

$snapshot = [
    'kind' => 'via-shared-layout-v1',
    'site_url' => $expectedSite,
    'pages' => [], 'templates' => [],
];

foreach ($pageSlugs as $id => $slug) {
    $post = get_post($id);
    if (!$post || $post->post_type !== 'page' ||
        $post->post_name !== $slug ||
        !in_array($post->post_status, ['draft', 'publish'], true)) {
        WP_CLI::error("VIA page identity/status mismatch: {$id}");
    }
    $raw = get_post_meta($id, '_elementor_data', true);
    $nodes = json_decode((string)$raw, true);
    if (!is_array($nodes) || count($nodes) < 1) {
        WP_CLI::error("Missing Elementor page content: {$id}");
    }
    $snapshot['pages'][$id] = [
        'post_status' => $post->post_status,
        'slug' => $slug,
        'layout' => get_post_meta($id, '_wp_page_template', true),
        'page_settings' => get_post_meta($id, '_elementor_page_settings', true),
        'elementor_data' => $id === 21 ? $raw : null,
    ];
}

$templateTypes = [57 => 'type_header', 58 => 'type_footer'];
foreach ($templateTypes as $id => $type) {
    $post = get_post($id);
    $nodes = json_decode((string)get_post_meta($id, '_elementor_data', true), true);
    if (!$post || $post->post_type !== 'elementor-hf' ||
        !in_array($post->post_status, ['draft', 'publish'], true) ||
        get_post_meta($id, 'ehf_template_type', true) !== $type ||
        !is_array($nodes) || !$nodes) {
        WP_CLI::error("Shared template mismatch: {$id}");
    }
    $snapshot['templates'][$id] = [
        'post_status' => $post->post_status,
        'include' => get_post_meta($id, 'ehf_target_include_locations', true),
        'exclude' => get_post_meta($id, 'ehf_target_exclude_locations', true),
    ];
}

// Safety check: approved Footer v7 is installed.
$footer = json_decode((string)get_post_meta(58, '_elementor_data', true), true);
if (($footer[0]['elements'][0]['settings']['_title'] ?? '') !==
    'Footer four-column layout') {
    WP_CLI::error('Footer 58 is not the approved four-column layout');
}

// Before unifying, homepage has its own first/last Header & Footer sections.
$homeRaw = $snapshot['pages'][21]['elementor_data'];
$home = json_decode((string)$homeRaw, true);
$first = $home[0]['settings']['_title'] ?? '';
$last = $home[count($home) - 1]['settings']['_title'] ?? '';
$alreadyUnified = false;
if ($first === '01 Header' && $last === '07 Footer' && count($home) >= 4) {
    $home = array_slice($home, 1, -1);
    WP_CLI::log('Homepage: duplicate first/last sections will be removed.');
} elseif ($first !== '01 Header' && $last !== '07 Footer') {
    $alreadyUnified = true;
    WP_CLI::log('Homepage: embedded Header/Footer already absent; preserving content.');
} else {
    WP_CLI::error('Homepage Header/Footer partially changed. Resolve manually.');
}

// A tested loader was created during the About pilot to enqueue CSS for
// shared templates. Its absence indicates a risk of unstyled templates.
$loader = WP_CONTENT_DIR . '/mu-plugins/via-shared-template-assets.php';
if (!is_file($loader)) {
    WP_CLI::error('Shared-template Elementor CSS loader not found: ' . $loader);
}

if (!is_dir($backupDir) || !is_writable($backupDir)) {
    WP_CLI::error('Private backup directory not writable');
}
$locations = [
    'rule' => ['specifics'],
    'specific' => array_map(
        static fn($id) => 'post-' . $id,
        array_keys($pageSlugs)
    ),
];
$empty = ['rule' => [], 'specific' => []];

WP_CLI::log('Verified all 29 VIA pages, shared templates 57/58, Site Kit 19, and CSS loader.');
WP_CLI::log('Shared template target: exact VIA page IDs 21-49 only.');
WP_CLI::log('Homepage already unified: ' . ($alreadyUnified ? 'yes' : 'no'));
WP_CLI::log('Draft page publication statuses will be preserved.');

if ($mode !== 'apply') {
    WP_CLI::success('DRY RUN PASSED. No WordPress changes.');
    return;
}

$backup = $backupDir . '/via-shared-unify-' . gmdate('Ymd-His') .
    '-' . wp_generate_password(8, false, false) . '.json';
$encodedSnapshot = wp_json_encode($snapshot, JSON_PRETTY_PRINT);
if (!$encodedSnapshot || file_put_contents($backup, $encodedSnapshot) === false) {
    WP_CLI::error('STOP: Unable to write rollback snapshot');
}
chmod($backup, 0600);
WP_CLI::log('Rollback snapshot: ' . $backup);

// Do not replace any existing homepage hero/pathway/CTA widget.
if (!$alreadyUnified) {
    $newData = wp_json_encode($home);
    if (!$newData) {
        WP_CLI::error('STOP: Cannot encode homepage');
    }
    update_post_meta(21, '_elementor_data', wp_slash($newData));
    if (get_post_meta(21, '_elementor_data', true) !== $newData) {
        WP_CLI::error('STOP: Homepage update could not be verified; use backup ' . $backup);
    }
}

foreach ($pageSlugs as $id => $slug) {
    update_post_meta($id, '_wp_page_template', 'elementor_header_footer');
    $settings = get_post_meta($id, '_elementor_page_settings', true);
    if (!is_array($settings)) {
        $settings = [];
    }
    $settings['hide_title'] = 'yes';
    update_post_meta($id, '_elementor_page_settings', $settings);
    clean_post_cache($id);
}
foreach ($templateTypes as $id => $type) {
    update_post_meta($id, 'ehf_target_include_locations', $locations);
    update_post_meta($id, 'ehf_target_exclude_locations', $empty);
    if (get_post_status($id) !== 'publish') {
        $result = wp_update_post(['ID' => $id, 'post_status' => 'publish'], true);
        if (is_wp_error($result)) {
            WP_CLI::error('STOP: Could not publish shared template ' . $id);
        }
    }
    clean_post_cache($id);
}

if (get_post_meta(21, '_wp_page_template', true) !== 'elementor_header_footer') {
    WP_CLI::error('Homepage layout verification failed; backup ' . $backup);
}
foreach ($templateTypes as $id => $type) {
    $actual = get_post_meta($id, 'ehf_target_include_locations', true);
    if (get_post_status($id) !== 'publish' || $actual !== $locations) {
        WP_CLI::error('Template condition verification failed: ' . $id);
    }
}
WP_CLI::success('VIA shared Header 57/Footer 58 assigned to pages 21-49. Backup: ' . $backup);
