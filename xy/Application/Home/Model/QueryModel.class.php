<?php
namespace Home\Model;
use Think\Model;

/**
 * 查询类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class QueryModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('page', 'isUnsignedInt', -9001, 
			self::MUST_VALIDATE, 'function',self::MODEL_QUERY),//page不合法
		array('pline', 'isUnsignedInt', -9002,
			self::MUST_VALIDATE, 'function',self::MODEL_QUERY),//pline不合法
		array('reg_st_time', '10,10', -9004,
			self::EXISTS_VALIDATE,'length',self::MODEL_QUERY),//reg_st_time不合法
		array('reg_st_time', 'check_unix_date', -9004,
			self::EXISTS_VALIDATE,'callback',self::MODEL_QUERY),//reg_st_time不合法
		array('reg_end_time', '10,10', -9005,
			self::EXISTS_VALIDATE, 'length',self::MODEL_QUERY),//reg_end_time不合法
		array('reg_end_time', 'check_unix_date', -9005,
			self::EXISTS_VALIDATE, 'callback',self::MODEL_QUERY),//reg_end_time不合法
		array('oid', 'isUnsignedInt', -9006,
			self::EXISTS_VALIDATE, 'function',self::MODEL_QUERY),//oid不合法
		array('wid', 'isUnsignedInt', -9007,
			self::EXISTS_VALIDATE, 'function',self::MODEL_QUERY),//wid不合法
		array('fid', 'isUnsignedInt', -9008,
			self::EXISTS_VALIDATE, 'function',self::MODEL_QUERY),//fid不合法
		array('operator_uid', 'isUnsignedInt', -9009,
			self::EXISTS_VALIDATE, 'function',self::MODEL_QUERY),//operator_uid不合法
		array('cid', 'isUnsignedInt', -9010,
			self::EXISTS_VALIDATE, 'function',self::MODEL_QUERY),//cid不合法

		//MODEL_SEARCH
		array('page', 'isUnsignedInt', -9001, 
			self::MUST_VALIDATE, 'function',self::MODEL_SEARCH),//page不合法
		array('pline', 'isUnsignedInt', -9002,
			self::MUST_VALIDATE, 'function',self::MODEL_SEARCH),//pline不合法
		array('type', 'check_QueryModel_type', -9011, 
			self::MUST_VALIDATE, 'callback',self::MODEL_SEARCH),//type不合法
		array('search', '1,50', -9012, 
			self::MUST_VALIDATE, 'length',self::MODEL_SEARCH), //search长度不合法


		//QueryModel_saleSummary
		array('reg_st_time', '10,10', -9004,
			self::EXISTS_VALIDATE,'length',self::QueryModel_saleSummary),//reg_st_time不合法
		array('reg_st_time', 'check_unix_date', -9004,
			self::EXISTS_VALIDATE,'callback',self::QueryModel_saleSummary),//reg_st_time不合法
		array('reg_end_time', '10,10', -9005,
			self::EXISTS_VALIDATE, 'length',self::QueryModel_saleSummary),//reg_end_time不合法
		array('reg_end_time', 'check_unix_date', -9005,
			self::EXISTS_VALIDATE, 'callback',self::QueryModel_saleSummary),//reg_end_time不合法

		//QueryModel_purchaseSummary
		array('reg_st_time', 'check_unix_date', -9004,
			self::EXISTS_VALIDATE,'callback',self::QueryModel_purchaseSummary),//reg_st_time不合法
		array('reg_end_time', 'check_unix_date', -9005,
			self::EXISTS_VALIDATE, 'callback',self::QueryModel_purchaseSummary),//reg_end_time不合法


		//QueryModel_skuSummary
        array('page','isUnsignedInt',-9001,
            self::MUST_VALIDATE,'function',self::QueryModel_skuSummary),//page不合法
        array('pline', 'isUnsignedInt', -9002,
            self::MUST_VALIDATE, 'function',self::QueryModel_skuSummary),//pline不合法
        array('reg_st_time', '10,10', -9004,
            self::EXISTS_VALIDATE,'length',self::QueryModel_skuSummary),//reg_st_time不合法
        array('reg_st_time', 'check_unix_date', -9004,
            self::EXISTS_VALIDATE,'callback',self::QueryModel_skuSummary),//reg_st_time不合法
        array('reg_end_time', '10,10', -9005,
            self::EXISTS_VALIDATE, 'length',self::QueryModel_skuSummary),//reg_end_time不合法
        array('reg_end_time', 'check_unix_date', -9005,
            self::EXISTS_VALIDATE, 'callback',self::QueryModel_skuSummary),//reg_end_time不合法
        array('sku_id', 'isUnsignedInt',-9016,
            self::MUST_VALIDATE, 'function',self::QueryModel_skuSummary), //sku_id不合法

        //QueryModel_skuChart
        array('sku_id', 'isUnsignedInt',-9016,
            self::MUST_VALIDATE, 'function',self::QueryModel_skuChart), //sku_id不合法

        //QueryModel_operatorChart
        array('operator_uid', 'isUnsignedInt', -9009,
            self::MUST_VALIDATE, 'function',self::QueryModel_operatorChart),//operator_uid不合法

        //QueryModel_operatorSummary
        array('page','isUnsignedInt',-9001,
            self::MUST_VALIDATE,'function',self::QueryModel_operatorSummary),//page不合法
        array('pline', 'isUnsignedInt', -9002,
            self::MUST_VALIDATE, 'function',self::QueryModel_operatorSummary),//pline不合法
        array('reg_st_time', '10,10', -9004,
            self::EXISTS_VALIDATE,'length',self::QueryModel_operatorSummary),//reg_st_time不合法
        array('reg_st_time', 'check_unix_date', -9004,
            self::EXISTS_VALIDATE,'callback',self::QueryModel_operatorSummary),//reg_st_time不合法
        array('reg_end_time', '10,10', -9005,
            self::EXISTS_VALIDATE, 'length',self::QueryModel_operatorSummary),//reg_end_time不合法
        array('reg_end_time', 'check_unix_date', -9005,
            self::EXISTS_VALIDATE, 'callback',self::QueryModel_operatorSummary),//reg_end_time不合法
        array('operator_uid', 'isUnsignedInt', -9009,
            self::MUST_VALIDATE, 'function',self::QueryModel_operatorSummary),//operator_uid不合法

        //QueryModel_queryOrder
        array('operator_uid', 'isUnsignedInt', -9009,
            self::MUST_VALIDATE, 'function',self::QueryModel_queryOrder),//operator_uid不合法
        array('page', 'isUnsignedInt', -9001,
            self::EXISTS_VALIDATE, 'function',self::QueryModel_queryOrder),//page不合法
        array('pline', 'isUnsignedInt', -9002,
            self::EXISTS_VALIDATE, 'function',self::QueryModel_queryOrder),//pline不合法

        //QueryModel_getOperatorInfo
        array('operator_uid', 'isUnsignedInt', -9009,
            self::MUST_VALIDATE, 'function',self::QueryModel_getOperatorInfo),//operator_uid不合法
	);

	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_QUERY,'function'),//填入所属创建者uid
		array('admin_uid','getAdminUid',self::MODEL_SEARCH,'function'),//填入所属创建者uid
	);

	/**
	 * 查询9种单据的统一接口.
	 *
	 * 9种单据分别为：
	 * - 4个交易单：销售单、销售退货单、采购单、采购单
	 * 
	 * - 4个财务单：付款单、收款单、费用单、其他收入单
	 * 
	 * - 1个库存单：盘点单
	 *
	 * - 必填参数：
	 * - @param unsigned_int $page 请求第几页的数据，从1开始计数
	 * - @param unsigned_int $pline 一页多少行
	 * - 选填参数：（“全部”请不要传该字段）
	 * - @param json $filter class、status、remainType过滤条件。
	 *
	 * 			- array $class 单据类别编号：不传代表全部。
	 * 			
	 * 			- array $status 单据状态：不传代表全部，a-可选值：1、2、5、90.  b-未完成传90。c-草稿请用queryDraft查询。
	 * 			
	 * 			- array $remainType 应收应付情况。'1|2|3':1-店铺还需收款的单据；2-店铺还需付款的单据。3-结清 d-不传代表全部。
	 * 			
	 * 			- array $financeType 收款方式情况。'101|102|103':101-有现金收款方式的单据；102-有银行收款方式的单据。103-有网络收款方式的单据。
	 * 			
	 * 			- array $bigClass 大类情况。'111|112|113':111-交易类单据；112-财务类单据。113-其他类单据。
	 * 			
	 * - @param unsigned_int $reg_st_time 创建时间的开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
	 * - @param unsigned_int $reg_end_time 创建时间的结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
	 * - @param bool $isInitialOrderShow 是否显示期初订单,不显示false,显示不要传
	 * - @param unsigned_int $operator_uid
	 * - @param unsigned_int $cid
	 * - 搜索的内容：
	 * - 注：如果有oid、wid、fid任意一项，则其他过滤条件全部忽略，只查主键
	 * - @param unsigned_int $oid
	 * - @param unsigned_int $wid
	 * - @param unsigned_int $fid
	 * 
	 * @param mixed|null $data POST的数据
	 * @example filter:
	 *			{
	 *				//下面两组可选；
	 *				"class":[1,71,73,81,82,83,84,85],//81到85为易企记（财务单据）
	 *				"status":[90],//1-可选值：1、2、5、81、82、83、84、85、90.  2-未完成传90。3-草稿请用queryDraft查询。81_85为易企记状态
	 *				"remainType":[1]
	 *			}
	 * @example filter mini: {"class":[1,71,73],"status":[90],"remainType":[1]}
	 * 
	 * @return array 数据库结果集，其中array[i]为数据库中的一行
	 * @throws \XYException
	 * @api
	 */
	public function query_(array $data = null)
	{
        $filter = $data['filter'];
		if (!$this->field('page,pline,reg_st_time,reg_end_time,oid,wid,fid,operator_uid,cid')->create($data,self::MODEL_QUERY))
			throw new \XYException(__METHOD__,$this->getError());

		if (!isset($data['reg_st_time']))
			unset($this->reg_st_time);
		else
		{
			$tmpDate = null;
			$tmpDate = getdate($this->reg_st_time);
			$this->reg_st_time  = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		}
		if (!isset($data['reg_end_time']))
			unset($this->reg_end_time);
		else
		{
			$tmpDate = null;
			$tmpDate = getdate($this->reg_end_time);
			$this->reg_end_time = mktime(23,59,59,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		}

		if (!isset($data['oid'])) 			unset($this->oid);
		if (!isset($data['wid'])) 			unset($this->wid);
		if (!isset($data['fid'])) 			unset($this->fid);
		if (!isset($data['operator_uid'])) 	unset($this->operator_uid);
		if (!isset($data['cid'])) 			unset($this->cid);
		    $cartTmp = json_decode(I('param.filter','',''),true);
        if (empty($cartTmp))
            $cartTmp = json_decode($filter,true);

		$tmpParamFilter = I('param.filter','','');
		if (!empty($tmpParamFilter))//允许json为空，这里只转换失败时报错
				if ( empty($cartTmp) )
					throw new \XYException(__METHOD__,-9050);

		//设置remainType
		if (!empty($cartTmp['remainType']))
		{
			$remainCondition = '( ';
			foreach ($cartTmp['remainType'] as $value)
			{
				$value = intval($value);
				if ( $this->check_QueryModel_remainType($value) )
				{
					$tmpRemainString = null;

					if ($value === 1)
						$tmpRemainString = '( `remain` > 0 )';
					elseif ($value === 2)
						$tmpRemainString = '( `remain` < 0 )';
					elseif ($value === 3)
						$tmpRemainString = '( `remain` = 0 )';

					if ($remainCondition === '( ')
						$remainCondition = '( '.$tmpRemainString;
					else
						$remainCondition .= ' or '.$tmpRemainString;
				}
				else
					throw new \XYException(__METHOD__,-9003);
			}
			$remainCondition .= ' )';
			log_("remainCondition",$remainCondition,$this);
		}


		//设置financeType
		if (!empty($cartTmp['financeType']))
		{
			$financeTypeCondition = '( ';
			foreach ($cartTmp['financeType'] as $value)
			{
				$value = intval($value);
				if ( $this->check_QueryModel_financeType($value) )
				{
					$tmpString = null;

					if ($value === 101)
						$tmpString = '( `cash` <> 0 )';
					elseif ($value === 102)
						$tmpString = '( `bank` <> 0 )';
					elseif ($value === 103)
						$tmpString = '( `online_pay` <> 0 )';

					if ($financeTypeCondition === '( ')
						$financeTypeCondition = '( '.$tmpString;
					else
						$financeTypeCondition .= ' or '.$tmpString;
				}
				else
					throw new \XYException(__METHOD__,-9014);
			}
			$financeTypeCondition .= ' )';
			log_("financeTypeCondition",$financeTypeCondition,$this);
		}


		//设置bigClass
		if (!empty($cartTmp['bigClass']))
		{
			$bigClassCondition = '( ';
			foreach ($cartTmp['bigClass'] as $value)
			{
				$value = intval($value);
				if ( $this->check_QueryModel_bigClass($value) )
				{
					$tmpString = null;
					
					if ($value === 111)
						$tmpString = '( `class` >= 1 and `class`<= 4)';
					elseif ($value === 112)
						$tmpString = '( (`class` >= 71 and `class` <= 74) or (`class` >= 5 and `class` <= 6) )';
					elseif ($value === 113)
						$tmpString = '( `class` >= 53 and `class` <= 54  )';

					if ($bigClassCondition === '( ')
						$bigClassCondition = '( '.$tmpString;
					else
						$bigClassCondition .= ' or '.$tmpString;
				}
				else
					throw new \XYException(__METHOD__,-9015);
			}
			$bigClassCondition .= ' )';
			log_("bigClassCondition",$bigClassCondition,$this);
		}
		//设置class
		if (!empty($cartTmp['class']))
		{
			$classCondition = '( ';
			foreach ($cartTmp['class'] as $value)
			{
				$value = intval($value);
				if ( $this->checkDeny_query_class($value) )
				{
					if ($classCondition === '( ')
						$classCondition = '( `class`='.$value;
					else
						$classCondition .= ' or `class`='.$value;

					//设置隐藏的class
					// if ($value == 1)
					// 	$classCondition .= ' or `class`=2';
					// elseif ($value == 3)
					// 	$classCondition .= ' or `class`=4';
				}
				else
					throw new \XYException(__METHOD__,-9051);
			}
			$classCondition .= ' )';
			log_("classCondition",$classCondition,$this);
		}

		//设置status
		if (!empty($cartTmp['status']))
		{
			$statusCondition = '( ';
			foreach ($cartTmp['status'] as $value)
			{
				$value = intval($value);
				if ( $this->checkDeny_query_status($value) )
				{
					if ($statusCondition === '( ')
						$statusCondition = '( `status`='.$value;
					else
						$statusCondition .= ' or `status`='.$value;

					//设置隐藏的status
					if ($value == 90)
					{
						$statusCondition .= ' or `status`=2 or `status`=4 or `status`=5 or `status`=6 or `status`=7 or `status`=8 or `status`=9 or `status`=10 or `status`=11 or `status`=12 or `status`=81 or `status`=82 or `status`=83 or `status`= 84 or `status` = 85';
					}
					if ($value == 80)
                    {
                        $statusCondition .= ' or `status`=81 or `status`=82 or `status`=83 or `status`= 84 or `status` = 85';
                    }
				}
				else
					throw new \XYException(__METHOD__,-9052);
			}
			$statusCondition .= ' )';
			log_("statusCondition",$statusCondition,$this);
		}

		$commonCondition = array();
		$orderCondition  = array();
		//base
		
		if(isset($data['isInitialOrderShow']))//判断是否要显示期初订单
		{
			if($data['isInitialOrderShow'] == 'false') //不显示只传false
				$commonCondition['status']     = array('not in',array(89,99,100));
			else
				throw new \XYException(__METHOD__,-9017);
		}
		else //显示期初订单,不传该字段
			$commonCondition['status']     = array('neq',100);

		/*$commonCondition['status']     = array('neq',100);*/
		//必须要在前面，因为下面会有覆盖
		if (!empty($cartTmp['remainType']))
			$orderCondition['_string'] = $remainCondition;

		//class是公用的，status只有99和100是给除order外的其他单据用的（先不管）
		//status for order
		if (!empty($cartTmp['class']))
			$commonCondition['_string'] = $classCondition;
		if ( (!empty($cartTmp['class'])) && (!empty($cartTmp['status'])) )
		{
			unset($commonCondition['status']);
			$commonCondition['_string'] = '( '.$classCondition.' ) AND ( '.$statusCondition.')';
		}
		elseif ( (empty($cartTmp['class'])) && (!empty($cartTmp['status'])) )
		{
			unset($commonCondition['status']);
			$commonCondition['_string'] = $statusCondition;
		}

		if (!empty($cartTmp['financeType']))
		{
			if (empty($commonCondition['_string']))
				$commonCondition['_string'] = $financeTypeCondition;
			else
				$commonCondition['_string'] .= ' AND '.$financeTypeCondition;
		}

		if (!empty($cartTmp['bigClass']))
		{
			if (empty($commonCondition['_string']))
				$commonCondition['_string'] = $bigClassCondition;
			else
				$commonCondition['_string'] .= ' AND '.$bigClassCondition;
		}

		// if ($financeNeedStatus99Tag)
		// {
		// 	if (empty($commonCondition['_string']))
		// 		$financeCondition['_string'] = '`status`=1 or `status`=99';
		// 	else
		// 		$financeCondition['_string'] = '( '.$commonCondition['_string'].' ) AND (`status`=1 or `status`=99)';
		// }
		
		// if ($financeNeedStatus99Tag)//只查草稿不查其他
		// {
		// 	$financeCondition['status'] = 99;
		// }

		//reg_time filter
		if ( isset($this->reg_st_time) && isset($this->reg_end_time) )
		{
			$commonCondition['reg_time']  = array('between',array($this->reg_st_time,$this->reg_end_time));
		}
		elseif ( isset($this->reg_st_time) && (!isset($this->reg_end_time)) )
		{
			$commonCondition['reg_time']  = array('egt',$this->reg_st_time);
		}
		elseif ( (!isset($this->reg_st_time)) && (isset($this->reg_end_time)) )
		{
			$commonCondition['reg_time']  = array('elt',$this->reg_end_time);
		}

		//search
		if (isset($this->operator_uid))
		{
			$commonCondition['operator_uid'] = $this->operator_uid;
		}

		if (isset($this->cid))
		{
			$commonCondition['cid'] = $this->cid;
		}

		if ( isset($this->oid) || isset($this->fid) || isset($this->wid) )//因为要忽略其他条件，所以只能放到最后
		{
			//忽略其他所有条件，只查主键
			$commonCondition           = array();
			$orderCondition            = array();
        /*	$commonCondition['status'] = array('neq',100);*/
			if(isset($data['isInitialOrderShow']))//判断是否要显示期初订单
			{
				if($data['isInitialOrderShow'] == 'false') //不显示只传false
					$commonCondition['status']     = array('not in',array(99,100));
				else
					throw new \XYException(__METHOD__,-9017);
			}
			else //显示期初订单,不传该字段
				$commonCondition['status']     = array('neq',100);

			if (isset($this->oid)) $commonCondition['oid'] = $this->oid;
			if (isset($this->fid)) $commonCondition['fid'] = $this->fid;
			if (isset($this->wid)) $commonCondition['wid'] = $this->wid;
		}

		//base
		$commonCondition['admin_uid'] = getAdminUid();//必须要在最后，因为search里有清空

		//gen sql
		// ->field('oid,class,cid,cid_name,contact_name,mobile,park_address,car_license,value')
		if ( (!empty($orderCondition['_string'])) && (!empty($commonCondition['_string'])) )
		{
			$tmpOrderCondition            = null;
			$tmpOrderCondition            = $commonCondition;
			$tmpOrderCondition['_string'] = $commonCondition['_string'] . ' AND ' . $orderCondition['_string'];
		}
		elseif ( (!empty($orderCondition['_string'])) && (empty($commonCondition['_string'])) )
		{
			$tmpOrderCondition            = null;
			$tmpOrderCondition            = $commonCondition;
			$tmpOrderCondition['_string'] = $orderCondition['_string'];
		}else//其余两种情况，均适用下面的语句
			$tmpOrderCondition            = $commonCondition;

		$OrderSQL = D('Order')
			->where($tmpOrderCondition)
			->buildSql();

		$FinanceSQL = D('Finance')
			->where($commonCondition)
			->buildSql();

		$WarehouseSQL = D('Warehouse')
			->where($commonCondition)
			->buildSql();
		
		if ($orderCondition['_string'])//如果设置了remain，那么就只能筛选order
			$unionSQL = '( '.$OrderSQL.' )';
		else
			$unionSQL = '( '.$OrderSQL.' UNION '.$FinanceSQL.' UNION '.$WarehouseSQL.' )';

		$reData['total_page'] = ceil(M('Query')->table($unionSQL.' tmp_union')->count() / $this->pline);
		if ($reData['total_page'] == 0)
			$reData['total_page'] = 1;
		if ($this->page > $reData['total_page'])
			$this->page = 1;
		$reData['now_page'] = $this->page;
		$reData['data'] = $this
			->table($unionSQL.' tmp_union')
			->order('reg_time desc')
			->page($this->page,$this->pline)
			->select();

		if ($reData['data'] === false)
			throw new \XYException(__METHOD__,-9000);

		$stoNameList = D('Storage')->getStoName($isInstallSql);
		//将购物车换回json
		foreach ($reData['data'] as &$value)
		{
			
			$value['sto_name'] = $stoNameList[$value['sto_id']];
			$value['new_sto_name'] = $stoNameList[$value['new_sto_id']];
			if (!empty($value['cart']))
			{
				$tmpSerialize = null;
				$tmpSerialize = unserialize($value['cart']);
				if ($tmpSerialize === false)
					throw new \XYException(__METHOD__,-9550);
				$value['cart'] = $tmpSerialize;
                if (!empty($tmpSerialize['finance_advice']))
                {
                    $value['finance_advice'] = $tmpSerialize['finance_advice'];
//                    unset($value['cart']['finance_advice']);
                }
                if (!empty($tmpSerialize['boss_advice']))
                {
                    $value['boss_advice'] = $tmpSerialize['boss_advice'];
//                    unset($value['cart']['boss_advice']);
                }
                if($value['class'] == 54)
                {
	                foreach($value['cart'] as $k=>$v)
	                {
	                	$value['cart'][$k]['sto_name'] = $value['sto_name'];
	                	$value['cart'][$k]['sto_id'] = $value['sto_id'];
	                }
                }
			}
			if (!empty($value['history']))
            {
                $value['history'] = unserialize($value['history']);
            }
		}


		return $reData;
	}





	/**
	 * 查询9种单据的  草稿  的统一接口.
	 *
	 * 9种单据分别为：
	 * - 4个交易单：销售单、销售退货单、采购单、采购单
	 * 
	 * - 4个财务单：付款单、收款单、费用单、其他收入单
	 * 
	 * - 1个库存单：盘点单
	 *
	 * @param unsigned_int $page 请求第几页的数据
	 * @param unsigned_int $pline 一页多少行
	 * @param json $filter class过滤条件。
	 * 			- array $class 单据类别编号：不传代表全部。
     *          - array $status  89为易企记单据，100为星云进销存单据不传代表全部
	 * @return array 数据库结果集，其中array[i]为数据库中的一行
	 * @throws \XYException
	 * @api
	 */
	public function queryDraft(array $data = null)
	{
		if (!$this->field('page,pline')->create($data,self::MODEL_QUERY))
			throw new \XYException(__METHOD__,$this->getError());

		$commonCondition              = array();
		$commonCondition['admin_uid'] = getAdminUid();
		$tmpFilter = json_decode(I('param.filter','',''),true);
		$tmpParamFilter = I('param.filter','','');

		if(!empty($tmParamFilter) )
		{
			if( empty($tmpFilter) )
				throw new \XYException(__METHOD__,-9050);
		}

		if(!empty($tmpFilter['class']) )
		{
			$tmp = array();
			foreach ($tmpFilter['class'] as $key => $value) 
			{

				$tmp[] = intval($value);
			}
			$commonCondition['class'] = array('in',$tmp);
		}
		if (!empty($tmpFilter['status']))
        {
            $tmp = array();
            foreach ($tmpFilter['status'] as $key => $value)
            {

                $tmp_status[] = intval($value);
            }
            $commonCondition['status'] = array('in',$tmp_status);
        }
		//gen sql
		$OrderSQL = D('Order')
			->where($commonCondition)
			->buildSql();

		$FinanceSQL = D('Finance')
			->where($commonCondition)
			->buildSql();

		$WarehouseSQL = D('Warehouse')
			->where($commonCondition)
			->buildSql();
		
		$unionSQL = '( '.$OrderSQL.' UNION '.$FinanceSQL.' UNION '.$WarehouseSQL.' )';
		log_("unionSQL",$unionSQL,$this);
		$reData['total_page'] = ceil(M('Query')->table($unionSQL.' tmp_union')->count() / $this->pline);
		if ($reData['total_page'] == 0)
			$reData['total_page'] = 1;
		if ($this->page > $reData['total_page'])
			$this->page = 1;
		$reData['now_page'] = $this->page;
		$reData['data'] = $this
			->table($unionSQL.' tmp_union')
			->order('update_time desc')
			->page($this->page,$this->pline)
			->select();

		if ($reData['data'] === false)
			throw new \XYException(__METHOD__,-9000);

		//将购物车换回json
		foreach ($reData['data'] as &$value)
		{
			if (!empty($value['cart']))
			{
				$tmpSerialize = null;
				$tmpSerialize = unserialize($value['cart']);
				if ($tmpSerialize === false)
					throw new \XYException(__METHOD__,-9550);
				$value['cart'] = $tmpSerialize;
                if (!empty($tmpSerialize['finance_advice']))
                {
                    $value['finance_advice'] = $tmpSerialize['finance_advice'];
//                    unset($value['cart']['finance_advice']);
                }
                if (!empty($tmpSerialize['boss_advice']))
                {
                    $value['boss_advice'] = $tmpSerialize['boss_advice'];
//                    unset($value['cart']['boss_advice']);
                }
			}
            if (!empty($value['history']))
            {
                $value['history'] = unserialize($value['history']);
            }
		}

		return $reData;
	}



	/**
	 * 搜索功能.
	 *
	 * - 如果是搜往来单位，则只查询name、qcode字段。
	 * - 如果是搜商品名，则查sku、spu的name、qcode字段
	 * - 如果是搜单据，则查order、warehouse中的货物（cart字段）、查order\warehouse\finance所有单据中的cid名称和开单人名称。单据里的cid_name目前这里是查实时的，并没有查表里的冗余字段
	 * 
	 * @param mixed|null $data POST的数据
	 *
	 * @param unsigned_int $page 请求第几页的数据
	 * @param unsigned_int $pline 一页多少行
	 * @param enum $type 1|2|3:要查单据、商品名、往来单位
	 * @param string $search 搜索的关键词（非id，id请前端自己处理）。
	 * 
	 * @return  array 数据库中的一行
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-10
	 * @api
	 */
	public function search(array $data = null)
	{
		$data['type'] = intval($data['type']);
		if (!$this->field('page,pline,type,search')->create($data,self::MODEL_SEARCH))
			throw new \XYException(__METHOD__,$this->getError());

		$map['admin_uid'] = getAdminUid();

		//往来单位
		if ($this->type == 3)
		{
			$like            = null;
			$like['name']    = array('like','%'.$this->search.'%');
			$like['qcode']   = array('like','%'.$this->search.'%');
			$like['sn']      = array('like','%'.$this->search.'%');
			$like['_logic']  = 'or';
			$map['_complex'] = $like;

			$reData['total_page'] = ceil(M('Company')->where($map)->count() / $this->pline);
			if ($reData['total_page'] == 0)
				$reData['total_page'] = 1;
			if ($this->page > $reData['total_page'])
				$this->page = 1;
			$reData['now_page'] = $this->page;
			$reData['data'] = D('Company')
				->where($map)
				->order('reg_time')
				->page($this->page,$this->pline)
				->select();
		}elseif ($this->type == 2)//商品名
		{
			$like              = null;
			//sku、spu的name、qcode
			$like['spu_name']  = array('like','%'.$this->search.'%');
			$like['qcode']     = array('like','%'.$this->search.'%');
			$like['spec_name'] = array('like','%'.$this->search.'%');
			$like['sn']        = array('like','%'.$this->search.'%');
			$like['_logic']    = 'or';
			$map['_complex']   = $like;
			
			$reData['total_page'] = ceil(M('Sku')->where($map)->count() / $this->pline);
			if ($reData['total_page'] == 0)
				$reData['total_page'] = 1;
			if ($this->page > $reData['total_page'])
				$this->page = 1;
			$reData['now_page'] = $this->page;
			$reData['data'] = D('Sku')
				->where($map)
				->order('reg_time')
				->page($this->page,$this->pline)
				->select();
		}elseif ($this->type == 1)//单据
		{
			$like                  = null;
			//order\warehouse\finance
			$like['operator_name'] = array('like','%'.$this->search.'%');
			$like['sn']            = array('like','%'.$this->search.'%');
			//cid_name->cid，目前这里是查实时的，并没有查表里的冗余字段
			$tmpMap['name']        = array('like','%'.$this->search.'%');
			$tmpCompany = D('Company')
				->where($tmpMap)
				->field('cid')
				->select();
			$cidList = array();
			foreach ($tmpCompany as $value)
			{
				$cidList[] = $value['cid'];
			}
			if (!empty($cidList))
				$like['cid']  = array('in',$cidList);

			$like['_logic']  = 'or';
			$map['_complex'] = $like;

			$FinanceSQL = D('Finance')
				->where($map)
				->buildSql();

			//order/warehouse中的
			$like['cart']    = array('like','%'.$this->search.'%');
			$map['_complex'] = $like;

			$OrderSQL = D('Order')
				->where($map)
				->buildSql();

			$WarehouseSQL = D('Warehouse')
				->where($map)
				->buildSql();

			$unionSQL = '( '.$OrderSQL.' UNION '.$FinanceSQL.' UNION '.$WarehouseSQL.' )';

			$reData['total_page'] = ceil(M('Query')->table($unionSQL.' tmp_union')->count() / $this->pline);
			if ($reData['total_page'] == 0)
				$reData['total_page'] = 1;
			if ($this->page > $reData['total_page'])
				$this->page = 1;
			$reData['now_page'] = $this->page;
			$reData['data'] = $this
				->table($unionSQL.' tmp_union')
				->order('reg_time')
				->page($this->page,$this->pline)
				->select();

			if ($reData['data'] === false)
				throw new \XYException(__METHOD__,-9000);

			//将购物车换回json
			foreach ($reData['data'] as &$value)
			{
				if (!empty($value['cart']))
				{
					$tmpSerialize = null;
					$tmpSerialize = unserialize($value['cart']);
					if ($tmpSerialize === false)
						throw new \XYException(__METHOD__,-9550);
					$value['cart'] = $tmpSerialize;
				}
			}
		}

		return $reData;
	}



	/**
	 * 生成仪表盘的数据.
	 *
	 * 注意：如果返回的数据不够5个，则后面会没有，请前端自己补全。比如七日热卖top5，只有4个，则返回的结果只有4个
	 * 
	 * @param mixed|null $data POST的数据
	 *
	 * @return array 其中，skuSaleTop的那个数组里，quantity是销售数量，stock是库存数量。
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-24
	 * @api
	 */
	public function dashboard()
	{
		$dbEverydaySummarySheet = D('EverydaySummarySheet');

		

		//n日数据
		$n = 30;//n日数据
		$tmpSummarySheet['0'] = $dbEverydaySummarySheet->summarySheet(array('reg_time'=>time()));
		for ($i = 1;$i < $n; $i++)
		{
			$tmpSummarySheet[$i] = $dbEverydaySummarySheet->summarySheet(array('reg_time'=>strtotime('-'.$i.' day')));
		}
		// log_("tmpSummarySheet",$tmpSummarySheet,$this);


		//画图用的数据：
		//1. n日销售额
		//2. n日毛利润
		foreach ($tmpSummarySheet as $value)
		{
			$reData['saleTop'][] = $value['statistics']['sale'];//之所以要用$value['statistics']['sale']而不是$value['sale']，是因为今日的数据，返回的没有$value['sale']
			$reData['gross_profit'][] = $value['statistics']['gross_profit'];
		}

		//7日的数据
		$summarySheetData[] = $tmpSummarySheet['0'];
		$summarySheetData[] = $tmpSummarySheet['1'];
		$summarySheetData[] = $tmpSummarySheet['2'];
		$summarySheetData[] = $tmpSummarySheet['3'];
		$summarySheetData[] = $tmpSummarySheet['4'];
		$summarySheetData[] = $tmpSummarySheet['5'];
		$summarySheetData[] = $tmpSummarySheet['6'];

		//七日sku热卖
		$statistics = array();
		$skuidList = array();
		foreach ($summarySheetData as $v)
		{
			if (!empty($v['statistics']['skuStatistics']))
			{
				foreach ($v['statistics']['skuStatistics'] as $value)
				{
					if (!isset($statistics[$value['sku_id']]))
					{
						$statistics[$value['sku_id']]['quantity'] = 0;//id:quantity
						$statistics[$value['sku_id']]['pilePrice'] = 0;//id:pilePrice
					}
					$statistics[$value['sku_id']]['quantity'] += xyround($value['quantity']);
					$statistics[$value['sku_id']]['pilePrice'] += xyround($value['pilePrice']);
				}
			}
		}
		arsort($statistics);//排序
		// log_("statistics",$statistics,$this);
		$i = 0;
		foreach ($statistics as $key => $value)//得到top5 skuidList
		{
			$skuidList[] = $key;
			$i++;
			if ($i >= 5)
				break;
		}
		$tmpSkuInfo = null;
		if ( !empty($skuidList) )
		{
			$tmpSkuInfo = D('Sku')->getList($skuidList,2,false,true);//得出来的是按sku_id排序的
			// log_("tmpSkuInfo",$tmpSkuInfo,$this);
			foreach ($skuidList as $v_key)//变为按销售量排序
			{
				if (isset($tmpSkuInfo[$v_key]))//有可能sku被删除了
				{
					$tmpSkuInfo[$v_key]['quantity'] = $statistics[$v_key]['quantity'];
					$tmpSkuInfo[$v_key]['pilePrice'] = $statistics[$v_key]['pilePrice'];
					$reData['skuSaleTop'][]         = $tmpSkuInfo[$v_key];//按skuidList里的顺序往里添加，所以排序了
				}
			}
		}
		else
			$reData['skuSaleTop'] = array();

		//欠账top5
		$companyMap['admin_uid'] = getAdminUid();
		$companyMap['balance']   = array('lt',0);
		$tmpCompanyTop = D('Company')->where($companyMap)->order('balance')->limit(5)->select();
		if (empty($tmpCompanyTop))
			$reData['companyTop'] = array();
		else
			$reData['companyTop'] = $tmpCompanyTop;



		//库存预警top5
		$tmpSkuStockTop = D('Sku')->where(array('admin_uid'=>getAdminUid(),'sku_class'=>0))->order('total_stock')->limit(5)->getField('sku_id,spu_name,spec_name,cat_name');
		$tmpSkuList = array_keys($tmpSkuStockTop);
		if($tmpSkuStockTop === false)
			throw new \XYException(__METHOD__,-9000);//允许为空
		if(!empty($tmpSkuList))
			$tmpSkuStoData = M('SkuStorage')->where(array('sku_id'=>array('in',$tmpSkuList),'admin_uid'=>getAdminUid()))->select();
		$tmpSto = M('Storage')->where(array('admin_uid'=>getAdminUid()))->getField('sto_id,sto_name');
		$skuStockTop = null;
		$i = 0;
		foreach($tmpSkuList as $key=>$value) //重新排序
		{
			foreach($tmpSkuStoData as $k=>$v)
			{
				if($v['sku_id'] == $value)
				{
					$i++;
					$skuStockTop[$i] = $v;
					$skuStockTop[$i]['spu_name'] = $tmpSkuStockTop[$value]['spu_name'];
					$skuStockTop[$i]['spec_name'] = $tmpSkuStockTop[$value]['spec_name'];
					$skuStockTop[$i]['cat_name'] = $tmpSkuStockTop[$value]['cat_name'];
					$skuStockTop[$i]['sto_name'] = $tmpSto[$v['sto_id']];
				}
			}
		}
		log_("skuStockTop",$skuStockTop,$this);

		if (empty($skuStockTop))
			$reData['skuStockTop'] = array();
		else
			$reData['skuStockTop'] = $skuStockTop;


		return $reData;
	}



	/**
	 * 得到销售汇总.
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 *
	 * - 选填参数（但最少要填一个）：
	 * - @param unsigned_int $reg_st_time 创建时间的开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
	 * - @param unsigned_int $reg_end_time 创建时间的结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
	 *
	 * @return array 统计信息
	 * @throws \XYException
	 * 
	 * @version 1.4 
	 * @author co8bit <me@co8bit.com>
	 * 
	 * @version 1.11整合采购单汇总 wtt <wtt@xingyunbooks.com>
	 * @date 2017-07-08
	 */
	public function saleSummary(array $data = null)
	{
		$reData = $this->orderSummary($data,1);
		return $reData;
	}

	/**
	 * 得到采购汇总
	 *
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param unsigned_int $reg_st_time 创建时间的开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
	 * @param unsigned_int $reg_end_time 创建时间的结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
	 *
	 * @return  array 统计信息
	 * @throws  \XYException
	 *
	 * @version 1.11
	 * @date 2017-07-08
	 * @author  wtt <wtt@xingyunbooks.com>
	 */
	
	public function purchaseSummary(array $data = null)
	{
		$reData = $this->orderSummary($data,2);
		return $reData;
	}

	/**
	 * 得到订单汇总
	 *
	 * @param mixed|null $data POST的数据
	 * @param unsigned_int $reg_st_time 创建时间的开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
	 * @param unsigned_int $reg_end_time 创建时间的结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
	 *
	 * @return  array 统计信息
	 * @throws  \XYException
	 *
	 * @author  wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @internal
	 * 
	 */
	public function orderSummary(array $data = null,$type = 1)
	{
		//验证字段
		if(!$this->field('reg_st_time,reg_end_time')->create($data,self::QueryModel_purchaseSummary))
			throw new \XYException(__METHOD__,$this->getError());
		//开始和结束时间至少要传一个
		if ( (!isset($data['reg_st_time'])) && (!isset($data['reg_end_time'])) )
			throw new \XYException(__METHOD__,-9013);

		/*组装sql*/
		$map = array();

		$map['admin_uid'] = getAdminUid();
		if($type == 1)
			$map['class'] = array('between',array(1,2));
		else
			$map['class'] = array('between',array(3,4));

		$map['_string'] = ' (status >= 1) and (status != 3) and (status != 91) and (status != 92) and (status != 99) and (status != 100)';
		$map['reg_time'] = $this->getTimeMap(array('reg_st_time'=>$this->reg_st_time,'reg_end_time'=>$this->reg_end_time));
		$orderData = D('order')->where($map)->select();
		log_("this->sql",D('order')->_sql(),D('order'));
		log_("orderData",$orderData,$this);
		if($orderData === false) //为空时返回null 允许返回为空
			throw new \XYException(__METHOD__,-9000);

		$skuStatistics               = null;
		$companyStatistics           = null;
		$salemanStatistics           = null;
		$purchaserStatistics         = null;
		$stoStatistics  			 = null;
		$reData						 = null;
		$reData['order_amount']      = 0;//订单笔数
		if($type ==1 )
		{
			$reData['sale_account']		 = 0;//订单数量
			$reData['sale_amount']       = 0;//销售额
			$reData['cost_amount']       = 0;//销售总成本
			$reData['gross_profit']      = 0;//销售毛利
			$reData['gross_profit_rate'] = 0;//销售毛利率
		}
		else
		{
			$reData['purchase_account']	 = 0;//采购数量
			$reData['purchase_amount']   = 0;//采购额
		}
		$stoIdList = null;

		$stoNameList = D('Storage')->getStoName($isInstallSql);

		//遍历结果集
		foreach( $orderData as $key=>$row)
		{
			//将购物车换回反序列化
			$tmpCart = null;
			$tmpCart = unserialize($row['cart']);
			if ($tmpCart === false)
				throw new \XYException(__METHOD__,-9550);
			$row['cart'] = $tmpCart; //？？不知道干嘛用的

			$reData['order_amount'] ++;//计算订单笔数
			$tmpCostTotalInOrder = 0;//一单内的成本总计，为了后面的按客户统计
			foreach($tmpCart as $item)
			{
				/**********sku统计  start*************/
				if (!isset($skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sku_id']))//init初始化
				{
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sku_id'] = 0;
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['spu_name'] = '';
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['spec_name'] = '';
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sn'] = '';//给手机端用的
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['quantity'] = 0;//订单数量
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['order_amount'] = 0;//订单笔数
					if($type == 1)
					{
						$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sale_amount'] = 0;//销售额
						$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['gross_profit'] = 0;//销售毛利
						$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['cost_amount'] = 0;//成本总额
						$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sale_avg_price'] = 0;//销售均价
						$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sale_avg_cost'] = 0;//平均成本
					}
					else
					{
						$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['purchase_amount'] = 0;//采购额
						$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['purchase_avg_price'] = 0;//采购均价	
					}
					
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sku_id'] = $item['sku_id'];
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['spu_name'] = $item['spu_name'];
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['spec_name'] = $item['spec_name'];
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sto_id'] = $row['sto_id'];
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sto_name'] = $stoNameList[$row['sto_id']];
				}

				// 
				if( $row['class'] ==1  ) //销售单
				{

					// $skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['cost_amount'] += xyround($item['cost'] * $item['quantity']);
					// $reData['cost_amount']                         += xyround($item['cost'] * $item['quantity']);
					// $tmpCostTotalInOrder                           += xyround($item['cost'] * $item['quantity']);
				}
				elseif( $row['class'] ==2|| $row['class'] == 4 ) //销售退货单或采购退货单
				{
					$item['quantity'] = 0 - $item['quantity'];
					$item['pilePrice'] = 0 - $item['pilePrice'];	
				}

				$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['cost_amount'] += xyround($item['cost'] * $item['quantity']);
				$reData['cost_amount']                         += xyround($item['cost'] * $item['quantity']);
				$tmpCostTotalInOrder                           += xyround($item['cost'] * $item['quantity']);
				$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sn'] = $item['sn'];//给手机端用的
				$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['quantity'] += xyround($item['quantity']);//订单数量
				$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['order_amount'] ++;

				if($type == 1)
				{
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['sale_amount'] += xyround($item['pilePrice']);
					$reData['sale_amount']  += xyround($item['pilePrice']);
					$reData['sale_account']	+= xyround($item['quantity']);//销售数量
				}
				else
				{
					$skuStatistics[$item['sku_id'].'-'.$row['sto_id']]['purchase_amount'] += xyround($item['pilePrice']);
					log_("item['pilePrice']",$item['pilePrice'],$this);
					log_("item['quantity']",$item['quantity'],$this);
					$reData['purchase_amount'] += xyround($item['pilePrice']);
					$reData['purchase_account']+= xyround($item['quantity']);//采购数量
					log_("reData000",$reData,$this);
				}

			}
			/**********sku统计  end*************/

			if($row['class'] == 2 || $row['class'] == 4) //采购单或销售退货单
				$row['value'] = 0-$row['value'];

			/**********company统计  start*************/
			log_("row",$row,$this);
			if(!isset($companyStatistics[$row['cid']]['cid']))//init初始化
			{
				$companyStatistics[$row['cid']]['cid'] = 0;
				$companyStatistics[$row['cid']]['cid_name'] = '';
				$companyStatistics[$row['cid']]['order_amount'] = 0;//销售笔数、采购笔数
				if($type == 1)
				{
					$companyStatistics[$row['cid']]['sale_amount'] = 0;//销售额
					$companyStatistics[$row['cid']]['cost_amount'] = 0;//销售成本
					$companyStatistics[$row['cid']]['order_avg_price'] = 0;//平均每单销售额
				}
				else
				{
					$companyStatistics[$row['cid']]['purchase_amount'] = 0;//采购额
					$companyStatistics[$row['cid']]['order_avg_price'] = 0;//平均每单采购额
				}
			}

			$companyStatistics[$row['cid']]['cid'] = $row['cid'];
			$companyStatistics[$row['cid']]['cid_name'] = $row['cid_name'];
			$companyStatistics[$row['cid']]['order_amount']++;
			if($type == 1)
			{
				$companyStatistics[$row['cid']]['sale_amount'] += xyround($row['value']);
				$companyStatistics[$row['cid']]['cost_amount'] += xyround($tmpCostTotalInOrder);
			}
			else
			{
				$companyStatistics[$row['cid']]['purchase_amount'] += xyround($row['value']);
			}
			/**********company统计  end*************/

			/*********操作人员统计  start*************/
			if(!isset($operatorStatics[$row['operator_uid']]['operator_uid']))//init初始化
			{
				$operatorStatics[$row['operator_uid']]['operator_uid'] = 0;
				$operatorStatics[$row['operator_uid']]['operator_name'] = '';
				$operatorStatics[$row['operator_uid']]['order_amount'] = 0;//销售笔数、采购笔数
				if($type ==1 )
				{
					$operatorStatics[$row['operator_uid']]['sale_amount'] = 0;//销售额
					$operatorStatics[$row['operator_uid']]['cost_amount'] = 0;//销售成本
					$operatorStatics[$row['operator_uid']]['order_avg_price'] = 0;//平均每单销售额
				}
				else
				{
					$operatorStatics[$row['operator_uid']]['purchase_amount'] = 0;//采购额
					$operatorStatics[$row['operator_uid']]['order_avg_price'] = 0;//平均每单采购额
				}
			}
			$operatorStatics[$row['operator_uid']]['operator_uid'] = $row['operator_uid'];
			$operatorStatics[$row['operator_uid']]['operator_name'] = $row['operator_name'];
			$operatorStatics[$row['operator_uid']]['order_amount'] ++;

			if($type ==1 )
			{
				$operatorStatics[$row['operator_uid']]['sale_amount'] += xyround($row['value']);
				$operatorStatics[$row['operator_uid']]['cost_amount'] += $tmpCostTotalInOrder;
			}
			else
			{
				$operatorStatics[$row['operator_uid']]['purchase_amount'] += xyround($row['value']);
			}

			/**********操作人员统计  end*************/


			/**********仓库统计  start*************/
			if(!isset($stoStatistics[$row['sto_id']]['sto_id']))//init 初始化
			{
				$stoStatistics[$row['sto_id']]['sto_id'] = 0;
				$stoStatistics[$row['sto_id']]['sto_name'] = '';
				$stoStatistics[$row['sto_id']]['purchase_amount'] = 0;//
				$stoStatistics[$row['sto_id']]['order_amount'] = 0;
				$stoStatistics[$row['sto_id']]['order_avg_price'] = 0;
			}
			$stoStatistics[$row['sto_id']]['sto_id'] = $row['sto_id'];
			$stoStatistics[$row['sto_id']]['sto_name'] = $row['sto_name'];

			$stoStatistics[$row['sto_id']]['purchase_amount'] += xyround($row['value']) ;//
			$stoStatistics[$row['sto_id']]['quantity'] = 0;
			$stoStatistics[$row['sto_id']]['order_amount'] ++ ;
			$stoIdList[] = $row['sto_id']; 
			/**********仓库统计  end*************/
		}

		log_("reData111",$reData,$this);
		if($type == 1)
		{
			foreach($skuStatistics as $key=>$value)
			{
				$skuStatistics[$key]['gross_profit'] = $skuStatistics[$key]['sale_amount']-$skuStatistics[$key]['cost_amount'];
				$skuStatistics[$key]['sale_avg_price'] = xydiv($skuStatistics[$key]['sale_amount'],$skuStatistics[$key]['quantity']);
				$skuStatistics[$key]['sale_avg_cost'] = xydiv($skuStatistics[$key]['cost_amount'],$skuStatistics[$key]['quantity']);
			}

			foreach($companyStatistics as $key=>$value)
			{

				$companyStatistics[$key]['order_avg_price'] = xydiv($companyStatistics[$key]['sale_amount'],$companyStatistics[$key]['order_amount']);
				$companyStatistics[$key]['gross_profit'] = $companyStatistics[$key]['sale_amount']-$companyStatistics[$key]['cost_amount'];
			}

			foreach($operatorStatics as $key=>$value)
			{

				$operatorStatics[$key]['order_avg_price'] = xydiv($operatorStatics[$key]['sale_amount'],$operatorStatics[$key]['order_amount']);
				$operatorStatics[$key]['gross_profit'] = $operatorStatics[$key]['sale_amount']-$operatorStatics[$key]['cost_amount'];
			}

			$reData['gross_profit'] = xysub($reData['sale_amount'] , $reData['cost_amount']);
			$reData['gross_profit_rate'] = xydiv($reData['gross_profit'],$reData['cost_amount']);
			$reData['salemanStatistics'] = $operatorStatics;

	
		}
		else
		{
			//获取库存数据
			$stockData = D('SkuStorage')->where('status=1')->select();
			log_("this->_sql()",D('SkuStorage')->_sql(),D('SkuStorage'));
			$skuStock = null;
			$stoStock = null;
			foreach($stockData as $key=>$value)
			{
				if(!isset($skuStock[$value['sku_id']]))//init
					$skuStock[$value['sku_id']] = 0;
				if(!isset($stoStock[$value['sto_id']]))//init
					$stoStock[$value['sto_id']] = 0;
				$skuStock[$value['sku_id']] += $value['stock'];
				$stoStock[$value['sto_id']] += $value['stock'];
 			}

			foreach($skuStatistics as $key=>$value)
			{
				$skuStatistics[$key]['purchase_avg_price'] = xydiv($skuStatistics[$key]['purchase_amount'],$skuStatistics[$key]['quantity']);
				$skuStatistics[$key]['stock'] = $skuStock[$key];
			}

			foreach($companyStatistics as $key=>$value)
			{
				$companyStatistics[$key]['order_avg_price'] = xydiv($companyStatistics[$key]['purchase_amount'],$companyStatistics[$key]['order_amount']);
			}

			foreach($operatorStatics as $key=>$value)
			{
				$operatorStatics[$key]['order_avg_price'] = xydiv($operatorStatics[$key]['purchase_amount'],$operatorStatics[$key]['order_amount']);
			}

			foreach($stoStatistics as $key=>$value)
				$stoStatistics[$key]['stock'] = $stoStock[$key];

			$reData['stoStatistics'] = $stoStatistics;
			$reData['purchaserStatistics'] = $operatorStatics;
		}

		//获取库存数据
		log_("skuStatistics",$skuStatistics,$this);
		log_("companyStatistics",$companyStatistics,$this);
		log_("salemanStatistics",$salemanStatistics,$this);
		log_("purchaserStatistics",$purchaserStatistics,$this);
		log_("stoStatistics",$stoStatistics,$this);
		log_("stoIdList",$stoIdList,$this);

		$reData['companyStatistics'] = $companyStatistics;
		$reData['skuStatistics'] = $skuStatistics;

		log_("reData",$reData,$this);
		return $reData;
	}

	/**
	 * 得到单个sku的进出汇总.
	 * @internal server
	 * @param mixed|null $data POST的数据
	 *
	 * - 必填参数：
	 * - @param unsigned_int $page 请求第几页的数据，从1开始计数
	 * - @param unsigned_int $pline 一页多少行
	 * - @param unsigned_int $sku_id 要查询的sku_id
	 * - @param unsigned_int $sto_id sku所在的仓库id
	 * - @param string       $field  查询结果所需要的字段 @interval server
	 * - 选填参数：（“全部”请不要传该字段）
	 * - @param json $filter class、status过滤条件。
	 *
	 * 			- array $class 单据类别编号：不传代表全部。
	 * 			
	 * 			- array $status 单据状态：不传代表全部，a-可选值：1、2、5、90.  b-未完成传90。
	 * 			
	 * - @param unsigned_int $reg_st_time 创建时间的开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
	 * - @param unsigned_int $reg_end_time 创建时间的结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
	 * 
	 * @example filter:
	 *			{
	 *				//下面两组可选；
	 *				"class":[1,71,73],
	 *				"status":[90],//1-可选值：1、2、5、90.  2-未完成传90。
	 *			}
	 * @example filter mini: {"class":[1,71,73],"status":[90],"remainType":[1]}
	 * 
	 * @return array 统计信息
	 * @throws \XYException
	 * 
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.9
	 * @api
	 */
	public function skuSummary(array $data = null)
	{
		//验证数据是否合法
		if (!$this->field('sku_id,page,pline,reg_st_time,reg_end_time')->create($data,self::QueryModel_skuSummary))
           throw new \XYException(__METHOD__,$this->getError());

        // 定义新的数组接收已通过验证的数据
       	$tmpData = array();
        $tmpData = $this->data;
        // 增加字段条件
       	$tmpData['field']='reg_time,class,operator_name,status,cart,oid,wid,sn,cid_name,sto_id,new_sto_id,check_name';

       	if(isset($data['sto_id']))
        {
            if(isUnsignedInt($data['sto_id']))
                $tmpData['sto_id'] = $data['sto_id'];
            else
                throw new \XYException(__METHOD__,-14015);
        }
        else
            throw new \XYException(__METHOD__,-14015);

        $orderAndWarehouse = D('SkuBill')->queryOrderAndWarehouse($tmpData);
		return $orderAndWarehouse;
	}


    /**
	 * 获取sku销量与采购量的图表 
	 * 
	 * @param mixed|null $data POST的数据
	 *
	 * - 必填参数：
	 * - @param unsigned_int $sku_id 要查询的sku_id
	 * - @param string       $field  查询结果所需要的字段 @interval server
	 * @return array 统计信息
	 * @throws \XYException
	 * 
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.9
	 * @api
	 */
    public function skuChart(array $data = null)
    {
    	//验证数据是否合法
        if(!$this->field('sku_id')->create($data,self::QueryModel_skuChart))
        {
            throw new \XYException(__METHOD__,$this->getError());
        }

        // 定义新的数组接收已通过验证的数据
        $tmpData = array();
        $tmpData = $this->data;

        //添加时间条件
        $tmpData['reg_st_time'] = strtotime(date('Y-m-01',strtotime('-5 month')));   // 前六个月第一天
        $tmpData['reg_end_time'] = strtotime(date('Y-m-'.date('t')));        // 当月最后一天
        //添加字段条件
        $tmpData['field']='reg_time,class,cart';

        if(isset($data['sto_id']))
        {
            if(isUnsignedInt($data['sto_id']))
                $tmpData['sto_id'] = $data['sto_id'];
            else
                throw new \XYException(__METHOD__,-14015);
        }
        else
            throw new \XYException(__METHOD__,-14015);
        $chartData = D('SkuBill')->queryOrderAndWarehouse($tmpData);

        /*****统计数据-初始化变量 start******/ 
        
        $statistics = array();
        // 获取当前日期所在的月份、年份
        $now_mon  = date('m');
        $now_year = date('Y');
        $statistics['saleCount'] = array_fill(0,6,0);
        $statistics['buyCount'] = array_fill(0,6,0);
        for($i=1;$i<7;$i++)
        {
        	$statistics['time'][] = date('Y',strtotime('-'.(6-$i).' month')).'/'.date('m',strtotime('-'.(6-$i).' month'));
        	$statistics['month'][] = date('m',strtotime('-'.(6-$i).' month'));
        }
        /*****统计数据-初始化变量 end******/ 

        foreach($chartData['data'] as $key => $value) {

            //获取数据月份
            $tmpTime = date('Y', $value['reg_time']).'/'.(date('m', $value['reg_time']));
            //获取数据对应的月份在$statistics数组中的对应下标
            $now_key = array_keys($statistics['time'],$tmpTime);
            if(!empty($now_key)){
	            foreach ($value['cart'] as $k => $v) {
	                // 选择订单类型进行统计
	                switch ($value['class']) {
	                    case 1:
	                        // 销售开单
	                         $statistics['saleCount'][$now_key[0]] +=
	                            $v['quantity'];
	                        break;
	                    case 2:
	                        // 销售退单
	                         $statistics['saleCount'][$now_key[0]] -=
	                            $v['quantity'];
	                        break;
	                    case 3:
	                        // 采购开单
	                        $statistics['buyCount'][$now_key[0]] +=
	                            $v['quantity'];
	                        break;
	                    case 4:
	                        // 采购退单
	                        $statistics['buyCount'][$now_key[0]] -=
	                            $v['quantity'];
	                        break;
	                    /*default :
	                        throw new \XYException(__METHOD__,-14503);*/
	                }
	            }
            }
        	
        }
        return $statistics;
    }


    /**
     * 生成销售趋势图的数据.
     *
     * @api
     * @param mixed|null $data POST的数据
     * - 必填参数：
     * - @param unsigned_int $operator_uid 要查询的sku_id
     * @return array 统计信息
     *
     * @throws \XYException
     *
     * @version 1.11
     * @author co8bit <me@co8bit.com>
     *
     * @version 1.11 王德昭 <wdz@xingyunbooks.com>
     * @date 2017-07-19
     */
    public function operatorChart(array $data = null)
    {
        //验证数据是否合法
        if(!$this->field('operator_uid')->create($data,self::QueryModel_operatorChart))
        {
            throw new \XYException(__METHOD__,$this->getError());
        }

        // 定义新的数组接收已通过验证的数据
        $tmpData = $this->data;

        //添加时间条件
        $tmpData['reg_st_time'] = strtotime(date('Y-m-01',strtotime('-5 month')));   // 前六个月第一天
        $tmpData['reg_end_time'] = strtotime(date('Y-m-'.date('t')));        // 当月最后一天
        //添加字段条件
        $tmpData['field']='reg_time,class,cart';
        $chartData = $this->queryOrder($tmpData);

        /*****统计数据-初始化变量 start******/

        $statistics = array();
        // 获取当前日期所在的月份、年份
        $now_mon  = date('m');
        $now_year = date('Y');
        $statistics['saleAmount'] = array_fill(0,6,0);
        $statistics['saleCount'] = array_fill(0,6,0);
        $statistics['grossProfit'] = array_fill(0,6,0);
        for($i=1;$i<7;$i++)
        {
            $statistics['time'][] = date('Y',strtotime('-'.(6-$i).' month')).'/'.date('m',strtotime('-'.(6-$i).' month'));
            $statistics['month'][] = date('m',strtotime('-'.(6-$i).' month'));
        }

        /*****统计数据-初始化变量 end******/

        foreach($chartData['data'] as $key => $value) {

            //获取数据月份
            $tmpTime = date('Y', $value['reg_time']).'/'.(date('m', $value['reg_time']));
            //获取数据对应的月份在$test数组中的对应下标
            $now_key = array_keys($statistics['time'],$tmpTime);

            foreach ($value['cart'] as $k => $v) {
                // 选择订单类型进行统计
                switch ($value['class']) {
                    case 1:
                        // 销售开单
                        $statistics['saleAmount'][$now_key[0]]+=
                            $v['pilePrice'];
                        $statistics['saleCount'][$now_key[0]] ++;
                        $statistics['grossProfit'][$now_key[0]]+=
                            $v['pilePrice']-$v['cost']*$v['quantity'];
                        break;
                    case 2:
                        // 销售退单
                        $statistics['saleAmount'][$now_key[0]]-=
                            $v['pilePrice'];
                        $statistics['saleCount'][$now_key[0]] --;
                        $statistics['grossProfit']-=
                            $v['pilePrice']-$v['cost']*$v['quantity'];
                        break;
                    /*default :
                        throw new \XYException(__METHOD__,-14503);*/
                }
            }

        }
        return $statistics;
    }


    /**
     * 得到单个operator的销售单汇总.
     * @internal server
     * @param mixed|null $data POST的数据
     *
     * - 必填参数：
     * - @param unsigned_int $page 请求第几页的数据，从1开始计数
     * - @param unsigned_int $pline 一页多少行
     * - @param unsigned_int $operator_uid 要查询的operator_uid
     * - 选填参数：（“全部”请不要传该字段）
     * - @param json $filter class、status过滤条件。
     *
     * 			- array $class 单据类别编号：不传代表全部。
     *
     * 			- array $status 单据状态：不传代表全部，a-可选值：1、2、5、90.  b-未完成传90。
     *
     * - @param unsigned_int $reg_st_time 创建时间的开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
     * - @param unsigned_int $reg_end_time 创建时间的结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
     *
     * @example filter:
     *			{
     *				//下面两组可选；
     *				"class":[1,2],
     *				"status":[90],//1-可选值：1、2、5、90.  2-未完成传90。
     *			}
     * @example filter mini: {"class":[1,2],"status":[90],"remainType":[1]}
     *
     * @return array 统计信息
     * @throws \XYException
     *
     * @author 王德昭 <wdz@xingyunbooks.com>
     * @version 1.9
     * @api
     */
    public function operatorSummary(array $data = null)
    {
        //验证数据是否合法
        if (!$this->field('operator_uid,page,pline,reg_st_time,reg_end_time')->create($data,self::QueryModel_operatorSummary))
            throw new \XYException(__METHOD__,$this->getError());
        log_("thisdata",$this->data,$this);
        // 定义新的数组接收已通过验证的数据
        $tmpData = array();
        $tmpData = $this->data;
        // 增加字段条件
        $tmpData['field']='reg_time,class,operator_name,status,cart,oid';
        $orderAndWarehouse = $this->queryOrder($tmpData);
        return $orderAndWarehouse;
    }

    /**
     * Operator 详情，查看业务往来
     * @internal server
     * @param mixed|null $data POST的数据
     * @param unsigned_int $operator_uid 要查询的operator_uid
     * @param unsigned_int $page 请求第几页的数据，从1开始计数
     * @param unsigned_int $pline 一页多少行
     * @param unsigned_int $reg_st_time 订单过滤开始时间
     * @param unsigned_int $reg_end_time 订单过滤结束时间
     * @param string       $field  查询结果所需要的字段
     * @param json $filter class、status
     *        - array $class 单据类别编号：不传代表全部。
     *        - array $status 单据状态：不传代表全部，a-可选值：1、2、5、90.  b-未完成传90。
     *
     * @return array 返回订单的数组和统计数据
     * @throws \XYException
     *
     * @author 王德昭 <wdz@xingyunbooks.com>
     * @version 1.9
     * @date    2017-07-21
     */
    public function queryOrder(array $data = null)
    {
        //验证数据是否合法
        if(!$this->field('operator_uid,page,pline')->create($data,self::QueryModel_queryOrder))
            throw new \XYException(__METHOD__,$this->getError());

        //手动验证其他字段

        if(isset($data['reg_st_time']))
        {
            if($this->check_unix_date($data['reg_st_time']))
                $this->reg_st_time = $data['reg_st_time'];
            else
                throw new \XYException(__METHOD__,-14012);
        }


        if(isset($data['reg_end_time']))
        {
            if($this->check_unix_date($data['reg_end_time']))
                $this->reg_end_time = $data['reg_end_time'];
            else
                throw new \XYException(__METHOD__,-14013);
        }

        if(is_string($data['field']))
            $this->field = $data['field'];
        else
            throw new \XYException(__METHOD__,-9018);

        if(isset($data['filter'])){
            if(is_string($data['filter']))
                $this->filter = $data['filter'];
            else
                throw new \XYException(__METHOD__,-9050);
        }
        log_("this->data",$this->data,$this);

        $filter = json_decode(I('param.filter','',''),true);
        if(!empty($data['filter'])){
            //filter条件不为空的时候判断转换是否成功
            if(empty($filter))
            {
                throw new \XYException(__METHOD__,-9050);
            }
        }

        // 判断前端传递的operator_uid在数据库中是否存在;
        $tmp = D('user')->getUserInfo(array('uid'=>$this->operator_uid),true);
        if(empty($tmp)){
            throw new \XYException(__METHOD__,-9009);
        }
        log_("tmpuser",$tmp,$this);
        // 根据 OPERATOR UID 获取其所在的 N 个订单的编号
        $operatorBillRet = D('order')->field('oid')
            ->where(array('operator_uid' => $this->operator_uid,'admin_uid'=>getAdminUid()))->select();

        // 遍历订单获取所有 OID 单独存储,以备查询
        $oidList = array();
        foreach($operatorBillRet as $item => $value){
            if(intval($value['oid']) != 0){
                $oidList[] = $value['oid'];
            }
        }

        // 拼接 condition
        $commondition = array();
        // 处理订单过滤开始和结束时间
        if(!isset($data['reg_st_time']))
        {
            unset($this->reg_st_time);
        }else
        {
            $tmpDate = null;
            $tmpDate = getdate($this->reg_st_time);
            $this->reg_st_time = mktime('0','0','0',$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
        }

        if(!isset($data['reg_end_time'])){
            unset($this->reg_end_time);
        }else
        {
            $tmpDate = null;
            $tmpDate = getdate($this->reg_end_time);
            $this->reg_end_time = mktime('23','59','59',$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
        }

        // 判断时间条件
        if ( isset($this->reg_st_time) && isset($this->reg_end_time))
        {
            $commonCondition['reg_time']  = array('between',array($this->reg_st_time,$this->reg_end_time));
        }elseif( isset($this->reg_st_time) && !isset($this->reg_end_time))
        {
            $commonCondition['reg_time']  = array('egt',$this->reg_st_time);
        }
        elseif ( (!isset($this->reg_st_time)) && (isset($this->reg_end_time)))
        {
            $commonCondition['reg_time']  = array('elt',$this->reg_end_time);
        }

        //设置oid
        if(count($oidList) > 0)
            $commonCondition['oid'] = array('in',$oidList);
        else
            $commonCondition['oid'] = 0;

        //设置class
        $class = array(1,2);
        //log_("filter",$filter,$this);
        if(!empty($filter['class'])){
            $classCondition = '(';
            foreach($filter['class'] as $value){
                $value = intval($value);
                if($this->checkDeny_query_class($value)){
                    //排除class值非1,2（销售单、销售退货单）的订单
                    if(in_array($value,$class))
                    {
                        if($classCondition === '('){
                            $classCondition = '( `class` = '.$value;
                        }else
                        {
                            $classCondition .= ' or `class`='.$value;
                        }
                    }
                    else
                    {
                        throw new \XYException(__METHOD__,-9051);
                    }
                }else
                {
                    throw new \XYException(__METHOD__,-9051);
                }
            }
            $classCondition .= ')';
        }

        //设置status
        if(!empty($filter['status'])){
            $statusCondition = '(';
            foreach($filter['status'] as $value){
                $value = intval($value);
                if($this->checkDeny_query_status($value)){
                    if($statusCondition ==='(')
                        $statusCondition = '(  `status`=' .$value;
                    else
                        $statusCondition .= ' or `status`='.$value;

                    if ($value == 90)
                        $statusCondition .= ' or `status`=2 or `status`=4 or `status`=5 or `status`=6 or `status`=7 or `status`=8 or `status`=9 or `status`=10 or `status`=11 or `status`=12';

                }else
                {
                    throw new \XYException(__METHOD__,-9052);
                }
            }
            $statusCondition .= ')';
            log_("statusCondition",$statusCondition,$this);
        }


        //草稿单状态
        $commonCondition['status']=array('not in',array(99,100));
        $commonCondition['class'] = array('in',array(1,2));

        // 设置class 和 status条件
        if((!empty($filter['class'])) && (!empty($filter['status']))){
            unset($commonCondition['status']);
            unset($commonCondition['class']);
            $commonCondition['_string'] = '('.$classCondition. ') AND ('.$statusCondition.')';
        }elseif( (empty($filter['class'])) && (!empty($filter['status']))){
            unset($commonCondition['status']);
            $commonCondition['_string'] =  $statusCondition;
        }elseif( (!empty($filter['class'])) && (empty($filter['status'])) )
        {
            unset($commonCondition['class']);
            $commonCondition['_string'] =  $classCondition;
        }

        // 获取admin_uid
        $commonCondition['admin_uid'] = getAdminUid();

        //拼接sql
        $orderSql = D('order')->where($commonCondition)->buildSql();

        log_('this->data2',$this->data,$this);
        //计算总页数、当前页码
        if(!empty($data['page']) && !empty($data['pline']))//没有传代表是QueryModel::skuChart()在调用，不分页
        {
            $reData['total_page'] = ceil($this->table($orderSql.'tmp')->count()/$data['pline']) ;
            if($reData['total_page'] == 0)
                $reData['total_page'] = 1;
            if($this->page > $reData['total_page'])
                $this->page = 1;
            $reData['now_page'] = $this->page;
            log_('this->data3',$this->data,$this);
            $reData['data'] = $this
                ->table($orderSql.'tmp')
                ->field($this->field)
                ->order('reg_time desc')
                ->page($reData['now_page'],$this->pline)
                ->select();
            log_("ordersql",$this->_sql(),$this);
        }else
        {
            $reData['data'] = $this
                ->table($orderSql.'tmp')
                ->field($this->field)
                ->order('reg_time desc')
                ->select();
            log_("ordersql",$this->_sql(),$this);
        }

        if($reData['data'] === false){
            throw new \XYException(__METHOD__,-9000);
        }

        foreach($reData['data'] as $key=>&$value)
        {
            //将购物车内容转换为json
            if(!empty($value['cart']))
            {
                $tmpSerialize = null;
                $tmpSerialize = unserialize($value['cart']);
                if($tmpSerialize === false){
                    throw new \XYException(__METHOD__,-9550);
                }
                $value['cart'] = $tmpSerialize;
            }
        }

        //将返回数据的cart下标都改为0
        foreach($reData['data'] as $k=>&$v)
        {
            foreach($v['cart'] as $k1=>$v1)//因为得不到v['cart']里唯一的一个的值的下标，所以只能foreach
            {
                if($k1 != 0)
                {
                    $v['cart'][0] = $v['cart'][$k1];
                    unset($v['cart'][$k1]);
                }
            }
            if (!empty($value['history']))
            {
                $value['history'] = unserialize($value['history']);
            }
        }
        return $reData;
    }

    /**
     * 得到operator_uid的人员的合计信息
     * @api
     * @param mixed|null $data POST的数据
     *
     * @param operator_uid $operator_uid
     *
     * @return array 数据库一行field后的结果
     * @throws \XYException
     *
     * @author 王德昭 <wdz@xingyunbooks.com>
     * @version 1.6
     * @date    2016-07-22
     */
    public function getOperatorInfo(array $data = null)
    {
        if(!$this->field('operator_uid')->create($data,self::QueryModel_getOperatorInfo))
            throw new \XYException(__METHOD__,$this->getError());
        $operator_uid=$this->operator_uid;
        $tmp = null;
        $tmp = D('user')->getUserInfo(array('uid'=>$this->operator_uid),true);
        //去掉不需要的字段
        unset($tmp['option_array']);
        unset($tmp['shop_name']);
        unset($tmp['admin_mobile']);
        unset($tmp['password']);
        unset($tmp['invitation_code']);
        unset($tmp['invitated_code']);
        unset($tmp['TODAY_HISTORY_CONFIG']);
        unset($tmp['GTClientID']);
        unset($tmp['session_id']);
        unset($tmp['admin_uid']);
        unset($tmp['email']);
        unset($tmp['depart_id']);
        unset($tmp['industry']);
        unset($tmp['province']);
        unset($tmp['reg_ip']);
        unset($tmp['last_login_time']);
        unset($tmp['last_login_ip']);
        unset($tmp['update_time']);
        unset($tmp['login_count']);
        unset($tmp['city']);
        unset($tmp['reg_time']);
        if (empty($tmp))
            throw new \XYException(__METHOD__,-9009);
        $tmpData=$this->saleSummary(array('reg_end_time'=>mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1));
        $tmp['gross_profit']=$tmpData['salemanStatistics'][$operator_uid]['gross_profit'];
        return $tmp;
    }
}