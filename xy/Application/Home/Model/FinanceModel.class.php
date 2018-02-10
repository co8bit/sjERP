<?php
namespace Home\Model;
use Nette\Utils\Json;
use Think\Model;

/**
 * 财务类Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class FinanceModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('fid', 'checkDeny_fid', -3009, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//fid不合法
		array('class', array(71,74), -3001, self::EXISTS_VALIDATE, 'between',self::MODEL_BOTH), //class不合法
		array('cash', 'isNonegativeReal', -3002, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//cash不合法
		array('bank', 'isNonegativeReal', -3003, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//bank不合法
		array('online_pay', 'isNonegativeReal', -3010, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//在线支付不合法
		array('remark', '0,1000', -3004, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //remark长度不合法
		array('name', '1,60', -3008, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //name长度不合法
		array('fid', 'checkDeny_fid', -3009, self::EXISTS_VALIDATE, 'callback',self::MODEL_INSERT),//fid不合法

		//MODEL_CREATE_DRAFT
		array('fid', 'checkDeny_fid', -3009, self::EXISTS_VALIDATE, 'callback',self::MODEL_CREATE_DRAFT),//fid不合法
		array('class', array(71,74), -3001, self::EXISTS_VALIDATE, 'between',self::MODEL_CREATE_DRAFT), //class不合法
		array('cash', 'isNonegativeReal', -3002, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//cash不合法
		array('bank', 'isNonegativeReal', -3003, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//bank不合法
		array('online_pay', 'isNonegativeReal', -3010, self::EXISTS_VALIDATE, 'function',self::MODEL_CREATE_DRAFT),//在线支付不合法
		array('remark', '0,1000', -3004, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //remark长度不合法
		array('name', '1,60', -3008, self::EXISTS_VALIDATE, 'length',self::MODEL_CREATE_DRAFT), //name长度不合法

		//MODEL_DELETE_DRAFT
		array('fid', 'checkDeny_fid', -3009, self::MUST_VALIDATE, 'callback',self::MODEL_DELETE_DRAFT),//fid不合法

        //queryFinanceOrder
        array('class', '1,20', -3001, self::EXISTS_VALIDATE, 'length',self::FinanceModel_query_FinanceOrder), //class不合法
        array('status', '1,20', -3502, self::EXISTS_VALIDATE, 'length',self::FinanceModel_query_FinanceOrder),//status不合法
        array('update_time', '1,20', -3502, self::EXISTS_VALIDATE, 'length',self::FinanceModel_query_FinanceOrder),//传入时间不合法

        //create_FinanceOrder
        array('class', '1,20', -3001, self::MUST_VALIDATE, 'length',self::FinanceModel_create_FinanceOrder), //class不合法
        array('cid', '1,20', -6001, self::EXISTS_VALIDATE, 'length',self::FinanceModel_create_FinanceOrder), //cid不合法
        array('fid', 'checkDeny_fid', -3009, self::EXISTS_VALIDATE, 'callback',self::FinanceModel_create_FinanceOrder),//fid不合法
        array('remark', '0,1000', -3004, self::EXISTS_VALIDATE, 'length',self::FinanceModel_create_FinanceOrder), //remark长度不合法
        array('is_invoice','1,2',-3606,self::EXISTS_VALIDATE,'length',self::FinanceModel_create_FinanceOrder),//is_invoice(是否有单据)参数不合法
        array('cid_name', '1,20', -6024, self::EXISTS_VALIDATE, 'length',self::FinanceModel_create_FinanceOrder), //cid_name不合法

        //create_FinanceOrder
        array('class', '1,20', -3001, self::EXISTS_VALIDATE, 'length',self::FinanceModel_create_FinanceOrderDraft), //class不合法
        array('cid', '1,20', -6001, self::EXISTS_VALIDATE, 'length',self::FinanceModel_create_FinanceOrderDraft), //cid不合法
        array('fid', 'checkDeny_fid', -3009, self::EXISTS_VALIDATE, 'callback',self::FinanceModel_create_FinanceOrderDraft),//fid不合法
        array('remark', '0,1000', -3004, self::EXISTS_VALIDATE, 'length',self::FinanceModel_create_FinanceOrderDraft), //remark长度不合法
        array('is_invoice','1,2',-3606,self::EXISTS_VALIDATE,'length',self::FinanceModel_create_FinanceOrderDraft),//is_invoice(是否有单据)参数不合法
        array('cid_name', '1,20', -6024, self::EXISTS_VALIDATE, 'length',self::FinanceModel_create_FinanceOrderDraft), //cid_name不合法

        //FinanceModel_edit_FinanceOrder
        array('fid', 'checkDeny_fid', -3009, self::MUST_VALIDATE, 'callback',self::FinanceModel_edit_FinanceOrder),//fid不合法
        array('fid', '0,200', -3009, self::EXISTS_VALIDATE, 'length',self::FinanceModel_edit_FinanceOrder),

        //FinanceOrderStatusChange
        array('fid', 'checkDeny_fid', -3009, self::MUST_VALIDATE, 'callback',self::FinanceModel_financeOrderStatusChange),//fid不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
		array('cash','xyround',self::MODEL_INSERT,'function'),//货币类数字自动四舍五入
		array('bank','xyround',self::MODEL_INSERT,'function'),//货币类数字自动四舍五入
		array('online_pay','xyround',self::MODEL_INSERT,'function'),//货币类数字自动四舍五入

		//MODEL_CREATE_DRAFT
		array('admin_uid','getAdminUid',self::MODEL_CREATE_DRAFT,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_CREATE_DRAFT),
		array('update_time', NOW_TIME, self::MODEL_CREATE_DRAFT),
		array('cash','xyround',self::MODEL_CREATE_DRAFT,'function'),//货币类数字自动四舍五入
		array('bank','xyround',self::MODEL_CREATE_DRAFT,'function'),//货币类数字自动四舍五入
		array('online_pay','xyround',self::MODEL_CREATE_DRAFT,'function'),//货币类数字自动四舍五入

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
	 * @internal server
	 *
	 * @param int $fid 需要写历史的单据主键
	 * @param string $action 动作名称
	 * @param unsigned_int $time 时间戳
	 * @param array $arr 变动的字段的字段名数组，形如a[0][0] = sku_id,a[0][1]是sku_id的真实值，如1
	 *
	 * @return string 可以给人看的操作记录
	 * @throws \XYException
	 */
	protected function writeHistory($fid,$action,$time,$arr = array())
	{
		$uid = getUid();
		$name = session('user_auth.name');
		$userSn = M('User')->where(array('uid'=>$uid,'admin_uid'=>getAdminUid()))->getField('sn');

		if (!is_array($arr))
			throw new \XYException(__METHOD__,-3997);;//修改操作没有修改任何字段而导致


		switch ($action)
		{
			case 'add':{
				$userInfo = $name.'(编号:'.$userSn.')于'.date("Y-m-d日H:i:s",$time).'创建';
				break;
			}
			case 'edit':{
				if ( ($arr[0][1] === '') || ($arr[0][1] === null) )
						$arr[0][1] = '空';
				$userInfo = '于'.date("Y-m-d日H:i:s",$time).'由'.$name.'(编号:'.$userSn.')修改备注为:'.$arr[0][1];
				break;
			}
            case 'examine':{
                $userInfo = '于'.date("Y-m-d日H:i:s",$time).'由'.$name.'(编号:'.$userSn.')审核';
                break;
            }
			default:
				throw new \XYException(__METHOD__,-3999);
		}


		$preHistory = null;
		$history_data = array();
		if ($fid === 0)
			$preHistory = '';
		else
		{
			$preHistory = M('Finance')->where(array('fid'=>$fid,'admin_uid'=>getAdminUid()))->lock(true)->getField('history');
			$history_data = unserialize($preHistory);
			if (empty($preHistory))
				throw new \XYException(__METHOD__,-3009);
		}
		$history_data[] = $userInfo;
		$re = serialize($history_data);
		if ( strlen($re) >= C('DATABASE_HISTORY_MAX_LENGTH') )
			throw new \XYException(__METHOD__,-3998);

		return $re;
	}

    /**
     * @param bool $isAlreadyStartTrans (后台参数)
     * @param Array $fid 想要操作的单据号
     * @param $status 将要改变到的状态 只能填84(老板审核通过)或者85(老板审核不通过)
     * @return int
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */

	public function groupStatusChange($data = null ,$isAlreadyStartTrans = false)
    {
        try {
            if (!$isAlreadyStartTrans) $this->startTrans();
            $operate = array();

            if (!is_array($data['fid']))
                throw new \XYException(__METHOD__, -3009);
            if ($data['status'] != 84 && $data['status'] != 85)
                throw new \XYException(__METHOD__, -3502);

            foreach ($data['fid'] as $value) {
                $queryData = D('Finance')->field('status')->where(array('fid' => $value))->find();
                if ($queryData['status'] != 82 && $queryData['status'] != 82) {
                    throw new \XYException(__METHOD__, -3605);
                } else {
                    $operateData = array();
                    $operateData = array('fid' => $value, 'status' => $data['status']);
                    $operate[] = $this->financeOrderStatusChange($operateData, ture);
                }
            }
            foreach ($operate as $value) {
                if ($value === false || $value === null)
                    throw new \XYException(__METHOD__, -8000);
            }
            if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
            return 1;
        } catch (\XYException $e) {
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
            $e->rethrows();
        } catch (\Think\Exception $e) {
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
            throw new \XYException(__METHOD__ . '===ThinkPHP的坑===>' . $e->__toString(), -403);
        }
    }

    /**财务或创建者订单审核改变状态操作
     * @param $status 想要改变到该status
     * @param $fid 自增键
     * @param $boss_advice 老板意见
     * @param $finance_advice 财务意见
     * @param $cart 如果为待审核状态收入单的审核,并且选择审核通过时,则需要传这个参数(也就是原来在开单时传的cart)
     * @example 支出数据格式
     * {
     *             		"data":[
     *             			{
     *             				"sku_id":1,//分类ID
     *                          "spec_name":"类别名"
     *             				"item_name":"支出项目",
     *                          "is_invoice":1,
     *                          "total_price":20,
     *                          "cost_remark":"xxxxxxxx"
     *             			},
     *             			{
     *             				"sku_id":2,
     *                          "spec_name":"类别名"
     *             				"item_name":"支出项目",
     *                          "total_price":20,
     *                          "is_invoice":0,//只有支出单才填写2.没有发票
     *                          "cost_remark":"xxxxxxxx"
     *             			}
     *                    ],
     *           "account_operate":[
     *             			        {
     *             				        "account_id":1,
     *                                  "account_number":6228480323061564619,
     *             				        "cost":20
     *             			        },
     *             			        {
     *             				        "account_id":2,
     *                                  "account_number":6228480323061564618,
     *             				        "cost":20
     *             			        }
     *             		],
     *             }
     * - 81.财务单据待审核
     * - 82.财务单据经财务审核通过
     * - 83.财务单据经财务审核未通过
     * - 84.财务单据经老板审核通过
     * - 85.财务单据经老板审核未通过
     * @author DizzyLee<728394036@qq.com>
     */
    public function financeOrderStatusChange($data = null,$isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);//开启事务

            if (!$this->field('fid,status,boss_advice,finance_advice')->create($data,self::FinanceModel_financeOrderStatusChange))
                throw new \XYException(__METHOD__,$this->getError());
            $queryData = M('Finance')->where(array('fid'=>$this->fid))->find();
            //如果为待审核状态收入单的审核,并且选择审核通过时,处理cart数据
            if ($queryData['class'] == 82 && ($queryData['status'] == 81 || $queryData['status'] == 83) && $this->status != 83 && $this->status != 85)
            {
                $tmpMoneyTotal = '0.0';
                $tmpTextCart = I('param.cart','','');
                if (empty($tmpTextCart))
                    $cartTmp = $data['cart'];
                else
                    $cartTmp = json_decode($tmpTextCart, true);
                if (empty($cartTmp['data']))
                    throw new \XYException(__METHOD__, -3050);
                foreach ($cartTmp['data'] as $key => $value)
                {
                    $items['data'][$key]['sku_id'] = I('data.sku_id', '0', 'htmlspecialchars', $value);
                    $items['data'][$key]['spec_name'] = I('data.spec_name/s', '0', 'htmlspecialchars', $value);
                    $items['data'][$key]['item_name'] = I('data.item_name/s', '0', 'htmlspecialchars', $value);
                    $items['data'][$key]['total_price'] = I('data.total_price/f', '0', 'htmlspecialchars', $value);
                    $items['data'][$key]['is_invoice'] = I('data.is_invoice', '0', 'htmlspecialchars', $value);
                    $items['data'][$key]['cost_remark'] = I('data.cost_remark', ' ', 'htmlspecialchars', $value);
                }
                foreach ($cartTmp['account_operate'] as $key => $value)
                {
                    $items['account_operate'][$key]['account_name'] = I('data.account_name/s', ' ', 'htmlspecialchars', $value);
                    $items['account_operate'][$key]['account_source_type'] = I('data.account_source_type/s', ' ', 'htmlspecialchars', $value);
                    $items['account_operate'][$key]['account_source_name'] = I('data.account_source_name/s', ' ', 'htmlspecialchars', $value);
                    $items['account_operate'][$key]['account_balance'] = I('data.account_balance/d', ' ', 'htmlspecialchars', $value);
                    $items['account_operate'][$key]['account_id'] = I('data.account_id/d', '0', 'htmlspecialchars', $value);
                    $items['account_operate'][$key]['cost'] = I('data.cost/f', '0', 'htmlspecialchars', $value);
                    $items['account_operate'][$key]['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $value);
                    $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['account_operate'][$key]['cost']);
                }
                if ($tmpMoneyTotal != $queryData['income'])
                    throw new \XYException(__METHOD__, -3052);
                $data['cart'] = serialize($items);
            }
            if(is_array($data['cart']))
            {
                $cart = $data['cart'];
            }
            elseif(!isset($data['cart']))
            {
                $cart = unserialize($queryData['cart']);
            }
            else
            {
               $cart = unserialize($data['cart']);
            }
            //如果填了意见，则保存进入cart
            if (isset($data['boss_advice']))
            {
                $cart['boss_advice'] = $data['boss_advice'];
                unset($data['boss_advice']);
            }

            if (isset($data['finance_advice']))
            {
                $cart['finance_advice'] = $data['finance_advice'];
                unset($data['finance_advice']);
            }
            $fid = $this->fid;
            $update_time = time();
            $data['cart'] = serialize($cart);
            $config_ = D('Config')->getShopConfig();
            $rpg = getRpg();
            if ($rpg != 9 && $rpg != 10 && $rpg != 1 && $rpg != 5 && $rpg != 8)
                throw new \XYException(__METHOD__,-300);
            $data['update_time'] = $update_time;
            $data['history'] = $this->writeHistory($fid,'examine',$update_time);
            if ($queryData === false)
                throw new \XYException(__METHOD__,-8000);
            if ($queryData === null||$queryData == 0)
                throw new \XYException(__METHOD__,-3603);
            if ($config_['finance_mode'] == 0)//老板与财务是同一人
            {
                if ($this->status == 82)
                {
                    $this->status = 84;
                    $data['status'] = 84;
                }

                if ($this->status == 83)
                {
                    $this->status = 85;
                    $data['status'] = 85;
                }
                if ($rpg == 8 || $rpg == 9|| $rpg == 10|| $rpg == 1 || $rpg == 5)
                {
                    if ($this->status == 84 || $this->status == 85 || $queryData['class'] == 83 || $queryData['class'] == 84)
                        $result = M('Finance')->lock(true)->where(array('fid'=> $this->fid))->save($data);
                    if($this->status == 84 && $queryData['status']!= 84 || $queryData['class'] == 83 || $queryData['class'] == 84)
                    {
                        $operate = $this->financeOrderOperate($cart,$queryData['class']);
                        if (!$operate)
                            throw new \XYException(__METHOD__,-24022);
                    }
                }
            }
            elseif ($config_['finance_mode'] == 1)//老板与财务是不是同一人
            {
                if ($rpg == 8 || $rpg == 9 || $rpg == 1 || $rpg == 5)
                {
                    if ($this->status == 82)
                    {
                        $this->status = 84;
                        $data['status'] = 84;
                    }

                    if ($this->status == 83)
                    {
                        $this->status = 85;
                        $data['status'] = 85;
                    }
                    if ($this->status == 85)
                        $result = M('Finance')->where(array('fid'=> $this->fid))->save($data);
                    elseif ($this->status == 84  && $queryData['status']!= 84 || $queryData['class'] == 83 || $queryData['class'] == 84)
                    {
                        $result = M('Finance')->lock(true)->where(array('fid'=> $this->fid))->save($data);
                        $operate = $this->financeOrderOperate($cart,$queryData['class']);
                        if (!$operate)
                            throw new \XYException(__METHOD__,-24022);
                    }
                    else
                        throw new \XYException(__METHOD__,-3605);
                }
                if ($rpg == 10)
                {
                    if ($this->status == 82 || $this->status == 83 )
                        $result = M('Finance')->lock(true)->where(array('fid'=> $this->fid))->save($data);
                    elseif ($queryData['class'] == 83 || $queryData['class'] == 84)
                    {
                        $result = M('Finance')->lock(true)->where(array('fid'=> $this->fid))->save($data);
                        $operate = $this->financeOrderOperate($cart,$queryData['class']);
                        if (!$operate)
                            throw new \XYException(__METHOD__,-24022);
                    }
                    else
                        throw new \XYException(__METHOD__,-3605);
                }
            }
            else
                throw new \XYException(__METHOD__,-3604);
            if ($result === false)
                throw new \XYException(__METHOD__,-8000);
            if ($result == 0)
                throw new \XYException(__METHOD__,-3605);
            $this->commit(__METHOD__);
            return 1;

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

    /**将操作数据转换为实际操作
     * @param $cart 操作数据
     * @param $class
     * - 81.财政收入单
     * - 82.财政支出单
     * - 83.财政提现单
     * - 84.财政转账单
     * @author DizzyLee<728394036@qq.com>
     */
    protected function financeOrderOperate($cart,$class)
    {
        if ($class != 81&&$class != 82&&$class != 83&&$class != 84)
            throw new \XYException(__METHOD__,-3601);
        if ($class == 81)
        {
            foreach ($cart['data'] as $key => $value)
            {
                foreach ($value['account_operate'] as $key2 => $value2)
                {
                    if (isset($value2['account_id']) && isset($value2['cost']))
                    {
                        $cart['data'][$key]['account_operate'][$key2]['account_operation_class'] = 1;
                        $operate = D('Account')->cash_Proposal($cart['data'][$key]['account_operate'][$key2],true);
                    }
                }
            }
        }
        if ($class == 82)
        {
            foreach ($cart['account_operate'] as $key => $value)
            {
                if (isset($value['account_id']) && isset($value['cost']))
                {
                    $cart['account_operate'][$key]['account_operation_class'] = 2;
                    $operate = D('Account')->cash_Proposal($cart['account_operate'][$key],true);
                }
            }
        }
        if ($class == 83)
        {
            foreach ($cart['data'] as $key => $value)
            {
                if (isset($value['account_operate']['account_id']) && isset($value['account_operate']['cost']))
                {
                    $cart['data'][$key]['account_operate']['account_operation_class'] = 3;
                    $operate = D('Account')->cash_Proposal($cart['data'][$key]['account_operate'],true);
                }
            }
        }
        if ($class == 84)
        {
            $operatedata = $cart['data']['account_operate'];
            $operatedata['account_number'] = $cart['data']['account_number'];
            $operate = D('Account')->transfer_Accounts($operatedata,true);
        }
        return $operate;
    }

    /**
     * 统计发票池
     * @return $reData 需要填补的剩余金额
     * @author DizzyLee<728394036@qq.com>
     */
    public function invoicePoolSummary()
    {

        $begin_This_month=mktime(0,0,0,date('m'),1,date('Y'));
        $map['admin_uid'] = getAdminUid();
        $map['status'] = array('in','84,1');
        $map['class'] = array('in','82,85');//82为支出单，85为发票填补单
        $query_data = $this->where($map)->select();
        if ($query_data === false || $query_data === null)
            throw new \XYException(__METHOD__,-8000);
        $map['reg_time'] = array('egt',$begin_This_month);
        $query_data_month = $this->where($map)->select();
        if ($query_data_month === false || $query_data_month === null)
            throw new \XYException(__METHOD__,-8000);
        $reData = array('money_pit' => 0,'invoice' => 0);
        foreach ($query_data as $key => $value)
        {
            if ($value['class'] == 82)
            {
                $cart = unserialize($value['cart']);
                foreach ($cart['data'] as $key2 => $value2)
                {
                    if ($value2['is_invoice'] == 0)//如果没有发票，就把该条记录中的交易金额加入还需填补的金额
                    {
                            $reData['money_pit'] = xyadd($reData['money_pit'],$value2['total_price'],4);
                    }
                }
            }
            elseif ($value['class'] == 85)
            {
                $reData['money_pit'] = xysub($reData['money_pit'],$value['income'],4);
            }

        }
        foreach ($query_data_month as $key =>$value)
        {
            if ($value['class'] == 82)
            {
                $cart = unserialize($value['cart']);
                foreach ($cart['data'] as $key2 => $value2)
                {
                    if ($value2['is_invoice'] == 1)//如果没有发票，就把该条记录中的交易金额加入还需填补的金额
                    {
                        $reData['invoice'] = xyadd($reData['invoice'],$value2['total_price'],4);
                    }
                }
            }
            elseif ($value['class'] == 85)
            {
                $reData['invoice'] = xyadd($reData['invoice'],$value['income'],4);
            }
        }

        return $reData;
    }

    /**查询易企记单据
     * @param $update_time 最后更新时间
     * @param string $class
     * - 81.财政收入单
     * - 82.财政支出单
     * - 83.财政提现单
     * - 84.财政转账单
     * @example '81,82,83'
     * @param string $status
     * - 81.财务单据待审核
     * - 82.财务单据经财务审核通过
     * - 83.财务单据经财务审核未通过
     * - 84.财务单据经老板审核通过
     * - 85.财务单据经老板审核未通过
     * - 86.完成交易#TODO
     * @example '81,82,83,84'
     * @return $querydata
     * @author DizzyLee<728394036@qq.com>
     */
    public function query_FinanceOrder($data = null)
    {
        if (!$this->field('class,update_time,status')->create($data,self::FinanceModel_query_FinanceOrder))
        throw new \XYExption(__METHOD__,$this->getError());
        if (!isset($this->class))
            $map['class'] = array('in','81,82,83,84');
        else
            $map['class'] = array('in',$this->class);
        if (!isset($this->status))
            $map['status'] = array('in','81,82,83,84,85');
        else
            $map['status'] = array('in',$this->status);
        if (isset($this->update_time))
            $map['update_time'] = array('egt',$data['update_time']);
        $querydata = $this->field()->where($map)->select();
        if ($querydata === false)
            throw new \XYException(__METHOD__,-8000);
        if ($querydata>0)
            return $querydata;
        else
            return '数据为空';
    }


    /**
     * 新建易企记单据
     * - 81.财政收入单
     * - 82.财政支出单
     * - 83.财政提现单
     * - 84.财政转账单
     * - 85.发票填补单
     *
     *
     * @param bool $isAlreadyStartTrans 是否已经开启事务 @internal
     * @api
     * @param unsigned_int $fid 草稿用：如果有fid则为草稿转为开单，应删除此fid对应的草稿。
     * @param string $class 单据类别
     * @param $income  该单交易总金额
     * @param unsigned_int $cid 单位cid
     * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
     * @param string $remark 备注
     * @param json $cart 操作数据
     * @example 收入数据格式    {
     *                  "company": "哈哈哈"
     *             		"data":[
     *             			{
     *             				"sku_id":1,//分类
     *                          "spec_name":"类别名",
     *             				"item_name":"收入项目",
     *                          "total_price":20,
     *                          "account_operate":[
     *             			        {
     *             				        "account_id":1,
     *                                  "account_number":6228480323061564619,
     *             				        "cost":10
     *             			        },
     *             			        {
     *             				        "account_id":2,
     *                                  "account_number":6228480323061564618,
     *             				        "cost":10
     *             			        }
     *             		        ],
     *                          "cost_remark":"xxxxxxxx"
     *             			},
     *             			{
     *             				"sku_id":2,
     *                          "spec_name":"类别名",
     *             				"item_name":"收入项目",
     *                          "total_price":20,
     *      *                   "account_operate":[
     *             			        {
     *             				        "account_id":1,
     *                                  "account_number":6228480323061564619,
     *             				        "cost":10
     *             			        },
     *             			       {
     *             				        "account_id":2,
     *                                  "account_number":6228480323061564619,
     *             				        "cost":10
     *             			       }
     *             		    ]
     *                          "cost_remark":"xxxxxxxx"
     *             			}
     *             		]
     *
     *             }
     * @example 提现单操作数据数据格式
     *          {
     *             		"data":[
     *                          {
     *                          "account_name" : "hahah",
     *                          "account_source_type" : "银行账户",
     *                          "account_source_name" : "中国银行",
     *                          "account_balance" : 1000.54,
     *                          "account_operate":{
     *             			        {
     *             				        "account_id":1,
     *                                  "account_number":6228480323061564619,
     *             				        "cost":20
     *             			        },
     *             		        },
     *                          {
     *                          "account_name" : "hahah",
     *                          "account_source_type" : "银行账户",
     *                          "account_source_name" : "中国银行",
     *                          "account_balance" : 1000.54,
     *                          "account_operate":{
     *             			        {
     *             				        "account_id":1,
     *                                  "account_number":6228480323061564619,
     *             				        "cost":20
     *             			        },
     *             		        },
     *                         }
     *             			],
     *             }
     * @example 转账单操作数据数据格式
     *          {
     *             		"data":{
     *                          "account_name": "hahahah",
     *                          "target_name": "hahahah",
     *                          "account_source_name": "中国银行",
     *                          "target_source_name": "中国银行",
     *                          "account_source_type": "银行账户",
     *                          "target_source_type": "银行账户",
     *                          "account_number":6228480323061564619,
     *                          "target_number":6228480323061564619,
     *                          "account_balance" : 1000.54,
     *                          "target_balance" : 1000.54,
     *                          "account_operate":{
     *                              "account_id":1,
     *                              "target_id":2,
     *                              "cost":10
     *                         },
     *             			},
     *             }
     * @return unsigned_int 成功返回fid > 0
     * @throws \XYException
     */
    public function create_FinanceOrder(array $data = null,$isAlreadyStartTrans = false)
    {
        try
        {
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
            $tmp_reg_time = null;
            $rpg = getRpg();
            $config_ = D('Config')->getShopConfig();
            if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);

            if (!$this->field('fid,class,cid,cart,income,remark,reg_time,cid_name')->create($data,self::FinanceModel_create_FinanceOrder))

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
                    throw new \XYException(__METHOD__,-3012);
                }
            }

            if ( ($this->class != 81) && ($this->class != 82) && ($this->class != 83)&& ($this->class != 84) && ($this->class != 85))
                throw new \XYException(__METHOD__,-3001);

            if (isset($data['fid']))//如果是草稿单据转来的，则记录id，在创建成功后删除草稿单据
            {
                $draftId = $this->fid;
                unset($this->fid);
            }
            if ($this->income <= 0)
                throw new \XYException(__METHOD__,-3006);

            $this->operator_uid = session('user_auth.uid');
            $this->operator_name = session('user_auth.name');
            $tmpMoneyTotal = '0.0';
            $tmpTextCart = I('param.cart','','');
            if ($this->class == 81||$this->class == 82||$this->class == 83||$this->class == 84)
            {
                if (empty($tmpTextCart))
                {
                    $cartTmp = $data['cart'];
                }
                else
                {
                    $cartTmp = json_decode($tmpTextCart, true);
                }
                if (empty($cartTmp['data']))
                    throw new \XYException(__METHOD__, -3050);
                //JSON数据的输入验证
                $items = null;
                //开始收入或支出项目处理
                if ($this->class == 81) {
                    foreach ($cartTmp['data'] as $key => $value) {
                        $items['data'][$key]['sku_id'] = I('data.sku_id', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['spec_name'] = I('data.spec_name/s', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['item_name'] = I('data.item_name', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['total_price'] = I('data.total_price/f', 0.0, 'htmlspecialchars', $value);
                        $items['data'][$key]['cost_remark'] = I('data.cost_remark', ' ', 'htmlspecialchars', $value);
                        foreach ($value['account_operate'] as $key2 => $value2)
                        {
                            $items['data'][$key]['account_operate'][$key2]['account_name'] = I('data.account_name/s', ' ', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_source_type'] = I('data.account_source_type/s', ' ', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_source_name'] = I('data.account_source_name/s', ' ', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_balance'] = I('data.account_balance/d', ' ', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_id'] = I('data.account_id/d', '0', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['cost'] = I('data.cost/f', 0.0, 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $value2);
                            $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data'][$key]['account_operate'][$key2]['cost']);
                        }
                    }
                }
                elseif ($this->class == 82)
                {
                    foreach ($cartTmp['data'] as $key => $value)
                    {
                        $items['data'][$key]['sku_id'] = I('data.sku_id', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['spec_name'] = I('data.spec_name/s', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['item_name'] = I('data.item_name/s', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['total_price'] = I('data.total_price/f', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['is_invoice'] = I('data.is_invoice', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['cost_remark'] = I('data.cost_remark', ' ', 'htmlspecialchars', $value);
                        $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data'][$key]['total_price']);
                    }
                }
                elseif ($this->class == 83)
                {
                    $correct = array(0);
                    foreach ($cartTmp['data'] as $key => $value)
                    {
                        $items['data'][$key]['account_name'] = I('data.account_name/s', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_source_type'] = I('data.account_source_type/s', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_source_name'] = I('data.account_source_name/s', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_balance'] = I('data.account_balance/d', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_operate']['account_id'] = I('data.account_id/d', '0', 'htmlspecialchars', $value['account_operate']);
                        if (is_array($items['data'][$key]['account_operate']))
                        {
                            foreach ($correct as $value1)
                            {
                                if ($value1 == $items['data'][$key]['account_operate']['account_id'])
                                    throw new \XYException(__METHOD__,-3607);
                            }
                            $correct[] = $items['data'][$key]['account_operate']['account_id'];
                        }
                        $items['data'][$key]['account_operate']['cost'] = I('data.cost/d', 0.0, 'htmlspecialchars', $value['account_operate']);
                        $items['data'][$key]['account_operate']['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $value['account_operate']);
                        $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data'][$key]['account_operate']['cost']);
                    }
                }
                elseif ($this->class == 84)
                {
                    $items['data']['account_name'] = I('data.account_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_source_type'] = I('data.account_source_type/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_source_name'] = I('data.account_source_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_balance'] = I('data.account_balance/d', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_name'] = I('data.target_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_source_type'] = I('data.target_source_type/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_source_name'] = I('data.target_source_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_number'] = I('data.target_number/s', '0', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_balance'] = I('data.target_balance/d', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_operate']['account_id'] = I('data.account_id/d', '0', 'htmlspecialchars', $cartTmp['data']['account_operate']);
                    $items['data']['account_operate']['cost'] = I('data.cost/f', 0.0, 'htmlspecialchars', $cartTmp['data']['account_operate']);
                    $items['data']['account_operate']['target_id'] = I('data.target_id/f', '0', 'htmlspecialchars', $cartTmp['data']['account_operate']);
                    $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data']['account_operate']['cost']);
                }
                    if ($tmpMoneyTotal != $this->income)
                        throw new \XYException(__METHOD__, -3052);
                    $this->cart = serialize($items);
                    if (empty($this->cart))
                        throw new \XYException(__METHOD__,-3050);
            }
            //写历史记录
            $this->history = $this->writeHistory(0,'add',$this->update_time);

            //设置单号
            if ($this->class == 81) $this->sn = $this->getNextSn('FIS');
            if ($this->class == 82) $this->sn = $this->getNextSn('FES');
            if ($this->class == 83) $this->sn = $this->getNextSn('FCO');
            if ($this->class == 84) $this->sn = $this->getNextSn('FTF');
            if ($this->class == 85) $this->sn = $this->getNextSn('PIO');
            if ($this->class == 81 || $this->class == 82 || $this->class == 83 || $this->class == 84)
                $this->status = 81;
            elseif ($this->class == 85)
                $this->status = 1; //发票单据在开完以后马上生效,只做统计用,所以直接为老板已审核状态;

            //删除对应的草稿单
            $fid = $this->add();
            if ($fid > 0)
            {
                if (isset($data['fid']))
                    $this->deleteDraft(array('fid'=>$draftId));
                $return_finance = D('Finance')->where(array('fid'=>$fid))->find();
                if (($return_finance['class'] == 83 || $return_finance['class'] == 84) )
                {
                    $operate_data = array('fid'=>$fid,'status'=>1);
                    $change = $this->financeOrderStatusChange($operate_data,true);
                }
                if ($config_['finance_mode'] == 1)
                {
                    if ($this->class ==81 && ($rpg == 1 || $rpg == 5 || $rpg == 8 || $rpg == 9))
                    {
                        $operate_data = array('fid'=>$fid,'status'=>84);
                        $change = $this->financeOrderStatusChange($operate_data,true);
                    }
                    if ($this->class ==81 && $rpg == 10)
                    {
                        $operate_data = array('fid'=>$fid,'status'=>82);
                        $change = $this->financeOrderStatusChange($operate_data,true);
                    }
                }
                elseif($config_['finance_mode'] == 0)
                {
                    if ($this->class ==81 && ($rpg == 1 || $rpg == 5 || $rpg == 8 || $rpg == 9 || $rpg == 10))
                    {
                        $operate_data = array('fid'=>$fid,'status'=>84);
                        $change = $this->financeOrderStatusChange($operate_data,true);
                    }

                }
                if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                return $fid;
            }
            else
                throw new \XYException(__METHOD__,-3000);
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
    public function create_FinanceOrderDraft($data = null,$isAlreadyStartTrans = false)
    {
        try
        {
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
            $tmp_reg_time = null;
            if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);

            if (!$this->field('fid,class,cid,cart,income,remark,reg_time,is_invoice,cid_name')->create($data,self::FinanceModel_create_FinanceOrderDraft))

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
                    throw new \XYException(__METHOD__,-3012);
                }
            }

            if ( ($this->class != 81) && ($this->class != 82) && ($this->class != 83)&& ($this->class != 84) && ($this->class != 85))
                throw new \XYException(__METHOD__,-3001);

            if (isset($data['fid']))//如果是草稿单据转来的，则记录id，在创建成功后删除草稿单据
            {
                $draftId = $this->fid;
                unset($this->fid);
            }
            $this->operator_uid = session('user_auth.uid');
            $this->operator_name = session('user_auth.name');
            $tmpMoneyTotal = 0.0;
            $tmpTextCart = I('param.cart','','');
            if (empty($tmpTextCart))
                $cartTmp = $data['cart'];
            else
                $cartTmp = json_decode($tmpTextCart,true);
            //JSON数据的输入验证
            $items = null;
            //开始收入或支出项目处理
            if ($this->class == 81||$this->class == 82||$this->class == 83||$this->class == 84)
            {
                if (empty($tmpTextCart))
                    $cartTmp = $data['cart'];
                else
                    $cartTmp = json_decode($tmpTextCart, true);
                //JSON数据的输入验证
                $items = null;
                //开始收入或支出项目处理
                if ($this->class == 81) {
                    foreach ($cartTmp['data'] as $key => $value) {
                        $items['data'][$key]['sku_id'] = I('data.sku_id', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['spec_name'] = I('data.spec_name/s', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['item_name'] = I('data.item_name', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['total_price'] = I('data.total_price/f', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['cost_remark'] = I('data.cost_remark', ' ', 'htmlspecialchars', $value);
                        foreach ($value['account_operate'] as $key2 => $value2)
                        {
                            $items['data'][$key]['account_operate'][$key2]['account_id'] = I('data.account_id/d', '0', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['cost'] = I('data.cost/f', '0', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $value2);
                            $items['data'][$key]['account_operate'][$key2]['account_operation_class'] = I('data.account_operation_class/f', '0', 'htmlspecialchars', $value2);
                            $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data'][$key]['account_operate'][$key2]['cost']);
                        }
                    }
                }
                elseif ($this->class == 82)
                {
                    foreach ($cartTmp['data'] as $key => $value)
                    {
                        $items['data'][$key]['sku_id'] = I('data.sku_id', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['spec_name'] = I('data.spec_name/s', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['item_name'] = I('data.item_name/s', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['total_price'] = I('data.total_price/f', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['is_invoice'] = I('data.is_invoice', '0', 'htmlspecialchars', $value);
                        $items['data'][$key]['cost_remark'] = I('data.cost_remark', ' ', 'htmlspecialchars', $value);
                        $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data'][$key]['total_price']);
                    }
                }
                elseif ($this->class == 83)
                {
                    foreach ($cartTmp['data'] as $key => $value)
                    {
                        $correct = array(0);
                        $items['data'][$key]['account_name'] = I('data.account_name/s', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_source_type'] = I('data.account_source_type/s', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_source_name'] = I('data.account_source_name/s', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_balance'] = I('data.account_balance/d', ' ', 'htmlspecialchars', $value);
                        $items['data'][$key]['account_operate']['account_id'] = I('data.account_id/d', '0', 'htmlspecialchars', $value['account_operate']);
                        foreach ($correct as $value1)
                        {
                            if ($value1 == $items['data'][$key]['account_operate']['account_id'])
                                throw new \XYException(__METHOD__,-3607);
                        }
                        $correct[] = $items['data'][$key]['account_operate']['account_id'];
                        $items['data'][$key]['account_operate']['cost'] = I('data.cost/d', '0', 'htmlspecialchars', $value['account_operate']);
                        $items['data'][$key]['account_operate']['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $value['account_operate']);
                        $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data'][$key]['account_operate']['cost']);
                    }
                }
                elseif ($this->class == 84)
                {
                    $items['data']['account_name'] = I('data.account_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_source_type'] = I('data.account_source_type/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_source_name'] = I('data.account_source_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_number'] = I('data.account_number/s', '0', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_balance'] = I('data.account_balance/d', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_name'] = I('data.target_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_source_type'] = I('data.target_source_type/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_source_name'] = I('data.target_source_name/s', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_number'] = I('data.target_number/s', '0', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['target_balance'] = I('data.target_balance/d', ' ', 'htmlspecialchars', $cartTmp['data']);
                    $items['data']['account_operate']['account_id'] = I('data.account_id/d', '0', 'htmlspecialchars', $cartTmp['data']['account_operate']);
                    $items['data']['account_operate']['cost'] = I('data.cost/f', 0.0, 'htmlspecialchars', $cartTmp['data']['account_operate']);
                    $items['data']['account_operate']['target_id'] = I('data.target_id/f', '0', 'htmlspecialchars', $cartTmp['data']['account_operate']);
                    $tmpMoneyTotal = xyadd($tmpMoneyTotal, $items['data']['account_operate']['cost']);
                }
                $this->cart = serialize($items);

            }
            $this->status = 89;
            //写历史记录
            $this->history = $this->writeHistory(0,'add',$this->update_time);


            if (empty($this->fid))
            {
                //设置单号
                if ($this->class == 81) $this->sn = $this->getNextSn('FIS');
                if ($this->class == 82) $this->sn = $this->getNextSn('FES');
                if ($this->class == 83) $this->sn = $this->getNextSn('FCO');
                if ($this->class == 84) $this->sn = $this->getNextSn('FTF');
                if ($this->class == 85) $this->sn = $this->getNextSn('PIO');
                $fid = $this->add();
                if ($fid > 0)
                {
                    if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                    return $fid;
                }
            }
            else
            {
                $fid = $this->save();
                if ( ($fid !== false) && ($fid !== null) )
                {
                    if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                    return 1;
                }
            }
                throw new \XYException(__METHOD__,-3000);

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
	 * 新建收款单、付款单.
	 * 
	 * 这里会出现两类4种情况：（下面的s、t、v均为正数）
	 * 1.收款单
	 * 		1-本次收款金额s<=总的需要收款的金额t，一切正常
	 * 		2-本次收款金额s>总的需要收款的金额t，用s还完t后还有多余，则拒绝
	 * 2.付款单
	 * 		1-本次付款金额s<=总的需要付款的金额t，一切正常
	 * 		2-本次付款金额s>总的需要付款的金额t，用s付完t后还有多余，则拒绝
	 *
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 是否已经开启事务 @internal
	 *
	 * @param unsigned_int $fid 草稿用：如果有fid则为草稿转为开单，应删除此fid对应的草稿。
	 * @param string $class 单据类别
	 * @param unsigned_int $cid 单位cid
	 * @param double $cash 现金
	 * @param double $bank 银行
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @param double $online_pay 在线支付
	 * @param string $remark 备注
	 * @param json $cart 订单列表
	 * @example    {
	 *             		"data":[
	 *             			{
	 *             				"oid":1,
	 *             				"money":10
	 *             			},
	 *             			{
	 *             				"oid":2,
	 *             				"money":10
	 *             			}
	 *             		]
	 *             }
	 *
	 * @example mini:{"data": [{"oid": 1,"money": 10},{"oid": 2,"money": 10}]}
	 * 
	 * @return unsigned_int 成功返回fid > 0
	 * @throws \XYException
	 */
	public function createReceiptOrPayment($data = null,$isAlreadyStartTrans = false)
	{
		$dbCompany = D('Company');
		$dbOrder = D('Order');

		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$tmp_reg_time = null;
			if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);

			if (!$this->field('fid,class,cid,cash,bank,online_pay,remark,operator_uid,operator_name')->create($data,self::MODEL_INSERT))
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
					throw new \XYException(__METHOD__,-3012);
				}
			}
			
			if ( ($this->class != 71) && ($this->class != 72) )
				throw new \XYException(__METHOD__,-3001);

			if (isset($data['fid']))//如果是草稿单据转来的，则记录id，在创建成功后删除草稿单据
			{
				$draftId = $this->fid;
				unset($this->fid);
			}


			//给queryRemain准备 模式
			if ($this->class == 71)
				$remainType = 1;
			else
				$remainType = 2;
			$this->cid_name = $dbCompany->getCompanyName($this->cid,1);

			$this->income = xyadd(xyadd($this->cash,$this->bank,0),$this->online_pay);
			if ($this->income <= 0)
				throw new \XYException(__METHOD__,-3006);
            if (!isset($this->operator_uid)||!isset($this->operator_name))
            {
                $this->operator_uid = session('user_auth.uid');
                $this->operator_name = session('user_auth.name');
            }
			//开始处理购物车
			$tmpMoneyTotal = 0.0;
			$tmpTextCart = I('param.cart','','');
			if (empty($tmpTextCart))
				$cartTmp = $data['cart'];
			else
				$cartTmp = json_decode(I('param.cart','',''),true);
			if (empty($cartTmp['data']))
				throw new \XYException(__METHOD__,-3050);

			$tmpOidList = null;
			$cart = null;
			foreach ($cartTmp['data'] as $key => $value)
			{
				if (	isUnsignedInt($value['oid']) &&
						isNonegativeReal($value['money'])
					)
				{
					$cart[$key]['oid'] = I('data.oid/d',0,'htmlspecialchars',$value);
					$tmpOidList[] = $cart[$key]['oid'];
					$cart[$key]['money'] = I('data.money/f',0,'htmlspecialchars',$value);
					$tmpMoneyTotal = xyadd($tmpMoneyTotal,$cart[$key]['money']);
				}
				else
					throw new \XYException(__METHOD__,-3050);
			}
			if (empty($cart))
				throw new \XYException(__METHOD__,-3050);

			if ($tmpMoneyTotal !== $this->income)
				throw new \XYException(__METHOD__,-3052);

			//queryRemain
			$orderData = null;
			$orderData = D('Company')->queryRemain(
									array(
										'type'=>$remainType,
										'cid'=>$this->cid,
									),
									true,
									$tmpOidList
								);

			//抹平订单
			$orderUpdateData = null;
			foreach ($cart as $i => $value)
			{
				if (!isset($orderData[$value['oid']]))
					throw new \XYException($value['oid'],-3051);

				if ( ($orderData[$value['oid']]['status'] ==  91) || ($orderData[$value['oid']]['status'] == 3) )
					throw new \XYException(__METHOD__,-3053);

				//给查询的时候存储快照数据
				$cart[$i]['reg_time']      = $orderData[$value['oid']]['reg_time'];
				$cart[$i]['leave_time']    = $orderData[$value['oid']]['leave_time'];
				$cart[$i]['class']         = $orderData[$value['oid']]['class'];
				$cart[$i]['operator_uid']  = $orderData[$value['oid']]['operator_uid'];
				$cart[$i]['operator_name'] = $orderData[$value['oid']]['operator_name'];
				$cart[$i]['status']        = $orderData[$value['oid']]['status'];
				$cart[$i]['value']         = $orderData[$value['oid']]['value'];
				$cart[$i]['off']           = $orderData[$value['oid']]['off'];
				$cart[$i]['receivable']    = $orderData[$value['oid']]['receivable'];
				$cart[$i]['remain']        = $orderData[$value['oid']]['remain'];
				$cart[$i]['sn']            = $orderData[$value['oid']]['sn'];

				//准备oid抹平账目
				$orderUpdateData[$i] = array(
						'oid'         => $orderData[$value['oid']]['oid'],
						'remain'      => $orderData[$value['oid']]['remain'],
						'update_time' => $this->update_time
					);

				//判断有木有超出可以收/付款的最大额度，即$value['money'] <= |$orderUpdateData[$i]['remain']|
				//$value['money']：还/付的钱
				//|$orderUpdateData[$i]['remain']|：待还/付
				if ($value['money'] > abs($orderUpdateData[$i]['remain']))
					throw new \XYException($value['oid'],-3011);
				
				if ($this->class == 71)
					/*$value['money'] = 0 - $value['money'];
				$orderUpdateData[$i]['remain'] += $value['money'];*/
					$orderUpdateData[$i]['remain'] = xysub($orderUpdateData[$i]['remain'],$value['money']);
				else
					$orderUpdateData[$i]['remain'] = xyadd($orderUpdateData[$i]['remain'],$value['money']);

			}

			//todo:一次做多个sql
			foreach ($orderUpdateData as $i => $value)
			{
				$tmpUpdateReturn = false;
				$tmpUpdateReturn = $dbOrder->save($value);
				if ( empty($tmpUpdateReturn) )
					throw new \XYException(__METHOD__,-3000);
			}


			//变更Company里的余额信息
			$dbCompany->setBalanceInFinance($this->cid,$this->income,$this->class);


			//写历史记录
			$this->history = $this->writeHistory(0,'add',$this->update_time);

			$this->cart = serialize($cart);
			if (empty($this->cart))
				throw new \XYException(__METHOD__,-3050);

			if ($this->class == 71) $this->sn = $this->getNextSn('FSK');
			if ($this->class == 72) $this->sn = $this->getNextSn('FFK');

			//删除对应的草稿单
			$fid = $this->add();
			if ($fid > 0)
			{
				if (isset($data['fid']))
					$this->deleteDraft(array('fid'=>$draftId));

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $fid;
			}
			else
				throw new \XYException(__METHOD__,-3000);
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
	 * 新建其他收入单、费用单
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * @param unsigned_int $fid 草稿用：如果有fid则为草稿转为开单，应删除此fid对应的草稿。
	 * @param string $class 单据类别
	 * @param string $name 收入来源名称或费用用途名称
	 * @param double $cash 现金
	 * @param double $bank 银行
	 * @param double $online_pay 在线支付
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @param string $remark 备注
	 * 
	 * @return unsigned_int 成功返回fid > 0
	 * @throws \XYException
	 */
	public function createIncomeOrExpense(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$tmp_reg_time = null;
			if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);

			if (!$this->field('fid,class,name,cash,bank,online_pay,remark')->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());

			//判断单据类型是否为其他收入单或费用单
			if ( ($this->class != 73) && ($this->class != 74) )
				throw new \XYException(__METHOD__,-3001);

			//必须放在create下面，因为create会自动创建一个时间
			if (isset($tmp_reg_time))
			{
				if($this->check_unix_date($tmp_reg_time))
				{
					$this->reg_time = $tmp_reg_time;
					$this->update_time = $tmp_reg_time;	
				}else
				{
					throw new \XYException(__METHOD__,-3012);
				}
			}

			if (isset($data['fid']))//如果是草稿单据转来的，则记录id，在创建成功后删除草稿单据
			{
				$draftId = $this->fid;
				unset($this->fid);
			}

			//计算总的收入
			$this->income = xyadd(xyadd($this->cash,$this->bank,0),$this->online_pay);
			//判断是否合法
			if ($this->income <= 0)
				throw new \XYException(__METHOD__,-3006);
			//获取操作人员的uid和用户名
			$this->operator_uid = session('user_auth.uid');
			$this->operator_name = session('user_auth.name');

			$this->history = $this->writeHistory(0,'add',$this->update_time);


			if ($this->class == 73) $this->sn = $this->getNextSn('FQS');
			if ($this->class == 74) $this->sn = $this->getNextSn('FFY');
			$fid = $this->add();

			//删除对应的草稿单
			if ($fid > 0)
			{
				if (isset($data['fid']))
					$this->deleteDraft(array('fid'=>$draftId));

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $fid;
			}
			else
				throw new \XYException(__METHOD__,-3000);
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
	 * 编辑单据信息。
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $fid
	 * @param string $remark 备注
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function editDocument(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);


			if (!$this->field('fid,remark')->create($data,self::MODEL_UPDATE))
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
			$this->history = $this->writeHistory($this->fid,'edit',$this->update_time,$arr);//$arr必须初始化为null，这是为了通过“没有任何修改却提交了”的这种情况

			//更新
			$tmp = $this->where(array('fid'=>$this->fid,'admin_uid'=>$this->admin_uid))->setField(array('remark'=>$this->remark,'history'=>$this->history));
			if ( ($tmp === false) || ($tmp === null) )
				throw new \XYException(__METHOD__,-3000);
			
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
	 * 新建收款单草稿、付款单草稿.
	 *
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * @param unsigned_int $fid 为空是新建草稿，有值是修改草稿
	 * @param string $class 单据类别
	 * @param unsigned_int $cid 单位cid
	 * @param double $cash 现金
	 * @param double $bank 银行
	 * @param double $online_pay 在线支付
	 * @param string $remark 备注
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @param json $cart 订单列表
	 * @example       {
	 *             		"data":[
	 *             			{
	 *             				"oid":1,
	 *             				"money":10
	 *             			},
	 *             			{
	 *             				"oid":2,
	 *             				"money":10
	 *             			}
	 *             		]
	 *             }
	 *
	 * @example mini:{"data":[{"oid":1,"money":10},{"oid":2,"money":10}]}
	 * 
	 * @return unsigned_int 成功返回fid > 0（新建）或1（修改）
	 * @throws \XYException
	 */
	public function createReceiptOrPaymentDraft(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			$dbCompany = D('Company');

			$tmp_reg_time = null;
			if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);

			if (!$this->field('fid,class,cid,cash,bank,online_pay,remark')->create($data,self::MODEL_CREATE_DRAFT))
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
					throw new \XYException(__METHOD__,-3012);
				}
			}

			if ( ($this->class != 71) && ($this->class != 72) )
				throw new \XYException(__METHOD__,-3001);

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
				throw new \XYException(__METHOD__,-3050);

			$cart = null;
			foreach ($cartTmp['data'] as $key => $value)
			{
				if (	isUnsignedInt($value['oid']) &&
						isNonegativeReal($value['money'])
					)
				{
					$cart[$key]['oid'] = I('data.oid/d',0,'htmlspecialchars',$value);
					$cart[$key]['money'] = I('data.money/f',0,'htmlspecialchars',$value);
				}
				else
					throw new \XYException(__METHOD__,-3050);
			}
			if ( empty($cartTmp) && ( (!empty($paramCartTmp)) || (!empty($data['cart'])) ) )
				throw new \XYException(__METHOD__,-3050);

			if (!empty($cart))
			{
				$this->cart = serialize($cart);
				if (empty($this->cart))
					throw new \XYException(__METHOD__,-3050);
			}

			$this->status = 100;

			if (empty($this->fid))
			{
				$this->sn = $this->getNextSn('DRA');
				$fid = $this->add();
				if ($fid > 0)
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return $fid;
				}
			}
			else
			{
				$fid = $this->save();
				if ( ($fid !== false) && ($fid !== null) )
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}

			throw new \XYException(__METHOD__,-3000);
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
     * 作废未审核财务单据
     * @param $fid 单据ID
     * @param bool $isAlreadyStartTrans
     * @author DizzyLee<728394036@qq.com>
     **/
	public function delete_FinanceOrder($data = null,$isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans = false) $this->startTrans();
            if (!$this->field('fid')->create($data,self::FinanceModel_delete_FinanceOrder));
            $map['fid'] = $this->fid;
            $this->status = 3;
            $rpg = getRpg();
            $uid = getUid();
            $queryData = M('Finance')->where($map)->find();
            if ($queryData === false || $queryData === null)
                throw new \XYException(__METHOD__,-8000);
            if ($queryData>0 && $queryData['status'] == 81)
            {
             //财务未审核单据所有人可作废自己开的单据
                if ($queryData['operator_uid'] == $uid)
                    $operate = $this->save();
                else
                    throw new \XYException(__METHOD__,-3609);
                if ($operate === null || $operate === false)
                    throw new \XYException(__METHOD__,-8000);
            }
            else
                throw new \XYException(__METHOD__,-3609);
            if ($operate > 0)
            {
                $this->commit(__METHOD__);
                return 1;
            }
        }catch (\XYException $e)
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
	 * 新建其他收入单草稿、费用单草稿
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $fid 为空是新建草稿，有值是修改草稿
	 * @param string $class 单据类别
	 * @param string $name 收入来源名称或费用用途名称
	 * @param double $cash 现金
	 * @param double $bank 银行
	 * @param double $online_pay 在线支付
	 * @param unsigned_int $reg_time 创建时间，可选参数。默认开单时间是当时，如果传入此项，则按此项时间。
	 * @param string $remark 备注
	 * 
	 * @return unsigned_int 成功返回fid > 0（新建）或1（修改）
	 * @throws \XYException
	 */
	public function createIncomeOrExpenseDraft(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$tmp_reg_time = null;
			if (isset($data['reg_time'])) $tmp_reg_time = I('data.reg_time/d',0,'htmlspecialchars',$data);

			if (!$this->field('fid,class,name,cash,bank,online_pay,remark')->create($data,self::MODEL_CREATE_DRAFT))
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
					throw new \XYException(__METHOD__,-3012);
				}
			}

			if ( ($this->class != 73) && ($this->class != 74) )
				throw new \XYException(__METHOD__,-3001);

			$this->status = 100;

			
			if (empty($this->fid))
			{
				$this->sn = $this->getNextSn('DRA');
				$fid = $this->add();
				if ($fid > 0)
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return $fid;
				}
			}
			else
			{
				$fid = $this->save();
				if ( ($fid !== false) && ($fid !== null) )
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}

			throw new \XYException(__METHOD__,-3000);
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
	 * 查询单个财务单据
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param unsigned_int $fid 要查询的那个单据的主键
	 * 
	 * @return array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 */
	public function queryOneDocument(array $data = null)
	{
		if (!$this->field('fid')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpInfo = $this->where(array('fid'=>$this->fid,'admin_uid'=>session('user_auth.admin_uid')))->find();
		
		if (empty($tmpInfo))
			throw new \XYException(__METHOD__,-3009);

		if ( ($tmpInfo['class'] == 71) || ($tmpInfo['class'] == 72) || ($tmpInfo['class'] == 100))
		{
			if ( ($tmpInfo['status'] == 100) && (empty($tmpInfo['cart'])) )//草稿单可以没有购物车
				;
			else
			{
				//将购物车换回json
				$tmpSerialize = null;
				$tmpSerialize = unserialize($tmpInfo['cart']);
				if ($tmpSerialize === false)
					throw new \XYException(__METHOD__,-3050);
				$tmpInfo['cart'] = $tmpSerialize;
			}
		}
		if(!empty($tmpInfo['history']))
            $tmpInfo['history'] = unserialize($tmpInfo['history']);
		return $tmpInfo;
	}

    /**按时间统计支出金额
     * @param $reg_st_time 开始时间戳
     * @param $reg_end_time 结束时间戳
     * @return $returnData
     * @author DizzyLee<728394036@qq.com>
     */
	public function getExpenditureStatistics($data = null)
    {
        $st_date = getdate($data['reg_st_time']);
        $end_date = getdate($data['reg_end_time']);
        $timeDiff = $data['reg_end_time'] - $data['reg_st_time'];
        $st_time = 0;
        $end_time = 0;
        //小于7天按七天显示
        if ($timeDiff <= 604800)
        {
            $st_time = mktime(0,0,0,$end_date['mon'],$end_date['mday']+1,$end_date['year'])-604800;
            $end_time = $st_time+604799;
        }
        //大于七天，小于等于一个月按天显示
        if ($timeDiff > 604800 && $timeDiff <=2678400)
        {
            $st_time = mktime(0,0,0,$st_date['mon'],$st_date['mday'],$st_date['year']);
            $end_time = mktime(23,59,59,$end_date['mon'],$end_date['mday'],$end_date['year']);
        }
        //大于一个月，小于15周按周显示，并且从开始时间的周一开始算，算到结束时间的周天
        if ($timeDiff >2678400 && $timeDiff <=9072000)
        {
            $st_time = $this->getWeekFirstDay(intval($data['reg_st_time']));
            $end_time = $this->getWeekLastDay(intval($data['reg_end_time']));
        }
        //大于15周按月显示，从开始时间的当月一号，统计到结束时间当月最后一天
        if ($timeDiff >9072000)
        {
            $st_time = mktime(0,0,0,$st_date['mon'],1,$st_date['year']);
            $end_time = $this->getMonthLastDay($data['reg_end_time']);
        }
        //支出单据查询参数
        $mapExp['admin_uid'] = getAdminUid();
        $mapExp['status'] = 84;
        $mapExp['class'] = 82;
        $mapExp['reg_time'] = array(array('egt',intval($st_time)),array('elt',intval($end_time)));

        //收入单据查询参数
        $mapInc['status'] = 84;
        $mapInc['class'] = 81;
        $mapInc['admin_uid'] = $mapExp['admin_uid'];
        $mapInc['reg_time'] = $mapExp['reg_time'];
        //数据查询
        $query_data_Exp = D('Finance')->where($mapExp)->select();
        if ($query_data_Exp === false || $query_data_Exp === null)
            throw new \XYException(__METHOD__,-8000);
        $query_data_Inc = D('Finance')->where($mapInc)->select();
        if ($query_data_Inc === false || $query_data_Inc === null)
            throw new \XYException(__METHOD__,-8000);

        //通过传入时间条件对数据进行分类
        foreach ($query_data_Exp as $key => $value)
        {
            $tmpDate = null;
            $tmpDate = getdate($value['reg_time']);
            if ($timeDiff <= 2678400)
            {
                $query_data_Exp[$key]['reg_time'] = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
            }
            elseif ($timeDiff > 2678400 && $timeDiff <= 9072000)
             {
                 $query_data_Exp[$key]['reg_time'] = $this->getWeekFirstDay($value['reg_time']);
             }
            elseif ($timeDiff > 9072000)
            {
                $query_data_Exp[$key]['reg_time'] = mktime(0,0,0,$tmpDate['mon'],1,$tmpDate['year']);
            }
        }

        foreach ($query_data_Inc as $key => $value)
        {
            $tmpDate = null;
            $tmpDate = getdate($value['reg_time']);
            if ($timeDiff <= 2678400)
            {
                $query_data_Inc[$key]['reg_time'] = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
            }
            elseif ($timeDiff > 2678400 && $timeDiff <= 9072000)
            {
                $query_data_Inc[$key]['reg_time'] = $this->getWeekFirstDay($value['reg_time']);
            }
            elseif ($timeDiff > 9072000)
            {
                $query_data_Inc[$key]['reg_time'] = mktime(0,0,0,$tmpDate['mon'],1,$tmpDate['year']);
            }
        }
        //产生空数据
        $tmptotal_data = array();
        $returnData = array();
        $time_interval = 0;
        if ($timeDiff <= 2678400)
            $time_interval = 86400;
        elseif ($timeDiff > 2678400 && $timeDiff <= 9072000)
            $time_interval = 604800;
        $cat_array = D('Sku')->queryFinanceCart($data['type'] = '1');
        foreach ($cat_array as $key => $value)
        {
            $tmptotal_data['class']['inc'][$value['sku_id']] = array();
            $tmptotal_data['class']['exp'][$value['sku_id']] = array();
            if ($timeDiff <= 9072000)
            {
                for ($i = $st_time;$i <= $end_time;$i = $i+$time_interval)
                {
                    $tmptotal_data['class']['exp'][$value['sku_id']]['price'][$i] = 0;
                    $tmptotal_data['class']['exp'][$value['sku_id']]['cat_name'] = $value['spec_name'];
                    $tmptotal_data['class']['inc'][$value['sku_id']]['price'][$i] = 0;
                    $tmptotal_data['class']['inc'][$value['sku_id']]['cat_name'] = $value['spec_name'];
                }
            }
            elseif ($timeDiff >9072000)
            {
                for ($i = $st_time;$i<= $end_time;$i = $next_month)
                {
                    $tmptotal_data['class']['exp'][$value['sku_id']]['price'][$i] = 0;
                    $tmptotal_data['class']['exp'][$value['sku_id']]['cat_name'] = $value['spec_name'];
                    $tmptotal_data['class']['inc'][$value['sku_id']]['price'][$i] = 0;
                    $tmptotal_data['class']['inc'][$value['sku_id']]['cat_name'] = $value['spec_name'];
                    $next_month = $this->getMonthLastDay($i)+1;
                }
            }
        }
        $tmptotal_data['all']['inc'] = array();
        $tmptotal_data['all']['exp'] = array();
        if ($timeDiff <= 9072000)
        {
            for ($i = $st_time;$i <= $end_time;$i = $i+$time_interval)
            {
                $tmptotal_data['all']['exp'][$i] = 0;
                $tmptotal_data['all']['inc'][$i] = 0;
            }
        }
        elseif ($timeDiff >9072000)
        {
            for ($i = $st_time;$i<= $end_time;$i = $next_month)
            {
                $tmptotal_data['all']['exp'][$i] = 0;
                $tmptotal_data['all']['inc'][$i] = 0;
                $next_date = getdate($i);
                $next_month = $this->getMonthLastDay($i)+1;
            }
        }
        //支出数据统计
        foreach ($query_data_Exp as $key => $value)
        {
            $tmpcart = unserialize($value['cart']);
            foreach ($tmpcart['data'] as $key1 => $value1)
            {
                $time  = $query_data_Exp[$key]['reg_time'];
                $cat = $value1['sku_id'];
                if (!isset($tmptotal_data['class']['exp'][$cat]))
                {
                    if ($timeDiff <= 9072000)
                    {
                        for ($i = $st_time;$i <= $end_time;$i = $i+$time_interval)
                        {
                            $tmptotal_data['class']['exp'][$cat]['price'][$i] = 0;
                            if (!isset($tmptotal_data['class']['exp'][$cat]['cat_name']))
                                $tmptotal_data['class']['exp'][$cat]['cat_name'] = '未知';
                        }
                    }
                    elseif ($timeDiff >9072000)
                    {
                        for ($i = $st_time;$i<= $end_time;$i = $next_month)
                        {
                            $tmptotal_data['class']['exp'][$cat]['price'][$i] = 0;
                            if (!isset($tmptotal_data['class']['exp'][$cat]['cat_name']))
                                $tmptotal_data['class']['exp'][$cat]['cat_name'] = '未知';
                            $next_month = $this->getMonthLastDay($i)+1;
                        }
                    }
                }
                $tmptotal_data['class']['exp'][$cat]['price'][$time] = xyadd($tmptotal_data['class']['exp'][$cat]['price'][$time],$value1['total_price']);
            }
        }
        foreach ($query_data_Exp as $key => $value)
        {
            $time  = $query_data_Exp[$key]['reg_time'];
            $tmptotal_data['all']['exp'][$time] = xyadd($tmptotal_data['all']['exp'][$time],$value['income']);
        }
        //支出返回数据数据封装
        foreach ($tmptotal_data['class']['exp'] as $key => $value) {
            $data = array();
            foreach ($value['price'] as $key1 => $value1) {
                $reTmpDate = getdate($key1);
                $reTmpDate2 = getdate($key1+518400);
                if ($timeDiff <= 2678400)
                    $data[] = array('time' => $reTmpDate['mon'].'.'.$reTmpDate['mday'], 'price' => $value1);
                elseif ($timeDiff > 2678400 && $timeDiff <= 9072000)
                    $data[] = array('time' => $reTmpDate['mon'].'.'.$reTmpDate['mday'].'-'.$reTmpDate2['mon'].'.'.$reTmpDate2['mday'], 'price' => $value1);
                elseif ($timeDiff > 9072000)
                    $data[] = array('time' => $reTmpDate['year'].'.'.$reTmpDate['mon'], 'price' => $value1);
            }
            $returnData['class_data']['exp'][] = array('cat_name' => $value['cat_name'],'pricedata' => $data);
        }
        foreach ($tmptotal_data['class']['exp'] as $key => $value)
        {
            $price = 0;
            foreach ($value['price'] as $key1 => $value1)
            {
                $price = $price + $value1;
            }
            $returnData['Pie_chart_data']['exp'][]= array('cat_name' => $value['cat_name'] , 'price' => $price);
        }
        unset($tmptotal_data['class']['exp']);
        foreach ($tmptotal_data['all']['exp'] as $key => $value)
        {
            $reTmpDate = getdate($key);
            $reTmpDate2 = getdate($key+518400);
            if ($timeDiff <= 2678400)
                $returnData['all_data']['exp'][] = array('time'=> $reTmpDate['mon'].'.'.$reTmpDate['mday'],'price' => $value);
            elseif ($timeDiff > 2678400 && $timeDiff <= 9072000)
                $returnData['all_data']['exp'][] = array('time'=> $reTmpDate['mon'].'.'.$reTmpDate['mday'].'-'.$reTmpDate2['mon'].'.'.$reTmpDate2['mday'],'price' => $value);
            elseif ($timeDiff > 9072000)
                $returnData['all_data']['exp'][] = array('time'=> $reTmpDate['year'].'.'.$reTmpDate['mon'],'price' => $value);
        }
        unset($tmptotal_data['all']['exp']);
        //收入数据统计
        foreach ($query_data_Inc as $key => $value)
        {
            $tmpcart = unserialize($value['cart']);
            foreach ($tmpcart['data'] as $key1 => $value1)
            {
                $time  = $query_data_Inc[$key]['reg_time'];
                $cat = $value1['sku_id'];
                if (!isset($tmptotal_data['class']['inc'][$cat]))
                {
                    if ($timeDiff <= 9072000)
                    {
                        for ($i = $st_time;$i <= $end_time;$i = $i+$time_interval)
                        {
                            $tmptotal_data['class']['inc'][$cat]['price'][$i] = 0;
                            if (!isset($tmptotal_data['class']['inc'][$cat]['cat_name']))
                                $tmptotal_data['class']['inc'][$cat]['cat_name'] = '未知';
                        }
                    }
                    elseif ($timeDiff >9072000)
                    {
                        for ($i = $st_time;$i<= $end_time;$i = $next_month)
                        {
                            $tmptotal_data['class']['inc'][$cat]['price'][$i] = 0;
                            if (!isset($tmptotal_data['class']['inc'][$cat]['cat_name']))
                                $tmptotal_data['class']['inc'][$cat]['cat_name'] = '未知';
                            $next_month = $this->getMonthLastDay($i)+1;
                        }
                    }
                }
                $tmptotal_data['class']['inc'][$cat]['price'][$time] = xyadd($tmptotal_data['class']['inc'][$cat]['price'][$time],$value1['total_price']);
            }
        }
        foreach ($query_data_Inc as $key => $value)
        {
            $time  = $query_data_Inc[$key]['reg_time'];
            $tmptotal_data['all']['inc'][$time] = xyadd($tmptotal_data['all']['inc'][$time],$value['income']);
        }

        //收入返回数据数据封装
        foreach ($tmptotal_data['class']['inc'] as $key => $value)
        {
            $data = array();
            foreach ($value['price'] as $key1 => $value1)
            {
                $reTmpDate = getdate($key1);
                $reTmpDate2 = getdate($key1+518400);
                if ($timeDiff <= 2678400)
                    $data[] = array('time' => $reTmpDate['mon'].'.'.$reTmpDate['mday'], 'price' => $value1);
                elseif ($timeDiff > 2678400 && $timeDiff <= 9072000)
                    $data[] = array('time' => $reTmpDate['mon'].'.'.$reTmpDate['mday'].'-'.$reTmpDate2['mon'].'.'.$reTmpDate2['mday'], 'price' => $value1);
                elseif ($timeDiff > 9072000)
                    $data[] = array('time' => $reTmpDate['year'].'.'.$reTmpDate['mon'], 'price' => $value1);
            }
            $returnData['class_data']['inc'][] = array('cat_name' => $value['cat_name'],'pricedata' => $data);
        }
        foreach ($tmptotal_data['class']['inc'] as $key => $value)
        {
            $price = 0;
            foreach ($value['price'] as $key1 => $value1)
            {
                $price = $price + $value1;
            }
            $returnData['Pie_chart_data']['inc'][]= array('cat_name' => $value['cat_name'] , 'price' => $price);
        }
        unset($tmptotal_data['class']['inc']);
        foreach ($tmptotal_data['all']['inc'] as $key => $value)
        {
            $reTmpDate = getdate($key);
            $reTmpDate2 = getdate($key+518400);
            if ($timeDiff <= 2678400)
                $returnData['all_data']['inc'][] = array('time'=> $reTmpDate['mon'].'.'.$reTmpDate['mday'],'price' => $value);
            elseif ($timeDiff > 2678400 && $timeDiff <= 9072000)
                $returnData['all_data']['inc'][] = array('time'=> $reTmpDate['mon'].'.'.$reTmpDate['mday'].'-'.$reTmpDate2['mon'].'.'.$reTmpDate2['mday'],'price' => $value);
            elseif ($timeDiff > 9072000)
                $returnData['all_data']['inc'][] = array('time'=> $reTmpDate['year'].'.'.$reTmpDate['mon'],'price' => $value);
        }
        unset($tmptotal_data['all']['inc']);

        return $returnData;
    }

    /**
     * 首页最近三十天统计接口
     * @return $returnData
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */

    public function financeDashboard()
    {
        //近30天时间参数
        $tmp_end = getdate(time());
        $end_time = mktime(23,59,59,$tmp_end['mon'],$tmp_end['mday'],$tmp_end['year']);
        $tm_st = getdate(time()-2678400);
        $st_time = mktime(0,0,0,$tm_st['mon'],$tm_st['mday'],$tm_st['year']);
        //支出单据查询参数
        $mapExp['admin_uid'] = getAdminUid();
        $mapExp['status'] = 84;
        $mapExp['class'] = 82;
        $mapExp['reg_time'] = array(array('egt',$st_time), array('elt', $end_time));

        //收入单据查询参数
        $mapInc['status'] = 84;
        $mapInc['class'] = 81;
        $mapInc['admin_uid'] = $mapExp['admin_uid'];
        $mapInc['reg_time'] = $mapExp['reg_time'];

        $query_data_Exp = D('Finance')->where($mapExp)->select();
        if ($query_data_Exp === false || $query_data_Exp === null)
            throw new \XYException(__METHOD__,-8000);
        $query_data_Inc = D('Finance')->where($mapInc)->select();
        if ($query_data_Inc === false || $query_data_Inc === null)
            throw new \XYException(__METHOD__,-8000);
        $tmptotal_data = array('exp' => array(),'inc' => array());

        //本月时间参数
        $mon_st_time = mktime(0,0,0,$tmp_end['mon'],1,$tmp_end['year']);
        $mon_end_time = $this->getMonthLastDay(time());
        $mapExp['reg_time'] = array(array('egt',$mon_st_time), array('elt', $mon_end_time));
        $mapInc['reg_time'] = $mapExp['reg_time'];

        $query_data_Exp_mon = D('Finance')->where($mapExp)->select();
        if ($query_data_Exp_mon === false || $query_data_Exp_mon === null)
            throw new \XYException(__METHOD__,-8000);
        $query_data_Inc_mon = D('Finance')->where($mapInc)->select();
        $query_data_Inc_mon = D('Finance')->where($mapInc)->select();
        if ($query_data_Inc_mon === false || $query_data_Inc_mon === null)
            throw new \XYException(__METHOD__,-8000);

        //生成空数据
        $time_interval = 86400;
        for ($i = $st_time;$i <= $end_time;$i = $i+$time_interval)
        {
            $tmptotal_data['exp'][$i] = 0;
            $tmptotal_data['inc'][$i] = 0;
        }
        //支出数据归类
        foreach ($query_data_Exp as $key => $value)
        {
            $tmpDate = null;
            $tmpDate = getdate($value['reg_time']);
            $query_data_Exp[$key]['reg_time'] = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
        }
        foreach ($query_data_Exp as $key => $value)
        {
            $time  = $query_data_Exp[$key]['reg_time'];
            $tmptotal_data['exp'][$time] = xyadd($tmptotal_data['exp'][$time],$value['income']);
        }
        //收入数据返回封装
        $returnData = array(
            'exp' => array(),
            'inc' => array(),
            'mon' => array(
                'exp' => 0,
                'inc' => 0,
                'money_pit' => 0
            )
        );
        foreach ($tmptotal_data['exp'] as $key => $value)
        {
            $expdate = getdate($key);
            $returnData['exp'][] = array('time' => $expdate['mon'].'.'.$expdate['mday'] ,'price' => $value);
        }
        unset($query_data_Exp);
        unset($tmptotal_data['exp']);
        //收入数据归类
        foreach ($query_data_Inc as $key => $value)
        {
            $tmpDate = null;
            $tmpDate = getdate($value['reg_time']);
            $query_data_Inc[$key]['reg_time'] = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
        }
        foreach ($query_data_Inc as $key => $value)
        {
            $time  = $query_data_Inc[$key]['reg_time'];
            $tmptotal_data['inc'][$time] = xyadd($tmptotal_data['exp'][$time],$value['income']);
        }
        //支出数据返回封装
        foreach ($tmptotal_data['inc'] as $key => $value)
        {
            $incdate = getdate($key);
            $returnData['inc'][] = array('time' => $incdate['mon'].'.'.$incdate['mday'] ,'price' => $value);
        }
        unset($query_data_Inc);
        unset($tmptotal_data['inc']);
        //本月总收入支出返回数据
        foreach ($query_data_Exp_mon as $key => $value)
        {
            $returnData['mon']['exp'] = xyadd($returnData['mon']['exp'],$value['income']);
        }
        foreach ($query_data_Inc_mon as $key => $value)
        {
            $returnData['mon']['inc'] = xyadd($returnData['mon']['inc'],$value['income']);
        }
        $money_pit = $this->invoicePoolSummary();
        $returnData['mon']['money_pit'] = $money_pit['money_pit'];
        $returnData['mon']['invoice'] = $money_pit['invoice'];
        return $returnData;

    }

    /**编辑订单
     * @param $fid
     * @param $boss_advice
     * @param $finance_advice
     * @param null $data
     * @author DizzyLee<728394036@qq.com>
     */
    public function edit_FinanceOrder($data = null)
    {
        $boss_advice = $data['boss_advice'];
        $finance_advice = $data['finance_advice'];
        if (!$this->field('fid,remark')->create($data,self::FinanceModel_edit_FinanceOrder))
            throw new \XYException(__METHOD__,$this->getError());
        $queryData = M('Finance')->where(array('fid' => $this->fid))->find();
        $rpg = getRpg();
        if ($queryData === false || $queryData === null)
            throw new \XYException(__METHOD__,-8000);
        if (is_array($queryData) && $queryData['status']!= 84 && $queryData['status']!= 85)
        {
            $cart = unserialize($queryData['cart']);
            if (($rpg == 9 || $rpg = 1 || $rpg = 5) && isset($data['boss_advice']))
                $cart['boss_advice'] = $boss_advice;
            if ($rpg == 10&&isset($data['finance_advice']))
                $cart['finance_advice'] = $finance_advice;
            $this->cart = serialize($cart);
            $operate = $this->save();
            if ($operate === false || $operate === null)
                throw new \XYException(__METHOD__,-8000);
            if ($operate>=0)
                return 1;
        }
        else
            throw new \XYException(__METHOD__,-3608);

    }




        /**
	 * 删除单个草稿单据
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $fid 要删除的那个单据的主键
	 *
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteDraft(array $data = null)
	{
		if (!$this->field('fid')->create($data,self::MODEL_DELETE_DRAFT))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('fid'=>$this->fid,'admin_uid'=>session('user_auth.admin_uid')))->delete();
		
		if (empty($tmp))
			throw new \XYException(__METHOD__,-3501);

		return 1;
	}

}