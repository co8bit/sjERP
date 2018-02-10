<?php
namespace Home\Controller;
use Think\Controller;

/**
 * Config类.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class ConfigController extends HomeController
{
	/**
	 * @see \Home\Model\ConfigModel::getLockShopStatus()
	 */
	public function getLockShopStatus()
	{
		try
		{
			$this->jsonReturn(D("Config")->getLockShopStatus());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ConfigModel::unlockShop()
	 */
	public function unlockShop()
	{
		try
		{
			$this->jsonReturn(D("Config")->unlockShop());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ConfigModel::lockShop()
	 */
	public function lockShop()
	{
		try
		{
			$this->jsonReturn(D("Config")->lockShop());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\ConfigModel::setShopConfig()
	 */
	public function setShopConfig()
	{
		try
		{
			$this->jsonReturn(D("Config")->setShopConfig(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\ConfigModel::getShopConfig()
	 */
	public function getShopConfig()
	{
		try
		{
			$this->jsonReturn(D("Config")->getShopConfig());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
}