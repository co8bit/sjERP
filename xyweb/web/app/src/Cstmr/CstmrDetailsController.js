//xxxxController
'use strict';

angular.module('XY').controller('CstmrDetailsController', ['EventService', '$scope', '$log', 'PageService', 'CstmrDetailsModel', 'CheckboxWidgetClass', 'MiscService', '$window', 'CstmrService',
    function (EventService, $scope, $log, PageService, model, CheckboxWidgetClass, MiscService, $window, CstmrService) {
        $scope.PageService = PageService;
        $scope.EventService = EventService;
        $scope.statementIsShow = false;
        //把model里的东西映射过来
        $scope.model = model; //映射model
        // 映射查询管理对象和它的一些成员
        $scope.query = model.query;
        $scope.data = model.query.getData();
        $scope.sendData = [];
        // $scope.sendData = angular.copy($scope.data);
        $scope.paginator = model.query.getPaginator();
        $scope.timeFilter = model.query.getTimeFilter();
        $scope.classCheckbox = model.query.getClassCheckbox();
        //$scope.classCheckbox
        $scope.statusCheckbox = model.query.getStatusCheckbox();
        $scope.remainTypeCheckbox = model.query.getremainTypeCheckbox();
        // 映射往来单位自身的信息
        $scope.cstmrInfo = model.cstmrInfo;
        $scope.isAllChecked = false;
        $scope.isCheckBoxShow = false;
        $scope.classCheckbox.selectAll(1);
        $scope.isMobile = MiscService.testMobile();
        /**
         *
         * @return {[type]} [description]
         */
        $scope.goBack = function () {
            switch (model.cstmrPage) {
                case 1:
                // $log.log('sss',model.cstmrNowPage);
                var data = {
                    queryInfo: model.queryInfo,
                    page:model.cstmrNowPage,
                };
                    PageService.closeDialog();
                    EventService.emit(EventService.ev.BACK_TO_MANAGE_COMPANY, data);
                    // EventService.emit(EventService.ev.MANAGE_COMPANY, model.queryInfo);
                    break;
                case 2:
                    $window.history.back();
                    break;
                case 3:
                    var cookie = model.orderCookies;
                    if (model.pageState == 1) {
                        EventService.emit(EventService.ev.BACK_TO_VIEW_TODAY_ORDER,cookie);
                        // EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
                    } else if (model.pageState == 2) {
                        EventService.emit(EventService.ev.BACK_TO_VIEW_ALL_ORDER,cookie);
                        // EventService.emit(EventService.ev.START_VIEW_ALL_ORDER);
                    }
                    break;
            }
        }

        EventService.on(EventService.ev.VIEW_BACK_TO_COMPANY,function(){
             model.query.request();
             $scope.data = model.query.getData();
             var pageItem = {
                 content: $scope.paginator.now_page,
             };
             $scope.paginator.clickButton(pageItem);
        });


        $scope.$watch('model.cstmrInfo',function(){
            $scope.cstmrInfo.name=model.cstmrInfo.name;
            $scope.cstmrInfo.address=model.cstmrInfo.address;
            $scope.cstmrInfo.remark=model.cstmrInfo.remark;
        });

        // //#646 剔除这两种单据
        // for(var i = $scope.classCheckbox.option.length-1; i>= 0 ; i--)
        // {
        //     if($scope.classCheckbox.option[i].optionName == '支出费用单' ||$scope.classCheckbox.option[i].optionName == '其他收入单')
        //     {
        //         $scope.classCheckbox.option.splice(i);
        //     }
        // }

        $scope.checkCstmrInfo = function () {
            EventService.emit(EventService.ev.COMPANY_EDIT_CONTACT, $scope.cstmrInfo.cid);
            var args = {
                orderPage: model.orderPage,
                cid: model.cid,
                query: model.queryInfo,
                cstmrPage: model.cstmrPage,
            }
            EventService.emit(EventService.ev.ORDER_ARGS, args)
        }

        $scope.clickViewDetails = function (item) {
            MiscService.sendViewDetailsEvent(item);

            var args = {
                orderPage: model.orderPage,
                cid: model.cid,
                query: model.queryInfo,
                cstmrPage: model.cstmrPage,
            }
            EventService.emit(EventService.ev.ORDER_ARGS, args)
        }

        $scope.saveCheckStatus = function (item){
                var isFound = false;
                for(var j=0;j<$scope.sendData.length;j++){
                    if(item.oid>0&&$scope.sendData[j].oid>0&&$scope.sendData[j].oid==item.oid){
                        if(item.isChecked==false){
                            $scope.sendData.splice(j,1);
                            // $log.log('ajjj',j);
                        }
                        isFound=true;
                        break;
                    }
                    if(item.fid>0&&$scope.sendData[j].fid>0&&$scope.sendData[j].fid==item.fid){
                        if(item.isChecked==false){
                            $scope.sendData.splice(j,1);
                            // $log.log('zjjj',j);
                        }
                        isFound=true;
                        break;
                    }
                }
                if(!isFound&&item.isChecked==true) {
                    $scope.sendData.splice(0,0,item);
                    // $log.log('zsjjj',j);
                }

            // $log.log('data',$scope.data);
            // $log.log('sendData',$scope.sendData);

        }
        $scope.initCheck = function(){
            $scope.isAllChecked = false;
            // $scope.$apply();
        }

        $scope.initSendData = function(){
            $scope.sendData = [];
            $scope.isAllChecked = false;
        };

        $scope.updateSatus =function(){
            for(var i = 0;i<$scope.data.length;i++){
                if($scope.data[i].isChecked == false) {
                    $scope.isAllChecked = false;
                    $scope.$apply();
                }
                for (var j = 0; j<$scope.sendData.length;j++){
                    if($scope.sendData[j].oid>0&&$scope.sendData[j].oid==$scope.data[i].oid){
                        $scope.data[i].isChecked = true;
                        // $log.log('iii',i);
                        break;
                    }
                    if($scope.sendData[j].fid>0&&$scope.sendData[j].fid==$scope.data[i].fid){
                        $scope.data[i].isChecked = true;
                        // $log.log('iii',i);
                        break;
                    }
                }
            }
            // $log.log('sendData',$scope.sendData);
        }

        // 全选/取消全选checkBox
        $scope.AllCheck = function(){
            for (var i = 0; i < $scope.data.length; i++) {
                $scope.data[i].isChecked = $scope.isAllChecked;
                $scope.saveCheckStatus($scope.data[i]);
            }
        }


        /**
         * 打开发送对账单界面
         */
        $scope.startStatement = function (type) {
            // $scope.saveCheckStatus();
            var idList = [];
            var wechatList = [];
            var _id = '';
            var _class = '';
            var salesOrderClassArray = [1, 2, 3, 4, 5, 6, '1', '2', '3', '4', '5', '6'];
            var financeOrderClassArray = [71, 72, 73, 74, '71', '72', '73', '74'];
            for(var i = 0; i < $scope.sendData.length; i++){
                if($scope.sendData[i].status != 100){
                    if(Number($scope.sendData[i].oid)){
                        _id = $scope.sendData[i].oid;
                    }else if(Number($scope.sendData[i].fid)){
                        _id = $scope.sendData[i].fid;
                    }
                    if($.inArray($scope.sendData[i].class,salesOrderClassArray) > -1){
                        _class = 1;
                    }else if($.inArray($scope.sendData[i].class,financeOrderClassArray)>-1){
                        _class = 2;
                    }
                    idList.push({
                        id:_id,
                        class:_class
                    });
                    wechatList.unshift($scope.sendData[i]);
                }
            }
            for(var k in $scope.data){
                $scope.data[k].isChecked = false;
            }
            $scope.isAllChecked = false;
            if (idList.length == 0) {
                PageService.showConfirmDialog('您没有选择有效单据!请检查!', ['重新选择','取消'],function(){
                    $scope.isAllChecked = false;
                },function (){
                    $scope.isCheckBoxShow = false;
                    $scope.isAllChecked = false;
                    $('#sendType').val(0);
                })
                $scope.sendData=[];
                return;
            }

            var data = {
                cid: model.cid,
                idList: JSON.stringify(idList)
            }

            if(type == 1){
                EventService.emit(EventService.ev.START_STATEMENT, data);
                $scope.sendData = [];
                initSelct();
            }else if(type == 2){
                EventService.emit(EventService.ev.START_STATEMENTWECHAT, wechatList);
                //点击发送后显示微信展示界面
                $scope.sendData = [];
                $scope.statementIsShow = true;
                initSelct();
            };
        }

		EventService.on('CLOSE_STATEMENTWECHAT',function(){
            $scope.statementIsShow = false;
		})

        /**
         * 删除往来单位
         */
        $scope.deleteCompany = function () {
            PageService.showConfirmDialog('确定删除往来单位?', [], function () {
                CstmrService.company.deleteCompany(model.cid, function () {
                    EventService.emit(EventService.ev.MANAGE_COMPANY);
                });
            });
        }
        /**
         * 初始化对账单发送方式
         * @return {[type]} [description]
         */
        function initSelct (){
            $('#sendType').val(0);
            $scope.sendType = '';
            $scope.isCheckBoxShow = false;
        }
        initSelct();
        // 使用jqUI
        $(function () {
            $('#reportrange span').html('' + ' 至 ' + '');
            $('#reportrange').daterangepicker({
                startDate: moment().startOf('day'),
                endDate: moment(),
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
                $('#reportrange span').html(start.format('YYYY-MM-DD') + ' 至 ' + end.format('YYYY-MM-DD'));
            });
            $('#daterangepicker').addClass('show-calendar');
            $('#daterangepicker').css('display','none');

            document.getElementById("sendType").addEventListener('change',function (){
                if ($('#sendType').val() == 1) {
                    $scope.sendType = 1;
                    $scope.isCheckBoxShow = true;
                }else{
                    $scope.sendType = 2;
                    $scope.isCheckBoxShow = true;
                }
            })
        });
    }
]);