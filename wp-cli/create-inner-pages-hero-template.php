<?php
/**
 * VIA Frontiers — Inner Pages Hero Template (Elementor Free Container).
 *
 * Usage:
 *   wp eval-file create-inner-pages-hero-template.php dry-run
 *   wp eval-file create-inner-pages-hero-template.php apply
 *
 * Creates ONE new Saved Container Template; does not insert it onto pages.
 * The approved homepage hero photo is a replaceable DEFAULT image only.
 * Breadcrumb/title/description/support text are deliberately editable
 * placeholders, since ordinary Elementor Free Saved Templates are copied
 * into pages and do not supply dynamic per-page fields automatically.
 *
 * Global fonts and colours: Elementor Site Kit 19. NO custom CSS.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode=$args[0]??'dry-run';
if (!in_array($mode,['dry-run','apply'],true)) WP_CLI::error('Use dry-run or apply');
if (site_url()!=='https://axonserver.com' || home_url()!=='https://axonserver.com' ||
    (int)get_option('page_on_front')!==21 || (int)get_option('elementor_active_kit')!==19)
    WP_CLI::error('Wrong development site, homepage, or Elementor Kit 19.');
if (!post_type_exists('elementor_library') || !taxonomy_exists('elementor_library_type'))
    WP_CLI::error('Elementor Saved Templates not registered. No changes.');

$title='Inner Pages Hero Template';
$all=get_posts(['post_type'=>'elementor_library','post_status'=>'any','posts_per_page'=>-1,'suppress_filters'=>true]);
$reference=null;
foreach($all as $post){
    if ($post->post_title===$title)
        WP_CLI::error('Template already exists (ID '.$post->ID.'); refusing to overwrite.');
    if ($post->post_title==='Sylvia counter Flexbox') {
        if($reference!==null) WP_CLI::error('Duplicate Sylvia reference template titles.');
        $reference=$post;
    }
}
if(!$reference || $reference->post_status!=='publish' ||
   get_post_meta($reference->ID,'_elementor_template_type',true)!=='container')
    WP_CLI::error('Published Sylvia counter Flexbox Container reference is missing.');
$refData=json_decode((string)get_post_meta($reference->ID,'_elementor_data',true),true);
if(!is_array($refData) || count($refData)!==1 ||
   ($refData[0]['elType']??'')!=='container')
    WP_CLI::error('Sylvia template root data unavailable.');
$refSettings=$refData[0]['settings']??[];
if(!isset($refSettings['padding']) || !is_array($refSettings['padding']))
    WP_CLI::error('Sylvia template has no native outer padding to match.');
$refTerms=wp_get_object_terms($reference->ID,'elementor_library_type',['fields'=>'ids']);
if(is_wp_error($refTerms) || !$refTerms) WP_CLI::error('Reference Container taxonomy missing.');

// The background comes from the existing approved, licensed homepage hero.
// Editors MUST replace the photo when a different inner page requires one.
$homeData=json_decode((string)get_post_meta(21,'_elementor_data',true),true);
if(!is_array($homeData))WP_CLI::error('Homepage data unavailable.');
$stage=null;
foreach($homeData as $section){
    if(($section['id']??'')==='0000015' &&
       ($section['settings']['_title']??'')==='02 Hero'){
        $nodes=$section['elements']??[];
        if(($nodes[0]['id']??'')==='0000014')$stage=$nodes[0];
    }
}
$photo=$stage['settings']['background_image']??null;
if(!is_array($photo) || empty($photo['url']) ||
    !str_starts_with($photo['url'],'https://axonserver.com/wp-content/uploads/'))
    WP_CLI::error('Approved homepage background image not verified.');

$kit=get_post_meta(19,'_elementor_page_settings',true);
if(!is_array($kit))WP_CLI::error('Site Kit 19 design data unavailable.');
$colors=[];$fonts=[];
foreach(array_merge($kit['system_colors']??[],$kit['custom_colors']??[]) as $v)
  if(!empty($v['_id']))$colors[$v['_id']]=true;
foreach(array_merge($kit['system_typography']??[],$kit['custom_typography']??[]) as $v)
  if(!empty($v['_id']))$fonts[$v['_id']]=true;
foreach(['primary','accent','via_white'] as $v)if(empty($colors[$v]))
    WP_CLI::error('Missing VIA Global Colour '.$v);
foreach(['primary','text','via_small_labels','via_buttons'] as $v)
    if(empty($fonts[$v]))WP_CLI::error('Missing VIA Global Font preset '.$v);

$length=static fn($v,$u='px')=>['unit'=>$u,'size'=>$v,'sizes'=>[]];
$space=static fn($top,$right,$bottom,$left)=>[
 'unit'=>'px','top'=>(string)$top,'right'=>(string)$right,
 'bottom'=>(string)$bottom,'left'=>(string)$left,'isLinked'=>false];
$gap=static fn($v)=>['unit'=>'px','size'=>$v,'column'=>(string)$v,'row'=>(string)$v,'isLinked'=>true];
$ids=[];
$element=static function(string $kind,string $name,array $settings,array $children=[])use(&$ids):array{
    $seed='VIA Inner Pages Hero v1/'.$name;
    $id=substr(hash('sha256',$seed),0,7);
    if(isset($ids[$id])) WP_CLI::error('Internal template ID collision.');
    $ids[$id]=true;
    $settings['_title']=$name;
    $result=['id'=>$id,'elType'=>$kind,'settings'=>$settings,'elements'=>$children];
    if($kind==='widget')$result['widgetType']=$name==='Hero CTA button'?'button':
        ($name==='Page title — edit on each page'?'heading':'text-editor');
    if($kind==='container')$result['isInner']=true;
    return $result;
};
$globalColour=static function(array &$settings,string $control,string $key):void{
    if(!isset($settings['__globals__']))$settings['__globals__']=[];
    $settings['__globals__'][$control]='globals/colors?id='.$key;
};
$globalFont=static function(array &$settings,string $key,int $size):void{
    if(!isset($settings['__globals__']))$settings['__globals__']=[];
    $settings['typography_typography']='custom';
    $settings['__globals__']['typography_typography']='globals/typography?id='.$key;
    $settings['typography_font_size']=$size===0?null:['unit'=>'px','size'=>$size,'sizes'=>[]];
    if($size===0)unset($settings['typography_font_size']);
    unset($settings['typography_font_family']);
};

// Breadcrumb is EDITABLE, not an automatically generated breadcrumb.
$breadcrumbSettings=[
 'editor'=>'<p><a href="/">Home</a> / Page Title</p>'
];
$globalFont($breadcrumbSettings,'via_small_labels',12);
$globalColour($breadcrumbSettings,'text_color','accent');
$breadcrumb=$element('widget','Breadcrumb — edit on each page',$breadcrumbSettings);

$headingSettings=['title'=>'Page Title','header_size'=>'h1','align'=>'left'];
$globalFont($headingSettings,'primary',36);
$globalColour($headingSettings,'title_color','primary');
$heading=$element('widget','Page title — edit on each page',$headingSettings);

$introSettings=['editor'=>'<p>Add this page’s short introductory description here.</p>'];
$globalFont($introSettings,'text',16);
$globalColour($introSettings,'text_color','primary');
$intro=$element('widget','Introduction — edit on each page',$introSettings);

$buttonSettings=[
 'text'=>'Start a Conversation →',
 'link'=>['url'=>'/contact','is_external'=>'','nofollow'=>''],
 'size'=>'md','align'=>'left',
 'typography_typography'=>'custom',
 '__globals__'=>[
    'typography_typography'=>'globals/typography?id=via_buttons',
    'background_color'=>'globals/colors?id=accent',
    'button_text_color'=>'globals/colors?id=via_white'
 ]
];
$cta=$element('widget','Hero CTA button',$buttonSettings);

$supportSettings=['editor'=>'<p>Optional supporting description — edit or remove this text.</p>'];
$globalFont($supportSettings,'text',14);
$globalColour($supportSettings,'text_color','primary');
$support=$element('widget','Supporting description — optional',$supportSettings);

$panel=[
 'content_width'=>'full',
 'width'=>$length(55,'%'),'width_tablet'=>$length(70,'%'),
 'width_mobile'=>$length(100,'%'),
 'flex_direction'=>'column','flex_direction_tablet'=>'column','flex_direction_mobile'=>'column',
 'flex_align_items'=>'flex-start','flex_justify_content'=>'center',
 'flex_gap'=>$gap(13),
 'padding'=>$space(25,28,27,28),
 'padding_tablet'=>$space(22,22,23,22),
 'padding_mobile'=>$space(18,17,20,17),
 'background_background'=>'classic',
 'border_radius'=>$space(14,14,14,14),
];
$globalColour($panel,'background_color','via_white');
$panelNode=$element('container','Editable inner-page hero content',$panel,
 [$breadcrumb,$heading,$intro,$cta,$support]);

$root=[
 'content_width'=>'full','width'=>$length(100,'%'),
 'flex_direction'=>'row','flex_direction_tablet'=>'row','flex_direction_mobile'=>'column',
 'flex_align_items'=>'center','flex_justify_content'=>'flex-start',
 'flex_wrap'=>'nowrap',
 'flex_gap'=>$gap(0),
 'min_height'=>$length(35,'vh'),          // half the approved 70vh desktop homepage hero.
 'min_height_tablet'=>$length(360,'px'),  // allows intrinsic content height to grow.
 'min_height_mobile'=>$length(420,'px'),
 'background_background'=>'classic',
 'background_image'=>$photo,
 'background_size'=>'cover','background_position'=>'center center',
 'background_position_tablet'=>'center center',
 'background_position_mobile'=>'center center',
 'background_repeat'=>'no-repeat'
];
foreach(['padding','padding_tablet','padding_mobile'] as $property)
    if(isset($refSettings[$property]))$root[$property]=$refSettings[$property];
$globalColour($root,'background_color','via_white');

$tree=$element('container','Inner Pages Hero — full width (35vh)', $root,[$panelNode]);
$data=[$tree];
$json=wp_json_encode($data,JSON_UNESCAPED_SLASHES);
if(!$json || !is_array(json_decode($json,true)))WP_CLI::error('Template JSON encoding failed.');

WP_CLI::log('Will create: '.$title.' — Elementor Free Container');
WP_CLI::log('Source: approved Homepage 21 background, changeable per inserted page.');
WP_CLI::log('Desktop: full-width photo; minimum 35vh (half the 70vh homepage).');
WP_CLI::log('Tablet: minimum 360px; mobile: minimum 420px; content never forcibly clipped.');
WP_CLI::log('Outer padding: matched from Sylvia counter Flexbox (ID '.$reference->ID.').');
WP_CLI::log('Editable placeholders: breadcrumb, page title, description, CTA, supporting description.');
WP_CLI::log('Only the NEW saved template is created; no existing page/template modified.');
if($mode==='dry-run'){WP_CLI::success('DRY RUN PASSED — zero writes');return;}

$dir='/home/amitaxon/via-fullsite-deployment/backups';
if(!is_dir($dir)||!is_writable($dir)) WP_CLI::error('Private backup directory unavailable.');
$log=$dir.'/inner-hero-created-'.gmdate('Ymd-His').'-'.wp_generate_password(7,false,false).'.json';

$id=wp_insert_post([
 'post_type'=>'elementor_library','post_status'=>'publish',
 'post_title'=>$title,'post_content'=>''
],true);
if(is_wp_error($id)||!$id)WP_CLI::error('Unable to create template.');
try{
    update_post_meta($id,'_elementor_template_type','container');
    update_post_meta($id,'_elementor_edit_mode','builder');
    update_post_meta($id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'4.3.4');
    update_post_meta($id,'_elementor_page_settings',[]);
    update_post_meta($id,'_elementor_data',wp_slash($json));
    if(get_post_meta($id,'_elementor_data',true)!==$json)
        throw new RuntimeException('Saved Elementor JSON did not verify.');
    $terms=wp_set_object_terms($id,array_map('intval',$refTerms),'elementor_library_type',false);
    if(is_wp_error($terms) || !$terms)
        throw new RuntimeException('Elementor Container taxonomy registration failed.');
    clean_post_cache($id);
    $manifest=[
      'created_at'=>gmdate('c'),'template_title'=>$title,'template_id'=>(int)$id,
      'source_homepage'=>21,'reference_template_id'=>(int)$reference->ID,
      'source_image_id'=>$photo['id']??null,'template_sha256'=>hash('sha256',$json),
      'note'=>'Private record; saved template only, not inserted onto live pages.'
    ];
    if(file_put_contents($log,wp_json_encode($manifest,JSON_PRETTY_PRINT),LOCK_EX)===false)
        throw new RuntimeException('Unable to create private template manifest.');
    chmod($log,0600);
}catch(Throwable $e){
    wp_delete_post($id,true);
    WP_CLI::error('Creation cancelled and the newly created template removed: '.$e->getMessage());
}
WP_CLI::success('Created Saved Container Template ID '.$id.': '.$title.'. Private manifest: '.$log);
