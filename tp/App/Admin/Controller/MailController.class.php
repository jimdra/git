<?php
/**
 * 邮件发放
 * date 2016-7-20
 * @author tomson
 */

namespace Admin\Controller;
use Think\Controller;
class MailController extends BaseController {

    public function record() {
        if(IS_AJAX) {
            $data = D('Mail')->getMailList($_REQUEST);
            echo $this->ajaxReturnData($data['rows'],$data['total']);
        }else {
            $this->display();
        }
    }

    public function full_service(){
        if(IS_POST){
            $_REQUEST['mail_to'] = 1;   //1代表全服
            $data = D('Mail')->mailTo($_REQUEST);
            if(is_array($data) && $data['code']==0){
                D('Mail')->addMailLog($_REQUEST);
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
                $this->success(L('operation_success'));
            }else{
                IS_AJAX && $this->ajaxReturn(0, $data['data']);
                $this->error($data['data']);
            }
        }else{
            $this->display();
        }
    }


    public function specified(){
        if(IS_POST){
            $_REQUEST['mail_to'] = 3;   //3代表指定玩家
            $data = D('Mail')->mailTo($_REQUEST);
            if(is_array($data) && $data['code']==0){
                $playerStr = $_REQUEST['player_info'];
                $playerList = explode(',',$playerStr);
                foreach($playerList as $v) {
                    $_REQUEST['player'] = $v;
                    D('Mail')->addMailLog($_REQUEST);
                }
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
                $this->success(L('operation_success'));
            }else{
                IS_AJAX && $this->ajaxReturn(0, $data['data']);
                $this->error($data['data']);
            }
        }else{
            $this->display();
        }
    }

}
?>