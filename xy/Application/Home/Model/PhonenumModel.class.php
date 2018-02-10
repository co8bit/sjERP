<?php
namespace Home\Model;
use Think\Model;

/**
 * 电话号码类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码（参考Company类）：{@see \ErrorCode\ErrorCode()}
 */
class PhonenumModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('cid', 'checkDeny_cid', -6001, self::MUST_VALIDATE, 'callback',self::MODEL_INSERT),//cid不合法
		array('cid', 'checkDeny_cid', -6001, self::EXISTS_VALIDATE, 'callback',self::MODEL_UPDATE),//cid不合法
		array('cid', 'checkDeny_cid', -6001, self::EXISTS_VALIDATE, 'callback',self::MODEL_QUERY),//cid不合法
		array('contact_id', 'checkDeny_contact_id', -6011, self::EXISTS_VALIDATE, 'callback',self::MODEL_UPDATE),//contact_id不合法
		array('phonenum_id', 'checkDeny_phonenum_id', -6015, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//phonenum_id不合法
		array('mobile', '0,25', -6004, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //mobile长度不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
	);



	/**
	 * 创建电话号.
	 * 
	 * 最多可以创建：DEFAULT_INIT_MAX_LIMIT_PHONENUM条
	 * note: 如果用户输入的电话已经存在，那么直接返回成功，即1
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $cid
	 * @param unsigned_int $contact_id
	 * @param string $mobile 电话号
	 * 
	 * @return unsigned_int 成功-phonenum_id > 0
	 * @throws \XYException
	 */
	public function createPhonenum(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if(!$this->field('cid,contact_id,mobile')->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());


			//检查联系人电话数是否达到上限及查重
			$tmpData = M('Phonenum')->where(array('contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->field('mobile')->lock(true)->select();
			foreach ($tmpData as $value)
			{
				if ($value['mobile'] == $this->mobile)
				{
					if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}
			$count = M('Phonenum')->where(array('contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->lock(true)->count();
			if ($count >= C('DEFAULT_INIT_MAX_LIMIT_PHONENUM'))
				throw new \XYException(__METHOD__,-6018);		

			$phonenum_id = $this->add();

			if ($phonenum_id > 0)
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $phonenum_id;
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
	 * 编辑联系人电话.
	 * 
	 * note:如果mobile为空，那么数据库里的mobile会被改为空
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $contact_id
	 * @param unsigned_int $phonenum_id 要修改的电话号的id
	 * @param string $mobile 电话号
	 * 
	 * @return '1' 成功
	 * @throws \XYException
	 */
	public function editPhonenum(array $data = null)
	{
		if(!$this->field('phonenum_id,contact_id,mobile')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(
			array('phonenum_id'=>$this->phonenum_id,'contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->save();
		if ( ($tmp === null) || ($tmp === false) )
			throw new \XYException(__METHOD__,-6000);

		return 1;
	}



	/**
	 * 删除联系人电话
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $phonenum_id
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deletePhonenum(array $data = null)
	{
		if(!$this->field('phonenum_id')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('phonenum_id'=>$this->phonenum_id,'admin_uid'=>$this->admin_uid))->delete();//TODO:目前这里是靠数据库的外键删除掉phonenum表和carlicense表里的相关东西的
		if ( empty($tmp) )
			throw new \XYException(__METHOD__,-6000);

		return 1;
	}



	/**
	 * 查询某个cid下的所有电话列表
	 * @api
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
			->field('contact_id,phonenum_id,mobile')
			->select();

		if ($reData === false)
			throw new \XYException(__METHOD__,-6000);
		elseif ($reData === null)
			throw new \XYException(__METHOD__,-6001);

		return $reData;
	}

}
