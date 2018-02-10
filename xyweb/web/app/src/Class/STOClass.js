/*** Created by Raytine on 2017/7/7.*/

xy.factory('STOClass', ['EventService', '$log', 'StockService','NetworkService','$q',
    function(EventService, $log, StockService,NetworkService,$q) {
        // var sto_template = {
        //     sto_id: 0,
        //     sto_name:"",
        //     sto_index:2,
        //     sto_remark:"",
        //     status:1,
        // };
        var STOClass = {};
         function STO() {
             var querySto = STOClass.newQuerySto();
             this.defaultSto={}; //默认仓库
             this.stos = [];//仓库数量
             this.setDefaultSto=function (sto) {
                 angular.copy(sto,this.defaultSto)
             };
             this.getDefaultSto=function (sto) {
                 return this.defaultSto
             };
            this.STOCtor = function (spu) {//初始化
                if(spu){
                    for(var i=0;i<spu.data.length;i++){
                        this.stos.push(spu.data[i]);
                    }
                }else{
                    var tem_sto ={};
                    angular.copy(this.defaultSto,tem_sto);
                    this.stos.push(tem_sto);
                }
            };
            this.addSto = function (data) {//添加仓库 默认添加的是默认仓库
                var tem_sto ={};
                angular.copy(this.defaultSto,tem_sto);
                this.stos.push(tem_sto);
            };
            this.deleteSto = function (index) {
              if(index!=0){
                  this.stos.splice(index,1)
              }
            };
            this.change =function (index,sto) {//改变仓库时
                this.stos[index]={};
                angular.copy(sto,this.stos[index])
            }
        }
        STOClass.newST0 = function (sto_id) {
            var out = new STO();
            return out
        };
         //仓库查询
         function QuerySto() {
             var allSto=[],queryType=1,onceSto={};
             this.setQueryType=function (type) {
                 queryType=type;
             };
             this.request=function () {
                return requset();
             };
             this.getAllSto=function () {
                 return allSto;
             };
             this.getSto=function (sto_id) {
                return getSto(sto_id);
             };
             var getSto=function (sto_id) {
                 var dataType={
                     sto_id:sto_id
                 };
                 var defered = $q.defer();
                NetworkService.request("getSto",dataType,function (data) {
                    if(data.EC>0){
                        onceSto={};
                        angular.copy(data.data,onceSto);
                        defered.resolve(onceSto)
                    }
                });
                 return defered.promise;
             };
             var requset = function () {
                 var defered = $q.defer(),
                      dataType={
                      type:queryType
                     };
                  NetworkService.request("querySto",dataType,function (data) {
                      if(data.EC>0){
                          angular.copy(data.data,allSto)
                      }
                      defered.resolve(data);
                  },function () {

                  });

                 return defered.promise;

             }
         }
        STOClass.newQuerySto=function () {
            var out = new QuerySto();
            return out ;
        };
        //仓库操作
         function OperateSt0() {
             var createSto = function (data) {
                 var defered = $q.defer(),dataType={};
                 angular.copy(data,dataType);
                 NetworkService.request("updateSto",dataType,function (data) {
                     if(data.EC>0){
                         defered.resolve(data);
                     }
                 },function () {});
                 return defered.promise;
             };
             this.createSto = function (data) {
                 return createSto(data);
             };
             this.deleteSto = function () {

             }
         }
         STOClass.newOperateSto =function () {
            var out = new OperateSt0();
            return out
         };
        return STOClass;
    }]);
