<?php
/**
 *
 * @author tomson
 *
 */
namespace Admin\Model;
use Think\Model;
class MailModel extends BaseModel{

    public function getMailList($search) {
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
        $sql = "SELECT * FROM api_mail WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $total_sql = "SELECT COUNT(*) AS p_count FROM api_mail WHERE {$where}";
        $data_list = M('api_mail')->query($sql);
        foreach($data_list as $v){
            $v['server_name'] = getServerName($v['platform_id'],$v['server_id']);
            $v['player'] = empty($v['player']) ? '全服' : $v['player'];
            $v['operation_time'] = date('Y-m-d H:i:s', $v['operation_time']);
            $dataList[] = $v;
        }
        $totalData = M('api_mail')->query($total_sql);
        $list['rows'] = $dataList;
        $list['total'] = $totalData[0]['p_count'];
        return $list;
    }

    public function mailTo($search){
        $platform_id = $search['platform_id'];
        $server_id = $search['server_id'];
        $parameter['cmd'] = 'mail';
        if($search['mail_to']==3){
            $parameter['players'] = explode(',',$search['player_info']);
        }
        $parameter['to'] = $search['mail_to'];
        $parameter['title'] = $search['title'];
        $parameter['content'] = $search['content'];
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $info = $this->postData($url,$parameter);
        return $info;
    }


    public function addMailLog($data){
        $info['platform_id'] = $data['platform_id'];
        $info['server_id'] = $data['server_id'];
        $info['title'] = $data['title'];
        $info['content'] = $data['content'];
        if(isset ($data['player'])){
            $info['player'] = $data['player'];
        }
        $info['operation_user'] = $_SESSION['admin']['username'];
        $info['operation_time'] = time();
        M('api_mail')->add($info);
    }

}
?>
