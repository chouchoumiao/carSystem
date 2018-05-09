<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 客户管理类
 */

namespace Admin\Controller;

use Admin\Model\ToolModel;
use Admin\Model\ValidateModel;
use Think\Controller;

class CustomerController extends Controller{

    private $_model;

    public function doAction(){

        $action = $_GET['action'];
        if( isset($action) && '' != $action ){
            $this->_model = D('Customer');

            switch($action){
                //取得所有客户信息(分页)
                case 'all':
                    $this->all();
                    break;
                //编辑客户信息
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
                default:
                    break;
            }
        }

    }


    /**
     * 检查正确性
     * @param $data
     * @return string
     */
    private function checkCustomerInfo($data){

        $msg = '';

        //为空判断
        if(!ValidateModel::isEmpty($data['customer_name'])){
            return $msg = '客户姓名不能为空';
        }


        return $msg;
    }

    /**
     * 显示添加画面
     */
    private function addShow(){
        //显示添加页面

        $this->assign('today',date("Y-m-d") );
        $this->display('customer_add_info');
    }
    /**
     * 追加新客户信息
     */
    private function add(){

        if($_POST['send'] == '新添加' ){

            //判断传入数据是否符合要求
            $msg = $this->checkCustomerInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if(false !== $this->_model->addCustomerInfo()){
                ToolModel::goToUrl('新增客户信息成功','all');
            }else{
                ToolModel::goBack('新增客户信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到新增客户的信息','all');
        }
    }

    /**
     * Json传递ID过来删除指定客户数据
     */
    private function del(){
        if(isset($_POST['id']) && (intval($_POST['id']) >= 0) ){
            //判断是否一行更更改
            if( 1 == $this->_model->deleteTheCustomerInfo(I('post.id',0))){
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
     * 根据ID来获得需要编辑修改的客户信息
     */
    private function update(){

        if(isset($_POST['id']) && (intval($_POST['id']) > 0) ){

            //判断传入数据是否符合要求
            $msg = $this->checkCustomerInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if( 1 == $this->_model->updateTheCustomerInfo()){
                ToolModel::goToUrl('修改客户信息成功','all');
            }else{
                ToolModel::goBack('修改客户信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到修改客户的信息','all');
        }
    }

    /**
     * 取得传过来ID对应的客户信息
     */
    private function the(){
        //如果有传值过来用查询传值的用户
        if(isset($_GET['id']) && '' != $_GET['id']){
            $id = I('get.id');
        }

        $data = $this->_model->getTheCustomerInfo($id);

        if(false !== $data){
            $this->assign('the',true);
            $this->assign('data',$data);
            $this->display('customer_the_info');
        }
    }

    /**
     * 取得客户信息并分页显示
     */
    private function all(){

        $count = $this->_model->getCustomerCount();

        //无数据
        if($count > 0) {

            //分页
            import('ORG.Util.Page');// 导入分页类
            $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);                //实例化分页类 传入总记录数
            $limit = $Page->firstRow . ',' . $Page->listRows;

            //取得分分页信息
            $customerInfo = D('Customer')->getPageCustomerInfo($limit);

            $show = $Page->show();// 分页显示输出


            for ($i=0;$i<count($customerInfo);$i++){

                if($customerInfo[$i]['customer_sex'] == 0){
                    $customerInfo[$i]['customer_sex'] = '男';
                }elseif ($customerInfo[$i]['customer_sex'] == 1){
                    $customerInfo[$i]['customer_sex'] = '女';
                }else{
                    $customerInfo[$i]['customer_sex'] = '未知';
                }
            }

            $this->assign('data', $customerInfo); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出

            if($_GET['p'] > 1){
                $No = intval($_GET['p'] - 1)*10;
                $this->assign('no', $No);    //赋值分页输
            }

        }

        $this->display('customer_info_show');

    }

}