<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
        <script src="/tp/Public/js/jquery/jquery.js"></script>
	<link href="/tp/Public/css/admin/style.css" rel="stylesheet"/>
        <link href="/tp/Public/css/themes/default/easyui.css" rel="stylesheet"/>
        <link href="/tp/Public/css/themes/icon.css" rel="stylesheet"/>
        <script src="/tp/Public/js/jquery.easyui.min.js"></script>
	<title><?php echo L('website_manage');?></title>
	<script>
	var URL = '/tp/index.php/Admin/AdminRole';
	var SELF = '/tp/index.php?m=&c=AdminRole&a=auth&id=2&menuid=12';
	var ROOT_PATH = '/tp';
	var APP	 =	 '/tp/index.php';
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
<div class="pad_10">
    <form action="<?php echo U('admin_role/auth');?>" method="post">
    <div class="J_tablelist table_list">
    <table width="100%" cellspacing="0" id="J_auth_tree">
    	<thead>
        	<tr><th align="left"><?php echo L('role_auth');?> - <?php echo ($role["name"]); ?></th></tr>
        </thead>
    	<tbody>
        	<?php echo ($list); ?>
    	</tbody>
    </table>
    <input type="hidden" name="id" value="<?php echo ($role["id"]); ?>"></input>
    <div>

    <div class="btn_wrap_fixed">
    	<label class="select_all"><input type="checkbox" name="checkall" class="J_checkall"><?php echo L('select_all');?>/<?php echo L('cancel');?></label>
        <input type="submit" class="btn" name="dosubmit" value="<?php echo L('submit');?>"/>
    </div>
    </form>
</div>
<script src="/tp/Public/js/jquery/plugins/jquery.tools.min.js"></script>
<script src="/tp/Public/js/jquery/plugins/formvalidator.js"></script>
<script src="/tp/Public/js/my.js"></script>
<script src="/tp/Public/js/admin.js"></script>
<script src="/tp/Public/js/swfupload.js"></script>
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

<?php if(isset($list_table)): ?><script src="/tp/Public/js/jquery/plugins/listTable.js"></script>
    <script>
        $(function(){
            $('.J_tablelist').listTable();
        });
    </script><?php endif; ?>
<script src="/tp/Public/js/jquery/plugins/jquery.treetable.js"></script>
<script>
$(function() {
    $("#J_auth_tree").treeTable({indent:20});

    $('.J_checkall').live('click', function(){
        $('.J_checkitem').attr('checked', this.checked);
        $('.J_checkall').attr('checked', this.checked);
    });

    $('.J_checkitem').live('click', function(){
        var chk = $("input[type='checkbox']"),
            count = chk.length,
            num = chk.index($(this)),
            level_top = level_bottom =  chk.eq(num).attr('level');
        for(var i=num; i>=0; i--){
            var le = chk.eq(i).attr('level');
            if(eval(le) < eval(level_top)){
                chk.eq(i).attr("checked", true);
                var level_top = level_top-1;
            }
        }
        for(var j=num+1; j<count; j++){
            var le = chk.eq(j).attr('level');
            if(chk.eq(num).attr("checked")) {
                if(eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", true);
                else if(eval(le) == eval(level_bottom)) break;
            }else{
                if(eval(le) > eval(level_bottom)) chk.eq(j).attr("checked", false);
                else if(eval(le) == eval(level_bottom)) break;
            }
        }
    });
});
</script>
</body>
</html>