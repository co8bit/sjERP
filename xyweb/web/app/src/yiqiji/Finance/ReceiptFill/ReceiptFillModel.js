'use strict'

xy.factory('ReceiptFillModle', ['$log', 'EventService', 'PageService', 'NetworkService',
	function($log, EventService, PageService, NetworkService){
		var model = {};
		model.money_pit = 0;
		model.init = function (arg){
			if (arg) {

			}else{
				model.orderInfo = {
					'class': 85,
					'income' : 0,
					'remark': '',
				}
			}
		}
		EventService.on(EventService.ev.START_RECEIPT_FILL, function(event, arg){
			if (arg) {
				model.init(arg);
			}else{
				model.init();
			}
			var dataToSend = {};
			NetworkService.request('invoicePoolSummary',dataToSend,function (data){
				model.money_pit = data.data.money_pit;
				model.Dialog = PageService.showDialog('ReceiptFill');
			})
		})
		return model;
	}
])