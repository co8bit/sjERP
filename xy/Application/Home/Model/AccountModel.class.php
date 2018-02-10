<?php
/**
 * Created by PhpStorm.
 * User: dizzylee
 * Date: 2017/6/8
 * Time: 下午12:52
 */
namespace Home\Model;
use Think\Model;

class AccountModel extends BaseadvModel
{

    protected $_validate = array(
        array('account_creator','0,100', -24001, self::MUST_VALIDATE, 'length',self::AccountModel_createAccount), //开户人姓名不合法
        array('account_number', '0,100', -24002, self::MUST_VALIDATE, 'length',self::AccountModel_createAccount), //账号长度不合法
        array('account_name', '1,20', -24003, self::MUST_VALIDATE,'length',self::AccountModel_createAccount),//账户名不合法
        array('account_balance', '0,20', -24005, self::MUST_VALIDATE, 'length',self::AccountModel_createAccount), //账户余额不合法
        array('qcode', '0,20', -24006, self::EXISTS_VALIDATE, 'length',self::AccountModel_createAccount), //速查码不合法
        array('account_remark', '0,200', -24007, self::EXISTS_VALIDATE, 'length',self::AccountModel_createAccount), //账户备注不合法
        array('bank_name', '0,40', -24009, self::EXISTS_VALIDATE,'length',self::AccountModel_createAccount),//开户行不合法
        array('account_source_type', '0,2', -24008, self::EXISTS_VALIDATE, 'length',self::AccountModel_createAccount), //来源种类不合法
        array('account_source_name', '0,20', -24011, self::MUST_VALIDATE, 'length',self::AccountModel_createAccount), //来源名不合法
        array('account_prefix', '0,10', -24005, self::EXISTS_VALIDATE, 'length',self::AccountModel_createAccount), //来源前缀不合法
        array('province', '1,20', -24012, self::EXISTS_VALIDATE, 'length',self::AccountModel_createAccount), //省不合法
        array('city', '1,20', -24013, self::EXISTS_VALIDATE, 'length',self::AccountModel_createAccount), //市不合法
        //操作验证
        array('account_id','1,20',-24014,self::MUST_VALIDATE,'length',self::AccountModel_cash_Proposal),//账户ID不合法
        array('query_balance', '0,20', -24005, self::MUST_VALIDATE, 'length',self::AccountModel_cash_Proposal), //账户余额不合法
        array('cost', '0,20', -24015, self::MUST_VALIDATE, 'length',self::AccountModel_cash_Proposal), //交易金额不合法
        //编辑账户验证
        array('account_id','1,20',-24014,self::MUST_VALIDATE,'length',self::AccountModel_edit_Account),//账户ID不合法
        array('account_creator','0,100', -24001, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //开户人姓名不合法
        array('account_number', '0,100', -24002, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //账号长度不合法
        array('account_name', '1,20', -24003, self::EXISTS_VALIDATE,'length',self::AccountModel_edit_Account),//账户名不合法
        array('account_balance', '0,20', -24005, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //账户余额不合法
        array('qcode', '0,20', -24006, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //速查码不合法
        array('account_remark', '0,200', -24007, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //账户备注不合法
        array('bank_name', '0,40', -24009, self::EXISTS_VALIDATE,'length',self::AccountModel_edit_Account),//开户行不合法
        array('account_source_type', '0,2', -24008, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //来源种类不合法
        array('account_source_name', '0,20', -24011, self::MUST_VALIDATE, 'length',self::AccountModel_edit_Account), //来源名不合法 的

        array('province', '0,20', -24012, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //省不合法
        array('city', '0,20', -24013, self::EXISTS_VALIDATE, 'length',self::AccountModel_edit_Account), //市不合法

        array('page', '1,20', -24020, self::MUST_VALIDATE, 'length',self::AccountModel_query_account), //分页参数不合法
        array('pline', '1,20', -24020, self::MUST_VALIDATE, 'length',self::AccountModel_query_account), //分页参数不合法
    );

    /**创建新的账户
     * 这里虽然要用到多张表操作，
     * 但其中并不存在强关联，
     * 且需要用到查询为null的情况，
     * 即便set_New_Source运行失败
     * 也要保证创建成功。
     * @param $account_creator 开户名
     * @param $account_number 账号
     * @param $account_name 账户名
     * @param $account_source_type 账户来源类型 1.银行账户，2.网络账户，3.现金账户
     * @param $account_source_name 账户来源名
     * @param $account_balance 账户预设余额
     * @param $bank_name 开户行名
     * @param $province 省
     * @param $city 市
     * @param $qcode 速查码
     * @param $account_remark 备注'
     *
     * @return 1 成功时返回 1;
     * @author DizzyLee<728394036@qq.com>
     */
    public function createAccount($data = null,$isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans) $this->startTrans();
            $data['reg_time'] = date('y-m-d h:m:s');
            $data['update_time'] = date('y-m-d h:m:s');
            $data['admin_uid'] = getAdminUid();
            if (!$this->field('account_creator,account_number,account_name,admin_uid,account_source_type,account_source_name,account_balance,bank_name,province,city,qcode,account_remark,reg_time,update_time')->create($data, self::AccountModel_createAccount)) {
                throw new \XYException(__METHOD__, $this->getError());
            }
            if (empty($this->qcode)) {
                $this->qcode = D('Other')->getPinYin($this->account_name);
                unset($py);
            }
            $this->status = 1;
            $account_source_type = $this->account_source_type;
            if ($account_source_type ==1 || $account_source_type == 2)
            {
                $account_source_name = $this->account_source_name;
                $account_number = $this->account_number;
            }
            if ($account_source_type == 2 || $account_source_type == 3)
            {
                $this->province = '缺省';
                $this->city ='缺省';
            }
            if ($account_source_type == 2)
                $this->account_source_name = '网络';
            if ($account_source_type == 3)
                $this->account_source_name = '现金';
            $this->sn = $this->getNextSn('ASN');
            $insert = $this->add();
            if ($insert === false || $insert ===null)
                throw new \XYException(__METHOD__,-8000);
            if ($account_source_type == 1) {
                $account_prefix = (int)mb_substr($account_number, 0, 6, 'utf-8');
                $this->set_New_Source($account_prefix, $account_source_name, $account_source_type);
            }
            if ($insert > 0)
                if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                return $insert;
        }
        catch(\XYException $e)
        {
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
            $e->rethrows();
        }
        catch (\Think\Exception $e)
        {
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
            throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
        }

    }

    /**设置新的账户来源，该方法在用户无法找到账户来源选择自己填写的时候调用
     * @param $account_prefix 识别码
     * @param $account_source_name 来源名
     * @param $account_source_type 账户来源种类
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    protected function set_New_Source($account_prefix = null,$account_source_name = null,$account_source_type = null)
    {
        $account_source = M('account_source');
        $query = $account_source->where(array('account_prefix' => $account_prefix))->find();
        if ($query === false)
            throw new \XYException(__METHOD__,-8000);
        if ($query === null)
        {
            $operate = $account_source->data(array('account_prefix' => $account_prefix,'account_source_name' => $account_source_name,'account_source_type' => $account_source_type))->add();
            if ($operate === false||$operate === null)
                throw new \XYException(__METHOD__,-8000);
           //当用户选择自己输入，而数据库中已存在该来源时，也就是$operate == 0时,不做任何操作
        }
    }

    /**编辑账户信息接口
     * @param $account_id 账户ID
     * @param $account_name 账户名
     * @param $bank_name 开户行
     * @param $province 省
     * @param $account_balance 账户预设余额
     * @param $city 市
     * @param $account_number 账号
     * @param $account_source_name 账户来源名
     * @param $account_creator 开户名
     * @param $account_remark
     * @param $qcode
     * @param $status
     * @return 1
     * @author DizzyLee<728394036@qq.com>
     */
    public function edit_Account($data = null)
    {
        if (!$this->field('account_creator,account_remark,qcode,account_source_name,bank_name,account_name,account_id,province,city,status,account_number,account_balance')->create($data,self::AccountModel_edit_Account))
            throw new \XYException(__METHOD__, $this->getError());
        $change = $this->save();
        if ($change === false || $change === null)
            throw new \XYException(__METHOD__,-8000);
        if (empty($data['account_prefix'])&&$this->account_source_type == 1) {
            $account_prefix = (int)mb_substr($this->account_number, 0, 6, 'utf-8');
            $this->set_New_Source($account_prefix, $this->account_source_name, $this->account_source_type);
        }
        return 1;
    }

    /**
     * 删除账户接口
     * @param $account_id
     * @return string
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */

    public function delete_Account($data = null)
    {
        if (!$this->field('account_id')->create($data,self::AccountModel_delete_Account))
            throw new \XYException(__METHOD__,$this->getError());
        $this->admin_uid = getAdminUid();
        $map['admin_uid'] =  $this->admin_uid;
        $map['account_id'] = $this->account_id;
        $account = D('Account')->where($map)->find();
        if ($account ===false || $account === null)
            throw new \XYException(__METHOD__,-8000);
        if ($account['account_source_type'] == 3)
            throw new \XYException(__METHOD__,-24023);
        $this->account_name = $account['account_name'].C('DELETED_MARKER');
        $this->status = -1;
        $change = $this->save();
        if ($change === false)
            throw new \XYException(__METHOD__,-8000);
        if ($change == 0)
            throw new \XYException(__METHOD__,-24021);
        return '删除成功';
    }

    /**查询账户来源列表
     * @param account_type 账户类型
     * @throws \XYException
     * @return 返回所有银行/网络账户来源
     * @author DizzyLee<728394036@qq.com>
     */
    public function query_Account_Source($data = null)
    {
        $query = M('account_source')->field('account_source_id,account_prefix','account_source_name')->where(array('account_source_type'=>$data['account_source_type']))->select();
        if ($query === false)
            throw new \XYException(__METHOD__,-8000);
        //如果数据为空，表示类表为空，暂无数据
        return $query;
    }

    /**查询账户接口
     * @return $query 返回所有账户信息
     * @param  $page 当前页码/必填
     * @param $type  1.查询全部启用或者未启用账户 2.只查询启用账户
     * @param  $pline 每页多少行/必填
     * @param  $account_source_type 账户来源类型 1.银行账户 2.网络账户 3现金账户
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function query_Account($data = null)
    {
        if (!isUnsignedInt($data['page'])||!isUnsignedInt($data['pline']))
            throw new \XYException(__METHOD__,-24020);
        $this->field('page,pline,account_source_type')->create($data,self::AccountModel_query_account);
        $condition['admin_uid'] = getAdminUid();
        if ($data['type'] == 1 || empty($data['type']))
        {
            $condition['status'] = array('in',array(0,1));
        }
        elseif ($data['type'] == 2)
        {
            $condition['status'] = array('in',array(1));
        }
        $condition['_logic'] = 'AND';
        if (!empty($this->account_source_type))
        {
            $condition['account_source_type'] = $this->account_source_type;
        }
        $totalpage = $this->where($condition)->count()/$this->pline;
        if ($this->page > $totalpage || $this->page == 0)
            $this->page = 1;
        $query = $this->where($condition)->page($this->page,$this->pline)->select();
        if ($query === false)
            throw new \XYException(__METHOD__,-8000);
        if ($query === null)
        {
            return '数据暂时为空';
        }
        return $query;
    }
    /**收入支出提现接口
     * @param $account_id 账户ID
     * @param $account_number 账号
     * @param $cost 交易金额
     * @param $account_operation_class  操作类型 1.收入 2.支出 3.提现 4.转账
     * @param $account_operation_remark 操作备注
     * @return 1
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function cash_Proposal($data = null,$isAlreadyStartTrans = false)
    {
        try {
            $account = new AccountModel();
            if (!$isAlreadyStartTrans) $account->startTrans();
            $cost = xyround($data['cost'], 4);
            $account_id = $data['account_id'];
            if (empty($data['account_operation_remark']))
                $data['account_operation_remark'] = '缺省';
            //更新账户数据
            $query_data = $account->lock(true)->field('account_balance')->where(array('account_id' => $account_id))->find();
            $new_data = array();
            if ($data['account_operation_class'] == 2 || $data['account_operation_class'] == 3)
                {
                    $new_data['account_balance'] =xysub($query_data['account_balance'],$cost,4);
                }

            elseif($data['account_operation_class'] == 1)
                $new_data['account_balance'] =xyadd($query_data['account_balance'],$cost,4);
            else
                throw new \XYException(__METHOD__,-24019);

            $new_data['update_time'] = date('y-m-d h:m:s');
            $result = $account->lock(true)->where(array('account_id' => $account_id))->save($new_data);
            if ($result === false || $result === null)
                throw new \XYException(__METHOD__,-8000);

        if ($result)
        {
            unset($new_data);
            unset($query_data);
            //增加操作记录
            $operate_data = array();
            $operate_data['account_id'] = $account_id;
            $operate_data['admin_uid'] = getAdminUid();
            $operate_data['account_number'] = $data['account_number'];
            $operate_data['account_operation_class'] = $data['account_operation_class'] ;
            $operate_data['cost'] = $cost;
            $operate_data['account_operation_code'] = $this->getNextSn('AOC');
            $operate_data['reg_time'] = date('y-m-d h:m:s');
            $operate_data['update_time'] = date('y-m-d h:m:s');
            $operate_data['account_operation_remark'] = $data['account_operation_remark'];
            $operate = M('account_operation_record')->lock(true)->add($operate_data);
            if ($operate === false||$operate === null)
                throw new \XYException(__METHOD__,-24017);
            if (!$operate)
            {
                throw new \XYException(__METHOD__,-24018);
            }
            unset($account);
                if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                return 1;

        }
        }catch (\XYException $e)
        {
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
            $e->rethrows();
        }
    }


    /**转账接口
     * @param $account_id 本账户ID
     * @param $target_id 目标账户的ID
     * @param $account_operation_class 操作类型 1.收入 2.支出 3.提现 4.转账
     * @param $cost 交易金额
     *@author DizzyLee<728394036@qq.com>
     */
    public function transfer_Accounts($data = null,$isAlreadyStartTrans = false)
    {
        try{
            $account = new AccountModel();
            if (!$isAlreadyStartTrans) $account->startTrans();
            $cost = xyround($data['cost'],4);
            $account_id = $data['account_id'];
            $target_id = $data['target_id'];
            if (empty($data['account_operation_remark']))
                $data['account_operation_remark'] = '缺省';

            //从当前账户转出
            $query_data_self = $account->lock(true)->field('account_balance')->where(array('account_id' => $account_id))->find();
            $new_data_self['account_balance'] = xysub($query_data_self['account_balance'],$cost,4);
            $new_data_self['update_time'] = date('y-m-d h:m:s');
            $result_self = $account->lock(true)->where(array('account_id' => $account_id))->save($new_data_self);
            if ($result_self === false || $result_self === null)
                throw new \XYException(__METHOD__,-8000);
            //转入目标账户
            $query_data_target = $account->lock(true)->field('account_balance')->where(array('account_id' => $target_id))->find();
            $new_data_target['account_balance'] = xyadd($query_data_target['account_balance'],$cost,4);
            $new_data_target['update_time'] = date('y-m-d h:m:s');
            $result_target = $account->lock(true)->where(array('account_id' => $target_id))->save($new_data_target);
            if ($result_self === false || $result_self === null)
                throw new \XYException(__METHOD__,-8000);

            if ($result_self&&$result_target)
            {
                unset($new_data_target);
                unset($new_data_self);
                unset($query_data_target);
                unset($query_balance_self);
                //增加转账操作记录
                $operate_data = array();
                $operate_data['account_id'] = $account_id;
                $operate_data['admin_uid'] = getAdminUid();
                $operate_data['account_number'] = $data['account_number'];
                $operate_data['account_operation_class'] = 4;
                $operate_data['cost'] = $cost;
                $operate_data['account_operation_code'] = $this->getNextSn('AOC');
                $operate_data['reg_time'] = date('y-m-d h:m:s');
                $operate_data['update_time'] = date('y-m-d h:m:s');
                $operate_data['account_operation_remark'] = $data['account_operation_remark'];
                $operate = M('account_operation_record')->add($operate_data);
            }
            if ($operate)
            {
                if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                return 1;
            }
        }catch (\XYException $e)
        {
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
            $e->rethrows();
        }
    }

}