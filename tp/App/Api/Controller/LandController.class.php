<?php
/**
* @desc 落地页Api
* @date 2017-07-11
* @author zhanjunjie
*/

namespace Api\Controller;
use Think\Controller;
class LandController extends Controller {

    protected $land_id = '';

    public function __construct() {
        parent::__construct();
        $this->land_id = intval($_REQUEST['land_id']);
    }

    public function land_log() {
        $type = I('type');
        $ip = get_client_ip();
        
        $data = array(
            'land_id'=>$this->land_id,
            'type'=>$type,
            'ip'=>$ip,
            'addtime'=> time()
        );
        M('land_log')->add($data);
        $status = 1;
        $callback = $_GET['callback'];
        echo $callback.'('.json_encode(array('status'=>$status)).')';exit;
    }

}
?>
