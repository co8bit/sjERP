<?php
namespace Home\Behaviors;

class modelbeginBehavior extends \Think\Behavior
{
    //行为执行入口
    public function run(&$param)
    {
    	echo 'begin:'.'<br>';
    	// \Think\Log::record('<font color="#5cb85c"><b>==============================MODEL START==============================</b></font>','WORK');
    }
}