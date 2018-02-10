'use strict'

xy.controller('AccountSettingController', ['$scope', '$log','AccountSettingModel', 'EventService',
    function($scope, $log, model, EventService) {
		$scope.accountList = model.accountList;


		$scope.createAccount = function (){
			EventService.emit(EventService.ev.START_CREATE_ACCOUNT,{type:1});
		}

		$scope.editAccount = function (item){
			if(item.account_source_type == 3){
				EventService.emit(EventService.ev.EDIT_CASH_ACCOUNT,item);
			}else{
				EventService.emit(EventService.ev.START_CREATE_ACCOUNT,{type:1,item,item});
			}
		}

    }
])
