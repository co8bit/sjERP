<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 查询控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class EverydaySummarySheetController extends HomeController
{
	/**
	 * @see \Home\Model\EverydaySummarySheetModel::summarySheet()
	 */
	public function summarySheet()
	{
		try
		{
			$this->jsonReturn(D("EverydaySummarySheet")->summarySheet(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\EverydaySummarySheetModel::queryList()
	 */
	public function queryList()
	{
		try
		{
			$this->jsonReturn(D("EverydaySummarySheet")->queryList(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
}