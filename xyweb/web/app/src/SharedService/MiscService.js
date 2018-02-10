angular.module('XY').factory('MiscService', ['EventService', '$log', 'PageService', 'ClassService', '$filter',
    function (EventService, $log, PageService, ClassService, $filter) {

        var MiscService = {}

        // 是否是可以用于价格的数字
        MiscService.testValue = function (num) {
            if (!isNaN(num)) {
                num = num.toString();
            }
            var patt = /(([1-9]\d*)|0)(\.\d{0,2})?/ //整数部分如果0开头只能是0

            var r = num.match(patt)

            // $log.debug(r)
            if (r) {
                if (r[0].length == num.length) {
                    // $log.debug('pass')
                    return true
                } else {
                    // $log.debug('fail')
                    return false
                }
            } else {
                // $log.debug('fail')
                return false
            }
        }

        MiscService.testMobile = function(){
            //检验是否为电脑端
            var isMobile=false;
        //$log.log('llllll',isMobile); //使用平台
            var sUserAgent = navigator.userAgent.toLowerCase();
            var bIsIpad = sUserAgent.match(/ipad/i) == "ipad";
            var bIsIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
            var bIsMidp = sUserAgent.match(/midp/i) == "midp";
            var bIsUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
            var bIsUc = sUserAgent.match(/ucweb/i) == "ucweb";
            var bIsAndroid = sUserAgent.match(/android/i) == "android";
            var bIsCE = sUserAgent.match(/windows ce/i) == "windows ce";
            var bIsWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
            // document.writeln("您的浏览设备为：");
            if (bIsIpad || bIsIphoneOs || bIsMidp || bIsUc7 || bIsUc || bIsAndroid || bIsCE || bIsWM) {
                isMobile = true;
                // document.writeln("phone");
            } else {
                isMobile =false;
                // document.writeln("pc");
            }
            return isMobile;
        }

        //检测11个数字
        MiscService.testElevenNum = function (str) {
            str = str.toString()
            var patt = /\d{11}/
            var r = str.match(patt)
            if (r) {
                if (r[0].length == str.length) {
                    // $log.debug('pass')
                    return true
                } else {
                    // $log.debug('fail')
                    return false
                }
            } else {
                // $log.debug('fail')
                return false
            }
        }



        // 检测是否是可以用于数量的数字
        MiscService.testQuantity = function (num) {
            num = num.toString();
            if (num) {
                var patt = /\d*\.?\d*/

                var r = num.match(patt)

                // $log.debug(r)
                if (r) {
                    if (r[0].length == num.length) {
                        // $log.debug('pass')
                        return true
                    } else {
                        // $log.debug('fail')
                        return false
                    }
                } else {
                    // $log.debug('fail')
                    return false
                }
            }
        }

        function AsyncTask() {
            this.class_id = 'AsyncTask'
                // this.procList = []
            var emptyFunc = function () {
                $log.log('emptyFunc in AsyncTask')
            }
            this.procLeftNum = 0
            this.finalProc = emptyFunc

            this.AsyncTaskCtor = function (num) {

                // this.procList = []
                if (num) {
                    this.procLeftNum = num
                } else {
                    this.procLeftNum = 0
                }
                this.finalProc = emptyFunc
            }

            // this.addProc = function(proc) {
            //  this.procList.push()
            //  this.procLeftNum = this.procList.length
            // }
            this.setFinalProc = function (proc) {
                this.finalProc = proc
            }
            this.setProcLeftNum = function (num) {
                this.procLeftNum = num
            }
            this.finishOneProc = function () {
                if (this.procLeftNum > 0) {
                    this.procLeftNum--
                        if (this.procLeftNum == 0) {
                            this.finalProc()
                        }
                } else {
                    $log.log('AsyncTask: There is no proc left.')
                }

            }
        }

        ClassService.registerClass(new AsyncTask())

        // 新建一个异步任务计数器,num为任务数
        MiscService.newAsyncTask = function (num) {
            var out = {
                class_id: 'AsyncTask'
            }
            ClassService.installMethod(out)
            out.AsyncTaskCtor(num)
            return out
        }

        // 发送与各种订单对应的查看详情事件
        // @param object 含有订单各属性的对象
        MiscService.sendViewDetailsEvent = function (item) {
            if (item.class >= 1 && item.class <= 4) {
                EventService.emit(EventService.ev.ORDER_VIEW_DETAILS, item.oid)
            }

            if(item.class==5||item.class==6){
                EventService.emit(EventService.ev.ORDER_VIEW_DETAILS, item.oid)
            }

            if (item.class >= 71 && item.class <= 74) {
                EventService.emit(EventService.ev.FINANCE_VIEW_DETAILS, item.fid)
            }
        }

        MiscService.formatTime = function (arr, mode) {
            var tmp;
            var time_show_1;
            var time_show_2;
            for (var key in arr) {
                tmp = arr[key]
                if (tmp.time) {
                    time_show_1 = $filter('date')(tmp.time * 1000, 'yyyy/MM/dd');
                    time_show_2 = $filter('date')(tmp.time * 1000, 'HH:mm');
                    if (mode == 0) {
                        tmp.time_show = time_show_1;
                    } else {
                        tmp.time_show = time_show_1 + '\u202f' + time_show_2;
                    }
                }
                if (tmp.reg_time) {
                    var reg_time_show_1 = $filter('date')(tmp.reg_time * 1000, 'yyyy-MM-dd');
                    var reg_time_show_2 = $filter('date')(tmp.reg_time * 1000, 'HH时mm分');
                    if (mode == 0) {
                        tmp.reg_time_show = reg_time_show_1;
                    } else {
                        tmp.reg_time_show = reg_time_show_1 + '\u202f' + reg_time_show_2;
                    }
                }
            }
        }

        MiscService.formatTimeForSummary = function (arr, mode) {
            var tmp;
            var reg_time_show_1;
            var reg_time_show_2;
            for (var key in arr) {
                tmp = arr[key]
                if (tmp.reg_time) {
                    reg_time_show_1 = $filter('date')(tmp.reg_time * 1000, 'yyyy-MM-dd汇总表');
                    reg_time_show_2 = $filter('date')(tmp.reg_time * 1000, 'HH时mm分');
                    if (mode == 0) {
                        tmp.reg_time_show = reg_time_show_1;
                    } else {
                        tmp.reg_time_show = reg_time_show_1 + '\u202f' + reg_time_show_2;
                    }
                }
            }
        }

        //根据单据对象数组的内容向每个元素中添加用于展示的字段，例如把[{class:1}]变成[{class:1,classShowContent:'销售单'}]
        // @param array 单据对象数组
        MiscService.genShowContent = function (arr, mode) {
            var tmp;
            for (var key in arr) {
                tmp = arr[key]
                if (tmp.time) {
                    tmp.time_show_1 = $filter('date')(tmp.time * 1000, 'yyyy/MM/dd');
                    tmp.time_show_2 = $filter('date')(tmp.time * 1000, 'HH:mm');
                    if (mode == 0) {
                        tmp.time_show = tmp.time_show_1;
                    } else {
                        tmp.time_show = tmp.time_show_1 + '\u202f' + tmp.time_show_2;
                    }
                }
                    //  创建时间
                if (tmp.reg_time) {
                    tmp.reg_time_show_1 = $filter('date')(tmp.reg_time * 1000, 'yyyy/MM/dd');
                    tmp.reg_time_show_2 = $filter('date')(tmp.reg_time * 1000, 'HH:mm');
                    if (mode == 0) {
                        tmp.reg_time_show = tmp.reg_time_show_1;
                    } else {
                        tmp.reg_time_show = tmp.reg_time_show_1 + '\u202f' + tmp.reg_time_show_2;
                    }
                }
                // 出库时间
                if (tmp.leave_time) {
                    if (tmp.leave_time == 0) {
                        tmp.leave_time_show = '无'
                    } else {
                        tmp.leave_time_show_1 = $filter('date')(tmp.leave_time * 1000, 'yyyy/MM/dd');
                        tmp.leave_time_show_2 = $filter('date')(tmp.leave_time * 1000, 'HH:mm');
                        if (mode == 0) {
                            tmp.leave_time_show = tmp.leave_time_show_1;
                        } else {
                            tmp.leave_time_show = tmp.leave_time_show_1 + '\u202f' + tmp.leave_time_show_2;
                        }
                    }
                }
                // 单据类型
                if (tmp.class) {
                    tmp.class = Number(tmp.class)
                    switch (tmp.class) {
                        case 1:
                            tmp.class_show = '销售单'
                            break
                        case 2:
                            tmp.class_show = '销售退货单'
                            break
                        case 3:
                            tmp.class_show = '采购单'
                            break
                        case 4:
                            tmp.class_show = '采购退货单'
                            break
                        case 5:
                            tmp.class_show = '应收款调整单'
                            break
                        case 6:
                            tmp.class_show = '应付款调整单'
                            break
                        case 53:
                            tmp.class_show = '盘点单'
                            break
                        case 54:
                            tmp.class_show = '调拨单'
                            break
                        case 71:
                            tmp.class_show = '收款单'
                            break
                        case 72:
                            tmp.class_show = '付款单'
                            break
                        case 73:
                            tmp.class_show = '其他收入单'
                            break
                        case 74:
                            tmp.class_show = '支出费用单'
                            break
                    }
                }
                if (tmp.status) {
                    tmp.status = Number(tmp.status)
                    switch (tmp.status) {
                        case 0:
                            tmp.status_show = '未知'
                            break
                        case 1:
                            tmp.status_show = '已完成'
                            break
                        case 2:
                            tmp.status_show = '异常'
                            break
                        case 3:
                            tmp.status_show = '已作废'
                            break
                        case 4:
                            tmp.status_show = '立即处理'
                            break
                        case 5:
                            tmp.status_show = '暂不通知库管'
                            break
                        case 6:
                            tmp.status_show = '正在通知库管'
                            break
                        case 7:
                            tmp.status_show = '库管未确认'
                            break
                        case 8:
                            tmp.status_show = '库管已打印单据,但未出库'
                            break
                        case 9:
                            tmp.status_show = '已出库但未送达'
                            break
                        case 10:
                            tmp.status_show = '库管已打印单据,但未入库'
                            break
                        case 11:
                            tmp.status_show = '待审核-暂缓发货'
                            break
                        case 12:
                            tmp.status_show = '待审核-立即处理'
                            break
                        case 90:
                            tmp.status_show = '未完成'
                            break
                        case 91:
                            tmp.status_show = '红冲单据'
                            break
                        case 92:
                            tmp.status_show = '红冲附属单据'
                            break
                        case 99:
                            tmp.status_show = '期初'
                            break
                        case 100:
                            tmp.status_show = '草稿'
                            break
                    }
                }

                if (tmp.remain || (tmp.remain == 0)) {

                }
            }

            // $log.debug('genShowContent result: ', arr)
        }


        // $log.log('test start!')
        // var t = MiscService.newAsyncTask()

        // t.setProcLeftNum(4)
        // t.setFinalProc(function() {
        //     $log.log('finalProc executed')
        // })

        // t.finishOneProc()
        // t.finishOneProc()
        // t.finishOneProc()

        // 打印单据
        MiscService.print = function (oid) {
            var domain = NetworkService.getDomain()
            Print.newInstance().print(domain, oid);
        }
            //Print.newInstance().print(domain, oid); // 打印
            //Print.newInstance().preview(domain, oid); // 预览

        /**
         * 给指定字符串加空格到指定长度
         * @param str
         * @param maxLength
         * @returns {string|*}
         */
        MiscService.addSpace = function (str, maxLength) {
            var spaceStr = '';
            var spaceLength = maxLength - str.length;
            for (var j = 0; j < spaceLength; j++) {
                spaceStr += '&ensp;';
            }
            str = spaceStr + str;

            return str;
        }

        /**
         * 格式化金额
         * @param number 数字
         * @param places 小数点后位数
         * @param symbol 货币符号
         * @param thousand 千位分隔符
         * @param decimal 小数点符号
         * @returns {string} money 格式化好的金额
         */
        MiscService.formatMoney = function (number, places, symbol, thousand, decimal) {
            number = number || 0;
            places = !isNaN(places = Math.abs(places)) ? places : 2;
            symbol = symbol !== undefined ? symbol : "";
            thousand = thousand || ",";
            decimal = decimal || ".";
            var negative = number < 0 ? "-" : "",
                i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
                j = (j = i.length) > 3 ? j % 3 : 0;
            var money = symbol + negative + (j ? i.substr(0, j) + thousand : "")  + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "");
            return money;
        }

        /**
         * 格式化金额对象,使得传入的字段数组中的字段的长度在格式化后,该传入对象中的所有相同字段长度一样
         * @param numberObject 数字数组或包含值为数字的对象数组
         * @param fieldArray 要格式化的字段数组，若numberArray为数字数组，此项传空数组[]
         * @param places 小数点后位数
         * @param symbol 货币符号
         * @param thousand 千位分隔符
         * @param decimal 小数点符号
         * @returns {Array}
         */
        MiscService.formatMoneyObject = function (numberObject, fieldArray, places, symbol, thousand, decimal) {
            var fieldShowArray = [];
            angular.copy(fieldArray, fieldShowArray);
            for (var k = 0; k < fieldShowArray.length; k++) {
                fieldShowArray[k] += '_show';
            }
            if (numberObject instanceof Array) { // 当numberObject为数组时
                var moneyObject = [];
                angular.copy(numberObject, moneyObject);

                if (fieldArray.length > 0) { // 当fieldArray内有元素时,numberObject为元素为对象的数组
                    for (var k = 0; k < fieldArray.length; k++) {
                        var maxLength = 0;
                        for (var i = 0; i < moneyObject.length; i++) {
                            moneyObject[i][fieldShowArray[k]] = MiscService.formatMoney(moneyObject[i][fieldArray[k]], places, symbol, thousand, decimal);
                        }
                        for (var i = 0; i < moneyObject.length; i++) {
                            maxLength = maxLength > moneyObject[i][fieldShowArray[k]].length ? maxLength : moneyObject[i][fieldShowArray[k]].length;
                        }
                        for (var i = 0; i < moneyObject.length; i++) {
                            // moneyObject[i][fieldShowArray[k]] = MiscService.addSpace(moneyObject[i][fieldShowArray[k]], maxLength);
                        }
                    }
                } else { // 当fieldArray为空时,numberObject为元素为数字的数组
                    var maxLength = 0;
                    for (var i = 0; i < moneyObject.length; i++) {
                        moneyObject[i] = MiscService.formatMoney(moneyObject[i], places, symbol, thousand, decimal);
                    }
                    for (var i = 0; i < moneyObject.length; i++) {
                        maxLength = maxLength > moneyObject[i].length ? maxLength : moneyObject[i].length;
                    }
                    for (var i = 0; i < moneyObject.length; i++) {
                        // moneyObject[i] = MiscService.addSpace(moneyObject[i], maxLength);
                    }
                }
            } else { // 当numberObject为对象时
                var moneyObject = {};
                angular.copy(numberObject, moneyObject);

                for (var k = 0; k < fieldArray.length; k++) {
                    var maxLength = 0;
                    for (var i in moneyObject) {
                        moneyObject[i][fieldShowArray[k]] = MiscService.formatMoney(moneyObject[i][fieldArray[k]], places, symbol, thousand, decimal);
                    }
                    for (var i in moneyObject) {
                        maxLength = maxLength > moneyObject[i][fieldShowArray[k]].length ? maxLength : moneyObject[i][fieldShowArray[k]].length;
                    }
                    for (var i in moneyObject) {
                        // moneyObject[i][fieldShowArray[k]] = MiscService.addSpace(moneyObject[i][fieldShowArray[k]], maxLength);
                    }
                }
            }

            return moneyObject;
        }

        /**
         * 格式化金额对象,使得传入的字段数组中的字段的长度在格式化后,该传入对象中的所有字段长度一样
         * @param numberObject 数字数组或包含值为数字的对象数组
         * @param fieldArray 要格式化的字段数组，若numberArray为数字数组，此项传空数组[]
         * @param places 小数点后位数
         * @param symbol 货币符号
         * @param thousand 千位分隔符
         * @param decimal 小数点符号
         * @returns {Array}
         */
        MiscService.formatMoneyObjectAll = function (numberObject, fieldArray, places, symbol, thousand, decimal) {
            var fieldShowArray = [];
            angular.copy(fieldArray, fieldShowArray);
            for (var k = 0; k < fieldShowArray.length; k++) {
                fieldShowArray[k] += '_show';
            }

            if (numberObject instanceof Array) { // 当numberObject为数组时
                var moneyObject = [];
                angular.copy(numberObject, moneyObject);

                if (fieldArray.length > 0) { // 当fieldArray内有元素时,numberObject为元素为对象的数组
                    var maxLength = 0;
                    for (var k = 0; k < fieldArray.length; k++) {
                        for (var i = 0; i < moneyObject.length; i++) {
                            moneyObject[i][fieldShowArray[k]] = MiscService.formatMoney(moneyObject[i][fieldArray[k]], places, symbol, thousand, decimal);
                        }
                        for (var i = 0; i < moneyObject.length; i++) {
                            maxLength = maxLength > moneyObject[i][fieldShowArray[k]].length ? maxLength : moneyObject[i][fieldShowArray[k]].length;
                        }
                        for (var i = 0; i < moneyObject.length; i++) {
                            // moneyObject[i][fieldShowArray[k]] = MiscService.addSpace(moneyObject[i][fieldShowArray[k]], maxLength);
                        }
                    }
                } else { // 当fieldArray为空时,numberObject为元素为数字的数组
                    var maxLength = 0;
                    for (var i = 0; i < moneyObject.length; i++) {
                        moneyObject[i] = MiscService.formatMoney(moneyObject[i], places, symbol, thousand, decimal);
                    }
                    for (var i = 0; i < moneyObject.length; i++) {
                        maxLength = maxLength > moneyObject[i].length ? maxLength : moneyObject[i].length;
                    }
                    for (var i = 0; i < moneyObject.length; i++) {
                        // moneyObject[i] = MiscService.addSpace(moneyObject[i], maxLength);
                    }
                }
            } else { // 当numberObject为对象时
                var moneyObject = {};
                angular.copy(numberObject, moneyObject);

                var maxLength = 0;
                for (var k = 0; k < fieldArray.length; k++) {
                    for (var i in moneyObject) {
                        moneyObject[i][fieldShowArray[k]] = MiscService.formatMoney(moneyObject[i][fieldArray[k]], places, symbol, thousand, decimal);
                    }
                    for (var i in moneyObject) {
                        maxLength = maxLength > moneyObject[i][fieldShowArray[k]].length ? maxLength : moneyObject[i][fieldShowArray[k]].length;
                    }
                    for (var i in moneyObject) {
                        // moneyObject[i][fieldShowArray[k]] = MiscService.addSpace(moneyObject[i][fieldShowArray[k]], maxLength);
                    }
                }
            }

            return moneyObject;
        }

        /**
         * 格式化金额对象
         * @param numberObject 要格式化的值为0的字段
         * @param fieldArray 索引0的字段要格式化为和索引1字段长度一致的字符串
         * @param places 小数点后位数
         * @param symbol 货币符号
         * @param thousand 千位分隔符
         * @param decimal 小数点符号
         * @returns {Array}
         */
        MiscService.formatMoneyZero = function (numberObject, fieldArray, places, symbol, thousand, decimal) {
            var fieldShowArray = [];
            angular.copy(fieldArray, fieldShowArray);
            for (var k = 0; k < fieldShowArray.length; k++) {
                fieldShowArray[k] += '_show';
            }

            var moneyObject = [];
            angular.copy(numberObject, moneyObject);

            var maxLength = 0;
            for (var k = 0; k < fieldArray.length; k++) {
                for (var i = 0; i < moneyObject.length; i++) {
                    moneyObject[i][fieldShowArray[k]] = MiscService.formatMoney(moneyObject[i][fieldArray[k]], places, symbol, thousand, decimal);
                }
            }
            for (var i = 0; i < moneyObject.length; i++) {
                maxLength = maxLength > moneyObject[i][fieldShowArray[1]].length ? maxLength : moneyObject[i][fieldShowArray[1]].length;
            }
            for (var k = 0; k < fieldArray.length; k++) {
                for (var i = 0; i < moneyObject.length; i++) {
                    moneyObject[i][fieldShowArray[k]] = MiscService.addSpace(moneyObject[i][fieldShowArray[k]], maxLength);
                }
            }

            return moneyObject;
        }

        /**
         * 将order对象里是金额的数字字段全部格式化为金额格式
         * @param misc
         * @param places 小数点后位数
         * @param symbol 货币符号
         * @param thousand 千位分隔符
         * @param decimal 小数点符号
         */
        MiscService.formatMoneyMisc = function (misc, fieldArray, places, symbol, thousand, decimal) {
            var moneyMisc = misc;
            var fieldShowArray = [];
            angular.copy(fieldArray, fieldShowArray);
            for (var k = 0; k < fieldShowArray.length; k++) {
                fieldShowArray[k] += '_show';
                moneyMisc[fieldShowArray[k]] = MiscService.formatMoney(moneyMisc[fieldArray[k]], places, symbol, thousand, decimal);
            }

            var maxLength = 0;

            for (var k = 0; k < fieldShowArray.length; k++) {
                moneyMisc[fieldShowArray[k]] = MiscService.formatMoney(moneyMisc[fieldArray[k]], places, symbol, thousand, decimal);
                maxLength = maxLength > moneyMisc[fieldShowArray[k]].length ? maxLength : moneyMisc[fieldShowArray[k]].length;
            }

            // for (var k = 0; k < fieldShowArray.length; k++) {
            //     moneyMisc[fieldShowArray[k]] = MiscService.addSpace(moneyMisc[fieldShowArray[k]], maxLength);
            // }

            return moneyMisc;
        }

        return MiscService

    }
])