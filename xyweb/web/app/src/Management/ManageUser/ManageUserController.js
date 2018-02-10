//ManageUserController
'use strict';

angular.module('XY').controller('ManageUserController', ['AuthGroupService','EventService', 'ManageUserModel', '$scope', '$log', 'UserService','$filter',
    function(AuthGroupService, EventService, model, $scope, $log, UserService,$filter) {

    	$scope.userList = model.userList;
        $scope.query = '';
        $scope.isYQJ = model.isYQJ;
        $scope.isYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);
		$scope.addSectorIsShow = false;
        // 筛选条件
        $scope.predicate = {
            oneMatch:{},
            allMatch:{},
        };
		$scope.quit = function (){
			$scope.addSectorIsShow = false;
		}
        // 筛选过后的列表
        $scope.displayedOrderInfo = [];

        $scope.$watch('query',function(newValue,oldValue){
            $scope.predicate.oneMatch.sn  = newValue;
            $scope.predicate.oneMatch.username  = newValue;
            $scope.predicate.oneMatch.name  = newValue;
        },true);

        var twoPhaseFilter = $filter('twoPhaseFilter');

        // 检测筛选条件
        $scope.$watch('predicate',function() {
            $scope.displayedOrderInfo = twoPhaseFilter($scope.userList,$scope.predicate);
        },true);

        // 监测data源数据
        $scope.$watch('userList',function() {
            $scope.displayedOrderInfo = twoPhaseFilter($scope.userList,$scope.predicate);
        },true);
    }
]);
