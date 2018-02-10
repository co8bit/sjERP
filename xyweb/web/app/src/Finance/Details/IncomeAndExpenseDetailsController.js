//IncomeAndExpenseDetailsController
'use strict'

angular.module('XY').controller('IncomeAndExpenseDetailsController', ['EventService', '$scope', '$log', 'FinancialDocumentDetailsModel','FinanceService','PageService','UserService',
    function(EventService, $scope, $log, model, FinanceService, PageService,UserService) {

    	$scope.model = model
    	$scope.docInfo = model.docInfo
        $scope.isShowMore = false
    	$scope.isEditing = false
    	$scope.isHistoryShow = false


    	$scope.clickFinishEditing = function() {
    		FinanceService.editDocument($scope.docInfo)
    	}

		switch ($scope.docInfo._class) {
			case 73:
				$scope.class = '其他收入单';
				break;
			case 74:
				$scope.class = '支出费用单';
				break;
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
				case 4:
					PageService.closeDialog();
					EventService.emit(EventService.ev.VIEW_BACK_TO_DRAFT,model.orderArgs.draftTab);
					// EventService.emit(EventService.ev.START_VIEW_DRAFT,model.orderArgs.draftTab);
					break;
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
