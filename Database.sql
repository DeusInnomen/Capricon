-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: mysql.phandemonium.org
-- Generation Time: May 19, 2016 at 09:13 AM
-- Server version: 5.6.25-log
-- PHP Version: 7.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capricon_registration`
--

-- --------------------------------------------------------

--
-- Table structure for table `ArtistDetails`
--

CREATE TABLE IF NOT EXISTS `ArtistDetails` (
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
  KEY `fk_ArtistDetails_PeopleID` (`PeopleID`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ArtistPresence`
--

CREATE TABLE IF NOT EXISTS `ArtistPresence` (
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
  KEY `fk_ArtistPresence_ArtistID` (`ArtistID`)
) ENGINE=InnoDB AUTO_INCREMENT=227 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ArtSales`
--

CREATE TABLE IF NOT EXISTS `ArtSales` (
  `ArtSalesID` int(8) NOT NULL AUTO_INCREMENT,
  `RecordID` int(8) NOT NULL,
  `ArtID` int(8) NOT NULL,
  `Price` decimal(5,2) NOT NULL,
  PRIMARY KEY (`ArtSalesID`)
) ENGINE=InnoDB AUTO_INCREMENT=371 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ArtSubmissions`
--

CREATE TABLE IF NOT EXISTS `ArtSubmissions` (
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
  KEY `fk_ArtSubmissions_ArtistAttendingID` (`ArtistAttendingID`)
) ENGINE=InnoDB AUTO_INCREMENT=3657 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `AvailableBadges`
--

CREATE TABLE IF NOT EXISTS `AvailableBadges` (
  `AvailableBadgeID` int(8) NOT NULL AUTO_INCREMENT,
  `Year` int(4) NOT NULL,
  `BadgeTypeID` int(8) NOT NULL,
  `Price` decimal(5,2) NOT NULL DEFAULT '0.00',
  `AvailableFrom` date NOT NULL,
  `AvailableTo` date NOT NULL,
  `AvailableOnline` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`AvailableBadgeID`),
  KEY `fk_AvailableBadges_BadgeTypeID` (`BadgeTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `BadgeCategory`
--

CREATE TABLE IF NOT EXISTS `BadgeCategory` (
  `CategoryID` int(11) NOT NULL AUTO_INCREMENT,
  `CategoryName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`CategoryID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `BadgeTypes`
--

CREATE TABLE IF NOT EXISTS `BadgeTypes` (
  `BadgeTypeID` int(8) NOT NULL AUTO_INCREMENT,
  `Name` varchar(15) NOT NULL,
  `Description` varchar(50) NOT NULL,
  `CategoryID` int(11) NOT NULL,
  PRIMARY KEY (`BadgeTypeID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ConfirmationLinks`
--

CREATE TABLE IF NOT EXISTS `ConfirmationLinks` (
  `Code` varchar(50) NOT NULL,
  `PeopleID` int(8) NOT NULL,
  `Type` varchar(20) NOT NULL,
  `Data` varchar(50) DEFAULT NULL,
  `Expires` datetime DEFAULT NULL,
  KEY `fk_ConfirmationLinks_PeopleID` (`PeopleID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ConventionDetails`
--

CREATE TABLE IF NOT EXISTS `ConventionDetails` (
  `Year` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Location` varchar(100) NOT NULL,
  KEY `Year` (`Year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `GiftCertificates`
--

CREATE TABLE IF NOT EXISTS `GiftCertificates` (
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
  KEY `fk_GiftCertificates_RecipientID` (`Recipient`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `OneTimeRegistrations`
--

CREATE TABLE IF NOT EXISTS `OneTimeRegistrations` (
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
) ENGINE=InnoDB AUTO_INCREMENT=340 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PanelIdeas`
--

CREATE TABLE IF NOT EXISTS `PanelIdeas` (
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
  KEY `fk_PanelIdeas_PeopleID` (`PeopleID`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PendingAccounts`
--

CREATE TABLE IF NOT EXISTS `PendingAccounts` (
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
  `Entered` integer(1) NOT NULL,
  PRIMARY KEY (`PendingID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `People`
--

CREATE TABLE IF NOT EXISTS `People` (
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
  KEY `fk_People_ParentID` (`ParentID`)
) ENGINE=InnoDB AUTO_INCREMENT=1999 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PeopleInterests`
--

CREATE TABLE IF NOT EXISTS `PeopleInterests` (
  `PeopleID` int(8) NOT NULL,
  `Interest` varchar(20) NOT NULL,
  PRIMARY KEY (`PeopleID`,`Interest`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PermissionDetails`
--

CREATE TABLE IF NOT EXISTS `PermissionDetails` (
  `Permission` varchar(20) NOT NULL,
  `ShortName` varchar(40) NOT NULL,
  `Description` varchar(200) NOT NULL,
  `Module` varchar(50) NOT NULL,
  `ExpireAfterCon` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Permission`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `Permissions`
--

CREATE TABLE IF NOT EXISTS `Permissions` (
  `PermissonID` int(8) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(8) NOT NULL,
  `Permission` varchar(20) NOT NULL,
  `Expiration` date DEFAULT NULL,
  PRIMARY KEY (`PermissonID`),
  KEY `fk_Permissions_PeopleID` (`PeopleID`),
  KEY `fk_Permissions_Permission` (`Permission`)
) ENGINE=InnoDB AUTO_INCREMENT=576 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ProgramSurvey`
--

CREATE TABLE IF NOT EXISTS `ProgramSurvey` (
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
  KEY `fk_ProgramSurvey_PeopleID` (`PeopleID`)
) ENGINE=InnoDB AUTO_INCREMENT=238 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PromoCodes`
--

CREATE TABLE IF NOT EXISTS `PromoCodes` (
  `CodeID` int(8) NOT NULL AUTO_INCREMENT,
  `Year` int(4) NOT NULL,
  `Code` varchar(20) NOT NULL,
  `Discount` decimal(5,2) NOT NULL,
  `Expiration` date DEFAULT NULL,
  `UsesLeft` int(3) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`CodeID`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PurchasedBadges`
--

CREATE TABLE IF NOT EXISTS `PurchasedBadges` (
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
  KEY `fk_PurchasedBadges_CertificateID` (`CertificateID`)
) ENGINE=InnoDB AUTO_INCREMENT=3255 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `PurchaseHistory`
--

CREATE TABLE IF NOT EXISTS `PurchaseHistory` (
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
  KEY `fk_PurchaseHistory_PurchaserID` (`PurchaserID`)
) ENGINE=InnoDB AUTO_INCREMENT=4005 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ShoppingCart`
--

CREATE TABLE IF NOT EXISTS `ShoppingCart` (
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
  KEY `fk_ShoppingCart_CertificateID` (`CertificateID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SurveyExpertise`
--

CREATE TABLE IF NOT EXISTS `SurveyExpertise` (
  `ExpertiseID` int(8) NOT NULL AUTO_INCREMENT,
  `Expertise` varchar(50) NOT NULL,
  PRIMARY KEY (`ExpertiseID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SurveyInterests`
--

CREATE TABLE IF NOT EXISTS `SurveyInterests` (
  `InterestID` int(8) NOT NULL AUTO_INCREMENT,
  `Interest` varchar(50) NOT NULL,
  PRIMARY KEY (`InterestID`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ArtistDetails`
--
ALTER TABLE `ArtistDetails`
  ADD CONSTRAINT `fk_ArtistDetails_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL;

--
-- Constraints for table `ArtistPresence`
--
ALTER TABLE `ArtistPresence`
  ADD CONSTRAINT `fk_ArtistPresence_ArtistID` FOREIGN KEY (`ArtistID`) REFERENCES `ArtistDetails` (`ArtistID`) ON DELETE SET NULL;

--
-- Constraints for table `ArtSubmissions`
--
ALTER TABLE `ArtSubmissions`
  ADD CONSTRAINT `fk_ArtSubmissions_ArtistAttendingID` FOREIGN KEY (`ArtistAttendingID`) REFERENCES `ArtistPresence` (`ArtistAttendingID`);

--
-- Constraints for table `AvailableBadges`
--
ALTER TABLE `AvailableBadges`
  ADD CONSTRAINT `fk_AvailableBadges_BadgeTypeID` FOREIGN KEY (`BadgeTypeID`) REFERENCES `BadgeTypes` (`BadgeTypeID`);

--
-- Constraints for table `ConfirmationLinks`
--
ALTER TABLE `ConfirmationLinks`
  ADD CONSTRAINT `fk_ConfirmationLinks_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE;

--
-- Constraints for table `GiftCertificates`
--
ALTER TABLE `GiftCertificates`
  ADD CONSTRAINT `fk_GiftCertificates_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL;

--
-- Constraints for table `PanelIdeas`
--
ALTER TABLE `PanelIdeas`
  ADD CONSTRAINT `fk_PanelIdeas_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL;

--
-- Constraints for table `People`
--
ALTER TABLE `People`
  ADD CONSTRAINT `fk_People_ParentID` FOREIGN KEY (`ParentID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE;

--
-- Constraints for table `PeopleInterests`
--
ALTER TABLE `PeopleInterests`
  ADD CONSTRAINT `fk_PeopleInterests_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE;

--
-- Constraints for table `Permissions`
--
ALTER TABLE `Permissions`
  ADD CONSTRAINT `fk_Permissions_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_Permissions_Permission` FOREIGN KEY (`Permission`) REFERENCES `PermissionDetails` (`Permission`) ON UPDATE CASCADE;

--
-- Constraints for table `ProgramSurvey`
--
ALTER TABLE `ProgramSurvey`
  ADD CONSTRAINT `fk_ProgramSurvey_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE;

--
-- Constraints for table `PurchasedBadges`
--
ALTER TABLE `PurchasedBadges`
  ADD CONSTRAINT `fk_PurchasedBadges_BadgeTypeID` FOREIGN KEY (`BadgeTypeID`) REFERENCES `BadgeTypes` (`BadgeTypeID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_PurchasedBadges_CertificateID` FOREIGN KEY (`CertificateID`) REFERENCES `GiftCertificates` (`CertificateID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_PurchasedBadges_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_PurchasedBadges_PromoCodeID` FOREIGN KEY (`PromoCodeID`) REFERENCES `PromoCodes` (`CodeID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_PurchasedBadges_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL;

--
-- Constraints for table `PurchaseHistory`
--
ALTER TABLE `PurchaseHistory`
  ADD CONSTRAINT `fk_PurchaseHistory_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_PurchaseHistory_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL;

--
-- Constraints for table `ShoppingCart`
--
ALTER TABLE `ShoppingCart`
  ADD CONSTRAINT `fk_ShoppingCart_CertificateID` FOREIGN KEY (`CertificateID`) REFERENCES `GiftCertificates` (`CertificateID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ShoppingCart_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ShoppingCart_PromoCodeID` FOREIGN KEY (`PromoCodeID`) REFERENCES `PromoCodes` (`CodeID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ShoppingCart_PurchaserID` FOREIGN KEY (`PurchaserID`) REFERENCES `People` (`PeopleID`) ON DELETE SET NULL;

  
CREATE TABLE IF NOT EXISTS `ShoppingCart` (
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
  KEY `fk_ShoppingCart_CertificateID` (`CertificateID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
  
  CREATE TABLE IF NOT EXISTS Invoice (
	InvoiceID int(8) NOT NULL AUTO_INCREMENT,
	InvoiceType varchar(20) NOT NULL,
	RelatedRecordID int(8) DEFAULT NULL,
	Status enum('Created','Sent','Paid','Cancelled') NOT NULL DEFAULT 'Created',
	Created datetime NOT NULL,
	Sent datetime DEFAULT NULL,
	Fulfilled datetime DEFAULT NULL,
	LastChanged timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (InvoiceID)
  );
  
  CREATE TABLE IF NOT EXISTS InvoiceLine (
	InvoiceLineID int(8) NOT NULL AUTO_INCREMENT,
	InvoiceID int(8) NOT NULL,
	LineNumber int(4) NOT NULL,
	Description varchar(200) NOT NULL,
	Price decimal(7, 2) NOT NULL DEFAULT 0.00,
	Tax decimal(5, 2) NOT NULL DEFAULT 0.00,
	ReferenceID int(8) DEFAULT NULL,
	PRIMARY KEY (InvoiceLineID),
	KEY fk_InvoiceLine_InvoiceID (InvoiceID)
  );
  
ALTER TABLE `InvoiceLine`
  ADD CONSTRAINT `fk_InvoiceLine_InvoiceID` FOREIGN KEY (`InvoiceID`) REFERENCES `Invoice` (`InvoiceID`) ON DELETE CASCADE;
  
  
CREATE TABLE IF NOT EXISTS InvoicesPaid (
	InvoicesPaidID int(8) NOT NULL AUTO_INCREMENT,
	RecordID int(8) NOT NULL,
	InvoiceID int(8) NOT NULL,
	PRIMARY KEY (InvoicesPaidID),
	KEY fk_InvoicesPaid_InvoiceID (InvoiceID),
	KEY fk_InvoicesPaid_RecordID (RecordID)
);

ALTER TABLE `InvoicesPaid`
  ADD CONSTRAINT `fk_InvoicesPaid_InvoiceID` FOREIGN KEY (`InvoiceID`) REFERENCES `Invoice` (`InvoiceID`) ON DELETE CASCADE;
ALTER TABLE `InvoicesPaid`
  ADD CONSTRAINT `fk_InvoicesPaid_RecordID` FOREIGN KEY (`RecordID`) REFERENCES `PurchaseHistory` (`RecordID`) ON DELETE CASCADE;

  CREATE TABLE IF NOT EXISTS DealerConfig (
	DealerConfigID int(8) NOT NULL AUTO_INCREMENT,
	Year int(4) NOT NULL,
	WaitListAfterTableNum int(3) NOT NULL DEFAULT 999,
	WaitListAfterDate datetime DEFAULT NULL,
	ElectricFee decimal(5, 2) NOT NULL DEFAULT '50.00',
	BadgeFee decimal(5, 2) NOT NULL DEFAULT '45.00',
	PRIMARY KEY (DealerConfigID)
  );
  
  CREATE TABLE IF NOT EXISTS DealerTablePrices (
	TablePriceID int(8) NOT NULL AUTO_INCREMENT,
	Quantity int(2) NOT NULL,
	Price decimal(5, 2) NOT NULL,
	PRIMARY KEY (TablePriceID)
  );
  
  CREATE TABLE IF NOT EXISTS Dealer (
	DealerID int(8) NOT NULL AUTO_INCREMENT,
	PeopleID int(8) NOT NULL,
	CompanyName varchar(50) NOT NULL,
	LegalName varchar(100) NOT NULL,
	URL varchar(100) DEFAULT NULL,
	Description varchar(300) NOT NULL DEFAULT '',
	Address1 varchar(80) NOT NULL,
	Address2 varchar(80) DEFAULT NULL,
	Address3 varchar(80) DEFAULT NULL,
	City varchar(30) NOT NULL,
	State varchar(2) DEFAULT NULL,
	ZipCode varchar(10) DEFAULT NULL,
	Country varchar(20) NOT NULL DEFAULT 'USA',
	Phone varchar(20) DEFAULT NULL,
	PhoneType enum('Home','Mobile','Work','Other') DEFAULT NULL,
	TaxNumber varchar(30) DEFAULT NULL,
	Created datetime NOT NULL,
	LastChanged timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (DealerID),
	KEY fk_Dealer_PeopleID (PeopleID)
  );

ALTER TABLE `Dealer`
  ADD CONSTRAINT `fk_Dealer_PeopleID` FOREIGN KEY (`PeopleID`) REFERENCES `People` (`PeopleID`) ON DELETE CASCADE;
  
  CREATE TABLE IF NOT EXISTS DealerPresence (
	DealerPresenceID int(8) NOT NULL AUTO_INCREMENT,
	DealerID int(8) NOT NULL,
	NumTables int(2) NOT NULL,
	ElectricalNeeded tinyint(1) NOT NULL DEFAULT 0,
	AddedDetails varchar(500) NOT NULL DEFAULT '',
	Status enum('Pending','Approved','Rejected','Waitlist') NOT NULL DEFAULT 'Pending',
	Created datetime NOT NULL,
	LastChanged timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (DealerPresenceID),
	KEY fk_DealerPresence_DealerID (DealerID)
  );
  
ALTER TABLE `DealerPresence`
  ADD CONSTRAINT `fk_DealerPresence_DealerID` FOREIGN KEY (`DealerID`) REFERENCES `Dealer` (`DealerID`) ON DELETE CASCADE;
    
  CREATE TABLE IF NOT EXISTS DealerBadges (
	DealerBadgeID int(8) NOT NULL AUTO_INCREMENT,
	DealerPresenceID int(8) NOT NULL,
	BadgeName varchar(40) NOT NULL,
	FirstName varchar(50) NOT NULL,
	LastName varchar(50) NOT NULL,
	Price decimal(5, 2) NOT NULL,
	BadgeTypeID int(8) NOT NULL,
	PRIMARY KEY (DealerBadgeID),
	KEY fk_DealerBadges_DealerPresenceID (DealerPresenceID),
	KEY fk_DealerBadges_BadgeTypeID (BadgeTypeID)
  );
  
ALTER TABLE `DealerBadges`
  ADD CONSTRAINT `fk_DealerBadges_DealerPresenceID` FOREIGN KEY (`DealerPresenceID`) REFERENCES `DealerPresence` (`DealerPresenceID`) ON DELETE CASCADE;
ALTER TABLE `DealerBadges`
  ADD CONSTRAINT `fk_DealerBadges_BadgeTypeID` FOREIGN KEY (`BadgeTypeID`) REFERENCES `BadgeTypes` (`BadgeTypeID`) ON DELETE SET NULL;
  
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


