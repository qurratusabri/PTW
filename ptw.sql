-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Feb 27, 2025 at 08:55 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ptw`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
`adminID` int(11) NOT NULL,
  `username` varchar(800) NOT NULL,
  `password` varchar(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `username`, `password`) VALUES
(1, 'itadmin', 'abcd.1234'),
(2, 'nsyazwanih', 'abcd.1234'),
(3, 'sfatimah', 'abcd.1234'),
(4, 'arifaizuddin', 'abcd.1234');

-- --------------------------------------------------------

--
-- Table structure for table `applicant`
--

CREATE TABLE IF NOT EXISTS `applicant` (
`applicantID` int(11) NOT NULL,
  `username` varchar(80) NOT NULL,
  `password` varchar(10) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=242 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `applicant`
--

INSERT INTO `applicant` (`applicantID`, `username`, `password`) VALUES
(224, 'klgshpremier', 'abcd.1234'),
(225, 'klgshmat', 'abcd.1234'),
(226, 'klgshpaed', 'abcd.1234'),
(227, 'klgshsurg', 'abcd.1234'),
(228, 'klgshmed', 'abcd.1234'),
(229, 'klgshicu', 'abcd.1234'),
(230, 'klgshae', 'abcd.1234'),
(231, 'klgshdialysis', 'abcd.1234'),
(232, 'klgshphar', 'abcd.1234'),
(233, 'klgshxray', 'abcd.1234'),
(234, 'klgshlab', 'abcd.1234'),
(235, 'klgshfems', 'abcd.1234'),
(236, 'klgshbo', 'abcd.1234'),
(237, 'klgshmedrec', 'abcd.1234'),
(238, 'klgshspd', 'abcd.1234'),
(239, 'klgshot', 'abcd.1234'),
(240, 'klgshdw', 'abcd.1234'),
(241, 'klgshout', 'abcd.1234');

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE IF NOT EXISTS `form` (
`id` int(5) unsigned zerofill NOT NULL,
  `name` varchar(100) NOT NULL,
  `durationFrom` date NOT NULL,
  `durationTo` date NOT NULL,
  `timeFrom` time NOT NULL,
  `timeTo` time NOT NULL,
  `companyName` varchar(500) NOT NULL,
  `svName` varchar(50) NOT NULL,
  `icNo` varchar(14) NOT NULL,
  `contactNo` varchar(15) NOT NULL,
  `longTermContract` varchar(100) NOT NULL,
  `workersName` varchar(800) NOT NULL,
  `exactLocation` varchar(100) NOT NULL,
  `workType` varchar(500) NOT NULL,
  `hazards` varchar(500) NOT NULL,
  `briefDate` date NOT NULL,
  `briefTime` time NOT NULL,
  `briefConducted` varchar(50) NOT NULL,
  `ppe` varchar(500) NOT NULL,
  `status` varchar(50) DEFAULT 'in progress',
  `applicantID` int(11) NOT NULL,
  `adminID` int(11) NOT NULL,
  `worksite` varchar(500) NOT NULL,
  `infection` varchar(500) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `services` varchar(255) NOT NULL,
  `passNo` varchar(255) NOT NULL,
  `remark` varchar(500) NOT NULL,
  `is_notified` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permit`
--

CREATE TABLE IF NOT EXISTS `permit` (
`permitID` int(11) NOT NULL,
  `signC` longtext NOT NULL,
  `nameC` varchar(50) NOT NULL,
  `positionC` varchar(20) NOT NULL,
  `dateC` date NOT NULL,
  `timeC` time NOT NULL,
  `signA` longtext NOT NULL,
  `nameA` varchar(50) NOT NULL,
  `positionA` varchar(20) NOT NULL,
  `dateA` date NOT NULL,
  `timeA` time NOT NULL,
  `signI` longtext NOT NULL,
  `nameI` varchar(50) NOT NULL,
  `positionI` varchar(20) NOT NULL,
  `dateI` date NOT NULL,
  `timeI` time NOT NULL,
  `signS` longtext NOT NULL,
  `nameS` varchar(50) NOT NULL,
  `positionS` varchar(20) NOT NULL,
  `dateS` date NOT NULL,
  `timeS` time NOT NULL,
  `id` int(5) unsigned zerofill DEFAULT NULL,
  `file` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE IF NOT EXISTS `services` (
`id` int(11) NOT NULL,
  `serviceName` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `serviceName`) VALUES
(4, 'Patient Service'),
(5, 'Information Technology'),
(7, 'Business Office'),
(8, 'Revenue Cycle'),
(9, 'Case Review'),
(10, 'Public Relation'),
(11, 'Safety & Health'),
(12, 'Outsource'),
(13, 'Healthcare Engineering'),
(29, 'Purchasing'),
(30, 'Accident and Emergency'),
(31, 'Accounts'),
(32, 'Administration'),
(33, 'Audiology'),
(34, 'Business Development'),
(35, 'Business Development / Office'),
(36, 'Clinical Services & Clinical Quality'),
(37, 'Consultant Clinic'),
(38, 'Customer Care & Experience'),
(39, 'Customer Service'),
(40, 'Diagnostic Imaging Services'),
(41, 'Dietary'),
(42, 'Endoscopy Room'),
(43, 'Finance'),
(44, 'Haemodialysis'),
(45, 'Health Information Management Services'),
(46, 'Health Screening'),
(47, 'Healthcare Engineering Services'),
(48, 'Human Resources Management'),
(49, 'ICU/CCU/CICU'),
(50, 'Klinik Waqaf An-Nur'),
(51, 'KPJ Wellness Services'),
(52, 'Marketing & Corporate Communication'),
(53, 'Marketing Department'),
(54, 'Maternity'),
(55, 'Medical Officer'),
(56, 'Medical Records'),
(57, 'Medical Ward'),
(58, 'Nursing Administration'),
(59, 'Operation Theatre'),
(60, 'Optometrist'),
(61, 'Outsource Services'),
(62, 'Paediatric Ward'),
(63, 'Patient Liaison Services'),
(64, 'Pharmacy'),
(65, 'Physiotherapy'),
(66, 'Public Relation Department'),
(67, 'Public Relations and Marketing'),
(68, 'Quality'),
(69, 'Risk & Compliance Services'),
(70, 'Surgical Ward');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
 ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `applicant`
--
ALTER TABLE `applicant`
 ADD PRIMARY KEY (`applicantID`);

--
-- Indexes for table `form`
--
ALTER TABLE `form`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permit`
--
ALTER TABLE `permit`
 ADD PRIMARY KEY (`permitID`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `applicant`
--
ALTER TABLE `applicant`
MODIFY `applicantID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=242;
--
-- AUTO_INCREMENT for table `form`
--
ALTER TABLE `form`
MODIFY `id` int(5) unsigned zerofill NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `permit`
--
ALTER TABLE `permit`
MODIFY `permitID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=71;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
