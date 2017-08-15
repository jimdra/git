<?php
/**
* @desc 微信Api
* @date 2016-10-10
* @author zhanjunjie
*/

namespace Api\Controller;
use Think\Controller;
class WechatController extends Controller {

    protected $site_id = '';

    public function __construct() {
        parent::__construct();
        $this->site_id = intval($_REQUEST['site_id']);
    }
    
    public function index() {
        import("Admin.ORG.Wechat");
        $wechat = new \Wechat();
        $wx_info = $this->getWxInfo($this->site_id);
        //$wechat::$token = $wx_info['token'];
        $wechat::$app_id = $wx_info['app_id'];
        $wechat::$app_secret = $wx_info['app_secret'];
        //判断access_token是否过期
        $expire_in = time() - $wx_info['access_token_time'];
        if ($wx_info['access_token'] && $expire_in < 7000 ) {
            $wechat::$access_token = $wx_info['access_token'];
        } else {
            $access_token = $wechat->accessToken();
            $info = array('access_token'=>$access_token,'access_token_time'=>time());
            M('wx_info',null,'web')->where(array('site_id'=>$this->site_id))->save($info);
            $wechat::$access_token = $access_token;
        }
        $data = $wechat->postData();
        //$data = array('MsgType'=>'text','FromUserName'=>'oUSLpwKVOHH6HDcjvQASCLhicANg','Content'=>'怎么下载');

        if ($data && is_array($data)) {
            $wechat::$userOpenId = $data['FromUserName'];
            $userInfo = $wechat->getUserInfo();
            //关注时二维码，记录数据，推送信息
            if ($data['Event'] == 'subscribe') {
                $this->subscribe($userInfo);
                $content = '/可怜 圣君终于找到奴奴啦！ 奴奴为您准备了丰厚福利、最新资讯、最全攻略，/害羞 让奴奴陪您一起走遍圣元大陆吧~~~';
                $wechat->serviceText($content);
            } elseif ($data['Event'] == 'unsubscribe') {
                $this->unsubscribe($userInfo);
            }

            if ($data['MsgType'] == 'event') {
                if ($data['EventKey'] == 'ios') {
                    $content = $this->getPackage($data,1);
                    $wechat->serviceText($content);
                } elseif ($data['EventKey'] == 'mix') {
                    $content = $this->getPackage($data,2);
                    $wechat->serviceText($content);
                } elseif ($data['EventKey'] == 'app') {
                    $content = $this->getPackage($data,3);
                    $wechat->serviceText($content);
                } elseif ($data['EventKey'] == 'service') {
                    $wechat->serviceText('【客服电话】：4009003270 \n【客服邮箱】：4009003270@b.qq.com \n 【客服QQ】： 4009003270 ');
                } elseif ($data['EventKey'] == 'submission') {
                    $wechat->serviceText('如果你有故事希望分享给广大儒道玩家，无论游戏攻略、玩家故事还是游戏趣闻，都可以投稿至邮箱：rudaoshouyou@163.com,我们会挑选优秀的投稿发布在儒道公众号上，还会有丰富的奖励哦！');
                }
                return '';
            }

            if ($data['MsgType'] == 'text') {
                $content = $data['Content'];
                $keywords = array('公测','测试','上线','什么时候能玩','怎么下载','啥时候','客户端','下载','下游戏','福利','礼包','预约','游戏');
                if (in_array($content, $keywords)) {
                    $wechat->serviceText('研发大大们正在加班加点地增加游戏玩法，优化游戏体验中~奴奴悄悄打听到，预计11月下旬就会上线了哦！\n先戳下方预约按钮预约，领取公测大礼包吧！！↓↓↓↓↓↓↓↓');
                }
                return '';
            }

        } else {
            //$wechat::$userOpenId = 'oUSLpwKVOHH6HDcjvQASCLhicANg';
            //$wechat->serviceText('微信传输没有结果');
        }
    }

    public function getPackage($info,$type) {
        switch ($type) {
            case 1:
                $type_name = '安卓官服&苹果IOS服';
                break;
            case 2:
                $type_name = '安卓平台服';
                break;
            case 3:
                $type_name = '应用宝-应用宝服';
                break;
        }
        $openid = $info['FromUserName'];
		$package_data = M('wx_package',null,'web')->where(array('openid'=>$openid,'type'=>$type,'site_id'=>$this->site_id))->find();
        if (!empty($package_data)) {
            $content = '您已领取过'.$type_name.'微信关注礼包啦，礼包激活码：'.$package_data['code']. ',随时关注儒道微信，奴奴会有更多福利送给圣君哦！';
        } else {
            $package = M('wx_package',null,'web')->where(array('is_active'=>0,'site_id'=>$this->site_id,'type'=>$type))->order('id asc')->find();
            if ($package) {
                $content = '恭喜圣君获得《儒道至圣》'.$type_name.'微信礼包一个，礼包激活码：'.$package['code'].',\n请登录游戏点击画面上方的“奖励”图标，在礼包兑换处输入礼包激活码兑换~';
            } else {
                $content = '微信礼包已被领取完毕，奴奴会尽快上新哒，请稍后再尝试领取~';
            }
            $data = array(
                'active_time'=>time(),
                'openid'=>$openid,
                'is_active'=>1
            );
            M('wx_package',null,'web')->where(array('id'=>$package['id']))->save($data);
        }
        return $content;
    }

    public function getWxInfo($site_id) {
        $info = M('wx_info',null,'web')->where(array('site_id'=>$site_id))->find();
        return $info;
    }

    public function subscribe($userInfo) {
        $openid = $userInfo['openid'];
        $data = array(
            'site_id'=>$this->site_id,
            'openid'=>$userInfo['openid'],
            'nickname'=>$userInfo['nickname'],
            'sex'=>$userInfo['sex'],
            'city'=>$userInfo['city'],
            'country'=>$userInfo['country'],
            'province'=>$userInfo['province'],
            'language'=>$userInfo['language'],
            'headimgurl'=>$userInfo['headimgurl'],
            'subscribe'=>$userInfo['subscribe'],
            'subscribe_time'=>$userInfo['subscribe_time']
        );
        if (M('wx_user',null,'web')->where(array('openid'=>$openid))->count()) {
            unset($data['openid']);

            return M('wx_user',null,'web')->where(array('openid'=>$openid))->save($data);
        }
        return M('wx_user',null,'web')->add($data);
    }

    public function unsubscribe($info) {
        $openid = $info['openid'];
        $data = array('subscribe'=>0,'cancel_time'=>time());
        return M('wx_user',null,'web')->where(array('openid'=>$openid))->save($data);
    }

}
?>
