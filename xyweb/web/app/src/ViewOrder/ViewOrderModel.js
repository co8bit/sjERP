'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('ViewOrderModel', ['EventService', '$log', 'PageService', 'MiscService', 'QueryService', 'QueryClass', 'AuthGroupService', 'UserService',
    function (EventService, $log, PageService, MiscService, QueryService, QueryClass, AuthGroupService, UserService) {

        var model = {};
        // 查询管理对象
        model.query = QueryClass.newQuery();

        model.query.setIsInitialOrderShow(false);
        model.pageState = {
            page: 1, //1表示今日单据 2表示所有单据
        };

        model.financeMode = null;

        model.cookie = {};//记录离开本页时本页的用户查询情况。
        /**
         * 获取模块显示状态
         */
        function getModuleIsShow() {
            model.isSalesOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.SalesOrder);
            model.isRequisitionShow = AuthGroupService.getIsShow(AuthGroupService.module.Requisition);
            model.isStockTakingShow = AuthGroupService.getIsShow(AuthGroupService.module.StockTaking);
            model.isReceiptAndPaymentShow = AuthGroupService.getIsShow(AuthGroupService.module.ReceiptAndPayment);
            model.isIncomeAndExpenseShow = AuthGroupService.getIsShow(AuthGroupService.module.IncomeAndExpense);
            model.isPurchaseOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.PurchaseOrder);
            model.isModifyOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.ModifyOrder);
            model.IsYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);
            model.userInfo = UserService.getLoginStatus();
            model.rpg = UserService.getLoginStatus().rpg;
        }

        // 查询今日单据和查询历史单据共用的初始化
        function init() {
            getModuleIsShow();
            // 新建一个查询管理对象
            // 装定初始查询条件
            model.query.setPage(1);
            model.query.setPline(8);

            var classOptionArr = [];

            if (model.isSalesOrderShow) {
                classOptionArr.push({
                    optionName: '销售单',
                    id: 1,
                });
                classOptionArr.push({
                    optionName: '销售退货单',
                    id: 2,
                });
            }

            if (model.isPurchaseOrderShow) {
                classOptionArr.push({
                    optionName: '采购单',
                    id: 3,
                });
                classOptionArr.push({
                    optionName: '采购退货单',
                    id: 4,
                });
            }

            if (model.isModifyOrderShow) {
                classOptionArr.push({
                    optionName: '应收款调整',
                    id: 5,
                });
                classOptionArr.push({
                    optionName: '应付款调整',
                    id: 6,
                });
            }

            if (model.isReceiptAndPaymentShow) {
                classOptionArr.push({
                    optionName: '收款单',
                    id: 71,
                });
                classOptionArr.push({
                    optionName: '付款单',
                    id: 72,
                });
            }
            if (model.isIncomeAndExpenseShow) {
                classOptionArr.push({
                    optionName: '其他收入单',
                    id: 73,
                });
                classOptionArr.push({
                    optionName: '支出费用单',
                    id: 74,
                });
            }
            if (model.isStockTakingShow) {
                classOptionArr.push({
                    optionName: '盘点单',
                    id: 53,
                });
                classOptionArr.push({
                    optionName: '调拨单',
                    id: 54,
                });
            }
            if (model.IsYqjShow) {
                if(model.rpg == 11){
                    //员工
                    classOptionArr.push({
                        optionName: '支出单',
                        id: 82,
                    });
                    model.query.setUid(model.userInfo.id);
                }

                if (model.rpg != 11) {
                    classOptionArr.push({
                        optionName: '支出单',
                        id: 82,
                    });
                    classOptionArr.push({
                        optionName: '收入单',
                        id: 81,
                    });
                    classOptionArr.push({
                        optionName: '提现单',
                        id: 83,
                    });
                    classOptionArr.push({
                        optionName: '转账单',
                        id: 84,
                    });
                    classOptionArr.push({
                        optionName: '发票填补单',
                        id: 85,
                    });
                }
            }
            if (model.IsYqjShow) {
                var statusOptionArr = [{
                    optionName: '财务待审核',
                    id: 81,
                }, {
                    optionName: '财务已通过',
                    id: 82,
                }, {
                    optionName: '财务已驳回',
                    id: 83,
                }, {
                    optionName: '老板待审阅',
                    id: 82,
                }, {
                    optionName: '老板已通过',
                    id: 84,
                }, {
                    optionName: '老板已驳回',
                    id: 85,
                }, {
                    optionName: '已完成',
                    id: 84,
                }, {
                    optionName: '已删除',
                    id: 3,
                }];
            } else {
                var statusOptionArr = [{
                    optionName: '未完成',
                    id: 90,
                }, {
                    optionName: '已完成',
                    id: 1,
                }, {
                    optionName: '异常',
                    id: 2,
                }, {
                    optionName: '暂不通知库管',
                    id: 5,
                }, {
                    optionName: '红冲单据',
                    id: 91,
                }, {
                    optionName: '已删除',
                    id: 3,
                }
                ];
                var financeTypeOptionArr = [{
                    optionName: '现金',
                    id: 101,
                }, {
                    optionName: '银行',
                    id: 102,
                }, {
                    optionName: '网络',
                    id: 103,
                },
                ];


                var bigClassOptionArr = [{
                    optionName: '交易类',
                    id: 111,
                }, {
                    optionName: '财务类',
                    id: 112,
                }, {
                    optionName: '其他类',
                    id: 113,
                },
                ];
                if(model.rpg ==3){
                    bigClassOptionArr.splice(1,1)
                }
            }
            model.query.getClassCheckbox().setOption(classOptionArr);
            model.query.getStatusCheckbox().setOption(statusOptionArr);
            model.query.getFinanceTypeCheckbox().setOption(financeTypeOptionArr);
            model.query.getBigClassCheckbox().setOption(bigClassOptionArr);
            //初始查看单据
            model.query.getClassCheckbox().selectAll(1);
        }

        function calcPline() {
            var bodyHeight = $(".function-body").height()
            var tableHeight = bodyHeight - 250
            // $log.debug('tableHeight',tableHeight)
            var ajustedPline = Math.floor(tableHeight / 36)
            return ajustedPline
        }

        function calcPline1() {
            var bodyHeight = $(".function-body").height()
            var tableHeight = bodyHeight - 300
            // $log.debug('tableHeight',tableHeight)
            var ajustedPline = Math.floor(tableHeight / 36)
            return ajustedPline
        }

        // 响应查看今日单据事件
        EventService.on(EventService.ev.BACK_TO_VIEW_TODAY_ORDER, function (event, args) {
            init();
            // model.isReview = false;//预设通过该事件进入的访问都是首次访问
            model.cookie = args;//初始化cookie

            model.orderPage = 1;

            // 计算合适的每页行数
            var ajustedPline = calcPline();
            model.query.setPline(ajustedPline);
            //库管用户只能看盘点单
            if (model.rpg == 3) {
                model.query.isWarehouse(true);
            }
            // model.query.cleanTime(); // 清除时间，查看今日单据不按时间筛选
            model.query.resetTime();
            model.pageState.page = 1; // 页面上是否隐藏日期选择器的依据
            // 异步任务计数器
            var task = MiscService.newAsyncTask(1);
            // 设定回调
            task.setFinalProc(function () {
                // 初始查询完成后清除回调
                model.query.setCustomCallback(undefined);
                // 都完成时跳转
                PageService.setNgViewPage('NullPage');
                PageService.setNgViewPage('ViewOrder');

            })

            // 新建一个管理查询的对象
            model.query.setCustomCallback(function () {
                // 查询成功后计数
                task.finishOneProc();
            });

            model.query.request();
        });

        EventService.on(EventService.ev.BACK_TO_VIEW_ALL_ORDER, function (event, args) {
            init();
            // model.isReview = false;//预设通过该事件进入的访问都是首次访问
            model.cookie = args;//初始化cookie
            model.financeMode = '';
            model.time = '';
            model.orderPage = 2;

            // 计算合适的每页行数
            var ajustedPline = calcPline1()
            model.query.setPline(ajustedPline)

            model.query.cleanTime();
            model.pageState.page = 2; // 页面上是否隐藏日期选择器的依据
            //库管用户只能看盘点单
            if (model.rpg == 3) {
                model.query.isWarehouse(true);
            }
            // 异步任务计数器
            var task = MiscService.newAsyncTask(1);
            // 设定回调
            task.setFinalProc(function () {
                // 初始查询完成后清除回调
                model.query.setCustomCallback(undefined);
                // 都完成时跳转
                PageService.setNgViewPage('NullPage');
                PageService.setNgViewPage('ViewOrder');

            })

            // 新建一个管理查询的对象
            model.query.setCustomCallback(function () {
                // 查询成功后计数
                task.finishOneProc();
            });

            model.query.request();

            model.financeMode = undefined;
        });

        EventService.on(EventService.ev.START_VIEW_TODAY_ORDER, function () {
            init();
            model.financeMode = '';
            model.time = '';
            model.isReview = false;//预设通过该事件进入的访问都是首次访问
            model.cookie = {};//初始化cookie
            model.orderPage = 1;

            // 计算合适的每页行数
            var ajustedPline = calcPline();

            model.query.setPline(ajustedPline);
            // model.query.cleanTime(); // 清除时间，查看今日单据不按时间筛选
            model.query.resetTime();
            model.pageState.page = 1; // 页面上是否隐藏日期选择器的依据
            //库管用户只能看盘点单
            if (model.rpg == 3) {
                model.query.isWarehouse(true);
            }
            // 异步任务计数器
            var task = MiscService.newAsyncTask(1);
            // 设定回调
            task.setFinalProc(function () {
                // 初始查询完成后清除回调
                model.query.setCustomCallback(undefined);
                // 都完成时跳转
                PageService.setNgViewPage('NullPage');
                PageService.setNgViewPage('ViewOrder');

            })

            // 新建一个管理查询的对象
            model.query.setCustomCallback(function () {
                // 查询成功后计数
                task.finishOneProc();
            });

            model.query.request();

        });

        // EventService.on(EventService.ev.VIEW_BACK_TO_ORDER,function(){
        //     model.isReview = true;
        // });

        // 响应查看所有单据事件
        EventService.on(EventService.ev.START_VIEW_ALL_ORDER, function (event, arg) {
            init();
            model.isReview = false;//预设通过该事件进入的访问都是首次访问
            model.cookie = {};//初始化cookie

            model.orderPage = 2;

            // 计算合适的每页行数
            var ajustedPline = calcPline1()
            model.query.setPline(ajustedPline)

            model.query.cleanTime();
            model.pageState.page = 2; // 页面上是否隐藏日期选择器的依据

            // 异步任务计数器
            var task = MiscService.newAsyncTask(1);
            // 设定回调
            task.setFinalProc(function () {
                // 初始查询完成后清除回调
                model.query.setCustomCallback(undefined);
                // 都完成时跳转
                PageService.setNgViewPage('NullPage');
                PageService.setNgViewPage('ViewOrder');

            })
            //库管用户只能看盘点单
            if (model.rpg == 3) {
                model.query.isWarehouse(true);
            }
            // 新建一个管理查询的对象
            model.query.setCustomCallback(function () {
                // 查询成功后计数
                task.finishOneProc();
            });
            if (arg != undefined && !isNaN(arg) && arg.type == undefined) {
                model.query.setStartTime(new Date(arg * 1000).getTime()); // 设置查询起始时间
                model.query.setEndTime(new Date(arg * 1000).getTime()); //设置查询结束时间
            }
            if (arg != undefined && arg.type != undefined) {
                model.financeMode = arg.type;
                model.time = arg.time;
                model.query.setStartTime(new Date(arg.time * 1000).getTime()); // 设置查询起始时间
                model.query.setEndTime(new Date(arg.time * 1000).getTime()); //设置查询结束时间
            } else {
                model.financeMode = '';
            }

            model.query.request();

        });

        return model; // or return model

    }
]);