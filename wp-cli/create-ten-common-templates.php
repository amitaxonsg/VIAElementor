<?php
/**
 * VIA Frontiers — Ten reusable Elementor Free Container templates.
 *
 * wp eval-file create-ten-common-templates.php dry-run
 * wp eval-file create-ten-common-templates.php apply
 *
 * Creates ONLY new elementor_library posts. Never alters published pages,
 * Header 57, Footer 58, homepage 21, Site Kit 19 or existing templates.
 * Sample text/metrics are CLEARLY marked editable placeholders.
 * All font/colour styles use Global Elementor Kit 19 bindings.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode=$args[0]??'dry-run';
if(!in_array($mode,['dry-run','apply'],true))WP_CLI::error('Use dry-run or apply');
if(site_url()!=='https://axonserver.com'||home_url()!=='https://axonserver.com'||
   (int)get_option('elementor_active_kit')!==19||(int)get_option('page_on_front')!==21)
   WP_CLI::error('Wrong VIA development site or Elementor Kit; no changes.');
if(!post_type_exists('elementor_library') || !taxonomy_exists('elementor_library_type'))
   WP_CLI::error('Elementor Saved Templates unavailable.');
$ref=get_post(178);
if(!$ref||$ref->post_type!=='elementor_library'||$ref->post_title!=='Sylvia counter Flexbox'||
   $ref->post_status!=='publish'||get_post_meta(178,'_elementor_template_type',true)!=='container')
   WP_CLI::error('Approved Sylvia reference (178) changed; abort.');
$refTree=json_decode((string)get_post_meta(178,'_elementor_data',true),true);
if(!is_array($refTree)||count($refTree)!==1||($refTree[0]['elType']??'')!=='container')
   WP_CLI::error('Invalid Sylvia reference layout.');
$refSettings=$refTree[0]['settings']??[];
foreach(['content_width','width','padding'] as $key)
    if(!isset($refSettings[$key]))WP_CLI::error('Sylvia missing outer spacing control: '.$key);
$types=wp_get_object_terms(178,'elementor_library_type',['fields'=>'ids']);
if(is_wp_error($types)||!$types)WP_CLI::error('Elementor Saved Container taxonomy unavailable.');
$kit=get_post_meta(19,'_elementor_page_settings',true);
if(!is_array($kit))WP_CLI::error('Global Kit settings unavailable.');
$colorIds=[];$fontIds=[];
foreach(array_merge($kit['system_colors']??[],$kit['custom_colors']??[]) as $v)
    if(!empty($v['_id']))$colorIds[$v['_id']]=true;
foreach(array_merge($kit['system_typography']??[],$kit['custom_typography']??[]) as $v)
    if(!empty($v['_id']))$fontIds[$v['_id']]=true;
foreach(['primary','accent','via_blue','via_stone','via_white','via_cream'] as $id)
    if(empty($colorIds[$id]))WP_CLI::error('Missing Global Color '.$id);
foreach(['primary','text','via_small_labels','via_buttons'] as $id)
    if(empty($fontIds[$id]))WP_CLI::error('Missing Global Font '.$id);

$widgetManager=\Elementor\Plugin::instance()->widgets_manager;
foreach(['heading','text-editor','button','accordion','html','image'] as $name)
    if(!$widgetManager->get_widget_types($name))
        WP_CLI::error('Required Elementor Free widget unavailable: '.$name);

$px=static fn($v,$unit='px')=>['unit'=>$unit,'size'=>$v,'sizes'=>[]];
$edge=static fn($a,$b,$c,$d)=>['unit'=>'px','top'=>(string)$a,'right'=>(string)$b,'bottom'=>(string)$c,'left'=>(string)$d,'isLinked'=>false];
$gap=static fn($v)=>['unit'=>'px','size'=>$v,'column'=>(string)$v,'row'=>(string)$v,'isLinked'=>true];
$slug='';$counter=0;
$make=static function(string $type,string $label,array $settings=[],array $children=[],string $widgetType='')use(&$slug,&$counter):array{
    $counter++;
    $id=substr(hash('sha256',"via-library-v1/{$slug}/{$counter}/{$label}"),0,7);
    $settings['_title']=$label;
    $node=['id'=>$id,'elType'=>$type,'settings'=>$settings,'elements'=>$children];
    if($type==='container')$node['isInner']=true;
    if($type==='widget')$node['widgetType']=$widgetType;
    return $node;
};
$col=static function(string $name,array $children,int $desktop=100,int $tablet=100,int $mobile=100)use($make,$px,$gap):array{
    return $make('container',$name,[
      'content_width'=>'full','width'=>$px($desktop,'%'),
      'width_tablet'=>$px($tablet,'%'),'width_mobile'=>$px($mobile,'%'),
      'flex_direction'=>'column','flex_direction_tablet'=>'column','flex_direction_mobile'=>'column',
      'flex_gap'=>$gap(12),'flex_justify_content'=>'flex-start','flex_align_items'=>'stretch'
    ],$children);
};
$row=static function(string $name,array $children,int $space=20)use($make,$px,$gap):array{
    return $make('container',$name,[
      'content_width'=>'full','width'=>$px(100,'%'),
      'flex_direction'=>'row','flex_direction_tablet'=>'row','flex_direction_mobile'=>'column',
      'flex_wrap'=>'wrap','flex_gap'=>$gap($space),'flex_align_items'=>'stretch',
      'flex_justify_content'=>'space-between'
    ],$children);
};
$addGlobal=static function(array &$settings,string $field,string $type,string $token):void{
    if(!isset($settings['__globals__']))$settings['__globals__']=[];
    $settings['__globals__'][$field]='globals/'.$type.'?id='.$token;
};
$head=static function(string $title,int $size=36,string $tone='primary',string $preset='primary')use($make,$px,$addGlobal):array{
    $s=['title'=>$title,'header_size'=>$size>28?'h2':'h3',
        'typography_typography'=>'custom','typography_font_size'=>$px($size),
        'typography_font_size_mobile'=>$px($size>28?30:$size)];
    $addGlobal($s,'title_color','colors',$tone);
    $addGlobal($s,'typography_typography','typography',$preset);
    return $make('widget','Heading — '.$title,$s,[],'heading');
};
$text=static function(string $copy,int $size=16,string $tone='primary',string $name='Body text')use($make,$px,$addGlobal):array{
    $s=['editor'=>'<p>'.esc_html($copy).'</p>','typography_typography'=>'custom',
        'typography_font_size'=>$px($size),'typography_line_height'=>$px(1.5,'em')];
    $addGlobal($s,'text_color','colors',$tone);
    $addGlobal($s,'typography_typography','typography','text');
    return $make('widget',$name,$s,[],'text-editor');
};
$eyebrow=static function(string $label)use($text):array{
    return $text($label,12,'accent','Small section label — edit');
};
$button=static function(string $label,string $link='/contact',string $tone='accent')use($make,$px,$edge,$addGlobal):array{
    $s=['text'=>$label,'link'=>['url'=>$link,'is_external'=>'','nofollow'=>''],
        'size'=>'md','align'=>'left','typography_typography'=>'custom',
        'border_radius'=>$edge(8,8,8,8),'button_padding'=>$edge(13,23,13,23),
        'typography_font_size'=>$px(15)];
    $addGlobal($s,'background_color','colors',$tone);
    $addGlobal($s,'button_text_color','colors','via_white');
    $addGlobal($s,'typography_typography','typography','via_buttons');
    return $make('widget','Button — '.$label,$s,[],'button');
};
$card=static function(string $name,array $children,int $desktop=31,int $tablet=48)use($col,$edge,$addGlobal):array{
    $n=$col($name,$children,$desktop,$tablet,100);
    $s=&$n['settings'];
    $s['padding']=$edge(26,24,28,24);
    $s['padding_tablet']=$edge(22,20,24,20);
    $s['padding_mobile']=$edge(22,18,24,18);
    $s['background_background']='classic';
    $addGlobal($s,'background_color','colors','via_white');
    $s['border_border']='solid';$s['border_width']=$edge(1,1,1,1);
    $addGlobal($s,'border_color','colors','via_stone');
    $s['border_radius']=$edge(16,16,16,16);
    return $n;
};
$image=static function(string $name)use($make,$px):array{
    return $make('widget',$name,[
       'image'=>['url'=>'','id'=>0],
       'image_size'=>'full','width'=>$px(100,'%'),
       'align'=>'center'
    ],[],'image');
};
$intro=static function(string $label,string $headline,string $description)use($col,$eyebrow,$head,$text):array{
    return $col('Section introduction',[
        $eyebrow($label),$head($headline),$text($description,16)
    ]);
};
$sections=[];
$start=static function(string $name)use(&$slug,&$counter):void{$slug=$name;$counter=0;};
$finish=static function(string $name,array $children,string $background='via_white')use(&$sections,$make,$refSettings,$gap,$px,$addGlobal):void{
    $s=[
      'content_width'=>'full','width'=>$px(100,'%'),
      'flex_direction'=>'column','flex_direction_tablet'=>'column','flex_direction_mobile'=>'column',
      'flex_gap'=>$gap(28),'flex_align_items'=>'stretch',
      'background_background'=>'classic'
    ];
    // Apply the user's currently-approved Sylvia outer geometry.
    foreach(['content_width','boxed_width','boxed_width_tablet','boxed_width_mobile',
             'width','width_tablet','width_mobile','padding','padding_tablet','padding_mobile'] as $prop){
        if(array_key_exists($prop,$refSettings))$s[$prop]=$refSettings[$prop];
        else unset($s[$prop]);
    }
    $addGlobal($s,'background_color','colors',$background);
    $root=$make('container',$name.' — outer container',$s,$children);
    $sections[$name]=[$root];
};

// 01: Editable native Elementor Accordion (no Pro licensing needed).
$start('inner-10-FAQ-Accordion');
$questions=[
 ['What can I expect?','Replace this with the client-approved answer about the service.'],
 ['Who is this suitable for?','Replace this with the relevant audience and participation requirements.'],
 ['How long does the process take?','Replace with an accurate duration or scheduling explanation.'],
 ['How do I enquire?','Explain the approved contact process and link to the relevant page.']
];
$tabs=[];
foreach($questions as $i=>$item){
    $tabs[]=['_id'=>substr(hash('sha256','via-faq/'.$i),0,7),
             'tab_title'=>$item[0],'tab_content'=>'<p>'.esc_html($item[1]).'</p>'];
}
$faqSettings=['tabs'=>$tabs,'title_tag'=>'h3','selected_icon'=>['value'=>'fas fa-minus','library'=>'fa-solid'],
              'icon'=>['value'=>'fas fa-plus','library'=>'fa-solid']];
$addGlobal($faqSettings,'title_color','colors','primary');
$addGlobal($faqSettings,'content_color','colors','primary');
$faq=$make('widget','FAQ — click each question to edit',$faqSettings,[],'accordion');
$finish('inner-10-FAQ-Accordion',[
    $intro('COMMON QUESTIONS','Frequently asked questions','Edit the questions and answers for each page.'),
    $faq
],'via_cream');

// 02: Left copy / right selectable Image widget.
$start('inner-11-Two-Column-Editorial');
$left=$col('Editorial copy',[
   $eyebrow('YOUR STORY'),$head('A considered approach to change.'),
   $text('Replace this introduction with the page-specific approved content.'),
   $text('Add a supporting paragraph that explains the value of this service.'),
   $button('Explore the Details →')
],52,100,100);
$right=$card('Image and caption',[
   $image('Replace with approved image'),$text('Choose an approved image in the Image widget above.',13)
],44,100);
$finish('inner-11-Two-Column-Editorial',[
   $row('Balanced editorial two columns',[$left,$right])
]);

// 03: Three neat service/value cards.
$start('inner-12-Three-Column-Highlights');
$three=[];
foreach([['01','Awareness'],['02','Growth'],['03','Application']] as $entry){
    $three[]=$card('Highlight '.$entry[0],[
      $head($entry[0],25,'accent'),
      $head($entry[1],25),
      $text('Replace this card with a clear description of the corresponding outcome or benefit.',16),
      $button('Learn More →')
    ],31,48);
}
$finish('inner-12-Three-Column-Highlights',[
   $intro('THREE PILLARS','What makes the difference.','Introduce three qualities, services or programmes.'),
   $row('Three highlight cards',$three)
],'via_cream');

// 04: Flip cards, using native editable headings and text on each side.
// Interaction has one scoped HTML widget with CSS + JS (Elementor Pro Flip Box not required).
$start('inner-13-Interactive-Flip-Boxes');
$flips=[];
foreach([['Personal Development','Build self-awareness','Explore the relevant opportunities and next steps.'],
         ['Leadership Coaching','Lead consciously','Discover suitable coaching and leadership formats.'],
         ['Practitioner Learning','Develop your practice','Review the training and practitioner pathways.']] as $i=>$entry) {
    $front=$col('Front — edit headings and text',[
        $head('0'.($i+1),25,'accent'),
        $head($entry[0],25),
        $text($entry[1].' — select this card to flip.',15)
    ]);
    $front['settings']['_css_classes']='via-flip-v1-front';
    $back=$col('Back — edit headings and text',[
        $head('Explore '.$entry[0],25),
        $text($entry[2],15),
        $text('Select again to return.',13,'via_blue')
    ]);
    $back['settings']['_css_classes']='via-flip-v1-back';
    $flip=$card('Editable flip card '.$i,[$front,$back],31,48);
    $flip['settings']['_css_classes']='via-flip-v1-card';
    $flips[]=$flip;
}
$interactive=<<<'HTML'
<style>
.via-flip-v1 .via-flip-v1-card{position:relative!important;perspective:1000px;min-height:270px;cursor:pointer;overflow:hidden}
.via-flip-v1 .via-flip-v1-card>.via-flip-v1-front,.via-flip-v1 .via-flip-v1-card>.via-flip-v1-back{
position:absolute;inset:0;box-sizing:border-box;padding:22px;display:flex;justify-content:center;
backface-visibility:hidden;-webkit-backface-visibility:hidden;
transition:transform .45s ease,opacity .45s ease;border-radius:14px}
.via-flip-v1 .via-flip-v1-card>.via-flip-v1-front{transform:rotateY(0)}
.via-flip-v1 .via-flip-v1-card>.via-flip-v1-back{transform:rotateY(180deg);opacity:0;background:var(--e-global-color-via_stone,#EEE)}
.via-flip-v1 .via-flip-v1-card.is-flipped>.via-flip-v1-front{transform:rotateY(-180deg);opacity:0}
.via-flip-v1 .via-flip-v1-card.is-flipped>.via-flip-v1-back{transform:rotateY(0);opacity:1}
.via-flip-v1 .via-flip-v1-card:focus-visible{outline:3px solid var(--e-global-color-accent);outline-offset:3px}
@media(max-width:767px){.via-flip-v1 .via-flip-v1-card{min-height:250px}}
@media(prefers-reduced-motion:reduce){
 .via-flip-v1 .via-flip-v1-card>.via-flip-v1-front,.via-flip-v1 .via-flip-v1-card>.via-flip-v1-back{transition:none}
}
</style>
<script>
(function(){
 if(window.__viaFlipV1Attached)return;
 window.__viaFlipV1Attached=true;
 function target(el){return el&&el.closest&&el.closest('.via-flip-v1 .via-flip-v1-card')}
 function toggle(card){
  var next=!card.classList.contains('is-flipped');
  card.classList.toggle('is-flipped',next);
  card.setAttribute('aria-pressed',String(next));
  var front=card.querySelector('.via-flip-v1-front'),back=card.querySelector('.via-flip-v1-back');
  if(front)front.setAttribute('aria-hidden',String(next));
  if(back)back.setAttribute('aria-hidden',String(!next));
 }
 function prepare(card){
  if(card.hasAttribute('data-via-flip-ready'))return;
  card.setAttribute('data-via-flip-ready','1');
  card.setAttribute('role','button');
  card.setAttribute('tabindex','0');
  card.setAttribute('aria-label','Flip card to view details');
  card.setAttribute('aria-pressed','false');
  var back=card.querySelector('.via-flip-v1-back');
  if(back)back.setAttribute('aria-hidden','true');
 }
 document.addEventListener('click',function(e){
  if(document.body.classList.contains('elementor-editor-active'))return;
  var card=target(e.target);
  if(!card||e.target.closest('a,button,input,textarea,select'))return;
  prepare(card);toggle(card);
 });
 document.addEventListener('keydown',function(e){
  if(document.body.classList.contains('elementor-editor-active'))return;
  if(e.key!=='Enter'&&e.key!==' ')return;
  var card=target(e.target);
  if(!card||e.target!==card)return;
  e.preventDefault();prepare(card);toggle(card);
 });
 function setup(){document.querySelectorAll('.via-flip-v1 .via-flip-v1-card').forEach(prepare)}
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',setup);
 else setup();
})();
</script>
HTML;
$code=$make('widget','Flip interaction HTML — only edit if changing effect',['html'=>$interactive],[],'html');
$flipRow=$row('Flip cards layout',$flips);
$flipRow['settings']['_css_classes']='via-flip-v1';
$finish('inner-13-Interactive-Flip-Boxes',[
   $intro('DISCOVER MORE','Turn ideas into possibilities.','Select a card to reveal more information, or use Enter/Space with the keyboard.'),
   $flipRow,$code
]);

// 05: Circular editable metrics — never invent a claim.
$start('inner-14-Track-Record-Circles');
$circles=[];
foreach([['XX+','Years / periods'],['XXX+','Clients / participants'],['X','Regions / markets']] as $i=>$entry){
    $circle=$col('Circular editable statistic '.($i+1),[
      $head($entry[0],36,'primary'),
      $text($entry[1],14,'via_blue')
    ]);
    $circle['settings']['width']=$px(180);
    $circle['settings']['min_height']=$px(180);
    $circle['settings']['padding']=$edge(20,20,20,20);
    $circle['settings']['border_radius']=$edge(999,999,999,999);
    $circle['settings']['flex_justify_content']='center';
    $circle['settings']['flex_align_items']='center';
    $circle['settings']['background_background']='classic';
    $addGlobal($circle['settings'],'background_color','colors','via_stone');
    $circles[]=$col('Track record stat column '.$i,[
        $circle,$text('Replace these placeholders with verified figures.',12)
    ],31,48);
}
$finish('inner-14-Track-Record-Circles',[
  $intro('TRACK RECORD','Experience at a glance.','Enter only figures supported by the organisation’s approved records.'),
  $row('Circular metrics row',$circles)
]);

// 06: Profile / Speaker profile.
$start('inner-15-Profile-Biography');
$photo=$card('Profile photograph',[
    $image('Choose profile photograph here'),
    $text('Replace the blank Image widget with an approved profile picture.',13)
],35,100);
$bio=$col('Editable profile copy',[
    $eyebrow('MEET OUR PEOPLE'),
    $head('Profile name goes here.'),
    $text('Role or professional specialisation.',16,'via_blue'),
    $text('Add a short, client-approved biography and relevant credentials. Keep personal details and qualifications accurate.'),
    $button('Contact Us →')
],61,100);
$finish('inner-15-Profile-Biography',[
    $row('Profile split layout',[$photo,$bio])
],'via_cream');

// 07: Service menu/overview, editable two-column rows and CTAs.
$start('inner-16-Services-Overview');
$serviceRows=[];
foreach([['Service One','Describe the scope and value of the first service.'],
         ['Service Two','Describe who the second service is suitable for.'],
         ['Service Three','Describe the next practical step for the third service.']] as $i=>$entry){
    $serviceRows[]=$card('Service '.$i,[
        $eyebrow('SERVICE 0'.($i+1)),
        $head($entry[0],25),
        $text($entry[1]),
        $button('Explore This Service →')
    ],31,48);
}
$finish('inner-16-Services-Overview',[
    $intro('SERVICES','Find the right support.','Edit each service, description and destination link.'),
    $row('Services grid',$serviceRows)
]);

// 08: Coaching process timeline, four flex columns.
$start('inner-17-Coaching-Journey');
$journey=[];
foreach([['01','Connect','Start with a conversation and explore your priorities.'],
         ['02','Clarify','Identify your goals and what success would look like.'],
         ['03','Work Together','Outline an approach and agree practical next steps.'],
         ['04','Review','Reflect on progress and plan what follows.']] as $entry){
    $journey[]=$card('Step '.$entry[0],[
        $head($entry[0],25,'accent'),$head($entry[1],25),
        $text($entry[2])
    ],23,48);
}
$finish('inner-17-Coaching-Journey',[
    $intro('THE PROCESS','A pathway you can understand.','Adapt the steps to your actual coaching or training process.'),
    $row('Four-step responsive process',$journey)
],'via_cream');

// 09: Classes and programmes catalogue; no fictional dates or course claims.
$start('inner-18-Classes-Programs');
$classes=[];
foreach([['Programme One','Format, dates and duration to be confirmed.'],
         ['Programme Two','Add audience, delivery mode and prerequisites here.'],
         ['Programme Three','Replace with actual curriculum details and next available dates.']] as $entry){
    $classes[]=$card('Editable programme card — '.$entry[0],[
        $eyebrow('TRAINING / CLASS'),
        $head($entry[0],25),
        $text($entry[1]),
        $text('Add schedule or accreditation information only once verified.',13,'via_blue'),
        $button('Enquire About This Class →')
    ],31,48);
}
$finish('inner-18-Classes-Programs',[
    $intro('COURSES & CLASSES','Learn at your own pace.','Replace programme titles and specifications with approved course details.'),
    $row('Class card grid',$classes)
]);

// 10: Contact and enquiry actions — two independently editable buttons.
$start('inner-19-Contact-Enquiry');
$left=$col('Contact intro and methods',[
    $eyebrow('GET IN TOUCH'),
    $head('Let’s start the conversation.'),
    $text('Tell us which service or programme interests you, and we will guide you to an appropriate next step.'),
    $text('Email: add the approved contact address in Elementor.',14,'via_blue')
],54,100);
$right=$card('Two independent enquiry calls to action',[
    $head('How can we help?',25),
    $text('Choose the best way to contact VIA Frontiers.'),
    $button('Send an Enquiry →','/contact'),
    $button('Ask About Coaching →','/contact'),
    $text('Both links are sample /contact destinations: edit each button to match its approved service.',13)
],42,100);
$finish('inner-19-Contact-Enquiry',[
    $row('Contact / enquiry two columns',[$left,$right])
],'via_cream');

$names=array_keys($sections);
if(count($names)!==10)WP_CLI::error('Expected 10 distinct Container templates; found '.count($names));
$existing=get_posts(['post_type'=>'elementor_library','post_status'=>'any',
    'posts_per_page'=>-1,'suppress_filters'=>true]);
foreach($existing as $post)
    if(in_array($post->post_title,$names,true))
        WP_CLI::error('Target template already exists: '.$post->post_title.' (ID '.$post->ID.'). No changes.');
$index=[];
foreach($sections as $name=>$nodes){
    $ids=[];
    $walk=static function(array $arr)use(&$walk,&$ids):void{
       foreach($arr as $node){
           $id=$node['id']??'';
           if(!$id||isset($ids[$id]))WP_CLI::error('Duplicate or missing Elementor ID.');
           $ids[$id]=true;
           $walk($node['elements']??[]);
       }
    };
    $walk($nodes);
    $json=wp_json_encode($nodes,JSON_UNESCAPED_SLASHES);
    if(!$json||!is_array(json_decode($json,true)))WP_CLI::error('Invalid generated JSON for '.$name);
    $index[$name]=$json;
    WP_CLI::log('CREATE: '.$name.' — '.count($ids).' native elements.');
}
WP_CLI::log('Uses Sylvia 178 outer spacing, Kit 19 Global Styles, editable sample content.');
WP_CLI::log('Only new Saved Templates; live homepage 21, header 57, footer 58 are untouched.');
if($mode==='dry-run')WP_CLI::success('DRY RUN PASSED — zero writes');

if($mode==='apply'){
    $dir='/home/amitaxon/via-fullsite-deployment/backups';
    if(!is_dir($dir)||!is_writable($dir))WP_CLI::error('Private manifest directory unavailable.');
    $created=[];
    try{
      foreach($index as $title=>$json){
         $postId=wp_insert_post(['post_type'=>'elementor_library',
                     'post_status'=>'publish','post_title'=>$title,'post_content'=>''],true);
         if(is_wp_error($postId)||!$postId)throw new RuntimeException('Could not create '.$title);
         $created[]=(int)$postId;
         update_post_meta($postId,'_elementor_template_type','container');
         update_post_meta($postId,'_elementor_edit_mode','builder');
         update_post_meta($postId,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'4.3.4');
         update_post_meta($postId,'_elementor_page_settings',[]);
         update_post_meta($postId,'_elementor_data',wp_slash($json));
         if(get_post_meta($postId,'_elementor_data',true)!==$json)throw new RuntimeException('JSON verification failed: '.$title);
         $termSet=wp_set_object_terms($postId,array_map('intval',$types),'elementor_library_type',false);
         if(is_wp_error($termSet)||!$termSet)throw new RuntimeException('Library term missing: '.$title);
         clean_post_cache($postId);
         WP_CLI::log('CREATED '.$postId.' '.$title);
      }
      $manifest=['created_at'=>gmdate('c'),'source_reference_id'=>178,'kit_id'=>19,'templates'=>$created,
          'names'=>$names,'note'=>'Created Saved Templates only; no live pages modified'];
      $log=$dir.'/via-common-ten-'.gmdate('Ymd-His').'-'.wp_generate_password(7,false,false).'.json';
      if(file_put_contents($log,wp_json_encode($manifest,JSON_PRETTY_PRINT),LOCK_EX)===false)
          throw new RuntimeException('Private manifest could not be saved');
      chmod($log,0600);
    }catch(Throwable $e){
      foreach($created as $id)wp_delete_post($id,true);
      WP_CLI::error('Batch failed, newly created template posts removed: '.$e->getMessage());
    }
    WP_CLI::success('Created 10 independent Elementor Saved Container templates. Private manifest: '.$log);
}
