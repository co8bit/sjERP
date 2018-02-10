'use strict'

xy.factory('TransferAccountsModel', ['EventService', '$log', 'PageService', 'NetworkService',
	function(EventService, $log, PageService, NetworkService) {
		var model = {};
		model.allAccountList = [];
		model.cost = 0;
		model.init = function(arg) {
			if(arg){
				model.orderInfo = {
					class: 84,
					remark: arg.remark,
					reg_time: arg.reg_time,
					income: arg.income,
					fid: arg.fid,
					cart: {
						data: {
							account_number: arg.cart.data.account_number,//转出账号
							account_name: arg.cart.data.account_name,//账户简称
							account_source_name: arg.cart.data.account_source_name,//银行
							account_balance: arg.cart.data.account_balance,//转账前余额
							account_source_type: arg.cart.data.account_source_type,//账户类型
							target_number: arg.cart.data.target_number,//转入账号
							target_name: arg.cart.data.target_name,//账户简称
							target_source_name: arg.cart.data.target_source_name,//银行
							target_balance: arg.cart.data.target_balance,//转账前余额
							target_source_type: arg.cart.data.target_source_type,//账户类型
							account_operate: {
								account_id: arg.cart.data.account_operate.account_id,//转出账户id
								target_id: arg.cart.data.account_operate.target_id,//转入账户id
								cost: arg.cart.data.account_operate.cost,//金额
							}
						}
					},
				}
				model.cost = arg.cart.data.account_operate.cost;
			}else{
				model.orderInfo = {
					class: 84,
					remark: '',
					reg_time: '',
					income: '',
					cart: {
						data: {
							account_number: '',//转出账号
							account_name: '',//账户简称
							account_source_name: '',//银行
							account_balance: '',//转账前余额
							target_number:'',//转入账号
							target_name: '',//账户简称
							target_source_name: '',//银行
							target_balance: '',//转账前余额
							account_operate: {
								account_id: '',//转出账户id
								target_id: '',//转入账户id
								cost: 0,
							}
						}
					}
				}
			}
		}
		EventService.on(EventService.ev.START_CREATE_TRANSFER_ACCOUNTS, function(event, arg) {
			if(arg) {
				model.init(arg)
			} else {
				model.init();
			}
			model.orderInfo._class = arg
			//账户信息
			model.accountData = {
				银行: [], //银行
				网络: [], //网络
				现金: [], //现金
			};

			var dataToSend = {
				page: 1,
				pline: 9999999999,
				type: 2,
			}
			NetworkService.request('queryAccount', dataToSend, function(data) {
				getAccountData(data.data)
				angular.copy(data.data, model.allAccountList);
				model.Dialog = PageService.showDialog('CreateTransferAccounts');
			})

		});

		function getAccountData(data) {
			var i = 0;
			for(var v of data) {
				if(v.account_source_type == 1) {
					model.accountData.银行.push(v);
				}
				if(v.account_source_type == 2) {
					model.accountData.网络.push(v);
				}
				if(v.account_source_type == 3) {
					model.accountData.现金.push(v);
				}
				i++
			}
		}
		return model;
	}
])