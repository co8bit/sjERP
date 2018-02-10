//xxxxController
'use strict';

angular.module('XY').controller('StockTakingController', ['EventService', '$scope', '$log', 'StockTakingModel', 'StockService','PageService','LockService','UserService',
    function(EventService, $scope, $log, model, StockService, PageService, LockService,UserService) {

    	$scope.model = model;
		$scope.cartAgent = model.orderInfo.cartAgent;
		$scope.skuList2 = StockService.getSkuList2();
		$scope.priceInput = {};
		$scope.priceInput.isShow = false;
		$scope.orderInfo = model.orderInfo;
		var orderInfo = angular.copy($scope.orderInfo, orderInfo);
		$scope.rpg = UserService.getLoginStatus().rpg;


    	// 控制显示的选项卡
    	$scope.pageState = 0;


    	$scope.clickLastStep = function() {
    		if ($scope.pageState > 0) {
    			$scope.pageState--;
    		}
    	};
    	$scope.clickNextStep = function() {
    		if ($scope.pageState < 1) {
    			$scope.pageState++;
    		}
    	};
		$scope.updateTotProfitAndLossValueAndQuantity = function() {
			var totValue = 0;
			var totQuantity = 0;
			for (var key in $scope.cartAgent.cartItemList) {
				// $log.log("$scope.cartAgent.cartItemList---",$scope.cartAgent.cartItemList);
				var quantity = isNaN($scope.cartAgent.cartItemList[key].quantity) ?　Number($scope.cartAgent.cartItemList[key].quantity) : $scope.cartAgent.cartItemList[key].quantity;
				var stock = isNaN($scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id].stock) ?　Number($scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id].stock) : $scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id].stock;
				var unit_price = isNaN($scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id].unit_price) ?　Number($scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id].unit_price) : $scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id].unit_price;
				// $log.debug("$scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id]---", $scope.skuList2[$scope.cartAgent.cartItemList[key].sku_id]);

				totQuantity += quantity  - stock;
				totValue += (quantity - stock) * unit_price;
			}
			$scope.totProfitAndLossValue = totValue.toFixed(2);
			$scope.totProfitAndLossQuantity = totQuantity;
		}

		$scope.$watch('cartAgent.cartItemList', function () {
			$scope.updateTotProfitAndLossValueAndQuantity();
		}, true);

		$scope.quit = function() {
			if (angular.equals(orderInfo, $scope.orderInfo ) || UserService.getLoginStatus().rpg == 3) {
				PageService.closeDialog();
				LockService.unlockShop();
			}else{
				PageService.showConfirmDialog('确定退出吗？',['直接退出','保存为草稿单据并退出','取消'],function () {
					PageService.closeDialog();
					LockService.unlockShop();
				},function () {
					$log.log("model.orderInfo:",model.orderInfo);
					StockService.createStockTakingDraft(model.orderInfo);
				});
			}
		}

		var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
			if (domEvent.keyCode == 27 && model.dialogId == PageService.dialogList.length) { // esc键
				$log.log('domEvent.keyCode:' + domEvent.keyCode);
				$log.log('PageService.isConfirmDialogShow:' + PageService.isConfirmDialogShow);
				$log.log('$scope.priceInput.isShow:' + $scope.priceInput.isShow);
				if (!PageService.isConfirmDialogShow && !$scope.priceInput.isShow) {
					$log.log('domEvent.keyCode::quit:' + domEvent.keyCode);
					$scope.$apply(function () {
						$scope.quit();
					})
				} else if ($scope.priceInput.isShow) {
					$scope.priceInput.isShow = false;
					EventService.emit(EventService.ev.CLOSE_PRICE_INPUT);
				}
			} else if (domEvent.keyCode == 13) { // 回车
				if ($scope.priceInput.isShow) {
					EventService.emit(EventService.ev.CLICK_FINISH);
				}
			}
		})

		$scope.$on('$destroy', function () {
			keydownHandle();
		})
    }
]);
