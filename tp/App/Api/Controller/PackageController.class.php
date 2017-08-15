<?php
/**
* @desc 礼包Api
* @date 2016-10-18
* @author zhanjunjie
*/

namespace Api\Controller;
use Think\Controller;
class PackageController extends Controller {

    protected $role_id = '';
    protected $code = '';
    protected $site_id = '';

    public function __construct() {
        parent::__construct();
        $this->site_id = intval($_REQUEST['site_id']);
        $this->role_id = intval($_REQUEST['role_id']);
        $this->code = $_REQUEST['code'];
    }

    public function code() {
        $site_info = M('site',null,'web')->where(array('site_id'=>$this->site_id))->find();
        $db = $site_info['site_code'].'_data';
        $data = array(
            'is_used'=>1,
            'use_time'=>time(),
            'role_id'=>$this->role_id
        );
        $pack_id = M('api_package_code',null,$db)->where(array('code'=>$this->code))->getField('pack_id');
        $pack_info = M('api_package',null,$db)->where(array('id'=>$pack_id))->find();
        if ($pack_info['is_exclude']==1) {
            $ret = M('api_package_code',null,$db)->where(array('code'=>$this->code))->save($data);
        } else {
            $data['pack_id'] = $pack_info['id'];
            $data['code'] = $this->code;
            $data['addtime'] = time();
            $ret = M('api_package_code',null,$db)->add($data);
        }

        $status = $ret ? 1 : 0;
        echo json_encode(array('status'=>$status));exit;
    }

}
?>
