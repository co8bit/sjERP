'use strict'

angular.module('XY').factory('SummarySheetModel', ['EventService', '$log', 'PageService', 'QueryClass', 'SummarySheetService','MiscService',
    function(EventService, $log, PageService, QueryClass, SummarySheetService, MiscService) {

        var model = {}

        model.isToday = false
        model.query = QueryClass.newQueryForSummary()
        model.todaySummarySheet = {}
        model.todayPageStete = 1 // 表示激活汇总表

        // 查看每日详情列表
        model.viewEverydaySheet = function() {
            model.query.cleanTime()
            model.query.request()
        }

        // 查看某日的详情
        model.viewSomedaySheet = function(time) {
            model.isToday = false
            SummarySheetService.everydaySummarySheet.summarySheet(time, function(data) {
                MiscService.genShowContent(data.statistics.IorE)
                angular.copy(data, model.todaySummarySheet);
                countTodayTotal();

                var reg_date = new Date();
                reg_date.setTime(data.reg_time * 1000);
                model.reg_date2 = reg_date.toLocaleDateString();

                PageService.setNgViewPage('TodaySummarySheet');
            })
        }


        // 查看实时数据
        EventService.on(EventService.ev.START_VIEW_TODAY_SUMMARY_SHEET, function() {
        	var time = Math.floor(new Date().getTime() / 1000)
            model.isToday = true
            SummarySheetService.everydaySummarySheet.summarySheet(time, function(data) {
                MiscService.genShowContent(data.statistics.IorE)
                angular.copy(data, model.todaySummarySheet);
                countTodayTotal();

                var reg_date = new Date();
                reg_date.setTime(data.reg_time * 1000);
                model.reg_date2 = reg_date.toLocaleDateString();
                
                PageService.setNgViewPage('TodaySummarySheet');
            })
        })

        function countTodayTotal() {
            model.todaySummarySheet.total = {quantity:0,price:0,income:0,profit:0,cost:0,avgPrice:0};
            for (var value in model.todaySummarySheet.statistics.skuStatistics)
            {

                model.todaySummarySheet.total.quantity += model.todaySummarySheet.statistics.skuStatistics[value].quantity;
                $log.debug(model.todaySummarySheet.statistics.skuStatistics[value]);
                model.todaySummarySheet.total.price += parseFloat(model.todaySummarySheet.statistics.skuStatistics[value].pilePrice);
                model.todaySummarySheet.total.profit += parseFloat(model.todaySummarySheet.statistics.skuStatistics[value].gross_profit);
                model.todaySummarySheet.total.cost += parseFloat(model.todaySummarySheet.statistics.skuStatistics[value].cost);
            }
            for (var value in model.todaySummarySheet.statistics.IorE)
            {
                model.todaySummarySheet.total.income += parseFloat(model.todaySummarySheet.statistics.IorE[value].income);
            }
            model.todaySummarySheet.total.income = Math.round(model.todaySummarySheet.total.income * 100)/100;
            model.todaySummarySheet.total.price = Math.round(model.todaySummarySheet.total.price * 100)/100;
            $log.debug(model.todaySummarySheet.total);
        }
        
        return model // 一定要返回对象

    }
])
