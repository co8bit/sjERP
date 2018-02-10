//网络服务，存储API名对应的url，对各API的调用使用JQ的ajax发出
'use strict';

xy.factory('NetworkService', ['$log', '$timeout', '$location', 'ConfigService', 'EventService', 'ClassService','PageService',
    function ($log, $timeout, $location, ConfigService, EventService, ClassService,PageService) {

        var NetworkService = {};

        // 初始化，设定服务器域名
        var domain = 'not set';
        var absUrl = $location.absUrl();
        NetworkService.print_log = true;
        (function(){
            if (absUrl.search('/xyweb') >= 0) {
                var tmpPos = absUrl.search('/xyweb')
                domain = absUrl.substring(0, tmpPos) + '/xy/';
                //domain = domain.replace('localhost','121.42.174.105');//若为本地环境，修改domain至测试服务器，测试服务器开放跨域请求
            } else if (absUrl.search('/app') >= 0) {
                var tmpPos = absUrl.search('/app')
                domain = absUrl.substring(0, tmpPos) + '/server/';
                NetworkService.print_log = false;
            }
        })()
        if($location.host() == '112.74.90.144'){
            NetworkService.print_log = true
        }

        NetworkService.getDomain = function () {
            return domain;
        }
        function setCookies(key,val,time){//key设置键 val设置值 time有效时间
            var date=new Date();
            var expiresDays=time;
            date.setTime(date.getTime()+expiresDays*24*3600*1000); //格式化为cookie识别的时间
            document.cookie=key + "=" + val +";expires="+date.toGMTString()+";path=/";  //设置cookie
        }
        //各APIurl
        var apiToUrl = {

            // 锁定商店
            config_lockShop: domain + 'index.php?m=Home&c=Config&a=lockShop',
            // 解锁商店
            config_unlockShop: domain + 'index.php?m=Home&c=Config&a=unlockShop',
            // 锁定商店
            config_getLockShopStatus: domain + 'index.php?m=Home&c=Config&a=getLockShopStatus',
            // 查询店铺的系统设置
            config_getShopConfig: domain + 'index.php?m=Home&c=Config&a=getShopConfig',
            // 设置店铺的系统设置
            config_setShopConfig: domain + 'index.php?m=Home&c=Config&a=setShopConfig',

            // 仓库stock warehouse start---------------------------------------------------------------------------------------------------------------------------
            //创建仓库
            updateSto:domain + "index.php?m=Home&c=Storage&a=updateSto",
            //查询所有仓库
            querySto:domain + "index.php?m=Home&c=Storage&a=querySto",
            //仓库详情
            getSto:domain+"index.php?m=Home&c=Storage&a=get_",
            //查询SKU
            querySKU: domain + 'index.php?m=Home&c=Good&a=querySKU',
            //创建SPUSKU/添加SKU
            createSKU: domain + 'index.php?m=Home&c=Good&a=updateSKU',
            //查询SPU
            querySPU: domain + 'index.php?m=Home&c=Good&a=querySPU',
            //查询SpuSku
            querySKUSTO:domain+"index.php?m=Home&c=SkuStorage&a=querySkuSto",
            //删除SPU
            deleteSPU: domain + 'index.php?m=Home&c=Good&a=deleteSPU',
            //编辑SPUSKU
            editSPUSKU: domain + 'index.php?m=Home&c=Good&a=editSPUSKU',
            //删除SKU
            deleteSKU: domain + 'index.php?m=Home&c=Good&a=deleteSKU',
            //创建类别
            createCat: domain + 'index.php?m=Home&c=Good&a=createCat',
            //查询类别
            queryCat: domain + 'index.php?m=Home&c=Good&a=queryCat',
            //修改类别
            editCat: domain + 'index.php?m=Home&c=Good&a=editCat',
            //删除类别
            deleteCat: domain + 'index.php?m=Home&c=Good&a=deleteCat',
            getGood: domain + 'index.php?m=Home&c=SkuStorage&a=get_',
            // 创建盘点单
            createStockTaking: domain + 'index.php?m=Home&c=Warehouse&a=createStockTakingOrRequisition',
            // 创建盘点单草稿
            createStockTakingDraft: domain + 'index.php?m=Home&c=Warehouse&a=createStockTakingOrRequisitionDraft',
            // 编辑盘点单
            editStockTaking: domain + 'index.php?m=Home&c=Warehouse&a=editStockTaking',
            // 查询盘点单
            warehouse_get_: domain + 'index.php?m=Home&c=Warehouse&a=get_',
            // 编辑盘点单
            warehouse_edit_: domain + 'index.php?m=Home&c=Warehouse&a=edit_',
            // 创建调拨单
            createRequisition:domain+'index.php?m=Home&c=Warehouse&a=createStockTakingOrRequisition',
            // 创建调拨单草稿
            createRequisitionDraft:domain + 'index.php?m=Home&c=Warehouse&a=createStockTakingOrRequisitionDraft',
            // 仓库 end------

            // 查询往来单位信息 start -----------------------------------------------------------------------------------------------------------
            company_queryList: domain + 'index.php?m=Home&c=Company&a=queryList',
            // 新建
            company_create_: domain + 'index.php?m=Home&c=Company&a=create_',
            // 查询一个
            company_get_: domain + 'index.php?m=Home&c=Company&a=get_',
            // 编辑
            company_edit_: domain + 'index.php?m=Home&c=Company&a=edit_',
            // 查询往来单位的未支付或未收款订单
            company_queryRemain: domain + 'index.php?m=Home&c=Company&a=queryRemain',
            // 删除
            compnay_deleteCompany: domain + 'index.php?m=Home&c=Company&a=deleteCompany',

            // 联系人 ----- start -------------------------------------------------------------------------------------------------------------------
            // 查询某个往来单位的联系人
            contact_queryList: domain + 'index.php?m=Home&c=Contact&a=queryList',

            // 停车位置----------------------------------------------------------------------------------------------------------------------------
            parkAddress_queryList: domain + 'index.php?m=Home&c=Parkaddress&a=queryList',
            parkAddress_create_: domain + 'index.php?m=Home&c=Parkaddress&a=create_',
            parkAddress_edit_: domain + 'index.php?m=Home&c=Parkaddress&a=edit_',
            parkAddress_delete_: domain + 'index.php?m=Home&c=Parkaddress&a=delete_',

            //验证码
            util_getJiyanToken: domain + 'index.php?m=Home&c=Util&a=getJiyanToken',

            //登录注册---start------
            //管理员注册
            admin_register: domain + 'index.php?m=Home&c=Login&a=admin_register',
            //登录
            login: domain + 'index.php?m=Home&c=Login&a=login',
            //登出
            logout: domain + 'index.php?m=Home&c=User&a=logout',
            //找回密码
            editUserPasswd : domain + 'index.php?m=Home&c=Login&a=editUserPasswd',
            // User Model ---------------------------------------------------------------------------------------------------------------------------------
            //注册普通员工
            user_register: domain + 'index.php?m=Home&c=User&a=register',
            //查询员工列表
            user_getList: domain + 'index.php?m=Home&c=User&a=getList',
            // 编辑员工信息
            user_editUserInfo: domain + 'index.php?m=Home&c=User&a=editUserInfo',

            // 反馈信息
            feedback_create_: domain + 'index.php?m=Home&c=Feedback&a=create_',

            //登录注册---end------

            //获取店铺信息
            user_getShopInfo: domain + 'index.php?m=Home&c=User&a=getShopInfo',
            //编辑店铺信息
            user_editShopInfo: domain + 'index.php?m=Home&c=User&a=editShopInfo',

            editSPU: domain + 'index.php?m=Home&c=Good&a=editSPU',
            // 订单------------------------------------------------------------------------------------------------------------------------------

            queryOrder: domain + 'index.php?m=Home&c=Order&a=queryOrder',
            // 查询一个订单
            order_get_: domain + 'index.php?m=Home&c=Order&a=get_',

            createOrder: domain + 'index.php?m=Home&c=Order&a=createOrder',
            order_createDraft: domain + 'index.php?m=Home&c=Order&a=createDraft',
            // 编辑订单
            editOrder: domain + 'index.php?m=Home&c=Order&a=editOrder',
            // 设置订单状态
            setOrderStatus: domain + 'index.php?m=Home&c=Order&a=setOrderStatus',

            //照片发送
            sendPicture:domain+ 'index.php?m=Home&c=QnUpload&a=uploadImgToQiniu',
            //财务----start-----------------------------------------------------------------------------------------------------------------------------------
            //创建收入单费用单
            createIncomeOrExpense: domain + 'index.php?m=Home&c=Finance&a=createIncomeOrExpense',
            // 收入费用草稿
            createIncomeOrExpenseDraft: domain + 'index.php?m=Home&c=Finance&a=createIncomeOrExpenseDraft',
            // 创建收款付款单
            createReceiptOrPayment: domain + 'index.php?m=Home&c=Finance&a=createReceiptOrPayment',
            // 收款付款单草稿
            createReceiptOrPaymentDraft: domain + 'index.php?m=Home&c=Finance&a=createReceiptOrPaymentDraft',
            // 查询一个财务单
            finance_queryOneDocument: domain + 'index.php?m=Home&c=Finance&a=queryOneDocument',
            // 编辑财务订单
            finance_editDocument: domain + 'index.php?m=Home&c=Finance&a=editDocument',

            //财务----end----
            test: domain + 'index.php?m=Home&c=Parkaddress&a=queryList',

            //查询 ----start-----------------------------------------------------------------------------------------------------------------------------------
            query_query_: domain + 'index.php?m=Home&c=Query&a=query_',
            query_queryDraft: domain + 'index.php?m=Home&c=Query&a=queryDraft',
            query_search: domain + 'index.php?m=Home&c=Query&a=search',
            query_skuSummary : domain + 'index.php?m=Home&c=Query&a=skuSummary',
            query_skuChart : domain + 'index.php?m=Home&c=Query&a=skuChart',
            //仪表盘dashboard
            query_dashboard: domain + 'index.php?m=Home&c=Query&a=dashboard',
            //备份下载
            query_backUpEveryDay: domain + 'index.php?m=Home&c=Query&a=backUpEveryDay',

            // 报表
            everydaySummarySheet_summarySheet: domain + 'index.php?m=Home&c=everydaySummarySheet&a=summarySheet',
            everydaySummarySheet_queryList: domain + 'index.php?m=Home&c=everydaySummarySheet&a=queryList',

            // 费用中心
            PaymentDetails_getMoneyTimeSave: domain + 'index.php?m=Home&c=PaymentDetails&a=getMoneyTimeSave',
            PaymentDetails_getPaybillAndSmsDetail: domain + 'index.php?m=Home&c=PaymentDetails&a=getPaybillAndSmsDetail',

            // 支付
            UserAccount_get_: domain + 'index.php?m=Home&c=UserAccount&a=get_',
            Paybill_getPayBillParam: domain + 'index.php?m=Home&c=Paybill&a=getPayBillParam',

            // 对账单
            Company_requestStatementOfAccount: domain + 'index.php?m=Home&c=Company&a=requestStatementOfAccount',
            SmsDetails_sendSMSStatementOfAccount: domain + 'index.php?m=Home&c=SmsDetails&a=sendSMSStatementOfAccount',

            //查询 ----end-----------------------------------------------------------------------------------------------------------------------------------

            // 注册验证-start--------------------------

            //体验账户使用登记
            ExperienceRegister:domain + 'index.php?m=Home&c=Apply&a=create_',

            //刪除草稿
            DeleteOrderDraft : domain + 'index.php?m=Home&c=Order&a=deleteDraft',
            DeleteWarehouseDraft : domain + 'index.php?m=Home&c=Warehouse&a=deleteDraft',
            DeleteFinanceDraft : domain + 'index.php?m=Home&c=Finance&a=deleteDraft',

            //应收应付款修改
            ModifyIncomeOrExpense : domain + 'index.php?m=Home&c=Order&a=createAdjustAROrAP',

            //销售汇总表
            SaleSummary :  domain + 'index.php?m=Home&c=Query&a=saleSummary',

            //采购汇总表
            PurchaseSummary : domain + 'index.php?m=Home&c=Query&a=purchaseSummary',

            //打印模板导入
            LoadPrintTemplate :  domain + 'index.php?m=Home&c=PrintTemplate&a=create_',
            //
            getCustomerLastPrice : domain + 'index.php?m=Home&c=Good&a=getThisCustomerLastPrice',
            //向服务器请求商店名称
            getUserInfo_shopName : domain + 'index.php?m=Home&c=User&a=getUserInfo_shopName',
            //向服务器请求模板
            getTemplate : domain + 'index.php?m=Home&c=PrintTemplate&a=get_',
            getTemplatePrivate : domain + 'index.php?m=Home&c=PrintTemplate&a=WBXget_',
            //向服务器发送模板
            createTemplate : domain + 'index.php?m=Home&c=PrintTemplate&a=create_',
            createTemplatePrivate : domain + 'index.php?m=Home&c=PrintTemplate&a=WBXcreate_',
            //获取自由数组
            getOptionArray : domain + 'index.php?m=Home&c=User&a=getOptionArray',
            //设置自由数组
            setOptionArray : domain + 'index.php?m=Home&c=User&a=setOptionArray',
            requestStatementOfWechatAccount : domain + 'index.php?m=Home&c=Company&a=requestStatementOfWechatAccount',

            //易企记↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
            //主页
            financeDashboard : domain + 'index.php?m=Home&c=Finance&a=financeDashboard',
            //创建账户
            createAccount : domain + 'index.php?m=Home&c=Account&a=createAccount',
            //查询账户列表
            queryAccount : domain + 'index.php?m=Home&c=Account&a=query_Account',
            //编辑账户
            editAccount : domain + 'index.php?m=Home&c=Account&a=edit_Account',
            //删除账户
            deleteAccount : domain + 'index.php?m=Home&c=Account&a=delete_Account',
            //新建部门
            createDepartment : domain + 'index.php?m=Home&c=Department&a=createDepartment',
            //编辑部门
            editDepartment : domain + 'index.php?m=Home&c=Department&a=editDepartment',
            //删除部门
            deleteDepartment : domain + 'index.php?m=Home&c=Department&a=deleteDepartment',
            //查询部门
            getDepartment : domain + 'index.php?m=Home&c=Department&a=getDepartment',
            //创建类别
            createFinanceCart : domain + 'index.php?m=Home&c=Finance&a=createFinanceCart',
            //查询类别列表
            queryFinanceCart : domain + 'index.php?m=Home&c=Finance&a=queryFinanceCart',
            //编辑类别
            editFinanceCart : domain + 'index.php?m=Home&c=Finance&a=editFinanceCart',
            //删除类别
            deleteFinanceCart : domain + 'index.php?m=Home&c=Finance&a=deleteFinanceCart',
            //创建财务单单据
            createFinanceOrder : domain + 'index.php?m=Home&c=Finance&a=create_FinanceOrder',
            //创建财务单的草稿单
            createFinanceOrderDraft : domain + 'index.php?m=Home&c=Finance&a=create_FinanceOrderDraft',
            //删除财务单据的草稿单
            deleteFinanceDraft : domain + 'index.php?m=Home&c=Finance&a=deleteDraft',
            //查询财务单
            queryFinanceOrder : domain + 'index.php?m=Home&c=Finance&a=query_FinanceOrder',
            //发票填补单
            invoicePoolSummary : domain + 'index.php?m=Home&c=Finance&a=invoicePoolSummary',
            //审核 改变单据状态
            financeOrderStatusChange: domain + 'index.php?Home&c=Finance&a=financeOrderStatusChange',
            //批量审核
            groupStatusChange: domain + 'index.php?Home&c=Finance&a=groupStatusChange',
            //统计表数据查询接口
            getExpenditureStatistics: domain + 'index.php?Home&c=Finance&a=getExpenditureStatistics',
            //详情页编辑备注
            editFinanceOrder: domain + 'index.php?Home&c=Finance&a=edit_FinanceOrder',
            //作废单据
            deleteFinanceOrder: domain + 'index.php?Home&c=Finance&a=delete_FinanceOrder',


        }
        // 把api全都加进EventService.ev事件列表里
        // 每个api请求时都发送对应事件，ajax成功时arg = EC ajax失败时 arg = 0
        for (var key in apiToUrl) {
            EventService.ev[key] = key;
        }

        //查找api的Url
        NetworkService.getUrl = function (apiName) {
            if (!apiToUrl[apiName]) {
                $log.error('getUrl ' + apiName + ' url not found!');
                return false;
            } else {
                return apiToUrl[apiName];
            }
        }

        // 该类的功能是防止同一接口在同一时间多次被请求
        //防止点击两次创造两次单据
        NetworkService.requesting = {
            apiNameArray: [],
            apiStartTime: [],
            start: function (apiName) {
                var startTime = new Date().getTime();
                NetworkService.requesting.apiNameArray.push(apiName);
                NetworkService.requesting.apiStartTime.push(startTime);
                $timeout(function () {
                    var index = $.inArray(apiName, NetworkService.requesting.apiNameArray);
                    if (index > -1) {
                        NetworkService.requesting.apiNameArray.splice(index, 1);
                    }
                }, 5000);
            },
            end: function (apiName) {
                function removeApi() {
                    NetworkService.requesting.apiNameArray.splice(index, 1);
                    NetworkService.requesting.apiStartTime.splice(index, 1);
                }
                var index = $.inArray(apiName, NetworkService.requesting.apiNameArray);
                if (index > -1) {
                    var endTime = new Date().getTime();
                    var startTime = NetworkService.requesting.apiStartTime[index]
                    var time = endTime - startTime;
                    // console.log('startTime:' + startTime + '---' + 'endTime:' + endTime);
                    var minTime = 1000;
                    if (time < minTime) {
                        $timeout(removeApi(), minTime - time);
                    } else {
                        removeApi();
                    }

                }
            },
        };

        //@function        request            和后端通信
        //@param   string  apiName            API名,用以查找本次请求的url
        //@param   {}      dataToSend         要发送的数据,一个对象
        //@param           callback_success   成功时的回调，参数data为服务器返回数据,如果服务器返回错误信息也会触发该调用，错误信息在data中体现
        //@param           callback_error     出错时的回调，这不是ajax出错,是服务器对调用的处理结果
        // param   bool    b_log      如果ajax成功, 是否log服务器返回信息
        NetworkService.request = function (apiName, dataToSend, callback_success, callback_error) {
            // $log.log('NetworkService.requesting.apiNameArray:', NetworkService.requesting.apiNameArray);
            var index = $.inArray(apiName, NetworkService.requesting.apiNameArray);
            // $log.log(apiName + '---$.inArray:' + index);
            if ($.inArray(apiName, NetworkService.requesting.apiNameArray) > -1) {
                return;
            }
            NetworkService.requesting.start(apiName);

            var url = this.getUrl(apiName);
            // $log.debug(url);
            if (!url) {
                $log.error('request ' + apiName + ' url not found!');
            } else {
                // // 已经log了send
                // var flag_send_loged = false;
                // // 如果b_log则发出的信息也log出来
                // if (b_log) {
                //     flag_send_loged = true;
                //     $log.log(apiName + ' send: ',dataToSend);
                // }
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: dataToSend,
                    dataType: 'json',
                    success: function (data) {//后台返回的数据存储于data,供调用

                        NetworkService.requesting.end(apiName);

                        $timeout(function () {
                            // EC > 0 请求成功
                            if (data.EC > 0) {
                                if(apiName=='DeleteOrderDraft'||apiName=='DeleteWarehouseDraft'||apiName=='DeleteFinanceDraft')
                                        {PageService.showSharedToast('草稿删除成功');
                                }
                                if (NetworkService.print_log) {
                                    $log.debug(apiName + ' ajax success! EC: ', data.EC);
                                    $log.log('  ' + apiName + ' send: ', dataToSend);
                                    $log.log('  ' + apiName + ' response: ', data);
                                }
                                // 成功的回调
                                if (callback_success) {
                                    callback_success(data);
                                }
                                // EC <= 0 请求失败
                            } else {
                                if(data.EC == '-8888' || data.EC == '-8889' || data.EC == '-8890'){
                                    //提示为登陆跳到登陆页
                                    setCookies("userInfo",JSON.stringify(null),7,1);
                                    setCookies('loginInfo',JSON.stringify(null),7,1);
                                    PageService.showIndexPage("Login");
                                }
                                $log.warn(apiName + ' ajax success! EC: ', data.EC);
                                $log.warn('  ' + apiName + ' send: ', dataToSend);
                                $log.warn('  ' + apiName + ' response: ', data);
                                // 失败的回调
                                if (callback_error) {
                                    callback_error(data);
                                }
                            }

                            EventService.errorAgent.dispose(apiName, data.EC, data.MSG)
                            // EventService.emit(EventService.ev[apiName], data.EC);
                        });

                    },
                    error: function () {
                        $log.error('请求失败！')
                        NetworkService.requesting.end(apiName);
                        if ('logout' == apiName) {
                            EventService.emit(EventService.ev.NOT_LOGGED_IN);
                        }
                    }
                });

            }
        }

        // 生成用于api发送的格式,第一层如果是对象或者数组就stringify，再去掉所有本地变量
        NetworkService.genApiData = function (srcObj) {
            var out = {};
            angular.copy(srcObj, out);



            // 去掉本地变量
            ClassService.removeLocalVariable(out);

            for (var key in out) {
                if (out.hasOwnProperty(key)) {
                    if (typeof out[key] == 'object') {
                        out[key] = JSON.stringify(out[key]);
                    }
                }
            }
            return out;
        }

        // 试验新的错误事件机制
        // EventService.on(EventService.ev.finance_editDocument,function(event,arg) {
        //     $log.debug('arg=',arg)
        //     arg.complete()
        // });

        // $log.debug('evName=',EventService.ev.finance_editDocument)
        // EventService.errorAgent.dispose('finance_editDocument', -2)

        return NetworkService;
    }
]);