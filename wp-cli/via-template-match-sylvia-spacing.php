<?php
/**
 * VIA Frontiers | Match four saved homepage templates to the approved Sylvia
 * counter Flexbox outer container width and padding. Template-only change.
 *
 * WP-CLI usage as WordPress account amitaxon:
 *   wp eval-file via-template-match-sylvia-spacing.php dry-run
 *   wp eval-file via-template-match-sylvia-spacing.php apply
 *   wp eval-file via-template-match-sylvia-spacing.php rollback /exact/backup/dir
 *
 * Existing widget layouts, contents, Elementor Global Fonts/Colors,
 * sliders, media, responsive internal columns and frontend pages untouched.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) {
    exit("WP-CLI only\n");
}

$mode = $args[0] ?? 'dry-run';
if (!in_array($mode, ['dry-run','apply','rollback'], true)) {
    WP_CLI::error('Use dry-run, apply or rollback /path/to/exact/backup');
}
if (site_url() !== 'https://axonserver.com' ||
    home_url() !== 'https://axonserver.com' ||
    (int)get_option('elementor_active_kit') !== 19 ||
    (int)get_option('page_on_front') !== 21) {
    WP_CLI::error('Wrong VIA WordPress environment; nothing changed.');
}

$names = [
    'Sylvia counter Flexbox',
    'homepage-03-Pathways',
    'homepage-04-FIRE-Approach',
    'homepage-05-Client-Voices',
    'homepage-06-Start-a-Conversation',
];
$backupRoot = '/home/amitaxon/via-fullsite-deployment/backups';
$version = 'via-template-sylvia-spacing-v1';

// Read all candidates without depending on legacy, deprecated WP functions.
$posts = get_posts([
    'post_type' => 'elementor_library',
    'post_status' => 'any',
    'posts_per_page' => -1,
    'suppress_filters' => true,
]);
$found = [];
foreach ($posts as $post) {
    if (!in_array($post->post_title, $names, true)) continue;
    if (isset($found[$post->post_title]))
        WP_CLI::error('Duplicate saved template: '.$post->post_title);
    if ($post->post_status !== 'publish' ||
        get_post_meta($post->ID,'_elementor_template_type',true) !== 'container')
        WP_CLI::error('Not a published Container template: '.$post->post_title);

    $raw=(string)get_post_meta($post->ID,'_elementor_data',true);
    $nodes=json_decode($raw,true);
    if (!is_array($nodes) || count($nodes)!==1 ||
        ($nodes[0]['elType']??'') !== 'container')
        WP_CLI::error('Unexpected template root: '.$post->post_title);
    $found[$post->post_title] = [
        'id'=>(int)$post->ID,'raw'=>$raw,'nodes'=>$nodes,
    ];
}
foreach ($names as $title) {
    if (!isset($found[$title])) WP_CLI::error('Missing saved template: '.$title);
}

// Use the CURRENT approved Sylvia root as the only width/padding authority.
// Never invent hardcoded px values, fonts, brand colours, or breakpoints.
$reference = $found['Sylvia counter Flexbox']['nodes'][0]['settings'] ?? [];
$props = [
    'content_width', 'boxed_width', 'boxed_width_tablet', 'boxed_width_mobile',
    'width', 'width_tablet', 'width_mobile',
    'padding', 'padding_tablet', 'padding_mobile',
];
$src = [];
foreach ($props as $key) {
    if (array_key_exists($key,$reference)) $src[$key]=$reference[$key];
}
if (empty($src['content_width']) || empty($src['width']) ||
    !is_array($src['width']) || !isset($src['width']['unit']) ||
    !isset($src['width']['size']) || empty($src['padding']) ||
    !is_array($src['padding'])) {
    WP_CLI::error('Sylvia reference is missing native width/padding settings. Refuse to guess.');
}
if (!in_array($src['content_width'], ['full','boxed'], true)) {
    WP_CLI::error('Unexpected Sylvia content width mode.');
}
WP_CLI::log('Reference: Sylvia counter Flexbox (ID '.$found['Sylvia counter Flexbox']['id'].')');
foreach ($src as $key=>$value) WP_CLI::log('  '.$key.' = '.wp_json_encode($value));

$changes=[];
foreach (array_slice($names,1) as $title) {
    $entry=$found[$title];
    $nodes=$entry['nodes'];
    $settings=&$nodes[0]['settings'];
    if (!is_array($settings)) WP_CLI::error('Missing template settings: '.$title);
    $modified=[];
    foreach ($props as $key) {
        $before=$settings[$key]??null;
        if (array_key_exists($key,$src)) {
            if ($before!==$src[$key]) {
                $settings[$key]=$src[$key];
                $modified[]=$key;
            }
        } elseif (array_key_exists($key,$settings)) {
            // Match unset reference values exactly (avoids stale boxed constraints).
            unset($settings[$key]);
            $modified[]=$key.' (unset)';
        }
    }
    unset($settings);
    $json=wp_json_encode($nodes);
    if (!$json || !is_array(json_decode($json,true)))
        WP_CLI::error('Invalid resulting Elementor JSON for '.$title);
    // Guard: only root settings from the approved property allow-list differ.
    $beforeRoot=$entry['nodes'][0];
    $afterRoot=$nodes[0];
    unset($beforeRoot['settings'],$afterRoot['settings']);
    if ($beforeRoot!==$afterRoot)
        WP_CLI::error('Unexpected widget/container changes in '.$title);
    foreach ($entry['nodes'][0]['settings'] as $key=>$value) {
        if (!in_array($key,$props,true) &&
            (!array_key_exists($key,$nodes[0]['settings']) || $nodes[0]['settings'][$key]!==$value))
            WP_CLI::error('Unapproved setting changed: '.$title.'/'.$key);
    }
    foreach ($nodes[0]['settings'] as $key=>$value) {
        if (!in_array($key,$props,true) &&
            !array_key_exists($key,$entry['nodes'][0]['settings']))
            WP_CLI::error('Unapproved setting added: '.$title.'/'.$key);
    }
    $changes[$title]=[
        'id'=>$entry['id'],'original'=>$entry['raw'],'new'=>$json,
        'modified'=>$modified,
    ];
    WP_CLI::log(($modified?'UPDATE':'UNCHANGED').': '.$title.' (ID '.$entry['id'].') '.implode(', ',$modified));
}

if ($mode==='dry-run') {
    WP_CLI::success('DRY RUN PASSED — only saved template outer widths/padding targeted; zero writes');
    return;
}

if ($mode==='rollback') {
    $dir=$args[1]??'';
    if (!is_string($dir) || !str_starts_with($dir,$backupRoot.'/via-template-spacing-') ||
        !is_dir($dir) || !is_file($dir.'/manifest.json'))
        WP_CLI::error('Provide exact private backup directory printed by apply.');
    $manifest=json_decode((string)file_get_contents($dir.'/manifest.json'),true);
    if (!is_array($manifest) || ($manifest['version']??'')!==$version ||
        count($manifest['templates']??[])!==4)
        WP_CLI::error('Invalid backup manifest.');
    $restores=[];
    foreach ($manifest['templates'] as $title=>$item) {
        if (!isset($found[$title]) || (int)$found[$title]['id']!==(int)($item['id']??0))
            WP_CLI::error('Template ID mismatch for rollback: '.$title);
        $current=$found[$title]['raw'];
        if (hash('sha256',$current)!==($item['after']??''))
            WP_CLI::error('Template was edited after this change; refuse to overwrite: '.$title);
        $path=$dir.'/'.$item['file'];
        if (!is_file($path)) WP_CLI::error('Backup file missing: '.$title);
        $original=(string)file_get_contents($path);
        if (hash('sha256',$original)!==($item['before']??''))
            WP_CLI::error('Backup hash mismatch: '.$title);
        $restores[$title]=['id'=>$item['id'],'data'=>$original];
    }
    foreach ($restores as $title=>$item) {
        update_post_meta($item['id'],'_elementor_data',wp_slash($item['data']));
        if (get_post_meta($item['id'],'_elementor_data',true)!==$item['data'])
            WP_CLI::error('Restore verification failed at '.$title);
        clean_post_cache($item['id']);
        WP_CLI::log('Restored '.$title);
    }
    WP_CLI::success('Rolled back four Saved Templates only. Live pages and Sylvia unchanged.');
    return;
}

// Apply only when all four templates are available and writable.
$changed=array_filter($changes,static fn($c)=>$c['original']!==$c['new']);
if (!$changed) { WP_CLI::success('All four Saved Templates already match Sylvia outer spacing.'); return; }
if (!is_dir($backupRoot) || !is_writable($backupRoot))
    WP_CLI::error('Private backup directory missing or unwritable');
$dir=$backupRoot.'/via-template-spacing-'.gmdate('Ymd-His').'-'.wp_generate_password(6,false,false);
if (!mkdir($dir,0700)) WP_CLI::error('Could not create private backup directory');
$manifest=['version'=>$version,'date'=>gmdate('c'),'reference_id'=>$found['Sylvia counter Flexbox']['id'],
           'templates'=>[]];
foreach ($changes as $title=>$item) {
    $filename='template-'.$item['id'].'.json';
    if (file_put_contents($dir.'/'.$filename,$item['original'],LOCK_EX)===false)
        WP_CLI::error('Cannot back up '.$title);
    chmod($dir.'/'.$filename,0600);
    $manifest['templates'][$title]=[
        'id'=>$item['id'],'file'=>$filename,
        'before'=>hash('sha256',$item['original']),
        'after'=>hash('sha256',$item['new']),
    ];
}
if (file_put_contents($dir.'/manifest.json',wp_json_encode($manifest,JSON_PRETTY_PRINT),LOCK_EX)===false)
    WP_CLI::error('Cannot write manifest');
chmod($dir.'/manifest.json',0600);
$written=[];
foreach ($changes as $title=>$item) {
    if ($item['original']===$item['new'])continue;
    $ok=update_post_meta($item['id'],'_elementor_data',wp_slash($item['new']));
    if (!$ok || get_post_meta($item['id'],'_elementor_data',true)!==$item['new']) {
        // Best-effort restore only the already touched Saved Templates.
        foreach ($written as $oldItem) {
            update_post_meta($oldItem['id'],'_elementor_data',wp_slash($oldItem['original']));
            clean_post_cache($oldItem['id']);
        }
        WP_CLI::error('Write verification failed; restored earlier writes. Backup: '.$dir);
    }
    $written[]=$item;
    clean_post_cache($item['id']);
    WP_CLI::log('Saved template updated: '.$title);
}
WP_CLI::success('Matched 4 Saved Templates to Sylvia width/padding. Backups: '.$dir);