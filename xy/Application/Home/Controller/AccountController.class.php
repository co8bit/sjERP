<?php
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/6/8
 * Time: 下午2:06
 */
namespace Home\Controller;
use Think\Controller;

class AccountController extends HomeController
{
    public function createAccount()
    {
        try
        {
            $this->jsonReturn(D("Account")->createAccount(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function query_Account_Source()
    {
        try
        {
            $this->jsonReturn(D("Account")->query_Account_Source(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function query_Account()
    {
        try
        {
            $this->jsonReturn(D("Account")->query_Account(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function delete_Account()
    {
        try
        {
            $this->jsonReturn(D("Account")->delete_Account(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }

    public function cash_Proposal()
    {
        try
        {
            $this->jsonReturn(D("Account")->cash_Proposal(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
    public function transfer_Accounts()
    {
        try
        {
            $this->jsonReturn(D("Account")->transfer_Accounts(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
    public function edit_Account()
    {
        try
        {
            $this->jsonReturn(D("Account")->edit_Account(I('param.')));
        }catch(\XYException $e)
        {
            $this->jsonErrorReturn($e->getCode());
        }
    }
}
