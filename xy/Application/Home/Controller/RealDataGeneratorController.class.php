<?php
namespace Home\Controller;
use Think\Controller;

require_once APP_PATH.'../vendor/autoload.php';

/**
 * 生成访客使用的真实数据控制器
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class RealDataGeneratorController extends GeneratorController
{
	protected $faker = null;

	protected $catCount = 0;//cat的条目数在本次生成了这么多个
	protected $cidCount = 0;//cid的条目数在本次生成了这么多个
	protected $skuCount = 0;//sku的条目数在本次生成了这么多个

	protected $shop1_admin_uid = 0;//第一个商家的管理员用户uid
	protected $shop1_user_uid = 0;//第一个商家的库管用户uid

	protected $adminUid = 4;

	protected $catList = array(); 
	protected $spuList = array();
	protected $skuList = array();
	protected $companyList = array();

	protected $RealOrderModel = array(
			'unit_price' => 50,
			'quantity' => 100,
			'off' => 50,
			'cash' => 1000,
			'bank' => 2000,
			'online_pay' => 2000,
		);

	protected function ranFloat()
	{
		return rand(9,11)/10;
	}

	 /**
	 * 生成假数据
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-11-23
	 */
	public function generator()
	{
		try
		{
			// $this->display();
			$seed = I('param.seed/d');

			$this->faker = \Faker\Factory::create('zh_CN');
			// $this->faker->addProvider(new \Faker\Provider\zh_CN\CatName($this->faker));
			$this->faker->seed($seed);

			log_("VisitorMobile:",C('VISIT_USER_MOBILE'),$this);
			$this->genAdminUserByUsername(C('VISIT_USER_MOBILE'));

			$this->praseProductList(APP_PATH."Home/Controller/SkuDemoList.php");

			$this->genCompany();
			$this->genOrder();
			$this->genWarehouse_StockTaking();
			$this->genFinance_IncomeOrExpense();
			$this->genFinance_ReceiptOrPayment();

			D("AuthGroupAccess")->setUserGroup(array(
					'uid'      => $this->adminUid,
					'group_id' => 1,
				));
			$this->show('<br><br>All OK<br><br>');
			$this->show('<a href="'.U("Index/index").'">Index</a><br>');
		}catch(\XYException $e)
		{
			$this->outputMETHOD(__METHOD__,false,$e);
		}
	}

	public function deleteAdminInfoByAdminId()
	{
		// $admin_uid = I('param.admin_uid/d');
		// try
		// {
		// 	//销户接口，删除用户所有用户信息
		// 	M('PaymentDetails')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('SmsDetails')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('UserAccount')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Paybill')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('SkuBill')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('EverydaySummarySheet')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Finance')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Warehouse')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Order')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Parkaddress')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('PaymentDetails')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Carlicense')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Phonenum')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Contact')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Company')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Sku')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Spu')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Cat')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('Config')->where(array('admin_uid' => $admin_uid))->delete();
		// 	M('User')->where(array('admin_uid' => $admin_uid))->delete();

		// 	$this->show('<br><br>用户注销成功<br><br>');
		// }catch(\XYException $e)
		// {
		// 	$this->outputMETHOD(__METHOD__,false,$e);
		// }

	}

	public function genAdminUserByUsername($username)
	{
		$this->outputMETHOD(__METHOD__);

		$dbUser = D('User');
		if (is_login() > 0) $dbUser->logout();

		//管理员用户1
		$tmpInfo = $dbUser->admin_register(array(
				'username'   => $username,
				'password'   => md5(C('passwordMD5Prefix').'123456'),
				'email'      => $this->faker->email,
				'where_know' => '访客体验账户',
			),false,true);
		$this->adminUid = $tmpInfo['uid'];
		
		C('VISITOR_UID',$this->adminUid);
		D('UserAccount')->renewForMember(array(
					'admin_uid' => getUid(),
					'bill_class' => 3,
					'member_count' => 12,
				),false,true);//设置管理员级别最高，需要Controller重新加载
		A('Home')->_initialize();//Model调用的登录，登陆完后需要加载C()的config配置
		$dbUser->editUserInfo(array(
				'uid'  => $this->adminUid,
				'name' => '体验账户',
				'qq'   => '123456',
			),false);
		$dbUser->editShopInfo(array(
				'shop_name' => "杭州跃迁科技",
				'industry'  => '互联网',
				'province'  => '浙江',
				'city'      => '杭州',
			));
		$dbUser->setGTClientID(array(
				'GTClientID'=>'a17e6ef66dd5c8cb49f73f96fae71f97',
			));


				//普通用户1，zxb
		$this->shop1_user_uid = $dbUser->register(array(
				'username' => '10987654321',
				'password' => md5(C('passwordMD5Prefix').'123456'),
				'status'   => 1,
				'name'     => '跃迁科技手机',
				'rpg'      => 3,
				'mobile'   => '10987654321',
				'qq'       => '1744357320',
				'email'    => $this->faker->email,
			));

		//android test user
		$this->shop1_user_uid = $dbUser->register(array(
				'username' => '10992526262',
				'password' => md5(C('passwordMD5Prefix').'123456'),
				'status'   => 1,
				'name'     => '跃迁科技测试账户',
				'rpg'      => 3,
				'mobile'   => '10992526262',
				'qq'       => '555555555',
				'email'    => $this->faker->email,
			));

		$this->outputMETHOD(__METHOD__,true);
	}

	public function genCompany()
	{
		$this->outputMETHOD(__METHOD__);

		$dbCompany = D('Company');

		$this->cidCount = $this->faker->numberBetween($min = 10,$max = 50);
		for ($i = 1; $i <= $this->cidCount; $i++)
		{
			$contactData = null;

			$companyName = $this->faker->unique()->company();
			for ($j = 0; $j < $this->faker->numberBetween($min = 1,$max = 4); $j++)
			{
				$contactData[] = array(
						'contact_name'  => $this->faker->unique()->name(),
						'phonenum'      => array(
								array('mobile' => $this->faker->phoneNumber()),
								array('mobile' => $this->faker->phoneNumber()),
							),
						'car_license' => array(
								array('car_license' => strtoupper($this->faker->bothify('浙??####'))),
								array('car_license' => strtoupper($this->faker->bothify('浙??####'))),
							),
					);
			}
			$this->companyList[] = $dbCompany->create_(array(
					'name'         => $companyName,
					'qcode'        => D('Other')->getPinYin($companyName),
					'address'      => $this->faker->address(),
					'remark'       => $this->faker->catchPhrase(),
					'init_payable' => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 0, $max = 200)),
					'status'       => 1,//todo：暂不考虑状态为0
					'contact'      => $contactData,
				),false);


			// if (!IS_CLI)
				// echo 'createCompany:'.$companyName.'<br>';
		}

		$this->outputMETHOD(__METHOD__,true);
	}

	public function praseProductList($filepath)
	{
		$productList = require($filepath);

		$dbCat = D('Cat');
		$dbSpu = D('Spu');
		$dbSku = D('Sku');
		foreach($productList as $key => $value)
		{
			if(array_key_exists($value["class_name"], $this->catList)
				&& array_key_exists($value["spu_name"], $this->spuList)
					&& array_key_exists($value["sku_name"], $this->skuList))
					continue;

			$catName = $value["class_name"];
			$spuName = $value["spu_name"];
			if(!array_key_exists($catName, $this->catList))
			{
				$tmpResult = $dbCat->where(array('cat_name' => $catName,'admin_uid'=>getAdminUid()))->find();
				if(!$tmpResult && $tmpResult == 0)
				{
					$this->catList[$catName] = $dbCat->createCat(array(
						'cat_name'  => $catName,
						'cat_index' => $this->faker->numberBetween($min = 1,$max = 2),
						'status'    => 1,//todo：暂不考虑状态为0
					),false);			
				}
			}
			if(!array_key_exists($spuName, $this->spuList))
			{
				log_("Spu_name:",$spuName,$this);
				log_("Spu_Length:",strlen($spuName),$this);
				log_("Cat_id:",$this->catList[$catName],$this);
				$tmpResult = $dbSpu->where(array('spu_name' => $spuName,'admin_uid'=>getAdminUid()))->find();
				if(!$tmpResult && $tmpResult == 0)
				{
					$spuId = $dbSpu->createSPU(array(
						'spu_name' 	=> $spuName,
						'spu_index' => $this->faker->numberBetween($min = 1,$max = 2),
						'qcode'		=> D('Other')->getPinYin($spuName),
						'cat_id' 	=> intval($this->catList[$catName]),
						'status' 	=> 1
						),false);
					$this->spuList[$spuName] = array(
						//spu_name,spu_index,qcode,cat_id,status
						'spu_id' => $spuId ,
						'cat_id' => $this->catList[$catName]
						);
				}
			}

			$skuData = array(array(
					'spec_name'  => ($value["sku_name"]),
					'stock'      => $this->faker->numberBetween($min = 1000,$max = 5000),
					'unit_price' => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 100, $max = 700)),
					'sku_index'  => $this->faker->numberBetween($min = 1,$max = 2),
					'status'    => 1,//todo：暂不考虑状态为0
				));
			$this->skuList[$value["sku_name"]] = $dbSku->createSKU(array(
					'spu_id' => intval($this->spuList[$spuName]['spu_id']),
					'skuData'    => array('data'=>$skuData),
				),false);


		}
	}

	public function genOrder()
	{
		$this->outputMETHOD(__METHOD__);


		$dbOrder = D('Order');
		$dbSku = D('Sku');

		for ($i = 0; $i < $this->faker->numberBetween($min = 50,$max = 100); $i++)
		{
			$cartData = null;

			for ($j = 0; $j < $this->faker->numberBetween($min = 1,$max = 4); $j++)
			{
				$tmpSkuID   = null;
				$tmpInfo    = null;
				//$tmpSkuID   = $this->faker->unique()->numberBetween($min = 1,$max = $this->skuCount);
				$tmpSkuID   = intval($this->skuList[array_rand($this->skuList)]);
				log_("Sku_id:",$this->skuList[array_rand($this->skuList)],$this);

				$tmpInfo    = $dbSku->get_(array('sku_id'=>$tmpSkuID));

				$cartData[] = array(
						'sku_id'    => $tmpSkuID,
						'spu_name'  => $tmpInfo['spu_name'],
						'spec_name' => $tmpInfo['spec_name'],
						'quantity'  => $this->RealOrderModel['quantity']*$this->ranFloat(),
						'unitPrice' =>  $this->RealOrderModel['unit_price']*$this->ranFloat(),
					);
			}
			$statusArray = array(4,5,100);
			$status = $statusArray[$this->faker->numberBetween($min = 0,$max = 2)];
			if ($status === 100)
			{
				$dbOrder->createDraft(array(
						'class'        => $this->faker->numberBetween($min = 1,$max = 4),
						'cid'          => $this->companyList[array_rand($this->companyList)],
						'contact_name' =>  $this->faker->name(),
						'mobile'       => $this->faker->phoneNumber(),
						'park_address' => $this->faker->address(),
						'car_license'  => strtoupper($this->faker->bothify('浙??####')),
						'off'          =>  count($cartData)*$this->RealOrderModel['off']*$this->ranFloat(),
						'cash'         =>  count($cartData)*$this->RealOrderModel['cash']*$this->ranFloat(),
						'bank'         =>  count($cartData)*$this->RealOrderModel['bank']*$this->ranFloat(),
						'online_pay'   =>  count($cartData)*$this->RealOrderModel['online_pay']*$this->ranFloat(),
						'remark'       => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'status'       => $status,
						'cart'         => array('data'=>$cartData),
					),false);
			}
			else
			{
				$dbOrder->createOrder(array(
						'class'        => $this->faker->numberBetween($min = 1,$max = 4),
						'cid'          => $this->companyList[array_rand($this->companyList)],
						'contact_name' => $this->faker->name(),
						'mobile'       => $this->faker->phoneNumber(),
						'park_address' => $this->faker->address(),
						'car_license'  => strtoupper($this->faker->bothify('浙??####')),
						'off'          =>  count($cartData)*$this->RealOrderModel['off']*$this->ranFloat(),
						'cash'         =>  count($cartData)*$this->RealOrderModel['cash']*$this->ranFloat(),
						'bank'         =>  count($cartData)*$this->RealOrderModel['bank']*$this->ranFloat(),
						'online_pay'   =>  count($cartData)*$this->RealOrderModel['online_pay']*$this->ranFloat(),
						'remark'       => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'status'       => $status,
						'cart'         => array('data'=>$cartData),
					),false);
			}
			// if (!IS_CLI)
				// echo 'createOrder:'.$i.'<br>';
		}

		$orders = $dbOrder->where(array("admin_uid" => $this->adminUid))->select();

		foreach ($orders as $key => $value) {

			$floatDay = rand(0,30);
			$reg_date = strtotime("-$floatDay day");
			$dbOrder->where(array("oid" => $value["oid"]))->setField('reg_time',$reg_date);
		}

		$this->outputMETHOD(__METHOD__,true);
	}



		/**
	 * 生成Warehouse的盘点单数据.
	 *
	 * 依赖genSku提供的skuCount
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-08
	 */
	public function genWarehouse_StockTaking()
	{
		$this->outputMETHOD(__METHOD__);


		$dbWarehouse = D('Warehouse');
		$dbSku = D('Sku');

		for ($i = 0; $i < $this->faker->numberBetween($min = 5,$max = 20); $i++)
		{
			$cartData = null;

			for ($j = 0; $j < $this->faker->numberBetween($min = 1,$max = 4); $j++)
			{
				$tmpSkuID   = null;
				$tmpSkuID   = intval($this->skuList[array_rand($this->skuList)]);

				log_("SkuId:",$this->skuList[array_rand($this->skuList)],$this);

				$cartData[] = array(
						'sku_id'    => $tmpSkuID,
						'quantity'  => $dbSku->where(array('sku_id' => $tmpSkuID))->getField('stock')+1,
					);
			}
			$statusArray = array(1,100);
			$status = $statusArray[$this->faker->numberBetween($min = 0,$max = 1)];
			log_("cartData",$cartData,true);
			if ($status === 100)
			{
				$dbWarehouse->createStockTakingDraft(array(
						'remark'    => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'check_uid' => $this->shop1_user_uid,
						'cart'      => array('data'=>$cartData),
					),false);
			}
			else
			{
				$dbWarehouse->createStockTaking(array(
						'remark'    => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'check_uid' => $this->shop1_user_uid,
						'cart'      => array('data'=>$cartData),
					),false,true);
			}
			// if (!IS_CLI)
				// echo 'createWarehouse_StockTaking:'.$i.'<br>';
		}

		$this->outputMETHOD(__METHOD__,true);
	}

}

