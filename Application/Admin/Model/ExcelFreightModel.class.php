<?php


/**
 * 货运报表Model
 */
namespace Admin\Model;

set_time_limit (0);

/**
 * 货运报表Model
 * Class ExcelFreightModel
 * @package Admin\Model
 */
	class ExcelFreightModel {


        /**
         ** 输出所有的费用信息
         * @param string $sheetName
         * @param $path
         */
        static function outputExcelFreightInfoByCase($sheetName='sheet',$path){

            $objPHPExcel = ToolModel::getObj($objSheet);

            $objSheet = $objPHPExcel->getActiveSheet();

            //设置sheetName
            ToolModel::setSheetName($objSheet,$sheetName);

            //设置标题部分
            self::setFreightTitle($objSheet);

            //取得数据并填入到指定位置，并设置样式
            self::setFreightDataByCase($objSheet);

            //输出到指定地方
            ToolModel::outputExcel($objPHPExcel,$path,'Excel2007');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

        }


        /**
         * 导入货运excel表
         * @return array
         */
        static function uploadFreight(){

            $objPHPExcel = null;
            $objPHPExcel = ToolModel::upLoadExcelFile($objPHPExcel);
            $sheetNames = $objPHPExcel->getSheetNames();
            $sheetCount = count($sheetNames);

            //多Sheet追加导入
            for ( $i = 0; $i < $sheetCount; $i++ ) {

                if( 1 != self::getAllSheetSFreightData($sheetNames[$i],$objPHPExcel)){
                    return false;
                }
            }

            return true;

        }

        /**
         ** 输出所有的货运信息
         * @param string $sheetName
         * @param $path
         */
        static function outputExcelFreightInfo($sheetName='sheet',$path){

            $objPHPExcel = ToolModel::getObj($objSheet);

            $objSheet = $objPHPExcel->getActiveSheet();

            //设置sheetName
            ToolModel::setSheetName($objSheet,$sheetName);

            //设置标题部分
            self::setFreightTitle($objSheet);

            //取得数据并填入到指定位置，并设置样式
            self::setAllData($objSheet);

            //输出到指定地方
            ToolModel::outputExcel($objPHPExcel,$path,'Excel2007');

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
            ToolModel::setDetailCell($objSheet,$data,'J',13);

            return $objSheet;

        }

        /**
         * 取得选择的开始与结束日期的数据并写入到Excel
         * @param $objSheet
         * @return mixed
         */
        private function setDateSerachData(&$objSheet){

            //填入数据
            //取得数据
            $data = D('Freight')->getInfoByDateSearchForExport();

            //设置具体内容
            ToolModel::setDetailCell($objSheet,$data,'J',13);

            return $objSheet;

        }


        /**
         * 取得月份别的货运信息（多个Sheet）
         */
        static function outputExcelFreightMonthInfo(){

            $objPHPExcel = ToolModel::getObj($objSheet);

            //取得数据并填入到指定位置，并设置样式
            self::setMonthData($objPHPExcel);

            //输出到指定地方
            ToolModel::outputExcel($objPHPExcel,'','Excel2007');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

        }

        /**
         * 按照选择的开始于结束日期导出对应数据
         * @param string $sheetName
         * @param $path
         */
        static function OutputExcelFreightDateSerachData($sheetName='sheet',$path=''){
            $objPHPExcel = ToolModel::getObj($objSheet);

            $objSheet = $objPHPExcel->getActiveSheet();

            //设置sheetName
            ToolModel::setSheetName($objSheet,$sheetName);

            //设置标题部分
            self::setFreightTitle($objSheet);

            //取数据并填入到指定位置，并设置样式
            if(isset($_POST['exportDateInfo'])){      //是选择开始与结束日期的条件处过来的导出的Excel

                self::setDateSerachData($objSheet);

            }else{

                ToolModel::goBack('传递参数错误');
                exit;

            }

            //输出到指定地方
            ToolModel::outputExcel($objPHPExcel,$path,'Excel2007');

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
                if( '' == strval($sheetName) ){
                    $sheetName = '空';
                }

                //自动生成Sheet
                self::creatNewSheet($objPHPExcel,$k,$sheetName);

                //取得需要填入相应Sheet的数据
                $data = $model->getInfoByMonth($sheetName);

                //取得当前活动的Sheet
                $objSheet = $objPHPExcel->getActiveSheet($k);

                //设置标题部分
                self::setFreightTitle($objSheet);

                //设置具体内容
                ToolModel::setDetailCell($objSheet,$data,'J',13);
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
            ToolModel::setSheetName($objSheet,$sheetName);

            return $objPHPExcel;


        }

        /**
         * 设置标题部分    => 通用
         * @param $objSheet
         * @return mixed
         */
        private function setFreightTitle(&$objSheet){

            //设置每列格式

            //A为日期格式
            $objSheet->getStyle('A')->getNumberFormat()
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

            //$objSheet = ToolModel::setTitle($objSheet,'I');//金额为保留两位小数
            //新增加单价为保留两位小数
            $objSheet->getStyle('J')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            $objSheet = ToolModel::setTitle($objSheet,'J');

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
            $objSheet->setCellValue('J3', '单价');

            return $objSheet;

        }

        /**
         * 导入货运信息
         * @param $sheetName
         * @param $objPHPExcel
         * @return int
         */
        private function getAllSheetSFreightData($sheetName,&$objPHPExcel){

            $sheet = $objPHPExcel->getSheetByName($sheetName);

            $highestRow = $sheet->getHighestRow(); // 取得总行数

            for($j=4;$j<=$highestRow;$j++) {
                $isAllNull = 0;

                if($sheet->getCell("A3") == '日期'){
                    $car_date = $sheet->getCellByColumnAndRow(0, $j)->getValue();

                    if( '' != strval($car_date)){
                        $add_data['car_date'] = gmdate("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($car_date));
                        $add_data['car_month'] = substr($add_data['car_date'],0,7);
                    }else{
                        $add_data['car_date'] = NULL;
                        $add_data['car_month'] = '';
                    }

                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的A列名称必须是[日期]');
                }

                if($sheet->getCell("B3") == '车号'){
                    $car_no = $sheet->getCellByColumnAndRow(1, $j)->getValue();
                    if(is_object($car_no))  $car_no = $car_no->__toString();
                    $add_data['car_no'] = $car_no;
                    if( '' == $car_no){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的B列名称必须是[车号]');
                }

                if($sheet->getCell("C3") == '货物名称'){
                    $goods_name = $sheet->getCellByColumnAndRow(2, $j)->getValue();
                    if(is_object($goods_name))  $goods_name = $goods_name->__toString();
                    $add_data['goods_name'] = $goods_name;
                    if( '' == $goods_name){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的C列名称必须是[货物名称]');
                }

                if($sheet->getCell("D3") == '装货地'){
                    $loading_place = $sheet->getCellByColumnAndRow(3, $j)->getValue();
                    if(is_object($loading_place))  $loading_place = $loading_place->__toString();
                    $add_data['loading_place'] = $loading_place;
                    if( '' == $loading_place){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的D列名称必须是[装货地]');
                }

                if($sheet->getCell("E3") == '卸货地'){
                    $unloading_place = $sheet->getCellByColumnAndRow(4, $j)->getValue();
                    if(is_object($unloading_place))  $unloading_place = $unloading_place->__toString();
                    $add_data['unloading_place'] = $unloading_place;
                    if( '' == $unloading_place){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的E列名称必须是[卸货地]');
                }

                if($sheet->getCell("F3") == '发货吨位'){
                    $loading_tonnage = $sheet->getCellByColumnAndRow(5, $j)->getValue();
                    if(is_object($loading_tonnage))  $loading_tonnage = $loading_tonnage->__toString();
                    $add_data['loading_tonnage'] = $loading_tonnage;
                    if( '' == $loading_tonnage){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的F列名称必须是[发货吨位]');
                }

                if($sheet->getCell("G3") == '收货吨位'){
                    $unloading_tonnage = $sheet->getCellByColumnAndRow(6, $j)->getValue();
                    if(is_object($unloading_tonnage))  $unloading_tonnage = $unloading_tonnage->__toString();
                    $add_data['unloading_tonnage'] = $unloading_tonnage;
                    if( '' == $unloading_tonnage){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的G列名称必须是是[收货吨位]');
                }

                if($sheet->getCell("H3") == '票号'){
                    $ticket_number = $sheet->getCellByColumnAndRow(7, $j)->getValue();
                    if(is_object($ticket_number))  $ticket_number = $ticket_number->__toString();
                    $add_data['ticket_number'] = $ticket_number;
                    if( '' == $ticket_number){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的H列名称必须是[票号]');
                }

                if($sheet->getCell("I3") == '金额'){
                    $amount = $sheet->getCellByColumnAndRow(8, $j)->getValue();
                    if(is_object($amount))  $amount = $amount->__toString();
                    $add_data['amount'] = $amount;
                    if( '' == $amount){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的I列名称必须是[金额]');
                }
                //新增加客户名称
                if($sheet->getCell("J3") == '客户名称'){
                    $customer = $sheet->getCellByColumnAndRow(9, $j)->getValue();
                    if(is_object($customer))  $customer = $customer->__toString();
                    $add_data['customer'] = $customer;
                    if( '' == $customer){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的J列名称必须是[客户名称]');
                }
                //新增加单价
                if($sheet->getCell("K3") == '单价'){
                    $price = $sheet->getCellByColumnAndRow(10, $j)->getValue();
                    if(is_object($price))  $price = $price->__toString();
                    $add_data['price'] = $price;
                    if( '' == $price){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的K列名称必须是[单价]');
                }

                $add_data['insert_time'] = ToolModel::getNowTime();
                $add_data['edit_time'] = ToolModel::getNowTime();
                $data[]=$add_data;

                //如果渠取到最后一行都为空，则丢弃这一行并且下面的都不进行获取
                if( 9 == $isAllNull){
                    array_pop($data);
                    break;
                }
            }
            if(false === D('Freight')->addFreightInfo($data)){
                ToolModel::goBack('Sheet名【'.$sheetName.'】中的数据导入失败');
                exit;
            }
            return 1;
        }

        private function setFreightDataByCase(&$objSheet){

            //填入数据
            //取得数据
            $data = $_SESSION['freightSerachData'];

            //设置具体内容
            ToolModel::setDetailCell($objSheet,$data,'J',13);

            return $objSheet;

        }

	}