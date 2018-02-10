<?php
namespace Home\Model;
use Think\Model;



/**
 * 仓库Model.
 *
 * # 单据类别
 * - 参考Order类的文档
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class WarehouseModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('sto_id', 'isUnsignedInt', -2006, self::MUST_VALIDATE, 'function',self::MODEL_INSERT),//sto_id不合法
		array('sto_id', 'isUnsignedInt', -2006, self::EXISTS_VALIDATE, 'function',self::MODEL_UPDATE),//sto_id不合法
		array('new_sto_id', 'isUnsignedInt', -2007, self::EXISTS_VALIDATE, 'function',self::MODEL_INSERT),//sto_id不合法
		array('new_sto_id', 'isUnsignedInt', -2007, self::EXISTS_VALIDATE, 'function',self::MODEL_UPDATE),//sto_id不合法
		array('wid', 'checkDeny_wid', -2001, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//wid不合法
		array('wid', 'checkDeny_wid', -2001, self::EXISTS_VALIDATE, 'callback',self::MODEL_INSERT),//wid不合法
		array('remark', '0,1000', -2003, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //remark长度不合法
		array('check_uid', 'isUnsignedInt', -2004, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//check_uid不合法
		array('class', array(53,54), -2002, self::MUST_VALIDATE, 'in',self::MODEL_INSERT),//class不合法
		array('class', array(53,54), -2002, self::EXISTS_VALIDATE, 'in',self::MODEL_UPDATE),//class不合法

		//MODEL_CREATE_DRAFT
		array('sto_id', 'isUnsignedInt', -2006, self::MUST_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//sto_id不合法
		array('new_sto_id', 'isUnsignedInt', -2007, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//sto_id不合法
		array('wid', 'checkDeny_wid', -2001, self::EXISTS_VALIDATE, 'callback',self::MODEL_CREATE_DRAFT),//wid不合法
		array('remark', '0,1000', -2003, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //remark长度不合法
		array('check_uid', 'isUnsignedInt', -2004, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//check_uid不合法
		array('class', array(53,54), -2002, self::MUST_VALIDATE, 'in',self::MODEL_CREATE_DRAFT),//class不合法

		//MODEL_DELETE_DRAFT
		array('wid', 'checkDeny_wid', -2001, self::MUST_VALIDATE, 'callback',self::MODEL_DELETE_DRAFT),//wid不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),

		//MODEL_CREATE_DRAFT
		array('admin_uid','getAdminUid',self::MODEL_CREATE_DRAFT,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_CREATE_DRAFT),
		array('update_time', NOW_TIME, self::MODEL_CREATE_DRAFT),

		//MODEL_DELETE_DRAFT
		array('admin_uid','getAdminUid',self::MODEL_DELETE_DRAFT,'function'),//填入所属创建者uid
		array('update_time', NOW_TIME, self::MODEL_DELETE_DRAFT),
	);



	/**
	 * 写操作记录字符串.
	 *
	 * note: 
	 * 1. 如果有地方需要传入$arr，$arr必须初始化为null，这是为了通过“没有任何修改却提交了”的这种情况
	 * 2. 外部必须开启事务
	 * 
	 * @param int $wid 需要写历史的单据主键
	 * @param unsigned_int $check_uid 盘点人uid
	 * @param unsigned_int $check_name 盘点人姓名
	 * @param string $action 动作名称
	 * @param unsigned_int $time 时间戳
	 * @param array $arr 变动的字段的字段名数组，形如a[0][0] = sku_id,a[0][1]是sku_id的真实值，如1
	 *
	 * @return string 可以给人看的操作记录
	 * @throws \XYException
	 */
	protected function writeHistory($wid,$check_uid,$check_name,$action,$time,$arr = array())
	{
		$uid = getUid();
		$name = session('user_auth.name');
		$userSn = M('User')->where(array('uid'=>$uid,'admin_uid'=>getAdminUid()))->getField('sn');
		$checkSn = M('User')->where(array('uid'=>$check_uid,'admin_uid'=>getAdminUid()))->getField('sn');

		if (!is_array($arr))
			throw new \XYException(__METHOD__,-2997);//修改操作没有修改任何字段而导致
		
		switch ($action)
		{
			case 'addStockTaking':{
				$userInfo = '由'.$check_name.'(编号:'.$checkSn.')盘点,于'.date("Y-m-d日H:i:s",$time).'由'.$name.'(编号:'.$userSn.')执行库存调整';
				break;
			}
			case 'addRequisition':{
				$userInfo = '由'.$check_name.'(编号:'.$checkSn.')调拨,于'.date("Y-m-d日H:i:s",$time).'由'.$name.'(编号:'.$userSn.')执行库存调整';
				break;
			}
			case 'edit':{
				if ( ($arr[0][1] === '') || ($arr[0][1] === null) )
						$arr[0][1] = '空';
				$userInfo = '于'.date("Y-m-d日H:i:s",$time).'由'.$name.'(编号:'.$userSn.')修改备注为:'.$arr[0][1];
				break;
			}
			default:
				throw new \XYException(__METHOD__,-2999);
		}

		$preHistory = null;
        $history_data = array();
		if ($wid === 0)
			$preHistory = '';
		else
		{
			$preHistory = M('Warehouse')->where(array('wid'=>$wid,'admin_uid'=>getAdminUid()))->lock(true)->getField('history');
            $history_data = unserialize($preHistory);
			if (empty($preHistory))
				throw new \XYException(__METHOD__,-2001);
		}
		
        $history_data[] = $userInfo;
        $re = serialize($history_data);

		if ( strlen($re) >= C('DATABASE_HISTORY_MAX_LENGTH') )
			throw new \XYException(__METHOD__,-2998);

		return $re;
	}



	/**
	 * 新建报溢报损表。
	 * @deprecated v0.2.70
	 * @param string $class 单据类别
	 * @param string $remark 备注
	 * @param string $check_uid 盘点人uid
	 * @param json $cart 购物车内容，购物车格式如下：
	 * @cart json:
	 * 	mini:{"data":[{"sku_id":1,"spu_name":"三黄鸡","spec_name":"10只","quantity":1,"unitPrice":123},{"sku_id":2,"spu_name":"三黄鸡","spec_name":"15只","quantity":2,"unitPrice":223}]}
	 * 	展开:
	 *		{
	 *			"data":
	 *			[
	 *				{
	 *					"sku_id":1,
	 *					"spu_name":"三黄鸡",
	 *					"spec_name":"10只",
	 *					"quantity":1,
	 *					"unitPrice":123
	 *				},
	 *				{
	 *					"sku_id":2,
	 *					"spu_name":"三黄鸡",
	 *					"spec_name":"15只",
	 *					"quantity":2,
	 *					"unitPrice":223
	 *				}
	 *			]
	 *		}
	 * 
	 * @return unsigned_int 成功返回wid > 0
	 * @return <=0 错误编码，查看全局错误编码信息
	 */
	public function createOverflowOrLoss()
	{
	// 	$this->startTrans(__METHOD__);

	// 	if (!$this->field('class,remark,check_uid')->create(I('param.'),self::MODEL_INSERT))
	// 	{
	// 		$this->rollback(__METHOD__);
	// 		return $this->getError();
	// 	}
		
	// 	$this->operator_uid = session('user_auth.uid');
	// 	$this->operator_name = session('user_auth.name');
	// 	$this->check_name = D('User')->getUserInfo_name($this->check_uid);
	// 	if (empty($this->check_name))
	// 	{
	// 		$this->rollback(__METHOD__);
	// 		return -3;
	// 	}
	// 	//开始处理购物车
	// 	$this->value = 0;
	// 	$this->num = 0;
	// 	$cartTmp = json_decode(I('param.cart','',''),true);
	// 	if (empty($cartTmp['data'])) 
	// 	{
	// 		$this->rollback(__METHOD__);
	// 		return -2050;
	// 	}
	// 	foreach ($cartTmp['data'] as $key => $value)
	// 	{
	// 		if (	isUnsignedInt($value['quantity']) &&
	// 				isNonegativeReal($value['unitPrice']) &&
	// 				(!empty($value['spu_name']))  &&
	// 				(!empty($value['spec_name']))
	// 				// $this->checkDeny_skuId_adminUid_stock($value['sku_id'],$value['quantity'])在下面更改库存的时候一起检车了
	// 			)
	// 		{
	// 			$cart[$key]['sku_id'] = $value['sku_id'];
	// 			$cart[$key]['spu_name'] = $value['spu_name'];
	// 			$cart[$key]['spec_name'] = $value['spec_name'];
	// 			$cart[$key]['quantity'] = $value['quantity'];
	// 			$cart[$key]['unitPrice'] = roundDouble($value['unitPrice']);
	// 			$cart[$key]['pilePrice'] = roundDouble($cart[$key]['quantity'] * $cart[$key]['unitPrice']);
	// 			$this->value += $cart[$key]['pilePrice'];
	// 			$this->num += $cart[$key]['quantity'];
	// 		}
	// 		else
	// 		{
	// 			$this->rollback(__METHOD__);
	// 			return -2050;
	// 		}
	// 	}
	// 	if (empty($cart))
	// 	{
	// 		$this->rollback(__METHOD__);
	// 		return -2050;
	// 	}
		
	// 	//更改库存 TODO:批量更新、查询单价时加锁、setInc可以么？
	// 	foreach ($cart as $key => $value)
	// 	{
	// 		if ( ($this->class == 52) )
	// 			$value['quantity'] = 0 - $value['quantity'];

	// 		$tmpReturn = D('Sku')->where(array('sku_id'=>$value['sku_id'],'admin_uid'=>$this->admin_uid,'status'=>1))->setInc('stock',$value['quantity']);

	// 		if ( empty($tmpReturn) )
	// 		{
	// 			$this->rollback(__METHOD__);
	// 			return ('-2051:'.$value['sku_id']);
	// 		}
	// 	}

	// 	$this->cart = serialize($cart);
	// 	$this->history = $this->writeHistory(0,$this->check_uid,$this->check_name,'add',$this->update_time);
	// 	if (is_numeric($this->history))
	// 	{
	// 		$this->rollback(__METHOD__);
	// 		if ($this->history === -2998)
	// 			return 1;//这是因为修改操作没有修改任何字段而导致
	// 		else
	// 			return $this->history;
	// 	}

	// 	$wid = $this->add();
	// 	if ($wid > 0)
	// 	{
	// 		$this->commit(__METHOD__);
	// 		return $wid;
	// 	}
	// 	else
	// 	{
	// 		$this->rollback(__METHOD__);
	// 		return 0;
	// 	}
	}



	/**
	 * 编辑报溢报损单据信息。
	 * @api
	 * @deprecated
	 * @param mixed|null $data POST的数据
	 * @param unsigned_int $wid
	 * @param string $remark 备注
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function editOverflowOrLoss(array $data = null)
	{
	// 	try
	// 	{
	// 		if (!$this->field('wid,remark')->create($data,self::MODEL_UPDATE))
	// 			throw new \XYException(__METHOD__,$this->getError());

	// 		$arr = null;//为了写操作记录
	// 		if (isset($this->remark))
	// 			$arr[] = array('remark',$this->remark);
	// 		else
	// 			return 1;//没有做任何修改也能通过

	// 		//写历史记录
	// 		$tmpInfo = $this->where(array('wid'=>$this->wid,'admin_uid'=>$this->admin_uid))->getField('history');
	// 		$tmpHistoryReturn = $this->writeHistory(strlen($tmpInfo),null,null,'edit',$this->update_time,$arr);//$arr必须初始化为null，这是为了通过“没有任何修改却提交了”的这种情况
	// 		$this->history = $tmpInfo.$tmpHistoryReturn;

	// 		//更新
	// 		$tmp = $this->where(array('wid'=>$this->wid,'admin_uid'=>$this->admin_uid))->setField(array('remark'=>$this->remark,'history'=>$this->history));
	// 		if ($tmp > 0)
	// 			return $tmp;
	// 		else
	// 			throw new \XYException(__METHOD__,-2000);
	// 	}catch(\XYException $e)
	// 	{
	// 		$e->rethrows();
	// 	}	
	}


	/**
	 * 查询单个库存单据
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param unsigned_int $wid 要查询的那个单据的主键
	 *
	 * @return array 数据库中的一行
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 0.2
	 * @date    2016-06-12
	 */
	public function get_(array $data = null)
	{
		if (!$this->field('wid')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpInfo = $this->where(array('wid'=>$this->wid,'admin_uid'=>session('user_auth.admin_uid')))->find();
		$stoNameList = D('Storage')->getStoName($isInstallSql);
		$tmpInfo['sto_name'] = $stoNameList[$tmpInfo['sto_id']];
		$tmpInfo['new_sto_name'] = $stoNameList[$tmpInfo['new_sto_id']];

		if (empty($tmpInfo))
			throw new \XYException(__METHOD__,-2001);
		
		//将购物车换回json
		if ($tmpInfo['status'] != 99)//系统单据没有购物车
		{
			if ( ($tmpInfo['status'] == 100) && (empty($tmpInfo['cart'])) )//草稿单可以没有购物车
				;
			else
			{
				$tmpSerialize = null;
				$tmpSerialize = unserialize($tmpInfo['cart']);
				if ($tmpSerialize === false)
						throw new \XYException(__METHOD__,-2050);
				$tmpInfo['cart'] = $tmpSerialize;
			}
		}
		if (!empty($tmpInfo['history']))
            $tmpInfo['history'] = unserialize($tmpInfo['history']);

		return $tmpInfo;
	}



	/**
	 * 编辑单据信息。
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $wid
	 * @param string $remark 备注
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function edit_(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if (!$this->field('wid,remark')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());

			$arr = null;//为了写操作记录
			if (isset($data['remark']))
				$arr[] = array('remark',$this->remark);
			else
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return 1;//没有做任何修改也能通过
			}

			//写历史记录
			$this->history = $this->writeHistory($this->wid,null,null,'edit',$this->update_time,$arr);//$arr必须初始化为null，这是为了通过“没有任何修改却提交了”的这种情况

			//更新
			$tmp = $this->where(array('wid'=>$this->wid,'admin_uid'=>$this->admin_uid))->setField(array('remark'=>$this->remark,'history'=>$this->history));
			if ($tmp > 0)
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $tmp;
			}
			else
				throw new \XYException(__METHOD__,-2000);
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
	 * 新建盘点单草稿.
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $wid 为空是新建草稿，有值是修改草稿
	 * @param unsigned_int $sto_id 仓库id
	 * @param unsigned_int $new_sto_id 新仓库id
	 * @param string $remark 备注
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @param string $check_uid 盘点人uid，注意：如果没有的话请传0,不要不传
	 * @param json $cart 购物车内容，购物车格式如下：
	 * @example $cart json:
	 *		{
	 *			"data":
	 *			[
	 *				{
	 *		  			"sku_id":"16",
	 *		     		"quantity":14,
	 *		       		"cat_name":"青海省",
	 *		  	      	"spec_name":"71只装",
	 *		  	       	"spu_name":"紫水晶色",
	 *		  	        "unit_price":499.08,
	 *		  	        "isedit":"true"  //变价调拨,平价调拨不用传
	 *		  	    },
	 *		  	    {
	 *		  			"sku_id":"16",
	 *		     		"quantity":14,
	 *		       		"cat_name":"青海省",
	 *		  	      	"spec_name":"71只装",
	 *		  	       	"spu_name":"紫水晶色",
	 *		  	        "unit_price":499.08
	 *		  	    }
	 *		  	]
	 *		 }
	 * @example mini: {"data":[{"sku_id":"16","quantity":14,"cat_name":"青海省","spec_name":"71只装","spu_name":"紫水晶色","unit_price":499.08,"isedit":"true"}]}
	 * @return unsigned_int 成功返回wid > 0（新建）或1（修改）
	 * @throws \XYException
	 */
	public function createStockTakingOrRequisitionDraft(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			log_("data",$data,$this);
			$tmp_reg_time = null;
			if(isset($data['reg_time']) )
				$tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);
			if (!$this->field('class,sto_id,wid,remark,check_uid,new_sto_id')->create($data,self::MODEL_CREATE_DRAFT))
				throw new \XYException(__METHOD__,$this->getError());

			if( !empty($tmp_reg_time) )
			{
				if($this->check_unix_date($tmp_reg_time))
				{
					$this->reg_time = $tmp_reg_time;
					$this->update_time = $tmp_reg_time;
				}else
				{	
					throw new \XYException(__METHOD__,-2005);
				}
			}
			
			if (!empty($this->check_uid))
				$this->check_name = D('User')->getUserInfo_name(array('uid'=>$this->check_uid));

			$this->status = 100;


			//开始处理购物车
			$paramCartTmp = null;
			$paramCartTmp = I('param.cart','','');
			if ( empty($paramCartTmp) && (!empty($data['cart'])) )
				$cartTmp = $data['cart'];
			else
				$cartTmp = json_decode($paramCartTmp,true);
			if ( empty($cartTmp) && ( (!empty($paramCartTmp)) || (!empty($data['cart'])) ) )//I函数不为空或$data传入的不为空，而cartTmp为空，则转换出错
				throw new \XYException(__METHOD__,-2050);

			$cart = null;
			foreach ($cartTmp['data'] as $key => $value)
			{
				if (	isUnsignedInt($value['sku_id']) &&
						isNonegativeReal($value['quantity'])
					)
				{
					$cart[$key]['sku_id'] = I('data.sku_id/d',0,'htmlspecialchars',$value);
					$cart[$key]['quantity'] = xyround(I('data.quantity/f',0,'htmlspecialchars',$value));
					$tmpSkuInfo = null;
					$tmpSkuInfo = M('Sku')->where(array('sku_id'=>$value['sku_id'],'admin_uid'=>$this->admin_uid,'status'=>1))->lock(true)->field('sku_id,cat_name,spu_name,spec_name')->find();
					$tmpSkuStoInfo = D('SkuStorage')->get_(array('sku_id'=>$value['sku_id'],'sto_id'=>$this->sto_id));

					$cart[$key]['spu_name']    = $tmpSkuInfo['spu_name'];
					$cart[$key]['spec_name']   = $tmpSkuInfo['spec_name'];
					$cart[$key]['cat_name']	   = $tmpSkuInfo['cat_name'];
					$cart[$key]['unit_price']  = $tmpSkuStoInfo['unit_price'];

					//变价调拨单
					if($this->class == 54 && isset($value['isedit']) )
					{
						if($value['isedit']=='true' && isset($value['unit_price']) &&isNonegativeReal($value['unit_price']))
						{
							$cart[$key]['isedit'] = 1;
							$cart[$key]['unit_price'] = xyround(I('data.unit_price/f',0,'htmlspecialchars',$value));
						}
						else
							throw new \XYException(__METHOD__,-2050);
					}
				}
				else
					throw new \XYException(__METHOD__,-2050);
			}
			if ( empty($cartTmp) && ( (!empty($paramCartTmp)) || (!empty($data['cart'])) ) )
				throw new \XYException(__METHOD__,-2050);

			if (!empty($cart))
			{
				$this->cart = serialize($cart);
				if (empty($this->cart))
					throw new \XYException(__METHOD__,-2050);
			}

			log_("this->data",$this->data,$this);
			if (empty($this->wid))
			{
				$this->sn = $this->getNextSn('DRA');
				$wid = $this->add();
				if ($wid > 0)
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return $wid;
				}
			}
			else
			{
				$wid = $this->save();
				if ( ($wid !== false) && ($wid !== null) )
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}
			
			throw new \XYException(__METHOD__,-2000);
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
	 * 删除单个草稿单据
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $wid 要删除的那个单据的主键
	 *
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteDraft(array $data = null)
	{
		if (!$this->field('wid')->create($data,self::MODEL_DELETE_DRAFT))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('wid'=>$this->wid,'admin_uid'=>session('user_auth.admin_uid')))->delete();
		
		if (empty($tmp))
			throw new \XYException(__METHOD__,-2501);

		return 1;
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
	public function deleteWarehouseOrder(array $data = null,$isAlreadyStartTrans = false)
	{
		// try
		// {
		// 	if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);


		// 	if (!$this->field('wid')->create($data,self::WarehouseModel_deleteWarehouseOrder))
		// 		throw new \XYException(__METHOD__,$this->getError());

		// 	$rowInfo = $this->where(array('wid'=>$this->wid,'admin_uid'=>session('user_auth.admin_uid')))->find();
			
		// 	if (empty($rowInfo))
		// 		throw new \XYException(__METHOD__,-2001);

		// 	if ($rowInfo['class'] == 53)
		// 		;
		// 	else
		// 		throw new \XYException(__METHOD__,-2002); 
			
		// 	//将影响复原
		// 	$cart = unserialize($data['cart']);
		// 	if ($cart === false)
		// 		throw new \XYException(__METHOD__,-2050);
			
		// 	log_("deleteWarehouseOrder_cart",$cart,$this);

		// 	$this->createOrder(array(
		// 					'class'        => $data['class'],
		// 					'cid'          => $data['cid'],
		// 					'contact_name' => $data['contact_name'],
		// 					'mobile'       => $data['mobile'],
		// 					'park_address' => $data['park_address'],
		// 					'car_license'  => $data['car_license'],
		// 					'off'          => $data['off'],
		// 					'cash'         => $data['cash'],
		// 					'bank'         => $data['bank'],
		// 					'online_pay'   => $data['online_pay'],
		// 					'remark'       =>'因作废单据'.$data['sn'].'所产生的红冲单据',
		// 					'status'       => 91,
		// 					'cart'         => array('data'=>$cart),
		// 				),true,false);

		// 	if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
		// }
		// catch(\XYException $e)
		// {
		// 	if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
		// 	$e->rethrows();
		// }
		// catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		// {
		// 	if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
		// 	throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		// }
		
	}

	/**
	 * 新建盘点单、调拨单.
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param bool $isSys 是否是系统调用。这里会在GeneratorController::genWarehouse_StockTaking()生成数据时调用为true
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 *
	 * @param unsigned_int $wid 草稿用：如果有wid则为草稿转为开单，应删除此wid对应的草稿。
	 * @param unsigned_int $sto_id 原仓库id
	 * @param unsigned_int $new_sto_id 新仓库id
	 * @param unsigned_int $class 单据类型
	 * @param string $remark 备注
	 * @param unsigned_int $check_uid 盘点人uid
	 * @param string $lockShopToken 上锁时服务器下发的token
	 * @param json $cart 购物车内容，购物车格式如下：
	 * @example $cart json:
	 *		{
	 *			"data":
	 *			[
	 *				{
	 *					"sku_id":1,
	 *					"quantity":1,
	 *					"unit_price":155,
	 *					"isedit":"true"  //变价调拨,平价调拨不用传
	 *				},
	 *				{
	 *					"sku_id":2,
	 *					"quantity":2,
	 *				}
	 *			]
	 *		}
	 * @example mini: {"data":[{"sku_id":1,"quantity":1,"unit_price":155,"isedit":true},{"sku_id":2,"quantity":2,}]}
	 * 
	 * @return unsigned_int 成功返回wid > 0
	 * @throws \XYException
	 */
	public function createStockTakingOrRequisition(array $data = null,$isAlreadyStartTrans = false,$isSys=false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			if($this->isVisitor()) $isSys = true; //判断是否为临时访客
			if(!$isSys)  //不是临时访客或生成假数据时调用时验证商店锁和token
			{
				//判断有没有商店锁
				$shopLockStatus = D('Config')->getLockShopStatus(true);
				if ($shopLockStatus['status'] === 0)
					throw new \XYException(__METHOD__,-10506);
				else
					D('Config')->lockShopTokenIsRight($data['lockShopToken'],true);//核对token是否正确
			}

			$dbSKU = D('Sku');
			$dbSkuSto = D('SkuStorage');
			$tmp_reg_time = null;
			if(isset($data['reg_time']))
				$tmp_reg_time =  I('data.reg_time/d',0,'htmlspecialchars',$data);
			// log_("data",$data,$this);
			if(!$this->field('sto_id,wid,remark,check_uid,new_sto_id,class')->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());

			if(isset($tmp_reg_time))
			{

				if(isUnsignedInt($tmp_reg_time))
				{
					$this->reg_time = $tmp_reg_time;
					$this->update_time = $tmp_reg_time;
				}else
				{	
					throw new \XYException(__METHOD__,-2005);
				}
			}

			if (isset($data['wid']))//如果是草稿单据转来的，则记录id，在创建成功后删除草稿单据
			{
				$draftId = $this->wid;
				unset($this->wid);
			}

			$this->operator_uid = session('user_auth.uid');
			$this->operator_name = session('user_auth.name');

			try{
				$this->check_name = D('User')->getUserInfo_name(array('uid'=>$this->check_uid));
			}catch(\XYException $e)
			{
				throw new \XYException(__METHOD__,-2502);
			}

			log_("this->data",$this->data,$this);
			$tmpCart = I('param.cart','','');
			if(empty($tmpCart))
				$cartTmp = $data['cart'];
			else
				$cartTmp = json_decode(I('param.cart','',''),true);
			log_("cartTmp",$cartTmp,$this);
			if(empty($cartTmp['data']))
				throw new \XYException(__METHOD__,-2050);

			$sku_id_array = null;
			foreach ($cartTmp['data'] as $key => $value)
			{
				if (	isUnsignedInt($value['sku_id']) &&
						isPositiveReal($value['quantity'])
						// $this->checkDeny_skuId_adminUid_stock($value['sku_id'],$value['quantity'])在下面更改库存的时候一起检查了
					)
				{
					$cart[$key]['sku_id'] = I('data.sku_id/d',0,'htmlspecialchars',$value);
					$sku_id_array[] = $cart[$key]['sku_id'];
					$cart[$key]['quantity'] = xyround(I('data.quantity/f',0,'htmlspecialchars',$value));
					//变价调拨单
					if($this->class == 54 && isset($value['isedit']) && isset($value['unit_price']))
					{
						if($value['isedit']=='true' && isNonegativeReal($value['unit_price']))
						{
							$cart[$key]['isedit'] = 1;
							$cart[$key]['unit_price'] = xyround(I('data.unit_price/f',0,'htmlspecialchars',$value));
						}
						else
							throw new \XYException(__METHOD__,-2050);
					}
				}
				else
					throw new \XYException(__METHOD__,-2050);
			}
			if (empty($cart))
				throw new \XYException(__METHOD__,-2050);

			$skuInfo = $dbSKU-> getList($sku_id_array,1,true);
			//要不要上锁？
			$skuStoInfoBef = $dbSkuSto->where(
				array(
					'sku_id'=>array('in',$sku_id_array),
					'status'=>1,
					'sto_id'=>$this->sto_id,
					'admin_uid'=>getAdminUid()
					 )
			)->lock(true)->getField('sku_id,sku_storage_id,sto_id,stock,unit_price');//调拨/盘点前的仓库对应的sku信息
			if($skuStoInfoBef===null )
				throw new \XYException(__METHOD__,-2050);
			elseif($skuStoInfoBef===false)
				throw new \XYException(__METHOD__,-2000);
			log_("_sql",$dbSkuSto->_sql(),$this);
			if($this->class == 54)
			{
				$skuStoInfoAf = $dbSkuSto->where(
				array(
					'sku_id'=>array('in',$sku_id_array),
					'status'=>1,
					'sto_id'=>$this->new_sto_id,
					'admin_uid'=>getAdminUid()
					 )
				)->lock(true)->getField('sku_id,sku_storage_id,sto_id,stock,unit_price');//调拨后的仓库对应的sku信息
				log_("_sql",$dbSkuSto->_sql(),$this);
				if($skuStoInfoAf===false)
					throw new \XYException(__METHOD__,-2000);
			}
			
			//更改库存 TODO:批量更新
			$this->value = 0;//调拨总金额
			$this->num = 0;//调拨单总数量
			log_("skuStoInfoBef",$skuStoInfoBef,$this);
			log_("skuStoInfoAf",$skuStoInfoAf,$this);
			foreach ($cart as $key => $value)
			{
				//为查询的时候保存历史快照
				$cart[$key]['spu_id']     = $skuInfo[$value['sku_id']]['spu_id'];
				$cart[$key]['spu_name']   = $skuInfo[$value['sku_id']]['spu_name'];
				$cart[$key]['spec_name']  = $skuInfo[$value['sku_id']]['spec_name'];
				$cart[$key]['cat_id']     = $skuInfo[$value['sku_id']]['cat_id'];
				$cart[$key]['cat_name']   = $skuInfo[$value['sku_id']]['cat_name'];
				$tmpSkuID = $value['sku_id'];
				$cart[$key]['cu_stock_bef']     = $skuStoInfoBef[$tmpSkuID]['stock']; //盘点/调拨调拨前原仓库库存
				//盘点单
				if($this->class == 53)
				{
					$cart[$key]['unit_price']     = $skuStoInfoBef[$tmpSkuID]['unit_price']; 
					$this->num = xyadd($this->num,abs(xysub($cart[$key]['quantity'],$cart[$key]['cu_stock_bef'],0)) );
					$this->value = xyadd($this->value,xymul(xysub($cart[$key]['quantity'],$cart[$key]['cu_stock_bef'],0),$cart[$key]['unit_price'],0));
					$cart[$key]['cu_stock_af']     = $cart[$key]['quantity'];
					$editDataBef = null;
					$editDataBef = array(
							'sku_storage_id' => $skuStoInfoBef[$tmpSkuID]['sku_storage_id'],
							'stock'=> $cart[$key]['quantity'],
							'update_time'=>$this->update_time
							);
				}
				//调拨单
				if($this->class == 54)
				{
					if(!isset($cart[$key]['isedit']))
						$cart[$key]['unit_price'] = $skuStoInfoBef[$tmpSkuID]['unit_price'];//平价调拨单
					$cart[$key]['old_unit_price'] = $skuStoInfoBef[$tmpSkuID]['unit_price'];
					//调出仓库目前库存
					$cart[$key]['cu_stock_af']     = xysub($cart[$key]['cu_stock_bef'],$cart[$key]['quantity']);

					$editDataBef = null;
					$editDataAf = null;
					if(empty($skuStoInfoAf[$value['sku_id']]))
					{
						//调入仓库库存原库存
						$cart[$key]['new_stock_bef'] = 0;
						//调入仓库目前库存
						$cart[$key]['new_stock_af'] = xyadd($cart[$key]['new_stock_bef'] , $cart[$key]['quantity']);
						$editDataAf = array(
								'stock'=> $cart[$key]['new_stock_af'],
								'unit_price'=>$cart[$key]['unit_price'],
								'update_time'=>$this->update_time,
								'reg_time'=>$this->reg_time,
								'sto_id'=> $this->new_sto_id,
								'sku_id'=> $value['sku_id']
							);
					}
					else
					{
						//调入仓库库存原库存
						$cart[$key]['new_stock_bef'] = $skuStoInfoAf[$tmpSkuID]['stock'];
						//调入仓库目前库存
						$cart[$key]['new_stock_af'] = xyadd($cart[$key]['new_stock_bef'] , $cart[$key]['quantity']);

						$editDataAf = array(
								'sku_storage_id' => $skuStoInfoAf[$tmpSkuID]['sku_storage_id'],
								'stock'=> $cart[$key]['new_stock_af'],
								'unit_price'=>xydiv(xyadd(xymul($skuStoInfoAf[$tmpSkuID]['stock'],$skuStoInfoAf[$tmpSkuID]['unit_price'],0),xymul($cart[$key]['quantity'],$cart[$key]['unit_price'],0),0),xyadd($skuStoInfoAf[$tmpSkuID]['stock'],$cart[$key]['quantity'],0)),
								'update_time'=>$this->update_time
							);
					}
					$editDataBef = array(
							'sku_storage_id' => $skuStoInfoBef[$tmpSkuID]['sku_storage_id'],
							'stock'=> $cart[$key]['cu_stock_af'] ,
							'update_time'=>$this->update_time
							);
					//更新
					$this->num = xyadd( $this->num , $cart[$key]['quantity'] );
					$this->value = xyadd( $this->value, xymul( $cart[$key]['quantity'] , $cart[$key]['unit_price'] ,0) );
				}
				log_("editDataBef",$editDataBef,$this);
				log_("editDataAf",$editDataAf,$this);
				//调拨金额
				$cart[$key]['pilePrice']      = xymul( $cart[$key]['quantity'] , $cart[$key]['unit_price'] );
				//
				$tmpReturn1 = $dbSkuSto->updateSkuSto($editDataBef);
				log_("dbSkuSto->_sql",$dbSkuSto->_sql(),$this);
				if ($tmpReturn1 === null )
					throw new \XYException($value['sku_id'].($this->sto_id),-2051);
				elseif ($tmpReturn1 === false )
					throw new \XYException($value['sku_id'].($this->sto_id),-2000);	

				if($this->class == 54)
				{
					$tmpReturn2 = $dbSkuSto->updateSkuSto($editDataAf);
					log_("dbSkuSto->_sql",$dbSkuSto->_sql(),$this);
					if ( $tmpReturn2 === null)
						throw new \XYException($value['sku_id'].($this->sto_id),-2051);
					elseif ( $tmpReturn2 === false)
						throw new \XYException($value['sku_id'].($this->sto_id),-2000);	
				}
			}
			log_("cart",$cart,$this);
			$this->cart = serialize($cart);
			if (empty($this->cart))
				throw new \XYException(__METHOD__,-2050);
			
			if($this->class==53)
			{
				$this->sn = $this->getNextSn('WPD');
				$this->history = $this->writeHistory(0,$this->check_uid,$this->check_name,'addStockTaking',$this->update_time);
			}	
			else
			{
				$this->sn = $this->getNextSn('WRN');
				$this->history = $this->writeHistory(0,$this->check_uid,$this->check_name,'addRequisition',$this->update_time);
			}
			$this->status = 1;

			$backup = $this->data;
			$wid = $this->add();
			if ($wid > 0)
			{
				//如果是草稿单据转来的，则删除草稿单据
				if (isset($data['wid']))
					$this->deleteDraft(array('wid'=>$draftId));

				foreach ($cart as $key => $value)
				{
					D('SkuBill')->create_(array(
							'sku_id'      => $value['sku_id'],
							'spu_id'      => $value['spu_id'],
							'wid'         => $wid,
							'bill_class'  => $backup['class'],
							'bill_status' => $backup['status'],
							'reg_time'    => $backup['reg_time'],
						));
				}

				if (!$isSys)
					D('Config')->unlockShop(true);//打开商品锁

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $wid;
			}
			else
				throw new \XYException(__METHOD__,-2000);
		}catch(\XYException $e)
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


}