<?php
namespace Home\Model;
use Think\Model;

/**
 * 存储打印模板的表.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class PrintTemplateModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		//PrintTemplateModel_create_
		array('class', array(1,1), -19002,
			self::MUST_VALIDATE, 'between',self::PrintTemplateModel_create_), //class不合法
		array('font_size', array(1,200), -19003,
			self::MUST_VALIDATE, 'between',self::PrintTemplateModel_create_), //font_size不合法

		//PrintTemplateModel_get_
		array('class', array(1,1), -19002,
			self::MUST_VALIDATE, 'between',self::PrintTemplateModel_get_), //class不合法

		//PrintTemplateModel_WBXcreate_
		array('class', array(1,1), -19002,
			self::MUST_VALIDATE, 'between',self::PrintTemplateModel_WBXcreate_), //class不合法
		array('font_size', array(1,200), -19003,
			self::MUST_VALIDATE, 'between',self::PrintTemplateModel_WBXcreate_), //font_size不合法
		array('admin_uid', 'isNonnegativeInt', -19502,
			self::MUST_VALIDATE, 'function',self::PrintTemplateModel_WBXcreate_), //admin_uid不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::PrintTemplateModel_create_,'function'),//填入所属创建者uid
	);


	/**
	 * 创建一个打印模板。如果相同class、admin_uid的记录已经存在，则更新。
	 *
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $class
	 * @param unsigned_int $font_size 表格里的字体大小
	 * @param unsigned_int $content 打印模板
	 * @param json $optionArray 选项数组。形如：[1,0,1,0,1…………]
	 *
	 * @return 1 ok
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.3
	 */
    public function create_(array $data = null)
    {
    	if (!$this->field('class,font_size')->create($data,self::PrintTemplateModel_create_))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpContent = I('param.content','',false);
		if (empty($tmpContent))
			$tmpContent = $data['content'];
		if (empty($tmpContent))
			throw new \XYException(__METHOD__,-19004);

		$this->content = base64_encode($tmpContent);


		$tmpTextOptionArray = I('param.optionArray','','');
		if (empty($tmpTextOptionArray))
			$optionArrayTmp = $data['optionArray'];
		else
			$optionArrayTmp = json_decode(I('param.optionArray','',''),true);
		if (empty($optionArrayTmp))
			throw new \XYException(__METHOD__,-19050);

		foreach ($optionArrayTmp as $key => $value)
		{
			if ( isNonnegativeInt($key) && isNonnegativeInt($value) )
				;
			else
			{
				throw new \XYException(__METHOD__,-19050);
			}
		}

		$this->optionArray = serialize($optionArrayTmp);
		if (empty($this->optionArray))
			throw new \XYException(__METHOD__,-19050);


		//update or create
		$stackBack  = null;
		$stackBack  = $this->data;
		$tmpInfo    = $this->get_(array('class'=>$this->class),true);
		$this->data = null;
		$this->data = $stackBack;

		if (empty($tmpInfo))
		{
			$tmpReturn = $this->add();
			if ($tmpReturn > 0)
				return 1;
			else
				throw new \XYException(__METHOD__,-19000);
		}
		else
		{
			$tmpReturn = $this->where(array('print_template_id'=>$tmpInfo['print_template_id']))->save();
			if ($tmpReturn >= 0)
				return 1;
			else
				throw new \XYException(__METHOD__,-19000);
		}
    }




    /**
	 * 得到打印模板
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isInCreate 是否在PrintTemplate::create_()里调用，如果是，则查询出来为null可以被允许 @internal
	 * 
	 * @param unsigned_int $class 类别
	 * 
	 * @return array 数据库中一行
	 * @throws \XYException
	 */
	public function get_(array $data = null,$isInCreate = false)
	{
		if (!$this->field('class')->create($data,self::PrintTemplateModel_get_))
			throw new \XYException(__METHOD__,$this->getError());

		$tmp = $this->where(array('admin_uid'=>getAdminUid(),'class'=>$this->class))->find();

		if ($tmp === null)
		{
			if ($isInCreate)
				return null;//必须在这里退出，不然会被下面的语句带上content字段，就不为空了
			else
				throw new \XYException(__METHOD__,-19501);
		}
		elseif ($tmp === false)
			throw new \XYException(__METHOD__,-19000);

		unset($tmp['admin_uid']);
		$tmp['content'] = base64_decode($tmp['content']);

		$tmpSerialize = null;
		$tmpSerialize = unserialize($tmp['optionArray']);
		if ($tmpSerialize === false)
				throw new \XYException(__METHOD__,-19050);
		$tmp['optionArray'] = $tmpSerialize;

		return $tmp;
	}













////////////////////////////////////////后门接口


	/**
	 * 创建一个打印模板。如果相同class、admin_uid的记录已经存在，则更新。
	 *
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param unsigned_int $class
	 * @param unsigned_int $font_size 表格里的字体大小
	 * @param unsigned_int $content 打印模板
	 * @param unsigned_int $admin_uid 管理员uid
	 * @param json $optionArray 选项数组。形如：[1,0,1,0,1…………]
	 *
	 * @return 1 ok
	 * @throws \XYException
	 *
	 * @author co8bit <me@co8bit.com>
	 * @version 1.3
	 */
    public function WBXcreate_(array $data = null)
    {
    	if (!$this->field('admin_uid,class,font_size')->create($data,self::PrintTemplateModel_WBXcreate_))
			throw new \XYException(__METHOD__,$this->getError());

		$tmpContent = I('param.content','',false);
		if (empty($tmpContent))
			$tmpContent = $data['content'];
		if (empty($tmpContent))
			throw new \XYException(__METHOD__,-19004);

		$this->content = base64_encode($tmpContent);


		$tmpTextOptionArray = I('param.optionArray','','');
		if (empty($tmpTextOptionArray))
			$optionArrayTmp = $data['optionArray'];
		else
			$optionArrayTmp = json_decode(I('param.optionArray','',''),true);
		if (empty($optionArrayTmp))
			throw new \XYException(__METHOD__,-19050);

		foreach ($optionArrayTmp as $key => $value)
		{
			if ( isNonnegativeInt($key) && isNonnegativeInt($value) )
				;
			else
			{
				throw new \XYException(__METHOD__,-19050);
			}
		}

		$this->optionArray = serialize($optionArrayTmp);
		if (empty($this->optionArray))
			throw new \XYException(__METHOD__,-19050);


		//update or create
		$stackBack  = null;
		$stackBack  = $this->data;
		$tmpInfo    = $this->WBXget_(array('admin_uid'=>$this->admin_uid,'class'=>$this->class),true);
		$this->data = null;
		$this->data = $stackBack;

		if (empty($tmpInfo))
		{
			$tmpReturn = $this->add();
			if ($tmpReturn > 0)
				return 1;
			else
				throw new \XYException(__METHOD__,-19000);
		}
		else
		{
			$tmpReturn = $this->where(array('print_template_id'=>$tmpInfo['print_template_id']))->save();
			if ($tmpReturn >= 0)
				return 1;
			else
				throw new \XYException(__METHOD__,-19000);
		}
    }















    /**
	 * 得到打印模板
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * @param bool $isInCreate 是否在PrintTemplate::create_()里调用，如果是，则查询出来为null可以被允许 @internal
	 * @param unsigned_int $admin_uid 管理员uid
	 * 
	 * @param unsigned_int $class 类别
	 * 
	 * @return array 数据库中一行
	 * @throws \XYException
	 */
	public function WBXget_(array $data = null,$isInCreate = false)
	{
		if (!$this->field('admin_uid,class')->create($data,self::PrintTemplateModel_get_))
			throw new \XYException(__METHOD__,$this->getError());

		if (!isNonnegativeInt($this->admin_uid))
			throw new \XYException(__METHOD__,-19502);

		$tmp = $this->where(array('admin_uid'=>$this->admin_uid,'class'=>$this->class))->find();

		if ($tmp === null)
		{
			if ($isInCreate)
				return null;//必须在这里退出，不然会被下面的语句带上content字段，就不为空了
			else
				throw new \XYException(__METHOD__,-19501);
		}
		elseif ($tmp === false)
			throw new \XYException(__METHOD__,-19000);

		unset($tmp['admin_uid']);
		$tmp['content'] = base64_decode($tmp['content']);

		$tmpSerialize = null;
		$tmpSerialize = unserialize($tmp['optionArray']);
		if ($tmpSerialize === false)
				throw new \XYException(__METHOD__,-19050);
		$tmp['optionArray'] = $tmpSerialize;

		return $tmp;
	}

}