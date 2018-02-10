<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 仓库控制器：创建、修改仓库等.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class StorageController extends HomeController
{
	/**
	 * 创建/修改仓库
	 * @see \Home\Model\StorageModel::createSto()
	 */
	public function updateSto()
	{
		try
		{
			$this->jsonReturn(D('Storage')->updateSto(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * 删除仓库
	 * @see \Home\Model\StorageModel::deleteSto()
	 */
	public function deleteSto()
	{
		try
		{
			$this->jsonReturn(D('Storage')->deleteSto(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * 查询仓库
	 * @see \Home\Model\StorageModel::querySto()
	 */
	public function querySto()
	{
		try
		{
			$this->jsonReturn(D('Storage')->querySto(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * 查询一个仓库
	 * @see \Home\Model\StorageModel::get_()
	 */
	public function get_()
	{
		try
		{
			$this->jsonReturn(D('Storage')->get_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->get_());
		}
	}


}