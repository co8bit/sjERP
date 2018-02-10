<?php
return array(
	
	//自己的key
	'XingYunBooks_UserAcount_SECRET' => 'v7J37g5FEUzCImR5x1L8SX9gUWb30mHQUzL8ZPYyldExoMHmxDFHKjBiLAkGVahB',//这里的key是随机的，不一定是要每个都一样
	'XingYunBooks_User_SECRET'       => 'v7J37g5FEUzCImR5x1L8SX9gUWb30mHQUzL8ZPYyldExoMHmxDFHKjBiLAkGVahB',
	'LOG_KEY'                        => 'v7J37g5FEUzCImR5x1L8SX9gUWb30mHQUzL8ZPYyldExoMHmxDFHKjBiLAkGVahB',
	'SendSMSStatementOfAccount_KEY'  => 'v7J37g5FEUzCImR5x1L8SX9gUWb30mHQUzL8ZPYyldExoMHmxDFHKjBiLAkGVahB',
	'SMS_KEY'                        => 'v7J37g5FEUzCImR5x1L8SX9gUWb30mHQUzL8ZPYyldExoMHmxDFHKjBiLAkGVahB',
	'PaymentDetails_KEY'             => 'v7J37g5FEUzCImR5x1L8SX9gUWb30mHQUzL8ZPYyldExoMHmxDFHKjBiLAkGVahB',
	'UC_AUTH_KEY'                    => 'v7J37g5FEUzCImR5x1L8SX9gUWb30mHQUzL8ZPYyldExoMHmxDFHKjBiLAkGVahB', //服务器端存储入数据库时的加密KEY
	'passwordMD5Prefix'              => 'sjerp',//前端md5(C('passwordMD5Prefix').$password)之后32bit给服务器时的前缀（生成假数据时使用）
	'USER_SEPARATE'                  => 'AkGVahB',//普通用户的username中的分隔符,修改需要同时修改UserModel中admin_mobile字段的检查长度
	
	
	//BeeCloud
	'BeeCloud_APP_ID'                => '',
	'BeeCloud_APP_SECRET'            => '',
	'BeeCloud_SELF_SECRET'           => '',
	
	
	//阿里大于
	'ALiDaYu_APPKEY'                 => '',
	'ALiDaYu_SECRET'                 => '',

	//七牛
    'accessKey'                      => '',
    'secretKey'                      => '',

    //MNS消息服务（消息队列）
	'MNS_KEY_ID'                     => '',
	'MNS_KEY_SECRET'                 => '',
    'PUBLIC_END_POINT'              => '',
	'PRIVATE_END_POINT'              => '',

);