<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 用户登录、注册控制器：只包括所有用户的登录和管理员用户的注册和一切不需要用户登录就可以进行的有关用户的操作.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class LoginController extends BaseController
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
	 * @see \Home\Model\UserModel::admin_register()
	 */
	public function admin_register()
	{
		try
		{
			if(!C('USER_ALLOW_REGISTER'))
	        {
	            $this->error('注册已关闭');
	        }
			$this->jsonReturn(D("User")->admin_register(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
	/**
	 * @see \Home\Model\UserModel::login()
	 */
	public function login()
	{
		try
		{
			if(!C('USER_ALLOW_LOGIN'))
				throw new \XYException(__METHOD__,-8888);
			$this->jsonReturn(D("User")->login(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\UserModel::editUserPasswd()
	 */
	public function editUserPasswd()
	{
		try
		{
			$this->jsonReturn(D("User")->editUserPasswd(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}
	public function create_proxy()
    {
        try
        {
            $this->jsonReturn(D("User")->create_proxy(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

}