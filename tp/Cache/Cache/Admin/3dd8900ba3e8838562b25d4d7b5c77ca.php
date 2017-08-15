<?php if (!defined('THINK_PATH')) exit();?><!--编辑角色-->
<div class="dialog_content">
	<form id="info_form" name="info_form" action="<?php echo u('admin_role/edit');?>" method="post">
    <table width="100%" class="table_form">
        <tr>
            <th width="80"><?php echo L('role_name');?> :</th>
            <td><input type="text" name="name" id="name" class="input-text" value="<?php echo ($info["name"]); ?>"></td>
        </tr>
        <tr>
            <th><?php echo L('role_desc');?> :</th>
            <td><textarea name="remark" id="remark" cols="50" rows="3"><?php echo ($info["remark"]); ?></textarea></td>
        </tr>
        <?php if($info["id"] > 1): ?><tr>
            <th><?php echo L('enabled');?> :</th>
            <td>
                <input type="radio" name="status" class="radio_style" value="1" <?php if($info["status"] == 1): ?>checked="checked"<?php endif; ?>> &nbsp;<?php echo L('yes');?>&nbsp;&nbsp;&nbsp;
                <input type="radio" name="status" class="radio_style" value="0" <?php if($info["status"] == 0): ?>checked="checked"<?php endif; ?>> &nbsp;<?php echo L('no');?>
            </td>
        </tr><?php endif; ?>
    </table>
    <input type="hidden" name="id" id="id" value="<?php echo ($info["id"]); ?>" />
    </form>
</div>
<script>
$(function(){


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
</script>