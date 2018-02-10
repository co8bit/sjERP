<?php
namespace Home\Controller;
use Think\Controller;

/**
 * SmsDetails类.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class SmsDetailsController extends HomeController
{
	/**
	 * @see \Home\Model\SmsDetailsModel::sendSMSStatementOfAccount()
	 */
	public function sendSMSStatementOfAccount()
	{
		try
		{
			$this->jsonReturn(D("SmsDetails")->sendSMSStatementOfAccount(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


}