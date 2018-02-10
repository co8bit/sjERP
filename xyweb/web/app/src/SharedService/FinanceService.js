//model or Service
'use strict';
// 财务单相关服务
xy.factory('FinanceService', ['EventService', '$log', 'StockService', 'CstmrService', 'NetworkService', 'OrderService', 'FinancialDocumentClass', 'MiscService',
    function (EventService, $log, StockService, CstmrService, NetworkService, OrderService, FinancialDocumentClass, MiscService) {

        var FinanceService = {}; // or model = {}

        // 生成符合API格式的购物车信息
        // param  {}     docInfo  订单对象
        // return string            购物车信息,用于装填到 dataToSend.cart
        function genCartStrForAPI(docInfo) {
            // 按照API格式装填每个订单付了多少钱
            var cart_arr = [];
            var tmp = {};
            for (var key in docInfo.orderList) {
                //必须得有金额不然草稿单打开会失败没有金额就填0
                tmp = {};
                tmp.oid = docInfo.orderList[key].oid;
                if (docInfo.orderList[key].money) {
                    tmp.money = docInfo.orderList[key].money;
                }else{
                    tmp.money = 0;
                }
                cart_arr.push(tmp);
            }

            var cart_data = {
                data: cart_arr,
            };

            //把skuData的内容变成json字符串
            var cart_str = JSON.stringify(cart_data);
            $log.debug('cart_str: ', cart_str);
            return cart_str;
        }

        // 把订单信息装填到用于API调用的对象
        // param  {}  orderInfo 订单对象
        // return {}            用于API调用的数据
        function genDatoToSend(docInfo) {

            var cash = isNaN(Number(docInfo.cash)) ? 0 : Number(docInfo.cash);
            var bank = isNaN(Number(docInfo.bank)) ? 0 : Number(docInfo.bank);
            var online_pay = isNaN(Number(docInfo.online_pay)) ? 0 : Number(docInfo.online_pay);

            // var now = parseInt(new Date().getTime() / 1000);
            var dataToSend = {
                bank: bank,
                fid: docInfo.fid,
                cid: docInfo.company.cid,
                cash: cash,
                class: docInfo._class,
                name: docInfo._name,
                online_pay: online_pay,
                remark: docInfo.remark,
                reg_time: docInfo.reg_time,
            }

            // 如果是收款付款单需要装填购物车信息
            if ((docInfo._class == 71) || (docInfo._class == 72)) {
                // 把购物车数据转成字符串
                var cart_str = genCartStrForAPI(docInfo);
                // 装填购物车信息
                dataToSend.cart = cart_str;
            }

            return dataToSend;
        }

        FinanceService.createIncomeOrExpense = function (docInfo) {
            var _class = docInfo._class // 先存一下单据类别，需要根据类别发出不同事件
            var dataToSend = genDatoToSend(docInfo);
            NetworkService.request('createIncomeOrExpense', dataToSend, function (data) {

                // 发出事件
                if (_class == 73) {
                    EventService.emit(EventService.ev.CREATE_INCOME_SUCCESS, {}, 1);
                }
                if (_class == 74) {
                    EventService.emit(EventService.ev.CREATE_EXPENSE_SUCCESS, {}, 1);
                }
                EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);

            }, function () {
                if (_class == 73) {
                    EventService.emit(EventService.ev.CREATE_INCOME_ERROR);
                }
                if (_class == 74) {
                    EventService.emit(EventService.ev.CREATE_EXPENSE_ERROR);
                }
            }, 1);

        }

        // 创建收入和支出单草稿
        FinanceService.createIncomeOrExpenseDraft = function (docInfo) {
            var dataToSend = genDatoToSend(docInfo);
            NetworkService.request('createIncomeOrExpenseDraft', dataToSend, function (data) {
                EventService.emit(EventService.ev.FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_SUCCESS, {});
                EventService.emit(EventService.ev.START_VIEW_DRAFT,1);

            }, function () {
                EventService.emit(EventService.ev.FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_ERROR);
            });
        }

        // 创建收款付款单
        FinanceService.createReceiptOrPayment = function (docInfo) {
            var _class = docInfo._class // 先存一下单据类别，需要根据类别发出不同事件

            var dataToSend = genDatoToSend(docInfo);

            NetworkService.request('createReceiptOrPayment', dataToSend, function (data) {

                // 发出事件
                if (_class == 71) {
                    EventService.emit(EventService.ev.CREATE_RECEIPT_SUCCESS, {}, 1);
                }
                if (_class == 72) {
                    EventService.emit(EventService.ev.CREATE_PAYMENT_SUCCESS, {}, 1);
                }
                EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);

            }, function () {

                if (_class == 71) {
                    EventService.emit(EventService.ev.CREATE_RECEIPT_ERROR);
                }
                if (_class == 72) {
                    EventService.emit(EventService.ev.CREATE_PAYMENT_ERROR);
                }
            }, 1);

        }

        // 创建收款付款单草稿
        FinanceService.createReceiptOrPaymentDraft = function (docInfo) {
            var dataToSend = genDatoToSend(docInfo);
            NetworkService.request('createReceiptOrPaymentDraft', dataToSend, function (data) {
                // 发出事件
                EventService.emit(EventService.ev.FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_SUCCESS, {}, 1);
                EventService.emit(EventService.ev.START_VIEW_DRAFT,1);
            }, function () {
                EventService.emit(EventService.ev.FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_ERROR);
            }, 1);
        }

        // 查询一个财务单
        // param int      fid       单号
        // param function callback  查询成功时的回调
        FinanceService.queryOneDocument = function (fid, callback) {
            var dataToSend = {
                fid: fid,
            }
            NetworkService.request('finance_queryOneDocument', dataToSend, function (data) {
                console.log(data);
                data.data.cash = parseFloat(data.data.cash);
                data.data.bank = parseFloat(data.data.bank);
                data.data.online_pay = parseFloat(data.data.online_pay);
                var tmpDoc;
                
                tmpDoc = FinancialDocumentClass.newDocFromApiOrderQueryOne(data.data);
                // $log.log('queryOneDocument tmpDoc: ',tmpDoc);
                MiscService.genShowContent(tmpDoc.orderList);
                if (tmpDoc.orderList) {
                    for (var i = 0; i < tmpDoc.orderList.length; i++) {
                        tmpDoc.orderList[i].remain_opposite = -tmpDoc.orderList[i].remain;
                    }
                    tmpDoc.orderList = MiscService.formatMoneyObject(tmpDoc.orderList, ['value', 'off', 'receivable', 'remain', 'remain_opposite', 'money']);
                }
                tmpDoc = MiscService.formatMoneyMisc(tmpDoc, ['cash', 'bank', 'income', 'online_pay']);
                if (callback) {
                    callback(tmpDoc);
                }
                EventService.emit(EventService.ev.FINANCE_QUERY_ONE_SUCCESS);
            }, function () {

            });
        }


        // 编辑财务单
        FinanceService.editDocument = function (docInfo) {
            var dataToSend = {
                fid: docInfo.fid,
                remark: docInfo.remark,
            }
            NetworkService.request('finance_editDocument', dataToSend, function () {

            }, function () {

            });
        }


        //初始化
        function init() {

        }

        init();

        return FinanceService; // or return model

    }
]);