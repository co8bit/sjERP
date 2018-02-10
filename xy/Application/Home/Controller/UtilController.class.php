<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 工具类.不需登录即可使用
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UtilController extends BaseController
{ 
	/**
	 * @see \Home\Model\UtilModel::requestSMSSendVerifyCode()
	 */
	public function requestSMSSendVerifyCode()
	{
		try
		{
			$this->jsonReturn(D("Util")->requestSMSSendVerifyCode(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}




	/**
	 * @see \Home\Model\UtilModel::getJiyanToken()
	 */
	public function getJiyanToken()
	{
		try
		{
			$this->jsonReturn(D("Util")->getJiyanToken(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}




	/**
	 * @see \Home\Model\PaybillModel::payWebhook()
	 */
	public function payWebhook()
	{
		D("Paybill")->payWebhook(I('param.'));
	}















//=====================================测试用====================================
//=====================================测试用====================================
//=====================================测试用====================================
//=====================================测试用====================================
//=====================================测试用====================================

	/**
	 * 首页，测试用
	 * @internal server 测试用
	 */
	public function index()
	{
		try//在debug状态下才可以使用
		{
			if (APP_DEBUG !== true)
	            $this->_empty();
	        $auth = new \Think\Auth();
	        if ( !$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,getUid()) )
	            throw new \XYException(__METHOD__,-300);
	    }catch(\XYException $e)
	    {
	    	$this->jsonErrorReturn($e->getCode());
	    }



		$this->display();
	}



	/**
	 * 极验测试页
	 * @internal server 测试用
	 */
	public function testjiyan()
	{
		try//在debug状态下才可以使用
		{
			if (APP_DEBUG !== true)
	            $this->_empty();
	        $auth = new \Think\Auth();
	        if ( !$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,getUid()) )
	            throw new \XYException(__METHOD__,-300);
	    }catch(\XYException $e)
	    {
	    	$this->jsonErrorReturn($e->getCode());
	    }



        $this->display();
	}


	/**
	 * @see \Home\Model\UtilModel::checkJiyanVerifyCode()
	 * @internal server 测试用
	 */
	public function checkJiyanVerifyCode()
	{
		try//在debug状态下才可以使用
		{
			if (APP_DEBUG !== true)
	            $this->_empty();
	        $auth = new \Think\Auth();
	        if ( !$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,getUid()) )
	            throw new \XYException(__METHOD__,-300);
	    }catch(\XYException $e)
	    {
	    	$this->jsonErrorReturn($e->getCode());
	    }



		try
		{
			$this->jsonReturn(D("Util")->checkJiyanVerifyCode(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

    public function get_xy_verify_code()
    {
        try
        {
            $this->jsonReturn(D("Util")->get_xy_verify_code(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
    public function check_xy_verify_code()
    {
        try
        {
            $this->jsonReturn(D("Util")->check_xy_verify_code(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
}