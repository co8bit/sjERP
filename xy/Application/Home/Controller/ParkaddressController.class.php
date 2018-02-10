<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 停车位置控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class ParkaddressController extends HomeController
{
	/**
	 * @see \Home\Model\ParkaddressModel::create_()
	 */
	public function create_()
	{
		try
		{
			$this->jsonReturn(D("Parkaddress")->create_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ParkaddressModel::edit_()
	 */
	public function edit_()
	{
		try
		{
			$this->jsonReturn(D("Parkaddress")->edit_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ParkaddressModel::delete_()
	 */
	public function delete_()
	{
		try
		{
			$this->jsonReturn(D("Parkaddress")->delete_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\ParkaddressModel::queryList()
	 */
	public function queryList()
	{
		try
		{
			$this->jsonReturn(D("Parkaddress")->queryList(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

}