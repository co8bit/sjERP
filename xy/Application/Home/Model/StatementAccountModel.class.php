<?php
namespace Home\Model;
use Think\Model;

/**
 * 往来单位对账单Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误编码:{@see \ErrorCode\ErrorCode()}
 */
class StatementAccountModel extends BaseadvModel
{
	protected $_validate = array(
		//StatementAccountModel_show_statement
		array('s_pwd','4',-22001,self::MUST_VALIDATE,'length',self::StatementAccountModel_show_statement),//s_pwd不合法
		);

	/**
	 * 显示账单
	 * @api
	 * 
	 * @param mixed $data post来的数据
	 * @param string $pwd 客户输入的查询密码
	 * @return
	 */
	public function show_statement(array $data = null)
	{

		if(!$this->field('s_guid,s_pwd')->create($data,self::StatementAccountModel_show_statement))
			throw new \XYException(__METHOD__,$this->getError());
		$dbState  = new StatementAccountModel();
		$queryData = $dbState->where(array('s_guid'=>$this->s_guid,'s_pwd'=>$this->s_pwd))->field('statementofaccount')->find();
		if(empty($queryData))
			throw new \XYException(__METHOD__,-22001);
		$reData = $queryData['statementofaccount'];
		$reData = unserialize($reData);
		foreach($reData as $k=>$v)
		{
			$reData[$k]['cart'] = unserialize($reData[$k]['cart']);
		}
		return $reData;
	}
}