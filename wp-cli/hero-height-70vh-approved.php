<?php
/**
 * VIA Frontiers — approved desktop hero height (October 13, 2026).
 *
 * wp eval-file hero-height-70vh-approved.php dry-run
 * wp eval-file hero-height-70vh-approved.php apply
 * wp eval-file hero-height-70vh-approved.php rollback
 *
 * Updates only Elementor Homepage 21, hero stage 0000014, desktop min_height.
 * Backups saved outside the document root; no other page sections modified.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) { exit("WP-CLI only\n"); }

$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run', 'apply', 'rollback'], true)) WP_CLI::error('Use dry-run, apply or rollback');
if (site_url() !== 'https://axonserver.com'
    || (int)get_option('page_on_front') !== 21
    || (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Unexpected environment');
}
$original = get_post_meta(21, '_elementor_data', true);
$data = json_decode((string)$original, true);
if (!is_array($data)) WP_CLI::error('Invalid Elementor JSON');

$matches = [];
foreach ($data as $i => $node) {
    if (($node['id'] ?? '') === '0000015' && ($node['settings']['_title'] ?? '') === '02 Hero') $matches[] = $i;
}
if (count($matches) !== 1) WP_CLI::error('Hero 0000015 not uniquely found');
$stage = &$data[$matches[0]]['elements'][0];
if (($stage['id'] ?? '') !== '0000014' || empty($stage['settings']['background_image']['url'])) {
    WP_CLI::error('Approved hero stage not found');
}
$current = $stage['settings']['min_height'] ?? null;
$from = ['unit'=>'vh','size'=>78,'sizes'=>[]];
$to = ['unit'=>'vh','size'=>70,'sizes'=>[]];
$expected = $mode === 'rollback' ? $to : $from;
$target = $mode === 'rollback' ? $from : $to;
if ($current !== $target && $current !== $expected) {
    WP_CLI::error('Refusing to replace an unexpected height: '.wp_json_encode($current));
}
WP_CLI::log('Current: '.wp_json_encode($current));
WP_CLI::log('Target: '.wp_json_encode($target));
if ($mode === 'dry-run') { WP_CLI::success('DRY RUN PASSED — unchanged'); return; }
if ($current === $target) { WP_CLI::success('Already at target height — unchanged'); return; }
$json = wp_json_encode($data);
$stage['settings']['min_height'] = $target;
$json = wp_json_encode($data);
if (!$json || !is_array(json_decode($json,true))) WP_CLI::error('Unable to encode Elementor JSON');
$dir = '/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($dir) || !is_writable($dir)) WP_CLI::error('Private backup directory missing or unwritable');
$backup = $dir.'/hero-height-70vh-approved-'.gmdate('Ymd-His').'-'.wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup,$original,LOCK_EX) === false) WP_CLI::error('Unable to save private backup');
chmod($backup, 0600);
if (!update_post_meta(21,'_elementor_data',wp_slash($json))) WP_CLI::error('Update failed, original data at '.$backup);
if (get_post_meta(21,'_elementor_data',true) !== $json) WP_CLI::error('Write verification failed; original data at '.$backup);
clean_post_cache(21);
WP_CLI::success('Hero height updated. Private backup: '.$backup);
