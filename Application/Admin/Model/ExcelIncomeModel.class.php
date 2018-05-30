<?php


/**
 * 利润报表Model
 */
namespace Admin\Model;

set_time_limit (0);

/**
 * 利润报表Model
 * Class ExcelFreightModel
 * @package Admin\Model
 */
	class ExcelIncomeModel {

        /**
         * 取得月份别的货运与费用信息（多个Sheet）
         */
        static function outputExcelIncomeMonthInfo(){

            $objPHPExcel = ToolModel::getObj($objSheet);

            //取得货运数据并填入到指定位置，并设置样式
            self::setFreightMonthData($objPHPExcel);

            //输出到指定地方
            ToolModel::outputExcel($objPHPExcel,'','Excel2007');

            $objPHPExcel->disconnectWorksheets();
            unset($objPHPExcel);

        }


        /**
         * 取得货运并填入到指定位置，并设置样式
         * @param $objPHPExcel
         * @return mixed
         */
        private function setFreightMonthData(&$objPHPExcel){

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
                $data = $model->getIncomeByMonth($sheetName);   //根据月份来取得货运信息

                $costModel = D('Cost');

                //取得当前活动的Sheet
                $objSheet = $objPHPExcel->getActiveSheet($k);

                //设置标题部分
                self::setIncomeTitle($objSheet);

                //取得【油】的累计值
                $data = $costModel->getIncomeCostByName($data,'油');
                //取得【过路费】的累计值
                $data = $costModel->getIncomeCostByName($data,'过路费');
                //取得【其他】的累计值
                $data = $costModel->getIncomeCostByName($data,'其它');


                //设置具体内容
                ToolModel::setDetailCell($objSheet,$data,'L',13);
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
        private function setIncomeTitle(&$objSheet){

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

            $objSheet->getStyle('K')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            $objSheet->getStyle('L')->getNumberFormat()
                ->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);

            $objSheet = ToolModel::setTitle($objSheet,'L');

            //设置项目样式
            $objSheet->setCellValue('A3', '月份');
            $objSheet->setCellValue('B3', '车号');
            $objSheet->setCellValue('C3', '总运费');
            $objSheet->setCellValue('D3', '油');
            $objSheet->setCellValue('E3', '过路费');
            $objSheet->setCellValue('F3', '其他');
            $objSheet->setCellValue('G3', '轮胎');
            $objSheet->setCellValue('H3', '结算工资');
            $objSheet->setCellValue('I3', '工资');
            $objSheet->setCellValue('J3', '保险');
            $objSheet->setCellValue('K3', '余额');
            $objSheet->setCellValue('L3', '备注');

            return $objSheet;

        }

	}