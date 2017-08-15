<?php
/**
 *
 * Attachment(附件管理)
 *
 * @author tomson
 */
namespace Admin\Controller;
use Think\Controller;
//加载上传类
use Think\Upload;
class AttachmentController extends  Controller {

    public function index() {

        $postd=array('more','isthumb','file_limit','file_types','file_size');
        foreach((array)$_REQUEST as $key=>$res) {
            if(in_array($key,$postd))$postdata[$key]=$res;
        }

        $this->assign('small_upfile_limit',$_REQUEST['file_limit']);

        $types = '*.'.str_replace(",",";*.",$_REQUEST['file_types']);

        $this->assign('file_size',$_REQUEST['file_size']);
        $this->assign('file_limit',$_REQUEST['file_limit']);
        $this->assign('file_types',$types);
        $this->assign('isthumb',$_REQUEST['isthumb']);

        $this->assign('more',$_GET['more']);

        if (IS_AJAX) {
            $response = $this->fetch();
            $data['status'] = 1;
            $data['data']=$response;
            $this->ajaxReturn($data);
        } else {
            $this->display();
        }
    }

    public function upload() {
        $ftype = array('jpg', 'gif', 'png', 'jpeg','apk','xml','keystore','mp4');
        $setting = array(
                'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
                'exts' => $ftype, //允许上传的文件后缀
                'autoSub' => true, //自动子目录保存文件
                'subName' => array('date', 'Ymd'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
                'rootPath' => './Uploads/', //保存根路径
        );
        /* 调用文件上传组件上传文件 */
        //实例化上传类，传入上面的配置数组
        $this->uploader = new Upload($setting, 'Local');
        $info = $this->uploader->upload($_FILES);
        $imagearr = explode(',', 'jpg,gif,png,jpeg,bmp,ttf,tif');
        //这里判断是否上传成功
        if ($info) {
            //// 上传成功 获取上传文件信息
            foreach ($info as &$file) {
                //拼接出上传目录
                $file['aid']=rand(1,1000);
                $file['rootpath'] = __ROOT__ . ltrim($setting['rootPath'], ".");
                //拼接出文件相对路径
                $file['filepath'] = $file['rootpath'] . $file['savepath'] . $file['savename'];

                $file['filename'] = $file['name'];
                $file['filesize'] = $file['size'];
                $file['fileext'] = strtolower($file['ext']);
                $file['isimage'] = in_array($file['ext'],$imagearr) ? 1 : 0;

            }
            //这里可以输出一下结果,相对路径的键名是$info['upload']['filepath']
            $this->ajaxReturnInfo($file,L('upload_ok'), '1');
        } else {
            //输出错误信息
            $this->ajaxReturnInfo(0,$this->uploader->getError(),0);
        }
    }


    /**
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @param String $info 提示信息
     * @param boolean $status 返回状态
     * @param String $status ajax返回类型 JSON XML
     * @return void
     */
    protected function ajaxReturnInfo($data,$info='',$status=1,$type='') {
        $result  =  array();
        $result['status']  =  $status;
        $result['info'] =  $info;
        $result['data'] = $data;
        //扩展ajax返回数据, 在Action中定义function ajaxAssign(&$result){} 方法 扩展ajax返回数据。
        if(method_exists($this,"ajaxAssign"))
            $this->ajaxAssign($result);
        if(empty($type)) $type  =   C('DEFAULT_AJAX_RETURN');
        if(strtoupper($type)=='JSON') {
            // 返回JSON数据格式到客户端 包含状态信息
            header("Content-Type:text/html; charset=utf-8");
            exit(json_encode($result));
        }elseif(strtoupper($type)=='XML') {
            // 返回xml格式数据
            header("Content-Type:text/xml; charset=utf-8");
            exit(xml_encode($result));
        }
    }

    public function filelist() {
        $this->dao = M('material');
        $where= 1;
        $p = $_GET['p'] = I('p');
        $game_id = I('game_id');
        $type_id = I('type_id');
        if ($game_id!=0) {
            $where .= " and game_id=".$game_id;
        }
        if ($type_id!=0) {
            $where .= " and type_id=".$type_id;
        }
        import("@.ORG.Page");
        $count = $this->dao->where($where)->count();
        $page=new \Page($count,12);

        $page->url = 'javascript:ajaxload(__PAGE__);';
        $show = $page->show();
        $this->assign("page",$show);
        $list=$this->dao->order('material_id desc')->where($where)
                ->limit($page->firstRow.','.$page->listRows)->select();

        $this->assign('list',$list);
        $game_list = M('game')->select();
        $this->assign('game_list',$game_list);
        $type_list = M('material_type')->select();
        $this->assign('type_list',$type_list);
        if ($p) {
            $str = '<div id="thumbnails1" ><ul class="attachment-list" style="padding:0;margin:0;">';
            foreach ($list as $key => $value) {
                $str .= '<li><div class="img"><a href="javascript:void(0);" data="'.$value['material_id'].'" onclick="javascript:add_file(this)"><div class="icon"></div><img src="'.$value['material_thumb'].'" style="max-width:80px;_width:80px;max-height:80px;_height:80px;"></a></div></li>  	';
            }
            $str .= '</ul></div><div id="pages" class="page">'.$show.'</div> ';
        }
        if (IS_AJAX) {
            $response = $p ? $str : $this->fetch();
            $data['status'] = 1;
            $data['data']=$response;
            $this->ajaxReturn($data);
        } else {
            $this->display();
        }
    }


}
?>