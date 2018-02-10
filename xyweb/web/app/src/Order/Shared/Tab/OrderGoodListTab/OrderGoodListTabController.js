// 货物列表标签页的controller
'use strict';

xy.controller('OrderGoodListTabController', ['EventService','$scope', '$log', 'SalesOrderModel',
    function(EventService,$scope, $log, model) {

        $scope.orderInfo = model.orderInfo;
    	$scope.query = {
    		query:'',
    	}

    	switch ($scope.orderInfo._class){
    		case 1:
    			$scope.quantityType = '销售数量';
    			break;
    		case 2:
    			$scope.quantityType = '退货数量';
    			break;
    		case 3:
    			$scope.quantityType = '采购数量';
    			break;
    		case 4:
    			$scope.quantityType = '退货数量';
    			break;
    	}

    	// 显示cartItemlist
    	$scope.test = function() {
    		$log.debug($scope.cartAgent.cartItemList)
    	}

		$scope.setCartItem = function (item) {
			$scope.priceInput.isShow = true;
			$scope.cartAgent.setEditingCartItemBySku(item);
            $scope.cartAgent.setEditingCartItemPriceAndQuantity($scope.cartAgent.cartItemList[item.sku_id]);
            EventService.emit(EventService.ev.SHOW_GOOD_LIST_TOTAL);
		}

        $scope.editCartItem = function (item) {
            $scope.priceInput.isShow = true;
            $scope.cartAgent.setEditingCartItemBySku(item);
            // $scope.cartAgent.setEditingCartItemBySku(item);
            EventService.emit(EventService.ev.SHOW_GOOD_LIST_TOTAL);
        }

        $scope.arr = Object.keys($scope.cartAgent.cartItemList).length;
        $scope.addPickGoodTab = function(){
            EventService.emit(EventService.ev.SHOW_GOOD_LIST_TOTAL);
            // $log.log('$scope.cartAgent.cartItemList wjw',$scope.cartAgent.cartItemList);
            //刷新数组
            $scope.arr = Object.keys($scope.cartAgent.cartItemList).length;
            $log.log('$scope.arr wjw',$scope.arr);
        }

    }
]);