'user strict'
xy.factory('ExpenseAndIncomeSummarySheetModel', ['EventService', '$log', 'PageService', 'NetworkService', '$q',
    function(EventService, $log, PageService, NetworkService, $q) {
		var model = {};
		model.exp = [];
		model.inc = [];
		model.allExp = [];
		model.allInc = [];
		model.chartExp = [];
		model.chartInc = [];
		model.chartExpCat = [];
		model.chartIncCat = [];
		//开始支出汇总表
		EventService.on(EventService.ev.START_VIEW_EXPENSE_AND_INCOME_SUMMARY_SHEET,function(){
			model.getSummarySheet().then(function(){
				PageService.setNgViewPage('ExpenseAndIncomeSummarySheet');
			})
		})
		model.getSummarySheet = function (timeData){
			var defered = $q.defer();
			if(timeData){
				angular.copy(timeData,model.time);
			}else{
				var time = new Date();
				var year = time.getFullYear();
				var month = time.getMonth() + 1;
				var st_time = new Date(year + '/' + month + '/' + 01 ) / 1000
				model.time = {
					reg_st_time: st_time,
					reg_end_time: time / 1000,
				};
			}
			NetworkService.request('getExpenditureStatistics',model.time,function (data){
				model.chartExpIsShow = false;
				model.chartIncIsShow = false;
				if (data.data.class_data.exp) {
					angular.copy(data.data.class_data.exp, model.exp);
				}
				if (data.data.class_data.inc) {
					angular.copy(data.data.class_data.inc, model.inc);
				}
				if(data.data.all_data.exp){
					angular.copy(data.data.all_data.exp, model.allExp)
				}
				if(data.data.all_data.inc){
					angular.copy(data.data.all_data.inc, model.allInc)
				}
				if(data.data.Pie_chart_data.exp){
					model.chartExp = [];
					model.chartExpCat = [];
					for(var v of data.data.Pie_chart_data.exp){
						if(v.price == 0){
							continue
						}
						var tmp = {
							value: v.price,
							name: v.cat_name,
						}
						model.chartExp.push(tmp)
						model.chartExpCat.push(v.cat_name)
					}
					if(model.chartExp.length == 0){
						model.chartExpIsShow = true
					}
				}
				if(data.data.Pie_chart_data.inc){
					model.chartInc = [];
					model.chartIncCat = [];
					for(var v of data.data.Pie_chart_data.inc){
						if(v.price == 0){
							continue
						}
						var tmp = {
							value: v.price,
							name: v.cat_name,
						}
						model.chartInc.push(tmp)
						model.chartIncCat.push(v.cat_name)
					}
					if(model.chartInc.length == 0){
						model.chartIncIsShow = true
					}
				}
				defered.resolve();
			})
			return defered.promise;
		}
		return model;
    }
])