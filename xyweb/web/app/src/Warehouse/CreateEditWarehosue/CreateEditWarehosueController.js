'use strict';

xy.controller('CreateEditWarehouseController', ['$rootScope', '$scope', '$log', 'PageService', 'CreateEditWarehosueModel', 'EventService',
    function ($rootScope, $scope, $log, PageService, model, EventService) {
        $scope.isCreate = model.isCreate;
        $scope.stoInfo = model.stoInfo;

        var stoInfo = angular.copy($scope.stoInfo.sto, stoInfo);
        if ($scope.isCreate) {
            $scope.stoInfo.sto.sto_name = "";//仓库名称
            $scope.stoInfo.sto.remark = "";//仓库备注
            $scope.stoInfo.sto.sto_index = 2;//显示优先级
            $scope.stoInfo.sto.status = 1;//启用状态
            $scope.disabled = false;
        }
        $scope.$watch("stoInfo.sto.sto_name",function (newValue, oldValue) {
           if($scope.stoInfo.sto.sto_name.length >20){
               $scope.stoInfo.sto.sto_name= oldValue
           }
        });
        $scope.isDefault = $scope.stoInfo.sto.sto_index == 1 ? true : false;
        $scope.isDisabled = $scope.stoInfo.sto.status == 1 ? false : true;
        $scope.enterWay = model.enterWay;
        $scope.changeIndex = function () { // 改变启用的状态
            if ($scope.stoInfo.sto.sto_index == 2) {
                PageService.showConfirmDialog('已有优先显示的仓库，确定要代替它优先显示么？', [], function () {
                    $scope.isDefault = true;
                    $scope.stoInfo.sto.sto_index = 1
                })
            } else {
                PageService.showConfirmDialog('确定要取消此仓库的优先显示么？', [], function () {
                    $scope.isDefault = false;
                    $scope.stoInfo.sto.sto_index = 2
                })
            }
        };
        $scope.changeStatus = function () {
            if ($scope.stoInfo.sto.status == 1) {
                $scope.stoInfo.sto.status = 0;
                $scope.isDisabled = true;
            } else {
                $scope.stoInfo.sto.status = 1;
                $scope.isDisabled = false;
            }
        };
        $scope.quit = function () {
            if (angular.equals(stoInfo, $scope.stoInfo.sto)) {
                PageService.closeDialog();
            } else {
                PageService.showConfirmDialog('确定放弃编辑么', [], function () {
                    PageService.closeDialog()
                })
            }
        };
        $scope.createStoBtn = function (type) {
            var dataType = {};
            if ($scope.stoInfo.sto.sto_name) {
                angular.copy($scope.stoInfo.sto, dataType);
                model.stoInfo.operate.createSto(dataType).then(function (data) {
                    if (type === 1) {
                        PageService.showSharedToast("仓库创建成功!");
                    } else {
                        PageService.showSharedToast("仓库修改成功!");
                    }

                    if ($scope.enterWay === "createSPU") {
                        PageService.closeDialog();
                        EventService.emit(EventService.ev.CREATESPU_UPDATE);
                    }
                    else if ($scope.enterWay === "createSaleOrder") {
                        PageService.closeDialog();
                        EventService.emit(EventService.ev.SALEORDER_UPDATE,data.data);
                    }
                    else {
                        EventService.emit(EventService.ev.START_OPEN_WAREHOUSE);
                        PageService.closeDialog('CreateEditWarehouse');
                    }

                });
            } else {
                PageService.showSharedToast("请输入仓库名!");
            }
        }
    }
]);