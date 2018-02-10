/*
 选货组件的Controller
 依赖父Scope内容: spuInfo 商品信息
 */
'use strict';

xy.controller('GoodListController', ['EventService', '$scope', '$log', 'PageService', 'StockService', '$filter', '$timeout','NetworkService',
    function (EventService, $scope, $log, PageService, StockService, $filter, $timeout, NetworkService) {

        $scope.catInfo = StockService.get_cats();
        $scope.skuInfo = StockService.get_skus_info();
        $scope.skuOrderInfo = {};
        
        // 计算并设置商品列表的宽度,使之与上面的搜索栏宽度一致
        $scope.setGoodListWH = function () {
            $('#good-list').height($(window).innerHeight() - $('.search-and-cat').height() - 135);

            var width = $('.search-and-cat').width();
            var num = Math.round(width * 0.01);
            $scope.len = width / num - 2;
            $log.log('good-unit',$scope.len);
            $('.good-unit').width($scope.len);
            $('.good-unit').height($scope.len);
            $log.log('create unit');
            $('.already-add').css({marginTop:-$scope.len});
        }
        //窗口改变时执行
        window.onresize = $scope.setGoodListWH;

        // 注入我们的“二阶段过滤器”
        var twoPhaseFilter = $filter('twoPhaseFilter');

        //快速搜索字符串
        $scope.queryInfo = {
            query: "",
        }

        $scope.displayedOrderInfo = [];
        $scope.selectedCatId = 0;

        // 筛选条件
        $scope.predicate = {
            oneMatch: {},
            allMatch: {},
        };

        //设定分类目录的ID
        $scope.setCatTo = function (cat_id) {
            $scope.predicate.allMatch.cat_id = cat_id;
            $scope.selectedCatId = cat_id;
        }

        $scope.$watch('queryInfo.query', function (newValue, oldValue) {

            $scope.predicate.oneMatch.spu_name = newValue;
            $scope.predicate.oneMatch.cat_name = newValue;
            $scope.predicate.oneMatch.spec_name = newValue;
            $scope.predicate.oneMatch.qcode = newValue;

        }, true)

        // 检测筛选条件
        $scope.$watch('predicate', function () {
            // $log.log('$scope.model.skuInfo',$scope.model.skuInfo);
            // $scope.displayedOrderInfo = twoPhaseFilter($scope.model.skuInfo, $scope.predicate);
            // $scope.skuOrderInfo = $scope.model.skuInfo;
            for( var key in $scope.skuOrderInfo){
                $scope.skuOrderInfo[key].isShow = false
                $scope.skuOrderInfo[key].isShowAgain = false;
            }
            $scope.setSkuShow($scope.predicate.oneMatch);
            $scope.setSkuShow($scope.predicate.allMatch);

        }, true);

        // 监测sku源数据
        $scope.$watch('model.skuInfo', function () {
            $scope.skuOrderInfo = $scope.model.skuInfo;
            for( var key in $scope.skuOrderInfo){
                $scope.skuOrderInfo[key].stock = Number($scope.skuOrderInfo[key].stock).toFixed(2); 
                $scope.skuOrderInfo[key].isShow = false
                $scope.skuOrderInfo[key].isShowAgain = false;
            }
            $scope.setSkuShow($scope.predicate.oneMatch);
            $scope.setSkuShow($scope.predicate.allMatch);
        }, true)

        $scope.setSkuShow = function (item){//检查是否通过本次过滤
            var input = $scope.skuOrderInfo;
            var flag;
            var tested;
            var str;
            var index;
            var predicate = item;


            for (var key in predicate) {
                predicate[key] = predicate[key].toString();
            }

            for (var i = 0 ; i < input.length ; i++) {

                flag = false;
                tested = false;

                for (var key in predicate) {
                    tested = true;
                    if (input[i][key] != undefined) {
                        str = input[i][key].toString();
                        index = str.indexOf(predicate[key]);
                        if (index >= 0) {
                            flag = true;
                            break;
                        }
                    }
                }
                if (flag || !tested) {
                    if($scope.skuOrderInfo[i].isShow){
                        $scope.skuOrderInfo[i].isShowAgain = true;
                    }
                    $scope.skuOrderInfo[i].isShow = true;
                }
            }
        }

        //设定购物车的item
        $scope.setCartItem = function (item) {
            $scope.editingItem = item;
            $scope.priceInput.isShow = true;

            $scope.cartAgent.setEditingCartItemBySku(item, 0); // 第二参数带0,则点击商品弹出的窗口中单价项为空

             // $log.log('item wjw',item);
             // 本Controller不需要注入SalesOrderModel也可以使用model里面的值，因为父Controller已经注入了Model
             // $log.log('SalesOrderModel.selectedCompanyCid',$scope.model.selectedCompanyCid);
            //  NetworkService.request('getCustomerLastPrice',{cid:$scope.model.selectedCompanyCid,},function(data){
            //     $log.log('data wjw',data);
            // });
        }

        $scope.editCartItem = function (item) {
            $scope.editingItem = item;
            $scope.priceInput.isShow = true;

            $scope.cartAgent.setEditingCartItemBySku(item, 0); // 第二参数带0,则点击商品弹出的窗口中单价项为空
            $log.log('data lsj',$scope.cartAgent.cartItemList[item.sku_id]);
            $scope.cartAgent.setEditingCartItemPriceAndQuantity($scope.cartAgent.cartItemList[item.sku_id]);
        }

        var closePriceInputHandle = EventService.on(EventService.ev.CLOSE_PRICE_INPUT, function () {
            if ($scope.editingItem) {
                $scope.cartAgent.setEditingCartItemByCartItem($scope.cartAgent.cartItemList[$scope.editingItem.sku_id]);
            }
        });

        $scope.$on('$destroy', function () {
            closePriceInputHandle();
        })


        $scope.quitGoodListTotal = function(){
            EventService.emit(EventService.ev.CLOSE_GOOD_LIST_TOTAL);

        }
    }
]);
