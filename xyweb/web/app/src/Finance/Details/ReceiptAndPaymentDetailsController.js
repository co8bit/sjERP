//xxxxController
'use strict'

angular.module('XY').controller('ReceiptAndPaymentDetailsController', ['EventService', '$scope', '$log','PageService','FinancialDocumentDetailsModel','FinanceService','UserService','NetworkService',
    function(EventService, $scope, $log,PageService, model, FinanceService,UserService,NetworkService) {
    	$scope.PageService = PageService
    	$scope.model = model
    	$scope.docInfo = model.docInfo
    	$scope.docInfo.orderList = model.docInfo.orderList;
    	var docInfo = angular.copy($scope.docInfo,docInfo);
    //	$log.error(model.docInfo);
    	$scope.rpg = UserService.getLoginStatus().rpg;

        $scope.isShowMore = false
    	// 操作日志默认不显示
    	$scope.isHistoryShow = false
    	// 默认不在编辑状态
    	$scope.isEditing = false

    //	$log.error('model.docInfo',model.docInfo);
    //	$log.error('model.docInfo.orderlist',model.docInfo.orderList);
        $scope.valueOfGoods = 0;
        $scope.toPay = 0;
    	for(var k of docInfo.orderList){
    		$scope.valueOfGoods += Number(k.value);
    		$scope.toPay += Number(k.remain_opposite);
    	}

    	$scope.clickFinishEditing = function() {
    		FinanceService.editDocument($scope.docInfo)
    	}

		switch ($scope.docInfo._class) {
			case 71:
				$scope.class = '收款单';
				$scope.cstmr = '收款对象';
				break;
			case 72:
				$scope.class = '付款单';
				$scope.cstmr = '付款对象';
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
			keydownHandle()
		})

		// 操作日志的隐藏和显示
    	$scope.clickHistory = function() {
    		PageService.showHistoryDialog($scope.docInfo.history)
    	}
    	//备注失去焦点后保存、发送
    	$scope.clickModify = function() {
            //发送编辑参数
          var dataToSend={}
            if(!angular.equals($scope.docInfo,docInfo)){
            	dataToSend.fid=$scope.docInfo.fid,
            	dataToSend.remark =$scope.docInfo.remark
            	NetworkService.request("editFinanceOrder",dataToSend,function(){
            		
            	}) 
            } 
            PageService.showSharedToast("编辑成功"); 
    	}
    	
    }
])

