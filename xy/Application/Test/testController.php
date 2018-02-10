<?php
// 引入ThinkPHP入口文件
require dirname(__FILE__).'/../../index.php';

//导入要测试的控制器
require 'C:\xampp\htdocs\xy\Application\Home\Controller\GoodController.class.php';


use Home\Controller\GoodController;//引入测试类
class SkuTest extends PHPUnit_Framework_TestCase{

	/*public function testUpdateSku()
	{
		// 新建控制器
	    $Cat = new SkuModel();
	    session('user_auth.admin_uid',1);
	    // 调用控制器方法
	    $Cat->updateSKU(
	    	array(
	    		'spu_name' => '暗矿蓝',
	    		'spu_id' => 3,
	    		'spu_index' => 1,
	    		'qcode'=>'rh',
	    		'cat_id'=>2,
	    		'skuStoData'=>array(
	    			'data'=>array(
	    				array(
	    					'sto_id'=>2,
	    					'skuStoData'=>array(
	    						array(
	    							'sku_id'	 => 0,
									'sku_storage_id' => 0,
									'stock'  	 => 800,
									'spec_name'  => '30只装',
									'unit_price' => 700.34,
									'sku_sto_index'=>2,
									'status'=> 1,
	    							),
	    						array(
	    							'sku_id'	 => 0,
									'sku_storage_id' => 0,
									'stock'  	 => 800,
									'spec_name'  => '31只装',
									'unit_price' => 700.34,
									'sku_sto_index'=>2,
									'status'=> 1,
	    							),
	    						array(
	    							'sku_id'	 => 0,
									'sku_storage_id' => 0,
									'stock'  	 => 800,
									'spec_name'  => '32只装',
									'unit_price' => 700.34,
									'sku_sto_index'=>2,
									'status'=> 1,
	    							)
	    						)

	    					),
	    				array(
	    					'sto_id'=>14,
	    					'skuStoData'=>array(
	    						array(
	    							'sku_id'	 => 7,
									'sku_storage_id' => 4,
									'stock'  	 => 800,
									'spec_name'  => '30只装',
									'unit_price' => 700.34,
									'sku_sto_index'=>2,
									'status'=> 1,
	    							),
	    						array(
	    							'sku_id'	 => 8,
									'sku_storage_id' => 5,
									'stock'  	 => 800,
									'spec_name'  => '33只装',
									'unit_price' => 700.34,
									'sku_sto_index'=>2,
									'status'=> 1,
	    							),
	    						array(
	    							'sku_id'	 => 9,
									'sku_storage_id' => 5,
									'stock'  	 => 800,
									'spec_name'  => '30只装',
									'unit_price' => 700.34,
									'sku_sto_index'=>2,
									'status'=> 1,
									'delete'=>true,	
	    							)
	    						)
	    					)
	    				)
	    			)
	    		)
	    	);
		// 断言
		$this->assertEquals(1);
		
	}*/
	public function testgetlist()
	{
		// 新建控制器
	    session('user_auth.admin_uid',1);
	    session('user_auth.uid',1);
	    $sku = new GoodController();

	    // 调用控制器方法
	    $re=$sku->get_(
	    	array('sku_id'=>4)
	    	);
	    // var_dump($re);
		// 断言
		$this->assertEquals(3,count($re));
		
	}

	/*public function testget_()
	{
		// 新建控制器
	    $sku = new SkuModel();
	    session('user_auth.admin_uid',1);

	    // 调用控制器方法
	    $re=$sku->get_(
	    	array('sku_id'=>4)
	    	);
		// 断言
		$this->assertEquals(8,count($re));
	}

	public function testcreatefinancecart()
	{
		// 新建控制器
	    $sku = new SkuModel();
	    session('user_auth.admin_uid',1);

	    // 调用控制器方法
	    $re=$sku->createFinanceCart(
	    	array(
	    		'spec_name'=>'111',
	    		'sku_index'=>1,
	    		'sku_class'=>0,
	    		'status'=>1
	    		)
	    	);
		// 断言
		$this->assertGreaterThan(0,$re);
	}

	public function testqueryFinanceCart()
	{
		// 新建控制器
	    $sku = new SkuModel();
	    session('user_auth.admin_uid',1);

	    // 调用控制器方法
	    $re=$sku->queryFinanceCart(
	    	array(
	    		'sku_id'=>1,
	    		'status'=>1,
	    		'type'=>1
	    		)
	    	);
		// 断言
		$this->assertEquals(1,count($re));
	}

	public function testdeleteFinanceCat()
	{
		// 新建控制器
	    $sku = new SkuModel();
	    session('user_auth.admin_uid',1);

	    // 调用控制器方法
	    $re=$sku->deleteFinanceCat(
	    	array(
	    		'sku_id'=>1,
	    		)
	    	);
		// 断言
		$this->assertEquals(1,$re);
	}*/

}