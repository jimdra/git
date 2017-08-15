<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content">
    <form id="info_form" action="<?php echo U("Wechat/$action_name");?>" method="post">
        <table width="100%" class="table_form">
            <tr>
                <th width="80"><?php echo L('site_name');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="site_id" id="J_site_id" <?php if($info): ?>disabled<?php endif; ?>>
                        <option value="0"><?php echo L('chose_site');?></option>
                        <?php if(is_array($site_list)): $i = 0; $__LIST__ = $site_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["site_id"]); ?>" <?php if($val['site_id'] == $info['site_id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["site_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('menu_name');?> :</th>
                <td><input type="text" name="menu_name" class="input-text" id="J_menu_name" value="<?php echo ($info["menu_name"]); ?>" ></td>
            </tr>
            <tr>
                <th><?php echo L('menu_level');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="level" id="level" onchange="is_parent(this.value)" class="level">
                        <option value="1" <?php if($info['parent_id'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L('top_menu');?></option>
                        <option value="2" <?php if($info['parent_id'] != 0): ?>selected="selected"<?php endif; ?>><?php echo L('sub_menu');?></option>
                    </select>
                </td>
            </tr>
            <tr class="parent" style="display: none">
                <th><?php echo L('menu_parent');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="parent_id" id="J_parent_id">
                        
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('menu_type');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 150px" name="action">
                        <option value="url" <?php if($info['action'] == 'url'): ?>selected<?php endif; ?>><?php echo L('link');?></option>
                        <option value="click" <?php if($info['action'] == 'click'): ?>selected<?php endif; ?>><?php echo L('click');?></option>
                        <!--<option value="media" <?php if($info['action'] == 'media'): ?>selected<?php endif; ?>><?php echo L('news');?></option>-->
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('menu_param');?> :</th>
                <td>
                    <input class="input-text" type="text" name="action_param" value="<?php echo ($info["action_param"]); ?>" size="27"> <font color="red">若是url用http开头</font>
                </td>
            </tr>
            <tr>
                <th><?php echo L('menu_status');?> :</th>
                <td>
                    <select style="clear:both;position: relative;width: 80px" name="status">
                        <option value="1" <?php if($info['status'] == 1): ?>selected<?php endif; ?>><?php echo L('open');?></option>
                        <option value="0" <?php if($info && $info['status'] == 0): ?>selected<?php endif; ?>><?php echo L('close');?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('menu_sort');?> :</th>
                <td><input type="text" name="sort" value="<?php echo ($info["sort"]); ?>" class="input-text"></td>
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
            $("#J_menu_name").formValidator({ onshow:lang.menu_name, onfocus:lang.menu_name}).inputValidator({ min:2, onerror:lang.menu_name});

            $('#info_form').ajaxForm({success:complate,dataType:'json'});
            function complate(result){
                if(result.status == 1){
                    $.dialog.get('menu_'+result.dialog).close();
                    $.TP.tip({content:result.msg});
                    window.location.reload();
                } else {
                    $.TP.tip({content:result.msg, icon:'alert'});
                }
            }
            var level = $('#level').val();
            var parent_id = "<?php echo ($info["parent_id"]); ?>";
            if (level==2) {
                is_parent(level,parent_id);
            }
        });

        function is_parent(val,parent_id) {
            var site_id = $('#J_site_id').val();
            $.ajax({
                type: "GET",
                url: "<?php echo U('Wechat/getTopMenu');?>",
                data: {site_id:site_id},
                dataType: "json",
                success: function(data){
                    $('#J_parent_id').empty();
                    var chose = "<?php echo L('chose');?>";
                    var html = '<option value="">'+ chose +'</option>';
                    if (data.rows) {
                        $.each(data.rows, function(commentIndex, comment){
                            if (parent_id==comment['id']) {
                                html += '<option value="'+comment['id']+'" selected>'+comment['menu_name']+'</option>';
                            } else {
                                html += '<option value="'+comment['id']+'">'+comment['menu_name']+'</option>';
                            }
                        });
                    }
                    $('#J_parent_id').html(html);
                }
            });
            if (val == 2) {
                $('.parent').show();
            } else {
                $('.parent').hide();
            }
        }
</script>