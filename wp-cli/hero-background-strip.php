<?php
/**
 * VIA Frontiers — photographic hero plus SINGLE credentials/statistics strip.
 * No third card. No extra JavaScript: Elementor Free Counter widgets animate natively.
 *
 * Inputs: approved original PNG at
 *   /home/amitaxon/894bb258-1b36-4047-b698-1e98075683c6.png
 *
 * Dry-run:
 *   wp eval-file hero-background-strip.php dry-run
 * Apply:
 *   wp eval-file hero-background-strip.php apply
 *
 * WordPress: axonserver.com development, homepage 21 only, Hero 0000015 only.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode=$args[0]??'dry-run';
if (!in_array($mode,['dry-run','apply'],true)) WP_CLI::error('Use dry-run or apply');
if (home_url()!=='https://axonserver.com' || site_url()!=='https://axonserver.com' ||
    (int)get_option('page_on_front')!==21 || (int)get_option('elementor_active_kit')!==19) {
    WP_CLI::error('Wrong development site/homepage/Elementor kit');
}
$home=get_post(21);
if (!$home || $home->post_type!=='page' || $home->post_status!=='publish' ||
    $home->post_name!=='via-frontiers-homepage-draft') WP_CLI::error('Homepage identity mismatch');
$source='/home/amitaxon/894bb258-1b36-4047-b698-1e98075683c6.png';
if (!is_file($source) || !is_readable($source)) WP_CLI::error('Upload the supplied background PNG to '.$source.' first');
if (filesize($source)<100000 || filesize($source)>25000000) WP_CLI::error('Unexpected background file size');
$imageInfo=@getimagesize($source);
if (!$imageInfo || ($imageInfo[2]??0)!==IMAGETYPE_PNG || $imageInfo[0]<1200 || $imageInfo[1]<500) {
    WP_CLI::error('Expected an approved wide PNG (at least 1200 × 500)');
}
$hash=hash_file('sha256',$source);
$old=get_post_meta(21,'_elementor_data',true);
$data=json_decode((string)$old,true);
if (!is_array($data) || !$data) WP_CLI::error('Homepage Elementor data invalid');

$kit=get_post_meta(19,'_elementor_page_settings',true);
if (!is_array($kit)) WP_CLI::error('Elementor Site Kit unavailable');
$colorIds=[];
foreach (array_merge($kit['system_colors']??[], $kit['custom_colors']??[]) as $entry) {
    if (!empty($entry['_id'])) $colorIds[$entry['_id']]=true;
}
$fontIds=[];
foreach (array_merge($kit['system_typography']??[], $kit['custom_typography']??[]) as $entry) {
    if (!empty($entry['_id'])) $fontIds[$entry['_id']]=true;
}
foreach (['primary','accent','via_white','via_stone'] as $key) {
    if (empty($colorIds[$key])) WP_CLI::error('Missing Global Color '.$key);
}
foreach (['primary','text','via_small_labels','via_buttons'] as $key) {
    if (empty($fontIds[$key])) WP_CLI::error('Missing Global Font '.$key);
}
$counterWidget=\Elementor\Plugin::instance()->widgets_manager->get_widget_types('counter');
if (!$counterWidget || $counterWidget->get_name()!=='counter') WP_CLI::error('Elementor Free counter widget not loaded');
$counterControls=$counterWidget->get_controls();
foreach (['starting_number','ending_number','suffix','duration','title'] as $key) {
    if (!isset($counterControls[$key])) WP_CLI::error('Counter missing essential setting '.$key);
}
// NOTE: Elementor 4.3.4 may omit number_color / title_color controls.
// Never fail the migration because those optional style controls are absent.

$heroKey=null;
foreach ($data as $i=>$node) {
    if (($node['id']??'')==='0000015' && ($node['settings']['_title']??'')==='02 Hero') {
        $heroKey=$i;break;
    }
}
if ($heroKey===null) WP_CLI::error('Hero container 0000015 not found');
$hero=&$data[$heroKey];
$row=&$hero['elements'][0];
if (($row['id']??'')!=='0000014' || count($row['elements']??[])!==3 ||
    ($row['elements'][0]['id']??'')!=='000000c' ||
    ($row['elements'][1]['id']??'')!=='000000d' ||
    ($row['elements'][1]['widgetType']??'')!=='image' ||
    ($row['elements'][2]['id']??'')!=='0000013') {
    WP_CLI::error('Expected current three-column hero not found; no changes');
}
$copy=&$row['elements'][0];
$credentials=&$row['elements'][2];
if (count($copy['elements']??[])!==4 ||
    ($copy['elements'][0]['id']??'')!=='0000006' ||
    ($copy['elements'][1]['id']??'')!=='0000007' ||
    ($copy['elements'][2]['id']??'')!=='0000008' ||
    ($copy['elements'][3]['id']??'')!=='000000b') WP_CLI::error('Hero copy differs from expected structure');
$buttons=$copy['elements'][3]['elements']??[];
if (count($buttons)!==2 || ($buttons[0]['id']??'')!=='0000009' ||
    ($buttons[1]['id']??'')!=='000000a') WP_CLI::error('CTA buttons changed');
$existing=$credentials['elements']??[];
if (count($existing)!==5 ||
    array_map(static fn($v)=>$v['id']??'', $existing)!==
    ['000000e','000000f','0000010','0000011','0000012']) {
    WP_CLI::error('Sylvia credentials changed; cannot safely rearrange');
}
$unit=static fn($n,$u='px')=>['unit'=>$u,'size'=>$n,'sizes'=>[]];
$space=static fn($t,$r,$b,$l)=>[
    'unit'=>'px','top'=>(string)$t,'right'=>(string)$r,
    'bottom'=>(string)$b,'left'=>(string)$l,'isLinked'=>false
];
$gap=static fn($n)=>['unit'=>'px','size'=>$n,'column'=>(string)$n,'row'=>(string)$n,'isLinked'=>true];
$setColor=static function(array &$s,string $key,string $token):void {
    if (!isset($s['__globals__']) || !is_array($s['__globals__'])) $s['__globals__']=[];
    unset($s[$key]);
    $s['__globals__'][$key]='globals/colors?id='.$token;
};
$setFont=static function(array &$s,string $prefix,string $token):void {
    if (!isset($s['__globals__']) || !is_array($s['__globals__'])) $s['__globals__']=[];
    $s[$prefix.'_typography']='custom';
    unset($s[$prefix.'_font_family']);
    $s['__globals__'][$prefix.'_typography']='globals/typography?id='.$token;
};
$uniqueId=static function(string $seed)use(&$data):string {
    $ids=[];
    $scan=static function($nodes)use(&$scan,&$ids):void {
        foreach ($nodes as $node) {
            if (!empty($node['id'])) $ids[$node['id']]=true;
            if (!empty($node['elements'])) $scan($node['elements']);
        }
    };
    $scan($data);
    for ($i=0;$i<100;$i++) {
        $candidate=substr(md5('via-photo-hero-'.$seed.'-'.$i),0,7);
        if (!isset($ids[$candidate]))return $candidate;
    }
    WP_CLI::error('Cannot allocate Elementor widget id');
};

// Use a stage with the photograph behind the headline, NOT an independent image widget.
// Outer section stacks the photo stage over one information strip.
$root=&$hero['settings'];
$root['content_width']='full';
$root['flex_direction']='column';
$root['flex_gap']=$gap(0);
$root['padding']=$space(0,0,0,0);
$root['padding_tablet']=$space(0,0,0,0);
$root['padding_mobile']=$space(0,0,0,0);
unset($root['min_height'],$root['min_height_tablet'],$root['min_height_mobile']);
$root['background_background']='classic';
$setColor($root,'background_color','via_white');

$stage=&$row['settings'];
$stage['content_width']='full';
$stage['flex_direction']='row';
$stage['flex_direction_tablet']='row';
$stage['flex_direction_mobile']='column';
$stage['flex_align_items']='center';
$stage['flex_justify_content']='flex-start';
$stage['flex_wrap']='nowrap';
$stage['flex_gap']=$gap(0);
$stage['padding']=$space(36,48,36,48);
$stage['padding_tablet']=$space(32,24,32,24);
$stage['padding_mobile']=$space(24,16,24,16);
$stage['min_height']=$unit(520);
$stage['min_height_tablet']=$unit(450);
$stage['min_height_mobile']=$unit(450);
$stage['background_background']='classic';
$stage['background_position']='center center';
$stage['background_position_tablet']='center center';
$stage['background_position_mobile']='center center';
$stage['background_repeat']='no-repeat';
$stage['background_size']='cover';

// Left readable white editorial card in front of the office background.
$left=&$copy['settings'];
$left['content_width']='full';
$left['width']=$unit(49,'%');
$left['width_tablet']=$unit(61,'%');
$left['width_mobile']=$unit(100,'%');
$left['flex_direction']='column';
$left['flex_justify_content']='center';
$left['flex_align_items']='flex-start';
$left['flex_gap']=$gap(14);
$left['padding']=$space(26,28,28,28);
$left['padding_tablet']=$space(23,22,23,22);
$left['padding_mobile']=$space(20,17,20,17);
$left['border_border']='solid';
$left['border_width']=$space(1,1,1,1);
$left['border_radius']=$space(10,10,10,10);
$left['background_background']='classic';
$setColor($left,'background_color','via_white');
$setColor($left,'border_color','via_stone');
$headline=&$copy['elements'][1]['settings'];
$setFont($headline,'typography','primary');
$setColor($headline,'title_color','primary');
$headline['typography_font_size']=$unit(54);
$headline['typography_font_size_tablet']=$unit(45);
$headline['typography_font_size_mobile']=$unit(36);
$headline['typography_line_height']=$unit(1.06,'em');
$kick=&$copy['elements'][0]['settings'];
$setFont($kick,'typography','via_small_labels');
$setColor($kick,'text_color','accent');
$kick['typography_font_size']=$unit(12);
$description=&$copy['elements'][2]['settings'];
$setFont($description,'typography','text');
$setColor($description,'text_color','primary');
$description['typography_font_size']=$unit(16);
$description['typography_line_height']=$unit(1.48,'em');
$buttonrow=&$copy['elements'][3]['settings'];
$buttonrow['flex_direction']='row';
$buttonrow['flex_direction_mobile']='column';
$buttonrow['flex_gap']=$gap(10);

// Remove floating hero portrait (the approved image becomes the background).
// KEEP the other two existing areas and move credentials into a single band below.
$infoName=$existing[0];$infoRoles=$existing[1];$years=$existing[2];$clients=$existing[3];$apac=$existing[4];

$nameContainer=[
    'id'=>$uniqueId('identity'),'elType'=>'container','isInner'=>true,
    'settings'=>[
        '_title'=>'Sylvia name and credentials',
        'content_width'=>'full','width'=>$unit(44,'%'),
        'width_tablet'=>$unit(100,'%'),'width_mobile'=>$unit(100,'%'),
        'flex_direction'=>'column','flex_gap'=>$gap(6)
    ],'elements'=>[$infoName,$infoRoles]
];

$counter=static function($node,int $end,string $label,int $duration)
    use($counterControls,$unit,$setFont,$setColor):array {
    $node['elType']='widget';$node['widgetType']='counter';$node['elements']=[];
    $node['settings']=[
        '_title'=>'VIA '.$end.'+ '.$label,
        'starting_number'=>0,'ending_number'=>$end,'suffix'=>'+',
        'duration'=>$duration,'title'=>$label,
        'thousand_separator'=>'', 'title_position'=>'after',
        'typography_number_typography'=>'custom',
        'typography_title_typography'=>'custom',
        'typography_number_font_size'=>$unit(28),
        'typography_title_font_size'=>$unit(13),
        '__globals__'=>[]
    ];
    $s=&$node['settings'];
    $setFont($s,'typography_number','primary');
    $setFont($s,'typography_title','text');
    if (isset($counterControls['number_color'])) $setColor($s,'number_color','primary');
    if (isset($counterControls['title_color'])) $setColor($s,'title_color','primary');
    return $node;
};
$years=$counter($years,35,'Years Experience',1800);
$clients=$counter($clients,3500,'Clients',2200);

$apac['settings']['editor']='<p><strong>APAC Experience</strong></p>';
$apac['settings']['typography_font_weight']='700';
$apac['settings']['typography_font_size']=$unit(16);
$setFont($apac['settings'],'typography','text');
$setColor($apac['settings'],'text_color','primary');

// Information under hero image: ONE strip, no third box.
$info=&$credentials['settings'];
$info['content_width']='boxed';
$info['boxed_width']=$unit(1392);
$info['flex_direction']='row';
$info['flex_direction_tablet']='column';
$info['flex_direction_mobile']='column';
$info['flex_wrap']='wrap';
$info['flex_align_items']='center';
$info['flex_align_items_tablet']='flex-start';
$info['flex_align_items_mobile']='flex-start';
$info['flex_justify_content']='space-between';
$info['flex_gap']=$gap(22);
$info['padding']=$space(22,44,22,44);
$info['padding_tablet']=$space(22,25,22,25);
$info['padding_mobile']=$space(22,20,25,20);
$info['width']=$unit(100,'%');
$info['width_tablet']=$unit(100,'%');
$info['width_mobile']=$unit(100,'%');
$info['border_border']='solid';
$info['border_width']=$space(1,0,0,0);
$info['border_radius']=$space(0,0,0,0);
$info['background_background']='classic';
$setColor($info,'background_color','via_white');
$setColor($info,'border_color','via_stone');
$credentials['elements']=[$nameContainer,$years,$clients,$apac];

$row['elements']=[$copy];
$hero['elements']=[$row,$credentials];

// Stage background is set once WordPress has imported the approved image.
$after=wp_json_encode($data);
if (!$after || !is_array(json_decode($after,true))) WP_CLI::error('Output Elementor JSON invalid');
WP_CLI::log('Validated original Hero, editable headline/buttons and Sylvia credentials.');
WP_CLI::log('Design: original background image, left copy/card, single strip below for credentials/counters/APAC.');
WP_CLI::log('No third card or floating portrait. Native Elementor Counter used without unavailable color control.');
WP_CLI::log('Approved PNG: '.$imageInfo[0].'x'.$imageInfo[1].', SHA256 '.substr($hash,0,12));
WP_CLI::log('Only homepage Hero 0000015 changes; other pages, shared header/footer and Site Kit stay untouched.');

if ($mode==='dry-run') {
    WP_CLI::success('DRY RUN PASSED — no changes');
    return;
}

$backupDir='/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($backupDir) || !is_writable($backupDir)) WP_CLI::error('Private backup directory unavailable');
$backup=$backupDir.'/hero-background-strip-21-'.gmdate('Ymd-His').'-'.
    wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup,$old)===false) WP_CLI::error('Private backup failed');
chmod($backup,0600);

// Reuse Media Library copy by SHA256 when available; otherwise import once.
$found=get_posts([
    'post_type'=>'attachment','post_status'=>'inherit','posts_per_page'=>1,
    'meta_key'=>'_via_hero_png_sha256','meta_value'=>$hash,'fields'=>'ids'
]);
$mediaId=$found?(int)$found[0]:0;
if (!$mediaId) {
    $bits=wp_upload_bits('via-frontiers-hero-background-'.substr($hash,0,12).'.png',
        null,file_get_contents($source));
    if (!empty($bits['error']) || empty($bits['file']) || empty($bits['url'])) {
        WP_CLI::error('WordPress Media upload failed; snapshot: '.$backup);
    }
    $mediaId=wp_insert_attachment([
        'post_mime_type'=>'image/png',
        'post_title'=>'VIA Frontiers approved hero background',
        'post_status'=>'inherit',
        'guid'=>$bits['url']
    ],$bits['file'],0,true);
    if (is_wp_error($mediaId) || !$mediaId) WP_CLI::error('Attachment creation failed');
    require_once ABSPATH.'wp-admin/includes/image.php';
    $metadata=wp_generate_attachment_metadata($mediaId,$bits['file']);
    if (is_array($metadata)) wp_update_attachment_metadata($mediaId,$metadata);
    update_post_meta($mediaId,'_wp_attachment_image_alt','Sylvia Fernandes in a bright professional meeting room');
    update_post_meta($mediaId,'_via_hero_png_sha256',$hash);
}
$mediaUrl=wp_get_attachment_url($mediaId);
if (!$mediaUrl) WP_CLI::error('Hero Media Library URL unavailable');

$stage['background_image']=['id'=>$mediaId,'url'=>$mediaUrl,'size'=>'full'];
$newData=wp_json_encode($data);
if (!$newData) WP_CLI::error('Could not encode Elementor JSON after image import');
update_post_meta(21,'_elementor_data',wp_slash($newData));
if (get_post_meta(21,'_elementor_data',true)!==$newData) {
    WP_CLI::error('Homepage write failed; restore from '.$backup);
}
clean_post_cache(21);
WP_CLI::success('Hero image + copy + single credential/counter strip applied. Media ID '.$mediaId.'. Backup '.$backup);
