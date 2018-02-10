<?php
namespace Home\Model;
use Think\Model;

/**
 * 用户配置信息的Model
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class ConfigModel extends BaseadvModel
{

	/* 自动验证 */
	protected $_validate = array(
		//ConfigModel_setShopConfig
		array('ck_jump_warehouseConfirm', 'checkDeny_bool_status', -10508, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//ck_jump_warehouseConfirm不合法
		array('ck_jump_warehouOut', 'checkDeny_bool_status', -10509, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//ck_jump_warehouOut不合法
		array('ck_jump_deliver', 'checkDeny_bool_status', -10510, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//ck_jump_deliver不合法
		array('rk_jump_warehouseConfirm', 'checkDeny_bool_status', -10511, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//rk_jump_warehouseConfirm不合法
		array('rk_jump_deliver', 'checkDeny_bool_status', -10512, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//rk_jump_deliver不合法
		array('order_flow_mode', 'checkDeny_bool_status', -10513, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//order_flow_mode不合法
		array('audit_flow_mode', 'checkDeny_bool_status', -10514, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//audit_flow_mode不合法
		array('is_show_foreground_printer_button', 'checkDeny_bool_status', -10516, 
			self::MUST_VALIDATE, 'callback',self::ConfigModel_setShopConfig),//is_show_foreground_printer_button不合法

	);


	/**
	 * 得到用户的配置信息
	 * @internal server
	 *
	 * @internal server
	 * @return array 用户配置信息，数据库中的一行
	 * @throws \XYException
	 */
	public function getShopSysConfig()
	{
		$tmp = $this->where(array('admin_uid'=>getAdminUid()))->find();

		if (empty($tmp))
			throw new \XYException(__METHOD__,-10501);

		unset($tmp['admin_uid']);
		return $tmp;
	}



	/**
	 * 得到sn config信息.
	 *
	 * 读取时会加锁，所以必须外部要打开事务才可以调用。
	 *
	 * @internal server
	 * @return array 用户sn信息，数据库中的一行
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 */
	public function getSnConfig()
	{
		if(defined('RUN_PHPUNIT'))
			$tmp = $this->where(array('config_id'=>1,'admin_uid'=>getAdminUid()))->lock(true)->find();
		else
			$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->lock(true)->find();
		if (empty($tmp))
			throw new \XYException(__METHOD__,-10502);

		unset($tmp['config_id']);//config id
		unset($tmp['admin_uid']);
		return $tmp;
	}



	/**
	 * 更新sn config信息，id+1需要在外部完成.
	 *
	 * 因为getSnConfig()读取时会加锁，所以必须外部要打开事务才可以调用。
	 *
	 * @internal server
	 * @return true 成功
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 */
	public function updateSnConfig($type,$id)
	{
		if(defined('RUN_PHPUNIT'))
			$tmp = $this->where(array('config_id'=>1,'admin_uid'=>getAdminUid()))->lock(true)->save(array($type=>$id));
		else
			$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->lock(true)->save(array($type=>$id));

		if (empty($tmp))
			throw new \XYException(__METHOD__,-10504);

		return true;
	}



	/**
	 * 给店铺上锁，并返回token。
	 * 
	 * @api
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @return string token 给店铺上锁的单据下发的token
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @date    2016-10-22
	 */
	public function lockShop($isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->lock(true)->getField('lock_shop_token');
			if ( ($tmp === false) || ($tmp === null) )
				throw new \XYException(__METHOD__,-10000);
			elseif (!empty($tmp))
				throw new \XYException(__METHOD__,-10507);

			$lockShopToken = getUid().'{(mhytg{as-fg-as}'.uniqid('',true).'sdb.a0)}'.getAdminUid().'{(vcg3.zl;'.guid().rand(10000,99999);
			if($this->isVisitor()) //访客后门，只生成token回给前端，不存储，解锁时也开后门，不进行token校验
				$tmp = 1;
			else
				$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->setField('lock_shop_token',$lockShopToken);
			if (empty($tmp))
				throw new \XYException(__METHOD__,-10000);

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			return $lockShopToken;
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
	 * 解锁商铺.
	 *
	 * 只要是同一个用户就能解锁，不验证token是否正确
	 *
	 * @api
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @return 1 ok
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.1
	 * @date    2016-10-23
	 */
	public function unlockShop($isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->lock(true)->getField('lock_shop_token');
			if ( ($tmp === false) || ($tmp === null) )
				throw new \XYException(__METHOD__,-10000);
			elseif (empty($tmp))
				throw new \XYException(__METHOD__,-10506);

			$end = strpos($tmp,'{(mhytg{as-fg-as}');
			$tokenUid = intval(substr($tmp, 0,$end));

			if ( $tokenUid === getUid() )
			{
				$tmp = null;
				$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->setField('lock_shop_token','');
				if (empty($tmp))
					throw new \XYException(__METHOD__,-10000);

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return 1;
			}

			throw new \XYException(__METHOD__,-10505);
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
	 * 核对单据传上来的token是否和锁定商铺的token一致
	 *
	 * note:需要确保外界有事务
	 *
	 * @internal server
	 * @param string $lockShopToken token
	 * 
	 * @return enum 0|1 1-一致,0-不一致
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.1
	 * @date    2016-10-23
	 */
	public function lockShopTokenIsRight($lockShopToken)
	{
		$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->lock(true)->getField('lock_shop_token');
		if ( ($tmp === false) || ($tmp === null) )
			throw new \XYException(__METHOD__,-10000);
		elseif (empty($tmp))
			throw new \XYException(__METHOD__,-10506);

		$end = strpos($tmp,'{(mhytg{as-fg-as}');
		$tokenUid = intval(substr($tmp, 0,$end));

		if ( ($tmp === $lockShopToken) && ($tokenUid === getUid()) )
			return 1;
		else
			throw new \XYException(__METHOD__,-10505);
	}





	/**
	 * 查询商铺的锁状态
	 *
	 * note:
	 * 1. 服务器调用时，需要确保外界有事务
	 *
	 * @api
	 * @param bool $isServer 是否在查询的时候加锁。web端查询不加锁，服务器端查询都要加锁
	 * @return array arr['status']为1有锁，为0是无锁。如果status为1，则：arr['tokenUid'],arr['sn']有意义
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.1
	 * @date    2016-10-23
	 */
	public function getLockShopStatus($isServer = false)
	{
		if(defined('RUN_PHPUNIT')) // 为单元测试留的后门
			$tmp = $this->where(array('config_id'=>1,'admin_uid'=>getAdminUid()))->lock($isServer)->getField('lock_shop_token');
		else
			$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->lock($isServer)->getField('lock_shop_token');
		// dump($tmp);
		// echo '<br>';
		// dump(getAdminUid());
		// echo '<br>';
		// $sql=$this->_sql();
		// echo $sql;
		// echo 111111111;
		if ( ($tmp === false) || ($tmp === null) )
			throw new \XYException(__METHOD__,-10000);
		elseif ($tmp === '')
		{
			$re             = null;
			$re['status']   = 0;
			$re['tokenUid'] = 0;
			$re['sn']       = '';
		}
		else
		{
			$end = strpos($tmp,'{(mhytg{as-fg-as}');
			$tokenUid = intval(substr($tmp, 0,$end));

			$userInfo = D('User')->getUserInfo_name_sn(array('uid'=>$tokenUid));
			$re             = null;
			$re['status']   = 1;
			$re['tokenUid'] = $tokenUid;
			$re['name']     = $userInfo['name'];
			$re['sn']       = $userInfo['sn'];
		}
		return $re;
	}



	/**
	 * 设置店铺系统设置
	 *
	 * @api
	 * @param bool $order_flow_mode 单据流模式,true-开启，false-不开启
	 * @param bool $audit_flow_mode 审核模式,true-开启，false-不开启
	 * @param bool $ck_jump_warehouseConfirm 出库类单据是否跳过库管确认收到动作,true跳过，false不跳
	 * @param bool $ck_jump_warehouOut 出库类单据是否跳过出库动作,true跳过，false不跳
	 * @param bool $ck_jump_deliver 出库类单据是否跳过送达动作,true跳过，false不跳
	 * @param bool $rk_jump_warehouseConfirm 入库类单据是否跳过库管确认收到动作,true跳过，false不跳
	 * @param bool $rk_jump_deliver 入库类单据是否跳过入库动作,true跳过，false不跳
	 * @param bool $is_show_foreground_printer_button 是否显示前台打印按钮,true-显示，false-不显示
     * @param bool $finance_mode 设置财务与Boss是否为同一人。
	 *
	 * @return 1 ok
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.1
	 * @date    2016-10-28
        */
        public function setShopConfig(array $data = null)
    {
        $data['order_flow_mode']                   = intval($data['order_flow_mode']);
        $data['audit_flow_mode']                   = intval($data['audit_flow_mode']);
        $data['ck_jump_warehouseConfirm']          = intval($data['ck_jump_warehouseConfirm']);
        $data['ck_jump_warehouOut']                = intval($data['ck_jump_warehouOut']);
        $data['ck_jump_deliver']                   = intval($data['ck_jump_deliver']);
        $data['rk_jump_warehouseConfirm']          = intval($data['rk_jump_warehouseConfirm']);
        $data['rk_jump_deliver']                   = intval($data['rk_jump_deliver']);
        $data['is_show_foreground_printer_button'] = intval($data['is_show_foreground_printer_button']);
        $data['finance_mode'] = intval($data['finance_mode']);

        if (!$this->field('order_flow_mode,audit_flow_mode,ck_jump_warehouseConfirm,ck_jump_warehouOut,ck_jump_deliver,finance_mode,rk_jump_warehouseConfirm,rk_jump_deliver,is_show_foreground_printer_button')->create($data,self::ConfigModel_setShopConfig))
            throw new \XYException(__METHOD__,$this->getError());

        $tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->save();
        if ( ($tmp === false) || ($tmp === null) )//$tmp == 0也通过
            throw new \XYException(__METHOD__,-10000);

		return 1;
	}



	/**
	 * 得到店铺的系统设置
	 * @api
	 * 
	 * @return array 参考setShopConfig的param
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.1
	 * @date    2016-10-28
	 */
	public function getShopConfig()
	{
		$tmp = $this->where(array('config_id'=>C('config_id'),'admin_uid'=>getAdminUid()))->field('order_flow_mode,audit_flow_mode,ck_jump_warehouseConfirm,ck_jump_warehouOut,ck_jump_deliver,rk_jump_warehouseConfirm,rk_jump_deliver,is_show_foreground_printer_button,finance_mode')->find();
		if (empty($tmp))
			throw new \XYException(__METHOD__,-10000);

		return $tmp;
	}



	/**
	 * 设置用户的会员类别
	 * @internal server
	 * @param unsigned_int $member_class 新的会员类别
	 *
	 * @return 1
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.2
	 * @date    2016-11-08
	 */
	public function setUserMember($admin_uid,$member_class)
	{
		$memberClass_employee = C('MemberClass_EmployeeNum');//会员类别与用户数对照表

		$updateData                       = null;
		$updateData['MAX_LIMIT_EMPLOYEE'] = $memberClass_employee[$member_class];
		$tmp = $this->where(array('admin_uid'=>$admin_uid))->save($updateData);

		if ($tmp === false)
			throw new \XYException(__METHOD__,-10000);
		elseif (empty($tmp))
			throw new \XYException(__METHOD__,-10515);

		return 1;
	}
}