'use strict'

angular.module('XY').factory('WarehouseModel', ['EventService','$q', '$log', 'PageService','NetworkService','STOClass',
    function(EventService , $q ,$log, PageService,NetworkService,STOClass) {
        var model = {};
        model.stoInfo=STOClass.newQuerySto();
        EventService.on(EventService.ev.START_OPEN_WAREHOUSE, function () {
            model.stoInfo.request().then(function () {
                model.stoInfo.allSto = model.stoInfo.getAllSto();
                    PageService.setNgViewPage('Warehouse');
            });
        });
        return model;
    }
]);