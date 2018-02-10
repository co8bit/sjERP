// 动态查询服务
'use strict'

angular.module('XY').factory('SummarySheetService', ['EventService', '$log', 'PageService', 'NetworkService', 'MiscService',
    function(EventService, $log, PageService, NetworkService, MiscService) {

        var SummarySheetService = {}

        SummarySheetService.everydaySummarySheet = {}

        // 查询历史报表列表
        SummarySheetService.everydaySummarySheet.summarySheet = function(time,callback) {
            var dataToSend = {reg_time:time}
            NetworkService.request('everydaySummarySheet_summarySheet', dataToSend, function(data) {
                console.log(data);

                var tmp = {};
                angular.copy(data.data, tmp);

                tmp.statistics.skuStatistics = MiscService.formatMoneyObject(tmp.statistics.skuStatistics, ['avgPrice', 'pilePrice']);
                tmp.statistics.IorE = MiscService.formatMoneyObject(tmp.statistics.IorE, ['income']);
                if (callback) {
                    callback(tmp)
                }
            }, function() {

            })
        }

        return SummarySheetService // or return model

    }
])
