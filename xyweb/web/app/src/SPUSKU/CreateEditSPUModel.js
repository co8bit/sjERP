// 保存正在创建或编辑的SPU信息 服务CreateSPU.html(CreateSPUController),也服务EditSPU.html(EditSPUController)
'use strict';

xy.factory('CreateEditSPUModel', ['EventService', '$log', 'SPUClass', 'PageService','StockService','LockService', 'LocalTableClass','STOClass',
    function(EventService, $log, SPUClass, PageService,StockService, LockService, LocalTableClass,STOClass) {

        // $log.debug("xxxxService init")

        var model = {};
            model.cat_name = '';
        var _filter = [{
            fieldName: ['cat_name'],
            value: '*',
            mode: 1,
        }];

        model.catList = StockService.getCatList();
        // 本地表对象
        model.localTable = LocalTableClass.newLocalTable(model.catList, _filter, 0);
        model.catListShow = model.localTable.getFilteredData();
        EventService.on(EventService.ev.CREATESPU_UPDATE,function () {
            model.stoInfo.query.request().then(function () {
                model.stoInfo.allSto = model.stoInfo.query.getAllSto();
                model.stoInfo.setDefaultSto( model.stoInfo.allSto[0]);
        })});
        // 监听事件开始业务弹出功能对话框
        EventService.on(EventService.ev.START_CREATE_EDIT_SPU, function(event, arg) {
            LockService.getLockShopStatus(0, function () {
                model.stoInfo = STOClass.newST0();
                model.stoInfo.query = STOClass.newQuerySto();
                if (arg) { //如果有数据
                    if(arg.checkCatName){ //判断数据里面是否传进来的是类别
                        model.cat_name = arg.checkCatName;
                        model.isEdit = false; //不是编辑模式
                    }else{
                        model.spu_id = arg;
                        var spu_id = arg;
                        StockService.queryCat(2);
                        model.isEdit = true; //编辑模式
                    }
                }else{
                    model.isEdit = true;
                }
                    model.stoInfo.query.request().then(function () {
                    model.stoInfo.allSto = model.stoInfo.query.getAllSto();
                    model.stoInfo.setDefaultSto( model.stoInfo.allSto[0]);
                    if (spu_id) {
                        model.spuInfo = SPUClass.newSPU(spu_id);
                        model.stoInfo.STOCtor(model.spuInfo.skus);
                        model.spuInfo.setDefaultStoId(model.stoInfo.allSto[0].sto_id);
                        model.spuInfo.setDefaultStoIdALL();
                        model.dialogId = PageService.showDialog('CreateSPU');
                        // 没参数是新建
                    } else {
                        model.stoInfo.STOCtor();
                        // $log.log('CreateSPU Event');
                        model.spuInfo = SPUClass.newSPU();
                        model.spuInfo.setDefaultStoId(model.stoInfo.allSto[0].sto_id);
                        model.spuInfo.setDefaultStoIdALL();
                        model.dialogId = PageService.showDialog('CreateSPU');
                    }
                });
                // 有spu_id是edit
            });
        });

        //编辑SPU成功
        EventService.on(EventService.ev.EDIT_SPUSKU_SUCCESS, function() {
            PageService.closeDialog();
        });
        // 新建SPU成功
        EventService.on(EventService.ev.CREATE_SPUSKU_SUCCESS, function() {
            PageService.closeDialog();
        });

        EventService.on(EventService.ev.RESTORE_LOCK_SHOP_STATUS, function () {
           model.retoreLockShopStatus = true;
        });

        return model;

    }

]);
