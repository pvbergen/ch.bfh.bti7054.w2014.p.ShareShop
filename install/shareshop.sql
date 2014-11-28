SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `shareshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `shareshop` ;

-- -----------------------------------------------------
-- Table `shareshop`.`sha_category`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `shareshop`.`sha_category` ;

CREATE  TABLE IF NOT EXISTS `shareshop`.`sha_category` (
  `cat_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `cat_name` VARCHAR(255) NOT NULL ,
  `cat_parentId` INT(10) UNSIGNED NULL ,
  PRIMARY KEY (`cat_id`) ,
  INDEX `sha_cat_cat` (`cat_parentId` ASC) ,
  CONSTRAINT `sha_cat_cat`
    FOREIGN KEY (`cat_parentId` )
    REFERENCES `shareshop`.`sha_category` (`cat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_location`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `shareshop`.`sha_location` ;

CREATE  TABLE IF NOT EXISTS `shareshop`.`sha_location` (
  `loc_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `loc_postcode` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`loc_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_article`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `shareshop`.`sha_article` ;

CREATE  TABLE IF NOT EXISTS `shareshop`.`sha_article` (
  `art_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `art_name` VARCHAR(255) NOT NULL ,
  `art_description` VARCHAR(1000) NOT NULL ,
  `art_image` BLOB NULL ,
  `art_locationId` INT(10) UNSIGNED NOT NULL ,
  `art_categoryId` INT(10) UNSIGNED NOT NULL ,
  `art_creationTimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`art_id`) ,
  INDEX `sha_fk_art_cat` (`art_categoryId` ASC) ,
  INDEX `sha_fk_art_loc` (`art_locationId` ASC) ,
  CONSTRAINT `sha_fk_art_cat`
    FOREIGN KEY (`art_categoryId` )
    REFERENCES `shareshop`.`sha_category` (`cat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sha_fk_art_loc`
    FOREIGN KEY (`art_locationId` )
    REFERENCES `shareshop`.`sha_location` (`loc_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
