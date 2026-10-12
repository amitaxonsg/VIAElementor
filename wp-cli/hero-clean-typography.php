<?php
/**
 * VIA Frontiers — remove photo-hero text box and divider; consistent Sylvia typography.
 *
 * Usage:
 *   wp eval-file hero-clean-typography.php dry-run
 *   wp eval-file hero-clean-typography.php apply
 *
 * Changes homepage page 21 / Hero 0000015 ONLY. Never modifies the header/footer,
 * approved background image, counters, CTAs or unrelated page sections.
 * Fonts/colors are Elementor Site Kit 19 Global Style references, not hardcoded.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit("WP-CLI only\n");
}
$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run','apply'], true)) {
    WP_CLI::error('Use dry-run or apply');
}
if (site_url() !== 'https://axonserver.com' ||
    home_url() !== 'https://axonserver.com' ||
    (int)get_option('page_on_front') !== 21 ||
    (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Wrong development site, homepage, or Elementor Kit');
}
$post=get_post(21);
if (!$post || $post->post_type!=='page' || $post->post_status!=='publish' ||
    $post->post_name!=='via-frontiers-homepage-draft') {
    WP_CLI::error('Homepage identity mismatch');
}
$kit=get_post_meta(19,'_elementor_page_settings',true);
if (!is_array($kit)) WP_CLI::error('Site Kit is unavailable');
$fontFamilies=[];
foreach (array_merge($kit['system_typography']??[], $kit['custom_typography']??[]) as $style) {
    if (isset($style['_id'])) {
        $fontFamilies[$style['_id']]=$style['typography_font_family']??'';
    }
}
foreach (['primary'=>'Cormorant Garamond','text'=>'Inter'] as $id=>$expected) {
    if (strcasecmp(trim((string)($fontFamilies[$id]??'')),$expected)!==0) {
        WP_CLI::error("Approved global font {$id} is not {$expected}");
    }
}
$palette=[];
foreach (array_merge($kit['system_colors']??[],$kit['custom_colors']??[]) as $style) {
    if (isset($style['_id'],$style['color'])) {
        $palette[$style['_id']]=strtoupper($style['color']);
    }
}
foreach (['primary'=>'#262A63','accent'=>'#F1592D'] as $id=>$expected) {
    if (($palette[$id]??'')!==$expected) {
        WP_CLI::error("Global {$id} color does not match the approved palette");
    }
}
$original=get_post_meta(21,'_elementor_data',true);
$data=json_decode((string)$original,true);
if (!is_array($data)) WP_CLI::error('Homepage Elementor data is invalid');
$hero=null;
foreach ($data as &$node) {
    if (($node['id']??'')==='0000015' &&
        ($node['settings']['_title']??'')==='02 Hero') {
        $hero=&$node;
        break;
    }
}
unset($node);
if ($hero===null || count($hero['elements']??[])!==2 ||
    ($hero['elements'][0]['id']??'')!=='0000014' ||
    ($hero['elements'][1]['id']??'')!=='0000013') {
    WP_CLI::error('Approved photo hero and single stats strip not found');
}
$stage=&$hero['elements'][0];
$strip=&$hero['elements'][1];
if (empty($stage['settings']['background_image']['url']) ||
    count($stage['elements']??[])!==1 ||
    ($stage['elements'][0]['id']??'')!=='000000c') {
    WP_CLI::error('Photo stage background or hero text structure differs');
}
$copy=&$stage['elements'][0];
if (count($copy['elements']??[])!==4 ||
    ($copy['elements'][1]['id']??'')!=='0000007' ||
    ($copy['elements'][2]['id']??'')!=='0000008') {
    WP_CLI::error('Hero editorial text changed');
}
if (count($strip['elements']??[])!==4 ||
    ($strip['elements'][1]['id']??'')!=='0000010' ||
    ($strip['elements'][2]['id']??'')!=='0000011' ||
    ($strip['elements'][1]['widgetType']??'')!=='counter' ||
    ($strip['elements'][2]['widgetType']??'')!=='counter' ||
    (int)($strip['elements'][1]['settings']['ending_number']??0)!==35 ||
    (int)($strip['elements'][2]['settings']['ending_number']??0)!==3500) {
    WP_CLI::error('Approved animated statistics not found');
}
$ident=&$strip['elements'][0];
if (($ident['settings']['_title']??'')!=='Sylvia name and credentials' ||
    count($ident['elements']??[])!==2 ||
    ($ident['elements'][0]['id']??'')!=='000000e' ||
    ($ident['elements'][0]['widgetType']??'')!=='heading' ||
    ($ident['elements'][1]['id']??'')!=='000000f' ||
    ($ident['elements'][1]['widgetType']??'')!=='text-editor') {
    WP_CLI::error('Sylvia name/credentials structure changed');
}

$unit=static fn($n,$u='px')=>['unit'=>$u,'size'=>$n,'sizes'=>[]];
$pad=static fn($t,$r,$b,$l)=>[
    'unit'=>'px','top'=>(string)$t,'right'=>(string)$r,
    'bottom'=>(string)$b,'left'=>(string)$l,'isLinked'=>false
];
$linkFont=static function(array &$settings,string $id):void {
    if (!isset($settings['__globals__']) || !is_array($settings['__globals__'])) {
        $settings['__globals__']=[];
    }
    $settings['typography_typography']='custom';
    unset($settings['typography_font_family']);
    $settings['__globals__']['typography_typography']='globals/typography?id='.$id;
};
$linkColor=static function(array &$settings,string $control,string $id):void {
    if (!isset($settings['__globals__']) || !is_array($settings['__globals__'])) {
        $settings['__globals__']=[];
    }
    unset($settings[$control]);
    $settings['__globals__'][$control]='globals/colors?id='.$id;
};

// Remove the opaque white card, rounded outline, and border from the left copy.
// Keep text in a comfortable position on the approved bright photograph.
$left=&$copy['settings'];
unset(
    $left['background_background'], $left['background_color'],
    $left['border_border'], $left['border_color'],
    $left['border_width'], $left['border_width_tablet'],
    $left['border_width_mobile'], $left['border_radius'],
    $left['box_shadow_box_shadow_type'], $left['box_shadow_box_shadow'],
    $left['background_overlay_background'], $left['background_overlay_color']
);
if (isset($left['__globals__']) && is_array($left['__globals__'])) {
    unset($left['__globals__']['background_color'],
        $left['__globals__']['border_color'],
        $left['__globals__']['background_overlay_color']);
}
$left['padding']=$pad(22,28,22,28);
$left['padding_tablet']=$pad(20,20,20,20);
$left['padding_mobile']=$pad(16,14,16,14);

// Remove divider/outline between the image stage and the stats strip.
$band=&$strip['settings'];
unset($band['border_border'], $band['border_width'], $band['border_color'],
    $band['border_radius'], $band['border_width_tablet'], $band['border_width_mobile']);
if (isset($band['__globals__']) && is_array($band['__globals__'])) {
    unset($band['__globals__']['border_color']);
}

// Typography: approved heading font and body font, no extra typeface.
$headline=&$copy['elements'][1]['settings'];
$linkFont($headline,'primary');
$linkColor($headline,'title_color','primary');
$headline['typography_font_weight']='500';

$intro=&$copy['elements'][2]['settings'];
$linkFont($intro,'text');
$linkColor($intro,'text_color','primary');
$intro['typography_font_weight']='400';
$intro['typography_font_size']=$unit(17);
$intro['typography_font_size_mobile']=$unit(16);
$intro['typography_line_height']=$unit(1.52,'em');

// Sylvia's name: Orange, bold approved Cormorant Garamond.
// Credentials: regular Inter, Navy.
$name=&$ident['elements'][0]['settings'];
$linkFont($name,'primary');
$linkColor($name,'title_color','accent');
$name['typography_font_weight']='700';
$name['typography_font_size']=$unit(33);
$name['typography_font_size_tablet']=$unit(30);
$name['typography_font_size_mobile']=$unit(28);
$name['typography_line_height']=$unit(1.12,'em');

$credentials=&$ident['elements'][1]['settings'];
$linkFont($credentials,'text');
$linkColor($credentials,'text_color','primary');
$credentials['typography_font_weight']='400';
$credentials['typography_font_size']=$unit(16);
$credentials['typography_font_size_mobile']=$unit(15);
$credentials['typography_line_height']=$unit(1.45,'em');

$after=wp_json_encode($data);
if (!$after || !is_array(json_decode($after,true))) {
    WP_CLI::error('Could not generate valid Elementor JSON');
}
WP_CLI::log('Verified Hero 0000015, approved photograph, information strip, 35+/3500+ counters.');
WP_CLI::log('Left editorial panel background and border removed; statistics top border removed.');
WP_CLI::log('Sylvia: bold Global Cormorant Garamond + Global Orange. Credentials: regular Global Inter + Navy.');
WP_CLI::log('Only 2 approved font families; no new CSS or hardcoded corporate color values.');
WP_CLI::log('Header/Footer and all other homepage sections unchanged.');

if ($mode==='dry-run') {
    WP_CLI::success('DRY RUN PASSED — no changes');
    return;
}
if ($after===$original) {
    WP_CLI::success('Hero already matches these settings — no changes');
    return;
}
$dir='/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($dir) || !is_writable($dir)) {
    WP_CLI::error('Private backup directory unavailable');
}
$backup=$dir.'/hero-clean-typography-21-'.gmdate('Ymd-His').'-'.
    wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup,$original)===false) {
    WP_CLI::error('Backup failed — no changes applied');
}
chmod($backup,0600);
update_post_meta(21,'_elementor_data',wp_slash($after));
if (get_post_meta(21,'_elementor_data',true)!==$after) {
    WP_CLI::error('Write verification failed. Restore from: '.$backup);
}
clean_post_cache(21);
WP_CLI::success('VIA Hero updated. Backup: '.$backup);
