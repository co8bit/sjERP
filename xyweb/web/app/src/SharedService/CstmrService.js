// 2016.04.30 正在修改客户服务
'use strict';

xy.factory('CstmrService', ['$rootScope', 'EventService', '$log', 'NetworkService', 'ClassService','MiscService','PageService', '$q',
    function($rootScope, EventService, $log, NetworkService, ClassService, MiscService, PageService, $q) {

        // 作为本Service的返回值
        var CstmrService = {};

        // 往来单位相关服务 搜索 WLDW
        CstmrService.company = {};
        // 联系人相关服务 搜索 lXR
        CstmrService.contact = {};
        // 停车地址 搜索 TCDZ
        CstmrService.parkAddress = {};

        // 往来单位列表
        var companyList = [];
        // 联系人
        var contactList = [];
        // 停车地址
        var parkAddressList = [];
        // 对某往来单位待收款订单
        // var receivableOrderList = [];
        // // 对某往来单位待付款订单
        // var payableOrderList = [];


        // WLDW 往来单位-- start -------------------------------------------------------------------------------------------------------------------------------------

        // 获取往来单位引用
        CstmrService.company.getCompanyList = function() {
            return companyList;
        }

        // 通过往来单位名字在本地查找往来单位，使用 '==' 匹配
        CstmrService.company.findCompanyByName = function(companyName) {
            var out = undefined;
            for (var key in companyList) {
                // $log.log('findCompanyByName ',companyList[key].name,companyName);
                if (companyList[key].name == companyName) {
                    out = companyList[key];
                    break;
                }
            }

            return out;
        }

        //API查询往来单位
        CstmrService.company.queryList = function(type,callback) {
            var defered = $q.defer();
            var dataToSend = {
                type: type,
            }
            NetworkService.request('company_queryList', dataToSend, function(data) {
                angular.copy(data.data, companyList);
                if (callback) {
                    callback();
                }
                defered.resolve();
                EventService.emit(EventService.ev.COMPANY_LIST_LOAD_SUCCESS);
            }, function() {
                defered.reject();
                $log.warn('Company_queryList error')
            })
            return defered.promise;
        }



        EventService.on(EventService.ev.SALES_ORDER_INIT,function(event,arg){
            contactList = [];
        })

        //API查询单个往来单位
        // CstmrService.company.queryOne = function(cid) {
        //     var dataToSend = {
        //         cid: cid,
        //     }
        //     NetworkService.request('company_queryOne', dataToSend, function(data) {

        //         if (data.EC > 0) {

        //         } else {

        //         }
        //     }, function() {

        //     });
        // }

        //API编辑往来单位
        CstmrService.company.edit = function(cstmrInfo, callback) {

            var dataToSend = NetworkService.genApiData(cstmrInfo);
            NetworkService.request('company_edit_', dataToSend, function(data) {
                PageService.showSharedToast('编辑成功！');
                PageService.closeDialog();
                if (callback) {
                    callback()
                }
            }, function() {

            })
        }

        //创建往来单位
        CstmrService.company.create = function(cstmrInfo, callback) {

            // var dataToSend = {};
            // angular.copy(cstmrInfo,dataToSend);
            // ClassService.removeLocalVariable(dataToSend);
            // dataToSend.contact = JSON.stringify(dataToSend.contact);

            var dataToSend = NetworkService.genApiData(cstmrInfo);

            dataToSend.init_payable = isNaN(dataToSend.init_payable) ? 0 : 0 - dataToSend.init_payable;
            dataToSend.status = dataToSend.status.toString();

            NetworkService.request('company_create_', dataToSend, function(data) {
                PageService.showSharedToast('创建成功！');
                PageService.closeDialog();

                if (callback) {
                    callback(data.data);
                }
            }, function() {

            });
        }

        CstmrService.company.get = function(cid, callback) {
            var dataToSend = {
                cid: cid,
            }
            NetworkService.request('company_get_', dataToSend, function(data) {
                var cstmrObj = {};
                angular.copy(data.data, cstmrObj);
                cstmrObj.class_id = "Company";
                for (var v in cstmrObj.contact) {
                    cstmrObj.contact[v].class_id = 'Contact';
                }
                ClassService.installMethod(cstmrObj);
                $log.log('cstmrObj after installMethod', cstmrObj);
                if (callback) {
                    callback(cstmrObj);
                }
            }, function() {

            });
        }

        //删除往来单位
        CstmrService.company.deleteCompany = function(cid, callback) {
            var dataToSend = {
                cid: cid,
            }
            NetworkService.request('compnay_deleteCompany', dataToSend, function(data) {
                $log.log('deleteCompany response: ', data)
                if (data.EC > 0) {
                    $log.log('deleteCompany success');
                    PageService.showSharedToast('删除成功!');
                    if (callback) {
                        callback();
                    }
                    $rootScope.$emit('DELETE_COMPANY_SUCCESS');
                } else {
                    $rootScope.$emit('DELETE_COMPANY_ERROR');
                }
            }, function() {
                $rootScope.$emit('DELETE_COMPANY_ERROR');
            })
        }

        // 获取代收款订单
        CstmrService.company.getReceivableOrderList = function() {
            return receivableOrderList;
        }

        // 获取代付款订单
        CstmrService.company.getPayableOrderList = function() {
            return payableOrderList;
        }

        // 对象转数组
        function converObjectToArray(input) {
            var out = [];
            for (var key in input) {
                if (key == 'totalRemain') {
                    continue
                }
                var tmp = {};
                // $log.log('in converObjectToArray, key=',key,input[key]);
                angular.copy(input[key], tmp);
                out.push(tmp);
            }
            return out;
        }

        // 查询对指定往来单位未收款或未付款订单
        // param int type 1:查询我店还需收款的单据;2:我店还需付款的单据
        // param int cid  往来单位cid
        CstmrService.company.queryRemain = function(type, cid) {
            // 待付款订单详细信息
            var remainOrderList;

            var dataToSend = {
                type: type,
                cid: cid,
            }

            NetworkService.request('company_queryRemain', dataToSend, function(data) {

                remainOrderList = converObjectToArray(data.data);
                MiscService.genShowContent(remainOrderList);

                if (remainOrderList) {
                    for (var i = 0; i < remainOrderList.length; i++) {
                        remainOrderList[i].remain_opposite = -remainOrderList[i].remain;
                    }
                    remainOrderList = MiscService.formatMoneyObject(remainOrderList, ['value', 'off', 'receivable', 'remain', 'remain_opposite']);
                }

                // console.log(remainOrderList);
                EventService.emit(EventService.ev.QUERY_REMAIN_SUCCESS, remainOrderList);
            }, function(data) {
                EventService.emit(EventService.ev.QUERY_REMAIN_ERROR, remainOrderList);
            })

        }

        // WLDW 往来单位-- end --------



        // 联系人----lXR ----start----------------------------------------------------------------------------------------------------------------/

        // 获取联系人信息的引用
        CstmrService.contact.getContactList = function() {
            return contactList;
        }

        // 查询某个往来单位的联系人
        CstmrService.contact.queryList = function(cid, callback) {
            var defered = $q.defer();//promise
            var dataToSend = {
                cid: cid,
            };
            NetworkService.request('contact_queryList', dataToSend, function(data) {
                angular.copy(data.data, contactList);
                EventService.emit(EventService.ev.QUERY_CONTACT_SUCCESS);
                if (callback) {
                    callback(data.data);
                }
                defered.resolve();//promise
            }, function() {
                EventService.emit(EventService.ev.QUERY_CONTACT_ERROR);
                defered.reject();//promise
            })
            return defered.promise;//promise
        }
        // 新建联系人
        CstmrService.contact.create = function(newContact) {
            NetworkService.request('create_contact', newContact, function(data) {
            })
        }

        // 联系人----lXR ----end-------


        // 停车地址----TCDZ ----start----------------------------------------------------------------------------------------------------------------

        // 获取停车地址引用
        CstmrService.parkAddress.getParkAddressList = function() {
            return parkAddressList;
        }

        // 查询停车地址
        CstmrService.parkAddress.queryList = function() {
            var defered = $q.defer();
            var dataToSend = {};

            NetworkService.request('parkAddress_queryList', dataToSend, function(data) {
                angular.copy(data.data, parkAddressList);
                EventService.emit(EventService.ev.PARK_ADDRESS_LOAD_SUCCESS);
                defered.resolve();
            }, function() {
                defered.reject();
            })
            return defered.promise;
        }


        // 新建停车地址
        CstmrService.parkAddress.create = function(parkAddressInfo) {
            var dataToSend = {
                park_address: parkAddressInfo.park_address,
            };

            NetworkService.request('parkAddress_create_', dataToSend, function(data) {
                PageService.showSharedToast('新建成功！');
                PageService.closeDialog();
                EventService.emit(EventService.ev.PARK_ADDRESS_CREATE_SUCCESS);
            }, function() {

            })
        }

        // 编辑停车地址
        CstmrService.parkAddress.edit = function(parkAddressInfo) {
            var dataToSend = {
                parkaddress_id: parkAddressInfo.parkaddress_id,
                park_address: parkAddressInfo.park_address,
            };

            NetworkService.request('parkAddress_edit_', dataToSend, function(data) {
                PageService.showSharedToast('保存成功！');
                PageService.closeDialog();
                EventService.emit(EventService.ev.PARK_ADDRESS_EDIT_SUCCESS);
            }, function() {

            })
        }

        // 编辑停车地址
        CstmrService.parkAddress.delete = function(parkAddressInfo) {
            var dataToSend = {
                parkaddress_id: parkAddressInfo.parkaddress_id,
            };

            NetworkService.request('parkAddress_delete_', dataToSend, function(data) {
                PageService.showSharedToast('删除成功！');
                EventService.emit(EventService.ev.START_MANAGE_PARK_ADDRESS);
            }, function() {

            })
        }

        // 停车地址----TCDZ ----end---------------------
        //初始化

        $rootScope.$on('CREATE_COMPANY_SUCCESS', function() {
            CstmrService.company.queryList(1);
        })

        $rootScope.$on('DELETE_COMPANY_SUCCESS', function() {
            CstmrService.company.queryList(1);
        })

        $rootScope.$on('EDIT_COMPANY_SUCCESS', function() {
            CstmrService.company.queryList(1);
        })

        // 新建停车地址成功自动刷新
        EventService.on(EventService.ev.parkAddress_create_, function(event, arg) {
            if (arg > 0) {
                CstmrService.parkAddress.queryList();
            }
        });

        // 编辑停车地址成功自动刷新
        EventService.on(EventService.ev.parkAddress_edit_, function(event, arg) {
            if (arg > 0) {
                CstmrService.parkAddress.queryList();
            }
        });

        // 删除停车地址成功自动刷新
        EventService.on(EventService.ev.parkAddress_delete_, function(event, arg) {
            if (arg > 0) {
                CstmrService.parkAddress.queryList();
            }
        });


        return CstmrService

    }
]);
