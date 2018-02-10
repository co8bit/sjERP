<?php
namespace Home\Model;
use Think\Model;


/**
 * 停车地址类Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class ParkaddressModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('parkaddress_id', 'checkDeny_parkaddress_id', -7001, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//parkaddress_id不合法
		array('parkaddress_id', 'checkDeny_parkaddress_id', -7001, self::EXISTS_VALIDATE, 'callback',self::MODEL_QUERY),//parkaddress_id不合法
		array('park_address', '1,500', -7002, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//park_address长度不合法
		array('park_address', 'check_not_all_blank', -7004, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),//park_address长度不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
	);



	/**
	 * 创建送货地址.
	 * 
	 * 最多可以创建：DEFAULT_INIT_MAX_LIMIT_PARKADDRESS条
	 * NOTE: 如果用户输入的车牌号已经存在，那么直接返回成功，即1
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param string $park_address 送货地址
	 * 
	 * @return unsigned_int 成功-parkaddress_id > 0
	 * @throws \XYException
	 */
	public function create_(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if(!$this->field('park_address')->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());

			//检查送货地址数是否达到上限及查重
			$tmpData = M('Parkaddress')->where(array('admin_uid'=>$this->admin_uid))->field('park_address')->lock(true)->select();
			foreach ($tmpData as $value)
			{
				if ($value['park_address'] == $this->park_address)
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}
			$count = M('Parkaddress')->where(array('admin_uid'=>$this->admin_uid))->lock(true)->count();
			if ($count >= C('DEFAULT_INIT_MAX_LIMIT_PARKADDRESS'))
				throw new \XYException(__METHOD__,-7003);

			$this->sn = $this->getNextSn('PAD');
			$parkaddress_id = $this->add();

			if ($parkaddress_id > 0)
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $parkaddress_id;
			}
			else
				throw new \XYException(__METHOD__,-7000);
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
	 * 编辑送货地址.
	 * 
	 * NOTE:如果park_address为空，那么数据库里的park_address会被改为空
	 * 
	 * @api
	 * @param unsigned_int $parkaddress_id 要修改的送货地址的id
	 * @param string $park_address 送货地址
	 * 
	 * @return '1' 成功
	 * @throws \XYException
	 */
	public function edit_(array $data = null)
	{
		if(!$this->field('parkaddress_id,park_address')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('parkaddress_id'=>$this->parkaddress_id,'admin_uid'=>$this->admin_uid))->save();
		if ( ($tmp === null) || ($tmp === false) )
			throw new \XYException(__METHOD__,-7000);

		return 1;
	}



	/**
	 * 删除送货地址.
	 * 
	 * @api
	 * @param unsigned_int $parkaddress_id 要删除的送货地址的id
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function delete_(array $data = null)
	{
		if(!$this->field('parkaddress_id')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('parkaddress_id'=>$this->parkaddress_id,'admin_uid'=>$this->admin_uid))->delete();
		if ( empty($tmp) )
			throw new \XYException(__METHOD__,-7000);
		else
			return 1;
	}



	/**
	 * 查询某个用户下的所有送货地址列表.
	 * 
	 * @api
	 * @return array 数据库中的一行，按parkaddress_id递增。json格式为{"data":[{"parkaddress_id":1,"park_address":"balabala",...}{..}]}
	 * @throws \XYException
	 */
	public function queryList()
	{
		$reData = $this
			->where(array('admin_uid'=>session('user_auth.admin_uid')))
			->order('parkaddress_id')
			->field('parkaddress_id,park_address,sn')
			->select();

		if ($reData === false)
			throw new \XYException(__METHOD__,-7000);

		return $reData;
	}

}
