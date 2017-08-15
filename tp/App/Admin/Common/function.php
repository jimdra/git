<?php

function load_url($url, $postdata="", $timeout = 60) {
    $postdata = is_array($postdata) ? http_build_query($postdata) : $postdata;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, false);

    #超时
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);

    #post
    if ($postdata) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    }

    $data = curl_exec($ch);
    $httpcode = intval(curl_getinfo($ch, CURLINFO_HTTP_CODE));


    if ($httpcode==200 && $data!==false) {
        curl_close($ch);
        return $data;
    }else {
        $errmsg = curl_error($ch);
        curl_close($ch);
        return array($httpcode, $errmsg);
    }
}

//字符串截取
function str_cut($sourcestr,$cutlength,$suffix='...') {
    $str_length = strlen($sourcestr);
    if($str_length <= $cutlength) {
        return $sourcestr;
    }
    $returnstr='';
    $n = $i = $noc = 0;
    while($n < $str_length) {
        $t = ord($sourcestr[$n]);
        if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
            $i = 1;
            $n++;
            $noc++;
        } elseif(194 <= $t && $t <= 223) {
            $i = 2;
            $n += 2;
            $noc += 2;
        } elseif(224 <= $t && $t <= 239) {
            $i = 3;
            $n += 3;
            $noc += 2;
        } elseif(240 <= $t && $t <= 247) {
            $i = 4;
            $n += 4;
            $noc += 2;
        } elseif(248 <= $t && $t <= 251) {
            $i = 5;
            $n += 5;
            $noc += 2;
        } elseif($t == 252 || $t == 253) {
            $i = 6;
            $n += 6;
            $noc += 2;
        } else {
            $n++;
        }
        if($noc >= $cutlength) {
            break;
        }
    }
    if($noc > $cutlength) {
        $n -= $i;
    }
    $returnstr = substr($sourcestr, 0, $n);


    if ( substr($sourcestr, $n, 6)) {
        $returnstr = $returnstr . $suffix;//超过长度时在尾处加上省略号
    }
    return $returnstr;
}

//文章内容验证
function checkfield($postdata) {

    $site_id=$postdata['site_id'];
    $site=M('site',null,'web');
    if(empty($site_id)) {
        $site_code=$postdata['site_code'];
    }else {
        $site_code=$site->where("site_id=$site_id")->getField('site_code');
    }
    $save_path="../uploadfiles/$site_code/".strftime("%Y%m%d",time());
    //mk_dir($save_path,0777);            //创建上传文件转移目录
	
    if (substr($postdata['icon'],0,9)=='/Uploads/') {
		$postdata['icon']=C('IMG_CDN').substr($postdata['icon'],8);
	}
    
	if (substr($postdata['thumb'],0,9)=='/Uploads/') {
		$postdata['thumb']=C('IMG_CDN').substr($postdata['thumb'],8);
	}
	
	if (substr($postdata['img'],0,9)=='/Uploads/') {
		$postdata['img']=C('IMG_CDN').substr($postdata['img'],8);
	}
	
	if (substr($postdata['logo'],0,9)=='/Uploads/') {
		$postdata['logo']=C('IMG_CDN').substr($postdata['logo'],8);
	}

    if ($postdata['content']) {
        $content = stripslashes($postdata['content']);
        if(preg_match_all("/(src)=([\"|']?)([^ \"'>]+\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
            $postcontent=$matches[3];
            foreach($postcontent as $k=>$res) {
                $filepath="";
                $name="";
                $new_save_path="";
                $m="";
                if(!empty($postcontent[$k])) {
					if (substr($postcontent[$k],0,9)=='/Uploads/') {
						$content = str_replace($postcontent[$k],C('IMG_CDN').substr($postcontent[$k],8),$content);
					}
                }
            }
        }
        $postdata['content']=$content;
    }

    return $postdata;
}

function material($postdata) {
    $game_id=$postdata['game_id'];
    $game=M('game',null,'web');
    $cdn = 'https://leishenhuyu.com';
    //$cdn = "http://cdn.leishenhuyu.com";
    $game_code=$game->where("game_id=$game_id")->getField('game_code');
    
    $save_path="../landing/images/".$game_code;
    
    if(substr($postdata['material_thumb'],0,9)=='/Uploads/') {
        mk_dir($save_path,0777);            //创建上传文件转移目录
        $filepath='.'.$postdata['material_thumb'];
        $name = basename($filepath);      //获取文件名
        $new_save_path=$save_path."/".$name;
        $m=copy($filepath,$new_save_path);
        if($m==true) {
            $postdata['material_thumb']=$cdn.substr($new_save_path,2);
        }
    }
    
    return $postdata;
}

function mk_dir($dir, $mode = 0777) {
    if (is_dir($dir) || @mkdir($dir, $mode))
        return true;
    if (!mk_dir(dirname($dir), $mode))
        return false;
    return @mkdir($dir, $mode);
}

 /**
 +----------------------------------------------------------
 * @param  $file   upload file $_FILES
 +----------------------------------------------------------
 * @return array   array("error","message")
 +----------------------------------------------------------
 */
function importExecl($filename,$exts='xls'){
	Vendor("PHPExcel.PHPExcel");
	Vendor("PHPExcel.PHPExcel.IOFactory");
	if($exts == 'xls'){
		Vendor("PHPExcel.PHPExcel.Reader.Excel5");
		$objReader=new \PHPExcel_Reader_Excel5();
	}else if($exts == 'xlsx'){
		import("PHPExcel.PHPExcel.Reader.Excel2007");
		$objReader=new \PHPExcel_Reader_Excel2007();
	}
	$objPHPExcel = $objReader->load($filename);
	$sheet = $objPHPExcel->getSheet(0);
	$highestRow = $sheet->getHighestRow();
	$highestColumn = $sheet->getHighestColumn();

	//循环获取表中的数据，$currentRow表示当前行，从哪行开始读取数据，索引值从0开始
	for($currentRow=2;$currentRow<=$highestRow;$currentRow++){
			//从哪列开始，A表示第一列
			for($currentColumn='A';$currentColumn<=$highestColumn;$currentColumn++){
					//数据坐标
					$address=$currentColumn.$currentRow;
					//读取到的数据，保存到数组$arr中
					$data[$currentRow][$currentColumn]=$sheet->getCell($address)->getValue();
			}

	}
	return $data;
}

/**
 +----------------------------------------------------------
 * Export Excel | 2013.08.23
 * Author:HongPing <hongping626@qq.com>
 +----------------------------------------------------------
 * @param $expTitle     string File name
 +----------------------------------------------------------
 * @param $expCellName  array  Column name
 +----------------------------------------------------------
 * @param $expTableData array  Table data
 +----------------------------------------------------------
 */
function exportExcel($expTitle,$expCellName,$expTableData){
	$xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
	$fileName = $xlsTitle.date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
	$cellNum = count($expCellName);
	$dataNum = count($expTableData);
	Vendor("PHPExcel.PHPExcel");
	$objPHPExcel = new PHPExcel();
	$cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

	//$objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
	//$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'  Export time:'.date('Y-m-d H:i:s'));
	for($i=0;$i<$cellNum;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'1', $expCellName[$i][1]);
	}
	  // Miscellaneous glyphs, UTF-8
	for($i=0;$i<$dataNum;$i++){
	  for($j=0;$j<$cellNum;$j++){
		$objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+2), $expTableData[$i][$expCellName[$j][0]]);
	  }
	}

	header('pragma:public');
	header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
	header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
	Vendor("PHPExcel.PHPExcel.IOFactory");
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
}

/**
 * 获取excel表数据
 * @param $filePath     excel路径
 * @param $mainKey      主键名称
 * @param $startMark    数据开始键
 * @param $endMark      数据结束键
 */
function getExcelData($filePath,$mainKey,$startMark,$endMark) {
    /*导入phpExcel核心类 */
    require_once VENDOR_PATH.'PHPExcel.php';
    require_once VENDOR_PATH.'PHPExcel/Writer/Excel5.php';     // 用于其他低版本xls
    require_once VENDOR_PATH.'PHPExcel/Writer/Excel2007.php'; // 用于 excel-2007 格式
    /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
    $PHPReader = new \PHPExcel_Reader_Excel2007();
    if(!$PHPReader->canRead($filePath)) {
        $PHPReader = new \PHPExcel_Reader_Excel5();
        if(!$PHPReader->canRead($filePath)) {
            echo 'no Excel';
            return;
        }
    }

    $PHPExcel = $PHPReader->load($filePath);
    $sheetCount= $PHPExcel->getSheetCount();
    for($i=0;$i<$sheetCount;$i++) {
        $currentSheet = $PHPExcel->getSheet($i);  //读取excel文件中的第一个工作表
        $allRow = $currentSheet->getHighestRow(); //取得一共有多少行
        $minMax = -1; //初始化最小行
        for($currentRow = 1;$currentRow <= $allRow;$currentRow++) {
            $val = $currentSheet->getCellByColumnAndRow(0,$currentRow)->getValue();/**ord()将字符转为十进制数*/
            if($val==$startMark) {
                $minMax=$currentRow;
            }
            if($val==$endMark) {
                $maxRow=$currentRow;
                break;
            }
        }
        if($minMax<0) continue; //如果没有开始标识，该工作表不会继续读取数据
        $allColumn = $currentSheet->getHighestColumn($minMax); //取得最大的列号

        $erp_orders_id = array();  //声明数组
        for($currentRow = $minMax+1;$currentRow <= $maxRow;$currentRow++) {

            /**从第A列开始输出*/
            for($currentColumn= 'B';$currentColumn<= $allColumn; $currentColumn++) {

                $addr = $currentColumn.$currentRow;
                $cell = $currentSheet->getCell($addr)->getValue();
                if($cell instanceof PHPExcel_RichText)     //富文本转换字符串
                    $cell = $cell->__toString();

                $key = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$minMax)->getValue();/**ord()将字符转为十进制数*/
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65,$currentRow)->getValue();/**ord()将字符转为十进制数*/
                if($key!='') {
                    $erp_orders_id[$currentRow][$key] = $val;
                }
                /**如果输出汉字有乱码，则需将输出内容用iconv函数进行编码转换，如下将gb2312编码转为utf-8编码输出*/
                //echo iconv('utf-8','gb2312', $val)."\t";

            }
        }
        foreach ($erp_orders_id as $val) {
            $data [$val [$mainKey]] = $val;
        }
    }
    return $data;
}

?>
