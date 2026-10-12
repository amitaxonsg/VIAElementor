<?php
/**
 * VIA Frontiers — 2-card homepage hero, with native Elementor Counter widgets.
 *
 * Dry run: wp --path=/home/amitaxon/public_html eval-file hero-two-cards.php dry-run
 * Apply:   wp --path=/home/amitaxon/public_html eval-file hero-two-cards.php apply
 *
 * - Home page 21 only; hero node 0000015 only.
 * - Approved copy, CTA destinations, logo, header/footer, and all other sections preserved.
 * - Removes the floating portrait only from this hero's Elementor structure; backup retains it.
 * - Uses Elementor Site Kit colors & fonts; no extra JS or globally applied CSS.
 */

if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit("Run using WP-CLI only\n");
}
$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run', 'apply'], true)) {
    WP_CLI::error('Usage: dry-run or apply');
}
if (site_url() !== 'https://axonserver.com' ||
    home_url() !== 'https://axonserver.com' ||
    (int)get_option('page_on_front') !== 21 ||
    (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Wrong development website, homepage or Elementor Site Kit');
}
$page = get_post(21);
if (!$page || $page->post_type !== 'page' ||
    $page->post_status !== 'publish' ||
    $page->post_name !== 'via-frontiers-homepage-draft') {
    WP_CLI::error('Homepage identity mismatch');
}
$counterWidget = \Elementor\Plugin::instance()->widgets_manager->get_widget_types('counter');
if (!$counterWidget || $counterWidget->get_name() !== 'counter') {
    WP_CLI::error('Elementor Free Counter widget is unavailable — no change made');
}
$requiredControls = ['starting_number','ending_number','suffix','duration','title',
    'thousand_separator','number_color','title_color','typography_number_typography',
    'typography_title_typography'];
$controls = $counterWidget->get_controls();
foreach ($requiredControls as $name) {
    if (!isset($controls[$name])) {
        WP_CLI::error("Installed Counter widget lacks expected control: {$name}");
    }
}

$old = get_post_meta(21, '_elementor_data', true);
$data = json_decode((string)$old, true);
if (!is_array($data) || !$data) WP_CLI::error('Invalid homepage Elementor JSON');
$kit = get_post_meta(19, '_elementor_page_settings', true);
if (!is_array($kit)) WP_CLI::error('Elementor Site Kit settings unavailable');
$colors = [];
foreach (array_merge($kit['system_colors'] ?? [], $kit['custom_colors'] ?? []) as $item) {
    if (isset($item['_id'], $item['color'])) {
        $colors[$item['_id']] = strtoupper($item['color']);
    }
}
foreach (['primary','accent','via_stone','via_white'] as $required) {
    if (empty($colors[$required])) WP_CLI::error("Missing Global Color {$required}");
}
$paper = 'via_white';
foreach ($colors as $name=>$hex) {
    if ($hex === '#F9F6F0') { $paper = $name; break; }
}
$typography = [];
foreach (array_merge($kit['system_typography'] ?? [], $kit['custom_typography'] ?? []) as $style) {
    if (!empty($style['_id'])) $typography[$style['_id']] = true;
}
foreach (['primary','text','via_small_labels'] as $name) {
    if (empty($typography[$name])) WP_CLI::error("Missing Global Font {$name}");
}

$heroIndex = null;
foreach ($data as $i=>$node) {
    if (($node['id'] ?? '') === '0000015' &&
        ($node['settings']['_title'] ?? '') === '02 Hero') {
        $heroIndex = $i;
        break;
    }
}
if ($heroIndex === null) WP_CLI::error('Hero section 0000015 not found');
$hero = &$data[$heroIndex];
$row = &$hero['elements'][0];
if (($row['id'] ?? '') !== '0000014') WP_CLI::error('Hero row changed');
$nodes = &$row['elements'];
if (!is_array($nodes) || !in_array(count($nodes), [2,3], true) ||
    ($nodes[0]['id'] ?? '') !== '000000c') {
    WP_CLI::error('Unexpected hero columns');
}
$alreadyTwo = count($nodes) === 2;
if ($alreadyTwo) {
    if (($nodes[1]['id'] ?? '') !== '0000013') {
        WP_CLI::error('2-card hero has unexpected right card');
    }
} else {
    if (($nodes[1]['id'] ?? '') !== '000000d' ||
        ($nodes[2]['id'] ?? '') !== '0000013' ||
        ($nodes[1]['widgetType'] ?? '') !== 'image') {
        WP_CLI::error('Original portrait/credential layout mismatch');
    }
    // Remove only the floating photo from this hero; it is retained in the backup.
    array_splice($nodes, 1, 1);
}
$copy = &$nodes[0];
$cred = &$nodes[1];

if (count($copy['elements'] ?? []) !== 4 ||
    ($copy['elements'][0]['id'] ?? '') !== '0000006' ||
    ($copy['elements'][1]['id'] ?? '') !== '0000007' ||
    ($copy['elements'][2]['id'] ?? '') !== '0000008' ||
    ($copy['elements'][3]['id'] ?? '') !== '000000b' ||
    ($copy['elements'][3]['elements'][0]['id'] ?? '') !== '0000009' ||
    ($copy['elements'][3]['elements'][1]['id'] ?? '') !== '000000a') {
    WP_CLI::error('Homepage headline/CTA widget structure changed');
}

$credentials = &$cred['elements'];
if (count($credentials ?? []) === 5) {
    $ids = array_map(static fn($n) => $n['id'] ?? null, $credentials);
    if ($ids !== ['000000e','000000f','0000010','0000011','0000012']) {
        WP_CLI::error('Credential content order unexpected');
    }
} elseif (count($credentials ?? []) === 6) {
    $ids = array_map(static fn($n) => $n['id'] ?? null, $credentials);
    if ($ids[0] !== '000000e' || $ids[1] !== '000000f' ||
        ($credentials[2]['widgetType'] ?? '') !== 'divider' ||
        $ids[3] !== '0000010' || $ids[4] !== '0000011' ||
        $ids[5] !== '0000012') {
        WP_CLI::error('Existing animated credential structure unexpected');
    }
} else {
    WP_CLI::error('Unexpected credential widget count');
}

$unit = static fn($size,$u='px') => ['unit'=>$u,'size'=>$size,'sizes'=>[]];
$spacing = static fn($t,$r,$b,$l) => [
    'unit'=>'px','top'=>(string)$t,'right'=>(string)$r,
    'bottom'=>(string)$b,'left'=>(string)$l,'isLinked'=>false
];
$gap = static fn($n) => [
    'unit'=>'px','size'=>$n,
    'column'=>(string)$n,'row'=>(string)$n,'isLinked'=>true
];
$globalColor = static function(array &$s,string $key,string $token): void {
    if (!isset($s['__globals__']) || !is_array($s['__globals__'])) $s['__globals__'] = [];
    unset($s[$key]);
    $s['__globals__'][$key] = 'globals/colors?id='.$token;
};
$globalFont = static function(array &$s,string $prefix,string $token): void {
    if (!isset($s['__globals__']) || !is_array($s['__globals__'])) $s['__globals__'] = [];
    $s[$prefix.'_typography'] = 'custom';
    unset($s[$prefix.'_font_family']);
    $s['__globals__'][$prefix.'_typography'] = 'globals/typography?id='.$token;
};

// Light outside surface and two inset editorial cards.
$s = &$hero['settings'];
$s['content_width'] = 'boxed';
$s['boxed_width'] = $unit(1392);
$s['background_background'] = 'classic';
$globalColor($s,'background_color','via_white');
$s['padding'] = $spacing(38,40,38,40);
$s['padding_tablet'] = $spacing(28,24,28,24);
$s['padding_mobile'] = $spacing(20,16,24,16);
unset($s['min_height'],$s['min_height_tablet'],$s['min_height_mobile']);

$r = &$row['settings'];
$r['content_width'] = 'full';
$r['flex_direction'] = 'row';
$r['flex_direction_tablet'] = 'column';
$r['flex_direction_mobile'] = 'column';
$r['flex_wrap'] = 'nowrap';
$r['flex_align_items'] = 'stretch';
$r['flex_align_items_tablet'] = 'stretch';
$r['flex_align_items_mobile'] = 'stretch';
$r['flex_justify_content'] = 'space-between';
$r['flex_gap'] = $gap(22);
$r['flex_gap_tablet'] = $gap(18);
$r['flex_gap_mobile'] = $gap(16);

foreach ([0=>59,1=>39] as $index=>$percent) {
    $c = &$nodes[$index]['settings'];
    $c['content_width'] = 'full';
    $c['width'] = $unit($percent,'%');
    $c['width_tablet'] = $unit(100,'%');
    $c['width_mobile'] = $unit(100,'%');
    $c['flex_direction'] = 'column';
    $c['background_background'] = 'classic';
    $globalColor($c,'background_color',$paper);
    $c['border_border'] = 'solid';
    $c['border_width'] = $spacing(1,1,1,1);
    $globalColor($c,'border_color','via_stone');
    $c['border_radius'] = $spacing(12,12,12,12);
    $c['padding'] = $index===0 ? $spacing(34,35,32,35) : $spacing(32,32,32,32);
    $c['padding_tablet'] = $spacing(28,26,28,26);
    $c['padding_mobile'] = $spacing(24,20,24,20);
}
unset($c);

$left = &$copy['settings'];
$left['flex_justify_content']='center';
$left['flex_align_items']='flex-start';
$left['flex_gap']=$gap(17);
$left['min_height']=$unit(390);
$left['min_height_tablet']=$unit(0);
$left['min_height_mobile']=$unit(0);

$kicker = &$copy['elements'][0]['settings'];
$globalFont($kicker,'typography','via_small_labels');
$globalColor($kicker,'text_color','accent');
$kicker['typography_font_size']=$unit(12);
$kicker['typography_text_transform']='uppercase';
$kicker['typography_letter_spacing']=$unit(1.1);

$heading = &$copy['elements'][1]['settings'];
$globalFont($heading,'typography','primary');
$globalColor($heading,'title_color','primary');
$heading['typography_font_size']=$unit(58);
$heading['typography_font_size_tablet']=$unit(46);
$heading['typography_font_size_mobile']=$unit(37);
$heading['typography_line_height']=$unit(1.06,'em');

$body = &$copy['elements'][2]['settings'];
$globalFont($body,'typography','text');
$globalColor($body,'text_color','primary');
$body['typography_font_size']=$unit(17);
$body['typography_font_size_mobile']=$unit(16);
$body['typography_line_height']=$unit(1.5,'em');

$btnrow = &$copy['elements'][3]['settings'];
$btnrow['flex_direction']='row';
$btnrow['flex_direction_tablet']='row';
$btnrow['flex_direction_mobile']='column';
$btnrow['flex_wrap']='wrap';
$btnrow['flex_gap']=$gap(10);
$btnrow['margin']=$spacing(14,0,0,0);

// Preserve content of Sylvia heading and biography but give the card tidy hierarchy.
$right = &$cred['settings'];
$right['flex_justify_content'] = 'center';
$right['flex_align_items'] = 'stretch';
$right['flex_gap'] = $gap(13);
$right['min_height'] = $unit(390);
$right['min_height_tablet'] = $unit(0);
$right['min_height_mobile'] = $unit(0);

$name = &$credentials[0]['settings'];
$globalFont($name,'typography','primary');
$globalColor($name,'title_color','primary');
$name['typography_font_size']=$unit(31);
$name['typography_font_size_mobile']=$unit(28);
$name['typography_line_height']=$unit(1.1,'em');

$role = &$credentials[1]['settings'];
$globalFont($role,'typography','text');
$globalColor($role,'text_color','primary');
$role['typography_font_size']=$unit(15);
$role['typography_line_height']=$unit(1.5,'em');

// Divider uses the corporate Stone Global Color.
if (count($credentials)===5) {
    $used = [];
    $scan = static function($ns) use (&$scan,&$used): void {
        foreach ($ns as $node) {
            if (isset($node['id'])) $used[$node['id']]=true;
            if (!empty($node['elements'])) $scan($node['elements']);
        }
    };
    $scan($data);
    $seed=0;
    do {
        $dividerId=substr(md5('via-editorial-stat-divider-'.$seed++),0,7);
    } while(isset($used[$dividerId]));
    $divider=[
        'id'=>$dividerId,
        'elType'=>'widget',
        'widgetType'=>'divider',
        'settings'=>[
            '_title'=>'Sylvia Credentials Divider',
            'weight'=>$unit(1),
            'gap'=>$unit(7),
            '__globals__'=>['color'=>'globals/colors?id=via_stone'],
        ],
        'elements'=>[],
    ];
    array_splice($credentials,2,0,[$divider]);
}

$counter = static function(array $node,int $final,string $label,int $duration)
    use ($unit,$globalColor,$globalFont): array {
    $node['elType']='widget';
    $node['widgetType']='counter';
    $node['elements']=[];
    $node['settings']=[
        '_title'=>"Animated {$final}+ {$label}",
        'starting_number'=>0,
        'ending_number'=>$final,
        'suffix'=>'+',
        'duration'=>$duration,
        'thousand_separator'=>'',
        'title'=>$label,
        'title_position'=>'end',
        'title_horizontal_alignment'=>'start',
        'number_position'=>'start',
        'typography_number_typography'=>'custom',
        'typography_number_font_size'=>$unit(32),
        'typography_number_line_height'=>$unit(1.1,'em'),
        'typography_title_typography'=>'custom',
        'typography_title_font_size'=>$unit(15),
        '__globals__'=>[],
    ];
    $settings=&$node['settings'];
    $globalFont($settings,'typography_number','primary');
    $globalFont($settings,'typography_title','text');
    $globalColor($settings,'number_color','primary');
    $globalColor($settings,'title_color','primary');
    return $node;
};
$credentials[3]=$counter($credentials[3],35,'Years Experience',1800);
$credentials[4]=$counter($credentials[4],3500,'Clients',2300);

$apac = &$credentials[5]['settings'];
$apac['editor']='<p><strong>APAC Experience</strong></p>';
$globalFont($apac,'typography','text');
$globalColor($apac,'text_color','primary');
$apac['typography_font_size']=$unit(16);
$apac['typography_font_weight']='700';

$after=wp_json_encode($data);
if (!$after || !is_array(json_decode($after,true))) {
    WP_CLI::error('Generated Elementor data invalid');
}
WP_CLI::log('Validated Hero 0000015. Two editable cards, no floating portrait.');
WP_CLI::log('Native Elementor Counter widgets: 35+ Years Experience and 3500+ Clients.');
WP_CLI::log('APAC Experience is bold. All fonts/colors use Elementor Globals.');
WP_CLI::log('Other sections, site header/footer, navigation, and page content unchanged.');

if ($mode==='dry-run') {
    WP_CLI::success('DRY RUN PASSED — no changes');
    return;
}

$backupDir='/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($backupDir) || !is_writable($backupDir)) {
    WP_CLI::error('Private backup directory is not writable');
}
$backup=$backupDir.'/hero-two-cards-21-'.gmdate('Ymd-His').'-'.
    wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup,$old)===false) WP_CLI::error('Backup failed — no changes made');
chmod($backup,0600);

if ($after!==$old) update_post_meta(21,'_elementor_data',wp_slash($after));
if (get_post_meta(21,'_elementor_data',true)!==$after) {
    WP_CLI::error('Verification failed; backup: '.$backup);
}
clean_post_cache(21);
WP_CLI::success('Hero updated to 2 cards with native counters. Backup: '.$backup);
