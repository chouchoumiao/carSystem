<?php
/**
 * Created by wujiayu.
 * User: Administrator
 * Date: 2016/5/19
 * Time: 13:59
 */

namespace Admin\Controller;

use Admin\Model\ToolModel;
use Think\Controller;

class LoginController extends Controller{

    private $actionName;

    public function index(){

        if((isset($_POST['action'])) && ('' != $_POST['action'])){
            $this->actionName = I('post.action');

            switch ($this->actionName){
                case 'login':
                    $this->login();
                    break;
                default:
                    ToolModel::goToUrl('非常操作','Login/login');
                    break;

            }
        }else{
            if( (!isset($_SESSION['username'])) || ('' == $_SESSION['username']) ){
                //无session则进入后台主页面
                $this->redirect('Login/login');
            }else{
                //有session则进入后台主页面
                $this->redirect('Index/index');
            }
        }

    }


    private function login(){

        //检查表单
        $msg = checkForm( $this->actionName );

        if( '' != $msg ){

            $arr['success'] = 'NG';
            $arr['msg'] = $msg;
            echo json_encode($arr);
            exit;
        }

        //检查登录用户名密码是否正确
        $userInfo = D('User')->checklogin();

        if( $userInfo ){

            //将用户信息中用户名 id 头像路径放入session中
            $_SESSION['uid']      = $userInfo['id'];
            $_SESSION['username'] = $userInfo['username'];
            $_SESSION['img']      = $userInfo['img'];

            //追加如果有记录当前地址的话则登录后默认跳转到该地址
            if( (isset($_SESSION['currentUrl'])) && ('' != $_SESSION['currentUrl']) ){
                $arr['currentUrl'] = $_SESSION['currentUrl'];
                unset($_SESSION['currentUrl']);     //清空该session
            }else{
                $arr['currentUrl'] = '';
            }

            $arr['success'] = 'OK';
            echo json_encode($arr);
            exit;
        }else{
            $arr['success'] = 'NG';
            $arr['msg'] = '用户名或密码错误，请重新输入！';
            echo json_encode($arr);
            exit;
        }

    }

    /**
     * 退出登录
     *
     */
    public function logout(){

        ToolModel::clearSession();  //清除所有session
        $this->redirect('Login/login');
    }
}