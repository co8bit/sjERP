<?php
use \Home\Model\BaseadvModel;

require_once(dirname(__FILE__) . '/' . 'IGt.Push.php');


class GeTui extends BaseadvModel
{
    // const HOST = 'https://api.getui.com/apiex.htm';
    //define('HOST','http://sdk.open.api.igexin.com/apiex.htm');//http的域名
    //define('HOST','https://api.getui.com/apiex.htm');//https的域名

    const APPKEY       = 'tqJIHoNTaX8AybfbNYZ1C1';
    const APPID        = 'PT7CP85fCe9IlwWmh2usO2';
    const MASTERSECRET = '48u1rznGVS9kSvBOB870U';

    // define('APPKEY','tqJIHoNTaX8AybfbNYZ1C1');
    // define('APPID','PT7CP85fCe9IlwWmh2usO2');
    // define('MASTERSECRET','48u1rznGVS9kSvBOB870U');
    // define('DEVICETOKEN','');
    // define('Alias','请输入别名');



    protected $igt = null;//个推提供的官方sdk实例

    public function __construct()
    {
        $this->igt = new IGeTui(NULL,self::APPKEY,self::MASTERSECRET,true);
    }



    /**
     * 订单推送接口
     * @param   string     $ClientID 个推ClientID
     * @param   string     $content  要发送的内容
     * 
     * @return  1
     * @throws \XYException
     * 
     * @author co8bit <me@co8bit.com>
     * @version 0.4
     * @date    2016-06-27
     */
    public function pushOrderMsg($ClientID = null,$content = '')
    {
        //消息模版：
        // $template = IGtNotificationTemplateDemo();//点击通知打开应用模板
        // $template = IGtLinkTemplateDemo();//点击通知打开网页模板
        // $template = IGtNotyPopLoadTemplateDemo();//点击通知弹窗下载模板
        $template = $this->AndroidTransmissionTemplate($content);//透传消息模版

        //定义"SingleMessage"
        $message = new IGtSingleMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(24*3600*1000);//离线时间,单位为毫秒
        $message->set_data($template);//设置推送消息类型
        $message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，2为4G/3G/2G，1为wifi推送，0为不限制推送
        
        //接收方
        $target = new IGtTarget();
        $target->set_appId(self::APPID);
        $target->set_clientId($ClientID);
        //$target->set_alias(Alias);

        try
        {
            $rep = $this->igt->pushMessageToSingle($message,$target);
            if ($rep['result'] !== 'ok')
            {
                // log_('$rep',$rep,$this);
                throw new \XYException(__METHOD__.'result:'.$rep['result']
                    // .'taskId:'.$rep['taskId']
                    // .'status:'.$rep['status']
                    ,-1700);
            }
            else
            {
                // log_('GT:',
                //     'result:'.$rep['result']
                //     .'<br>taskId:'.$rep['taskId']
                //     .'<br>status:'.$rep['status']
                //     ,$this,'INFO');
                \Think\Log::write('GT:'.'======>'.
                    'result:'.$rep['result']
                    .'<br>taskId:'.$rep['taskId']
                    .'<br>status:'.$rep['status']
                    ,'INFO');
                return 1;
            }
        }catch(RequestException $e)
        {
            //失败时重发
            // $requstId =e.getRequestId();
            // $rep = $this->igt->pushMessageToSingle($message,$target,$requstId);




            // log_('$e',$e,$this);
            \Think\Log::write('GT:$e'.'======>'.
                    dump($e,false)
                    ,'INFO');
            throw new \XYException(__METHOD__,-1701);
        }
    }


    /**
     * 安卓模板
     * @param   string     $content
     *
     * @return IGtTransmissionTemplate $template
     * 
     * @author co8bit <me@co8bit.com>
     * @version 0.4
     * @date    2016-06-27
     */
    public function AndroidTransmissionTemplate($content = '')
    {
        $template =  new IGtTransmissionTemplate();
        $template->set_appId(self::APPID);//应用appid
        $template->set_appkey(self::APPKEY);//应用appkey
        $template->set_transmissionType(2);//透传消息类型,    收到消息是否立即启动应用，1为立即启动，2则广播等待客户端自启动
        
        $template->set_transmissionContent($content);//透传内容.2048中/英文字符
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        

        //APN简单推送
        // $template = new IGtAPNTemplate();
        // $apn = new IGtAPNPayload();
        // $alertmsg=new SimpleAlertMsg();
        // $alertmsg->alertMsg="";
        // $apn->alertMsg=$alertmsg;
        // // $apn->badge=2;
        // // $apn->sound="";
        // $apn->add_customMsg("payload","payload");
        // $apn->contentAvailable=1;
        // $apn->category="ACTIONABLE";
        // $template->set_apnInfo($apn);
        // $message = new IGtSingleMessage();

        //APN高级推送
        // $apn = new IGtAPNPayload();
        // $alertmsg=new DictionaryAlertMsg();
        // $alertmsg->body="body";
        // $alertmsg->actionLocKey="ActionLockey";
        // $alertmsg->locKey="LocKey";
        // $alertmsg->locArgs=array("locargs");
        // $alertmsg->launchImage="launchimage";
        // //IOS8.2 支持
        // $alertmsg->title="Title";
        // $alertmsg->titleLocKey="TitleLocKey";
        // $alertmsg->titleLocArgs=array("TitleLocArg");

        // $apn->alertMsg=$alertmsg;
        // $apn->badge=7;
        // $apn->sound="";
        // $apn->add_customMsg("payload","payload");
        // $apn->contentAvailable=1;
        // $apn->category="ACTIONABLE";
        // $template->set_apnInfo($apn);

        return $template;
    }
}