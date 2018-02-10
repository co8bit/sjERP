'use strict';

xy.controller('GoodSpecPriceInputController', ['$rootScope', '$scope', '$log', 'StockService', 'NumPadClassService', 'MiscService', 'PageService', 'EventService', '$timeout','NetworkService', 'SalesOrderModel',
    function ($rootScope, $scope, $log, StockService, NumPadClassService, MiscService, PageService, EventService, $timeout, NetworkService, model) {

        //$log.debug('AddAeditingCartItem cartAgent: ',$scope.cartAgent)
        $scope.isMobile=MiscService.testMobile();
        $scope.cartAgent.editingCartItem.unit_price = '';
        $scope.thisCompanySkuLastPrice = 0;

        //监控输入框中的数量，更新数量
        $scope.$watch('cartAgent.editingCartItem.quantity', function (newValue, OldValue) {

             if (typeof newValue == 'string') {
                 $scope.cartAgent.editingCartItem.quantity = newValue.replace(/[^0-9 | \.]+/, '');
             }

            if ($scope.orderInfo._class == 1 || $scope.orderInfo._class == 4){
                // var stock = parseInt($scope.cartAgent.editingCartItem.stock);
                var stock = $scope.cartAgent.editingCartItem.stock;
                if ($scope.cartAgent.editingCartItem.quantity > stock) {
                    $scope.cartAgent.editingCartItem.quantity = stock; 
                }
                if ($scope.cartAgent.editingCartItem.quantity <= stock) {
                    $scope.cartAgent.editingCartItem.quantity = $scope.cartAgent.editingCartItem.quantity.toString(); 
                }
            }

            if ($scope.cartAgent) {
                $scope.cartAgent.updateEditingItemPrice();
            } 
            
        });

        //监控输入框中的单价，更新金额
        $scope.$watch('cartAgent.editingCartItem.unit_price', function (newValue, OldValue) {
             if (typeof newValue == 'string') {
                 $scope.cartAgent.editingCartItem.unit_price = newValue.replace(/[^0-9 | \.]+/, '');
             }
             $scope.cartAgent.editingCartItem.unit_price = $scope.cartAgent.editingCartItem.unit_price.toString();
            if ($scope.cartAgent) {
                $scope.cartAgent.updateEditingItemPrice();
            }
        });

        // 监控输入框中商品的sku_id，如果变化,说明开始处理新SKU，广播事件通知小键盘
        $scope.$watch('cartAgent.editingCartItem.sku_id', function () {
            if ($scope.cartAgent.editingCartItem.sku_id == -1)
                return;
            $scope.thisCompanySkuLastPrice = 0;

            //在这里请求Cid对应的上一次售价
            NetworkService.request('getCustomerLastPrice',{cid:model.selectedCompanyCid,sku_id:$scope.cartAgent.editingCartItem.sku_id},function(data){
                $log.log('thisCompanySkuLastPrice上次售价',data);
                if (!data.data.price1)
                    data.data.price1 = 0;
                $scope.thisCompanySkuLastPrice = data.data.price1;
            });


            // if ( ($scope.cartAgent.editingCartItem.unit_price == '') && ($scope.cartAgent.editingCartItem.last_selling_price != 0) )
            //     $scope.cartAgent.editingCartItem.unit_price = ''+$scope.cartAgent.editingCartItem.last_selling_price;

            $scope.$broadcast('EDITING_GOOD_CHANGED')
        })

        $scope.selectCompanySkuLastPrice = function(){
            $scope.cartAgent.editingCartItem.unit_price =  '' + $scope.thisCompanySkuLastPrice;
        }

        $scope.selectSkuRecentPrice = function(){
            $scope.cartAgent.editingCartItem.unit_price =  '' + $scope.cartAgent.editingCartItem.last_selling_price;
        }



        $scope.$on('EDITING_GOOD_CHANGED', function () {
            $log.log('EDITING_GOOD_CHANGED');
            $scope.numPad.setState(0);
            $scope.numPad.setButtonStr('Finish', '下一步');
        })

        function pressNumCallback(state, num) {
            switch (state) {
                case 0:
                    $scope.cartAgent.editingCartItem.quantity = $scope.numPad.joinTwoNum($scope.cartAgent.editingCartItem.quantity, num);
                    break;
                case 1:
                    $scope.cartAgent.editingCartItem.unit_price = $scope.numPad.joinTwoNum($scope.cartAgent.editingCartItem.unit_price, num);
                    break;
                default:
                    ;
            }
        }

        function pressOtherCallback(state, input) {
            switch (input) {
                case 'Clear':
                    switch (state) {
                        case 0:
                            $scope.cartAgent.editingCartItem.quantity = '';
                            break;
                        case 1:
                            $scope.cartAgent.editingCartItem.unit_price = '';
                            break;
                    }
                    break;
                case 'BackSpace':
                    switch (state) {
                        case 0:
                            $scope.cartAgent.editingCartItem.quantity = ($scope.cartAgent.editingCartItem.quantity).toString().substring(0, $scope.cartAgent.editingCartItem.quantity.length - 1);
                            break;
                        case 1:
                            $scope.cartAgent.editingCartItem.unit_price = $scope.cartAgent.editingCartItem.unit_price.toString().substring(0, $scope.cartAgent.editingCartItem.unit_price.length - 1);
                            break;
                    }
                    break;
                case 'Finish':
                $log.log('llllll',state); //使用平台
                    if (state == 0) {
                        $scope.numPad.state += 1;
                        $scope.numPad.setButtonStr('Finish', '完成');
                    } else if (state == 1) {
                        $scope.finishEditing();
                        $scope.numPad.state == 0;
                    }
                    break;
                default:
                    ;
            }
        }

// //检验是否为电脑端
//         $scope.isMobile = true;
//         //$log.log('llllll',isMobile); //使用平台
//             var sUserAgent = navigator.userAgent.toLowerCase();  
//             var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";  
//             var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";  
//             var bIsMidp = sUserAgent.match(/midp/i) == "midp";  
//             var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";  
//             var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";  
//             var bIsAndroid = sUserAgent.match(/android/i) == "android";  
//             var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";  
//             var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";  
//             // document.writeln("您的浏览设备为：");  
//             if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {  
//                 $scope.isMobile = true;
//                 // document.writeln("phone");  
//             } else {  
//                 $scope.isMobile =false;
//                 // document.writeln("pc");  
//             } 





        /**
         * 将商品加入购物车
         */
        $scope.finishEditing = function () {
            if($scope.cartAgent.editingCartItem.quantity<0){
                PageService.showConfirmDialog('库存为负，无法开销售单!');
                return;
            }
            //if (MiscService.testValue($scope.cartAgent.editingCartItem.unit_price)) {
                $scope.cartAgent.addToCart($scope.cartAgent.editingCartItem,false);
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

        $scope.cancelInput = function () {
            $scope.priceInput.isShow = false;
            EventService.emit(EventService.ev.CLOSE_PRICE_INPUT);
        }

        $scope.$watch('priceInput.isShow', function () {
            $timeout(function () {
                $('#first-input').focus();
            });
        });

        $scope.$watch('numPad.state', function () {
            if ($scope.numPad.state == 0) {
                $scope.numPad.setButtonStr('Finish', '下一步');
                $('#first-input').focus();
            } else if ($scope.numPad.state == 1) {
                $scope.numPad.setButtonStr('Finish', '完成');
                $('#second-input').focus();
            }
        });

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 9) {
                ++$scope.numPad.state;
            }
        });

        var finishHandle = EventService.on(EventService.ev.CLICK_FINISH, function () {
            $scope.finishEditing();
        });

        $scope.$on('$destroy', function () {
            finishHandle();
            keydownHandle();
        })
        $scope.priceIsShow = true;
        //退货单和采购单不显示售价
            if(Cookies.get('salesOrderType') != undefined){
                if (Cookies.get('salesOrderType') == '3' || Cookies.get('salesOrderType') == '4') {
                    $scope.priceIsShow = false;
                }else{
                    $scope.priceIsShow = true;
                }
            }
    }
]);