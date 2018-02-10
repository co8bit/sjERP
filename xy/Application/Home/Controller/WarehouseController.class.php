<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 库存方面的控制器：报损报溢等.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class WarehouseController extends HomeController
{
	/**
	 * @see \Home\Model\WarehouseModel::createStockTakingOrRequisition()
	 */
	public function createStockTakingOrRequisition()
	{
		try
		{
			$this->jsonReturn(D("Warehouse")->createStockTakingOrRequisition(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}	


	/**
	 * @see \Home\Model\WarehouseModel::createStockTakingOrRequisitionDraft()
	 */
	public function createStockTakingOrRequisitionDraft()
	{
		try
		{
			$this->jsonReturn(D("Warehouse")->createStockTakingOrRequisitionDraft(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
	


	/**
	 * @see \Home\Model\WarehouseModel::get_()
	 */
	public function get_()
	{
		try
		{
			$this->jsonReturn(D("Warehouse")->get_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\WarehouseModel::edit_()
	 */
	public function edit_()
	{
		try
		{
			$this->jsonReturn(D("Warehouse")->edit_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\WarehouseModel::deleteDraft()
	 */
	public function deleteDraft()
	{
		try
		{
			$this->jsonReturn(D('Warehouse')->deleteDraft(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}







	/**
	 * @see \Home\Model\WarehouseModel::createOverflowOrLoss()
	 * @deprecated v0.2.70
	 */
	public function createOverflowOrLoss()
	{
		$this->_empty();
	}



	/**
	 * @see \Home\Model\WarehouseModel::editOverflowOrLoss()
	 * @deprecated
	 */
	public function editOverflowOrLoss()
	{
		$this->_empty();
	// 	try
	// 	{
	// 		$this->jsonReturn(D("Warehouse")->editOverflowOrLoss(I('param.')));
	// 	}catch(\XYException $e)
	// 	{
	// 		$this->jsonErrorReturn($e->getCode());
	// 	}
	}



}