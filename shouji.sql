-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `shouji`;

DROP TABLE IF EXISTS `guestbook`;
CREATE TABLE `guestbook` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '姓名',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `contents` text NOT NULL COMMENT '留言内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='反馈表';


DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orders` int(11) NOT NULL COMMENT '交易号',
  `num` int(5) NOT NULL COMMENT '编号',
  `user` varchar(20) NOT NULL COMMENT '操作者',
  `time` int(11) NOT NULL COMMENT '操作时间',
  `type` int(1) NOT NULL COMMENT '交易类型（1、入库;2、借用;3、归还;4、返修:5、退货;6、处理）',
  `ip` varchar(15) NOT NULL COMMENT '操作者IP',
  `status` int(2) NOT NULL COMMENT '状态（0、交易失败;1、交易成功）',
  `remark` varchar(255) NOT NULL COMMENT '备注说明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='历史记录表';


DROP TABLE IF EXISTS `mobile`;
CREATE TABLE `mobile` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `num` int(5) NOT NULL COMMENT '手机编号',
  `pinpai` varchar(50) NOT NULL COMMENT '品牌',
  `model` varchar(100) NOT NULL COMMENT '型号',
  `imei` varchar(200) NOT NULL COMMENT 'IMEI串号',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `peijian` varchar(200) NOT NULL COMMENT '附属配件',
  `user` varchar(20) NOT NULL COMMENT '借用者',
  `buy` varchar(30) NOT NULL COMMENT '购买渠道（用于区别华强北）',
  `buy_time` date NOT NULL COMMENT '入库日期',
  `status` int(2) NOT NULL COMMENT '0未知,1正常,2返修,3退货,4报废',
  `system` varchar(100) NOT NULL COMMENT '系统版本',
  `ram` varchar(10) NOT NULL COMMENT '运行内存',
  `rom` varchar(10) NOT NULL COMMENT '机身存储',
  `screen` varchar(10) NOT NULL COMMENT '屏幕尺寸',
  `pixel` varchar(20) NOT NULL COMMENT '分辨率',
  `lte` varchar(30) NOT NULL COMMENT '网络制式',
  `remark` varchar(255) NOT NULL COMMENT '备注说明',
  PRIMARY KEY (`id`),
  UNIQUE KEY `num` (`num`),
  KEY `pinpai` (`pinpai`),
  KEY `model` (`model`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机信息表';


DROP TABLE IF EXISTS `mobile_loss`;
CREATE TABLE `mobile_loss` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `num` int(5) NOT NULL COMMENT '手机编号',
  `pinpai` varchar(50) NOT NULL COMMENT '品牌',
  `model` varchar(100) NOT NULL COMMENT '型号',
  `imei` varchar(200) NOT NULL COMMENT 'IMEI串号',
  `color` varchar(20) NOT NULL COMMENT '颜色',
  `peijian` varchar(200) NOT NULL COMMENT '附属配件',
  `user` varchar(20) NOT NULL COMMENT '借用者',
  `buy` varchar(30) NOT NULL COMMENT '购买渠道（用于区别华强北）',
  `buy_time` date NOT NULL COMMENT '入库日期',
  `local` varchar(20) NOT NULL COMMENT '盘点仓',
  `status` int(2) NOT NULL COMMENT '0未知,1正常,2返修,3退货,4报废',
  `remark` varchar(255) NOT NULL COMMENT '备注说明',
  `qingdian` varchar(10) NOT NULL COMMENT '是否清点',
  PRIMARY KEY (`id`),
  UNIQUE KEY `num` (`num`),
  KEY `pinpai` (`pinpai`),
  KEY `model` (`model`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='不明手机信息';

INSERT INTO `mobile_loss` (`id`, `num`, `pinpai`, `model`, `imei`, `color`, `peijian`, `user`, `buy`, `buy_time`, `local`, `status`, `remark`, `qingdian`) VALUES
(253,	63,	'联想',	'A208t',	'860793028449196 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(254,	65,	'三星',	'GALAXY Trend Ⅱ（SCH-I739）',	'',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(255,	66,	'三星',	'GALAXY Win G3（GT-I8558）',	'356524053539788 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(256,	68,	'三星',	'GALAXYS Trend G3（GT-S7568I）',	'358494052083915 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(257,	69,	'三星',	'GALAXY Style DUOS（GT-I8262D）',	'359536050361215 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(258,	72,	'三星',	'GALAXYSⅡDous（SCH-i929）',	'',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(259,	74,	'小米',	'MI3C',	'865009022687318 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(260,	78,	'联想',	'A390t',	'862576023132356 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'6.30日入库',	'否\r\n'),
(261,	79,	'联想',	'A208t',	'860793025283127 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'6.30日入库',	'否\r\n'),
(262,	83,	'酷派',	'Coolpad 5892',	'864097020533783 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'2014.9.5日菜刀入库（无电池）',	'否\r\n'),
(292,	140,	'三星',	'SM-G3508I',	'359787/05/221614/9',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(346,	256,	'三星',	'GALACY S4(GT-I9500)',	'357747057932464 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(368,	289,	'华为',	'Che2-TL00(1GB)',	'865647022759783 ',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n'),
(369,	290,	'小米',	'小米手机4电信4G版16GB',	'865932020765705 ',	'',	'',	'不明',	'华强北',	'0000-00-00',	'ROM仓',	0,	'华强北给的（2040元）',	'否\r\n'),
(409,	356,	'三星',	'SM-A3000',	'355698065563340 ',	'',	'',	'不明',	'华强北',	'0000-00-00',	'ROM仓',	0,	'5.11华强北1465元',	'否\r\n'),
(410,	357,	'三星',	'SM-A3009',	'355595061486908 ',	'',	'',	'不明',	'华强北',	'0000-00-00',	'ROM仓',	0,	'5.11华强北1445元',	'否\r\n'),
(411,	358,	'三星',	'SM-A3009',	'355595061493235 ',	'',	'',	'不明',	'华强北',	'0000-00-00',	'ROM仓',	0,	'5.11华强北1445元',	'否\r\n'),
(487,	488,	'三星',	'SM-N9002',	'357555052681576/357555052681574',	'',	'',	'不明',	'',	'0000-00-00',	'ROM仓',	0,	'',	'否\r\n');

DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `month` varchar(7) NOT NULL COMMENT '年月份',
  `income` int(4) NOT NULL COMMENT '入库（收）',
  `send` int(4) NOT NULL COMMENT '报废处理（发）',
  `store` int(4) NOT NULL COMMENT '结存',
  `return` int(4) NOT NULL COMMENT '退货',
  `repair` int(4) NOT NULL COMMENT '送修',
  `create_time` int(10) NOT NULL COMMENT '结算时间',
  UNIQUE KEY `month` (`month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='财务收发存结算表';


DROP TABLE IF EXISTS `session`;
CREATE TABLE `session` (
  `session_id` varchar(255) NOT NULL,
  `session_expire` int(11) NOT NULL,
  `session_data` blob,
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户唯一标识',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `nickname` varchar(32) NOT NULL COMMENT '昵称',
  `email` varchar(150) NOT NULL COMMENT '邮箱',
  `role` int(2) NOT NULL COMMENT '用户角色(0为游客，1为普通用户，2为管理员)',
  `status` int(2) NOT NULL COMMENT '用户状态',
  `remark` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';


-- 2016-03-04 09:43:03
