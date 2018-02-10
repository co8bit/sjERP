//xxxxController
'use strict'

angular.module('XY').controller('ReceiptAndPaymentController', ['EventService', '$scope', '$log', 'ReceiptAndPaymentModel', 'NumPadClassService', 'CstmrService',
    '$timeout', 'FinanceService', 'PageService','MiscService',
    function (EventService, $scope, $log, model, NumPadClassService, CstmrService,
        $timeout, FinanceService, PageService,MiscService) {

        // $log.debug('ReceiptAndPaymentController')


        $scope.model = model;
        // 收款/付款单对象
        model.docInfo.updateActual();
        $scope.docInfo = model.docInfo;
        var docInfo = {};
        $timeout(function () {
            docInfo = angular.copy($scope.docInfo, docInfo);
        }, 200);
        $scope.now_page = 1;
        $scope.companyList = CstmrService.company.getCompanyList();
        model.allList = model.localTable.getAllData();
        model.pline = model.localTable.getPline();
        $scope.allList = model.allList;
        $scope.orderList = model.orderListShow;
        $scope.pageList = model.localTable.getPageList();
        $scope.isMobile = MiscService.testMobile();
        // $scope.companyNameInput = ''
        $scope.companyNameInput = $scope.docInfo.company.name
        // 显示往来单位自动完成列表
        $scope.isCompanyListShow = false

        // 初始化开单显示时间
        var time = new Date();
        var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
        var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        $scope.reg_time = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
        // 使用jqUI
        $(function () {
            $scope.docInfo.reg_time = Math.floor(new Date().getTime() /1000) ;
            $("#datepicker1").datepicker({
                showAnim : 'slide',
                // changeMonth: true,
                // changeYear: true,
                onSelect: function (timeStr) {
                    $scope.docInfo.reg_time = new Date(timeStr).getTime() /1000 ;
                    $log.debug('datepicker1',new Date(timeStr).getTime() /1000 );

                    //当前小时 分 秒 判断选择日期是否为今天？返回当前时间 ：返回选择日期的23:59
                    var Hours=new Date().getHours(),
                    Minutes=new Date().getMinutes(),
                    Seconds=new Date().getSeconds();
                    if($scope.docInfo.reg_time-8*3600 == parseInt(new Date()/1000)-Hours*3600-Minutes*60-Seconds){
                        $scope.docInfo.reg_time = new Date().getTime() /1000;
                        $log.debug("testnow:",$scope.docInfo.reg_time,new Date($scope.docInfo.reg_time*1000));
                    }else{
                        $scope.docInfo.reg_time = $scope.docInfo.reg_time + 16 * 3600 - 60 ;
                        $log.debug("testnow:",$scope.docInfo.reg_time,new Date($scope.docInfo.reg_time*1000));
                    }
                },
                showButtonPanel: true,
            })
            angular.element('#function-tab').css('height',37 * 10 + 379 + 'px');
        });

        $scope.totalValue = 0;
        $scope.totalRemain = 0;
        $scope.calTotal=function(){
            var totalValue = 0;
            var totalRemain = 0;
            for(var key in $scope.orderList){
                if($scope.orderList.status != 100){
                    if($scope.orderList[key].value>0) totalValue = totalValue + Number($scope.orderList[key].value);
                    if($scope.orderList[key].remain > 0) totalRemain = totalRemain + Number($scope.orderList[key].remain);
                    if($scope.orderList[key].remain_opposite>0) totalRemain = totalRemain + Number($scope.orderList[key].remain_opposite);
                }
            }
            $scope.totalValue = totalValue.toFixed(2);
            $scope.totalRemain = totalRemain.toFixed(2);
        }


        // 点击了页码
        $scope.selectPage = function (pageItem) {
            model.localTable.changeNowPage(pageItem);
            $log.debug('in selectPage, pageItem = ', pageItem);
            $scope.now_page = model.localTable.getNowPage();
            $scope.calTotal();
            $scope.calcTotalMoney();
        }

        //点击客户输入框
        $scope.clickcompanyNameInput = function () {
            $scope.companyNameInput = '';
            $scope.isCompanyListShow = true;
        }

        // 选定一个客户
        $scope.clickCompany = function (company) {
            $scope.companyNameInput = company.name;
            $scope.isCompanyListShow = false;
            angular.element('#companyNameInput').blur();
        }
        document.body.click = function(){
            angular.element('#datepicker1').blur();

        }
        // 新增往来单位
        $scope.clickAddNewCompany = function () {
            EventService.emit(EventService.ev.START_CREATE_COMPANY);
        }

        //watch客户输入框,输入框内容变化时自动匹配联系人，匹配上了自动选定往来单位
        $scope.$watch('companyNameInput', function (newValue) {
            $scope.totalMoney = 0;
            $scope.totalNow = 0;
            $scope.totalRemain = 0;
            $scope.totalValue = 0;
            var company = CstmrService.company.findCompanyByName(newValue);
            // $log.log('company:', company);
            $scope.model.docInfo.setCompany(company);
            $scope.allList = model.localTable.getAllData();
            // $log.log('allList ', $scope.allList);
            // $log.log('orderList ', $scope.orderList);

        })


        // 当选定新的往来单位时自动查询他的联系人信息,以提供“最近使用的联系人”  并在输入框中显示
        $scope.$watch('model.docInfo.company.cid', function (newValue) {
            // $log.debug('model.docInfo.company.cid changed')
            // cid>0说明已选定往来单位
            if (newValue > 0) {
                // 查询待收款订单
                if ($scope.model.docInfo._class == 71) {
                    CstmrService.company.queryRemain(1, newValue);
                }
                // 查询待付款订单
                if ($scope.model.docInfo._class == 72) {
                    CstmrService.company.queryRemain(2, newValue);
                }
                // 在输入框中显示名字
                $scope.companyNameInput = $scope.docInfo.company.name;

                // 更新支付数据
                // $scope.model.orderInfo.calcNumber()
            }
        })

        //当发生点击body事件，判断是否点发生在客户姓名输入框
        var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function (angularEvent, domEvent) {

            $timeout(function () {
                // 如果没在输入框上也没在下来列表上就关闭下拉列表
                // $log.log(domEvent.target)
                if (!($(domEvent.target).hasClass('cstmr-name-input')) && !($(domEvent.target).hasClass('auto-complete-content'))) {
                    $scope.isCompanyListShow = false;
                }
            })
        })

        //销毁监听
        $scope.$on('$destroy', function () {
            clickHandle();
        })


        // 检测金额变化自动更新
        $scope.$watchGroup(['model.docInfo.cash', 'model.docInfo.bank', 'model.docInfo.online_pay'], function (newValue, oldValue) {
            model.docInfo.updateActual();
        });

        // 清空已分配金额
        $scope.clearAllocated = function () {
            for (var key in $scope.docInfo.orderList) {
                $scope.docInfo.orderList[key].money = ''
            }
        };
        $scope.judge = function (item,$index) {
            if(item.remain<0){
                if(item.money > -item.remain){
                    item.money = -parseFloat(Number(item.remain).toFixed(2))
                }
            }else {
                if(item.money > item.remain){
                    item.money = parseFloat(Number(item.remain).toFixed(2))
                }
            }
            $scope.allList[$index+$scope.now_page*model.pline-model.pline].money = item.money
        };
        $scope.log=function ($index) {
              $log.log($scope.allList[$index+$scope.now_page*model.pline-model.pline].money);
        }
        // 创建单据
        $scope.createReceiptOrPayment = function () {
            for (var i = 0; i < $scope.allList.length; i++) {
                if ($scope.allList[i].money) {
                    $scope.allList[i].money = parseFloat($scope.allList[i].money.toString());
                }
            }
        //    $log.error(typeof $scope.docInfo.cash)
            $scope.docInfo.cash = parseFloat($scope.docInfo.cash)
            $scope.docInfo.bank = parseFloat($scope.docInfo.bank)
            $scope.docInfo.online_pay = parseFloat($scope.docInfo.online_pay)
        //    $log.error('docInfo1',$scope.docInfo);
        //    $log.error(typeof $scope.docInfo.cash)
            angular.copy($scope.allList, $scope.docInfo.orderList);
            if ($scope.docInfo.company.cid == undefined) {
                PageService.showConfirmDialog('<div class="title"><a class="red">收款对象错误</a></div>'
                    + '<div><a>可能是您直接在输入框中手动输入了不存在的<span class="red">收款对象</span>所导致。您可以通过以下三种中的任意一种方法解决：</a></div>'
                    + '<div><a>1.选择<span class="red">收款对象</span>下拉列表中已有的收款对象名称，而不是手动输入。</a></div>'
                    + '<div><a>2.手动输入<span class="red">收款对象</span>下拉列表中已经存在的收款对象名称。</a></div>'
                    + '<div><a>3.如果该收款对象确实是新往来单位，请点击<span class="red">收款对象</span>下拉列表中的<span class="red">增加新单位</span>按钮新建。</a></div>');
            } else {
                FinanceService.createReceiptOrPayment($scope.docInfo);
            }
        }

        function pressNumCallback(state, num) {
            switch (state) {
                case 0:
                    $scope.docInfo.cash = $scope.numPad.joinTwoNum($scope.docInfo.cash, num)
                    break
                case 1:
                    $scope.docInfo.bank = $scope.numPad.joinTwoNum($scope.docInfo.bank, num)
                    break
                case 2:
                    $scope.docInfo.online_pay = $scope.numPad.joinTwoNum($scope.docInfo.online_pay, num)
                    break
                default:

            }
        }

        function pressOtherCallback(state, input) {
            switch (input) {
                case 'Clear':
                    switch (state) {
                        case 0:
                            $scope.docInfo.cash = ''
                            break
                        case 1:
                            $scope.docInfo.bank = ''
                            break
                        case 2:
                            $scope.docInfo.online_pay = ''
                            break
                    }
                    break
                case 'Finish':
                    switch (state) {
                        case 0:
                            $scope.numPad.setState(1)
                            break
                        case 1:
                            $scope.numPad.setState(2)
                            break
                        case 2:
                            $scope.numPad.setState(0)
                            break
                    }
                    break
                default:
            }
        }
        // 这个页面上的小键盘状态 :
        // state == 0 , 修改数量
        // 1 , 修改单价
        $scope.numPad = NumPadClassService.newNumPad(pressNumCallback, pressOtherCallback)
        $scope.numPad.setState(0);


        $scope.totalNow = 0 ;
        $scope.totalMoney = 0;
        $scope.calcTotalMoney = function () {
            $scope.totalNow = 0 ;
            var money = 0;
            var totalMoney = 0;
            var tempMoney = 0;
            // $log.log('dsf',$scope.orderList);
            for(var key in $scope.orderList){
                if($scope.orderList[key].money>0)tempMoney += $scope.orderList[key].money;
            }
            for (var i = 0; i < $scope.allList.length; i++) {
                money = isNaN(Number($scope.allList[i].money)) ? 0 : Number($scope.allList[i].money);
                totalMoney += money;
            }
            $scope.totalNow = tempMoney.toFixed(2);
            $scope.totalMoney = totalMoney.toFixed(2);

        }

        $scope.$watch('allList', function () {
                        $scope.calTotal();
            if ($scope.allList.length == 0) {
                $scope.totalMoney = 0;
                $scope.totalNow = 0;
                $scope.totalRemain = 0;
                $scope.totalValue = 0;
            }
        }, true);

        $scope.quit = function () {
            //检查是否修改，修改提示
            if (angular.equals(docInfo, $scope.docInfo)) {
                PageService.closeDialog();
            } else {
                PageService.showConfirmDialog('确定退出吗？', ['直接退出', '保存为草稿单据并退出', '取消'], function () {
                    PageService.closeDialog();
                }, function () {
                    // 把每一个金额都存入，没有则存0
                    for (var i = 0; i < $scope.allList.length; i++) {
                        if ($scope.allList[i].money) {
                            $scope.allList[i].money = parseFloat($scope.allList[i].money.toString());
                        }else{
                            $scope.allList[i].money = 0;
                        }
                    }
                    angular.copy($scope.allList, $scope.docInfo.orderList);
                    FinanceService.createReceiptOrPaymentDraft($scope.docInfo);
                });
            }
        }
       
        var getNewCompanyHandle = EventService.on(EventService.ev.CREATE_COMPANY_SUCCESS, function (event, company) {
            // $log.log('newCompany:', company);
            EventService.on(EventService.ev.COMPANY_LIST_LOAD_SUCCESS, function () {
                $scope.companyNameInput = company.name;
            });
        });

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
                $scope.$apply(function () {
                    $scope.quit();
                })
            }
        })

        $scope.$on('$destroy', function () {
            getNewCompanyHandle();
            keydownHandle();
        });

        $(function (){
            if(model.item){
                $scope.companyNameInput = model.item.name;
                // $scope.totRemain = 0 - model.item.balance;
                $scope.totalMoney = 0;
                $scope.totalNow = 0;
                $scope.totalRemain = 0;
                $scope.totalValue = 0;
                var getCompany = setInterval(function () {
                	var company = CstmrService.company.findCompanyByName(model.item.name);
                	// $log.log('company:', company);
                    if (company) {
                    	$scope.model.docInfo.setCompany(company);
                    	$scope.allList = model.localTable.getAllData();
                    	clearInterval(getCompany)
                    }
                }, 100);
            }
        })
        $scope.inputNumber = function(value){
                if(parseFloat($scope.docInfo[value]) < 0 ){
                    $scope.docInfo[value] = 0 - parseFloat($scope.docInfo[value])
                }else{
                    $scope.docInfo[value] = parseFloat($scope.docInfo[value])
                }
        }

        ////收付款单输入框金额不能为负数
        $scope.onlyNumber = function(item,value){
            if(isNaN(item[value])){
                item[value]=parseFloat(item[value]);
            }
            if(item[value]< 0 ){
                 item[value]=0-item[value];   
            }     
        }
    }
])
