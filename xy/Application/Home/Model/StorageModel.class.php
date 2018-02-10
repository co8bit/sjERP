<?php
namespace Home\Model;
use Think\Model\AdvModel;

/**
 * 仓库Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误编码:{@see \ErrorCode\ErrorCode()}
 */
class StorageModel extends BaseadvModel
{
	/*数据库字段*/
	// protected $fields = array('sto_id', 'sn', 'sto_name','admin_uid','sto_index','status','remark','reg_time');

	/* 自动验证 */
	protected $_validate = array(
		//StorageModel_updateSto
		array('sto_name', '1,80', -23002, self::MUST_VALIDATE, 'length',self::StorageModel_updateSto), //仓库名称不合法
		array('sto_index', 'isUnsignedInt', -23003, self::MUST_VALIDATE, 'function',self::StorageModel_updateSto), //sto_index不合法
		array('status', 'checkDeny_bool_status', -23004, self::MUST_VALIDATE, 'callback',self::StorageModel_updateSto), //status不合法
		array('sto_id', 'isUnsignedInt', -23001, self::EXISTS_VALIDATE, 'function',self::StorageModel_updateSto),//sto_id不合法
		array('remark', '0,1000', -23006, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //remark长度不合法
		//StorageModel_deleteSto
		array('sto_id', 'isUnsignedInt', -23001, self::EXISTS_VALIDATE, 'function',self::StorageModel_deleteSto),//sto_id不合法

		//StorageModel_get_
		array('sto_id','isUnsignedInt',-23001,self::MUST_VALIDATE,'function',self::StorageModel_get_),

	);
	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::StorageModel_updateSto),
/*		array('update_time', NOW_TIME, self::MODEL_BOTH),*/
	);

	/**
	 * 创建/编辑仓库
	 * @api
	 * @param  bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param mix|null $data POST的数据
	 * @param unsigned int  $sto_id  要修改的仓库id  创建新仓库时不传该参数
	 * @param  $isInstallSql 是否在一键部署时调用 @internal
	 * @param string $sto_name 仓库名称
	 * @param string $remark 备注
	 * @param unsigned_int $sto_index 显示顺序，一个正整数，数字越小，显示越靠前，1是最前，默认为2
	 * @param enum $status 0:不启用|1:启用 状态 
	 * 
	 * @return unsigned_int 成功-sto_id >= 0 允许什么都不改就提交
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-06-07
	 */
	public function updateSto(array $data = null,$isAlreadyStartTrans = false,$isInstallSql = false)
	{
		try{
			//开启事务
			if(!$isAlreadyStartTrans)  $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('config')->getLockShopStatus();
			if($shopLockStatus['status'] == 1)
				throw new \XYException(__METHOD__,-10507);

			$data['status'] = intval($data['status']);
			log_("data",$data,$this);
			if( !$this->field('sto_id,sto_name,sto_index,remark,status')->create($data,self::StorageModel_updateSto) )
				throw new \XYException(__METHOD__,$this->getError());
			$backData = $this->data;//下面用$this的操作会改变$this->data的值
			log_("this->data",$this->data,$this);
			$stoNameList = $this->getStoName($isInstallSql);
			//判断仓库名称是否重复
			if(empty($backData['sto_id']))
			{
				if(in_array($backData['sto_name'], $stoNameList))
					throw new \XYException(__METHOD__,-23002);
			}
			else
			{
				$keys = array_keys($stoNameList,$backData['sto_name']);
				if(!empty($keys))
				{
					if($keys[0] != $backData['sto_id'])
						throw new \XYException(__METHOD__,-23002);
				}
			}

			//如果sto_index为1,则要将原先数据库内的sto_index=1 的数据改为2
			if($this->sto_index == 1)
			{
				$queryData = $this->where(array('admin_uid'=>$this->admin_uid,'sto_index'=>1))->lock(true)->find();
				if(!empty($queryData))//允许查询结果为空
				{
					$re = $this->where('sto_id = '.$queryData['sto_id'])->setField('sto_index',2);
					if(empty($re))
						throw new \XYException(__METHOD__,-23000);
				}
			}
			log_("this->data",$this->data,$this);
			// var_dump($data);
			// var_dump($this->data);
			// var_dump($this->sto_id);
			// var_dump($backData);
			log_("backData",$backData,$this);

			if(empty($backData['sto_id']))
			{
				$backData['sn']= $this->getNextSn('STO');
				$res = $this->add($backData);
				if(empty($res))
					throw new \XYException(__METHOD__,-23000);//添加数据失败
				else
				{
					if(!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return $res;
				}
			}
			else
			{
				//判断该仓库是否存在
				$tmp = $this->get_(array('sto_id'=>$backData['sto_id']));
				if(empty($tmp))
					throw new \XYException(__METHOD__,-23501);
				// var_dump($backData);
				// echo 3333;
				$tmpRe = $this->save($backData);
				// $sql = $this->getLastSql();
				// echo $sql;
				// var_dump($tmpRe);
				if( $tmpRe === null || $tmpRe === false)
					throw new \XYException(__METHOD__,-23000);
				else //没有实质性修改或者修改成功都返回 1
				{
					if(!$isAlreadyStartTrans) $this->commit(__METHOD__);
					return 1;
				}
			}
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
	 * 删除仓库
	 * @api
	 * @param  bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param mix|null $data POST的数据
	 * @param unsigned int  sto_id  要删除的仓库id  
	 * @return 1 成功 
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-07-06
	 */
	public function deleteSto(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			//判断有没有商店锁
			$shopLockStatus = D('config')->getLockShopStatus();
			if($shopLockStatus['status'] == 1)
				throw new \XYException(__METHOD__,-10507);
			//验证字段
			if(!$this->field('sto_id')->create($data,self::StorageModel_deleteSto))
				throw new \XYException(__METHOD__,$this->getError());
			// var_dump($this->data);
			//判断该仓库是否存在
			$tmp = $this->get_(array('sto_id'=>$this->sto_id));
			if(empty($tmp))
				throw new \XYException(__METHOD__,-23501);
			if($tmp['status'] == -1)
				throw new \XYException(__METHOD__,-23502);
			// var_dump($this->data);
			// var_dump($tmp);
			//删除数据
			$delData = null;
			$delData['sto_id'] = $this->sto_id;
			$delData['admin_uid'] = $this->admin_uid;
			$delData['status'] = -1;
			$delData['sto_name'] = $tmp['sto_name'].C('DELETED_MARKER');
			log_("delData",$delData,$this);
			// var_dump($delData['sto_name']);

			// var_dump($tmp['sto_name']);
			// var_dump(C('DELETED_MARKER'));
			// var_dump($delData);
			// echo 111111;
			$res = $this->save($delData);
			// echo $this->_sql();
			// var_dump($res) ;
			// echo 22222;
			if(empty($res))
				throw new \XYException(__METHOD__,-23000);
			else
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $res;
			}
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
	 * 得到一个sto_id的仓库的信息
	 * @internal
	 * @param mixed|null $data POST的数据
	 *
	 * @param sto_id 仓库id
	 * @param isLock 是否锁表@internal
	 *
	 * @return array 数据库一行field后的结果
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-06-09
	 */
	public function get_(array $data = null,$isLock = false )
	{
		log_("data",$data,$this);
		//验证数据
		if(!$this->field('sto_id')->create($data,self::StorageModel_get_) )
			throw new \XYException(__METHOD__,$this->getError());
		log_("this->data",$this->data,$this);
		$reData = null;
		$reData = $this->where(array('sto_id'=>$this->sto_id,'admin_uid'=>$this->admin_uid))->lock($isLock)->find();
		if($reData===false)
			throw new \XYException(__METHOD__,-23000);
		if(empty($reData) )
			throw new \XYException(__METHOD__,-23501);
		return $reData;	
	}

	/**
	 * 获取当前店铺所有sto_id对应的sto_name
	 * @internal
	 * @param mixed|null $data POST的数据
	 * @param isLock 是否锁表@internal
	 *
	 * @return array 数据库一行field后的结果
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-06-09
	 */
	public function getStoName($isInstallSql = false,$isLock = false)
	{
		$stoNameList = M('Storage')->where('admin_uid='.getAdminUid())->lock($isLock)->getField('sto_id,sto_name');
		if($isInstallSql)
		{
			if( $stoNameList === false )
				throw new \XYException(__METHOD__,-23000);
		}
		else
		{
			if(empty($stoNameList))
				throw new \XYException(__METHOD__,-23000);
		}

		return $stoNameList;
	}


	/**
	 * 得到一坨sto的信息
	 * @api
	 * @param mixed|null $data POST的数据
	 *
	 * @param enum $type 1|2:模式 1-所有storage 2-有效storage，即status=1的storage
	 *
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.11
	 * @date    2017-06-10
	 */
	public function querySto(array $data = null)
	{
		switch (intval($data['type']))
		{
			case '1':{
				$tmpRe = D('Storage')->where(array('admin_uid'=>getAdminUid()) )->order('sto_index')->select();
				break;
			}
			case '2':{
				$tmpRe = D('Storage')->where(array('admin_uid'=>getAdminUid(),'status'=>1))->order('sto_index')->select();
				break;
			}
			default:
				throw new \XYException(__METHOD__,-23005);
		}

		if ($tmpRe === false || $tmpRe === null)
			throw new \XYException(__METHOD__,-23000);//不允许为空
		log_("tmpRe",$tmpRe,$this);
		//获取总数量
		$stoIdList = null;
		foreach($tmpRe as $key=>$value)
		{
			$stoIdList[] = $value['sto_id'];
		}
		$queryData = D('skuStorage')->where(array('admin_uid'=>getAdminUid(),'sto_id'=>array('in',$stoIdList)))->select();
		log_("queryData",$queryData,$this);
		foreach($tmpRe as $key=>$value)
		{
			$tmpRe[$key]['totalStock'] = 0;
			$tmpRe[$key]['totalValue'] = 0;
			foreach($queryData as $k=>$v)
			{
				if($value['sto_id']==$v['sto_id'])
				{
					$tmpRe[$key]['totalStock'] += $v['stock'];
					$tmpRe[$key]['totalValue'] += xymul($v['unit_price'],$v['stock']);
				}
			}
		}
		log_("tmpRe",$tmpRe,$this);
		return $tmpRe;
	}
	















}