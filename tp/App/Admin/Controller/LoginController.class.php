<?php
/**
 * 后台登陆
 *
 * @author zhanjunjie
 */

namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
    public function index() {
        $this->display();
    }

    public function dologin($username = null, $password = null, $verify = null) {
        /* 检测验证码 TODO: */
        if(md5($verify)!=$_SESSION['verify']) {
            $this->error('验证码输入错误！');
        }
        $admin = M('admin')->where(array('username'=>$username, 'status'=>1))->find();
        $time =time();
        $ip=get_client_ip();
        if (!$admin) {
            $this->error('登陆失败,不存在该用户！');
        }
        if ($admin['password'] != md5($password)) {
            $this->error('登陆失败,密码错误！');
        }
        if(preg_match('/^\d*$/',$password)) {
            $this->error(L('您的密码不安全，请联系管理员修改！'));
        }

        session('admin', array(
                'id' => $admin['id'],
                'role_id' => $admin['role_id'],
                'username' => $admin['username'],
                'realname'=>$admin['realname'],
        ));
        M('admin')->where(array('id'=>$admin['id']))->save(array('last_time'=>time(), 'last_ip'=>get_client_ip(),'login_count'=>array('exp','login_count+1')));
        $this->success('登录成功！', U('Index/index'));
    }


    
}
?>
