DROP table IF EXISTS rbac_authitems;
CREATE TABLE `rbac_authitems` (
  `name` varchar(64) NOT NULL COMMENT '权限完整名称，包含mondule,controller,action',
  `module` varchar(50) NOT NULL DEFAULT '' COMMENT '模块儿',
  `controller` varchar(50) NOT NULL DEFAULT '' COMMENT '控制器',
  `action` varchar(45) NOT NULL DEFAULT '' COMMENT '权限标识名-操作',
  `description` varchar(45) NOT NULL DEFAULT '' COMMENT '权限说明',
  `type` enum('operation','data','custom') NOT NULL DEFAULT 'operation' COMMENT '权限类型：操作，数据，自定义',
  `allowed` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为“总是允许”',
  `bizrule` varchar(200) NOT NULL DEFAULT '' COMMENT '权限表达式',
  `data` varchar(200) NOT NULL DEFAULT '' COMMENT '扩展数据',
  PRIMARY KEY (`name`),
  KEY `module` (`module`),
  KEY `controllers` (`controller`),
  KEY `action` (`action`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限表';

DROP table IF EXISTS rbac_authtask;
CREATE TABLE `rbac_authtask` (
  `task_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `task_name` varchar(64) NOT NULL COMMENT '任务名称',
  `task_category_id` int(11) NOT NULL DEFAULT '0' COMMENT '任务分类',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '任务描述',
  `bizrule` varchar(200) NOT NULL DEFAULT '' COMMENT '任务规则（php代码）',
  `data` varchar(200) NOT NULL DEFAULT '' COMMENT '扩展数据',
  PRIMARY KEY (`task_id`),
  UNIQUE KEY `task_name` (`task_name`),
  KEY `category_id` (`task_category_id`)
) ENGINE=InnoDB  AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COMMENT='rbac任务表';

DROP table IF EXISTS rbac_role;
CREATE TABLE `rbac_role` (
  `role_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '角色ID',
  `role_name` varchar(45) NOT NULL DEFAULT '' COMMENT '角色名称',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '角色描述',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '继承的父级角色ID，拥有父级所有权限',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0无效，1有效',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `roler_name_UNIQUE` (`role_name`)
) ENGINE=InnoDB  AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COMMENT='角色表';

DROP table IF EXISTS rbac_role_task;
CREATE TABLE `rbac_role_task` (
  `role_id` int(11) unsigned NOT NULL COMMENT '角色ID',
  `task_id` int(10) unsigned NOT NULL COMMENT '任务ID',
  UNIQUE KEY `role_task` (`role_id`,`task_id`),
  KEY `role_id` (`role_id`),
  KEY `task_id` (`task_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色权限(任务)关系表';

DROP table IF EXISTS rbac_task_category;
CREATE TABLE `rbac_task_category` (
  `task_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '任务分类ID',
  `task_category_name` varchar(50) NOT NULL COMMENT '任务分类名称',
  PRIMARY KEY (`task_category_id`),
  UNIQUE KEY `task_category_name` (`task_category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8 COMMENT='任务分类表';

DROP table IF EXISTS rbac_task_items;
CREATE TABLE `rbac_task_items` (
  `task_id` int(11) unsigned NOT NULL COMMENT '任务ID',
  `authitems_name` varchar(64) NOT NULL DEFAULT '' COMMENT '操作组合名称',
  UNIQUE KEY `task_authitems` (`task_id`,`authitems_name`),
  KEY `task_id` (`task_id`),
  KEY `authitems_name` (`authitems_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='任务授权项表';

DROP table IF EXISTS rbac_user_role;
CREATE TABLE `rbac_user_role` (
  `user_id` int(10) unsigned NOT NULL COMMENT '用户ID',
  `role_id` int(10) unsigned NOT NULL COMMENT '角色ID',
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户角色关系表';

DROP table IF EXISTS bmp_user;
CREATE TABLE `bmp_user` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `email` varchar(50) NOT NULL COMMENT 'Email地址',
  `gender` tinyint(4) NOT NULL DEFAULT '-1' COMMENT '性别：0为女，1为男，-1为保密',
  `password` varchar(64) NOT NULL COMMENT '用户密码（password_hash值）',
  `real_name` varchar(20) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `mobile` varchar(11) NOT NULL DEFAULT '',
  `create_time` int(10) unsigned DEFAULT '0' COMMENT '用户创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10001 DEFAULT CHARSET=utf8 COMMENT='用户表';

