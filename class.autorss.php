<?php

/*======================================================================*\
|| #################################################################### ||
|| # Push Notification System Wordpress Plugin                        # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright ©2014-2017 Smart IO Labs Inc. All Rights Reserved.     # ||
|| # This file may not be redistributed in whole or significant part. # ||
|| # --- Smart Push Notification System IS NOT FREE SOFTWARE ---      # ||
|| # https://smartiolabs.com/product/push-notification-system         # ||
|| #################################################################### ||
\*======================================================================*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class smpush_autorss extends smpush_controller {
  public static $wpdateformat;

  public function __construct() {
    parent::__construct();
  }

  public static function run_rss_reader() {
    global $wpdb;
    include_once(ABSPATH.WPINC.'/feed.php');

    $queuefeeds = $wpdb->get_row("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM ".$wpdb->prefix."push_autorss_sources WHERE active='1' AND lastupdate<=".(current_time('timestamp') - 2700)." ORDER BY lastupdate ASC", ARRAY_A);
    if(!empty($queuefeeds['ids'])){
      $wpdb->query("UPDATE ".$wpdb->prefix."push_autorss_sources SET lastupdate='".current_time('timestamp')."' WHERE id IN($queuefeeds[ids])");
      $feeds = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_autorss_sources WHERE id IN($queuefeeds[ids])", ARRAY_A);
      if($feeds){
        foreach($feeds as $rssfeed){
          try{
            $rss = fetch_feed(urldecode($rssfeed['link']));
            if(is_wp_error($rss)){
              $wpdb->update($wpdb->prefix.'push_autorss_sources', array('read_status' => '2', 'read_error' => htmlspecialchars(addslashes($rss->get_error_message()))), array('id' => $rssfeed['id']));
            }
            $maxitems = $rss->get_item_quantity(10);
            $items = $rss->get_items(0, $maxitems);
          }catch(Exception $e){
            $wpdb->update($wpdb->prefix.'push_autorss_sources', array('read_status' => '2', 'read_error' => htmlspecialchars(addslashes($e->getMessage()))), array('id' => $rssfeed['id']));
            continue;
          }

          $newitems = 0;
          if(!empty($maxitems)){
            foreach($items as $item){
              $rssurl = urldecode($item->get_permalink());
              $content = strip_tags($item->get_content());
              $md5rssurl = md5($item->get_permalink());
              $subject = $item->get_title();
              if($enclosure = $item->get_enclosure()){
                $mediaurl = $enclosure->get_thumbnail();
              }
              else{
                $mediaurl = '';
              }
              $date = $item->get_date('Y-m-d H:i:s');

              if(!empty($rssurl) && !empty($subject)){
                $oldrssid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_autorss_data WHERE md5link='$md5rssurl' AND sourceid='$rssfeed[id]'");
                if($oldrssid > 0){
                  continue;
                }
                $data = array();
                $data['sourceid'] = $rssfeed['id'];
                $data['campid'] = $rssfeed['campid'];
                $data['link'] = urlencode($rssurl);
                $data['md5link'] = $md5rssurl;
                $data['subject'] = addslashes($subject);
                if(!empty($rssfeed['text_limit'])){
                  $content = self::ShortString($content, $rssfeed['text_limit']);
                }
                $data['content'] = addslashes($content);
                $wpdb->insert($wpdb->prefix.'push_autorss_data', $data);
                $newitems++;
                if(!empty($rssfeed['read_limit']) && $rssfeed['read_limit'] == $newitems){
                  break;
                }
              }
            }
          }
          $wpdb->query("UPDATE ".$wpdb->prefix."push_autorss_sources SET data_counter=data_counter+$newitems,read_status='1' WHERE id='$rssfeed[id]'");
        }
      }
    }
    //process rss auto importer feeds

    //create campaigns for rss auto importer feeds
    $rssdata = $wpdb->get_row("SELECT GROUP_CONCAT(id SEPARATOR ',') AS ids FROM ".$wpdb->prefix."push_autorss_data WHERE published='0' ORDER BY id ASC", ARRAY_A);
    if(!empty($rssdata['ids'])){
      $wpdb->query("UPDATE ".$wpdb->prefix."push_autorss_data SET published='1' WHERE id IN($rssdata[ids])");
      $datafeeds = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_autorss_data WHERE id IN($rssdata[ids])", ARRAY_A);
      if($datafeeds){
        foreach($datafeeds as $datafeed){
          $template = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_archive WHERE id='$datafeed[campid]'", ARRAY_A);
          if($template){
            $template['name'] = $datafeed['subject'];
            $template['message'] = $datafeed['content'];
            $template['send_type'] = 'now';
            $template['options'] = unserialize($template['options']);
            $template['options']['name'] = $datafeed['subject'];
            $template['options']['message'] = $datafeed['content'];
            $template['options']['fbmsn_message'] = $datafeed['content'];
            $template['options']['fbnotify_message'] = $datafeed['content'];
            $template['options']['fbnotify_link'] = $datafeed['link'];
            $template['options']['email'] = $datafeed['content'];
            $template['options']['desktop_link'] = $datafeed['link'];
            $template['options']['desktop_title'] = $datafeed['subject'];
            $template['options'] = serialize($template['options']);
            unset($template['id']);
            $wpdb->insert($wpdb->prefix.'push_archive', $template);
          }
        }
      }
    }
    //create campaigns for rss auto importer feeds
  }
  
  public static function page() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_autorss';
    if ($_POST) {
      if(smpush_env_demo){
        echo 1;
        exit;
      }
      if (empty($_POST['title']) || empty($_POST['link']) || empty($_POST['campid'])) {
        self::jsonPrint(0, __('All fields are required.', 'smpush-plugin-lang'));
      }
      $data = array();
      $data['title'] = $_POST['title'];
      $data['link'] = urlencode($_POST['link']);
      $data['campid'] = $_POST['campid'];
      $data['text_limit'] = $_POST['text_limit'];
      $data['read_limit'] = $_POST['read_limit'];
      $data['active'] = (isset($_POST['active']))? 1 : 0;
      if (!empty($_POST['id'])) {
        $wpdb->update($wpdb->prefix.'push_autorss_sources', $data, array('id' => $_POST['id']));
      } else {
        $data['lastupdate'] = 0;
        $wpdb->insert($wpdb->prefix.'push_autorss_sources', $data);
      }
      echo 1;
      exit;
    }
    elseif (isset($_GET['delete'])) {
      if(smpush_env_demo){
        echo 1;
        exit;
      }
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_autorss_sources WHERE id='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_autorss_data WHERE sourceid='$_GET[id]'");
      wp_redirect($pageurl);
    }
    elseif (isset($_GET['id'])) {
      if ($_GET['id'] == -1) {
        $source = array('id' => 0, 'title' => '', 'link' => '', 'campid' => 0, 'text_limit' => '0', 'read_limit' => '0', 'active' => '1');
      }
      else {
        $source = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_autorss_sources WHERE id='$_GET[id]'", 'ARRAY_A');
        $source = stripslashes_deep($source);
      }
      $templates = $wpdb->get_results("SELECT id,name FROM ".$wpdb->prefix."push_archive WHERE send_type='template' ORDER BY id ASC", 'ARRAY_A');
      include(smpush_dir.'/pages/rss_form.php');
      exit;
    }
    else {
      self::$wpdateformat = get_option('date_format').' '.get_option('time_format');
      $sources = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_autorss_sources ORDER BY id ASC");
      include(smpush_dir.'/pages/rss_manage.php');
    }
  }
  
}