/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for bunnycdn
CREATE DATABASE IF NOT EXISTS `bunnycdn` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `bunnycdn`;

-- Dumping structure for table bunnycdn.actions
CREATE TABLE IF NOT EXISTS `actions` (
                                         `task` varchar(24) DEFAULT NULL,
                                         `zone_name` varchar(124) DEFAULT NULL,
                                         `file` varchar(124) DEFAULT NULL,
                                         `file_other` varchar(124) DEFAULT NULL,
                                         `datetime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table bunnycdn.deleted_files
CREATE TABLE IF NOT EXISTS `deleted_files` (
                                               `zone_name` varchar(124) DEFAULT NULL,
                                               `file` varchar(124) DEFAULT NULL,
                                               `dir` varchar(124) DEFAULT NULL,
                                               `datetime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table bunnycdn.file_history
CREATE TABLE IF NOT EXISTS `file_history` (
                                              `zone_name` varchar(124) DEFAULT NULL,
                                              `new_name` varchar(124) DEFAULT NULL,
                                              `new_dir` varchar(124) DEFAULT NULL,
                                              `old_name` varchar(124) DEFAULT NULL,
                                              `old_dir` varchar(124) DEFAULT NULL,
                                              `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table bunnycdn.log_main
CREATE TABLE IF NOT EXISTS `log_main` (
                                          `zid` int(11) DEFAULT NULL,
                                          `rid` varchar(64) NOT NULL,
                                          `result` varchar(4) DEFAULT NULL,
                                          `file_url` varchar(224) DEFAULT NULL,
                                          `referer` varchar(224) DEFAULT NULL,
                                          `datetime` datetime DEFAULT NULL,
                                          PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table bunnycdn.log_more
CREATE TABLE IF NOT EXISTS `log_more` (
                                          `zid` int(11) NOT NULL,
                                          `rid` varchar(124) NOT NULL,
                                          `status` int(11) DEFAULT NULL,
                                          `bytes` int(11) DEFAULT NULL,
                                          `ip` varchar(16) DEFAULT NULL,
                                          `user_agent` varchar(224) DEFAULT NULL,
                                          `cdn_dc` varchar(6) DEFAULT NULL,
                                          `country_code` varchar(6) DEFAULT NULL,
                                          PRIMARY KEY (`rid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table bunnycdn.pullzones
CREATE TABLE IF NOT EXISTS `pullzones` (
                                           `id` int(11) NOT NULL,
                                           `name` varchar(64) DEFAULT NULL,
                                           `origin_url` varchar(124) DEFAULT NULL,
                                           `enabled` tinyint(1) DEFAULT 1,
                                           `bandwidth_used` int(11) DEFAULT 0,
                                           `bandwidth_limit` int(11) DEFAULT 0,
                                           `monthly_charge` decimal(12,12) DEFAULT 0.000000000000,
                                           `storage_zone_id` int(11) DEFAULT NULL,
                                           PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
-- Dumping structure for table bunnycdn.storagezones
CREATE TABLE IF NOT EXISTS `storagezones` (
                                              `id` int(11) NOT NULL,
                                              `name` varchar(64) NOT NULL,
                                              `storage_used` int(11) NOT NULL DEFAULT 0,
                                              `files_stored` int(11) NOT NULL DEFAULT 0,
                                              `enabled` tinyint(1) NOT NULL DEFAULT 1,
                                              `date_modified` datetime NOT NULL,
                                              PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;