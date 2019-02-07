<div class="wrap">
   <div id="smpush-icon-chanmanage" class="icon32"><br></div>
   <h2><?php echo __('RSS Auto Push', 'smpush-plugin-lang')?><a href="javascript:" onclick="smpush_open_service(-1,2,'',30)" class="add-new-h2"><?php echo __('New Source', 'smpush-plugin-lang')?></a>
   <img src="<?php echo smpush_imgpath.'/wpspin_light.gif';?>" alt="" class="smpush_service_-1_loading" style="display:none" />
   </h2>
   <div id="col-container">
      <div id="col-left" style="width: 100%;margin-top: 10px;">
         <div class="col-wrap">
             <table class="wp-list-table widefat fixed tags" cellspacing="0">
                <thead>
                   <tr>
                      <th scope="col" class="manage-column" style="width:35%"><span><?php echo __('Title', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center"><span><?php echo __('Data Counter', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center"><span><?php echo __('Read Status', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center"><span><?php echo __('Errors', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center smpush-canhide"><span><?php echo __('Last Update', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center"><span><?php echo __('Status', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column column-categories" style="width:100px"><span></span></th>
                   </tr>
                </thead>
                <tfoot>
                   <tr>
                      <th scope="col" class="manage-column"><span><?php echo __('Title', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center"><span><?php echo __('Data Counter', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center "><span><?php echo __('Read Status', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center smpush-canhide"><span><?php echo __('Errors', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center "><span><?php echo __('Last Update', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column smpush-center"><span><?php echo __('Status', 'smpush-plugin-lang')?></span></th>
                      <th scope="col" class="manage-column column-categories"><span></span></th>
                   </tr>
                </tfoot>
                <tbody id="the-list" data-wp-lists="list:tag">
                <?php if($sources){$counter = 0;foreach($sources AS $source){$counter++;?>
                   <tr id="smpush-service-tab-<?php echo $source->id;?>" class="smpush-service-tab <?php if($counter%2 == 0){echo 'alternate';}?>">
                      <td class="name column-name"><strong><?php echo $source->title;?></strong></td>
                      <td class="description column-description smpush-center"><?php echo $source->data_counter;?></td>
                      <td class="description column-description smpush-center"><?php if($source->read_status == 2):?><img title="<?php echo __('Failed', 'smpush-plugin-lang')?>" src="<?php echo smpush_imgpath.'/error.png';?>" /><?php elseif($source->read_status == 1):?><img title="<?php echo __('Success', 'smpush-plugin-lang')?>" src="<?php echo smpush_imgpath.'/right.png';?>" /><?php else:?><img title="<?php echo __('Pending', 'smpush-plugin-lang')?>" src="<?php echo smpush_imgpath.'/clock.png';?>" /><?php endif;?></td>
                      <td class="description column-description smpush-center"><?php if($source->read_status == 2):?><img title="<?php echo $source->read_error?>" src="<?php echo smpush_imgpath.'/inactive.png';?>" /><?php endif;?></td>
                      <td class="description column-description smpush-center smpush-canhide"><?php echo (empty($source->lastupdate))? '' : gmdate(self::$wpdateformat, $source->lastupdate);?></td>
                      <td class="description column-description smpush-center"><?php echo ($source->active == 1)? __('Active', 'smpush-plugin-lang') : __('Inactive', 'smpush-plugin-lang');?></td>
                      <td class="description column-categories">
                      <input type="button" class="button action smpush-open-btn" value="<?php echo __('Edit', 'smpush-plugin-lang')?>" onclick="smpush_open_service(<?php echo $source->id;?>,'','',30)" />
                      <input type="button" class="button action smpush-open-btn" value="<?php echo __('Delete', 'smpush-plugin-lang')?>" onclick="smpush_delete_service(<?php echo $source->id;?>)" style="margin-top:4px" />
                      <img src="<?php echo smpush_imgpath.'/wpspin_light.gif';?>" alt="" class="smpush_service_<?php echo $source->id;?>_loading" style="display:none" />
                      </td>
                   </tr>
                <?php }}else{?>
                <tr class="no-items smpush-center"><td class="colspanchange" colspan="7"><?php echo __('No items found.', 'smpush-plugin-lang')?></td></tr>
                <?php }?>
                </tbody>
             </table>
             <br class="clear">
         </div>
      </div>
      <div id="col-right" class="smpush_form_ajax" style="width: 70%;display:none"></div>
   </div>
</div>
<script type="text/javascript">
var smpush_pageurl = '<?php echo $pageurl;?>';
</script>