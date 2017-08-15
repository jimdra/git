<?php
/**
 *
 * @author tomson
 *
 */
namespace Admin\Model;
use Think\Model;
class GoodsModel extends BaseModel{

    public function getGoodsList($search) {
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
        $sql = "SELECT * FROM api_goods WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $total_sql = "SELECT COUNT(*) AS p_count FROM api_goods WHERE {$where}";
        $data_list = M('api_goods')->query($sql);
        foreach($data_list as $v){
            $attachment = json_decode($v['attachment'],true);
            foreach($attachment as $val){
                $v['attachment_str'] .= $val['name'].'*'.$val['num'].'  ';
            }
            $v['server_name'] = getServerName($v['platform_id'],$v['server_id']);
            $v['player'] = empty($v['player']) ? '全服' : $v['player'];
            $v['review_time'] = $v['review_time']>0 ? date('Y-m-d H:i:s', $v['review_time']) : '';
            $v['operation_time'] = date('Y-m-d H:i:s', $v['operation_time']);
            if($v['status']==1){
                $v['status_str'] = '待审核';
            }else if($v['status']==2){
                $v['status_str'] = '审核已发送';
            }else if($v['status']==-1){
                $v['status_str'] = '已驳回';
            }
            $dataList[] = $v;
        }
        $totalData = M('api_goods')->query($total_sql);
        $list['rows'] = $dataList;
        $list['total'] = $totalData[0]['p_count'];
        return $list;
    }

    public function reviewGoods($data){
        $id = $data['id'];
        $info = M('api_goods')->where("id={$id}")->find();
        $platform_id = $info['platform_id'];
        $server_id = $info['server_id'];
        $parameter['cmd'] = 'mail';
        if($info['mail_to']==3){
            $player[] = $info['player'];
            $parameter['players'] = $player;
        }
        $parameter['to'] = $info['mail_to'];
        $parameter['title'] = $info['title'];
        $parameter['content'] = $info['content'];
        $attachment = json_decode($info['attachment'], true);
        foreach($attachment as $v) {
            if($v['type']=='monster') {
                $monster[] = $v['id'];
            }else {
                if($v['id']=='gold') {
                    $att['gold'] = $v['num'];
                }else if($v['id']=='money') {
                    $att['money'] = $v['num'];
                }else if($v['id']=='vipexp') {
                    $att['vipexp'] = $v['num'];
                }else {
                    $item['id'] = $v['id'];
                    $item['count'] = $v['num'];
                    $items[] = $item;
                }
            }
        }
        if(!empty($monster)){
            $att['monsters'] = $monster;
        }
        if($items){
            $att['items'] = $items;
        }
        $parameter['attach'] = $att;
        $serverInfo = getServerInfo($platform_id,$server_id);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $tt = $this->postData($url,$parameter);
        if(is_array($tt) && $tt['code']==0){
            $ret['review_user'] = $_SESSION['admin']['username'];
            $ret['review_time'] = time();
            $ret['status'] = 2;
            $t = M('api_goods')->where("id={$id}")->save($ret);
        }
        return $t;
    }


    public function addGoods($data){
        $info['platform_id'] = $data['platform_id'];
        $info['server_id'] = $data['server_id'];
        $info['title'] = $data['title'];
        $info['content'] = $data['content'];
        $info['mail_to'] = $data['mail_to'];
        if(isset ($data['player'])){
            $info['player'] = $data['player'];
        }
        $info['attachment'] = $data['attachment'];
        $info['operation_user'] = $_SESSION['admin']['username'];
        $info['operation_time'] = time();
        $info['reason'] = $data['reason'];
        $info['status'] = 1;
        $ret = M('api_goods')->add($info);
        return $ret;
    }

    public function rejectGoods($data){
        $info['review_user'] = $_SESSION['admin']['username'];
        $info['review_time'] = time();
        $info['status'] = -1;
        $ret = M('api_goods')->where("id={$data['id']}")->save($info);
        return $ret;
    }

}
?>
