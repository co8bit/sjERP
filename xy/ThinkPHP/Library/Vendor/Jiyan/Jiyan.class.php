<?php
use \Home\Model\BaseadvModel;

require_once(dirname(__FILE__) . '/lib/class.geetestlib.php');



class Jiyan extends BaseadvModel
{
    protected $config = null;
    protected $JyInstance = null;//极验库实例

    public function __construct()
    {
        $this->_config = loadConfig_key(__METHOD__);
        
        $this->JyInstance = new GeetestLib($this->_config['JIYAN_CAPTCHA_ID'],$this->_config['JIYAN_PRIVATE_KEY']);
    }




    /**
     * 得到极验的token.
     *
     * 未做输入检查
     *
     * @param string $user_id 用户识别符
     *
     * @return array 成功，返回极验下发的token。
     *         session 写入session array=>jiyan,
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.13
     * @date    2016-08-25
     */
    public function getJiyanToken($user_id)
    {
        if (empty($user_id))
            throw new \XYException(__METHOD__,-12004);

        $user_id = think_ucenter_md5($user_id,$this->_config['JIYAN_MD5_KEY']);
        $status  = $this->JyInstance->pre_process($user_id);
        
        session('jiyan',array('jiyanserver'=>$status,'userid'=>$user_id));
        return $this->JyInstance->get_response();
    }



    /**
     * 向极验服务器查询用户是否输对验证码（极验的验证结果）.
     *
     * 未做输入检查
     * @param mixed|null $data POST的数据
     *
     * @param string $geetest_challenge 验证事件流水号
     * @param string $geetest_validate 
     * @param string $geetest_seccode 
     *
     * @return bool true-用户输对了验证码.false-用户输错了验证码
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.13
     * @date    2016-08-29
     */
    public function checkJiyanVerifyCode(array $data = null)
    {
        $userId = session('jiyan.userid');
        $jiyanServerStatus = session('jiyan.jiyanserver');

        if ($jiyanServerStatus == 1)//服务器正常
        {
            $result = $this->JyInstance->success_validate($data['geetest_challenge'], $data['geetest_validate'], $data['geetest_seccode'], $userId);
            if ($result)
                return true;
            else
                return false;
        }
        else//服务器宕机,走failback模式
        {
            $result = $this->JyInstance->fail_validate($data['geetest_challenge'],$data['geetest_validate'],$data['geetest_seccode']);
            if ($result)
                return true;
            else
                return false;
        }
    }
}