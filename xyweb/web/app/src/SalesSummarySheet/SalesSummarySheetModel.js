'use strict';
angular.module('XY').factory('SalesSummarySheetModel',['EventService', '$log', 'PageService', 'MiscService', 'QueryService', 'QueryClass', 'AuthGroupService','LocalTableClass','NetworkService',
	function(EventService, $log, PageService, MiscService, QueryService, QueryClass, AuthGroupService,LocalTableClass, NetworkService){

		var model = {};//model负责获取并存储后台数据
		
		//初始化时间为当天
		var reg_st_time_init = parseInt(new Date().getTime() /1000);
    	var reg_end_time_init = parseInt(new Date().getTime() /1000);

        var _filter_spu = [{
            fieldName: ['spu_name'],
            value: '*',//*是任意值
            mode: 1,
        }];
        var _filter_cid_name = [{
            fieldName: ['cid_name'],
            value: '*',//*是任意值
            mode: 1,
        }];
        var _filter_operator_name = [{
            fieldName: ['operator_name'],
            value: '*',//*是任意值
            mode: 1,
        }];
        function calcPline() {
            var bodyHeight = $(".function-body").height();
            var tableHeight = bodyHeight - 200;
            // $log.debug('tableHeight',tableHeight);
            var ajustedPline = Math.floor(tableHeight / 36);
            return ajustedPline;
        }

		// 响应查看今日单据事件
        EventService.on(EventService.ev.START_VIEW_SALES_SUMMARY_SHEET, function (event,args) {
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

			NetworkService.request('SaleSummary',args,function (data){
                //添加序号
		    	sequence(data.data.skuStatistics);
		    	sequence(data.data.salemanStatistics);
		    	sequence(data.data.companyStatistics);
		    	// angular.copy(data , model.data);
                // 构造本地表对象，传入查询类型type

                model.data = data;
                model.dataArr = arrData(data);//把data变成数组，传递给LocalTable

                model.constructLocalTable(model.dataArr,args.type);

            	if (args.isOnce)
            	PageService.setNgViewPage('SalesSummarySheet');
	        });
        })

        
        //添加序号
        function sequence(model_object){
    		var i = 0;
	    	for (var value in model_object){
	    		model_object[value].no = ++i;
	    	}
		}
        //根据数据构造localTable，构造好以后各个分页的内容已经在paginate中生成，用model.localTable.getPageList()可以获得分页页码，点击
        model.constructLocalTable= (dataArr,type) =>{
            //为model添加localTable，返回给controller使用
            if(type==1){
                model.localTable = LocalTableClass.newLocalTable(dataArr['skuStatistics'] , _filter_spu, 10);
            }
            if(type==2){
                model.localTable = LocalTableClass.newLocalTable(dataArr['companyStatistics'] , _filter_cid_name, 10);
            }
            if(type==3){
                model.localTable = LocalTableClass.newLocalTable(dataArr['salemanStatistics'] , _filter_operator_name, 10);
            }
            model.statisticsList = model.localTable.getShowData();

            let newPline = calcPline();
  			model.localTable.changePline(newPline);
        }

        //把data从对象变为数组,因为newLocalTable第一个参数类型是数组（调不出来就用单步看）
        function arrData(data){

            var dataArr = new Array();
            //把返回sku从对象变为数组
            var i = 0;
            dataArr['skuStatistics'] = new Array();
            for(var key in data.data.skuStatistics){
                dataArr['skuStatistics'][i] = data.data.skuStatistics[key];
                i++;
            }
            // 把saleman从对象变为数组
            var i = 0;
            dataArr['salemanStatistics'] = new Array();
            for(var key in data.data.salemanStatistics){
                dataArr['salemanStatistics'][i] = data.data.salemanStatistics[key];
                i++;
            }
            //把companyStatistics从对象变为数组
            var i = 0;
            dataArr['companyStatistics'] = new Array();
            for(var key in data.data.companyStatistics){
                dataArr['companyStatistics'][i] = data.data.companyStatistics[key];
                i++;
            }

            return dataArr;
        }

        return model // or return model

	}
]);