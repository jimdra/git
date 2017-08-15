<?php if (!defined('THINK_PATH')) exit();?><!--添加管理员-->
<div class="dialog_content">
	<form id="info_form" name="info_form" action="<?php echo u('Admin/add');?>" method="post">
	<table width="100%" class="table_form">
		<tr> 
	      <th width="80"><?php echo L('admin_username');?> :</th>
	      <td><input type="text" name="username" id="username" class="input-text"></td>
	    </tr>
            <tr>
	      <th width="80"><?php echo L('admin_realname');?> :</th>
	      <td><input type="text" name="realname" id="realname" class="input-text"></td>
	    </tr>
	    <tr> 
	      <th><?php echo L('password');?> :</th>
	      <td><input type="password" name="password" id="password" class="input-text"></td>
	    </tr>
	    <tr>
	      <th><?php echo L('cofirmpwd');?> :</th>
	      <td><input type="password" name="repassword" id="repassword" class="input-text"></td>
	    </tr>
	    <tr>
	      <th><?php echo L('admininrole');?> :</th>
	      <td>
	      	<select name="role_id">
	        	<?php if(is_array($role_list)): $i = 0; $__LIST__ = $role_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["id"]); ?>"><?php echo ($val["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
	        </select>
	      </td>
	    </tr>
	</table>
	</form>
</div>
<script>
$(function(){
        $.formValidator.initConfig({formid:"info_form",autotip:true});
        $("#J_realname").formValidator({ onshow:'请输入真实姓名', onfocus:'请输入真实姓名'}).inputValidator({ min:2, max:10, onerror:'请输入真实姓名'});
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