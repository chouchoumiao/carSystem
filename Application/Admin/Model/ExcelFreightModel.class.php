<?php

/**
 * 货运报表Model
 */
namespace Admin\Model;

/**
 * 货运报表Model
 * Class ExcelFreightModel
 * @package Admin\Model
 */
	class ExcelFreightModel {

        /**
         * 导入货运excel表
         * @return array
         */
        static function uploadFreight(){

            import("Org.Util.PHPExcel");
            import("Org.Util.PHPExcel.IOFactory");

            if (! empty ( $_FILES ['file_stu'] ['name'] )) {
                $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
                $file_types = explode(".", $_FILES ['file_stu']['name']);
                $file_type = $file_types [count($file_types) - 1];
                /*判别是不是.xls文件，判别是不是excel文件*/
                if (strtolower($file_type) != "xls") {
                    ToolModel::goBack('不是Excel文件，重新上传');
                }
                /*设置上传路径*/
                $savePath = PUBLIC_PATH . '/excel/';
                /*以时间来命名上传的文件*/
                $str = date('Ymdhis');
                $file_name = $str . "." . $file_type;
                /*是否上传成功*/
                if (!copy($tmp_file, $savePath . $file_name)) {
                    ToolModel::goBack('上传失败');
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

            //获取工作表的数目
            $sheetCount = $objPHPExcel->getSheetCount();

            for ( $i = 0; $i < $sheetCount; $i++ ) {

                if( 1 !=self::getAllSheetSData($i,$objPHPExcel)){
                    unlink($file_path);
                    return false;
                }

            }

            unlink($file_path);
            return true;

        }

        /**
         ** 输出所有的货运信息
         * @param string $sheetName
         * @param $path
         */
        static function outputExcelFreightInfo($sheetName='sheet',$path){

            $objPHPExcel = self::getObj($objSheet);

            $objSheet = $objPHPExcel->getActiveSheet();

            //设置sheetName
            self::setSheetName($objSheet,$sheetName);

            //设置标题部分
            self::setTitle($objSheet);

            //取得数据并填入到指定位置，并设置样式
            self::setAllData($objSheet);

            //输出到指定地方
            self::outputExcel($objPHPExcel,$path,'Excel2007');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

        }

        /**
         * 取得数据并填入到指定位置，并设置样式
         * @param $objSheet
         * @return mixed
         */
        private function setAllData(&$objSheet){

            //填入数据
            //取得数据
            $data = D('Freight')->getFreightExcelInfo();

            //设置具体内容
            self::setCell($objSheet,$data);

            return $objSheet;

        }


        /**
         * 取得月份别的货运信息（多个Sheet）
         */
        static function outputExcelFreightMonthInfo(){

            $objPHPExcel = self::getObj($objSheet);

            //取得数据并填入到指定位置，并设置样式
            self::setMonthData($objPHPExcel);

            //输出到指定地方
            self::outputExcel($objPHPExcel,'','Excel2007');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

        }

        /**
         * 取得数据并填入到指定位置，并设置样式
         * @param $objPHPExcel
         * @return mixed
         */
        private function setMonthData(&$objPHPExcel){

            $model = D('Freight');
            //取得不重复的月份数据
            $month = $model->getNorepeatMonth();

            for($k=0;$k<count($month);$k++){

                //取得Sheet名
                $sheetName = $month[$k]['car_month'];

                //自动生成Sheet
                self::creatNewSheet($objPHPExcel,$k,$sheetName);

                //取得需要填入相应Sheet的数据
                $data = $model->getInfoByMonth($sheetName);

                //取得当前活动的Sheet
                $objSheet = $objPHPExcel->getActiveSheet($k);

                //设置标题部分
                self::setTitle($objSheet);

                //设置具体内容
                self::setCell($objSheet,$data);
            }

            return $objPHPExcel;
        }


        /*********************************************************************************************************/
        /**
         * 自动生成Sheet
         * @param $objPHPExcel
         * @param $k
         * @param $sheetName
         * @return mixed
         */
        private function creatNewSheet(&$objPHPExcel,$k,$sheetName){
            //默认已经有一个sheet名叫 worksheet，所有第一个不用建立
            if($k >0 ){
                $objPHPExcel->createSheet();
            }
            $objPHPExcel->setactivesheetindex($k);

            $objSheet = $objPHPExcel->getActiveSheet($k);

            /** 设置工作表名称 */
            $objSheet->setTitle($sheetName);

            return $objPHPExcel;


        }

        /**
         * 设置标题部分    => 通用
         * @param $objSheet
         * @return mixed
         */
        private function setTitle(&$objSheet){

            //设置每列格式

            //A为日期格式
            $objSheet->getStyle('F')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD);

            //发货吨位为保留两位小数
            $objSheet->getStyle('F')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
            //收货吨位为保留两位小数
            $objSheet->getStyle('G')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            //金额为保留两位小数
            $objSheet->getStyle('I')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);


            //设置第一行样式
            $objSheet->setCellValue('A1', '嵊州市锡锋物流运输公司');

            $objSheet->mergeCells('A1:I2');
            $objSheet->getStyle('A1')->getFont()->setName('宋体');
            $objSheet->getStyle('A1')->getFont()->setSize(24);
            $objSheet->getStyle('A1')->getFont()->setBold(true);

            //设置项目样式
            $objSheet->setCellValue('A3', '日期');
            $objSheet->setCellValue('B3', '车号');
            $objSheet->setCellValue('C3', '货物名称');
            $objSheet->setCellValue('D3', '装货地');
            $objSheet->setCellValue('E3', '卸货地');
            $objSheet->setCellValue('F3', '发货吨位');
            $objSheet->setCellValue('G3', '卸货吨位');
            $objSheet->setCellValue('H3', '票号');
            $objSheet->setCellValue('I3', '金额');

            $objSheet->getStyle('A3:I3')->getFont()->setName('宋体');
            $objSheet->getStyle('A3:I3')->getFont()->setSize(12);
            $objSheet->getStyle('A3:I3')->getFont()->setBold(true);

            return $objSheet;

        }

        /**
         * 设置sheet名称    => 通用
         * @param $objSheet
         * @param $sheetName
         * @return mixed
         */
        private function setSheetName(&$objSheet,$sheetName){

            $objSheet->setTitle($sheetName);
            return $objSheet;
        }

        /**
         * 获得PHPExcel对象  => 通用
         * @param $objSheet
         * @return \PHPExcel
         */
        private function getObj(&$objSheet){

            import("Org.Util.PHPExcel");
            import("Org.Util.PHPExcel.IOFactory");

            return $objSheet = new \PHPExcel();                     //实例化一个PHPExcel()对象

        }

        /**
         * 输出具体数据并设置格式  => 通用
         * @param $objSheet
         * @param $data
         * @return mixed
         */
        private function setCell(&$objSheet,$data){
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
                $objSheet->getStyle( 'A'.$i.':I'.$i)->applyFromArray($styleThinBlackBorderOutline);
                $objSheet->getStyle('A'.$i.':I'.$i )->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
            }

            //所有单元格都设置垂直居中显示
            $objStyleA = $objSheet->getStyle('A1:I'.$count);
            $objAlignA = $objStyleA->getAlignment();
            $objAlignA->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);    //左右居中
            $objAlignA->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);  //上下居中

            //设置单元格长度
            $objSheet->getDefaultColumnDimension()->setWidth(13);

            return $objSheet;
        }

        /**
         * 输出到指定位置   => 通用
         * @param $objPHPExcel
         * @param $path
         * @param string $type
         */
        private function outputExcel(&$objPHPExcel,$path,$type = 'Excel2007'){
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

        /**
         * 导入货运信息
         * @param $i
         * @param $objPHPExcel
         * @return int
         */
        private function getAllSheetSData($i,&$objPHPExcel){

            $sheet = $objPHPExcel->getSheet( $i ) ;

            $highestRow = $sheet->getHighestRow(); // 取得总行数

            for($j=4;$j<=$highestRow;$j++) {
                $isAllNull = 0;

                if($objPHPExcel->getActiveSheet()->getCell("A3") == '日期'){
                    $car_date = $sheet->getCellByColumnAndRow(0, $j)->getValue();

                    if( '' != strval($car_date)){
                        $add_data['car_date'] = gmdate("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($car_date));
                        $add_data['car_month'] = substr($add_data['car_date'],0,7);
                    }else{
                        $add_data['car_date'] = NULL;
                        $add_data['car_month'] = '';
                    }

                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第一列名称必须是日期');
                }

                if($objPHPExcel->getActiveSheet()->getCell("B3") == '车号'){
                    $car_no = $sheet->getCellByColumnAndRow(1, $j)->getValue();
                    if(is_object($car_no))  $car_no = $car_no->__toString();
                    $add_data['car_no'] = $car_no;
                    if( '' == $car_no){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第二列名称必须是车号');
                }

                if($objPHPExcel->getActiveSheet()->getCell("C3") == '货物名称'){
                    $goods_name = $sheet->getCellByColumnAndRow(2, $j)->getValue();
                    if(is_object($goods_name))  $goods_name = $goods_name->__toString();
                    $add_data['goods_name'] = $goods_name;
                    if( '' == $goods_name){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第三列名称必须是货物名称');
                }

                if($objPHPExcel->getActiveSheet()->getCell("D3") == '装货地'){
                    $loading_place = $sheet->getCellByColumnAndRow(3, $j)->getValue();
                    if(is_object($loading_place))  $loading_place = $loading_place->__toString();
                    $add_data['loading_place'] = $loading_place;
                    if( '' == $loading_place){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第四列名称必须是装货地');
                }

                if($objPHPExcel->getActiveSheet()->getCell("E3") == '卸货地'){
                    $unloading_place = $sheet->getCellByColumnAndRow(4, $j)->getValue();
                    if(is_object($unloading_place))  $unloading_place = $unloading_place->__toString();
                    $add_data['unloading_place'] = $unloading_place;
                    if( '' == $unloading_place){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第五列名称必须是卸货地');
                }

                if($objPHPExcel->getActiveSheet()->getCell("F3") == '发货吨位'){
                    $loading_tonnage = $sheet->getCellByColumnAndRow(5, $j)->getValue();
                    if(is_object($loading_tonnage))  $loading_tonnage = $loading_tonnage->__toString();
                    $add_data['loading_tonnage'] = $loading_tonnage;
                    if( '' == $loading_tonnage){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第六列名称必须是发货吨位');
                }

                if($objPHPExcel->getActiveSheet()->getCell("G3") == '收货吨位'){
                    $unloading_tonnage = $sheet->getCellByColumnAndRow(6, $j)->getValue();
                    if(is_object($unloading_tonnage))  $unloading_tonnage = $unloading_tonnage->__toString();
                    $add_data['unloading_tonnage'] = $unloading_tonnage;
                    if( '' == $unloading_tonnage){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第七列名称必须是是收货吨位');
                }

                if($objPHPExcel->getActiveSheet()->getCell("H3") == '票号'){
                    $ticket_number = $sheet->getCellByColumnAndRow(7, $j)->getValue();
                    if(is_object($ticket_number))  $ticket_number = $ticket_number->__toString();
                    $add_data['ticket_number'] = $ticket_number;
                    if( '' == $ticket_number){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第八列名称必须是票号');
                }

                if($objPHPExcel->getActiveSheet()->getCell("I3") == '金额'){
                    $amount = $sheet->getCellByColumnAndRow(8, $j)->getValue();
                    if(is_object($amount))  $amount = $amount->__toString();
                    $add_data['amount'] = $amount;
                    if( '' == $amount){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('第'.($i + 1).'个Sheet中的第九列名称必须是金额');
                }

                $add_data['insert_time'] = ToolModel::getNowTime();
                $add_data['edit_time'] = ToolModel::getNowTime();
                $data[]=$add_data;

                //如果渠取到最后一行都为空，则丢弃这一行并且下面的都不进行获取
                if( 8 == $isAllNull){
                    array_pop($data);
                    break;
                }
            }
            if(false === D('Freight')->addFreightInfo($data)){
                ToolModel::goBack('第'.($i + 1).'个Sheet中的数据导入失败');
                exit;
            }
            return 1;
        }

	}