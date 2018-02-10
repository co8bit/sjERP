<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 用于权限添加的类.安装需要删除
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class AuthController extends HomeController
{ 
	
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



    public function test()
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


        $auth = new \Think\Auth();
        if ( !$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,getUid()) )
        {
            echo '你没有权限';
        }
        else
        {
        	echo '你有权限';
        }
    }



    /**
	 * @see \Home\Model\AuthGroupAccessModel::setUserGroup()
	 */
	public function setUserGroup()
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
			$this->jsonReturn(D("AuthGroupAccess")->setUserGroup(I('param.')));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	 /**
	 * @see \Home\Model\AuthGroupModel::create_()
	 */
	public function createUserGroup()
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
			$data = array(
					'title' => 'this is title',
					'status' => 1,
					'rules' => array(1,2),
				);
			$this->jsonReturn(D("AuthGroup")->create_($data));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}



	/**
	 * @see \Home\Model\AuthRuleModel::create_()
	 */
	public function createAuthRule()
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
			$data = array(
					'name' => 'Home/Auth/test1',
					'title' => 'this is title',
					'status' => 1,
					'condition' => '',
				);
			$this->jsonReturn(D("AuthRule")->create_($data));
		}catch(\XYException $e)
		{
			$this->jsonErrorReturn($e->getCode());
		}
	}

}