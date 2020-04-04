ALTER TABLE `pullzones`
    ADD COLUMN `zone_us`   TINYINT(1) NULL DEFAULT '0' AFTER `storage_zone_id`,
    ADD COLUMN `zone_eu`   TINYINT(1) NULL DEFAULT '0' AFTER `zone_us`,
    ADD COLUMN `zone_asia` TINYINT(1) NULL DEFAULT '0' AFTER `zone_eu`,
    ADD COLUMN `zone_sa`   TINYINT(1) NULL DEFAULT '0' AFTER `zone_asia`,
    ADD COLUMN `zone_af`   TINYINT(1) NULL DEFAULT '0' AFTER `zone_sa`;

ALTER TABLE `storagezones`
    ADD COLUMN `zone_us`   TINYINT(1) NULL DEFAULT '0' AFTER `date_modified`,
    ADD COLUMN `zone_eu`   TINYINT(1) NULL DEFAULT '0' AFTER `zone_us`,
    ADD COLUMN `zone_asia` TINYINT(1) NULL DEFAULT '0' AFTER `zone_eu`,
    ADD COLUMN `zone_sa`   TINYINT(1) NULL DEFAULT '0' AFTER `zone_asia`,
    ADD COLUMN `zone_af`   TINYINT(1) NULL DEFAULT '0' AFTER `zone_sa`;