<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

use Minishlink\WebPush\VAPID;

class smpush_controller extends smpush_helper{
  public static $apisetting;
  public static $defconnection;
  public static $pushdb;
  public static $history;
  public static $data;
  public static $platforms = array('ios','iosfcm','android','wp','wp10','bb','chrome','safari','firefox','opera','samsung','fbmsn','fbnotify','email');
  public static $webPlatforms = array('chrome','safari','firefox','opera','edge','samsung');
  public static $mobilePlatforms = array('ios','iosfcm','android','wp','wp10','bb');
  public static $platform_titles = array('iOS','iOS FCM','Android','Windows Phone','Windows 10','BlackBerry','Chrome','Safari','Firefox','Opera','Edge','Samsung Browser','Messenger','FB Notifications','Newsletter');

  public function __construct(){
    $this->plugin_bootstrap();
    $this->set_def_connection();
    $this->cron_setup();
    $this->add_rewrite_rules();
    $this->initAction();
    $this->moveServiceWokrer();
    $this->initSupportAMP();

    if(self::$defconnection['dbtype'] == 'remote'){
      self::$pushdb = new wpdb(self::$defconnection['dbuser'], self::$defconnection['dbpass'], self::$defconnection['dbname'], self::$defconnection['dbhost']);
      if(!self::$pushdb){
        $this->output(0, __('Connecting with the remote push notification database is failed', 'smpush-plugin-lang'));
      }
    }
    else{
      global $wpdb;
      self::$pushdb = $wpdb;
    }
    self::$pushdb->hide_errors();
  }

  public function set_def_connection(){
    global $wpdb;
    self::$defconnection = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."push_connection WHERE id='".self::$apisetting['def_connection']."'", 'ARRAY_A');
  }

  public function initSupportAMP(){
    new smpush_amp(self::$apisetting);
  }

  public static function platformType($platform){
    if(in_array($platform, self::$webPlatforms)){
      return 'web';
    }
    elseif(in_array($platform, self::$mobilePlatforms)){
      return 'mobile';
    }
    else{
      return $platform;
    }
  }
  
  public static function parse_query($query){
    if(preg_match_all("/{([a-zA-Z0-9_]+)}/", $query, $matches)){
      foreach($matches[1] AS $match){
        if($match == 'ios_name' OR $match == 'iosfcm_name' OR $match == 'android_name' OR $match == 'wp_name' OR $match == 'wp10_name' OR $match == 'bb_name' OR $match == 'chrome_name' OR $match == 'safari_name' OR $match == 'firefox_name'
           OR $match == 'opera_name' OR $match == 'samsung_name' OR $match == 'edge_name' OR $match == 'fbmsn_name' OR $match == 'fbnotify_name' OR $match == 'email_name' OR $match == 'counter_name')
          $query = str_replace('{'.$match.'}', self::$defconnection[$match] , $query);
        elseif($match == 'tbname_temp')
          $query = str_replace('{'.$match.'}', '`'.self::$defconnection['tbname'].'_temp'.'`' , $query);
        else
          $query = str_replace('{'.$match.'}', '`'.self::$defconnection[$match].'`' , $query);
      }
    }
    $query = str_replace('{wp_prefix}', SMPUSHTBPRE, $query);
    return $query;
  }

  public static function setting(){
    if($_POST){
      self::saveOptions();
    }
    elseif (isset($_GET['loadtaxs'])) {
      if(empty($_GET['smiopush_post_type'])){
        echo '';
        exit;
      }
      $html = '<option value=""></option>';
      $taxonomy_objects = get_object_taxonomies($_GET['smiopush_post_type'], 'objects');
      foreach ($taxonomy_objects as $type => $object){
        $html .= '<option value="'.$type.'">'.$type.'</option>';
      }
      echo $html;
      exit;
    }
    elseif (isset($_GET['loadcats'])) {
      if(empty($_GET['smiopush_object_name'])){
        echo '';
        exit;
      }
      wp_terms_checklist(0, array('taxonomy' => $_GET['smiopush_object_name']));
      exit;
    }
    else{
      global $wpdb;
      $connections = $wpdb->get_results("SELECT id,title FROM ".$wpdb->prefix."push_connection ORDER BY id ASC");
      wp_enqueue_script('media-upload');
      wp_enqueue_script('thickbox');
      wp_enqueue_script('jquery');
      wp_enqueue_style('thickbox');
      wp_enqueue_style('wp-color-picker');
      wp_enqueue_script('wp-color-picker');
      //labelauty libs
      wp_enqueue_style('smpush-labelauty-style');
      wp_enqueue_script('smpush-jquery-labelauty');

      if(smpush_mobapp_mode && is_multisite() && ! is_super_admin()){
        $canEditApiKeys = false;
      }
      else{
        $canEditApiKeys = true;
      }

      $envErrors = array();
      if(phpversion() < 7.1){
        $envErrors[] = 'PHP version must be 7.1 or later.';
      }
      if(!function_exists('gmp_init')){
        $envErrors[] = 'GMP PHP extension must be installed and enabled.';
      }
      if(!function_exists('openssl_decrypt')){
        $envErrors[] = 'OpenSSL PHP extension must be installed and enabled.';
      }
      if(!function_exists('mb_check_encoding')){
        $envErrors[] = 'MBSTRING PHP extension must be installed and enabled.';
      }
      if(!function_exists('curl_init')){
        $envErrors[] = 'CURL PHP extension must be installed and enabled.';
      }

      self::loadpage('setting', 1, array('connections' => $connections, 'canEditApiKeys' => $canEditApiKeys, 'envErrors' => $envErrors));
    }
  }

  public static function documentation(){
    include(smpush_dir.'/class.documentation.php');
    self::load_jsplugins();
    $document = new smpush_documentation();
    $document = $document->build();
    $smpushexurl['auth_key'] = (self::$apisetting['complex_auth']==1)?md5(date('m/d/Y').self::$apisetting['auth_key'].date('H:i')):self::$apisetting['auth_key'];
    $smpushexurl['push_basename'] = get_bloginfo('wpurl') .'/'.self::$apisetting['push_basename'];
    include(smpush_dir.'/pages/documentation.php');
  }

  public static function loadpage($template, $noheader=0, $params=0){
    self::load_jsplugins();
    $noheader = ($noheader == 0)?'':'&noheader=1';
    $page_url = admin_url().'admin.php?page=smpush_'.$template.$noheader;
    include(smpush_dir.'/pages/'.$template.'.php');
  }

  public static function load_jsplugins(){
    wp_enqueue_style('smpush-style');
    if(is_rtl()){
      wp_enqueue_style('smpush-rtl');
    }
    wp_enqueue_script('smpush-mainscript');
    wp_enqueue_script('smpush-plugins');
  }

  public static function saveOptions(){
    if(smpush_env_demo){
      echo 1;
      die();
    }
    $newsetting = array();
    foreach($_POST AS $key=>$value){
      if(!in_array($key, array('submit'))){
        $newsetting[$key] = $value;
        unset(self::$apisetting[$key]);
      }
    }
    unset($newsetting['selectDIV']);
    
    $categories = array();
    if(!empty($_POST['post_category'])){
      foreach($_POST['post_category'] as $catid){
        $categories[] = $catid;
      }
    }
    elseif(!empty($_POST['tax_input'])){
      foreach($_POST['tax_input'] as $tax){
        if (!empty($tax)){
          foreach($tax as $catid){
            $categories[] = $catid;
          }
        }
      }
    }
    unset(self::$apisetting['post_category']);
    unset(self::$apisetting['tax_input']);
    self::$apisetting['subspage_category'] = $categories;
    
    $checkbox = array(
    'bb_notify_friends',
    'bb_notify_messages',
    'bb_notify_activity',
    'bb_notify_activity_admins_only',
    'bb_notify_xprofile',
    'desktop_status',
    'desktop_debug',
    'desktop_chrome_status',
    'desktop_firefox_status',
    'desktop_safari_status',
    'desktop_opera_status',
    'desktop_edge_status',
    'desktop_samsung_status',
    'desktop_modal',
    'desktop_logged_only',
    'desktop_admins_only',
    'desktop_gps_status',
    'auto_geo',
    'complex_auth',
    'ios_onebyone',
    'apple_sandbox',
    'wp_authed',
    'bb_dev_env',
    'android_titanium_payload',
    'android_corona_payload',
    'android_fcm_msg',
    'ios_titanium_payload',
    'smtp_status',
    'msn_widget_status',
    'e_post_chantocats',
    'e_apprpost',
    'e_appcomment',
    'e_newcomment',
    'e_newcomment_allusers',
    'e_usercomuser',
    'e_postupdated',
    'e_newpost',
    'e_newcomment_mentions',
    'stop_summarize',
    'metabox_check_status',
    'msn_official_widget_status',
    'fblogin_regin_newsletter',
    'fblogin_regin_fbnotifs',
    'fblogin_regin_wpuser',
    'desktop_welc_status',
    'e_wpjobman_status',
    'desktop_offline',
    'subspage_geo_status',
    'subspage_keywords',
    'subspage_channels',
    'subspage_cats_status',
    'subspage_plat_web',
    'subspage_plat_mobile',
    'subspage_plat_msn',
    'subspage_plat_email',
    'subspage_show_catimages',
    'msn_woo_checkout',
    'msn_woo_cartbtn',
    'subspage_matchone',
    'fast_bridge',
    'gdpr_icon',
    'gdpr_subs_btn',
    'gdpr_ver_option',
    'e_woo_waiting',
    'e_woo_abandoned',
    'e_woo_aband_last_rem',
    'android_msg_counter',
    'ios_msg_counter',
    'desktop_webpush',
    'desktop_webpush_old',
    'webpush_onesignal_payload',
    'no_disturb',
    'black_overlay',
    'desktop_welc_redir',
    'pwa_support',
    'amp_support',
    'amp_post_widget',
    'amp_page_widget',
    'amp_post_shortcode',
    'amp_page_shortcode',
    'pwa_kaludi_support',
    );

    foreach($checkbox AS $inptname){
      if(!isset($_POST[$inptname])){
        self::$apisetting[$inptname] = 0;
      }
    }
    $upload_dir = wp_upload_dir();
    $cert_upload_path = $upload_dir['basedir'].'/certifications';
    if(! file_exists($cert_upload_path)){
      if(! mkdir($cert_upload_path)){
        die(__('can not create a directory to save the certifications files under uploads directory .', 'smpush-plugin-lang'));
      }
    }
    if(!empty($_FILES['apple_cert_upload']['tmp_name'])){
      if(strtolower(substr($_FILES['apple_cert_upload']['name'], strrpos($_FILES['apple_cert_upload']['name'], '.') + 1)) == 'pem'){
        $target_path = $cert_upload_path.'/cert_connection_'.time().'_'.$newsetting['def_connection'].'.pem';
        if(move_uploaded_file($_FILES['apple_cert_upload']['tmp_name'], $target_path)){
          unset(self::$apisetting['apple_cert_path']);
          $newsetting['apple_cert_path'] = addslashes($target_path);
        }
      }
    }
    if(!empty($_FILES['apple_certp8_upload']['tmp_name'])){
      if(strtolower(substr($_FILES['apple_certp8_upload']['name'], strrpos($_FILES['apple_certp8_upload']['name'], '.') + 1)) == 'p8'){
        $target_path = $cert_upload_path.'/cert_connection_'.time().'_'.$newsetting['def_connection'].'.p8';
        if(move_uploaded_file($_FILES['apple_certp8_upload']['tmp_name'], $target_path)){
          unset(self::$apisetting['apple_certp8_path']);
          $newsetting['apple_certp8_path'] = addslashes($target_path);
        }
      }
    }
    if(!empty($_FILES['wp_cert']['tmp_name'])){
      $ext = strtolower(substr($_FILES['wp_cert']['name'], strrpos($_FILES['wp_cert']['name'], '.') + 1));
      $target_path = $cert_upload_path.'/wp_cert_connection_'.time().'_'.$newsetting['def_connection'].'.'.$ext;
      if(move_uploaded_file($_FILES['wp_cert']['tmp_name'], $target_path)){
        unset(self::$apisetting['wp_cert']);
        $newsetting['wp_cert'] = addslashes($target_path);
      }
    }
    if(!empty($_FILES['wp_pem']['tmp_name'])){
      $ext = strtolower(substr($_FILES['wp_pem']['name'], strrpos($_FILES['wp_pem']['name'], '.') + 1));
      $target_path = $cert_upload_path.'/wp_pem_connection_'.time().'_'.$newsetting['def_connection'].'.'.$ext;
      if(move_uploaded_file($_FILES['wp_pem']['tmp_name'], $target_path)){
        unset(self::$apisetting['wp_pem']);
        $newsetting['wp_pem'] = addslashes($target_path);
      }
    }
    if(!empty($_FILES['wp_cainfo']['tmp_name'])){
      $ext = strtolower(substr($_FILES['wp_cainfo']['name'], strrpos($_FILES['wp_cainfo']['name'], '.') + 1));
      $target_path = $cert_upload_path.'/wp_cainfo_connection_'.time().'_'.$newsetting['def_connection'].'.'.$ext;
      if(move_uploaded_file($_FILES['wp_cainfo']['tmp_name'], $target_path)){
        unset(self::$apisetting['wp_cainfo']);
        $newsetting['wp_cainfo'] = addslashes($target_path);
      }
    }
    if(!empty($_FILES['safari_cert_upload']['tmp_name'])){
      $ext = strtolower(substr($_FILES['safari_cert_upload']['name'], strrpos($_FILES['safari_cert_upload']['name'], '.') + 1));
      $target_path = $cert_upload_path.'/safari_cert_connection_'.time().'_'.$newsetting['def_connection'].'.'.$ext;
      if(move_uploaded_file($_FILES['safari_cert_upload']['tmp_name'], $target_path)){
        unset(self::$apisetting['safari_cert_path']);
        $newsetting['safari_cert_path'] = addslashes($target_path);
      }
    }
    if(!empty($_FILES['safari_certp12_upload']['tmp_name'])){
      $ext = strtolower(substr($_FILES['safari_certp12_upload']['name'], strrpos($_FILES['safari_certp12_upload']['name'], '.') + 1));
      $target_path = $cert_upload_path.'/safari_certp12_connection_'.time().'_'.$newsetting['def_connection'].'.'.$ext;
      if(move_uploaded_file($_FILES['safari_certp12_upload']['tmp_name'], $target_path)){
        unset(self::$apisetting['safari_certp12_path']);
        $newsetting['safari_certp12_path'] = addslashes($target_path);
      }
    }
    
    if(!empty(self::$apisetting['safari_pack_path'])){
      @unlink($cert_upload_path.'/'.self::$apisetting['safari_pack_path']);
      unset(self::$apisetting['safari_pack_path']);
    }
    
    self::$apisetting = array_map('wp_slash', self::$apisetting);
    self::$apisetting = array_merge($newsetting, self::$apisetting);

    if(!empty(self::$apisetting['desktop_webpush']) && empty(self::$apisetting['chrome_vapid_public'])){
      require(smpush_dir.'/lib/web-push-php/vendor/autoload.php');
      $vapkeys = VAPID::createVapidKeys();
      self::$apisetting['chrome_vapid_public'] = $vapkeys['publicKey'];
      self::$apisetting['chrome_vapid_private'] = $vapkeys['privateKey'];
    }
    
    /*if(self::$apisetting['msn_accesstoken'] != self::$apisetting['msn_oldaccesstoken']){
      $helper = new smpush_helper();
      $response = json_decode($helper->buildCurl('https://graph.facebook.com/v2.10/me/subscribed_apps?access_token='.self::$apisetting['msn_accesstoken'], false, true), true);
      if(isset($response['success']) && $response['success'] == 'true'){
        self::$apisetting['msn_subscribe_error'] = 0;
        self::$apisetting['msn_oldaccesstoken'] = self::$apisetting['msn_accesstoken'];
      }
      else{
        self::$apisetting['msn_subscribe_error'] = 1;
      }
    }*/
    self::$apisetting['msn_subscribe_error'] = 0;
    self::$apisetting['last_change_time'] = time();
    self::$apisetting['settings_version'] = self::$apisetting['settings_version']+0.01;

    if (self::$apisetting['desktop_webpush'] == 0){
      self::$apisetting['amp_support'] = 0;
    }

    self::setup_bridge();
    @unlink(smpush_cache_dir.'/jwt_header');
    
    update_option('smpush_options', self::$apisetting);
    echo 1;
    die();
  }

  public static function loadHistory($field, $index=false){
    if($index === false){
      if(isset(self::$history[$field])){
        if(is_array(self::$history[$field])){
          return array_map('stripslashes', self::$history[$field]);
        }
        else{
          return stripslashes(self::$history[$field]);
        }
      }
    }
    else{
      if(isset(self::$history[$field][$index])){
        return stripslashes(self::$history[$field][$index]);
      }
    }
    return '';
  }
  
  public static function loadData($field, $index=false, $defaultvalue=false){
    if($index === false){
      if(isset(self::$data[$field])){
        if(is_array(self::$data[$field])){
          return array_map('stripslashes_deep', self::$data[$field]);
        }
        else{
          return stripslashes(self::$data[$field]);
        }
      }
    }
    else{
      if(isset(self::$data[$field][$index])){
        if(is_array(self::$data[$field][$index])){
          return array_map('stripslashes_deep', self::$data[$field][$index]);
        }
        else{
          return stripslashes(self::$data[$field][$index]);
        }
      }
    }
    return ($defaultvalue !== false)? $defaultvalue : '';
  }

  public function build_menus(){
    add_menu_page('Settings', __('Smart Notification', 'smpush-plugin-lang'), 'delete_pages', 'smpush_setting', array('smpush_controller', 'setting'), 'div', 4);
    add_submenu_page('smpush_setting', __('New Campaign', 'smpush-plugin-lang'), __('New Campaign', 'smpush-plugin-lang'), 'delete_pages', 'smpush_send_notification', array('smpush_sendpush', 'send_notification'));
    add_submenu_page('smpush_setting', __('Campaigns Pending', 'smpush-plugin-lang'), __('Campaigns Pending', 'smpush-plugin-lang'), 'delete_pages', 'smpush_campending', array('smpush_modules', 'archivepending'));
    add_submenu_page('smpush_setting', __('Campaigns Archive', 'smpush-plugin-lang'), __('Campaigns Archive', 'smpush-plugin-lang'), 'delete_pages', 'smpush_archive', array('smpush_modules', 'archive'));
    add_submenu_page('smpush_setting', __('Auto Campaigns', 'smpush-plugin-lang'), __('Auto Campaigns', 'smpush-plugin-lang'), 'delete_pages', 'smpush_archiveauto', array('smpush_modules', 'archiveauto'));
    add_submenu_page('smpush_setting', __('RSS Auto Push', 'smpush-plugin-lang'), __('RSS Auto Push', 'smpush-plugin-lang'), 'delete_pages', 'smpush_autorss', array('smpush_autorss', 'page'));
    add_submenu_page('smpush_setting', __('Statistics', 'smpush-plugin-lang'), __('Statistics', 'smpush-plugin-lang'), 'delete_pages', 'smpush_statistics', array('smpush_modules', 'statistics'));
    add_submenu_page('smpush_setting', __('Event Manager', 'smpush-plugin-lang'), __('Event Manager', 'smpush-plugin-lang'), 'delete_pages', 'smpush_events', array('smpush_event_manager', 'page'));
    add_submenu_page('smpush_setting', __('Manage Connections', 'smpush-plugin-lang'), __('Manage Connections', 'smpush-plugin-lang'), 'delete_pages', 'smpush_connections', array('smpush_modules', 'connections'));
    add_submenu_page('smpush_setting', __('Manage Subscribers', 'smpush-plugin-lang'), __('Manage Subscribers', 'smpush-plugin-lang'), 'delete_pages', 'smpush_tokens', array('smpush_modules', 'tokens'));
    add_submenu_page('smpush_setting', __('Import Subscribers', 'smpush-plugin-lang'), __('Import Subscribers', 'smpush-plugin-lang'), 'delete_pages', 'smpush_import', array('smpush_modules', 'import'));
    add_submenu_page('smpush_setting', __('OneSignal Migration', 'smpush-plugin-lang'), __('OneSignal Migration', 'smpush-plugin-lang'), 'delete_pages', 'smpush_onesignal', array('smpush_modules', 'onesignal'));
    add_submenu_page('smpush_setting', __('Push Notification Channels', 'smpush-plugin-lang'), __('Manage Channels', 'smpush-plugin-lang'), 'delete_pages', 'smpush_channel', array('smpush_modules', 'push_channel'));
    add_submenu_page('smpush_setting', __('Test Dashboard', 'smpush-plugin-lang'), __('Test Dashboard', 'smpush-plugin-lang'), 'delete_pages', 'smpush_test_sending', array('smpush_modules', 'testing'));
    add_submenu_page('smpush_setting', __('Developer Documentation', 'smpush-plugin-lang'), __('Documentation', 'smpush-plugin-lang'), 'delete_pages', 'smpush_documentation', array('smpush_controller', 'documentation'));
    add_submenu_page(NULL, __('Sending Push Notification', 'smpush-plugin-lang'), __('Sending Push Notification', 'smpush-plugin-lang'), 'delete_pages', 'smpush_send_process', array('smpush_sendpush', 'send_process'));
    add_submenu_page(NULL, __('Queue Push', 'smpush-plugin-lang'), __('Queue Push', 'smpush-plugin-lang'), 'delete_pages', 'smpush_runqueue', array('smpush_sendpush', 'RunQueue'));
    add_submenu_page(NULL, __('Cancel Queue Push', 'smpush-plugin-lang'), __('Cancel Queue Push', 'smpush-plugin-lang'), 'delete_pages', 'smpush_cancelqueue', array('smpush_sendpush', 'smpush_cancelqueue'));
    add_submenu_page(NULL, __('Active invalid tokens', 'smpush-plugin-lang'), __('Active invalid tokens', 'smpush-plugin-lang'), 'delete_pages', 'smpush_active_tokens', array('smpush_sendpush', 'activateTokens'));
    add_submenu_page(NULL, __('Watch real-time GPS', 'smpush-plugin-lang'), __('Watch real-time GPS', 'smpush-plugin-lang'), 'delete_pages', 'smpush_realtime_gps', array('smpush_sendpush', 'gpsRealtime'));
    add_submenu_page(NULL, 'Campaign Ajax', 'Campaign Ajax', 'delete_pages', 'smpush_ajax_actions', array('smpush_modules', 'ajax_actions'));
    if(is_multisite() && ! is_super_admin()){
      return;
    }
    add_submenu_page('smpush_setting', __('System Error Log', 'smpush-plugin-lang'), __('System Error Log', 'smpush-plugin-lang'), 'delete_pages', 'smpush_error_log', array('smpush_modules', 'error_log'));
    add_submenu_page('smpush_setting', __('System Auto Update', 'smpush-plugin-lang'), __('Auto Update', 'smpush-plugin-lang'), 'delete_pages', 'smpush_autoupdate', array('smpush_autoupdate', 'auto_update'));
  }

  public static function register_cron($schedules){
    $schedules['smpush_few_days'] = array(
      'interval' => 259200,
      'display' => __('Once every 3 days')
    );
    $schedules['smpush_recurring_min'] = array(
      'interval' => 900,
      'display' => __('Every 15 minutes')
    );
    return $schedules;
  }

  public function cron_setup(){
    if(!wp_next_scheduled('smpush_recurring_cron')){
      wp_schedule_event(current_time('timestamp'), 'smpush_recurring_min', 'smpush_recurring_cron');
	  }
    if(!wp_next_scheduled('smpush_silent_cron')){
      wp_schedule_event(current_time('timestamp'), 'hourly', 'smpush_silent_cron');
	  }
    if(!wp_next_scheduled('smpush_update_counters')){
      wp_schedule_event(mktime(3,0,0,date('m'),date('d'),date('Y')), 'daily', 'smpush_update_counters');
	  }
    if(! wp_next_scheduled('smpush_cron_fewdays')){
      wp_schedule_event(mktime(15,0,0,date('m'),date('d'),date('Y')), 'smpush_few_days', 'smpush_cron_fewdays');
	  }
    if(get_transient('smpush_update_notice') !== false){
      add_action('admin_notices', array('smpush_controller', 'update_notice'));
    }
  }

  public function check_update_notify(){
    if(function_exists('curl_init')){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://smartiolabs.com/update/push_notification");
      curl_setopt($ch, CURLOPT_REFERER, 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
      if(defined('WP_PROXY_HOST')){
        curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
        curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        if(defined('WP_PROXY_USERNAME')){
          curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
          curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
        }
      }
      $data = json_decode(curl_exec($ch));
      curl_close($ch);
      if($data !== NULL){
        if($data->version > SMPUSHVERSION){
          set_transient('smpush_update_notice', $data, 86400);
        }
      }
    }
  }

  public static function update_notice(){
    $data = get_transient('smpush_update_notice');
    echo '<div class="notice notice-warning is-dismissible"><p><a href="'.$data->link.'" target="_blank">'.$data->plugin.' '.$data->version.'</a> '.__('is available! Please update your system using the', 'smpush-plugin-lang').' <a href="'.admin_url().'admin.php?page=smpush_autoupdate">'.__('auto update page', 'smpush-plugin-lang').'</a>.</p></div>';
  }
  
  public static function demonote(){
    echo '<div class="notice notice-error"><p><strong>It is a demo version and some features are disabled like save settings, delete records or send push messages.</strong></p></div>';
  }
  
  public static function bizfeature(){
    //echo '<div class="notice notice-error"><p><strong>This feature is available for Developer & Business plans only.</strong></p></div>';
  }
  
  public static function illgeal(){
    //echo '<div class="notice notice-error"><p>It is illegal use of `Smart Push Notification` plugin in multisite Wordpress network. Please upgrade your plan to Developer or Business for using this feature <a href="https://smartiolabs.com/product/push-notification-system#plans" target="_blank">click here</a></p></div>';
  }
  
  public static function license(){
    return;
    echo '<div class="notice notice-error"><p>Some of `Push Notification System` plugin functions are disabled. Please enter your purchase code in the `Auto Update` page.</p></div>';
  }

  public static function update_counters(){
    global $wpdb;
    $defconid = self::$apisetting['def_connection'];
    $counter = self::$pushdb->get_var(self::parse_query("SELECT COUNT({id_name}) FROM {tbname}"));
    $wpdb->query("UPDATE ".$wpdb->prefix."push_connection SET `counter`='$counter' WHERE id='$defconid'");
    
    $wpdb->query("DELETE FROM ".$wpdb->prefix."push_history WHERE timepost<NOW()-INTERVAL 60 DAY");
    $wpdb->query("DELETE FROM ".$wpdb->prefix."push_desktop_messages WHERE timepost<NOW()-INTERVAL 3 DAY");
    $wpdb->query("DELETE FROM ".$wpdb->prefix."push_feedback WHERE timepost<NOW()-INTERVAL 10 DAY");
    $wpdb->query("DELETE FROM ".$wpdb->prefix."push_cron_queue WHERE timepost<NOW()-INTERVAL 10 DAY");
  }
  
  public static function update_all_counters(){
    global $wpdb;
    self::update_counters();
    $channels = $wpdb->get_results("SELECT id FROM ".$wpdb->prefix."push_channels");
    if($channels){
      foreach($channels as $channel){
        $count = $wpdb->get_var("SELECT COUNT(token_id) FROM ".$wpdb->prefix."push_relation WHERE channel_id='$channel->id'");
        $wpdb->query("UPDATE ".$wpdb->prefix."push_channels SET `count`='$count' WHERE id='$channel->id'");
      }
    }
  }
  
  public static function run_silent_cron(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, get_bloginfo('wpurl').'/?smpushcontrol=cron_job&time='.time());
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if(defined('WP_PROXY_HOST')){
      curl_setopt($ch, CURLOPT_PROXY, WP_PROXY_HOST);
      curl_setopt($ch, CURLOPT_PROXYPORT, WP_PROXY_PORT);
      curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      if(defined('WP_PROXY_USERNAME')){
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, WP_PROXY_USERNAME.':'.WP_PROXY_PASSWORD);
        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);
      }
    }
    curl_exec($ch);
    curl_close($ch);
  }

  public function get_option($index){
    return self::$apisetting[$index];
  }

  public function plugin_bootstrap(){
    self::$apisetting = get_option('smpush_options');
    self::$apisetting = array_map('stripslashes_deep', self::$apisetting);
    self::$apisetting['gdpr_ver_text_processed'] = self::processGDPRText(self::$apisetting['gdpr_privacylink'], self::$apisetting['gdpr_termslink'], self::$apisetting['gdpr_ver_text']);
    if(is_multisite() && !file_exists(smpush_upload_dir.'/smpush_premium.info')){
      add_action('admin_notices', array('smpush_controller', 'illgeal'));
    }
    if(empty(self::$apisetting['purchase_code'])){
      add_action('admin_notices', array('smpush_controller', 'license'));
    }
    if(smpush_env_demo){
      add_action('admin_notices', array('smpush_controller', 'demonote'));
      if(!empty($_GET['page']) && $_GET['page'] == 'smpush_autorss'){
        add_action('admin_notices', array('smpush_controller', 'bizfeature'));
      }
    }
    wp_register_script('smpush-fb-chat-sdk', 'https://connect.facebook.net/'.self::$apisetting['msn_lang'].'/sdk/xfbml.customerchat.js#xfbml=1&version=v2.11');
    wp_register_script('smpush-fb-sdk', 'https://connect.facebook.net/'.self::$apisetting['msn_lang'].'/sdk.js#xfbml=1&version=v2.11');
    wp_register_script('smpush-gmap-source', 'https://maps.googleapis.com/maps/api/js?v=3.exp&key='.self::$apisetting['gmaps_apikey'], array('jquery'), SMPUSHVERSION);
  }

  public function add_mime_types($mime_types){
    $mime_types['crt'] = 'application/x-x509-user-cert';
    $mime_types['cer'] = 'application/pkix-cert';
    $mime_types['pem'] = 'application/x-pem-file';
    $mime_types['pfx'] = 'application/x-pkcs12';
    $mime_types['p12'] = 'application/x-pkcs12';
    $mime_types['csr'] = 'application/pkcs10';
    return $mime_types;
  }
  
  //for future use
  public function add_rewrite_rules(){
    $apiname = self::$apisetting['push_basename'];
    add_rewrite_rule($apiname.'/?$', 'index.php?smpushcontrol=debug', 'top');
    add_rewrite_rule($apiname.'/(.+)$', 'index.php?smpushcontrol=$matches[1]', 'top');
  }

  public static function setup_bridge(){
    @unlink(ABSPATH.'/smart_manifest.js');
    @unlink(ABSPATH.'/smart_service_worker.js');
    @unlink(ABSPATH.'/smart_push_sw.js');
    @unlink(ABSPATH.'/smwp_amp_sw.js');
    @unlink(ABSPATH.'/smart_bridge.php');
    @unlink(smpush_dir.'/js/frontend_webpush.js');
    if(is_multisite()){
      @unlink(smpush_cache_dir.'/settings'.get_current_blog_id());
    }
    else{
      @unlink(smpush_cache_dir.'/settings');
    }

    $helper = new smpush_helper();
    $bridgeContents = $helper->readlocalfile(smpush_dir.'/bridge.php');
    $bridgeContents = str_replace('$include_path = \'.\';', '$include_path = \''.smpush_dir.'\';', $bridgeContents);
    $helper->storelocalfile(ABSPATH.'/smart_bridge.php', $bridgeContents);

    self::setup_htaccess();
    self::generateCaches();
  }
  
  public static function generateCaches(){
    if(self::$apisetting['desktop_status'] == 1){
      if(self::$apisetting['desktop_webpush'] == 0 || self::$apisetting['desktop_webpush_old'] == 1){
        smpush_build_profile::manifest(true);
        smpush_build_profile::service_worker(true);
      }
    }
  }

  public static function setup_htaccess(){
    if(!file_exists(ABSPATH.'/.htaccess'))return;
    $helper = new smpush_helper();
    $htaccess = $helper->readlocalfile(ABSPATH.'/.htaccess');
    if(empty($htaccess)){
      return;
    }
    $htaccess = preg_replace('/# BEGIN SMART PUSH FAST BRIDGE([\s\S]*?)# END SMART PUSH FAST BRIDGE\s?\n?/i', '', $htaccess);
    if(empty($htaccess)){
      return;
    }
    $smartbridge = "# BEGIN SMART PUSH FAST BRIDGE\n";
    $smartbridge .= "<IfModule mod_rewrite.c>\n";
    $smartbridge .= "RewriteEngine On\n";
    $smartbridge .= "RewriteRule ^".((!empty(self::$apisetting['push_basename']))? self::$apisetting['push_basename'] : 'push')."/([_0-9a-zA-Z-]+)/?(.*)?$ index.php?smpushcontrol=$1/$2&%{QUERY_STRING}\n";
    if(!isset(self::$apisetting['fast_bridge']) || self::$apisetting['fast_bridge'] == 1){
      $smartbridge .= "RewriteCond %{QUERY_STRING} ^smpushcontrol=(get_archive|get_link|go)(.*)$\n";
      $smartbridge .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
      if(file_exists(ABSPATH.'/smart_bridge.php')){
        $smartbridge .= "RewriteRule ^(.*)$ smart_bridge.php?%{QUERY_STRING} [L]\n";
      }
      else{
        $smartbridge .= "RewriteRule ^(.*)$ ".(trim(str_replace(array(realpath(ABSPATH), '\\'), array('','/'), realpath(smpush_dir)), '/'))."/bridge.php?%{QUERY_STRING} [L]\n";
      }
    }
    $smartbridge .= "</IfModule>\n";
    $smartbridge .= "# END SMART PUSH FAST BRIDGE\n";
    if(strpos($htaccess, '# BEGIN SMART PUSH FAST BRIDGE') === false){
      $helper->storelocalfile(ABSPATH.'/.htaccess', $smartbridge.$htaccess);
    }
  }
  
  public function moveServiceWokrer(){
    if(self::$apisetting['desktop_webpush'] == 1){
      if(!file_exists(ABSPATH . '/smart_push_sw.js') || filesize(ABSPATH . '/smart_push_sw.js') == 0){
        if(file_exists(smpush_dir . '/js/sw.js')){
          $swcontents = $this->readlocalfile(smpush_dir . '/js/sw.js');
          $this->storelocalfile(smpush_dir . '/smart_push_sw.js', $swcontents);
          @rename(smpush_dir . '/smart_push_sw.js', ABSPATH . '/smart_push_sw.js');
        }
      }
    }
  }

  public function initAction(){
    if(self::$apisetting['desktop_paytoread'] == 2 && empty($_COOKIE['smpush_device_token'])){
      add_filter('the_content', array($this, 'filterProtectContent'), 99, 1);
    }
  }

  public function filterProtectContent($content){
    if(is_single() === false){
      return $content;
    }
    if (preg_match('/bot|crawl|curl|mediapartners|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|teoma|contaxe|yandex|libwww-perl|facebook|facebookexternalhit/i', $_SERVER['HTTP_USER_AGENT'])){
      return $content;
    }
    $content = self::ShortHTMLString($content, self::$apisetting['desktop_paytoread_textsize']);
    $content .= '<p style="text-align:center"><button type="button" id="SMIOPayToReadButton" onclick="smpushDrawReqWindow()">'.self::$apisetting['desktop_paytoread_substext'].'</button></p>';
    return $content;
  }
    
  public static function refresh_linked_user(){
    setcookie('smpush_fresh_linked_user', 'true', (time()+2592000), COOKIEPATH);
  }
  
  public static function smtp_config(PHPMailer $mailer){
    if(self::$apisetting['smtp_status'] == 0){
      return;
    }
    if ( !is_object( $mailer ) ) $mailer = (object) $mailer;
    $mailer->IsSMTP();
    $mailer->Port = self::$apisetting['smtp_port'];
    $mailer->Host = self::$apisetting['smtp_host'];
    $mailer->CharSet = 'utf-8';
    if(!empty(self::$apisetting['smtp_secure'])){
      $mailer->SMTPSecure = self::$apisetting['smtp_secure'];
    }
    if(empty(self::$apisetting['smtp_username'])){
      $mailer->SMTPAuth = false;
    }
    else{
      $mailer->SMTPAuth = true;
      $mailer->Username = self::$apisetting['smtp_username'];
      $mailer->Password = self::$apisetting['smtp_password'];
    }
    $mailer->SMTPDebug = (smpush_env == 'test')? 1 : 0; // enables SMTP debug information (for testing), 1 = errors and messages, 2 = messages only
  }
  
  public function start_fetch_method(){
    $profile = get_query_var('smpushprofile');
    $method = get_query_var('smpushcontrol');
    if(!empty($method)){
      if(strpos($method, 'safari/v') !== false){
        new smpush_api('safari', true, $method);
      }
      else{
        new smpush_api($method);
      }
    }
    if(!empty($_GET['smpushprofile'])){
      new smpush_build_profile($_GET['smpushprofile']);
    }
  }

  public function register_vars($vars){
    $vars[] = 'smpushcontrol';
      return $vars;
  }

}