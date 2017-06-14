-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.5.5-10.1.21-MariaDB


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema capricon
--

CREATE DATABASE IF NOT EXISTS capricon;
USE capricon;

--
-- Definition of table `ArtSales`
--

DROP TABLE IF EXISTS `ArtSales`;
CREATE TABLE `ArtSales` (
  `ArtSalesID` int(8) NOT NULL AUTO_INCREMENT,
  `RecordID` int(8) NOT NULL,
  `ArtID` int(8) NOT NULL,
  `Price` decimal(5,2) NOT NULL,
  PRIMARY KEY (`ArtSalesID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ArtSales`
--

/*!40000 ALTER TABLE `ArtSales` DISABLE KEYS */;
/*!40000 ALTER TABLE `ArtSales` ENABLE KEYS */;


--
-- Definition of table `ArtSubmissions`
--

DROP TABLE IF EXISTS `ArtSubmissions`;
CREATE TABLE `ArtSubmissions` (
  `ArtID` int(8) NOT NULL AUTO_INCREMENT,
  `ArtistAttendingID` int(8) NOT NULL,
  `ShowNumber` int(4) DEFAULT NULL,
  `Title` varchar(100) NOT NULL,
  `Notes` varchar(100) NOT NULL DEFAULT '',
  `IsPrintShop` tinyint(1) NOT NULL DEFAULT '0',
  `IsOriginal` tinyint(1) NOT NULL DEFAULT '0',
  `OriginalMedia` varchar(100) DEFAULT NULL,
  `PrintNumber` varchar(10) DEFAULT NULL,
  `PrintMaxNumber` varchar(10) DEFAULT NULL,
  `MinimumBid` decimal(7,2) DEFAULT NULL,
  `QuickSalePrice` decimal(7,2) DEFAULT NULL,
  `QuantitySent` int(4) DEFAULT NULL,
  `QuantitySold` int(4) DEFAULT NULL,
  `LocationCode` varchar(10) DEFAULT NULL,
  `Category` varchar(20) DEFAULT NULL,
  `PurchaserBadgeID` int(8) DEFAULT NULL,
  `FinalSalePrice` decimal(7,2) DEFAULT NULL,
  `Auctioned` tinyint(1) NOT NULL DEFAULT '0',
  `FeesPaid` tinyint(1) DEFAULT NULL,
  `CheckedIn` tinyint(1) NOT NULL DEFAULT '0',
  `Claimed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ArtID`),
  KEY `fk_ArtSubmissions_ArtistAttendingID` (`ArtistAttendingID`),
  CONSTRAINT `fk_ArtSubmissions_ArtistAttendingID` FOREIGN KEY (`ArtistAttendingID`) REFERENCES `ArtistPresence` (`ArtistAttendingID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ArtSubmissions`
--

/*!40000 ALTER TABLE `ArtSubmissions` DISABLE KEYS */;
INSERT INTO `ArtSubmissions` (`ArtID`,`ArtistAttendingID`,`ShowNumber`,`Title`,`Notes`,`IsPrintShop`,`IsOriginal`,`OriginalMedia`,`PrintNumber`,`PrintMaxNumber`,`MinimumBid`,`QuickSalePrice`,`QuantitySent`,`QuantitySold`,`LocationCode`,`Category`,`PurchaserBadgeID`,`FinalSalePrice`,`Auctioned`,`FeesPaid`,`CheckedIn`,`Claimed`) VALUES 
 (1,1,1,'Art Show Item A','Test Notes',0,1,'',NULL,NULL,'100.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0),
 (2,1,2,'Art Show Item B','',0,0,'','1','20','150.00',NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,0,0,0);
/*!40000 ALTER TABLE `ArtSubmissions` ENABLE KEYS */;


--
-- Definition of table `ArtistDetails`
--

DROP TABLE IF EXISTS `ArtistDetails`;
CREATE TABLE `ArtistDetails` (
  `ArtistID` int(8) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(8) DEFAULT NULL,
  `DisplayName` varchar(50) NOT NULL,
  `LegalName` varchar(50) NOT NULL,
  `IsPro` tinyint(1) NOT NULL DEFAULT '0',
  `IsEAP` tinyint(1) NOT NULL DEFAULT '0',
  `CanPhoto` tinyint(1) NOT NULL DEFAULT '0',
  `Website` varchar(100) DEFAULT NULL,
  `ArtType` varchar(100) DEFAULT NULL,
  `Notes` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`ArtistID`),
  KEY `fk_ArtistDetails_PeopleID` (`PeopleID`),
  CONSTRAINT `fk_ArtistDetails_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ArtistDetails`
--

/*!40000 ALTER TABLE `ArtistDetails` DISABLE KEYS */;
INSERT INTO `ArtistDetails` (`ArtistID`,`PeopleID`,`DisplayName`,`LegalName`,`IsPro`,`IsEAP`,`CanPhoto`,`Website`,`ArtType`,`Notes`) VALUES 
 (1,1,'Brett Ferlin','Brett Ferlin',1,0,0,'','','');
/*!40000 ALTER TABLE `ArtistDetails` ENABLE KEYS */;


--
-- Definition of table `ArtistPresence`
--

DROP TABLE IF EXISTS `ArtistPresence`;
CREATE TABLE `ArtistPresence` (
  `ArtistAttendingID` int(8) NOT NULL AUTO_INCREMENT,
  `ArtistID` int(8) DEFAULT NULL,
  `Year` int(4) NOT NULL,
  `ArtistNumber` int(4) NOT NULL,
  `IsAttending` tinyint(1) NOT NULL DEFAULT '1',
  `AgentName` varchar(50) DEFAULT NULL,
  `AgentContact` varchar(50) DEFAULT NULL,
  `ShippingPref` varchar(10) DEFAULT NULL,
  `ShippingAddress` varchar(200) DEFAULT NULL,
  `ShippingCost` decimal(6,2) DEFAULT NULL,
  `ShippingPrepaid` decimal(6,2) DEFAULT NULL,
  `ShippingDetails` varchar(100) NOT NULL DEFAULT '',
  `NeedsElectricity` tinyint(1) NOT NULL DEFAULT '0',
  `NumTables` int(4) NOT NULL DEFAULT '1',
  `NumGrid` int(4) NOT NULL DEFAULT '0',
  `HasPrintShop` tinyint(1) NOT NULL DEFAULT '0',
  `Notes` varchar(500) NOT NULL DEFAULT '',
  `Status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `StatusReason` varchar(100) DEFAULT NULL,
  `LocationCode` varchar(10) NOT NULL DEFAULT '',
  `FeesWaivedReason` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ArtistAttendingID`),
  KEY `fk_ArtistPresence_ArtistID` (`ArtistID`),
  CONSTRAINT `fk_ArtistPresence_ArtistID` FOREIGN KEY (`ArtistID`) REFERENCES `ArtistDetails` (`ArtistID`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ArtistPresence`
--

/*!40000 ALTER TABLE `ArtistPresence` DISABLE KEYS */;
INSERT INTO `ArtistPresence` (`ArtistAttendingID`,`ArtistID`,`Year`,`ArtistNumber`,`IsAttending`,`AgentName`,`AgentContact`,`ShippingPref`,`ShippingAddress`,`ShippingCost`,`ShippingPrepaid`,`ShippingDetails`,`NeedsElectricity`,`NumTables`,`NumGrid`,`HasPrintShop`,`Notes`,`Status`,`StatusReason`,`LocationCode`,`FeesWaivedReason`) VALUES 
 (1,1,2018,2,1,NULL,NULL,NULL,NULL,NULL,NULL,'',0,1,0,0,'','Approved',NULL,'',NULL);
/*!40000 ALTER TABLE `ArtistPresence` ENABLE KEYS */;


--
-- Definition of table `AvailableBadges`
--

DROP TABLE IF EXISTS `AvailableBadges`;
CREATE TABLE `AvailableBadges` (
  `AvailableBadgeID` int(8) NOT NULL AUTO_INCREMENT,
  `Year` int(4) NOT NULL,
  `BadgeTypeID` int(8) NOT NULL,
  `Price` decimal(5,2) NOT NULL DEFAULT '0.00',
  `AvailableFrom` date NOT NULL,
  `AvailableTo` date NOT NULL,
  `AvailableOnline` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`AvailableBadgeID`),
  KEY `fk_AvailableBadges_BadgeTypeID` (`BadgeTypeID`),
  CONSTRAINT `fk_AvailableBadges_BadgeTypeID` FOREIGN KEY (`BadgeTypeID`) REFERENCES `BadgeTypes` (`BadgeTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `AvailableBadges`
--

/*!40000 ALTER TABLE `AvailableBadges` DISABLE KEYS */;
/*!40000 ALTER TABLE `AvailableBadges` ENABLE KEYS */;


--
-- Definition of table `BadgeCategory`
--

DROP TABLE IF EXISTS `BadgeCategory`;
CREATE TABLE `BadgeCategory` (
  `CategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `BadgeCategory`
--

/*!40000 ALTER TABLE `BadgeCategory` DISABLE KEYS */;
/*!40000 ALTER TABLE `BadgeCategory` ENABLE KEYS */;


--
-- Definition of table `BadgeTypes`
--

DROP TABLE IF EXISTS `BadgeTypes`;
CREATE TABLE `BadgeTypes` (
  `BadgeTypeID` int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(15) NOT NULL,
  `Description` varchar(50) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  PRIMARY KEY (`BadgeTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `BadgeTypes`
--

/*!40000 ALTER TABLE `BadgeTypes` DISABLE KEYS */;
/*!40000 ALTER TABLE `BadgeTypes` ENABLE KEYS */;


--
-- Definition of table `ConfirmationLinks`
--

DROP TABLE IF EXISTS `ConfirmationLinks`;
CREATE TABLE `ConfirmationLinks` (
  `Code` varchar(50) NOT NULL,
  `PeopleID` int(8) NOT NULL,
  `Type` varchar(20) NOT NULL,
  `Data` varchar(50) DEFAULT NULL,
  `Expires` datetime DEFAULT NULL,
  KEY `fk_ConfirmationLinks_PeopleID` (`PeopleID`),
  CONSTRAINT `fk_ConfirmationLinks_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ConfirmationLinks`
--

/*!40000 ALTER TABLE `ConfirmationLinks` DISABLE KEYS */;
/*!40000 ALTER TABLE `ConfirmationLinks` ENABLE KEYS */;


--
-- Definition of table `ConventionDetails`
--

DROP TABLE IF EXISTS `ConventionDetails`;
CREATE TABLE `ConventionDetails` (
  `Year` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Location` varchar(100) NOT NULL,
  KEY `Year` (`Year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ConventionDetails`
--

/*!40000 ALTER TABLE `ConventionDetails` DISABLE KEYS */;
/*!40000 ALTER TABLE `ConventionDetails` ENABLE KEYS */;


--
-- Definition of table `GiftCertificates`
--

DROP TABLE IF EXISTS `GiftCertificates`;
CREATE TABLE `GiftCertificates` (
  `CertificateID` int(8) NOT NULL AUTO_INCREMENT,
  `CertificateCode` varchar(20) NOT NULL,
  `PurchaserID` int(8) DEFAULT NULL,
  `Recipient` varchar(100) DEFAULT NULL,
  `Purchased` date NOT NULL,
  `Redeemed` date DEFAULT NULL,
  `OriginalValue` decimal(5,2) NOT NULL DEFAULT '0.00',
  `CurrentValue` decimal(5,2) NOT NULL DEFAULT '0.00',
  `Badges` int(4) NOT NULL DEFAULT '0',
  `CanTransfer` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`CertificateID`),
  KEY `fk_GiftCertificates_PurchaserID` (`PurchaserID`),
  KEY `fk_GiftCertificates_RecipientID` (`Recipient`),
  CONSTRAINT `fk_GiftCertificates_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `GiftCertificates`
--

/*!40000 ALTER TABLE `GiftCertificates` DISABLE KEYS */;
/*!40000 ALTER TABLE `GiftCertificates` ENABLE KEYS */;


--
-- Definition of table `OneTimeRegistrations`
--

DROP TABLE IF EXISTS `OneTimeRegistrations`;
CREATE TABLE `OneTimeRegistrations` (
  `OneTimeID` int(8) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Address1` varchar(80) NOT NULL,
  `Address2` varchar(80) DEFAULT NULL,
  `City` varchar(30) NOT NULL,
  `State` varchar(2) DEFAULT NULL,
  `ZipCode` varchar(10) DEFAULT NULL,
  `Country` varchar(20) NOT NULL DEFAULT 'USA',
  `Phone1` varchar(20) DEFAULT NULL,
  `Phone1Type` enum('Home','Mobile','Work','Other') DEFAULT NULL,
  `Phone2` varchar(20) DEFAULT NULL,
  `Phone2Type` enum('Home','Mobile','Work','Other') DEFAULT NULL,
  `LastChanged` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`OneTimeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `OneTimeRegistrations`
--

/*!40000 ALTER TABLE `OneTimeRegistrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `OneTimeRegistrations` ENABLE KEYS */;


--
-- Definition of table `PanelIdeas`
--

DROP TABLE IF EXISTS `PanelIdeas`;
CREATE TABLE `PanelIdeas` (
  `PanelIdeaID` int(8) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(8) DEFAULT NULL,
  `Year` int(4) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Title` varchar(100) NOT NULL,
  `Description` varchar(500) NOT NULL,
  `Participants` varchar(500) NOT NULL,
  `CanContact` tinyint(1) NOT NULL DEFAULT '0',
  `Created` datetime NOT NULL,
  PRIMARY KEY (`PanelIdeaID`),
  KEY `fk_PanelIdeas_PeopleID` (`PeopleID`),
  CONSTRAINT `fk_PanelIdeas_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PanelIdeas`
--

/*!40000 ALTER TABLE `PanelIdeas` DISABLE KEYS */;
/*!40000 ALTER TABLE `PanelIdeas` ENABLE KEYS */;


--
-- Definition of table `PendingAccounts`
--

DROP TABLE IF EXISTS `PendingAccounts`;
CREATE TABLE `PendingAccounts` (
  `PendingID` varchar(50) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `BadgeName` varchar(40) NOT NULL DEFAULT '',
  `Address1` varchar(80) NOT NULL,
  `Address2` varchar(80) DEFAULT NULL,
  `City` varchar(30) NOT NULL,
  `State` varchar(2) DEFAULT NULL,
  `ZipCode` varchar(10) DEFAULT NULL,
  `Country` varchar(20) NOT NULL DEFAULT 'USA',
  `Phone1` varchar(20) DEFAULT NULL,
  `Phone1Type` enum('Home','Mobile','Work','Other') DEFAULT NULL,
  `Phone2` varchar(20) DEFAULT NULL,
  `Phone2Type` enum('Home','Mobile','Work','Other') DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `HeardFrom` varchar(100) DEFAULT NULL,
  `Interests` varchar(200) NOT NULL DEFAULT '',
  `Password` varchar(255) NOT NULL,
  `Expires` datetime NOT NULL,
  `Entered` int(10) unsigned NOT NULL,
  PRIMARY KEY (`PendingID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PendingAccounts`
--

/*!40000 ALTER TABLE `PendingAccounts` DISABLE KEYS */;
/*!40000 ALTER TABLE `PendingAccounts` ENABLE KEYS */;


--
-- Definition of table `People`
--

DROP TABLE IF EXISTS `People`;
CREATE TABLE `People` (
  `PeopleID` int(8) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Address1` varchar(80) NOT NULL,
  `Address2` varchar(80) DEFAULT NULL,
  `City` varchar(30) NOT NULL,
  `State` varchar(2) DEFAULT NULL,
  `ZipCode` varchar(10) DEFAULT NULL,
  `Country` varchar(20) NOT NULL DEFAULT 'USA',
  `Phone1` varchar(20) DEFAULT NULL,
  `Phone1Type` enum('Home','Mobile','Work','Other') DEFAULT NULL,
  `Phone2` varchar(20) DEFAULT NULL,
  `Phone2Type` enum('Home','Mobile','Work','Other') DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Registered` date NOT NULL,
  `BadgeName` varchar(40) NOT NULL,
  `Banned` tinyint(1) NOT NULL DEFAULT '0',
  `ParentID` int(8) DEFAULT NULL,
  `HeardFrom` varchar(100) NOT NULL DEFAULT '',
  `IsCharity` tinyint(1) NOT NULL DEFAULT '0',
  `LastChanged` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PeopleID`),
  KEY `fk_People_ParentID` (`ParentID`),
  CONSTRAINT `fk_People_ParentID` FOREIGN KEY (`ParentID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `People`
--

/*!40000 ALTER TABLE `People` DISABLE KEYS */;
INSERT INTO `People` (`PeopleID`,`FirstName`,`LastName`,`Address1`,`Address2`,`City`,`State`,`ZipCode`,`Country`,`Phone1`,`Phone1Type`,`Phone2`,`Phone2Type`,`Email`,`Password`,`Registered`,`BadgeName`,`Banned`,`ParentID`,`HeardFrom`,`IsCharity`,`LastChanged`) VALUES 
 (1,'Brett','Ferlin','Test',NULL,'Test','IL','60605','USA',NULL,NULL,NULL,NULL,'bferlin@gmail.com','$2y$13$3XSICmHDf6les/QZnZPXaeQE5QGX3pg8wioi4WZjpS5MWoiubo/WG','2017-04-14','SnuSnu',0,NULL,'',0,'2017-04-16 22:21:23');
/*!40000 ALTER TABLE `People` ENABLE KEYS */;


--
-- Definition of table `PeopleInterests`
--

DROP TABLE IF EXISTS `PeopleInterests`;
CREATE TABLE `PeopleInterests` (
  `PeopleID` int(8) NOT NULL,
  `Interest` varchar(20) NOT NULL,
  PRIMARY KEY (`PeopleID`,`Interest`),
  CONSTRAINT `fk_PeopleInterests_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PeopleInterests`
--

/*!40000 ALTER TABLE `PeopleInterests` DISABLE KEYS */;
/*!40000 ALTER TABLE `PeopleInterests` ENABLE KEYS */;


--
-- Definition of table `PermissionDetails`
--

DROP TABLE IF EXISTS `PermissionDetails`;
CREATE TABLE `PermissionDetails` (
  `Permission` varchar(20) NOT NULL,
  `ShortName` varchar(40) NOT NULL,
  `Description` varchar(200) NOT NULL,
  `Module` varchar(50) NOT NULL,
  `ExpireAfterCon` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Permission`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PermissionDetails`
--

/*!40000 ALTER TABLE `PermissionDetails` DISABLE KEYS */;
INSERT INTO `PermissionDetails` (`Permission`,`ShortName`,`Description`,`Module`,`ExpireAfterCon`) VALUES 
 ('superadmin','superadmin','Superadmin','',0);
/*!40000 ALTER TABLE `PermissionDetails` ENABLE KEYS */;


--
-- Definition of table `Permissions`
--

DROP TABLE IF EXISTS `Permissions`;
CREATE TABLE `Permissions` (
  `PermissonID` int(8) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(8) NOT NULL,
  `Permission` varchar(20) NOT NULL,
  `Expiration` date DEFAULT NULL,
  PRIMARY KEY (`PermissonID`),
  KEY `fk_Permissions_PeopleID` (`PeopleID`),
  KEY `fk_Permissions_Permission` (`Permission`),
  CONSTRAINT `fk_Permissions_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE,
  CONSTRAINT `fk_Permissions_Permission` FOREIGN KEY (`Permission`) REFERENCES `PermissionDetails` (`Permission`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `Permissions`
--

/*!40000 ALTER TABLE `Permissions` DISABLE KEYS */;
INSERT INTO `Permissions` (`PermissonID`,`PeopleID`,`Permission`,`Expiration`) VALUES 
 (1,1,'superadmin',NULL);
/*!40000 ALTER TABLE `Permissions` ENABLE KEYS */;


--
-- Definition of table `ProgramSurvey`
--

DROP TABLE IF EXISTS `ProgramSurvey`;
CREATE TABLE `ProgramSurvey` (
  `SurveyID` int(8) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(8) NOT NULL,
  `Year` int(11) NOT NULL,
  `PreferredContact` varchar(20) NOT NULL DEFAULT 'Email',
  `Website` varchar(100) NOT NULL DEFAULT '',
  `Biography` varchar(500) NOT NULL DEFAULT '',
  `DayJob` varchar(100) NOT NULL DEFAULT '',
  `Expertise` varchar(100) NOT NULL DEFAULT '',
  `ExpertiseText` varchar(500) NOT NULL DEFAULT '',
  `Arrival` varchar(20) NOT NULL DEFAULT 'Wed 8am',
  `Departure` varchar(20) NOT NULL DEFAULT 'Mon 8am',
  `MaxPanelsTh` int(11) NOT NULL DEFAULT '0',
  `PanelStartTh` int(11) NOT NULL DEFAULT '15',
  `PanelEndTh` int(11) NOT NULL DEFAULT '0',
  `MaxPanelsFr` int(11) NOT NULL DEFAULT '0',
  `PanelStartFr` int(11) NOT NULL DEFAULT '8',
  `PanelEndFr` int(11) NOT NULL DEFAULT '0',
  `MaxPanelsSa` int(11) NOT NULL DEFAULT '0',
  `PanelStartSa` int(11) NOT NULL DEFAULT '8',
  `PanelEndSa` int(11) NOT NULL DEFAULT '0',
  `MaxPanelsSu` int(11) NOT NULL DEFAULT '0',
  `PanelStartSu` int(11) NOT NULL DEFAULT '8',
  `PanelEndSu` int(11) NOT NULL DEFAULT '15',
  `AvailabilityNotes` varchar(200) NOT NULL DEFAULT '',
  `Interests` varchar(200) NOT NULL DEFAULT '',
  `InterestsText` varchar(3000) NOT NULL DEFAULT '',
  `WillingAutograph` tinyint(4) NOT NULL DEFAULT '0',
  `WillingReading` tinyint(4) NOT NULL DEFAULT '0',
  `WillingYA` tinyint(4) NOT NULL DEFAULT '0',
  `WillingKids` tinyint(4) NOT NULL DEFAULT '0',
  `ProgramIdeas` varchar(500) NOT NULL DEFAULT '',
  `ProgramIdeaTitle` varchar(100) NOT NULL DEFAULT '',
  `ProgramIdeaPanelists` varchar(200) NOT NULL DEFAULT '',
  `OverdonePrograms` varchar(500) NOT NULL DEFAULT '',
  `PanelistToAvoid` varchar(500) NOT NULL DEFAULT '',
  `Accessibility` varchar(200) NOT NULL DEFAULT '',
  `AdditionalInfo` varchar(500) NOT NULL DEFAULT '',
  `CanShareInfo` tinyint(4) NOT NULL DEFAULT '0',
  `Created` datetime NOT NULL,
  PRIMARY KEY (`SurveyID`),
  KEY `fk_ProgramSurvey_PeopleID` (`PeopleID`),
  CONSTRAINT `fk_ProgramSurvey_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ProgramSurvey`
--

/*!40000 ALTER TABLE `ProgramSurvey` DISABLE KEYS */;
/*!40000 ALTER TABLE `ProgramSurvey` ENABLE KEYS */;


--
-- Definition of table `PromoCodes`
--

DROP TABLE IF EXISTS `PromoCodes`;
CREATE TABLE `PromoCodes` (
  `CodeID` int(8) NOT NULL AUTO_INCREMENT,
  `Year` int(4) NOT NULL,
  `Code` varchar(20) NOT NULL,
  `Discount` decimal(5,2) NOT NULL,
  `Expiration` date DEFAULT NULL,
  `UsesLeft` int(3) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`CodeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PromoCodes`
--

/*!40000 ALTER TABLE `PromoCodes` DISABLE KEYS */;
/*!40000 ALTER TABLE `PromoCodes` ENABLE KEYS */;


--
-- Definition of table `PurchaseHistory`
--

DROP TABLE IF EXISTS `PurchaseHistory`;
CREATE TABLE `PurchaseHistory` (
  `RecordID` int(8) NOT NULL AUTO_INCREMENT,
  `PurchaserID` int(8) DEFAULT NULL,
  `PurchaserOneTimeID` int(8) DEFAULT NULL,
  `ItemTypeName` varchar(20) DEFAULT NULL,
  `ItemTypeID` int(8) DEFAULT NULL,
  `Details` varchar(100) NOT NULL,
  `PeopleID` int(8) DEFAULT NULL,
  `OneTimeID` int(8) DEFAULT NULL,
  `Price` decimal(5,2) NOT NULL DEFAULT '0.00',
  `Year` int(4) DEFAULT NULL,
  `Purchased` datetime NOT NULL,
  `PaymentSource` varchar(15) DEFAULT NULL,
  `PaymentReference` text,
  `AmountRefunded` decimal(5,2) DEFAULT NULL,
  `RefundReason` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`RecordID`),
  KEY `fk_PurchaseHistory_PeopleID` (`PeopleID`),
  KEY `fk_PurchaseHistory_PurchaserID` (`PurchaserID`),
  CONSTRAINT `fk_PurchaseHistory_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL,
  CONSTRAINT `fk_PurchaseHistory_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PurchaseHistory`
--

/*!40000 ALTER TABLE `PurchaseHistory` DISABLE KEYS */;
/*!40000 ALTER TABLE `PurchaseHistory` ENABLE KEYS */;


--
-- Definition of table `PurchasedBadges`
--

DROP TABLE IF EXISTS `PurchasedBadges`;
CREATE TABLE `PurchasedBadges` (
  `BadgeID` int(8) NOT NULL AUTO_INCREMENT,
  `Year` int(4) NOT NULL,
  `PeopleID` int(8) DEFAULT NULL,
  `OneTimeID` int(8) DEFAULT NULL,
  `PurchaserID` int(8) DEFAULT NULL,
  `OneTimePurchaserID` int(8) DEFAULT NULL,
  `BadgeNumber` int(4) NOT NULL,
  `BadgeTypeID` int(8) NOT NULL,
  `BadgeName` varchar(40) NOT NULL,
  `Department` varchar(60) DEFAULT NULL,
  `Status` enum('Pending','Paid','Inactive','Rolled Over','Deleted','Frozen','Refunded') NOT NULL DEFAULT 'Pending',
  `OriginalPrice` decimal(5,2) NOT NULL DEFAULT '0.00',
  `AmountPaid` decimal(5,2) NOT NULL DEFAULT '0.00',
  `PaymentSource` varchar(15) NOT NULL,
  `PaymentReference` text,
  `PromoCodeID` int(8) DEFAULT NULL,
  `CertificateID` int(8) DEFAULT NULL,
  `RecordID` int(11) DEFAULT NULL,
  `Created` datetime NOT NULL,
  PRIMARY KEY (`BadgeID`),
  KEY `fk_PurchasedBadges_PeopleID` (`PeopleID`),
  KEY `fk_PurchasedBadges_PurchaserID` (`PurchaserID`),
  KEY `fk_PurchasedBadges_BadgeTypeID` (`BadgeTypeID`),
  KEY `fk_PurchasedBadges_PromoCodeID` (`PromoCodeID`),
  KEY `fk_PurchasedBadges_CertificateID` (`CertificateID`),
  CONSTRAINT `fk_PurchasedBadges_BadgeTypeID` FOREIGN KEY (`BadgeTypeID`) REFERENCES `BadgeTypes` (`BadgeTypeID`) ON UPDATE CASCADE,
  CONSTRAINT `fk_PurchasedBadges_CertificateID` FOREIGN KEY (`CertificateID`) REFERENCES `GiftCertificates` (`CertificateID`) ON DELETE SET NULL,
  CONSTRAINT `fk_PurchasedBadges_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL,
  CONSTRAINT `fk_PurchasedBadges_PromoCodeID` FOREIGN KEY (`PromoCodeID`) REFERENCES `PromoCodes` (`CodeID`) ON DELETE SET NULL,
  CONSTRAINT `fk_PurchasedBadges_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `PurchasedBadges`
--

/*!40000 ALTER TABLE `PurchasedBadges` DISABLE KEYS */;
/*!40000 ALTER TABLE `PurchasedBadges` ENABLE KEYS */;


--
-- Definition of table `ShoppingCart`
--

DROP TABLE IF EXISTS `ShoppingCart`;
CREATE TABLE `ShoppingCart` (
  `CartID` int(8) NOT NULL AUTO_INCREMENT,
  `PurchaserID` int(8) DEFAULT NULL,
  `ItemTypeName` varchar(20) DEFAULT NULL,
  `ItemTypeID` int(8) DEFAULT NULL,
  `ItemDetail` varchar(50) DEFAULT NULL,
  `PeopleID` int(8) DEFAULT NULL,
  `Price` decimal(5,2) NOT NULL DEFAULT '0.00',
  `PromoCodeID` int(8) DEFAULT NULL,
  `CertificateID` int(8) DEFAULT NULL,
  `Created` datetime NOT NULL,
  PRIMARY KEY (`CartID`),
  KEY `fk_ShoppingCart_PeopleID` (`PeopleID`),
  KEY `fk_ShoppingCart_PurchaserID` (`PurchaserID`),
  KEY `fk_ShoppingCart_PromoCodeID` (`PromoCodeID`),
  KEY `fk_ShoppingCart_CertificateID` (`CertificateID`),
  CONSTRAINT `fk_ShoppingCart_CertificateID` FOREIGN KEY (`CertificateID`) REFERENCES `GiftCertificates` (`CertificateID`) ON DELETE SET NULL,
  CONSTRAINT `fk_ShoppingCart_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL,
  CONSTRAINT `fk_ShoppingCart_PromoCodeID` FOREIGN KEY (`PromoCodeID`) REFERENCES `PromoCodes` (`CodeID`) ON DELETE SET NULL,
  CONSTRAINT `fk_ShoppingCart_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ShoppingCart`
--

/*!40000 ALTER TABLE `ShoppingCart` DISABLE KEYS */;
/*!40000 ALTER TABLE `ShoppingCart` ENABLE KEYS */;


--
-- Definition of table `SurveyExpertise`
--

DROP TABLE IF EXISTS `SurveyExpertise`;
CREATE TABLE `SurveyExpertise` (
  `ExpertiseID` int(8) NOT NULL AUTO_INCREMENT,
  `Expertise` varchar(50) NOT NULL,
  PRIMARY KEY (`ExpertiseID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `SurveyExpertise`
--

/*!40000 ALTER TABLE `SurveyExpertise` DISABLE KEYS */;
/*!40000 ALTER TABLE `SurveyExpertise` ENABLE KEYS */;


--
-- Definition of table `SurveyInterests`
--

DROP TABLE IF EXISTS `SurveyInterests`;
CREATE TABLE `SurveyInterests` (
  `InterestID` int(8) NOT NULL AUTO_INCREMENT,
  `Interest` varchar(50) NOT NULL,
  PRIMARY KEY (`InterestID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `SurveyInterests`
--

/*!40000 ALTER TABLE `SurveyInterests` DISABLE KEYS */;
/*!40000 ALTER TABLE `SurveyInterests` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
