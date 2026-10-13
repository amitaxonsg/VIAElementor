<?php
/**
 * VIA Frontiers / page 21 / Pathways — update ONLY copy from approved reference.
 * Usage via WP-CLI as account owner: wp eval-file update-pathways-reference-copy.php dry-run|apply
 * No CSS, font, colours, sizing, widget IDs, images, links or layouts changed.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) { exit("WP-CLI only\n"); }

$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run', 'apply'], true)) WP_CLI::error('Usage: dry-run or apply');
if (site_url() !== 'https://axonserver.com' || home_url() !== 'https://axonserver.com'
    || (int) get_option('page_on_front') !== 21
    || (int) get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Development site/homepage/Site Kit guard failed');
}
$post = get_post(21);
if (!$post || $post->post_type !== 'page' || $post->post_status !== 'publish'
    || $post->post_name !== 'via-frontiers-homepage-draft') {
    WP_CLI::error('Homepage identity guard failed');
}

$original = get_post_meta(21, '_elementor_data', true);
$data = json_decode((string) $original, true);
if (!is_array($data) || !$data) WP_CLI::error('Homepage Elementor JSON invalid');

// Work only in the known Pathways section — never traverse/rewrite other sections.
$matched = [];
foreach ($data as $index => $section) {
    if (($section['id'] ?? '') === '000002c'
        && ($section['settings']['_title'] ?? '') === '03 Pathways'
        && ($section['elType'] ?? '') === 'container') $matched[] = $index;
}
if (count($matched) !== 1) WP_CLI::error('Expected exactly one Pathways section');
$section = &$data[$matched[0]];

$byId = [];
$indexNodes = static function (array &$items) use (&$indexNodes, &$byId): void {
    foreach ($items as &$item) {
        $id = $item['id'] ?? '';
        if ($id !== '') {
            if (isset($byId[$id])) WP_CLI::error("Duplicate Elementor ID {$id}");
            $byId[$id] = &$item;
        }
        if (isset($item['elements']) && is_array($item['elements'])) $indexNodes($item['elements']);
    }
    unset($item);
};
$indexNodes($section['elements']);

// Before any changes, confirm the card structure, imagery and original buttons exist.
$structure = [
    '0000016' => 'text-editor',
    '0000017' => 'heading',
    'a71c9f2' => 'heading',
    '0000018' => 'text-editor',
    '0000019' => 'heading', '000001a' => 'text-editor',
    '000001b' => 'text-editor', '000001c' => 'image', '000001d' => 'button',
    '000001f' => 'heading', '0000020' => 'text-editor',
    '0000021' => 'text-editor', '0000022' => 'image', '0000023' => 'button',
    '0000025' => 'heading', '0000026' => 'text-editor',
    '0000027' => 'text-editor', '0000028' => 'image', '0000029' => 'button',
];
foreach ($structure as $id => $type) {
    if (!isset($byId[$id]) || ($byId[$id]['widgetType'] ?? '') !== $type)
        WP_CLI::error("Expected {$type} widget {$id} not found; no changes");
}
foreach (['000002b' => ['000001e','0000024','000002a'],
          '000001e' => ['0000019','000001a','000001b','000001c','000001d'],
          '0000024' => ['000001f','0000020','0000021','0000022','0000023'],
          '000002a' => ['0000025','0000026','0000027','0000028','0000029']] as $id => $expected) {
    if (!isset($byId[$id]) || ($byId[$id]['elType'] ?? '') !== 'container')
        WP_CLI::error("Required card container {$id} is missing");
    $children = array_column($byId[$id]['elements'] ?? [], 'id');
    if ($children !== $expected)
        WP_CLI::error("Card container {$id} structure changed; no changes");
}

// Authorised changes derived from the provided reference screenshot.
// Strings are plain text; old Elementor <p> wrappers must match exactly or abort.
$edits = [
    'a71c9f2' => ['title', 'One human approach.', 'One approach.'],
    '000001b' => ['editor', '<p>Build self-awareness, change what keeps repeating and move towards a more fulfilling future.</p>',
                   '<p>Build self-awareness, eradicate negativity and move towards a more fulfilling future.</p>'],
    '0000020' => ['editor', '<p>Lead consciously through change.</p>',
                   '<p>Adopt intuitive leadership.</p>'],
    '0000021' => ['editor', '<p>Develop stronger teams and more adaptive, human organisations through coaching and leadership training.</p>',
                   '<p>Develop stronger teams and more adaptive, human organisations.</p>'],
    '0000026' => ['editor', '<p>Develop practical skills.</p>',
                   '<p>Build your practice.</p>'],
    '0000027' => ['editor', '<p>Build your practice through certification, applied methods and practitioner development.</p>',
                   '<p>Get certified by the Society of NLP, Orlando, USA.</p>'],
];
$changed = [];
foreach ($edits as $id => [$field, $old, $new]) {
    $current = $byId[$id]['settings'][$field] ?? null;
    if ($current === $new) continue; // Idempotent.
    if ($current !== $old) WP_CLI::error("Widget {$id} was independently edited. Found unexpected {$field}; refusing to overwrite");
    $byId[$id]['settings'][$field] = $new;
    $changed[] = $id;
}

// Unchanged reference lines: validate existence, do not overwrite editor changes.
foreach (['0000017'=>'Three paths.', '0000019'=>'Individuals',
          '000001f'=>'Leaders & Organisations', '0000025'=>'Coaches & Practitioners'] as $id=>$expected) {
    if (($byId[$id]['settings']['title'] ?? '') !== $expected)
        WP_CLI::error("Unrecognised heading at {$id}; no changes");
}

WP_CLI::log('Pathways reference copy: '.count($changed).' widget(s) to update: '.implode(', ', $changed));
WP_CLI::log('Typography, links, images, CTA text, page sections and styling untouched.');
if ($mode === 'dry-run') { WP_CLI::success('DRY RUN PASSED — nothing written'); return; }
if (!$changed) { WP_CLI::success('Copy already matches screenshot — no changes'); return; }

$json = wp_json_encode($data);
if (!$json || !is_array(json_decode($json, true))) WP_CLI::error('New Elementor JSON invalid');
$directory = '/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($directory) || !is_writable($directory)) WP_CLI::error('Private backup directory unavailable');
$backup = $directory.'/pathways-reference-copy-21-'.gmdate('Ymd-His').'-'.wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup, $original, LOCK_EX) === false) WP_CLI::error('Unable to back up current Elementor content');
chmod($backup, 0600);
if (!update_post_meta(21, '_elementor_data', wp_slash($json)))
    WP_CLI::error('Elementor content update failed; original backup: '.$backup);
if (get_post_meta(21, '_elementor_data', true) !== $json)
    WP_CLI::error('Post-write check failed — restore from backup: '.$backup);
clean_post_cache(21);
WP_CLI::success('Updated only approved Pathways copy. Backup: '.$backup);
