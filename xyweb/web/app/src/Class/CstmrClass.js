// 往来单位类
//xxxxModel or Service or Class
'use strict';

// or xy.factory('xxxxModel') or xy.factory('xxxxClass')
angular.module('XY').factory('CstmrClass', ['EventService', '$log', 'PageService', 'ClassService',
    function(EventService, $log, PageService, ClassService) {

        var CstmrClass = {};

        // 联系人
        function Contact() {
            this.class_id = 'Contact';
            this.ContactCtor = function() {

                this.contact_id = this.contact_id || 0;
                // 新建标记
                this.new = (this.contact_id > 0 ) ? 0 : 1;
                // 删除标记
                this.delete = this.delete || 0;
                // 名字
                this.contact_name = this.contact_name || "";

                this.__mobileTmpl    =  { mobile: "", phonenum_id: 0, delete: 0,new:0 };
                this.__carLicenseTmpl = { car_license: "", carlicense_id: 0, delete: 0,new:0};

                // 联系人的电话号码
                this.phonenum = this.phonenum || [];
                this.car_license = this.car_license || [];

                // 统计电话号码和车牌号数量
                this.__phonenumCount = 0;
                this.__carLicenseCount = 0;
                this.updateInfo();

                if (this.__phonenumCount == 0) {
                    this.addMobile();
                }
                if (this.__carLicenseCount == 0) {
                    this.addCarLicense();
                }
            }

            // 增加一个电话
            this.addMobile = function() {
                if (this.__phonenumCount < 6) {
                    var tmp = {};
                    angular.copy(this.__mobileTmpl, tmp);
                    tmp.new = 1;
                    this.phonenum.push(tmp);
                }
                this.updateInfo();
            }

            // 删除电话
            this.deleteMobile = function(index) {
                if (this.__phonenumCount > 1) {
                    if (this.phonenum[index].phonenum_id > 0) {
                        this.phonenum[index].delete = 1;
                    } else {
                        this.phonenum.splice(index, 1);
                    }
                    this.updateInfo();
                }
            }

            // 增加车牌号
            this.addCarLicense = function() {
                    if (this.__carLicenseCount < 6) {
                        var tmp = {};
                        angular.copy(this.__carLicenseTmpl, tmp);
                        tmp.new = 1;
                        this.car_license.push(tmp);
                    }
                    this.updateInfo();
                }
                // 删除车牌号

            this.deleteCarLicense = function(index) {
                if (this.__carLicenseCount > 1) {
                    if (this.car_license[index].carlicense_id > 0) {
                        this.car_license[index].delete = 1;
                    } else {
                        this.car_license.splice(index, 1);
                    }
                    this.updateInfo();
                }
            }

            // 更新信息(目前是统计电话号码和车牌号数量)
            this.updateInfo = function() {
                var count = 0;
                for (var v in this.phonenum) {
                    // 带删除标记的不算
                    if (this.phonenum[v].delete != 1) {
                        count++;
                    }
                }
                this.__phonenumCount = count;
                count = 0;
                for (var v in this.car_license) {
                    if (this.car_license[v].delete != 1) {
                        count++;
                    }
                }
                this.__carLicenseCount = count;
            }
        }
        ClassService.registerClass(new Contact());

        // var t = {class_id:'Contact',a:[],}
        // t.a.class_id = "Contact";
        // ClassService.installMethod(t);

        // $log.log(t);

        CstmrClass.newContact = function() {
            var out = { class_id: 'Contact' };
            ClassService.installMethod(out);
            out.ContactCtor();
            return out;
        }


        // 往来单位
        function Company() {
            this.class_id = 'Company';
            this.CompanyCtor = function() {
                this.cid = this.cid || 0;
                this.name = this.name || "";
                this.qcode = this.qcode || "";
                this.address = this.address || "";
                this.remark = this.remark || "";
                this.init_payable = this.init_payable || "";
                this.status = this.status || "1";

                // 往来单位的各联系人
                this.contact = this.contact || [];
                this.__contactCount = 0;
                this.updateInfo();

                // 如果没有联系人，加入一个空的联系人
                if (this.__contactCount == 0) {
                    this.addContact();
                }
            }

            this.addContact = function() {
                if (this.__contactCount < 6) {
                    var tmpContact = CstmrClass.newContact();
                    this.contact.push(tmpContact);
                    this.updateInfo();
                }
            }

            // 删除联系人
            this.deleteContact = function(index) {
                if (this.__contactCount > 1) {
                    // 如果contact_id>0说明是服务器获取的,标记删除
                    if (this.contact[index].contact_id > 0) {
                        this.contact[index].delete = 1;
                        // 否则说明是本地新建的，直接删除
                    } else {
                        this.contact.splice(index, 1);
                    }
                    this.updateInfo();
                }
            }

            this.updateInfo = function() {
                var count = 0;
                for (var v in this.contact) {
                    if (this.contact[v].delete != 1) {
                        count++;
                    }
                }
                this.__contactCount = count;
            }

        }
        ClassService.registerClass(new Company());

        // $log.log(ClassService.getClassIdToProto());

        // 新建往来单位对象
        CstmrClass.newCompany = function() {
            var out = { class_id: "Company" };
            ClassService.installMethod(out);
            out.CompanyCtor();
            return out;
        }

        return CstmrClass; // or return model

    }
]);


// 'use strict';



// xy.factory('CstmrClass', ['EventService', '$log',
//     function(EventService, $log) {

//         var CstmrClass = {};



//         function CstmrClass() {
//             this.Ctor = functiion(cstmr) {
// if (cstmr) {
//     this.name = "";
//     this.qcode = "";
//     this.address = "";
//     this.remark = "";
//     this.init_payable = 0;
//     this.status = 1;
//     this.contact = [];
//     this.addContact();

// }
//     }
// }

// CstmrClass.newCstmr = function(cstmr) {
//     var out = new CstmrClass();
//     out.Ctor(cstmr);
//     return out;
// }

// function init() {

// }

// init();

//         return CstmrClass;

//     }
// ]);

// <!--  * 在2模式下（下面字段需全部传输，一旦某个字段不存在则其值在数据库中会被修改为''）：
//  * @param unsigned_int $cid(创建的时候没有这一项)
//  * @param string $name 单位名称
//  * @param string $qcode 速查码
//  * @param string $address 地址
//  * @param string $remark 备注
//  * @param double $init_payable 期初应付款（编辑的时候没有这一项）
//  * @param 0||1 $status 是否启用
//  * @param json contact 联系人最终状态的全部数据，格式如下：
//  */
//  -->
//         {
//             "contact_id": "1", 
//             "name": "外婆家", 
//             "phonenum": [
//                 {
//                     "phonenum_id": "1", 
//                     "mobile": "15023658955"
//                 }, 
//                 {
//                     "phonenum_id": "9", 
//                     "mobile": "189663"
//                 }
//             ], 
//             "car_license": [
//                 {
//                     "carlicense_id": "1", 
//                     "car_license": "浙AA5202"
//                 }, 
//                 {
//                     "carlicense_id": "8", 
//                     "car_license": "car_license"
//                 }, 
//                 {
//                     "carlicense_id": "9", 
//                     "car_license": "car_license45645"
//                 }
//             ]
//         }, 

// var contactTemplate = {
//     name: "",
//     phonenum: [],
// }
