// SPUClass SPU类，初始化,存储,编辑单个SPU
'use strict'

xy.factory('SPUClass', ['EventService', '$log', 'StockService',
    function(EventService, $log, StockService) {

        // 用于新增sku的模板
        var sku_template = {
                 sku_id: 0,
                 spec_name: '',
                 stock: '',
                 unit_price: '',
                 sku_sto_index: '2',
                 sku_sto_status: '1',
        };


        var SPUClass = {};
        // SPU类用来存储单个SPU
        function SPU() {
            var defaultStoId="";
            this.SPUCtor = function(spu_id) {
                var tmp_spu = {};
                // 参数存在的话, 通过spu_id装填一个已存在的SPU
                if (spu_id) {
                    // 通过spu_id获得被复制spu的信息
                    tmp_spu = StockService.get_spu_by_id(spu_id);
                    // $log.warn('tmp_spu = ', tmp_spu)
                    // 没找到发出警告
                    if (tmp_spu == 0) {
                        $log.error('SPU Ctor: 没找到指定SPU！')
                            // 找到了复制数据
                    } else {
                        this.spu_id = tmp_spu.spu_id;
                        this.spu_name = tmp_spu.spu_name;
                        // 速查码
                        this.qcode = tmp_spu.qcode;
                        // 所属类别id,name
                        this.cat_id = tmp_spu.cat_id;
                        this.cat_name = tmp_spu.cat_name;
                        // 在商品列表中显示时的排序优先级
                        this.spu_index = tmp_spu.spu_index;
                        // 启用状态 1 = 启用, 0 = 停用
                        this.spu_status = tmp_spu.spu_status;
                        // 包含的规格
                        this.skus = {data:[]};
                        for(var key in tmp_spu.skuStoData.data){
                            this.skus.data.push(tmp_spu.skuStoData.data[key])
                        };
                        this.delete = {}
                    }
                    //没参数, 新建一个空的
                } else {
                    this.spu_id = "";
                    this.spu_name = "";
                    this.qcode = "";
                    this.cat_id = "";
                    this.cat_name="";
                    this.spu_index = "2";
                    this.spu_status = "1";
                    this.status = "1";
                    this.skus = {data:[]};
                    //自带一条空规格
                    this.addSKU(0)
                }
            };
            //增加一个规格
            this.setDefaultStoId = function (sto_id) {
                defaultStoId = sto_id;
            };
            this.setDefaultStoIdALL=function (sto_id) {
                for(var key in this.skus.data){
                   if( this.skus.data[key].sto_id===undefined){
                       this.skus.data[key].sto_id = defaultStoId
                   }
                }
            };
            this.change = function (stoindex,sto_id) {
                this.skus.data[stoindex].sto_id=sto_id;
            };
            this.addSKU = function($index,sto_id) {
                var tmp_sku = {};
                if(this.skus.data[$index]===undefined){
                    this.skus.data[$index]={
                        sto_id:sto_id,
                        skuStoData:[]}
                }
                if(this.skus.data[$index].sto_id!=sto_id&&sto_id!=undefined){
                    this.skus.data[$index].sto_id=sto_id
                }
                angular.copy(sku_template, tmp_sku);
                this.skus.data[$index].skuStoData.push(tmp_sku)
            };
            // 移除一个SKU
            // 靠以下方法来索引sku:
            // 可以在ng-repeat中使用$index属性, 与数组下标是对应的, e.g. ng-click="functionName($index)"
            this.removeSTOSKU=function(stoIndex){
                if(stoIndex!=0){
                    this.skus.data.splice(stoIndex,1)
                }
            };
            this.removeSKU = function(index,stoIndex) {
                if (this.skus.data[stoIndex].skuStoData[index].sku_id) {//将删除的sku添加进一个数组中
                    if(!this.delete[stoIndex]){
                        this.delete[stoIndex] =[]
                    }
                    this.delete[stoIndex].push(this.skus.data[stoIndex].skuStoData[index].sku_id)
                }
                this.skus.data[stoIndex].skuStoData.splice(index, 1)

                // // 如果就剩一个sku了,不能再删
                // if (this.skus.length <= 1) {
                //     $log.log('至少有一个规格')
                //     // 否则标记为删除
                // } else {
                //     // 如果这是编辑SPU
                //     if (this.spu_id && (this.spu_id > 0)) {
                //         // 用标记的方式删除
                //         // this.skus[index].delete = 1
                //         this.skus[index].delete == undefined ? this.skus[index].delete = true : this.skus[index].delete = !this.skus[index].delete
                //         // 如果是创建SPU
                //     } else {
                //         // 直接删除数组元素
                //         this.skus.splice(index, 1)
                //     }
                //     // 现在改用这种标记的方式删除
                // }
            }

        }

        // 商品类别
        function Cat() {
            this.CatCtor = function(cat) {
                // 有参数则拷贝
                if (cat) {
                    this.cat_id = cat.cat_id;
                    this.cat_name = cat.cat_name;
                    this.cat_index = cat.cat_index;
                    this.status = cat.status

                //无参数则初始化为空
                } else {
                    this.cat_id = undefined;
                    // 类别名
                    this.cat_name = '';
                    // 优先显示,'1'=优先,'2'=正常
                    this.cat_index = '2';
                    // 是否启用,'1'=启用,='0'不启用
                    this.status = '1'
                }
            }
        }

        //new一个SPU
        // param spu_id 1.传入spu_id则拷贝
        //              2.不传则新建空的
        SPUClass.newSPU = function(spu_id) {
            var out = new SPU();
            out.SPUCtor(spu_id);
            return out
        };


        //new一个Cat
        SPUClass.newCat = function(cat){
            var out = new Cat();
            out.CatCtor(cat);
            return out
        };

        return SPUClass // or return model

    }
]);
