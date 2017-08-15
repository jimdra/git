<?php
/**
 *
 * @author zhanjunjie
 *
 */
namespace Admin\Model;
use Think\Model;
class LandModel extends BaseModel {

    public function getStatList($search) {
        $this->landStat($search);
        $where = '1 ';
        
        if(!empty($search['p_start'])) {
            $start_time = strtotime($search['p_start'].' 00:00:00');
            $where.=" and UNIX_TIMESTAMP(stat_date)>='{$start_time}'";
        }
        if(!empty($search['p_end'])) {
            $end_time = strtotime($search['p_end'].' 23:59:59');
            $where.=" and UNIX_TIMESTAMP(stat_date)<'{$end_time}'";
        }
        if(!empty($search['game_id'])) {
            $where.=" and i.game_id='{$search['game_id']}'";
        }
        if(!empty($search['channel_id'])) {
            $where.=" and i.channel_id='{$search['channel_id']}'";
        }
        $limit = isset($search['rows']) && $search['rows'] ? $search['rows'] : 20;
        $page = isset($search['page']) && $search['page'] ? $search['page'] : 1;
        $offset	= ($page - 1)*$limit;
        $order=!empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
        $sort=!empty($_REQUEST['sort']) ? $_REQUEST['sort'] : 'stat_date';
        $limitStr = isset($search['export']) ? '' : 'limit '.$offset.','.$limit;
        $sql = "SELECT s.* FROM land_stat as s LEFT JOIN land_info as i ON s.land_id=i.id WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $total_sql = "SELECT COUNT(*) AS p_count FROM land_stat as s LEFT JOIN land_info as i ON s.land_id=i.id WHERE {$where}";
        $stat_list = M('land_stat')->query($sql);
        foreach($stat_list as $v){
            $land_info = M('land_info')->where(array('id'=>$v['land_id']))->find();
            $v['land_url'] = $land_info['land_url'];
            $v['game_name'] = M('game')->where(array('game_id'=>$land_info['game_id']))->getField('game_name');
            $v['land_title'] = $land_info['land_title'];
            $dataList[] = $v;
        }
        $totalData = M('land_stat')->query($total_sql);
        $list['rows'] = $dataList;
        $list['total'] = $totalData[0]['p_count'];
        return $list;
    }
    
    public function landStat($search) {
        if(!empty($search['p_start'])) {
            $start_time = strtotime($search['p_start'].' 00:00:00');
            $where.=" and addtime>='{$start_time}'";
        } else {
            $start_time = strtotime(date('Y-m-d',strtotime('-1 day')).' 00:00:00');
            $where.=" and addtime>='{$start_time}'";
        }
        if(!empty($search['p_end'])) {
            $end_time = strtotime($search['p_end'].' 23:59:59');
            $where.=" and addtime<'{$end_time}'";
        } else {
            $end_time = strtotime(date('Y-m-d').' 23:59:59');
            $where.=" and addtime<'{$end_time}'";
        }
        $sql = "SELECT FROM_UNIXTIME(addtime,'%Y-%m-%d') AS stat_date,land_id,count(*) AS num,count(distinct ip) AS ip_num FROM land_log WHERE type=1 {$where} GROUP BY land_id,stat_date";
        $visit_list = M('land_log')->query($sql);
        foreach ($visit_list as $key => $value) {
            $info = array(
                'stat_date'=>$value['stat_date'],
                'land_id'=>$value['land_id'],
                'visit_num'=>$value['num'],
                'visit_num_ip'=>$value['ip_num']
            );
            $ret = M('land_stat')->add($info,'',true);
            if ($ret === false) {
                M('land_stat')->where(array('stat_date'=>$value['stat_date'],'land_id'=>$value['land_id']))->save(array('visit_num'=>$value['num'],'visit_num_ip'=>$value['ip_num']));
            }
        }
        $sql = "SELECT FROM_UNIXTIME(addtime,'%Y-%m-%d') AS stat_date,land_id,count(*) AS num,count(distinct ip) AS ip_num FROM land_log WHERE type=2 {$where} GROUP BY land_id,stat_date";
        $down_list = M('land_log')->query($sql);
        foreach ($down_list as $key => $value) {
            $info = array(
                'stat_date'=>$value['stat_date'],
                'land_id'=>$value['land_id'],
                'down_num'=>$value['num'],
                'down_num_ip'=>$value['ip_num']
            );
            $ret = M('land_stat')->add($info);
            if ($ret === false) {
                M('land_stat')->where(array('stat_date'=>$value['stat_date'],'land_id'=>$value['land_id']))->save(array('down_num'=>$value['num'],'down_num_ip'=>$value['ip_num']));
            }
        }
    }


}
?>
