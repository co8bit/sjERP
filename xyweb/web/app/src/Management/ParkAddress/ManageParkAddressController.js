'use strict'

angular.module('XY').controller('ManageParkAddressController', ['EventService', '$scope', '$log', 'PageService', 'CstmrService', 'ManageParkAddressModel','MiscService',
    function(EventService, $scope, $log, PageService, CstmrService, model,MiscService) {

        CstmrService.parkAddress.queryList();
        $scope.pageNow = 1;
        $scope.PageService = PageService
        $scope.EventService = EventService
        $scope.parkList = model.parkListShow
        $scope.pageList = model.localTable.getPageList();
        $scope.isMobile = MiscService.testMobile();
        // 快速搜索输入内容
        $scope.queryInfo = {
            query: '',
        }

        $scope.$watch('queryInfo.query', function() {
            model.localTable.changeFilterCondition(0, $scope.queryInfo.query)
        }, true)

        $scope.selectPage = function(pageItem) {
            $scope.pageNow = pageItem;
            model.localTable.changeNowPage(pageItem);
        }

        // 点击新建按钮
        $scope.clickNew = function() {
            EventService.emit(EventService.ev.START_CREATE_EDIT_PARK_ADDRESS);
        }

        // 点击编辑按钮
        $scope.clickEdit = function(item) {
            EventService.emit(EventService.ev.START_CREATE_EDIT_PARK_ADDRESS, item)
        }

        $scope.confirmDelete = function(item) {
            item.deleted = false;
            PageService.showConfirmDialog('确定删除吗?', [], function () {
                CstmrService.parkAddress.delete(item);
                CstmrService.parkAddress.queryList();
                item.deleted = true;

            });
        }

        var refreshDataHandle = EventService.on(EventService.ev.PARK_ADDRESS_LOAD_SUCCESS, function () {
            model.localTable.calc();
            model.localTable.changeNowPage($scope.pageNow);
        });

        var requeryDataHandle = EventService.on(EventService.ev.PARK_ADDRESS_CREATE_SUCCESS, function () {
            CstmrService.parkAddress.queryList();
            var pageLast = model.localTable.getLastPage();
            setTimeout(function() {
                model.localTable.changeNowPage($scope.pageList[pageLast]);
            }, 200);//延时使页面跳转在表格更新后执行。
            
            // $log.log('lastPage',model.localTable.getLastPage());
        });

        var requeryDataHandle = EventService.on(EventService.ev.PARK_ADDRESS_EDIT_SUCCESS, function () {
            var nowPage;
            angular.copy($scope.pageNow,nowPage)
            CstmrService.parkAddress.queryList();
            // setTimeout(function() {
            //     model.localTable.changeNowPage(nowPage);
            // }, 200);//延时使页面跳转在表格更新后执行。
            // $log.log('lastPage',model.localTable.getLastPage());
        });

        $scope.$on('$destroy', function () {
            refreshDataHandle();
            requeryDataHandle();
        });
    }
])
