'use strict';

// 创建和编辑SPU都是这个

xy.controller('CreateSPUController', ['$timeout', 'EventService', '$scope', '$log', 'PageService', 'UserService', 'StockService', 'CreateEditSPUModel', 'PYService','SPUClass',
    function ($timeout, EventService, $scope, $log, PageService, UserService, StockService, model, PYService,SPUClass) {

        // $log.debug('CreateSPUController');
        // 映射正在创建的SPU
        $scope.sendInfo={data:[]};
        $scope.spuInfo = model.spuInfo;
        $scope.stoInfo = model.stoInfo;

        $scope.stoInfo.query = model.stoInfo.query;
        $scope.stoInfo.defaultSto=$scope.stoInfo.getDefaultSto();
        // var defaultTemp={sto_id: $scope.stoInfo.defaultSto.sto_id,skuStoData:[]};
        var spuInfo = {};
        angular.copy($scope.spuInfo,spuInfo);
        $scope.catList = model.catListShow;
        $scope.isCatListShow = false;
        $scope.isStoListShow = [false];
        $scope.isQcodeListShow = false;
        $scope.qcodeList = [];
        $scope.isEdit = model.isEdit;
        $scope.clickStoInput=function (index) {
            $scope.isStoListShow[index] = !$scope.isStoListShow[index];
        };
        $scope.selectSto = function (index,stoIndex) {//选择某个仓库
            $scope.stoInfo.change(stoIndex,$scope.stoInfo.allSto[index]);
            $scope.spuInfo.change(stoIndex,$scope.stoInfo.allSto[index].sto_id);
        };
        if(!$scope.isEdit){ //不是编辑模式获取传入参数类别
            $scope.spuInfo.cat_name = model.cat_name;
        }
        $scope.$watch('spuInfo.cat_name', function () {
            model.localTable.changeFilterCondition(0, '');
        });
        // 增加SKU 按钮
        $scope.addSKU = function (index,sto) {
            $scope.spuInfo.addSKU(index,sto);
        };
        $scope.removeSTOSKU = function (stoIndex) {
            $scope.spuInfo.removeSTOSKU(stoIndex);
        };
        $scope.addSto=function () {//添加仓库时
            $scope.isStoListShow.push(false);
            $scope.stoInfo.addSto();
            $scope.addSKU($scope.stoInfo.stos.length-1,$scope.stoInfo.defaultSto.sto_id);
            // $log.error("skus",$scope.spuInfo.skus)
        };
        $scope.deleteSto = function (stoIndex) {
            var skulist = $scope.spuInfo.skus.data[stoIndex].skuStoData;
            if($scope.spuInfo.skus.data.length == 1){

            }else {
                if(skulist.length>0&&(skulist[0].spec_name||skulist[0].unit_price||skulist[0].stock)){
                    PageService.showConfirmDialog("此仓库中的商品也将清空！",[],function () {
                        $scope.stoInfo.deleteSto(stoIndex);
                        $scope.removeSTOSKU(stoIndex);
                    })
                }else {
                    $scope.stoInfo.deleteSto(stoIndex);
                    $scope.removeSTOSKU(stoIndex);
                }
            }

        };
        // 删除SKU
        $scope.removeSKU = function (index,stoIndex,item) {
            if ($scope.spuInfo.skus.length <= 1) {
                PageService.showConfirmDialog('该商品下所有规格均已被删除，如果不增加新的规格，则保存后当前商品会被删除', [], function () {
                    $scope.spuInfo.removeSKU(index,stoIndex,item);
                })
            } else{
                if(item.spec_name==""){
                    $scope.spuInfo.removeSKU(index,stoIndex,item);
                }else{
                    PageService.showConfirmDialog('确定删除规格吗？',[],function(){
                        $scope.spuInfo.removeSKU(index,stoIndex,item);
                    });
                }
            }
        };
        function isStoListFalse() {
            for(var  key  in  $scope.isStoListShow ){
                if($scope.isStoListShow[key]){
                    $scope.isStoListShow[key]=false;
                }
            }
        }//隐藏所有的仓库下拉框
        function isStoListhasTrue() {
            for(var key in $scope.isStoListShow){
                if( $scope.isStoListShow[key]){
                    return true
                }
            }
        }//查看是有显示的仓库下拉框

        var validateCat = function (callback) {
            var isFound = false;
            for (var i = 0; i < model.catList.length; i++) {
                if ($scope.spuInfo.cat_name == model.catList[i].cat_name) {
                    $scope.spuInfo.cat_id = model.catList[i].cat_id;
                    isFound = true;
                    break;
                }
            }

            if (isFound) {
                callback();
            } else {
                PageService.showConfirmDialog('<div class="title"><a class="red">所属类别错误</a></div>'
                    + '<div><a>可能是您直接在输入框中手动输入了不存在的<span class="red">所属类别</span>所导致。您可以通过以下三种中的任意一种方法解决：</a></div>'
                    + '<div><a>1.选择<span class="red">所属类别</span>下拉列表中已有的类别名称，而不是手动输入。</a></div>'
                    + '<div><a>2.手动输入<span class="red">所属类别</span>下拉列表中已经存在的类别名称。</a></div>'
                    + '<div><a>3.如果该类别确实是新的类别，请点击<span class="red">所属类别</span>下拉列表中的<span class="red">新建类别</span>按钮新建。</a></div>');
            }
        };

        //创建按钮
        //如果有参数 1-继续创建商品
        $scope.create = function (go) {
            validateCat(function () {
                    StockService.createSKU($scope.spuInfo).then(function () {

                        if (model.retoreLockShopStatus) {
                            EventService.emit(EventService.ev.LOCK_SHOP);
                            model.retoreLockShopStatus = false;
                        }
                    });
                    if(go){
                        $scope.spuInfo = SPUClass.newSPU(0);
                        EventService.emit(EventService.ev.START_CREATE_EDIT_SPU);
                        PageService.showDialog('CreateSPU');
                    }
                }

            );
        };

        //修改按钮
        $scope.saveEdit = function () {
            validateCat(function () {
                StockService.editSPUSKU(model.spuInfo, spuInfo);
            });
        };

        // 装填测试数据
        $scope.fill = function () {
            $log.log('fill');
            $scope.spuInfo.spu_name = "苹果";
            $scope.spuInfo.qcode = 'pg';
            $scope.spuInfo.cat_id = 1;

        };

        // 点击了速查码输入框
        $scope.clickQcodeInput = function () {
            if ($scope.qcodeList.length > 0) {
                $scope.isQcodeListShow = !$scope.isQcodeListShow;
            } else {
                $scope.isQcodeListShow = false;
            }
        };

        // 点击了类别输入框
        $scope.clickCatInput = function () {
            $scope.isCatListShow = !$scope.isCatListShow;
        };

        // 点击了速查码选项
        $scope.clickQcode = function (qcode) {
            $scope.spuInfo.qcode = qcode;
            $scope.isQcodeListShow = false;
        };

        // 点击了类别选项
        $scope.clickCat = function (cat) {
            $scope.spuInfo.cat_id = cat.cat_id;
            $scope.spuInfo.cat_name = cat.cat_name;
            $scope.isCatListShow = false;
            isStoListFalse();
        };

        // 点击了类别下拉选项中的新建类别按钮
        $scope.clickNewCat = function () {
            EventService.emit(EventService.ev.START_CREATE_EDIT_CAT);
        };
        $scope.clickNewSto = function () {
            EventService.emit(EventService.ev.CREATE_EDIT_WAREHOUSE,1);
        };

        $scope.delete = function () {
            PageService.showConfirmDialog('删除完该商品下的所有规格，该商品会自动被删除');
        };

        $scope.genQcode = function () {
            if ($scope.spuInfo.spu_name) {
                $scope.qcodeList = PYService.getPY($scope.spuInfo.spu_name);
            } else {
                $scope.qcodeList = [];
            }

            if ($scope.qcodeList.length > 1) {
                $scope.isQcodeListShow = true;
            } else {
                $scope.isQcodeListShow = false;
            }

            $scope.spuInfo.qcode = $scope.qcodeList[0];
        };
        $scope.quit = function () {
            if (angular.equals($scope.spuInfo, spuInfo)) {
                PageService.closeDialog();
            } else {
                PageService.showConfirmDialog('确定放弃编辑吗？', [], function () {
                    PageService.closeDialog();
                    if (model.retoreLockShopStatus) {
                        EventService.emit(EventService.ev.LOCK_SHOP);
                        model.retoreLockShopStatus = false;
                    }
                });
            }
        };

        var clickHandle = EventService.on(EventService.ev.CLICK_BODY, function (angularEvent, domEvent) {
            $timeout(function () {
                // 如果没在类别输入框上也没在下来列表上就关闭下拉列表
                if (!$(domEvent.target).hasClass('cat-name') && $scope.isCatListShow) {
                    $scope.isCatListShow = false;
                }
                if(!$(domEvent.target).hasClass("sto-name")&& isStoListhasTrue()){
                    isStoListFalse();
                }
            });

        });

        var createCatHandle = EventService.on(EventService.ev.CREATE_CAT_SUCCESS, function (event, catInfo) {
            $scope.spuInfo.cat_name = catInfo.cat_name;
            $scope.spuInfo.cat_id = catInfo.cat_id;
        });

        var keydownHandle = EventService.on(EventService.ev.KEY_DOWN, function (event, domEvent) {
            if (domEvent.keyCode == 27 && !PageService.isConfirmDialogShow && model.dialogId == PageService.dialogList.length) {
                $scope.$apply(function () {
                    $scope.quit();
                });
            }
        });

        // 当信息源变化时自动刷新
        var refreshDataHandle = EventService.on(EventService.ev.LOAD_CAT_SUCCESS, function (evnet, arg) {
            model.localTable.calc();
        });

        $scope.$on('$destroy', function () {
            keydownHandle();
            createCatHandle();
            clickHandle();
            refreshDataHandle();
        })

        ////库存、单价输入框不能输入负数
        $scope.onlyNumber = function(item,value){
            if(isNaN(item[value])){
                item[value]=parseFloat(item[value]);
            }
            if(item[value]< 0 ){
                 item[value]=0-item[value];   
            }     
        }
    }
]);
