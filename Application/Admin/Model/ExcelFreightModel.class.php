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

	}