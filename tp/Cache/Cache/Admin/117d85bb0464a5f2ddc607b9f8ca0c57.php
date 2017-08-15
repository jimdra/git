<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo ($seo_title); ?></title>
        <meta name="viewport" content="initial-scale=1, user-scalable=0, minimal-ui">
        <meta name="keywords" content="<?php echo ($seo_keywords); ?>" />
        <meta name="Description" content="<?php echo ($seo_description); ?>" />
        <link rel="stylesheet" type="text/css" href="/css/index.css">
        <script src="/js/jquery.js" type="text/javascript"></script>
        <script src="/js/zzsc.js" type="text/javascript"></script>
        <script type="text/javascript" src="/js/superslide.2.1.js"></script>

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
    <!-- 焦点图开始 -->
    <div class="fullSlide">
        <div class="bd">
            <ul>
                <?php  $_result=M('Slide_data',null,'web')->where(" status=1 and slide_id=1 ")->order("id DESC ")->limit("5")->select();; if ($_result): $k=0;foreach($_result as $key=>$vo):++$k;$mod = ($k % 2 );?><li _src="url(<?php echo ($vo["img"]); ?>)" style="background:#fff center 0 no-repeat;"><a href="#"></a></li><?php endforeach; endif;?>
            </ul>
        </div>
        <div class="hd"><ul></ul></div>
        <span class="prev"></span>
        <span class="next"></span>
    </div>
    <!--焦点图结束-->
    <!--游戏分类开始-->
    <div id="zzsc">
        <div id="wai_box">
            <div class="zzsc_box">
                <ul>
                    <?php  $_result=M('Slide_data',null,'web')->where(" status=1 and slide_id=2 ")->order("id DESC ")->limit("5")->select();; if ($_result): $k=0;foreach($_result as $key=>$vo):++$k;$mod = ($k % 2 );?><li>
                            <i class="hot"></i><a href="#" class="images"><img src="<?php echo ($vo["img"]); ?>" width="330" height="207" /></a><span class="title purple"><i class="purple_phone"></i><div class="des"><h2><?php echo ($vo["title"]); ?></h2><p><?php echo ($vo["description"]); ?></p></div></span>
                            <div class="ceng">
                                <div class="ewm"><img src="<?php echo ($vo["thumb"]); ?>" alt=""></div>
                                <div class="gamemsg">
                                    <p class="gametype"><?php echo ($vo["title"]); ?></p>
                                    <p class="gamedes"><?php echo ($vo["description"]); ?></p>
                                    <a href="#" class="gamelink"></a>
                                </div>
                            </div>
                        </li><?php endforeach; endif;?>
                </ul>
            </div>
        </div>
    </div>
    <!--游戏分类结束-->
    <!--公司资料开始-->
    <div class="container">
        <div class="gameinfomation">
            <div class="news left">
                <div class="newstitle">最新资讯<a href="/news/" class="checkmove"></a></div>
                <ul>
                    <?php  $_result=M("Article",null,'web')->field("*")->where(" 1  AND `show` like '%1%' AND status=1  and cat_id='36'")->order("addtime desc")->limit("3")->select();; if ($_result): $k=0;foreach($_result as $key=>$vo):++$k;$mod = ($k % 2 );?><li>
                        <div class="wen">
                            <a href="<?php echo ($vo["url"]); ?>"><h3><?php echo (str_cut($vo["title"],35)); ?></h3></a>
                            <a href="<?php echo ($vo["url"]); ?>"><p class="gamedesp"><?php echo (str_cut(strip_tags($vo["content"]),100)); ?></p></a>
                        </div>
                        <a href="<?php echo ($vo["url"]); ?>" class="more"><p>查看详情</p></a>
                    </li><?php endforeach; endif;?>
                </ul>
            </div>
            <div class="right company">
                <div class="companytitle">公司简介</div>
                <div class="companydesp">
                    雷神互娱是一家专注于移动游戏研发与发行的新兴游戏公司，致力于打造一流的移动游戏精品。 团队汇聚了手游研发和发行经验的专业人才，核心团队自2008年就专注于游戏研发与运营，多款自研产品月流水超过千万，并多次获得中国游戏行业的重要奖项。目前已经上线的游戏有“超级战争”、“剑雨乾坤”、“神魔西游”等。
                </div>
                <div class="contact">
                    <p>关注我们<i class="xian"></i><a href="#" class="qq"></a><a href="#" class="wechat"></a></p>
                </div>
            </div>
        </div>
    </div>

    <!--公司资料结束-->
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
<script>
    $(".fullSlide").hover(function () {
        $(this).find(".prev,.next").stop(true, true).fadeTo("show", 0.5)
    },
            function () {
                $(this).find(".prev,.next").fadeOut()
            });
    $(".fullSlide").slide({
        titCell: ".hd ul",
        mainCell: ".bd ul",
        effect: "fold",
        autoPlay: true,
        autoPage: true,
        trigger: "click",
        startFun: function (i) {
            var curLi = jQuery(".fullSlide .bd li").eq(i);
            if (!!curLi.attr("_src")) {
                curLi.css("background-image", curLi.attr("_src")).removeAttr("_src")
            }
        }
    });
</script>