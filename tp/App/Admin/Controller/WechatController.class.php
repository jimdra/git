<?php
/**
* @desc 公众号管理
* @date 2016-08-18
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class WechatController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $site_list = M('site', null, 'web')->where(array('status'=>1))->select();
        $this->assign('site_list', $site_list);
    }
    
    public function index() {
        $mod = M('wx_info', null, 'web');
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    public function add() {
        $mod = M('wx_info', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('wx_info', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('wx_info', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }

    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['site_name'] = M('site', null, 'web')->where(array('site_id'=>$v['site_id']))->getField('site_name');
        }
        return $list;
    }

    public function menuList() {
        $site_id = I('site_id');
        $menu = $this->getMenu($site_id);
        $this->assign('list',$menu);
        $this->assign('search',array('site_id'=>$site_id));
        $this->display('menu');
    }

    public function menu_add() {
        $mod = M('wx_menu', null, 'web');
        !empty($mod) && $this->_add($mod,'menu_edit');
    }

    public function menu_edit() {
        $mod = M('wx_menu', null, 'web');
        !empty($mod) && $this->_edit($mod,'menu_edit');
    }

    public function menu_delete() {
        $mod = M('wx_menu', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }

    public function create() {
        import("@.ORG.Wechat");
        $site_id = I('site_id');
        $wx_info = M('wx_info',null,'web')->where(array('site_id'=>$site_id))->find();
        $menu = array();
        $data = $this->getMenu($site_id);
        foreach ($data as $rs) {
            if ($rs['status']==0) continue; // 跳过已关闭的一级菜单
            $children = current($rs['child']);

            if (isset($rs['child']) && !empty($children)) { // 有子菜单
                $child = array();
                foreach ($rs['child'] as $_rs) {
                    if ($_rs['status']==0) continue; // 跳过已关闭的二级菜单

                    if ($_rs['action'] == 'url') {
                        $child[] = array('name' => $_rs['menu_name'], 'type' => 'view', 'url' => $_rs['action_param']);
                    } elseif ($_rs['action'] == 'media') {
                        $child[] = array('name' => $_rs['menu_name'], 'type' => 'media_id', 'media_id' => $_rs['action_param']);
                    }else{
                        $child[] = array('name' => $_rs['menu_name'], 'type' => 'click', 'key' => $_rs['action_param']);
                    }
                }
                $menu['button'][] = array('name' => $rs['menu_name'], 'sub_button' => $child);
            } else { // 无子菜单

                if ($rs['action'] == 'url') {
                    $menu['button'][] = array('name' => $rs['menu_name'], 'type' => 'view', 'url' => $rs['action_param']);
                } elseif ($rs['action'] == 'media') {
                    $menu['button'][] = array('name' => $_rs['menu_name'], 'type' => 'media_id', 'media_id' => $rs['action_param']);
                }else{
                    $menu['button'][] = array('name' => $rs['menu_name'], 'type' => 'click', 'key' => $rs['action_param']);
                }
            }
        }

        $wechat = new \Wechat();
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
        $ret = $wechat->createMenu($menu);
        if ($ret['errcode'] == 0) {
            die('ok');
        } else {
            die('状态码：'.$ret['errcode'].'错误信息：'.$ret['errmsg']);
        }
    }

    public function getMenu($site_id) {
        $menu = M('wx_menu',null,'web')->where(array('parent_id'=>0,'site_id'=>$site_id))->order('sort desc')->select();
        foreach ($menu as $k=>$v) {
            $menu[$k]['child'] = M('wx_menu',null,'web')->where(array('parent_id'=>$v['id']))->order('sort desc')->select();
        }
        return $menu;
    }

    public function getTopMenu() {
        $site_id = I('site_id');
        $top_menu = M('wx_menu',null,'web')->where(array('site_id'=>$site_id,'parent_id'=>0))->select();
        if (IS_AJAX) {
            echo $this->ajaxReturnData($top_menu);
        } else {
            return $top_menus;
        }
    }

}
?>
