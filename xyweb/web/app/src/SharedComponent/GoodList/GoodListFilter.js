// 选货的过滤器

'use strict'
xy.filter('goodList', ['$log',
    function($log) {

        // Create the return function and set the required parameter name to **input**
        return function(input, search, cat_id) {

            var out = [];

            // Using the angular.forEach method, go through the array of data and perform the operation of figuring out if the language is statically or dynamically typed.
            angular.forEach(input, function(item) {

                $log.debug('filterInput: ', input)


                var flag = true


                //先检查类别
                if (item.cat_id != cat_id) {
                    flag = false
                }

                //再检查名字
                if (item.spu_name.search(search) < 0) {
                    flag = false
                }

                if (flag) {
                    out.push(item)
                }

            })

            return out;
        }

    }
]);
