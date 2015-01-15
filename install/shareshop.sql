-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema shareshop
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema shareshop
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `shareshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `shareshop` ;

-- -----------------------------------------------------
-- Table `shareshop`.`sha_location`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_location` (
  `loc_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `loc_street` VARCHAR(255) NOT NULL,
  `loc_postcode` VARCHAR(10) NOT NULL,
  `loc_town` VARCHAR(255) NOT NULL,
  `loc_mapLat` VARCHAR(255) NOT NULL,
  `loc_mapLng` VARCHAR(255) NULL,
  PRIMARY KEY (`loc_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_user` (
  `usr_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `usr_username` VARCHAR(255) NOT NULL,
  `usr_password` CHAR(32) NOT NULL COMMENT 'http://stackoverflow.com/questions/247304/what-data-type-to-use-for-hashed-password-field-and-what-length',
  `usr_email` VARCHAR(255) NOT NULL,
  `usr_salt` CHAR(10) BINARY NULL,
  `usr_language` CHAR(5) NULL,
  `usr_loc_id` INT(10) UNSIGNED NULL,
  PRIMARY KEY (`usr_id`),
  UNIQUE INDEX `username_UNIQUE` (`usr_username` ASC),
  UNIQUE INDEX `usr_email_UNIQUE` (`usr_email` ASC),
  INDEX `fk_sha_location_id` (`usr_loc_id` ASC),
  CONSTRAINT `fk_sha_user_sha_location`
    FOREIGN KEY (`usr_loc_id`)
    REFERENCES `shareshop`.`sha_location` (`loc_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_articles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_articles` (
  `art_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `art_name` VARCHAR(255) NOT NULL,
  `art_description` VARCHAR(1000) NOT NULL,
  `art_image` VARCHAR(255) NULL,
  `art_usr_id` INT(10) UNSIGNED NOT NULL,
  `art_creation_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`art_id`),
  INDEX `sha_fk_art_usr_idx` (`art_usr_id` ASC),
  CONSTRAINT `sha_fk_art_usr`
    FOREIGN KEY (`art_usr_id`)
    REFERENCES `shareshop`.`sha_user` (`usr_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_categories` (
  `cat_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `cat_name` VARCHAR(255) NOT NULL,
  `cat_parent_id` INT(10) UNSIGNED NULL,
  PRIMARY KEY (`cat_id`),
  INDEX `sha_cat_cat_idx` (`cat_parent_id` ASC),
  CONSTRAINT `sha_cat_cat`
    FOREIGN KEY (`cat_parent_id`)
    REFERENCES `shareshop`.`sha_categories` (`cat_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_session`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_session` (
  `session_id` CHAR(32) NOT NULL,
  `session_usr_id` INT(10) UNSIGNED NOT NULL,
  `session_state` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  `session_create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `session_update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `fk_sha_session_sha_user1_idx` (`session_usr_id` ASC),
  PRIMARY KEY (`session_id`),
  UNIQUE INDEX `session_id_UNIQUE` (`session_id` ASC),
  CONSTRAINT `fk_sha_session_sha_user1`
    FOREIGN KEY (`session_usr_id`)
    REFERENCES `shareshop`.`sha_user` (`usr_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);


-- -----------------------------------------------------
-- Table `shareshop`.`sha_art_cat_rel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_art_cat_rel` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `art_id` INT(10) UNSIGNED NOT NULL,
  `cat_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  INDEX `sha_fk_rel_cat_idx` (`cat_id` ASC),
  INDEX `sha_fk_art_cat_idx` (`art_id` ASC),
  CONSTRAINT `sha_fk_rel_cat`
    FOREIGN KEY (`cat_id`)
    REFERENCES `shareshop`.`sha_categories` (`cat_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sha_fk_rel_art`
    FOREIGN KEY (`art_id`)
    REFERENCES `shareshop`.`sha_articles` (`art_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_exchange`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_exchange` (
  `exchange_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `requesting_user` INT(10) UNSIGNED NOT NULL,
  `answering_user` INT(10) UNSIGNED NOT NULL,
  `requesting_rating` SMALLINT UNSIGNED NULL,
  `answering_rating` SMALLINT UNSIGNED NULL,
  `state` SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`exchange_id`),
  INDEX `fk_sha_request_sha_user3_idx` (`requesting_user` ASC),
  INDEX `fk_sha_request_sha_user4_idx` (`answering_user` ASC),
  CONSTRAINT `fk_sha_request_sha_user3`
    FOREIGN KEY (`requesting_user`)
    REFERENCES `shareshop`.`sha_user` (`usr_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sha_request_sha_user4`
    FOREIGN KEY (`answering_user`)
    REFERENCES `shareshop`.`sha_user` (`usr_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_exchange_step`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_exchange_step` (
  `step_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `exchange_id` INT UNSIGNED NOT NULL,
  `step_created` TIMESTAMP NOT NULL,
  `step_remark` TEXT NULL,
  `step_type` SMALLINT UNSIGNED NOT NULL,
  PRIMARY KEY (`step_id`),
  INDEX `fk_sha_request_step_sha_request1_idx` (`exchange_id` ASC),
  UNIQUE INDEX `unique_step_per_exchange` (`exchange_id` ASC, `step_type` ASC),
  CONSTRAINT `fk_sha_request_step_sha_request1`
    FOREIGN KEY (`exchange_id`)
    REFERENCES `shareshop`.`sha_exchange` (`exchange_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_exchange_step_item`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `shareshop`.`sha_exchange_step_item` (
  `step_id` INT UNSIGNED NOT NULL,
  `art_id` INT(10) UNSIGNED NOT NULL,
  INDEX `fk_sha_request_step_item_sha_request_step1_idx` (`step_id` ASC),
  INDEX `fk_sha_request_step_item_sha_articles1_idx` (`art_id` ASC),
  CONSTRAINT `fk_sha_request_step_item_sha_request_step1`
    FOREIGN KEY (`step_id`)
    REFERENCES `shareshop`.`sha_exchange_step` (`step_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_sha_request_step_item_sha_articles1`
    FOREIGN KEY (`art_id`)
    REFERENCES `shareshop`.`sha_articles` (`art_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
