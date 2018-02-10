<?php
namespace Home\Behaviors;

class appstartBehavior extends \Think\Behavior
{
    //行为执行入口
    public function run(&$param)
    {
    	// \Think\Log::write('<font color="#5cb85c"><b>==================================================START==================================================</b></font>','WORK');
     //    \Think\Log::write('服务器接收到的参数：'.dump(I("param."),false),'INFO');
    }
}