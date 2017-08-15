<?php
/**
* @desc 渠道管理
* @date 2017-08-11
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class ChannelController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $mod = M('channel', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }
    
    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['platform'] = $v['platform_id'] == 1 ? 'iOS' : 'android';
        }
        return $list;
    }

    protected function _search($mod) {
        $map = array();
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'channel_id';
        return $map;
    }

    public function add() {
        $mod = M('channel', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('channel', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('channel', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }
    
}
?>
