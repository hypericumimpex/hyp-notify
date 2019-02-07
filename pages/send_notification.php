<div class="wrap" id="smpush-dashboard">
   <div id="smpush-icon-push" class="icon32"><br></div>
   <h2><?php echo get_admin_page_title();?>
     <a href="<?php echo admin_url();?>admin.php?page=smpush_active_tokens&noheader=1" data-confirm="<?php echo __('Are you sure you want to activate all invalid device tokens', 'smpush-plugin-lang')?>?" class="smio-delete add-new-h2"><?php echo __('Active All Tokens', 'smpush-plugin-lang')?></a>
     <a href="javascript:" class="add-new-h2" onclick="smpushResetHistoryTables()"><?php echo __('Reset Table Views', 'smpush-plugin-lang')?></a>
   </h2>
   <form action="<?php echo $page_url;?>" method="post" id="smpushSendCampignForm" onsubmit="return smpushAutoSaveNewsletter()">
      <input type="hidden" name="noheader" value="1" />
      <input type="hidden" name="id" value="<?php echo self::loadData('id')?>" />
      <input type="hidden" name="latitude" id="smio_latitude" value="<?php echo self::loadData('latitude')?>" />
      <input type="hidden" name="longitude" id="smio_longitude" value="<?php echo self::loadData('longitude')?>" />
      <div id="col-container">
         <div id="col-left" style="width: 98%">
            <div class="metabox-holder" data-smpush-counter="1">
               <div class="postbox-container" style="width:100%;">
                  <div class="meta-box-sortables">
                     <div class="postbox">
                        <img src="<?php echo smpush_imgpath; ?>/close.png" class="smpushCloseTB" style="display:none" />
                        <div class="handlediv" title="<?php echo __('Click to toggle', 'smpush-plugin-lang')?>"><br></div>
                        <h3><label><?php echo __('Message', 'smpush-plugin-lang')?></label></h3>
                        <div class="inside">
                           <table class="form-table">
                              <tbody>
                                <tr valign="middle">
                                    <td class="first"><?php echo __('Campaign Name', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <input name="name" type="text" size="70" value="<?php echo self::loadData('name')?>" required />
                                    </td>
                                 </tr>
                                 <tr valign="middle">
                                    <td colspan="2">
                                 <div class="categorydiv smpush_tabs">
                                        <ul class="category-tabs">
                                          <li><a href="#smpush-msg-desktop"><?php echo __('Web & Mobile', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-msg-fbmsn"><?php echo __('Facebook Messenger', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-msg-fbnotify"><?php echo __('Facebook Notification', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-msg-email"><?php echo __('Newsletter', 'smpush-plugin-lang')?></a></li>
                                        </ul>
                                   <div id="smpush-msg-desktop" class="tabs-panel">
                                     <?php include('send_notification_web.php')?>
                                   </div>
                                   <div id="smpush-msg-fbmsn" class="tabs-panel">
                                     <table class="form-table">
                                          <tr valign="top">
                                            <td class="first"><?php echo __('Subject', 'smpush-plugin-lang')?></td>
                                            <td>
                                               <input name="fbmsn_subject" type="text" size="80" value="<?php echo self::loadData('options', 'fbmsn_subject')?>" />
                                            </td>
                                         </tr>
                                         <tr valign="top">
                                           <td class="first" style="width: 13%"><?php echo __('Message', 'smpush-plugin-lang')?></td>
                                            <td>
                                               <textarea name="fbmsn_message" cols="40" rows="10" class="smpush_emoji large-text"><?php echo self::loadData('options', 'fbmsn_message')?></textarea>
                                            </td>
                                         </tr>
                                         <tr valign="middle">
                                            <td class="first"><?php echo __('Embedded Image', 'smpush-plugin-lang')?></td>
                                            <td>
                                               <input class="smpush_upload_field_msnimage" type="url" size="60" name="fbmsn_image" value="<?php echo self::loadData('options','fbmsn_image'); ?>" />
                                                <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_msnimage" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
                                                <p class="description"><?php echo __('Choose an image to display in the Messenger message template', 'smpush-plugin-lang')?></p>
                                            </td>
                                         </tr>
                                         <tr valign="top">
                                            <td class="first"><?php echo __('Button Text', 'smpush-plugin-lang')?></td>
                                            <td>
                                              <input name="fbmsn_button" type="text" size="40" placeholder="<?php echo __('button like Check Out or Read More', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('options', 'fbmsn_button')?>" />
                                            </td>
                                         </tr>
                                         <tr valign="top">
                                            <td class="first"><?php echo __('Target Link', 'smpush-plugin-lang')?></td>
                                            <td>
                                               <input name="fbmsn_link" type="url" size="80" value="<?php echo self::loadData('options', 'fbmsn_link')?>" />
                                            </td>
                                         </tr>
                                         </table>
                                    </div>
                                   <div id="smpush-msg-fbnotify" class="tabs-panel">
                                     <table class="form-table">
                                         <tr valign="top">
                                            <td class="first"><?php echo __('Message', 'smpush-plugin-lang')?></td>
                                            <td>
                                               <textarea name="fbnotify_message" cols="40" rows="10" class="smpush_emoji large-text"><?php echo self::loadData('options', 'fbnotify_message')?></textarea>
                                            </td>
                                         </tr>
                                         <tr valign="top">
                                            <td class="first"><?php echo __('Open Link Action', 'smpush-plugin-lang')?></td>
                                            <td>
                                              <select name="fbnotify_openaction">
                                                <option value="outside"><?php echo __('Open target link outside Facebook', 'smpush-plugin-lang')?></option>
                                                <option value="inside" <?php if(self::loadData('options', 'fbnotify_openaction') == 'inside'){echo 'selected="selected"';}?>><?php echo __('Open target link inside Facebook application', 'smpush-plugin-lang')?></option>
                                              </select>
                                            </td>
                                         </tr>
                                         <tr valign="top">
                                            <td class="first"><?php echo __('Target Link', 'smpush-plugin-lang')?></td>
                                            <td>
                                               <input name="fbnotify_link" type="url" size="80" value="<?php echo self::loadData('options', 'fbnotify_link')?>" />
                                            </td>
                                         </tr>
                                         </table>
                                    </div>
                                   <div id="smpush-msg-email" class="tabs-panel">
                                     <table class="form-table">
    <tr valign="top"><td class="first"><?php echo __('From Email', 'smpush-plugin-lang')?></td>
    <td>
      <input name="email_sender" value="<?php echo self::loadData('options', 'email_sender')?>" type="email" size="50" />
    </td></tr>
    <tr valign="top"><td class="first"><?php echo __('From Name', 'smpush-plugin-lang')?></td>
    <td>
        <input name="email_fname" value="<?php echo self::loadData('options', 'email_fname')?>" type="text" size="30" />
    </td></tr>
    <tr valign="top"><td class="first"><?php echo __('Email Subject', 'smpush-plugin-lang')?></td>
    <td>
        <input class="smpush_emoji" name="email_subject" value="<?php echo self::loadData('options', 'email_subject')?>" type="text" size="80" />
    </td></tr>
    <tr valign="top"><td class="first"><?php echo __('Template', 'smpush-plugin-lang')?></td>
    <td>
      <select id="newsletterSelTemplate">
        <option value="0"><?php echo __('Select template', 'smpush-plugin-lang')?></option>
        <?php foreach($params['newsletter_templates'] as $newsletter_template): ?>
          <option value="<?php echo $newsletter_template->id?>"><?php echo $newsletter_template->title?></option>
        <?php endforeach;?>
      </select>
    </td></tr>
    <tr valign="top"><td colspan="2">
      <div id="newsletter-plugin-container"></div>
      <textarea class="smpush-hide" name="email" id="newsletterHTMLholder"><?php echo self::loadData('options', 'email')?></textarea>
      <textarea class="smpush-hide" name="emailjson" id="newsletterJSONholder"><?php echo self::loadData('emailjson')?></textarea>
    </td></tr>
                                         </table>
                                    </div>
                                 </div>
                                    </td>
                                 <tr valign="middle">
                                    <td colspan="2">
                                      <div class="categorydiv smpush_tabs">
                                        <ul class="category-tabs">
                                          <li><a href="#smpush-tabs-all"><?php echo __('All Platforms', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-tabs-android"><?php echo __('Android', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-tabs-wp8"><?php echo __('Windows Phone 8', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-tabs-wp10"><?php echo __('Windows 10', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-tabs-ios"><?php echo __('iOS Adjustments', 'smpush-plugin-lang')?></a></li>
                                          <li><a href="#smpush-tabs-android-custs"><?php echo __('Android Adjustments', 'smpush-plugin-lang')?></a></li>
                                        </ul>
                                        <div id="smpush-tabs-all" class="tabs-panel">
                                          <table class="form-table">
                                            <tbody>
                                               <tr valign="top">
                                                  <td class="first"><?php echo __('Type', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <select name="extra_type" class="smpush-payload">
                                                        <option value="multi" <?php if(self::loadData('extra_type') == 'json'){echo 'selected="selected"';}?>><?php echo __('Multi Values', 'smpush-plugin-lang')?></option>
                                                        <option value="normal" <?php if(self::loadData('extra_type') == 'normal'){echo 'selected="selected"';}?>><?php echo __('Normal Text', 'smpush-plugin-lang')?></option>
                                                        <option value="json"><?php echo __('JSON', 'smpush-plugin-lang')?></option>
                                                     </select>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="smpush-payload-multi" <?php if(self::loadData('extra_type') != 'json' && self::loadData('extra_type') != ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="key[]" value="<?php echo self::loadData('key', 0)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>" size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('value', 0)?>" name="value[]" type="text" size="20" /><br />
                                                     <input name="key[]" value="<?php echo self::loadData('key', 1)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('value', 1)?>" name="value[]" type="text" size="20" /><br />
                                                     <input name="key[]" value="<?php echo self::loadData('key', 2)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('value', 2)?>" name="value[]" type="text" size="20" /><br />
                                                     <input name="key[]" value="<?php echo self::loadData('key', 3)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('value', 3)?>" name="value[]" type="text" size="20" /><br />
                                                     <input name="key[]" value="<?php echo self::loadData('key', 4)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('value', 4)?>" name="value[]" type="text" size="20" /><br />
                                                     <input name="key[]" value="<?php echo self::loadData('key', 5)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('value', 5)?>" name="value[]" type="text" size="20" />
                                                     <p class="description"><?php echo __('Keys with empty values will ignore.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="smpush-payload-normal" <?php if(self::loadData('extra_type') == 'json' || self::loadData('extra_type') == ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                    <textarea name="extra" class="regular-text" style="width:95%;height:80px"><?php echo self::loadData('extra')?></textarea>
                                                     <p class="description"><?php echo __('Send with push message as name `relatedvalue`', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                        </div>
                                        <div id="smpush-tabs-android" class="tabs-panel">
                                          <table class="form-table">
                                            <tbody>
                                               <tr valign="top">
                                                  <td class="first"><?php echo __('Type', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <select name="and_extra_type" class="and_smpush-payload">
                                                        <option value="multi" <?php if(self::loadData('and_extra_type') == 'json'){echo 'selected="selected"';}?>><?php echo __('Multi Values', 'smpush-plugin-lang')?></option>
                                                        <option value="normal" <?php if(self::loadData('and_extra_type') == 'normal'){echo 'selected="selected"';}?>><?php echo __('Normal Text', 'smpush-plugin-lang')?></option>
                                                        <option value="json"><?php echo __('JSON', 'smpush-plugin-lang')?></option>
                                                     </select>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="and_smpush-payload-multi" <?php if(self::loadData('and_extra_type') != 'json' && self::loadData('and_extra_type') != ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="and_key[]" value="<?php echo self::loadData('and_key', 0)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('and_value', 0)?>" name="and_value[]" type="text" size="20" /><br />
                                                     <input name="and_key[]" value="<?php echo self::loadData('and_key', 1)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('and_value', 1)?>" name="and_value[]" type="text" size="20" /><br />
                                                     <input name="and_key[]" value="<?php echo self::loadData('and_key', 2)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('and_value', 2)?>" name="and_value[]" type="text" size="20" /><br />
                                                     <input name="and_key[]" value="<?php echo self::loadData('and_key', 3)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('and_value', 3)?>" name="and_value[]" type="text" size="20" /><br />
                                                     <input name="and_key[]" value="<?php echo self::loadData('and_key', 4)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('and_value', 4)?>" name="and_value[]" type="text" size="20" /><br />
                                                     <input name="and_key[]" value="<?php echo self::loadData('and_key', 5)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('and_value', 5)?>" name="and_value[]" type="text" size="20" />
                                                     <p class="description"><?php echo __('Keys with empty values will ignore.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="and_smpush-payload-normal" <?php if(self::loadData('and_extra_type') == 'json' || self::loadData('and_extra_type') == ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <textarea name="and_extra" class="regular-text" style="width:95%;height:80px"><?php echo self::loadData('and_extra')?></textarea>
                                                     <p class="description"><?php echo __('Send with push message as name `relatedvalue`', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                        </div>
                                        <div id="smpush-tabs-wp8" class="tabs-panel">
                                          <table class="form-table">
                                            <tbody>
                                               <tr valign="top">
                                                  <td class="first"><?php echo __('Type', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <select name="wp_extra_type" class="wp_smpush-payload">
                                                        <option value="multi" <?php if(self::loadData('wp_extra_type') == 'json'){echo 'selected="selected"';}?>><?php echo __('Multi Values', 'smpush-plugin-lang')?></option>
                                                        <option value="normal" <?php if(self::loadData('wp_extra_type') == 'normal'){echo 'selected="selected"';}?>><?php echo __('Normal Text', 'smpush-plugin-lang')?></option>
                                                        <option value="json"><?php echo __('JSON', 'smpush-plugin-lang')?></option>
                                                     </select>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="wp_smpush-payload-multi" <?php if(self::loadData('wp_extra_type') != 'json' && self::loadData('wp_extra_type') != ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="wp_key[]" value="<?php echo self::loadData('wp_key', 0)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp_value', 0)?>" name="wp_value[]" type="text" size="20" /><br />
                                                     <input name="wp_key[]" value="<?php echo self::loadData('wp_key', 1)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp_value', 1)?>" name="wp_value[]" type="text" size="20" /><br />
                                                     <input name="wp_key[]" value="<?php echo self::loadData('wp_key', 2)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp_value', 2)?>" name="wp_value[]" type="text" size="20" /><br />
                                                     <input name="wp_key[]" value="<?php echo self::loadData('wp_key', 3)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp_value', 3)?>" name="wp_value[]" type="text" size="20" /><br />
                                                     <input name="wp_key[]" value="<?php echo self::loadData('wp_key', 4)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp_value', 4)?>" name="wp_value[]" type="text" size="20" /><br />
                                                     <input name="wp_key[]" value="<?php echo self::loadData('wp_key', 5)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp_value', 5)?>" name="wp_value[]" type="text" size="20" />
                                                     <p class="description"><?php echo __('Keys with empty values will ignore.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="wp_smpush-payload-normal" <?php if(self::loadData('wp_extra_type') == 'json' || self::loadData('wp_extra_type') == ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <textarea name="wp_extra" class="regular-text" style="width:95%;height:80px"><?php echo self::loadData('wp_extra')?></textarea>
                                                     <p class="description"><?php echo __('Send with push message as name `relatedvalue`', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                        </div>
                                        <div id="smpush-tabs-wp10" class="tabs-panel">
                                          <table class="form-table">
                                            <tbody>
                                               <tr valign="top">
                                                  <td class="first"><?php echo __('Type', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <select name="wp10_extra_type" class="wp10_smpush-payload">
                                                        <option value="multi" <?php if(self::loadData('wp10_extra_type') == 'json'){echo 'selected="selected"';}?>><?php echo __('Multi Values', 'smpush-plugin-lang')?></option>
                                                        <option value="normal" <?php if(self::loadData('wp10_extra_type') == 'normal'){echo 'selected="selected"';}?>><?php echo __('Normal Text', 'smpush-plugin-lang')?></option>
                                                        <option value="json"><?php echo __('JSON', 'smpush-plugin-lang')?></option>
                                                     </select>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="wp10_smpush-payload-multi" <?php if(self::loadData('wp10_extra_type') != 'json' && self::loadData('wp10_extra_type') != ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="wp10_key[]" value="<?php echo self::loadData('wp10_key', 0)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp10_value', 0)?>" name="wp10_value[]" type="text" size="20" /><br />
                                                     <input name="wp10_key[]" value="<?php echo self::loadData('wp10_key', 1)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp10_value', 1)?>" name="wp10_value[]" type="text" size="20" /><br />
                                                     <input name="wp10_key[]" value="<?php echo self::loadData('wp10_key', 2)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp10_value', 2)?>" name="wp10_value[]" type="text" size="20" /><br />
                                                     <input name="wp10_key[]" value="<?php echo self::loadData('wp10_key', 3)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp10_value', 3)?>" name="wp10_value[]" type="text" size="20" /><br />
                                                     <input name="wp10_key[]" value="<?php echo self::loadData('wp10_key', 4)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp10_value', 4)?>" name="wp10_value[]" type="text" size="20" /><br />
                                                     <input name="wp10_key[]" value="<?php echo self::loadData('wp10_key', 5)?>" type="text" placeholder="<?php echo __('key', 'smpush-plugin-lang')?>"  size="10" /> <input placeholder="<?php echo __('value', 'smpush-plugin-lang')?>" value="<?php echo self::loadData('wp10_value', 5)?>" name="wp10_value[]" type="text" size="20" />
                                                     <p class="description"><?php echo __('Keys with empty values will ignore.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="top" class="wp10_smpush-payload-normal" <?php if(self::loadData('wp10_extra_type') == 'json' || self::loadData('wp10_extra_type') == ''){echo 'style="display:none;"';}?>>
                                                  <td class="first"><?php echo __('Payload', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <textarea name="wp10_extra" class="regular-text" style="width:95%;height:80px"><?php echo self::loadData('wp10_extra')?></textarea>
                                                     <p class="description"><?php echo __('Send with push message as name `relatedvalue`', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="middle">
                                                  <td class="first">Image</td>
                                                  <td>
                                                    <input name="wp10_img" type="url" value="<?php echo self::loadData('wp10_img')?>" size="35" />
                                                     <p class="description"><?php echo __('Image link to appear beside the subject of push message .', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                        </div>
                                        <div id="smpush-tabs-ios" class="tabs-panel">
                                          <table class="form-table">
                                            <tbody>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Lock Key', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="ios_slide" type="text" value="<?php echo self::loadData('options', 'ios_slide')?>" />
                                                     <p class="description"><?php echo __('Change (view) sentence in (Slide to view)', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Badge', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="ios_badge" type="text" value="<?php echo self::loadData('options', 'ios_badge')?>" />
                                                     <p class="description"><?php echo __('The number to display as the badge of the application icon.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Sound', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="ios_sound" type="text" value="<?php echo self::loadData('options', 'ios_sound');?>" />
                                                     <p class="description"><?php echo __('The name of a sound file in the application bundle.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Content Available', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="ios_cavailable" type="text" value="<?php echo self::loadData('options', 'ios_cavailable')?>" />
                                                     <p class="description"><?php echo __('Provide this key with a value of 1 to indicate that new content is available.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Launch Image', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="ios_launchimg" type="text" value="<?php echo self::loadData('options', 'ios_launchimg')?>" />
                                                     <p class="description"><?php echo __('The filename of an image file in the application bundle.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                        </div>
                                        <div id="smpush-tabs-android-custs" class="tabs-panel">
                                          <table class="form-table">
                                            <tbody>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Title', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="android_title" type="text" value="<?php echo self::loadData('options', 'android_title')?>" />
                                                     <p class="description"><?php echo __('Title of notification appears above the message body.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Icon', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="android_icon" type="text" value="<?php echo self::loadData('options', 'android_icon')?>" />
                                                     <p class="description"><?php echo __('Set icon file name to customize the push message icon.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                               <tr valign="middle">
                                                  <td class="first"><?php echo __('Sound', 'smpush-plugin-lang')?></td>
                                                  <td>
                                                     <input name="android_sound" type="text" value="<?php echo self::loadData('options', 'android_sound');?>" />
                                                     <p class="description"><?php echo __('The sound to play when the device receives the notification.', 'smpush-plugin-lang')?></p>
                                                  </td>
                                               </tr>
                                            </tbody>
                                         </table>
                                        </div>
                                      </div>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="metabox-holder" data-smpush-counter="2">
               <div class="postbox-container" style="width:100%;">
                  <div class="meta-box-sortables">
                     <div class="postbox">
                       <img src="<?php echo smpush_imgpath; ?>/close.png" class="smpushCloseTB" style="display:none" />
                        <div class="handlediv" title="<?php echo __('Click to toggle', 'smpush-plugin-lang')?>"><br></div>
                        <h3><label><?php echo __('GEO-fence settings', 'smpush-plugin-lang')?></label></h3>
                        <div class="inside">
                           <table class="form-table">
                              <tbody>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('GPS Last Update', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <input name="gps_expire_time" value="<?php echo self::loadData('gps_expire_time', false, 1)?>" type="number" size="10" step="1" /> <?php echo __('Hour', 'smpush-plugin-lang')?>
                                       <p class="description"><?php echo __('Set its value to 0 for ignoring the last update time', 'smpush-plugin-lang')?></p>
                                    </td>
                                 </tr>
                                 <tr valign="top">
                                    <td colspan="2">
                                       <div id="smio_gmap_search">
                                          <input id="smio_gmap_address" class="smio_gmap_input" type="text" placeholder="<?php echo __('Put the search address then press Enter...', 'smpush-plugin-lang')?>" />
                                          <input name="radius" id="smio_gmap_radius" value="<?php echo self::loadData('radius')?>" class="smio_gmap_input" type="number" step="1" placeholder="<?php echo __('Radius in miles', 'smpush-plugin-lang')?>" style="width:150px" />
                                       </div>
                                       <div id="smio-gmap"></div>
                                       <br /><a href="<?php echo admin_url();?>admin.php?page=smpush_realtime_gps&noheader=1&width=800&height=700" class="button button-primary thickbox"><?php echo __('Watch Real-time GPS', 'smpush-plugin-lang')?></a>
                                    </td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="metabox-holder" data-smpush-counter="4">
               <div class="postbox-container" style="width:100%;">
                  <div class="meta-box-sortables">
                     <div class="postbox">
                       <img src="<?php echo smpush_imgpath; ?>/close.png" class="smpushCloseTB" style="display:none" />
                        <div class="handlediv" title="<?php echo __('Click to toggle', 'smpush-plugin-lang')?>"><br></div>
                        <h3><label><?php echo __('Send Settings', 'smpush-plugin-lang')?></label></h3>
                        <div class="inside">
                           <table class="form-table">
                              <tbody>
                                <?php if(self::loadData('send_type') == 'custom'): ?>
                                <input type="hidden" name="send_type" value="custom" />
                                <?php elseif(self::loadData('send_type') == 'live'): ?>
                                <input type="hidden" name="send_type" value="live" />
                                <?php else: ?>
                                 <tr valign="top">
                                    <td class="first" style="width: 22%"><?php echo __('Send type', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <input class="send_ontime_option_now smpush_jradio" name="send_type" value="geofence" type="radio" <?php if(self::loadData('send_type') == "geofence"):?>checked="checked"<?php endif;?> data-icon="<?php echo smpush_imgpath; ?>/navigation.png" data-labelauty='<?php echo __('Auto Geo-Fence', 'smpush-plugin-lang')?>' data-note='<?php echo __('Automatically send message to any device locate in the selected Geo-Zone', 'smpush-plugin-lang')?>' />
                                        <input class="send_ontime_option_now smpush_jradio" name="send_type" value="template" type="radio" <?php if(self::loadData('send_type') == "template"):?>checked="checked"<?php endif;?> data-icon="<?php echo smpush_imgpath; ?>/template.png" data-labelauty='<?php echo __('Save as template', 'smpush-plugin-lang')?>' data-note='<?php echo __('Save message as a template to use it later', 'smpush-plugin-lang')?>' />
                                        <input class="send_ontime_option_now send_option_instant smpush_jradio" name="send_type" value="now" type="radio" <?php if(self::loadData('send_type') == "now"):?>checked="checked"<?php endif;?> data-icon="<?php echo smpush_imgpath; ?>/send.png" data-labelauty='<?php echo __('Send now', 'smpush-plugin-lang')?>' />
                                        <input class="send_ontime_option_date smpush_jradio" name="send_type" value="time" type="radio" <?php if(self::loadData('send_type') == "time"):?>checked="checked"<?php endif;?> data-icon="<?php echo smpush_imgpath; ?>/calendar.png" data-labelauty='<?php echo __('Send on time', 'smpush-plugin-lang')?>' />
                                        <div style="float:right" class="send_ontime_later <?php if(self::loadData('send_type') != 'time'):?>smpush-hide<?php endif;?>">
                                          <input class="smpush-timepicker send_ontime_later <?php if(self::loadData('send_type') != 'time'):?>smpush-hide<?php endif;?>" value="<?php $starttime = self::loadData('starttime');echo (empty($starttime))?'':date('Y-m-d h:i a', strtotime($starttime))?>" name="send_time" style="width:300px;margin:10px 0" placeholder='<?php echo __('Select start time', 'smpush-plugin-lang')?>' type="text" readonly="readonly" required />
                                          <div class="clear"></div>
                                          <label>
                                            <input name="send_repeatly" type="checkbox" <?php if(self::loadData('repeat_interval') > 0):?>checked="checked"<?php endif;?> /> <?php echo __('Repeat Every', 'smpush-plugin-lang')?>
                                            <input style="width:100px" name="repeat_interval" value="<?php echo self::loadData('repeat_interval')?>" type="number" size="6" />
                                            <select name="repeat_age" style="width:100px">
                                              <option value="minute" <?php if(self::loadData('repeat_age') == "minute"):?>selected="selected"<?php endif;?>><?php echo __('Minute', 'smpush-plugin-lang')?></option>
                                              <option value="hour" <?php if(self::loadData('repeat_age') == "hour"):?>selected="selected"<?php endif;?>><?php echo __('Hour', 'smpush-plugin-lang')?></option>
                                              <option value="day" <?php if(self::loadData('repeat_age') == "day"):?>selected="selected"<?php endif;?>><?php echo __('Day', 'smpush-plugin-lang')?></option>
                                              <option value="month" <?php if(self::loadData('repeat_age') == "month"):?>selected="selected"<?php endif;?>><?php echo __('Month', 'smpush-plugin-lang')?></option>
                                              <option value="year" <?php if(self::loadData('repeat_age') == "year"):?>selected="selected"<?php endif;?>><?php echo __('Year', 'smpush-plugin-lang')?></option>
                                            </select>
                                          </td>
                                        </div>
                                    </td>
                                 </tr>
                                 <?php endif;?>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('Device type', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <select name="platforms[]" multiple="multiple" class="smpush_select2" style="width:100%;display:none">
                                          <option value="ios" <?php $chanhistory=self::loadData('platforms');if(!empty($chanhistory)){if(in_array('ios', $chanhistory)){echo 'selected="selected"';}}?>>iOS</option>
                                          <option value="iosfcm" <?php if(!empty($chanhistory)){if(in_array('iosfcm', $chanhistory)){echo 'selected="selected"';}}?>>iOS FCM</option>
                                          <option value="android" <?php if(!empty($chanhistory)){if(in_array('android', $chanhistory)){echo 'selected="selected"';}}?>>Android</option>
                                          <option value="wp" <?php if(!empty($chanhistory)){if(in_array('wp', $chanhistory)){echo 'selected="selected"';}}?>>Windows Phone 8</option>
                                          <option value="wp10" <?php if(!empty($chanhistory)){if(in_array('wp10', $chanhistory)){echo 'selected="selected"';}}?>>Windows 10</option>
                                          <option value="bb" <?php if(!empty($chanhistory)){if(in_array('bb', $chanhistory)){echo 'selected="selected"';}}?>>BlackBerry</option>
                                          <option value="chrome" <?php if(!empty($chanhistory)){if(in_array('chrome', $chanhistory)){echo 'selected="selected"';}}?>>Chrome</option>
                                          <option value="safari" <?php if(!empty($chanhistory)){if(in_array('safari', $chanhistory)){echo 'selected="selected"';}}?>>Safari</option>
                                          <option value="firefox" <?php if(!empty($chanhistory)){if(in_array('firefox', $chanhistory)){echo 'selected="selected"';}}?>>Firefox</option>
                                          <option value="opera" <?php if(!empty($chanhistory)){if(in_array('opera', $chanhistory)){echo 'selected="selected"';}}?>>Opera</option>
                                          <option value="edge" <?php if(!empty($chanhistory)){if(in_array('edge', $chanhistory)){echo 'selected="selected"';}}?>>Edge</option>
                                          <option value="samsung" <?php if(!empty($chanhistory)){if(in_array('samsung', $chanhistory)){echo 'selected="selected"';}}?>>Samsung Browser</option>
                                          <option value="fbmsn" <?php if(!empty($chanhistory)){if(in_array('fbmsn', $chanhistory)){echo 'selected="selected"';}}?>>Facebook Messenger</option>
                                          <option value="fbnotify" <?php if(!empty($chanhistory)){if(in_array('fbnotify', $chanhistory)){echo 'selected="selected"';}}?>>Facebook Notification</option>
                                          <option value="email" <?php if(!empty($chanhistory)){if(in_array('email', $chanhistory)){echo 'selected="selected"';}}?>>Newsletter</option>
                                       </select>
                                    </td>
                                 </tr>
                                 <?php if($params['dbtype'] == 'localhost'):?>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('User group', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <select name="usergroups[]" multiple="multiple" class="smpush_select2" style="width:100%;display:none">
                                         <?php
                                          $r = '';
                                          $editable_roles = array_reverse( get_editable_roles() );
                                          foreach ( $editable_roles as $role => $details ) {
                                              $name = translate_user_role($details['name'] );
                                              $chanhistory=self::loadData('options', 'usergroups');
                                              if (!empty($chanhistory) && in_array($role, $chanhistory)) {
                                                  $r .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
                                              } else {
                                                  $r .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
                                              }
                                          }
                                          echo $r;
                                         ?>
                                       </select>
                                    </td>
                                 </tr>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('WordPress users emails [Newsletter]', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <select name="emailgroups[]" multiple="multiple" class="smpush_select2" style="width:100%;display:none">
                                         <?php
                                          $r = '';
                                          $editable_roles = array_reverse( get_editable_roles() );
                                          foreach ( $editable_roles as $role => $details ) {
                                              $name = translate_user_role($details['name'] );
                                              $chanhistory=self::loadData('options', 'emailgroups');
                                              if (!empty($chanhistory) && in_array($role, $chanhistory)) {
                                                  $r .= "\n\t<option selected='selected' value='" . esc_attr( $role ) . "'>$name</option>";
                                              } else {
                                                  $r .= "\n\t<option value='" . esc_attr( $role ) . "'>$name</option>";
                                              }
                                          }
                                          echo $r;
                                         ?>
                                       </select>
                                    </td>
                                 </tr>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('In channels (AND)', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <select name="inchannels_and[]" multiple="multiple" class="smpush_select2" style="width:100%;display:none">
                                          <?php $chanhistory=self::loadData('options','inchannels_and');foreach($params['channels'] AS $channel){?>
                                          <option value="<?php echo $channel->id;?>" <?php if(!empty($chanhistory)){if(in_array($channel->id, $chanhistory)){echo 'selected="selected"';}}?>><?php echo $channel->title;?> (<?php echo $channel->count;?>)</option>
                                          <?php }?>
                                       </select>
                                       <p class="description"><?php echo __('Users subscribed in channels with AND relation', 'smpush-plugin-lang')?></p>
                                    </td>
                                 </tr>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('In channels (OR)', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <select name="inchannels_or[]" multiple="multiple" class="smpush_select2" style="width:100%;display:none">
                                          <?php $chanhistory=self::loadData('options','inchannels_or');foreach($params['channels'] AS $channel){?>
                                          <option value="<?php echo $channel->id;?>" <?php if(!empty($chanhistory)){if(in_array($channel->id, $chanhistory)){echo 'selected="selected"';}}?>><?php echo $channel->title;?> (<?php echo $channel->count;?>)</option>
                                          <?php }?>
                                       </select>
                                       <p class="description"><?php echo __('Users subscribed in channels with OR relation', 'smpush-plugin-lang')?></p>
                                    </td>
                                 </tr>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('Not in channels (AND)', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <select name="notchannels_and[]" multiple="multiple" class="smpush_select2" style="width:100%;display:none">
                                          <?php $chanhistory=self::loadData('options','notchannels_and');foreach($params['channels'] AS $channel){?>
                                          <option value="<?php echo $channel->id;?>" <?php if(!empty($chanhistory)){if(in_array($channel->id, $chanhistory)){echo 'selected="selected"';}}?>><?php echo $channel->title;?> (<?php echo $channel->count;?>)</option>
                                          <?php }?>
                                       </select>
                                       <p class="description"><?php echo __('Users not subscribed in channels with AND relation', 'smpush-plugin-lang')?></p>
                                    </td>
                                 </tr>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('Not in channels (OR)', 'smpush-plugin-lang')?></td>
                                    <td>
                                       <select name="notchannels_or[]" multiple="multiple" class="smpush_select2" style="width:100%;display:none">
                                          <?php $chanhistory=self::loadData('options','notchannels_or');foreach($params['channels'] AS $channel){?>
                                          <option value="<?php echo $channel->id;?>" <?php if(!empty($chanhistory)){if(in_array($channel->id, $chanhistory)){echo 'selected="selected"';}}?>><?php echo $channel->title;?> (<?php echo $channel->count;?>)</option>
                                          <?php }?>
                                       </select>
                                       <p class="description"><?php echo __('Users not subscribed in channels with OR relation', 'smpush-plugin-lang')?></p>
                                    </td>
                                 </tr>
                                 <?php endif;?>
                                 <?php if(self::loadData('processed') == 1): ?>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('Rerun', 'smpush-plugin-lang')?></td>
                                    <td><label><input name="rerun" type="checkbox" /> <?php echo __('System already processed this campaign enable this option to process again', 'smpush-plugin-lang')?></td></td>
                                 </tr>
                                 <?php endif;?>
                                 <tr valign="top">
                                    <td class="first"><?php echo __('Status', 'smpush-plugin-lang')?></td>
                                    <td><label><input name="status" type="checkbox" <?php if(self::loadData('status') == 1){echo 'checked="checked"';}?> /> <?php echo __('System will skip this campaign if you disable the campaign status', 'smpush-plugin-lang')?></td></td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
           <?php if(self::loadData('send_type') != 'custom' && self::loadData('send_type') != 'live'): ?>
           <div id="smpush-calculate-dashboard" class="metabox-holder" data-smpush-counter="3">
               <div class="postbox-container" style="width:100%;">
                  <div class="postbox">
                    <img src="<?php echo smpush_imgpath; ?>/close.png" class="smpushCloseTB" style="display:none" />
                     <table class="form-table" style="margin-top: 0;">
                        <tbody>
                           <tr valign="top">
                              <td>
                                <h4 class="heading">iOS</h4>
                                <p class="nothing"><span id="smpush-calculate-span-ios">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Android</h4>
                                <p class="nothing"><span id="smpush-calculate-span-android">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Chrome</h4>
                                <p class="nothing"><span id="smpush-calculate-span-chrome">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Safari</h4>
                                <p class="nothing"><span id="smpush-calculate-span-safari">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Opera</h4>
                                <p class="nothing"><span id="smpush-calculate-span-opera">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Edge</h4>
                                <p class="nothing"><span id="smpush-calculate-span-edge">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Samsung Browser</h4>
                                <p class="nothing"><span id="smpush-calculate-span-samsung">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Messenger</h4>
                                <p class="nothing"><span id="smpush-calculate-span-fbmsn">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td rowspan="2">
                                 <input type="button" id="smpush-calculate-btn" class="button" value="<?php echo __('Calculate Devices', 'smpush-plugin-lang')?>">
                                 <img src="<?php echo smpush_imgpath;?>/wpspin_light.gif" class="smpush_calculate_process" alt="" />
                              </td>
                           </tr>
                           <tr valign="top">
                             <td>
                                <h4 class="heading">iOS FCM</h4>
                                <p class="nothing"><span id="smpush-calculate-span-iosfcm">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                             <td>
                                <h4 class="heading">Firefox</h4>
                                <p class="nothing"><span id="smpush-calculate-span-firefox">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Windows Phone</h4>
                                <p class="nothing"><span id="smpush-calculate-span-wp">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">BlackBerry</h4>
                                <p class="nothing"><span id="smpush-calculate-span-bb">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Windows 10</h4>
                                <p class="nothing"><span id="smpush-calculate-span-wp10">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">FB Notifications</h4>
                                <p class="nothing"><span id="smpush-calculate-span-fbnotify">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                              <td>
                                <h4 class="heading">Newsletter</h4>
                                <p class="nothing"><span id="smpush-calculate-span-email">0</span> <?php echo __('Device', 'smpush-plugin-lang')?></p>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
            <div class="metabox-holder">
               <div class="postbox-container" style="width:100%;">
                  <div class="postbox">
                     <table class="form-table" style="margin-top: 0;">
                        <tbody>
                           <tr valign="top">
                              <td>
                                <input type="submit" name="sendlive" onclick="smpushActivatedSubmitBTN(this)" class="smpush-btn-startsendnow button button-primary <?php if(self::loadData('send_type') != "" AND self::loadData('send_type') != "now"):?>smpush-hide<?php endif;?>" value="<?php echo __('Live Send Dashboard', 'smpush-plugin-lang')?>">
                                <?php if(self::loadData('id') == ""):?>
                                <input type="submit" name="cronsend" onclick="smpushActivatedSubmitBTN(this)" class="button button-primary" value="<?php echo __('Send To Queue', 'smpush-plugin-lang')?>">
                                <?php else:?>
                                <input type="submit" name="cronsend" onclick="smpushActivatedSubmitBTN(this)" class="button button-primary" value="<?php echo __('Save Changes', 'smpush-plugin-lang')?>">
                                <?php endif;?>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </div>
            </div>
           <?php endif; ?>
         </div>
      </div>
   </form>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {
  smpushHideHistoryTables();
  jQuery(".smpush_select2").smpush_select2({tags: true})
 if(typeof postboxes !== 'undefined')
   postboxes.add_postbox_toggles('dashboard_page_stats');
 
  jQuery(".send_ontime_option_now, .send_ontime_option_date").click(function () {
    if(jQuery('.send_option_instant').is(':checked')){
      jQuery(".smpush-btn-startsendnow").removeClass("smpush-hide");
    }
    else{
      jQuery(".smpush-btn-startsendnow").addClass("smpush-hide");
    }
  });
  
  jQuery(".send_ontime_option_now").click(function () {
    if(jQuery('.send_ontime_option_now').is(':checked')){
      jQuery(".send_ontime_later").addClass("smpush-hide");
    }
  });

  jQuery(".send_ontime_option_date").click(function () {
    if(jQuery('.send_ontime_option_date').is(':checked')){
      jQuery(".send_ontime_later").removeClass("smpush-hide");
    }
  });
  if(jQuery("input[name='desktop_title']").val() != ""){
    jQuery("input[name='desktop_title']").trigger('onkeyup');
  }
  if(jQuery("textarea[name='message']").val() != ""){
    jQuery("textarea[name='message']").trigger('onkeyup');
  }
  if(jQuery("input[name='desktop_icon']").val() != ""){
    jQuery("input[name='desktop_icon']").trigger('change');
  }
  
});

function smpushremoveNonAlpha(input) {
    var string = jQuery(input).val().replace(/\W/g, '');
    jQuery(input).val(string);
  }
function smpushActionDelRow(button) {
  if(jQuery(".smpushChromeActions div.smpushlinebetween").length == 1){
    return;
  }
  jQuery(button).closest("div.smpushlinebetween").remove();
}
function smpushActionAddRow(button) {
  var maxloop = parseInt(jQuery(".smpushChromeActions .smpushlinebetween:last .actionsLoop").val())+1;
  var newRow = "<div class='smpushlinebetween row'>"+jQuery(button).closest("div.smpushlinebetween").html()+"</div>";
  jQuery(".smpushChromeActions").append(newRow);
  jQuery(".smpushChromeActions .smpushlinebetween:last").find("input[type='text'], input[type='url']").val("");
  jQuery(".smpushChromeActions .smpushlinebetween:last").find("input[type='text'], input[type='url']").removeAttr("id");

  var loop = parseInt(jQuery(button).closest("div.smpushlinebetween").find(".actionsLoop").val());
  jQuery(".smpushChromeActions .smpushlinebetween:last input[type='text'], .smpushChromeActions .smpushlinebetween:last input[type='url']").each(function () {
    var newname = jQuery(this).attr("name").replace("["+loop+"]", "["+maxloop+"]");
    jQuery(this).attr("name", newname);
  });
  jQuery(".smpushChromeActions .smpushlinebetween:last input[name*='actions[icon]']").attr("class", "smpush_upload_field_action"+maxloop);
  jQuery(".smpushChromeActions .smpushlinebetween:last .smpush_upload_file_btn").attr("data-container", "smpush_upload_field_action"+maxloop);
  jQuery(".smpushChromeActions .smpushlinebetween:last .actionsLoop").val(maxloop);

  smpushAutoLoad(".smpushChromeActions .smpushlinebetween:last");
}

function smpushActivatedSubmitBTN(button){
  jQuery("#smpushSendCampignForm input[type='submit']").removeClass("activatedSubmitBTN");
  jQuery(button).addClass("activatedSubmitBTN");
}

function smpushAutoSaveNewsletter(){
  if(bee !== null && beeSavedChanges === false){
    jQuery("#smpushSendCampignForm .activatedSubmitBTN").attr("data-text", jQuery("#smpushSendCampignForm .activatedSubmitBTN").val());
    jQuery("#smpushSendCampignForm .activatedSubmitBTN").val("<?php echo __('Saving Changes', 'smpush-plugin-lang')?>");
    jQuery("#smpushSendCampignForm .activatedSubmitBTN").attr("disabled", "disabled");
    bee.save();
    return false;
  }
  return true;
}

var bee = null;
var beeSavedChanges = false;
jQuery(document).ready(function () {
  if(jQuery("#newsletter-plugin-container").length > 0){
    var request = function(method, url, data, type, callback) {
      var req = new XMLHttpRequest();
      console.log(type);
      req.onreadystatechange = function() {
        if (req.readyState === 4 && req.status === 200) {
          var response = JSON.parse(req.responseText);
          callback(response);
        }
      };
      req.open(method, url, true);
      if (data && type) {
        if(type === 'multipart/form-data') {
          var formData = new FormData();
          for (var key in data) {
            formData.append(key, data[key]);
          }
          data = formData;          
        }
        else {
          req.setRequestHeader('Content-type', type);
        }
      }
      req.send(data);
    };

    var mergeTags = [
    {
      name: '<?php echo __('Unsubscribe Link', 'smpush-plugin-lang')?>',
      value: '<?php echo '<a href="{unsubscribe_link}">'.__('Unsubscribe', 'smpush-plugin-lang').'</a>'?>'
    },
    ];

    var beeConfig = {
      uid: 'smartiolabs_<?php echo str_replace('.', '', $_SERVER['SERVER_NAME'])?>',
      container: 'newsletter-plugin-container',
      autosave: false,
      language: '<?php echo $params['beePluginLang']?>',
      mergeTags: mergeTags,
      onSave: function(jsonFile, htmlFile) {
        beeSavedChanges = true;
        jQuery("#newsletterHTMLholder").val(htmlFile);
        jQuery("#newsletterJSONholder").val(jsonFile);
        jQuery("#smpushSendCampignForm").find("input[type='submit']").removeAttr("disabled");
        jQuery("#smpushSendCampignForm .activatedSubmitBTN").val(jQuery("#smpushSendCampignForm .activatedSubmitBTN").attr("data-text"));
        jQuery("#smpushSendCampignForm .activatedSubmitBTN").trigger("click");
      },
      onSaveAsTemplate: function(jsonFile) {
        alert("<?php echo __('under development', 'smpush-plugin-lang')?>");
      },
      onAutoSave: function(jsonFile) {
      },
      onSend: function(htmlFile) {
        request('POST', '<?php echo admin_url()?>admin.php?page=smpush_ajax_actions&action=testemail&noheader=1', {"email": jQuery("input[name='email_sender']").val(), "content": htmlFile, "subject": jQuery("input[name='email_subject']").val()}, null, function(data){} );
      },
      onError: function(errorMessage) {
        console.log('onError ', errorMessage);
      }
    };
    
    request(
      'POST', 
      'https://auth.getbee.io/apiauth',
      'grant_type=password&client_id=0e2b54b6-11e3-4a52-8506-e8c819b6ba7d&client_secret=OFI8dTMf61XUlvbbqLAkWxnq27tKRHa95DaVdj6hD9QO7Q7Ll2P',
      'application/x-www-form-urlencoded',
      function(token) {
        BeePlugin.create(token, beeConfig, function(beePluginInstance) {
          bee = beePluginInstance;
          request('GET', '<?php echo admin_url()?>admin.php?page=smpush_ajax_actions&action=jsontemplate&noheader=1&template=<?php $emailtemplate = self::loadData('emailtemplate');echo ((empty($emailtemplate))? 4 : $emailtemplate)?>', null, null,
            function(template) {
              bee.start(template);
            });
        });
    });
    
    jQuery("#newsletterSelTemplate").change(function () {
      jQuery.get('<?php echo admin_url()?>admin.php?page=smpush_ajax_actions&action=jsontemplate&noheader=1',{"template":jQuery(this).val()},function(template){
        template = JSON.parse(template);
        bee.load(template);
      });
    });
    
  }
});
</script>