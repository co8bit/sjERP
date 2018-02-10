//CstmrModel
'use strict';

angular.module('XY').factory('CstmrModel', ['EventService', '$log', 'PageService', 'CstmrClass', 'CstmrService', 'QueryService', 'ClassService', 'MiscService',
    function(EventService, $log, PageService, CstmrClass, CstmrService, QueryService, ClassService, MiscService) {


        var model = {};
        // 正在新建的往来单位
        model.cstmrInfo = undefined;
        // 正在编辑flag
        model.pageState = {
            isEdit: false, // 是否是编辑(相对新建而言)
        }

        // 新建往来单位
        EventService.on(EventService.ev.START_CREATE_COMPANY, function() {
            model.cstmrInfo = CstmrClass.newCompany();
            model.pageState.isEdit = false;
            model.dialogId = PageService.showDialog('CreateCstmr');
        })

        // 编辑往来单位联系人
        EventService.on(EventService.ev.COMPANY_EDIT_CONTACT, function(event, arg) {
            model.cid = arg;
            CstmrService.company.get(model.cid, function(cstmrObj) {
                $log.error('cstmrObj',cstmrObj)
                model.cstmrInfo = cstmrObj;
                model.cstmrInfo.init_payable = Number(model.cstmrInfo.init_payable)
                for (var v of model.cstmrInfo.contact) {
                    for (var v2 of v.phonenum) {
                        v2.mobile = Number(v2.mobile)
                    }
                }

                model.pageState.isEdit = true;
                model.dialogId = PageService.showDialog('CreateCstmr');
            });
        })

        EventService.on(EventService.ev.ORDER_ARGS,function(evnet,args) {
            model.orderArgs = args;
        })

        // EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,item.cid);
        return model;

    }
]);
