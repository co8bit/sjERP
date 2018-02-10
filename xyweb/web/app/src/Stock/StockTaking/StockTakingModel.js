//xxxxModel or Service or Class
'use strict'

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('StockTakingModel', ['EventService', '$q', '$log', 'PageService', 'OrderInfoClassService', 'StockService', 'UserService', 'LockService',
    function (EventService, $q, $log, PageService, OrderInfoClassService, StockService, UserService, LockService) {

        // $log.debug("StockTakingModel init")

        var model = {};
        // 响应开始创建盘点单事件
        EventService.on(EventService.ev.START_STOCK_TAKING, function () {
            LockService.getLockShopStatus(0, function () {
                LockService.lockShop(function () {
                    //开始盘点更新一下库存
                    var queryPromise = $q.all([StockService.queryCat(1), StockService.querySTO(1), UserService.user.getList(2)]);
                    model.orderInfo = OrderInfoClassService.newStockTakingOrder();
                    queryPromise.then(function () {
                        model.userList = UserService.getUserList();
                        model.stoList = StockService.getStoList();
                        model.catInfo = StockService.get_cats();
                        // StockService.querySKUSTO(1, model.stoList[0].sto_id).then(function () {
                        //     model.skuList = StockService.getSkuStoList();
                        //     model.defaultSto = model.stoList[0];
                            model.dialogId = PageService.showDialog('StockTaking');
                        // });
                    })
                });
            });
        });

        // 从盘点单草稿继续开盘点单
        EventService.on(EventService.ev.CONTINUE_STOCK_TAKING, function (event, arg) {
            LockService.getLockShopStatus(0, function () {
                LockService.lockShop(function () {
                    var queryPromise = $q.all([StockService.queryCat(1), StockService.querySTO(1), UserService.user.getList(2)]);
                    StockService.getStockOrder(arg,53,function (orderInfo) {
                        model.userList = UserService.getUserList();
                        model.stoList = StockService.getStoList();
                        model.catInfo = StockService.get_cats();
                        model.orderInfo = orderInfo;
                        model.orderInfo.check_name = "";
                        model.orderInfo.check_uid = undefined;
                        // $log.debug('盘点单 orderInfo',JSON.stringify(orderInfo))
                        // $log.debug('check_uid到底是什么',model.orderInfo.check_uid)
                        queryPromise.then(function () {
                           StockService.querySKUSTO(2,orderInfo.sto_id).then(function () {
                               model.skuInfo = StockService.getSkuStoList();
                               model.dialogId = PageService.showDialog('StockTaking');
                           })
                        })
                    });
                });
            });
        });
        // 创建盘点单成功
        EventService.on(EventService.ev.CREATE_STOCK_TAKING_SUCCESS, function () {
            PageService.showSharedToast('创建盘点单成功');
            PageService.closeDialog(model.dialogId);
        });

        // 创建盘点单失败
        EventService.on(EventService.ev.CREATE_STOCK_TAKING_ERROR, function () {
            PageService.showSharedToast('创建盘点单失败');
        })
        ;
        // 编辑盘点单api事件
        EventService.on(EventService.ev.warehouse_edit_, function (event, arg) {
            if (arg.ec > 0) {
                PageService.showSharedToast('编辑成功');
            } else {
                PageService.showSharedToast('编辑失败');
            }
            arg.complete();
        });

        // 编辑盘点单api事件
        EventService.on(EventService.ev.createStockTakingDraft, function (event, arg) {
            if (arg.ec > 0) {
                PageService.showSharedToast('盘点单保存草稿成功');
                PageService.closeDialog(model.dialogId);
                LockService.unlockShop();
                EventService.emit(EventService.ev.START_VIEW_DRAFT, 1);

            } else {
                PageService.showSharedToast('保存草稿失败');
            }
        });

        EventService.on(EventService.ev.ORDER_ARGS, function (evnet, args) {
            model.orderArgs = args;
        });

        EventService.on(EventService.ev.CREATE_SKU_SUCCESS, function () {
            StockService.querySKU(1);
        });

        return model; // or return model
    }
]);
