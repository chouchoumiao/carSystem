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


        $file_path = PUBLIC_PATH.'/excel/11111.xls';

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

//                $str .= $objPHPExcel->getActiveSheet()->getCell("$k$j")->getValue() . '\\';//读取单元格
            }

            $strs = explode("\\", $str);


            if($objPHPExcel->getActiveSheet()->getCell("A3") == '日期'){
                $add_data['car_date'] = $strs[0];
            }else{
                ToolModel::goBack('第一列名称必须是日期');
            }

            $add_data['car_month'] = substr($strs[0],0,7);
            $add_data['car_no'] = $strs[1];
            $add_data['car_driver'] = $strs[2];
            $add_data['goods_name'] = $strs[3];
            $add_data['loading_place'] = $strs[4];
            $add_data['unloading_place'] = $strs[5];
            $add_data['loading_tonnage'] = $strs[6];
            $add_data['unloading_tonnage'] = $strs[7];
            $add_data['ticket_number'] = $strs[8];
            $add_data['amount'] = $strs[9];
            $add_data['insert_time'] = ToolModel::getNowTime();
            $add_data['insert_time'] = ToolModel::getNowTime();
            $data[]=$add_data;
            $num++;
        }

        dump($data);
        exit;

//        if (! empty ( $_FILES ['file_stu'] ['name'] ))
//        {
//            $tmp_file = $_FILES ['file_stu'] ['tmp_name'];
//            $file_types = explode ( ".", $_FILES ['file_stu'] ['name'] );
//            $file_type = $file_types [count ( $file_types ) - 1];
//            /*判别是不是.xls文件，判别是不是excel文件*/
//            if (strtolower ( $file_type ) != "xls")
//            {
//                $this->error ( '不是Excel文件，重新上传' );
//            }
//            /*设置上传路径*/
//            $savePath = PUBLIC_PATH . '/excel/';
//            /*以时间来命名上传的文件*/
//            $str = date ( 'Ymdhis' );
//            $file_name = $str . "." . $file_type;
//            /*是否上传成功*/
//            if (! copy ( $tmp_file, $savePath . $file_name ))
//            {
//                $this->error ( '上传失败' );
//            }
//            /*
//               *对上传的Excel数据进行处理生成编程数据,这个函数会在下面第三步的ExcelToArray类中
//              注意：这里调用执行了第三步类里面的read函数，把Excel转化为数组并返回给$res,再进行数据库写入
//            */
//            $res = Service ( 'ExcelToArray' )->read ( $savePath . $file_name );
//
//            dump($res);exit;
//            /*
//                 重要代码 解决Thinkphp M、D方法不能调用的问题
//                 如果在thinkphp中遇到M 、D方法失效时就加入下面一句代码
//             */
//            //spl_autoload_register ( array ('Think', 'autoload' ) );
//            /*对生成的数组进行数据库的写入*/
//            foreach ( $res as $k => $v )
//            {
//                if ($k != 0)
//                {
//                    $data ['uid'] = $v [0];
//                    $data ['password'] = sha1 ( '111111' );
//                    $data ['email'] = $v [1];
//                    $data ['uname'] = $v [3];
//                    $data ['institute'] = $v [4];
//                    $result = M ( 'user' )->add ( $data );
//                    if (! $result)
//                    {
//                        $this->error ( '导入数据库失败' );
//                    }
//                }
//            }
//        }
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