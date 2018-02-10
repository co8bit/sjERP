<?php
namespace Home\Model;
use Think\Model;

/**
 * 权限AuthGroup类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class AuthGroupModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//AuthGroupModel_create_
		array('title', '1,200', -13003,
			self::MUST_VALIDATE, 'length',self::AuthGroupModel_create_), //title不合法
		array('status', 'checkDeny_bool_status',-13004,
			self::MUST_VALIDATE, 'callback',self::AuthGroupModel_create_), //status不合法
		array('rules', 'check_AuthGroupModel_rules',-13005,
			self::MUST_VALIDATE, 'callback',self::AuthGroupModel_create_), //rules不合法
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
	 * @param string $title 中文名称
	 * @param enum $status 0|1:1为正常，0为禁用
	 * @param array $rules 用户组拥有的规则id
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
    	if (!$this->field('title,status,rules')->create($data,self::AuthGroupModel_create_))
			throw new \XYException(__METHOD__,$this->getError());

		$formatRules = '';
		foreach ($this->rules as$v)
		{
			$formatRules .= $v . ',';
		}
		$formatRules = rtrim($formatRules,',');

		$this->rules = null;
		$this->rules = $formatRules;

		$tmpReturn = $this->add();

		if ($tmpReturn > 0)
			return 1;
		else
			throw new \XYException(__METHOD__,-13000);
    }

}