// 事件服务，用于定义，发出，监听全局事件
'use strict';

xy.factory('EventService', ['$rootScope', '$log', 'ConfigService', 'PageService',
    function ($rootScope, $log, ConfigService, PageService) {

        // $log.debug('EventService init')

        var EventService = {};

        // 所有全局事件
        EventService.ev = {

            NOT_LOGGED_IN: 'NOT_LOGGED_IN', // 遇到了-8889 未登录错误，服务器认为用户没有登录

            // 登录注册
            LOGIN_SUCCESS: 'LOGIN_SUCCESS', // 登录成功

            LOGIN_STATUS_CHANGED: 'LOGIN_STATUS_CHANGED', //登录状态变化

            ADMIN_REGISTER_SUCCESS: 'ADMIN_REGISTER_SUCCESS', // 管理员注册成功
            ADMIN_REGISTER_ERROR: 'ADMIN_REGISTER_ERROR', // 管理员注册失败
            ADMIN_RECOVERY_SUCCESS: 'ADMIN_RECOVERY_SUCCESS', //管理员重置密码成功

            USER_REGISTER_SUCCESS: 'USER_REGISTER_SUCCESS', // 注册普通用户成功
            USER_REGISTER_ERROR: 'USER_REGISTER_ERROR',//注册普通用户失败
            // 标题栏搜索
            SEARCH_TITLE_BAR: 'SEARCH_TITLE_BAR', // arg = 搜索内容


            //仪表盘
            START_VIEW_DASHBOARD: 'START_VIEW_DASHBOARD',
            //人脸识别
            OPEN_FACE_RECOGNITION:'OPEN_FACE_RECOGNITION',
            OPEN_FACE_DETAIL:'OPEN_FACE_DETAIL',
            START_CAMERA:'START_CAMERA',



            // 草稿单据 ------------------------------------------------------------------------------------------------------------------------------------
            START_VIEW_DRAFT: 'START_VIEW_DRAFT', // 查看草稿单据
            DRAFT_LOAD_SUCCESS: 'DRAFT_LOAD_SUCCESS',

            // 查看单据
            START_VIEW_TODAY_ORDER: 'START_VIEW_TODAY_ORDER', // 查看今日单据
            START_VIEW_ALL_ORDER: 'START_VIEW_ALL_ORDER', // 查看所偶单据

            // 商品相关-start--------------------------------------------------------------------------------------------------------------------------------

            // 类别
            START_CREATE_EDIT_CAT: 'START_CREATE_EDIT_CAT', // 开始创建/编辑类别

            EDIT_CAT_SUCCESS: 'EDIT_CAT_SUCCESS', //编辑类别信息成功
            EDIT_CAT_ERROR: 'EDIT_CAT_ERROR', //编辑类别信息失败

            CREATE_CAT_SUCCESS: 'CREATE_CAT_SUCCESS', //创建类别成功
            CREATE_CAT_ERROR: 'CREATE_CAT_ERROR', //创建类别失败

            DELETE_CAT_SUCCESS: 'DELETE_CAT_SUCCESS', //删除类别成功
            DELETE_CAT_ERROR: 'DELETE_CAT_ERROR',

            LOAD_CAT_SUCCESS: 'LOAD_CAT_SUCCESS',
            LOAD_CAT_ERROR: 'LOAD_CAT_ERROR',

            // SPUSKU
            START_VIEW_STOCK: 'START_VIEW_STOCK', // 查看库存
            START_MANAGE_SKU: 'START_MANAGE_SKU', // 查看库存

            START_CREATE_EDIT_SPU: 'START_CREATE_EDIT_SPU', // 开始创建或编辑SPU,通知CreateEditModel初始化 参数见CreateEditSPUModel: function init();
            START_CREATE_EDIT_STO: 'START_CREATE_EDIT_SPU',

            CREATE_SKU_SUCCESS: 'CREATE_SKU_SUCCESS', //创建SKU成功
            CREATE_SKU_ERROR: 'CREATE_SKU_ERROR',
            CREATESPU_UPDATE: 'CREATESPU_UPDATE', //更新新建SKU页面信息

            EDIT_SPUSKU_SUCCESS: 'EDIT_SPUSKU_SUCCESS', // 编辑SPUSKU成功
            EDIT_SPUSKU_ERROR: 'EDIT_SPUSKU_ERROR',

            DELETE_SKU_SUCCESS: 'DELETE_SKU_SUCCESS', // 删除SKU成功
            DELETE_SKU_ERROR: 'DELETE_SKU_ERROR',

            LOAD_SKUS_INFO_SUCCESS: 'LOAD_SKUS_INFO_SUCCESS',
            LOAD_SKUS_INFO_ERROR: 'LOAD_SKUS_INFO_ERROR',

            CREATE_STOCK_TAKING_SUCCESS: 'CREATE_STOCK_TAKING_SUCCESS', // 创建盘点单成功
            CREATE_STOCK_TAKING_ERROR: 'CREATE_STOCK_TAKING_ERROR', // 创建盘点单失败

            EDIT_STOCK_TAKING: 'EDIT_STOCK_TAKING', //编辑盘点单 arg: 1:成功 0:失败

            START_STOCK_TAKING: 'START_STOCK_TAKING', // 开始新建盘点单
            CONTINUE_STOCK_TAKING: 'CONTINUE_STOCK_TAKING', // 继续开盘点单 arg=单号
            STOCK_TAKING_VIEW_DETAILS: 'STOCK_TAKING_VIEW_DETAILS', // 查看盘点单详情 arg = wid

            START_REQUISITION: 'START_REQUISITION', //开始调拨单
            CONTINUE_REQUISITION:'CONTINUE_REQUISITION',//继续调拨单草稿单
            CREATE_STOCK_REQUISITION_SUCCESS:"CREATE_STOCK_REQUISITION_SUCCESS",//调拨单创建成功
            REQUISITION_VIEW_DETAILS:"REQUISITION_VIEW_DETAILS",//查看调拨单详情

            querySKU: 'QUERY_SKU',
            LOAD_SPU_LIST_SUCCESS: 'LOAD_SPU_LIST_SUCCESS',
            // 商品相关 --end--

            // 财务相关 --start-------------------------------------------------------------------------------------------------------------------------------
            START_CREATE_INCOME: 'START_CREATE_INCOME', // 开始创建其他收入单
            START_CREATE_EXPENSE: 'START_CREATE_EXPENSE', // 开始创建费用单

            START_MODIFY_INCOME: 'START_MODIFY_INCOME',//开始创建调整应收款单
            CREATE_MODIFYINCOME_SUCCESS : 'CREATE_MODIFYINCOME_SUCCESS',
            CREATE_MODIFYINCOME_ERROR : 'CREATE_MODIFYINCOME_ERROR',

            START_MODIFY_EXPENSE: 'START_MODIFY_EXPENSE',//开始创建调整应付款单
            CREATE_MODIFYEXPENSE_SUCCESS: 'CREATE_MODIFYEXPENSE_SUCCESS',
            CREATE_MODIFYEXPENSE_ERROR: 'CREATE_MODIFYEXPENSE_ERROR',



            CONTINUE_CREATE_INCOME: 'CONTINUE_CREATE_INCOME', // 继续开收入单
            CONTINUE_CREATE_EXPENSE: 'CONTINUE_CREATE_EXPENSE', // 继续开费用单

            START_CREATE_RECEIPT: 'START_CREATE_RECEIPT', // 开始创建收款单
            START_CREATE_PAYMENT: 'START_CREATE_PAYMENT', // 开始创建付款单

            CONTINUE_CREATE_RECEIPT: 'CONTINUE_CREATE_RECEIPT', //继续开收款单
            CONTINUE_CREATE_PAYMENT: 'CONTINUE_CREATE_PAYMENT', // 继续开付款单

            CREATE_INCOME_SUCCESS: 'CREATE_INCOME_SUCCESS', // 创建其他收入单成功
            CREATE_INCOME_ERROR: 'CREATE_INCOME_ERROR', // 创建其他收入单失败

            CREATE_EXPENSE_SUCCESS: 'CREATE_EXPENSE_SUCCESS', // 创建费用单失败
            CREATE_EXPENSE_ERROR: 'CREATE_EXPENSE_ERROR', // 创建费用单失败

            CREATE_RECEIPT_SUCCESS: 'CREATE_RECEIPT_SUCCESS', // 创建收款单成功
            CREATE_RECEIPT_ERROR: 'CREATE_RECEIPT_ERROR',

            CREATE_PAYMENT_SUCCESS: 'CREATE_PAYMENT_SUCCESS', // 创建收款单成功
            CREATE_PAYMENT_ERROR: 'CREATE_PAYMENT_ERROR',

            FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_SUCCESS: 'FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_SUCCESS', // 创建收款单或付款单草稿成功
            FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_ERROR: 'FINANCE_CREATE_RECEIPT_OR_PAYMENT_DRAFT_ERROR', // 创建收款单或付款单草稿失败

            FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_SUCCESS: 'FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_SUCCESS', // 创建支出或收入单草稿成功
            FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_ERROR: 'FINANCE_CREATE_INCOME_OR_EXPENSE_DRAFT_ERROR',

            FINANCE_VIEW_DETAILS: 'FINANCE_VIEW_DETAILS', // 四种财务单的查看详情都用这个事件 arg = 订单号

            FINANCE_QUERY_ONE_SUCCESS: 'FINANCE_QUERY_ONE_SUCCESS', //查询一个财务单成功
            // 财务相关 --end--

            // 往来单位 -start--------------------------------------------------------------------------------------------------------------------------------
            START_CREATE_COMPANY: 'START_CREATE_COMPANY', // 开始新建往来单位

            UPDATE_CSTMR_NOW: 'UPDATE_CSTMR_NOW', //往来单位编辑成功
            COMPANY_VIEW_DETAILS: 'COMPANY_VIEW_DETAILS', // 查看往来单位 arg = cid(往来单位id)
            COMPANY_EDIT_CONTACT: 'COMPANY_EDIT_CONTACT', // 编辑往来单位联系人 arg = cid

            QUERY_CONTACT_SUCCESS: 'QUERY_CONTACT_SUCCESS', // 查询联系人成功
            QUERY_CONTACT_ERROR: 'QUERY_CONTACT_ERROR',

            QUERY_REMAIN_SUCCESS: 'QUERY_REMAIN_SUCCESS', // 查询某联系人未收款未付款订单成功
            QUERY_REMAIN_ERROR: 'QUERY_REMAIN_ERROR',
            MANAGE_COMPANY: 'MANAGE_COMPANY',

            COMPANY_LIST_LOAD_SUCCESS: 'COMPANY_LIST_LOAD_SUCCESS',
            CREATE_COMPANY_SUCCESS: 'CREATE_COMPANY_SUCCESS',

            // 往来单位 -end--


            // 订单 --start--------------------------------------------------------------------------------------------------------------------------------
            START_CREATE_ORDER: 'START_CREATE_ORDER', // 开始创建订单 arg = 订单类型
            CONTINUE_CREATE_ORDER: 'CONTINUE_CREATE_ORDER', //继续开单 arg = 草稿的单号
            START_REOPEN_ORDER: 'START_REOPEN_ORDER', //重新开单 arg = {calss：订单类型，orderInfo: 订单详情}
            START_CREATE_Buy_ORDER: 'START_CREATE_Buy_ORDER', // 新开采购单 自动填充
            SALEORDER_UPDATE:'SALEORDER_UPDATE',//刷新信息

            CREATE_ORDER_SUCCESS: 'CREATE_ORDER_SUCCESS',
            CREATE_ORDER_ERROR: 'CREATE_ORDER_ERROR',

            ORDER_CREATE_DRAFT_SUCCESS: 'ORDER_CREATE_DRAFT_SUCCESS',
            ORDER_CREATE_DRAFT_ERROR: 'ORDER_CREATE_DRAFT_ERROR',

            VIEW_BACK_TO_ORDER:'VIEW_BACK_TO_ORDER',//查看订单详情之后回到订单查看页
            VIEW_BACK_TO_COMPANY:'VIEW_BACK_TO_COMPANY',
            VIEW_BACK_TO_DRAFT:'VIEW_BACK_TO_DRAFT',

            BACK_TO_MANAGE_COMPANY:'BACK_TO_MANAGE_COMPANY',//查看往来对象详情后回到往来对象查看页
            BACK_TO_VIEW_TODAY_ORDER:'BACK_TO_VIEW_TODAY_ORDER',
            BACK_TO_VIEW_ALL_ORDER:'BACK_TO_VIEW_ALL_ORDER',

            ORDER_VIEW_DETAILS: 'ORDER_VIEW_DETAILS', // 查看订单详情 arg = oid;
            ORDER_ARGS: 'ORDER_ARGS',
            ORDER_CLASS: 'ORDER_CLASS',

            TRANSFER_COMPANY_CID: 'TRANSFER_COMPANY_CID',
            // 订单 --end--


            // 报表 summarysheet start ----------------
            START_VIEW_TODAY_SUMMARY_SHEET: 'START_VIEW_TODAY_SUMMARY_SHEET', // 查看实时数据

            START_VIEW_SALES_SUMMARY_SHEET: 'START_VIEW_SALES_SUMMARY_SHEET',//查看销售统计

            START_VIEW_PURCHASE_SUMMARY_SHEET: 'START_VIEW_PURCHASE_SUMMARY_SHEET',//查看采购统计

            // 报表 end

            // 管理相关 --start-----------------------------------------------------------------------------------------------------------------------------------
            START_CREATE_EDIT_USER: 'START_CREATE_EDIT_USER', // 创建和编辑用户弹窗 arg = undefined / uid

            QUERY_USER_SUCCESS: 'QUERY_USER_SUCCESS', // 查询员工信息成功
            QUERY_USER_ERROR: 'QUERY_USER_ERROR',

            EDIT_SHOP_NAME_SUCCESS: 'EDIT_SHOP_NAME_SUCCESS', // 更改店铺名成功
            EDIT_SHOP_NAME_ERROR: 'EDIT_SHOP_NAME_ERROR',

            START_MANAGE_USER: 'START_MANAGE_USER', // 管理成员
            GET_USER_INFO: 'GET_USER_INFO', // 获取用户信息

            START_MANAGE_PARK_ADDRESS: 'START_MANAGE_PARK_ADDRESS', // 管理停车位置
            START_CREATE_EDIT_PARK_ADDRESS: 'START_CREATE_EDIT_PARK_ADDRESS', // 开始新建或编辑停车位置 // arg = 无(表示新建)/停车位置信息（表示编辑）
            PARK_ADDRESS_LOAD_SUCCESS: 'PARK_ADDRESS_LOAD_SUCCESS', // 停车位置加载完毕
            PARK_ADDRESS_EDIT_SUCCESS: 'PARK_ADDRESS_EDIT_SUCCESS', // 停车位置创建编辑成功
            PARK_ADDRESS_CREATE_SUCCESS: 'PARK_ADDRESS_CREATE_SUCCESS', // 停车位置创建编辑成功

            START_VIEW_SHOPINFO: 'START_VIEW_SHOPINFO', // 查看店铺设置

            SYSTEM_SETTINGS: 'SYSTEM_SETTINGS',//系统设置
            PRINT_SETTINGS: 'PRINT_SETTINGS',//打印设置

            // 管理相关 --end--

            BIG_DIALOG_INITIALIZE: 'BIG_DIALOG_INITIALIZE', // 初始化 全屏对话框功能
            CLICK_BODY: 'CLICK_BODY', // 点击body
            WINDOW_RESIZE: 'WINDOW_RESIZE', // window resize事件

            KEY_DOWN: 'KEY_DOWN', // 键盘按键
            CONFIRM_DIALOG: 'CONFIRM_DIALOG', // 确定对话框
            CLICK_FINISH: 'CLICK_FINISH',
            CLOSE_PRICE_INPUT: 'CLOSE_PRICE_INPUT',

            // 费用中心
            START_EXPENSE_CENTER: 'START_EXPENSE_CENTER',

            // 支付
            START_ACCOUNT_PAY: 'START_ACCOUNT_PAY',

            // 对账单
            START_STATEMENT: 'START_STATEMENT',
            START_CHECK_MOBILE: 'START_CHECK_MOBILE',
            START_STATEMENTWECHAT: 'START_STATEMENTWECHAT',
            CLOSE_STATEMENTWECHAT: 'CLOSE_STATEMENTWECHAT',

            // 锁定店铺
            LOCK_SHOP: 'LOCK_SHOP',
            RESTORE_LOCK_SHOP_STATUS: 'RESTORE_LOCK_SHOP_STATUS',

            // 本地翻页类
            LOCAL_TABLE_FILTER_DONE: 'LOCAL_TABLE_FILTER_DONE',

            PRINT_ORDER: 'PRINT_ORDER',


            //viewStock 页面点击详情
            START_CREATE_SkuDetail: 'START_CREATE_SkuDetail',

            //监听过滤器改变事件
            CHANGE_CHECKBOX: 'CHANGE_CHECKBOX',

            CLOSE_GOOD_LIST_TOTAL: 'CLOSE_GOOD_LIST_TOTAL',//关闭商品列表
            SHOW_GOOD_LIST_TOTAL: 'SHOW_GOOD_LIST_TOTAL',//显示商品列表

            //初始化开交易单信息
            SALES_ORDER_INIT: 'SALES_ORDER_INIT',
            //仓库
            START_OPEN_WAREHOUSE: 'START_OPEN_WAREHOUSE',
            CREATE_EDIT_WAREHOUSE:'CREATE_EDIT_WAREHOUSE',
            CREATE_STO_WAREHOUSE:'CREATE_STO_WAREHOUSE',  //新建仓库
            EDIT_STO_WAREHOUSE:"EDIT_STO_WAREHOUSE",      //编辑仓库
            DELETE_STO_WAREHOUSE:"DELETE_STO_WAREHOUSE",  //删除仓库
            QUERY_STO_WAREHOUSE:"QUERY_STO_WAREHOUSE",    //查询仓库
            //判断是否处于找回密码状态
            IF_RECOVERY:'IF_RECOVERY',

            //易企记↓
            START_ACCOUNT: 'START_ACCOUNT',//账户设置
            START_CREATE_ACCOUNT: 'START_CREATE_ACCOUNT',//新建账户
            EDIT_CASH_ACCOUNT: 'EDIT_CASH_ACCOUNT',//编辑现金账户
            UPDATE_ACCOUNTLIST: 'UPDATE_ACCOUNTLIST',//刷新账户列表
            START_CORPORATE_SECTORY: 'START_CORPORATE_SECTORY',// 公司部门设计
            START_CREATE_INCOME_OR_EXPENSE: 'START_CREATE_INCOME_OR_EXPENSE',//开始收入支出单
            START_CONTINUE_INCOME_OR_EXPENSE: 'START_CONTINUE_INCOME_OR_EXPENSE',//继续收入支出单
            START_CREATE_TRANSFER_ACCOUNTS: 'START_CREATE_TRANSFER_ACCOUNTS',//开始 转账
            START_CREATE_WITHDRAW: 'START_CREATE_WITHDRAW',//开始 提现
            START_RECEIPT_FILL: 'START_RECEIPT_FILL', //发票填补开单
            START_VIEW_YIQIJI_DRAFT: 'START_VIEW_YIQIJI_DRAFT',//待办事项
            START_VIEW_INCOME_EXPENSE_TYPE: 'START_VIEW_INCOME_EXPENSE_TYPE',//收支出类别管理
            UPDATA_CATLIST: 'UPDATA_CATLIST', // 更新类别列表
            START_CREATE_CAT: 'START_CREATE_CAT',//创建类别
            START_EDIT_CAT: 'START_EDIT_CAT',//编辑类别

            START_VIEW_EXPENSE_AND_INCOME_SUMMARY_SHEET: 'START_VIEW_EXPENSE_AND_INCOME_SUMMARY_SHEET',//支出统计
            START_VIEW_INCOME_SUMMARY_SHEET: 'START_VIEW_INCOME_SUMMARY_SHEET',//收入统计

            START_VIEW_INCOME_DETAIL: 'START_VIEW_INCOME_DETAIL',//收入详情
            START_VIEW_EXPEND_DETAIL: 'START_VIEW_EXPEND_DETAIL',//支出详情
            START_VIEW_TRANSFER_ACCOUNT_DETAIL: 'START_VIEW_TRANSFER_ACCOUNT_DETAIL',//转账详情
            START_VIEW_WITHDRAW_DETAIL: 'START_VIEW_WITHDRAW_DETAIL',//提现详情

            START_FINANCE_EXAMINE: 'START_FINANCE_EXAMINE',//财务审核
            START_BOSS_EXAMINE: 'START_BOSS_EXAMINE',//boos审核
        }

        // 发出事件
        // @param evName   string      事件名
        // @param [arg]    anything    事件可携带参数
        // @param bLog     bool        在控制台log
        EventService.emit = function (evName, arg, bLog) {

            //如果 bLog == 1 就log
            if (bLog == 1) {
                $log.debug('Event emitted: ', evName, ', arg: ', arg)
            }

            if (evName == undefined || EventService.ev[evName] == undefined) {
                $log.error('try to emit undefined event !');
            } else {
                $rootScope.$emit(evName, arg);
            }

        }

        // 监听事件
        // @param  evName    string    要监听的事件名
        // @param  listener  function  回调function,事件发生时会被以参数(event,arg)调用, 参考angular文档"Scope.$on": https://docs.angularjs.org/api/ng/type/$rootScope.Scope
        // @return           function  用于销毁该监听
        EventService.on = function (evName, listener) {
            return $rootScope.$on(evName, listener)
        }


        // 管理错误信息的单例
        EventService.errorAgent = {
            errorId: 0,
            errorArr: [],
        }
        EventService.errorAgent.genId = function () {
            return EventService.errorAgent.errorId++
        }

        // 处理错误信息
        // apiName API名也是事件名
        // ec 错误代码
        EventService.errorAgent.dispose = function (apiName, ec, msg) {
            // $log.warn(apiName, ec)

            // 该错误信息
            var errorInfo = {
                evName: apiName,
                ec: ec,
                flag_dispose: false, // 已经被处置的标记
            }

            // 发出事件,带一个完成处置回调
            EventService.emit(EventService.ev[apiName], {
                ec: ec,
                complete: function () {
                    errorInfo.flag_dispose = true
                }
            });

            // 如果是ec<=0并且没有被处置的话,弹出默认提示
            if (!errorInfo.flag_dispose && errorInfo.ec <= 0) {
                $log.warn('flag_dispose == false', apiName, ec)
                if (ec == -8402) {
                    return;
                }
                var content = null;
                //对应api下查找 错误信息
                if (ConfigService.priorityErrorInfo[apiName] != undefined){
                    if(ConfigService.priorityErrorInfo[apiName][ec] != undefined){
                        content = ConfigService.priorityErrorInfo[apiName][ec];
                    }
                }
                //通用里面查找错误信息
                if ( content == null && ConfigService.errorInfo[ec] != undefined ){
                    content = ConfigService.errorInfo[ec];
                }
                //显示错误提示，没有则返回操作失败
                if (content) {
                    PageService.showConfirmDialog(content);
                } else {
                    $log.debug("unknown_error:", errorInfo.ec);
                    PageService.showConfirmDialog("操作失败！"  + msg);
                }

                var notLoginECArray = [-8501, -8502, -8503, -8889, -8890];
                if ($.inArray(errorInfo.ec, notLoginECArray) > -1) {
                    EventService.emit(EventService.ev.NOT_LOGGED_IN);
                }
            }
        }

        // 完成处置某事件，外界处置某事件之后的
        EventService.errorAgent.completeDisposal;


        return EventService

    }
]);