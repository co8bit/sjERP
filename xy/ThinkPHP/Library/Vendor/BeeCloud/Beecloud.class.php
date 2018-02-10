<?php
// use \Home\Model\BaseadvModel;

// // require_once(dirname(__FILE__) . '/lib/class.geetestlib.php');

// class Beecloud extends BaseadvModel
// {
//     protected $config = null;
//     protected $JyInstance = null;//极验库实例

//     public function __construct()
//     {
//         $this->config = require_once(APP_PATH . '../config/config.php');
        
//         $this->app_id     = $this->config['BeeCloud_APP_ID'];
//         $this->app_secret = $this->config['BeeCloud_APP_SECRET'];
//     }

//     public function getPayBillParam(array $data = null,$isAlreadyStartTrans = false)
//     {
//         $sn = str_replace('-','',guid());
        
//         // $out_trade_no = "bc" . time();//订单号，需要保证唯一性
//         // $sign = md5($app_id . $title . $amount . $out_trade_no . $app_secret);
//     }

// }