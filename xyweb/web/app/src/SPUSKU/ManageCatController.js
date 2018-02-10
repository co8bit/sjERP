'use strict'

angular.module('XY').controller('ManageCatController', ['EventService', '$scope', '$log', 'StockService', '$filter', 'PageService', 'CreateEditCatModel', 'ManageCatModel',
    function(EventService, $scope, $log, StockService, $filter, PageService, CreateEditCatModel, model) {

        // $log.debug('ManageCatController')

        // 自动加载
        StockService.queryCat(1)

        $scope.catListShow = model.skuListShow
        $scope.pageList = model.localTable.getPageList()

        // 快速搜索输入内容
        $scope.queryInfo = {
            query: '',
        }

        $scope.$watch('queryInfo.query', function() {
            model.localTable.changeFilterCondition(0, $scope.queryInfo.query)
        }, true);

        // 点击了页码
        $scope.selectPage = function(pageItem) {
            model.localTable.changeNowPage(pageItem)
            $log.debug('in selectPage, pageItem = ',pageItem)
        }

        // 当信息源变化时自动刷新
        var refreshDataHandle = EventService.on(EventService.ev.queryCat, function(evnet, arg) {
            model.localTable.calc()
        })

        //新建类别
        $scope.clickCreateCat = function() {
            EventService.emit(EventService.ev.START_CREATE_EDIT_CAT)
        }

        //编辑类别
        $scope.clickEditCat = function(cat) {
            EventService.emit(EventService.ev.START_CREATE_EDIT_CAT, cat)
        }

        //删除类别
        $scope.deleteCat = function(cat) {
            StockService.deleteCat(cat)
        }

        //监听删除类别成功事件
        var deleteCatHandle = EventService.on(EventService.ev.DELETE_CAT_SUCCESS, function(event, arg) {
            PageService.showSharedToast('删除类别成功')
        })

        // 离开页面销毁监听
        $scope.$on('$destroy', function() {
            refreshDataHandle();
            deleteCatHandle();
        })
    }
])
