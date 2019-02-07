<form action="<?php echo $pageurl;?>&noheader=1" method="post" id="smpush_jform" class="validate">
<input type="hidden" name="id" value="<?php echo $source['id'];?>">
   <div id="post-body" class="metabox-holder columns-2">
      <div id="post-body-content" class="edit-form-section">
         <div id="namediv" class="stuffbox">
            <h3><label><?php echo (empty($source['title']))? __('Add New Source', 'smpush-plugin-lang') : $source['title'];?></label></h3>
            <div class="inside">
               <table class="form-table">
                <tbody>
                  <tr valign="top">
                     <td class="first"><?php echo __('Title', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                     <input name="title" type="text" size="40" value="<?php echo $source['title'];?>" aria-required="true">
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('RSS Link', 'smpush-plugin-lang')?></td>
                     <td class="form-required">
                       <input name="link" type="url" size="60" value="<?php echo urldecode($source['link']);?>" aria-required="true">
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Message Template', 'smpush-plugin-lang')?></td>
                     <td>
                     <select name="campid" aria-required="true">
                       <option value="0"><?php echo __('Select Message Template', 'smpush-plugin-lang')?></option>
                      <?php if(!empty($templates)):foreach($templates as $template): ?>
                        <option value="<?php echo $template['id']?>" <?php if($source['campid'] == $template['id']){?>selected="selected"<?php }?>><?php echo $template['id'].'- '.$template['name']?></option>
                      <?php endforeach;endif; ?>
                     </select>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Text Limit', 'smpush-plugin-lang')?></td>
                     <td>
                       <label><input name="text_limit" type="number" size="6" value="<?php echo $source['text_limit'];?>"> <?php echo __('Limit number of characters for the content of feed items.', 'smpush-plugin-lang')?></label>
                       <p class="description"><?php echo __('Set it 0 for unlimited number of new fetched items.', 'smpush-plugin-lang')?></p>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Read Limit', 'smpush-plugin-lang')?></td>
                     <td>
                       <label><input name="read_limit" type="number" size="6" value="<?php echo $source['read_limit'];?>"> <?php echo __('Limit number of new fetched items each time.', 'smpush-plugin-lang')?></label>
                       <p class="description"><?php echo __('Set it 0 for unlimited number of new fetched items.', 'smpush-plugin-lang')?></p>
                     </td>
                  </tr>
                  <tr valign="top">
                     <td class="first"><?php echo __('Status', 'smpush-plugin-lang')?></td>
                     <td>
                      <input name="active" type="checkbox" <?php if($source['active'] == 1) { ?>checked="checked"<?php } ?>>
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