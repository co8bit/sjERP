'use strict';

xy.controller('TempController', ['$scope', 'NetworkService', '$log', '$timeout', 'PageService', 'EventService', 'UserService',
    function($scope, NetworkService, $log, $timeout, PageService, EventService, UserService) {

        // $scope.PageService = PageService;

        // $log.debug('TempController init')


        // $scope.queryUser = function(type) {
        //     UserService.queryUser(type)
        // }

        // //修改公司名
        // $scope.changeCompanyNameTo = function(name) {
        //     UserService.editShopName(name)
        // }


        // $scope.queryUserType = 1
        //     //获取公司信息引用
        // $scope.companyInfo = UserService.getCompanyInfo()

        // $log.debug('companyInfo', UserService.getCompanyInfo())


        // $('#table_id').DataTable();


        var data = [];
        var tmp = {
            "name": "Tiger Nixon",
            "position": "System Architect",
            "salary": "$3,120",
            "start_date": "2011/04/25",
            "office": "Edinburgh",
            "extn": "5421"
        };
        for (var i = 0; i<100 ;i++) {
            data.push(tmp);
        }

        // var data = [{
        //     "name": "Tiger Nixon",
        //     "position": "System Architect",
        //     "salary": "$3,120",
        //     "start_date": "2011/04/25",
        //     "office": "Edinburgh",
        //     "extn": "5421"
        // }, {
        //     "name": "Garrett Winters",
        //     "position": "Director",
        //     "salary": "$5,300",
        //     "start_date": "2011/07/25",
        //     "office": "Edinburgh",
        //     "extn": "8422"
        // }, {
        //     "name": "Garrett Winters",
        //     "position": "Director",
        //     "salary": "$5,300",
        //     "start_date": "2011/07/25",
        //     "office": "Edinburgh",
        //     "extn": "8422"
        // }, {
        //     "name": "Garrett Winters",
        //     "position": "Director",
        //     "salary": "$5,300",
        //     "start_date": "2011/07/25",
        //     "office": "Edinburgh",
        //     "extn": "8422"
        // }, {
        //     "name": "Garrett Winters",
        //     "position": "Director",
        //     "salary": "$5,300",
        //     "start_date": "2011/07/25",
        //     "office": "Edinburgh",
        //     "extn": "8422"
        // }, {
        //     "name": "Garrett Winters",
        //     "position": "Director",
        //     "salary": "$5,300",
        //     "start_date": "2011/07/25",
        //     "office": "Edinburgh",
        //     "extn": "8422"
        // }, {
        //     "name": "Garrett Winters",
        //     "position": "Director",
        //     "salary": "$5,300",
        //     "start_date": "2011/07/25",
        //     "office": "Edinburgh",
        //     "extn": "8422"
        // }, {
        //     "name": "Garrett Winters",
        //     "position": "Director",
        //     "salary": "$5,300",
        //     "start_date": "2011/07/25",
        //     "office": "Edinburgh",
        //     "extn": "8422"
        // }];

        $('#example').DataTable({
            data: data,
            columns: [
                { data: "name" },
                { data: "position" },
            ],
            scrollY: 400,
            paging:true,
        });

    }
]);
