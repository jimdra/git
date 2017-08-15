<?php
/**
 *
 * @author tomson
 *
 */
namespace Admin\Model;
use Think\Model;
class BannedModel extends BaseModel{

    public function getBannedList($search) {
        $where = '1 ';
        if(!empty ($search['platform_id'])){
            $where.=" and platform_id='{$search['platform_id']}'";
        }
        if(!empty($search['server_id'])){
            $where.=" and server_id='{$search['server_id']}'";
        }
        if(!empty($search['player_info'])) {
            if($search['player_type']=='role_name') {
                $where.=" and role_name='{$search['player_info']}'";
            }else if($search['player_type']=='role_id') {
                $where.=" and player_id='{$search['player_info']}'";
            }
        }
        if(!empty($search['p_start'])) {
            $start_time = strtotime($search['p_start']);
            $where.=" and operation_time>='{$start_time}'";
        }
        if(!empty($search['p_end'])) {
            $end_time = strtotime($search['p_end']);
            $where.=" and operation_time<'{$end_time}'";
        }
        $limit = isset($search['rows']) && $search['rows'] ? $search['rows'] : 100;
        $page = isset($search['page']) && $search['page'] ? $search['page'] : 1;
        $offset	= ($page - 1)*$limit;
        $order=!empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
        $sort=!empty($_REQUEST['sort']) ? $_REQUEST['sort'] : 'operation_time';
        $sql = "SELECT * FROM api_banned WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $total_sql = "SELECT COUNT(*) AS p_count FROM api_banned WHERE {$where}";
        $data_list = M('api_banned')->query($sql);
        foreach($data_list as $v){
            $v['banned_type'] = $v['banned_type']==1 ? '封号' : '禁言';
            $v['banned_opt'] = $v['banned_opt']==1 ? '封禁' : '解封';
            $v['start_time'] = $v['start_time']>0 ? date('Y-m-d H:i:s', $v['start_time']) : '';
            $v['end_time'] = $v['end_time']>0 ? date('Y-m-d H:i:s', $v['end_time']) : '';
            $v['unbanned_time'] = $v['unbanned_time']>0 ? date('Y-m-d H:i:s', $v['unbanned_time']) : '';
            $v['operation_time'] = date('Y-m-d H:i:s', $v['operation_time']);
            $dataList[] = $v;
        }
        $totalData = M('api_banned')->query($total_sql);
        $list['rows'] = $dataList;
        $list['total'] = $totalData[0]['p_count'];
        return $list;
    }

    public function banned($search){
        $platform_id = $search['platform_id'];
        $server_id = $search['server_id'];
        if($search['banned_opt']=='banned'){
            $parameter['cmd'] = 'limit';
            $parameter['b_time'] = $search['start_time'];
            $parameter['e_time'] = $search['end_time'];
        }else if($search['banned_opt']=='unbanned'){
            $parameter['cmd'] = 'unlimit';
        }
        if($search['player_type']=='role_name'){
            $parameter['name'] = $search['player_info'];
        }else if($search['player_type']=='role_id'){
            $parameter['player_id'] = $search['player_info'];
        }
        $parameter['type'] = $search['type'];
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $info = $this->postData($url,$parameter);
        return $info;
    }


    public function addBannedLog($data){
        $playerInfo = D('Player')->getPlayerInfo($data);
        $info['platform_id'] = $data['platform_id'];
        $info['server_id'] = $data['server_id'];
        $info['account'] = $playerInfo[0]['account_id'];
        $info['role_name'] = $playerInfo[0]['name'];
        $info['role_id'] = $playerInfo[0]['player_id'];
        $info['banned_opt'] = $data['banned_opt'] =='banned' ? 1 : 2;
        $info['banned_type'] = $data['type'];
        if($data['banned_opt']=='banned') {
            $info['start_time'] = strtotime($data['start_time']);
            $info['end_time'] = strtotime($data['end_time']);
        }else {
            $info['unbanned_time'] = time();
        }
        $info['banned_user'] = $_SESSION['admin']['username'];
        $info['operation_time'] = time();
        $info['reason'] = $data['reason'];
        M('api_banned')->add($info);
    }

}
?>
