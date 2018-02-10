//xxxxController
'use strict';

angular.module('XY').controller('CreateEditParkAddressController', ['EventService', '$scope', '$log','PageService','ManagementModel','CstmrService',
    function(EventService, $scope, $log,PageService, model,CstmrService) {

        $scope.PageService = PageService;
        $scope.EventService = EventService;

        $scope.editingParkAddress = model.editingParkAddress; // 正在新建/编辑的停车位置
        var editingParkAddress = angular.copy($scope.editingParkAddress, editingParkAddress);

        $scope.clickCreate = function() {
        	CstmrService.parkAddress.create($scope.editingParkAddress);
        }

        $scope.clickEdit = function() {
        	CstmrService.parkAddress.edit($scope.editingParkAddress);
        }

        $scope.quit = function() {
            if (angular.equals(editingParkAddress, $scope.editingParkAddress)) {
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