/*
Navicat MySQL Data Transfer

Source Server         : myci
Source Server Version : 50628
Source Host           : 119.29.98.41:3306
Source Database       : finance

Target Server Type    : MYSQL
Target Server Version : 50628
File Encoding         : 65001

Date: 2017-07-28 10:24:07
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for admin_job
-- ----------------------------
DROP TABLE IF EXISTS `admin_job`;
CREATE TABLE `admin_job` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `job_name` varchar(36) DEFAULT NULL,
  `explain` varchar(240) DEFAULT NULL,
  `vaild` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_job
-- ----------------------------
INSERT INTO `admin_job` VALUES ('1', '超级管理员', '超级管理员', '1');
INSERT INTO `admin_job` VALUES ('3', '管理员', '管理员', '1');

-- ----------------------------
-- Table structure for admin_job_auth
-- ----------------------------
DROP TABLE IF EXISTS `admin_job_auth`;
CREATE TABLE `admin_job_auth` (
  `admin_job_id` int(8) NOT NULL,
  `func_key` varchar(24) NOT NULL,
  `auth_key` varchar(24) NOT NULL,
  PRIMARY KEY (`admin_job_id`,`func_key`,`auth_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of admin_job_auth
-- ----------------------------
INSERT INTO `admin_job_auth` VALUES ('1', 'adminjob', 'add');
INSERT INTO `admin_job_auth` VALUES ('1', 'adminjob', 'delete');
INSERT INTO `admin_job_auth` VALUES ('1', 'adminjob', 'edit');
INSERT INTO `admin_job_auth` VALUES ('1', 'adminjob', 'export');
INSERT INTO `admin_job_auth` VALUES ('1', 'func', 'add');
INSERT INTO `admin_job_auth` VALUES ('1', 'func', 'delete');
INSERT INTO `admin_job_auth` VALUES ('1', 'func', 'edit');
INSERT INTO `admin_job_auth` VALUES ('1', 'func', 'export');
INSERT INTO `admin_job_auth` VALUES ('1', 'menu', 'add');
INSERT INTO `admin_job_auth` VALUES ('1', 'menu', 'delete');
INSERT INTO `admin_job_auth` VALUES ('1', 'menu', 'edit');
INSERT INTO `admin_job_auth` VALUES ('1', 'menu', 'export');
INSERT INTO `admin_job_auth` VALUES ('1', 'staff', 'add');
INSERT INTO `admin_job_auth` VALUES ('1', 'staff', 'delete');
INSERT INTO `admin_job_auth` VALUES ('1', 'staff', 'edit');
INSERT INTO `admin_job_auth` VALUES ('1', 'staff', 'export');
INSERT INTO `admin_job_auth` VALUES ('1', 'system', 'export');
INSERT INTO `admin_job_auth` VALUES ('3', 'adminjob', 'add');
INSERT INTO `admin_job_auth` VALUES ('3', 'adminjob', 'export');
INSERT INTO `admin_job_auth` VALUES ('3', 'func', 'delete');
INSERT INTO `admin_job_auth` VALUES ('3', 'func', 'edit');
INSERT INTO `admin_job_auth` VALUES ('3', 'func', 'export');
INSERT INTO `admin_job_auth` VALUES ('3', 'menu', 'edit');
INSERT INTO `admin_job_auth` VALUES ('3', 'menu', 'export');
INSERT INTO `admin_job_auth` VALUES ('3', 'staff', 'edit');
INSERT INTO `admin_job_auth` VALUES ('3', 'staff', 'export');
INSERT INTO `admin_job_auth` VALUES ('3', 'system', 'export');

-- ----------------------------
-- Table structure for background_func
-- ----------------------------
DROP TABLE IF EXISTS `background_func`;
CREATE TABLE `background_func` (
  `key` varchar(24) NOT NULL,
  `func_name` varchar(24) DEFAULT NULL,
  `background_url_key` int(6) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of background_func
-- ----------------------------
INSERT INTO `background_func` VALUES ('adminjob', '管理员', '0');
INSERT INTO `background_func` VALUES ('func', '功能管理', '0');
INSERT INTO `background_func` VALUES ('menu', '菜单管理', '0');
INSERT INTO `background_func` VALUES ('staff', '职位管理', '0');
INSERT INTO `background_func` VALUES ('system', '后台管理', '0');

-- ----------------------------
-- Table structure for finance_menu
-- ----------------------------
DROP TABLE IF EXISTS `finance_menu`;
CREATE TABLE `finance_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `named` varchar(36) DEFAULT NULL,
  `icon` varchar(120) DEFAULT NULL,
  `url` varchar(120) DEFAULT NULL,
  `open_type` varchar(1) DEFAULT '1' COMMENT '1、主窗口\r\n2、弹出窗口',
  `sort` int(3) DEFAULT '100',
  `level` int(2) DEFAULT '1',
  `parent` int(11) DEFAULT '0',
  `showed` int(1) DEFAULT '1',
  `screen_auth` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of finance_menu
-- ----------------------------
INSERT INTO `finance_menu` VALUES ('5', '后台管理', '', 'finance/admin/menu', '1', '100', '1', '0', '1', '{\"system\":[\"export\"]}');
INSERT INTO `finance_menu` VALUES ('6', '菜单管理', '', 'finance/admin/menu', '1', '100', '2', '5', '1', '{\"menu\":[\"export\"]}');
INSERT INTO `finance_menu` VALUES ('13', '功能管理', '', 'finance/admin/func', '1', '100', '2', '5', '1', '{\"func\":[\"export\"]}');
INSERT INTO `finance_menu` VALUES ('14', '管理员', '', 'finance/admin/adminjob', '1', '100', '2', '5', '1', '{\"adminjob\":[\"export\"]}');
INSERT INTO `finance_menu` VALUES ('15', '职位', '', 'finance/admin/staff', '1', '100', '2', '5', '1', '{\"staff\":[\"export\"]}');

-- ----------------------------
-- Table structure for func_auth
-- ----------------------------
DROP TABLE IF EXISTS `func_auth`;
CREATE TABLE `func_auth` (
  `func_key` varchar(24) NOT NULL,
  `key` varchar(24) NOT NULL,
  `auth_name` varchar(24) DEFAULT NULL,
  PRIMARY KEY (`func_key`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of func_auth
-- ----------------------------
INSERT INTO `func_auth` VALUES ('adminjob', 'add', '添加');
INSERT INTO `func_auth` VALUES ('adminjob', 'delete', '删除');
INSERT INTO `func_auth` VALUES ('adminjob', 'edit', '编辑');
INSERT INTO `func_auth` VALUES ('func', 'add', '添加');
INSERT INTO `func_auth` VALUES ('func', 'delete', '删除');
INSERT INTO `func_auth` VALUES ('func', 'edit', '编辑');
INSERT INTO `func_auth` VALUES ('menu', 'add', '添加');
INSERT INTO `func_auth` VALUES ('menu', 'delete', '删除');
INSERT INTO `func_auth` VALUES ('menu', 'edit', '编辑');
INSERT INTO `func_auth` VALUES ('staff', 'add', '添加');
INSERT INTO `func_auth` VALUES ('staff', 'delete', '删除');
INSERT INTO `func_auth` VALUES ('staff', 'edit', '编辑');

-- ----------------------------
-- Table structure for staff
-- ----------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(24) NOT NULL,
  `true_name` varchar(24) DEFAULT NULL,
  `sex` varchar(9) DEFAULT NULL,
  `header_img` varchar(120) DEFAULT NULL,
  `staff_num` varchar(16) DEFAULT NULL,
  `study_his` varchar(24) DEFAULT NULL,
  `store_id` varchar(24) DEFAULT '0',
  `pwd` varchar(64) NOT NULL,
  `job` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of staff
-- ----------------------------
INSERT INTO `staff` VALUES ('1', 'admin', '管理员', '男', '/resources/upload/2017-02-13/1486947527396580.jpg', '0012', null, '0#1', '96e79218965eb72c92a549dd5a330112', '[\"1\"]');
INSERT INTO `staff` VALUES ('3', 'test', '管理员', '男', '/resources/upload/2017-04-18/1492484785125368.jpg', '002', null, '0', '96e79218965eb72c92a549dd5a330112', '[\"3\"]');

-- ----------------------------
-- Table structure for staff_job
-- ----------------------------
DROP TABLE IF EXISTS `staff_job`;
CREATE TABLE `staff_job` (
  `staff_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  PRIMARY KEY (`staff_id`,`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of staff_job
-- ----------------------------
INSERT INTO `staff_job` VALUES ('1', '1');
INSERT INTO `staff_job` VALUES ('3', '3');
