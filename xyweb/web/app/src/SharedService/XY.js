    // App启动 //
'use strict';
// 'cfp.hotkeys',
var xy = angular.module('XY', ['ngCookies', 'ngRoute', 'ngTouch', 'smart-table', 'ngSanitize']);//, 'ionic'
var xyvs = '?xyvs=2.0.1';//版本号
xy.config(['$routeProvider',
    function($routeProvider) {
        $routeProvider

        //空页面用来查看单据页面中转
        .when('/NullPage',{
            templateUrl:'web/app/src/NullPage/NullPage.html' + xyvs,
        })
        //仪表盘
        .when('/Dashboard', {
            templateUrl: 'web/app/src/Dashboard/Dashboard.html' + xyvs,
            controller: 'DashboardController',
        })
        .when('/Temp', {
            templateUrl: 'web/app/src/Temp/Temp.html' + xyvs,
            controller: 'TempController',
        })
        // 标题栏搜索
        .when('/Search', {
            templateUrl: 'web/app/src/Search/Search.html' + xyvs,
            controller: 'SearchController',
        })
        // 草稿单据(待办事项)
       .when('/ViewDraft', {
            templateUrl: 'web/app/src/Draft/ViewDraft.html' + xyvs,
            controller: 'ViewDraftController',
        })
        //人脸识别
       .when("/FaceRecognition",{
                templateUrl: 'web/app/src/FaceRecognition/FaceRecognition.html' + xyvs,
                controller: 'FaceRecognitionController',
       })
       .when("/FaceDetail",{
                templateUrl: 'web/app/src/FaceRecognition/FaceDetail/FaceDetail.html' + xyvs,
                controller: 'FaceDetailController',
       })
       .when('/Camera',{
                templateUrl:'web/app/src/FaceRecognition/Camera/Camera.html' + xyvs,
                controller:'CameraController',
       })
        // 今日单据和所有单据
        .when('/ViewOrder', {
            templateUrl: 'web/app/src/ViewOrder/ViewOrder.html' + xyvs,
            controller: 'ViewOrderController',
        })


        // 查询系列
        // 查询功能菜单页
        // when('/QueryMenu', {
        //     templateUrl: 'web/app/src/Query/QueryMenu.html' + xyvs,
        //     controller: 'QueryMenuController'
        // }).

        // 库存相关
        // 库存流水
        .when('/StockHistory', {
            templateUrl: 'web/app/src/Query/SharedTemplate/SalesHistorySupplyHistory.html' + xyvs,
            controller: 'StockHistoryController'
        })

        //查看库存
        .when('/ViewStock', {
            templateUrl: 'web/app/src/Stock/ViewStock.html' + xyvs,
            controller: 'ViewStockController'
        })

        //个人信息
       .when('/profile', {
            templateUrl: 'web/app/src/Profile/profile.html' + xyvs,
            controller: 'ProfileController'
        })

        .when('/profile', {
            templateUrl: 'web/app/src/Profile/profile.html' + xyvs,
            controller: 'ProfileController'
        })

        //管理商品
        .when('/ManageGoodAndCat', {
            templateUrl: 'web/app/src/SPUSKU/ManageGoodAndCat.html' + xyvs,
            controller: 'ManageGoodAndCatController'
        })

        //管理单位
        .when('/ManageCstmr', {
            templateUrl: 'web/app/src/Cstmr/ManageCstmr.html' + xyvs,
            controller: 'ManageCstmrController'
        })

        //往来单位详情页
        .when('/CstmrDetails', {
            templateUrl: 'web/app/src/Cstmr/CstmrDetails.html' + xyvs,
            controller: 'CstmrDetailsController'
        })

        // 管理用户
        .when('/ManageUser', {
            templateUrl: 'web/app/src/Management/ManageUser/ManageUser.html' + xyvs,
            controller: 'ManageUserController'
        })

        // 本店用户信息
        .when('/UserInfo', {
            templateUrl: 'web/app/src/Management/UserInfo/UserInfo.html' + xyvs,
            controller: 'UserInfoController'
        })

        // 店铺设置
        .when('/ShopInfo', {
            templateUrl: 'web/app/src/Management/ShopInfo/ShopInfo.html' + xyvs,
            controller: 'ShopInfoController'
        })

        // 停车位置
        .when('/ManageParkAddress', {
            templateUrl: 'web/app/src/Management/ParkAddress/ManageParkAddress.html' + xyvs,
            controller: 'ManageParkAddressController'
        })

        // 打印设置
        .when('/PrintSettings', {
            templateUrl: 'web/app/src/Management/PrintSettings/PrintSettings.html' + xyvs,
            controller: 'PrintSettingsController'
        })

        //内部人员使用的打印设置
        .when('/PrintSettingsPrivate', {
            templateUrl: 'web/app/src/Management/PrintSettings/PrintSettingsPrivate.html' + xyvs,
            controller: 'PrintSettingsPrivateController'
        })

        // 系统设置
        .when('/SystemSettings', {
            templateUrl: 'web/app/src/Management/SystemSettings/SystemSettings.html' + xyvs,
            controller: 'SystemSettingsController'
        })
        // 关于我们
        .when('/AboutUs', {
            templateUrl: 'web/app/src/Management/AboutUs/AboutUs.html' + xyvs,
            controller: 'AboutUsController'
        })

        // 反馈
       .when('/FeedBack', {
            templateUrl: 'web/app/src/Management/FeedBack/FeedBack.html' + xyvs,
            controller: 'FeedBackController'
        })

        // 报表
        .when('/EverydaySummarySheet', {
            templateUrl: 'web/app/src/SummarySheet/EverydaySummarySheet.html' + xyvs,
            controller: 'EverydaySummarySheetController',
        })

        .when('/TodaySummarySheet', {
            templateUrl: 'web/app/src/SummarySheet/TodaySummarySheet.html' + xyvs,
            controller: 'TodaySummarySheetController',
        })

        // 设置
        // when('/settings', {
        //     templateUrl: 'web/app/src/Settings/settings.html' + xyvs,
        //     controller: 'SettingsController'
        // }).

        // 共享界面

        // 选择用户
        .when('/SelectUser', {
            templateUrl: 'web/app/src/SharedComponent/SelectUser/SelectUser.html' + xyvs,
            controller: 'SelectUserController'
        })

        //销售汇总表
        .when('/SalesSummarySheet',{
            templateUrl:'web/app/src/SalesSummarySheet/SalesSummarySheet.html' + xyvs,
            controller:'SalesSummarySheetController',
        })

        // 采购汇总表
        .when('/PurchaseSummarySheet',{
            templateUrl:'web/app/src/PurchaseSummarySheet/PurchaseSummarySheet.html' + xyvs,
            controller:'PurchaseSummarySheetController',
        })

        //财务汇总表
        .when('/FinanceSummarySheet',{
            templateUrl:'web/app/src/SummarySheet/FinanceSummarySheet.html' + xyvs,
            controller:'FinanceSummarySheetController',
        })
        //库存
        .when('/SkuDetail',{
            templateUrl:'web/app/src/SkuDetail/SkuDetail.html' + xyvs,
            controller:'SkuDetailController',
        })
        //仓库
        .when('/Warehouse',{
            templateUrl:'web/app/src/Warehouse/Warehouse.html' + xyvs,
            controller:'WarehouseController',
        })
        //新建/编辑 仓库
        .when('/CreateEditWarehouse',{
            templateUrl:'web/app/src/Warehouse/CreateEditWarehosue/CreateEditWarehosue.html' + xyvs,
            controller:'CreateEditWarehosueController',
        })
        //易企记的路由 ↓↓↓↓↓↓↓↓↓↓
        ////草稿单
        .when('/AccountSetting',{
            templateUrl: 'web/app/src/yiqiji/AccountSetting/AccountSetting.html' + xyvs,
            controller: 'AccountSettingController',
        })
        .when('/CorporateSector',{
            templateUrl: 'web/app/src/yiqiji/CorporateSector/CorporateSector.html' + xyvs,
            controller: 'CorporateSectorController',
        })

        .when('/incomeAndExpenseType',{
            templateUrl: 'web/app/src/yiqiji/incomeAndExpenseType/incomeAndExpenseType.html' + xyvs,
            controller: 'incomeAndExpenseTypeController',
        })
        //收支出汇总表
        .when('/ExpenseAndIncomeSummarySheet',{
            templateUrl: 'web/app/src/yiqiji/SummarySheet/ExpenseAndIncomeSummarySheet.html' + xyvs,
            controller: 'ExpenseAndIncomeSummarySheetController',
        })
        //草稿单
        .when('/Draft',{
            templateUrl: 'web/app/src/yiqiji/Draft/Draft.html' + xyvs,
            controller: 'DraftController',
        })

        .otherwise({
            redirectTo: '/Dashboard'
        });
    }
]);

// advanced: 6,
// settings: 7,
// index: 1,


// newSalesOrder: 2,
// submitSalesOrder: 3,
// viewStock: 4,
// profile: 5,


// NewCat     : 8,
// ManageCat  : 9,
// NewGood    : 10, // 新建商品
// ManageGood : 11,


// selectLogInMode: -1,
// manageLogin: -2,
// normalLogin: -3,
// signUp:
