<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 用户控制器：所有需要用户登才可以进行的有关用户的操作.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UserController extends HomeController
{
	/**
	 * 首页，测试用
	 * @internal
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

		echo 'uid:'.session("user_auth.uid")." || username:".session("user_auth.username")." || name:".session("user_auth.name")." || admin_uid:".session("user_auth.admin_uid")."<br>sign:".session("user_auth_sign")."<br>";

		$this->display();
	}



	/**
	 * @see \Home\Model\UserModel::register()
	 */
	public function register()
	{
        if(!C('USER_ALLOW_REGISTER'))
        {
            $this->error('注册已关闭');
        }

		try
		{
			$this->jsonReturn(D("User")->register(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\UserModel::logout()
	 */
	public function logout()
	{
		try
		{
			$this->jsonReturn(D('User')->logout());
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

	
	/**
	 * @see \Home\Model\UserModel::editShopInfo()
	 */
	public function editShopInfo()
	{
		try
		{
			$this->jsonReturn(D("User")->editShopInfo(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\UserModel::getList()
	 */
	public function getList()
	{
		try
		{
			$this->jsonReturn(D("User")->getList(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}


	/**
	 * @see \Home\Model\UserModel::getUserTODAY_HISTORY_CONFIG()
	 * @deprecated v0.3
	 */
	public function getUserTODAY_HISTORY_CONFIG()
	{
		$this->_empty();
		// $tmp = D('User')->getUserTODAY_HISTORY_CONFIG();

		// if ($tmp === false)//用户还没有设置过这一项
		// {
		// 	$this->jsonReturn(null);
		// }
		// elseif ($tmp <= 0)//返回错误
		// {
		// 	$this->jsonReturn($tmp);
		// }
		// else
		// {
		// 	$this->jsonReturn($tmp);
		// }
	}



	/**
	 * @see \Home\Model\UserModel::setUserTODAY_HISTORY_CONFIG()
	 * @deprecated v0.3
	 */
	public function setUserTODAY_HISTORY_CONFIG()
	{
		$this->_empty();
		// $this->jsonReturn(D('User')->setUserTODAY_HISTORY_CONFIG());
	}


	/**
	 * @see \Home\Model\UserModel::getUserInfo_shopName()
	 */
	public function getUserInfo_shopName()
	{
		try
		{
			$this->jsonReturn(D("User")->getUserInfo_shopName(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\UserModel::getShopInfo()
	 */
	public function getShopInfo()
	{
		try
		{
			$this->jsonReturn(D("User")->getShopInfo(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\UserModel::getUserInfo_name()
	 */
	public function getUserInfo_name()
	{
		try
		{
			$this->jsonReturn(D("User")->getUserInfo_name(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\UserModel::setGTClientID()
	 */
	public function setGTClientID()
	{
		try
		{
			$this->jsonReturn(D("User")->setGTClientID(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\UserModel::editUserInfo()
	 */
	public function editUserInfo()
	{
		try
		{
			$this->jsonReturn(D("User")->editUserInfo(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
    /**
     * @see \Home\Model\UserModel::setOptionArray()
     */
	public function setOptionArray(){
	    try{
	        $this->jsonReturn(D('User')->setOptionArray(I('param.')));
        }catch(\XYException $e){
	        $this->jsonErrorReturn($e->getCode());
        }
    }

    /**
     * @see \Home\Model\UserModel::getOptionArray()
     */
    public function getOptionArray(){
        try{
            $this->jsonReturn(D('User')->getOptionArray());
        }catch(\XYException $e){
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function getStaffInfo(){
        try{
            $this->jsonReturn(D('User')->getStaffInfo());
        }catch(\XYException $e){
            $this->jsonErrorReturn($e->getCode());
        }
    }
    public function changeRpg()
    {
        try
        {
            $this->jsonReturn(D("User")->changeRpg(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
}
