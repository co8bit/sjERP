<?php
// 引入ThinkPHP入口文件
require dirname(__FILE__).'/../../index.php';

use Home\Model\WarehouseModel;//引入测试类
// use Home\Model\ConfigModel;//引入测试类
class WarehouseTest extends PHPUnit_Framework_TestCase{


	public function testget_()
	{
		// 新建控制器
	    $warehouse = new WarehouseModel();
	    session('user_auth.admin_uid',1);

	    // 调用控制器方法
	    $re=$warehouse->get_(
	    	array('wid' => 1)
	    	);
		// 断言
		$this->assertEquals(1,$re['wid']);
	}

	public function testget_fail()
	{
		try{
			// 新建控制器
		    $warehouse = new WarehouseModel();
		    session('user_auth.admin_uid',1);

		    // 调用控制器方法
		    $re=$warehouse->get_(
		    	array('wid' => 1.5)
		    	);
		}catch(\XYException $e)
		{
			$error = $e->getCode();
			// 断言
			$this->assertEquals(-2001,$error);
		}
	}

	public function testget_fail2()
	{
		try{
			// 新建控制器
		    $warehouse = new WarehouseModel();
		    session('user_auth.admin_uid',1);

		    // 调用控制器方法
		    $re=$warehouse->get_(
		    	array('wid' =>10000)
		    	);
		}catch(\XYException $e)
		{
			$error = $e->getCode();
			// 断言
			$this->assertEquals(-2001,$error);
		}
	}

	public function testedit()
	{
		// 新建控制器
	    $warehouse = new WarehouseModel();
	    session('user_auth.admin_uid',1);

	    // 调用控制器方法
	    $re=$warehouse->get_(
	    	array('wid' => 1,'remark'=>'')
	    	);
		// 断言
		$this->assertEquals(1,$re['wid']);
	}

	public function testedit1()
	{
		// 新建控制器
	    $warehouse = new WarehouseModel();
	    session('user_auth.admin_uid',1);

	    // 调用控制器方法
	    $re=$warehouse->get_(
	    	array('wid' => 1,'remark'=>'11111')
	    	);
		// 断言
		$this->assertEquals(1,$re['wid']);
	}

	public function testcreateStockTakingOrRequisitionDraft()
	{
		
	}



}