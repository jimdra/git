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
	var URL = '/tp/index.php/Admin/Wechat';
	var SELF = '/tp/index.php?m=&c=Wechat&a=menuList&menuid=48';
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
<!--站点管理-->
<div class="pad_lr_10">
    <form name="searchform" method="get" >
        <table width="100%" cellspacing="0" class="search_form">
            <tbody>
                <tr>
                    <td>
                        <div class="explain_col">
                            <input type="hidden" name="m" value="Admin" />
                            <input type="hidden" name="c" value="Wechat" />
                            <input type="hidden" name="a" value="menuList" />
                            <input type="hidden" name="menuid" value="<?php echo ($menuid); ?>" />
                            &nbsp;&nbsp;<?php echo L('site_name');?> :
                            <select id="site_id" name="site_id">
                                <option value="0"><?php echo L('chose_site');?></option>
                                <?php if(is_array($site_list)): $i = 0; $__LIST__ = $site_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["site_id"]); ?>" <?php if($vo['site_id'] == $search['site_id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["site_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <input type="submit" name="search" class="btn" value="<?php echo L('search');?>" />
                            <input type="button" name="create" class="btn" onclick="createWxMenu()" value="<?php echo L('createWxMenu');?>" />
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <div class="textbox">
        <div class="picbox" role="table">
            <div class="J_tablelist table_list">
                <table width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="40">ID</th>
                            <th><?php echo L('top_menu_name');?></th>
                            <th><?php echo L('sub_menu_name');?></th>
                            <th><?php echo L('menu_type');?></th>
                            <th width="800"><?php echo L('menu_param');?></th>
                            <th><?php echo L('menu_sort');?></th>
                            <th><?php echo L('menu_status');?></th>
                            <th width=100><?php echo L('operations_manage');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><tr>
                            <td align="center"><?php echo ($val["id"]); ?></td>
                            <td align="center"><?php echo ($val["menu_name"]); ?></td>
                            <td align="center"></td>
                            <td align="center"><?php if($val["child"] == ''): echo ($val["action"]); endif; ?></td>
                            <td align="center"><?php if($val["child"] == ''): echo ($val["action_param"]); endif; ?></td>
                            <td align="center"><?php echo ($val["sort"]); ?></td>
                            <td align="center"><?php if($val["status"] == 1): echo L('open'); else: echo L('close'); endif; ?></td>
                            <td align="center">
                                <a href="javascript:;" class="J_showdialog" data-uri="<?php echo U('Wechat/menu_edit',array('id'=>$val['id']));?>" data-title="<?php echo L('edit');?> - <?php echo ($val['menu_name']); ?>" data-id="menu_edit" data-width="500" data-height="350"><?php echo L('edit');?></a> |
                                <a href="javascript:;" class="J_confirmurl" data-uri="<?php echo U('Wechat/menu_delete', array('id'=>$val['id']));?>" data-msg="<?php echo sprintf(L('confirm_delete_one'),$val['menu_name']);?>"><?php echo L('delete');?></a>
                            </td>
                        </tr>
                        <?php if($val.child): if(is_array($val["child"])): $i = 0; $__LIST__ = $val["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                            <td align="center"><?php echo ($vo["id"]); ?></td>
                            <td align="center"></td>
                            <td align="center"><?php echo ($vo["menu_name"]); ?></td>
                            <td align="center"><?php echo ($vo["action"]); ?></td>
                            <td align="center"><?php echo ($vo["action_param"]); ?></td>
                            <td align="center"><?php echo ($vo["sort"]); ?></td>
                            <td align="center"><?php if($vo["status"] == 1): echo L('open'); else: echo L('close'); endif; ?></td>
                            <td align="center">
                                <a href="javascript:;" class="J_showdialog" data-uri="<?php echo U('Wechat/menu_edit',array('id'=>$vo['id']));?>" data-title="<?php echo L('edit');?> - <?php echo ($vo['menu_name']); ?>" data-id="menu_edit" data-width="500" data-height="350"><?php echo L('edit');?></a> |
                                <a href="javascript:;" class="J_confirmurl" data-uri="<?php echo U('Wechat/menu_delete', array('id'=>$vo['id']));?>" data-msg="<?php echo sprintf(L('confirm_delete_one'),$vo['menu_name']);?>"><?php echo L('delete');?></a>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!--<div class="btn_wrap_fixed">
        <label class="select_all mr10"><input type="checkbox" name="checkall" class="J_checkall"><?php echo L('select_all');?>/<?php echo L('cancel');?></label>
        <input type="button" class="btn" data-tdtype="batch_action" data-acttype="ajax" data-uri="<?php echo U('Wechat/delete');?>" data-name="id" data-msg="<?php echo L('confirm_delete');?>" value="<?php echo L('delete');?>" />
        <div id="pages"><?php echo ($page); ?></div>
    </div>-->
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
<script type="text/javascript">
function createWxMenu() {
    if (!confirm('慎重！确定要重新创建微信菜单？')) return;
    var site_id = $("#site_id").val();
    if (site_id==0) {
        alert('请先选择站点');
        return;
    }
    $.ajax({
        type: "GET",
        url: "<?php echo U('Wechat/create');?>",
        data: {site_id:site_id},
        dataType: "text",
        success: function(msg){
            if(msg == 'ok'){
                alert('更新成功！！');
            } else {
                alert(msg);
            }
        }
    });
}
</script>
</body>
</html>