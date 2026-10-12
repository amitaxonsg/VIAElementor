<?php
/**
 * VIA Frontiers — match APAC statistic to 35+ / 3500+ statistics.
 *
 * Dry run: wp eval-file apac-stat-display.php dry-run
 * Apply:   wp eval-file apac-stat-display.php apply
 *
 * Home page 21 only, fourth element of the credentials strip inside Hero 0000015.
 * Existing 35+/3500+ counters, background image, buttons and page sections remain unchanged.
 */

if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run','apply'], true)) {
    WP_CLI::error('Use dry-run or apply');
}
if (home_url() !== 'https://axonserver.com' ||
    site_url() !== 'https://axonserver.com' ||
    (int)get_option('elementor_active_kit') !== 19 ||
    (int)get_option('page_on_front') !== 21) {
    WP_CLI::error('Wrong WordPress site, Elementor Kit or homepage');
}
$page = get_post(21);
if (!$page || $page->post_type !== 'page' || $page->post_status !== 'publish' ||
    $page->post_name !== 'via-frontiers-homepage-draft') {
    WP_CLI::error('Homepage identity mismatch');
}
$before = get_post_meta(21, '_elementor_data', true);
$data = json_decode((string)$before, true);
if (!is_array($data) || !$data) WP_CLI::error('Elementor homepage data invalid');
$heroIndex = null;
foreach ($data as $i => $section) {
    if (($section['id'] ?? '') === '0000015' &&
        ($section['settings']['_title'] ?? '') === '02 Hero') {
        $heroIndex = $i;
        break;
    }
}
if ($heroIndex === null) WP_CLI::error('Hero 0000015 not found');

$hero = &$data[$heroIndex];
$strip = &$hero['elements'][1];
if (($strip['id'] ?? '') !== '0000013' ||
    count($hero['elements'] ?? []) !== 2 ||
    count($strip['elements'] ?? []) !== 4) {
    WP_CLI::error('Expected single credentials strip not found');
}
$children = &$strip['elements'];
if (($children[1]['id'] ?? '') !== '0000010' ||
    ($children[1]['widgetType'] ?? '') !== 'counter' ||
    (int)($children[1]['settings']['ending_number'] ?? -1) !== 35 ||
    ($children[2]['id'] ?? '') !== '0000011' ||
    ($children[2]['widgetType'] ?? '') !== 'counter' ||
    (int)($children[2]['settings']['ending_number'] ?? -1) !== 3500) {
    WP_CLI::error('35+/3500+ counters differ from approved structure');
}
$apac = $children[3] ?? [];
$firstTime = (($apac['id'] ?? '') === '0000012' &&
    ($apac['widgetType'] ?? '') === 'text-editor');
$alreadyUpdated = (($apac['elType'] ?? '') === 'container' &&
    ($apac['settings']['_title'] ?? '') === 'APAC statistic' &&
    count($apac['elements'] ?? []) === 2 &&
    ($apac['elements'][0]['id'] ?? '') === '0000012' &&
    ($apac['elements'][0]['settings']['title'] ?? '') === 'APAC' &&
    ($apac['elements'][1]['settings']['title'] ?? '') === 'Experience');
if (!$firstTime && !$alreadyUpdated) {
    WP_CLI::error('APAC element changed; refusing to overwrite');
}

$kit = get_post_meta(19, '_elementor_page_settings', true);
if (!is_array($kit)) WP_CLI::error('Elementor global settings unavailable');
$colors = [];
foreach (array_merge($kit['system_colors'] ?? [], $kit['custom_colors'] ?? []) as $entry) {
    if (!empty($entry['_id'])) $colors[$entry['_id']] = true;
}
$fonts = [];
foreach (array_merge($kit['system_typography'] ?? [], $kit['custom_typography'] ?? []) as $entry) {
    if (!empty($entry['_id'])) $fonts[$entry['_id']] = true;
}
foreach (['primary','secondary'] as $id) {
    if (empty($colors[$id])) WP_CLI::error("Global color {$id} missing");
}
foreach (['primary','text'] as $id) {
    if (empty($fonts[$id])) WP_CLI::error("Global font {$id} missing");
}

if ($firstTime) {
    $ids = [];
    $scan = static function (array $nodes) use (&$scan,&$ids): void {
        foreach ($nodes as $node) {
            if (!empty($node['id'])) $ids[$node['id']] = true;
            if (!empty($node['elements'])) $scan($node['elements']);
        }
    };
    $scan($data);
    $newId = static function(string $seed) use (&$ids): string {
        for ($i = 0; $i < 100; $i++) {
            $candidate = substr(md5("via-apac-stat:{$seed}:{$i}"), 0, 7);
            if (!isset($ids[$candidate])) {
                $ids[$candidate] = true;
                return $candidate;
            }
        }
        WP_CLI::error('Could not assign unique Elementor ID');
    };
    $unit = static fn($n,$u='px') => ['unit'=>$u,'size'=>$n,'sizes'=>[]];
    $gap = static fn($n) => [
        'unit'=>'px','size'=>$n,'column'=>(string)$n,'row'=>(string)$n,'isLinked'=>true
    ];
    $container = [
        'id'=>$newId('container'),
        'elType'=>'container',
        'isInner'=>true,
        'settings'=>[
            '_title'=>'APAC statistic',
            'content_width'=>'full',
            'width'=>$unit(18,'%'),
            'width_tablet'=>$unit(100,'%'),
            'width_mobile'=>$unit(100,'%'),
            'flex_direction'=>'column',
            'flex_justify_content'=>'center',
            'flex_align_items'=>'center',
            'flex_align_items_tablet'=>'flex-start',
            'flex_align_items_mobile'=>'flex-start',
            'flex_gap'=>$gap(4),
        ],
        'elements'=>[
            [
                'id'=>'0000012',
                'elType'=>'widget',
                'widgetType'=>'heading',
                'settings'=>[
                    '_title'=>'APAC — statistic heading',
                    'title'=>'APAC',
                    'header_size'=>'div',
                    'align'=>'center',
                    'align_tablet'=>'left',
                    'align_mobile'=>'left',
                    'typography_typography'=>'custom',
                    'typography_font_size'=>$unit(50),
                    'typography_font_size_tablet'=>$unit(42),
                    'typography_font_size_mobile'=>$unit(40),
                    'typography_line_height'=>$unit(1.08,'em'),
                    '__globals__'=>[
                        'typography_typography'=>'globals/typography?id=primary',
                        'title_color'=>'globals/colors?id=primary'
                    ]
                ],
                'elements'=>[]
            ],
            [
                'id'=>$newId('label'),
                'elType'=>'widget',
                'widgetType'=>'heading',
                'settings'=>[
                    '_title'=>'Experience — statistic label',
                    'title'=>'Experience',
                    'header_size'=>'div',
                    'align'=>'center',
                    'align_tablet'=>'left',
                    'align_mobile'=>'left',
                    'typography_typography'=>'custom',
                    'typography_font_size'=>$unit(16),
                    'typography_line_height'=>$unit(1.3,'em'),
                    '__globals__'=>[
                        'typography_typography'=>'globals/typography?id=text',
                        'title_color'=>'globals/colors?id=secondary'
                    ]
                ],
                'elements'=>[]
            ]
        ]
    ];
    $children[3] = $container;
}
$after = wp_json_encode($data);
if (!$after) WP_CLI::error('Could not encode updated Elementor data');
WP_CLI::log($alreadyUpdated
    ? 'APAC already updated; no layout changes required.'
    : 'APAC now has a large global Cormorant heading and Inter label underneath.');
WP_CLI::log('Scope: only APAC item; photo, 35+/3500+ counters, header/footer untouched.');

if ($mode === 'dry-run') {
    WP_CLI::success('DRY RUN PASSED — no WordPress changes');
    return;
}
if ($after === $before) {
    WP_CLI::success('APAC was already styled; no writes necessary');
    return;
}
$dir = '/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($dir) || !is_writable($dir)) WP_CLI::error('Private backups directory unavailable');
$backup = $dir.'/hero-apac-before-'.gmdate('Ymd-His').'-'.
    wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup,$before)===false) WP_CLI::error('Could not back up Elementor data');
chmod($backup,0600);
update_post_meta(21,'_elementor_data',wp_slash($after));
if (get_post_meta(21,'_elementor_data',true)!==$after) {
    WP_CLI::error('APAC update verification failed; backup: '.$backup);
}
clean_post_cache(21);
WP_CLI::success('APAC display updated. Backup: '.$backup);
