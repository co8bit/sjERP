//xxxxModel or Service or Class
'use strict'

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('IncomeAndExpenseModel', ['EventService', '$log', 'PageService', 'FinancialDocumentClass', 'FinanceService',
    function(EventService, $log, PageService, FinancialDocumentClass, FinanceService) {

        var model = {}

            // 单据信息
        model.docInfo = undefined

        model.initIncome = function() {
            this.docInfo = FinancialDocumentClass.newIncome();
        }

        model.initExpense = function() {
            this.docInfo = FinancialDocumentClass.newExpense();
        }

        // 监听开始业务事件
        EventService.on(EventService.ev.START_CREATE_INCOME, function() {
            // 准备开始业务
            model.initIncome()
            model.docInfo = FinancialDocumentClass.newIncome();
            model.docInfo.cash = Number(model.docInfo.cash);
            model.docInfo.bank = Number(model.docInfo.bank);
            model.docInfo.online_pay = Number(model.docInfo.online_pay);
                // 显示业务界面
            model.dialogId = PageService.showDialog('IncomeAndExpense');
        })

        EventService.on(EventService.ev.CONTINUE_CREATE_INCOME, function(event, arg) {
            FinanceService.queryOneDocument(arg, function(tmpDoc) {
                model.docInfo = tmpDoc;
                $log.debug('装填出来的income单据',tmpDoc);
                model.dialogId = PageService.showDialog('IncomeAndExpense');
            })
        })


        EventService.on(EventService.ev.START_CREATE_EXPENSE, function() {
            // 准备开始业务
            model.initExpense()
                // 显示业务界面
            model.dialogId = PageService.showDialog('IncomeAndExpense');
        })

        EventService.on(EventService.ev.CONTINUE_CREATE_EXPENSE, function(event, arg) {
            FinanceService.queryOneDocument(arg, function(tmpDoc) {
                model.docInfo = tmpDoc;
                $log.debug('装填出来的expense单据',tmpDoc);
                model.dialogId = PageService.showDialog('IncomeAndExpense');
            })
        })





        // 监听API结果显示提示
        EventService.on(EventService.ev.CREATE_INCOME_SUCCESS, function() {
            PageService.showSharedToast('创建其他收入单成功');
            PageService.closeDialog(model.dialogId);
        })

        EventService.on(EventService.ev.CREATE_INCOME_ERROR, function() {
            PageService.showSharedToast('创建其他收入单失败');
        })

        EventService.on(EventService.ev.CREATE_EXPENSE_SUCCESS, function() {
            PageService.showSharedToast('创建支出费用单成功');
            PageService.closeDialog(model.dialogId);
        })

        EventService.on(EventService.ev.CREATE_EXPENSE_ERROR, function() {
            PageService.showSharedToast('创建支出费用单失败');
        })

        // 收入支出草稿成功
        EventService.on(EventService.ev.FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_SUCCESS, function() {
            PageService.showSharedToast('保存草稿成功');
            PageService.closeDialog(model.dialogId);
        })

        // 收入支出草稿失败
        EventService.on(EventService.ev.FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_ERROR, function() {
            PageService.showSharedToast('保存草稿失败');
        })

        return model // or return model

    }
])
