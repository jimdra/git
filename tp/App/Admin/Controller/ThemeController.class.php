<?php
/**
* @desc 模板管理
* @date 2017-07-18
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class ThemeController extends BaseController {

    public function _initialize() {
        parent::_initialize();
    }
    
    public function index() {
        $mod = M('theme', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'theme_id';
        return $map;
    }

    public function add() {
        $mod = M('theme', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('theme', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }

    public function delete() {
        $mod = M('theme', null, 'web');
        !empty($mod) && $this->_delete($mod);
    }
    
    public function getTheme() {
        if (IS_AJAX) {
            $theme_id = I('theme_id');
            $land_id = I('land_id');
            $material_num = I('material_num');
            $theme = M('theme')->where(array('theme_id'=>$theme_id))->find();
            $str = '';
            $msg = '';
            if ($land_id) {
                $land_info = M('land_info')->where(array('id'=>$land_id))->find();
                $link = explode(',', $land_info['link_serial']);
                $material = M('material')->where("material_id in ({$land_info['images']})")->order("field(material_id,{$land_info['images']})")->select();
                $msg = $land_info['material'];
            }
            $status = $theme['material_num']==0 ? 2 : 1;
            $num = $theme['material_num']==0 ? $material_num : $theme['material_num'];
            
            for($i=1;$i<=$num;$i++) {
                $str .= '<input type="text" name="images[]" id="images_'.$i.'" class="input-text" onblur="getMaterial(this.value,'.$i.')" value="'.$material[$i-1]['material_id'].'" size="10">';
                $str .= '<input type="button" class="btn" onclick="filelist(\'chosefile_'.$i.'\',\'images_'.$i.'\','.$i.',\'选择素材\')" value="选择素材">';
                if (in_array($i, $link)) {
                    $str .= '<input type="checkbox" name="link_serial[]" checked value="'.$i.'" />是否链接';
                } else {
                    $str .= '<input type="checkbox" name="link_serial[]" value="'.$i.'" />是否链接';
                }
                $str .= '<img src="'.$material[$i-1]['material_thumb'].'" style="margin-left: 50px;" id="material_'.$i.'" width="240" /></br>';
            }
            
            $this->ajaxReturn($status, $msg, $str);
        }
    }

}
?>
