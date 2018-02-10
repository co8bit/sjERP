// 收款单和付款单Model
'use strict'

angular.module('XY').factory('ReceiptAndPaymentModel', ['EventService', '$log', 'PageService', 'CstmrService', 'FinancialDocumentClass','FinanceService','LocalTableClass',
    function(EventService, $log, PageService, CstmrService, FinancialDocumentClass, FinanceService, LocalTableClass) {
        // $log.debug("ReceiptAndPaymentModel init")
        var model = {}

        function initLocalTable(orderList) {
            model.localTable = LocalTableClass.newLocalTable(orderList, [], 10);
            model.orderListShow = model.localTable.getShowData();
            model.localTable.changePline(model.calcPline());
        }

        model.calcPline = function() {
            var bodyHeight = $(".function-body").height();
            var tableHeight = bodyHeight - 500;
            var ajustedPline = Math.floor(tableHeight / 37);
            return 5;
        }

        //监听开始创建收款单事件
        EventService.on(EventService.ev.START_CREATE_RECEIPT, function(event, arg) {
            CstmrService.company.queryList(2);
            model.docInfo = FinancialDocumentClass.newReceipt();
            model.docInfo.cash = Number(model.docInfo.cash);
            model.docInfo.bank = Number(model.docInfo.bank);
            model.docInfo.online_pay = Number(model.docInfo.online_pay);
            initLocalTable(model.docInfo.orderList);
            model.item = arg;
            model.dialogId = PageService.showDialog('ReceiptAndPayment');
        })

        //监听开始创建付款单事件
        EventService.on(EventService.ev.START_CREATE_PAYMENT, function(event, arg) {
            CstmrService.company.queryList(2);
            model.docInfo = FinancialDocumentClass.newPayment();
            model.docInfo.cash = Number(model.docInfo.cash);
            model.docInfo.bank = Number(model.docInfo.bank);
            model.docInfo.online_pay = Number(model.docInfo.online_pay);
            initLocalTable(model.docInfo.orderList);
            model.dialogId = PageService.showDialog('ReceiptAndPayment');
        })

        //--------------------------继续开单


        EventService.on(EventService.ev.CONTINUE_CREATE_RECEIPT, function(event, arg) {
            CstmrService.company.queryList(2);

            FinanceService.queryOneDocument(arg, function(docInfo) {
                // 查到的财务单引用到model里
                model.docInfo = docInfo;
                initLocalTable(model.docInfo.orderList);
                model.dialogId = PageService.showDialog('ReceiptAndPayment');

            });

        })


        EventService.on(EventService.ev.CONTINUE_CREATE_PAYMENT, function(event, arg) {
            CstmrService.company.queryList(2);

            FinanceService.queryOneDocument(arg, function(docInfo) {
                // 查到的财务单引用到model里
                model.docInfo = docInfo;
                initLocalTable(model.docInfo.orderList);

                model.dialogId = PageService.showDialog('ReceiptAndPayment');

            });
        })

        //-----------------------继续开单

        // 查询待收款付款数据后赋给编辑中的订单对象
        EventService.on(EventService.ev.QUERY_REMAIN_SUCCESS, function(event, arg) {
            var tmp_orderList = arg;
            // if (tmp_orderList[0]) {
            //     tmp_orderList[0].money = model.docInfo.actual;
            // }
            model.docInfo.setOrderList(tmp_orderList);

            model.localTable.calc();
        })

        // 创建收款单成功
        EventService.on(EventService.ev.CREATE_RECEIPT_SUCCESS, function(event, arg) {
            PageService.showSharedToast('创建收款单成功');
            PageService.closeDialog();
        })

        // 创建收款单失败
        EventService.on(EventService.ev.CREATE_RECEIPT_ERROR, function(event, arg) {
            PageService.showSharedToast('创建收款单失败');
        })

        // 创建付款单成功
        EventService.on(EventService.ev.CREATE_PAYMENT_SUCCESS, function(event, arg) {
            PageService.showSharedToast('创建付款单成功');
            PageService.closeDialog();
        })

        // 创建付款单失败
        EventService.on(EventService.ev.CREATE_PAYMENT_ERROR, function(event, arg) {
            PageService.showSharedToast('创建付款单失败');
        })

        // 收付款单草稿成功
        EventService.on(EventService.ev.FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_SUCCESS, function() {
            PageService.showSharedToast('保存草稿成功');
            PageService.closeDialog(model.dialogId);
        })

        // 收入款单草稿失败
        EventService.on(EventService.ev.FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_ERROR, function() {
            PageService.showSharedToast('保存草稿失败');

        })



        return model

    }
])
