<?php
/*
Plugin Name: HYP Smart Notifications
Plugin URI: https://github.com/hypericumimpex/hyp-notify/
Description: Provides a complete solution to send web and mobile notification messages to platforms iOS, Android, Chrome, Safari, Firefox, Opera, Edge, Samsung Browser, Windows Phone 8, Windows 10, BlackBerry 10, FB Messenger and Newsletter.
Author: Hypericum
Version: 8.4.8
Author URI: https://github.com/hypericumimpex/
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('smpush_dir', plugin_dir_path(__FILE__));
define('smpush_imgpath', plugins_url('/images', __FILE__));
define('smpush_csspath', plugins_url('/css', __FILE__));
define('smpush_jspath', plugins_url('/js', __FILE__));
define('SMPUSHVERSION', 8.48);
define('smpush_env', 'production');//debug, production
define('smpush_env_demo', false);
define('smpush_mobapp_mode', false);

include(smpush_dir.'/class.helper.php');
include(smpush_dir.'/class.controller.php');
include(smpush_dir.'/class.sendpush.php');
include(smpush_dir.'/class.windowsphone.php');
include(smpush_dir.'/class.universal.windows.php');
include(smpush_dir.'/class.blackberry.php');
include(smpush_dir.'/class.sendcron.php');
include(smpush_dir.'/class.autorss.php');
include(smpush_dir.'/class.events.php');
include(smpush_dir.'/class.widget.php');
include(smpush_dir.'/class.modules.php');
include(smpush_dir.'/class.api.php');
include(smpush_dir.'/class.autoupdate.php');
require(smpush_dir.'/class.geolocation.php');
require(smpush_dir.'/class.browserpush.php');
require(smpush_dir.'/class.build.profile.php');
require(smpush_dir.'/class.localization.php');
require(smpush_dir.'/class.event.manager.php');
require(smpush_dir.'/class.shortcode.php');
require(smpush_dir.'/class.amp.php');
require(smpush_dir.'/class.peepso.php');

$upload_dir = wp_upload_dir();
define('smpush_upload_dir', $upload_dir['basedir']);
define('smpush_cache_dir', smpush_dir.'/lib/cache');

register_activation_hook(__FILE__, 'smpush_install');
register_uninstall_hook(__FILE__, 'smpush_uninstall');

add_action('init', 'smpush_start');
add_action('wpmu_new_blog', 'smpush_new_blog_installed', 99, 6);
add_filter('cron_schedules', array('smpush_controller', 'register_cron'));
add_filter('auth_cookie_expiration', 'smiopush_expiration_filter', 99, 3);
add_action('login_footer', 'smiopush_expiration_rememberme', 99);
add_action('bp_login_widget_form', 'smiopush_bb_expiration_rememberme', 99);

//Push notification for custom events
add_action('transition_post_status', array('smpush_events', 'queue_event'), 99, 3);
add_action('woocommerce_new_order', array('smpush_events', 'woocommerce_event'), 99, 1);
add_action('woocommerce_before_order_object_save', array('smpush_events', 'woocommerce_event'), 99, 1);
add_action('job-manager-alert', array('smpush_events', 'job_manager_alert' ), 10, 2);
add_action('wp_insert_comment', array('smpush_events', 'new_comment'), 99, 2);
add_action('comment_unapproved_to_approved', array('smpush_events', 'comment_approved'));
add_action('add_meta_boxes', array('smpush_events', 'build_meta_box'));
add_action('widgets_init', array('smpush_modules', 'widget'));
add_action('plugins_loaded', array('smpush_localization', 'load_textdomain'));
add_action('bp_notification_after_save', array('smpush_events', 'buddy_notifications'), 99, 1);
add_action('bp_activity_after_save', array('smpush_events', 'buddy_activity'), 99, 1);
add_filter('peepso_notifications_data_before_add', array('smpush_peepso_events', 'peepso_notification'), 99);
add_action('woocommerce_before_checkout_form', array('smpush_shortcode', 'woo_messenger_checkout'));
add_action('woocommerce_after_add_to_cart_button', array('smpush_shortcode', 'woo_messenger_cartbtn'));
add_filter('woocommerce_get_availability_text', array('smpush_shortcode', 'woo_waiting_notifier'), 99, 1);
add_shortcode('smart_push_widget', array('smpush_widget', 'shortcode'));
add_shortcode('smart_push_messenger', array('smpush_shortcode', 'messenger'));
add_shortcode('smart_push_fbloign', array('smpush_shortcode', 'fbloign'));
add_shortcode('smart_push_history', array('smpush_shortcode', 'push_history'));
add_shortcode('smart_subscription_page', array('smpush_shortcode', 'subscription'));

function smpush_start(){
  global $wpdb;
  define('SMPUSHTBPRE', $wpdb->prefix);
  $smpush_controller = new smpush_controller();
  
  $smpush_version = get_option('smpush_version');
  if($smpush_version != SMPUSHVERSION){
    smpush_upgrade($smpush_version);
  }
  
  add_action('template_redirect', array($smpush_controller, 'start_fetch_method'));
  add_action('deleted_user', array('smpush_api', 'delete_relw_app'));
  add_action('admin_menu', array($smpush_controller, 'build_menus'), 99);
  add_action('wp_enqueue_scripts', 'smpush_frontend_scripts');
  add_action('admin_enqueue_scripts', 'smpush_scripts');
  add_action('admin_enqueue_scripts', array('smpush_localization', 'javascript'));
  add_action('smpush_update_counters', array('smpush_controller', 'setup_bridge'));
  add_action('smpush_update_counters', array($smpush_controller, 'check_update_notify'));
  add_action('smpush_update_counters', array('smpush_controller', 'update_all_counters'));
  add_action('smpush_recurring_cron', array('smpush_controller', 'run_silent_cron'));
  add_action('wp_login', array('smpush_controller', 'refresh_linked_user'));
  add_action('smpush_silent_cron', array('smpush_autorss', 'run_rss_reader'));
  add_action('wp_footer', array('smpush_build_profile', 'messengerWidget'), 0);
  add_action('wp_footer', array('smpush_build_profile', 'load_frontend_push'), 0);

  add_filter('query_vars', array($smpush_controller, 'register_vars'));
}

function smpush_frontend_scripts(){
  wp_register_script('smpush-tooltipster', smpush_jspath.'/tooltipster.bundle.min.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-selectize', smpush_jspath.'/selectize.min.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-gmap-js', smpush_jspath.'/gmap.js', array('jquery', 'smpush-gmap-source'), SMPUSHVERSION);
  wp_register_script('smpush-frontend', smpush_jspath.'/frontend.js', array('jquery'), SMPUSHVERSION);
  wp_register_style('smpush-frontend', smpush_csspath.'/frontend.css', array(), SMPUSHVERSION, true);
  wp_register_style('smpush-selectize', smpush_csspath.'/selectize.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-tooltipster', smpush_csspath.'/tooltipster.bundle.min.css', array(), SMPUSHVERSION);
  wp_enqueue_script('smpush-frontend');
}

function smpush_scripts(){
  wp_register_script('smpush-progbarscript', smpush_jspath.'/jquery.progressbar.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-mainscript', smpush_jspath.'/smio-function.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-plugins', smpush_jspath.'/smio-plugins.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-select2-js', smpush_jspath.'/select2.min.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-gmap-js', smpush_jspath.'/gmap.js', array('jquery', 'smpush-gmap-source'), SMPUSHVERSION);
  wp_register_script('smpush-emojipicker', smpush_jspath.'/emojionearea.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-jquery-labelauty', smpush_jspath.'/jquery-labelauty.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-jquery-sliderAccess', smpush_jspath.'/jquery-ui-sliderAccess.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-timepicker-addon', smpush_jspath.'/jquery-ui-timepicker-addon.min.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-moment-js', smpush_jspath.'/moment.min.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-chart-bundle', smpush_jspath.'/Chart.bundle.min.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-chart-lib', smpush_jspath.'/Chart.min.js', array('jquery'), SMPUSHVERSION);
  wp_register_script('smpush-BeePlugin', 'https://app-rsrc.getbee.io/plugin/BeePlugin.js', array('jquery'), SMPUSHVERSION);
  wp_register_style('smpush-jquery-smoothness', smpush_csspath.'/smoothness.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-mainstyle', smpush_csspath.'/autoload-style.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-style', smpush_csspath.'/smio-style.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-progbarstyle', smpush_csspath.'/smio-progressbar.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-select2-style', smpush_csspath.'/select2.min.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-labelauty-style', smpush_csspath.'/jquery-labelauty.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-timepicker-addon', smpush_csspath.'/jquery-ui-timepicker-addon.min.css', array(), SMPUSHVERSION);
  wp_register_style('smpush-emojipicker', smpush_csspath.'/emojionearea.css', array(), SMPUSHVERSION);

  wp_enqueue_style('smpush-mainstyle');
  if(is_rtl()){
    wp_register_style('smpush-rtl', smpush_csspath.'/smio-style-rtl.css', array(), SMPUSHVERSION);
  }
  if(get_bloginfo('version') > 3.7){
    wp_register_style('smpush-fix38', smpush_csspath.'/autoload-style38.css', array(), SMPUSHVERSION);
    wp_enqueue_style('smpush-fix38');
  }
}

function smpush_new_blog_installed($blog_id, $user_id, $domain, $path, $site_id, $meta) {
  $purchase_code = $google_apikey = '';
  if(is_multisite()){
    $settings = get_option('smpush_options');
    $purchase_code = $settings['purchase_code'];
    $google_apikey = $settings['google_apikey'];
  }
  smpush_install_code($blog_id, $purchase_code, $google_apikey);
}

function smpush_install(){
  global $wpdb;
  if(is_multisite()){
    $blogs = $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs");
    if($blogs){
      foreach($blogs as $blog){
        smpush_install_code($blog->blog_id);
      }
    }
  }
  else{
    smpush_install_code();
  }
}

function smpush_install_code($blog_id = false, $purchase_code='', $google_apikey=''){
  if(smpush_mobapp_mode && is_multisite()){
    if(!empty(get_option('smpush_network_authkey'))){
      $network_authkey = get_option('smpush_network_authkey');
    }
    else{
      $network_authkey = smpush_helper::saltHash(25);
      update_option('smpush_network_authkey', $network_authkey);
    }
  }
  if($blog_id !== false){
    switch_to_blog($blog_id);
  }
  if(get_option('smpush_version') > 0){
    if($blog_id !== false){
      restore_current_blog();
    }
    return;
  }
  require_once(ABSPATH.'wp-admin/includes/upgrade.php');
  require(smpush_dir.'/install.php');
}

function smpush_upgrade($version){
  require_once(smpush_dir.'/upgrade.php');
}

function smpush_uninstall(){
  global $wpdb;
  if(is_multisite()){
    $blogs = $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs");
    if($blogs){
      foreach($blogs as $blog){
        switch_to_blog($blog->blog_id);
        smpush_uninstall_code();
      }
      restore_current_blog();
    }
  }
  else{
    smpush_uninstall_code();
  }
}

function smpush_uninstall_code(){
  $settings = get_option('smpush_options');
  if($settings['uninstall_action'] == 'files'){
    return;
  }
  global $wpdb;
  global $wp_rewrite;
  $wpdb->hide_errors();
  if(empty($settings) || $settings['uninstall_action'] == 'data' || $settings['uninstall_action'] == 'destroy'){
    $wp_rewrite->flush_rules();
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_queue`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."sm_push_tokens`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_channels`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_relation`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_connection`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_feedback`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_archive`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_cron_queue`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_archive_reports`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_statistics`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_events`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_events_queue`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_desktop_messages`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_autorss_data`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_autorss_sources`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_history`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_newsletter_templates`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_newsletter_views`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_notifier`");
    $wpdb->query("DROP TABLE `".$wpdb->prefix."push_subscriptions`");
    if(empty($settings) || $settings['uninstall_action'] == 'destroy'){
      delete_option('smpush_options');
      delete_option('widget_smpush_widget');
    }
    delete_option('smpush_version');
    delete_option('smpush_network_authkey');
    delete_option('smpush_history');
    delete_option('smpush_instant_send');
    delete_option('smpush_cron_stats');
    delete_option('smpush_woo_reminders');
    delete_option('smpush_stats');
    wp_clear_scheduled_hook('smpush_recurring_cron');
    wp_clear_scheduled_hook('smpush_silent_cron');
    wp_clear_scheduled_hook('smpush_update_counters');
    wp_clear_scheduled_hook('smpush_cron_fewdays');
    @unlink(ABSPATH.'/smart_manifest.js');
    @unlink(ABSPATH.'/smart_service_worker.js');
    @unlink(ABSPATH.'/smart_bridge.php');
    @unlink(ABSPATH.'/smart_push_sw.js');
  }
}

function smiopush_expiration_rememberme(){
  echo '<script>document.getElementById("rememberme").checked = true</script>';
}

function smiopush_bb_expiration_rememberme(){
  echo '<script>document.getElementById("bp-login-widget-rememberme").checked = true</script>';
}

function smiopush_expiration_filter($seconds, $user_id, $remember){
  return 15552000;
}