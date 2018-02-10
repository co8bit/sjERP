'use strict'

angular.module('XY').controller('WarehouseController',['$scope','WarehouseModel','$log','EventService','UserService',
	function ($scope,model,$log,EventService,UserService) {
	$scope.rpg = UserService.getLoginStatus().rpg
    $scope.stoInfo= model.stoInfo;
    $scope.allStoValue = 0;
	    $scope.statusName={
        "0":"停用",
        "1": "正常"
         };
	    for(var key in $scope.stoInfo.allSto){
	    	if($scope.stoInfo.allSto[key].totalValue){
                $scope.allStoValue+=$scope.stoInfo.allSto[key].totalValue
			}
		}

		$scope.onpenViewStock = function (sto_id) {
			EventService.emit(EventService.ev.START_VIEW_STOCK,sto_id);
		};
		$scope.CreateWarehouse=function(){
			EventService.emit(EventService.ev.CREATE_EDIT_WAREHOUSE);
		};
		$scope.EditWarehouse=function(item){
			EventService.emit(EventService.ev.CREATE_EDIT_WAREHOUSE,item);
		}
	}
])
