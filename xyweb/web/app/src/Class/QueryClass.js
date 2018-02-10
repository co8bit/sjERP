'use strict';

angular.module('XY').factory('QueryClass', ['EventService', '$log', 'PageService', 'ClassService', 'CheckboxWidgetClass', 'QueryService',
    function (EventService, $log, PageService, ClassService, CheckboxWidgetClass, QueryService) {

        var QueryClass = {};

        function Paginator() {

            this.pageList = [];
            this.now_page = undefined; // 当前页码
            this.tot_page = undefined; // 总页数

            // 除了本页和收尾页外额外显示的页数
            var extraNum = 6;
            // var minPage = 999;

            var leftEnd = false;
            var rightEnd = false;

            // 状态改变时的回调
            var stateChangeCallback = function () {
                $log.warn('in paginator, stateChangeCallback not set but called!');
            }

            // var pageUnit = {
            //     content: 'toBeSet',
            //     active: false,
            //     callback: undefined,
            // }

            // 构造
            this.PaginatorCtor = function (callback) {
                stateChangeCallback = callback;
            }

            // 设置状态变化回调
            this.setStateChangeCallback = function (callback) {
                stateChangeCallback = callback;
            }

            // 除了本页之外额外显示的页数(不包括首尾页)
            this.setExtraNum = function (num) {
                extraNum = num;
            }

            this.clickButton = function (pageItem) {
                var content = pageItem.content;
                switch (content) {
                    case '<':
                        if (this.now_page > 1) {
                            stateChangeCallback(this.now_page - 1);
                        }
                        break;
                    case '>':
                        if (this.now_page < this.tot_page) {
                            stateChangeCallback(this.now_page + 1);
                        }
                        break;
                    case '…':
                        break;
                    default:
                        stateChangeCallback(content);
                }
            }

            // 设定新的分页状态
            this.setPageState = function (now_page, tot_page) {
                // 把服务器返回的当前页页码和总页数存住
                now_page = Number(now_page);
                tot_page = Number(tot_page);
                this.now_page = now_page;
                this.tot_page = tot_page;
                leftEnd = false; //左侧已不能再添加按钮(1已经在里面了)
                rightEnd = false; //右侧已不能再添加按钮(tot_page在里面了)
                var pageToAdd = extraNum; // 还要尝试添加的按钮数量
                var tmpPageList = []; // 清空按钮数组
                // 添加本页按钮到数组中
                var tmp = {
                    content: now_page,
                    isNowPage: true,
                    active: true,
                };
                tmpPageList.push(tmp);
                // 先尝试把pageToAdd数量的按钮加到本页按钮两边
                while ((pageToAdd > 0) && (!leftEnd || !rightEnd)) {
                    // 尝试往右边加1个
                    // $log.log('while tmpPageList = ', tmpPageList);

                    var len = tmpPageList.length;
                    var rightNum = Number(tmpPageList[len - 1].content);
                    if (rightNum < tot_page) {
                        tmp = {
                            content: rightNum + 1,
                            isNowPage: false,
                            active: true,
                        }
                        tmpPageList.push(tmp);
                        pageToAdd--;
                    } else {
                        rightEnd = true;
                    }
                    // 尝试往左边加1个
                    var leftNum = Number(tmpPageList[0].content);
                    if (leftNum > 1) {
                        tmp = {
                            content: leftNum - 1,
                            isNowPage: false,
                            active: true,
                        }
                        tmpPageList.unshift(tmp);
                        pageToAdd--;
                    } else {
                        leftEnd = true;
                    }
                }
                // 检查是否需要添加首页和尾页以及省略号
                // 页码1已经在里面，什么都不用做
                if (tmpPageList[0].content == 1) {

                    // 没有页码1，加入页码1
                } else {
                    // 如果当前最小页码大于2，1和2之间需要加入省略号
                    if (tmpPageList[0].content > 2) {
                        tmpPageList.unshift({
                            content: '…',
                            isNowPage: false,
                        });
                    }
                    // 加入页码1按钮
                    tmpPageList.unshift({
                        content: 1,
                        isNowPage: false,
                    });
                }

                var largestPageNum = tmpPageList[tmpPageList.length - 1].content;
                if (largestPageNum == tot_page) {

                } else {
                    if (largestPageNum < tot_page - 1) {
                        tmpPageList.push({
                            content: '…',
                            isNowPage: false,
                        });
                    }
                    tmpPageList.push({
                        content: tot_page,
                        isNowPage: false,
                    });
                }

                if (tmpPageList) {
                    angular.copy(tmpPageList, this.pageList);
                } else {
                    this.pageList = [];
                }
            }
        }

        QueryClass.newPaginator = function (callback) {
            var out = new Paginator();
            out.PaginatorCtor(callback);
            return out;
        }

        //库存详情查询专用
        function QuerySkuSummary() {

            var sku_id = undefined;
            var sto_id = undefined;

            var now_page = 1;
            var tot_page = 1;
            var pline = 30;
            var data = [];
            var timeData = {};
            var custumCallback = undefined;
            // 过滤器
            var classCheckbox = undefined;    //单据过滤器
            var statusCheckbox = undefined;    //状态过滤器
            var remainTypeCheckbox = undefined;    // 是否产生应收应付过滤器
            var bigClassCheckbox = undefined;    //类别过滤器
            var financeTypeCheckbox = undefined;    //收款方式过滤器

            //时间过滤器
            var timeFilter = {
                reg_st_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
                reg_end_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
            }
            //设置sku_id
            this.setSku_id = function (num) {
                sku_id = num;
            }
            this.setSto_id = function (num) {
                sto_id = num;
            }
            //设置当前显示页
            this.setPage = function (num) {
                now_page = num;
            }
            //设置显示行数
            this.setPline = function (num) {
                pline = num;
            }
            // 清除时间
            this.cleanTime = function () {
                timeFilter.reg_st_time = undefined;
                timeFilter.reg_end_time = undefined;
            }
            // 设置时间
            this.setStartTime = function (time) {
                timeFilter.reg_st_time = time;
            }
            this.setEndTime = function (time) {
                timeFilter.reg_end_time = time;
            }
            // 改变时间并触发通信
            this.changeStartTime = function (time) {
                timeFilter.reg_st_time = time;
                request();
            }
            this.changeEndTime = function (time) {
                timeFilter.reg_end_time = time;
                request();
            }
            //获取时间
            this.getTimeFilter = function () {
                return timeFilter;
            }

            // 获取类别过滤器
            this.getClassCheckbox = function () {
                return classCheckbox;
            }

            this.getStatusCheckbox = function () {
                return statusCheckbox;
            }
            this.getremainTypeCheckbox = function () {
                return remainTypeCheckbox;
            }
            this.getFinanceTypeCheckbox = function () {
                return financeTypeCheckbox;
            }
            this.getBigClassCheckbox = function () {
                return bigClassCheckbox;
            }

            this.getPaginator = function () {
                return paginator;
            }

            this.request = function () {
                request();
            }

            this.setCustomCallback = function (callback) {
                custumCallback = callback;
            }

            this.getData = function () {
                return data;
            }

            this.getRealTimeData = function () {
                return timeData;
            }

            // 给到分页器的回调
            var pageChangeCallback = function (targetPage) {
                now_page = targetPage;
                request();
            }

            //监听checkbox
            var checkboxChangeCallback = function () {
                now_page = 1; // 查询第一页
                request(); // 查询
            }

            var paginator = QueryClass.newPaginator(pageChangeCallback);

            this.QueryCtor = function () {
                classCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                statusCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                remainTypeCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                financeTypeCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                bigClassCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
            };

            //请求数据
            var request = function () {
                var dataToSend = {};
                dataToSend.filter = {};
                dataToSend.filter.class = [];
                angular.copy(classCheckbox.filterData, dataToSend.filter.class);
                dataToSend.filter.status = [];
                angular.copy(statusCheckbox.filterData, dataToSend.filter.status);
                dataToSend.filter.remainType = [];
                angular.copy(remainTypeCheckbox.filterData, dataToSend.filter.remainType);
                dataToSend.filter.bigClass = [];
                angular.copy(bigClassCheckbox.filterData, dataToSend.filter.bigClass);
                dataToSend.filter.financeType = [];
                angular.copy(financeTypeCheckbox.filterData, dataToSend.filter.financeType);

                dataToSend.filter = JSON.stringify(dataToSend.filter);

                dataToSend.page = now_page;
                dataToSend.pline = pline;

                dataToSend.sku_id = sku_id;
                dataToSend.sto_id = sto_id;
                // 如果有时间的话加入时间,发送时转为毫秒数
                if (timeFilter.reg_st_time) {
                    dataToSend.reg_st_time = Math.floor(new Date(timeFilter.reg_st_time).getTime() / 1000);
                }
                if (timeFilter.reg_end_time) {
                    dataToSend.reg_end_time = Math.floor(new Date(timeFilter.reg_end_time).getTime() / 1000);
                }
                //请求数据
                QueryService.query.skuSummary(dataToSend, requestCallback);
                QueryService.query.realTime(dataToSend, realTimeCallback);
            }
            // 请求回调
            var requestCallback = function (respondData) {
                now_page = respondData.now_page;
                tot_page = respondData.total_page;
                paginator.setPageState(now_page, tot_page);
                angular.copy(respondData.data, data);
                if (custumCallback) {
                    custumCallback();
                }
            }
            var realTimeCallback = function (respondData) {
                angular.copy(respondData.data, timeData);
            }
        }

        QueryClass.newQuerySkuSummary = function () {
            var out = new QuerySkuSummary();
            out.QueryCtor();
            return out;
        }
        // 动态查询类
        // 管理一个动态查询
        function Query() {
            var cid = undefined;
            var operator_uid = undefined;
            var isDraft = false; // 是否是要查询草稿,会导致request中的发送请求的逻辑不一样;
            var isWarehouse = false; //是否库管登陆
            var isYqj = false; //是否易企记查询
            var now_page = 1;
            var tot_page = 1;
            var pline = 30;
            var data = [];
            var custumCallback = undefined;
            // 过滤器
            var classCheckbox = undefined;      //单据过滤器
            var statusCheckbox = undefined;     //状态过滤器
            var remainTypeCheckbox = undefined; // 是否产生应收应付过滤器
            var bigClassCheckbox = undefined; //类别过滤器
            var financeTypeCheckbox = undefined;     //收款方式过滤器
            var isInitialOrderShow = true; //
            var timeFilter = {
                reg_st_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
                reg_end_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
            }

            this.getTimeFilter = function () {
                return timeFilter;
            }

            this.setUid = function(__uid){
                operator_uid = __uid;
            }
            // 清除时间
            this.cleanTime = function () {
                timeFilter.reg_st_time = undefined;
                timeFilter.reg_end_time = undefined;
            }

            // 初始化时间
            this.resetTime = function () {
                timeFilter.reg_st_time = new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString();
                timeFilter.reg_end_time = new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString();
            }

            // 设置时间
            this.setStartTime = function (time) {
                timeFilter.reg_st_time = time;
            }
            this.setEndTime = function (time) {
                timeFilter.reg_end_time = time;
            }

            // 改变时间并触发通信
            this.changeStartTime = function (time) {
                timeFilter.reg_st_time = time;
                request();
            }

            this.changeEndTime = function (time) {
                timeFilter.reg_end_time = time;
                request();
            }

            this.setIsInitialOrderShow = function (isShow) {
                isInitialOrderShow = isShow;
            }

            // 请求数据
            var request = function (callback_success) {
                if (!isDraft) {
                    // 将各过滤条件装填到发送数据
                    var dataToSend = {};
                    if (!isInitialOrderShow) {
                        dataToSend.isInitialOrderShow = false;
                    }
                    dataToSend.filter = {};
                    dataToSend.filter.class = [];
                    angular.copy(classCheckbox.filterData, dataToSend.filter.class);
                    dataToSend.filter.status = [];
                    angular.copy(statusCheckbox.filterData, dataToSend.filter.status);
                    dataToSend.filter.remainType = [];
                    angular.copy(remainTypeCheckbox.filterData, dataToSend.filter.remainType);
                    dataToSend.filter.bigClass = [];
                    angular.copy(bigClassCheckbox.filterData, dataToSend.filter.bigClass);
                    dataToSend.filter.financeType = [];
                    angular.copy(financeTypeCheckbox.filterData, dataToSend.filter.financeType);
                    //
                    // if (isWarehouse) { //如果是库管只能看盘点单的草稿
                    //     dataToSend.filter.class = [53];
                    // }
                    dataToSend.filter = JSON.stringify(dataToSend.filter);
                    if (cid) {
                        dataToSend.cid = cid;
                    }
                    dataToSend.page = now_page;
                    dataToSend.pline = pline;

                    // 如果有时间的话加入时间,发送时转为毫秒数
                    if (timeFilter.reg_st_time) {
                        dataToSend.reg_st_time = Math.floor(new Date(timeFilter.reg_st_time).getTime() / 1000);
                    }
                    if (timeFilter.reg_end_time) {
                        dataToSend.reg_end_time = Math.floor(new Date(timeFilter.reg_end_time).getTime() / 1000);
                    }
                    if(operator_uid){
                        dataToSend.operator_uid = operator_uid;
                    }
                    // 请求数据
                    QueryService.query.query(dataToSend, requestCallback);
                } else {
                    var dataToSend = {};
                    dataToSend.page = now_page;
                    dataToSend.pline = pline;
                    //dataToSend.filter =  '{"status":[],"status":[],"remainType":[],"bigClass":[],"financeType":[]
                    dataToSend.filter = '{"status":[100]}';
                    if (isWarehouse) {
                        //库管只能看盘点单的草稿
                        dataToSend.filter = '{"class":[53],"status":[100]}'; //,"status":[],"remainType":[],"bigClass":[],"financeType":[]
                    }
                    if (isYqj) {
                        //只查询易企记的草稿单
                        dataToSend.filter = '{"status":[89]}'; //,"status":[],"remainType":[],"bigClass":[],"financeType":[]
                    }
                    QueryService.query.queryDraft(dataToSend, requestCallback);
                }
            }

            // 设置查询模式
            this.setIsDraft = function (__isDraft) {
                // $log.log(1);
                isDraft = __isDraft;
                request();
            }

            this.isWarehouse = function (__isWarehouse) {
                isWarehouse = __isWarehouse
                request();
            }

            this.setYqj = function (__isYqj) {
                isYqj = __isYqj;
            }
            // 选择了查询草稿
            this.selectDraft = function () {
                isDraft = true;
                now_page = 1;
                request();
            }

            // 给到分页器的回调
            var pageChangeCallback = function (targetPage) {
                now_page = targetPage;
                request();
            }
            //监听checkbox
            var checkboxChangeCallback = function () {
                isDraft = false; // 切换至非草稿模式
                now_page = 1; // 查询第一页

                //checkbox改变通知使用者
                EventService.emit(EventService.ev.CHANGE_CHECKBOX);

                request(); // 查询
            }

            var paginator = QueryClass.newPaginator(pageChangeCallback);

            this.QueryCtor = function () {
                classCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                statusCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                remainTypeCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                financeTypeCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
                bigClassCheckbox = CheckboxWidgetClass.newCheckboxWidget(checkboxChangeCallback);
            }

            this.setCid = function (num) {
                cid = num;
            }
            this.setPage = function (num) {
                now_page = num;
            }
            this.setPline = function (num) {
                pline = num;
            }

            // 获取类别过滤器
            this.getClassCheckbox = function () {
                return classCheckbox;
            }

            this.getStatusCheckbox = function () {
                return statusCheckbox;
            }
            this.getremainTypeCheckbox = function () {
                return remainTypeCheckbox;
            }
            this.getFinanceTypeCheckbox = function () {
                return financeTypeCheckbox;
            }
            this.getBigClassCheckbox = function () {
                return bigClassCheckbox;
            }

            this.getPaginator = function () {
                return paginator;
            }

            this.request = function () {
                request()
            }

            // 请求回调
            var requestCallback = function (respondData) {
                now_page = respondData.now_page;
                tot_page = respondData.total_page;
                paginator.setPageState(now_page, tot_page);
                // data = respondData.data;
                angular.copy(respondData.data, data);
                // $log.log('query respondData: ', respondData);
                if (custumCallback) {
                    custumCallback();
                }
            }

            this.setCustomCallback = function (callback) {
                custumCallback = callback;
            }

            this.getData = function () {
                return data;
            }

        }

        QueryClass.newQuery = function () {
            var out = new Query();
            out.QueryCtor();
            return out;
        }

        // 给标题栏搜索用的Query,这里有
        function QueryForSearch() {

            var now_page = 1;
            var tot_page = 1;
            var pline = 5;
            var data = [];
            var custumCallback = undefined;

            var search = ''; //搜索的内容
            var type = 1; // 要搜索的类型

            // 请求数据
            var request = function () {
                // 将各过滤条件装填到发送数据
                var dataToSend = {};

                dataToSend.page = now_page;
                dataToSend.pline = pline;
                //
                if (search == undefined) {
                    search = '';
                }
                dataToSend.search = search;
                dataToSend.type = Number(type);

                // 请求数据
                QueryService.query.search(dataToSend, requestCallback);
            }

            this.cleanData = function () {
                angular.copy({}, data);
            }

            // 搜索的内容
            this.getSearchContent = function () {
                return search;
            }

            this.setType = function (num) {
                type = num;
            }

            this.setSearch = function (str) {
                search = str;
            }

            // 给到分页器的回调
            var pageChangeCallback = function (targetPage) {
                now_page = targetPage;
                request();
            }

            var paginator = QueryClass.newPaginator(pageChangeCallback);

            this.QueryCtor = function () {

            }

            this.setPage = function (num) {
                now_page = num;
            }
            this.setPline = function (num) {
                pline = num;
            }

            this.getPaginator = function () {
                return paginator;
            }

            this.request = function () {
                request();
            }

            // 请求回调
            var requestCallback = function (respondData) {
                now_page = respondData.now_page;
                tot_page = respondData.total_page;
                paginator.setPageState(now_page, tot_page);
                // data = respondData.data;
                angular.copy(respondData.data, data);

                //如果搜索的是商品(type == 2)的话计算一下总价,api返回字段不含总价
                if (type == 2) {
                    for (var i = 0; i < data.length; i++) {
                        data[i].tot_price = data[i].unit_price * data[i].stock;
                    }
                }

                // $log.log('query respondData: ', respondData);
                if (custumCallback) {
                    custumCallback();
                }
            }

            this.setCustomCallback = function (callback) {
                custumCallback = callback;
            }

            this.getData = function () {
                return data;
            }

        }

        QueryClass.newQueryForSearch = function () {
            var out = new QueryForSearch();
            return out;
        }

        // 用于报表的查询
        function QueryForSummary() {

            var now_page = 1;
            var tot_page = 1;
            var pline = 30;
            var data = [];
            var custumCallback = undefined;

            var timeFilter = {
                reg_st_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
                reg_end_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
            }

            this.getTimeFilter = function () {
                return timeFilter;
            }

            // 清除时间
            this.cleanTime = function () {
                timeFilter.reg_st_time = undefined;
                timeFilter.reg_end_time = undefined;
            }

            // 初始化时间
            this.resetTime = function () {
                timeFilter.reg_st_time = new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString();
                timeFilter.reg_end_time = new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString();
            }

            // 设置时间
            this.setStartTime = function (time) {
                timeFilter.reg_st_time = time;
            }
            this.setEndTime = function (time) {
                timeFilter.reg_end_time = time;
            }

            // 改变时间并触发通信
            this.changeStartTime = function (time) {
                timeFilter.reg_st_time = time;
                request();
            }

            this.changeEndTime = function (time) {
                timeFilter.reg_end_time = time;
                request();
            }

            // 请求数据
            var request = function () {

                // 将各过滤条件装填到发送数据
                var dataToSend = {};

                dataToSend.page = now_page;
                dataToSend.pline = pline;
                // 如果有时间的话加入时间,发送时转为毫秒数
                if (timeFilter.reg_st_time) {
                    dataToSend.reg_st_time = Math.floor(new Date(timeFilter.reg_st_time).getTime() / 1000);
                }
                if (timeFilter.reg_end_time) {
                    dataToSend.reg_end_time = Math.floor(new Date(timeFilter.reg_end_time).getTime() / 1000);
                }
                // 请求数据
                QueryService.everydaySummarySheet.query(dataToSend, requestCallback);

            }

            // 给到分页器的回调
            var pageChangeCallback = function (targetPage) {
                now_page = targetPage;
                request();
            }

            var paginator = QueryClass.newPaginator(pageChangeCallback);

            this.QueryCtor = function () {

            }

            this.setPage = function (num) {
                now_page = num;
            }
            this.setPline = function (num) {
                pline = num;
            }


            this.getPaginator = function () {
                return paginator;
            }

            this.request = function () {
                request();
            }

            // 请求回调
            var requestCallback = function (respondData) {
                now_page = respondData.now_page;
                tot_page = respondData.total_page;
                paginator.setPageState(now_page, tot_page);
                // data = respondData.data;
                angular.copy(respondData.data, data);
                // $log.log('query respondData: ', respondData);
                if (custumCallback) {
                    custumCallback();
                }
            }

            this.setCustomCallback = function (callback) {
                custumCallback = callback;
            }

            this.getData = function () {
                return data;
            }

        }

        // 报表查询
        QueryClass.newQueryForSummary = function () {
            var out = new QueryForSummary();
            out.QueryCtor();
            return out;
        }

        function queryForExpense() {
            var now_page = 1;
            var tot_page = 1;
            var pline = 8;
            var type = 1;
            var data = [];
            var custumCallback = undefined;

            var timeFilter = {
                reg_st_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
                reg_end_time: new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString(),
            }

            this.getTimeFilter = function () {
                return timeFilter;
            }

            this.setType = function (num) {
                type = num;
            }

            // 清除时间
            this.cleanTime = function () {
                timeFilter.reg_st_time = undefined;
                timeFilter.reg_end_time = undefined;
            }

            // 初始化时间
            this.resetTime = function () {
                timeFilter.reg_st_time = new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString();
                timeFilter.reg_end_time = new Date().getFullYear().toString() + '/' + (new Date().getMonth() + 1).toString() + '/' + new Date().getDate().toString();
            }

            // 设置开始时间
            this.setStartTime = function (time) {
                timeFilter.reg_st_time = time;
            }

            // 设置结束时间
            this.setEndTime = function (time) {
                timeFilter.reg_end_time = time;
            }

            // 改变开始时间并触发通信
            this.changeStartTime = function (time) {
                timeFilter.reg_st_time = time;
                request();
            }

            // 改变结束时间并触发通信
            this.changeEndTime = function (time) {
                timeFilter.reg_end_time = time;
                request();
            }

            // 请求数据
            var request = function () {

                // 将各过滤条件装填到发送数据
                var dataToSend = {};

                dataToSend.page = now_page;
                dataToSend.pline = pline;
                dataToSend.type = type;

                // 如果有时间的话加入时间,发送时转为毫秒数
                if (timeFilter.reg_st_time) {
                    dataToSend.reg_st_time = Math.floor(new Date(timeFilter.reg_st_time).getTime() / 1000);
                }
                if (timeFilter.reg_end_time) {
                    dataToSend.reg_end_time = Math.floor(new Date(timeFilter.reg_end_time).getTime() / 1000);
                }
                // 请求数据
                QueryService.query.expense(dataToSend, requestCallback);

            }

            // 给到分页器的回调
            var pageChangeCallback = function (targetPage) {
                now_page = targetPage;
                request();
            }

            var paginator = QueryClass.newPaginator(pageChangeCallback);

            this.QueryCtor = function () {

            }

            this.setPage = function (num) {
                now_page = num;
            }

            this.setPline = function (num) {
                pline = num;
            }

            this.getPaginator = function () {
                return paginator;
            }

            this.request = function () {
                request();
            }

            // 请求回调
            var requestCallback = function (respondData) {
                now_page = respondData.now_page || 1;
                tot_page = respondData.total_page || 1;
                paginator.setPageState(now_page, tot_page);
                // data = respondData.data;
                angular.copy(respondData.data, data);
                // $log.log('query respondData: ', respondData);
                if (custumCallback) {
                    custumCallback();
                }
            }

            this.setCustomCallback = function (callback) {
                custumCallback = callback;
            }

            this.getData = function () {
                return data;
            }
        }

        QueryClass.newQueryForExpense = function () {
            var out = new queryForExpense();
            out.QueryCtor();
            return out;
        }


        return QueryClass;
    }


]);