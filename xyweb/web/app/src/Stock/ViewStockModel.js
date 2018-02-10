'use strict';

angular.module('XY').factory('ViewStockModel', ['EventService', "$q", '$log', 'PageService', 'LocalTableClass', 'StockService', 'MiscService', 'UserService', 'NetworkService',
    function (EventService, $q, $log, PageService, LocalTableClass, StockService, MiscService, UserService, NetworkService) {

        var model = {};
        var loginStatus = UserService.getLoginStatus();
        var _filter = [{
            fieldName: ['spu_name', 'spec_name', "cat_name","sn"],
            value: '*',
            mode: 1,
        }, {
            fieldName: ["cat_id"],
            value: "*",
            mode: 1
        }];
        model.presentSto = "";
        model.stoList = {};
        var skuStoInfo,stockInfo;

        //格式化金额
        model.formatSkuList = function () {
            angular.copy(MiscService.formatMoneyObject(model.skuList, ['unit_price', 'tot_price']), model.skuList);
        };
        //获取body高度，在changePline里面根据body高度来设置显示数量
        function calcPline() {
            var bodyHeight = $(".function-body").height();
            var tableHeight = bodyHeight - 200;
            // $log.debug('tableHeight',tableHeight);
            var ajustedPline = Math.floor(tableHeight / 36);
            return ajustedPline;
        }
        model.querySKUSTO = function (type,sto_id,scope) {
            var defered =$q.defer();
            StockService.querySKUSTO(type,sto_id).then(function () {
                skuStoInfo = StockService.getSkuStoList();
                stockInfo = StockService.getStock();
                model.localTable = LocalTableClass.newLocalTable(skuStoInfo, _filter, 10);
                model.skuList = model.localTable.getShowData();
                model.totalInfo = model.localTable.getTotalInfo();

                // if(scope){
                //     scope.skuList = model.skuList;
                // }
                var newPline = calcPline();
                model.localTable.changePline(newPline);
                model.localTable.changeFilterCondition(1, '*');
                defered.resolve();
            });
            return defered.promise;
        };
        EventService.on(EventService.ev.START_VIEW_STOCK, function (event, arg) {
            $q.all([model.querySKUSTO(1,arg), StockService.querySTO(1).then(function () {
                model.stoList = StockService.getStoList();
            })]).then(function () {
                PageService.setNgViewPage('ViewStock');
            });
            if (arg) {
                model.presentSto = arg;
                model.isAll = false;
                model.backBtnIsShow = true;
            } else {
                model.isAll = true;
                model.backBtnIsShow = false;

            }
        });

        // EventService.on(EventService.ev.START_CREATE_SkuDetail,function(event,arg){
        //     PageService.setNgViewPage('SkuDetail');
        //     model.arg=arg;
        // });

        model.colList = [
            {
                colName: '商品编号',
                DIYName: '商品编号',
                isShow: true,
                colCssTitle: '',
                colCss: '',
                colShow: true,
                colClick: '',
                bind: 'sn',
            },
            {
                colName: '商品名',
                DIYName: '商品名',
                isShow: true,
                colCssTitle: '',
                colCss: 'ellipsis pointer',
                colShow: true,
                colClick: 'skuDetail(item)',
                bind: 'spu_name',

            },
            {
                colName: '速查码',
                DIYName: '速查码',
                isShow: false,
                colCssTitle: '',
                colCss: 'ellipsis pointer',
                colShow: true,
                colClick: '',
                bind: 'qcode',
            },
            {
                colName: '规格',
                DIYName: '规格',
                isShow: true,
                colCssTitle: '',
                colCss: 'ellipsis',
                colShow: true,
                colClick: '',
                bind: 'spec_name',

            },
/*            {
                colName: '图片',
                DIYName: '图片',
                isShow: true,
                colCssTitle: '',
                colCss: '',
                colShow: true,
                colClick: '',
                bind: 'picture',

            },*/

            {
                colName: '类别',
                DIYName: '类别',
                isShow: true,
                colCssTitle: '',
                colCss: '',
                colShow: true,
                colClick: '',
                bind: 'cat_name',

            },
            {
                colName: '库存',
                DIYName: '库存',
                isShow: true,
                colCssTitle: 'stock',
                colCss: 'stock',
                colShow: true,
                colClick: '',
                bind: 'stock',

            },
        /*    {
                colName: '单价',
                DIYName: '单价',
                isShow: true,
                colCssTitle: 'price_title unit_price',
                colCss: 'price unit_price',
                colShow: loginStatus.rpg == 1 || loginStatus.rpg == 4,
                colClick: '',
                bind: 'unit_price',

            },*/
            {
                colName: '售价',
                DIYName: '售价',
                isShow: false,
                colCssTitle: 'price_title',
                colCss: 'price',
                colShow: 'loginStatus.rpg == 1 || loginStatus.rpg == 4',
                colClick: '',
                bind: 'last_selling_price',

            },
            {
                colName: '金额',
                DIYName: '金额',
                isShow: true,
                colCssTitle: 'price_title',
                colCss: 'price',
                colShow: loginStatus.rpg == 1 || loginStatus.rpg == 4,
                colClick: '',
                bind: 'tot_price',

            },
        ];
        return model; // or return model

    }
]);
