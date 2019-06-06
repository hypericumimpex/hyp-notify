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

class smpush_amp extends smpush_helper{
  private $options;
  private $amp_web_push_loaded;

  public function __construct($options){
    $this->options = $options;
    $this->amp_web_push_loaded = false;

    add_action('amp_post_template_head', array($this, 'amp_javascript_library'));
    add_filter('superpwa_sw_filename', array($this, 'superpwa_compatible'));
    add_shortcode('smart_amp_optin', array($this, 'amp_shortcode_integration_code'));
    add_filter('the_content', array($this, 'amp_integration_code'));
    add_filter('superpwa_manifest', 'superpwa_add_gcm_sender_id');
    wp_enqueue_script('smpush-frontend');

    if($this->options['pwa_kaludi_support'] == 1){
      $this->pwa_kaludi_compatible();
    }

    //add_action('amp_before_footer', array($this, 'amp_integration_code'));
  }

  private function activated_sw_file(){
    if($this->options['desktop_webpush'] == 1){
      return ABSPATH.'/smart_push_sw.js';
    }
    else{
      return ABSPATH.'/smart_service_worker.js';
    }
  }

  private function amp_sw_file_contents(){
    $jsfile = $this->readlocalfile($this->activated_sw_file());
    $jsfile .= "\n\n\n";
    $jsfile .= 'const DEBUGE_MODE = '.((smpush_env == 'debug')? 1 : 0).";\n";
    $jsfile .= 'const VAPID_WEB_PUSH = '.(($this->options['desktop_webpush'] == 1)? 1 : 0).";\n";
    $jsfile .= 'const API_ENDPOINT = "'.rtrim(get_bloginfo('wpurl'), '/').'/?smpushcontrol=savetoken'."\";\n";
    $jsfile .= 'const SERVER_KEY = "'.$this->options['chrome_vapid_public']."\";\n\n";
    $jsfile .= $this->readlocalfile(__DIR__.'/js/amp_sw.js');

    return preg_replace('/\s+/', ' ', $jsfile);
  }

  function generate_AMP_SW(){
    if(! file_exists(ABSPATH.'/smwp_amp_sw.js')){
      $handler = fopen(plugin_dir_path(__FILE__).'/smwp_amp_sw.js', 'w+');
      fwrite($handler, $this->amp_sw_file_contents());
      fclose($handler);
      @rename(plugin_dir_path(__FILE__).'/smwp_amp_sw.js', ABSPATH.'/smwp_amp_sw.js');
    }
  }

  function pwa_kaludi_compatible(){
    if(file_exists(ABSPATH.'/pwa-register-sw.js') && file_exists(ABSPATH.'/pwa-sw.js')){
      $kaludi_init_sw = $this->readlocalfile(ABSPATH.'/pwa-register-sw.js');
      if(strpos($kaludi_init_sw, 'smart_sw.js') === false){
        $kaludi_init_sw = str_replace('/pwa-sw.js', '/smart_sw.js', $kaludi_init_sw);

        $kaludi_sw = $this->readlocalfile(ABSPATH.'/pwa-sw.js');

        $this->storelocalfile(ABSPATH.'/pwa-register-sw.js', $kaludi_init_sw);
        $this->storelocalfile(ABSPATH.'/smart_sw.js', $this->sw_file_contents()."\n\n".$kaludi_sw);
      }
    }
  }

  function superpwa_add_gcm_sender_id($manifest){
    $manifest['gcm_sender_id'] = $this->options['chrome_projectid'];
    return $manifest;
  }

  function superpwa_compatible($pwa_sw_name){
    if($this->options['pwa_support'] == 0){
      return $pwa_sw_name;
    }
    if(file_exists($this->activated_sw_file())){
      $smart_sw = $this->readlocalfile($this->activated_sw_file());
      if(strpos($smart_sw, 'superpwa') === false){
        $this->storelocalfile($this->activated_sw_file(), $smart_sw . "\n\n" . superpwa_sw_template());
      }
    }
    return basename($this->activated_sw_file());
  }

  function superpwa_sw_template($sw) {
    if(file_exists($this->activated_sw_file())){
      $smart_sw = $this->readlocalfile($this->activated_sw_file());
      if(strpos($smart_sw, 'superpwa') === false){
        $this->storelocalfile($this->activated_sw_file(), $smart_sw . "\n\n" . superpwa_sw_template());
      }
    }
    return $sw;
  }

  function amp_integration_code($content=''){
    if($this->options['amp_support'] == 0){
      return $content;
    }
    if (is_single() && $this->options['amp_post_widget'] == 1) {
      return $content.$this->amp_load_code();
    }
    elseif (is_page() && $this->options['amp_page_widget'] == 1) {
      return $content.$this->amp_load_code();
    }
    return $content;
  }

  function amp_shortcode_integration_code($args){
    if($this->options['amp_support'] == 0){
      return '';
    }
    if (is_single() && $this->options['amp_post_shortcode'] == 1) {
      return $this->amp_load_code($args);
    }
    elseif (is_page() && $this->options['amp_page_shortcode'] == 1) {
      return $this->amp_load_code($args);
    }
    return '';
  }

  function amp_javascript_library(){
    if($this->options['amp_support'] == 0){
      return '';
    }
    if (is_single() && ($this->options['amp_post_widget'] == 1 || $this->options['amp_post_shortcode'] == 1)) {
      $this->amp_web_push_loaded = true;
      echo '<script async custom-element="amp-web-push" src="https://cdn.ampproject.org/v0/amp-web-push-0.1.js"></script>';
    }
    elseif (is_page() && ($this->options['amp_page_widget'] == 1 || $this->options['amp_page_shortcode'] == 1)) {
      $this->amp_web_push_loaded = true;
      echo '<script async custom-element="amp-web-push" src="https://cdn.ampproject.org/v0/amp-web-push-0.1.js"></script>';
    }
    return '';
  }

  function amp_load_code($args=''){
    $wpurl = rtrim(get_bloginfo('wpurl'), '/');
    $subs_text = (empty($args['subscribe']))? __('Subscribe to Notifications', 'smpush-plugin-lang') : $args['subscribe'];
    $unsubs_text = (empty($args['unsubscribe']))? __('Opt-out from Notifications', 'smpush-plugin-lang') : $args['unsubscribe'];
    $this->generate_AMP_SW();
    $widget_css = 'style="display: block;margin: 20px auto;"';
    $btn_css = 'style="margin: auto;width: 280px;box-sizing: border-box;border:none;border-radius:3px;padding:0 16px;min-width:64px;height:36px;vertical-align:middle;text-align:center;text-overflow:ellipsis;text-transform:uppercase;color:#fff;background-color:#2c95f3;box-shadow:0 3px 1px -2px rgba(0,0,0,.2),0 2px 2px 0 rgba(0,0,0,.14),0 1px 5px 0 rgba(0,0,0,.12);font-size:14px;font-weight:500;line-height:36px;overflow:hidden;outline:none;cursor:pointer;transition:box-shadow 0.2s;"';
    $ampoutput = '';
    if($this->amp_web_push_loaded === false){
      $ampoutput .= '<script async custom-element="amp-web-push" src="https://cdn.ampproject.org/v0/amp-web-push-0.1.js"></script>';
    }
    $ampoutput .= '<amp-web-push'
      .' id="amp-web-push"'
      .' layout="nodisplay"'
      .' helper-iframe-url="'.smpush_jspath.'/amp-helper-iframe.html"'
      .' permission-dialog-url="'.smpush_jspath.'/amp-permission-dialog.html"'
      .' service-worker-url="'.$wpurl.'/smwp_amp_sw.js'.'"'
      .'></amp-web-push>';
    $ampoutput .= '<amp-web-push-widget visibility="unsubscribed" layout="fixed" width="285" height="40" '.$widget_css.'>'
      .'<button '.$btn_css.' on="tap:amp-web-push.subscribe">'.$subs_text.'</button>'
      .'</amp-web-push-widget>'
      .'<amp-web-push-widget visibility="subscribed" layout="fixed" width="285" height="40" '.$widget_css.'>'
      .'<button '.$btn_css.' class="amp-web-push-button" on="tap:amp-web-push.unsubscribe">'.$unsubs_text.'</button>'
      .'</amp-web-push-widget>'
      .'<amp-web-push-widget visibility="blocked" layout="fixed" width="285" height="40" '.$widget_css.'>'.__('Looks like you have blocked notifications!', 'smpush-plugin-lang')
      .'</amp-web-push-widget>';
    return $ampoutput;
  }
  
}