'use strict'

/**
 * 用户权限服务
 */
xy.factory('AuthGroupService', ['$log', 'UserService', 'EventService',
    function ($log, UserService, EventService) {

        var AuthGroupService = {};

        AuthGroupService.module = {
            AccountPay: 'AccountPay',             // 充值
            FeedBack: 'FeedBack',               // 反馈
            ShopInfo: 'ShopInfo',               // 店铺设置
            ManageUser: 'ManageUser',             // 成员管理
            ManageParkAddress: 'ManageParkAddress',      // 停车位置
            PrintSettings: 'PrintSettings',          // 打印设置
            SystemSettings: 'SystemSettings',         // 系统设置
            AboutUs: 'AboutUs',                // 关于
            UserInfo: 'UserInfo',               // 用户设置
            SalesOrder: 'SalesOrder',             // 销售单据
            PurchaseOrder: 'PurchaseOrder',          // 采购单据
            ModifyOrder: 'ModifyOrder',            // 调节单
            NewSalesOrder: 'NewSalesOrderController', //新交易类单据

            StockTaking: 'StockTaking',            // 盘点单
            Requisition: 'Requisition',            // 调拨单
            ReceiptAndPayment: 'ReceiptAndPayment',      // 收付款单
            IncomeAndExpense: 'IncomeAndExpense',       // 其他收入/支出费用单
            CreateSPU: 'CreateSPU',              // 新建商品
            CreateCstmr: 'CreateCstmr',            // 新建单位
            CreateEditCat: 'CreateEditCat',          // 新建类别
            Dashboard: 'Dashboard',              // 主页
            ViewDraft: 'ViewDraft',              // 待办事项
            ViewOrder: 'ViewOrder',              // 查看单据
            ManageCstmr: 'ManageCstmr',            // 往来单位
            ViewStock: 'ViewStock',              // 查看库存
            ManageGoodAndCat: 'ManageGoodAndCat',       // 管理商品
            TodaySummarySheet: 'TodaySummarySheet',      // 今日实时数据
            EverydaySummarySheet: 'EverydaySummarySheet',   // 每日汇总表
            SalesSummarySheetShow: 'SalesSummarySheetShow',  //销售汇总表


            IsYqjShow: 'IsYqjShow',              // 易企记
            IncomeStatistics: 'IncomeStatistics',       //收入单
            ExpenditureStatistics: 'ExpenditureStatistics',  //支出单
            TransferAccounts: 'TransferAccounts',       //内部转账
            Withdrawals: 'Withdrawals',            //内部提现
            ReceiptFill: 'ReceiptFill',            //发票填补开单
            CostView: 'CostView',               // 查看成本
            IncomeSummarySheet: 'IncomeSummarySheet',     //收入汇总表
            ExpendSummarySheet: 'ExpendSummarySheet',     //支出汇总表
            financeExamine: 'financeExamine',   //财务审核
            boosExamine: 'boosExamine', //boos审核
            corporateSector: 'corporateSector',// 部门设置
            accountSeting: 'accountSeting',// 账户设置
            IncomeAndExpenseSummarySheet: 'IncomeAndExpenseSummarySheet', //收入支出统计
        }

        // 权限规则数组
        var authRuleArr = [];

        /**
         * 创建规则
         * @param id 模块编号
         * @param module 模块名称
         */
        var createAuthRule = function (id, module) {
            var rule = {
                module: module,
                status: false,
            };

            authRuleArr[id] = rule;
        }

        // 生成规则
        createAuthRule(1, AuthGroupService.module.AccountPay);              // 充值
        createAuthRule(2, AuthGroupService.module.FeedBack);                // 反馈
        createAuthRule(3, AuthGroupService.module.ShopInfo);                // 店铺设置
        createAuthRule(4, AuthGroupService.module.ManageUser);              // 成员管理
        createAuthRule(5, AuthGroupService.module.ManageParkAddress);       // 停车位置
        createAuthRule(6, AuthGroupService.module.SystemSettings);          // 系统设置
        createAuthRule(7, AuthGroupService.module.AboutUs);                 // 关于
        createAuthRule(8, AuthGroupService.module.UserInfo);                // 用户设置
        createAuthRule(9, AuthGroupService.module.SalesOrder);              // 销售单
        createAuthRule(10, AuthGroupService.module.PurchaseOrder);          // 采购单
        createAuthRule(11, AuthGroupService.module.ModifyOrder);            // 调节单
        createAuthRule(12, AuthGroupService.module.StockTaking);            // 盘点单
        createAuthRule(13, AuthGroupService.module.ReceiptAndPayment);      // 收付款单
        createAuthRule(14, AuthGroupService.module.IncomeAndExpense);       // 其他收入/支出费用单
        createAuthRule(15, AuthGroupService.module.CreateSPU);              // 新建商品
        createAuthRule(16, AuthGroupService.module.CreateCstmr);            // 新建单位
        createAuthRule(17, AuthGroupService.module.CreateEditCat);          // 新建类别
        createAuthRule(18, AuthGroupService.module.Dashboard);              // 主页
        createAuthRule(19, AuthGroupService.module.ViewDraft);              // 待办事项
        createAuthRule(20, AuthGroupService.module.ViewOrder);              // 查看单据
        createAuthRule(21, AuthGroupService.module.ManageCstmr);            // 往来单位
        createAuthRule(22, AuthGroupService.module.ViewStock);              // 查看库存
        createAuthRule(23, AuthGroupService.module.ManageGoodAndCat);       // 管理商品
        createAuthRule(24, AuthGroupService.module.TodaySummarySheet);      // 今日实时数据
        createAuthRule(25, AuthGroupService.module.EverydaySummarySheet);   // 每日汇总表``
        createAuthRule(26, AuthGroupService.module.SalesSummarySheetShow);  // 每日汇总表``
        createAuthRule(27, AuthGroupService.module.CostView);               // 成本查看
        createAuthRule(28, AuthGroupService.module.PrintSettings);          // 打印设置
        createAuthRule(29, AuthGroupService.module.TransferAccounts);       // 内部转账
        createAuthRule(30, AuthGroupService.module.Withdrawals);            // 内部提现
        createAuthRule(31, AuthGroupService.module.IncomeStatistics);       // 支出统计
        createAuthRule(32, AuthGroupService.module.ExpenditureStatistics);  // 收入统计
        createAuthRule(33, AuthGroupService.module.ReceiptFill);            // 发票填补开单
        createAuthRule(34, AuthGroupService.module.IncomeSummarySheet);     // 收入汇总表
        createAuthRule(35, AuthGroupService.module.ExpendSummarySheet);     // 支出汇总表
        createAuthRule(36, AuthGroupService.module.Requisition);            // 调拨单
        createAuthRule(37, AuthGroupService.module.financeExamine);         // 财务审核
        createAuthRule(38, AuthGroupService.module.boosExamine);            // boos审核

        createAuthRule(39, AuthGroupService.module.corporateSector);        // 部门设置
        createAuthRule(40, AuthGroupService.module.accountSeting);          // 账户设置
        createAuthRule(41, AuthGroupService.module.IncomeAndExpenseSummarySheet);   // 收入支出统计


        createAuthRule(101, AuthGroupService.module.IsYqjShow);             // 易企记

        // 用户组数组
        var authGroupArr = [];

        /**
         * 创建用户组
         * @param rpg 用户组编号
         * @param authArr 权限为true的数组
         */
        var createAuthGroup = function (rpg, authArr) {
            var tmpAuthRuleArr = [];
            angular.copy(authRuleArr, tmpAuthRuleArr);
            for (var i = 0; i < authArr.length; i++) {
                tmpAuthRuleArr[authArr[i]].status = true;
            }
            authGroupArr[rpg] = tmpAuthRuleArr;
        }

        // 生成用户组
        createAuthGroup(1, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 36]); // 进销存创建者
        createAuthGroup(7, [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 36]); // 管理员
        createAuthGroup(2, [1, 2, 5, 7, 8, 9, 12, 13, 16, 19, 20, 21, 22, 28]); // 销售
        createAuthGroup(3, [1, 2, 7, 8, 9, 10, 12, 19, 20, 22, 28]); // 库管
        createAuthGroup(4, [1, 2, 5, 7, 8, 9, 10, 11, 12, 13, 16, 18, 19, 20, 21, 22, 24, 25, 27, 28]); // 股东
        createAuthGroup(8, [4, 6, 8, 18, 19, 20, 29, 30, 31, 32, 33, 34, 35, 37, 38, 39, 40, 41, 101]); // 易企记 创建者
        createAuthGroup(9, [4, 6, 8, 18, 19, 20, 29, 30, 31, 32, 33, 34, 35, 37, 38, 39, 40, 41, 101]); // 易企记 老板
        createAuthGroup(10, [8, 18, 19, 20, 29, 30, 31, 32, 33, 34, 35, 37, 40, 41, 101]); // 易企记 财务
        createAuthGroup(11, [8, 19, 20, 31, 34, 35, 101]); // 易企记 员工
        createAuthGroup(12, [6, 8, 18, 19, 20, 29, 30, 31, 32, 33, 34, 35, 37, 38, 39, 40, 41, 101]); // 易企记 管理员

        /**
         * 通过模块名获得该模块的显示状态
         * @param module
         * @returns {*}
         */
        AuthGroupService.getIsShow = function (module) {
            var isShow;
            var rpg = UserService.getLoginStatus().rpg;

            for (var i in authGroupArr[rpg]) {
                if (authGroupArr[rpg][i]) {
                    if (authGroupArr[rpg][i].module == module) {
                        isShow = authGroupArr[rpg][i].status;
                    }
                }
            }
            return isShow;
        };

        return AuthGroupService;

    }
]);