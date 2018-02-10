'use strict';

xy.controller('RequisitionPriceInputController', ['$rootScope', '$scope', 'NetworkService', '$log', 'StockService', 'NumPadClassService', 'MiscService', 'PageService', 'EventService', 'UserService', '$timeout',
	function($rootScope, $scope, NetworkService, $log, StockService, NumPadClassService, MiscService, PageService, EventService, UserService, $timeout) {
		//$log.debug('AddAeditingCartItem cartAgent: ',$scope.cartAgent)
		$scope.isMobile = MiscService.testMobile();
		//小键盘打开默认第一个输入框获取焦点
		$(function(){
			$('#first-input').focus();
		});
		$scope.orderInfo.cartAgent.editingCartItem.Oldprice =$scope.orderInfo.cartAgent.editingCartItem.unit_price
		//监控输入框中的数量，更新数量
		$scope.$watch('orderInfo.cartAgent.editingCartItem.quantity', function(newValue, OldValue) {
			if(typeof newValue == 'string') {
				$scope.orderInfo.cartAgent.editingCartItem.quantity = newValue.replace(/[^0-9 | \.]+/, '');
			}
			if($scope.orderInfo.cartAgent.editingCartItem.unit_price){
				 $scope.profitAndLossValue = Number($scope.orderInfo.cartAgent.editingCartItem.quantity) * Number($scope.orderInfo.cartAgent.editingCartItem.unit_price)
			}
			if(($scope.class == 1) || ($scope.class == 4)) {
				// var stock = parseInt($scope.cartAgent.editingCartItem.stock);
				var stock = $scope.orderInfo.cartAgent.editingCartItem.stock;
				if($scope.orderInfo.cartAgent.editingCartItem.quantity > stock) {
					$scope.orderInfo.cartAgent.editingCartItem.quantity = stock;
				} else if($scope.orderInfo.cartAgent.editingCartItem.quantity == stock) {
					$scope.orderInfo.cartAgent.editingCartItem.quantity = $scope.cartAgent.editingCartItem.quantity.toString();
				}
			}
			if($scope.orderInfo.cartAgent) {
				$scope.orderInfo.cartAgent.updateEditingItemPrice();
			}

		});
		//监控输入框中的单价，更新金额

		$scope.$watch('orderInfo.cartAgent.editingCartItem.unit_price', function(newValue, OldValue) {
			if(typeof newValue == 'string') {
				$scope.orderInfo.cartAgent.editingCartItem.unit_price = newValue.replace(/[^0-9 | \.]+/, '');
			}
            if($scope.orderInfo.cartAgent.editingCartItem.quantity){
                $scope.profitAndLossValue = Number($scope.orderInfo.cartAgent.editingCartItem.quantity) * Number($scope.orderInfo.cartAgent.editingCartItem.unit_price)
            }
			if($scope.orderInfo.cartAgent) {
				$scope.orderInfo.cartAgent.updateEditingItemPrice();
			}
		});

		// 监控输入框中商品的sku_id，如果变化,说明开始处理新SKU，广播事件通知小键盘
		$scope.$watch('orderInfo.cartAgent.editingCartItem.sku_id', function() {
			if($scope.orderInfo.cartAgent.editingCartItem.sku_id == -1)
				return;
			// $log.log('should broadcast')
			// $log.log('orderInfo',$scope.orderInfo);
			$log.log('SalesOrderModel.selectedCompanyCid wjw', $scope.model.selectedCompanyCid);
			$scope.thisCompanySkuLastPrice = 0;

			//在这里请求Cid对应的上一次售价
			NetworkService.request('getCustomerLastPrice', {
				cid: $scope.model.selectedCompanyCid,
				sku_id: $scope.orderInfo.cartAgent.editingCartItem.sku_id
			}, function(data) {
				$log.log('data', data);
				if(!data.data.price1)
					data.data.price1 = 0;
				$scope.thisCompanySkuLastPrice = data.data.price1;
			});

			// if ( ($scope.cartAgent.editingCartItem.unit_price == '') && ($scope.cartAgent.editingCartItem.last_selling_price != 0) )
			//     $scope.cartAgent.editingCartItem.unit_price = ''+$scope.cartAgent.editingCartItem.last_selling_price;

			$scope.$broadcast('EDITING_GOOD_CHANGED')
		})

		$scope.selectCompanySkuLastPrice = function() {
			$scope.orderInfo.cartAgent.editingCartItem.unit_price = '' + $scope.thisCompanySkuLastPrice;
		}

		$scope.selectSkuRecentPrice = function() {
			$scope.orderInfo.cartAgent.editingCartItem.unit_price = '' + $scope.orderInfo.cartAgent.editingCartItem.last_selling_price;
		}

		$scope.$on('EDITING_GOOD_CHANGED', function() {
			$log.log('EDITING_GOOD_CHANGED');
			$scope.numPad.setState(0);
			$scope.numPad.setButtonStr('Finish', '下一步');
		})

		function pressNumCallback(state, num) {
			switch(state) {
				case 0:
					 $scope.orderInfo.cartAgent.editingCartItem.quantity = $scope.numPad.joinTwoNum($scope.orderInfo.cartAgent.editingCartItem.quantity, num);
					break;
				case 1:
					$scope.orderInfo.cartAgent.editingCartItem.unit_price = $scope.numPad.joinTwoNum($scope.orderInfo.cartAgent.editingCartItem.unit_price, num);
					break;
				default:
					;
			}
		}

		function pressOtherCallback(state, input) {
			switch(input) {
				case 'Clear':
					switch(state) {
						case 0:
							$scope.orderInfo.cartAgent.editingCartItem.quantity = '';
							break;
						case 1:
							$scope.orderInfo.cartAgent.editingCartItem.unit_price = '';
							break;
					}
					break;
				case 'BackSpace':
					switch(state) {
						case 0:
							$scope.orderInfo.cartAgent.editingCartItem.quantity = ($scope.orderInfo.cartAgent.editingCartItem.quantity).toString().substring(0, $scope.orderInfo.cartAgent.editingCartItem.quantity.length - 1);
							break;
						case 1:
							$scope.orderInfo.cartAgent.editingCartItem.unit_price = $scope.orderInfo.cartAgent.editingCartItem.unit_price.substring(0, $scope.orderInfo.cartAgent.editingCartItem.unit_price.length - 1);
							break;
					}
					break;
				case 'Finish':
					if(state == 0) {
						$scope.numPad.state += 1;
						$scope.numPad.setButtonStr('Finish', '完成');
					} else if(state == 1) {
						$scope.finishEditing();
						$scope.numPad.state == 0;
					}
					break;
				default:
					;
			}
		}
		/**
		 * 将商品加入购物车
		 */
		$scope.finishEditing = function() {
			//if (MiscService.testValue($scope.cartAgent.editingCartItem.unit_price)) {
			$scope.orderInfo.cartAgent.addToCart($scope.orderInfo.cartAgent.editingCartItem, false);
			$scope.priceInput.isShow = false;
			// } else {
			//     PageService.showConfirmDialog('单价输入有误!');
			// }
		}

		// 这个页面上的小键盘状态 :
		// state == 0 , 修改数量
		// 1 , 修改单价
		$scope.numPad = NumPadClassService.newNumPad(pressNumCallback, pressOtherCallback);
		$scope.numPad.state = 0;
		$scope.numPad.setButtonStr('Finish', '下一步');
		$scope.cancelInput = function() {
			$scope.priceInput.isShow = false;
			EventService.emit(EventService.ev.CLOSE_PRICE_INPUT);
		}

		$scope.$watch('priceInput.isShow', function() {
			$timeout(function() {
				$('#first-input').focus();
			});
		});

		$scope.$watch('numPad.state', function() {
			if($scope.numPad.state == 0) {
				$scope.numPad.setButtonStr('Finish', '下一步');
				$('#first-input').focus();
			} else if($scope.numPad.state == 1) {
				$scope.numPad.setButtonStr('Finish', '完成');
				$('#second-input').focus();
			}
		});

		var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function(event, domEvent) {
			if(domEvent.keyCode == 9) {
				++$scope.numPad.state;
			}
		});

		var finishHandle = EventService.on(EventService.ev.CLICK_FINISH, function() {
			$scope.finishEditing();
		});

		$scope.$on('$destroy', function() {
			finishHandle();
			keydownHandle();
		})
		$scope.priceIsShow = true;
		//退货单和采购单不显示售价
		if(Cookies.get('salesOrderType') != undefined) {
			if(Cookies.get('salesOrderType') == '3' || Cookies.get('salesOrderType') == '4') {
				$scope.priceIsShow = false;
			} else {
				$scope.priceIsShow = true;
			}
		}
	}
]);