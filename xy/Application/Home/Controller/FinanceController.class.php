<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 财务方面的控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class FinanceController extends HomeController
{
	/**
	 * @see \Home\Model\FinanceModel::createReceiptOrPayment()
	 */
	public function createReceiptOrPayment()
	{
		try{
			$this->jsonReturn(D("Finance")->createReceiptOrPayment(I('param.')));
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
	 * @see \Home\Model\FinanceModel::createReceiptOrPaymentDraft()
	 */
	public function createReceiptOrPaymentDraft()
	{
		try{
			$this->jsonReturn(D("Finance")->createReceiptOrPaymentDraft(I('param.')));
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
	 * @see \Home\Model\FinanceModel::createIncomeOrExpense()
	 */
	public function createIncomeOrExpense()
	{
		try
		{
			$this->jsonReturn(D("Finance")->createIncomeOrExpense(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\FinanceModel::createIncomeOrExpenseDraft()
	 */
	public function createIncomeOrExpenseDraft()
	{
		try
		{
			$this->jsonReturn(D("Finance")->createIncomeOrExpenseDraft(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\FinanceModel::editDocument()
	 */
	public function editDocument()
	{
		try
		{
			$this->jsonReturn(D("Finance")->editDocument(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\FinanceModel::queryOneDocument()
	 */
	public function queryOneDocument()
	{
		try
		{
			$this->jsonReturn(D("Finance")->queryOneDocument(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\FinanceModel::deleteDraft()
	 */
	public function deleteDraft()
	{
		try
		{
			$this->jsonReturn(D('Finance')->deleteDraft(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

    /**
     * @see \Home\Model\create_FinanceOrder::create_FinanceOrder()
     */
    public function create_FinanceOrder()
    {
        try
        {
            $this->jsonReturn(D('Finance')->create_FinanceOrder(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function query_FinanceOrder()
    {
        try
        {
            $this->jsonReturn(D('Finance')->query_FinanceOrder(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function create_FinanceOrderDraft()
    {
        try
        {
            $this->jsonReturn(D('Finance')->create_FinanceOrderDraft(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function createFinanceCart()
    {
        try
        {
            $this->jsonReturn(D('Sku')->createFinanceCart(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function editFinanceCart()
    {
        try
        {
            $this->jsonReturn(D('Sku')->editFinanceCart(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function queryFinanceCart()
    {
        try
        {
            $this->jsonReturn(D('Sku')->queryFinanceCart(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function deleteFinanceCart()
    {
        try
        {
            $this->jsonReturn(D('Sku')->deleteFinanceCart(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function financeOrderStatusChange()
    {
        try
        {
            $this->jsonReturn(D('Finance')->financeOrderStatusChange(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
    public function invoicePoolSummary()
    {
        try
        {
            $this->jsonReturn(D('Finance')->invoicePoolSummary(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
    public function groupStatusChange()
    {
        try
        {
            $this->jsonReturn(D('Finance')->groupStatusChange(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function getExpenditureStatistics()
    {
        try
        {
            $this->jsonReturn(D('Finance')->getExpenditureStatistics(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function financeDashboard()
    {
        try
        {
            $this->jsonReturn(D('Finance')->financeDashboard(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
    public function edit_FinanceOrder()
    {
        try
        {
            $this->jsonReturn(D('Finance')->edit_FinanceOrder(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function delete_FinanceOrder()
    {
        try
        {
            $this->jsonReturn(D('Finance')->delete_FinanceOrder(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

}