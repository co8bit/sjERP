'use strict';

// 开销售单
// 把model中的一些对象映射至$scope，为了给子scope引用，
// 子scope应尽量不透过父scope直接引用model，这样依赖只在相邻两层之间建立，减少耦合

xy.controller('SalesOrderController', ['$scope', '$log', 'StockService', 'SalesOrderModel', 'PageService', 'ConfigService', 'CstmrService', '$filter', 'GenService', 'OrderService', 'EventService', 'SPUClass', 'LockService', '$timeout', '$location', '$anchorScroll',
    function ($scope, $log, StockService, model, PageService, ConfigService, CstmrService, $filter, GenService, OrderService, EventService, SPUClass, LockService, $timeout, $location, $anchorScroll) {
        $scope.orderInfo = {};
        //不是新开的单子会有model.orderInfo
        //备份订单和客户信息 客户信息只有不是新开的有
        var orderInfo = {};
        //备份 company_name
        var company_name = '';
        $scope.orderInfo = model.orderInfo;

        //如果有客户名，显示客户名并备份
        if ($scope.orderInfo.company_name) {
            $scope.companyNameInput = $scope.orderInfo.company_name;
            company_name = $scope.orderInfo.company_name;
        }
        //不是新开的单据
        $scope.isNew = true;
        //初始不显示选着商品界面
        $scope.PickGoodTabIsShow = false;
        // 是否显示往来单位自动完成列表
        $scope.isCompanyListShow = false;
        // 是否显示联系人电话号码自动完成列表
        $scope.isContactPhonenumListShow = false;
        // 是否显示停车位置自动完成列表
        $scope.isParkAddressListShow = false;
        // 是否显示往来单位信息列表
        $scope.isCstmrInfoShow = false;

        // 是否显示车牌号列表
        $scope.isCarLicenseListShow = false;
        // 是否显示送货人/联系人
        $scope.contactNameIsShow = model.contactNameIsShow;


        $scope.whetherDeliver = model.contactNameIsShow;
        // 仓库列表
        $scope.stoList = model.stoList;
        $scope.defaultSto = model.defaultSto;
        model.warehouse?$scope.orderInfo.setWarehouse(model.warehouse):$scope.orderInfo.setWarehouse($scope.defaultSto)//初始化仓库
        // 往来单位
        $scope.companyList = CstmrService.company.getCompanyList();
        // 建立对联系人信息的映射
        $scope.contactList = CstmrService.contact.getContactList();
        // 映射停车位置
        $scope.parkAddressList = CstmrService.parkAddress.getParkAddressList();
        //购物车
        $scope.cartAgent = model.orderInfo.cartAgent;
        //获取到数据后 更新金额 (继续开单之类的用)
        if ($scope.orderInfo.freight) {
            $scope.cartAgent.updateCartInfo();
            $scope.orderInfo.calcNumber();
        }
        //小键盘初始不显示
        $scope.priceInput = {};
        $scope.priceInput.isShow = false;
        //选中的客户cid
        model.selectedCompanyCid = null;
        //选着商品界面初始
        $scope.catInfo = model.catInfo; //获取类别
        $scope.skuInfo = model.skuInfo; //获取skuinfo
        $scope.skuOrderInfo = {}; //选中skuinfo
        $scope.orderInfo.isToday = true//默认是今日开单
        // 初始化开单显示时间
        var time = new Date();
        if (model.timeModeTime) {
            time = new Date(model.timeModeTime);
            $scope.orderInfo.reg_time = time.getTime() / 1000 + 16 * 3600 - 60; //初始化时间到选中日期的23:59
        }
        var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
        var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        $scope.reg_time = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
        $scope.orderInfo.reg_time = time.getTime() / 1000;
        //初始开单方式
        $scope.isPrint = model.isPrint;//获取当前开单按钮显示状态；
        $scope.createOrderItem = model.isPrint == true ? '开单并打印' : '开单';//设置当前开单按钮显示文字；
        $scope.createOrderCheckBoxIsShow = false;
        $scope.reopen = model.reopen; //重开和复制单据都是true，新开和草稿单为false
        $scope.isDelete = model.isDelete;//重开是要作废原单据，复制不作废原单据
        //是否可编辑
        $scope.isDisable = "disabled";
        //退出按钮
        $scope.quit = function () {
            if($scope.reopen){
                PageService.closeDialog();
                return
            }
            if (angular.equals(orderInfo, $scope.orderInfo)) {
                PageService.closeDialog();
            } else {
                PageService.showConfirmDialog('确定退出吗？', ['直接退出', '保存为草稿单据并退出', '取消'], function () {
                    PageService.closeDialog();
                }, function () {
                    // EventService.emit(EventService.ev.START_VIEW_DRAFT,1);
                    OrderService.createDraft($scope.orderInfo);
                });
            }
        };

        /*-------------------------------------------------------------------------分割线           头部结束   客户信息开始----------------------------------------------------------------------------------------------*/

        //点击客户输入框
        $scope.clickcompanyNameInput = function () {
        //    $log.error("1", $scope.stoList);
            $scope.companyNameInput = '';

            $scope.isCompanyListShow = true;
        };
        //点击仓库输入框
        $scope.clickStoNameInput = function () {
            $scope.orderInfo.sto_name = '';
            $scope.isStoListShow = true;
        };

        //watch客户输入框,输入框内容变化时自动匹配联系人，匹配上了自动选定往来单位
        $scope.$watch('companyNameInput', function (newValue) {
            var company = CstmrService.company.findCompanyByName(newValue);
            $scope.orderInfo.setCompany(company);
        });
        // 点击新增客户
        $scope.clickAddNewCompany = function () {
            EventService.emit(EventService.ev.START_CREATE_COMPANY, {}, 1);
        };

        // 选定一个客户
        $scope.clickCompany = function (company) {
            $scope.isCompanyListShow = false;
            $scope.orderInfo.setCompany(company);
            model.selectedCompanyCid = company.cid;
        };

        // 当选定新的往来单位时自动查询他的联系人信息,以提供“最近使用的联系人”  并在输入框中显示
        $scope.$watch('orderInfo.company.cid', function (newValue) {
            $scope.contactList = CstmrService.contact.getContactList();
            // cid>0说明已选定往来单位
            if (newValue > 0) {
                // 查询往来单位的联系人
                CstmrService.contact.queryList(newValue, function (data) {
                    // $log.log('data[0]',data[0]);
                    $scope.orderInfo.setContact(data[0]);
                });
                // 自动选定往来单位的第一个联系人,不能这样做，因为联系人信息可能还没查回来
                // $scope.clickContact($scope.contactList[0]);
                // 显示名字
                $scope.companyNameInput = $scope.orderInfo.company.name;
                // 更新结余
                $scope.orderInfo.calcNumber();
                // $log.log('计算完结余之后',$scope.model.orderInfo);
            }
        });

        //点击联系人选项
        $scope.clickContact = function (contact) {
            $scope.orderInfo.setContact(contact);
            $scope.selectedContact = {};
            angular.copy(contact, $scope.selectedContact);
            $scope.newContact = false;
            $scope.isCstmrInfoShow = true;
        };

        //点击添加联系人按钮
        $scope.addContact = function () {
            $scope.orderInfo.addContact();
            $scope.selectedContact = {};
            $scope.newContact = true;
            $scope.isCstmrInfoShow = true;
        };

        //当发生点击body事件，判断是否点发生在客户姓名输入框
        var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function (angularEvent, domEvent) {
            $timeout(function () {
                // 如果没在输入框上也没在下来列表上就关闭下拉列表

                if (!($(domEvent.target).hasClass('cstmr-name-input')) && !($(domEvent.target).hasClass('auto-complete-content'))) {
                    $scope.isCompanyListShow = false;

                }

                if (!($(domEvent.target).hasClass('sto-name-input')) && !($(domEvent.target).hasClass('auto-complete-content'))) {
                    $scope.isStoListShow = false;
                    if ($scope.orderInfo.sto_name == "") {
                        updateStoInfo($scope.defaultSto);
                        $scope.orderInfo.setWarehouse($scope.defaultSto);
                        $scope.orderInfo.sto_name = $scope.defaultSto.sto_name;
                    }
                }
                // 关闭联系人电话自动完成列表
                if (!($(domEvent.target).hasClass('contact-phonenum'))) {
                    $scope.isContactPhonenumListShow = false;
                }

                // 关闭停车位置自动完成列表
                if (!($(domEvent.target).hasClass('car-license-input'))) {
                    $scope.isCarLicenseListShow = false;
                }

                // 关闭停车位置自动完成列表
                if (!($(domEvent.target).hasClass('parkaddress-input'))) {
                    $scope.isParkAddressListShow = false;
                }

            });
        });

        var getNewCompanyHandle = EventService.on(EventService.ev.CREATE_COMPANY_SUCCESS, function (event, company) {
            $log.log('newCompany:', company);
            EventService.on(EventService.ev.COMPANY_LIST_LOAD_SUCCESS, function () {
                $scope.companyNameInput = company.name;
            });
        });

        //销毁监听
        $scope.$on('$destroy', function () {
            clickHandle();
            getNewCompanyHandle();
        });

        $scope.$watchGroup(['orderInfo.off', 'orderInfo.online_pay', 'orderInfo.cash', 'orderInfo.bank'], function () {
            $scope.orderInfo.calcNumber();
        });

        $scope.$watch('whetherDeliver', function (newValue) {
            model.contactNameIsShow = newValue;
            //newValue是false又，说明是选中了送货退出来了，要重新请求list然后重置第一个为联系人
            if (newValue == false && $scope.orderInfo.company.cid != undefined) {
                CstmrService.contact.queryList($scope.orderInfo.company.cid, function (data) {
                    // $log.log('data[0]',data[0]);
                    $scope.orderInfo.setContact(data[0]);
                    $scope.orderInfo.mobile = '';
                    $scope.orderInfo.car_license = '';
                });
            }
        });

        /*-------------------------------------------------------------------------分割线           商品列表结束   购物车开始----------------------------------------------------------------------------------------------*/
        function updateStoInfo(item) {
                $scope.orderInfo.sto_name = item.sto_name;
            if (item.sto_id != $scope.orderInfo.sto_id) {
                $scope.orderInfo.setWarehouse(item);
                $scope.cartAgent.cartItemList = {};
                $scope.cartAgent.addcount = 0;
                $scope.cartAgent.addprice = 0;
                $scope.isStoListShow = false;
                //更新skulist的信息
                // if ($scope.orderInfo._class === 3) {
                //     return
                // }
                StockService.querySKUSTO(2, item.sto_id).then(function () {
                    model.skuInfo = StockService.getSkuStoList();
                    $scope.skuInfo = model.skuInfo;
                });
            }
        }

        //仓库部分

        $scope.clickSto = function (item) {
            updateStoInfo(item);
        };
        $scope.clickAddNewSto = function () {
            EventService.emit(EventService.ev.CREATE_EDIT_WAREHOUSE,2);
        };
        //点击增加商品按钮
        $scope.addPickGoodTab = function () {
            $scope.skuChange(); //刷新货物列表
            $scope.PickGoodTabIsShow = true;
        };

        //编辑购物车内的商品
        $scope.setCartItem = function (item) {
            $scope.skuChange();
            $scope.editingItem = item;
            $scope.PickGoodTabIsShow = true;
            $scope.priceInput.isShow = true;
            $scope.cartAgent.setEditingCartItemBySku(item); // 第二参数带0,则点击商品弹出的窗口中单价项为空
            $scope.cartAgent.setEditingCartItemPriceAndQuantity($scope.cartAgent.cartItemList[item.sku_id]);
        };
        //删除购物车内的商品
        $scope.deleteItem = function (item) {
            $scope.cartAgent.deleteItem(item);
            $scope.skuChange(); //刷新货物列表
            updateSharePrice();//重新计算运费分摊
        };

        // 购物车价格变化时更新付款信息
        $scope.$watch('cartAgent.totPrice', function () {
            $scope.orderInfo.calcNumber();
        });
        $scope.$watch("cartAgent.cartItemListLength", function () {
            $scope.statistics()
        });
        //统计添加商品的数量价格
        $scope.statistics = function () {
            $scope.cartAgent.addprice = 0;
            $scope.cartAgent.addcount = 0;
            for (let key in $scope.cartAgent.cartItemList) {
                var scope = $scope.cartAgent.cartItemList[key];
                $scope.cartAgent.addcount += scope.quantity;
                $scope.cartAgent.addprice += scope.pile_price;
            }
        };
        /*-------------------------------------------------------------------------分割线           客户信息结束   商品列表开始----------------------------------------------------------------------------------------------*/
        // 计算并设置商品列表的宽度,使之与上面的搜索栏宽度一致
        $scope.setGoodListWH = function () {
            $('#good-list').height($(window).innerHeight() - $('.search-and-cat').height() - 135);

            var width = $('.search-and-cat').width();
            var num = Math.round(width * 0.01);
            $scope.len = width / num - 2;
            $log.log('good-unit', $scope.len);
            $('.good-unit').width($scope.len);
            $('.good-unit').height($scope.len);
            $log.log('create unit');
            $('.already-add').css({marginTop: -$scope.len});
        }
        //窗口改变时执行
        window.onresize = $scope.setGoodListWH;

        // 注入我们的“二阶段过滤器”
        var twoPhaseFilter = $filter('twoPhaseFilter');

        //初始化快速搜索字符串
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

        //监听搜索栏变化
        $scope.$watch('queryInfo.query', function (newValue, oldValue) {

            $scope.predicate.oneMatch.spu_name = newValue;
            $scope.predicate.oneMatch.cat_name = newValue;
            $scope.predicate.oneMatch.spec_name = newValue;
            $scope.predicate.oneMatch.qcode = newValue;

        }, true)

        // 检测筛选条件
        $scope.$watch('predicate', function () {
            $scope.skuChange();
        }, true);

        // 监测sku源数据
        $scope.$watch('model.skuInfo', function () {
            $scope.skuChange();
        }, true)

        //skuInfo 发生变化执行   刷新货物列表
        $scope.skuChange = function () {
            $scope.catInfo = model.catInfo; //获取类别
            $scope.skuInfo = model.skuInfo; //获取skuinfo
            $scope.skuOrderInfo = model.skuInfo;
            for (var key in $scope.skuOrderInfo) {
                $scope.skuOrderInfo[key].stock = Number($scope.skuOrderInfo[key].stock).toFixed(2);
                $scope.skuOrderInfo[key].isShow = false;
                $scope.skuOrderInfo[key].isShowAgain = false;
            }
            $scope.setSkuShow($scope.predicate.oneMatch);
            $scope.setSkuShow($scope.predicate.allMatch);
        };

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
        }

        //编辑不在购物车的商品
        $scope.addCartItem = function (item) {
            $scope.editingItem = item;
            $scope.priceInput.isShow = true;
            $scope.cartAgent.setEditingCartItemBySku(item); // 第二参数带0,则点击商品弹出的窗口中单价项为空
        }

        //编辑在购物车商品
        $scope.editCartItem = function (item) {
            $scope.editingItem = item;
            $scope.PickGoodTabIsShow = true;
            $scope.priceInput.isShow = true;
            $scope.cartAgent.setEditingCartItemBySku(item); // 第二参数带0,则点击商品弹出的窗口中单价项为空
            $scope.cartAgent.setEditingCartItemPriceAndQuantity($scope.cartAgent.cartItemList[item.sku_id]);
        }

        $scope.quitGoodListTotal = function () {
            $scope.PickGoodTabIsShow = false;
            updateSharePrice();//重新计算运费分摊
            $scope.statistics()
        }
        //新增商品成功 刷新货物列表
        EventService.on(EventService.ev.CREATE_SKU_SUCCESS, function () {
            // $scope.skuChange();
        })

        //点击了编辑商品框的取消
        var closePriceInputHandle = EventService.on(EventService.ev.CLOSE_PRICE_INPUT, function () {
            if ($scope.editingItem) {
                $scope.cartAgent.setEditingCartItemByCartItem($scope.cartAgent.cartItemList[$scope.editingItem.sku_id]);
            }
        });

        $scope.$on('$destroy', function () {
            closePriceInputHandle();
        });
        //运费分摊计算
        function updateSharePrice() {
            var count = 0;
            var price = 0;
            if ($scope.cartAgent.cartItemList) {
                for (var k in $scope.cartAgent.cartItemList) {
                    count += parseFloat($scope.cartAgent.cartItemList[k].quantity);
                    price += parseFloat($scope.cartAgent.cartItemList[k].pile_price);
                }
                $scope.cartAgent.addCountAndPrice(count, price);//把购物车的总数和总金额添加进购物车每个商品之内
            }
        }

        //监听运费金额
        $scope.$watch('cartAgent.freight', function (newValue) {
            if (newValue) {
                updateSharePrice();
            }
        })

        // 计算金额（优惠，应收实收应付实付和结余的）
        $scope.updatePaymentInfo = function () {
            $scope.orderInfo.calcNumber();
        }
        /*-------------------------------------------------------------------- 分割线       右侧悬浮开始------------------------------------------------------------------------------------------*/
        //点击全款
        $scope.inFull = function (type) {

            var nowPrice; //当前应收应付
            if ($scope.orderInfo._class == 1 || $scope.orderInfo._class == 4) {
                //收款
                nowPrice = $scope.orderInfo.receivableThisTime;
            } else {
                //付款
                nowPrice = $scope.orderInfo.payableThisTime;
            }
            switch (type) {
                case 'cash':
                    $scope.orderInfo.cash = parseFloat(nowPrice.toFixed(2));
                    $scope.orderInfo.bank = 0;
                    $scope.orderInfo.online_pay = 0;
                    break;
                case 'bank':
                    $scope.orderInfo.bank = parseFloat(nowPrice.toFixed(2));
                    $scope.orderInfo.cash = 0;
                    $scope.orderInfo.online_pay = 0;
                    break;
                case 'online_pay':
                    $scope.orderInfo.online_pay = parseFloat(nowPrice.toFixed(2));
                    $scope.orderInfo.cash = 0;
                    $scope.orderInfo.bank = 0;
                    break;
            }
            $scope.updatePaymentInfo();
        }

        //选择开单
        $scope.createOrderCheck = function (isTrue) {
            //换选项
            if (isTrue) {
                $scope.createOrderItem = '开单并打印';
                $scope.createOrderCheckBoxIsShow = false;
                $scope.isPrint = true;
                model.isPrint = true;
            } else {
                $scope.createOrderItem = '开单';
                $scope.createOrderCheckBoxIsShow = false;
                $scope.isPrint = false;
                model.isPrint = false;
            }
        };

        //判断选项框是否弹出
        $scope.CheckBoxIsShow = function () {
            if ($scope.createOrderCheckBoxIsShow) {
                $scope.createOrderCheckBoxIsShow = false;
            } else {


                $scope.createOrderCheckBoxIsShow = true;
            }
        };
        //点击改变输入框是否可输入状态
        $scope.isQuaBan = false;
        $scope.isPriBan = false;
        $scope.quantityBan = function (item) {
            if (item) {
                item.pile_price = item.quantity * item.unit_price;
                $scope.statistics();
            }
            $scope.isQuaBan = !$scope.isQuaBan;
        };
        $scope.watchQuantity = function (item) {
            if ($scope.orderInfo._class == 1 || $scope.orderInfo._class == 3) {
                if (item.quantity > item.stock) {
                    item.quantity = item.stock;
                }
            }
        };
        $scope.priceBan = function (item) {
            if (item) {
                item.pile_price = item.quantity * item.unit_price;
                $scope.statistics()
            }
            $scope.isPriBan = !$scope.isPriBan;
        };
        // 创建订单 flag_Print 为是否打印
        $scope.createOrder = function (flag_Print) {
            // $log.error($scope.orderInfo);
            LockService.getLockShopStatus(1, function () {
                PageService.showConfirmDialog('开单中...');
                //不是新开的单据就会有 company_name 要是没有重新选择客户 company_name == companyNameInput
                //以下判断后 选择备份中的客户信息
                if (company_name != undefined && company_name == $scope.companyNameInput) {
                    var company = CstmrService.company.findCompanyByName(company_name);
                    $scope.clickCompany(company)
                }
                if ($scope.orderInfo.company.cid == undefined) {
                    if ($scope.orderInfo._class == 1 || $scope.orderInfo._class == 2) {
                        var companyClass = '客户';
                    } else {
                        var companyClass = '供货商';
                    }
                    PageService.showConfirmDialog('<div class="title"><a class="red">' + companyClass + '信息错误</a></div>'
                        + '<div><a>可能是您直接在输入框中手动输入了不存在的<span class="red">' + companyClass + '名称</span>所导致。您可以通过以下三种中的任意一种方法解决：</a></div>'
                        + '<div><a>1.选择<span class="red">' + companyClass + '信息</span>下拉列表中已有的' + companyClass + '名称，而不是手动输入。</a></div>'
                        + '<div><a>2.手动输入<span class="red">' + companyClass + '信息</span>下拉列表中已经存在的' + companyClass + '名称。</a></div>'
                        + '<div><a>3.如果该类别确实是新的往来单位，请点击<span class="red">' + companyClass + '信息</span>下拉列表中的<span class="red">增加新单位</span>按钮新建。</a></div>');
                    return;
                }

                if (!$scope.orderInfo.mobile) delete $scope.orderInfo.mobile;
                if (!$scope.orderInfo.car_license) delete $scope.orderInfo.car_license;

                //如果是重开单据 判断有没有修改客户，没有改变设置为原来的客户
                if ($scope.reopen) {
                    //判断是否删除原单据
                    $scope.orderInfo.reopen = true;
                    if ($scope.isDelete) {
                        $scope.orderInfo.isDelete = true;
                    }
                }

                //判断单据类型
                if ($scope.orderInfo._class == 1) {
                } else if ($scope.orderInfo._class == 2 || $scope.orderInfo._class == 3) {
                    if ($scope.allocateIsShow) {
                        $scope.orderInfo.is_calculated = 1;
                        $scope.orderInfo.freight_cal_method = $scope.allocate;
                    } else {
                        $scope.orderInfo.is_calculated = 0;
                    }

                }
                $scope.orderInfo.freight = $scope.cartAgent.freight; //把购物车里的运费拿出来
                OrderService.createOrder($scope.orderInfo, flag_Print, function () {
                    model.setOptionArray(); //保存按钮选中状态，送货和开单
                    // 判断是否要创建新的联系人
                    if($scope.newContact) {
                        let newContact = {
                            cid: $scope.orderInfo.company.cid,
                            contact_name: $scope.orderInfo.contact_name,
                            cart: {
                                phonenum: [{
                                    phonenum: $scope.orderInfo.mobile
                                }],
                                car_license: [{
                                    car_license: $scope.orderInfo.car_license
                                }]
                            }
                        }
                        CstmrService.contact.create(newContact);
                    }
                    PageService.closeConfirmDialog();
                });
            });
        }
        if (model.item) {
            $scope.PickGoodTabIsShow = true;
            // var item = {};
            // model.spuInfo = SPUClass.newSPU(model.item.spu_id);
            // // model.spuInfo = SPUClass.newSPU(model.item.spu_id);
            // for (var k in model.spuInfo.skus) {
            //     if (model.spuInfo.skus[k].sn == model.item.sn) {
            //         item = model.spuInfo.skus[k];
            //     }
            // }
            $scope.addCartItem(model.item);
        }
        /***********-------------------------------------------页面DOM加载完后----------------------------------------------------------------------------------------------***/
        $(function () {
            //备份orderInfo
            angular.copy($scope.orderInfo, orderInfo);
            switch ($scope.orderInfo._class) {
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


        })

        $scope.onlyNumber = function (value) {
            if (isNaN($scope.orderInfo[value])) {
                $scope.orderInfo[value] = parseFloat($scope.orderInfo[value])
            }
            if (($scope.orderInfo[value]) < 0) {
                $scope.orderInfo[value] = 0 - parseFloat($scope.orderInfo[value])
            }else{
                $scope.orderInfo[value] = parseFloat($scope.orderInfo[value])
            }
        }

        $scope.gotoBottom = function () {
            // 将location.hash的值设置为
            // 你想要滚动到的元素的id
            $location.hash('bottom');
            // 调用 $anchorScroll()
            $anchorScroll();
        };

    }
]);