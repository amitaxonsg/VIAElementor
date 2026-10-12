<?php
/**
 * VIA Frontiers — typography and APAC visual alignment on homepage 21.
 *
 * Dry-run: wp eval-file hero-typography-apac.php dry-run
 * Apply:   wp eval-file hero-typography-apac.php apply
 *
 * Two approved font families only: Cormorant Garamond and Inter.
 * Slogan: Orange + Blue, both driven by Elementor Global Colors.
 * APAC: same visual weight as 35+/3500+, "Experience" below.
 * Does not alter animated counters, buttons, original hero photo or layout.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode=$args[0] ?? 'dry-run';
if (!in_array($mode,['dry-run','apply'],true)) WP_CLI::error('Use dry-run or apply');

if (home_url() !== 'https://axonserver.com' ||
    site_url() !== 'https://axonserver.com' ||
    (int)get_option('page_on_front') !== 21 ||
    (int)get_option('elementor_active_kit') !== 19) {
    WP_CLI::error('Wrong development site, homepage, or Elementor Site Kit');
}
$post=get_post(21);
if (!$post || $post->post_type !== 'page' ||
    $post->post_status !== 'publish' ||
    $post->post_name !== 'via-frontiers-homepage-draft') {
    WP_CLI::error('Homepage identity mismatch');
}

$kit=get_post_meta(19,'_elementor_page_settings',true);
if (!is_array($kit)) WP_CLI::error('Site Kit data unavailable');
$families=[];
foreach (array_merge($kit['system_typography']??[],$kit['custom_typography']??[]) as $item) {
    if (isset($item['_id'])) {
        $families[$item['_id']]=$item['typography_font_family']??'';
    }
}
foreach (['primary'=>'Cormorant Garamond','text'=>'Inter','via_small_labels'=>'Inter'] as $id=>$expected) {
    if (strcasecmp(trim($families[$id]??''),$expected)!==0) {
        WP_CLI::error("Global font {$id} is not {$expected}; currently ".($families[$id]??'missing'));
    }
}
$palette=[];
foreach (array_merge($kit['system_colors']??[],$kit['custom_colors']??[]) as $item) {
    if (isset($item['_id'],$item['color'])) $palette[$item['_id']]=strtoupper($item['color']);
}
foreach (['primary'=>'#262A63','accent'=>'#F1592D','via_blue'=>'#7499D0'] as $id=>$expected) {
    if (($palette[$id]??'')!==$expected) {
        WP_CLI::error("Global color {$id} is not {$expected}; currently ".($palette[$id]??'missing'));
    }
}

$original=get_post_meta(21,'_elementor_data',true);
$elements=json_decode((string)$original,true);
if (!is_array($elements)) WP_CLI::error('Homepage Elementor data invalid');
$hero=null;
foreach ($elements as &$root) {
    if (($root['id']??'')==='0000015' &&
        ($root['settings']['_title']??'')==='02 Hero') {
        $hero=&$root;
        break;
    }
}
unset($root);
if ($hero===null || count($hero['elements']??[])!==2 ||
    ($hero['elements'][0]['id']??'')!=='0000014' ||
    ($hero['elements'][1]['id']??'')!=='0000013') {
    WP_CLI::error('Approved photo hero + single credentials strip not found');
}
$stage=&$hero['elements'][0];
$strip=&$hero['elements'][1];
$copy=&$stage['elements'][0];
if (($copy['id']??'')!=='000000c' ||
    ($copy['elements'][0]['id']??'')!=='0000006' ||
    ($copy['elements'][0]['widgetType']??'')!=='text-editor' ||
    ($copy['elements'][2]['id']??'')!=='0000008' ||
    ($copy['elements'][2]['widgetType']??'')!=='text-editor') {
    WP_CLI::error('Hero text widgets changed — no update');
}
if (count($strip['elements']??[])!==4 ||
    ($strip['elements'][1]['id']??'')!=='0000010' ||
    ($strip['elements'][2]['id']??'')!=='0000011' ||
    ($strip['elements'][1]['widgetType']??'')!=='counter' ||
    ($strip['elements'][2]['widgetType']??'')!=='counter' ||
    (int)($strip['elements'][1]['settings']['ending_number']??-1)!==35 ||
    (int)($strip['elements'][2]['settings']['ending_number']??-1)!==3500) {
    WP_CLI::error('Credentials strip or counter data changed — no update');
}
$apac=&$strip['elements'][3];
$wasText=(($apac['id']??'')==='0000012' &&
    ($apac['widgetType']??'')==='text-editor');
$wasHeadings=(($apac['elType']??'')==='container' &&
    ($apac['settings']['_title']??'')==='APAC statistic' &&
    count($apac['elements']??[])===2 &&
    ($apac['elements'][0]['id']??'')==='0000012' &&
    ($apac['elements'][0]['widgetType']??'')==='heading' &&
    ($apac['elements'][1]['widgetType']??'')==='heading');
if (!$wasText && !$wasHeadings) {
    WP_CLI::error('APAC content changed — no update');
}

$unit=static fn($n,$u='px')=>['unit'=>$u,'size'=>$n,'sizes'=>[]];
$gap=static fn($n)=>[
    'unit'=>'px','size'=>$n,'column'=>(string)$n,
    'row'=>(string)$n,'isLinked'=>true
];
$font=static function(array &$settings,string $prefix,string $id):void {
    if (!isset($settings['__globals__']) || !is_array($settings['__globals__'])) $settings['__globals__']=[];
    $settings[$prefix.'_typography']='custom';
    unset($settings[$prefix.'_font_family']);
    $settings['__globals__'][$prefix.'_typography']='globals/typography?id='.$id;
};
$color=static function(array &$settings,string $name,string $id):void {
    if (!isset($settings['__globals__']) || !is_array($settings['__globals__'])) $settings['__globals__']=[];
    unset($settings[$name]);
    $settings['__globals__'][$name]='globals/colors?id='.$id;
};

// Slogan retains exact meaning, with the two phrases using Elementor Global Colors.
// SVG logo is approved original artwork; Inter ExtraBold is the closest in-system
// companion without loading an unapproved third website font.
$kicker=&$copy['elements'][0]['settings'];
$originalKicker=trim(wp_strip_all_tags((string)($kicker['editor']??'')));
if (!preg_match('/unearth.+lasting impact/i',$originalKicker) &&
    !preg_match('/unearth unconscious drivers\./i',$originalKicker)) {
    WP_CLI::error('Slogan has changed; refusing to replace it');
}
$kicker['editor']='<p><strong><span style="color:var(--e-global-color-accent)">unearth unconscious drivers.</span> <span style="color:var(--e-global-color-via_blue)">Lasting impact..</span></strong></p>';
$font($kicker,'typography','via_small_labels');
$kicker['typography_font_weight']='800';
$kicker['typography_font_size']=$unit(15);
$kicker['typography_font_size_mobile']=$unit(13);
$kicker['typography_letter_spacing']=$unit(0.4);
$kicker['typography_line_height']=$unit(1.4,'em');
$kicker['typography_text_transform']='none';
// Mixed-color words are linked to Elementor global CSS vars in the widget's editor HTML.
// The widget's base color is Navy as a fallback for when no inline span exists.
$color($kicker,'text_color','primary');

// The paragraph must be Inter, not Cormorant Garamond.
$paragraph=&$copy['elements'][2]['settings'];
$font($paragraph,'typography','text');
$color($paragraph,'text_color','primary');
$paragraph['typography_font_size']=$unit(17);
$paragraph['typography_font_size_mobile']=$unit(16);
$paragraph['typography_font_weight']='400';
$paragraph['typography_line_height']=$unit(1.55,'em');

// Create/update APAC so its graphic weight equals the animated Counter numbers.
$ids=[];
$scan=static function($nodes)use(&$scan,&$ids):void {
    foreach ($nodes as $n) {
        if (isset($n['id'])) $ids[$n['id']]=true;
        if (!empty($n['elements'])) $scan($n['elements']);
    }
};
$scan($elements);
$makeId=static function(string $seed)use(&$ids):string {
    for ($i=0;$i<100;$i++){
        $candidate=substr(md5('via-apac-typography-'.$seed.'-'.$i),0,7);
        if (!isset($ids[$candidate])) {$ids[$candidate]=true;return $candidate;}
    }
    WP_CLI::error('Cannot generate unique Elementor ID');
};

if ($wasText) {
    $apac=[
        'id'=>$makeId('container'),
        'elType'=>'container','isInner'=>true,
        'settings'=>[
            '_title'=>'APAC statistic',
            'content_width'=>'full',
            'width'=>$unit(18,'%'),
            'width_tablet'=>$unit(100,'%'),
            'width_mobile'=>$unit(100,'%'),
            'flex_direction'=>'column',
            'flex_align_items'=>'center',
            'flex_align_items_tablet'=>'flex-start',
            'flex_align_items_mobile'=>'flex-start',
            'flex_justify_content'=>'center',
            'flex_gap'=>$gap(3),
        ],
        'elements'=>[
            ['id'=>'0000012','elType'=>'widget','widgetType'=>'heading','settings'=>[],'elements'=>[]],
            ['id'=>$makeId('label'),'elType'=>'widget','widgetType'=>'heading','settings'=>[],'elements'=>[]]
        ]
    ];
} else {
    $apac['settings']['flex_direction']='column';
    $apac['settings']['flex_align_items']='center';
    $apac['settings']['flex_align_items_tablet']='flex-start';
    $apac['settings']['flex_align_items_mobile']='flex-start';
    $apac['settings']['flex_gap']=$gap(3);
}
$apacTitle=&$apac['elements'][0]['settings'];
$apacTitle['_title']='APAC — large statistic';
$apacTitle['title']='APAC';
$apacTitle['header_size']='div';
$apacTitle['size']='xxl';
$apacTitle['align']='center';
$apacTitle['align_tablet']='left';
$apacTitle['align_mobile']='left';
$font($apacTitle,'typography','primary');
$color($apacTitle,'title_color','primary');
$apacTitle['typography_font_size']=$unit(58);
$apacTitle['typography_font_size_tablet']=$unit(46);
$apacTitle['typography_font_size_mobile']=$unit(42);
$apacTitle['typography_line_height']=$unit(1.05,'em');
$apacTitle['typography_font_weight']='500';

$apacSubtitle=&$apac['elements'][1]['settings'];
$apacSubtitle['_title']='APAC statistic label';
$apacSubtitle['title']='Experience';
$apacSubtitle['header_size']='div';
$apacSubtitle['align']='center';
$apacSubtitle['align_tablet']='left';
$apacSubtitle['align_mobile']='left';
$font($apacSubtitle,'typography','text');
$color($apacSubtitle,'title_color','via_blue');
$apacSubtitle['typography_font_size']=$unit(20);
$apacSubtitle['typography_font_size_mobile']=$unit(17);
$apacSubtitle['typography_font_weight']='400';
$apacSubtitle['typography_line_height']=$unit(1.3,'em');

$after=wp_json_encode($elements);
if (!$after || !is_array(json_decode($after,true))) WP_CLI::error('Failed to encode Elementor update');

WP_CLI::log('Approved global site fonts: Primary='.$families['primary'].', Text='.$families['text'].', Labels='.$families['via_small_labels']);
WP_CLI::log('Approved global colors: Navy='.$palette['primary'].', Orange='.$palette['accent'].', Blue='.$palette['via_blue']);
WP_CLI::log('Slogan: Inter ExtraBold; "unearth unconscious drivers." Orange / "Lasting impact.." Blue.');
WP_CLI::log('Description: Inter. APAC: Cormorant Garamond 58px + Inter Blue label.');
WP_CLI::log('Scope: Hero section 0000015 only; background photo and animated 35+/3500+ counters unchanged.');

if ($mode==='dry-run'){
    WP_CLI::success('DRY RUN PASSED — no changes made');
    return;
}
$dir='/home/amitaxon/via-fullsite-deployment/backups';
if (!is_dir($dir)||!is_writable($dir)) WP_CLI::error('Private backups directory missing');
$backup=$dir.'/hero-type-apac-21-'.gmdate('Ymd-His').'-'.wp_generate_password(8,false,false).'.json';
if (file_put_contents($backup,$original)===false) WP_CLI::error('Could not create rollback snapshot');
chmod($backup,0600);
if ($after!==$original) update_post_meta(21,'_elementor_data',wp_slash($after));
if (get_post_meta(21,'_elementor_data',true)!==$after) {
    WP_CLI::error('Elementor write verification failed; backup '.$backup);
}
clean_post_cache(21);
WP_CLI::success('Hero slogan, paragraph and APAC typography updated. Backup '.$backup);
