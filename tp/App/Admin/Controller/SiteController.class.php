<?php
/**
* @desc 站点管理
* @date 2016-08-8
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class SiteController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $mod = M('site', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $site_name=I('site_name','','trim');
        $site_code=I('site_code','','trim');

        $site_code && $map['site_code'] = array('eq', $site_code);
        $site_name && $map['site_name'] = array('like', '%'.$site_name.'%');
        $this->assign('search', array(
            'site_code'=>$site_code,
            'site_name' => $site_name
        ));
        return $map;
    }

    public function add() {
        $mod = M('site', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('site', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('site', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }



}
?>
