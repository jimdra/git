<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php if($action_name=='add'): echo U('MaterialType/add'); else: echo U('MaterialType/edit'); endif; ?>" method="post">
        <table width="100%" class="table_form">
            
            <tr>
                <th><?php echo L('type_name');?> :</th>
                <td><input type="text" name="type_name" class="input-text" id="J_type_name" value="<?php echo ($info["type_name"]); ?>" ></td>
            </tr>
        </table>
        <input type="hidden" name="type_id" value="<?php echo ($info["type_id"]); ?>" />
    </form>
</div>

<script>
        <?php $_result=L('js_type');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_type_name").formValidator({ onshow:lang.type_name, onfocus:lang.type_name}).inputValidator({ min:2, max:10, onerror:lang.type_name});
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