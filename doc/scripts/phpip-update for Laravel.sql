-- Make actor table compatible with Laravel authentication
ALTER TABLE `phpip_laravel`.`actor` 
CHANGE COLUMN `password` `password` VARCHAR(60) NULL DEFAULT NULL;
ALTER TABLE `phpip_laravel`.`actor`
ADD COLUMN `remember_token` VARCHAR(100) NULL DEFAULT NULL AFTER `updater`;

-- Uncapitalizing all the ID fields
ALTER TABLE `phpip_laravel`.`actor` 
DROP FOREIGN KEY `fk_actor_company`,
DROP FOREIGN KEY `fk_actor_parent`,
DROP FOREIGN KEY `fk_actor_site`;
ALTER TABLE `phpip_laravel`.`actor` 
CHANGE COLUMN `ID` `id` INT(11) NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `parent_ID` `parent_id` INT(11) NULL DEFAULT NULL COMMENT 'Parent company of this company (another actor), where applicable. Useful for linking several companies owned by a same corporation' ,
CHANGE COLUMN `company_ID` `company_id` INT(11) NULL DEFAULT NULL COMMENT 'Mainly for inventors and contacts. ID of the actor\'s company or employer (another record in the actors table)' ,
CHANGE COLUMN `site_ID` `site_id` INT(11) NULL DEFAULT NULL COMMENT 'Mainly for inventors and contacts. ID of the actor\'s company site (another record in the actors table), if the company has several sites that we want to differentiate' ;
ALTER TABLE `phpip_laravel`.`actor` 
ADD CONSTRAINT `fk_actor_company`
  FOREIGN KEY (`company_id`)
  REFERENCES `phpip_laravel`.`actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_actor_parent`
  FOREIGN KEY (`parent_id`)
  REFERENCES `phpip_laravel`.`actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_actor_site`
  FOREIGN KEY (`site_id`)
  REFERENCES `phpip_laravel`.`actor` (`id`)
  ON UPDATE CASCADE;
  
ALTER TABLE `phpip_laravel`.`classifier` 
DROP FOREIGN KEY `fk_lnkmatter`,
DROP FOREIGN KEY `fk_matter`,
DROP FOREIGN KEY `fk_value`;
ALTER TABLE `phpip_laravel`.`classifier` 
CHANGE COLUMN `ID` `id` INT(11) NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `matter_ID` `matter_id` INT(11) NOT NULL ,
CHANGE COLUMN `value_ID` `value_id` INT(11) NULL DEFAULT NULL COMMENT 'Links to the classifier_values table if it has a link to classifier_types' ,
CHANGE COLUMN `lnk_matter_ID` `lnk_matter_id` INT(11) NULL DEFAULT NULL COMMENT 'Matter this case is linked to' ;
ALTER TABLE `phpip_laravel`.`classifier` 
ADD CONSTRAINT `fk_lnkmatter`
  FOREIGN KEY (`lnk_matter_id`)
  REFERENCES `phpip_laravel`.`matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_matter`
  FOREIGN KEY (`matter_id`)
  REFERENCES `phpip_laravel`.`matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_value`
  FOREIGN KEY (`value_id`)
  REFERENCES `phpip_laravel`.`classifier_value` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
  
ALTER TABLE `phpip_laravel`.`classifier_value` 
CHANGE COLUMN `ID` `id` INT(11) NOT NULL AUTO_INCREMENT ;

ALTER TABLE `phpip_laravel`.`default_actor` 
CHANGE COLUMN `ID` `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `phpip_laravel`.`event` 
DROP FOREIGN KEY `fk_event_altmatter`,
DROP FOREIGN KEY `fk_event_matter`;
ALTER TABLE `phpip_laravel`.`event` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `matter_ID` `matter_id` INT(11) NOT NULL ,
CHANGE COLUMN `alt_matter_ID` `alt_matter_id` INT(11) NULL DEFAULT NULL COMMENT 'Essentially for priority claims. ID of prior patent this event refers to' ;
ALTER TABLE `phpip_laravel`.`event` 
ADD CONSTRAINT `fk_event_altmatter`
  FOREIGN KEY (`alt_matter_id`)
  REFERENCES `phpip_laravel`.`matter` (`id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_event_matter`
  FOREIGN KEY (`matter_id`)
  REFERENCES `phpip_laravel`.`matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `phpip_laravel`.`matter` 
DROP FOREIGN KEY `fk_matter_container`,
DROP FOREIGN KEY `fk_matter_parent`;
ALTER TABLE `phpip_laravel`.`matter` 
CHANGE COLUMN `ID` `id` INT(11) NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `parent_ID` `parent_id` INT(11) NULL DEFAULT NULL COMMENT 'Link to parent patent. Used to create a hierarchy' ,
CHANGE COLUMN `container_ID` `container_id` INT(11) NULL DEFAULT NULL COMMENT 'Identifies the container matter from which this matter gathers its shared data. If null, this matter is a container' ;
ALTER TABLE `phpip_laravel`.`matter` 
ADD CONSTRAINT `fk_matter_container`
  FOREIGN KEY (`container_id`)
  REFERENCES `phpip_laravel`.`matter` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_matter_parent`
  FOREIGN KEY (`parent_id`)
  REFERENCES `phpip_laravel`.`matter` (`id`)
  ON UPDATE CASCADE;

ALTER TABLE `phpip_laravel`.`matter_actor_lnk` 
DROP FOREIGN KEY `fk_lnk_actor`,
DROP FOREIGN KEY `fk_lnk_company`,
DROP FOREIGN KEY `fk_lnk_matter`;
ALTER TABLE `phpip_laravel`.`matter_actor_lnk` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `matter_ID` `matter_id` INT(11) NOT NULL ,
CHANGE COLUMN `actor_ID` `actor_id` INT(11) NOT NULL ,
CHANGE COLUMN `company_ID` `company_id` INT(11) NULL DEFAULT NULL COMMENT 'A copy of the actor\'s company ID, if applicable, at the time the link was created.' ;
ALTER TABLE `phpip_laravel`.`matter_actor_lnk` 
ADD CONSTRAINT `fk_lnk_actor`
  FOREIGN KEY (`actor_id`)
  REFERENCES `phpip_laravel`.`actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_lnk_company`
  FOREIGN KEY (`company_id`)
  REFERENCES `phpip_laravel`.`actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_lnk_matter`
  FOREIGN KEY (`matter_id`)
  REFERENCES `phpip_laravel`.`matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `phpip_laravel`.`task` 
DROP FOREIGN KEY `fk_trigger_id`;
ALTER TABLE `phpip_laravel`.`task` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `trigger_ID` `trigger_id` INT(11) UNSIGNED NOT NULL COMMENT 'Link to generating event' ;
ALTER TABLE `phpip_laravel`.`task` 
ADD CONSTRAINT `fk_trigger_id`
  FOREIGN KEY (`trigger_id`)
  REFERENCES `phpip_laravel`.`event` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `phpip_laravel`.`task_rules` 
CHANGE COLUMN `ID` `id` INT(11) NOT NULL AUTO_INCREMENT ;

