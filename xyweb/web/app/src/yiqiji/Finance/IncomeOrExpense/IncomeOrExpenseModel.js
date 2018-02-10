//xxxxModel or Service or Class
'use strict'

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('IncomeOrExpenseModel', ['EventService', '$log', 'PageService', 'IncomeOrExpenseClass', 'QueryCatClass', '$q', 'NetworkService', 'UserService',
    function(EventService, $log, PageService, IncomeOrExpenseClass, QueryCatClass, $q, NetworkService, UserService) {

        var model = {};
        model.reg_time = '';
        model.accountList = [];
		model.query = QueryCatClass.newQueryCat();
        model.query.setType(2);
		model.userList = UserService.getUserList();
        model.init = function(arg,orderInfo) {
            // new一个订单
            model.orderInfo = IncomeOrExpenseClass.newIncomeOrExpense(arg);
            if (orderInfo) {
				model.orderInfo.fillOrderInfo(orderInfo);
            }
        }

        model.queryAccountList = function(){
        	let defered = $q.defer();
        	var dataToSend = {
        		page: 1,
        		pline: 999999,
                type: 2,
        	}
        	NetworkService.request('queryAccount',dataToSend,function (data){
        		var account = model.accountList;
        		angular.copy(data.data, model.accountList);
        		defered.resolve()
        	})
        	return defered.promise;
        }

        /**
         * [开始创建收入支出单]
         * @arg //接收参数
         * arg == 1  //新建开单 1是支出 2是收入
         * arg.Type == 1  //单据类型
         * arg.type == 2, arg.data == data //继续开单 data 是参数
         */
        EventService.on('START_CREATE_INCOME_OR_EXPENSE', function(event, arg){
    		model.init(arg);
        	$q.all([model.query.request(),model.queryAccountList()]).then(function (data){
        		model.Dialog = PageService.showDialog('CreateIncomeOrExpense');
        	})
        })

		EventService.on(EventService.ev.START_CONTINUE_INCOME_OR_EXPENSE, function(event,arg){
			model.init(arg.class,arg);
			model.reg_time = arg.reg_time;
			$q.all([model.query.request(),model.queryAccountList(),UserService.user.getList(2)]).then(function (data){
        		model.Dialog = PageService.showDialog('CreateIncomeOrExpense');
        	})
		});
		return model // or return model

    }
])
