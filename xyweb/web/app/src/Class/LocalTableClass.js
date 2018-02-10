// 本地表，用于给本地数据提供筛选和翻页，例如商品管理
'use strict';

angular.module('XY').factory('LocalTableClass', ['EventService', '$log', 'PageService',
    function (EventService, $log, PageService) {

        var LocalTableClass = {};

        // 本地表类
        // @param [] _primDataArr   原始数据数组
        // @param [] _filterArr     过滤条件数量

        // _filterArr 是一个对象数组，数组中每个对象代表一个筛选条件，筛选条件由3部分组成，字段名(fieldName)、值(value)、匹配模式(mode)，
        // 在一个筛选条件中可以有多个被匹配的字段名(fieldName是数组)，只要其中一个匹配即匹配
        // 多个筛选条件之间是与的关系
        // mode为匹配模式，1表示value是子串即可，2表示字符串相等

        // _filterArr e.g.
        // [
        //  {
        //      fieldName: ['spu_name','cat'],
        //      value: '123',
        //      mode:1,
        //  },
        //  {
        //      fieldName:['spec'],
        //      value: '456',
        //      mode:2,
        //  }
        // ]
        function LocalTable(_primDataArr, _filterArr, _pline, _type) {
            // $log.log('start LocalTable constructor', _primDataArr, _filterArr)

            var primDataArr = _primDataArr;
            var type = _type;
            var pline = _pline || 10; // 每页多少行
            var now_page = 1;// 当前显示的页码
            var tot_page; // 总共页数
            var filter = _filterArr; // 过滤器
            var filterNum = _filterArr.length;
            var filteredData = []; // 已经过过滤的数据
            var showData = []; // 分好页用于展示的数据
            var pageList = []; // 用于显示的页码
            var stock_price = 0;   //用于显示查看库存显示的总金额
            var stock_quantity = 0;//用于显示查看库存显示的总数量
            var totalInfo = {};
            totalInfo.totalValue = 0;
            totalInfo.totalStock = 0;

            // $log.debug('income filter = ', _filterArr)

            // 初始化的时候进行过滤
            calc();
            this.getLastPage = function () {
                return tot_page;
            };

            this.getPline = function () {
                return pline;
            };
            this.getAllData = function () {
                return filteredData
            };

            // 获取当前页码
            this.getNowPage = function () {
                return now_page
            };

            // 手动触发刷新
            this.calc = function () {
                calc();
            };
            this.getTotalInfo = function () {
                return totalInfo
            };

            // 获取过滤信息
            this.getFilter = function () {
                return filter
            };
            // 获取过滤信息
            this.getFilteredData = function () {
                return filteredData
            };
            // 获取用于显示的信息
            this.getShowData = function () {
                return showData
            };

            // 获取页码列表
            this.getPageList = function () {
                return pageList
            };

            //获取总金额
            this.getStockPrice = function () {
                return stock_price;
            };

            //获取总数量
            this.getStockQuantity = function () {
                return stock_quantity;
            };
            // 改变筛选条件(经常被调用)
            // int filterNo 筛选条件编号
            // str value    新值
            this.changeFilterCondition = function (conditionNo, value) {
                filter[conditionNo].value = value;
                now_page = 1; // 筛选条件改变时页码自动回到1
                this.calc();
            };

            // 改变当前显示页码
            // @param {} 页码信息 (应该来自pageList)
            this.changeNowPage = function (pageObj) {
                $log.debug('changeNowPage pageObj = ', pageObj)
                if (pageObj.active) {
                    if (pageObj.action > 0) {
                        pageObj.action == 1 ? paginate(now_page - 1) : 1;
                        pageObj.action == 2 ? paginate(now_page + 1) : 1;
                        paginate(now_page)
                    } else {
                        paginate(pageObj.content)
                    }
                }
            };

            this.changePlineByPage = function (_pline, _page) {
                pline = _pline;
                paginate(_page)
            };

            // 改变每页显示数量
            this.changePline = function (_pline) {
                pline = _pline;
                // 重新分页装填showData
                paginate(1)
            };

            // 进行过滤，就是一个for循环逐个字段匹配
            function calc() {

                var tmpOut = [];

                for (var i = 0; i < primDataArr.length; i++) {
                    // $log.debug('prim[i]=', primDataArr[i])
                    var flag_pass = true;
                    for (var j = 0; j < filterNum; j++) {
                        var t = check(primDataArr[i], filter[j]);
                        if (!t) {
                            flag_pass = false;
                            break
                        }
                    }
                    // 如果通过筛选则加入过滤后数组
                    if (flag_pass) {
                        var tmpDataObj = {};
                        angular.copy(primDataArr[i], tmpDataObj);
                        // $log.error(i,"+",tmpDataObj);
                        tmpOut.push(tmpDataObj)
                        // $log.error(tmpOut+"tmpOut");
                    }
                }
                totalInfo.totalValue = 0;
                totalInfo.totalStock = 0;
                for (var item of tmpOut) {
                    totalInfo.totalValue += Number(item.total_price);
                    totalInfo.totalStock += Number(item.stock);
                }
                // 填入已过滤数据
                angular.copy(tmpOut, filteredData);
                // $log.error("filteredData",filteredData);
                // $log.debug('filteredData = ', filteredData)
                paginate(1);
                EventService.emit(EventService.ev.LOCAL_TABLE_FILTER_DONE);
            }

            //一个筛选条件的筛选
            function check(input, filterObj) {
                var fieldName = filterObj.fieldName;
                var value = filterObj.value.toString();
                var mode = filterObj.mode;
                var flag_pass = false;
                for (var i = 0; i < fieldName.length; i++) {
                    if (mode == 2) {
                        if (value == input[fieldName[i]]) {
                            flag_pass = true
                        }
                    }
                    // 模式1为子串匹配
                    if (mode == 1) {
                        if (input[fieldName[i]].toString().indexOf(value) >= 0) {
                            flag_pass = true
                        }
                    }
                    // *表示任意
                    if (value == '*') {
                        flag_pass = true
                    }
                }
                // $log.debug('check,input,filterObj = ', input, filterObj)
                // $log.debug('result = ', flag_pass)
                return flag_pass
            }

            // 分页(前提是已经有filterdData了)
            // @param int _now_page 当前页码
            function paginate(_now_page) {

                // $log.debug('in paginate, _now_page = ',_now_page)

                // 把服务器返回的当前页页码和总页数存住
                now_page = Number(_now_page);

                // filteredData = []
                // for (var i = 0; i < 100; i++) {
                //     filteredData.push(i)
                // }

                // 计算总页数
                var dataLength = filteredData.length
                tot_page = Math.ceil(dataLength / pline)
                // 确定当前目标页码在总页码范围内
                if (now_page < 1) {
                    now_page = 1
                }

                if (now_page > tot_page) {
                    now_page = tot_page
                }

                // 装填要显示的当前页内容
                var nowPageIndex = 0 + pline * (now_page - 1)
                var tmpShowData = filteredData.slice(nowPageIndex, nowPageIndex + pline);
                angular.copy(tmpShowData, showData);
                //计算查看库存页的总计(金额和数量)
                //(过滤后的而不是每页的如果是显示每页总计的就用showData,过去后的用filteredData。)
                stock_price = 0;
                stock_quantity = 0;
                // $log.error("showData",showData);
                //统计结束 计算结果加入showData
                if (tot_page <= 0) {
                    tmpPageList = []
                } else {
                    var leftEnd = false; //左侧已不能再添加按钮(1已经在里面了)
                    var rightEnd = false; //右侧已不能再添加按钮(tot_page在里面了)
                    var pageToAdd = 6; // 还要尝试添加的按钮数量
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
                            tmpPageList.unshift({content: '…', isNowPage: false,});
                        }
                        // 加入页码1按钮
                        tmpPageList.unshift({content: 1, isNowPage: false, active: true});
                    }

                    var largestPageNum = tmpPageList[tmpPageList.length - 1].content;
                    if (largestPageNum == tot_page) {

                    } else {
                        if (largestPageNum < tot_page - 1) {
                            tmpPageList.push({content: '…', isNowPage: false,});
                        }
                        tmpPageList.push({content: tot_page, isNowPage: false, active: true});
                    }
                }

                tmpPageList.unshift({content: '<', isNowPage: false, action: 1, active: true}) //action 1 表示上一页
                tmpPageList.push({content: '>', isNowPage: false, action: 2, active: true}) //action 1 表示下一页
                angular.copy(tmpPageList, pageList);

                // $log.debug('pageList = ', pageList)

            }

        }

        LocalTableClass.newLocalTable = function (_primDataArr, _filterArr, _pline, _type) {
            var out = new LocalTable(_primDataArr, _filterArr, _pline, _type);
            return out;
        }

        // var prim = [{ a: 10, b: 1, c: '888', d: 222 }, { a: 33, b: 22, c: 1, d: 888 }]
        // var _filter = [{
        //     fieldName: ['c', 'd'],
        //     value: '888',
        //     mode: 1,
        // }, {
        //     fieldName: ['b'],
        //     value: 2,
        //     mode: 1,
        // }]
        //
        // var t = LocalTableClass.newLocalTable(prim, _filter, 5)
        // // $log.log('t = ', t)
        //
        // var ar = [0, 1, 2, 3, 4, 5, 6]
        //
        // var y = ar.slice(3, 10)
        // // $log.debug('y=', y)

        return LocalTableClass; // or return model

    }
]);
