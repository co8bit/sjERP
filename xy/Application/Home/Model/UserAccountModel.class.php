<?php
namespace Home\Model;
use Think\Model;

/**
 * 星云进销存的用户账户系统的类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UserAccountModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		// UserAccountModel_renewForBalance
		array('admin_uid', 'isUnsignedInt', -16004,
			self::MUST_VALIDATE, 'function',self::UserAccountModel_renewForBalance), //admin_uid错误
		
		// UserAccountModel_renewForMember
		array('admin_uid', 'isUnsignedInt', -16004,
			self::MUST_VALIDATE, 'function',self::UserAccountModel_renewForMember), //admin_uid错误
		array('member_count', 'check_PaybillModel_memberCount', -16005,
			self::MUST_VALIDATE, 'callback',self::UserAccountModel_renewForMember), //member_count错误
		array('member_class', 'check_UserAccountModel_memberClass', -16002,
			self::MUST_VALIDATE, 'function',self::UserAccountModel_renewForMember), //bill_class错误 == member_class错误
	);



	/* 自动完成 */
	protected $_auto = array(
		//UserAccountModel_renewForBalance
		array('update_time', NOW_TIME, self::UserAccountModel_renewForBalance),
		
        //UserAccountModel_renewForMember
        array('update_time', NOW_TIME, self::UserAccountModel_renewForMember),
	);



	protected $UserAcountSecret = null;//加密秘钥


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
		return md5($data['admin_uid'].$data['member_class'].$data['member_st_time'].C('XingYunBooks_UserAcount_SECRET').$data['member_count'].$data['member_end_time'].$data['balance'].$data['balance_sms_gift'].$data['update_time']);//.$data['member_buy_count']
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
	protected function checkSign(array $data)
	{
		if ( $this->calcSign($data) == $data['sign'] )
			return true;
		else
			return false;
	}


	/**
	 * 管理员用户创建时创建对应的UserAccount账户
	 * @internal server
	 * 
	 * @return 1
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.2
	 * @date    2016-11-06
	 */
    public function create_()
    {
		$this->admin_uid        = getAdminUid();
        $this->member_class     = 0;
        $this->member_st_time   = 0;
		$this->member_count     = 0;
        $this->member_buy_count = 0;
        // $this->member_class     = 1;
        // $this->member_st_time   = NOW_TIME;
        // $this->member_count     = 1;

		$this->member_end_time  = 0;
		$this->balance          = 0;
		$this->balance_sms_gift = 1000;
		$this->update_time      = NOW_TIME;
		$this->sign             = $this->calcSign($this->data);
		$tmpReturn = $this->add();

		if ($tmpReturn > 0)
			return 1;
		else
			throw new \XYException(__METHOD__,-16000);
    }



    /**
     * 前端查询公司账户详情时使用.
     *
     * @api
     *
     * @return array 单据在数据库中的详情
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
    public function get_()
    {
    	$tmp = $this->where(array('admin_uid'=>getAdminUid()))->find();//todo:用主键查询

    	if ($tmp === false)
    		throw new \XYException(__METHOD__,-16000);
    	elseif (empty($tmp))
    		throw new \XYException(__METHOD__,-16502);

    	if ($this->checkSign($tmp))
    	{
    		unset($tmp['sign']);
    		return $tmp;
    	}
    	else
    		throw new \XYException(__METHOD__,-16501);
    }




    /**
     * 后端查找公司账户详情（加锁）.
     *
     * note:外部需要打开事务才可以。
     *
     * @internal server
     *
     * @return array 单据在数据库中的详情
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
    public function _find($admin_uid)
    {
    	$tmp = M('UserAccount')->where(array('admin_uid'=>intval($admin_uid)))->lock(true)->find();//todo:用主键查询

    	if ($tmp === false)
    		throw new \XYException(__METHOD__,-16000);
    	elseif (empty($tmp))
    		throw new \XYException(__METHOD__,-16502);
    	
    	if ($this->checkSign($tmp))
    		return $tmp;
    	else
    		throw new \XYException(__METHOD__,-16501);
    }





    /**
     * 余额充值操作
     * @internal server
     * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
     * 
     * @param array $data paybill数据库中的一行,即一条付款信息
     *
     * @return 1
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
    public function renewForBalance(array $data = null,$isAlreadyStartTrans = false)
    {
    	try
    	{
    		if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

    		if (!$this->field('admin_uid')->create($data,self::UserAccountModel_renewForBalance))
				throw new \XYException(__METHOD__,$this->getError());
			if (intval($data['bill_class']) !== 4)
				throw new \XYException(__METHOD__,-16002);
			if (isUnsignedInt($data['bill_money']))
				$this->bill_money = intval($data['bill_money']);
			else
				throw new \XYException(__METHOD__,-16003);

			$queryData = $this->_find($this->admin_uid);

			$updateData                = null;
			$updateData                = $queryData;
			$updateData['update_time'] = $this->update_time;
			$updateData['balance']     = 0;
			$updateData['balance']     = $queryData['balance'] + $this->bill_money;
			$updateData['sign']        = $this->calcSign($updateData);
			$tmpReturn = $this->where(array('user_account_id'=>$queryData['user_account_id'],'admin_uid'=>$this->admin_uid))->save($updateData);
			if ($tmpReturn === false)
				throw new \XYException(__METHOD__,-16000);
			elseif (empty($tmpReturn))
				throw new \XYException(__METHOD__,-16000);

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
     * 会员续费/开通操作
     *
     * note：目前只支持同类型会员的续费，不支持升级会员版本。
     *
     * @internal server
     * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
     * @param bool $isSysCall 在管理员注册(UserModel::AdminRegisterAfterAutoAdd())的时候会被置为true，这时候不参加优惠
     * 
     * @param array $data paybill数据库中的一行,即一条付款信息
     *
     * @return 1
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-07
     */
    public function renewForMember(array $data = null,$isAlreadyStartTrans = false,$isSysCall = false)
    {
    	try
    	{
    		if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

    		$data['member_class'] = $data['bill_class'];

    		if (!$this->field('admin_uid,member_count,member_class')->create($data,self::UserAccountModel_renewForMember))
				throw new \XYException(__METHOD__,$this->getError());

			$queryData = $this->_find($this->admin_uid);
            //注意：queryData里的是原来的数据；$this->data里的是新账单的数据，不要弄混了.
            //$this->member_class代替$this->bill_class,没有后者可以用
            
            $updateData                = null;
            $updateData                = $queryData;
            $updateData['update_time'] = $this->update_time;


            //对优惠进行限制：只能购买一年
            if ( ($this->member_class == 1) && (!$isSysCall) )//只有1有优惠
            {
                if ($this->member_count == 6)
                    $updateData['save_off_record'] = 1;
                else
                    $updateData['save_off_record'] = 2;

                if ( ($queryData['save_off_record'] == 1) && ($this->member_count == 6) )
                {
                    $updateData['save_off_record'] = 2;
                }
                elseif ( ($queryData['save_off_record'] == 1) && ($this->member_count == 12) )
                    throw new \XYException(__METHOD__,-16007);

                if ($queryData['save_off_record'] == 2)
                    throw new \XYException(__METHOD__,-16007);
            }



            if ($queryData['member_class'] == 0)//原来是免费版
            {
                $updateData['member_class']     = $this->member_class;
                $updateData['member_st_time']   = $this->update_time;
                $updateData['member_count']     = $this->member_count;
                $giftMoney                      = C('MemberClass_giftMoney');
                $updateData['balance_sms_gift'] = $giftMoney[$this->member_class];
                $timestamp                      = strtotime('+'.$this->member_count.' month -1 day',$updateData['member_st_time']);
                $dateArray                      = getdate($timestamp);
                $updateData['member_end_time']  = mktime(23,59,59,$dateArray['mon'],$dateArray['mday'],$dateArray['year']);
                D('Config')->setUserMember($this->admin_uid,$this->member_class);
            }
            else//原来是非免费版存在续费、升级、降级等，需特殊处理
            {
                if ($queryData['member_class'] == $this->member_class)//同等级续费
                {
                    $updateData['member_count']    = $queryData['member_count'] + $this->member_count;
                    $timestamp                     = strtotime('+'.$updateData['member_count'].' month -1 day',$queryData['member_st_time']);
                    $dateArray                     = getdate($timestamp);
                    $updateData['member_end_time'] = mktime(23,59,59,$dateArray['mon'],$dateArray['mday'],$dateArray['year']);
                }
                elseif ($queryData['member_class'] < $this->member_class)//升级
                {
                    $updateData['member_class']     = $this->member_class;
                    $updateData['member_st_time']   = $this->update_time;
                    $updateData['member_count']     = $this->member_count;
                    $giftMoney                      = C('MemberClass_giftMoney');
                    $updateData['balance_sms_gift'] = $giftMoney[$this->member_class];
                    $timestamp                      = strtotime('+'.$this->member_count.' month -1 day',$updateData['member_st_time']);
                    $dateArray                      = getdate($timestamp);
                    $updateData['member_end_time']  = mktime(23,59,59,$dateArray['mon'],$dateArray['mday'],$dateArray['year']);
                    D('Config')->setUserMember($this->admin_uid,$this->member_class);
                }
                else
                    throw new \XYException(__METHOD__,-16504);//降级
            }

			// log_("time1",date("Y-m-d H:i:s",$timestamp),$this);
			log_("member_end_time",date("Y-m-d H:i:s",$updateData['member_end_time']),$this);

            $updateData['member_buy_count'] = $this->member_count;

			$updateData['sign'] = $this->calcSign($updateData);
			$tmpReturn = $this->where(array('user_account_id'=>$queryData['user_account_id'],'admin_uid'=>$this->admin_uid))->save($updateData);//更新的是updateData
			if ($tmpReturn === false)
				throw new \XYException(__METHOD__,-16000);
			elseif (empty($tmpReturn))
				throw new \XYException(__METHOD__,-16000);

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
     * 内扣：扣款，然后创建一条扣款记录
     *
     * @internal server
     * @param mixed|null $data POST的数据
     * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
     *
     * @param enum $class 类别.1-短信 @server 透传给PaymentDetailsModel::create_()
     * @param unsigned_int $id 对应详情信息的主键值 @server 透传给PaymentDetailsModel::create_()
     * @param unsigned_int $operator_uid 操作人uid @server 透传给PaymentDetailsModel::create_()
     * @param unsigned_int $money 金额 @server 透传给PaymentDetailsModel::create_()
     * 
     * @return 1
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-06
     */
    public function deduction(array $data = null,$isAlreadyStartTrans = false)
    {
        try
        {
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
            // log_("data",$data,$this);
            if (!isUnsignedInt($data['money']))
                throw new \XYException(__METHOD__,-16006);
            $money = intval($data['money']);

            $backData    = $this->data;
            $originMoney = $money;
            $queryData   = $this->_find(getAdminUid());
            $this->data  = null;
            $this->data  = $backData;

            if ($queryData['balance_sms_gift'] > 0)
            {
                if ($queryData['balance_sms_gift'] >= $money)
                {
                    $queryData['balance_sms_gift'] -= $money;
                    $money                         = 0;
                }
                else
                {
                    $money                         -= $queryData['balance_sms_gift'];
                    $queryData['balance_sms_gift'] = 0;
                }
            }
            if ($money > 0)//赠送的钱不够，导致本次扣费未扣完
            {
                if ($queryData['balance'] >= $money)
                {
                    $queryData['balance'] -= $money;
                }
                else
                    throw new \XYException(__METHOD__,-16505);
            }
            $this->balance_sms_gift = $queryData['balance_sms_gift'];
            $this->balance          = $queryData['balance'];
            $this->update_time      = NOW_TIME;
            $this->sign             = $this->calcSign($this->data);

            $tmpReturn = $this->where(array('admin_uid'=>getAdminUid()))->save();
            log_("sql",M()->_sql(),$this);

            if ( !($tmpReturn > 0) )
                throw new \XYException(__METHOD__,-16000);

            D('PaymentDetails')->create_($data);

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

}