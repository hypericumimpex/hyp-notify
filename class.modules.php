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

class smpush_modules extends smpush_controller {

  public static $wpdateformat;
  public static $reports;

  public function __construct() {
    parent::__construct();
  }
  
  public static function widget() {
    register_widget('smpush_widget');
  }
  
  public static function error_log() {
    if(!empty($_POST['clear'])){
      @unlink(smpush_dir.'/cron_log.log');
    }
    $error_log = '';
    if(file_exists(smpush_dir.'/cron_log.log')){
      $error_log = file_get_contents(smpush_dir.'/cron_log.log');
    }
    include(smpush_dir.'/pages/error_log.php');
  }
  
  public static function collectUsage($fromdate, $todate, $platform, $msgid=0) {
	global $wpdb;
    $where = '';
    $dwhere = '';
    if(!empty($platform)){
      $where .= " AND platid='$platform'";
    }
    if(!empty($msgid)){
      $where .= " AND msgid='$msgid'";
      $dwhere .= " AND msgid='$msgid'";
    }
    $where .= " AND `date` BETWEEN '$fromdate' AND '$todate' GROUP BY `date` ASC";
    $dwhere .= " AND `date` BETWEEN '$fromdate' AND '$todate'";
    self::$reports['platforms'] = array(
    'smsgs' => 0,
    'fmsgs' => 0,
    'views' => 0,
    'clicks' => 0,
    'devices' => 0,
    'total' => 0,
    );
    
    $results = $wpdb->get_results("SELECT SUM(stat) AS smsg,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='smsg' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['smsg'] = $result['smsg'];
        @self::$reports[$result['date']]['totals'] += $result['smsg'];
        self::$reports['platforms']['smsgs'] += $result['smsg'];
      }
    }
    $results = $wpdb->get_results("SELECT SUM(stat) AS fmsg,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='fmsg' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['fmsg'] = $result['fmsg'];
        @self::$reports[$result['date']]['totalf'] += $result['fmsg'];
        self::$reports['platforms']['fmsgs'] += $result['fmsg'];
      }
    }
    
    $results = $wpdb->get_results("SELECT SUM(stat) AS sschmsg,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='sschmsg' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['sschmsg'] = $result['sschmsg'];
        @self::$reports[$result['date']]['totals'] += $result['sschmsg'];
        self::$reports['platforms']['smsgs'] += $result['sschmsg'];
      }
    }

    $results = $wpdb->get_results("SELECT SUM(stat) AS fschmsg,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='fschmsg' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['fschmsg'] = $result['fschmsg'];
        @self::$reports[$result['date']]['totalf'] += $result['fschmsg'];
        self::$reports['platforms']['fmsgs'] += $result['fschmsg'];
      }
    }
    
    $results = $wpdb->get_results("SELECT SUM(stat) AS sgeomsg,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='sgeomsg' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['sgeomsg'] = $result['sgeomsg'];
        @self::$reports[$result['date']]['totals'] += $result['sgeomsg'];
        self::$reports['platforms']['smsgs'] += $result['sgeomsg'];
      }
    }
    $results = $wpdb->get_results("SELECT SUM(stat) AS fgeomsg,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='fgeomsg' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['fgeomsg'] = $result['fgeomsg'];
        @self::$reports[$result['date']]['totalf'] += $result['fgeomsg'];
        self::$reports['platforms']['fmsgs'] += $result['fgeomsg'];
      }
    }
    
    if(empty($msgid)){
      $results = $wpdb->get_results("SELECT SUM(stat) AS newdevice,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='newdevice' $where", ARRAY_A);
      if($results){
        foreach($results as $result){
          self::$reports[$result['date']]['newdevice'] = $result['newdevice'];
        }
      }
      $results = $wpdb->get_results("SELECT SUM(stat) AS invdevice,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='invdevice' $where", ARRAY_A);
      if($results){
        foreach($results as $result){
          self::$reports[$result['date']]['invdevice'] = $result['invdevice'];
        }
      }
      
      $results = $wpdb->get_results("SELECT SUM(stat) AS registered,platid FROM ".$wpdb->prefix."push_statistics WHERE `action`='newdevice' $dwhere GROUP BY `platid` ASC", ARRAY_A);
      if($results){
        foreach($results as $result){
          self::$reports['platforms'][$result['platid']] = $result['registered'];
          self::$reports['platforms']['total'] += $result['registered'];
        }
      }

      $results = $wpdb->get_results("SELECT SUM(stat) AS counter,`action` FROM ".$wpdb->prefix."push_statistics WHERE `action` IN ('newdevice','invdevice') $dwhere GROUP BY `action` ASC", ARRAY_A);
      if($results){
        foreach($results as $result){
          self::$reports['platforms'][$result['action']] = $result['counter'];
          self::$reports['platforms']['devices'] += $result['counter'];
        }
      }
    }
    
    $results = $wpdb->get_results("SELECT SUM(stat) AS views,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='views' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['views'] = $result['views'];
        self::$reports['platforms']['views'] += $result['views'];
      }
    }
    
    $results = $wpdb->get_results("SELECT SUM(stat) AS clicks,`date` FROM ".$wpdb->prefix."push_statistics WHERE `action`='clicks' $where", ARRAY_A);
    if($results){
      foreach($results as $result){
        self::$reports[$result['date']]['clicks'] = $result['clicks'];
        self::$reports['platforms']['clicks'] += $result['clicks'];
      }
    }
    
  }
  
  public static function statistics() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_statistics';
    $pagname = 'smpush_statistics';
    wp_enqueue_script('smpush-moment-js');
    wp_enqueue_script('smpush-chart-bundle');
    wp_enqueue_script('smpush-chart-lib');
    wp_enqueue_style('smpush-jquery-smoothness');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-datepicker');
    if(empty($_GET['fromdate'])){
      $_GET['fromdate'] = date('Y-m-1', current_time('timestamp'));
    }
    if(empty($_GET['todate'])){
      $_GET['todate'] = date('Y-m-d', current_time('timestamp'));
    }
    if(empty($_GET['platform'])){
      $_GET['platform'] = 0;
    }
    self::collectUsage($_GET['fromdate'], $_GET['todate'], $_GET['platform']);
    $stat_date = array();
    $stat_date['start'] = strtotime($_GET['fromdate']);
    $stat_date['end'] = strtotime($_GET['todate']);
    include(smpush_dir.'/pages/general_statistics.php');
  }
  
  public static function archivepending() {
    self::archive(true, false);
  }

  public static function archiveauto() {
    self::archive(false, true);
  }

  public static function ajax_actions() {
    if ($_GET['action'] == 'testemail') {
      wp_mail($_POST['email'], $_POST['subject'], $_POST['content'], array('From: '.bloginfo('name').' <'.get_option('admin_email').'>'));
      echo 1;
      exit;
    }
    elseif ($_GET['action'] == 'jsontemplate') {
      global $wpdb;
      $template = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_newsletter_templates WHERE id='$_GET[template]'");
      if($_GET['template'] <= 9){
        $templateContents = file_get_contents(smpush_dir.'/newsletter/'.$template->template.'.json');
        $templateContents = str_replace('{SITEDOMAIN}', get_bloginfo('wpurl'), $templateContents);
        $templateContents = str_replace('{ASSETS_LINK}', (plugins_url().'/smio-push-notification/newsletter/images'), $templateContents);
        echo $templateContents;
      }
      else{
        echo stripslashes($template->template);
      }
      exit;
    }
  }
  
  public static function archive($pendingonly=false, $autocamps=false) {
    if (isset($_GET['action']) && $_GET['action'] == 'edit') {
      smpush_sendpush::send_notification();
      return;
    }
    global $wpdb;
    self::load_jsplugins();
    if($_REQUEST['page'] == 'smpush_campending'){
      $pageurl = admin_url().'admin.php?page=smpush_campending';
      $pagname = 'smpush_campending';
    }
    elseif($_REQUEST['page'] == 'smpush_archiveauto'){
      $pageurl = admin_url().'admin.php?page=smpush_archiveauto';
      $pagname = 'smpush_archiveauto';
    }
    else{
      $pageurl = admin_url().'admin.php?page=smpush_archive';
      $pagname = 'smpush_archive';
    }
    
    if (isset($_GET['delete'])) {
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_archive WHERE id='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_archive_reports WHERE msgid='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE sendoptions='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_statistics WHERE msgid='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_desktop_messages WHERE msgid='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_history WHERE msgid='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_newsletter_templates WHERE msgid='$_GET[id]'");
      exit;
    }
    if (isset($_GET['empty'])) {
      $wpdb->query("TRUNCATE ".$wpdb->prefix."push_archive");
      $wpdb->query("TRUNCATE ".$wpdb->prefix."push_archive_reports");
      $wpdb->query("TRUNCATE ".$wpdb->prefix."push_cron_queue");
      $wpdb->query("TRUNCATE ".$wpdb->prefix."push_queue");
      $wpdb->query("TRUNCATE ".$wpdb->prefix."push_desktop_messages");
      $wpdb->query("TRUNCATE ".$wpdb->prefix."push_history");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_newsletter_templates WHERE static='0'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_statistics WHERE msgid>0");
      wp_redirect($pageurl);
    }
    elseif (!empty($_GET['apply'])) {
      if (!empty($_GET['doaction'])) {
        $doaction = $_GET['doaction'];
      } elseif (!empty($_GET['doaction2'])) {
        $doaction = $_GET['doaction2'];
      }
      $ids = implode(',', $_GET['archive']);
      if ($doaction == 'delete') {
        $wpdb->query("DELETE FROM ".$wpdb->prefix."push_archive WHERE id IN($ids)");
        $wpdb->query("DELETE FROM ".$wpdb->prefix."push_archive_reports WHERE msgid IN($ids)");
        parent::update_counters();
      }
      elseif ($doaction == 'active') {
        $wpdb->query("UPDATE ".$wpdb->prefix."push_archive SET status='1' WHERE id IN($ids)");
      }
      elseif ($doaction == 'deactive') {
        $wpdb->query("UPDATE ".$wpdb->prefix."push_archive SET status='0' WHERE id IN($ids)");
      }
      wp_redirect($pageurl);
    }
    elseif (isset($_GET['action']) && $_GET['action'] == 'reports') {
      wp_enqueue_script('smpush-moment-js');
      wp_enqueue_script('smpush-chart-bundle');
      wp_enqueue_script('smpush-chart-lib');
      wp_enqueue_script('jquery-ui-datepicker');
      wp_enqueue_style('smpush-jquery-ui');
      if(empty($_GET['fromdate'])){
        $_GET['fromdate'] = date('Y-m-1', current_time('timestamp'));
      }
      if(empty($_GET['todate'])){
        $_GET['todate'] = date('Y-m-d', current_time('timestamp'));
      }
      if(empty($_GET['platform'])){
        $_GET['platform'] = 0;
      }
      self::collectUsage($_GET['fromdate'], $_GET['todate'], $_GET['platform'], $_GET['msgid']);
      $stat_date = array();
      $stat_date['start'] = strtotime($_GET['fromdate']);
      $stat_date['end'] = strtotime($_GET['todate']);
      include(smpush_dir.'/pages/archive_reports.php');
    }
    else {
      self::$wpdateformat = get_option('date_format').' '.get_option('time_format');
      $where = array();
      $order = 'ORDER BY id DESC';
      if (!empty($_GET['type'])) {
        if($_GET['type'] == 'now'){
          $where[] = "send_type IN('now','live')";
        }
        else{
          $where[] = "send_type='$_GET[type]'";
        }
      }
      else{
        $_GET['type'] = '';
      }
      if (!empty($_GET['status'])) {
        $where[] = "status='".(($_GET['status'] == 1)? 1 : 0)."'";
      }
      else{
        $_GET['status'] = 0;
      }
      if (!empty($_GET['query'])) {
        $where[] = "(name LIKE '%$_GET[query]%' OR message LIKE '%$_GET[query]%')";
        $order = '';
      }
      else{
        $_GET['query'] = '';
      }
      if($pendingonly){
        $where[] = "processed='0'";
      }
      else{
        $where[] = "processed='1'";
      }
      if($autocamps){
        $where[] = "send_type='custom'";
      }
      elseif(! $pendingonly){
        $where[] = "send_type!='custom'";
      }
      if (count($where) > 0) {
        $where = 'WHERE '.implode(' AND ', $where);
      } else {
        $where = '';
      }
      $sql = self::Paging("SELECT * FROM ".$wpdb->prefix."push_archive $where $order", $wpdb);
      $archives = $wpdb->get_results($sql);
      $paging_args = array(
      'base' => preg_replace('/&?callpage=([0-9]+)/', '', $_SERVER['REQUEST_URI']).'%_%',
      'format' => '&callpage=%#%',
      'total' => self::$paging['pages'],
      'current' => self::$paging['page'],
      'show_all' => false,
      'end_size' => 3,
      'mid_size' => 2,
      'prev_next' => true,
      'prev_text' => __('« Previous'),
      'next_text' => __('Next »')
      );
      include(smpush_dir.'/pages/archive_manage.php');
    }
  }

  public static function tokens() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_tokens';
    $pagname = 'smpush_tokens';
    if (!empty($_POST['device_type'])) {
      if (empty($_POST['device_token'])) {
        self::jsonPrint(0, __('Field `Device Token` is required.', 'smpush-plugin-lang'));
      }
      if ($_POST['id'] > 0) {
        self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {id_name}='$_POST[id]',{token_name}='$_POST[device_token]',{md5token_name}='".md5($_REQUEST['device_token'])."',{type_name}='$_POST[device_type]',{info_name}='$_POST[information]',{active_name}='$_POST[active]',{latitude_name}='$_POST[latitude]',{longitude_name}='$_POST[longitude]',{gpstime_name}='".current_time('timestamp')."' WHERE {id_name}='$_POST[id]'"));
        $tokenid = $_POST['id'];
      } else {
        self::$pushdb->query(self::parse_query("INSERT INTO {tbname} ({token_name},{md5token_name},{type_name},{info_name},{active_name},{latitude_name},{longitude_name},{gpstime_name},{postdate}) VALUES ('$_POST[device_token]','".md5($_REQUEST['device_token'])."','$_POST[device_type]','$_POST[information]','$_POST[active]','$_POST[latitude]','$_POST[longitude]','".current_time('timestamp')."','".gmdate('Y-m-d H:i:s', current_time('timestamp'))."')"));
        $tokenid = self::$pushdb->insert_id;
        $wpdb->query("UPDATE ".$wpdb->prefix."push_connection SET counter=counter+1 WHERE id='".self::$apisetting['def_connection']."'");
      }
      if (!empty($_POST['channels'])) {
        $_POST['channels'] = implode(',', $_POST['channels']);
        smpush_api::editSubscribedChannels($tokenid, $_POST['channels']);
      }
      echo 1;
      exit;
    }
    elseif (isset($_GET['remove_duplicates'])) {
      self::$pushdb->query(self::parse_query("CREATE TABLE {tbname_temp} AS SELECT * FROM {tbname} GROUP BY {md5token_name}"));
      if (empty(self::$pushdb->last_error)) {
        self::$pushdb->query(self::parse_query("ALTER TABLE {tbname_temp} ADD PRIMARY KEY({id_name})"));
        self::$pushdb->query(self::parse_query("ALTER TABLE {tbname_temp} CHANGE {id_name} {id_name} INT(11) NOT NULL AUTO_INCREMENT"));
        self::$pushdb->query(self::parse_query("ALTER TABLE  {tbname_temp} ADD INDEX ({md5token_name})"));
        self::$pushdb->query(self::parse_query("DROP TABLE {tbname}"));
        self::$pushdb->query(self::parse_query("RENAME TABLE {tbname_temp} TO {tbname}"));
        parent::update_counters();
        wp_redirect($pageurl);
      }
      else {
        wp_die(__('An error has occurred, the system stopped and rolled back the changes.', 'smpush-plugin-lang'));
      }
    }
    elseif (isset($_GET['remove_deads'])) {
      $counter = 0;
      $delIDs = [];
      $devices = self::$pushdb->get_results(self::parse_query("SELECT {id_name} AS devid FROM {tbname} WHERE {active_name}='0'"));
      if($devices){
        foreach($devices as $device){
          if($counter == 1000){
            $delIDs = implode(',', $delIDs);
            self::$pushdb->query(self::parse_query("DELETE FROM {tbname} WHERE {id_name} IN ($delIDs)"));
            $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE token_id IN ($delIDs)");
            $counter = 0;
            $delIDs = [];
          }
          $counter++;
          $delIDs[] = $device->devid;
        }
        if($counter < 1000 && $counter > 0){
          $delIDs = implode(',', $delIDs);
          self::$pushdb->query(self::parse_query("DELETE FROM {tbname} WHERE {id_name} IN ($delIDs)"));
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE token_id IN ($delIDs)");
        }
      }
      parent::update_counters();
      wp_redirect($pageurl);
    }
    elseif (isset($_GET['delete'])) {
      self::$pushdb->query(self::parse_query("DELETE FROM {tbname} WHERE {id_name}='$_GET[id]'"));
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE token_id='$_GET[id]' AND connection_id='".self::$apisetting['def_connection']."'");
      parent::update_counters();
      exit;
    }
    elseif (isset($_GET['id'])) {
      $channels = $wpdb->get_results("SELECT id,title FROM ".$wpdb->prefix."push_channels ORDER BY title ASC");
      $types_name = $wpdb->get_row("SELECT ios_name,iosfcm_name,android_name,wp_name,bb_name,chrome_name,safari_name,firefox_name,wp10_name,fbmsn_name,fbnotify_name,opera_name,edge_name,samsung_name,email_name FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'");
      if ($_GET['id'] == -1) {
        $token = array('id' => 0, 'device_token' => '', 'device_type' => '', 'information' => '', 'latitude' => '', 'longitude' => '', 'channels' => array(), 'active' => 1);
      }
      else {
        $subschannels = $wpdb->get_results("SELECT channel_id FROM ".$wpdb->prefix."push_relation WHERE token_id='$_GET[id]' AND connection_id='".self::$apisetting['def_connection']."'");
        $token = self::$pushdb->get_row(self::parse_query("SELECT {id_name} AS id,{token_name} AS device_token,{type_name} AS device_type,{info_name} AS information,{active_name} AS active,{latitude_name} AS latitude,{longitude_name} AS longitude FROM {tbname} WHERE {id_name}='$_GET[id]'"), 'ARRAY_A');
        $token = array_map('stripslashes', $token);
        $token['channels'] = array();
        if ($subschannels) {
          foreach ($subschannels as $subschannel) {
            $token['channels'][] = $subschannel->channel_id;
          }
        }
      }
      include(smpush_dir.'/pages/token_form.php');
      exit;
    }
    else {
      $types_name = $wpdb->get_row("SELECT dbtype,ios_name,iosfcm_name,android_name,wp_name,bb_name,chrome_name,safari_name,firefox_name,wp10_name,fbmsn_name,fbnotify_name,opera_name,edge_name,samsung_name,email_name FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'");
      $channels = $wpdb->get_results("SELECT id,title FROM ".$wpdb->prefix."push_channels ORDER BY title ASC");
      $where = array();
      $inner = '';
      $order = 'ORDER BY {tbname}.{id_name} DESC';
      if (!empty($_GET['query'])) {
        $where[] = "({tbname}.{token_name}='$_GET[query]' OR {tbname}.{info_name} LIKE '%$_GET[query]%')";
        $order = '';
      }
      if (!empty($_GET['device_type'])) {
        $where[] = "{tbname}.{type_name}='$_GET[device_type]'";
      }
      else {
        $_GET['device_type'] = '';
      }
      if (!empty($_GET['userid'])) {
        $where[] = "{tbname}.userid='$_GET[userid]'";
      }
      else {
        $_GET['userid'] = '';
      }
      if (!empty($_GET['status'])) {
        if ($_GET['status'] == 2)
          $status = 0;
        else
          $status = 1;
        $where[] = "{tbname}.{active_name}='$status'";
      }
      else {
        $_GET['status'] = '-1';
      }
      if (!empty($_GET['channel_id'])) {
        $table = $wpdb->prefix.'push_relation';
        $inner = "INNER JOIN $table ON($table.token_id={tbname}.{id_name} AND $table.connection_id='".self::$apisetting['def_connection']."')";
        $where[] = "$table.channel_id='$_GET[channel_id]'";
        $order = 'GROUP BY {tbname}.{id_name} DESC';
      } else {
        $_GET['channel_id'] = '';
      }
      if (count($where) > 0) {
        $where = 'WHERE '.implode(' AND ', $where);
      } else {
        $where = '';
      }
      if (!empty($_GET['apply'])) {
        if (!empty($_GET['doaction'])) {
          $doaction = $_GET['doaction'];
        } elseif (!empty($_GET['doaction2'])) {
          $doaction = $_GET['doaction2'];
        }
        $ids = implode(',', $_GET['device']);
        if ($doaction == 'activate') {
          self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='1' WHERE {id_name} IN($ids)"));
        } elseif ($doaction == 'deactivate') {
          self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name} IN($ids)"));
        } elseif ($doaction == 'delete') {
          self::$pushdb->query(self::parse_query("DELETE FROM {tbname} WHERE {id_name} IN($ids)"));
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE token_id IN($ids) AND connection_id='".self::$apisetting['def_connection']."'");
          parent::update_counters();
        }
        wp_redirect($pageurl);
      } elseif (!empty($_GET['applytoall'])) {
        if (!empty($_GET['doaction'])) {
          $doaction = $_GET['doaction'];
        } elseif (!empty($_GET['doaction2'])) {
          $doaction = $_GET['doaction2'];
        }
        $tokens = self::$pushdb->get_results(self::parse_query("SELECT {tbname}.{id_name} AS id FROM {tbname} $inner $where GROUP BY {tbname}.{id_name}"));
        if ($tokens) {
          foreach ($tokens as $token) {
            if ($doaction == 'activate') {
              self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='1' WHERE {id_name}='".$token->id."'"));
            } elseif ($doaction == 'deactivate') {
              self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='".$token->id."'"));
            } elseif ($doaction == 'delete') {
              self::$pushdb->query(self::parse_query("DELETE FROM {tbname} WHERE {id_name}='".$token->id."'"));
              $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE token_id='".$token->id."' AND connection_id='".self::$apisetting['def_connection']."'");
              parent::update_counters();
            }
          }
        }
        wp_redirect($pageurl);
      }
      $dbtype = $wpdb->get_var("SELECT dbtype FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'");
      if($dbtype == 'remote'){
        $sql = self::Paging(self::parse_query("SELECT {tbname}.{id_name} AS id,{tbname}.{token_name} AS device_token,{tbname}.{type_name} AS device_type
        ,{tbname}.{info_name} AS information,{tbname}.{latitude_name} AS latitude,{tbname}.{longitude_name} AS longitude,{tbname}.{postdate} AS timepost,{tbname}.{active_name} AS active FROM {tbname}
        $inner $where $order"), self::$pushdb);
      }
      else{
        $sql = self::Paging(self::parse_query("SELECT {tbname}.{id_name} AS id,{tbname}.{token_name} AS device_token,{tbname}.{type_name} AS device_type
        ,{tbname}.{info_name} AS information,{tbname}.{latitude_name} AS latitude,{tbname}.{longitude_name} AS longitude,{tbname}.{postdate} AS timepost,{tbname}.{active_name} AS active,$wpdb->users.user_login AS user FROM {tbname}
        LEFT JOIN $wpdb->users ON($wpdb->users.ID={tbname}.userid)
        $inner $where $order"), self::$pushdb);
      }
      self::$wpdateformat = get_option('date_format').' '.get_option('time_format');
      $tokens = self::$pushdb->get_results($sql);
      $paging_args = array(
      'base' => preg_replace('/&?callpage=([0-9]+)/', '', $_SERVER['REQUEST_URI']).'%_%',
      'format' => '&callpage=%#%',
      'total' => self::$paging['pages'],
      'current' => self::$paging['page'],
      'show_all' => false,
      'end_size' => 3,
      'mid_size' => 2,
      'prev_next' => true,
      'prev_text' => __('« Previous'),
      'next_text' => __('Next »')
      );
      include(smpush_dir.'/pages/token_manage.php');
    }
  }

  public static function push_channel() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_channel';
    if ($_POST) {
      if (empty($_POST['title'])) {
        self::jsonPrint(0, __('Field title is required.', 'smpush-plugin-lang'));
      }
      if ($_POST['privacy'] == 1)
        $privacy = 1;
      else
        $privacy = 0;
      if ($_POST['id'] > 0) {
        $wpdb->update($wpdb->prefix.'push_channels', array('title' => $_POST['title'], 'description' => $_POST['description'], 'private' => $privacy), array('id' => $_POST['id']));
      } else {
        $wpdb->insert($wpdb->prefix.'push_channels', array('title' => $_POST['title'], 'description' => $_POST['description'], 'private' => $privacy));
      }
      echo 1;
      exit;
    } elseif (isset($_GET['update_counters'])) {
      parent::update_all_counters();
      wp_redirect($pageurl);
    } elseif (isset($_GET['delete'])) {
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_channels WHERE id='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE channel_id='$_GET[id]'");
      wp_redirect($pageurl);
    } elseif (isset($_GET['default'])) {
      $wpdb->query("UPDATE ".$wpdb->prefix."push_channels SET `default`='0'");
      $wpdb->update($wpdb->prefix.'push_channels', array('default' => 1), array('id' => $_GET['id']));
      wp_redirect($pageurl);
    } elseif (isset($_GET['id'])) {
      if ($_GET['id'] == -1) {
        $channel = array('id' => 0, 'title' => '', 'description' => '', 'private' => '0');
      } else {
        $channel = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_channels WHERE id='$_GET[id]'", 'ARRAY_A');
        $channel = array_map('stripslashes', $channel);
      }
      include(smpush_dir.'/pages/channel_form.php');
      exit;
    } else {
      $channels = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_channels ORDER BY id DESC");
      include(smpush_dir.'/pages/channel_manage.php');
    }
  }
  
  public static function import() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_import';
    if(!empty($_FILES['csv']['tmp_name'])) {
      $upload_dir = wp_upload_dir();
      $cert_upload_path = $upload_dir['basedir'].'/certifications';
      if(! file_exists($cert_upload_path)){
        if(! mkdir($cert_upload_path)){
          header( 'HTTP/1.1 400 BAD REQUEST');
          echo __('can not create a directory to save the certifications files under uploads directory .', 'smpush-plugin-lang');
          exit;
        }
      }
      
      $csvpath = $cert_upload_path.'/imported_file_'.time().'.csv';
      @unlink($csvpath);
      if(!move_uploaded_file($_FILES['csv']['tmp_name'], $csvpath)){
        header( 'HTTP/1.1 400 BAD REQUEST');
        echo __('Can not move CSV file.', 'smpush-plugin-lang');
        exit;
      }
    
      $delimiter = $_POST['delimiter'];
      $csvhtml = '';
      if (($handle = fopen($csvpath, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
          $num = count($data);
          for ($c=0; $c < $num; $c++) {
            $data[$c] = trim(str_replace(array("\r", "\n", "\r\n"), '', $data[$c]));
            if(!empty($data[$c])){
              $csvhtml .= '<option value="'.$c.'">'.$data[$c].'</option>';
            }
          }
          fclose($handle);
          break;
        }
      }
      if(empty($csvhtml)){
        header( 'HTTP/1.1 400 BAD REQUEST');
        echo __('Can not parse CSV file.', 'smpush-plugin-lang');
        @unlink($csvpath);
      }
      else{
        header( 'HTTP/1.1 200 OK');
        include(smpush_dir.'/pages/tokens_import_options.php');
      }
      exit;
    }
    elseif ($_POST) {
      @set_time_limit(0);
      $device_token = $_POST['token'];
      $platform = $_POST['platform'];
      $info = $_POST['info'];
      
      $row = 1;
      $duplications = $new = $fail = $error = 0;
      $csvpath = realpath($_POST['csvfile']);
      if(!file_exists($csvpath)){
        header( 'HTTP/1.1 400 BAD REQUEST');
        echo __('Can not parse CSV file.', 'smpush-plugin-lang');
        @unlink($csvpath);
        exit;
      }
      if (($handle = fopen($csvpath, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 0, $_POST['delimiter'])) !== FALSE) {
          $row++;
          if($row == 2)continue;
          if(empty($data[$device_token])){
            $fail++;
            continue;
          }
          $data_device_token = addslashes($data[$device_token]);
          $data_info = addslashes($data[$info]);
          $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} FROM {tbname} WHERE {md5token_name}='".md5($data_device_token)."' AND {type_name}='$platform'"));
          if($tokenid > 0){
            $duplications++;
          }
          else{
            self::$pushdb->query(self::parse_query("INSERT INTO {tbname} ({token_name},{md5token_name},{type_name},{info_name},{active_name},{latitude_name},{longitude_name},{gpstime_name}) VALUES ('$data_device_token','".md5($data_device_token)."','$platform','$data_info','1','0.00000000','0.00000000','".current_time('timestamp')."')"));
            $new++;
          }
        }
        fclose($handle);
        @unlink($csvpath);
      }
      
      if($new > 0){
        $current_date = gmdate('Y-m-d', current_time('timestamp'));
        $statid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_statistics WHERE platid='$platform' AND `date`='$current_date' AND action='newdevice'");
        if(empty($statid)){
          $stat = array();
          $stat['platid'] = $platform;
          $stat['date'] = $current_date;
          $stat['action'] = 'newdevice';
          $stat['stat'] = $new;
          $wpdb->insert($wpdb->prefix.'push_statistics', $stat);
        }
        else{
          $wpdb->query("UPDATE ".$wpdb->prefix."push_statistics SET `stat`=`stat`+$new WHERE id='$statid'");
        }
      }
      
      if(empty($error)){
        header( 'HTTP/1.1 200 OK');
        echo __('System finished importing subscribers', 'smpush-plugin-lang').': '.$new.' '.__('new', 'smpush-plugin-lang').' . '.$duplications.' '.__('duplicated', 'smpush-plugin-lang').' . '.$fail.' '.__('fail', 'smpush-plugin-lang');
      }
      else{
        header( 'HTTP/1.1 400 BAD REQUEST');
        echo $error;
      }
      exit;
    }
    else {
      include(smpush_dir.'/pages/tokens_import.php');
    }
  }

  public static function onesignal() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_onesignal';
    if (isset($_POST['appid'])) {
      $helper = new smpush_helper();
      $params = [];
      $headers = array('Authorization: Basic '.$_POST['seckey'], 'Content-Type: application/json');
      $params = "{\n    \"extra_fields\": [\"location\", \"rooted\", \"ip\", \"country\", \"web_auth\", \"web_p256\", \"external_user_id\", \"notification_types\"], \"last_active_since\": \"1469392779\"\n}";
      $response = $helper->buildCurl('https://onesignal.com/api/v1/players/csv_export?app_id='.$_POST['appid'], false, $params, $headers);
      if($helper->curl_status == 200){
        $csvinfo = json_decode($response, true);
        if(! empty($csvinfo['csv_file_url'])){
          echo '<br><br>CSV Link:<br><p><input value="'.$csvinfo['csv_file_url'].'" onfocus="jQuery(this).select()" type="text" size="100"></p>';
        }
        else{
          echo 'Error! '.$response;
        }
      }
      else{
        echo 'Error! please ensure that you entered the correct App ID and Secret Key<br>'.$response;
      }
    }
    elseif (isset($_POST['csvlink'])) {
      @set_time_limit(0);

      $helper = new smpush_helper();
      $csv_gz_contents = $helper->buildCurl($_POST['csvlink']);
      if($helper->curl_status == 200){
        $upload_dir = wp_upload_dir();
        $csv_gz_file = $upload_dir['basedir'].'/onesignal_csv.gz';

        $csv_gz_handler = fopen($csv_gz_file, 'w+');
        fwrite($csv_gz_handler, $csv_gz_contents);
        fclose($csv_gz_handler);

        $csv_file = $upload_dir['basedir'].'/onesignal.csv';
        $buffer_size = 4096; // read 4kb at a time

        // Open our files (in binary mode)
        $file = gzopen($csv_gz_file, 'rb');
        $out_file = fopen($csv_file, 'wb');

        // Keep repeating until the end of the input file
        while (!gzeof($file)) {
          // Read buffer-size bytes
          // Both fwrite and gzread and binary-safe
          fwrite($out_file, gzread($file, $buffer_size));
        }
        // Files are done, close files
        fclose($out_file);
        gzclose($file);
        @unlink($csv_gz_file);
      }
      else{
        echo 'Error during downloading file contents !<br>';
        echo __('Note that OneSignal takes some time to proccess your CSV file after generating your CSV link.', 'smpush-plugin-lang');
        exit;
      }

      $sys_keys = array();
      $sys_keys['device_token'] = 'identifier';
      $sys_keys['platform'] = 'device_type';
      $sys_keys['info'] = 'device_model';
      $sys_keys['lat'] = 'lat';
      $sys_keys['lng'] = 'lon';
      $sys_keys['userid'] = 'external_user_id';
      $sys_keys['auth'] = 'web_auth';
      $sys_keys['p256'] = 'web_p256';

      $row = 1;
      $duplications = $new = $fail = $error = 0;
      $csv_file = realpath($csv_file);
      $current_time = current_time('timestamp');
      if(!file_exists($csv_file)){
        echo __('Can not parse CSV file.', 'smpush-plugin-lang').'<br>';
        echo __('Note that OneSignal takes some time to proccess your CSV file after generating your CSV link.', 'smpush-plugin-lang');
        @unlink($csv_file);
        exit;
      }

      if (($handle = fopen($csv_file, 'r')) !== FALSE) {
        while (($data = fgetcsv($handle, 0, ',')) !== FALSE) {
          $row++;
          if($row == 2){
            foreach($sys_keys as $key_name => $key_value){
              foreach($data as $csv_key_index => $csv_key){
                if($key_value == $csv_key){
                  $$key_name = $csv_key_index;
                  break;
                }
              }
            }
            continue;
          }
          if(empty($data[$device_token])){
            $fail++;
            continue;
          }
          $data_device_token = json_encode(array('endpoint' => $data[$device_token], 'auth' => $data[$auth], 'p256dh' => $data[$p256]));
          $data_info = addslashes($data[$info]);
          $data_userid = intval($data[$userid]);
          $data_lat = (empty($data[$lat]))? '0.00000000' : $data[$lat];
          $data_lng = (empty($data[$lng]))? '0.00000000' : $data[$lng];
          if($data[$platform] == 3){
            $device_type = 'wp';
          }
          elseif($data[$platform] == 5){
            $device_type = 'chrome';
          }
          elseif($data[$platform] == 6){
            $device_type = 'wp10';
          }
          elseif($data[$platform] == 7){
            $device_type = 'safari';
          }
          elseif($data[$platform] == 8){
            $device_type = 'firefox';
          }
          elseif($data[$platform] == 11){
            $device_type = 'email';
            $data_device_token = $data[$device_token];
          }
          else{
            continue;
          }
          if(isset($_POST['no_duplicates'])){
            $tokenid = self::$pushdb->get_var(self::parse_query("SELECT {id_name} FROM {tbname} WHERE {md5token_name}='".md5($data_device_token)."' AND {type_name}='$device_type'"));
          }
          else{
            $tokenid = 0;
          }
          if($tokenid > 0){
            $duplications++;
          }
          else{
            self::$pushdb->query(self::parse_query("INSERT INTO {tbname} ({token_name},{md5token_name},{type_name},{info_name},{active_name},{latitude_name},{longitude_name},{gpstime_name}) VALUES ('$data_device_token','".md5($data_device_token)."','$device_type','$data_info','1','$data_lat','$data_lng','".$current_time."')"));
            if($data_userid > 0){
              self::$pushdb->query(self::parse_query("UPDATE {tbname} SET userid='$data_userid' WHERE id='".self::$pushdb->insert_id."'"));
            }
            $new++;
          }
        }
        fclose($handle);
        @unlink($csv_file);
      }

      if($new > 0){
        $current_date = gmdate('Y-m-d', current_time('timestamp'));
        $statid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_statistics WHERE platid='$platform' AND `date`='$current_date' AND action='newdevice'");
        if(empty($statid)){
          $stat = array();
          $stat['platid'] = $platform;
          $stat['date'] = $current_date;
          $stat['action'] = 'newdevice';
          $stat['stat'] = $new;
          $wpdb->insert($wpdb->prefix.'push_statistics', $stat);
        }
        else{
          $wpdb->query("UPDATE ".$wpdb->prefix."push_statistics SET `stat`=`stat`+$new WHERE id='$statid'");
        }
      }

      if(empty($error)){
        echo __('System finished importing subscribers', 'smpush-plugin-lang').': '.$new.' '.__('new', 'smpush-plugin-lang').' . '.$duplications.' '.__('duplicated', 'smpush-plugin-lang').' . '.$fail.' '.__('fail', 'smpush-plugin-lang');
      }
      else{
        echo $error;
      }
      exit;
    }
    else {
      include(smpush_dir.'/pages/onesignal_import.php');
    }
  }

  public static function testing() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_test_sending';
    if ($_POST) {
      $message = $_POST['message'];
      $applelog = __('No device token', 'smpush-plugin-lang');
      $googlelog = __('No device token', 'smpush-plugin-lang');
      if (!empty($_POST['ios_token'])) {
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', self::$apisetting['apple_cert_path']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$apisetting['apple_passphrase']);
        if (self::$apisetting['apple_sandbox'] == 1) {
          $appleserver = 'tls://gateway.sandbox.push.apple.com:2195';
        } else {
          $appleserver = 'tls://gateway.push.apple.com:2195';
        }
        @$fp = stream_socket_client($appleserver, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp) {
          if (empty($errstr))
            $errstr = __('Apple Certification error or problem with Password phrase', 'smpush-plugin-lang');
          if ($err == 111)
            $errstr .= __(' - Contact your host to enable outgoing ports', 'smpush-plugin-lang');
          $applelog = __('Failed to connect', 'smpush-plugin-lang').": $err $errstr".PHP_EOL;
        }
        else {
          $message = html_entity_decode(preg_replace("/U\+([0-9A-F]{4})/", "&#x\\1;", $message), ENT_NOQUOTES, 'UTF-8');
          $body['aps'] = array('alert' => $message, 'sound' => 'default');
          $payload = json_encode($body, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
          @$msg = chr(0).pack('n', 32).pack('H*', $_POST['ios_token']).pack('n', strlen($payload)).$payload;
          $resj = fwrite($fp, $msg, strlen($msg));
          fclose($fp);
          $applelog = __('Connected successfully with Apple server', 'smpush-plugin-lang')."\n".__('If you did not receive any message please check again your certification file and mobile code', 'smpush-plugin-lang');
        }
      }
      if (!empty($_POST['android_token'])) {
        if (function_exists('curl_init')) {
          $url = 'https://fcm.googleapis.com/fcm/send';
          if (self::$apisetting['android_titanium_payload'] == 1) {
            $data = array();
            $data['payload']['android']['alert'] = $message;
          }
          elseif (self::$apisetting['android_corona_payload'] == 1) {
            $data = array();
            $data['alert'] = $message;
          }
          elseif(self::$apisetting['android_fcm_msg'] == 1){
            $data = array('message' => $message);
            $notification = array();
            $notification['body'] = $message;
            $notification['title'] = self::$apisetting['android_title'];
          }
          else {
            $data = array('message' => $message);
          }
          $fields = array('registration_ids' => array($_POST['android_token']), 'data' => $data);
          if(self::$apisetting['android_fcm_msg'] == 1){
            $fields['notification'] = $notification;
          }
          $headers = array('Authorization: key='.self::$apisetting['google_apikey'], 'Content-Type: application/json');
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0));
          if(defined('WP_PROXY_HOST')){
            curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
            curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
            if(defined('WP_PROXY_USERNAME')){
              curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
              curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
            }
          }
          $result = curl_exec($ch);
          if ($result === false) {
            $googlelog = 'Curl failed: '.curl_error($ch);
          } else {
            $googlelog = $result;
          }
          curl_close($ch);
        } else {
          $googlelog = __('CURL Library is not support in your host', 'smpush-plugin-lang');
        }
      }
      echo '<h3>'.__('Apple Response', 'smpush-plugin-lang').'</h3><p><pre class="smpush_pre">'.$applelog.'</pre></p><h3>'.__('Google Response', 'smpush-plugin-lang').'</h3><p><pre class="smpush_pre">'.$googlelog.'</pre></p>';
    } else {
      include(smpush_dir.'/pages/test_sending.php');
    }
  }

  public static function connections() {
    global $wpdb;
    self::load_jsplugins();
    $pageurl = admin_url().'admin.php?page=smpush_connections';
    if ($_POST) {
      extract($_POST);
      if (empty($title)) {
        self::jsonPrint(0, __('Field title is required.', 'smpush-plugin-lang'));
      }
      if ($type == 'remote') {
        if (function_exists('mysqli_connect')) {
          @$testlink = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
          if ($testlink->connect_errno) {
            self::jsonPrint(0, __('Could not connect with remote database error', 'smpush-plugin-lang').': '.$testlink->connect_error);
          }
        } else {
          @$testlink = mysql_connect($dbhost, $dbuser, $dbpass, $dbname);
          if (!$testlink) {
            self::jsonPrint(0, __('Could not connect with remote database error', 'smpush-plugin-lang').': '.mysql_error());
          }
        }
        @$pushdb = new wpdb($dbuser, '', $dbname, $dbhost);
      } else {
        $pushdb = $wpdb;
      }
      $pushdb->hide_errors();
      $_tbname = str_replace('{wp_prefix}', $wpdb->prefix, $tbname);
      $count = $pushdb->get_var("SELECT COUNT($id_name) FROM `$_tbname`");
      if ($count === null) {
        self::jsonPrint(0, __('Table or ID name column is wrong', 'smpush-plugin-lang'));
      }
      $test = $pushdb->get_row("SELECT COUNT($token_name) FROM `$_tbname` LIMIT 0,1");
      if ($test === null) {
        self::jsonPrint(0, __('Device Token column is wrong', 'smpush-plugin-lang'));
      }
      $test = $pushdb->get_row("SELECT COUNT($md5token_name) FROM `$_tbname` LIMIT 0,1");
      if ($test === null) {
        self::jsonPrint(0, __('MD5 Device Token column is wrong', 'smpush-plugin-lang'));
      }
      $test = $pushdb->get_row("SELECT COUNT($id_name) FROM `$_tbname` WHERE `$type_name`='$ios_name' OR `$type_name`='$iosfcm_name' OR `$type_name`='$android_name' OR `$type_name`='$wp_name' OR `$type_name`='$wp10_name' OR `$type_name`='$bb_name' OR `$type_name`='$chrome_name' OR `$type_name`='$safari_name' OR `$type_name`='$firefox_name' OR `$type_name`='$opera_name' OR `$type_name`='$edge_name' OR `$type_name`='$samsung_name' OR `$type_name`='$fbmsn_name' OR `$type_name`='$fbnotify_name' OR `$type_name`='$email_name' LIMIT 0,1");
      if ($test === null) {
        self::jsonPrint(0, __('Type column or Device type values is wrong', 'smpush-plugin-lang'));
      }
      if (!empty($info_name)) {
        $test = $pushdb->get_row("SELECT COUNT($info_name) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('Information column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $info_name = 'information';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$info_name` TINYTEXT NOT NULL");
      }
      if (!empty($active_name)) {
        $test = $pushdb->get_row("SELECT COUNT($active_name) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('Active column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $active_name = 'active';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$active_name` BOOLEAN NOT NULL");
        $pushdb->query("UPDATE `$_tbname` SET `$active_name`='1'");
      }
      if (!empty($latitude_name)) {
        $test = $pushdb->get_row("SELECT COUNT($latitude_name) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('Latitude column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $latitude_name = 'latitude';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$latitude_name` DECIMAL(10, 8) NOT NULL");
      }
      if (!empty($longitude_name)) {
        $test = $pushdb->get_row("SELECT COUNT($longitude_name) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('Longitude column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $longitude_name = 'longitude';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$longitude_name` DECIMAL(11, 8) NOT NULL");
      }
      if (!empty($gpstime_name)) {
        $test = $pushdb->get_row("SELECT COUNT($gpstime_name) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('GPS update time column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $gpstime_name = 'gps_time_update';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$gpstime_name` VARCHAR(15) NOT NULL");
      }
      if (!empty($geotimeout_name)) {
        $test = $pushdb->get_row("SELECT COUNT($geotimeout_name) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('GPS timeout column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $geotimeout_name = 'geotimeout_name';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$geotimeout_name` VARCHAR(15) NOT NULL");
      }
      $test = $pushdb->get_row("SELECT COUNT(`userid`) FROM `$_tbname` LIMIT 0,1");
      if ($test === null) {
        $pushdb->query("ALTER TABLE `$_tbname` ADD `userid` INT NOT NULL");
      }
      $test = $pushdb->get_row("SELECT COUNT(`timepost`) FROM `$_tbname` LIMIT 0,1");
      if ($test === null) {
        $pushdb->query("ALTER TABLE `$_tbname` ADD `timepost` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
      }
      if (!empty($postdate)) {
        $test = $pushdb->get_row("SELECT COUNT($postdate) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('Added time column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $postdate = 'timepost';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$postdate` VARCHAR(15) NOT NULL");
      }
      if (!empty($counter_name)) {
        $test = $pushdb->get_row("SELECT COUNT($counter_name) FROM `$_tbname` LIMIT 0,1");
        if ($test === null) {
          self::jsonPrint(0, __('Added counter column name is wrong', 'smpush-plugin-lang'));
        }
      } else {
        $counter_name = 'counter';
        $pushdb->query("ALTER TABLE `$_tbname` ADD `$counter_name` SMALLINT NOT NULL");
      }
      $test = $pushdb->get_row("SELECT COUNT(`receive_again_at`) FROM `$_tbname` LIMIT 0,1");
      if ($test === null) {
        $pushdb->query("ALTER TABLE `$_tbname` ADD `receive_again_at` VARCHAR(15) NOT NULL DEFAULT '0'");
      }
      $data = array(
      'title' => $title,
      'description' => $description,
      'dbtype' => $type,
      'dbhost' => $dbhost,
      'dbname' => $dbname,
      'dbuser' => $dbuser,
      'dbpass' => $dbpass,
      'tbname' => $tbname,
      'id_name' => $id_name,
      'token_name' => $token_name,
      'md5token_name' => $md5token_name,
      'type_name' => $type_name,
      'ios_name' => $ios_name,
      'iosfcm_name' => $iosfcm_name,
      'android_name' => $android_name,
      'wp_name' => $wp_name,
      'wp10_name' => $wp10_name,
      'bb_name' => $bb_name,
      'chrome_name' => $chrome_name,
      'firefox_name' => $firefox_name,
      'safari_name' => $safari_name,
      'opera_name' => $opera_name,
      'edge_name' => $edge_name,
      'samsung_name' => $samsung_name,
      'fbmsn_name' => $fbmsn_name,
      'fbnotify_name' => $fbnotify_name,
      'email_name' => $email_name,
      'info_name' => $info_name,
      'active_name' => $active_name,
      'latitude_name' => $latitude_name,
      'longitude_name' => $longitude_name,
      'gpstime_name' => $gpstime_name,
      'geotimeout_name' => $geotimeout_name,
      'counter_name' => $counter_name,
      'postdate' => $postdate,
      'counter' => $count
      );
      if ($id > 0) {
        $wpdb->update($wpdb->prefix.'push_connection', $data, array('id' => $id));
      } else {
        $wpdb->insert($wpdb->prefix.'push_connection', $data);
      }
      echo 1;
      exit;
    } elseif (isset($_GET['delete'])) {
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_connection WHERE id='$_GET[id]'");
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE connection_id='$_GET[id]'");
      wp_redirect($pageurl);
    } elseif (isset($_GET['id'])) {
      if ($_GET['id'] == -1) {
        $connection = array('id' => 0, 'title' => '', 'description' => '', 'dbtype' => '', 'dbhost' => '', 'dbname' => '', 'dbuser' => '', 'dbpass' => '', 'tbname' => '', 'token_name' => '', 'md5token_name' => '', 'type_name' => '', 'ios_name' => '', 'iosfcm_name' => '', 'android_name' => '', 'bb_name' => '', 'wp_name' => '', 'wp10_name' => '', 'chrome_name' => '', 'safari_name' => '', 'firefox_name' => '', 'opera_name' => '', 'edge_name' => '', 'samsung_name' => '', 'fbmsn_name' => '', 'fbnotify_name' => '', 'email_name' => '', 'id_name' => '', 'info_name' => '', 'active_name' => '', 'counter_name' => '', 'latitude_name' => '', 'longitude_name' => '', 'gpstime_name' => '', 'geotimeout_name' => '');
      } else {
        $connection = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_connection WHERE id='$_GET[id]'", 'ARRAY_A');
        $connection = array_map('stripslashes', $connection);
      }
      include(smpush_dir.'/pages/connection_form.php');
      exit;
    } else {
      $connections = $wpdb->get_results("SELECT id,title,description,counter FROM ".$wpdb->prefix."push_connection ORDER BY id DESC");
      include(smpush_dir.'/pages/connection_manage.php');
    }
  }

}