'use strict'

xy.factory('IncomeAndExpendDetailModel', [ '$log', 'EventService', 'PageService', '$filter', 'QueryCatClass', 'NetworkService', '$q',
	function($log, EventService, PageService, $filter, QueryCatClass, NetworkService, $q) {
		var model = {};
		model.orderInfo = {};
		model.query = QueryCatClass.newQueryCat();
		model.isFinance = false;//财务审核
		model.isBoss = false;//boss审核
		model.isView = true;//查看模式
		model.dataLen = 0;
		model.dataIndex = 0;
		model.accountList = [];
		model.data = [];
		EventService.on(EventService.ev.START_VIEW_INCOME_DETAIL, function(event,arg){
			//查看模式 收入
			model.orderInfo = arg;
			model.isView = true;//查看模式
			model.Dialog = PageService.showDialog('IncomeAndExpendDetail');
		});


		EventService.on(EventService.ev.START_VIEW_EXPEND_DETAIL, function(event,arg){
			//查看模式 支出
			model.orderInfo = arg;
			model.isView = true;//查看模式
			model.Dialog = PageService.showDialog('IncomeAndExpendDetail');
		});

		/**
		 *	财务审核
	 	 * 	只有支出单需要财务审核
		 */
		EventService.on(EventService.ev.START_FINANCE_EXAMINE, function(event,arg){
			angular.copy(arg.data, model.data);	//传入数据数组
			model.dataIndex = arg.index; //传入的项所在位置
			model.isView = false;	//查看模式
			model.isFinance = true;	//财务审核
			model.isBoss = false;	//boss审核
			model.queryAccount().then(function (){
				model.Dialog = PageService.showDialog('IncomeAndExpendDetail')
			})
		});
		model.queryAccount = function(){
			var defered = $q.defer();
			//财务审核下加载账户列表
			var dataToSend = {
				page: 1,
				pline: 999999,
				type: 2,
			}
			NetworkService.request('queryAccount',dataToSend,function (data){
				for(var v of data.data){
					v.account_source_type_show = v.account_source_type==1 ? '银行' : '网络';
					v.cost = 0;//初始化accountlist的cost
				}
				angular.copy(data.data, model.accountList)
				defered.resolve();
			})
			return defered.promise;
		}
		/**
		 *	boss审核
		 */
		EventService.on(EventService.ev.START_BOSS_EXAMINE, function(event,arg){
			// $log.error('boss',arg)
			angular.copy(arg.data, model.data);	//传入数据数组
			model.dataIndex = arg.index; //传入的项所在位置
			model.isView = false;	//查看模式
			model.isFinance = false;	//财务审核
			model.isBoss = true;	//boss审核
			model.Dialog = PageService.showDialog('IncomeAndExpendDetail')
		});

		return model;
	}
])