<?php
require dirname(__FILE__).'/../../index.php';
use Home\Model\AccountModel;
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/7/3
 * Time: 下午7:50
 */
class AccountModelTest extends PHPUnit_Framework_TestCase
{

    public function test()
    {

    }

    public function testCreateAccount()
    {
        $testModel = new AccountModel();
        session('user_auth.admin_uid',1);
        $data_bank = array(
            'account_creator' => '测试',
            'account_number'  => '6228480323061564619',
            'account_name' => '农行卡',
            'account_source_type'  => 1,
            'account_source_name' => '中国农业银行',
            'account_balance' => '10000',
            'bank_name' => '中国农业银行杭州支行',
            'province' => '浙江省',
            'city' => '杭州市',
            'qcode' => 'nhk',
            'account_remark' => '测试正常数据',
        );
        $data_net = array(
            'account_creator' => '测试',
            'account_number'  => '13456331002',
            'account_name' => '支付宝账户',
            'account_source_type'  => 2,
            'account_balance' => '5000',
            'province' => '浙江省',
            'city' => '杭州市',
            'qcode' => 'zfbzh',
            'account_remark' => '测试正常数据',
        );

        $data_cash = array(
            'account_creator' => '测试',
            'account_number'  => '13456331002',
            'account_name' => '现金账户',
            'account_source_type'  => 2,
            'account_balance' => '5000',
            'province' => '浙江省',
            'city' => '杭州市',
            'qcode' => 'xjzh',
            'account_remark' => '测试正常数据',
        );
        $result1 = $testModel->CreateAccount($data_bank,false);
        $result2 = $testModel->CreateAccount($data_net,false);
        $result3 = $testModel->CreateAccount($data_cash,false);
        assert($result1);
        assert($result2);
        assert($result3);
    }
}