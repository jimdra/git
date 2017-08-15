<?php


$database = require ('dbconfig.php');

$config = array(
        'MODULE_ALLOW_LIST' => array('Admin','Api'),
        'MODULE_DENY_LIST'      =>  array('Common'),
        'DEFAULT_MODULE' => 'Admin',
        'URL_MODEL' => 0, //URL模式
        'pack_json_path' => "./data/task_dp/done",                                  //打包json文件存放路径
        'pack_file_path' => "./Uploads/" ,                                          //打包上传文件路径
        'pack_success_check_path' => './data/task_dp/todo_ftp/',                    //打包成功检测目录
        'release_test_check_path' => './data/task_dp/done/',                        //发布测试包成功检测目录
        'xml_original_config_path'=>'./data/',                                      //原xml存放目录
        'xml_release_path'=>'./data/copy/',                                         //xml同步目录
        'pack_formal_domain'=>'http://192.168.50.196/downapi/download.php'          //正式下载包主域名

);

return array_merge($database, $config);
