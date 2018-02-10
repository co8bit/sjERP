<?php
namespace Home\Behaviors;

class appendBehavior extends \Think\Behavior
{
    protected $tracePageTabs =  array('BASE'=>'基本','FILE'=>'文件','INFO'=>'流程','ERR|NOTIC'=>'错误','SQL'=>'SQL','DEBUG'=>'调试');

    // 行为扩展的执行入口必须是run
    public function run(&$params)
    {
            $this->showTrace();
    }

    /**
     * 显示页面Trace信息
     * @access private
     */
    private function showTrace() {
         // 系统默认显示信息
        $files  =  get_included_files();
        $info   =   array();
        foreach ($files as $key=>$file){
            $info[] = $file.' ( '.number_format(filesize($file)/1024,2).' KB )';
        }
        $trace  =   array();
        $base   =   array(
            '请求信息'  =>  date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME']).' '.$_SERVER['SERVER_PROTOCOL'].' '.$_SERVER['REQUEST_METHOD'].' : '.__SELF__,
            '运行时间'  =>  $this->showTime(),
            '吞吐率'    =>  number_format(1/G('beginTime','viewEndTime'),2).'req/s',
            '内存开销'  =>  MEMORY_LIMIT_ON?number_format((memory_get_usage() - $GLOBALS['_startUseMems'])/1024,2).' kb':'不支持',
            '查询信息'  =>  N('db_query').' queries '.N('db_write').' writes ',
            '文件加载'  =>  count(get_included_files()),
            '缓存信息'  =>  N('cache_read').' gets '.N('cache_write').' writes ',
            '配置加载'  =>  count(C()),
            '会话信息'  =>  'SESSION_ID='.session_id(),
            );
        // 读取应用定义的Trace文件
        $traceFile  =   COMMON_PATH.'Conf/trace.php';
        if(is_file($traceFile)) {
            $base   =   array_merge($base,include $traceFile);
        }
        $debug  =   trace();
        $tabs   =   C('TRACE_PAGE_TABS',null,$this->tracePageTabs);
        foreach ($tabs as $name=>$title){
            switch(strtoupper($name)) {
                case 'BASE':// 基本信息
                    $trace[$title]  =   $base;
                    break;
                case 'FILE': // 文件信息
                    $trace[$title]  =   $info;
                    break;
                default:// 调试信息
                    $name       =   strtoupper($name);
                    if(strpos($name,'|')) {// 多组信息
                        $names  =   explode('|',$name);
                        $result =   array();
                        foreach($names as $name){
                            $result   +=   isset($debug[$name])?$debug[$name]:array();
                        }
                        $trace[$title]  =   $result;
                    }else{
                        $trace[$title]  =   isset($debug[$name])?$debug[$name]:'';
                    }
            }
        }

        //错误
        if (!empty($trace['错误']))
        {
            $tmp = null;
            foreach ($trace['错误'] as $key => $value)
            {
                $tmp .= $trace['错误'][$key].'<br>';
            }
            \Think\Log::record($tmp,'WARN');
        }
        
        //SQL
        if (!empty($trace['SQL']))
        {
            $tmp = null;
            foreach ($trace['SQL'] as $key => $value)
            {
                $tmp .= $trace['SQL'][$key].'<br>';
            }
            \Think\Log::record('<br>本次执行的全部SQL语句：<br>'.$tmp,'SQL');
        }


        //调试
        if (!empty($trace['调试']))
        {
            $tmp = null;
            foreach ($trace['调试'] as $key => $value)
            {
                $tmp .= $trace['调试'][$key].'<br>';
            }
            \Think\Log::record($tmp,'DEBUG');
        }


        //日志输出
        //基本信息
        if ((CONTROLLER_NAME != 'Index') && (!empty($trace['基本'])))
        {
        	$tmp = null;
	        foreach ($trace['基本'] as $key => $value)
	        {
	            $tmp .= $key.$trace['基本'][$key].'<br>';
	        }
	        \Think\Log::record('<br>基本信息：<br>'.$tmp,'INFO');
	    }

	    /*
	    //文件
        if (!empty($trace['文件']))
        {
        	$tmp = null;
	        foreach ($trace['文件'] as $key => $value)
	        {
	            $tmp .= $trace['文件'][$key].'<br>';
	        }
	        \Think\Log::record('<br>文件：<br>'.$tmp,'INFO');
	    }


	    //流程
        if (!empty($trace['流程']))
        {
        	$tmp = null;
	        foreach ($trace['流程'] as $key => $value)
	        {
	            $tmp .= $trace['流程'][$key].'<br>';
	        }
	        \Think\Log::record('<br>流程：<br>'.$tmp,'INFO');
	    }
        */



        unset($files,$info,$base);
    }

    /**
     * 获取运行时间
     */
    private function showTime() {
        // 显示运行时间
        G('beginTime',$GLOBALS['_beginTime']);
        G('viewEndTime');
        // 显示详细运行时间
        return G('beginTime','viewEndTime').'s ( Load:'.G('beginTime','loadTime').'s Init:'.G('loadTime','initTime').'s Exec:'.G('initTime','viewStartTime').'s Template:'.G('viewStartTime','viewEndTime').'s )';
    }

}