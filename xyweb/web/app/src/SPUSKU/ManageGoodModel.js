'use strict';

angular.module('XY').factory('ManageGoodModel', ['EventService', '$log', 'PageService', 'StockService', 'LocalTableClass',
    function (EventService, $log, PageService, StockService, LocalTableClass) {

        // $log.debug("ManageGoodModel init")

        var model = {};
        model.spuInfo = [];


        var _filter = [{
            fieldName: ['spu_name', "cat_name", 'qcode'],
            value: '*',
            mode: 1,
        }, {
            fieldName: ["cat_id"],
            value: "*",
            mode: 1
        }];

        function calcPline() {
            var bodyHeight = $(".function-body").height();
            var tableHeight = bodyHeight - 200;
            // $log.debug('tableHeight',tableHeight);
            var ajustedPline = Math.floor(tableHeight / 36);
            return ajustedPline;
        }

        EventService.on(EventService.ev.START_MANAGE_SKU, function (event, arg) {
            StockService.querySKU(1).then(function () {
                angular.copy(StockService.get_spus_info(), model.spuInfo);
                if (!model.localTable) {
                    model.localTable = LocalTableClass.newLocalTable(model.spuInfo, _filter, 10);
                    model.spuList = model.localTable.getShowData();
                    var newPline = calcPline();
                    model.localTable.changePline(newPline);
                    model.localTable.changeFilterCondition(1, '*');
                } else {
                    model.localTable.calc();
                }
                PageService.setNgViewPage('ManageGoodAndCat');
            });
        });

        return model; // or return model

    }
]);
