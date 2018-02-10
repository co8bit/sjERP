<?php
namespace Home\Controller;
use Think\Controller;

/**
 * FaceRec类.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class FaceRecController extends HomeController
{
	/**
	 * @see \Home\Model\FaceRecModel::upload_pic()
	 */
	public function upload_pic()
	{
		try
		{
			$this->jsonReturn(D("FaceRec")->upload_pic(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\FaceRecModel::getUserId()
	 */
	public function comparePhoto()
	{
		try
		{
			$this->jsonReturn(D("FaceRec")->comparePhoto(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



}