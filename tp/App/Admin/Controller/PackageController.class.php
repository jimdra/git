<?php
/**
* @desc 礼包管理
* @date 2016-10-14
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class PackageController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $site_list = M('site', null, 'web')->where(array('status'=>1))->select();
        $this->assign('site_list', $site_list);
        $mod = M('wx_package', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $site_id=I('site_id');
        $site_code=I('code','','trim');
        $is_active=I('is_active');

        $site_id && $map['site_id'] = array('eq', $site_id);
        $site_code && $map['code'] = array('eq', $site_code);
        $map['is_active'] = array('eq', $is_active);
        $this->assign('search', array(
            'site_id'=>$site_id,
            'code' => $site_code,
            'is_active'=>$is_active
        ));
        return $map;
    }

    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['site_name'] = M('site', null, 'web')->where(array('site_id'=>$v['site_id']))->getField('site_name');
            switch ($v['type']) {
                case 1:
                    $list[$k]['type_name'] = L('ios');
                    break;
                 case 2:
                    $list[$k]['type_name'] = L('mix');
                    break;
                 case 3:
                    $list[$k]['type_name'] = L('app');
                    break;
            }

            $list[$k]['active_time'] = $v['active_time'] > 0 ? date("Y-m-d H:i:s",$v['active_time']) : '';
            $list[$k]['status'] = $v['is_active']==1 ? '已领取' : '未领取';
        }
        return $list;
    }

    public function add() {
        if(IS_POST){
            if(isset($_FILES["import"]) && ($_FILES["import"]["error"] == 0)){
                $ext = pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION);
                $result = $this->importExecl($_FILES["import"]["tmp_name"],$ext);
                if(!empty ($result)){
                    foreach ($result as $k => $v) {
                        $data[] = array(
                            'site_id' => I('site_id'),
                            'code' => $v['A'],
                            'type'=>I('type')
                        );
                    }
                    $ret = M('wx_package',null,'web')->addAll($data);
                }
            }
            if($ret !== false){
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
                $this->success(L('operation_success'));
            }else{
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        }else{
            $site_list = M('site', null, 'web')->where(array('status'=>1))->select();
            $this->assign('site_list', $site_list);
            if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display();
            }
        }
    }

    public function edit(){
        $mod = M('site', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('wx_package', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }



}
?>
