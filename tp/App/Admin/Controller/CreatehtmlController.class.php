<?php
/**
 * @desc 生成静态
 * @date 2016-08-15
 * @author zhanjunjie
 */

namespace Admin\Controller;
use Think\Controller;
class CreatehtmlController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $site_list = M('site', null, 'web')->where(array('status'=>1))->select();
        $this->assign('site_list', $site_list);
    }

    public function index() {

        $this->display();
    }

    public function doindex() {
        $site_id = intval($_GET['site_id']);
        if($site_id=='0') {
            $this->error(L('no_site'));
        }
        $this->create_index($site_id);

        $this->assign ( 'jumpUrl', U(CONTROLLER_NAME.'/index') );
        $this->success(L('index_create_OK'));
    }

    public function createlist() {
        $site_id = I('site_id',0,'intval');
        $result = M('category', null, 'web')->where(array('site_id'=> $site_id))->select();
        $array = array();
        foreach($result as $r) {
            $r['cname'] = $r['cat_name'];
            $r['pid'] = $r['parent_id'];
            $r['id'] = $r['cat_id'];
            $r['disabled'] = M('category', null, 'web')->where(array('parent_id'=>$r['cat_id']))->count() ? 'disabled' : '';
            $array[] = $r;
        }
        import("@.ORG.Tree");
        $tree = new \Tree();
        $str  = "<option value='\$id'  \$disabled>\$spacer \$cname</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign('select_categorys', $select_categorys);
        $this->assign('site_id', $site_id);
        $this->display('show');

    }

    public function doCreatelist() {
        $this->assign ( 'waitSecond', 0);
        extract($_GET,EXTR_SKIP);
        $site_id = I('site_id',0,'intval');
        if($site_id=='0') {
            $this->error(L('no_site'));
        }
        $site_name = M('site',null,'web')->where('site_id='.$site_id)->getField('site_name');

        $doid = $doid ? intval($doid) : 0;
        $count = intval($_GET['count']);
        if($dosubmit!=1) {
            $catids=array();
            if($_GET['catids'][0]) {
                $catids = $_SESSION['catids'] = $_GET['catids'];
            }else {
                $cate_list = M('category', null, 'web')->where(array('site_id'=> $site_id))->select();
                foreach($cate_list as $id=>$cat) {
                    $catids[] = $cat['cat_id'];
                }
                $catids = $_SESSION['catids'] = $catids;
            }
        }else {
            $catids =$_SESSION['catids'];
        }
        if(!isset($catids[$doid])) {
            unset($_SESSION['catids']);
            $forward = U("Createhtml/createlist");
            $this->assign ( 'jumpUrl', $forward);
            $this->success(L('create_update_success'));
        }else {
            $id = $catids[$doid];
            if(empty($count)) {
                $where = " status=1 ";
                $cat = M('category',null,'web')->where(array('cat_id'=>$id))->find();
                if($cat['parent_id'] == 0) {
                    $child = M('category',null,'web')->field('group_concat(cat_id) as child')->where(array('parent_id'=>$cat['cat_id']))->find();
                    $cat['arrchildid'] = !empty ($child) ? $cat['cat_id'].','.$child['child'] : $cat['cat_id'];
                    $where .= " and cat_id in(".$cat['arrchildid'].")";
                }else {
                    $where .=  " and cat_id='$id'";
                }
                $dao= M('article', null, 'web');
                $count = $dao->where($where)->count();
            }
            if(empty($pages)) {
                $cat_pagesize = 15;
                $pages = ceil($count/$cat_pagesize);
            }

            $p = max(intval($p), 1);
            $j = 1;
            do {
                $this->create_list($id,$site_id,$p,$count);
                $j++;
                $p++;
            } while ($p <= $pages && $j < $pagesize);
            $cat = M('category',null,'web')->where(array('cat_id'=>$id))->find();
            if($p <= $pages) {
                $endpage = intval($p+$pagesize);
                $percent = round($p/$pages, 2)*100;
                $urlarray=array(
                        'count' => $count,
                        'doid' => $doid,
                        'dosubmit' => 1,
                        'pages' => $pages,
                        'p' => $p,
                        'pagesize' => $pagesize,
                        'iscreatehtml'=>1,
                        'site_id'=>$site_id,
                );
                $message = L('updating').'<<'.$site_name.'>>'.$cat['cat_name'].L('create_update_count').$pages.L('create_update_list_num').$p.L('items_list').$percent.L('items1');
                $forward = U("Createhtml/".ACTION_NAME,$urlarray);
            } else {
                $doid++;
                $urlarray=array(
                        'doid' => $doid,
                        'dosubmit' => 1,
                        'p' => 1,
                        'pagesize' => $pagesize,
                        'iscreatehtml'=>1,
                        'site_id'=>$site_id,
                );
                $message = L('start_updating').'<<'.$site_name.'>>'.$cat['cat_name']." ...";
                $forward = U("Createhtml/".ACTION_NAME,$urlarray);
            }
            $this->assign ( 'jumpUrl', $forward);
            $this->success($message);
        }
    }

    public function createshow() {
        $site_id = I('site_id',0,'intval');
        $result = M('category', null, 'web')->where(array('site_id'=> $site_id))->select();
        $array = array();
        foreach($result as $r) {
            $r['cname'] = $r['cat_name'];
            $r['pid'] = $r['parent_id'];
            $r['id'] = $r['cat_id'];
            $r['disabled'] = M('category', null, 'web')->where(array('parent_id'=>$r['cat_id']))->count() ? 'disabled' : '';
            $array[] = $r;
        }
        import("@.ORG.Tree");
        $tree = new \Tree();
        $str  = "<option value='\$id'  \$disabled>\$spacer \$cname</option>";
        $tree->init($array);
        $select_categorys = $tree->get_tree(0, $str);
        $this->assign('select_categorys', $select_categorys);
        $this->assign('site_id', $site_id);
        $this->display('show');
    }

    public function doCreateshow() {

        $this->assign ( 'waitSecond', 0);
        extract($_GET,EXTR_SKIP);
        $site_id = I('site_id',0,'intval');
        if($site_id=='0') {
            $this->error(L('no_site'));
        }
        $site_name = M('site',null,'web')->where('site_id='.$site_id)->getField('site_name');

        $doid = $doid ? intval($doid) : 0;

        if($dosubmit!=1) {
            $catids=array();
            if($_GET['catids'][0]) {
                $catids = $_SESSION['catids'] = $_GET['catids'];
            }else {
                $cate_list = M('category', null, 'web')->where(array('site_id'=> $site_id))->select();
                foreach($cate_list as $id=>$cat) {
                    $catids[] = $cat['cat_id'];
                }
                $catids = $_SESSION['catids'] = $catids;
            }
        }else {
            $catids =$_SESSION['catids'];
        }
        if(!isset($catids[$doid])) {
            unset($_SESSION['catids']);
            $forward = U("Createhtml/Createshow");
            $this->assign ( 'jumpUrl', $forward);
            $this->success(L('create_update_success'));
        }else {
            $id = $catids[$doid];
            $dao = M('article',null,'web');
            $where = "cat_id=$id";
            $p = max(intval($p), 1);
            $start = $pagesize*($p-1);

            if(!isset($count)) {
                $count = $dao->where($where)->count();
            }
            $pages = ceil($count/$pagesize);

            if($count) {
                $list = $dao->where($where)->limit($start . ',' . $pagesize)->select();
                foreach($list as $r) {
                    $this->create_show($r['id']);
                }
            }
            $cat = M('category',null,'web')->where(array('cat_id'=>$id))->find();
            if($pages > $p) {
                $p++;
                $creatednum = $start + count($list);
                $percent = round($creatednum/$count, 2)*100;
                $urlarray=array(
                        'doid' => $doid,
                        'dosubmit' => 1,
                        'count' => $count,
                        'pages' => $pages,
                        'p' => $p,
                        'pagesize' => $pagesize,
                        'iscreatehtml'=>1,
                        'site_id'=>$site_id,
                );

                $message = L('updating').'<<'.$site_name.'>>'.$cat['cat_name'].L('create_update_count').$count.L('create_update_num').$creatednum.L('items').$percent.L('items1');
                $forward = U("Createhtml/".ACTION_NAME,$urlarray);
                $this->assign ( 'jumpUrl', $forward);
                $this->success($message);
            } else {
                $doid++;
                $urlarray=array(
                        'doid' => $doid,
                        'dosubmit' => 1,
                        'p' => 1,
                        'pagesize' => $pagesize,
                        'iscreatehtml'=>1,
                        'site_id'=>$site_id,
                );
                $message = L('start_updating').'<<'.$site_name.'>>'.$cat['cat_name']." ...";
                $forward = U("Createhtml/".ACTION_NAME,$urlarray);
                $this->assign ( 'jumpUrl', $forward);
                $this->success($message);
            }
        }

    }


}
?>
