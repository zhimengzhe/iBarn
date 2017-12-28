SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `collection`;
CREATE TABLE `collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `sid` int(11) NOT NULL DEFAULT '0',
  `mapId` int(11) NOT NULL DEFAULT '0',
  `location` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `isdir` tinyint(1) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `collectTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `source` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT 'os',
  PRIMARY KEY (`id`),
  KEY `mapId` (`mapId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `fileinfo`;
CREATE TABLE `fileinfo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(64) NOT NULL DEFAULT '',
  `mime` varchar(80) NOT NULL DEFAULT '',
  `location` varchar(200) NOT NULL DEFAULT '',
  `md5` varchar(64) NOT NULL DEFAULT '',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `location` (`location`) USING BTREE,
  UNIQUE KEY `hash` (`hash`) USING BTREE,
  KEY `size` (`size`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `filemap`;
CREATE TABLE `filemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pid` int(11) NOT NULL DEFAULT '0',
  `path` varchar(2000) COLLATE utf8_bin NOT NULL DEFAULT '',
  `location` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `isdir` tinyint(1) NOT NULL DEFAULT '0',
  `mime` varchar(80) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `origin` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `class` tinyint(3) NOT NULL DEFAULT '0',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  KEY `path` (`path`(255)) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `location` (`location`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `recycle`;
CREATE TABLE `recycle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mapId` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '',
  `pid` int(11) NOT NULL DEFAULT '0',
  `path` varchar(2000) COLLATE utf8_bin NOT NULL DEFAULT '',
  `location` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `isdir` tinyint(1) NOT NULL DEFAULT '0',
  `mime` varchar(80) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `origin` varchar(10) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `path` (`path`(255)) USING BTREE,
  KEY `pid` (`pid`) USING BTREE,
  KEY `location` (`location`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

DROP TABLE IF EXISTS `share`;
CREATE TABLE `share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `type` varchar(1) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `sid` int(11) NOT NULL DEFAULT '0',
  `mapId` int(11) NOT NULL DEFAULT '0',
  `isdir` tinyint(1) NOT NULL DEFAULT '0',
  `size` int(11) NOT NULL DEFAULT '0',
  `view` int(11) NOT NULL DEFAULT '0',
  `down` int(11) NOT NULL DEFAULT '0',
  `saveNum` int(11) NOT NULL DEFAULT '0',
  `pwd` varchar(8) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `createdTime` datetime NOT NULL,
  `shareTime` datetime NOT NULL,
  `overTime` datetime NOT NULL,
  `price` int(11) NOT NULL DEFAULT '0',
  `source` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `mapId` (`mapId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '',
  `email` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `password` char(32) COLLATE utf8_bin NOT NULL DEFAULT '',
  `avatar` varchar(250) COLLATE utf8_bin NOT NULL DEFAULT '',
  `fileNum` int(11) NOT NULL DEFAULT '0',
  `size` bigint(20) NOT NULL DEFAULT '0',
  `capacity` int(11) NOT NULL DEFAULT '0',
  `role` tinyint(1) NOT NULL DEFAULT '0',
  `token` varchar(45) COLLATE utf8_bin NOT NULL DEFAULT '',
  `time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastLoginTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=COMPACT;