<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/16
 * Time: 20:49
 * 货运类管理控制器
 */

namespace Admin\Controller;

use Admin\Model\ToolModel;
use Think\Controller;
use Admin\Model\ExcelFreightModel;

class ReportController extends Controller{

    public function doAction(){

        $action = $_GET['action'];
        if( isset($action) && '' != $action ){
            switch($action){
                //货运报表相关
                case 'freight':
                    $this->freight();
                    break;
                case 'freightAll':
                    $this->freightAll();
                    break;
                case 'freightMonth':
                    $this->freightMonth();
                    break;
                case 'showInputFreight':
                    $this->showInputFreight();
                    break;
                case 'uploadFreight':
                    $this->uploadFreight();
                    break;

//                //编辑货运信息
//                case 'the':
//                    $this->the();
//                    break;
//                case 'update':
//                    $this->update();
//                    break;
//                case 'del':
//                    $this->del();
//                    break;
//                case 'addShow':
//                    $this->addShow();
//                    break;
//                case 'add':
//                    $this->add();
//                    break;

            }
        }

    }


    private function uploadFreight(){

        import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.IOFactory");

        if (! empty ( $_FILES ['file_stu'] ['name'] )) {
            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
            $file_types = explode(".", $_FILES ['file_stu']['name']);
            $file_type = $file_types [count($file_types) - 1];
            /*判别是不是.xls文件，判别是不是excel文件*/
            if (strtolower($file_type) != "xls") {
                $this->error('不是Excel文件，重新上传');
            }
            /*设置上传路径*/
            $savePath = PUBLIC_PATH . '/excel/';
            /*以时间来命名上传的文件*/
            $str = date('Ymdhis');
            $file_name = $str . "." . $file_type;
            /*是否上传成功*/
            if (!copy($tmp_file, $savePath . $file_name)) {
                $this->error('上传失败');
            }
        }

        $file_path = $savePath . $file_name;

        if (!file_exists($file_path)) {
            die('no file!');
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
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $highestColumn = $sheet->getHighestColumn(); // 取得总列数
        $num = 0;

        for($j=4;$j<=$highestRow;$j++) {
            $str = '';
            for ($k = 'A'; $k <= $highestColumn; $k++) {

                if($k=='A'){//指定H列为时间所在列
                    $str .= gmdate("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue())). '\\';
                }else{
                    $str .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue(). '\\';
                }
            }

            $isAllNull = 0;
            $strs = explode("\\", $str);


            if($objPHPExcel->getActiveSheet()->getCell("A3") == '日期'){
                $add_data['car_date'] = $strs[0];
                $add_data['car_month'] = substr($strs[0],0,7);
                if( '' == $strs[0]){
                    $isAllNull = $isAllNull + 1;
                }

            }else{
                ToolModel::goBack('第一列名称必须是日期');
            }

            if($objPHPExcel->getActiveSheet()->getCell("B3") == '车号'){
                $add_data['car_no'] = $strs[1];
                if( '' == $strs[1]){
                    $isAllNull = $isAllNull + 1;
                }
            }else{
                ToolModel::goBack('第二列名称必须是车号');
            }

            if($objPHPExcel->getActiveSheet()->getCell("C3") == '货物名称'){
                $add_data['goods_name'] = $strs[2];
                if( '' == $strs[2]){
                    $isAllNull = $isAllNull + 1;
                }
            }else{
                ToolModel::goBack('第三列名称必须是货物名称');
            }

            if($objPHPExcel->getActiveSheet()->getCell("D3") == '装货地'){
                $add_data['loading_place'] = $strs[3];
                if( '' == $strs[3]){
                    $isAllNull = $isAllNull + 1;
                }
            }else{
                ToolModel::goBack('第四列名称必须是装货地');
            }

            if($objPHPExcel->getActiveSheet()->getCell("E3") == '卸货地'){
                $add_data['unloading_place'] = $strs[4];
                if( '' == $strs[4]){
                    $isAllNull = $isAllNull + 1;
                }
            }else{
                ToolModel::goBack('第五列名称必须是卸货地');
            }

            if($objPHPExcel->getActiveSheet()->getCell("F3") == '发货吨位'){
                if( '' == $strs[5]){
                    $isAllNull = $isAllNull + 1;
                }
                $add_data['loading_tonnage'] = $strs[5];
            }else{
                ToolModel::goBack('第六列名称必须是发货吨位');
            }

            if($objPHPExcel->getActiveSheet()->getCell("G3") == '收货吨位'){
                if( '' == $strs[6]){
                    $isAllNull = $isAllNull + 1;
                }
                $add_data['unloading_tonnage'] = $strs[6];
            }else{
                ToolModel::goBack('第七列名称必须是收货吨位');
            }

            if($objPHPExcel->getActiveSheet()->getCell("H3") == '票号'){
                if( '' == $strs[7]){
                    $isAllNull = $isAllNull + 1;
                }
                $add_data['ticket_number'] = $strs[7];
            }else{
                ToolModel::goBack('第八列名称必须是金额');
            }

            if($objPHPExcel->getActiveSheet()->getCell("I3") == '金额'){
                $add_data['amount'] = $strs[8];
                if( '' == $strs[8]){
                    $isAllNull = $isAllNull + 1;
                }
            }else{
                ToolModel::goBack('第九列名称必须是金额');
            }

            $add_data['insert_time'] = ToolModel::getNowTime();
            $add_data['edit_time'] = ToolModel::getNowTime();
            $data[]=$add_data;

            //如果渠取到最后一行都为空，则丢弃这一行并且下面的都不进行获取
            if( 8 == $isAllNull){
                array_pop($data);
                break;
            }else{
                $num++;
            }
        }

        if( false !== D('Freight')->addFreightInfo($data)){
            ToolModel::goBack('导入成功');
        }else {
            ToolModel::goBack('导入失败');
        }

    }


    private function showInputFreight(){

        $this->display('freight_input_info');
    }

    private function freight(){

        $this->display('freightShow');
    }

    /**
     * 导出所有货运信息
     * 按照日期升序排序
     */
    private function freightAll(){
        ExcelFreightModel::outputExcelFreightInfo('货运全数据','');
    }

    /**
     * 按月导出所有货运信息
     * 按照日期升序排序
     */
    private function freightMonth(){

        ExcelFreightModel::outputExcelFreightMonthInfo();
    }

}