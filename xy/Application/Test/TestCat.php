<?php
// 引入ThinkPHP入口文件
require dirname(__FILE__).'/../../index.php';
use Home\Model\CatModel;//引入测试类
class IndexTest extends PHPUnit_Framework_TestCase{

	public function test()
	{
		// 新建控制器
	    $Cat = new CatModel();
	    // 调用控制器方法
	    $Cat->test();
		// 断言
		$this->expectOutputString('123');
		
	}
	public function testget_()
	{
		// 新建控制器
        $Cat = new CatModel();
        // 调用控制器方法
        $Cat->test();
        // 断言
        $this->expectOutputString('5');
		
	}

}