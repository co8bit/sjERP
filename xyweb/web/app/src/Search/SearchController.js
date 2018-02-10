//xxxxController
'use strict';

angular.module('XY').controller('SearchController', ['EventService', '$scope', '$log','PageService','SearchModel','MiscService',
    function(EventService, $scope, $log,PageService,model,MiscService) {
        $scope.PageService = PageService;
        $scope.EventService = EventService;
        $scope.MiscService = MiscService

        $scope.isMobile = MiscService.testMobile();
        $scope.model = model;
        $scope.query = model.query;
        $scope.data = model.query.getData();
        $scope.paginator = model.query.getPaginator();

        $scope.pageState = model.pageState;

        $scope.searchContent = model.query.getSearchContent();

        $scope.clickTab = function(num) {
        	$scope.query.setType(num);
        	$scope.query.request();
        	$scope.pageState.activeTab = num;
        }

        $scope.testButton = function() {
            $log.log();
        }

        $scope.checkRecord = function (cid) {
            var args = {
                cid : cid,
                cstmrPage : 2,
            }
            EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,args);
        }

        $scope.viewDetails = function(item) {
            MiscService.sendViewDetailsEvent(item);

            EventService.emit(EventService.ev.ORDER_ARGS, {})
        }

    }
]);

