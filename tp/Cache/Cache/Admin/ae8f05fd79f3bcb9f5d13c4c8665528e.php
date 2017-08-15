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
	var URL = '/index.php/Admin/Goods';
	var SELF = '/index.php?m=&c=Goods&a=full_service&menuid=28';
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
<script src="/Public/js/MyDate/WdatePicker.js"></script>
<div class="dialog_content" style="margin: 20px">
    <form name="info_form" id="info_form" action="<?php echo U('Goods/full_service');?>" method="post" onsubmit="return check();">
        <table width="100%" class="table_form">
            <tr>
                <th width="80"><?php echo L('choose_game');?> :</th>
                <td>
                    <select class="searCom" name="platform_id" id="PlatformID" onchange="changePlatfrom(this.value);"></select>
                </td>
            </tr>
            <tr>
                <th><?php echo L('choose_server');?> :</th>
                <td>
                    <input type="hidden" name="server_ids" id="server_ids" />
                    <div id="server_list" style=" float: left;width: 500px;"></div>
                </td>
            </tr>
            <tr>
                <th><?php echo L('mail_title');?> :</th>
                <td><input name="title" id="title" class="input-text"  style="width: 300px" /></td>
            </tr>
            <tr>
                <th><?php echo L('mail_content');?> :</th>
                <td><textarea name="content" id="content" cols="55" rows="5"></textarea></td>
            </tr>
            <tr>
                <th><?php echo L('attachment');?> :</th>
                <td style=" width: 150px">
                    <table class="table_list">
                        <thead>
                            <tr>
                                <th style="text-align: center;" width="80">类型</th>
                                <th style="text-align: center;" width="80">数量</th>
                                <th style="text-align: center;" width=80>操作</th>
                            </tr>
                        </thead>
                        <tbody id="attachment">
                            <tr>
                                <td align="center"><input type="hidden" name="attachment_code[]" value="gold" /><input name="attachment_type[]" type="hidden" value="property" style="width: 50px" /><input type="hidden" name="attachment_name[]" value="钻石" />钻石</td>
                                <td align="center"><input name="attachment_num[]" class="input-text" value="0" style="width: 50px" /></td>
                                <td align="center"></td>
                            </tr>
                            <tr>
                                <td align="center"><input type="hidden" name="attachment_code[]" value="money" /><input name="attachment_type[]" type="hidden" value="property" style="width: 50px" /><input type="hidden" name="attachment_name[]" value="金币" />金币</td>
                                <td align="center"><input name="attachment_num[]" class="input-text" value="0" style="width: 50px" /></td>
                                <td align="center"></td>
                            </tr>
                            <tr>
                                <td align="center"><input type="hidden" name="attachment_code[]" value="vipexp" /><input name="attachment_type[]" type="hidden" value="property" style="width: 50px" /><input type="hidden" name="attachment_name[]" value="vip经验" />vip经验</td>
                                <td align="center"><input name="attachment_num[]" class="input-text" value="0" style="width: 50px" /></td>
                                <td align="center"></td>
                            </tr>
                        </tbody>
                    </table>
                    <a href="javascript:;" onclick="showDialog(this);"  data-uri="index.php?m=Admin&c=Goods&a=add_property" data-title="<?php echo L('add_property');?>"  data-id="add_property" data-width="450" data-height="50"><?php echo L('add_property');?></a>
                </td>
                <td>
                    <table class="table_list">
                        <thead>
                            <tr>
                                <th style="text-align: center;" width="80">类型</th>
                                <th style="text-align: center;" width=80>操作</th>
                            </tr>
                        </thead>
                        <tbody id="monster_list">

                        </tbody>
                    </table>
                    <a href="javascript:;" onclick="showDialog(this);"  data-uri="index.php?m=Admin&c=Goods&a=add_monster" data-title="<?php echo L('add_monster');?>"  data-id="add_monster" data-width="400" data-height="30"><?php echo L('add_monster');?></a>
                </td>
            </tr>
            <tr>
                <th><?php echo L('reason');?> :</th>
                <td><textarea name="reason" id="reason" cols="55" rows="5"></textarea></td>
            </tr>
        </table>
        <br/>
        <INPUT TYPE="submit"  value="<?php echo L('submit');?>" class="btn" >
    </form>
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
    });

    function changePlatfrom(platform_id){
        $.ajax({
            type: "GET",
            url: "<?php echo U('Ajax/serverlist');?>",
            data: {pid:platform_id},
            dataType: "json",
            success: function(data){
                $('#server_list').empty();   //清空server_list里面的所有内容
                var show_all = "<?php echo L('showall');?>";
                var html = '<input class="check_all_server" type="checkbox" name="checkall"> '+ show_all +' &nbsp;&nbsp;';
                $.each(data, function(commentIndex, comment){
                    html += '<input class="check_server" type="checkbox" value="'+comment['id']+'"> ' + comment['text']+ '&nbsp;&nbsp;';
                });
                $('#server_list').html(html);
            }
        });
    }
    
    function check(){
        var platform_id = $('#PlatformID').val(),
        server_id = $('#ServerID').val(),
        title = $('#title').val(),
        content = $('#content').val();
        if(platform_id==''){
            alert('请选择游戏平台');
            return false;
        }
        if(server_id==''){
            alert('请选择游戏服务器');
            return false;
        }
        if(title==''){
            alert('标题不能为空');
            return false;
        }
        if(content==''){
            alert('内容不能为空');
            return false;
        }
        return true;
    }
</script>
</body>
</html>