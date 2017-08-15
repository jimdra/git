<?php
/**
 *
 * @author tomson
 */

namespace Admin\Controller;
use Think\Controller;
class AjaxController extends Controller {

    //获取游戏平台列表
    public function platformlist() {
        $list = M('game_platform')->field('platform_id as id,platform_name as text')->select();
        echo json_encode($list);
    }

    //获取游戏服列表
    public function serverlist(){
        $platform_id = I('pid');
        $list = M('game_server')->field('server_id as id,server_name as text')->where("platform_id=".$platform_id)->select();
        echo json_encode($list);
    }





}
?>
