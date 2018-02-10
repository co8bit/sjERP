'use strict'

//xxxxModel or Service or Class
'use strict'

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('ModifyIncomeOrExpenseModel', ['EventService', '$log', 'PageService','CstmrService',
    function(EventService, $log, PageService,CstmrService) {

        var model = {}

        //显示应收款调整单页面
        EventService.on(EventService.ev.START_MODIFY_INCOME, function(event, arg) {
        	model.docInfoClass = 5;
            model.dialogId = PageService.showDialog('ModifyIncomeOrExpense');
            CstmrService.company.queryList(2);
        })

        // 创建应收款调整单成功
        EventService.on(EventService.ev.CREATE_MODIFYINCOME_SUCCESS, function(event, arg) {
            PageService.showSharedToast('创建应收款调整单成功');
            PageService.closeDialog();
        })

        //显示应付款调整单页面
        EventService.on(EventService.ev.START_MODIFY_EXPENSE, function(event, arg) {
        	model.docInfoClass = 6;
            model.dialogId = PageService.showDialog('ModifyIncomeOrExpense');
            CstmrService.company.queryList(2);
        })
        // 创建应付款调整单成功
        EventService.on(EventService.ev.CREATE_MODIFYEXPENSE_SUCCESS, function(event, arg) {
            PageService.showSharedToast('创建应付款调整单成功');
            PageService.closeDialog();
        })



        return model // or return model

    }
])
