<?php
namespace Home\Model;
use Think\Model;

/**
 * sku_storage 关联表.
 * 用途：查询某个sku在某个storage那里的成本、库存等信息.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class SkuStorageModel extends BaseadvModel
{
	protected $_validated = array(

		//SkuStorageModel_updateSkuStorage
		array('sku_id','isUnsignedInt',-25001,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('sto_id','isUnsignedInt',-25002,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('admin_id','isUnsignedInt',-25003,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('unit_price','isNonegativeReal',-25004,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('stock','isNonegativeReal',-25005,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('reg_time','isUnsignedInt',-25006,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('update_time','isUnsignedInt',-25007,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('sku_storage_id','isUnsignedInt',-25008,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('sku_sto_index','isUnsignedInt',-25009,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),
		array('status','checkDeny_bool_status',-25010,self::MUST_VALIDATE,'function',self::SkuStorageModel_updateSkuStorage),

		//SkuStorageModel_get_
		array('sku_id','isUnsignedInt',-25001,self::MUST_VALIDATE,'function',self::SkuStorageModel_get_),
		array('sto_id','isUnsignedInt',-25002,self::MUST_VALIDATE,'function',self::SkuStorageModel_get_),

		//SkuStorageModel_querySkuSto
		array('sto_id','isUnsignedInt',-25002,self::MUST_VALIDATE,'function',self::SkuStorageModel_querySkuSto),

		//SkuStorageModel_skuStoSummary
		array('sto_id','isUnsignedInt',-25002,self::MUST_VALIDATE,'function',self::SkuStorageModel_querySkuSto),

		//SkuStorageModel_deleteSkuSto
		array('sto_storage_id','isUnsignedInt',-25008,self::MUST_VALIDATE,'function',self::SkuStorageModel_deleteSkuSto),

	);

	/* 自动完成 */
	protected $_auto = array(
	array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),
		);


	/**
	 * 生成/编辑Sku与Storage关联表数据
	 * - @internal
	 * - @param mixed|null $data POST的数据
	 * - @param unsigned_int $sku_id 
	 * - @param unsigned_int $sto_id 
	 * - @param unsigned_int $admin_uid 
	 * - @param double $unit_price
	 * - @param double $stock
	 * - @param unsigned $reg_time
	 * - @param unsigned $update_time
	 * - @return 1 ok
	 * - @throws \XYException
	 * - @author wtt <wtt@xingyunbooks.com>
	 * - @version 1.11
	 * - @date    2017-06-10
	 */
	public function updateSkuSto(array $data = null)
	{
		log_("data",$data,$this);
		if (!$this->field('sku_storage_id,sto_id,sku_id,unit_price,stock,reg_time,update_time,sku_sto_index,sku_sto_status')->create($data,self::SkuStorageModel_updateSkuStorage))
			throw new \XYException(__METHOD__,$this->getError());
		log_("this->data",$this->data,$this);


		if(empty($this->sku_storage_id)) // 结果为空，新增数据
		{
			$tmpReturn = $this->add();
			if($tmpReturn > 0)
				return 1;
			else
				throw new \XYException(__METHOD__,-25000);
		}
		else // 结果不为空，修改数据
		{
			$tmpReturn = $this->save();
			if($tmpReturn >=0)
				return 1;
			else
				throw new \XYException(__METHOD__,-25000);
		}
	}

	/**
	 * 得到某个sku在某个仓库中的信息
	 * - @api
	 * - @param mixed|null $data POST的数据
	 * * @param bool $isInUpdateSkuStorage 是否在SkuStorageModel::updateSkuStorage()里调用，如果是，则查询出来为null可以被允许 @internal
	 * - @param unsigned_int $sku_id 
	 * - @param unsigned_int $sto_id 
	 * - @return array $reData 
	 * - @throws \XYException
	 * - @author wtt <wtt@xingyunbooks.com>
	 * - @version 1.11
	 * - @date    2017-06-10
	 */
	public function get_(array $data = null,$isInUpdateSkuStorage = false)
	{
		if (!$this->field('sku_id,sto_id')->create($data,self::SkuStorageModel_get_))
			throw new \XYException(__METHOD__,$this->getError());

		log_("this->data",$this->data,$this);
		$skuInfo = D('Sku')->get_(array('sku_id'=>$this->sku_id));
		$tmp = $this->where(array('sku_id'=>$this->sku_id,'sto_id'=>$this->sto_id))->find();
		$stoData = M('Storage')->getField('sto_id,sto_name');
		if(empty($stoData))
			throw new \XYException(__METHOD__,-25002);
		$tmp['sto_name'] = $stoData[$this->sto_id];
		log_("tmp",$tmp,$this);
		if($tmp === null)
		{
			if($isInUpdateSkuStorage)
				return null;//新增数据时调用，允许返回null???新增数据为什么会调用这个？？
			else
				throw new \XYException(__METHOD__,-25501);
		}	
		elseif($tmp === false)
			throw new \XYException(__METHOD__,-25000);
		$tmp['total_price'] = xymul($tmp['unit_price'],$tmp['stock']);
		$tmp['sn'] = $skuInfo['sn'];
		$tmp['spec_name'] = $skuInfo['spec_name'];
		$tmp['spu_name'] = $skuInfo['spu_name'];
		$tmp['cat_name'] = $skuInfo['cat_name'];
		$tmp['spu_id'] = $skuInfo['spu_id'];
		$reData = null;
		$reData = $tmp;
		return $reData;
	}


	/**
	 * 查询某个仓库SkuSto信息
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned $type 模式 1-所有SKU 2-有效SKU，即sku_sto_status=1的sku
	 * @param unsigned_int $sto_id 仓库id
	 * @param unsigned_int $isLock 是否锁表
	 * 
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 */
	/*public function querySkuSto(array $data = null,$isLock)
	{

		switch (intval($data['type']))
		{
			case '1':{
				$tmpRe = D('SkuStorage')->where(array('admin_uid'=>getAdminUid(),'sto_id'=>$data['sto_id']))->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku_id')->select();
				break;
			}
			case '2':{
				$tmpRe = D('SkuStorage')->where(array('admin_uid'=>getAdminUid(),'status'=>1,'cat_status'=>1,'spu_status'=>1))->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku_id')->select();
				break;
			}
			default:
				throw new \XYException(__METHOD__,-5013);
		}

		if ($tmpRe === false)
			throw new \XYException(__METHOD__,-5000);

		return $tmpRe;
	}*/

	/**
	 * 查询店铺SkuSto
	 * @api
	 * @param unsigned_int $isLock 是否锁表 @internal
	 * @param mixed|null $data POST的数据
	 * @param unsigned $type 模式 1-所有SKU 2-有效SKU，即status=1&&cat_status=1&spu_status=1的SKU
	 * @example data json 
	 * {			
	 * 				"type":1,
	 * 				"sto_id":1 如果全部则传空--查看库存界面,开单界面必须传
	 * }
	 *
	 * 
	 * @example data mini:	{"stoData":{"sto_id":[1,2]}}
	 * 
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 * @author wtt <wtt@xingyunbooks.com>
	 * @data 2017-07-01
	 */
	public function querySkuSto(array $data = null,$isLock = false)
	{
		if (!$this->field('sto_id')->create($data,self::SkuStorageModel_querySkuSto))
			throw new \XYException(__METHOD__,$this->getError());

		if(!empty($this->sto_id)) //选择了仓库的
		{
			switch (intval($data['type']))
			{
				case '1':{
					$tmpRe = D('Sku')->join('RIGHT JOIN sku_storage ON sku.sku_id=sku_storage.sku_id')->where(array('sku_storage.admin_uid'=>getAdminUid(),'sto_id'=>$this->sto_id))->lock($isLock)->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku_storage.sku_id,sku_storage.stock,sku_storage.unit_price')->select();
					log_("_sql",$this->_sql(),$this);
					break;
				}
				case '2':{
					$tmpRe = D('Sku')->join('RIGHT JOIN sku_storage ON sku.sku_id=sku_storage.sku_id')->where(array('sku_storage.admin_uid'=>getAdminUid(),'sto_id'=>$this->sto_id,'status'=>1,'cat_status'=>1,'spu_status'=>1))->lock($isLock)->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku_storage.sku_id,sku_storage.stock,sku_storage.unit_price')->select();
					break;
				}
				default:
					throw new \XYException(__METHOD__,-23005);
			}
		}else
		{
			switch (intval($data['type']))
			{
				case '1':{
					$tmpRe = D('Sku')->join('RIGHT JOIN sku_storage ON sku.sku_id=sku_storage.sku_id')->where(array('sku_storage.admin_uid'=>getAdminUid()))->lock($isLock)->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku_storage.sku_id,sku_storage.stock,sku_storage.unit_price')->select();
					break;
				}
				case '2':{
					$tmpRe = D('Sku')->join('RIGHT JOIN sku_storage ON sku.sku_id=sku_storage.sku_id')->where(array('sku_storage.admin_uid'=>getAdminUid(),'status'=>1,'cat_status'=>1,'spu_status'=>1))->lock($isLock)->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku_storage.sku_id,sku_storage.stock,sku_storage.unit_price')->select();
					break;
				}
				default:
					throw new \XYException(__METHOD__,-23005);
			}
		}
		if ($tmpRe === false)
			throw new \XYException(__METHOD__,-5000);

		$stoData = M('Storage')->getField('sto_id,sto_name');
		if (empty($stoData))
			throw new \XYException(__METHOD__,-25002);
		$reData = null;
		$reData['totalStock'] = 0;
		$reData['totalValue'] = 0;
		if( $tmpRe == NULL) // 如果没有数据 赋值为空数组
		{
			$reData['data'] = array();
		}
		else //如果有数据，处理数据
		{
			$tmpReData = null;
			if(empty($this->sto_id)) //没有选仓库,数据需要合并
			{
				foreach($tmpRe as $key=>$value)
				{
					if(!isset($tmpReData[$value['sku_id']]))
					{
						//初始化
						$tmpReData[$value['sku_id']]['sku_id'] = 0;
						$tmpReData[$value['sku_id']]['sn'] = '';
						$tmpReData[$value['sku_id']]['spu_name'] = '';
						$tmpReData[$value['sku_id']]['spec_name'] = '';
						$tmpReData[$value['sku_id']]['cat_id'] = '';
						$tmpReData[$value['sku_id']]['cat_name'] = '';
						$tmpReData[$value['sku_id']]['stock'] = 0 ; 
						// $tmpReData[value['sku_id']]['unit'] = 0 ;
						$tmpReData[$value['sku_id']]['status'] = 0 ;
						$tmpReData[$value['sku_id']]['total_price'] = 0;
						//赋值
						$tmpReData[$value['sku_id']]['sku_id'] = $value['sku_id'];
						$tmpReData[$value['sku_id']]['sn'] = $value['sn'];
						$tmpReData[$value['sku_id']]['spu_name'] = $value['spu_name'];
						$tmpReData[$value['sku_id']]['spec_name'] = $value['spec_name'];
						$tmpReData[$value['sku_id']]['cat_name'] = $value['cat_name'];
						$tmpReData[$value['sku_id']]['cat_id'] = $value['cat_id'];
					}
					$tmpReData[$value['sku_id']]['stock'] += $value['stock'] ;
					$tmpReData[$value['sku_id']]['total_price'] += $value['stock']*$value['unit_price'] ;
					if($value['status'] == 1)
						$tmpReData[$value['sku_id']]['status'] = 1 ; 
				}
				log_("tmpReData",$tmpReData,$this);
				foreach($tmpReData as $key=>$value)
				{
					$reData['data'][] = $value;
					$reData['totalStock'] += $value['stock'];
					$reData['totalValue'] += $value['total_price'];
				}

			}else
			{
				foreach($tmpRe as $key=>$value)
				{
					$tmpRe[$key]['sto_name'] = $stoData[$value['sto_id']];
					$tmpRe[$key]['total_price'] = xymul($value['unit_price'],$value['stock']); 
					$reData['totalStock'] += $value['stock'];
					$reData['totalValue'] += xymul($value['unit_price'],$value['stock']);
				}
				$reData['data'] = $tmpRe;
			}
		}
		return $reData;
	}

	/**
	 * 删除指定的skusto信息
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $sku_storage_id 
	 * @return '1' 成功
	 * @throws \XYException
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-06-20
	 */

	public function deleteSkuSto(array $data=null,$isAlreadyStartTrans=false)
	{
		try{
			log_("data",$data,$this);
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if($shopLockStatus['status'] ===1 )
				throw new \XYException(__METHOD__,-10507);

			if(!$this->field('sku_storage_id')->create($data,self::SkuStorageModel_deleteSkuSto))
				throw new \XYException(__METHOD__,$this->getError());
			$delData = array(
				"sku_storage_id"=>$this->sku_storage_id,
				'sku_sto_status' => -1,
				);
			$tmp = $this->lock(true)->save($delData);
			if(empty($tmp))
				throw new \XYException(__METHOD__,-25000);
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


}

