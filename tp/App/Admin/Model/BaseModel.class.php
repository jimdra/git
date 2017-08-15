<?php
/**
 *
 * @author tomson
 *
 */
namespace Admin\Model;
use Think\Model;
class BaseModel extends Model {

    public function postData($url,$parameter) {
        $ret['data'] = json_encode($parameter,true);
        $ret['tag'] = '';
        $poststr = json_encode($ret,true);
        //返回json字符串
        $content = load_url($url, $poststr);
        //转为数组
        $dataInfo = json_decode($content, true);
        return $dataInfo;
    }
}
?>
