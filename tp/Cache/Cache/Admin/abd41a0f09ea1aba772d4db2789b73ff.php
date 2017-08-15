<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php if($action_name=='add'): echo U('Wechat/add'); else: echo U('Wechat/edit'); endif; ?>" method="post">
        <table width="100%" class="table_form">
            <tr>
                <th width="80"><?php echo L('site_name');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="site_id" id="J_site_id">
                        <option value="0"><?php echo L('chose_site');?></option>
                        <?php if(is_array($site_list)): $i = 0; $__LIST__ = $site_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["site_id"]); ?>" <?php if($val['site_id'] == $info['site_id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["site_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('app_id');?> :</th>
                <td><input type="text" name="app_id" class="input-text" id="J_app_id" value="<?php echo ($info["app_id"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('app_secret');?> :</th>
                <td><input type="text" name="app_secret" id="J_app_secret" value="<?php echo ($info["app_secret"]); ?>" class="input-text"></td>
            </tr>
            <tr>
                <th><?php echo L('token');?> :</th>
                <td><input type="text" name="token" id="J_token" value="<?php echo ($info["token"]); ?>" class="input-text"></td>
            </tr>
        </table>
        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
    </form>
</div>
<script>
        <?php $_result=L('js_wechat');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_site_id").formValidator({ onshow:lang.site_name, onfocus:lang.site_name}).inputValidator({ min:1, onerror:lang.site_name});
            $("#J_app_id").formValidator({ onshow:lang.app_id, onfocus:lang.app_id}).inputValidator({ min:2, onerror:lang.app_id});
            $("#J_app_secret").formValidator({ onshow:lang.app_secret, onfocus:lang.app_secret}).inputValidator({ min:2, onerror:lang.app_secret});
            $("#J_token").formValidator({ onshow:lang.token, onfocus:lang.token}).inputValidator({ min:2, onerror:lang.token});
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