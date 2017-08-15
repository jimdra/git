<?php
/**
* @desc 落地页管理
* @date 2017-07-10
* @author zhanjunjie
*/

namespace Admin\Controller;
use Think\Controller;
class LandController extends BaseController {

    public function _initialize() {
        parent::_initialize();
        $game_list = M('game')->select();
        $this->assign('game_list',$game_list);
        $theme_list = M('theme')->select();
        $this->assign('theme_list',$theme_list);
        $channel_list = M('channel')->select();
        $this->assign('channel_list',$channel_list);
    }
    
    public function index() {
        $mod = M('land_info', null, 'web');
        $map = $this->_search($mod);
        !empty($mod) && $this->_list($mod, $map);
        $this->display();
    }

    protected function _search($mod) {
        $map = array();
        $keyword=I('keyword','','trim');
        $searchtype=I('searchtype','','trim');
        $game_id = I('game_id',0,'intval');
        $game_id>0 && $map['game_id'] = array('eq',$game_id);
        if(!empty($keyword) && !empty($searchtype)) {
            $map[$searchtype]=array('like','%'.$keyword.'%');
        }
        $this->sort = !empty($sort)? $sort:'desc';
        $this->order =!empty($order)? $order:'id';
        $this->assign('search', array(
                'game_id'=>$game_id,
                'searchtype' => $searchtype,
                'order'=>$this->order,
                'sort'=>$this->sort,
                'listRows'=>$this->listRows,
        ));
        return $map;
    }
    
    public function _before_list($list) {
        foreach ($list as $k=>$v) {
            $list[$k]['game_name'] = M('game')->where(array('game_id'=>$v['game_id']))->getField('game_name');
        }
        return $list;
    }

    public function add() {
        $mod = M('land_info', null, 'web');
        !empty($mod) && $this->_add($mod,'edit');
    }

    public function edit(){
        $mod = M('land_info', null, 'web');
        !empty($mod) && $this->_edit($mod);
    }
    
    public function copy() {
        $oid = I('id');
        $land_info = M('land_info')->where(array('id'=>$oid))->find();
        unset($land_info['id']);
        $land_info['adduser'] = session('admin.username');
        $land_info['addtime'] = time();
        $land_id = M('land_info')->add($land_info);
        $this->land_url($land_id);
        IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
        $this->success(L('operation_success'));
    }

    public function delete() {
        $mod = M('land_info', null, 'web');
        $pk = $mod->getPk();
        $ids = trim(I($pk), ',');
        if ($ids) {
            $id_array = explode(',', $ids);
            foreach ($id_array as $v) {
                $land_info = $mod->where(array('id'=>$v))->find();
                $path_info = parse_url($land_info['land_url']);
                $file= '..'. $path_info['path'];
                @unlink($file);
            }
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
    
    public function _before_insert($data) {
        $data['images'] = implode(',', array_filter($data['images']));
        $data['link_serial'] = implode(',', $data['link_serial']);
        $data['adduser'] = session('admin.username');
        $data['addtime'] = time();
        return $data;
    }
    
    public function _after_insert($id) {
        $this->land_url($id);
        $land_info = M('land_info')->where(array('id'=>$id))->find();
        if ($land_info['batch']!=0) {
            $batch = $land_info['batch'];
            unset($land_info['id']);
            unset($land_info['batch']);
            $land_title = $land_info['land_title'];
            for($i=1;$i<$batch;$i++) {
                $land_info['land_title'] = $land_title.'_'.$i;
                $land_id = M('land_info')->add($land_info);
                $this->land_url($land_id);
            }
        }
    }
    
    public function _before_update($data) {
        $data['images'] = implode(',', array_filter($data['images']));
        $data['link_serial'] = implode(',', $data['link_serial']);
        $data['adduser'] = session('admin.username');
        $data['addtime'] = time();
        unset($data['batch']);
        return $data;
    }
    
    public function _after_update($id) {
        $this->land_url($id);
    }
    
    public function land_url($id) {
        $land_info = M('land_info')->where(array('id'=>$id))->find();
        $game_info = M('game')->where(array('game_id'=>$land_info['game_id']))->find();
        $theme = M('theme')->where(array('theme_id'=>$land_info['theme']))->find();
        $theme['theme_code'] = $land_info['is_default']==1 ? 'default' : $theme['theme_code'];
        $land_info['host'] = $land_info['host'] ? $land_info['host'] : 'leishenhuyu.com';
        $host = $land_info['host']=='leishengame.com' ? 'http://www.leishengame.com' : 'https://'.$land_info['host'];
        $path = '/landing/html/'.$game_info['game_code'].'/';
        $file_name = base64_encode($id.$game_info['game_code'].$game_info['game_name']);
        $land_url = $host.$path.$file_name.'.html';
        M('land_info')->where(array('id'=>$id))->save(array('land_url'=>$land_url));
        $material = M('material')->where("material_id in ({$land_info['images']})")->order("field(material_id,{$land_info['images']})")->select();
        $link = explode(',', $land_info['link_serial']);
        $this->assign('link',$link);
        $this->assign('land_info',$land_info);
        $this->assign('game_info',$game_info);
        $this->assign('material',$material);
        $dir = '..'.$path;
        $this->buildHtml($file_name,$dir,'./templates/theme/'.$theme['theme_code'].C('TMPL_TEMPLATE_SUFFIX'));
        set_time_limit(0);
        exec("/usr/bin/sudo /root/publish_2.sh");
    }
    
    public function recreate() {
        $land = M('land_info')->where("theme != ''")->select();
        foreach ($land as $key => $value) {
            $this->land_url($value['id']);
        }
        IS_AJAX && $this->ajaxReturn(1, L('operation_success'));
        $this->success(L('operation_success'));
    }
    
    public function stat() {
        if(IS_AJAX) {
            $data = D('Land')->getStatList($_REQUEST);
            echo $this->ajaxReturnData($data['rows'],$data['total']);
        }else {
            $this->display();
        }
    }

    public function export() {
        $xlsName  = 'land';
        $xlsCell  = array(
            array('stat_date','统计日期'),
            array('land_url','落地页链接'),
            array('game_name','所属游戏'),
            array('land_title','广告名称'),
            array('visit_num','访问次数'),
            array('visit_num_ip','独立IP访问次数'),
            array('down_num','下载次数'),
            array('down_num_ip','独立IP下载次数')
        );
        $data  = D('Land')->getStatList(array('export'=>1));
        $xlsData = $data['rows'];
        exportExcel($xlsName,$xlsCell,$xlsData);
    }

}
?>
