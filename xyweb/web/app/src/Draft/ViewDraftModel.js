'use strict';

angular.module('XY').factory('ViewDraftModel', ['EventService', '$log', 'PageService','QueryClass','MiscService','UserService',
    function(EventService, $log, PageService,QueryClass,MiscService,UserService) {

        // $log.debug("ViewDraftModel init");

        var model = {};

        model.query = {};
        // 页面状态
        model.pageState = {
            activeTab : 1,
        };
        model.rpg = UserService.getLoginStatus().rpg;

        //计算主体大小 计算显示行数在下面的响应事件中设置显示行数
       model.calcPline = function () {
            var bodyHeight = $(".function-body").height()
            var ajustedPline = Math.floor(bodyHeight / 36) - 3;
            return ajustedPline;
        };


        // 监听查看往来详情
        EventService.on(EventService.ev.START_VIEW_DRAFT, function(event, arg) {
            // if(NOWpage) update it 
            model.orderPage = 4;

            model.pageState.activeTab = arg;// 激活第一个选项卡
            // 新建一个查询管理对象
            model.query = QueryClass.newQuery();

            // 装定初始查询条件
            model.query.resetTime();
            model.query.setPage(1);
            var ajustedPline = model.calcPline();
            model.query.setPline(ajustedPline);
            model.query.setCid(UserService.getLoginStatus().id);
            if(UserService.getLoginStatus().rpg == 3){
                model.query.isWarehouse(true);
            }
            // 异步任务计数器
            var task = MiscService.newAsyncTask(1);
            // 设定回调
            task.setFinalProc(function() {
                // 初始查询完成后清除回调
                model.query.setCustomCallback(undefined);
                // 都完成时跳转
                PageService.setNgViewPage('NullPage');
                PageService.setNgViewPage('ViewDraft');

            });

            // 新建一个管理查询的对象
            model.query.setCustomCallback(function() {
                // 查询成功后计数
                task.finishOneProc();
            });

            // 除了草稿单据的选项卡对应的条件

            if(model.rpg!=3){
                var statusOptionArr = [{
                    optionName: '异常单据',
                    id: 2,
                }, {
                    optionName: '暂不通知库管单据',
                    id: 5,
                },];
                model.query.getStatusCheckbox().setOption(statusOptionArr);
            }
            switch (arg) {
                case 1:
                    model.query.setIsDraft(true);
                    break;
                case 2:
                    model.query.cleanTime();
                    model.query.setCid('');
                    model.query.getStatusCheckbox().selectExclusiveById(2);
                    break;
                case 3:
                    model.query.cleanTime();
                    model.query.setCid('');
                    model.query.getStatusCheckbox().selectExclusiveById(5);
                    break;
            }

            // model.query.request();

        });
        return model;

    }
]);
