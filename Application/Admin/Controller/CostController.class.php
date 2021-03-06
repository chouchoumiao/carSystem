<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/16
 * Time: 20:49
 * 货运类管理控制器
 */

namespace Admin\Controller;

use Admin\Model\ExcelCostModel;
use Think\Controller;
use Admin\Model\ToolModel;
use Admin\Model\ValidateModel;

class CostController extends Controller{

    private $_model;

    public function doAction(){

        $action = $_GET['action'];
        if( isset($action) && '' != $action ){
            $this->_model = D('Cost');

            switch($action){
                //取得所有车辆信息(分页)
                case 'all':
                    $this->all();
                    break;
                //编辑费用信息
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
                //固定费用输入
                case 'fixedShow':
                    $this->fixedShow();
                    break;
                case 'addFixed':
                    $this->addFixed();
                    break;

                //按条件查询
                case 'costByCase':
                    $this->costByCase();
                    break;
                case 'costByCaseGetInfo':
                    $this->costByCaseGetInfo();
                    break;
                //导出查询的结果
                case 'outPutSerachCostResult':
                    $this->outPutSerachCostResult();
                    break;
            }
        }

    }

    /**
     * 导出查询的结果
     */
    private function outPutSerachCostResult(){

        if(!isset($_SESSION['costSerachData'])){
            ToolModel::goBack('导出失败，请重新点击查询后再导出');
        }else{
            ExcelCostModel::outputExcelCostInfo('费用查询结果','');
        }

    }

    /**
     * 根据条件来取得数据
     */
    private function costByCase(){

        $this->display('cost_by_case');

    }

    /**
     * 根据条件查询对应的值，并分页
     */
    private function costByCaseGetInfo(){

        if(isset($_POST['searchInfo']) && (I('post.searchInfo','') == '查询') ){

            //第一次进来先清空session中的
            if(isset($_SESSION['costCase'])){

                unset($_SESSION['costCase']);
            }

            if(isset($_SESSION['costSerachData'])){

                unset($_SESSION['costSerachData']);
            }
            $data = $this->_model->getInfoByCase();

            if(false === $data){
                ToolModel::goBack('查询错误');
            }else{
                $count = count($data);
                if($count == 0){
                    ToolModel::goBackAndFlash('该组合条件查询没有结果，请确认条件是否有误');
                }else{

                    if (!isset($_SESSION['costSerachData'])) {

                        //为了能导出方便，将数据存入Session中
                        for ($i = 0; $i < count($data); $i++) {
                            $_SESSION['costSerachData'][$i]['car_no'] = $data[$i]['car_no'];
                            $_SESSION['costSerachData'][$i]['cost_date'] = $data[$i]['cost_date'];
                            $_SESSION['costSerachData'][$i]['car_driver'] = $data[$i]['car_driver'];
                            $_SESSION['costSerachData'][$i]['cost_name'] = $data[$i]['cost_name'];
                            $_SESSION['costSerachData'][$i]['cost_amount'] = $data[$i]['cost_amount'];
                            $_SESSION['costSerachData'][$i]['cost_note'] = $data[$i]['cost_note'];
                        }
                    }

                    self::doPageDate($count);
                }
            }
        }else{

            if(isset($_GET['p'])){

                $data = $this->_model->getInfoByCase();
                $count = count($data);

                self::doPageDate($count);
            }
        }

    }

    /**
     * 根据ID来获得需要编辑修改的车辆信息
     */
    private function update(){

        if(isset($_POST['id']) && (intval($_POST['id']) > 0) ){

            //判断传入数据是否符合要求
            $msg = $this->checkCostInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if( 1 == $this->_model->updateTheCostInfo()){
                ToolModel::goToUrl('修改费用信息成功','all');
            }else{
                ToolModel::goBack('修改费用信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到修改费用的信息','all');
        }
    }

    /**
     * 取得传过来ID对应的费用信息
     */
    private function the(){
        //如果有传值过来用查询传值的用户
        if(isset($_GET['id']) && '' != $_GET['id']){
            $id = I('get.id');
        }

        $data = $this->_model->getTheCostInfo($id);

        if(false !== $data){

            switch ($data['cost_name']){
                case '冲油':
                case '工资':
                case '轮胎':
                case '补胎':
                $this->assign('fixed',true);
                $this->assign('fixedName',$data['cost_name']);
            }

            $this->assign('the',true);
            $this->assign('data',$data);
            $this->display('cost_the_info');
        }
    }

    /**
     * Json传递ID过来删除指定费用数据
     */
    private function del(){
        if(isset($_POST['id']) && (intval($_POST['id']) >= 0) ){
            //判断是否一行更更改
            if( 1 == $this->_model->deleteTheCostInfo(I('post.id',0))){
                $arr['success'] = JSON_RETURN_OK;
            }else{
                $arr['success'] = JSON_RETURN_NG;
            }
        }else{
            $arr['success'] = JSON_RETURN_UNKNOW;
        }
        echo json_encode($arr);
    }


    private function all(){

        $count = $this->_model->getCostCount();

        //无数据
        if($count > 0) {

            //分页
            import('ORG.Util.Page');// 导入分页类
            $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);                //实例化分页类 传入总记录数
            $limit = $Page->firstRow . ',' . $Page->listRows;

            //取得分分页信息
            $costInfo = $this->_model->getPageCostInfo($limit);

            $show = $Page->show();// 分页显示输出

            $this->assign('data', $costInfo); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出

            if($_GET['p'] > 1){
                $No = intval($_GET['p'] - 1)*10;
                $this->assign('no', $No);    //赋值分页输
            }


        }
        $this->display('cost_info_show');

    }

    /**
     * 数据验证
     * @param $data
     * @return string
     */
    private function checkCostInfo(&$data){

        $msg = '';

        if(!(ValidateModel::checkDate($data['cost_date']))){
            return $msg = '日期格式错误';
        }

        //为空判断
        if(!ValidateModel::isEmpty($data['car_no'])){
            return $msg = '车牌号不能为空';
        }
        if( (ToolModel::getStrLen($data['car_no']) > 10 ) || (ToolModel::getStrLen($data['car_no']) < 7) ){
            return $msg = '车牌号位数必须在7到10之间';
        }

        if(!ValidateModel::isEmpty($data['car_driver'])){
            return $msg = '驾驶员姓名不能为空';
        }
        if( (ToolModel::getStrLen($data['car_driver']) > 5) || (ToolModel::getStrLen($data['car_driver']) < 2) ){
            return $msg = '驾驶员姓名位数只能是2到5位';
        }

        //如果不是数字或者金额则改为0.00
        if(!is_numeric($data['cost_amount']) || (!ValidateModel::isEmpty($data['cost_amount']))){
            $data['cost_amount'] = '0.00';
        }

//        if(!ValidateModel::isEmpty($data['cost_name'])){
//            return $msg = '报销内容不能为空';
//        }
//        if( ToolModel::getStrLen($data['cost_name']) > 100){
//            return $msg = '报销内容不能超过100位';
//        }
//
//        //不能使用ValidateModel::isNum进行判断会浮点型数据判断不对
//        if(!is_numeric($data['cost_amount']) || (!ValidateModel::isEmpty($data['cost_amount']))){
//            return $msg = '报销金额为空或者不是数字';
//        }

        return $msg;
    }

    /**
     * 显示新增画面
     */
    private function addShow(){
        //显示添加页面
        $this->assign('today',date("Y-m-d") );
        $this->display('cost_add_info');
    }

    /**
     * 显示固定费用画面
     */
    private function fixedShow(){
        //显示添加页面
        $this->assign('today',date("Y-m-d") );
        $this->display('cost_fexedAdd_info');
    }

    /**
     * 输入固定运费数据
     */
    private function addFixed(){

        if($_POST['send'] == '新添加' ){

            //判断传入数据是否符合要求
            $msg = $this->checkCostInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if(false !== $this->_model->addCostInfo()){
                ToolModel::goToUrl('新增固定费用信息成功','all');
            }else{
                ToolModel::goBack('新增固定费用信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到新增固定费用的信息','all');
        }

    }

    /**
     * 输入运费数据
     */
    private function add(){
        if($_POST['send'] == '新添加' ){

            //判断传入数据是否符合要求
            $msg = $this->checkCostInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if(false !== $this->_model->addCostInfo()){
                ToolModel::goToUrl('新增费用信息成功','all');
            }else{
                ToolModel::goBack('新增费用信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到新增费用的信息','all');
        }

    }

    /**
     * @param $count
     */
    private function doPageDate($count){

        //分页
        import('ORG.Util.Page');// 导入分页类
        $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);                //实例化分页类 传入总记录数
        $limit = $Page->firstRow . ',' . $Page->listRows;

        //取得分分页信息
        $costInfo = $this->_model->getPageCostInfoByCase($limit);

        $show = $Page->show();// 分页显示输出

        $this->assign('case', true); //是查询得到的结果，可以显示查出按钮
        $this->assign('data', $costInfo); //用户信息注入模板
        $this->assign('page', $show);    //赋值分页输出

        if($_GET['p'] > 1){
            $No = intval($_GET['p'] - 1)*10;
            $this->assign('no', $No);    //赋值分页输
        }

        $this->display('cost_info_show');

    }

}