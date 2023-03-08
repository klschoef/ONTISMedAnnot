drop database if exists DB_NAME;
create database DB_NAME;
use DB_NAME;
drop user if exists DB_NAME;
create user 'DB_NAME' identified by 'DB_PASS';
grant usage on *.* to 'DB_NAME'@'localhost' identified by 'DB_PASS';
grant all privileges on DB_NAME.* to 'DB_NAME'@'localhost';
flush privileges;

drop table if exists annotations;
CREATE TABLE `annotations` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `image` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
 `caption` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
 `reviewed` BOOLEAN NOT NULL DEFAULT 1,
 `ts` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) DEFAULT CHARACTER SET = utf8;
