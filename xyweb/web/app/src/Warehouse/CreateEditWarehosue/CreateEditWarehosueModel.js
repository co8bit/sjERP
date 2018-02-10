'use strict';

xy.factory('CreateEditWarehosueModel', ['EventService', '$log', 'PageService', 'NetworkService', 'STOClass',
    function (EventService, $log, PageService, NetworkService, STOClass) {
        var model = {};
        model.stoInfo = {
            operate: {},//操作方法
            sto: {}//仓库数据
        };
        // 正在创建的warehouse对象
        model.stoInfo.operate = STOClass.newOperateSto();
        EventService.on(EventService.ev.CREATE_EDIT_WAREHOUSE, function (event, arg) {
            if (arg == 1) {
                model.enterWay = "createSPU";
            }
            if (arg == 2) {
                model.enterWay = "createSaleOrder";
            }
            if (arg && arg != 1&&arg != 2) {
                model.isCreate = false;
                angular.copy(arg, model.stoInfo.sto)
            } else {
                model.isCreate = true;
            }
            model.dialogId = PageService.showDialog('CreateEditWarehouse');
        });
        return model;
    }
]);