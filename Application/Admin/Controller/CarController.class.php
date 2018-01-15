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
            }
        }

    }

    /**
     * Json传递ID过来删除指定车辆数据
     */
    public function del(){
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

            $this->assign('data', $carInfo); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出

            $this->display('car_info_show');

        }

    }

}