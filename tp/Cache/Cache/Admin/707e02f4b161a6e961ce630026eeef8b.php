<?php if (!defined('THINK_PATH')) exit();?><!--菜单添加-->
<div class="dialog_content">
	<form id="info_form" name="info_form" action="<?php echo u('menu/add');?>" method="post">
    <table width="100%" class="table_form">
    	<tr>
          <th width="100"><?php echo L('menu_parentid');?> :</th>
          <td>
          	<select name="pid">
            	<option value="0"><?php echo L('no_parent_menu');?></option>
            	<?php echo ($select_menus); ?>
            </select>
          </td>
        </tr>
        <tr>
          <th><?php echo L('menu_name');?> :</th>
          <td><input type="text" name="name" id="name" class="input-text"></td>
        </tr>
        <tr>
          <th><?php echo L('module_name');?> :</th>
          <td><input type="text" name="module_name" id="module_name" class="input-text"></td>
        </tr>
        <tr>
          <th><?php echo L('action_name');?> :</th>
          <td><input type="text" name="action_name" id="action_name" class="input-text"></td>
        </tr>
        <tr> 
          <th><?php echo L('att_data');?> :</th>
          <td><input type="text" name="data" id="data" class="input-text"></td>
        </tr>
        <tr>
          <th><?php echo L('remark');?> :</th>
          <td><textarea name="remark" id="remark" cols="40" rows="3"></textarea></td>
        </tr>
        <tr>
          <th><?php echo L('menu_display');?> :</th>
          <td>
            <label><input type="radio" name="display" class="radio_style" value="1" checked="checked"> <?php echo L('yes');?>&nbsp;&nbsp;</label>
            <label><input type="radio" name="display" class="radio_style" value="0"> <?php echo L('no');?></label>
          </td>
        </tr>
        <tr>
          <th><?php echo L('menu_iframe');?> :</th>
          <td>
            <label><input type="radio" name="iframe" class="radio_style" value="1" onchange="javascript:change_iframe(this.value);"> <?php echo L('yes');?>&nbsp;&nbsp;</label>
            <label><input type="radio" name="iframe" class="radio_style" value="0" checked="checked" onchange="javascript:change_iframe(this.value);"> <?php echo L('no');?></label>
          </td>
        </tr>
        <tr id="iframeWidth" style="display: none">
          <th><?php echo L('menu_iframe_width');?> :</th>
          <td><input type="text" name="iframeWidth" id="iframeWidth" class="input-text"></td>
        </tr>
        <tr id="iframeHeight" style="display: none">
          <th><?php echo L('menu_iframe_height');?> :</th>
          <td><input type="text" name="iframeHeight" id="iframeHeight" class="input-text"></td>
        </tr>
    </table>
    </form>
</div>

<script>
$(function(){
    
	$.formValidator.initConfig({formid:"info_form",autotip:true});
	$("#name").formValidator({ onshow:lang.please_input+lang.menu_name, onfocus:lang.please_input+lang.menu_name, oncorrect:lang.input_right}).inputValidator({ min:1, onerror:lang.please_input+lang.menu_name});
	$("#module_name").formValidator({ onshow:lang.please_input+lang.module_name, onfocus:lang.please_input+lang.module_name, oncorrect:lang.input_right}).inputValidator({ min:1, onerror:lang.please_input+lang.module_name});
	$("#action_name").formValidator({ onshow:lang.please_input+lang.action_name, onfocus:lang.please_input+lang.action_name, oncorrect:lang.input_right}).inputValidator({ min:1, onerror:lang.please_input+lang.action_name});
	
	$('#info_form').ajaxForm({success:complate,dataType:'json'});
	function complate(result){
		if(result.status == 1){
			$.dialog.get(result.dialog).close();
			$.TP.tip({content:result.msg});
			window.location.reload();
		} else {
			$.TP.tip({content:result.msg, icon:'alert'});
		}
	}
})
function change_iframe(v){
    if(v==1){
        $("#iframeWidth").show();
        $("#iframeHeight").show();
    }else{
        $("#iframeWidth").hide();
        $("#iframeHeight").hide();
    }
}
</script>