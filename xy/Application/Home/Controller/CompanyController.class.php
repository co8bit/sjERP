<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 往来单位控制器：包括创建、修改往来单位.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class CompanyController extends HomeController
{
	/**
	 * @see \Home\Model\CompanyModel::create_()
	 */
	public function create_()
	{
		try
		{
			$this->jsonReturn(D("Company")->create_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\CompanyModel::edit_()
	 */
	public function edit_()
	{
		try
		{
			$this->jsonReturn(D("Company")->edit_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\CompanyModel::deleteCompany()
	 */
	public function deleteCompany()
	{
		try
		{
			$this->jsonReturn(D('Company')->deleteCompany(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
	
	

	/**
	 * @see \Home\Model\CompanyModel::queryList()
	 */
	public function queryList()
	{
		try
		{
			$this->jsonReturn(D('Company')->queryList(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\CompanyModel::get_()
	 */
	public function get_()
	{
		try
		{
			$this->jsonReturn(D('Company')->get_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\CompanyModel::getBalance()
	 */
	public function getBalance()
	{
		try
		{
			$this->jsonReturn(null,D('Company')->getBalance(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\CompanyModel::queryRemain()
	 */
	public function queryRemain()
	{
		try
		{
			$this->jsonReturn(D('Company')->queryRemain(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\CompanyModel::requestStatementOfAccount()
	 */
	public function requestStatementOfAccount()
	{
		try
		{
			$this->jsonReturn(D('Company')->requestStatementOfAccount(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\CompanyModel::requestStatementOfWechatAccount()
	 */
	public function requestStatementOfWechatAccount()
	{
		try
		{
			$this->jsonReturn(D('Company')->requestStatementOfWechatAccount(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

    public function companyTransactionStatistics()
    {
        try
        {
            $this->jsonReturn(D('Company')->companyTransactionStatistics(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

}