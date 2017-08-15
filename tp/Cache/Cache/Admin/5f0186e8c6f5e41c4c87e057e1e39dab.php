<?php if (!defined('THINK_PATH')) exit();?><div class="dialog_content" style="margin: 20px">
    <form name="info_form" id="info_form" action="<?php echo U('Goods/add_monster');?>"  method="post">
        <table width="100%" class="table_form">
            <tr>
                <th><?php echo L('monster_type');?> :</th>&nbsp;
                <td>
                    <select onchange="getSelectData(this);">
                        <option value=""><?php echo L('please_select');?></option>
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <input type="hidden" id="monster_code">
                    <input type="hidden" id="monster_name">
                </td>
            </tr>
        </table>
    </form>
</div>

<script type="text/javascript">

    function getSelectData(obj){
        var txt=$(obj).find("option:selected").text();
        var val=$(obj).find("option:selected").val();
        $('#monster_code').val(val);
        $('#monster_name').val(txt);
    }

    function delectAtt(id){
        $('#tr_'+id).remove();
    }

    $(function(){
        $('#info_form').ajaxForm({success:complate,dataType:'json'});
        function complate(result){
            if(result.status == 1){
                var monster_code = $('#monster_code').val();
                var monster_name = $('#monster_name').val();
                var str = '<tr id="tr_'+monster_code+'"><td align="center"><input type="hidden" name="attachment_code[]" value="'+monster_code+'" /><input type="hidden" name="attachment_name[]" value="'+monster_name+'" />'+monster_name+'</td><input name="attachment_num[]" type="hidden" value="1" style="width: 50px" /><input name="attachment_type[]" type="hidden" value="monster" style="width: 50px" /><td align="center"><a href="javascript:;" onclick="delectAtt('+monster_code+');">删除</a></td></tr>';
                $('#monster_list').append(str);
                $.dialog.get('add_monster').close();
            } else {
                $.TP.tip({content:result.msg, icon:'alert'});
            }
        }
    })
</script>