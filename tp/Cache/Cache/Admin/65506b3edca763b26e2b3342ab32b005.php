<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo ($seo_title); ?></title>
        <meta name="keywords" content="<?php echo ($seo_keywords); ?>">
        <meta name="Description" content="<?php echo ($seo_description); ?>">
        <link rel="stylesheet" type="text/css" href="/css/newslist.css">
        <script src="/js/jquery.js" type="text/javascript"></script>
    </head>
    <body>
        <nav>
    <div class="container">
        <h1 class="logo left">
            <img src="/images/logo.jpg" width="100%" alt="雷神互娱">
        </h1>
        <div class="menu right">
            <ul>
                <li><a href="/">首页</a></li>
                <li><a href="/about.html">公司简介</a></li>
                <li><a href="/news/">新闻动态</a></li>
                <li><a href="/game/">产品</a></li>
                <li><a href="/join/">加入我们</a></li>
            </ul>
        </div>
    </div>
</nav>
        <div style="clear: both"></div>
        <div class="container con">
            <div class="location">
                <i class="ge"></i><a href="/">首页</a> > <?php echo ($cat["cat_name"]); ?>
            </div>
            <div class="contact left">
                <div class="guangfang">
                    <a href="#" class="wechat"></a>
                    <a href="#" class="weibo"></a>
                </div>
                <div class="message">
                    <ul>
                        <li>客服电话：111111111</li>
                        <li>客服QQ：22222222</li>
                        <li>客服邮箱：333333333</li>
                    </ul>
                </div>
                <div class="tabclass">
                    <a href="#" class="shangwu">商务合作</a>
                    <a href="#" class="jianjie">公司简介</a>
                    <a href="#" class="zhaopin">贤才招聘</a>
                    <a href="#" class="phoneNumber">联系方式</a>
                </div>
            </div>
            <div class="newslist right">
                <div class="newslisttitle">雷神资讯</div>
                <div class="newslistcon">
                    <ul>
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                            <a href="<?php echo ($vo["url"]); ?>" title="<?php echo ($vo["title"]); ?>" target="_blank"><span class="listtitle"><?php echo ($vo["title"]); ?></span><?php echo (date("Y-m-d",$vo["addtime"])); ?></a>
                        </li><?php endforeach; endif; else: echo "" ;endif; ?>
                    </ul>
                </div>
            </div>
            <div style="clear: both"></div>
        </div>
        <!--底部信息-->
        <div class="footer">
    <div class="container">
        <div class="footerlogo left"><img src="/images/logo.png" alt="雷神互娱"></div>
        <div class="footercompany">
            <p><a href="#">关于雷神</a>|<a href="#">法律声明</a>|<a href="#">联系我们</a></p>
            <p> &nbsp;雷神互娱(天津)科技有限公司 COPYRIGHT © 2016 LEISHENHUYU.COM LIMITED. ALL RIGHTS RESERVED</p>
            <p> &nbsp;健康游戏忠告:抵制不良游戏 拒绝盗版游戏 注意自我保护 谨防受骗上当 适当游戏益脑 沉迷游戏益脑 沉迷游戏伤身 合理安排时间</p>
            <P><a href="#">津网文</a>|津ICP备16008761号-1</P>
        </div>
    </div>
</div>
<script>
    var height = ($(window).height()-203)+'px';
    $(".con").css("min-height", height);
</script>
    </body>
</html>