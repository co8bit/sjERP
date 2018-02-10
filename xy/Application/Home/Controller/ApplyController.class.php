<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 官网的申请表控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class ApplyController extends BaseController
{
	/**
	 * @internal
	 * @author co8bit <me@co8bit.com>
	 * @version 0.3
	 * @date    2016-06-14
	 */
	public function index()
	{
		$this->_empty();
	}


	
	/**
	 * @see \Home\Model\ApplyModel::create_()
	 */
	public function create_()
	{
		try
		{
			$this->jsonReturn(D("Apply")->create_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ApplyModel::done()
	 */
	public function done()
	{
		try
		{
			$this->jsonReturn(D("Apply")->done(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
}