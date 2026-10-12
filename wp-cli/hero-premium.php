<?php
/** VIA homepage hero: native Elementor layout refinement; dry-run by default. */
if (!defined('WP_CLI') || !defined('ABSPATH')) exit("WP-CLI required\n");
if (home_url() !== 'https://axonserver.com' || site_url() !== 'https://axonserver.com' ||
    (int)get_option('elementor_active_kit') !== 19 || (int)get_option('page_on_front') !== 21) {
    WP_CLI::error('Wrong WordPress installation, Site Kit, or homepage');
}
$p=get_post(21);
if (!$p || $p->post_type!=='page' || $p->post_status!=='publish' || $p->post_name!=='via-frontiers-homepage-draft') {
    WP_CLI::error('Homepage identity mismatch');
}
$old=get_post_meta(21,'_elementor_data',true);
$data=json_decode((string)$old,true);
if (!is_array($data)) WP_CLI::error('Invalid Elementor data');
$kit=get_post_meta(19,'_elementor_page_settings',true);
if (!is_array($kit)) WP_CLI::error('Invalid Elementor globals');
$colors=[];
foreach (array_merge($kit['system_colors']??[], $kit['custom_colors']??[]) as $c) {
    if (!empty($c['_id']) && !empty($c['color'])) $colors[$c['_id']]=$c['color'];
}
foreach (['primary','accent','via_stone','via_white'] as $token) {
    if (empty($colors[$token])) WP_CLI::error("Missing global token $token");
}
$paper='via_white';
foreach ($colors as $id=>$hex) if (strcasecmp($hex,'#F9F6F0')===0) $paper=$id;
$index=null;
foreach ($data as $i=>$n) if (($n['id']??'')==='0000015' && ($n['settings']['_title']??'')==='02 Hero') $index=$i;
if ($index===null) WP_CLI::error('Expected hero not found');
$hero=&$data[$index];
$row=&$hero['elements'][0];
if (($row['id']??'')!=='0000014' || count($row['elements']??[])!==3 ||
    ($row['elements'][0]['id']??'')!=='000000c' || ($row['elements'][1]['id']??'')!=='000000d' ||
    ($row['elements'][2]['id']??'')!=='0000013') WP_CLI::error('Hero structure mismatch');
$copy=&$row['elements'][0]; $photo=&$row['elements'][1]; $cred=&$row['elements'][2];
if (count($copy['elements']??[])!==4 || ($copy['elements'][1]['id']??'')!=='0000007' ||
    ($copy['elements'][3]['id']??'')!=='000000b') WP_CLI::error('Hero text/button structure changed');
$buttons=&$copy['elements'][3]['elements'];
if (count($buttons??[])!==2 || ($buttons[0]['id']??'')!=='0000009' ||
    ($buttons[1]['id']??'')!=='000000a') WP_CLI::error('CTA structure changed');
$size=fn($n,$unit='px')=>['unit'=>$unit,'size'=>$n,'sizes'=>[]];
$box=fn($t,$r,$b,$l)=>['unit'=>'px','top'=>(string)$t,'right'=>(string)$r,'bottom'=>(string)$b,'left'=>(string)$l,'isLinked'=>false];
$gap=fn($n)=>['unit'=>'px','size'=>$n,'column'=>(string)$n,'row'=>(string)$n,'isLinked'=>true];
$color=function(&$s,$property,$id) {unset($s[$property]);$s['__globals__'][$property]='globals/colors?id='.$id;};
$font=function(&$s,$property,$id) {$s['__globals__'][$property]='globals/typography?id='.$id;$s[$property]='custom';unset($s['typography_font_family']);};
// Light editorial section with controlled spacing and native responsive settings.
$h=&$hero['settings'];$h['content_width']='boxed';$h['boxed_width']=$size(1392);
$h['background_background']='classic';$color($h,'background_color',$paper);
$h['padding']=$box(45,48,42,48);$h['padding_tablet']=$box(35,28,30,28);$h['padding_mobile']=$box(30,20,30,20);
$h['min_height']=$size(490);$h['min_height_tablet']=$size(0);$h['min_height_mobile']=$size(0);
$r=&$row['settings'];$r['flex_direction']='row';$r['flex_direction_tablet']='column';$r['flex_direction_mobile']='column';
$r['flex_wrap']='nowrap';$r['flex_align_items']='center';$r['flex_align_items_tablet']='center';$r['flex_align_items_mobile']='center';
$r['flex_justify_content']='space-between';$r['flex_gap']=$gap(20);$r['flex_gap_tablet']=$gap(18);$r['flex_gap_mobile']=$gap(18);
$c=&$copy['settings'];$c['width']=$size(43,'%');$c['width_tablet']=$size(100,'%');$c['width_mobile']=$size(100,'%');
$c['flex_direction']='column';$c['flex_justify_content']='center';$c['flex_align_items']='flex-start';$c['flex_gap']=$gap(15);
$img=&$photo['settings'];$img['width']=$size(320);$img['width_tablet']=$size(300);$img['width_mobile']=$size(270);
$img['height']=$size(420);$img['height_tablet']=$size(360);$img['height_mobile']=$size(330);
$img['object_fit']='contain';$img['align']='center';
$cc=&$cred['settings'];$cc['width']=$size(20,'%');$cc['width_tablet']=$size(100,'%');$cc['width_mobile']=$size(100,'%');
$cc['flex_direction']='column';$cc['flex_gap']=$gap(12);$cc['padding']=$box(12,0,12,20);
$cc['padding_tablet']=$box(18,0,0,0);$cc['padding_mobile']=$box(16,0,0,0);
$cc['background_background']='classic';$color($cc,'background_color',$paper);
$cc['border_border']='solid';$cc['border_width']=$box(0,0,0,1);
$cc['border_width_tablet']=$box(1,0,0,0);$cc['border_width_mobile']=$box(1,0,0,0);
$cc['border_radius']=$box(0,0,0,0);$color($cc,'border_color','via_stone');
// Brand typography remains connected to active Elementor Site Kit.
$kick=&$copy['elements'][0]['settings'];$font($kick,'typography_typography','via_small_labels');$color($kick,'text_color','accent');
$kick['typography_font_size']=$size(13);$kick['typography_text_transform']='uppercase';$kick['typography_letter_spacing']=$size(1.2);
$title=&$copy['elements'][1]['settings'];$font($title,'typography_typography','primary');$color($title,'title_color','primary');
$title['typography_font_size']=$size(62);$title['typography_font_size_tablet']=$size(48);$title['typography_font_size_mobile']=$size(38);
$title['typography_line_height']=$size(1.05,'em');
$body=&$copy['elements'][2]['settings'];$font($body,'typography_typography','text');$color($body,'text_color','primary');
$body['typography_font_size']=$size(17);$body['typography_font_size_mobile']=$size(16);
$body['typography_line_height']=$size(1.5,'em');
$br=&$copy['elements'][3]['settings'];$br['flex_direction']='row';$br['flex_direction_mobile']='column';$br['flex_wrap']='wrap';
$br['flex_gap']=$gap(12);$br['margin']=$box(16,0,0,0);
foreach ($buttons as $i=>&$button) {
    $s=&$button['settings'];$font($s,'typography_typography','via_buttons');
    $s['border_border']='solid';$s['border_width']=$box(1,1,1,1);$s['border_radius']=$box(6,6,6,6);
    $s['text_padding']=$box(15,21,15,21);
    $color($s,'background_color',$i===0?'accent':'via_white');
    $color($s,'button_text_color',$i===0?'via_white':'primary');
    $color($s,'border_color',$i===0?'accent':'primary');
    $color($s,'button_background_hover_color',$i===0?'primary':'accent');
    $color($s,'hover_color','via_white');
}
unset($button);
// Preserve every credential's exact content, only normalize the hierarchy.
foreach ($cred['elements'] as $i=>&$el) {
    $s=&$el['settings'];if (($el['widgetType']??'')==='heading') {
        $font($s,'typography_typography','primary');$color($s,'title_color','primary');
        $s['typography_font_size']=$size($i===0?25:23);$s['typography_line_height']=$size(1.12,'em');
    } elseif (($el['widgetType']??'')==='text-editor') {
        $font($s,'typography_typography','text');$color($s,'text_color','primary');
        $s['typography_font_size']=$size(14);$s['typography_line_height']=$size(1.5,'em');
    }
}
unset($el);
$after=wp_json_encode($data);if (!$after) WP_CLI::error('JSON output invalid');
WP_CLI::log('Verified homepage Hero 0000015; preserved header, footer, other sections, text and images.');
WP_CLI::log('Design: light editorial, three columns, Inter summary, Cormorant heading, primary/outline CTAs.');
WP_CLI::log('All font families and corporate colors are referenced from Elementor Globals.');
if (($args[0]??'dry-run')!=='apply') {WP_CLI::success('DRY RUN PASSED — no changes');return;}
$dir='/home/amitaxon/via-fullsite-deployment/backups';if (!is_dir($dir)||!is_writable($dir)) WP_CLI::error('No private backup directory');
$bak=$dir.'/hero-premium-21-'.gmdate('Ymd-His').'-'.wp_generate_password(8,false,false).'.json';
if (file_put_contents($bak,$old)===false) WP_CLI::error('Backup failed');chmod($bak,0600);
if ($after!==$old) update_post_meta(21,'_elementor_data',wp_slash($after));
if (get_post_meta(21,'_elementor_data',true)!==$after) WP_CLI::error('Write verification failed; backup '.$bak);
clean_post_cache(21);
WP_CLI::success('Homepage hero updated. Backup: '.$bak);
