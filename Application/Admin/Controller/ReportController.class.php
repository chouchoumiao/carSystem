<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/16
 * Time: 20:49
 * 货运类管理控制器
 */

namespace Admin\Controller;

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