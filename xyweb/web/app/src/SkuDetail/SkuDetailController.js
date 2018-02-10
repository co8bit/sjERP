'use strict';

xy.controller('SkuDetailController', ['EventService', '$scope', '$log', 'PageService', 'SkuDetailModel', 'MiscService', '$filter', 'PrintService', 'StockService', 'NetworkService','UserService',
    function (EventService, $scope, $log, PageService, model, MiscService, $filter, PrintService, StockService, NetworkService,UserService) {
        // 把model里的东西映射过来
        $scope.model = model; //映射model
        // 映射查询管理对象和它的一些成员
        $scope.item = model.item;
        $scope.itemDetail = model.itemDetail;
        //把model里的东西映射过来
        $scope.model = model; //映射model
        // 映射查询管理对象和它的一些成员
        $scope.query = model.query;
        $scope.data = model.query.getData();
        $scope.realTime = model.query.getRealTimeData();
        $scope.paginator = model.query.getPaginator();
        $scope.timeFilter = model.query.getTimeFilter();
        $scope.classCheckbox = model.query.getClassCheckbox();
        $scope.statusCheckbox = model.query.getStatusCheckbox();
        $scope.isMobile = MiscService.testMobile();
        $scope.rpg =  UserService.getLoginStatus().rpg;
        // setInterval(function(){
        // 	$log.error('data',$scope.data)
        // },1000)

        var dataToSend = {
            sku_id: $scope.item.sku_id,
            sto_id: $scope.item.sto_id
        };
        NetworkService.request('query_skuChart', dataToSend, function (data) {
            $scope.chartData = [];
            angular.copy(data.data, $scope.chartData);
            $scope.draw();
        })

        $scope.backToStock = function () {
            history.back();
        };
        // 使用jqUI初始化时间空间
        $(function () {
            $('#reportrange span').html('' + ' 至 ' + '');
            $('#reportrange').daterangepicker({
                startDate: moment().subtract('days', 3).startOf("month"),
//					showDropdowns:true,
                // endDate: moment(),
                // maxDate : moment(), //最大时间
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
                    '所有': [moment().subtract(16, "month").startOf("month"), moment()],
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
                format: 'YYYY-MM-DD', //日期格式 YYYY-MM-DD HH:mm:ss
                separator: ' to ',

                locale: {
                    applyLabel: '确认',
                    cancelLabel: '取消',
                    fromLabel: '起始时间',
                    toLabel: '结束时间',
                    customRangeLabel: '自定义',
                    firstDay: 1,
                },
            }, function (start, end, label) { //选择日期后回调
                $scope.query.setStartTime(new Date(start).getTime()); // 设置查询起始时间
                $scope.query.setEndTime(new Date(end).getTime()); //设置查询结束时间
                $scope.query.request(); //开始查询
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
            });
            $('#daterangepicker').addClass('show-calendar');
            $('#daterangepicker').css('display', 'none');
        })

        $scope.draw = function (obj) {
            var salesChartDiv1 = document.getElementById('sales-chart');
            var salesChart = echarts.init(salesChartDiv1);
            var salesoption = {
                title: {
                    text: ''
                },
                tooltip: {
                    trigger: 'axis'
                },
                //图例
                legend: {
                    //图例位置
                    right: '15%',
                    //图例布局朝向 'horizontal'：横向   ，，'vertical':纵向
                    orient: 'vertical',
                    //绑定线条   标记类型包括  圆形'circle', 方形'rect', 圆角方形'roundRect', 三角形'triangle', 菱形'diamond', 'pin', 'arrow'
                    data: [{
                        name: '销售走势',
                        // 强制设置图形为方形
                        icon: 'roundRect',
                    },
                        {
                            name: '采购走势',
                            icon: 'roundRect'
                        }
                    ]
                },
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: $scope.chartData.time,
                },
                yAxis: {
                    type: 'value',
                    boundaryGap: [0, '20%'],
                    axisLabel: {
                        margin: '5',
                    }
                },
                series: [{
                    name: '销售走势',
                    type: 'line',
                    //是否平滑显示
                    smooth: 'true',
                    symbol: 'none',
                    itemStyle: {
                        normal: {
                            color: '#36bc9b'
                        }
                    },
                    lineStyle: {
                        normal: {
                            color: '#36bc9b'
                        }
                    },
                    //动态数据放入data
                    data: $scope.chartData.saleCount,
                },
                    {
                        name: '采购走势',
                        type: 'line',
                        barMaxWidth: '3',
                        //是否平滑显示
                        smooth: 'true',
                        symbol: 'none',
                        itemStyle: {
                            normal: {
                                color: '#ff6633'
                            }
                        },
                        lineStyle: {
                            normal: {
                                color: '#ff6633'
                            }
                        },
                        //动态数据放入data
                        data: $scope.chartData.buyCount,
                        //					data: [[28,200], [27,80000], [26,55000], [25,20000], [24,65945],[23,75000], [22,20000], [21,78945],[20,46233]]
                    }
                ]
            };
            // 使用刚指定的配置项和数据显示图表。
            salesChart.setOption(salesoption);
            window.onresize = function () {
                salesChart.resize();
            }
        }
        $scope.editSPU = function (spu_id) {
            $log.error(spu_id);
            EventService.emit(EventService.ev.START_CREATE_EDIT_SPU, spu_id)
        }

        $scope.viewDetails = function (item) {
            if (item.class >= 1 && item.class <= 4) {
                var oid = item.oid;
                EventService.emit(EventService.ev.ORDER_VIEW_DETAILS, oid)
            }
            if(item.class == 53){
                EventService.emit(EventService.ev.STOCK_TAKING_VIEW_DETAILS,item.wid)
            }
            if(item.class == 54){
                EventService.emit(EventService.ev.REQUISITION_VIEW_DETAILS,item.wid)
            }
        }

    }
])