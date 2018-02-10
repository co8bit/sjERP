<?php

/**
 * 异常类.
 *
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class XYException extends \Exception
{
	/**
	 * XYException构造函数.打印日志。
	 * @param   string           $message
	 * @param   integer          $code
	 * @param   XYException|null $previous
	 * @internal  server
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.2
	 * @date    2016-06-02
	 */
	public function __construct($message = "", $code = 0,XYException $previous = NULL)
	{
		if (!is_int($code))
		{
			\Think\Log::record('message:'.dump($message,false),'EMERG');
			\Think\Log::record('code_dumpMethod_output:'.dump($code,false),'EMERG');
			\Think\Log::record('previous:'.dump($previous,false),'EMERG');
			$code = -400;
		}
		
		parent::__construct($message,$code,$previous);

		if ($previous === null)
		{
			$EMsg = new \ErrorCode\ErrorCode();
        	$ERROR_MSG_CN = $EMsg->getErrorCodeMsg();

			\Think\Log::record($message . 'exception返回异常(错误)代码：<font style="color:#FF0000;">' . $code . '</font><br>'
					.'exception返回异常中文：<font style="color:#FF0000;">' . $ERROR_MSG_CN[$code] . '</font><br>'
					.'最后一条sql语句：'.M()->_sql().'<br>'
					.'TRACE: ' . $this->__toString(),\Think\Log::NOTICE);
		}
	}


	/**
	 * 重新抛出该异常，让后面的程序捕获
	 * @throws XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 0.2
	 * @date    2016-06-02
	 */
	public function rethrows()
	{
		throw new \XYException($this->message,$this->code,$this);
	}
}