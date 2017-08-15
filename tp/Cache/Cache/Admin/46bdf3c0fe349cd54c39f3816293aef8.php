<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php if($action_name=='add'): echo U('Game/add'); else: echo U('Game/edit'); endif; ?>" method="post">
        <table width="100%" class="table_form">
            
            <tr>
                <th><?php echo L('game_name');?> :</th>
                <td><input type="text" name="game_name" class="input-text" id="J_game_name" value="<?php echo ($info["game_name"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('game_dir');?> :</th>
                <td><input type="text" name="game_code" class="input-text" id="J_game_code" value="<?php echo ($info["game_code"]); ?>" ></td>
            </tr>
        </table>
        <input type="hidden" name="game_id" value="<?php echo ($info["game_id"]); ?>" />
    </form>
</div>

<script>
        <?php $_result=L('js_game');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_game_name").formValidator({ onshow:lang.game_name, onfocus:lang.game_name}).inputValidator({ min:2, max:100, onerror:lang.game_name});
            $("#J_game_code").formValidator({ onshow:lang.game_dir, onfocus:lang.game_dir}).inputValidator({ min:2, max:50, onerror:lang.game_dir}).regexValidator({ regexp:'\\w$', onerror:lang.allow_letter});
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