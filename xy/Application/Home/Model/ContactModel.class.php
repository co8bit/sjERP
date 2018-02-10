<?php
namespace Home\Model;
use Think\Model;


/**
 * 联系人Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码（参考Company类）：{@see \ErrorCode\ErrorCode()}
 */
class ContactModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('contact_id', 'checkDeny_contact_id', -6011, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//contact_id不合法
		array('contact_id', 'checkDeny_contact_id', -6011, self::EXISTS_VALIDATE, 'callback',self::MODEL_QUERY),//contact_id不合法
		array('cid', 'checkDeny_cid', -6001, self::MUST_VALIDATE, 'callback',self::MODEL_UPDATE),//cid不合法
		array('cid', 'checkDeny_cid', -6001, self::EXISTS_VALIDATE, 'callback',self::MODEL_QUERY),//cid不合法
		array('contact_name', '1,45', -6007, self::MUST_VALIDATE, 'length',self::MODEL_INSERT), //联系人名称长度不合法
		array('contact_name', '1,45', -6007, self::EXISTS_VALIDATE, 'length',self::MODEL_UPDATE), //联系人名称长度不合法

		//ContactModel_deleteContact
		array('contact_id', 'checkDeny_contact_id', -6011, self::MUST_VALIDATE, 'callback',self::ContactModel_deleteContact),//contact_id不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),

		//ContactModel_deleteContact
		array('admin_uid','getAdminUid',self::ContactModel_deleteContact,'function'),//填入所属创建者uid
		array('update_time', NOW_TIME, self::ContactModel_deleteContact),
	);



	/**
	 * 创建联系人.
	 * 
	 * 最多可以创建：DEFAULT_INIT_MAX_LIMIT_CONTACT条
	 * @api
	 * @param mixed $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param bool $sysCall 是否是系统调用，如果true的话则cart字段已经转换成数组了. @internal
	 * 
	 * @param unsigned_int $cid
	 * @param string $contact_name 联系人名称
	 * @param json $cart
	 * @example json cart:
	 *				{
	 *					"phonenum":[
	 *						{
	 *							"mobile":"15023658955"
	 *						},
	 *						{
	 *							"mobile":"57128121110"
	 *						}
	 *					],
	 *					"car_license":[
	 *						{
	 *							"car_license":"浙AA5202"
	 *						},
	 *						{
	 *							"car_license":"car_license"
	 *						}
	 *					]
	 *				}
	 * @example mini: {"phonenum":[{"mobile":"15023658955"},{"mobile":"57128121110"}],"car_license":[{"car_license":"浙AA5202"},{"car_license":"car_license"}]}
	 * 
	 * @return unsigned_int 成功-contact_id > 0
	 * @throws \XYException
	 */
	public function createContact(array $data = null,$isAlreadyStartTrans = false,$sysCall = false)
	{
		try
		{
			$dbPhonenum = D('Phonenum');
			$dbCarlicense = D('Carlicense');
			
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if(!$this->field('cid,contact_name')->create($data,self::MODEL_INSERT))
				throw new \XYException(__METHOD__,$this->getError());

			$cid = $this->cid;//因为add后会清空
			
			//检查联系人是否达到上限
			$count = M('Contact')->where(array('cid'=>$this->cid,'admin_uid'=>$this->admin_uid))->lock(true)->count();
			if ($count >= C('DEFAULT_INIT_MAX_LIMIT_CONTACT'))
					throw new \XYException(__METHOD__,-6017);

			$contact_id = $this->add();
			if ($contact_id <= 0)
				throw new \XYException(__METHOD__,-6000);

			//开始处理购物车
			if ($sysCall)
				$cartTmp = $data['cart'];
			else
				$cartTmp = json_decode(I('param.cart','',''),true);
			if (empty($cartTmp))//因为允许只有联系人而没有电话和车牌号配套，所以当出现{"phonenum":[],"car_license":[]}这种情况时，就创建一个只有联系人没有车牌号和电话的就好了
				throw new \XYException(__METHOD__,-6050);

			if (	is_array($cartTmp['phonenum']) &&
					is_array($cartTmp['car_license'])
				)
			{
				//创建电话
				foreach ($cartTmp['phonenum'] as $tmp)
				{
					$tmpReturn = 0;
					$tmpReturn = $dbPhonenum->createPhonenum(array(
							'cid' => $cid,
							'contact_id' => $contact_id,
							'mobile' => I('data.mobile/s','','htmlspecialchars',$tmp),
						),true);
				}

				//创建车牌号
				foreach ($cartTmp['car_license'] as $tmp)
				{
					$tmpReturn = 0;

					$tmpReturn = $dbCarlicense->createCarlicense(array(
							'cid' => $cid,
							'contact_id' => $contact_id,
							'car_license' => I('data.car_license/s','','htmlspecialchars',$tmp),
						),true);
				}
			}
			else
				throw new \XYException(__METHOD__,-6050);

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
			return $contact_id;
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
	 * 编辑联系人信息.
	 * 
	 * note: 不作任何修改会返回成功
	 * @api
	 * @param mixed $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 * @param bool $sysCall 是否是系统调用，如果true的话则cart字段已经转换成数组了. @internal
	 *
	 * @param unsigned_int $contact_id
	 * @param unsigned_int $cid
	 * 可选参数：
	 * @param string $contact_name 联系人名称
	 * @param json $contact 联系人的电话、车牌号列表
	 * @example contact:
	 *             {
	 *					"phonenum":[
	 *						{
	 *							"phonenum_id":1,//要修改的电话号的id
	 *							"mobile":"15023658955"//电话号
	 *						},
	 *						{
	 *							"new":1,
	 *							"phonenum_id":0,
	 *							"mobile":"57128121110"
	 *						},
	 *						{
	 *							"delete":1,
	 *							"phonenum_id":1//要修改的电话号的id
	 *						}
	 *					],
	 *					"car_license":[
	 *						{
	 *							"carlicense_id":1,//要修改的车牌号的id
	 *							"car_license":"浙AA5202"//车牌号
	 *						},
	 *						{
	 *							"new":1,
	 *							"carlicense_id":0,
	 *							"car_license":"car_license"
	 *						},
	 *						{
	 *							"delete":1,
	 *							"carlicense_id":2
	 *						}
	 *					]
	 *				}
	 * @example mini: {"phonenum":[{"phonenum_id":1,"mobile":"15023658955"},{"new":1,"phonenum_id":0,"mobile":"57128121110"},{"delete":1,"phonenum_id":1}],"car_license":[{"carlicense_id":1,"car_license":"浙AA5202"},{"new":1,"carlicense_id":0,"car_license":"car_license"},{"delete":1,"carlicense_id":2}]}
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function edit_(array $data = null,$isAlreadyStartTrans = false,$sysCall = false)
	{
		try
		{
			$dbPhonenum = D('Phonenum');
			$dbCarlicense = D('Carlicense');


			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			if(!$this->field('contact_id,cid,contact_name')->create($data,self::MODEL_UPDATE))
				throw new \XYException(__METHOD__,$this->getError());

			if (!isset($data['contact_name'])) unset($this->data['contact_name']);

			$oData = $this->data;

			if (isset($this->data['contact_name']))//修改了名字的话
			{
				$contactRe = $this->where(array('contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->save();
			}


			$cartTmp = null;
			if ($sysCall)
				$cartTmp = $data['contact'];
			else
				$cartTmp = json_decode(I('param.contact','',''),true);
			$tmpParamContact = I('param.contact','','');
			if (!empty($tmpParamContact))//允许json为空，这里只转换失败时报错
				if ( empty($cartTmp) )
					throw new \XYException(__METHOD__,-6050);

			foreach ($cartTmp['phonenum'] as $value)
			{
				if (
						( isset($value['phonenum_id']) && isNonnegativeInt($value['phonenum_id']) )//id只能用isNonnegativeInt验证，因为当其为0时代表create/delete
					)
				{
					if ( isset($value['new']) && ($value['new'] === 1) )//新增
					{
						$dbPhonenum->createPhonenum(array(
							'cid'        => $oData['cid'],
							'contact_id' => $oData['contact_id'],
							'mobile'     => I('data.mobile/s','','htmlspecialchars',$value),
						),true);
					}
					elseif ( isset($value['delete']) && ($value['delete'] == 1) )//删除
					{
						$dbPhonenum->deletePhonenum(array(
							'phonenum_id' => I('data.phonenum_id/d',0,'htmlspecialchars',$value)
						));
					}
					else//修改
					{
						$dbPhonenum->editPhonenum(array(
							'contact_id'  => $oData['contact_id'],
							'phonenum_id' => I('data.phonenum_id/d',0,'htmlspecialchars',$value),
							'mobile'      => I('data.mobile/s','','htmlspecialchars',$value),
						));
					}
				}
				else
					throw new \XYException(__METHOD__,-6054);
			}



			foreach ($cartTmp['car_license'] as $value)
			{
				if (
						( isset($value['carlicense_id']) && isNonnegativeInt($value['carlicense_id']) )//id只能用isNonnegativeInt验证，因为当其为0时代表create/delete
					)
				{
					if ( isset($value['new']) && ($value['new'] == 1) )//新增
					{
						$dbCarlicense->createCarlicense(array(
							'cid'         => $oData['cid'],
							'contact_id'  => $oData['contact_id'],
							'car_license' => I('data.car_license/s','','htmlspecialchars',$value),
						),true);
					}
					elseif ( isset($value['delete']) && ($value['delete'] == 1) )//删除
					{
						$dbCarlicense->deleteCarlicense(array(
							'carlicense_id' => I('data.carlicense_id/d',0,'htmlspecialchars',$value)
						));
					}
					else//修改
					{
						$dbCarlicense->editCarlicense(array(
							'contact_id'    => $oData['contact_id'],
							'carlicense_id' => I('data.carlicense_id/d',0,'htmlspecialchars',$value),
							'car_license'   => I('data.car_license/s','','htmlspecialchars',$value),
						));
					}
				}
				else
					throw new \XYException(__METHOD__,-6056);
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
	 * 删除联系人
	 * @api
	 * @param unsigned_int $contact_id
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function deleteContact(array $data = null)
	{
		if(!$this->field('contact_id')->create($data,self::ContactModel_deleteContact))
			throw new \XYException(__METHOD__,$this->getError());
		
		$tmp = $this->where(array('contact_id'=>$this->contact_id,'admin_uid'=>$this->admin_uid))->delete();//TODO:目前这里是靠数据库的外键删除掉phonenum表和carlicense表里的相关东西的
		//TODO: 用不用加上cid进行验证？
		if ( empty($tmp) )
			throw new \XYException(__METHOD__,-6000);

		return 1;
	}



	/**
	 * 查询某个cid下的联系人列表
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $cid
	 *
	 * @throws \XYException
	 * @return 数据集为空，json格式为{"EC":1,"data":[]}
	 * @return:
	 * 1.数组格式：
	 * $reData['contact'] = array(
	 * 		['contact_id'] = contact_id,
	 * 		['contact_name'] = contact_name,
	 * 		['phonenum'] = array(
	 * 			['phonenum_id'] = ...,
	 * 			['mobile'] = ...,
	 * 		)
	 * 		[car_license] = array(
	 * 			[carlicense_id] = ...,
	 * 			['car_license'] = ...
	 * 		)
	 * 	)
	 * 
	 *
	 * 2.返回的数组有可能如下：（没有phonenum或car_license）
	 * array(3) {
	 *     ["contact_id"] => string(1) "1"
	 *     ["contact_name"] => string(9) "外婆家"
	 *     ["phonenum"] => array(2) {
	 *       [0] => array(2) {
	 *         ["phonenum_id"] => string(1) "1"
	 *         ["mobile"] => string(11) "15023658955"
	 *       }
	 *       [1] => array(2) {
	 *         ["phonenum_id"] => string(1) "9"
	 *         ["mobile"] => string(6) "189663"
	 *       }
	 *     }
	 *  }
	 *
	 * 3.完整json返回样例：
	 * {
	 *     "EC": 1, 
	 *     "data": [
	 *         {
	 *             "contact_id": "1", 
	 *             "contact_name": "外婆家", 
	 *             "phonenum": [
	 *                 {
	 *                     "phonenum_id": "1", 
	 *                     "mobile": "15023658955"
	 *                 }, 
	 *                 {
	 *                     "phonenum_id": "9", 
	 *                     "mobile": "189663"
	 *                 }
	 *             ], 
	 *             "car_license": [
	 *                 {
	 *                     "carlicense_id": "1", 
	 *                     "car_license": "浙AA5202"
	 *                 }, 
	 *                 {
	 *                     "carlicense_id": "8", 
	 *                     "car_license": "car_license"
	 *                 }, 
	 *                 {
	 *                     "carlicense_id": "9", 
	 *                     "car_license": "car_license45645"
	 *                 }
	 *             ]
	 *         }, 
	 *         {
	 *             "contact_id": "6", 
	 *             "contact_name": "wbxaa", 
	 *             "phonenum": [
	 *                 {
	 *                     "phonenum_id": "5", 
	 *                     "mobile": "57128121110"
	 *                 }
	 *             ],
	 *         }, 
	 *         {
	 *             "contact_id": "7", 
	 *             "contact_name": "wbxaa",
	 *             "car_license": [
	 *                 {
	 *                     "carlicense_id": "6", 
	 *                     "car_license": "car_license"
	 *                 }
	 *             ]
	 *         }, 
	 *         {
	 *             "contact_id": "8", 
	 *             "contact_name": "wbxaa",
	 *         }
	 *     ]
	 * }
	 */
	public function queryList(array $data = null)
	{
		if (!($this->field('cid')->create($data,self::MODEL_QUERY)))
			throw new \XYException(__METHOD__,$this->getError());
			

		// $tmpRe = $this
		// 	->where(array('contact.cid'=>$this->cid,'contact.admin_uid'=>session('user_auth.admin_uid')))
		// 	->join('phonenum ON contact.contact_id = phonenum.contact_id','LEFT')
		// 	->join('carlicense ON contact.contact_id = carlicense.contact_id','LEFT')
		// 	->field('contact.contact_id,contact.contact_name,contact.balance,phonenum.mobile,carlicense.car_license')
		// 	->select();
			
		$tmpData['contact'] = $this
			->where(array('cid'=>$this->cid,'admin_uid'=>session('user_auth.admin_uid')))
			->order('contact_id')
			->field('contact_id,contact_name')
			->select();
		if ($tmpData['contact'] === false)
			throw new \XYException(__METHOD__,-6000);
		elseif ($tmpData['contact'] === null)
			throw new \XYException(__METHOD__,-6001);
			
		$tmpData['phonenum'] = D('Phonenum')->queryListInCid(array('cid'=>$this->cid));
		$tmpData['car_license'] = D('Carlicense')->queryListInCid(array('cid'=>$this->cid));

		// dump($tmpData['contact']);
		// dump($tmpData['phonenum']);
		// dump($tmpData['car_license']);
		foreach ($tmpData['contact'] as $key => $value)
		{
			foreach ($tmpData['phonenum'] as $mobileValue)
			{
				if ($mobileValue['contact_id'] == $value['contact_id'])
				{
					$tmpData['contact'][$key]['phonenum'][] = array(
							'phonenum_id'=>$mobileValue['phonenum_id'],
							'mobile'=>$mobileValue['mobile'],
						);
				}
			}

			foreach ($tmpData['car_license'] as $carlicenseValue)
			{
				if ($carlicenseValue['contact_id'] == $value['contact_id'])
				{
					$tmpData['contact'][$key]['car_license'][] = array(
							'carlicense_id'=>$carlicenseValue['carlicense_id'],
							'car_license'=>$carlicenseValue['car_license'],
						);
				}
			}
		}
		// dump($tmpData['contact']);
		
		return $tmpData['contact'];
	}

}
