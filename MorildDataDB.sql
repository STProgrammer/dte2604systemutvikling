-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 28, 2021 at 02:12 PM
-- Server version: 10.5.9-MariaDB
-- PHP Version: 7.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stud_v20_keser`
--

-- --------------------------------------------------------

--
-- Table structure for table `Groups`
--

CREATE TABLE `Groups` (
  `groupID` int(11) NOT NULL,
  `projectName` varchar(45) COLLATE utf8_danish_ci DEFAULT NULL,
  `groupName` varchar(45) COLLATE utf8_danish_ci NOT NULL,
  `isAdmin` tinyint(4) NOT NULL DEFAULT 0,
  `groupLeader` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Hours`
--

CREATE TABLE `Hours` (
  `hourID` int(11) NOT NULL,
  `taskID` int(11) DEFAULT NULL,
  `whoWorked` int(11) NOT NULL,
  `startTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `endTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `timeWorked` bigint(20) DEFAULT 0,
  `activated` tinyint(1) NOT NULL DEFAULT 1,
  `location` varchar(30) COLLATE utf8_danish_ci DEFAULT NULL,
  `phaseID` int(11) DEFAULT NULL,
  `absenceType` varchar(30) COLLATE utf8_danish_ci DEFAULT NULL,
  `overtimeType` int(1) DEFAULT NULL,
  `comment` longtext COLLATE utf8_danish_ci DEFAULT NULL,
  `commentBoss` longtext COLLATE utf8_danish_ci DEFAULT NULL,
  `isChanged` tinyint(1) NOT NULL DEFAULT 0,
  `stampingStatus` tinyint(1) NOT NULL DEFAULT 0,
  `taskType` varchar(30) COLLATE utf8_danish_ci NOT NULL DEFAULT 'Definert oppgave'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Triggers `Hours`
--
DELIMITER $$
CREATE TRIGGER `calculateTimeWorked` BEFORE UPDATE ON `Hours` FOR EACH ROW SET NEW.timeWorked = time_to_sec(TIMEDIFF(new.endTime, new.startTime))
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `HoursLogs`
--

CREATE TABLE `HoursLogs` (
  `logID` int(11) NOT NULL,
  `timeChanged` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `hourID` int(11) NOT NULL,
  `taskID` int(11) DEFAULT NULL,
  `whoWorked` int(11) NOT NULL,
  `startTime` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `endTime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `timeWorked` bigint(20) NOT NULL DEFAULT 0,
  `activated` tinyint(4) NOT NULL,
  `location` varchar(30) COLLATE utf8_danish_ci DEFAULT NULL,
  `phaseID` int(11) DEFAULT NULL,
  `absenceType` varchar(30) COLLATE utf8_danish_ci DEFAULT NULL,
  `overtimeType` int(1) DEFAULT NULL,
  `comment` longtext COLLATE utf8_danish_ci DEFAULT NULL,
  `commentBoss` longtext COLLATE utf8_danish_ci DEFAULT NULL,
  `isChanged` tinyint(4) NOT NULL,
  `stampingStatus` tinyint(4) NOT NULL,
  `taskType` varchar(30) COLLATE utf8_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Phases`
--

CREATE TABLE `Phases` (
  `phaseID` int(11) NOT NULL,
  `phaseName` varchar(45) COLLATE utf8_danish_ci NOT NULL,
  `projectName` varchar(45) COLLATE utf8_danish_ci NOT NULL,
  `startTime` date DEFAULT NULL,
  `finishTime` date DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci COMMENT='					';

-- --------------------------------------------------------

--
-- Table structure for table `ProgressTable`
--

CREATE TABLE `ProgressTable` (
  `progressID` int(11) NOT NULL,
  `projectName` varchar(45) COLLATE utf8_danish_ci DEFAULT NULL,
  `phase` int(11) DEFAULT NULL,
  `sumEstimate` int(11) NOT NULL DEFAULT 0,
  `sumEstimateDone` int(11) NOT NULL DEFAULT 0,
  `idealProgress` int(11) NOT NULL DEFAULT 0,
  `sumTimeSpent` int(11) NOT NULL DEFAULT 0,
  `registerDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Projects`
--

CREATE TABLE `Projects` (
  `projectID` int(11) NOT NULL,
  `projectName` varchar(45) COLLATE utf8_danish_ci NOT NULL,
  `projectLeader` int(11) DEFAULT NULL,
  `startTime` date DEFAULT NULL,
  `finishTime` date DEFAULT NULL,
  `status` int(1) NOT NULL,
  `customer` int(11) DEFAULT NULL,
  `isAcceptedByAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `activePhase` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Status`
--

CREATE TABLE `Status` (
  `status` varchar(30) COLLATE utf8_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Dumping data for table `Status`
--

INSERT INTO `Status` (`status`) VALUES
('Annet'),
('Fri'),
('Møte'),
('N/A'),
('På jobb'),
('På reise'),
('Syk');

-- --------------------------------------------------------

--
-- Table structure for table `TaskCategories`
--

CREATE TABLE `TaskCategories` (
  `categoryName` varchar(30) COLLATE utf8_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Dumping data for table `TaskCategories`
--

INSERT INTO `TaskCategories` (`categoryName`) VALUES
('Administrativt'),
('Hjemmekontor'),
('Kontor'),
('Kurs'),
('Møte'),
('Prosjekt'),
('Rapportering'),
('Selvstudie');

-- --------------------------------------------------------

--
-- Table structure for table `TaskDependencies`
--

CREATE TABLE `TaskDependencies` (
  `firstTask` int(11) DEFAULT NULL,
  `secondTask` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Tasks`
--

CREATE TABLE `Tasks` (
  `taskID` int(11) NOT NULL,
  `phaseID` int(11) DEFAULT NULL,
  `groupID` int(11) DEFAULT NULL,
  `parentTask` int(11) DEFAULT NULL,
  `taskName` varchar(45) COLLATE utf8_danish_ci NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `projectName` varchar(45) COLLATE utf8_danish_ci DEFAULT NULL,
  `timeSpent` int(11) NOT NULL DEFAULT 0,
  `estimatedTime` int(11) DEFAULT 0,
  `hasSubtask` tinyint(1) NOT NULL DEFAULT 0,
  `mainResponsible` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `userID` int(11) NOT NULL,
  `username` varchar(45) COLLATE utf8_danish_ci NOT NULL,
  `firstName` varchar(90) COLLATE utf8_danish_ci NOT NULL,
  `lastName` varchar(90) COLLATE utf8_danish_ci NOT NULL,
  `address` varchar(90) COLLATE utf8_danish_ci NOT NULL,
  `zipCode` varchar(10) COLLATE utf8_danish_ci NOT NULL,
  `city` varchar(45) COLLATE utf8_danish_ci NOT NULL,
  `phoneNumber` varchar(45) COLLATE utf8_danish_ci DEFAULT NULL,
  `mobileNumber` varchar(45) COLLATE utf8_danish_ci DEFAULT NULL,
  `emailAddress` varchar(60) COLLATE utf8_danish_ci NOT NULL,
  `IMAddress` varchar(45) COLLATE utf8_danish_ci DEFAULT NULL,
  `dateRegistered` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(100) COLLATE utf8_danish_ci NOT NULL,
  `userType` int(11) NOT NULL DEFAULT 1 COMMENT '0 = customer, 1 = temporary worker, 2 = worker, 3 = admin',
  `isProjectLeader` tinyint(1) NOT NULL DEFAULT 0,
  `isGroupLeader` tinyint(1) NOT NULL DEFAULT 0,
  `isEmailVerified` tinyint(1) NOT NULL DEFAULT 0,
  `isVerifiedByAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(30) COLLATE utf8_danish_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`userID`, `username`, `firstName`, `lastName`, `address`, `zipCode`, `city`, `phoneNumber`, `mobileNumber`, `emailAddress`, `IMAddress`, `dateRegistered`, `password`, `userType`, `isProjectLeader`, `isGroupLeader`, `isEmailVerified`, `isVerifiedByAdmin`, `status`) VALUES
(2, 'tanita', 'Tanita Fossli', 'Brustad', 'Marihandstien 18 B', '8515', 'Narvik', NULL, '+4798860052', 'tanita.f.brustad@uit.no', NULL, '2021-05-28 12:36:11', '$2y$10$F3Eyo1aW/1z/n6TL/9sLQOvBENmrkO9jDKd2i8tp29EQiiNhfibiq', 3, 0, 0, 1, 1, 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `UsersAndGroups`
--

CREATE TABLE `UsersAndGroups` (
  `groupID` int(11) NOT NULL,
  `userID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Groups`
--
ALTER TABLE `Groups`
  ADD PRIMARY KEY (`groupID`),
  ADD KEY `Groups Group leader_idx` (`groupLeader`),
  ADD KEY `Groups Project name_idx` (`projectName`);

--
-- Indexes for table `Hours`
--
ALTER TABLE `Hours`
  ADD PRIMARY KEY (`hourID`),
  ADD KEY `Hours User ID FK_idx` (`whoWorked`),
  ADD KEY `Hours Phase ID FK_idx` (`phaseID`),
  ADD KEY `Hours absence type_idx` (`absenceType`),
  ADD KEY `Hours Location_idx` (`location`),
  ADD KEY `Hours Task ID FK_idx` (`taskID`) USING BTREE,
  ADD KEY `Hours taskType FK_idx` (`taskType`) USING BTREE;

--
-- Indexes for table `HoursLogs`
--
ALTER TABLE `HoursLogs`
  ADD UNIQUE KEY `logID` (`logID`);

--
-- Indexes for table `Phases`
--
ALTER TABLE `Phases`
  ADD PRIMARY KEY (`phaseID`),
  ADD KEY `Phases Project name FK_idx` (`projectName`);

--
-- Indexes for table `ProgressTable`
--
ALTER TABLE `ProgressTable`
  ADD PRIMARY KEY (`progressID`),
  ADD KEY `progress_project_FK` (`projectName`);

--
-- Indexes for table `Projects`
--
ALTER TABLE `Projects`
  ADD PRIMARY KEY (`projectID`),
  ADD UNIQUE KEY `projectID_UNIQUE` (`projectID`),
  ADD UNIQUE KEY `projectName_UNIQUE` (`projectName`),
  ADD KEY `Project leader FK_idx` (`projectLeader`),
  ADD KEY `Projects Customer FK_idx` (`customer`),
  ADD KEY `activePhase FK` (`activePhase`) USING BTREE;

--
-- Indexes for table `Status`
--
ALTER TABLE `Status`
  ADD PRIMARY KEY (`status`);

--
-- Indexes for table `TaskCategories`
--
ALTER TABLE `TaskCategories`
  ADD PRIMARY KEY (`categoryName`);

--
-- Indexes for table `TaskDependencies`
--
ALTER TABLE `TaskDependencies`
  ADD KEY `First Task FK_idx` (`firstTask`),
  ADD KEY `TaskDependencies Second Task FK_idx` (`secondTask`);

--
-- Indexes for table `Tasks`
--
ALTER TABLE `Tasks`
  ADD PRIMARY KEY (`taskID`),
  ADD KEY `Tasks Project name FK_idx` (`projectName`),
  ADD KEY `Tasks phaseID FK_idx` (`phaseID`),
  ADD KEY `Tasks groupID FK_idx` (`groupID`),
  ADD KEY `Tasks parentTask_idx` (`parentTask`),
  ADD KEY `Tasks mainResponsible FK_idx` (`mainResponsible`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `UserID_UNIQUE` (`userID`),
  ADD UNIQUE KEY `Username_UNIQUE` (`username`),
  ADD UNIQUE KEY `Email address_UNIQUE` (`emailAddress`),
  ADD KEY `Users Status FK_idx` (`status`);

--
-- Indexes for table `UsersAndGroups`
--
ALTER TABLE `UsersAndGroups`
  ADD PRIMARY KEY (`userID`,`groupID`),
  ADD KEY `UsersAndGroups UserID_idx` (`userID`),
  ADD KEY `UsersAndGroups Group ID FK` (`groupID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Groups`
--
ALTER TABLE `Groups`
  MODIFY `groupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `Hours`
--
ALTER TABLE `Hours`
  MODIFY `hourID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=442;

--
-- AUTO_INCREMENT for table `HoursLogs`
--
ALTER TABLE `HoursLogs`
  MODIFY `logID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `Phases`
--
ALTER TABLE `Phases`
  MODIFY `phaseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `ProgressTable`
--
ALTER TABLE `ProgressTable`
  MODIFY `progressID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `Projects`
--
ALTER TABLE `Projects`
  MODIFY `projectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Tasks`
--
ALTER TABLE `Tasks`
  MODIFY `taskID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Groups`
--
ALTER TABLE `Groups`
  ADD CONSTRAINT `Groups Group leader` FOREIGN KEY (`groupLeader`) REFERENCES `Users` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Groups Project name` FOREIGN KEY (`projectName`) REFERENCES `Projects` (`projectName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Hours`
--
ALTER TABLE `Hours`
  ADD CONSTRAINT `Hours Absence type` FOREIGN KEY (`absenceType`) REFERENCES `Absence types` (`absenceType`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Hours Phase ID FK` FOREIGN KEY (`phaseID`) REFERENCES `Phases` (`phaseID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Hours Task ID FK` FOREIGN KEY (`taskID`) REFERENCES `Tasks` (`taskID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Hours User ID FK` FOREIGN KEY (`whoWorked`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Hours taskType FK` FOREIGN KEY (`taskType`) REFERENCES `TaskCategories` (`categoryName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Phases`
--
ALTER TABLE `Phases`
  ADD CONSTRAINT `Phases Project name FK` FOREIGN KEY (`projectName`) REFERENCES `Projects` (`projectName`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ProgressTable`
--
ALTER TABLE `ProgressTable`
  ADD CONSTRAINT `progress_project_FK` FOREIGN KEY (`projectName`) REFERENCES `Projects` (`projectName`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `Projects`
--
ALTER TABLE `Projects`
  ADD CONSTRAINT `Projects Customer FK` FOREIGN KEY (`customer`) REFERENCES `Users` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Projects Project leader FK` FOREIGN KEY (`projectLeader`) REFERENCES `Users` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Projects activePhase FK` FOREIGN KEY (`activePhase`) REFERENCES `Phases` (`phaseID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `TaskDependencies`
--
ALTER TABLE `TaskDependencies`
  ADD CONSTRAINT `TaskDependencies First Task FK` FOREIGN KEY (`firstTask`) REFERENCES `Tasks` (`taskID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `TaskDependencies Second Task FK` FOREIGN KEY (`secondTask`) REFERENCES `Tasks` (`taskID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `Tasks`
--
ALTER TABLE `Tasks`
  ADD CONSTRAINT `Tasks Project name FK` FOREIGN KEY (`projectName`) REFERENCES `Projects` (`projectName`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Tasks groupID FK` FOREIGN KEY (`groupID`) REFERENCES `Groups` (`groupID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Tasks mainResponsible FK` FOREIGN KEY (`mainResponsible`) REFERENCES `Users` (`userID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Tasks parentTask FK` FOREIGN KEY (`parentTask`) REFERENCES `Tasks` (`taskID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Tasks phaseID FK` FOREIGN KEY (`phaseID`) REFERENCES `Phases` (`phaseID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `Users`
--
ALTER TABLE `Users`
  ADD CONSTRAINT `Users Status FK` FOREIGN KEY (`status`) REFERENCES `Status` (`status`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `UsersAndGroups`
--
ALTER TABLE `UsersAndGroups`
  ADD CONSTRAINT `UsersAndGroups Group ID FK` FOREIGN KEY (`groupID`) REFERENCES `Groups` (`groupID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `UsersAndGroups UserID` FOREIGN KEY (`userID`) REFERENCES `Users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
