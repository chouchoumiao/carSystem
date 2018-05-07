<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 驾驶员管理类
 */

namespace Admin\Controller;

use Admin\Model\ToolModel;
use Admin\Model\ValidateModel;
use Think\Controller;

class DriverController extends Controller{

    private $_model;

    public function doAction(){

        $action = $_GET['action'];
        if( isset($action) && '' != $action ){
            $this->_model = D('Driver');

            switch($action){
                //取得所有驾驶员信息(分页)
                case 'all':
                    $this->all();
                    break;
                //编辑驾驶员信息
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
                case 'getDriverName':
                    $this->getDriverName();
                    break;
                default:
                    break;
            }
        }

    }

    /**
     * 根据输入的驾驶员自动显示驾驶员姓名
     */
    private function getDriverName(){

        if (isset($_POST['driverName'])){
            $car_driver = $this->_model->getDriverInfoByDriverName();

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
    private function checkDriverInfo($data){

        $msg = '';

        //为空判断
        if(!ValidateModel::isEmpty($data['driver_name'])){
            return $msg = '驾驶员姓名不能为空';
        }


        return $msg;
    }

    /**
     * 显示添加画面
     */
    private function addShow(){
        //显示添加页面

        $this->assign('today',date("Y-m-d") );
        $this->display('driver_add_info');
    }
    /**
     * 追加新驾驶员信息
     */
    private function add(){

        if($_POST['send'] == '新添加' ){

            //判断传入数据是否符合要求
            $msg = $this->checkDriverInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if(false !== $this->_model->addDriverInfo()){
                ToolModel::goToUrl('新增驾驶员信息成功','all');
            }else{
                ToolModel::goBack('新增驾驶员信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到新增驾驶员的信息','all');
        }
    }

    /**
     * Json传递ID过来删除指定驾驶员数据
     */
    private function del(){
        if(isset($_POST['id']) && (intval($_POST['id']) >= 0) ){
            //判断是否一行更更改
            if( 1 == $this->_model->deleteTheDriverInfo(I('post.id',0))){
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
     * 根据ID来获得需要编辑修改的驾驶员信息
     */
    private function update(){

        if(isset($_POST['id']) && (intval($_POST['id']) > 0) ){

            //判断传入数据是否符合要求
            $msg = $this->checkDriverInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if( 1 == $this->_model->updateTheDriverInfo()){
                ToolModel::goToUrl('修改驾驶员信息成功','all');
            }else{
                ToolModel::goBack('修改驾驶员信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到修改驾驶员的信息','all');
        }
    }

    /**
     * 取得传过来ID对应的驾驶员信息
     */
    private function the(){
        //如果有传值过来用查询传值的用户
        if(isset($_GET['id']) && '' != $_GET['id']){
            $id = I('get.id');
        }

        $data = $this->_model->getTheDriverInfo($id);

        if(false !== $data){
            $this->assign('the',true);
            $this->assign('data',$data);
            $this->display('driver_the_info');
        }
    }

    /**
     * 取得驾驶员信息并分页显示
     */
    private function all(){

        $count = $this->_model->getDriverCount();

        //无数据
        if($count > 0) {

            //分页
            import('ORG.Util.Page');// 导入分页类
            $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);                //实例化分页类 传入总记录数
            $limit = $Page->firstRow . ',' . $Page->listRows;

            //取得分分页信息
            $driverInfo = D('Driver')->getPageDriverInfo($limit);

            $show = $Page->show();// 分页显示输出


            for ($i=0;$i<count($driverInfo);$i++){

                if($driverInfo[$i]['driver_is_active'] == 1){
                    $driverInfo[$i]['driver_is_active'] = '在职';
                }elseif ($driverInfo[$i]['driver_is_active'] == 0){
                    $driverInfo[$i]['driver_is_active'] = '离职';
                }else{
                    $driverInfo[$i]['driver_is_active'] = '未知';
                }

                //离职人员显示为红色
                if($driverInfo[$i]['driver_leave_date'] != '9999-12-31'){
                    $driverInfo[$i]['driver_name'] = '<font color="red">'.$driverInfo[$i]['driver_name'].'</font>';
                    $driverInfo[$i]['driver_from_date'] = '<font color="red">'.$driverInfo[$i]['driver_from_date'].'</font>';
                    $driverInfo[$i]['driver_is_active'] = '<font color="red">'.$driverInfo[$i]['driver_is_active'].'</font>';
                    $driverInfo[$i]['driver_leave_date'] = '<font color="red">'.$driverInfo[$i]['driver_leave_date'].'</font>';
                    $driverInfo[$i]['insert_time'] = '<font color="red">'.$driverInfo[$i]['insert_time'].'</font>';
                    $driverInfo[$i]['edit_time'] = '<font color="red">'.$driverInfo[$i]['edit_time'].'</font>';
                }
            }



            $this->assign('data', $driverInfo); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出

            if($_GET['p'] > 1){
                $No = intval($_GET['p'] - 1)*10;
                $this->assign('no', $No);    //赋值分页输
            }

        }

        $this->display('driver_info_show');

    }

}