'use strict';
angular.module('XY').factory('PurchaseSummarySheetModel',['EventService', '$log', 'PageService', 'MiscService', 'QueryService', 'QueryClass', 'AuthGroupService','LocalTableClass','NetworkService',
    function(EventService, $log, PageService, MiscService, QueryService, QueryClass, AuthGroupService,LocalTableClass, NetworkService){

        var model = {};//model负责获取并存储后台数据

        //初始化时间为当天
        // var reg_st_time_init = parseInt(new Date().getTime() /1000);
        // var reg_end_time_init = parseInt(new Date().getTime() /1000);
        //
        // var _filter_spu = [{
        //     fieldName: ['spu_name'],
        //     value: '*',//*是任意值
        //     mode: 1,
        // }];
        // var _filter_cid_name = [{
        //     fieldName: ['cid_name'],
        //     value: '*',//*是任意值
        //     mode: 1,
        // }];
        // var _filter_operator_name = [{
        //     fieldName: ['operator_name'],
        //     value: '*',//*是任意值
        //     mode: 1,
        // }];
        // function calcPline() {
        //     var bodyHeight = $(".function-body").height();
        //     var tableHeight = bodyHeight - 200;
        //     // $log.debug('tableHeight',tableHeight);
        //     var ajustedPline = Math.floor(tableHeight / 36);
        //     return ajustedPline;
        // }
        // 响应查看今日单据事件
        EventService.on(EventService.ev.START_VIEW_PURCHASE_SUMMARY_SHEET, function (event,args) {
            //如果参数是空，则说明是从页面点击进入，没有参数可穿，需要赋初始值
            if(args == null){
                var args = {};
                args.type = 1;
                args.reg_st_time = reg_st_time_init;
                args.reg_end_time = reg_end_time_init;
                args.isOnce = true;
            }
            else{//不为空，说明是从controller发起的事件，参数直接传入
                args.isOnce = false;
            }
             console.log('我监听到了事件');
            NetworkService.request('PurchaseSummary',args,function (data){
                //添加序号
                console.log('返回的采购数据', data);
                sequence(data.data.skuStatistics);
                sequence(data.data.salemanStatistics);
                sequence(data.data.companyStatistics);
                // angular.copy(data , model.data);
                // 构造本地表对象，传入查询类型type

                model.data = data;
                model.dataArr = arrData(data);//把data变成数组，传递给LocalTable

                model.constructLocalTable(model.dataArr,args.type);

                if (args.isOnce)
                    PageService.setNgViewPage('PurchaseSummarySheet');
            });
        })

        return model // or return model

    }
]);