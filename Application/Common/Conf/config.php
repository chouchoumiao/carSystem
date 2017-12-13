<?php

header("Content-type: text/html;charset=utf-8");

define(MY_SITE, $_SERVER['SERVER_NAME']);   //发送邮件附带地址的时候需要  本地环境

define(DOCUMENT_ROOT,$_SERVER['DOCUMENT_ROOT']);

define(PUBLIC_PATH,$_SERVER['DOCUMENT_ROOT'].'/carSystem/Public');

//头像文件夹地址
define(PROFILE_PATH,$_SERVER['DOCUMENT_ROOT'].'/carSystem/Public/Uploads/profile');
//文章图片文件夹路径
define(POST_PATH,$_SERVER['DOCUMENT_ROOT'].'/carSystem/Public/Uploads/post');
//文章附件文件夹路径
define(POST_ATTACHMENT_PATH,$_SERVER['DOCUMENT_ROOT'].'/carSystem/Public/Uploads/postAttachment');
//资源库文件夹路径
define(MEDIA_PATH,$_SERVER['DOCUMENT_ROOT'].'/carSystem/Public/Uploads/Media');

define(CURRENT_URL,'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);

define(PAGE_SHOW_COUNT_10,10);	//每页显示10条记录



return array(

	//'SHOW_ERROR_MSG'        =>  true,


    //'DB_DEBUG' => true,

	//'SHOW_PAGE_TRACE'=>true,        //开启追踪调试

	/********************数据库配置**************************/
	'DB_TYPE' => 'mysql',
	'DB_HOST' => 'localhost',
	'DB_NAME' => 'carSystem',
	'DB_USER' => 'root',
	'DB_PWD' => '84112326Wu',
	'DB_PORT' => 3306,
	'DB_PREFIX' => 'ccm_',
    'DB_CHARSET' => 'utf8',
	/********************数据库配置**************************/

	/********************邮件设置(管理员邮箱)**************************/
    'MAIL_ADDRESS'=>'wu_jy1984@163.com', // 邮箱地址
    'MAIL_LOGINNAME'=>'wu_jy1984@163.com', // 邮箱登录帐号
    'MAIL_SMTP'=>'smtp.163.com', // 邮箱SMTP服务器
    'MAIL_PASSWORD'=>'mon4184860', // 邮箱密码
	/********************邮件设置(管理员邮箱)**************************/


    'DEFAULT_CHARSET' => 'utf-8', // 默认输出编码


	'DEFAULT_MODULE' => 'Admin',  // 追加默认模块设置为Admin

    //性别数组
    'SEX_ARRAY' => array(
        0=>'女',
        1=>'男'
    ),


    //激活状态数组
    'STATUS_ARRAY' => array(
		-1=>'未审核',
        0=>'未激活',
        1=>'已激活'
    ),


    /**********************上传时一般设定**********************/

    //文件上传默认大小:5M
    'FILE_SIZE' => 5242880,
    
    //定义资源库可上传的文件后缀
	'POST_UPLOAD_TYPE_ARRAY' => array(
		'jpg','png','jpeg','gif',
		'txt','xls','pdf','doc',
		'xlsx','docx','pptx','pptx'
	),

    //定义资源库可上传的文件后缀
    'POST_UPLOAD_Attachment_TYPE_ARRAY' => array(
        'zip','7z','rar',
        'jpg','png','jpeg','gif',
        'txt','xls','pdf','doc',
        'xlsx','docx','pptx','pptx'
    ),

    //图片资源后缀
	'MEDIA_TYPE_ARRAY'=> array(
		'jpg','png','jpeg','gif'
	),

    //文档资源后缀
	'FILE_TYPE_ARRAY'=> array(
		'txt','xls','pdf','doc',
		'xlsx','docx','pptx','pptx'
	),
    /**********************上传时一般设定**********************/


);