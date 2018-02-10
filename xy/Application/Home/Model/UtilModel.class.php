<?php
namespace Home\Model;
use Think\Model;
use Think\Think;
use Think\Verify;

/**
 * 工具类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UtilModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//MODEL_SINGLE_SMS
		array('type', 'check_UtilModel_MODEL_SINGLE_SMS_type', -12001, 
			self::MUST_VALIDATE, 'callback',self::MODEL_SINGLE_SMS),//type不合法
		array('mobile', 'checkMobile', -12002,
			self::MUST_VALIDATE, 'callback',self::MODEL_SINGLE_SMS), //手机格式不正确
		array('mobile', '11,11', -12002,
			self::MUST_VALIDATE, 'length',self::MODEL_SINGLE_SMS), //手机格式不正确

		//MODEL_VERIFY_CODE_CHECK
		array('type', 'check_UtilModel_MODEL_SINGLE_SMS_type', -12001, 
			self::MUST_VALIDATE, 'callback',self::MODEL_VERIFY_CODE_CHECK),//type不合法
		array('verify_code', '4,4', -12003,
			self::MUST_VALIDATE, 'length',self::MODEL_VERIFY_CODE_CHECK), //验证码不合法
        array('mobile', '11,11', -12002,
            self::MUST_VALIDATE, 'length',self::MODEL_VERIFY_CODE_CHECK), //手机格式不正确
	);



	/* 自动完成 */
	protected $_auto = array(
	);


	private $seKey = 'xybooks.comsxbk';//加密密钥
	private $expire = 600;//验证码过期时间（含，s），


	/**
	 * 加密字符串
     * @internal server
	 * @param   string $str 要加密的字符串
	 * 
	 * @return  string 加密后的字符串
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.13
	 * @date    2016-08-25
	 */
    public function encryption($str)
    {
        $key = substr(md5($this->seKey), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }



    /**
     * 生成验证码
     * @param enum $type 要发送的验证码的类别。参考文档的Util的短信type
     * @param string $mobile 对应的手机号
     * 
     * @return string 验证码真实的字符串
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.13
     * @date    2016-08-25
     */
	protected function generatorVerifyCode($type,$mobile)
    {
    	//生成
		$verifyCode                   = rand(1000,9999);
		$key                          = $this->encryption($this->seKey);
		$code                         = $this->encryption($verifyCode);

		//写入session
        $verifyCodeArray                  = array();
        $verifyCodeArray['verify_code']   = $code;//把校验码保存到session
        $verifyCodeArray['verify_time']   = NOW_TIME;//验证码创建时间
        $verifyCodeArray['verify_mobile'] = $mobile;//验证码对应的手机号码
		session('verifyCodeType'.$type.'and'.$key,$verifyCodeArray);


        return $verifyCode;
    }



    /**
     * 验证验证码是否正确
     * @internal server
     * @param mixed|null $data POST的数据
     *
     * @param enum $type 要发送的验证码的类别。参考文档的Util的短信type
     * @param string $verify_code 要检测的用户输入的验证码
     * @param string $mobile 对应的手机号
     *
     * @return 1 成功
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.13
     * @date    2016-08-26
     */
    public function checkVerifyCode(array $data = null)
    {
    	if (!$this->field('type,verify_code,mobile')->create($data,self::MODEL_VERIFY_CODE_CHECK))
			throw new \XYException(__METHOD__,$this->getError());

    	$key = $this->encryption($this->seKey);
        
        $verifyCodeArray = session('verifyCodeType'.$this->type.'and'.$key);


        if (empty($verifyCodeArray))//session里的验证码不存在
        	throw new \XYException(__METHOD__,-12502);

        // session 过期
        if (NOW_TIME - $verifyCodeArray['verify_time'] >= $this->expire)
        {
            session('verifyCodeType'.$this->type.'and'.$key, null);
            throw new \XYException(__METHOD__,-12503);
        }

        if ( $this->encryption($this->verify_code) == $verifyCodeArray['verify_code'] 
            && $this->mobile == $verifyCodeArray['verify_mobile'])
        {
            log_('后端存储的验证码',$verifyCodeArray['verify_code']);
            log_('前端发送过来的验证码',$this->encryption($this->verify_code));
            session('verifyCodeType'.$this->type.'and'.$key, null);
            return 1;
        }
        else
        	throw new \XYException(__METHOD__,-12504);
    }



	/**
	 * 请求发送短信验证码
	 *
	 * @api
	 * @param mixed|null $data POST的数据
     * 
     * @param string $geetest_challenge 验证事件流水号
     * @param string $geetest_validate
     * @param string $geetest_seccode
	 * @param enum $type 要发送的验证码的类别。参考文档的Util的短信type
	 * @param string $mobile 电话号码
     * 
	 * @return 1 成功
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.13
	 * @date    2016-08-25
	 */
	public function requestSMSSendVerifyCode(array $data = null)
	{
        $isSkipver = false;//是否跳过极验验证码，比如搞活动的时候

        $data['type'] = intval($data['type']);

        if (!$this->field('type,mobile')->create($data,self::MODEL_SINGLE_SMS))
            throw new \XYException(__METHOD__,$this->getError());

        if ($this->type == 1)//新注册，手机码查重，只要不是管理员手机都可以注册
        {
            $tmpQueryData = M('User')->where(array('admin_mobile'=>$this->mobile))->find();
            if ($tmpQueryData === false)
                throw new \XYException(__METHOD__,-12508);
            if ($tmpQueryData === null)
                ;
            else
                throw new \XYException(__METHOD__,-12509);
        }elseif ($this->type == 3)//找回密码
        {
            $tmpQueryData = M('User')->where(array('admin_mobile'=>$this->mobile))->find();
            if ($tmpQueryData === false)
                throw new \XYException(__METHOD__,-12000);
            if ($tmpQueryData === null)
                throw new \XYException(__METHOD__,-8027);
            $isSkipver = true;
        }elseif ($this->type == 4)
        {
            $tmpQueryData = M('User')->where(array('admin_mobile'=>$this->mobile))->find();
            if ($tmpQueryData === false)
                throw new \XYException(__METHOD__,-12508);
            if ($tmpQueryData === null)
                ;
            else
                throw new \XYException(__METHOD__,-12509);
            $isSkipver = true;
        }elseif ($this->type == 1001)//免费100天活动
        {
            $tmpQueryData = M('User')->where(array('admin_mobile'=>$this->mobile))->find();
            if ($tmpQueryData === false)
                throw new \XYException(__METHOD__,-12508);
            if ($tmpQueryData === null)
                ;
            else
                throw new \XYException(__METHOD__,-12509);
            $isSkipver = true;
        }

		if (!$isSkipver)
        {
            $result = $this->check_xy_verify_code($data['xy_verify_code']);
            if (!$result)
                throw new \XYException(__METHOD__,-12601);
        }

        import('Vendor.Sms.sms');
        $Sms = new \Sms();
        $sendReturn = $Sms->sendSingleVerifyCode($this->type,$this->mobile,$this->generatorVerifyCode($this->type,$this->mobile));

        if ( ( ($this->type == 1) ||($this->type == 4)|| ($this->type == 1001) ) && ($sendReturn) )
        {
            $reg_ip          = get_client_ip(0,true);
            $reg_time_format = date("Y-m-d H:i:s",NOW_TIME);

            $prefixTitle = '';//标题前缀
            if ($this->type == 1 || ($this->type == 4))
                $prefixTitle = '【新的注册尝试--官网】';
            elseif ($this->type == 1001)
                $prefixTitle = '【新的注册尝试--免费100天】';

            try
            {
                $ipInfo  = D('Util')->getIPInfo($reg_ip);
                $country = $ipInfo['country'];
                $area    = $ipInfo['area'];
                $region  = $ipInfo['region'];
                $city    = $ipInfo['city'];
                $county  = $ipInfo['county'];
                $isp     = $ipInfo['isp'];
            }catch(\XYException $e)
            {
                log_($prefixTitle."得到ip信息出现问题",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
            }

            try
            {
                $emailBody =
                    '<table border="1">
                        <tr>
                            <th>电话</th>
                            <th>注册时间</th>
                            <th>注册ip</th>
                            <th>国家</th>
                            <th>区域</th>
                            <th>省份</th>
                            <th>市</th>
                            <th>县</th>
                            <th>ISP服务商</th>
                        </tr>
                        <tr>' . 
                            '<td>'.$this->mobile.'</td>' . 
                            '<td>'.$reg_time_format.'</td>' . 
                            '<td>'.$reg_ip.'</td>' . 
                            '<td>'.$country.'</td>' . 
                            '<td>'.$area.'</td>' . 
                            '<td>'.$region.'</td>' . 
                            '<td>'.$city.'</td>' . 
                            '<td>'.$county.'</td>' . 
                            '<td>'.$isp.'</td>' . 
                        '</tr>
                    </table>
                    <br><br><br><br><br>';

                session('request_sms_send_verify_code_email_body',$emailBody);

                D('Util')->sendMail(
                        C('ADMIN_REGISTER_MAIL_LIST'),
                        $prefixTitle.'有新人注册尝试',
                        $emailBody
                    );
            }
            catch(\XYException $e)
            {
                log_('邮件发送失败：'.$prefixTitle.'有新人注册尝试',$e->getCode(),__METHOD__,\Think\LOG::EMERG);
            }
        }

        return 1;
	}



    /**
     * 通过验证码的session，得到给用户下发验证码的手机号
     * 
     * @param enum $type 短信类型。3-找回密码
     * 
     * @return string admin_mobile 手机号
     * 
     * @throws \XYException
     */
    public function getVerifyCodeAdminMobile($type)
    {
	    $key = $this->encryption($this->seKey);
	    $tmp = session('verifyCodeType'.$type.'and'.$key);
	    return $tmp['verify_mobile'];
    }

	/**
     * 得到极验的token
     * @api
     * 
     * @return array 成功，返回极验下发的token。
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.13
     * @date    2016-08-25
     */
	public function getJiyanToken()
	{
        $uid = getUid();
		if (empty($uid))
			$userid = uniqid('xyjxc',true).rand(10000,99999);
		else
			$userid = getUid();

		import('Vendor.Jiyan.Jiyan');
        $jy = new \Jiyan();
        return $jy->getJiyanToken($userid);
	}



	/**
     * 向极验服务器查询用户是否输对验证码（极验的验证结果）.
     *
     * 因为不写入数据库，所以未作任何输入检查。
     * 
     * @internal server
     * @param mixed|null $data POST的数据
     *
     * @param string $geetest_challenge 验证事件流水号
     * @param string $geetest_validate
     * @param string $geetest_seccode
     *
     * @return bool-true 用户输对了验证码
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.13
     * @date    2016-08-29
     */
    public function checkJiyanVerifyCode(array $data = null)
    {
		import('Vendor.Jiyan.Jiyan');
        $jy = new \Jiyan();
        $re = $jy->checkJiyanVerifyCode(array(
					'geetest_challenge' => $data['geetest_challenge'],
					'geetest_validate'  => $data['geetest_validate'],
					'geetest_seccode'   => $data['geetest_seccode'],
        		));

        if (!$re)
        	throw new \XYException(__METHOD__,-12505);

        return true;
    }



    /**
     * 发送邮件
     * @internal server
     * @param array $toList 收件人列表
     * @param string $subject 标题
     * @param string $body 内容
     *
     * @return 1 成功
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.15
     * @date    2016-09-07
     */
    public function sendMail(array $toList,$subject,$body)
    {
        if (empty($toList))
            throw new \XYException(__METHOD__,-12506);

        require_once APP_PATH.'../vendor/autoload.php';

        $mail = new \PHPMailer;
        // $mail->SMTPDebug = 3;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->CharSet    ='UTF-8';
        $mail->SMTPAuth   = true;                               // Enable SMTP authentication
        $mail->SMTPSecure = 'SSL';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 25;                                    // TCP port to connect to
        $mail->Host       = 'smtp.mxhichina.com';  // Specify main and backup SMTP servers
        $mail->Username   = 'noreply@yueqian.me';                 // SMTP username
        $mail->Password   = 'Vu7ns9rwNhKu';                           // SMTP password
        
        $mail->setFrom('noreply@yueqian.me', '跃迁科技');
        // $mail->addReplyTo('info@example.com', 'Information');
        foreach ($toList as $value)
        {
            $mail->addAddress($value);               // Name is optional
        }
    
        $mail->isHTML(true);                                  // Set email format to HTML
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->Subject = '【'.C('HOST_NAME').'】'.$subject;
        $mail->Body    = $body;
        

        if(!$mail->send())
            throw new \XYException(__METHOD__ .$mail->ErrorInfo,-12507);

        return 1;
    }




    /**
     * 发送邮件，无论什么情况下都不抛出异常
     * @internal server
     * @param array $toList 收件人列表
     * @param string $subject 标题
     * @param string $body 内容
     *
     * @return 1 成功
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.15
     * @date    2016-09-07
     */
    public function sendMailNoThrowException(array $toList,$subject,$body)
    {
        try
        {
            return $this->sendMail($toList,$subject,$body);
        }
        catch(\XYException $e)
        {
            log_("发送邮件出现问题",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
        }
    }



    /**
     * 得到ip地址的中文详细信息.
     * 建议在try中使用
     * @internal server
     * @param string $ip 要查询信息的ip地址
     *
     * @return array ip信息,如下
     *         {
     *               "ip": "210.75.225.254",
     *               "country": "中国",
     *               "area": "华北",
     *               "region": "北京市",
     *               "city": "北京市",
     *               "county": "",//县
     *               "isp": "电信",
     *               "country_id": "86",
     *               "area_id": "100000",
     *               "region_id": "110000",
     *               "city_id": "110000",
     *               "county_id": "-1",
     *               "isp_id": "100017"
     *           }
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.15
     * @date    2016-09-08
     */
    public function getIPInfo($ip)
    {
        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        $ipInfo = json_decode(file_get_contents($url),true);
        if ( ( $ipInfo['code'] == 1 ) || empty($ipInfo) )
            throw new \XYException(__METHOD__,-12507);

        $reData = null;
        $reData['ip'] = I('data.ip/s','','htmlspecialchars',$ipInfo['data']);
        $reData['country'] = I('data.country/s','','htmlspecialchars',$ipInfo['data']);
        $reData['area'] = I('data.area/s','','htmlspecialchars',$ipInfo['data']);
        $reData['region'] = I('data.region/s','','htmlspecialchars',$ipInfo['data']);
        $reData['city'] = I('data.city/s','','htmlspecialchars',$ipInfo['data']);
        $reData['county'] = I('data.county/s','','htmlspecialchars',$ipInfo['data']);
        $reData['isp'] = I('data.isp/s','','htmlspecialchars',$ipInfo['data']);
        $reData['country_id'] = I('data.country_id/s','','htmlspecialchars',$ipInfo['data']);
        $reData['area_id'] = I('data.area_id/s','','htmlspecialchars',$ipInfo['data']);
        $reData['region_id'] = I('data.region_id/s','','htmlspecialchars',$ipInfo['data']);
        $reData['city_id'] = I('data.city_id/s','','htmlspecialchars',$ipInfo['data']);
        $reData['county_id'] = I('data.county_id/s','','htmlspecialchars',$ipInfo['data']);
        $reData['isp_id'] = I('data.isp_id/s','','htmlspecialchars',$ipInfo['data']);

        return $reData;
    }
    public function get_xy_verify_code()
    {
        $config = array(
            'fontSize' => 30,
            'length' => 4,
            'expire' => 600,
            'useNose' => false
        );
        $verify = new Verify($config);
        return $verify->entry();
    }

    public function check_xy_verify_code($xy_verify_code)
    {
        $verify = new Verify();
        $result = $verify->check($xy_verify_code);
        return $result;
    }

}