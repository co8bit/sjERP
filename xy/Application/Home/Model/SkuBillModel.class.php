<?php
namespace Home\Model;
use Think\Model;

/**
 * sku_单据 关联表.
 * 用途：看sku/spu是被哪个单据卖了.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class SkuBillModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//SkuBillModel_create_
		array('sku_id', 'isUnsignedInt',-14002,
			self::MUST_VALIDATE, 'function',self::SkuBillModel_create_), //sku_id不合法
		array('spu_id', 'isUnsignedInt',-14003,
			self::MUST_VALIDATE, 'function',self::SkuBillModel_create_), //spu_id不合法
		array('bill_class', 'isUnsignedInt',-14004,
			self::MUST_VALIDATE, 'function',self::SkuBillModel_create_), //bill_class不合法
		array('bill_status', 'isNonnegativeInt',-14005,
			self::MUST_VALIDATE, 'function',self::SkuBillModel_create_), //bill_status不合法
		array('reg_time', 'isUnsignedInt',-14006,
			self::MUST_VALIDATE, 'function',self::SkuBillModel_create_), //reg_time不合法
		array('oid', 'isUnsignedInt',-14007,
			self::EXISTS_VALIDATE, 'function',self::SkuBillModel_create_), //oid不合法
		array('wid', 'isUnsignedInt',-14008,
			self::EXISTS_VALIDATE, 'function',self::SkuBillModel_create_), //wid不合法

        //SkuBillModel_queryOrderAndWarehouse
        array('sku_id', 'isUnsignedInt',-14002,
            self::MUST_VALIDATE, 'function',self::SkuBillModel_queryOrderAndWarehouse), //sku_id不合法
    );




	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::SkuBillModel_create_,'function'),//填入所属创建者uid
        array('admin_uid','getAdminUid',self::SkuBillModel_queryOrderAndWarehouse,'function'),//填入所属创建者uid
	);


	/**
	 * 创建sku/spu与单据的关联记录
	 * @internal server
	 *
	 * @param mixed|null $data POST的数据
	 * 
	 * - 必填参数：
	 * - @param unsigned_int $sku_id
	 * - @param unsigned_int $spu_id
	 * - @param unsigned_int $bill_class oid或wid的原单据的类别
	 * - @param unsigned_int $bill_status oid或wid的原单据的状态
	 * - @param unsigned_int $reg_time oid或者wid的单据创建时间，为了保持和交易单或库管单时间的一致，所以需要传入，而不是动态获取
	 * - 选填参数（以下两个二选一）：
	 * - @param unsigned_int $oid
	 * - @param unsigned_int $wid
	 *
	 * @return 1 ok
	 * @throws \XYException
	 * 
	 * @author co8bit <me@co8bit.com>
	 * @version 1.1
	 * @date    2016-10-30
	 */
    public function create_(array $data = null)
    {
    	if (!$this->field('sku_id,spu_id,oid,wid,bill_class,bill_status,reg_time')->create($data,self::SkuBillModel_create_))
			throw new \XYException(__METHOD__,$this->getError());

		if ( (empty($this->oid)) && (empty($this->wid)) )
			throw new \XYException(__METHOD__,-14009);

		$tmpReturn = $this->add();

		if ($tmpReturn > 0)
			return 1;
		else
			throw new \XYException(__METHOD__,-14000);
    }



    /**
     * 得到sku、spu是否已经产生了业务往来
     * @internal server
     *
     * @param unsigned_int $id 要查询的sku_id或spu_id的值
     * @param enum $type 'sku'|'spu'.  'sku'-要查sku是否发生了业务往来，'spu'-要查spu是否发生了业务往来
     *
     * @return bool true-发生了业务往来,false-没发送业务往来
     * @throws \XYException
     *
     * @author co8bit <me@co8bit.com>
     * @version 1.1
     * @date    2016-10-31
     */
    public function isHasContacts($id,$type)
    {
    	if (!isUnsignedInt($id)) throw new \XYException(__METHOD__,-14501);
    	if ( ($type === 'sku') || ($type === 'spu') )
    		;
    	else
    		throw new \XYException(__METHOD__,-14502);

    	$key = $type.'_id';
    	$tmp = $this->where(array($key=>$id,'admin_uid'=>getAdminUid()))->find();

    	if ($tmp === false)
    		throw new \XYException(__METHOD__,-14000);
    	elseif (empty($tmp))
    		return false;
    	else
    		return true;
    }




    /**
     * SKU 详情，查看业务往来
     * @internal server
     * @param mixed|null $data POST的数据
     * @param unsigned_int $sku_id 要查询的sku_id
     * @param unsigned_int $reg_st_time 订单过滤开始时间
     * @param unsigned_int $reg_end_time 订单过滤结束时间
     * @param string       $field  查询结果所需要的字段
     * @param unsigned_int $sto_id sku所在的仓库id
     * @param json $filter class、status 
     *        - array $class 单据类别编号：不传代表全部。
     *        - array $status 单据状态：不传代表全部，a-可选值：1、2、5、90.  b-未完成传90。
     *
     * @return array 返回订单的数组和统计数据
     * @throws \XYException
     *
     * @author wtt <wtt@xingyunbooks.com>
     * @version 1.9
     * @date    2017-05-01
     */
    public function queryOrderAndWarehouse(array $data = null)
    {
        //验证数据是否合法
        if(!$this->field('sku_id')->create($data,self::SkuBillModel_queryOrderAndWarehouse))
            throw new \XYException(__METHOD__,$this->getError());

        //手动验证其他字段
        if(isset($data['page']))
        {
            if (isUnsignedInt($data['page']))
                $this->page = $data['page'];
            else
                throw new \XYException(__METHOD__,-14010);
        }

        if(isset($data['pline']))
        {
            if(isUnsignedInt($data['pline']))
                $this->pline = $data['pline'];
            else
                throw new \XYException(__METHOD__,-14011);
        }

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

        if(is_string($data['field']))
            $this->field = $data['field'];
        else
            throw new \XYException(__METHOD__,-14014);

        if(isset($data['sto_id']))
        {
            if(isUnsignedInt($data['sto_id']))
                $this->sto_id = $data['sto_id'];
            else
                throw new \XYException(__METHOD__,-14015);
        }
        else
            throw new \XYException(__METHOD__,-14015);

        if(isset($data['filter'])){
            if(is_string($data['filter']))
                $this->filter = $data['filter'];
            else
                throw new \XYException(__METHOD__,-14050);
        }


        $filter = json_decode(I('param.filter','',''),true);
        if(!empty($data['filter'])){
            //filter条件不为空的时候判断转换是否成功
            if(empty($filter))
            {
                throw new \XYException(__METHOD__,-14050);
            }
        }

        // 判断前端传递的sku_id在数据库中是否存在;
        $tmp = D('sku')->get_(array('sku_id'=>$this->sku_id));
        if(empty($tmp)){
            throw new \XYException(__METHOD__,-14002);
        }

        // 根据 SKU ID 获取其所在的 N 个订单的编号
        $skuBillRet = $this->field('oid,wid')
            ->where(array('sku_id' => $this->sku_id,'admin_uid'=>getAdminUid()))->select();

        // 遍历订单获取所有 OID,WID 单独存储,以备查询
        $oidList = array();
        $widList = array();
        foreach($skuBillRet as $item => $value){
            // 一个 SKU ,WID 和 OID 不会同时存在
            if(intval($value['oid']) != 0){
                $oidList[] = $value['oid'];
            }
            elseif(intval($value['wid']) != 0){
                $widList[] = $value['wid'];
            }
        }

        // 拼接 condition
        $commondition = array();
        $commondition['reg_time'] = $this->getTimeMap(array('reg_st_time'=>$this->reg_st_time,'reg_end_time'=>$this->reg_end_time));

        //设置oid、wid
        if(count($oidList) > 0)
            $orderMap['oid'] = array('in',$oidList);
        else
            $orderMap['oid'] = null;

        if(count($widList) > 0)
            $warehouseMap['wid'] = array('in',$widList);
        else
            $warehouseMap['wid'] = null;
        //设置仓库
        $orderMap['sto_id'] = $this->sto_id;

        //设置class
        $class = array(1,2,3,4,53,54);
        if(!empty($filter['class'])){
            $classCondition = '(';
            foreach($filter['class'] as $value){
                $value = intval($value);
                if($this->checkDeny_query_class($value)){
                    //排除class值非1,2,3,4,53,54（销售单、销售退货单、采购单、采购退货单、盘点单、调拨单）的订单
                    if(in_array($value,$class))
                    { 
                        if($classCondition === '('){
                            $classCondition = '( `class` = '.$value;
                        }else
                        {
                            $classCondition .= ' or `class`='.$value;
                        }
                    }
                    else
                    {
                        throw new \XYException(__METHOD__,-14051);
                    }
                }else
                {
                    throw new \XYException(__METHOD__,-14051);
                }
            }
            $classCondition .= ')';
        }

        //设置status
        if(!empty($filter['status'])){
            $statusCondition = '(';
            foreach($filter['status'] as $value){
                $value = intval($value);
                if($this->checkDeny_query_status($value)){
                    if($statusCondition ==='(')
                        $statusCondition = '(  `status`=' .$value;
                    else
                        $statusCondition .= ' or `status`='.$value;
                        
                    if ($value == 90)
                            $statusCondition .= ' or `status`=2 or `status`=4 or `status`=5 or `status`=6 or `status`=7 or `status`=8 or `status`=9 or `status`=10 or `status`=11 or `status`=12';
                    
                }else
                {
                    throw new \XYException(__METHOD__,-14052);
                }
            }
            $statusCondition .= ')';
            log_("statusCondition",$statusCondition,$this);
        }


        //去掉草稿单和期初订单
        $commonCondition['status']=array('not in',array(99,100));
        $commonCondition['class'] = array('in',array(1,2,3,4,53,54));

        // 设置class 和 status条件
        if((!empty($filter['class'])) && (!empty($filter['status']))){
            unset($commonCondition['status']);
            unset($commonCondition['class']);
            $commonCondition['_string'] = '('.$classCondition. ') AND ('.$statusCondition.')';
        }elseif( (empty($filter['class'])) && (!empty($filter['status']))){
            unset($commonCondition['status']);
            $commonCondition['_string'] =  $statusCondition;
        }elseif( (!empty($filter['class'])) && (empty($filter['status'])) )
        {
            unset($commonCondition['class']);
            $commonCondition['_string'] =  $classCondition;
        }

        // 获取admin_uid 
        $commonCondition['admin_uid'] = getAdminUid();

        //拼接sql
        $orderSql = D('order')->where($commonCondition)->where($orderMap)->buildSql();
        if(empty($commonCondition['_string']))
            $commonCondition['_string'] = "(`sto_id` = ". $this->sto_id  . " or `new_sto_id` =" .$this->sto_id.")";
        else
            $commonCondition['_string'] .= "AND (`sto_id` = ". $this->sto_id  . " or `new_sto_id`=" .$this->sto_id.")";

        $warehouseSql = D('warehouse')->where($commonCondition)->where($warehouseMap)->buildSql();
        $unionSql ='( '.$orderSql.' UNION '.$warehouseSql.' )';
        log_("orderSql",$orderSql,$this);
        log_("warehouseSql",$warehouseSql,$this);
        log_("unionSql",$unionSql,$this);
        //计算总页数、当前页码
        if(!empty($data['page']) && !empty($data['pline']))//没有传代表是QueryModel::skuChart()在调用，不分页
        {
            $reData['total_page'] = ceil($this->table($unionSql.' tmp_union')->count()/$data['pline']) ;
            if($reData['total_page'] == 0)
                $reData['total_page'] = 1;
            if($this->page > $reData['total_page'])
                $this->page = 1;
            $reData['now_page'] = $this->page;
            $reData['data'] = $this
                ->table($unionSql.' tmp_union')
                ->field($this->field)
                ->order('reg_time desc')
                ->page($reData['now_page'],$this->pline)
                ->select();
        }else
        {
            $reData['data'] = $this
                ->table($unionSql.' tmp_union')
                ->field($this->field)
                ->order('reg_time desc')
                ->select();
        }

        if($reData['data'] === false){
            throw new \XYException(__METHOD__,-14000);
        }

        foreach($reData['data'] as $key=>&$value)
        {
            //将购物车内容转换为json
            if(!empty($value['cart']))
                {
                    $tmpSerialize = null;
                    $tmpSerialize = unserialize($value['cart']);
                    if($tmpSerialize === false){
                        throw new \XYException(__METHOD__,-14550);
                    }
                    $value['cart'] = $tmpSerialize;
                    //删除cart中非当前sku_id的数据
                    foreach($value['cart'] as $k1=>$v1)
                    {
                        if($v1['sku_id'] !=$this->sku_id)
                            {
                                unset($value['cart'][$k1]);
                            }
                    }
                }
        }

        //将返回数据的cart下标都改为0
        foreach($reData['data'] as $k=>&$v)
        {
            foreach($v['cart'] as $k1=>$v1)//因为得不到v['cart']里唯一的一个的值的下标，所以只能foreach
            {
                if($k1 != 0)
                {
                    $v['cart'][0] = $v['cart'][$k1];
                    unset($v['cart'][$k1]);
                }
                
                if( $v['wid'] != 0 && $v['class'] == 54 && $v['sto_id'] == $this->sto_id )
                    $v['cart'][0]['quantity'] = 0 - $v['cart'][0]['quantity']; //调出仓库数量改为负数
                
                if( $v['wid']!=0 && $v['class'] == 53 )
                    $v['cart'][0]['num'] = $v['cart'][0]['cu_stock_af']-$v['cart'][0]['cu_stock_bef'];
            }
        }
        return $reData;
    }
 
}

