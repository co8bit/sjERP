'use strict';

// 开销售单
xy.factory('SalesOrderModel', ['EventService', '$log', 'StockService', 'CstmrService', 'CartClassService', 'OrderInfoClassService', 'PageStateInfoClass', 'PageService', 'OrderService', 'LockService', 'NetworkService', 'SPUClass', '$q',
    function (EventService, $log, StockService, CstmrService, CartClassService, OrderInfoClassService, PageStateInfoClass, PageService, OrderService, LockService, NetworkService, SPUClass, $q) {

        var model = {};
        //new页面状态，保存当前激活的标签页等
        model.pageState = PageStateInfoClass.newPageStateInfo();
        model.selectedCatId = 0;
        // 建立映射
        model.catInfo = StockService.get_cats();

        // 存储选中的联系人
        model.selectedContact = {};
        //初始化选中公司ID
        model.selectedCompanyCid = null;
        model.isPrint = false;
        model.contactNameIsShow = false;

        //初始化时间模式为空
        model.timeModeTime = '';
        //初始开单不是重开
        model.reopen = false;
        var queryPromise = null;


        // 根据订单类型初始化model以开始一个新的开单流程
        // param arg int 订单类型
        model.init = function (arg) {
            // new一个订单
            model.orderInfo = OrderInfoClassService.newOrder(arg);
            // 更新商品信息
            queryPromise = $q.all([StockService.queryCat(2), StockService.querySTO(2), CstmrService.company.queryList(2), CstmrService.parkAddress.queryList()]);
            // 更新往来单位信息
            // CstmrService.company.queryList(2);
            // CstmrService.parkAddress.queryList();

        };
        // 监听事件进行初始化 新建单据
        EventService.on(EventService.ev.START_CREATE_ORDER, function (event, arg) {
            LockService.getLockShopStatus(0, function () {
                model.getOptionArray().then(function () {
                    model.class = arg;
                    if (arg >= 1 && arg <= 4 || arg == 54) {
                        model.init(arg);
                        queryPromise.then(function () {
                            model.stoList = StockService.getStoList();
                            model.reopen = false; //是否重新开单
                            model.item = ''; //新开采购单传入才有
                            model.defaultSto = model.stoList[0];
                            model.warehouse = null;
                            // var query_sto = arg == 3 ? "" : model.stoList[0];
                            StockService.querySKUSTO(2, model.stoList[0].sto_id).then(function () {
                                model.skuInfo = StockService.getSkuStoList();
                                model.dialogId = PageService.showDialog('SalesOrder');
                                EventService.emit(EventService.ev.SALES_ORDER_INIT);
                            })
                        });
                        Cookies.set('salesOrderType', arg);
                    }
                });
            });
        });

        // 监听事件进行初始化 新建采购单(主页进入)
        EventService.on(EventService.ev.START_CREATE_Buy_ORDER, function (event, arg) {
            LockService.getLockShopStatus(0, function () {
                model.getOptionArray().then(function () {
                    model.class = 3;
                    model.init(3);
                    queryPromise.then(function () {
                        model.stoList = StockService.getStoList();
                        model.reopen = false; //是否重新开单
                        model.item = arg; //新开采购单传入的item
                        model.defaultSto = model.stoList[0];
                        StockService.querySKUSTO(2).then(function () {
                            model.warehouse = {
                                sto_name: model.item.sto_name,
                                sto_id: model.item.sto_id,
                            };
                            model.skuInfo = StockService.getSkuStoList();
                            model.dialogId = PageService.showDialog('SalesOrder');
                            EventService.emit(EventService.ev.SALES_ORDER_INIT);
                        });
                    });
                });
                Cookies.set('salesOrderType', 3);
            });
        });

        // 监听事件进行初始化 继续草稿单
        EventService.on(EventService.ev.CONTINUE_CREATE_ORDER, function (event, arg) {
            LockService.getLockShopStatus(0, function () {
                model.getOptionArray().then(function () {
                    // 先查询要显示详情的订单，在回调中装填数据并显示页面
                    OrderService.queryOneOrder(arg, function (orderInfo) {
                        model.orderInfo = orderInfo;
                        model.reopen = false; //是否重新开单
                        model.item = ''; //新开采购单传入才有
                        model.orderInfo.off = parseFloat(model.orderInfo.off);
                        model.orderInfo.cash = parseFloat(model.orderInfo.cash);
                        model.orderInfo.bank = parseFloat(model.orderInfo.bank);
                        model.orderInfo.online_pay = parseFloat(model.orderInfo.online_pay);
                        orderInfo.status = 4;// 继续开单默认是立即发货
                        // 更新商品信息
                        //查询promise 更新往来单位信息
                        queryPromise = $q.all([StockService.queryCat(2), StockService.querySTO(2), CstmrService.company.queryList(2), CstmrService.parkAddress.queryList()]);
                        queryPromise.then(function () {
                            model.warehouse = {
                                sto_name: orderInfo.sto_name,
                                sto_id: orderInfo.sto_id
                            };
                            model.stoList = StockService.getStoList();
                            StockService.querySKUSTO(2, model.orderInfo.sto_id).then(function () {
                                model.skuInfo = StockService.getSkuStoList();
                                model.dialogId = PageService.showDialog('SalesOrder');
                            });
                        });
                    });
                })
            });
        });
        EventService.on(EventService.ev.SALEORDER_UPDATE, function (event, data) {
            StockService.querySTO(2).then(function () {
                model.stoList = StockService.getStoList();
                model.defaultSto = model.stoList[0];
                for (let item of model.stoList) {
                    if (item.sto_id == data) {
                        model.orderInfo.setWarehouse(item);
                    }
                }
                StockService.querySKUSTO(2,data).then(function () {
                    model.skuInfo = StockService.getSkuStoList();
                })
            })
        });
        //监听事件进行初始化 重新/复制单据
        EventService.on(EventService.ev.START_REOPEN_ORDER, function (event, arg) {
            LockService.getLockShopStatus(0, function () {
                model.getOptionArray().then(function () {
                    model.orderInfo = arg;
                    model.reopen = true; // 是否重新开单
                    model.item = ''; //新开采购单传入才有
                    //把接收到的金额转换成数字
                    model.orderInfo.off = parseFloat(arg.off);
                    model.orderInfo.cash = parseFloat(arg.cash);
                    model.orderInfo.bank = parseFloat(arg.bank);
                    model.orderInfo.online_pay = parseFloat(arg.online_pay);
                    model.orderInfo.freight = parseFloat(arg.freight);
                    //初始化重新开单的时间和状态 时间为now 状态为4/5默认为4要修改可以在开单时候修改
                    model.orderInfo.status = 4;
                    // 更新商品信息
                    //查询promise 更新往来单位信息
                    queryPromise = $q.all([StockService.queryCat(2), StockService.querySTO(2), CstmrService.company.queryList(2), CstmrService.parkAddress.queryList()]);
                    //准备工作结束后开始填写开单
                    queryPromise.then(function () {
                        model.stoList = StockService.getStoList();
                        model.defaultSto = model.stoList[0];
                        StockService.querySKUSTO(2, model.orderInfo.sto_id).then(function () {
                            model.warehouse = {
                                sto_name: model.orderInfo.sto_name,
                                sto_id: model.orderInfo.sto_id,
                            };
                            model.skuInfo = StockService.getSkuStoList();
                            model.dialogId = PageService.showDialog('SalesOrder');
                        });

                    })
                })
            })
        })
        //获取option
        model.getOptionArray = function () {
            var defered = $q.defer();
            NetworkService.request('getOptionArray', {}, function (data) {
                model.option_array = [];
                model.option_array = angular.copy(data.data.option_array, model.option_array)
                //送货信息
                if (data.data.option_array[101] == undefined || data.data.option_array[101] == 0) {
                    model.contactNameIsShow = false;
                } else {
                    model.contactNameIsShow = true;
                }
                //开单信息
                if (data.data.option_array[102] == undefined || data.data.option_array[102] == 0) {
                    model.isPrint = false; //开单
                } else {
                    model.isPrint = true; //开单并打印
                }
                defered.resolve();
            });
            return defered.promise;
        };
        //设置option
        model.setOptionArray = function () {
            function replacer(key, value) {
                if (typeof value === 'undefined') {
                    return 0;
                }
                return value;
            }

            var option_array = [];
            angular.copy(model.option_array, option_array);
            option_array[101] = model.contactNameIsShow == true ? 1 : 0;
            option_array[102] = model.isPrint == true ? 1 : 0;
            var dataToSend = {
                option_array: JSON.stringify(option_array, replacer),
            }
            NetworkService.request('setOptionArray', dataToSend, function (data) {
                model.getOptionArray();
            })

        }

        EventService.on(EventService.ev.CREATE_ORDER_SUCCESS, function (event, arg) {
            if (model.orderInfo.importWay === "reOpen") {//如果参数是reOpen 则是重开单据 会删除原来的单据
                OrderService.setOrderStatus(
                    {oid: model.orderInfo.oid, status: 3},
                    function () {
                        model.orderInfo.status = 3;
                    });
            }
            PageService.showSharedToast('开单成功');
            PageService.closeDialog(model.dialogId);
        });

        EventService.on(EventService.ev.CREATE_ORDER_ERROR, function (event, arg) {
            // PageService.closeDialog();
        });

        EventService.on(EventService.ev.ORDER_CREATE_DRAFT_SUCCESS, function () {
            PageService.showSharedToast('保存草稿成功');
            PageService.closeDialog(model.dialogId);
        });

        EventService.on(EventService.ev.CREATE_SKU_SUCCESS, function () {
            StockService.querySKU(1);
        });

        return model;

    }
]);