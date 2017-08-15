<?php
/**
* @desc 幻灯片管理
* @date 2016-11-15
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class SlideController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $site_list = M('site', null, 'web')->where(array('status'=>1))->select();
        $this->assign('site_list', $site_list);
    }
    
    public function index() {
        $mod = M('slide', null, 'web');
        //$map = $this->_search($mod);
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
        $mod = M('slide', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('slide', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('slide', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }

    public function picmanage(){
        $slide_id = I('slide_id');
        $slide = M('Slide', null, 'web')->where(array('id'=>$slide_id))->find();
        $list = M('slide_data', null, 'web')->where(array('slide_id'=>$slide_id))->order("id DESC")->select();
        $this->assign('slide',$slide);
        $this->assign('list',$list);
        $this->display();
    }

    public function addpic(){
        $slide_id = I('slide_id');
        $site_id = M('slide',null,'web')->where(array('id'=>$slide_id))->getField('site_id');
        if (IS_POST) {
            $data = array(
                'site_id'=>$site_id,
                'slide_id'=>$slide_id,
                'title'=>I('title'),
                'icon'=>I('icon'),
                'thumb'=>I('thumb'),
                'img'=>I('img'),
                'link'=>I('link'),
                'description'=>I('description'),
                'status'=>I('status')
            );
            $data = checkfield($data);
            unset($data['site_id']);
            $ret = M('slide_data', null, 'web')->add($data);
            if ($ret) {
                $this->success(L('operation_success'),U(CONTROLLER_NAME.'/picmanage',array('slide_id'=>$slide_id)));
            } else {
                $this->error(L('operation_failure'));
            }
        } else {
            $slide = M('slide', null, 'web')->find($slide_id);
            $this->assign('slide', $slide);
            $this->display('editpic');
        }
    }

    public function editpic(){
        $id = I('id');
        $site_id = M('slide',null,'web')->where(array('id'=>$slide_id))->getField('site_id');
        if (IS_POST) {
            $slide_id = I('slide_id');
            $data = array(
                'site_id'=>$site_id,
                'title'=>I('title'),
                'icon'=>I('icon'),
                'thumb'=>I('thumb'),
                'img'=>I('img'),
                'link'=>I('link'),
                'description'=>I('description'),
                'status'=>I('status')
            );
            $data = checkfield($data);
            unset($data['site_id']);
            $ret = M('slide_data', null, 'web')->where(array('id'=>$id))->save($data);
            if ($ret!==false) {
                $this->success(L('operation_success'),U(CONTROLLER_NAME.'/picmanage',array('slide_id'=>$slide_id)));
            } else {
                $this->error(L('operation_failure'));
            }
        } else {
            $info = M('slide_data',null,'web')->find($id);
            $slide = M('slide', null, 'web')->find($info['slide_id']);
            $this->assign('slide', $slide);
            $this->assign('info',$info);
            $this->display();
        }
    }

    public function deletepic(){
        $mod = M('slide_data', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }

}
?>
