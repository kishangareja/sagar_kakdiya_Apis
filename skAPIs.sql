/*
SQLyog Community v12.09 (32 bit)
MySQL - 5.7.31 : Database - sagar_kakdiya_apis
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`sagar_kakdiya_apis` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `sagar_kakdiya_apis`;

/*Table structure for table `category` */

DROP TABLE IF EXISTS `category`;

CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `image` varchar(150) DEFAULT NULL,
  `creation_datetime` datetime DEFAULT NULL,
  `modification_datetime` datetime DEFAULT NULL,
  `deletion_datetime` datetime NOT NULL,
  `is_deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `category` */

insert  into `category`(`id`,`name`,`image`,`creation_datetime`,`modification_datetime`,`deletion_datetime`,`is_deleted`) values (4,'Clothes','domain_1900635231592311554.jpg','2021-03-14 17:10:21','2021-03-14 17:10:25','2021-03-14 17:11:02',0),(5,'Tommy Jeans','domain_1900635231592311554.jpg','2021-03-14 18:05:47','2021-03-14 18:05:50','2021-03-14 18:05:52',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
