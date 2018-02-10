<?php
namespace Home\Model;
use Think\Model;

/**
 * 向我们付钱时的类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class CusListModel extends BaseadvModel
{

	/* 自动验证 */
	protected $_validate = array(
		//CusListModel_createRecord
		array('cid', 'isUnsignedInt',-15002,
			self::MUST_VALIDATE, 'function',self::CusListModel_createRecord), //cid不合法
	);


	/* 自动完成 */
	protected $_auto = array(
		//FaceRecModel
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
	);

    /**
     * 获取最新的访客列表
     *
     * @api 
     *
     * @author wtt <wtt@xingyunbooks.com>
     * @version 1.11
     * @date    2017-07-17    
     */
    public  function getCusList(array $data = null,$isAlreadyStartTrans = false)
    {
    	try
    	{
    		if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);
    		//删除访客列表中更新时间1小时前的数据
			$time = NOW_TIME-3600;
			$delRes = $this->where('update_time<'.$time)->delete();
			// log_("_sql",$this->_sql(),$this);
			if($delRes === false)
				throw new \XYException(__METHOD__,-26000); //允许删除的行数为0
			$db = new CusListModel();
	    	$reData = $db->where('admin_uid='.getAdminUid())->order('update_time desc')->select();
	    	// log_("this->sql_()",$this->_sql(),$this);
	    	// log_("reData",$reData,$this);

	    	if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
	    	return $reData;
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
     * 添加访客列表数据
     *
     * @internal
     *
     * @author wtt <wtt@xingyunbooks.com>
     * @version 1.11
     * @date    2017-07-17    
     */
    public function createRecord(array $data,$isAlreadyStartTrans=false)
    {
    	try
    	{
    		if (!$isAlreadyStartTrans) $this->startTrans(__METHOD__);

    		if(!$this->field('cid,company_name,photo')->create($data,self::CusListModel_createRecord))
    			throw new \XYException(__METHOD__,$this->getError());
    		$res = $this->add();
    		if($res)
	    		return $insertRes;
	    	else
	    		throw new \XYException(__METHOD__,-26000);
    		if (!$isAlreadyStartTrans) $this->commit(__METHOD__);
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
 

}