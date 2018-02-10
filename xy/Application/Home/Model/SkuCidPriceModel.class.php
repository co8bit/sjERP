<?php
namespace Home\Model;
use Think\Model;

/**
 * sku_cid_price 关联表.
 * 用途：查询某个sku在某个cid那里的最近售价.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class SkuCidPriceModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//SkuCidPriceModel_updateLastPrice
		array('sku_id', 'isUnsignedInt',-21002,
			self::MUST_VALIDATE, 'function',self::SkuCidPriceModel_updateLastPrice), //sku_id 不合法
		array('spu_id', 'isUnsignedInt',-21003,
			self::MUST_VALIDATE, 'function',self::SkuCidPriceModel_updateLastPrice), //spu_id 不合法
		array('cid', 'isUnsignedInt',-21004,
			self::MUST_VALIDATE, 'function',self::SkuCidPriceModel_updateLastPrice), //cid 不合法
		array('price1', 'isNonegativeReal', -21005,
			self::MUST_VALIDATE, 'function',self::SkuCidPriceModel_updateLastPrice),//price1 不合法
		array('quantity1', 'isNonegativeReal', -21006,
			self::MUST_VALIDATE, 'function',self::SkuCidPriceModel_updateLastPrice),//quantity1 不合法
		array('update_time', 'check_unix_date',-21007,
			self::MUST_VALIDATE, 'callback',self::SkuCidPriceModel_updateLastPrice), //update_time 不合法

		//SkuCidPriceModel_get_
		array('sku_id', 'isUnsignedInt',-21002,
			self::MUST_VALIDATE, 'function',self::SkuCidPriceModel_get_), //sku_id 不合法
		array('cid', 'isUnsignedInt',-21004,
			self::MUST_VALIDATE, 'function',self::SkuCidPriceModel_get_), //cid 不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::SkuCidPriceModel_updateLastPrice,'function'),//填入所属创建者uid
	);



	/**
	 * 创建sku/spu与cid的price关联记录
	 * @internal server
	 *
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $sku_id
	 * @param unsigned_int $spu_id
	 * @param unsigned_int $cid
	 * @param unsigned_int $price1 最近售价1
	 * @param unsigned_int $quantity1 最近卖出数量1（与price1对应）
	 * @param unsigned_int $update_time 更新时间，为了保持和交易单或库管单时间的一致，所以需要传入，而不是动态获取
	 *
	 * @return 1 ok
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 1.5
	 */
    public function updateLastPrice(array $data = null)
    {
    	if (!$this->field('sku_id,spu_id,cid,price1,quantity1,update_time')->create($data,self::SkuCidPriceModel_updateLastPrice))
			throw new \XYException(__METHOD__,$this->getError());

		$stackBack  = null;
		$stackBack  = $this->data;
		$tmpInfo    = $this->get_(array('sku_id'=>$this->sku_id,'cid'=>$this->cid),true);
		$this->data = null;
		$this->data = $stackBack;

		if (empty($tmpInfo))
		{
			$tmpReturn = $this->add();
			if ($tmpReturn > 0)
				return 1;
			else
				throw new \XYException(__METHOD__,-21000);
		}
		else
		{
			$tmpReturn = $this->where(array('sku_cid_price_id'=>$tmpInfo['sku_cid_price_id']))->save();

			if ($tmpReturn >= 0)
				return 1;
			else
				throw new \XYException(__METHOD__,-21000);
		}
    }





    /**
	 * 得到某个sku的某个cid的最近售价
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isInUpdateLastPrice 是否在SkuCidPriceModel::updateLastPrice()里调用，如果是，则查询出来为null可以被允许 @internal
	 * 
	 * @param unsigned_int $sku_id
	 * @param unsigned_int $cid
	 * 
	 * @return array ['price1'=>10.5,'quantity1'=>12].最近售价与最近销售数量的数组
	 * @throws \XYException
	 */
	public function get_(array $data = null,$isInUpdateLastPrice = false)
	{
		if (!$this->field('sku_id,cid')->create($data,self::SkuCidPriceModel_get_))
		{
			return null;//因为用户不选客户直接选货也是被允许的
			// throw new \XYException(__METHOD__,$this->getError());
		}

		$tmp = $this->where(array('admin_uid'=>getAdminUid(),'sku_id'=>$this->sku_id,'cid'=>$this->cid))->find();

		if ($tmp === null)
		{
			if ($isInUpdateLastPrice)
				return null;//必须在这里退出，不然会被下面的语句带上reData，就不为空了
			else
				throw new \XYException(__METHOD__,-21501);
		}
		elseif ($tmp === false)
			throw new \XYException(__METHOD__,-21000);

		$reData              = null;
		$reData['price1']    = $tmp['price1'];
		$reData['quantity1'] = $tmp['quantity1'];

		if ($isInUpdateLastPrice)
			$reData['sku_cid_price_id'] = $tmp['sku_cid_price_id'];

		return $reData;
	}


}