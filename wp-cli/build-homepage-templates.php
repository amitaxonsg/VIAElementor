<?php
/**
 * VIA Frontiers — create FOUR independent homepage Container Library templates.
 *
 * wp eval-file build-homepage-templates.php dry-run
 * wp eval-file build-homepage-templates.php apply
 *
 * WordPress/Elementor page 21 is READ ONLY. Uses current website copy, media,
 * links and global style bindings. Saves new items to elementor_library only.
 * NEVER overwrites an existing template or modifies Header 57 / Footer 58.
 * Design is native Elementor Flexbox — no CSS files and no font-family literals.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode=$args[0]??'dry-run';
if (!in_array($mode,['dry-run','apply'],true)) WP_CLI::error('Use dry-run or apply');
if (site_url()!=='https://axonserver.com' || home_url()!=='https://axonserver.com' ||
    (int)get_option('page_on_front')!==21 || (int)get_option('elementor_active_kit')!==19)
    WP_CLI::error('Wrong VIA development environment. No changes.');
$page=get_post(21);
if (!$page || $page->post_type!=='page' || $page->post_name!=='via-frontiers-homepage-draft' || $page->post_status!=='publish')
    WP_CLI::error('Unexpected homepage. No changes.');
if (!post_type_exists('elementor_library') || !taxonomy_exists('elementor_library_type'))
    WP_CLI::error('Elementor Library is unavailable. No changes.');

$ref=get_page_by_title('Sylvia counter Flexbox',OBJECT,'elementor_library');
if (!$ref || get_post_meta($ref->ID,'_elementor_template_type',true)!=='container')
    WP_CLI::error('Existing Sylvia counter Flexbox reference template missing or not of type container.');
$refTerms=wp_get_object_terms($ref->ID,'elementor_library_type',['fields'=>'ids']);
if (is_wp_error($refTerms) || !$refTerms) WP_CLI::error('Cannot determine Elementor Container taxonomy from reference.');

$raw=(string)get_post_meta(21,'_elementor_data',true);
$pageData=json_decode($raw,true);
if (!is_array($pageData) || !$pageData) WP_CLI::error('Invalid homepage Elementor JSON');
$kit=get_post_meta(19,'_elementor_page_settings',true);
if (!is_array($kit))WP_CLI::error('Elementor Global Kit 19 not found.');
$globals=['colors'=>[],'typography'=>[]];
foreach (array_merge($kit['system_colors']??[],$kit['custom_colors']??[]) as $v)
    if (!empty($v['_id']))$globals['colors'][$v['_id']]=true;
foreach (array_merge($kit['system_typography']??[],$kit['custom_typography']??[]) as $v)
    if (!empty($v['_id']))$globals['typography'][$v['_id']]=true;
foreach (['primary','accent','via_white','via_cream','via_stone'] as $id)
    if (empty($globals['colors'][$id]))WP_CLI::error('Global colour missing: '.$id);
foreach (['primary','text'] as $id)
    if (empty($globals['typography'][$id]))WP_CLI::error('Global font missing: '.$id);

$unit=static fn($n,$u='px')=>['unit'=>$u,'size'=>$n,'sizes'=>[]];
$edge=static fn($t,$r,$b,$l)=>['unit'=>'px','top'=>(string)$t,'right'=>(string)$r,'bottom'=>(string)$b,'left'=>(string)$l,'isLinked'=>false];
$gap=static fn($n)=>['unit'=>'px','size'=>$n,'column'=>(string)$n,'row'=>(string)$n,'isLinked'=>true];
$global=static function(array &$s,string $prop,string $group,string $id)use($globals):void{
    if (empty($globals[$group][$id]))WP_CLI::error('Missing global '.$group.': '.$id);
    if (!isset($s['__globals__']) || !is_array($s['__globals__']))$s['__globals__']=[];
    unset($s[$prop]);
    $s['__globals__'][$prop]='globals/'.$group.'?id='.$id;
};
$paint=static function(array &$s,string $key,string $color)use($global):void{
    $global($s,$key,'colors',$color);
};
$font=static function(array &$s,string $preset,int $size,string $prefix='typography')use($global,$unit):void{
    unset($s[$prefix.'_font_family']);
    $global($s,$prefix.'_typography','typography',$preset);
    $s[$prefix.'_typography']='custom';
    $s[$prefix.'_font_size']=$unit($size);
    $s[$prefix.'_font_size_mobile']=$unit($size);
};
$layout=static function(array &$s,string $direction='column')use($unit):void{
    $s['content_width']='full'; unset($s['boxed_width']);
    $s['flex_direction']=$direction;
    $s['flex_direction_tablet']=$direction;
    $s['flex_direction_mobile']='column';
    $s['width']=$unit(100,'%');
};
$setWidth=static function(array &$s,float $desktop,float $tablet=100,float $mobile=100)use($unit):void{
    $s['width']=$unit($desktop,'%');
    $s['width_tablet']=$unit($tablet,'%');
    $s['width_mobile']=$unit($mobile,'%');
};
$newContainer=static function(string $name,array $children, array $settings=[])use($layout):array{
    $s=['_title'=>$name];$layout($s);
    return ['id'=>'','elType'=>'container','isInner'=>true,'settings'=>array_merge($s,$settings),'elements'=>$children];
};
$expectChildren=static function(array $n,array $ids,string $label):void{
    $actual=array_column($n['elements']??[],'id');
    if ($actual!==$ids)WP_CLI::error($label.' changed; found '.implode(',',$actual));
};
$top=static function(string $id,string $label)use($pageData):array{
    $found=[];
    foreach($pageData as $node)
      if (($node['id']??'')===$id && ($node['settings']['_title']??'')===$label) $found[]=$node;
    if(count($found)!==1 || ($found[0]['elType']??'')!=='container')
      WP_CLI::error('Homepage source section not uniquely available: '.$label);
    return $found[0];
};
$idsIn=static function(array $nodes)use(&$idsIn):array{
    $result=[];
    foreach($nodes as $node){
      if (!empty($node['id']))$result[]=$node['id'];
      if (!empty($node['elements']))$result=array_merge($result,$idsIn($node['elements']));
    }
    return $result;
};
$unique=static function(array &$nodes,string $key)use(&$unique):void{
    static $counter=0;
    foreach($nodes as &$n){
        $counter++;
        $n['id']=substr(hash('sha256','via-reusable-v1/'.$key.'/'.$counter),0,7);
        if (!empty($n['elements']))$unique($n['elements'],$key);
    }
    unset($n);
};
$wrap=static function(array $child,string $title)use($newContainer):array {
    return $newContainer($title,[$child]);
};
$sectionBase=static function(array &$section,string $title,string $background='via_cream')use($layout,$paint,$gap,$edge):void{
    $s=&$section['settings'];
    $s['_title']=$title;
    $layout($s);
    $s['flex_gap']=$gap(24);
    $s['padding']=$edge(60,40,64,40);
    $s['padding_tablet']=$edge(44,24,48,24);
    $s['padding_mobile']=$edge(34,18,38,18);
    $s['background_background']='classic';
    $paint($s,'background_color',$background);
    // Prevent source page CSS classes from becoming generic template dependencies.
    unset($s['_css_classes']);
};

// 1 — Pathways: eyebrow/headline/description + original 3 cards.
$path=$top('000002c','03 Pathways');
$pChildren=$path['elements'];
$pIds=array_column($pChildren,'id');
if ($pIds===['0000016','b7621a0','0000018','000002b']) {
    [$eyebrow,$headingRow,$description,$cards]=$pChildren;
    $expectChildren($headingRow,['0000017','a71c9f2'],'Pathways headline row');
} elseif ($pIds===['0000016','0000017','a71c9f2','0000018','000002b']) {
    [$eyebrow,$h1,$h2,$description,$cards]=$pChildren;
    $headingRow=$newContainer('Two-colour headline',[$h1,$h2]);
} else WP_CLI::error('Pathways source structure changed. No templates created.');
$expectChildren($cards,['000001e','0000024','000002a'],'Pathways cards');
$font($eyebrow['settings'],'text',12);
foreach($headingRow['elements'] as &$h){
    if (($h['widgetType']??'')!=='heading') WP_CLI::error('Pathways heading widget changed');
    $font($h['settings'],'primary',36);
}
unset($h);
$layout($headingRow['settings'],'row');
$headingRow['settings']['flex_wrap']='wrap';
$headingRow['settings']['flex_align_items']='baseline';
$headingRow['settings']['flex_gap']=$gap(12);
$headingRow['settings']['flex_direction_mobile']='row';
$left=$newContainer('Pathways heading',[$eyebrow,$headingRow]);
$left['settings']['flex_gap']=$gap(10);$setWidth($left['settings'],62,100,100);
$right=$wrap($description,'Pathways description');$setWidth($right['settings'],35,100,100);
$intro=$newContainer('Pathways introduction',[$left,$right]);
$layout($intro['settings'],'row');
$intro['settings']['flex_wrap']='wrap';
$intro['settings']['flex_align_items']='center';
$intro['settings']['flex_justify_content']='space-between';
$intro['settings']['flex_gap']=$gap(18);
$cards['settings']['_title']='Pathways three-card row';
$layout($cards['settings'],'row');
$cards['settings']['flex_wrap']='wrap';
$cards['settings']['flex_gap']=$gap(20);
$cards['settings']['flex_align_items']='stretch';
foreach($cards['elements'] as &$card){
    if (($card['elType']??'')!=='container' || count($card['elements']??[])!==5)
       WP_CLI::error('Unexpected Pathways card widgets');
    [$title,$lead,$body,$image,$button]=$card['elements'];
    if (($title['widgetType']??'')!=='heading' || ($image['widgetType']??'')!=='image' || ($button['widgetType']??'')!=='button')
       WP_CLI::error('Pathways card widget types changed');
    $font($title['settings'],'primary',25);
    $bodyWrap=$wrap($body,'Card description');$setWidth($bodyWrap['settings'],68,100,100);
    $media=$wrap($image,'Card existing media');$setWidth($media['settings'],30,100,100);
    $media['settings']['flex_align_items']='center';
    $media['settings']['flex_justify_content']='center';
    // Keep image source, proportions and user-supplied brand artwork untouched.
    $textMedia=$newContainer('Card body and image',[$bodyWrap,$media]);
    $layout($textMedia['settings'],'row');$textMedia['settings']['flex_wrap']='wrap';
    $textMedia['settings']['flex_gap']=$gap(10);$textMedia['settings']['flex_align_items']='center';
    $card['elements']=[$title,$lead,$textMedia,$button];
    $layout($card['settings']);
    $setWidth($card['settings'],31.5,48,100);
    $card['settings']['flex_gap']=$gap(14);
    $card['settings']['padding']=$edge(24,24,24,24);
    $card['settings']['padding_mobile']=$edge(20,18,22,18);
    $card['settings']['background_background']='classic';
    $paint($card['settings'],'background_color','via_white');
    $card['settings']['border_border']='solid';
    $card['settings']['border_width']=$edge(3,0,0,0);
    $paint($card['settings'],'border_color','accent');
    $card['settings']['border_radius']=$edge(14,14,14,14);
    $button['settings']['_margin']=$edge(12,0,0,0);
    $card['elements'][3]=$button;
}
unset($card);
$path['elements']=[$intro,$cards];
$sectionBase($path,'homepage-03-Pathways');

// 2 — FIRE Approach: clone the *CURRENT* proven native four-row Elementor design.
// The original 000003f section was replaced on the server by b8800dd.
// Its two title widgets and four nested rows must be preserved in place.
$fire=$top('b8800dd','VIA FIRE Native Rows');
$expectChildren($fire,['b5bb7c9','eb885d3'],'Current native FIRE section');
[$fireHeading,$steps]=$fire['elements'];
if (($fireHeading['elType']??'')!=='container' || ($steps['elType']??'')!=='container')
    WP_CLI::error('Native FIRE headline or rows container changed.');
$expectChildren($fireHeading,['f5391fc','931821d'],'Current two-colour FIRE headline');
$expectChildren($steps,['9c53df3','937ef3f','de631fa','cb93c4a'],'Current four FIRE rows');
foreach($fireHeading['elements'] as $i=>&$h){
    if (($h['widgetType']??'')!=='heading')
        WP_CLI::error('Native FIRE title must contain two heading widgets.');
    // Preserve the user's title WORDING, but keep heading font/size/global colours editable.
    $font($h['settings'],'primary',36);
    $paint($h['settings'],'title_color',$i===0?'primary':'accent');
}
unset($h);
$layout($fireHeading['settings'],'row');
$fireHeading['settings']['flex_wrap']='wrap';
$fireHeading['settings']['flex_direction_mobile']='row';
$fireHeading['settings']['flex_align_items']='baseline';
$fireHeading['settings']['flex_gap']=$gap(12);
$fireHeading['settings']['_title']='FIRE heading — two global colours';
$layout($steps['settings'],'column');
$steps['settings']['flex_gap']=$gap(12);
$steps['settings']['padding']=$edge(18,18,18,18);
$steps['settings']['padding_mobile']=$edge(10,10,10,10);
$steps['settings']['_title']='Four existing FIRE process rows';
// Each step ALREADY has its own approved letter circle and text layout.
// Reuse its exact child hierarchy, strings, colours, and widget settings;
// do not flatten the redesigned cards or re-create their contents.
foreach($steps['elements'] as &$card){
    if (($card['elType']??'')!=='container' || count($card['elements']??[])<1)
        WP_CLI::error('Existing FIRE step container is incomplete.');
    $layout($card['settings'],'row');
    $card['settings']['flex_direction_tablet']='row';
    $card['settings']['flex_direction_mobile']='row';
    $card['settings']['flex_wrap']='wrap';
    $card['settings']['flex_align_items']='center';
    $card['settings']['flex_gap']=$gap(14);
    $card['settings']['padding']=$edge(15,20,15,20);
    $card['settings']['padding_mobile']=$edge(14,14,14,14);
}
unset($card);
$fire['elements']=[$fireHeading,$steps];
$sectionBase($fire,'homepage-04-FIRE-Approach','via_white');

// 3 — Client Voices: preserve all real quotations, authors and attributions.
$voices=$top('000004c','05 Testimonials');
$expectChildren($voices,['0000040','0000041','000004b'],'Client Voices');
[$voiceLabel,$voiceHeading,$voiceGrid]=$voices['elements'];
if (count($voiceGrid['elements']??[])!==3)WP_CLI::error('Expected exactly three testimonial cards');
$font($voiceLabel['settings'],'text',12);
$font($voiceHeading['settings'],'primary',36);
$intro=$newContainer('Client Voices heading',[$voiceLabel,$voiceHeading]);
$intro['settings']['flex_gap']=$gap(10);$setWidth($intro['settings'],24,100,100);
$layout($voiceGrid['settings'],'row');
$voiceGrid['settings']['flex_wrap']='wrap';
$voiceGrid['settings']['flex_gap']=$gap(16);
$setWidth($voiceGrid['settings'],74,100,100);
foreach($voiceGrid['elements'] as &$card){
    if (($card['elType']??'')!=='container' || empty($card['elements'])) WP_CLI::error('Testimonial card unexpectedly empty');
    $layout($card['settings']);
    $setWidth($card['settings'],31.5,31.5,100);
    $card['settings']['flex_justify_content']='space-between';
    $card['settings']['flex_gap']=$gap(16);
    $card['settings']['padding']=$edge(26,22,26,22);
    $card['settings']['padding_mobile']=$edge(22,18,24,18);
    $card['settings']['background_background']='classic';
    $paint($card['settings'],'background_color','via_white');
    $card['settings']['border_border']='solid';
    $card['settings']['border_width']=$edge(3,0,0,0);
    $paint($card['settings'],'border_color','accent');
    $card['settings']['border_radius']=$edge(12,12,12,12);
}
unset($card);
$voices['elements']=[$intro,$voiceGrid];
$sectionBase($voices,'homepage-05-Client-Voices');
$layout($voices['settings'],'row');
$voices['settings']['flex_wrap']='wrap';
$voices['settings']['flex_align_items']='flex-start';
$voices['settings']['flex_gap']=$gap(22);

// 4 — Closing CTA: locate by actual section heading, not guessed element IDs.
$matches=[];
$hasPhrase=static function(array $node)use(&$hasPhrase):bool{
    if (($node['widgetType']??'')==='heading'){
        $title=html_entity_decode(wp_strip_all_tags((string)($node['settings']['title']??'')),ENT_QUOTES,'UTF-8');
        if (stripos($title,'Where would you like to start?')!==false)return true;
    }
    foreach($node['elements']??[] as $child)if($hasPhrase($child))return true;
    return false;
};
foreach($pageData as $section)
    if (($section['elType']??'')==='container' && $hasPhrase($section))$matches[]=$section;
if(count($matches)!==1)WP_CLI::error('Cannot uniquely identify closing Start a Conversation section');
$cta=$matches[0];
$widgets=[];
$flatten=static function(array $node)use(&$flatten,&$widgets):void{
    if (($node['elType']??'')==='widget')$widgets[]=$node;
    foreach($node['elements']??[] as $child)$flatten($child);
};
$flatten($cta);
$types=array_count_values(array_map(static fn($node)=>$node['widgetType']??'',$widgets));
if (($types['heading']??0)!==1 || ($types['text-editor']??0)!==2 || ($types['button']??0)!==1 || array_sum($types)!==4)
    WP_CLI::error('CTA content structure differs: '.wp_json_encode($types));
$label=null;$headline=null;$support=null;$button=null;
foreach($widgets as $widget){
  if($widget['widgetType']==='heading')$headline=$widget;
  elseif($widget['widgetType']==='button')$button=$widget;
  elseif($label===null)$label=$widget;
  else $support=$widget;
}
if (!$label || !$headline || !$support || !$button)WP_CLI::error('CTA widget extraction failed');
$font($label['settings'],'text',12);$font($headline['settings'],'primary',36);
$left=$newContainer('CTA label and heading',[$label,$headline]);
$left['settings']['flex_gap']=$gap(12);$setWidth($left['settings'],44,100,100);
$middle=$newContainer('CTA description',[$support]);
$setWidth($middle['settings'],32,100,100);
$right=$newContainer('CTA button',[$button]);
$setWidth($right['settings'],20,100,100);
$right['settings']['flex_align_items']='center';
$right['settings']['flex_justify_content']='center';
$cta['elements']=[$left,$middle,$right];
$sectionBase($cta,'homepage-06-Start-a-Conversation');
$layout($cta['settings'],'row');
$cta['settings']['flex_wrap']='wrap';
$cta['settings']['flex_align_items']='center';
$cta['settings']['flex_justify_content']='space-between';
$cta['settings']['flex_gap']=$gap(18);

// Build independent self-contained Library documents. Each source section
// is deep-copied; the live page's original IDs/settings/data remain unchanged.
$definitions=[
    'homepage-03-Pathways'=>$path,
    'homepage-04-FIRE-Approach'=>$fire,
    'homepage-05-Client-Voices'=>$voices,
    'homepage-06-Start-a-Conversation'=>$cta,
];
foreach($definitions as $name=>&$section){
    $arr=[$section];
    $unique($arr,$name);
    $section=$arr[0];
    $all=$idsIn([$section]);
    if(count($all)!==count(array_unique($all)))WP_CLI::error('Duplicate new template ID for '.$name);
    $encoded=wp_json_encode([$section]);
    if(!$encoded || !is_array(json_decode($encoded,true)))WP_CLI::error('Template JSON invalid for '.$name);
    // Nothing is written to WordPress during this phase.
}
unset($section);

// Never overwrite an existing saved template, including an existing version.
$existing=get_posts(['post_type'=>'elementor_library','post_status'=>'any',
                     'numberposts'=>-1,'fields'=>'ids']);
foreach($existing as $id){
    $title=get_the_title($id);
    if(array_key_exists($title,$definitions))WP_CLI::error('Template already exists: '.$title.' (ID '.$id.'). No changes.');
}
WP_CLI::log('Verified Elementor Site Kit 19 and current homepage sections.');
foreach($definitions as $title=>$section){
    $widgets=0;
    $walk=static function(array $nodes)use(&$walk,&$widgets):void{
        foreach($nodes as $node){
          if(($node['elType']??'')==='widget')$widgets++;
          $walk($node['elements']??[]);
        }
    };
    $walk([$section]);
    WP_CLI::log('CREATE: '.$title.' — '.$widgets.' existing text/image/button widgets, native responsive containers');
}
WP_CLI::log('Source homepage 21, Header 57, Footer 58, original content and Site Kit unchanged.');
if($mode==='dry-run'){WP_CLI::success('DRY RUN PASSED — zero writes');return;}

$backupDir='/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($backupDir) || !is_writable($backupDir))WP_CLI::error('Private backup folder unavailable');
$manifestPath=$backupDir.'/homepage-template-library-'.gmdate('Ymd-His').'-'.wp_generate_password(7,false,false).'.json';
$manifest=['date'=>gmdate('c'),'source_homepage'=>21,'source_sha256'=>hash('sha256',$raw),
    'template_titles'=>array_keys($definitions),'source_copy_sensitive'=>true,
    'note'=>'Private manifest only. Homepage unchanged; Elementor templates created separately.'];
if(file_put_contents($manifestPath,wp_json_encode($manifest,JSON_PRETTY_PRINT),LOCK_EX)===false)
    WP_CLI::error('Could not write private manifest');
chmod($manifestPath,0600);
$created=[];
try{
    foreach($definitions as $title=>$section){
        $postId=wp_insert_post(['post_type'=>'elementor_library','post_status'=>'publish',
                'post_title'=>$title,'post_content'=>''],true);
        if(is_wp_error($postId) || !$postId)throw new RuntimeException('Cannot create '.$title.': '.(is_wp_error($postId)?$postId->get_error_message():'unknown error'));
        $created[]=(int)$postId;
        update_post_meta($postId,'_elementor_template_type','container');
        update_post_meta($postId,'_elementor_edit_mode','builder');
        update_post_meta($postId,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'4.3.4');
        update_post_meta($postId,'_elementor_page_settings',[]);
        $json=wp_json_encode([$section]);
        update_post_meta($postId,'_elementor_data',wp_slash($json));
        if(get_post_meta($postId,'_elementor_data',true)!==$json)throw new RuntimeException('Saved JSON mismatch for '.$title);
        $set=wp_set_object_terms($postId,array_map('intval',$refTerms),'elementor_library_type',false);
        if(is_wp_error($set) || !$set)throw new RuntimeException('Cannot set Elementor container taxonomy for '.$title);
        clean_post_cache($postId);
        WP_CLI::log('Created library template '.$postId.': '.$title);
    }
}catch(Throwable $e){
    // Only discard posts created by this single run, never other library items.
    foreach($created as $postId)wp_delete_post($postId,true);
    WP_CLI::error('Creation aborted; newly created template posts rolled back: '.$e->getMessage());
}
WP_CLI::success('Created '.count($created).' NEW Elementor Container templates. Private manifest: '.$manifestPath);
