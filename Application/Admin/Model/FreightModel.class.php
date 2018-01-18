<?php

/**
 *  * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 货运管理类
 *
 */
namespace Admin\Model;

    class FreightModel {

        private $_model;

        public function __construct(){
            $this->_model = M('freight');
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
                ->field('car_date,car_no,goods_name,loading_place,unloading_place,loading_tonnage,unloading_tonnage,ticket_number,amount')
                ->order('car_date')
                ->select();
        }

        /**
         * @return mixed
         * 取得所有货运的数量
         */
        public function getFreightCount(){

            return ToolModel::getIntCount($this->_model->count());

        }

        /**
         * 取得分页信息
         * @param $limit
         * @return mixed
         */
        public function getPageFreightInfo($limit){

            return $this->_model->order('id')->limit($limit)->select();

        }

        /**
         * 取得指定货运信息
         * @param $id
         * @return mixed
         */
        public function getTheFreightInfo($id){
            return $this->_model->find($id);
        }

        /**
         * 更新对应的货运信息
         */
        public function updateTheFreightInfo(){

            $_POST['car_month'] = substr($_POST['car_date'],0,7);

            //追加更新时间
            $_POST['edit_time'] = date('Y-m-d H:i:s', time());

            return $this->_model->save($_POST);

        }

        /**
         * 根据传入的ID进行删除该货运信息
         * @param $id
         * @return mixed
         */
        public function deleteTheFreightInfo($id){

            $where['id'] = $id;

            return $this->_model->where($where)->delete();

        }

        /**
         * 新追加货运信息
         * @return mixed
         */
        public function addFreightInfo(){

            //追加更新时间
            $_POST['car_month'] = substr($_POST['car_date'],0,7);

            $_POST['insert_time'] = date('Y-m-d H:i:s', time());
            $_POST['edit_time'] = date('Y-m-d H:i:s', time());

            return $this->_model->add($_POST);
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
            $where['car_month'] = $month;

            return $this->_model
                ->where($where)
                ->field('car_date,car_no,goods_name,loading_place,unloading_place,loading_tonnage,unloading_tonnage,ticket_number,amount')
                ->order('car_date')
                ->select();
        }
    }
