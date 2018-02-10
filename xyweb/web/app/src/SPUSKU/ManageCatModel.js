'use strict';

angular.module('XY').factory('ManageCatModel', ['EventService', '$log', 'PageService', 'LocalTableClass', 'StockService',
    function(EventService, $log, PageService, LocalTableClass, StockService) {

        // $log.debug("ManageCatModel init")

        var model = {};

        var _filter = [{
            fieldName: ['cat_name'],
            value: '*',
            mode: 1,
        }, {
            fieldName: ['cat_id'],
            value: '*',
            mode: 1,
        }]

        var catInfo = StockService.getCatList();
        model.localTable = LocalTableClass.newLocalTable(catInfo, _filter, 10);
        model.skuListShow = model.localTable.getShowData();

        function calcPline() {
            var bodyHeight = $(".function-body").height()
            var tableHeight = bodyHeight - 200
                // $log.debug('tableHeight',tableHeight)
            var ajustedPline = Math.floor(tableHeight / 36)
            return ajustedPline
        }

        EventService.on(EventService.ev.START_MANAGE_SKU, function() {
            var newPline = calcPline()
            model.localTable.changePline(newPline)
        });

        return model;

    }
]);
