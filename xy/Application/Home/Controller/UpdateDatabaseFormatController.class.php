<?php
namespace Home\Controller;
use Think\Controller;


/**
 * 往来单位控制器：包括创建、修改往来单位.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UpdateDatabaseFormatController extends BaseController
{
	/**
	 * @see \Home\Model\UpdateDatabaseFormatModel::resetRemain()
	 */
	public function resetRemain()
	{
		try
		{
			$this->jsonReturn(D('UpdateDatabaseFormat')->resetRemain());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	/**
	 * @see \Home\Model\UpdateDatabaseFormatModel::initializeStoSku()
	 */
	public function initializeStoSku()
	{
		try
		{
			$this->jsonReturn(D('UpdateDatabaseFormat')->initializeStoSku());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

    public function history_trans_form()
    {
        try
        {
            $this->jsonReturn(D('UpdateDatabaseFormat')->history_trans_form());
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
}