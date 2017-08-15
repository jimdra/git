<?php
namespace Admin\Controller;
use Think\Controller;
class AdminController extends BaseController
{
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('admin');
    }
    
    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['rolename'] = M('admin_role')->where(array('id'=>$v['role_id']))->getField('name');
        }
        return $list;
    }

    public function _before_index() {
        $this->list_relation = true;
    }

    public function _before_add() {
        $role_list = M('admin_role')->where('status=1')->select();
        $this->assign('role_list', $role_list);
    }

    public function _before_insert($data='') {
        if( ($data['password']=='')||(trim($data['password']=='')) ){
            unset($data['password']);
        }else{
            if(strlen($_POST['password'])<8){
                IS_AJAX && $this->ajaxReturn(0, L('密码不能小于8位！'));
                $this->error(L('密码不能小于8位'));
            }
            if(preg_match('/^\d*$/',$_POST['password'])){
                IS_AJAX && $this->ajaxReturn(0, L('密码不能全数字！'));
                $this->error(L('密码不能全数字'));
            }
            $data['password'] = md5($data['password']);
            unset ($data['repassword']);
        }
        return $data;
    }

    public function _before_edit() {
        $this->_before_add();
    }

    public function _before_update($data=''){
        if(trim($data['password']=='')){
            unset($data['password']);
        }else{
            if(strlen($_POST['password'])<8){
                IS_AJAX && $this->ajaxReturn(0, L('密码不能小于8位！'));
                $this->error(L('密码不能小于8位'));
            }
            if(preg_match('/^\d*$/',$_POST['password'])){
                IS_AJAX && $this->ajaxReturn(0, L('密码不能全数字！'));
                $this->error(L('密码不能全数字'));
            }
            $data['password'] = md5($data['password']);
            unset ($data['repassword']);
        }
        return $data;
    }

    public function ajax_check_name() {
        $name = trim($_REQUEST['username']);
        $id = intval($_REQUEST['id']);
        if ($this->_mod->name_exists($name, $id)) {
            echo 0;
        } else {
            echo 1;
        }
    }


    

}