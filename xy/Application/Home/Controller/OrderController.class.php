<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 订单控制器：创建、修改订单等.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class OrderController extends HomeController
{
	/**
	 * @see \Home\Model\OrderModel::createOrder()
	 */
	public function createOrder()
	{
		try
		{
			$this->jsonReturn(D("Order")->createOrder(I('param.')));
		}catch(\XYException $e)
		{
			$tmp = $e->getMessage();
			if (is_numeric($tmp))
				$this->jsonErrorReturn(array('EC'=>$e->getCode(),'data'=>$tmp));
			else
				$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\OrderModel::createOrder()
	 */
	public function editOrder()
	{
		try
		{
			$this->jsonReturn(D("Order")->editOrder(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\OrderModel::createOrder()
	 */
	public function setOrderStatus()
	{
		try
		{
			$this->jsonReturn(D("Order")->setOrderStatus(I('param.'),false));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}




	/**
	 * @see \Home\Model\OrderModel::get_()
	 */
	public function get_()
	{
		try
		{
			$this->jsonReturn(D('Order')->get_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\OrderModel::mGet_()
	 */
	public function mGet_()
	{
		try
		{
			$this->jsonReturn(D('Order')->mGet_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\OrderModel::createDraft()
	 */
	public function createDraft()
	{
		try
		{
			$this->jsonReturn(D("Order")->createDraft(I('param.')));
		}catch(\XYException $e)
		{
			$tmp = $e->getMessage();
			if (is_numeric($tmp))
				$this->jsonErrorReturn(array('EC'=>$e->getCode(),'data'=>$tmp));
			else
				$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\OrderModel::deleteDraft()
	 */
	public function deleteDraft()
	{
		try
		{
			$this->jsonReturn(D('Order')->deleteDraft(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\OrderModel::createAdjustAROrAP()
	 */
	public function createAdjustAROrAP()
	{
		try
		{
			$this->jsonReturn(D('Order')->createAdjustAROrAP(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

}