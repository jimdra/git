<?php

namespace Admin\Controller;
use Think\Controller;


class PublicController extends Controller {


    public function verify_code(){
        header('Content-type: image/jpeg');
        $type	 = isset($_GET['type'])? $_GET['type']:'jpeg';
        import("@.ORG.Image");
        $image=new \Image ();
        $image->buildImageVerify(4,1,$type);
    }


}
