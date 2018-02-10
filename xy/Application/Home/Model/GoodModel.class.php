<?php
namespace Home\Model;
use Think\Model;


/**
 * 商品类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class GoodModel extends BaseadvModel
{
	/**
	 * 检查sku_id的合法性
	 * @param  unsigned_int $sku_id 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_sku_id($sku_id)
	{
		return isUnsignedInt($sku_id);
	}



	/**
	 * 检查cat_id的合法性
	 * @param  unsigned_int $cat_id 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_cat_id($cat_id)
	{
		return isUnsignedInt($cat_id);
	}



	/**
	 * 检查spu_id的合法性
	 * @param  unsigned_int $spu_id 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_spu_id($spu_id)
	{
		return isUnsignedInt($spu_id);
	}

	/**
	 * 检查spu_id的合法性
	 * @param  unsigned_int $sto_id 
	 * @return boolean true-合法,false-不合法
	 */
	protected function checkDeny_sto_id($sto_id)
	{
		return isUnsignedInt($sto_id);
	}

	protected function check_finance_cart($spec_name)
    {
        $query_data = D('Sku')->where(array('spec_name' => $spec_name,'sku_class'=>1,'admin_uid'=>getAdminUid()))->find();
        if ($query_data === null)
            return true;
        else
            return false;
    }

}
