-- MySQL dump 10.13  Distrib 5.7.12, for Linux (x86_64)
--
-- Host: localhost    Database: api_server
-- ------------------------------------------------------
-- Server version	5.7.12-0ubuntu1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `account`
--

DROP TABLE IF EXISTS `account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `account` (
	`id` varchar(255) NOT NULL COMMENT '클라이언트 ID',
	`guid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '게임 ID',
	`status` char(1) NOT NULL DEFAULT '0' COMMENT '플레이어 상태(0:정상, 1:일시중지, 2:영구정지, 9:삭제)',
	`os` char(1) NOT NULL DEFAULT '0' COMMENT 'OS ID',
	`platform` int(11) unsigned NOT NULL COMMENT '플랫폼 ID',
	`device` varchar(100) NOT NULL COMMENT '디바이스 ID',
	`version` varchar(10) NOT NULL COMMENT '클라이언트 버전',
	`msg` varchar(255) NULL COMMENT '계정 상태 설명',
	`create_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '생성일자',
	`delete_date` int(11) unsigned NULL COMMENT '삭제일자',
	`cease_date` int(11) unsigned NULL COMMENT '일시중지일자',
	`reg_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '업데이트 일자',
	PRIMARY KEY (`id`),
	UNIQUE KEY `UNIQUE` (`guid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='인증정보';
/*!40101 SET character_set_client = @saved_cs_client */;

