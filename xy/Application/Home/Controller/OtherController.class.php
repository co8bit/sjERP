<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 其他控制器.要求权限控制。
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class OtherController extends HomeController
{
	/**
	 * @see \Home\Model\OtherModel::loadExcelFrom_ForCompany()
	 */
	public function loadExcelFrom_ForCompany()
	{
		try
		{
			$this->jsonReturn(D("Other")->loadExcelFrom_ForCompany(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\OtherModel::loadExcelFrom_ForSku()
	 */
	public function loadExcelFrom_ForSku()
	{
		try
		{
			$this->jsonReturn(D("Other")->loadExcelFrom_ForSku(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

}