<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 前台公共控制器，目的：将没有登录的用户拒之门外。
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class HomeController extends BaseController
{
    /**
     * 权限检查、获取配置
     * @return  没有权限会被跳转
     * @internal
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.2
     * @date    2016-05-24
     */
    protected function _initialize()
    {
        try
        {
            parent::_initialize();

            //检查是否登录
            $this->isLogin();

            //检查权限
            $auth = new \Think\Auth();
            log_("MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME",MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,$this);
            if ( !$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,getUid()) )
                throw new \XYException(__METHOD__,-300);

            //同一用户不能两地同时登陆
            if ( (MODULE_NAME === 'Home') && (CONTROLLER_NAME === 'User') && (ACTION_NAME === 'logout') )//登出不用检查
                ;
            else
            {
                log_("D('User')->getError()",D('User')->getError(),$this);
                D('User')->setErrorNull();//test
                $tmp = D('User')->getUserInfo(array('uid'=>getUid()));
                if ( ($tmp['admin_mobile'] != C('VISIT_USER_MOBILE')) && ($tmp['admin_mobile'] != C('ROOT_USER_MOBILE'))
                    && (md5(session_id().C('XingYunBooks_User_SECRET')) != $tmp['session_id']) )
                {
                    // log_("XingYunBooks_User_SECRET",C('XingYunBooks_User_SECRET'),$this);
                    // log_("session_id",session_id(),$this);
                    // log_("md5",md5(session_id().C('XingYunBooks_User_SECRET')),$this);
                    // log_("tmp['session_id']",$tmp['session_id'],$this);
                    D('User')->logout();
                    throw new \XYException(__METHOD__,-8890);
                }
            }

            //加载用户的配置
            $config = D('Config')->getShopSysConfig();
            C($config);

        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
            exit(0);
        }
    }



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

        
        $this->display();
    }



	/**
     * 判断是否登录
     * @internal
     * @return  boolean
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.2
     * @date    2016-05-24
     */
	protected function isLogin()
	{
		/* 用户登录检测 */
        // var_dump(is_login());
		if (is_login() > 0)
            return true;
        else
        {
            throw new \XYException(__METHOD__,-8889);
        }
	}




    /**
     * 得到登录用户的uid
     * @return unsigned_int uid
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.15
     * @date    2016-09-16
     */
    protected function getUid()
    {
        return is_login();
    }

}
