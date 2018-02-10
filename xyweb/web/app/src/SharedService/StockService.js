'use strict';

xy.factory('StockService', ['EventService', '$rootScope', '$log', '$q', 'NetworkService', '$filter', 'PageService', 'OrderInfoClassService', 'MiscService', 'LockService',
    function (EventService, $rootScope, $log, $q, NetworkService, $filter, PageService, OrderInfoClassService, MiscService, LockService) {

        var StockService = {};

        //test
        StockService.f1 = function () {
            return PageService.a;
        }

        // SPU的类别
        var cats = [];

        //所有SKU信息 没有索引，直接把服务器返回的数组拷贝进来
        var skus_info = [];
        var skus_sto = [];
        var sto_list = [];
        // 这个sku列表是按sku_id索引的，即：skuList2[sku_id]这样来获取sku
        var skuList2 = {};
        var stock = {}//库存数量和金额

        // skus_info = [{
        //     "sku_id": "6",
        //     "admin_uid": "2",
        //     "spu_id": "6",
        //     "spu_name": "123",
        //     "spu_index": "1",
        //     "qcode": "123",
        //     "spu_status": "1",
        //     "cat_id": "2",
        //     "cat_name": "hh",
        //     "cat_index": "0",
        //     "cat_status": "1",
        //     "spec_name": "123",
        //     "stock": "123",
        //     "unit_price": "123",
        //     "sku_index": "1",
        //     "status": "1",
        //     "reg_time": "1452610967",
        //     "update_time": "1452610967"

        //所有SPU的信息
        var spus_info = [];
        // spus_info = {
        //     [
        //         {

        //             "sku_id": "6",
        //             "admin_uid": "2",
        //             "spu_id": "1",
        //             "spu_name": "123",
        //             "spu_index": "1",    这层应该是spu信息，为了省事就一个sku的信息直接复制过来
        //             "qcode": "123",
        //             "spu_status": "1",
        //             "cat_id": "2",
        //             ...
        //             ...
        //             skus:[               第二层是sku信息

        //获取对 cats 的引用
        StockService.get_cats = function () {
            return cats;
        };

        //获取对 cats 的引用
        StockService.getCatList = function () {
            return cats;
        };

        StockService.getStoList = function () {
            return sto_list;
        };
        // 【弃用】获取对 skus_info 的引用
        StockService.get_skus_info = function () {
            return skus_info;
        };
        StockService.getStock = function () {
            return stock;
        };

        // 获取sku列表更直观的函数名
        StockService.getSkuList = function () {
            return skus_info;
        };
        StockService.getSkuStoList = function () {
            for (var i = 0; i < skus_sto.length; i++) {
                if (skus_sto[i] === "" || typeof(skus_sto[i]) === "undefined") {
                    skus_sto.splice(i, 1);
                    i = i - 1;
                }

            }
            return skus_sto;
        };

        // 新增加的skuList2使用sku_id索引
        StockService.getSkuList2 = function () {
            return skuList2;
        };

        //获取对 spus_info 的引用
        StockService.get_spus_info = function () {
            return spus_info;
        };

        //获取单个SPU
        StockService.get_spu_by_id = function (spu_id) {
            var out = {}
            var flagFound = false;
            for (var i = spus_info.length - 1; i >= 0; i--) {
                if (spus_info[i].spu_id == spu_id) {
                    //找到了
                    angular.copy(spus_info[i], out)
                    flagFound = true;
                    break;
                }
            }
            //找到了返回数据，没找到返回0
            if (flagFound) {
                return out;
            } else {
                return 0;
            }
        };

        // 获取单个sku
        // param  int sku_id skuID
        // return {}         找到了对象中是sku信息，没找到是空对象
        StockService.get_sku_by_id = function (sku_id) {
            var out = {};
            var flagFound = false;
            for (var i = skus_info.length - 1; i >= 0; i--) {
                if (skus_info[i].sku_id == sku_id) {
                    //找到了
                    angular.copy(skus_info[i], out)
                    flagFound = true;
                    break;
                }
            }

            //找到了返回数据，没找到返回0
            // if (flagFound) {
            //     return out;
            // } else {
            //     return 0;
            // }

            // 无论如何返回值是对象，这样改是为了访问返回值的属性不会报错
            return out;
        };

        // 本方法信息：
        // * @function queryCat 查询类别
        // * @param int type  就是api中type
        // * 成功或失败都发出 事件

        // api信息:
        //  * 查询商品分类
        //  * @param unsigned $type 模式 1=所有cat 2=有效cat，即status=1的cat
        //  * @return json_array {"data":[{数据库中的一行}{..}]}

        StockService.queryCat = function (type) {
            var defered = $q.defer();
            (function () {
                var dataToSend = {
                    type: type,
                };
                NetworkService.request('queryCat', dataToSend, function (data) {
                    // $log.log('queryCat response:', data);
                    // cats = data.data;
                    angular.copy(data.data, cats);

                    //status类型转为数字
                    // for (var i = 0; i < cats.length; i++) {
                    // cats[i].cat_index = Number(cats[i].cat_index);
                    //     cats[i].status = Number(cats[i].status);
                    // };
                    defered.resolve();
                    EventService.emit(EventService.ev.LOAD_CAT_SUCCESS);

                }, function (data) {
                    defered.reject();
                    EventService.emit(EventService.ev.LOAD_CAT_ERROR);
                });
            })();
            StockService.queryCatPromise = defered.promise;
            return defered.promise;
        };

        // 本方法信息
        // * @function editCat
        // * @param type int  就是api中type
        // * 成功或失败都发出 事件
        StockService.editCat = function (catInfo) {
            LockService.getLockShopStatus(0, function () {

                var dataToSend = {
                    cat_id: catInfo.cat_id,
                    cat_name: catInfo.cat_name,
                    cat_index: catInfo.cat_index,
                    status: Number(catInfo.status),
                }
                NetworkService.request('editCat', dataToSend, function (data) {
                    EventService.emit(EventService.ev.EDIT_CAT_SUCCESS, {}, 1)
                }, function () {
                    EventService.emit(EventService.ev.EDIT_CAT_ERROR, {}, 1)
                }, 1);
            });
        };

        //删除类别
        StockService.deleteCat = function (item) {
            LockService.getLockShopStatus(0, function () {
                var dataToSend = {
                    cat_id: item.cat_id,
                }
                NetworkService.request('deleteCat', dataToSend, function (data) {

                    EventService.emit(EventService.ev.DELETE_CAT_SUCCESS);
                    PageService.closeDialog();


                }, function () {
                    $rootScope.$emit('DELETE_CAT_ERROR');
                }, 1)
            });
        };

        //创建类别
        StockService.createCat = function (catInfo) {

            LockService.getLockShopStatus(0, function () {
                var dataToSend = {
                    cat_name: catInfo.cat_name,
                    cat_index: catInfo.cat_index,
                    status: Number(catInfo.status),
                }

                NetworkService.request('createCat', dataToSend, function (data) {

                    $log.debug('createCat response:', data);

                    var cat = {};
                    cat.cat_name = catInfo.cat_name;
                    cat.cat_id = data.data;
                    $rootScope.$emit('CREATE_CAT_SUCCESS', cat);

                }, function () {
                    $rootScope.$emit('CREATE_CAT_ERROR');
                });
            });
        };


        // 新建SPUSKU（内涵至少1个SKU, 虽然接口名叫CreatSKU其实是一起创建, 这个接口还有第二种用法目前用不着
        StockService.createSKU = function (spuInfo1, callback) {
            var defered = $q.defer();
            var haveSku = false;
            LockService.getLockShopStatus(0, function () {
                var spuInfo = {};
                angular.copy(spuInfo1, spuInfo);
                if (spuInfo.qcode == undefined) {
                    spuInfo.qcode = '';
                }

                var empty = [];
                for (var i = 0; i < spuInfo.skus.data.length; i++) {
                    for (var j = 0, skusData = spuInfo.skus.data[i].skuStoData; j < skusData.length; j++) {
                        if (!skusData[j].spec_name && !skusData[j].unit_price && !skusData[j].stock) {
                            skusData.splice(j, 1)
                        } else {
                            haveSku = true
                        }
                    }
                    if (!haveSku) {
                        PageService.showSharedToast("请给商品添加规格");
                        return;
                    }
                    // for (var j = 0, skusData = spuInfo.skus.data[i].skuStoData; j < skusData.length; j++) {
                    //     if (!empty[i] && !skusData[j].spec_name && !skusData[j].unit_price && !skusData[j].stock) {
                    //
                    //     }
                    // }
                    for (var j = 0, skusData = spuInfo.skus.data[i].skuStoData; j < skusData.length; j++) {
                        if (j != 0 && skusData[j].spec_name.toString() == '') {
                            skusData.splice(j, 1);
                            continue;
                        }
                        skusData[j].spec_name = skusData[j].spec_name.toString();
                        skusData[j].stock = Number(skusData[j].stock);
                        skusData[j].unit_price = Number(skusData[j].unit_price);
                        skusData[j].sku_sto_index = Number(skusData[j].sku_sto_index);
                        skusData[j].sku_sto_status = Number(skusData[j].sku_sto_status);
                        skusData[j].sku_storage_id = 0;
                        skusData[j].sku_id = 0;
                    }
                }
                //把skuData的内容变成json字符串
                var str_skuData = JSON.stringify(spuInfo.skus);
                // $log.log('str_skuData', str_skuData);
                //装填dataToSend,包括调整spu信息的类型
                var dataToSend = {
                    // spu_id: "",
                    spu_name: spuInfo.spu_name.toString(),
                    spu_index: Number(spuInfo.spu_index),
                    qcode: spuInfo.qcode.toString(),
                    cat_id: Number(spuInfo.cat_id),
                    spu_status: Number(spuInfo.spu_status),
                    skuStoData: str_skuData,
                };
                NetworkService.request('createSKU', dataToSend, function (data) {
                    if (data.EC > 0) {
                        $log.error('createSKU success response = ', data);
                        defered.resolve();
                        EventService.emit(EventService.ev.START_MANAGE_SKU);
                        PageService.showSharedToast('创建成功！');
                        PageService.closeDialog();
                    } else {
                        $log.warn('createSKU error', data);
                        EventService.emit(EventService.ev.CREATE_SKU_ERROR, {}, 1)
                    }
                }, function () {
                    EventService.emit(EventService.ev.CREATE_SKU_ERROR, {}, 1)
                })
            });
            return defered.promise
        };

        StockService.editSPUSKU = function (spuInfoDisplay, spuInfoOrigin) {
            LockService.getLockShopStatus(0, function () {
                //复制一份避免改掉model里的值
                var spuInfo = {}, empty = [];
                angular.copy(spuInfoDisplay, spuInfo);
                if (spuInfo.qcode == undefined) {
                    spuInfo.qcode = '';
                }
                for (let key in spuInfoOrigin.skus.data) {
                    var itemOrigin = spuInfoOrigin.skus.data[key];
                    if (spuInfo.skus.data[key] == undefined) { // 如果删了整个仓库的情况
                        spuInfo.skus.data[key] = {};
                        let allDeleteItem = spuInfo.skus.data[key];
                        allDeleteItem.sto_id = itemOrigin.sto_id;
                        allDeleteItem.sto_name = itemOrigin.sto_name;
                        allDeleteItem.skuStoData = [];
                        for (let j = 0, item = itemOrigin.skuStoData; j < item.length; j++) {
                            item[j].delete = true;
                            spuInfo.skus.data[key].skuStoData.push(item[j])
                        }
                    }
                    if (spuInfo.delete[key] != undefined) {
                        for (let j = 0, item = itemOrigin.skuStoData; j < item.length; j++) {
                            if (spuInfo.delete[key].indexOf(item[j].sku_id) > -1) {
                                item[j].delete = true;
                                spuInfo.skus.data[key].skuStoData.push(item[j])
                            }
                        }
                    }
                }
                for (var i = 0; i < spuInfo.skus.data.length; i++) {

                    for (var j = 0, skusData = spuInfo.skus.data[i].skuStoData; j < skusData.length; j++) {
                        if (!skusData[j].spec_name && !skusData[j].unit_price && !skusData[j].stock) {
                            skusData.splice(j, 1);
                        }
                    }
                    // for (var j = 0, skusData = spuInfo.skus.data[i].skuStoData; j < skusData.length; j++) {
                    //     if (!empty[i] && !skusData[j].spec_name && !skusData[j].unit_price && !skusData[j].stock) {
                    //
                    //     }
                    // }
                    for (var j = 0, skusData = spuInfo.skus.data[i].skuStoData; j < skusData.length; j++) {
                        if (j != 0 && skusData[j].spec_name.toString() == '') {
                            skusData.splice(j, 1);
                            continue;
                        }
                        skusData[j].spec_name = skusData[j].spec_name.toString();
                        skusData[j].stock = Number(skusData[j].stock);
                        skusData[j].unit_price = Number(skusData[j].unit_price);
                        skusData[j].sku_sto_index = Number(skusData[j].sku_sto_index);
                        skusData[j].sku_sto_status = Number(skusData[j].sku_sto_status);
                        skusData[j].sku_storage_id = skusData[j].sku_storage_id ? skusData[j].sku_storage_id : 0;
                        skusData[j].sku_id = skusData[j].sku_id ? skusData[j].sku_id : 0;
                    }
                }
                // 得装填的skus信息

                //sku信息类型调整为接口要求的类型(应该不用管类型)

                //把skuData的内容变成json字符串
                if (spuInfo.skus) {
                    var str_skuData = JSON.stringify(spuInfo.skus);
                }
                //装填dataToSend,包括调整spu信息的类型
                var dataToSend = {
                    spu_id: spuInfo.spu_id,
                    spu_name: spuInfo.spu_name.toString(),
                    spu_index: Number(spuInfo.spu_index),
                    qcode: spuInfo.qcode.toString(),
                    cat_id: Number(spuInfo.cat_id),
                    spu_status: Number(spuInfo.spu_status),
                    skuStoData: str_skuData,
                };

                NetworkService.request('editSPUSKU', dataToSend, function (data) {
                    if (data.EC > 0) {
                        $log.debug('editSPUSKU success response = ', data);
                        EventService.emit(EventService.ev.EDIT_SPUSKU_SUCCESS, {}, 1)
                        PageService.showSharedToast('编辑成功！');
                    } else {
                        // $log.warn('editSPUSKU error', data);
                        EventService.emit(EventService.ev.EDIT_SPUSKU_ERROR, {}, 1)
                    }
                }, function () {
                    EventService.emit(EventService.ev.EDIT_SPUSKU_ERROR, {}, 1)
                });

                return dataToSend;
            });
        };
        StockService.querySKUSTO = function (type, sto_id) {
            var dataType = {
                type: type
            };
            if (sto_id) {
                dataType.sto_id = sto_id
            }
            var defered = $q.defer();
            NetworkService.request("querySKUSTO", dataType, function (data) {
                angular.copy(data.data.data, skus_sto);
                for (var key in skuList2) { // 设置索引为id的对象
                    skuList2[key] = undefined;
                }
                for (var item of data.data.data) {
                    skuList2[item.sku_id] = {};
                    angular.copy(item, skuList2[item.sku_id])
                }
                defered.resolve()
            }, function () {
                defered.reject()
            });
            return defered.promise
        };
        StockService.querySTO = function (type) {
            var dataType = {
                type: type
            }, defered = $q.defer();
            NetworkService.request("querySto", dataType, function (data) {
                if (data.EC > 0) {
                    angular.copy(data.data, sto_list);
                    defered.resolve()
                }
            }, function () {
                defered.reject()
            });
            return defered.promise;
        };
        // 删除SKU
        StockService.deleteSKU = function (sku_id) {
            LockService.getLockShopStatus(0, function () {
                var dataToSend = {
                    sku_id: sku_id,
                };

                NetworkService.request('deleteSKU', dataToSend, function (data) {

                    $log.log('deleteSKU data = ', data);

                    // if (data.EC > 0) {
                    EventService.emit(EventService.ev.DELETE_SKU_SUCCESS, {}, 1);
                    // }

                }, function () {
                    EventService.emit(EventService.ev.DELETE_SKU_ERROR, {}, 1);
                });
            });
        };

        // @function calc_spus_info 把SKU信息组织成第一层SPU信息，每个SPU信息里有各SKU信息
        // @param    skus           众SKU信息
        // @return   result         重新编排完的两层数组
        var calc_spus_info = function (skus) {

            var result = [];
            for (var i = 0; i < skus.length; i++) {
                if (skus[i] != undefined) {
                    result.push(skus[i])
                }
            }
            // var result = [];
            // var tmp_spu_id;
            // var flag_exist;
            // var tmp_sku_ori;
            // var tmp_sku_des;
            // var tmp_spu;
            // for (var i = 0; i < skus.length; i++) {
            //     tmp_sku_ori = skus[i];
            //     tmp_spu_id = skus[i].spu_id;
            //
            //     flag_exist = false;
            //     for (var j = 0; j < result.length; j++) {
            //         if (result[j].spu_id == tmp_spu_id) {
            //             tmp_sku_des = {};
            //             angular.copy(tmp_sku_ori, tmp_sku_des);
            //             result[j].skus.push(tmp_sku_des);
            //             flag_exist = true;
            //             break;
            //         }
            //     }
            //     ;
            //     if (!flag_exist) {
            //         tmp_spu = {};
            //         tmp_sku_des = {};
            //         //装填spu信息（因为是sku信息的子集所以这里直接拷贝了）
            //         angular.copy(tmp_sku_ori, tmp_spu);
            //         //装填此sku信息，准备挂在新建的spu下
            //         angular.copy(tmp_sku_ori, tmp_sku_des);
            //
            //         //把sku信息放入spu对象里
            //         tmp_spu.skus = [];
            //         tmp_spu.skus.push(tmp_sku_des);
            //
            //         //把spu放进结果里
            //         result.push(tmp_spu);
            //     }
            // }
            return result;
        };

        // @function querySKU 从服务器加载SKU信息，会装填 skus_info 和 spus_info, 完成后发出事件,目前只是用type==1即加载全部sku信息的方式，然后由前端筛选并装填
        StockService.querySKU = function (type) {
            var dataToSend = {
                type: type,
            }, defered = $q.defer();
            NetworkService.request('querySKU', dataToSend, function (data) {
                //如果获取的是全部SKU信息
                if (type == 1) {
                    //装填到skus_info;
                    angular.copy(data.data, skus_info);
                    // 装填到skuList2
                    // for (var key in skuList2) {
                    //     skuList2[key] = undefined;
                    // }
                    // for (var v in data.data) {
                    //     skuList2[data.data[v].sku_id] = {};
                    //     angular.copy(data.data[v], skuList2[data.data[v].sku_id]);
                    // }
                    // $log.log('querySKU skuList2: ',skuList2);
                    // 计算每个sku总价
                    // for (var i = 0; i < skus_info.length; i++) {
                    //    if(skus_info[i]!=undefined){
                    //        for(var key in skus_info[i].skuStoData.data ){
                    //             var item = skus_info[i].skuStoData.data[key];
                    //            item.tot_price=0;
                    //            item.tot_stock =0;
                    //             for(var k =0,len=item.skuStoData.length ;k<len;k++){
                    //                 item.tot_price += Number(item.skuStoData[k].unit_price)* Number(item.skuStoData[k].stock);
                    //                 item.tot_stock+=Number(item.skuStoData[k].stock)
                    //             }
                    //        }
                    //    }
                    // }
                    //组织成第一层SPU第二层SKU的形式
                    spus_info = calc_spus_info(skus_info);
                    // $log.log('querySKU skus_info = ', skus_info);
                    // $log.log('querySKU spus_info = ', spus_info);
                    //发出事件
                    defered.resolve();
                    EventService.emit(EventService.ev.LOAD_SKUS_INFO_SUCCESS, skus_info);
                }
            }, function (data, resolve, reject) {
                defered.reject();
                EventService.emit(EventService.ev.LOAD_SKUS_INFO_ERROR);
            });
            return defered.promise;
        };

        //删除SPU
        StockService.deleteSPU = function (spu_id) {
            LockService.getLockShopStatus(0, function () {
                var dataToSend = {
                    spu_id: spu_id,
                }
                NetworkService.request('deleteSPU', dataToSend, function (data) {

                    $log.log('deleteSPU data : ', data);

                    if (data > 0) {
                        $log.log('deleteSPU success');
                        $rootScope.$emit('DELETE_SPU_SUCCESS', data);
                    } else {
                        $log.log('deleteSPU error');
                        $rootScope.$emit('DELETE_SPU_ERROR', data);
                    }

                }, function () {
                    $rootScope.$emit('DELETE_SPU_ERROR');
                });
            });
        };

        // 生成盘点单api发送数据cart属性需要装填的字符串
        function genCartStrForAPI(orderInfo) {
            var cart_arr = [];
            var cartItemList = orderInfo.cartAgent.cartItemList;
            var tar_item;
            var src_item;
            for (var v in cartItemList) {
                src_item = cartItemList[v];
                tar_item = {
                    sto_id: src_item.sto_id,
                    sku_id: src_item.sku_id,
                    quantity: src_item.quantity,
                    cat_name: src_item.cat_name,
                    spec_name: src_item.spec_name,
                    spu_name: src_item.spu_name,
                    unit_price: src_item.unit_price,
                }
                if (src_item.isedit) {
                    tar_item.isedit = src_item.isedit
                }
                cart_arr.push(tar_item)
            }

            var cart_data = {
                data: cart_arr,
            };
            var cart_str = JSON.stringify(cart_data);

            return cart_str;
        }


        // 生成盘点单api发送数据
        function genDataToSend(orderInfo) {
            // var now = parseInt(new Date().getTime() / 1000);

            var dataToSend = {
                remark: orderInfo.remark,
                check_uid: orderInfo.check_uid,
                reg_time: orderInfo.reg_time,
                class: orderInfo.class,
                sto_id: orderInfo.sto_id
            };
            if (orderInfo.class == 54) {
                dataToSend.lockShopToken = orderInfo.lockShopToken;

                dataToSend.new_sto_id = orderInfo.new_sto_id;
            }
            // 把购物车数据转成字符串
            var cart_str = genCartStrForAPI(orderInfo);
            // 装填购物车信息
            dataToSend.cart = cart_str;
            return dataToSend;
        }

        // 创建盘点单
        StockService.createStockTaking = function (orderInfo, token) {
            var dataToSend = genDataToSend(orderInfo);
            dataToSend.lockShopToken = token;
            NetworkService.request('createStockTaking', dataToSend, function () {
                EventService.emit('CREATE_STOCK_TAKING_SUCCESS');
                EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
            }, function () {
                EventService.emit('CREATE_STOCK_TAKING_ERROR');
            });
        };

        // 创建盘点单 草稿单据
        StockService.createStockTakingDraft = function (orderInfo) {
            var dataToSend = genDataToSend(orderInfo);
            NetworkService.request('createStockTakingDraft', dataToSend, function (data) {
                EventService.emit(EventService.ev.ORDER_CREATE_DRAFT_SUCCESS);
                EventService.emit(EventService.ev.START_VIEW_DRAFT, 1);
            }, function () {
                EventService.emit(EventService.ev.ORDER_CREATE_DRAFT_ERROR)
            })
        };
        //创建调拨单 草稿单据
        StockService.createRequisitionDraft = function (orderInfo) {
            var dataToSend = genDataToSend(orderInfo);
            NetworkService.request('createRequisitionDraft', dataToSend, function (data) {
                EventService.emit(EventService.ev.ORDER_CREATE_DRAFT_SUCCESS);
                EventService.emit(EventService.ev.START_VIEW_DRAFT, 1);
            }, function () {
                EventService.emit(EventService.ev.ORDER_CREATE_DRAFT_ERROR);
            })
        };
        //创建盘点单
        StockService.createRequisition = function (orderInfo) {
            var dataToSend = genDataToSend(orderInfo);
            // $log.error(dataToSend);
            NetworkService.request('createRequisition', dataToSend, function (data) {
                if (data.EC > 0) {
                    EventService.emit(EventService.ev.CREATE_STOCK_REQUISITION_SUCCESS);
                    EventService.emit(EventService.ev.START_VIEW_TODAY_ORDER);
                }
            }, function () {

            });
        };
        // 查询盘点单
        StockService.getStockOrder = function (wid, orderClass, callback) {
            var defered = $q.defer();
            var dataToSend = {
                wid: wid,
            };
            NetworkService.request('warehouse_get_', dataToSend, function (data) {
                var tmpOrderInfo;
                switch (orderClass) {
                    case 53:
                        tmpOrderInfo = OrderInfoClassService.newStockTakingOrderFromApiData(data.data);
                        break;
                    case 54:
                        tmpOrderInfo = OrderInfoClassService.newRequisitionOrderFromApiData(data.data);
                        break;
                }
                tmpOrderInfo.cartAgent.cartItemList = MiscService.formatMoneyObject(tmpOrderInfo.cartAgent.cartItemList, ['unit_price']);
                $log.log('getStockTaking tmpOrderInfo: ', tmpOrderInfo);
                defered.resolve(tmpOrderInfo);
                if (callback) {
                    callback(tmpOrderInfo);
                }
            }, function () {

            });
            return defered.promise;
        };

        // 编辑盘点单
        StockService.editStockTaking = function (orderInfo) {
            var dataToSend = {
                wid: orderInfo.wid,
                remark: orderInfo.remark,
            };
            NetworkService.request('warehouse_edit_', dataToSend, function () {

            }, function () {

            });
        };

        //初始化
        var initStockService = function () {

            //创建SKU/SPU成功自动刷新
            $rootScope.$on('CREATE_SKU_SUCCESS', function () {
                StockService.querySKU(1);
            });

            //编辑SPU成功自动刷新
            EventService.on(EventService.ev.EDIT_SPUSKU_SUCCESS, function () {
                StockService.querySKU(1);
            });

            //删除SPU成功自动刷新
            $rootScope.$on('DELETE_SPU_SUCCESS', function () {
                StockService.querySKU(1);
            });

            //创建类别成功自动刷新
            $rootScope.$on('CREATE_CAT_SUCCESS', function () {
                StockService.queryCat(1);
            });
            EventService.on(EventService.ev.EDIT_CAT_SUCCESS, function () {
                StockService.queryCat(1);
            });
            // 创建盘点单成功更新库存
            EventService.on(EventService.ev.CREATE_STOCK_TAKING_SUCCESS, function () {
                StockService.querySKU(1);
            });
            EventService.on(EventService.ev.CREATE_STOCK_REQUISITION_SUCCESS, function () {
                StockService.querySKUSTO(1);
            });
            //删除类别自动刷新
            EventService.on(EventService.ev.DELETE_CAT_SUCCESS, function () {
                StockService.queryCat(1);
            });
            //删除SKU自动更新
            EventService.on(EventService.ev.DELETE_SKU_SUCCESS, function () {
                StockService.querySKU(1);
            });
        };

        initStockService();

        return StockService;
    }
]);