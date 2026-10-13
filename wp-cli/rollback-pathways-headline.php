<?php
/** VIA Pathways inner heading-row rollback — page 21 only.
 * Runs as wp eval-file rollback-pathways-headline.php dry-run|apply
 * Unwraps only native row b7621a0; never restores unrelated old page data.
 */
if (!defined('ABSPATH') || !defined('WP_CLI')) exit("WP-CLI only\n");
$mode=$args[0]??'dry-run';
if (!in_array($mode,['dry-run','apply'],true)) WP_CLI::error('Use dry-run or apply');
if (site_url()!=='https://axonserver.com'||home_url()!=='https://axonserver.com'
 ||(int)get_option('page_on_front')!==21||(int)get_option('elementor_active_kit')!==19)
 WP_CLI::error('Environment mismatch');
$old=get_post_meta(21,'_elementor_data',true);
$data=json_decode((string)$old,true);
if(!is_array($data))WP_CLI::error('Invalid Elementor JSON');
$idx=null;
foreach($data as $i=>$section)
 if(($section['id']??'')==='000002c'&&($section['settings']['_title']??'')==='03 Pathways') {
  if($idx!==null)WP_CLI::error('Duplicate section');$idx=$i;
 }
if($idx===null)WP_CLI::error('Section missing');
$nodes=&$data[$idx]['elements'];
$ids=array_map(static fn($x)=>$x['id']??'',$nodes);
$before=['0000016','0000017','a71c9f2','0000018','000002b'];
$after=['0000016','b7621a0','0000018','000002b'];
if($ids===$before){WP_CLI::success('Already ungrouped — no changes');return;}
if($ids!==$after)WP_CLI::error('Section differs; refuse rollback');
$row=$nodes[1];
if(($row['elType']??'')!=='container'||
 array_map(static fn($x)=>$x['id']??'',$row['elements']??[])!==['0000017','a71c9f2'])
 WP_CLI::error('Row contains unexpected elements');
array_splice($nodes,1,1,$row['elements']);
$json=wp_json_encode($data);
if(!$json||!is_array(json_decode($json,true)))WP_CLI::error('Invalid new JSON');
WP_CLI::log('Will unwrap two heading widgets without changing their content, colours or unrelated sections.');
if($mode==='dry-run'){WP_CLI::success('DRY RUN PASSED — unchanged');return;}
$dir='/home/amitaxon/via-fullsite-deployment/backups';
if(!is_dir($dir)||!is_writable($dir))WP_CLI::error('Backup unavailable');
$backup=$dir.'/pathways-ungroup-21-'.gmdate('Ymd-His').'-'.wp_generate_password(8,false,false).'.json';
if(file_put_contents($backup,$old,LOCK_EX)===false)WP_CLI::error('Backup failed');
chmod($backup,0600);
if(!update_post_meta(21,'_elementor_data',wp_slash($json)))WP_CLI::error('Failed to write new structure: '.$backup);
if(get_post_meta(21,'_elementor_data',true)!==$json)WP_CLI::error('Write verification failed: '.$backup);
clean_post_cache(21);
WP_CLI::success('Headings ungrouped. Backup: '.$backup);
