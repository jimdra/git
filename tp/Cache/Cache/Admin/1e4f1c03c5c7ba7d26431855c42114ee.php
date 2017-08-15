<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php if($action_name=='add'): echo U('Slide/add'); else: echo U('Slide/edit'); endif; ?>" method="post">
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
                <th><?php echo L('name');?> :</th>
                <td><input type="text" name="name" class="input-text" id="J_name" value="<?php echo ($info["name"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('width');?> :</th>
                <td><input type="text" name="width" value="<?php echo ($info["width"]); ?>" class="input-text"></td>
            </tr>
            <tr>
                <th><?php echo L('height');?> :</th>
                <td><input type="text" name="height" value="<?php echo ($info["height"]); ?>" class="input-text"></td>
            </tr>
        </table>
        <input type="hidden" name="menuid" value="<?php echo ($menuid); ?>" />
        <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
    </form>
</div>
<script>
        <?php $_result=L('js_site');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
        $.formValidator.initConfig({formid:"info_form",autotip:true});
        $("#J_site_id").formValidator({ onshow:lang.site_name, onfocus:lang.site_name}).inputValidator({ min:1, onerror:lang.site_name});
        $("#J_site_url").formValidator({ onshow:lang.site_url, onfocus:lang.site_url}).inputValidator({ min:2, onerror:lang.site_url});
        $("#J_web_url").formValidator({ empty:true, onfocus:lang.site_url}).inputValidator({ min:2, onerror:lang.site_url});
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