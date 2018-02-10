'use strict';

// 用户支付页面
xy.controller('ExpenseCenterController', ['$scope', '$log', 'PageService', 'EventService', 'PayService', 'ExpenseCenterModel','MiscService',
    function ($scope, $log, PageService, EventService, PayService, model,MiscService) {
        $(function () {
            // $("#datepicker1").datepicker({
            //     onSelect: function (timeStr) {//选中日期后触发时间
            //         $scope.query.setStartTime(timeStr);
            //         $scope.query.request();
            //     },
            //     showButtonPanel: true,
            // })
            // $("#datepicker2").datepicker({
            //     onSelect: function (timeStr) {
            //         $scope.query.setEndTime(timeStr);
            //         $scope.query.request();
            //     },
            //     showButtonPanel: true,
            // })
            if(model.time != undefined && model.time != ''){
                var time = new Date(model.time * 1000);
                var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
                var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
                $('#reportrange span').html(time.getFullYear() + '-' + month + '-' + day + ' 至  ' + time.getFullYear() + '-' + month + '-' + day);
                model.time = '';
            }else{
                $('#reportrange span').html('' + ' 至 ' + '');
            }
            $('#reportrange').daterangepicker({
                startDate: moment().subtract('days',3).startOf("month"),
//					showDropdowns:true,
                // endDate: moment(),
                // maxDate : moment(), //最大时间
                // dateLimit : {
                //     days : 30
                // }, //开始和结束最大间隔天数
                showDropdowns: true, //下拉选择年月
                showWeekNumbers: false, // 是否显示第几周
                timePicker: false, //时间选择
                timePickerIncrement: 60, //分钟选择间隔
                timePicker12Hour: false, //24小时制 false为12小时制
                language: 'zh-CN',
                ranges: {
                    '所有': [moment().subtract(16,"month").startOf("month"), moment()],
                    '今日': [moment().startOf('day'), moment()],
                    '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
                    '最近7日': [moment().subtract('days', 6), moment()],
                    '最近30日': [moment().subtract('days', 29), moment()],
                    '本月': [moment().startOf("month"), moment().endOf("month")],
                    '上个月': [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
                },
                opens: 'right', //弹出位置
                buttonClasses: ['xy-btn xy-btn-default'],
                applyClass: 'xy-btn-small orange',
                cancelClass: 'xy-btn-small',
                format: 'YYYY-MM-DD HH:mm:ss', //日期格式
                separator: ' to ',

                locale: {
                    applyLabel: '确认',
                    cancelLabel: '取消',
                    fromLabel: '起始时间',
                    toLabel: '结束时间',
                    customRangeLabel: '自定义',
                    firstDay: 1,
                },
            }, function(start, end, label) { //选择日期后回调
                $scope.query.setStartTime( new Date(start).getTime() ); // 设置查询起始时间
                $scope.query.setEndTime( new Date(end).getTime() ); //设置查询结束时间
                $scope.query.request(); //开始查询
                $scope.data = model.query.getData();
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
            });
            $('#daterangepicker').addClass('show-calendar');
            $('#daterangepicker').css('display','none');
        });
        $scope.chargeNum = '';
        $scope.userAccountInfo = model.userAccountInfo;
        $scope.saved = model.saved;
        $scope.query = model.query;
        $scope.data = model.query.getData();
        $scope.paginator = model.query.getPaginator();
        $scope.timeFilter = model.query.getTimeFilter();
        $scope.tab = 2;
        $scope.isChargeDialogShow = false;
        $scope.isSMSDetailDialogShow = false;
        $scope.sms = {};//
        $scope.isMobile = MiscService.testMobile();
        $scope.VipEndCountdown = getDayDifference( new Date().getTime(),model.userAccountInfo.member_end_time*1000);//js时间戳为毫秒级
        $scope.vip_end_date = new Date(model.userAccountInfo.member_end_time*1000).toLocaleDateString();
        function getDayDifference(time1,time2) {
            $log.debug('time1:'+time1+',time2:'+time2);
            if(!time1 || !time2) return 0;
            if(time1 == 0 || time2 == 0) return 0;
            var day = Math.ceil((time2-time1)/86400000);
            return day;
        }
        
        var memberInfo = {
            type: 0,
            count: 0,
            money: 0,
        }

        $scope.selectTab = function (num) {
            $scope.tab = num;
            model.changeType(num);
        }

        $scope.charge = function () {
            if ($scope.chargeNum == '') {
                PageService.showConfirmDialog('请输入要充值的金额!');
                return;
            } else {
                var chargeNum = parseInt($scope.chargeNum * 100, 10);
            }

            memberInfo.type = 4;
            memberInfo.money = chargeNum;
            PayService.pay(memberInfo, function () {
                $scope.isChargeDialogShow = false;
            });
        }

        $scope.setSMS = function (item) {
            angular.copy(item, $scope.sms);
            $scope.isSMSDetailDialogShow = true;
        }

        $scope.quit = function () {
            PageService.closeDialog();
        }

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 27 && model.dialogId == PageService.dialogList.length) {
                if (!PageService.isConfirmDialogShow && !$scope.isChargeDialogShow && !$scope.isSMSDetailDialogShow) {
                    $scope.$apply(function () {
                        $scope.quit();
                    });
                } else if ($scope.isChargeDialogShow) {
                    $scope.isChargeDialogShow = false;
                } else if ($scope.isSMSDetailDialogShow) {
                    $scope.isSMSDetailDialogShow = false;
                }
            }

            if (domEvent.keyCode == 13 && model.dialogId == PageService.dialogList.length) {
                if (!PageService.isConfirmDialogShow && !$scope.isChargeDialogShow && !$scope.isSMSDetailDialogShow) {
                    $scope.$apply(function () {
                        $scope.quit();
                    });
                } else if ($scope.isChargeDialogShow) {
                    $scope.charge();
                } else if ($scope.isSMSDetailDialogShow) {
                    $scope.isSMSDetailDialogShow = false;
                }
            }
        });

        $scope.$on('$destroy', function () {
            keydownHandle();
        });

    }
]);