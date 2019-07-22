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

$include_path = '.';

require($include_path.'/class.helper.php');

class smpush_bridge extends smpush_helper {
  private $apisetting;
  private $wpdb;
  private $table_prefix;
  private $include_path;
  private $platforms = array('ios','iosfcm','android','wp','wp10','bb','chrome','safari','firefox','opera','edge','samsung','fbmsn','fbnotify','email');
  
  public function __construct($include_path){
    parent::__construct();
    if($include_path == '.'){
      $wp_config = $this->readlocalfile('../../../wp-config.php');
    }
    else{
      $wp_config = $this->readlocalfile('./wp-config.php');
    }
    $this->include_path = $include_path;
    $wp_config = str_replace('*/', "*/\n", $wp_config);
    $wp_config = $this->strip_comments($wp_config);
    $wp_config = preg_replace('/(require|include_once)([^;]*);/i', '', $wp_config);
    $wp_config = str_replace(array('<?php','?>'), '', $wp_config);
    eval($wp_config);

    require($include_path.'/lib/db/ez_sql_core.php');
    define('CACHE_DIR', $include_path.'/lib/cache');

    if(function_exists ('mysqli_connect')){
      require($include_path.'/lib/db/ez_sql_mysqli.php');
      $this->wpdb = new ezSQL_mysqli();
      $this->wpdb->quick_connect(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST, '', DB_CHARSET);
    }
    else{
      require($include_path.'/lib/db/ez_sql_mysql.php');
      $this->wpdb = new ezSQL_mysql();
      $this->wpdb->quick_connect(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST, DB_CHARSET);
    }
    $this->wpdb->query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION';");
    $this->table_prefix = $table_prefix;

    $this->loadSettings();

    if(defined('MULTISITE') && MULTISITE){
      $URI = explode('?', $_SERVER['REQUEST_URI']);
      $cuBlog = basename(addslashes($URI[0]));
      if(defined('PATH_CURRENT_SITE') && !empty(PATH_CURRENT_SITE)){
        $orgBlog = str_replace('/', '', PATH_CURRENT_SITE);
      }
      else{
        $orgBlog = basename($this->apisetting['home_url']);
      }
      if(!empty($cuBlog) && $cuBlog != $orgBlog){
        if(!empty($orgBlog)){
          $netBlogID = $this->wpdb->get_var("SELECT blog_id FROM ".$this->table_prefix."blogs WHERE `path`='/$orgBlog/$cuBlog/'");
        }
        else{
          $netBlogID = $this->wpdb->get_var("SELECT blog_id FROM ".$this->table_prefix."blogs WHERE `path`='/$cuBlog/'");
        }
        $this->table_prefix = $table_prefix.$netBlogID.'_';
        $this->loadSettings($netBlogID);
      }
    }

    $this->ParseOutput = true;
  }

  private function strip_comments($source) {
    if (!defined('T_ML_COMMENT')) {
      define('T_ML_COMMENT', T_COMMENT);
    } else {
      define('T_DOC_COMMENT', T_ML_COMMENT);
    }
    $tokens = token_get_all($source);
    $ret = '';
    foreach ($tokens as $token) {
      if (is_string($token)) {
        $ret.= $token;
      } else {
        list($id, $text) = $token;
        switch ($id) {
          case T_COMMENT:
          case T_ML_COMMENT: // we've defined this
          case T_DOC_COMMENT: // and this
            break;
          default:
            $ret.= $text;
            break;
        }
      }
    }
    return trim($ret);
  }

  public function get_archive(){
    $order = 'DESC';
    $where = '';
    $push_archiveTB = $this->table_prefix.'push_archive';
    if(!empty($_REQUEST['order'])){
      if(strtolower($_REQUEST['order']) == 'asc') $order = 'ASC';
      else $order = 'DESC';
    }
    if(!empty($_REQUEST['platform'])){
      if($_REQUEST['platform'] == 'chrome'){
        $where = "AND $push_archiveTB.desktop LIKE '%chrome%'";
      }
      elseif($_REQUEST['platform'] == 'firefox'){
        $where = "AND $push_archiveTB.desktop LIKE '%firefox%'";
      }
      elseif($_REQUEST['platform'] == 'safari'){
        $where = "AND $push_archiveTB.desktop LIKE '%safari%'";
      }
      elseif($_REQUEST['platform'] == 'opera'){
        $where = "AND $push_archiveTB.desktop LIKE '%opera%'";
      }
      elseif($_REQUEST['platform'] == 'samsung'){
        $where = "AND $push_archiveTB.desktop LIKE '%samsung%'";
      }
      elseif($_REQUEST['platform'] == 'edge'){
        $where = "AND $push_archiveTB.desktop LIKE '%edge%'";
      }
      else{
        die();
      }
    }
    if(!empty($_REQUEST['deviceID'])){
      if($this->apisetting['desktop_offline'] == 1){
        $historyLimit = 4;
      }
      else{
        $historyLimit = 1;
      }
      $sql = "SELECT $push_archiveTB.id,$push_archiveTB.message,$push_archiveTB.starttime,$push_archiveTB.options FROM ".$this->table_prefix."push_desktop_messages
      INNER JOIN $push_archiveTB ON($push_archiveTB.id=".$this->table_prefix."push_desktop_messages.msgid AND $push_archiveTB.status='1')
      WHERE ".$this->table_prefix."push_desktop_messages.token='".md5($_REQUEST['deviceID'])."' AND ".$this->table_prefix."push_desktop_messages.type='$_REQUEST[platform]' ORDER BY ".$this->table_prefix."push_desktop_messages.timepost DESC LIMIT 0,$historyLimit";
      $gets = $this->wpdb->get_results($sql, 'ARRAY_A');
      if(!$gets) return $this->output(1, array());
      if($gets){
        foreach($gets as $get){
          $this->saveStats($_REQUEST['platform'], 'views', $get['id']);
        }
        $this->wpdb->query("DELETE FROM ".$this->table_prefix."push_desktop_messages WHERE token='".md5($_REQUEST['deviceID'])."' AND type='$_REQUEST[platform]'");
      }
    }
    elseif($_REQUEST['userid']){
      if(!empty($_REQUEST['mainPlatforms'])){
        if($_REQUEST['platform'] == 'mobile'){
          $where = "AND ".$this->table_prefix."push_history.platform='mobile'";
        }
        elseif($_REQUEST['mainPlatforms'] == 'fbmsn'){
          $where = "AND ".$this->table_prefix."push_history.platform='fbmsn'";
        }
        elseif($_REQUEST['mainPlatforms'] == 'fbnotify'){
          $where = "AND ".$this->table_prefix."push_history.platform='fbnotify'";
        }
        elseif($_REQUEST['mainPlatforms'] == 'email'){
          $where = "AND ".$this->table_prefix."push_history.platform='email'";
        }
        else{
          $where = "AND ".$this->table_prefix."push_history.platform='web'";
        }
      }
      else{
        $where = "AND ".$this->table_prefix."push_history.platform='web'";
      }
      $sql = "SELECT $push_archiveTB.id,$push_archiveTB.message,".$this->table_prefix."push_history.timepost AS starttime,".$this->table_prefix."push_history.platform AS mainPlatform,$push_archiveTB.options FROM ".$this->table_prefix."push_history
      INNER JOIN $push_archiveTB ON($push_archiveTB.id=".$this->table_prefix."push_history.msgid AND $push_archiveTB.status='1')
      WHERE ".$this->table_prefix."push_history.userid='$_REQUEST[userid]' $where GROUP BY ".$this->table_prefix."push_history.msgid ORDER BY ".$this->table_prefix."push_history.timepost $order";
      $sql = $this->Paging($sql, $this->wpdb);
      $gets = $this->wpdb->get_results($sql, 'ARRAY_A');
      if(!$gets) return $this->output(0, gettext('No result found', 'smpush-plugin-lang'));
    }
    else{
      $sql = "SELECT id,message,starttime,options FROM ".$this->table_prefix."push_archive WHERE send_type IN('now','time','geofence','custom') $where ORDER BY id ".$order;
      $sql = $this->Paging($sql, $this->wpdb);
      $gets = $this->wpdb->get_results($sql, 'ARRAY_A');
      if(!$gets) return $this->output(0, gettext('No result found', 'smpush-plugin-lang'));
    }
    if(file_exists(ABSPATH.'/smart_bridge.php')){
      $siteurl = $this->apisetting['home_url'].'/smart_bridge.php';
    }
    else{
      $siteurl = $this->apisetting['home_url'].'/';
    }
    $messages = array();
    foreach ($gets as $get){
      $options = unserialize($get['options']);
      $message = array();
      $message['id'] = $get['id'];
      if(!empty($get['mainPlatform'])){
        if($get['mainPlatform'] == 'web'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($get['message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'mobile'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($get['message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'fbmsn'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($options['fbmsn_message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'fbnotify'){
          $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($options['fbnotify_message'])), ENT_NOQUOTES, 'UTF-8');
        }
        elseif($get['mainPlatform'] == 'email'){
          $message['message'] = htmlspecialchars_decode(self::cleanString($options['email']));
        }
      }
      else{
        $message['message'] = html_entity_decode(preg_replace("/U\+([0-9A-F]{4,5})/i", "&#x\\1;", self::cleanString($get['message'])), ENT_NOQUOTES, 'UTF-8');
      }
      $message['starttime'] = $get['starttime'];
      $message['mobtitle'] = self::cleanString($options['android_title'], true);
      $message['title'] = self::cleanString($options['desktop_title'], true);
      if(empty($_REQUEST['platform']) && (empty($_REQUEST['mainPlatforms']) || $_REQUEST['mainPlatforms'] != 'web')){
        $message['link'] = '';
        $payload = json_decode($options['extravalue'], true);
        if(!empty($payload) && !empty($payload['link'])){
          $message['link'] = $payload['link'];
        }
      }
      elseif($this->apisetting['no_disturb'] == 1){
        $message['link'] = $this->apisetting['home_url'];
        $message['target'] = $siteurl.'?smpushcontrol=get_link&id='.$get['id'].'&platform='.$_REQUEST['platform'];
      }
      else{
        $message['link'] = $siteurl.'?smpushcontrol=get_link&id='.$get['id'].'&platform='.$_REQUEST['platform'];
        $message['target'] = '';
      }
      $message['icon'] = (!empty($options['desktop_icon']))? self::cleanString($options['desktop_icon']) : '';

      $message['renotify'] = ($this->apisetting['no_disturb'] == 1)? true : false;
      
      $message['actions'] = array();
      if(!empty($options['desktop_actions'])){
        foreach($options['desktop_actions']['id'] as $ackey => $action){
          //$message['actions'][$ackey]['keyid'] = self::cleanString($options['desktop_actions']['id'][$ackey]);
          $message['actions'][$ackey]['keyid'] = 'button_id_'.$ackey;
          $message['actions'][$ackey]['id'] = 'button_'.$ackey;
          $message['actions'][$ackey]['text'] = self::cleanString($options['desktop_actions']['text'][$ackey]);
          $message['actions'][$ackey]['icon'] = self::cleanString(urldecode($options['desktop_actions']['icon'][$ackey]));
          $desktop_link = self::cleanString(urldecode($options['desktop_actions']['link'][$ackey]));
          $message['actions'][$ackey]['link'] = $siteurl.'?smpushcontrol=go&id='.$get['id'].'&platform='.$_REQUEST['platform'].'&target='.urlencode($desktop_link);
        }
      }
      $message['direction'] = (empty($options['desktop_dir']))? 'auto' : $options['desktop_dir'];
      $message['vibrate'] = (empty($options['desktop_vibrate']))? array() : explode('.', $options['desktop_vibrate']);
      $message['silent'] = (!isset($options['desktop_silent']))? '' : $options['desktop_silent'];
      if(empty($options['desktop_icon'])){
        $message['icon'] = '';
      }
      else{
        $message['icon'] = self::cleanString(urldecode($options['desktop_icon']));
      }
      if(empty($options['desktop_bigimage'])){
        $message['bigimage'] = '';
      }
      else{
        $message['bigimage'] = self::cleanString(urldecode($options['desktop_bigimage']));
      }
      if(empty($options['desktop_badge'])){
        $message['badge'] = '';
      }
      else{
        $message['badge'] = self::cleanString(urldecode($options['desktop_badge']));
      }
      if(empty($options['desktop_sound'])){
        $message['sound'] = '';
      }
      else{
        $message['sound'] = self::cleanString(urldecode($options['desktop_sound']));
      }
      $message['requireInteraction'] = (empty($options['desktop_interaction']))? 'false' : 'true';
      
      $messages[] = $message;
    }
    return $this->output(1, $messages);
  }

  public function views_tracker(){
    $this->saveStats($_REQUEST['platform'], 'views', $_REQUEST['id']);
  }
  
  public function get_link(){
    $this->CheckParams(array('id'));
    $message = $this->wpdb->get_row("SELECT id,options FROM ".$this->table_prefix."push_archive WHERE id='$_REQUEST[id]'", ARRAY_A);
    $message['options'] = unserialize($message['options']);
    if(!empty($_REQUEST['platform'])){
      $this->saveStats($_REQUEST['platform'], 'clicks', $message['id']);
    }
    if(!empty($_REQUEST['platform']) && $_REQUEST['platform'] == 'fbmsn' && !empty($message['options']['fbmsn_link'])){
      $link = urldecode(self::cleanString($message['options']['fbmsn_link']));
    }
    else{
      $link = urldecode(self::cleanString($message['options']['desktop_link']));
    }
    if(empty($link)){
      $link = $this->apisetting['home_url'];
    }
    echo '<script data-cfasync="false" type="text/javascript">window.location="'.$link.'"</script>';
    exit;
  }
  
  public function go(){
    $this->CheckParams(array('id','platform','target'));
    if(!in_array($_REQUEST['platform'], $this->platforms)){
      exit;
    }
    if(!empty($_GET['deviceid'])){
      $viewid = $this->wpdb->get_var("SELECT id FROM ".$this->table_prefix."push_newsletter_views WHERE msgid='$_GET[id]' AND deviceid='$_GET[deviceid]' AND action='click'");
      if(!$viewid){
        $data = array();
        $data['msgid'] = $_GET['id'];
        $data['deviceid'] = $_GET['deviceid'];
        $data['platid'] = $_GET['platform'];
        $data['action'] = 'click';
        $this->wpdb->insert($this->table_prefix.'push_newsletter_views', $data);
        
        $this->saveStats($_REQUEST['platform'], 'clicks', $_REQUEST['id']);
      }
    }
    else{
      $this->saveStats($_REQUEST['platform'], 'clicks', $_REQUEST['id']);
    }
    $link = urldecode(self::cleanString($_REQUEST['target']));
    if(empty($link)){
      $link = $this->apisetting['home_url'];
    }
    echo '<script data-cfasync="false" type="text/javascript">window.location="'.$link.'"</script>';
    exit;
  }

  public function tracking() {
    $this->CheckParams(array('id','platform','deviceid'));

    if(is_numeric($_GET['deviceid'])){
      $where = "deviceid='$_GET[deviceid]'";
    }
    else{
      $where = "device_hash='".md5(base64_decode($_GET['deviceid']))."'";
    }

    $viewid = $this->wpdb->get_var("SELECT id FROM ".$this->table_prefix."push_newsletter_views WHERE msgid='$_GET[id]' AND $where AND action='view'");
    if(!$viewid){
      $data = array();
      $data['msgid'] = $_GET['id'];
      if(is_numeric($_GET['deviceid'])){
        $data['deviceid'] = $_GET['deviceid'];
      }
      else{
        $data['device_hash'] = md5(base64_decode($_GET['deviceid']));
      }
      $data['platid'] = $_GET['platform'];
      $data['action'] = 'view';
      $this->wpdb->insert($this->table_prefix.'push_newsletter_views', $data);

      $this->saveStats($_GET['platform'], 'views', $_GET['id']);
    }
    header('Content-Type: image/gif');
    echo $this->readlocalfile($this->include_path.'/images/unnamed.gif');
    exit;
  }
  
  private function loadSettings($network=''){
    if(file_exists(CACHE_DIR.'/settings'.$network)){
      $this->apisetting = unserialize($this->readlocalfile(CACHE_DIR.'/settings'.$network));
    }
    else{
      $this->apisetting = unserialize($this->wpdb->get_var('SELECT option_value FROM '.$this->table_prefix.'options WHERE option_name="smpush_options"'));
      $this->apisetting['home_url'] = $this->wpdb->get_var('SELECT option_value FROM '.$this->table_prefix.'options WHERE option_name="home"');
      $this->apisetting = $this->stripslashes_deep($this->apisetting);
      @chmod(CACHE_DIR, 0750);
      $domain = $this->getDomain($this->apisetting['home_url']);
      if($domain != 'localhost'){
        $this->storelocalfile(CACHE_DIR.'/settings'.$network, serialize($this->apisetting));
      }
    }
  }
  
  private function saveStats($platid, $action, $msgid=0, $rate=1){
    $current_date = date('Y-m-d');
    $where = (empty($msgid))? '' : 'AND msgid="'.$msgid.'"';
    $statid = $this->wpdb->get_var("SELECT id FROM ".$this->table_prefix."push_statistics WHERE platid='$platid' AND `date`='$current_date' AND action='$action' $where");
    if(empty($statid)){
      $stat = array();
      $stat['platid'] = $platid;
      $stat['date'] = $current_date;
      $stat['action'] = $action;
      $stat['msgid'] = (empty($msgid))? 0 : $msgid;
      $stat['stat'] = $rate;
      $this->wpdb->insert($this->table_prefix.'push_statistics', $stat);
    }
    else{
      $this->wpdb->query("UPDATE ".$this->table_prefix."push_statistics SET `stat`=`stat`+$rate WHERE id='$statid'");
    }
  }
  
  function SecureInputs($value){
    if(! is_numeric($value)){
      if(is_array($value)){
        foreach($value AS $key=>$v){
          if(is_array($v)) $value[$key] = $this->SecureInputs($v);
          else{
            $value[$key] = htmlspecialchars(trim($v), ENT_QUOTES);
          }
        }
      }
      else{
        $value = htmlspecialchars(trim($value), ENT_QUOTES);
      }
    }
    return $value;
  }
  
}

$bridge = new smpush_bridge($include_path);

$_REQUEST = array_map(array($bridge, 'SecureInputs'), $_REQUEST);
$_POST = array_map(array($bridge, 'SecureInputs'), $_POST);
$_GET = array_map(array($bridge, 'SecureInputs'), $_GET);

if(isset($_GET['smpushcontrol'])){
  switch ($_GET['smpushcontrol']){
    case 'get_archive':
      $bridge->get_archive();
      break;
    case 'get_link':
      $bridge->get_link();
      break;
    case 'go':
      $bridge->go();
      break;
    case 'tracking':
      $bridge->tracking();
      break;
    case 'views_tracker':
      $bridge->views_tracker();
      break;
  }
}