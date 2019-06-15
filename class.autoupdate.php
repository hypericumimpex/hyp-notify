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

class smpush_autoupdate extends smpush_controller {

  public static $wpdateformat;

  public function __construct() {
    parent::__construct();
  }
  
  public static function auto_update() {
    $helper = new smpush_helper();
    $content = '';
    if(!empty($_POST['startupdate'])){
      if(smpush_env_demo){
        $content .= 'update feature is not allowed in the demo version';
      }
      if(empty(self::$apisetting['purchase_code'])){
        $content .= '<p>failed : enter your private purchase code to proceed in the updating process</p>';
      }
      if(!function_exists('rmdir')){
        $content .= '<p>failed : function rmdir() is not supported in your server</p>';
      }
      if(!function_exists('unlink')){
        $content .= '<p>failed : function unlink() is not supported in your server</p>';
      }
      if(!function_exists('fopen')){
        $content .= '<p>failed : function fopen() is not supported in your server</p>';
      }
      if(chmod(smpush_dir, 0777) === false){
        $content .= '<p>failed : directory wp-content does not have permission 0777</p>';
      }
      if(!class_exists('ZipArchive')){
        $content .= '<p>failed : ZipArchive library is not supported in your server</p>';
      }
      if(empty($content)){
        $lastupdate = json_decode($helper->buildCurl('https://smartiolabs.com/update/push_notification'), true);
        $updateData = $helper->buildCurl('https://smartiolabs.com/download', false, array('purchase_code' => self::$apisetting['purchase_code']));
        if($helper->curl_status == 200){
          $localzipfile = plugin_dir_path( __DIR__ ).'/smiopush_update_package.zip';
          @unlink($localzipfile);
          $handle = fopen($localzipfile, 'w');
          fwrite($handle, $updateData);
          fclose($handle);
          if(md5_file($localzipfile) == $lastupdate['md5_hash']){
            $zip = new ZipArchive;
            $ziphandle = $zip->open($localzipfile);
            if ($ziphandle === TRUE) {
              self::delTree(smpush_dir);
              $zip->extractTo(plugin_dir_path( __DIR__ ));
              $zip->close();
              @unlink($localzipfile);
              $content = '<p>'.__('your system has been successfully upgraded to the latest version', 'smpush-plugin-lang').' '.$lastupdate['version'].'</p>';
              if(strpos(self::$apisetting['purchase_code'], '-') !== false){
                $content .= '<div class="notice notice-warning is-dismissible"><p>It\'s 5 years of free updates we deliver for you and we still continue. Giving us <a href="https://codecanyon.net/downloads" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> is a very big push for us &#9786; <a href="https://codecanyon.net/downloads" target="_blank">it\'s one click</a></p></div>';
              }
            }
            else {
              @unlink($localzipfile);
              $content = '<p>'.__('Something happens while downloading the update package...Please try again later', 'smpush-plugin-lang').'</p>';
            }
          }
          else{
            @unlink($localzipfile);
            $content = '<p>'.__('Something happens while downloading the update package...Please try again later', 'smpush-plugin-lang').'</p>';
          }
        }
        else{
          $content = $updateData;
        }
      }
      include(smpush_dir.'/pages/auto_update.php');
      exit();
    }
    if(!empty($_POST['save']) && smpush_env_demo === false){
      self::$apisetting['purchase_code'] = $_POST['purchase_code'];
    }
    $lastupdate = json_decode($helper->buildCurl('https://smartiolabs.com/update/push_notification', false, array('purchase_code' => self::$apisetting['purchase_code'])), true);
    if($helper->curl_status == 401){
      self::$apisetting['vip'] = 0;
      self::$apisetting['purchase_code'] = '';
      update_option('smpush_options', self::$apisetting);
      if(is_multisite()){
        self::updateNetworkPurchaseCode('');
      }
      @chmod(smpush_upload_dir.'/smpush_premium.info', 777);
      @unlink(smpush_upload_dir.'/smpush_premium.info');
    }
    elseif($lastupdate !== NULL){
      $content = '<p>'.__('System current version', 'smpush-plugin-lang').' : '.SMPUSHVERSION.'</p><p>'.__('System last version', 'smpush-plugin-lang').' : '.$lastupdate['version'].'</p>';
      if(isset($lastupdate['plan']['vip_features']) && $lastupdate['plan']['vip_features'] == 1){
        self::$apisetting['vip'] = 1;
        if(! file_exists(smpush_upload_dir.'/smpush_premium.info')){
          $helper->storelocalfile(smpush_upload_dir.'/smpush_premium.info', self::$apisetting['purchase_code']);
          @chmod(smpush_upload_dir.'/smpush_premium.info', 600);
        }
      }
      elseif(isset($lastupdate['plan']['vip_features']) && $lastupdate['plan']['vip_features'] == 0){
        self::$apisetting['vip'] = 0;
        @chmod(smpush_upload_dir.'/smpush_premium.info', 777);
        @unlink(smpush_upload_dir.'/smpush_premium.info');
      }
      update_option('smpush_options', self::$apisetting);
      if(is_multisite()){
        self::updateNetworkPurchaseCode(self::$apisetting['purchase_code']);
      }
    }
    else{
      $content = '<p>'.__('System current version', 'smpush-plugin-lang').' : '.SMPUSHVERSION.'</p><p>'.__('System last version : failed to connect</p>', 'smpush-plugin-lang');
      $content .= '<p>Curl status: '.$helper->curl_status.'</p>';
      $content .= '<p>Curl error: '.$helper->curl_error.'</p>';
    }
    $content .= '<form action="" method="post">
      <input name="purchase_code" type="text" size="50" value="'.((!empty(self::$apisetting['purchase_code']) && smpush_env_demo === false)? self::$apisetting['purchase_code'] : '').'" />
      <input type="submit" name="save" class="button button-primary" value="'.__('Save Changes', 'smpush-plugin-lang').'">';
    if(empty(self::$apisetting['purchase_code'])){
      $content .= '<p class="howto">'.__('For how to get your private purchase code', 'smpush-plugin-lang').' <a href="http://smartiolabs.com/blog/52/where-is-my-purchase-code/" target="_blank">'.__('click here', 'smpush-plugin-lang').'</a></p>';
      $content .= '<p><input type="submit" name="startupdate" class="button button-primary" value="'.__('Start System Update', 'smpush-plugin-lang').'" disabled="disabled"></p>';
    }
    elseif(!empty($lastupdate['version']) && $lastupdate['version'] > SMPUSHVERSION){
      $content .= '<p><input type="submit" name="startupdate" class="button button-primary" value="'.__('Start System Update', 'smpush-plugin-lang').'"></p>';
    }
    $content .= '</form>';
    include(smpush_dir.'/pages/auto_update.php');
  }

  private static function updateNetworkPurchaseCode($purchase_code) {
    global $wpdb;
    $blogs = $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs");
    if($blogs){
      foreach($blogs as $blog){
        switch_to_blog($blog->blog_id);
        $settings = get_option('smpush_options');
        $settings['purchase_code'] = $purchase_code;
        update_option('smpush_options', $settings);
      }
      restore_current_blog();
    }
  }

  private static function delTree($dirPath) {
    if (!is_dir($dirPath)) {
      return;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
      $dirPath .= '/';
    }
    $files = glob($dirPath.'*', GLOB_MARK);
    foreach ($files as $file) {
      if (is_dir($file)) {
        self::delTree($file);
      } else {
        unlink($file);
      }
    }
    @rmdir($dirPath);
    return true;
  }

}