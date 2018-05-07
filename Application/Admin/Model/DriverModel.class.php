<?php

/**
 *  * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 驾驶员管理类
 *
 */
namespace Admin\Model;

    class DriverModel {

        private $_model;

        public function __construct(){
            $this->_model = M('driver');
        }

        /**
         * @return mixed
         * 取得所有驾驶员信息
         */
        public function getDriverInfo(){

            return $this->_model->select();

        }

        /**
         * @return mixed
         * 取得所有驾驶员的数量
         */
        public function getDriverCount(){

            return ToolModel::getIntCount($this->_model->count());

        }

        /**
         * 取得分页信息
         * @param $limit
         * @return mixed
         */
        public function getPageDriverInfo($limit){

            return $this->_model->order('id')->limit($limit)->select();

        }

        /**
         * 取得指定驾驶员信息
         * @param $id
         * @return mixed
         */
        public function getTheDriverInfo($id){
            return $this->_model->find($id);
        }

        /**
         * 更新对应的驾驶员信息
         */
        public function updateTheDriverInfo(){
//            unset($_SESSION['driver_all_name']);

            if($_POST['driver_is_active'] == '1'){
                $_POST['driver_leave_date'] = '9999-12-31';
            }


            //追加更新时间
            $_POST['edit_time'] = date('Y-m-d H:i:s', time());

            return $this->_model->save($_POST);

        }

        /**
         * 根据传入的ID进行删除该驾驶员信息
         * @param $id
         * @return mixed
         */
        public function deleteTheDriverInfo($id){

//            unset($_SESSION['driver_all_name']);

            $where['id'] = $id;

            return $this->_model->where($where)->delete();

        }

        /**
         * 新追加驾驶员信息
         * @param string $data
         * @return bool|mixed|string
         */
        public function addDriverInfo($data = ''){

//            unset($_SESSION['driver_all_name']);

            if('' == $data){

                if($_POST['driver_is_active'] == '1'){
                    $_POST['driver_leave_date'] = '9999-12-31';
                }

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
        public function getDriverExcelInfo(){

            return $this->_model
                ->field('driver_name,driver_from_time,driver_is_active,driver_leave_time')
                ->order('id')
                ->select();
        }

        //根据输入的驾驶员姓名实时显示匹配的数据并显示在网页下拉框中
        public function getDriverInfoByDriverName(){

//            if(!isset($_SESSION['driver_all_name'])){
                $driverName = I('post.driverName','');
                $where['driver_name'] = array('like','%'.$driverName.'%');
                $rst = $this->_model->where($where)->getField('driver_name',true);
//                $_SESSION['driver_all_name'] = $rst;
                return $rst;
//            }else{
//                return $_SESSION['driver_all_name'];
//            }

        }
    }