<?php
namespace Home\Controller;
use Think\Controller;
require_once(APP_PATH.'Common/Common/exception.php');
require_once(APP_PATH.'Home/Conf/error_code.php');


define("MODEL_INSERT",1);
define("MODEL_UPDATE",2);




/**
 * 最基础的前台公共控制器，没有权限之分.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 *
 * ![json编码json_option](http://php.net/manual/zh/json.constants.php):
 * JSON_FORCE_OBJECT:使一个非关联数组输出一个类（Object）而非数组。 在数组为空而接受者需要一个类（Object）的时候尤其有用。 自 PHP 5.3.0 起生效。
 * @todo JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_UNESCAPED_UNICODE
 */
class BaseController extends Controller
{
    protected $config = null;

    /* 空操作，用于输出404页面 */
    public function _empty()
    {
        $this->jsonErrorReturn(-404);
        exit(0);
    }

    

    protected function _initialize()
    {
        register_shutdown_function('Home\Controller\BaseController::fatalError');
        set_error_handler('Home\Controller\BaseController::appError');
        set_exception_handler('Home\Controller\BaseController::appException');

        header("Content-Type:text/html; charset=utf-8");


        //加载全局配置
        C(loadConfig(__METHOD__));
        C(loadConfig_key(__METHOD__));
        C(loadLocalConfig(__METHOD__));
        // log_("loadConfig",loadConfig(),$this,'DEBUG',true);
        // log_("C('CONST_FEE')",C('CONST_FEE'),$this,'DEBUG',true);


        if (C('LOG_RECORD_METHOD') == 'File')
            \Think\Log::write('<font style="color:#FFFFFF; font-weight:bold;background: #000000;">==================================================START==================================================</font>'
                .'<br><font color="#FF66FF"><b>PARAM:</b></font>服务器接收到的参数：'.dump(I("param."),false)
                ,'WORK');
        else

        {
            \Think\Log::write('<font style="color:#FFFFFF; font-weight:bold;background: #000000;">==================================================START-record==================================================</font>'.'<br><font color="#FF66FF"><b>PARAM:</b></font>服务器接收到的参数：'.dump(I("param."),false)
                ,'DEBUG');
        }

    }



    /**
     * json方式返回正确的数据到客户端，这里返回的都是EC=1的数据.
     * @note 数字被当作字符串输出
     * @param mixed $data 要返回的数据，只接受3种格式：
*                    输入        调用者期望目的
*                    1.null：       正确的返回，但是json返回值为空
*                    2.数组：       输出json
*                    3.string:      要输出的字符串
*                    4.数字:        被当成字符串输出
     * @param int $json_option 传递给json_encode的option参数
     * 
     * @return json格式如下：
     *         1. null：{"EC":1,"data":[]}
     *         2. 数组：{"EC":1,"data":[{"sku_id":"7"},{...}]}
     *                  {"EC":1,"data":{"time":[1,0,0]}}
     *         3. 字符串或数字：{"EC":1,"data":"xy"}
     *                          {"EC":1,"data":"10"}
     */
    protected function jsonReturn($data,$json_option = 0)
    {
        if (C('IS_ALLOW_CROSS_DOMAIN'))
        {
            // $origin = isset($_SERVER['HTTP_REFERER']) ? I('server.HTTP_REFERER') : '';
            $origin = '*';
            if ( in_array($origin,C('ALLOW_ORIGIN'),true) )
            {
                $origin = rtrim($origin,'/');
                header('Access-Control-Allow-Origin:'.$origin);
                // log_("origin",$origin,$this);
                // log_("$origin",'Access-Control-Allow-Origin:'.$origin,$this);
                header('Access-Control-Allow-Methods:POST,GET');
                header('Access-Control-Allow-Headers:application/x-www-form-urlencoded,content-type');
            }
            else
              exit('406');
        }


        $echoJson = '';
        if ($data === null)//$data为null
        {
            $echoJson = '{"EC":1,"data":'.json_encode(array(),$json_option).'}';
        }
        else
        {
            $echoJson = '{"EC":1,"data":'.json_encode($data,$json_option).'}';
        }

        //输出日志
        //因为用record不能自定义标签，所以先INFO，然后在变色那里改成PARAM
        // \Think\Log::record('最后的返回Array：'.dump($data,false),'INFO');
        \Think\Log::record('最后的返回Array：关。在BaseController中可以打开','INFO');
        // \Think\Log::record('最后的返回JSON：'.$echoJson,'INFO');
        \Think\Log::record('最后的返回JSON：'.mb_substr($echoJson,0,min(50,mb_strlen($echoJson))).'......','INFO');

        if (C('JSONRETURN_HEADER_JSON'))
        {
            header('Content-Type:application/json; charset=utf-8');
            exit($echoJson);
        }
        else //HTML
        {
            header('Content-Type:application/json; charset=utf-8');
            echo $echoJson;
        }
    }



    /**
     * json方式返回错误代码EC到客户端
     * @param int $data 要返回的ec或者json前的数组，且这个数组里要有一个EC字段，其内数字<=0
     * @param int $json_option 传递给json_encode的option参数
     * 
     * @return json格式如下：{"EC":0}
     *                       {"EC":-51,"data":"1"}
     * @return 未知错误的json格式如下：{"EC":-400}，同时会输出一条异常log
     */
    protected function jsonErrorReturn($data,$json_option = 0)
    {

        if (C('IS_ALLOW_CROSS_DOMAIN'))
        {
            // $origin = isset($_SERVER['HTTP_REFERER']) ? I('server.HTTP_REFERER') : '';
            $origin = '*';
            if ( in_array($origin,C('ALLOW_ORIGIN'),true) )
            {
                $origin = rtrim($origin,'/');
                header('Access-Control-Allow-Origin:'.$origin);
                // log_("origin",$origin,$this);
                // log_("$origin",'Access-Control-Allow-Origin:'.$origin,$this);
                header('Access-Control-Allow-Methods:POST,GET');
                header('Access-Control-Allow-Headers:application/x-www-form-urlencoded,content-type');
            }
            else
              exit('406');
        }


        $EMsg = new \ErrorCode\ErrorCode();
        $ERROR_MSG_CN = $EMsg->getErrorCodeMsg();

        $errorMSGInfo = '';

        if (is_numeric($data))
        {
            if ($data <= 0)
            {
                if (empty($ERROR_MSG_CN[$data]))
                    log_("JSON返回了一个没有对应中文意思的错误代码：".$data,null,__METHOD__,\Think\Log::EMERG);
                $errorMSGInfo = $ERROR_MSG_CN[$data];
                $echoJson = json_encode(array('EC'=>$data,'MSG'=>$ERROR_MSG_CN[$data]),$json_option);
            }
            else
            {
                $echoJson = json_encode(array('EC'=>-400,'MSG'=>$ERROR_MSG_CN['-400']),$json_option);
                log_("JSON返回了一个本应<=0，但却>0的数字",null,__METHOD__,\Think\Log::EMERG);
            }
        }
        elseif ( is_array($data) && is_numeric($data['EC']) && ($data['EC'] <= 0) )
        {
            $data['MSG'] = $ERROR_MSG_CN[$data['EC']];
            if (empty($data['MSG']))
                    log_("JSON返回了一个没有对应中文意思的错误代码：".$data['EC'],null,__METHOD__,\Think\Log::EMERG);
            $errorMSGInfo = $data['MSG'];
            $echoJson = json_encode($data,$json_option);
        }
        else//奇怪的错误
        {
            $echoJson = json_encode(array('EC'=>-400,'MSG'=>$ERROR_MSG_CN['-400']),$json_option);
            log_("JSON返回了一个奇怪的错误",null,__METHOD__,\Think\Log::EMERG);
        }

        //输出日志
        //因为用record不能自定义标签，所以先INFO，然后在变色那里改成PARAM
        // \Think\Log::record('最后的返回Array：'.dump($data,false),'INFO');
        \Think\Log::record('最后的返回Array：关。在BaseController中可以打开','INFO');
        \Think\Log::record('错误的中文意思：'.$errorMSGInfo,'INFO');
        \Think\Log::record('最后的返回JSON：'.$echoJson,'INFO');

        if (C('JSONRETURN_HEADER_JSON'))
        {
            header('Content-Type:application/json; charset=utf-8');
            exit($echoJson);
        }
        else //HTML
        {
            header('Content-Type:application/json; charset=utf-8');
            echo $echoJson;
        }
    }



    // 致命错误捕获
    static public function fatalError()
    {
      log_("error_get_last()",error_get_last(),null);
      // log_("debug_print_backtrace()",debug_print_backtrace(),null);
      log_("debug_backtrace()",debug_backtrace(),null);

      \Think\Log::save();
      $e = error_get_last();
      if ($e) {
            switch($e['type']){
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:  
              ob_end_clean();
              self::halt($e);
              break;
          }
      }
  }



    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline)
    {
      switch ($errno)
      {
          case E_ERROR:
          case E_PARSE:
          case E_CORE_ERROR:
          case E_COMPILE_ERROR:
          case E_USER_ERROR:
            ob_end_clean();
            $errorStr = "$errstr ".$errfile." 第 $errline 行.";
            if (C('LOG_RECORD')) \Think\Log::write("[$errno] ".$errorStr,\Think\Log::ERR);
            self::halt($errorStr);
            break;
          default:
            $errorStr = "[#errno] $errstr ".$errfile." 第 $errline 行.";
            if (C('LOG_RECORD')) \Think\Log::write("[$errno] ".$errorStr,\Think\Log::ERR);
            break;
      }
    }



    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e)
    {
        $error = array();
        $error['message']   =   $e->getMessage();
        $trace              =   $e->getTrace();
        if('E'==$trace[0]['function']) {
            $error['file']  =   $trace[0]['file'];
            $error['line']  =   $trace[0]['line'];
        }else{
            $error['file']  =   $e->getFile();
            $error['line']  =   $e->getLine();
        }
        $error['trace']     =   $e->getTraceAsString();

        \Think\Log::record('未被捕获的异常：????<br>TRACE:'.$e->__toString(),\Think\Log::EMERG);
        \Think\Log::record('未被捕获的异常：'.$error['message'],\Think\Log::EMERG);


        // // 发送404信息
        // header('HTTP/1.1 404 Not Found');
        // header('Status:404 Not Found');

        self::jsonErrorReturn(-401);
        exit(0);
    }




    /**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
    static public function halt($error)
    {
        $e = array();
        if (APP_DEBUG || IS_CLI)
        {
            //调试模式下输出错误信息
            if (!is_array($error)) {
                $trace          = debug_backtrace();
                $e['message']   = $error;
                $e['file']      = $trace[0]['file'];
                $e['line']      = $trace[0]['line'];
                ob_start();
                debug_print_backtrace();
                $e['trace']     = ob_get_clean();
            } else {
                $e              = $error;
            }

            if(IS_CLI){
                exit(iconv('UTF-8','gbk',$e['message']).PHP_EOL.'FILE: '.$e['file'].'('.$e['line'].')'.PHP_EOL.$e['trace']);

            }
        }
        
        log_("e",$e,null,'EMERG');

        self::jsonErrorReturn(-402);
        exit(0);
    }
}
