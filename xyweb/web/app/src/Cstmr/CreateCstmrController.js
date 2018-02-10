'use strict';

xy.controller('CreateCstmrController', ['$scope', '$log', 'CstmrService','PageService', 'CstmrModel','EventService','PYService','$timeout',"CstmrClass",
    function($scope, $log, CstmrService,PageService,model,EventService,PYService,$timeout,CstmrClass) {
    	$scope.model = model;
    	var cstmrInfo = angular.copy(model.cstmrInfo,cstmrInfo)
    	$scope.cstmrInfo = model.cstmrInfo;
    	$scope.PageService = PageService;
        $scope.pageState = model.pageState;
        $scope.$log = $log;
		$scope.isQcodeListShow = false;
		$scope.qcodeList = [];
        //如果有参数 1-继续创建公司
    	$scope.createCompany = function(go){
    		CstmrService.company.create($scope.cstmrInfo, function (cid) {
				EventService.emit(EventService.ev.MANAGE_COMPANY);

				var company = {
					name : $scope.cstmrInfo.name,
					cid : cid,
				}
				EventService.emit(EventService.ev.CREATE_COMPANY_SUCCESS, company); // 创建往来单位成功,将单位名称发出去
			});
    		if(go===1){
    			$scope.cstmrInfo = CstmrClass.newCompany();
                $scope.isQcodeListShow = false;
                $scope.qcodeList = [];
                $scope.PageService.showDialog("CreateCstmr");
			}
    	}

    	$scope.editCompany = function() {
			$scope.cstmrInfo.init_payable = $scope.cstmrInfo.init_payable == '' ? 0 : 0 - $scope.cstmrInfo.init_payable;

            $scope.cstmrInfo.status = $scope.cstmrInfo.status.toString();

			CstmrService.company.edit($scope.cstmrInfo, function () {
				EventService.emit(EventService.ev.COMPANY_VIEW_DETAILS,model.orderArgs);
			});

			EventService.emit(EventService.ev.UPDATE_CSTMR_NOW,$scope.cstmrInfo);
		}

        var isCopy = false;
        $scope.isCopy = function(istrue){
        	isCopy = istrue;
        }
        $scope.$watch('cstmrInfo.name', function () {
        	if($scope.cstmrInfo.contact[0].contact_name == ''){
        		$scope.isCopy(true);
        	}
        	if(isCopy){
        		$scope.cstmrInfo.contact[0].contact_name = $scope.cstmrInfo.name;
        	}
		})

		// 点击了速查码输入框
		$scope.clickQcodeInput = function() {
			if ($scope.qcodeList.length > 0) {
				$scope.isQcodeListShow = !$scope.isQcodeListShow;
			} else {
				$scope.isQcodeListShow = false;
			}
		}

		// 点击了速查码选项
		$scope.clickQcode = function(qcode) {
			$scope.cstmrInfo.qcode = qcode;
			$scope.isQcodeListShow = false;
		}

		$scope.genQcode = function () {
			if ($scope.cstmrInfo.name) {
				$scope.qcodeList = PYService.getPY($scope.cstmrInfo.name);
			} else {
				$scope.qcodeList = [];
			}

			if ($scope.qcodeList.length > 1) {
				$scope.isQcodeListShow = true;
			} else {
				$scope.isQcodeListShow = false;
			}

			$scope.cstmrInfo.qcode = $scope.qcodeList[0];
		}

		$scope.quit = function() {
			if (angular.equals($scope.cstmrInfo, cstmrInfo)) {
				PageService.closeDialog();
			} else {
				PageService.showConfirmDialog('确定放弃编辑吗？',[],function () {
					PageService.closeDialog();
				});
			}
		}

		var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
			if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
				$scope.$apply(function () {
					$scope.quit();
				})
			}
		});

		$scope.$on('$destroy', function () {
			keydownHandle();
		});
    }
]);
