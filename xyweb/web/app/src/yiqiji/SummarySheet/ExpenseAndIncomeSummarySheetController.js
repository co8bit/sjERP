'use strict'

xy.controller('ExpenseAndIncomeSummarySheetController', ['$scope', '$log', 'ExpenseAndIncomeSummarySheetModel', 'PageService',
	function($scope, $log, model, PageService) {
		$scope.model = model;
		$scope.isAllChart = true;
		$scope.exp = model.exp;
		$scope.inc = model.inc;
		$scope.allExp = model.allExp;
		$scope.allInc = model.allInc;
		$scope.initAllExp = function (){
			$scope.allExpPriceArr = [];
			$scope.allExpTimeArr = [];
			for (var v of $scope.allExp) {
				$scope.allExpPriceArr.push(v.price)
				$scope.allExpTimeArr.push(v.time)
			}
		}
		$scope.initAllInc = function (){
			$scope.allIncPriceArr = [];
			$scope.allIncTimeArr = [];
			for (var v of $scope.allInc) {
				$scope.allIncPriceArr.push(v.price)
				$scope.allIncTimeArr.push(v.time)
			}
		}
		/**
		 * 支出柱状图数据整理
		 */
		$scope.initEXP = function (){
			$scope.expCatArr = [];//类别数组
			$scope.expPriceArr = [];//金额数组
			$scope.expTimeArr = [];//时间数组
			if($scope.exp.length == 0){
				return
			}
			for (var i in $scope.exp) {
				$scope.expCatArr.push(String($scope.exp[i].cat_name))
				var priceTemp = [];
				for(var v of $scope.exp[i].pricedata){
					if(v.price == 0){
						priceTemp.push('');
					}else{
						priceTemp.push(v.price);
					}
				}
				$scope.expPriceArr[i] = [];
				angular.copy(priceTemp, $scope.expPriceArr[i]);
				if($scope.expTimeArr.length === 0){
					for (var v of $scope.exp[i].pricedata) {
						$scope.expTimeArr.push(v.time)
					}
				}
			}
			$scope.expSeries = [];
			for (var i in $scope.expCatArr) {
				var seriesItem = {
					name : $scope.expCatArr[i],
					type: 'bar',
					stack: '总量',
					label: {
						normal: {
							show: true,
							position: 'insideRight'
						}
					},
					barWidth: '30px',
					data: $scope.expPriceArr[i],
				};
				$scope.expSeries.push(seriesItem);
			}
		}
		/**
		 * 收入柱状图数据整理
		 */
		$scope.initINC = function (){
			$scope.incCatArr = [];//类别数组
			$scope.incPriceArr = [];//金额数组
			$scope.incTimeArr = [];//时间数组
			if($scope.inc.length == 0){
				return
			}
			for (var i in $scope.inc) {
				$scope.incCatArr.push(String($scope.inc[i].cat_name))
				var priceTemp = [];
				for(var v of $scope.inc[i].pricedata){
					if(v.price == 0){
						priceTemp.push('');
					}else{
						priceTemp.push(v.price);
					}
				}
				$scope.incPriceArr[i] = [];
				angular.copy(priceTemp, $scope.incPriceArr[i])
				if($scope.incTimeArr.length === 0){
					for (var v of $scope.inc[i].pricedata) {
						$scope.incTimeArr.push(v.time)
					}
				}
			}
			$scope.incSeries = [];
			for (var i in $scope.incCatArr) {
				var seriesItem = {
					name : $scope.incCatArr[i],
					type: 'bar',
					stack: '总量',
					label: {
						normal: {
							show: true,
							position: 'insideRight'
						}
					},
					data: $scope.incPriceArr[i],
				};
				$scope.incSeries.push(seriesItem);
			}
		}
		$scope.initAllExp();//刷新支出总数据
		$scope.initAllInc();//刷新收入总数据
		$scope.initEXP();//刷新支出数据
		$scope.initINC();//刷新收入数据

		var chart1, chart2, chart3, chart4, chart5, chart6, showChart1, showChart2, showChart3;
		var option1, option2, option3, option4, option5, option6;
		var showChart;
		$scope.draw = function(obj) {
			chart1 = echarts.init(document.getElementById('chart1'));
			chart2 = echarts.init(document.getElementById('chart2'));
			chart3 = echarts.init(document.getElementById('chart3'));
			chart4 = echarts.init(document.getElementById('chart4'));
			chart5 = echarts.init(document.getElementById('chart5'));
			chart6 = echarts.init(document.getElementById('chart6'));
			$scope.setOption();
			// 使用刚指定的配置项和数据显示图表。
			chart1.setOption(option1);
			chart2.setOption(option2);
			chart3.setOption(option3);
			chart4.setOption(option4);
			chart5.setOption(option5);
			chart6.setOption(option6);
		}
		$scope.setOption = function (){
			option1 = {
				//option1 总汇总表支出
				title: {
					text: '总支出趋势图',
					textAlign: 'left',
					left: '40%',
				},
				tooltip: {
					trigger: 'axis',
				},
				//图例
				legend: {
					//图例位置
					top: '30px',
					right: '15%',
					//图例布局朝向 'horizontal'：横向   ，，'vertical':纵向
					orient: 'vertical',
					//绑定线条   标记类型包括  圆形'circle', 方形'rect', 圆角方形'roundRect', 三角形'triangle', 菱形'diamond', 'pin', 'arrow'
					data: [{
							name: '支出走势',
							// 强制设置图形为方形
							icon: 'roundRect',
						}
					]
				},
				xAxis: {
					// type: 'category',
					boundaryGap: false,
					axisLabel: {
						show:true,
                        rotate:65,
                        interval: 0,
					},
					data: $scope.allExpTimeArr,
				},
				yAxis: {
					// type: 'value',
					axisLabel: {
						margin: '5',
					}
				},
				series: [{
					name: '支出走势',
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
					data: $scope.allExpPriceArr,
				}]
			};
			option2 = {
				//option1 总汇总表支出
				title: {
					text: '总收入趋势图',
					textAlign: 'left',
					left: '40%',
				},
				tooltip: {
					trigger: 'axis',
				},
				//图例
				legend: {
					//图例位置
					top: '30px',
					right: '15%',
					//图例布局朝向 'horizontal'：横向   ，，'vertical':纵向
					orient: 'vertical',
					//绑定线条   标记类型包括  圆形'circle', 方形'rect', 圆角方形'roundRect', 三角形'triangle', 菱形'diamond', 'pin', 'arrow'
					data: [{
							name: '收入走势',
							// 强制设置图形为方形
							icon: 'roundRect',
						}
					]
				},
				xAxis: {
					type: 'category',
					boundaryGap: false,
					axisLabel: {
						show:true,
                        rotate:65,
                        interval: 0,
					},
					data: $scope.allIncTimeArr,
				},
				yAxis: {
					type: 'value',
					boundaryGap: [0, '20%'],
					axisLabel: {
						margin: '5',
					}
				},
				series: [{
					name: '收入走势',
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
					data: $scope.allIncPriceArr,
				}]
			};
			option3 = {
				title: {
					text: '分类支出对比统计图',
					textAlign: 'left',
					left: '40%',
				},
				tooltip: {
					trigger: 'axis',
					axisPointer: { // 坐标轴指示器，坐标轴触发有效
						type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
					}
				},
				legend: {
					data: $scope.expCatArr,
					left: '80px',
					top: '30px',
					width: '100%',
				},
				grid: {
					left: '3%',
					right: '4%',
					bottom: '3%',
					containLabel: true
				},
				xAxis: {
					type: 'value',
				},
				yAxis: {
					type: 'category',
					axisLabel: {
						show:true,
                        interval: 0,
					},
					data: $scope.expTimeArr,
				},
				series: $scope.expSeries,
			};
			option4 = {
				title: {
					text: '分类收入对比统计图',
					textAlign: 'left',
					left: '40%',
				},
				tooltip: {
					trigger: 'axis',
					axisPointer: { // 坐标轴指示器，坐标轴触发有效
						type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
					}
				},
				legend: {
					data: $scope.incCatArr,
					left: '80px',
					top: '30px',
				},
				grid: {
					left: '3%',
					right: '4%',
					bottom: '3%',
					containLabel: true
				},
				xAxis: {
					type: 'value',
				},
				yAxis: {
					type: 'category',
					data: $scope.incTimeArr,
					axisLabel: {
						show:true,
                        interval: 0,
					},
				},
				series: $scope.incSeries,
			};
			option5 = {
				title : {
			        text: '分类支出占比图',
			        x:'center'
			    },
			    tooltip : {
			        trigger: 'item',
			        formatter: "{a} <br/>{b} : {c} ({d}%)"
			    },
			    legend: {
			        orient: 'vertical',
			        left: 'left',
			        data: model.chartExpCat,
			    },
			    series : [
			        {
			            name: '访问来源',
			            type: 'pie',
			            radius : '55%',
			            center: ['50%', '60%'],
			            data:  model.chartExp,
			            itemStyle: {
			                emphasis: {
			                    shadowBlur: 10,
			                    shadowOffsetX: 0,
			                    shadowColor: 'rgba(0, 0, 0, 0.5)'
			                }
			            }
			        }
			    ]

			};
			option6 = {
				title : {
			        text: '分类收入占比图',
			        x:'center'
			    },
			    tooltip : {
			        trigger: 'item',
			        formatter: "{a} <br/>{b} : {c} ({d}%)"
			    },
			    legend: {
			        orient: 'vertical',
			        left: 'left',
			        data: model.chartIncCat,
			    },
			    series : [
			        {
			            name: '访问来源',
			            type: 'pie',
			            radius : '55%',
			            center: ['50%', '60%'],
			            data:  model.chartInc,
			            itemStyle: {
			                emphasis: {
			                    shadowBlur: 10,
			                    shadowOffsetX: 0,
			                    shadowColor: 'rgba(0, 0, 0, 0.5)'
			                }
			            }
			        }
			    ]

			};
		}
		function reDraw(){
			setTimeout(function(){
				if($scope.nowIndex){
					showChart1.resize();
					showChart2.resize();
					showChart3.resize();
				}else{
					chart1.resize();
					chart2.resize();
					chart3.resize();
					chart4.resize();
					chart5.resize();
					chart6.resize();
				}
			},100)
		}
		/**
		 * 改变窗口大小刷新图标
		 * @return {[type]} [description]
		 */
		window.onresize = function(){
			reDraw();
		}
		$scope.clickChart = function(index) {
			$scope.isAllChart = false;
			$scope.nowIndex = index;
			switch(index) {
				case 1:
					showChart1.setOption(option1)
					break;
				case 2:
					showChart1.setOption(option2)
					break;
				case 3:
					showChart2.setOption(option3)
					break;
				case 4:
					showChart2.setOption(option4)
					break;
				case 5:
					showChart3.setOption(option5)
					break;
				case 6:
					showChart3.setOption(option6)
					break;
				default:
					break;
			}
			reDraw();
		}
		/**
		 * 大图返回按钮 隐藏大图
		 * @return {[type]} [description]
		 */
		$scope.back = function() {
			$scope.isAllChart = true;
			$scope.nowIndex = '';
			reDraw();
		}

		$(function () {
			showChart1 = echarts.init(document.getElementById("showChart1"));
			showChart2 = echarts.init(document.getElementById("showChart2"));
			showChart3 = echarts.init(document.getElementById("showChart3"));
			$scope.draw();
            var time = new Date(model.time.reg_end_time * 1000);
            var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
            var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
            $('#reportrange span').html(time.getFullYear() + '-' + month + '-' + '01' + ' 至  ' + time.getFullYear() + '-' + month + '-' + day);
            $('#reportrange').daterangepicker({
                startDate: moment().subtract('days',3).startOf("month"),
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
                    '所有': [moment().subtract(16,"month").startOf("month"), moment()],
                    '本周':[moment().subtract('week',0).startOf('week'),moment()],
                    '最近7日': [moment().subtract('days', 6), moment()],
                    '上周':[moment().subtract('week',1).startOf('week'),moment().subtract('week',1).endOf('week')],
                    '本月': [moment().startOf("month"), moment().endOf("month")],
                    '最近30日': [moment().subtract('days', 29), moment()],
                    '上个月': [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
                    '今年': [moment().subtract(0, "year").startOf("year"), moment()],
                    '去年': [moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").endOf("year")],
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
            	var timeDate = {
	            	reg_st_time: new Date(start).getTime() / 1000,
					reg_end_time: new Date(end).getTime() / 1000,
            	}
                model.getSummarySheet(timeDate).then(function(){
	                $scope.initAllExp();//刷新支出总数据
					$scope.initAllInc();//刷新收入总数据
					$scope.initEXP();//刷新支出数据
					$scope.initINC();//刷新收入数据
					$scope.setOption();
					$scope.clickChart($scope.nowIndex);
					if(model.chartExpIsShow && $scope.nowIndex == 5 || model.chartIncIsShow && $scope.nowIndex == 6){
						PageService.showConfirmDialog('该时间段内没有数据，请重新选择时间',['确认'])
					}
                })
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
            });
            $('#daterangepicker').addClass('show-calendar');
            $('#daterangepicker').css('display','none');

        })
	}
])