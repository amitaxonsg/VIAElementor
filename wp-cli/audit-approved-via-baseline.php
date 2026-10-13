<?php
/**
 * Read-only VIA Frontiers style and Saved Template baseline check.
 * wp eval-file audit-approved-via-baseline.php
 *
 * Does not regenerate CSS, change database, save files, or clear caches.
 * Output is safe for public ticket reports (no database credentials).
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");

if (site_url()!=='https://axonserver.com' || home_url()!=='https://axonserver.com' ||
    (int)get_option('page_on_front')!==21 || (int)get_option('elementor_active_kit')!==19)
    WP_CLI::error('Wrong WordPress site / front page / Elementor Global Kit.');

$errors=[];
foreach ([21=>'Homepage',57=>'Shared header',58=>'Shared footer',19=>'Site Kit'] as $id=>$label){
    $post=get_post($id);
    if(!$post || $post->post_status!=='publish')$errors[]="$label $id not published/missing";
}
$kit=get_post_meta(19,'_elementor_page_settings',true);
if (!is_array($kit))WP_CLI::error('Kit 19 Elementor settings are invalid.');
$colors=[];
foreach(array_merge($kit['system_colors']??[],$kit['custom_colors']??[]) as $item)
    if(!empty($item['_id']))$colors[$item['_id']]=strtoupper($item['color']??'');
$expected=['primary'=>'#262A63','accent'=>'#F1592D','via_blue'=>'#7499D0','via_white'=>'#FFFFFF'];
WP_CLI::log('=== VIA APPROVED GLOBAL COLOURS (SITE KIT 19) ===');
foreach($expected as $key=>$value){
    $actual=$colors[$key]??'MISSING';
    WP_CLI::log("$key: $actual (expected $value)");
    if($actual!==$value)$errors[]="Global colour $key differs";
}
$fonts=[];
foreach(array_merge($kit['system_typography']??[],$kit['custom_typography']??[]) as $item)
    if(!empty($item['_id']))$fonts[$item['_id']]=true;
foreach(['primary','text'] as $key)if(empty($fonts[$key]))$errors[]="Global Typography $key missing";

$dir=WP_CONTENT_DIR.'/uploads/elementor/css';
$kitCss="$dir/post-19.css";$homeCss="$dir/post-21.css";
WP_CLI::log('=== GENERATED ELEMENTOR STYLESHEETS ===');
foreach([$kitCss,$homeCss] as $css){
    WP_CLI::log(basename($css).': '.(is_file($css)?(filesize($css).' bytes'):'MISSING'));
    if(!is_file($css) || filesize($css)<100)$errors[]='Generated CSS missing: '.basename($css);
}
if (is_file($kitCss)){
    $css=(string)file_get_contents($kitCss);
    foreach($expected as $token=>$hex){
        $pattern='/--e-global-color-'.preg_quote($token,'/').'\s*:\s*'.preg_quote($hex,'/').'\s*(?:[;}])/i';
        if(!preg_match($pattern,$css))$errors[]='Kit CSS missing/mismatched variable '.$token;
    }
}

WP_CLI::log('=== CURRENT ELEMENTOR SAVED CONTAINER TEMPLATES ===');
$titles=[
 'Sylvia counter Flexbox',
 'homepage-03-Pathways',
 'homepage-04-FIRE-Approach',
 'homepage-05-Client-Voices',
 'homepage-06-Start-a-Conversation',
 'Inner Pages Hero Template'
];
$found=[];
foreach(get_posts(['post_type'=>'elementor_library','post_status'=>'any',
                   'posts_per_page'=>-1,'suppress_filters'=>true]) as $post){
    if(!in_array($post->post_title,$titles,true))continue;
    if(isset($found[$post->post_title]))$errors[]='Duplicate Saved Template title '.$post->post_title;
    $found[$post->post_title]=$post;
}
foreach($titles as $title){
    if(!isset($found[$title])){
        $errors[]='Missing Saved Template: '.$title;
        WP_CLI::log('MISSING: '.$title);
        continue;
    }
    $post=$found[$title];$type=get_post_meta($post->ID,'_elementor_template_type',true);
    $json=json_decode((string)get_post_meta($post->ID,'_elementor_data',true),true);
    $valid=$post->post_status==='publish' && $type==='container' && is_array($json) && count($json)>0;
    WP_CLI::log(($valid?'OK':'INVALID').' '.$post->ID.' '.$title.' ['.$type.']');
    if(!$valid)$errors[]='Invalid saved Container template '.$title;
}

WP_CLI::log('=== TYPOGRAPHY STUDIO (NO RULES MODIFIED) ===');
$rules=get_option('via_typography_studio_rules_v2',[]);
WP_CLI::log('Current saved rules: '.(is_array($rules)?count($rules):'INVALID'));
if(!is_array($rules))$errors[]='Typography Studio saved rules invalid';

WP_CLI::log('=== AUDIT RESULT ===');
if($errors){
    foreach($errors as $error)WP_CLI::warning($error);
    WP_CLI::error('Approved baseline check FAILED (read-only). Do not deploy templates until reviewed.');
}
WP_CLI::success('VIA APPROVED BASELINE PASSED. No changes made. Confirm homepage CSS is referenced by rendered HTML separately.');
