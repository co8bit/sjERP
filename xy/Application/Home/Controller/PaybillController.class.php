<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 向我们付钱时的类控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class PaybillController extends HomeController
{
	/**
	 * @see \Home\Model\PaybillModel::getPayBillParam()
	 */
	public function getPayBillParam()
	{
		try
		{
			$this->jsonReturn(D("Paybill")->getPayBillParam(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
}