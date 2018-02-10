<?php
namespace Home\Model;
use Think\Model;

/**
 * 权限AuthGroupAccess类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class AuthGroupAccessModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//AuthGroupAccessModel_setUserGroup
		array('uid', 'isUnsignedInt', -13001,
			self::MUST_VALIDATE, 'function',self::AuthGroupAccessModel_setUserGroup),//uid不合法
		array('group_id', 'isUnsignedInt', -13002,
			self::MUST_VALIDATE, 'function',self::AuthGroupAccessModel_setUserGroup),//group_id不合法
	);



	/* 自动完成 */
	protected $_auto = array(
	);



	/**
	 * 设置用户所属用户组.如果表里没有会新建，如果uid-group_id已存在，则会更新.
	 * 没有权限检测
	 * @internal server
	 * @param mixed|null $data POST的数据
	 *
	 * @param unsigned_int $uid 要设置的用户uid
	 * @param unsigned_int $group_id 组id
	 *
	 * @return 1 ok
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 0.15
	 * @date    2016-09-16
	 */
    public function setUserGroup(array $data = null)
    {
    	if (!$this->field('uid,group_id')->create($data,self::AuthGroupAccessModel_setUserGroup))
			throw new \XYException(__METHOD__,$this->getError());

		//超级权限只有uid=1才可以
		if ( ($this->group_id == 5) && ($this->uid !== 1) && ($this->group_id == 8))
			throw new \XYException(__METHOD__,-13002);
		if ( $this->group_id > 15 )
			throw new \XYException(__METHOD__,-13002);

		$tmpInfo = M('AuthGroupAccess')->where(array('uid'=>$this->uid))->find();
		if (empty($tmpInfo))
		{
			$tmpReturn = $this->add(array('uid'=>$this->uid,'group_id'=>$this->group_id));
		}
		elseif ($tmpInfo['group_id'] == $this->group_id)
			return 1;
		else
		{
			$tmpReturn = $this->where(array('uid'=>$this->uid))->save(array('group_id'=>$this->group_id));
		}

		if (empty($tmpReturn))
			throw new \XYException(__METHOD__,-13000);

		return 1;
    }

}