'use strict'

xy.factory('CreateEditAccountModel', ['$log', 'EventService', 'PageService', function($log, EventService, PageService) {
	var model = {};
	model.accountInfo = {};
	model.cashInfo = {};
	//初始化有参数则是编辑
	model.init = function(arg) {
		if(arg) {
			model.accountInfo.account_id= arg.account_id;
			model.accountInfo.account_creator = arg.account_creator; //开户人
			model.accountInfo.account_number = arg.account_source_type == 1 ? Number(arg.account_number) : arg.account_number; //银行账号是数字,网络是字符串
			model.accountInfo.account_name = arg.account_name; //账户名
			model.accountInfo.account_source_type = arg.account_source_type; //账户来源类型
			model.accountInfo.account_source_name = arg.account_source_name; //账户来源名
			model.accountInfo.account_balance = parseFloat(arg.account_balance); //账户预设余额
			model.accountInfo.account_remark = arg.account_remark; //备注
			model.accountInfo.bank_name = arg.bank_name; //开户行名
			model.accountInfo.province = arg.province; //省
			model.accountInfo.city = arg.city; //市
			model.accountInfo.qcode = arg.qcode; //速查码
			model.accountInfo.status = arg.status;//状态
		} else {
			model.accountInfo.account_creator = ''; //开户人
			model.accountInfo.account_number = ''; //账号
			model.accountInfo.account_name = ''; //账户名
			model.accountInfo.account_source_type = ''; //账户来源类型
			model.accountInfo.account_source_name = ''; //账户来源名
			model.accountInfo.account_balance = 0; //账户预设余额
			model.accountInfo.account_remark = ''; //备注
			model.accountInfo.bank_name = ''; //开户行名
			model.accountInfo.province = ''; //省
			model.accountInfo.city = ''; //市
			model.accountInfo.qcode = ''; //速查码
			model.accountInfo.status = 1;//状态
		}
	}
	//新建编辑 网络，银行账户
	EventService.on(EventService.ev.START_CREATE_ACCOUNT, function(event, arg) {
		model.isUpdate = false;//是否账户设置里进入的
		model.isCash = false;//是否现金账户
		model.isEdit = false;//是否编辑模式
		if(arg.type == 1) {
			// 资金账户内打开的新建
			if(arg.item){
				// arg说明是编辑
				model.init(arg.item);
				model.isEdit = true;
				model.dialogId = PageService.showDialog('CreateEditAccount');
			} else {
				// 没有是新建
				model.init()
				model.dialogId = PageService.showDialog('CreateEditAccount');
			}
		}else{
			// 开单时打开的新建
			model.init()
			model.isUpdate = true;
			model.dialogId = PageService.showDialog('CreateEditAccount');
		}
	});
	//编辑现金账户，现金账户只有编辑没有删除和新建
	EventService.on(EventService.ev.EDIT_CASH_ACCOUNT, function(event, arg) {
		//编辑现金账户
		model.isCash = true;
		model.cashInfo = arg;
		model.dialogId = PageService.showDialog('CreateEditAccount');
	})
	return model;
}])