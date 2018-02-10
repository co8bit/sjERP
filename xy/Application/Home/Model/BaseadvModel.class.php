<?php
namespace Home\Model;
use Think\Model\AdvModel;

/**
 * 高级Model的通用基类
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class BaseadvModel extends AdvModel
{
	const MODEL_QUERY                         = 4;//当“验证时间”这个值大于3（MODEL_BOTH）后，在create时“验证条件”填写的值是大于3的值时，在_validate数组里“验证时间”是MODEL_BOTH的那些条件并不会生效，必须是_validate数组里的"验证条件"和create里的"验证条件"（如都是MODEL_QUER)一致时才验证。
	const MODEL_GET                           = 5;
	
	const MODEL_CREATE_DRAFT                  = 11;
	const MODEL_DELETE_DRAFT                  = 12;
	
	const MODEL_SEARCH                        = 20;
	const MODEL_REPORT                        = 21;
	
	const AuthGroupAccessModel_setUserGroup   = 100;
	const AuthGroupModel_create_              = 101;
	const AuthRuleModel_create_               = 102;
	
	const MODEL_SINGLE_SMS                    = 501;
	const MODEL_VERIFY_CODE_CHECK             = 502;
	const MODEL_admin_register                = 503;
	
	//new
	const UserModel_login                     = 504;
	const UserModel_editShopInfo              = 505;
	const CatModel_createCat                  = 506;
	const CatModel_editCat                    = 507;
	const SpuModel_createSPU                  = 508;
	const SpuModel_editSPU                    = 509;
	const OrderModel_createOrder              = 510;
	const ContactModel_deleteContact          = 511;
	const ConfigModel_setShopConfig           = 512;
	const SkuBillModel_create_                = 513;
	const PaybillModel_getPayBillParam        = 514;
	const UserAccountModel_renewForBalance    = 515;
	const UserAccountModel_renewForMember     = 516;
	const UserModel_editPassword              = 517;
	const PaymentDetailsModel_create_         = 518;
	const OrderModel_deleteCid                = 519;
	const PrintTemplateModel_create_          = 520;
	const PrintTemplateModel_get_             = 521;
	const OrderModel_createAdjustAROrAP       = 522;
	const WarehouseModel_deleteWarehouseOrder = 523;
	const QueryModel_saleSummary              = 524;
	const SkuCidPriceModel_updateLastPrice    = 525;
	const SkuCidPriceModel_get_               = 526;
	const QueryModel_skuChart 				  = 527;
	const PrintTemplateModel_WBXcreate_       = 528;
	const QueryModel_skuSummary 			  = 529;
	const SkuBillModel_queryOrderAndWarehouse = 530;
	const CompanyModel_requestStatementOfWechatAccount = 531;
	const StatementAccountModel_show_statement= 532;
	const StorageModel_updateSto 			  = 533;
	const AccountModel_createAccount          = 534;
	const AccountModel_set_New_Source         = 535;
    const AccountModel_cash_Proposal          = 536;
    const AccountModel_edit_Account           = 537;
    const SkuModel_createFinanceCart          = 538;
    const FinanceModel_create_FinanceOrder    = 539;
    const FinanceModel_query_FinanceOrder     = 540;
    const FinanceModel_financeOrderStatusChange = 541;
    const AccountModel_query_account          = 542;
    const AccountModel_delete_Account         = 543;
    const UserModel_create_proxy              = 544;
    const SkuModel_editFinanceCart            = 545;
	const SkuModel_querySKU					  = 546;
	const StorageModel_get_                   = 547;
	const StorageModel_deleteSto 			  = 548;
	const SkuStorageModel_updateSkuStorage    = 549;
	const SkuStorageModel_get_    			  = 550;
	const SkuStorageModel_deleteSkuSto        = 551;
	const FinanceModel_getExpenditureStatistics = 552;
	const QueryModel_purchaseSummary          = 553;
    const DepartmentModel_createDepartment = 554;
    const DepartmentModel_deleteDepartment = 555;
    const DepartmentModel_editDepartment   = 556;
    const FinanceModel_create_FinanceOrderDraft = 557;
    const SkuStorageModel_querySkuSto 		= 558;
    const FaceRecModel_upload_pic			= 559;
    const CusListModel_createRecord  		= 560;
    const CompanyModel_companyTransactionStatistics = 561;
    const QueryModel_operatorSummary          = 562;
    const QueryModel_operatorChart            = 563;
    const QueryModel_queryOrder               = 564;
    const QueryModel_getOperatorInfo          = 565;
    const FinanceModel_edit_FinanceOrder      = 566;
    const FinanceModel_delete_FinanceOrder    = 567;
	private $snIDArray = null;//getNextSn()时用到的sn id array






    //RPG_MODE
    const XY_ESS_MODE                         = 10001;
    const BANK_MODE                           = 10002;

	protected function _initialize()
	{
		\Think\Log::record('------------------------------<font style="color:#FFFFFF; font-weight:bold;background: #5cb85c;">'
			.get_class($this)
			.'</font> START------------------------------','INFO');
	}




	/**
	 * 得到下一个Sn编号，调用者需要在事务里。
	 *
	 * note:读取时会加锁，所以必须外部要打开事务才可以调用。
	 * 
	 * @param string $type sn类型，具体值参考{@see \Doc\doc_view()}
	 *
	 * @return string 具体的编号
	 * @author co8bit <me@co8bit.com>
	 */
	protected function getNextSn($type)
	{
		$dbConfig = D('Config');
		if ($this->snIDArray === null)
			$this->snIDArray = $dbConfig->getSnConfig();

		$sn = '';
		$id = ++$this->snIDArray[$type];

		// switch($type)
		// {
		// 	case 'SKU'://sku_sn
		// 		$sn = 'SKU'.$id;
		// 		$dbConfig->updateSnConfig('SKU',$id);
		// 		break;
		// 	case 'PAD'://停车位置
		// 		$sn = 'PAD'.$id;
		// 		$dbConfig->updateSnConfig('PAD',$id);
		// 		break;
		// 	case 'CSN'://往来单位
		// 		$sn = 'CSN'.$id;
		// 		$dbConfig->updateSnConfig('CSN',$id);
		// 		break;

		// 	case 'OXC'://销售单
		// 		// $sn = 'OXC'.$id;
		// 		// break;
		// 	case 'OXT'://销售退货单
		// 		// $sn = 'OXT'.$id;
		// 		// break;
		// 	case 'OCR'://采购单
		// 		// $sn = 'OCR'.$id;
		// 		// break;
		// 	case 'OCT'://采购退货单
		// 		// $sn = 'OCT'.$id;
		// 		// break;
		// 	case 'FSK'://收款单
		// 		// $sn = 'FSK'.$id;
		// 		// break;
		// 	case 'FFK'://付款单
		// 		// $sn = 'FFK'.$id;
		// 		// break;
		// 	case 'FQS'://其他收入
		// 		// $sn = 'FQS'.$id;
		// 		// break;
		// 	case 'FFY'://费用单
		// 		// $sn = 'FFY'.$id;
		// 		// break;
		// 	case 'WPD'://盘点单
		// 		// $sn = 'WPD'.$id;
		// 		$tmpNum = 10000000;
		// 		$prefix = $type . substr(($tmpNum+getAdminUid()),1,7);
		// 		$sn = uniqid($prefix,true).rand(10,99);
		// 		break;
		// 	case 'DRA'://草稿单
		// 		// $sn = 'DRA'.$id;
		// 		// break;
		// 	default:
		// 		throw new \XYException(__METHOD__,-10503);
		// }

		$dbConfig->updateSnConfig($type,$id);
		$tmpNum = 1000000;
		$sn = $type . substr($tmpNum+$id,1,6);//3+6位的连续编号

		return $sn;
	}




	/**
	 * 检查cid的合法性
	 * @param  unsigned_int $cid
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_cid($cid)
	{
		return isUnsignedInt($cid);
	}


	/**
	 * 检查contact_id的合法性
	 * @param  unsigned_int $contact_id
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_contact_id($contact_id)
	{
		return isUnsignedInt($contact_id);
	}


	/**
	 * 检查phonenum_id的合法性
	 * @param  unsigned_int $phonenum_id
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_phonenum_id($phonenum_id)
	{
		return isUnsignedInt($phonenum_id);
	}


	/**
	 * 检查carlicense_id的合法性
	 * @param  unsigned_int $carlicense_id
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_carlicense_id($carlicense_id)
	{
		return isUnsignedInt($carlicense_id);
	}



	/**
	 * 检查wid的合法性
	 * @param  unsigned_int $wid
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_wid($wid)
	{
		return isUnsignedInt($wid);
	}



	/**
	 * 检查fid的合法性
	 * @param  unsigned_int $fid
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_fid($fid)
	{
		return isUnsignedInt($fid);
	}



	/**
	 * 检查parkaddress_id的合法性
	 * @param  unsigned_int $parkaddress_id
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_parkaddress_id($parkaddress_id)
	{
		return isUnsignedInt($parkaddress_id);
	}



	/**
	 * 检查oid的合法性
	 * @param  unsigned_int $oid 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_oid($oid)
	{
		return isUnsignedInt($oid);
	}



	/**
	 * 在有数据库的其他字段对其有依赖性的时候检查cat_id的合法性
	 * 即必须判断这个值是否真的存在
	 * @param  unsigned_int $cat_id
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_depend_cat_id($cat_id)
	{
		$catInfo = M('Cat')->where(array('cat_id'=>$cat_id,'admin_uid'=>getAdminUid()))->getField('cat_id');
		if ($catInfo > 0)
			return true;
		else
			return false;
	}

	/**
	 * 判断该店铺仓库名称是否唯一
	 * @param  string $sto_name
	 * @return  boolean true-合法,false-不合法
	 */
	protected function checkDeny_stoName($sto_name)
	{
		$sto_name_list = D('Storage')->getStoName();
		if(in_array($sto_name,$sto_name_list) )
			return false;
		else
			return true;
	}

	/**
	 * 在有数据库的其他字段对其有依赖性的时候检查(companyName-admin_uid)这一组数据是否存在
	 * 即必须判断这个值是否真的存在
	 * @param  unsigned_int $name
	 * @return boolean true-存在,false-不存在
	 */
	protected function checkDeny_depend_companyName($name)
	{
		$count = M('Company')->where(array('name'=>$name,'admin_uid'=>getAdminUid()))->count();
		if ($count >= 1)
			return true;
		else
			return false;
	}	




	/**
	 * 在有数据库的其他字段对其有依赖性的时候检查(companyName-admin_uid)这一组数据是否存在
	 * 即必须判断这个值是否真的存在
	 * @param  unsigned_int $name
	 * @return boolean true-不存在,false-存在
	 */
	protected function not_checkDeny_depend_companyName($name)
	{
		return ( !$this->checkDeny_depend_companyName($name) );
	}



	/**
	 * 检查bool型status的合法性
	 * @param  unsigned_int $status
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_bool_status($status)
	{
	    log_('status==================',$status);
		if ( ($status === 1) || ($status === 0) )
			return true;
		else
			return false;
	}

	protected function checkDeny_depart_id($depart_id)
    {
        return isUnsignedInt($depart_id);
    }



	/**
	 * 检查query中单据的class的合法性（class!=2、4）
	 * @param  unsigned_int $class
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_query_class($class)
	{
		if ( 	   ($class === 1) 
				|| ($class === 2)
				|| ($class === 3)
				|| ($class === 4)
				|| ($class === 5)
				|| ($class === 6)
				|| ($class === 53)
				|| ($class === 54)
				|| ($class === 71)
				|| ($class === 72)
				|| ($class === 73)
				|| ($class === 74)
                || ($class === 81)
                || ($class === 82)
                || ($class === 83)
                || ($class === 84)
                || ($class === 85)

			)
			return true;
		else
			return false;
	}


	/**
	 * 检查query中单据的status的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_query_status($value)
	{
		if ( 	   ($value === 1)//已完成
				|| ($value === 2)//异常
				|| ($value === 5)//暂缓处理
				|| ($value === 90)//未完成
				|| ($value === 99)//系统自动创建的期初应收、期初应付
                || ($value === 81)
                || ($value === 82)
                || ($value === 83)
                || ($value === 84)
                || ($value === 85)
				|| ($value === 3)
				|| ($value === 91)
				|| ($value === 92)
				// || ($value === 100)//草稿单
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查QueryModel中单据的remainType的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_QueryModel_remainType($value)
	{
		if ( 	   ($value === 1)
				|| ($value === 2)
				|| ($value === 3)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查QueryModel中单据的financeType的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_QueryModel_financeType($value)
	{
		if ( 	   ($value === 101)
				|| ($value === 102)
				|| ($value === 103)
			)
			return true;
		else
			return false;
	}




	/**
	 * 检查QueryModel中单据的bigClass的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_QueryModel_bigClass($value)
	{
		if ( 	   ($value === 111)
				|| ($value === 112)
				|| ($value === 113)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查QueryModel中单据的type的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_QueryModel_type($value)
	{
		if ( 	   ($value === 1)
				|| ($value === 2)
				|| ($value === 3)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查checkDeny_everyday_summary_sheet_id的合法性
	 * @param  unsigned_int $checkDeny_everyday_summary_sheet_id
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_everyday_summary_sheet_id($checkDeny_everyday_summary_sheet_id)
	{
		return isUnsignedInt($checkDeny_everyday_summary_sheet_id);
	}


	/**
	 * 检查UtilModel中MODEL_SINGLE_SMS的type的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_UtilModel_MODEL_SINGLE_SMS_type($value)
	{
		if ( 	   ($value === 1)
				|| ($value === 2)
				|| ($value === 3)
                || ($value === 4)
				|| ($value === 1001)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检测手机号是否合法
	 * @param  string $mobile 用户名
	 * @return boolean          ture - 未禁用，false - 禁止注册
	 * @todo 手机号检测
	 */
	protected function checkMobile($mobile)
	{
		//TODO:手机号检测
		if ( isInt($mobile) && (strlen($mobile) == 11) && ($mobile[0] != 0) && ($mobile[0] != '+') )
			return true;
		else
			return false;
	}



	/**
	 * 检测用户组的规则字段是否合法
	 * AuthGroupModel::rules字段
	 * 
	 * @param  array $rules 规则的数组
	 * @return boolean          ture - 合格，false - 不合格
	 */
	protected function check_AuthGroupModel_rules($rules)
	{
		if (!is_array($rules))
			return false;

		if (empty($rules))
			return false;

		foreach ($rules as$v)
		{
			if (!isUnsignedInt($v))
				return false;
		}

		return true;
	}



	/**
	 * 检查数据库内该管理员名下是否cat_name重名
	 * @param  unsigned_int $cat_name
	 * @return boolean true-合法，不重名,false-不合法，重名
	 */
	protected function checkDeny_depend_catName($cat_name)
	{
		$catInfo = M('Cat')->where(array('cat_name'=>$cat_name,'admin_uid'=>getAdminUid()))->find();
		if ($catInfo === null)
			return true;
		else
			return false;
	}



	/**
	 * 检查数据库内该管理员名下是否spu_name重名
	 * @param  unsigned_int $spu_name
	 * @return boolean true-合法，不重名,false-不合法，重名
	 */
	protected function checkDeny_depend_spuName($spu_name)
	{
		$tmpInfo = M('Spu')->where(array('spu_name'=>$spu_name,'admin_uid'=>getAdminUid()))->find();
		if ($tmpInfo === null)
			return true;
		else
			return false;
	}
    protected function checkDeny_Depart_name($depart_name)
    {
        $tmpInfo = M('Department')->where(array('depart_name'=>$depart_name,'admin_uid'=> getAdminUid()))->find();
        if ($tmpInfo === null)
            return true;
        else
            return false;
    }



	/**
	 * 检查PaybillModel中bill_class的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_PaybillModel_billClass($value)
	{
		if ( 	   ($value == 1)
				|| ($value == 2)
				|| ($value == 3)
				|| ($value == 4)
                || ($value == 5)
                || ($value == 6)
                || ($value == 7)
                || ($value == 8)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查PaybillModel中member_count的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_PaybillModel_memberCount($value)
	{
		if ( 	   ($value == 1)
				|| ($value == 3)
				|| ($value == 4)
				|| ($value == 6)
				|| ($value == 12)
                || ($value == 24)
                || ($value == 36)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查UserAccountModel中member_class的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_UserAccountModel_memberClass($value)
	{
		if ( 	   ($value == 1)
				|| ($value == 2)
				|| ($value == 3)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查unix的日期格式合不合法
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_unix_date($value)
	{
		if ( isNonnegativeInt($value) && ( strlen($value) == 10 ) )
			return true;
		else
			return false;
	}



	/**
	 * 计算发送短信的时候需要花费的短信条数
	 * @param  string $text
	 * 
	 * @return unsigned_int 需要花费的短信条数
	 */
	protected function calcSMSNum($text)
	{
		$strlen = mb_strlen($text,'utf-8');
		if ($strlen <= 70)
			return 1;
		else
		{
			return ceil($strlen / 67);
		}
	}



	/**
	 * 检查当前账户是否是临时访客
	 * 
	 * @return boolean true-访客,false-非访客
	 */
	protected function isVisitor()
	{
		$tmp = D('User')->getUserInfo(array('uid'=>getAdminUid()));
		if($tmp['admin_mobile'] == C('VISIT_USER_MOBILE'))
			return ture;
		else 
			return false;
	}





	/**
	 * 检查PaymentDetailsModel中class的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_PaymentDetailsModel_class($value)
	{
		if ( 	   ($value == 1)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查UserModel中rpg的合法性
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-合法,false-不合法
	 */
	protected function check_UserModel_rpg($value)
	{
		if ( 	   ($value == 2)
				|| ($value == 3)
				|| ($value == 4)
				|| ($value == 7)
                || ($value == 8)
                || ($value == 9)
                || ($value == 10)
                || ($value == 11)
                || ($value == 12)
			)
			return true;
		else
			return false;
	}



	/**
	 * 检查该值是不是全部由空格组成
	 * @param  unsigned_int $value
	 * 
	 * @return boolean true-不是,false-是
	 */
	protected function check_not_all_blank($value)
	{
		$i = 0;
		$count = mb_strlen($value,'utf-8');
		for ($i = 0; $i < $count; $i++)
			if ($value[$i] != ' ')
				return true;
		return false;
	}

	/**
	 * 处理时间戳,将开始时间改为当天00:00:00 结束时间改为23:59:59
	 *
	 * @param mixed|null $data
	 * @param unsigned_int $reg_st_time 创建时间的开始时间，只要是当天内就好，服务器会自动计算成为当天开始的00:00:00
	 * @param unsigned_int $reg_end_time 创建时间的结束时间，只要是当天内就好，服务器会自动计算成为当天结束的23:59:59
	 *  
	 * @return array
	 */
	protected function getTimeMap(array $data=null)
	{
		//检验时间格式
		if(isset($data['reg_st_time']))
        { 
            if($this->check_unix_date($data['reg_st_time']))
                $this->reg_st_time = $data['reg_st_time'];
            else
                throw new \XYException(__METHOD__,-14012);
        }


        if(isset($data['reg_end_time']))
        {
            if($this->check_unix_date($data['reg_end_time']))
                $this->reg_end_time = $data['reg_end_time'];
            else
                throw new \XYException(__METHOD__,-14013); 
        }

		//计算开始时间和结束时间
		if (!isset($data['reg_st_time']))
			unset($this->reg_st_time);
		else
		{
			$tmpDate = null;
			$tmpDate = getdate($this->reg_st_time);
			$this->reg_st_time  = mktime(0,0,0,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		}
		if (!isset($data['reg_end_time']))
			unset($this->reg_end_time);
		else
		{
			$tmpDate = null;
			$tmpDate = getdate($this->reg_end_time);
			$this->reg_end_time = mktime(23,59,59,$tmpDate['mon'],$tmpDate['mday'],$tmpDate['year']);
		}

		$timeMap = array();

		//reg_time filter
		if ( isset($this->reg_st_time) && isset($this->reg_end_time) )
		{
			$timeMap  = array('between',array($this->reg_st_time,$this->reg_end_time));
		}
		elseif ( isset($this->reg_st_time) && (!isset($this->reg_end_time)) )
		{
			$timeMap  = array('egt',$this->reg_st_time);
		}
		elseif ( (!isset($this->reg_st_time)) && (isset($this->reg_end_time)) )
		{
			$timeMap  = array('elt',$this->reg_end_time);
		}

		return $timeMap;

	}

    /**自动计算出当月最后一天23点59分59秒的时间戳
     * @param null $timeStamp
     * @author DizzyLee<728394036@qq.com>
     */
	public function getMonthLastDay($timeStamp = null)
    {
        $date = getdate($timeStamp);
        $tmp_day = mktime(0,0,0,$date['mon'],1,$date['year']);
        $time = strtotime('+1 month -1 day',$tmp_day)+86399;
        return $time;
    }

    /**自动计算本周第一天0点0分0秒的时间戳
     * @param null $timeStamp
     * @author DizzyLee<728394036@qq.com>
     */
    public function getWeekFirstDay($timeStamp = null)
    {
        $date = getdate($timeStamp);
        $tmp_day = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
        $time = strtotime('last sunday +1 day', $tmp_day);
        return $time;
    }
    /**自动计算本周最后一天23点59分59秒的时间戳
     * @param null $timeStamp
     * @author DizzyLee<728394036@qq.com>
     */
    public function getWeekLastDay($timeStamp = null)
    {
        $date = getdate($timeStamp);
        $tmp_day = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
        $time = strtotime('next monday -1 day', $tmp_day)+86399;
        return $time;
    }

    /**检测Rpg_mode是否合法
     * @param null $rpg_mode
     * @return bool
     * @author DizzyLee<728394036@qq.com>
     */
    public function check_rpg_mode($rpg_mode = null)
    {
        if (
            $rpg_mode    != self::XY_ESS_MODE
            && $rpg_mode != self::BANK_MODE
        )
            return false;
        else
            return true;
    }
	
}
