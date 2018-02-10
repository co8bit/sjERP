<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG',true);
// 绑定Home模块到当前入口文件
define('BIND_MODULE','Home');

// 定义应用目录
/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
// define('APP_PATH','./Application/');
define('APP_PATH',dirname(__FILE__).'/Application/');

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define ( 'RUNTIME_PATH', './Runtime/' );

// 引入ThinkPHP入口文件
require APP_PATH.'../ThinkPHP/ThinkPHP.php';
