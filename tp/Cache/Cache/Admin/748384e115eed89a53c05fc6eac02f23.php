<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php if($action_name=='add'): echo U('Channel/add'); else: echo U('Channel/edit'); endif; ?>" method="post">
        <table width="100%" class="table_form">
            
            <tr>
                <th><?php echo L('channel_name');?> :</th>
                <td><input type="text" name="channel_name" class="input-text" id="J_channel_name" value="<?php echo ($info["channel_name"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('platform');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="platform_id" id="J_platform_id">
                        <option value="0"><?php echo L('chose_platform');?></option>
                        <option value="1" <?php if($info['platform_id'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L('ios');?></option>
                        <option value="2" <?php if($info['platform_id'] == 2): ?>selected="selected"<?php endif; ?>><?php echo L('android');?></option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="hidden" name="channel_id" value="<?php echo ($info["channel_id"]); ?>" />
    </form>
</div>

<script>
        <?php $_result=L('js_channel');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_channel_name").formValidator({ onshow:lang.channel_name, onfocus:lang.channel_name}).inputValidator({ min:2, max:100, onerror:lang.channel_name});
            $("#J_platform_id").formValidator({ onshow:lang.platform_id, onfocus:lang.platform_id}).inputValidator({ min:1, max:5, onerror:lang.platform_id});
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