-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `config_global`;
CREATE TABLE `config_global` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `value` varchar(150) NOT NULL,
  `description` text,
  `lastupdate_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastupdate_user` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `config_global` (`id`, `name`, `value`, `description`, `lastupdate_date`, `lastupdate_user`) VALUES
(1,	'USERS_REMOVE_NEW_AFTER_DAYS',	'5',	'Remove new users(that don\'t have email address confirmed) after the selected number of days. ',	'2014-10-22 08:26:11',	1),
(2,	'USERS_REMOVE_DELETED_AFTER_X_DAYS',	'2',	'Remove deleted accounts after X days. It is recommended that they are kept in DB for a while just to avoid accounts deleted by mistake. ',	'2014-10-22 08:46:54',	1),
(3,	'USERS_DEFAULT_TITLE_ID',	'1',	'Default title used for new users. ',	'2014-10-22 08:26:11',	1),
(4,	'USERS_DEFAULT_GROUP_IDS',	'1',	'List of group IDs separated by \",\"',	'2014-10-31 11:55:34',	0),
(5,	'FACEBOOK_APPID',	'',	'Facebook App ID (Used for Login)',	'2014-11-05 10:31:02',	1),
(6,	'FACEBOOK_APPSECRET',	'',	'Facebook App Secret (Used for Login)',	'2014-11-05 10:31:02',	1),
(7,	'GOOGLE_CLIENTID',	'',	'Client ID for Google App',	'2014-11-05 10:28:40',	1),
(8,	'GOOGLE_CLIENTSECRET',	'',	'Client Secret for Google App',	'2014-11-05 10:28:40',	1),
(9,	'GOOGLE_DEVELOPERKEY',	'',	'Used by google login',	'2014-11-05 10:28:40',	1);

DROP TABLE IF EXISTS `config_user`;
CREATE TABLE `config_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `value` varchar(200) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_user_id` (`name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `crontab`;
CREATE TABLE `crontab` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(150) NOT NULL DEFAULT '*',
  `interval` varchar(250) NOT NULL,
  `command` varchar(250) NOT NULL,
  `log` varchar(250) NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL,
  `laststart` timestamp NULL DEFAULT NULL,
  `pid` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `crontab` (`id`, `user`, `interval`, `command`, `log`, `enabled`, `laststart`, `pid`) VALUES
(1,	'*',	'*/60',	'/local/web/dev/libs/demoapp/php/run.php clean users',	'/local/web/dev/libs/demoapp/logs/clean.log',	1,	'2014-10-31 13:05:43',	31266);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(40) NOT NULL,
  `icon` VARCHAR (100) NOT NULL DEFAULT 'default.png',
  `register_date` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `last_login_source` varchar(30) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `new_email` varchar(120) DEFAULT NULL,
  `fb_id` varchar(150) NOT NULL,
  `google_id` varchar(150) NOT NULL,
  `title_id` tinyint(5) unsigned NOT NULL,
  `createdbyadmin_id` int(10) unsigned DEFAULT NULL,
  `joinuser_id` int(10) unsigned DEFAULT NULL,
  `deleteblock_date` timestamp NULL DEFAULT NULL,
  `lastconfirmationmail_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users2groups`;
CREATE TABLE `users2groups` (
  `group_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`group_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users_groups`;
CREATE TABLE `users_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `label` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users_groups` (`id`, `name`, `label`) VALUES
(1,	'PUBLIC_USERS',	'Public Users - Default group for new users - Basic access'),
(2,	'DEVELOPERS',	'Developers - Can Manage User Groups, Cron Jobs & Config Options'),
(3,	'ADMINS',	'Admins - Can manage Config, and edit User Groups Labels'),
(4,	'MODERATORS',	'Moderators - Can Manage Users and User Titles');

DROP TABLE IF EXISTS `users_history`;
CREATE TABLE `users_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `action` smallint(5) unsigned NOT NULL,
  `admin_id` int(10) unsigned DEFAULT NULL,
  `comment` text,
  `ip` varchar(39) DEFAULT NULL COMMENT 'computer ip v4 or v6',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `users_titles`;
CREATE TABLE `users_titles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `auto` tinyint(3) unsigned NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users_titles` (`id`, `auto`, `title`, `description`) VALUES
(1,	1,	'New commer',	'');

-- 2014-11-07 09:31:10
