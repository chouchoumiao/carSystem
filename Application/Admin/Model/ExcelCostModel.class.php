<?php


/**
 * 货运报表Model
 */
namespace Admin\Model;

set_time_limit (0);

/**
 * 费用报表Model
 * Class ExcelFreightModel
 * @package Admin\Model
 */
	class ExcelCostModel {


        /**
         ** 输出所有的费用信息
         * @param string $sheetName
         * @param $path
         */
        static function outputExcelCostInfo($sheetName='sheet',$path){

            $objPHPExcel = ToolModel::getObj($objSheet);

            $objSheet = $objPHPExcel->getActiveSheet();

            //设置sheetName
            ToolModel::setSheetName($objSheet,$sheetName);

            //设置标题部分
            self::setCostTitle($objSheet);

            //取得数据并填入到指定位置，并设置样式
            self::setCostData($objSheet);

            //输出到指定地方
            ToolModel::outputExcel($objPHPExcel,$path,'Excel2007');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

        }

        /**
         * 设置标题部分    => 通用
         * @param $objSheet
         * @return mixed
         */
        private function setCostTitle(&$objSheet){

            //设置每列格式

            //A为日期格式
            $objSheet->getStyle('A')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD);

            //发货费用金额为保留两位小数
            $objSheet->getStyle('E')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);


            $objSheet = ToolModel::setTitle($objSheet,'F');

            //设置项目样式
            $objSheet->setCellValue('A3', '车牌号');
            $objSheet->setCellValue('B3', '日期');
            $objSheet->setCellValue('C3', '驾驶员');
            $objSheet->setCellValue('D3', '费用内容');
            $objSheet->setCellValue('E3', '费用金额');
            $objSheet->setCellValue('F3', '备注');

            return $objSheet;

        }

        private function setCostData(&$objSheet){

            //填入数据
            //取得数据
            $data = $_SESSION['costSerachData'];

            //新需求，将导出数据按照日期升序进行导出 wujiayu 20180731
            foreach($data as $key=>$v){
                $data[$key]['cost_date'] = date("Y-m-d",strtotime($v['cost_date']));
            }
            $newDate = array();

            foreach ($data as $info) {
                $newDate[] = $info['cost_date'];
            }
            array_multisort($newDate,SORT_ASC,$data);

            //设置具体内容
            ToolModel::setDetailCell($objSheet,$data,'F',13);

            return $objSheet;

        }

	}