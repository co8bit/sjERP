<?php
namespace Home\Behaviors;

class modelendBehavior extends \Think\Behavior
{
    //行为执行入口
    public function run(&$param)
    {
    	echo 'end:'.'<br>';
    	// \Think\Log::record('<font color="#5cb85c"><b>==============================MODEL START==============================</b></font>','WORK');
    }
}