<?php
namespace Home\Controller;
use Think\Controller;

/**
 * 后台控制台.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class IndexController extends BaseController
{
    /**
     * 初始化
     * @internal
     * @return  不是debug模式会被重定向
     *
     * @author co8bit <me@co8bit.com>
     * @version 0.2
     * @date    2016-05-24
     */
    protected function _initialize()
    {
        parent::_initialize();

        try//在debug状态下才可以使用
        {
            if (APP_DEBUG !== true)
               $this->_empty();
            $auth = new \Think\Auth();
            if ( !$auth->check(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME,getUid()) )
               throw new \XYException(__METHOD__,-300);
        }catch(\XYException $e)

        {
            $this->jsonErrorReturn($e->getCode());
        }

        // parent::_initialize();
        header("Content-Type:text/html; charset=utf-8");
    }


	/**
	 * 首页，测试用
     * @internal
	 */
    public function index()
    {
        $this->show('<a href="'.U("Install/index").'">Install</a><br>');
        $this->show('<a href="'.U("Generator/index").'">Generator</a><br>');
        $this->show('<a href="'.U("Index/watchLog").'">查看今日log</a>');
        $this->show('&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<a href="'.U("Index/deleteLog").'">删除今日log</a><br>');
        $this->display();
        echo 'uid:'.session("user_auth.uid")." || username:".session("user_auth.username")." || name:".session("user_auth.name")." || admin_uid:".session("user_auth.admin_uid")."<br>";
        // $this->show('<a href="'.U("Update/index").'">Update</a><br>');
        $this->show('<a href="'.U("Util/index").'">Util</a><br>');
        $this->show('<a href="'.U("Config/index").'">Config</a><br>');
        $this->show('<a href="'.U("Login/index").'">Login</a><br>');
        $this->show('<a href="'.U("User/index").'">User</a><br>');
        $this->show('<a href="'.U("Good/index").'">Good</a><br>');
        $this->show('<a href="'.U("Company/index").'">Company</a><br>');
        $this->show('<a href="'.U("Contact/index").'">Contact</a><br>');
        $this->show('<a href="'.U("Phonenum/index").'">Phonenum</a><br>');
        $this->show('<a href="'.U("Carlicense/index").'">Carlicense</a><br>');
        $this->show('<a href="'.U("Parkaddress/index").'">Parkaddress</a><br>');
        $this->show('<a href="'.U("Order/index").'">Order</a><br>');
        $this->show('<a href="'.U("Warehouse/index").'">Warehouse</a><br>');
        $this->show('<a href="'.U("Finance/index").'">Finance</a><br>');
        $this->show('<a href="'.U("Query/index").'">Query</a><br>');
        $this->show('<a href="'.U("EverydaySummarySheet/index").'">EverydaySummarySheet</a><br>');
        $this->show('<a href="'.U("Feedback/index").'">Feedback</a><br>');
        $this->show('<a href="'.U("Auth/index").'">Auth</a><br>');
        $this->show('<a href="'.U("Paybill/index").'">Paybill</a><br>');
        $this->show('<a href="'.U("UserAccount/index").'">UserAccount</a><br>');
        $this->show('<a href="'.U("SmsDetails/index").'">SmsDetails</a><br>');
        $this->show('<a href="'.U("PaymentDetails/index").'">PaymentDetails</a><br>');
        $this->show('<a href="'.U("PrintTemplate/index").'">PrintTemplate</a><br>');
        $this->show('<a href="'.U("Other/index").'">Other</a><br>');
        $this->show('<a href="'.U("Storage/index").'">Storage</a><br>');
        $this->show('<a href="'.U("StatementOfAccount/index").'">StatementOfAccount</a><br>');
        $this->show('<a href="'.U("Account/index").'">Account</a><br>');
        $this->show('<a href="'.U("SkuStorage/index").'">SkuStorage</a><br>');
        $this->show('<a href="'.U("FaceRec/index").'">FaceRec</a><br>');
        $this->show('<a href="'.U("QnUpload/index").'">QnUpload</a><br>');
    }




    /**
     * 看log
     * @internal
     */
    public function watchLog()
    {
        $inputData = I('param.');
        if (I('param.type') == 1)
            $date = date('m_d',strtotime("-1 day"));
        elseif (I('param.type') == 2)
            $date = date('m_d',strtotime("-2 day"));
        elseif (empty($inputData['m']) || empty($inputData['d']))
            $date = date('m_d');
        else
            $date = $inputData['m'].'_'.$inputData['d'];

        $log = file_get_contents(RUNTIME_PATH .'Logs/Home/17_'.$date.'.log');

        //日志信息可视化：
        $log = str_replace("\r", "<br>", $log);//将文件换行变成网页换行

        $log = str_replace("#0 ", " #0<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#1 ", " #1<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#2 ", " #2<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#3 ", " #3<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#4 ", " #4<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#5 ", " #5<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#6 ", " #6<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#7 ", " #7<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#8 ", " #8<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#9 ", " #9<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#10 ", " #10<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#11 ", " #11<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#12 ", " #12<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#13 ", " #13<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#14 ", " #14<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#15 ", " #15<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#16 ", " #16<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#17 ", " #17<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#18 ", " #18<br>", $log);//异常抛出的调试信息换行
        $log = str_replace("#19 ", " #19<br>", $log);//异常抛出的调试信息换行

        $offset = 0;
        $totalAnchor = 0;
        $title = null;
        $timeContent = null;
        $TITLE_START = '/index.php?m=Home&c=';

        while (true)
        {
            $st = strpos($log,'==================================================START==================================================',$offset);
            if ($st > 0)
            {
                $time_st = strpos($log,'[ '.date('Y').'-',$st);
                $time_end = strpos($log,' ]',$time_st);
                $timeContent[] = substr($log,$time_st,$time_end-$time_st).']';

                $title_st = strpos($log,$TITLE_START,$st)+strlen($TITLE_START)-2;
                $title_end = strpos($log,'<br>',$title_st);
                $title[] = substr($log,$title_st,$title_end-$title_st);

                $totalAnchor++;
                $replaceString = null;
                $replaceString = '<div id="'.$totalAnchor.'"></div>';
                $log = substr_replace($log,$replaceString,$st,0);
                $offset = $st + strlen($replaceString)+2;
            }
            else
                break;
        }

        $log = str_replace("PARAM:", "<font color='#FF66FF'><b>PARAM:</b></font>", $log);//PARAM字体加浅蓝
        $log = str_replace("INFO: 最后的返回", "<font color='#FF66FF'><b>PARAM:</b></font>最后的返回", $log);//PARAM字体加浅蓝
        $log = str_replace("INFO: 错误的中文意思", "<font color='#FF66FF'><b>PARAM:</b></font>错误的中文意思", $log);//PARAM字体加浅蓝
        $log = str_replace("WORK:", "<font color='#5cb85c'><b>WORK:</b></font>", $log);//WORK字体加绿
        $log = str_replace("INFO:", "<font color='#5bc0de'><b>INFO:</b></font>", $log);//INFO字体加浅蓝

        $log = str_replace("EMERG:", "<font style='color:#FFFFFF; font-weight:bold;background: #FF0000;'>EMERG:</font>", $log);//严重错误，字体加红
        // $log = str_replace("EMERG:", "<font style='color:#FFFFFF; font-weight:bold;background: #FF0000;'>EMERG:", $log);//服务器后端检查日志时使用，严重错误，会把所有该标记后面的内容加红
        $log = str_replace("ALERT:", "<font color='#FF0000'><b>ALERT:</b></font>", $log);//字体加红
        $log = str_replace("CRIT:", "<font color='#FF0000'><b>CRIT:</b></font>", $log);//字体加红
        $log = str_replace("ERR:", "<font color='#FF0000'><b>ERR:</b></font>", $log);//字体加红
        $log = str_replace("WARN:", "<font color='#FF0000'><b>WARN:</b></font>", $log);//字体加红
        $log = str_replace("NOTICE:", "<font color='#FF0000'><b>NOTICE:</b></font>", $log);//字体加红
        $log = str_replace("NOTIC:", "<font color='#FF0000'><b>NOTIC:</b></font>", $log);//字体加红

        $log = str_replace("DEBUG:", "<font color='#337ab7'><b>DEBUG:</b></font>", $log);//DEBUG字体加深蓝
        $log = str_replace("======>", "<font style='color:#FFFFFF; font-weight:bold;background: #337ab7;'>======></font>", $log);//======>字体加深蓝

        $log = str_replace("SQL:", "<font color='#f0ad4e'><b>SQL:</b></font>", $log);//SQL字体变黄


        $log = str_replace('========================================END========================================', '========================================END========================================<div><a href="#goToTop">返回最上</a></div>', $log);//SQL字体变黄

        echo '<h1 id="goToTop">这里是'.$date.'日的日志</h1>';
        echo '<a href="#goToBottom">到最后</a><br><a href="'.U("Index/index").'">返回Index</a><br></h2>';
        echo '<h1>========================================</h1>';

        for ($i = $totalAnchor; $i >= 1; $i--)
        {
            if ($i < 10)
                echo "<a href='#$i'> 0$i  :  ".$timeContent[$i-1].' ---- '.$title[$i-1].'</a><br>';
            else
                echo "<a href='#$i'>$i  :  ".$timeContent[$i-1].' ---- '.$title[$i-1].'</a><br>';
        }
        echo '<h1>========================================</h1>';
        echo htmlspecialchars_decode($log);
        echo '<a href="#goToTop">返回最上</a>';
        echo '<h2><a id="goToBottom" href="'.U("Index/index").'">返回Index</a><br></h2>';
    }



    /**
     * 删除log
     * @internal
     */
    public function deleteLog()
    {
        $date = date('m_d');
        echo \Think\Storage::unlink(RUNTIME_PATH .'Logs/Home/17_'.$date.'.log','F');
        echo '<br><a href="'.U("Index/index").'">返回Index</a><br>';
    }


}