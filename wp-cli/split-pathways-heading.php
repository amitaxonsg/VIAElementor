<?php
/**
 * VIA Frontiers homepage Pathways section title split.
 * Only Elementor page 21, section ID 000002c.
 * Usage: wp eval-file split-pathways-heading.php dry-run|apply
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) { exit("WP-CLI only\n"); }
$mode=$args[0]??'dry-run';
if (!in_array($mode,['dry-run','apply'],true)) WP_CLI::error('Use dry-run or apply');
if (site_url()!=='https://axonserver.com' || (int)get_option('page_on_front')!==21 || (int)get_option('elementor_active_kit')!==19)
 WP_CLI::error('Target site/page/kit mismatch');
$post=get_post(21);
if (!$post || $post->post_type!=='page' || $post->post_status!=='publish') WP_CLI::error('Invalid homepage');
$original=get_post_meta(21,'_elementor_data',true);
$data=json_decode((string)$original,true);
if (!is_array($data)) WP_CLI::error('Invalid Elementor JSON');
$indexes=[];
foreach($data as $idx=>$el) if(($el['id']??'')==='000002c' && ($el['settings']['_title']??'')==='03 Pathways')$indexes[]=$idx;
if(count($indexes)!==1)WP_CLI::error('Pathways section not uniquely found');
$idx=$indexes[0];
$children=&$data[$idx]['elements'];
if(!is_array($children))WP_CLI::error('Missing Pathways children');
$titleI=null;
foreach($children as $i=>$node)if(($node['id']??'')==='0000017')$titleI=$i;
if($titleI===null || ($children[$titleI]['widgetType']??'')!=='heading')WP_CLI::error('Expected Pathways heading absent');
$current=$children[$titleI]['settings']['title']??'';
$nextId='a71c9f2';
$already=false;
foreach($children as $node)if(($node['id']??'')===$nextId)$already=true;
if($already && $current==='Three paths.') {
 WP_CLI::success('Already split; no changes');return;
}
if($already || $current!=='Three paths. One human approach.')WP_CLI::error('Heading modified or unexpected; no changes made');
$children[$titleI]['settings']['title']='Three paths.';
$new=$children[$titleI];
$new['id']=$nextId;
$new['settings']['title']='One human approach.';
$new['settings']['_title']='Pathways heading — Orange phrase';
$new['settings']['__globals__']['title_color']='globals/colors?id=accent';
unset($new['settings']['title_color']);
array_splice($children,$titleI+1,0,[$new]);
$after=wp_json_encode($data);
if(!$after || !is_array(json_decode($after,true)))WP_CLI::error('Unable to encode modified Elementor data');
WP_CLI::log('Split into TWO independent Elementor heading widgets: Navy Three paths. / Orange One human approach.');
WP_CLI::log('All card text, images and URLs preserved. Scoped CSS handles image layout.');
if($mode==='dry-run'){WP_CLI::success('DRY RUN PASSED — unchanged');return;}
$dir='/home/amitaxon/via-fullsite-deployment/backups';
if(!is_dir($dir)||!is_writable($dir))WP_CLI::error('Private backup dir inaccessible');
$backup=$dir.'/pathways-title-21-'.gmdate('Ymd-His').'-'.wp_generate_password(8,false,false).'.json';
if(file_put_contents($backup,$original)===false)WP_CLI::error('Backup write failed');
chmod($backup,0600);
update_post_meta(21,'_elementor_data',wp_slash($after));
if(get_post_meta(21,'_elementor_data',true)!==$after)WP_CLI::error('Write verification failed; restore from '.$backup);
clean_post_cache(21);
WP_CLI::success('Pathways heading split. Backup: '.$backup);
