/**
 * Created by Raytine on 2017/7/25.
 */
angular.module("XY").factory('StockDetailModel', ['$log', 'EventService', 'StockService', 'NetworkService', 'PageService', 'OrderInfoClassService', 'OrderService',
    function ($log, EventService, StockService, NetworkService, PageService, OrderInfoClassService, OrderService) {
        var model = {};
        model.details = {};
        model.refreshOrderInfo = function () {
            OrderService.queryOneOrder(model.oid, function (orderInfo) {
                angular.copy(orderInfo, model.orderInfo);
                angular.copy(orderInfo, model.orderInfoBak);
            });
        };
        EventService.on(EventService.ev.REQUISITION_VIEW_DETAILS, function (event, arg) {
            if (Object.prototype.toString.call(arg) == "[object String]") {
                StockService.getStockOrder(arg, 54).then(function (data) {
                    $log.error(data);
                    angular.copy(data, model.details)
                    model.DialogId = PageService.showDialog('StockDetail')
                })
            } else {
                angular.copy(OrderInfoClassService.newRequisitionOrderFromApiData(arg), model.details);
                model.DialogId = PageService.showDialog('StockDetail')
            }

        });
        EventService.on(EventService.ev.STOCK_TAKING_VIEW_DETAILS, function (event, arg) {
            if (Object.prototype.toString.call(arg) == "[object String]") {
                StockService.getStockOrder(arg, 53).then(function (data) {
                    angular.copy(data, model.details);
                    model.DialogId = PageService.showDialog('StockDetail')
                })
            } else {
                angular.copy(OrderInfoClassService.newStockTakingOrderFromApiData(arg), model.details);
                model.DialogId = PageService.showDialog('StockDetail')
            }

        });
        return model;

    }]);