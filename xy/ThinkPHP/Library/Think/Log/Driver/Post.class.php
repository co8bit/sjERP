<?php

namespace Think\Log\Driver;

class Post {

    protected $config  =   array(
        'log_time_format'   =>  ' c ',
    );

    // 实例化并传入参数
    public function __construct($config=array()){
        $this->config   =   array_merge($this->config,$config);
    }

    /**
     * 日志写入接口
     * @access public
     * @param string $log 日志信息
     * @param string $destination  写入目标
     * @return void
     */
    public function write($log,$destination='')
    {
        // $_config = null;
        // $_config = loadConfig(__METHOD__);
        // $destination = $_config['LOG_POST_RECORD_METHOD_URL'];
        // \Home\Model\BaseadvModel::log("some_info_DEBUG",null,$this);
        // \Home\Model\BaseadvModel::log("loadConfig(__METHOD__)",loadConfig(__METHOD__),$this);
        // \Home\Model\BaseadvModel::log("C('CONST_FEE')",C('CONST_FEE'),$this);
        
        // $destination = '121.42.174.105:9999';
        
        $destination = C('LOG_POST_RECORD_METHOD_URL');

        $data = array(
            'trans_id'       => guid(),//唯一事件id
            'moduleName'     => 'server',//模块名称:server/web/android
            'admin_uid'      => getAdminUid(),
            'uid'            => getUid(),
            'instance_group' => C('INSTANCE_GROUP'),//实例组:official,test,dev
            'instance_id'    => C('INSTANCE_ID'),//实例id,主机id
            'host_name'      => C('HOST_NAME'),//主机名称
            'event_group'    => '',//事件组，预留
            'client_ip'      => $_SERVER['REMOTE_ADDR'],//客户ip地址
            'request_uri'    => __SELF__,//请求网址字符串
            'module'         => MODULE_NAME,
            'controller'     => CONTROLLER_NAME,
            'action'         => ACTION_NAME,
            'time'           => date($this->config['log_time_format']),
            'content'        => $log,
            'result'         => '',//返回结果，预留
        );
        $data['sign'] = md5(C('LOG_KEY').implode('',$data));

        $jsonStr = json_encode($data);
        list($returnCode, $returnContent) = $this->http_post_json($destination, $jsonStr);
    }



    private function http_post_json($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return array($httpCode, $response);
    }
}
