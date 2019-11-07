<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once(ABSPATH.'wp-admin/includes/upgrade.php');

@set_time_limit(0);

global $wpdb;
$wpdb->hide_errors();

$version = str_replace(',', '.', $version);

if($version < 2.0){
  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_feedback` (
    `id` tinyint(4) NOT NULL AUTO_INCREMENT,
    `tokens` longtext NOT NULL,
    `feedback` longtext NOT NULL,
    `device_type` set('ios','android','ios_invalid') NOT NULL,
    PRIMARY KEY (`id`)
    )";
  dbDelta($sql);
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_queue` ADD `expire` SMALLINT NOT NULL ,
    ADD `ios_slide` VARCHAR( 40 ) NOT NULL ,
    ADD `feedback` BOOLEAN NOT NULL");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ADD `information` TINYTEXT NOT NULL,
    ADD `active` BOOLEAN NOT NULL");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_queue` CHANGE `device_type` `device_type` VARCHAR( 10 ) NOT NULL");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `info_name` VARCHAR(50) NOT NULL AFTER `android_name`,
    ADD `active_name` VARCHAR(20) NOT NULL AFTER `info_name`");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_connection` SET active_name='active',info_name='information' WHERE id='1'");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_tokens` SET `active`='1'");
  $version = 2.0;
}
if($version == 2.0){
  $version = 2.1;
}
if($version == 2.1){
  $version = 2.2;
}
if($version == 2.2){
  $wpdb->query("TRUNCATE `".$wpdb->prefix."push_queue`");
  $wpdb->query("ALTER TABLE  `".$wpdb->prefix."push_queue` DROP  `extravalue` ,
    DROP  `extra_type` ,
    DROP  `expire` ,
    DROP  `ios_slide`");
  $wpdb->query("ALTER TABLE  `".$wpdb->prefix."push_queue` ADD  `options` MEDIUMTEXT NOT NULL");
  $version = 2.3;
}
if($version == 2.3){
  $setting = get_option('smpush_options');
  update_option('smpush_options', unserialize($setting));
  $version = 2.4;
}
if($version == 2.4){
  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_archive` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `message` mediumtext NOT NULL,
    `starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `endtime` datetime DEFAULT NULL,
    `report` mediumtext NOT NULL,
    PRIMARY KEY (`id`)
    )";
  dbDelta($sql);
  $wpdb->query("ALTER TABLE  `".$wpdb->prefix."push_queue` DROP  `message` ,DROP  `options`");
  add_option('smpush_history', '');
  $version = 2.5;
}
if($version == 2.5){
  $version = 2.6;
}
if($version == 2.6){
  $sql = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_cron_queue` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `token` varchar(255) NOT NULL,
    `device_type` varchar(10) NOT NULL,
    `sendtime` varchar(50) NOT NULL,
    `sendoptions` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sendtime` (`sendtime`),
    KEY `device_type` (`device_type`)
    )";
  dbDelta($sql);
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_queue` ADD INDEX (`device_type`)");
  $setting = get_option('smpush_options');
  $setting['e_apprpost'] = 0;
  $setting['e_appcomment'] = 0;
  $setting['e_newcomment'] = 0;
  $setting['e_usercomuser'] = 0;
  $setting['e_postupdated'] = 0;
  $setting['e_newpost'] = 0;
  $setting['e_apprpost_body'] = __('Your post "{subject}" is approved and published', 'smpush-plugin-lang');
  $setting['e_appcomment_body'] = __('Your comment "{comment}" is approved and published now', 'smpush-plugin-lang');
  $setting['e_newcomment_body'] = __('Your post "{subject}" have new comments, Keep in touch with your readers', 'smpush-plugin-lang');
  $setting['e_usercomuser_body'] = __('Someone reply on your comment "{comment}"', 'smpush-plugin-lang');
  $setting['e_postupdated_body'] = __('The post you subscribed in "{subject}" got updated', 'smpush-plugin-lang');
  $setting['e_newpost_body'] = __('We have published a new topic "{subject}"', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);
  $version = 3;
}
if($version == 3){
  $version = 3.1;
}
if($version == 3.1){
  $version = 3.2;
}
if($version == 3.2){
  $version = 3.3;
}
if($version == 3.3){
  $setting = get_option('smpush_options');
  $setting['ios_titanium_payload'] = 0;
  $setting['android_titanium_payload'] = 0;
  update_option('smpush_options', $setting);
  $version = 3.4;
}
if($version == 3.4){
  $version = 3.5;
}
if($version == 3.5){
  $setting = get_option('smpush_options');
  $setting['complex_auth'] = 0;
  update_option('smpush_options', $setting);
  $version = 3.6;
}
if($version == 3.6){
  $setting = get_option('smpush_options');
  $setting['e_post_chantocats'] = 0;
  update_option('smpush_options', $setting);
  $version = 3.7;
}
if($version == 3.7){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD  `transient` VARCHAR( 50 ) NOT NULL");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_cron_queue` ADD INDEX (`sendoptions`)");
  $version = 3.8;
}
if($version == 3.8){
  $version = 3.9;
}
if($version == 3.9){
  $version = 3.91;
}
if($version == 3.91){
  $version = 3.92;
}
if($version == 3.92){
  $version = 3.93;
}
if($version == 3.93){
  $version = 3.94;
}
if($version == 3.94){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `latitude_name` varchar(50) NOT NULL AFTER `info_name`, ADD `longitude_name` varchar(50) NOT NULL AFTER `latitude_name`, ADD `gpstime_name` varchar(50) NOT NULL AFTER `longitude_name`;");
  $wpdb->update($wpdb->prefix.'push_connection', array('latitude_name' => 'latitude', 'longitude_name' => 'longitude', 'gpstime_name' => 'gps_time_update'), array('tbname' => '{wp_prefix}push_tokens'));
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ADD `latitude` DECIMAL(10, 8) NOT NULL AFTER `information`, ADD `longitude` DECIMAL(11, 8) NOT NULL AFTER `latitude`, ADD `gps_time_update` VARCHAR(15) NOT NULL AFTER `longitude`;");
  $setting = get_option('smpush_options');
  $setting['stop_summarize'] = 0;
  $setting['geo_provider'] = 'telize.com';
  $setting['db_ip_apikey'] = '';
  $setting['auto_geo'] = 1;
  update_option('smpush_options', $setting);
  $version = 4.0;
}
if($version == 4.0){
  $version = 4.1;
}
if($version == 4.1){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` CHANGE `device_type` `device_type` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `wp_name` VARCHAR(20) NOT NULL AFTER `android_name`, ADD `bb_name` VARCHAR(20) NOT NULL AFTER `wp_name`");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `md5token_name` VARCHAR(50) NOT NULL AFTER `token_name`");
  $wpdb->update($wpdb->prefix.'push_connection', array('wp_name' => 'wp', 'bb_name' => 'bb', 'md5token_name' => 'md5device_token'), array('tbname' => '{wp_prefix}push_tokens'));
  $setting = get_option('smpush_options');
  $setting['wp_authed'] = '0';
  $setting['wp_cert'] = '';
  $setting['wp_pem'] = '';
  $setting['wp_cainfo'] = '';
  $setting['bb_appid'] = '';
  $setting['bb_password'] = '';
  $setting['bb_cpid'] = '';
  $setting['bb_dev_env'] = 0;
  $setting['android_corona_payload'] = 0;
  $setting['purchase_code'] = '';
  update_option('smpush_options', $setting);
  smpush_move_certs();
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ADD `md5device_token` VARCHAR(32) NOT NULL AFTER `device_token`");
  $wpdb->query('UPDATE `'.$wpdb->prefix.'push_tokens` SET `md5device_token`=MD5(`device_token`)');
  $wpdb->query('ALTER TABLE '.$wpdb->prefix.'push_tokens DROP INDEX device_token');
  $wpdb->query('ALTER TABLE '.$wpdb->prefix.'push_tokens ADD INDEX(`md5device_token`)');
  $version = 4.2;
}
if($version == 4.2){
  $version = 4.3;
}
if($version == 4.3){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_cron_queue` CHANGE `sendoptions` `sendoptions` INT NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_feedback` CHANGE `device_type` `device_type` SET('ios','android','ios_invalid','chrome','firefox')");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` DROP `report`,DROP `transient`");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD `options` TEXT NULL DEFAULT NULL");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD `send_type` ENUM('sendnow','cronsend','feedback') NOT NULL AFTER `id`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD `desktop` VARCHAR(50) NOT NULL");
  $wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_statistics` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `date` date NOT NULL,
      `platid` int(11) NOT NULL,
      `action` varchar(10) NOT NULL,
      `stat` int(11) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  $wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_archive_reports` (
      `msgid` int(11) NOT NULL,
      `report_time` varchar(15) NOT NULL,
      `report` text NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `chrome_name` VARCHAR(20) NOT NULL AFTER `bb_name`, ADD `safari_name` VARCHAR(20) NOT NULL AFTER `chrome_name`, ADD `firefox_name` VARCHAR(20) NOT NULL AFTER `safari_name`");
  $wpdb->update($wpdb->prefix.'push_connection', array('chrome_name' => 'chrome', 'safari_name' => 'safari', 'firefox_name' => 'firefox'), array('tbname' => '{wp_prefix}push_tokens'));
  add_option('smpush_instant_send', array());
  add_option('smpush_cron_stats', array());
  add_option('smpush_stats', array());

  $setting = get_option('smpush_options');
  $setting['chrome_apikey'] = '';
  $setting['desktop_status'] = '0';
  $setting['desktop_modal'] = '0';
  $setting['desktop_modal_title'] = __('Keep me posted', 'smpush-plugin-lang');
  $setting['desktop_modal_message'] = __('Give us a permission to receive push notification messages and we will keep you posted !', 'smpush-plugin-lang');
  $setting['desktop_deficon'] = '';
  $setting['desktop_chrome_status'] = '0';
  $setting['chrome_projectid'] = '';
  $setting['desktop_firefox_status'] = '0';
  $setting['desktop_safari_status'] = '0';
  $setting['safari_cert_path'] = '';
  $setting['safari_passphrase'] = '';
  $setting['safari_web_id'] = '';
  $setting['desktop_btn_subs_text'] = __('Enable Push Messages', 'smpush-plugin-lang');
  $setting['desktop_btn_unsubs_text'] = __('Disable Push Messages', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);

  $version = 5.0;
}
if($version == 5.0){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive_reports` ADD INDEX(`msgid`)");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `wp10_name` VARCHAR(20) NOT NULL AFTER `firefox_name`");
  $wpdb->update($wpdb->prefix.'push_connection', array('wp10_name' => 'wp10'), array('tbname' => '{wp_prefix}push_tokens'));

  $setting = get_option('smpush_options');
  $setting['desktop_modal_cancel_text'] = __('Ignore', 'smpush-plugin-lang');
  $setting['wp10_pack_sid'] = '';
  $setting['wp10_client_secret'] = '';
  $setting['safari_certp12_path'] = '';
  $setting['safari_icon'] = '';
  if($setting['geo_provider'] == 'telize.com'){
    $setting['geo_provider'] = 'ip-api.com';
  }
  update_option('smpush_options', $setting);
  $version = 5.1;
}
if($version == 5.1){
  $version = 5.2;
}
if($version == 5.2){
  $wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_events_queue` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `post_id` int(11) NOT NULL,
      `old_status` varchar(50) NOT NULL,
      `new_status` varchar(50) NOT NULL,
      `post` mediumtext NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  $wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_events` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(200) NOT NULL,
      `event_type` varchar(50) NOT NULL,
      `post_type` varchar(50) NOT NULL,
      `message` text NOT NULL,
      `notify_segment` varchar(50) NOT NULL,
      `userid_field` varchar(100) NOT NULL,
      `conditions` text NOT NULL,
      `desktop_link` BOOLEAN NOT NULL,
      `ignore` tinyint(1) NOT NULL,
      `status` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

  $setting = get_option('smpush_options');
  $wpdb->insert($wpdb->prefix.'push_events', array('title' => __('Notify all members when administrator published a new post', 'smpush-plugin-lang'), 'event_type' => 'publish', 'post_type' => 'post', 'message' => str_replace('{subject}', '{$post_title}', $setting['e_newpost_body']), 'notify_segment' => 'all', 'status' => $setting['e_newpost'], 'desktop_link' => 1));
  $wpdb->insert($wpdb->prefix.'push_events', array('title' => __('Notify author when administrator approved and published his post', 'smpush-plugin-lang'), 'event_type' => 'approve', 'post_type' => 'post', 'message' => str_replace('{subject}', '{$post_title}', $setting['e_apprpost_body']), 'notify_segment' => 'post_owner', 'status' => $setting['e_apprpost'], 'desktop_link' => 1));
  $wpdb->insert($wpdb->prefix.'push_events', array('title' => __('Notify all users subscribed in a post when has got a new update', 'smpush-plugin-lang'), 'event_type' => 'update', 'post_type' => 'post', 'message' => str_replace('{subject}', '{$post_title}', $setting['e_postupdated_body']), 'notify_segment' => 'post_commenters', 'status' => $setting['e_postupdated'], 'desktop_link' => 1));
  unset($setting['e_newpost']);
  unset($setting['e_newpost_body']);
  unset($setting['e_apprpost']);
  unset($setting['e_apprpost_body']);
  unset($setting['e_postupdated']);
  unset($setting['e_postupdated_body']);
  $setting['bb_notify_friends'] = 0;
  $setting['bb_notify_messages'] = 0;
  $setting['bb_notify_activity'] = 0;
  $setting['bb_notify_xprofile'] = 0;
  update_option('smpush_options', $setting);
  $version = 5.3;
}
if($version == 5.3){
  $setting = get_option('smpush_options');
  $setting['bb_notify_activity_admins_only'] = 1;
  update_option('smpush_options', $setting);
  $version = 5.4;
}
if($version == 5.4){
  $wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."push_desktop_messages` (
      `msgid` int(11) NOT NULL,
      `token` varchar(32) NOT NULL,
      `type` varchar(10) NOT NULL,
      `timepost` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

  $setting = get_option('smpush_options');
  $setting['desktop_debug'] = 0;
  update_option('smpush_options', $setting);
  $version = 5.5;
}
if($version == 5.5){
  $version = 5.6;
}
if($version <= 5.6){
  $setting = get_option('smpush_options');
  $setting['gmaps_apikey'] = '';
  update_option('smpush_options', $setting);
  $version = 5.7;
}
if($version <= 5.7){
  $version = 5.8;
}
if($version <= 5.8){
  $setting = get_option('smpush_options');
  $setting['desktop_logged_only'] = 0;
  $setting['apple_appid'] = '';
  $setting['desktop_modal_saved_text'] = __('Saved', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);
  $version = 5.9;
}
if($version <= 5.9){
  $setting = get_option('smpush_options');
  $setting['apple_api_ver'] = 'http2';
  update_option('smpush_options', $setting);
  $version = 5.91;
}
if($version <= 5.91){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ADD `last_geomsg_time` VARCHAR(15) NOT NULL DEFAULT '0' AFTER `gps_time_update`");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD `repeat_interval` SMALLINT NOT NULL AFTER `endtime`, ADD `repeat_age` VARCHAR(15) NOT NULL AFTER `repeat_interval`");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` CHANGE `send_type` `send_type` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` CHANGE `options` `options` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD `status` BOOLEAN NOT NULL AFTER `desktop`, ADD `processed` BOOLEAN NOT NULL AFTER `status`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD `latitude` DECIMAL(10,8) NULL AFTER `desktop`, ADD `longitude` DECIMAL(11,8) NULL AFTER `latitude`, ADD `radius` MEDIUMINT NOT NULL AFTER `longitude`, ADD `gps_expire_time` SMALLINT NOT NULL AFTER `radius`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD `platforms` VARCHAR(200) NOT NULL AFTER `id`, ADD `name` VARCHAR(200) NOT NULL AFTER `platforms`;");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_archive` SET `send_type`='live',processed='1' WHERE `send_type`='sendnow'");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_archive` SET `send_type`='custom',processed='1' WHERE `send_type`='cronsend'");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_archive` SET `platforms`='[\"all\"]',status='1'");
  $wpdb->query("TRUNCATE `".$wpdb->prefix."push_cron_queue`");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` CHANGE `latidude_name` `latitude_name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `geotimeout_name` VARCHAR(50) NOT NULL AFTER `gpstime_name`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_statistics` CHANGE `platid` `platid` VARCHAR(20) NOT NULL, ADD `msgid` INT NOT NULL AFTER `platid`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` CHANGE `latidude` `latitude` DECIMAL(10,8) NOT NULL;");
  $wpdb->query("DELETE FROM `".$wpdb->prefix."push_desktop_messages` WHERE `type`='safari'");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events` ADD `payload_fields` TEXT NOT NULL AFTER `conditions`, ADD `msg_template` INT NOT NULL AFTER `payload_fields`");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_feedback` ADD `msgid` INT NOT NULL AFTER `device_type`, ADD `timepost` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `msgid`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_cron_queue` ADD `timepost` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `sendoptions`;");
  $wpdb->update($wpdb->prefix.'push_connection', array('latitude_name' => 'latitude', 'geotimeout_name' => 'last_geomsg_time'), array('tbname' => '{wp_prefix}push_tokens'));

  $setting = get_option('smpush_options');
  if($setting['desktop_modal'] == 1){
    $setting['desktop_request_type'] = 'popup';
  }
  else{
    $setting['desktop_request_type'] = 'native';
  }
  $setting['ios_badge'] = '';
  $setting['ios_launch'] = '';
  $setting['ios_sound'] = 'default';
  $setting['android_fcm_msg'] = 0;
  $setting['android_title'] = '';
  $setting['android_icon'] = '';
  $setting['android_sound'] = 'default';
  $setting['desktop_title'] = '';
  $setting['desktop_popup_position'] = 'center';
  $setting['desktop_icon_message'] = __('Give us a permission to receive push notification messages and we will keep you posted !', 'smpush-plugin-lang');
  $setting['desktop_icon_position'] = 'bottomright';
  $setting['desktop_popup_css'] = '';
  $setting['desktop_delay'] = 0;
  $setting['desktop_admins_only'] = 0;
  $setting['desktop_gps_status'] = 0;
  $setting['desktop_paytoread'] = 0;
  $setting['desktop_reqagain'] = 3;
  $setting['desktop_run_places'] = array(0 => 'all');
  unset($setting['desktop_modal']);
  update_option('smpush_options', $setting);
  $version = 6.0;
}
if($version <= 6.0){
  $setting = get_option('smpush_options');
  $setting['metabox_check_status'] = 0;
  $setting['e_newcomment_allusers'] = 0;
  $setting['e_newcomment_allusers_body'] = __('Notify all users that commented on a post when adding a new comment on this post', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);
  $version = 6.1;
}
if($version <= 6.1){
  $setting = get_option('smpush_options');
  $setting['desktop_popup_layout'] = 'modern';
  $setting['desktop_popupicon'] = '';
  $setting['desktop_showin_pageids'] = '';
  $setting['cron_limit'] = 0;
  update_option('smpush_options', $setting);
  $version = 6.2;
}
if($version <= 6.2){
  $wpdb->query("CREATE TABLE `".$wpdb->prefix."push_autorss_data` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `sourceid` int(11) NOT NULL,
      `campid` int(11) NOT NULL,
      `subject` varchar(200) NOT NULL,
      `content` text NOT NULL,
      `link` text NOT NULL,
      `md5link` varchar(32) NOT NULL,
      `published` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  $wpdb->query("CREATE TABLE `".$wpdb->prefix."push_autorss_sources` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `campid` int(11) NOT NULL,
      `title` varchar(150) NOT NULL,
      `link` text NOT NULL,
      `text_limit` int(11) NOT NULL,
      `read_limit` int(11) NOT NULL,
      `read_status` tinyint(1) NOT NULL,
      `read_error` text NOT NULL,
      `lastupdate` varchar(15) NOT NULL,
      `data_counter` int(11) NOT NULL,
      `active` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_feedback` CHANGE `id` `id` INT NOT NULL AUTO_INCREMENT;");
  $setting = get_option('smpush_options');
  $setting['desktop_paytoread_message'] = '';
  $setting['desktop_welc_status'] = 0;
  $setting['desktop_welc_title'] = '';
  $setting['desktop_welc_message'] = '';
  $setting['desktop_welc_icon'] = '';
  $setting['desktop_welc_link'] = '';
  $setting['vip'] = 1;
  update_option('smpush_options', $setting);
  $version = 6.3;
}
if($version <= 6.3){
  $version = 6.31;
}
if($version <= 6.31){
  $setting = get_option('smpush_options');
  $setting['ios_onebyone'] = 0;
  update_option('smpush_options', $setting);
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events_queue` ADD `pushtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `post`;");
  $version = 6.32;
}
if($version <= 6.32){
  $setting = get_option('smpush_options');
  $setting['desktop_notsupport_msg'] = '';
  update_option('smpush_options', $setting);
  $version = 6.4;
}
if($version <= 6.4){
  $setting = get_option('smpush_options');
  $setting['desktop_paytoread_darkness'] = 9;
  update_option('smpush_options', $setting);
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events_queue` ADD `pushtime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `post`;");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_events` SET `desktop_link`='1'");
  $version = 6.41;
}
if($version <= 6.41){
  $setting = get_option('smpush_options');
  $setting['desktop_paytoread_textsize'] = '';
  $setting['desktop_paytoread_substext'] = '';
  update_option('smpush_options', $setting);
  $version = 6.5;
}
if($version <= 6.5){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `opera_name` VARCHAR(20) NOT NULL AFTER `wp10_name`, ADD `samsung_name` VARCHAR(20) NOT NULL AFTER `opera_name`, ADD `fbmsn_name` VARCHAR(20) NOT NULL AFTER `samsung_name`, ADD `fbnotify_name` VARCHAR(20) NOT NULL AFTER `fbmsn_name`, ADD `email_name` VARCHAR(20) NOT NULL AFTER `fbnotify_name`;");
  $wpdb->update($wpdb->prefix.'push_connection', array('opera_name' => 'opera', 'samsung_name' => 'samsung', 'fbmsn_name' => 'fbmsn', 'fbnotify_name' => 'fbnotify', 'email_name' => 'email'), array('tbname' => '{wp_prefix}push_tokens'));

  $setting = get_option('smpush_options');
  $setting['desktop_opera_status'] = '0';
  $setting['desktop_samsung_status'] = '0';
  $setting['msn_verify'] = rand(10000,20000);
  $setting['msn_appid'] = '';
  $setting['msn_secret'] = '';
  $setting['msn_oldaccesstoken'] = '';
  $setting['msn_accesstoken'] = '';
  $setting['msn_subscribe_error'] = '';
  $setting['fbnotify_appid'] = '';
  $setting['fbnotify_secret'] = '';
  $setting['fbnotify_applink'] = '';
  $setting['fbnotify_method'] = 'iframe';
  $setting['fbnotify_width'] = '';
  $setting['fbnotify_height'] = '';
  $setting['smtp_status'] = 0;
  $setting['smtp_host'] = '';
  $setting['smtp_port'] = '';
  $setting['smtp_username'] = '';
  $setting['smtp_password'] = '';
  $setting['msn_widget_status'] = 0;
  $setting['msn_fbpage_link'] = '';
  update_option('smpush_options', $setting);

  $wpdb->query("CREATE TABLE `".$wpdb->prefix."push_newsletter_templates` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `msgid` int(11) NOT NULL,
      `title` varchar(150) NOT NULL,
      `template` text NOT NULL,
      `static` tinyint(1) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
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

  $wpdb->query("CREATE TABLE `".$wpdb->prefix."push_newsletter_views` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `msgid` int(11) NOT NULL,
      `platid` smallint(6) NOT NULL,
      `deviceid` int(11) NOT NULL,
      `action` varchar(10) NOT NULL,
      `timepost` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

  $version = 7.0;
}
if($version <= 7){
  $version = 7.1;
}
if($version <= 7.1){
  $setting = get_option('smpush_options');

  $connection = array(
    'title' => __('Default Connection', 'smpush-plugin-lang'),
    'description' => __('Plugin default connection', 'smpush-plugin-lang'),
    'dbtype' => 'localhost',
    'tbname' => '{wp_prefix}push_tokens',
    'id_name' => 'id',
    'token_name' => 'device_token',
    'md5token_name' => 'md5device_token',
    'type_name' => 'device_type',
    'ios_name' => 'ios',
    'android_name' => 'android',
    'wp_name' => 'wp',
    'bb_name' => 'bb',
    'chrome_name' => 'chrome',
    'safari_name' => 'safari',
    'firefox_name' => 'firefox',
    'opera_name' => 'opera',
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
    'active_name' => 'active',
    'counter' => '0',
  );
  $bool = $wpdb->get_var("SELECT id FROM `".$wpdb->prefix."push_connection` WHERE tbname='{wp_prefix}push_tokens'");
  if(!empty($bool)){
    $wpdb->update($wpdb->prefix.'push_connection', $connection, array('id' => $bool));
  }
  else{
    $wpdb->insert($wpdb->prefix.'push_connection', $connection);
    $setting['def_connection'] = $wpdb->insert_id;
  }

  $setting['msn_widget_title'] = __('Send us a message on Facebook', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);

  $version = 7.2;
}
if($version <= 7.2){
  $setting = get_option('smpush_options');
  $setting['msgs_interval'] = 10;
  update_option('smpush_options', $setting);
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ADD `receive_again_at` VARCHAR(15) NOT NULL DEFAULT '0' AFTER `last_geomsg_time`;");
  $version = 7.3;
}
if($version <= 7.3){
  $version = 7.31;
}
if($version <= 7.31){
  $setting = get_option('smpush_options');
  $setting['smtp_secure'] = '';
  update_option('smpush_options', $setting);
  $version = 7.32;
}
if($version <= 7.32){
  $setting = get_option('smpush_options');
  $setting['msn_lang'] = 'en_US';
  $setting['msn_official_widget_status'] = 0;
  $setting['msn_official_fbpage_id'] = '';
  $setting['msn_btn_fblink'] = '';
  $setting['msn_btn_text'] = __('Send us message', 'smpush-plugin-lang');
  $setting['msn_btn_width'] = 160;
  $setting['msn_btn_height'] = 40;
  $setting['msn_btn_color'] = '#fff';
  $setting['msn_btn_bgcolor'] = '#0084ff';
  $setting['msn_btn_icon'] = smpush_imgpath.'/messenger_w.png';
  $setting['fblogin_btn_text'] = __('Login With Facebook', 'smpush-plugin-lang');
  $setting['fblogin_btn_width'] = 205;
  $setting['fblogin_btn_height'] = 40;
  $setting['fblogin_btn_color'] = '#fff';
  $setting['fblogin_btn_bgcolor'] = '#0084ff';
  $setting['fblogin_btn_icon'] = smpush_imgpath.'/facebook_w.png';
  $setting['fblogin_regin_newsletter'] = 1;
  $setting['fblogin_regin_fbnotifs'] = 0;
  $setting['fblogin_regin_wpuser'] = 1;
  $setting['e_newcomment_mentions'] = 0;
  $setting['e_newcomment_mentions_body'] = __('Someone mention you in comment "{comment}"', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);
  $version = 7.4;
}
if($version <= 7.4){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ADD `timepost` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `receive_again_at`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `postdate` VARCHAR(20) NOT NULL AFTER `geotimeout_name`;");
  $wpdb->query("CREATE TABLE `".$wpdb->prefix."push_history` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `msgid` int(11) NOT NULL,
      `userid` int(11) NOT NULL,
      `platform` varchar(10) NOT NULL,
      `timepost` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  $wpdb->update($wpdb->prefix.'push_connection', array('postdate' => 'timepost'), array('tbname' => '{wp_prefix}push_tokens'));
  $setting = get_option('smpush_options');
  $setting['desktop_offline'] = 1;
  $setting['e_wpjobman_status'] = 0;
  $setting['e_wpjobman_body'] = __('You have ({counter}) job offers waiting you, good luck!', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);
  $wpdb->query("UPDATE `".$wpdb->prefix."push_events` SET `desktop_link`='1' WHERE post_type='post'");
  $version = 7.5;
}
if($version <= 7.5){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_cron_queue` CHANGE `sendtime` `sendtime` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_relation` ADD `userid` INT NOT NULL AFTER `connection_id`;");
  $wpdb->query("CREATE TABLE `".$wpdb->prefix."push_subscriptions` (
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
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
  $setting = get_option('smpush_options');
  $setting['msn_woo_checkout'] = 0;
  $setting['msn_woo_cartbtn'] = 0;
  $setting['subspage_geo_status'] = 0;
  $setting['subspage_geo_lat'] = '';
  $setting['subspage_geo_lng'] = '';
  $setting['subspage_geo_acf'] = '';
  $setting['subspage_keywords'] = 1;
  $setting['subspage_channels'] = 1;
  $setting['subspage_cats_status'] = 1;
  $setting['subspage_plat_web'] = 1;
  $setting['subspage_plat_mobile'] = 1;
  $setting['subspage_plat_msn'] = 1;
  $setting['subspage_plat_email'] = 1;
  $setting['subspage_applink_play'] = '';
  $setting['subspage_applink_ios'] = '';
  $setting['subspage_applink_wp'] = '';
  $setting['subspage_post_type'] = 'post';
  $setting['subspage_post_type_tax'] = 'category';
  $setting['subspage_category'] = array();
  update_option('smpush_options', $setting);
  $version = 7.6;
}
if($version <= 7.6){
  $setting = get_option('smpush_options');
  $setting['last_change_time'] = time();
  $setting['subspage_matchone'] = 0;
  update_option('smpush_options', $setting);
  $version = 7.7;
  update_option('smpush_version', $version);
  $innodb = $wpdb->get_var('SELECT SUPPORT FROM INFORMATION_SCHEMA.ENGINES WHERE ENGINE="InnoDB"');
  if($innodb != 'NO'){
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive_reports` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_feedback` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_statistics` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_relation` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_history` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_desktop_messages` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_newsletter_views` ENGINE=InnoDB;");
    $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_subscriptions` ENGINE=InnoDB;");
  }
}
if($version <= 7.71){
  $version = 7.72;
  update_option('smpush_version', $version);
  smpush_controller::setup_htaccess();
  $setting = get_option('smpush_options');
  $setting['fast_bridge'] = 1;
  update_option('smpush_options', $setting);
}
if($version <= 7.72){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events` ADD `email` BOOLEAN NOT NULL AFTER `desktop_link`");
  $version = 7.73;
}
if($version <= 7.73){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events` ADD `fbmsn_message` TEXT NOT NULL AFTER `message`, ADD `fbnotify_message` TEXT NOT NULL AFTER `fbmsn_message`, ADD `email_message` TEXT NOT NULL AFTER `fbnotify_message`;");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_events` SET `fbmsn_message`=`message`,`fbnotify_message`=`message`,`email_message`=`message`");

  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `iosfcm_name` VARCHAR(20) NOT NULL AFTER `ios_name`, ADD `edge_name` VARCHAR(20) NOT NULL AFTER `samsung_name`;");
  $wpdb->update($wpdb->prefix.'push_connection', array('iosfcm_name' => 'iosfcm','edge_name' => 'edge'), array('tbname' => '{wp_prefix}push_tokens'));

  $setting = get_option('smpush_options');
  $setting['apple_cert_type'] = 'pem';
  $setting['apple_certp8_path'] = '';
  $setting['apple_teamid'] = '';
  $setting['apple_keyid'] = '';
  $setting['desktop_edge_status'] = 0;
  $setting['subspage_show_catimages'] = 1;
  $setting['chrome_manifest'] = '';
  update_option('smpush_options', $setting);

  $version = 7.74;
}
if($version <= 7.74){
  $setting = get_option('smpush_options');
  if(!isset($setting['msn_subs_command'])){
    $setting['msn_subs_command'] = '';
    $setting['msn_unsubs_command'] = '';
    update_option('smpush_options', $setting);
  }
  $version = 7.75;
}
if($version <= 7.75){
  $version = 7.76;
}
if($version <= 7.76){
  $version = 7.77;
}
if($version <= 7.77){
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events` ADD `subject` VARCHAR(200) NOT NULL AFTER `post_type`;");
  $setting = get_option('smpush_options');
  $setting['desktop_icon_unsubs_text'] = __('Unsubscribe from receiving our notifications and delete your subscription', 'smpush-plugin-lang');
  $setting['fblogin_btn_redirect'] = '';
  $setting['uninstall_action'] = 'files';
  $setting['desktop_iconimage'] = '';
  $setting['desktop_welc_redir_link'] = '';
  $setting['desktop_welc_redir'] = 0;
  $setting['gdpr_termslink'] = '';
  $setting['gdpr_privacylink'] = '';
  $setting['gdpr_icon'] = 0;
  $setting['gdpr_subs_btn'] = 0;
  $setting['gdpr_ver_option'] = 0;
  $setting['gdpr_ver_text'] = __('By proceeding in this form you will receive our marketing notifications and agree to our #Privacy Policy# and #Terms of Use#', 'smpush-plugin-lang');
  update_option('smpush_options', $setting);
  $version = 7.8;
}
if($version <= 7.8){
  $version = 7.81;
}
if($version <= 7.81){
  $setting = get_option('smpush_options');
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
  update_option('smpush_options', $setting);

  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events` ADD `subs_filter` VARCHAR(10) NOT NULL AFTER `conditions`;");
  $wpdb->query("UPDATE `".$wpdb->prefix."push_events` SET `subs_filter` = 'all'");

  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_cron_queue` ADD `counter` SMALLINT NOT NULL AFTER `device_type`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_queue` ADD `counter` SMALLINT NOT NULL AFTER `device_type`;");

  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_tokens` ADD `counter` SMALLINT NOT NULL AFTER `timepost`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` ADD `counter_name` VARCHAR(20) NOT NULL AFTER `postdate`;");
  $wpdb->update($wpdb->prefix.'push_connection', array('counter_name' => 'counter'), array('tbname' => '{wp_prefix}push_tokens'));

  $version = 7.82;
}
if($version <= 7.82){
  $version = 7.83;
}
if($version <= 7.83){
  $wpdb->query("RENAME TABLE `".$wpdb->prefix."push_tokens` TO `".$wpdb->prefix."sm_push_tokens`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."sm_push_tokens` CHANGE `device_token` `device_token` VARCHAR(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `md5device_token` `md5device_token` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `device_type` `device_type` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `gps_time_update` `gps_time_update` INT UNSIGNED NOT NULL, CHANGE `last_geomsg_time` `last_geomsg_time` INT UNSIGNED NOT NULL DEFAULT '0', CHANGE `receive_again_at` `receive_again_at` INT UNSIGNED NOT NULL DEFAULT '0', CHANGE `counter` `counter` SMALLINT(6) UNSIGNED NOT NULL;");

  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_cron_queue` CHANGE `token` `token` VARCHAR(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `device_type` `device_type` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `counter` `counter` SMALLINT(6) UNSIGNED NOT NULL, CHANGE `sendtime` `sendtime` INT UNSIGNED NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_queue` CHANGE `token` `token` VARCHAR(1000) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `device_type` `device_type` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `counter` `counter` SMALLINT(6) UNSIGNED NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` CHANGE `platforms` `platforms` CHAR(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `name` `name` CHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `send_type` `send_type` CHAR(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `repeat_interval` `repeat_interval` SMALLINT(6) UNSIGNED NOT NULL, CHANGE `repeat_age` `repeat_age` CHAR(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `desktop` `desktop` CHAR(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `radius` `radius` MEDIUMINT(9) UNSIGNED NOT NULL, CHANGE `gps_expire_time` `gps_expire_time` SMALLINT(6) UNSIGNED NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive_reports` CHANGE `report_time` `report_time` INT UNSIGNED NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_autorss_data` CHANGE `subject` `subject` CHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `md5link` `md5link` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_autorss_sources` CHANGE `title` `title` CHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `lastupdate` `lastupdate` INT UNSIGNED NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_channels` CHANGE `title` `title` CHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, CHANGE `description` `description` CHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_desktop_messages` CHANGE `token` `token` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `type` `type` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_feedback` CHANGE `device_type` `device_type` CHAR(15) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_history` CHANGE `platform` `platform` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_newsletter_views` CHANGE `platid` `platid` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `action` `action` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_statistics` CHANGE `platid` `platid` CHAR(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL, CHANGE `action` `action` CHAR(10) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL;");

  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_connection` CHANGE `description` `description` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_archive` ADD INDEX( `send_type`, `starttime`, `status`, `processed`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_autorss_data` ADD INDEX( `sourceid`, `md5link`), ADD INDEX(`published`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_autorss_sources` ADD INDEX( `lastupdate`, `active`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_channels` ADD INDEX( `private`, `default`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_events_queue` ADD INDEX(`pushtime`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_feedback` ADD INDEX(`msgid`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_newsletter_templates` ADD INDEX(`msgid`), ADD INDEX(`static`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_newsletter_views` ADD INDEX( `msgid`, `deviceid`, `action`);");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."sm_push_tokens` ADD INDEX( `md5device_token`, `device_type`), ADD INDEX( `latitude`, `longitude`, `last_geomsg_time`), ADD INDEX(`receive_again_at`), ADD INDEX(`active`);");

  $wpdb->update($wpdb->prefix.'push_connection', array('tbname' => 'sm_push_tokens'), array('tbname' => '{wp_prefix}push_tokens'));

  $setting = get_option('smpush_options');
  $setting['desktop_webpush'] = 0;
  $setting['chrome_vapid_public'] = '';
  $setting['chrome_vapid_private'] = '';
  update_option('smpush_options', $setting);
  $version = 8;
}
if($version <= 8){
  $wpdb->update($wpdb->prefix.'push_connection', array('tbname' => '{wp_prefix}sm_push_tokens'), array('tbname' => 'sm_push_tokens'));
  $version = 8.1;
}
if($version <= 8.1){
  $version = 8.2;
}
if($version <= 8.2){
  $wpdb->query("DELETE FROM ".$wpdb->prefix."push_relation WHERE token_id='0' AND userid='0'");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_queue` ADD `token_id` INT NOT NULL AFTER `id`;");
  $wpdb->query("ALTER TABLE `".$wpdb->prefix."push_cron_queue` ADD `token_id` INT NOT NULL AFTER `id`;");
  $version = 8.3;
}
if($version <= 8.3){
  $setting = get_option('smpush_options');
  $setting['desktop_webpush_old'] = 0;
  update_option('smpush_options', $setting);
  $version = 8.31;
}
if($version <= 8.31){
  $version = 8.32;
}
if($version <= 8.32){
  $version = 8.33;
}
if($version <= 8.33){
  $setting = get_option('smpush_options');
  $setting['webpush_onesignal_payload'] = 0;
  update_option('smpush_options', $setting);
  $version = 8.34;
}
if($version <= 8.4){
  $version = 8.4;
}
if($version <= 8.4){
  $version = 8.41;
}
if($version <= 8.41){
  $version = 8.42;
}
if($version <= 8.42){
  $setting = get_option('smpush_options');
  $setting['settings_version'] = SMPUSHVERSION;
  $setting['no_disturb'] = 0;
  update_option('smpush_options', $setting);

  smpush_controller::setup_bridge();

  $version = 8.43;
}
if($version <= 8.43){
  $setting = get_option('smpush_options');
  $setting['black_overlay'] = 1;
  update_option('smpush_options', $setting);

  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_newsletter_views` ADD `device_hash` CHAR(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL AFTER `deviceid`, ADD INDEX (`device_hash`);');

  smpush_controller::setup_bridge();

  $version = 8.44;
}
if($version <= 8.44){
  smpush_controller::setup_bridge();
  $version = 8.45;
}
if($version <= 8.45){
  $wpdb->query("CREATE TABLE `".$wpdb->prefix."push_notifier` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `userid` int(11) NOT NULL,
    `tokenid` int(11) NOT NULL,
    `object_id` int(11) NOT NULL,
    `type` varchar(15) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

  $settings = get_option('smpush_options');
  $settings['pwa_support'] = 0;
  $settings['amp_support'] = 0;
  $settings['amp_post_widget'] = 0;
  $settings['amp_page_widget'] = 0;
  $settings['amp_post_shortcode'] = 0;
  $settings['amp_page_shortcode'] = 0;
  $settings['pwa_kaludi_support'] = 0;
  update_option('smpush_options', $settings);

  $version = 8.46;
}
if($version <= 8.46){
  $settings = get_option('smpush_options');
  $settings['peepso_notifications'] = 0;
  $settings['pwa_kaludi_support'] = 0;
  update_option('smpush_options', $settings);
  $version = 8.47;
}
if($version <= 8.47){
  $version = 8.48;
}
if($version <= 8.48){
  $version = 8.481;
}
if($version <= 8.481){
  $version = 8.482;
  smpush_controller::setup_bridge();
}
if($version <= 8.482){
  $version = 8.483;
  smpush_controller::setup_bridge();
}
if($version <= 8.483){
  $settings = get_option('smpush_options');
  $settings['fast_bridge'] = 1;
  update_option('smpush_options', $settings);
  $version = 8.484;
}
if($version <= 8.484){
  $version = 8.485;
}
if($version <= 8.485){
  $version = 8.486;
  smpush_controller::setup_bridge();
}
if($version <= 8.486){
  $settings = get_option('smpush_options');
  $settings['subspage_rating'] = 0;
  $settings['pwaforwp_support'] = 0;
  update_option('smpush_options', $settings);
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_subscriptions` ADD `temp` SMALLINT UNSIGNED NOT NULL AFTER `radius`;');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_history` ADD `postid` INT UNSIGNED NOT NULL AFTER `msgid`;');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_desktop_messages` ADD INDEX( `token`, `type`), ADD INDEX(`timepost`);');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_events` ADD INDEX( `event_type`, `post_type`, `status`);');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_events_queue` ADD INDEX(`post_id`);');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_history` ADD INDEX( `userid`, `platform`), ADD INDEX(`postid`);');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_relation` ADD INDEX(`token_id`), ADD INDEX(`userid`);');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_statistics` ADD INDEX( `date`, `platid`, `msgid`, `action`);');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_events` ADD `once_notify` BOOLEAN NOT NULL AFTER `subs_filter`;');
  $version = 8.487;
}
if($version <= 8.487){
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'sm_push_tokens` ADD `firebase` BOOLEAN NOT NULL AFTER `counter`, ADD INDEX(`firebase`);');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_queue` ADD `firebase` BOOLEAN NOT NULL AFTER `feedback`;');
  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_cron_queue` ADD `firebase` BOOLEAN NOT NULL AFTER `timepost`;');

  $wpdb->query('ALTER TABLE `'.$wpdb->prefix.'push_connection` ADD `firebase_name` CHAR(20) NOT NULL AFTER `counter_name`;');
  $wpdb->update($wpdb->prefix.'push_connection', array('firebase_name' => 'firebase'), array('tbname' => '{wp_prefix}sm_push_tokens'));
  $wpdb->update($wpdb->prefix.'push_connection', array('firebase_name' => 'firebase'), array('id' => 1));

  $settings = get_option('smpush_options');
  $settings['desktop_used_webpush'] = $settings['desktop_webpush'];
  $settings['desktop_webpush'] = 1;
  $settings['firebase_auth_file'] = '';
  $settings['firebase_config'] = '';
  update_option('smpush_options', $settings);

  $version = 9;
}
if($version <= 9){
  $version = 9.1;
}

delete_transient('smpush_update_notice');
@unlink(smpush_cache_dir.'/settings');
@unlink(smpush_dir.'/js/frontend_webpush.js');
update_option('smpush_version', str_replace(',', '.', $version));

function smpush_move_certs(){
  global $wpdb;
  if(is_multisite()){
    $blogs = $wpdb->get_results("SELECT blog_id FROM $wpdb->blogs");
    if($blogs){
      foreach($blogs as $blog){
        switch_to_blog($blog->blog_id);
        smpush_move_certs_onesite();
      }
      restore_current_blog();
    }
  }
  else{
    smpush_move_certs_onesite();
  }
}

function smpush_move_certs_onesite(){
  $upload_dir = wp_upload_dir();
  if(! file_exists($upload_dir['basedir'].'/certifications')){
    @mkdir($upload_dir['basedir'].'/certifications');
  }
  $settings = get_option('smpush_options');
  if(empty($settings['apple_cert_path'])){
    return;
  }
  $settings['apple_cert_path'] = stripslashes($settings['apple_cert_path']);
  $target_path = $upload_dir['basedir'].'/certifications/'.basename($settings['apple_cert_path']);
  @rename($settings['apple_cert_path'], $target_path);
  $settings['apple_cert_path'] = addslashes($target_path);
  update_option('smpush_options', $settings);
}