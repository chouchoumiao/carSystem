<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 车辆管理类
 */

namespace Admin\Controller;

use Admin\Model\ToolModel;
use Admin\Model\ValidateModel;
use Think\Controller;

class CarController extends Controller{

    private $_model;

    public function doAction(){

        $action = $_GET['action'];
        if( isset($action) && '' != $action ){
            $this->_model = D('Car');

            switch($action){
                //取得所有车辆信息(分页)
                case 'all':
                    $this->all();
                    break;
                //编辑车辆信息
                case 'the':
                    $this->the();
                    break;
                case 'update':
                    $this->update();
                    break;
                case 'del':
                    $this->del();
                    break;
                case 'addShow':
                    $this->addShow();
                    break;
                case 'add':
                    $this->add();
                    break;
//                case 'getDriver':         去除自动显示驾驶员姓名，因为车与驾驶员不绑定
//                    $this->getDriver();
//                    break;//
                case 'getCarNo':         //自动取得匹配的车牌号码
                    $this->getCarNo();
                    break;
                default:    //测试PHPExcel
                    $this->test();
                    break;
            }
        }

    }

//    去除自动显示驾驶员姓名，因为车与驾驶员不绑定
//    /**
//     * 根据输入的车牌号自动显示驾驶员姓名
//     */
//    private function getDriver(){
//
//        if (isset($_POST['carNo'])){
//            $car_driver = $this->_model->getDriverInfoBycarNo();
//
//            if( false !== $car_driver){
//                $arr['success'] = JSON_RETURN_OK;
//                $arr['msg'] = $car_driver;
//            }else{
//                $arr['success'] = JSON_RETURN_NG;
//            }
//        }else{
//            $arr['success'] = JSON_RETURN_UNKNOW;
//        }
//
//        echo json_encode($arr);
//    }

    //去除自动显示驾驶员姓名，因为车与驾驶员不绑定
    /**
     * 根据输入的车牌号自动显示驾驶员姓名
     */
    private function getCarNo(){

        if (isset($_POST['carNo'])){
            $car_driver = $this->_model->getInfoInfoBycarNo();

            if( false !== $car_driver){
                $arr['success'] = JSON_RETURN_OK;
                $arr['msg'] = $car_driver;
            }else{
                $arr['success'] = JSON_RETURN_NG;
            }
        }else{
            $arr['success'] = JSON_RETURN_UNKNOW;
        }

        echo json_encode($arr);
    }

    /**
     * 检查正确性
     * @param $data
     * @return string
     */
    private function checkCarInfo($data){

        $msg = '';

        //为空判断
        if(!ValidateModel::isEmpty($data['car_no'])){
            return $msg = '车牌号不能为空';
        }

        if( (ToolModel::getStrLen($data['car_no']) > 10 ) || (ToolModel::getStrLen($data['car_no']) < 7) ){
            return $msg = '车牌号位数必须在7到10之间';
        }

        if(!ValidateModel::isEmpty($data['car_frame'])){
            return $msg = '车架号不能为空';
        }

        if(ToolModel::getStrLen($data['car_frame']) > 18){
            return $msg = '车架号位数不能大于18位';
        }

        if(!ValidateModel::isEmpty($data['car_owner'])){
            return $msg = '车主不能为空';
        }

        if( (ToolModel::getStrLen($data['car_owner']) > 25) || (ToolModel::getStrLen($data['car_owner']) < 2) ){
            return $msg = '车主位数只能是2到25位';
        }

        if(!(ValidateModel::checkDate($data['car_insurance_expires']))){
            return $msg = '日期格式错误';
        }

        //为空判断
        if(!ValidateModel::isEmpty($data['car_insurance_name'])){
            return $msg = '保险公司名称不能为空';
        }

        if( ToolModel::getStrLen($data['car_insurance_name']) >= 25  ){
            return $msg = '保险公司名称位数必须小于25位';
        }

        return $msg;
    }

    /**
     * 显示添加画面
     */
    private function addShow(){
        //显示添加页面
        $this->display('car_add_info');
    }
    /**
     * 追加新车辆信息
     */
    private function add(){

        if($_POST['send'] == '新添加' ){

            //判断传入数据是否符合要求
            $msg = $this->checkCarInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if(false !== $this->_model->addCarInfo()){
                ToolModel::goToUrl('新增车辆信息成功','all');
            }else{
                ToolModel::goBack('新增车辆信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到新增车辆的信息','all');
        }
    }

    /**
     * Json传递ID过来删除指定车辆数据
     */
    private function del(){
        if(isset($_POST['id']) && (intval($_POST['id']) >= 0) ){
            //判断是否一行更更改
            if( 1 == $this->_model->deleteTheCarInfo(I('post.id',0))){
                $arr['success'] = JSON_RETURN_OK;
            }else{
                $arr['success'] = JSON_RETURN_NG;
            }
        }else{
            $arr['success'] = JSON_RETURN_UNKNOW;
        }
        echo json_encode($arr);
    }

    /**
     * 根据ID来获得需要编辑修改的车辆信息
     */
    private function update(){

        if(isset($_POST['id']) && (intval($_POST['id']) > 0) ){

            //判断传入数据是否符合要求
            $msg = $this->checkCarInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if( 1 == $this->_model->updateTheCarInfo()){
                ToolModel::goToUrl('修改车辆信息成功','all');
            }else{
                ToolModel::goBack('修改车辆信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到修改车辆的信息','all');
        }
    }

    /**
     * 取得传过来ID对应的车辆信息
     */
    private function the(){
        //如果有传值过来用查询传值的用户
        if(isset($_GET['id']) && '' != $_GET['id']){
            $id = I('get.id');
        }

        $data = $this->_model->getTheCarInfo($id);

        if(false !== $data){
            $this->assign('the',true);
            $this->assign('data',$data);
            $this->display('car_the_info');
        }
    }

    /**
     * 取得车辆信息并分页显示
     */
    private function all(){

        $count = $this->_model->getCarCount();

        //无数据
        if($count > 0) {

            //分页
            import('ORG.Util.Page');// 导入分页类
            $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);                //实例化分页类 传入总记录数
            $limit = $Page->firstRow . ',' . $Page->listRows;

            //取得分分页信息
            $carInfo = D('Car')->getPageCarInfo($limit);

            $show = $Page->show();// 分页显示输出

            $d1 = strtotime(date("Y-m-d"));

            for ($i=0;$i<count($carInfo);$i++){


                $d2 = strtotime($carInfo[$i]['car_insurance_expires']);

                $Days = round(($d2-$d1)/3600/24);
                if($Days <= 90){
                    $carInfo[$i]['car_insurance_expires'] = '<font color="red">'.$carInfo[$i]['car_insurance_expires'].'</font>';
                }
            }

            $this->assign('data', $carInfo); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出

            if($_GET['p'] > 1){
                $No = intval($_GET['p'] - 1)*10;
                $this->assign('no', $No);    //赋值分页输
            }

        }

        $this->display('car_info_show');

    }

}