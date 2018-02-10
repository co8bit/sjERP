'use strict';

xy.controller('StockTakingPriceInputController', ['$rootScope', '$scope', '$log', 'StockService', 'NumPadClassService', 'MiscService', 'PageService', 'EventService','UserService', '$timeout',
    function ($rootScope, $scope, $log, StockService, NumPadClassService, MiscService, PageService, EventService,UserService, $timeout) {

        // 正在盘点sku的盈亏数量
        $scope.isMobile = MiscService.testMobile();
        $scope.profitAndLossAmount = 0;
        $scope.profitAndLossValue = 0;

        $scope.rpg = UserService.getLoginStatus().rpg;

        //监控输入框中的数量，更新金额

        $scope.$watch('cartAgent.editingCartItem.quantity', function (newValue, oldValue) {
            $scope.isStockTaking = false;
            if (typeof newValue == 'string') {
                $scope.cartAgent.editingCartItem.quantity =String(newValue).replace(/[^0-9 | \.]+/, '');
            }
            if ($scope.cartAgent) {
                $scope.cartAgent.updateEditingItemPrice();
            }
            if ($scope.cartAgent.editingCartItem.sku_id > 0) {
                $scope.profitAndLossAmount = (Number($scope.cartAgent.editingCartItem.quantity) - Number($scope.skuList2[$scope.cartAgent.editingCartItem.sku_id].stock)).toFixed(2);
                $scope.profitAndLossValue = (Number($scope.cartAgent.editingCartItem.unit_price) * $scope.profitAndLossAmount).toFixed(2);
                if($scope.profitAndLossAmount!=0) {$scope.isStockTaking=true;}
            }
        });

        // 监控输入框中商品的sku_id，如果变化,说明开始处理新SKU，广播事件通知小键盘
        $scope.$watch('cartAgent.editingCartItem.sku_id', function () {
            $scope.$broadcast('EDITING_GOOD_CHANGED')
        });

        $scope.$on('EDITING_GOOD_CHANGED', function () {
            $log.log('EDITING_GOOD_CHANGED');
            $scope.numPad.setState(0);
            // $scope.numPad.setButtonStr('Finish','下一步');
            // 如果选定了sku
            if ($scope.cartAgent.editingCartItem.sku_id <= 0) {
                $scope.profitAndLossAmount = 0;
                $scope.profitAndLossValue = 0;
            }
        });

//检验是否为电脑端
        var isMobile = false;
        //$log.log('llllll',isMobile); //使用平台
            var sUserAgent = navigator.userAgent.toLowerCase();  
            var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";  
            var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";  
            var bIsMidp = sUserAgent.match(/midp/i) == "midp";  
            var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";  
            var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";  
            var bIsAndroid = sUserAgent.match(/android/i) == "android";  
            var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";  
            var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";  
            // document.writeln("您的浏览设备为：");  
            if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {  
                isMobile = true;
                // document.writeln("phone");  
            } else {  
                isMobile =false;
                // document.writeln("pc");  
            } 


        function pressNumCallback(state, num) {
            switch (state) {
                case 0:
                    $scope.cartAgent.editingCartItem.quantity = $scope.numPad.joinTwoNum($scope.cartAgent.editingCartItem.quantity, num);
                    break;
                default:
                    break;
            }
        }

        function pressOtherCallback(state, input) {
            switch (input) {
                case 'Clear':
                    switch (state) {
                        case 0:
                            $scope.cartAgent.editingCartItem.quantity = '';
                            break;
                    }
                    break;
                case 'BackSpace':
                    switch (state) {
                        case 0:
                            $scope.cartAgent.editingCartItem.quantity = $scope.cartAgent.editingCartItem.quantity.substring(0, $scope.cartAgent.editingCartItem.quantity.length - 1);
                            break;
                    }
                    break;
                case 'Finish':
                    $scope.finishEditing();
                    break;
                default:
                    ;
            }
        }

        // 检查是不是数字然后加入购物车
        $scope.finishEditing = function () {
            // if($scope.profitAndLossAmount==0) $scope.cartAgent.deleteFromCart($scope.cartAgent.editingCartItem);
            // else
                $scope.cartAgent.addToCart($scope.cartAgent.editingCartItem,$scope.isStockTaking);
            $scope.priceInput.isShow = false;
        };

        // 这个页面上的小键盘状态 :
        // state == 0 , 修改数量
        // 1 , 修改单价
        $scope.numPad = NumPadClassService.newNumPad(pressNumCallback, pressOtherCallback);
        $scope.numPad.state = 0;

        $scope.cancelInput = function () {
            $scope.priceInput.isShow = false;
            EventService.emit(EventService.ev.CLOSE_PRICE_INPUT);
        }

        $scope.$watch('priceInput.isShow', function () {
            $timeout(function () {
                $('#first-input').focus();
            });
        });

        var finishHandle = EventService.on(EventService.ev.CLICK_FINISH, function () {
            $scope.finishEditing();
        });

        $scope.$on('$destroy', function () {
            finishHandle();
        });
    }
]);