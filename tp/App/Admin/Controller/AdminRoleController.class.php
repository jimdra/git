<?php
namespace Admin\Controller;
use Think\Controller;
class AdminRoleController extends BaseController {
    protected $_mod = '';
    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('AdminRole');
    }


    public function auth()
    {
        $menu_mod = D('Menu');
        $auth_mod = M('Admin_auth');
        if (isset($_POST['dosubmit'])) {
            $id = intval($_POST['id']);
            //清空权限
            $auth_mod->where(array('role_id'=>$id))->delete();
            if (is_array($_POST['menu_id']) && count($_POST['menu_id']) > 0) {
                foreach ($_POST['menu_id'] as $menu_id) {
                    $auth_mod->add(array(
                        'role_id' => $id,
                        'menu_id' => $menu_id
                    ));
                }
            }
            $this->success(L('operation_success'));
        } else {
            $id = I('id', 'intval');
            import("@.ORG.Tree");
            $tree = new \Tree();
            $tree->icon = array('│ ','├─ ','└─ ');
            $tree->nbsp = '&nbsp;&nbsp;&nbsp;';
            $result = $menu_mod->order('ordid')->select();
            //获取被操作角色权限
            $role_data = $this->_mod->find($id);
            $role_data['role_priv']=$auth_mod->query("SELECT b.* FROM admin_auth AS a, menu AS b WHERE a.menu_id = b.id AND a. role_id='{$id}'");
            $priv_ids = array();
            foreach ($role_data['role_priv'] as $val) {
                $priv_ids[] = $val['id'];
            }
            foreach($result as $k=>$v) {
                $result[$k]['level'] = $v['level'];
                $result[$k]['checked'] = (in_array($v['id'], $priv_ids))? ' checked' : '';
                $result[$k]['parentid_node'] = ($v['pid'])? ' class="child-of-node-'.$v['pid'].'"' : '';
            }
            $str  = "<tr id='node-\$id' \$parentid_node>" .
                        "<td style='padding-left:10px;'>\$spacer<input type='checkbox' name='menu_id[]' value='\$id' class='J_checkitem' level='\$level' \$checked> \$name</td>
                    </tr>";
            $tree->init($result);
            $menu_list = $tree->get_tree(0, $str);
            $this->assign('list', $menu_list);
            $this->assign('role', $role_data);
            $this->display();
        }
    }

}