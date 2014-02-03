-- phpMyAdmin SQL Dump
-- version 3.3.8
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 26, 2011 at 04:52 PM
-- Server version: 5.1.51
-- PHP Version: 5.3.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `tdproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE IF NOT EXISTS `address` (
  `address_id` int(10) NOT NULL AUTO_INCREMENT,
  `country_id_fk` int(11) NOT NULL,
  `state` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postcode` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `number` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `post_office_box` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `address_idx_01` (`country_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `address`
--


-- --------------------------------------------------------

--
-- Table structure for table `address_type`
--

CREATE TABLE IF NOT EXISTS `address_type` (
  `address_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`address_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `address_type`
--

INSERT INTO `address_type` (`address_type_id`, `name`) VALUES
(1, 'privat'),
(2, 'geschäftlich');

-- --------------------------------------------------------

--
-- Table structure for table `assertion`
--

CREATE TABLE IF NOT EXISTS `assertion` (
  `assertion_id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `include_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`assertion_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `assertion`
--


-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `company_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id_fk` int(10) DEFAULT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `additional_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefax` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `customer_number` varchar(25) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`company_id`),
  KEY `company_idx_01` (`company_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `company`
--


-- --------------------------------------------------------

--
-- Table structure for table `company_address`
--

CREATE TABLE IF NOT EXISTS `company_address` (
  `company_address_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id_fk` int(10) NOT NULL,
  `address_id_fk` int(10) NOT NULL,
  `address_type_id_fk` int(10) NOT NULL,
  PRIMARY KEY (`company_address_id`),
  KEY `company_address_idx_01` (`company_id_fk`),
  KEY `company_address_idx_02` (`address_id_fk`),
  KEY `company_address_idx_03` (`address_type_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `company_address`
--


-- --------------------------------------------------------

--
-- Table structure for table `company_note`
--

CREATE TABLE IF NOT EXISTS `company_note` (
  `company_note_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id_fk` int(10) NOT NULL,
  `note_id_fk` int(10) NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`company_note_id`),
  KEY `company_note_idx_01` (`company_id_fk`),
  KEY `company_note_idx_02` (`note_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `company_note`
--


-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `country_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `indicator` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`country_id`, `name`, `indicator`) VALUES
(1, 'Deutschland', 'D'),
(2, 'Österreich', 'A'),
(4, 'Indien', 'IN'),
(3, 'Schweiz', 'CH');

-- --------------------------------------------------------

--
-- Table structure for table `estimation`
--

CREATE TABLE IF NOT EXISTS `estimation` (
  `estimation_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id_fk` int(10) NOT NULL,
  `task_id_fk` int(10) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `unit` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `complexity` varchar(25) COLLATE utf8_unicode_ci NOT NULL,
  `quantity` int(10) NOT NULL,
  `minimum` int(10) NOT NULL,
  `normal` int(10) NOT NULL,
  `maximum` int(10) NOT NULL,
  `average` int(10) NOT NULL,
  PRIMARY KEY (`estimation_id`),
  UNIQUE KEY `estimation_uidx_01` (`user_id_fk`,`task_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `estimation`
--


-- --------------------------------------------------------

--
-- Table structure for table `note`
--

CREATE TABLE IF NOT EXISTS `note` (
  `note_id` int(10) NOT NULL AUTO_INCREMENT,
  `note_type_id_fk` int(10) NOT NULL,
  `create_user_id_fk` int(10) NOT NULL,
  `remind_user_id_fk` int(10) NOT NULL,
  `created_at` int(10) NOT NULL,
  `remind_at` int(10) NOT NULL,
  `subject` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `note` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`note_id`),
  KEY `note_idx_01` (`note_type_id_fk`),
  KEY `note_idx_02` (`create_user_id_fk`),
  KEY `note_idx_03` (`remind_user_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `note`
--


-- --------------------------------------------------------

--
-- Table structure for table `note_type`
--

CREATE TABLE IF NOT EXISTS `note_type` (
  `note_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`note_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `note_type`
--

INSERT INTO `note_type` (`note_type_id`, `name`) VALUES
(1, 'Erinnerung'),
(2, 'Nachtelefonieren'),
(3, 'Anschreiben');

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE IF NOT EXISTS `person` (
  `person_id` int(10) NOT NULL AUTO_INCREMENT,
  `company_id_fk` int(10) DEFAULT NULL,
  `salutation_id_fk` int(10) DEFAULT NULL,
  `user_id_fk` int(10) DEFAULT NULL,
  `position` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstname` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefax` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`person_id`),
  KEY `person_idx_01` (`company_id_fk`),
  KEY `person_idx_02` (`salutation_id_fk`),
  KEY `person_idx_03` (`user_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `person`
--


-- --------------------------------------------------------

--
-- Table structure for table `person_address`
--

CREATE TABLE IF NOT EXISTS `person_address` (
  `person_address_id` int(10) NOT NULL AUTO_INCREMENT,
  `person_id_fk` int(10) NOT NULL,
  `address_id_fk` int(10) NOT NULL,
  `address_type_id_fk` int(10) NOT NULL,
  PRIMARY KEY (`person_address_id`),
  KEY `person_address_idx_01` (`person_id_fk`),
  KEY `person_address_idx_02` (`address_id_fk`),
  KEY `person_address_idx_03` (`address_type_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `person_address`
--


-- --------------------------------------------------------

--
-- Table structure for table `person_note`
--

CREATE TABLE IF NOT EXISTS `person_note` (
  `person_note_id` int(10) NOT NULL AUTO_INCREMENT,
  `person_id_fk` int(10) NOT NULL,
  `note_id_fk` int(10) NOT NULL,
  `reason` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`person_note_id`),
  KEY `person_note_idx_01` (`person_id_fk`),
  KEY `person_note_idx_02` (`note_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `person_note`
--


-- --------------------------------------------------------

--
-- Table structure for table `person_project`
--

CREATE TABLE IF NOT EXISTS `person_project` (
  `person_project_id` int(10) NOT NULL AUTO_INCREMENT,
  `person_id_fk` int(10) NOT NULL,
  `project_id_fk` int(10) NOT NULL,
  PRIMARY KEY (`person_project_id`),
  KEY `person_project_idx_01` (`person_id_fk`),
  KEY `person_project_idx_02` (`project_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `person_project`
--


-- --------------------------------------------------------

--
-- Table structure for table `project`
--

CREATE TABLE IF NOT EXISTS `project` (
  `project_id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id_fk` int(10) DEFAULT NULL,
  `company_id_fk` int(10) NOT NULL,
  `template_id_fk` int(10) NOT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`project_id`),
  KEY `project_idx_01` (`project_id_fk`),
  KEY `project_idx_02` (`company_id_fk`),
  KEY `project_idx_03` (`template_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `project`
--


-- --------------------------------------------------------

--
-- Table structure for table `project_task`
--

CREATE TABLE IF NOT EXISTS `project_task` (
  `project_task_id` int(10) NOT NULL AUTO_INCREMENT,
  `project_id_fk` int(10) NOT NULL,
  `task_id_fk` int(10) NOT NULL,
  PRIMARY KEY (`project_task_id`),
  KEY `project_task_idx_01` (`project_id_fk`),
  KEY `project_task_idx_02` (`task_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `project_task`
--


-- --------------------------------------------------------

--
-- Table structure for table `project_user`
--

CREATE TABLE IF NOT EXISTS `project_user` (
  `project_user_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id_fk` int(11) NOT NULL,
  `user_id_fk` int(11) NOT NULL,
  PRIMARY KEY (`project_user_id`),
  KEY `project_user_idx_01` (`project_id_fk`),
  KEY `project_user_idx_02` (`user_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `project_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE IF NOT EXISTS `report` (
  `report_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `description` tinytext COLLATE utf8_unicode_ci NOT NULL,
  `unit` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`report_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`report_id`, `name`, `description`, `unit`) VALUES
(1, 'Accounts', 'Accountübersicht (JasperServer Testbericht)', '/reports/samples/AllAccounts'),
(2, 'Projektliste', 'Test', '/reports/samples/projectlist'),
(3, 'Taskliste', 'Taskliste, gruppiert nach Projekt.', '/reports/tdproject/task_overview');

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `resource_id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_locale` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `message` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`resource_id`),
  UNIQUE KEY `resource_uidx_01` (`key`,`resource_locale`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=886 ;

--
-- Dumping data for table `resource`
--

INSERT INTO `resource` (`resource_id`, `resource_locale`, `key`, `message`) VALUES
(35, 'en_US', 'page.content.tabs.resource.grid.footer.row.actions', 'Actions'),
(453, 'en_US', 'page.content.tabs.account.account.userIdFk', 'Account'),
(481, 'de_DE', 'page.content.tabs.address.addressGrid.header.row.addressTypeIdFk', 'Typ'),
(19, 'en_US', 'page.content.tabs.company.grid.header.row.actions', 'Actions'),
(111, 'en_US', 'page.content.tabs.logging.grid.footer.row.projectName', 'Project'),
(337, 'en_US', 'page.content.tabs.address.addressGrid.footer.row.addressIdFk', ''),
(603, 'de_DE', 'page.content.project.user.userGrid.header.row.userRole', 'Rolle'),
(661, 'en_US', 'page.content.tabs.reports.reports.unit', 'Unit'),
(745, 'en_US', 'page.navigation.root.setting.user', 'Users'),
(84, 'en_US', 'page.content.tabs.note.grid.footer.row.actions', 'Actions'),
(132, 'de_DE', 'page.content.tabs.resource.grid.header.row.key', 'Key'),
(542, 'en_US', 'page.content.tabs.companies.companyGrid.footer.row.name', 'Name'),
(548, 'de_DE', 'page.content.tabs.person.grid.header.row.actions', 'Aktionen'),
(653, 'de_DE', 'page.content.tabs.report.grid.footer.row.name', 'Name'),
(807, 'en_US', 'page.content.toolbar.reorg', 'Reorg'),
(5, 'de_DE', 'page.content.tabs.company.grid.header.row.email', 'E-Mail'),
(88, 'en_US', 'page.content.tabs.person.grid.header.row.fullName', 'Name'),
(116, 'en_US', 'page.content.tabs.logging.logging', 'Logging'),
(155, 'en_US', 'page.content.project.project', 'Project'),
(465, 'de_DE', 'page.content.tabs.employees.employeeGrid.header.row.personIdFk', ''),
(454, 'de_DE', 'page.content.tabs.company.company', 'Stammdaten'),
(476, 'de_DE', 'page.content.tabs.employees.employeeGrid.footer.row.actions', 'Aktionen'),
(612, 'de_DE', 'content.task.description', 'Beschreibung'),
(626, 'en_US', 'content.make-estimation.quantity', 'Quantity'),
(692, 'de_DE', 'page.content.tabs.smtp.smtp.useSmtp', 'Verwenden'),
(715, 'de_DE', 'page.content.tabs.ldap.ldap.syncedAt', 'Zuletzt synchronisiert am'),
(837, 'de_DE', 'page.content.tabs.userperformance.grid.footer.row.actions', 'Actions'),
(80, 'en_US', 'page.content.tabs.note.grid.footer.row.noteTypeName', 'Type'),
(203, 'en_US', 'page.content.tabs.template.grid.header.row.templateId', 'ID'),
(569, 'de_DE', 'page.content.tabs.account.account.userIdFk', 'Benutzerkonto'),
(813, 'en_US', 'page.content.tabs.userperformance.grid.header.row.actions', 'Actions'),
(109, 'en_US', 'page.content.tabs.logging.grid.header.row.until', 'until'),
(332, 'en_US', 'page.content.tabs.address.addressGrid.header.row.addressIdFk', ''),
(507, 'de_DE', 'page.content.tabs.note.note.note', 'Nachricht'),
(757, 'de_DE', 'page.navigation.root.logging.project', 'Projekte'),
(855, 'de_DE', 'page.content.tabsUserStatistic.user.performance.userTurnover', 'Turnover'),
(92, 'en_US', 'page.content.tabs.person.grid.footer.row.personId', 'ID'),
(235, 'en_US', 'page.content.tabs.company.company.name', 'Name'),
(598, 'de_DE', 'page.content.project.project.project.companyIdFk', 'Kunde'),
(600, 'de_DE', 'page.content.project.user.userGrid.header.row.userIdFk', ''),
(503, 'de_DE', 'page.content.tabs.note.grid.footer.row.actions', 'Aktionen'),
(590, 'en_US', 'page.content.project.project.project.projectIdFk', 'Parent Project'),
(597, 'de_DE', 'page.content.project.project.project.projectIdFk', 'Übergeordnetes Projekt'),
(712, 'de_DE', 'page.content.tabs.user.user.enabled', 'Aktiv'),
(535, 'en_US', 'page.content.tabs.personen.personGrid.footer.row.fullName', 'Name'),
(573, 'de_DE', 'page.content.tabs.logging.logging.from', 'von'),
(696, 'de_DE', 'page.content.tabs.smtp.smtp.smtpPassword', 'Passwort'),
(54, 'en_US', 'page.content.tabs.employees.employeeGrid.footer.row.fullName', 'Name'),
(97, 'en_US', 'page.content.tabs.person.person', 'Base Data'),
(179, 'en_US', 'page.content.tabs.user.grid.footer.row.username', 'Username'),
(466, 'de_DE', 'page.content.tabs.employees.employeeGrid.header.row.personId', 'ID'),
(636, 'de_DE', 'page.content.tabs.template.grid.header.row.templateId', 'ID'),
(118, 'de_DE', 'page.content.toolbar.editLogging', 'Zeit bearbeiten'),
(27, 'en_US', 'page.content.tabs.resource.grid.header.row.key', 'Key'),
(103, 'en_US', 'page.content.toolbar.editLogging', 'Edit Logging'),
(181, 'en_US', 'page.content.tabs.user.grid.footer.row.actions', 'Actions'),
(646, 'de_DE', 'page.content.tabs.template.template.description', 'Beschreibung'),
(815, 'en_US', 'page.content.tabs.userperformance.grid.footer.row.roleName', 'Role'),
(835, 'de_DE', 'page.content.tabs.userperformance.grid.footer.row.username', 'Username'),
(610, 'de_DE', 'page.content.project.estimation', 'Schätzungen'),
(607, 'de_DE', 'page.content.project.user.userGrid.footer.row.userRole', 'Rolle'),
(681, 'de_DE', 'page.content.tabs.settings.settings', 'Einstellungen'),
(856, 'en_US', 'page.content.tabs.userperformance.grid.header.row.actualPerformance', 'Actual'),
(146, 'en_US', 'page.content.tabs.project', 'Projects'),
(532, 'en_US', 'page.content.tabs.personen.personGrid.header.row.fullName', 'Name'),
(836, 'de_DE', 'page.content.tabs.userperformance.grid.footer.row.userPerformance', 'Performance'),
(666, 'en_US', 'page.content.tabs.resources.resources.key', 'Key'),
(677, 'en_US', 'page.content.tabs.smtp.smtp.smtpPort', 'Port'),
(764, 'en_US', 'page.content.tabs.user.user.contractedHours', 'Contracted Hours'),
(95, 'en_US', 'page.content.tabs.person.grid.footer.row.phone', 'Phone'),
(516, 'de_DE', 'page.content.tabs.personen.personGrid.footer.row.fullName', 'Name'),
(579, 'en_US', 'page.content.tabs.logging.logging.description', 'Description'),
(657, 'de_DE', 'page.content.tabs.reports.reports.name', 'Name'),
(868, 'de_DE', 'content.address.countryIdFk', 'Land'),
(7, 'de_DE', 'page.content.tabs.company.grid.header.row.actions', 'Aktionen'),
(133, 'de_DE', 'page.content.tabs.resource.grid.header.row.locale', 'Locale'),
(139, 'de_DE', 'page.content.tabs.resource.grid.footer.row.message', 'Value'),
(242, 'en_US', 'page.content.tabs.company.external.customerNumber', 'Customer Number'),
(477, 'de_DE', 'page.content.tabs.address', 'Adressen'),
(762, 'de_DE', 'page.navigation.root.setting.setting', 'Einstellungen'),
(658, 'de_DE', 'page.content.tabs.reports.reports.unit', 'Unit'),
(687, 'de_DE', 'page.content.tabs.ldap.ldap.ldapBindRequired', 'Bind erforderlich'),
(94, 'en_US', 'page.content.tabs.person.grid.footer.row.email', 'Mail'),
(563, 'de_DE', 'page.content.tabs.contact-data.contact-dat.email', 'E-Mail'),
(585, 'de_DE', 'page.content.tabs.project.grid.footer.row.projectId', 'ID'),
(61, 'en_US', 'page.content.tabs.address.addressGrid.header.row.fullAddress', 'Address'),
(176, 'en_US', 'page.content.tabs.user.grid.header.row.actions', 'Actions'),
(463, 'de_DE', 'page.content.tabs.company.external.customerNumber', 'Kundennummer'),
(554, 'de_DE', 'page.content.tabs.person.person', 'Stammdaten'),
(568, 'de_DE', 'page.content.tabs.account.account', 'Benutzerkonto'),
(629, 'de_DE', 'content.make-estimation', 'Schätzung abgeben'),
(22, 'en_US', 'page.content.tabs.company.grid.footer.row.email', 'Mail'),
(39, 'en_US', 'page.content.toolbar.save', 'Save'),
(83, 'en_US', 'page.content.tabs.note.grid.footer.row.remindAt', 'due to'),
(648, 'de_DE', 'page.content.tabs.report', 'Berichte'),
(755, 'de_DE', 'page.navigation.root.logging', 'Projekte'),
(809, 'en_US', 'page.content.tabs.userperformance.grid.header.row.userId', 'ID'),
(834, 'de_DE', 'page.content.tabs.userperformance.grid.footer.row.roleName', 'Role'),
(14, 'en_US', 'page.content.tabs.company', 'Companies'),
(147, 'en_US', 'page.content.tabs.project.grid.header.row.projectId', 'ID'),
(801, 'en_US', 'page.navigation.root.user.performance.user.performance', 'User Performance'),
(3, 'de_DE', 'page.content.tabs.company.grid.header.row.companyId', 'ID'),
(9, 'de_DE', 'page.content.tabs.company.grid.footer.row.name', 'Name'),
(28, 'en_US', 'page.content.tabs.resource.grid.header.row.locale', 'Locale'),
(166, 'en_US', 'page.content.project.logging', 'Loggings'),
(241, 'en_US', 'page.content.tabs.company.company.companyIdFk', 'Related Company'),
(609, 'de_DE', 'page.content.project.task', 'Tasks'),
(846, 'en_US', 'content.address.countryIdFk', 'Country'),
(43, 'en_US', 'page.content.tabs.company.company', 'Base Data'),
(60, 'en_US', 'page.content.tabs.address.addressGrid.header.row.addressId', 'ID'),
(87, 'en_US', 'page.content.tabs.person.grid.header.row.personId', 'ID'),
(509, 'de_DE', 'page.content.tabs.note.note.noteTypeIdFk', 'Typ'),
(511, 'de_DE', 'page.content.tabs.personen.personGrid.header.row.personIdFk', ''),
(540, 'en_US', 'page.content.tabs.companies.companyGrid.footer.row.companyIdFk', ''),
(560, 'de_DE', 'page.content.tabs.person.person.companyIdFk', 'Unternehmen'),
(143, 'de_DE', 'page.content.toolbar.back', 'Zurück'),
(173, 'en_US', 'page.content.tabs.user.grid.header.row.roleName', 'Role'),
(601, 'de_DE', 'page.content.project.user.userGrid.header.row.userId', 'ID'),
(744, 'en_US', 'page.navigation.root.setting.setting', 'Settings'),
(843, 'en_US', 'content.address.postcode', 'Postcoed'),
(239, 'en_US', 'page.content.tabs.company.company.telefax', 'Telefax'),
(594, 'de_DE', 'page.content.project.project', 'Projekt'),
(802, 'de_DE', 'page.navigation.root.user.performance.user.performance', 'Benutzer Performance'),
(857, 'en_US', 'page.content.tabs.userperformance.grid.footer.row.actualPerformance', 'Actual'),
(44, 'en_US', 'page.content.tabs.company.external', 'External'),
(48, 'en_US', 'page.content.tabs.employees.employeeGrid.header.row.fullName', 'Name'),
(74, 'en_US', 'page.content.tabs.note.grid.header.row.createUserUsername', 'from'),
(167, 'en_US', 'page.content.project.task', 'Tasks'),
(475, 'de_DE', 'page.content.tabs.employees.employeeGrid.footer.row.phone', 'Telefon'),
(559, 'de_DE', 'page.content.tabs.person.person.position', 'Position'),
(679, 'en_US', 'page.content.tabs.smtp.smtp.smtpPassword', 'Password'),
(804, 'de_DE', 'page.navigation.root.statistic', 'Statistiken'),
(42, 'en_US', 'title.resource.overview', 'Translations'),
(68, 'en_US', 'page.content.tabs.address.addressGrid.footer.row.actions', 'Actions'),
(126, 'de_DE', 'page.content.tabs.logging.grid.footer.row.projectName', 'Projekt'),
(306, 'en_US', 'title.project.view.toolbar', 'title.project.view.toolbar'),
(524, 'en_US', 'page.content.tabs.note.note.subject', 'Subject'),
(589, 'en_US', 'page.content.project.project.project.name', 'Name'),
(616, 'en_US', 'content.task', 'Edit Task'),
(131, 'de_DE', 'page.content.tabs.resource.grid.header.row.resourceId', 'ID'),
(451, 'en_US', 'page.content.tabs.contact-data.contact-dat.telefax', 'Telefax'),
(850, 'de_DE', 'page.content.tabsUserStatistic.user.performance.userPerformance', 'Monthly'),
(488, 'de_DE', 'page.content.toolbar.createAddress', 'Neue Adresse'),
(553, 'de_DE', 'page.content.tabs.person.grid.footer.row.actions', 'Aktionen'),
(702, 'de_DE', 'page.content.tabs.user.grid.header.row.actions', 'Actions'),
(153, 'en_US', 'page.content.tabs.project.grid.footer.row.companyName', 'Customer'),
(675, 'en_US', 'page.content.tabs.smtp.smtp.useSmtp', 'Use'),
(825, 'de_DE', 'title.user.performance.overview', 'Benutzer Performance'),
(89, 'en_US', 'page.content.tabs.person.grid.header.row.email', 'Mail'),
(130, 'de_DE', 'page.content.tabs.resource', 'tab.label.resource'),
(588, 'de_DE', 'page.content.tabs.project.grid.footer.row.actions', 'Aktionen'),
(606, 'de_DE', 'page.content.project.user.userGrid.footer.row.username', 'Benutzername'),
(718, 'en_US', 'page.content.tabs.user.user.rate', 'Internal Price'),
(810, 'en_US', 'page.content.tabs.userperformance.grid.header.row.roleName', 'Role'),
(190, 'en_US', 'page.content.tabs.report.grid.header.row.reportId', 'ID'),
(518, 'de_DE', 'page.content.tabs.companies.companyGrid.header.row.companyIdFk', ''),
(748, 'en_US', 'page.navigation.root.logging.project', 'Projects'),
(78, 'en_US', 'page.content.tabs.note.grid.footer.row.noteId', 'ID'),
(479, 'de_DE', 'page.content.tabs.address.addressGrid.header.row.addressId', 'ID'),
(515, 'de_DE', 'page.content.tabs.personen.personGrid.footer.row.personId', 'ID'),
(159, 'en_US', 'page.content.project.user.userGrid.header.row.userId', 'ID'),
(627, 'en_US', 'content.toolbar.saveEstimation', 'Save Estimation'),
(689, 'de_DE', 'page.content.tabs.ldap.ldap.ldapDn', 'Dn'),
(10, 'de_DE', 'page.content.tabs.company.grid.footer.row.email', 'E-Mail'),
(76, 'en_US', 'page.content.tabs.note.grid.header.row.remindAt', 'due to'),
(443, 'en_US', 'page.content.tabs.person.person.salutationIdFk', 'Salutation'),
(655, 'de_DE', 'page.content.tabs.reports', 'Bericht'),
(703, 'de_DE', 'page.content.tabs.user.grid.footer.row.userId', 'ID'),
(823, 'de_DE', 'tab.label.user.performance', 'Benutzer Performance'),
(41, 'en_US', 'title.resource.view', 'Translation'),
(70, 'en_US', 'page.content.tabs.note', 'Notes'),
(461, 'de_DE', 'page.content.tabs.company.company.companyIdFk', 'Verknüpftes Unternehmen'),
(514, 'de_DE', 'page.content.tabs.personen.personGrid.footer.row.personIdFk', ''),
(651, 'de_DE', 'page.content.tabs.report.grid.header.row.actions', 'Aktionen'),
(498, 'de_DE', 'page.content.tabs.note.grid.footer.row.subject', 'Betreff'),
(445, 'en_US', 'page.content.tabs.person.person.lastname', 'Lastname'),
(500, 'de_DE', 'page.content.tabs.note.grid.footer.row.createUserUsername', 'von'),
(621, 'en_US', 'content.estimations-made', 'Estimations Made'),
(851, 'de_DE', 'page.content.tabs.userperformance.grid.header.row.actualPerformance', 'Actual'),
(115, 'en_US', 'page.content.tabs.logging.logged', 'My Loggings'),
(632, 'de_DE', 'content.make-estimation.complexity', 'Komplexität'),
(58, 'en_US', 'page.content.tabs.address', 'Addresses'),
(204, 'en_US', 'page.content.tabs.template.grid.header.row.name', 'Name'),
(806, 'de_DE', 'page.navigation.root.statistic.user.performance', 'Benutzer Performance'),
(525, 'en_US', 'page.content.tabs.note.note.remindAt', 'Remind at'),
(660, 'en_US', 'page.content.tabs.reports.reports.name', 'Name'),
(667, 'en_US', 'page.content.tabs.resources.resources.locale', 'Locale'),
(759, 'de_DE', 'page.navigation.root.report', 'Berichte'),
(841, 'en_US', 'content.address.number', 'House Number'),
(30, 'en_US', 'page.content.tabs.resource.grid.header.row.actions', 'Actions'),
(114, 'en_US', 'page.content.tabs.logging.grid.footer.row.until', 'until'),
(547, 'de_DE', 'page.content.tabs.person.grid.header.row.phone', 'Telefon'),
(608, 'de_DE', 'page.content.project.logging', 'Zeiten'),
(854, 'de_DE', 'page.content.tabsUserStatistic.user.performance.userCosts', 'Costs'),
(33, 'en_US', 'page.content.tabs.resource.grid.footer.row.locale', 'Locale'),
(129, 'de_DE', 'page.content.tabs.logging.grid.footer.row.until', 'bis'),
(194, 'en_US', 'page.content.tabs.report.grid.footer.row.name', 'Name'),
(195, 'en_US', 'page.content.tabs.report.grid.footer.row.actions', 'Actions'),
(446, 'en_US', 'page.content.tabs.person.person.title', 'Title'),
(474, 'de_DE', 'page.content.tabs.employees.employeeGrid.footer.row.email', 'E-Mail'),
(604, 'de_DE', 'page.content.project.user.userGrid.footer.row.userIdFk', ''),
(686, 'de_DE', 'page.content.tabs.ldap.ldap.ldapHost', 'Host'),
(831, 'de_DE', 'page.content.tabs.userperformance.grid.header.row.userPerformance', 'Performance'),
(91, 'en_US', 'page.content.tabs.person.grid.header.row.actions', 'Actions'),
(100, 'en_US', 'page.content.tabs.account', 'Account'),
(521, 'de_DE', 'page.content.tabs.companies.companyGrid.footer.row.companyIdFk', ''),
(645, 'de_DE', 'page.content.tabs.template.template.name', 'Name'),
(171, 'en_US', 'page.content.tabs.user', 'Users'),
(575, 'de_DE', 'page.content.tabs.logging.logging.description', 'Beschreibung'),
(760, 'de_DE', 'page.navigation.root.setting', 'System'),
(62, 'en_US', 'page.content.tabs.address.addressGrid.header.row.addressTypeIdFk', 'Type'),
(160, 'en_US', 'page.content.project.user.userGrid.header.row.username', 'Username'),
(237, 'en_US', 'page.content.tabs.company.company.email', 'Mail'),
(459, 'de_DE', 'page.content.tabs.company.company.telefax', 'Telefax'),
(502, 'de_DE', 'page.content.tabs.note.grid.footer.row.remindAt', 'fällig'),
(682, 'de_DE', 'page.content.tabs.settings.settings.emailSupport', 'E-Mail Support'),
(722, 'en_US', 'page.content.tabs.ldap.ldap.syncedAt', 'Last Synchronization'),
(210, 'en_US', 'page.content.tabs.tasks', 'Tasks'),
(529, 'en_US', 'page.content.tabs.personen', 'Persons'),
(6, 'de_DE', 'page.content.tabs.company.grid.header.row.phone', 'Telefon'),
(492, 'de_DE', 'page.content.tabs.note.grid.header.row.noteTypeName', 'Typ'),
(493, 'de_DE', 'page.content.tabs.note.grid.header.row.createUserUsername', 'von'),
(519, 'de_DE', 'page.content.tabs.companies.companyGrid.header.row.companyId', 'ID'),
(561, 'de_DE', 'page.content.tabs.contact-data', 'Kontaktdaten'),
(581, 'de_DE', 'page.content.tabs.project.grid.header.row.projectId', 'ID'),
(826, 'de_DE', 'page.content.toolbar.reorg', 'Reorg'),
(829, 'de_DE', 'page.content.tabs.userperformance.grid.header.row.roleName', 'Role'),
(450, 'en_US', 'page.content.tabs.contact-data.contact-dat.phone', 'Phone'),
(544, 'de_DE', 'page.content.tabs.person.grid.header.row.personId', 'ID'),
(552, 'de_DE', 'page.content.tabs.person.grid.footer.row.phone', 'Telefon'),
(708, 'de_DE', 'page.content.tabs.user.user', 'Stammdaten'),
(8, 'de_DE', 'page.content.tabs.company.grid.footer.row.companyId', 'ID'),
(464, 'de_DE', 'page.content.tabs.employees', 'Mitarbeiter'),
(478, 'de_DE', 'page.content.tabs.address.addressGrid.header.row.addressIdFk', ''),
(510, 'de_DE', 'page.content.tabs.personen', 'Personen'),
(721, 'en_US', 'page.content.tabs.ldap.ldap.ldapSynced', 'Synchronized'),
(751, 'de_DE', 'page.navigation.root.note.company', 'Unternehmen'),
(821, 'en_US', 'page.content.tabsUserStatistic.user.performance.userPerformance', 'Monthly'),
(593, 'en_US', 'page.content.project.user.userGrid.footer.row.userIdFk', ''),
(683, 'de_DE', 'page.content.tabs.ldap', 'LDAP'),
(50, 'en_US', 'page.content.tabs.employees.employeeGrid.header.row.phone', 'Phone'),
(448, 'en_US', 'page.content.tabs.person.person.companyIdFk', 'Company'),
(551, 'de_DE', 'page.content.tabs.person.grid.footer.row.email', 'E-Mail'),
(747, 'en_US', 'page.navigation.root.logging.logging', 'Logging'),
(839, 'en_US', 'content.address', 'Edit Address'),
(840, 'en_US', 'content.address.street', 'Street'),
(26, 'en_US', 'page.content.tabs.resource.grid.header.row.resourceId', 'ID'),
(47, 'en_US', 'page.content.tabs.employees.employeeGrid.header.row.personId', 'ID'),
(55, 'en_US', 'page.content.tabs.employees.employeeGrid.footer.row.email', 'Mail'),
(93, 'en_US', 'page.content.tabs.person.grid.footer.row.fullName', 'Name'),
(107, 'en_US', 'page.content.tabs.logging.grid.header.row.taskName', 'Task'),
(187, 'en_US', 'page.content.tabs.smtp', 'SMTP'),
(209, 'en_US', 'page.content.tabs.template.template', 'Template'),
(556, 'de_DE', 'page.content.tabs.person.person.firstname', 'Vorname'),
(822, 'en_US', 'tab.label.user.performance', 'User Performance'),
(135, 'de_DE', 'page.content.tabs.resource.grid.header.row.actions', 'Actions'),
(456, 'de_DE', 'page.content.tabs.company.company.additionalName', 'Namenszusatz'),
(506, 'de_DE', 'page.content.tabs.note.note.remindAt', 'Erinnern am'),
(522, 'de_DE', 'page.content.tabs.companies.companyGrid.footer.row.companyId', 'ID'),
(558, 'de_DE', 'page.content.tabs.person.person.title', 'Titel'),
(202, 'en_US', 'page.content.tabs.template', 'Templates'),
(595, 'de_DE', 'page.content.project.project.project', 'Projekt'),
(622, 'en_US', 'content.make-estimation', 'Make an Estimation'),
(688, 'de_DE', 'page.content.tabs.ldap.ldap.ldapDomain', 'Domain'),
(12, 'de_DE', 'page.content.tabs.company.grid.footer.row.actions', 'Aktionen'),
(197, 'en_US', 'page.content.tabs.reports.reports', 'Report'),
(533, 'en_US', 'page.content.tabs.personen.personGrid.footer.row.personIdFk', ''),
(564, 'de_DE', 'page.content.tabs.contact-data.contact-dat.phone', 'Telefon'),
(659, 'de_DE', 'page.content.tabs.reports.reports.description', 'Beschreibung'),
(870, 'de_DE', 'page.content.project.project.project.templateIdFk', 'Template'),
(51, 'en_US', 'page.content.tabs.employees.employeeGrid.header.row.actions', 'Actions'),
(586, 'de_DE', 'page.content.tabs.project.grid.footer.row.name', 'Name'),
(642, 'en_US', 'page.content.tabs.template.template.name', 'Name'),
(151, 'en_US', 'page.content.tabs.project.grid.footer.row.projectId', 'ID'),
(460, 'de_DE', 'page.content.tabs.company.company.website', 'Webseite'),
(482, 'de_DE', 'page.content.tabs.address.addressGrid.header.row.actions', 'Aktionen'),
(513, 'de_DE', 'page.content.tabs.personen.personGrid.header.row.fullName', 'Name'),
(531, 'en_US', 'page.content.tabs.personen.personGrid.header.row.personId', 'ID'),
(578, 'en_US', 'page.content.tabs.logging.logging.until', 'until'),
(630, 'de_DE', 'content.make-estimation.description', 'Beschreibung'),
(824, 'en_US', 'title.user.performance.overview', 'User Performance'),
(833, 'de_DE', 'page.content.tabs.userperformance.grid.footer.row.userId', 'ID'),
(152, 'en_US', 'page.content.tabs.project.grid.footer.row.name', 'Name'),
(761, 'de_DE', 'page.navigation.root.setting.resource', 'Übersetzungen'),
(803, 'en_US', 'page.navigation.root.statistic', 'Statistics'),
(845, 'en_US', 'content.address.postOfficeBox', 'Post Office Box'),
(862, 'de_DE', 'content.address.street', 'Strasse'),
(29, 'en_US', 'page.content.tabs.resource.grid.header.row.message', 'Value'),
(105, 'en_US', 'page.content.tabs.logging.grid.header.row.taskUserId', 'ID'),
(168, 'en_US', 'page.content.project.estimation', 'Estimations'),
(178, 'en_US', 'page.content.tabs.user.grid.footer.row.roleName', 'Role'),
(497, 'de_DE', 'page.content.tabs.note.grid.footer.row.noteId', 'ID'),
(848, 'de_DE', 'page.content.tabsUserStatistic.user', 'User'),
(16, 'en_US', 'page.content.tabs.company.grid.header.row.name', 'Name'),
(37, 'en_US', 'page.content.tabs.resources.resources', 'Resources'),
(495, 'de_DE', 'page.content.tabs.note.grid.header.row.remindAt', 'fällig'),
(649, 'de_DE', 'page.content.tabs.report.grid.header.row.reportId', 'ID'),
(652, 'de_DE', 'page.content.tabs.report.grid.footer.row.reportId', 'ID'),
(690, 'de_DE', 'page.content.tabs.smtp', 'SMTP'),
(731, 'en_US', 'page.navigation.root.note', 'ERP'),
(716, 'en_US', 'page.content.tabs.user.user.username', 'Username'),
(743, 'en_US', 'page.navigation.root.note.note', 'Notes'),
(191, 'en_US', 'page.content.tabs.report.grid.header.row.name', 'Name'),
(462, 'de_DE', 'page.content.tabs.company.external', 'Extern'),
(555, 'de_DE', 'page.content.tabs.person.person.salutationIdFk', 'Anrede'),
(624, 'en_US', 'content.make-estimation.unit', 'Unit'),
(685, 'de_DE', 'page.content.tabs.ldap.ldap.useLdap', 'Verwenden'),
(700, 'de_DE', 'page.content.tabs.user.grid.header.row.username', 'Username'),
(753, 'de_DE', 'page.navigation.root.note.person', 'Personen'),
(863, 'de_DE', 'content.address.number', 'Hausnummer'),
(571, 'de_DE', 'page.content.tabs.logging.logging', 'Zeiten'),
(710, 'de_DE', 'page.content.tabs.user.user.email', 'E-Mail'),
(713, 'de_DE', 'page.content.tabs.user.user.roleIdFk', 'Rolle'),
(32, 'en_US', 'page.content.tabs.resource.grid.footer.row.key', 'Key'),
(508, 'de_DE', 'page.content.tabs.note.note.remindUserIdFk', 'Benutzer'),
(562, 'de_DE', 'page.content.tabs.contact-data.contact-dat', 'Kontaktdaten'),
(583, 'de_DE', 'page.content.tabs.project.grid.header.row.companyName', 'Kunde'),
(634, 'de_DE', 'content.toolbar.saveEstimation', 'Schätzung Speichern'),
(663, 'de_DE', 'page.content.tabs.resources.resources.key', 'Key'),
(21, 'en_US', 'page.content.tabs.company.grid.footer.row.name', 'Name'),
(99, 'en_US', 'page.content.tabs.contact-data.contact-dat', 'Contact Data'),
(117, 'de_DE', 'page.content.toolbar.deleteLogging', 'Zeit löschen'),
(134, 'de_DE', 'page.content.tabs.resource.grid.header.row.message', 'Value'),
(520, 'de_DE', 'page.content.tabs.companies.companyGrid.header.row.name', 'Name'),
(742, 'en_US', 'page.navigation.root.note.person', 'Persons'),
(812, 'en_US', 'page.content.tabs.userperformance.grid.header.row.userPerformance', 'Performance'),
(864, 'de_DE', 'content.address.state', 'Bundesland'),
(90, 'en_US', 'page.content.tabs.person.grid.header.row.phone', 'Phone'),
(136, 'de_DE', 'page.content.tabs.resource.grid.footer.row.resourceId', 'ID'),
(157, 'en_US', 'page.content.project.user', 'User'),
(238, 'en_US', 'page.content.tabs.company.company.phone', 'Phone'),
(504, 'de_DE', 'page.content.tabs.note.note', 'Details'),
(714, 'de_DE', 'page.content.tabs.ldap.ldap.ldapSynced', 'Synchronisiert'),
(859, 'en_US', 'page.content.tabsUserStatistic.user.performance.userCosts', 'Costs'),
(110, 'en_US', 'page.content.tabs.logging.grid.footer.row.taskUserId', 'ID'),
(656, 'de_DE', 'page.content.tabs.reports.reports', 'Bericht'),
(680, 'de_DE', 'page.content.tabs.settings', 'Einstellungen'),
(684, 'de_DE', 'page.content.tabs.ldap.ldap', 'LDAP'),
(861, 'de_DE', 'content.address', 'Adresse Bearbeiten'),
(539, 'en_US', 'page.content.tabs.companies.companyGrid.header.row.name', 'Name'),
(319, 'en_US', 'page.content.tabs.employees.employeeGrid.header.row.personIdFk', ''),
(620, 'en_US', 'content.toolbar.saveTask', 'Save Task'),
(623, 'en_US', 'content.make-estimation.description', 'Description'),
(144, 'de_DE', 'page.content.toolbar.save', 'Speichern'),
(172, 'en_US', 'page.content.tabs.user.grid.header.row.userId', 'ID'),
(530, 'en_US', 'page.content.tabs.personen.personGrid.header.row.personIdFk', ''),
(193, 'en_US', 'page.content.tabs.report.grid.footer.row.reportId', 'ID'),
(505, 'de_DE', 'page.content.tabs.note.note.subject', 'Betreff'),
(541, 'en_US', 'page.content.tabs.companies.companyGrid.footer.row.companyId', 'ID'),
(545, 'de_DE', 'page.content.tabs.person.grid.header.row.fullName', 'Name'),
(582, 'de_DE', 'page.content.tabs.project.grid.header.row.name', 'Name'),
(664, 'de_DE', 'page.content.tabs.resources.resources.locale', 'Locale'),
(669, 'en_US', 'page.content.tabs.settings.settings.emailSupport', 'Mail Support'),
(86, 'en_US', 'page.content.tabs.person', 'Persons'),
(472, 'de_DE', 'page.content.tabs.employees.employeeGrid.footer.row.personId', 'ID'),
(536, 'en_US', 'page.content.tabs.companies', 'Companies'),
(617, 'en_US', 'content.task.description', 'Description'),
(665, 'de_DE', 'page.content.tabs.resources.resources.message', 'Message'),
(756, 'de_DE', 'page.navigation.root.logging.logging', 'Zeiten'),
(11, 'de_DE', 'page.content.tabs.company.grid.footer.row.phone', 'Telefon'),
(662, 'en_US', 'page.content.tabs.reports.reports.description', 'Description'),
(842, 'en_US', 'content.address.state', 'State'),
(112, 'en_US', 'page.content.tabs.logging.grid.footer.row.taskName', 'Task'),
(164, 'en_US', 'page.content.project.user.userGrid.footer.row.username', 'Username'),
(805, 'en_US', 'page.navigation.root.statistic.user.performance', 'User Performance'),
(844, 'en_US', 'content.address.city', 'City'),
(534, 'en_US', 'page.content.tabs.personen.personGrid.footer.row.personId', 'ID'),
(538, 'en_US', 'page.content.tabs.companies.companyGrid.header.row.companyId', 'ID'),
(691, 'de_DE', 'page.content.tabs.smtp.smtp', 'SMTP'),
(449, 'en_US', 'page.content.tabs.contact-data.contact-dat.email', 'Mail'),
(737, 'en_US', 'page.navigation.root.report', 'Reports'),
(827, 'de_DE', 'page.content.tabs.userperformance', 'Benutzer Performance'),
(605, 'de_DE', 'page.content.project.user.userGrid.footer.row.userId', 'ID'),
(619, 'en_US', 'content.task.taskTypeIdFk', 'Type'),
(625, 'en_US', 'content.make-estimation.complexity', 'Complexity'),
(811, 'en_US', 'page.content.tabs.userperformance.grid.header.row.username', 'Username'),
(599, 'de_DE', 'page.content.project.user', 'Benutzer'),
(750, 'de_DE', 'page.navigation.root.note', 'ERP'),
(828, 'de_DE', 'page.content.tabs.userperformance.grid.header.row.userId', 'ID'),
(838, 'en_US', 'page.content.project.project.project.templateIdFk', 'Template'),
(66, 'en_US', 'page.content.tabs.address.addressGrid.footer.row.fullAddress', 'Address'),
(101, 'en_US', 'page.content.tabs.account.account', 'Account'),
(457, 'de_DE', 'page.content.tabs.company.company.email', 'E-Mail'),
(808, 'en_US', 'page.content.tabs.userperformance', 'tab.label.user.performance'),
(852, 'de_DE', 'page.content.tabs.userperformance.grid.footer.row.actualPerformance', 'Actual'),
(596, 'de_DE', 'page.content.project.project.project.name', 'Name'),
(615, 'de_DE', 'content.toolbar.saveTask', 'Task Speichern'),
(640, 'de_DE', 'page.content.tabs.template.grid.footer.row.name', 'Name'),
(650, 'de_DE', 'page.content.tabs.report.grid.header.row.name', 'Name'),
(749, 'en_US', 'page.navigation.root.logging.template', 'Templates'),
(523, 'de_DE', 'page.content.tabs.companies.companyGrid.footer.row.name', 'Name'),
(637, 'de_DE', 'page.content.tabs.template.grid.header.row.name', 'Name'),
(701, 'de_DE', 'page.content.tabs.user.grid.header.row.email', 'E-Mail'),
(858, 'en_US', 'page.content.tabsUserStatistic.user.performance.userBillableHours', 'Billable Hours'),
(75, 'en_US', 'page.content.tabs.note.grid.header.row.remindUserUsername', 'for'),
(119, 'de_DE', 'page.content.tabs.logging', 'Zeiten'),
(185, 'en_US', 'page.content.tabs.settings', 'Settings'),
(467, 'de_DE', 'page.content.tabs.employees.employeeGrid.header.row.fullName', 'Name'),
(1, 'de_DE', 'page.content.toolbar.new', 'Neu'),
(567, 'de_DE', 'page.content.tabs.account', 'Benutzerkonto'),
(121, 'de_DE', 'page.content.tabs.logging.grid.header.row.projectName', 'Projekt'),
(141, 'de_DE', 'page.content.tabs.resources', 'Resources'),
(156, 'en_US', 'page.content.project.project.project', 'Project'),
(494, 'de_DE', 'page.content.tabs.note.grid.header.row.remindUserUsername', 'für'),
(752, 'de_DE', 'page.navigation.root.note.note', 'Wiedervorlage'),
(501, 'de_DE', 'page.content.tabs.note.grid.footer.row.remindUserUsername', 'für'),
(527, 'en_US', 'page.content.tabs.note.note.remindUserIdFk', 'User'),
(613, 'de_DE', 'content.task.billable', 'Abrechenbar'),
(165, 'en_US', 'page.content.project.user.userGrid.footer.row.userRole', 'Role'),
(177, 'en_US', 'page.content.tabs.user.grid.footer.row.userId', 'ID'),
(740, 'en_US', 'page.navigation.root.setting', 'Settings'),
(853, 'de_DE', 'page.content.tabsUserStatistic.user.performance.userBillableHours', 'Billable Hours'),
(869, 'de_DE', 'content.toolbar.saveAddress', 'Adresse Speichern'),
(73, 'en_US', 'page.content.tabs.note.grid.header.row.noteTypeName', 'Type'),
(77, 'en_US', 'page.content.tabs.note.grid.header.row.actions', 'Actions'),
(108, 'en_US', 'page.content.tabs.logging.grid.header.row.from', 'from'),
(142, 'de_DE', 'page.content.tabs.resources.resources', 'Resources'),
(148, 'en_US', 'page.content.tabs.project.grid.header.row.name', 'Name'),
(635, 'de_DE', 'page.content.tabs.template', 'Templates'),
(526, 'en_US', 'page.content.tabs.note.note.note', 'Note'),
(673, 'en_US', 'page.content.tabs.ldap.ldap.ldapDomain', 'Domain'),
(13, 'en_US', 'page.content.toolbar.new', 'New'),
(25, 'en_US', 'page.content.tabs.resource', 'tab.label.resource'),
(102, 'en_US', 'page.content.toolbar.deleteLogging', 'Delete Logging'),
(698, 'de_DE', 'page.content.tabs.user.grid.header.row.userId', 'ID'),
(758, 'de_DE', 'page.navigation.root.logging.template', 'Templates'),
(860, 'en_US', 'page.content.tabsUserStatistic.user.performance.userTurnover', 'Turnover'),
(480, 'de_DE', 'page.content.tabs.address.addressGrid.header.row.fullAddress', 'Adresse'),
(537, 'en_US', 'page.content.tabs.companies.companyGrid.header.row.companyIdFk', ''),
(580, 'de_DE', 'page.content.tabs.project', 'Projekte'),
(676, 'en_US', 'page.content.tabs.smtp.smtp.smtpHost', 'Host'),
(40, 'en_US', 'page.content.toolbar.delete', 'Delete'),
(56, 'en_US', 'page.content.tabs.employees.employeeGrid.footer.row.phone', 'Phone'),
(182, 'en_US', 'page.content.tabs.user.user', 'User Data'),
(236, 'en_US', 'page.content.tabs.company.company.additionalName', 'Addional Name'),
(486, 'de_DE', 'page.content.tabs.address.addressGrid.footer.row.addressTypeIdFk', 'Typ'),
(550, 'de_DE', 'page.content.tabs.person.grid.footer.row.fullName', 'Name'),
(574, 'de_DE', 'page.content.tabs.logging.logging.until', 'bis'),
(697, 'de_DE', 'page.content.tabs.user', 'Benutzer'),
(866, 'de_DE', 'content.address.city', 'Stadt'),
(849, 'de_DE', 'page.content.tabsUserStatistic.user.performance', 'Performance'),
(85, 'en_US', 'page.content.tabs.note.note', 'Details'),
(120, 'de_DE', 'page.content.tabs.logging.grid.header.row.taskUserId', 'ID'),
(145, 'de_DE', 'page.content.toolbar.delete', 'Löschen'),
(175, 'en_US', 'page.content.tabs.user.grid.header.row.email', 'E-Mail'),
(549, 'de_DE', 'page.content.tabs.person.grid.footer.row.personId', 'ID'),
(566, 'de_DE', 'page.content.tabs.contact-data.contact-dat.mobile', 'Mobilfunknummer'),
(633, 'de_DE', 'content.make-estimation.quantity', 'Anzahl'),
(709, 'de_DE', 'page.content.tabs.user.user.username', 'Benutzername'),
(18, 'en_US', 'page.content.tabs.company.grid.header.row.phone', 'Phone'),
(20, 'en_US', 'page.content.tabs.company.grid.footer.row.companyId', 'ID'),
(325, 'en_US', 'page.content.tabs.employees.employeeGrid.footer.row.personIdFk', ''),
(643, 'en_US', 'page.content.tabs.template.template.description', 'Description'),
(699, 'de_DE', 'page.content.tabs.user.grid.header.row.roleName', 'Role'),
(717, 'en_US', 'page.content.tabs.user.user.email', 'Mail'),
(53, 'en_US', 'page.content.tabs.employees.employeeGrid.footer.row.personId', 'ID'),
(57, 'en_US', 'page.content.tabs.employees.employeeGrid.footer.row.actions', 'Actions'),
(82, 'en_US', 'page.content.tabs.note.grid.footer.row.remindUserUsername', 'for'),
(517, 'de_DE', 'page.content.tabs.companies', 'Unternehmen'),
(641, 'de_DE', 'page.content.tabs.template.grid.footer.row.actions', 'Aktionen'),
(81, 'en_US', 'page.content.tabs.note.grid.footer.row.createUserUsername', 'from'),
(485, 'de_DE', 'page.content.tabs.address.addressGrid.footer.row.fullAddress', 'Adresse'),
(587, 'de_DE', 'page.content.tabs.project.grid.footer.row.companyName', 'Kunde'),
(814, 'en_US', 'page.content.tabs.userperformance.grid.footer.row.userId', 'ID'),
(106, 'en_US', 'page.content.tabs.logging.grid.header.row.projectName', 'Project'),
(528, 'en_US', 'page.content.tabs.note.note.noteTypeIdFk', 'Type'),
(830, 'de_DE', 'page.content.tabs.userperformance.grid.header.row.username', 'Username'),
(2, 'de_DE', 'page.content.tabs.company', 'Unternehmen'),
(45, 'en_US', 'page.content.tabs.employees', 'Employees'),
(127, 'de_DE', 'page.content.tabs.logging.grid.footer.row.taskName', 'Task'),
(470, 'de_DE', 'page.content.tabs.employees.employeeGrid.header.row.actions', 'Aktionen'),
(572, 'de_DE', 'page.content.tabs.logging.logging.projectIdFk', 'Projekt'),
(639, 'de_DE', 'page.content.tabs.template.grid.footer.row.templateId', 'ID'),
(96, 'en_US', 'page.content.tabs.person.grid.footer.row.actions', 'Actions'),
(207, 'en_US', 'page.content.tabs.template.grid.footer.row.name', 'Name'),
(458, 'de_DE', 'page.content.tabs.company.company.phone', 'Telefon'),
(489, 'de_DE', 'page.content.tabs.note', 'Wiedervorlagen'),
(678, 'en_US', 'page.content.tabs.smtp.smtp.smtpUser', 'User'),
(38, 'en_US', 'page.content.toolbar.back', 'Back'),
(98, 'en_US', 'page.content.tabs.contact-data', 'Contact Data'),
(672, 'en_US', 'page.content.tabs.ldap.ldap.ldapBindRequired', 'Bind required'),
(79, 'en_US', 'page.content.tabs.note.grid.footer.row.subject', 'Subject'),
(154, 'en_US', 'page.content.tabs.project.grid.footer.row.actions', 'Actions'),
(188, 'en_US', 'page.content.tabs.smtp.smtp', 'SMTP'),
(452, 'en_US', 'page.content.tabs.contact-data.contact-dat.mobile', 'Mobile'),
(543, 'de_DE', 'page.content.tabs.person', 'Personen'),
(557, 'de_DE', 'page.content.tabs.person.person.lastname', 'Nachname'),
(570, 'de_DE', 'page.content.tabs.logging.logged', 'Meine Zeiten'),
(72, 'en_US', 'page.content.tabs.note.grid.header.row.subject', 'Subject'),
(183, 'en_US', 'page.content.tabs.ldap', 'LDAP'),
(186, 'en_US', 'page.content.tabs.settings.settings', 'Settings'),
(491, 'de_DE', 'page.content.tabs.note.grid.header.row.subject', 'Betreff'),
(602, 'de_DE', 'page.content.project.user.userGrid.header.row.username', 'Benutzername'),
(614, 'de_DE', 'content.task.taskTypeIdFk', 'Typ'),
(719, 'en_US', 'page.content.tabs.user.user.enabled', 'Activ'),
(15, 'en_US', 'page.content.tabs.company.grid.header.row.companyId', 'ID'),
(138, 'de_DE', 'page.content.tabs.resource.grid.footer.row.locale', 'Locale'),
(189, 'en_US', 'page.content.tabs.report', 'Reports'),
(592, 'en_US', 'page.content.project.user.userGrid.header.row.userIdFk', ''),
(704, 'de_DE', 'page.content.tabs.user.grid.footer.row.roleName', 'Role'),
(104, 'en_US', 'page.content.tabs.logging', 'Loggings'),
(137, 'de_DE', 'page.content.tabs.resource.grid.footer.row.key', 'Key'),
(484, 'de_DE', 'page.content.tabs.address.addressGrid.footer.row.addressId', 'ID'),
(668, 'en_US', 'page.content.tabs.resources.resources.message', 'Message'),
(694, 'de_DE', 'page.content.tabs.smtp.smtp.smtpPort', 'Port'),
(746, 'en_US', 'page.navigation.root.setting.resource', 'Translations'),
(71, 'en_US', 'page.content.tabs.note.grid.header.row.noteId', 'ID'),
(184, 'en_US', 'page.content.tabs.ldap.ldap', 'LDAP'),
(469, 'de_DE', 'page.content.tabs.employees.employeeGrid.header.row.phone', 'Telefon'),
(674, 'en_US', 'page.content.tabs.ldap.ldap.ldapDn', 'Dn'),
(730, 'en_US', 'page.navigation.root.dashboard', 'Dashboard'),
(865, 'de_DE', 'content.address.postcode', 'PLZ'),
(754, 'de_DE', 'page.navigation.root.dashboard', 'Dashboard'),
(765, 'de_DE', 'page.content.tabs.user.user.contractedHours', 'Monatliche Arbeitszeit'),
(867, 'de_DE', 'content.address.postOfficeBox', 'Postfach'),
(163, 'en_US', 'page.content.project.user.userGrid.footer.row.userId', 'ID'),
(192, 'en_US', 'page.content.tabs.report.grid.header.row.actions', 'Actions'),
(473, 'de_DE', 'page.content.tabs.employees.employeeGrid.footer.row.fullName', 'Name'),
(611, 'de_DE', 'content.task', 'Task Bearbeiten'),
(4, 'de_DE', 'page.content.tabs.company.grid.header.row.name', 'Name'),
(49, 'en_US', 'page.content.tabs.employees.employeeGrid.header.row.email', 'Mail'),
(150, 'en_US', 'page.content.tabs.project.grid.header.row.actions', 'Actions'),
(196, 'en_US', 'page.content.tabs.reports', 'Report'),
(628, 'de_DE', 'content.estimations-made', 'Abgegebene Schätzungen'),
(483, 'de_DE', 'page.content.tabs.address.addressGrid.footer.row.addressIdFk', ''),
(128, 'de_DE', 'page.content.tabs.logging.grid.footer.row.from', 'von'),
(180, 'en_US', 'page.content.tabs.user.grid.footer.row.email', 'E-Mail'),
(471, 'de_DE', 'page.content.tabs.employees.employeeGrid.footer.row.personIdFk', ''),
(631, 'de_DE', 'content.make-estimation.unit', 'Einheit'),
(647, 'de_DE', 'page.content.tabs.tasks', 'Tasks'),
(706, 'de_DE', 'page.content.tabs.user.grid.footer.row.email', 'E-Mail'),
(763, 'de_DE', 'page.navigation.root.setting.user', 'Benutzer'),
(800, 'de_DE', 'title.user.performance.view', 'Benutzer Performance'),
(31, 'en_US', 'page.content.tabs.resource.grid.footer.row.resourceId', 'ID'),
(123, 'de_DE', 'page.content.tabs.logging.grid.header.row.from', 'von'),
(577, 'en_US', 'page.content.tabs.logging.logging.from', 'from'),
(847, 'en_US', 'content.toolbar.saveAddress', 'Save Address'),
(34, 'en_US', 'page.content.tabs.resource.grid.footer.row.message', 'Value'),
(36, 'en_US', 'page.content.tabs.resources', 'Resources'),
(584, 'de_DE', 'page.content.tabs.project.grid.header.row.actions', 'Aktionen'),
(705, 'de_DE', 'page.content.tabs.user.grid.footer.row.username', 'Username'),
(799, 'en_US', 'title.user.performance.view', 'User Performance'),
(817, 'en_US', 'page.content.tabs.userperformance.grid.footer.row.userPerformance', 'Performance'),
(122, 'de_DE', 'page.content.tabs.logging.grid.header.row.taskName', 'Task'),
(125, 'de_DE', 'page.content.tabs.logging.grid.footer.row.taskUserId', 'ID'),
(161, 'en_US', 'page.content.project.user.userGrid.header.row.userRole', 'Role'),
(205, 'en_US', 'page.content.tabs.template.grid.header.row.actions', 'Actions'),
(576, 'en_US', 'page.content.tabs.logging.logging.projectIdFk', 'Project'),
(654, 'de_DE', 'page.content.tabs.report.grid.footer.row.actions', 'Aktionen'),
(711, 'de_DE', 'page.content.tabs.user.user.rate', 'Interner Verrechnungspreis'),
(174, 'en_US', 'page.content.tabs.user.grid.header.row.username', 'Username'),
(447, 'en_US', 'page.content.tabs.person.person.position', 'Position'),
(644, 'de_DE', 'page.content.tabs.template.template', 'Templatedaten'),
(832, 'de_DE', 'page.content.tabs.userperformance.grid.header.row.actions', 'Actions'),
(23, 'en_US', 'page.content.tabs.company.grid.footer.row.phone', 'Phone'),
(720, 'en_US', 'page.content.tabs.user.user.roleIdFk', 'Role'),
(820, 'en_US', 'page.content.tabsUserStatistic.user.performance', 'Performance'),
(63, 'en_US', 'page.content.tabs.address.addressGrid.header.row.actions', 'Actions'),
(124, 'de_DE', 'page.content.tabs.logging.grid.header.row.until', 'bis'),
(206, 'en_US', 'page.content.tabs.template.grid.footer.row.templateId', 'ID'),
(455, 'de_DE', 'page.content.tabs.company.company.name', 'Name'),
(468, 'de_DE', 'page.content.tabs.employees.employeeGrid.header.row.email', 'E-Mail'),
(693, 'de_DE', 'page.content.tabs.smtp.smtp.smtpHost', 'Host'),
(707, 'de_DE', 'page.content.tabs.user.grid.footer.row.actions', 'Actions'),
(17, 'en_US', 'page.content.tabs.company.grid.header.row.email', 'Mail'),
(208, 'en_US', 'page.content.tabs.template.grid.footer.row.actions', 'Actions'),
(240, 'en_US', 'page.content.tabs.company.company.website', 'Website'),
(487, 'de_DE', 'page.content.tabs.address.addressGrid.footer.row.actions', 'Aktionen'),
(591, 'en_US', 'page.content.project.project.project.companyIdFk', 'Customer'),
(638, 'de_DE', 'page.content.tabs.template.grid.header.row.actions', 'Aktionen'),
(816, 'en_US', 'page.content.tabs.userperformance.grid.footer.row.username', 'Username'),
(444, 'en_US', 'page.content.tabs.person.person.firstname', 'Firstname'),
(546, 'de_DE', 'page.content.tabs.person.grid.header.row.email', 'E-Mail'),
(671, 'en_US', 'page.content.tabs.ldap.ldap.ldapHost', 'Host'),
(618, 'en_US', 'content.task.billable', 'Billable'),
(69, 'en_US', 'page.content.toolbar.createAddress', 'New Address'),
(512, 'de_DE', 'page.content.tabs.personen.personGrid.header.row.personId', 'ID'),
(734, 'en_US', 'page.navigation.root.logging', 'Projects'),
(818, 'en_US', 'page.content.tabs.userperformance.grid.footer.row.actions', 'Actions'),
(24, 'en_US', 'page.content.tabs.company.grid.footer.row.actions', 'Actions'),
(490, 'de_DE', 'page.content.tabs.note.grid.header.row.noteId', 'ID'),
(499, 'de_DE', 'page.content.tabs.note.grid.footer.row.noteTypeName', 'Typ'),
(565, 'de_DE', 'page.content.tabs.contact-data.contact-dat.telefax', 'Telefax'),
(695, 'de_DE', 'page.content.tabs.smtp.smtp.smtpUser', 'Benutzer'),
(67, 'en_US', 'page.content.tabs.address.addressGrid.footer.row.addressTypeIdFk', 'Type'),
(140, 'de_DE', 'page.content.tabs.resource.grid.footer.row.actions', 'Actions'),
(149, 'en_US', 'page.content.tabs.project.grid.header.row.companyName', 'Customer'),
(670, 'en_US', 'page.content.tabs.ldap.ldap.useLdap', 'Use'),
(65, 'en_US', 'page.content.tabs.address.addressGrid.footer.row.addressId', 'ID'),
(113, 'en_US', 'page.content.tabs.logging.grid.footer.row.from', 'from'),
(496, 'de_DE', 'page.content.tabs.note.grid.header.row.actions', 'Aktionen'),
(741, 'en_US', 'page.navigation.root.note.company', 'Companies'),
(819, 'en_US', 'page.content.tabsUserStatistic.user', 'User'),
(871, 'en_US', 'content.loggings-made', 'estimation.view.fieldset.label.loggings-made'),
(872, 'en_US', 'page.content.project.logging.loggingGrid.header.row.taskUserId', 'ID'),
(873, 'en_US', 'page.content.project.logging.loggingGrid.header.row.projectName', 'Project'),
(874, 'en_US', 'page.content.project.logging.loggingGrid.header.row.taskName', 'Task'),
(875, 'en_US', 'page.content.project.logging.loggingGrid.header.row.from', 'from'),
(876, 'en_US', 'page.content.project.logging.loggingGrid.header.row.until', 'until'),
(877, 'en_US', 'page.content.project.logging.loggingGrid.footer.row.taskUserId', 'ID'),
(878, 'en_US', 'page.content.project.logging.loggingGrid.footer.row.projectName', 'Project'),
(879, 'en_US', 'page.content.project.logging.loggingGrid.footer.row.taskName', 'Task'),
(880, 'en_US', 'page.content.project.logging.loggingGrid.footer.row.from', 'from'),
(881, 'en_US', 'page.content.project.logging.loggingGrid.footer.row.until', 'until'),
(882, 'en_US', 'page.content.project.logging.loggingGrid.header.row.userName', 'logging.overview.grid.label.username'),
(883, 'en_US', 'page.content.project.logging.loggingGrid.footer.row.userName', 'logging.overview.grid.label.username'),
(884, 'en_US', 'page.content.tabs.logging.grid.header.row.username', 'logging.overview.grid.label.username'),
(885, 'en_US', 'page.content.tabs.logging.grid.footer.row.username', 'logging.overview.grid.label.username');

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `role_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id_fk` int(11) DEFAULT NULL,
  `user_id_fk` int(11) DEFAULT NULL,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`),
  KEY `role_idx_01` (`role_id_fk`),
  KEY `role_idx_02` (`user_id_fk`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=81 ;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`role_id`, `role_id_fk`, `user_id_fk`, `name`) VALUES
(5, 2, NULL, 'Projectmanager'),
(1, 5, NULL, 'Administrator'),
(2, 4, NULL, 'User'),
(4, 3, NULL, 'Developer'),
(3, NULL, NULL, 'Guest'),
(6, 5, 1, 'admin'),
(7, 3, 2, 'guest');

-- --------------------------------------------------------

--
-- Table structure for table `rule`
--

CREATE TABLE IF NOT EXISTS `rule` (
  `rule_id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id_fk` int(10) NOT NULL,
  `assertion_id_fk` int(10) DEFAULT NULL,
  `resource` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `privileges` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `permission` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`rule_id`),
  UNIQUE KEY `rule_uidx_01` (`role_id_fk`,`resource`),
  KEY `rule_idx_01` (`role_id_fk`),
  KEY `rule_idx_02` (`assertion_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `rule`
--


-- --------------------------------------------------------

--
-- Table structure for table `salutation`
--

CREATE TABLE IF NOT EXISTS `salutation` (
  `salutation_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`salutation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `salutation`
--

INSERT INTO `salutation` (`salutation_id`, `name`) VALUES
(1, 'Herr'),
(2, 'Frau');

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `setting_id` int(10) NOT NULL AUTO_INCREMENT,
  `email_support` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `use_ldap` tinyint(1) NOT NULL,
  `ldap_host` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ldap_bind_required` tinyint(1) NOT NULL DEFAULT '0',
  `ldap_domain` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ldap_dn` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `use_smtp` tinyint(1) NOT NULL DEFAULT '0',
  `smtp_host` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `smtp_port` int(10) NOT NULL DEFAULT '25',
  `smtp_user` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `smtp_password` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`setting_id`, `email_support`, `use_ldap`, `ldap_host`, `ldap_bind_required`, `ldap_domain`, `ldap_dn`, `use_smtp`, `smtp_host`, `smtp_port`, `smtp_user`, `smtp_password`) VALUES
(4, 'someone@yourdomain.com', 0, '127.0.0.1', 1, 'yourdomain.com', 'ou=user,dc=yourdomain,dc=com', 1, 'mail.yourdomain.com', 25, 'admin', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE IF NOT EXISTS `task` (
  `task_id` int(10) NOT NULL,
  `task_id_fk` int(10) DEFAULT NULL,
  `task_type_id_fk` int(10) DEFAULT NULL,
  `left_node` int(10) DEFAULT NULL,
  `right_node` int(10) DEFAULT NULL,
  `order_number` int(10) DEFAULT NULL,
  `level` int(10) DEFAULT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `billable` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_id`),
  KEY `task_idx_01` (`task_id_fk`),
  KEY `task_idx_02` (`task_type_id_fk`),
  KEY `task_idx_03` (`left_node`),
  KEY `task_idx_04` (`right_node`),
  KEY `task_idx_05` (`order_number`),
  KEY `task_idx_06` (`level`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `task`
--


-- --------------------------------------------------------

--
-- Table structure for table `task_lock`
--

CREATE TABLE IF NOT EXISTS `task_lock` (
  `lockId` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `lockTable` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `lockStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`lockId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `task_lock`
--


-- --------------------------------------------------------

--
-- Table structure for table `task_task_id_seq`
--

CREATE TABLE IF NOT EXISTS `task_task_id_seq` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `task_task_id_seq`
--


-- --------------------------------------------------------

--
-- Table structure for table `task_type`
--

CREATE TABLE IF NOT EXISTS `task_type` (
  `task_type_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`task_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `task_type`
--

INSERT INTO `task_type` (`task_type_id`, `name`) VALUES
(5, 'Extension, Extern'),
(1, 'Projekt'),
(2, 'Teilprojekt'),
(4, 'Extension, Intern'),
(3, 'Extensions'),
(6, 'Tätigkeit');

-- --------------------------------------------------------

--
-- Table structure for table `task_user`
--

CREATE TABLE IF NOT EXISTS `task_user` (
  `task_user_id` int(10) NOT NULL AUTO_INCREMENT,
  `task_id_fk` int(10) NOT NULL,
  `user_id_fk` int(10) NOT NULL,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `project_id_fk` int(11) NOT NULL,
  `task_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `project_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `from` int(10) NOT NULL DEFAULT '0',
  `until` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`task_user_id`),
  KEY `task_user_idx_01` (`task_id_fk`),
  KEY `task_user_idx_02` (`user_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `task_user`
--


-- --------------------------------------------------------

--
-- Table structure for table `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `template_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `template`
--


-- --------------------------------------------------------

--
-- Table structure for table `template_task`
--

CREATE TABLE IF NOT EXISTS `template_task` (
  `template_task_id` int(10) NOT NULL AUTO_INCREMENT,
  `template_id_fk` int(10) NOT NULL,
  `task_id_fk` int(10) NOT NULL,
  PRIMARY KEY (`template_task_id`),
  KEY `template_task_idx_01` (`template_id_fk`),
  KEY `template_task_idx_02` (`task_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `template_task`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `rate` int(10) NOT NULL DEFAULT '20',
  `contracted_hours` int(11) NOT NULL DEFAULT '160',
  `ldap_synced` tinyint(1) NOT NULL DEFAULT '0',
  `synced_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_uidx_01` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=44 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `email`, `username`, `password`, `enabled`, `rate`, `contracted_hours`, `ldap_synced`, `synced_at`) VALUES
(1, 'admin@yourdomain.com', 'admin', '4f4bada72cb0cbb588a295e92eae4ad5', 1, 0, 0, 0, NULL),
(2, 'guest@yourdomain.com', 'guest', '4f4bada72cb0cbb588a295e92eae4ad5', 0, 0, 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_performance`
--

CREATE TABLE IF NOT EXISTS `user_performance` (
  `user_performance_id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id_fk` int(10) NOT NULL,
  `month` int(10) NOT NULL,
  `year` int(10) NOT NULL,
  `performance` int(10) NOT NULL,
  `billable_hours` int(10) NOT NULL,
  `turnover` int(10) NOT NULL,
  `costs` int(10) NOT NULL,
  PRIMARY KEY (`user_performance_id`),
  KEY `user_performance_idx_01` (`user_id_fk`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

--
-- Dumping data for table `user_performance`
--


-- --------------------------------------------------------

--
-- Table structure for table `widget`
--

CREATE TABLE IF NOT EXISTS `widget` (
  `widget_id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `block` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `include_file` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`widget_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `widget`
--

INSERT INTO `widget` (`widget_id`, `name`, `block`, `include_file`) VALUES
(2, 'Note', 'TDProject_ERP_Block_Dashboard_Widget_Note', 'TDProject/ERP/Block/Dashboard/Widget/Note.php'),
(3, 'Logging', 'TDProject_Project_Block_Dashboard_Widget_Logging', 'TDProject/Project/Block/Dashboard/Widget/Logging.php');
