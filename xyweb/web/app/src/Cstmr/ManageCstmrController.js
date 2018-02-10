'use strict';

xy.controller('ManageCstmrController', ['EventService', '$scope', '$log', 'CstmrService', 'PageService', '$filter', 'ManageCstmrModel','MiscService',
    function(EventService, $scope, $log, CstmrService, PageService, $filter, model,MiscService) {

        CstmrService.company.queryList(1);
        $scope.companyList = model.companyListShow;
        $scope.pageList = model.localTable.getPageList();
        $scope.isMobile = MiscService.testMobile();
        $scope.queryInfo = {
            query: '',
        }

        if (model.query) {
            $scope.queryInfo.query = model.query;
        }

        $scope.$watch('queryInfo.query', function() {
            model.localTable.changeFilterCondition(0, $scope.queryInfo.query);
        }, true);
        var now_Page = 1 ;
        // 点击了页码
        $scope.selectPage = function(pageItem) {
            model.localTable.changeNowPage(pageItem);
            now_Page = model.localTable.getNowPage();
            // $log.log('model.now_page',model.now_page);
            $log.debug('in selectPage, pageItem = ',pageItem);
        }

        // 当信息源变化时自动刷新
        var refreshDataHandle = EventService.on(EventService.ev.COMPANY_LIST_LOAD_SUCCESS, function() {
            model.localTable.calc();
        });
        $scope.now_page = model.now_page;
        var item = {
            action:0,
            content:$scope.now_page,
        };

        if(item.content>0) {
         setTimeout(function() {
            $scope.$apply(function () {
                model.localTable.changeNowPage($scope.pageList[$scope.now_page]);
                now_Page = model.localTable.getNowPage();
                });
            }, 100);
        }
        $scope.$on('$destroy', function() {
            refreshDataHandle();
        });

        $scope.checkRecord = function (cid) {
            var args = {
                cid : cid,
                query : $scope.queryInfo.query,
                cstmrPage : 1,
                nowPage: now_Page,
            }
            // $log.log('args.now_page',args.nowPage);
            EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,args);
        }
    }
]);
