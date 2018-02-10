// 动态查询服务
'use strict';

angular.module('XY').factory('QueryService', ['EventService', '$log', 'PageService', 'NetworkService', 'MiscService', 'AuthGroupService',
    function (EventService, $log, PageService, NetworkService, MiscService, AuthGroupService) {

        // $log.log('queryService');

        var QueryService = {};

        QueryService.query = {};

        // 动态查询api
        // param {} data 装填好的待发送数据
        // param func callback 查询成功时的回调，以返回的订单数组为参数
        QueryService.query.query = function (data, callback) {
            var dataToSend = data;

            NetworkService.request('query_query_', dataToSend, function (data) {
                // console.log(data);
                var tmp = [];
                angular.copy(data.data, tmp);
                MiscService.genShowContent(tmp.data); //向单据列表增加显示字段

                for (var i = 0; i < tmp.data.length; i++) {
                    tmp.data[i].remain_opposite = -tmp.data[i].remain;
                }
                tmp.data = MiscService.formatMoneyObject(tmp.data, ['remain', 'remain_opposite', 'cash', 'bank', 'online_pay', 'cost', 'selling_price']);
                tmp.data = MiscService.formatMoneyObjectAll(tmp.data, ['value', 'income']);
                console.log(tmp);

                callback(tmp);
            }, function () {

            })
        }

        // 查询草稿
        QueryService.query.queryDraft = function (data, callback) {
            var dataToSend = data;
            NetworkService.request('query_queryDraft', dataToSend, function (data) {
                var tmp = [];
                angular.copy(data.data, tmp);
                MiscService.genShowContent(tmp.data); //向单据列表增加显示字段
                callback(tmp);
                EventService.emit(EventService.ev.DRAFT_LOAD_SUCCESS);
            }, function () {

            })
        }

        // 标题栏搜索功能的api
        QueryService.query.search = function (data, callback) {
            var dataToSend = data;
            NetworkService.request('query_search', dataToSend, function (data) {
                var tmp = [];
                angular.copy(data.data, tmp);
                MiscService.genShowContent(tmp.data); //向单据列表增加显示字段
                $log.log('search_tmp', tmp);

                for (var i = 0; i < tmp.data.length; i++) {
                    tmp.data[i].zero = 0;
                }
                tmp.data = MiscService.formatMoneyObject(tmp.data, ['value', 'balance', 'unit_price', 'tot_price']);
                tmp.data = MiscService.formatMoneyZero(tmp.data, ['zero', 'remain']);

                callback(tmp);
            }, function () {

            })
        }

        // 标题栏搜索功能的api
        QueryService.query.dashboard = function (callback) {
            var dataToSend = {};
            // $log.error();
            if(AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow)){
                NetworkService.request('financeDashboard', dataToSend, function(data){
                    callback(data.data);
                })
            }else{
                NetworkService.request('query_dashboard', dataToSend, function (data) {

                    var tmp = data.data;

                    for (var i = 0; i < 29; i++) {
                        tmp.saleTop[i] = Number(tmp.saleTop[i]);
                    }
                    tmp.skuSaleTop = MiscService.formatMoneyObject(tmp.skuSaleTop, ['unit_price']);
                    tmp.skuStockTop = MiscService.formatMoneyObject(tmp.skuStockTop, ['unit_price']);
                    tmp.companyTop = MiscService.formatMoneyObject(tmp.companyTop, ['balance']);

                    callback(tmp);

                })
            }
        }

        // 报表
        QueryService.everydaySummarySheet = {};

        // 查询历史报表列表
        QueryService.everydaySummarySheet.query = function (data, callback) {
            var dataToSend = data;
            NetworkService.request('everydaySummarySheet_queryList', dataToSend, function (data) {
                var tmp = [];
                angular.copy(data.data, tmp);
                MiscService.formatTimeForSummary(tmp.data, 0); //向单据列表增加显示字段

                tmp.data = MiscService.formatMoneyObject(tmp.data, ['sale', 'receivable', 'actually_income', 'payable', 'actually_paid', 'expense', 'other_income', 'gross_profit']);

                callback(tmp);
                $log.log('tmp.data:', tmp.data);
            }, function () {

            })
        }

        QueryService.query.expense = function (data, callback) {
            var dataToSend = data;
            NetworkService.request('PaymentDetails_getPaybillAndSmsDetail', dataToSend, function (data) {
                // console.log(data);
                var tmp = [];
                angular.copy(data.data, tmp);
                MiscService.formatTime(tmp.data, 0);
                callback(tmp);
            }, function () {

            })
        }
        //库存详情使用查询
        QueryService.query.skuSummary = function (data, callback) {
            var dataToSend = data;
            NetworkService.request('query_skuSummary', dataToSend, function(data) {
                var tmp = [];
                angular.copy(data.data, tmp);
                MiscService.formatTime(tmp.data, 0);
                MiscService.genShowContent(tmp.data);
                // tmp.data = MiscService.formatMoneyObject (tmp.data,['value', 'income', 'remain', 'remain_opposite', 'cash', 'bank', 'online_pay', 'cost', 'selling_price']);
                callback(tmp)
            }, function () {

            })
        };
        //商品库存的的实时查询
        QueryService.query.realTime = function (data ,callback) {
            var dataToSend =data;
            NetworkService.request("getGood",dataToSend,function (data) {
                var tmp = {};
                angular.copy(data,tmp);
                callback(tmp)
            },function () {

            })
        };

        return QueryService; // or return model

    }
]);