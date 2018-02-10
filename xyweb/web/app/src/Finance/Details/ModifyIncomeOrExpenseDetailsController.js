//ModifyIncomeOrExpenseDetailsController
'use strict'

angular.module('XY').controller('ModifyIncomeOrExpenseDetailsController', ['EventService', '$scope', '$log', 'OrderDetailsModel','FinanceService','PageService','UserService','OrderService',
    function(EventService, $scope, $log, model, FinanceService, PageService,UserService,OrderService) {
    	$scope.class = model.class
    	$scope.model = model
    	$scope.docInfo = model.orderInfo
        $scope.isShowMore = false
    	$scope.isEditing = false
    	$scope.isHistoryShow = false


    	$scope.clickFinish = function() {
            model.orderInfo.status = 1
            OrderService.setOrderStatus($scope.docInfo, function () {
                model.refreshOrderInfo();
            })
        }

		$scope.quit = function() {
			PageService.closeDialog(model.dialogId);
			switch (model.orderArgs.orderPage) {
				case 1:
					PageService.closeDialog();
					EventService.emit(EventService.ev.VIEW_BACK_TO_ORDER);
					// EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
					break;
				case 2:
					PageService.closeDialog();
					EventService.emit(EventService.ev.VIEW_BACK_TO_ORDER);
					// EventService.emit(EventService.ev.START_VIEW_ALL_ORDER);
					break;
				case 3:
					PageService.closeDialog();
					EventService.emit(EventService.ev.VIEW_BACK_TO_COMPANY);
					// EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,model.orderArgs);
					break;
			}
		}

		$scope.clickModify = function() {
    		switch ($scope.isEditing) {
    			case false :
    				$scope.isEditing = true
    				break
    			case true : 
    				$scope.isEditing = false
                    model.compareAndEditOrder($scope.docInfo)
    				break
    			default:
    		}
    	}



		var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
			if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
				$scope.$apply(function () {
					$scope.quit();
				})
			}
		})

		$scope.$on('$destroy', function () {
			keydownHandle();
		})
    }
])
