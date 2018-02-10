<?php
namespace \DatebaseSQL;

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
  `email` char(32) NOT NULL DEFAULT '' COMMENT '用户邮箱',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `last_login_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_login_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录IP',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(4) not null DEFAULT 0 COMMENT '用户状态',

  `name` char(32) NOT NULL DEFAULT '未填写' COMMENT '姓名',

  `TODAY_HISTORY_CONFIG` varchar(500) NOT NULL DEFAULT '' COMMENT '今日经营历史的过滤器配置',/*TODO:够么？*/

  PRIMARY KEY (`uid`),
  UNIQUE KEY `admin_mobile` (`admin_mobile`),
  UNIQUE KEY `shop_name` (`shop_name`),
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户表';
INSERT INTO `user` 
(`uid`, `shop_name`, `admin_mobile`, `username`, `password`,`mobile`,`status`,`name`,`admin_uid`) VALUES 
(NULL, 'xy','57128121110', '57128121110', '359699051ee8891ca09ee3989ccdbd90','57128121110','1','杭州跃迁科技有限公司',1);/*密码是123456*/;
INSERT INTO `user` 
(`uid`, `shop_name`, `admin_mobile`, `username`, `password`,`mobile`,`status`,`name`,`admin_uid`) VALUES
(NULL, 'xyjxc','57128121110AkGVahB15029297212', '15029297212', '359699051ee8891ca09ee3989ccdbd90','15029297212','1','跃迁科技2',1);/*密码是123456*/;


/*配置表，一行一个企业的配置*/
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `EMPLOYEE_NUM` int(10) unsigned not null DEFAULT 2 COMMENT '员工数量',

  /*流程模式*/
  `WAREHOUSE_FLOW` boolean not null DEFAULT 1 COMMENT '是否开启库管端流程模式,true开启，false关闭',
  `ORDER_FLOW_MODE` tinyint(4) not null DEFAULT 2 COMMENT '订单流程模式',/*1-开启审核模式,2-关闭审核模式*/
  `DELETE_ORDER_ALLOW_STATUS` tinyint(4) not null DEFAULT 2 COMMENT '删除订单所允许的操作状态模式',/*1-状态8/10可以删除订单，2-状态8/10不能删除订单*/
  
  PRIMARY KEY (`id`),
  KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='配置表';
INSERT INTO `config` (`id`,`admin_uid`, `EMPLOYEE_NUM`) VALUES (NULL, '1',2);



-- ================================商品相关Start=======================================================
DROP TABLE IF EXISTS `sku`;
DROP TABLE IF EXISTS `spu`;
DROP TABLE IF EXISTS `cat`;

    /*商品分类表*/;
    CREATE TABLE `cat` (
      `cat_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'cat_id',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      
      `cat_name` char(20) NOT NULL DEFAULT '未分类' COMMENT '所属类别名称',
      `cat_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT 'cat显示顺序',
      `status` tinyint(4) not null DEFAULT 1 COMMENT '状态',/*1是开启，0为未启用*/

      `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

      PRIMARY KEY (`cat_id`),
      KEY `admin_uid` (`admin_uid`),
      KEY `status` (`status`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品分类表';
    INSERT INTO `cat` (`cat_name`,`cat_index`,`status`,`admin_uid`,`reg_time`) VALUES ('类别1','1','1','1','1453744541');


    /*商品spu表*/;
    CREATE TABLE `spu` (
      `spu_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'spu_id',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      `cat_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属类别id',/*0为未分类*/
      
      `spu_name` char(20) NOT NULL DEFAULT '' COMMENT '商品名称(spu)',
      `spu_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT '商品spu显示顺序,一个正整数，数字越小，显示越靠前',
      `qcode` char(20) NOT NULL DEFAULT '' COMMENT '速查编码',/*精确到spu*/
      `status` tinyint(4) not null DEFAULT 1 COMMENT '状态',/*1是开启，0为未启用*/

      `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',

      PRIMARY KEY (`spu_id`),
      KEY `admin_uid` (`admin_uid`),
      KEY `status` (`status`),
      constraint FK_SPU_cat_id foreign key (cat_id) references cat(cat_id) on delete cascade on update cascade
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品spu表';
    INSERT INTO `spu` (`spu_name`,`spu_index`,`qcode`,`status`,`admin_uid`,`reg_time`,`cat_id`) VALUES ('商品名称','1','速查编码','1','1','1453744606','1');

/*商品sku表*/;
CREATE TABLE `sku` (
  `sku_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'sku_id',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
  
  -- spu表冗余信息
  `spu_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '商品(SPU)名称id',
  `spu_name` char(20) NOT NULL DEFAULT '' COMMENT '商品名称(spu)',
  `spu_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT '商品spu显示顺序,一个正整数，数字越小，显示越靠前',
  `qcode` char(20) NOT NULL DEFAULT '' COMMENT '速查编码',/*精确到spu*/
  `spu_status` tinyint(4) not null DEFAULT '1' COMMENT '状态',/*1是开启，0为未启用*/

  -- cat表冗余信息
  `cat_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属类别id',/*0为未分类*/
  `cat_name` char(20) NOT NULL DEFAULT '未分类' COMMENT '所属类别名称',
  `cat_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT 'cat显示顺序',
  `cat_status` tinyint(4) not null DEFAULT '1' COMMENT '状态',/*1是开启，0为未启用*/

  -- sku自带信息
  `spec_name` char(20) NOT NULL DEFAULT '默认' COMMENT '规格名称',  
  `stock` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '库存数量',
  `unit_price` double NOT NULL DEFAULT 0 COMMENT '库存产品的成本（单价）',
  `sku_index` int(5) unsigned NOT NULL DEFAULT 0 COMMENT '商品sku显示顺序,一个正整数，数字越小，显示越靠前',
  `status` tinyint(4) not null DEFAULT 1 COMMENT '状态',/*1是开启，0为未启用*/

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',

  PRIMARY KEY (`sku_id`),
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`),
  constraint FK_SKU_spu_id foreign key (spu_id) references spu(spu_id) on delete cascade on update cascade,
  constraint FK_SKU_cat_id foreign key (cat_id) references cat(cat_id) on delete cascade on update cascade
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='商品sku表';
INSERT INTO `sku` (`admin_uid`,`spu_id`,`spu_name`,`spu_index`,`qcode`,`spu_status`,`cat_id`,`cat_name`,`cat_index`,`cat_status`,`spec_name`,`stock`,`unit_price`,`sku_index`,`status`,`reg_time`,`update_time`) VALUES ('1','1','商品名称','1','速查编码','1','1',NULL,NULL,NULL,'10只','1000','123','1','1','1453744606','1453744606'),('1','1','商品名称','1','速查编码','1','1',NULL,NULL,NULL,'12只','1000','250','2','1','1453744606','1453744606');

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
  
  `name` char(45) not null DEFAULT '' COMMENT '单位名称',
  `qcode` char(20) NOT NULL DEFAULT '' COMMENT '速查编码',
  `mobile` char(25) NOT NULL DEFAULT '' COMMENT '电话',
  `address` char(60) NOT NULL DEFAULT '' COMMENT '地址',

  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/

  `balance` double NOT NULL DEFAULT 0 COMMENT '结余（应付-应收），负数是别人欠店主，正数是店主欠别人',
  `status` tinyint(4) not null DEFAULT 1 COMMENT '状态，是否启用',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `lock_version` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '乐观锁',/*TODO:对么！？*/

  PRIMARY KEY (`cid`),
  UNIQUE INDEX `adminUid_companyName` (`admin_uid`, `name`) USING HASH,
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='往来单位表';

    /*联系人表*/
    CREATE TABLE `contact` (
      `contact_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '联系人ID',
      `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',
      `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的cid',

      `name` char(45) not null DEFAULT '' COMMENT '联系人名称',

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

  `park_address` varchar(500) NOT NULL DEFAULT '' COMMENT '停车位置',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

  PRIMARY KEY (`parkaddress_id`),
  KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='停车位置表';



/*订单表*/
DROP TABLE IF EXISTS `order`;
CREATE TABLE `order` (
  `oid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'oid',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `class` tinyint(3) not null DEFAULT 1 COMMENT '单据类别',
  -- 单位信息
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '往来单位主键',
  `company_name` char(45) not null DEFAULT '' COMMENT '单位名称冗余',
  `contact_name` char(45) not null DEFAULT '' COMMENT '联系人名称',
  `mobile` char(25) NOT NULL DEFAULT '' COMMENT '电话',
  `park_address` varchar(500) NOT NULL DEFAULT '' COMMENT '停车位置',
  `car_license` char(30) NOT NULL DEFAULT '' COMMENT '车牌号',
  
  -- 订单信息
  `value` double NOT NULL DEFAULT 0 COMMENT '货物价值（订单实际价值）',
  `off` double NOT NULL DEFAULT 0 COMMENT '优惠',
  `receivable` double NOT NULL DEFAULT 0 COMMENT '应收（货物价值-优惠）',
  `cash` double NOT NULL DEFAULT 0 COMMENT '现金',
  `bank` double NOT NULL DEFAULT 0 COMMENT '银行',
  `online_pay` double NOT NULL DEFAULT 0 COMMENT '在线支付',
  `income` double NOT NULL DEFAULT 0 COMMENT '实收（现金+银行+在线支付）',
  `balance` double NOT NULL DEFAULT 0 COMMENT '发生交易的那一刻的本单结余快照（实收-应收）',
  `remain` double NOT NULL DEFAULT 0 COMMENT '等待客户付款的金额即待收金额，正数为客户欠店铺，负数为店铺欠客户',
  -- balance:结余（应付-应收），负数是别人欠店主，正数是店主欠别人
  `history_balance` double NOT NULL DEFAULT 0 COMMENT '此前结余',
  `total_balance` double NOT NULL DEFAULT 0 COMMENT '总结余（此前结余+本单结余）',


  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/
  `operator_uid` int(10) unsigned NOT NULL COMMENT '开单人uid',
  `operator_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '开单人姓名，是冗余的，只存第一遍，之后不更改',
  `history` mediumtext NOT NULL DEFAULT '' COMMENT '操作记录',/*TODO:10000条操作记录,够么？*/
  `leave_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '出库时间',
  `status` tinyint(3) not null DEFAULT 1 COMMENT '状态',
  `exceptionNo` tinyint(3) not null DEFAULT 0 COMMENT '异常状态码',
  `exception` varchar(500) NOT NULL DEFAULT '' COMMENT '异常原因',

  -- 购物车信息
  `cart` text NOT NULL COMMENT '购物车',/*50个sku:80*300，TODO:够么？*/

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',

  PRIMARY KEY (`oid`),
  KEY `admin_uid` (`admin_uid`),
  KEY `status` (`status`),
  KEY `remain` (`remain`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='订单表';


/*仓库进出表*/
DROP TABLE IF EXISTS `warehouse`;
CREATE TABLE `warehouse` (
  `wid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'wid',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `class` tinyint(3) not null DEFAULT 0 COMMENT '单据类别',
  `num` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '变更数量',
  `value` double NOT NULL DEFAULT 0 COMMENT '变更总价值',
  `cart` text NOT NULL COMMENT '购物车',/*50个sku:80*300，TODO:够么？*/
  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/
  `history` mediumtext NOT NULL DEFAULT '' COMMENT '操作记录',/*10000条操作记录,TODO:够么？*/
  
  `operator_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '调整执行人uid',
  `operator_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '调整执行人姓名，是冗余的，只存第一遍，之后不更改',
  `check_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '盘点人uid',
  `check_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '盘点人姓名，是冗余的，只存第一遍，之后不更改',

  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',

  PRIMARY KEY (`wid`),
  KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='仓库进出表';



/*财务表*/
DROP TABLE IF EXISTS `finance`;
CREATE TABLE `finance` (
  `fid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'fid',
  `admin_uid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '所属的创建者uid',

  `class` tinyint(3) not null DEFAULT 0 COMMENT '单据类别',
  `name`  char(60) not null DEFAULT '' COMMENT '收入来源名称或费用用途名称',

    -- 单位信息
  `cid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '往来单位主键',
  `cid_name` char(45) not null DEFAULT '' COMMENT '单位名称冗余',

  `cash` double NOT NULL DEFAULT 0 COMMENT '现金',
  `bank` double NOT NULL DEFAULT 0 COMMENT '银行',
  `online_pay` double NOT NULL DEFAULT 0 COMMENT '在线支付',
  `income` double NOT NULL DEFAULT 0 COMMENT '实收（现金+银行+在线支付）',
  `remark` text NOT NULL DEFAULT '' COMMENT '备注',/*TODO:够么？*/

  `operator_uid` int(10) unsigned NOT NULL COMMENT '开单人uid',
  `operator_name` char(16) NOT NULL DEFAULT '未填写' COMMENT '开单人姓名，是冗余的，只存第一遍，之后不更改',
  `history` mediumtext NOT NULL DEFAULT '' COMMENT '操作记录',/*10000条操作记录,TODO:够么？*/

  `status` tinyint(3) not null DEFAULT 1 COMMENT '状态',
    -- 购物车信息
  `cart` text NOT NULL COMMENT '购物车',/*50个sku:80*300，TODO:够么？*/
  
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',

  PRIMARY KEY (`fid`),
  KEY `admin_uid` (`admin_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='财务表';




/*报名表*/
-- DROP TABLE IF EXISTS `apply`;
-- CREATE TABLE `apply` (
--   `apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户ID',

--   `name` varchar(32) NOT NULL DEFAULT '未填写' COMMENT '姓名',
--   `shop_name` varchar(100) not null DEFAULT '' COMMENT '公司名',
--   `industry_name` varchar(500) not null DEFAULT '' COMMENT '行业名',
--   `wechat` varchar(500) not null DEFAULT '' COMMENT '微信号',
--   `mobile` varchar(50) NOT NULL DEFAULT '' COMMENT '手机',
--   `email` varchar(100) NOT NULL DEFAULT '' COMMENT '邮箱',
--   `isDone` tinyint(255) UNSIGNED ZEROFILL NOT NULL COMMENT '是否跟进',

--   `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
--   `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
--   `update_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',

--   PRIMARY KEY (`apply_id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='报名表';

/**
 * 数据库结构.
 *
 * 具体请看源代码<a href="./source-function-DatebaseSQL.datebaseSQL.html">点这里查看</a>
 *
 * @author co8bit <me@co8bit.com>
 * @version 0.2
 * @date    2016-06-07
 */
function datebaseSQL(){};