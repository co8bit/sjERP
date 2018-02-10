<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-5-9
 * Time: 下午3:12
 */
require_once(dirname(__FILE__) . '/' . 'LogUtils.php');
class HttpManager
{
    private static function httpPost($url, $data, $gzip, $action)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'GeTui PHP/1.0');
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, GTConfig::getHttpConnectionTimeOut());
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, GTConfig::getHttpSoTimeOut());
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $header = array("Content-Type:text/html;charset=UTF-8");
        if ($gzip) {
            $data = gzencode($data, 9);
            array_push($header,'Accept-Encoding:gzip');
            array_push($header,'Content-Encoding:gzip');
            curl_setopt($curl, CURLOPT_ENCODING, "gzip");
        }
        if(!is_null($action))
        {
            array_push($header,"Gt-Action:".$action);
        }
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $curl_version = curl_version();
        if ($curl_version['version_number'] >= 462850) {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 30000);
            curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
        }
        //通过代理访问接口需要在此处配置代理
        //curl_setopt($curl, CURLOPT_PROXY, '192.168.1.18:808');
        //请求失败有3次重试机会
        $result = HttpManager::exeBySetTimes(3, $curl);
        curl_close($curl);
        return $result;
    }
	
	public static function httpHead($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($curl, CURLOPT_USERAGENT, 'GeTui PHP/1.0');
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'HEAD');
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, GTConfig::getHttpConnectionTimeOut());
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, GTConfig::getHttpSoTimeOut());
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $header = array("Content-Type:text/html;charset=UTF-8");
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);

        $curl_version = curl_version();
        if ($curl_version['version_number'] >= 462850) {
            curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 30000);
            curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
        }
        //通过代理访问接口需要在此处配置代理
        //curl_setopt($curl, CURLOPT_PROXY, '192.168.1.18:808');
        //请求失败有3次重试机会
		$result = HttpManager::exeBySetTimes(3, $curl);
		
        curl_close($curl);
        return $result;
    }

    public static function httpPostJson($url, $params, $gzip)
    {
        if(!isset($params["version"]))
        {
            $params["version"] = GTConfig::getSDKVersion();
        }
        $action = $params["action"];
        $data = json_encode($params);
        $result = null;
        try {
            $resp = HttpManager::httpPost($url, $data, $gzip, $action);
            //LogUtils::debug("发送请求 post:{$data} return:{$resp}");
            $result = json_decode($resp, true);
            return $result;
        } catch (Exception $e) {
            throw new RequestException($params["requestId"],"httpPost:[".$url."] [" .$data." ] [ ".$result."]:",$e);
        }
    }

    private static function exeBySetTimes($count, $curl)
    {
        $result = curl_exec($curl);
		$info = curl_getinfo($curl);
		$code = $info["http_code"];
		
        if (curl_errno($curl) != 0 && $code != 200) {
            LogUtils::debug("request errno: ".curl_errno($curl).",url:".$info["url"]);
			$count--;
            if ($count > 0) {
                $result = HttpManager::exeBySetTimes($count, $curl);
            }
        }
        return $result;
    }
}