CREATE TABLE `pre_doc_member` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL COMMENT '用户id',
  `name` varchar(60) DEFAULT '' COMMENT '用户标识',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='用户信息';

CREATE TABLE `pre_doc` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) DEFAULT '' COMMENT '文档标题',
  `name` varchar(60) DEFAULT '' COMMENT '文档标识',
  `author` int(10) unsigned NOT NULL COMMENT '文档作者ID',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='文档表';

CREATE TABLE `pre_doc_intro` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `did` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文档id',
  `intro` varchar(60) DEFAULT '' COMMENT '简介内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='简介表';

CREATE TABLE `pre_doc_catalog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `did` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文档id',
  `pid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '父id',
  `title` varchar(60) DEFAULT '' COMMENT '标题',
  `file` varchar(120) DEFAULT '' COMMENT '文件路径',
  `sort` tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '0未启用;1已启用',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `update_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='目录表';

CREATE TABLE `pre_doc_detail` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `did` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '文档id',
  `cid` int(11) unsigned NOT NULL DEFAULT 0 COMMENT '目录id',
  `content` text COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='内容表';