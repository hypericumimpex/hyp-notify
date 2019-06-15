<div class="wrap">
  <div id="smpush-icon-devsetting" class="icon32"><br></div>
  <h2><?php echo __('Smart Notification Settings', 'smpush-plugin-lang')?></h2>

  <p class="description" style="color:red;text-align: right"><?php echo __('Notice: Please reset your cache plugin and purge all cache after changing any options. still face problems please check our', 'smpush-plugin-lang')?> <a href="https://smartiolabs.com/product/push-notification-system/documentation#faq" target="_blank"><?php echo __('FAQ', 'smpush-plugin-lang')?></a></p>

  <div id="col-container" class="smpush-settings-page">
    <form action="<?php echo $page_url; ?>" method="post" id="smpush_jform" class="validate">
      
      <input class="smpush_jradio" name="selectDIV" value="general" type="radio" data-icon="<?php echo smpush_imgpath; ?>/cogs.png" data-labelauty='<?php echo __('General', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="mobile" type="radio" data-icon="<?php echo smpush_imgpath; ?>/mobile.png" data-labelauty='<?php echo __('Mobile Push', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="web" type="radio" data-icon="<?php echo smpush_imgpath; ?>/web.png" data-labelauty='<?php echo __('Web Push', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="messenger" type="radio" data-icon="<?php echo smpush_imgpath; ?>/messenger.png" data-labelauty='<?php echo __('Messenger', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="facebook" type="radio" data-icon="<?php echo smpush_imgpath; ?>/facebook.png" data-labelauty='<?php echo __('Notifications', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="email" type="radio" data-icon="<?php echo smpush_imgpath; ?>/email.png" data-labelauty='<?php echo __('Newsletter', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="subs_page" type="radio" data-icon="<?php echo smpush_imgpath; ?>/form.png" data-labelauty='<?php echo __('Subscription Page', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="events" type="radio" data-icon="<?php echo smpush_imgpath; ?>/events.png" data-labelauty='<?php echo __('Events', 'smpush-plugin-lang')?>' />
      <input class="smpush_jradio" name="selectDIV" value="gdpr" type="radio" data-icon="<?php echo smpush_imgpath; ?>/gdpr.png" data-labelauty='GDPR' />

      <div id="col-left" class="smpush-tabs-mobile smpush-tabs-ios smpush-tabs-android smpush-tabs-corona smpush-tabs-windows smpush-tabs-blackberry smpush-radio-tabs" style="display:none">
        <input class="smpush_jradio" name="selectDIV" value="ios" type="radio" data-icon="<?php echo smpush_imgpath; ?>/apple.png" data-labelauty='<?php echo __('iOS', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="android" type="radio" data-icon="<?php echo smpush_imgpath; ?>/android.png" data-labelauty='<?php echo __('Android', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="corona" type="radio" data-icon="<?php echo smpush_imgpath; ?>/corona.png" data-labelauty='<?php echo __('Corona', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="windows" type="radio" data-icon="<?php echo smpush_imgpath; ?>/wp.png" data-labelauty='<?php echo __('WP', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="blackberry" type="radio" data-icon="<?php echo smpush_imgpath; ?>/blackberry.png" data-labelauty='<?php echo __('BlackBerry', 'smpush-plugin-lang')?>' />
      </div>
      
      <div id="col-left" class="smpush-tabs-web smpush-tabs-desktop smpush-tabs-popup smpush-tabs-chrome smpush-tabs-firefox smpush-tabs-safari smpush-tabs-opera smpush-tabs-edge smpush-tabs-samsung smpush-tabs-amp smpush-tabs-welcmsg smpush-radio-tabs" style="display:none">
        <input class="smpush_jradio" name="selectDIV" value="desktop" type="radio" data-icon="<?php echo smpush_imgpath; ?>/desktop.png" data-labelauty='<?php echo __('General', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="popup" type="radio" data-icon="<?php echo smpush_imgpath; ?>/popup.png" data-labelauty='<?php echo __('Pop-up', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="chrome" type="radio" data-icon="<?php echo smpush_imgpath; ?>/chrome.png" data-labelauty='<?php echo __('Chrome', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="firefox" type="radio" data-icon="<?php echo smpush_imgpath; ?>/firefox.png" data-labelauty='<?php echo __('Firefox', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="safari" type="radio" data-icon="<?php echo smpush_imgpath; ?>/safari.png" data-labelauty='<?php echo __('Safari', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="opera" type="radio" data-icon="<?php echo smpush_imgpath; ?>/opera.png" data-labelauty='<?php echo __('Opera', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="edge" type="radio" data-icon="<?php echo smpush_imgpath; ?>/edge.png" data-labelauty='<?php echo __('Edge', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="samsung" type="radio" data-icon="<?php echo smpush_imgpath; ?>/samsung.png" data-labelauty='<?php echo __('Samsung', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="amp" type="radio" data-icon="<?php echo smpush_imgpath; ?>/amp.png" data-labelauty='<?php echo __('AMP', 'smpush-plugin-lang')?>' />
        <input class="smpush_jradio" name="selectDIV" value="welcmsg" type="radio" data-icon="<?php echo smpush_imgpath; ?>/welcome.png" data-labelauty='<?php echo __('Welcome', 'smpush-plugin-lang')?>' />
      </div>
      
      <div id="col-left" class="smpush-tabs-general smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><span><?php echo __('General Settings', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                  <?php if(!empty($params['canEditApiKeys'])): ?>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Authentication Key', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="auth_key" type="text" value="<?php echo self::$apisetting['auth_key']; ?>" size="50" class="regular-text">
                        <p class="description"><?php echo __('Send this key with any request with a parameter called <code>auth_key</code> to prevent access to API from outside .', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Also you can send this key in the header of each request in a parameter called <code>auth_key</code> for more security .', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Leave it empty to disable this feature .', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                  <?php endif; ?>
                    <?php if (self::$apisetting['complex_auth'] == 1) { ?>
                    <tr valign="top">
                      <td class="first">Complex Authentication</td>
                      <td>
                        <label><input name="complex_auth" type="checkbox" value="1" <?php if (self::$apisetting['complex_auth'] == 1) { ?>checked="checked"<?php } ?>> Put the authentication key into an encrypted string</label>
                        <p class="description">The encrypted string will be in the following format <a href="http://en.wikipedia.org/wiki/MD5" target="_blank">MD5</a>(Date in m/d/y - Your auth key - Time in H:m)</p>
                        <p class="description">For example <a href="http://en.wikipedia.org/wiki/MD5" target="_blank">MD5</a>(<?php echo date('m/d/Y').self::$apisetting['auth_key'].date('H:i'); ?>)</p>
                      </td>
                    </tr>
                    <?php } ?>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('API Base Name', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="push_basename" type="text" value="<?php echo self::$apisetting['push_basename']; ?>" class="regular-text">
                        <p class="description"><span><code><?php echo get_bloginfo('wpurl') ; ?>/</code><abbr>API_BASE_NAME<code>/</code></abbr></span></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Messages Limitation', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="msgs_interval" type="number" value="<?php echo self::$apisetting['msgs_interval']; ?>" class="regular-text" min="0" size="10">
                        <p class="description"><?php echo __('Number of minutes between each message that every device can receive. system will ignore any messages in the unallowed time.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Processed Limitation', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="cron_limit" type="number" value="<?php echo self::$apisetting['cron_limit']; ?>" class="regular-text" min="0" size="10">
                        <p class="description"><?php echo __('Number of processed campaigns in each cron-job time. Set it 0 to be unlimited.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Default Connection', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <select name="def_connection" class="postform">
                          <?php foreach ($params['connections'] AS $connection) { ?>
                            <option value="<?php echo $connection->id; ?>" <?php if ($connection->id == self::$apisetting['def_connection']) { ?>selected="selected"<?php } ?>><?php echo $connection->title; ?></option>
                          <?php } ?>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Auto Geolocation', 'smpush-plugin-lang')?></td>
                      <td><label><input name="auto_geo" type="checkbox" value="1" <?php if (self::$apisetting['auto_geo'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable auto collecting the device location from its connection point if system does not receive the location parameters (Not 100% Accurate)', 'smpush-plugin-lang')?></label></td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Geolocation Provider', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <select name="geo_provider" onchange="if (this.value == 'db-ip.com' || this.value == 'telize.com') { jQuery('.smio_dbip_apikey').show(); } else { jQuery('.smio_dbip_apikey').hide(); }">
                          <option value="db-ip.com" <?php if (self::$apisetting['geo_provider'] == 'db-ip.com') { ?>selected="selected"<?php } ?>>db-ip.com</option>
                          <option value="telize.com" <?php if (self::$apisetting['geo_provider'] == 'telize.com') { ?>selected="selected"<?php } ?>>telize.com</option>
                          <option value="ip-api.com" <?php if (self::$apisetting['geo_provider'] == 'ip-api.com') { ?>selected="selected"<?php } ?>>ip-api.com [Free]</option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top" class="smio_dbip_apikey" <?php if (self::$apisetting['geo_provider'] != 'db-ip.com' && self::$apisetting['geo_provider'] != 'telize.com') { ?>style="display:none;"<?php } ?>>
                      <td class="first"><label>db-ip.com <?php echo __('API Key', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="db_ip_apikey" type="text" value="<?php echo self::$apisetting['db_ip_apikey']; ?>" class="regular-text" size="50">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Google Maps API Key', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="gmaps_apikey" type="text" value="<?php echo self::$apisetting['gmaps_apikey']; ?>" class="regular-text" size="50">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Fast Bridge', 'smpush-plugin-lang')?></td>
                      <td><label><input name="fast_bridge" type="checkbox" value="1" <?php if (self::$apisetting['fast_bridge'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Smart trick to bypass WordPress huge loading time for plugin requests only.', 'smpush-plugin-lang')?></label></td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Apple API Version', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="apple_api_ver" type="radio" value="http2" <?php if (self::$apisetting['apple_api_ver'] == 'http2') { ?>checked="checked"<?php } ?>> <?php echo __('New Apple API version uses new HTTP/2 protocol [Recommended]', 'smpush-plugin-lang')?></label><br />
                        <label><input name="apple_api_ver" type="radio" value="ssl" <?php if (self::$apisetting['apple_api_ver'] == 'ssl') { ?>checked="checked"<?php } ?>> <?php echo __('Old Apple API version uses SSL connection', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Metabox Option', 'smpush-plugin-lang')?></td>
                      <td><label><input name="metabox_check_status" type="checkbox" value="1" <?php if (self::$apisetting['metabox_check_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Default status for the mute notification checkbox when creating new posts is activated', 'smpush-plugin-lang')?></label></td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Uninstall Plugin', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="uninstall_action" type="radio" value="files" <?php if (self::$apisetting['uninstall_action'] == 'files') { ?>checked="checked"<?php } ?>> <?php echo __('Remove plugin files only.', 'smpush-plugin-lang')?></label><br />
                        <label><input name="uninstall_action" type="radio" value="data" <?php if (self::$apisetting['uninstall_action'] == 'data') { ?>checked="checked"<?php } ?>> <?php echo __('Remove plugin files and all data except plugin settings and configurations.', 'smpush-plugin-lang')?></label><br />
                        <label><input name="uninstall_action" type="radio" value="destroy" <?php if (self::$apisetting['uninstall_action'] == 'destroy') { ?>checked="checked"<?php } ?>> <?php echo __('Completely remove plugin files, options and all data.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-ios smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/apple.png" alt="" /> <span><?php echo __('Apple Connection Settings', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Certification Type', 'smpush-plugin-lang')?></td>
                      <td><label><input name="apple_sandbox" type="checkbox" value="1" <?php if (self::$apisetting['apple_sandbox'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable Apple sandbox server for development certification type', 'smpush-plugin-lang')?></label></td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('App ID', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="apple_appid" type="text" value="<?php echo self::$apisetting['apple_appid']; ?>" class="regular-text">
                        <p class="description"><?php echo __('App ID under App IDs page in Identifiers block.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Certification Type', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <select name="apple_cert_type" onchange="if (this.value == 'pem') { jQuery('.ios_cert_p8_fields').hide();jQuery('.ios_cert_pem_fields').show(); } else { jQuery('.ios_cert_pem_fields').hide();jQuery('.ios_cert_p8_fields').show(); }">
                          <option value="pem" <?php if (self::$apisetting['apple_cert_type'] == 'pem') { ?>selected="selected"<?php } ?>><?php echo __('Old PEM Certification', 'smpush-plugin-lang')?></option>
                          <option value="p8" <?php if (self::$apisetting['apple_cert_type'] == 'p8') { ?>selected="selected"<?php } ?>><?php echo __('New P8 Certification', 'smpush-plugin-lang')?></option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top" class="ios_cert_p8_fields" <?php if (self::$apisetting['apple_cert_type'] != 'p8') { ?>style="display:none;"<?php } ?>>
                      <td class="first"><label><?php echo __('Team ID', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="apple_teamid" type="text" value="<?php echo self::$apisetting['apple_teamid']; ?>" class="regular-text">
                      </td>
                    </tr>
                    <tr valign="top" class="ios_cert_p8_fields" <?php if (self::$apisetting['apple_cert_type'] != 'p8') { ?>style="display:none;"<?php } ?>>
                      <td class="first"><label><?php echo __('Key ID', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="apple_keyid" type="text" value="<?php echo self::$apisetting['apple_keyid']; ?>" class="regular-text">
                      </td>
                    </tr>
                    <tr valign="top" class="ios_cert_p8_fields" <?php if (self::$apisetting['apple_cert_type'] != 'p8') { ?>style="display:none;"<?php } ?>>
                      <td class="first"><label><?php echo __('Certification .P8 File', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="apple_certp8_path" type="text" value="<?php echo self::$apisetting['apple_certp8_path']; ?>" size="60" class="regular-text">
                        <input name="apple_certp8_upload" type="file">
                      </td>
                    </tr>
                    <tr valign="top" class="ios_cert_pem_fields" <?php if (self::$apisetting['apple_cert_type'] != 'pem') { ?>style="display:none;"<?php } ?>>
                      <td class="first"><label><?php echo __('Certification .PEM File', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="apple_cert_path" type="text" value="<?php echo self::$apisetting['apple_cert_path']; ?>" size="60" class="regular-text">
                        <input name="apple_cert_upload" type="file">
                      </td>
                    </tr>
                    <tr valign="top" class="ios_cert_pem_fields" <?php if (self::$apisetting['apple_cert_type'] != 'pem') { ?>style="display:none;"<?php } ?>>
                      <td class="first"><label><?php echo __('Password Phrase', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="apple_passphrase" type="text" value="<?php echo self::$apisetting['apple_passphrase']; ?>" class="regular-text">
                        <p class="description"><?php echo __('Apple password phrase for sending push notification.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Badge', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="ios_badge" type="text" value="<?php echo self::$apisetting['ios_badge']; ?>" class="regular-text" size="40">
                        <p class="description"><?php echo __('The number to display as the badge of the application icon.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Badge Counter', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="ios_msg_counter" type="checkbox" value="1" <?php if (self::$apisetting['ios_msg_counter'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Automatically count number of messages that device receive it', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Launch Image', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="ios_launch" type="text" value="<?php echo self::$apisetting['ios_launch']; ?>" class="regular-text" size="40">
                        <p class="description"><?php echo __('The filename of an image file in the application bundle.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Sound', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="ios_sound" type="text" value="<?php echo self::$apisetting['ios_sound']; ?>" class="regular-text" size="40">
                        <p class="description"><?php echo __('The name of a sound file in the application bundle.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Message Truncate', 'smpush-plugin-lang')?></td>
                      <td><label><input name="stop_summarize" type="checkbox" value="1" <?php if (self::$apisetting['stop_summarize'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Disable truncate iOS push message if exceeds the allowed payload', 'smpush-plugin-lang')?></label></td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Titanium Compatibility', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="ios_titanium_payload" type="checkbox" value="1" <?php if (self::$apisetting['ios_titanium_payload'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __("Make iOS's payload compatible with Titanium platform", 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('One By One', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="ios_onebyone" type="checkbox" value="1" <?php if (self::$apisetting['ios_onebyone'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Send devices to Apple one by one instead of 1000 devices in each connection', 'smpush-plugin-lang')?></label>
                        <p class="description"><?php echo __('Apple closes connection when receive number of invalid devices.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Enable this option when you do not receive the push message on number of devices.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Use new Apple API provider HTTP/2 to avoid this problem.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-android smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/android.png" alt="" /> <span><?php echo __('Android Connection Settings', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                  <?php if(!empty($params['canEditApiKeys'])): ?>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('API Key', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="google_apikey" type="text" value="<?php echo self::$apisetting['google_apikey']; ?>" class="regular-text" size="70">
                        <p class="description"><?php echo __('Google API key for sending Android push notification.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Firebase Compatibility', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="android_fcm_msg" type="checkbox" value="1" <?php if (self::$apisetting['android_fcm_msg'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __("Message structure compatible with FCM.", 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Titanium Compatibility', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="android_titanium_payload" type="checkbox" value="1" <?php if (self::$apisetting['android_titanium_payload'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __("Make Android's payload compatible with Titanium platform", 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                  <?php endif; ?>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Title', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="android_title" type="text" value="<?php echo self::$apisetting['android_title']; ?>" class="regular-text" size="40">
                        <p class="description"><?php echo __('Title of notification appears above the message body.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Icon', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="android_icon" type="text" value="<?php echo self::$apisetting['android_icon']; ?>" class="regular-text" size="40">
                        <p class="description"><?php echo __('Set icon file name to customize the push message icon.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Sound', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="android_sound" type="text" value="<?php echo self::$apisetting['android_sound']; ?>" class="regular-text" size="40">
                        <p class="description"><?php echo __('The sound to play when the device receives the notification.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Counter', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="android_msg_counter" type="checkbox" value="1" <?php if (self::$apisetting['android_msg_counter'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Automatically count number of messages that device receive it', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php if(!empty($params['canEditApiKeys'])): ?>
      <div id="col-left" class="smpush-tabs-corona smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/corona.png" alt="" /> <span><?php echo __('Corona Compatibility', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="android_corona_payload" type="checkbox" value="1" <?php if (self::$apisetting['android_corona_payload'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Make the message structure compatible with Corona platform', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <div id="col-left" class="smpush-tabs-windows smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/wp.png" alt="" /> <span><?php echo __('Windows Phone 8 Settings', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Authenticated', 'smpush-plugin-lang')?></td>
                      <td><label><input name="wp_authed" type="checkbox" value="1" <?php if (self::$apisetting['wp_authed'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Windows Phone 8 authenticated apps have no limit quota for sending daily.', 'smpush-plugin-lang')?></label></td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Certificate File', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="wp_cert" type="file" class="regular-text"><?php if (!empty(self::$apisetting['wp_cert'])): ?> <img title="Uploaded" src="<?php echo smpush_imgpath; ?>/valid.png" alt="" /><?php endif; ?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Private key', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="wp_pem" type="file" class="regular-text"><?php if (!empty(self::$apisetting['wp_pem'])): ?> <img title="Uploaded" src="<?php echo smpush_imgpath; ?>/valid.png" alt="" /><?php endif; ?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('CA Info', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="wp_cainfo" type="file" class="regular-text"><?php if (!empty(self::$apisetting['wp_cainfo'])): ?> <img title="Uploaded" src="<?php echo smpush_imgpath; ?>/valid.png" alt="" /><?php endif; ?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-windows smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/wp.png" alt="" /> <span><?php echo __('Universal Windows 10 Settings', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Package SID', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="wp10_pack_sid" type="text" size="80" value="<?php echo self::$apisetting['wp10_pack_sid']?>" placeholder="e.g. ms-app://S-1-15-2-2972962901-2322836549-3722629029" class="regular-text">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Client Secret', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="wp10_client_secret" type="text" size="60" value="<?php echo self::$apisetting['wp10_client_secret']?>" placeholder="e.g. Vex8L9WOFZuj95euaLrvSH7XyoDhLJc7" class="regular-text">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-blackberry smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/blackberry.png" alt="" /> <span><?php echo __('BlackBerry Connection Settings', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Development Mode', 'smpush-plugin-lang')?></td>
                      <td><label><input name="bb_dev_env" type="checkbox" value="1" <?php if (self::$apisetting['bb_dev_env'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable development mode', 'smpush-plugin-lang')?></label></td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Application ID', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="bb_appid" type="text" value="<?php echo self::$apisetting['bb_appid']; ?>" class="regular-text" size="50">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Password', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="bb_password" type="text" value="<?php echo self::$apisetting['bb_password']; ?>" class="regular-text">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('CPID', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="bb_cpid" type="text" value="<?php echo self::$apisetting['bb_cpid']; ?>" class="regular-text">
                        <p class="description"><?php echo __('Content Provider ID is provided by BlackBerry in the email you received.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-desktop smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/desktop.png" alt="" /> <span><?php echo __('Desktop Notifications', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Title', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="desktop_title" type="text" value="<?php echo self::$apisetting['desktop_title']; ?>" class="regular-text" size="40">
                        <p class="description"><?php echo __('Title of notification appears above the message body.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_gps_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_gps_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable GPS location detector', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_debug" type="checkbox" value="1" <?php if (self::$apisetting['desktop_debug'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable debug mode to track any errors in the browser console', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_logged_only" type="checkbox" value="1" <?php if (self::$apisetting['desktop_logged_only'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable for logged users only', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_admins_only" type="checkbox" value="1" <?php if (self::$apisetting['desktop_admins_only'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable for administrators only', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-popup smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/desktop.png" alt="" /> <span><?php echo __('Popup Box Settings', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Request Type', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="desktop_request_type" class="smpushReuqestTypePicker_DISABLED">
                          <option value="native"><?php echo __('Native Opt-in', 'smpush-plugin-lang')?></option>
                          <option value="subs_page"><?php echo __('Subscription Page', 'smpush-plugin-lang')?></option>
                          <option value="popup" <?php if (self::$apisetting['desktop_request_type'] == 'popup'):?>selected="selected"<?php endif;?>><?php echo __('Popup Box', 'smpush-plugin-lang')?></option>
                          <option value="icon" <?php if (self::$apisetting['desktop_request_type'] == 'icon'):?>selected="selected"<?php endif;?>><?php echo __('Icon', 'smpush-plugin-lang')?></option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top">
                      <th class="first"><?php echo __('Customize Popup', 'smpush-plugin-lang')?></th>
                    </tr>
                    <tr valign="top" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Layout', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="desktop_popup_layout">
                          <option value="modern"><?php echo __('Modern', 'smpush-plugin-lang')?></option>
                          <option value="native" <?php if (self::$apisetting['desktop_popup_layout'] == 'native'):?>selected="selected"<?php endif;?>><?php echo __('Like Native', 'smpush-plugin-lang')?></option>
                          <option value="flat" <?php if (self::$apisetting['desktop_popup_layout'] == 'flat'):?>selected="selected"<?php endif;?>><?php echo __('Flat Design', 'smpush-plugin-lang')?></option>
                          <option value="fancy" <?php if (self::$apisetting['desktop_popup_layout'] == 'fancy'):?>selected="selected"<?php endif;?>><?php echo __('Fancy Layout', 'smpush-plugin-lang')?></option>
                          <option value="dark" <?php if (self::$apisetting['desktop_popup_layout'] == 'dark'):?>selected="selected"<?php endif;?>><?php echo __('Dark', 'smpush-plugin-lang')?></option>
                          <option value="ocean" <?php if (self::$apisetting['desktop_popup_layout'] == 'ocean'):?>selected="selected"<?php endif;?>><?php echo __('Light Ocean', 'smpush-plugin-lang')?></option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Position', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="desktop_popup_position">
                          <option value="center"><?php echo __('Center of screen', 'smpush-plugin-lang')?></option>
                          <option value="topcenter" <?php if (self::$apisetting['desktop_popup_position'] == 'topcenter'):?>selected="selected"<?php endif;?>><?php echo __('Top center', 'smpush-plugin-lang')?></option>
                          <option value="topright" <?php if (self::$apisetting['desktop_popup_position'] == 'topright'):?>selected="selected"<?php endif;?>><?php echo __('Top right', 'smpush-plugin-lang')?></option>
                          <option value="topleft" <?php if (self::$apisetting['desktop_popup_position'] == 'topleft'):?>selected="selected"<?php endif;?>><?php echo __('Top left', 'smpush-plugin-lang')?></option>
                          <option value="bottomright" <?php if (self::$apisetting['desktop_popup_position'] == 'bottomright'):?>selected="selected"<?php endif;?>><?php echo __('Bottom right', 'smpush-plugin-lang')?></option>
                          <option value="bottomleft" <?php if (self::$apisetting['desktop_popup_position'] == 'bottomleft'):?>selected="selected"<?php endif;?>><?php echo __('Bottom left', 'smpush-plugin-lang')?></option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Modal Head Title', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="desktop_modal_title" value="<?php echo self::$apisetting['desktop_modal_title']; ?>" class="regular-text" size="40" />
                      </td>
                    </tr>
                    <tr valign="middle" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Modal Message', 'smpush-plugin-lang')?></td>
                      <td>
                        <textarea name="desktop_modal_message" rows="8" cols="70" class="regular-text"><?php echo self::$apisetting['desktop_modal_message']; ?></textarea>
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Subscribe Button Text', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="desktop_btn_subs_text" value="<?php echo self::$apisetting['desktop_btn_subs_text']; ?>" class="regular-text" size="40" />
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Unsubscribe Button Text', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="desktop_btn_unsubs_text" value="<?php echo self::$apisetting['desktop_btn_unsubs_text']; ?>" class="regular-text" size="40" />
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Ignore Button Text', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="desktop_modal_cancel_text" value="<?php echo self::$apisetting['desktop_modal_cancel_text']; ?>" class="regular-text" size="20" />
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-popup-settings" <?php if(self::$apisetting['desktop_request_type'] != 'popup'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Saved Button Text', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="desktop_modal_saved_text" value="<?php echo self::$apisetting['desktop_modal_saved_text']; ?>" class="regular-text" size="20" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Logo Icon', 'smpush-plugin-lang')?></td>
                      <td>
                        <input class="smpush_upload_field_popupicon" type="url" size="50" name="desktop_popupicon" value="<?php echo self::$apisetting['desktop_popupicon']; ?>" />
                        <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_popupicon" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                        <p class="description"><?php echo __('Set a website logo to appear in the pop-up body.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <th class="first"><?php echo __('Customize Icon', 'smpush-plugin-lang')?></th>
                    </tr>
                    <tr valign="top" class="smpush-icon-settings" <?php if(self::$apisetting['desktop_request_type'] != 'icon'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Position', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="desktop_icon_position">
                          <option value="topright" <?php if (self::$apisetting['desktop_icon_position'] == 'topright'):?>selected="selected"<?php endif;?>><?php echo __('Top right', 'smpush-plugin-lang')?></option>
                          <option value="topleft" <?php if (self::$apisetting['desktop_icon_position'] == 'topleft'):?>selected="selected"<?php endif;?>><?php echo __('Top left', 'smpush-plugin-lang')?></option>
                          <option value="bottomright" <?php if (self::$apisetting['desktop_icon_position'] == 'bottomright'):?>selected="selected"<?php endif;?>><?php echo __('Bottom right', 'smpush-plugin-lang')?></option>
                          <option value="bottomleft" <?php if (self::$apisetting['desktop_icon_position'] == 'bottomleft'):?>selected="selected"<?php endif;?>><?php echo __('Bottom left', 'smpush-plugin-lang')?></option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-icon-settings" <?php if(self::$apisetting['desktop_request_type'] != 'icon'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Subscribe Message', 'smpush-plugin-lang')?></td>
                      <td>
                        <textarea name="desktop_icon_message" rows="8" cols="70" class="regular-text"><?php echo self::$apisetting['desktop_icon_message']; ?></textarea>
                      </td>
                    </tr>
                    <tr valign="top" class="smpush-icon-settings" <?php if(self::$apisetting['desktop_request_type'] != 'icon'):?>style="display:"<?php endif;?>>
                      <td class="first"><?php echo __('Unsubscribe Message', 'smpush-plugin-lang')?></td>
                      <td>
                        <textarea name="desktop_icon_unsubs_text" rows="8" cols="70" class="regular-text"><?php echo self::$apisetting['desktop_icon_unsubs_text']; ?></textarea>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Icon Image', 'smpush-plugin-lang')?></td>
                      <td>
                        <input class="smpush_upload_field_iconimage" type="url" size="50" name="desktop_iconimage" value="<?php echo self::$apisetting['desktop_iconimage']; ?>" />
                        <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_iconimage" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                        <p class="description"><?php echo __('Set a custom icon image instead of the default bell image. Recommended size 38x38 px', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <th class="first"><?php echo __('Additional Customization', 'smpush-plugin-lang')?></th>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Custom CSS', 'smpush-plugin-lang')?></td>
                      <td>
                        <textarea name="desktop_popup_css" rows="8" cols="70" class="regular-text"><?php echo self::$apisetting['desktop_popup_css']; ?></textarea>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Black Overlay', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="black_overlay" type="checkbox" value="1" <?php if (self::$apisetting['black_overlay'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Display black overlay when the popup appears.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Delay Time', 'smpush-plugin-lang')?></td>
                      <td>
                        <input name="desktop_delay" type="number" value="<?php echo self::$apisetting['desktop_delay']; ?>" class="regular-text" style="width:70px"> <?php echo __('Number of seconds to delay appearing the request permissions for visitors.', 'smpush-plugin-lang')?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Request Again', 'smpush-plugin-lang')?></td>
                      <td>
                        <input name="desktop_reqagain" type="number" value="<?php echo self::$apisetting['desktop_reqagain']; ?>" class="regular-text" style="width:70px"> <?php echo __('Number of days to request the permissions again from users when click on ignore button.', 'smpush-plugin-lang')?>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td class="first"><?php echo __('Not Supported Message', 'smpush-plugin-lang')?></td>
                      <td>
                        <textarea name="desktop_notsupport_msg" rows="8" cols="70" class="regular-text"><?php echo self::$apisetting['desktop_notsupport_msg']; ?></textarea>
                        <p class="description"><?php echo __('Show a message if user visits website with browser does not support web push notification.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Show In', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="desktop_run_places[]" multiple="multiple" size="7" style="width:250px">
                          <option value="noplace" <?php if (in_array('noplace', self::$apisetting['desktop_run_places'])):?>selected="selected"<?php endif;?>><?php echo __('No Place', 'smpush-plugin-lang')?></option>
                          <option value="all" <?php if (in_array('all', self::$apisetting['desktop_run_places'])):?>selected="selected"<?php endif;?>><?php echo __('All Places', 'smpush-plugin-lang')?></option>
                          <option value="homepage" <?php if (in_array('homepage', self::$apisetting['desktop_run_places'])):?>selected="selected"<?php endif;?>><?php echo __('Homepage', 'smpush-plugin-lang')?></option>
                          <option value="post" <?php if (in_array('post', self::$apisetting['desktop_run_places'])):?>selected="selected"<?php endif;?>><?php echo __('Post', 'smpush-plugin-lang')?></option>
                          <option value="page" <?php if (in_array('page', self::$apisetting['desktop_run_places'])):?>selected="selected"<?php endif;?>><?php echo __('Page', 'smpush-plugin-lang')?></option>
                          <option value="category" <?php if (in_array('category', self::$apisetting['desktop_run_places'])):?>selected="selected"<?php endif;?>><?php echo __('Category', 'smpush-plugin-lang')?></option>
                          <option value="taxonomy" <?php if (in_array('taxonomy', self::$apisetting['desktop_run_places'])):?>selected="selected"<?php endif;?>><?php echo __('Taxonomy', 'smpush-plugin-lang')?></option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Show In Specific Pages', 'smpush-plugin-lang')?></td>
                      <td>
                        <input name="desktop_showin_pageids" type="text" value="<?php echo self::$apisetting['desktop_showin_pageids']; ?>" placeholder="44,1,32,48,3,56,8,43,1713" size="50" class="regular-text">
                        <p class="description"><?php echo __('Put each page ID separated by (,) to request push permissions these pages only.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-popup smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/desktop.png" alt="" /> <span><?php echo __('Pay To Read', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Status', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="desktop_paytoread" onchange="smpushPayToSelector(this.value)">
                          <option value="0"><?php echo __('Disabled', 'smpush-plugin-lang')?></option>
                          <option value="1" <?php if (self::$apisetting['desktop_paytoread'] == 1):?>selected="selected"<?php endif;?>><?php echo __('Popup without dismiss option', 'smpush-plugin-lang')?></option>
                          <option value="2" <?php if (self::$apisetting['desktop_paytoread'] == 2):?>selected="selected"<?php endif;?>><?php echo __('Truncate post contents', 'smpush-plugin-lang')?></option>
                        </select>
                        <p class="description"><?php echo __('Force the visitor to subscribe to continue browsing your content.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="middle" class="paytoreadOptions1" <?php if (self::$apisetting['desktop_paytoread'] != 1):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('Message', 'smpush-plugin-lang')?></td>
                      <td>
                        <textarea name="desktop_paytoread_message" rows="8" cols="70" class="regular-text"><?php echo self::$apisetting['desktop_paytoread_message']; ?></textarea>
                        <p class="description"><?php echo __('Show a message if pay to read option is enabled and visitor blocked the push permissions.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top" class="paytoreadOptions1" <?php if (self::$apisetting['desktop_paytoread'] != 1):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('Darkness Grade', 'smpush-plugin-lang')?></td>
                      <td>
                        <input name="desktop_paytoread_darkness" type="number" min="1" max="10" value="<?php echo self::$apisetting['desktop_paytoread_darkness']; ?>" class="regular-text" style="width:70px"> <?php echo __('Pay To Read darkness grade from 1 to 10.', 'smpush-plugin-lang')?>
                      </td>
                    </tr>
                    <tr valign="top" class="paytoreadOptions2" <?php if (self::$apisetting['desktop_paytoread'] != 2):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('Text Size', 'smpush-plugin-lang')?></td>
                      <td>
                        <input name="desktop_paytoread_textsize" type="number" step="50" value="<?php echo self::$apisetting['desktop_paytoread_textsize']; ?>" class="regular-text" style="width:70px"> <?php echo __('Number of allowed characters for post contents before cutting.', 'smpush-plugin-lang')?>
                      </td>
                    </tr>
                    <tr valign="top" class="paytoreadOptions2" <?php if (self::$apisetting['desktop_paytoread'] != 2):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('Subscribe Message', 'smpush-plugin-lang')?></td>
                      <td>
                        <input name="desktop_paytoread_substext" type="text" value="<?php echo self::$apisetting['desktop_paytoread_substext']; ?>" class="regular-text" size="80">
                        <p class="description"><?php echo __('Write a message for user to know about why can not see the complete post contents.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-chrome smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/chrome.png" alt="" /> <span>Chrome</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_chrome_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_chrome_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification listener for Chrome browser', 'smpush-plugin-lang')?></label>
                        <p class="description">
                          <?php echo __('Chrome push notification requires your site working under <code>HTTPS</code> protocol .', 'smpush-plugin-lang')?>
                          <a href="//namecheap.pxf.io/c/477005/386535/5618" target="_blank"><?php echo __('Buy one for $9 only', 'smpush-plugin-lang')?></a>
                        </p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Enable Web Push', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="desktop_webpush" type="checkbox" value="1" <?php if(!empty($params['envErrors'])):?>disabled<?php endif; ?> <?php if (self::$apisetting['desktop_webpush'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Send web push messages with payload will save server resources because it does not make API requests to fetch the messages from your server.', 'smpush-plugin-lang')?></label>
                        <p class="description"><?php echo __('Recommended for VPS or dedicated servers not shared hosting', 'smpush-plugin-lang')?></p>
                        <?php if(!empty($params['envErrors'])):?>
                          <p class="description"><?php echo __('Your server is not ready yet to send web push. List of requirements:', 'smpush-plugin-lang')?></p>
                          <?php foreach($params['envErrors'] as $envError): ?>
                            <p class="description"><?php echo $envError; ?></p>
                          <?php endforeach; endif; ?>
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_show">
                      <td class="first"><?php echo __('VAPID Public Key', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="chrome_vapid_public" value="<?php echo self::$apisetting['chrome_vapid_public']; ?>" class="regular-text" size="80" />
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_show">
                      <td class="first"><?php echo __('VAPID Private Key', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="chrome_vapid_private" value="<?php echo self::$apisetting['chrome_vapid_private']; ?>" class="regular-text" size="50" />
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_show">
                      <td class="first"><?php echo __('Desktop Push Compatibility', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="desktop_webpush_old" type="checkbox" value="1" <?php if (self::$apisetting['desktop_webpush_old'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable this option if you already have web push subscribers before plugin\'s version 8.0', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_show">
                      <td class="first"><?php echo __('OneSignal Compliant', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="webpush_onesignal_payload" type="checkbox" value="1" <?php if (self::$apisetting['webpush_onesignal_payload'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable this option if you import some subscribers from OneSignal.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_hide">
                      <td class="first"><?php echo __('Offline Messages', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="desktop_offline" type="checkbox" value="1" <?php if (self::$apisetting['desktop_offline'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Show offline messages when user opens his browser again', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_hide">
                      <td class="first">Firebase <?php echo __('API Key', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="chrome_apikey" value="<?php echo self::$apisetting['chrome_apikey']; ?>" class="regular-text" size="50" />
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_hide">
                      <td class="first">Firebase <?php echo __('Sender ID', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="number" name="chrome_projectid" value="<?php echo self::$apisetting['chrome_projectid']; ?>" class="regular-text" placeholder="<?php echo __('e.g. 590173865545', 'smpush-plugin-lang')?>" size="30" />
                        <p class="description"><?php echo __('For how to get API key and project number', 'smpush-plugin-lang')?> <a href="https://smartiolabs.com/blog/61/get-api-key-sender-id-fcm-push-notification-firebase/" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('No Disturb', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input name="no_disturb" type="checkbox" value="1" <?php if (self::$apisetting['no_disturb'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Always show last notification only and cancel previous one', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_hide">
                      <td class="first"><?php echo __('Default Icon', 'smpush-plugin-lang')?></td>
                      <td>
                        <input class="smpush_upload_field_deskicon" type="url" size="50" name="desktop_deficon" value="<?php echo self::$apisetting['desktop_deficon']; ?>" />
                        <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_deskicon" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                        <p class="description"><?php echo __('Choose an icon in a standard size 192x192 px', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top" class="desktop_webpush_hide">
                      <td class="first"><?php echo __('Additional Manifest', 'smpush-plugin-lang')?></td>
                      <td>
                        <textarea name="chrome_manifest" rows="8" cols="70" placeholder='e.g. {"start_url":"/index.html","display":"standalone"}' class="regular-text"><?php echo self::$apisetting['chrome_manifest']; ?></textarea>
                        <p class="description"><?php echo __('Plugin cancels other Manifest files so add here any Manifest configurations in JSON format you want to load.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-firefox smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/firefox.png" alt="" /> <span>Firefox</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_firefox_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_firefox_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification listener for Firefox browser', 'smpush-plugin-lang')?></label>
                        <p class="description">
                          <?php echo __('Firefox push notification requires your site working under <code>HTTPS</code> protocol .', 'smpush-plugin-lang')?>
                          <a href="//namecheap.pxf.io/c/477005/386535/5618" target="_blank"><?php echo __('Buy one for $9 only', 'smpush-plugin-lang')?></a>
                        </p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-opera smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/opera.png" alt="" /> <span>Opera</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_opera_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_opera_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification listener for Opera browser', 'smpush-plugin-lang')?></label>
                        <p class="description">
                          <?php echo __('Opera push notification depends on Chrome configurations to work .', 'smpush-plugin-lang')?>
                        </p>
                        <?php echo __('Opera push notification requires your site working under <code>HTTPS</code> protocol .', 'smpush-plugin-lang')?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-edge smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/edge.png" alt="" /> <span>Edge</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_edge_status" type="checkbox" <?php if (self::$apisetting['desktop_webpush'] == 0): ?>disabled="disabled"<?php endif; ?> value="1" <?php if (self::$apisetting['desktop_edge_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification listener for Edge browser', 'smpush-plugin-lang')?></label>
                        <p class="description">
                          <?php echo __('Edge push notification depends on Chrome configurations to work .', 'smpush-plugin-lang')?>
                        </p>
                        <?php echo __('Edge push notification requires your site working under <code>HTTPS</code> protocol .', 'smpush-plugin-lang')?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-samsung smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/samsung.png" alt="" /> <span>Samsung Browser</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_samsung_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_samsung_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification listener for Samsung Browser', 'smpush-plugin-lang')?></label>
                        <p class="description">
                          <?php echo __('Samsung Browser push notification depends on Chrome configurations to work .', 'smpush-plugin-lang')?>
                        </p>
                        <?php echo __('Samsung Browser push notification requires your site working under <code>HTTPS</code> protocol .', 'smpush-plugin-lang')?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-amp smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/amp.png" alt="" /> <span>AMP</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Enable PWA Support', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="pwa_support" type="checkbox" value="1" <?php if (self::$apisetting['pwa_support'] == 1) { ?>checked="checked"<?php } ?>>
                          <?php echo __('Enable PWA support for', 'smpush-plugin-lang')?> <a href="https://wordpress.org/plugins/super-progressive-web-apps/" target="_blank">SuperPWA</a> <?php echo __('plugin', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <input name="pwa_kaludi_support" type="hidden" value="0">
                    <!--<tr valign="top">
                      <td class="first"><label><?php echo __('Enable PWA Support', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="pwa_kaludi_support" type="checkbox" value="1" <?php if (self::$apisetting['pwa_kaludi_support'] == 1) { ?>checked="checked"<?php } ?>>
                        <?php echo __('Enable PWA support for', 'smpush-plugin-lang')?> <a href="https://wordpress.org/plugins/pwa-for-wp/" target="_blank">PWA for WP</a> <?php echo __('plugin', 'smpush-plugin-lang')?>
                      </td>
                    </tr>-->
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Enable AMP Support', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="amp_support" type="checkbox" value="1" <?php if (self::$apisetting['amp_support'] == 1) { ?>checked="checked"<?php } ?> <?php if (self::$apisetting['desktop_webpush'] == 0) { ?>disabled="disabled"<?php } ?>>
                        <?php if (self::$apisetting['desktop_webpush'] == 0): ?>
                          <code><?php echo __('AMP supports VAPID web push only.', 'smpush-plugin-lang')?></code>
                        <?php endif; ?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('AMP Post Widget', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="amp_post_widget" type="checkbox" value="1" <?php if (self::$apisetting['amp_post_widget'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable showing AMP opt-in buttons under post content.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('AMP Page Widget', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="amp_page_widget" type="checkbox" value="1" <?php if (self::$apisetting['amp_page_widget'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable showing AMP opt-in buttons under page content.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('AMP Post Shortcode', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="amp_post_shortcode" type="checkbox" value="1" <?php if (self::$apisetting['amp_post_shortcode'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable showing AMP opt-in shortcode buttons for posts.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('AMP Page Shortcode', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input name="amp_page_shortcode" type="checkbox" value="1" <?php if (self::$apisetting['amp_page_shortcode'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable showing AMP opt-in shortcode buttons for pages.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('AMP Buttons Shortcode', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <label><input type="text" size="80" value="[smart_amp_optin subscribe='Subscribe to Notifications' unsubscribe='Opt-out from Notifications']" class="regular-text" onfocus="jQuery(this).select()" readonly>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-safari smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/safari.png" alt="" /> <span>Safari</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_safari_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_safari_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification listener for Safari browser', 'smpush-plugin-lang')?></label>
                        <p class="description">
                          <?php echo __('Safari push notification requires your site working under <code>HTTPS</code> protocol .', 'smpush-plugin-lang')?>
                          <a href="//namecheap.pxf.io/c/477005/386535/5618" target="_blank"><?php echo __('Buy one for $9 only', 'smpush-plugin-lang')?></a>
                        </p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Certification .PEM File', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" size="50" name="safari_cert_path" value="<?php echo self::$apisetting['safari_cert_path']; ?>" />
                        <input type="file" name="safari_cert_upload" />
                        <p class="description"><?php echo __('Follow this video for how to create Safari certificates', 'smpush-plugin-lang')?> <a href="https://www.youtube.com/watch?v=FKOEkxlM7TA" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Certification .P12 File', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" size="50" name="safari_certp12_path" value="<?php echo self::$apisetting['safari_certp12_path']; ?>" />
                        <input type="file" name="safari_certp12_upload" />
                        <p class="description"><?php echo __('Do not have Apple developer account?', 'smpush-plugin-lang').' '.__('We provide a paid service to generate your certificates for $10 only', 'smpush-plugin-lang')?> <a href="https://smartiolabs.com/support" target="_blank"><?php echo __('request now', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Password Phrase', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <input name="safari_passphrase" type="text" value="<?php echo self::$apisetting['safari_passphrase']; ?>" class="regular-text">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Website Push ID', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" size="30" name="safari_web_id" placeholder="e.g. web.com.example.domain" value="<?php echo self::$apisetting['safari_web_id']; ?>" />
                        <p class="description"><?php echo __('The Website Push ID, as specified in your registration with the Member Center.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Push Icon', 'smpush-plugin-lang')?></td>
                      <td>
                        <input class="smpush_upload_field_safariicon" type="url" size="50" name="safari_icon" value="<?php echo self::$apisetting['safari_icon']; ?>" />
                        <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_safariicon" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                        <p class="description"><?php echo __('Choose an icon in a standard size 256x256 px', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>

                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-messenger smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/messenger.png" alt="" /> <span>Messenger</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Webhook Callback URL', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="<?php echo get_bloginfo('wpurl').'/'.self::$apisetting['push_basename']; ?>/facebook/?action=callback" class="regular-text" size="80" onclick="jQuery(this).select()" readonly />
                        <p class="description"><?php echo __('For how to setup Facebook Messenger', 'smpush-plugin-lang')?> <a href="https://smartiolabs.com/blog/91/how-to-setup-facebook-messenger/" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Webhook Verify Token', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="<?php echo self::$apisetting['msn_verify']?>" class="regular-text" size="30" onclick="jQuery(this).select()" readonly />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Login URL', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="<?php echo get_bloginfo('wpurl').'/'.self::$apisetting['push_basename']; ?>/facebook/?action=login" class="regular-text" size="80" onclick="jQuery(this).select()" readonly />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('App ID', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_appid" value="<?php echo self::$apisetting['msn_appid']; ?>" class="regular-text" size="30" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('App Secret', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_secret" value="<?php echo self::$apisetting['msn_secret']; ?>" class="regular-text" size="50" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Page Access Token', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_accesstoken" value="<?php echo self::$apisetting['msn_accesstoken']; ?>" class="regular-text" size="80" />
                        <?php if(!empty(self::$apisetting['msn_subscribe_error'])):?><?php echo __('Error while connecting with Facebook try save changes again or check the access token.', 'smpush-plugin-lang')?><?php endif;?>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Subscribe Command', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_subs_command" placeholder="e.g. subscribe me" value="<?php echo self::$apisetting['msn_subs_command']; ?>" class="regular-text" size="30" />
                        <p class="description"><?php echo __('Set a word or statement when a user sends it in Messenger plugin will process his subscription.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Leave it empty if you want plugin process any user subscription automatically if he sends any message.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Unsubscribe Command', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_unsubs_command" placeholder="e.g. unsubscribe and delete my account" value="<?php echo self::$apisetting['msn_unsubs_command']; ?>" class="regular-text" size="30" />
                        <p class="description"><?php echo __('Set a word or statement when a user sends it in Messenger plugin will terminate his subscription.', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Language', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="msn_lang" class="regular-text">
                        <?php $fb_langs = smpush_localization::facebook_langs(); foreach($fb_langs as $locale => $lang):?>
                          <option value="<?php echo $locale?>" <?php if($locale == self::$apisetting['msn_lang']):?>selected="selected"<?php endif;?>><?php echo $lang?></option>
                        <?php endforeach;?>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top"><td colspan="2"><strong><?php echo __('WooCommerce Widget', 'smpush-plugin-lang')?></strong></td></tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Checkout Page', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="msn_woo_checkout" <?php if(self::$apisetting['msn_woo_checkout'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Show widget in the checkout page to request from user subscribe to receive his order status updates', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Cart Button', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="msn_woo_cartbtn" <?php if(self::$apisetting['msn_woo_cartbtn'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Show send us button after add to cart button in the product page', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top"><td colspan="2"><strong><?php echo __('Messenger Widget', 'smpush-plugin-lang')?></strong></td></tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Status', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="msn_widget_status" <?php if(self::$apisetting['msn_widget_status'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Enable Messenger widget to allow for plugin collect your subscribers when messaging you', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Facebook Page Link', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="url" name="msn_fbpage_link" value="<?php echo self::$apisetting['msn_fbpage_link']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Widget Title', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_widget_title" value="<?php echo self::$apisetting['msn_widget_title']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top"><td colspan="2"><strong><?php echo __('Official Messenger Chat Plugin', 'smpush-plugin-lang')?></strong></td></tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Status', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="msn_official_widget_status" <?php if(self::$apisetting['msn_official_widget_status'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Enable Messenger chat plugin to allow for plugin collect your subscribers and customers chating with you from website.', 'smpush-plugin-lang')?></label>
                        <p class="description"><?php echo __('A. Facebook requires your website served over HTTPS to activate this plugin.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('B. Whitelist your domain:', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('1. Click Settings at the top of your Page.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('2. Click Messenger Platform on the left.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('3. Edit whitelisted domains for your page in the Whitelisted Domains section.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('For further info', 'smpush-plugin-lang')?> <a href="https://developers.facebook.com/docs/messenger-platform/reference/messenger-profile-api/domain-whitelisting" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Facebook Page ID', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="number" name="msn_official_fbpage_id" value="<?php echo self::$apisetting['msn_official_fbpage_id']; ?>" class="regular-text" size="80" />
                        <p class="description"><?php echo __('Find your Facebook ID', 'smpush-plugin-lang')?> <a href="https://findmyfbid.com" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top"><td colspan="2"><strong><?php echo __('Messenger Button', 'smpush-plugin-lang')?></strong></td></tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Shortcode', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="[smart_push_messenger]" onclick="jQuery(this).select()" readonly class="regular-text" size="40" /><br />
                        <input type="text" value="[smart_push_messenger width=&quot;160&quot; height=&quot;40&quot; text=&quot;Send us message&quot; bgcolor=&quot;#0084ff&quot; color=&quot;#fff&quot;]" onclick="jQuery(this).select()" readonly class="regular-text" size="100" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Facebook Page Link', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="url" name="msn_btn_fblink" value="<?php echo self::$apisetting['msn_btn_fblink']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Button Text', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_btn_text" value="<?php echo self::$apisetting['msn_btn_text']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Width', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="number" name="msn_btn_width" value="<?php echo self::$apisetting['msn_btn_width']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Height', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="number" name="msn_btn_height" value="<?php echo self::$apisetting['msn_btn_height']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Text Color', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_btn_color" value="<?php echo self::$apisetting['msn_btn_color']; ?>" class="smpush_color_picker" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Background Color', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="msn_btn_bgcolor" value="<?php echo self::$apisetting['msn_btn_bgcolor']; ?>" class="smpush_color_picker" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Icon', 'smpush-plugin-lang')?></td>
                      <td>
                        <input class="smpush_upload_field_msnbtnicon" type="url" size="60" name="msn_btn_icon" value="<?php echo self::$apisetting['msn_btn_icon']?>" />
                        <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_msnbtnicon" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-facebook smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/facebook.png" alt="" /> <span>Facebook Notifications</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Canvas URL', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="<?php echo get_bloginfo('wpurl').'/'.self::$apisetting['push_basename']; ?>/facebook/?action=canvas" class="regular-text" size="80" onclick="jQuery(this).select()" readonly />
                        <p class="description"><?php echo __('For how to setup Facebook Notifications', 'smpush-plugin-lang')?> <a href="https://smartiolabs.com/blog/93/how-to-setup-facebook-notifications-application/" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Login URL', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="<?php echo get_bloginfo('wpurl').'/'.self::$apisetting['push_basename']; ?>/facebook/?action=login" class="regular-text" size="80" onclick="jQuery(this).select()" readonly />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('App ID', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="fbnotify_appid" value="<?php echo self::$apisetting['fbnotify_appid']; ?>" class="regular-text" size="30" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('App Secret', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="fbnotify_secret" value="<?php echo self::$apisetting['fbnotify_secret']; ?>" class="regular-text" size="50" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Web Game/App Link', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="fbnotify_applink" value="<?php echo self::$apisetting['fbnotify_applink']; ?>" class="regular-text" size="80" />
                        <p class="description"><?php echo __('Enter your Facebook game or app link to open it after authenticating', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Open Method', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="fbnotify_method" class="regular-text" onchange="if(this.value == 'iframe')jQuery('.FBnotifyFrameSize').show(); else jQuery('.FBnotifyFrameSize').hide();">
                          <option value="iframe"><?php echo __('In Iframe', 'smpush-plugin-lang')?></option>
                          <option value="redirect" <?php if(self::$apisetting['fbnotify_method'] == 'redirect'):?>selected="selected"<?php endif;?>><?php echo __('Redirect visitor to your web link', 'smpush-plugin-lang')?></option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top" class="FBnotifyFrameSize" <?php if(self::$apisetting['fbnotify_method'] == 'redirect'):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('Frame Size', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="fbnotify_width" value="<?php echo self::$apisetting['fbnotify_width']; ?>" class="regular-text" size="10" placeholder="<?php echo __('Width', 'smpush-plugin-lang')?>" />
                        <input type="text" name="fbnotify_height" value="<?php echo self::$apisetting['fbnotify_height']; ?>" class="regular-text" size="10" placeholder="<?php echo __('Height', 'smpush-plugin-lang')?>" />
                      </td>
                    </tr>
                    <tr valign="top"><td colspan="2"><strong><?php echo __('Login Button', 'smpush-plugin-lang')?></strong></td></tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Shortcode', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="[smart_push_fbloign]" onclick="jQuery(this).select()" readonly class="regular-text" size="40" /><br />
                        <input type="text" value="[smart_push_fbloign width=&quot;205&quot; height=&quot;40&quot; text=&quot;Login With Facebook&quot; bgcolor=&quot;#0084ff&quot; color=&quot;#fff&quot; redirect=&quot;&quot;]" onclick="jQuery(this).select()" readonly class="regular-text" size="100" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Actions', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="fblogin_regin_newsletter" <?php if(self::$apisetting['fblogin_regin_newsletter'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Use Facebook email to register user as Newsletter subscriber', 'smpush-plugin-lang')?></label><br>
                        <label><input type="checkbox" value="1" name="fblogin_regin_fbnotifs" <?php if(self::$apisetting['fblogin_regin_fbnotifs'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Use Facebook ID to register user as Facebook Notification subscriber', 'smpush-plugin-lang')?></label><br>
                        <label><input type="checkbox" value="1" name="fblogin_regin_wpuser" <?php if(self::$apisetting['fblogin_regin_wpuser'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Use Facebook info to register user as WordPress user', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Redirect To', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <code><?php echo get_bloginfo('wpurl') ; ?>/</code><input name="fblogin_btn_redirect" type="text" value="<?php echo self::$apisetting['fblogin_btn_redirect']; ?>" class="regular-text">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Button Text', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="fblogin_btn_text" value="<?php echo self::$apisetting['fblogin_btn_text']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Width', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="number" name="fblogin_btn_width" value="<?php echo self::$apisetting['fblogin_btn_width']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Height', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="number" name="fblogin_btn_height" value="<?php echo self::$apisetting['fblogin_btn_height']; ?>" class="regular-text" size="60" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Text Color', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="fblogin_btn_color" value="<?php echo self::$apisetting['fblogin_btn_color']; ?>" class="smpush_color_picker" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Background Color', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="fblogin_btn_bgcolor" value="<?php echo self::$apisetting['fblogin_btn_bgcolor']; ?>" class="smpush_color_picker" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Icon', 'smpush-plugin-lang')?></td>
                      <td>
                        <input class="smpush_upload_field_fbloginicon" type="url" size="60" name="fblogin_btn_icon" value="<?php echo self::$apisetting['fblogin_btn_icon']?>" />
                        <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_fbloginicon" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-email smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/email.png" alt="" /> <span>Newsletter</span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first">SMTP</td>
                      <td>
                        <label><input type="checkbox" value="1" name="smtp_status" <?php if(self::$apisetting['smtp_status'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Enable SMTP mail method to reach inbox easily', 'smpush-plugin-lang')?></label>
                        <p class="description"><?php echo __('Ask your host provider about your SMTP configurations', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('SMTP Host', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="smtp_host" value="<?php echo self::$apisetting['smtp_host']; ?>" class="regular-text" size="40" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><label><?php echo __('Secure', 'smpush-plugin-lang')?></label></td>
                      <td>
                        <select name="smtp_secure" class="postform">
                            <option value=""><?php echo __('None', 'smpush-plugin-lang')?></option>
                            <option value="ssl" <?php if (self::$apisetting['smtp_secure'] == 'ssl') { ?>selected="selected"<?php } ?>>SSL</option>
                            <option value="tls" <?php if (self::$apisetting['smtp_secure'] == 'tls') { ?>selected="selected"<?php } ?>>TLS</option>
                        </select>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Port', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="smtp_port" value="<?php echo self::$apisetting['smtp_port']; ?>" class="regular-text" size="10" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Username', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="smtp_username" value="<?php echo self::$apisetting['smtp_username']; ?>" class="regular-text" size="30" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Password', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="password" name="smtp_password" value="<?php echo self::$apisetting['smtp_password']; ?>" class="regular-text" size="30" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-welcmsg smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/welcome.png" alt="" /> <span><?php echo __('Desktop Welcome Message', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_welc_redir" type="checkbox" value="1" <?php if (self::$apisetting['desktop_welc_redir'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable redirect user to link after successful subscription', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Redirected Link', 'smpush-plugin-lang')?></td>
                      <td>
                        <input name="desktop_welc_redir_link" class="regular-text" type="url" size="50" value="<?php echo self::$apisetting['desktop_welc_redir_link']?>" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="desktop_welc_status" type="checkbox" value="1" <?php if (self::$apisetting['desktop_welc_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable desktop push notification welcome message', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="middle">
                      <td class="first"><?php echo __('Title', 'smpush-plugin-lang')?></td>
                      <td>
                         <input name="desktop_welc_title" class="regular-text" type="text" size="50" value="<?php echo self::$apisetting['desktop_welc_title']?>" onkeyup="jQuery('.smpush-sample_notification_title').html(this.value)" />
                      </td>
                      <td rowspan="3">
                        <div class="smpush-sample_notification">
                          <img src="" class="smpush-sample_notification_logo">
                          <button type="button" class="smpush-close" aria-label="Close"><span aria-hidden="true">×</span></button>
                          <div class="smpush-sample_notification_title"><?php echo __('Notification Title', 'smpush-plugin-lang')?></div>
                          <div class="smpush-sample_notification_message"><?php echo __('This is the Notification Message', 'smpush-plugin-lang')?></div>
                          <div class="smpush-sample_notification_url"><?php echo $_SERVER['HTTP_HOST']?></div>
                        </div>
                      </td>
                   </tr>
                   <tr valign="top">
                      <td class="first"><?php echo __('Message', 'smpush-plugin-lang')?></td>
                      <td>
                         <textarea name="desktop_welc_message" cols="40" rows="10" id="smpush-message" onkeyup="jQuery('.smpush-sample_notification_message').html(this.value)" class="regular-text"><?php echo self::$apisetting['desktop_welc_message']?></textarea>
                      </td>
                   </tr>
                   <tr valign="middle">
                      <td class="first"><?php echo __('Icon', 'smpush-plugin-lang')?></td>
                      <td>
                         <input class="smpush_upload_field_deskicon" type="url" size="60" name="desktop_welc_icon" value="<?php echo self::$apisetting['desktop_welc_icon']?>" onchange="jQuery('.smpush-sample_notification_logo').attr('src',this.value)" />
                          <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_deskicon" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                          <p class="description"><?php echo __('Choose an icon in a standard size 192x192 px', 'smpush-plugin-lang')?></p>
                      </td>
                   </tr>
                   <tr valign="middle">
                      <td class="first"><?php echo __('Link To Open', 'smpush-plugin-lang')?></td>
                      <td>
                         <input name="desktop_welc_link" class="regular-text" type="url" size="50" value="<?php echo self::$apisetting['desktop_welc_link']?>" />
                         <p class="description"><?php echo __('Open link when user clicks on notification message', 'smpush-plugin-lang')?></p>
                      </td>
                   </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-subs_page smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/form.png" alt="" /> <span><?php echo __('Subscription Page', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td class="first"><?php echo __('Shortcode', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" value="[smart_subscription_page]" onclick="jQuery(this).select()" readonly class="regular-text" size="40" />
                      </td>
                    </tr>
                   <tr valign="top">
                      <td class="first"><?php echo __('Geo-Fence', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="subspage_geo_status" <?php if(self::$apisetting['subspage_geo_status'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Allow to users selecting area to receive notifications about posts that located inside it', 'smpush-plugin-lang')?></label>
                        <p class="description"><?php echo __('Must set latitude and longitude keys from post meta keys to enable this feature', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top" class="smpush_geo_fields" <?php if(self::$apisetting['subspage_geo_status'] == 0):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('Latitude Meta Key', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="subspage_geo_lat" value="<?php echo self::$apisetting['subspage_geo_lat']; ?>" class="regular-text" size="30" />
                      </td>
                    </tr>
                    <tr valign="top" class="smpush_geo_fields" <?php if(self::$apisetting['subspage_geo_status'] == 0):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('Longitude Meta Key', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="subspage_geo_lng" value="<?php echo self::$apisetting['subspage_geo_lng']; ?>" class="regular-text" size="30" />
                      </td>
                    </tr>
                    <tr valign="top" class="smpush_geo_fields" <?php if(self::$apisetting['subspage_geo_status'] == 0):?>style="display:none"<?php endif;?>>
                      <td class="first"><?php echo __('OR ACF Field Key', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="text" name="subspage_geo_acf" value="<?php echo self::$apisetting['subspage_geo_acf']; ?>" class="regular-text" size="30" />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Keywords', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="subspage_keywords" <?php if(self::$apisetting['subspage_keywords'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Enable users entering some keywords to receive notifications about them', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Channels', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="subspage_channels" <?php if(self::$apisetting['subspage_channels'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Enable users choosing between the plugin channels', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Categories', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="subspage_cats_status" <?php if(self::$apisetting['subspage_cats_status'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Show categories for users to subscribe in its posts notifications', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Categories Images', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="subspage_show_catimages" <?php if(self::$apisetting['subspage_show_catimages'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Display categories in a grid view with images', 'smpush-plugin-lang')?></label>
                        <p class="description"><?php echo __('To enable categories images you need to install this plugin', 'smpush-plugin-lang')?> <a href="http://wordpress.org/plugins/categories-images/" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Match Case', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="subspage_matchone" <?php if(self::$apisetting['subspage_matchone'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Send to user if the notification accepts one of his categories, keywords or geo-fence entries.', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                   <tr valign="top">
                     <td class="first"><?php echo __('Categories Source', 'smpush-plugin-lang')?></td>
                      <td>
                        <select name="subspage_post_type" onchange="smiopushPostType(this)">
                        <option value="post" <?php if(self::$apisetting['subspage_post_type'] == 'post'){?>selected="selected"<?php }?>>Post</option>
                        <?php $post_types = get_post_types(array('_builtin' => false, 'public' => true));foreach ($post_types as $post_type):?>
                         <option value="<?php echo $post_type;?>" <?php if(self::$apisetting['subspage_post_type'] == $post_type){?>selected="selected"<?php }?>><?php echo $post_type;?></option>
                         <?php endforeach;?>
                      </select>
                       <select name="subspage_post_type_tax" id="smiopushPostTaxSelc" onchange="smiopushPostTax(this)">
                         <?php $taxonomy_objects = get_object_taxonomies(self::$apisetting['subspage_post_type'], 'objects');foreach ($taxonomy_objects as $type => $object):?>
                          <option value="<?php echo $type;?>" <?php if(self::$apisetting['subspage_post_type_tax'] == $type){?>selected="selected"<?php }?>><?php echo $type;?></option>
                          <?php endforeach;?>
                       </select>
                        <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smiopush_taxs_load" style="display:none" alt="" /><br />
                        <div id="taxonomy-category" class="categorydiv" style="margin-top:5px">
                          <div id="category-all" class="tabs-panel">
                           <ul id="categorychecklist" data-wp-lists="list:category" class="smiopushPostTaxDIV categorychecklist form-no-clear">
                            <?php wp_terms_checklist(0, array('selected_cats' => self::$apisetting['subspage_category'],'taxonomy' => self::$apisetting['subspage_post_type_tax'], 'checked_ontop' => false));?>
                           </ul>
                          </div>
                        </div>
                      </td>
                   </tr>
                   <tr valign="top">
                      <td class="first"><?php echo __('Supported Platforms', 'smpush-plugin-lang')?></td>
                      <td>
                        <label><input type="checkbox" value="1" name="subspage_plat_web" <?php if(self::$apisetting['subspage_plat_web'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Web Push Notification', 'smpush-plugin-lang')?></label><br />
                        <label><input type="checkbox" value="1" name="subspage_plat_mobile" <?php if(self::$apisetting['subspage_plat_mobile'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Mobile Push Notification', 'smpush-plugin-lang')?></label><br />
                        <label><input type="checkbox" value="1" name="subspage_plat_msn" <?php if(self::$apisetting['subspage_plat_msn'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Facebook Messenger', 'smpush-plugin-lang')?></label><br />
                        <label><input type="checkbox" value="1" name="subspage_plat_email" <?php if(self::$apisetting['subspage_plat_email'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Email', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td class="first"><?php echo __('Mobile App Links', 'smpush-plugin-lang')?></td>
                      <td>
                        <input type="url" name="subspage_applink_play" value="<?php echo self::$apisetting['subspage_applink_play']; ?>" placeholder="<?php echo __('Google Play', 'smpush-plugin-lang')?>" class="regular-text" size="60" /><br />
                        <input type="url" name="subspage_applink_ios" value="<?php echo self::$apisetting['subspage_applink_ios']; ?>" placeholder="<?php echo __('App Store', 'smpush-plugin-lang')?>" class="regular-text" size="60" /><br />
                        <input type="url" name="subspage_applink_wp" value="<?php echo self::$apisetting['subspage_applink_wp']; ?>" placeholder="<?php echo __('Windows Phone', 'smpush-plugin-lang')?>" class="regular-text" size="60" /><br />
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-events smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/events.png" alt="" /> <span><?php echo __('Push Notification Events', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="e_post_chantocats" type="checkbox" value="1" <?php if (self::$apisetting['e_post_chantocats'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Notify only members which subscribed in a channel name equivalent with the post category name', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="e_appcomment" type="checkbox" value="1" <?php if (self::$apisetting['e_appcomment'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Notify user when administrator approved on his comment', 'smpush-plugin-lang')?></label>
                        <input name="e_appcomment_body" type="text" value='<?php echo self::$apisetting['e_appcomment_body']; ?>' class="regular-text" size="80">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="e_newcomment" type="checkbox" value="1" <?php if (self::$apisetting['e_newcomment'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Notify author when added new comment on his post', 'smpush-plugin-lang')?></label>
                        <input name="e_newcomment_body" type="text" value='<?php echo self::$apisetting['e_newcomment_body']; ?>' class="regular-text" size="80">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="e_newcomment_allusers" type="checkbox" value="1" <?php if (self::$apisetting['e_newcomment_allusers'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Notify all users that commented on a post when adding a new comment on this post', 'smpush-plugin-lang')?></label>
                        <input name="e_newcomment_allusers_body" type="text" value='<?php echo self::$apisetting['e_newcomment_allusers_body']; ?>' class="regular-text" size="80">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="e_newcomment_mentions" type="checkbox" value="1" <?php if (self::$apisetting['e_newcomment_mentions'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Notify user when someone mention him in another comment', 'smpush-plugin-lang')?></label>
                        <input name="e_newcomment_mentions_body" type="text" value='<?php echo self::$apisetting['e_newcomment_mentions_body']; ?>' class="regular-text" size="80">
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="e_usercomuser" type="checkbox" value="1" <?php if (self::$apisetting['e_usercomuser'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Notify user when someone comment on his comment', 'smpush-plugin-lang')?></label>
                        <input name="e_usercomuser_body" type="text" value='<?php echo self::$apisetting['e_usercomuser_body']; ?>' class="regular-text" size="80">
                        <br class="clear">
                        <p class="description"><?php echo __('Notice: System will replace {subject},{comment} words with the subject of topic or comment content.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Notice: System will send the topic ID with the push notification message as name `relatedvalue`.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Notice: To use this feature first please enable the cron-job service, Look', 'smpush-plugin-lang')?> <a href="http://smartiolabs.com/product/push-notification-system/documentation#cron-job" target="_blank"><?php echo __('here', 'smpush-plugin-lang')?></a> <?php echo __('for further information', 'smpush-plugin-lang')?></p>                      </td>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-events smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/woocommerce.png" alt="" /> <span><?php echo __('WooCommerce', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                  <tr valign="top">
                    <td colspan="2">
                      <label><input name="e_woo_waiting" type="checkbox" value="1" <?php if (self::$apisetting['e_woo_waiting'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Show waiting list button for out of stock products where user can select it to be notified once product is available in stock again.', 'smpush-plugin-lang')?></label>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Notification Title', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_waiting_title" class="regular-text" type="text" size="40" value="<?php echo self::$apisetting['e_woo_waiting_title']?>" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Notification Message', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_waiting_message" class="regular-text" type="text" size="70" value="<?php echo self::$apisetting['e_woo_waiting_message']?>" />
                      <br class="clear">
                      <p class="description"><?php echo __('Available variables', 'smpush-plugin-lang')?> <code>{id}</code> <code>{title}</code> <code>{stock}</code> <code>{price}</code> <code>{discount}</code> <code>{description}</code></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td colspan="2">
                      <label><input name="e_woo_abandoned" type="checkbox" value="1" <?php if (self::$apisetting['e_woo_abandoned'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Enable the push notifications reminders for abandoned carts.', 'smpush-plugin-lang')?></label>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Reminder After', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_aband_maxage" class="regular-text" type="number" size="5" value="<?php echo self::$apisetting['e_woo_aband_maxage']?>" /> <?php echo __('hour', 'smpush-plugin-lang')?>
                      <p class="description"><?php echo __('Run the reminder for carts that remains inactive for number of hours', 'smpush-plugin-lang')?></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Reminder Max Times', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_aband_times" class="regular-text" type="number" size="5" value="<?php echo self::$apisetting['e_woo_aband_times']?>" />
                      <p class="description"><?php echo __('Run the reminder for number of times then disable it', 'smpush-plugin-lang')?></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Reminder Interval', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_aband_interval" class="regular-text" type="number" size="5" value="<?php echo self::$apisetting['e_woo_aband_interval']?>" /> <?php echo __('hour', 'smpush-plugin-lang')?>
                      <p class="description"><?php echo __('Time between each reminder and other', 'smpush-plugin-lang')?></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Notification Title', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_aband_title" class="regular-text" type="text" size="40" value="<?php echo self::$apisetting['e_woo_aband_title']?>" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Notification Message', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_aband_message" class="regular-text" type="text" size="70" value="<?php echo self::$apisetting['e_woo_aband_message']?>" />
                      <br class="clear">
                      <p class="description"><?php echo __('Available variables', 'smpush-plugin-lang')?><code>{productscount}</code> <code>{totalmoney}</code> <code>{customer_name}</code></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Last Reminder', 'smpush-plugin-lang')?></td>
                    <td>
                      <label><input name="e_woo_aband_last_rem" type="checkbox" value="1" <?php if (self::$apisetting['e_woo_aband_last_rem'] == 1) { ?>checked="checked"<?php } ?> />
                      <?php echo __('The last notification message before disabling the reminder for the cart', 'smpush-plugin-lang')?></label>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Notification Title', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_aband_last_title" class="regular-text" type="text" size="40" value="<?php echo self::$apisetting['e_woo_aband_last_title']?>" />
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Notification Message', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="e_woo_aband_last_message" class="regular-text" type="text" size="70" value="<?php echo self::$apisetting['e_woo_aband_last_message']?>" />
                      <br class="clear">
                      <p class="description"><?php echo __('Available variables', 'smpush-plugin-lang')?><code>{productscount}</code> <code>{totalmoney}</code> <code>{customer_name}</code></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td colspan="2">
                      <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                      <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-events smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/wpjobman.png" alt="" /> <span><?php echo __('WP Job Manager', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td colspan="2">
                        <label><input name="e_wpjobman_status" type="checkbox" value="1" <?php if (self::$apisetting['e_wpjobman_status'] == 1) { ?>checked="checked"<?php } ?>> <?php echo __('Notify user when has a new job offers match his choices', 'smpush-plugin-lang')?></label>
                        <input name="e_wpjobman_body" type="text" value='<?php echo self::$apisetting['e_wpjobman_body']; ?>' class="regular-text" size="80">
                        <br class="clear">
                        <p class="description"><?php echo __('Notice: System will replace {counter} word with the number of matched job offers.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Notice: System will replace {alert} word with the title of job alert.', 'smpush-plugin-lang')?></p>
                        <p class="description"><?php echo __('Notice: To use this feature first please enable the cron-job service, Look', 'smpush-plugin-lang')?> <a href="http://smartiolabs.com/product/push-notification-system/documentation#cron-job" target="_blank"><?php echo __('here', 'smpush-plugin-lang')?></a> <?php echo __('for further information', 'smpush-plugin-lang')?></p>                      </td>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-events smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/peepso.png" alt="" /> <span><?php echo __('PeepSo', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                  <tr valign="top">
                    <td>
                      <label><input name="peepso_notifications" type="checkbox" value="1" <?php if (self::$apisetting['peepso_notifications'] == 1) { ?>checked="checked"<?php } ?>>&nbsp;
                        <?php echo __('Enable all PeepSo notifications', 'smpush-plugin-lang')?></label>
                    </td>
                  </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-events smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/buddypress.png" alt="" /> <span><?php echo __('BuddyPress Events', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                    <tr valign="top">
                      <td>
                        <label><input name="bb_notify_friends" type="checkbox" value="1" <?php if (self::$apisetting['bb_notify_friends'] == 1) { ?>checked="checked"<?php } ?>>&nbsp;
                        <?php echo __('Enable user receive push notification for friends component', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td>
                        <label><input name="bb_notify_messages" type="checkbox" value="1" <?php if (self::$apisetting['bb_notify_messages'] == 1) { ?>checked="checked"<?php } ?>>&nbsp;
                        <?php echo __('Enable user receive push notification for messages component', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td>
                        <label><input name="bb_notify_activity" type="checkbox" value="1" <?php if (self::$apisetting['bb_notify_activity'] == 1) { ?>checked="checked"<?php } ?>>&nbsp;
                        <?php echo __('Enable user receive push notification for activity component', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td>
                        <label><input name="bb_notify_activity_admins_only" type="checkbox" value="1" <?php if (self::$apisetting['bb_notify_activity_admins_only'] == 1) { ?>checked="checked"<?php } ?>>&nbsp;
                        <?php echo __('Send push notifications for group activities to administrators only', 'smpush-plugin-lang')?></label>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td>
                        <label><input name="bb_notify_xprofile" type="checkbox" value="1" <?php if (self::$apisetting['bb_notify_xprofile'] == 1) { ?>checked="checked"<?php } ?>>&nbsp;
                        <?php echo __('Enable user receive push notification for xprofile component', 'smpush-plugin-lang')?></label>
                        <p class="description"><?php echo __('Notice: To use this feature first please enable the cron-job service, Look', 'smpush-plugin-lang')?> <a href="https://smartiolabs.com/product/push-notification-system/documentation#cron-job" target="_blank"><?php echo __('here', 'smpush-plugin-lang')?></a> <?php echo __('for further information', 'smpush-plugin-lang')?></p>
                      </td>
                    </tr>
                    <tr valign="top">
                      <td colspan="2">
                          <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                          <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="col-left" class="smpush-tabs-gdpr smpush-radio-tabs">
        <div id="post-body" class="metabox-holder columns-2">
          <div>
            <div id="namediv" class="stuffbox">
              <h3><label><img src="<?php echo smpush_imgpath; ?>/gdpr.png" alt="" /> <span><?php echo __('GDPR Compliance', 'smpush-plugin-lang')?></span></label></h3>
              <div class="inside">
                <table class="form-table">
                  <tbody>
                  <tr valign="top">
                    <td class="first"><label><?php echo __('Terms Page Link', 'smpush-plugin-lang')?></label></td>
                    <td>
                      <code><?php echo get_bloginfo('wpurl') ; ?>/</code><input name="gdpr_termslink" type="text" value="<?php echo self::$apisetting['gdpr_termslink']; ?>" class="regular-text">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><label><?php echo __('Privacy Page Link', 'smpush-plugin-lang')?></label></td>
                    <td>
                      <code><?php echo get_bloginfo('wpurl') ; ?>/</code><input name="gdpr_privacylink" type="text" value="<?php echo self::$apisetting['gdpr_privacylink']; ?>" class="regular-text">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Icon', 'smpush-plugin-lang')?></td>
                    <td>
                      <label><input type="checkbox" value="1" name="gdpr_icon" <?php if(self::$apisetting['gdpr_icon'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Enable showing icon where users can unsubscribe at anytime', 'smpush-plugin-lang')?></label>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Subscription Page', 'smpush-plugin-lang')?></td>
                    <td>
                      <label><input type="checkbox" value="1" name="gdpr_subs_btn" <?php if(self::$apisetting['gdpr_subs_btn'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Show unsubscribe and delete user subscription button in the subscription page.', 'smpush-plugin-lang')?></label>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Agreement Verification', 'smpush-plugin-lang')?></td>
                    <td>
                      <label><input type="checkbox" value="1" name="gdpr_ver_option" <?php if(self::$apisetting['gdpr_ver_option'] == 1):?>checked="checked"<?php endif;?> /> <?php echo __('Show agreement verification for all subscription forms and pages.', 'smpush-plugin-lang')?></label>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Verification Text', 'smpush-plugin-lang')?></td>
                    <td>
                      <input name="gdpr_ver_text" type="text" value="<?php echo self::$apisetting['gdpr_ver_text']; ?>" class="regular-text" size="80">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td class="first"><?php echo __('Agreement Statement', 'smpush-plugin-lang')?></td>
                    <td>
                      <textarea readonly cols="50" rows="15" style="width: 70%" onclick="jQuery(this).select()"><?php include(smpush_dir.'/pages/privacy.php')?></textarea>
                      <p class="description"><?php echo __('You can use or append this statement into your privacy policy page.', 'smpush-plugin-lang')?></p>
                    </td>
                  </tr>
                  <tr valign="top">
                    <td colspan="2">
                      <input type="submit" name="submit" class="button button-primary" value="<?php echo __('Save All Settings', 'smpush-plugin-lang')?>">
                      <img src="<?php echo smpush_imgpath; ?>/wpspin_light.gif" class="smpush_process" alt="" />
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>
<style>
input.labelauty + label{margin-top: 5px;height:70px!important;width:70px!important;background-color: #eaeaea!important;}
input.labelauty + label > img{width: 24px!important;height: 24px;}
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery("input[name='desktop_webpush'], input[name='desktop_webpush_old']").change(function () {
    if(jQuery("input[name='desktop_webpush']").is(':checked') && jQuery("input[name='desktop_webpush_old']").is(':checked')){
      jQuery(".desktop_webpush_hide").show();
      jQuery(".desktop_webpush_show").show();
    }
    else if(jQuery("input[name='desktop_webpush']").is(':checked')){
      jQuery(".desktop_webpush_hide").hide();
      jQuery(".desktop_webpush_show").show();
    }
    else{
      jQuery(".desktop_webpush_show").hide();
      jQuery(".desktop_webpush_hide").show();
    }
  });
  jQuery("input[name='desktop_webpush']").trigger("change");

  jQuery("input[name='subspage_geo_status']").change(function () {
    if(jQuery(this).is(':checked')){
      jQuery(".smpush_geo_fields").show();
    }
    else{
      jQuery(".smpush_geo_fields").hide();
    }
  });
  jQuery(".smpush_jradio").change(function () {
    jQuery(".smpush-radio-tabs").hide();
    jQuery(".smpush-tabs-"+jQuery(this).val()).show();
  });
});

function smiopushPostType(select){
  jQuery('.smiopush_taxs_load').show();
  jQuery.get("<?php echo $page_url?>&loadtaxs=1&noheader=1&smiopush_post_type="+jQuery(select).val(), function(data){
    jQuery('.smiopush_taxs_load').hide();
    jQuery("#smiopushPostTaxSelc").html(data);
  });
}

function smiopushPostTax(select){
  jQuery('.smiopush_taxs_load').show();
  jQuery.get("<?php echo $page_url?>&loadcats=1&noheader=1&smiopush_object_name="+jQuery(select).val(), function(data){
    jQuery('.smiopush_taxs_load').hide();
    jQuery(".smiopushPostTaxDIV").html(data);
  });
}
</script>