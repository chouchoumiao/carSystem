<?php


/**
 * 货运报表Model
 */
namespace Admin\Model;

set_time_limit (0);

/**
 * 车辆信息报表Model
 * Class ExcelCarInfoModel
 * @package Admin\Model
 */
	class ExcelCarInfoModel {


        /**
         ** 输出所有的货运信息
         * @param string $sheetName
         * @param $path
         */
        static function outputExcelCarInfo($sheetName='sheet',$path){

            $objPHPExcel = ToolModel::getObj($objSheet);

            $objSheet = $objPHPExcel->getActiveSheet();

            //设置sheetName
            ToolModel::setSheetName($objSheet,$sheetName);

            //设置标题部分
            self::setCarTitle($objSheet);

            //取得数据并填入到指定位置，并设置样式
            self::setAllData($objSheet);

            //输出到指定地方
            ToolModel::outputExcel($objPHPExcel,$path,'Excel2007');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

        }

        private function setAllData(&$objSheet){

            //填入数据
            //取得数据
            $data = D('Car')->getCarExcelInfo();

            //设置具体内容
            ToolModel::setDetailCell($objSheet,$data,'E',15);

            return $objSheet;

        }


        /**
         * 导入车辆excel表
         * @return array
         */
        static function uploadCarInfo(){

            $objPHPExcel = null;
            $objPHPExcel = ToolModel::upLoadExcelFile($objPHPExcel);
            $sheetNames = $objPHPExcel->getSheetNames();
            $sheetCount = count($sheetNames);

            //多Sheet追加导入
            for ( $i = 0; $i < $sheetCount; $i++ ) {

                if( 1 != self::getAllSheetSCarData($sheetNames[$i],$objPHPExcel)){
                    return false;
                }
            }
            return true;
        }


        /**
         * 导入车辆信息
         * @param $sheetName
         * @param $objPHPExcel
         * @return int
         */
        private function getAllSheetSCarData($sheetName,&$objPHPExcel){

            $sheet = $objPHPExcel->getSheetByName($sheetName);

            $highestRow = $sheet->getHighestRow(); // 取得总行数

            for($j=2;$j<=$highestRow;$j++) {
                $isAllNull = 0;

                if($sheet->getCell("A1") == '车号'){
                    $car_no = $sheet->getCellByColumnAndRow(0, $j)->getValue();
                    if(is_object($car_no))  $car_no = $car_no->__toString();
                    $add_data['car_no'] = $car_no;
                    if( '' == $car_no){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的A列名称必须是车号');
                }

                if($sheet->getCell("B1") == '车架号'){
                    $car_frame = $sheet->getCellByColumnAndRow(1, $j)->getValue();
                    if(is_object($car_frame))  $car_frame = $car_frame->__toString();
                    $add_data['car_frame'] = $car_frame;
                    if( '' == $car_frame){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的B列名称必须是车架号');
                }

                if($sheet->getCell("C1") == '车主'){
                    $car_owner = $sheet->getCellByColumnAndRow(2, $j)->getValue();
                    if(is_object($car_owner))  $car_owner = $car_owner->__toString();
                    $add_data['car_owner'] = $car_owner;
                    if( '' == $car_owner){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的C列名称必须是车主');
                }

                if($sheet->getCell("D1") == '保单到期日'){
                    $car_insurance_expires = $sheet->getCellByColumnAndRow(3, $j)->getValue();
                    if(is_object($car_insurance_expires))  $car_insurance_expires = $car_insurance_expires->__toString();
                    $add_data['car_insurance_expires'] = $car_insurance_expires;
                    if( '' == $car_insurance_expires){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的D列名称必须是保单到期日');
                }

                if($sheet->getCell("E1") == '保险公司'){
                    $car_insurance_name = $sheet->getCellByColumnAndRow(4, $j)->getValue();
                    if(is_object($car_insurance_name))  $car_insurance_name = $car_insurance_name->__toString();
                    $add_data['car_insurance_name'] = $car_insurance_name;
                    if( '' == $car_insurance_name){
                        $isAllNull = $isAllNull + 1;
                    }
                }else{
                    ToolModel::goBack('Sheet名【'.$sheetName.'】中的E列名称必须是保险公司');
                }

                $add_data['insert_time'] = ToolModel::getNowTime();
                $add_data['edit_time'] = ToolModel::getNowTime();
                $data[]=$add_data;

                //如果渠取到最后一行都为空，则丢弃这一行并且下面的都不进行获取
                if( 5 == $isAllNull){
                    array_pop($data);
                    break;
                }
            }
            if(false === D('Car')->addCarInfo($data)){
                ToolModel::goBack('Sheet名【'.$sheetName.'】中的数据导入失败');
                exit;
            }
            return 1;
        }


        /**
         * 设置标题部分    => 通用
         * @param $objSheet
         * @return mixed
         */
        private function setCarTitle(&$objSheet){

            //设置每列格式

            //C为日期格式
            $objSheet->getStyle('D')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD);

            $objSheet = ToolModel::setTitle($objSheet,'E');

            //设置项目样式
            $objSheet->setCellValue('A3', '车号');
            $objSheet->setCellValue('B3', '车架号');
            $objSheet->setCellValue('C3', '车主');
            $objSheet->setCellValue('D3', '保单到期日');
            $objSheet->setCellValue('E3', '保险公司');

            return $objSheet;

        }

	}