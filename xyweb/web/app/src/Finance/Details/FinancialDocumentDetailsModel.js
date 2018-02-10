'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('FinancialDocumentDetailsModel', ['EventService', '$log','PageService','FinancialDocumentClass','FinanceService',
    function(EventService, $log, PageService, FinancialDocumentClass,FinanceService) {

        var model = {};

        // 单据信息
        model.docInfo = undefined;
        // 对话框id
        model.dialogId = undefined;

        // 响应查看财务单详情事件,arg是fid
        EventService.on(EventService.ev.FINANCE_VIEW_DETAILS,function(event,args){

            // 请求要查询的财务单详情
            FinanceService.queryOneDocument(args,function(docInfo){
                // 查到的财务单引用到model里
                model.docInfo = docInfo;
                model.docInfo.orderList = docInfo.orderList;
                // 根据单据类型显示界面,73/74是收入支出单
                if ((docInfo.class == 73)||(docInfo.class == 74)) {
                    model.dialogId = PageService.showDialog('IncomeAndExpenseDetails');
                }

                 $log.log('FinancialDocumentDetailsModel',docInfo);
                 $log.log('history',history);
                // 71/72收款付款单
                if ((docInfo.class == 71)||(docInfo.class == 72)) {
                    model.dialogId = PageService.showDialog('ReceiptAndPaymentDetails');
                }
            });
        });


        EventService.on(EventService.ev.ORDER_ARGS,function(evnet,args) {
            model.orderArgs = args;
        })


        EventService.on(EventService.ev.finance_editDocument,function(event,arg){
            $log.debug('arg =', arg)
            if (arg.ec > 0) {
                PageService.showSharedToast('编辑财务单成功');
                // PageService.closeDialog(model.dialogId);
                // EventService.emit(EventService.ev.FINANCE_VIEW_DETAILS,model.docInfo.fid);
            } else {
                PageService.showSharedToast('编辑财务单失败');
                arg.complete()
            }
        });

        return model; // or return model
    }
]);
