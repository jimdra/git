<?php
/**
 * @desc 栏目管理
 * @date 2016-08-8
 * @author zhanjunjie
 */

namespace Admin\Controller;
use Think\Controller;
class CategoryController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $site_list = M('site', null, 'web')->select();
        $this->assign('site_list', $site_list);
    }

    public function index() {
        import("@.ORG.Tree");
        $tree = new \Tree();
        $tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
        $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
        $result = M('category', null, 'web')->order('cat_id')->select();
        $array = array();
        foreach($result as $r) {
            $r['cat_site'] = M('site', null, 'web')->where(array('site_id'=>$r['site_id']))->getField('site_name');
            $r['cname'] = $r['cat_name'];
            $r['pid'] = $r['parent_id'];
            $r['id'] = $r['cat_id'];
            $r['str_manage'] = '<a href="javascript:;" class="J_showdialog" data-uri="'.U('Category/add',array('pid'=>$r['cat_id'],'site_id'=>$r['site_id'])).'" data-title="'.L('add_subcat').'" data-id="add" data-width="400" data-height="200">'.L('add_subcat').'</a> |
                                <a href="javascript:;" class="J_showdialog" data-uri="'.U('Category/edit',array('cat_id'=>$r['cat_id'])).'" data-title="'.L('edit').' - '. $r['cat_name'] .'" data-id="edit" data-width="400" data-height="200">'.L('edit').'</a> |
                                <a href="javascript:;" class="J_confirmurl" data-acttype="ajax" data-uri="'.U('Category/delete',array('cat_id'=>$r['cat_id'])).'" data-msg="'.sprintf(L('confirm_delete_one'),$r['cat_name']).'">'.L('delete').'</a>';
            $array[] = $r;
        }
        $str  = "<tr>
                <td align='center'><input type='checkbox' value='\$id' class='J_checkitem'></td>
                <td align='center'>\$id</td>
                <td align='left'>\$spacer\$cname</td>
                <td align='center'>\$cat_site</td>
                <td align='center'>\$cat_dir</td>
                <td align='center'>\$str_manage</td>
                </tr>";
        $tree->init($array);
        $cate_list = $tree->get_tree(0, $str);
        $this->assign('cate_list', $cate_list);

        $this->assign('list_table', true);
        $this->display();
    }

    public function add() {
        $mod = M('category', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit() {
        $mod = M('category', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('category', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }

    public function _before_add() {
        $info['site_id'] = I('site_id');
        $this->assign('info', $info);
        import("@.ORG.Tree");
        $tree = new \Tree();
        $result = M('category', null, 'web')->select();
        $array = array();
        foreach($result as $r) {
            $r['cname'] = $r['cat_name'];
            $r['pid'] = $r['parent_id'];
            $r['id'] = $r['cat_id'];
            $r['selected'] = $r['cat_id'] == $_GET['pid'] ? 'selected' : '';
            $array[] = $r;
        }
        $str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
        $tree->init($array);
        $parent_cate = $tree->get_tree(0, $str);
        $this->assign('parent_cate', $parent_cate);
    }

    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['cat_site'] = M('site', null, 'web')->where(array('site_id'=>$v['site_id']))->getField('site_name');
        }
        return $list;
    }

    public function _before_edit() {
        $id = I('cat_id','intval');
        $info = M('category', null, 'web')->find($id);
        import("@.ORG.Tree");
        $tree = new \Tree();
        $result = M('category', null, 'web')->select();
        $array = array();
        foreach($result as $r) {
            $r['cname'] = $r['cat_name'];
            $r['pid'] = $r['parent_id'];
            $r['id'] = $r['cat_id'];
            $r['selected'] = $r['cat_id'] == $info['parent_id'] ? 'selected' : '';
            $array[] = $r;
        }
        $str  = "<option value='\$id' \$selected>\$spacer \$cname</option>";
        $tree->init($array);
        $parent_cate = $tree->get_tree(0, $str);
        $this->assign('parent_cate', $parent_cate);
    }



}
?>
