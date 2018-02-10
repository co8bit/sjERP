<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 反馈类控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class FeedbackController extends HomeController
{
	/**
	 * @see \Home\Model\FeedbackModel::create_()
	 */
	public function create_()
	{
		try
		{
			$this->jsonReturn(D("Feedback")->create_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
}