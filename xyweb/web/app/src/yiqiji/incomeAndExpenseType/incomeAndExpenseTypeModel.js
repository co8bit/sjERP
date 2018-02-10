'user strict'

xy.factory('incomeAndExpenseModel', ['EventService', '$log', 'PageService', 'NetworkService', 'QueryCatClass',
    function(EventService, $log, PageService, NetworkService, QueryCatClass) {
		var model = {};
		model.catList = [];
		model.query = QueryCatClass.newQueryCat();
		EventService.on(EventService.ev.START_VIEW_INCOME_EXPENSE_TYPE, function () {
			model.query.request().then(function (){
				PageService.setNgViewPage('incomeAndExpenseType');
				}
			)
		});

		//更新类别列表
		EventService.on(EventService.ev.UPDATA_CATLIST, function(event,arg){
			model.query.request();
		});

		return model;
	}
])