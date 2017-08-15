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
	var SELF = 'http://admin.leishenhuyu.com/index.php?m=&c=Land&a=add&menuid=38';
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
            <form id="info_form" action="<?php echo U("Land/$action_name");?>" method="post">
                <input type="hidden" name="menuid" value="<?php echo ($menuid); ?>" />
                <table width="100%" class="table_form">
                    <tr>
                        <th width="80"><?php echo L('game_name');?> :</th>
                        <td>
                            <select style="clear:both;position: relative;width: 150px" name="game_id" id="J_game_id" <?php if($info['game_id']): ?>disabled<?php endif; ?>>
                                <option value="0"><?php echo L('chose_game');?></option>
                                <?php if(is_array($game_list)): $i = 0; $__LIST__ = $game_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["game_id"]); ?>" <?php if($val['game_id'] == $info['game_id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["game_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('channel');?> :</th>
                        <td>
                            <select style="clear:both;position: relative;width: 150px" name="channel_id" id="J_channel_id">
                                <option value="0"><?php echo L('chose_channel');?></option>
                                <?php if(is_array($channel_list)): $i = 0; $__LIST__ = $channel_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["channel_id"]); ?>" <?php if($val['channel_id'] == $info['channel_id']): ?>selected="selected"<?php endif; ?>><?php echo ($val["channel_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('land_title');?> :</th>
                        <td><input type="text" name="land_title" class="input-text" id="J_land_title" value="<?php echo ($info["land_title"]); ?>" size="30"></td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('title');?> :</th>
                        <td><input type="text" name="title" class="input-text" id="J_title" value="<?php echo ($info["title"]); ?>" size="30"></td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('is_default');?> :</th>
                        <td>
                            <select style="clear:both;position: relative;width: 150px" name="is_default">
                                <option value="0" <?php if($info['is_default'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L('no');?></option>
                                <option value="1" <?php if($info['is_default'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L('yes');?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('theme');?> :</th>
                        <td>
                            <select style="clear:both;position: relative;width: 150px" name="theme" onchange="select_theme(this.value)" id="J_theme">
                                <option value="0"><?php echo L('chose_theme');?></option>
                                <?php if(is_array($theme_list)): $i = 0; $__LIST__ = $theme_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><option value="<?php echo ($val["theme_id"]); ?>" <?php if($val['theme_id'] == $info['theme']): ?>selected="selected"<?php endif; ?>><?php echo ($val["theme_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                            <input type="text" name="material" id="material" class="input-text" value="" onblur="set_material(this.value)" size="5" style="display: none;">
                        </td>
                    </tr>
                    <tr id='images' style="display: none;">
                        <th width="80"><?php echo L('material');?> :</th>
                        <td>
                            
                        </td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('ios_down_url');?> :</th>
                        <td><input type="text" name="ios_down_url" class="input-text" id="J_ios_down_url" value="<?php echo ($info["ios_down_url"]); ?>" size="30"></td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('android_down_url');?> :</th>
                        <td><input type="text" name="android_down_url" class="input-text" id="J_android_down_url" value="<?php echo ($info["android_down_url"]); ?>" size="30"></td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('bottom_info');?> :</th>
                        <td><input type="text" name="bottom_info" class="input-text" id="J_bottom_info" value="<?php echo ($info["bottom_info"]); ?>" size="30"></td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('click_type');?> :</th>
                        <td>
                            <select style="clear:both;position: relative;width: 150px" name="click_type">
                                <option value="1" <?php if($info['click_type'] == 1): ?>selected="selected"<?php endif; ?>><?php echo L('button');?></option>
                                <option value="2" <?php if($info['click_type'] == 2): ?>selected="selected"<?php endif; ?>><?php echo L('all_link');?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('auto_jump');?> :</th>
                        <td>
                            <select style="clear:both;position: relative;width: 150px" name="auto_jump">
                                <option value="0" <?php if($info['auto_jump'] == 0): ?>selected="selected"<?php endif; ?>><?php echo L('no_auto');?></option>
                                <option value="3" <?php if($info['auto_jump'] == 3): ?>selected="selected"<?php endif; ?>>3s</option>
                                <option value="5" <?php if($info['auto_jump'] == 5): ?>selected="selected"<?php endif; ?>>5s</option>
                                <option value="10" <?php if($info['auto_jump'] == 10): ?>selected="selected"<?php endif; ?>>10s</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('stat_js');?> :</th>
                        <td><textarea id="stat_js" name="stat_js" rows='8' cols="30"><?php echo ($info["stat_js"]); ?></textarea></td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('batch');?> :</th>
                        <td>
                            <input type="text" name="batch" class="input-text" id="J_batch" value="<?php echo ($info["batch"]); ?>" <?php if($info['id']): ?>disabled<?php endif; ?> size="10">
                        </td>
                    </tr>
                    <tr>
                        <th width="80"><?php echo L('host');?> :</th>
                        <td>
                            <select style="clear:both;position: relative;width: 150px" name="host">
                                <option value="leishenhuyu.com" <?php if($info['host'] == 'leishenhuyu.com'): ?>selected="selected"<?php endif; ?>>leishenhuyu.com</option>
                                <option value="qitiangame.com" <?php if($info['host'] == 'qitiangame.com'): ?>selected="selected"<?php endif; ?>>qitiangame.com</option>
                                <option value="leishengame.com" <?php if($info['host'] == 'leishengame.com'): ?>selected="selected"<?php endif; ?>>leishengame.com</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn" value="<?php echo L('submit');?>">
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="id" value="<?php echo ($info["id"]); ?>" />
            </form>
        </div>
    </div>
</div>
<script>
        <?php $_result=L('js_land');if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?>lang.<?php echo ($key); ?> = "<?php echo ($val); ?>";<?php endforeach; endif; else: echo "" ;endif; ?>
        $(function(){
            $.formValidator.initConfig({formid:"info_form",autotip:true});
            $("#J_game_id").formValidator({ onshow:lang.game_id, onfocus:lang.game_id}).inputValidator({ min:1, onerror:lang.game_id});
            $("#J_channel_id").formValidator({ onshow:lang.channel_id, onfocus:lang.channel_id}).inputValidator({ min:1, onerror:lang.channel_id});
            $("#J_theme").formValidator({ onshow:lang.theme, onfocus:lang.theme}).inputValidator({ min:1, onerror:lang.theme});
            $("#J_land_title").formValidator({ onshow:lang.land_title, onfocus:lang.land_title}).inputValidator({ min:2, max:200, onerror:lang.land_title});
            
            var theme = "<?php echo ($info["theme"]); ?>";
            var land_id = "<?php echo ($info["id"]); ?>";
            if (theme) {
                select_theme(theme,land_id);
            }
        });
        
        function select_theme(theme,land_id=0) {
            if (theme!=0) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('Theme/getTheme');?>",
                    data: "theme_id="+theme+"&land_id="+land_id,
                    success: function(result){
                        if (result.status==2) {
                            $('#material').val(result.msg).show();
                            $('#images').hide();
                            if (land_id!=0) {
                                var material = "<?php echo ($info["material"]); ?>";
                                set_material(material,land_id);
                            }
                        } else {
                            $('#material').hide();
                            $('#images td').empty().append(result.data);
                            $('#images').show();
                        }
                    }
                });
            } else {
                $('#material').hide();
                $('#images').hide();
            }
        }
        
        function set_material(material_num,land_id=0) {
            if (material_num!=0) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('Theme/getTheme');?>",
                    data: "material_num="+material_num+"&land_id="+land_id,
                    success: function(result){
                        $('#images td').empty().append(result.data);
                        $('#images').show();
                    }
                });
            }
        }
        
        function getMaterial(material_id,id) {
            if (material_id!='') {
                $.ajax({
                    type: "POST",
                    url: "<?php echo U('Material/getMaterial');?>",
                    data: "material_id="+material_id,
                    success: function(result){
                        $('#material_'+id).attr('src',result.data);
                    }
                });
            } else {
                $('#material_'+id).attr('src','');
            }
        }

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