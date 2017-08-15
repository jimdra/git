<?php
/**
* @desc 素材管理
* @date 2017-07-17
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class MaterialController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $game_list = M('game')->select();
        $this->assign('game_list',$game_list);
        $type_list = M('material_type')->select();
        $this->assign('type_list',$type_list);
    }
    
    public function index() {
        $mod = M('material', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $keyword=I('keyword','','trim');
        $searchtype=I('searchtype','','trim');
        $game_id = I('game_id',0,'intval');
        $game_id>0 && $map['game_id'] = array('eq',$game_id);
        if(!empty($keyword) && !empty($searchtype)) {
            $map[$searchtype]=array('like','%'.$keyword.'%');
        }
        //print_r($map);die;
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'material_id';
        $this->assign('search', array(
                'game_id'=>$game_id,
                'searchtype' => $searchtype,
                'order'=>$this->order,
                'sort'=>$this->sort,
                'listRows'=>$this->listRows,
        ));
        return $map;
    }

    public function add() {
        $mod = M('material', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('material', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('material', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }
    
    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['game_name'] = M('game')->where(array('game_id'=>$v['game_id']))->getField('game_name');
            $list[$k]['type_name'] = M('material_type')->where(array('type_id'=>$v['type_id']))->getField('type_name');
        }
        return $list;
    }
    
    public function _before_insert($data) {
        $data['adduser'] = session('admin.username');
        $data['addtime'] = time();
        $data = material($data);
        return $data;
    }
    
    public function _before_update($data) {
        $data['adduser'] = session('admin.username');
        $data['addtime'] = time();
        $data = material($data);
        return $data;
    }
    
    public function getMaterial() {
        if (IS_AJAX) {
            $material_id = I('material_id');
            $material = M('material')->where(array('material_id'=>$material_id))->find();
            
            $this->ajaxReturn(1, '', $material['material_thumb']);
        }
    }
    
}
?>
