<?php
namespace Home\Controller;
use Think\Controller;
/**
 * 不需要权限
 */
class StatementOfAccountController extends BaseController
{


	/**
	 * 显示账单
	 * @internal
	 * @param string $pwd 客户输入的查询密码
	 * @return
	 */
	public function show_statement()
	{
		if (IS_POST)
		{
			try
			{
				$this->jsonReturn(D('StatementAccount')->show_statement(I('param.')));
				/*$data = D('StatementAccount')->show_statement(I('param.'));
				$this->assign('data',$data);
				$this->display();*/
			}catch(\XYException $e)
			{
				$this->jsonErrorReturn($e->getCode());
			}
		}
		else
		{
			$s_guid = I('param.s_guid/s','','htmlspecialchars');
			log_("s_guid",$s_guid,$this);
			$this->assign('s_guid',$s_guid);
			$this->display('statementWechatOnMobile');
		}
	}

	public function index()
	{
		$this->display();
	}
}