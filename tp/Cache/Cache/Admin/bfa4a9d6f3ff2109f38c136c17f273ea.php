<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php if($action_name=='add'): echo U('Theme/add'); else: echo U('Theme/edit'); endif; ?>" method="post">
        <table width="100%" class="table_form">
            
            <tr>
                <th><?php echo L('theme_name');?> :</th>
                <td><input type="text" name="theme_name" class="input-text" id="J_theme_name" value="<?php echo ($info["theme_name"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('theme_code');?> :</th>
                <td><input type="text" name="theme_code" class="input-text" id="J_theme_code" value="<?php echo ($info["theme_code"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('material_num');?> :</th>
                <td><input type="text" name="material_num" class="input-text" id="J_material_num" value="<?php echo ($info["material_num"]); ?>" ></td>
            </tr>
        </table>
        <input type="hidden" name="theme_id" value="<?php echo ($info["theme_id"]); ?>" />
    </form>
</div>

<script>
        <?php $_result=L('js_theme');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_theme_name").formValidator({ onshow:lang.theme_name, onfocus:lang.theme_name}).inputValidator({ min:2, onerror:lang.theme_name});
            $("#J_theme_code").formValidator({ onshow:lang.theme_code, onfocus:lang.theme_code}).inputValidator({ min:2, max:50, onerror:lang.theme_code}).regexValidator({ regexp:'\\w$', onerror:lang.allow_letter});
            $("#J_material_num").formValidator({ onshow:lang.material_num, onfocus:lang.material_num}).inputValidator({ type:'num',min:0, max:6, onerror:lang.material_num_range}).regexValidator({ regexp:'num',datatype:'enum',onerror:lang.allow_num});
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