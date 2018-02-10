<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 车牌号控制器
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码（参考Company类）：{@see \ErrorCode\ErrorCode()}
 */
class CarlicenseController extends HomeController
{
	/**
	 * @see \Home\Model\CarlicenseModel::createCarlicense()
	 */
	public function createCarlicense()
	{
		try
		{
			$this->jsonReturn(D("Carlicense")->createCarlicense(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\CarlicenseModel::editCarlicense()
	 */
	public function editCarlicense()
	{
		try
		{
			$this->jsonReturn(D("Carlicense")->editCarlicense(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\CarlicenseModel::deleteCarlicense()
	 */
	public function deleteCarlicense()
	{
		try
		{
			$this->jsonReturn(D("Carlicense")->deleteCarlicense(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


}