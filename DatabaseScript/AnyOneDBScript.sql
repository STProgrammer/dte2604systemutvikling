-- MySQL Script generated by MySQL Workbench
-- Sat Feb 27 14:54:24 2021
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `Users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Users` ;

CREATE TABLE IF NOT EXISTS `Users` (
  `UserID` INT NOT NULL,
  `Username` VARCHAR(45) NOT NULL,
  `Firstname` VARCHAR(90) NOT NULL,
  `Lastname` VARCHAR(90) NOT NULL,
  `Address` VARCHAR(90) NOT NULL,
  `Zip code` INT(6) NOT NULL,
  `City` VARCHAR(45) NOT NULL,
  `Phone number` VARCHAR(45) NULL,
  `Mobile number` VARCHAR(45) NULL,
  `Group` VARCHAR(45) NULL,
  `Project` VARCHAR(45) NULL,
  `Email address` VARCHAR(90) NOT NULL,
  `IM address` VARCHAR(45) NOT NULL,
  `Date registered` TIMESTAMP NOT NULL,
  `Password` VARCHAR(100) NOT NULL,
  `Rights` INT(1) NOT NULL DEFAULT 0,
  `Is admin` TINYINT NOT NULL DEFAULT 0,
  `Is project leader` TINYINT NOT NULL DEFAULT 0,
  `Is group leader` TINYINT NOT NULL DEFAULT 0,
  `Is working` TINYINT NOT NULL DEFAULT 0,
  `Is customer` TINYINT NOT NULL DEFAULT 0,
  `Verified` TINYINT NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`),
  UNIQUE INDEX `UserID_UNIQUE` (`UserID` ASC) VISIBLE,
  UNIQUE INDEX `Username_UNIQUE` (`Username` ASC) VISIBLE,
  UNIQUE INDEX `Email address_UNIQUE` (`Email address` ASC) VISIBLE)
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
  `Status` INT(1) NULL,
  `Customer` INT NULL,
  PRIMARY KEY (`Project name`),
  INDEX `Project leader FK_idx` (`Project leader` ASC) VISIBLE,
  INDEX `Projects Customer FK_idx` (`Customer` ASC) VISIBLE,
  CONSTRAINT `Projects Project leader FK`
    FOREIGN KEY (`Project leader`)
    REFERENCES `Users` (`UserID`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `Projects Customer FK`
    FOREIGN KEY (`Customer`)
    REFERENCES `Users` (`UserID`)
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
-- Table `Tasks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Tasks` ;

CREATE TABLE IF NOT EXISTS `Tasks` (
  `TaskID` INT NOT NULL,
  `Task name` VARCHAR(45) NOT NULL,
  `Project` VARCHAR(45) NULL,
  `Category` VARCHAR(45) NULL,
  `Start time` TIMESTAMP NULL,
  `Finish time` TIMESTAMP NULL,
  `Status` INT(1) NULL COMMENT '0 = not started\n1 = started\n2 = paused\n3 = finished',
  PRIMARY KEY (`TaskID`),
  INDEX `Tasks Project name FK_idx` (`Project` ASC) VISIBLE,
  INDEX `Tasks Category name FK_idx` (`Category` ASC) VISIBLE,
  CONSTRAINT `Tasks Project name FK`
    FOREIGN KEY (`Project`)
    REFERENCES `Projects` (`Project name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `Tasks Category name FK`
    FOREIGN KEY (`Category`)
    REFERENCES `TaskCategories` (`Category name`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Hours`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Hours` ;

CREATE TABLE IF NOT EXISTS `Hours` (
  `TaskID` INT NOT NULL,
  `Who worked` INT NOT NULL,
  `Start time` TIMESTAMP NULL,
  `End time` TIMESTAMP NULL,
  `Activated / deactivated` TINYINT NULL DEFAULT 1,
  PRIMARY KEY (`TaskID`, `Who worked`),
  INDEX `Who worked ID_idx` (`Who worked` ASC) VISIBLE,
  CONSTRAINT `Hours TaskID FK`
    FOREIGN KEY (`TaskID`)
    REFERENCES `Tasks` (`TaskID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `Hours Who worked ID`
    FOREIGN KEY (`Who worked`)
    REFERENCES `Users` (`UserID`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Groups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Groups` ;

CREATE TABLE IF NOT EXISTS `Groups` (
  `Group name` VARCHAR(45) NOT NULL,
  `Is admin` TINYINT NOT NULL DEFAULT 0,
  `Group leader` INT NULL,
  PRIMARY KEY (`Group name`),
  INDEX `Groups Group leader_idx` (`Group leader` ASC) VISIBLE,
  CONSTRAINT `Groups Group leader`
    FOREIGN KEY (`Group leader`)
    REFERENCES `Users` (`UserID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Tags` ;

CREATE TABLE IF NOT EXISTS `Tags` (
  `Tag` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`Tag`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TasksAndTags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TasksAndTags` ;

CREATE TABLE IF NOT EXISTS `TasksAndTags` (
  `Tag` VARCHAR(45) NOT NULL,
  `TaskID` INT NOT NULL,
  INDEX `TasksAndTags Tag FK_idx` (`Tag` ASC) VISIBLE,
  INDEX `TasksAndTags TaskID FK_idx` (`TaskID` ASC) VISIBLE,
  PRIMARY KEY (`Tag`, `TaskID`),
  CONSTRAINT `TasksAndTags Tag FK`
    FOREIGN KEY (`Tag`)
    REFERENCES `Tags` (`Tag`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `TasksAndTags TaskID FK`
    FOREIGN KEY (`TaskID`)
    REFERENCES `Tasks` (`TaskID`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
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
-- Table `Reports`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Reports` ;

CREATE TABLE IF NOT EXISTS `Reports` (
  `Report name` VARCHAR(45) NOT NULL,
  `Generated by` INT NOT NULL,
  `Date updated` TIMESTAMP NULL,
  `Report` LONGBLOB NULL,
  `Report type` VARCHAR(30) NULL,
  `Rights` INT(1) NOT NULL,
  PRIMARY KEY (`Report name`),
  INDEX `Report type_idx` (`Report type` ASC) VISIBLE,
  INDEX `Generated by FK_idx` (`Generated by` ASC) VISIBLE,
  CONSTRAINT `Reports Report type FK`
    FOREIGN KEY (`Report type`)
    REFERENCES `Report types` (`Report type`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE,
  CONSTRAINT `Reporst Generated by FK`
    FOREIGN KEY (`Generated by`)
    REFERENCES `Users` (`UserID`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `GroupsAndProjects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GroupsAndProjects` ;

CREATE TABLE IF NOT EXISTS `GroupsAndProjects` (
  `Group name` VARCHAR(45) NOT NULL,
  `Project name` VARCHAR(45) NOT NULL,
  INDEX `GroupsAndProjects Group name FK_idx` (`Group name` ASC) VISIBLE,
  INDEX `GroupsAndProjects Project name FK_idx` (`Project name` ASC) VISIBLE,
  PRIMARY KEY (`Group name`, `Project name`),
  CONSTRAINT `GroupsAndProjects Group name FK`
    FOREIGN KEY (`Group name`)
    REFERENCES `Groups` (`Group name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `GroupsAndProjects Project name FK`
    FOREIGN KEY (`Project name`)
    REFERENCES `Projects` (`Project name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UsersAndTasks`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UsersAndTasks` ;

CREATE TABLE IF NOT EXISTS `UsersAndTasks` (
  `UserID` INT NOT NULL,
  `TaskID` INT NOT NULL,
  INDEX `UsersAndTasks UserID FK_idx` (`UserID` ASC) VISIBLE,
  INDEX `UsersAndTasks TaskID FK_idx` (`TaskID` ASC) VISIBLE,
  PRIMARY KEY (`UserID`, `TaskID`),
  CONSTRAINT `UsersAndTasks UserID FK`
    FOREIGN KEY (`UserID`)
    REFERENCES `Users` (`UserID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `UsersAndTasks TaskID FK`
    FOREIGN KEY (`TaskID`)
    REFERENCES `Tasks` (`TaskID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UsersAndGroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UsersAndGroups` ;

CREATE TABLE IF NOT EXISTS `UsersAndGroups` (
  `Group name` VARCHAR(45) NOT NULL,
  `UsersID` INT NOT NULL,
  INDEX `UsersAndGroups Group name FK_idx` (`Group name` ASC) VISIBLE,
  INDEX `UsersAndGroups UserID_idx` (`UsersID` ASC) VISIBLE,
  PRIMARY KEY (`Group name`, `UsersID`),
  CONSTRAINT `UsersAndGroups Group name FK`
    FOREIGN KEY (`Group name`)
    REFERENCES `Groups` (`Group name`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `UsersAndGroups UserID`
    FOREIGN KEY (`UsersID`)
    REFERENCES `Users` (`UserID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UsersAndProjects`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UsersAndProjects` ;

CREATE TABLE IF NOT EXISTS `UsersAndProjects` (
  `UserID` INT NOT NULL,
  `Project name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`UserID`, `Project name`),
  INDEX `UsersAndProjects Project name_idx` (`Project name` ASC) VISIBLE,
  CONSTRAINT `UsersAndProjects UserID`
    FOREIGN KEY (`UserID`)
    REFERENCES `Users` (`UserID`)
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
  `First Task` INT NOT NULL,
  `Second Task` INT NOT NULL,
  PRIMARY KEY (`First Task`, `Second Task`),
  INDEX `Second Task FK_idx` (`Second Task` ASC) VISIBLE,
  CONSTRAINT `TaskDependencies First Task FK`
    FOREIGN KEY (`First Task`)
    REFERENCES `Tasks` (`TaskID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `TaskDependencies Second Task FK`
    FOREIGN KEY (`Second Task`)
    REFERENCES `Tasks` (`TaskID`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `TasksAndGroups`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `TasksAndGroups` ;

CREATE TABLE IF NOT EXISTS `TasksAndGroups` (
  `TaskID` INT NOT NULL,
  `Group name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`TaskID`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `UnverifiedUsers`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `UnverifiedUsers` ;

CREATE TABLE IF NOT EXISTS `UnverifiedUsers` (
  `Username` VARCHAR(45) NOT NULL,
  `Firstname` VARCHAR(90) NOT NULL,
  `Lastname` VARCHAR(90) NOT NULL,
  `Address` VARCHAR(90) NOT NULL,
  `Zip code` INT(6) NOT NULL,
  `City` VARCHAR(45) NOT NULL,
  `Phone number` VARCHAR(45) NULL,
  `Mobile number` VARCHAR(45) NULL,
  `Group` VARCHAR(45) NULL,
  `Project` VARCHAR(45) NULL,
  `Email address` VARCHAR(90) NULL,
  `IM address` VARCHAR(45) NULL,
  `Date registered` TIMESTAMP NOT NULL,
  `Password` VARCHAR(100) NOT NULL,
  `Rights` INT(1) NOT NULL DEFAULT 0,
  `Is admin` TINYINT NOT NULL DEFAULT 0,
  `Is project leader` TINYINT NOT NULL DEFAULT 0,
  `Is group leader` TINYINT NOT NULL DEFAULT 0,
  `Is working` TINYINT NOT NULL DEFAULT 0,
  `Is customer` TINYINT NOT NULL DEFAULT 0,
  UNIQUE INDEX `Username_UNIQUE` (`Username` ASC))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
