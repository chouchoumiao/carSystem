<?php

/**
 * 验证方法类
 */
namespace Admin\Model;

    class ToolModel {

        static function getIntCount($count){

            if( false !== $count){
                $count =  intval($count);
            }

            return $count;
        }

        /**
         * 解决中文多字符问题，改方式将中文认为一个字符
         * @param $str
         * @return int
         */
        static function getStrLen($str){
            preg_match_all('/./us', $str, $match);
            return count($match[0]);
        }

        /**
         * 返回从0开始到指定位数的字符串
         * @param $str
         * @param $len
         * @return string
         * 中文截取
         */
        static function getSubString($str,$len){

            return mb_substr($str,0,$len,'utf-8').'...';
        }

        /**
         * 根据传入的字符串，截取图片地址后返回数组
         * @param $str
         * @return mixed
         */
        static function getImgPath($str){

            $newStr =  str_replace("\"","'",$str);

            $preg = '/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg]))[\'|\"].*?[\/]?>/i';
            preg_match_all($preg, $newStr, $imgArr);
            return $imgArr[1];
        }

        /**
         * 错误返回
         * @param $msg
         */
		static function goBack($msg){

            echo "<script>alert('$msg');history.back();</script>";

            exit;
        }

        /**
         * 错误返回
         * @param $msg
         */
        static function goReload($msg){
            echo "<script>alert('$msg');location.reload()</script>";
            exit;
        }

        /**
         * 错误关闭
         * @param $msg
         */
        static function goClose($msg){
            echo "<script>alert('$msg');close()</script>";
            exit;
        }

        /**
         * 错误跳转
         * @param $msg
         * @param $url
         */
        static function goToUrl($msg,$url){
            echo "<script>alert('$msg');location='$url'</script>";
            exit;
        }

        /**
         * 删除指定文件
         * @param $img 绝对路径文件
         * @return bool
         */
        static function delImg($img){
            if(file_exists($img)){

                if(unlink($img)){
                    return 1;
                }else{
                    return '删除失败';
                }
            }
            return '文件不存在';

        }

        /**
         * 将时间戳转化为正常时间格式
         * @param $data
         * @return bool|string
         */
        static function formartTime($data){
            return date('Y-m-d H:i:s', $data);
        }

        /**
         * 简单判定是否为二维数组
         * @param $arr
         * @return bool
         */
        static function isTwoArray($arr){
            return (is_array($arr[0])) ? true : false;
        }

        /**
         * 更新session
         */
        static function setNowUserBaseSession(){
            $where['id'] = $_SESSION['uid'];
            $obj = M('m_user')->where($where)->find();

            $_SESSION['username'] = $obj['username'];
            $_SESSION['img']      = $obj['img'];
        }


        /**
         * 清除session
         * 根据传入的name清除指定的额session，不传入则默认清除所有session(退出登录用)
         * @param string $name
         */
        static function clearSession( $name = '' ){

            if( '' == $name){
                if(isset($_SESSION['username'])){
                    unset($_SESSION['username']);
                }

                if(isset($_SESSION['uid'])){
                    unset($_SESSION['uid']);
                }

                if(isset($_SESSION['img'])){
                    unset($_SESSION['img']);
                }

                if(isset($_SESSION['newImg'])){
                    unset($_SESSION['newImg']);
                }

                if(isset($_SESSION['carSystemImg'])){
                    unset($_SESSION['carSystemImg']);
                }

                if(isset($_SESSION['currentUrl'])){
                    unset($_SESSION['currentUrl']);
                }

                if(isset($_SESSION['activeNotice'])){
                    unset($_SESSION['activeNotice']);
                }

                if(isset($_SESSION['activeNoticeCount'])){
                    unset($_SESSION['activeNoticeCount']);
                }
            }else{
                if(isset($_SESSION[$name])){
                    unset($_SESSION[$name]);
                }
            }

        }

        /**
         * 上传图片
         * @param $config
         * @return mixed   正确则返回路径名称 错误则返回错误信息
         */
        static function uploadImg($config){

            if (!empty($_FILES)) {

                $upload = new \Think\Upload($config);// 实例化上传类
                $info = $upload->upload();

                //判断是否有图
                $pathName = '';
                if($info){
                    foreach($info as $file){
                        $pathName .= $file['savepath'].$file['savename'];
                    }
                    $retArr['success'] = 1;
                    $retArr['msg'] = $pathName;
                    $retArr['size'] = $file['size'];
                    $retArr['fileName'] = $file['name'];
                    $retArr['saveName'] = $file['savename'];
                    return $retArr;
                }
                else{
                    $retArr['success'] = 0;
                    $retArr['msg'] = $upload->getError();
                    return $retArr;
                }
            }
        }

        /**
         * 返回当前时间
         * @return false|string
         */
        static function getNowTime(){

            return date('Y-m-d H:i:s', time());
        }


    }