<?php
/**
* @desc 游戏管理
* @date 2017-07-14
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class GameController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $mod = M('game', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'game_id';
        return $map;
    }

    public function add() {
        $mod = M('game', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('game', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('game', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }
    
}
?>
