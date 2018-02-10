//xxxxController
'use strict';

angular.module('XY').controller('StockTakingGoodListController', ['EventService', '$scope', '$log', 'NumPadClassService', 'StockService', '$filter', 'StockTakingModel', 'UserService', 'PageService',
    function (EventService, $scope, $log, NumPadClassService, StockService, $filter, model, UserService, PageService) {
        // 获得类别列表
        $scope.catList = model.catInfo;
        $scope.skuList = model.skuInfo ? model.skuInfo : [];
        $scope.stoList = model.stoList;
        $scope.defaultSto = model.stoList[0];
        $scope.userList = model.userList;
        $scope.skuList2 = StockService.getSkuList2();
        $scope.skuOrderInfo = {};
        $scope.rpg = UserService.getLoginStatus().rpg;
        $scope.orderInfo = model.orderInfo;
        if (model.orderInfo.sto_id) {
            $scope.isPromptSelectShow = false;
            $scope.stoListShow = false;
        } else {
            $scope.isPromptSelectShow = true;
            $scope.stoListShow = true;
            $scope.alreadySelect = true;

        }
        // 筛选相关

        // 注入我们的“二阶段过滤器”
        var twoPhaseFilter = $filter('twoPhaseFilter');

        //快速搜索字符串
        $scope.queryInfo = {
            query: "",
        };

        $scope.displayedOrderInfo = [];
        $scope.selectedCatId = '';

        // 筛选条件
        $scope.predicate = {
            oneMatch: {},
            allMatch: {},
        };
        // 选择仓库
        $scope.setStoTo = function (item) {
            var str = "选择后将锁定" + item.sto_name + "仓库"
            PageService.showConfirmDialog(str, [], function () {
                $scope.alreadySelect = false;
                StockService.querySKUSTO(1, item.sto_id).then(function () {
                    $scope.skuList = StockService.getSkuStoList();
                    $scope.isPromptSelectShow = false;
                    $scope.stoListShow = false;
                });
                model.orderInfo.sto_id = item.sto_id;
            })
        };
        // 选择类别
        $scope.setCatTo = function (cat_id) {
            // $log.log('cat_id',cat_id);
            $scope.predicate.allMatch.cat_id = cat_id;
            $scope.selectedCatId = cat_id;
        };

        $scope.$watch('queryInfo.query', function (newValue, oldValue) {
            $scope.predicate.oneMatch.spu_name = newValue;
            $scope.predicate.oneMatch.cat_name = newValue;
            $scope.predicate.oneMatch.spec_name = newValue;
            $scope.predicate.oneMatch.qcode = newValue;
        }, true);

        // 检测筛选条件
        $scope.$watch('predicate', function () {
            for (var key in $scope.skuOrderInfo) {
                $scope.skuOrderInfo[key].isShow = false;
                $scope.skuOrderInfo[key].isShowAgain = false;
            }
            $scope.setSkuShow($scope.predicate.oneMatch);
            $scope.setSkuShow($scope.predicate.allMatch);
            // $scope.displayedOrderInfo = twoPhaseFilter($scope.skuList,$scope.predicate);
        }, true);

        // 监测sku源数据
        $scope.$watch('skuList', function () {
            $scope.skuOrderInfo = $scope.skuList;
            for (var key in $scope.skuOrderInfo) {
                $scope.skuOrderInfo[key].stock = Number($scope.skuOrderInfo[key].stock).toFixed(2);
                $scope.skuOrderInfo[key].isShow = false;
                $scope.skuOrderInfo[key].isShowAgain = false;
            }
            $scope.setSkuShow($scope.predicate.oneMatch);
            $scope.setSkuShow($scope.predicate.allMatch);

            // $scope.displayedOrderInfo = twoPhaseFilter($scope.skuList,$scope.predicate);
        }, true);

        $scope.setSkuShow = function (item) {//检查是否通过本次过滤
            var input = $scope.skuOrderInfo;
            var flag;
            var tested;
            var str;
            var index;
            var predicate = item;


            for (var key in predicate) {
                predicate[key] = predicate[key].toString();
            }

            for (var i = 0; i < input.length; i++) {

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
                    if ($scope.skuOrderInfo[i].isShow) {
                        $scope.skuOrderInfo[i].isShowAgain = true;
                    }
                    $scope.skuOrderInfo[i].isShow = true;
                }
            }
        };

        $scope.setCartItem = function (item) {

            $scope.editingItem = item;
            $scope.priceInput.isShow = true;
            $scope.cartAgent.setEditingCartItemBySku(item);
            // $log.error("cart",$scope.cartAgent)
        };

        // 计算并设置商品列表的宽度,使之与上面的搜索栏宽度一致
        $scope.setGoodListWH = function () {
            $('#good-list').height($(window).innerHeight() - $('.search-and-cat').height() - 135);

            var width = $('.search-and-cat').width();
            var num = Math.round(width / 100);
            $scope.len = width / num - 2;

            $('.good-unit').width($scope.len);
            $('.good-unit').height($scope.len);
            $('.already-add').css({marginTop: -$scope.len});
        };
        window.onresize = $scope.setGoodListWH;

        $scope.createSKU = function () {
            EventService.emit(EventService.ev.START_CREATE_EDIT_SPU);
            EventService.emit(EventService.ev.RESTORE_LOCK_SHOP_STATUS);
        };

        var closePriceInputHandle = EventService.on(EventService.ev.CLOSE_PRICE_INPUT, function () {
            if ($scope.editingItem) {
                $scope.cartAgent.setEditingCartItemByCartItem($scope.cartAgent.cartItemList[$scope.editingItem.sku_id]);
            }
        });

        $scope.$on('$destroy', function () {
            closePriceInputHandle();
        })
    }
]);