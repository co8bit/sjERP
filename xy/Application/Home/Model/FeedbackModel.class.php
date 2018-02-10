<?php
namespace Home\Model;
use Think\Model;

/**
 * 反馈类Model.
 * 
 * - 参考文档：{@see \Doc\doc_view()}
 * - 错误代码：{@see \ErrorCode\ErrorCode()}
 */
class FeedbackModel extends BaseadvModel
{
	/* 自动验证 */
	protected $_validate = array(
		array('content', '1,1000',-10001,self::MUST_VALIDATE, 'length',self::MODEL_INSERT), //content长度不合法
	);



	/* 自动完成 */
	protected $_auto = array(
		array('admin_uid','getAdminUid',self::MODEL_BOTH,'function'),//填入所属创建者uid
		array('reg_time', NOW_TIME, self::MODEL_INSERT),
		array('update_time', NOW_TIME, self::MODEL_BOTH),
	);



	/**
	 * 创建一条反馈.
	 * 
	 * @api
	 * @param mixed|null $data POST的数据
	 * 
	 * @param string $content 内容
	 * 
	 * @return unsigned_int 成功 > 0
	 * @throws \XYException
	 */
	public function create_(array $data = null)
	{
		if(!$this->field('content')->create($data,self::MODEL_INSERT))
			throw new \XYException(__METHOD__,$this->getError());

		$this->uid = getUid();
		
		$feedback_id = $this->add();

		if ($feedback_id > 0)
			return $feedback_id;
		else
			throw new \XYException(__METHOD__,-9000);
	}

}
