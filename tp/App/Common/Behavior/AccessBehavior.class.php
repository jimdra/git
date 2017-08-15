<?php
/**
 * 用户访问日志行为
 * @author Huaqiang Zhong <huaqiangs@live.com>
 */
namespace Common\Behavior;
use Common\Common\User;

class AccessBehavior extends \Think\Behavior {

    public function run(&$return) {
        $access = array();
        $access['username'] = $_SESSION['admin']['username'];
        $access['module'] = MODULE_NAME;
        $access['controller'] = CONTROLLER_NAME;
        $access['action'] = ACTION_NAME;
        $access['act_time'] = time();
        $access['ip'] = get_client_ip();
        $access['params'] = json_encode($_REQUEST);
        M('admin_access_log')->add($access);
    }
}