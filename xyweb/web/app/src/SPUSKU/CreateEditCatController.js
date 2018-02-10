'use strict';


xy.controller('CreateEditCatController', ['$rootScope', '$scope', '$log', 'PageService', 'StockService','CreateEditCatModel','EventService','SPUClass',
    function($rootScope, $scope, $log, PageService, StockService, model, EventService,SPUClass) {



    	// 引用正在编辑的cat对象
    	$scope.catInfo = model.catInfo;
		var catInfo = angular.copy($scope.catInfo, catInfo);
        //go等于1 就是继续创建
    	$scope.createCat =  function(cat,go){
            StockService.createCat(cat);
    		if(go===1){
                EventService.emit(EventService.ev.START_CREATE_EDIT_CAT);
                $scope.catInfo = SPUClass.newCat();
                catInfo = angular.copy($scope.catInfo,catInfo);
                PageService.showDialog('CreateEditCat');
			}
    	};

    	$scope.editCat = function(cat) {
			StockService.editCat(cat);
		}

		$scope.confirmDeleteCat = function (cat) {
			PageService.showConfirmDialog('确认删除类别么？',['确认','取消'],function (){
				StockService.deleteCat(cat);
			})
		}

		$scope.quit = function() {
			if (angular.equals(catInfo, $scope.catInfo)) {
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
		})

		$scope.$on('$destroy', function () {
			keydownHandle();
		})
    }
]);
