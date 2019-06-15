<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb, $wp_rewrite;

$wpdb->hide_errors();

$dbEngine = 'MyISAM';

$myisamdb = $wpdb->get_var('SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE="MyISAM"');
if($myisamdb == 'NO'){
  $dbEngine = 'InnoDB';
}

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `platforms` char(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `name` char(200) NOT NULL,
  `send_type` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `message` text NOT NULL,
  `starttime` timestamp NOT NULL DEFAULT current_timestamp(),
  `endtime` datetime DEFAULT NULL,
  `repeat_interval` smallint(6) UNSIGNED NOT NULL,
  `repeat_age` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `options` longtext DEFAULT NULL,
  `desktop` char(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `radius` mediumint(9) UNSIGNED NOT NULL,
  `gps_expire_time` smallint(6) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL,
  `processed` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `send_type` (`send_type`,`starttime`,`status`,`processed`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_archive_reports` (
  `msgid` int(11) NOT NULL,
  `report_time` int(10) UNSIGNED NOT NULL,
  `report` text NOT NULL,
  KEY `msgid` (`msgid`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_autorss_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sourceid` int(11) NOT NULL,
  `campid` int(11) NOT NULL,
  `subject` char(200) NOT NULL,
  `content` text NOT NULL,
  `link` text NOT NULL,
  `md5link` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sourceid` (`sourceid`,`md5link`),
  KEY `published` (`published`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_autorss_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campid` int(11) NOT NULL,
  `title` char(150) NOT NULL,
  `link` text NOT NULL,
  `text_limit` int(11) NOT NULL,
  `read_limit` int(11) NOT NULL,
  `read_status` tinyint(1) NOT NULL,
  `read_error` text NOT NULL,
  `lastupdate` int(10) UNSIGNED NOT NULL,
  `data_counter` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lastupdate` (`lastupdate`,`active`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_channels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(50) NOT NULL,
  `description` char(200) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `default` tinyint(1) NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `private` (`private`,`default`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_connection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(200) NOT NULL,
  `dbtype` enum('localhost','remote') NOT NULL,
  `dbhost` char(50) NOT NULL DEFAULT 'localhost',
  `dbname` char(50) NOT NULL,
  `dbuser` char(50) NOT NULL,
  `dbpass` char(50) NOT NULL,
  `tbname` char(50) NOT NULL,
  `id_name` char(50) NOT NULL,
  `token_name` char(50) NOT NULL,
  `md5token_name` char(50) NOT NULL,
  `type_name` char(50) NOT NULL,
  `ios_name` char(20) NOT NULL,
  `iosfcm_name` char(20) NOT NULL,
  `android_name` char(20) NOT NULL,
  `wp_name` char(20) NOT NULL,
  `bb_name` char(20) NOT NULL,
  `chrome_name` char(20) NOT NULL,
  `safari_name` char(20) NOT NULL,
  `firefox_name` char(20) NOT NULL,
  `opera_name` char(20) NOT NULL,
  `edge_name` char(20) NOT NULL,
  `samsung_name` char(20) NOT NULL,
  `fbmsn_name` char(20) NOT NULL,
  `fbnotify_name` char(20) NOT NULL,
  `email_name` char(20) NOT NULL,
  `wp10_name` char(20) NOT NULL,
  `info_name` char(50) NOT NULL,
  `latitude_name` char(50) NOT NULL,
  `longitude_name` char(50) NOT NULL,
  `gpstime_name` char(50) NOT NULL,
  `geotimeout_name` char(50) NOT NULL,
  `postdate` char(20) NOT NULL,
  `counter_name` char(20) NOT NULL,
  `active_name` char(20) NOT NULL,
  `counter` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_cron_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token_id` INT NOT NULL,
  `token` varchar(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `device_type` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `counter` smallint(6) UNSIGNED NOT NULL,
  `sendtime` int(10) UNSIGNED NOT NULL,
  `sendoptions` int(11) NOT NULL,
  `timepost` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `sendtime` (`sendtime`),
  KEY `device_type` (`device_type`),
  KEY `sendoptions` (`sendoptions`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_desktop_messages` (
  `msgid` int(11) NOT NULL,
  `token` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `type` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `timepost` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `event_type` char(50) NOT NULL,
  `post_type` char(50) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `fbmsn_message` text NOT NULL,
  `fbnotify_message` text NOT NULL,
  `email_message` text NOT NULL,
  `notify_segment` char(50) NOT NULL,
  `userid_field` char(100) NOT NULL,
  `conditions` text NOT NULL,
  `subs_filter` char(10) NOT NULL,
  `payload_fields` text NOT NULL,
  `msg_template` int(11) NOT NULL,
  `desktop_link` tinyint(1) NOT NULL,
  `email` tinyint(1) NOT NULL,
  `ignore` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_events_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `old_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `post` mediumtext NOT NULL,
  `pushtime` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `pushtime` (`pushtime`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tokens` longtext NOT NULL,
  `feedback` longtext NOT NULL,
  `device_type` char(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `msgid` int(11) NOT NULL,
  `timepost` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `msgid` (`msgid`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msgid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `platform` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `timepost` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_newsletter_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msgid` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `template` text NOT NULL,
  `static` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `msgid` (`msgid`),
  KEY `static` (`static`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_newsletter_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msgid` int(11) NOT NULL,
  `platid` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `deviceid` int(11) NOT NULL,
  `device_hash` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `action` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `timepost` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `msgid` (`msgid`,`deviceid`,`action`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_notifier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `tokenid` int(11) NOT NULL,
  `object_id` int(11) NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token_id` INT NOT NULL,
  `token` varchar(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `device_type` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `counter` smallint(6) UNSIGNED NOT NULL,
  `feedback` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `device_type` (`device_type`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_relation` (
  `channel_id` int(11) NOT NULL,
  `token_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `connection_id` int(11) NOT NULL,
  KEY `channel_id` (`channel_id`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_statistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `platid` char(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `msgid` int(11) NOT NULL,
  `action` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `stat` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_subscriptions` (
  `userid` int(11) NOT NULL,
  `keywords` varchar(200) NOT NULL,
  `categories` varchar(200) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `radius` smallint(6) NOT NULL,
  `web` tinyint(1) NOT NULL,
  `mobile` tinyint(1) NOT NULL,
  `msn` tinyint(1) NOT NULL,
  `email` tinyint(1) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."sm_push_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `device_token` varchar(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `md5device_token` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `device_type` char(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `information` tinytext NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `gps_time_update` int(10) UNSIGNED NOT NULL,
  `last_geomsg_time` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `receive_again_at` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `timepost` timestamp NOT NULL DEFAULT current_timestamp(),
  `counter` smallint(6) UNSIGNED NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `md5device_token` (`md5device_token`,`device_type`),
  KEY `latitude` (`latitude`,`longitude`,`last_geomsg_time`),
  KEY `receive_again_at` (`receive_again_at`),
  KEY `active` (`active`)
) ENGINE=$dbEngine DEFAULT CHARSET=".DB_CHARSET.";";
dbDelta($sql);

$wpdb->query("INSERT INTO `".$wpdb->prefix."push_channels` (`id`, `title`, `private`, `default`) VALUES (1, '".__('Main Channel', 'smpush-plugin-lang')."', 0, 1);");

$wpdb->insert($wpdb->prefix.'push_connection', array(
  'title' => __('Default Connection', 'smpush-plugin-lang'),
  'description' => __('Plugin default connection', 'smpush-plugin-lang'),
  'dbtype' => 'localhost',
  'tbname' => '{wp_prefix}sm_push_tokens',
  'id_name' => 'id',
  'token_name' => 'device_token',
  'md5token_name' => 'md5device_token',
  'type_name' => 'device_type',
  'ios_name' => 'ios',
  'iosfcm_name' => 'iosfcm',
  'android_name' => 'android',
  'wp_name' => 'wp',
  'bb_name' => 'bb',
  'chrome_name' => 'chrome',
  'safari_name' => 'safari',
  'firefox_name' => 'firefox',
  'opera_name' => 'opera',
  'edge_name' => 'edge',
  'samsung_name' => 'samsung',
  'fbmsn_name' => 'fbmsn',
  'fbnotify_name' => 'fbnotify',
  'email_name' => 'email',
  'wp10_name' => 'wp10',
  'info_name' => 'information',
  'latitude_name' => 'latitude',
  'longitude_name' => 'longitude',
  'gpstime_name' => 'gps_time_update',
  'geotimeout_name' => 'last_geomsg_time',
  'postdate' => 'timepost',
  'counter_name' => 'counter',
  'active_name' => 'active',
  'counter' => '0',
));

$wpdb->insert($wpdb->prefix.'push_events', array('title' => __('Notify all members when administrator published a new post', 'smpush-plugin-lang'), 'event_type' => 'publish', 'post_type' => 'post', 'subject' => __('We have published a new topic', 'smpush-plugin-lang'), 'message' => '{$post_title}', 'notify_segment' => 'all', 'subs_filter' => 'all', 'desktop_link' => 1, 'status' => 1));
$wpdb->insert($wpdb->prefix.'push_events', array('title' => __('Notify author when administrator approved and published his post', 'smpush-plugin-lang'), 'event_type' => 'approve', 'post_type' => 'post', 'subject' => __('Your post is approved and published', 'smpush-plugin-lang'), 'message' => '{$post_title}', 'notify_segment' => 'post_owner', 'subs_filter' => 'all', 'desktop_link' => 1, 'status' => 0));
$wpdb->insert($wpdb->prefix.'push_events', array('title' => __('Notify all users subscribed in a post when has got a new update', 'smpush-plugin-lang'), 'event_type' => 'update', 'post_type' => 'post', 'subject' => __('The post you subscribed in got new updates', 'smpush-plugin-lang'), 'message' => '{$post_title}', 'notify_segment' => 'post_commenters', 'subs_filter' => 'all', 'desktop_link' => 1, 'status' => 0));

$wpdb->query("INSERT INTO `".$wpdb->prefix."push_newsletter_templates` (`id`, `msgid`, `title`, `template`, `static`) VALUES
    (1, 0, 'Basic Ecommerce', 'BF-basic-e-commerce', 1),
    (2, 0, 'Basic Newsletter', 'BF-basic-newsletter', 1),
    (3, 0, 'Basic Onecolumn', 'BF-basic-onecolumn', 1),
    (4, 0, 'Basic Standard', 'BF-basic-standard', 1),
    (5, 0, 'Blank Template', 'BF-blank-template', 1),
    (6, 0, 'Ecommerce Template', 'BF-ecommerce-template', 1),
    (7, 0, 'Newsletter Template', 'BF-newsletter-template', 1),
    (8, 0, 'Promo Template', 'BF-promo-template', 1),
    (9, 0, 'Simple Template', 'BF-simple-template', 1);");

$setting = array(
  'auth_key' => (empty($network_authkey))? smpush_helper::saltHash(25) : $network_authkey,
  'complex_auth' => 0,
  'push_basename' => 'push',
  'def_connection' => 1,
  'apple_sandbox' => 0,
  'stop_summarize' => 0,
  'msgs_interval' => 0,
  'gmaps_apikey' => '',
  'apple_passphrase' => '',
  'apple_cert_path' => '',
  'apple_appid' => '',
  'apple_api_ver' => 'ssl',
  'apple_cert_type' => 'pem',
  'apple_certp8_path' => '',
  'apple_teamid' => '',
  'apple_keyid' => '',
  'ios_onebyone' => 0,
  'desktop_paytoread_message' => '',
  'desktop_welc_status' => 0,
  'desktop_welc_title' => '',
  'desktop_welc_message' => '',
  'desktop_welc_icon' => '',
  'desktop_welc_link' => '',
  'desktop_welc_redir_link' => '',
  'desktop_welc_redir' => 0,
  'google_apikey' => (empty($google_apikey))? '' : $google_apikey,
  'chrome_apikey' => '',
  'chrome_manifest' => '',
  'desktop_offline' => '0',
  'desktop_status' => '0',
  'desktop_debug' => '0',
  'desktop_request_type' => 'popup',
  'desktop_logged_only' => '0',
  'desktop_modal_title' => __('Keep me posted', 'smpush-plugin-lang'),
  'desktop_modal_message' => __('Give us a permission to sending you push notification messages and we will keep you posted !', 'smpush-plugin-lang'),
  'safari_web_id' => '',
  'desktop_popup_layout' => 'flat',
  'desktop_popupicon' => '',
  'desktop_showin_pageids' => '',
  'desktop_btn_subs_text' => __('Subscribe', 'smpush-plugin-lang'),
  'desktop_btn_unsubs_text' => __('Unsubscribe', 'smpush-plugin-lang'),
  'desktop_modal_cancel_text' => __('Ignore', 'smpush-plugin-lang'),
  'desktop_modal_saved_text' => __('Saved', 'smpush-plugin-lang'),
  'desktop_deficon' => '',
  'desktop_notsupport_msg' => '',
  'desktop_paytoread_darkness' => 9,
  'desktop_paytoread_textsize' => '',
  'desktop_paytoread_substext' => '',
  'desktop_chrome_status' => '0',
  'desktop_edge_status' => '0',
  'chrome_projectid' => '',
  'desktop_firefox_status' => '0',
  'desktop_safari_status' => '0',
  'safari_cert_path' => '',
  'safari_certp12_path' => '',
  'safari_icon' => '',
  'safari_passphrase' => '',
  'ios_titanium_payload' => 0,
  'android_titanium_payload' => 0,
  'purchase_code' => 1,//do not steal so you have honor
  'vip' => 1,//do not steal so you have honor
  'wp_authed' => '0',
  'wp_cert' => '',
  'wp_pem' => '',
  'wp10_pack_sid' => '',
  'wp10_client_secret' => '',
  'wp_cainfo' => '',
  'bb_appid' => '',
  'bb_password' => '',
  'bb_cpid' => '',
  'bb_dev_env' => 0,
  'android_corona_payload' => 0,
  'geo_provider' => 'ip-api.com',
  'db_ip_apikey' => '',
  'auto_geo' => 0,
  'cron_limit' => 0,
  'e_post_chantocats' => 0,
  'e_appcomment' => 0,
  'e_newcomment' => 0,
  'e_usercomuser' => 0,
  'e_appcomment_body' => __('Your comment "{comment}" is approved and published now', 'smpush-plugin-lang'),
  'e_newcomment_body' => __('Your post "{subject}" have new comments, Keep in touch with your readers', 'smpush-plugin-lang'),
  'e_usercomuser_body' => __('Someone reply on your comment "{comment}"', 'smpush-plugin-lang'),
  'e_newcomment_allusers' => 0,
  'e_newcomment_allusers_body' => __('Notify all users that commented on a post when adding a new comment on this post', 'smpush-plugin-lang'),
  'e_newcomment_mentions' => 0,
  'e_newcomment_mentions_body' => __('Someone mention you in comment "{comment}"', 'smpush-plugin-lang'),
  'metabox_check_status' => 0,
  'bb_notify_friends' => 0,
  'bb_notify_messages' => 0,
  'bb_notify_activity' => 0,
  'bb_notify_activity_admins_only' => 0,
  'bb_notify_xprofile' => 0,
  'ios_badge' => '',
  'ios_launch' => '',
  'ios_sound' => 'default',
  'android_fcm_msg' => 1,
  'android_title' => '',
  'android_icon' => '',
  'android_sound' => 'default',
  'desktop_title' => '',
  'desktop_popup_position' => 'center',
  'desktop_icon_message' => __('Give us a permission to receive push notification messages and we will keep you posted !', 'smpush-plugin-lang'),
  'desktop_icon_unsubs_text' => __('Unsubscribe from receiving our notifications and delete your subscription', 'smpush-plugin-lang'),
  'desktop_icon_position' => 'bottomright',
  'desktop_iconimage' => '',
  'desktop_popup_css' => '',
  'desktop_delay' => 0,
  'desktop_admins_only' => 0,
  'desktop_gps_status' => 0,
  'desktop_paytoread' => 0,
  'desktop_reqagain' => 3,
  'desktop_run_places' => array(0 => 'all'),
  'desktop_opera_status' => '0',
  'desktop_samsung_status' => '0',
  'msn_verify' => rand(10000,20000),
  'msn_appid' => '',
  'msn_secret' => '',
  'msn_oldaccesstoken' => '',
  'msn_accesstoken' => '',
  'msn_subscribe_error' => '',
  'msn_widget_title' => __('Send us a message on Facebook', 'smpush-plugin-lang'),
  'fbnotify_appid' => '',
  'fbnotify_secret' => '',
  'fbnotify_applink' => '',
  'fbnotify_method' => 'iframe',
  'fbnotify_width' => '',
  'fbnotify_height' => '',
  'smtp_status' => 0,
  'smtp_host' => '',
  'smtp_port' => '',
  'smtp_username' => '',
  'smtp_password' => '',
  'msn_woo_checkout' => 0,
  'msn_woo_cartbtn' => 0,
  'msn_widget_status' => 0,
  'msn_fbpage_link' => '',
  'msn_lang' => 'en_US',
  'msn_official_widget_status' => 0,
  'msn_official_fbpage_id' => '',
  'msn_subs_command' => 'subscribe me',
  'msn_unsubs_command' => 'unsubscribe and delete my account',
  'msn_btn_fblink' => '',
  'msn_btn_text' => __('Send us message', 'smpush-plugin-lang'),
  'msn_btn_width' => 160,
  'msn_btn_height' => 40,
  'msn_btn_color' => '#fff',
  'msn_btn_bgcolor' => '#0084ff',
  'msn_btn_icon' => smpush_imgpath.'/messenger_w.png',
  'fblogin_btn_redirect' => '',
  'fblogin_btn_text' => __('Login With Facebook', 'smpush-plugin-lang'),
  'fblogin_btn_width' => 205,
  'fblogin_btn_height' => 40,
  'fblogin_btn_color' => '#fff',
  'fblogin_btn_bgcolor' => '#0084ff',
  'fblogin_btn_icon' => smpush_imgpath.'/facebook_w.png',
  'fblogin_regin_newsletter' => 1,
  'fblogin_regin_fbnotifs' => 0,
  'fblogin_regin_wpuser' => 1,
  'e_wpjobman_status' => 0,
  'e_wpjobman_body' => __('You have ({counter}) job offers waiting you for ({alert}) alert, good luck!', 'smpush-plugin-lang'),
  'subspage_geo_status' => 0,
  'subspage_geo_lat' => '',
  'subspage_geo_lng' => '',
  'subspage_geo_acf' => '',
  'subspage_keywords' => 1,
  'subspage_channels' => 1,
  'subspage_cats_status' => 1,
  'subspage_plat_web' => 1,
  'subspage_plat_mobile' => 1,
  'subspage_plat_msn' => 1,
  'subspage_plat_email' => 1,
  'subspage_applink_play' => '',
  'subspage_applink_ios' => '',
  'subspage_applink_wp' => '',
  'subspage_post_type' => 'post',
  'subspage_post_type_tax' => 'category',
  'subspage_category' => array(),
  'subspage_matchone' => 0,
  'subspage_show_catimages' => 1,
  'gdpr_termslink' => '',
  'gdpr_privacylink' => '',
  'gdpr_icon' => 0,
  'gdpr_subs_btn' => 0,
  'gdpr_ver_option' => 0,
  'gdpr_ver_text' => __('By proceeding in this form you will receive our marketing notifications and agree to our #Privacy Policy# and #Terms of Use#', 'smpush-plugin-lang'),
  'fast_bridge' => 0,
  'uninstall_action' => 'files',
  'last_change_time' => time(),
);

$setting['android_msg_counter'] = 0;
$setting['ios_msg_counter'] = 0;
$setting['e_woo_waiting'] = 0;
$setting['e_woo_waiting_title'] = __('{title} in stock !', 'smpush-plugin-lang');
$setting['e_woo_waiting_message'] = __('catch your waiting product now is available again with price ${price}', 'smpush-plugin-lang');
$setting['e_woo_abandoned'] = 0;
$setting['e_woo_aband_maxage'] = 24;
$setting['e_woo_aband_times'] = 3;
$setting['e_woo_aband_interval'] = 72;
$setting['e_woo_aband_title'] = __('{customer_name} do not miss your cart !', 'smpush-plugin-lang');
$setting['e_woo_aband_message'] = __('we saved {productscount} items cart but quantities are limited. click here to complete your order', 'smpush-plugin-lang');
$setting['e_woo_aband_last_rem'] = 0;
$setting['e_woo_aband_last_title'] = __('{customer_name} the last call !', 'smpush-plugin-lang');
$setting['e_woo_aband_last_message'] = __('click here and complete now your order with this special coupon', 'smpush-plugin-lang');
$setting['desktop_webpush'] = 0;
$setting['webpush_onesignal_payload'] = 0;
$setting['desktop_webpush_old'] = 0;
$setting['chrome_vapid_public'] = '';
$setting['chrome_vapid_private'] = '';
$setting['settings_version'] = SMPUSHVERSION;
$setting['black_overlay'] = 1;
$setting['no_disturb'] = 0;
$setting['pwa_support'] = 0;
$setting['amp_support'] = 0;
$setting['amp_post_widget'] = 0;
$setting['amp_page_widget'] = 0;
$setting['amp_post_shortcode'] = 0;
$setting['amp_page_shortcode'] = 0;
$setting['pwa_kaludi_support'] = 0;
$setting['peepso_notifications'] = 0;

add_option('smpush_options', $setting);
add_option('smpush_version', str_replace(',', '.', SMPUSHVERSION));
add_option('smpush_instant_send', array());
add_option('smpush_cron_stats', array());
add_option('smpush_stats', array());
add_option('smpush_history', '');

$wp_rewrite->flush_rules(false);
smpush_controller::setup_htaccess();

if($blog_id !== false){
  restore_current_blog();
}