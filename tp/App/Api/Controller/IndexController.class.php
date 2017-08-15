<?php
/**
* @desc Api
* @date 2017-07-13
* @author zhanjunjie
*/

namespace Api\Controller;
use Think\Controller;
class IndexController extends BaseController {

    public function __construct() {
        parent::__construct();
        
    }

    public function index() {
        print_r(session('user'));
        echo '<br/>';
        echo '<a href="/index.php/Api/Index/logout">退出</a>';
    }
    
    public function logout() {
        session('user', null);
        $this->success(L('logout_success'), U('Index/index',array('site_id'=>1)));
        exit;
    }

}
?>
