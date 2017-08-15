<?php
/**
 * 前台控制器基类
 *
 * @author zhanjunjie
 */
namespace Api\Controller;
use Think\Controller;
class BaseController extends Controller {
    protected $_name = '';
    protected $site_id = '';
    public function _initialize() {
        $this->_name = CONTROLLER_NAME;
        $this->site_id = $_REQUEST['site_id'];
        $this->check_priv();
    }

    public function ajaxReturnData($list,$count=0,$footer=array()){
        $data['rows'] = !empty($list) ? $list : '';
        $data['total'] = !empty($count) ? $count : count($list);
        $data['footer'] = array($footer);
        return json_encode($data);
    }


    public function _empty() {
        $this->_404();
    }

    protected function _404($url = '') {
        if ($url) {
            redirect($url);
        } else {
            send_http_status(404);
            $this->display(TMPL_PATH . '404.html');
            exit;
        }
    }

    /**
     * AJAX返回数据标准
     *
     * @param int $status
     * @param string $msg
     * @param mixed $data
     * @param string $dialog
     */
    protected function ajaxReturn($status=1, $msg='', $data='', $dialog='') {
        parent::ajaxReturn(array(
                'status' => $status,
                'msg' => $msg,
                'data' => $data,
                'dialog' => $dialog,
        ));
    }

    public function check_priv() {
        if ( (!isset($_SESSION['user']) || !$_SESSION['user']) ) {
            $wx_info = M('wx_info')->where(array('site_id'=> $this->site_id))->find();
            if (isset($_GET['code']) && !empty($_GET['code'])) {
                $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$wx_info['app_id']."&secret=".$wx_info['app_secret']."&code=".$_GET['code']."&grant_type=authorization_code";
                $data = load_url($url);
                $data = json_decode($data,true);
                $openid = $data['openid'];
                $wx_user = M('wx_user')->where(array('openid'=>$openid))->find();
                session('user', array(
                        'openid' => $wx_user['openid'],
                        'nickname' => $wx_user['nickname'],
                        'headimgurl' => $wx_user['headimgurl'],
                ));
                redirect(U('Index/index',array('site_id'=> $this->site_id),false,true));
            } else {
                $redirect_uri = U('Index/index',array('site_id'=> $this->site_id),false,true);
                $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$wx_info['app_id']."&redirect_uri=". urlencode($redirect_uri)."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
                header('Location: '.$url);
            }
        }
    }

}
