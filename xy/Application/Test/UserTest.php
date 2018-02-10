<?php
// 引入ThinkPHP入口文件
require dirname(__FILE__).'/../../index.php';

//导入要测试的控制器
require APP_PATH.'xy\Application\Home\Model\UserModel.class.php';

use Home\Model\UserModel;//引入测试类
class IndexTest extends PHPUnit_Framework_TestCase{

	public function test()
	{
		// 新建控制器
	    $User = new UserModel();
	    $data = array(
	    	'username' => "57128121110",
	    	'password' => "de2886ca66a1e008e70e910e2c716bff",
	    	'type'	   => 1,
	    	'mode'	   => 2
	    	);
	    // 调用控制器方法
	    $res = $User->login($data);
		// 断言
		$this->assertEquals(1,$res['uid']);
		
	}

}