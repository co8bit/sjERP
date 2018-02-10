<?php
namespace Home\Model;
use Think\Model;


/**
 * 订单类Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class OrderModel extends BaseadvModel
{
	// @NOTE 因为apigen不支持const=array，所以只能弃用，用下面这个不安全的，请任何时候都不要更改STATUS_INFO的值：
	// const STATUS_INFO = ['未知','已完成','异常','删除','立即处理','暂缓发货','正在通知库管','已通知库管，库管未确认','库管已打印，但未出库','已出库但未送达','库管已打印单据，但未入库','待审核-暂缓发货','待审核-立即处理'];//状态记录信息   
	private $STATUS_INFO = array('未知','已完成','异常','删除','立即处理','暂缓发货','正在通知库管','已通知库管，库管未确认','库管已打印，但未出库','已出库但未送达','库管已打印单据，但未入库','待审核-暂缓发货','待审核-立即处理');//状态记录信息   

	/* 自动验证 */
	protected $_validate = array(
		array('oid', 'checkDeny_oid', -1001, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//oid不合法
		array('cid', 'checkDeny_cid', -1002, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),//cid不合法
		array('mobile', '0,25', -1004, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //mobile长度不合法
		array('park_address', '0,500', -1005, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //park_address长度不合法
		array('car_license', '0,30', -1006, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //car_license长度不合法
		array('off', 'isNonegativeReal', -1007, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//off不合法
		array('cash', 'isNonegativeReal', -1008, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//cash不合法
		array('bank', 'isNonegativeReal', -1009, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//bank不合法
		array('online_pay', 'isNonegativeReal', -1018, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//在线支付不合法
		array('remark', '0,1000', -1010, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //remark长度不合法
		array('status', array(1,99), -1011, self::EXISTS_VALIDATE, 'between',self::MODEL_BOTH), //status不合法
		array('exceptionNo', array(0,3), -1012, self::EXISTS_VALIDATE, 'between',self::MODEL_BOTH), //exceptionNo不合法
		array('exception', '0,500', -1013, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //exception长度不合法
		array('class', array(1,4), -1015, self::EXISTS_VALIDATE, 'between',self::MODEL_BOTH), //class不合法
		array('contact_name', '1,45', -1017, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //联系人名称长度不合法
				//注：cid并不需要验证admin_uid所属，因为getBalanceLock()里面搜索的时候是加了条件admin_uid的
		

		//MODEL_CREATE_DRAFT
		array('oid', 'checkDeny_oid', -1001, self::EXISTS_VALIDATE, 'callback',self::MODEL_CREATE_DRAFT),//oid不合法
		array('cid', 'checkDeny_cid', -1002, self::EXISTS_VALIDATE, 'callback',self::MODEL_CREATE_DRAFT),//cid不合法
		array('mobile', '0,25', -1004, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //mobile长度不合法
		array('park_address', '0,500', -1005, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //park_address长度不合法
		array('car_license', '0,30', -1006, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //car_license长度不合法
		array('off', 'isNonegativeReal', -1007, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//off不合法
		array('cash', 'isNonegativeReal', -1008, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//cash不合法
		array('bank', 'isNonegativeReal', -1009, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//bank不合法
		array('online_pay', 'isNonegativeReal', -1018, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//在线支付不合法
		array('remark', '0,1000', -1010, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //remark长度不合法
		array('status', array(1,99), -1011, self::EXISTS_VALIDATE, 'between',self::MODEL_CREATE_DRAFT), //status不合法
		array('exceptionNo', array(0,3), -1012, self::EXISTS_VALIDATE, 'between',self::MODEL_CREATE_DRAFT), //exceptionNo不合法
		array('exception', '0,500', -1013, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //exception长度不合法
		array('class', array(1,4), -1015, self::EXISTS_VALIDATE, 'between',self::MODEL_CREATE_DRAFT), //class不合法
		array('contact_name', '1,45', -1017, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //联系人名称长度不合法
		array('sto_id','isUnsignedInt',-1039,self::MUST_VALIDATE,'function',self::MODEL_CREATE_DRAFT),//sto_id不合法

		//MODEL_DELETE_DRAFT
		array('oid', 'checkDeny_oid', -1001, self::MUST_VALIDATE, 'callback',self::MODEL_DELETE_DRAFT),//oid不合法


		//OrderModel_deleteCid
		array('cid', 'checkDeny_cid', -1002, self::MUST_VALIDATE, 'callback',self::OrderModel_deleteCid),//oid不合法

		//OrderModel_createOrder
		array('oid', 'checkDeny_oid', -1001, self::EXISTS_VALIDATE, 'callback',self::OrderModel_createOrder),//oid不合法
		array('cid', 'checkDeny_cid', -1002, self::MUST_VALIDATE, 'callback',self::OrderModel_createOrder),//cid不合法
		array('mobile', '0,25', -1004, self::MUST_VALIDATE, 'length',self::OrderModel_createOrder), //mobile长度不合法
		array('park_address', '0,500', -1005, self::MUST_VALIDATE, 'length',self::OrderModel_createOrder), //park_address长度不合法
		array('car_license', '0,30', -1006, self::MUST_VALIDATE, 'length',self::OrderModel_createOrder), //car_license长度不合法
		array('off', 'isNonegativeReal', -1007, self::MUST_VALIDATE, 'function',self::OrderModel_createOrder),//off不合法
		array('cash', 'isNonegativeReal', -1008, self::MUST_VALIDATE, 'function',self::OrderModel_createOrder),//cash不合法
		array('bank', 'isNonegativeReal', -1009, self::MUST_VALIDATE, 'function',self::OrderModel_createOrder),//bank不合法
		array('online_pay', 'isNonegativeReal', -1018, self::MUST_VALIDATE, 'function',self::OrderModel_createOrder),//在线支付不合法
		array('remark', '0,1000', -1010, self::MUST_VALIDATE, 'length',self::OrderModel_createOrder), //remark长度不合法
		array('status', array(1,99), -1011, self::MUST_VALIDATE, 'between',self::OrderModel_createOrder), //status不合法
		array('exceptionNo', array(0,3), -1012, self::MUST_VALIDATE, 'between',self::OrderModel_createOrder), //exceptionNo不合法
		array('exception', '0,500', -1013, self::MUST_VALIDATE, 'length',self::OrderModel_createOrder), //exception长度不合法
		array('class', array(1,4), -1015, self::MUST_VALIDATE, 'between',self::OrderModel_createOrder), //class不合法
		array('contact_name', '1,45', -1017, self::MUST_VALIDATE, 'length',self::OrderModel_createOrder), //联系人名称长度不合法
        array('warehouse_remark', '0,500', -1010, self::MUST_VALIDATE, 'length',self::OrderModel_createOrder),
        array('is_calculated',array(0,1),-1034,self::EXISTS_VALIDATE,'in',self::OrderModel_createOrder),//运费是否计入成本不合法
        array('freight','isNonegativeReal',-1035,self::EXISTS_VALIDATE,'function',self::OrderModel_createOrder),//运费不合法
        array('freight_cal_method',array(0,2),-1036,self::EXISTS_VALIDATE,'between',self::OrderModel_createOrder),//计费计入成本方式不合法
        array('sto_id','isUnsignedInt',-1039,self::EXISTS_VALIDATE,'function',self::OrderModel_createOrder),//sto_id不合法
        array('isdeliver',array(0,1),-1041,self::EXISTS_VALIDATE,'in',self::OrderModel_createOrder),//是否送货参数不合法

		//OrderModel_createAdjustAROrAP
		array('cid', 'checkDeny_cid', -1002, self::MUST_VALIDATE, 'callback',self::OrderModel_createAdjustAROrAP),//cid不合法
		array('class', array(5,6), -1015, self::MUST_VALIDATE, 'between',self::OrderModel_createAdjustAROrAP), //class不合法
		array('name', '1,60', -1020, self::MUST_VALIDATE, 'length',self::OrderModel_createAdjustAROrAP), //name长度不合法
		array('income', 'isPositiveReal', -1033, self::MUST_VALIDATE, 'function',self::OrderModel_createAdjustAROrAP),//income不合法
        array('warehouse_remark', '0,500', -1010, self::MUST_VALIDATE, 'length',self::OrderModel_createAdjustAROrAP), //备注长度不合法
		array('remark', '0,1000', -1010, self::MUST_VALIDATE, 'length',self::OrderModel_createAdjustAROrAP), //remark长度不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('update_time', NOW_TIME, self::MODEL_BOTH),


		//MODEL_CREATE_DRAFT
		array('admin_uid','getAdminUid',self::MODEL_CREATE_DRAFT,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_CREATE_DRAFT),
		array('update_time', NOW_TIME, self::MODEL_CREATE_DRAFT),
		array('off','xyround',self::MODEL_CREATE_DRAFT,'function'),//货币类数字自动四舍五入
		array('cash','xyround',self::MODEL_CREATE_DRAFT,'function'),//货币类数字自动四舍五入
		array('bank','xyround',self::MODEL_CREATE_DRAFT,'function'),//货币类数字自动四舍五入
		array('online_pay','xyround',self::MODEL_CREATE_DRAFT,'function'),//货币类数字自动四舍五入

		//MODEL_DELETE_DRAFT
		array('admin_uid','getAdminUid',self::MODEL_DELETE_DRAFT,'function'),//填入所属创建者uid
		array('update_time', NOW_TIME, self::MODEL_DELETE_DRAFT),
		
		//OrderModel_deleteCid
		array('admin_uid','getAdminUid',self::OrderModel_deleteCid,'function'),//填入所属创建者uid
		array('update_time', NOW_TIME, self::OrderModel_deleteCid),

		//OrderModel_createOrder
		array('admin_uid','getAdminUid',self::OrderModel_createOrder,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::OrderModel_createOrder),
		array('update_time', NOW_TIME, self::OrderModel_createOrder),
		array('off','xyround',self::OrderModel_createOrder,'function'),//货币类数字自动四舍五入
		array('cash','xyround',self::OrderModel_createOrder,'function'),//货币类数字自动四舍五入
		array('bank','xyround',self::OrderModel_createOrder,'function'),//货币类数字自动四舍五入
		array('online_pay','xyround',self::OrderModel_createOrder,'function'),//货币类数字自动四舍五入
		array('freight','xyround',self::OrderModel_createOrder,'function'),//货币类数字自动四舍五入

		//OrderModel_createAdjustAROrAP
		array('admin_uid','getAdminUid',self::OrderModel_createAdjustAROrAP,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::OrderModel_createAdjustAROrAP),
		array('update_time', NOW_TIME, self::OrderModel_createAdjustAROrAP),
		array('income','xyround',self::OrderModel_createAdjustAROrAP,'function'),//货币类数字自动四舍五入
	);







	/**
	 * 写操作记录字符串.
	 * 
	 * note:
	 * 1. 如果有地方需要传入$arr，$arr必须初始化为null，这是为了通过“没有任何修改却提交了”的这种情况
	 * 2. 外部必须开启事务
	 * 
	 * @api
	 * @param int $oid 需要写历史的单据主键
	 * @param string $action 动作名称
	 * @param unsigned_int $time 时间戳
	 * @param array $arr 变动的字段的字段名数组，形如a[0][0] = sku_id,a[0][1]是sku_id的真实值，如1
	 * @param unknown $extra1 额外字段1。
	 * @param unknown $extra2 额外字段2。当status为2的时候这两个参数必须传入，分别为exceptionNo和exception
	 * 
	 * @return string 可以给人看的操作记录
	 * @throws \XYException
	 */
	protected function writeHistory($oid,$action,$time,$arr = array(),$extra1 = null,$extra2 = null)
	{
		$uid = getUid();
		$name = session('user_auth.name');
		$userSn = M('User')->where(array('uid'=>$uid,'admin_uid'=>getAdminUid()))->getField('sn');

		if (!is_array($arr))
			throw new \XYException(__METHOD__,-1997);;//修改操作没有修改任何字段而导致

		$userInfo = $name.'(编号:'.$userSn.')于'.date("Y-m-d日H:i:s",$time);
		switch ($action)
		{
			case 'add':{
				$action_name = '创建';
				break;
			}
			case 'edit':{
				$action_name = '修改';
				break;
			}
			default:
				throw new \XYException(__METHOD__,-1999);
		}

		$field = '';
		foreach ($arr as $key => $value)
		{
			switch ($value[0])
			{
				case 'contact_name':{
					if ( ($value[1] === '') || ($value[1] === null) )
						$value[1] = '空';
					$field .= '联系人名称'.'为'.$value[1].',';
					break;
				}
				case 'mobile':{
					if ( ($value[1] === '') || ($value[1] === null) )
						$value[1] = '空';
					$field .= '电话'.'为'.$value[1].',';
					break;
				}
				case 'park_address':{
					if ( ($value[1] === '') || ($value[1] === null) )
						$value[1] = '空';
					$field .= '停车位置'.'为'.$value[1].',';
					break;
				}
				case 'car_license':{
					if ( ($value[1] === '') || ($value[1] === null) )
						$value[1] = '空';
					$field .= '车牌号'.'为'.$value[1].',';
					break;
				}
                case 'warehouse_remark':{
                    if ( ($value[1] === '') || ($value[1] === null) )
                        $value[1] = '空';
                    $field .= '送货信息备注'.'为'.$value[1].',';
                    break;
                }
				case 'remark':{
					if ( ($value[1] === '') || ($value[1] === null) )
						$value[1] = '空';
					$field .= '备注'.'为'.$value[1].',';
					break;
				}
				case 'exceptionNo':{//为editOrder写的
					if ( ($value[1] === '') || ($value[1] === null) )
						$value[1] = '空';
					$field .= '异常状态码'.'为'.$value[1].',';
					break;
				}
				case 'exception':{//为editOrder写的
					if ( ($value[1] === '') || ($value[1] === null) )
						$value[1] = '空';
					$field .= '异常原因'.'为'.$value[1].',';
					break;
				}
				case 'status':{//为setOrderStatus写的
					if ($value[1] == 2)//当状态为2的时候
					{
						if ( ($extra2 === '') || ($extra2 === null) )
							$extra2 = '空';
						if ($extra1 == 3)
						{
							$field .= '状态'.'为'.$this->STATUS_INFO[$value[1]].'，异常原因：'.$extra2.'。异常备注：'.$extra2.'。';
						}
						elseif ($extra1 == 1)
						{
							$field .= '状态'.'为'.$this->STATUS_INFO[$value[1]].'，异常原因：库房货不够。异常备注：'.$extra2.'。';
						}
						elseif ($extra1 == 2)
						{
							$field .= '状态'.'为'.$this->STATUS_INFO[$value[1]].'，异常原因：客户装不下。异常备注：'.$extra2.'。';
						}
					}
					else
					{
						$field .= '状态'.'为'.$this->STATUS_INFO[$value[1]].',';
					}
					break;
				}
				default:
					throw new \XYException(__METHOD__,-1999);
			}
		}
		$field = rtrim($field,',');

		$preHistory = null;
        $history_data = array();
		if ($oid === 0)
			$preHistory = '';
		else
		{
			$preHistory = M('Order')->where(array('oid'=>$oid,'admin_uid'=>getAdminUid()))->lock(true)->getField('history');
            $history_data = unserialize($preHistory);
			if (empty($preHistory))
				throw new \XYException(__METHOD__,-1001);
		}

        $history_data[] = $userInfo;
        $re = serialize($history_data);


		if ( strlen($re) >= C('DATABASE_HISTORY_MAX_LENGTH') )
			throw new \XYException(__METHOD__,-1998);

		return $re;
	}



	/**
	 * 新建订单
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 是否已经开启事务 @internal
	 * @param bool $sys_mode 系统调用，即是否是Company->createCompany()的时候被调用，当其为true时，只用填入class cid remark，额外填入value. @internal
	 *
	 * @param bool $reopen 订单重新打开 重开-true  非重开-不传
	 * @param unsigned_int $oid 草稿用：如果有oid且没有$reopen则为草稿转为开单，应删除此oid对应的草稿。
	 * @param unsigned_int $class 单据类别
	 * @param unsigned_int $sto_id 仓库id
	 * @param int $isdeliver 是否送货 送货-1 不送货-0
	 * @param unsigned_int $cid 公司cid
	 * @param string $contact_name 联系人名称
	 * @param string $mobile 手机
	 * @param string $park_address 停车位置
	 * @param string $car_license 车牌号
     * @param string $warehouse_remark 送货信息备注
	 * @param double $off 优惠
	 * @param double $cash 现金
	 * @param double $bank 银行
	 * @param double $online_pay 在线支付
	 * @param double $freight 运费
	 * @param int    $is_calculated 运费是否计入
	 * 1-采购单运费计入成本或其他订单选垫付
	 * 0-采购单运费不计入成本或其他订单选我方承担 即另外生成一张费用单
	 * @param int    $freight_cal_method 采购单运费分摊方式 1- 按数量 2-按金额  不计入不传
	 * @param double $freight_received 已收运费（不能超过运费总金额）
	 * @param string $remark 备注
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @param '4||5||11||12' $status 状态
	 * @param json $cart 购物车内容，购物车格式如下：
	 * @example cart json:
	 * 	mini:{"data":[{"sku_id":1,"spu_name":"三黄鸡","spec_name":"10只","quantity":10,"unitPrice":123},{"sku_id":2,"spu_name":"三黄鸡","spec_name":"15只","quantity":20,"unitPrice":223}]}
	 * @example	展开:
	 *		{
	 *			"data":
	 *			[
	 *				{
	 *					"sku_id":1,
	 *					"spu_name":"三黄鸡",
	 *					"spec_name":"10只",
	 *					"quantity":10,
	 *					"unitPrice":123,
	 *					"comment":"备注"
	 *				},
	 *				{
	 *					"sku_id":2,
	 *					"spu_name":"三黄鸡",
	 *					"spec_name":"15只",
	 *					"quantity":20,
	 *					"unitPrice":223,
	 *					"comment":"备注"
	 *				}
	 *			]
	 *		}
	 *		
	 * @return unsigned_int 成功返回oid > 0
	 * @throws \XYException
	 *
	 * @since v0.2.28 $name => 无
	 */
	public function createOrder(array $data = null,$isAlreadyStartTrans = false,$sys_mode = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			log_("data",$data,$this);
			$data['is_calculated'] = intval($data['is_calculated']);
			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);

			$dbCompany     = D('Company');
			$dbContact     = D('Contact');
			$dbPhonenum    = D('Phonenum');
			$dbCarlicense  = D('Carlicense');
			$dbParkaddress = D('Parkaddress');
			$dbUser        = D('User');

			log_("data",$data,$this);
			//Company->createCompany()时调用，生成期初订单
			if ($sys_mode === true)
			{ 
				$data['contact_name'] = '系统';
				$data['mobile']       = '系统';
				$data['park_address'] = '系统';
				$data['car_license']  = '系统';
				$data['contack_remark'] = '系统';
				$data['off']          = 0;
				$data['cash']         = 0;
				$data['bank']         = 0;
				$data['online_pay']   = 0;
				$data['status']       = 99;
			}

			$tmp_reg_time = null;
			if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);
			if (!$this->field('oid,class,cid,contact_name,mobile,park_address,car_license,warehouse_remark,sto_id,off,cash,bank,online_pay,remark,status,freight,is_calculated,freight_cal_method')->create($data,self::OrderModel_createOrder))
				throw new \XYException(__METHOD__,$this->getError());
			log_("this->data",$this->data,$this);
			//必须放在create下面，因为create会自动创建一个时间
			if (isset($tmp_reg_time))
			{
				if($this->check_unix_date($tmp_reg_time))
				{
					$this->reg_time = $tmp_reg_time;
					$this->update_time = $tmp_reg_time;	
				}else
				{
					throw new \XYException(__METHOD__,-1038);
				}
			}


			if ($sys_mode === false)
			{
				//判断是什么订单流模式
				if (C('audit_flow_mode') == 1)
				{
					if (
							($this->status != 11) &&
							($this->status != 12) &&
							($this->status != 91) 
						)
					{
						throw new \XYException(__METHOD__,-1011);
					}
				}
				else
				{
					if (
						($this->status != 4) &&
						($this->status != 5) &&
						($this->status != 91) 

						)
					{
						throw new \XYException(__METHOD__,-1011);
					}
				}
			}

			// 单据作废时deleteOrder调用
			if ($this->status == 91)//红冲单据，各项逆反
			{
				$this->off = xysub(0 , $data['off']);
				$this->cash = xysub(0 , $data['cash']) ;
				$this->bank = xysub(0 , $data['bank']);
				$this->online_pay = xysub(0 , $data['online_pay']);

				//购物车的逆反在处理购物车那里做
			}

			if(isset($data['oid']) )
			{
				if(isset($data['reopen']) && $data['reopen'] === 'true')
				{
					if($data['reopen'] === 'true' )
					{
						
						//复制单据,原单据不做处理
					}
					else	
						throw new \XYException(__METHOD__,-1037);
				}
				else //如果是草稿单据转来的，则记录id，在创建成功后删除草稿单据
					$draftId = $this->oid;
					
				unset($this->oid);
			}

			//开始处理往来单位、联系人、电话、车牌号、送货地址信息、备注
			//处理往来单位
			// log_("this->cid",$this->cid,$this);
			if ($sys_mode)
				$this->cid_name = $dbCompany->getCompanyName($this->cid,0);
			else
				$this->cid_name = $dbCompany->getCompanyName($this->cid,1);
			
			if ($sys_mode === false)
			{
				//处理联系人、电话、车牌号
				//获取当前往来单位的联系人名称
				$tmpContactList = $dbContact->queryList(array('cid'=>$this->cid));
				$contactId = -1;
				$mobileId = -1;
				$carLicenseId = -1;
				
				// log_('tmpContactList',$tmpContactList,$this);

				foreach ($tmpContactList as $contactValue)
				{
					if ($contactValue['contact_name'] == $this->contact_name)
					{
						$contactId = $contactValue['contact_id'];
						foreach ($contactValue['phonenum'] as $value)
						{
							if ($this->mobile == $value['mobile'])
								$mobileId = $value['phonenum_id'];
						}
						foreach ($contactValue['car_license'] as $value)
						{
							if ($this->car_license == $value['car_license'])
								$carLicenseId = $value['carlicense_id'];
						}
					}
				}

				// log_('contactId',$contactId,$this);
				// log_('mobileId',$mobileId,$this);
				// log_('carLicenseId',$carLicenseId,$this);

				//联系人相关的创建st
				//这里采用的是较松的策略，即能创建就创建，创建不了就算了，不做强制校验
				if ($contactId <= 0)
				{
					try
					{
						$dbContact->createContact(array(
							'cid'          => $this->cid,
							'contact_name' => $this->contact_name,
							'cart'         => array(
														'phonenum' => array(
															array('mobile' => $this->mobile)
														),
														'car_license' => array(
															array('car_license' => $this->car_license)
														),
													),
							),true,true);
						}
					catch(\XYException $e)//截获联系人、电话、车牌号、停车位置、送货信息备注创建的异常，因为新建单据可以不用创建这些
					{
					}
				}
				else
				{
					//这里采用的是较松的策略，即能创建就创建，创建不了就算了，不做强制校验
					if ($mobileId <= 0)
					{
						try
						{
							$tmpPhonenumId = $dbPhonenum->createPhonenum(array(
									'cid'        =>$this->cid,
									'contact_id' =>$contactId,
									'mobile'     =>$this->mobile,
								),true);
						}
						catch(\XYException $e)//截获联系人、电话、车牌号、停车位置创建的异常，因为新建单据可以不用创建这些
						{
						}
					}
					if ($carLicenseId <= 0)
					{
						try
						{
							$tmpCarlicenseId = $dbCarlicense->createCarlicense(array(
									'cid'         =>$this->cid,
									'contact_id'  =>$contactId,
									'car_license' =>$this->car_license,
								),true);
						}
						catch(\XYException $e)//截获联系人、电话、车牌号、停车位置创建的异常，因为新建单据可以不用创建这些
						{
						}
					}
				}

				try
				{
					//处理送货地址信息
					//这里采用的是较松的策略，即能创建就创建，创建不了就算了，不做强制校验
					$tmpParkaddressId = $dbParkaddress->create_(array('park_address'=>$this->park_address),true);
				}
				catch(\XYException $e)//截获联系人、电话、车牌号、停车位置创建的异常，因为新建单据可以不用创建这些
				{
				}
				//联系人相关的创建end
				
				
				//开始处理购物车
				$this->value = 0;
				$totalQuantity = 0;
				if($this->status == 91)
				{
					$cartTmp = $data['cart'];
					// log_("cartTmp",$cartTmp,$this);
				}
				else
				{
					$tmpTextCart = I('param.cart','','');
					if (empty($tmpTextCart))
						$cartTmp = $data['cart'];
					else
						$cartTmp = json_decode(I('param.cart','',''),true);
				}
				if (empty($cartTmp['data']))
					throw new \XYException(__METHOD__,-1050);

				log_('cartTmp',$cartTmp,$this);
				$sto_id = $this->sto_id;


				foreach ($cartTmp['data'] as $key => $value)
				{
					if (	isUnsignedInt($value['sku_id']) &&
							is_numeric($value['quantity']) &&
							isNonegativeReal($value['unitPrice']) &&
							(!empty($value['spu_name']))  &&
							(!empty($value['spec_name']))
							// $this->checkDeny_skuId_adminUid_stock($value['sku_id'],$value['quantity'])在下面更改库存的时候一起检车了
						)
					{
						$cart[$key]['sku_id'] = I('data.sku_id/d',0,'htmlspecialchars',$value);
						if ($this->status == 91)
							$cart[$key]['quantity'] = xysub(0 , xyround(I('data.quantity/f',0,'htmlspecialchars',$value) ) );
						else
							$cart[$key]['quantity'] = xyround(I('data.quantity/f',0,'htmlspecialchars',$value) );
						$cart[$key]['unitPrice'] = xyround(I('data.unitPrice/f',0,'htmlspecialchars',$value) );
						$cart[$key]['pilePrice'] = xymul($cart[$key]['quantity'],$cart[$key]['unitPrice']);
						$cart[$key]['comment'] = I('data.comment/s','','htmlspecialchars',$value);
						$this->value = xyadd($this->value,$cart[$key]['pilePrice']);
						$totalQuantity = xyadd($totalQuantity,$cart[$key]['quantity']);
					}
					else
					{
						throw new \XYException(__METHOD__,-1050);
					}
				}
				log_("cart",$cart,$this);
				if (empty($cart))
				{
					throw new \XYException(__METHOD__,-1050);
				}

				// log_("this->value",$this->value,$this);
				// log_("totalQuantity",$totalQuantity,$this);
				//更改库存
				//TODO:批量更新
				$spuList = null;

				foreach ($cart as $key => $value)
				{
					$tmpSkuInfo = null;
					$tmpSkuStoInfo = null;
					$tmpSkuInfo = M('Sku')->where(array('sku_id'=>$value['sku_id'],'admin_uid'=>$this->admin_uid,'status'=>1))->lock(true)->field('sku_id,total_stock,sn,spu_id,spu_name,spec_name')->find();
					$tmpSkuStoInfo = M('SkuStorage')->where(array('sku_id'=>$value['sku_id'],'admin_uid'=>$this->admin_uid,'sto_id'=>$this->sto_id) )->lock(true)->field('sku_storage_id,sku_id,sto_id,unit_price,stock')->find();
					if(empty($tmpSkuInfo) || empty($tmpSkuStoInfo))
						throw new \XYException(__METHOD__,-1050);
					log_("tmpSkuStoInfo",$tmpSkuStoInfo,$this);
					log_("tmpSkuInfo",$tmpSkuInfo,$this);

					$spuList[$value['sku_id']] = $tmpSkuInfo['spu_id'];
					$cart[$key]['cost']        = $tmpSkuStoInfo['unit_price'];
					$cart[$key]['sn']          = $tmpSkuInfo['sn'];
					$cart[$key]['spu_name']    = $tmpSkuInfo['spu_name'];
					$cart[$key]['spec_name']   = $tmpSkuInfo['spec_name'];

					// 销售单、采购退货单，订单数量改为负
					if ( ($this->class == 1) || ($this->class == 4) )
					{
						$value['quantity'] = xysub(0 , $value['quantity']);
					}

					log_("value",$value,$this);
					// //要支持负库存，允许出货量大于库存，1051异常删除     2016/11/22   liuxun
					if ( ($this->class == 1) || ($this->class == 4) )//如果是出库类单据需要检查出货数量要小于等于库存
					{
						if ( ($this->status != 91) && ( abs($value['quantity']) > $tmpSkuStoInfo['stock'] ) )
							throw new \XYException(__METHOD__,-1051);
					}

					if ( ($this->class == 3))//如果是采购进货单需要更新成本单价
					{
						// dump($tmpSkuInfo);
						// echo 'zong:'.$tmpSkuInfo['unit_price']*$tmpSkuInfo['stock'] + $value['quantity']*$value['unitPrice'].'<br>';
						// echo 'up:'.( $tmpSkuInfo['unit_price']*$tmpSkuInfo['stock'] + $value['quantity']*$value['unitPrice'] ) / ( $tmpSkuInfo['stock'] + $value['unitPrice'] ).'<br>';

						//由于库存可能为负，此处计算价格需加绝对值
						//
						$originalCost = xymul($tmpSkuStoInfo['unit_price'],$tmpSkuStoInfo['stock'],0);
						$newCost = xymul($value['quantity'],$value['unitPrice'],0);
						if($this->is_calculated === 1)//运费计入成本
						{
							if(isset($data['freight']) && isPositiveReal($data['freight']))
								$freight = I('param.freight/f',0,'htmlspecialchars');
							else
								throw new \XYException(__METHOD__,-1035);
							// log_("freight",$freight,$this);
							if($this->status == 91)
								$freight = xysub(0 , $freight);
							$newUnitPrice = null;
							if(isset($data['freight_cal_method']) && in_array($data['freight_cal_method'],array(1,2)) )
							{}
							else
								throw new \XYException(__METHOD__,-1036);
							if(I('param.freight_cal_method/d','','int') == 1)//按数量分摊
							{
								$freightCost = xymul(xydiv($value['quantity'],$totalQuantity,0),$freight);
							}elseif(I('param.freight_cal_method/d','','int') == 2)//按价格分摊
							{
								$freightCost = xymul(xydiv($value['pilePrice'],$this->value,0),$freight);
							}
							$newUnitPrice = xydiv( 
									xyadd(xyadd( $originalCost , $newCost , 0 ) , $freightCost , 0 )
									,
									( xyadd($tmpSkuStoInfo['stock'],$value['quantity'],0) ) 
								) ;
							$cart[$key]['pilePrice'] = xyadd($cart[$key]['pilePrice'],$freightCost);
							$cart[$key]['freightCost'] = $freightCost;
						}
						elseif($this->is_calculated === 0)
						{//运费不计入成本
							$newUnitPrice = null;
							$newUnitPrice = xydiv( 
									xyadd( 
										xymul($tmpSkuStoInfo['unit_price'],$tmpSkuStoInfo['stock'],0)
										,
										xymul($value['quantity'],$value['unitPrice'],0)
										,
										0 
										)
									,
									( xyadd($tmpSkuStoInfo['stock'],$value['quantity'],0) ) 
								) ;
							$cart[$key]['freightCost'] = 0;
						}else
						{
							throw new \XYException(__METHOD__,-1034);
						}
						// log_("originalCost",$originalCost,$this);
						// log_("newCost",$newCost,$this);
						// log_("freightCost",$freightCost,$this);
						// log_("newUnitPrice",$newUnitPrice,$this);
						$tmpSkuReturn = M('SkuStorage')->where(array('sku_storage_id'=>$tmpSkuStoInfo['sku_storage_id'],'status'=>1))
						  ->setField('unit_price',
							$newUnitPrice
							);
					}
					
					// 更新库存，如果是销售单，还要更新last_selling_price字段
					if ($this->class == 1)
						$tmpReturn1 = M('Sku')->where(array('sku_id'=>$value['sku_id'],'admin_uid'=>$this->admin_uid,'status'=>1))->setField(array(
									'last_selling_price' => $value['unitPrice'],
								));
					// 更新sku_storage表库存
					$tmpReturn2 = M('SkuStorage')->where(array('sku_storage_id'=>$tmpSkuStoInfo['sku_storage_id'],'admin_uid'=>$this->admin_uid,'sku_sto_status'=>1))->setField(array('stock'=> xyadd($tmpSkuStoInfo['stock'],$value['quantity'])));
					// 更新sku表库存
					$dbSku = new SkuModel();
					$tmpReturn3 = $dbSku->where(array('sku_id'=>$value['sku_id'],'admin_uid'=>$this->admin_uid))->setField(array('total_stock'=>xyadd($tmpSkuInfo['total_stock'],$value['quantity'])));
					if( (isset($tmpReturn1) && $tmpReturn1===false) || $tmpReturn2 ===false || $tmpReturn2 === null || $tmpReturn3 ===false || $tmpReturn3 === null)
						throw new \XYException(__METHOD__,-1000);
					/*if (  
							empty($tmpReturn)
							|| ( (isset($tmpSkuReturn)) && (($tmpSkuReturn === null) || ($tmpSkuReturn === false)) )//允许tmpSkuReturn返回0，因为有可能成本价不改变
						)
					{
						// $this->rollback(__METHOD__);
						// return ('-51:'.$value['sku_id']);
						// log_('$value["quantity"]',$value["quantity"],$this);
						throw new \XYException($value['sku_id'],-1051);
					}*/
				}

				//序列化购物车信息
				$this->cart = serialize($cart);
				if (empty($this->cart))
					throw new \XYException(__METHOD__,-1050);
			}

			if ($sys_mode === true)
			{
				if (isNonegativeReal($data['value']))
					$this->value = I('data.value/f',0,'htmlspecialchars',$data);
				else
					throw new \XYException(__METHOD__,-1019);
			}

			//计算应收款
			$this->receivable = 0;
			$this->receivable = xysub($this->value,$this->off);
			if($this->class == 3 && $this->freight>0 && $this->is_calculated ==1)//采购单运费计入成本
				$this->value = xyadd($this->value,$freight);

			if ( ($this->status != 91) && ($this->receivable < 0) )
				throw new \XYException(__METHOD__,-1401);
			// log_("receivable",$receivable,$this);
			//计算实收
			$this->income = 0;
			$this->income = xyadd($this->cash,xyadd($this->bank,$this->online_pay,0) );
			//发生交易的那一刻的本单结余快照（实收-应收）
			// log_("income",$income,$this);
			$this->balance = 0;
			$this->balance = xysub($this->income,$this->receivable);
			if ( ($this->class == 2) || ($this->class == 3) )//当为采购类订单的时候这里是实付-应付,所以需要0-
				$this->balance = 0 - $this->balance;
			// log_("balance",$balance,$this);
			$this->remain = 0 - $this->balance;
			// log_("remain",$remain,$this);
			//获取往来单位历史余额
			$this->history_balance = 0;
			if ($sys_mode)
				$this->history_balance = $dbCompany->getBalance(array('cid'=>$this->cid),true,0);
			else
				$this->history_balance = $dbCompany->getBalance(array('cid'=>$this->cid),true,1);
			// log_("this->history_balance",$this->history_balance,$this);
			$this->total_balance = 0;
			$this->total_balance = xyadd($this->history_balance,$this->balance);
			// log_("total_balance",$total_balance,$this);
			$this->history = $this->writeHistory(0,'add',$this->update_time);
			//获取操作人员信息
			$this->operator_uid = session('user_auth.uid');
			$this->operator_name = session('user_auth.name');

			if ($this->class == 1) $this->sn = $this->getNextSn('OXC');
			if ($this->class == 2) $this->sn = $this->getNextSn('OXT');
			if ($this->class == 3) $this->sn = $this->getNextSn('OCR');
			if ($this->class == 4) $this->sn = $this->getNextSn('OCT');

			// log_("this->sn",$this->sn,$this);
			$status = intval($this->status);
			log_("this->data",$this->data,$this);
			log_("this->is_calculated",$this->is_calculated,$this);
			// 如果采购单运费不计入成本或其他订单运费由我方承担 单独生成一张费用支出单
			if($this->is_calculated === 0)
			{
				if( $this->freight > 0)
				{
					$expenseData = null;
					$expenseData['name'] = '运费';
					switch ($this->class) {
						case '1':
						 	$expenseData['remark'] = "由销售单".$this->sn.'产生的运费金额';
							break;
						case '2':
						 	$expenseData['remark'] = "由销售退货单".$this->sn.'产生的运费金额';
							break;
						case '3':
						 	$expenseData['remark'] = "由采购单".$this->sn.'产生的运费金额';
							break;
						case '4':
						 	$expenseData['remark'] = "由采购退货单".$this->sn.'产生的运费金额';
							break;
						default:
							throw new \XYException(__METHOD__,-1015);
							break;
					}
					$expenseData['class'] = 74;
					$expenseData['cash'] = $this->freight;
					$expenseData['bank'] = 0;
					$expenseData['online_pay'] = 0;
					$expenseData['reg_time'] = $this->reg_time;
					$tmp = D('Finance')->createIncomeOrExpense($expenseData);
				}
				elseif($this->freight < 0)
					throw new \XYException(__METHOD__,-1035);

			}

			$backup = $this->data;
			// log_("this->data",$this->data,$this);
			// log_("this->backup",$backup,$this);

			log_("this->data",$this->data,$this);
			$oid = $this->add();
			// 如果其他订单运费选择代付 生成一张应收款增加单
			if($this->is_calculated == 0)
			{
				if($this->freight>0)//不填运费时不做任何操作
				{
					if( $this->freight_received>=0 && $this->freight_received < $this->freight )
					{
						//生成一张应收款增加单
						$adjustARData = null;
						$adjustARData['cid'] = $this->cid;
						$adjustARData['class'] = 5;
						$adjustARData['income'] = $this->freight-$this->freight_received;
						$adjustARData['name'] = 'a';
						switch ($this->class) {
							case '1':
							 	$adjustARData['remark'] = "由销售单".$this->sn.'产生的运费垫付金额';
								break;
							case '2':
							 	$adjustARData['remark'] = "由销售退货单".$this->sn.'产生的运费垫付金额';
								break;
							case '3':
							 	$adjustARData['remark'] = "由采购单".$this->sn.'产生的运费垫付金额';
								break;
							case '4':
							 	$adjustARData['remark'] = "由采购退货单".$this->sn.'产生的运费垫付金额';
								break;
							default:
								throw new \XYException(__METHOD__,-1015);
								break;
						}
						$adjustARData['reg_time'] = $this->reg_time;
						$tmpad = $this->createAdjustAROrAP($adjustARData);
					}
					elseif($this->freight_received = $this->freight)
					{
						//生成一张应收款增加单
						$adjustARData = null;
						$adjustARData['cid'] = $this->cid;
						$adjustARData['class'] = 5;
						$adjustARData['income'] = $this->freight;
						switch ($this->class) {
							case '1':
							 	$adjustARData['remark'] = "由销售单".$this->sn.'产生的运费垫付金额';
								break;
							case '2':
							 	$adjustARData['remark'] = "由销售退货单".$this->sn.'产生的运费垫付金额';
								break;
							case '3':
							 	$adjustARData['remark'] = "由采购单".$this->sn.'产生的运费垫付金额';
								break;
							case '4':
							 	$adjustARData['remark'] = "由采购退货单".$this->sn.'产生的运费垫付金额';
								break;
							default:
								throw new \XYException(__METHOD__,-1015);
								break;
						}
						$adjustARData['reg_time'] = $this->reg_time;
						$tmpad = $this->createAdjustAROrAP($adjustARData);

						//生成一张对应的收款单
						$receiptData = null;
						$receiptData['cid'] = $this->cid;
						$receiptData['class'] = 73;
						$receiptData['cash'] = $this->freight;
						$receiptData['bank'] = 0;
						$receiptData['online_pay'] = 0;
						switch ($this->class) {
							case '1':
							 	$receiptData['remark'] = "由销售单".$this->sn.'产生的运费垫付金额';
								break;
							case '2':
							 	$receiptData['remark'] = "由销售退货单".$this->sn.'产生的运费垫付金额';
								break;
							case '3':
							 	$receiptData['remark'] = "由采购单".$this->sn.'产生的运费垫付金额';
								break;
							case '4':
							 	$receiptData['remark'] = "由采购退货单".$this->sn.'产生的运费垫付金额';
								break;
							default:
								throw new \XYException(__METHOD__,-1015);
								break;
						}
						$receiptData['reg_time'] = $this->reg_time;
						$receiptData['cart']['data'] = array(
							array(
								'oid'	=> $tmpad,
								'money' => $this->freight
								)
							);
						$tmpad = $this->createReceiptOrPayment($receiptData);
					}
					else
						throw new \XYException(__METHOD__,-1042);
				}
			}
			

			if ($oid > 0)
			{
				if(isset($draftId))
				{
					//如果是草稿单据转来的，则记录id，在创建成功后删除草稿单据
					$this->deleteDraft(array('oid'=>$draftId));
				}

				if ($backup['balance'] != 0)
				{
					if ($sys_mode)
						$dbCompany->setBalanceInOrder($backup['cid'],$backup['balance'],0);
					else
						$dbCompany->setBalanceInOrder($backup['cid'],$backup['balance'],1);
				}

				if($backup['class'] ==1 )
					//消费记录加1
					$comUpdate = M('Company')->where(array('cid'=>$backup['cid']))->setInc('purchase_times');

				foreach ($cart as $key => $value)
				{
					//创建sku_bill单据
					log_("spuList",$spuList,$this);
					D('SkuBill')->create_(array(
							'sku_id'      => $value['sku_id'],
							'spu_id'      => $spuList[$value['sku_id']],
							'oid'         => $oid,
							'bill_class'  => $backup['class'],
							'bill_status' => $backup['status'],
							'reg_time'    => $backup['reg_time'],
						));
					//创建sku_cid_price 单据
					if ( ($backup['class'] == 1) && ($status != 91) )//红冲单据不处理
						D('SkuCidPrice')->updateLastPrice(array(
								'sku_id'      => $value['sku_id'],
								'spu_id'      => $spuList[$value['sku_id']],
								'cid'         => $backup['cid'],
								'price1'      => $value['unitPrice'],
								'quantity1'   => $value['quantity'],
								'update_time' => $backup['reg_time'],
							));
				}

				if ($status == 91)
					;
				elseif ( (C('order_flow_mode') == false) && ($status != 99) && $status !=5  )//如果没有开启单据流程模式且没有勾选暂缓发货，就把订单直接设置为完成状态
				{
					$this->setOrderStatus(array('oid' =>$oid,'status' =>1,'update_time' =>$backup['update_time']),true);
				}
				else
				{
					if ($status == 4)
					{
						$this->setOrderStatus(array(//尝试推送到库管
								'oid'    =>$oid,
								'status' =>6,
								'update_time' =>$backup['update_time']
							),true);
					}
				}

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);

				return $oid;
			}
			else
			{
				throw new \XYException(__METHOD__,-1000);
			}
		}catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
	}



	/**
	 * 编辑订单信息。
	 * 
	 * - @param mixed|null $data POST的数据
	 * - @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * - @param unsigned_int $oid，必填
	 * -
	 * - 可选参数（以下字段任选一个以上）：
	 * - @param string $contact_name 联系人名
	 * - @param string $mobile 手机
	 * - @param string $park_address 停车位置
	 * - @param string $car_license 车牌号
     * - @param string $warehouse_remark 送货信息备注
	 * - @param string $remark 备注
	 * - 当status为2的时候，需额外传入：
	 * - @param unsigned_int $exceptionNo 异常状态码
	 * - @param string $exception 异常原因
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 *
	 * @api
	 * @since v0.3 不能更改状态了
	 */
	public function editOrder(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			// if (isset($data['reg_time'])) $tmp_reg_time = $data['reg_time'];
			if (!$this->field('oid,contact_name,mobile,park_address,car_license,warehouse_remark,remark,exceptionNo,exception')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());

			// if (isset($data['reg_time'])) $this->reg_time = $tmp_reg_time;
			$tmpInfo = M('Order')->where(array('oid'=>$this->oid,'admin_uid'=>$this->admin_uid))->find();
			if ($tmpInfo['status'] == 99)
				throw new \XYException(__METHOD__,-1502);

			$arr = null;//为了写操作记录
			if (!isset($data['contact_name'])) 	unset($this->contact_name);
			if (!isset($data['mobile'])) 		unset($this->mobile);
			if (!isset($data['park_address'])) 	unset($this->park_address);
            if (!isset($data['warehouse_remark'])) 	unset($this->warehouse_remark);
			if (!isset($data['car_license'])) 	unset($this->car_license);
			if (!isset($data['remark'])) 		unset($this->remark);
			if (!isset($data['reg_time'])) 		unset($this->reg_time);
			if (!isset($data['exceptionNo'])) 	unset($this->exceptionNo);
			if (!isset($data['exception'])) 	unset($this->exception);

			if (isset($this->contact_name)) $arr[] = array('contact_name',$this->contact_name);
			if (isset($this->mobile)) 		$arr[] = array('mobile',$this->mobile);
			if (isset($this->park_address)) $arr[] = array('park_address',$this->park_address);
			if (isset($this->car_license)) 	$arr[] = array('car_license',$this->car_license);
            if (isset($this->warehouse_remark)) 	$arr[] = array('warehouse_remark',$this->warehouse_remark);
			if (isset($this->remark))	 	$arr[] = array('remark',$this->remark);
			if (isset($this->reg_time))	 	$arr[] = array('reg_time',$this->reg_time);
			if (isset($this->exceptionNo)) 	$arr[] = array('exceptionNo',$this->exceptionNo);
			if (isset($this->exception)) 	$arr[] = array('exception',$this->exception);


			//写历史记录
			$this->history = $this->writeHistory($this->oid,'edit',$this->update_time,$arr);//$arr必须初始化为null，这是为了通过“没有任何修改却提交了”的这种情况

			//更新
			$tmp = $this->where(array('oid'=>$this->oid,'admin_uid'=>$this->admin_uid))->save();
			
			if ( ($tmp === false) || ($tmp === null) )
				throw new \XYException(__METHOD__,-1000);

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			return 1;
		}
		catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
	}




	/**
	 * 删除单据（红冲单据）
	 * @note 传入的数据没有检查是否正确，要确保传入的是正确的数据！！！！！
	 *
	 * @internal server
	 * @param mixed|null $data 数据库里单据一行的信息
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 1.3
	 * @date    2017-02-23
	 */
	public function deleteOrder(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$dbCompany = D('Company');
			
			log_("deleteOrder_data",$data,$this);

			if ( ($data['class'] >=1) && ($data['class'] <=4) )
				;
			else
				throw new \XYException(__METHOD__,-1015); 
			
			//将影响复原
			$cart = unserialize($data['cart']);
			if ($cart === false)
				throw new \XYException(__METHOD__,-1050);
			
			log_("deleteOrder_cart",$cart,$this);

			if($data['is_calculated'] === 1 )
			{
				$this->createOrder(array(
								'class'        => $data['class'],
								'cid'          => $data['cid'],
								'sto_id'	   => $data['sto_id'],
								'contact_name' => $data['contact_name'],
								'mobile'       => $data['mobile'],
								'park_address' => $data['park_address'],
								'car_license'  => $data['car_license'],
								'warehouse_remark' => $data['warehouse_remark'],
								'off'          => $data['off'],
								'cash'         => $data['cash'],
								'bank'         => $data['bank'],
								'online_pay'   => $data['online_pay'],
								'remark'       =>'因作废单据'.$data['sn'].'所产生的红冲单据',
								'status'       => 91,
								'cart'         => array('data'=>$cart),
								'is_calculated'=> $data['is_calculated'],
								'freight'	   => $data['freight'],
								'freight_cal_method' => $data['freight_cal_method']

							),true,false);
			}else
			{
				$this->createOrder(array(
								'class'        => $data['class'],
								'cid'          => $data['cid'],
								'sto_id'	   => $data['sto_id'],
								'contact_name' => $data['contact_name'],
								'mobile'       => $data['mobile'],
								'park_address' => $data['park_address'],
								'car_license'  => $data['car_license'],
								'warehouse_remark' => $data['warehouse_remark'],
								'off'          => $data['off'],
								'cash'         => $data['cash'],
								'bank'         => $data['bank'],
								'online_pay'   => $data['online_pay'],
								'remark'       =>'因作废单据'.$data['sn'].'所产生的红冲单据',
								'status'       => 91,
								'cart'         => array('data'=>$cart),
							),true,false);
			}

			//这里分如下情况：
			//1. 不产生应收应付：没什么要处理的
			//2. 产生了应收应付款，但没开过收付款单：没什么要处理的
			//3. 产生了应收应付款，且开过了收付款单：要调整余额。又因为receivable（应收）>=0，income（实收）>=0，所以会产生如下4种情况：
			//		1.收款类：
			//				1. 应收50 实收10 {remain:40,company_balance:-40} ------- 收30 {remain:10,company_balance:-10}
			//				2. 应收50 实收70 {remain:-20,company_balance:20} ------- 付10 {remain:-10,company_balance:10}
			//		2.付款类：
			//				1. 应付50 实付10 {remain:-40,company_balance:40} ------- 付30 {remain:-10,company_balance:10}
			//				2. 应付50 实付60 {remain:10,company_balance:-10} ------- 收3  {remain:7,company_balance:-7}
			if ( ($data['class'] == 1) || ($data['class'] == 4) )//收款类
			{
				$received = xysub( xysub($data['receivable'],$data['income'],0),$data['remain']);//已收
				if ($received != 0)//产生了应收应付款，并且开过了收付款单
				{
					$dbCompany->setBalanceInFinance($data['cid'],0-$received,-1);//将已收产生的效果抵消
					
					if ($received > 0)//应收比实收多
						$redClass = 6;
					elseif ($received < 0)//应收比实收少
						$redClass = 5;

					$backStack  = null;
					$backStack  = $this->data;
					$this->data = null;
					$this->createAdjustAROrAP(array(
							'cid'    => $data['cid'],
							'class'  => $redClass,
							'name'   => '因作废单据'.$data['sn'].'所产生的红冲单据',
							'income' => abs($received),
							'remark' => '因作废单据'.$data['sn'].'所产生的红冲单据',
						),true,true);
					$this->data = $backStack;
				}
			}
			elseif ( ($data['class'] == 2) || ($data['class'] == 3) )//付款类
			{
				$paid = xysub( xysub($data['receivable'],$data['income'],0),(xysub(0,$data['remain'],0)) );//已付
				if ($paid != 0)//产生了应收应付款，并且开过了收付款单
				{
					$dbCompany->setBalanceInFinance($data['cid'],$paid,-1);//将已收产生的效果抵消
					
					if ($paid > 0)//应收比实收多
						$redClass = 5;
					elseif ($paid < 0)//应收比实收少
						$redClass = 6;

					$backStack  = null;
					$backStack  = $this->data;
					$this->data = null;
					$this->createAdjustAROrAP(array(
							'cid'    => $data['cid'],
							'class'  => $redClass,
							'name'   => '因作废单据'.$data['sn'].'所产生的红冲单据',
							'income' => abs($paid),
							'remark' => '因作废单据'.$data['sn'].'所产生的红冲单据',
						),true,true);
					$this->data = $backStack;
				}
			}

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
		}
		catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}
		catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
	}



 	/**
 	 * 设置订单状态
 	 * 
 	 * - @param unsigned_int $oid
 	 * - @param unsigned_int $status 状态
 	 * - 当设置status为2的时候，需额外传入：
	 * - @param unsigned_int $exceptionNo 异常状态码
	 * - @param string $exception 异常原因
	 *
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
 	 *
 	 * @return 1 成功
 	 * @throws \XYException
	 *
	 * @api
 	 */
 	public function setOrderStatus(array $data = null,$isAlreadyStartTrans = false)
 	{
 		$dbUser = D('User');

 		try
 		{
	 		if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

	 		// 新建订单时修改时间与创建时间相同,在data数据中另外传递
	 		$tmp_update_time = null ;
	 		if(isset($data['update_time']))
	 			$tmp_update_time = $data['update_time'];
			if (!$this->field('oid,status,exceptionNo,exception')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());
	 		if(isset($tmp_update_time) && $tmp_update_time>0)
	 			$this->update_time = $tmp_update_time;

			$tmpInfo = null;
			$tmpInfo = M('Order')->where(array('oid'=>$this->oid,'admin_uid'=>$this->admin_uid))->find();
			//根据状态判断是否可以改成当前的状态，并执行不同的操作。
			switch ($this->status)
			{
				case 1:{//完成
					if (
							($tmpInfo['status'] != 2) &&
							($tmpInfo['status'] != 4) &&
							($tmpInfo['status'] != 5) &&
							($tmpInfo['status'] != 6) &&
							($tmpInfo['status'] != 7) &&
							($tmpInfo['status'] != 8) &&
							($tmpInfo['status'] != 9) &&
							($tmpInfo['status'] != 10) &&
							($tmpInfo['status'] != 91)
						)
						throw new \XYException(__METHOD__,-1021);

					if ( ($tmpInfo['class'] == 2) || ($tmpInfo['class'] == 3) )//入库类单据
						$this->leave_time = $this->update_time;

					break;
				}
				case 2:{//异常
					if (
							($tmpInfo['status'] == 1) ||
							($tmpInfo['status'] == 3)
						)
						throw new \XYException(__METHOD__,-1022);

					break;
				}
				case 3:{//删除
				// 	if (
				// 			($tmpInfo['status'] == 2) ||
				// 			($tmpInfo['status'] == 4) ||
				// 			($tmpInfo['status'] == 5) ||
				// 			($tmpInfo['status'] == 6) ||
				// 			($tmpInfo['status'] == 7) ||
				// 			($tmpInfo['status'] == 11) ||
				// 			($tmpInfo['status'] == 12) ||
				// 			(
				// 				(C('DELETE_ORDER_ALLOW_STATUS') == 1) &&
				// 				(
				// 					($tmpInfo['status'] == 8) ||
				// 					($tmpInfo['status'] == 10)
				// 				)
				// 			)
				// 		)
				// 		;
				// 	else
				// 		throw new \XYException(__METHOD__,-1023);

					if ( ($tmpInfo['status'] == 3) || ($tmpInfo['status'] == 91)  || ($tmpInfo['status'] == 92) || ($tmpInfo['status'] == 99) || ($tmpInfo['status'] == 100) )
						throw new \XYException(__METHOD__,-1023);
					
					$stackBack = null;
					$stackBack = $this->data;
					$this->deleteOrder($tmpInfo,true);
					$this->data = $stackBack;

					break;
				}
				case 4:{
					if (
							($tmpInfo['status'] != 12)
						)
						throw new \XYException(__METHOD__,-1024);
					break;
				}
				case 5:{
					if (
							($tmpInfo['status'] != 11)				
						)
						throw new \XYException(__METHOD__,-1025);
					break;
				}
				case 6:{
					if (
							($tmpInfo['status'] != 5) && ($tmpInfo['status'] != 4) && ($tmpInfo['status'] != 6) && ($tmpInfo['status'] != 7)
						)
						throw new \XYException(__METHOD__,-1026);
					break;
				}
				case 7:{
					if (
							($tmpInfo['status'] != 6)
						)
						throw new \XYException(__METHOD__,-1027);
					break;
				}
				case 8:{//出库
					if (
							($tmpInfo['status'] != 7)
						)
						throw new \XYException(__METHOD__,-1028);
					if ( ($tmpInfo['class'] != 1) && ($tmpInfo['class'] != 4) )
						throw new \XYException(__METHOD__,-1028);
					
					break;
				}
				case 9:{
					if (
							($tmpInfo['status'] != 8)
						)
						throw new \XYException(__METHOD__,-1029);

					$this->leave_time = $this->update_time;
					break;
				}
				case 10:{
					if (
							($tmpInfo['status'] != 7)
						)
						throw new \XYException(__METHOD__,-1030);
					if ( ($tmpInfo['class'] != 2) && ($tmpInfo['class'] != 3) )
						throw new \XYException(__METHOD__,-1030);
					break;
				}
				case 11:{
					//判断
					if (
							($tmpInfo['status'] != 2)
						)
						throw new \XYException(__METHOD__,-1031);

					//操作
					if ( C('DELETE_ORDER_ALLOW_STATUS') == 2 )
						$this->status = 5;

					break;
				}
				case 12:{
					//判断
					if (
							($tmpInfo['status'] != 2)
						)
						throw new \XYException(__METHOD__,-1032);

					//操作
					if ( C('DELETE_ORDER_ALLOW_STATUS') == 2 )
						$this->status = 4;

					break;
				}
				default:
					throw new \XYException(__METHOD__,-1011);
			}
			

			$this->history = $this->writeHistory($this->oid,'edit',$this->update_time,array(array('status',$this->status)),$this->exceptionNo,$this->exception);

			if (empty($this->exceptionNo)) unset($this->exceptionNo);
			if (!isset($data['exception'])) unset($this->exception);

			//更新
			$oid    = $this->oid;
			$status = $this->status;
			$class  = $tmpInfo['class'];

			$tmp = $this->where(array('oid'=>$this->oid,'admin_uid'=>$this->admin_uid))->save();
			if ( ($tmp === null) || ($tmp === false) )
				throw new \XYException(__METHOD__,-1000);
			else
			{
				if ($status == 6)
				{
					$GTClientID = $dbUser->getWarehouseUidGTClientID();
					log_("GTClientID is :",$GTClientID,$this);
					if (empty($GTClientID))//没有设置库管端手机
						;
					else
					{
						$asyn_task_data = array(
							'GTClientID' => $GTClientID,
							'oid' 		 => $oid,
							);
						log_('Test asyn Node task.Notify Warehouse.',$asyn_task_data,$this);

						$task = M('AsynTasks');
						$task_data = array(
							'name'		=> 'Notify Warehouse',
							'class'	 	=> 1,
							'data'		=> json_encode($asyn_task_data),
							);
			
						$task->add($task_data);
						// import('Vendor.getui.getui');
						// $gt = new \GeTui();
						//$gt->pushOrderMsg($GTClientID,'{"oid":'.$oid.'}');
						$this->setOrderStatus(array('oid'=>$oid,'status'=>7),true);
					}
				}

				//出库类
				if ( ($status == 7) && (($class == 1) || ($class == 4)) && (C('ck_jump_warehouseConfirm') == true) )
					$this->setOrderStatus(array('oid'    =>$oid,'status' =>8,),true);
				if ( ($status == 8) && (($class == 1) || ($class == 4)) && (C('ck_jump_warehouOut') == true) )
					$this->setOrderStatus(array('oid'    =>$oid,'status' =>9,),true);
				if ( ($status == 9) && (($class == 1) || ($class == 4)) && (C('ck_jump_deliver') == true) )
					$this->setOrderStatus(array('oid'    =>$oid,'status' =>1,),true);
				//入库类
				if ( ($status == 7) && (($class == 2) || ($class == 3)) && (C('rk_jump_warehouseConfirm') == true) )
					$this->setOrderStatus(array('oid'    =>$oid,'status' =>10,),true);
				if ( ($status == 10) && (($class == 2) || ($class == 3)) && (C('rk_jump_deliver') == true) )
					$this->setOrderStatus(array('oid'    =>$oid,'status' =>1,),true);

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return 1;
			}
		}catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
 	}
 

 	/**
	 * 新建订单草稿
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $oid 为空是新建草稿，有值是修改草稿
	 * @param unsigned_int $class 单据类别
	 * @param unsigned_int $sto_id 仓库id
	 * @param unsigned_int $cid 公司cid
	 * @param string $contact_name 联系人名称
	 * @param string $mobile 手机
	 * @param string $park_address 停车位置
	 * @param string $car_license 车牌号
	 * @param double $off 优惠
	 * @param double $cash 现金
	 * @param double $bank 银行
	 * @param double $online_pay 在线支付
	 * @param string $remark 备注
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @param json $cart 购物车内容，购物车格式如下：
	 * @cart json:
	 * 	mini:{"data":[{"sku_id":1,"spu_name":"三黄鸡","spec_name":"10只","quantity":10,"unitPrice":123},{"sku_id":2,"spu_name":"三黄鸡","spec_name":"15只","quantity":20,"unitPrice":223}]}
	 * 	展开:
	 *		{
	 *			"data":
	 *			[
	 *				{
	 *					"sku_id":1,
	 *					"spu_name":"三黄鸡",
	 *					"spec_name":"10只",
	 *					"quantity":10,
	 *					"unitPrice":123
	 *				},
	 *				{
	 *					"sku_id":2,
	 *					"spu_name":"三黄鸡",
	 *					"spec_name":"15只",
	 *					"quantity":20,
	 *					"unitPrice":223
	 *				}
	 *			]
	 *		}
	 *		
	 * @return unsigned_int 成功返回oid > 0（新建）或1（修改）
	 * @throws \XYException
	 *
	 * @since v0.2.28 $name => 无
	 */
 	public function createDraft(array $data = null,$isAlreadyStartTrans = false)
 	{
 		try
 		{
 			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$dbCompany = D('Company');
			
			$tmp_reg_time = null;
			if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);

			if (!$this->field('sto_id,oid,class,cid,contact_name,mobile,park_address,car_license,off,cash,bank,online_pay,remark,warehouse_remark')->create($data,self::MODEL_CREATE_DRAFT))
				throw new \XYException(__METHOD__,$this->getError());

			$oid = $this->oid;
			$dbOrder = new OrderModel();
			if(!empty($this->oid))
			{
				$oidData = $dbOrder->get_(array('oid'=>$this->oid));
				if($oidData['status'] <> 100)
					throw new \XYException(__METHOD__,-1043);
			}

			//必须放在create下面，因为create会自动创建一个时间
			if (isset($tmp_reg_time))
			{
                if($this->check_unix_date($tmp_reg_time))
				{
					$this->reg_time = $tmp_reg_time;
					$this->update_time = $tmp_reg_time;	
				}else
				{
					throw new \XYException(__METHOD__,-1038);
				}
			}

			$this->status = 100;

			if (!empty($this->cid))
				$this->cid_name = $dbCompany->getCompanyName($this->cid,1);

			//开始处理购物车
			$paramCartTmp = null;
			$paramCartTmp = I('param.cart','','');
			if ( empty($paramCartTmp) && (!empty($data['cart'])) )
				$cartTmp = $data['cart'];
			else
				$cartTmp = json_decode($paramCartTmp,true);
			if ( empty($cartTmp) && ( (!empty($paramCartTmp)) || (!empty($data['cart'])) ) )//I函数不为空或$data传入的不为空，而cartTmp为空，则转换出错
				throw new \XYException(__METHOD__,-1050);

			$cart = null;
			foreach ($cartTmp['data'] as $key => $value)
			{
				if (	isUnsignedInt($value['sku_id']) &&
						is_numeric($value['quantity']) &&
						isNonegativeReal($value['unitPrice']) &&
						(!empty($value['spu_name']))  &&
						(!empty($value['spec_name']))
					)
				{
					$cart[$key]['sku_id']    = I('data.sku_id/d',0,'htmlspecialchars',$value);
					$cart[$key]['spu_name']  = I('data.spu_name/s','','htmlspecialchars',$value);
					$cart[$key]['spec_name'] = I('data.spec_name/s','','htmlspecialchars',$value);
					$cart[$key]['comment']   = I('data.comment/s','','htmlspecialchars',$value);
					$cart[$key]['quantity']  = xyround(I('data.quantity/f',0,'htmlspecialchars',$value));
					$cart[$key]['unitPrice'] = xyround(I('data.unitPrice/f',0,'htmlspecialchars',$value));
				}
				else
				{
					throw new \XYException(__METHOD__,-1050);
				}
			}
			if ( empty($cartTmp) && ( (!empty($paramCartTmp)) || (!empty($data['cart'])) ) )
				throw new \XYException(__METHOD__,-1050);

			if (!empty($cart))
			{
				$this->cart = serialize($cart);
				if (empty($this->cart))
					throw new \XYException(__METHOD__,-1050);
			}

			if (empty($this->oid))
			{
				$this->sn = $this->getNextSn('DRA');
				$oid = $this->add();
				if ($oid > 0)
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return $oid;
				}
			}
			else
			{
				$oid = $this->save();
				if ( ($oid !== false) && ($oid !== null) )
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}

			throw new \XYException(__METHOD__,-1000);
		}
		catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
 	}



 	/**
	 * 查询单个订单
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isLock 是否加锁，如果为true，需要在外部打开事务. @internal
	 * 
	 * @param unsigned_int $oid 要查询的那个单据的主键
	 * 
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 */
	public function get_(array $data = null,$isLock = false)
	{
		if (!$this->field('oid')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpInfo = $this->where(array('oid'=>$this->oid,'admin_uid'=>session('user_auth.admin_uid')))->lock($isLock)->find();
		$stoNameList = D('Storage')->getStoName($isInstallSql);
		$tmpInfo['sto_name'] = $stoNameList[$tmpInfo['sto_id']];
		
		if (empty($tmpInfo))
			throw new \XYException(__METHOD__,-1001);

		//将购物车换回json
		if ($tmpInfo['status'] == 99 || $tmpInfo['class'] == 5 || $tmpInfo['class'] == 6)//系统单据、应收应付增加单没有购物车
			;
		else
		{
			if ( ($tmpInfo['status'] == 100) && (empty($tmpInfo['cart'])) )//草稿单可以没有购物车
				;
			else
			{
				$tmpSerialize = null;
				$tmpSerialize = unserialize($tmpInfo['cart']);
				if ($tmpSerialize === false)
						throw new \XYException(__METHOD__,-1050);
				foreach($tmpSerialize as $k=>&$v)
				{	
					$v['sto_id'] = $tmpInfo['sto_id'];
					if(!isset($v['freightCost']))//历史数据，没有选分摊金额时
						$v['freightCost'] = 0;
				}
				$tmpInfo['cart'] = $tmpSerialize;
			}
		}
		if (!empty($tmpInfo['history']))
        {
            $tmpInfo['history'] = unserialize($tmpInfo['history']);
        }

		return $tmpInfo;
	}



	 /**
	 * mobile用的查询单个订单API
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $oid 要查询的那个单据的主键
	 * 
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 */
	public function mGet_(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if (!$this->field('oid')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());

			$tmpInfo = M('Order')->where(array('oid'=>$this->oid,'admin_uid'=>session('user_auth.admin_uid')))->lock(true)->find();
			if ($tmpInfo === false)
				throw new \XYException(__METHOD__,-1000);
			elseif (empty($tmpInfo))
				throw new \XYException(__METHOD__,-1001);

			$oid        = $this->oid;
			$class      = $tmpInfo['class'];
			$updateTime = $this->update_time;

			//更新手机端已读状态
			$updateData = null;
			$updateData['oid']         = $oid;
			$updateData['GeTuiGet']    = ++$tmpInfo['GeTuiGet'];
			$updateData['update_time'] = $updateTime;
			$tmp = $this->save($updateData);
			if (empty($tmp))
				throw new \XYException(__METHOD__,-1000);

			if ( ($tmpInfo['GeTuiGet'] == 1) && ( ($class == 1) || ($class == 4) ) )//出库类单据
				$this->setOrderStatus(array('oid'=>$oid,'status'=>8),true);
			if ( ($tmpInfo['GeTuiGet'] == 1) && ( ($class == 2) || ($class == 3) ) )//入库类单据
				$this->setOrderStatus(array('oid'=>$oid,'status'=>10),true);

			$lastData = M('Order')->where(array('oid'=>$oid,'admin_uid'=>session('user_auth.admin_uid')))->find();//最新数据，因为不做任何更新了，所以在事务外面查，不然因为锁了主键，会失败
			if ($lastData === false)
				throw new \XYException(__METHOD__,-1000);
			elseif (empty($lastData))
				throw new \XYException(__METHOD__,-1001);

			//将购物车unserialize
			$tmpSerialize = null;
			$tmpSerialize = unserialize($lastData['cart']);
			if ($tmpSerialize === false)
					throw new \XYException(__METHOD__,-1050);
			$lastData['cart'] = $tmpSerialize;


			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);

			return $lastData;
		}catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
	}



	/**
	 * 删除单个草稿单据
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $oid 要删除的那个单据的主键
	 *
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteDraft(array $data = null)
	{
		if (!$this->field('oid')->create($data,self::MODEL_DELETE_DRAFT))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('oid'=>$this->oid,'admin_uid'=>session('user_auth.admin_uid')))->delete();
		
		if (empty($tmp))
			throw new \XYException(__METHOD__,-1501);

		return 1;
	}



	/**
	 * 删除某个cid下的全部单据
	 *
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * @param bool $isIgnoreEmpty 当删除返回结果为空的时候是否抛出异常。true-不抛出
	 * @param unsigned_int $cid
	 *
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function delete_cid(array $data = null,$isIgnoreEmpty = false)
	{
		if (!$this->field('cid')->create($data,self::OrderModel_deleteCid))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('cid'=>$this->cid,'admin_uid'=>session('user_auth.admin_uid')))->delete();
		
		if ($tmp === false)
			throw new \XYException(__METHOD__,-1000);
		elseif (!$isIgnoreEmpty)
		{
			if (empty($tmp))
				throw new \XYException(__METHOD__,-1503);
		}

		return 1;
	}





	/**
	 * 新建  调整应收款、应付款
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param bool $isDeleteOrder 当Order::deleteOrder()调用时，传入true，将会把status置为91，代表红冲单据 @internal 
	 *
	 * @param string $cid 要调整的往来单位id
	 * @param string $class 单据类别
	 * @param string $name 摘要
	 * @param double $income 金额>0
	 * @param string $remark 备注
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @return unsigned_int 成功返回oid > 0
	 * @throws \XYException
	 */
	public function createAdjustAROrAP(array $data = null,$isAlreadyStartTrans = false,$isDeleteOrder = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$dbCompany = D('Company');

			$tmp_reg_time = null;
			if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);
			if (!$this->field('cid,class,name,income,remark')->create($data,self::OrderModel_createAdjustAROrAP))
				throw new \XYException(__METHOD__,$this->getError());
			
			//必须放在create下面，因为create会自动创建一个时间
			if (isset($tmp_reg_time))
			{
				if($this->check_unix_date($tmp_reg_time))
				{
					$this->reg_time = $tmp_reg_time;
					$this->update_time = $tmp_reg_time;	
				}else
				{
					throw new \XYException(__METHOD__,-1038);
				}
			}

			$this->operator_uid = session('user_auth.uid');
			$this->operator_name = session('user_auth.name');

			$this->history_balance = 0;
			$this->history_balance = $dbCompany->getBalance(array('cid'=>$this->cid),true,1);

			// if ($this->income > 0)
			// 	;
			// else//如果大于0则不涉及到因扣减的过多，导致应收变应付，应付变应收这种“师出无名”的状况
			// {
			// 	if ( $this->income > abs($this->history_balance) )
			// 		throw new \XYException(__METHOD__,-1033);
			// }

			//income必然>0
			if ($this->class == 5)
				$this->remain = $this->income;
			else
				$this->remain = 0 - $this->income;

			$this->history         = $this->writeHistory(0,'add',$this->update_time);
			$this->cid_name        = $dbCompany->getCompanyName($this->cid,1);
			if ($isDeleteOrder)
				$this->status = 92;
			else
				$this->status = 1;

			$balanceChangeValue = 0;
			if ($this->class == 5)
			{
				$balanceChangeValue = 0 - $this->income;
				$this->sn           = $this->getNextSn('AAR');
			}
			elseif ($this->class == 6)
			{
				$balanceChangeValue = $this->income;
				$this->sn           = $this->getNextSn('AAP');
			}

			$backup = $this->data;

			$oid = $this->add();
			if ($oid > 0)
			{
				$dbCompany->setBalanceInOrder($backup['cid'],$balanceChangeValue,1);

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $oid;
			}
			else
				throw new \XYException(__METHOD__,-1000);
		}catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
	}

}
