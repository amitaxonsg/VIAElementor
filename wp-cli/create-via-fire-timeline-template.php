<?php
/**
 * VIA Frontiers | FIRE Timeline — Native Elementor Free Container Saved Template.
 * Usage: wp eval-file create-via-fire-timeline-template.php dry-run|apply
 * Creates one NEW library template only, never modifies homepage or existing templates.
 * Uses Elementor Kit 19 globals, and Sylvia 178 width/padding reference.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run', 'apply'], true)) WP_CLI::error('Use dry-run or apply');
if (site_url() !== 'https://axonserver.com' || home_url() !== 'https://axonserver.com' ||
    (int)get_option('elementor_active_kit') !== 19 || (int)get_option('page_on_front') !== 21)
    WP_CLI::error('Wrong VIA development environment; zero writes.');
if (!post_type_exists('elementor_library') || !taxonomy_exists('elementor_library_type'))
    WP_CLI::error('Elementor Saved Templates not registered.');

$name = 'inner-20-FIRE-Timeline';
$posts = get_posts(['post_type'=>'elementor_library','post_status'=>'any',
                    'posts_per_page'=>-1,'suppress_filters'=>true]);
foreach ($posts as $post) {
    if ($post->post_title === $name)
        WP_CLI::error('Saved Template already exists: '.$name.' (ID '.$post->ID.'). No overwrite.');
}
$ref = get_post(178);
if (!$ref || $ref->post_title !== 'Sylvia counter Flexbox' ||
    $ref->post_type !== 'elementor_library' || $ref->post_status !== 'publish' ||
    get_post_meta(178,'_elementor_template_type',true) !== 'container')
    WP_CLI::error('Sylvia reference Container ID 178 changed.');
$refData = json_decode((string)get_post_meta(178,'_elementor_data',true),true);
if (!is_array($refData) || count($refData)!==1 || ($refData[0]['elType']??'')!=='container')
    WP_CLI::error('Sylvia Container JSON changed.');
$refSettings = $refData[0]['settings']??[];
foreach (['content_width','width','padding'] as $k)
    if (!isset($refSettings[$k])) WP_CLI::error('Sylvia missing layout reference '.$k);
$terms=wp_get_object_terms(178,'elementor_library_type',['fields'=>'ids']);
if (is_wp_error($terms) || !$terms) WP_CLI::error('No Container taxonomy on Sylvia template.');

$kit=get_post_meta(19,'_elementor_page_settings',true);
if (!is_array($kit)) WP_CLI::error('Elementor Kit 19 settings missing.');
$colors=[];$fonts=[];
foreach(array_merge($kit['system_colors']??[], $kit['custom_colors']??[]) as $c)
    if (!empty($c['_id'])) $colors[$c['_id']]=true;
foreach(array_merge($kit['system_typography']??[], $kit['custom_typography']??[]) as $f)
    if (!empty($f['_id'])) $fonts[$f['_id']]=true;
foreach (['primary','accent','via_white','via_stone'] as $token)
    if (empty($colors[$token])) WP_CLI::error('Kit 19 Global Color missing: '.$token);
foreach (['primary','text','via_small_labels'] as $token)
    if (empty($fonts[$token])) WP_CLI::error('Kit 19 Global Typography missing: '.$token);
foreach (['heading','text-editor'] as $type) {
    if (!\Elementor\Plugin::instance()->widgets_manager->get_widget_types($type))
        WP_CLI::error('Missing Elementor Free widget '.$type);
}

$length=static fn($n,$unit='px')=>['unit'=>$unit,'size'=>$n,'sizes'=>[]];
$edge=static fn($t,$r,$b,$l)=>['unit'=>'px','top'=>(string)$t,'right'=>(string)$r,
    'bottom'=>(string)$b,'left'=>(string)$l,'isLinked'=>false];
$gap=static fn($n)=>['unit'=>'px','size'=>$n,'column'=>(string)$n,'row'=>(string)$n,'isLinked'=>true];
$seq=0;
$node=static function(string $type,string $name,array $settings,array $children=[],string $widget='')use(&$seq):array{
    $seq++;
    $id=substr(hash('sha256','VIA-FIRE-TIMELINE-v1/'.$seq.'/'.$name),0,7);
    $settings['_title']=$name;
    $n=['id'=>$id,'elType'=>$type,'settings'=>$settings,'elements'=>$children];
    if ($type==='container') $n['isInner']=true;
    if ($type==='widget') $n['widgetType']=$widget;
    return $n;
};
$bind=static function(array &$s,string $control,string $family,string $token):void{
    if (!isset($s['__globals__']))$s['__globals__']=[];
    $s['__globals__'][$control]='globals/'.$family.'?id='.$token;
};
$typography=static function(array &$s,string $token,int $size,int $mobile)use($bind,$length):void{
    $s['typography_typography']='custom';
    $s['typography_font_size']=$length($size);
    $s['typography_font_size_mobile']=$length($mobile);
    // Font family is NOT hardcoded; switching Kit 19 changes this widget.
    $bind($s,'typography_typography','typography',$token);
};
$heading=static function(string $text,string $title,int $size=25,int $mobile=23,string $color='primary',string $font='primary')use($node,$typography,$bind):array{
    $s=['title'=>$text,'header_size'=>$size>28?'h2':'h3','align'=>'left'];
    $typography($s,$font,$size,$mobile);
    $bind($s,'title_color','colors',$color);
    return $node('widget',$title,$s,[],'heading');
};
$body=static function(string $text,string $title,int $size=16)use($node,$typography,$bind):array{
    $s=['editor'=>'<p>'.esc_html($text).'</p>'];
    $typography($s,'text',$size,$size);
    $bind($s,'text_color','colors','primary');
    return $node('widget',$title,$s,[],'text-editor');
};
$container=static function(string $label,string $direction,array $children,array $settings=[])use($node,$length,$gap):array{
    $s=['content_width'=>'full','width'=>$length(100,'%'),
        'flex_direction'=>$direction,'flex_direction_tablet'=>$direction,
        'flex_direction_mobile'=>$direction,
        'flex_gap'=>$gap(12),'flex_align_items'=>'center'];
    return $node('container',$label,array_merge($s,$settings),$children);
};
$circle=static function(string $value,string $label,int $size,int $fontSize,string $bg,string $fg)use($container,$heading,$length,$bind,$edge,$gap):array{
    $h=$heading($value,$label.' text',$fontSize,$fontSize,$fg);
    $h['settings']['align']='center';
    $s=['width'=>$length($size),'min_height'=>$length($size),
        'width_mobile'=>$length($size-8),'min_height_mobile'=>$length($size-8),
        'padding'=>$edge(4,4,4,4),
        'border_radius'=>$edge(999,999,999,999),
        'flex_justify_content'=>'center','flex_align_items'=>'center','flex_gap'=>$gap(0),
        'background_background'=>'classic'];
    $bind($s,'background_color','colors',$bg);
    return $container($label,'column',[$h],$s);
};

// Brand-guided, editable sample copy, matching the approved FIRE terminology.
$intro=$container('FIRE timeline introduction','column',[
    $body('THE VIA APPROACH','Eyebrow — editable',12),
    $heading('A Clear Path to Lasting Change','Main title — editable',36,30),
    $body('Find, Interrupt, Replace and Embed — the four steps of the VIA approach.',
          'Introduction — editable',16)
],[ 'flex_align_items'=>'center','flex_gap'=>$gap(9) ]);
$intro['elements'][0]['settings']['align']='center';
$bind($intro['elements'][0]['settings'],'text_color','colors','accent');
$intro['elements'][1]['settings']['align']='center';
$intro['elements'][2]['settings']['align']='center';

$steps=[
 ['01','F','Find','the pattern and understand what’s happening and why.'],
 ['02','I','Interrupt','the cycle and act on the pattern by changing the response.'],
 ['03','R','Replace','the response and choose a new response.'],
 ['04','E','Embed','the change and reinforce the new behavior until it becomes lasting.'],
];
$rows=[];
foreach($steps as $i=>$step){
    [$number,$letter,$verb,$explanation]=$step;
    $num=$circle($number,'Step '.$number.' number',46,17,'via_stone','primary');
    $connectorSettings=[
        'width'=>$length(2),'min_height'=>$length(44),
        'width_mobile'=>$length(2),'min_height_mobile'=>$length(52),
        'background_background'=>'classic','flex_grow'=>1,
        'padding'=>$edge(0,0,0,0)
    ];
    $bind($connectorSettings,'background_color','colors','accent');
    $connector=$container('Timeline connector '.$number,'column',[],$connectorSettings);
    $rail=$container('Timeline number rail '.$number,'column',
        $i===count($steps)-1?[$num]:[$num,$connector],
        ['width'=>$length(65),'width_tablet'=>$length(58),'width_mobile'=>$length(52),
         'flex_gap'=>$gap(8),'flex_align_items'=>'center','flex_justify_content'=>'flex-start']);
    $letterBadge=$circle($letter,'FIRE letter '.$letter,58,28,'primary','via_white');
    $dividerSettings=[
        'width'=>$length(2),'min_height'=>$length(44),
        'width_mobile'=>$length(2),'min_height_mobile'=>$length(36),
        'background_background'=>'classic','padding'=>$edge(0,0,0,0)
    ];
    $bind($dividerSettings,'background_color','colors','accent');
    $divider=$container('Vertical orange divider '.$letter,'column',[],$dividerSettings);
    $words=$container('Editable FIRE wording '.$number,'row',[
        $heading($verb,'Action title '.$verb,25,23),
        $body($explanation,'Explanation '.$number,16)
    ],['flex_wrap'=>'wrap','flex_justify_content'=>'flex-start',
       'flex_align_items'=>'baseline','flex_gap'=>$gap(5),
       'flex_grow'=>1]);
    $cardSettings=[
        'content_width'=>'full','width'=>$length(90,'%'),'width_tablet'=>$length(88,'%'),
        'width_mobile'=>$length(84,'%'),
        'flex_direction'=>'row','flex_direction_tablet'=>'row','flex_direction_mobile'=>'row',
        'flex_wrap'=>'nowrap','flex_align_items'=>'center','flex_justify_content'=>'flex-start',
        'flex_gap'=>$gap(16),'padding'=>$edge(20,24,20,24),
        'padding_tablet'=>$edge(17,18,17,18),
        'padding_mobile'=>$edge(14,12,14,12),
        'background_background'=>'classic',
        'border_border'=>'solid','border_width'=>$edge(1,1,1,1),
        'border_radius'=>$edge(16,16,16,16)
    ];
    $bind($cardSettings,'background_color','colors','via_white');
    $bind($cardSettings,'border_color','colors','via_stone');
    $card=$container('FIRE content card '.$number,'row',[$letterBadge,$divider,$words],$cardSettings);
    $row=$container('FIRE step '.$number.' row','row',[$rail,$card],[
        'width'=>$length(100,'%'),'flex_gap'=>$gap(10),
        'flex_wrap'=>'nowrap','flex_align_items'=>'stretch',
        'flex_justify_content'=>'flex-start'
    ]);
    $rows[]=$row;
}
$timeline=$container('FIRE four-step timeline','column',$rows,[
    'width'=>$length(100,'%'),'flex_gap'=>$gap(10),
    'flex_align_items'=>'stretch'
]);

$outer=[
    'content_width'=>'full','width'=>$length(100,'%'),
    'flex_direction'=>'column','flex_direction_tablet'=>'column','flex_direction_mobile'=>'column',
    'flex_gap'=>$gap(26),'flex_align_items'=>'stretch',
    'background_background'=>'classic'
];
foreach(['content_width','boxed_width','boxed_width_tablet','boxed_width_mobile',
         'width','width_tablet','width_mobile',
         'padding','padding_tablet','padding_mobile'] as $key){
    if(array_key_exists($key,$refSettings))$outer[$key]=$refSettings[$key];
    else unset($outer[$key]);
}
$bind($outer,'background_color','colors','via_white');
$root=$container('VIA FIRE Timeline — top-level Saved Template','column',[$intro,$timeline],$outer);
$data=[$root];

$ids=[];$walk=static function(array $nodes)use(&$ids,&$walk):void{
    foreach($nodes as $n){
        if(!isset($n['id'])||isset($ids[$n['id']]))WP_CLI::error('Duplicate Elementor element ID.');
        $ids[$n['id']]=true;
        $walk($n['elements']??[]);
    }
};
$walk($data);
$json=wp_json_encode($data,JSON_UNESCAPED_SLASHES);
if(!$json||!is_array(json_decode($json,true))) WP_CLI::error('Invalid resulting Elementor JSON.');
WP_CLI::log('NEW Saved Container template: '.$name);
WP_CLI::log('Editable heading, body copy, four step numbers, F/I/R/E badges, actions and explanations.');
WP_CLI::log('Reference: Sylvia counter Flexbox 178 outer width and responsive padding.');
WP_CLI::log('Kit 19 Global Colours/Fonts; NO CSS injected; no Elementor Pro widgets.');
WP_CLI::log('Elements: '.count($ids).'; Homepage 21, Header 57, Footer 58, Site Kit 19 unchanged.');
if($mode==='dry-run'){
    WP_CLI::success('DRY RUN PASSED — zero writes');
    return;
}
$dir='/home/amitaxon/via-fullsite-deployment/backups';
if(!is_dir($dir)||!is_writable($dir))WP_CLI::error('Private manifest folder unavailable.');
$id=wp_insert_post(['post_type'=>'elementor_library','post_status'=>'publish',
                    'post_title'=>$name,'post_content'=>''],true);
if(is_wp_error($id)||!$id)WP_CLI::error('Unable to create library template.');
try{
    update_post_meta($id,'_elementor_template_type','container');
    update_post_meta($id,'_elementor_edit_mode','builder');
    update_post_meta($id,'_elementor_version',defined('ELEMENTOR_VERSION')?ELEMENTOR_VERSION:'4.3.4');
    update_post_meta($id,'_elementor_page_settings',[]);
    update_post_meta($id,'_elementor_data',wp_slash($json));
    if(get_post_meta($id,'_elementor_data',true)!==$json)
        throw new RuntimeException('Saved Elementor JSON verification failed');
    $termsSet=wp_set_object_terms($id,array_map('intval',$terms),'elementor_library_type',false);
    if(is_wp_error($termsSet)||!$termsSet)
        throw new RuntimeException('Unable to set Elementor Container taxonomy');
    $manifest=['created_at'=>gmdate('c'),'template_id'=>(int)$id,
        'template_title'=>$name,'template_sha256'=>hash('sha256',$json),
        'reference_template'=>178,'kit_id'=>19,'scope'=>'new library item only'];
    $file=$dir.'/via-fire-timeline-created-'.gmdate('Ymd-His').'-'.
        wp_generate_password(7,false,false).'.json';
    if(file_put_contents($file,wp_json_encode($manifest,JSON_PRETTY_PRINT),LOCK_EX)===false)
        throw new RuntimeException('Cannot write private creation manifest');
    chmod($file,0600);
    clean_post_cache($id);
}catch(Throwable $e){
    wp_delete_post($id,true);
    WP_CLI::error('Creation cancelled, new post deleted. '.$e->getMessage());
}
WP_CLI::success('Created '.$name.' as Elementor Saved Container ID '.$id.'. Manifest: '.$file);
