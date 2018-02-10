'use strict'

xy.controller('WithdrawDetailController', [ '$scope', '$log', 'TransferAndWithdrawDetail', 'PageService', 'NetworkService',
	function($scope, $log, model, PageService, NetworkService) {
		$scope.orderInfo = model.orderInfo;
		var orderInfo = angular.copy($scope.orderInfo,orderInfo);
		$scope.isTransfer = $scope.orderInfo.class == 84 ? true : false;
		if($scope.isTransfer){
			$scope.data = $scope.orderInfo.cart.data;
		}
		$scope.clickHistory = function (){
			PageService.showHistoryDialog($scope.orderInfo.history)
		}
		$scope.saveRemark = function (){
			if(!angular.equals($scope.orderInfo,orderInfo)){
				var dataToSend = {
					fid: $scope.orderInfo.fid,
					remark: $scope.orderInfo.remark,
				}
				NetworkService.request('editFinanceOrder',dataToSend,function(data){
					PageService.showSharedToast('修改备注成功！');
				})
			}
		}
		$scope.quit = function (){
			PageService.closeDialog(model.Dialog)
		}
	}
])