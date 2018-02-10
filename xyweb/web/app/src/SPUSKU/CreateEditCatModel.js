//xxxxModel or Service or Class
'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('CreateEditCatModel', ['EventService', '$log', 'PageService', 'SPUClass','LockService','StockService',
    function(EventService, $log, PageService, SPUClass, LockService,StockService) {

        var model = {};
        // 正在创建的cat对象
        model.catInfo = {};

        EventService.on(EventService.ev.START_CREATE_EDIT_CAT,function(event,arg){
            LockService.getLockShopStatus(0, function () {
                if (arg) {
                    model.catInfo = SPUClass.newCat(arg);
                    // model.catInfo = StockService.getCatList();
                    model.dialogId = PageService.showDialog('CreateEditCat');
                } else {
                    model.catInfo = SPUClass.newCat();
                    // model.catInfo = StockService.getCatList();
                    $log.log('model',model.catInfo);
                    model.dialogId = PageService.showDialog('CreateEditCat');
                }
            });
        });

        EventService.on(EventService.ev.CREATE_CAT_SUCCESS,function(){
            PageService.showSharedToast("创建类别成功");
            setTimeout(function() {
                PageService.closeDialog();
                PageService.setNgViewPage('ManageGoodAndCat');
            }, 10);
            
        })

        EventService.on(EventService.ev.CREATE_CAT_ERROR,function(){
            PageService.showSharedToast("创建类别失败");
        })

        EventService.on(EventService.ev.EDIT_CAT_SUCCESS,function(){
            PageService.showSharedToast("编辑类别成功");
            setTimeout(function() {
                PageService.closeDialog();
            }, 10);
        })

        EventService.on(EventService.ev.EDIT_CAT_ERROR,function(){
            PageService.showSharedToast("编辑类别失败");
        })


        return model;

    }
]);