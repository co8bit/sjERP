'use strict';


angular.module('XY').controller('ModifyIncomeOrExpenseController', ['EventService', '$scope', '$log', 'ModifyIncomeOrExpenseModel', 'NumPadClassService','PageService', 'MiscService', 'CstmrService','NetworkService',
    function(EventService, $scope, $log, model, NumPadClassService, PageService, MiscService,CstmrService, NetworkService) {

		$scope.model = model;
		$scope.isCompanyListShow = false;//默认是不显示客户列表
		$scope.docInfoClass = model.docInfoClass;
		$scope.balance = 0;
	    $scope.companyList = CstmrService.company.getCompanyList();
	    // 初始化开单显示时间
	    var time = new Date();
        var month = time.getMonth() < 10 ? '0' + (time.getMonth() + 1) : (time.getMonth() + 1);
        var day = time.getDate() < 10 ? '0' + time.getDate() : time.getDate();
        $scope.reg_time_show = time.getFullYear() + '-' + month + '-' + day; //初始化开单时间为当前时间
        // 使用jqUI
        $(function () {
            $scope.reg_time = Math.floor(new Date().getTime() /1000) ;
            $("#datepicker1").datepicker({
            	showAnim : 'slide',
                onSelect: function (timeStr) {
                    $scope.reg_time = new Date(timeStr).getTime() /1000 ;
                    $log.debug('datepicker1',new Date(timeStr).getTime() /1000 );
                    
                    //当前小时 分 秒 判断选择日期是否为今天？返回当前时间 ：返回选择日期的23:59
                    var Hours=new Date().getHours(),
                    Minutes=new Date().getMinutes(),
                    Seconds=new Date().getSeconds();
                    if($scope.reg_time-8*3600 == parseInt(new Date()/1000)-Hours*3600-Minutes*60-Seconds){
                        $scope.reg_time = new Date().getTime() /1000;
                        $log.debug("testnow:",$scope.reg_time,new Date($scope.reg_time*1000));
                    }else{
                        $scope.reg_time = $scope.reg_time + 16 * 3600 - 60 ;
                        $log.debug("testnow:",$scope.reg_time,new Date($scope.reg_time*1000));
                    }
                },
                showButtonPanel: true,
            })

        });

	    //点击客户输入框
	    $scope.clickcompanyNameInput = function () {
	        $scope.companyNameInput = '';
	        $scope.isCompanyListShow = true;
	    }

	    // 选定一个客户
        $scope.clickCompany = function (company) {
            $scope.companyNameInput = company.name;
            //隐藏弹出框
			$scope.isCompanyListShow = false;
        }

        //获得客户的id
        //watch客户输入框,输入框内容变化时自动匹配联系人，匹配上了自动选定往来单位

        var companyId = ""
        $scope.$watch('companyNameInput', function (newValue) {
            var company = CstmrService.company.findCompanyByName(newValue);
            if(company!=undefined){
            	companyId = company.cid;
            	$scope.balance = parseFloat(company.balance);
            }
        })

        //新增客户
        $scope.clickAddNewCompany = function(){
        	 EventService.emit(EventService.ev.START_CREATE_COMPANY);
        }

		//键盘操作实时刷新输入框（未完成）

        // 限制输入数字
        $scope.$watch('companyIncome', function (newValue, OldValue) {
            if (typeof newValue == 'string'){
            	$scope.companyIncome = newValue.replace(/[^0-9 | \.]+/, '');
			}
        });

        $scope.create = function(docInfoClass){
         	// if ($scope.company.cid == undefined) {
          //       PageService.showConfirmDialog('<div class="title"><a class="red">收款对象错误</a></div>'
          //           + '<div><a>可能是您直接在输入框中手动输入了不存在的<span class="red">收款对象</span>所导致。您可以通过以下三种中的任意一种方法解决：</a></div>'
          //           + '<div><a>1.选择<span class="red">收款对象</span>下拉列表中已有的收款对象名称，而不是手动输入。</a></div>'
          //           + '<div><a>2.手动输入<span class="red">收款对象</span>下拉列表中已经存在的收款对象名称。</a></div>'
          //           + '<div><a>3.如果该收款对象确实是新往来单位，请点击<span class="red">收款对象</span>下拉列表中的<span class="red">增加新单位</span>按钮新建。</a></div>');
          //   }
            // else{

			//判定选择了哪个radio,
	        // $scope.radioSelect = radioSelect;
	        var tmp_companyIncome = $scope.companyIncome;
	        // if($scope.radioSelect == "true"){
	        // 	tmp_companyIncome = tmp_companyIncome;
	        // }
	        // else if($scope.radioSelect == "false"){
	        // 	tmp_companyIncome = 0 - tmp_companyIncome;
	        // }

	        // $log.log('$scope.radioSelect:', $scope.radioSelect);
	        // $log.log('$scope.companyIncome:', tmp_companyIncome);

	        NetworkService.request('ModifyIncomeOrExpense',{
	        	cid : companyId,
	        	class : docInfoClass,
	        	name : "a",
	        	income : tmp_companyIncome,
	        	remark : $scope.companyRemark,
	        	reg_time : $scope.reg_time,
	        },function (){
	        	if(docInfoClass==5){
	        		EventService.emit(EventService.ev.CREATE_MODIFYINCOME_SUCCESS);
	        		EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);

	        	}
	        	else if(docInfoClass==6){
	        		EventService.emit(EventService.ev.CREATE_MODIFYEXPENSE_SUCCESS);
	        		EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
	        	}
	        });

		}

	    $scope.quit = function(){
	    	PageService.closeDialog();
	    }

    }
]);


