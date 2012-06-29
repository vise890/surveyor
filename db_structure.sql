-- phpMyAdmin SQL Dump
-- version 3.1.2deb1ubuntu0.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 16, 2010 at 04:20 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

SET AUTOCOMMIT=0;
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `shelter`
--

--
-- Table structure for table `database_input_cities`
--

CREATE TABLE IF NOT EXISTS `database_input_cities` (
  `ID` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `city` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `database_input_cities`
--


-- --------------------------------------------------------

--
-- Table structure for table `database_input_city_fields`
--

CREATE TABLE IF NOT EXISTS `database_input_city_fields` (
  `ID` int(11) NOT NULL auto_increment,
  `city` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `city` (`city`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1427 ;



-- --------------------------------------------------------

--
-- Table structure for table `database_input_ui_drop_down`
--

CREATE TABLE IF NOT EXISTS `database_input_ui_drop_down` (
  `ID` int(11) NOT NULL auto_increment,
  `field_ID` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `field_ID` (`field_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=681 ;


-- --------------------------------------------------------

--
-- Table structure for table `database_input_ui_fields`
--

CREATE TABLE IF NOT EXISTS `database_input_ui_fields` (
  `ID` int(11) NOT NULL auto_increment,
  `position` int(4) NOT NULL,
  `tab_ID` int(10) NOT NULL,
  `db_table` varchar(255) collate utf8_bin NOT NULL,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `type` varchar(255) collate utf8_bin NOT NULL,
  `is_array` tinyint(1) NOT NULL,
  `allow_query` tinyint(1) NOT NULL,
  `label` varchar(255) collate utf8_bin NOT NULL,
  `validation` varchar(255) collate utf8_bin NOT NULL,
  `js_validation` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `label` (`label`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=125 ;



--
-- Table structure for table `database_input_ui_tabs`
--

CREATE TABLE IF NOT EXISTS `database_input_ui_tabs` (
  `ID` int(11) NOT NULL auto_increment,
  `position` smallint(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `db_table` varchar(100) NOT NULL,
  `many_to_one` tinyint(1) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `label` (`label`),
  UNIQUE KEY `class` (`class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;



--
-- Table structure for table `Documents`
--

CREATE TABLE IF NOT EXISTS `Documents` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  `ration_card` varchar(255) NOT NULL,
  `ration_card_no` varchar(255) NOT NULL,
  `voter_id_card` varchar(255) NOT NULL,
  `voter_id_card_no` varchar(255) NOT NULL,
  `name_in_voter_list` varchar(255) NOT NULL,
  `photo_pass` varchar(255) NOT NULL,
  `photo_pass_no` varchar(255) NOT NULL,
  `pay_corporation_tax` varchar(255) NOT NULL,
  `tax_amount` varchar(255) NOT NULL,
  `pay_water_bill` varchar(255) NOT NULL,
  `saving_group` varchar(255) NOT NULL,
  `saving_group_type` varchar(255) NOT NULL,
  `member_of_saving_group` varchar(255) NOT NULL,
  `bpl_card` varchar(255) NOT NULL,
  `bpl_card_no` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`),
  UNIQUE KEY `survey_ID_2` (`survey_ID`),
  UNIQUE KEY `survey_ID_3` (`survey_ID`),
  UNIQUE KEY `survey_ID_4` (`survey_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=148 ;



--
-- Table structure for table `Drainage_Road_Electricity`
--

CREATE TABLE IF NOT EXISTS `Drainage_Road_Electricity` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  `monsoon_drainage_facility` varchar(255) NOT NULL,
  `monsoon_drainage_type` varchar(255) NOT NULL,
  `monsoon_drainage_flow_properly` varchar(255) NOT NULL,
  `gutter_in_slum` varchar(255) NOT NULL,
  `gutter_type` varchar(255) NOT NULL,
  `drainage_cleaned_daily` varchar(255) NOT NULL,
  `drainage_cleaned_by` varchar(255) NOT NULL,
  `drainage_cleaning_frequency` varchar(255) NOT NULL,
  `front_road_paved` varchar(255) NOT NULL,
  `road_width` varchar(255) NOT NULL,
  `road_type` varchar(255) NOT NULL,
  `electricity_connection` varchar(255) NOT NULL,
  `electricity_meter` varchar(255) NOT NULL,
  `electricity_monthly_payment` varchar(255) NOT NULL,
  `electricity_pay_to` varchar(255) NOT NULL,
  `electricity_borrowed_from_house_no` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`),
  UNIQUE KEY `survey_ID_2` (`survey_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=147 ;



--
-- Table structure for table `FamilyDetail`
--

CREATE TABLE IF NOT EXISTS `FamilyDetail` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `age` float NOT NULL,
  `marital_status` varchar(255) NOT NULL,
  `completed_education` varchar(255) NOT NULL,
  `current_education` varchar(255) NOT NULL,
  `occupation` varchar(255) NOT NULL,
  `service_sector` varchar(255) NOT NULL,
  `monthly_income` mediumint(11) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `survey_ID` (`survey_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=416 ;



--
-- Table structure for table `Health_Toilet`
--

CREATE TABLE IF NOT EXISTS `Health_Toilet` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  `health_service` varchar(255) NOT NULL,
  `health_visit_frequency` varchar(255) NOT NULL,
  `monthly_medical_expense` varchar(255) NOT NULL,
  `toilet_type` varchar(255) NOT NULL,
  `pay_for_toilet` varchar(255) NOT NULL,
  `toilet_monthly_rent` varchar(255) NOT NULL,
  `sewerage_type` varchar(255) NOT NULL,
  `water_source_in_toilet` varchar(255) NOT NULL,
  `after_toilet_hands_cleaned_with` varchar(255) NOT NULL,
  `pregnant_woman` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`),
  UNIQUE KEY `survey_ID_2` (`survey_ID`),
  UNIQUE KEY `survey_ID_3` (`survey_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=77 ;



--
-- Table structure for table `HIV`
--

CREATE TABLE IF NOT EXISTS `HIV` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



--
-- Table structure for table `HouseholdDetail`
--

CREATE TABLE IF NOT EXISTS `HouseholdDetail` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  `no_of_members` mediumint(11) NOT NULL,
  `no_of_males` mediumint(11) NOT NULL,
  `no_of_females` mediumint(11) NOT NULL,
  `women_self_employment` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`),
  UNIQUE KEY `survey_ID_2` (`survey_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=148 ;


--
-- Table structure for table `MovableAssets`
--

CREATE TABLE IF NOT EXISTS `MovableAssets` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(255) NOT NULL,
  `asset_quantity` mediumint(11) NOT NULL,
  `asset_type` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=99 ;

--
-- Dumping data for table `MovableAssets`
--


--
-- Table structure for table `OtherDetail`
--

CREATE TABLE IF NOT EXISTS `OtherDetail` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `OtherDetail`
--


-- --------------------------------------------------------

--
-- Table structure for table `OtherSlumDetail`
--

CREATE TABLE IF NOT EXISTS `OtherSlumDetail` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `OtherSlumDetail`
--


-- --------------------------------------------------------

--
-- Table structure for table `SlumDetail`
--

CREATE TABLE IF NOT EXISTS `SlumDetail` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `areacode` varchar(255) NOT NULL,
  `giscode` varchar(255) NOT NULL,
  `dtrzone` varchar(255) NOT NULL,
  `dateofsurvey` date NOT NULL,
  `nameofslum` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `wardno` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`),
  KEY `city` (`city`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `SlumDetail`
--


-- --------------------------------------------------------

--
-- Table structure for table `slumlevel_input_city_fields`
--

CREATE TABLE IF NOT EXISTS `slumlevel_input_city_fields` (
  `ID` int(11) NOT NULL auto_increment,
  `city` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `city` (`city`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `slumlevel_input_city_fields`
--


-- --------------------------------------------------------

--
-- Table structure for table `slumlevel_input_ui_drop_down`
--

CREATE TABLE IF NOT EXISTS `slumlevel_input_ui_drop_down` (
  `ID` int(11) NOT NULL auto_increment,
  `field_ID` int(11) NOT NULL,
  `option_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  KEY `field_ID` (`field_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `slumlevel_input_ui_drop_down`
--


-- --------------------------------------------------------

--
-- Table structure for table `slumlevel_input_ui_fields`
--

CREATE TABLE IF NOT EXISTS `slumlevel_input_ui_fields` (
  `ID` int(11) NOT NULL auto_increment,
  `position` int(4) NOT NULL,
  `tab_ID` int(10) NOT NULL,
  `db_table` varchar(255) collate utf8_bin NOT NULL,
  `name` varchar(255) collate utf8_bin NOT NULL,
  `type` varchar(255) collate utf8_bin NOT NULL,
  `is_array` tinyint(1) NOT NULL,
  `allow_query` tinyint(1) NOT NULL,
  `label` varchar(255) collate utf8_bin NOT NULL,
  `validation` varchar(255) collate utf8_bin NOT NULL,
  `js_validation` varchar(255) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `label` (`label`),
  KEY `type` (`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=8 ;



--
-- Table structure for table `slumlevel_input_ui_tabs`
--

CREATE TABLE IF NOT EXISTS `slumlevel_input_ui_tabs` (
  `ID` int(11) NOT NULL auto_increment,
  `position` smallint(11) NOT NULL,
  `label` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `db_table` varchar(100) NOT NULL,
  `many_to_one` tinyint(1) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `label` (`label`),
  UNIQUE KEY `class` (`class`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;


--
-- Table structure for table `SurveyDetail`
--

CREATE TABLE IF NOT EXISTS `SurveyDetail` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  `city` varchar(20) NOT NULL,
  `slum_name` varchar(255) NOT NULL,
  `house_no` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `years_in_slum` varchar(255) NOT NULL,
  `religion` varchar(255) NOT NULL,
  `caste` varchar(255) NOT NULL,
  `caste_category` varchar(255) NOT NULL,
  `contruction_type` varchar(255) NOT NULL,
  `house_ownership` varchar(255) NOT NULL,
  `survey_date` date NOT NULL,
  `house_use` varchar(255) NOT NULL,
  `survey_by` varchar(255) NOT NULL,
  `information_by` varchar(255) NOT NULL,
  `family_head` varchar(255) NOT NULL,
  `native_place` varchar(255) NOT NULL,
  `years_in_city` varchar(255) NOT NULL,
  `years_in_current_house` varchar(255) NOT NULL,
  `shifted_from` varchar(255) NOT NULL,
  `land_at_native_place` varchar(255) NOT NULL,
  `mother_tongue` varchar(255) NOT NULL,
  `house_owner_name` varchar(255) NOT NULL,
  `house_area` varchar(255) NOT NULL,
  `roof_type` varchar(255) NOT NULL,
  `flooring_type` varchar(255) NOT NULL,
  `no_of_floors` varchar(255) NOT NULL,
  `family_head_sex` varchar(255) NOT NULL,
  `monthly_house_rent` mediumint(11) NOT NULL,
  `cooking_fuel` varchar(255) NOT NULL,
  `chullah_for_heating_water` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`),
  KEY `city` (`city`),
  KEY `slum_name` (`slum_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=148 ;



--
-- Table structure for table `Waste_Water`
--

CREATE TABLE IF NOT EXISTS `Waste_Water` (
  `ID` int(11) NOT NULL auto_increment,
  `survey_ID` varchar(20) NOT NULL,
  `disposal_type` varchar(255) NOT NULL,
  `solid_waste_separation` varchar(255) NOT NULL,
  `disposal_frequency` varchar(255) NOT NULL,
  `disposal_collection` varchar(255) NOT NULL,
  `burn_solid_waste_in_open_space` varchar(255) NOT NULL,
  `pay_for_waste_collection` varchar(255) NOT NULL,
  `waste_collection_monthly_payment` varchar(255) NOT NULL,
  `water_connection_type` varchar(255) NOT NULL,
  `sell_water_to_others` varchar(255) NOT NULL,
  `water_quality` varchar(255) NOT NULL,
  `drinking_water_treatment` varchar(255) NOT NULL,
  `water_supply_duration` varchar(255) NOT NULL,
  `water_supply_pressure` varchar(255) NOT NULL,
  `purchase_water_form_others` varchar(255) NOT NULL,
  PRIMARY KEY  (`ID`),
  UNIQUE KEY `survey_ID` (`survey_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=147 ;



COMMIT;
