<?php
use \Home\Model\BaseadvModel;

require_once(dirname(__FILE__) . '/' . 'taobaoSDK/TopSdk.php');


class Sms extends BaseadvModel
{
    protected $topClient = null;//topClient实例

    public function __construct()
    {
        $this->topClient            = new TopClient;
        $this->topClient->appkey    = C('ALiDaYu_APPKEY');
        $this->topClient->secretKey = C('ALiDaYu_SECRET');
    }




    /**
     * 给单一用户发送验证码
     * 
     * note:内部没有验证参数是否正确
     * 
     * @internal server
     * 
     * @param string $phone 电话号码
     * @param string $verifyCode 要发送的验证码
     *
     * @return 1 成功
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.13
     * @date    2016-08-25
     */
    public function sendSingleVerifyCode($type,$phone,$verifyCode)
    {
        if ($type == 1)
            $sign = '注册验证';
        elseif ($type == 2)
            $sign = '登录验证';
        elseif ($type == 3)
            $sign = '变更验证';
        elseif ($type == 1001)
            $sign = '星云进销存验证';
        elseif ($type == 4)
            $sign = '注册验证';

        if ($type == 1)
        {
            $req = new AlibabaAliqinFcSmsNumSendRequest;
            // $req->setExtend("123456");
            $req->setSmsType("normal");
            $req->setSmsFreeSignName($sign);//签名
            $req->setSmsParam('{"code":"' . $verifyCode . '","product":"【星云进销存】的"}');//短信内容
            $req->setRecNum($phone);//设置手机号
            $req->setSmsTemplateCode("SMS_13202228");//短信模板id
            $resp = $this->topClient->execute($req);
        }
        elseif ($type == 3)
        {
            $req = new AlibabaAliqinFcSmsNumSendRequest;
            // $req->setExtend("123456");
            $req->setSmsType("normal");
            $req->setSmsFreeSignName($sign);//签名
            $req->setSmsParam('{"code":"' . $verifyCode . '","product":"【星云进销存】的"}');//短信内容
            $req->setRecNum($phone);//设置手机号
            $req->setSmsTemplateCode("SMS_13202226");//短信模板id
            $resp = $this->topClient->execute($req);
        }
        elseif ($type == 1001) {
            $req = new AlibabaAliqinFcSmsNumSendRequest;
            // $req->setExtend("123456");
            $req->setSmsType("normal");
            $req->setSmsFreeSignName($sign);//签名
            $req->setSmsParam('{"code":"' . $verifyCode . '","product":"免费100天"}');//短信内容
            $req->setRecNum($phone);//设置手机号
            $req->setSmsTemplateCode("SMS_65570023");//短信模板id
            $resp = $this->topClient->execute($req);
        }

        if ($resp->result->success)
            return 1;
        else
        {
            try
            {
                $emailBody =dump($resp,false).'<br>'.C('HOST_NAME')
                    .'
                    <br><br><br><br><br>
                    详情请看：<a href="http://www.xingyunbooks.com/server">http://www.xingyunbooks.com/server</a>';

                D('Util')->sendMail(
                        C('EMERG_MAIL_LIST'),
                        '【崩溃级BUG】短息发送失败',
                        $emailBody
                    );
                log_("【崩溃级BUG】短息发送失败",$resp,$this);
            }
            catch(\XYException $e)
            {
                log_("邮件发送失败：【崩溃级BUG】短息发送失败",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
            }



            throw new \XYException(__METHOD__,-12501);
        }
    }




    /**
     * 给单一或一群用户发送对账单
     *
     * note:传入号码为11位手机号码，不能加0或+86。群发短信需传入多个号码，以英文逗号分隔，一次调用最多传入200个号码。
     * 
     * note:内部没有验证参数是否正确
     * 
     * @internal server
     *
     * @param string $phoneArray 电话字符串
     * @param string $dateSt 格式化后的开始日期
     * @param string $dateEnd 格式化后的结束日期
     * @param string $money 金钱及配套说明
     * @param string $content 账单详细信息
     * 
     * @return 1 成功
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.2
     * @date    2016-08-25
     */
    public function sendStatementOfAccount($phoneArrayString,$dateSt,$dateEnd,$money,$content)
    {
        
        $data                   = null;
        $data['dateSt']         = $dateSt;
        $data['dateEnd']        = $dateEnd;
        $data['money']          = $money;
        $data['orderInfoArray'] = $content;

        $jsonContent = json_encode($data);
        if (empty($jsonContent))
            throw new \XYException(__METHOD__,-12511);


        //发送
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        // $req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName('星云进销存对账单');//签名，修改此id需要去SmsModel::SendSMSStatementOfAccount()修改模板内容
        $req->setSmsParam($jsonContent);//内容
        $req->setRecNum($phoneArrayString);//设置手机号
        $req->setSmsTemplateCode("SMS_58390202");//短信模板id，修改此id需要去SmsModel::SendSMSStatementOfAccount()修改模板内容
        $resp = $this->topClient->execute($req);

        if ($resp->result->success)
            return 1;
        else
        {
            try
            {
                $emailBody =dump($resp,false).'
                    <br><br><br><br><br>
                    详情请看：<a href="http://www.xingyunbooks.com/server">http://www.xingyunbooks.com/server</a>';

                D('Util')->sendMail(
                        C('EMERG_MAIL_LIST'),
                        '【崩溃级BUG】短息对账单发送失败',
                        $emailBody
                    );
                log_("【崩溃级BUG】短息对账单发送失败",$resp,$this);
            }
            catch(\XYException $e)
            {
                log_("邮件发送失败：【崩溃级BUG】短息对账单发送失败",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
            }

            throw new \XYException(__METHOD__,-12510);
        }
    }




    /**
     * 注册成功欢迎短信.
     * 给用户注册成功后发送注册成功提醒短信
     *
     * note:传入号码为11位手机号码，不能加0或+86。
     * 
     * note:内部没有验证参数是否正确
     * 
     * @internal server
     *
     * @param string $phoneString 电话字符串
     * @param string $username 用户名
     * @param string $password 密码
     * 
     * @return 1 成功
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.9
     */
    public function sendSuccessAdminRegister($phoneString,$username,$password)
    {
        $data             = null;
        $data['username'] = $username;
        $data['password'] = $password;

        $jsonContent = json_encode($data);
        if (empty($jsonContent))
            throw new \XYException(__METHOD__,-12511);


        //发送
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        // $req->setExtend("123456");
        $req->setSmsType("normal");
        $req->setSmsFreeSignName('星云进销存');//签名
        $req->setSmsParam($jsonContent);//内容
        $req->setRecNum($phoneString);//设置手机号
        $req->setSmsTemplateCode("SMS_66970009");//短信模板id
        $resp = $this->topClient->execute($req);

        if ($resp->result->success)
            return 1;
        else
        {
            try
            {
                $emailBody =dump($resp,false).'
                    <br><br><br><br><br>
                    详情请看：<a href="http://www.xingyunbooks.com/server">http://www.xingyunbooks.com/server</a>';

                D('Util')->sendMail(
                        C('EMERG_MAIL_LIST'),
                        '【崩溃级BUG】注册成功欢迎短信发送失败',
                        $emailBody
                    );
                log_("【崩溃级BUG】注册成功欢迎短信发送失败",$resp,$this);
            }
            catch(\XYException $e)
            {
                log_("邮件发送失败：【崩溃级BUG】注册成功欢迎短信发送失败",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
            }

            throw new \XYException(__METHOD__,-12512);
        }
    }
}