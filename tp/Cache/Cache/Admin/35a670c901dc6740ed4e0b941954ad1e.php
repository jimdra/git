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
	var URL = '/index.php/Admin/Site';
	var SELF = 'http://admin.leishenhuyu.com/index.php?m=&c=Site&a=add&menuid=20';
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
<!--编辑/添加站点-->
<div class="pad_lr_10">
    <div class="textbox">
        <div class="picbox" role="table">
            <form id="info_form" action="<?php if($action_name=='add'): echo U('Site/add'); else: echo U('Site/edit'); endif; ?>" method="post">
                <table width="100%" class="table_form">
                    <tr>
                        <th width="80"><?php echo L('site_name');?> :</th>
                        <td><input type="text" name="site_name" class="input-text" id="J_site_name" value="<?php echo ($info["site_name"]); ?>" ></td>
                    </tr>
                    <tr>
                        <th><?php echo L('site_code');?> :</th>
                        <td><input type="text" name="site_code" class="input-text" id="J_site_code" value="<?php echo ($info["site_code"]); ?>" ></td>
                    </tr>
                    <tr>
                        <th><?php echo L('site_url');?> :</th>
                        <td><input type="text" name="site_url" id="J_site_url" value="<?php echo ($info["site_url"]); ?>" class="input-text"></td>
                    </tr>
                    <tr>
                        <th><?php echo L('web_url');?> :</th>
                        <td><input type="text" name="web_url" id="J_web_url" value="<?php echo ($info["web_url"]); ?>" class="input-text"></td>
                    </tr>
                    <tr>
                        <th><?php echo L('site_title');?> :</th>
                        <td><input type="text" name="site_title" id="J_site_title" value="<?php echo ($info["site_title"]); ?>" class="input-text"></td>
                    </tr>
                    <tr>
                        <th><?php echo L('site_keyword');?> :</th>
                        <td><input type="text" name="keyword" class="input-text" value="<?php echo ($info["keyword"]); ?>" size="30"></td>
                    </tr>
                    <tr>
                        <th><?php echo L('site_description');?> :</th>
                        <td><textarea class="" name="description" rows="4" cols="55"><?php echo ($info["description"]); ?></textarea></td>
                    </tr>
                    <tr>
                        <th><?php echo L('site_logo');?> :</th>
                        <td>
                            <input type="text" class="input-text " name="site_logo" id="site_logo" value="<?php echo ($info["site_logo"]); ?>" size="30">
                            <input type="button" class="btn" value="<?php echo L('uploadfiles');?>" onclick="javascript:swfupload('pic_uploadfile','site_logo','文件上传',1,0,0,1,'jpg,jpeg,gif,png',5,up_image)">
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo L('site_status');?> :</th>
                        <td>
                    <input type="radio" name="status" class="radio_style" value="1" <?php if(!$info || $info["status"] == 1): ?>checked="checked"<?php endif; ?>> &nbsp;<?php echo L('yes');?>&nbsp;&nbsp;&nbsp;
                    <input type="radio" name="status" class="radio_style" value="0" <?php if($info && $info["status"] == 0): ?>checked="checked"<?php endif; ?>> &nbsp;<?php echo L('no');?>
                    </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn" value="<?php echo L('submit');?>">
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="menuid" value="<?php echo ($menuid); ?>" />
                <input type="hidden" name="site_id" value="<?php echo ($info["site_id"]); ?>" />
            </form>
        </div>
    </div>
</div>
<script>
        <?php $_result=L('js_site');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_site_name").formValidator({ onshow:lang.site_name, onfocus:lang.site_name}).inputValidator({ min:2, max:10, onerror:lang.site_name});
            $("#J_site_code").formValidator({ onshow:lang.site_code, onfocus:lang.site_code}).inputValidator({ min:2, max:10, onerror:lang.site_code}).regexValidator({ regexp:'\\w', onerror:lang.allow_letter});
            $("#J_site_url").formValidator({ onshow:lang.site_url, onfocus:lang.site_url}).inputValidator({ min:2, onerror:lang.site_url});
            $("#J_web_url").formValidator({ empty:true, onfocus:lang.site_url}).inputValidator({ min:2, onerror:lang.site_url});

        });
</script>
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
</body>
</html>