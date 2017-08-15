<?php
/**
 * 微信公众平台 消息接口
 * 流程：1、当用户回复公众平台时，消息传到本地址。2、本地址程序可回复消息给用户。
 * 微信服务器在五秒内收不到响应会断掉连接。
 * http://mp.weixin.qq.com/wiki/
 *
 * @date   2013-05-06
 */
class Wechat
{
    public static $token = ''; // 公众平台填写的token
    public static $app_id = ''; // 公众平台的app_id
    public static $app_secret = ''; // 公众平台的app_secret
    public static $access_token = '';
    public static $userOpenId, $adminOpenId;

    /**
     * 加密后的字符串与$signature对比，标识该请求来源于微信
     * @return bool
     */
    public static function checkSignature()
    {
        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $tmpArr = array(self::$token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获得普通用户发送过来的消息
     * 当普通微信用户向公众账号发消息时，微信服务器将POST该消息到填写的URL上。
     *
     * @return bool|\SimpleXMLElement
     */
    public static function postData()
    {
        //$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        $postStr = file_get_contents("php://input");
        if (empty($postStr)) return array();
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        // 公共
        $postData['adminOpenId'] = self::$adminOpenId = strval($postObj->ToUserName);
        $postData['userOpenId'] = self::$userOpenId = strval($postObj->FromUserName); // 普通用户（一个OpenID）
        $postData['MsgId'] = strval($postObj->MsgId); // 消息id，64位整型
        $postData['MsgType'] = strval($postObj->MsgType); // text image location link event
        $postData['CreateTime'] = strval($postObj->CreateTime);

        // 文本消息 text
        $postData['Content'] = trim(strval($postObj->Content)); // 文本消息内容
        // 图片消息 image
        $postData['PicUrl'] = strval($postObj->PicUrl); // 图片链接
		//语音信息 voice
        $postData['MediaId'] = strval($postObj->MediaId); // 多媒体ID

        // 地理位置消息 location
        $postData['Location_X'] = strval($postObj->Location_X); // 地理位置纬度
        $postData['Location_Y'] = strval($postObj->Location_Y); // 地理位置经度
        $postData['Scale'] = strval($postObj->Scale); // 地图缩放大小
        $postData['Label'] = strval($postObj->Label); // 地理位置信息
        // event事件的 地理位置消息LOCATION
        $postData['Latitude'] = strval($postObj->Latitude); // 地理位置纬度
        $postData['Longitude'] = strval($postObj->Longitude); // 地理位置经度
        $postData['Precision'] = strval($postObj->Precision); // 地理位置精度
        // 链接消息 link
        $postData['Title'] = strval($postObj->Title); // 消息标题
        $postData['Description'] = strval($postObj->Description); // 消息描述
        $postData['Url'] = strval($postObj->Url); // 消息链接
        // 事件推送 event
        $postData['Event'] = strval($postObj->Event); // 事件类型，subscribe(订阅)、unsubscribe(取消订阅)、CLICK(自定义菜单点击事件) card_pass_check(卡券通过审核)、card_not_pass_check（卡券未通过审核） user_get_card(用户领取卡券) user_del_card(用户删除卡券)
        $postData['EventKey'] = strval($postObj->EventKey); // 事件KEY值，与自定义菜单接口中KEY值对应

        //卡卷事件推送之审核事件
        //事件类型， card_pass_check(卡券通过审核)、card_not_pass_check（卡券未通过审核）
        $postData['ToUserName'] = strval($postObj->ToUserName); // 开发者微信号
        $postData['FromUserName'] = strval($postObj->FromUserName); //发送方open_id
        $postData['CardId'] = strval($postObj->CardId); //卡卷ID
       
        //卡卷领取
        //事件类型，user_get_card(用户领取卡券)
        $postData['FriendUserName'] = strval($postObj->FriendUserName); //赠送方账号（一个OpenID），"IsGiveByFriend”为1 时填写该参数。
        $postData['IsGiveByFriend'] = strval($postObj->IsGiveByFriend); //是否为转赠，1 代表是，0 代表否。
        $postData['UserCardCode'] = strval($postObj->UserCardCode); //code 序列号。自定义code 及非自定义code的卡券被领取后都支持事件推送。
        $postData['OuterId'] = strval($postObj->OuterId); //领取场景值，用于领取渠道数据统计。可在生成二维码接口及添加JS API 接口中自定义该字段的整型值。
        
        
        //删除卡卷(用户删除code)
        //事件类型，user_del_card(用户删除卡券)
        $postData['UserCardCode'] = strval($postObj->UserCardCode); //商户自定义code 值。非自定code 推送为空
        
        return $postData;
    }

    /**
     * 回复文本消息模板
     * @param $content 长度不超过2048字节
     * @return string
     */
    public static function textTpl($content)
    {
        $textTpl = '<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>';
        return sprintf($textTpl, self::$userOpenId, self::$adminOpenId, time(), 'text', $content);
    }

    /**
     * 回复图文消息模板
     * @param array $article_array  一条消息 array() 或多条 array(array(), array());
     *                              Title         图文消息标题
     *                              Description   图文消息描述
     *                              PicUrl        图片链接，支持JPG、PNG格式，较好的效果为大图360*200，小图200*200
     *                              Url           点击图文消息跳转链接
     * @return string
     *
     * Articles    多条图文消息信息，默认第一个item为大图
     * ArticleCount 图文消息个数，限制为10条以内
     */
    public static function newsTpl($article_array)
    {
        if (!is_array(current($article_array))) $article_array = array($article_array);

        $articles = '';
        foreach ($article_array as $val) {
            $articles .= "<item>
                          <Title><![CDATA[{$val['title']}]]></Title>
                          <Description><![CDATA[{$val['description']}]]></Description>
                          <PicUrl><![CDATA[{$val['picurl']}]]></PicUrl>
                          <Url><![CDATA[{$val['url']}]]></Url>
                          </item>";
        }
        $msg_type = 'news';
        $count = count($article_array);
        return "<xml>
                <ToUserName><![CDATA[" . self::$userOpenId . "]]></ToUserName>
                <FromUserName><![CDATA[" . self::$adminOpenId . "]]></FromUserName>
                <CreateTime>" . time() . "</CreateTime>
                <MsgType><![CDATA[{$msg_type}]]></MsgType>
                <ArticleCount>{$count}</ArticleCount>
                <Articles>
                {$articles}
                </Articles>
                </xml> ";
    }

    /**
     * 回复客服文本消息
     */
    public static function serviceText($content)
    {
        if (empty(self::$userOpenId)) return false;

        $data = '{"touser": "' . self::$userOpenId . '", "msgtype": "text",  "text": { "content": "' . $content . '" }}';
        $ret = wCurl::post('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . self::getAccessToken(), $data);
        $_data = json_decode($ret, true);
        return $_data;
    }

    /**
     * 回复客服图文消息
     */
    public static function serviceNews($article_array)
    {
        if (empty(self::$access_token) || empty(self::$userOpenId) || !is_array($article_array) || empty($article_array)) return '';
        if (!is_array(current($article_array))) $article_array = array($article_array);

        $articles = '';
        foreach ($article_array as $val) {
            $articles .= ' {
					 "title":"' . $val['title'] . '",
					 "description":"' . $val['description'] . '",
					 "url":"' . $val['url'] . '",
					 "picurl":"' . $val['picurl'] . '"
				 },';
        }
        $articles = substr($articles, 0, -1);
        $tpl = '{
			"touser":"' . self::$userOpenId . '",
			"msgtype":"news",
			"news":{
				"articles": [
					' . $articles . '
				 ]
			}
		}';

        $ret = wCurl::post('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token=' . self::getAccessToken(), $tpl);
        $_data = json_decode($ret, true);
        return ($_data['errcode'] == 0);
    }

    /**
     * 获取access_token
     *
     * @return string
     */
    public static function getAccessToken()
    {
        return self::$access_token;
        //return self::$access_token ? self::$access_token : self::accessToken();
    }


    public static function accessToken()
    {
        $ret = wCurl::get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . self::$app_id . '&secret=' . self::$app_secret);
        $data = json_decode($ret, true);
        if ($data['errcode'] != 0) return '';
        return $data['access_token'];
    }

    /**
     * 获取永久二维码
     *
     * @param $scene_id 标识值 永久二维码时最大值为100000（目前参数只支持1--100000）
     * @return string     返回图片地址
     */
    public static function getQrcode($scene_id)
    {
        if ($scene_id > 100000) return '';

        $access_token = self::getAccessToken();
        $data['action_name'] = 'QR_LIMIT_SCENE'; // 永久
        $data['action_info'] = array('scene' => array('scene_id' => $scene_id));

        $ret = wCurl::post('http://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token, json_encode($data));
        $_data = json_decode($ret, true);
        if ($_data['errcode'] != 0) return '';
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $_data['ticket'];
    }

    /**
     * 获取临时二维码
     *
     * @param $scene_id 标识值 (为32位非0整型)
     * @return string     返回图片地址
     */
    public static function getTempQrcode($scene_id)
    {
        $access_token = self::getAccessToken();
        $data['action_name'] = 'QR_SCENE'; // 临时
        $data['expire_seconds'] = 1800; // 有效时间 秒  最大1800
        $data['action_info'] = array('scene' => array('scene_id' => $scene_id));		
        $ret = wCurl::post('http://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $access_token, json_encode($data));
        $_data = json_decode($ret, true);
        //  var_dump($access_token);
        //    var_dump($_data);
        //   exit;
        if ($_data['errcode'] != 0) return '';
        return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . $_data['ticket'];
    }

    /**
     * 获取个人信息
     *
     * @return array
     */
    public static function getUserInfo()
    {
        $ret = wCurl::get('https://api.weixin.qq.com/cgi-bin/user/info?access_token=' . self::getAccessToken() . '&openid=' . self::$userOpenId . '&lang=zh_CN');
        $ret = json_decode($ret, true);
        return ($ret['errcode'] == 0) ? $ret : array();
    }

    // 创建菜单
    public static function createMenu($data = array())
    {
        $jsonData = json_encode($data);
        $jsonData = self::_unicodeDecode($jsonData);
        $ret = wCurl::post('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . self::getAccessToken(), $jsonData);
        $_data = json_decode($ret, true);
        return $_data;
    }

    /**
     * 获取永久素材列表
     *
     * @param $type 素材的类型
     * @return array     返回列表
     */
    public static function getMaterial($type)
    {
        $access_token = self::getAccessToken();
        $data['type'] = $type;
        $data['offset'] = 0;
        $data['count'] = 20;

        $ret = wCurl::post('https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token=' . $access_token, json_encode($data));
        $_data = json_decode($ret, true);
        return $_data;
    }

    // unicode \u形式的中文 转成 普通中文
    private static function _unicodeDecode($string)
    {
        return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', create_function(
            '$matches', 'return mb_convert_encoding(pack("H*", $matches[1]), "UTF-8", "UCS-2BE");'
        ), $string);
    }

    /**
     * 根据经纬度获取地址
     *
     * @param $point 23.137466,113.352425
     * @return string
     */
    public static function pointToAddress($point)
    {
        $ret = wCurl::get('http://api.map.baidu.com/geocoder/v2/?ak=63c094ef70725e24c3198fe46e6357ef&output=json&pois=0&location=' . $point);
        $_data = json_decode($ret, true);
        return ($_data['status'] == 0) ? $_data['result']['formatted_address'] : '';
        /*{
          status: 0,
          result: {
            location: {
              lng: 113.35242499464,
              lat: 23.137466022819
            },
            formatted_address: "广东省广州市天河区虹口街18号",
            business: "石牌,岗顶,天河公园",
            addressComponent: {
              city: "广州市",
              district: "天河区",
              province: "广东省",
              street: "虹口街",
              street_number: "18号"
            },
            cityCode: 257
          }
        }*/
    }
}

/**
 * curl
 *
 * @time      2012-12-21
 * @reference 网络资源
 */
class wCurl
{
    public static $is_proxy = true; // 是否启用代理
    public static $proxy_ip = ''; // 234.234.234.234代理服务器地址
    public static $cookie_file = ''; // E:/sss/Cache/cookie_curl.txt设置Cookie文件保存路径及文件名
    public static $user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; SeaPort/1.2; Windows NT 5.2; .NET CLR 1.1.4322)';

    /**
     * 模拟登录，并保存Cookie
     *
     * @param $url
     * @param $data
     * @return mixed
     */
    public static function login($url, $data)
    {
        $curl = curl_init();
        if (self::$is_proxy) {
            curl_setopt($curl, CURLOPT_PROXY, self::$proxy_ip);
        }
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, self::$user_agent); // 模拟用户使用的浏览器
        @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_COOKIEJAR, self::$cookie_file); // 存放Cookie信息的文件名称
        curl_setopt($curl, CURLOPT_COOKIEFILE, self::$cookie_file); // 读取上面所储存的Cookie信息
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $r = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        }
        curl_close($curl);
        return $r;
    }

    /**
     * 模拟获取
     * @param $url
     * @return mixed
     */
    public static function get($url)
    {
        $curl = curl_init();
        if (self::$is_proxy) {
            curl_setopt($curl, CURLOPT_PROXY, self::$proxy_ip);
        }
        if (self::$cookie_file) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, self::$cookie_file); // 读取上面所储存的Cookie信息
        }
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, self::$user_agent); // 模拟用户使用的浏览器
        @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_HTTPGET, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_TIMEOUT, 120); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $r = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        }
        curl_close($curl);
        return $r;
    }

    /**
     * 模拟提交
     * @param $url
     * @param $data
     * @return mixed
     */
    public static function post($url, $data)
    {
        $curl = curl_init();
        if (self::$is_proxy) {
            curl_setopt($curl, CURLOPT_PROXY, self::$proxy_ip);
        }
        if (self::$cookie_file) {
            curl_setopt($curl, CURLOPT_COOKIEFILE, self::$cookie_file); // 读取上面所储存的Cookie信息
        }
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        //curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, self::$user_agent); // 模拟用户使用的浏览器
        @curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 120); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $r = curl_exec($curl);
        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        }
        curl_close($curl);
        return $r;
    }
}