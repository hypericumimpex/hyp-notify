<table class="form-table">
  <tr valign="middle">
      <td class="first"><?php echo __('Title', 'smpush-plugin-lang')?></td>
      <td>
         <input name="desktop_title" class="smpush_emoji" type="text" size="50" value="<?php echo self::loadData('options','desktop_title')?>" onkeyup="jQuery('.smpush-sample_notification_title').html(smpushProcessSmilies(this.value))" />
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
         <textarea name="message" cols="40" rows="10" id="smpush-message" onkeyup="jQuery('.smpush-sample_notification_message').html(smpushProcessSmilies(this.value))" class="smpush_emoji large-text"><?php echo self::loadData('message')?></textarea>
         <p class="description"><?php echo __('Reference for unicode smileys codes', 'smpush-plugin-lang')?> <a href="http://apps.timwhitlock.info/emoji/tables/unicode" target="_blank"><?php echo __('click here', 'smpush-plugin-lang')?></a></p>
      </td>
   </tr>
   <tr valign="middle">
      <td class="first"><?php echo __('Icon', 'smpush-plugin-lang')?></td>
      <td>
         <input class="smpush_upload_field_deskicon" type="url" size="60" name="desktop_icon" value="<?php echo self::loadData('options','desktop_icon'); ?>" onchange="jQuery('.smpush-sample_notification_logo').attr('src',this.value)" />
          <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_deskicon" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
          <p class="description"><?php echo __('Choose an icon in a standard size 192x192 px', 'smpush-plugin-lang')?></p>
      </td>
   </tr>
   <tr valign="middle">
      <td class="first"><?php echo __('Link To Open', 'smpush-plugin-lang')?></td>
      <td>
        <input name="desktop_link" type="url" size="50" value="<?php echo urldecode(self::loadData('options','desktop_link'))?>" />
         <p class="description"><?php echo __('Open link when user clicks on notification message', 'smpush-plugin-lang')?></p>
         <p class="description"><?php echo __('Leave it empty to set it as your website link', 'smpush-plugin-lang')?></p>
      </td>
   </tr>
   <tr valign="middle">
      <td class="first"><?php echo __('Big Image', 'smpush-plugin-lang')?></td>
      <td>
         <input class="smpush_upload_field_bigimage" type="url" size="60" name="desktop_bigimage" value="<?php echo self::loadData('options','desktop_bigimage'); ?>" />
          <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_bigimage" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
          <p class="description"><?php echo __('Big image appears under message content. Recommended size 300x200 px', 'smpush-plugin-lang')?></p>
      </td>
   </tr>
   <tr valign="middle">
      <td class="first"><?php echo __('Badge', 'smpush-plugin-lang')?></td>
      <td>
         <input class="smpush_upload_field_deskbadge" type="url" size="60" name="desktop_badge" value="<?php echo self::loadData('options','desktop_badge'); ?>" />
          <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_deskbadge" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
          <p class="description"><?php echo __('Badge icon for mobile only. Recommended size 24x24 px', 'smpush-plugin-lang')?></p>
      </td>
   </tr>
   <tr valign="middle">
      <td class="first"><?php echo __('Sound URL', 'smpush-plugin-lang')?></td>
      <td>
         <input class="smpush_upload_field_desksound" type="url" size="60" name="desktop_sound" value="<?php echo self::loadData('options','desktop_sound'); ?>" />
          <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_desksound" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
          <p class="description"><?php echo __('Play sound file when notification arrived for mobiles only', 'smpush-plugin-lang')?></p>
      </td>
   </tr>
    <tr valign="middle">
      <td class="first"><?php echo __('Duration', 'smpush-plugin-lang')?></td>
    <td>
      <select name="desktop_interaction">
        <option value="true" <?php if(self::loadData('options','desktop_interaction') == "true"): ?>selected="selected"<?php endif;?>><?php echo __('Open until interaction', 'smpush-plugin-lang')?></option>
        <option value="false" <?php if(self::loadData('options','desktop_interaction') == "false"): ?>selected="selected"<?php endif;?>><?php echo __('20 Seconds', 'smpush-plugin-lang')?></option>
      </select>
    </td>
    </tr>
    <tr valign="middle">
      <td class="first"><?php echo __('Vibrate', 'smpush-plugin-lang')?></td>
    <td>
      <input name="desktop_vibrate" value="<?php echo self::loadData('options','desktop_vibrate'); ?>" type="text" size="50" placeholder="100.200.100.300.200" />
      <p class="description"><?php echo __('Make mobile vibrates when message arrives with different power', 'smpush-plugin-lang')?></p>
    </td></tr>
    <tr valign="middle"><td class="first"><?php echo __('Silent', 'smpush-plugin-lang')?></td>
      <td>
        <label><input name="desktop_silent" <?php if(self::loadData('options','desktop_silent') == 1): ?>checked="checked"<?php endif;?> type="checkbox" /> <?php echo __('Message arrives silently in mobile', 'smpush-plugin-lang')?></label>
    </td>
    </tr>
    <tr valign="middle"><td class="first"><?php echo __('Direction', 'smpush-plugin-lang')?></td>
      <td>
        <select name="desktop_dir">
          <option value="auto"><?php echo __('Auto', 'smpush-plugin-lang')?></option>
          <option value="rtl" <?php if(self::loadData('options','desktop_dir') == "rtl"): ?>selected="selected"<?php endif;?>><?php echo __('RTL', 'smpush-plugin-lang')?></option>
          <option value="ltr" <?php if(self::loadData('options','desktop_dir') == "ltr"): ?>selected="selected"<?php endif;?>><?php echo __('LTR', 'smpush-plugin-lang')?></option>
        </select>
      </td>
    </tr>
    <tr valign="middle"><td class="first"><?php echo __('Chrome Actions', 'smpush-plugin-lang')?></td>
      <td>
        <div class="smpushChromeActions">
          <?php
          $desktop_actions = self::loadData('options','desktop_actions');
          if(empty($desktop_actions)){
            $desktop_actions = array('id' => array(0 => ''), 'text' => array(0 => ''), 'icon' => array(0 => ''), 'link' => array(0 => ''));
          }
          for($i=0;$i<count($desktop_actions['id']);$i++):
          ?>
          <div class="smpushlinebetween">
            <input value="<?php echo $i;?>" type="hidden" class="actionsLoop" />
            <input name="desktop_actions[id][<?php echo $i;?>]" value="<?php echo $desktop_actions['id'][$i];?>" onkeyup="smpushremoveNonAlpha(this)"  onchange="smpushremoveNonAlpha(this)" type="text" size="8" placeholder="<?php echo __('Action ID', 'smpush-plugin-lang')?>">
            <input name="desktop_actions[text][<?php echo $i;?>]" value="<?php echo $desktop_actions['text'][$i];?>" type="text" size="15" placeholder="<?php echo __('Button Text', 'smpush-plugin-lang')?>">
            <input class="smpush_upload_field_action<?php echo $i;?>" name="desktop_actions[icon][<?php echo $i;?>]" value="<?php echo $desktop_actions['icon'][$i];?>" type="url" size="25" placeholder="<?php echo __('Icon URL', 'smpush-plugin-lang')?>">
            <input class="smpush_upload_file_btn button action" data-container="smpush_upload_field_action<?php echo $i;?>" type="button" value="<?php echo __('Select File', 'smpush-plugin-lang')?>" />
            <br />
            <input name="desktop_actions[link][<?php echo $i;?>]" value="<?php echo $desktop_actions['link'][$i];?>" type="url" size="50" placeholder="<?php echo __('Open Link', 'smpush-plugin-lang')?>">
            <button type="button" class="button" onclick="smpushActionAddRow(this)"><?php echo __('Add', 'smpush-plugin-lang')?></button>
            <button type="button" class="button" onclick="smpushActionDelRow(this)"><?php echo __('Remove', 'smpush-plugin-lang')?></button>
          </div>
          <?php endfor;?>
        </div>
      </td>
    </tr>
  <tr valign="middle">
    <td class="first"><?php echo __('UTM Parameters', 'smpush-plugin-lang')?></td>
    <td>
      <input name="desktop_utm_source" value="<?php echo self::loadData('options','desktop_utm_source');?>" type="text" size="30" placeholder="<?php echo __('Source', 'smpush-plugin-lang')?>"><br />
      <input name="desktop_utm_medium" value="<?php echo self::loadData('options','desktop_utm_medium');?>" type="text" size="30" placeholder="<?php echo __('Medium', 'smpush-plugin-lang')?>"><br />
      <input name="desktop_utm_campaign" value="<?php echo self::loadData('options','desktop_utm_campaign');?>" type="text" size="30" placeholder="<?php echo __('Campaign', 'smpush-plugin-lang')?>">
    </td>
  </tr>
</table>