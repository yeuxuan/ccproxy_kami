/*
 Navicat Premium Data Transfer

 Source Server         : root
 Source Server Type    : MySQL
 Source Server Version : 50726 (5.7.26)
 Source Host           : localhost:3306
 Source Schema         : ccpy

 Target Server Type    : MySQL
 Target Server Version : 50726 (5.7.26)
 File Encoding         : 65001

 Date: 29/10/2022 23:42:37
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for app_server
-- ----------------------------
DROP TABLE IF EXISTS `app_server`;
CREATE TABLE `app_server`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `serverip` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of app_server
-- ----------------------------

-- ----------------------------
-- Table structure for application
-- ----------------------------
DROP TABLE IF EXISTS `application`;
CREATE TABLE `application`  (
  `appid` int(11) NOT NULL AUTO_INCREMENT,
  `appcode` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'appcode',
  `appname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `serverip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '属于user',
  `found_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`appid`) USING BTREE,
  UNIQUE INDEX `appcode`(`appcode`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 56544 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of application
-- ----------------------------

-- ----------------------------
-- Table structure for daili
-- ----------------------------
DROP TABLE IF EXISTS `daili`;
CREATE TABLE `daili`  (
  `did` int(11) NOT NULL AUTO_INCREMENT COMMENT '代理ID',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `cookies` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT ' 登录会话',
  PRIMARY KEY (`did`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of daili
-- ----------------------------

-- ----------------------------
-- Table structure for kami
-- ----------------------------
DROP TABLE IF EXISTS `kami`;
CREATE TABLE `kami`  (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `kami` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '卡密',
  `times` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '时长',
  `comment` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `host` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '站点',
  `sc_user` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '生成用户',
  `state` int(1) NOT NULL DEFAULT 0 COMMENT '状态:0=未使用,1=已使用',
  `use_date` timestamp NULL DEFAULT NULL COMMENT '使用时间',
  `username` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '使用账号',
  `app` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '使用app软件',
  `end_date` timestamp NULL DEFAULT NULL COMMENT '到期时间',
  `ext` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '拓展参数',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `kami`(`kami`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '注册卡密' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of kami
-- ----------------------------

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`  (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `operation` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `msg` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `operationdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `operationer` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '操作人',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ip',
  PRIMARY KEY (`logid`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 250 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of log
-- ----------------------------

-- ----------------------------
-- Table structure for order_list
-- ----------------------------
DROP TABLE IF EXISTS `order_list`;
CREATE TABLE `order_list`  (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `order_id` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '订单号',
  `type` int(1) NOT NULL DEFAULT 0 COMMENT '支付类型:1=微信,2=支付宝',
  `price` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '订单价格',
  `comment` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注信息',
  `state` int(1) NOT NULL COMMENT '支付状态:0=未支付,1=已支付,2=异常',
  `complete` int(1) NOT NULL COMMENT '是否完结:0=未完结,1=已完结',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `pay_date` timestamp NULL DEFAULT NULL,
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '开通账户',
  `password` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '开通密码',
  `code` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户码',
  `tel` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '电话号码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '订单列表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of order_list
-- ----------------------------

-- ----------------------------
-- Table structure for server_list
-- ----------------------------
DROP TABLE IF EXISTS `server_list`;
CREATE TABLE `server_list`  (
  `id` int(15) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `ip` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '服务器ip',
  `serveruser` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ccproxy登录账号',
  `password` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'ccproxy登录密码',
  `state` int(1) NOT NULL DEFAULT 1 COMMENT '是否可用:0=不可用,1=可用',
  `comment` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '备注',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `certificate` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '证书地址',
  `cport` int(5) NOT NULL COMMENT 'CCProxy端口',
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '所属账号',
  `applist` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '应用大全',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `ip`(`ip`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 20 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '服务器列表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of server_list
-- ----------------------------

-- ----------------------------
-- Table structure for sub_admin
-- ----------------------------
DROP TABLE IF EXISTS `sub_admin`;
CREATE TABLE `sub_admin`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `password` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `hostname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '网站标题',
  `cookies` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT ' 登录会话',
  `found_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `over_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '到期时间',
  `siteurl` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1 COMMENT '站点违规',
  `pan` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '网盘',
  `wzgg` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '网站公告',
  `kf` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客服',
  `img` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '图片',
  `ggswitch` int(1) NOT NULL COMMENT '公告开关',
  `qx` int(1) NOT NULL COMMENT '权限',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `username`(`username`) USING BTREE,
  INDEX `id`(`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 61 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '普通管理员' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sub_admin
-- ----------------------------
INSERT INTO `sub_admin` VALUES (1, 'admin', '123456', '一花端口', '83275ievSUMhd+lxqyaiHaByaNh/AsEdw9iOFmBCD9n2L5GjdwJc43tia/no84+p81W8Q0ChfigYCObiN9jLuZcwxg', '2022-01-12 19:24:34', '2023-03-13 00:00:00', 'localhost:7788', 1, './assets/img/bj.jpg', '注意<br>PHP版本最好设置为7.3<br>不然容易出现问题<br><div style=\"color:red\"><span>我是公告</span></div>我是公告', 'http://wpa.qq.com/msgrd?v=3&uin=487735913&site=qq&menu=yes', 'https://tse1-mm.cn.bing.net/th/id/OIP-C.tV58EvgJSzLG3iOTLzB85QHaEo?pid=ImgDet&rs=1', 1, 0);

SET FOREIGN_KEY_CHECKS = 1;