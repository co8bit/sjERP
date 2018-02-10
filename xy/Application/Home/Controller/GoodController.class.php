<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 商品控制器：创建、修改商品等.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class GoodController extends HomeController
{

	public function test()
	{
		echo '123';
	}
	/**
	 * 创建类别
	 * @see \Home\Model\CatModel::createCat()
	 */
	public function createCat()
	{
		try
		{
			$this->jsonReturn(D("Cat")->createCat(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * 编辑类别信息
	 * @see \Home\Model\CatModel::editCat()
	 */
	public function editCat()
	{
		try
		{
			$this->jsonReturn(D("Cat")->editCat(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * 删除类别
	 * @see \Home\Model\CatModel::deleteCat()
	 */
	public function deleteCat()
	{
		try
		{
			$this->jsonReturn(D("Cat")->deleteCat(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * 查询店铺分类
	 * @see \Home\Model\CatModel::queryCat()
	 */
	public function queryCat()
	{
		try
		{
			$this->jsonReturn(D('Cat')->queryCat(I("param.")));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\SkuModel::updateSKU()
	 */
	public function updateSKU()
	{
		try
		{
			$this->jsonReturn(D("Sku")->updateSKU(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\SpuModel::deleteSPU()
	 */
	public function deleteSPU()
	{
		try
		{
			$this->jsonReturn(D("Spu")->deleteSPU(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * 同时修改商品的SPU和SKU信息.
	 *
	 * @api
	 * @param 参考editSKU和editSPU的输入输出
	 * @see \Home\Model\SpuModel::editSPU()
	 * @see \Home\Model\SkuModel::editSKU()
	 *
	 * @return 1 成功
	 */
	public function editSPUSKU()
	{
		try
		{
			M()->startTrans(__METHOD__);
			$returnEditSpu = D("Spu")->editSPU(I('param.'),true);
			$returnEditSku = D("Sku")->updateSKU(I('param.'),true);
			M()->commit(__METHOD__);
			$this->jsonReturn(1);
		}catch(\XYException $e)
		{
			M()->rollback(__METHOD__);
			$this->jsonErrorReturn($e->getCode());
		}
	}


	
	/**
	 * @see \Home\Model\SkuModel::querySkU()
	 */
	public function querySkU()
	{
		try
		{
			$this->jsonReturn(D('Sku')->querySkU(I("param.")));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\SkuModel::get_()
	 */
	public function get_()
	{
		try
		{
			$this->jsonReturn(D('Sku')->get_(I("param."),true));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\SpuModel::querySPU()
	 */
	public function querySPU()
	{
		try
		{
			$this->jsonReturn(D('Spu')->querySPU(I("param.")));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\SkuCidPriceModel::getThisCustomerLastPrice()
	 */
	public function getThisCustomerLastPrice()
	{
		try
		{
			$this->jsonReturn(D('SkuCidPrice')->get_(I("param."),true));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


}