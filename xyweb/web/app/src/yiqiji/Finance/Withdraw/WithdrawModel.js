'user strict'

xy.factory('WithdrawModel',['EventService', 'PageService', 'WithdrawClass', '$log', 'NetworkService', function(EventService, PageService, WithdrawClass, $log, NetworkService){
		var model = {};

		model.init = function (orderInfo){
			model.orderInfo = WithdrawClass.newWithdraw();
			if (orderInfo) {
				//继续开单则填充单据信息
				model.orderInfo.fillOrderInfo(orderInfo);
			}
		}

		EventService.on(EventService.ev.START_CREATE_WITHDRAW, function(event, arg){
            if(arg){ //新建收入单
            	model.init(arg);
            }else{
            	model.init();
            }
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
				model.Dialog = PageService.showDialog('CreateWithdraw');
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
