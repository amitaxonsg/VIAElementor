<?php
/**
 * VIA Frontiers — card-based Premium Editorial hero (v2).
 *
 * Usage:
 *   wp eval-file hero-editorial-cards.php dry-run
 *   wp eval-file hero-editorial-cards.php apply
 *
 * Scope: homepage 21, Hero 0000015 only. Header/footer and all other
 * page sections are preserved. Approved branding is read from Elementor Kit 19.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");

if (home_url() !== 'https://axonserver.com' ||
    site_url() !== 'https://axonserver.com' ||
    (int)get_option('page_on_front') !== 21 ||
    (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Wrong development site, homepage, or Elementor Kit');
}
$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run', 'apply'], true)) WP_CLI::error('Use dry-run or apply');

$page = get_post(21);
if (!$page || $page->post_type !== 'page' ||
    $page->post_status !== 'publish' ||
    $page->post_name !== 'via-frontiers-homepage-draft') {
    WP_CLI::error('Homepage identity mismatch');
}

$before = get_post_meta(21, '_elementor_data', true);
$data = json_decode((string)$before, true);
if (!is_array($data) || !$data) WP_CLI::error('Invalid homepage Elementor data');

$kit = get_post_meta(19, '_elementor_page_settings', true);
if (!is_array($kit)) WP_CLI::error('Elementor Site Kit settings unavailable');
$available = [];
foreach (array_merge($kit['system_colors'] ?? [], $kit['custom_colors'] ?? []) as $item) {
    if (isset($item['_id'], $item['color'])) $available[$item['_id']] = strtoupper($item['color']);
}
foreach (['primary', 'accent', 'via_white', 'via_stone'] as $required) {
    if (empty($available[$required])) WP_CLI::error("Missing Elementor Global Color: $required");
}
$paper = 'via_white';
foreach ($available as $name => $hex) {
    if ($hex === '#F9F6F0') { $paper = $name; break; }
}

$heroKey = null;
foreach ($data as $i => $element) {
    if (($element['id'] ?? '') === '0000015' &&
        ($element['settings']['_title'] ?? '') === '02 Hero') {
        $heroKey = $i;
        break;
    }
}
if ($heroKey === null) WP_CLI::error('Hero section 0000015 not found');

$hero = &$data[$heroKey];
$row = &$hero['elements'][0];
if (($row['id'] ?? '') !== '0000014' ||
    count($row['elements'] ?? []) !== 3 ||
    ($row['elements'][0]['id'] ?? '') !== '000000c' ||
    ($row['elements'][1]['id'] ?? '') !== '000000d' ||
    ($row['elements'][2]['id'] ?? '') !== '0000013') {
    WP_CLI::error('Hero structure changed. No updates made');
}
$copy = &$row['elements'][0];
$portrait = &$row['elements'][1];
$cred = &$row['elements'][2];

if (($copy['elements'][1]['id'] ?? '') !== '0000007' ||
    ($copy['elements'][3]['id'] ?? '') !== '000000b' ||
    ($copy['elements'][3]['elements'][0]['id'] ?? '') !== '0000009' ||
    ($copy['elements'][3]['elements'][1]['id'] ?? '') !== '000000a') {
    WP_CLI::error('Editorial copy or CTA structure changed');
}

$sz = static fn($value, $unit='px') => ['unit'=>$unit, 'size'=>$value, 'sizes'=>[]];
$pad = static fn($top,$right,$bottom,$left) => [
    'unit'=>'px', 'top'=>(string)$top, 'right'=>(string)$right,
    'bottom'=>(string)$bottom, 'left'=>(string)$left, 'isLinked'=>false
];
$gap = static fn($n) => [
    'unit'=>'px', 'size'=>$n, 'column'=>(string)$n,
    'row'=>(string)$n, 'isLinked'=>true
];
$globalColor = static function(array &$settings, string $key, string $token): void {
    $settings['__globals__'][$key] = 'globals/colors?id=' . $token;
    unset($settings[$key]);
};

// Soft background shared by the whole hero. Hero is a single Elementor section.
$hs = &$hero['settings'];
$hs['content_width'] = 'boxed';
$hs['boxed_width'] = $sz(1392);
$hs['background_background'] = 'classic';
$globalColor($hs, 'background_color', $paper);
$hs['padding'] = $pad(38,40,38,40);
$hs['padding_tablet'] = $pad(30,24,32,24);
$hs['padding_mobile'] = $pad(22,16,26,16);
$hs['min_height'] = $sz(490);
$hs['min_height_tablet'] = $sz(0);
$hs['min_height_mobile'] = $sz(0);

// Three desktop elements, single-column responsive flow.
$rs = &$row['settings'];
$rs['flex_direction'] = 'row';
$rs['flex_direction_tablet'] = 'column';
$rs['flex_direction_mobile'] = 'column';
$rs['flex_wrap'] = 'nowrap';
$rs['flex_align_items'] = 'center';
$rs['flex_align_items_tablet'] = 'stretch';
$rs['flex_align_items_mobile'] = 'stretch';
$rs['flex_justify_content'] = 'space-between';
$rs['flex_gap'] = $gap(20);
$rs['flex_gap_tablet'] = $gap(18);
$rs['flex_gap_mobile'] = $gap(16);

// Left: understated white editorial panel with a thin orange accent edge.
$cs = &$copy['settings'];
$cs['content_width'] = 'full';
$cs['width'] = $sz(45, '%');
$cs['width_tablet'] = $sz(100, '%');
$cs['width_mobile'] = $sz(100, '%');
$cs['flex_direction'] = 'column';
$cs['flex_justify_content'] = 'center';
$cs['flex_align_items'] = 'flex-start';
$cs['flex_gap'] = $gap(14);
$cs['padding'] = $pad(32,30,32,30);
$cs['padding_tablet'] = $pad(25,24,25,24);
$cs['padding_mobile'] = $pad(22,18,22,18);
$cs['background_background'] = 'classic';
$globalColor($cs, 'background_color', 'via_white');
$cs['border_border'] = 'solid';
$cs['border_width'] = $pad(1,1,1,3);
$globalColor($cs, 'border_color', 'via_stone');
$cs['border_radius'] = $pad(14,14,14,14);

// Keep approved Cormorant/Inter references, and allow local responsive sizes.
$heading = &$copy['elements'][1]['settings'];
$heading['typography_font_size'] = $sz(54);
$heading['typography_font_size_tablet'] = $sz(45);
$heading['typography_font_size_mobile'] = $sz(37);
$heading['typography_line_height'] = $sz(1.07, 'em');
$heading['margin'] = $pad(2,0,0,0);
$lede = &$copy['elements'][2]['settings'];
$lede['typography_font_size'] = $sz(17);
$lede['typography_font_size_mobile'] = $sz(16);
$lede['typography_line_height'] = $sz(1.5,'em');

// Preserve CTA text, destinations and Elementor global bindings.
// The primary/outline styling was already approved in v1.
$ctarow = &$copy['elements'][3]['settings'];
$ctarow['flex_direction'] = 'row';
$ctarow['flex_direction_mobile'] = 'column';
$ctarow['flex_wrap'] = 'wrap';
$ctarow['flex_gap'] = $gap(10);
$ctarow['margin'] = $pad(16,0,0,0);

// Center: approved original Sylvia photo, uncropped, aligned to panel bottom.
$is = &$portrait['settings'];
$is['width'] = $sz(330);
$is['width_tablet'] = $sz(305);
$is['width_mobile'] = $sz(280);
$is['height'] = $sz(430);
$is['height_tablet'] = $sz(380);
$is['height_mobile'] = $sz(350);
$is['object_fit'] = 'contain';
$is['align'] = 'center';
$is['align_tablet'] = 'center';
$is['align_mobile'] = 'center';

// Right: lighter, visually distinct credentials card.
$cr = &$cred['settings'];
$cr['content_width'] = 'full';
$cr['width'] = $sz(24,'%');
$cr['width_tablet'] = $sz(100,'%');
$cr['width_mobile'] = $sz(100,'%');
$cr['flex_direction'] = 'column';
$cr['flex_gap'] = $gap(12);
$cr['padding'] = $pad(27,23,27,23);
$cr['padding_tablet'] = $pad(24,22,24,22);
$cr['padding_mobile'] = $pad(22,18,22,18);
$cr['background_background'] = 'classic';
$globalColor($cr, 'background_color', 'via_white');
$cr['border_border'] = 'solid';
$cr['border_width'] = $pad(1,1,1,1);
$cr['border_width_mobile'] = $pad(1,1,1,1);
$cr['border_radius'] = $pad(14,14,14,14);
$globalColor($cr, 'border_color', 'via_stone');

// Keep credentials themselves untouched so the customer can edit each widget.
$after = wp_json_encode($data);
if (!$after || !is_array(json_decode($after,true))) WP_CLI::error('Invalid generated Elementor data');

WP_CLI::log('Hero 0000015 found. Updating layout only; heading, photo, cards and CTA content preserved.');
WP_CLI::log('Approved globals retained: Navy/Orange, Cormorant Garamond/Inter.');
WP_CLI::log('Homepage shared Header/Footer, navigation, and all other pages remain unchanged.');

if ($mode === 'dry-run') {
    WP_CLI::success('DRY RUN PASSED; no WordPress changes.');
    return;
}

$dir = '/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($dir) || !is_writable($dir)) WP_CLI::error('Private backup directory unavailable');
$file = $dir . '/hero-editorial-cards-21-' . gmdate('Ymd-His') . '-' .
    wp_generate_password(8,false,false) . '.json';
if (file_put_contents($file, $before) === false) WP_CLI::error('Backup failed, no changes made');
chmod($file,0600);
if ($before !== $after) update_post_meta(21,'_elementor_data',wp_slash($after));
if (get_post_meta(21,'_elementor_data',true) !== $after) {
    WP_CLI::error('Hero verification failed; rollback snapshot: ' . $file);
}
clean_post_cache(21);
WP_CLI::success('Editorial hero cards applied. Backup: ' . $file);
