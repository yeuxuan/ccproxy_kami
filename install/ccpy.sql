-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2022-06-25 20:39:37
-- 服务器版本： 5.7.26
-- PHP 版本： 7.3.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `ccpy`
--

-- --------------------------------------------------------

--
-- 表的结构 `application`
--

CREATE TABLE `application` (
  `appid` int(11) NOT NULL,
  `appcode` varchar(255) NOT NULL COMMENT 'appcode',
  `appname` varchar(255) NOT NULL,
  `serverip` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL COMMENT '属于user',
  `found_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `application`
--

INSERT INTO `application` (`appid`, `appcode`, `appname`, `serverip`, `username`, `found_time`) VALUES
(1, 'ba67539107451724c557265cf0d7d732', '一花私端', '124.554.42.168', 'admin', '2022-02-15 17:58:15'),
(2, '08540f6997903c5c7ec7b5998839bc28', '一花公端', '106.255.160.255', 'admin', '2022-02-14 01:22:47');

-- --------------------------------------------------------

--
-- 表的结构 `daili`
--

CREATE TABLE `daili` (
  `did` int(11) NOT NULL COMMENT '代理ID',
  `username` varchar(255) NOT NULL COMMENT '用户名',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `cookies` varchar(255) NOT NULL COMMENT ' 登录会话'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `kami`
--

CREATE TABLE `kami` (
  `id` int(15) NOT NULL COMMENT '编号',
  `kami` varchar(128) NOT NULL COMMENT '卡密',
  `times` varchar(20) NOT NULL COMMENT '时长',
  `comment` varchar(20) NOT NULL COMMENT '备注',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `host` varchar(255) NOT NULL COMMENT '站点',
  `sc_user` varchar(20) NOT NULL COMMENT '生成用户',
  `state` int(1) NOT NULL DEFAULT '0' COMMENT '状态:0=未使用,1=已使用',
  `use_date` timestamp NULL DEFAULT NULL COMMENT '使用时间',
  `username` varchar(25) DEFAULT NULL COMMENT '使用账号',
  `app` varchar(255) NOT NULL COMMENT '使用app软件',
  `end_date` timestamp NULL DEFAULT NULL COMMENT '到期时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='注册卡密';

-- --------------------------------------------------------

--
-- 表的结构 `log`
--

CREATE TABLE `log` (
  `logid` int(11) NOT NULL,
  `operation` varchar(255) NOT NULL,
  `msg` varchar(255) NOT NULL,
  `operationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `operationer` varchar(255) NOT NULL COMMENT '操作人',
  `ip` varchar(255) NOT NULL COMMENT 'ip'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `order_list`
--

CREATE TABLE `order_list` (
  `id` int(15) NOT NULL COMMENT '编号',
  `order_id` varchar(40) NOT NULL COMMENT '订单号',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '支付类型:1=微信,2=支付宝',
  `price` varchar(20) NOT NULL DEFAULT '' COMMENT '订单价格',
  `comment` varchar(100) NOT NULL COMMENT '备注信息',
  `state` int(1) NOT NULL COMMENT '支付状态:0=未支付,1=已支付,2=异常',
  `complete` int(1) NOT NULL COMMENT '是否完结:0=未完结,1=已完结',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `pay_date` timestamp NULL DEFAULT NULL,
  `username` varchar(20) NOT NULL COMMENT '开通账户',
  `password` varchar(20) NOT NULL COMMENT '开通密码',
  `code` varchar(60) NOT NULL COMMENT '用户码',
  `tel` varchar(20) NOT NULL COMMENT '电话号码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单列表';

-- --------------------------------------------------------

--
-- 表的结构 `server_list`
--

CREATE TABLE `server_list` (
  `id` int(15) NOT NULL COMMENT '编号',
  `ip` varchar(60) NOT NULL COMMENT '服务器ip',
  `serveruser` varchar(40) NOT NULL COMMENT 'ccproxy登录账号',
  `password` varchar(40) NOT NULL COMMENT 'ccproxy登录密码',
  `state` int(1) NOT NULL DEFAULT '1' COMMENT '是否可用:0=不可用,1=可用',
  `comment` varchar(200) NOT NULL COMMENT '备注',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `certificate` varchar(200) DEFAULT NULL COMMENT '证书地址',
  `cport` int(5) NOT NULL COMMENT 'CCProxy端口',
  `username` varchar(255) NOT NULL COMMENT '所属账号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='服务器列表';

--
-- 转存表中的数据 `server_list`
--

INSERT INTO `server_list` (`id`, `ip`, `serveruser`, `password`, `state`, `comment`, `found_date`, `certificate`, `cport`, `username`) VALUES
(1, '124.223.42.168', 'admin', 'xxxxxx', 1, '一号服务器', '2022-01-13 03:54:12', 'NULL', 8981, 'admin'),
(19, '106.225.160.255', 'admin', 'xxxxxxx', 1, '二号服务器', '2022-03-19 07:24:10', NULL, 8981, 'admin');

-- --------------------------------------------------------

--
-- 表的结构 `sub_admin`
--

CREATE TABLE `sub_admin` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(20) NOT NULL COMMENT '密码',
  `hostname` varchar(20) NOT NULL COMMENT '网站标题',
  `cookies` varchar(255) NOT NULL COMMENT ' 登录会话',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `over_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
  `siteurl` varchar(255) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT '1' COMMENT '站点违规',
  `pan` varchar(255) NOT NULL COMMENT '网盘',
  `wzgg` text NOT NULL COMMENT '网站公告',
  `kf` varchar(255) NOT NULL COMMENT '客服',
  `img` varchar(255) NOT NULL COMMENT '图片',
  `ggswitch` int(1) NOT NULL COMMENT '公告开关',
  `qx` int(1) NOT NULL COMMENT '权限'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='普通管理员';

--
-- 转存表中的数据 `sub_admin`
--

INSERT INTO `sub_admin` (`id`, `username`, `password`, `hostname`, `cookies`, `found_date`, `over_date`, `siteurl`, `state`, `pan`, `wzgg`, `kf`, `img`, `ggswitch`, `qx`) VALUES
(1, 'admin', '123456', '一花端口', 'e9deHYQTskz/JHy01TiC4aIKufxa4EX18a2c6mGJkTMmggP0+1FLxDByATyLhX9zqVFxmOiMqKKEpQOGJYOTOk3cJQ', '2022-01-12 11:24:34', '2023-03-12 16:00:00', '', 1, 'https://www.keaidian.com/uploads/allimg/190424/24110307_5.jpg', '测试公告<br>公告<br>公告<br><div style=\"color:red\"><span>测试公告</span></div>123', 'http://wpa.qq.com/msgrd?v=3&uin=487735913&site=qq&menu=yes', 'https://www.keaidian.com/uploads/allimg/190424/24110307_5.jpg', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `sup_admin`
--

CREATE TABLE `sup_admin` (
  `id` int(15) NOT NULL COMMENT '编号',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `password` varchar(20) NOT NULL COMMENT '登录密码',
  `cookies` varchar(225) NOT NULL COMMENT '登录会话',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='超级管理员';

--
-- 转存表中的数据 `sup_admin`
--

INSERT INTO `sup_admin` (`id`, `username`, `password`, `cookies`, `found_date`) VALUES
(1, 'admin', '123456', '1dcdGKifpWhmV5agAZ1qzjUSKKHf0JSvcFiVPlZY58TO4nKt0YI6wfjm9nhBbD4tjtucDd+x36Oja1b0E04AecjM+Q', '2022-01-27 16:00:00');

--
-- 转储表的索引
--

--
-- 表的索引 `application`
--
ALTER TABLE `application`
  ADD PRIMARY KEY (`appid`),
  ADD UNIQUE KEY `appcode` (`appcode`);

--
-- 表的索引 `daili`
--
ALTER TABLE `daili`
  ADD PRIMARY KEY (`did`);

--
-- 表的索引 `kami`
--
ALTER TABLE `kami`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kami` (`kami`);

--
-- 表的索引 `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`logid`);

--
-- 表的索引 `order_list`
--
ALTER TABLE `order_list`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `server_list`
--
ALTER TABLE `server_list`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip` (`ip`);

--
-- 表的索引 `sub_admin`
--
ALTER TABLE `sub_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `id` (`id`);

--
-- 表的索引 `sup_admin`
--
ALTER TABLE `sup_admin`
  ADD PRIMARY KEY (`id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `application`
--
ALTER TABLE `application`
  MODIFY `appid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56512;

--
-- 使用表AUTO_INCREMENT `daili`
--
ALTER TABLE `daili`
  MODIFY `did` int(11) NOT NULL AUTO_INCREMENT COMMENT '代理ID';

--
-- 使用表AUTO_INCREMENT `kami`
--
ALTER TABLE `kami`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号', AUTO_INCREMENT=72498;

--
-- 使用表AUTO_INCREMENT `log`
--
ALTER TABLE `log`
  MODIFY `logid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- 使用表AUTO_INCREMENT `order_list`
--
ALTER TABLE `order_list`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号';

--
-- 使用表AUTO_INCREMENT `server_list`
--
ALTER TABLE `server_list`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号', AUTO_INCREMENT=20;

--
-- 使用表AUTO_INCREMENT `sub_admin`
--
ALTER TABLE `sub_admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=61;

--
-- 使用表AUTO_INCREMENT `sup_admin`
--
ALTER TABLE `sup_admin`
  MODIFY `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号', AUTO_INCREMENT=2;
COMMIT;