<?php
namespace Home\Model;
use Think\Model;


/**
 * 官网的申请表控制器.
 *
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class ApplyModel extends BaseadvModel
{
	/* 用户模型自动验证 */
	protected $_validate = array(
		// array('apply_id', 'isUnsignedInt', -10211, self::MUST_VALIDATE, 'function',self::MODEL_UPDATE),//apply_id不合法
		// array('name', '1,32', -10201, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //姓名不合法
		// array('name', 'checkDenyMember', -10201, self::EXISTS_VALIDATE, 'callback',self::MODEL_BOTH), //姓名不合法
		// array('shop_name', '0,100', -10202, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //公司名不合法
		// array('industry_name', '1,500', -10203, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //行业名不合法
		// array('wechat', '0,500',-10204, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //微信不合法
		array('mobile', '0,50', -10205, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //电话不合法
		// array('email', '0,100', -10206, self::EXISTS_VALIDATE, 'length',self::MODEL_BOTH), //邮箱不合法
	);



	/* 用户模型自动完成 */
	protected $_auto = array(
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
	);



	/**
	 * 检测用户名是不是被禁止注册
	 * @param  string $name 用户名
	 * @return boolean          ture - 未禁用，false - 禁止注册
	 */
	protected function checkDenyMember($name)
	{
		switch ($name)
		{
			case "admin":
			case "administrator":
			case "杭州跃迁科技有限公司":
			case "跃迁科技":
			case "跃迁":
			case "星云":
			case "星云进销存":
			case "进销存":
			case "":return false;
		}
		return true;
	}



	/**
	 * 新建报名表
	 * @api
	 */
	public function create_($data = null)
	{
		if(!$this->field('name,shop_name,industry_name,wechat,mobile,email,where_know')->create($data,self::MODEL_INSERT))
			throw new \XYException(__METHOD__,$this->getError());

		if ( empty($this->wechat) && empty($this->mobile) && empty($this->email) )
			throw new \XYException(__METHOD__,-10210);

		$this->reg_ip          = get_client_ip(0,true);
		$this->reg_time_format = date("Y-m-d H:i:s",$this->reg_time);

		try
		{
			$ipInfo        = D('Util')->getIPInfo($this->reg_ip);
			$this->country = $ipInfo['country'];
			$this->area    = $ipInfo['area'];
			$this->region  = $ipInfo['region'];
			$this->city    = $ipInfo['city'];
			$this->county  = $ipInfo['county'];
			$this->isp     = $ipInfo['isp'];
		}catch(\XYException $e)
		{
			log_("ApplyModel得到ip信息出现问题",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
		}

		$backupData = $this->data;

		$primaryKey = $this->add();
		

		try
		{
			$emailBody =
				'<table border="1">
					<tr>
						<th>apply_id</th>
						<th>姓名</th>
						<th>公司名称</th>
						<th>行业</th>
						<th>微信</th>
						<th>电话</th>
						<th>邮箱</th>
						<th>从何处知道</th>
						<th>是否处理</th>
						<th>注册时间</th>
						<th>注册ip</th>
						<th>国家</th>
						<th>区域</th>
						<th>省份</th>
						<th>市</th>
						<th>县</th>
						<th>ISP服务商</th>
					</tr>
					<tr>' . 
						'<td>'.$primaryKey.'</td>' . 
						'<td>'.$backupData['name'].'</td>' . 
						'<td>'.$backupData['shop_name'].'</td>' . 
						'<td>'.$backupData['industry_name'].'</td>' . 
						'<td>'.$backupData['wechat'].'</td>' . 
						'<td>'.$backupData['mobile'].'</td>' . 
						'<td>'.$backupData['email'].'</td>' . 
						'<td>'.$backupData['where_know'].'</td>' . 
						'<td>'.'未处理'.'</td>' . 
						'<td>'.$backupData['reg_time_format'].'</td>' . 
						'<td>'.$backupData['reg_ip'].'</td>' . 
						'<td>'.$backupData['country'].'</td>' . 
						'<td>'.$backupData['area'].'</td>' . 
						'<td>'.$backupData['region'].'</td>' . 
						'<td>'.$backupData['city'].'</td>' . 
						'<td>'.$backupData['county'].'</td>' . 
						'<td>'.$backupData['isp'].'</td>' . 
					'</tr>
				</table>
				<br><br><br><br><br>
				详情请看：<a href="http://www.xingyunbooks.com/server">http://www.xingyunbooks.com/server</a>';


			D('Util')->sendMail(
					C('ADMIN_REGISTER_MAIL_LIST'),
					'【测试体验】有人申请测试体验',
					$emailBody
				);
		}
		catch(\XYException $e)
		{
			log_("ApplyModel发送邮件出现问题",$e->getCode(),__METHOD__,\Think\LOG::EMERG);
		}

		if ($primaryKey > 0)
			return $primaryKey;
		else
			throw new \XYException(__METHOD__,-10000);
	}



	/**
	 * 将申请表标记为已处理.
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $apply_id
	 * 
	 * @return 1 成功
	 * @throws \XYException
	 */
	public function done(array $data = null)
	{
		if(!$this->field('apply_id,isDone')->create($data,self::MODEL_UPDATE))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('apply_id'=>$this->apply_id))->save(array('isDone'=>1,'isDone'=>$this->isDone));
		if ( ($tmp === null) || ($tmp === false) )
			throw new \XYException(__METHOD__,-10000);

		return 1;
	}

}