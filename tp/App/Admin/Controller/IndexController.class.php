<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends BaseController {
    public function _initialize() {
        parent::_initialize();
        $this->_mod = D('menu');
    }

    public function index() {
        $top_menus = $this->_mod->admin_menu(0);
        $this->assign('top_menus', $top_menus);
        $rolename = M('admin_role')->where(array('id'=>$_SESSION['admin']['role_id']))->getField('name');
        $my_admin = array('username'=>$_SESSION['admin']['username'], 'rolename'=>$rolename);
        $this->assign('my_admin', $my_admin);
        $this->display();
    }

    public function panel() {
        $system_info = array(
                'server_domain' => $_SERVER['SERVER_NAME'] . ' [ ' . gethostbyname($_SERVER['SERVER_NAME']) . ' ]',
                'server_os' => PHP_OS,
                'web_server' => $_SERVER["SERVER_SOFTWARE"],
                'php_version' => PHP_VERSION,
                'mysql_version' => mysql_get_server_info(),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'max_execution_time' => ini_get('max_execution_time') . '秒',
                'safe_mode' => (boolean) ini_get('safe_mode') ?  L('yes') : L('no'),
                'zlib' => function_exists('gzclose') ?  L('yes') : L('no'),
                'curl' => function_exists("curl_getinfo") ? L('yes') : L('no'),
                'timezone' => function_exists("date_default_timezone_get") ? date_default_timezone_get() : L('no')
        );
        $this->assign('system_info', $system_info);
        $this->display();
    }

    public function logout() {
        session('admin', null);
        $this->success(L('logout_success'), U('index/login'));
        exit;
    }



    public function left() {
        $menuid = I('menuid');
        if ($menuid) {
            $left_menu = $this->_mod->admin_menu($menuid);
            foreach ($left_menu as $key=>$val) {
                $left_menu[$key]['sub'] = $this->_mod->admin_menu($val['id']);
            }
        } else {
            $left_menu[0] = array('id'=>0,'name'=>L('common_menu'));
            $left_menu[0]['sub'] = array();
            if ($r = $this->_mod->where(array('often'=>1))->select()) {
                $left_menu[0]['sub'] = $r;
            }
            array_unshift($left_menu[0]['sub'], array('id'=>0,'name'=>L('common_menu_set'),'module_name'=>'index','action_name'=>'often_menu'));
        }
        $this->assign('left_menu', $left_menu);
        $this->display();
    }

    public function often() {
        if (isset($_POST['do'])) {
            $id_arr = isset($_POST['id']) && is_array($_POST['id']) ? $_POST['id'] : '';
            $this->_mod->where(array('ofen'=>1))->save(array('often'=>0));
            $id_str = implode(',', $id_arr);
            $this->_mod->where('id IN('.$id_str.')')->save(array('often'=>1));
            $this->success(L('operation_success'));
        } else {
            $r = $this->_mod->admin_menu(0);
            $list = array();
            foreach ($r as $v) {
                $v['sub'] = $this->_mod->admin_menu($v['id']);
                foreach ($v['sub'] as $key=>$sv) {
                    $v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
                }
                $list[] = $v;
            }
            $this->assign('list', $list);
            $this->display();
        }
    }

    public function edit() {
        $mod=M('admin');
        $id = $_SESSION['admin']['id'];
        if (IS_POST) {
            $password=$mod->where('id='.$id)->getField('password');
            if(md5($_POST['oldPassword'])!=$password) {
                IS_AJAX && $this->ajaxReturn(0, L('old_password_error'));
                $this->error(L('old_password_error'));
            }
            if(strlen($_POST['Password'])<8){
                IS_AJAX && $this->ajaxReturn(0, L('密码不能小于8位！'));
                $this->error(L('密码不能小于8位'));
            }
            if(preg_match('/^\d*$/',$_POST['Password'])){
                IS_AJAX && $this->ajaxReturn(0, L('密码不能全数字！'));
                $this->error(L('密码不能全数字'));
            }
            if($_POST['Password']!=$_POST['repassword']){
                IS_AJAX && $this->ajaxReturn(0, L('两次密码不一致！'));
                $this->error(L('两次密码不一致'));
            }
            if(!empty($_POST['Password'])) {
                $data['password']=md5($_POST['Password']);
                if (false !== $mod->where('id='.$id)->save($data)) {
                    IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'edit');
                    $this->success(L('operation_success'));
                    R('Index/logout');
                } else {
                    IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                    $this->error(L('operation_failure'));
                }
            }else {
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'edit');
                $this->success(L('operation_success'));
                R('Index/logout');
            }
        } else {
            $info = $mod->find($id);
            $this->assign('info', $info);
            $this->assign('open_validator', true);
            if (IS_AJAX) {
                $response = $this->fetch();
                $this->ajaxReturn(1, '', $response);
            }
        }
    }

    public function map() {
        $r = $this->_mod->admin_menu(0);
        $list = array();
        foreach ($r as $v) {
            $v['sub'] = $this->_mod->admin_menu($v['id']);
            foreach ($v['sub'] as $key=>$sv) {
                $v['sub'][$key]['sub'] = $this->_mod->admin_menu($sv['id']);
            }
            $list[] = $v;
        }
        $this->assign('list', $list);
        $this->display();
    }

    public function repair() {
        $mod=M('menu');
        $data=$mod->order("id asc")->select();
        foreach($data as $v) {
            if($v['pid']==0) {
                $mod->where("id=".$v['id'])->save(array('level'=>0));
            }else {
                $level=$mod->where("id=".$v['pid'])->getField('level');
                $level=$level+1;
                $mod->where("id=".$v['id'])->save(array('level'=>$level));
            }
        }
    }

    public function cache($status='') {
        if(is_file(RUNTIME_PATH.'common~runtime.php'))@unlink(RUNTIME_PATH.'common~runtime.php');
        //is_dir(DATA_PATH . '_fields/') && $this->deldir(DATA_PATH . '_fields/');
        //$filePath = "./Public/excel/property.xls";   //道具表路径
        //$list = getExcelData($filePath,'id','start','end');
        $list[] = array('id'=>'prestige','name'=>'竞技声望');
        $list[] = array('id'=>'energy','name'=>'体力');
        //F('property',$list); // 缓存写入
        $filePathMonster = "./Public/excel/monster.xls";   //统帅表路径
        //$Monsterlist = getExcelData($filePathMonster,'id','start','end');
        //F('monster',$Monsterlist); // 缓存写入
        $this->repair();   //修复菜单等级
        $this->ajaxReturn(1, L('clear_success'));
    }
}




