SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `shareshop` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `shareshop` ;

-- -----------------------------------------------------
-- Table `shareshop`.`sha_categories`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `shareshop`.`sha_categories` ;

CREATE  TABLE IF NOT EXISTS `shareshop`.`sha_categories` (
  `cat_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `cat_name` VARCHAR(255) NOT NULL ,
  `cat_parent_id` INT(10) UNSIGNED NULL ,
  PRIMARY KEY (`cat_id`) ,
  INDEX `sha_cat_cat` (`cat_parent_id` ASC) ,
  CONSTRAINT `sha_cat_cat`
    FOREIGN KEY (`cat_parent_id` )
    REFERENCES `shareshop`.`sha_categories` (`cat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_locations`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `shareshop`.`sha_locations` ;

CREATE  TABLE IF NOT EXISTS `shareshop`.`sha_locations` (
  `loc_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `loc_postcode` VARCHAR(10) NOT NULL ,
  PRIMARY KEY (`loc_id`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `shareshop`.`sha_articles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `shareshop`.`sha_articles` ;

CREATE  TABLE IF NOT EXISTS `shareshop`.`sha_articles` (
  `art_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `art_name` VARCHAR(255) NOT NULL ,
  `art_description` VARCHAR(1000) NOT NULL ,
  `art_image` BLOB NULL ,
  `art_loc_id` INT(10) UNSIGNED NOT NULL ,
  `art_cat_id` INT(10) UNSIGNED NOT NULL ,
  `art_creation_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`art_id`) ,
  INDEX `sha_fk_art_cat` (`art_cat_id` ASC) ,
  INDEX `sha_fk_art_loc` (`art_loc_id` ASC) ,
  CONSTRAINT `sha_fk_art_cat`
    FOREIGN KEY (`art_cat_id` )
    REFERENCES `shareshop`.`sha_categories` (`cat_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `sha_fk_art_loc`
    FOREIGN KEY (`art_loc_id` )
    REFERENCES `shareshop`.`sha_locations` (`loc_id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
