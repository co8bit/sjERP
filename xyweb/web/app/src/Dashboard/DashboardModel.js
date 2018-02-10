//xxxxModel or Service or Class
'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('DashboardModel', ['EventService', '$log', 'PageService', 'QueryService','UserService', 'AuthGroupService', '$location',
    function(EventService, $log, PageService, QueryService, UserService, AuthGroupService, $location) {

        // $log.debug("DashboardModel")

        var model = {};

        // model.flag_controllerDraw = 0;
        // model.flag_domIsReady = 0;
        model.date = [];
        model.data = {};
        model.exp = 0;
        model.inc = 0;
        model.invoice = 0;
        model.money_pit = 0;
        model.pageState = {
            activeTab: 1,
        }
        model.IsYqjShow = model.IsYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);
        // 设置用于30日销售趋势图的X轴日期数组
        function setDateArr() {
            model.date = [];
            var today = new Date();
            // today = today.setDate(today.getDate() + 1);
            var timeMs = new Date(today).getTime();

            var dateArr = [];
            for (var i = 30; i >= 1; i--) {
                var tmpDate = new Date(timeMs);
                var monthNum = tmpDate.getMonth() + 1;
                var dateNum = tmpDate.getDate();
                var timeStr = monthNum.toString() + '.' + dateNum.toString();
                dateArr.push(timeStr);
                timeMs -= 24 * 3600 * 1000;
            }

            dateArr = dateArr.reverse();
            angular.copy(dateArr, model.date);
        }

        model.draw = function() {
            var salesChartDiv = document.getElementById('sales-chart');
            var grossProfitChartDiv = document.getElementById('gross-profit-chart');

            if (!salesChartDiv)
                return;

            var resizeChart = function () {
                salesChartDiv.style.width = window.innerWidth - 200 + 'px';
                salesChartDiv.style.height = '300px';
                grossProfitChartDiv.style.width = window.innerWidth - 200 + 'px';
                grossProfitChartDiv.style.height = '300px';
            };
            resizeChart();

            var salesChart = echarts.init(salesChartDiv);
            var grossProfitChart = echarts.init(grossProfitChartDiv);

            // 指定图表的配置项和数据
            var salesOption = {
                title: {
                    // text: '最近7日销售额',
                },
                grid: {
                    x:120
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['销售额']
                },
                xAxis: {
                    // type: 'category',
                    boundaryGap: false,
                    axisLabel: {
                        show:true,
                        rotate:45,
                        interval: 0,
                    },
                    data: model.date,
                },
                yAxis: {
                    position:'left',
                    boundaryGap:[0,'20%'],
                    axisLabel:{
                        margin:'5',
                    }
                },
                series: [{
                    name: '销售额',
                    type: 'line',
                    smooth: true,
                    symbol: 'none',
                    itemStyle: {
                        normal: {
                            color: 'rgb(255, 70, 131)'
                        }
                    },
                    areaStyle: {
                        normal: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                offset: 0,
                                color: 'rgb(255, 158, 68)'
                            }, {
                                offset: 1,
                                color: 'rgb(255, 70, 131)'
                            }])
                        }
                    },
                    data: model.data.saleTop,
                }]
            };
            var grossProfitOption = {
                title: {
                    // text: '最近7日销售额',
                },
                grid: {
                    x:120
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['毛利润']
                },
                xAxis: {
                    // type: 'category',
                    boundaryGap: false,
                    axisLabel: {
                        show:true,
                        rotate:45,
                        interval: 0,
                    },
                    data: model.date,
                },
                yAxis: {
                    position:'left',
                    boundaryGap:[0,'20%'],
                    axisLabel:{
                        margin:'5',
                    }
                },
                series: [{
                    name: '毛利润',
                    type: 'line',
                    smooth: true,
                    symbol: 'none',
                    itemStyle: {
                        normal: {
                            color: 'rgb(255, 70, 131)'
                        }
                    },
                    areaStyle: {
                        normal: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                offset: 0,
                                color: 'rgb(255, 158, 68)'
                            }, {
                                offset: 1,
                                color: 'rgb(255, 70, 131)'
                            }])
                        }
                    },
                    data: model.data.gross_profit,
                }]
            };

            var expOption = {
                title: {
                    // text: '最近7日销售额',
                },
                grid: {
                    x:120
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['支出']
                },
                xAxis: {
                    // type: 'category',
                    boundaryGap: false,
                    axisLabel: {
                        show:true,
                        rotate:45,
                        interval: 0,
                    },
                    data: model.timeArr,
                },
                yAxis: {
                    position:'left',
                    boundaryGap:[0,'20%'],
                    axisLabel:{
                        margin:'5',
                    }
                },
                series: [{
                    name: '支出',
                    type: 'line',
                    smooth: true,
                    symbol: 'none',
                    itemStyle: {
                        normal: {
                            color: 'rgb(255, 70, 131)'
                        }
                    },
                    areaStyle: {
                        normal: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                offset: 0,
                                color: 'rgb(255, 158, 68)'
                            }, {
                                offset: 1,
                                color: 'rgb(255, 70, 131)'
                            }])
                        }
                    },
                    data: model.expPriceArr,
                }]
            }

            var incOption = {
                title: {
                    // text: '最近7日销售额',
                },
                grid: {
                    x:120
                },
                tooltip: {
                    trigger: 'axis'
                },
                legend: {
                    data: ['收入']
                },
                xAxis: {
                    // type: 'category',
                    boundaryGap: false,
                    axisLabel: {
                        show:true,
                        rotate:45,
                        interval: 0,
                    },
                    data: model.timeArr,
                },
                yAxis: {
                    position:'left',
                    boundaryGap:[0,'20%'],
                    axisLabel:{
                        margin:'5',
                    }
                },
                series: [{
                    name: '收入',
                    type: 'line',
                    smooth: true,
                    symbol: 'none',
                    itemStyle: {
                        normal: {
                            color: 'rgb(255, 70, 131)'
                        }
                    },
                    areaStyle: {
                        normal: {
                            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                offset: 0,
                                color: 'rgb(255, 158, 68)'
                            }, {
                                offset: 1,
                                color: 'rgb(255, 70, 131)'
                            }])
                        }
                    },
                    data: model.incPriceArr,
                }]
            }

            // 使用刚指定的配置项和数据显示图表。
            if(model.IsYqjShow){
                salesChart.setOption(expOption)
                grossProfitChart.setOption(incOption)
            }else{
                salesChart.setOption(salesOption);
                grossProfitChart.setOption(grossProfitOption);
            }
            window.onresize = function () {
                resizeChart();
                salesChart.resize();
                grossProfitChart.resize();
            };
        }

        // 处理查看仪表盘的数据请求
        model.dealViewDashboard = function() {
            if (UserService.getLoginStatus().status > 0) {
                QueryService.query.dashboard(function(data) {
                    angular.copy(data, model.data)
                    if(model.IsYqjShow){
                        model.expPriceArr = [];
                        model.incPriceArr = [];
                        model.timeArr = [];
                        for(var v of model.data.exp){
                            model.expPriceArr.push(v.price)
                            model.timeArr.push(v.time)
                        }
                        for(var v of model.data.inc){
                            model.incPriceArr.push(v.price)
                        }
                        model.exp = data.mon.exp;
                        model.inc = data.mon.inc;
                        model.money_pit = data.mon.money_pit;
                        model.invoice = data.mon.invoice;
                    }else{
                        setDateArr();
                        model.data.saleTop = model.data.saleTop.reverse();
                        model.data.gross_profit = model.data.gross_profit.reverse();
                    }

                    model.draw();
                });
            }
        }


        // 响应查看仪表盘事件
        EventService.on(EventService.ev.START_VIEW_DASHBOARD, function() {
            model.IsYqjShow = AuthGroupService.getIsShow(AuthGroupService.module.IsYqjShow);
            if($location.path == '/Dashboard'){
                // 登陆跳入主页
                PageService.setNgViewPage('NullPage');
                PageService.setNgViewPage('Dashboard');
            }else{
                PageService.setNgViewPage('Dashboard');
            }
        });

        return model; // or return model

    }
]);
