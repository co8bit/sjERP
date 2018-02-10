'use strict';

// 用户支付页面
xy.controller('AccountPayController', ['$scope', '$log', 'PageService', 'EventService', 'PayService', 'AccountPayModel',
    function ($scope, $log, PageService, EventService, PayService, model) {

        $scope.chargeNum = '';
        $scope.btnsName = new Array(4);

        /**
         * 生成按钮上的文字
         */
        var genBtnName = function () {
            if ($scope.userAccountInfo.member_class == 0)
            {
                $scope.btnsName[1] = '立即开通';
            }else{
                $scope.btnsName[1] = '续费升级';
            }
            // for (var i = 0; i < $scope.btnsName.length; i++) {
            //     if (i == 0) {
            //         $scope.btnsName[i] = '';
            //         continue;
            //     }
            //     if ($scope.userAccountInfo.member_class == 0) {
            //
            //         continue;
            //     }
            //     if ($scope.userAccountInfo.member_class < i) {
            //         $scope.btnsName[i] = '升级';
            //     } else if ($scope.userAccountInfo.member_class == i) {
            //         $scope.btnsName[i] = '续费';
            //     } else if ($scope.userAccountInfo.member_class > i) {
            //         $scope.btnsName[i] = '';
            //     }
            //     $log.log('$scope.btnsName[' + i + ']', $scope.btnsName[i]);
            // }
        }

        genBtnName();

        PayService.pay(model.memberInfo);

        // 用户开通选择的类型和开通的月份
        var memberInfo = {
            type: 0,
            count: 0,
            money: 0,
        }

        // 价格表
        var price = [{
            original: [360],
            current: []
        }, {
            original: [888],
            current: []
        }, {
            original: [999,1699,2199],
            current: []
        },{
            original: [1288,2288,2888],
            current: []
        },{
            original: [1666,2888,3666],
            current: []
        },{
            original: [2999,4999,6999],
            current: []
        },{
            original: [299],
            current: []
        }];

        /**
         * 设置价格
         */
        function setPrice() {
            var t = memberInfo.type;

            if (t > 0) {
                $scope.moreUser =  t>2?true:false;
                for(var i =0,len = price[t-1].original.length;i<len;i++){
                    $("#original-price-" + i).html(addYuan(price[t - 1].original[i]));
                }
            }
            $scope.showSelectDialog();
        }

        /**
         * 给价格前加上¥
         * 
         * @param {Number} price 价格
         * @returns
         */
        function addYuan(price) {
            var out = '¥' + String(price);
            return out;
        }

        /**
         * 给价格前加上空格&ensp;
         * 
         * @param {String} price 价格
         * @returns
         */
        // function addSpace(price) {
        //     var len = price.length;
        //     len = len == 2 ? 3 : len; // 如果价格本身长度为1,前面也加一个空格
        //     for (var i = 0; i < len - 2; i++) {
        //         price = '&ensp;' + price;
        //     }
        //     return price;
        // }

        var selectedYear = 0;

        /**
         * 设置选中的年
         * 
         * @param {Number} year 选中的年
         */
        $scope.selectYear = function (year) {
            switch (year) {
                case 1:
                    selectedYear = 1;
                    break;
                case 2:
                    selectedYear = 2;
                    break;
                case 3:
                    selectedYear = 3;
                    break;
            }
            for (var i = 1; i <= 3; i++) {
                if (i == year) {
                    $('#month-' + i).addClass('selected');
                } else {
                    $('#month-' + i).removeClass('selected');
                }
            }
        }

        /**
         * 显示支付时长的选择dialog
         */
        $scope.showSelectDialog = function () {
            $scope.selectYear(4);
            $("#select-dialog").css({
                "visibility": "visible"
            });
        }

        /**
         * 隐藏支付时长的选择的dialog
         */
        $scope.hideSelectDialog = function () {
            $("#select-dialog").css({
                "visibility": "hidden"
            });
        }

        /**
         * 设置用户开通类型
         * @param type
         */
        $scope.setMemberInfoType = function (type) {
            memberInfo.type = type;
            setPrice();
        }

        /**
         * 设置用户开通选择的月份
         */
        $scope.setMemberInfoCount = function () {
            if(!selectedYear) {
                PageService.showSharedToast('请选择时长！');
                return
            }
           else{
                memberInfo.count = selectedYear*12;
                $scope.hideSelectDialog();
                PayService.pay(memberInfo);
            }
        }

        /**
         * 充值短信
         */
        $scope.chargeSMS = function () {
            if ($scope.chargeNum == '') {
                PageService.showConfirmDialog('请输入要充值的金额!');
                return;
            } else {
                var chargeNum = parseInt($scope.chargeNum * 100, 10);
            }

            memberInfo.type = 4;
            memberInfo.money = chargeNum;
            PayService.pay(memberInfo);
        }

        $scope.quit = function () {
            PageService.showConfirmDialog('确定放弃支付吗？', [], function () {
                PageService.closeDialog();
            });
        }

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
                $scope.$apply(function () {
                    $scope.quit();
                })
            }
        });

        $scope.$on('$destroy', function () {
            keydownHandle();
        });

    }
]);