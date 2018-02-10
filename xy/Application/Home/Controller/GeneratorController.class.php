<?php
namespace Home\Controller;
use Think\Controller;

require_once APP_PATH.'../vendor/autoload.php';

/**
 * 生成假数据控制器
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class GeneratorController extends BaseController
{
	protected $faker = null;

	protected $catCount = 0;//cat的条目数在本次生成了这么多个
	protected $cidCount = 0;//cid的条目数在本次生成了这么多个
	protected $stoCount = 0;//sto的条目数在本次生成了这么多个
	protected $skuCount = 0;//sku的条目数在本次生成了这么多个

	protected $shop1_admin_uid = 0;//第一个商家的管理员用户uid
	protected $shop1_user_uid = 0;//第一个商家的库管用户uid


	/**
	 * 输出  进入、成功、出错  信息。
	 * @param string $methodName 方法名称，调用时需传入__METHOD__
	 * @param bool $type 不传代表是进入函数，传了代表成功与否
	 * @param \XYException $e 失败时的异常
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-07
	 */
	protected function outputMETHOD($methodName = null,$type = null,\XYException $e = null)
	{
		if (IS_CLI)
			echo "\n";
		else
		{
			if ($type === null)
				echo '<br>';
		}

		$EMsg = new \ErrorCode\ErrorCode();
        $ERROR_MSG_CN = $EMsg->getErrorCodeMsg();

		$tmpArray = explode('::',$methodName);
		$methodName = $tmpArray[1];
		if ($type === null)
		{
			if (!IS_CLI)
				echo '<font style="color:#FFFFFF; font-weight:bold;background: #337ab7;">==========  '.$methodName.'  开始  ==========</font>';
		}
		elseif ($type)
		{
			if (IS_CLI)
				echo '==========  '.$methodName.'  成功  =========='."\n";
			else
				echo '<font style="color:#FFFFFF; font-weight:bold;background: #5cb85c;">==========  '.$methodName.'  成功  ==========</font>';
		}
		elseif (!$type)
		{
			if (IS_CLI)
			{
				echo '==========  '.$e->getMessage().'  失败  =========='."\n";
				echo '异常(错误)代码：' . $e->getCode() . "\n";
				echo '异常(错误)中文信息：' . $ERROR_MSG_CN[$e->getCode()] . "\n";
			}
			else
			{
				echo '<font style="color:#FFFFFF; font-weight:bold;background: #FF0000;">==========  '.$e->getMessage().'  失败  ==========</font><br>';
				echo '异常(错误)代码<font style="color:#FF0000;">：' . $e->getCode() . '</font><br>';
				echo '异常(错误)中文信息：<font style="color:#FF0000;">：' . $ERROR_MSG_CN[$e->getCode()] . '</font><br>';
			}
		}
		if (IS_CLI)
			echo "\n";
		else
		{
			if ($type === null)
				echo '<br>';
		}
	}


	public function index()
	{
		$this->display();
	}



	/**
	 * 生成假数据
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-04
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

			$this->genAuth();
			$this->genAdminUser();
			$this->genCat();
			$this->genSto();
			$this->genSKU();
			$this->genCompany();
			$this->genOrder();
			$this->genWarehouse_StockTaking();
			$this->genFinance_IncomeOrExpense();
			$this->genFinance_ReceiptOrPayment();
			// $this->genVisitor();

			$this->show('<br><br>All OK<br><br>');
			$this->show('<a href="'.U("Index/index").'">Index</a><br>');
		}catch(\XYException $e)
		{
			$this->outputMETHOD(__METHOD__,false,$e);
		}
	}
	/**
	 * 生成权限相关的东西
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.15
	 * @date    2016-09-18
	 */
	public function genAuth()
	{
		$this->outputMETHOD(__METHOD__);
      
        $sql_file = 'xy_auth.sql';
        $installer = new InstallController();
        $installer->updateSQL($sql_file);

		//生成规则
		D("AuthRule")->create_(array('name' => 'Home/Carlicense/createCarlicense','title' => 'asd','status' => 1,'condition' => ''));//         1
		D("AuthRule")->create_(array('name' => 'Home/Carlicense/editCarlicense','title' => 'asd','status' => 1,'condition' => ''));//         2
		D("AuthRule")->create_(array('name' => 'Home/Carlicense/deleteCarlicense','title' => 'asd','status' => 1,'condition' => ''));//         3

		D("AuthRule")->create_(array('name' => 'Home/Company/create_','title' => 'asd','status' => 1,'condition' => ''));//         4
		D("AuthRule")->create_(array('name' => 'Home/Company/edit_','title' => 'asd','status' => 1,'condition' => ''));//         5
		D("AuthRule")->create_(array('name' => 'Home/Company/deleteCompany','title' => 'asd','status' => 1,'condition' => ''));//         6
		D("AuthRule")->create_(array('name' => 'Home/Company/queryList','title' => 'asd','status' => 1,'condition' => ''));//         7
		D("AuthRule")->create_(array('name' => 'Home/Company/get_','title' => 'asd','status' => 1,'condition' => ''));//         8
		D("AuthRule")->create_(array('name' => 'Home/Company/getBalance','title' => 'asd','status' => 1,'condition' => ''));//         9
		D("AuthRule")->create_(array('name' => 'Home/Company/queryRemain','title' => 'asd','status' => 1,'condition' => ''));//         10

		D("AuthRule")->create_(array('name' => 'Home/Contact/createContact','title' => 'asd','status' => 1,'condition' => ''));//         11
		D("AuthRule")->create_(array('name' => 'Home/Contact/edit_','title' => 'asd','status' => 1,'condition' => ''));//         12
		D("AuthRule")->create_(array('name' => 'Home/Contact/deleteContact','title' => 'asd','status' => 1,'condition' => ''));//         13
		D("AuthRule")->create_(array('name' => 'Home/Contact/queryList','title' => 'asd','status' => 1,'condition' => ''));//         14

		D("AuthRule")->create_(array('name' => 'Home/EverydaySummarySheet/summarySheet','title' => 'asd','status' => 1,'condition' => ''));//         15
		D("AuthRule")->create_(array('name' => 'Home/EverydaySummarySheet/queryList','title' => 'asd','status' => 1,'condition' => ''));//         16

		D("AuthRule")->create_(array('name' => 'Home/Feedback/create_','title' => 'asd','status' => 1,'condition' => ''));//         17

		D("AuthRule")->create_(array('name' => 'Home/Finance/createReceiptOrPayment','title' => 'asd','status' => 1,'condition' => ''));//         18
		D("AuthRule")->create_(array('name' => 'Home/Finance/createReceiptOrPaymentDraft','title' => 'asd','status' => 1,'condition' => ''));//         19
		D("AuthRule")->create_(array('name' => 'Home/Finance/createIncomeOrExpense','title' => 'asd','status' => 1,'condition' => ''));//         20
		D("AuthRule")->create_(array('name' => 'Home/Finance/createIncomeOrExpenseDraft','title' => 'asd','status' => 1,'condition' => ''));//         21
		D("AuthRule")->create_(array('name' => 'Home/Finance/editDocument','title' => 'asd','status' => 1,'condition' => ''));//         22
		D("AuthRule")->create_(array('name' => 'Home/Finance/queryOneDocument','title' => 'asd','status' => 1,'condition' => ''));//         23
		D("AuthRule")->create_(array('name' => 'Home/Finance/deleteDraft','title' => 'asd','status' => 1,'condition' => ''));//         24

		D("AuthRule")->create_(array('name' => 'Home/Good/createCat','title' => 'asd','status' => 1,'condition' => ''));//         25
		D("AuthRule")->create_(array('name' => 'Home/Good/editCat','title' => 'asd','status' => 1,'condition' => ''));//         26
		D("AuthRule")->create_(array('name' => 'Home/Good/deleteCat','title' => 'asd','status' => 1,'condition' => ''));//         27
		D("AuthRule")->create_(array('name' => 'Home/Good/queryCat','title' => 'asd','status' => 1,'condition' => ''));//         28
		D("AuthRule")->create_(array('name' => 'Home/Good/createSKU','title' => 'asd','status' => 1,'condition' => ''));//         29
		D("AuthRule")->create_(array('name' => 'Home/Good/editSKU','title' => 'asd','status' => 1,'condition' => ''));//         30
		D("AuthRule")->create_(array('name' => 'Home/Good/editSPUSKU','title' => 'asd','status' => 1,'condition' => ''));//         31
		D("AuthRule")->create_(array('name' => 'Home/Good/deleteSKU','title' => 'asd','status' => 1,'condition' => ''));//         32
		D("AuthRule")->create_(array('name' => 'Home/Good/querySkU','title' => 'asd','status' => 1,'condition' => ''));//         33
		D("AuthRule")->create_(array('name' => 'Home/Good/editSPU','title' => 'asd','status' => 1,'condition' => ''));//         34
		D("AuthRule")->create_(array('name' => 'Home/Good/deleteSPU','title' => 'asd','status' => 1,'condition' => ''));//         35

		D("AuthRule")->create_(array('name' => 'Home/Order/createOrder','title' => 'asd','status' => 1,'condition' => ''));//         36
		D("AuthRule")->create_(array('name' => 'Home/Order/editOrder','title' => 'asd','status' => 1,'condition' => ''));//         37
		D("AuthRule")->create_(array('name' => 'Home/Order/setOrderStatus','title' => 'asd','status' => 1,'condition' => ''));//         38
		D("AuthRule")->create_(array('name' => 'Home/Order/get_','title' => 'asd','status' => 1,'condition' => ''));//         39
		D("AuthRule")->create_(array('name' => 'Home/Order/mGet_','title' => 'asd','status' => 1,'condition' => ''));//         40
		D("AuthRule")->create_(array('name' => 'Home/Order/createDraft','title' => 'asd','status' => 1,'condition' => ''));//         41
		D("AuthRule")->create_(array('name' => 'Home/Order/deleteDraft','title' => 'asd','status' => 1,'condition' => ''));//         42

		D("AuthRule")->create_(array('name' => 'Home/Parkaddress/create_','title' => 'asd','status' => 1,'condition' => ''));//         43
		D("AuthRule")->create_(array('name' => 'Home/Parkaddress/edit_','title' => 'asd','status' => 1,'condition' => ''));//         44
		D("AuthRule")->create_(array('name' => 'Home/Parkaddress/delete_','title' => 'asd','status' => 1,'condition' => ''));//         45
		D("AuthRule")->create_(array('name' => 'Home/Parkaddress/queryList','title' => 'asd','status' => 1,'condition' => ''));//         46

		D("AuthRule")->create_(array('name' => 'Home/Phonenum/createPhonenum','title' => 'asd','status' => 1,'condition' => ''));//         47
		D("AuthRule")->create_(array('name' => 'Home/Phonenum/editPhonenum','title' => 'asd','status' => 1,'condition' => ''));//         48
		D("AuthRule")->create_(array('name' => 'Home/Phonenum/deletePhonenum','title' => 'asd','status' => 1,'condition' => ''));//         49

		D("AuthRule")->create_(array('name' => 'Home/Query/query_','title' => '查询9种单据的统一接口','status' => 1,'condition' => ''));//         50
		D("AuthRule")->create_(array('name' => 'Home/Query/queryDraft','title' => 'asd','status' => 1,'condition' => ''));//         51
		D("AuthRule")->create_(array('name' => 'Home/Query/search','title' => 'asd','status' => 1,'condition' => ''));//         52
		D("AuthRule")->create_(array('name' => 'Home/Query/dashboard','title' => 'asd','status' => 1,'condition' => ''));//         53

		D("AuthRule")->create_(array('name' => 'Home/User/register','title' => 'asd','status' => 1,'condition' => ''));//         54
		D("AuthRule")->create_(array('name' => 'Home/User/logout','title' => 'asd','status' => 1,'condition' => ''));//         55
		D("AuthRule")->create_(array('name' => 'Home/User/editShopInfo','title' => 'asd','status' => 1,'condition' => ''));//         56
		D("AuthRule")->create_(array('name' => 'Home/User/getList','title' => '查询店铺的所有人员信息','status' => 1,'condition' => ''));//         57
		D("AuthRule")->create_(array('name' => 'Home/User/getUserInfo_shopName','title' => '得到企业名称','status' => 1,'condition' => ''));//         58
		D("AuthRule")->create_(array('name' => 'Home/User/getShopInfo','title' => '得到企业信息','status' => 1,'condition' => ''));//         59
		D("AuthRule")->create_(array('name' => 'Home/User/getUserInfo_name','title' => '获得uid为$uid的用户的真实姓名','status' => 1,'condition' => ''));//         60
		D("AuthRule")->create_(array('name' => 'Home/User/setGTClientID','title' => 'asd','status' => 1,'condition' => ''));//         61
		D("AuthRule")->create_(array('name' => 'Home/User/editUserInfo','title' => '修改用户信息','status' => 1,'condition' => ''));//         62

		D("AuthRule")->create_(array('name' => 'Home/Warehouse/createStockTakingOrRequisition','title' => 'asd','status' => 1,'condition' => ''));//         63
		D("AuthRule")->create_(array('name' => 'Home/Warehouse/createStockTakingOrRequisitionDraft','title' => 'asd','status' => 1,'condition' => ''));//         64
		D("AuthRule")->create_(array('name' => 'Home/Warehouse/get_','title' => 'asd','status' => 1,'condition' => ''));//         65
		D("AuthRule")->create_(array('name' => 'Home/Warehouse/edit_','title' => 'asd','status' => 1,'condition' => ''));//         66
		D("AuthRule")->create_(array('name' => 'Home/Warehouse/deleteDraft','title' => 'asd','status' => 1,'condition' => ''));//         67

		D("AuthRule")->create_(array('name' => 'Home/Index/index','title' => 'asd','status' => 1,'condition' => ''));//         68
		D("AuthRule")->create_(array('name' => 'Home/Index/watchLog','title' => 'asd','status' => 1,'condition' => ''));//         69
		D("AuthRule")->create_(array('name' => 'Home/Index/deleteLog','title' => 'asd','status' => 1,'condition' => ''));//         70
		D("AuthRule")->create_(array('name' => 'Home/Generator/generator','title' => 'asd','status' => 1,'condition' => ''));//         71
		D("AuthRule")->create_(array('name' => 'Home/Util/index','title' => 'asd','status' => 1,'condition' => ''));//         72
		D("AuthRule")->create_(array('name' => 'Home/Util/testjiyan','title' => 'asd','status' => 1,'condition' => ''));//         73
		D("AuthRule")->create_(array('name' => 'Home/Util/checkJiyanVerifyCode','title' => 'asd','status' => 1,'condition' => ''));//         74
		D("AuthRule")->create_(array('name' => 'Home/User/index','title' => 'asd','status' => 1,'condition' => ''));//         75
		D("AuthRule")->create_(array('name' => 'Home/Good/index','title' => 'asd','status' => 1,'condition' => ''));//         76
		D("AuthRule")->create_(array('name' => 'Home/Company/index','title' => 'asd','status' => 1,'condition' => ''));//         77
		D("AuthRule")->create_(array('name' => 'Home/Contact/index','title' => 'asd','status' => 1,'condition' => ''));//         78
		D("AuthRule")->create_(array('name' => 'Home/Phonenum/index','title' => 'asd','status' => 1,'condition' => ''));//         79
		D("AuthRule")->create_(array('name' => 'Home/Carlicense/index','title' => 'asd','status' => 1,'condition' => ''));//         80
		D("AuthRule")->create_(array('name' => 'Home/Parkaddress/index','title' => 'asd','status' => 1,'condition' => ''));//         81
		D("AuthRule")->create_(array('name' => 'Home/Order/index','title' => 'asd','status' => 1,'condition' => ''));//         82
		D("AuthRule")->create_(array('name' => 'Home/Warehouse/index','title' => 'asd','status' => 1,'condition' => ''));//         83
		D("AuthRule")->create_(array('name' => 'Home/Finance/index','title' => 'asd','status' => 1,'condition' => ''));//         84
		D("AuthRule")->create_(array('name' => 'Home/Query/index','title' => 'asd','status' => 1,'condition' => ''));//         85
		D("AuthRule")->create_(array('name' => 'Home/EverydaySummarySheet/index','title' => 'asd','status' => 1,'condition' => ''));//         86
		D("AuthRule")->create_(array('name' => 'Home/Feedback/index','title' => 'asd','status' => 1,'condition' => ''));//         87
		D("AuthRule")->create_(array('name' => 'Home/Auth/index','title' => 'asd','status' => 1,'condition' => ''));//         88
		D("AuthRule")->create_(array('name' => 'Home/Config/index','title' => 'asd','status' => 1,'condition' => ''));//         89

		D("AuthRule")->create_(array('name' => 'Home/Login/admin_register','title' => '补漏','status' => 1,'condition' => ''));//         90
		D("AuthRule")->create_(array('name' => 'Home/Config/unlockShop','title' => '补漏','status' => 1,'condition' => ''));//         91
		D("AuthRule")->create_(array('name' => 'Home/Config/lockShop','title' => '补漏','status' => 1,'condition' => ''));//         92
		D("AuthRule")->create_(array('name' => 'Home/Config/getLockShopStatus','title' => '补漏','status' => 1,'condition' => ''));//         93
		D("AuthRule")->create_(array('name' => 'Home/Config/setShopConfig','title' => '补漏','status' => 1,'condition' => ''));//         94
		D("AuthRule")->create_(array('name' => 'Home/Config/getShopConfig','title' => '补漏','status' => 1,'condition' => ''));//         95
		D("AuthRule")->create_(array('name' => 'Home/Good/querySPU','title' => '补漏','status' => 1,'condition' => ''));//         96
		D("AuthRule")->create_(array('name' => 'Home/Paybill/getPayBillParam','title' => '补漏','status' => 1,'condition' => ''));//         97
		D("AuthRule")->create_(array('name' => 'Home/UserAccount/get_','title' => '补漏','status' => 1,'condition' => ''));//         98

		D("AuthRule")->create_(array('name' => 'Home/Paybill/index','title' => '补漏','status' => 1,'condition' => ''));//         99
		D("AuthRule")->create_(array('name' => 'Home/UserAccount/index','title' => '补漏','status' => 1,'condition' => ''));//         100
		D("AuthRule")->create_(array('name' => 'Home/Install/auto_updateSQL_generator','title' => '补漏','status' => 1,'condition' => ''));//         101
		D("AuthRule")->create_(array('name' => 'Home/RealDataGenerator/generator','title' => '补漏','status' => 1,'condition' => ''));//         102

		D("AuthRule")->create_(array('name' => 'Home/Company/requestStatementOfAccount','title' => '补漏','status' => 1,'condition' => ''));//         103
		D("AuthRule")->create_(array('name' => 'Home/SmsDetails/SendSMSStatementOfAccount','title' => '补漏','status' => 1,'condition' => ''));//         104
		D("AuthRule")->create_(array('name' => 'Home/SmsDetails/index','title' => '补漏','status' => 1,'condition' => ''));//         105

		D("AuthRule")->create_(array('name' => 'Home/PaymentDetails/getPaybillAndSmsDetail','title' => '补漏','status' => 1,'condition' => ''));//         106
		D("AuthRule")->create_(array('name' => 'Home/PaymentDetails/getMoneyTimeSave','title' => '补漏','status' => 1,'condition' => ''));//         107
		D("AuthRule")->create_(array('name' => 'Home/RealDataGenerator/deleteAdminInfoByAdminId','title' => '补漏','status' => 1,'condition' => ''));//         108
		D("AuthRule")->create_(array('name' => 'Home/PaymentDetails/index','title' => '补漏','status' => 1,'condition' => ''));//         109
		D("AuthRule")->create_(array('name' => 'Home/PrintTemplate/index','title' => '补漏','status' => 1,'condition' => ''));//         110
		D("AuthRule")->create_(array('name' => 'Home/PrintTemplate/create_','title' => '补漏','status' => 1,'condition' => ''));//         111
		D("AuthRule")->create_(array('name' => 'Home/PrintTemplate/get_','title' => '补漏','status' => 1,'condition' => ''));//         112
		D("AuthRule")->create_(array('name' => 'Home/Order/createAdjustAROrAP','title' => '补漏','status' => 1,'condition' => ''));//         113
		D("AuthRule")->create_(array('name' => 'Home/Query/saleSummary','title' => '补漏','status' => 1,'condition' => ''));//         114
		D("AuthRule")->create_(array('name' => 'Home/Other/loadExcelFrom_ForCompany','title' => '补漏','status' => 1,'condition' => ''));//         115
		D("AuthRule")->create_(array('name' => 'Home/Other/loadExcelFrom_ForSku','title' => '补漏','status' => 1,'condition' => ''));//         116
		D("AuthRule")->create_(array('name' => 'Home/Other/index','title' => '补漏','status' => 1,'condition' => ''));//         117
		D("AuthRule")->create_(array('name' => 'Home/Good/getThisCustomerLastPrice','title' => '补漏','status' => 1,'condition' => ''));//         118
		D("AuthRule")->create_(array('name' => 'Home/User/getOptionArray','title' => '补漏','status' => 1,'condition' => ''));//         119
		D("AuthRule")->create_(array('name' => 'Home/User/setOptionArray','title' => '补漏','status' => 1,'condition' => ''));//         120
		D("AuthRule")->create_(array('name' => 'Home/PrintTemplate/WBXcreate_','title' => '补漏','status' => 1,'condition' => ''));//         121
		D("AuthRule")->create_(array('name' => 'Home/PrintTemplate/WBXget_','title' => '补漏','status' => 1,'condition' => ''));//         122
		D("AuthRule")->create_(array('name' => 'Home/Login/index','title' => '补漏','status' => 1,'condition' => ''));//         123
		D("AuthRule")->create_(array('name' => 'Home/Query/skuSummary','title' => '补漏','status' => 1,'condition' => ''));//         124
		D("AuthRule")->create_(array('name' => 'Home/Query/skuChart','title' => '补漏','status' => 1,'condition' => ''));//         125
        D("AuthRule")->create_(array('name' => 'Home/Query/backUpEveryDay','title' => 'excel备份每日单据','status' => 1,'condition' => ''));//         126
        D("AuthRule")->create_(array('name' => 'Home/Company/requestStatementOfWechatAccount','title' => '微信对账单','status' => 1,'condition' => ''));//         127
        D("AuthRule")->create_(array('name' => 'Home/Company/wechatAccount','title' => '微信对账单','status' => 1,'condition' => ''));//         128
        D("AuthRule")->create_(array('name' => 'Home/Good/get_','title' => '查询单个sku信息','status' => 1,'condition' => ''));//         129
        D("AuthRule")->create_(array('name' => 'Home/Account/index','title' => '内账相关接口','status' => 1,'condition' => ''));//         130
        D("AuthRule")->create_(array('name' => 'Home/Account/createAccount','title' => '创建账户','status' => 1,'condition' => ''));//         131
        D("AuthRule")->create_(array('name' => 'Home/Account/cash_Proposal','title' => '提现接口','status' => 1,'condition' => ''));//         132
        D("AuthRule")->create_(array('name' => 'Home/Account/transfer_Accounts','title' => '转账接口','status' => 1,'condition' => ''));//         133
        D("AuthRule")->create_(array('name' => 'Home/Finance/createFinanceCart','title' => '创建财务账类别','status' => 1,'condition' => ''));//         134
        D("AuthRule")->create_(array('name' => 'Home/Finance/queryFinanceCart','title' => '查询财务账类别','status' => 1,'condition' => ''));//         135
        D("AuthRule")->create_(array('name' => 'Home/Finance/deleteFinanceCart','title' => '删除财务账类别','status' => 1,'condition' => ''));//         136
        D("AuthRule")->create_(array('name' => 'Home/Finance/create_FinanceOrder','title' => '创建银行账财务相关单据','status' => 1,'condition' => ''));//         137
        D("AuthRule")->create_(array('name' => 'Home/Finance/create_FinanceOrderDraft','title' => '创建银行账财务相关单据草稿','status' => 1,'condition' => ''));//         138
        D("AuthRule")->create_(array('name' => 'Home/Finance/financeOrderStatusChange','title' => '银行账单据审核','status' => 1,'condition' => ''));//         139
        D("AuthRule")->create_(array('name' => 'Home/Finance/query_FinanceOrder','title' => '银行账单查询','status' => 1,'condition' => ''));//         140
        D("AuthRule")->create_(array('name' => 'Home/Account/edit_Account','title' => '编辑账户','status' => 1,'condition' => ''));//         141
        D("AuthRule")->create_(array('name' => 'Home/Account/query_account_source','title' => '查询已有账户来源(补漏)','status' => 1,'condition' => ''));//         142
        D("AuthRule")->create_(array('name' => 'Home/Account/query_account','title' => '查询已有账户','status' => 1,'condition' => ''));//         143
        D("AuthRule")->create_(array('name' => 'Home/Account/delete_Account','title' => '删除已有账户','status' => 1,'condition' => ''));//         144
        D("AuthRule")->create_(array('name' => 'Home/UpdateDatabaseFormat/resetRemain','title' => '重置数据库小数点位数','status' => 1,'condition' => ''));//         145
        D("AuthRule")->create_(array('name' => 'Home/Storage/index','title' => 'asd','status' => 1,'condition' => ''));//         146
        D("AuthRule")->create_(array('name' => 'Home/Storage/updateSto','title' => 'asd','status' => 1,'condition' => ''));//         147
        D("AuthRule")->create_(array('name' => 'Home/Storage/deleteSto','title' => 'asd','status' => 1,'condition' => ''));//        148
        D("AuthRule")->create_(array('name' => 'Home/Storage/querySto','title' => 'asd','status' => 1,'condition' => ''));//       149
        D("AuthRule")->create_(array('name' => 'Home/Storage/get_','title' => 'asd','status' => 1,'condition' => ''));//           150
        D("AuthRule")->create_(array('name' => 'Home/Storage/deleteSkuSto','title' => 'asd','status' => 1,'condition' => ''));//   151
        D("AuthRule")->create_(array('name' => 'Home/SkuStorage/index','title' => 'asd','status' => 1,'condition' => ''));//       152
        D("AuthRule")->create_(array('name' => 'Home/SkuStorage/querySkuSto','title' => 'asd','status' => 1,'condition' => ''));// 153
        D("AuthRule")->create_(array('name' => 'Home/Good/updateSKU','title' => '生成或修改SKU','status' => 1,'condition' => ''));//     154
        D("AuthRule")->create_(array('name' => 'Home/UpdateDatabaseFormat/initializeStoSku','title' => '多仓多库初始化','status' => 1,'condition' => ''));//         155
        D("AuthRule")->create_(array('name' => 'Home/Finance/invoicePoolSummary','title' => '统计需要填补的金额','status' => 1,'condition' => ''));//         156
        D("AuthRule")->create_(array('name' => 'Home/SkuStorage/skuStoSummary','title' => '仓库总库存量、库存金额统计统计','status' => 1,'condition' => ''));// 157
        D("AuthRule")->create_(array('name' => 'Home/SkuStorage/get_','title' => '获取单个sku数据','status' => 1,'condition' => ''));// 158
        D("AuthRule")->create_(array('name' => 'Home/Query/purchaseSummary','title' => '采购汇总','status' => 1,'condition' => ''));//         159
        D("AuthRule")->create_(array('name' => 'Home/Department/createDepartment','title' => '创建部门','status' => 1,'condition' => ''));//         160
        D("AuthRule")->create_(array('name' => 'Home/Department/editDepartment','title' => '创建部门','status' => 1,'condition' => ''));//         161
        D("AuthRule")->create_(array('name' => 'Home/Department/getDepartment','title' => '创建部门','status' => 1,'condition' => ''));//         162
        D("AuthRule")->create_(array('name' => 'Home/Department/deleteDepartment','title' => '创建部门','status' => 1,'condition' => ''));//         163
        D("AuthRule")->create_(array('name' => 'Home/Finance/getExpenditureStatistics','title' => '按时间统计支出金额','status' => 1,'condition' => ''));//         164
        D("AuthRule")->create_(array('name' => 'Home/Finance/groupStatusChange','title' => '批量审核','status' => 1,'condition' => ''));//         165
        D("AuthRule")->create_(array('name' => 'Home/FaceRec/index','title' => ' ','status' => 1,'condition' => ''));//         166
        D("AuthRule")->create_(array('name' => 'Home/FaceRec/upload_pic','title' => '上传照片','status' => 1,'condition' => ''));//         167
        D("AuthRule")->create_(array('name' => 'Home/FaceRec/comparePhoto','title' => '对比照片','status' => 1,'condition' => ''));//         168
        D("AuthRule")->create_(array('name' => 'Home/CusList/getCusList','title' => '获取访客列表','status' => 1,'condition' => ''));//         169
        D("AuthRule")->create_(array('name' => 'Home/CusList/createRecord','title' => '更新访客列表数据','status' => 1,'condition' => ''));//         170
        D("AuthRule")->create_(array('name' => 'Home/QnUpload/index','title' => '七牛index','status' => 1,'condition' => ''));//         171
        D("AuthRule")->create_(array('name' => 'Home/Company/companyTransactionStatistics','title' => '往来单位销售统计','status' => 1,'condition' => ''));//         172
        D("AuthRule")->create_(array('name' => 'Home/Finance/financeDashboard','title' => '首页近三十天收支统计接口','status' => 1,'condition' => ''));//         173
        D("AuthRule")->create_(array('name' => 'Home/Finance/edit_FinanceOrder','title' => '编辑财务单据备注','status' => 1,'condition' => ''));//         174
        D("AuthRule")->create_(array('name' => 'Home/Finance/delete_FinanceOrder','title' => '作废未审核财务单据','status' => 1,'condition' => ''));//         175
        D("AuthRule")->create_(array('name' => 'Home/User/changeRpg','title' => '修改自己的RPG','status' => 1,'condition' => ''));//         176
        D("AuthRule")->create_(array('name' => 'Home/UpdateDatabaseFormat/history_trans_form','title' => '内部接口字段转换','status' => 1,'condition' => ''));//         177
        D("AuthRule")->create_(array('name' => 'Home/Finance/editFinanceCart','title' => '修改类别','status' => 1,'condition' => ''));//         178

        //添加用户组
		D("AuthGroup")->create_(array(
				'title' => '创建者',
				'status' => 1,
				'rules' => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,90,91,92,93,94,95,96,97,98,103,104,106,107,111,112,113,114,115,116,118,119,120,124,125,126,127,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,147,148,149,153,154,156,158,160,161,162,163,164,165,171,172,173,174,175,176,178),
			));
		D("AuthGroup")->create_(array(
				'title' => '销售',
				'status' => 1,
				'rules' => array(1,2,3,4,5,7,8,9,10,11,12,13,14,17,18,19,20,21,22,23,24,28,33,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,55,57,58,59,60,62,63,64,65,66,67,91,92,93,96,97,98,103,104,106,107,112,113,118,119,120,124,125,126,127,129,130,131,149,153,158,171,172,173,174,175),
			));
		D("AuthGroup")->create_(array(
				'title' => '库管',
				'status' => 1,
				'rules' => array(17,28,33,36,37,38,39,40,50,51,52,55,57,58,59,60,61,62,63,64,65,66,67,91,92,93,97,98,106,107,111,112,113,119,120,124,125,126,127,129,142,147,148,149,153,158,171,172,173,174,175),
			));
		D("AuthGroup")->create_(array(
				'title' => '股东',
				'status' => 1,
				'rules' => array(7,8,9,10,14,15,16,17,23,28,33,39,40,46,50,51,52,53,55,57,58,59,60,62,65,91,92,93,96,97,98,103,104,106,107,112,113,114,118,119,120,124,125,126,129,127,142,149,153,154,158,164,171,172,173,174,175),
			));
		D("AuthGroup")->create_(array(
				'title' => '自己人',
				'status' => 1,
				'rules' => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,71,72,73,74,75,76,77,78,79,80,81,82,83,84,85,86,87,88,89,90,91,92,93,94,95,96,97,98,99,100,101,102,103,104,105,106,107,108,109,110,111,112,113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,145,146,147,148,149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165,166,167,168,169,170,171,172,173,174,175,176,177,178),
			));
		D("AuthGroup")->create_(array(
				'title' => '体验用户',
				'status' => 1,
				'rules' => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,90,91,92,93,94,95,96,97,98,102,103,106,107,111,112,113,114,118,119,120,124,125,127,129,130,131,132,133,134,135,136,137,138,142,147,148,149,153,154,158,160,161,162,163,165,171,172,173,174,175,176),
			));
		D("AuthGroup")->create_(array(
				'title' => '管理员',
				'status' => 1,
				'rules' => array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,90,91,92,93,94,95,96,97,98,103,104,106,107,111,112,113,114,115,116,118,119,120,124,125,126,127,129,130,131,132,133,134,135,136,137,138,142,147,148,149,150,151,153,154,158,160,161,162,163,164,171,172,173,174,175),
			));
        D("AuthGroup")->create_(array(
            'title' => '易企记创建者用户用户',
            'status' => 1,
            'rules' => array(17,24,28,50,51,53,54,55,56,57,58,59,60,61,62,84,90,93,94,95,98,99,119,120,123,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,156,160,161,162,163,164,165,166,171,172,173,174,175,176,178),
        ));
        D("AuthGroup")->create_(array(
            'title' => '易企记老板',
            'status' => 1,
            'rules' => array(17,24,28,50,51,53,54,55,56,57,58,59,60,61,62,84,90,93,94,95,98,99,119,120,123,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,156,160,161,162,163,164,165,166,171,172,173,174,175,178),
        ));
        D("AuthGroup")->create_(array(
            'title' => '易企记财务',
            'status' => 1,
            'rules' => array(17,24,28,50,51,53,54,55,56,57,58,59,60,61,62,84,90,93,95,98,99,119,120,123,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,156,164,165,166,171,172,173,174,175,178),
        ));
        D("AuthGroup")->create_(array(
            'title' => '易企记员工',
            'status' => 1,
            'rules' => array(17,24,28,50,51,53,54,55,57,58,59,60,61,62,84,90,93,94,95,98,99,119,120,123,129,130,131,132,133,134,135,136,137,138,139,140,141,142,143,144,162,164,166,171,172,173,174,175),
        ));
        D("AuthGroup")->create_(array(
            'title' => '空白用户',
            'status' => 1,
            'rules' => array(55,56,90,94,95,176),
        ));
        $this->outputMETHOD(__METHOD__,true);
	}

	public function genAdminUser()
	{
		$this->outputMETHOD(__METHOD__);


		$dbUser = D('User');
		if (is_login() > 0)
			$dbUser->logout();

		//管理员用户1
		$tmpInfo = $dbUser->admin_register(array(
				'username'   => C('ROOT_USER_MOBILE'),
				'password'   => md5(C('passwordMD5Prefix').'123456'),
				'email'      => $this->faker->email,
				'where_know' => '我自己开发的= =',
			),false,true);
//		$changerpg = $dbUser->changeRpg(array('rpg_mode' => 10001));
		$this->shop1_admin_uid = $tmpInfo['uid'];
		D('Config')->setUserMember(1,3);//设置管理员级别最高，需要Controller重新加载
		A('Home')->_initialize();//Model调用的登录，登陆完后需要加载C()的config配置
		$dbUser->editUserInfo(array(
				'uid'  => $this->shop1_admin_uid,
				'name' => '跃迁科技',
				'qq'   => '1744357320',
			),false);
		$dbUser->editShopInfo(array(
				'shop_name' => "跃迁科技",
				'industry'  => '互联网',
				'province'  => '浙江',
				'city'      => '杭州',
			));
		$dbUser->setGTClientID(array(
				'GTClientID'=>'a17e6ef66dd5c8cb49f73f96fae71f97',
			));

		//普通用户1，zxb
		$this->shop1_user_uid = $dbUser->register(array(
				'username' => '15029297212',
				'password' => md5(C('passwordMD5Prefix').'123456'),
				'status'   => 1,
				'name'     => '跃迁科技手机',
				'rpg'      => 3,
				'mobile'   => '15029297212',
				'qq'       => '1744357320',
				'email'    => $this->faker->email,
			));

		//android test user
		$this->shop1_user_uid = $dbUser->register(array(
				'username' => '15012345678',
				'password' => md5(C('passwordMD5Prefix').'123456'),
				'status'   => 1,
				'name'     => '跃迁科技测试账户',
				'rpg'      => 3,
				'mobile'   => '15012345678',
				'qq'       => '555555555',
				'email'    => $this->faker->email,
			));


		//管理员用户1 logout		
		$dbUser->logout();
		//普通用户1 login
		$dbUser->login(array(
				'admin'    => C('ROOT_USER_MOBILE'),
				'username' => '15029297212',
				'password' => md5(C('passwordMD5Prefix').'123456'),
				'mode'     => 1,
				'type'     => 1,
			));
		// A('Home')->_initialize();//Model调用的登录，登陆完后需要加载C()的config配置.因为该用户没有权限调用这个，而且也不需要，所以注释掉
		$dbUser->setGTClientID(array(
				'GTClientID'=>'a17e6ef66dd5c8cb49f73f96fae71f97',
			));
		//普通用户1 logout
		$dbUser->logout();


		//管理员用户1 login
		$dbUser->login(array(
				'username' => C('ROOT_USER_MOBILE'),
				'password' => md5(C('passwordMD5Prefix').'123456'),
				'mode'     => 2,
				'type'     => 1,
			));
		A('Home')->_initialize();//Model调用的登录，登陆完后需要加载C()的config配置

		// if (!IS_CLI)
		// 	echo '登录【管理员用户1】的用户成功<br>';

		$this->outputMETHOD(__METHOD__,true);
	}




	/**
	 * 生成Cat数据
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-08
	 */
	public function genCat()
	{
		$this->outputMETHOD(__METHOD__);


		$dbCat = D('Cat');

		$this->catCount = $this->faker->numberBetween($min = 2,$max = 10);
		for ($i = 1; $i <= $this->catCount; $i++)
		{
			$cat_name = $this->faker->unique()->state();
			$dbCat->createCat(array(
					'cat_name'  => $cat_name,
					'cat_index' => $this->faker->numberBetween($min = 1,$max = 2),
					'status'    => 1,//todo：暂不考虑状态为0
				),false);
			// if (!IS_CLI)
				// echo 'createCat:'.$cat_name.'<br>';
		}

		$this->outputMETHOD(__METHOD__,true);
	}

	/**
	 * 生成Storage数据
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.11
	 * @date  2017-06-05
	 */
	public function genSto()
	{
		$this->outputMETHOD(__METHOD__);

		$dbStorage = D('Storage');
		$this->stoCount = $this->faker->numberBetween($min=2,$max=5);
		for ($i = 1;$i <= $this->stoCount;$i++)
		{

			$sto_name = $this->faker->unique()->area();
			$dbStorage ->updateSto(array(
				'sto_name' =>$sto_name,
				'sto_index' => 2,
				'status' => 1,
				),false,true);
		}

		$this->outputMETHOD(__METHOD__,true);
	}
	

	/**
	 * 生成SKU数据.
	 *
	 * 注意：依赖genCat提供的catCount和genSto提供的stoCount
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-08
	 */
	public function genSKU()
	{
		$this->outputMETHOD(__METHOD__);

		$dbSku = D('Sku');

		for ($i = 1; $i <= $this->faker->numberBetween($min = 10,$max = 20); $i++)
		{
			log_("num",$this->faker->numberBetween($min = 10,$max = 20),$this);
			$skuData = null;
			$status = null;
			$sku_sto_index = null;
			$status = array(0,1);
			$sku_sto_index = array(1,2,3,4,5);
			$spuName = $this->faker->unique()->colorName();
			$tmpSkuCount = $this->faker->numberBetween($min = 1,$max = 5);
			for ($j = 0;$j<$tmpSkuCount;$j++)
			{
				$skuStoData[] = array(
					'sku_id'	 => 0,
					'sku_storage_id' => 0,
					'stock'  	 => $this->faker->numberBetween($min = 1000,$max = 5000),
					'spec_name'  => $this->faker->unique()->numerify('##只装'),
					'unit_price' => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 100, $max = 700)),
					"sku_sto_index"=>$this->faker->numberBetween($min = 1,$max = 2),
					"sku_sto_status"=> 1,
					);
			}


			// for ($j = 0; $j < $tmpSkuCount; $j++)
			// {
				$tmpData = array(
					array(
						'sto_id'  => 1,
						'skuStoData' => $skuStoData							
						),
					array(
						'sto_id'  => 2,
						'skuStoData' => $skuStoData							
						)
					);
			// }
			$dbSku->updateSKU(array(
					'cat_id'     => $this->faker->numberBetween($min = 2,$max = $this->catCount),
					'spu_name'   => $spuName,
					'spu_index'  => $this->faker->numberBetween($min = 1,$max = 2),
					'qcode'      => D('Other')->getPinYin($spuName),
					'spu_status' => 1,//todo：暂不考虑状态为0
					'skuStoData' => array('data'=>$tmpData),
				),false);
			// if (!IS_CLI)
				// echo 'createSPU:'.$spuName.'<br>';
			$this->skuCount += $tmpSkuCount;
		}

		$this->outputMETHOD(__METHOD__,true);
	}
	



	/**
	 * 生成Company数据.
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-08
	 */
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
			$dbCompany->create_(array(
					'name'         => $companyName,
					'qcode'        => D('Other')->getPinYin($companyName),
					'address'      => $this->faker->address(),
					'remark'       => $this->faker->catchPhrase(),
					'init_payable' => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1000, $max = 70000)),
					'status'       => 1,//todo：暂不考虑状态为0
					'contact'      => $contactData,
				),false);
			// if (!IS_CLI)
				// echo 'createCompany:'.$companyName.'<br>';
		}

		$this->outputMETHOD(__METHOD__,true);
	}



	/**
	 * 生成Order数据.
	 *
	 * 依赖genSku提供的skuCount、依赖genCompany提供的cidCount、genSto提供的stoCount
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-08
	 */
	public function genOrder()
	{
		$this->outputMETHOD(__METHOD__);


		$dbOrder = D('Order');
		$dbSku = D('Sku');
		$dbSkuSto = D('SkuStorage');
		$dbSto = D('Storage');
		$stoList = $dbSkuSto->distinct(true)->getField('sto_id',true);
		$stoInfo = $dbSto->getField('sto_id,sto_name');
		$maxIndex = count($stoList)-1;
		log_("stoList",$stoList,$this);
		for ($i = 0; $i < $this->faker->numberBetween($min = 2,$max = 5); $i++)
		{
			$cartData = null;
			$tmpStoID = null;
			$tmpStoID = $stoList[$this->faker->numberBetween($min=0,$max = $maxIndex)];
			$tmpStoName = $stoInfo[$tmpStoID];
			$tmpSkuStoList = $dbSkuSto->where(array('sto_id'=>$tmpStoID,'admin_uid'=>getAdminUid()))->select();
			$tmpSkuIndexCount = count($tmpSkuStoList)-1;
			for ($j = 0; $j < $this->faker->numberBetween($min = 1,$max = 1 ); $j++)
			{
				$tmpSkuID   = null;
				$tmpInfo    = null;

				$tmpIndex   = $this->faker->numberBetween($min = 0,$max = $tmpSkuIndexCount );
				$tmpSkuID   = $tmpSkuStoList[$tmpIndex]['sku_id'];

				$tmpInfo    = $dbSku->get_(array('sku_id'=>$tmpSkuID));
				$cartData[] = array(
						'sku_id'    => $tmpSkuID,
						'spu_name'  => $tmpInfo['spu_name'],
						'spec_name' => $tmpInfo['spec_name'],
						'quantity'  => $this->faker->numberBetween($min = 1,$max = 50),
						'unitPrice' => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 500)), 
					);
			}
			$statusArray = array(4,5,100);
			$status = $statusArray[$this->faker->numberBetween($min = 0,$max = 2)];
			if ($status === 100)
			{
				
				$dbOrder->createDraft(array(
						'class'        => $this->faker->numberBetween($min = 1,$max = 4),
						'cid'          => $this->faker->numberBetween($min = 1,$max = $this->cidCount),
						'sto_id'       => $tmpStoID,
						'contact_name' => $this->faker->name(),
						'mobile'       => $this->faker->phoneNumber(),
						'park_address' => $this->faker->address(),
						'car_license'  => strtoupper($this->faker->bothify('浙??####')),
						'off'          => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 99)),
						'cash'         => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 999999)),
						'bank'         => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 500000)),
						'online_pay'   => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 200000)),
						'remark'       => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'status'       => $status,
						'freight'	   => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 2000)),
						'is_calculated'=> $this->faker->numberBetween($min = 0,$max = 1),
						'cart'         => array('data'=>$cartData),
					),false);
			}
			else
			{
				$dbOrder->createOrder(array(
						'class'        => $this->faker->numberBetween($min = 1,$max = 4),
						'cid'          => $this->faker->numberBetween($min = 1,$max = $this->cidCount),
						'sto_id'       => $tmpStoID,
						'contact_name' => $this->faker->name(),
						'mobile'       => $this->faker->phoneNumber(),
						'park_address' => $this->faker->address(),
						'car_license'  => strtoupper($this->faker->bothify('浙??####')),
						'off'          => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 99)),
						'cash'         => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 999999)),
						'bank'         => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 500000)),
						'online_pay'   => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 200000)),
						'remark'       => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'status'       => $status,
						'freight'	   => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 2000)),
						'is_calculated'=> $this->faker->numberBetween($min = 0,$max = 1),
						'cart'         => array('data'=>$cartData),
					),false);
			}
			// if (!IS_CLI)
				// echo 'createOrder:'.$i.'<br>';
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
		$dbSkuSto = D('SkuStorage');
		for ($i = 0; $i < $this->faker->numberBetween($min = 5,$max = 20); $i++)
		{
			$cartData = null;

			$tmpStoID = null;
			$tmpStoInfo = null;
			$tmpStoList = null;
			$tmpStoList = $dbSkuSto->where(array('admin_uid'=>getAdminUid()))->field('sku_storage_id,sku_id,sto_id')->select();
			log_("tmpStoList",$tmpStoList,$this);
			$tmpStoID = $tmpStoList[$this->faker->numberBetween($min=1,$max =count($tmpStoList)-1)]['sto_id'];
			$tmpSkuStoList = $dbSkuSto->where(array('sto_id'=>$tmpStoID,'admin_uid'=>getAdminUid()))->select();

			log_("tmpStoID",$tmpStoID,$this);
			log_("tmpSkuStoList",$tmpSkuStoList,$this);
			$tmpSkuIndexCount = count($tmpSkuStoList)-1;
			for ($j = 0; $j < $this->faker->numberBetween($min = 1,$max = 1); $j++)
			{
				$tmpSkuID   = null;
				$tmpIndex   = $this->faker->numberBetween($min = 0,$max = $tmpSkuIndexCount );
				$tmpSkuID   = $tmpSkuStoList[$tmpIndex]['sku_id'];

				$cartData[] = array(
						'sku_id'    => $tmpSkuID,
						'quantity'  => $this->faker->numberBetween($min = 1,$max = 10)
					);
			}
			$statusArray = array(1,100);
			$status = $statusArray[$this->faker->numberBetween($min = 0,$max = 1)];
			if ($status === 100)
			{
				$dbWarehouse->createStockTakingOrRequisitionDraft(array(
						'sto_id'    => $tmpStoID,
						'remark'    => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'check_uid' => $this->shop1_user_uid,
						'class'		=> 53,
						'cart'      => array('data'=>$cartData),
					),false);
			}
			else
			{
				$dbWarehouse->createStockTakingOrRequisition(array(
						'sto_id'    => $tmpStoID,
						'remark'    => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'check_uid' => $this->shop1_user_uid,
						'class'		=> 53,
						'cart'      => array('data'=>$cartData),
					),false,true);
			}
			// if (!IS_CLI)
				// echo 'createWarehouse_StockTaking:'.$i.'<br>';
		}

		$this->outputMETHOD(__METHOD__,true);
	}




	/**
	 * 生成Finance的其他收入单、费用单.
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-08
	 */
	public function genFinance_IncomeOrExpense()
	{
		$this->outputMETHOD(__METHOD__);


		$dbFinance = D('Finance');

		for ($i = 0; $i < $this->faker->numberBetween($min = 5,$max = 20); $i++)
		{
			$tmpName = array('意外所得','红包','其他','工资奖金','租金水电','物流人工','营销广告','资金税费','办公行政','日常杂费');
			$class   = null;
			$name    = null;
			$class   = $this->faker->numberBetween($min = 73,$max = 74);
			if ($class === 73)
				$name = $tmpName[$this->faker->numberBetween($min = 0,$max = 2)];
			else
				$name = $tmpName[$this->faker->numberBetween($min = 2,$max = 9)];

			$statusArray = array(1,100);
			$status = $statusArray[$this->faker->numberBetween($min = 0,$max = 1)];
			if ($status === 100)
			{
				$dbFinance->createIncomeOrExpenseDraft(array(
						'class'      => $class,
						'name'       => $name,
						'cash'       => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 1000)),
						'bank'       => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 1000)),
						'online_pay' => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 1000)),
						'remark'     => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
					),false);
			}
			else
			{
				$dbFinance->createIncomeOrExpense(array(
						'class'      => $class,
						'name'       => $name,
						'cash'       => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 1000)),
						'bank'       => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 1000)),
						'online_pay' => xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = 1000)),
						'remark'     => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
					));
			}
			// if (!IS_CLI)
				// echo 'createFinance_IncomeOrExpense:'.$i.'<br>';
		}

		$this->outputMETHOD(__METHOD__,true);
	}



	/**
	 * 生成Finance的收款单、付款单.
	 *
	 * 依赖genCompany提供的cidCount
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-08
	 */
	public function genFinance_ReceiptOrPayment()
	{
		$this->outputMETHOD(__METHOD__);


		$dbFinance = D('Finance');
		$dbCompany = D('Company');

		for ($i = 0; $i < $this->faker->numberBetween($min = 5,$max = 15); $i++)
		{
			$cartData   = null;
			$tmpCid     = $this->faker->numberBetween($min = 1,$max = $this->cidCount);
			$tmpClass   = $this->faker->numberBetween($min = 71,$max = 72);
			$totalMoney = 0;
			if ($tmpClass === 71)
				$tmpType = 1;
			else
				$tmpType = 2;

			$orderInfo = $dbCompany->queryRemain(array(
					'type' => $tmpType,
					'cid' => $tmpCid,
				));
			if (empty($orderInfo))
				continue;

			$j = 1;
			$tmpNum = $this->faker->numberBetween($min = 1,$max = min(5,count($orderInfo)));//本次还多少个Order的钱
			foreach ($orderInfo as $key => $value)
			{
				if ($value['remain'] < 0)
					$value['remain'] = 0 - $value['remain'];
				$tmpMoney = xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = min(100,$value['remain'])));
				$cartData[] = array(
						'oid'        => $value['oid'],
						'reg_time'   => $value['reg_time'],
						'leave_time' => $value['leave_time'],
						'class'      => $value['class'],
						'status'     => $value['status'],
						'value'      => $value['value'],
						'off'        => $value['off'],
						'receivable' => $value['receivable'],
						'remain'     => $value['remain'],
						'money'      => $tmpMoney,
					);
				$totalMoney += $tmpMoney;
				
				$j++;
				if ($j > $tmpNum)
					break;
			}

			$cash       = 0;
			$bank       = 0;
			$online_pay = 0;
			$cash = xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = min(50,$totalMoney)));
			$totalMoney -= $cash;
			if ($totalMoney > 0)
			{
				$bank       = xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = min(50,$totalMoney)));
				$online_pay = xysub($totalMoney,$bank);
				while($online_pay<0)
				{
						$bank       = xyround($this->faker->randomFloat($nbMaxDecimals = 2, $min = 1, $max = min(50,$totalMoney)));
						$online_pay = xysub($totalMoney,$bank);
				}
			}
			$statusArray = array(1,100);
			$status = $statusArray[$this->faker->numberBetween($min = 0,$max = 1)];
			if ($status === 100)
			{
				$dbFinance->createReceiptOrPaymentDraft(array(
						'class'      => $tmpClass,
						'cid'        => $tmpCid,
						'cash'       => abs($cash),
						'bank'       => abs($bank),
						'online_pay' => abs($online_pay),
						'remark'     => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'cart'       => array('data'=>$cartData),
					),false);
			}
			else
			{
				$dbFinance->createReceiptOrPayment(array(
						'class'      => $tmpClass,
						'cid'        => $tmpCid,
						'cash'       => abs($cash),
						'bank'       => abs($bank),
						'online_pay' => abs($online_pay),
						'remark'     => '这里是备注'.$this->faker->sentence($nbWords = 6, $variableNbWords = true),
						'cart'       => array('data'=>$cartData),
					),false);
			}
			// if (!IS_CLI)
				// echo 'createFinance_ReceiptOrPayment:'.$i.'<br>';
		}

		$this->outputMETHOD(__METHOD__,true);
	}



	/**
	 * 生成体验账户
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.10
	 * @date    2017-05-27
	 */
	public function genVisitor()
	{
		$this->outputMETHOD(__METHOD__);

		A('RealDataGenerator')->generator();

		$this->outputMETHOD(__METHOD__,true);
	}

}