<?php
return array(
	//'配置项'=>'配置值'

	//自定义权限控制开关
	'AUTH_GUEST' => true, //是否开启游客模式
	'VERIFY_CODE' => false,//否开启验证码

	//SESSIONDB设置
    'SESSION_TYPE'          => 'Db',
    //'SESSION_PREFIX' => 'mobileggdysgdys',
    'SESSION_EXPIRE'        =>28800,//秒

	//开启TRACE调试信息
	'SHOW_PAGE_TRACE' => false,
	//简化模板目录结构
	'TMPL_FILE_DEPR'=>'_',
	//URL模式
	'URL_MODEL' => 2,
	//数据库配置
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => '127.0.0.1', // 服务器地址
	'DB_NAME'   => 'shouji', // 数据库名
	'DB_USER'   => 'root', // 用户名
	'DB_PWD'    => '2879046tkgg@', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '', // 数据表前缀
);