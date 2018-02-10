<?php
namespace Home\Model;
use Nette\Security\User;
use function Qiniu\entry;
use Think\Model;
use Think\Verify;

define(UC_AUTH_KEY,C('UC_AUTH_KEY'));


/**
 * 会员Model
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class UserModel extends BaseadvModel
{
	/* 用户模型自动验证 */
	protected $_validate = array(
		/* 验证手机号码 */
		array('mobile', 'checkMobile', -8001, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH), //手机格式不正确
		array('mobile', '11,11', -8002, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //手机长度不合法
		array('admin_mobile', 'require', -8003, self::EXISTS_VALIDATE,'',self::MODEL_BOTH),//创建者手机号必须存在
		array('admin_mobile', '', -8004, self::EXISTS_VALIDATE, 'unique',self::MODEL_BOTH), //手机号已被注册
		array('admin_mobile', '1,34', -8005, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //手机号长度不合法
		array('shop_name', '', -8006, self::EXISTS_VALIDATE, 'unique',self::MODEL_BOTH), //公司名已被注册
		array('shop_name', '1,45', -8007, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //公司名长度不合法
		array('username', '1,15', -8008, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //用户名长度不合法
		array('username', 'checkDenyMember', -8009, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH), //在用户名禁止注册列表中
		array('password', '6,32', -8010, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //密码长度不合法
		array('email', 'email', -8011, self::EXISTS_VALIDATE,'',self::MODEL_BOTH), //邮箱格式不正确
		array('email', '1,200', -8012, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //邮箱长度不合法
		array('name', '1,100', -8013, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //真实姓名name不合法
		array('uid', 'isUnsignedInt', -8014, self::EXISTS_VALIDATE, 'function',self::MODEL_UPDATE),//uid不合法
		array('industry', '1,100', -8021, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//industry不合法
		array('province', '1,32', -8022, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//province不合法
		array('city', '1,32', -8023, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//city不合法
		array('qq', '0,32', -8024, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//qq不合法
        array('invitated_code', '6,6', -8028, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//邀请码不合法
        array('invitation_code', '6,6', -8028, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH),//邀请码不合法
		array('status', 'checkDeny_bool_status', -8025, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),//status不合法
		array('rpg', 'check_UserModel_rpg', -8026, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH),//rpg不合法
        array('depart_id','0,20',-24014,self::EXISTS_VALIDATE,'length',self::MODEL_BOTH),
        array('depart_id','0,20',-24014,self::EXISTS_VALIDATE,'length',self::MODEL_UPDATE),

		//MODEL_QUERY
		array('GTClientID', '32,32', -8020, self::MUST_VALIDATE, 'length',self::MODEL_QUERY), //GTClientID不合法

		// array('shop_name', 'require', -8005, self::EXISTS_VALIDATE),//公司用户名必须存在,长度验证1-x可以验证是否存在
		// array('email', 'checkDenyEmail', -8007, self::EXISTS_VALIDATE, 'callback'), //在邮箱禁止注册列表中
		// array('email', '', -8008, self::EXISTS_VALIDATE, 'unique'), //邮箱被占用

		//MODEL_admin_register
		array('mobile', 'checkMobile', -8001,
			self::MUST_VALIDATE, 'callback',self::MODEL_admin_register), //手机格式不正确
		array('mobile', '11,11', -8002,
			self::MUST_VALIDATE, 'length',self::MODEL_admin_register), //手机长度不合法
		array('username', '1,15', -8008,
			self::MUST_VALIDATE, 'length',self::MODEL_admin_register), //用户名长度不合法
		array('username', 'checkDenyMember', -8009,
			self::MUST_VALIDATE, 'callback',self::MODEL_admin_register), //在用户名禁止注册列表中
		array('admin_mobile', 'require', -8003,
			self::MUST_VALIDATE,'',self::MODEL_admin_register),//创建者手机号必须存在
		array('admin_mobile', '', -8004,
			self::MUST_VALIDATE, 'unique',self::MODEL_admin_register), //手机号已被注册
		array('admin_mobile', '1,34', -8005,
			self::MUST_VALIDATE, 'length',self::MODEL_admin_register), //手机号长度不合法
		array('shop_name', '', -8006,
			self::MUST_VALIDATE, 'unique',self::MODEL_admin_register), //公司名已被注册
		array('shop_name', '1,45', -8007,
			self::MUST_VALIDATE, 'length',self::MODEL_admin_register), //公司名长度不合法
		array('password', '6,32', -8010,
			self::MUST_VALIDATE, 'length',self::MODEL_admin_register), //密码长度不合法
		array('name', '1,100', -8013,
			self::MUST_VALIDATE, 'length',self::MODEL_admin_register), //真实姓名name不合法

		//UserModel_login
		array('username', '1,15', -8008,
			self::MUST_VALIDATE, 'length',self::UserModel_login), //用户名长度不合法
		array('username', 'checkDenyMember',-8009,
			self::MUST_VALIDATE, 'callback',self::UserModel_login), //在用户名禁止注册列表中
		array('password', '6,32', -8010,
			self::MUST_VALIDATE, 'length',self::UserModel_login), //密码长度不合法

		//UserModel_editShopInfo
		array('shop_name', '1,45', -8007,
			self::EXISTS_VALIDATE, 'length',self::UserModel_editShopInfo), //公司名长度不合法
		array('industry', '1,100', -8021,
			self::EXISTS_VALIDATE, 'length',self::UserModel_editShopInfo),//industry不合法
		array('province', '1,32', -8022,
			self::EXISTS_VALIDATE, 'length',self::UserModel_editShopInfo),//province不合法
		array('city', '1,32', -8023,
			self::EXISTS_VALIDATE, 'length',self::UserModel_editShopInfo),//city不合法

		//UserModel_editPassword
		array('admin_mobile', 'require', -8003,
			self::MUST_VALIDATE,'',self::UserModel_editPassword),//创建者手机号必须存在
		array('admin_mobile', '1,34', -8005,
			self::MUST_VALIDATE, 'length',self::UserModel_editPassword), //手机号长度不合法

        //UserModel_create_proxy
        array('mobile', '1,34', -8005,
            self::MUST_VALIDATE, 'length',self::UserModel_create_proxy), //手机号长度不合法
        array('mobile', 'checkMobile', -8001, self::MUST_VALIDATE, 'callback',self::UserModel_create_proxy), //手机格式不正确
        array('name', '1,15', -8008, self::MUST_VALIDATE, 'length',self::UserModel_create_proxy), //用户名长度不合法
        array('area', '1,60', -8031, self::MUST_VALIDATE,'length',self::UserModel_create_proxy), //用户名长度不合法
	);




	/* 用户模型自动完成 */
	protected $_auto = array(
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),

		//MODEL_admin_register
		array('reg_time', NOW_TIME, self::MODEL_admin_register),
		array('update_time', NOW_TIME, self::MODEL_admin_register),

		//UserModel_login
		array('update_time', NOW_TIME, self::UserModel_login),

		//UserModel_editShopInfo
		array('update_time', NOW_TIME, self::UserModel_editShopInfo),
	);




	/**
	 * 检测用户名是不是被禁止注册
	 * @param  string $username 用户名
	 * @return boolean          ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyMember($username)
	{
		switch ($username)
		{
			case "admin":
			case "administrator":
			case "杭州跃迁科技有限公司":
			case "跃迁科技":
			case "跃迁":
			case "":return false;
		}
		return true; //TODO: 暂不限制，下一个版本完善
	}





	/**
	 * 检测邮箱是不是被禁止注册
	 * @param  string $email 邮箱
	 * @return boolean       ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyEmail($email)
	{
		return true; //TODO: 暂不限制，下一个版本完善
	}



	/**
     * 检测注册时输入他人的邀请码是否存在数据库
     * @param string  $invitated_code 他人的邀请码
     * @return 1/false
     * @throws \XYException
     * @author DizzyLee
	*/
    protected function checkInvitatedCode($invitated_code)
    {
        $user = new UserModel();
        $invitation = $user->where(array('invitation_code' => $invitated_code))->find();
        if ($invitation === false)
            throw new \XYException(__METHOD__,-8000);
        if ($invitation === null)
            return false;
        return 1;
    }





    /**
     * 随机生成邀请码
     * @param $mobile 用户手机号
     * @return string $invitation_code 自身邀请码
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
    protected function createInvitationCode()
    {
        $invitation_code = '';
        while(ture)
        {
            $char = "1,2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z";
            $list = explode(",",$char);
            for($i = 0; $i <6; $i++)
            {
                $rand = rand(0, 32); //33个字母;
                $invitation_code = $list[$rand].$invitation_code;
            }
            if (!$this->checkInvitatedCode($invitation_code))
                return $invitation_code;
        }

    }


	/**
	 * 根据配置指定用户状态
	 * @return integer 用户状态
	 */
	protected function getStatus()
	{
		return true; //TODO: 暂不限制，下一个版本完善
	}



	/**
	 * 退出登录
	 * @api
	 * @return 1 退出成功
	 * @throws \XYException
	 */
	public function logout()
	{
		if(is_login())
		{
			session('user_auth', null);
			session('user_auth_sign', null);
			session(null);

			//再次判断session是否存在
	        if ( is_login() )
	            throw new \XYException(__METHOD__,-8050);
	        else
	        	return 1;
		}
		else
		{
			throw new \XYException(__METHOD__,-8889);
		}
	}



	/**
	 * 注册管理员后自动添加内容（数据库数据）
	 * @internal server
	 * @param array $data 额外数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * @throws \XYException
	 */
	protected function AdminRegisterAfterAutoAdd($data,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//Config
			$tmp = null;
			$tmp = D("Config")->add(array(
					'admin_uid'          => session('user_auth.admin_uid'),
					'MAX_LIMIT_EMPLOYEE' => C('DEFAULT_INIT_MAX_LIMIT_EMPLOYEE')
				));
			if ( !($tmp > 0) )
				throw new \XYException(__METHOD__,-8019);
			controller('Home');//Model调用的登录，登陆完后需要加载C()的config配置.这里不是controller('Home')->_initialize();是因为这里是Model，而_initialize()是protected的。


			//Company
			D('Company')->create_(array(
					'name'         => '零售客户',
					'qcode'        => 'lskh',
					'address'      => '当场',
					'init_payable' => 0,
					'status'       => 1,
					'contact'      => array(
							array(
									'contact_name' =>'零售',
									'phonenum'     =>array(),
									'car_license'  =>array(),
								),
						),
					// '[{"contact_name":"零售"}]',
				),true);
			$cat_id = D('Cat')->createCat(array(
			    'cat_class' => 1,
               'status' => 1,
               'cat_name' => '财务项目',
               'cat_index' => 1,
           ),true);
			$spu_id = D('Spu')->createSPU(array(
               'spu_class' => 1,
               'spu_name' => '财务二级分类',
               'spu_index' => 1,
               'qcode' => 'cwejfl',
               'cat_id' => $cat_id,
               'status' => 1,
           ),true);
           D('Sku')->createFinanceCart(array(
               'sku_class' => 1,
               'spec_name' => '交通费',
               'sku_index' => 1,
               'status'	   => 1
           ),true);
           D('Sku')->createFinanceCart(array(
               'sku_class' => 1,
               'spec_name' => '办公用品',
               'sku_index' => 1,
               'status'	   => 1
           ),true);

           D('Sku')->createFinanceCart(array(
               'sku_class' => 1,
               'spec_name' => '团队建设',
               'sku_index' => 1,
               'status'	   => 1
           ),true);
           //创建默认仓库
           D('Storage')->updateSto(array(
               'sto_name' => '默认仓库',
               'sto_index' => 1,
               'status' => 1,
           ),true,true);
           //创建默认现金账户
            D('Account')->createAccount(array(
                'account_creator' => '默认',
                'account_number' =>'10001',
                'account_name' => '现金',
                'account_source_type' => 3,
                'account_source_name' => '现金账户',
                'account_balance' => 0,
            ));
            /**
             *  * @param $account_creator 开户名
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
             */
			//会员相关
			D('UserAccount')->create_();
			if ($data['register_type'] == 1001)
			{
				D('UserAccount')->renewForMember(array(
						'admin_uid' => getUid(),
						'bill_class' => 1,
						'member_count' => 4,
					),true,true);//默认是4个月的金卡测试账户
			}
			else//默认续费时间
			{
				D('UserAccount')->renewForMember(array(
						'admin_uid' => getUid(),
						'bill_class' => 1,
						'member_count' => 4,
					),true,true);//默认是4个月的金卡测试账户
			}

			//User SN
			$tmpReturn = M('User')->where(array('uid'=>getUid()))->setField('sn',$this->getNextSn('USN'));
			if (empty($tmpReturn))
				throw new \XYException(__METHOD__,-8014);

			//打印机模板
			$tmp_printTemplateOptionArray      = array(1,1,1,1,1);
			$tmp_printTemplateOptionArray[201] = 0;
			D('PrintTemplate')->create_(array(
					'class' => 1,
					'font_size' => 14,
					'optionArray' => $tmp_printTemplateOptionArray,
					'content' => '
						LODOP.PRINT_INITA(0,3,911,351,"星云进销存线下打印_标准");LODOP.SET_PRINT_PAGESIZE(1,2410,930,"");LODOP.SET_PRINT_MODE("PROGRAM_CONTENT_BYVAR",true);LODOP.ADD_PRINT_TEXT(9,224,320,40,lodop_mShopName);LODOP.SET_PRINT_STYLEA(0,"FontName","微软雅黑");LODOP.SET_PRINT_STYLEA(0,"FontSize",14);LODOP.SET_PRINT_STYLEA(0,"Alignment",2);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.SET_PRINT_STYLEA(0,"Vorient",1);LODOP.ADD_PRINT_TEXTA("DistributorMobile",42,369,232,30,lodop_contact_name);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TABLE(114,55,826,187,lodop_data_table);LODOP.SET_PRINT_STYLEA(0,"ItemName","productinfo");LODOP.ADD_PRINT_TEXTA("PAmount",92,130,105,30,lodop_value);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXTA("PrintTime",25,543,143,20,lodop_reg_date);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(42,53,79,30,"客户：");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXTA("Remark",66,127,268,30,lodop_park_address);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(92,52,90,30,"货物价值：");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXTA("DistributorName",42,99,214,30,lodop_cid_name);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(66,52,97,30,"送货地址：");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(42,313,65,30,"联系人:");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(41,643,172,30,lodop_car_license);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(41,574,107,30,"车牌号：");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(92,235,79,30,"优惠：");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(92,286,101,30,lodop_off);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(92,387,147,30,lodop_yingshou);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(25,687,47,20,"第");LODOP.SET_PRINT_STYLEA(0,"ItemType",2);LODOP.ADD_PRINT_TEXT(25,727,65,20,"页，共");LODOP.SET_PRINT_STYLEA(0,"ItemType",3);LODOP.ADD_PRINT_TEXT(25,792,27,20,"页");LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(25,54,49,20,"单号：");LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(25,95,139,20,lodop_sn);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(92,533,166,30,lodop_income);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(92,691,97,30,"本次结余：");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(92,766,110,30,lodop_remain);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(311,52,76,28,"经办人：");LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);LODOP.ADD_PRINT_TEXT(311,109,179,28,lodop_operator_name);LODOP.SET_PRINT_STYLEA(0,"FontSize",12);LODOP.SET_PRINT_STYLEA(0,"ItemType",1);
						'
				));



			//注册成功发送邮件
			D('Util')->sendMailNoThrowException(
					C('ADMIN_REGISTER_MAIL_LIST'),
					'【注册成功】有用户完成注册',
					session('request_sms_send_verify_code_email_body')
				);

			if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
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
     * [Login类]注册一个新创建者
     * @note 注册后直接为登录状态，即已设置session
     *
     * @api
     * @param mixed|null $data POST的数据
     * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
     * @param bool $isSysCall 是否是系统调用。这里是generator的时候调用，以避开发送验证码才能注册以及设置权限为“自己人”。@internal
     * @param $isyiqiji  如果为ture，则为易企记注册
     * @param  string $username 用户名
     * @param $string $reg_what 1.星云进销存注册 2.易企记注册
     * @param  string $password 用户密码,md5("sjerp".$password)之后32bit给服务器。服务器参数：C('passwordMD5Prefix')
     * @param  string $verify_code 手机验证码
     * @param  string $where_know 从哪里得知我们【可选】
     * @param enum $type 要发送的验证码的类别。参考文档的Util的短信type
     *
     * @return unsigned_int 成功返回uid > 0
     * @throws \XYException
     */
    public function admin_register(array $data = null,$isAlreadyStartTrans = false,$isSysCall = false)
    {
        if (is_login())
            throw new \XYException(__METHOD__,-8400);
        try
        {
            if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
            $data['admin_mobile']    = $data['username'];
            $data['shop_name']       = $data['username'];
            $data['mobile']          = $data['username'];
            $data['name']            = '未填用户名';
            $data['invitation_code'] = $this->createInvitationCode();
            $tmp_type = intval(I('param.type'));
            if ($tmp_type != 1001)
                $tmp_type = 1;
            if (!$isSysCall)
                D('Util')->checkVerifyCode(array('type'=>$tmp_type,'verify_code'=>$data['verify_code'],'mobile'=>$data['username']));//检测手机验证码

            if (!$this->field('username,password,admin_mobile,shop_name,mobile,name,invitation_code,invitated_code')->create($data,self::MODEL_admin_register))//注：创建者手机号并没有单独检测，只检测了用户手机号
                throw new \XYException(__METHOD__,$this->getError());

            if ( isset($this->invitated_code) )
                if ($this->checkInvitatedCode($this->invitated_code) === false)
                    throw new \XYException(__METHOD__,-8028);
            $backup                 = null;
            $backup['username']     = $this->username;
            $backup['admin_mobile'] = $this->admin_mobile;
            if (empty($this->password) || ($this->password == '0cee1330dd286f8e60ae1504d234edf0') )
                throw new \XYException(__METHOD__,-8010);
            if ($isSysCall)
                $this->rpg = 1;
            else
                $this->rpg = 12;
            $this->status   = 1;
            $this->password = think_ucenter_md5($this->password,UC_AUTH_KEY);
            $this->reg_ip   = get_client_ip(0,true);
            $this->option_array = serialize(array(0));
            $uid = $this->add();
            if ( !($uid > 0) )//user表中插入成功
                throw new \XYException(__METHOD__,-8000);
            else
                $uid = intval($uid);

            //分配用户组
            if ( $isSysCall && ($uid === 1) )
            {
                D("AuthGroupAccess")->setUserGroup(array(
                    'uid'      => $uid,
                    'group_id' => 5,
                ));
            }

            elseif($data['admin_mobile'] == C('VISIT_USER_MOBILE'))
                D("AuthGroupAccess")->setUserGroup(array(
                    'uid'      => $uid,
                    'group_id' => 6,
                ));
            else
            {
                D("AuthGroupAccess")->setUserGroup(array(
                    'uid'      => $uid,
                    'group_id' => 12,
                ));
            }

            //服务器统计
            $where_know = I('data.where_know/s','','htmlspecialchars',$data);
            M('ServerStatistics')->add(array(
                'uid'             => $uid,
                'where_know'      => $where_know,
                'reg_time_format' => date("Y-m-d H:i:s",NOW_TIME),
                'reg_time'        => NOW_TIME,
                'reg_ip'          => get_client_ip(0,true),
            ));

            $tmpAdminUidReturn = $this->where(array('uid'=>$uid))->setField('admin_uid',$uid);
            if (empty($tmpAdminUidReturn))
                throw new \XYException(__METHOD__,-8000);

            //登录
            $loginReturn = $this->login(array(
                'admin'    => $data['admin_mobile'],
                'username' => $data['username'],
                'password' => $data['password'],
                'mode'     => 2,
                'type'     => 1,
            ),true);

            //创建成功后自动添加一些数据
            $AdminRegisterAfterAutoAddData = null;

            //发送注册成功短信
            if ($tmp_type == 1001)
            {
                import('Vendor.Sms.sms');
                $Sms = new \Sms();
                $sendReturn = $Sms->sendSuccessAdminRegister($backup['admin_mobile'],$backup['username'],'728034');
                $AdminRegisterAfterAutoAddData['register_type'] = 1001;
            }

            $this->AdminRegisterAfterAutoAdd($AdminRegisterAfterAutoAddData,true);//自动添加一些数据

            //一切都ok
            if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
            return $loginReturn;
        }catch(\XYException $e)
        {
            if (is_login() > 0)
                $this->logout();//退出登录
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);
            $e->rethrows();
        }
        catch(\Think\Exception $e)//打开这个会使得不好调试，但是不会造成因为thinkphp抛出异常而影响程序业务逻辑
        {
            if (is_login() > 0)
                $this->logout();//退出登录
            if (!$isAlreadyStartTrans) $this->rollback(__METHOD__);

            throw new \XYException(__METHOD__.'===ThinkPHP的坑===>'.$e->__toString(),-403);
        }
    }




	/**
	 * 注册一个新普通用户.
	 * note:   注册后没有设为登录状态
	 *
	 * - 必填：
	 * - @param string $username 用户名
	 * - @param string $password 用户密码,md5("sjerp".$password)之后32bit给服务器。服务器参数：C('passwordMD5Prefix')
	 * - @param enum $status 0|1
	 * - @param string $name 姓名
	 * - @param enum rpg 2|3|4|7|9|10:角色编号 9.易企记老板 10.易企记财务 11.易企记员工 12.管理员
	 * - 可选：
	 * - @param string $mobile
	 * - @param string $email
	 * - @param string $qq
	 * - @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * @param mixed|null $data POST的数据
	 *
	 * @return 1
	 * @throws \XYException
	 * @api
	 */
	public function register(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
            $rpg = getRpg();
			$tmpNowUidInfo = M('User')->where(array("uid"=>getUid(),'status'=>1))->getField('rpg');
			$tmpNowUidInfo = intval($tmpNowUidInfo);
			if ( (intval($tmpNowUidInfo) !== 1) && (intval($tmpNowUidInfo) !== 5) && (intval($tmpNowUidInfo) !== 7) && (intval($tmpNowUidInfo) !== 8))
				throw new \XYException(__METHOD__,-8508);

			$adminUid = getAdminUid();
			$adminInfo = M('User')->where(array("uid"=>$adminUid,'status'=>1))->find();
			if (empty($adminInfo["shop_name"]))
				throw new \XYException(__METHOD__,-8007);
			$tmp = M('User')->where(array("admin_uid"=>$adminUid))->count();
            if ($rpg != 8)
            {
                if ($tmp >= C('MAX_LIMIT_EMPLOYEE'))
                    throw new \XYException(__METHOD__,-8016);
            }

			$data['admin_uid']    = $adminUid;
			$data['shop_name']    = $adminInfo["shop_name"].C('USER_SEPARATE').$data['username'];
			$data['admin_mobile'] = $adminInfo["admin_mobile"].C('USER_SEPARATE').$data['username'];

			if (empty($data['mobile'])) unset($data['mobile']);
			if (empty($data['email'])) unset($data['email']);
			if (empty($data['qq'])) unset($data['qq']);
			$data['status'] = intval($data['status']);

			if(!$this->field('admin_uid,shop_name,admin_mobile,username,mobile,password,email,status,name,rpg,qq,depart_id')->create($data,self::MODEL_INSERT))//注：创建者手机号并没有单独检测，只检测了用户手机号
				throw new \XYException(__METHOD__,$this->getError());

			if (empty($this->password) || ($this->password == '0cee1330dd286f8e60ae1504d234edf0') )
				throw new \XYException(__METHOD__,-8010);

			if (!isset($data['mobile'])) 	unset($this->mobile);
			if (!isset($data['email'])) 	unset($this->email);
			if (!isset($data['qq'])) 		unset($this->qq);

			$this->password = think_ucenter_md5($this->password,UC_AUTH_KEY);
			$this->reg_ip   = get_client_ip(0,true);

			//backup
			$rpg = $this->rpg;
			$this->sn = $this->getNextSn('USN');
			$this->option_array = serialize(array(0));
			$uid = $this->add();

			if ($uid > 0)
			{
				//分配用户组
				D("AuthGroupAccess")->setUserGroup(array(
						'uid'      => $uid,
						'group_id' => $rpg,
					));

				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return $uid;
			}
			else
				throw new \XYException(__METHOD__,-8000);
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

    /**易企记员工注册
     * @param null $data
     * @param bool $isAlreadyStartTrans
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */

	public function staffRegister($data = null,$isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans) $this->startTrans();
            if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
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




	/**
	 * [Login类]用户登录认证
     ** - @param String $shop_name 店铺名称
     * - @param string $industry 行业
     * - @param string $province 省
     * - @param string $city 市
	 *
	 * - @param mixed|null $data POST的数据
	 * - @param bool $isInAdminRegister 是否是在User::admin_register()里调用的登录。如果是，则不返回一些在调用User::AdminRegisterAfterAutoAdd()之后才会有的内容，比如print_template。 @internal
	 * -
	 * - @param  string  $admin    创建者手机号/用户名，当为创建者登录时不需要传
	 * - @param  string  $username 用户名
	 * - @param  string  $password 用户密码,md5("sjerp".$password)之后32bit给服务器。服务器参数：C('passwordMD5Prefix')
	 * - @param  unsigned_int  $mode 登录模式，1-普通用户登录，2-创建者用户登录
	 * - @param  unsigned_int  $type 普通员工登录类型 （1-创建者手机，2-公司用户名）
	 * -
	 * - 如果输入密码错误超过3次，需要传入极验验证码相关参数：
	 * - @param string $geetest_challenge 验证事件流水号
     * - @param string $geetest_validate
     * - @param string $geetest_seccode
	 * -
	 * - @return unsigned_int 成功返回uid > 0
	 * - @throws \XYException
	 *
	 * @api
	 */
	public function login(array $data = null,$isInAdminRegister = false)
	{
		//统计尝试登陆的次数
		$loginCountKey   = D('Util')->encryption('logincount');
		$loginCountValue = session($loginCountKey);
		if (empty($loginCountValue))
			$loginCountValue = 0;
		$loginCountValue++;
		session($loginCountKey,$loginCountValue);

		//如果输入密码错误超过3次，需要检查极验验证码
//		if ($isInAdminRegister)
//			;
//		elseif ($loginCountValue >= 4)
//			D('Util')->checkJiyanVerifyCode($data);

		if (!$this->field('username,password')->create($data,self::UserModel_login))
			throw new \XYException(__METHOD__,$this->getError());

		$admin = I('data.admin/s','','htmlspecialchars',$data);
		if ($data['mode'] == 1)
		{
			$map = array('username'=>$this->username);
			$admin = $admin.C('USER_SEPARATE').$this->username;
			switch ($data['type'])
			{
			case 1:
				$map['admin_mobile'] = $admin;
				break;
			case 2:
				$map['shop_name'] = $admin;
				break;
			default:
				throw new \XYException(__METHOD__,-8401); //参数错误
			}
		}
		else
		{
			$map = array();
			switch ($data['type'])
			{
			case 1:
				$map['admin_mobile'] = $this->username;
				break;
			default:
				throw new \XYException(__METHOD__,-8401); //参数错误
			}
		}
		$map['status'] = 1;


		/* 获取用户数据 */
		$user = M('User')->where($map)->find();
		if(is_array($user) && $user['status'])
		{
			/* 验证用户密码 */
			if (think_ucenter_md5($this->password,UC_AUTH_KEY) === $user['password'])
			{
				$this->updateLogin($user['uid']);//更新用户登录信息

				$auth = array(
					'uid'       => $user['uid'],
					'username'  => $user['username'],
					'name'      => $user['name'],
					'admin_uid' => $user['admin_uid'],
                    'rpg'       => $user['rpg']
		        );
		        if ( ($data['mode'] == 2) && ($user['admin_uid'] == 0) )//这句话可以不要了??
		        	$auth['admin_uid'] = $user['uid'];
		        // log_("auth",$auth,$this);
        		session('user_auth', $auth);
        		session('user_auth_sign', data_auth_sign($auth));
        		session($loginCountKey,null);
        		// log_("session user_auth",session('user_auth'),$this);
        		// log_("session user_auth_sign",data_auth_sign($auth),$this);

        		if ($isInAdminRegister)
					return array(
							'uid'       => $user['uid'],
							'shop_name' => $this->getUserInfo_shopName(),
							'rpg'       => $user['rpg'],
							'name'      => $user['name'],
							'saas_uid'  => $user['admin_uid'],
							'industry'  => $user['industry'],
						);
				else
					return array(
							'uid'       => $user['uid'],
							'shop_name' => $this->getUserInfo_shopName(),
							'rpg'       => $user['rpg'],
							'name'      => $user['name'],
							'saas_uid'  => $user['admin_uid'],
							'industry'  => $user['industry'],
							// 'print_template' => D('PrintTemplate')->get_(array('class'=>1)),
						);
			}
			else
			{
				throw new \XYException(__METHOD__,-8402); //密码错误
			}
            $shop_name = I('data.shop_name/s','默认','htmlspecialchars',$data);
            $industry = I('data.industry/s','默认','htmlspecialchars',$data);
            $province = I('data.province/s','默认','htmlspecialchars',$data);
            $city = I('data.city/s','默认','htmlspecialchars',$data);
			if (!empty($shop_name) || !empty($industry) || !empty($province) || !empty($city))
            {
                $this->editShopInfo(array(
                    'shop_name' => $shop_name,
                    'industry' => $industry,
                    'province' => $province,
                    'city' => $city,
                ));
            }
		}
		else
		{
			throw new \XYException(__METHOD__,-8403); //创建者不存在或用户不存在或被禁用
		}
	}



	/**
	 * 更新用户登录信息
	 * @param  integer $uid 用户ID
	 */
	protected function updateLogin($uid)
	{
		// log_("session_id",session_id(),$this);
		// log_("XingYunBooks_User_SECRET",C('XingYunBooks_User_SECRET'),$this);
		// log_("md5",md5(session_id().C('XingYunBooks_User_SECRET')),$this);
		$data = array(
			'uid'             => $uid,
			'last_login_time' => NOW_TIME,
			'last_login_ip'   => get_client_ip(0,true),
			'session_id'      => md5(session_id().C('XingYunBooks_User_SECRET')),
		);
		$this->where(array('uid'=>$uid))->setInc('login_count',1);//登录次数+1

		// log_("data",$data,$this);
		$tmp = $this->save($data);
		// log_("tmp",$tmp,$this);
	}






	/**
	 * 获得uid为$uid的用户的信息
	 * @internal server
	 *
	 * @param mixed|null $data POST的数据
	 * @param bool $isIgnoreNull 当查出来数据为空的时候是否报异常。true-不报。这里用在知道uid查询用户名的情况。比如PaymentDetails::getPaybillAndSmsDetail()，知道uid，要查这个uid的用户名。之所以要忽略空，是因为用户有可能会删除用户
	 *
	 * @param  unsigned_int $uid 要查询的uid
	 *
	 * @return String 名称
	 * @throws \XYException
	 */
	public function getUserInfo(array $data = null,$isIgnoreNull = false)
	{
		if (!($this->field('uid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('uid'=>$this->uid,'admin_uid'=>getAdminUid()))->find();

		if ($tmp === null)
		{
			if ($isIgnoreNull)
				;
			else
				throw new \XYException(__METHOD__,-8512);
		}
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}




	/**
	 * 获得uid为$uid的用户的真实姓名
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isIgnoreNull 当查出来数据为空的时候是否报异常。true-不报。这里用在知道uid查询用户名的情况。比如PaymentDetails::getPaybillAndSmsDetail()，知道uid，要查这个uid的用户名。之所以要忽略空，是因为用户有可能会删除用户
	 *
	 * @param  unsigned_int $uid 要查询的uid
	 *
	 * @return String 名称
	 * @throws \XYException
	 */
	public function getUserInfo_name(array $data = null,$isIgnoreNull = false)
	{
		if (!($this->field('uid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('uid'=>$this->uid,'admin_uid'=>getAdminUid()))->getField('name');

		if ($tmp === null)
		{
			if ($isIgnoreNull)
				;
			else
				throw new \XYException(__METHOD__,-8501);
		}
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}




	/**
	 * 获得uid为$uid的用户的真实姓名和sn
	 * @api
	 * @param mixed|null $data POST的数据
	 *
	 * @param  unsigned_int $uid 要查询的uid
	 *
	 * @return array array['name']-名称,array['sn']=sn
	 * @throws \XYException
	 */
	public function getUserInfo_name_sn(array $data = null)
	{
		if (!($this->field('uid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('uid'=>$this->uid,'admin_uid'=>getAdminUid()))->field('name,sn')->find();

		if ($tmp === null)
			throw new \XYException(__METHOD__,-8501);
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}



	/**
	 * 获得uid为$uid的用户的sn
	 * @api
	 * @param mixed|null $data POST的数据
	 *
	 * @param  unsigned_int $uid 要查询的uid
	 *
	 * @return String sn
	 * @throws \XYException
	 */
	public function getUserInfo_sn(array $data = null)
	{
		if (!($this->field('uid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('uid'=>$this->uid,'admin_uid'=>getAdminUid()))->getField('sn');

		if ($tmp === null)
			throw new \XYException(__METHOD__,-8511);
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}



	/**
	 * 得到企业名称
	 *
	 * @api
	 * @return string 企业名称
	 * @throws \XYException
	 */
	public function getUserInfo_shopName()
	{
		$tmp = $this->where(array('uid'=>session('user_auth.admin_uid'),'status'=>1))->getField('shop_name');

		if ($tmp === null)
			throw new \XYException(__METHOD__,-8502);
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}



	/**
	 * 得到企业信息
	 *
	 * @api
	 * @return array 企业信息
	 * @throws \XYException
	 */
	public function getShopInfo()
	{
		$tmp = $this->where(array('uid'=>session('user_auth.admin_uid'),'status'=>1))->field('admin_uid,shop_name,industry,province,city')->find();

		if ($tmp === null)
			throw new \XYException(__METHOD__,-8510);
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}



	/**
	 * 设置用户的个推GTClientID
	 * @api
	 *
	 * @param mixed|null $data POST的数据
	 *
	 * @param string $GTClientID 个推的ClientID
	 *
	 * @return 1 ok
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.4
	 * @date    2016-06-26
	 */
	public function setGTClientID(array $data = null)
	{
		if(!$this->field('GTClientID')->create($data,self::MODEL_QUERY))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('uid'=>session('user_auth.uid'),'status'=>1))->save();

		if ( ($tmp === null) || ($tmp === false) )
			throw new \XYException(__METHOD__,-8000);

		return 1;
	}



	/**
	 * 得到某个用户的GTClientID
	 * @internal
	 *
	 * @param mixed|null $data POST的数据
	 *
	 * @param unsigned_int $uid
	 *
	 * @return string GTClientID
	 * @throws \XYException
	 * @author co8bit <me@co8bit.com>
	 * @version 0.4
	 * @date    2016-06-27
	 */
	public function getUserInfo_GTClientID(array $data = null)
	{
		if (!($this->field('uid')->create($data,self::MODEL_UPDATE)))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('uid'=>$this->uid,'admin_uid'=>session('user_auth.admin_uid'),'status'=>1))->getField('GTClientID');

		if ($tmp === null)
			throw new \XYException(__METHOD__,-8503);
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}



	/**
	 * 得到库管用户的GTClientID
	 * @internal
	 *
	 * @return string GTClientID
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.4
	 * @date    2016-06-27
	 */
	public function getWarehouseUidGTClientID()
	{
		$tmp = $this->where(array('admin_uid'=>getAdminUid(),'status'=>1,'rpg'=>3))->getField('GTClientID',true);

		if ($tmp === null)
			;
		if ($tmp === false)
			throw new \XYException(__METHOD__,-8000);

		return $tmp;
	}



	/**
	 * 查询店铺的所有人员信息
	 * @api
	 *
	 * @param mixed|null $data POST的数据
	 *
	 * @param unsigned $type 模式 1-所有user 2-有效user，即status=1的user
	 *
	 * @return array 数据库中一行
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.4
	 * @date    2016-06-27
	 */
	public function getList(array $data = null)
	{
		switch ($data['type'])
		{
			case 1:{
				$tmp = $this->where(array('admin_uid'=>session('user_auth.admin_uid')))->select();
				break;
			}
			case 2:{
				$tmp = $this->where(array('admin_uid'=>session('user_auth.admin_uid'),'status'=>1))->select();
				break;
			}
			default:
				throw new \XYException(__METHOD__,-8506);
		}
		if (empty($tmp))
			throw new \XYException(__METHOD__,-8507);

		return $tmp;
	}



	/**
	 * 编辑店铺信息.
	 *
	 * note: 只能是创建者才能修改
	 *
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * @param String $shop_name 店铺名称
	 * @param string $industry 行业
	 * @param string $province 省
	 * @param string $city 市
	 *
	 * @return  1
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.4
	 * @date    2016-06-27
	 * @api
	 */
	public function editShopInfo(array $data = null,$isAlreadyStartTrans = false)
	{
		//检测是不是创建者在修改
		if (session('user_auth.uid') !== session('user_auth.admin_uid'))
			throw new \XYException(__METHOD__,-8017);

		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			$tmpInfo = M('User')->where(array('uid'=>getAdminUid(),'status'=>1))->find();
			if ($tmpInfo['shop_name'] == $data['shop_name']) unset($data['shop_name']);
			if ($tmpInfo['industry'] == $data['industry']) unset($data['industry']);
			if ($tmpInfo['province'] == $data['province']) unset($data['province']);
			if ($tmpInfo['city'] == $data['city']) unset($data['city']);

			if (empty($data)) return 1;

			if(!$this->field('shop_name,industry,province,city')->create($data,self::UserModel_editShopInfo))
				throw new \XYException(__METHOD__,$this->getError());

			if (isset($this->shop_name))
			{
				//公司名称需要全局唯一
				$tmpShopName = M('User')->where(array('shop_name'=>$this->shop_name))->find();
				if (!empty($tmpShopName))
					throw new \XYException(__METHOD__,-8006);


				//检查$this->shop_name是否合法
				//1.不是11位纯数字
				//2.范围在1-30位
				$i = 0;
				$tag = true;
				$len = strlen($this->shop_name);
				for ($j = 0; $j < $len; $j++)
				{
					if (!isNonnegativeInt($this->shop_name[$j]))
						$tag = false;
					$i++;
				}
				if ( ($tag) && ($i == 11) )
					throw new \XYException(__METHOD__,-8018);
				if ($i == 0)
					throw new \XYException(__METHOD__,-8007);

				//更新创建者的shop_name放到了最后的save里去了

				//更新员工的shop_name
				$map['uid']  = array('neq',session('user_auth.admin_uid'));
				$tmp2 = M('User')->where(array("admin_uid"=>session("user_auth.admin_uid")))->where($map)->select();
				if ($tmp2 === false)
					throw new \XYException(__METHOD__,-8000);
				elseif ($tmp2 === null)
					throw new \XYException(__METHOD__,-8014);

				foreach ($tmp2 as $key => $value)
				{
					$tmp = null;
					$tmp = $this->where(array('uid'=>$value['uid']))->setField('shop_name',$this->shop_name.C('USER_SEPARATE').$value["username"]);
					if ( empty($tmp) )
						throw new \XYException(__METHOD__,-8000);
				}
			}

			$tmp = $this->where(array('uid'=>session('user_auth.admin_uid'),'status'=>1))->save();

			if ( ($tmp === null) || ($tmp === false) )
				throw new \XYException(__METHOD__,-8000);

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
	 * 修改用户信息
	 *
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * @param unsigned_int $uid 要被修改的用户的uid
	 * @param string $username 要被修改的用户名
	 * @param string $password
	 * @param enum rpg 2|3|4|7|8|9|10|11:角色编号
	 * @param string $mobile
	 * @param string $email
	 * @param string $name
	 * @param string $qq
	 *
	 * @param mixed|null $data POST的数据
	 *
	 * @return  1
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 0.4
	 * @date    2016-06-27
	 * @api
	 */
	public function editUserInfo(array $data = null,$isAlreadyStartTrans = false)
	{
		try
		{
			if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

			//判断有没有商店锁
			$shopLockStatus = D('Config')->getLockShopStatus(true);
			if ($shopLockStatus['status'] === 1)
				throw new \XYException(__METHOD__,-10507);

			if ( isset($data['status']) ) $data['status'] = intval($data['status']);

			$tmpInfo = M('User')->where(array('uid'=>intval($data['uid'])))->find();
			if ($tmpInfo['rpg'] == $data['rpg']) unset($data['rpg']);
			if ($tmpInfo['username'] == $data['username']) unset($data['username']);
			if ($tmpInfo['password'] == $data['password']) unset($data['password']);
			if (empty($data['password']) || ($data['password'] == '0cee1330dd286f8e60ae1504d234edf0') ) unset($data['password']);//pasword为空
			if ($tmpInfo['mobile'] == $data['mobile']) unset($data['mobile']);
			if ($tmpInfo['email'] == $data['email']) unset($data['email']);
			if ($tmpInfo['name'] == $data['name']) unset($data['name']);
			if ($tmpInfo['qq'] == $data['qq']) unset($data['qq']);
			if ($tmpInfo['status'] == $data['status']) unset($data['status']);
			if ($tmpInfo['depart_id'] == $data['depart_id']) unset($data['depart_id']);

			if (empty($data))
			{
				if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
				return 1;
			}

			if(!$this->field('uid,username,rpg,password,mobile,email,name,qq,status,depart_id')->create($data,self::MODEL_UPDATE))
			{
                throw new \XYException(__METHOD__, $this->getError());
            }
			//是否有权修改该uid的信息
			if ( intval($this->uid) === getUid() )//修改自己-允许
			{
				$originData = $this->data;
				$queryData = $this->getUserInfo(array('uid'=>getUid()));
				$this->data = null;
				$this->data = $originData;
				if( $queryData['admin_mobile'] === C('VISIT_USER_MOBILE') && !empty($data['password']))
					throw new \XYException(__METHOD__,-8504);

			}
			elseif ( getAdminUid($this->uid) === getAdminUid(getUid()) )//被修改的人和修改别人的人是同一个店铺的
			{
				$originData = $this->data;
				$model = new UserModel();
				$queryData  = $model->getUserInfo(array('uid'=>getUid()));//正在修改自己或别人的人
                $model = null;
                $model = new UserModel();
				$queryData2 = $model->getUserInfo(array('uid'=>$this->uid));//被修改的人
                unset($model);
				$this->data = null;
				$this->data = $originData;
				//这种情况下必须是该商铺的管理员才可以
				if ( ($queryData['rpg'] == 1) || ($queryData['rpg'] == 7) || ($queryData['rpg'] == 8) )
					;
				else
					throw new \XYException(__METHOD__,-8504);

				if ($queryData['rpg'] == 1|| $queryData['rpg'] == 8 || $queryData2['rpg'] == 10)
					;
				elseif ( ($queryData2['rpg'] == 1) || ($queryData2['rpg'] == 7)|| $queryData2['rpg'] == 8)//普通管理员不能修改其他管理员的信息
                {
                    throw new \XYException(__METHOD__, -8504);
                }

			}
			else
				throw new \XYException(__METHOD__,-8504);

			if ( isset($this->mobile) && (intval($this->uid) === getAdminUid($this->uid)) )//暂不支持修改创建者的手机
				throw new \XYException(__METHOD__,-8800);

			if (isset($this->status))
			{
				//$this->uid：被修改的人
				//getUid()：正在修改自己或别人的人
				if ( (intval($this->uid) === getAdminUid($this->uid)) )//任何情况下，创建者用户不支持被修改其值
					throw new \XYException(__METHOD__,-8802);
				if ( getUid() === getAdminUid($this->uid) )//正在修改别人的人是创建者，且他修改的不是自己的值（因为上一条if）
					;
				elseif ( ($queryData['rpg'] == 1) || ($queryData['rpg'] == 7) || ($queryData['rpg'] == 8))//正在修改别人的人是管理员
				{
					//不能修改创建者的
					if ( (intval($this->uid) === getAdminUid($this->uid)) )//任何情况下，创建者用户不支持被修改
						throw new \XYException(__METHOD__,-8808);

					//管理员用户不支持修改自身。同时，因为第一条if，所以不会修改创建者的值
					if (intval($this->uid) === getUid())
						throw new \XYException(__METHOD__,-8808);

					//管理员不支持修改其他管理员的角色
					if ( ($queryData2['rpg'] == 1) || ($queryData2['rpg'] == 7) || ($queryData2['rpg'] == 8) || ($queryData2['rpg'] == 10))
						throw new \XYException(__METHOD__,-8808);
				}
				else//正在修改别人的人是普通人
					throw new \XYException(__METHOD__,-8808);//非管理员用户不支持修改角色
			}

			if (isset($this->rpg))
			{
				//$this->uid：被修改的人
				//getUid()：正在修改自己或别人的人
				if ( (intval($this->uid) === getAdminUid($this->uid)) )//任何情况下，创建者用户不支持被修改其角色值
					throw new \XYException(__METHOD__,-8804);
				if ( getUid() === getAdminUid($this->uid) )//正在修改别人的人是创建者，且他修改的不是自己的rpg（因为上一条if）
					;
				elseif ( ($queryData['rpg'] == 1) || ($queryData['rpg'] == 7) || ($queryData['rpg'] == 8))//正在修改别人的人是管理员
				{
					//不能修改创建者的角色
					if ( (intval($this->uid) === getAdminUid($this->uid)) )//任何情况下，创建者用户不支持被修改其角色值
						throw new \XYException(__METHOD__,-8804);

					//管理员用户不支持修改自身角色。同时，因为第一条if，所以不会修改创建者的角色值
					if (intval($this->uid) === getUid())
						throw new \XYException(__METHOD__,-8804);

					//管理员不支持修改其他管理员的角色
					if ( ($queryData2['rpg'] == 1) || ($queryData2['rpg'] == 7) )
						throw new \XYException(__METHOD__,-8807);
				}
				else//正在修改别人的人是普通人
					throw new \XYException(__METHOD__,-8803);//非管理员用户不支持修改角色
			}


			if (isset($this->username))//检查是否有权限
			{
				//$this->uid：被修改的人
				//getUid()：正在修改自己或别人的人
				if ( (intval($this->uid) === getAdminUid($this->uid)) )//任何情况下，创建者用户不支持被修改其值
					throw new \XYException(__METHOD__,-8808);
				if ( getUid() === getAdminUid($this->uid) )//正在修改别人的人是创建者，且他修改的不是自己的值（因为上一条if）
					;
				elseif ( ($queryData['rpg'] == 1) || ($queryData['rpg'] == 7) ||($queryData['rpg'] == 8))//正在修改别人的人是管理员
				{
					//不能修改创建者的
					if ( (intval($this->uid) === getAdminUid($this->uid)) )//任何情况下，创建者用户不支持被修改
						throw new \XYException(__METHOD__,-8808);

					//管理员用户不支持修改自身。同时，因为第一条if，所以不会修改创建者的值
					if (intval($this->uid) === getUid())
						throw new \XYException(__METHOD__,-8808);

					//管理员不支持修改其他管理员的角色
					if ( ($queryData2['rpg'] == 1) || ($queryData2['rpg'] == 7) || ($queryData2['rpg'] == 8))
						throw new \XYException(__METHOD__,-8808);
				}
				else//正在修改别人的人是普通人
					throw new \XYException(__METHOD__,-8808);//非管理员用户不支持修改角色
			}

			if ( isset($this->username))
			{
				//检查是否有重复
				$queryData = M('User')->where(array('admin_uid'=>getAdminUid()))->getField('username',true);//todo: 乐观锁
				foreach ($queryData as $value)
				{
					if ($value == $this->username)
						throw new \XYException(__METHOD__,-8806);
				}


				//更改admin_mobile和shop_name
				$admin_mobile = substr($tmpInfo['admin_mobile'],0,strpos($tmpInfo['admin_mobile'],C('USER_SEPARATE')));
				// log_("admin_mobile",$admin_mobile,$this);
				$this->admin_mobile = $admin_mobile.C('USER_SEPARATE').$this->username;

				$shop_name = substr($tmpInfo['shop_name'],0,strpos($tmpInfo['shop_name'],C('USER_SEPARATE')));
				$this->shop_name = $shop_name.C('USER_SEPARATE').$this->username;
			}

			if (isset($this->password))
				$this->password = think_ucenter_md5($this->password,UC_AUTH_KEY);

			$backData = $this->data;

			$tmp = $this->where(array('uid'=>$this->uid))->save();

			if ( ($tmp === null) || ($tmp === false) )
				throw new \XYException(__METHOD__,-8000);

			if (isset($data['rpg']))
			{
				D("AuthGroupAccess")->setUserGroup(array(
						'uid'      => intval($backData['uid']),
						'group_id' => intval($backData['rpg']),
					));
			}


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
     * 设置用户选项
     *
     * @param mixed|null $data POST的数据
     *
     * @return  1
     * @throws \XYException
     *
     * @author xiao <admin@end.wiki>
     * @version 1.7
     * @date    2017-04-28
     * @api
     */
    public function setOptionArray(array $data = null){

        $tmpOptionArray = json_decode(I('param.option_array','',false),true);
        if(empty($tmpOptionArray)){
            $tmpOptionArray = $data['option_array'];
        }
        if(empty($tmpOptionArray) || !is_array($tmpOptionArray))
            throw new \XYException(__METHOD__,-8051);

        foreach($tmpOptionArray as $key => $value){
            if ( isNonnegativeInt($key) && isNonnegativeInt($value) )
                ;
            else
            {
                throw new \XYException(__METHOD__,-8051);
            }
        }


        $this->option_array = serialize($tmpOptionArray);

        if(empty($this->option_array))
            throw new \XYException(__METHOD__,-8051);

        $tmpReturn = $this->where(array('admin_uid'=>getAdminUid(),'uid'=>getUid()))->save();

        if ($tmpReturn >= 0)
            return 1;
        else
            throw new \XYException(__METHOD__,-8000);
    }
    /**
     * 获取用户选项
     * @api
     * @return array
     * @throws \XYException
     *
     * @author jinzhi <admin@end.wiki>
     * @version 1.7
     */
    public function getOptionArray(){

        $tmp = $this->field('option_array')->where(array('admin_uid' => getAdminUid(),'uid'=>getUid()))->find();

        if($tmp === null)
            throw new \XYException(__METHOD__,-8513);
        elseif($tmp === false)
            throw new \XYException(__METHOD__,-8000);

        if($tmp['option_array']){
            $tmpSerialize = unserialize($tmp['option_array']);
            if($tmpSerialize === false)
                throw new \XYException(__METHOD__,-8051);
            $tmp['option_array'] = $tmpSerialize;
        }else{
            $tmp['option_array'] = null;
        }
        return $tmp;
    }
    /**
     * 通过手机号获得用户信息
     * @param string $mobile 手机号
     *
     * @throws \XYException
     * @return array
     *
     * @author DizzyLee
     * @version 1.8
     */
    public function getUserInfoByMobile($mobile = null)
    {
        $user = new UserModel();
        $queryData = $user->where(array('admin_mobile'=>$mobile))->find();
        if ($queryData === false)
            throw new \XYException(__METHOD__,-8000);
        elseif ($queryData === null)
            throw new \XYException(__METHOD__,-8514);
        return $queryData;
    }


	/**
	 * 找回密码.
	 *
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isAlreadyStartTrans 在调用本函数的时候外部是否已开启事务。@internal
	 *
	 * @param string $admin_mobile 根管理员用户名
	 * @param string $password 新密码
	 * @param string $verify_code 短信验证码
	 *
	 * @return 1
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.2
	 * @date    2016-11-21
    */
	public function editUserPasswd($data = null,$isAlreadyStartTrans = false)
    {

        $data['admin_mobile'] = D('Util')->getVerifyCodeAdminMobile(3);
//        log_($data);
        D('Util')->checkVerifyCode(array('type'=>3,'verify_code'=>$data['verify_code'],'mobile'=>$data['admin_mobile']));//检测手机验证码
        if(!$this->field('admin_mobile,password')->create($data,self::UserModel_editPassword))
            throw new \XYException(__METHOD__,$this->getError());
        $queryData = $this->getUserInfoByMobile($data['admin_mobile']);
        $this->uid = $queryData["uid"];
        if(!($this->uid > 0))
            throw new \XYException(__METHOD__,-8027);

        if (empty($this->password) || ($this->password == '0cee1330dd286f8e60ae1504d234edf0') )
            throw new \XYException(__METHOD__,-8010);
        $this->password = think_ucenter_md5($this->password,UC_AUTH_KEY);
        if ( !($this->save() >= 0) )
            throw new \XYException(__METHOD__,-8000);
        return 1;
	}

    /**
     *获取员工信息
     * @return $staffdata
     * @author DizzyLee<728394036@qq.com>
     */
	public function getStaffInfo()
    {
        $admin_uid = getAdminUid();
        $staffdata = D('User')->field('uid,name,sn')->where(array('admin_uid' => $admin_uid))->select();
        if ($staffdata === false || $staffdata === null)
            throw new \XYException(__METHOD__,-8000);
        return $staffdata;
    }

    /**
     * 增加代理商接口
     * @param $area 所在区域
     * @param $name 姓名
     * @param $mobile 手机
     * @return $add 返回自增主键
     * @throws \XYException
     * @author DizzyLee<728394036@qq.com>
     */
	public function create_proxy($data = null)
    {
        if (!$this->field('mobile,name,area')->create($data,Self::UserModel_create_proxy))
            throw new \XYException(__METHOD__,$this->getError());
        $add = M('proxy_company')->add($data);
        if ($add === false)
            throw new \XYException(__METHOD__,-8000);
        if ($add == 0||$add == '')
            throw new \XYException(__METHOD__,-8032);
        if ($add>0)
            return $add;
    }

    /**切换用户角色接口
     * @param $rpg_mode RPG角色模式 10001星云进销存 10002 易企记
     * @author DizzyLee<728394036@qq.com>
     */
    public function changeRpg($data = null,$isAlreadyStartTrans = false)
    {
        try{
            if (!$isAlreadyStartTrans) $this->startTrans();
            if (!$this->check_rpg_mode($data['rpg_mode']))
                throw new \XYException(__METHOD__,-40001);
            $rpg_mode = $data['rpg_mode'];
            $uid = getUid();
            $user = D('User')->where(array('uid'=>$uid))->find();
            if ($user ===false || $user === null)
                throw new \XYException(__METHOD__,-8000);
            if ($user == 0)
                throw new \XYException(__METHOD__,-8403);
            if ($rpg_mode == self::XY_ESS_MODE)
                $user['rpg'] = 1;
            elseif ($rpg_mode == self::BANK_MODE)
                $user['rpg'] = 8;
            $operate = D('User')->where(array('uid'=>$uid))->save($user);
            if ($operate ===false || $operate === null)
                throw new \XYException(__METHOD__,-8000);
        D("AuthGroupAccess")->setUserGroup(array(
            'uid'      => $uid,
            'group_id' => $user['rpg'],
        ));
            if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
            $now_session = session('user_auth');
            if (is_array($now_session)) {
            	if ($now_session['rpg'] != $user['rpg']) {
            	$now_session['rpg'] = $user['rpg'];
            	session('user_auth',$now_session);
            	session('user_auth_sign', data_auth_sign($now_session));
            }
            }
            
            return $user['rpg'];
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
