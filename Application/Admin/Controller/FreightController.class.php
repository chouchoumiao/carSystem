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
use Admin\Model\ToolModel;
use Admin\Model\ValidateModel;
use Admin\Model\ExcelFreightModel;

class FreightController extends Controller{

    private $_model;

    public function doAction(){

        $action = $_GET['action'];
        if( isset($action) && '' != $action ){
            $this->_model = D('Freight');

            switch($action){
                //取得所有车辆信息(分页)
                case 'all':
                    $this->all();
                    break;
                //编辑货运信息
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
                //按条件查询
                case 'freightByCase':
                    $this->freightByCase();
                    break;
                case 'freightByCaseGetInfo':
                    $this->freightByCaseGetInfo();
                    break;
                case 'freightByDateShow':
                    $this->freightByDateShow();
                    break;
                case 'freightByDateSearch':
                    $this->freightByDateSearch();
                    break;
                case 'outPutSerachFreightResult':
                    $this->outPutSerachFreightResult();
                    break;
            }
        }

    }

    /**
     * 导出查询的结果
     */
    private function outPutSerachFreightResult(){

        if(!isset($_SESSION['freightSerachData'])){
            ToolModel::goBack('导出失败，请重新点击查询后再导出');
        }else{
            ExcelFreightModel::outputExcelFreightInfoByCase('货运查询结果','');
        }

    }

    private function freightByDateSearch(){

        if(isset($_POST['searchDateInfo']) && ('查询') == I('searchDateInfo','') ){

            //第一次进来先清空session中的
            unset($_SESSION['dateCase']);
            $data = $this->_model->getInfoByDateSearch();

            if(false === $data){
                ToolModel::goBack('查询错误');
            }else{
                if(count($data) == 0){
                    ToolModel::goBackAndFlash('该组合条件查询没有结果，请确认条件是否有误');
                }else{

                    self::getDateCaseInfoWithPage();

                }
            }
        }else if(isset($_POST['exportDateInfo']) && ('导出') == I('exportDateInfo','') ){
            self::exportDataCaseData();

        }else{
            if(isset($_GET['p'])){

                self::getDateCaseInfoWithPage();
            }
        }
    }

    private function exportDataCaseData(){
        ExcelFreightModel::OutputExcelFreightDateSerachData();
    }

    private function getDateCaseInfoWithPage(){

        $data = $this->_model->getInfoByDateSearch();

        if(false !== $data){
            //分页
            import('ORG.Util.Page');// 导入分页类
            $Page = new \Org\Util\Page(count($data), PAGE_SHOW_COUNT_10);                //实例化分页类 传入总记录数
            $limit = $Page->firstRow . ',' . $Page->listRows;

            //取得分页信息
            $freighInfo = $this->_model->getPageFreightInfoByDateCase($limit);

            $show = $Page->show();// 分页显示输出

            $this->assign('data', $freighInfo); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输


            if($_GET['p'] > 1){
                $No = intval($_GET['p'] - 1)*10;
                $this->assign('no', $No);    //赋值分页输
            }

        }

        $this->display('freight_info_show');
    }

    /**
     * 显示日期条件查询画面
     */
    private function freightByDateShow(){
        $this->display('freight_date_search');
    }

    /**
     * 根据条件来取得数据
     */
    private function freightByCase(){

        $this->display('freight_by_case');

    }

    /**
     * 根据条件查询对应的值，并分页
     */
    private function freightByCaseGetInfo(){

        if(isset($_POST['searchInfo']) && (I('post.searchInfo','') == '查询') ){

            //第一次进来先清空session中的
            unset($_SESSION['case']);
            $data = $this->_model->getInfoByCase();

            if(isset($_SESSION['freightSerachData'])){
                unset($_SESSION['freightSerachData']);
            }

            if(false === $data){
                ToolModel::goBack('查询错误');
            }else{
                $count = count($data);
                if($count == 0){
                    ToolModel::goBackAndFlash('该组合条件查询没有结果，请确认条件是否有误');
                }else{

                    if (!isset($_SESSION['freightSerachData'])) {

                        //为了能导出方便，将数据存入Session中
                        for ($i = 0; $i < count($data); $i++) {
                            $_SESSION['freightSerachData'][$i]['car_date'] = $data[$i]['car_date'];
                            $_SESSION['freightSerachData'][$i]['car_no'] = $data[$i]['car_no'];
                            $_SESSION['freightSerachData'][$i]['goods_name'] = $data[$i]['goods_name'];
                            $_SESSION['freightSerachData'][$i]['loading_place'] = $data[$i]['loading_place'];
                            $_SESSION['freightSerachData'][$i]['unloading_place'] = $data[$i]['unloading_place'];
                            $_SESSION['freightSerachData'][$i]['loading_tonnage'] = $data[$i]['loading_tonnage'];
                            $_SESSION['freightSerachData'][$i]['unloading_tonnage'] = $data[$i]['unloading_tonnage'];
                            $_SESSION['freightSerachData'][$i]['ticket_number'] = $data[$i]['ticket_number'];
                            $_SESSION['freightSerachData'][$i]['amount'] = $data[$i]['amount'];
                            $_SESSION['freightSerachData'][$i]['price'] = $data[$i]['price'];
                        }
                    }

                    //分页
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
            $msg = $this->checkFreightInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if( 1 == $this->_model->updateTheFreightInfo()){
                ToolModel::goToUrl('修改货运信息成功','all');
            }else{
                ToolModel::goBack('修改货运信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到修改货运的信息','all');
        }
    }

    /**
     * 取得传过来ID对应的货运信息
     */
    private function the(){
        //如果有传值过来用查询传值的用户
        if(isset($_GET['id']) && '' != $_GET['id']){
            $id = I('get.id');
        }

        $data = $this->_model->getTheFreightInfo($id);

        if(false !== $data){
            $this->assign('the',true);
            $this->assign('data',$data);
            $this->display('freight_the_info');
        }
    }

    /**
     * Json传递ID过来删除指定货运数据
     */
    private function del(){
        if(isset($_POST['id']) && (intval($_POST['id']) >= 0) ){
            //判断是否一行更更改
            if( 1 == $this->_model->deleteTheFreightInfo(I('post.id',0))){
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

        $count = $this->_model->getFreightCount();

        //无数据
        if($count > 0) {

            //分页
            import('ORG.Util.Page');// 导入分页类
            $Page = new \Org\Util\Page($count, PAGE_SHOW_COUNT_10);                //实例化分页类 传入总记录数
            $limit = $Page->firstRow . ',' . $Page->listRows;

            //取得分分页信息
            $freighInfo = $this->_model->getPageFreightInfo($limit);

            $show = $Page->show();// 分页显示输出

            $this->assign('data', $freighInfo); //用户信息注入模板
            $this->assign('page', $show);    //赋值分页输出

            if($_GET['p'] > 1){
                $No = intval($_GET['p'] - 1)*10;
                $this->assign('no', $No);    //赋值分页输
            }


        }
        $this->display('freight_info_show');

    }

    /**
     * 数据验证
     * @param $data
     * @return string
     */
    private function checkFreightInfo(&$data){

        $msg = '';

        if(!(ValidateModel::checkDate($data['car_date']))){
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

//        if(!ValidateModel::isEmpty($data['goods_name'])){
//            return $msg = '货物名称不能为空';
//        }
//        if( ToolModel::getStrLen($data['goods_name']) > 100){
//            return $msg = '货物名称不能超过100位';
//        }
//
//        if(!ValidateModel::isEmpty($data['loading_place'])){
//            return $msg = '装货地名称不能为空';
//        }
//        if( ToolModel::getStrLen($data['loading_place']) > 100){
//            return $msg = '装货地不能超过100位';
//        }
//
//        if(!ValidateModel::isEmpty($data['unloading_place'])){
//            return $msg = '卸货地名称不能为空';
//        }
//        if( ToolModel::getStrLen($data['unloading_place']) > 100){
//            return $msg = '卸货地不能超过100位';
//        }
//
        if(!is_numeric($data['loading_tonnage']) || (!ValidateModel::isEmpty($data['loading_tonnage']))){
            //return $msg = '发货吨位为空或者不是数字';
            $data['loading_tonnage'] = '0.00';
        }

        if(!is_numeric($data['unloading_tonnage']) || (!ValidateModel::isEmpty($data['unloading_tonnage']))){
//            return $msg = '收货吨位为空或者不是数字';
            $data['unloading_tonnage'] = '0.00';
        }
//
//        if(ValidateModel::isEmpty($data['ticket_number'])){
//
//            //不为空的时候判断是不是都是都是数字
//            if(!ValidateModel::isNum($data['ticket_number'],'int')){
//                return $msg = '输入的票号必须是纯数字';
//            }
//
//            if( ToolModel::getStrLen($data['ticket_number']) > 8){
//                return $msg = '输入的票号不能大于8位';
//            }
//        }
//
        //不能使用ValidateModel::isNum进行判断会浮点型数据判断不对
        if(!is_numeric($data['amount']) || (!ValidateModel::isEmpty($data['amount']))){
//            return $msg = '金额为空或者不是数字';
            $data['amount'] = '0.00';
        }

        //追加单价修改为空时候会出错的问题
        if(!is_numeric($data['price']) || (!ValidateModel::isEmpty($data['price']))){
//            return $msg = '金额为空或者不是数字';
            $data['price'] = '0.00';
        }

        return $msg;
    }

    private function addShow(){

        $this->assign('today',date("Y-m-d") );

        //显示添加页面
        $this->display('freight_add_info');
    }
    /**
     * 输入运费数据
     */
    private function add(){
        if($_POST['send'] == '新添加' ){

            //判断传入数据是否符合要求
            $msg = $this->checkFreightInfo($_POST);
            if( '' != $msg){
                ToolModel::goBack($msg);
            }

            //判断是否一行更更改
            if(false !== $this->_model->addFreightInfo()){
                ToolModel::goToUrl('新增货运信息成功','all');
            }else{
                ToolModel::goBack('新增货运信息出错');
            }
        }else{
            ToolModel::goToUrl('未获取到新增货运的信息','all');
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

        //取得分页信息
        $freighInfo = $this->_model->getPageFreightInfoByCase($limit);

        $show = $Page->show();// 分页显示输出

        $this->assign('case', true); //是查询得到的结果，可以显示查出按钮
        $this->assign('data', $freighInfo); //用户信息注入模板
        $this->assign('page', $show);    //赋值分页输出

        if($_GET['p'] > 1){
            $No = intval($_GET['p'] - 1)*10;
            $this->assign('no', $No);    //赋值分页输
        }

        $this->display('freight_info_show');

    }

}