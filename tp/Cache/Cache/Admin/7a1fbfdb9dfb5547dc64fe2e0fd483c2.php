<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content" style="margin: 20px">
    <form name="info_form" id="info_form" action="<?php echo U('Goods/add_property');?>"  method="post">
        <table width="100%" class="table_form">
            <tr>
                <th><?php echo L('property_type');?> :</th>&nbsp;
                <td>
                    <select onchange="getSelectData(this);">
                        <option value=""><?php echo L('please_select');?></option>
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <input type="hidden" id="attachment_code">
                    <input type="hidden" id="attachment_name">
                </td>
            </tr>
            <tr>
                <th><?php echo L('property_num');?> :</th>&nbsp;
                <td><input type="text" name="attachment_num" id="attachment_num" class="input-text"></td>
            </tr>
        </table>
    </form>
</div>

<script type="text/javascript">

    function getSelectData(obj){
        var txt=$(obj).find("option:selected").text();
        var val=$(obj).find("option:selected").val();
        $('#attachment_code').val(val);
        $('#attachment_name').val(txt);
    }

    function delectAtt(id){
        $('#tr_'+id).remove();
    }

    $(function(){
        $('#info_form').ajaxForm({success:complate,dataType:'json'});
        function complate(result){
            if(result.status == 1){
                var attachment_code = $('#attachment_code').val();
                var attachment_name = $('#attachment_name').val();
                var attachment_num = $('#attachment_num').val();
                var str = '<tr id="tr_'+attachment_code+'"><td align="center"><input type="hidden" name="attachment_code[]" value="'+attachment_code+'" /><input type="hidden" name="attachment_name[]" value="'+attachment_name+'" />'+attachment_name+'</td><td align="center"><input name="attachment_num[]" class="input-text" value="'+attachment_num+'" style="width: 50px" /></td><input name="attachment_type[]" type="hidden" value="property" style="width: 50px" /><td align="center"><a href="javascript:;" onclick="delectAtt('+attachment_code+');">删除</a></td></tr>';
                $('#attachment').append(str);
                $.dialog.get('add_property').close();
            } else {
                $.TP.tip({content:result.msg, icon:'alert'});
            }
        }
    })
</script>