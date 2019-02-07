<div class="wrap">
   <div id="smpush-icon-chanmanage" class="icon32"><br></div>
   <h2><?php echo __('Import Subscribers', 'smpush-plugin-lang')?></h2>
   <div id="col-container">
     <div id="col-right" class="smpushFormAjaxResult" style="width: 100%">
       <form action="<?php echo $pageurl;?>&noheader=1" method="post" id="smpush_jform" class="validate">
           <div id="post-body" class="metabox-holder columns-2">
              <div id="post-body-content" class="edit-form-section">
                 <div id="namediv" class="stuffbox">
                    <h3><label><?php echo __('Step 1', 'smpush-plugin-lang');?></label></h3>
                    <div class="inside">
                       <table class="form-table">
                        <tbody>
                          <tr valign="top" class="form-required">
                             <td class="first"><?php echo __('Select CSV file', 'smpush-plugin-lang')?></td>
                             <td>
                             <input name="csv" type="file" size="40" aria-required="true">
                             <p class="description"><?php echo __('Upload CSV file of your subscribers with first line name of columns and UTF-8 or ANSI encoding.', 'smpush-plugin-lang')?></p>
                             </td>
                          </tr>
                          <tr valign="top" class="form-required">
                              <td class="first"><?php echo __('Delimiter', 'smpush-plugin-lang')?></td>
                              <td>
                                <select name="delimiter" aria-required="true">
                                  <option value=",">,</option>
                                  <option value=";">;</option>
                                  <option value="@">@</option>
                                  <option value="$">$</option>
                                </select>
                              </td>
                           </tr>
                          <tr valign="top">
                            <td colspan="2"><input type="submit" name="submit" id="smio-submit" class="button button-primary" style="width: 120px;" value="<?php echo __('Upload File', 'smpush-plugin-lang')?>">
                            <img src="<?php echo smpush_imgpath;?>/wpspin_light.gif" class="smpush_process" alt="" /></td>
                         </tr>
                        </tbody>
                      </table>
                    </div>
                 </div>
              </div>
           </div>
        </form>
     </div>
   </div>
</div>
<script type="text/javascript">
var smpush_pageurl = '<?php echo $pageurl;?>';
jQuery(document).ready(function() {
});
</script>