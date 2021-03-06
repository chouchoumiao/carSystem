<?php

/**
 *  * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 费用管理类
 *
 */
namespace Admin\Model;

    class CostModel {

        private $_model;

        public function __construct(){
            $this->_model = M('cost');
        }


        /**
         * wujiayu
         * 根据传入的月份取得每月累计的费用分类总金额
         * @return mixed
         */
        public function getIncomeCostByName(&$data,$costName){

            for ($i=0;$i<count($data);$i++){
                $where['cost_month'] = $data[$i]['car_month'];
                $where['car_no'] = $data[$i]['car_no'];
                $where['cost_name'] = $costName;

                $rst = $this->_model
                    ->where($where)
                    ->field('sum(cost_amount) as amount' )
                    ->select();

                if($costName == '油'){
                    $data[$i]['cost_name1'] = $rst[0]['amount'];
                }elseif ($costName == '过路费'){
                    $data[$i]['cost_name2'] = $rst[0]['amount'];
                }elseif ($costName == '其它'){
                    $data[$i]['cost_name3'] = $rst[0]['amount'];
                }
            }
            return $data;
        }



        /**
         * @return mixed
         * 取得所有货运信息
         */
        public function getFreightInfo(){

            return $this->_model->select();

        }

        /**
         * @return mixed
         * 取得所有货运信息
         */
        public function getFreightExcelInfo(){

            return $this->_model
                ->field('car_date,car_no,goods_name,loading_place,unloading_place,loading_tonnage,unloading_tonnage,ticket_number,price,amount')
                ->order('car_date')
                ->select();
        }

        /**
         * @return mixed
         * 取得所有费用的数量
         */
        public function getCostCount(){

            return ToolModel::getIntCount($this->_model->count());

        }

        /**
         * 取得分页信息
         * @param $limit
         * @return mixed
         */
        public function getPageCostInfo($limit){

            return $this->_model->order('id')->limit($limit)->select();

        }

        /**
         * 取得指定费用信息
         * @param $id
         * @return mixed
         */
        public function getTheCostInfo($id){
            return $this->_model->find($id);
        }

        /**
         * 更新对应的费用信息
         */
        public function updateTheCostInfo(){

            $_POST['cost_month'] = substr($_POST['cost_date'],0,7); //修正Bug

            //追加更新时间
            $_POST['edit_time'] = date('Y-m-d H:i:s', time());

            return $this->_model->save($_POST);

        }

        /**
         * 根据传入的ID进行删除该货运信息
         * @param $id
         * @return mixed
         */
        public function deleteTheCostInfo($id){

            $where['id'] = $id;

            return $this->_model->where($where)->delete();

        }

        /**
         * 新追费用运信息
         * @param string $data
         * @return mixed
         */
        public function addCostInfo($data = ''){

            if('' == $data){
                $addData['car_no'] = $_POST['car_no'];
                $addData['cost_date'] = $_POST['cost_date'];
                $addData['cost_month'] = substr($_POST['cost_date'],0,7);
                $addData['car_driver'] = $_POST['car_driver'];
                $addData['cost_note'] = $_POST['cost_note'];
                $addData['insert_time'] = ToolModel::getNowTime();
                $addData['edit_time'] = ToolModel::getNowTime();

                if(is_array($_POST['cost_name'])){
                    for ($i=0;$i<count($_POST['cost_name']);$i++){
                        $addData['cost_name'] = $_POST['cost_name'][$i];
                        $addData['cost_amount'] = $_POST['cost_amount'][$i];
                        $this->_model->add($addData);
                    }
                }

            }else{
                return $this->_model->addAll($data);
            }



        }

        /**
         * 取得不重复的月份，并升序
         * @return mixed
         */
        public function getNorepeatMonth(){
            return $this->_model->distinct(true)->order('car_month')->field('car_month')->select();
        }

        /**
         * 根据传入的月份取得对应的数据
         * @param $month
         * @return mixed
         */
        public function getInfoByMonth($month){

            if('空' == $month){
                $where['car_month'] = '';
            }else{
                $where['car_month'] = $month;
            }

            return $this->_model
                ->where($where)
                ->field('car_date,car_no,goods_name,loading_place,unloading_place,loading_tonnage,unloading_tonnage,ticket_number,price,amount')
                ->order('car_date')
                ->select();
        }

        /**
         * 根据选择的条件进行查询
         */
        public function getInfoByCase(){

            $where = self::getCase();

            return $this->_model->where($where)->select();

        }

        /**
         * 按条件取得分页信息
         * @param $limit
         * @return mixed
         */
        public function getPageCostInfoByCase($limit){

            $where = self::getCase();
            return $this->_model->order('id')->limit($limit)->where($where)->select();

        }

        private function getCase(){

            $where = [];

            if(isset($_SESSION['costCase']) && (!isset($_POST['searchInfo']))){

                $where = $_SESSION['costCase'];

            }else {

                if (I('post.cost_date', '') != '') {
                    $where['cost_date'] = I('post.cost_date', '');
                }
                if (I('post.cost_month', '') != '') {
                    $where['cost_month'] = I('post.cost_month', '');
                }
                if (I('post.car_no', '') != '') {
                    $where['car_no'] = I('post.car_no', '');
                }

                if (I('post.car_driver', '') != '') {
                    $where['car_driver'] = I('post.car_driver', '');
                }

                if (I('post.cost_name', '') != '') {
                    $where['cost_name'] = I('post.cost_name', '');
                }

                $_SESSION['costCase'] = $where;
            }
            return $where;
        }

        private function getDateCase(){

            if(isset($_SESSION['costDateCase']) && (!isset($_POST['searchInfo']))){

                $where = $_SESSION['costDateCase'];

            }else {

                $startDate = I('post.car_start_date', '');
                $endDate = I('post.car_end_date', '');

                if ( ($startDate != '') && ($endDate != '') ) {
                    $where = 'car_date BETWEEN \''.$startDate. '\' AND \''.$endDate.'\'';
                }else{
                    if(($startDate == '')){
                        $where = 'car_date <= \''.$endDate.'\'';
                    }else{
                        $where = 'car_date >= \''.$startDate.'\'';
                    }
                }

                $_SESSION['costDateCase'] = $where;
            }
            return $where;
        }

        /**
         * 按开始与结束日期条件取得所有信息
         * @return string
         */
        public function getInfoByDateSearch(){

            $where = self::getDateCase();
            return $this->_model->where($where)->select();
        }

        /**
         * 按开始与结束日期条件取得所有信息
         * @return string
         */
        public function getInfoByDateSearchForExport(){

            $where = self::getDateCase();

            return $this->_model
                ->where($where)
                ->field('car_date,car_no,goods_name,loading_place,unloading_place,loading_tonnage,unloading_tonnage,ticket_number,price,amount')
                ->order('car_date')
                ->select();
        }


        /**
         * 按开始与结束日期条件取得分页信息
         * @param $limit
         * @return mixed
         */
        public function getPageFreightInfoByDateCase($limit){

            $where = self::getDateCase();
            return $this->_model->order('id')->limit($limit)->where($where)->select();

        }
    }
