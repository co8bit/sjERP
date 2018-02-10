<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 更新控制台.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UpdateController extends BaseController
{
    /**
     * 初始化
     * @internal
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.2
     * @date    2016-05-24
     */
    protected function _initialize()
    {
        parent::_initialize();
        header("Content-Type:text/html; charset=utf-8");
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

        $this->show('<a href="'.U("Index/index").'">Index</a><br>');
        $this->display();
    }


    /**
     * android手机APP打开时的检查更新接口.
     * 
     * @api
     * @deprecated 0.17
     * @return json 按格式给出
     * @example json:
     * {"EC":1,"data":{"force_update":false,"update":false,"version_name":"1.0.0","download_url":"","update_log":""}}
     * {"EC":-405}
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.3
     * @date    2016-06-13
     */
    public function androidUpdateCheck()
    {
        $this->_empty();
        // $data = null;
        // $data['1.0.0'] = array(
        //         'force_update' =>false,
        //         'update'       =>true,
        //         'version_name' =>'1.0.0',
        //         'download_url' =>"http://bbs.xyjxc.me/androidAPP/xyjxc.apk",
        //         'update_log'   =>"test"
        //     );

        // $version_name = I('param.version_name');
        // if (empty($data[$version_name]))
        //     $this->jsonErrorReturn(-405);
        // else
        //     $this->jsonReturn($data[$version_name]);
    }



}