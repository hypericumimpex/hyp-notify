<select class="smpushPostAttri smpushPostAttriSelector" style="width:150px;float:left;margin:0 5px 10px 0">
  <option value=""><?php echo __('Choose Attribute', 'smpush-plugin-lang')?></option>
</select>
<select class="smpushPostAttriFormat" style="float:left;margin:0 5px 10px 0">
   <option value="regular"><?php echo __('Select Format', 'smpush-plugin-lang')?></option>
   <option value="CapitalizeFirst">CapitalizeFirst</option>
   <option value="CapitalizeAllFirst">CapitalizeAllFirst</option>
   <option value="UPPERCASE">UPPERCASE</option>
   <option value="lowercase">lowercase</option>
   <option value="datetime">Date Time</option>
   <option value="date">Date</option>
   <option value="regular">Regular</option>
   <option value="truncate">Truncate</option>
 </select>
<select class="smpushPostAttriFunction smpushPostFunctions" style="float:left;margin:0 5px 10px 0">
  <option value=""><?php echo __('Pass ID To Function And Get', 'smpush-plugin-lang')?></option>
</select>
<input type="text" class="smpushPostAttriDefault" size="15" placeholder="<?php echo __('Default Value', 'smpush-plugin-lang')?>" style="float:left;margin:0 5px 10px 0">
<input type="button" class="smpushInsertAtrri button button-primary" style="float:left;margin:0 5px 5px 0" value="<?php echo __('Insert', 'smpush-plugin-lang')?>">