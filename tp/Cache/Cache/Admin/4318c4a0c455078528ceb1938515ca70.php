<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php if($action_name=='add'): echo U('Category/add'); else: echo U('Category/edit'); endif; ?>" method="post">
        <table width="100%" class="table_form">
            <tr>
                <th width="80"><?php echo L('catsite');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="site_id">
                        <?php if(is_array($site_list)): $i = 0; $__LIST__ = $site_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["site_id"]); ?>" <?php if($val['site_id'] == $info['site_id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["site_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('catparent');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="parent_id">
                        <option value="0"><?php echo L('no_parent_cate');?></option>
                        <?php echo ($parent_cate); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('ispage');?> :</th>
                <td>
                    <input type="radio" name="ispage" class="input-radio" <?php if(!$info or ($info["ispage"] == 0)): ?>checked<?php endif; ?> value="0" /><?php echo L('no');?> <input type="radio" name="ispage" class="input-radio" <?php if($info["ispage"] == 1): ?>checked<?php endif; ?> value="1" /><?php echo L('yes');?>
                </td>
            </tr>
            <tr>
                <th><?php echo L('catname');?> :</th>
                <td><input type="text" name="cat_name" class="input-text" id="J_cat_name" value="<?php echo ($info["cat_name"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('catdir');?> :</th>
                <td><input type="text" name="cat_dir" class="input-text" id="J_cat_dir" value="<?php echo ($info["cat_dir"]); ?>" ></td>
            </tr>
<!--            <tr>
                <th><?php echo L('zq_url');?> :</th>
                <td><input type="text" name="zq_url" class="input-text" value="<?php echo ($info["zq_url"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('gl_url');?> :</th>
                <td><input type="text" name="gl_url" class="input-text" value="<?php echo ($info["gl_url"]); ?>" ></td>
            </tr>-->
        </table>
        <input type="hidden" name="cat_id" value="<?php echo ($info["cat_id"]); ?>" />
    </form>
</div>

<script>
        <?php $_result=L('js_cate');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_cat_name").formValidator({ onshow:lang.cat_name, onfocus:lang.cat_name}).inputValidator({ min:2, max:10, onerror:lang.cat_name});
            $("#J_cat_dir").formValidator({ onshow:lang.cat_dir, onfocus:lang.cat_dir}).inputValidator({ min:2, max:50, onerror:lang.cat_dir}).regexValidator({ regexp:'\\w', onerror:lang.allow_letter});
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