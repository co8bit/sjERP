<?php


/**
 * 输出log日志
 * @param  string $info  要输出的信息
 * @param anyone $data 要被显示的简单变量或要被dump出的复合变量
 * @param  this  $pthis 请传入外部this指针
 * @param  string $level log等级
 * @param bool $noCache 是否马上写入：true-马上写入
 */
function log_($info = '',$data = null,$pthis = null,$level = 'DEBUG',$noCache = false)
{
    if ($pthis === null)
        $methodName = '??????';
    else
        $methodName = get_class($pthis);
    
    if ($data === null)
        $dumpInfo = '';
    else
        $dumpInfo = dump($data,false);

    if ($noCache)
        \Think\Log::write($methodName.'======>'.$info.' : '.$dumpInfo,$level);
    else
        \Think\Log::record($methodName.'======>'.$info.' : '.$dumpInfo,$level);
}




/**
 * 系统非常规MD5加密方法
 *
 * @param  string $str 要加密的字符串
 * @return string 
 */
function think_ucenter_md5($str, $key)
{
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 */
function is_login()
{
    $user = session('user_auth');
    log_("user",$user);
    log_("session('user_auth_sign')",session('user_auth_sign'));
    log_("data_auth_sign(user)",data_auth_sign($user));
    // var_dump($user);
    // var_dump(session());
    if (empty($user))
        return 0;
    else
    {
    	// echo 33333;
    	// var_dump(data_auth_sign($user));
    	// var_dump(is_bool(data_auth_sign($user)));
    	$tmp = session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    	// var_dump($tmp);
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    //数据类型检测
    // var_dump($data);
    if(!is_array($data))
    {
        $data = (array)$data;
    }
    ksort($data);//排序
    $code = http_build_query($data);//url编码并生成query字符串
    // var_dump($code);
    $sign = sha1($code); //生成签名
    // var_dump($sign);
    return $sign;
}

/**
 * 判断字符串是不是整数
 * @param  string $data,要判断的字符串
 * @return boolean
 */
function isInt($data)
{
	if(is_numeric($data))
	{
		if (strpos($data,".") !== false)
			return false;
		else
			return true;
	}
	else
		return false;
}

/**
 * 判断字符串是不是正整数(>0)
 * @param  string $data,要判断的字符串
 * @return boolean
 */
function isUnsignedInt($data)
{
	if ( isInt($data) && ($data > 0) )
		return true;
	else
		return false;
}


/**
 * 判断字符串是不是非负整数(>=0)
 * @param  string $data,要判断的字符串
 * @return boolean
 */
function isNonnegativeInt($data)
{
	if ( isInt($data) && ($data >= 0) )
		return true;
	else
		return false;
}



/**
 * 判断字符串是不是非负实数(>=0)
 * @param  string $data,要判断的字符串
 * @return boolean
 */
function isNonegativeReal($number)
{
	if ( is_numeric($number) && ($number >= 0) )
		return true;
	else
		return false;
}



/**
 * 判断字符串是不是正实数(>0)
 * @param  string $data,要判断的字符串
 * @return boolean
 */
function isPositiveReal($number)
{
	if ( is_numeric($number) && ($number > 0) )
		return true;
	else
		return false;
}



/**
 * 四舍五入一个数
 * @param  double $num,要四舍五入的数字
 * @param  int $digit 要保留的小数位数
 * @return double 四舍五入后的数
 */
function xyround($num = 0,$digit = 2)
{
	return round($num,$digit);
}

/**
 * 判断输入的数据小数位长度
 * @param string $num 要判断小数点位数的数据
 * @return int $len 小数位数
 */
function getLenOfDeciNum($num)
{
	if( (strpos($num,'.')) !== false )
	{
		$len = strlen($num)-strpos($num,'.')-1;
	}else
	{
		$len = 0;
	}
	return $len;
}

/**
 * 判断货币小数位数是否合法
 * @param string $num 要判断小数点位数的数据
 * @return boolean
 */
function isLegalCurrency($num)
{
	if( (strpos($num,'.')) !== false )
	{
		$len = strlen($num)-strpos($num,'.')-1;
		if($len > 2)
			return false;
	}
	return true;		
}

/**
 * 浮点型数据相加
 * @param mix $a 左操作数
 * @param mix $b 右操作数
 * @param int $digit 要保留的小数位数,默认为2,多于两个操作数时,中间计算过程传0，最后一步计算时传值
 * @return $data 计算后的数据
 */
function xyadd($a,$b,$digit = 2)
{
	if(!$digit)
	{
		$data = $a+$b;
	}else
	{
		$data = round(($a+$b),$digit);
	}
	return $data;
}

/**
 * 浮点型数据相减
 * @param mix $a 左操作数
 * @param mix $b 右操作数
 * @param int $digit 要保留的小数位数,默认为2,多于两个操作数时,中间计算过程传0，最后一步计算时传值
 * @return $data 计算后的数据
 */
function xysub($a,$b,$digit=2)
{
	if(!$digit)
	{
		$data = $a-$b;
	}else
	{
		$data = round(($a-$b),$digit);
	}
	return $data;
}

/**
 * 浮点型数据相乘
 * @param mix $a 左操作数
 * @param mix $b 右操作数
 * @param int $digit 要保留的小数位数,默认为2,多于两个操作数时,中间计算过程传0，最后一步计算时传值
 * @return $data 计算后的数据
 */
function xymul($a,$b,$digit=2)
{
	if(!$digit)
	{
		$data = $a*$b;
	}else
	{
		$data = round(($a*$b),$digit);
	}
	return $data;
}

/**
 * 浮点型数据相除
 * @param mix $a 左操作数
 * @param mix $b 右操作数
 * @param int $digit 要保留的小数位数,默认为2,多于两个操作数时,中间计算过程传0，最后一步计算时传值
 * @return $data 计算后的数据
 */
function xydiv($a,$b,$digit=2)
{
	if(!$digit)
	{
		$data = $a/$b;
	}else
	{
		$data = round(($a/$b),$digit);
	}
	return $data;
}

/**
 * 浮点型数据取模
 * @param mix $a 左操作数
 * @param mix $b 右操作数
 * @param int $digit 要保留的小数位数,默认为2,多于两个操作数时,中间计算过程传0，最后一步计算时传值
 * @return $data 计算后的数据
 */
function xymod($a,$b,$digit=2)
{
	if(!$digit)
	{
		$data = $a%$b;
	}else
	{
		$data = round($a%$b,$digit);
	}
	return $data;
}

/**
 * 判断浮点数大小
 * @param mix $a 左操作数
 * @param mix $b 右操作数
 * @param int $digit 设置用来做比较的小数位数,如果不传默认为2
 * @return bool $res 比较的结果 0:相等 1:$a>$b -1:$a<$b
 */
function xycomp($a,$b,$digit=2)
{
	if(!$digit)
		return bccomp($a,$b);
	else
		return bccomp($a,$b,$digit);
}






/**
 * 生成全局唯一标识符
 *
 * @author co8bit <me@co8bit.com>
 * @date    2016-10-22
 */
function guid()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}



/**
 * 查找特殊子串
 * 
 * eg.如从'开通-金卡会员'中找出是什么类型的会员，即找到关键字：`金卡`，可以这么做：$title = findSubStr($queryData['bill_title'],'-','会员');
 *
 * @param string $text 总文本
 * @param string $stIdentifier 开始处的标识符
 * @param string $endIdentifier 结束处的标识符
 *
 * @return string 找到的子串
 * 
 * @author co8bit <me@co8bit.com>
 * @version 1.2
 * @date    2016-11-08
 */
function findSubStr($text,$stIdentifier,$endIdentifier)
{
	$st     = strpos($text,$stIdentifier) + strlen($stIdentifier);
	$end    = strpos($text,$endIdentifier);
	$substr = substr($text,$st,$end-$st);
	return $substr;
}




/**
 * 加载配置文件 支持格式转换 仅支持一级配置
 * @param string $file 配置文件名
 * @param string $parse 配置解析方法 有些格式需要用户自己解析
 * @return array
 */
function loadConfigFile($file,$parse=CONF_PARSE)
{
    $ext  = pathinfo($file,PATHINFO_EXTENSION);
    switch($ext){
        case 'php':
        {
        	// throw new \XYException(__METHOD__,-22);
            return require $file;
        }
        case 'ini':
            return parse_ini_file($file);
        case 'yaml':
            return yaml_parse_file($file);
        case 'xml':
            return (array)simplexml_load_file($file);
        case 'json':
            return json_decode(file_get_contents($file), true);
        default:
            if(function_exists($parse)){
                return $parse($file);
            }else{
                E(L('_NOT_SUPPORT_').':'.$ext);
            }
    }
}



/**
 * 读取业务逻辑配置文件
 * @return  array 配置文件
 *
 * @author co8bit <me@co8bit.com>
 * @version 1.2
 * @date    2016-11-08
 */
function loadConfig($methodName = null)
{
	// echo 'st:'.$methodName.'======>'.'loadConfig()<br>';
	return loadConfigFile(APP_PATH . '../config/config.php');
}
/**
 * 读取本地配置文件
 * @return  array 配置文件
 *
 * @author DizzyLee
 * @version 1.2
 * @date    2017-06-07
 */
function loadLocalConfig($methodName = null)
{
	// echo 'st:'.$methodName.'======>'.'loadConfig()<br>';
	return loadConfigFile(APP_PATH . '../config/localconfig.php');
}



/**
 * 读取key配置文件
 * @return  array 配置文件
 *
 * @author co8bit <me@co8bit.com>
 * @version 1.2
 * @date    2016-11-08
 */
function loadConfig_key($methodName = null)
{
	// echo 'st:'.$methodName.'======>'.'loadConfig()<br>';
	return loadConfigFile(APP_PATH . '../config/key.php');
}



/**
 * 获得admin_uid
 * @param unsigned_int $uid 要查adminUid的uid
 * @return unsigned_int admin_uid
 *
 * @todo 如果账户被删除了，并不能实时更新session，会出错。需要更严格的检查
 */
function getAdminUid($uid = null)
{
	if ($uid === null)
		return intval(session('user_auth.admin_uid'));
	else
	{
		$tmp = M('User')->where(array('uid'=>$uid))->getField('admin_uid');
		if (empty($tmp))
			throw new \XYException(__METHOD__,-8505);
		return intval($tmp);
	}
}
function getRpg()
{
    return intval(session('user_auth.rpg'));
}



/**
 * 获得用户uid
 * @return unsigned_int admin_uid
 */
function getUid()
{
	return intval(session('user_auth.uid'));
}


































//+=================================================================================================
//+=================================================================================================
//+======================================以下函数未使用=============================================
//+=================================================================================================
//+=================================================================================================





/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string 
 */
function think_ucenter_encrypt($data, $key, $expire = 0) {
	$key  = md5($key);
	$data = base64_encode($data);
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char =  '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x=0;
		$char  .= substr($key, $x, 1);
		$x++;
	}
	$str = sprintf('%010d', $expire ? $expire + time() : 0);
	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
	}
	return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 * @return string 
 */
function think_ucenter_decrypt($data, $key){
	$key    = md5($key);
	$x      = 0;
	$data   = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data   = substr($data, 10);
	if($expire > 0 && $expire < time()) {
		return '';
	}
	$len  = strlen($data);
	$l    = strlen($key);
	$char = $str = '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char  .= substr($key, $x, 1);
		$x++;
	}
	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		}else{
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}
