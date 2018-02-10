<?php
return array(
	/**
	 * @todo  关掉后会让watchlog有log输出，不知道为什么
	 */
	'SHOW_PAGE_TRACE' =>true, //显示页面Trace信息，@todo: 关掉会影响log，有待解决
	'JSONRETURN_HEADER_JSON' =>true,//正式部署请为true。jsonReturn等函数返回页面的时候是否强制exit();

	'LOG_RECORD' => true, // 开启日志记录
	'LOG_LEVEL'  =>'EMERG,ALERT,CRIT,ERR,WARN,NOTIC,NOTICE,INFO,DEBUG,SQL,WORK,PARAM', //日志记录类型
	'DB_DEBUG'  			=>  true, // 数据库调试模式 开启后可以记录SQL日志
	'LOG_EXCEPTION_RECORD'  =>  true,    // 是否记录异常信息日志
	
	// 数据库配置信息
	'DB_TYPE'   => 'mysql', // 数据库类型
	'DB_HOST'   => 'localhost', // 服务器地址
	'DB_NAME'   => 'databasename', // 数据库名
	'DB_USER'   => 'databasename', // 用户名
	'DB_PWD'    => 'databasepwd', // 密码
	'DB_PORT'   => 3306, // 端口
	'DB_PREFIX' => '', // 数据库表前缀
	'DB_PARAMS' => array(\PDO::ATTR_CASE => \PDO::CASE_NATURAL),//数据库字段名不强制小写

	'URL_MODEL' => 0,//路由模式
	
	'SESSION_AUTO_START'    => true,    // 是否自动开启Session
	// 'SESSION_OPTIONS' => array(
	// 		'domain' => '',
	// 		'path' => '/',
	// 	),
	
	// 'TOKEN_ON'=>true,  // 是否开启令牌验证
	// 'TOKEN_NAME'=>'__hash__',    // 令牌验证的表单隐藏字段名称
	// 'TOKEN_TYPE'=>'md5',  //令牌哈希验证规则 默认为MD5
	// 'TOKEN_RESET'=>true,  //令牌验证出错后是否重置令牌 默认为true


	// 'REDIRECT_URL' => 'http://www.xyjxc.me/app',
	
);