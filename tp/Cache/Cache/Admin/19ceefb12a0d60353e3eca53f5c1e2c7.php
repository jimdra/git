<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="cn">
<head>
	<meta charset="UTF-8">
	<title><?php echo ($land_info['land_title']); ?></title>
	<meta charset="UTF-8">
	<meta name="author" content="maiyongchao, ith5cn@163.com">
	<meta content="width=device-width,user-scalable=no" name="viewport">
	<meta name="HandheldFriendly" content="true">
	<meta http-equiv="x-rim-auto-match" content="none">
	<meta name="format-detection" content="telephone=no">
	<script type="text/javascript" src="https://cdn.qitiangame.com/landing/js/zepto.min.js"></script>
	<script>
			var dpr = 1 / window.devicePixelRatio;
			document.write('<meta name="viewport" content="width=device-width,user-scalable=no,initial-scale='+dpr+',minimum-scale='+dpr+',maximum-scale='+dpr+'" />')
			var fz = document.documentElement.clientWidth / 10;
			document.getElementsByTagName('html')[0].style.fontSize = fz + 'px';
	</script>
	<style>
	a,button,input{outline:none;}
	body, p, div, input, h1, h2, h3, h4, h5, h6, ul, li, dl, dt, dd, form { margin: 0px; padding: 0px; list-style: outside none none; vertical-align: middle; }
	body{max-width: 10.0rem;margin:0 auto;background:#020D4B;-webkit-tap-highlight-color:rgba(0,0,0,0);}
	.wamp{width: 10.0rem;position: relative;}
	.warm img{width:100%;position: relative;float:left;}
	.info{text-align: center;bottom: 2%;font-size:0.46875rem;color:#fff;background:#020D4B;padding-top: 10%;padding-bottom: 5%;}
    a img{border:none;}
    .footer{position: fixed;bottom: 0}
	#allLink{display: none;position: absolute;z-index: 997;}
	#hb{display:none;position:absolute; width: 7.0rem;height: 8.322581rem;left:15%;z-index: 999}
	#hb1{width: 100%}
	#btn2{position: absolute;top:6.451613rem;left: 1.564516rem;width:4.032258rem}
	#btnclose{width: 1.290323rem;right: 0;top:0;position: absolute;display:block;}
	#open{width: 100%;display: none;}
	#vi{padding-top:12%;width:100%;margin-bottom:-3%;}
	</style>
</head>
<body>
<?php if($land_info["click_type"] == 2): ?><a id="allLink"></a><?php endif; ?>
		<div class="warm">
			<?php if(is_array($material)): $i = 0; $__LIST__ = $material;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(in_array($i,$link)): ?><a><?php endif; ?>
			<?php if(pathinfo($vo['material_thumb'],PATHINFO_EXTENSION) == 'mp4'): ?><video src="<?php echo ($vo["material_thumb"]); ?>" id="vi" autoplay loop></video>
			<?php else: ?>
			<img src="<?php echo ($vo["material_thumb"]); ?>" width="100%" <?php if($i == 1): ?>style="position:fixed;z-index:998;max-width:10.322581rem;"<?php endif; ?>><?php endif; ?>
			<?php if(in_array($i,$link)): ?></a><?php endif; endforeach; endif; else: echo "" ;endif; ?>
			<div style="clear:both"></div>
			<p class="info" id="info"><?php echo ($land_info['bottom_info']); ?></p>
		</div>
</body>
</html>
<?php echo ($land_info['stat_js']); ?>
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
			this.info = document.getElementById('info');
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
			this.info.style.top = document.body.scrollHeight +'px';
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