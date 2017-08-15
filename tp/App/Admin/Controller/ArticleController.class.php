<?php
/**
 * @desc 文章管理
 * @date 2016-08-8
 * @author zhanjunjie
 */

namespace Admin\Controller;
use Think\Controller;
class ArticleController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }

    public function index() {
        $site_list = M('site', null, 'web')->select();
        $this->assign('site_list', $site_list);
        $mod = M('article', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $keyword=I('keyword','','trim');
        $searchtype=I('searchtype','id','trim');
        $site = I('site',0,'intval');
        $cate = I('cate',0,'intval');
        $thumb = I('thumb');
        if( $_GET['status']==null ) {
            $status = -1;
        }else {
            $status = intval($_GET['status']);
        }
        $site>0 && $map['site_id'] = array('eq',$site);
        $cate>0 && $map['cat_id'] = array('eq',$cate);
        $status>=0 && $map['status'] = array('eq',$status);
        $thumb==1 && $map['thumb'] = array('neq','');
        if(!empty($keyword) && !empty($searchtype)) {
            $map[$searchtype]=array('like','%'.$keyword.'%');
        }
        $sort=I('sort','','trim');
        $order=I('order','','trim');
        $listRows=I('listRows','','trim');
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'id';
        $this->listRows=!empty($listRows)? $listRows:'20';
        $this->assign('search', array(
                'site'=>$site,
                'cate'=>$cate,
                'status'=>$status,
                'searchtype' => $searchtype,
                'order'=>$this->order,
                'sort'=>$this->sort,
                'listRows'=>$this->listRows,
        ));
        return $map;
    }

    public function add() {
        $site_list = M('site', null, 'web')->select();
        $this->assign('site_list', $site_list);
        $this->assign('time', date("Y-m-d H:i:s", time()));
        $mod = M('article', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit() {
        $site_list = M('site', null, 'web')->select();
        $this->assign('site_list', $site_list);
        $this->assign('pc',1);
        $this->assign('wap',2);
        $mod = M('article', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('article', null, 'web');
        $pk = $mod->getPk();
        $ids = trim(I($pk), ',');
        if ($ids) {
            $id_array = explode(',', $ids);
            foreach ($id_array as $v) {
                $article = M('article',null,'web')->where(array('id'=>$v))->find();
                $site = M('site',null,'web')->where(array('site_id'=>$article['site_id']))->find();
                $pc_file= '../html/'.$site['site_code'].'/pc'.$article['url'];
                $m_file= '../html/'.$site['site_code'].'/mobile'.$article['url'];
                @unlink($pc_file);
                @unlink($m_file);
            }
            if (false !== $mod->delete($ids)) {
                $p = max(intval($p), 1);
                $j = 1;
                do {
                    $this->create_list($article['cat_id'],$article['site_id'],$p);
                    $j++;
                    $p++;
                    $pages = isset($pages) ? $pages : PAGESTOTAL;
                } while ($p <= $pages && $j < $pagesize);
                $this->create_index($article['site_id']);
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

    public function _before_insert($data) {
        $data['show'] = implode(',', $data['show']);
        $data['addtime'] = strtotime($data['addtime']);
        $data = checkfield($data);
        return $data;
    }

    public function _after_insert($id) {
        $this->after($id);
    }

    public function _before_update($data) {
        $data['show'] = implode(',', $data['show']);
        $data['addtime'] = strtotime($data['addtime']);
        $data['updatetime'] = time();
        $data = checkfield($data);
        return $data;
    }

    public function _after_update($id) {
        $this->after($id);
    }

    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['cat_name'] = M('category', null, 'web')->where(array('cat_id'=>$v['cat_id']))->getField('cat_name');
            $list[$k]['site_name'] = M('site', null, 'web')->where(array('site_id'=>$v['site_id']))->getField('site_name');
        }
        return $list;
    }

    public function after($id) {
        $article = M('article', null, 'web')->where(array('id'=>$id))->find();
        $cat = M('category', null, 'web')->where(array('cat_id'=>$article['cat_id']))->find();
        $site = M('site', null, 'web')->where(array('site_id'=>$article['site_id']))->find();
        $url = $this->geturl($cat,$article,$site['site_code']);
        $data['id'] = $id;
        $data['url'] = str_replace('/html/'.$site['site_code'],'',$url[0]);
        M('article', null, 'web')->save($data);
        if ($cat['parent_id'] != 0) $this->clhtml($cat['parent_id'], $article['site_id']);
        $this->clhtml($article['cat_id'], $article['site_id']);
        $this->create_show($id);
        $this->create_index($article['site_id']);
    }

    public function clhtml($cat_id, $site_id) {
        $p = max(intval($p), 1);
        $j = 1;
        do {
            $this->create_list($cat_id,$site_id,$p);
            $j++;
            $p++;
            $pages = isset($pages) ? $pages : PAGESTOTAL;
        } while ($p <= $pages && $j < $pagesize);
    }

    public function getCate() {
        $site_id = I('site_id');
        $cat_id = I('cat_id');
        import("@.ORG.Tree");
        $tree = new \Tree();
        $result = M('category', null, 'web')->where(array('site_id'=> $site_id))->select();
        $array = array();
        foreach($result as $r) {
            $r['cname'] = $r['cat_name'];
            $r['pid'] = $r['parent_id'];
            $r['id'] = $r['cat_id'];
            $r['selected'] = $r['cat_id'] == $cat_id ? 'selected' : '';
            $r['disabled'] = M('category', null, 'web')->where(array('parent_id'=>$r['cat_id']))->count() ? 'disabled' : '';
            $array[] = $r;
        }
        $str  = "<option value='\$id' \$disabled \$selected>\$spacer \$cname</option>";
        $tree->init($array);
        $cate_list = $tree->get_tree(0, $str);
        if (IS_AJAX) {
            $this->ajaxReturn(1, '', $cate_list);
        }
    }

    public function sql() {
        $list = M('article',null,'web')->where(array('site_id'=>6))->select();
        foreach ($list as $k=>$v) {
            switch ($v['cat_id']) {
                case 22:
                    $list[$k]['url'] = '/news/xinwen/'.$v['id'].'.html';
                    break;
                case 23:
                    $list[$k]['url'] = '/news/gongao/'.$v['id'].'.html';
                    break;
                case 24:
                    $list[$k]['url'] = '/news/huodong/'.$v['id'].'.html';
                    break;
                case 25:
                    $list[$k]['url'] = '/news/meiti/'.$v['id'].'.html';
                    break;

                default:
                    break;
            }
            //$list[$k]['content'] = str_replace('http://hjfile.huanjia.cc/Upload/', 'http://files.web.joyhj.com/', $v['content']);
        }
        foreach ($list as $value) {
            M('article',null,'web')->save($value);
        }
    }



}
?>
