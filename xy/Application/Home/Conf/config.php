<?php
return array(
	//'配置项'=>'配置值'
	'USER_ALLOW_REGISTER'                =>true,//允许用户注册
	'USER_ALLOW_LOGIN'                   =>true,//允许用户登录
	
	'DEFAULT_INIT_MAX_LIMIT_EMPLOYEE'    =>2,//初始可创建的员工数量，请同步更改数据库表中的Config->MAX_LIMIT_EMPLOYEE的DEFAULT值,todo
	'DEFAULT_INIT_MAX_LIMIT_PARKADDRESS' =>100,//默认的一个用户可以创建的送货地址数量的上限
	'DEFAULT_INIT_MAX_LIMIT_CONTACT'     =>6,//默认的一个cid内的联系人数量上限
	'DEFAULT_INIT_MAX_LIMIT_PHONENUM'    =>6,//默认的一个联系人contact_id内的电话号数量上限
	'DEFAULT_INIT_MAX_LIMIT_CARLICENSE'  =>6,//默认的一个联系人contact_id内的车牌号数量上限
	
	
	
	
	'DATABASE_HISTORY_MAX_LENGTH'        => 16770000,//数据库允许的history字段的最大长度
);