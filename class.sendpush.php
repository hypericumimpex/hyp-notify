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

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class smpush_sendpush extends smpush_controller {

  public static $cronSendOperation = false;
  
  const TIME_BINARY_SIZE = 4;
  const TOKEN_LENGTH_BINARY_SIZE = 2;
  const DEVICE_BINARY_SIZE = 32;
  const ERROR_RESPONSE_SIZE = 6;
  const ERROR_RESPONSE_COMMAND = 8;
  const STATUS_CODE_INTERNAL_ERROR = 999;
  
  protected static $_aErrorResponseMessages = array(
  0 => 'No errors encountered',
  1 => 'Processing error',
  2 => 'Missing device token',
  3 => 'Missing topic',
  4 => 'Missing payload',
  5 => 'Invalid token size',
  6 => 'Invalid topic size',
  7 => 'Invalid payload size',
  8 => 'Invalid token',
  self::STATUS_CODE_INTERNAL_ERROR => 'Internal error'
  );
  
  protected static $apnsErrors = array(
  'BadCollapseId' => 'The collapse identifier exceeds the maximum allowed size',
  'BadDeviceToken' => 'The specified device token was bad. Verify that the request contains a valid token and that the token matches the environment.',
  'BadExpirationDate' => 'The apns-expiration value is bad.',
  'BadMessageId' => 'The apns-id value is bad.',
  'BadPriority' => 'The apns-priority value is bad.',
  'BadTopic' => 'The APP ID is invalid. Please enter the correct APP ID in your application settings.',
  'DeviceTokenNotForTopic' => 'The device token does not match the specified topic.',
  'DuplicateHeaders' => 'One or more headers were repeated.',
  'IdleTimeout' => 'Idle time out.',
  'MissingDeviceToken' => 'The device token is not specified in the request :path. Verify that the :path header contains the device token.',
  'MissingTopic' => 'The APP ID is invalid. Please enter the correct APP ID in your application settings.',
  'PayloadEmpty' => 'The message payload was empty',
  'TopicDisallowed' => 'Pushing to this topic is not allowed',
  'BadCertificate' => 'The certificate or password phrase is wrong. Please resubmit the right info in the application settings.',
  'BadCertificateEnvironment' => 'The client certificate is for the wrong environment. Enbale/Disable the sandbox option in the application settings.',
  'ExpiredProviderToken' => 'The provider token is stale and a new token should be generated',
  'Forbidden' => 'The specified action is not allowed',
  'InvalidProviderToken' => 'The provider token is not valid or the token signature could not be verified',
  'MissingProviderToken' => 'No provider certificate was used to connect to APNs and Authorization header was missing or no provider token was specified',
  'BadPath' => 'The request contained a bad :path value',
  'MethodNotAllowed' => 'The specified :method was not POST',
  'Unregistered' => 'The device token is inactive for the specified topic.',
  'PayloadTooLarge' => 'The message payload was too large. Please minify your message text or custom payloads.',
  'TooManyProviderTokenUpdates' => 'The provider token is being updated too often',
  'TooManyRequests' => 'Too many requests were made consecutively to the same device token',
  'InternalServerError' => 'An internal server error occurred',
  'ServiceUnavailable' => 'The service is unavailable',
  'Shutdown' => 'The server is shutting down'
  );
  
  protected static $sendoptions = array(
  'msgid' => '',
  'message' => '',
  'iostestmode' => 0,
  'feedback' => 1,
  'expire' => 0,
  'ios_slide' => '',
  'ios_badge' => 0,
  'ios_sound' => '',
  'ios_cavailable' => 0,
  'ios_launchimg' => '',
  'android_title' => '',
  'android_icon' => '',
  'android_sound' => '',
  'wp10_img' => '',
  'extra_type' => '',
  'extravalue' => '',
  'and_extra_type' => '',
  'and_extravalue' => '',
  'wp_extra_type' => '',
  'wp_extravalue' => '',
  'wp10_extra_type' => '',
  'wp10_extravalue' => '',
  'bb_extra_type' => '',
  'bb_extravalue' => '',
  'fbmsn_subject' => '',
  'fbmsn_message' => '',
  'fbmsn_link' => '',
  'fbmsn_image' => '',
  'fbmsn_button' => '',
  'fbnotify_message' => '',
  'fbnotify_openaction' => 'outside',
  'fbnotify_link' => '',
  'email' => '',
  'email_subject' => '',
  'email_fname' => '',
  'email_sender' => '',
  );

  private static $wpurl;
  private static $fbattachment;

  public function __construct() {
    parent::__construct();
    @set_time_limit(0);
    @ini_set('log_errors', 1);
    @ini_set('display_errors', 0);
    @ini_set('error_log', smpush_dir.'/cron_log.log');
  }
  
  private static function getMessagData($msgid) {
    global $wpdb;
    self::$data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_archive WHERE id='$msgid'", ARRAY_A);
    self::$data['platforms'] = json_decode(self::$data['platforms'], true);
    self::$data['options'] = unserialize(self::$data['options']);
    self::$data['usergroups'] = (!empty(self::$data['options']['usergroups']))? self::$data['options']['usergroups'] : array();
    self::$data['emailgroups'] = (!empty(self::$data['options']['emailgroups']))? self::$data['options']['emailgroups'] : array();
    if(!empty(self::$data['options']['email'])){
      self::$data['emailtemplate'] = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_newsletter_templates WHERE msgid='$msgid'");
    }
    if(self::$data['options']['extra_type'] != 'normal'){
      $fields = json_decode(self::$data['options']['extravalue'], true);
      if(!empty($fields)){
        foreach($fields as $key => $value){
          self::$data['key'][] = $key;
          self::$data['value'][] = $value;
        }
      }
    }
    if(self::$data['options']['and_extra_type'] != 'normal'){
      $fields = json_decode(self::$data['options']['and_extravalue'], true);
      if(!empty($fields)){
        foreach($fields as $key => $value){
          self::$data['and_key'][] = $key;
          self::$data['and_value'][] = $value;
        }
      }
    }
    if(self::$data['options']['wp_extra_type'] != 'normal'){
      $fields = json_decode(self::$data['options']['wp_extravalue'], true);
      if(is_array($fields)){
        foreach($fields as $key => $value){
          self::$data['wp_key'][] = $key;
          self::$data['wp_value'][] = $value;
        }
      }
    }
    if(self::$data['options']['wp10_extra_type'] != 'normal'){
      $fields = json_decode(self::$data['options']['wp10_extravalue'], true);
      if(is_array($fields)){
        foreach($fields as $key => $value){
          self::$data['wp10_key'][] = $key;
          self::$data['wp10_value'][] = $value;
        }
      }
    }
    if(!empty(self::$data['options']['wp10_img'])){
      self::$data['wp10_img'] = self::$data['options']['wp10_img'];
    }
  }

  private static function archiveMsgLog($message, $sendtime, $sendtype, $options, $desktop_notification = array('chrome','safari','firefox','opera','edge','samsung'), $msgid=false) {
    global $wpdb;
    if(!empty($desktop_notification)){
      $desktop_notification = implode(',', $desktop_notification);
    }
    else{
      $desktop_notification = '';
    }
    $data = array();
    $data['message'] = $message;
    if(!empty($options['platforms'])){
      $data['platforms'] = json_encode($options['platforms']);
    }
    if(!empty($options['emailjson'])){
      $emailjson = $options['emailjson'];
      unset($options['emailjson']);
    }
    if(!empty($options['name'])){
      $data['name'] = $options['name'];
    }
    if(!empty($sendtime) && is_numeric($sendtime)){
      $data['starttime'] = gmdate('Y-m-d H:i:s', $sendtime);
    }
    elseif(!empty($sendtime)){
      $data['starttime'] = $sendtime;
    }
    else{
      $data['starttime'] = gmdate('Y-m-d H:i:s', current_time('timestamp'));
    }
    if(isset($options['gps_expire_time'])){
      $data['gps_expire_time'] = $options['gps_expire_time'];
    }
    if(!empty($options['latitude'])){
      $data['latitude'] = $options['latitude'];
    }
    if(!empty($options['longitude'])){
      $data['longitude'] = $options['longitude'];
    }
    if(isset($options['radius'])){
      $data['radius'] = $options['radius'];
    }
    if(isset($options['repeat_interval'])){
      $data['repeat_interval'] = $options['repeat_interval'];
    }
    if(isset($options['repeat_age'])){
      $data['repeat_age'] = $options['repeat_age'];
    }
    if(isset($options['status'])){
      $data['status'] = $options['status'];
    }
    if(isset($options['processed'])){
      $data['processed'] = $options['processed'];
    }
    $data['send_type'] = $sendtype;
    $data['desktop'] = $desktop_notification;
    
    if(empty($options['ios_badge']) && !empty(self::$apisetting['ios_badge'])){
      $options['ios_badge'] = self::$apisetting['ios_badge'];
    }
    if(empty($options['ios_launchimg']) && !empty(self::$apisetting['ios_launch'])){
      $options['ios_launchimg'] = self::$apisetting['ios_launch'];
    }
    if(empty($options['ios_sound']) && !empty(self::$apisetting['ios_sound'])){
      $options['ios_sound'] = self::$apisetting['ios_sound'];
    }
    if(empty($options['android_title']) && !empty(self::$apisetting['android_title'])){
      $options['android_title'] = self::$apisetting['android_title'];
    }
    if(empty($options['android_icon']) && !empty(self::$apisetting['android_icon'])){
      $options['android_icon'] = self::$apisetting['android_icon'];
    }
    if(empty($options['android_sound']) && !empty(self::$apisetting['android_sound'])){
      $options['android_sound'] = self::$apisetting['android_sound'];
    }
    if(empty($options['desktop_icon']) && !empty(self::$apisetting['desktop_deficon'])){
      $options['desktop_icon'] = self::$apisetting['desktop_deficon'];
    }
    if(empty($options['desktop_title']) && !empty(self::$apisetting['desktop_title'])){
      $options['desktop_title'] = self::$apisetting['desktop_title'];
    }
    elseif(empty($options['desktop_title'])){
      $options['desktop_title'] = get_bloginfo('name');
    }

    $data['options'] = serialize($options);
    if($msgid === false){
      $wpdb->insert($wpdb->prefix.'push_archive', $data);
      $msgid = $wpdb->insert_id;
      if(!empty($emailjson)){
        $wpdb->insert($wpdb->prefix.'push_newsletter_templates', array('msgid' => $msgid, 'template' => $emailjson, 'static' => 0));
      }
    }
    else{
      $wpdb->update($wpdb->prefix.'push_archive', $data, array('id' => $msgid));
      if(!empty($emailjson)){
        $jsonemailid = $wpdb->get_var("SELECT id FROM ".$wpdb->prefix."push_newsletter_templates WHERE msgid='$msgid'");
        if($jsonemailid){
          $wpdb->update($wpdb->prefix.'push_newsletter_templates', array('template' => $emailjson), array('id' => $jsonemailid));
        }
        else{
          $wpdb->insert($wpdb->prefix.'push_newsletter_templates', array('msgid' => $msgid, 'template' => $emailjson, 'static' => 0));
        }
      }
    }
    return $msgid;
  }
  
  public static function gpsRealtime() {
    if(isset($_GET['smpushlive'])){
      if(!empty($_GET['lastupdate'])){
        $lasttime = $_GET['lastupdate'];
      }
      else{
        $lasttime = current_time('timestamp')-3600;
      }
      $devices = self::$pushdb->get_results(self::parse_query("SELECT {type_name} AS devicetype,{info_name} AS deviceinfo,{latitude_name} AS latitude,{longitude_name} AS longitude FROM {tbname} WHERE {gpstime_name}>'$lasttime' AND {active_name}='1'"), 'ARRAY_A');
      if($devices){
        $devices[0]['lastupdate'] = (string)current_time('timestamp');
        echo json_encode($devices);
      }
      else{
        echo current_time('timestamp');
      }
      exit;
    }
    self::loadpage('realtime_gps', 1, array());
  }

  public static function SendPushMessage($device_token, $device_type, $message, $sendsetting = array(), $sendtime = 0) {
    self::$sendoptions['message'] = $message;
    self::$sendoptions['status'] = 1;
    self::$sendoptions = array_merge(self::$sendoptions, $sendsetting);
    self::$sendoptions['query'] = "SELECT {id_name} AS id, {tbname}.{token_name} AS device_token,{tbname}.{type_name} AS device_type,{tbname}.{counter_name} AS counter,{tbname}.userid FROM {tbname} WHERE {tbname}.{md5token_name}='".md5($device_token)."' AND {tbname}.{type_name}='$device_type'";
    
    if ($sendtime == 0) {
      $sendtime = current_time('timestamp');
    }
    
    self::archiveMsgLog($message, $sendtime, 'custom', self::$sendoptions);
  }

  public static function SendCronPush($ids, $message, $extravalue, $gettype = 'userid', $sendsetting = array(), $sendtime = 0, $channel_filter_ids=false, $gps_loc_filter=false, $template_id=false) {
    global $wpdb;
    $inner = $select = $where = '';
    $send_type = 'custom';
    
    if(!empty($channel_filter_ids)){
      $defconid = self::$apisetting['def_connection'];
      $tablename = $wpdb->prefix.'push_relation';
      $inner = "INNER JOIN $tablename ON($tablename.channel_id IN($channel_filter_ids) AND (($tablename.connection_id='$defconid' AND $tablename.token_id={tbname}.{id_name}) OR ({tbname}.userid>0 AND {tbname}.userid=$tablename.userid)))";
    }
    
    if(!empty($gps_loc_filter['latitude']) AND ! empty($gps_loc_filter['longitude']) AND ! empty($gps_loc_filter['radius'])) {
      $select = ",(3959*acos(cos(radians($gps_loc_filter[latitude]))*cos(radians({tbname}.{latitude_name}))*cos(radians({tbname}.{longitude_name})-radians($gps_loc_filter[longitude]))+sin(radians($gps_loc_filter[latitude]))*sin(radians({tbname}.{latitude_name})))) AS geodistance";
      $where = " AND {tbname}.{geotimeout_name}<".(current_time('timestamp')-3600);
      if(!empty($gps_loc_filter['gps_expire'])){
        $where .= " AND {tbname}.{gpstime_name}>".(current_time('timestamp')-($gps_loc_filter['gps_expire']*3600));
      }
      $order = 'HAVING geodistance<='.$gps_loc_filter['radius'].' ORDER BY {tbname}.{id_name} ASC';
    }
    else{
      $order = 'GROUP BY {tbname}.{id_name}';
    }
    
    if ($ids == 'all') {
      $sendsetting['query'] = "SELECT {id_name} AS id, {tbname}.{token_name} AS device_token,{tbname}.{type_name} AS device_type,{tbname}.{counter_name} AS counter,{tbname}.userid $select FROM {tbname} $inner WHERE {tbname}.{active_name}='1' AND {tbname}.receive_again_at<CURRENT_TIME_NOW $where $order";
    }
    elseif ($gettype == 'userid') {
      if(is_array($ids)){
        $ids = implode(',', $ids);
      }
      $sendsetting['query'] = "SELECT {id_name} AS id, {tbname}.{token_name} AS device_token,{tbname}.{type_name} AS device_type,{tbname}.{counter_name} AS counter,{tbname}.userid $select FROM {tbname} $inner WHERE {tbname}.userid IN($ids) AND {tbname}.{active_name}='1' AND {tbname}.receive_again_at<CURRENT_TIME_NOW $where $order";
    }
    elseif ($gettype == 'tokenid') {
      $ids = implode(',', $ids);
      $sendsetting['query'] = "SELECT {id_name} AS id, {tbname}.{token_name} AS device_token,{tbname}.{type_name} AS device_type,{tbname}.{counter_name} AS counter,{tbname}.userid $select FROM {tbname} $inner WHERE {tbname}.{id_name} IN($ids) AND {tbname}.{active_name}='1' AND {tbname}.receive_again_at<CURRENT_TIME_NOW $where $order";
    }
    elseif ($gettype == 'channel') {
      $defconid = self::$apisetting['def_connection'];
      $tablename = $wpdb->prefix.'push_relation';
      $sendsetting['query'] = "SELECT {id_name} AS id, {tbname}.{token_name} AS device_token,{tbname}.{type_name} AS device_type,{tbname}.{counter_name} AS counter,{tbname}.userid $select FROM $tablename
      INNER JOIN {tbname} ON(({tbname}.{id_name}=$tablename.token_id OR ({tbname}.userid>0 AND {tbname}.userid=$tablename.userid)) AND {tbname}.{active_name}='1' AND {tbname}.receive_again_at<CURRENT_TIME_NOW)
      WHERE $tablename.channel_id IN($ids) AND ($tablename.connection_id='$defconid' OR $tablename.connection_id='0') $where $order";
    }
    else{
      $send_type = $gettype;
    }
    
    if(empty($ids)){
      return 0;
    }
    
    
    if(! is_array($message)){
      $message = array(
      'web' => strip_tags($message),
      'fbmsn' => strip_tags($message),
      'fbnotify' => strip_tags($message),
      'email' => $message,
      );
    }
    $sendsetting['message'] = $message['web'];

    if(!empty($message['subject'])){
      $sendsetting['desktop_title'] = $message['subject'];
    }

    if(is_multisite()){
      $mu_blog_path = $wpdb->get_var("SELECT `path` FROM $wpdb->blogs WHERE blog_id='$wpdb->blogid'");
      if (is_array($extravalue)) {
        $extravalue['blog_path'] = $mu_blog_path;
      }
      else {
        $extravalue = array('relatedvalue' => $extravalue, 'blog_path' => $mu_blog_path);
      }
    }

    if (!empty($extravalue)) {
      if (is_array($extravalue)) {
        $sendsetting['extra_type'] = 'json';
        $sendsetting['extravalue'] = json_encode($extravalue, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
      }
      else {
        $sendsetting['extra_type'] = 'normal';
        $sendsetting['extravalue'] = $extravalue;
      }
    }
    if ($sendtime == 0) {
      $sendtime = current_time('timestamp');
    }
    
    if(!empty($template_id)){
      $template = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_archive WHERE id='$template_id' AND status='1'", ARRAY_A);
      if(!empty($template)){
        $template['options'] = unserialize($template['options']);
        $mergedOptions = array_merge($template['options'], $sendsetting);
        $mergedOptions['name'] = '';
        $mergedOptions['message'] = $message['web'];
        $mergedOptions['desktop_icon'] = (empty($sendsetting['desktop_icon']))? $mergedOptions['desktop_icon'] : $sendsetting['desktop_icon'];
        $mergedOptions['desktop_link'] = (empty($sendsetting['desktop_link']))? $mergedOptions['desktop_link'] : $sendsetting['desktop_link'];
        $mergedOptions['fbnotify_openaction'] = (empty($mergedOptions['fbnotify_openaction']))? 'outside' : $mergedOptions['fbnotify_openaction'];
        $mergedOptions['fbnotify_message'] = (empty($mergedOptions['fbnotify_message']))? $message['fbnotify'] : $mergedOptions['fbnotify_message'];
        $mergedOptions['fbnotify_link'] = (empty($mergedOptions['desktop_link']))? $sendsetting['desktop_link'] : $mergedOptions['desktop_link'];
        $mergedOptions['fbmsn_subject'] = (empty($mergedOptions['fbmsn_subject']))? $sendsetting['desktop_title'] : $mergedOptions['fbmsn_subject'];
        $mergedOptions['fbmsn_message'] = (empty($mergedOptions['fbmsn_message']))? $message['fbmsn'] : $mergedOptions['fbmsn_message'];
        $mergedOptions['fbmsn_image'] = (empty($mergedOptions['fbmsn_image']))? $sendsetting['desktop_icon'] : $mergedOptions['fbmsn_image'];
        $mergedOptions['fbmsn_button'] = (empty($mergedOptions['fbmsn_button']))? __('Check Out', 'smpush-plugin-lang') : $mergedOptions['fbmsn_button'];
        $mergedOptions['fbmsn_link'] = (empty($mergedOptions['fbmsn_link']))? $sendsetting['desktop_link'] : $mergedOptions['fbmsn_link'];
        $mergedOptions['email'] = (!empty($message['email']))? $message['email'] : $mergedOptions['email'];
        $mergedOptions['email_subject'] = (empty($mergedOptions['email_subject']))? $sendsetting['desktop_title'] : $mergedOptions['email_subject'];
        $mergedOptions['email_fname'] = (empty($mergedOptions['email_fname']))? get_bloginfo('name') : $mergedOptions['email_fname'];
        $mergedOptions['email_sender'] = (empty($mergedOptions['email_sender']))? get_option('admin_email') : $mergedOptions['email_sender'];
        if($sendsetting['extra_type'] == 'json' && !empty($template['options']['extravalue']) && $template['options']['extra_type'] == 'json'){
          $mergedOptions['extravalue'] = array_merge(json_decode($sendsetting['extravalue'], true), json_decode($template['options']['extravalue'], true));
          $mergedOptions['extravalue'] = json_encode($mergedOptions['extravalue'], defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
        }
        if(!empty($sendsetting['desktop_icon'])){
          $mergedOptions['wp_extra_type'] = 'json';
          $mergedOptions['wp_extravalue'] = json_encode(array('wp_image' => $sendsetting['desktop_icon']), defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
          $mergedOptions['wp10_img'] = $sendsetting['desktop_icon'];
        }
        self::$sendoptions = $mergedOptions;
      }
    }
    else{
      self::$sendoptions = array_merge(self::$sendoptions, $sendsetting);
      self::$sendoptions['fbnotify_message'] = $message['fbnotify'];
      self::$sendoptions['fbnotify_link'] = $sendsetting['desktop_link'];
      self::$sendoptions['fbnotify_openaction'] = 'outside';
      self::$sendoptions['fbmsn_message'] = $message['fbmsn'];
      self::$sendoptions['fbmsn_subject'] = $sendsetting['desktop_title'];
      self::$sendoptions['fbmsn_image'] = (empty($sendsetting['desktop_icon']))? '' : $sendsetting['desktop_icon'];
      self::$sendoptions['fbmsn_button'] = __('Check Out', 'smpush-plugin-lang');
      self::$sendoptions['fbmsn_link'] = $sendsetting['desktop_link'];
      self::$sendoptions['email'] = $message['email'];
      self::$sendoptions['email_subject'] = $sendsetting['desktop_title'];
      self::$sendoptions['email_fname'] = get_bloginfo('name');
      self::$sendoptions['email_sender'] = get_option('admin_email');
      if(!empty($sendsetting['desktop_icon'])){
        self::$sendoptions['wp_extra_type'] = 'json';
        self::$sendoptions['wp_extravalue'] = json_encode(array('wp_image' => $sendsetting['desktop_icon']), defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
        self::$sendoptions['wp10_img'] = $sendsetting['desktop_icon'];
      }
    }
    self::$sendoptions['status'] = 1;

    if(!empty($sendsetting['desktop_link'])){
      self::$sendoptions['and_extra_type'] = 'json';
      self::$sendoptions['and_extravalue'] = json_encode(array('link' => $sendsetting['desktop_link']), defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    }
    
    $msgid = self::archiveMsgLog($message['web'], $sendtime, 'custom', self::$sendoptions);
    return $msgid;
  }

  public static function activateTokens() {
    self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='1'"));
    self::update_counters();
    wp_redirect(admin_url().'admin.php?page=smpush_send_notification');
  }

  public static function smpush_cancelqueue() {
    global $wpdb;
    $wpdb->query("TRUNCATE `".$wpdb->prefix."push_queue`");
    delete_transient('smpush_post');
    delete_transient('smpush_resum');
    update_option('smpush_instant_send', array());
    self::updateStats();
    wp_redirect(admin_url().'admin.php?page=smpush_archive');
  }

  public static function send_process($resumsend, $allcount = 0, $increration = 0) {
    self::load_jsplugins();
    wp_enqueue_style('smpush-progbarstyle');
    wp_enqueue_script('smpush-progbarscript');
    include (smpush_dir.'/pages/send_process.php');
  }

  public static function send_notification() {
    global $wpdb;
    $resume_mode = false;
    if (!empty($_GET['calculate'])) {
      $stats = self::calculateDevices();
      echo json_encode($stats);
      exit;
    }
    if (!empty($_GET['savehistory'])) {
      update_option('smpush_history', $_POST);
      echo 1;
      exit;
    }
    if (!empty($_GET['clearhistory'])) {
      update_option('smpush_history', '');
      echo 1;
      exit;
    }
    if (get_transient('smpush_resum') !== false && !isset($_GET['lastid'], $_GET['increration'])) {
      $_POST = get_transient('smpush_post');
      $resume_mode = true;
    }
    if ($_POST) {
      if (isset($_POST['message'])) {
        $wpdb->query("TRUNCATE `".$wpdb->prefix."push_queue`");
        self::$sendoptions['message'] = $_POST['message'];
        $desktop_notification = array();
        $where = $joinsql = '';
        if(!empty($_POST['platforms'])){
          $_POST['platforms'] = array_flip($_POST['platforms']);
        }
        if(empty($_POST['platforms']) || (isset($_POST['platforms']['ios']) && isset($_POST['platforms']['android']) && isset($_POST['platforms']['wp']) && isset($_POST['platforms']['bb'])
           && isset($_POST['platforms']['chrome']) && isset($_POST['platforms']['safari']) && isset($_POST['platforms']['firefox']) && isset($_POST['platforms']['opera']) && isset($_POST['platforms']['samsung'])
           && isset($_POST['platforms']['fbmsn']) && isset($_POST['platforms']['fbnotify']) && isset($_POST['platforms']['email']) && isset($_POST['platforms']['edge']) && isset($_POST['platforms']['iosfcm']))){
          $_POST['platforms'] = array('all');
          $desktop_notification = array('chrome','safari','firefox','opera','edge','samsung');
          $where = '';
        }
        elseif(!empty($_POST['platforms'])){
          $_POST['platforms'] = array_flip($_POST['platforms']);
        }
        if (in_array('ios', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{ios_name}'";
        }
        if (in_array('iosfcm', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{iosfcm_name}'";
        }
        if (in_array('android', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{android_name}'";
        }
        if (in_array('wp', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{wp_name}'";
        }
        if (in_array('wp10', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{wp10_name}'";
        }
        if (in_array('bb', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{bb_name}'";
        }
        if (in_array('chrome', $_POST['platforms'])) {
          array_push($desktop_notification, 'chrome');
          $where .= " OR {tbname}.{type_name}='{chrome_name}'";
        }
        if (in_array('safari', $_POST['platforms'])) {
          array_push($desktop_notification, 'safari');
          $where .= " OR {tbname}.{type_name}='{safari_name}'";
        }
        if (in_array('firefox', $_POST['platforms'])) {
          array_push($desktop_notification, 'firefox');
          $where .= " OR {tbname}.{type_name}='{firefox_name}'";
        }
        if (in_array('opera', $_POST['platforms'])) {
          array_push($desktop_notification, 'opera');
          $where .= " OR {tbname}.{type_name}='{opera_name}'";
        }
        if (in_array('edge', $_POST['platforms'])) {
          array_push($desktop_notification, 'edge');
          $where .= " OR {tbname}.{type_name}='{edge_name}'";
        }
        if (in_array('samsung', $_POST['platforms'])) {
          array_push($desktop_notification, 'samsung');
          $where .= " OR {tbname}.{type_name}='{samsung_name}'";
        }
        if (in_array('fbmsn', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{fbmsn_name}'";
        }
        if (in_array('fbnotify', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{fbnotify_name}'";
        }
        if (in_array('email', $_POST['platforms'])) {
          $where .= " OR {tbname}.{type_name}='{email_name}'";
        }
        if(!empty($where)){
          $where = ' AND ('.ltrim($where, ' OR ').') ';
        }
        if(!empty($_POST['usergroups'])){
          $usergroupsql = '';
          foreach($_POST['usergroups'] as $user_role){
            $usergroupsql .= 'OR '.$wpdb->usermeta.'.meta_value LIKE \'%'.$user_role.'%\'';
          }
          $usergroupsql = ltrim($usergroupsql, 'OR ');
          $joinsql .= "INNER JOIN $wpdb->usermeta ON($wpdb->usermeta.user_id={tbname}.userid AND $wpdb->usermeta.meta_key='".$wpdb->prefix."capabilities' AND ($usergroupsql))";
        }
        
        if (!empty($_POST['latitude']) AND ! empty($_POST['longitude']) AND ! empty($_POST['radius'])) {
          $select = ",(3959*acos(cos(radians($_POST[latitude]))*cos(radians({tbname}.{latitude_name}))*cos(radians({tbname}.{longitude_name})-radians($_POST[longitude]))+sin(radians($_POST[latitude]))*sin(radians({tbname}.{latitude_name})))) AS geodistance";
          if(!empty($_POST['gps_expire'])){
            $where .= " AND {tbname}.{gpstime_name}>".(current_time('timestamp')-($_POST['gps_expire']*3600));
          }
          $where .= " AND {tbname}.{geotimeout_name}<".(current_time('timestamp')-3600);
          $order = 'HAVING geodistance<='.$_POST['radius'].' ORDER BY {tbname}.{id_name} ASC';
        }
        else{
          $select = '';
          $order = 'ORDER BY {tbname}.{id_name} ASC';
        }
        if (!empty($_POST['inchannels_and']) OR ! empty($_POST['inchannels_or']) OR ! empty($_POST['notchannels_and']) OR ! empty($_POST['notchannels_or'])) {
          $defconid = self::$apisetting['def_connection'];
          $tablename = $wpdb->prefix.'push_relation';
          //do not forget to change in calculateDevices() if you will make any changes in this query
          $smpush_query = self::parse_query("SELECT {tbname}.{id_name} AS token_id,{tbname}.{token_name} AS device_token,{tbname}.{counter_name} AS counter,{tbname}.{type_name} AS device_type,{tbname}.userid,GROUP_CONCAT($tablename.`channel_id` SEPARATOR ',') AS channelids $select FROM {tbname}
          INNER JOIN $tablename ON(($tablename.token_id={tbname}.{id_name} AND $tablename.connection_id='$defconid') OR ({tbname}.userid>0 AND {tbname}.userid=$tablename.userid))
          $joinsql
          WHERE {tbname}.{active_name}='1' AND {tbname}.receive_again_at<".current_time('timestamp')." $where AND {tbname}.{id_name}>[lastid] GROUP BY {tbname}.{id_name} $order LIMIT 0,[limit]");
          $alltokens = $wpdb->get_var(self::parse_query("SELECT  COUNT(DISTINCT({tbname}.{id_name})) FROM {tbname}
          INNER JOIN $tablename ON(($tablename.token_id={tbname}.{id_name} AND $tablename.connection_id='$defconid') OR ({tbname}.userid>0 AND {tbname}.userid=$tablename.userid))
          WHERE {tbname}.{active_name}='1' AND {tbname}.receive_again_at<".current_time('timestamp')." $where"));
        }
        else {
          //do not forget to change in calculateDevices() if you will make any changes in this query
          $smpush_query = self::parse_query("SELECT {id_name} AS token_id,{token_name} AS device_token,{type_name} AS device_type,{tbname}.{counter_name} AS counter,{tbname}.userid $select FROM {tbname} $joinsql WHERE {active_name}='1' AND receive_again_at<".current_time('timestamp')." $where AND {id_name}>[lastid] $order LIMIT 0,[limit]");
          $alltokens = self::$pushdb->get_var(self::parse_query("SELECT COUNT({id_name}) FROM {tbname} WHERE {active_name}='1' AND receive_again_at<".current_time('timestamp')." $where"));
        }
        if ($alltokens === null) {
          wp_die('Please reconfig the default push notification database connection <a href="'.admin_url().'admin.php?page=smpush_connections">here</a>');
        }
        if(!empty($_POST['emailgroups'])){
          $usergroupsql = '';
          foreach($_POST['emailgroups'] as $user_role){
            $usergroupsql .= 'OR '.$wpdb->usermeta.'.meta_value LIKE \'%'.$user_role.'%\'';
          }
          $usergroupsql = ltrim($usergroupsql, 'OR ');
          $alltokens += $wpdb->get_var("SELECT COUNT($wpdb->users.ID) FROM $wpdb->users
          INNER JOIN $wpdb->usermeta ON($wpdb->usermeta.user_id=$wpdb->users.ID AND $wpdb->usermeta.meta_key='".$wpdb->prefix."capabilities' AND ($usergroupsql))");
        }
        //$feedback = (isset($_POST['feedback'])) ? 1 : 0;
        $_POST['feedback'] = 1;
        $feedback = 1;
        $iostestmode = self::$apisetting['ios_onebyone'];
        $_POST['feedback'] = $feedback;
        $_POST['iostestmode'] = $iostestmode;
        if ($_POST['extra_type'] == 'multi') {
          $json = array();
          foreach ($_POST['key'] as $loop => $key) {
            if (!empty($key) && !empty($_POST['value'][$loop])) {
              $json[$key] = $_POST['value'][$loop];
            }
          }
          if (empty($json)) {
            $_POST['extra'] = '';
            $_POST['extra_type'] = '';
          } else {
            $_POST['extra'] = json_encode($json, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
            $_POST['extra_type'] = 'json';
          }
        }
        if ($_POST['and_extra_type'] == 'multi') {
          $json = array();
          foreach ($_POST['and_key'] as $loop => $key) {
            if (!empty($key) && !empty($_POST['and_value'][$loop])) {
              $json[$key] = $_POST['and_value'][$loop];
            }
          }
          if (empty($json)) {
            $_POST['and_extra'] = '';
            $_POST['and_extra_type'] = '';
          } else {
            $_POST['and_extra'] = json_encode($json, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
            $_POST['and_extra_type'] = 'json';
          }
        }
        if ($_POST['wp_extra_type'] == 'multi') {
          $json = array();
          foreach ($_POST['wp_key'] as $loop => $key) {
            if (!empty($key) && !empty($_POST['wp_value'][$loop])) {
              $json[$key] = $_POST['wp_value'][$loop];
            }
          }
          if (empty($json)) {
            $_POST['wp_extra'] = '';
            $_POST['wp_extra_type'] = '';
          } else {
            $_POST['wp_extra'] = json_encode($json, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
            $_POST['wp_extra_type'] = 'json';
          }
        }
        if ($_POST['wp10_extra_type'] == 'multi') {
          $json = array();
          foreach ($_POST['wp10_key'] as $loop => $key) {
            if (!empty($key) && !empty($_POST['wp10_value'][$loop])) {
              $json[$key] = $_POST['wp10_value'][$loop];
            }
          }
          if (empty($json)) {
            $_POST['wp10_extra'] = '';
            $_POST['wp10_extra_type'] = '';
          } else {
            $_POST['wp10_extra'] = json_encode($json, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
            $_POST['wp10_extra_type'] = 'json';
          }
        }
        
        $actions = array();
        $accounter = 0;
        if(!empty($_POST['desktop_actions']['id'])){
          foreach($_POST['desktop_actions']['id'] as $key => $value){
            if(!empty($_POST['desktop_actions']['id'][$key]) && !empty($_POST['desktop_actions']['text'][$key]) && !empty($_POST['desktop_actions']['icon'][$key]) && !empty($_POST['desktop_actions']['link'][$key])){
              $actions['id'][$accounter] = $_POST['desktop_actions']['id'][$key];
              $actions['text'][$accounter] = $_POST['desktop_actions']['text'][$key];
              $actions['icon'][$accounter] = $_POST['desktop_actions']['icon'][$key];
              $actions['link'][$accounter] = $_POST['desktop_actions']['link'][$key];
              $accounter++;
            }
          }
        }
        
        $options = array(
        'platforms' => $_POST['platforms'],
        'name' => $_POST['name'],
        'message' => $_POST['message'],
        'iostestmode' => $_POST['iostestmode'],
        'feedback' => $_POST['feedback'],
        'expire' => (empty($_POST['expire']))?0:$_POST['expire'],
        'desktop_link' => $_POST['desktop_link'],
        'desktop_title' => $_POST['desktop_title'],
        'desktop_icon' => $_POST['desktop_icon'],
        'desktop_bigimage' => $_POST['desktop_bigimage'],
        'desktop_badge' => $_POST['desktop_badge'],
        'desktop_sound' => $_POST['desktop_sound'],
        'desktop_interaction' => $_POST['desktop_interaction'],
        'desktop_vibrate' => $_POST['desktop_vibrate'],
        'desktop_silent' => (isset($_POST['desktop_silent']))? 1 : 0,
        'desktop_dir' => $_POST['desktop_dir'],
        'desktop_actions' => $actions,
        'email' => $_POST['email'],
        'emailjson' => $_POST['emailjson'],
        'email_subject' => $_POST['email_subject'],
        'email_fname' => $_POST['email_fname'],
        'email_sender' => $_POST['email_sender'],
        'fbnotify_link' => $_POST['fbnotify_link'],
        'fbnotify_openaction' => $_POST['fbnotify_openaction'],
        'fbmsn_subject' => $_POST['fbmsn_subject'],
        'fbmsn_message' => $_POST['fbmsn_message'],
        'fbmsn_link' => $_POST['fbmsn_link'],
        'fbmsn_image' => $_POST['fbmsn_image'],
        'fbmsn_button' => $_POST['fbmsn_button'],
        'fbnotify_message' => $_POST['fbnotify_message'],
        'ios_slide' => $_POST['ios_slide'],
        'ios_badge' => $_POST['ios_badge'],
        'ios_sound' => $_POST['ios_sound'],
        'ios_cavailable' => $_POST['ios_cavailable'],
        'ios_launchimg' => $_POST['ios_launchimg'],
        'android_title' => $_POST['android_title'],
        'android_icon' => $_POST['android_icon'],
        'android_sound' => $_POST['android_sound'],
        'wp10_img' => $_POST['wp10_img'],
        'extra_type' => $_POST['extra_type'],
        'extravalue' => $_POST['extra'],
        'and_extra_type' => $_POST['and_extra_type'],
        'and_extravalue' => $_POST['and_extra'],
        'wp_extra_type' => $_POST['wp_extra_type'],
        'wp_extravalue' => $_POST['wp_extra'],
        'wp10_extra_type' => $_POST['wp10_extra_type'],
        'wp10_extravalue' => $_POST['wp10_extra'],
        'gps_expire_time' => (empty($_POST['gps_expire_time']))?0:$_POST['gps_expire_time'],
        'radius' => (empty($_POST['radius']))?0:$_POST['radius'],
        'status' => (isset($_POST['status']))?1:0,
        'inchannels_and' => (empty($_POST['inchannels_and']))?array():$_POST['inchannels_and'],
        'inchannels_or' => (empty($_POST['inchannels_or']))?array():$_POST['inchannels_or'],
        'notchannels_and' => (empty($_POST['notchannels_and']))?array():$_POST['notchannels_and'],
        'notchannels_or' => (empty($_POST['notchannels_or']))?array():$_POST['notchannels_or']
        );
        if(!empty($_POST['latitude']) AND !empty($_POST['longitude'])){
          $options['latitude'] = $_POST['latitude'];
          $options['longitude'] = $_POST['longitude'];
        }
        $options['sendtype'] = (isset($_POST['sendlive'])) ? 'live' : $_POST['send_type'];
        if(isset($_POST['rerun'])){
          $options['processed'] = 0;
        }
        if($_POST['send_type'] == 'time'){
          $options['sendtime'] = strtotime($_POST['send_time']);
          if(isset($_POST['send_repeatly'])){
            $options['repeat_interval'] = $_POST['repeat_interval'];
            $options['repeat_age'] = $_POST['repeat_age'];
          }
          else{
            $options['repeat_interval'] = 0;
            $options['repeat_age'] = '';
          }
        }
        else{
          $options['sendtime'] = gmdate('Y-m-d H:i:s', current_time('timestamp'));
        }
        if(!empty($_POST['usergroups'])){
          $options['usergroups'] = $_POST['usergroups'];
        }
        if(!empty($_POST['emailgroups'])){
          $options['emailgroups'] = $_POST['emailgroups'];
        }
        $options['query'] = $smpush_query;
        
        if(! $resume_mode && empty($_POST['id'])){
          $msgid = self::archiveMsgLog($options['message'], $options['sendtime'], $options['sendtype'], $options, $desktop_notification);
        }
        elseif(! $resume_mode && !empty($_POST['id'])){
          $msgid = $_POST['id'];
          self::archiveMsgLog($options['message'], $options['sendtime'], $options['sendtype'], $options, $desktop_notification, $_POST['id']);
        }
        if(! $resume_mode && $options['sendtype'] == 'live'){
          set_transient('smpush_post', $_POST, 43200);
          $handler_options = array(
          'token_counter' => 0,
          'lastid' => 0,
          'msgid' => $msgid,
          );
          update_option('smpush_instant_send', $handler_options);
          
          if (isset($_POST['feedback']) && (in_array('ios', $_POST['platforms']) OR in_array('all', $_POST['platforms']))) {
            $wpdb->insert($wpdb->prefix.'push_feedback', array('device_type' => 'ios', 'msgid' => $msgid));
          }
        }
                
        if($options['sendtype'] == 'live'){
          if ($alltokens == 0)
            $increration = 0;
          else
            $increration = ceil($alltokens / 20);
          self::send_process(false, $alltokens, $increration, $feedback);
        }
        else{
          echo '<script>window.location = "'.admin_url().'admin.php?page=smpush_campending"</script>';
        }
      }
      else {
        self::smpush_cancelqueue();
        echo '<script>window.location = "'.admin_url().'admin.php?page=smpush_send_notification"</script>';
      }
    }
    elseif (isset($_GET['lastid'], $_GET['increration'])) {
      $handler_options = get_option('smpush_instant_send');
      if (empty($_GET['lastid']) && !empty($handler_options['lastid'])) {
        $_GET['lastid'] = $handler_options['lastid'];
      }
      $message_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_archive WHERE id='$handler_options[msgid]'", ARRAY_A);
      $message_data['options'] = unserialize($message_data['options']);
      $smpush_query = $message_data['options']['query'];
      $options = $message_data['options'];
      $tokencounter = $handler_options['token_counter'];
      $query = str_replace(array('[lastid]', '[limit]'), array($_GET['lastid'], $_GET['increration']), $smpush_query);
      $tokens = self::$pushdb->get_results($query);
      if (!empty(self::$pushdb->last_error)) {
        self::jsonPrint(0, '<p class="error">'.__('Please reconfig the default push notification database connection', 'smpush-plugin-lang').'</p>');
      }
      if ($tokens) {
        if (!empty($options['inchannels_and']) OR ! empty($options['inchannels_or']) OR ! empty($options['notchannels_and']) OR ! empty($options['notchannels_or'])) {
          foreach ($tokens AS $token) {
            $lastid = $token->token_id;
            $token->channelids = explode(',', $token->channelids);
            //do not forget to change in calculateDevices() if you will make any changes in this query
            if (!empty($options['inchannels_and'])) {
              $intersect = array_intersect($token->channelids, $options['inchannels_and']);
              if (count($intersect) != count($options['inchannels_and'])) {
                continue;
              }
            }
            if (!empty($options['inchannels_or'])) {
              $bool = array_intersect($token->channelids, $options['inchannels_or']);
              if(empty($bool)){
                continue;
              }
            }
            if (!empty($options['notchannels_and'])) {
              $bool = array_intersect($token->channelids, $options['notchannels_and']);
              if(!empty($bool)){
                continue;
              }
            }
            if (!empty($options['notchannels_or'])) {
              $bool = array_diff($options['notchannels_or'], $token->channelids);
              if(empty($bool)){
                continue;
              }
            }
            if(!isset($token->userid)){
              $token->userid = $wpdb->get_var("SELECT userid FROM ".$wpdb->prefix."sm_push_tokens WHERE id='$token->token_id'");
            }
            if ($options['sendtype'] == 'live') {
              $wpdb->insert($wpdb->prefix.'push_queue', array('token_id' => $token->token_id, 'token' => $token->device_token, 'device_type' => $token->device_type, 'counter' => $token->counter));
            }
            else {
              $wpdb->insert($wpdb->prefix.'push_cron_queue', array('token_id' => $token->token_id, 'token' => $token->device_token, 'device_type' => $token->device_type, 'counter' => $token->counter, 'sendtime' => $message_data['starttime'], 'sendoptions' => $handler_options['msgid']));
            }
            if(!empty($token->userid)){
              $wpdb->insert($wpdb->prefix.'push_history', array('platform' => self::platformType($token->device_type), 'userid' => $token->userid, 'msgid' => $handler_options['msgid'], 'timepost' => $message_data['starttime']));
            }
            if (self::$apisetting['android_msg_counter'] == 1 && $token->device_type == 'android') {
              self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {counter_name}={counter_name}+1 WHERE {id_name}='$token->token_id'"));
            }
            if (self::$apisetting['ios_msg_counter'] == 1 && $token->device_type == 'ios') {
              self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {counter_name}={counter_name}+1 WHERE {id_name}='$token->token_id'"));
            }
            $tokencounter++;
          }
        }
        else {
          foreach ($tokens AS $token) {
            if(!isset($token->userid)){
              $token->userid = $wpdb->get_var("SELECT userid FROM ".$wpdb->prefix."sm_push_tokens WHERE id='$token->token_id'");
            }
            if ($options['sendtype'] == 'live') {
              $wpdb->insert($wpdb->prefix.'push_queue', array('token_id' => $token->token_id, 'token' => $token->device_token, 'device_type' => $token->device_type, 'counter' => $token->counter));
            }
            else{
              $wpdb->insert($wpdb->prefix.'push_cron_queue', array('token_id' => $token->token_id, 'token' => $token->device_token, 'device_type' => $token->device_type, 'counter' => $token->counter, 'sendtime' => $message_data['starttime'], 'sendoptions' => $handler_options['msgid']));
            }
            if(!empty($token->userid)){
              $wpdb->insert($wpdb->prefix.'push_history', array('platform' => self::platformType($token->device_type), 'userid' => $token->userid, 'msgid' => $handler_options['msgid'], 'timepost' => $message_data['starttime']));
            }
            if (self::$apisetting['android_msg_counter'] == 1 && $token->device_type == 'android') {
              self::$pushdb->get_var(self::parse_query("UPDATE {tbname} SET {counter_name}={counter_name}+1 WHERE {id_name}='$token->token_id'"));
            }
            if (self::$apisetting['ios_msg_counter'] == 1 && $token->device_type == 'ios') {
              self::$pushdb->get_var(self::parse_query("UPDATE {tbname} SET {counter_name}={counter_name}+1 WHERE {id_name}='$token->token_id'"));
            }
            $lastid = $token->token_id;
            $tokencounter++;
          }
        }
        $handler_options['lastid'] = $lastid;
        $handler_options['token_counter'] = $tokencounter;
        update_option('smpush_instant_send', $handler_options);
        set_transient('smpush_resum', 1, 43200);
        self::jsonPrint(1, $lastid);
      }
      if(!empty($options['emailgroups'])){
        $usergroupsql = '';
        foreach($options['emailgroups'] as $user_role){
          $usergroupsql .= 'OR '.$wpdb->usermeta.'.meta_value LIKE \'%'.$user_role.'%\'';
        }
        $usergroupsql = ltrim($usergroupsql, 'OR ');
        $extraWPEmails = $wpdb->get_results("SELECT $wpdb->users.user_email,$wpdb->usermeta.user_id FROM $wpdb->users
        INNER JOIN $wpdb->usermeta ON($wpdb->usermeta.user_id=$wpdb->users.ID AND $wpdb->usermeta.meta_key='".$wpdb->prefix."capabilities' AND ($usergroupsql))
        GROUP BY $wpdb->users.ID");
        if(!empty($extraWPEmails)){
          foreach($extraWPEmails as $extraWPEmail){
            if ($options['sendtype'] == 'live') {
              $wpdb->insert($wpdb->prefix.'push_queue', array('token' => $extraWPEmail->user_email, 'device_type' => 'email'));
            }
            else{
              $wpdb->insert($wpdb->prefix.'push_cron_queue', array('token' => $extraWPEmail->user_email, 'device_type' => 'email', 'sendtime' => $message_data['starttime'], 'sendoptions' => $handler_options['msgid']));
            }
            if(!empty($extraWPEmail->user_id)){
              $wpdb->insert($wpdb->prefix.'push_history', array('platform' => 'email', 'userid' => $extraWPEmail->user_id, 'msgid' => $handler_options['msgid'], 'timepost' => $message_data['starttime']));
            }
            $tokencounter++;
          }
        }
      }
      delete_transient('smpush_resum');
      delete_transient('smpush_post');
      if ($options['sendtype'] == 'live') {
        self::$sendoptions['message'] = $options['message'];
        self::updateStats();
        self::updateStats('totalsend', $tokencounter);
        self::jsonPrint(-1, $tokencounter);
      }
      else {
        self::jsonPrint(-2, $tokencounter);
      }
    }
    else {
      $queuecount = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."push_queue");
      if ($queuecount > 0 && empty($_GET['id'])) {
        self::send_process(true, $queuecount);
      }
      else {
        $params = array();
        $params['all'] = self::$defconnection['counter'];
        $params['channels'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_channels ORDER BY id ASC");
        $params['newsletter_templates'] = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_newsletter_templates WHERE static='1' ORDER BY id ASC");
        $params['dbtype'] = $wpdb->get_var("SELECT dbtype FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'");
        $beePluginLangs = array('en-US','es-ES','fr-FR','it-IT','pt-BR','id-ID','ja-JP','zh-CN','zh-HK','da-DE','da-DK','sv-SE','pl-PL','ru-RU','ko-KR','nl-NL','fi-FI','cs-CZ');
        $WPlang = str_replace('_', '-', get_locale());
        if(in_array($WPlang, $beePluginLangs)){
          $params['beePluginLang'] = $WPlang;
        }
        else{
          $params['beePluginLang'] = 'en-US';
        }
        wp_enqueue_script('postbox');
        wp_enqueue_script('smpush-gmap-js');
        wp_enqueue_script('smpush-BeePlugin');
        wp_enqueue_script('smpush-select2-js');
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_style('smpush-select2-style');
        //labelauty libs
        wp_enqueue_style('smpush-labelauty-style');
        wp_enqueue_script('smpush-jquery-labelauty');
        //timepicker libs
        wp_enqueue_style('smpush-jquery-smoothness');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_style('smpush-timepicker-addon');
        wp_enqueue_style('smpush-emojipicker');
        wp_enqueue_script('smpush-jquery-sliderAccess');
        wp_enqueue_script('smpush-timepicker-addon');
        wp_enqueue_script('smpush-emojipicker');
        add_thickbox();
        if(!empty($_GET['id'])){
          self::getMessagData($_GET['id']);
        }
        else{
          self::$data = array('send_type' => 'now', 'status' => 1);
        }
        self::loadpage('send_notification', 0, $params);
      }
    }
  }
  
  public static function calculateDevices($msgid=false) {
    global $wpdb;
    if($msgid !== false){
      self::getMessagData($msgid);
      $_POST = self::$data;
      $_POST = array_merge($_POST['options'], $_POST);
    }
    $stats = array('ios' => 0, 'iosfcm' => 0, 'android' => 0,'wp' => 0,'wp10' => 0, 'bb' => 0, 'chrome' => 0, 'safari' => 0, 'firefox' => 0, 'opera' => 0, 'edge' => 0, 'samsung' => 0, 'fbmsn' => 0, 'fbnotify' => 0, 'email' => 0);
    $select = $joinsql = $order = $where = $gpswhere = '';
    $deviceIDs = array();
    $types_name = $wpdb->get_row("SELECT ios_name,iosfcm_name,edge_name,android_name,wp_name,bb_name,chrome_name,safari_name,firefox_name,wp10_name,fbmsn_name,fbnotify_name,opera_name,samsung_name,email_name FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'");
    if(!empty($_POST['platforms'])){
      $_POST['platforms'] = array_flip($_POST['platforms']);
    }
    if(empty($_POST['platforms']) || (isset($_POST['platforms']['ios']) && isset($_POST['platforms']['android']) && isset($_POST['platforms']['wp']) && isset($_POST['platforms']['wp10']) && isset($_POST['platforms']['bb'])
      && isset($_POST['platforms']['chrome']) && isset($_POST['platforms']['safari']) && isset($_POST['platforms']['firefox']) && isset($_POST['platforms']['opera']) && isset($_POST['platforms']['samsung'])
      && isset($_POST['platforms']['fbmsn']) && isset($_POST['platforms']['fbnotify']) && isset($_POST['platforms']['email']) && isset($_POST['platforms']['edge']) && isset($_POST['platforms']['iosfcm']))){
      $_POST['platforms'] = array('all');
      $where = '';
    }
    elseif(!empty($_POST['platforms'])){
      $_POST['platforms'] = array_flip($_POST['platforms']);
    }
    if (in_array('ios', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{ios_name}'";
    }
    if (in_array('iosfcm', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{iosfcm_name}'";
    }
    if (in_array('android', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{android_name}'";
    }
    if (in_array('wp10', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{wp10_name}'";
    }
    if (in_array('bb', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{bb_name}'";
    }
    if (in_array('chrome', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{chrome_name}'";
    }
    if (in_array('safari', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{safari_name}'";
    }
    if (in_array('firefox', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{firefox_name}'";
    }
    if (in_array('opera', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{opera_name}'";
    }
    if (in_array('edge', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{edge_name}'";
    }
    if (in_array('samsung', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{samsung_name}'";
    }
    if (in_array('fbmsn', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{fbmsn_name}'";
    }
    if (in_array('fbnotify', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{fbnotify_name}'";
    }
    if (in_array('email', $_POST['platforms'])) {
      $where .= " OR {tbname}.{type_name}='{email_name}'";
    }
    if(!empty($where)){
      $where = ' AND ('.ltrim($where, ' OR ').') ';
    }
    if(!empty($_POST['usergroups'])){
      $usergroupsql = '';
      foreach($_POST['usergroups'] as $user_role){
        $usergroupsql .= 'OR '.$wpdb->usermeta.'.meta_value LIKE \'%'.$user_role.'%\'';
      }
      $usergroupsql = ltrim($usergroupsql, 'OR ');
      $joinsql .= "INNER JOIN $wpdb->usermeta ON($wpdb->usermeta.user_id={tbname}.userid AND $wpdb->usermeta.meta_key='".$wpdb->prefix."capabilities' AND ($usergroupsql))";
    }
    if(!empty($_POST['emailgroups'])){
      $usergroupsql = '';
      foreach($_POST['emailgroups'] as $user_role){
        $usergroupsql .= 'OR '.$wpdb->usermeta.'.meta_value LIKE \'%'.$user_role.'%\'';
      }
      $usergroupsql = ltrim($usergroupsql, 'OR ');
      $extraWPEmails = $wpdb->get_var("SELECT COUNT($wpdb->users.ID) FROM $wpdb->users
      INNER JOIN $wpdb->usermeta ON($wpdb->usermeta.user_id=$wpdb->users.ID AND $wpdb->usermeta.meta_key='".$wpdb->prefix."capabilities' AND ($usergroupsql))");
    }
    
    if (!empty($_POST['latitude']) AND ! empty($_POST['longitude']) AND ! empty($_POST['radius'])) {
      $select = ",(3959*acos(cos(radians($_POST[latitude]))*cos(radians({tbname}.{latitude_name}))*cos(radians({tbname}.{longitude_name})-radians($_POST[longitude]))+sin(radians($_POST[latitude]))*sin(radians({tbname}.{latitude_name})))) AS geodistance";
      $gpswhere .= " AND {tbname}.{geotimeout_name}<".(current_time('timestamp')-3600);
      if(!empty($_POST['gps_expire'])){
        $gpswhere .= " AND {tbname}.{gpstime_name}>".(current_time('timestamp')-($_POST['gps_expire']*3600));
      }
      $order = 'HAVING geodistance<='.$_POST['radius'];
    }
    if($_POST['send_type'] == 'custom'){
      if(empty(self::$apisetting['msgs_interval'])){
        $_POST['options']['query'] = str_replace('AND {tbname}.receive_again_at<CURRENT_TIME_NOW', '', $_POST['options']['query']);
      }
      else{
        $_POST['options']['query'] = str_replace('CURRENT_TIME_NOW', current_time('timestamp'), $_POST['options']['query']);
      }
      $cdevices = self::$pushdb->get_results(self::parse_query($_POST['options']['query']), ARRAY_A);
      if(smpush_env == 'debug'){
        self::log(self::parse_query($_POST['options']['query']));
      }
      if(empty($cdevices)){
        return false;
      }
      else{
        $cindevices = array();
        foreach($cdevices as $cdevice){
          $cindevices[] = $cdevice['id'];
        }
        $gpswhere .= ' AND {tbname}.{id_name} IN ('.implode(',', $cindevices).')';
      }
    }
    if (!empty($_POST['inchannels_and']) OR ! empty($_POST['inchannels_or']) OR ! empty($_POST['notchannels_and']) OR ! empty($_POST['notchannels_or'])) {
      if(!empty(self::$apisetting['msgs_interval'])){
        $gpswhere .= ' AND {tbname}.receive_again_at<'.current_time('timestamp');
      }
      $defconid = self::$apisetting['def_connection'];
      $tablename = $wpdb->prefix.'push_relation';
      $tokens = $wpdb->get_results(self::parse_query("SELECT {tbname}.{id_name} AS tokenid,{tbname}.{type_name} AS device_type,GROUP_CONCAT($tablename.`channel_id` SEPARATOR ',') AS channelids $select FROM {tbname}
      INNER JOIN $tablename ON(($tablename.token_id={tbname}.{id_name} AND $tablename.connection_id='$defconid') OR ({tbname}.userid>0 AND {tbname}.userid=$tablename.userid))
      $joinsql
      WHERE {tbname}.{active_name}='1' $gpswhere $where GROUP BY {tbname}.{id_name} $order"));
      if($tokens){
        foreach ($tokens AS $token) {
          $token->channelids = explode(',', $token->channelids);
          if (!empty($_POST['inchannels_and'])) {
            $intersect = array_intersect($token->channelids, $_POST['inchannels_and']);
            if (count($intersect) != count($_POST['inchannels_and'])) {
              continue;
            }
          }
          if (!empty($_POST['inchannels_or'])) {
            $bool = array_intersect($token->channelids, $_POST['inchannels_or']);
            if(empty($bool)){
              continue;
            }
          }
          if (!empty($_POST['notchannels_and'])) {
            $bool = array_intersect($token->channelids, $_POST['notchannels_and']);
            if(!empty($bool)){
              continue;
            }
          }
          if (!empty($_POST['notchannels_or'])) {
            $bool = array_diff($_POST['notchannels_or'], $token->channelids);
            if(empty($bool)){
              continue;
            }
          }
          array_push($deviceIDs, $token->tokenid);
          if($token->device_type == $types_name->ios_name){
            $stats['ios']++;
          }
          if($token->device_type == $types_name->iosfcm_name){
            $stats['iosfcm']++;
          }
          elseif($token->device_type == $types_name->android_name){
            $stats['android']++;
          }
          elseif($token->device_type == $types_name->wp_name){
            $stats['wp']++;
          }
          elseif($token->device_type == $types_name->wp10_name){
            $stats['wp10']++;
          }
          elseif($token->device_type == $types_name->bb_name){
            $stats['bb']++;
          }
          elseif($token->device_type == $types_name->chrome_name){
            $stats['chrome']++;
          }
          elseif($token->device_type == $types_name->safari_name){
            $stats['safari']++;
          }
          elseif($token->device_type == $types_name->firefox_name){
            $stats['firefox']++;
          }
          elseif($token->device_type == $types_name->opera_name){
            $stats['opera']++;
          }
          elseif($token->device_type == $types_name->edge_name){
            $stats['edge']++;
          }
          elseif($token->device_type == $types_name->samsung_name){
            $stats['samsung']++;
          }
          elseif($token->device_type == $types_name->fbmsn_name){
            $stats['fbmsn']++;
          }
          elseif($token->device_type == $types_name->fbnotify_name){
            $stats['fbnotify']++;
          }
          elseif($token->device_type == $types_name->email_name){
            $stats['email']++;
          }
        }
      }
    }
    else {
      if(!empty(self::$apisetting['msgs_interval'])){
        $gpswhere .= ' AND receive_again_at<'.current_time('timestamp');
      }
      $tokenstats = self::$pushdb->get_results(self::parse_query("SELECT {tbname}.{id_name} AS tokenid,{tbname}.{type_name} AS device_type $select FROM {tbname} $joinsql WHERE {active_name}='1' $gpswhere $order"), 'ARRAY_A');
      if(!empty($tokenstats)){
        foreach($tokenstats as $tokenstat){
          if ($tokenstat['device_type'] == $types_name->ios_name && (in_array('ios', $_POST['platforms']) OR in_array('all', $_POST['platforms']))) {
            $stats['ios']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          if ($tokenstat['device_type'] == $types_name->iosfcm_name && (in_array('iosfcm', $_POST['platforms']) OR in_array('all', $_POST['platforms']))) {
            $stats['iosfcm']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->android_name && (in_array('android', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['android']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->wp_name && (in_array('wp', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['wp']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->wp10_name && (in_array('wp10', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['wp10']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->bb_name && (in_array('bb', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['bb']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->chrome_name && (in_array('chrome', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['chrome']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->safari_name && (in_array('safari', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['safari']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->firefox_name && (in_array('firefox', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['firefox']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->opera_name && (in_array('opera', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['opera']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->edge_name && (in_array('edge', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['edge']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->samsung_name && (in_array('samsung', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['samsung']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->fbmsn_name && (in_array('fbmsn', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['fbmsn']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->fbnotify_name && (in_array('fbnotify', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['fbnotify']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
          elseif($tokenstat['device_type'] == $types_name->email_name && (in_array('email', $_POST['platforms']) OR in_array('all', $_POST['platforms']))){
            $stats['email']++;
            array_push($deviceIDs, $tokenstat['tokenid']);
          }
        }
      }
    }
    if(!empty($extraWPEmails)){
      $stats['email'] += $extraWPEmails;
    }
    if($msgid !== false){
      return implode(',', $deviceIDs);
    }
    else{
      return $stats;
    }
  }
  
  public static function RunQueue() {
    global $wpdb;
    $iphone_devices = array();
    $android_devices = array();
    $wp_devices = array();
    $wp10_devices = array();
    $bb_devices = array();
    $chrome_devices = array();
    $safari_devices = array();
    $firefox_devices = array();
    $opera_devices = array();
    $samsung_devices = array();
    $fbmsn_devices = array();
    $fbnotify_devices = array();
    $email_devices = array();
    $iosfcm_devices = array();
    $edge_devices = array();
    $icounter = $acounter = $wcounter = $w10counter = $bcounter = $ccounter = $scounter = $fcounter = $counter9 = $counter10 = $counter11 = $counter12 = $counter13 = $counter14 = $counter15 = 0;
    
    $handler_options = get_option('smpush_instant_send');
    $message_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_archive WHERE id='$handler_options[msgid]'", ARRAY_A);
    
    if($message_data['send_type'] == 'feedback'){
      self::connectFeedback(0, false, $handler_options['msgid']);
      self::updateStats('all');
    }
    
    $options = unserialize($message_data['options']);
    $options['msgid'] = $handler_options['msgid'];
    
    $all_count = $wpdb->get_var("SELECT COUNT(*) FROM ".$wpdb->prefix."push_queue");
    $os_name = $wpdb->get_row("SELECT android_name,ios_name,iosfcm_name,edge_name,wp_name,bb_name,chrome_name,safari_name,firefox_name,wp10_name,opera_name,samsung_name,fbmsn_name,fbnotify_name,email_name FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'");
    if ($options['iostestmode'] == 1) {
      $limit = 1;
    }
    else {
      $limit = 1000;
    }
    $queue = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->ios_name' LIMIT 0,$limit");
    $queue2 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->android_name' LIMIT 0,$limit");
    $queue3 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->wp_name' LIMIT 0,$limit");
    $queue4 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->bb_name' LIMIT 0,$limit");
    $queue5 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->chrome_name' LIMIT 0,$limit");
    $queue6 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->safari_name' LIMIT 0,$limit");
    $queue7 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->firefox_name' LIMIT 0,$limit");
    $queue8 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->wp10_name' LIMIT 0,$limit");
    $queue9 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->opera_name' LIMIT 0,$limit");
    $queue10 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->samsung_name' LIMIT 0,$limit");
    $queue11 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->fbmsn_name' LIMIT 0,$limit");
    $queue12 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->fbnotify_name' LIMIT 0,$limit");
    $queue13 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->email_name' LIMIT 0,$limit");
    $queue14 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->iosfcm_name' LIMIT 0,$limit");
    $queue15 = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_queue WHERE device_type='$os_name->edge_name' LIMIT 0,$limit");
    if (!$queue && !$queue2 && !$queue3 && !$queue4 && !$queue5 && !$queue6 && !$queue7 && !$queue8 && !$queue9 && !$queue10 && !$queue11 && !$queue12 && !$queue13 && !$queue14 && !$queue15) {
      self::connectFeedback($all_count, false, $handler_options['msgid']);
      $wpdb->query("UPDATE ".$wpdb->prefix."push_archive SET processed='1' WHERE id='$handler_options[msgid]'");
      self::updateStats('all');
    }
    foreach ($queue AS $queueone) {
      $iphone_devices[$icounter]['token'] = $queueone->token;
      $iphone_devices[$icounter]['id'] = $queueone->id;
      $iphone_devices[$icounter]['queue_id'] = $queueone->id;
      $iphone_devices[$icounter]['badge'] = $queueone->counter;
      $icounter++;
    }
    foreach ($queue2 AS $queueone) {
      $android_devices['token'][$acounter] = $queueone->token;
      $android_devices['id'][$acounter] = $queueone->id;
      $android_devices['queue_id'][$acounter] = $queueone->id;
      $acounter++;
    }
    foreach ($queue3 AS $queueone) {
      $wp_devices['token'][$wcounter] = $queueone->token;
      $wp_devices['id'][$wcounter] = $queueone->token_id;
      $wp_devices['queue_id'][$wcounter] = $queueone->id;
      $wcounter++;
    }
    foreach ($queue4 AS $queueone) {
      $bb_devices['token'][$bcounter] = $queueone->token;
      $bb_devices['id'][$bcounter] = $queueone->token_id;
      $bb_devices['queue_id'][$bcounter] = $queueone->id;
      $bcounter++;
    }
    foreach ($queue5 AS $queueone) {
      $chrome_devices['token'][$ccounter] = $queueone->token;
      $chrome_devices['id'][$ccounter] = $queueone->token_id;
      $chrome_devices['queue_id'][$ccounter] = $queueone->id;
      $ccounter++;
    }
    foreach ($queue6 AS $queueone) {
      $safari_devices[$scounter]['token'] = $queueone->token;
      $safari_devices[$scounter]['id'] = $queueone->token_id;
      $safari_devices[$scounter]['queue_id'] = $queueone->id;
      $scounter++;
    }
    foreach ($queue7 AS $queueone) {
      $firefox_devices['token'][$fcounter] = $queueone->token;
      $firefox_devices['id'][$fcounter] = $queueone->token_id;
      $firefox_devices['queue_id'][$fcounter] = $queueone->id;
      $fcounter++;
    }
    foreach ($queue8 AS $queueone) {
      $wp10_devices['token'][$w10counter] = $queueone->token;
      $wp10_devices['id'][$w10counter] = $queueone->token_id;
      $wp10_devices['queue_id'][$w10counter] = $queueone->id;
      $w10counter++;
    }
    foreach ($queue9 AS $queueone) {
      $opera_devices['token'][$counter9] = $queueone->token;
      $opera_devices['id'][$counter9] = $queueone->token_id;
      $opera_devices['queue_id'][$counter9] = $queueone->id;
      $counter9++;
    }
    foreach ($queue10 AS $queueone) {
      $samsung_devices['token'][$counter10] = $queueone->token;
      $samsung_devices['id'][$counter10] = $queueone->token_id;
      $samsung_devices['queue_id'][$counter10] = $queueone->id;
      $counter10++;
    }
    foreach ($queue11 AS $queueone) {
      $fbmsn_devices[$counter11]['token'] = $queueone->token;
      $fbmsn_devices[$counter11]['id'] = $queueone->token_id;
      $fbmsn_devices[$counter11]['queue_id'] = $queueone->id;
      $counter11++;
    }
    foreach ($queue12 AS $queueone) {
      $fbnotify_devices[$counter12]['token'] = $queueone->token;
      $fbnotify_devices[$counter12]['id'] = $queueone->token_id;
      $fbnotify_devices[$counter12]['queue_id'] = $queueone->id;
      $counter12++;
    }
    foreach ($queue13 AS $queueone) {
      $email_devices[$counter13]['token'] = $queueone->token;
      $email_devices[$counter13]['id'] = $queueone->token_id;
      $email_devices[$counter13]['queue_id'] = $queueone->id;
      $counter13++;
    }
    foreach ($queue14 AS $queueone) {
      $iosfcm_devices['token'][$counter14] = $queueone->token;
      $iosfcm_devices['id'][$counter14] = $queueone->token_id;
      $iosfcm_devices['queue_id'][$counter14] = $queueone->id;
      $counter14++;
    }
    foreach ($queue15 AS $queueone) {
      $edge_devices['token'][$counter15] = $queueone->token;
      $edge_devices['id'][$counter15] = $queueone->token_id;
      $edge_devices['queue_id'][$counter15] = $queueone->id;
      $counter15++;
    }
    $message = $options['message'];
    if (!session_id()) {
      session_start();
    }
    if ($icounter > 0)
      self::connectPush($message, $iphone_devices, 'ios', $options, true, $all_count, false, $handler_options['msgid']);
    if ($acounter > 0)
      self::connectPush($message, $android_devices, 'android', $options, true, $all_count, false, $handler_options['msgid']);
    if ($wcounter > 0)
      self::connectPush($message, $wp_devices, 'wp', $options, true, $all_count, false, $handler_options['msgid']);
    if ($bcounter > 0)
      self::connectPush($message, $bb_devices, 'bb', $options, true, $all_count, false, $handler_options['msgid']);
    if ($ccounter > 0)
      self::connectPush($message, $chrome_devices, 'chrome', $options, true, $all_count, false, $handler_options['msgid']);
    if ($scounter > 0)
      self::connectPush($message, $safari_devices, 'safari', $options, true, $all_count, false, $handler_options['msgid']);
    if ($fcounter > 0)
      self::connectPush($message, $firefox_devices, 'firefox', $options, true, $all_count, false, $handler_options['msgid']);
    if ($w10counter > 0)
      self::connectPush($message, $wp10_devices, 'wp10', $options, true, $all_count, false, $handler_options['msgid']);
    if ($counter9 > 0)
      self::connectPush($message, $opera_devices, 'opera', $options, true, $all_count, false, $handler_options['msgid']);
    if ($counter10 > 0)
      self::connectPush($message, $samsung_devices, 'samsung', $options, true, $all_count, false, $handler_options['msgid']);
    if ($counter11 > 0)
      self::connectPush($message, $fbmsn_devices, 'fbmsn', $options, true, $all_count, false, $handler_options['msgid']);
    if ($counter12 > 0)
      self::connectPush($message, $fbnotify_devices, 'fbnotify', $options, true, $all_count, false, $handler_options['msgid']);
    if ($counter13 > 0)
      self::connectPush($message, $email_devices, 'email', $options, true, $all_count, false, $handler_options['msgid']);
    if ($counter14 > 0)
      self::connectPush($message, $iosfcm_devices, 'iosfcm', $options, true, $all_count, false, $handler_options['msgid']);
    if ($counter15 > 0)
      self::connectPush($message, $edge_devices, 'edge', $options, true, $all_count, false, $handler_options['msgid']);
    self::jsonPrint(1, array('message' => '', 'all_count' => $all_count));
  }

  public static function connectPush($message, $device_token, $device_type, $options, $showerror = true, $all_count = 0, $cronjob = false, $msgid=0) {
    global $wpdb;
    self::$cronSendOperation = $cronjob;
    $idsToDel = array();
    add_action('phpmailer_init', array('smpush_controller', 'smtp_config'), 99, 1);
    if ($cronjob === true) {
      smpush_helper::$returnValue = 'cronjob';
    }
    $siteurl = get_bloginfo('wpurl');
    $message = str_replace(array('"','\''), '`', self::cleanString($message));
    $helper = new smpush_helper();
    $sendCounter = 0;
    self::$sendoptions = $options;
    if ($device_type == 'ios' && self::$apisetting['apple_api_ver'] == 'http2') {
      $payload = self::getPayload($message);
      if (self::$sendoptions['expire'] > 0) {
        $expiry = current_time('timestamp') + (self::$sendoptions['expire'] * 3600);
      } else {
        $expiry = 0;
      }
      foreach ($device_token AS $key => $sDevice) {
        $sDevice['token'] = str_replace(array(' ', '-'), '', $sDevice['token']);
        if (isset($sDevice['id']) && $cronjob === false) {
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id='".$sDevice['queue_id']."'");
        }
        unset($device_token[$key]);
        if (preg_match('~^[a-f0-9]{64}$~i', $sDevice['token'])) {
          if (self::$apisetting['ios_msg_counter'] == 1 && isset($sDevice['badge'])) {
            $payload = json_decode($payload, true);
            $payload['aps']['badge'] = $sDevice['badge'];
            @$payload = json_encode($payload, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
          }
          if(smpush_env == 'debug'){
            $response = true;
            self::log('sent to: '.$sDevice['token']);
            self::log($payload);
          }
          else{
            $response = self::connectAPNS($sDevice['token'], $payload, 'ios');
          }
          if($response === false){
            $wpdb->insert($wpdb->prefix.'push_feedback', array('tokens' => $sDevice['token'], 'device_type' => 'ios_invalid', 'msgid' => $msgid));
          }
          elseif($response === -1){
            self::updateStats('iosfail', 1, $cronjob, $msgid);
          }
          elseif($response === true){
            //successfull message
          }
          else{
            return self::jsonPrint(0, '<p class="error">'.$response.'</p>');
          }
        }
        else {
          $wpdb->insert($wpdb->prefix.'push_feedback', array('tokens' => $sDevice['token'], 'device_type' => 'ios_invalid', 'msgid' => $msgid));
        }
        $sendCounter++;
      }
      self::updateStats('iossend', $sendCounter, $cronjob, $msgid);
      if (!empty($_SESSION['smpush_firstrun'])) {
        $_SESSION['smpush_firstrun'] = 0;
        self::jsonPrint(2, array('message' => '<p>'.__('Connection With Apple server established successfully', 'smpush-plugin-lang').'</p>'.'<p>'.__('Apple server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'ios' && self::$apisetting['apple_api_ver'] == 'ssl') {
      $payload = self::getPayload($message);
      if(smpush_env == 'debug'){
        if (!empty($_GET['firstrun'])) {
          $_SESSION['smpush_firstrun'] = 1;
          @fclose($fpssl);
          self::jsonPrint(2, array('message' => '<p>'.__('Connection With Apple server established successfully', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
        }
      }
      else{
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', self::$apisetting['apple_cert_path']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$apisetting['apple_passphrase']);
        if (self::$apisetting['apple_sandbox'] == 1) {
          $appleserver = 'tls://gateway.sandbox.push.apple.com:2195';
        }
        else {
          $appleserver = 'tls://gateway.push.apple.com:2195';
        }
        @$fpssl = stream_socket_client($appleserver, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fpssl && $showerror) {
          if (empty($errstr))
            $errstr = __('Apple Certification error or problem with Password phrase', 'smpush-plugin-lang');
          if ($err == 111)
            $errstr .= __(' - Contact your host to enable outgoing ports', 'smpush-plugin-lang');
          elseif ($errstr == 'Connection timed out') {
            @fclose($fpssl);
            sleep(10);
            return self::jsonPrint(2, array('message' => '<p class="error">'.__('Connection timed out or your host blocked the outgoing port 2195...System trying reconnect now', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
          }
          self::jsonPrint(0, '<p class="error">'.__('Could not establish connection with SSL server', 'smpush-plugin-lang').': '.$errstr.'</p>');
        } elseif (!$fpssl)
          return;
        elseif (!empty($_GET['firstrun'])) {
          $_SESSION['smpush_firstrun'] = 1;
          @fclose($fpssl);
          self::jsonPrint(2, array('message' => '<p>'.__('Connection With Apple server established successfully', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
        }
      }
      if (self::$sendoptions['expire'] > 0) {
        $expiry = current_time('timestamp') + (self::$sendoptions['expire'] * 3600);
      } else {
        $expiry = 0;
      }
      foreach ($device_token AS $key => $sDevice) {
        $sDevice['token'] = str_replace(array(' ', '-'), '', $sDevice['token']);
        if (isset($sDevice['id']) && $cronjob === false) {
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id='".$sDevice['queue_id']."'");
        }
        unset($device_token[$key]);
        if (preg_match('~^[a-f0-9]{64}$~i', $sDevice['token'])) {
          if (self::$apisetting['ios_msg_counter'] == 1 && isset($sDevice['badge'])) {
            $payload = json_decode($payload, true);
            $payload['aps']['badge'] = $sDevice['badge'];
            @$payload = json_encode($payload, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
          }
          if ($expiry > 0) {
            @$sslwrite = chr(1).pack("N", $sDevice['id']).pack("N", $expiry).pack("n", 32).pack('H*', $sDevice['token']).pack("n", strlen($payload)).$payload;
          } else {
            @$sslwrite = chr(0).pack('n', 32).pack('H*', $sDevice['token']).pack('n', strlen($payload)).$payload;
          }
          $sslwriteLen = strlen($sslwrite);
          if(smpush_env == 'debug'){
            $response = true;
            self::log('sent to: '.$sDevice['token']);
            self::log($payload);
          }
          elseif ($sslwriteLen !== (int) @fwrite($fpssl, $sslwrite)) {
            self::updateStats('iossend', $sendCounter, $cronjob, $msgid);
            @fclose($fpssl);
            sleep(3);
            return self::jsonPrint(2, array('message' => '', 'all_count' => $all_count));
          }
          $sendCounter++;
          if (!empty($_SESSION['smpush_firstrun']) OR ( self::$sendoptions['iostestmode'] == 1 AND $cronjob === false)) {
            stream_set_blocking($fpssl, 0);
            stream_set_write_buffer($fpssl, 0);
            $read = array($fpssl);
            $null = NULL;
            $nChangedStreams = stream_select($read, $null, $null, 0, 1000000);
            if ($nChangedStreams !== false && $nChangedStreams > 0) {
              $status = @ord(fread($fpssl, 1));
              if (in_array($status, array(3, 4, 6, 7))) {
                @fclose($fpssl);
                self::jsonPrint(0, '<p class="error">'.__('Apple server error', 'smpush-plugin-lang').': '.self::$_aErrorResponseMessages[$status].'</p>');
              }
              if ($status == 8) {
                $wpdb->insert($wpdb->prefix.'push_feedback', array('tokens' => $sDevice['token'], 'device_type' => 'ios_invalid', 'msgid' => $msgid));
                @fclose($fpssl);
                if (self::$sendoptions['iostestmode'] == 1) {
                  $_SESSION['smpush_firstrun'] = 0;
                  self::jsonPrint(2, array('message' => '<p>'.__('Apple server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
                } else {
                  self::updateStats('iossend', $sendCounter, $cronjob, $msgid);
                  self::connectPush($message, $device_token, $device_type, $options, true, $all_count, $cronjob);
                }
              }
            }
          }
          if (!empty($_SESSION['smpush_firstrun'])) {
            self::updateStats('iossend', $sendCounter, $cronjob, $msgid);
            $_SESSION['smpush_firstrun'] = 0;
            @fclose($fpssl);
            self::jsonPrint(2, array('message' => '<p>'.__('Apple server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
          }
        } else {
          $wpdb->insert($wpdb->prefix.'push_feedback', array('tokens' => $sDevice['token'], 'device_type' => 'ios_invalid', 'msgid' => $msgid));
        }
      }
      self::updateStats('iossend', $sendCounter, $cronjob, $msgid);
      @fclose($fpssl);
    }
    elseif ($device_type == 'safari') {
      $payload = array();
      $payload['aps']['alert'] = array(
        'title' => self::cleanString(self::$sendoptions['desktop_title'], true),
        'body' => $message
      );
      if(!empty(self::$sendoptions['ios_slide'])){
        $payload['aps']['alert']['action'] = self::$sendoptions['ios_slide'];
      }
      
      $payload['aps']['url-args'] = array('/'.self::$apisetting['push_basename'].'/get_link/?id='.self::$sendoptions['msgid'].'&platform=safari');
      $payload = json_encode($payload, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
      if(smpush_env != 'debug'){
        $ctx = stream_context_create();
        stream_context_set_option($ctx, 'ssl', 'local_cert', self::$apisetting['safari_cert_path']);
        stream_context_set_option($ctx, 'ssl', 'passphrase', self::$apisetting['safari_passphrase']);
        $appleserver = 'tls://gateway.push.apple.com:2195';
        @$fpssl = stream_socket_client($appleserver, $err, $errstr, 60, STREAM_CLIENT_CONNECT | STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fpssl && $showerror) {
          if (empty($errstr))
            $errstr = __('Safari Certification error or problem with Password phrase', 'smpush-plugin-lang');
          if ($err == 111)
            $errstr .= __(' - Contact your host to enable outgoing ports', 'smpush-plugin-lang');
          elseif ($errstr == 'Connection timed out') {
            @fclose($fpssl);
            sleep(10);
            return self::jsonPrint(2, array('message' => '<p class="error">'.__('Connection timed out or your host blocked the outgoing port 2195...System trying reconnect now', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
          }
          self::jsonPrint(0, '<p class="error">'.__('Could not establish connection with Safari SSL server', 'smpush-plugin-lang').': '.$errstr.'</p>');
        }
        elseif (!$fpssl)return;
      }
      foreach ($device_token AS $key => $sDevice) {
        $sDevice['token'] = str_replace(array(' ', '-'), '', $sDevice['token']);
        if (isset($sDevice['id']) && $cronjob === false) {
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id='".$sDevice['queue_id']."'");
        }
        unset($device_token[$key]);
        @$sslwrite = chr(0).pack('n', 32).pack('H*', $sDevice['token']).pack('n', strlen($payload)).$payload;
        $sslwriteLen = strlen($sslwrite);
        if(smpush_env == 'debug'){
          $response = true;
          self::log('sent to: '.$sDevice['token']);
          self::log($payload);
        }
        elseif ($sslwriteLen !== (int) @fwrite($fpssl, $sslwrite)) {
          self::updateStats('safarisend', $sendCounter, $cronjob, $msgid);
          @fclose($fpssl);
          sleep(3);
          return self::jsonPrint(2, array('message' => '', 'all_count' => $all_count));
        }
        $sendCounter++;
      }
      self::updateStats('safarisend', $sendCounter, $cronjob, $msgid);
      if(smpush_env != 'debug'){
        fclose($fpssl);
      }
      if (!empty($_GET['safari_notify'])) {
        self::jsonPrint('safari_server_reponse', array('message' => '<p>'.__('Connection With Safari server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Safari server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'wp') {
      $payload = array();
      if (!empty(self::$sendoptions['wp_extravalue'])) {
        if (self::$sendoptions['wp_extra_type'] == 'normal') {
          $payload['relatedvalue'] = stripslashes(self::$sendoptions['wp_extravalue']);
        }
        else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['wp_extravalue']));
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $payload[$key] = stripslashes($value);
            }
          }
        }
      }
      elseif (!empty(self::$sendoptions['extravalue'])) {
        if (self::$sendoptions['extra_type'] == 'normal') {
          $payload['relatedvalue'] = stripslashes(self::$sendoptions['extravalue']);
        }
        else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['extravalue']));
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $payload[$key] = stripslashes($value);
            }
          }
        }
      }
      if (!function_exists('curl_init') && $showerror)
        self::jsonPrint(0, '<p class="error">'.__('Windows Phone: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'', 'smpush-plugin-lang').'</p>');
      elseif (!function_exists('curl_init'))
        return;
      
      foreach($device_token['token'] as $key => $token){
        if(smpush_env == 'debug'){
          self::log('sent to: '.$token);
          self::log($message);
          self::log($payload);
          $response = true;
        }
        else{
          $response = WindowsPhonePushNotification::push_toast($token, $message, $payload);
        }
        if (!empty($response['X-DeviceConnectionStatus']) && $response['X-DeviceConnectionStatus'] == 'Expired' && self::$sendoptions['feedback'] == 1) {
          self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($token)."'"));
          self::updateStats('wpfail', 1, $cronjob, $msgid);
        }
        elseif ($response === false && $showerror) {
          self::jsonPrint(0, '<p class="error">'.__('Windows Phone push notification server not responding or unauthorized response', 'smpush-plugin-lang').'</p>');
        }
      }
      if (isset($device_token['id'])) {
        self::updateStats('wpsend', count($device_token['id']), $cronjob, $msgid);
        if ($cronjob === false) {
          $ids = implode(',', $device_token['queue_id']);
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($ids)");
        }
      }
      if (!empty($_GET['wp_notify'])) {
        self::jsonPrint('wp_server_reponse', array('message' => '<p>'.__('Connection With Windows Phone server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Windows Phone server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'wp10') {
      $payload = array();
      $payload['msgid'] = $msgid;
      if (!empty(self::$sendoptions['wp10_extravalue'])) {
        if (self::$sendoptions['wp10_extra_type'] == 'normal') {
          $payload['relatedvalue'] = stripslashes(self::$sendoptions['wp10_extravalue']);
        }
        else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['wp10_extravalue']), true);
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $payload[$key] = self::cleanString($value, true);
            }
          }
        }
      }
      elseif (!empty(self::$sendoptions['extravalue'])) {
        if (self::$sendoptions['extra_type'] == 'normal') {
          $payload['relatedvalue'] = self::cleanString(self::$sendoptions['extravalue'], true);
        }
        else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['extravalue']));
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $payload[$key] = self::cleanString($value, true);
            }
          }
        }
      }
      if (!function_exists('curl_init') && $showerror)
        self::jsonPrint(0, '<p class="error">Windows 10: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'</p>');
      elseif (!function_exists('curl_init'))
        return;
      
      foreach($device_token['token'] as $key => $token){
        if(smpush_env == 'debug'){
          self::log('sent to: '.$token);
          self::log($message);
          self::log($payload);
          $response = true;
        }
        else{
          $response = UniversalWindows10::pushToastWP10($token, $message, $payload, self::$sendoptions['wp10_img']);
        }
        if ($response === false) {
          if(self::$sendoptions['feedback'] == 1){
            self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($token)."'"));
            self::updateStats('wp10fail', 1, $cronjob, $msgid);
          }
        }
        elseif ($response !== true && $showerror) {
          self::jsonPrint(0, '<p class="error">'.__('Windows 10 push notification server returns error', 'smpush-plugin-lang').': '.$response.'</p>');
        }
      }
      if (isset($device_token['id'])) {
        self::updateStats('wp10send', count($device_token['id']), $cronjob, $msgid);
        if ($cronjob === false) {
          $ids = implode(',', $device_token['queue_id']);
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($ids)");
        }
      }
      if (!empty($_GET['wp10_notify'])) {
        self::jsonPrint('wp10_server_reponse', array('message' => '<p>'.__('Connection With Windows 10 server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Windows 10 server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'bb') {
      if (!function_exists('curl_init') && $showerror)
        self::jsonPrint(0, '<p class="error">BlackBerry: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'</p>');
      elseif (!function_exists('curl_init'))
        return;
      
      $payload = array();
      $payload['message'] = $message;
      if (!empty(self::$sendoptions['bb_extravalue'])) {
        if (self::$sendoptions['bb_extra_type'] == 'normal') {
          $payload['relatedvalue'] = self::cleanString(self::$sendoptions['bb_extravalue'], true);
        }
        else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['bb_extravalue']));
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $payload[$key] = self::cleanString($value, true);
            }
          }
        }
      }
      elseif (!empty(self::$sendoptions['extravalue'])) {
        if (self::$sendoptions['extra_type'] == 'normal') {
          $payload['relatedvalue'] = self::cleanString(self::$sendoptions['extravalue'], true);
        }
        else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['extravalue']));
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $payload[$key] = self::cleanString($value, true);
            }
          }
        }
      }
      
      if(smpush_env == 'debug'){
        $response = true;
        self::log('sent to: '.implode(',', $device_token['token']));
        self::log($payload);
      }
      else{
        $response = blackBerryPushNotification::pushMessage($device_token['token'], $payload, $showerror);
      }
      
      if (isset($device_token['id'])) {
        self::updateStats('bbsend', count($device_token['id']), $cronjob, $msgid);
        if ($cronjob === false) {
          $ids = implode(',', $device_token['queue_id']);
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($ids)");
        }
        if (self::$sendoptions['feedback'] == 1) {
          //$wpdb->insert($wpdb->prefix.'push_feedback', array('tokens' => serialize($device_token['token']), 'feedback' => $response, 'device_type' => 'bb', 'msgid' => $msgid));
        }
      }
      if (!empty($_GET['bb_notify'])) {
        self::jsonPrint('bb_server_reponse', array('message' => '<p>'.__('Connection With BlackBerry server established successfully', 'smpush-plugin-lang').'</p><p>'.__('BlackBerry server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'android' || $device_type == 'iosfcm') {
      $message = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", $message), ENT_NOQUOTES, 'UTF-8');
      $baseurl = 'https://fcm.googleapis.com/fcm/send';
      if (self::$apisetting['android_titanium_payload'] == 1) {
        $data = array();
        $data['payload']['android']['alert'] = $message;
      }
      elseif (self::$apisetting['android_corona_payload'] == 1) {
        $data = array();
        $data['alert'] = $message;
      }
      elseif(self::$apisetting['android_fcm_msg'] == 1){
        $data = array();
        $data['message'] = $message;
        $notification = array();
        $notification['body'] = $message;
        if(!empty(self::$sendoptions['desktop_title'])){
          $notification['title'] = self::cleanString(self::$sendoptions['desktop_title'], true);
        }
        elseif(!empty(self::$sendoptions['android_title'])){
          $notification['title'] = self::$sendoptions['android_title'];
        }
        if(!empty(self::$sendoptions['android_icon'])){
          $notification['icon'] = self::$sendoptions['android_icon'];
        }
        if(!empty(self::$sendoptions['android_sound'])){
          $notification['sound'] = self::$sendoptions['android_sound'];
        }
      }
      else{
        $data = array();
        $data['message'] = $message;
        if(!empty(self::$sendoptions['android_title'])){
          $data['title'] = self::$sendoptions['android_title'];
        }
        if(!empty(self::$sendoptions['android_icon'])){
          $data['icon'] = self::$sendoptions['android_icon'];
        }
        if(!empty(self::$sendoptions['android_sound'])){
          $data['sound'] = self::$sendoptions['android_sound'];
        }
      }
      if (!empty(self::$sendoptions['and_extravalue'])) {
        if (self::$sendoptions['and_extra_type'] == 'normal') {
          if (self::$apisetting['android_titanium_payload'] == 1) {
            $data['payload']['android']['relatedvalue'] = self::cleanString(self::$sendoptions['and_extravalue'], true);
          } else {
            $data['relatedvalue'] = self::cleanString(self::$sendoptions['and_extravalue'], true);
          }
        }
        else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['and_extravalue']));
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              if (self::$apisetting['android_titanium_payload'] == 1 && !in_array($key, array('title', 'icon', 'badge', 'sound', 'vibrate'))) {
                $data['payload']['android'][$key] = self::cleanString($value, true);
              } elseif (self::$apisetting['android_titanium_payload'] == 1) {
                $data['payload'][$key] = self::cleanString($value, true);
              } else {
                $data[$key] = self::cleanString($value, true);
              }
            }
          }
        }
      }
      if (!empty(self::$sendoptions['extravalue'])) {
        if (self::$sendoptions['extra_type'] == 'normal') {
          if (self::$apisetting['android_titanium_payload'] == 1) {
            $data['payload']['android']['relatedvalue'] = self::cleanString(self::$sendoptions['extravalue'], true);
          } else {
            $data['relatedvalue'] = self::cleanString(self::$sendoptions['extravalue'], true);
          }
        } else {
          $extravalue = json_decode(stripslashes(self::$sendoptions['extravalue']));
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              if (self::$apisetting['android_titanium_payload'] == 1 && !in_array($key, array('title', 'icon', 'badge', 'sound', 'vibrate'))) {
                $data['payload']['android'][$key] = self::cleanString($value, true);
              } elseif (self::$apisetting['android_titanium_payload'] == 1) {
                $data['payload'][$key] = self::cleanString($value, true);
              } else {
                $data[$key] = self::cleanString($value, true);
              }
            }
          }
        }
      }
      if (self::$apisetting['android_titanium_payload'] == 1) {
        $data['payload']['android']['msgid'] = $msgid;
      } else {
        $data['msgid'] = $msgid;
      }
      $fields = array('registration_ids' => $device_token['token'], 'data' => $data);
      if(self::$apisetting['android_fcm_msg'] == 1){
        unset($fields['data']['message']);
        $fields['notification'] = $notification;
      }
      if (self::$sendoptions['expire'] > 0) {
        $fields['time_to_live'] = self::$sendoptions['expire'] * 3600;
      }
      $headers = array('Authorization: key='.self::$apisetting['google_apikey'], 'Content-Type: application/json');
      if (!function_exists('curl_init') && $showerror)
        self::jsonPrint(0, '<p class="error">Google: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'</p>');
      elseif (!function_exists('curl_init'))
        return;
      $chandle = curl_init();
      curl_setopt($chandle, CURLOPT_URL, $baseurl);
      curl_setopt($chandle, CURLOPT_POST, true);
      curl_setopt($chandle, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
      if(defined('WP_PROXY_HOST')){
        curl_setopt($chandle, CURLOPT_PROXY, WP_PROXY_HOST);
        curl_setopt($chandle, CURLOPT_PROXYPORT, WP_PROXY_PORT);
        curl_setopt($chandle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        if(defined('WP_PROXY_USERNAME')){
          curl_setopt($chandle, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
          curl_setopt($chandle, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
        }
      }
      curl_setopt($chandle, CURLOPT_POSTFIELDS, json_encode($fields, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0));
      if(smpush_env == 'debug'){
        $result = true;
        $httpcode = 200;
        self::log($device_token['token']);
        unset($fields['registration_ids']);
        self::log($fields);
      }
      else{
        $result = curl_exec($chandle);
        $httpcode = curl_getinfo($chandle, CURLINFO_HTTP_CODE);
      }
      if ($result === FALSE && $showerror) {
        self::jsonPrint(0, '<p class="error">'.__('Google push notification server not responding', 'smpush-plugin-lang').'</p>');
      }
      elseif ($httpcode == 503 && $showerror) {
        self::jsonPrint(0, '<p class="error">'.__('Google push notification server not responding', 'smpush-plugin-lang').'</p>');
      }
      elseif ($httpcode == 401 && $showerror) {
        $result = json_decode($result, true);
        if (!empty($result['results'][0]['error']))
          self::jsonPrint(0, '<p class="error">'.$result['results'][0]['error'].'</p>');
        else
          self::jsonPrint(0, '<p class="error">'.__('Invalid Google API key', 'smpush-plugin-lang').'</p>');
      }
      if (isset($device_token['id'])) {
        self::updateStats($device_type.'send', count($device_token['id']), $cronjob, $msgid);
        if ($cronjob === false) {
          $ids = implode(',', $device_token['queue_id']);
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($ids)");
        }
        if (self::$sendoptions['feedback'] == 1) {
          $androidfail = 0;
          if(!empty($result['results'])){
            foreach ($result['results'] AS $key => $status) {
              if (isset($status['error'])) {
                if ($status['error'] == 'InvalidRegistration' || $status['error'] == 'NotRegistered' || $status['error'] == 'MismatchSenderId') {
                  self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='".$device_token['id'][$key]."'"));
                  $androidfail++;
                }
              }
              elseif (isset($status['registration_id'])) {
                self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='".$device_token['id'][$key]."'"));
                $androidfail++;
              }
            }
          }
          self::updateStats($device_type.'fail', $androidfail, $cronjob, $msgid);
        }
      }
      curl_close($chandle);
      if ($device_type == 'android' && !empty($_GET['google_notify'])) {
        self::jsonPrint(3, array('message' => '<p>'.__('Connection With Google server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Google server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'iosfcm' && !empty($_GET['iosfcm_notify'])) {
        self::jsonPrint('iosfcm_server_reponse', array('message' => '<p>'.__('Connection With iOS FCM established successfully', 'smpush-plugin-lang').'</p><p>'.__('iOS FCM accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif (self::$apisetting['desktop_webpush'] == 1 && ($device_type == 'chrome' || $device_type == 'opera' || $device_type == 'samsung' || $device_type == 'edge' || $device_type == 'firefox')) {
      require_once smpush_dir.'/lib/web-push-php/vendor/autoload.php';

      $invalidDevs = array();
      $subscriptions = array();
      $webInsertSql = '';
      $webpushfail = 0;
      $messagePayload = self::getWebPushPayload($message, $device_type);

      if(smpush_env == 'debug'){
        self::log($device_token['token']);
        self::log($messagePayload);
      }
      else{
        $auth = [
          'FCM' => self::$apisetting['chrome_apikey'],
          'VAPID' => [
            'subject' => get_bloginfo('wpurl'),
            'publicKey' => self::$apisetting['chrome_vapid_public'],
            'privateKey' => self::$apisetting['chrome_vapid_private'],
          ]
        ];

        foreach($device_token['token'] as $key => $sToken){
          $token = json_decode($sToken, true);
          if(!empty($token)){
            $subscriptions[] = [
              'id' => $device_token['id'][$key],
              'subscription' => Subscription::create([
                "endpoint" => $token['endpoint'],
                "keys" => ['p256dh' => $token['p256dh'], 'auth' => $token['auth']]
              ]),
              'payload' => $messagePayload,
            ];
          }
          else{
            $webInsertSql .= "('".self::$sendoptions['msgid']."', '".md5($sToken)."', '".$device_type."'),";
            $endpoint = ($device_type == 'firefox')? 'https://updates.push.services.mozilla.com/wpush/v1/' : 'https://fcm.googleapis.com/fcm/send/';
            $subscriptions[] = [
              'id' => $device_token['id'][$key],
              'subscription' => Subscription::create([
                "endpoint" => $endpoint.$sToken
              ]),
              'oldfcm' => ($device_type == 'firefox')? 0 : 1,
              'payload' => null,
            ];
          }
        }
        if(!empty($webInsertSql)){
          $wpdb->query('INSERT INTO '.$wpdb->prefix.'push_desktop_messages (`msgid`,`token`,`type`) VALUES '.rtrim($webInsertSql, ',')).';';
        }

        $webPush = new WebPush($auth);
        foreach ($subscriptions as $notification) {
          $webPush->sendNotification($notification['subscription'], $notification['payload']);
        }
        foreach ($webPush->flush(60) as $key => $report) {
          $endpoint = $report->getRequest()->getUri()->__toString();
          if(!empty($subscriptions[$key]['oldfcm'])){
            $response = json_decode($report->getResponse()->getBody(), true);
            if(!empty($response['failure'])){
              if (isset($response['results'][0]['error'])) {
                if ($response['results'][0]['error'] == 'InvalidRegistration' || $response['results'][0]['error'] == 'NotRegistered' || $response['results'][0]['error'] == 'MismatchSenderId') {
                  $webpushfail++;
                  $invalidDevs[] = $device_token['id'][$key];
                }
              }
              elseif (isset($response['results'][0]['registration_id'])) {
                $webpushfail++;
                $invalidDevs[] = $device_token['id'][$key];
              }
              if(smpush_env == 'debug'){
                self::log("[x] Message failed to sent for subscription {$endpoint}: ".$response['results'][0]['error']);
              }
              continue;
            }
          }
          if ($report->isSuccess()) {
            if(smpush_env == 'debug'){
              self::log("[v] Message sent successfully for subscription {$endpoint}");
            }
          } else {
            $webpushfail++;
            $invalidDevs[] = $device_token['id'][$key];
            if(smpush_env == 'debug'){
              self::log("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
            }
          }
        }
      }

      if (isset($device_token['id'])) {
        self::updateStats($device_type.'send', count($device_token['id']), $cronjob, $msgid);
        self::updateStats($device_type.'fail', $webpushfail, $cronjob, $msgid);
        if ($cronjob === false) {
          $ids = implode(',', $device_token['queue_id']);
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($ids)");
        }
        if (self::$sendoptions['feedback'] == 1 && !empty($invalidDevs)) {
          self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name} IN(".implode(',', $invalidDevs).")"));
        }
      }

      if ($device_type == 'chrome' && !empty($_GET['chrome_notify'])) {
        self::jsonPrint('chrome_server_reponse', array('message' => '<p>'.__('Connection With Chrome server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Chrome server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'opera' && !empty($_GET['opera_notify'])) {
        self::jsonPrint('opera_server_reponse', array('message' => '<p>'.__('Connection With Opera server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Opera server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'samsung' && !empty($_GET['samsung_notify'])) {
        self::jsonPrint('samsung_server_reponse', array('message' => '<p>'.__('Connection With Samsung Browser server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Samsung Browser server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'edge' && !empty($_GET['edge_notify'])) {
        self::jsonPrint('edge_server_reponse', array('message' => '<p>'.__('Connection With Edge server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Edge server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'firefox' && !empty($_GET['firefox_notify'])) {
        self::jsonPrint('firefox_server_reponse', array('message' => '<p>'.__('Connection With Firefox server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Firefox server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'chrome' || $device_type == 'opera' || $device_type == 'samsung' || $device_type == 'edge') {
      if(!empty(self::$sendoptions['msgid'])){
        $webInsertSql = '';
        foreach($device_token['token'] as $token){
          $webInsertSql .= "('".self::$sendoptions['msgid']."','".md5($token)."','".$device_type."'),";
        }
        $wpdb->query("INSERT INTO ".$wpdb->prefix."push_desktop_messages (`msgid`,`token`,`type`) VALUES ".rtrim($webInsertSql, ',').';');
      }
      $baseurl = 'https://fcm.googleapis.com/fcm/send';
      $data = array('message' => $message);
      $fields = array('registration_ids' => $device_token['token'], 'data' => $data);
      if (self::$sendoptions['expire'] > 0) {
        $fields['time_to_live'] = self::$sendoptions['expire'] * 3600;
      }
      $headers = array('Authorization: key='.self::$apisetting['chrome_apikey'], 'Content-Type: application/json');
      if (!function_exists('curl_init') && $showerror)
        self::jsonPrint(0, '<p class="error">Google: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'</p>');
      elseif (!function_exists('curl_init'))
        return;
      $chandle = curl_init();
      curl_setopt($chandle, CURLOPT_URL, $baseurl);
      curl_setopt($chandle, CURLOPT_POST, true);
      curl_setopt($chandle, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
      if(defined('WP_PROXY_HOST')){
        curl_setopt($chandle, CURLOPT_PROXY, WP_PROXY_HOST);
        curl_setopt($chandle, CURLOPT_PROXYPORT, WP_PROXY_PORT);
        curl_setopt($chandle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        if(defined('WP_PROXY_USERNAME')){
          curl_setopt($chandle, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
          curl_setopt($chandle, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
        }
      }
      curl_setopt($chandle, CURLOPT_POSTFIELDS, json_encode($fields, defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0));
      if(smpush_env == 'debug'){
        $result = true;
        $httpcode = 200;
        self::log($device_token['token']);
        self::log($data);
      }
      else{
        $result = curl_exec($chandle);
        $httpcode = curl_getinfo($chandle, CURLINFO_HTTP_CODE);
      }
      if ($result === FALSE && $showerror) {
        self::jsonPrint(0, '<p class="error">'.__('Chrome push notification server not responding', 'smpush-plugin-lang').'</p>');
      }
      elseif ($httpcode == 503 && $showerror) {
        self::jsonPrint(0, '<p class="error">'.__('Chrome push notification server not responding', 'smpush-plugin-lang').'</p>');
      }
      elseif ($httpcode == 401 && $showerror) {
        $result = json_decode($result, true);
        if (!empty($result['results'][0]['error']))
          self::jsonPrint(0, '<p class="error">'.$result['results'][0]['error'].'</p>');
        else
          self::jsonPrint(0, '<p class="error">'.__('Invalid Chrome API key', 'smpush-plugin-lang').'</p>');
      }
      else{
        $result = json_decode($result, true);
      }
      if (isset($device_token['id'])) {
        self::updateStats($device_type.'send', count($device_token['id']), $cronjob, $msgid);
        if ($cronjob === false) {
          $ids = implode(',', $device_token['queue_id']);
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($ids)");
        }
        if (self::$sendoptions['feedback'] == 1) {
          $chromefail = 0;
          if(!empty($result['results'])){
            foreach ($result['results'] AS $key => $status) {
              if (isset($status['error'])) {
                if ($status['error'] == 'InvalidRegistration' || $status['error'] == 'NotRegistered' || $status['error'] == 'MismatchSenderId') {
                  self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='".$device_token['id'][$key]."'"));
                  $chromefail++;
                }
              }
              elseif (isset($status['registration_id'])) {
                self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='".$device_token['id'][$key]."'"));
                $chromefail++;
              }
            }
          }
          self::updateStats($device_type.'fail', $chromefail, $cronjob, $msgid);
        }
      }
      curl_close($chandle);
      if ($device_type == 'chrome' && !empty($_GET['chrome_notify'])) {
        self::jsonPrint('chrome_server_reponse', array('message' => '<p>'.__('Connection With Chrome server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Chrome server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'opera' && !empty($_GET['opera_notify'])) {
        self::jsonPrint('opera_server_reponse', array('message' => '<p>'.__('Connection With Opera server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Opera server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'samsung' && !empty($_GET['samsung_notify'])) {
        self::jsonPrint('samsung_server_reponse', array('message' => '<p>'.__('Connection With Samsung Browser server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Samsung Browser server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
      elseif ($device_type == 'edge' && !empty($_GET['edge_notify'])) {
        self::jsonPrint('edge_server_reponse', array('message' => '<p>'.__('Connection With Edge server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Edge server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'firefox') {
      $fails = 0;
      $baseurl = 'https://updates.push.services.mozilla.com/wpush/v1/';
      if (!function_exists('curl_init') && $showerror)
        self::jsonPrint(0, '<p class="error">Firefox: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'</p>');
      elseif (!function_exists('curl_init'))
        return;
      if(!empty(self::$sendoptions['msgid'])){
        $webInsertSql = '';
        foreach($device_token['token'] as $token){
          $webInsertSql .= "('".self::$sendoptions['msgid']."', '".md5($token)."', '".$device_type."'),";
        }
        $wpdb->query('INSERT INTO '.$wpdb->prefix.'push_desktop_messages (`msgid`,`token`,`type`) VALUES '.rtrim($webInsertSql, ',')).';';
      }
      foreach($device_token['token'] as $key => $token){
        $chandle = curl_init();
        curl_setopt($chandle, CURLOPT_URL, $baseurl.$token);
        curl_setopt($chandle, CURLOPT_HTTPHEADER, array('TTL: 5184000'));
        curl_setopt($chandle, CURLOPT_POST, TRUE);
        curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
        if(defined('WP_PROXY_HOST')){
          curl_setopt($chandle, CURLOPT_PROXY, WP_PROXY_HOST);
          curl_setopt($chandle, CURLOPT_PROXYPORT, WP_PROXY_PORT);
          curl_setopt($chandle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
          if(defined('WP_PROXY_USERNAME')){
            curl_setopt($chandle, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
            curl_setopt($chandle, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
          }
        }
        if(smpush_env == 'debug'){
          $result = true;
          $httpcode = 200;
          self::log('sent to: '.$token);
        }
        else{
          $result = curl_exec($chandle);
          $httpcode = curl_getinfo($chandle, CURLINFO_HTTP_CODE);
        }
        if (($httpcode == 404 || $httpcode == 410) && self::$sendoptions['feedback'] == 1) {
          self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {id_name}='".$device_token['id'][$key]."'"));
          $fails++;
        }
        curl_close($chandle);
      }
      if (isset($device_token['id'])) {
        self::updateStats('firefoxsend', count($device_token['id']), $cronjob, $msgid);
        self::updateStats('firefoxfail', $fails, $cronjob, $msgid);
        if ($cronjob === false) {
          $ids = implode(',', $device_token['queue_id']);
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($ids)");
        }
      }
      if (!empty($_GET['firefox_notify'])) {
        self::jsonPrint('firefox_server_reponse', array('message' => '<p>'.__('Connection With Firefox server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Firefox server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
      }
    }
    elseif ($device_type == 'fbmsn') {
      if (!function_exists('curl_init') && $showerror)
        self::jsonPrint(0, '<p class="error">Facebook Messenger: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'</p>');
      elseif (!function_exists('curl_init'))
        return;
      $message = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", stripslashes(self::$sendoptions['fbmsn_message'])), ENT_QUOTES, 'UTF-8');
      $message = self::processLinks($message, $device_type, false);
      foreach ($device_token AS $key => $sDevice) {
        $params = array(
        'access_token' => self::$apisetting['msn_accesstoken'],
        'recipient' => json_encode(array('id' => $sDevice['token'])),
        'message' => json_encode(array('text' => $message))
        );
        if(!empty(self::$sendoptions['fbmsn_subject']) && !empty(self::$sendoptions['fbmsn_message']) && !empty(self::$sendoptions['fbmsn_button'])){
          $msnTemplate = array();
          $msnTemplate['attachment'] = array();
          $msnTemplate['attachment']['type'] = 'template';
          $msnTemplate['attachment']['payload'] = array(
            'template_type' => 'generic',
            'elements' => array(0 => array(
              'title' => self::cleanString(self::$sendoptions['fbmsn_subject'], true),
              'image_url' => (!empty(self::$sendoptions['fbmsn_image']))? self::cleanString(urldecode(self::$sendoptions['fbmsn_image'])) : '',
              'subtitle' => $message,
              'buttons' => array(0 => array(
                'type' => 'web_url',
                'url' => $siteurl.'/'.self::$apisetting['push_basename'].'/get_link/?id='.self::$sendoptions['msgid'].'&platform=fbmsn',
                'title' => self::cleanString(self::$sendoptions['fbmsn_button']),
              )),
              'default_action' => array(
                'type' => 'web_url',
                'url' => $siteurl.'/'.self::$apisetting['push_basename'].'/get_link/?id='.self::$sendoptions['msgid'].'&platform=fbmsn',
                'messenger_extensions' => 'FALSE',
                'webview_height_ratio' => 'COMPACT',
              )
            ))
          );
          $params['message'] = json_encode($msnTemplate);
        }
        elseif(empty(self::$sendoptions['fbmsn_subject']) && !empty(self::$sendoptions['fbmsn_message']) && empty(self::$sendoptions['fbmsn_button'])){
        }
        elseif(empty(self::$sendoptions['fbmsn_subject']) && !empty(self::$sendoptions['fbmsn_image']) && empty(self::$sendoptions['fbmsn_button'])){
          if(!empty(self::$sendoptions['fbmsn_image'])){
            self::facebookAttatchMedia(self::cleanString(urldecode(self::$sendoptions['fbmsn_image'])));

            if(!empty(self::$fbattachment['attachment_id'])){
              $msnTemplate = array();
              $msnTemplate['attachment'] = array();
              $msnTemplate['attachment']['type'] = 'template';
              $msnTemplate['attachment']['payload'] = array(
                'template_type' => 'media',
                'elements' => array(0 => array(
                  'media_type' => 'image',
                  'attachment_id' => self::$fbattachment['attachment_id'],
                ))
              );
              $params['message'] = json_encode($msnTemplate);
            }
          }
        }
        elseif(!empty(self::$sendoptions['desktop_title'])){
          $msnTemplate = array();
          $msnTemplate['attachment'] = array();
          $msnTemplate['attachment']['type'] = 'template';
          $msnTemplate['attachment']['payload'] = array(
            'template_type' => 'generic',
            'elements' => array(0 => array(
              'title' => self::cleanString(self::$sendoptions['desktop_title'], true),
              'image_url' => (!empty(self::$sendoptions['desktop_icon']))? self::cleanString(urldecode(self::$sendoptions['desktop_icon'])) : '',
              'subtitle' => $message,
              'buttons' => array(0 => array(
                'type' => 'web_url',
                'url' => $siteurl.'/'.self::$apisetting['push_basename'].'/get_link/?id='.self::$sendoptions['msgid'].'&platform=fbmsn',
                'title' => __('Check Out', 'smpush-plugin-lang'),
              )),
              'default_action' => array(
                'type' => 'web_url',
                'url' => $siteurl.'/'.self::$apisetting['push_basename'].'/get_link/?id='.self::$sendoptions['msgid'].'&platform=fbmsn',
                'messenger_extensions' => 'FALSE',
                'webview_height_ratio' => 'COMPACT',
              )
            ))
          );
          $params['message'] = json_encode($msnTemplate);
        }
        if(smpush_env == 'debug'){
          self::log('sent to: '.$sDevice['token']);
          self::log($message);
          self::updateStats('fbmsnsend', 1, $cronjob, $msgid);
        }
        else{
          $response = json_decode($helper->buildCurl('https://graph.facebook.com/v3.3/me/messages', false, $params), true);
          if($helper->curl_status == 200){
            self::updateStats('fbmsnsend', 1, $cronjob, $msgid);
          }
          elseif($helper->curl_status == 400 && isset($response['error']['message']) && $response['error']['code'] == 190){
            self::jsonPrint(0, '<p class="error">'.'Facebook Messenger: '.$response['error']['message'].'</p>');
          }
          elseif($helper->curl_status == 400 && isset($response['error']['message']) && $response['error']['code'] == 551){
            self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($sDevice['token'])."' AND {type_name}='fbmsn'"));
            self::updateStats('fbmsnsend', 1, $cronjob, $msgid);
            self::updateStats('fbmsnfail', 1, $cronjob, $msgid);
          }
          else{
            self::updateStats('fbmsnsend', 1, $cronjob, $msgid);
          }
        }
        if (!empty($_GET['fbmsn_notify'])) {
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id='$sDevice[queue_id]'");
          self::jsonPrint('fbmsn_server_reponse', array('message' => '<p>'.__('Connection With Facebook Messenger server established successfully', 'smpush-plugin-lang').'</p><p>'.__('Facebook Messenger server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
        }
        if ($cronjob === false) {
          $idsToDel[] = $sDevice['queue_id'];
        }
      }
      if ($cronjob === false) {
        $idsToDel = implode(',', $idsToDel);
        $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($idsToDel)");
      }
    }
    elseif ($device_type == 'fbnotify') {
      foreach ($device_token AS $key => $sDevice) {
        if (!function_exists('curl_init') && $showerror)
          self::jsonPrint(0, '<p class="error">Facebook Notification: '.__('CURL Library is not support in your host', 'smpush-plugin-lang').'</p>');
        elseif (!function_exists('curl_init'))
          return;
        $message = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", stripslashes(self::$sendoptions['fbnotify_message'])), ENT_QUOTES, 'UTF-8');
        if(self::$sendoptions['fbnotify_openaction'] == 'outside'){
          $link = '&outlink='.urlencode(self::processLinks(self::$sendoptions['fbnotify_link'], $device_type, false));
        }
        else{
          $link = '&inapp='.urlencode(self::processLinks(self::$sendoptions['fbnotify_link'], $device_type, false));
        }
        $params = array(
        'access_token' => self::$apisetting['fbnotify_appid'].'|'.self::$apisetting['fbnotify_secret'],
        'href' => $link,
        'template' => $message
        );
        if(smpush_env == 'debug'){
          self::log('sent to: '.$sDevice['token']);
          self::log($message);
          self::log($link);
          self::updateStats('fbnotifysend', 1, $cronjob, $msgid);
        }
        else{
          $response = json_decode($helper->buildCurl('https://graph.facebook.com/'.$sDevice['token'].'/notifications', false, $params), true);
          if(!empty($response['success'])){
            self::updateStats('fbnotifysend', 1, $cronjob, $msgid);
          }
          elseif(!empty($response['error']['message']) && $response['error']['message'] == '(#200) Cannot send notifications to a user who has not installed the app'){
            self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($sDevice['token'])."' AND {type_name}='fbnotify'"));
            self::updateStats('fbnotifysend', 1, $cronjob, $msgid);
            self::updateStats('fbnotifyfail', 1, $cronjob, $msgid);
          }
          elseif($helper->curl_status == 400 && isset($response['error']['message']) && $response['error']['code'] == 190){
            self::jsonPrint(0, '<p class="error">'.'Facebook Messenger: '.$response['error']['message'].'</p>');
          }
          //elseif(!empty($response['error']['message'])){
            //return self::jsonPrint(0, '<p class="error">'.$response['error']['message'].'</p>');
          //}
          else{
            self::updateStats('fbnotifysend', 1, $cronjob, $msgid);
          }
        }
        if (!empty($_GET['fbnotify_notify'])) {
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id='$sDevice[queue_id]'");
          self::jsonPrint('fbnotify_server_reponse', array('message' => '<p>'.__('Connection With Facebook notifications established successfully', 'smpush-plugin-lang').'</p><p>'.__('Facebook notifications server accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
        }
        if ($cronjob === false) {
          $idsToDel[] = $sDevice['queue_id'];
        }
      }
      if ($cronjob === false) {
        $idsToDel = implode(',', $idsToDel);
        $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($idsToDel)");
      }
    }
    elseif ($device_type == 'email') {
      self::$sendoptions['email'] = self::processLinks(htmlspecialchars_decode(stripslashes(self::$sendoptions['email'])), $device_type, true);
      self::$sendoptions['email_subject'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", stripslashes(self::$sendoptions['email_subject'])), ENT_QUOTES, 'UTF-8');
      foreach ($device_token AS $key => $sDevice) {
        $emailcontents = self::$sendoptions['email'];
        $devID = (empty($sDevice['id'])) ? base64_encode($sDevice['token']) : $sDevice['id'];
        $emailcontents .= '<img style="width: 0px; max-height: 0px; overflow: hidden; display: none;" src="'.self::processLinks(false, $device_type, false, 'tracking', $devID).'" border="0">';
        $emailcontents = str_replace('{unsubscribe_link}', self::processLinks(false, $device_type, false, 'unsubscribe', base64_encode($sDevice['token'])), $emailcontents);
        $emailcontents = str_replace('{DEVICE_ID}', $sDevice['id'], $emailcontents);
        if(smpush_env == 'debug'){
          self::log('sent to: '.$sDevice['token']);
          self::log(self::$sendoptions['email_subject']);
          self::log(array('From: '.self::$sendoptions['email_fname'].' <'.self::$sendoptions['email_sender'].'>'));
          self::log($emailcontents);
          self::updateStats('emailsend', 1, $cronjob, $msgid);
        }
        else{
          wp_mail($sDevice['token'], self::$sendoptions['email_subject'], $emailcontents, array('Content-Type: text/html; charset=UTF-8','From: '.self::$sendoptions['email_fname'].' <'.self::$sendoptions['email_sender'].'>'));
          self::updateStats('emailsend', 1, $cronjob, $msgid);
        }
        if (!empty($_GET['email_notify'])) {
          $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id='$sDevice[queue_id]'");
          self::jsonPrint('email_server_reponse', array('message' => '<p>'.__('Email is authenticated successfully', 'smpush-plugin-lang').'</p><p>'.__('Email accepts the payload and start working', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
        }
        if ($cronjob === false) {
          $idsToDel[] = $sDevice['queue_id'];
        }
      }
      if ($cronjob === false) {
        $idsToDel = implode(',', $idsToDel);
        $wpdb->query("DELETE FROM ".$wpdb->prefix."push_queue WHERE id IN($idsToDel)");
      }
    }
  }
  
  private static function processLinks($message, $devicetype, $htmlEnabled, $method='go', $deviceID=''){
    if(empty(self::$wpurl)){
      self::$wpurl = rtrim(get_bloginfo('wpurl'), '/').'/';
    }
    $wpurl = self::$wpurl;
    if(in_array($method, ['tracking','go']) && file_exists(ABSPATH.'/smart_bridge.php')){
      $wpurl .= 'smart_bridge.php';
    }
    if($htmlEnabled){
      $pattern = '/<a[^>]+href=([\'"])http(?<href>.+?)\1[^>]*>/i';
    }
    else{
      $pattern = '@http(s)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
    }
    if($message === false){
      $message = $wpurl.'?smpushcontrol='.$method.'&id='.self::$sendoptions['msgid'].'&platform='.$devicetype.'&deviceid='.$deviceID;
    }
    else{
      $message = preg_replace_callback($pattern, function($m) use($htmlEnabled, $devicetype, $method, $wpurl) {
        if($htmlEnabled){
          //return '<a href="'.$wpurl.'/'.self::$apisetting['push_basename'].'/'.$method.'/?id='.self::$sendoptions['msgid'].'&platform='.$devicetype.'&deviceid={DEVICE_ID}&target='.urlencode('http'.$m['href']).'">';
          return '<a href="'.$wpurl.'?smpushcontrol='.$method.'&id='.self::$sendoptions['msgid'].'&platform='.$devicetype.'&deviceid={DEVICE_ID}&target='.urlencode('http'.$m['href']).'">';
        }
        else{
          $paramDevice = (!empty($deviceID))? '&deviceid='.$deviceID : '';
          //return $wpurl.'/'.self::$apisetting['push_basename'].'/'.$method.'/?id='.self::$sendoptions['msgid'].'&platform='.$devicetype.'&target='.urlencode($m[0]).$paramDevice;
          return $wpurl.'?smpushcontrol='.$method.'&id='.self::$sendoptions['msgid'].'&platform='.$devicetype.'&target='.urlencode($m[0]).$paramDevice;
        }
      }, $message);
    }
    return $message;
  }

  public static function facebookAttatchMedia($img_link) {
    if(!empty(self::$fbattachment)){
      return;
    }
    $msnTemplate = array();
    $msnTemplate['attachment'] = array();
    $msnTemplate['attachment']['type'] = 'image';
    $msnTemplate['attachment']['payload'] = array(
      "is_reusable" => true,
      'url' => $img_link
    );
    $attach_params = [];
    $attach_params['message'] = json_encode($msnTemplate);
    $helper = new smpush_helper();
    self::$fbattachment = json_decode($helper->buildCurl('https://graph.facebook.com/v3.3/me/message_attachments?access_token='.self::$apisetting['msn_accesstoken'], false, $attach_params), true);
  }

  public static function connectFeedback($all_count, $cronjob = false, $msgid = 0) {
    global $wpdb;
    self::$cronSendOperation = $cronjob;
    if ($cronjob === true) {
      smpush_helper::$returnValue = 'cronjob';
    }
    $feedbacks = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."push_feedback WHERE msgid='$msgid'");
    if(empty($feedbacks)){
      return;
    }
    foreach ($feedbacks AS $feedback) {
      $fail = $androidfail = $chromefail = 0;
      if ($feedback->device_type == 'ios_invalid') {
        self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($feedback->tokens)."'"));
        self::updateStats('iosfail', 1, $cronjob, $msgid);
      }
      elseif ($feedback->device_type == 'safari_invalid') {
        self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($feedback->tokens)."'"));
        self::updateStats('safarifail', 1, $cronjob, $msgid);
      }
      elseif ($feedback->device_type == 'android' || $feedback->device_type == 'iosfcm') {
        if ($feedback->device_type == 'android' && !empty($_GET['feedback_google'])) {
          self::jsonPrint(5, '<p>'.__('Start processing Google feedback queries', 'smpush-plugin-lang').'</p>');
        }
        elseif ($feedback->device_type == 'iosfcm' && !empty($_GET['feedback_iosfcm'])) {
          self::jsonPrint(8, '<p>'.__('Start processing iOS FCM feedback queries', 'smpush-plugin-lang').'</p>');
        }
        $tokens = unserialize($feedback->tokens);
        $result = json_decode($feedback->feedback, true);
        if(!empty($result['results'])){
          foreach ($result['results'] AS $key => $status) {
            if (isset($status['error'])) {
              if ($status['error'] == 'InvalidRegistration' || $status['error'] == 'NotRegistered' || $status['error'] == 'MismatchSenderId') {
                self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($tokens[$key])."'"));
                $androidfail++;
              }
            }
            elseif (isset($status['registration_id'])) {
              self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($tokens[$key])."'"));
              $androidfail++;
            }
          }
        }
        self::updateStats($feedback->device_type.'fail', $androidfail, $cronjob, $msgid);
      }
      elseif ($feedback->device_type == 'chrome' || $feedback->device_type == 'opera' || $feedback->device_type == 'samsung' || $feedback->device_type == 'edge') {
        if (!empty($_GET['feedback_chrome'])) {
          self::jsonPrint(6, '<p>'.__('Start processing Chrome feedback queries', 'smpush-plugin-lang').'</p>');
        }
        $tokens = unserialize($feedback->tokens);
        $result = json_decode($feedback->feedback, true);
        if(!empty($result['results'])){
          foreach ($result['results'] AS $key => $status) {
            if (isset($status['error'])) {
              if ($status['error'] == 'InvalidRegistration' || $status['error'] == 'NotRegistered' || $status['error'] == 'MismatchSenderId') {
                self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($tokens[$key])."'"));
                $chromefail++;
              }
            }
            elseif (isset($status['registration_id'])) {
              self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($tokens[$key])."'"));
              $chromefail++;
            }
          }
        }
        self::updateStats($feedback->device_type.'fail', $chromefail, $cronjob, $msgid);
      }
      elseif ($feedback->device_type == 'firefox') {
        self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($feedback->tokens)."'"));
        self::updateStats('firefoxfail', 1, $cronjob, $msgid);
      }
      elseif ($feedback->device_type == 'ios' && self::$apisetting['apple_api_ver'] == 'ssl' && !empty(self::$apisetting['apple_cert_path']) && !empty(self::$apisetting['apple_passphrase'])) {
        if (!empty($_GET['feedback_open'])) {
          self::jsonPrint(4, '<p>'.__('Start connection and reading with Apple feedback server, Maybe takes some time', 'smpush-plugin-lang').'</p>');
        }
        if(smpush_env != 'debug'){        
          $ctx = stream_context_create();
          stream_context_set_option($ctx, 'ssl', 'local_cert', self::$apisetting['apple_cert_path']);
          stream_context_set_option($ctx, 'ssl', 'passphrase', self::$apisetting['apple_passphrase']);
          if (self::$apisetting['apple_sandbox'] == 1) {
            $appleserver = 'tls://feedback.sandbox.push.apple.com:2196';
          } else {
            $appleserver = 'tls://feedback.push.apple.com:2196';
          }
          @$fpssl = stream_socket_client($appleserver, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);
          if (!$fpssl) {
            if (empty($errstr))
              $errstr = __('Apple certification error or problem with Password phrase', 'smpush-plugin-lang');
            if ($err == 111)
              $errstr .= __(' - Contact your host to enable outgoing ports', 'smpush-plugin-lang');
            self::jsonPrint(0, '<p class="error">'.__('Could not establish connection with SSL server', 'smpush-plugin-lang').': '.$errstr.'</p>');
          }
          $nFeedbackTupleLen = self::TIME_BINARY_SIZE + self::TOKEN_LENGTH_BINARY_SIZE + self::DEVICE_BINARY_SIZE;
          $sBuffer = '';
          while (!feof($fpssl)) {
            $sBuffer .= $sCurrBuffer = fread($fpssl, 8192);
            $nCurrBufferLen = strlen($sCurrBuffer);
            unset($sCurrBuffer, $nCurrBufferLen);
            $nBufferLen = strlen($sBuffer);
            if ($nBufferLen >= $nFeedbackTupleLen) {
              $nFeedbackTuples = floor($nBufferLen / $nFeedbackTupleLen);
              for ($i = 0; $i < $nFeedbackTuples; $i++) {
                $sFeedbackTuple = substr($sBuffer, 0, $nFeedbackTupleLen);
                $sBuffer = substr($sBuffer, $nFeedbackTupleLen);
                $aFeedback = self::_parseBinaryTuple($sFeedbackTuple);
                self::$pushdb->query(self::parse_query("UPDATE {tbname} SET {active_name}='0' WHERE {md5token_name}='".md5($aFeedback['deviceToken'])."'"));
                $fail++;
                unset($aFeedback);
              }
            }
            $read = array($fpssl);
            $null = NULL;
            $nChangedStreams = stream_select($read, $null, $null, 0, 1000000);
            if ($nChangedStreams === false) {
              break;
            }
          }
          self::updateStats('iosfail', $fail, $cronjob, $msgid);
          if ($fail > 0) {
            self::jsonPrint(2, array('message' => '<p>'.__('Reading from Apple feedback is finised, try to read again for more', 'smpush-plugin-lang').'</p>', 'all_count' => $all_count));
          }
        }
      }
      $wpdb->query("DELETE FROM ".$wpdb->prefix."push_feedback WHERE id='".$feedback->id."'");
    }
  }

  protected static function getWebPushPayload($message, $device_type) {
    $siteurl = get_bloginfo('wpurl');
    $messagePayload['title'] = self::cleanString(self::$sendoptions['desktop_title'], true);
    $messagePayload['body'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($message)), ENT_NOQUOTES, 'UTF-8');
    $target = $siteurl.'/'.self::$apisetting['push_basename'].'/get_link/?id='.self::$sendoptions['msgid'].'&platform='.$device_type;
    if(self::$apisetting['no_disturb'] == 1){
      $messagePayload['tag'] = $siteurl;
    }
    else{
      $messagePayload['tag'] = self::$sendoptions['msgid'];
    }
    $messagePayload['data']['target'] = $target;
    $messagePayload['renotify'] = (self::$apisetting['no_disturb'] == 1)? true : false;

    $messagePayload['icon'] = (!empty(self::$sendoptions['desktop_icon']))? self::cleanString(urldecode(self::$sendoptions['desktop_icon'])) : self::cleanString(self::$sendoptions['desktop_icon']);
    if(!empty(self::$sendoptions['desktop_actions'])){
      if(self::$apisetting['webpush_onesignal_payload'] == 1){
        $messagePayload['o'] = array();
        foreach(self::$sendoptions['desktop_actions']['id'] as $ackey => $action){
          //$messagePayload['actions'][$ackey]['action'] = htmlspecialchars_decode(self::$sendoptions['desktop_actions']['id'][$ackey]);
          $messagePayload['o'][$ackey]['i'] = 'button_' . $ackey;
          $messagePayload['o'][$ackey]['n'] = self::cleanString(self::$sendoptions['desktop_actions']['text'][$ackey]);
          $messagePayload['o'][$ackey]['p'] = self::cleanString(urldecode(self::$sendoptions['desktop_actions']['icon'][$ackey]));
          $desktop_link = self::cleanString(urldecode(self::$sendoptions['desktop_actions']['link'][$ackey]));
          $messagePayload['o'][$ackey]['u'] = $siteurl . '/' . self::$apisetting['push_basename'] . '/go/?id=' . self::$sendoptions['msgid'] . '&platform=' . $device_type . '&target=' . urlencode($desktop_link);
        }
      }
      $messagePayload['actions'] = array();
      foreach(self::$sendoptions['desktop_actions']['id'] as $ackey => $action){
        //$messagePayload['actions'][$ackey]['action'] = htmlspecialchars_decode(self::$sendoptions['desktop_actions']['id'][$ackey]);
        $messagePayload['actions'][$ackey]['action'] = 'button_' . $ackey;
        $messagePayload['actions'][$ackey]['title'] = self::cleanString(self::$sendoptions['desktop_actions']['text'][$ackey]);
        $messagePayload['actions'][$ackey]['icon'] = self::cleanString(urldecode(self::$sendoptions['desktop_actions']['icon'][$ackey]));
        $desktop_link = self::cleanString(urldecode(self::$sendoptions['desktop_actions']['link'][$ackey]));
        $messagePayload['data']['actions']['button_' . $ackey] = $siteurl . '/' . self::$apisetting['push_basename'] . '/go/?id=' . self::$sendoptions['msgid'] . '&platform=' . $device_type . '&target=' . urlencode($desktop_link);
      }
    }
    if(! empty(self::$sendoptions['desktop_dir']) && self::$sendoptions['desktop_dir'] != 'auto'){
      $messagePayload['dir'] = self::$sendoptions['desktop_dir'];
    }
    if(! empty(self::$sendoptions['desktop_silent'])){
      $messagePayload['silent'] = self::$sendoptions['desktop_silent'];
    }
    if(! empty(self::$sendoptions['desktop_vibrate'])){
      $messagePayload['vibrate'] = self::$sendoptions['desktop_vibrate'];
    }
    if(! empty(self::$sendoptions['desktop_bigimage'])){
      $messagePayload['image'] = self::cleanString(urldecode(self::$sendoptions['desktop_bigimage']));
    }
    if(! empty(self::$sendoptions['desktop_badge'])){
      $messagePayload['badge'] =self::cleanString(urldecode(self::$sendoptions['desktop_badge']));
    }
    if(! empty(self::$sendoptions['desktop_sound'])){
      $messagePayload['sound'] = self::cleanString(urldecode(self::$sendoptions['desktop_sound']));
    }
    $messagePayload['requireInteraction'] = (empty(self::$sendoptions['desktop_interaction']))? false : true;
    $messagePayload['command'] = 'fetch("'.$siteurl.'/'.self::$apisetting['push_basename'].'/views_tracker/?id='.self::$sendoptions['msgid'].'&platform='.$device_type.'");';
    if(self::$apisetting['webpush_onesignal_payload'] == 1){
      $messagePayload['custom']['i'] = '08474f98-236b-40cd-a367-242a24962896';
      $messagePayload['custom']['u'] = $target;
      $messagePayload['alert'] = $messagePayload['body'];
    }
    return json_encode($messagePayload);
  }

  protected static function _getPayload($message) {
    if (self::$apisetting['ios_titanium_payload'] == 1) {
      $aPayload['aps'] = array();
      $aPayload['aps']['alert'] = $message;
      if (!empty(self::$sendoptions['ios_sound'])) {
        $aPayload['aps']['sound'] = stripslashes(self::$sendoptions['ios_sound']);
      }
      if (!empty(self::$sendoptions['ios_badge'])) {
        $aPayload['aps']['badge'] = (int)self::$sendoptions['ios_badge'];
      }
      if (!empty(self::$sendoptions['extravalue'])) {
        if (self::$sendoptions['extra_type'] == 'normal') {
          $aPayload['relatedvalue'] = self::cleanString(self::$sendoptions['extravalue'], true);
        } else {
          $extravalue = json_decode(self::$sendoptions['extravalue'], true);
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $aPayload[$key] = self::cleanString($value, true);
            }
          }
        }
      }
    } else {
      $aPayload['aps'] = array();
      if (!empty(self::$sendoptions['ios_slide']) OR ! empty(self::$sendoptions['ios_launchimg']) OR ! empty(self::$sendoptions['desktop_title'])) {
        $aPayload['aps']['alert']['body'] = $message;
        if (!empty(self::$sendoptions['desktop_title'])) {
          $aPayload['aps']['mutable-content'] = 1;
          $aPayload['aps']['alert']['title'] = self::cleanString(self::$sendoptions['desktop_title'], true);
        }
        if (!empty(self::$sendoptions['ios_slide'])) {
          $aPayload['aps']['alert']['action-loc-key'] = stripslashes(self::$sendoptions['ios_slide']);
        }
        if (!empty(self::$sendoptions['ios_launchimg'])) {
          $aPayload['aps']['alert']['launch-image'] = stripslashes(self::$sendoptions['ios_launchimg']);
        }
      }
      else {
        $aPayload['aps']['alert'] = $message;
      }
      if (!empty(self::$sendoptions['ios_sound'])) {
        $aPayload['aps']['sound'] = stripslashes(self::$sendoptions['ios_sound']);
      }
      else{
        $aPayload['aps']['sound'] = 'default';
      }
      if (!empty(self::$sendoptions['ios_cavailable'])) {
        $aPayload['aps']['content-available'] = self::$sendoptions['ios_cavailable'];
      }
      if (!empty(self::$sendoptions['ios_badge'])) {
        $aPayload['aps']['badge'] = (int)self::$sendoptions['ios_badge'];
      }
      if (!empty(self::$sendoptions['extravalue'])) {
        if (self::$sendoptions['extra_type'] == 'normal') {
          if (self::$apisetting['android_corona_payload'] == 1) {
            $aPayload['aps']['custom'] = json_encode(array('relatedvalue' => self::cleanString(self::$sendoptions['extravalue'], true)), defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
          }
          else{
            $aPayload['aditional_param']['relatedvalue'] = self::cleanString(self::$sendoptions['extravalue'], true);
            $aPayload['relatedvalue'] = self::cleanString(self::$sendoptions['extravalue'], true);
          }
        }
        elseif (self::$apisetting['android_corona_payload'] == 1) {
          $aPayload['aps']['custom'] = self::cleanString(self::$sendoptions['extravalue'], true);
        }
        else {
          $extravalue = json_decode(self::$sendoptions['extravalue'], true);
          if ($extravalue) {
            foreach ($extravalue AS $key => $value) {
              $aPayload['aditional_param'][$key] = self::cleanString($value, true);
              $aPayload[$key] = self::cleanString($value, true);
            }
          }
        }
      }
    }
    if (self::$apisetting['android_corona_payload'] == 1 && !empty($aPayload['aps']['custom'])) {
      $aPayload['aps']['custom'] = array_merge(json_decode($aPayload['aps']['custom'], true), array('msgid' => self::$sendoptions['msgid']));
      $aPayload['aps']['custom'] = json_encode($aPayload['aps']['custom'], defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    }
    elseif (self::$apisetting['android_corona_payload'] == 1){
      $aPayload['aps']['custom'] = json_encode(array('msgid' => self::$sendoptions['msgid']), defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    }
    else{
      $aPayload['msgid'] = self::$sendoptions['msgid'];
    }
    return $aPayload;
  }

  protected static function getPayload($message) {
    $message = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", $message), ENT_NOQUOTES, 'UTF-8');
    if (phpversion() < 5.3 OR self::$apisetting['stop_summarize'] == 1) {
      return json_encode(self::_getPayload($message));
    }
    @$sJSON = json_encode(self::_getPayload($message), defined('JSON_UNESCAPED_UNICODE') ? JSON_UNESCAPED_UNICODE : 0);
    if (!defined('JSON_UNESCAPED_UNICODE') && function_exists('mb_convert_encoding')) {
      $sJSON = preg_replace_callback('~\\\\u([0-9a-f]{4})~i', create_function('$aMatches', 'return mb_convert_encoding(pack("H*", $aMatches[1]), "UTF-8", "UTF-16");'), $sJSON);
    }
    $sJSONPayload = str_replace('"aps":[]', '"aps":{}', $sJSON);
    $nJSONPayloadLen = strlen($sJSONPayload);
    if (self::$apisetting['apple_api_ver'] == 'http2') {
      $maxPayloadSize = 4000;
    }
    else{
      $maxPayloadSize = 2000;
    }
    if ($nJSONPayloadLen > $maxPayloadSize) {
      $nMaxTextLen = $nTextLen = strlen($message) - ($nJSONPayloadLen - $maxPayloadSize);
      if ($nMaxTextLen > 0) {
        while (strlen($message = mb_substr($message, 0,  --$nTextLen, 'UTF-8')) > $nMaxTextLen);
        return self::getPayload($message);
      } else {
        self::jsonPrint(0, '<p class="error">Apple notification message is too long: '.$nJSONPayloadLen.' bytes. Maximum size is 256 bytes</p>');
      }
    }
    return $sJSONPayload;
  }
  
  protected static function connectAPNS($deviceToken, $payload, $platform) {
    if (!defined('CURL_HTTP_VERSION_2_0')) {
      define('CURL_HTTP_VERSION_2_0', 3);
    }
    $chandle = curl_init();
    
    if($platform == 'safari'){
      $cert = self::$apisetting['safari_cert_path'];
      $passphrase = self::$apisetting['safari_passphrase'];
      $appid = self::$apisetting['safari_web_id'];
      $serverAPNS = 'https://api.push.apple.com/3/device/';
      $reqHeader = array('apns-topic: '.$appid);
      
      curl_setopt($chandle, CURLOPT_SSLCERT, $cert);
      curl_setopt($chandle, CURLOPT_SSLCERTPASSWD, $passphrase);
    }
    else{
      $cert = self::$apisetting['apple_cert_path'];
      $passphrase = self::$apisetting['apple_passphrase'];
      $appid = self::$apisetting['apple_appid'];
      if (self::$apisetting['apple_sandbox'] == 1) {
        $serverAPNS = 'https://api.development.push.apple.com/3/device/';
      }
      else {
        $serverAPNS = 'https://api.push.apple.com/3/device/';
      }
      
      if(self::$apisetting['apple_cert_type'] == 'pem'){
        $reqHeader = array('apns-topic: '.$appid);
        curl_setopt($chandle, CURLOPT_SSLCERT, $cert);
        curl_setopt($chandle, CURLOPT_SSLCERTPASSWD, $passphrase);
      }
      elseif(self::$apisetting['apple_cert_type'] == 'p8'){
        require_once(smpush_dir.'/lib/inc_jwt_helper.php');
        $helper = new smpush_helper();
        
        if(file_exists(smpush_cache_dir.'/jwt_header')){
          $header_jwt = $helper->readlocalfile(smpush_cache_dir.'/jwt_header');
        }
        else{
          $arParam = array();
          $arParam['teamId'] = self::$apisetting['apple_teamid'];
          $arParam['authKeyId'] = self::$apisetting['apple_keyid'];
          $arParam['apns-topic'] = $appid;
          $arClaim = ['iss' => $arParam['teamId'], 'iat'=>time()];
          $arParam['p_key'] = $helper->readlocalfile(self::$apisetting['apple_certp8_path']);
          try {
            $header_jwt = smpushJWT::encode($arClaim, $arParam['p_key'], $arParam['authKeyId'], 'RS256');
          } catch (Exception $e) {
            return('iOS authentication error: '.$e->getMessage());
          }
          $helper->storelocalfile(smpush_cache_dir.'/jwt_header', $header_jwt);
        }
        
        $reqHeader = array('apns-topic: '.$appid, 'authorization: bearer '.$header_jwt);
      }
    }
    curl_setopt($chandle, CURLOPT_URL, $serverAPNS.$deviceToken);
    curl_setopt($chandle, CURLOPT_PORT, 443);
    curl_setopt($chandle, CURLOPT_POST, true);
    curl_setopt($chandle, CURLOPT_HTTPHEADER, $reqHeader);
    curl_setopt($chandle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($chandle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($chandle, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($chandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
    if(defined('WP_PROXY_HOST')){
      curl_setopt($chandle, CURLOPT_PROXY, WP_PROXY_HOST);
      curl_setopt($chandle, CURLOPT_PROXYPORT, WP_PROXY_PORT);
      curl_setopt($chandle, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      if(defined('WP_PROXY_USERNAME')){
        curl_setopt($chandle, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
        curl_setopt($chandle, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
      }
    }
    $response = curl_exec($chandle);
    $httpcode = curl_getinfo($chandle, CURLINFO_HTTP_CODE);
    curl_close($chandle);

    if (!empty($response)) {
      $response = json_decode($response, true);
      if(isset(self::$apnsErrors[$response['reason']])){
        $failMSG = self::$apnsErrors[$response['reason']];
      }
      else{
        $failMSG = '';
      }
    }

    switch ($httpcode) {
      case 200:
        return true;
      case 400:
        if ($response['reason'] == 'BadDeviceToken' || $response['reason'] == 'DeviceTokenNotForTopic') {
          //invalid device token
          return false;
        } else {
          return($failMSG);
        }
        break;
      case 403:
        return($failMSG);
      case 404:
        return($failMSG);
      case 405:
        return($failMSG);
      case 410:
        //invalid device token
        return false;
      case 413:
        return($failMSG);
      case 429:
        //not received
        return -1;
      case 500:
        return($failMSG);
      case 503:
        return($failMSG);
      case 0:
        return('Server must be installed CURL version >= 7.46 and OpenSSL version >= 1.0.2e with HTTP/2 enabled');
    }
  }

  public static function updateStats($index = '', $value = 0, $cronjob = false, $archiveid=0) {
    if (self::$cronSendOperation === true OR $cronjob === true) {
      $transient = 'smpush_cron_stats_'.$archiveid;
    }
    else {
      $transient = 'smpush_stats';
    }
    if ($index == 'reset') {
      delete_option($transient);
      return;
    }
    if (empty($index)) {
      if (self::$cronSendOperation === false AND $cronjob === false) {
        $handler_options = get_option('smpush_instant_send');
        $archiveid = $handler_options['msgid'];
      }
      $stats = array('totalsend' => 0, 'iossend' => 0, 'iosfail' => 0, 'iosfcmsend' => 0, 'iosfcmfail' => 0, 'androidsend' => 0, 'androidfail' => 0, 'wpsend' => 0, 'wpfail' => 0, 'wp10send' => 0, 'wp10fail' => 0, 'bbsend' => 0, 'bbfail' => 0, 'chromesend' => 0, 'chromefail' => 0, 'safarisend' => 0, 'safarifail' => 0, 'firefoxsend' => 0, 'firefoxfail' => 0, 'operasend' => 0, 'operafail' => 0, 'edgesend' => 0, 'edgefail' => 0, 'samsungsend' => 0, 'samsungfail' => 0, 'fbmsnsend' => 0, 'fbmsnfail' => 0, 'fbnotifysend' => 0, 'fbnotifyfail' => 0, 'emailsend' => 0, 'emailfail' => 0, 'archiveid' => $archiveid);
      if(smpush_env == 'debug'){
        self::log('saving all stats: '.json_encode($stats));
      }
      update_option($transient, $stats, false);
      return;
    }
    $stats = get_option($transient);
    if ($index == 'all') {
      $stats['totalfail'] = $stats['iosfail'] + $stats['iosfcmfail'] + $stats['androidfail'] + $stats['wpfail'] + $stats['wp10fail'] + $stats['bbfail'] + $stats['chromefail'] + $stats['safarifail'] + $stats['firefoxfail'] + $stats['operafail'] + $stats['edgefail'] + $stats['samsungfail'] + $stats['fbmsnfail'] + $stats['fbnotifyfail'] + $stats['emailfail'];
      $archid = $stats['archiveid'];
      unset($stats['archiveid']);
      
      global $wpdb;
      $wpdb->update($wpdb->prefix.'push_archive', array('endtime' => gmdate('Y-m-d H:i:s', current_time('timestamp'))), array('id' => $archid));
      $wpdb->insert($wpdb->prefix.'push_archive_reports', array('msgid' => $archid, 'report_time' => current_time('timestamp'), 'report' => serialize($stats)));
      
      $message = $wpdb->get_row("SELECT send_type FROM ".$wpdb->prefix."push_archive WHERE id='$archid'", ARRAY_A);
      $current_date = gmdate('Y-m-d', current_time('timestamp'));

      if($message['send_type'] == 'now' || $message['send_type'] == 'custom' || $message['send_type'] == 'live'){
        $skey = 'smsg';
        $fkey = 'fmsg';
      }
      elseif($message['send_type'] == 'time'){
        $skey = 'sschmsg';
        $fkey = 'fschmsg';
      }
      elseif($message['send_type'] == 'geofence'){
        $skey = 'sgeomsg';
        $fkey = 'fgeomsg';
      }
      
      if(!empty($stats['safarisend'])){
        $stats['safariviews'] = $stats['safarisend']-$stats['safarifail'];
      }
      if(!empty($stats['fbmsnsend'])){
        $stats['fbmsnviews'] = $stats['fbmsnsend']-$stats['fbmsnfail'];
      }
      if(!empty($stats['fbnotifysend'])){
        $stats['fbnotifyviews'] = $stats['fbnotifysend']-$stats['fbnotifyfail'];
      }

      foreach(self::$platforms as $platform){
        if(empty($stats[$platform.'send'])){
          continue;
        }
        $pastvalues = array();
        $dbpastvalues = $wpdb->get_results("SELECT id,action,stat FROM ".$wpdb->prefix."push_statistics WHERE platid='$platform' AND `msgid`='$archid' AND `date`='$current_date'", ARRAY_A);
        if($dbpastvalues){
          foreach($dbpastvalues as $dbpastvalue){
            $pastvalues[$dbpastvalue['action']]['value'] = $dbpastvalue['stat'];
            $pastvalues[$dbpastvalue['action']]['id'] = $dbpastvalue['id'];
          }
        }
        $data = array();
        $data['platid'] = $platform;
        $data['msgid'] = $archid;
        $data['date'] = $current_date;

        if(isset($pastvalues[$skey])){
          $data['stat'] = $pastvalues[$skey]['value']+$stats[$platform.'send'];
          $wpdb->update($wpdb->prefix.'push_statistics', array('stat' => $data['stat']), array('id' => $pastvalues[$skey]['id']));
        }
        else{
          $data['action'] = $skey;
          $data['stat'] = $stats[$platform.'send'];
          $wpdb->insert($wpdb->prefix.'push_statistics', $data);
        }

        if(isset($pastvalues[$fkey])){
          $data['stat'] = $pastvalues[$fkey]['value']+$stats[$platform.'fail'];
          $wpdb->update($wpdb->prefix.'push_statistics', array('stat' => $data['stat']), array('id' => $pastvalues[$fkey]['id']));
        }
        else{
          $data['action'] = $fkey;
          $data['stat'] = $stats[$platform.'fail'];
          $wpdb->insert($wpdb->prefix.'push_statistics', $data);
        }

        if(isset($pastvalues['invdevice'])){
          $data['stat'] = $pastvalues['invdevice']['value']+$stats[$platform.'fail'];
          $wpdb->update($wpdb->prefix.'push_statistics', array('stat' => $data['stat']), array('id' => $pastvalues['invdevice']['id']));
        }
        else{
          $data['action'] = 'invdevice';
          $data['stat'] = $stats[$platform.'fail'];
          $wpdb->insert($wpdb->prefix.'push_statistics', $data);
        }
      }
      
      if(isset($pastvalues['views']) && isset($stats['safariviews'])){
        $data['stat'] = $pastvalues['views']['value']+$stats['safariviews'];
        $wpdb->update($wpdb->prefix.'push_statistics', array('stat' => $data['stat']), array('id' => $pastvalues['views']['id']));
      }
      elseif(isset($stats['safariviews'])){
        $data['action'] = 'views';
        $data['stat'] = $stats['safariviews'];
        $wpdb->insert($wpdb->prefix.'push_statistics', $data);
      }
      
      if(isset($pastvalues['views']) && isset($stats['fbmsnviews'])){
        $data['stat'] = $pastvalues['views']['value']+$stats['fbmsnviews'];
        $wpdb->update($wpdb->prefix.'push_statistics', array('stat' => $data['stat']), array('id' => $pastvalues['views']['id']));
      }
      elseif(isset($stats['fbmsnviews'])){
        $data['action'] = 'views';
        $data['stat'] = $stats['fbmsnviews'];
        $wpdb->insert($wpdb->prefix.'push_statistics', $data);
      }
      
      if(isset($pastvalues['views']) && isset($stats['fbnotifyviews'])){
        $data['stat'] = $pastvalues['views']['value']+$stats['fbnotifyviews'];
        $wpdb->update($wpdb->prefix.'push_statistics', array('stat' => $data['stat']), array('id' => $pastvalues['views']['id']));
      }
      elseif(isset($stats['fbnotifyviews'])){
        $data['action'] = 'views';
        $data['stat'] = $stats['fbnotifyviews'];
        $wpdb->insert($wpdb->prefix.'push_statistics', $data);
      }
      
      if (self::$cronSendOperation === true) {
        return $stats;
      }
      $result = self::printReport($stats);
      return self::jsonPrint(-1, $result);
    }
    if ($index == 'totalsend') {
      if ($stats[$index] > 0)
        return;
    }
    $stats[$index] = $stats[$index] + $value;
    if(smpush_env == 'debug'){
      self::log('update stats: '.$index.' with '.$value);
    }
    update_option($transient, $stats, false);
  }

  public static function printReport($stats) {
    if (isset($stats['error'])) {
      return '<p><strong>'.$stats['error'].'</strong></p>';
    }
    $result = '<p><strong>iOS '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['iossend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver or invalid tokens', 'smpush-plugin-lang').': '.$stats['iosfail'].' '.__('device token', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>iOS FCM '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['iosfcmsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver or invalid tokens', 'smpush-plugin-lang').': '.$stats['iosfcmfail'].' '.__('device token', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Android '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['androidsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['androidsend'] - $stats['androidfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['androidfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Windows Phone '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['wpsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['wpsend'] - $stats['wpfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['wpfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Blackberry '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['bbsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['bbsend'] - $stats['bbfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['bbfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Windows 10 '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['wp10send'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['wp10send'] - $stats['wp10fail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['wp10fail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Chrome '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['chromesend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['chromesend'] - $stats['chromefail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['chromefail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Safari '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['safarisend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['safarisend'] - $stats['safarifail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['safarifail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Firefox '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['firefoxsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['firefoxsend'] - $stats['firefoxfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['firefoxfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Opera '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['operasend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['operasend'] - $stats['operafail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['operafail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Edge '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['edgesend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['edgesend'] - $stats['edgefail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['edgefail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Samsung Browser '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['samsungsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['samsungsend'] - $stats['samsungfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['samsungfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Facebook Messenger '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['fbmsnsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['fbmsnsend'] - $stats['fbmsnfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['fbmsnfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Facebook Notification '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['fbnotifysend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['fbnotifysend'] - $stats['fbnotifyfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['fbnotifyfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>Newsletter '.__('Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent messages', 'smpush-plugin-lang').': '.$stats['emailsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Successful delivered', 'smpush-plugin-lang').': '.($stats['emailsend'] - $stats['emailfail']).' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver and invalid tokens', 'smpush-plugin-lang').': '.$stats['emailfail'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p><strong>'.__('Total Report', 'smpush-plugin-lang').':</strong></p>';
    $result .= '<p>'.__('Total sent', 'smpush-plugin-lang').': '.$stats['totalsend'].' '.__('message', 'smpush-plugin-lang').'</p>';
    $result .= '<p>'.__('Failure to deliver or invalid tokens', 'smpush-plugin-lang').': '.$stats['totalfail'].' '.__('device token', 'smpush-plugin-lang').'</p>';
    return $result;
  }

  public static function SendPush($ids, $message, $extravalue) {return;}
  
  protected static function _parseBinaryTuple($sBinaryTuple) {
    return unpack('Ntimestamp/ntokenLength/H*deviceToken', $sBinaryTuple);
  }
  
}