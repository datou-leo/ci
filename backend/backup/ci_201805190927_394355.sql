/* This file is created by mysqliReback 2018-05-19 09:27:19 */
 /* 创建表结构`ci_admin_act`*/
 DROP TABLE IF EXISTS`ci_admin_act`;/* mysqliReback Separation */ 
 CREATE TABLE `ci_admin_act` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '该动作的标题',
  `type_id` int(11) NOT NULL COMMENT '分类的编号，通常用于前台处理',
  `parent_id` int(11) NOT NULL COMMENT '该动作的直接父类的id',
  `depth` smallint(6) NOT NULL COMMENT '动作的层级深度，从0起',
  `path` varchar(255) NOT NULL COMMENT '层级的排列，以逗号分隔',
  `url` varchar(255) NOT NULL COMMENT '该动作的链接',
  `front_url` varchar(255) NOT NULL,
  `internal` tinyint(4) NOT NULL COMMENT '是否为站内链接？1=是，0=否',
  `ajax` tinyint(4) NOT NULL DEFAULT '0',
  `target` varchar(32) NOT NULL DEFAULT '_self' COMMENT '该链接的弹出方式，默认_self',
  `remark` varchar(255) NOT NULL COMMENT '备注说明',
  `is_use` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否为必启栏目',
  `is_fixed` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否固定栏目',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `timeline` int(11) NOT NULL COMMENT '创建该动作的时间戳',
  `photo` varchar(255) NOT NULL,
  `thumb` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `path_index` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=100015 DEFAULT CHARSET=utf8;/* mysqliReback Separation */
 /* 插入数据`ci_admin_act`*/
 INSERT INTO`ci_admin_act`VALUES('100000','后台首页','100000','0','0','100000','','','1','0','_self','','1','1','1','0','',''),('100001','系统设置','100001','0','0','100001','','','1','0','_self','','0','1','2','0','',''),('100002','后台首页','100002','100000','1','100000,100002','','','1','0','_self','','1','1','3','0','',''),('100003','修改密码','100003','100000','1','100000,100003','password','','1','1','_self','','1','1','4','0','',''),('100004','浏览前台','100004','100000','1','100000,100004','http://local.ci.com/','','0','0','_blank','','1','1','5','0','',''),('100005','安全退出','100005','100000','1','100000,100005','login/delete','','1','0','_self','','1','1','6','0','',''),('100006','关键子设置','100006','100001','1','100001,100006','setup','','1','1','_self','','0','1','7','0','',''),('100007','管理员列表','100007','100001','1','100001,100007','admin_user','','1','0','_self','','0','1','8','0','',''),('100008','后台栏目','100008','100001','1','100001,100008','admin_menu','','1','0','_self','','2','1','9','0','',''),('100009','数据备份','100009','100001','1','100001,100009','backup','','1','1','_self','','0','1','10','0','',''),('100010','文章管理','100010','0','0','100010','','','1','0','_self','','0','0','14','1526113920','',''),('100011','文章分类','100011','100010','1','100010,100011','article_type','','1','0','_self','','0','0','15','1526113958','',''),('100012','文章列表','100012','100010','1','100010,100012','article','','1','0','_self','','0','0','16','1526113973','',''),('100013','关于我们','100013','0','0','100013','','','1','0','_self','','0','0','100013','1526630196','',''),('100014','联系我们','100014','100013','1','100013,100014','single/index/1','','1','1','_self','','0','0','100014','1526630231','','');/* mysqliReback Separation */
 /* 创建表结构`ci_admin_ban`*/
 DROP TABLE IF EXISTS`ci_admin_ban`;/* mysqliReback Separation */ 
 CREATE TABLE `ci_admin_ban` (
  `ip` varchar(50) NOT NULL,
  `ban` tinyint(4) DEFAULT '0' COMMENT '是否禁止登录',
  `reason` varchar(100) DEFAULT '' COMMENT '被禁止理由',
  `attempt` int(11) DEFAULT '0' COMMENT '输入错误次数',
  `timeline` int(11) DEFAULT '0' COMMENT '解封时间戳',
  `timelast` int(11) DEFAULT '0' COMMENT '上次输入的时间戳',
  PRIMARY KEY (`ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* mysqliReback Separation */
 /* 创建表结构`ci_admin_user`*/
 DROP TABLE IF EXISTS`ci_admin_user`;/* mysqliReback Separation */ 
 CREATE TABLE `ci_admin_user` (
  `account` varchar(64) NOT NULL,
  `password` varchar(32) DEFAULT '',
  `role` varchar(64) DEFAULT '',
  `auth` text COMMENT '拥有的权限',
  `shortcut` text COMMENT '我的链接',
  `title` varchar(100) DEFAULT '' COMMENT '该帐号的名称',
  `email` varchar(255) DEFAULT '',
  `qq` varchar(255) DEFAULT '',
  `msn` varchar(255) DEFAULT '',
  `tel` varchar(100) DEFAULT '',
  `timeline` int(11) DEFAULT '0' COMMENT '该帐号的创建时间戳',
  `create_ip` varchar(64) DEFAULT '',
  `login_ip` varchar(64) DEFAULT '',
  `login_count` int(11) DEFAULT '0' COMMENT '登录次数',
  `timelast` int(11) DEFAULT '0',
  PRIMARY KEY (`account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;/* mysqliReback Separation */
 /* 插入数据`ci_admin_user`*/
 INSERT INTO`ci_admin_user`VALUES('administrator','e10adc3949ba59abbe56e057f20f883e','admin','','','','','','','','0','127.0.0.1','127.0.0.1','19','1526691830');/* mysqliReback Separation */
 /* 创建表结构`ci_article`*/
 DROP TABLE IF EXISTS`ci_article`;/* mysqliReback Separation */ 
 CREATE TABLE `ci_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '文章id',
  `sort_id` int(11) DEFAULT '1' COMMENT '排序编号',
  `type_id` int(11) DEFAULT '0' COMMENT '分类',
  `title` varchar(255) DEFAULT '' COMMENT '标题',
  `intro` varchar(1000) DEFAULT '' COMMENT '摘要',
  `content` mediumtext COMMENT '内容',
  `timeline` int(11) DEFAULT '0' COMMENT '发布/修改时间',
  `expire` int(11) DEFAULT '0' COMMENT '公告过期时间',
  `author` varchar(50) DEFAULT '' COMMENT '作者',
  `from` varchar(255) DEFAULT '' COMMENT '摘自',
  `url` varchar(255) DEFAULT '' COMMENT '链接',
  `show` tinyint(4) DEFAULT '1' COMMENT '显隐',
  `top` tinyint(4) DEFAULT '0' COMMENT '置顶',
  `recommend` tinyint(4) DEFAULT '0' COMMENT '推荐',
  `auditing` int(4) DEFAULT '0' COMMENT '审核',
  `photo` varchar(255) DEFAULT '' COMMENT '相关图片',
  `thumb` varchar(255) DEFAULT '' COMMENT '图片缩略图',
  `click` int(11) DEFAULT '0' COMMENT '浏览次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;/* mysqliReback Separation */
 /* 插入数据`ci_article`*/
 INSERT INTO`ci_article`VALUES('1','1','2','第一篇文章标题','','第一篇文章内容','1526290907','0','datou','','','1','0','0','0','/backend/upload/201805/f49e114d70d2a0f6f440181545516b01.jpg','/backend/upload/201805/f49e114d70d2a0f6f440181545516b01_thumb.jpg','0'),('2','2','3','第2篇文章标题','','第2篇文章内容','1526625172','0','datou','','','1','0','0','0','/backend/upload/201805/d5be9452af41ffe715053b5ca0199f5b.jpg','/backend/upload/201805/d5be9452af41ffe715053b5ca0199f5b_thumb.jpg','0');/* mysqliReback Separation */
 /* 创建表结构`ci_article_type`*/
 DROP TABLE IF EXISTS`ci_article_type`;/* mysqliReback Separation */ 
 CREATE TABLE `ci_article_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) DEFAULT '1',
  `parent_id` int(11) DEFAULT '0',
  `depth` int(11) DEFAULT '0',
  `path` varchar(255) DEFAULT '',
  `title` varchar(255) DEFAULT '',
  `intro` varchar(1000) DEFAULT '',
  `photo` varchar(255) DEFAULT '',
  `thumb` varchar(255) DEFAULT '',
  `order_id` int(11) DEFAULT '0',
  `timeline` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `path_index` (`path`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;/* mysqliReback Separation */
 /* 插入数据`ci_article_type`*/
 INSERT INTO`ci_article_type`VALUES('1','1','0','0','1','一级分类1','','','','1','1526288749'),('2','2','1','1','1,2','二级分类1','','','','2','1526288796'),('3','3','1','1','1,3','二级分类2','','','','3','1526288926'),('4','4','1','1','1,4','二级分类3','','','','4','1526288943'),('5','5','0','0','5','一级分类2','','','','5','1526288958'),('6','6','5','1','5,6','二级分类1','','','','6','1526288973'),('7','7','5','1','5,7','二级分类2','','','','7','1526288986'),('8','8','5','1','5,8','二级分类3','','','','8','1526289004');/* mysqliReback Separation */
 /* 创建表结构`ci_config`*/
 DROP TABLE IF EXISTS`ci_config`;/* mysqliReback Separation */ 
 CREATE TABLE `ci_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) DEFAULT '' COMMENT '配置分类，方便批量读取',
  `config` varchar(100) DEFAULT '',
  `value` text,
  PRIMARY KEY (`id`),
  KEY `config_index` (`config`,`category`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;/* mysqliReback Separation */
 /* 插入数据`ci_config`*/
 INSERT INTO`ci_config`VALUES('1','admin','backup','1526471604'),('2','site','title','大头'),('3','site','keywords','大头'),('4','site','description','大头');/* mysqliReback Separation */
 /* 创建表结构`ci_single`*/
 DROP TABLE IF EXISTS`ci_single`;/* mysqliReback Separation */ 
 CREATE TABLE `ci_single` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一编号',
  `type_id` int(11) DEFAULT NULL COMMENT '类型',
  `title` varchar(255) DEFAULT NULL COMMENT '标题',
  `intro` varchar(1000) DEFAULT NULL COMMENT '简介',
  `content` mediumtext COMMENT '内容',
  `timeline` int(11) DEFAULT NULL COMMENT '发布/修改时间',
  `photo` varchar(255) DEFAULT NULL,
  `thumb` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;/* mysqliReback Separation */
 /* 插入数据`ci_single`*/
 INSERT INTO`ci_single`VALUES('1','','','','<ul style=\"white-space:normal;\">
	<li>
		昵称:datou
	</li>
	<li>
		qq:2323178881
	</li>
	<li>
		Tel:18329123270
	</li>
	<li>
		微信:datou-leo
	</li>
	<li>
		ci使用开发群:646864389
	</li>
</ul>','','','');/* mysqliReback Separation */