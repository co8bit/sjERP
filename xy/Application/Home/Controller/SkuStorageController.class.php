<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 订单控制器：创建、修改订单等.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class SkuStorageController extends HomeController
{

	/**
	 * @see \Home\Model\SkuStorageModel::querySkuSto()
	 */
	public function querySkuSto()
	{
			try
			{
				$this->jsonReturn(D("SkuStorage")->querySkuSto(I('param.')));
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
	 * @see \Home\Model\SkuStorageModel::skuStoSummary()
	 */
	public function skuStoSummary()
	{
			try
			{
				$this->jsonReturn(D("SkuStorage")->skuStoSummary(I('param.')));
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
	 * @see \Home\Model\SkuStorageModel::get_()
	 */
	public function get_()
	{
			try
			{
				$this->jsonReturn(D("SkuStorage")->get_(I('param.')));
			}catch(\XYException $e)
			{
				$tmp = $e->getMessage();
				if (is_numeric($tmp))
					$this->jsonErrorReturn(array('EC'=>$e->getCode(),'data'=>$tmp));
				else
					$this->jsonErrorReturn($e->getCode());
			}
	}



}