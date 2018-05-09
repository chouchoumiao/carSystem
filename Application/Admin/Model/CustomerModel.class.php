<?php

/**
 *  * Created by wujiayu.
 * User: Administrator
 * Date: 2018/01/15
 * Time: 20:49
 * 客户管理类
 *
 */
namespace Admin\Model;

    class CustomerModel {

        private $_model;

        public function __construct(){
            $this->_model = M('customer');
        }

        /**
         * @return mixed
         * 取得所有客户信息
         */
        public function getCustomerInfo(){

            return $this->_model->select();

        }

        /**
         * @return mixed
         * 取得所有客户的数量
         */
        public function getCustomerCount(){

            return ToolModel::getIntCount($this->_model->count());

        }

        /**
         * 取得分页信息
         * @param $limit
         * @return mixed
         */
        public function getPageCustomerInfo($limit){

            return $this->_model->order('id')->limit($limit)->select();

        }

        /**
         * 取得指定客户信息
         * @param $id
         * @return mixed
         */
        public function getTheCustomerInfo($id){
            return $this->_model->find($id);
        }

        /**
         * 更新对应的客户信息
         */
        public function updateTheCustomerInfo(){
//            unset($_SESSION['customer_all_name']);


            //追加更新时间
            $_POST['edit_time'] = date('Y-m-d H:i:s', time());

            return $this->_model->save($_POST);

        }

        /**
         * 根据传入的ID进行删除该客户信息
         * @param $id
         * @return mixed
         */
        public function deleteTheCustomerInfo($id){

//            unset($_SESSION['customer_all_name']);

            $where['id'] = $id;

            return $this->_model->where($where)->delete();

        }

        /**
         * 新追加客户信息
         * @param string $data
         * @return bool|mixed|string
         */
        public function addCustomerInfo($data = ''){

//            unset($_SESSION['customer_all_name']);

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
        public function getCustomerExcelInfo(){

            return $this->_model
                ->field('customer_name,customer_sex,customer_address,customer_tel')
                ->order('id')
                ->select();
        }

    }