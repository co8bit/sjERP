<?php
class tpunit{
	static function v5_x(){
		require_once TP_BASEPATH.'thinkphp/base.php';
	}
	static function v3_2_3(){
		error_reporting(0);
		include_once dirname(__FILE__).'/replace/AjaxReturnEvent.php';
		include TP_BASEPATH.'ThinkPHP/Common/functions.php';
		
		define('EXT', '.class.php');
		define('APP_PATH',TP_BASEPATH.'Application/');
		// 系统常量定义
		defined('THINK_PATH')   or define('THINK_PATH',     TP_BASEPATH.'ThinkPHP/');
		defined('APP_PATH')     or define('APP_PATH',       dirname($_SERVER['SCRIPT_FILENAME']).'/');
		defined('APP_STATUS')   or define('APP_STATUS',     'config'); // 应用状态 加载对应的配置文件
		defined('APP_DEBUG')    or define('APP_DEBUG',      false); // 是否调试模式
		defined('RUNTIME_PATH') or define('RUNTIME_PATH',   APP_PATH.'Runtime/');   // 系统运行时目录
		defined('LIB_PATH')     or define('LIB_PATH',       realpath(THINK_PATH.'Library').'/'); // 系统核心类库目录
		defined('CORE_PATH')    or define('CORE_PATH',      LIB_PATH.'Think/'); // Think类库目录
		defined('BEHAVIOR_PATH')or define('BEHAVIOR_PATH',  LIB_PATH.'Behavior/'); // 行为类库目录
		defined('MODE_PATH')    or define('MODE_PATH',      THINK_PATH.'Mode/'); // 系统应用模式目录
		defined('VENDOR_PATH')  or define('VENDOR_PATH',    LIB_PATH.'Vendor/'); // 第三方类库目录
		defined('COMMON_PATH')  or define('COMMON_PATH',    APP_PATH.'Common/'); // 应用公共目录
		defined('CONF_PATH')    or define('CONF_PATH',      COMMON_PATH.'Conf/'); // 应用配置目录
		defined('LANG_PATH')    or define('LANG_PATH',      COMMON_PATH.'Lang/'); // 应用语言目录
		defined('HTML_PATH')    or define('HTML_PATH',      APP_PATH.'Html/'); // 应用静态目录
		defined('LOG_PATH')     or define('LOG_PATH',       RUNTIME_PATH.'Logs/'); // 应用日志目录
		defined('TEMP_PATH')    or define('TEMP_PATH',      RUNTIME_PATH.'Temp/'); // 应用缓存目录
		defined('DATA_PATH')    or define('DATA_PATH',      RUNTIME_PATH.'Data/'); // 应用数据目录
		defined('CACHE_PATH')   or define('CACHE_PATH',     RUNTIME_PATH.'Cache/'); // 应用模板缓存目录
		defined('CONF_EXT')     or define('CONF_EXT',       '.php'); // 配置文件后缀
		defined('CONF_PARSE')   or define('CONF_PARSE',     '');    // 配置文件解析方法
		defined('ADDON_PATH')   or define('ADDON_PATH',     APP_PATH.'Addon');
		define('IS_CGI',(0 === strpos(PHP_SAPI,'cgi') || false !== strpos(PHP_SAPI,'fcgi')) ? 1 : 0 );
		define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
		define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
		$config=include_once TP_BASEPATH.'ThinkPHP/Conf/convention.php';
		

		C($config);
		// 读取当前应用状态对应的配置文件
		if(APP_STATUS && is_file(CONF_PATH.APP_STATUS.CONF_EXT))
    		C(include CONF_PATH.APP_STATUS.CONF_EXT); 
    	// echo 444;
    	
		spl_autoload_register('my_autoload');

		// echo 333;

		/**
 		* 类库自动加载
 		* @param string $class 对象类名
 		* @return void
 		*/
		function my_autoload($class) {
		echo 1;
			if($class=='Think\Controller'){
				// include  dirname(__FILE__).'/replace/Controller.class.php';
				include  'C:\xampp\htdocs\xy\ThinkPHP\Library\Think\Controller.class.php';
				return;
			}
	
			if (false !== strpos($class, '\\')) {
				$name = strstr($class, '\\', true);
		
				if (in_array($name, array('Think', 'Org', 'Behavior', 'Com', 'Vendor')) || is_dir(LIB_PATH . $name)) {
					// Library目录下面的命名空间自动定位
					$path = LIB_PATH;
			
				} else {
					// 检测自定义命名空间 否则就以模块为命名空间
					$namespace = C('AUTOLOAD_NAMESPACE');
					$path = isset($namespace[$name]) ? dirname($namespace[$name]) . '/' : APP_PATH;
				}
				$filename = $path . str_replace('\\', '/', $class) . EXT;
				if (is_file($filename)) {
					// Win环境下面严格区分大小写
					if (IS_WIN && false === strpos(str_replace('/', '\\', realpath($filename)), $class . EXT)) {
						return;
					}
					include $filename;
				}
			} elseif (!C('APP_USE_NAMESPACE')) {
				// 自动加载的类库层
				foreach (explode(',',C('APP_AUTOLOAD_LAYER')) as $layer) {
					if (substr($class, -strlen($layer)) == $layer) {
						if (require_cache(MODULE_PATH . $layer . '/' . $class . EXT)) {
							return;
						}
					}
				}
				// 根据自动加载路径设置进行尝试搜索
				foreach (explode(',',C('APP_AUTOLOAD_PATH')) as $path) {
					if (import($path . '.' . $class))
						// 如果加载类成功则返回
						return;
				}
			}
		}//end of my_autoload

		echo 222;
	}
}


//兼容老版本
if(!defined('TPUNIT_VERSION')){
	define('TPUNIT_VERSION', '3.2.3');
}
//根据版本，选择不同的base文件
switch(TPUNIT_VERSION){
	case '5.x':{
		//tpunit::v5_x();
		break;
	}
	default:{
		tpunit::v3_2_3();
		echo 22;
	}
}