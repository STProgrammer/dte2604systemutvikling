-- MySQL Script generated by MySQL Workbench
-- Tue Mar  9 15:55:27 2021
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `Status`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Status` ;

CREATE TABLE IF NOT EXISTS `Status` (
  `Status` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`Status`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Users` ;

CREATE TABLE IF NOT EXISTS `Users` (
  `User ID` INT NOT NULL AUTO_INCREMENT,
  `Username` VARCHAR(45) NOT NULL,
  `First name` VARCHAR(90) NOT NULL,
  `Last name` VARCHAR(90) NOT NULL,
  `Address` VARCHAR(90) NOT NULL,
  `Zip code` VARCHAR(10) NOT NULL,
  `City` VARCHAR(45) NOT NULL,
  `Phone number` VARCHAR(45) NULL,
  `Mobile number` VARCHAR(45) NULL,
  `Email address` VARCHAR(60) NOT NULL,
  `IM address` VARCHAR(45) NOT NULL,
  `Date registered` TIMESTAMP NOT NULL,
  `Password` VARCHAR(100) NOT NULL,
  `Is admin` TINYINT NOT NULL DEFAULT 0,
  `Is project leader` TINYINT NOT NULL DEFAULT 0,
  `Is group leader` TINYINT NOT NULL DEFAULT 0,
  `Is temporary` TINYINT NOT NULL DEFAULT 0,
  `Is customer` TINYINT NOT NULL DEFAULT 0,
  `Is email verified` TINYINT NOT NULL DEFAULT 0,
  `Is verified by admin` TINYINT NOT NULL DEFAULT 0,
  `Status` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`User ID`),
  UNIQUE INDEX `UserID_UNIQUE` (`User ID` ASC) VISIBLE,
  UNIQUE INDEX `Username_UNIQUE` (`Username` ASC) VISIBLE,
  UNIQUE INDEX `Email address_UNIQUE` (`Email address` ASC) VISIBLE,
  INDEX `Users Status FK_idx` (`Status` ASC) VISIBLE,
  CONSTRAINT `Users Status FK`
    FOREIGN KEY (`Status`)
    REFERENCES `Status` (`Status`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Projects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Projects` ;

CREATE TABLE IF NOT EXISTS `Projects` (
  `Project name` VARCHAR(45) NOT NULL,
  `Project leader` INT NULL,
  `Start time` TIMESTAMP NULL,
  `Finish time` TIMESTAMP NULL,
  `Status` INT(1) NOT NULL,
  `Customer` INT NULL,
  `Is accepted by admin` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`Project name`),
  INDEX `Project leader FK_idx` (`Project leader` ASC) VISIBLE,
  INDEX `Projects Customer FK_idx` (`Customer` ASC) VISIBLE,
  CONSTRAINT `Projects Project leader FK`
    FOREIGN KEY (`Project leader`)
    REFERENCES `Users` (`User ID`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `Projects Customer FK`
    FOREIGN KEY (`Customer`)
    REFERENCES `Users` (`User ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Groups` ;

CREATE TABLE IF NOT EXISTS `Groups` (
  `Group ID` INT NOT NULL AUTO_INCREMENT,
  `Group name` VARCHAR(45) NOT NULL,
  `Is admin` TINYINT NOT NULL DEFAULT 0,
  `Group leader` INT NULL,
  INDEX `Groups Group leader_idx` (`Group leader` ASC) VISIBLE,
  PRIMARY KEY (`Group ID`),
  CONSTRAINT `Groups Group leader`
    FOREIGN KEY (`Group leader`)
    REFERENCES `Users` (`User ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TaskCategories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TaskCategories` ;

CREATE TABLE IF NOT EXISTS `TaskCategories` (
  `Category name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Category name`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `GroupsAndProjects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GroupsAndProjects` ;

CREATE TABLE IF NOT EXISTS `GroupsAndProjects` (
  `Group ID` INT NOT NULL,
  `Project name` VARCHAR(45) NOT NULL,
  INDEX `GroupsAndProjects Project name FK_idx` (`Project name` ASC) VISIBLE,
  CONSTRAINT `GroupsAndProjects Group ID FK`
    FOREIGN KEY (`Group ID`)
    REFERENCES `Groups` (`Group ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `GroupsAndProjects Project name FK`
    FOREIGN KEY (`Project name`)
    REFERENCES `Projects` (`Project name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Tasks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Tasks` ;

CREATE TABLE IF NOT EXISTS `Tasks` (
  `Task ID` INT NOT NULL AUTO_INCREMENT,
  `Category` VARCHAR(45) NULL,
  `Parent task` VARCHAR(45) NULL,
  `Task name` VARCHAR(45) NOT NULL,
  `Start time` TIMESTAMP NULL,
  `Finish time` TIMESTAMP NULL,
  `Status` INT(1) NOT NULL DEFAULT 0,
  `Project name` VARCHAR(45) NULL,
  `Estimated time` INT NULL,
  `Time left` INT NULL,
  `Has subtasks` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`Task ID`),
  INDEX `Tasks TaskCategories FK_idx` (`Category` ASC) VISIBLE,
  INDEX `Tasks Project name FK_idx` (`Project name` ASC) VISIBLE,
  CONSTRAINT `Tasks TaskCategories FK`
    FOREIGN KEY (`Category`)
    REFERENCES `TaskCategories` (`Category name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Tasks Project name FK`
    FOREIGN KEY (`Project name`)
    REFERENCES `Projects` (`Project name`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UsersAndTasks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UsersAndTasks` ;

CREATE TABLE IF NOT EXISTS `UsersAndTasks` (
  `User ID` INT NULL,
  `Task ID` INT NULL,
  INDEX `UsersAndTasks UserID FK_idx` (`User ID` ASC) VISIBLE,
  INDEX `UsersAndTasks Task ID FK_idx` (`Task ID` ASC) VISIBLE,
  CONSTRAINT `UsersAndTasks UserID FK`
    FOREIGN KEY (`User ID`)
    REFERENCES `Users` (`User ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `UsersAndTasks Task ID FK`
    FOREIGN KEY (`Task ID`)
    REFERENCES `Tasks` (`Task ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UsersAndGroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UsersAndGroups` ;

CREATE TABLE IF NOT EXISTS `UsersAndGroups` (
  `Group ID` INT NULL,
  `User ID` INT NULL,
  INDEX `UsersAndGroups UserID_idx` (`User ID` ASC) VISIBLE,
  CONSTRAINT `UsersAndGroups Group ID FK`
    FOREIGN KEY (`Group ID`)
    REFERENCES `Groups` (`Group ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `UsersAndGroups UserID`
    FOREIGN KEY (`User ID`)
    REFERENCES `Users` (`User ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UsersAndProjects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UsersAndProjects` ;

CREATE TABLE IF NOT EXISTS `UsersAndProjects` (
  `User ID` INT NULL,
  `Project name` VARCHAR(45) NULL,
  INDEX `UsersAndProjects Project name_idx` (`Project name` ASC) VISIBLE,
  CONSTRAINT `UsersAndProjects User ID`
    FOREIGN KEY (`User ID`)
    REFERENCES `Users` (`User ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `UsersAndProjects Project name`
    FOREIGN KEY (`Project name`)
    REFERENCES `Projects` (`Project name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TaskDependencies`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TaskDependencies` ;

CREATE TABLE IF NOT EXISTS `TaskDependencies` (
  `First Task` INT NULL,
  `Second Task` INT NULL,
  INDEX `First Task FK_idx` (`First Task` ASC) VISIBLE,
  INDEX `TaskDependencies Second Task FK_idx` (`Second Task` ASC) VISIBLE,
  CONSTRAINT `TaskDependencies First Task FK`
    FOREIGN KEY (`First Task`)
    REFERENCES `Tasks` (`Task ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `TaskDependencies Second Task FK`
    FOREIGN KEY (`Second Task`)
    REFERENCES `Tasks` (`Task ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Report types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Report types` ;

CREATE TABLE IF NOT EXISTS `Report types` (
  `Report type` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`Report type`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TasksAndGroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TasksAndGroups` ;

CREATE TABLE IF NOT EXISTS `TasksAndGroups` (
  `Task ID` INT NULL,
  `Group ID` INT NULL,
  INDEX `TasksAndGorups Group name FK_idx` (`Group ID` ASC) VISIBLE,
  INDEX `TasksAndGroups Task ID_idx` (`Task ID` ASC) VISIBLE,
  CONSTRAINT `TasksAndGroups Group ID FK`
    FOREIGN KEY (`Group ID`)
    REFERENCES `Groups` (`Group ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `TasksAndGroups Task ID`
    FOREIGN KEY (`Task ID`)
    REFERENCES `Tasks` (`Task ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Phases`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Phases` ;

CREATE TABLE IF NOT EXISTS `Phases` (
  `Phase ID` INT NOT NULL AUTO_INCREMENT,
  `Phase name` VARCHAR(45) NOT NULL,
  `Project name` VARCHAR(45) NOT NULL,
  `Start time` TIMESTAMP NULL,
  `Finish time` TIMESTAMP NULL,
  `Status` INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`Phase ID`),
  INDEX `Phases Project name FK_idx` (`Project name` ASC) VISIBLE,
  CONSTRAINT `Phases Project name FK`
    FOREIGN KEY (`Project name`)
    REFERENCES `Projects` (`Project name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `PhasesAndTasks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `PhasesAndTasks` ;

CREATE TABLE IF NOT EXISTS `PhasesAndTasks` (
  `Phase ID` INT NULL,
  `Task ID` INT NULL,
  INDEX `PhasesAndTasks Phase ID_idx` (`Phase ID` ASC) VISIBLE,
  INDEX `PhasesAndTasks Task ID_idx` (`Task ID` ASC) VISIBLE,
  CONSTRAINT `PhasesAndTasks Phase ID`
    FOREIGN KEY (`Phase ID`)
    REFERENCES `Phases` (`Phase ID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `PhasesAndTasks Task ID`
    FOREIGN KEY (`Task ID`)
    REFERENCES `Tasks` (`Task ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Absence types`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Absence types` ;

CREATE TABLE IF NOT EXISTS `Absence types` (
  `Absence type` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`Absence type`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Location`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Location` ;

CREATE TABLE IF NOT EXISTS `Location` (
  `Location` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`Location`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Hours`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Hours` ;

CREATE TABLE IF NOT EXISTS `Hours` (
  `Hour ID` INT NOT NULL AUTO_INCREMENT,
  `Task ID` INT NOT NULL,
  `Who worked` INT NOT NULL,
  `Start time` TIMESTAMP NULL,
  `End time` TIMESTAMP NULL,
  `Activated` TINYINT NOT NULL DEFAULT 1,
  `Location` VARCHAR(30) NULL,
  `Phase ID` INT NULL,
  `Absence type` VARCHAR(30) NULL,
  `Overtime type` INT(1) NULL,
  `Comment` LONGTEXT NULL,
  `Is changed` TINYINT NOT NULL DEFAULT 0,
  `Stamping status` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`Hour ID`),
  INDEX `Hours Task ID FK_idx` (`Task ID` ASC) VISIBLE,
  INDEX `Hours User ID FK_idx` (`Who worked` ASC) VISIBLE,
  INDEX `Hours Phase ID FK_idx` (`Phase ID` ASC) VISIBLE,
  INDEX `Hours absence type_idx` (`Absence type` ASC) VISIBLE,
  INDEX `Hours Location_idx` (`Location` ASC) VISIBLE,
  CONSTRAINT `Hours Task ID FK`
    FOREIGN KEY (`Task ID`)
    REFERENCES `Tasks` (`Task ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Hours User ID FK`
    FOREIGN KEY (`Who worked`)
    REFERENCES `Users` (`User ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Hours Phase ID FK`
    FOREIGN KEY (`Phase ID`)
    REFERENCES `Phases` (`Phase ID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Hours Absence type`
    FOREIGN KEY (`Absence type`)
    REFERENCES `Absence types` (`Absence type`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `Hours Location`
    FOREIGN KEY (`Location`)
    REFERENCES `Location` (`Location`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Reports`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Reports` ;

CREATE TABLE IF NOT EXISTS `Reports` (
  `Report ID` INT NOT NULL AUTO_INCREMENT,
  `Report name` VARCHAR(45) NOT NULL,
  `Generated by` INT NULL,
  `Date updated` TIMESTAMP NULL,
  `Report` MEDIUMBLOB NOT NULL,
  `Report type` VARCHAR(30) NOT NULL,
  `File type` VARCHAR(30) NOT NULL,
  `Group ID` INT NULL,
  `Project name` VARCHAR(45) NULL,
  `Phase ID` INT NULL,
  PRIMARY KEY (`Report ID`),
  INDEX `Reports User ID FK_idx` (`Generated by` ASC) VISIBLE,
  INDEX `Reports Report types FK_idx` (`Report type` ASC) VISIBLE,
  INDEX `Reports Group ID FK_idx` (`Group ID` ASC) VISIBLE,
  INDEX `Reports Project name FK_idx` (`Project name` ASC) VISIBLE,
  INDEX `Reports Phase ID FK_idx` (`Phase ID` ASC) VISIBLE,
  CONSTRAINT `Reports User ID FK`
    FOREIGN KEY (`Generated by`)
    REFERENCES `Users` (`User ID`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `Reports Report types FK`
    FOREIGN KEY (`Report type`)
    REFERENCES `Report types` (`Report type`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `Reports Group ID FK`
    FOREIGN KEY (`Group ID`)
    REFERENCES `Groups` (`Group ID`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `Reports Project name FK`
    FOREIGN KEY (`Project name`)
    REFERENCES `Projects` (`Project name`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `Reports Phase ID FK`
    FOREIGN KEY (`Phase ID`)
    REFERENCES `Phases` (`Phase ID`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `Status`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Status` (`Status`) VALUES ('Sick');
INSERT INTO `Status` (`Status`) VALUES ('Travel');
INSERT INTO `Status` (`Status`) VALUES ('Meeting');
INSERT INTO `Status` (`Status`) VALUES ('Various');
INSERT INTO `Status` (`Status`) VALUES ('Working');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Users`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (1, 'abdullah', 'Abdullah', 'Karagøz', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'aka160@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (2, 'halil', 'Halil Ibrahim', 'Keser', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'hke006@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (3, 'tore', 'Tore', 'Bjerkan', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'tbj034@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (4, 'are', 'Are Magnus Lohne|', 'Abrahamsen', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'aab057@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (5, 'asbjoern', 'Asbjørn', 'Bjørge', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'abj075@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (6, 'nicholai', 'Nicholai Mørch', 'Rindarøy', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'nri007@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (7, 'joergen', 'Jørgen', 'Rypdal', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'jry017@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');
INSERT INTO `Users` (`User ID`, `Username`, `First name`, `Last name`, `Address`, `Zip code`, `City`, `Phone number`, `Mobile number`, `Email address`, `IM address`, `Date registered`, `Password`, `Is admin`, `Is project leader`, `Is group leader`, `Is temporary`, `Is customer`, `Is email verified`, `Is verified by admin`, `Status`) VALUES (8, 'tine', 'Tine Nathalie', 'Joramo', 'Some address 45', '4568', 'Oslo', '+478999898', '+478797987', 'tjo221@post.uit.no', 'someIMaddress', '2021-03-09 15:54:00', '$2y$10$34L1AX8nlMAeRm4kovfksOVeeohGUXCBnaCgTtvr6XbuY35W5gMlG', 1, 0, 0, 0, 0, 1, 1, 'Working');

COMMIT;


-- -----------------------------------------------------
-- Data for table `TaskCategories`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `TaskCategories` (`Category name`) VALUES ('Self-study');
INSERT INTO `TaskCategories` (`Category name`) VALUES ('Course');
INSERT INTO `TaskCategories` (`Category name`) VALUES ('Meeting');
INSERT INTO `TaskCategories` (`Category name`) VALUES ('Project Engineering');
INSERT INTO `TaskCategories` (`Category name`) VALUES ('Nothing ');
INSERT INTO `TaskCategories` (`Category name`) VALUES ('');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Report types`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Report types` (`Report type`) VALUES ('Standard reports');
INSERT INTO `Report types` (`Report type`) VALUES ('Progress charts');
INSERT INTO `Report types` (`Report type`) VALUES ('Project report');
INSERT INTO `Report types` (`Report type`) VALUES ('Group report');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Absence types`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Absence types` (`Absence type`) VALUES ('Sick');
INSERT INTO `Absence types` (`Absence type`) VALUES ('Travel');
INSERT INTO `Absence types` (`Absence type`) VALUES ('Holyday');

COMMIT;


-- -----------------------------------------------------
-- Data for table `Location`
-- -----------------------------------------------------
START TRANSACTION;
INSERT INTO `Location` (`Location`) VALUES ('Home');
INSERT INTO `Location` (`Location`) VALUES ('Outside of the office');
INSERT INTO `Location` (`Location`) VALUES ('Out of country');
INSERT INTO `Location` (`Location`) VALUES ('Office');

COMMIT;

