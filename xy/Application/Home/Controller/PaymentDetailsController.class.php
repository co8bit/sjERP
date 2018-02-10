<?php
namespace Home\Controller;
use Think\Controller;

/**
 * PaymentDetails类.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class PaymentDetailsController extends HomeController
{

	/**
	 * @see \Home\Model\PaymentDetailsModel::getPaybillAndSmsDetail()
	 */
	public function getPaybillAndSmsDetail()
	{
		try
		{
			$this->jsonReturn(D("PaymentDetails")->getPaybillAndSmsDetail(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\PaymentDetailsModel::getMoneyTimeSave()
	 */
	public function getMoneyTimeSave()
	{
		try
		{
			$this->jsonReturn(D("PaymentDetails")->getMoneyTimeSave(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

}