'user strict'

xy.factory('DraftModel', ['EventService', '$log', 'PageService', 'QueryClass', 'UserService', 'MiscService',
	function(EventService, $log, PageService, QueryClass, UserService, MiscService) {
		var model = {};
		model.query = {};
		// 页面状态
		model.pageState = {
			activeTab: 1,
		}

		//计算主体大小 计算显示行数在下面的响应事件中设置显示行数
		model.calcPline = function() {
			var bodyHeight = $(".function-body").height()
			var pline = Math.floor(bodyHeight / 36) - 3;
			return pline;
		}

		// 监听查看往来详情
		EventService.on(EventService.ev.START_VIEW_YIQIJI_DRAFT, function(event, arg) {
			model.orderPage = 4;

			// 激活第arg个选项卡
			model.pageState.activeTab = arg;

			// 新建一个查询管理对象
			model.query = QueryClass.newQuery();

			// 装定初始查询条件
			model.query.resetTime();//时间
			model.query.setPage(1);//页码
			model.query.setYqj(true);//设置查询模式为易企记

			//设定每页选定行数
			var pline= model.calcPline();
			model.query.setPline(pline);//显示行数

			//用户cid
			model.query.setCid(UserService.getLoginStatus().id);

			// 异步任务计数器
			var task = MiscService.newAsyncTask(1);

			// 新建一个管理查询的对象
			model.query.setCustomCallback(function() {
				// 查询成功后计数
				task.finishOneProc();
			});

			// 设定回调
			task.setFinalProc(function() {
				// 初始查询完成后清除回调
				model.query.setCustomCallback(undefined);
				// 都完成时跳转
				PageService.setNgViewPage('NullPage');
				PageService.setNgViewPage('Draft');

			})

			// 设定除了草稿单据的选项卡对应的条件
			var statusOptionArr = [{
				optionName: '财务待审核单据',
				id: 81,
			}, {
				optionName: '老板待查阅单据',
				id: 82,
			}, ];

			//设定选项卡
			model.query.getStatusCheckbox().setOption(statusOptionArr);

			//要打开的选项卡
			switch(arg) {
				case 1:
					model.query.setIsDraft(true);
					break;
				case 2:
					model.query.cleanTime();
					model.query.setCid('');
					model.query.getStatusCheckbox().selectExclusiveById(81);
					break;
				case 3:
					model.query.cleanTime();
					model.query.setCid('');
					model.query.getStatusCheckbox().selectExclusiveById(82);
					break;
			}
		})
		return model;
	}
])