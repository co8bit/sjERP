//xxxxController
'use strict'

xy.controller('ManageGoodController', ['EventService', '$scope', '$log', 'StockService', 'PageService', 'CreateEditSPUModel', '$filter', 'LocalTableClass','ManageGoodModel','MiscService',
    function(EventService, $scope, $log, StockService, PageService, CreateSPUModel, $filter, LocalTableClass,model,MiscService) {

        // $log.debug('ManageGoodController')

        //刷新库存


        $scope.catInfo = StockService.get_cats();
        $scope.spuList = model.spuList;
        $scope.filterInfo = model.localTable.getFilter();
        $scope.pageList = model.localTable.getPageList();
        $scope.isMobile = MiscService.testMobile();

        //选中的类别
        $scope.checkCatName = '';

        // 快速搜索输入内容
        $scope.queryInfo = {
            query: '',
        };

        $scope.$watch('queryInfo.query',function() {
            model.localTable.changeFilterCondition(0,$scope.queryInfo.query);
        },true);

        // 显示出来的的列表
        $scope.selectCat = function(cat_id) {
            model.localTable.changeFilterCondition(1,cat_id);
        };

        // 点击了页码
        $scope.selectPage = function(pageItem) {
            model.localTable.changeNowPage(pageItem);
            $log.debug('in selectPage, pageItem = ',pageItem)
        };

        // 操作提示
        var deleteSkuHandle = EventService.on(EventService.ev.DELETE_SKU_SUCCESS, function() {
            PageService.showSharedToast('删除SKU成功！');
        });

        // 当信息源变化时自动刷新
        var refreshDataHandle = EventService.on(EventService.ev.querySKU,function(evnet,arg) {
            model.localTable.calc();
        });

        $scope.$on('$destroy', function() {
            deleteSkuHandle();
            refreshDataHandle();
        });

        // 初始化结束 ---------------------------------------------------------------------------------------------------------
        $scope.createSPU = function() {
            // 发出事件
            if($scope.checkCatName){
                EventService.emit(EventService.ev.START_CREATE_EDIT_SPU,{checkCatName : $scope.checkCatName})
            }else{
                EventService.emit(EventService.ev.START_CREATE_EDIT_SPU)
            }
        };

        $scope.editSPU = function(spu_id) {
            $log.log('editSPU spu_id = ', spu_id);
            //通知model准备好要编辑的spu
            EventService.emit(EventService.ev.START_CREATE_EDIT_SPU, spu_id)
        };

        $scope.deleteSKU = function(sku_id) {
            $log.log('deleteSKU');
            StockService.deleteSKU(sku_id)
        };

        //选中类别时记住类别
        $scope.checkCat = function (value) {
            $scope.checkCatName = value;
        }
    }
]);