//xxxxModel or Service or Class
'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('CstmrDetailsModel', ['EventService', '$log', 'PageService', 'MiscService', 'QueryService', 'CstmrService', 'QueryClass','AuthGroupService',
    function(EventService, $log, PageService, MiscService, QueryService, CstmrService, QueryClass, AuthGroupService) {

        var model = {};
        model.query = {};
        model.cstmrInfo = {};

        EventService.on(EventService.ev.UPDATE_CSTMR_NOW, function(event,cstmrInfo){
            model.cstmrInfo=cstmrInfo;
        });

        function getModuleIsShow() {
            // model.isSalesOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.SalesOrder);
            model.isSalesOrderShow = true;
            model.isStockTakingShow = AuthGroupService.getIsShow(AuthGroupService.module.StockTaking);
            model.isReceiptAndPaymentShow = AuthGroupService.getIsShow(AuthGroupService.module.ReceiptAndPayment);
            model.isModifyOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.ModifyOrder);
            model.isPurchaseOrderShow = AuthGroupService.getIsShow(AuthGroupService.module.PurchaseOrder);
        }

        // 监听查看往来详情
        EventService.on(EventService.ev.COMPANY_VIEW_DETAILS, function(event, args) {
            model.orderPage = 3;
            model.cstmrNowPage = args.nowPage;
            // $log.log('cstmrNowPage',model.cstmrNowPage);
            model.cstmrPage = args.cstmrPage;
            model.cid = args.cid;
            model.queryInfo = args.query;
            model.pageState = args.pageState;
            model.orderCookies = args.cookies;
            getModuleIsShow();
            // 新建一个查询管理对象
            model.query = QueryClass.newQuery();
            // 异步任务计数器
            var task = MiscService.newAsyncTask(2);
            // 设定回调
            task.setFinalProc(function() {
                // 初始查询完成后清除回调
                model.query.setCustomCallback(undefined);
                // 都完成时跳转
                PageService.setNgViewPage('CstmrDetails');
            })

            // 新建一个管理查询的对象
            model.query.setCustomCallback(function() {
                // 查询成功后计数
                task.finishOneProc();
            });

            // 装定初始查询条件
            model.query.setPage(1);
            model.query.setPline(8);
            model.query.setCid(args.cid);

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


            var statusOptionArr = [{
                optionName: '已完成',
                id: 1,
            }, {
                optionName: '异常',
                id: 2,
            }, {
                optionName: '暂不通知库管',
                id: 5,
            }, {
                optionName: '未完成',
                id: 90,
            }, {
                optionName: '已删除',
                id: 3,
            }, {
                optionName: '红冲单据',
                id: 91,
            },
            ];

            var balanceOptionArr = [{
                optionName: '产生应收',
                id: 1,
            }, {
                optionName: '产生应付',
                id: 2,
            }, {
                optionName: '结清',
                id: 3,
            }];

            model.query.getClassCheckbox().setOption(classOptionArr);
            model.query.getStatusCheckbox().setOption(statusOptionArr);
            model.query.getremainTypeCheckbox().setOption(balanceOptionArr);

            model.query.cleanTime();

            // model.query.setStartTime(1);
            // model.query.setEndTime(new Date());
            // model.setEndTime(new Date().endTime);

            model.query.request();

            // 请求往来单位自身信息
            CstmrService.company.get(args.cid, function(cstmrObj) {
                model.cstmrInfo = cstmrObj;
                task.finishOneProc();
            });

        })

        return model;

    }

]);
