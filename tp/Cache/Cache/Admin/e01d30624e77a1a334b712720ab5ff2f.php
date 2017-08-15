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
	var URL = '/index.php/Admin/Player';
	var SELF = '/index.php?m=&c=Player&a=index&menuid=16';
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
        <a href="<?php echo U($val['module_name'].'/'.$val['action_name'],array('menuid'=>$menuid)); echo ($val["data"]); ?>" class="<?php echo ($val["class"]); ?>"><em><?php echo L($val['name']);?></em></a><?php endforeach; endif; else: echo "" ;endif; endif; ?>
    </div>
</div><?php endif; ?>
<div class="pad_lr_10">

    <form name="searchform" id="search_form">
        <table width="100%" cellspacing="0" class="search_form">
            <tbody>
                <tr>
                    <td>
                        <div class="explain_col">
                            &nbsp;&nbsp;<?php echo L('choose_game');?> :
                            <select class="searCom" name="platform_id" id="PlatformID">
			    </select>
                            &nbsp;&nbsp;<?php echo L('choose_server');?>:
                            <select name="server_id" id="ServerID" style="width:155px;"></select>
                            &nbsp;&nbsp;
                            <select id="player_type" name="player_type">
                                <option value="role_name"><?php echo L('role_name');?></option>
                                <option value="role_id"><?php echo L('role_id');?></option>
                            </select>
                            <input type="text" name="player_info" id="player_info"  style="width:155px;height:20px;">
                            <input type="button" onclick="dofind();" class="btn" value="<?php echo L('search');?>" />
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
                    <th data-options="field:'account_id',width:80,align:'center'"><?php echo L('account_id');?></th>
                    <th data-options="field:'name',width:80,align:'center'"><?php echo L('role_name');?></th>
                    <th data-options="field:'player_id',width:80,align:'center'"><?php echo L('role_id');?></th>
                    <th data-options="field:'lv',width:80,align:'center'"><?php echo L('level');?></th>
                    <th data-options="field:'guild_name',width:80,align:'center'"><?php echo L('guild');?></th>
                    <th data-options="field:'gold',width:80,align:'center'"><?php echo L('gold');?></th>
                    <th data-options="field:'money',width:80,align:'center'"><?php echo L('money');?></th>
                    <th data-options="field:'fighting',width:80,align:'center'"><?php echo L('fighting');?></th>
                    <th data-options="field:'vip',width:80,align:'center'"><?php echo L('vip_level');?></th>
                    <th data-options="field:'pvp_ranking',width:80,align:'center'"><?php echo L('pvp_ranking');?></th>
                    <th data-options="field:'create_time',width:150,align:'center'"><?php echo L('create_time');?></th>
                    <th data-options="field:'logout_time',width:150,align:'center'"><?php echo L('last_login_time');?></th>
                    <th data-options="field:'limit_chat',width:80,align:'center'"><?php echo L('limit_chat');?></th>
                    <th data-options="field:'limit_login',width:80,align:'center'"><?php echo L('limit_login');?></th>
                    <th data-options="field:'people',width:80,align:'center'"><?php echo L('people');?></th>
                    <th data-options="field:'opt',width:250,align:'center',formatter:formatOper"><?php echo L('opt');?></th>
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
    $("#PlatformID").linkage({ objects:['PlatformID','ServerID'], showAll:[true,true],all:'<?php echo L("showall");?>' , selected:{ platformlist:"<?php echo ($search['platform_id']); ?>",serverlist:"<?php echo ($search['server_id']); ?>"}});
    $('#scl_tt2').datagrid({
            singleSelect:true,
            nowrap:true,
            fitColumns:true,
            url:'<?php echo U("Player/index");?>'
        });
});

function formatOper(val,row,index){
    var html = '<a href="javascript:;" onclick="showDialog(this);"  data-uri="index.php?m=Admin&c=Player&a=building&platform_id='+row.platform_id+'&server_id='+row.server_id+'&role_id='+row.player_id+'" data-title="'+row.name+'"  data-id="edit" data-width="800" data-height="210"><?php echo L('view_building');?></a> | ';
    html += '<a href="javascript:;" onclick="showDialog(this);"  data-uri="index.php?m=Admin&c=Player&a=equipment&platform_id='+row.platform_id+'&server_id='+row.server_id+'&role_id='+row.player_id+'" data-title="'+row.name+'"  data-id="edit" data-width="800" data-height="210"><?php echo L('view_equipment');?></a> | ';
    html += '<a href="javascript:;" onclick="showDialog(this);"  data-uri="index.php?m=Admin&c=Player&a=captain&platform_id='+row.platform_id+'&server_id='+row.server_id+'&role_id='+row.player_id+'" data-title="'+row.name+'"  data-id="edit" data-width="800" data-height="210"><?php echo L('view_captain');?></a> | ';
    html += '<a href="javascript:;" onclick="showDialog(this);"  data-uri="index.php?m=Admin&c=Player&a=mail&platform_id='+row.platform_id+'&server_id='+row.server_id+'&role_id='+row.player_id+'" data-title="'+row.name+'"  data-id="edit" data-width="800" data-height="210"><?php echo L('view_mail');?></a>';
    return html;
}

function dofind() {
        var platform_id = $('#PlatformID').val(),
        server_id = $('#ServerID').val(),
        player_type = $('#player_type').val(),
        player_info = $('#player_info').val();
        $('#scl_tt2').datagrid("reload", {
            platform_id: platform_id,
            server_id:server_id,
            player_type:player_type,
            player_info:player_info
        });
}
</script>
</body>
</html>