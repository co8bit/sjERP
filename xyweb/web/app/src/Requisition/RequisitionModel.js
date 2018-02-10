'use strict'

angular.module('XY').factory('RequisitionModel', ['EventService','$q', '$log', 'PageService', 'OrderInfoClassService', 'StockService', 'UserService', 'LockService','OrderService',
    function (EventService,$q,$log, PageService, OrderInfoClassService, StockService, UserService, LockService,OrderService) {
        var model = {};


        var queryPromise = null;
        // 响应开始创建调拨单事件
        EventService.on(EventService.ev.START_REQUISITION, function () {
            queryPromise = $q.all([StockService.queryCat(1), StockService.querySTO(2),UserService.user.getList(2)]);
            LockService.getLockShopStatus(0, function () {
                LockService.lockShop(function () {
                    //开始调拨更新一下库存
                    model.orderInfo = OrderInfoClassService.newRequisitionOrder();
                    queryPromise.then(function () {
                        model.userList = UserService.getUserList();
                        model.stoList = StockService.getStoList();
                        model.catInfo = StockService.get_cats();
                        model.dialogId = PageService.showDialog('Requisition')
                        // StockService.querySKUSTO(1, model.stoList[0].sto_id).then(
                        //     function () {
                        //         model.skuInfo = StockService.getSkuStoList();
                        //     }
                        // )
                    })
                });
            });
        });
        EventService.on(EventService.ev.CONTINUE_REQUISITION,function (event,arg) {
            LockService.getLockShopStatus(0, function () {
                LockService.lockShop(function () {
                    StockService.getStockOrder(arg,54, function(orderInfo){
                        $log.error(orderInfo);
                        queryPromise = $q.all([StockService.queryCat(1), StockService.querySTO(2),UserService.user.getList(2)]);
                         model.orderInfo = orderInfo;
                         queryPromise.then(function () {
                             model.userList = UserService.getUserList();
                             model.stoList = StockService.getStoList();
                             model.catInfo = StockService.get_cats();
                             StockService.querySKUSTO(2, orderInfo.sto_id).then(
                                 function () {
                                     model.skuInfo = StockService.getSkuStoList();
                                     model.dialogId = PageService.showDialog('Requisition')
                                 }
                             )
                         })

                    })
                })
            });

        });
        EventService.on(EventService.ev.CREATE_STOCK_REQUISITION_SUCCESS,function () {
            PageService.showSharedToast("创建调拨单成功");
            model.dialogId = PageService.closeDialog('Requisition')
        });
        return model;
    }
])