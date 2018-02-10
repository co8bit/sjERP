<?php
namespace Home\Model;
use Think\Model;

/**
 * 分类Model
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class CatModel extends GoodModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('cat_id', 'checkDeny_cat_id', -5080, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//cat_id不合法
		array('cat_name', '1,20', -5081, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //cat_name长度不合法
		array('cat_index', 'isUnsignedInt', -5082, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//cat_index不合法
		array('status', 'checkDeny_bool_status', -5010, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH), //status不合法

		//CatModel_createCat
		array('cat_name', '1,20', -5081,
			self::MUST_VALIDATE, 'length',self::CatModel_createCat), //cat_name长度不合法
		array('cat_name', 'checkDeny_depend_catName', -5084,
			self::MUST_VALIDATE, 'callback',self::CatModel_createCat), //cat_name重名
		array('cat_index', 'isUnsignedInt',-5082,
			self::MUST_VALIDATE, 'function',self::CatModel_createCat),//cat_index不合法
		array('status', 'checkDeny_bool_status', -5010,
			self::MUST_VALIDATE, 'callback',self::CatModel_createCat), //status不合法

		//CatModel_editCat
		array('cat_id', 'checkDeny_cat_id', -5080,
			self::MUST_VALIDATE, 'callback',self::CatModel_editCat),//cat_id不合法
		array('cat_name', '1,20', -5081,
			self::EXISTS_VALIDATE, 'length',self::CatModel_editCat), //cat_name长度不合法
		array('cat_name', 'checkDeny_depend_catName', -5084,
			self::EXISTS_VALIDATE, 'callback',self::CatModel_editCat), //cat_name重名
		array('cat_index', 'isUnsignedInt',-5082,
			self::EXISTS_VALIDATE, 'function',self::CatModel_editCat),//cat_index不合法
		array('status', 'checkDeny_bool_status',-5010,
			self::EXISTS_VALIDATE, 'callback',self::CatModel_editCat), //status不合法



	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid

		//CatModel_createCat
		array('admin_uid','getAdminUid',self::CatModel_createCat,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::CatModel_createCat),

		//CatModel_editCat
		array('admin_uid','getAdminUid',self::CatModel_editCat,'function'),//填入所属创建者uid
	);
	
	/**
	 * 测试单元测试用
	 */
	public function test()
	{
		echo '123';
	}

	/**
	 * 创建类别
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param string $cat_name 类别名称
	 * @param unsigned_int $cat_index 显示顺序，一个正整数，数字越小，显示越靠前，1是最前.现今，默认为2
	 * @param enum $status 0|1 状态
     * @param $cat_class 0商品类别 1收入支出项目类别
	 * 
	 * @return unsigned_int 成功-cat_id > 0
	 * @throws \XYException
	 */
	public function createCat(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
		    if (!isset($data['cat_class']))
            {
                $data['cat_class'] = 0;
            }
			$data['status'] = intval($data['status']);
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);


			if (!$this->field('cat_name,cat_index,cat_class,status')->create($data,self::CatModel_createCat))
				throw new \XYException(__METHOD__,$this->getError());

			$tmp = $this->add();
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
	 * 编辑类别信息.
	 * 
	 * 注意:这里需要同步更改sku表里的cat冗余信息。
	 * note:当没做实质性修改时返回1
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $cat_id
	 * @param string $cat_name 类别名称
	 * @param unsigned_int $cat_index 显示顺序，一个正整数，数字越小，显示越靠前，1是最前
	 * @param enum $status 0|1 状态
	 * 
	 * @return '1' 成功
	 * @throws \XYException
	 */
	public function editCat(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);

			$queryData = M('Cat')->where(array('cat_id'=>intval($data['cat_id']),'admin_uid'=>getAdminUid()))->find();
			if ($queryData['cat_name'] == $data['cat_name']) unset($data['cat_name']);
			$data['status'] = intval($data['status']);

			if (!$this->field('cat_id,cat_name,cat_index,status')->create($data,self::CatModel_editCat))
				throw new \XYException(__METHOD__,$this->getError());

			$createData = $this->data;

			$tmp = $this->where(array('cat_id'=>$this->cat_id,'admin_uid'=>$this->admin_uid))->save();

			if ( ($tmp === null) || ($tmp === false) )
			{
				throw new \XYException(__METHOD__,-5000);
			}
			elseif ($tmp === 0)
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return 1;
			}
			else
			{
				//更改SKU
				if (isset($createData['cat_id'])) $skuData['cat_id']       = $createData['cat_id'];
				if (isset($createData['cat_name'])) $skuData['cat_name']   = $createData['cat_name'];
				if (isset($createData['cat_index'])) $skuData['cat_index'] = $createData['cat_index'];
				if (isset($createData['status'])) $skuData['cat_status']   = $createData['status'];
				if (isset($createData['status'])) $skuData['spu_status']   = $createData['status'];
				if (isset($createData['status'])) $skuData['status']       = $createData['status'];
				$tmp = null;
				$tmp = D('Sku')->where(array('cat_id'=>$skuData['cat_id'],'admin_uid'=>$createData['admin_uid']))->save($skuData);
				if ( ($tmp === null) || ($tmp === false) )
					throw new \XYException(__METHOD__,-5000);


				//更改SPU
				$tmp = null;
				$tmp = D('Spu')->where(array('cat_id'=>$skuData['cat_id'],'admin_uid'=>$createData['admin_uid']))->save(array('status'=>$skuData['status']));
				if ( ($tmp === null) || ($tmp === false) )
					throw new \XYException(__METHOD__,-5000);

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return 1;
			}
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
	 * 删除类别
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $cat_id
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteCat(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);


			if (!($this->field('cat_id')->create($data,self::MODEL_UPDATE)))
				throw new \XYException(__METHOD__,$this->getError());

			$tmpCount = D('Sku')->where(array('cat_id'=>$this->cat_id,'admin_uid'=>$this->admin_uid))->count();
			if ($tmpCount >= 1)
				throw new \XYException(__METHOD__,-5083);
			$queryData = $this->where(array('cat_id'=>$this->cat_id,'admin_uid'=>$this->admin_uid))->find();
			$deleteCatData = array(
				'cat_id'=>$this->cat_id,
				'status' => -1,
				'cat_name' => $queryData['cat_name'].C('DELETED_MARKER')
				);

			$tmp = $this->save($deleteCatData);//TODO:目前这里是靠数据库的外键删除掉SKU表里的相关东西的
			if (empty($tmp))
				throw new \XYException(__METHOD__,-5000);
			
			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			return 1;
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
	 * 查询店铺分类
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param $cat_class 0.来自商品分类，1.银行账分类
	 * @param enum $type 1|2:模式 1-所有cat 2-有效cat，即status=1的cat
	 * 
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 */
	public function queryCat(array $data = null)
	{
        if (!isset($data['cat_class']))
        {
            $data['cat_class'] = 0;
        }
		switch (intval($data['type']))
		{
			case '1':{
				$tmpRe = D('Cat')->where(array('admin_uid'=>session('user_auth.admin_uid'),'cat_class'=>$data['cat_class'],'status'=>array('in',array(0,1))))->order('cat_index')->select();
				break;
			}
			case '2':{
				$tmpRe = D('Cat')->where(array('admin_uid'=>session('user_auth.admin_uid'),'cat_class'=>$data['cat_class'],'status'=>1))->order('cat_index')->select();
				break;
			}
			default:
				throw new \XYException(__METHOD__,-5012);
		}

		if ($tmpRe === false)
			throw new \XYException(__METHOD__,-5000);

		return $tmpRe;
	}



	/**
	 * 得到类别信息
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * @param unsigned_int $cat_id
	 * @param enum $type 1|2: 1-所有状态，2-有效状态
	 * @param $cat_class 0商品类别 1收入支出项目类别
     *
	 * @return array 数据库中的一行
	 * @throws \XYException
	 */
	public function get_(array $data = null,$type)
	{
        if (!isset($data['cat_class']))
        {
            $data['cat_class'] = 0;
        }
		if (!($this->field('cat_id,cat_class')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		if ($type == 1)
			$tmp = $this->where(array('cat_id'=>$this->cat_id,'admin_uid'=>$this->admin_uid,'cat_class' => $this->cat_class))->find();
		elseif ($type == 2)
			$tmp = $this->where(array('cat_id'=>$this->cat_id,'admin_uid'=>$this->admin_uid,'status'=>1,'cat_class' => $this->cat_class))->find();

		if (empty($tmp))
			throw new \XYException(__METHOD__,-5000);
		
		return $tmp;
	}


}