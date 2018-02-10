//xxxxController
'use strict'

angular.module('XY').controller('ViewOrderController', ['EventService', '$scope', '$log', 'PageService', 'ViewOrderModel', 'MiscService', '$filter', 'PrintService','UserService',
    function (EventService, $scope, $log, PageService, model, MiscService, $filter,PrintService,UserService) {
        $scope.PageService = PageService;
        $scope.EventService = EventService;
        $scope.rpg = UserService.getLoginStatus().rpg;
        $scope.dateIsShow = false;
        $scope.isAll = true; //
        $scope.IsYqjShow = model.IsYqjShow;
        //把信息存到cookie里，创建event来决定是否加载。
        // 使用jqUI初始化时间空间
        $(function () {
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
            $('#daterangepicker').addClass('show-calendar');
            $('#daterangepicker').css('display','none');
        });

        //把model里的东西映射过来
        $scope.model = model; //映射model
        // 映射查询管理对象和它的一些成员
        $scope.query = model.query;
        $scope.data = model.query.getData();
        $scope.paginator = model.query.getPaginator();
        $scope.timeFilter = model.query.getTimeFilter();
        $scope.classCheckbox = model.query.getClassCheckbox();
        $scope.statusCheckbox = model.query.getStatusCheckbox();
        $scope.financeTypeCheckbox = model.query.getFinanceTypeCheckbox();
        $scope.bigClassCheckbox = model.query.getBigClassCheckbox();
        $scope.isMobile = MiscService.testMobile();

        function resizeInit(){
            if ($(window).width() <= 1024) {
                $scope.isMinShow = false;
                var cashTag = false;
                var bankTag = false;
                var online_payTag = false;
                for (var i in $scope.financeTypeCheckbox.filterData) {
                   switch ($scope.financeTypeCheckbox.filterData[i]){
                     case 101:
                         $scope.isInCashShow = true;
                         $scope.isInBankShow = false;
                         $scope.isInOnlinePayShow = false;
                         cashTag = true;
                         bankTag = false;
                         online_payTag = false;
                         break;
                     case 102:
                         $scope.isInBankShow = false;
                         $scope.isInBankShow = true;
                         $scope.isInOnlinePayShow = false;
                         cashTag = false;
                         bankTag = true;
                         online_payTag = false;
                         break;
                    case 103:
                         $scope.isInBankShow = false;
                         $scope.isInBankShow = false;
                         $scope.isInOnlinePayShow = true;
                         cashTag = false;
                         bankTag = false;
                         online_payTag = true;
                         break;
                    }
                    $scope.isInFinanceMode = true;
               }

               if (!cashTag)
                 $scope.isInCashShow = false;
               if (!bankTag)
                 $scope.isInBankShow = false;
               if (!online_payTag)
                $scope.isInOnlinePayShow = false;

            }else{
                $scope.isMinShow = true;
                $scope.isInCashShow = true;
                $scope.isInBankShow = true;
                $scope.isInOnlinePayShow = true;
            }

        }
        resizeInit();

        window.onresize = function(){
            resizeInit();
        }

		$scope.modeInit=function(){
	        $scope.isInFinanceMode = false;
	        $scope.isInCashShow = false;
	        $scope.isInBankShow = false;
	        $scope.isInOnlinePayShow = false;
		}
        $scope.modeInit();
        switch (model.financeMode){
            case 'cash':
                $scope.financeTypeCheckbox.select(($scope.financeTypeCheckbox.option)[0]);
                break;
            case 'bank':
                $scope.financeTypeCheckbox.select(($scope.financeTypeCheckbox.option)[1]);
                break;
            case 'online':
                $scope.financeTypeCheckbox.select(($scope.financeTypeCheckbox.option)[2]);
                break;
        }
        EventService.on(EventService.ev.CHANGE_CHECKBOX,function(event,arg){
            if ($scope.bigClassCheckbox.filterData.length > 0 || $scope.financeTypeCheckbox.filterData.length > 0){
     //        	var cashTag = false;
     //        	var bankTag = false;
     //        	var online_payTag = false;
     //            for (var i of $scope.financeTypeCheckbox.filterData) {
     //                switch (i){
     //                	case 101:
     //                		$scope.isInCashShow = true;
     //                		cashTag = true;
     //                		break;
     //                	case 102:
     //                		$scope.isInBankShow = true;
     //                		bankTag = true;
     //                		break;
     //               		case 103:
     //                		$scope.isInOnlinePayShow = true;
     //                		online_payTag = true;
     //                		break;
     //                }
					// $scope.isInFinanceMode = true;
     //            }

     //            if (!cashTag)
     //            	$scope.isInCashShow = false;
     //            if (!bankTag)
     //            	$scope.isInBankShow = false;
     //            if (!online_payTag)
     //             $scope.isInOnlinePayShow =false;
                    resizeInit();
                    $scope.isInFinanceMode = true;
            }else{
                $scope.modeInit();
                $scope.isInFinanceMode= false;
            }
            if ($scope.classCheckbox.selectedId ==8 ) {
            	$scope.modeInit();
            }
        });


        // 页面状态
        $scope.pageState = model.pageState;

        // 快速搜索输入内容
        $scope.queryInfo = {
            query: '',
        }

        // 筛选条件
        $scope.predicate = {
            oneMatch: {},
            allMatch: {},
        };

        // 筛选过后的列表
        $scope.displayedOrderInfo = [];

        $scope.classCheckbox.selectAll(1);
        // $log.log('isReview',model.isReview);
        $scope.checkClassCheckbox = function (type){
            // $log.error(type)
            // $scope.bigClassCheckbox.selectAll();
            if (type == 1) {
                $scope.isAll = true;
                $scope.classCheckbox.selectAll(1);
            }else{
                $scope.isAll = false;
                $scope.classCheckbox.selectSingle(type);
            }
        }
        if(model.isReview){//复原离开当前页时显示的信息
            // setTimeout(function() {//等待页面加载完成
                // $scope.$apply(function(){
                    $log.log('cookie',model.cookie);

                    for(var item in model.cookie.classCheckbox){
                        if(model.cookie.classCheckbox[item].flag_chosen){
                            $scope.classCheckbox.flag_none=false;
                        }
                    }


                    for(var item in model.cookie.statusCheckbox){
                        if(model.cookie.statusCheckbox[item].flag_chosen){
                            $scope.statusCheckbox.flag_none=false;
                        }
                    }


                    for(var item in model.cookie.financeTypeCheckbox){
                        if(model.cookie.financeTypeCheckbox[item].flag_chosen){
                            $scope.financeTypeCheckbox.flag_none=false;
                        }
                    }


                    for(var item in model.cookie.bigClassCheckbox){
                        if(model.cookie.bigClassCheckbox[item].flag_chosen){
                            $scope.bigClassCheckbox.flag_none=false;
                        }
                    }



                    // if(model.cookie.st_time){
                    //     $scope.query.setStartTime(model.cookie.st_time);
                    //     $scope.query.request();
                    // }
                    // if(model.cookie.end_time){
                    //     $scope.query.setStartTime(model.cookie.end_time);
                    //     $scope.query.request();
                    // }
                    // // $scope.timeFilter = model.cookie.time;//时间
                    // // $log.log('time',model.cookie.time);

                    $scope.statusCheckbox.option = model.cookie.statusCheckbox;//状态
                    $scope.financeTypeCheckbox.option = model.cookie.financeTypeCheckbox;//付款方式
                    $scope.bigClassCheckbox.option = model.cookie.bigClassCheckbox;//类目
                    $scope.classCheckbox.option = model.cookie.classCheckbox;//订单分类

                    $scope.query.setStartTime(model.cookie.st_time);
                    $scope.query.setEndTime(model.cookie.end_time);
                    $scope.classCheckbox.generateFilterData();
                    $scope.statusCheckbox.generateFilterData();
                    $scope.financeTypeCheckbox.generateFilterData();
                    $scope.bigClassCheckbox.generateFilterData();
                    // $scope.query.setPage(model.cookie.pageNow);
                    $scope.predicate = model.cookie.predicate;
                    $scope.queryInfo.query = model.cookie.query;//查询


                // });

                setTimeout(function() {//需要等待查询条件装载完毕。
                    $scope.$apply(function(){
                        var pageitem= {
                            content: model.cookie.pageNow,
                        }
                        $log.log('item',pageitem);
                        $scope.paginator.clickButton(pageitem);
                    });
                },300);

                model.isReview = false;
            // }, 10);



            // //复原离开前click事件
            // for(var item in $scope.classCheckbox.option){
            //     if(item.flag_chosen){
            //         $scope.classCheckbox.selectSingle(item);
            //     }
            // }
            // for(var item in $scope.statusCheckbox.option){
            //     if(item.flag_chosen){
            //         $scope.statusCheckbox.select(item);
            //     }
            // }
            // for(var item in $scope.financeTypeCheckbox.option){
            //     if(item.flag_chosen){
            //         $scope.financeTypeCheckbox.select(item);
            //     }
            // }
            // for(var item in $scope.bigClassCheckbox.option){
            //     if(item.flag_chosen){
            //         $scope.bigClassCheckbox.select(item);
            //     }
            // }
            // model.isReview = false;
        }

        // 查看订单详情
        $scope.viewDetails = function (item) {
            if (item.class == 81){
                EventService.emit(EventService.ev.START_VIEW_INCOME_DETAIL,item);
            }if (item.class == 82){
                EventService.emit(EventService.ev.START_VIEW_EXPEND_DETAIL,item);
            }else if(item.class == 83){
                //提现
                EventService.emit(EventService.ev.START_VIEW_WITHDRAW_DETAIL,item);
            }else if(item.class == 84){
                //转账
                EventService.emit(EventService.ev.START_VIEW_TRANSFER_ACCOUNT_DETAIL,item);
            }else if(item.class == 53){
                EventService.emit(EventService.ev.STOCK_TAKING_VIEW_DETAILS,item)
            }else if(item.class == 54){
                EventService.emit(EventService.ev.REQUISITION_VIEW_DETAILS,item)
            }
            else{
                //开始访问详情
                MiscService.sendViewDetailsEvent(item);

                var args = {
                    orderPage: $scope.model.orderPage,
                };
                EventService.emit(EventService.ev.ORDER_ARGS, args);
                // $scope.isInFinanceMode = false;
            }
        }



        $scope.$watch('queryInfo.query', function (newValue, oldValue) {
            $scope.predicate.oneMatch.sn = newValue;
            $scope.predicate.oneMatch.cid_name = newValue;
            $scope.predicate.oneMatch.status_show = newValue;
            $scope.predicate.oneMatch.operator_name = newValue;

        }, true);

        var twoPhaseFilter = $filter('twoPhaseFilter');

        // 检测筛选条件
        $scope.$watch('predicate', function () {
            // $log.log('$scope.model.skuInfo',$scope.model.skuInfo);
            $scope.displayedOrderInfo = twoPhaseFilter($scope.data, $scope.predicate);
        }, true);

        // 监测data源数据
        $scope.$watch('data', function () {
            $scope.displayedOrderInfo = twoPhaseFilter($scope.data, $scope.predicate);
        }, true);

        $scope.$watch('classCheckbox.flag_none',function(){
            if($scope.classCheckbox.flag_none==true) $scope.classCheckbox.selectAll(1);;
        },true);

        EventService.on(EventService.ev.VIEW_BACK_TO_ORDER,function(){
            $scope.data = model.query.getData();
            $scope.displayedOrderInfo = twoPhaseFilter($scope.data, $scope.predicate);
            var pageItem = {
                content: $scope.paginator.now_page,
            };
            $scope.paginator.clickButton(pageItem);

        });




        $scope.checkDetails = function (item) {

            // //首先存下当前的cookie信息: 订单分类、查询和predicate、时间、状态*3、分页
            model.cookie.classCheckbox = $scope.classCheckbox.option;
            model.cookie.predicate = $scope.predicate;
            model.cookie.query = $scope.queryInfo.query;//查询
            model.cookie.st_time = $scope.timeFilter.reg_st_time;
            model.cookie.end_time = $scope.timeFilter.reg_end_time;//时间选项
            model.cookie.statusCheckbox = $scope.statusCheckbox.option;//存下各个Option.selected
            model.cookie.financeTypeCheckbox = $scope.financeTypeCheckbox.option;
            model.cookie.bigClassCheckbox = $scope.bigClassCheckbox.option;
            model.cookie.pageNow = $scope.paginator.now_page;
            model.isReview = true;//预设所有的访问都是再次访问
            $log.log('cookieTosave',model.cookie);

            var args = {
                cid: item.cid,
                cstmrPage: 3,
                pageState: model.pageState.page,
                cookies:model.cookie,
            }
            EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS, args);
        }

        $scope.print = function (item) {
            $log.debug(PrintService);
            $log.debug(item);
            EventService.emit(EventService.ev.PRINT_ORDER,item.oid);

        }

    }
])