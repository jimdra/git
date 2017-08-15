<?php
/**
* @desc 素材分类
* @date 2017-07-31
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class MaterialTypeController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $mod = M('material_type', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'type_id';
        return $map;
    }

    public function add() {
        $mod = M('material_type', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('material_type', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('material_type', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }
    
}
?>
