//CreateIncomeController
'use strict';

angular.module('XY').controller('IncomeAndExpenseController', ['EventService', '$scope', '$log', 'IncomeAndExpenseModel', 'FinanceService', 'NumPadClassService','PageService', 'MiscService',
    function(EventService, $scope, $log, model, FinanceService, NumPadClassService, PageService, MiscService) {

        model.docInfo.updateActual();
        $scope.model = model;
        $scope.docInfo = model.docInfo;
        var docInfo = angular.copy($scope.docInfo, docInfo);

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

        });

        // 检测金额变化自动更新
        $scope.$watch('docInfo.cash', function (newValue, oldValue) {
            // if (!MiscService.testValue(newValue)) {
            //     // $scope.docInfo.cash = newValue.replace(/[^0-9 | \.]+/, '');
            // }
            $scope.docInfo.updateActual();
        });

        $scope.$watch('docInfo.bank', function (newValue, oldValue) {
            // if (!MiscService.testValue(newValue)) {
            //     // $scope.docInfo.bank = newValue.replace(/[^0-9 | \.]+/, '');
            // }

            $scope.docInfo.updateActual();
        });

        $scope.$watch('docInfo.online_pay', function (newValue, oldValue) {
            // if (!MiscService.testValue(newValue)) {
            //     // $scope.docInfo.online_pay = newValue.replace(/[^0-9 | \.]+/, '');
            // }

            $scope.docInfo.updateActual();
        });

        $scope.createIncome = function() {
        	FinanceService.createIncomeOrExpense($scope.docInfo);
            $scope.docInfo.cash = parseFloat($scope.docInfo.cash)
            $scope.docInfo.bank = parseFloat($scope.docInfo.bank)
            $scope.docInfo.online_pay = parseFloat($scope.docInfo.online_pay)
            $scope.numPad.state = -1;
        }
        $scope.createExpense = function() {
            FinanceService.createIncomeOrExpense($scope.docInfo);
            $scope.docInfo.cash = parseFloat($scope.docInfo.cash)
            $scope.docInfo.bank = parseFloat($scope.docInfo.bank)
            $scope.docInfo.online_pay = parseFloat($scope.docInfo.online_pay)
            $scope.numPad.state = -1;
        }

        function pressNumCallback(state, num) {
            switch (state) {
                case 0:
                    $scope.docInfo.cash = $scope.numPad.joinTwoNum($scope.docInfo.cash, num);
                    break;
                case 1:
                    $scope.docInfo.bank = $scope.numPad.joinTwoNum($scope.docInfo.bank, num);
                    break;
                case 2:
                    $scope.docInfo.online_pay = $scope.numPad.joinTwoNum($scope.docInfo.online_pay, num);
                    break;
                default:;
            }
        }

        function pressOtherCallback(state, input) {
            switch (input) {
                case 'Clear':
                    switch (state) {
                        case 0:
                            $scope.docInfo.cash = '';
                            break;
                        case 1:
                            $scope.docInfo.bank = '';
                            break;
                        case 2:
                            $scope.docInfo.online_pay = '';
                            break;
                    }
                    break;
                case 'BackSpace':
                    switch (state) {
                        case 0:
                            $scope.docInfo.cash = $scope.docInfo.cash.substring(0, $scope.docInfo.cash.length - 1);
                            break;
                        case 1:
                            $scope.docInfo.bank = $scope.docInfo.bank.substring(0, $scope.docInfo.bank.length - 1);
                            break;
                        case 2:
                            $scope.docInfo.online_pay = $scope.docInfo.online_pay.substring(0, $scope.docInfo.online_pay.length - 1);
                            break;
                    }
                    break;
                case 'Finish':
                    if (state < 2) {
                        $scope.numPad.state += 1;
                    } else if (state == 2) {
                        if ($scope.docInfo._class == 73) {
                            $scope.createIncome();
                        } else if ($scope.docInfo._class == 74) {
                            $scope.createExpense();
                        }
                    }
                    break;
                default:;
            }
        }

        // 这个页面上的小键盘状态 :
        // 0 输入现金金额
        // 1 输入银行金额
        // 2 输入网络付款金额
        $scope.numPad = NumPadClassService.newNumPad(pressNumCallback, pressOtherCallback);
        $scope.numPad.setState(-1);
        $scope.numPad.setButtonStr('Finish','下一步');

        $scope.$watch('numPad.state', function () {
            switch ($scope.numPad.state) {
                case -1:
                    $scope.numPad.setButtonStr('Finish','下一步');
                    break;
                case 0:
                    $scope.numPad.setButtonStr('Finish','下一步');
                    $('#first-input').focus();
                    break;
                case 1:
                    $scope.numPad.setButtonStr('Finish','下一步');
                    $('#second-input').focus();
                    break;
                case 2:
                    $scope.numPad.setButtonStr('Finish','完成');
                    $('#third-input').focus();
                    break;
            }
        });


        $scope.quit = function() {
            if (angular.equals($scope.docInfo, docInfo)) {
                PageService.closeDialog();
            } else {
                PageService.showConfirmDialog('确定退出吗？',['直接退出','保存为草稿单据并退出','取消'],function () {
                    PageService.closeDialog();
                },function () {
                    FinanceService.createIncomeOrExpenseDraft($scope.docInfo);
                });
            }
        }

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 9) {
                ++$scope.numPad.state;
            }

            if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
                $scope.$apply(function () {
                    $scope.quit();
                })
            }
        })

        $scope.$on('$destroy', function () {
            keydownHandle();
        })

        $scope.inputNumber = function(value){
                if(parseFloat($scope.docInfo[value]) < 0 ){
                    $scope.docInfo[value] = 0 - parseFloat($scope.docInfo[value])
                }else{
                    $scope.docInfo[value] = parseFloat($scope.docInfo[value])
                }
        }
    }
]);
