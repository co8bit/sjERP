'use strict'

xy.controller('ViewStockController', ['EventService', '$scope', '$timeout', '$log', 'StockService', 'CreateEditSPUModel', 'PageService', '$filter', 'ViewStockModel', 'MiscService', 'SkuDetailModel', 'UserService', 'LocalTableClass',
    function (EventService, $scope, $timeout, $log, StockService, CreateEditSPUModel, PageService, $filter, model, MiscService, SkuDetailModel, UserService, LocalTableClass) {
        /**
         * 初始判断窗口大小
         */
        $scope.isMobile = MiscService.testMobile();
        $scope.isShowPrice = UserService.getLoginStatus().rpg == 3;
        function init() {
            var height = $(window).height();
            if (height < 800) {
                $scope.isPad = true;
            } else {
                $scope.isPad = false;
            }

        }

        $('body').focus();
        $(function () {
            init();
        });
        window.onresize = function () {
            init();
        };

        $scope.newColList = [];
        $scope.colList = [];
        // 更新数据
        StockService.querySKU(1);
        StockService.queryCat(1);

        $scope.isAll = model.isAll//是否显示单价
        // 建立映射
        $scope.catInfo = StockService.get_cats();
        $scope.skuList = model.skuList;
        $scope.filterInfo = model.localTable.getFilter();
        $scope.pageList = model.localTable.getPageList();
        $scope.querySKUSTO = model.querySKUSTO;
        //总计
        $scope.stock_price = model.localTable.getStockPrice();
        $scope.stock_quantity = model.localTable.getStockQuantity();
        $scope.backBtnIsShow = model.backBtnIsShow;
        $scope.colSettingIsShow = false;
        $scope.isreplace = true;
        $scope.stoList = model.stoList;
         $scope.totalInfo  = model.totalInfo;
        //选中的类别
        $scope.checkCatName = '';

        $scope.backToStock = function () {
            EventService.emit(EventService.ev.START_OPEN_WAREHOUSE);
        };

        // 快速搜索输入内容
        $scope.queryInfo = {
            query: '',
        };
        $scope.$watch('queryInfo.query', function () {
            model.localTable.changeFilterCondition(0, $scope.queryInfo.query);
        }, true);

        $scope.selectCat = function (cat_id) {
            model.localTable.changeFilterCondition(1, cat_id);
            $scope.totalInfo = model.localTable.getTotalInfo()
        };
        $scope.presentSto = model.presentSto;//当前所选仓库
        $scope.selectSto = function (sto_id) {
            $scope.isAll = sto_id !== "" ? false : true;
            $scope.presentSto = sto_id;
            $scope.querySKUSTO(1, sto_id).then(function () {
                $scope.skuList = model.skuList;
                $scope.pageList = model.localTable.getPageList();
                $scope.totalInfo = model.localTable.getTotalInfo()
            })
        };
        // 点击了页码
        $scope.selectPage = function (pageItem) {
            model.localTable.changeNowPage(pageItem);
            model.formatSkuList();
            $log.debug('in selectPage, pageItem = ', pageItem);
        };

        // 当信息源变化时自动刷新
        var refreshDataHandle = EventService.on(EventService.ev.querySKU, function (evnet, arg) {
            model.localTable.calc();
        });

        var filterDoneHandle = EventService.on(EventService.ev.LOCAL_TABLE_FILTER_DONE, function () {
            model.formatSkuList();
        });

        $scope.$on('$destroy', function () {
            refreshDataHandle();
            filterDoneHandle();
        });

        //创建SPU
        $scope.createSPU = function () {
            if ($scope.checkCatName) {
                EventService.emit(EventService.ev.START_CREATE_EDIT_SPU, {checkCatName: $scope.checkCatName})
            } else {
                EventService.emit(EventService.ev.START_CREATE_EDIT_SPU)
            }
        };

        //选中类别时记住类别
        $scope.checkCat = function (value) {
            $scope.checkCatName = value;
        };

        //打开库存详情
        $scope.skuDetail = function (item) {
            EventService.emit(EventService.ev.START_CREATE_SkuDetail, item);
        };


        /**
         * 初始判断窗口大小
         */
        function init() {
            var height = $(window).height();
            var width = $(window).width();
            if (height < 800) {
                $scope.isPad = true;
            } else {
                $scope.isPad = false;
            }
            if (width > 1400) {
                $scope.isreplace = false;
            } else {
                $scope.isreplace = true;
            }

        }

        /**
         * 窗口改变大小初始化
         */
        window.onresize = function () {
            init();
        };

        $(function () {
            //初始化
            init();
//			updataList();
            /**
             * 可拖动排序函数
             * @param {Object} e
             * @param {Object} tr
             */
//			var fixHelperModified = function(e, tr) {
//					var $originals = tr.children();
//					var $helper = tr.clone();
//					$helper.children().each(function(index) {
//						$(this).width($originals.eq(index).width())
//					});
//					return $helper;
//				},
//				updateIndex = function(e, ui) {
//					$scope.newColList = [];
//					$('td.index', ui.item.parent()).each(function(i) {
//						$(this).html(i + 1);
//						updataColList($(this),i);
//					});
//				};
//			$("#sort tbody").sortable({
//				helper: fixHelperModified,
//				stop: updateIndex
//			}).disableSelection();
        });


        /**
         * 传入每个便利到的元素把值给一个新的数组
         * @param e
         * @param i
         */
        function updataColList(e, i) {
            var tr = e.parent();
            var td = e.parent().children('td.hide');
            var index = td.html();
            $scope.newColList[i] = $scope.data[index];
        }

        $scope.colList = model.colList;
        $scope.openSettingPage = function () {
            $scope.data = [];
            angular.copy($scope.colList, $scope.data);
            $scope.colSettingIsShow = true;
        };

        $scope.quit = function () {
            //退出不做任何修改
            $scope.colSettingIsShow = false;
        };


        $scope.recover = function () {
            //恢复默认设置 因为colName不可以改变，显示的只是DIYName 只要把colName赋值给DIYName就可以了
            for (var v in $scope.data) {
                $scope.data[v].DIYName = $scope.data[v].colName;
                $scope.data[v].isShow = true;
                if ($scope.data[v].colName == '速查码' || $scope.data[v].colName == '售价') {
                    $scope.data[v].isShow = false;
                }
            }
        };
        $scope.submit = function () {
            var dataToSend;
            if ($scope.newColList.length == 0) {
                dataToSend = $scope.data;
            } else {
                dataToSend = $scope.newColList;
                $scope.newColList = [];
            }
            $scope.colList = dataToSend;
            model.colList = dataToSend;

            updataList();

            $scope.colSettingIsShow = false;

        };

        $scope.$watch('model.skuList', function (oldValue, newValue) {
            updataList();
        });
        function updataList() {
            $scope.newSkuList = [{data0: 'data0'}];
            // $log.error('newlist',$scope.newskuList)

            var i = 0;
            for (var v in $scope.skuList) {
//				$log.error(v)
                var data0 = $scope.skuList[v][$scope.colList[0].bind];
                var data1 = $scope.skuList[v][$scope.colList[1].bind];
                var data2 = $scope.skuList[v][$scope.colList[2].bind];
                var data3 = $scope.skuList[v][$scope.colList[3].bind];
                var data4 = $scope.skuList[v][$scope.colList[4].bind];
                var data5 = $scope.skuList[v][$scope.colList[5].bind];
                var data6 = $scope.skuList[v][$scope.colList[6].bind];
                var data7 = $scope.skuList[v][$scope.colList[7].bind];
//               var data8 = $scope.skuList[v][$scope.colList[8].bind];
//				$scope.newSkuList[0] = {data0: data0};
                // $log.error('newlist',$scope.newskuList)
//				if ($scope.newSkuList[i] == undefined) {
//					$scope.newSkuList[i] = {
//						data0: data0,
//						data1: data1,
//						data2: data2,
//						data3: data3,
//						data4: data4,
//						data5: data5,
//						data6: data6,
//						data7: data7,
//						data8: data8,
//					};
//				}
//				i++;
            }
//			$log.error('newlist',$scope.newskuList)
//			$scope.skuList = $scope.newSkuList;
        }

//		$scope.sn = $scope.skuList[0].sn;
//		$scope.spu = $scope.skuList[0].spu_name;

        $(".close").mouseover(function(){
          $(".close>img").attr("src","web/app/img/misc/guanbi.png");
        });
        $(".close").mouseout(function(){
          $(".close>img").attr("src","web/app/img/misc/24856-200.png");
        });
    }
]);
