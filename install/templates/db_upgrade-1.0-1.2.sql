--
-- upgrades database to include timestamps and reviewed flag,
-- WARNING: only needed when using older database version (prepare_db-1.0.sql)
--

use DB_NAME;
-- fill current annotations with reviewed: false
ALTER TABLE `annotations` ADD `reviewed` BOOLEAN NOT NULL DEFAULT 0;
ALTER TABLE `annotations` ADD `updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;
-- change review status of newly entered annotations
ALTER TABLE `annotations` CHANGE `reviewed` `reviewed` BOOLEAN NOT NULL DEFAULT 1;
-- update existing annots to include current timestamp
UPDATE `annotations` SET `updated` = CURRENT_TIMESTAMP where `updated` IS NULL;