'use strict'
angular.module('XY').controller('RequisitionController', ['$scope', '$log', 'RequisitionModel', 'PageService', 'StockService', 'UserService', 'EventService', 'LockService', '$filter', 'NumPadClassService', '$timeout',
    function ($scope, $log, model, PageService, StockService, UserService, EventService, LockService, $filter, NumPadClassService, $timeout) {
        //把model映射过来
        $scope.model = model;
        $scope.orderInfo = model.orderInfo;
        $scope.orderInfo.cartAgent.addCount = 0;//列表数量之和
        $scope.orderInfo.cartAgent.addPrice = 0;//列表价格之和
        $scope.skuInfo = model.skuInfo;
        $scope.catInfo = model.catInfo;
        $scope.stoList = model.stoList;
        $scope.userList = model.userList;
        $scope.userName = "";
        $scope.priceInput = {};
        $scope.priceInput.isShow = false;
        $scope.orderInfo.remark = "";//备注
        $scope.orderInfo.lockShopToken = LockService.token;
        $scope.orderInfo.check_uid = $scope.userList[0].uid;
        $scope.isSelectSto = false;
        $scope.rpg = UserService.getLoginStatus().rpg;
        //初始化货物列表不显示
        $scope.PickGoodTabIsShow = false;

        //初始化显示时间
        var time = new Date();
        if (model.timeModeTime) {
            time = new Date(model.timeModeTime);
            $scope.orderInfo.reg_time = time.getTime() / 1000 + 16 * 3600 - 60; //初始化时间到选中日期的23:59
        }
        var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
        var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        $scope.reg_time = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
        $scope.orderInfo.reg_time = time.getTime() / 1000;
        // 计算并设置商品列表的宽度,使之与上面的搜索栏宽度一致
        //---------------------------仓库部分------------------------
        var defaultSto = $scope.stoList[0];//默认仓库
        // 入库仓库
        $scope.orderInfo.stoInNameInput = model.orderInfo.new_sto_name ? model.orderInfo.new_sto_name : "";

        $scope.isInStoShow = false;
        $scope.clickStoInInput = function () {
            $scope.orderInfo.stoInNameInput = "";
            $scope.isInStoShow = true;
        };
        $scope.clickInSto = function (item) {
            $scope.orderInfo.setNewSto(item);
            $scope.orderInfo.stoInNameInput = item.sto_name;
            $scope.isInStoShow = false;
        };

        // if (!$scope.orderInfo.sto_id) {//不是草稿单的情况下才设置
        //     $scope.orderInfo.setOldSto(defaultSto);
        // }
        // 出库仓库
        // $scope.orderInfo.stoOutNameInput = model.orderInfo.sto_name ? model.orderInfo.sto_name : defaultSto.sto_name;
        $scope.isOutStoShow = false;
        $scope.clickStoOutInput = function () {
            $scope.orderInfo.stoOutNameInput = "";
            $scope.isOutStoShow = true;
        };
        function updateStoSku(item) {//更改sto来更新sku列表的函数
            $scope.orderInfo.cartAgent.cartItemList = {};
            $scope.orderInfo.stoOutNameInput = item.sto_name;
            $scope.orderInfo.setOldSto(item);
            $scope.orderInfo.cartAgent.addCount = 0;
            $scope.orderInfo.cartAgent.addPrice = 0;
            $scope.isOutStoShow = false;
            StockService.querySKUSTO(2, item.sto_id).then(function () {
                model.skuInfo = StockService.getSkuStoList();
                $scope.skuInfo = model.skuInfo;
            })
        }

        $scope.clickOutSto = function (item) {
            $scope.isSelectSto = true;
            // if ($scope.orderInfo.sto_id != item.sto_id) {
                updateStoSku(item)
            // }
        };
        var orderInfo = angular.copy($scope.orderInfo, orderInfo);
        //---------------------操作人-----------------
        $scope.changeUser = function () {
            $scope.orderInfo.check_uid = $scope.userName.uid
        };
        var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function (angularEvent, domEvent) {
            $timeout(function () {
                // 如果没在输入框上也没在下来列表上就关闭下拉列表

                if (!($(domEvent.target).hasClass("selectStoOut"))) {
                    $scope.isOutStoShow = false;
                    if ($scope.orderInfo.stoOutNameInput == "") {
                        // updateStoSku(defaultSto);
                        $scope.isSelectSto = false;
                    }
                }
                if (!($(domEvent.target).hasClass("selectStoIn"))) {
                    $scope.isInStoShow = false;
                }
            });
        });//监听点击BODY的事件


        $scope.setGoodListWH = function () {
            $('#good-list').height($(window).innerHeight() - $('.search-and-cat').height() - 135);

            var width = $('.search-and-cat').width();
            var num = Math.round(width * 0.01);
            $scope.len = width / num - 2;
            $log.log('good-unit', $scope.len);
            $('.good-unit').width($scope.len);
            $('.good-unit').height($scope.len);
            $log.log('create unit');
            $('.already-add').css({
                marginTop: -$scope.len
            });
        };
        //窗口改变时执行
        window.onresize = $scope.setGoodListWH;
        //退出按钮
        $scope.quit = function () {
            if (angular.equals(orderInfo, $scope.orderInfo) || UserService.getLoginStatus().rpg == 3) {
                PageService.closeDialog();
                LockService.unlockShop();
            } else {
                PageService.showConfirmDialog('确定退出吗？', ['直接退出', '保存为草稿单据并退出', '取消'], function () {
                    PageService.closeDialog();
                    LockService.unlockShop();
                }, function () {
                    // $log.log("model.orderInfo:", model.orderInfo);
                    StockService.createRequisitionDraft($scope.orderInfo);
                });
            }
        };

        $scope.addPickGoodTab = function () {
            if(!$scope.isSelectSto){
                PageService.showSharedToast("请选择出库仓库")
                return;
            }
            $scope.PickGoodTabIsShow = true;
        };

        $scope.skuOrderInfo = {};
        // 注入我们的“二阶段过滤器”
        var twoPhaseFilter = $filter('twoPhaseFilter');
        //快速搜索字符串
        $scope.queryInfo = {
            query: "",
        };
        // 筛选条件
        $scope.predicate = {
            oneMatch: {},
            allMatch: {},
        };
        //设定分类目录的ID
        $scope.setCatTo = function (cat_id) {
            $scope.predicate.allMatch.cat_id = cat_id;
            $scope.selectedCatId = cat_id;
        };

        $scope.$watch('queryInfo.query', function (newValue, oldValue) {
            $scope.predicate.oneMatch.spu_name = newValue;
            $scope.predicate.oneMatch.cat_name = newValue;
            $scope.predicate.oneMatch.spec_name = newValue;
            $scope.predicate.oneMatch.qcode = newValue;

        }, true);
        function totalPriceCount() {//统计合计的总金额和数量
            $scope.orderInfo.cartAgent.addCount = 0;
            $scope.orderInfo.cartAgent.addPrice = 0;
            for (var key in $scope.orderInfo.cartAgent.cartItemList) {
                var item = $scope.orderInfo.cartAgent.cartItemList[key];
                $scope.orderInfo.cartAgent.addCount += item.quantity;
                $scope.orderInfo.cartAgent.addPrice += item.quantity * item.unit_price
            }
        }

        $scope.achieveAdd = function () {
            totalPriceCount()
        };
        // 检测筛选条件
        $scope.$watch('predicate', function () {
            // $log.log('$scope.model.skuInfo',$scope.model.skuInfo);
            // $scope.displayedOrderInfo = twoPhaseFilter($scope.model.skuInfo, $scope.predicate);
            // $scope.skuOrderInfo = $scope.model.skuInfo;
            for (var key in $scope.skuOrderInfo) {
                $scope.skuOrderInfo[key].isShow = false;
                $scope.skuOrderInfo[key].isShowAgain = false;
            }
            $scope.setSkuShow($scope.predicate.oneMatch);
            $scope.setSkuShow($scope.predicate.allMatch);

        }, true);

        // 监测sku源数据
        $scope.$watch('model.skuInfo', function () {
            $scope.skuOrderInfo = $scope.model.skuInfo;
            for (var key in $scope.skuOrderInfo) {
                $scope.skuOrderInfo[key].isShow = false;
                $scope.skuOrderInfo[key].isShowAgain = false;
            }
            $scope.setSkuShow($scope.predicate.oneMatch);
            $scope.setSkuShow($scope.predicate.allMatch);
        }, true);

        $scope.setSkuShow = function (item) { //检查是否通过本次过滤
            var input = $scope.skuOrderInfo;
            if (input == undefined) return;

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

        //设定购物车的item
        $scope.setCartItem = function (item) {
            $scope.editingItem = item;
            $scope.priceInput.isShow = true;

            $scope.orderInfo.cartAgent.setEditingCartItemBySku(item, 0); // 第二参数带0,则点击商品弹出的窗口中单价项为空

            // $log.log('item wjw',item);
            // 本Controller不需要注入SalesOrderModel也可以使用model里面的值，因为父Controller已经注入了Model
            // $log.log('SalesOrderModel.selectedCompanyCid',$scope.model.selectedCompanyCid);
            //  NetworkService.request('getCustomerLastPrice',{cid:$scope.model.selectedCompanyCid,},function(data){
            //     $log.log('data wjw',data);
            // });
        };

        $scope.editCartItem = function (item) {
            $scope.editingItem = item;
            $scope.priceInput.isShow = true;

            $scope.orderInfo.cartAgent.setEditingCartItemBySku(item, 0); // 第二参数带0,则点击商品弹出的窗口中单价项为空
            $log.log('data lsj', $scope.orderInfo.cartAgent.cartItemList[item.sku_id]);
            $scope.orderInfo.cartAgent.setEditingCartItemPriceAndQuantity($scope.orderInfo.cartAgent.cartItemList[item.sku_id]);
        };

        var closePriceInputHandle = EventService.on(EventService.ev.CLOSE_PRICE_INPUT, function () {
            if ($scope.editingItem) {
                $scope.orderInfo.cartAgent.setEditingCartItemByCartItem($scope.orderInfo.cartAgent.cartItemList[$scope.editingItem.sku_id]);
            }
        });

        $scope.$on('$destroy', function () {
            closePriceInputHandle();
        });

        $scope.quitGoodListTotal = function () {
            $scope.PickGoodTabIsShow = false;
        };
        /**
         * 下面是numpad 相关
         */

        //监控输入框中的数量，更新金额
        $scope.$watch('orderInfo.cartAgent.editingCartItem.quantity', function (newValue, oldValue) {
            $scope.isStockTaking = false;
            if (typeof newValue == 'string') {
                $scope.orderInfo.cartAgent.editingCartItem.quantity = newValue.replace(/[^0-9 | \.]+/, '');
            }
            if ($scope.orderInfo.cartAgent) {
                $scope.orderInfo.cartAgent.updateEditingItemPrice();
            }
            if ($scope.orderInfo.cartAgent.editingCartItem.sku_id > 0) {
                $scope.profitAndLossAmount = Number($scope.orderInfo.cartAgent.editingCartItem.quantity).toFixed(2);
                $scope.profitAndLossValue = (Number($scope.orderInfo.cartAgent.editingCartItem.unit_price) * $scope.profitAndLossAmount).toFixed(2);
                if ($scope.profitAndLossAmount != 0) {
                    $scope.isStockTaking = true;
                }
            }
            if($scope.orderInfo.cartAgent.editingCartItem.quantity > $scope.orderInfo.cartAgent.editingCartItem.stock){
                $scope.orderInfo.cartAgent.editingCartItem.quantity = $scope.orderInfo.cartAgent.editingCartItem.stock
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
            if ($scope.orderInfo.cartAgent.editingCartItem.sku_id <= 0) {
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
            isMobile = false;
            // document.writeln("pc");
        }

        $scope.createReqOrder = function () {
            StockService.createRequisition($scope.orderInfo);
        };
        function pressNumCallback(state, num) {
            switch (state) {
                case 0:
                    $scope.orderInfo.cartAgent.editingCartItem.quantity = $scope.numPad.joinTwoNum($scope.orderInfo.cartAgent.editingCartItem.quantity, num);
                    break;
                case 1:
                    $scope.orderInfo.cartAgent.editingCartItem.unit_price = $scope.numPad.joinTwoNum($scope.orderInfo.cartAgent.editingCartItem.unit_price, num);
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
                            $scope.orderInfo.cartAgent.editingCartItem.quantity = '';
                            break;
                    }
                    break;
                case 'BackSpace':
                    switch (state) {
                        case 0:
                            $scope.orderInfo.cartAgent.editingCartItem.quantity = $scope.orderInfo.cartAgent.editingCartItem.quantity.substring(0, $scope.orderInfo.cartAgent.editingCartItem.quantity.length - 1);
                            break;
                    }
                    break;
                case 'Finish':
                    $scope.finishEditing();
                    break;
                default:
            }
        }

        // 检查是不是数字然后加入购物车
        $scope.finishEditing = function () {
            $scope.orderInfo.cartAgent.addToCart($scope.orderInfo.cartAgent.editingCartItem, $scope.isStockTaking);
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
        };


        var finishHandle = EventService.on(EventService.ev.CLICK_FINISH, function () {
            $scope.finishEditing();
        });

        $scope.$on('$destroy', function () {
            finishHandle();
        });
        $("#datepicker1").datepicker({
            showAnim: 'slide',
            onSelect: function (timeStr) {
                $scope.orderInfo.reg_time = new Date(timeStr).getTime() / 1000;
                $log.debug('datepicker1', new Date(timeStr).getTime() / 1000);
                //当前小时 分 秒 判断选择日期是否为今天？返回当前时间 ：返回选择日期的23:59
                var Hours = new Date().getHours(),
                    Minutes = new Date().getMinutes(),
                    Seconds = new Date().getSeconds();
                if ($scope.orderInfo.reg_time - 8 * 3600 == parseInt(new Date() / 1000) - Hours * 3600 - Minutes * 60 - Seconds) {
                    $scope.orderInfo.reg_time = new Date().getTime() / 1000;
                    $log.debug("testnow:", $scope.orderInfo.reg_time, new Date($scope.orderInfo.reg_time * 1000));
                } else {
                    $scope.orderInfo.isToday = false;
                    $scope.orderInfo.reg_time = $scope.orderInfo.reg_time + 16 * 3600 - 60;
                    $log.debug("testnow:", $scope.orderInfo.reg_time, new Date($scope.orderInfo.reg_time * 1000));
                }
            },
            showButtonPanel: true,
        })

    }
]);