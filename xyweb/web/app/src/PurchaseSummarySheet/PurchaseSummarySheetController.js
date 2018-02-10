'use strict'

angular.module('XY').controller('PurchaseSummarySheetController',['$rootScope', '$scope', '$log', '$timeout', '$cacheFactory', 'PageService', 'ConfigService', 'CstmrService',
    'GenService', 'OrderService', 'EventService','PurchaseSummarySheetModel','NetworkService','MiscService',
    function($rootScope, $scope, $log, $timeout, $cacheFactory, PageService, ConfigService, CstmrService,
             GenService, OrderService, EventService,model,NetworkService,MiscService){

        // var tmp_date_st = parseInt(new Date().getTime() /1000);
        // var tmp_date_end = parseInt(new Date().getTime() /1000);

        $scope.model = model;
        // $scope.pageList = model.localTable.getPageList();
        // $scope.isMobile = MiscService.testMobile();
        // $scope.order_amount = model.data.data.order_amount;


        //初始化选中项为按商品
        $scope.selectedItemID = 1;
        // //初始化日期
        // $scope.reg_st_time = new Date();
        // $scope.reg_end_time = new Date();
        // //初始化选项
        // $scope.option = [
        //     {option_name :'按商品', flag_chosen : 1},
        //     {option_name :'按供应商', flag_chosen : 0},
        //     {option_name :'按经办人', flag_chosen : 0},
        //     {option_name : '按仓库', flag_chosen: 0}
        // ];
        // tmp_date_st = parseInt(new Date(start).getTime() / 1000);
        // tmp_date_end = parseInt(new Date(end).getTime() / 1000);
        // var args = {
        //     type : $scope.selectedItemID,
        //     reg_st_time : tmp_date_st,
        //     reg_end_time : tmp_date_end,
        // };
        $(function(){
            EventService.emit(EventService.ev.START_VIEW_PURCHASE_SUMMARY_SHEET,args);
        });

        // $scope.setTimeNull = function(arg){
        //     if(arg == 1){
        //     $('#datepicker1').datepicker('setDate', null);
        //     tmp_date_st = parseInt(new Date().getTime() /1000);
        //     }else{
        //         $('#datepicker2').datepicker('setDate', null);
        //         tmp_date_end = parseInt(new Date().getTime() /1000);
        //     }

        //     var args = {
        //                 type : $scope.selectedItemID,
        //                 reg_st_time : tmp_date_st,
        //                 reg_end_time : tmp_date_end,
        //             }
        //             var tmpDate1 = new Date();
        //             //发出事件
        //             EventService.emit(EventService.ev.START_VIEW_SALES_SUMMARY_SHEET,args);
        // }

        //选择时间// 初始化不成功
        // 使用jqUI初始化时间空间
        // JQuery可以自动监控时间状态，直接在里面发送事件请求即可（不需要watch）
        // $(function () {
        //     //默认显示当天时间
        //     var date = new Date();
        //     var today = date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
        //     $('#reportrange span').html(today + ' 至 ' + today);
        //
        //     $('#reportrange').daterangepicker({
        //         startDate: moment().startOf('day'),
        //         endDate: moment(),
        //         // maxDate : moment(), //最大时间为现在
        //         // dateLimit : {
        //         //     days : 30
        //         // }, //开始和结束最大间隔天数
        //         parentEl: $(".time-container"),
        //         showDropdowns: true, //下拉选择年月
        //         showWeekNumbers: false, // 是否显示第几周
        //         timePicker: false, //时间选择
        //         timePickerIncrement: 60, //分钟选择间隔
        //         timePicker12Hour: false, //24小时制 false为12小时制
        //         language: 'zh-CN',
        //         ranges: {
        //             '所有': [moment().subtract(16,"month").startOf("month"), moment()],
        //             '今日': [moment().startOf('day'), moment()],
        //             '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
        //             '最近7日': [moment().subtract('days', 6), moment()],
        //             '最近30日': [moment().subtract('days', 29), moment()],
        //             '本月': [moment().startOf("month"), moment().endOf("month")],
        //             '上个月': [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")]
        //         },
        //         opens: 'right', //弹出位置
        //         buttonClasses: ['xy-btn xy-btn-default'],
        //         applyClass: 'xy-btn-small orange',
        //         cancelClass: 'xy-btn-small',
        //         format: 'YYYY-MM-DD', //日期格式
        //         separator: ' to ',
        //
        //         locale: {
        //             applyLabel: '确认',
        //             cancelLabel: '取消',
        //             fromLabel: '起始时间',
        //             toLabel: '结束时间',
        //             customRangeLabel: '自定义',
        //             firstDay: 1,
        //         },
        //     }, function(start, end, label) { //选择日期后回调
        //
        //
        //         tmp_date_st = parseInt(new Date(start).getTime() / 1000);
        //         tmp_date_end = parseInt(new Date(end).getTime() / 1000);
        //         var args = {
        //             type : $scope.selectedItemID,
        //             reg_st_time : tmp_date_st,
        //             reg_end_time : tmp_date_end,
        //         }
        //         var tmpDate1 = new Date();
        //         //发出事件
        //         console.log('请求参数', args);
        //         EventService.emit(EventService.ev.START_VIEW_PURCHASE_SUMMARY_SHEET,args);
        //
        //         showButtonPanel: true,
        //
        //
        //             // $scope.query.setStartTime( new Date(start.format('YYYY-MM-DD HH:mm:ss')).getTime() ); // 设置查询起始时间
        //             // $scope.query.setEndTime( new Date(end.format('YYYY-MM-DD HH:mm:ss')).getTime() ); //设置查询结束时间
        //             // $scope.query.request(); //开始查询
        //             $('#reportrange span').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
        //     });
        //
        //     $('#daterangepicker').addClass('show-calendar');
        //     $('#daterangepicker').css('display','none');
        //     $("[data-toggle='tooltip']").tooltip();
        // })

        //选择查询类型：按商品，按客户，按销售员
        // $scope.selectCat = function(itemStr){
        //     if(itemStr == '按商品'){
        //         $scope.selectedItemID = 1;
        //         $scope.option[0].flag_chosen = 1;
        //         $scope.option[1].flag_chosen = 0;
        //         $scope.option[2].flag_chosen = 0;
        //     }
        //     else if(itemStr == '按客户'){
        //         $scope.selectedItemID = 2;
        //         $scope.option[0].flag_chosen = 0;
        //         $scope.option[1].flag_chosen = 1;
        //         $scope.option[2].flag_chosen = 0;
        //     }
        //     else if(itemStr == '按销售员'){
        //         $scope.selectedItemID = 3;
        //         $scope.option[0].flag_chosen = 0;
        //         $scope.option[1].flag_chosen = 0;
        //         $scope.option[2].flag_chosen = 1;
        //     }
        // }
        //监控ID的变化
        // $scope.$watch('selectedItemID',function(newValue,oldValue, scope){
        //     //当ID发生变化时，请求数据
        //     if(newValue != oldValue){
        //         var args = {
        //             type : $scope.selectedItemID,
        //             reg_st_time : tmp_date_st,
        //             reg_end_time : tmp_date_end,
        //         }
        //         EventService.emit(EventService.ev.START_VIEW_SALES_SUMMARY_SHEET,args);
        //     }
        // });

        // 快速搜索输入内容
        // $scope.queryInfo = {
        //     query: '',
        // }
        //监控搜索框中的内容
        // $scope.$watch('queryInfo.query',function() {
        //     //每次搜索框内容改变都会进入监控，进入后重新请求通信，即可刷新页面
        //     $log.log('queryInfo.query:', $scope.queryInfo.query);
        //     model.localTable.changeFilterCondition(0, $scope.queryInfo.query);
        // },true)

        // 点击了页码，修改显示页面
        // $scope.selectPage = function(pageItem) {
        //     model.localTable.changeNowPage(pageItem);
        //     // // model.formatSkuList();
        // }

        //打开库存详情
        // $scope.skuDetail = function(item){
        //     EventService.emit(EventService.ev.START_CREATE_SkuDetail,item);
        // }

        // 列表排序
        // $scope.sortBy = function(sortName,type, $event) {
        //     var statisticsArray = [];
        //     switch (type) {
        //         case 1:
        //             statisticsArray = model.dataArr['skuStatistics'];
        //             break;
        //         case 2:
        //             statisticsArray = model.dataArr['companyStatistics'];
        //             break;
        //         case 3:
        //             statisticsArray = model.dataArr['salemanStatistics'];
        //             break;
        //         default:
        //             $log.log('default');
        //     }
        //
        //     // 当点击时，将已被点击过得图标的颜色去除
        //     // 因为有可能点中 i 元素，造成只有 i 小图标变颜色，所以取其父元素 span
        //     if($($event.target).is('i')) {
        //         $($event.target).parents('span').parents('div').children('span').removeClass('sort');
        //         $($event.target).parents('span').addClass('sort');
        //     }
        //     else {
        //         // 同辈元素去除 sort 类，以将同辈元素去除颜色
        //         $($event.target).siblings('span').removeClass('sort');
        //         // 点击中的元素添加 sort 类，以添加颜色
        //         $($event.target).addClass('sort');
        //         if(!$($event.target).hasClass('desc')) {
        //             $($event.target).removeClass('asc');
        //             $($event.target).addClass('desc');
        //             statisticsArray.sort((a,b) => b[sortName] - a[sortName]);
        //         }
        //         else {
        //             $($event.target).removeClass('desc');
        //             $($event.target).addClass('asc');
        //             statisticsArray.sort((a,b) =>  - b[sortName] + a[sortName]);
        //         }
        //     }
        //     model.constructLocalTable(model.dataArr, type);
        // }
    }
]);