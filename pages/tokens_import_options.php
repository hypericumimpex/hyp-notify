<form action="<?php echo $pageurl;?>&noheader=1" method="post" id="smpush_jform" class="validate">
<input type="hidden" name="csvfile" value="<?php echo $csvpath;?>">
<input type="hidden" name="delimiter" value="<?php echo $delimiter;?>">
   <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content" class="edit-form-section">
         <div id="namediv" class="stuffbox">
            <h3><label><?php echo __('Step 2', 'smpush-plugin-lang')?></label></h3>
            <div class="inside">
               <table class="form-table">
                <tbody>
                  <tr valign="top" class="form-required">
                     <td class="first"><?php echo __('Device Token', 'smpush-plugin-lang')?></td>
                     <td>
                        <select name="token">
                          <?php echo $csvhtml?>
                        </select>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Platform', 'smpush-plugin-lang')?></td>
                     <td>
                        <select name="platform">
                          <?php for($i=0;$i<=count(self::$platforms);$i++):?>
                            <option value="<?php echo self::$platforms[$i]?>"><?php echo self::$platform_titles[$i]?></option>
                          <?php endfor;?>
                        </select>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Information', 'smpush-plugin-lang')?></td>
                     <td>
                     <select name="info">
                        <?php echo $csvhtml?>
                     </select>
                     </td>
                  </tr>
                  <tr valign="top">
                    <td colspan="2"><input type="submit" name="submit" id="smio-submit" class="button button-primary" style="width: 120px;" value="<?php echo __('Save Changes', 'smpush-plugin-lang')?>">
                    <img src="<?php echo smpush_imgpath;?>/wpspin_light.gif" class="smpush_process" alt="" /></td>
                 </tr>
                </tbody>
              </table>
            </div>
         </div>
      </div>
   </div>
</form>