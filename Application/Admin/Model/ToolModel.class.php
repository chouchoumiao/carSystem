<?php

/**
 * 验证方法类
 */
namespace Admin\Model;

    class ToolModel {


        static function setDetailCell(&$objSheet,$data,$rows,$setWidth = 13){

            $objSheet->fromArray($data,null, 'A4');  //利用fromArray()直接一次性填充数据
            //算出最后一行的列数，用来算范围，加边框
            $count = count($data) + 3;

            $styleThinBlackBorderOutline = array(
                'borders' => array (
                    'outline' => array (
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,   //设置border样式
                    ),
                ),
            );

            //循环将所有单元格的字段都加边框
            for($i=1;$i<=$count;$i++){
                $objSheet->getStyle( 'A'.$i.':'.$rows.$i)->applyFromArray($styleThinBlackBorderOutline);
                $objSheet->getStyle('A'.$i.':'.$rows.$i )->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
            }

            //所有单元格都设置垂直居中显示
            $objStyleA = $objSheet->getStyle('A1:'.$rows.$count);
            $objAlignA = $objStyleA->getAlignment();
            $objAlignA->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    //左右居中
            $objAlignA->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);  //上下居中

            //设置单元格长度
            $objSheet->getDefaultColumnDimension()->setWidth($setWidth);

            return $objSheet;
        }

        /**
         * @param $objSheet
         * @param $rows
         *      最大列的字母
         * @return mixed
         */
        static function setTitle(&$objSheet,$rows){

            $firstRow = 'A1'; //开始单元格为A1
            $mergeCells='A1:'.$rows.'2';  //默认第一第二列合并居中为头部
            $cellsSetStyle='A3:'.$rows.'3'; //默认第三列开始为真正字段列

            //设置第一行样式
            $objSheet->setCellValue($firstRow, '嵊州市锡锋物流运输公司');

            $objSheet->mergeCells($mergeCells);
            $objSheet->getStyle($firstRow)->getFont()->setName('宋体');
            $objSheet->getStyle($firstRow)->getFont()->setSize(24);
            $objSheet->getStyle($firstRow)->getFont()->setBold(true);

            $objSheet->getStyle($cellsSetStyle)->getFont()->setName('宋体');
            $objSheet->getStyle($cellsSetStyle)->getFont()->setSize(12);
            $objSheet->getStyle($cellsSetStyle)->getFont()->setBold(true);

            return $objSheet;

        }

        /**
         * 设置sheet名称    => 通用
         * @param $objSheet
         * @param $sheetName
         * @return mixed
         */
        static function setSheetName(&$objSheet,$sheetName){

            $objSheet->setTitle($sheetName);
            return $objSheet;
        }

        /**
         * 获得PHPExcel对象  => 通用
         * @param $objSheet
         * @return \PHPExcel
         */
        static function getObj(&$objSheet){

            import("Org.Util.PHPExcel");
            import("Org.Util.PHPExcel.IOFactory");

            return $objSheet = new \PHPExcel();                     //实例化一个PHPExcel()对象

        }


        static function outputExcel(&$objPHPExcel,$path,$type = 'Excel2007'){
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel,$type);   //设定写入excel的类型

        if( '' == $path ){
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="output.xlsx"');
            header("Content-Transfer-Encoding: binary");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
            $objWriter->save('php://output');
        }else{
            $objWriter->save($path);
        }

    }

        static function upLoadExcelFile(&$objPHPExcel){
            import("Org.Util.PHPExcel");
            import("Org.Util.PHPExcel.IOFactory");

            if (! empty ( $_FILES ['file_stu'] ['name'] )) {
                $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
                $file_types = explode(".", $_FILES ['file_stu']['name']);
                $file_type = $file_types [count($file_types) - 1];
                /*判别是不是.xls文件，判别是不是excel文件*/
                if ((strtolower($file_type) != "xls") && (strtolower($file_type) != "xlsx")) {
                    self::goBack('不是Excel文件，重新上传');
                }
                /*设置上传路径*/
                $savePath = PUBLIC_PATH . '/excel/';
                /*以时间来命名上传的文件*/
                $str = date('Ymdhis');
                $file_name = $str . "." . $file_type;
                /*是否上传成功*/
                if (!copy($tmp_file, $savePath . $file_name)) {
                    self::goBack('上传失败');
                }
            }

            $file_path = $savePath . $file_name;

            if (!file_exists($file_path)) {
                die('上传路径错误!');
            }

            //文件的扩展名
            $ext = strtolower(pathinfo($file_path,PATHINFO_EXTENSION));

            if($ext == 'xlsx'){
                $objReader=\PHPExcel_IOFactory::createReader('Excel2007');
                $objPHPExcel = $objReader->load($file_path,'utf-8');
            }elseif($ext == 'xls'){
                $objReader=\PHPExcel_IOFactory::createReader('Excel5');
                $objPHPExcel = $objReader->load($file_path,'utf-8');
            }

            //删除临时存放的文件
            self::delFile($file_path);

            return $objPHPExcel;

            //获取工作表的名称与数目
        }

        static function getIntCount($count){

            if( false !== $count){
                $count =  intval($count);
            }

            return $count;
        }

        /**
         * 解决中文多字符问题，改方式将中文认为一个字符
         * @param $str
         * @return int
         */
        static function getStrLen($str){
            preg_match_all('/./us', $str, $match);
            return count($match[0]);
        }

        /**
         * 返回从0开始到指定位数的字符串
         * @param $str
         * @param $len
         * @return string
         * 中文截取
         */
        static function getSubString($str,$len){

            return mb_substr($str,0,$len,'utf-8').'...';
        }

        /**
         * 根据传入的字符串，截取图片地址后返回数组
         * @param $str
         * @return mixed
         */
        static function getImgPath($str){

            $newStr =  str_replace("\"","'",$str);

            $preg = '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/i';
            preg_match_all($preg, $newStr, $imgArr);
            return $imgArr[1];
        }

        /**
         * 错误返回
         * @param $msg
         */
		static function goBack($msg){

            echo "<script>alert('$msg');history.back();</script>";

            exit;
        }

        /**
         * 错误返回并刷新页面
         * @param $msg
         */
		static function goBackAndFlash($msg){

            echo "<script>alert('$msg');window.location.href=document.referrer;</script>";

            exit;
        }

        /**
         * 错误返回
         * @param $msg
         */
        static function goReload($msg){
            echo "<script>alert('$msg');location.reload()</script>";
            exit;
        }

        /**
         * 错误关闭
         * @param $msg
         */
        static function goClose($msg){
            echo "<script>alert('$msg');close()</script>";
            exit;
        }

        /**
         * 错误跳转
         * @param $msg
         * @param $url
         */
        static function goToUrl($msg,$url){
            echo "<script>alert('$msg');location='$url'</script>";
            exit;
        }

        /**
         * 删除指定文件
         * @param $path 绝对路径文件
         * @return bool
         */
        static function delFile($path){
            if(file_exists($path)){

                if(unlink($path)){
                    return 1;
                }else{
                    return '删除失败';
                }
            }
            return '文件不存在';

        }

        /**
         * 将时间戳转化为正常时间格式
         * @param $data
         * @return bool|string
         */
        static function formartTime($data){
            return date('Y-m-d H:i:s', $data);
        }

        /**
         * 简单判定是否为二维数组
         * @param $arr
         * @return bool
         */
        static function isTwoArray($arr){
            return (is_array($arr[0])) ? true : false;
        }

        /**
         * 更新session
         */
        static function setNowUserBaseSession(){
            $where['id'] = $_SESSION['uid'];
            $obj = M('m_user')->where($where)->find();

            $_SESSION['username'] = $obj['username'];
            $_SESSION['img']      = $obj['img'];
        }


        /**
         * 清除session
         * 根据传入的name清除指定的额session，不传入则默认清除所有session(退出登录用)
         * @param string $name
         */
        static function clearSession( $name = '' ){

            if( '' == $name){
                if(isset($_SESSION['username'])){
                    unset($_SESSION['username']);
                }

                if(isset($_SESSION['uid'])){
                    unset($_SESSION['uid']);
                }

                if(isset($_SESSION['img'])){
                    unset($_SESSION['img']);
                }

                if(isset($_SESSION['newImg'])){
                    unset($_SESSION['newImg']);
                }

                if(isset($_SESSION['carSystemImg'])){
                    unset($_SESSION['carSystemImg']);
                }

                if(isset($_SESSION['currentUrl'])){
                    unset($_SESSION['currentUrl']);
                }

                if(isset($_SESSION['activeNotice'])){
                    unset($_SESSION['activeNotice']);
                }

                if(isset($_SESSION['activeNoticeCount'])){
                    unset($_SESSION['activeNoticeCount']);
                }
            }else{
                if(isset($_SESSION[$name])){
                    unset($_SESSION[$name]);
                }
            }

        }

        /**
         * 上传图片
         * @param $config
         * @return mixed   正确则返回路径名称 错误则返回错误信息
         */
        static function uploadImg($config){

            if (!empty($_FILES)) {

                $upload = new \Think\Upload($config);// 实例化上传类
                $info = $upload->upload();

                //判断是否有图
                $pathName = '';
                if($info){
                    foreach($info as $file){
                        $pathName .= $file['savepath'].$file['savename'];
                    }
                    $retArr['success'] = 1;
                    $retArr['msg'] = $pathName;
                    $retArr['size'] = $file['size'];
                    $retArr['fileName'] = $file['name'];
                    $retArr['saveName'] = $file['savename'];
                    return $retArr;
                }
                else{
                    $retArr['success'] = 0;
                    $retArr['msg'] = $upload->getError();
                    return $retArr;
                }
            }
        }

        /**
         * 返回当前时间
         * @return false|string
         */
        static function getNowTime(){

            return date('Y-m-d H:i:s', time());
        }


    }