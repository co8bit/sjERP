<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 打印模板存取控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class PrintTemplateController extends HomeController
{
	/**
	 * @see \Home\Model\PrintTemplateModel::create_()
	 */
	public function create_()
	{
		try
		{
			$this->jsonReturn(D("PrintTemplate")->create_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\PrintTemplateModel::get_()
	 */
	public function get_()
	{
		try
		{
			$this->jsonReturn(D('PrintTemplate')->get_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}




	/**
	 * @see \Home\Model\PrintTemplateModel::WBXcreate_()
	 */
	public function WBXcreate_()
	{
		try
		{
			$this->jsonReturn(D("PrintTemplate")->WBXcreate_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\PrintTemplateModel::WBXget_()
	 */
	public function WBXget_()
	{
		try
		{
			$this->jsonReturn(D('PrintTemplate')->WBXget_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

}