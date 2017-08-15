<?php
/**
 * 菜单显示控制类
 *
 * @author tomson
 */
namespace Admin\Model;
use Think\Model;
class MenuModel extends Model {
    
    protected $_validate = array(
        array('name', 'require', '{%menu_name_require}'), //菜单名称为必须
        array('name', 'require', '{%module_name_require}'), //模块名称必须
        array('name', 'require', '{%action_name_require}'), //方法名称必须
    );

    public function admin_menu($pid, $with_self=false) {
        $pid = intval($pid);
        $condition = array('pid' => $pid);
        if ($with_self) {
            $map['iframe'] = 1;
        }else{
            $map['iframe']  = array('neq',1);
        }
        $map['_complex'] = $condition;
        $map['display'] = 1;
        $menus = M("menu")->where($map)->order('ordid')->select();
        $auth = M('admin_auth')->where('role_id='.$_SESSION['admin']['role_id'])->select();
        if($_SESSION['admin']['role_id']==1) {    //超级管理员默认不用验证
            $my_menus=$menus;
        }else {
            foreach($auth as $val) {
                foreach($menus as $v) {
                    if($v['id']==$val['menu_id']) {
                        $my_menus[]=$v;
                    }
                }
            }
        }
        return $my_menus;
    }
    
    public function sub_menu($pid = '', $big_menu = false) {
        $array = $this->admin_menu($pid, false);  
        $numbers = count($array);
        if ($numbers==0 && !$big_menu) {
            return '';
        }
        return $array;
    }

    public function big_menu($pid = '', $big_menu = true) {
        $array = $this->admin_menu($pid, true);
        return $array;
    }
    
    public function get_level($id,$array=array(),$i=0) {
        foreach($array as $n=>$value){
            if ($value['id'] == $id) {
                if($value['pid']== '0') {
                    return $i;
                }
                $this->get_level($value['pid'],$array,$i);
                $i++;
            }
        }
    }


}