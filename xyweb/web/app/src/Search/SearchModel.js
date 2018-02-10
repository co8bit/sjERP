//xxxxModel or Service or Class
'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('SearchModel', ['EventService', '$log', 'PageService', 'QueryClass',
    function(EventService, $log, PageService, QueryClass) {

        // $log.debug("xxxxService init")
        var model = {};
        model.query = QueryClass.newQueryForSearch();
        model.pageState = {
            activeTab: 1,
        };

        model.searchContent = ''

        function calcPline() {
            var bodyHeight = $(".function-body").height()
            var tableHeight = bodyHeight - 150
                // $log.debug('tableHeight',tableHeight)
            var ajustedPline = Math.floor(tableHeight / 36)
            return ajustedPline
        }

        EventService.on(EventService.ev.SEARCH_TITLE_BAR, function(event, arg) {

            
            // 计算合适的每页行数
            var ajustedPline = calcPline()
            model.query.setPline(ajustedPline)

            var searchContent = arg;
            model.searchContent = arg
            model.query.cleanData();
            model.query.setType(1);
            model.query.setSearch(searchContent);
            model.pageState.activeTab = 1;
            model.query.setCustomCallback(function() {
                model.query.setCustomCallback(undefined);
                PageService.setNgViewPage('Search');
            });
            model.query.request();

        });

        return model; // or return model

    }
]);
