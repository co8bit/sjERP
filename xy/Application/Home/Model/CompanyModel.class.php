<?php
namespace Home\Model;
use Think\Model\AdvModel;
use Home\Model\QnUploadModel;
use Home\Model\QueryModel;
/**
 * 往来单位Model.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误编码:{@see \ErrorCode\ErrorCode()}
 */
class CompanyModel extends BaseadvModel
{
	/*数据库字段*/
	// protected $fields = array('cid', 'admin_uid', 'name', 'qcode','mobile','address','remark','balance','status','reg_time','update_time','lock_version','type');
	// protected $pk     = 'cid';

	/* 自动验证 */
	protected $_validate = array(
		array('cid', 'checkDeny_cid', -6001, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//cid不合法
		array('name', '1,45', -6002, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //单位名称不合法
		array('name', 'not_checkDeny_depend_companyName', -6002, self::MUST_VALIDATE, 'callback',self::MODEL_INSERT), //单位名称不合法
		array('qcode', '1,20', -6003, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //qcode长度不合法
		array('mobile', '0,25', -6004, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //mobile长度不合法
		array('address', '0,60', -6006, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //address长度不合法
		array('remark', '0,1000', -6009, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //remark长度不合法
		array('status', 'checkDeny_bool_status', -6013, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH), //status长度不合法

        //companyTransactionStatistics
        array('cid','checkDeny_cid',-6001,self::MUST_VALIDATE,'callback',self::CompanyModel_companyTransactionStatistics), ///cid不合法

	);

	

	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
	);




	/**
	 * 新建往来单位.
	 * 
	 * 会自动创建一个name、mobile、car_license的联系人
	 * 
	 * @api
	 * @param mixed $data post来的数据
	 * @param bool $isAlreadyStartTrans 是否已经开启事务 @internal
	 * @param string $name 单位名称
	 * @param string $qcode 速查码
	 * @param string $address 地址
	 * @param string $remark 备注
	 * @param double $init_payable 期初应付款{透传}
	 * @param enum $status 0|1:是否启用
	 * @param json $contact json传入联系人信息
	 * @example json:
	 * 		[
	 *			{
	 *				"contact_name":"wbx",
	 *				"phonenum":[{"mobile":"15023658955"},{"mobile":"57128121110"}],
	 *				"car_license":[{"car_license":"浙AA5202"},{"car_license":"car_license"}],
	 *			},
	 *			{
	 *				"contact_name":"lalalala",
	 *				"phonenum":[{"mobile":"15236555555"}],
	 *				"car_license":[{"car_license":"浙BB5204"},{"car_license":"car_license2"}],
	 *			}
	 *		]
	 *	@example mini: [{"contact_name":"wbx","phonenum":[{"mobile":"15023658955"},{"mobile":"57128121110"}],"car_license":[{"car_license":"浙AA5202"},{"car_license":"car_license"}]},{"contact_name":"lalalala","phonenum":[{"mobile":"15236555555"}],"car_license":[{"car_license":"浙BB5204"},{"car_license":"car_license2"}]}]
	 *		
	 * @return unsigned_int 成功返回cid > 0
	 * @throws \XYException
	 */
	public function create_(array $data = null,$isAlreadyStartTrans = false)
	{
		$dbContact = D('Contact');
		$dbFinance = D('Finance');
		$dbOrder   = D('Order');

		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$data['status'] = intval($data['status']);

			//1.创建往来单位基本信息
			if (!$this->field('name,qcode,address,remark,status')->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());

			if (isset($data['init_payable']))
				$init_payable = $data['init_payable'];
			else
				$init_payable = I('param.init_payable/f');
			if(is_numeric($init_payable))
				$init_payable = xyround($init_payable);
			else
				throw new \XYException(__METHOD__,-6010);
			$this->balance = 0;
			$this->sn      = $this->getNextSn('CSN');

			$cid = $this->add();
			if ($cid <= 0)
				throw new \XYException(__METHOD__,-6000);

			//2.创建期初欠付帐（销售、采购单）
			$tmpClass = 0;
			if ($init_payable < 0)
			{
				$tmpClass = 1;//销售单
				$tmpRemark = '期初应收款';
				$tmpStoId = 1;
				$init_payable = 0 - $init_payable;
			}
			elseif ($init_payable > 0)
			{
				$tmpClass = 3;//采购单
				$tmpStoId = 1;
				$tmpRemark = '期初应付款';
			}
			else
			{
				$tmpClass = 0;
			}

			if ($tmpClass != 0)
			{
				$dbOrder->createOrder(array(
						'class'  => $tmpClass,
						'cid'    => $cid,
						'sto_id' => $tmpStoId,
						'remark' => $tmpRemark,
						'value' => $init_payable,
					),true,true);
			}

			//3.创建联系人
			//开始处理购物车
			$tmpTextCart = I('param.contact','','');
			if (empty($tmpTextCart))
				$cartTmp = $data['contact'];
			else
				$cartTmp = json_decode(I('param.contact','',''),true);
			if (empty($cartTmp))//因为下面的foreach是在调用createContact，所以当出现[{}]这种情况时，for执行，createContact返回错误，可以避免创建时联系人为空这种情况
				throw new \XYException(__METHOD__,-6050);

			foreach ($cartTmp as $value)
			{
				$dbContact->createContact(array(
						'cid'          => $cid,
						'contact_name' => I('data.contact_name/s','','htmlspecialchars',$value),
						'cart'         => $value,
					),true,true);
			}
			//添加图片
			if (!empty($data['image_base64']))
            {
                $qn = new QnUploadModel();
                $name = $cid.'-face';
                $ret = $qn->uploadImgToQiniu($data['image_base64'],$name);
                $image_url = $qn->getImgUrlByKey(C('QINIU_DOMAIN'),$ret['key']);
                $operate = D('Company')->where(array('cid' => $cid))->save(array('image_url' => $image_url));
                if ($operate ===null || $operate === false)
                    throw new \XYException(__METHOD__,-8000);

                //添加face_token
                $base64_image = str_replace(' ', '+', $data['image_base64']);
		        //匹配图片格式,在本地暂存
		        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image, $result))
		            $type = $result[2];
		        if (!($type == 'png'||$type == 'jpg'))
		            throw new \XYException(__METHOD__,-30003);
		        $filePath = APP_PATH."../Public/Uploads/";
		        if(!file_exists($filePath))
		        {
		            mkdir($filePath, 0777);
		        }
		        $fileName = $filePath.getUid().time().'.'.$type;
		        if (!file_put_contents($fileName, base64_decode(str_replace($result[1], '', $base64_image))))
		            throw new \XYException(__METHOD__,-30002);
		        D('FaceRec')->upload_pic(array(
		        	'cid' =>$cid,
		        	'fileName' =>$fileName
		        	),true);
		        //删除暂存文件
		        if (file_exists($fileName))
            		unlink($fileName);



            }
			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			
			return $cid;
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
	 * 编辑往来单位.
	 * 
	 * - @param mixed|null $data POST的数据
	 * - @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * -
	 * - @param unsigned_int $cid，必填
	 * - @param string $name 单位名称
	 * - @param string $qcode 速查码
	 * - @param string $address 地址
	 * - @param string $remark 备注
	 * - @param enum $status 0|1:是否启用
	 * -
	 * - 可选参数（以下字段任选一个以上）：
	 * - @param json $contact 联系人信息（没有的时候请传null）。新增和删除的时候contact_id=0
	 * @example json:
	 *			[
	 *				{
	 *					"contact_id":1,
	 *					"contact_name":"wbx",
	 *					"phonenum":[
	 *						{
	 *							"phonenum_id":1,
	 *							"mobile":"15023658955"
	 *						},
	 *						{
	 *							"phonenum_id":2,
	 *							"mobile":"57128121110"
	 *						}
	 *					],
	 *					"car_license":[
	 *						{
	 *							"carlicense_id":1,
	 *							"car_license":"浙AA5202"
	 *						},
	 *						{
	 *							"carlicense_id":2,
	 *							"car_license":"car_license"
	 *						}
	 *					]
	 *				},
	 *				{
	 *    				"contact_id":0,
	 *    				"new":1,
	 *    				"contact_name":"contact_name",
	 *					"phonenum":[
	 *						{
	 *					  		"mobile":"15236555555"
	 *						}
	 *					],
	 *					"car_license":[
	 *						{
	 *							"car_license":"浙BB5204"
	 *						},
	 *						{
	 *							"car_license":"car_license2"
	 *						}
	 *					]
	 *				},
	 *				{
	 *    				"contact_id":2,
	 *    				"delete":1
	 *				}
	 *			]
	 * @example mini: [{"contact_id":1,"contact_name":"wbx","phonenum":[{"phonenum_id":1,"mobile":"15023658955"},{"phonenum_id":2,"mobile":"57128121110"}],"car_license":[{"carlicense_id":1,"car_license":"浙AA5202"},{"carlicense_id":2,"car_license":"car_license"}]},{"contact_id":0,"new":1,"contact_name":"contact_name","phonenum":[{"mobile":"15236555555"}],"car_license":[{"car_license":"浙BB5204"},{"car_license":"car_license2"}]},{"contact_id":2,"delete":1}]
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 * @api
	 */
	public function edit_(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$data['status'] = intval($data['status']);

			$dbContact = D('Contact');

			if (!$this->field('cid,name,qcode,address,remark,status')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());
			
			$tmpCheckData = M('Company')->where(array('cid'=>$this->cid,'admin_uid'=>$this->admin_uid))->find();
			if ($tmpCheckData['name']    == $this->data['name']) unset($this->data['name']);
			if ($tmpCheckData['qcode']   == $this->qcode) unset($this->qcode);
			if ($tmpCheckData['address'] == $this->address) unset($this->address);
			if ($tmpCheckData['remark']  == $this->remark) unset($this->remark);
			if ($tmpCheckData['status']  == $this->status) unset($this->status);

			if (isset($this->data['name']))//修改了名字的话
			{
				if ($this->checkDeny_depend_companyName($this->data['name']))//name-admin_uid键值对已经存在了
					throw new \XYException(__METHOD__,-6005);
			}


			$cartTmp = json_decode(I('param.contact','',''),true);
			if (empty($cartTmp))//因为下面的foreac会判断是否存在contact_id，所以当出现[{}]这种情况时，for执行，返回错误，可以避免编辑时联系人为空这种情况
				throw new \XYException(__METHOD__,-6050);

			foreach ($cartTmp as $value)
			{
				if (
						( isset($value['contact_id']) && isNonnegativeInt($value['contact_id']) )//contact_id只能用isNonnegativeInt验证，因为当其为0时代表新增/delete
					)
				{
					if ( isset($value['new']) && ($value['new'] == 1) )//新增
					{
						$dbContact->createContact(array(
							'cid'          => $this->cid,
							'contact_name' => I('data.contact_name/s','','htmlspecialchars',$value),
							'cart'         => $value,
						),true,true);
					}
					elseif ( isset($value['delete']) && ($value['delete'] == 1) )//删除
					{
						$dbContact->deleteContact(array(
							'contact_id' => I('data.contact_id/d',0,'htmlspecialchars',$value)
						));
					}
					else//修改
					{
						if (isset($value['contact_name']))
							$dbContact->edit_(array(
								'contact_id'   => I('data.contact_id/d',0,'htmlspecialchars',$value),
								'cid'          => $this->cid,
								'contact_name' => I('data.contact_name/s','','htmlspecialchars',$value),
								'contact'      => $value,
							),true,true);
						else
							$dbContact->edit_(array(
								'contact_id'   => I('data.contact_id/d',0,'htmlspecialchars',$value),
								'cid'          => $this->cid,
								'contact'      => $value,
							),true,true);
					}
				}
				else
					throw new \XYException(__METHOD__,-6055);
			}

		
			$tmp = $this->where(array('cid'=>$this->cid,'admin_uid'=>$this->admin_uid))->save();
			if ( ($tmp === null) || ($tmp === false) )
				throw new \XYException(__METHOD__,-6000);

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
	 * 删除往来单位
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * 
	 * @param unsigned_int $cid
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteCompany(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if (!($this->field('cid')->create($data,self::MODEL_UPDATE)))
				throw new \XYException(__METHOD__,$this->getError());

			//没有发生业务往来，即没有开任何单子才能被删除
			//检查有没有业务往来
			// $queryData = D('Query')->query_(array(
			// 		'page'  => 1,
			// 		'pline' => 2,
			// 		'cid'   => $this->cid,
			// 	));
			// log_("queryData",$queryData,$this);

			// if ( !empty($queryData['data']) )//如果为空则通过，不为空则继续检查是不是只有一个系统自动建立的期初账目，如果是，则通过，否则不能被删除
			// {
			// 	if (!empty($queryData['data'][1]))//如果有两条记录，又因为期初帐只可能有1条，所以一定有往来，该往来单位不能被删除
			// 		throw new \XYException(__METHOD__,-6020);
			// 	elseif ($queryData['data'][0]['status'] == 99)//如果只有一条记录，而且是自动创建的期初，则通过
			// 		;
			// 	else
			// 		throw new \XYException(__METHOD__,-6020);
			// }
			
			$cid = $this->cid;

			$tmp = $this->where(array('cid'=>$this->cid,'admin_uid'=>$this->admin_uid))->delete();

			if ( empty($tmp) )
				throw new \XYException(__METHOD__,-6000);

			D('Order')->delete_cid(array('cid'=>$cid),true);
			
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
	 * 查询往来单位列表
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param enum $type 1|2 模式:1-所有company 2-有效company，即status=1的company
	 * 
	 * @return array 数据库中的一行cid,name,qcode,address,remark,balance,status
	 * @throws \XYException
	 */
	public function queryList(array $data = null)
	{
		switch ($data['type'])
		{
			case '1':{
				$tmpRe = $this->where(array('admin_uid'=>session('user_auth.admin_uid')))->field('cid,name,qcode,address,remark,balance,status')->order('status desc')->select();
				break;
			}
			case '2':{
				$tmpRe = $this->where(array('admin_uid'=>session('user_auth.admin_uid'),'status'=>1))->field('cid,name,qcode,address,remark,balance,status')->order('status desc')->select();
				break;
			}
			default:
				throw new \XYException(__METHOD__,-6504);
		}

		if ($tmpRe === false)
			throw new \XYException(__METHOD__,-6000);

		return $tmpRe;
	}



	/**
	 * 查询具体的某个往来单位的信息
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $cid
	 *
	 * @return array 数据库中的一行，json格式为{"data":{数据库中的一行}}
	 * @throws \XYException
	 */
	public function get_(array $data = null)
	{
		if (!($this->field('cid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpRe = $this->where(array('cid'=>$this->cid,'admin_uid'=>session('user_auth.admin_uid')))->find();

		if ($tmpRe === false)
			throw new \XYException(__METHOD__,-6000);
		elseif ($tmpRe === null)
			throw new \XYException(__METHOD__,-6001);

		$tmpRe['contact'] = $contactData = D('Contact')->queryList(array('cid'=>$this->cid));

		return $tmpRe;
	}

    /**查询该账号下所有公司联系人方法
     *
     * @return mixed 当前账号下所有公司与其联系人
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
	public function get_All()
    {
        $tmpRe = $this->where(array('admin_uid'=>session('user_auth.admin_uid')))->select();
        if ($tmpRe === false)
            throw new \XYException(__METHOD__,-6000);
        elseif ($tmpRe === null)
            throw new \XYException(__METHOD__,-6001);

        foreach ($tmpRe as $k => $v)
        {
            $tmpRe[$k]['contact'] = D('Contact')->queryList(array('cid'=>$v['cid']));
        }
        return $tmpRe;
    }



	/**
	 * 获得cid的公司结余.
	 * 
	 * note: 使用加锁时必须是外环境打开了事务，本函数中不做任何事务处理
	 * 
	 * @api
	 * @param  unsigned_int $cid
	 * @param  bool $isLock @server:是否加锁
	 * @param  enum $type 0-不限制状态，1-状态为1
	 * 
	 * @return double 该公司的结余
	 * @return <=0 错误
	 *
	 * @todo :锁
	 */
	public function getBalance(array $data = null,$isLock = false,$type)
	{
		if (!($this->field('cid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());
		
		if ($type == 0)
			$tmp = $this->where(array('cid'=>$this->cid,'admin_uid'=>session('user_auth.admin_uid')))->lock($isLock)->getField('balance');
		else
			$tmp = $this->where(array('cid'=>$this->cid,'status'=>1,'admin_uid'=>session('user_auth.admin_uid')))->lock($isLock)->getField('balance');
		
		if ($tmp === false)
			throw new \XYException(__METHOD__,-6000);
		elseif ($tmp === null)
			throw new \XYException(__METHOD__,-6001);

		return $tmp;
	}



	/**
	 * 获得cid为$cid的公司的名字
	 * @internal server
	 * 
	 * @param unsigned_int $cid 要查询的cid
	 * @param unsigned_int $type 查询类型，0-查所有状态下的name，1-status=1的名字
	 * 
	 * @return String 名称
	 * @throws \XYException
	 */
	public function getCompanyName($cid,$type)
	{
		if ($type === 0)
			$tmp = $this->where(array('cid'=>$cid,'admin_uid'=>getAdminUid()))->getField('name');
		else
			$tmp = $this->where(array('cid'=>$cid,'admin_uid'=>getAdminUid(),'status'=>1))->getField('name');
		log_("cid",$cid,$this);
		log_("admin_uid",getAdminUid(),$this);
		if ($tmp === null)
			throw new \XYException(__METHOD__,-6503);
		if ($tmp === false)
			throw new \XYException(__METHOD__,-6000);
		
		return $tmp;
	}



	/**
	 * 用于在Order类中更新往来单位余额.
	 * 注意：外部需打开实务才可以.
	 * 
	 * @internal server
	 * 
	 * @param unsigned_int $cid
	 * @param double $income 实收（现金+银行+网上支付）金额
	 * @param enum $type 0-往来单位状态无限制，1-往来单位状态=1
	 *
	 * @return true 更新成功
	 * @throws \XYException
	 */
	public function setBalanceInOrder($cid,$income,$type)
	{
		if ($income == 0)
			throw new \XYException(__METHOD__,-6012);

		//$income,负数是别人欠店主，应收
		//$income,正数是店主欠别人，应付

		if ($type == 0)
			$preBalance = M('Company')->where(array('cid'=>$cid,'admin_uid'=>getAdminUid()))->lock(true)->getField('balance');
		else
			$preBalance = M('Company')->where(array('cid'=>$cid,'admin_uid'=>getAdminUid(),'status'=>1))->lock(true)->getField('balance');

		if ( ($preBalance === false) || ($preBalance === null) )
			throw new \XYException(__METHOD__,-6506);

		$tmpInfo                = null;
		$tmpInfo['balance']     = xyadd($preBalance,$income);
		$tmpInfo['update_time'] = NOW_TIME;

		if ($type == 0)
			$tmp = $this->where(array('cid'=>$cid,'admin_uid'=>getAdminUid()))->save($tmpInfo);
		else
			$tmp = $this->where(array('cid'=>$cid,'admin_uid'=>getAdminUid(),'status'=>1))->save($tmpInfo);

		if (empty($tmp))
			throw new \XYException(__METHOD__,-6000);

		return true;
	}



	/**
	 * 用于在Finance类中更新往来单位余额.
	 * 注意：外部需打开实务才可以
	 * 
	 * @internal server
	 * 
	 * @param unsigned_int $cid
	 * @param double $income 实收（现金+银行）金额
	 * @param unsigned_int $class Finance中的单据类别.当其为-1时，为Order:::deleteOrder()调用
	 *
	 * @return true 更新成功
	 * @throws \XYException
	 * 
	 * @todo 测试锁
	 */
	public function setBalanceInFinance($cid,$income,$class)
	{
		if ($income == 0)
			throw new \XYException(__METHOD__,-6012);

		if ($class == 72)//付款单
			$income = 0 - $income;
		$preBalance = M('Company')->where(array('cid'=>$cid,'admin_uid'=>getAdminUid(),'status'=>1))->lock(true)->getField('balance');
		if ( ($preBalance === false) || ($preBalance === null) )
			throw new \XYException(__METHOD__,-6505);

		$tmpInfo                = null;
		$tmpInfo['balance']     = xyadd($preBalance, $income);
		$tmpInfo['update_time'] = NOW_TIME;

		$tmp = $this->where(array('cid'=>$cid,'admin_uid'=>getAdminUid(),'status'=>1))->save($tmpInfo);
		if (empty($tmp))
			throw new \XYException(__METHOD__,-6000);
		
		return true;
	}



	 /**
	 * 查询某个cid内的还需付款或还需收款的单据
	 * @note 使用加锁时必须是外环境打开了事务，本函数中不做任何事务处理
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param bool $isLock 是否加锁 @internal
	 * @param array $oidList 当$isLock为true的时候，请传入这一项，要被锁定的oid列表 @internal $oidList[i]=10;
	 * 
	 * @param enum $type '1|2':1-店铺还需收款的单据；2-店铺还需付款的单据
	 * @param unsigned_int $cid
	 *
	 * @throws \XYException
	 * @return array 数据库中的一行，按reg_time递增。json格式为[{"5":{数据库中的一行}},{"8":{..}}]
	 * @todo 强制检查oidList的每一项都是数字key=>数字value的键值对。这个操作是锁表还是锁行？
	 */
	public function queryRemain(array $data = null,$isLock = false,array $oidList = null)
	{
		if (!($this->field('cid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());
		
		$map['_string']  = ' (status <> 91) AND (status <> 3) ';

		if ( ($data['type'] === 1) || ($data['type'] === '1') )
			$map['remain']  = array('gt',0);
		elseif ( ($data['type'] === 2) || ($data['type'] === '2') )
			$map['remain'] = array('lt',0);
		else
			throw new \XYException(__METHOD__,-6501);

		if ($isLock)
		{
			if (empty($oidList))
				throw new \XYException(__METHOD__,-6502);
			else
				$map['oid']  = array('in',$oidList);
		}

		$sql = D('Order')
			->where($map)
			->where(array('cid'=>$this->cid,'admin_uid'=>session('user_auth.admin_uid')))
			->order('reg_time')
			->lock($isLock)
			->select(false);
		log_("sql",$sql,$this);

		$reData = D('Order')
			->where($map)
			->where(array('cid'=>$this->cid,'admin_uid'=>session('user_auth.admin_uid')))
			->order('reg_time')
			->lock($isLock)
			->getField('oid,sn,class,value,off,receivable,remain,operator_uid,operator_name,leave_time,status,reg_time');
			// ->field('oid,class,receivable,remain,operator_uid,operator_name,leave_time,status,reg_time')
			// ->select();
		$reData['totalRemain'] = 0 ;
		foreach($reData as $key=>$value)
		{
			$reData['totalRemain'] += abs($value['remain']);
		}

		if ( ($reData === false) )
			throw new \XYException(__METHOD__,-6000);

		return $reData;
	}

	/**
	 * 请求往来单位相关单据
	 * @internal
	 * @param mixed|null $data POST的数据
	 * @param unsigned_int $cid 往来单位主键
	 * @param json $idList 选中的id，只能选oid或fid。且不能选择状态为100的单据。
	 * @example json:
	 *			[
	 *				{
	 *					"id":34,
	 *					"class":"1",//oid
	 *				},
	 *				{
	 *					"id":15,
	 *					"class":"2",//fid
	 *				}
	 *			]
	 * @example json mini: [{"id":34,"class":"1"},{"id":15,"class":"2"}]
	 *
	 * @return array
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.10
	 * @date 2017-5-25
	 */
	public function queryStamentOfAccount(array $data=null)
	{
		if (!($this->field('cid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		$cartTmp = json_decode(I('param.idList','',''),true);
        if (empty($cartTmp))
            throw new \XYException(__METHOD__,-6050);

        $oidList = null;
        $fidList = null;
        foreach ($cartTmp as $value)
        {
            if ( isUnsignedInt($value['id']) && (($value['class'] == 1) || ($value['class'] == 2)) )
            {
            	if ($value['class'] == 1)//oid
            	{
            		$oidList[] = $value['id'];
            	}
            	else//fid
            	{
            		$fidList[] = $value['id'];
            	}
            }
            else
                throw new \XYException(__METHOD__,-6051);
        }
        if ( empty($oidList) && empty($fidList) )
        	throw new \XYException(__METHOD__,-6050);

        if (!empty($oidList))
        {
			$map              = null;
			$map['oid']       = array('in',$oidList);
			$map['cid']       = $this->cid;
			$map['admin_uid'] = getAdminUid();
			$OrderSQL = D('Order')
				->where($map)
				->buildSql();
		}
		if (!empty($fidList))
        {
			$map              = null;
			$map['fid']       = array('in',$fidList);
			$map['cid']       = $this->cid;
			$map['admin_uid'] = getAdminUid();
			$FinanceSQL = D('Finance')
				->where($map)
				->buildSql();
		}

		if (empty($oidList))
			$unionSQL = $FinanceSQL;
		elseif (empty($fidList))
			$unionSQL = $OrderSQL;
		else
			$unionSQL = '( '.$OrderSQL.' UNION '.$FinanceSQL.' )';
		
		$queryData = $this
			->table($unionSQL.' tmp_union')
			->order('reg_time')
			->select();
		if ( empty($queryData) )
			throw new \XYException(__METHOD__,-6050);
		return $queryData;
	}



	/**
	 * web请求往来对账单的内容
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $cid 往来单位主键
	 * @param json $idList 选中的id，只能选oid或fid。且不能选择状态为99或100的单据。
	 * @example json:
	 *			[
	 *				{
	 *					"id":34,
	 *					"class":"1",//oid
	 *				},
	 *				{
	 *					"id":15,
	 *					"class":"2",//fid
	 *				}
	 *			]
	 * @example json mini: [{"id":34,"class":"1"},{"id":15,"class":"2"}]
	 *
	 * @return array
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.2
	 * @date    2016-11-27
	 */
	public function requestStatementOfAccount(array $data = null)
    {
		//验证数据是否合法
		if (!($this->field('cid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());
		//定义新的数组接收验证通过的数据
		$tmpData = $this->data;
		$cartTmp = json_decode(I('param.idList','',''),true);
		log_("cartTmp",$cartTmp,$this);
        if (empty($cartTmp))
            throw new \XYException(__METHOD__,-6050);
       	$tmpData['idList'] = $cartTmp;
       	$queryData = $this-> queryStamentOfAccount($tmpData);

		log_("queryData",$queryData,$this);
		$content = '';
		$key = 1;
		$totalMoney = 0;
		foreach ($queryData as $value)
		{
			if ( ($value['status'] == 100) )
				throw new \XYException(__METHOD__,-6051); //草稿单

			if ( ($value['class'] == 1) || ($value['class'] == 2) || ($value['class'] == 3) || ($value['class'] == 4) )
			{
				if ($value['status'] == 99)
				{
					if ($value['class'] == 1)
						$orderClass = '期初应收款，还需收您';
					elseif ($value['class'] == 3)
						$orderClass = '期初应付款，还需付您';
					else
						throw new \XYException(__METHOD__,-6051);
					$content .= '第'.$key.'张单据：'.date('Y-m-d日',$value['reg_time']).$orderClass.abs($value['remain']).'元。';
					$totalMoney += $value['remain'];
				}
				else
				{
					//将购物车换回json
					$cart = null;
					$tmpSerialize = null;
					$tmpSerialize = unserialize($value['cart']);
					if ($tmpSerialize === false)
						throw new \XYException(__METHOD__,-1050);
					$value['cart'] = $tmpSerialize;

					// log_("value",$value,$this);
					// log_("cart",$value['cart'],$this);

					if ($value['class'] == 1)
						$orderClass = '销售单';
					elseif ($value['class'] == 2)
						$orderClass = '销售退货单';
					elseif ($value['class'] == 3)
						$orderClass = '采购单';
					elseif ($value['class'] == 4)
						$orderClass = '采购退货单';
					$content .= '第'.$key.'张单据：'.date('Y-m-d日',$value['reg_time']).$orderClass.'，';
					$totalMoney += $value['remain'];
					foreach ($value['cart'] as $v)
					{
						$content .= $v['spu_name'].$v['spec_name'].$v['quantity'].'件X'.$v['unitPrice'].'元='.$v['pilePrice'].'元，';
					}
					$content = rtrim($content,'，');
					$content .= '。';
					$content .= '该单总计'.$value['value'].'元，';
					if ($value['off'] != 0)
						$content .= '优惠'.$value['off'].'元，';
					$content .= '该单还差'.abs($value['remain']).'元结清。';
				}
			}
			elseif( ($value['class'] == 5) || ($value['class'] == 6) )
			{
				if ($value['class'] == 5)
						$orderClass = '应收款增加单，还需收您';
					elseif ($value['class'] == 6)
						$orderClass = '应付款增加单，还需付您';
					else
						throw new \XYException(__METHOD__,-6051);
					$content .= '第'.$key.'张单据：'.date('Y-m-d日',$value['reg_time']).$orderClass.abs($value['remain']).'元。';
					$totalMoney += $value['remain'];
			}		
			elseif ( ($value['class'] == 71) || ($value['class'] == 72) )
			{
				//将购物车换回json
				$cart = null;
				$tmpSerialize = null;
				$tmpSerialize = unserialize($value['cart']);
				if ($tmpSerialize === false)
					throw new \XYException(__METHOD__,-3050);
				$value['cart'] = $tmpSerialize;
				
				// log_("value",$value,$this);
				// log_("cart",$value['cart'],$this);

				if ($value['class'] == 71)
					$orderClass = '收款单，收您：';
				elseif ($value['class'] == 72)
					$orderClass = '付款单，付您：';
				else
					throw new \XYException(__METHOD__,-6051);
				$content .= '第'.$key.'张单据：'.date('Y-m-d日',$value['reg_time']).$orderClass.abs($value['income']).'元。';
			}
			else
				throw new \XYException(__METHOD__,-6051);
			++$key;
		}
		if ( empty($content) )
			throw new \XYException(__METHOD__,-6050);
		$content = rtrim($content,'。');


		$reData            = null;
		$reData['dateSt']  = $queryData[0]['reg_time'];
		$reData['dateEnd'] = $queryData[$key-2]['reg_time'];
		$reData['money']   = $totalMoney;
		$reData['content'] = $content;
		$reData['sign']    = md5(C('SendSMSStatementOfAccount_KEY') . $reData['money'] . $reData['content'] );
		$reData['token']   = md5('xytoken'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'{'.getUid().'}'.NOW_TIME.rand(10000,99999));
		
		//preprocessing
		$dateSt   = date('Y-m-d日',$reData['dateSt']);
		$dateEnd  = date('Y-m-d日',$reData['dateEnd']);
		$shopName = D('User')->getUserInfo_shopName();
		$money    = $reData['money'];
        if ($money < 0)//????
            $moneyPre = '['.$shopName.']共欠您';
        else
            $moneyPre = '您共欠['.$shopName.']';
        $money   = $moneyPre.abs($money);

        //添加短信发送记录
		$sms_text   = '【星云进销存对账单】您好，从'.$dateSt.'到'.$dateEnd.'期间，'.$money.'元。详情如下：'.$content.'。星云进销存，让生意更简单。';
		$num        = $this->calcSMSNum($sms_text);
		$priceArray = C('MemberClass_smsMoney');
		$memberInfo = D('UserAccount')->get_();
		$expense    = $num * $priceArray[$memberInfo['member_class']];

		$reData['sms_text_last'] = $money.'元。详情如下：'.$content.'。星云进销存，让生意更简单。';
		$reData['num']           = $num;//短信条数
		$reData['expense']       = $expense;//短信费用
		
		// $session_key = MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'{'.getUid().'}';
		$session_key = strtoupper(MODULE_NAME.CONTROLLER_NAME.ACTION_NAME.getUid());
		log_("$session_key",$session_key,$this);
		session($session_key,$reData);
		return $reData;
    }

	/**
	 * 微信对账单
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $cid 往来单位主键
	 * @param string $pwd 客户获取账单随机密码
	 * @param json $idList 选中的id，只能选oid或fid。且不能选择状态为99或100的单据。
	 * @example json:
	 *			[
	 *				{
	 *					"id":34,
	 *					"class":"1",//oid
	 *				},
	 *				{
	 *					"id":15,
	 *					"class":"2",//fid
	 *				}
	 *			]
	 * @example json mini: [{"id":34,"class":"1"},{"id":15,"class":"2"}]
	 *
	 * @return array
	 * @throws \XYException
	 *
	 * @author wtt <wtt@xingyunbooks.com>
	 * @version 1.10
	 * @date    2017-5-27
	 */
	public function requestStatementOfWechatAccount(array $data = null)
	{
		//验证数据是否合法
		if (!($this->field('cid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		//定义新的数组接收验证通过的数据
		$tmpData = $this->data;
		$cartTmp = json_decode(I('param.idList','',''),true);
        if (empty($cartTmp))
            throw new \XYException(__METHOD__,-6050);
       	$tmpData['idList'] = $cartTmp;
       	$queryData = $this-> queryStamentOfAccount($tmpData);
       	log_("queryData",$queryData,$this);
       	$reg_time = null;
		foreach($queryData as $k=>$v)
		{
			if( $v['status'] == 100)
				throw new \XYException(__METHOD__,-6051);

			if( $v['class'] == 1 || $v['class'] == 2 || $v['class'] == 3 || $v['class'] == 4 || $v['class'] == 5 || $v['class'] == 6 || $v['class'] == 71 || $v['class'] == 72)
			{

			}
			else
			{
				throw new \XYException(__METHOD__,-6051);
			}
			$reg_time[$k] = $v['reg_time'];
		}
		array_multisort($reg_time, SORT_DESC, $queryData);//将数据按照创建时间排序

		// 构建存入数据库statement_account的数据
		$statement_account = array();
		$statement_account['statementofaccount'] = serialize($queryData);
		$s_guid = md5('xytoken'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'{'.getUid().'}'.NOW_TIME.rand(10000,99999));
		//判断s_guid是否重复
		$tmps_guid = D('statement_account')->getField('s_guid',true);
		$flag = true;
		while($flag)
		{
			if(in_array($s_guid,$tmps_guid) )
			{
				$s_guid = md5('xytoken'.MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME.'{'.getUid().'}'.NOW_TIME.rand(10000,99999));
			}else
			{
				$flag = false;
			}
		}
		$statement_account['s_guid'] = $s_guid;
		if((!isset($data['pwd'])) || empty($data['pwd']) )
			throw new \XYException(__METHOD__,-6023);
		else
			if(strlen($data['pwd']) == 4)
				$statement_account['s_pwd'] = I('param.pwd/s','','htmlspecialchars');
			else
				throw new \XYException(__METHOD__,-6023);

		/*if(empty($statement_account['s_pwd']))
			throw new \XYException(__METHOD__,-6023);*/
		log_("statement_account",$statement_account,$this);
		// 插入数据
		D('statement_account')->add($statement_account);

		// $url = U('Home/StatementOfAccount/inputPwd?s_guid='.$statement_account['s_guid']);
		
		return $s_guid ;
	}

	public function companyTransactionStatistics($data = null)
    {
        if (!$this->field('cid')->create($data, self::CompanyModel_companyTransactionStatistics))
            throw new \XYException(__METHOD__, $this->getError());
        $queryModel = new QueryModel();
        $map['page'] = 1;
        $map['pline'] = 100;
        $map['cid'] = $this->cid;
        $map['filter'] = json_encode(array('class' => array(1), 'status' => array(1)));
        $queryData = $queryModel->query_($map);
        //购买记录数据处理
        foreach ($queryData['data'] as $key => $value) {
            foreach ($value['cart'] as $key1 => $value1) {
                $recordData[] = array(
                    'sku_id' => $value1['sku_id'],
                    'quantity' => $value1['quantity'],
                    'unitPrice' => $value1['unitPrice'],
                    'pilePrice' => $value1['pilePrice'],
                    'spu_name' => $value1['spu_name'],
                    'spec_name' => $value1['spec_name'],
                    'cost' => $value1['cost'],
                    'reg_time' => $value['reg_time'],
                );
            }
        }
        //推荐商品
        $tmpRecom = array();
        foreach ($recordData as $key => $value) {
            $tmpRecom[$value['sku_id']]['quantity'] = xyadd($tmpRecom[$value1['sku_id']]['quantity'], $value['quantity']);
            $tmpRecom[$value['sku_id']]['spu_name'] = $value['spu_name'];
            $tmpRecom[$value['sku_id']]['spec_name'] = $value['spec_name'];
            $tmpRecom[$value['sku_id']]['cost'] = $value['cost'];
        }
        $recommend = array();
        foreach ($tmpRecom as $key => $value) {
            $recommend[] = array(
                'sku_id' => $key,
                'spu_name' => $value['spu_name'],
                'spec_name' => $value['spec_name'],
                'quantity' => $value['quantity'],
                'cost'     => $value['cost'],
            );
        }
        for ($i = 0; $i < count($recommend); $i++) {
            for ($j = 0; $j < $j - $i - 1; $j++) {
                if ($recommend[$j]['quantity'] < $recommend[$j + 1]['quantity']) {
                    $tmp = $recommend[$j];
                    $recommend[$j] = $recommend[$j + 1];
                    $recommend[$j + 1] = $tmp;
                }
            }
        }
        $returnData = array('record_data' => $recordData, 'recommend' => $recommend);
        return $returnData;
    }
}