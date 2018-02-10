//xxxxController
'use strict';

angular.module('XY').controller('RequisitionGoodListController', ['EventService', '$scope', '$log','NumPadClassService', 'StockService','$filter', 'RequisitionModel','UserService',
    function(EventService, $scope, $log, NumPadClassService, StockService,$filter,model,UserService) {
 	// 获得类别列表
 	$scope.catList = StockService.getCatList();
 	$scope.skuList = StockService.getSkuStoList();
 	$scope.skuList2 = StockService.getSkuList2();
  	$scope.rpg = UserService.getLoginStatus().rpg;
 	// 筛选相关

     // 注入我们的“二阶段过滤器”
     var twoPhaseFilter = $filter('twoPhaseFilter');

     //快速搜索字符串
     $scope.queryInfo = {
         query: "",
     }

     $scope.displayedOrderInfo = [];
     $scope.selectedCatId = '';

     // 筛选条件
     $scope.predicate = {
         oneMatch:{},
         allMatch:{},
     };

     // 选择类别
     $scope.setCatTo = function(cat_id) {
     	$log.log('cat_id',cat_id);
         $scope.predicate.allMatch.cat_id = cat_id;
         $scope.selectedCatId = cat_id;
     }

     $scope.$watch('queryInfo.query',function(newValue,oldValue){

         $scope.predicate.oneMatch.spu_name  = newValue;
         $scope.predicate.oneMatch.cat_name  = newValue;
         $scope.predicate.oneMatch.spec_name = newValue;
         $scope.predicate.oneMatch.qcode = newValue;

     },true)

     // 检测筛选条件
     $scope.$watch('predicate',function() {
         $scope.displayedOrderInfo = twoPhaseFilter($scope.skuList,$scope.predicate);
     },true);

     // 监测sku源数据
     $scope.$watch('skuList',function() {
         $scope.displayedOrderInfo = twoPhaseFilter($scope.skuList,$scope.predicate);

     },true)

     $scope.setCartItem = function (item) {
         $scope.editingItem = item;
         $scope.priceInput.isShow = true;
         $scope.orderInfo.cartAgent.setEditingCartItemBySku(item);
     }

     // 计算并设置商品列表的宽度,使之与上面的搜索栏宽度一致
     $scope.setGoodListWH = function () {
         $('#good-list').height($(window).innerHeight() - $('.search-and-cat').height() - 135);

         var width = $('.search-and-cat').width();
         var num = Math.round(width / 100);
         $scope.len = width / num - 2;

         $('.good-unit').width($scope.len);
         $('.good-unit').height($scope.len);
         $('.already-add').css({marginTop:-$scope.len});
     }
     window.onresize = $scope.setGoodListWH;

     $scope.createSKU = function () {
         EventService.emit(EventService.ev.START_CREATE_EDIT_SPU);
         EventService.emit(EventService.ev.RESTORE_LOCK_SHOP_STATUS);
     }

     var closePriceInputHandle = EventService.on(EventService.ev.CLOSE_PRICE_INPUT, function () {
         if ($scope.editingItem) {
             $scope.orderInfo.cartAgent.setEditingCartItemByCartItem($scope.orderInfo.cartAgent.cartItemList[$scope.editingItem.sku_id]);
         }
     });

     $scope.$on('$destroy', function () {
         closePriceInputHandle();
     })

    }
]);