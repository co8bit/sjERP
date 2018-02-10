'user strict'

xy.controller('DraftController',['$scope', '$log', 'DraftModel', 'EventService', 'PageService','NetworkService', 'AuthGroupService',
	function($scope, $log, model, EventService, PageService, NetworkService, AuthGroupService){

		$scope.pageState = model.pageState;
		$scope.query = model.query;
		$scope.statusCheckbox = model.query.getStatusCheckbox()
		$scope.data = model.query.getData();
		$scope.paginator = model.query.getPaginator();
		$scope.isAllChecked = false;
		$scope.boosExamineIsShow = AuthGroupService.getIsShow(AuthGroupService.module.boosExamine);
		$scope.financeExamineIsShow = AuthGroupService.getIsShow(AuthGroupService.module.financeExamine);
		$scope.continueToFill = function (item){
			switch (item.class){
				case 81:
					EventService.emit(EventService.ev.START_CONTINUE_INCOME_OR_EXPENSE,item)
					break;
				case 82:
					EventService.emit(EventService.ev.START_CONTINUE_INCOME_OR_EXPENSE,item)
					break;
				case 83:
					EventService.emit(EventService.ev.START_CREATE_WITHDRAW,item)
					break;
				case 84:
					EventService.emit(EventService.ev.START_CREATE_TRANSFER_ACCOUNTS,item)
					break;
			}
		}
		$scope.deleteDraft = function (item){
			PageService.showConfirmDialog('确认删除该草稿单？',['删除','取消'],function (){
				var dataToSend = {
					fid: item.fid
				};
				NetworkService.request('deleteFinanceDraft',dataToSend,function (data){
					model.query.request();
				})
			})
		}
		/**
		 * 进入财务审核
		 * @param {Object} index
		 */
		$scope.financeExamine = function (index){
			var data = []
			for (v of $scope.data) {
				var tmp = {};
				angular.copy(v, tmp)
				data.push(tmp)
			}
			EventService.emit(EventService.ev.START_FINANCE_EXAMINE,{index:index,data,data});
		}
		/**
		 * 进入boos审核
		 * @param {number} index 单据在列表中的位置
		 */
		$scope.boosExamine = function (index){
			var data = []
			for (v of $scope.data) {
				var tmp = {};
				angular.copy(v, tmp)
				data.push(tmp)
			}
			EventService.emit(EventService.ev.START_BOSS_EXAMINE,{index:index,data,data});
		}
		$scope.AllCheck = function (){
			$scope.isAllChecked = !$scope.isAllChecked;
			for (var v of $scope.data) {
				v.isCheck = $scope.isAllChecked;
			}
		}
		$scope.checkItem = function (item){
			item.isCheck = !item.isCheck;
			var isAllCheck = true;
			for (var v of $scope.data) {
				if (!v.isCheck) {
					isAllCheck = false;
					$scope.isAllChecked = false;
					break;
				}
			}
			if (isAllCheck) {
				$scope.isAllChecked = true;
			}
		}
		$scope.batchCheck = function (){
			var data = [];
			//获取勾选中的单子
			for (var v of $scope.data) {
				if (v.isCheck) {
					data.push(v.fid)
				}
			}
			//判断有无勾选单据
			if (data.length == 0) {
				PageService.showConfirmDialog('您没有选择任何单据！',['确认']);
			}else{
				PageService.showConfirmDialog('请选择将要进行的批量操作！',['通过','驳回','取消'],function (){
					var dataToSend = {
						fid: data,
						status: 84,
					};
					NetworkService.request('groupStatusChange',dataToSend,function (data){
						PageService.showSharedToast('批量通过完成！');
						model.query.request();
					})
				},function (){
					var dataToSend = {
						fid: data,
						status: 85,
					};
					NetworkService.request('groupStatusChange',dataToSend,function (data){
						PageService.showSharedToast('批量驳回完成！');
						model.query.request();
					})
				})
			}
		}

		function clearTime(){
			if(model.time != undefined && model.time != ''){
                var time = new Date(model.time * 1000);
                var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
                var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
                $('#reportrange span').html(time.getFullYear() + '-' + month + '-' + day + ' 至  ' + time.getFullYear() + '-' + month + '-' + day);
                model.time = '';
            }else{
                $('#reportrange span').html('' + ' 至 ' + '');
            }
		}
		// 使用jqUI初始化时间空间
        $(function () {
			clearTime();
            $('#reportrange').daterangepicker({
                startDate: moment().subtract('days',3).startOf("month"),
//					showDropdowns:true,
                // endDate: moment(),
                // maxDate : moment(), //最大时间
                // dateLimit : {
                //     days : 30
                // }, //开始和结束最大间隔天数
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
        })


		$scope.switchTab = function (tab) {
            switch (tab) {
                case 1:
                	clearTime();
            		model.query.cleanTime();
                    $scope.query.setIsDraft(true);
                    $scope.pageState.activeTab = 1;
                    break;
                case 2:
                	clearTime();
                    model.query.cleanTime();
                    model.query.setCid('');
                    $scope.statusCheckbox.selectExclusiveById(81);
                    $scope.pageState.activeTab = 2;
                    break;
                case 3:
                	clearTime();
                    model.query.cleanTime();
                    model.query.setCid('');
                    $scope.statusCheckbox.selectExclusiveById(82);
                    $scope.pageState.activeTab = 3;
                    break;
            }
        }
	}
])
