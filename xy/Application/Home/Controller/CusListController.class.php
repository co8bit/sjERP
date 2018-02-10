<?php
namespace Home\Controller;
use Think\Controller;

/**
 * FaceRec类.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class CusListController extends HomeController
{
	/**
	 * @see \Home\Model\CusListModel::getCusList()
	 */
	public function getCusList()
	{
		try
		{
			$this->jsonReturn(D("CusList")->getCusList(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\CusListModel::createRecord()
	 */
	public function createRecord()
	{
		try
		{
			$this->jsonReturn(D("CusList")->createRecord(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


}