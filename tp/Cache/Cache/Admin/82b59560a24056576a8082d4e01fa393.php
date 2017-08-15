<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="/Public/js/jquery/jquery.js"></script>
<style>
    html, body, p, table{font: normal 12PX Tahoma,Verdana,Helvetica,Arial,sans-serif; }
    html{_overflow-y:scroll}
    .swfupload {position: absolute;z-index: 1;}
    .mainnav_title {display:none;}
    #tabs {margin:0px auto;overflow:hidden;}
    #tabs .title {height:29px;}
    #tabs .title ul li {float:left;height:27px;line-height:27px;border:1px solid #CFDFF0;border-bottom:0;background:#EAEEF4;padding:0px 10px;list-style-type:none;}
    #tabs .title ul li a{color: #444444}
    #tabs .title ul li.on {background:#FFF;height:28px;}
    #tabs .content_1 {border:1px solid #CFDFF0;overflow:hidden;border-top:none;}
    #tabs .tabbox {display:none;padding:10px;}
</style>
<link href="/Public/js/swfupload/swfupload.css" rel="stylesheet" type="text/css" />

<div id="content" style="width:600px;">
    <div id="tabs">
        <div class="title"><ul><li class="on"><a href="javascript:void(0);"><?php echo L('upload_file');?></a></li>
                <li><a href="javascript:void(0);"><?php echo L('upload_url');?></a></li>
                <?php if(!empty($more)): ?><li><a href="javascript:void(0);" onclick="ajaxload('filelist');"><?php echo L('upload_list_file');?></a></li><?php endif; ?>
            </ul></div>

        <div class="content_1">
            <div class="tabbox" style="display:block;">
                <div id="divMovieContainer">
                    <div class="selectbut"><span id="spanButtonPlaceHolder"></span></div>
                    <input type="button" value="<?php echo L('start_upload');?>" id="uploadbut" onclick="swfu.startUpload();"/>
                    <div style="color:#959595;line-height:24px;height:24px;background:url() no-repeat;padding-left:20px;"><?php echo L('upload_say4');?> <font color="red"><?php echo ($file_types); ?></font> <?php echo L('upload_say5');?></div><br>
                    <div style="color:#454545;clear:both;line-height:24px;height:24px;"> <?php echo L('upload_say1');?> <font color="red"><?php echo ($small_upfile_limit); ?></font> <?php echo L('upload_say2');?> <font color="red"><?php echo ($file_size); ?></font>	<?php echo L('upload_say3');?></div>
                </div>


                <div id="divStatus"><?php echo L('upload_say6');?><span id="tdFilesQueued">0</span><?php echo L('upload_say7');?><span id="tdErrors">0</span> <?php echo L('upload_say8');?><span id="tdFilesUploaded">0</span> <?php echo L('upload_say9');?></div>
                <fieldset  id="swfupload-box">
                    <legend><?php echo L('file_list');?></legend>
                    <ul id="fsUploadProgress"></ul>
                    <ul class="attachment-list"  id="thumbnails"></ul>
                </fieldset>

            </div>
            <div class="tabbox">
                <br>
        	<?php echo L('upload_fileurl');?>: <input type="text" id="fileurl" name="fileurl" class="input-text" value=""  style="width:350px;"  onblur="addfileurl(this)">
                <br><br><br>

            </div>
            <div class="tabbox">
                <div id="filelist"></div>
            </div>
        </div>
    </div>

    <div  id="myuploadform" style="display:none;" ></div>
    <script type="text/javascript" src="/Public/js/swfupload/swfupload.js"></script>
<script type="text/javascript" src="/Public/js/swfupload/fileprogress.js"></script>
<script type="text/javascript" src="/Public/js/swfupload/handlers.js"></script>
<script type="text/javascript">
    $.ajaxSetup ({ cache: false });

    var file_limit = <?php echo ($file_limit); ?>;
    var swfu;
    $(function(){
        var settings = {
            flash_url : "/Public/js/swfupload/swfupload.swf?"+Math.random(),
            upload_url: "<?php echo U('Attachment/upload');?>",
            file_post_name : "filedata",
            post_params: {"PHPSESSID" : "<?php echo time();?>", "isthumb" : "<?php echo $isthumb;?>"},
            file_size_limit : "<?php echo $file_size;?> MB",
            file_types : "<?php echo $file_types;?>",
            file_types_description : "All Files",
            file_upload_limit : "<?php echo $file_limit;?>",
            file_queue_limit : 0,
            custom_settings : {
                progressTarget : "fsUploadProgress",
                cancelButtonId : "btnCancel",
                tdFilesQueued : document.getElementById("tdFilesQueued"),
                tdFilesUploaded : document.getElementById("tdFilesUploaded"),
                tdErrors : document.getElementById("tdErrors")
            },
            debug: false,
            prevent_swf_caching : false,

            button_image_url : "",
            button_placeholder_id: "spanButtonPlaceHolder",
            button_width: 75,
            button_height: 29,
            button_text : '',
            button_text_style : '',
            button_text_top_padding: 0,
            button_text_left_padding: 0,
            button_window_mode: SWFUpload.WINDOW_MODE.TRANSPARENT,
            button_cursor: SWFUpload.CURSOR.HAND,
            swfupload_preload_handler : preLoad,
            swfupload_load_failed_handler : loadFailed,

            file_queued_handler : fileQueued,
            file_queue_error_handler : fileQueueError,
            upload_start_handler : uploadStart,
            upload_progress_handler : uploadProgress,
            upload_error_handler : uploadError,
            upload_success_handler : uploadSuccess,
            upload_complete_handler : uploadComplete,
            file_dialog_complete_handler:fileDialogComplete
        };


        swfu = new SWFUpload(settings);
    })




    function ajaxload(inputid)
    {
        var data = '';
        var url =  "<?php echo U('Attachment/filelist');?>";
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(msg){
                alert(msg);
                $('#'+inputid).html(msg);
                for(var i=0;i<ids.length;i++){
                    $('#on_'+ids[i]).addClass("on");
                }
            }
        });
    }
</script>
    <script>
        //TAB切换
        function Tabs(id,title,content,box,on,action){
            if(action){
                $(id+' '+title).click(function(){
                    $(this).addClass(on).siblings().removeClass(on);
                    $(content+" > "+box).eq($(id+' '+title).index(this)).show().siblings().hide();
                });
            }else{
                $(id+' '+title).mouseover(function(){
                    $(this).addClass(on).siblings().removeClass(on);
                    $(content+" > "+box).eq($(id+' '+title).index(this)).show().siblings().hide();
                });
            }
        }
        new Tabs("#tabs",".title ul li",".content_1",".tabbox","on",1);
        function addfileurl(obj) {
            var strs = $(obj).val() ?  $(obj).val() : '';

            if(strs){
                var datas='<div id="uplist_1"><input type="hidden" id="aids" name="aids"  value="0"  /><input type="text"  id="filedata" name="filedata" value="'+strs+'"  /><input type="text"  id="namedata" name="namedata" value=""  /> &nbsp;<a href="javascript:remove_this(\'uplist_1\');"><?php echo L('remove');?></a> </div>';
                $('#myuploadform').html(datas);
                $('#thumbnails   a ').removeClass("on");
                $('.img a ').removeClass("on");
            }else{

                $('#myuploadform').html('');
            }
        }
    </script>
    <?php if(!empty($_GET[editorid])): ?><div id="bootline"></div>
        <div id="btnbox" class="btn" style="padding-left:450px;">
            <INPUT TYPE="submit" onclick="insert2Xheditor();" value="<?php echo L(dosubmit);?>" class="button " >
            <input TYPE="reset" onclick="unloadme();" value="<?php echo L(cancel);?>" class="button">
        </div><?php endif; ?>
<script src="/Public/js/my.js"></script>
<script src="/Public/js/swfupload.js"></script>