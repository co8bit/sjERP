<?php
namespace Home\Model;
use Think\Model;

/**
 * 权限AuthRule类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class AuthRuleModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//AuthRuleModel_create_
		array('name', '1,200', -13006,
			self::MUST_VALIDATE, 'length',self::AuthRuleModel_create_), //name不合法
		array('title', '1,200', -13007,
			self::MUST_VALIDATE, 'length',self::AuthRuleModel_create_), //title不合法
		array('status', 'checkDeny_bool_status',-13008,
			self::MUST_VALIDATE, 'callback',self::AuthRuleModel_create_), //status不合法
		array('condition', '0,200', -13009,
			self::MUST_VALIDATE, 'length',self::AuthRuleModel_create_), //condition不合法
	);



	/* 自动完成 */
	protected $_auto = array(
	);



	/**
	 * 创建用户组.
	 * 没有权限检测
	 * @internal server
	 * @param mixed|null $data POST的数据
	 *
	 * @param string $name 规则
	 * @param string $title 中文名称
	 * @param enum $status 0|1:1为正常，0为禁用
	 * @param string $condition 规则表达式，为空表示存在就验证，不为空表示按照条件验证，即规则附件条件,满足附加条件的规则,才认为是有效的规则
	 *
	 * @return 1 ok
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 0.15
	 * @date    2016-09-16
	 */
    public function create_(array $data = null)
    {
    	if (!$this->field('name,title,status,condition')->create($data,self::AuthRuleModel_create_))
			throw new \XYException(__METHOD__,$this->getError());

		$this->type = 1;

		$tmpReturn = $this->add();

		if ($tmpReturn > 0)
			return 1;
		else
			throw new \XYException(__METHOD__,-13000);
    }

}