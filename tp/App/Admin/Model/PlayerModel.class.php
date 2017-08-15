<?php
/**
 * 
 * @author tomson
 *
 */
namespace Admin\Model;
use Think\Model;
class PlayerModel extends BaseModel{

    public function getPlayerInfo($search){
        $platform_id = $search['platform_id'];
        $server_id = $search['server_id'];
        if($search['player_type']=='role_name'){
            $parameter['name'] = $search['player_info'];
        }else if($search['player_type']=='role_id'){
            $parameter['player_id'] = $search['player_info'];
        }
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $parameter['cmd'] = 'view_player';
        $ret = $this->postData($url,$parameter);
        $list = array();
        if($ret['code']==0){
            $list = $ret['data'];
        }
        if(!empty($list)) {
            $list['platform_id'] = $search['platform_id'];
            $list['server_id'] = $search['server_id'];
            $list['limit_chat'] = $list['limit_chat']==0 ? L('not_banned_chat') : $list['limit_chat'];
            $list['limit_login'] = $list['limit_login']==0 ? L('not_banned_login') : $list['limit_login'];
        }
        $info[] = $list;
        return $info;
    }

    public function getPlayerBuilding($search){
        $buildList = array(
            1=>array('build_id'=>1,'build_name'=>'主建筑'),
            2=>array('build_id'=>2,'build_name'=>'酒馆'),
            3=>array('build_id'=>3,'build_name'=>'城门'),
            4=>array('build_id'=>4,'build_name'=>'学院'),
            5=>array('build_id'=>5,'build_name'=>'市场'),
            6=>array('build_id'=>6,'build_name'=>'庄园'),
            7=>array('build_id'=>7,'build_name'=>'封地'),
        );
        $platform_id = $search['platform_id'];
        $server_id = $search['server_id'];
        $parameter['player_id'] = $search['role_id'];
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $parameter['cmd'] = 'view_building';
        $ret = $this->postData($url,$parameter);
        $list = array();
        if($ret['code']==0){
            $list = $ret['data'];
        }
        foreach($list as $v){
            if($v['buildId']<=7){
                $info['build_id'] = $v['buildId'];
                $info['build_name'] = $buildList[$v['buildId']]['build_name'];
                $info['build_level'] = $v['buildLv'];
                $dataList[] = $info;
            }
        }
        return $dataList;
    }

    public function getEquipment($search){
        $platform_id = $search['platform_id'];
        $server_id = $search['server_id'];
        $parameter['player_id'] = $search['role_id'];
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $parameter['cmd'] = 'view_equip';
        $ret = $this->postData($url,$parameter);
        $list = array();
        if($ret['code']==0){
            $list = $ret['data'];
        }
        return $list;
    }

    public function getCaptain($search){
        $platform_id = $search['platform_id'];
        $server_id = $search['server_id'];
        $parameter['player_id'] = $search['role_id'];
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $parameter['cmd'] = 'view_monster';
        $ret = $this->postData($url,$parameter);
        $list = array();
        if($ret['code']==0){
            $list = $ret['data'];
        }
        foreach($list as $v){
            $info['name'] = $v['name'];
            $info['step'] = $v['step'];
            $info['quality'] = $v['quality'];
            $info['power'] = '暂无';
            $info['battle'] = '暂无';
            $info['runes'] = $this->getRunesStr($v['runes']);
            $dataList[] = $info;
        }
        return $dataList;
    }

    //符文转义
    public function getRunesStr($data){
        foreach($data as $v){
            $str .= $v['name'].'， ';
        }
        return $str;
    }


    public function getMail($search){
        $platform_id = $search['platform_id'];
        $server_id = $search['server_id'];
        $parameter['player_id'] = $search['role_id'];
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $parameter['cmd'] = 'view_mail';
        $ret = $this->postData($url,$parameter);
        $list = array();
        if($ret['code']==0){
            foreach($ret['data']['data'] as $v){
                $v['goods'] = getAttachStr($v['attach']);
                $v['content'] = $v['context'];
                if($v['attach_state']==0){
                    $v['goods_status'] = '没有附件';
                }else if($v['attach_state']==1){
                    $v['goods_status'] = '可领';
                }else{
                    $v['goods_status'] = '已领';
                }
                $list[] = $v;
            }
        }
        return $list;
    }

}
?>
