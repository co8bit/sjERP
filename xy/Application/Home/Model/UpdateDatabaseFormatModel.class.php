<?php
namespace Home\Model;
use Think\Model;

/**
 * 查询类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UpdateDatabaseFormatModel extends BaseadvModel
{
	/**
	 * 修改数据库remain小数点超过两位的情况,四舍五入
	 * 外部需开启事务
	 * @internal
	 * @param bool $isAlreadyStartTrans 是否已经开启事务 @internal
	 * @return int 成功返回1,不成功返回0
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.10
	 * @date    2016-5-27
	 */
	public function resetRemain($isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);//开启事务
			
			// 实例化Ordermodel
			$dbOrder = D('Order');
			$oid_array = $dbOrder->lock(true)->getField('oid',true);
			foreach ($oid_array as $key => $value)
			{
				$queryData = $dbOrder->where('oid = '.$value)->field('oid,remain')->find();
				if(getLenOfDeciNum($queryData['remain'])>2 )
				{
					$data = array();
					$data['remain'] = xyround($queryData['remain']);
					$res = $dbOrder->where('oid = '.$queryData['oid'])->save($data);
					if($res >0)
					{
						log_("oid",$queryData['oid'],$this);	
					}
					else
						throw new \XYException(__METHOD__,-6000);
				}
			}
			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
		}
		catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}
		catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}	
	}

	/**
	 * 初始化数据库,为每一个店铺生成一个初始仓库
	 * @internal
	 * @param bool $isAlreadyStartTrans 是否已经开启事务 @internal
	 * @return int 成功返回1,不成功返回0
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.10
	 * @date    2017-07-05
	 */
	public function initializeStoSku($isAlreadyStartTrans = false)
	{								 
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			//实例化
			$dbsku = D('Sku');
			$dbsto = D('Storage');
			$dbskusto = D('SkuStorage');
			$dbuser = D('User');
			$config = D('Config');

			//为每一个店铺生成一个初始仓库
			//获取目前所有的店铺创建者admin_uid
			$tmpAdminUid = $dbuser->where('rpg= 1')->getField('admin_uid',true);
			foreach($tmpAdminUid as $key=>$value)
			{
				$stoData[$key]['admin_uid'] = $value;
				$stoData[$key]['sto_name'] = '默认仓库';
				$stoData[$key]['sn'] = 'STO' . '000001';//3+6位的连续编号
				$stoData[$key]['sto_index'] = 1;
				$stoData[$key]['status'] = 1;
				$stoData[$key]['reg_time'] = NOW_TIME;
			}
			$tmpsto = $dbsto->addAll($stoData);
			if($tmpsto >0)
				;
			else
				throw new \XYException(__METHOD__,-23000);

			//更新对应的config表
			$tmpconfig = $config->where(array('admin_uid'=>array('in',$tmpAdminUid)))->setField('STO',1);
			if($tmpconfig >0)
				;
			else
				throw new \XYException(__METHOD__,-10000);

			$stoIdList = $dbsto->getField('admin_uid,sto_id');
			
			// //更新skustorage表
			// $skuData = $dbsku->field('sku_id,admin_uid,unit_price,stock,sku_index,status')->where('sku_class=0')->select();
			// log_("skuData",$skuData,$this);
			// //获取admin_uid对应的sto_id
			// foreach( $skuData as $key=>&$value)
			// {
			// 	$value['sto_id'] = $stoIdList[$value['admin_uid']];
			// 	$value['reg_time'] = NOW_TIME;
			// 	$value['update_time'] = NOW_TIME;
			// 	$value['sku_sto_index'] = $value['sku_index'];
			// 	$value['sku_sto_status'] = $value['status'];
			// 	unset($value['sku_index']);
			// } 
			// log_("skuData",$skuData,$this);
			// $tmpskusto = $dbskusto->addAll($skuData);
			// if($tmpskusto >0)
			// 	;
			// else
			// 	throw new \XYException(__METHOD__,-25000);

			//将原始order表数据和warehouse表数据的sto_id 和 sto_name 改为默认仓库
			$oidList = M('Order')->getField('oid,admin_uid');
			$widList = M('Warehouse')->getField('wid,admin_uid');
			foreach($oidList as $key=>$value)
			{
				$orderUpdate = null;
				$orderUpdate['oid'] = $key;
				$orderUpdate['sto_id'] = $stoIdList[$value];
				$orderUpdate['sto_name'] = '默认仓库';
				$orderRes = M('Order')->save($orderUpdate);
				if( empty($orderRes ) )
					throw new \XYException(__METHOD__,-25000);
			}

			foreach($widList as $key=>$value)
			{
				$warehouseUpdate = null;
				$warehouseUpdate['wid'] = $key;
				$warehouseUpdate['sto_id'] = $stoIdList[$value];
				$warehouseUpdate['sto_name'] = '默认仓库';
				$warehouseRes = M('Warehouse')->save($warehouseUpdate);
				if( empty($warehouseRes ) )
					throw new \XYException(__METHOD__,-25000);
			}

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			return 1;

		}catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}
		catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
	}

    /**
     * 历史操作数据处理
     * @param bool $isAlreadyStartTrans
     * @return int
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function history_trans_form($isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans) $this->startTrans();
            $condition1['oid'] = array('egt',0);
            $condition2['wid'] = array('egt',0);
            $condition3['fid'] = array('egt',0);
            $orderArray = D('Order')->field('oid,history')->where($condition1)->select();
            $warehouseArray = D('Warehouse')->field('wid,history')->where($condition2)->select();
            $financeArray = D('Finance')->field('fid,history')->where($condition3)->select();
            foreach ($orderArray as $key => &$value)
            {
                if (!empty($value['history']))
                {
                    $tmp = unserialize($value['history']);
                    if (!is_array($tmp))
                    {
                        $newhistory = explode(';',$value['history']);
                        array_pop($newhistory);
                        $value['history'] = serialize($newhistory);
                        $operate = M('Order')->where(array('oid'=>$value['oid']))->save($value);
                        if ($operate === null || $operate === false)
                            throw new \XYException(__METHOD__,-8000);
                    }
                }

            }
            foreach ($warehouseArray as $key => &$value)
            {
                if (!empty($value['history']))
                {
                    $tmp = unserialize($value['history']);
                    if (!is_array($tmp))
                    {
                        $newhistory = explode(';',$value['history']);
                        array_pop($newhistory);
                        $value['history'] = serialize($newhistory);
                        $operate = M('Warehouse')->where(array('wid'=>$value['wid']))->save($value);
                        if ($operate === null || $operate === false)
                            throw new \XYException(__METHOD__,-8000);
                    }
                }

            }
            foreach ($financeArray as $key => &$value)
            {
                if (!empty($value['history']))
                {
                    $tmp = unserialize($value['history']);
                    if (!is_array($tmp))
                    {
                        $newhistory = explode(';',$value['history']);
                        array_pop($newhistory);
                        $value['history'] = serialize($newhistory);
                        $operate = M('Finance')->where(array('fid'=>$value['fid']))->save($value);
                        if ($operate === null || $operate === false)
                            throw new \XYException(__METHOD__,-8000);
                    }
                }

            }
            if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
            return 1;
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
}