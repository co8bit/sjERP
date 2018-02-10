// AccountPayModel
'use strict';

angular.module('XY').factory('SkuDetailModel', ['EventService', '$log', 'PageService', 'MiscService', 'QueryService', 'QueryClass', 'AuthGroupService', 'LocalTableClass', 'StockService', 'NetworkService',
    function (EventService, $log, PageService, MiscService, QueryService, QueryClass, AuthGroupService, LocalTableClass, StockService, NetworkService) {
        var model = {};
        model.item = {};
        model.query = QueryClass.newQuerySkuSummary();
        model.itemDetail = {};
        /**
         * 获取模块显示状态
         */
        function getModuleIsShow() {
            // model.isSalesOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.SalesOrder);
            model.isSalesOrderShow = true;
            model.isStockTakingShow = AuthGroupService.getIsShow(AuthGroupService.module.StockTaking);
            model.isReceiptAndPaymentShow = AuthGroupService.getIsShow(AuthGroupService.module.ReceiptAndPayment);
            model.isIncomeAndExpenseShow = AuthGroupService.getIsShow(AuthGroupService.module.IncomeAndExpense);
            model.isRequisitionShow = AuthGroupService.getIsShow(AuthGroupService.module.Requisition);
        }

        // 查询今日单据和查询历史单据共用的初始化
        function init() {
            getModuleIsShow();
            // 新建一个查询管理对象
            // 装定初始查询条件

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
                classOptionArr.push({
                    optionName: '采购单',
                    id: 3,
                });
                classOptionArr.push({
                    optionName: '采购退货单',
                    id: 4,
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
            },
            ];

            model.query.getClassCheckbox().setOption(classOptionArr);
            model.query.getStatusCheckbox().setOption(statusOptionArr);
        }

        EventService.on(EventService.ev.START_CREATE_SkuDetail, function (event, item) {
            angular.copy(item, model.item);
            init();
            model.query.setPage(1);
            model.query.setPline(4);
            model.query.setSku_id(item.sku_id);
            model.query.setSto_id(item.sto_id);
            model.query.cleanTime();
            //
            model.buyCount = [];
            model.salesCount = [];
            model.monthArr = [];
            model.yearArr = [];
            // 异步任务计数器
            var task = MiscService.newAsyncTask(1);
            // 设定回调
            task.setFinalProc(function () {
                // 初始查询完成后清除回调
                model.itemDetail = model.query.getRealTimeData();
                model.query.setCustomCallback(undefined);
                StockService.querySKU(1).then(function () {
                    PageService.setNgViewPage('SkuDetail');
                })
                // 都完成时跳转
            });

            // 新建一个管理查询的对象
            model.query.setCustomCallback(function () {
                // 查询成功后计数
                task.finishOneProc();
            });
            model.query.request();
        });
        return model;
    }
]);