<?php
namespace Home\Model;
use Think\Model;



/**
 * 扣款详情的类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class PaymentDetailsModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		// PaymentDetailsModel_create_
		array('class', 'check_PaymentDetailsModel_class', -18002,
			self::MUST_VALIDATE, 'callback',self::PaymentDetailsModel_create_), //class错误
        array('id', 'isUnsignedInt', -18003,
            self::MUST_VALIDATE, 'function',self::PaymentDetailsModel_create_), //id错误
        array('operator_uid', 'isUnsignedInt', -18004,
            self::MUST_VALIDATE, 'function',self::PaymentDetailsModel_create_), //operator_uid错误
        array('money', 'isUnsignedInt', -18005,
            self::MUST_VALIDATE, 'function',self::PaymentDetailsModel_create_), //money错误
	);



	/* 自动完成 */
	protected $_auto = array(
		// PaymentDetailsModel_create_
		array('reg_time', NOW_TIME, self::PaymentDetailsModel_create_),
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
        return md5(C('PaymentDetails_KEY').$data['admin_uid'].$data['class'].$data['id'].$data['operator_uid'].$data['money'].$data['reg_time']);
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
	 * 创建一条扣款记录
     *
     * @internal server
     * @param mixed|null $data POST的数据
     *
     * @param enum $class 类别.1-短信
     * @param unsigned_int $id 对应详情信息的主键值
     * @param unsigned_int $operator_uid 操作人uid
     * @param unsigned_int $money 金额
     * 
	 * @return 1
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.2
	 * @date    2016-11-06
	 */
    public function create_(array $data = null)
    {
        if (!$this->field('class,id,operator_uid,money')->create($data,self::PaymentDetailsModel_create_))
            throw new \XYException(__METHOD__,$this->getError());

        $this->admin_uid = getAdminUid();
        $this->sign      = $this->calcSign($this->data);

		$tmpReturn = $this->add();

		if ($tmpReturn > 0)
			return 1;
		else
			throw new \XYException(__METHOD__,-18000);
    }



    /**
     * 查询店铺的充值记录和短信使用记录
     *
     * @api
     * @param mixed|null $data POST的数据
     * 
     * @param enum $type 请求类型.1-收入 2-支出
     * @param unsigned_int $total_page 分页总页数
     * @param unsigned_int $pline 当前请求页数
     * @param unsigned_int $reg_st_time
     * @param unsigned_int $reg_end_time
     * @return 1
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-11-26
     */
    public function getPaybillAndSmsDetail($data = null)
    {
        if(!isset($data['type']) 
            || !isUnsignedInt($data['page'])
            || !isUnsignedInt($data['pline']))
            throw new \XYException(__METHOD__,-9001);
        $map['admin_uid'] = getAdminUid();
        $map['reg_time'] = array();
        if (!empty($data['reg_st_time']))
        {
            $map['reg_time'] = array(array('egt',intval($data['reg_st_time'])));
        }
        log_('reg_st_time',$map['reg_time']);
        if (!empty($data['reg_end_time']))
        {
            $map['reg_time'] = array_merge($map['reg_time'],array(array('elt',intval($data['reg_end_time']))));
        }
        if (empty($data['reg_st_time'])&&empty($data['reg_end_time']))
            unset($map['reg_time']);
        log_('reg_end_time',$map['reg_time']);
        if($data["type"] == 1)//收入
        {
            $result['total_page'] = ceil(M('Paybill')->where($map)->count() / $data['pline']);
            if ($result['total_page'] == 0)
                $result['total_page'] = 1;
            if ($data['page'] > $result['total_page'])
                $data['page'] = 1;
            $result['now_page'] = $data['page'];

            //支付记录相关
            $paybills = M('Paybill')->where($map)->order('reg_time desc')->page($data['page'],$data['pline'])->select();
            if($paybills === false)
                throw new \XYException(__METHOD__,-15000);
            elseif (empty($paybills)) 
                $result['data'] = array();
            else
            {
                foreach ($paybills as $key => $value) {
                    if(!D('Paybill')->checkSign($value))
                        throw new \XYException(__METHOD__,-15006);
                    $tmp = null;
                    $tmp['class'] = $value['bill_title'];
                    $tmp['operator_name'] = D('User')->getUserInfo_name(array('uid' => $value['operator_uid']),true);
                    
                    if($value['bill_status'] == 0) $tmp['bill_status'] = '未处理';
                    if($value['bill_status'] == 1) $tmp['bill_status'] = '已完成';
                    if($value['bill_status'] == 2) $tmp['bill_status'] = '已处理';
                    $tmp['time'] = $value['reg_time'];
                    $tmp['money'] = $value['bill_money'];

                    $result['data'][] = $tmp;
                }
            }


        }elseif($data["type"] == 2)//支出
        {
            $result['total_page'] = ceil(M('SmsDetails')->where($map)->count() / $data['pline']);
            if ($result['total_page'] == 0)
                $result['total_page'] = 1;
            if ($data['page'] > $result['total_page'])
                $data['page'] = 1;
            $result['now_page'] = $data['page'];

            //短信记录相关
            $SmsDetails = M('SmsDetails')->where($map)->order('reg_time desc')->page($data['page'],$data['pline'])->select();
            if($SmsDetails === false)
                throw new \XYException(__METHOD__,-15000);
            elseif (empty($SmsDetails)) 
                $result['data'] = array();
            else
            {
                foreach ($SmsDetails as $key => $value) {
                    if(!D('SmsDetails')->checkSign($value))
                        throw new \XYException(__METHOD__,-15006);
                    $tmp = null;
                    $tmp['class']         = '短信';
                    $tmp['operator_name']      = D('User')->getUserInfo_name(array('uid' => $value['operator_uid']),true);
                    $tmp['time']          = $value['reg_time'];
                    $tmp['money']         = $value['money'];
                    $tmp['sms_recipient'] = $value['phone'];
                    $tmp['sms_content']   = $value['sms_text'];
                    $tmp['sms_num']       = $value['num'];
                    
                    $result['data'][]     = $tmp;

                }                
            }                       
        }else
        {
            throw new \XYException(__METHOD__,-9011);
        }

        return $result;
    }



    /**
     * 查询店铺节省金额
     *
     * @api
     * @param mixed|null $data POST的数据
     * 
     * @return code:1,data:{sms_saved_money:33(分钱),sms_saved_time:3(分钟)}
     * @throws \XYException
     */
    public function getMoneyTimeSave($data = null)
    {
        $SmsCount = M('SmsDetails')->where(array('admin_uid'=>getAdminUid()))->select();
        if($SmsCount === false)
            throw new \XYException(__METHOD__,-15000);
        $money = 0;
        foreach ($SmsCount as $k => $v)
        {
            $money = $money + $v['money'];
        }
        $result['sms_saved_money'] = $money/6 * 0.04;//这里每天节省的钱暂时写死，讨论两种方案：用会员等级对应的短信费率去计算，遍历统计表进行计算.
        $result['sms_saved_time'] = $money/6 * 0.5;

        return $result;
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