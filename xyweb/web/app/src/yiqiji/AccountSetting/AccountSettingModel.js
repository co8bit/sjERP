'use strict'

xy.factory('AccountSettingModel', ['$log','EventService', 'PageService', 'NetworkService',
    function($log, EventService, PageService, NetworkService) {
		var model = {};
		model.accountList = [];
		model.isUpdate = false;
		EventService.on(EventService.ev.START_ACCOUNT, function(event,arg){
			var dataToSend = {
				page: 1,
				pline: 20,
				type: 1,//查询全部是1，启用是2
			}
			//账户设置 打开的新建
			NetworkService.request('queryAccount',dataToSend,function (data){
				angular.copy(data.data,model.accountList);
				PageService.setNgViewPage('AccountSetting');
			})
		});
		return model;
    }
])