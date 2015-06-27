/*
SQLyog Community v12.03 (64 bit)
MySQL - 5.1.73 : Database - dfa_devinfo_data_admin
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`dfa_devinfo_data_admin` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `dfa_devinfo_data_admin`;

/*Table structure for table `m_application_logs` */

DROP TABLE IF EXISTS `m_application_logs`;

CREATE TABLE `m_application_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL COMMENT 'DATABASE,USER,SERVICE,TEMPLATE,DATAENTRY',
  `action` varchar(50) NOT NULL COMMENT 'ADD,UPDATE,DELETE,ERROR,IMPORT,EXPORT,PUBLISH,DBCONNECTION',
  `description` longtext,
  `ip_address` varchar(50) NOT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` int(11) NOT NULL COMMENT 'User id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `m_application_logs` */

/*Table structure for table `m_database_connections` */

DROP TABLE IF EXISTS `m_database_connections`;

CREATE TABLE `m_database_connections` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `devinfo_db_connection` varchar(255) DEFAULT NULL COMMENT 'Connection String of the DevInfo SQL Server Database',
  `archived` tinyint(1) DEFAULT '0' COMMENT 'Never delete any record from the database. Archive the deleted\r\nrecords',
  `created` datetime DEFAULT NULL COMMENT 'Date created on',
  `createdby` int(11) DEFAULT '0' COMMENT 'User ID of the Owner of the DevInfo Database connection creator.',
  `modified` datetime DEFAULT NULL COMMENT 'modified on',
  `modifiedby` int(11) DEFAULT '0' COMMENT 'modified by\r',
  PRIMARY KEY (`ID`),
  KEY `createdby` (`createdby`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `m_database_connections` */

insert  into `m_database_connections`(`ID`,`devinfo_db_connection`,`archived`,`created`,`createdby`,`modified`,`modifiedby`) values (1,NULL,0,NULL,0,NULL,0),(2,'{\"db_source\":\"Mysql\",\"db_name\":\"Testdevinfodb\",\"db_host\":\"dgps-os\",\"db_login\":\"root\",\"db_password\" :\"root\",\"db_port\":\"\",\"db_database\":\"dfa_devinfo_data_admin\"}',0,NULL,0,NULL,0);

/*Table structure for table `m_ius_validations` */

DROP TABLE IF EXISTS `m_ius_validations`;

CREATE TABLE `m_ius_validations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db_id` int(11) NOT NULL,
  `indicator_gid` varchar(255) NOT NULL,
  `unit_gid` varchar(255) NOT NULL,
  `subgroup_gid` varchar(255) NOT NULL,
  `is_textual` enum('0','1') NOT NULL DEFAULT '0' COMMENT 'JSON string having all validations about IUS',
  `min_value` varchar(50) DEFAULT NULL,
  `max_value` varchar(50) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `m_ius_validations` */

/*Table structure for table `m_roles` */

DROP TABLE IF EXISTS `m_roles`;

CREATE TABLE `m_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(50) NOT NULL COMMENT 'SUPERADMIN,ADMIN,TEMPLATE,DATAENTRY',
  `role_name` varchar(50) NOT NULL COMMENT 'name of the role like admin ,super admin,data entry ,template',
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `m_roles` */

/*Table structure for table `m_system_confirgurations` */

DROP TABLE IF EXISTS `m_system_confirgurations`;

CREATE TABLE `m_system_confirgurations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(765) NOT NULL,
  `key_value` varchar(765) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `m_system_confirgurations` */

insert  into `m_system_confirgurations`(`id`,`key_name`,`key_value`) values (1,'APP_NAME','DFA Data Admin Tool'),(2,'DEVINFO_DBID','2');

/*Table structure for table `m_transaction_logs` */

DROP TABLE IF EXISTS `m_transaction_logs`;

CREATE TABLE `m_transaction_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `db_id` int(11) NOT NULL COMMENT 'database dev info id',
  `action` varchar(50) DEFAULT NULL COMMENT 'ADD, UPDATE, DELETE, IMPORT, EXPORT',
  `module` varchar(255) DEFAULT NULL COMMENT 'TEMPLATE, DATAENTRY',
  `submodule` varchar(255) DEFAULT NULL COMMENT 'INDICATOR,UNIT,SUBGROUP,IUS,IC,AREA,TIMEPERIOD,SOURCE,FOOTNOTE',
  `identifier` varchar(255) DEFAULT NULL COMMENT 'submodule GID',
  `previousvalue` varchar(50) NOT NULL COMMENT 'submodule previous value',
  `newvalue` varchar(50) DEFAULT NULL COMMENT 'submodule new value',
  `status` varchar(10) DEFAULT NULL COMMENT 'DONE,FAILED',
  `description` longtext COMMENT 'Descript, could be an error message ',
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `m_transaction_logs` */

/*Table structure for table `m_users` */

DROP TABLE IF EXISTS `m_users`;

CREATE TABLE `m_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(765) DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `role_id` tinyint(1) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `createdby` int(11) NOT NULL,
  `modified` datetime DEFAULT NULL,
  `modifiedby` int(11) NOT NULL,
  `lastloggedin` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `m_users` */

insert  into `m_users`(`id`,`name`,`email`,`password`,`status`,`role_id`,`created`,`createdby`,`modified`,`modifiedby`,`lastloggedin`) values (1,'','admin@test.com','$2y$10$rcXIDinnMBaUwxhYQ2rMweRJbumvWv.4MiIoMJ3iH8YkkIUUl9y1a',NULL,1,NULL,0,NULL,0,NULL),(3,'','rishi@test.com','$2y$10$.lKp2V99F98/ND6rnjcC2.7FiaM9oCJ2Q5rElG57E2sjvwomr7boS',NULL,NULL,'2015-06-23 13:58:19',0,'2015-06-23 13:58:19',0,NULL);

/*Table structure for table `r_access_areas` */

DROP TABLE IF EXISTS `r_access_areas`;

CREATE TABLE `r_access_areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_database_role_id` int(11) NOT NULL COMMENT 'User Database Role foreign key ',
  `user_database_id` int(11) NOT NULL COMMENT 'User Database foreign key to make join with this table directly',
  `area_id` varchar(100) NOT NULL COMMENT 'Area ID',
  `area_name` varchar(100) NOT NULL COMMENT 'Area Name',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `r_access_areas` */

/*Table structure for table `r_access_indicators` */

DROP TABLE IF EXISTS `r_access_indicators`;

CREATE TABLE `r_access_indicators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_database_role_id` int(11) NOT NULL COMMENT 'User Database Role foreign key',
  `user_database_id` int(11) NOT NULL COMMENT 'User Database id, to make join wiht this table directly',
  `indicator_gid` varchar(255) DEFAULT NULL COMMENT 'Indicator GID',
  `indicator_name` varchar(255) NOT NULL COMMENT 'Indicator Name',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `r_access_indicators` */

/*Table structure for table `r_user_database_roles` */

DROP TABLE IF EXISTS `r_user_database_roles`;

CREATE TABLE `r_user_database_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_database_id` int(11) DEFAULT NULL,
  `role_id` int(5) NOT NULL,
  `area_access` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-ALL, 1-SELECTED AREAS STROED in r_access_areas',
  `indicator_access` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0-ALL, 1-SELECTED INDICATORS STROED in r_access_indicators',
  `createdon` datetime DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modifiedon` datetime DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `r_user_database_roles` */

/*Table structure for table `r_user_databases` */

DROP TABLE IF EXISTS `r_user_databases`;

CREATE TABLE `r_user_databases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `db_id` int(11) NOT NULL,
  `is_default_db` enum('0','1') NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `createdby` int(11) DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `modifiedby` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `r_user_databases` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
