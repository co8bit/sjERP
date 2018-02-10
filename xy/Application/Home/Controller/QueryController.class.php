<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 查询控制器
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class QueryController extends HomeController
{
	/**
	 * @see \Home\Model\QueryModel::query_()
	 */
	public function query_()
	{
		try
		{
			$this->jsonReturn(D("Query")->query_(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

    public function backUpEveryDay()
    {
        try
        {
            $this->jsonReturn(D("Other")->backupByExcelFile());
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

	/**
	 * @see \Home\Model\QueryModel::queryDraft()
	 */
	public function queryDraft()
	{
		try
		{
			$this->jsonReturn(D("Query")->queryDraft(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\QueryModel::search()
	 */
	public function search()
	{
		try
		{
			$this->jsonReturn(D("Query")->search(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\QueryModel::dashboard()
	 */
	public function dashboard()
	{
		try
		{
			$this->jsonReturn(D("Query")->dashboard());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\QueryModel::saleSummary()
	 */
	public function saleSummary()
	{
		try
		{
			$this->jsonReturn(D("Query")->saleSummary(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\QueryModel::purchaseSummary()
	 */
	public function purchaseSummary()
	{
		try
		{
			$this->jsonReturn(D("Query")->purchaseSummary(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	public function skuSummary(){
        try
        {
            $this->jsonReturn(D("Query")->skuSummary(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }


    public function skuChart(){
        try
        {
            $this->jsonReturn(D("Query")->skuChart(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function operatorSummary(){
        try
        {
            $this->jsonReturn(D("Query")->operatorSummary(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }


    public function operatorChart(){
        try
        {
            $this->jsonReturn(D("Query")->operatorChart(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function queryOrder(){
        try
        {
            $this->jsonReturn(D("Query")->queryOrder(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function getOperatorInfo(){
        try
        {
            $this->jsonReturn(D("Query")->getOperatorInfo(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
}