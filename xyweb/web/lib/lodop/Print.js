var Print = {

    newInstance: function () {

        var print = {};

        var LODOP;
        var mOid; // 请求订单所传的参数
        var mDomain; // 服务器地址
        var mOrder; // 订单
        var mShopName; // 商店名称
        var mType = 0; // 1是打印， 2是预览
        var mTop = 0; // 距顶端距离自增

        /**
         * 打印
         * @param domain
         * @param oid
         */
        print.print = function (domain, oid) {
            mDomain = domain;
            mOid = oid;
            mType = 1;
            requestOrder();
        };

        /**
         * 预览
         * @param domain
         * @param oid
         */
        print.preview = function (domain, oid) {
            mDomain = domain;
            mOid = oid;
            mType = 2;
            requestOrder();
        }

        /**
         * 向服务器发送oid并请求订单json
         */
        function requestOrder() {
            var param = {'oid': mOid};

            $.ajax({
                type: 'POST',
                url: mDomain + 'index.php?m=Home&c=Order&a=get_',
                data: param,
                dataType: 'json',
                success: function (data) {
                    if (data.EC > 0) {
                        mOrder = data.data;
                        requestShopName();
                    }
                }
            });
        }
        /**
         * 向服务器请求商店名称
         */
        function requestShopName() {
            $.ajax({
                type: 'POST',
                url: mDomain + 'index.php?m=Home&c=User&a=getUserInfo_shopName',
                dataType: 'json',
                success: function (data) {
                    if (data.EC > 0) {
                        mShopName = data.data;
                        CreatePrintPage();
                    }
                }
            });
        }
        /**
         * 建立打印页
         * @constructor
         */
        function CreatePrintPage() {
            LODOP = getLodop();
            LODOP.PRINT_INIT(""); // 初始化运行环境，清理异常打印遗留的系统资源
            LODOP.SET_PRINTER_INDEX("GP-58MBIII"); // 按名称或序号指定要进行打印输出的设备，指定后禁止重新选择
            LODOP.SET_PRINT_PAGESIZE(3, "57mm", "5mm", ""); // 设定打印纸张为固定纸张或自适应内容高，并设定相关大小值或纸张名及打印方向
            LODOP.SET_PRINT_STYLE("Alignment", 2); // 居中
            LODOP.SET_PRINT_STYLE("Bold", 1); // 粗体
            addSingle(mShopName); // 标题--商店名称

            LODOP.SET_PRINT_STYLE("Alignment", 1); // 左靠齐
            LODOP.SET_PRINT_STYLE("Bold", 0); // 非粗体
            addPair("客户名：", mOrder.cid_name);
            addPair("联系人：", mOrder.contact_name);
            addPair("电话：", mOrder.mobile);
            addPair("车牌号：", mOrder.car_license);
            addPair("停车位置：", mOrder.park_address);
            addLine();
            addSingle("名称        规格/       数量/");
            addSingle("            单价        总价");
            addCart();

            if (mType === 1) {
                LODOP.PRINT();
            } else if (mType === 2) {
                LODOP.PREVIEW();
            }
        };

        /**
         * 判断{@param c}是否属于ascii码表中的字符
         * @param c
         * @returns {boolean}
         */
        function isLetter(c) {
            var a = 128;
            return c / a <= 1 ? true : false;
        };
        /**
         * 得到一个字符串的长度,显示的长度,一个汉字或日韩文长度为2,英文字符长度为1
         * @param s
         * @returns {number}
         */
        function getLength(s) {
            if (s == null) {
                return 0;
            }
            var len = 0;
            for (var i = 0; i < s.length; i++) {
                len++;
                if (!isLetter(s.charCodeAt(i))) {
                    len++;
                }
            }
            return len;
        };

        /**
         * 根据传入的长度生成指定长度的空格
         * @param length
         * @returns {*}
         */
        function getSpace(length) {
            var space = "";
            for (var i = 0; i < length; i++) {
                space += " ";
            }
            return space;
        };

        /**
         * 增加一条水平虚线
         */
        function addLine() {
            LODOP.ADD_PRINT_LINE(mTop, 0, mTop, "57mm", 2);
            mTop += 3;
        }

        /**
         * 一行内增加一条文本
         * @param str
         */
        function addSingle(str) {
            LODOP.ADD_PRINT_TEXT(mTop, 0, "57mm", 15, str);
            mTop += 15;
        }

        /**
         * 一行内增加一对文本
         * @param key
         * @param value
         */
        function addPair(key, value) {
            var keyLength = getLength(key);
            var valueLength = getLength(value);
            var wholeLength = getLength(key + value);
            var keySpace = getSpace(keyLength);
            var usableLength = 30 - keyLength;

            if (wholeLength > 30) {

                var valueArray = splitToArray(value, usableLength);

                for (var i = 0; i < valueArray.length; i++) {
                    if (i === 0) {
                        addSingle(key + valueArray[i]);
                    } else {
                        addSingle(keySpace + valueArray[i]);
                    }
                }
            } else {
                addSingle(key + value);
            }
        }

        /**
         * 增加订单表格
         * @param cart
         */
        function addCart() {
            var cart = mOrder.cart;

            var leftSpace = getSpace(12); // 左侧项空白时的空格
            var midSpace = getSpace(12); // 中间项空白时的空格
            var rightSpace = getSpace(6); // 右侧项空白时的空格
            var fillSpace = "";

            for (var i = 0; i < cart.length; i++) {
                var spuArray = splitToArray(cart[i].spu_name, 10); // 名称
                var specArray = splitToArray(cart[i].spec_name + "/", 10); // 规格
                var quantityArray = splitToArray(cart[i].quantity + "/", 6); // 数量
                var unitPriceArray = splitToArray(cart[i].unitPrice + "元", 10); // 单价
                var pilePriceArray = splitToArray(cart[i].pilePrice + "元", 6); // 总价

                var spuRow = spuArray.length;
                var specRow = specArray.length;
                var quantityRow = quantityArray.length;
                var unitPriceRow = unitPriceArray.length;
                var pilePriceRow = pilePriceArray.length;

                addLine();

                var upperRow = Math.max(spuRow, specRow, quantityRow);
                var lowerRow = Math.max(unitPriceRow, pilePriceRow);
                var totalRow = upperRow + lowerRow;
                for (var j = 0; j < totalRow; j++) {
                    var temp = "";

                    if (j + 1 > spuRow) {
                        temp += leftSpace;
                    } else {
                        fillSpace = getSpace(12 - getLength(spuArray[j]));
                        temp += spuArray[j] + fillSpace;
                    }

                    if (j + 1 <= upperRow) {
                        if (j + 1 > specRow) {
                            temp += midSpace;
                        } else {
                            fillSpace = getSpace(12 - getLength(specArray[j]));
                            temp += specArray[j] + fillSpace;
                        }

                        if (j + 1 > quantityRow) {
                            temp += rightSpace;
                        } else {
                            fillSpace = getSpace(6 - getLength(quantityArray[j]));
                            temp += quantityArray[j] + fillSpace;
                        }
                    } else {
                        if (j + 1 - upperRow > unitPriceRow) {
                            temp += midSpace;
                        } else {
                            fillSpace = getSpace(12 - getLength(unitPriceArray[j - upperRow]));
                            temp += unitPriceArray[j - upperRow] + fillSpace;
                        }

                        if (j + 1 - upperRow > pilePriceRow) {
                            temp += rightSpace;
                        } else {
                            fillSpace = getSpace(6 - getLength(pilePriceArray[j - upperRow]));
                            temp += pilePriceArray[j - upperRow] + fillSpace;
                        }
                    }

                    addSingle(temp);
                }
            }

        }

        /**
         * 将给定的字符串按给定长度（打印长度）分割成数组
         * @param str
         */
        function splitToArray(str, length) {
            console.log(str);
            var strArray = [];
            var temp = "";

            for (var i = 0; i < str.length; i++) {
                temp += str.substring(i, i + 1);
                if (getLength(temp) > length) {
                    temp = temp.substring(0, temp.length - 1);
                    strArray.push(temp);
                    temp = str.substring(i, i + 1);
                } else if (getLength(temp) === length) {
                    strArray.push(temp);
                    temp = "";
                }
            }

            if (temp.length > 0) {
                strArray.push(temp);
                temp = "";
            }

            return strArray;
        }

        return print;

    }

}