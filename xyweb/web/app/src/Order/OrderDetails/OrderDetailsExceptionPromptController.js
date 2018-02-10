'use strict';

angular.module('XY').controller('OrderDetailsExceptionPromptController', ['EventService', '$scope', '$log', 'OrderDetailsModel', 'PageService','OrderService',
    function(EventService, $scope, $log, model, PageService,OrderService) {
    	$scope.model = model;
    	$scope.orderInfo = model.orderInfo;

    	// 确认标记异常
    	$scope.confitmMarkException = function() {

			model.orderInfo.status = 2;
			OrderService.setOrderStatus(model.orderInfo, function () {
				model.refreshOrderInfo();
			});
			PageService.closeDialog();

    	}
    }
]);