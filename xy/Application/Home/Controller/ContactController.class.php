<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 联系人控制器
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码（参考Company类）：{@see \ErrorCode\ErrorCode()}
 */
class ContactController extends HomeController
{
	/**
	 * @see \Home\Model\ContactModel::createContact()
	 */
	public function createContact()
	{
		try
		{
			$this->jsonReturn(D("Contact")->createContact(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ContactModel::edit_()
	 */
	public function edit_()
	{
		try
		{
			$this->jsonReturn(D("Contact")->edit_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ContactModel::deleteContact()
	 */
	public function deleteContact()
	{
		try
		{
			$this->jsonReturn(D("Contact")->deleteContact(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\ContactModel::queryList()
	 */
	public function queryList()
	{
		try
		{
			$this->jsonReturn(D('Contact')->queryList(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

}