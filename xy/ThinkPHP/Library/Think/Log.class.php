<?php
namespace Think;
/**
 * 日志处理类
 */
class Log {

    // 日志级别 从上到下，由低到高
    const EMERG     = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT     = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT      = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR       = 'ERR';  // 一般错误: 一般性错误
    const WARN      = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE    = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO      = 'INFO';  // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  // 调试: 调试信息
    const SQL       = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效

    // 日志信息
    static protected $log       =  array();

    // 日志存储
    static protected $storage   =   null;

    // 日志初始化
    static public function init($config=array()){
        $_config = null;
        // \Home\Model\BaseadvModel::log("init ss",null,null,'DEBUG',true);
        // \Home\Model\BaseadvModel::log("init loadConfig(__METHOD__)",loadConfig(__METHOD__),null,'DEBUG',true);
        // self::write("save ss");
        // self::write("save loadConfig(__METHOD__)".dump(loadConfig(__METHOD__),false));
        $_config = loadLocalConfig(__METHOD__);
        $type    = $_config['LOG_RECORD_METHOD'];
        // \Home\Model\BaseadvModel::log("init type",$type,null,'DEBUG',true);
        // self::write("save type".dump($type,false));
            
        $class   = strpos($type,'\\')? $type: 'Think\\Log\\Driver\\'. ucwords(strtolower($type));
        unset($config['type']);
        self::$storage = new $class($config);
    }

    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     */
    static function record($message,$level=self::ERR,$record=false) {
        if($record || false !== strpos(C('LOG_LEVEL'),$level)) {
            self::$log[] =   "{$level}: {$message}\r\n";
        }
    }

    /**
     * 日志保存
     * @static
     * @access public
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function save($type='',$destination='') {
        
        if ( function_exists('\fastcgi_finish_request') )
        {
            // echo 'cunzai';
            // self::write("cunzai ss");
            \fastcgi_finish_request();
        }
        
        if(empty(self::$log)) return ;

        if(empty($destination)){
            $destination = C('LOG_PATH').date('y_m_d').'.log';
        }
        if(!self::$storage){
            $_config = null;
            // \Home\Model\BaseadvModel::log("save ss",null,null,'DEBUG',true);
            // \Home\Model\BaseadvModel::log("save loadConfig(__METHOD__)",loadConfig(__METHOD__),null,'DEBUG',true);
            // self::write("save ss");
            // self::write("save loadConfig(__METHOD__)".dump(loadConfig(__METHOD__),false));
            $_config = loadLocalConfig(__METHOD__);
            $type    = $_config['LOG_RECORD_METHOD'];
            // \Home\Model\BaseadvModel::log("save type",$type,null,'DEBUG',true);
            // self::write("save type".dump($type,false));
            
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();            
        }


        // wbx
        \Think\Log::record('<font style="color:#FFFFFF; background: #000000;">========================================END========================================</font>','WORK');

        $message    =   implode('',self::$log);
        // 保存后清空日志缓存
        self::$log = array();

        self::$storage->write($message,$destination);


    }

    /**
     * 日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function write($message,$level=self::ERR,$type='',$destination='') {
        if(!self::$storage){
            $_config = null;
            // \Home\Model\BaseadvModel::log("write ss",null,null,'DEBUG',false);
            // \Home\Model\BaseadvModel::log("write loadConfig(__METHOD__)",loadConfig(__METHOD__),null,'DEBUG',false);
            // self::record("save ss");
            // self::record("save loadConfig(__METHOD__)".dump(loadConfig(__METHOD__),false));
            $_config = loadLocalConfig(__METHOD__);
            $type    = $_config['LOG_RECORD_METHOD'];
            // \Home\Model\BaseadvModel::log("write type",$type,null,'DEBUG',false);
            // self::record("save type".dump($type,false));
            
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            $config['log_path'] = C('LOG_PATH');
            self::$storage = new $class($config);            
        }
        if(empty($destination)){
            $destination = C('LOG_PATH').date('y_m_d').'.log';        
        }

        //输出之前的日志
        $message_pre    =   implode('',self::$log);
        self::$log = array();

        self::$storage->write($message_pre . "{$level}: {". time() ."}{$message}", $destination);
    }
}