'use strict'

xy.factory('TransferAndWithdrawDetail', [ '$log', 'EventService', 'PageService',
	function($log, EventService, PageService) {
		var model = {};
		model.orderInfo = {};
		EventService.on(EventService.ev.START_VIEW_WITHDRAW_DETAIL, function(event,arg){
			//提现开单
			model.orderInfo = arg;
			model.orderInfo.class_show = '提现';
			model.Dialog = PageService.showDialog('TransferAndWithdrawDetail');
		});
		EventService.on(EventService.ev.START_VIEW_TRANSFER_ACCOUNT_DETAIL, function(event,arg){
			//转账
			model.orderInfo = arg;
			model.orderInfo.class_show = '转账';
			model.Dialog = PageService.showDialog('TransferAndWithdrawDetail')
		});
	return model;
	}
])