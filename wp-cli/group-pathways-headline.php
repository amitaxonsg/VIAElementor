<?php
/**
 * VIA Frontiers | Group Pathways' two Elementor headline widgets.
 *
 * wp eval-file group-pathways-headline.php dry-run
 * wp eval-file group-pathways-headline.php apply
 *
 * Page 21 / Elementor Kit 19 / Pathways section 000002c ONLY.
 * Does not change typography colours, card settings, links, photos, or content.
 * Requires the completed split and reference-copy update.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) { exit("WP-CLI only\n"); }

$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run', 'apply'], true)) WP_CLI::error('Usage: dry-run or apply');
if (site_url() !== 'https://axonserver.com' || home_url() !== 'https://axonserver.com'
    || (int)get_option('page_on_front') !== 21 || (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Site, homepage, or Kit does not match the VIA development environment');
}
$post = get_post(21);
if (!$post || $post->post_type !== 'page' || $post->post_status !== 'publish'
    || $post->post_name !== 'via-frontiers-homepage-draft') WP_CLI::error('Unexpected homepage identity');

$original = get_post_meta(21, '_elementor_data', true);
$data = json_decode((string)$original, true);
if (!is_array($data) || !$data) WP_CLI::error('Invalid Elementor homepage data');

$indices = [];
foreach ($data as $i => $section) {
    if (($section['id'] ?? '') === '000002c' &&
        ($section['elType'] ?? '') === 'container' &&
        ($section['settings']['_title'] ?? '') === '03 Pathways') $indices[] = $i;
}
if (count($indices) !== 1) WP_CLI::error('Exactly one Pathways container required');
$section = &$data[$indices[0]];
$children = &$section['elements'];
if (!is_array($children)) WP_CLI::error('Pathways children missing');

$ids = array_map(static fn($el) => $el['id'] ?? '', $children);
$rowId = 'b7621a0';
$expectedBefore = ['0000016','0000017','a71c9f2','0000018','000002b'];
$expectedAfter  = ['0000016', $rowId,'0000018','000002b'];

if ($ids === $expectedAfter) {
    $row = $children[1];
    $innerIds = array_map(static fn($el) => $el['id'] ?? '', $row['elements'] ?? []);
    if (($row['elType'] ?? '') !== 'container' || $innerIds !== ['0000017','a71c9f2'])
        WP_CLI::error('Existing heading row differs from intended structure');
    WP_CLI::success('Already grouped — no changes needed');
    return;
}
if ($ids !== $expectedBefore) {
    WP_CLI::error('Pathways layout changed. Found direct child IDs: '.implode(', ', $ids));
}
if (($children[1]['widgetType'] ?? '') !== 'heading' ||
    ($children[1]['settings']['title'] ?? '') !== 'Three paths.' ||
    ($children[2]['widgetType'] ?? '') !== 'heading' ||
    ($children[2]['settings']['title'] ?? '') !== 'One approach.') {
    WP_CLI::error('The two independently coloured heading widgets differ');
}
if (($children[3]['widgetType'] ?? '') !== 'text-editor' ||
    ($children[4]['elType'] ?? '') !== 'container') {
    WP_CLI::error('Following description or card grid differs');
}
// Ensure our proposed row ID does not collide anywhere in the Elementor page.
$findId = static function(array $items, string $needle) use (&$findId): bool {
    foreach ($items as $el) {
        if (($el['id'] ?? '') === $needle) return true;
        if (!empty($el['elements']) && is_array($el['elements']) && $findId($el['elements'], $needle)) return true;
    }
    return false;
};
if ($findId($data, $rowId)) WP_CLI::error('Proposed Elementor row ID already exists');

// Reparent existing editable widgets, preserving ALL their original settings.
$first = $children[1];
$second = $children[2];
$row = [
    'id' => $rowId,
    'elType' => 'container',
    'isInner' => true,
    'settings' => [
        '_title' => 'Pathways headline — two editable colours',
        'content_width' => 'full',
        'flex_direction' => 'row',
        'flex_direction_tablet' => 'row',
        'flex_direction_mobile' => 'row',
        'flex_wrap' => 'wrap',
        'flex_justify_content' => 'flex-start',
        'flex_align_items' => 'baseline'
    ],
    'elements' => [$first, $second]
];
array_splice($children, 1, 2, [$row]);
if (array_map(static fn($el) => $el['id'] ?? '', $children) !== $expectedAfter)
    WP_CLI::error('Unexpected section structure after reparenting');

$after = wp_json_encode($data);
if (!$after || !is_array(json_decode($after, true))) WP_CLI::error('Unable to encode Elementor data');
WP_CLI::log('Preserved: Pathways label, independently editable Navy/Orange headings, description and all 3 existing cards.');
WP_CLI::log('New nested row b7621a0 contains only the two headings. No font families or colours are hardcoded.');
WP_CLI::log('No other Elementor sections or site settings changed.');
if ($mode === 'dry-run') { WP_CLI::success('DRY RUN PASSED — no changes written'); return; }

$dir = '/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($dir) || !is_writable($dir)) WP_CLI::error('Private backup directory unavailable');
$backup = $dir.'/pathways-heading-row-21-'.gmdate('Ymd-His').'-'.wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup, $original, LOCK_EX) === false) WP_CLI::error('Could not write backup');
chmod($backup, 0600);
if (!update_post_meta(21, '_elementor_data', wp_slash($after)))
    WP_CLI::error('Database write failed. Original saved at '.$backup);
if (get_post_meta(21, '_elementor_data', true) !== $after)
    WP_CLI::error('Write validation failed. Restore from '.$backup);
clean_post_cache(21);
WP_CLI::success('Pathways headline row applied. Backup: '.$backup);
