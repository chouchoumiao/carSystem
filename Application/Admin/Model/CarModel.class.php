<?php

/**
 *  * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 车辆管理类
 *
 */
namespace Admin\Model;

    class CarModel {

        private $_model;

        public function __construct(){
            $this->_model = M('car_info');
        }

        /**
         * @return mixed
         * 取得所有车辆信息
         */
        public function getCarInfo(){

            return $this->_model->select();

        }

        /**
         * @return mixed
         * 取得所有车辆的数量
         */
        public function getCarCount(){

            return ToolModel::getIntCount($this->_model->count());

        }

        /**
         * 取得分页信息
         * @param $limit
         * @return mixed
         */
        public function getPageCarInfo($limit){

            return $this->_model->order('id')->limit($limit)->select();

        }

        /**
         * 取得指定车辆信息
         * @param $id
         * @return mixed
         */
        public function getTheCarInfo($id){
            return $this->_model->find($id);
        }

        /**
         * 更新对应的车辆信息
         */
        public function updateTheCarInfo(){
            //追加更新时间
            $_POST['edit_time'] = date('Y-m-d H:i:s', time());

            return $this->_model->save($_POST);

        }

        /**
         * 根据传入的ID进行删除该车辆信息
         * @param $id
         * @return mixed
         */
        public function deleteTheCarInfo($id){

            $where['id'] = $id;

            return $this->_model->where($where)->delete();

        }

        /**
         * 新追加车辆信息
         * @param string $data
         * @return bool|mixed|string
         */
        public function addCarInfo($data = ''){


            if('' == $data){
                //追加更新时间
                $_POST['insert_time'] = ToolModel::getNowTime();
                $_POST['edit_time'] = ToolModel::getNowTime();

                return $this->_model->add($_POST);
            }else{
                return $this->_model->addAll($data);
            }

        }

        /**
         * @return mixed
         * 取得所有货运信息
         */
        public function getCarExcelInfo(){

            return $this->_model
                ->field('car_no,car_frame,car_owner,car_insurance_expires,car_insurance_name')
                ->order('id')
                ->select();
        }

        public function getDriverInfoBycarNo(){
            $car_no = I('post.carNo','');
            $where['car_no'] = $car_no;
            return $this->_model->where($where)->getField('car_driver');

        }
    }