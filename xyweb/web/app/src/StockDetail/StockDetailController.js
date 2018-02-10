/**
 * Created by Raytine on 2017/7/25.
 */
xy.controller("StockDetailController",['$scope','$log','$filter','PageService','StockDetailModel','EventService','StockService','MiscService','NetworkService','OrderService','UserService',
    function ($scope,$log,$filter,PageService,model,EventService,StockService,MiscService,NetworkService,OrderService,UserService) {
        $scope.details  = model.details;
        $scope.rpg = UserService.getLoginStatus().rpg;
        var remark = $scope.details.remark;
        // $log.error($scope.details.cart);
        $scope.details.time = $filter('date')($scope.details.update_time * 1000, 'yyyy/MM/dd HH:mm');
        $scope.showLog = function () {
            PageService.showHistoryDialog(model.details.history)
        };
        $scope.quit = function () {
            if(remark != $scope.details.remark){
                EventService.emit(EventService.ev.VIEW_BACK_TO_ORDER);
            }
            PageService.closeDialog(model.DialogId)
        };
        $scope.changeRemark = function () {
            if(remark != $scope.details.remark){
                var dataToSend={
                    wid:$scope.details.wid,
                    remark:$scope.details.remark
                };
                OrderService.editOrder(dataToSend, function () {

                })
            }
        }
}]);