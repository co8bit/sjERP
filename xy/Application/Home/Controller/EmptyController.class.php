<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 空操作重定向
 */
class EmptyController extends BaseController
{

	/**
	 * 空操作，用于输出404页面
	 * @return  重定向
	 * @internal
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.2
	 * @date    2016-05-24
	 */
	public function _empty()
    {
    	if (RUN_PHPUNIT)
    		;
    	else
    	{
			$this->jsonErrorReturn(-404);
			exit(0);
		}
	}

	/**
	 * 空控制器，重定向
	 * @return  重定向
	 * @internal
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.2
	 * @date    2016-05-24
	 */
    protected function _initialize()
    {
        if (RUN_PHPUNIT)
    		;
    	else
    	{
			$this->jsonErrorReturn(-404);
			exit(0);
		}
    }

}
