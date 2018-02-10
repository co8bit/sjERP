<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 星云进销存的用户账户系统的类控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UserAccountController extends HomeController
{
	/**
	 * @see \Home\Model\UserAccountModel::get_()
	 */
	public function get_()
	{
		try
		{
			$this->jsonReturn(D("UserAccount")->get_());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
}