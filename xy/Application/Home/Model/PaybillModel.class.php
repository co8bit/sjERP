<?php
namespace Home\Model;
use Think\Model;

/**
 * 向我们付钱时的类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class PaybillModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//PaybillModel_getPayBillParam
		array('bill_class', 'check_PaybillModel_billClass',-15002,
			self::MUST_VALIDATE, 'callback',self::PaybillModel_getPayBillParam), //bill_class不合法
		array('member_count', 'check_PaybillModel_memberCount', -15011,
			self::EXISTS_VALIDATE, 'callback',self::PaybillModel_getPayBillParam), //member_count不合法
		array('bill_money', 'isUnsignedInt', -15003,
			self::EXISTS_VALIDATE, 'function',self::PaybillModel_getPayBillParam), //bill_money不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		//PaybillModel_getPayBillParam
		array('update_time', NOW_TIME, self::PaybillModel_getPayBillParam),
		array('reg_time', NOW_TIME, self::PaybillModel_getPayBillParam),
	);

	protected $app_id      = null;
	protected $app_secret  = null;
	protected $self_secret = null;//自己用的加密秘钥

	//价格-费用表
	protected $const_fee = null;


	public function __construct()
    {
    	parent::__construct();

		$this->app_id         = C('BeeCloud_APP_ID');
		$this->app_secret     = C('BeeCloud_APP_SECRET');
		$this->self_secret    = C('BeeCloud_SELF_SECRET');
		$this->const_fee      = C('CONST_FEE');
		$this->const_cost_fee = C('CONST_COST_FEE');
    }



    /**
     * 计算会员续费价格
     *
     * note: 本函数内部没有字段检查，请确保传入已检查的字段
     * @internal server
     *
     * @param array $queryData_UserAccount user_account表的一行数据
     * @param unsigned_int $bill_class 客户的用户数 1为1 2为2 3为3 4为4 5为5 6为6-10用户
     * @param unsigned_int $member_count 用户购买的月数 1年为12 2年24 3年为36
     * @param unsigned_int $update_time 当前时间，用来计算用户已经消费了多少天了
     *
     * @return unsigned_int 续费需要收取的钱数
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-08
     */
    protected function calcMemberRenewPrice(array $queryData_UserAccount,$bill_class,$member_count,$update_time)
    {
    	if ( ($queryData_UserAccount['member_class'] == $bill_class) || ($queryData_UserAccount['member_class'] == 0) )//续费或原来为免费版用户
    		return $this->const_fee[$bill_class][$member_count];
    	elseif ($queryData_UserAccount['member_class'] < $bill_class)//升级
    	{
    		if ( empty($queryData_UserAccount['member_st_time']) || empty($queryData_UserAccount['member_count']) )
    			throw new \XYException(__METHOD__,-15012);
			$goDay    = floor(($update_time-$queryData_UserAccount['member_st_time']) / 86400);//已经用了多少天会员了，这里是下取整，即只算完整用过的天数，不算当天在内。如1.1日购买，1.12日升级，那么这里的结果是已经用了11天会员，而不是12。
			$goMoney  = $this->const_cost_fee[$queryData_UserAccount['member_class']][$queryData_UserAccount['member_buy_count']] * $goDay;//已经用掉的费用
			$totalPay = $this->const_fee[$queryData_UserAccount['member_class']][$queryData_UserAccount['member_buy_count']];//用户之前付出的钱数
			$balance  = $totalPay - $goMoney;//用户剩余的钱数（可以参与抵扣的钱数）
			$money    = ( $this->const_fee[$bill_class][$member_count] - $balance);//用户升级还需要付出的钱数
    		log_("update_time",$update_time,$this);
    		log_("queryData_UserAccount['member_st_time']",$queryData_UserAccount['member_st_time'],$this);
    		log_("goDay",$goDay,$this);
    		log_("goMoney",$goMoney,$this);
    		log_("totalPay",$totalPay,$this);
    		log_("balance",$balance,$this);
    		log_("money",$money,$this);
    		return $money;
    	}
    	else//降级
    		throw new \XYException(__METHOD__,-16504);
    }



    /**
     * 计算签名
     * @internal server
     *
     * @param array $data 数据库中的一行
     *
     * @return string md5
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
	protected function calcSign(array $data)
	{
		return md5($data['bill_title'] . $data['bill_money'] . $this->self_secret . $data['admin_uid'] . $data['uid'] . $data['sn'] . $data['bill_class'] . $data['bill_status'] . $data['reg_time'] . $data['member_count']);
	}




	/**
     * 验证数据库中返回的结果签名是否正确
     * @internal server
     *
     * @param array $data 数据库中的一行
     *
     * @return bool true-ok
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
	public function checkSign(array $data)
	{
		if ( $this->calcSign($data) == $data['sign'] )
			return true;
		else
			return false;
	}




    /**
     * 得到支付参数
     * @api
     * @param mixed|null $data POST的数据
     * 
     * @param enum $bill_class 1|2|3|4|5|6|8 分别为：1用户|2用户|3用户|4用户|5用户|6-10用户|充值
     * @param enum $member_count 12|24|36 时长
     * @param unsigned_int $bill_money 单位：分。可选，如果type=8，需要传入充值多少钱.
     *
     * @return array
     * @throws \XYException
     * 
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
	public function getPayBillParam(array $data = null)
    {
    	if ( $this->isVisitor() )
    		throw new \XYException(__METHOD__,-15503);

    	if (!$this->field('bill_class,member_count,bill_money')->create($data,self::PaybillModel_getPayBillParam))
			throw new \XYException(__METHOD__,$this->getError());

		$billData       = null;

		$queryData_UserAccount = D('UserAccount')->get_();


//		//对优惠进行限制：只能购买一年
//		if ($this->bill_class == 1)//只有1有优惠
//		{
//			if ( ($queryData_UserAccount['save_off_record'] == 1) && ($this->member_count == 12) )
//				throw new \XYException(__METHOD__,-16007);
//
//			if ($queryData_UserAccount['save_off_record'] == 2)
//				throw new \XYException(__METHOD__,-16007);
//		}



		if ($this->bill_class == 8)//短信充值
    	{
    		$billData['bill_title'] = '短信充值-'.round($this->bill_money/100,2).'元';
    		$billData['bill_money'] = $this->bill_money;
    		$billData['member_count'] = 0;
    	}
    	elseif ($queryData_UserAccount['member_class'] == 0)//原来是免费版，则随意
		{
			$billData['bill_title']   = '开通-'.$this->const_fee[$this->bill_class]['title'].'会员'.$this->member_count.'个月';
			$billData['member_count'] = $this->member_count;
			$billData['bill_money']   = $this->calcMemberRenewPrice($queryData_UserAccount,$this->bill_class,$this->member_count,$this->update_time);
		}
    	else//原来是非免费版存在续费、升级、降级等，需特殊处理
		{
			if ($queryData_UserAccount['member_class'] == $this->bill_class)//同等级续费
			{
				$billData['bill_title']   = '续费-'.$this->const_fee[$this->bill_class]['title'].'会员'.$this->member_count.'个月';
				$billData['member_count'] = $this->member_count;
				$billData['bill_money']   = $this->calcMemberRenewPrice($queryData_UserAccount,$this->bill_class,$this->member_count,$this->update_time);
			}
			elseif ($queryData_UserAccount['member_class'] < $this->bill_class)//升级
			{
				$billData['bill_title']   = '升级到-'.$this->const_fee[$this->bill_class]['title'].'会员并购买'.$this->member_count.'个月';
				$billData['member_count'] = $this->member_count;
				$billData['bill_money']   = $this->calcMemberRenewPrice($queryData_UserAccount,$this->bill_class,$this->member_count,$this->update_time);
			}
			else
				throw new \XYException(__METHOD__,-16504);//降级
		}

		$billData['admin_uid']   = getAdminUid();
		$billData['uid']         = getUid();
		$billData['bill_class']  = $this->bill_class;
		$billData['bill_status'] = 0;
		$billData['reg_time']    = $this->reg_time;
		$billData['update_time'] = $this->update_time;
    	

    	while (true)//直到产生唯一的订单号为止
    	{
			$sn  = str_replace('-','',guid());
			$tmp = M('Paybill')->where(array('sn'=>$sn))->find();
			if ($tmp === false)
	    		throw new \XYException(__METHOD__,-15000);
	    	elseif (empty($tmp))
	    	{
				$billData['sn']   = $sn;
				$billData['sign'] = $this->calcSign($billData);
				$id               = $this->add($billData);
    			if ($id > 0)
    				break;
    		}
    	}
        $sign = md5($this->app_id . $billData['bill_title'] . $billData['bill_money'] . $sn . $this->app_secret);

        log_("sn",$sn,$this);
        return array(
					'bill_title' => $billData['bill_title'],
					'bill_money' => $billData['bill_money'],
					'sn'         => $sn,
					'sign'       => $sign,
        		);
    }





    /**
     * 用户支付成功后，beeclod会回调该函数
     * 
     * @api
     * @param mixed|null $data POST的数据
     * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
     *
     * @return 'success'|null
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
    public function payWebhook(array $data = null,$isAlreadyStartTrans = false)
    {
    	$queryData = null;
    	try
    	{
    		if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

    		$jsonStr = file_get_contents("php://input");
	    	$msg = json_decode($jsonStr);
	    	if (empty($msg))
	    		throw new \XYException(__METHOD__,-15013);

	    	//msg用到的跟数据库有关的东西需要在这里转义！！！！！
			$backMsg               = clone $msg;
			$msg->transaction_id   = null;
			$msg->channel_type     = null;
			$msg->sub_channel_type = null;
			$msg->transaction_id   = I('data.transaction_id/s','','htmlspecialchars',array('transaction_id'=>$backMsg->transaction_id));
			$msg->channel_type     = I('data.channel_type/s','','htmlspecialchars',array('channel_type'=>$backMsg->channel_type));
			$msg->sub_channel_type = I('data.sub_channel_type/s','','htmlspecialchars',array('sub_channel_type'=>$backMsg->sub_channel_type));

			$this->update_time = NOW_TIME;

			log_("msg->transaction_id",$msg->transaction_id,$this);

	    	/*
	    	第一步:验证签名
	    	 */
	    	$sign = md5($this->app_id . $this->app_secret . $msg->timestamp);
			if ($sign !== $msg->sign)
				throw new \XYException(__METHOD__,-15004);

			/*
			第二步：过滤重复的Webhook
			 */
			$queryData = $this->_find($msg->transaction_id);
			//单据是否已被处理过
			if (intval($queryData['bill_status']) !== 0)
				throw new \XYException(__METHOD__,-15501);

			/*
			第三步：验证订单金额
			 */
			//3.1 数据库内数据是否正确
			if ( ($queryData['bill_class'] == 1) || ($queryData['bill_class'] == 2) || ($queryData['bill_class'] == 3) )
	    	{
	    		//标题一致+金钱一致=>类别一致
	    		$title = findSubStr($queryData['bill_title'],'-','会员');

				$queryData_UserAcount = D('UserAccount')->_find($queryData['admin_uid']);
				$money                = $this->calcMemberRenewPrice($queryData_UserAcount,$queryData['bill_class'],$queryData['member_count'],$this->update_time);

				if ( ($title == $this->const_fee[$queryData['bill_class']]['title']) &&//数据库账单的标题和预设一致
	    		     ($queryData['bill_money'] == $money) )//数据库账单的金额和预设金额一致
	    			;
	    		else
	    			throw new \XYException(__METHOD__,-15007);
	    	}
	    	elseif ($queryData['bill_class'] == 4)
	    	{
				$num = findSubStr($queryData['bill_title'],'-','元');
				$num = $num * 100;

				if ($queryData['bill_money'] != $num)
					throw new \XYException(__METHOD__,-15007);
	    	}
	    	else
	    		throw new \XYException(__METHOD__,-15010);
			//3.2 beecloud云端单据金额是否正确
			if (intval($msg->transaction_fee) <= 0)
				throw new \XYException(__METHOD__,-15008);
			if ($queryData['bill_money'] != intval($msg->transaction_fee))
				throw new \XYException(__METHOD__,-15008);

			/*
			第四步：处理业务逻辑
			 */
			if($msg->transaction_type === "PAY")
			{
			    //4.1 支付状态是否变为支付成功,true代表成功
			    if ($msg->trade_success == true)
			    	;
			    else
			    	throw new \XYException(__METHOD__,-15009);


			    //4.2 开始处理真正的业务逻辑
			    if ( ($queryData['bill_class'] == 1) || ($queryData['bill_class'] == 2) || ($queryData['bill_class'] == 3) )
		    	{
		    		D('UserAccount')->renewForMember($queryData,true);
		    	}
		    	elseif ($queryData['bill_class'] == 4)
		    	{
		    		D('UserAccount')->renewForBalance($queryData,true);
		    	}


			    //4.3 更新单据状态
				$updateData                     = null;
				$updateData['bill_status']      = 1;
				$updateData['channel_type']     = $msg->channel_type;
				$updateData['sub_channel_type'] = $msg->sub_channel_type;
				$updateData['update_time']      = NOW_TIME;

				//calc sign
				$signData = null;
				$signData = $queryData;
				$signData = array_merge($signData,$updateData);
				$updateData['sign'] = $this->calcSign($signData);

			    $tmpReturn = $this->where(array('paybill_id'=>$queryData['paybill_id']))->save($updateData);
			    if (empty($tmpReturn))
			    	throw new \XYException(__METHOD__,-15000);

			    try
	            {
					$emailData             = $queryData;
					$emailData['reg_time'] = date('Y-m-d H:i:s',$emailData['reg_time']);
					$emailUserInfo         = D('User')->getUserInfo(array('uid'=>$queryData['admin_uid']));
                    log_('emailData',$emailData,$this);
	                $emailBody =dump($emailData,false).'<br>'.dump($emailUserInfo,false).'<br>';
                    log_('emailBody',$emailBody,$this);//测试付款通知

	                D('Util')->sendMail(
	                        C('ADMIN_REGISTER_MAIL_LIST'),
	                        '【运营监控】有人付费',
	                        $emailBody
	                    );
	                log_("mail已发",null,$this);
	            }
	            catch(\XYException $e)
	            {
	                log_("邮件发送失败：【运营监控】有人付费",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
	            }

			}
			elseif ($msg->transaction_type === "REFUND")//退款的结果
			{
				throw new \XYException(__METHOD__,-15502);
			}

			/*
			第五步：返回
			 */
			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			log_("success",null,$this);
		}
		catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);

			//更新订单单据
			if ( ($e->getCode() == -15000) || ($e->getCode() == -16000) || ($e->getCode() == -10000) )
				log_("[崩溃级错误]支付时数据库错误",null,$this);
			else
			{
				if ($e->getCode() != -15501)//如果是重复订单，则不要更新单据状态，不然成功的会被改为未成功
				{
					sleep(5);
					$updateData                     = null;
					$updateData['bill_status']      = 2;
					$updateData['channel_type']     = $msg->channel_type;
					$updateData['sub_channel_type'] = $msg->sub_channel_type;
					$updateData['update_time']      = NOW_TIME;

					//calc sign
					$signData = null;
					$signData = $queryData;
					$signData = array_merge($signData,$updateData);
					$updateData['sign'] = $this->calcSign($signData);

					if (!empty($queryData['paybill_id']))
				    	M('Paybill')->where(array('paybill_id'=>$queryData['paybill_id']))->save($updateData);//该操作无所谓失败或者成功

				}
				log_("fail",null,$this);
			}
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
    }



    /**
     * 查找支付单据详情.
     *
     * note:外部需要打开事务才可以。
     *
     * @internal server
     * @param string $sn 单据编号
     *
     * @return array 单据在数据库中的详情
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
    protected function _find($sn)
    {
    	$tmp = M('Paybill')->where(array('sn'=>$sn))->lock(true)->find();//todo:用主键查询

    	if ($tmp === false)
    		throw new \XYException(__METHOD__,-15000);
    	elseif (empty($tmp))
    		throw new \XYException(__METHOD__,-15005);

    	//验证数据库中得到的信息的真伪
    	if (!$this->checkSign($tmp))
			throw new \XYException(__METHOD__,-15006);
   		return $tmp;
    }





     /**
     * 查登录店铺的支付详情.
     *
     * note:外部需要打开事务才可以。
     *
     * @internal server
     * @param string $sn 单据编号
     *
     * @return array 单据在数据库中的详情
     * @throws \XYException
     *
     * @author liuxun <lx@xingyunbooks.com>
     * @version 1.2
     * @date    2016-11-29
     */
     public function getPayBills($data = null)
     {
     	$payBills = M('Paybill')->where(array('admin_uid'=>getAdminUid()))->select();
     	if($payBills == false)
     		throw new \XYException(__METHOD__,-15000);
     	elseif (empty($payBills)) 
     		throw new \XYException(__METHOD__,-15005);

     	foreach ($payBills as $key => $value) {
     		if(!$this->checkSign($value))
     			throw new \XYException(__METHOD__,-15006);
     	}

     	return $payBills;
     }

}