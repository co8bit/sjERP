'use strict'

angular.module('XY').controller('EverydaySummarySheetController', ['EventService', '$scope', '$log', 'PageService', 'SummarySheetModel','MiscService',
    function (EventService, $scope, $log, PageService, model,MiscService) {
        $scope.PageService = PageService;
        $scope.EventService = EventService;

        $scope.model = model;
        $scope.query = model.query;
        $scope.data = model.query.getData();
        $scope.paginator = model.query.getPaginator();
        $scope.isShow = true;
        $scope.timeFilter = model.query.getTimeFilter();
        $scope.isMobile = MiscService.testMobile();

        // 使用jqUI初始化时间空间
        $(function () {
        	//默认显示当天时间
            var date = new Date();
            var today = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
            $('#reportrange span').html(' 至 ');

            $('#reportrange').daterangepicker({
                startDate: moment().startOf('day'),
                endDate: moment(),
                // maxDate : moment(), //最大时间为现在
                // dateLimit : {
                //     days : 30
                // }, //开始和结束最大间隔天数
                parentEl: $(".time-container"),
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
                format: 'YYYY-MM-DD', //日期格式
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
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
            });
            $scope.$apply(function () {
                getIsShow();
            })
            $('#daterangepicker').addClass('show-calendar');
            $('#daterangepicker').css('display','none');
        });

        model.viewEverydaySheet();

        /**
         * 应收、实收、应付、实付的显示状态
         */
        function getIsShow() {
            $scope.isShow = $(window).width() < 1366 ? false : true; // 应收、实收、应付、实付是否显示
        }

        // 当浏览器窗口变化时重新获得显示状态
        $(window).resize(function () {
            $scope.$apply(function () {
                getIsShow();
            })
        });


    }
])