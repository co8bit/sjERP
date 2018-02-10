<?php
namespace Home\Model;
use Think\Model;

/**
 * SPU Model
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class SpuModel extends GoodModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('spu_id', 'checkDeny_spu_id', -5050, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//spu_id不合法
		array('spu_name', '1,50', -5051, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //spu_name长度不合法
		array('spu_index', 'isUnsignedInt', -5052, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//spu_index不合法
		array('qcode', '1,50', -5053, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//qcode长度不合法
		array('cat_id', 'checkDeny_depend_cat_id', -5054, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),//cat_id不合法
		array('status', 'checkDeny_bool_status', -5011, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH), //status不合法

		//SpuModel_createSPU
		array('spu_name', '1,50', -5051, self::MUST_VALIDATE, 'length',self::SpuModel_createSPU), //spu_name长度不合法
		array('spu_name', 'checkDeny_depend_spuName', -5056, self::MUST_VALIDATE, 'callback',self::SpuModel_createSPU), //spu_name重名
		array('spu_index', 'isUnsignedInt', -5052, self::MUST_VALIDATE, 'function',self::SpuModel_createSPU),//spu_index不合法
		array('qcode', '1,50', -5053, self::MUST_VALIDATE, 'length',self::SpuModel_createSPU),//qcode长度不合法
		array('cat_id', 'checkDeny_depend_cat_id', -5054, self::MUST_VALIDATE, 'callback',self::SpuModel_createSPU),//cat_id不合法
		array('status', 'checkDeny_bool_status', -5011, self::MUST_VALIDATE, 'callback',self::SpuModel_createSPU), //status不合法

		//SpuModel_editSPU
		array('spu_id', 'checkDeny_spu_id', -5050, self::MUST_VALIDATE, 'callback',self::SpuModel_editSPU),//spu_id不合法
		array('spu_name', 'checkDeny_depend_spuName', -5056, self::EXISTS_VALIDATE, 'callback',self::SpuModel_editSPU), //spu_name重名
		array('spu_name', '1,50', -5051, self::EXISTS_VALIDATE, 'length',self::SpuModel_editSPU), //spu_name长度不合法
		array('spu_index', 'isUnsignedInt', -5052, self::MUST_VALIDATE, 'function',self::SpuModel_editSPU),//spu_index不合法
		array('qcode', '1,50', -5053, self::MUST_VALIDATE, 'length',self::SpuModel_editSPU),//qcode长度不合法
		array('cat_id', 'checkDeny_depend_cat_id', -5054, self::MUST_VALIDATE, 'callback',self::SpuModel_editSPU),//cat_id不合法
		array('status', 'checkDeny_bool_status', -5011, self::MUST_VALIDATE, 'callback',self::SpuModel_editSPU), //status不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid

		//SpuModel_createSPU
		array('admin_uid','getAdminUid',self::SpuModel_createSPU,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::SpuModel_createSPU),

		//SpuModel_editSPU
		array('admin_uid','getAdminUid',self::SpuModel_editSPU,'function'),//填入所属创建者uid
	);



	/**
	 * 创建商品(会附带一条规格,创建公司内账相关项目时，需要多传一个参数spu_class = 1)
	 * 
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param $spu_class 商品类别来源 0.进销存 1.公司内账一起记
	 * @param string $spu_name 商品名称
	 * @param unsigned_int $spu_index 显示顺序，一个正整数，数字越小，显示越靠前，1是最前
	 * @param string $qcode 速查码
	 * @param unsigned_int $cat_id 类别
	 * @param enum $status 0|1 状态
	 * 
	 * @return unsigned_int 成功-spu_id > 0
	 * @throws \XYException
	 */
	public function createSPU(array $data = null,$isAlreadyStartTrans = false)
	{
		try {
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
            if (!isset($data['spu_class']))
            {
                $data['spu_class'] = 0;
            }

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);

			if (!$this->field('spu_name,spu_index,qcode,cat_id,status,spu_class')->create($data,self::SpuModel_createSPU))
				throw new \XYException(__METHOD__,$this->getError());
			log_("this->data",$this->data,$this);
			$tmp = $this->add();
			log_("tmp",$tmp,$this);
			if ($tmp > 0)
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $tmp;
			}
			else
				throw new \XYException(__METHOD__,-5000);
		}
		catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
	}



	/**
	 * 编辑商品信息(SPU).
	 * 
	 * 注意，这里需要同步更改sku表里的spu冗余信息
	 * @note 没有任何实质性修改返回1
	 * 
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $spu_id
	 * @param string $spu_name 商品名称
	 * @param unsigned_int $spu_index 显示顺序，一个正整数，数字越小，显示越靠前，1是最前
	 * @param string $qcode 速查码
	 * @param unsigned_int $cat_id 类别
	 * @param enum $spu_status 0|1 状态
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function editSPU(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);

			$queryData = M('Spu')->where(array('spu_id'=>intval($data['spu_id']),'admin_uid'=>getAdminUid()))->find();
			if ($queryData['spu_name'] == $data['spu_name']) unset($data['spu_name']);

			$data['spu_index'] = intval($data['spu_index']);
			$data['status']    = intval($data['spu_status']);

			if (!$this->field('spu_id,spu_name,spu_index,qcode,cat_id,status')->create($data,self::SpuModel_editSPU))
				throw new \XYException(__METHOD__,$this->getError());

			$spu_id  = $this->spu_id;

			$catInfo = D('Cat')->get_(array('cat_id'=>$this->cat_id),1);

			$tmpData = array(
					'spu_index'  => $this->spu_index,
					'qcode'      => $this->qcode,
					'spu_status' => $this->status,
					'admin_uid'  => $this->admin_uid,
					'cat_id'     => $this->cat_id,
					'cat_name'   => $catInfo['cat_name'],
					'status'     => $this->status,
					);
			if (isset($data['spu_name']))
				$tmpData['spu_name'] = $this->spu_name;

			$tmp = $this->where(array('spu_id'=>$this->spu_id,'admin_uid'=>$this->admin_uid))->save();
			if ( ($tmp === null) || ($tmp === false) )
				throw new \XYException(__METHOD__,-5000);
			
			//成功，更改Sku表里的字段
			if ($tmp === 0)//如果什么都没改，则返回修改成功
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return 1;
			}

			$tmp = null;
			$tmp = D("Sku")->where(array('spu_id'=>$spu_id,'admin_uid'=>$tmpData['admin_uid']))->save($tmpData);
			if ( ($tmp === null) || ($tmp === false) )
				throw new \XYException(__METHOD__,-5000);

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			return 1;
			
		}catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
	}



	/**
	 * 删除SPU
	 *
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $spu_id
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteSPU(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);


			if (!$this->field('spu_id')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());

			//当该spu还有sku的时候不能被删除
			$tmpCount = M('Sku')->where(array('spu_id'=>$this->spu_id,'admin_uid'=>$this->admin_uid))->find();
			if ($tmpCount === false)
				throw new \XYException(__METHOD__,-5000);
			elseif (!empty($tmpCount))
				throw new \XYException(__METHOD__,-5503);

			/*v1.4.1:sku可以随时删除
			//spu已经发生业务往来不能被删除
			$isHasContacts = D('SkuBill')->isHasContacts($this->spu_id,'spu');
			if ($isHasContacts)
				throw new \XYException(__METHOD__,-5504);
			*/
			$queryData = $this->where(array('spu_id'=>$this->spu_id,'admin_uid'=>$this->admin_uid))->find();
			$deleteSPUData = array(
				"spu_id"=>$this->spu_id,
				'status' => -1,
				'spu_name' => $queryData['spu_name'].C('DELETED_MARKER')
			);
			$tmp = $this->save($deleteSPUData);//TODO:目前这里是靠数据库的外键删除掉SKU表里的相关东西的
			if ( empty($tmp) )
				throw new \XYException(__METHOD__,-5000);

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			return 1;
		}
		catch(\XYException $e)
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			$e->rethrows();
		}catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
		{
			if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
			throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
		}
		
	}




	/**
	 * 查询店铺SPU
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned $type 模式 1-所有SPU 2-有效SPU，即cat_status=1&spu_status=1的SPU
	 * 
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 */
	public function querySPU(array $data = null)
	{
        if (!isset($data['spu_class']))
        {
            $data['spu_class'] = 0;
        }
		switch (intval($data['type']))
		{
			case '1':{
				$tmpRe = D('Spu')->where(array('spu.admin_uid'=>getAdminUid(),'spu.status'=>array('in',array(0,1)),'cat.status'=>array('in',array(0,1))))->join('cat ON cat.cat_id = spu.cat_id')->order('spu_index,cat_index,spu.cat_id,spu_id')->field('spu_id,spu.cat_id,spu_name,spu_index,qcode,spu.status,spu.reg_time,cat_name,cat_index')->select();
				break;
			}
			case '2':{
				$tmpRe = D('Spu')->where(array('spu.admin_uid'=>getAdminUid(),'spu.status'=>1,'cat.status'=>1,'spu_class' => $data['spu_class']))->join('cat ON cat.cat_id = spu.cat_id')->order('spu_index,cat_index,spu.cat_id,spu_id')->field('spu_id,spu.cat_id,spu_name,spu_index,qcode,spu.status,spu.reg_time,cat_name,cat_index')->select();
				break;
			}
			default:
				throw new \XYException(__METHOD__,-5015);
		}

		if ($tmpRe === false)
			throw new \XYException(__METHOD__,-5000);

		return $tmpRe;
	}

}
