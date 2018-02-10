<?php
namespace Home\Model;
use Think\Model;

/**
 * 查询类Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class EverydaySummarySheetModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//MODEL_INSERT
		array('reg_time', '10,10', -11002, 
			self::MUST_VALIDATE, 'length', self::MODEL_INSERT), //reg_time长度不合法
		array('reg_time', 'isNonnegativeInt', -11003,
			self::MUST_VALIDATE, 'function', self::MODEL_INSERT), //reg_time类型不合法

		//MODEL_GET
		array('everyday_summary_sheet_id', 'checkDeny_everyday_summary_sheet_id', -11001,
			self::MUST_VALIDATE, 'callback', self::MODEL_GET),//everyday_summary_sheet_id不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		//MODEL_INSERT
		array('admin_uid','getAdminUid',self::MODEL_INSERT,'function'),//填入所属创建者uid
		array('update_time', NOW_TIME, self::MODEL_INSERT),

		//MODEL_QUERY
		array('admin_uid','getAdminUid',self::MODEL_QUERY,'function'),//填入所属创建者uid

		//MODEL_GET
		array('admin_uid','getAdminUid',self::MODEL_GET,'function'),//填入所属创建者uid
	);




	/**
	 * 得到今日汇总表.
	 * 
	 * 它分两种情况：
	 * 
	 * - reg_time为今日时间，则不论如何都需要重新生成数据、更新数据库
	 * - reg_time不为今日时间，则判断是否创建过当日汇总表，如果创建过则调用get_，如果没有则新建
	 *
	 * 注意：reg_time在这里不是传统意义上的reg_time,而是一个假的生成时间，这个时间只能是当日的，比如今天是7.25，summarySheet传入reg_time为6.29，那么reg_time应该为6.29,而不是7.25。因为所有的筛选都是通过reg_time做的。
	 * 
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $reg_time 要得到汇总表的那一天
	 *
	 * @return array 统计信息，具体请查看axure画的原型
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-10
	 */
	public function summarySheet(array $data = null)
	{
		if (!$this->field('reg_time')->create($data,self::MODEL_INSERT))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpDate = null;
		$tmpDate = getdate($this->reg_time);
		$stDate  = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		$endDate = mktime(23,59,59,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		// log_(date("Y-m-d H:i:s",$this->reg_time),null,$this);
		// log_(date("Y-m-d H:i:s",$stDate),null,$this);
		// log_(date("Y-m-d H:i:s",$endDate),null,$this);

		// if ( ($stDate > NOW_TIME) || (NOW_TIME > $endDate) )//不在今天内
		// {
		// 	$mapTest              = null;
		// 	$tmpDateTest          = null;
		// 	$tmpDateTest          = getdate($this->reg_time);
		// 	$stDateTest           = mktime(0,0,0,$tmpDateTest['mon'],$tmpDateTest['mday'],$tmpDateTest['year']);
		// 	$endDateTest          = mktime(23,59,59,$tmpDateTest['mon'],$tmpDateTest['mday'],$tmpDateTest['year']);
		// 	$mapTest['admin_uid'] = $this->admin_uid;
		// 	$mapTest['reg_time']  = array('between',array($stDateTest,$endDateTest));
		// 	$tmpReTest            = null;
		// 	$tmpReTest            = M('EverydaySummarySheet')->where($mapTest)->find();
		// 	if (!empty($tmpReTest))//之前生成过了
		// 		return $this->get_(array('everyday_summary_sheet_id'=>$tmpReTest['everyday_summary_sheet_id']));
		// 	//如果之前没有生成，则一切不影响，继续往下走
		// }


		//不管是否在今天内，都需要更新数据库
		$dbOrder   = D('Order');
		$dbFinance = D('Finance');
		$map['admin_uid'] = $this->admin_uid;
		$map['_string'] = 'status<>0 AND status<>3 AND status<>91 AND status<>99 AND status<>100';
		$map['reg_time'] = array('between',array($stDate,$endDate));
		$orderInfo   = $dbOrder->where($map)->select();
		if ($orderInfo === false)
			throw new \XYException(__METHOD__,-11000);
		$financeInfo = $dbFinance->where($map)->select();
		if ($financeInfo === false)
			throw new \XYException(__METHOD__,-11000);

		//销售额、应收、应付，只能由交易产生他们，但他们不用分三种支付方式
		$reData['sale']            = 0;//销售额，算优惠掉的钱数
		$reData['sale_off']        = 0;//销售优惠额
		$reData['receivable']      = 0;//应收款
		$reData['payable']         = 0;//应付款
		$reData['actually_income'] = 0;//总实收款
		$reData['actually_paid']   = 0;//总实付款
		

		/**
		 * 实收类：由交易类实收和财务类实收构成
		 */
		$reData['total_income']                      = 0;
		$reData['income_cash_total']                 = 0;//现金收入总计
		$reData['income_bank_total']                 = 0;//银行收入总计
		$reData['income_online_pay_total']           = 0;//网络收入总计
		//交易类实收
		$reData['transactionReceiptTotalIncome']     = 0;//总实收
		$reData['transactionReceiptCashTotal']       = 0;
		$reData['transactionReceiptBankTotal']       = 0;
		$reData['transactionReceiptOnline_payTotal'] = 0;
		$reData['transactionReceiptOff']             = 0;
		//财务类实收
		$reData['financeReceiptTotalIncome']         = 0;//总实收
		$reData['financeReceiptCashTotal']           = 0;
		$reData['financeReceiptBankTotal']           = 0;
		$reData['financeReceiptOnline_payTotal']     = 0;
		// $reData['financeReceiptOff']              = 0;


		/**
		 * 实付款类：由交易类实付和财务类实付构成
		 */
		$reData['total_paid']                     = 0;
		$reData['paid_cash_total']                = 0;
		$reData['paid_bank_total']                = 0;
		$reData['paid_online_pay_total']          = 0;
		//交易类实付款类
		$reData['transactionPaidTotal']           = 0;//总实付款
		$reData['transactionPaidCashTotal']       = 0;
		$reData['transactionPaidBankTotal']       = 0;
		$reData['transactionPaidOnline_payTotal'] = 0;
		$reData['transactionPaidOff']             = 0;
		//财务类实付款类
		$reData['financePaidTotal']               = 0;//总实付款
		$reData['financePaidCashTotal']           = 0;
		$reData['financePaidBankTotal']           = 0;
		$reData['financePaidOnline_payTotal']     = 0;
		// $reData['financePaidOff']              = 0;


		/**
		 * 其他
		 */
		//其他收入
		$reData['other_income']           = 0;//其他收入总计
		$reData['incomeCashTotal']        = 0;
		$reData['incomeBankTotal']        = 0;
		$reData['incomeeOnline_payTotal'] = 0;

		//费用
		$reData['expense']                = 0;//费用总计
		$reData['expenseCashTotal']       = 0;
		$reData['expenseBankTotal']       = 0;
		$reData['expenseOnline_payTotal'] = 0;


		/**
		 * 结余类
		 */
		$reData['total_balance']            = 0;//结余总计
		$reData['balance_cash_total']       = 0;//现金结余总计
		$reData['balance_bank_total']       = 0;//银行结余总计
		$reData['balance_online_pay_total'] = 0;//网络结余总计
		$reData['gross_profit']             = 0;//毛利润


		/**
		 * 应收款调整、应付款调整 类
		 */
		$reData['AAR_inc'] = 0;//应收款增加
		$reData['AAR_dec'] = 0;//应收款减少
		$reData['AAP_inc'] = 0;//应付款增加
		$reData['AAP_dec'] = 0;//应付款减少



	
		$RorP     = array();//收付款单
		$IorE     = array();//其他收入与费用单
		$AAROrAAP = array();//应收款调整单与应付款调整单

		$skuStatistics             = null;
		//将购物车换回json
		foreach ($orderInfo as $value)
		{
			if (!empty($value['cart']))
			{
				$tmpSerialize = null;
				$tmpSerialize = unserialize($value['cart']);
				if ($tmpSerialize === false)
				{
					log_('$value',$value,$this);
					throw new \XYException(__METHOD__,-11550);
				}
				$value['cart'] = $tmpSerialize;
				foreach ($value['cart'] as $rowValue)
				{
					if ($value['class'] == 1)//销售单
					{
						if (!isset($skuStatistics[$rowValue['sku_id']]['sku_id']))//init
						{
							$skuStatistics[$rowValue['sku_id']]['sku_id']       = 0;
							$skuStatistics[$rowValue['sku_id']]['spu_name']     = 0;
							$skuStatistics[$rowValue['sku_id']]['spec_name']    = 0;
							$skuStatistics[$rowValue['sku_id']]['quantity']     = 0;
							$skuStatistics[$rowValue['sku_id']]['pilePrice']    = 0;
							$skuStatistics[$rowValue['sku_id']]['gross_profit'] = 0;
						}
						$skuStatistics[$rowValue['sku_id']]['sku_id']    = $rowValue['sku_id'];
						$skuStatistics[$rowValue['sku_id']]['spu_name']  = $rowValue['spu_name'];
						$skuStatistics[$rowValue['sku_id']]['sn']        = $rowValue['sn'];
						$skuStatistics[$rowValue['sku_id']]['spec_name'] = $rowValue['spec_name'];
						$skuStatistics[$rowValue['sku_id']]['quantity']  += xyround($rowValue['quantity']);
						$skuStatistics[$rowValue['sku_id']]['pilePrice'] += xyround($rowValue['pilePrice']);
						$skuStatistics[$rowValue['sku_id']]['cost']      += xyround($rowValue['cost'] * $rowValue['quantity']);

						$skuStatistics[$rowValue['sku_id']]['gross_profit'] += xyround(($rowValue['pilePrice'] - ($rowValue['quantity'] * $rowValue['cost']) ));
						$reData['gross_profit']                             += xyround(($rowValue['pilePrice'] - ($rowValue['quantity'] * $rowValue['cost']) ));
					}
				}
			}

			if ($value['class'] == 1)//销售单
			{
				$reData['sale']     += $value['value'];
				$reData['sale_off'] += $value['off'];
			}
			else if ($value['class'] == 2)//销售退货单
			{
				$reData['sale'] -= $value['value'];
			}

			if ( ($value['class'] == 1) || ($value['class'] == 4) )//收款类订单
			{
				$reData['receivable']                        += $value['receivable'];
				$reData['transactionReceiptTotalIncome']     += $value['income'];
				$reData['transactionReceiptCashTotal']       += $value['cash'];
				$reData['transactionReceiptBankTotal']       += $value['bank'];
				$reData['transactionReceiptOnline_payTotal'] += $value['online_pay'];
				$reData['transactionReceiptOff']             += $value['off'];
			}
			elseif ( ($value['class'] == 2) || ($value['class'] == 3) )//付款类订单
			{
				$reData['payable']                        += $value['receivable'];
				$reData['transactionPaidTotal']           += $value['income'];
				$reData['transactionPaidCashTotal']       += $value['cash'];
				$reData['transactionPaidBankTotal']       += $value['bank'];
				$reData['transactionPaidOnline_payTotal'] += $value['online_pay'];
				$reData['transactionPaidOff']             += $value['off'];
			}

			if ( ($value['class'] == 5) || ($value['class'] == 6) )//应收应付款调整类单据
			{
				$AAROrAAP[] = $value;
				if ($value['class'] == 5)
				{
					if ($value['income'] > 0)
						$reData['AAR_inc'] += $value['income'];
					else
						$reData['AAR_dec'] += abs($value['income']);
				}
				elseif ($value['class'] == 6)
				{
					if ($value['income'] > 0)
						$reData['AAP_inc'] += $value['income'];
					else
						$reData['AAP_dec'] += abs($value['income']);
				}
			}
		}
		foreach ($skuStatistics as $key => $value)
		{
			$skuStatistics[$key]['avgPrice'] = xydiv($skuStatistics[$key]['pilePrice'] , $skuStatistics[$key]['quantity']);
		}

		
		foreach ($financeInfo as $value)
		{
			if ( ($value['class'] == 71) || ($value['class'] == 72) )//收付款单
			{
				$RorP[] = $value;
				if ($value['class'] == 71)
				{
					$reData['financeReceiptTotalIncome']     += $value['income'];
					$reData['financeReceiptCashTotal']       += $value['cash'];
					$reData['financeReceiptBankTotal']       += $value['bank'];
					$reData['financeReceiptOnline_payTotal'] += $value['online_pay'];
					// $reData['financeReceiptOff']             += $value['off'];
				}elseif ($value['class'] == 72)
				{
					$reData['financePaidTotal']           += $value['income'];
					$reData['financePaidCashTotal']       += $value['cash'];
					$reData['financePaidBankTotal']       += $value['bank'];
					$reData['financePaidOnline_payTotal'] += $value['online_pay'];
					// $reData['financePaidOff']             += $value['off'];
				}
			}
			elseif( ($value['class'] == 73) || ($value['class'] == 74) )//其他收入与费用单
			{
				$IorE[] = $value;
				if ($value['class'] == 73)
				{
					$reData['other_income']           += $value['income'];
					$reData['incomeCashTotal']        += $value['cash'];
					$reData['incomeBankTotal']        += $value['bank'];
					$reData['incomeeOnline_payTotal'] += $value['online_pay'];
				}elseif ($value['class'] == 74)
				{
					$reData['expense']                += $value['income'];
					$reData['expenseCashTotal']       += $value['cash'];
					$reData['expenseBankTotal']       += $value['bank'];
					$reData['expenseOnline_payTotal'] += $value['online_pay'];
				}
			}
		}

		$reData['skuStatistics'] = $skuStatistics;
		$reData['RorP']          = $RorP;
		$reData['IorE']          = $IorE;
		$reData['AAROrAAP']      = $AAROrAAP;
		//实收详情
		$reData['income_cash_total'] 		= 	$reData['transactionReceiptCashTotal'] + 
												$reData['financeReceiptCashTotal'] + 
												$reData['incomeCashTotal'];//现金收入总计
		$reData['income_bank_total'] 		= 	$reData['transactionReceiptBankTotal'] + 
												$reData['financeReceiptBankTotal'] + 
												$reData['incomeBankTotal'];//银行收入总计
		$reData['income_online_pay_total'] 	= 	$reData['transactionReceiptOnline_payTotal'] + 
												$reData['financeReceiptOnline_payTotal'] + 
												$reData['incomeeOnline_payTotal'];//网络收入总计

		//一些额外计算
		$reData['total_income'] = $reData['income_cash_total'] + 
								  $reData['income_bank_total'] + 
								  $reData['income_online_pay_total'];
		$reData['total_paid'] =  $reData['transactionPaidTotal'] + 
								 $reData['financePaidTotal'] +
								 $reData['expense'];
		$reData['paid_cash_total'] = $reData['transactionPaidCashTotal'] +
									 $reData['financePaidCashTotal'] + 
									 $reData['expenseCashTotal'];
		$reData['paid_bank_total'] = $reData['transactionPaidBankTotal'] + 
									 $reData['financePaidBankTotal'] + 
									 $reData['expenseBankTotal'];
		$reData['paid_online_pay_total'] = $reData['transactionPaidOnline_payTotal'] + 
										   $reData['financePaidOnline_payTotal'] + 
										   $reData['expenseOnline_payTotal'];
		$reData['total_balance'] = $reData['balance_cash_total'] + 
								   $reData['balance_bank_total'] + 
								   $reData['balance_online_pay_total'];

		//结余总计详情
		$reData['balance_cash_total']       = 	$reData['income_cash_total'] -
												$reData['transactionPaidCashTotal'] - 
												$reData['financePaidCashTotal'] - 
												$reData['expenseCashTotal'];//现金结余总计
		$reData['balance_bank_total']       = 	$reData['income_bank_total'] -
												$reData['transactionPaidBankTotal'] - 
												$reData['financePaidBankTotal'] - 
												$reData['expenseBankTotal'];//银行结余总计
		$reData['balance_online_pay_total'] = 	$reData['income_online_pay_total'] -
												$reData['transactionPaidOnline_payTotal'] - 
												$reData['financePaidOnline_payTotal'] - 
												$reData['expenseOnline_payTotal'];//网络结余总计


		$reData['actually_income'] 	=   $reData['transactionReceiptTotalIncome'] + 
										$reData['financeReceiptTotalIncome'] + 
										$reData['other_income'];//总实收款
		$reData['actually_paid']   =   	$reData['transactionPaidTotal'] + 
										$reData['financePaidTotal'] + 
										$reData['expense'];//总实付款
        $reData['net_profit']      =    $reData['gross_profit']+$reData['other_income']-$reData['expense'];

		//sys信息
		$reData['admin_uid']   = $this->admin_uid;

		$jsonedReData                  = $reData;
		$jsonedReData['reg_time']      = $this->reg_time;
		$jsonedReData['update_time']   = $this->update_time;

		$jsonedReData['statistics'] = serialize($reData);
		if (empty($jsonedReData['statistics']))
			throw new \XYException(__METHOD__,-11501);

		//准备最终返回的数据
		$lastReData                = null;
		$lastReData['statistics']  = $reData;
		$lastReData['reg_time']    = $jsonedReData['reg_time'];
		$lastReData['update_time'] = $jsonedReData['update_time'];

		
		// //写入数据库
		// if ( ($stDate > NOW_TIME) || (NOW_TIME > $endDate) )//不在今天内，一定是add数据库
		// {
		// 	$pk = $this->add($jsonedReData);
		// 	if ($pk > 0)
		// 		return $lastReData;
		// 	else
		// 		throw new \XYException(__METHOD__,-11000);
		// }
		// else//在今天内
		{
			$tmpDate          = null;
			$map              = null;
			$tmpDate          = getdate($this->reg_time);
			$stDate           = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
			$endDate          = mktime(23,59,59,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
			$map['admin_uid'] = $this->admin_uid;
			$map['reg_time']  = array('between',array($stDate,$endDate));
			$tmpRe            = null;
			$tmpRe            = M('EverydaySummarySheet')->where($map)->find();
			if (empty($tmpRe))
			{
				$pk = $this->add($jsonedReData);
				if ($pk > 0)
					return $lastReData;
				else
					throw new \XYException(__METHOD__,-11000);
			}
			else
			{
				unset($jsonedReData['reg_time']);
				$map['everyday_summary_sheet_id'] = $tmpRe['everyday_summary_sheet_id'];
				$pk = $this->where($map)->save($jsonedReData);
				if ( ($pk === null) || ($pk === false) )
					throw new \XYException(__METHOD__,-11000);

				return $lastReData;
			}
		}
	}



	/**
	 * 查询每日汇总表的那个list.
	 *
	 * - 必填参数：
	 * - @param unsigned_int $page 请求第几页的数据，从1开始计数
	 * - @param unsigned_int $pline 一页多少行
	 * - 可选参数：
	 * - @param unsigned_int $reg_st_time 开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
	 * - @param unsigned_int $reg_end_time 结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
	 * - 上面reg_st_time和reg_end_time都不传，代表查所有
	 * 
	 * @param mixed|null $data POST的数据
	 * 
	 * @return array 数据库结果集，其中array[i]为数据库中的一行
	 * @throws \XYException
	 * @api
	 */
	public function queryList(array $data = null)
	{
		if ( isUnsignedInt($data['page']) )
			$this->page = intval($data['page']);
		else
			throw new \XYException(__METHOD__,-9001);
		if ( isUnsignedInt($data['pline']) )
			$this->pline = intval($data['pline']);
		else
			throw new \XYException(__METHOD__,-9002);
		if ( isset($data['reg_st_time']) )
		{
			if ( $this->check_unix_date($data['reg_st_time']) )
				$this->reg_st_time = intval($data['reg_st_time']);
			else
				throw new \XYException(__METHOD__,-9004);
		}
		if ( isset($data['reg_end_time']) )
		{
			if ( $this->check_unix_date($data['reg_end_time']) )
				$this->reg_end_time = intval($data['reg_end_time']);
			else
				throw new \XYException(__METHOD__,-9005);
		}

		$map['admin_uid'] = getAdminUid();

		/*
		处理reg_st_time and reg_end_time
		 */
		
		//不显示创建时间以前的表
		$UserQuery = D('User')->getUserInfo(array('uid'=>getAdminUid()));//注意，是管理员的创建时间
		// log_("this->reg_st_time",$this->reg_st_time,$this);
		// log_("UserQuery['reg_time']",$UserQuery['reg_time'],$this);
		if ($this->reg_st_time < $UserQuery['reg_time'])
			$this->reg_st_time = $UserQuery['reg_time'];
		// log_("this->reg_st_time",$this->reg_st_time,$this);

		if (isset($this->reg_st_time))
		{
			$tmpDate = null;
			$tmpDate = getdate($this->reg_st_time);
			$this->reg_st_time  = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		}
		if (isset($this->reg_end_time))
		{
			$tmpDate = null;
			$tmpDate = getdate($this->reg_end_time);
			$this->reg_end_time = mktime(23,59,59,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		}

		//reg_time filter
		if ( isset($this->reg_st_time) && isset($this->reg_end_time) )
		{
			$map['reg_time']  = array('between',array($this->reg_st_time,$this->reg_end_time));
		}
		elseif ( isset($this->reg_st_time) && (!isset($this->reg_end_time)) )
		{
			$map['reg_time']  = array('egt',$this->reg_st_time);
		}
		elseif ( (!isset($this->reg_st_time)) && (isset($this->reg_end_time)) )
		{
			$map['reg_time']  = array('elt',$this->reg_end_time);
		}

		//return result
		$reData['total_page'] = ceil(M('EverydaySummarySheet')->where($map)->count() / $this->pline);
		if ($reData['total_page'] == 0)
			$reData['total_page'] = 1;
		if ($this->page > $reData['total_page'])
			$this->page = 1;
		$reData['now_page'] = $this->page;
		$reData['data'] = $this
			->where($map)
			->order('reg_time desc')
			->field('everyday_summary_sheet_id,reg_time,sale,receivable,payable,actually_income,actually_paid,income_cash_total,income_bank_total,income_online_pay_total,balance_cash_total,balance_bank_total,balance_online_pay_total,other_income,expense,gross_profit')
			->page($this->page,$this->pline)
			->select();
		return $reData;
	}



	/**
	 * 得到当日汇总单的详情.
	 * 
	 * @internal 内部函数，被summarySheet调用
	 * 
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $everyday_summary_sheet_id 要查询的那个单据的主键
	 *
	 * @return array 数据库一行的信息（即某一日的汇总表）
	 * @throws \XYException
	 */
	public function get_(array $data = null)
	{
		if (!$this->field('everyday_summary_sheet_id')->create($data,self::MODEL_GET))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpInfo = $this->where(array('everyday_summary_sheet_id'=>$this->everyday_summary_sheet_id,'admin_uid'=>$this->admin_uid))->find();
		
		if (empty($tmpInfo))
			throw new \XYException(__METHOD__,-11001);

		//将购物车换回json
		$tmpSerialize = null;
		$tmpSerialize = unserialize($tmpInfo['statistics']);
		if ($tmpSerialize === false)
				throw new \XYException(__METHOD__,-11502);
		$tmpInfo['statistics'] = $tmpSerialize;

		return $tmpInfo;
	}
}