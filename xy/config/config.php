<?php
return array(
	//支付那里的：价格-费用表
	'CONST_FEE' => array(
					// array('title'=>'免费版'),//class 0
					
					//等比缩放（用来测试升级的数据）
					// array('title'=>'金卡','money'=>30,'cost'=>1),//class 1.  money:价格，cost:当升级计算抵扣时，每天的使用价格
					// array('title'=>'黑卡','money'=>50,'cost'=>1),//class 2
					// array('title'=>'钻石卡','money'=>300,'cost'=>10),//class 3
					
					//最便宜（用来测试新开和续费的数据）
					// array('title'=>'金卡','money'=>1,'cost'=>1),//class 1.  money:价格，cost:当升级计算抵扣时，每天的使用价格
					// array('title'=>'黑卡','money'=>1,'cost'=>1),//class 2
					// array('title'=>'钻石卡','money'=>1,'cost'=>10),//class 3

					//实际数值
					// array('title'=>'金卡','money'=>3000,'cost'=>100),//class 1.  money:价格，cost:当升级计算抵扣时，每天的使用价格
					// array('title'=>'黑卡','money'=>5000,'cost'=>166),//class 2
					// array('title'=>'钻石卡','money'=>30000,'cost'=>1000),//class 3
					// array('title'=>'短信充值'),//class 4

                    0 => array(0=>0,1=>0,3=>0,6=>0,12=>0,'title'=>'免费版'),//免费版
					1 => array(0=>0,12=>36000,'title'=>'1用户'),//金卡
					2 => array(0=>0,12=>88800,'title'=>'2用户'),//黑卡
					3 => array(0=>0,12=>99900,24=>169900,36=>219900,'title'=>'3用户'),//钻石卡
					4 => array(0=>0,12=>128800,24=>228800,36=>288800,'title'=>'4用户'),
                    5 => array(0=>0,12=>166600,24=>288800,36=>366600,'title'=>'5用户'),
                    6 => array(0=>0,12=>299900,24=>499900,36=>699900,'title'=>'6-10用户'),
                    7 => array(0=>0,'title'=>'10用户以上'),
                    8 => array(0=>0,'短信收费'),
				),
	//支付那里的：升级时已经用掉的天数的每天抵扣费。
	//此值越大，用户剩余的钱（可抵扣）就越少，也就是软件升级所花费的钱就越贵
	//so,如果一个地方没有值，则是取这行中最大的值填入，不然的话会造成无法升级（比如金卡升级到黑卡所需金额被扣成了负数）
	'CONST_COST_FEE' => array(
					0 => array(0=>9999999,1=>9999999,3=>9999999,6=>9999999,12=>9999999),//免费版
					1 => array(0=>9999999,12=>99),//1用户
					2 => array(0=>9999999,12=>243),//2用户
					3 => array(0=>9999999,12=>274,24=>465,36=>602),//3用户
                    4 => array(0=>9999999,12=>353,24=>627,36=>791),//4用户
                    5 => array(0=>9999999,12=>456,24=>791,36=>1004),//5用户
                    6 => array(0=>9999999,12=>822,24=>1370,36=>1918),//6-10用户
				),

	'MemberClass_EmployeeNum' => array(//会员类别与用户数对照表
					2,//class 0
					1,//class 1
					2,//class 2
					3,//class 3
                    4,//class 4
                    5,//class 5
                    10,//class 6
				),
	'MemberClass_smsMoney' => array(//会员类别与发送短信的费用的对照表
					8,//class 0
                    6,//class 1
                    6,//class 2
                    6,//class 3
                    6,//class 4
                    6,//class 5
                    6,//class 6
				),
	'MemberClass_giftMoney' => array(//会员类别与赠送的短信费用对照表
					0,//class 0
					500,//class 1
					1000,//class 2
					3000//class 3
				),
	


	//邮件组
	'ADMIN_REGISTER_MAIL_LIST' => array('wbx@xingyunbooks.com','trn@xingyunbooks.com','wh@xingyunbooks.com','lcj@xingyunbooks.com'),
	'EMERG_MAIL_LIST'          => array('wbx@xingyunbooks.com','trn@xingyunbooks.com','wh@xingyunbooks.com','lcj@xingyunbooks.com'),


	'ROOT_USER_MOBILE'  => '57128121110',
	'VISIT_USER_MOBILE' => '12345678901',

	'DELETED_MARKER'	=> '-已删除',//已删除商品、规格、分类、往来单位等的标识符
    'QINIU_DOMAIN'      => '',//七牛云给域名

);