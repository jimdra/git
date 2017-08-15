<?php
/**
 *
 * @author tomson
 *
 */
namespace Admin\Model;
use Think\Model;
class RechargeModel extends BaseModel {

    public function getRechargeList($search) {
        $rechargeTyle = array(
            -1 => array('type_id'=>'-1','type_name'=>'充值异常'),
            1 => array('type_id'=>'-1','type_name'=>'充值失败'),
            2 => array('type_id'=>'2','type_name'=>'充值成功'),
        );
        $where = '1 ';
        if(empty ($search['platform_id'])){
            return array();
        }else{
            $platform_id = $search['platform_id'];
        }
        if(!empty($search['server_id'])){
            $where.=" and server_id='{$search['server_id']}'";
        }
        if(!empty($search['player_info'])) {
            if($search['player_type']=='role_name') {
                $where.=" and nick_name='{$search['player_info']}'";
            }else if($search['player_type']=='role_id') {
                $where.=" and player_id='{$search['player_info']}'";
            }
        }
        if(!empty($search['p_start'])) {
            $where.=" and charge_time>='{$search['p_start']}'";
        }
        if(!empty($search['p_end'])) {
            $where.=" and charge_time<'{$search['p_end']}'";
        }
        $limit = isset($search['rows']) && $search['rows'] ? $search['rows'] : 100;
        $page = isset($search['page']) && $search['page'] ? $search['page'] : 1;
        $offset	= ($page - 1)*$limit;
        $order=!empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
        $sort=!empty($_REQUEST['sort']) ? $_REQUEST['sort'] : 'charge_time';
        $sql = "SELECT * FROM slog_recharge WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $total_sql = "SELECT COUNT(*) AS orderCount,SUM(money) AS totalMoney,COUNT(DISTINCT player_id) AS totalPeople FROM slog_recharge WHERE {$where}";
        $order_list = M('slog_recharge', null, 'yylql_game_'.$platform_id)->query($sql);
        foreach($order_list as $v){
            $server_name = getServerName($search['platform_id'],$v['server_id']);
            $v['server_name'] = !empty($server_name) ? $server_name : $v['server_id'];
            $v['first_charge'] = $v['first_charge']==1 ? '是' : '否';
            $v['oper_status'] = $rechargeTyle[$v['oper_status']]['type_name'];
            $dataList[] = $v;
        }
        $totalData = M('slog_recharge', null, 'yylql_game_'.$platform_id)->query($total_sql);
        $total_data['account_id'] = '合计';
        $total_data['player_id']=$totalData[0]['totalPeople'];
        $total_data['money']=$totalData[0]['totalMoney'];
        $list['rows'] = $dataList;
        $list['total'] = $totalData[0]['orderCount'];
        $list['footer'] = $total_data;
        return $list;
    }


    public function getRankingList($search){
        $where = '1 ';
        if(empty ($search['platform_id'])){
            return array();
        }else{
            $platform_id = $search['platform_id'];
        }
        if(!empty($search['server_id'])){
            $where.=" and server_id='{$search['server_id']}'";
        }
        if(!empty($search['p_start'])) {
            $where.=" and charge_time>='{$search['p_start']}'";
        }
        if(!empty($search['p_end'])) {
            $where.=" and charge_time<'{$search['p_end']}'";
        }
        $limit = isset($search['rows']) && $search['rows'] ? $search['rows'] : 100;
        $page = isset($search['page']) && $search['page'] ? $search['page'] : 1;
        $offset	= ($page - 1)*$limit;
        $order=!empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
        $sort=!empty($_REQUEST['sort']) ? $_REQUEST['sort'] : 'mymoney';
        $sql = "SELECT account_id,server_id,player_id,nick_name,MAX(`level`) AS mylevel,SUM(money) AS mymoney,MAX(charge_time) AS last_charge_time FROM slog_recharge WHERE {$where} GROUP BY account_id,server_id,player_id,nick_name ORDER BY {$sort} {$order}";
        $order_list = M('slog_recharge', null, 'yylql_game_'.$platform_id)->query($sql);
        foreach($order_list as $key=> $v){
            $v['ranking'] = $key+1;
            $server_name = getServerName($search['platform_id'],$v['server_id']);
            $v['server_name'] = !empty($server_name) ? $server_name : $v['server_id'];
            $dataList[] = $v;
        }
        $list['rows'] = $dataList;
        $list['total'] = count($dataList);
        return $list;
    }

    public function getNoSellList($search){
        $where = 'state=0 ';
        if(empty ($search['platform_id'])){
            return array();
        }else{
            $platform_id = $search['platform_id'];
        }
        if(!empty($search['server_id'])){
            $where.=" and server_id='{$search['server_id']}'";
        }
        if(!empty($search['player_info'])) {
            if($search['player_type']=='role_name') {
                $where.=" and nick_name='{$search['player_info']}'";
            }else if($search['player_type']=='role_id') {
                $where.=" and player_id='{$search['player_info']}'";
            }
        }
        if(!empty($search['order_code'])){
            $where.=" and order_uuid='{$search['order_code']}'";
        }
        if(!empty($search['p_start'])) {
            $where.=" and create_time>='{$search['p_start']}'";
        }
        if(!empty($search['p_end'])) {
            $where.=" and create_time<'{$search['p_end']}'";
        }
        $limit = isset($search['rows']) && $search['rows'] ? $search['rows'] : 100;
        $page = isset($search['page']) && $search['page'] ? $search['page'] : 1;
        $offset	= ($page - 1)*$limit;
        $order=!empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
        $sort=!empty($_REQUEST['sort']) ? $_REQUEST['sort'] : 'create_time';
        $sql = "SELECT * FROM order_uuid WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $order_list = M('order_uuid', null, 'yylql_sdk_'.$platform_id)->query($sql);
        foreach($order_list as $key=> $v){
            $v['platform_id'] = $search['platform_id'];
            $server_name = getServerName($search['platform_id'],$v['server_id']);
            $v['server_name'] = !empty($server_name) ? $server_name : $v['server_id'];
            $dataList[] = $v;
        }
        $list['rows'] = $dataList;
        $list['total'] = count($dataList);
        return $list;
    }


    public function fill_order($data){
        $platform_id = $data['platform_id'];
        $order_code = $data['order_code'];
        $info = M('order_uuid', null, 'yylql_sdk_'.$platform_id)->where("order_uuid='{$order_code}'")->find();
        $server_id = $info['server_id'];
        $parameter['cmd'] = 'recharge';
        $parameter['recharge_id'] = $info['recharge_id'];
        $parameter['order_num'] = $info['order_uuid'];
        $parameter['plat'] = $info['plat'];
        $parameter['player_id'] = $info['player_id'];
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $info = $this->postData($url,$parameter);
        return $info;
    }

}
?>
