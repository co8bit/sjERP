'use strict';

xy.factory('CartClassService', ['$rootScope', '$log',
    function ($rootScope, $log) {

        // 目前的对原型继承的用法是:原型函数内定义函数，包括初始化函数，继承的目的是继承这些函数，
        // 原型链末端的函数作为类用来new，每次new完之后调用原型链上的初始化函数将属性赋值到新对象上(如有必要)
        // 建议将这一过程封装到一个接口里，例如newCart()


        // $log.debug('CartClassService init')

        var CartClassService = {}; // or xxxxService = {}

        function cartItemClass() {
            // 初始化购物车条目
            this.initCartItem = function () {
                this.sto_id= -1;
                this.sto_name ="";
                this.sku_id = -1;
                this.spu_name = "";
                this.cat_name = "";
                this.spec_name = "";
                this.unit_price = '';
                this.quantity = '';
                this.pile_price = 0;
                this.stock = '';
                this.last_selling_price = '';
                this.comment = '';
            };
            // 从同类型对象拷贝
            this.copy = function (src) {
                if (src) {
                    this.sto_id = src.sto_id ==undefined?"":src.sto_id;
                    this.sto_name = src.sto_name;
                    this.sku_id = src.sku_id == undefined ? '' : src.sku_id;
                    this.spu_name = src.spu_name;
                    this.cat_name = src.cat_name;
                    this.spec_name = src.spec_name;
                    this.comment = src.comment;
                    this.unit_price = parseFloat(src.unit_price);
                    this.quantity = parseFloat(src.quantity);
                    this.pile_price = parseFloat(src.pile_price);
                    this.last_selling_price = parseFloat(src.last_selling_price);
                    this.stock = parseFloat(src.stock);
                    this.isedit = src.isedit ? src.isedit:false;
                }
            };
            // 从(自Api返回值parse来的obj对象)拷贝,和copy的区别是字段名不一样
            this.copyFromServerDataObj = function (cartObj) {
                this.sto_name = cartObj.sto_name;
                this.sto_id =cartObj.sto_id;
                this.spu_id = cartObj.spu_id;
                this.sku_id = cartObj.sku_id;
                this.spu_name = cartObj.spu_name;
                this.cat_name = cartObj.cat_name;
                this.spec_name = cartObj.spec_name;
                this.comment = cartObj.comment;
                this.sn = cartObj.sn;
                this.freightCost = cartObj.freightCost; //分摊金额
                // this.pilePrice = cartObj.pilePrice;//分摊金额和元金额总和
                // 服务器的单价字段名有两种可能
                if (cartObj.unitPrice) {
                    this.unit_price = cartObj.unitPrice
                } else {
                    this.unit_price = cartObj.unit_price
                }
                // this.unit_price = cartObj.unitPrice;
                this.quantity = cartObj.quantity;
                this.pile_price = cartObj.pilePrice;
                this.last_selling_price = cartObj.last_selling_price;
                this.stock = cartObj.stock;

            };

            // 将该条目设定为某SPU x
            // 将该条目设定为某SKU
            this.fillWithSKU = function (sku, type) {

                // 复制SPU名
              this.sto_id = sku.sto_id;

                this.sto_name = sku.sto_name;
                this.sku_id = sku.sku_id;
                this.spu_name = sku.spu_name;
                this.cat_name = sku.cat_name;
                this.spec_name = sku.spec_name;
                // 复制SPU中的SKU信息到待选择规格列表
                //angular.copy(spu.skus, this.alter_skus)
                // 暂时选中第一个规格
                //angular.copy(this.alter_skus[0], this.selected_sku)
                // 设定价格
                // $log.error("this1",this);
                // $log.error("sku",sku.unit_price);
                this.unit_price = sku.unit_price;
                // $log.error("this",this);
                // 数量保持不变
                // this.quantity = this.quantity
                // 设定价格
                this.stock = parseFloat(sku.stock);
                this.pile_price = parseFloat(this.quantity * this.unit_price);
                this.last_selling_price = parseFloat(sku.last_selling_price);
            }

            // 【这个版本选货界面sku都展开了】
            // 选定一种规格
            // this.selectSKU = function(sku) {
            //     // 复制sku对象
            //     angular.copy(sku, this.selected_sku);
            //     // 设定单价
            //     this.unit_price = this.selected_sku.unit_price;
            //     // 设定总价
            //     this.pile_price = this.quantity * this.unit_price;
            // }

            this.updatePrice = function () {
                this.pile_price = this.quantity * this.unit_price;
            }
        }

        //购物车条目类型
        // function cartItem() {}
        // cartItem.prototype = new cartItemProto()

        //返回一个新购物车条目
        CartClassService.newCartItem = function () {
            var out = new cartItemClass();
            out.initCartItem();
            return out;
        };

        // 购物车// 购物车// 购物车// 购物车// 购物车// 购物车// 购物车// 购物车// 购物车// 购物车
        function cartAgentClass() {
            this.cartAgentClassCtor = function () {

                //购物车条目列表
                this.cartItemList = {};
                // 正在添加的和正在修改的条目
                this.editingCartItem = CartClassService.newCartItem();

                // 总sku数
                this.cartItemListLength = 0;
                // 总价
                this.totPrice = 0;
                this.freight = 0;

            };
            this.addCountAndPrice = function (num1,num2){
                for (var k in this.cartItemList){
                    this.cartItemList[k].count = num1;
                    this.cartItemList[k].price = num2;
                }
            };
            // 从服务器返回的购物车信息装填
            //
            this.fillWithApiData = function (cart) {
                var tmpCartItem;
                for (var v in cart) {
                    tmpCartItem = CartClassService.newCartItem();
                    tmpCartItem.copyFromServerDataObj(cart[v]);
                    this.cartItemList[cart[v].sku_id] = tmpCartItem;
                    $log.log(tmpCartItem, '\n\n\n')
                }
                this.updateCartInfo();
            };

            //将条目添加至购物车
            this.addToCart = function (item,isStockTaking) {
                var cartItem = angular.copy(item, cartItem);
                cartItem.unit_price = cartItem.unit_price == '' ? 0 : parseFloat(cartItem.unit_price);
                cartItem.quantity = cartItem.quantity == '' ? 0 : parseFloat(cartItem.quantity);

                // $log.log('addToCart', cartItem);
                var sku_id = cartItem.sku_id;

                // 数量大于0添加到cartItemList
                if (cartItem.quantity > 0||(isStockTaking==true&&cartItem.quantity>=0)) {
                    // 先复制一个传入cartItem
                    var tmpItem = CartClassService.newCartItem();
                    tmpItem.copy(cartItem);
                    // 加入购物车
                    this.cartItemList[sku_id] = tmpItem;

                    // 数量小于等于0直接删除
                } else {
                    if (this.cartItemList[sku_id]) {
                        delete this.cartItemList[sku_id];
                    }
                }
                this.updateCartInfo();
                // 用一个空的cartItem替代this.editingCartItem
                this.editingCartItem = CartClassService.newCartItem();
            };

            //盘点单未改动盘点条目
            this.deleteFromCart = function (item) {
                var cartItem = angular.copy(item, cartItem);
                var sku_id = cartItem.sku_id;
                if (this.cartItemList[sku_id]) {
                    delete this.cartItemList[sku_id];
                }
                this.updateCartInfo();
                // 用一个空的cartItem替代this.editingCartItem
                this.editingCartItem = CartClassService.newCartItem();
            };


            //删除一个购物车条目
            this.deleteItem = function (cartItem) {

                delete this.cartItemList[cartItem.sku_id];

                this.updateCartInfo();
            };


            // 从sku设定当前cartItem
            this.setEditingCartItemBySku = function (sku, type) {
                this.editingCartItem = CartClassService.newCartItem();
                this.editingCartItem.fillWithSKU(sku, type);
            };

            this.setEditingCartItemPriceAndQuantity = function(cartItem){
                this.editingCartItem.unit_price = cartItem.unit_price;
                this.editingCartItem.quantity = cartItem.quantity;
            };

            // 从已存在的cartItem设定当前正在编辑的cartItem
            this.setEditingCartItemByCartItem = function (cartItem) {
                this.editingCartItem = CartClassService.newCartItem();
                this.editingCartItem.copy(cartItem);
            };


            // 获取正在修改的购物车条目
            this.getEditingCartItem = function () {
                return this.editingCartItem;
            };

            ///////
            ///////
            ///////
            //下面是更新购物车信息,都统一到 updateCartInfo 里

            // 计算购物车总价
            this.calcTotalPrice = function () {
                var tot = 0;
                for (var i in this.cartItemList) {
                    this.cartItemList[i].updatePrice();
                    tot += this.cartItemList[i].pile_price;
                }
                // if (this.freight > 0) {//总金额加上运费
                //     tot += parseFloat(this.freight.toFixed(2));
                // }
                this.totPrice = tot;
            };

            // 更新购物车中条目数
            this.updateCartItemListLength = function () {
                this.cartItemListLength = Object.getOwnPropertyNames(this.cartItemList).length;
            };

            // 计算正在编辑的货品的价格
            this.updateEditingItemPrice = function () {
                if (this.editingCartItem) {
                    this.editingCartItem.updatePrice();
                }
            };


            // 更新购物车信息,购物车内容有变动时调用
            this.updateCartInfo = function () {
                this.calcTotalPrice();
                this.updateCartItemListLength();

            }

        }

        // 盘点单专用购物车，把计算总价
        function StockTakingCartAgent() {
            this.StockTakingCartAgentCtor = function () {
                this.cartAgentClassCtor();

                // 总盈亏
                this.totProfitAndLossValue = 0;
                this.totProfitAndLossQuantity = 0;


                // 从服务器返回的购物车信息装填
                // 注意！！！装填的不是cartItem对象,是普通对象
                // param string str 服务器返回的购物车信息(json 字符串)
                // this.copyFromApiCartStr = function(cart) {
                //     var cartArr;
                //     cartArr = JSON.parse(str);
                //     for (var v of cartArr) {
                //         this.cartItemList[v.sku_id] = v;
                //     }
                // }

                // 更新总盈亏件数和价值
                this.updateTotProfitAndLossValueAndQuantity = function () {
                    // var totValue = 0;
                    // var totQuantity = 0;
                    // var skuList2 = StockService.getSkuList2();
                    // for (var key in this.cartItemList) {
                    //     // $log.log('updateTotProfitAndLossValue',this.cartItemList[key].quantity,skuList2[this.cartItemList[key].sku_id].stock,skuList2[this.cartItemList[key].sku_id].unit_price);
                    //     totQuantity += Number(this.cartItemList[key].quantity) - Number(skuList2[this.cartItemList[key].sku_id].stock);
                    //     totValue += Number(this.cartItemList[key].quantity - Number(skuList2[this.cartItemList[key].sku_id].stock)) * Number(skuList2[this.cartItemList[key].sku_id].unit_price);
                    // }
                    // this.totProfitAndLossValue = totValue;
                    // this.totProfitAndLossQuantity = totQuantity;
                }

                // 新的updateCartInfo
                this.updateCartInfo = function () {
                    this.calcTotalPrice();
                    this.updateCartItemListLength();
                    // this.updateTotProfitAndLossQuantity();
                    // this.updateTotProfitAndLossValue();
                    this.updateTotProfitAndLossValueAndQuantity();
                }
            }
        }

        StockTakingCartAgent.prototype = new cartAgentClass();

        // 生成一个购物车代理
        CartClassService.newCartAgent = function () {
            var out = new cartAgentClass();
            out.cartAgentClassCtor();
            return out;
        }

        CartClassService.newStockTakingCartAgent = function () {
            var out = new StockTakingCartAgent();
            out.StockTakingCartAgentCtor();
            return out;
        }

        //调拨单专用购物车
        function RequisitionCartAgent(){
            this.RequisitionCartAgentCtor = function(){
                this.cartAgentClassCtor();
                // 总盈亏
                this.totProfitAndLossValue = 0;
                this.totProfitAndLossQuantity = 0;
            }
        }
        RequisitionCartAgent.prototype = new cartAgentClass();

        CartClassService.newRequisitionCartAgent = function () {
            var out = new RequisitionCartAgent();
            out.RequisitionCartAgentCtor();
            return out;
        };

        return CartClassService; // or return xxxxService

    }
]);
