<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <script src="/Public/js/jquery/jquery.js"></script>
	<link href="/Public/css/admin/style.css" rel="stylesheet"/>
        <link href="/Public/css/themes/default/easyui.css" rel="stylesheet"/>
        <link href="/Public/css/themes/icon.css" rel="stylesheet"/>
        <script src="/Public/js/jquery.easyui.min.js"></script>
	<title><?php echo L('website_manage');?></title>
	<script>
	var URL = '/index.php/Admin/Land';
	var SELF = 'http://admin.leishenhuyu.com/index.php?m=&c=Land&a=stat&menuid=42';
	var ROOT_PATH = '';
	var APP	 =	 '/index.php';
	//语言项目
	var lang = new Object();
	<?php $_result=L('js_lang');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
	</script>
</head>

<body>
<div id="J_ajax_loading" class="ajax_loading"><?php echo L('ajax_loading');?></div>
<?php if(($sub_menu != '') OR ($big_menu != '')): ?><div class="subnav">
    <div class="content_menu ib_a blue line_x">
    	<?php if(!empty($big_menu)): if(is_array($big_menu)): $i = 0; $__LIST__ = $big_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a class="add fb J_showdialog" href="javascript:void(0);" data-uri="<?php echo U($vo['module_name'].'/'.$vo['action_name'],array('menuid'=>$vo['id']));?>" data-title="<?php echo ($vo["name"]); ?>" data-id="<?php echo ($vo["action_name"]); ?>" data-width="<?php echo ($vo["iframeWidth"]); ?>" data-height="<?php echo ($vo["iframeHeight"]); ?>" data-other="<?php echo ($vo["data"]); ?>"><em><?php echo ($vo["name"]); ?></em></a>&nbsp;&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; endif; ?>
        <?php if(!empty($sub_menu)): if(is_array($sub_menu)): $key = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($key % 2 );++$key; if($key != 1): ?><span>|</span><?php endif; ?>
        <a class="add fb" href="<?php echo U($val['module_name'].'/'.$val['action_name'],array('menuid'=>$menuid)); echo ($val["data"]); ?>" class="<?php echo ($val["class"]); ?>"><em><?php echo L($val['name']);?></em></a><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </div>
</div><?php endif; ?>
<script src="/Public/js/MyDate/WdatePicker.js"></script>
<div class="pad_lr_10">

    <form name="searchform" id="search_form">
        <table width="100%" cellspacing="0" class="search_form">
            <tbody>
                <tr>
                    <td>
                        <div class="explain_col">
                            <input name="p_start" id="p_start"  onfocus="WdatePicker({dateFmt:'yyyy-MM-dd' })" value="<?php echo date('Y-m-d',strtotime('-1 day')); ?>" class="Wdate" /> -
                            <input name="p_end" id="p_end" onfocus="WdatePicker({dateFmt:'yyyy-MM-dd' })" value="<?php echo date('Y-m-d',time()); ?>" class="Wdate" />
                            <select id="game_id" name="game_id">
                                <option value="0"><?php echo L('game_name');?></option>
                                <?php if(is_array($game_list)): $i = 0; $__LIST__ = $game_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["game_id"]); ?>" <?php if($vo['game_id'] == $search['game_id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["game_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <select id="channel_id" name="channel_id">
                                <option value="0"><?php echo L('channel');?></option>
                                <?php if(is_array($channel_list)): $i = 0; $__LIST__ = $channel_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["channel_id"]); ?>" <?php if($vo['channel_id'] == $search['channel_id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["channel_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <input type="button" onclick="dofind();" class="btn" value="<?php echo L('search');?>" />
                            <a class="btn J_confirmurl" href="javascript:void(0);" data-uri="<?php echo U('Land/export');?>" data-title="<?php echo L('export');?>" data-id="export" data-width="450" data-height="200" data-msg="确认导出吗"><?php echo L('export');?></a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <div class="J_tablelist table_list">
        <table width="100%" cellspacing="0" id="scl_tt2" style="height:500px;">
            <thead>
                <tr>
                    <th data-options="field:'stat_date',width:100,align:'center'"><?php echo L('stat_time');?></th>
                    <th data-options="field:'land_url',width:150,align:'center'"><?php echo L('land_url');?></th>
                    <th data-options="field:'game_name',width:80,align:'center'"><?php echo L('game_name');?></th>
                    <th data-options="field:'land_title',width:100,align:'center'"><?php echo L('land_title');?></th>
                    <th data-options="field:'visit_num',width:50,align:'center'"><?php echo L('visit_num');?></th>
                    <th data-options="field:'visit_num_ip',width:50,align:'center'"><?php echo L('visit_num_ip');?></th>
                    <th data-options="field:'down_num',width:50,align:'center'"><?php echo L('down_num');?></th>
                    <th data-options="field:'down_num_ip',width:50,align:'center'"><?php echo L('down_num_ip');?></th>
                </tr>
            </thead>
        </table>
    </div>
    <div class="btn_wrap_fixed">
    </div>
</div>
<script src="/Public/js/jquery/plugins/jquery.tools.min.js"></script>
<script src="/Public/js/jquery/plugins/formvalidator.js"></script>
<script src="/Public/js/my.js"></script>
<script src="/Public/js/admin.js"></script>
<script src="/Public/js/swfupload.js"></script>
<script>
    //初始化弹窗
    (function (d) {
        d['okValue'] = lang.dialog_ok;
        d['cancelValue'] = lang.dialog_cancel;
        d['title'] = lang.dialog_title;
    })($.dialog.defaults);

    //全选反选服务器
    $('.check_all_server').live('click', function(){
        $('.check_server').attr('checked', this.checked);
        $('.check_all_server').attr('checked', this.checked);
        var ids = '';
        $('.check_server:checked').each(function(){
            ids +=  ',' + $(this).val() ;
        });
        $('#server_ids').val(ids);
    });

    $('.check_server').live('click', function(){
        var ids = '';
        $('.check_server:checked').each(function(){
            ids +=  ',' + $(this).val() ;
        });
        $('#server_ids').val(ids);
    });

    function confirmUrl(obj){
        var self = $(obj),
        uri = self.attr('data-uri'),
        acttype = self.attr('data-acttype'),
        title = (self.attr('data-title') != undefined) ? self.attr('data-title') : lang.confirm_title,
        msg = self.attr('data-msg'),
        callback = self.attr('data-callback');
        $.dialog({
            title:title,
            content:msg,
            padding:'10px 20px',
            lock:true,
            ok:function(){
                if(acttype == 'ajax'){
                    $.getJSON(uri, function(result){
                        if(result.status == 1){
                            $.TP.tip({content:result.msg});
                            if(callback != undefined){
                                eval(callback+'(self)');
                            }else{
                                window.location.reload();
                            }
                        }else{
                            $.TP.tip({content:result.msg, icon:'error'});
                        }
                    });
                }else{
                    location.href = uri;
                }
            },
            cancel:function(){}
        });
    }

    function showDialog(obj){
        var self = $(obj),
        dtitle = self.attr('data-title'),
        did = self.attr('data-id'),
        duri = self.attr('data-uri'),
        dwidth = parseInt(self.attr('data-width')),
        dheight = parseInt(self.attr('data-height')),
        dpadding = (self.attr('data-padding') != undefined) ? self.attr('data-padding') : '',
        dcallback = self.attr('data-callback');
        var other = "",ids="";
        if(self.attr('data-other')) {
            other = self.attr('data-other');
        }
        if(other == "checkbox") {
            $('.J_checkitem:checked').each(function(){
                ids += $(this).val() + ',';
            });
            if(ids == "") {
                $.TP.tip({
                    content:lang.plsease_select_rows,
                    icon:'alert'
                });
                return false;
            }
        }else{
            duri+=other;
        }
        $.dialog({
            id:did
        }).close();
        $.dialog({
            id:did,
            title:dtitle,
            width:dwidth ? dwidth : 'auto',
            height:dheight ? dheight : 'auto',
            padding:dpadding,
            lock:true,
            ok:function(){
                var info_form = this.dom.content.find('#info_form');
                if(info_form[0] != undefined){
                    info_form.submit();
                    if(dcallback != undefined){
                        eval(dcallback+'()');
                    }
                    return false;
                }
                if(dcallback != undefined){
                    eval(dcallback+'()');
                }
            },
            cancel:function(){}
        });
        $.getJSON(duri, function(result){
            if(result.status == 1){
                $.dialog.get(did).content(result.data);
            }
        });
        return false;
    }
</script>

<?php if(isset($list_table)): ?><script src="/Public/js/jquery/plugins/listTable.js"></script>
    <script>
        $(function(){
            $('.J_tablelist').listTable();
        });
    </script><?php endif; ?>
<script src="/Public/js/option-change.js"></script>
<script type="text/javascript">
$(function(){
    $('#scl_tt2').datagrid({
            singleSelect:true,
            nowrap:true,
            url:'<?php echo U("Land/stat");?>',
            fitColumns:true,
            nowrap:true,
            pagination:true,  //分页
            pageSize: 20,
            pageList: [20,30,50]
        });
});

function dofind() {
        var p_start = $('#p_start').val(),
        p_end = $('#p_end').val(),
        game_id = $('#game_id').val(),
        channel_id = $('#channel_id').val();
        $('#scl_tt2').datagrid("reload", {
            p_start : p_start,
            p_end : p_end,
            game_id : game_id,
            channel_id : channel_id
        });
}

function doexport() {
    
}
</script>
</body>
</html>