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
    }


    /**
     * -- phpMyAdmin SQL Dump
    -- version 4.4.1.1
    -- http://www.phpmyadmin.net
    --
    -- Host: localhost:3306
    -- Generation Time: 2018-01-15 23:53:13
    -- 服务器版本： 5.5.42
    -- PHP Version: 5.6.7

    SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
    SET time_zone = "+00:00";

    --
    -- Database: `carSystem`
    --

    -- --------------------------------------------------------

    --
    -- 表的结构 `ccm_car_info`
    --

    CREATE TABLE `ccm_car_info` (
    `id` int(8) unsigned NOT NULL,
    `car_no` char(20) NOT NULL,
    `car_driver1` char(10) NOT NULL,
    `car_driver2` char(10) NOT NULL,
    `car_driver3` char(10) NOT NULL,
    `car_insurance_expires` date NOT NULL,
    `insert_time` datetime NOT NULL,
    `edit_time` datetime NOT NULL
    ) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

    --
    -- 转存表中的数据 `ccm_car_info`
    --

    INSERT INTO `ccm_car_info` (`id`, `car_no`, `car_driver1`, `car_driver2`, `car_driver3`, `car_insurance_expires`, `insert_time`, `edit_time`) VALUES
    (12, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (13, '11', '11', '11', '11', '2018-01-02', '2018-01-09 00:00:00', '2018-01-18 00:00:00'),
    (14, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (15, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (16, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (17, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (21, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (22, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (23, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (24, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (25, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (26, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (27, '11', '11', '11', '11', '2018-01-02', '2018-01-09 00:00:00', '2018-01-18 00:00:00'),
    (28, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (29, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (30, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00'),
    (31, '22', '22', '22', '22', '2018-01-18', '2018-01-23 00:00:00', '2018-01-26 00:00:00');

    -- --------------------------------------------------------

    --
    -- 表的结构 `ccm_login`
    --

    CREATE TABLE `ccm_login` (
    `id` int(8) unsigned NOT NULL,
    `name` int(11) NOT NULL,
    `pwd` int(11) NOT NULL,
    `sex` tinyint(1) NOT NULL,
    `authority` tinyint(1) NOT NULL COMMENT '权限',
    `identity` tinyint(1) NOT NULL COMMENT '身份',
    `insert_time` datetime NOT NULL COMMENT '新增时间',
    `edit_time` datetime NOT NULL COMMENT '修改时间'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    -- --------------------------------------------------------

    --
    -- 表的结构 `ccm_m_user`
    --

    CREATE TABLE `ccm_m_user` (
    `id` int(11) NOT NULL,
    `login_name` varchar(20) NOT NULL COMMENT '登录用户名',
    `username` varchar(30) NOT NULL COMMENT '用户名',
    `autopass` varchar(20) NOT NULL,
    `password` varchar(32) NOT NULL COMMENT '密码',
    `img` varchar(30) NOT NULL,
    `email` varchar(30) NOT NULL COMMENT '邮箱',
    `token` varchar(50) NOT NULL COMMENT '帐号激活码',
    `token_exptime` int(10) NOT NULL COMMENT '激活码有效期',
    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态,-1:以注册等待管理员激活 0-未激活,1-已激活',
    `regtime` int(10) NOT NULL COMMENT '注册时间'
    ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

    --
    -- 转存表中的数据 `ccm_m_user`
    --

    INSERT INTO `ccm_m_user` (`id`, `login_name`, `username`, `autopass`, `password`, `img`, `email`, `token`, `token_exptime`, `status`, `regtime`) VALUES
    (1, 'wujiayu', '吴佳裕', '111111', '96e79218965eb72c92a549dd5a330112', '93_5795cc64e1370.jpg', 'wujiayuwujiayuwujiayu@126.com', 'c3370400a5e77c660bbc37023f95a037', 1468387714, 1, 1467782914),
    (2, 'wuxuefeng', '吴雪峰', '111111', '96e79218965eb72c92a549dd5a330112', 'default.jpg', 'sss@126.com', 'f1e7fe47b093a99fd9a5ad7b87bd7cd3', 1492929654, 1, 1492324854);

    -- --------------------------------------------------------

    --
    -- 表的结构 `ccm_user_detail_info`
    --

    CREATE TABLE `ccm_user_detail_info` (
    `id` int(11) NOT NULL,
    `uid` int(11) NOT NULL COMMENT '用户ID',
    `udi_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态,0-女,1-男',
    `udi_tel` varchar(11) NOT NULL DEFAULT '13000000000' COMMENT '手机',
    `udi_area` varchar(80) NOT NULL,
    `udi_address` varchar(200) NOT NULL COMMENT '地址',
    `udi_dep_id` varchar(20) NOT NULL COMMENT '所属部门id',
    `udi_auto_id` int(4) NOT NULL COMMENT '权限id',
    `udi_description` text NOT NULL COMMENT '个人描述',
    `udi_workplace` text NOT NULL COMMENT '//工作地点',
    `udi_update_time` int(10) NOT NULL COMMENT '更新时间'
    ) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

    --
    -- 转存表中的数据 `ccm_user_detail_info`
    --

    INSERT INTO `ccm_user_detail_info` (`id`, `uid`, `udi_sex`, `udi_tel`, `udi_area`, `udi_address`, `udi_dep_id`, `udi_auto_id`, `udi_description`, `udi_workplace`, `udi_update_time`) VALUES
    (1, 1, 1, '', '', '', '["1","2","3","4"]', 88, '333333', '', 1467782914),
    (2, 2, 0, '', '', '', '["1"]', 2, '', '', 1469178897);

    --
    -- Indexes for dumped tables
    --

    --
    -- Indexes for table `ccm_car_info`
    --
    ALTER TABLE `ccm_car_info`
    ADD PRIMARY KEY (`id`);

    --
    -- Indexes for table `ccm_login`
    --
    ALTER TABLE `ccm_login`
    ADD PRIMARY KEY (`id`);

    --
    -- Indexes for table `ccm_m_user`
    --
    ALTER TABLE `ccm_m_user`
    ADD PRIMARY KEY (`id`);

    --
    -- Indexes for table `ccm_user_detail_info`
    --
    ALTER TABLE `ccm_user_detail_info`
    ADD PRIMARY KEY (`id`);

    --
    -- AUTO_INCREMENT for dumped tables
    --

    --
    -- AUTO_INCREMENT for table `ccm_car_info`
    --
    ALTER TABLE `ccm_car_info`
    MODIFY `id` int(8) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=34;
    --
    -- AUTO_INCREMENT for table `ccm_login`
    --
    ALTER TABLE `ccm_login`
    MODIFY `id` int(8) unsigned NOT NULL AUTO_INCREMENT;
    --
    -- AUTO_INCREMENT for table `ccm_m_user`
    --
    ALTER TABLE `ccm_m_user`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
    --
    -- AUTO_INCREMENT for table `ccm_user_detail_info`
    --
    ALTER TABLE `ccm_user_detail_info`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
     */