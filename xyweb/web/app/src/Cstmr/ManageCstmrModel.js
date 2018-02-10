'use strict';

angular.module('XY').factory('ManageCstmrModel', ['EventService', '$log', 'PageService', 'LocalTableClass','CstmrService',
    function(EventService, $log, PageService, LocalTableClass, CstmrService) {

        var model = {};

        var _filter = [{
            fieldName: ['name','qcode'],
            value: '*',
            mode: 1,
        }]

        var companyList = CstmrService.company.getCompanyList();

        // 本地表对象
        model.localTable = LocalTableClass.newLocalTable(companyList, _filter, 10);
        model.companyListShow = model.localTable.getShowData();

        function calcPline() {
            var bodyHeight = $(".function-body").height();
            var tableHeight = bodyHeight - 130;
            // $log.debug('tableHeight',tableHeight);
            var ajustedPline = Math.floor(tableHeight / 36);
            return ajustedPline;
        }

        EventService.on(EventService.ev.MANAGE_COMPANY, function (event, arg) {
            var newPline = calcPline();
            model.localTable.changePline(newPline);

            model.query = arg;
            PageService.setNgViewPage('ManageCstmr');
        });

        EventService.on(EventService.ev.BACK_TO_MANAGE_COMPANY,function(event,args){ 
             var newPline = calcPline();
             // model.localTable.changePlineByPage(newPline,args.page);
            model.localTable.changePline(newPline);
            model.query = args.queryInfo;
            model.now_page = args.page;
            // $log.log('model.now_page',args.page);
            PageService.setNgViewPage('ManageCstmr');

        });

        return model; // or return model

    }
]);
