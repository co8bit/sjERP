<?php
namespace Home\Model;
use Think\Model;


/**
 * 车牌号Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码（参考Company类）：{@see \ErrorCode\ErrorCode()}
 */
class CarlicenseModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('cid', 'checkDeny_cid', -6001, self::MUST_VALIDATE, 'callback',self::MODEL_INSERT),//cid不合法
		array('cid', 'checkDeny_cid', -6001, self::EXISTS_VALIDATE, 'callback',self::MODEL_UPDATE),//cid不合法
		array('cid', 'checkDeny_cid', -6001, self::EXISTS_VALIDATE, 'callback',self::MODEL_QUERY),//cid不合法
		array('contact_id', 'checkDeny_contact_id', -6011, self::EXISTS_VALIDATE, 'callback',self::MODEL_UPDATE),//contact_id不合法
		array('carlicense_id', 'checkDeny_carlicense_id', -6016, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//carlicense_id不合法
		array('car_license', '0,30', -6008, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //car_license长度不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
	);



	/**
	 * 创建车牌号
	 * 最多可以创建：DEFAULT_INIT_MAX_LIMIT_CARLICENSE条
	 * NOTE: 如果用户输入的车牌号已经存在，那么直接返回成功，即1
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $cid
	 * @param unsigned_int $contact_id
	 * @param string $car_license 车牌号
	 * 
	 * @return unsigned_int 成功-carlicense_id > 0
	 * @throws \XYException
	 */
	public function createCarlicense(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if(!$this->field('cid,contact_id,car_license')->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());

			//检查联系人电话数是否达到上限及查重
			$tmpData = M('Carlicense')->where(array('contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->field('car_license')->lock(true)->select();
			foreach ($tmpData as $value)
			{
				if ($value['car_license'] == $this->car_license)
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}
			$count = M('Carlicense')->where(array('contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->lock(true)->count();
			if ($count >= C('DEFAULT_INIT_MAX_LIMIT_CARLICENSE'))
				throw new \XYException(__METHOD__,-6019);
			

			$carlicense_id = $this->add();

			if ($carlicense_id > 0)
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $carlicense_id;
			}
			else
				throw new \XYException(__METHOD__,-6000);
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
	 * 编辑联系人车牌号.
	 * 
	 * NOTE:如果car_license为空，那么数据库里的car_license会被改为空
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $contact_id
	 * @param unsigned_int $carlicense_id 要修改的车牌号的id
	 * @param string $car_license 车牌号
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function editCarlicense(array $data = null)
	{
		if(!$this->field('carlicense_id,contact_id,car_license')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(
			array('carlicense_id'=>$this->carlicense_id,'contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->save();
		if ( ($tmp === null) || ($tmp === false) )
			throw new \XYException(__METHOD__,-6000);

		return 1;
	}



	/**
	 * 删除联系人车牌号
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $carlicense_id
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteCarlicense(array $data = null)
	{
		if(!$this->field('carlicense_id')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('carlicense_id'=>$this->carlicense_id,'admin_uid'=>$this->admin_uid))->delete();//TODO:目前这里是靠数据库的外键删除掉phonenum表和carlicense表里的相关东西的
		if ( empty($tmp) )
			throw new \XYException(__METHOD__,-6000);

		return 1;
	}



	/**
	 * 查询某个cid下的所有车牌号列表
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $cid
	 *
	 * @return array 数据库中的一行，按contact_id递增。json格式为{"data":[{数据库中的一行}{..}]}
	 *         具体参考Contact类的queryList方法。
	 * @throws \XYException
	 */
	public function queryListInCid(array $data = null)
	{
		if (!$this->field('cid')->create($data,self::MODEL_QUERY))
			throw new \XYException(__METHOD__,$this->getError());
			
		$reData = $this
			->where(array('cid'=>$this->cid,'admin_uid'=>session('user_auth.admin_uid')))
			->order('contact_id')
			->field('contact_id,carlicense_id,car_license')
			->select();

		if ($reData === false)
			throw new \XYException(__METHOD__,-6000);

		return $reData;
	}

}
