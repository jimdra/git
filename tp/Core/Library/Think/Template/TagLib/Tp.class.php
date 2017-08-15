<?php

/**
 * Tp标签库解析类
 */
namespace Think\Template\TagLib;
use Think\Template\TagLib;
class Tp extends TagLib
{
    protected $tags = array(
        // 标签定义：
        //attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'list'    =>  array('attr'=>'name,field,limit,order,catid,siteid,type,thumb,where,sql,key,page,mod,id,ids,status','level'=>3),
        'db'      =>  array('attr'=>'dbname,sql,key,mod,id','level'=>3),
        'flash'   =>  array('attr'=>'flashid,key,mod,id','level'=>3),
        'mod'     =>  array('attr'=>'name,server_gid,field,limit,order,catid,thumb,where,sql,key,mod,id,status','level'=>3),
        'game'    =>  array('attr'=>'name,gid,field,limit,order,cid,fid,where,sql,key,mod,id,status,sl','level'=>3),
        'server'  =>  array('attr'=>'name,gid,sid,field,limit,order,where,sql,key,mod,id,status','level'=>3),
    );

    public function _mod($tag,$content)
    {
        //$tag  = $this->parseXmlAttr($attr,'list');
        $id   = ! empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
        $key  = ! empty($tag['key'])?$tag['key']:'i';
        $mod  = isset($tag['mod'])?$tag['mod']:'2';

        if ($tag['name'])
        {
            //根据用户输入的值拼接查询条件
            $sql    = '';
            $module = $tag['name'];
            $order  = isset($tag['order'])?$tag['order']:'id desc';
            $where  = isset($tag['where'])?$tag['where']: ' 1 ';
            $limit  = isset($tag['limit'])?$tag['limit']: '10';

            if($tag['server_gid'])
            {
                $gid = $tag['server_gid'];
                $where .=  '  and server_gid ='.$gid;
            }

            $sql = "M(\"{$module}\",null,'platform')->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
        }
        else
        {
            if (!$tag['sql']) return ''; //排除没有指定model名称，也没有指定sql语句的情况
            $sql = "M('',null,'platform')->query('{$tag['sql']}')";
        }
        

        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
        $parsestr .= $content;//解析在article标签中的内容
        $parsestr .= '<?php endforeach; endif;?>';
        return $parsestr;
    }


    public function _flash($tag,$content)
    {
        //$tag     = $this->parseXmlAttr($attr,'flash');
        $id      = ! empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
        $key     = ! empty($tag['key'])?$tag['key']:'i';
        $mod     = isset($tag['mod'])?$tag['mod']:'2';
        $flashid = !empty($tag['flashid'])? $tag['flashid'] : '';
        $where   = 1;


        if($flashid && !is_numeric($flashid))
        {
            if(substr($flashid,0,2)=='T[')
            {
                $T = $this->tpl->get('T');
                preg_match_all("/T\[(.*)\]$/",$flashid,$arr);
                $flashid=$T[$arr[1][0]];
            }
            else
            {
                $flashid= $this->tpl->get($flashid);
            }
        }

        if($flashid)$where .=" and id=$flashid ";
        $flash = M('Slide',null,'web')->where($where)->find();
        if(empty($flash)) return  '';
        $wherepic = " status=1 and slide_id=$flashid ";
        $order="id DESC ";
        $limit= 5;
        $sql="M('Slide_data',null,'web')->where(\"{$wherepic}\")->order(\"{$order}\")->select();";
        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
        $parsestr .= $content;//解析在article标签中的内容
        $parsestr .= '<?php endforeach; endif;?>';
        return $parsestr;
    }

    public function _db($attr,$content)
    {
        $tag    = $this->parseXmlAttr($attr,'db');
        $id     = ! empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
        $key    = ! empty($tag['key'])?$tag['key']:'i';
        $sql    = ! empty($tag['sql'])? $tag['sql'] : '';
        $dbname = isset($tag['dbname'])?$tag['dbname']:'';
        $mod    = isset($tag['mod'])?$tag['mod']:'2';

        $dbsource=  F('Dbsource');
        $db=$dbsource[$dbname];
        if(empty($db) || empty($sql)) return '';
        $sql = str_replace('{tablepre}',$db['dbtablepre'],$sql);
        $db = 'mysql://'.$db['username'].':'.$db['password'].'@'.$db['host'].':'.$db['port'].'/'.$db['dbname'];
        $sql="M()->db(1,\"{$db}\")->query(\"{$sql}\");";

        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
        $parsestr .= $content;//解析在article标签中的内容
        $parsestr .= '<?php endforeach; endif;?>';
        return  $parsestr;
    }


    public function _link($attr,$content)
    {
        $tag    = $this->parseXmlAttr($attr,'link');
        $id     = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
        $key    = !empty($tag['key'])?$tag['key']:'i';
        $mod    = isset($tag['mod'])?$tag['mod']:'2';

        //$typeid    = isset($tag['$typeid'])?$tag['$typeid']:'';
        $linktype    = isset($tag['linktype'])?$tag['linktype']:'';
        $order  = isset($tag['order'])?$tag['order']:'id desc';
        $limit  = isset($tag['limit'])?$tag['limit']: '10';
        $field  = isset($tag['field'])?$tag['field']:'*';
        $where = ' status = 1 ';

        if($tag['catid'])
        {
            $catid = $tag['catid'];
            $where .=  '  and catid ='.$catid;
        }

        if($linktype)
        {
            $where .=  " and  linktype=".$linktype;
        }

        if($tag['gid'])
        {
            $gid = $tag['gid'];
            $where .= '  and gid ='.$gid;
        }
        $sql  = "M(\"Link\",null,'platform')->field(\"{$field}\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";

        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
        $parsestr .= $content;//解析在article标签中的内容
        $parsestr .= '<?php endforeach; endif;?>';
        return  $parsestr;
    }

    public function _list($tag,$content)
    {
        //$tag    = $this->parseXmlAttr($attr,'list');
        $id     = !empty($tag['id'])?$tag['id']:'r';  //定义数据查询的结果存放变量
        $key    = !empty($tag['key'])?$tag['key']:'i';
        $page   = !empty($tag['page'])? '1' : '0';
        $mod    = isset($tag['mod'])?$tag['mod']:'2';
        if ($tag['name'] || $tag['catid'])
        {
            //根据用户输入的值拼接查询条件
            $sql    = '';
            $module = isset($tag['name'])?$tag['name']:'Article';;
            $order  = isset($tag['order'])?$tag['order']:'id desc';
            $field  = isset($tag['field'])?$tag['field']:'*';
            $where  = isset($tag['where'])?$tag['where']: ' 1 ';
            $limit  = isset($tag['limit'])?$tag['limit']: '10';
            $status = isset($tag['status'])? intval($tag['status']) : '1';
            $ids =  isset($tag['ids'])?$tag['ids']:'';
            $type = isset($tag['type'])?$tag['type']:'pc';

            if($ids)
            {
                if($ids && !is_numeric($ids))
                {
                    if(substr($ids,0,2)=='T[')
                    {
                        $T = $this->tpl->get('T');
                        preg_match_all("/T\[(.*)\]$/",$ids,$arr);
                        $ids=$T[$arr[1][0]];
                    }
                    else
                    {
                        $ids= $this->tpl->get($ids);
                    }
                }
                if(strpos($ids,','))
                {
                    $where .= " AND id in($ids) ";
                }
                else
                {
                    $where .= " AND id=$ids ";
                }
            }
            if($type && $module == 'Article') $type=='pc' ? $where .= " AND `show` like '%1%'" : $where .= " AND `show` like '%2%'";
            if(ucfirst($module)!='Page')$where .= " AND status=$status ";
            if($tag['catid'])
            {
                $onezm  = substr($tag['catid'],0,1);
                if(substr($tag['catid'],0,2)=='T[')
                {
                    $T = $this->tpl->get('T');
                    preg_match_all("/T\[(.*)\]$/",$tag['catid'],$cidarr);
                    $catid=$T[$cidarr[1][0]];
                }
                elseif(!is_numeric($onezm))
                {
                    $catid = $this->tpl->get($tag['catid']);
                }
                else
                {
                    $catid = $tag['catid'];
                }
                if(is_numeric($catid))
                {
                    $category = M('category',null,'web')->where(array('cat_id'=>$catid))->find();
                    if($category['parent_id'] == 0) {
                        $child = M('category',null,'web')->field('group_concat(cat_id) as child')->where(array('parent_id'=>$catid))->find();
                        $category['arrchildid'] = !empty ($child['child']) ? $category['cat_id'].','.$child['child'] : $category['cat_id'];
                        $where .= " and cat_id in(".$category['arrchildid'].")";
                    }else {
                        $where .=  " and cat_id='$catid'";
                    }
                }
                elseif($onezm=='$')
                {
                    $where .=  ' AND cat_id in('.$tag['catid'].')';
                }
                else
                {
                    $where .=  ' AND cat_id in('.strip_tags($tag['catid']).')';
                }
            }
            if($tag['siteid'])
            {
                $site_id = $tag['siteid'];
                $where .=  '  AND site_id ='.$site_id;
            }

            if($tag['thumb'])
            {
                $where .=  ' AND  thumb !=\'\' ';
            }
            $sql  = "M(\"{$module}\",null,'web')->field(\"{$field}\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
        }
        else
        {
            if (!$tag['sql']) return ''; //排除没有指定model名称，也没有指定sql语句的情况
            $sql = "M('',null,'web')->query(\"{$tag['sql']}\")";
        }
        
        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
        $parsestr .= $content;//解析在article标签中的内容
        $parsestr .= '<?php endforeach; endif;?>';
        return $parsestr;
    }

    //游戏
    public function _game($attr, $content)
    {
        $tag  = $this->parseXmlAttr($attr,'game');
        $id   = isset($tag['id']) && ! empty($tag['id']) ? $tag['id'] : 'r';  //定义数据查询的结果存放变量
        $key  = isset($tag['key']) && ! empty($tag['key']) ? $tag['key'] : 'i';
        $mod  = isset($tag['mod']) ? $tag['mod'] : '2';
        $server_list = isset($tag['sl']) ? $tag['sl'] : 'sl';

        if (isset($tag['name']) && $tag['name'])
        {
            //根据用户输入的值拼接查询条件
            $sql    = '';
            $module = $tag['name'];
            $order  = isset($tag['order']) ? $tag['order'] : 'game_time desc';
            $where  = isset($tag['where']) ? $tag['where'] : ' 1 ';
            $limit  = isset($tag['limit']) ? $tag['limit'] : '15';
            $field  = isset($tag['field']) ? $tag['field'] : '*';
            //指定id的游戏
            if(isset($tag['gid']) && $tag['gid'])
            {
                $gid = (int) $tag['gid'];
                $where .=  ' and game_id ='.$gid;
            }
            //分类
            if(isset($tag['cid']) && $tag['cid'])
            {
                $cid = (int) $tag['cid'];
                $where .= ' and game_class ='.$cid;
            }
            //题材
            if(isset($tag['fid']) && $tag['fid'])
            {
                $fid = (int) $tag['fid'];
                $where .= ' and game_feature ='.$fid;
            }

            //题材
            if(isset($tag['status']) && $tag['status'])
            {
                $status = (int) $tag['status'];
                $where .= ' and game_status ='.$fid;
            }
            else
            {
                $where .= ' and game_status = 1';
            }
            

            $sql = "M(\"{$module}\",'online_','platform')->field(\"{$field}\")->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
        }
        else
        {
            if (isset($tag['sql']) && ! $tag['sql']) return ''; //排除没有指定model名称，也没有指定sql语句的情况
            $sql = "M('','','platform')->query('{$tag['sql']}');";
        }
        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
        //取得最新服
        $parsestr .= '<?php $sqlstr = "SELECT max(server_num) as game_sid, max(server_id) as server_id FROM online_server WHERE server_gid=\'".$'.$id.'[\'game_id\']."\' and server_status = 1 limit 1;";'.
                     '$server_list = M("'.$module.'", "", "platform")->query($sqlstr);'.
                     '$'.$server_list.'=isset($server_list[0]) ? $server_list[0] : false; ?>';
        $parsestr .= $content; //解析在article标签中的内容
        $parsestr .= '<?php endforeach; endif;?>';
        return $parsestr;
    }

    public function _server($attr, $content)
    {
        $tag  = $this->parseXmlAttr($attr,'game');
        $id   = isset($tag['id']) && ! empty($tag['id']) ? $tag['id'] : 'r';  //定义数据查询的结果存放变量
        $key  = isset($tag['key']) && ! empty($tag['key']) ? $tag['key'] : 'i';
        $mod  = isset($tag['mod']) ? $tag['mod'] : '2';

        if (isset($tag['name']) && $tag['name'])
        {
            //根据用户输入的值拼接查询条件
            $sql    = '';
            $module = 'server';//$tag['name'];
            $order  = isset($tag['order']) ? $tag['order'] : 'server_time desc';
            $where  = isset($tag['where']) ? $tag['where'] : ' 1 ';
            $limit  = isset($tag['limit']) ? $tag['limit'] : '15';
            $field  = isset($tag['field']) ? $tag['field'] : '*';

            $server_start = strtotime(date('Y-m-d H:i'));
            $where .= ' and server_start < '.$server_start;
            //指定id的服务器
            if(isset($tag['sid']) && $tag['sid'])
            {
                $sid = (int) $tag['sid'];
                $where .=  ' and server_id ='.$sid;
            }

            //状态
            if(isset($tag['status']) && $tag['status'])
            {
                $status = (int) $tag['status'];
                $where .= ' and server_status ='.$fid;
            }
            else
            {
                $where .= ' and server_status = 1';
            }
            //游戏
            if(isset($tag['gid']) && $tag['gid'])
            {
                $gid = (int) $tag['gid'];
                $where .= ' and server_gid ='.$gid;
            }

            $sql = "M(\"{$module}\",'online_','platform')->field(\"{$field}\")->join('online_game ON server_gid = game_id')->where(\"{$where}\")->order(\"{$order}\")->limit(\"{$limit}\")->select();";
        }
        else
        {
            if (isset($tag['sql']) && ! $tag['sql']) return ''; //排除没有指定model名称，也没有指定sql语句的情况
            $sql = "M('','','platform')->query('{$tag['sql']}');";
        }

        //下面拼接输出语句
        $parsestr  = '';
        $parsestr .= '<?php  $_result='.$sql.'; if ($_result): $'.$key.'=0;';
        $parsestr .= 'foreach($_result as $key=>$'.$id.'):';
        $parsestr .= '++$'.$key.';$mod = ($'.$key.' % '.$mod.' );?>';
        $parsestr .= $content; //解析在article标签中的内容
        $parsestr .= '<?php endforeach; endif;?>';
        return $parsestr;
    }

}
?>