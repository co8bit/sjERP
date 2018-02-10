<?php
namespace Home\Model;
use Think\Model;
/**
 * SKU Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 * - {@todo:以spu、cat表为准，进行定时检查}
 */
class SkuModel extends GoodModel
{
	const MODEL_EDIT = 4;
	/* 自动验证 */
	protected $_validate = array(
		/*
			这里只检查外键id是否合法，如spu_id，至于冗余信息的合法性，如spu_name、cat_name等，因为是从数据库中读的，所以不用做检查。它们新建的时候是从各自的Model，如spu_name是从SpuModel建立的，在新建的时候由它们自己的Model进行判断。
		 */
		array('sku_id', 'checkDeny_sku_id', -5001, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//sku_id不合法
		array('spu_id', 'checkDeny_spu_id', -5002, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),//spu_id不合法
		array('cat_id', 'checkDeny_cat_id', -5003, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),//cat_id不合法
		array('spec_name', '1,50', -5004, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //spec_name长度不合法
		array('sku_index', 'isNonnegativeInt', -5007, self::EXISTS_VALIDATE, 'function',self::MODEL_BOTH),//sku_index不合法
		array('sku_id', 'checkDeny_sku_id', -5001, self::EXISTS_VALIDATE, 'callback',self::MODEL_EDIT),//sku_id不合法
		array('spu_id', 'checkDeny_spu_id', -5002, self::MUST_VALIDATE, 'callback',self::MODEL_EDIT),//spu_id不合法
		array('cat_id', 'checkDeny_cat_id', -5003, self::MUST_VALIDATE, 'callback',self::MODEL_EDIT),//cat_id不合法

        //createFinanceCart
        array('sku_class','0,3',-5506,self::EXISTS_VALIDATE,'length',self::SkuModel_createFinanceCart),
        array('spec_name','1,50',-5004,self::MUST_VALIDATE,'length',self::SkuModel_createFinanceCart),//spec_name长度不合法
        array('spec_name','check_finance_cart',-5021,self::MUST_VALIDATE,'callback',self::SkuModel_createFinanceCart),//spec_name长度不合法
        array('sku_index', 'isNonnegativeInt', -5007, self::EXISTS_VALIDATE, 'function',self::SkuModel_createFinanceCart),//sku_index不合法
        array('status', 'checkDeny_bool_status', -5010, self::EXISTS_VALIDATE, 'callback',self::SkuModel_createFinanceCart), //status不合法

        //editFinanceCart
        array('sku_id','checkDeny_sku_id',-5001,self::MUST_VALIDATE,'callback',self::SkuModel_editFinanceCart),
        array('sku_class','0,3',-5506,self::EXISTS_VALIDATE,'length',self::SkuModel_editFinanceCart),
        array('spec_name','1,50',-5004,self::EXISTS_VALIDATE,'length',self::SkuModel_editFinanceCart),//spec_name长度不合法
        array('sku_index', 'isNonnegativeInt', -5007, self::EXISTS_VALIDATE, 'function',self::SkuModel_editFinanceCart),//sku_index不合法
        array('status', 'checkDeny_bool_status', -5010, self::EXISTS_VALIDATE, 'callback',self::SkuModel_editFinanceCart), //status不合法

		//注：status经过create并不合法，需要手动赋值$status ? 1 : 0
	);
	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH)
	);
	/**
	 * 创建/修改商品.
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param unsigned_int spu_id 填写spu_id则为模式1，否则为模式2。
	 *                             模式1-编辑已知产品SPU，模式2-创建SPU
	 * -----以下是模式1和模式2都  必填  项：
	 * @param json skuStoData sku/sto信息
	 * @param unsigned_int sku_id 为0时，为新增sku，>0时，修改sku信息
	 * @param unsigned_int sku_storage_id 为0时，为新增sku，>0时，修改sku信息
	 * @param bool "delete":true,		//删除 如果没有删除不填
	 * @example skuStoData json:
	 * 	mini:{"data":[{"sto_id":1,"skuStoData":[{"sku_storage_id":43,"sku_id":"15","spec_name":"29只装","stock":3220,"unit_price":641.5,"sku_sto_index":1,"sku_sto_status":1},{"sku_storage_id":0,"sku_id":0,"spec_name":"46只装","stock":3230,"unit_price":645,"sku_sto_index":2,"sku_sto_status":1},{"delete":true,"sku_storage_id":44,"sku_id":"16","spec_name":"93只装","stock":3320,"unit_price":645,"sku_sto_index":2,"sku_sto_status":1}]},{"sto_id":2,"skuStoData":[{"sku_storage_id":46,"sku_id":"16","spec_name":"93只装","stock":3220,"unit_price":641.5,"sku_sto_index":1,"sku_sto_status":1},{"sku_storage_id":0,"sku_id":0,"spec_name":"46只装","stock":3230,"unit_price":645,"sku_sto_index":2,"sku_sto_status":0},{"sku_storage_id":0,"sku_id":0,"spec_name":"48只装","stock":3230,"unit_price":645,"sku_sto_index":2,"sku_sto_status":1},{"delete":true,"sku_storage_id":45,"sku_id":"15","spec_name":"29只装","stock":3320,"unit_price":645,"sku_sto_index":2,"sku_sto_status":1}]}]}
	 * @example 展开:
	 * {
		"data":
		[
			{
				"sto_id":1,//仓库id
				"skuStoData":
				[
					{
						"sku_storage_id":43,   //修改
						"sku_id":"15",
						"spec_name":"29只装",
						"stock":3220,
						"unit_price":641.5,
						"sku_sto_index":1,
						"sku_sto_status":1
					},
					{
						"sku_storage_id":0,    //新增
						"sku_id":0,         //新增
						"spec_name":"46只装",
						"stock":3230,
						"unit_price":645,
						"sku_sto_index":2,
						"sku_sto_status":1
					},
					{
						"delete":true,
						"sku_storage_id":44,    //删除
						"sku_id":"16",
						"spec_name":"93只装",
						"stock":3320,
						"unit_price":645,
						"sku_sto_index":2,
						"sku_sto_status":1
					}
				]
			},
			{
				"sto_id":2,
				"skuStoData":
				[
					{
						"sku_storage_id":46,   //修改或者什么都不改
						"sku_id":"16",
						"spec_name":"93只装",
						"stock":3220,
						"unit_price":641.5,
						"sku_sto_index":1,
						"sku_sto_status":1
					},
					{
						"sku_storage_id":0,    //新增
						"sku_id":0,         //新增
						"spec_name":"46只装",
						"stock":3230,
						"unit_price":645,
						"sku_sto_index":2,
						"sku_sto_status":0
					},
					{
						"sku_storage_id":0,    //新增
						"sku_id":0,         //新增
						"spec_name":"48只装",
						"stock":3230,
						"unit_price":645,
						"sku_sto_index":2,
						"sku_sto_status":1
					},
					{
						"delete":true,		//删除
						"sku_storage_id":45,   
						"sku_id":"15",
						"spec_name":"29只装",
						"stock":3320,
						"unit_price":645,
						"sku_sto_index":2,
						"sku_sto_status":1
					}
				]
			}
		]
	}
	 * -----模式2必填项：(模式1不填)
	 * @param unsigned_int $cat_id 类别id
	 * @param string $spu_name 商品名称
	 * @param unsigned_int $spu_index spu显示顺序，一个正整数，数字越小，显示越靠前，1是最前。现今，默认为2
	 * @param string $qcode 速查码
	 * @param unsigned_int $spu_status spu是否启用  0|1 1-启用 0-不启用
	 * 
	 * @return '1' 成功
	 * @throws \XYException
	 */
	public function updateSKU(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);

			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);
			log_("data",$data,$this);
			//验证字段
			if (!$this->field("spu_name,spu_index,qcode,cat_id,spu_id")->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());
			log_("this->data",$this->data,$this);
			//spu
			$spu_id = I('data.spu_id/d','','int',$data);//注意这里的默认值一定要为整型，不然isInt判断会错
			log_("spu_id11111",$spu_id,$this);
			if (isUnsignedInt($spu_id))//给已知商品添加规格
			{
				$spuInfo = M('Spu')->where(array('spu_id'=>$spu_id,'admin_uid'=>session('user_auth.admin_uid')))->lock(true)->find();
				if (empty($spuInfo))
					throw new \XYException(__METHOD__,-5008);
				$this->spu_id     = $spuInfo['spu_id'];
				$this->spu_name   = $spuInfo['spu_name'];
				$this->spu_index  = $spuInfo['spu_index'];
				$this->qcode      = $spuInfo['qcode'];
				$this->spu_status = $spuInfo['status'];
				$this->cat_id     = $spuInfo['cat_id'];
				$spu_id = $this->spu_id; //注意，因为后面create了，所以必须要用$spu_id，而不是$this->spu_id
				$this->sku_class  = 0;
			}
			else//创建SPU
			{
				$spu_id = null;
				$spu_id = D("Spu")->createSPU(array(
						'spu_name'  => $this->spu_name,
						'spu_index' => $this->spu_index,
						'qcode'     => $this->qcode,
						'cat_id'    => $this->cat_id,
						'status'    => intval($data['spu_status']),
					),true);
				log_("spu_id",$spu_id,$this);
				// $this->spu_id = $spu_id;
				$this->spu_status = $data['spu_status'] ? 1 : 0;
			}

			$spu_name = $this->spu_name;
			$spu_index = $this->spu_index;
			$qcode = $this->qcode;
			$spu_status = $this->spu_status;

			//cat
			//新建类别时会先调用createCat接口，取得返回的cat_id
			$catInfo = M('Cat')->where(array('cat_id'=>$this->cat_id,'admin_uid'=>getAdminUid()))->lock(true)->find();
			if (empty($catInfo))
				throw new \XYException(__METHOD__,-5003);
			/*$this->cat_name = $catInfo["cat_name"];
			$this->cat_index = $catInfo["cat_index"];
			$this->cat_status = $catInfo['status'];*/
			$cat_id = $this->cat_id;//注意，因为后面create了，所以必须要用$cat_id，而不是$this->cat_id

			//取出同一spu下所有sku的信息，为了判断是否重名
			$specNameList = null;
			$tmpQueryData = null;
			// $spu_id = 3;
			// $tmpQueryData = M('Sku')->where(array('spu_id'=>$spu_id,'admin_uid'=>$this->admin_uid))->lock(true)->getField('sku_id,spec_name');
			$tmpQueryData = M('Sku')->where(array('spu_id'=>$spu_id,'admin_uid'=>$this->admin_uid))->lock(true)->select();
			if ($tmpQueryData === false)
				throw new \XYException(__METHOD__,-5000);
			log_("tmpQueryData",$tmpQueryData,$this);
			if(!empty($tmpQueryData)) // 允许商品名存在，sku不存在的情况出现
			{
				foreach ($tmpQueryData as $key => $value)
				{
					$specNameList[$value['sku_id']][] = $value['spec_name'];
					$specNameListBack[$value['sku_id']][] = $value['spec_name'];
				}
			}
			log_("specNameList",$specNameList,$this);
			//获取新增skuData数据
			$tmpTextCart = I('param.skuStoData','','');
			if (empty($tmpTextCart))
				$cartTmp = $data['skuStoData'];
			else
				$cartTmp = json_decode(I('param.skuStoData','',''),true);
			if (empty($cartTmp['data']))
				throw new \XYException(__METHOD__,-5009);
			log_("cartTmp",$cartTmp,$this);
			//新增的仓库和spec_name是否重复
			$stoIdList = array();
			$tmpSkuSto = array();
			$skuSto    = array();
			foreach($cartTmp['data'] as $key => $value)
			{
				if(isNonnegativeInt($value['sto_id']))
				{
					$tmpSto = I('data.sto_id/d','','int',$value);
					$stoIdList[] = $tmpSto;
					//判断仓库是否重复
					if(count(array_keys($stoIdList,$tmpSto) )>1 )
						throw new \XYException(__METHOD__,-5019);
				}
				else
					throw new \XYException(__METHOD__,-5018);
			}

			$tmpSkuSto =  D('SkuStorage')->join('LEFT JOIN sku ON sku.sku_id=sku_storage.sku_id')->where(array('sku_storage.sku_sto_status'=>1,'sku_storage.admin_uid'=>getAdminUid(),'spu_id'=>$spu_id))->select();
			$stoSpec = array();
			// 生成以sto_id 为下标的规格数组
			foreach($stoIdList as $key=>$value)
			{
				foreach($tmpSkuSto as $k=>$v)
				{
					if($value == $v['sto_id'])
					{
						$skuSto[$value][] = $v;
						$stoSpec[$value][$v['sku_id']] = $v['spec_name'];
					}
				}
			}

			$stoData = null;
			$skuIdList = null;
			$newSpecNameList = null;
			$skuStoData = null;//需要新增的skuStoData数据
			$editSkuStoData = null;//需要修改的skuStoData数据
			$delSkuStoData = null;//需要删除的skuStoData数据
			$tmpSkuUpdateData = null;//需要修改的skuData数据
			$tmpSkuData = null;//需要新增的skuData数据
			$tmpSpecNameList = null;
			foreach ($cartTmp['data'] as $key => $value)
			{
				if(empty($value['skuStoData']))
					throw new \XYException(__METHOD__,-5009);
				foreach($value['skuStoData'] as $k=>$v)
				{
					log_("v",$v,$this);
					if  (  	
							( isset($v['sku_id']) && isNonnegativeInt($v['sku_id']) )
							&&
							(  isset($v['sku_storage_id']) && isNonnegativeInt($v['sku_storage_id']) ) //sku_id sku_storage_id只能用isNonnegativeInt验证，因为当其为0时代表新增
							&&
							(
								(	
									isUnsignedInt($v['sku_sto_index']) &&
									(
										isset($v['spec_name'])  && 
										($v['spec_name'] !== '') &&
										($v['spec_name'] !== false) && 
										($v['spec_name'] !== null)
									) &&
									(isNonegativeReal($v['stock']))&&
									(isNonegativeReal($v['unit_price'])) &&
									(isNonegativeReal($v['sku_sto_status']))
								)//允许spec_name写'0',所以不能直接用!empty()来判断
								||
								(   isset($v['delete'])
									&& ( $v['delete'] == 1 )
								)
							)
						)
					{
						//获取当前仓库id
						$tmpStoID = null;
						$tmpStoID = I('data.sto_id/d','','htmlspecialchars',$value);
						//获取当前规格名称
						$tmpSpecName = null;
						$tmpSpecName = I('data.spec_name/s','','htmlspecialchars',$v);
						//获取当前sku_id
						$tmpSkuID = null;
						$tmpSkuID = I('data.sku_id/d','','htmlspecialchars',$v);
						//获取当前sku_storage_id
						$tmpSku_storage_id = null;
						$tmpSku_storage_id = I('data.sku_storage_id/d','','htmlspecialchars',$v);
						log_("tmpStoID",$tmpStoID,$this);
						log_("tmpSkuID",$tmpSkuID,$this);
						log_("tmpSku_storage_id",$tmpSku_storage_id,$this);
						if(empty($v['sku_id']) && empty($v['sku_storage_id'])  )//新增
						{
							$tmpSkuStoData = null;
							$tmpSkuStoData = array(
							'sto_id'	 => $tmpStoID,
							'stock'      => xyround(I('data.stock/f',0,'htmlspecialchars',$v) ),
							'unit_price' => xyround(I('data.unit_price/f',0,'htmlspecialchars',$v) ),
							'admin_uid'  => getAdminUid(),
							'reg_time'   => NOW_TIME,
							'update_time'   => NOW_TIME,
							'sku_sto_status'     => I('data.sku_sto_status/d',0,'htmlspecialchars',$v),
							'spec_name' => $tmpSpecName,//后面用来获取对应的sku_id
							'sku_sto_index'     => I('data.sku_sto_index/d',0,'htmlspecialchars',$v)
							);
							$skuStoData[] = $tmpSkuStoData;
							//当前仓库新增规格
							$stoSpec[$tmpStoID][0][] = I('data.spec_name/s','','htmlspecialchars',$v);
							$tmpCreate = null;
							$tmpCreateSku = array(
								'sku_sto_status'     => I('data.sku_sto_status/d',0,'htmlspecialchars',$v),
								'sku_sto_index'  => I('data.sku_sto_index/d',0,'htmlspecialchars',$v),
								'spec_name'  => $tmpSpecName,
								'stock' 	=> xyround(I('data.stock/f',0,'htmlspecialchars',$v) )
							);
							$tmpSkuData[$tmpSpecName][] = $tmpCreateSku;
							// $newSpecNameList[] = $tmpSpecName;//新增规格数组
							// 
							log_("1111111",1111111,$this);
							
						}
						elseif ( isset($v['delete']) && ($v['delete'] == 1) )//删除
						{
							log_("22222222",22222222,$this);
							/*$skuDeleteData[] = array('sku_id'=>I('data.sku_id/d',0,'htmlspecialchars',$v));*/
							$delSkuStoData[] = array('sku_storage_id'=>$tmpSku_storage_id);
							unset($stoSpec[$tmpStoID][$tmpSkuID]);
							if(isset($specNameList[$tmpSkuID]))
								unset($specNameList[$tmpSkuID]);

						}
						else //修改
						{
							log_("333333333",333333333,$this);
							//判断是否存在同一个sku_id改不同的规格名称的情况
							$tmpSpecNameList[$tmpSkuID][] =  $tmpSpecName;
							// if( count($tmpSpecNameList[$tmpSkuID]) >1 )
							// 	throw new \XYException(__METHOD__,-5020);
							if( count(array_keys($tmpSpecNameList[$tmpSkuID],$tmpSpecName)) != count($tmpSpecNameList[$tmpSkuID]))
								throw new \XYException(__METHOD__,-5020);
							$specNameList[$tmpSkuID] = $tmpSpecName;
							$tmpSkuStoData = null;
							$tmpSkuStoData = array(
							// 'sto_id'	 => $tmpStoID,
							// 'sku_id'     => $tmpSkuID,
							'sku_storage_id' => $tmpSku_storage_id,
							'stock'      => xyround(I('data.stock/f',0,'htmlspecialchars',$v) ),
							'unit_price' => xyround(I('data.unit_price/f',0,'htmlspecialchars',$v) ),
							'admin_uid'  => getAdminUid(),
							'update_time'   => NOW_TIME,
							'sku_sto_status'     => I('data.sku_sto_status/d',0,'htmlspecialchars',$v),
							'sku_sto_index'     => I('data.sku_sto_index/d',0,'htmlspecialchars',$v)
							);
							$editSkuStoData[] = $tmpSkuStoData;
							$tmpCreate = null;
							$tmpCreate = array(
								'sku_id'     => $tmpSkuID,
								'spec_name'  => $tmpSpecName,
								'sku_sto_index'  => I('data.sku_sto_index/d',0,'htmlspecialchars',$v),
								'sku_sto_status'     => I('data.sku_sto_status/d',0,'htmlspecialchars',$v),
								'stock'      => xyround(I('data.stock/f',0,'htmlspecialchars',$v) ),
								'sto_id'     => $tmpStoID
							);
							$tmpSkuUpdateData[$tmpSkuID][] = $tmpCreate;
							$stoSpec[$tmpStoID][$tmpSkuID] = $tmpSpecName;
						}
					}
					else
						throw new \XYException(__METHOD__,-5009);
				}
			}

			log_("stoSpec",$stoSpec,$this);
			//检查每个仓库中的规格是否重名
			foreach($stoSpec as $key=>$value)
			{
				foreach($value as $k=>$v)
				{
					if($k !=0)//修改
					{
						if(count(array_keys($value,$v)) >1)
							throw new \XYException(__METHOD__,-5014);
					}
					else
					{
						foreach($v as $k1=>$v1)
						{
							if( (count(array_keys($v,$v1)) >1) || in_array($v1,$value) ) //判断新增的规格是否在修改的规格中
								throw new \XYException(__METHOD__,-5014);	
						}
					}
				}
			}

			// foreach($newSpecNameList as $key=>$value)//检查需要新增的规格在已有+修改的规格中是否重复
			// {
			// 	if(in_array($value,$specNameList))
			// 		throw new \XYException(__METHOD__,-5014);
			// }

			// foreach($specNameList as $key=>$value)//检查需要修改的规格在已有+修改的规格中是否重复
			// {
			// 	if(count(array_keys($specNameList,$value)) >1)
			// 		throw new \XYException(__METHOD__,-5014);
			// }

			log_("skuStoData",$skuStoData,$this);
			log_("editSkuStoData",$editSkuStoData,$this);
			log_("delSkuStoData",$delSkuStoData,$this);
			log_("tmpSkuUpdateData",$tmpSkuUpdateData,$this);
			log_("tmpSkuData",$tmpSkuData,$this);

			// log_("specNameList",$specNameList,$this);
			//处理sku新增的情况
			if(!empty($tmpSkuData))
			{
				foreach($tmpSkuData as $key=>$value)
				{
					// log_("key",$key,$this);
					if(!in_array($key,$specNameList))
					{
						$tmpStatusList = null;
						$tmpIndexLIst = null;
						$skuData = null;
						log_("skuData",$skuData,$this);
						$total_stock = 0;
						foreach($value as $k=>$v)
						{
							$tmpStatusList[] = $v['sku_sto_status'];
							$tmpIndexLIst[] = $v['sku_sto_index'];
							$total_stock +=$v['stock'];
						}
						if(in_array(1,$tmpStatusList))
							$skuData['status'] = 1;
						else
							$skuData['status'] = 0;

						if(in_array(1,$tmpIndexLIst))
							$skuData['sku_index'] = 1;
						else
							$skuData['sku_index'] = 2;

						$skuData['spec_name'] = $key;
						$skuData['spu_id']  = $spu_id;
						$skuData['spu_name']   = $spu_name;
						$skuData['spu_index']  = $spu_index;
						$skuData['qcode']      = $qcode;
						$skuData['spu_status'] = $spu_status;
						$skuData['cat_id']     = $cat_id;
						$skuData['cat_name']   = $catInfo["cat_name"];
						$skuData['cat_index']  = $catInfo["cat_index"];
						$skuData['cat_status'] = $catInfo['status'];
						$skuData['admin_uid']  = getAdminUid();
						$skuData['total_stock']  = $total_stock;
					    $skuData['sn']         = $this->getNextSn('SKU');
					    log_("skuData1111",$skuData,$this);
					    if(!$this->field('spec_name,spu_id,spu_name,spu_index,qcode,spu_status,cat_id,cat_name,cat_index,cat_status,admin_uid,sn,status,sku_index,total_stock')->create($skuData,self::MODEL_INSERT))
					    	throw new \XYException(__METHOD__,$this->getError());
					    $skuIdList[$key] = $this->add();
					}
					else
					{
						$tmp= array_keys($specNameList,$key);
						$skuIdList[$key] = $tmp[0];
					}
				}
			}

			//修改和新增sku_storage数据
			$dbSkuSto = D('SkuStorage');
			if(!empty($skuStoData))
			{
				foreach($skuStoData as $key=>$value)//新增
				{
					$value['sku_id'] = $skuIdList[$value['spec_name']];
					$resAddSkuSto = $dbSkuSto->updateSkuSto($value);
				}
			}

			if(!empty($editSkuStoData))
			{
				foreach($editSkuStoData as $key=>$value)//修改
				{
					$resEditSkuSto[] = $dbSkuSto->updateSkuSto($value);
				}
			}
			
			if(!empty($delSkuStoData))
			{
				foreach($delSkuStoData as $key=>$value) //删除
				{
					log_("value",$value,$this);
					$resDelSkuSto[] = $dbSkuSto->deleteSkuSto($value,true);
				}
			}
			
			log_("this->data11111",$this->data,$this);
			//修改sku_id
			if(!empty($tmpSkuUpdateData))
			{
				foreach($tmpSkuUpdateData as $key=>$value)
				{
					$tmpStatusList = null;
					$tmpIndexLIst = null;
					log_("value",$value,$this);
					log_("skuData",$skuData,$this);
					$SkuUpdateData = null;
					foreach($value as $k=>$v)
					{
						$tmpStatusList[] = $v['sku_sto_status'];
						$tmpIndexLIst[] = $v['sku_sto_index'];
						foreach($tmpSkuSto as $row=>$item)
						{
							if($row['sto_id'] == $value['sto_id'] && $row['sku_id'] == $value['sku_id'])
							{
								$stock_ori = $item['stock'];
								$total_stock = $item['total_stock'];
							}
						}
						if($v['sku_sto_status'] == 0)
							$total_stock -= $stock_ori;
						else
							$total_stock += $v['stock']-$stock_ori;
						
					}
					if(in_array(1,$tmpStatusList))
						$SkuUpdateData['status'] = 1;
					else
						$SkuUpdateData['status'] = 0;

					if(in_array(1,$tmpIndexLIst))
						$SkuUpdateData['sku_index'] = 1;
					else
						$SkuUpdateData['sku_index'] = 2;
					$SkuUpdateData['spec_name'] = $v['spec_name'];
					$SkuUpdateData['sku_id'] = $key;
					$SkuUpdateData['spu_name']   = $spu_name;
					$SkuUpdateData['spu_index']  = $spu_index;
					$SkuUpdateData['qcode']      = $qcode;
					$SkuUpdateData['spu_status'] = $spu_status;
					$SkuUpdateData['cat_id']     = $cat_id;
					$SkuUpdateData['cat_name']   = $catInfo["cat_name"];
					$SkuUpdateData['cat_index']  = $catInfo["cat_index"];
					$SkuUpdateData['cat_status'] = $catInfo['status'];
					$SkuUpdateData['spu_id'] = $spu_id;
					$SkuUpdateData['admin_uid']  = getAdminUid();
					$SkuUpdateData['total_stock']  = $total_stock;
					log_("SkuUpdateData",$SkuUpdateData,$this);
				    if(!$this->field('sku_id,spec_name,spu_id,spu_name,qcode,spu_status,cat_id,cat_name,cat_index,cat_status,admin_uid,sn,total_stock,Pstatus,sku_index')->create($SkuUpdateData,self::MODEL_EDIT))
				    	throw new \XYException(__METHOD__,$this->getError());
				    log_("this->data",$this->data,$this);
				    $resUpdataSku[] = $this->save();
				}
			}
			
			
			//查找当前所有仓库下所有的未被删除的sku_id 对应的信息
			$tmpData = $dbSkuSto -> where(array('sto_id'=>array('in',$stoIdList),'admin_uid'=> getAdminUid(),'isdelete'=>0)) ->getField('sku_id',true);
			if(!empty($specNameListBack))
			{
				foreach($specNameListBack as $key => $value)
				{
					if(!in_array($key,$tmpData))//sku_id不在sku_storage_id表中
					{
						$deleteSkuData = null;
						$deleteSkuData['sku_id'] = $key;
						$this->deleteSku($deleteSkuData,true);
					}
				}
			}

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
	 * 删除SKU
	 * 
	 * @internal server
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $sku_id
	 * 
	 * @return '1' 成功
	 * @throws \XYException
	 * 
	 * @todo 如果删除的是一个SPU的最后一个SKU怎么办
	 */
	public function deleteSKU(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);
			if(!$this->field('sku_id')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());
			/*v1.4.1:sku可以随时删除
			//sku已经发生业务往来不能被删除
			// $isHasContacts = D('SkuBill')->isHasContacts($this->sku_id,'sku');
			// if ($isHasContacts)
			// 	throw new \XYException(__METHOD__,-5505);
			*/
			
			/*v1.2: sku库存不为0可以删除
			//sku库存不为0不能被删除
			$tmpQueryData = M('Sku')->where(array("sku_id"=>$this->sku_id,'admin_uid'=>$this->admin_uid))->find();
			if ($tmpQueryData['stock'] > 0)
				throw new \XYException(__METHOD__,-5502);
			*/
		
			$tmpQueryData = M('Sku')->where(array("sku_id"=>$this->sku_id,'admin_uid'=>$this->admin_uid))->find();
			$deleteSkuData = array(
				"sku_id"=>$this->sku_id,
				'admin_uid'=>$this->admin_uid,
				'status' => -1,
				'spec_name' => $tmpQueryData['spec_name'].C('DELETED_MARKER')
				);
			$tmp = $this->save($deleteSkuData);
		
			if ( empty($tmp) )
				throw new \XYException(__METHOD__,-5000);
			//spu底下没有sku时，自动删除spu
			//todo:只有编辑店铺的时候也加上店铺锁后，这里其实才是正确的.SpuModel::deleteSpu()也一样
			$tmpCount = M('Sku')->where(array('spu_id'=>$tmpQueryData['spu_id'],'admin_uid'=>$this->admin_uid))->find();
			if ($tmpCount === false)
				throw new \XYException(__METHOD__,-5000);
			elseif (empty($tmpCount))
				D('Spu')->deleteSPU(array('spu_id'=>$tmpQueryData['spu_id']),true);
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
	 * 用sku_id得到指定的一坨sku信息.
	 *
	 * 注意：如果isLock=true，要在外部打开事务.
	 * 得到的结果集为空会扔异常.
	 *
	 * @internal
	 * @param array $data $data['sku_id']=sku_id
	 * @param enum $type 1|2.  1-只查有效sku（即status=1），2-查所有sku
	 * @param bool $isLock 是否加锁
	 * @param bool $isIgnoreEmpty 当为true时，数据库查询结果为空不抛出异常。这里是为了QueryModel::dashboard()留的后门。如果用户只有一个sku，那么当sku被删除后，为空会报错
	 * 
	 * @return  array[id] = 数据库中的指定字段
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.2
	 * @date    2016-06-09
	 */
	public function getList(array $data = null,$type,$isLock = false,$isIgnoreEmpty = false)
	{
		foreach ($data as $value)
		{
			if (!isUnsignedInt($value))
				throw new \XYException(__METHOD__,-5001);
		}
		log_("data",$data,$this);
		$map['sku_id']  = array('in',$data);
		if ($type == 1)
			$map['status'] = 1;
		$tmp = null;
		$map['sku_class'] = 0;
		$tmp = $this->where($map)->lock($isLock)->getField('sku_id,sn,spu_id,spu_name,spec_name,cat_id,cat_name,total_stock');
		log_("tmp",$tmp,$this);
		if ($tmp === false)
			throw new \XYException(__METHOD__,-5000);
		elseif (empty($tmp))
		{
			if (!$isIgnoreEmpty)
				throw new \XYException(__METHOD__,-5001);
		}
		return $tmp;
	}
	/**
	 * 得到sku_id的货物的在所有仓库的合计信息
	 * @api
	 * @param mixed|null $data POST的数据
	 *
	 * @param sku_id $sku_id
	 *
	 * @return array 数据库一行field后的结果
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.6
	 * @date    2016-07-09
	 */
	public function get_(array $data = null)
	{
		if(!$this->field('sku_id')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());
		
		$tmp = null;
		$tmp = $this->where(array('sku_id'=>$this->sku_id,'admin_uid'=>$this->admin_uid))->field('sku_id,spu_id,spu_name,spec_name,cat_id,cat_name,total_stock,status,sn')->find();
		if (empty($tmp))
			throw new \XYException(__METHOD__,-5501);
		return $tmp;
	}

	/**
	 * 查询店铺SKU
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned $type 模式 1-所有SKU 2-有效SKU，即status=1&&cat_status=1&spu_status=1的SKU
	 * 
	 * @return json_array {"data":[{数据库中的一行}{..}]}
	 * @throws \XYException
	 */
	public function querySkU(array $data = null)
	{
		switch (intval($data['type']))
		{
			case '1':{
				$tmpRe = D('Sku')->join('LEFT JOIN sku_storage on sku.sku_id=sku_storage.sku_id')->where(array('sku.admin_uid'=>getAdminUid(),'sku_class'=>0))->where('sku_sto_status<>-1')->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku.sku_id')->select();
				break;
			}
			case '2':{
				$tmpRe = D('Sku')->join('LEFT JOIN sku_storage on sku.sku_id=sku_storage.sku_id')->where(array('sku.admin_uid'=>getAdminUid(),'status'=>1,'cat_status'=>1,'spu_status'=>1,'sku_class'=>0))->where('sku_sto_status<>-1')->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku.sku_id')->select();
				break;
			}
			default:
				throw new \XYException(__METHOD__,-5013);
		}
		if ($tmpRe === false)
			throw new \XYException(__METHOD__,-5000);
		$reData = null;
		$tmpdata = null;
		$stoData = M('Storage')->where(array('admin_uid'=>getAdminUid()))->getField('sto_id,sto_name');
		foreach($tmpRe as $key=>$value)
		{
			if(!isset($reData[$value['spu_id']]))
			{
				$reData[$value['spu_id']]['spu_id'] = $value['spu_id'];
				$reData[$value['spu_id']]['cat_id'] = $value['cat_id'];
				$reData[$value['spu_id']]['cat_name'] = $value['cat_name'];
				$reData[$value['spu_id']]['cat_status'] = $value['cat_status'];
				$reData[$value['spu_id']]['spu_id'] = $value['spu_id'];
				$reData[$value['spu_id']]['spu_name'] = $value['spu_name'];
				$reData[$value['spu_id']]['spu_index'] = $value['spu_index'];
				$reData[$value['spu_id']]['spu_status'] = $value['spu_status'];
				$reData[$value['spu_id']]['qcode'] = $value['qcode'];
			}
			if(!isset($reData[$value['spu_id']]['skuStoData']['data'][$value['sto_id']]))
			{	
				$reData[$value['spu_id']]['skuStoData']['data'][$value['sto_id']]['sto_id'] = $value['sto_id'];
				$reData[$value['spu_id']]['skuStoData']['data'][$value['sto_id']]['sto_name'] = $stoData[$value['sto_id']];
				$reData[$value['spu_id']]['skuStoData']['data'][$value['sto_id']]['skuStoData'] = null;
			}
			$tmpData = null;
			$tmpData['sku_id'] = $value['sku_id'];
			$tmpData['sku_storage_id'] = $value['sku_storage_id'];
			$tmpData['spec_name'] = $value['spec_name'];
			$tmpData['stock'] = $value['stock'];
			$tmpData['unit_price'] = $value['unit_price'];
			$tmpData['sku_sto_index'] = $value['sku_sto_index'];
			$tmpData['sku_sto_status'] = $value['sku_sto_status'];
			$tmpData['sn'] = $value['sn'];
			$reData[$value['spu_id']]['skuStoData']['data'][$value['sto_id']]['skuStoData'][] = $tmpData;

		}
		return $reData;
	}

    /**创建公司内账需要用到的商品
     * 考虑到之后扩展多级类别，这里将SKU表字段，当做财务项目类别字段
     * @param $sku_class 类别来源 用来区分的字段（0.星云进销存，1.易企记）
     * @param $spec_name 类别名
     * @param $sku_index 优先级
     * @param $status 状态
     * @author DizzyLee<728394036@qq.com>
     */
    public function createFinanceCart(array $data = null,$isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans) $this->startTrans();
            if (!isset($data['sku_class']))
            {
                $data['sku_class'] = 1;
            }
            $data['status'] = intval($data['status']);
            //判断有没有商店锁
            $shopLockStatus = D('Config')->getLockShopStatus(true);
            if ($shopLockStatus['status'] === 1)
                throw new \XYException(__METHOD__,-10507);

            if (!$this->field('spec_name,sku_index,sku_class,status')->create($data,self::SkuModel_createFinanceCart))
                throw new \XYException(__METHOD__,$this->getError());
            $this->status = 1;
            $spu = D('Spu')->field('spu_id,spu_name')->where(array('spu_class' => 1,'admin_uid' => getAdminUid()))->find();
            $cart = D('Cat')->field('cat_id,cat_name')->where(array('cat_class' => 1,'admin_uid' => getAdminUid()))->find();
            $this->spu_id = $spu['spu_id'];
            $this->cat_id = $cart['cat_id'];
            $this->spu_name = $spu['spu_name'];
            $this->cat_name = $cart['cat_name'];
            $this->sn = $this->getNextSn('SKU');
            $tmp = $this->add();
            if ($tmp > 0)
            {
                if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                return $tmp;
            }
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
     * 查询已经存在的财务项目类别
     * @api
     * @param unsigned $type 模式 1-所有有效financeCart 2.#TODO按多级分类查找financeCart
     * @param int status
     * @return $tmpRe 项目类别
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function queryFinanceCart($data = null)
    {
        switch (intval($data['type']))
        {
            case '1':{
                $map['admin_uid'] = session('user_auth.admin_uid');
                $map['sku_class'] = 1;
                $map['status'] = array('in','0,1');
                if (isset($data['status']))
                    $map['status'] = $data['status'];
                $tmpRe = D('Sku')->where($map)->order('sku_index,sku_id')->select();
                break;
            }
            case '2':{
                $map['admin_uid'] = session('user_auth.admin_uid');
                $map['sku_class'] = 1;
                $map['status'] = array('in','1');
                if (isset($data['status']))
                    $map['status'] = $data['status'];
                $tmpRe = D('Sku')->where($map)->order('sku_index,sku_id')->select();
                break;
            }
            #TODO 多级分类查找
//            case '2':{
//                $tmpRe = D('Sku')->where(array('admin_uid'=>session('user_auth.admin_uid'),'status'=>1,'cat_status'=>1,'spu_status'=>1,'sku_class' => 1))->order('sku_index,spu_index,cat_index,cat_id,spu_id,sku_id')->select();
//                break;
//            }
            default:
                throw new \XYException(__METHOD__,-5013);
        }

        if ($tmpRe === false)
            throw new \XYException(__METHOD__,-5000);

        return $tmpRe;
    }

    /**
     * 删除财务类别
     * @api
     * @param mixed|null $data POST的数据
     * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
     * @param unsigned_int $sku_id 需要删除的类别ID
     * @return 1 成功
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    public function deleteFinanceCart(array $data = null,$isAlreadyStartTrans = false)
    {
        try
        {
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

            //判断有没有商店锁
            $shopLockStatus = D('Config')->getLockShopStatus(true);
            if ($shopLockStatus['status'] === 1)
                throw new \XYException(__METHOD__,-10507);
            if (!($this->field('sku_id')->create($data,self::MODEL_UPDATE)))
                throw new \XYException(__METHOD__,$this->getError());
            $querydata = D('Sku')->where(array('sku_id' => $this->sku_id))->find();
            if ($querydata['sku_class'] != 1)
                throw new \XYException(__METHOD__,-5507);
            if ($querydata['status'] == -1)
                throw new \XYException(__METHOD__,-5508);
            $querydata['status'] = -1;
            $querydata['spec_name'] = $querydata['spec_name'].C('DELETED_MARKER');
            $operate = D('Sku')->where(array('sku_id' => $this->sku_id))->save($querydata);
            if ($operate === false || $operate === null)
                throw new \XYException(__METHOD__,-8000);
            if ($operate == 0)
                return '未做任何修改';
            if ($operate > 0)
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
    /**编辑类别
     * 考虑到之后扩展多级类别，这里将SKU表字段，当做财务项目类别字段
     * @param $sku_id 主键
     * @param $spec_name 类别名
     * @param $sku_id
     * @param $sku_index 优先级
     * @param $status 状态
     * @author DizzyLee<728394036@qq.com>
     */
    public function editFinanceCart(array $data = null,$isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans) $this->startTrans();
            $data['status'] = intval($data['status']);
            if (!$this->field('sku_id,spec_name,sku_index,status')->create($data,self::SkuModel_editFinanceCart))
                throw new \XYException(__METHOD__,$this->getError());
            $query_data = M('Sku')->where(array('sku_id' => $this->sku_id))->find();
            if ($query_data['sku_class'] != 1)
                throw new \XYException(__METHOD__,-5507);
            $past_data = $query_data = M('Sku')->where(array('spec_name' => $this->spec_name))->find();
            if (is_array($past_data)&&$past_data['sku_id']!= $query_data['sku_id'])
                throw new \XYException(__METHOD__,-5022);

            $operate = $this->where(array('sku_id'=>$this->sku_id))->save();
            if ($operate > 0)
            {
                if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
                return 1;
            }
            if ($operate == 0)
            {
                return '未进行任何修改';
            }
            if ($operate === null&&$operate === false)
                throw new \XYException(__METHOD__,-8000);
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
