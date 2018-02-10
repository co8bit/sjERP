'use strict';

xy.factory('LockService', ['$log', 'EventService', 'PageService', 'NetworkService','UserService',
    function ($log, EventService, PageService, NetworkService, UserService) {

    var LockService = {};

    /**
     * 锁定商店
     * @param callback 锁定成功的回调
     */
    LockService.lockShop = function (callback) {
        NetworkService.request('config_lockShop', '', function (data) {
            LockService.token = data.data;
            if (callback) {
                callback();
            }
            PageService.showConfirmDialog('已经锁定库存数据，其他用户在锁定期间不能创建交易类单据、盘点单，不能新建、编辑、删除商品，不能新建、编辑、删除商品类别，不能修改成员信息！');
        });
    }

    /**
     * 解锁商店
     */
    LockService.unlockShop = function () {
        NetworkService.request('config_unlockShop', '', function () {
            PageService.showSharedToast('商店解锁成功！');
        });
    }

    /**
     * 获取商店锁定状态
     * @param pageState
     * @param callback
     */
    LockService.getLockShopStatus = function (pageState, callback) {
        NetworkService.request('config_getLockShopStatus', '', function (data) {
            if (data.data.sn == '') {
                if (callback) {
                    callback();
                }
            } else {
                var userInfo = UserService.findUserById(UserService.getLoginStatus().id);
                $log.log("userInfo:", userInfo);
                console.log(data.data);
                if (userInfo.uid == data.data.tokenUid) {
                    PageService.showConfirmDialog('店铺已被您锁定，请先解锁后再执行其他操作！', ['解锁'], function () {
                        LockService.unlockShop();
                        if (callback) {
                            callback();
                        }
                    });
                } else {
                    var name = data.data.name == undefined ? '' : data.data.name;
                    switch (pageState) {
                        case 0: // 其他
                            PageService.showConfirmDialog(name + '(编号：' + data.data.sn + ')' + '正在开盘点单，已锁定店铺货物，目前不能执行该操作。');
                            break;
                        case 1: // 交易类单据和盘点单最后开单时弹出的提示
                            PageService.showConfirmDialog(name + '(编号：' + data.data.sn + ')' + '正在开盘点单，已锁定店铺货物，目前不能执行该操作。如需执行该操作请让他解锁或您先将本单据保存为草稿单据，稍后再继续开单。');
                            break;
                        case 2: // 刚登录弹出的提示
                            PageService.showConfirmDialog(name + '(编号：' + data.data.sn + ')' + '正在开盘点单，已锁定店铺货物，其他用户在锁定期间不能创建交易类单据、盘点单，不能新建、编辑、删除商品，不能新建、编辑、删除商品类别，不能修改成员信息。');
                            break;
                    }
                }
            }
        });
    }

    EventService.on(EventService.ev.LOCK_SHOP, function (event, arg) {
        LockService.lockShop();
    });

    return LockService;
}]);
