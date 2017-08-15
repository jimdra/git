<?php
/**
 * @desc 友情链接
 * @date 2016-08-26
 * @author zhanjunjie
 */

namespace Web\Controller;
use Think\Controller;
class LinkController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $site_list = M('site', null, 'web')->select();
        $this->assign('site_list', $site_list);
        $mod = M('link', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $keyword=I('keyword','','trim');
        $searchtype=I('searchtype','id','trim');
        $site = I('site',0,'intval');
        if( $_GET['status']==null ) {
            $status = -1;
        }else {
            $status = intval($_GET['status']);
        }
        $site>0 && $map['site_id'] = array('eq',$site);
        $status>=0 && $map['status'] = array('eq',$status);
        if(!empty($keyword) && !empty($searchtype)) {
            $map[$searchtype]=array('like','%'.$keyword.'%');
        }
        $sort=I('sort','','trim');
        $order=I('order','','trim');
        $listRows=I('listRows','','trim');
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'id';
        $this->listRows=!empty($listRows)? $listRows:'20';
        $this->assign('search', array(
                'site'=>$site,
                'status'=>$status,
                'searchtype' => $searchtype,
                'order'=>$this->order,
                'sort'=>$this->sort,
                'listRows'=>$this->listRows,
        ));
        return $map;
    }

    public function add() {
        $site_list = M('site', null, 'web')->select();
        $this->assign('site_list', $site_list);
        $mod = M('link', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit() {
        $site_list = M('site', null, 'web')->select();
        $this->assign('site_list', $site_list);
        $mod = M('link', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('link', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }

    public function _before_insert($data) {
        $data['addtime'] = time();
        $data = checkfield($data);
        return $data;
    }

    public function _before_update($data) {
        $data['addtime'] = time();
        $data = checkfield($data);
        return $data;
    }

    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['site_name'] = M('site', null, 'web')->where(array('site_id'=>$v['site_id']))->getField('site_name');
        }
        return $list;
    }


}
?>
