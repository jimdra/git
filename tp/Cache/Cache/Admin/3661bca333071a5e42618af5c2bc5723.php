<?php if (!defined('THINK_PATH')) exit();?><!--编辑管理员-->
<div style="width: 400px">
	<form id="info_form" action="<?php echo U('Index/edit');?>" method="post">
	<table width="100%" class="table_form">
		<tr>
      		<th width="80"><?php echo L('admin_username');?> :</th>
      		<td><?php echo ($info["username"]); ?></td>
    	</tr>
        <tr>
	      <th width="80">旧密码 :</th>
	      <td><input type="password" name="oldPassword" id="J_oldPassword" class="input-text" ></td>
	    </tr>
    	<tr> 
      		<th><?php echo L('password');?> :</th>
      		<td><input type="password" name="Password" id="J_password" class="input-text"></td>
    	</tr>
    	<tr>
      		<th><?php echo L('cofirmpwd');?> :</th>
      		<td><input type="password" name="repassword" id="J_repassword" class="input-text"></td>
    	</tr>
	</table>
	<input type="hidden" name="AID" value="<?php echo ($info["AID"]); ?>" />
	</form>
</div>

<script>
$(function(){
	$.formValidator.initConfig({formid:"info_form",autotip:true});
	$("#J_password").formValidator({ empty:true, onshow:lang.not_edit_password, onfocus:lang.password+lang.between_6_to_20}).inputValidator({ min:6, max:20, onerror:lang.password+lang.between_6_to_20});
	$("#J_repassword").formValidator({ empty:true, onshow:lang.not_edit_password, onfocus:lang.cofirmpwd}).compareValidator({desid:"J_password",operateor:"=",onerror:lang.passwords_not_match});

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
});
</script>