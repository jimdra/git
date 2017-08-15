<?php
/**
 * 后台控制器基类
 *
 * @author tomson
 */
namespace Admin\Controller;
use Think\Controller;
class BaseController extends Controller {
    protected $_name = '';
    protected $menuid = 0;
    public function _initialize() {
        $this->_name = CONTROLLER_NAME;
        $this->check_priv();
        $this->menuid = I('menuid', 0, 'trim');
        if ($this->menuid) {
            $sub_menu = D('menu')->sub_menu($this->menuid, $this->big_menu);
            $selected = '';
            foreach ($sub_menu as $key=>$val) {
                $sub_menu[$key]['class'] = '';
                if (CONTROLLER_NAME == $val['module_name'] && ACTION_NAME == $val['action_name'] && strpos(__SELF__, $val['data'])) {
                    $sub_menu[$key]['class'] = $selected = 'on';
                }
            }
            if (empty($selected)) {
                foreach ($sub_menu as $key=>$val) {
                    if (CONTROLLER_NAME == $val['module_name'] && ACTION_NAME == $val['action_name']) {
                        $sub_menu[$key]['class'] = 'on';
                        break;
                    }
                }
            }
            $this->assign('sub_menu', $sub_menu);
            $big_menu = D('menu')->big_menu($this->menuid, true);
            $this->assign('big_menu', $big_menu);
        }
        $this->assign('menuid', $this->menuid);
        $this->assign('controller_name',CONTROLLER_NAME);
        $this->assign('action_name',ACTION_NAME);

    }


    public function ajaxReturnData($list,$count=0,$footer=array()){
        $data['rows'] = !empty($list) ? $list : '';
        $data['total'] = !empty($count) ? $count : count($list);
        $data['footer'] = array($footer);
        return json_encode($data);
    }


    public function _empty() {
        $this->_404();
    }

    protected function _404($url = '') {
        if ($url) {
            redirect($url);
        } else {
            send_http_status(404);
            $this->display(TMPL_PATH . '404.html');
            exit;
        }
    }

    /**
     * 发送邮件
     */
    public function send_mail($is_sync = true) {
        if (!$is_sync) {
            //异步
            session('async_sendmail', true);
            return true;
        } else {
            //同步
            session('async_sendmail', null);
            return D('mail_queue')->send();
        }
    }

    /**
     * 上传文件默认规则定义
     */
    protected function _upload_init($upload) {
        $allow_max = 5; //上传大小限制8M
        $allow_exts = array('jpg','jpeg','png'); //读取配置
        $allow_max && $upload->maxSize = $allow_max * 1024;   //文件大小限制
        $allow_exts && $upload->allowExts = $allow_exts;  //文件类型限制
        $upload->saveRule = 'uniqid';
        return $upload;
    }

    /**
     * 上传文件
     */
    protected function _upload($file, $dir = '', $thumb = array(), $save_rule='uniqid') {
        import("@.ORG.UploadFile");
        $upload = new \UploadFile();

        if ($dir) {
            $upload->savePath = $dir;
        }

        if ($thumb) {
            $upload->thumb = true;
            $upload->thumbMaxWidth = $thumb['width'];
            $upload->thumbMaxHeight = $thumb['height'];
            $upload->thumbPrefix = '';
            $upload->thumbSuffix = isset($thumb['suffix']) ? $thumb['suffix'] : '_thumb';
            $upload->thumbExt = isset($thumb['ext']) ? $thumb['ext'] : '';
            $upload->thumbRemoveOrigin = isset($thumb['remove_origin']) ? true : false;
        }
        //自定义上传规则
        $upload = $this->_upload_init($upload);
        if( $save_rule!='uniqid' ) {
            $upload->saveRule = $save_rule;
        }

        if ($result = $upload->uploadOne($file)) {
            return array('error'=>0, 'info'=>$result);
        } else {
            return array('error'=>1, 'info'=>$upload->getErrorMsg());
        }
    }

    /**
     * AJAX返回数据标准
     *
     * @param int $status
     * @param string $msg
     * @param mixed $data
     * @param string $dialog
     */
    protected function ajaxReturn($status=1, $msg='', $data='', $dialog='') {
        parent::ajaxReturn(array(
                'status' => $status,
                'msg' => $msg,
                'data' => $data,
                'dialog' => $dialog,
        ));
    }

    /**
     * 列表页面
     */
    public function index($model='') {
        $mod = D($this->_name);
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }


    public function add() {
        $mod = D($this->_name);
        !empty($mod) && $this->_add($mod);
    }

    /**
     * 添加
     */
    public function _add($mod,$template='add') {
        $menuid = $_REQUEST['menuid'];
        if (IS_POST) {
            unset ($_POST['menuid']);

            if (false === $data = $mod->create($_POST)) {
                $this->error ( $model->getError () );
            }

            if (method_exists($this, '_before_insert')) {
                $data = $this->_before_insert($data);
            }
            if( $mod->add($data) ) {
                if( method_exists($this, '_after_insert')) {
                    $id = $mod->getLastInsID();
                    $this->_after_insert($id);
                }
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'add');
                $this->success(L('operation_success'),U(CONTROLLER_NAME.'/index',array('menuid'=>$menuid)));
            } else {

                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            $this->assign('open_validator', true);
            $this->assign('menuid',$_REQUEST['menuid']);
            if (IS_AJAX) {
                $response = $this->fetch($template);
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display($template);
            }
        }
    }

    public function edit() {
        $mod = D($this->_name);
        !empty($mod) && $this->_edit($mod);
    }

    /**
     * 修改
     */
    public function _edit($mod,$template='edit') {

        $pk = $mod->getPk();
        $search = I('search');
        $menuid = $_REQUEST['menuid'];
        if (IS_POST) {
            unset ($_POST['menuid']);
            if (false === $data = $mod->create($_POST)) {
                $this->error ( $model->getError () );
            }

            if (method_exists($this, '_before_update')) {
                $data = $this->_before_update($data);
            }
            if (false !== $mod->save($data)) {
                if( method_exists($this, '_after_update')) {
                    $id = $data['id'];
                    $this->_after_update($id);
                }
                if(!empty($search)) {
                    $search = unserialize(urldecode($search));
                }else {
                    $search = array();
                }
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'), '', 'edit');
                $this->success(L('operation_success'),U(CONTROLLER_NAME.'/index',array_merge(array('menuid'=>$menuid),$search)));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            $id = I($pk, 'intval');
            $info = $mod->find($id);
            $this->assign('info', $info);
            $this->assign('open_validator', true);
            $this->assign('search',$search);
            $this->assign('menuid',$menuid);
            if (IS_AJAX) {
                $response = $this->fetch($template);
                $this->ajaxReturn(1, '', $response);
            } else {
                $this->display($template);
            }
        }
    }

    public function ajax_edit() {
        $mod = D($this->_name);
        !empty($mod) && $this->_ajax_edit($mod);
    }

    /**
     * ajax修改单个字段值
     */
    public function _ajax_edit($mod) {
        //AJAX修改数据
        $pk = $mod->getPk();
        $id = I($pk, 'intval');
        $field = I('field', 'trim');
        $val = I('val', 'trim');
        //允许异步修改的字段列表  放模型里面去 TODO
        $mod->where(array($pk=>$id))->setField($field, $val);
        $this->ajaxReturn(1);
    }


    public function delete() {
        $mod = D($this->_name);
        !empty($mod) && $this->_delete($mod);
    }

    /**
     * 删除
     */
    public function _delete($mod) {
        $pk = $mod->getPk();
        $ids = trim(I($pk), ',');
        if ($ids) {
            if (false !== $mod->delete($ids)) {
                IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
                $this->success(L('operation_success'));
            } else {
                IS_AJAX && $this->ajaxReturn(0, L('operation_failure'));
                $this->error(L('operation_failure'));
            }
        } else {
            IS_AJAX && $this->ajaxReturn(0, L('illegal_parameters'));
            $this->error(L('illegal_parameters'));
        }
    }

    /**
     * 获取请求参数生成条件数组
     */
    protected function _search($mod) {
        //生成查询条件
        $map = array();
        foreach ($mod->getDbFields() as $key => $val) {
            if (substr($key, 0, 1) == '_') {
                continue;
            }
            if (I($val)) {
                $map[$val] = I($val);
            }
        }
        return $map;
    }

    /**
     * 列表处理
     *
     * @param obj $model  实例化后的模型
     * @param array $map  条件数据
     * @param string $sort_by  排序字段
     * @param string $order_by  排序方法
     * @param string $field_list 显示字段
     * @param intval $pagesize 每页数据行数
     */
    protected function _list($model, $map = array(), $sort_by='', $order_by='', $field_list='*', $pagesize_list='') {
        //排序
        $mod_pk = $model->getPk();
        $sort=trim($_REQUEST['sort']);
        $order=trim($_REQUEST['order']);
        $listRows=trim($_REQUEST['listRows']);
        if ($sort) {
            $sort = $sort;
        } else if (!empty($sort_by)) {
            $sort = $sort_by;
        } else if ($this->sort) {
            $sort = $this->sort;
        }
        if ($order) {
            $order = $order;
        } else if (!empty($order_by)) {
            $order = $order_by;
        } else if ($this->order) {
            $order = $this->order;
        } else {
            $order = $mod_pk;
        }

        if ($listRows) {
            $pagesize = $listRows;
        } else if (!empty($pagesize_list)) {
            $pagesize = $pagesize_list;
        } else if ($this->listRows) {
            $pagesize = $this->listRows;
        } else {
            $pagesize = 20;   //默认分页数
        }

        //如果需要分页
        if ($pagesize) {
            $count = $model->where($map)->count($mod_pk);
            import("@.ORG.Page");
            $pager = new \Page($count, $pagesize);
        }
        $select = $model->field($field_list)->where($map)->order($order . ' ' . $sort);

        $this->list_relation && $select->relation(true);
        if ($pagesize) {
            $select->limit($pager->firstRow.','.$pager->listRows);
            $page = $pager->show();
            $this->assign("page", $page);
        }
        $list = $select->select();
        if (method_exists($this, '_before_list')) {
            $list = $this->_before_list($list);
        }
        $this->assign('list', $list);
        $this->assign('list_table', true);
        return $list;
    }
    
    //更新站点首页
    public function create_index($site_id) {

        $index="1";    //定义为首页
        $this->assign('index',$index);

        $this->assign('site_id',$site_id);
        $siteData = M('site', null, 'web')->where(array('site_id'=>$site_id))->find();
        $site_code = $siteData['site_code'];
        $this->assign('site_code',$site_code);
        $this->assign('site',$siteData);


        $this->assign ('seo_title',$siteData['site_title']);
        $this->assign ('seo_keywords',$siteData['keyword']);
        $this->assign ('seo_description',$siteData['description']);

        if(!file_exists('./templates/'.$site_code.'/pc/index'.C('TMPL_TEMPLATE_SUFFIX'))) {
            $this->error('<<'.$siteData['site_name'].'>>'.'主页模板不存在');
        }
        if ($siteData['web_url']) {
            $this->buildHtml('index','../html/m.'.$site_code.'/','./templates/'.$site_code.'/mobile/index'.C('TMPL_TEMPLATE_SUFFIX'));
        }
        $r=$this->buildHtml('index','../html/'.$site_code.'/','./templates/'.$site_code.'/pc/index'.C('TMPL_TEMPLATE_SUFFIX'));
        if($r) return true;
    }

    //更新站点列表页
    public function create_list($catid,$site_id,$p=1,$count=0) {

        $catid =intval($catid);
        if(empty($catid)) $this->success (L('do_empty'));
        $cat = M('category',null,'web')->where(array('cat_id'=>$catid))->find();
        $this->assign($site_id);
        $this->assign('pc',1);
        $siteData = M('site',null,'web')->where('site_id='.$site_id)->find();
        $site_code = $siteData['site_code'];
        $this->assign('cat', $cat);
        $this->assign('site_code',$site_code);
        $this->assign('site',$siteData);
        $this->assign('site_id',$siteData['site_id']);

        $urlrule = $this->geturl($cat,'',$site_code);
        $url= ($p > 1 ) ? $urlrule[1] :  $urlrule[0];
        $url = str_replace('{$page}', $p, $url);
        if(strstr($url,C('HTML_FILE_SUFFIX'))) {
            $filename = basename($url,C('HTML_FILE_SUFFIX'));
            $dir = dirname($url).'/';
        }else {
            $filename = 'index';
            $dir= $url.'/';
        }

        $dir = substr($dir,strlen(__ROOT__.'/'));

        $seo_title = $p>1 ? $siteData['site_name'].$cat['cat_name'].'-第'.$p.'页' : $siteData['site_name'].$cat['cat_name'];

        $this->assign ('seo_title',$seo_title);
        $this->assign ('seo_keywords',$siteData['keyword']);
        $this->assign ('seo_description',$siteData['description']);
        if ($cat['ispage']) {
            $template_r = $filename;
        } else {
            $where = " status=1 ";
            if($cat['parent_id'] == 0) {
                $child = M('category',null,'web')->field('group_concat(cat_id) as child')->where(array('parent_id'=>$cat['cat_id']))->find();
                $cat['arrchildid'] = !empty ($child['child']) ? $cat['cat_id'].','.$child['child'] : $cat['cat_id'];
                $where .= " and cat_id in(".$cat['arrchildid'].")";
            }else {
                $where .=  " and cat_id='$catid'";
            }
            $dao = M('article',null,'web');
            if(empty($count))$count = $dao->where($where)->count();
            if($count) {
                import ( "@.ORG.Page" );
                $listRows =  !empty($cat['pagesize']) ? $cat['pagesize'] : 15;
                $page = new \Page ( $count, $listRows, '', '', $p );
                $pageUrlrule[]= str_replace('/html/'.$site_code,'',$urlrule[0]);
                $pageUrlrule[]= str_replace('/html/'.$site_code,'',$urlrule[1]);
                $page->urlrule = $pageUrlrule;
                $page->setConfig('theme', '%first% %upPage% %linkPage% %downPage% %end%');
                $pages = $page->show();
                $list = $dao->where($where)->order('id desc')->limit($page->firstRow . ',' . $page->listRows)->select();
                $this->assign('pages',$pages);
                $this->assign('list',$list);
            }
            $template_r = 'list';
        }
        $template = file_exists('./templates/'.$site_code.'/pc/'.$cat['cat_dir'].C('TMPL_TEMPLATE_SUFFIX')) ? $cat['cat_dir'] : $template_r;
        if ($siteData['web_url']) {
            $web_dir = str_replace('html/'.$site_code, '../html/m.'.$site_code.'', $dir);
            $this->buildHtml($filename,$web_dir,'./templates/'.$site_code.'/mobile/'.$template.C('TMPL_TEMPLATE_SUFFIX'));
        }
        $pc_dir = str_replace('html/'.$site_code, '../html/'.$site_code.'', $dir);
        $r=$this->buildHtml($filename,$pc_dir,'./templates/'.$site_code.'/pc/'.$template.C('TMPL_TEMPLATE_SUFFIX'));

        if($r) return true;
    }

    //更新站点内容页
    public function create_show($id) {

        $p =1;
        $id=intval($id);
        if(empty($id)) $this->success (L('do_empty'));

        $dao= M('article',null,'web');
        $data = $dao->find($id);

        $prevData=$dao->where("cat_id={$data['cat_id']} && id<{$data['id']}")->order('id desc')->limit('1')->find();   //上一篇
        $nextData=$dao->where("cat_id={$data['cat_id']} && id>{$data['id']}")->order('id asc')->limit('1')->find();    //下一篇
        $this->assign('prev', $prevData);
        $this->assign('next', $nextData);


        $site = M('site',null,'web');
        $siteData = $site->where('site_id='.$data['site_id'])->find();
        $site_code = $siteData['site_code'];
        $this->assign('site_code',$site_code);
        $this->assign('site',$siteData);
        $this->assign('site_id',$siteData['site_id']);

        $catid = $data['cat_id'];
        $this->assign('catid',$catid);
        $cat = M('category',null,'web')->where(array('cat_id'=>$catid))->find();
        $this->assign ($cat);

        $seo_title = $data['title'].'_'.$siteData['site_name'].$cat['cat_name'];
        $this->assign ('seo_title',$seo_title);
        $this->assign ('seo_keywords',$data['keyword']);
        $this->assign ('seo_description',$data['description']);
        $this->assign('id',$id);
        $urlrule = $this->geturl($cat,$data,$site_code);

        $template = 'show';

        //手动分页
        $CONTENT_POS = strpos($data['content'], '[page]');
        if($CONTENT_POS !== false) {

            $pageurls=array();
            $contents = array_filter(explode('[page]',$data['content']));
            $pagenumber = count($contents);
            for($i=1; $i<=$pagenumber; $i++) {
                $pageurls[$i] = str_replace('{$page}',$i,$urlrule);
            }
            //生成分页
            foreach ($pageurls as $p=>$urls) {
                $pages = content_pages($pagenumber,$p, $pageurls);
                $this->assign ('pages',$pages);
                $data['content'] = $contents[$p-1];
                $this->assign ($data);
                $url= ($p > 1 ) ? $urls[1] :  $urls[0];
                if(strstr($url,C('HTML_FILE_SUFFIX'))) {
                    $filename = basename($url,C('HTML_FILE_SUFFIX'));
                    $dir = dirname($url).'/';
                }else {
                    $filename = 'index';
                    $dir= $url;
                }
                $dir = substr($dir,strlen(__ROOT__.'/'));
                $this->buildHtml($filename,$dir,'./sites/'.$site_code.'/templates/'.$template.C('TMPL_TEMPLATE_SUFFIX'));
            }
        }else {
            $url = str_replace('{$page}', $p, $urlrule[0]);
            if(strstr($url,C('HTML_FILE_SUFFIX'))) {
                $filename = basename($url,C('HTML_FILE_SUFFIX'));
                $dir = dirname($url).'/';
            }else {
                $filename = 'index';
                $dir= $url;
            }
            $this->assign ('pages','');
            $this->assign ('data',$data);
            $dir = substr($dir,strlen(__ROOT__.'/'));
            if ($siteData['web_url']) {
                $web_dir = str_replace('html/'.$site_code, '../html/m.'.$site_code.'', $dir);
                $this->buildHtml($filename,$web_dir,'./templates/'.$site_code.'/mobile/'.$template.C('TMPL_TEMPLATE_SUFFIX'));
            }
            $pc_dir = str_replace('html/'.$site_code, '../html/'.$site_code.'', $dir);
            $this->buildHtml($filename,$pc_dir,'./templates/'.$site_code.'/pc/'.$template.C('TMPL_TEMPLATE_SUFFIX'));
        }

        return true;
    }
    
    public function geturl($cat,$data='',$site_code='') {

        $id = $data['id'];

        $catid = $cat['cat_id'];
        $parentdir = $cat['parent_id']!=0 ? M('category',null,'web')->where(array('cat_id'=>$cat['parent_id']))->getField('cat_dir') . '/' : '';
        $catdir = $cat['cat_dir'];

        $showurlrule = 'html/{$site_code}/{$parentdir}{$catdir}/{$id}.html|html/{$site_code}/{$parentdir}{$catdir}/{$id}_{$page}.html';
        $listurlrule = 'html/{$site_code}/{$parentdir}{$catdir}|html/{$site_code}/{$parentdir}{$catdir}/p_{$page}.html';

        if(empty($urls)) {
            $index =  __ROOT__.'/';
            if($id) {
                $urls = str_replace(array('{$site_code}','{$parentdir}','{$catdir}','{$catid}','{$id}'),array($site_code,$parentdir,$catdir,$catid,$id),$showurlrule);
            }else {
                $urls = str_replace(array('{$site_code}','{$parentdir}','{$catdir}','{$catid}','{$id}'),array($site_code,$parentdir,$catdir,$catid,$id),$listurlrule);
            }
            $urls = explode('|',$urls);
            $urls[0]=$index.$urls[0];
            $urls[1]=$index.$urls[1];
        }
        return $urls;
    }

    public function check_priv() {
        if ( (!isset($_SESSION['admin']) || !$_SESSION['admin']) ) {
            $this->redirect('Login/index');
        }
        if($_SESSION['admin']['role_id'] == 1) {
            return true;
        }
        if (in_array(CONTROLLER_NAME, explode(',', 'Index,Ajax'))) {
            return true;
        }
        $menu_mod = M('menu');
        $menu_id = $menu_mod->where(array('module_name'=>CONTROLLER_NAME, 'action_name'=>ACTION_NAME))->getField('id');
        //$priv_mod = D('admin_role_priv');
        $priv_mod = M('admin_auth');
        $r = $priv_mod->where(array('menu_id'=>$menu_id, 'role_id'=>$_SESSION['admin']['role_id']))->count();
        if (!$r) {
            $this->error(L('_VALID_ACCESS_'));
        }
    }

    protected function update_config($new_config, $config_file = '') {
        !is_file($config_file) && $config_file = CONF_PATH . 'home/config.php';
        if (is_writable($config_file)) {
            $config = require $config_file;
            $config = array_merge($config, $new_config);
            file_put_contents($config_file, "<?php \nreturn " . stripslashes(var_export($config, true)) . ";", LOCK_EX);
            @unlink(RUNTIME_FILE);
            return true;
        } else {
            return false;
        }
    }

}
