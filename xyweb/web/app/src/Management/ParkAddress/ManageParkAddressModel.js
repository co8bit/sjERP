'use strict';

angular.module('XY').factory('ManageParkAddressModel', ['EventService', '$log', 'PageService', 'CstmrService', 'LocalTableClass',
    function(EventService, $log, PageService, CstmrService, LocalTableClass) {

        var model = {};

        var _filter = [{
            fieldName: ['sn', 'park_address'],
            value: '*',
            mode: 1,
        }]

		var parkAddress = CstmrService.parkAddress.getParkAddressList();

        // 本地表对象
        model.localTable = LocalTableClass.newLocalTable(parkAddress, _filter, 10);
        model.parkListShow = model.localTable.getShowData();

        function calcPline() {
            var bodyHeight = $(".function-body").height();
            var tableHeight = bodyHeight - 200;
                // $log.debug('tableHeight',tableHeight)
            var ajustedPline = Math.floor(tableHeight / 36);
            return ajustedPline;
        }

        EventService.on(EventService.ev.START_MANAGE_PARK_ADDRESS, function() {
            var newPline = calcPline();
            model.localTable.changePline(newPline);
            CstmrService.parkAddress.queryList();
            PageService.setNgViewPage('ManageParkAddress');
        });

        return model; // or return model

    }
]);
