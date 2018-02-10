<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 电话号码管理控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码（参考Company类）：{@see \ErrorCode\ErrorCode()}
 */
class PhonenumController extends HomeController
{
	/**
	 * @see \Home\Model\PhonenumModel::createPhonenum()
	 */
	public function createPhonenum()
	{
		try
		{
			$this->jsonReturn(D("Phonenum")->createPhonenum(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\PhonenumModel::editPhonenum()
	 */
	public function editPhonenum()
	{
		try
		{
			$this->jsonReturn(D("Phonenum")->editPhonenum(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\PhonenumModel::deletePhonenum()
	 */
	public function deletePhonenum()
	{
		try
		{
			$this->jsonReturn(D("Phonenum")->deletePhonenum(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



}