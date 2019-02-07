<?php $smpush_mute_activated = get_post_meta(get_the_ID(), 'smpush_mute_activated', true);?>
<table>
  <tbody>
    <tr valign="middle">
      <td>
        <h2 style="padding:12px 0"><?php echo __('Notification Lock', 'smpush-plugin-lang')?></h2>
        <select name="smpush_mute">
          <option value="yes" <?php if ((self::$apisetting['metabox_check_status'] == 1 && empty($smpush_mute_activated)) || $smpush_mute_activated == 'yes'):?>selected="selected"<?php endif; ?>><?php echo __('Locked, don\'t notify users', 'smpush-plugin-lang')?></option>
          <option value="no" <?php if ((self::$apisetting['metabox_check_status'] == 0 && empty($smpush_mute_activated)) || $smpush_mute_activated == 'no'):?>selected="selected"<?php endif; ?>><?php echo __('Unlocked', 'smpush-plugin-lang')?></option>
          <option value="lock"><?php echo __('Send & lock', 'smpush-plugin-lang')?></option>
        </select>
      </td>
    </tr>
    <tr valign="middle">
      <td>
        <h2 style="padding:12px 0"><?php echo __('Specific Channels', 'smpush-plugin-lang')?></h2>
        <ul class="categorychecklist form-no-clear" style="margin: 0">
          <li><label class="selectit"><input type="checkbox" name="smpush_all_users" checked="checked"> <?php echo __('All Users', 'smpush-plugin-lang')?></label></li>
          <?php foreach ($channels as $channel): ?>
            <li><label class="selectit"><input value="<?php echo $channel->id; ?>" type="checkbox" name="smpush_channels[]" disabled="disabled"> <?php echo $channel->title; ?> (<?php echo $channel->count; ?>)</label></li>
          <?php endforeach; ?>
        </ul>
      </td>
    </tr>
    <tr valign="middle">
      <td>
        <div class="misc-pub-section curtime misc-pub-curtime" style="padding:5px 0">
          <span id="timestamp"><?php echo __('Push Time', 'smpush-plugin-lang')?> <b><?php echo __('immediately')?></b></span>
          <a href="#" id="smio_edit_timestamp" class="edit-timestamp hide-if-no-js" role="button"><span aria-hidden="true"><?php echo __('Edit')?></span></a>
          <fieldset id="smio_timestampdiv" class="hide-if-js" style="display: none;">
            <div id="smio_timestampdiv"><?php self::touch_time(false, 0, true); ?></div>
            <p>
              <a href="#" class="smio_close_timestamp save-timestamp hide-if-no-js button"><?php echo __('OK')?></a>
              <a href="#" class="smio_close_timestamp cancel-timestamp hide-if-no-js button-cancel"><?php echo __('Cancel')?></a>
            </p>
          </fieldset>
        </div>
      </td>
    </tr>
  </tbody>
</table>
<script type="text/javascript">
jQuery(document).ready(function() {
  jQuery('input[name="smpush_all_users"]').click(function(){
    if(jQuery(this)[0].checked){
      jQuery('input[name="smpush_channels[]"]').attr("disabled", "disabled");
    }
    else{
      jQuery('input[name="smpush_channels[]"]').removeAttr("disabled");
    }
  });
  
  jQuery('#smio_edit_timestamp').click(function(){
    jQuery(this).hide();
    jQuery('#smio_timestampdiv').slideDown();
  });
  
  jQuery('.smio_close_timestamp').click(function(){
    jQuery('#smio_timestampdiv').slideUp(function(){ jQuery('#smio_edit_timestamp').show(); });
  });
  
});
</script>
<style>
#smio_timestampdiv select {
height: 21px;
line-height: 14px;
padding: 0;
vertical-align: top;
font-size: 12px;
}
#smio_timestampdiv input {border-width: 1px;border-style: solid;width: 2em;height: 21px;font-size: 13px;}
</style>