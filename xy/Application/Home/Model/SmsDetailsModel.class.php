<?php
namespace Home\Model;
use Think\Model;

/**
 * 短信系统的类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class SmsDetailsModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		// // PaybillModel_renewForBalance
		// array('admin_uid', 'isUnsignedInt', -16004,
		// 	self::MUST_VALIDATE, 'function',self::PaybillModel_renewForBalance), //admin_uid错误
		
		// // PaybillModel_renewForMember
		// array('admin_uid', 'isUnsignedInt', -16004,
		// 	self::MUST_VALIDATE, 'function',self::PaybillModel_renewForMember), //admin_uid错误
		// array('member_count', 'check_PaybillModel_memberCount', -16005,
		// 	self::MUST_VALIDATE, 'callback',self::PaybillModel_renewForMember), //member_count错误
		// array('member_class', 'check_UserAccountModel_memberClass', -16002,
		// 	self::MUST_VALIDATE, 'function',self::PaybillModel_renewForMember), //bill_class错误 == member_class错误
	);



	/* 自动完成 */
	protected $_auto = array(
		// //PaybillModel_renewForBalance
		// array('update_time', NOW_TIME, self::PaybillModel_renewForBalance),
		
		// //PaybillModel_renewForMember
		// array('update_time', NOW_TIME, self::PaybillModel_renewForMember),
	);



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
        return md5(C('SMS_KEY').$data['admin_uid'].$data['class'].$data['operator_uid'].$data['phone'].$data['num'].$data['sms_text'].$data['money'].$data['reg_time']);
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
	 * 用户给他们的客户发送短信对账单
     *
     * @api
     * @param mixed|null $data POST的数据
     * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
     * 
     * @param unsigned_int $dateSt 开始日期的unix时间，10位
     * @param unsigned_int $dateEnd 结束日期的unix时间，10位
     * @param double $money 金钱
     * @param string $content 账单详细信息
     * @param string $token token
     * @param string $sign sign
     * @param json $phoneArray 电话数组，a[i]为电话
     * @example json:
     *          ["57128121110","18966937860"]
     * @example json mini: ["57128121110","18966937860"]
	 * 
	 * @return 1
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.2
	 * @date    2016-11-06
	 */
    public function sendSMSStatementOfAccount(array $data = null,$isAlreadyStartTrans = false)
    {
        try
        {
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
            
            $dateSt  = I('data.dateSt/d',0,'htmlspecialchars',$data);
            $dateEnd = I('data.dateEnd/d',0,'htmlspecialchars',$data);
            $money   = I('data.money/f',0,'htmlspecialchars',$data);
            $content = I('data.content/s','','htmlspecialchars',$data);
            $token   = I('data.token/s','','htmlspecialchars',$data);
            $sign    = I('data.sign/s','','htmlspecialchars',$data);

            // log_("data",$data,$this);
            // log_("dateSt",$dateSt,$this);
            // log_("dateEnd",$dateEnd,$this);
            // log_("money",$money,$this);
            // log_("content",$content,$this);
            // log_("token",$token,$this);
            // log_("sign",$sign,$this);

            //验证token
            // $session_key = 'Home/Company/requeststatementofaccount{'.getUid().'}';
            $session_key = strtoupper('HomeCompanyrequeststatementofaccount'.getUid());
            log_("session_key",$session_key,$this);
            if (session('?'.$session_key))
            {
                $sessionData = session($session_key);
                session($session_key,null);
            }
            else
                throw new \XYException(__METHOD__,-17011);
            // log_("sessionData",$sessionData,$this);
            if ($sessionData['money'] != $money)
                throw new \XYException(__METHOD__,-17009);
            if ($sessionData['content'] != $content)
                throw new \XYException(__METHOD__,-17008);
            if ($sessionData['token'] != $token)
                throw new \XYException(__METHOD__,-17011);

            //验证手机号
            $cartTmp = json_decode(I('param.phoneArray','',''),true);
            if (empty($cartTmp))
                throw new \XYException(__METHOD__,-17050);
            // log_("cartTmp",$cartTmp,$this);
            
            if (!is_array($cartTmp))
                throw new \XYException(__METHOD__,-17003);
            if (empty($cartTmp))
                throw new \XYException(__METHOD__,-17003);
            if (count($cartTmp) > 200)
                throw new \XYException(__METHOD__,-17004);

            $phoneCount = 0;
            foreach ($cartTmp as $value)
            {
                if (!$this->checkMobile($value))
                    throw new \XYException(__METHOD__,-17010);
                ++$phoneCount;
            }
            $phoneArrayString = implode(',', $cartTmp);
            if (empty($phoneArrayString))
                throw new \XYException(__METHOD__,-17003);

            //验证时间
            if ( !$this->check_unix_date($dateSt) )
                throw new \XYException(__METHOD__,-17006);
            if ( !$this->check_unix_date($dateEnd) )
                throw new \XYException(__METHOD__,-17007);

             //验证内容
            if ( empty($content) )
                throw new \XYException(__METHOD__,-17008);
            if ( strlen($content) > 60000)
                throw new \XYException(__METHOD__,-17008);

            //验证金钱
            if (!is_numeric($money))
                throw new \XYException(__METHOD__,-17009);

             //验证sign
            if ( empty($sign) )
                throw new \XYException(__METHOD__,-17002);
    		if (md5(C('SendSMSStatementOfAccount_KEY') . $money . $content) !== $sign)//同步修改requestSMSStatementOfAccountContent
                throw new \XYException(__METHOD__,-17002);

            //preprocessing，修改下面需要同步修改CompanyModel::requestStatementOfAccount()从preprocessing开始的部分
            $dateSt = date('Y-m-d日',$dateSt);
            $dateEnd = date('Y-m-d日',$dateEnd);
            $shopName = D('User')->getUserInfo_shopName();
            if ($money < 0)//????
                $moneyPre = '['.$shopName.']共欠您';
            else
                $moneyPre = '您共欠['.$shopName.']';
            $money   = $moneyPre.abs($money);

            //添加短信发送记录
            $this->sms_text     = '【星云进销存对账单】您好，从'.$dateSt.'到'.$dateEnd.'期间，'.$money.'元。详情如下：'.$content.'。星云进销存，让生意更简单。';
            $this->reg_time     = NOW_TIME;
            $this->admin_uid    = getAdminUid();
            $this->class        = 1;
            $this->operator_uid = getUid();
            $this->phone        = $phoneArrayString;
            $this->num          = $this->calcSMSNum($this->sms_text);//一个收件人花费的短信条数
            $priceArray         = C('MemberClass_smsMoney');
            $memberInfo         = D('UserAccount')->get_();
            $this->money        = $this->num * $priceArray[$memberInfo['member_class']] * $phoneCount;
            $this->sign         = $this->calcSign($this->data);

            $backData = $this->data;

            $tmpReturn = $this->add();

            if (!($tmpReturn > 0))
                throw new \XYException(__METHOD__,-17000);

            D('UserAccount')->deduction(array(
                    'class'        => 1,
                    'id'           => $tmpReturn,
                    'operator_uid' => $backData['operator_uid'],
                    'money'        => $backData['money'],
                ),true);//一定要在发送前扣费，不然会出现短信发了余额不足的情况

            //send
            import('Vendor.Sms.sms');
            $Sms = new \Sms();
            $Sms->sendStatementOfAccount($phoneArrayString,$dateSt,$dateEnd,$money,$content);

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
     * 查询登录公司短信计费详情时使用.
     *
     * @api
     *
     * @return array 单据在数据库中的详情
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-29
     */
    public function getSmsDetail($data = null)
    {   
        $SmsDetails = $this->where(array('admin_uid' => getAdminUid()))->select();
        if($SmsDetails == false)
            throw new \XYException(__METHOD__,15000);
        if(empty($SmsDetails))
            throw new \XYException(__METHOD__,15005);

        foreach ($SmsDetails as $key => $value) {
            //if(!$this->checkSign($SmsDetails))
               // throw new \XYException(__METHOD__,16501);
        }

        return $SmsDetails;

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
    // public function get_()
    // {
    // 	$tmp = $this->where(array('admin_uid'=>getAdminUid()))->find();//todo:用主键查询

    // 	if ($tmp === false)
    // 		throw new \XYException(__METHOD__,-16000);
    // 	elseif (empty($tmp))
    // 		throw new \XYException(__METHOD__,-16502);

    // 	if ($this->checkSign($tmp))
    // 	{
    // 		unset($tmp['sign']);
    // 		return $tmp;
    // 	}
    // 	else
    // 		throw new \XYException(__METHOD__,-16501);
    // }




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
    // public function _find($admin_uid)
    // {
    // 	$tmp = M('UserAccount')->where(array('admin_uid'=>intval($admin_uid)))->lock(true)->find();//todo:用主键查询

    // 	if ($tmp === false)
    // 		throw new \XYException(__METHOD__,-16000);
    // 	elseif (empty($tmp))
    // 		throw new \XYException(__METHOD__,-16502);
    	
    // 	if ($this->checkSign($tmp))
    // 		return $tmp;
    // 	else
    // 		throw new \XYException(__METHOD__,-16501);
    // }

}