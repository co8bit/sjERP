-- 注意：写的时候因为要确保自动导入成功，每一行必须以分号结尾，如：
-- INSERT INTO `user` (`uid`, `name`) VALUES (NULL, '杭州跃迁科技有限公司')/*密码是123456*/;
-- 而不是：INSERT INTO `user` (`uid`, `name`) VALUES (NULL, '杭州跃迁科技有限公司');/*密码是123456*/
;


-- NOTE:
-- 正式部署的时候建议删掉下面这句话：（因为意义不明）
SET NAMES utf8;
-- 
;



CREATE USER 'databasename'@'localhost' IDENTIFIED BY 'databasepwd';
GRANT USAGE ON *.* TO 'databasename'@'localhost' IDENTIFIED BY 'databasepwd' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;
-- CREATE DATABASE IF NOT EXISTS `databasename`;
CREATE DATABASE IF NOT EXISTS `databasename` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
GRANT ALL PRIVILEGES ON `databasename`.* TO 'databasename'@'localhost';

use databasename;

/*用户表*/
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',/*TODO:是否不够？*/
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',

  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',/*是创建者的话该项为创建者本身uid*/
  
  /**
   * 1.默认为创建者手机号。
   * 2.为创建者的时候是公司用户名，不是的时候是公司用户名+用户名
   * 3.公司用户名禁止人工注册为11位纯数字
   * 4.创建者修改公司用户名的时候记得要把所有下属的shop_name字段改掉
   */`shop_name` char(45) not null DEFAULT '' COMMENT '公司用户名',
  `admin_mobile` char(30) NOT NULL DEFAULT '' COMMENT '创建者手机',/*为创建者的时候是创建者手机（与mobile同步），不是的时候是创建者手机+用户名*/
  `username` char(15) NOT NULL DEFAULT '' COMMENT '用户名',/*为创建者时跟admin_mobile、mobile同步,不是创建者时是自己的用户名*/
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',/*为创建者时跟admin_mobile、username同步,不是创建者时是自己的手机号*/

  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `email` char(200) NOT NULL DEFAULT '' COMMENT '用户邮箱',

  `name` char(32) NOT NULL DEFAULT '未填用户名' COMMENT '姓名',
  `rpg` tinyint(4) not null DEFAULT 0 COMMENT '用户角色',
  `qq` char(32) NOT NULL DEFAULT '' COMMENT 'QQ',
  `invitated_code` char(32) NOT NULL DEFAULT '' COMMENT '被谁邀请',
  `invitation_code` char(32) NOT NULL DEFAULT '' COMMENT '邀请码',
  `depart_id` int(20) unsigned NOT NULL DEFAULT 0 COMMENT '所在部门ID',


  -- 企业信息
  `industry` char(100) NOT NULL DEFAULT '' COMMENT '行业',
  `province` char(32) NOT NULL DEFAULT '请选择' COMMENT '省',
  `city` char(32) NOT NULL DEFAULT '请选择' COMMENT '市',

  `TODAY_HISTORY_CONFIG` varchar(500) NOT NULL DEFAULT '' COMMENT '今日经营历史的过滤器配置',/*TODO:够么？*/

  `GTClientID` char(32) NOT NULL DEFAULT '' COMMENT '个推的ClientID',
  `session_id` char(32) NOT NULL DEFAULT '' COMMENT '最后一个登陆的session_id',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` varchar(100) NOT NULL DEFAULT '' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` varchar(100) NOT NULL DEFAULT '' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) not null DEFAULT 0 COMMENT '用户状态',
  `login_count` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '用户登录次数计数',
  `option_array` text  NOT NULL DEFAULT '' COMMENT '用户选项数组',
  `outer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'faceset标识',

  PRIMARY KEY (`uid`),
  UNIQUE KEY `admin_mobile` (`admin_mobile`),
  UNIQUE KEY `shop_name` (`shop_name`),
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户表';



/*配置表，一行一个企业的配置*/
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `config_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `MAX_LIMIT_EMPLOYEE` int(10) unsigned not null DEFAULT 2 COMMENT '员工数量',

  `lock_shop_token` char(150) NOT NULL DEFAULT '' COMMENT '锁店时的token',

  /*流程模式*/
  `order_flow_mode` boolean not null DEFAULT 0 COMMENT '单据流模式，true-开启，false-不开启',
  `audit_flow_mode` boolean not null DEFAULT 0 COMMENT '审核模式，true-开启，false-不开启',
  `DELETE_ORDER_ALLOW_STATUS` tinyint(4) not null DEFAULT 2 COMMENT '删除订单所允许的操作状态模式',/*1-状态8/10可以删除订单，2-状态8/10不能删除订单*/

  -- 对于下面的跳过选项:true跳过，false不跳
  `ck_jump_warehouseConfirm` boolean not null DEFAULT 0 COMMENT '出库类单据是否跳过库管确认收到动作',
  `ck_jump_warehouOut` boolean not null DEFAULT 0 COMMENT '出库类单据是否跳过出库动作',
  `ck_jump_deliver` boolean not null DEFAULT 0 COMMENT '出库类单据是否跳过送达动作',
  `rk_jump_warehouseConfirm` boolean not null DEFAULT 0 COMMENT '入库类单据是否跳过库管确认收到动作',
  `rk_jump_deliver` boolean not null DEFAULT 0 COMMENT '入库类单据是否跳过入库动作',
  `is_show_foreground_printer_button` boolean not null DEFAULT 0 COMMENT '是否显示前台打印按钮',
  `finance_mode` boolean not NULL DEFAULT 1 COMMENT '财务与老板是否为同一人',
  -- sn
  `USN` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'user_sn',
  `SKU` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'sku_sn',
  `PAD` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '停车位置',
  `CSN` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '往来单位',
  `OXC` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '销售单',
  `OXT` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '销售退货单',
  `OCR` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '采购单',
  `OCT` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '采购退货单',
  `FSK` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '收款单',
  `FFK` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '付款单',
  `FQS` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '其他收入',
  `FFY` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '费用单',
  `WPD` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '盘点单',
/*  `WPR` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '平价调拨单',
  `WPA` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '变价调拨单',*/
  `WRN` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '调拨单',
  `DRA` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '草稿单',
  `AAR` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '应收款调整',
  `AAP` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '应付款调整',
  `STO` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '仓库编号',
  `AOC` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '账户操作码', 
  `FIS` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '财政收入单',
  `FES` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '财政支出单',
  `FCO` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '财政提现单',
  `FTF` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '财政转账单',
  `ASN` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'account_sn',
  `PIO` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '填补单',


  PRIMARY KEY (`config_id`),
  UNIQUE KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='配置表';



-- ================================商品相关Start=======================================================
DROP TABLE IF EXISTS `sku`;
DROP TABLE IF EXISTS `spu`;
DROP TABLE IF EXISTS `cat`;
DROP TABLE IF EXISTS `storage`;

    /*商品分类表*/;
    CREATE TABLE `cat` (
      `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'cat_id',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      
      `cat_name` char(20) NOT NULL DEFAULT '未分类' COMMENT '所属类别名称',
      `cat_class` int(3) NOT NULL DEFAULT '0' COMMENT '类别的来源（财务类别或是商品类别）',
      `cat_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT 'cat显示顺序，一个正整数，数字越小，显示越靠前，1是最前',
      `status` tinyint(4) not null DEFAULT 1 COMMENT '状态',/*1是开启，0为未启用*/

      `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

      PRIMARY KEY (`cat_id`),
      UNIQUE INDEX `adminUid_catName` (`admin_uid`, `cat_name`) USING HASH,
      KEY `admin_uid` (`admin_uid`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品分类表';


    /*商品spu表*/;
    CREATE TABLE `spu` (
      `spu_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'spu_id',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      `cat_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属类别id',/*0为未分类*/
      
      `spu_name` char(50) NOT NULL DEFAULT '' COMMENT '商品名称(spu)',
      `spu_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT '商品spu显示顺序，一个正整数，数字越小，显示越靠前，1是最前',
      `spu_class` int(3) NOT NULL DEFAULT '0' COMMENT '类别的来源（财务类别或是商品类别）',
      `qcode` char(50) NOT NULL DEFAULT '' COMMENT '速查编码',/*精确到spu*/
      `status` tinyint(4) not null DEFAULT 1 COMMENT '状态，未使用',/*1是开启，0为未启用*/

      `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

      PRIMARY KEY (`spu_id`),
      UNIQUE INDEX `adminUid_spuName` (`admin_uid`, `spu_name`) USING HASH,
      KEY `admin_uid` (`admin_uid`),
      KEY `status` (`status`),
      constraint FK_SPU_cat_id foreign key (cat_id) references cat(cat_id) on delete cascade on update cascade
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品spu表';

    /*storage 仓库表*/
  CREATE TABLE `storage` (
    `sto_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
    `sn`char(20) NOT NULL DEFAULT '' COMMENT '仓库编号',
    `sto_name` char(80)  NOT NULL DEFAULT '' COMMENT '仓库名称',
    `admin_uid` int(10) unsigned  NOT NULL DEFAULT 0 COMMENT '所属店铺uid',
    `sto_index` tinyint(5) unsigned NOT NULL DEFAULT 2 COMMENT 'storage显示顺序,一个正整数,数字越小,显示越靠前',
    `status` tinyint(4)  NOT NULL DEFAULT 1 COMMENT '状态',/*1是开启，0为未启用*/
    `remark` text NOT NULL DEFAULT '' COMMENT '备注',
    `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    PRIMARY KEY (`sto_id`),
    UNIQUE INDEX `adminUid_sn` (`admin_uid`, `sn`) USING HASH,
    KEY `admin_uid` (`admin_uid`),
    KEY `status` (`status`)
  ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='仓库表';



/*商品sku表*/
CREATE TABLE `sku` (
  `sku_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'sku_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',
  
  -- spu表冗余信息
  `spu_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '商品(SPU)名称id',
  `spu_name` char(50) NOT NULL DEFAULT '' COMMENT '商品名称(spu)',
  `spu_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT '商品spu显示顺序，一个正整数，数字越小，显示越靠前，1是最前',
  `qcode` char(50) NOT NULL DEFAULT '' COMMENT '速查编码',/*精确到spu*/
  `spu_status` tinyint(4) not null DEFAULT '1' COMMENT '状态',/*1是开启，0为未启用*/

  -- cat表冗余信息
  `cat_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属类别id',/*0为未分类*/
  `cat_name` char(20) NOT NULL DEFAULT '未分类' COMMENT '所属类别名称',
  `cat_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT 'cat显示顺序',
  `cat_status` tinyint(4) not null DEFAULT '1' COMMENT '状态',/*1是开启，0为未启用*/

  -- sku自带信息
  `spec_name` char(50) NOT NULL DEFAULT '默认' COMMENT '规格名称',  
  `total_stock` double NOT NULL DEFAULT 0 COMMENT '总库存数量',
  -- `unit_price` double NOT NULL DEFAULT 0 COMMENT '库存产品的成本（单价）',
  `last_selling_price` double NOT NULL DEFAULT 0 COMMENT '该sku的最后卖出价',
  `sku_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT '商品sku显示顺序,一个正整数，数字越小，显示越靠前，1是最前',
  `sku_class` int(3) NOT NULL DEFAULT '0' COMMENT '类别的来源（商品类别或是财务类别）',
  `status` tinyint(4) not null DEFAULT 1 COMMENT '状态',/*1是开启，0为未启用*/

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',


  PRIMARY KEY (`sku_id`),
  UNIQUE INDEX `adminUid_sn` (`admin_uid`, `sn`) USING HASH,
  UNIQUE INDEX `adminUid_spuId_specName` (`admin_uid`, `spu_id`,`spec_name`) USING HASH,
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`),
  constraint FK_SKU_spu_id foreign key (spu_id) references spu(spu_id) on delete cascade on update cascade,
  constraint FK_SKU_cat_id foreign key (cat_id) references cat(cat_id) on delete cascade on update cascade
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品sku表';

-- ================================商品相关Over=======================================================


-- ================================往来单位相关Start=======================================================
/*往来单位表*/
DROP TABLE IF EXISTS `carlicense`;
DROP TABLE IF EXISTS `phonenum`;
DROP TABLE IF EXISTS `contact`;
DROP TABLE IF EXISTS `company`;

CREATE TABLE `company` (
  `cid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',
  
  `name` char(45) not null DEFAULT '' COMMENT '单位名称',
  `qcode` char(50) NOT NULL DEFAULT '' COMMENT '速查编码',
  `address` char(60) NOT NULL DEFAULT '' COMMENT '地址',

  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/

  `balance` double NOT NULL DEFAULT 0 COMMENT '结余（应付-应收），负数是别人欠店主，正数是店主欠别人',
  `status` tinyint(4) not null DEFAULT 1 COMMENT '状态，是否启用',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `lock_version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐观锁',/*TODO:对么！？*/
  `image_url` varchar(600) NOT NULL DEFAULT ' ' COMMENT '照片URL',
  `visit_times` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '进店次数',
  `purchase_times` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '购买次数',
  PRIMARY KEY (`cid`),
  UNIQUE INDEX `adminUid_companyName` (`admin_uid`, `name`) USING HASH,
  UNIQUE INDEX `adminUid_sn` (`admin_uid`, `sn`) USING HASH,
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='往来单位表';

    /*联系人表*/
    CREATE TABLE `contact` (
      `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '联系人ID',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的cid',

      `contact_name` char(45) not null DEFAULT '' COMMENT '联系人名称',

      `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
      `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

      PRIMARY KEY (`contact_id`),
      constraint FK_CONTACT_cid foreign key (cid) references company(cid) on delete cascade on update cascade,
      KEY `admin_uid` (`admin_uid`),
      KEY `cid` (`cid`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='联系人表';

    /*电话表*/
    CREATE TABLE `phonenum` (
      `phonenum_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '联系人ID',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的cid',
      `contact_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的contact_id',

      `mobile` char(25) NOT NULL DEFAULT '' COMMENT '电话',

      `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
      `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

      PRIMARY KEY (`phonenum_id`),
      constraint FK_PHONENUM_cid foreign key (cid) references company(cid) on delete cascade on update cascade,
      constraint FK_PHONENUM_contact_id foreign key (contact_id) references contact(contact_id) on delete cascade on update cascade,
      KEY `admin_uid` (`admin_uid`),
      KEY `cid` (`cid`),
      KEY `contact_id` (`contact_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='电话表';


    /*车牌表*/
    CREATE TABLE `carlicense` (
      `carlicense_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '车牌号ID',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的cid',
      `contact_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的contact_id',

      `car_license` char(30) NOT NULL DEFAULT '' COMMENT '车牌号',

      `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
      `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

      PRIMARY KEY (`carlicense_id`),
      constraint FK_CARLICENSE_cid foreign key (cid) references company(cid) on delete cascade on update cascade,
      constraint FK_CARLICENSE_contact_id foreign key (contact_id) references contact(contact_id) on delete cascade on update cascade,
      KEY `admin_uid` (`admin_uid`),
      KEY `cid` (`cid`),
      KEY `contact_id` (`contact_id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='车牌表';

-- ================================往来单位相关End=======================================================

/*停车位置表*/
DROP TABLE IF EXISTS `parkaddress`;
CREATE TABLE `parkaddress` (
  `parkaddress_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '停车位置ID',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',

  `park_address` varchar(500) NOT NULL DEFAULT '' COMMENT '停车位置',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

  PRIMARY KEY (`parkaddress_id`),
  UNIQUE INDEX `adminUid_sn` (`admin_uid`, `sn`) USING HASH,
  KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='停车位置表';



/*订单表*/
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  -- common
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `class` tinyint(3) not null DEFAULT 1 COMMENT '单据类别',
  `value` double NOT NULL DEFAULT 0 COMMENT '货物价值（订单实际价值）',
  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/
  `operator_uid` int(10) unsigned NOT NULL COMMENT '开单人uid',
  `operator_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '开单人姓名，是冗余的，只存第一遍，之后不更改',
  `history` mediumtext NOT NULL DEFAULT '' COMMENT '操作记录',/*TODO:10000条操作记录,够么？*/
  `status` tinyint(3) not null DEFAULT 1 COMMENT '状态',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `cart` text NOT NULL COMMENT '购物车',/*50个sku:80*300，TODO:够么？*/
  `isdeliver` tinyint(4) not null DEFAULT 1 COMMENT '是否送货 1-送货 0-不送货',
  `freight` double NOT NULL DEFAULT 0 COMMENT '运费',
  `freight_received` double NOT NULL DEFAULT 0 COMMENT '代付已收运费',
  `is_calculated` int NOT NULL DEFAULT 0 COMMENT '1为计入成本,0为不计入成本',
  `freight_cal_method` int NOT NULL DEFAULT 0 COMMENT '运费计算方式 1- 按数量 2-按金额 不计入成本时默认为0',

  -- order和finance共同字段
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '往来单位主键',
  `cid_name` char(45) not null DEFAULT '' COMMENT '单位名称冗余',
  `off` double NOT NULL DEFAULT 0 COMMENT '优惠',
  `cash` double NOT NULL DEFAULT 0 COMMENT '现金',
  `bank` double NOT NULL DEFAULT 0 COMMENT '银行',
  `online_pay` double NOT NULL DEFAULT 0 COMMENT '在线支付',
  `income` double NOT NULL DEFAULT 0 COMMENT '实收（现金+银行+在线支付）',
  `name`  char(60) not null DEFAULT '' COMMENT '收入来源名称或费用用途名称',



  -- order
  `oid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'oid',
  `contact_name` char(45) not null DEFAULT '' COMMENT '联系人名称',
  `mobile` char(25) NOT NULL DEFAULT '' COMMENT '电话',
  `park_address` varchar(500) NOT NULL DEFAULT '' COMMENT '停车位置',
  `car_license` char(30) NOT NULL DEFAULT '' COMMENT '车牌号',
  `warehouse_remark` text NOT NULL DEFAULT '' COMMENT '送货信息备注',
  `sto_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '仓库id',

  `receivable` double NOT NULL DEFAULT 0 COMMENT '应收（货物价值-优惠）',
  `balance` double NOT NULL DEFAULT 0 COMMENT '发生交易的那一刻的本单结余快照（实收-应收）',
  `remain` double NOT NULL DEFAULT 0 COMMENT '等待客户付款的金额即待收金额，正数为客户欠店铺，负数为店铺欠客户',
  -- Company里的balance:结余（应付-应收），负数是别人欠店主，正数是店主欠别人
  `history_balance` double NOT NULL DEFAULT 0 COMMENT '此前结余',
  `total_balance` double NOT NULL DEFAULT 0 COMMENT '总结余（此前结余+本单结余）',

  `leave_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '出库时间',
  `exceptionNo` tinyint(3) not null DEFAULT 0 COMMENT '异常状态码',
  `exception` varchar(500) NOT NULL DEFAULT '' COMMENT '异常原因',
  `GeTuiGet` tinyint(3) not null DEFAULT 0 COMMENT '是否手机端已读',


  -- warehouse
  `wid` int(10) unsigned NOT NULL  COMMENT 'wid',
  `num` double NOT NULL DEFAULT 0 COMMENT '变更数量',
  `check_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '盘点人uid',
  `check_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '盘点人姓名，是冗余的，只存第一遍，之后不更改',
  `new_sto_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '新仓库id',

  -- finance
  `fid` int(10) unsigned NOT NULL  COMMENT 'fid',

  -- sn
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',

  PRIMARY KEY (`oid`),
  UNIQUE INDEX `adminUid_sn` (`admin_uid`, `sn`) USING HASH,
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`),
  KEY `remain` (`remain`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单表';


/*仓库进出表*/
DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE `warehouse` (
  -- common
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `class` tinyint(3) not null DEFAULT 1 COMMENT '单据类别',
  `value` double NOT NULL DEFAULT 0 COMMENT '货物价值（订单实际价值）',
  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/
  `operator_uid` int(10) unsigned NOT NULL COMMENT '开单人uid',
  `operator_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '开单人姓名，是冗余的，只存第一遍，之后不更改',
  `history` mediumtext NOT NULL DEFAULT '' COMMENT '操作记录',/*TODO:10000条操作记录,够么？*/
  `status` tinyint(3) not null DEFAULT 1 COMMENT '状态',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `cart` text NOT NULL COMMENT '购物车',/*50个sku:80*300，TODO:够么？*/
  `isdeliver` tinyint(4) not null DEFAULT 1 COMMENT '是否送货 1-送货 0-不送货',
  `freight` double NOT NULL DEFAULT 0 COMMENT '运费',
  `freight_received` double NOT NULL DEFAULT 0 COMMENT '代付已收运费',
  `is_calculated` int NOT NULL DEFAULT 0 COMMENT '1为计入成本,0为不计入成本',
  `freight_cal_method` int NOT NULL DEFAULT 0 COMMENT '运费计算方式 1- 按数量 2-按金额 不计入成本时默认为0',

  -- order和finance共同字段
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '往来单位主键',
  `cid_name` char(45) not null DEFAULT '' COMMENT '单位名称冗余',
  `off` double NOT NULL DEFAULT 0 COMMENT '优惠',
  `cash` double NOT NULL DEFAULT 0 COMMENT '现金',
  `bank` double NOT NULL DEFAULT 0 COMMENT '银行',
  `online_pay` double NOT NULL DEFAULT 0 COMMENT '在线支付',
  `income` double NOT NULL DEFAULT 0 COMMENT '实收（现金+银行+在线支付）',
  `name`  char(60) not null DEFAULT '' COMMENT '收入来源名称或费用用途名称',



  -- order
  `oid` int(10) unsigned NOT NULL COMMENT 'oid',
  `contact_name` char(45) not null DEFAULT '' COMMENT '联系人名称',
  `mobile` char(25) NOT NULL DEFAULT '' COMMENT '电话',
  `park_address` varchar(500) NOT NULL DEFAULT '' COMMENT '停车位置',
  `car_license` char(30) NOT NULL DEFAULT '' COMMENT '车牌号',
  `warehouse_remark` text NOT NULL DEFAULT '' COMMENT '送货信息备注',
  `sto_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '仓库id',

  `receivable` double NOT NULL DEFAULT 0 COMMENT '应收（货物价值-优惠）',
  `balance` double NOT NULL DEFAULT 0 COMMENT '发生交易的那一刻的本单结余快照（实收-应收）',
  `remain` double NOT NULL DEFAULT 0 COMMENT '等待客户付款的金额即待收金额，正数为客户欠店铺，负数为店铺欠客户',
  -- Company里的balance:结余（应付-应收），负数是别人欠店主，正数是店主欠别人
  `history_balance` double NOT NULL DEFAULT 0 COMMENT '此前结余',
  `total_balance` double NOT NULL DEFAULT 0 COMMENT '总结余（此前结余+本单结余）',

  `leave_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '出库时间',
  `exceptionNo` tinyint(3) not null DEFAULT 0 COMMENT '异常状态码',
  `exception` varchar(500) NOT NULL DEFAULT '' COMMENT '异常原因',
  `GeTuiGet` tinyint(3) not null DEFAULT 0 COMMENT '是否手机端已读',


  -- warehouse
  `wid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'wid',
  `num` double NOT NULL DEFAULT 0 COMMENT '变更数量',
  `check_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '盘点人uid',
  `check_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '盘点人姓名，是冗余的，只存第一遍，之后不更改',

  `new_sto_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '新仓库id',
  -- finance
  `fid` int(10) unsigned NOT NULL  COMMENT 'fid',

  -- sn
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',


  PRIMARY KEY (`wid`),
  UNIQUE INDEX `adminUid_sn` (`admin_uid`, `sn`) USING HASH,
  KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='仓库进出表';



/*财务表*/
DROP TABLE IF EXISTS `finance`;
CREATE TABLE `finance` (
  -- common
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `class` tinyint(3) not null DEFAULT 1 COMMENT '单据类别',
  `value` double NOT NULL DEFAULT 0 COMMENT '货物价值（订单实际价值）',
  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/
  `operator_uid` int(10) unsigned NOT NULL COMMENT '开单人uid',
  `operator_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '开单人姓名，是冗余的，只存第一遍，之后不更改',
  `history` mediumtext NOT NULL DEFAULT '' COMMENT '操作记录',/*TODO:10000条操作记录,够么？*/
  `status` tinyint(3) not null DEFAULT 1 COMMENT '状态',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  `cart` text NOT NULL COMMENT '购物车',/*50个sku:80*300，TODO:够么？*/
  `isdeliver` tinyint(4) not null DEFAULT 1 COMMENT '是否送货 1-送货 0-不送货',
  `freight` double NOT NULL DEFAULT 0 COMMENT '运费',
  `freight_received` double NOT NULL DEFAULT 0 COMMENT '代付已收运费',
  `is_calculated` int NOT NULL DEFAULT 0 COMMENT '1为计入成本,0为不计入成本',
  `freight_cal_method` int NOT NULL DEFAULT 0 COMMENT '运费计算方式 1- 按数量 2-按金额 不计入成本时默认为0',

  -- order和finance共同字段
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '往来单位主键',
  `cid_name` char(45) not null DEFAULT '' COMMENT '单位名称冗余',
  `off` double NOT NULL DEFAULT 0 COMMENT '优惠',
  `cash` double NOT NULL DEFAULT 0 COMMENT '现金',
  `bank` double NOT NULL DEFAULT 0 COMMENT '银行',
  `online_pay` double NOT NULL DEFAULT 0 COMMENT '在线支付',
  `income` double NOT NULL DEFAULT 0 COMMENT '实收（现金+银行+在线支付）',
  `name`  char(60) not null DEFAULT '' COMMENT '收入来源名称或费用用途名称',



  -- order
  `oid` int(10) unsigned NOT NULL  COMMENT 'oid',
  `contact_name` char(45) not null DEFAULT '' COMMENT '联系人名称',
  `mobile` char(25) NOT NULL DEFAULT '' COMMENT '电话',
  `park_address` varchar(500) NOT NULL DEFAULT '' COMMENT '停车位置',
  `car_license` char(30) NOT NULL DEFAULT '' COMMENT '车牌号',
  `warehouse_remark` text NOT NULL DEFAULT '' COMMENT '送货信息备注',
  `sto_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '仓库id',

  `receivable` double NOT NULL DEFAULT 0 COMMENT '应收（货物价值-优惠）',
  `balance` double NOT NULL DEFAULT 0 COMMENT '发生交易的那一刻的本单结余快照（实收-应收）',
  `remain` double NOT NULL DEFAULT 0 COMMENT '等待客户付款的金额即待收金额，正数为客户欠店铺，负数为店铺欠客户',
  -- Company里的balance:结余（应付-应收），负数是别人欠店主，正数是店主欠别人，这个意义是错的，应该是remain的意义才是对的，待修正
  `history_balance` double NOT NULL DEFAULT 0 COMMENT '此前结余',
  `total_balance` double NOT NULL DEFAULT 0 COMMENT '总结余（此前结余+本单结余）',

  `leave_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '出库时间',
  `exceptionNo` tinyint(3) not null DEFAULT 0 COMMENT '异常状态码',
  `exception` varchar(500) NOT NULL DEFAULT '' COMMENT '异常原因',
  `GeTuiGet` tinyint(3) not null DEFAULT 0 COMMENT '是否手机端已读',


  -- warehouse
  `wid` int(10) unsigned NOT NULL  COMMENT 'wid',
  `num` double NOT NULL DEFAULT 0 COMMENT '变更数量',
  `check_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '盘点人uid',
  `check_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '盘点人姓名，是冗余的，只存第一遍，之后不更改',
  `new_sto_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '新仓库id',
  -- finance
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'fid',

  -- sn
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',

  PRIMARY KEY (`fid`),
  UNIQUE INDEX `adminUid_sn` (`admin_uid`, `sn`) USING HASH,
  KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='财务表';





/*反馈表*/
DROP TABLE IF EXISTS `feedback`;
CREATE TABLE `feedback` (
  `feedback_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'feedback_id',

  `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
  `content` mediumtext NOT NULL DEFAULT '' COMMENT '反馈内容',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

  PRIMARY KEY (`feedback_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='反馈表';





/*query用的假表*/
DROP TABLE IF EXISTS `query`;
CREATE TABLE `query` (
  `sku_id` int(10) unsigned NOT NULL COMMENT 'sku_id',
  `page` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '请求第几页的数据',
  `pline` int(10) unsigned NOT NULL DEFAULT 10 COMMENT '一页多少行',

  -- `filter` mediumtext not null DEFAULT '' COMMENT '过滤器条件',
  `remainType` tinyint(3) NOT NULL DEFAULT 1 COMMENT '应收应付情况。1-店铺还需收款的单据；2-店铺还需付款的单据',
  `reg_st_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间的开始时间',
  `reg_end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间的结束时间',
  -- `search` mediumtext NOT NULL DEFAULT '' COMMENT '搜索内容'
  `oid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'oid',
  `wid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'wid',
  `fid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'fid',
  `operator_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'operator_uid',
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '往来单位主键',

  `type` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'search时的type',
  `search` mediumtext NOT NULL DEFAULT '' COMMENT '搜索内容'

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='query用的假表';

/*updateDatabaseFormat 假表*/
DROP TABLE IF EXISTS `update_database_format`;
CREATE TABLE `update_database_format` (
  `reg_st_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间的开始时间'

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='update_database_format用的假表';



/*Util用的假表*/
DROP TABLE IF EXISTS `util`;
CREATE TABLE `util` (
  `type` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'type',
  `mobile` char(15) NOT NULL DEFAULT '' COMMENT '用户手机',
  `verify_code` char(4) NOT NULL DEFAULT '' COMMENT '验证码'

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='Util用的假表';





/*每日汇总表*/
DROP TABLE IF EXISTS `everyday_summary_sheet`;
CREATE TABLE `everyday_summary_sheet` (
  `everyday_summary_sheet_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'everyday_summary_sheet_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  

  `sale` double NOT NULL DEFAULT 0 COMMENT '销售额，算优惠掉的钱数',
  `sale_off` double NOT NULL DEFAULT 0 COMMENT '销售优惠额',
  `receivable` double NOT NULL DEFAULT 0 COMMENT '应收款',
  `payable` double NOT NULL DEFAULT 0 COMMENT '应付款',
  `actually_income` double NOT NULL DEFAULT 0 COMMENT '总实收款',
  `actually_paid` double NOT NULL DEFAULT 0 COMMENT '总实付款',
  
  `income_cash_total` double NOT NULL DEFAULT 0 COMMENT '现金收入总计',
  `income_bank_total` double NOT NULL DEFAULT 0 COMMENT '银行收入总计',
  `income_online_pay_total` double NOT NULL DEFAULT 0 COMMENT '网络收入总计',
  `balance_cash_total` double NOT NULL DEFAULT 0 COMMENT '现金结余总计',
  `balance_bank_total` double NOT NULL DEFAULT 0 COMMENT '银行结余总计',
  `balance_online_pay_total` double NOT NULL DEFAULT 0 COMMENT '网络结余总计',
  
  `other_income` double NOT NULL DEFAULT 0 COMMENT '其他收入总计',
  `expense` double NOT NULL DEFAULT 0 COMMENT '费用总计',
  `gross_profit` double NOT NULL DEFAULT 0 COMMENT '毛利润',

  `statistics` text NOT NULL COMMENT '完整的统计信息',/*TODO:够么？*/

  PRIMARY KEY (`everyday_summary_sheet_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `reg_time` (`reg_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='每日汇总表';





-- ================================权限相关Start=======================================================

/*规则表*/
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` char(200) NOT NULL DEFAULT '' COMMENT '规则唯一标识',
  `title` char(200) NOT NULL DEFAULT '' COMMENT '规则中文名称',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：为1正常，为0禁用',
  `condition` char(200) NOT NULL DEFAULT '' COMMENT '规则表达式，为空表示存在就验证，不为空表示按照条件验证，即规则附件条件,满足附加条件的规则,才认为是有效的规则',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='规则表';



/*用户组表*/
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` char(200) NOT NULL DEFAULT '' COMMENT '用户组中文名称',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '为1正常，为0禁用',
  `rules` text(65535) NOT NULL DEFAULT '' COMMENT '用户组拥有的规则id，多个规则","隔开',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户组表';



/*用户组明细表*/
DROP TABLE IF EXISTS `auth_group_access`;
CREATE TABLE `auth_group_access` (
  `uid` int(10) unsigned NOT NULL COMMENT 'uid',
  `group_id` int(10) unsigned NOT NULL COMMENT '用户组id',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户组明细表';

-- ================================权限相关End=======================================================





/*报名表*/
DROP TABLE IF EXISTS `apply`;
CREATE TABLE `apply` (
  `apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'apply_id',

  `name` varchar(32) NOT NULL DEFAULT '未填写' COMMENT '姓名',
  `shop_name` varchar(100) not null DEFAULT '' COMMENT '公司名',
  `industry_name` varchar(500) not null DEFAULT '' COMMENT '行业名',
  `wechat` varchar(500) not null DEFAULT '' COMMENT '微信号',
  `mobile` varchar(50) NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
  `where_know` varchar(200) NOT NULL DEFAULT '' COMMENT '从何处得知我们',
  `isDone` tinyint(255) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否跟进',
  `reg_time_format` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '注册时间-格式化',

  `country` varchar(100) not null DEFAULT '' COMMENT '国家',
  `area` varchar(100) not null DEFAULT '' COMMENT '区域',
  `region` varchar(100) not null DEFAULT '' COMMENT '省份',
  `city` varchar(100) not null DEFAULT '' COMMENT '市',
  `county` varchar(100) not null DEFAULT '' COMMENT '县',
  `isp` varchar(100) not null DEFAULT '' COMMENT 'ISP服务商',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` varchar(100) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

  PRIMARY KEY (`apply_id`)
) ENGINE =InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='报名表';




/*服务器统计信息表*/
DROP TABLE IF EXISTS `server_statistics`;
CREATE TABLE `server_statistics` (
  `ss_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ss_id',

  `uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '用户ID',
  `where_know` varchar(200) NOT NULL DEFAULT '' COMMENT '从何处得知我们',

  `reg_time_format` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '注册时间-格式化',
  `reg_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '注册时间',
  `reg_ip` varchar(100) NOT NULL DEFAULT '0' COMMENT '注册IP',

  PRIMARY KEY (`ss_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='服务器统计信息表';





/*sku_单据 关联表*/
DROP TABLE IF EXISTS `sku_bill`;
CREATE TABLE `sku_bill` (
  `sku_bill_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'sku_bill_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `sku_id` int(10) unsigned NOT NULL COMMENT 'sku_id',
  `spu_id` int(10) unsigned NOT NULL COMMENT 'spu_id',
  `oid` int(10) unsigned NOT NULL COMMENT 'oid',
  `wid` int(10) unsigned NOT NULL COMMENT 'wid',
  `bill_class` tinyint(3) not null DEFAULT 1 COMMENT '单据类别',
  `bill_status` tinyint(3) not null DEFAULT 1 COMMENT '单据状态',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

  PRIMARY KEY (`sku_bill_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `sku_id` (`sku_id`),
  KEY `spu_id` (`spu_id`),
  KEY `bill_class` (`bill_class`),
  KEY `reg_time` (`reg_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='sku_单据 关联表';


/*sku_cid_price 关联表*/
DROP TABLE IF EXISTS `sku_cid_price`;
CREATE TABLE `sku_cid_price` (
  `sku_cid_price_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'sku_cid_price_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `sku_id` int(10) unsigned NOT NULL COMMENT 'sku_id',
  `spu_id` int(10) unsigned NOT NULL COMMENT 'spu_id',
  `cid` int(10) unsigned NOT NULL COMMENT 'cid',
  `price1` double NOT NULL DEFAULT 0 COMMENT '最近售价1',
  `quantity1` double NOT NULL DEFAULT 0 COMMENT '最近卖出数量1（与price1对应）',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

  PRIMARY KEY (`sku_cid_price_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `sku_id` (`sku_id`),
  KEY `spu_id` (`spu_id`),
  KEY `cid` (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='sku_cid_price 关联表';





/*支付单据表（向星云进销存支付的单据）*/
DROP TABLE IF EXISTS `paybill`;
CREATE TABLE `paybill` (
  `paybill_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'paybill_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的老板uid',
  `uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建人uid',
  `sn` char(33) NOT NULL DEFAULT '' COMMENT 'sn',

  `bill_title` char(100) not null DEFAULT '' COMMENT '账单名称',
  `bill_money` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单金额，单位：分',
  `bill_class` tinyint(3) not null DEFAULT 1 COMMENT '单据类别',
  `bill_status` tinyint(3) not null DEFAULT 0 COMMENT '单据状态,0-未处理，1-已完成,2-已处理，但该单据无效',
  `member_count` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '一共买了几个月',
  `sign` char(33) NOT NULL DEFAULT '' COMMENT '自己加密的签名',
  `channel_type` char(33) NOT NULL DEFAULT '' COMMENT '渠道类型，WX/ALI/UN/KUAIQIAN/JD/BD/YEE/PAYPAL 分别代表微信/支付宝/银联/快钱/京东/百度/易宝/PAYPAL',
  `sub_channel_type` char(33) NOT NULL DEFAULT '' COMMENT '代表以上各个渠道的子渠道，参看字段说明',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

  PRIMARY KEY (`paybill_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `uid` (`uid`),
  UNIQUE KEY `sn` (`sn`) USING HASH
  -- UNIQUE INDEX `sn` (`sn`) USING HASH todo:????
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='支付单据表（向星云进销存支付的单据）';





/*星云进销存的用户账户系统*/
DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account` (
  `user_account_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'user_account_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的老板uid',

  `member_class` tinyint(3) not null DEFAULT 0 COMMENT '会员类型',
  `member_buy_count` tinyint(3) not null DEFAULT 0 COMMENT '这次购买的是什么时常的套餐：0、1、3、6、12',
  `member_st_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员开始时间',
  `member_count` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '从开始时间起一共买了几个月',
  `member_end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员结束时间',
  `save_off_record` tinyint(3) not null DEFAULT 0 COMMENT '所享受的优惠记录.1-购买过6个月的优惠;2-购买过12个月的优惠（含2个6个月或1个12个月）',

  `balance` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '账户余额，单位：分',
  `balance_sms_gift` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '账户短信费赠送余额，单位：分',

  `sign` char(33) NOT NULL DEFAULT '' COMMENT '自己加密的签名',

  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

  PRIMARY KEY (`user_account_id`),
  UNIQUE KEY `admin_uid` (`admin_uid`),
  KEY `member_end_time` (`member_end_time`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='星云进销存的用户账户系统';





/*星云进销存用户的短信发送记录明细表*/
DROP TABLE IF EXISTS `sms_details`;
CREATE TABLE `sms_details` (
  `sms_details_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'sms_details_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的老板uid',

  `class` tinyint(3) not null DEFAULT 0 COMMENT '类型,1-对账单.2-祝福短信',
  `operator_uid` int(10) unsigned NOT NULL COMMENT '操作人的uid',
  `phone` text NOT NULL DEFAULT '' COMMENT '收件人字符串',
  `num` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '一个人的短信条数',
  `sms_text` text NOT NULL DEFAULT '' COMMENT '发送的短信内容',/*TODO:够么？*/
  `money` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '本次短信的费用，单位：分',
  `sign` char(33) NOT NULL DEFAULT '' COMMENT '自己加密的签名',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

  PRIMARY KEY (`sms_details_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `reg_time` (`reg_time`),
  KEY `class` (`class`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='星云进销存用户的短信发送记录明细表';





/*星云进销存用户的余额支出明细表（内扣系统）*/
DROP TABLE IF EXISTS `payment_details`;
CREATE TABLE `payment_details` (
  `payment_details_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'payment_details_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的老板uid',

  `class` tinyint(3) not null DEFAULT 0 COMMENT '类型,1-短信',
  `id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '对应详情信息的主键值',
  `operator_uid` int(10) unsigned NOT NULL COMMENT '操作人的uid',
  `money` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '本次交易金额，单位：分',
  `sign` char(33) NOT NULL DEFAULT '' COMMENT '自己加密的签名',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

  PRIMARY KEY (`payment_details_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `reg_time` (`reg_time`),
  KEY `class` (`class`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='星云进销存用户的余额支出明细表（内扣系统）';



/*Node异步任务列表*/
DROP TABLE IF EXISTS `asyn_tasks`;
CREATE TABLE `asyn_tasks` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'task_id',
  `name` char(20) NOT NULL DEFAULT '' COMMENT '任务名',
  `class` tinyint(3) NOT NULL DEFAULT 0 COMMENT '任务类型,1-通知库管',
  `publisher` char(30) NOT NULL DEFAULT '' COMMENT '发布异步任务的session号',
  `data` text NOT NULL DEFAULT '' COMMENT '异步任务的Json入参',
  `sign` char(33) NOT NULL DEFAULT '' COMMENT '自己加密的签名',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

  PRIMARY KEY (`task_id`),
  KEY `reg_time`(`reg_time`),
  KEY `class` (`class`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='需要Node处理的异步任务列表';




/*存储打印模板的表*/
DROP TABLE IF EXISTS `print_template`;
CREATE TABLE `print_template` (
  `print_template_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'print_template_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `class` tinyint(3) NOT NULL DEFAULT 0 COMMENT '该模板的种类,1-销售出库单',
  `font_size` tinyint(3) NOT NULL DEFAULT 0 COMMENT '单据货物行里的字号',
  `content` text NOT NULL DEFAULT '' COMMENT '模板',
  `optionArray` text NOT NULL DEFAULT '' COMMENT '选项数组',

  PRIMARY KEY (`print_template_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `class` (`class`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='存储打印模板的表';




/*other用的假表*/
DROP TABLE IF EXISTS `other`;
CREATE TABLE `other` (
  `reg_st_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间的开始时间'

) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='other用的假表';

/*statement_account对账单表*/
DROP TABLE IF EXISTS `statement_account`;
CREATE TABLE `statement_account` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `s_guid` char(80)  NOT NULL DEFAULT '0' COMMENT '随机验证码',
  `s_pwd`char(6)  NOT NULL DEFAULT '0' COMMENT '获取账单密码',
  `statementofaccount`text  NOT NULL DEFAULT '' COMMENT '账单内容',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='存储客户需要的对账单信息表';


/*sku_storage sku仓库关联表*/
DROP TABLE IF EXISTS `sku_storage`;
CREATE TABLE `sku_storage` (
  `sku_storage_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `sku_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'sku_id',
  `sto_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '仓库名称',
  `admin_uid`int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属店铺uid',
  `unit_price` double NOT NULL DEFAULT 0 COMMENT '库存产品的成本（单价）',
  `stock` double NOT NULL DEFAULT 0 COMMENT '库存数量',
  `reg_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT'创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT 0 COMMENT'修改时间',
  `sku_sto_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT 'sku在所在仓库的显示顺序，一个正整数，数字越小，显示越靠前，1是最前',
  `sku_sto_status` tinyint(4)  NOT NULL DEFAULT 1 COMMENT '状态',/*1是开启,0为未启用,-1为已删除*/
  PRIMARY KEY (`sku_storage_id`),
  UNIQUE INDEX `adminUid_sku_sto` (`admin_uid`, `sto_id`,`sku_id`) USING HASH,
  KEY `sku_id` (`sku_id`),
  KEY `sto_id` (`sto_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `sku_sto_status` (`sku_sto_status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='sku仓库关联表';

/*账户表*/
DROP TABLE IF EXISTS `account`;
CREATE TABLE `account` (
  `account_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `account_creator` char(80) NOT NULL DEFAULT '' COMMENT '开户人',
  `account_number` char(20) NOT NULL DEFAULT '' COMMENT '账号',
  `account_name` char(80) NOT NULL DEFAULT '' COMMENT '账户名称',
  `account_source_name` char(20) NOT NULL DEFAULT '' COMMENT '来源名称',
  `account_source_type` int(2) NOT NULL DEFAULT 0 COMMENT '账户来源类型',
  `account_balance` double NOT NULL DEFAULT 0 COMMENT '账户余额',
  `province` char(10) NOT NULL DEFAULT '' COMMENT '省',
  `city` char(10) NOT NULL DEFAULT '' COMMENT '市',
  `bank_name` char(20) NOT NULL DEFAULT '' COMMENT '开户行',
  `qcode` char(50) NOT NULL DEFAULT '' COMMENT '速查编码',
  `account_remark` text NOT NULL DEFAULT '' COMMENT '账户信息备注',
  `status` tinyint(4) not null DEFAULT 0 COMMENT '账户状态（1.有效,0,无效）',
  `sn` char(20) NOT NULL DEFAULT '' COMMENT 'sn',

  `reg_time` timestamp NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT 0 COMMENT '最后操作时间',
  PRIMARY KEY (`account_id`),
  KEY `admin_uid` (`admin_uid`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='账户表';

/*开户来源表*/
DROP TABLE IF EXISTS `account_source`;
CREATE TABLE `account_source` (
  `account_source_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `account_prefix` char(10) NOT NULL DEFAULT '' COMMENT '账户来源前6位',
  `account_source_name` char(20) NOT NULL DEFAULT 'unknownbank' COMMENT '来源名称',
  `account_source_type` int(2) NOT NULL DEFAULT 0 COMMENT '账户来源类型',
   PRIMARY KEY (`account_source_id`),
   KEY `account_prefix` (`account_prefix`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='账户来源表';

/*账户操作记录表*/
DROP TABLE IF EXISTS `account_operation_record`;
CREATE TABLE `account_operation_record` (
  `account_operation_record_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `account_id` int(20) unsigned NOT NULL COMMENT '账户ID',
  `account_number` char(20) NOT NULL DEFAULT '' COMMENT '账号',
  `account_operation_class` int(3) NOT NULL DEFAULT 0 COMMENT '账户操作类型（操作来源）',
  `cost` int(20) NOT NULL DEFAULT 0 COMMENT '交易金额',
  `account_operation_code` char(20) NOT NULL DEFAULT '' COMMENT '操作单号',
  `reg_time` timestamp NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT 0 COMMENT '更新时间',
  `account_operation_remark` text NOT NULL DEFAULT '' COMMENT '账户操作备注',
  PRIMARY KEY (`account_operation_record_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `account_id` (`account_id`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='账户操作记录表';

/**代理表*/
DROP TABLE IF EXISTS `proxy_company`;
CREATE TABLE `proxy_company` (
  `proxy_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `name` char(10) NOT NULL DEFAULT '' COMMENT '姓名',
  `area` char(20) NOT NULL DEFAULT '' COMMENT '所在地区',
  `mobile` char(20) NOT NULL DEFAULT '' COMMENT '手机号',
  PRIMARY KEY (`proxy_id`),
  KEY `name` (`name`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='代理商表';

DROP TABLE IF EXISTS  `department`;
CREATE TABLE `department` (
  `depart_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `depart_name` char(10) NOT NULL DEFAULT '' COMMENT '部门名字',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  `depart_manager_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '预留字段,部门主管的UID',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '部门状态（1.有效,0,无效',
  `remark` text NOT NULL DEFAULT '' COMMENT '部门信息备注',
  `reg_time` timestamp NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT 0 COMMENT '最后操作时间',
  PRIMARY KEY (`depart_id`),
  KEY `admin_uid` (`admin_uid`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='部门表';


DROP TABLE IF EXISTS  `face_rec`;
CREATE TABLE `face_rec` (
  `face_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'cid',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'admin_uid',
  `name` char(45) not null DEFAULT '' COMMENT '单位名称',
  `photo` text(600) not null DEFAULT '' COMMENT '图片地址',
  `remark` text NOT NULL DEFAULT '' COMMENT '备注',
  `reg_time` int(10) unsigned DEFAULT 0 COMMENT '创建时间',
  `update_time` int(10) unsigned DEFAULT 0 COMMENT '最后操作时间',
  PRIMARY KEY (`face_id`),
  KEY `admin_uid` (`admin_uid`)
  -- constraint FK_CONTACT_cid foreign key (cid) references company(cid) on delete cascade on update cascade,
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='人脸识别表';

DROP TABLE IF EXISTS  `cus_list`;
CREATE TABLE `cus_list` (
  `cus_list_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'cid',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'admin_uid',
  `company_name` char(45) not null DEFAULT '' COMMENT '单位名称',
  `photo` text(600) not null DEFAULT '' COMMENT '图片地址',
  `update_time` int(10) unsigned DEFAULT 0 COMMENT '最后操作时间',
  PRIMARY KEY (`cus_list_id`),
  KEY `admin_uid` (`admin_uid`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='访客列表';

/**七牛上传相关表*/
DROP TABLE IF EXISTS  `qn_upload`;
CREATE TABLE `qn_upload` (
  `upload_id` int(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增主键',
  `uid` int(20) NOT NULL DEFAULT 0 COMMENT 'uid',
  `fname` varchar(512) NOT NULL,
  `fkey` varchar(512) NOT NULL,
  `create_time`  int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `description` varchar(1024),
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态（1.有效,0,无效)',
  `remark` text NOT NULL DEFAULT '' COMMENT '信息备注',
  `reg_time` timestamp NOT NULL DEFAULT 0 COMMENT '创建时间',
  `update_time` timestamp NOT NULL DEFAULT 0 COMMENT '最后操作时间',
  PRIMARY KEY (`upload_id`),
  KEY `uid` (`uid`)
)ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='七牛上传相关表';


