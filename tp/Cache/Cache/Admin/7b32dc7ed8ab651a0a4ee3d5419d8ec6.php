<?php if (!defined('THINK_PATH')) exit();?><style>
    html, body, p, table{font: normal 12PX Tahoma,Verdana,Helvetica,Arial,sans-serif; }
    html{_overflow-y:scroll}
    .swfupload {position: absolute;z-index: 1;}
    .mainnav_title {display:none;}
    #tabs {margin:0px auto;overflow:hidden;}
    #tabs .title {height:29px;}
    #tabs .title ul li {float:left;height:27px;line-height:27px;border:1px solid #CFDFF0;border-bottom:0;background:#EAEEF4;padding:0px 10px;list-style-type:none;}
    #tabs .title ul li a{color: #444444}
    #tabs .title ul li.on {background:#FFF;height:28px;}
    #tabs .content_1 {border:1px solid #CFDFF0;overflow:hidden;}
    #tabs .tabbox {padding:10px;}
</style>
<link href="/Public/js/swfupload/swfupload.css" rel="stylesheet" type="text/css" />

<div id="content" style="width:600px;">
    <div id="tabs">
        <div class="title">
            <ul>
                <li class="on"><a href="javascript:void(0);"><?php echo L('upload_list_file');?></a></li>
            </ul>
            <div class="" style="margin-left:85px;">
                <select id="game_id" name="game_id">
                    <option value="0"><?php echo L('game_name');?></option>
                    <?php if(is_array($game_list)): $i = 0; $__LIST__ = $game_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["game_id"]); ?>" <?php if($vo['game_id'] == $search['game_id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["game_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                <select id="type_id" name="type_id">
                    <option value="0"><?php echo L('type_name');?></option>
                    <?php if(is_array($type_list)): $i = 0; $__LIST__ = $type_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["type_id"]); ?>" <?php if($vo['type_id'] == $search['type_id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["type_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
                <input type="button" name="search" onclick="ajaxload()" class="btn" value="<?php echo L('search');?>" />
            </div>
        </div>

        <div class="content_1">

            <div class="tabbox">
                <div id="filelist">
                    <div id="thumbnails1" ><ul class="attachment-list" style="padding:0;margin:0;">
                            <?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li><div class="img"><a href="javascript:void(0);" data="<?php echo ($vo["material_id"]); ?>" onclick="javascript:add_file(this)"><div class="icon"></div><img src="<?php echo ($vo['material_thumb']); ?>"  style="max-width:80px;_width:80px;max-height:80px;_height:80px;"></a></div></li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul></div>
                    <div id="pages" class="page"><?php echo ($page); ?></div> 
                </div>
            </div>
        </div>
    </div>
<script>
    function ajaxload(p=1){
        var game_id = $('#game_id').val();
        var type_id = $('#type_id').val();
        var data = 'game_id='+game_id+'&type_id='+type_id+'&p='+p;
        var url = "<?php echo U('Attachment/filelist');?>";
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(result){
                $('#filelist').empty().append(result.data);
            }
        });
    }
    
    function add_file(obj){
 	var src = $(obj).children("img").attr("path");
	var name = $(obj).children("img").attr("alt");
	var filesize =  $(obj).children("img").attr("imgsize"); 
	if($(obj).hasClass('on')){
            $(obj).removeClass("on");
	} else {
            $('#filelist li a').removeClass('on');
            $(obj).addClass("on");
	}
    }
</script>