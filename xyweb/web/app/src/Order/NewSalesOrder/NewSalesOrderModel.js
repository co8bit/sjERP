'use strict';

xy.factory('NewSalesOrderModel',['EventService', '$log', 'StockService', 'CstmrService', 'CartClassService', 'OrderInfoClassService', 'PageStateInfoClass', 'PageService','OrderService','LockService',
	function(EventService, $log, StockService, CstmrService, CartClassService, OrderInfoClassService, PageStateInfoClass, PageService, OrderService, LockService){

		var model = {};

		// 监听事件进行初始化
        // EventService.on(EventService.ev.START_CREATE_ORDER, function(event, arg) {
        //     LockService.getLockShopStatus(0, function () {
        //         // model.class = arg;
        //         // if (arg >= 1 && arg <= 4) {
        //         //     // model.init(arg);
        //         //     queryPromise.then(function () {
        //         //         model.dialogId = PageService.showDialog('NewSalesOrder');
        //         //     });
        //         // }
        //         $log.log('NewSalesOrderModel arg ',arg);
        //         model.dialogId = PageService.showDialog('NewSalesOrder');
        //     });
        // })



		return model;
	}    
]);