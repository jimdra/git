<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title><?php if($land_info['title']): echo ($land_info['title']); else: echo ($game_info['game_name']); endif; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
	    <meta name="renderer" content="webkit">
	    <meta name="format-detection" content="telephone=no">
	    <link rel="stylesheet" type="text/css" href="https://leishenhuyu.com/landing/css/swiper.3.1.2.min.css"/>
	    <link rel="stylesheet" type="text/css" href="https://leishenhuyu.com/landing/css/css.css"/>
	    <style>
			#allLink{position: absolute;z-index: 999}
	    </style>

	</head>
	<body>
		<?php if($land_info["click_type"] == 2): ?><a id="allLink"></a><?php endif; ?>
		<!-- 顶部轮播开始 -->
		<div class="headMove">
			 <div class="swiper-container">
		        <div class="swiper-wrapper">
					<?php if(is_array($material)): $i = 0; $__LIST__ = array_slice($material,0,$land_info['material']-2,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="swiper-slide <?php if($i != 1): ?>swiper-slide-active<?php endif; ?>" ><img src="<?php echo ($vo["material_thumb"]); ?>"/></div><?php endforeach; endif; else: echo "" ;endif; ?>
		        </div>
		    </div>
		</div>
		<!-- 顶部轮播结束 -->
		<!-- 点击放大开始 -->
		<div id="pic_one" style="display:;">
			<?php if(is_array($material)): $i = 0; $__LIST__ = array_slice($material,0,$land_info['material']-2,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="pic_c">
					<div class="cc"><img src="<?php echo ($vo["material_thumb"]); ?>"/></div>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
			<!-- 点击放大里面分页开始 -->
			<div class="pages">
				<?php if(is_array($material)): $i = 0; $__LIST__ = array_slice($material,0,$land_info['material']-2,true);if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div <?php if($i == 1): ?>class="cur"<?php endif; ?>></div><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
			<!-- 点击放大里面分页结束 -->
		</div>
		<!-- 点击放大结束 -->	
		<div class=" clearfix">
			<img src="<?php echo ($material[$land_info['material']-2]['material_thumb']); ?>" width="100%">
		</div>	
		<div class="more">
		<!--下载链接-->
			<p><a>查看更多精华评论</a></p>
		</div>
		<div class="copyr">
				<?php echo ($land_info['bottom_info']); ?>
			</div>
		<div class="dowBx">
		<!--下载链接-->
			<a ><img src="<?php echo ($material[$land_info['material']-1]['material_thumb']); ?>" width="100%"></a>
		</div>		
	</body>
</html>
<?php echo ($land_info['stat_js']); ?>
<script type="text/javascript" src="https://leishenhuyu.com/landing/js/jquery-2.1.4.min.js" ></script>
<script type="text/javascript" src="https://leishenhuyu.com/landing/js/swiper.3.1.2.min.js" ></script>
<script type="text/javascript" src="https://leishenhuyu.com/landing/js/js.js" ></script>
<script>
var land_id = "<?php echo ($land_info['id']); ?>";
	var click_type = "<?php echo ($land_info['click_type']); ?>";
	var url = 'https://admin.leishenhuyu.com';
	window.onload = function(){
		var APK = "<?php echo ($land_info['android_down_url']); ?>";		//Android下载链接
		var IOS = "<?php echo ($land_info['ios_down_url']); ?>";		//IOS下载链接
		var TIME = "<?php echo ($land_info['auto_jump']); ?>";			//延时秒数
		function handle(){
			this.a = document.getElementsByTagName('a');
			this.allLink = document.getElementById('allLink');
			//this.info = document.getElementById('info');
		} 
		handle.prototype.init = function(){
			if (click_type==2) {
			this.allClient();  //全屏点击
			}
			this.clickLink(APK,IOS);
			this.log(land_id,1);
			if (TIME!=0) {
			this.setTime(APK,IOS,TIME+'000');
			}
			//this.info.style.top = document.body.scrollHeight +'px';
		}
		handle.prototype.allClient = function(){ 
			this.allLink.style.display = 'block';
			this.allLink.style.width = document.body.clientWidth+'px';
			this.allLink.style.height = document.body.scrollHeight +'px';
		}
		handle.prototype.clickLink = function(apk,ios){
			var _this = this;
			for(var i=0;i<this.a.length;i++){
				this.a[i].onclick = function()
				{
					_this.log(land_id,2);
					if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
					    window.location.href =ios;   //iphone的下载链接
					} else if (/(Android)/i.test(navigator.userAgent)) {   //判断Android
					    window.location.href =apk;   //Android的下载链接
					};
				}
			}
		}
		handle.prototype.setTime = function(apk,ios,settime){
		var _this = this;
			if (/(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)) {  //判断iPhone|iPad|iPod|iOS
				setTimeout(function(){
					_this.log(land_id,2);
					 window.location.href =ios;   //iphone的下载链接
				},settime)
			   
			} else if (/(Android)/i.test(navigator.userAgent)) {   //判断Android
				setTimeout(function(){
					_this.log(land_id,2);	
					  window.location.href =apk;   //Android的下载链接
				},settime)
			};
		}
		handle.prototype.log = function(land_id,type_id){
			$.ajax({
				type: "GET",
				url: url+'/index.php?m=Api&c=Land&a=land_log&land_id='+land_id+'&type='+type_id,
				dataType: "jsonp",
				jsonp: "callback",
				success: function(data){
					
				}
			});
		}
		var FN = new handle();
		FN.init();
	}
</script>