<?php
// 引入ThinkPHP入口文件
require dirname(__FILE__).'/../../index.php';
include_once dirname(__FILE__).'/../../ThinkPHP/Common/functions.php';

define(RUN_PHPUNIT, true);
use Home\Model\OrderModel;//引入测试类
class OrderTest extends PHPUnit_Framework_TestCase{

	public function createOrderProvider()
	{
		return array(
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>1,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			),
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>2,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			),
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>3,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			),
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>4,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			)
		);
	}

	/**
	 *@dataProvider createOrderProvider
	 */
	public function testcreateOrderSuccess($bank,$cash,$online_pay,$off,$company_name,$contact_name,$mobile,$car_license,$park_address,$freight,$reg_time,$remark,$status,$sto_id,$class,$cid,$cart)
	{
		// 新建控制器
	    $order = new OrderModel();
	    session('user_auth.admin_uid',1);
	    session('user_auth.uid',1);
	    session('user_auth.name',1);
	    // 调用控制器方法
	    $re=$order->createOrder(
	    	array(
	    		'bank'         =>$bank,
				'cash'         =>$cash,
				'online_pay'   =>$online_pay,
				'off'          =>$off,
				'company_name' =>$company_name,
				'contact_name' =>$contact_name,
				'mobile'       =>$mobile,
				'car_license'  =>$car_license,
				'park_address' =>$park_address,
				'freight'      =>$freight,
				'reg_time'     =>$reg_time,
				'remark'       =>$remark,
				'status'       =>$status,
				'sto_id'       =>$sto_id,
				'class'        =>$class,
				'cid'          =>$cid,
				'cart'         =>$cart
	    		)
	    	);
		// 断言
		$this->assertGreaterThan(0,$re);
	}

	/**************我是测试通过的分界线*********************************************************/

	public function reopenOrderProvider()
	{
		return array(
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>1,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			),
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>2,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			),
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>3,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			),
			array(
				'bank'         =>0,
				'cash'         =>0,
				'online_pay'   =>0,
				'off'          =>0,
				'company_name' =>'零售客户',
				'contact_name' =>'谢凤兰',
				'mobile'       =>'15130701272',
				'car_license'  =>'浙YB8204',
				'park_address' =>'',
				'freight'      =>0,
				'reg_time'     =>1501232049.587,
				'remark'       =>'',
				'status'       =>4,
				'sto_id'       =>1,
				'class'        =>4,
				'cid'          =>1,
				'cart'         =>array(
					'data'=>array(
						array("sku_id"=>"10","spu_name"=>"红色","spec_name"=>"02只装","quantity"=>2,"unitPrice"=>236.5),
						array("sku_id"=>"28","spu_name"=>"香槟黄","spec_name"=>"42只装","quantity"=>1,"unitPrice"=>256.3),
						array("sku_id"=>"45","spu_name"=>"玫瑰褐","spec_name"=>"25只装","quantity"=>3,"unitPrice"=>586)
					)
				)
			)
		);
	}

	/**
	 *@dataProvider createOrderProvider
	 */
	public function testcreateOrderSuccess($bank,$cash,$online_pay,$off,$company_name,$contact_name,$mobile,$car_license,$park_address,$freight,$reg_time,$remark,$status,$sto_id,$class,$cid,$cart)
	{
		// 新建控制器
	    $order = new OrderModel();
	    session('user_auth.admin_uid',1);
	    session('user_auth.uid',1);
	    session('user_auth.name',1);
	    // 调用控制器方法
	    $re=$order->createOrder(
	    	array(
	    		'bank'         =>$bank,
				'cash'         =>$cash,
				'online_pay'   =>$online_pay,
				'off'          =>$off,
				'company_name' =>$company_name,
				'contact_name' =>$contact_name,
				'mobile'       =>$mobile,
				'car_license'  =>$car_license,
				'park_address' =>$park_address,
				'freight'      =>$freight,
				'reg_time'     =>$reg_time,
				'remark'       =>$remark,
				'status'       =>$status,
				'sto_id'       =>$sto_id,
				'class'        =>$class,
				'cid'          =>$cid,
				'cart'         =>$cart
	    		)
	    	);
		// 断言
		$this->assertGreaterThan(0,$re);
	}

	



}