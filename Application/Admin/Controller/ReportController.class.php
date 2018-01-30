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
use Admin\Model\ExcelCarInfoModel;

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
                case 'freightDateSerach':
                    $this->freightDateSerach();
                    break;
                case 'showInputFreight':
                    $this->showInputFreight();
                    break;
                case 'uploadFreight':
                    $this->uploadFreight();
                    break;

                //车辆信息报表相关
                case 'car':
                    $this->car();
                    break;
                case 'carAll':
                    $this->carAll();
                    break;
                case 'showInputCar':
                    $this->showInputCar();
                    break;

                case 'uploadCarInfo':
                    $this->uploadCarInfo();
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


    /**
     * * * * * * * * * * * * * * * * * * * * * * * 车辆信息相关* * * * * * * * * * * * * * * * * * * * * * *
     * ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
     */

    /**
     * 导出所有车辆信息报表
     */
    private function carAll(){
        ExcelCarInfoModel::outputExcelCarInfo('车辆信息全数据','');
    }

    /**
     * 显示车辆信息页面
     */
    private function car(){

        $this->display('carShow');
    }

    private function showInputCar(){
        $this->display('car_input_info');
    }

    private function uploadCarInfo(){

        if( ExcelCarInfoModel::uploadCarInfo() ){
            ToolModel::goBack('导入成功');
        }else {
            ToolModel::goBack('导入失败');
        }

    }


    /**
     * ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
     * * * * * * * * * * * * * * * * * * * * * * * 车辆信息相关* * * * * * * * * * * * * * * * * * * * * * *
     */




    /**
     * * * * * * * * * * * * * * * * * * * * * * * 货运信息相关* * * * * * * * * * * * * * * * * * * * * * *
     * ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
     */

    /**
     * 导入Excel写入数据库
     */
    private function uploadFreight(){

        if( ExcelFreightModel::uploadFreight() ){
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

    /**
     * 导出按开始日期与结束日期的查询数据
     */
    private function freightDateSerach(){

    }
    /**
     * ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
     * * * * * * * * * * * * * * * * * * * * * * * 货运信息相关* * * * * * * * * * * * * * * * * * * * * * *
     */

}