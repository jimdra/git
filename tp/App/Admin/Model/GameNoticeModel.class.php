<?php
/**
 *
 * @author tomson
 *
 */
namespace Admin\Model;
use Think\Model;
class GameNoticeModel extends BaseModel{


    public function getScrollList($search){
        $where = '1 ';
        if(!empty ($search['platform_id'])){
            $where.=" and platform_id='{$search['platform_id']}'";
        }
        if(!empty($search['server_id'])){
            $where.=" and server_id='{$search['server_id']}'";
        }
        if(!empty($search['p_start'])) {
            $start_time = strtotime($search['p_start']);
            $where.=" and release_time>='{$start_time}'";
        }
        if(!empty($search['p_end'])) {
            $end_time = strtotime($search['p_end']);
            $where.=" and release_time<'{$end_time}'";
        }
        $limit = isset($search['rows']) && $search['rows'] ? $search['rows'] : 100;
        $page = isset($search['page']) && $search['page'] ? $search['page'] : 1;
        $offset	= ($page - 1)*$limit;
        $order=!empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
        $sort=!empty($_REQUEST['sort']) ? $_REQUEST['sort'] : 'end_time';
        $sql = "SELECT * FROM api_notice WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $total_sql = "SELECT COUNT(*) AS p_count FROM api_notice WHERE {$where}";
        $data_list = M('api_notice')->query($sql);
        foreach($data_list as $v){
            $v['server_name'] = getServerName($v['platform_id'],$v['server_id']);
            $v['start_time'] = date('Y-m-d H:i:s',$v['start_time']);
            $v['end_time'] = date('Y-m-d H:i:s',$v['end_time']);
            $v['release_time'] = date('Y-m-d H:i:s',$v['release_time']);
            $v['review_time'] = $v['review_time']>0 ? date('Y-m-d H:i:s',$v['review_time']) : '';
            $v['status'] = $v['status']==1 ? '待审核' : '审核已发送';
            $dataList[] = $v;
        }
        $totalData = M('api_notice')->query($total_sql);
        $list['rows'] = $dataList;
        $list['total'] = $totalData[0]['p_count'];
        return $list;
    }

    public function addNotice($data){
        $info['platform_id'] = $data['platform_id'];
        $info['server_id'] = $data['server_id'];
        $info['start_time'] = strtotime($data['start_time']);
        $info['end_time'] = strtotime($data['end_time']);
        $info['content'] = $data['content'];
        $info['step'] = $data['step'];
        $info['count'] = $data['count'];
        $info['release_time'] = time();
        $info['release_user'] = $_SESSION['admin']['username'];
        $info['status'] = 1;
        $ret = M('api_notice')->add($info);
        return $ret;
    }

    public function editNotice($data){
        $info['start_time'] = strtotime($data['start_time']);
        $info['end_time'] = strtotime($data['end_time']);
        $info['content'] = $data['content'];
        $info['step'] = $data['step'];
        $info['count'] = $data['count'];
        $info['status'] = 1;
        $ret = M('api_notice')->where("id={$data['id']}")->save($info);
        return $ret;
    }

    public function reviewNotice($data){
        $id = $data['id'];
        $info = M('api_notice')->where("id={$id}")->find();
        $parameter['cmd'] = 'notice';
        $parameter['id'] = $info['id'];
        $parameter['n_count'] = $info['count'];
        $parameter['t_step'] = $info['step'];
        $parameter['content'] = $info['content'];
        $parameter['b_time'] = date('Y-m-d H:i:s',$info['start_time']);
        $parameter['e_time'] = date('Y-m-d H:i:s',$info['end_time']);
        $serverInfo = getServerInfo($info['platform_id'],$info['server_id']);
        $url = 'http://'.$serverInfo['server_url'].':'.$serverInfo['server_port'];
        $info = $this->postData($url,$parameter);
        if(is_array($info) && $info['code']==0){
            $ret['review_user'] = $_SESSION['admin']['username'];
            $ret['review_time'] = time();
            $ret['status'] = 2;
            $t = M('api_notice')->where("id={$id}")->save($ret);
        }
        return $t;
    }

    public function getSystemList($search){
        $where = '1 ';
        if(!empty ($search['platform_id'])){
            $where.=" and platform_id='{$search['platform_id']}'";
        }
        if(!empty($search['server_id'])){
            $where.=" and server_id='{$search['server_id']}'";
        }
        if(!empty($search['p_start'])) {
            $start_time = strtotime($search['p_start']);
            $where.=" and release_time>='{$start_time}'";
        }
        if(!empty($search['p_end'])) {
            $end_time = strtotime($search['p_end']);
            $where.=" and release_time<'{$end_time}'";
        }
        $limit = isset($search['rows']) && $search['rows'] ? $search['rows'] : 100;
        $page = isset($search['page']) && $search['page'] ? $search['page'] : 1;
        $offset	= ($page - 1)*$limit;
        $order=!empty($_REQUEST['order']) ? $_REQUEST['order'] : 'desc';
        $sort=!empty($_REQUEST['sort']) ? $_REQUEST['sort'] : 'release_time';
        $sql = "SELECT * FROM api_notice_system WHERE {$where} ORDER BY {$sort} {$order} {$limitStr}";
        $total_sql = "SELECT COUNT(*) AS p_count FROM api_notice_system WHERE {$where}";
        $data_list = M('api_notice_system')->query($sql);
        foreach($data_list as $v){
            $v['server_name'] = getServerName($v['platform_id'],$v['server_id']);
            $v['release_time'] = date('Y-m-d H:i:s',$v['release_time']);
            $dataList[] = $v;
        }
        $totalData = M('api_notice_system')->query($total_sql);
        $list['rows'] = $dataList;
        $list['total'] = $totalData[0]['p_count'];
        return $list;
    }

    public function addNoticeSystem($data){
        $info['platform_id'] = $data['platform_id'];
        $info['server_id'] = $data['server_id'];
        $info['title'] = $data['title'];
        $info['content'] = $data['content'];
        $info['release_time'] = time();
        $info['release_user'] = $_SESSION['admin']['username'];
        $ret = M('api_notice_system')->add($info);
        return $ret;
    }

    public function editNoticeSystem($data){
        $info['title'] = $data['title'];
        $info['content'] = $data['content'];
        $ret = M('api_notice_system')->where("id={$data['id']}")->save($info);
        return $ret;
    }

}
?>
