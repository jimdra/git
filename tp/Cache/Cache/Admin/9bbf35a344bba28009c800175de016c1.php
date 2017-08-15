<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form name="info_form" id="info_form" action="<?php if($action_name=='add'): echo U($controller_name.'/add'); else: echo U($controller_name.'/edit'); endif; ?>" method="post">
        <table width="100%" class="table_form">
            <tr>
                <th width="80"><?php echo L('platform_name');?> :</th>
                <td><input type="text" name="platform_name" class="input-text" id="J_platform_name"  value="<?php echo ($info["platform_name"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('api_url');?> :</th>
                <td><input type="text" name="api_url" class="input-text" id="J_api_url" value="<?php echo ($info["api_url"]); ?>" ></td>
            </tr>
        </table>
        <input type="hidden" name="platform_id" value="<?php echo ($info["platform_id"]); ?>" />
    </form>
</div>

<script>
        <?php $_result=L('js_lang_admin');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>

    $(function(){
        var platform_name = "<?php echo L('platform_name');?>";
        var api_url = "<?php echo L('api_url');?>";
        $.formValidator.initConfig({formid:"info_form",autotip:true});
        $("#J_platform_name").formValidator({ onshow:lang.please_input+platform_name, onfocus:lang.please_input+platform_name}).inputValidator({ min:2, max:50, onerror:lang.please_input+platform_name});
        $("#J_api_url").formValidator({ onshow:lang.please_input+api_url, onfocus:lang.please_input+api_url}).inputValidator({ min:2, max:150, onerror:lang.please_input+api_url});

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