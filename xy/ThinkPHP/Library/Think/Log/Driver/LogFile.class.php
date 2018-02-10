<?php

namespace Think\Log\Driver;

class LogFile {

    protected $config  =   array(
        'log_time_format'   =>  ' c ',
        'log_file_size'     =>  2097152
    );

    protected $myselfConfig = null;

    private $uuid = null;

    // 实例化并传入参数
    public function __construct($config=array()){
        $this->config       = array_merge($this->config,$config);
        $this->myselfConfig = loadLocalConfig(__METHOD__);
        $this->myselfConfig = array_merge($this->myselfConfig,loadConfig_key(__METHOD__));
    }

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination  写入目标
     * @return void
     */
    public function write($log,$destination='') {

        if ($this->uuid == null)
        {
            $this->uuid = guid();
        }

        $data = array(
            'trans_id'       => $this->uuid,//唯一事件id
            'moduleName'     => 'server',//模块名称:server/web/android
            'admin_uid'      =>  getAdminUid(),
            'uid'            =>  getUid(),
            'instance_group' => $this->myselfConfig['INSTANCE_GROUP'],//实例组:official,test,dev
            'instance_id'    => $this->myselfConfig['INSTANCE_ID'],//实例id,主机id
            'host_name'      => $this->myselfConfig['HOST_NAME'],//主机名称
            'event_group'    => '',//事件组，预留
            'client_ip'      => $_SERVER['REMOTE_ADDR'],//客户ip地址
            'request_uri'    => __SELF__,//请求网址字符串
            'module'         => MODULE_NAME,
            'controller'     => CONTROLLER_NAME,
            'action'         => ACTION_NAME,
            'content'        => $log,
            'result'         => '',//返回结果，预留
            'shop_name'      => session('user_auth.shop_name'),
            'user_name'           => session('user_auth.name'),
            'reg_time'           => date($this->config['log_time_format']),
        );
        foreach ($data as $key => $value)
        {
            if ($value === null)
                $data[$key] = 0;
        }
        $data['sign'] = md5($this->myselfConfig['LOG_KEY'].implode('',$data));

            $files = scandir($this->myselfConfig['log_path'] . "log_file/");
            $destination = 0;
            foreach ($files as $name)
            {
                $tmpFile = null;
                $tmpFile = explode('.',$name,1);
                $tmp = (int)($tmpFile[0]);
                if ($tmp > $destination) $destination = $tmp;
            }
            if ($destination == 0) $destination = time();
            $destination = $this->myselfConfig['log_path'] . "log_file/" . ((string)$destination) . ".log";
            chmod($destination,0777);
            // 自动创建日志目录
            $log_dir = dirname($destination);
            if (!is_dir($log_dir)) {
                mkdir($log_dir, 0755, true);
            }
            //检测日志文件大小，超过配置大小则备份日志文件重新生成
            if(is_file($destination) && floor($this->config['log_file_size']) <= filesize($destination) ){
                $destination = $this->myselfConfig['log_path'] . "log_file/" . ((string)time()) . ".log";
                chmod($destination,0777);
            }
        
        //error_log(json_encode($data)."\r\n{$log}\r\n", 3,$destination);
        error_log(json_encode($data)."\0", 3,$destination);
    }

}
