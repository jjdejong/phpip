/* Run this script on an existing schema */

-- Make actor table compatible with Laravel authentication
ALTER TABLE `actor` 
CHANGE COLUMN `password` `password` VARCHAR(60) DEFAULT NULL;

ALTER TABLE `actor`
ADD COLUMN `remember_token` VARCHAR(100) DEFAULT NULL AFTER `updater`;

-- Adding virtual suffix column for creating unique human readable references (UIDs) and base the UID index on the suffix
ALTER TABLE matter
DROP KEY `UID`;

ALTER TABLE matter 
ADD COLUMN suffix VARCHAR(16) AS ( CONCAT_WS('', CONCAT_WS('-', CONCAT_WS('/', country, origin), type_code), idx) ) AFTER idx;

	-- This will throw a unique constraint error if the UIDs are not unique in the matter table. Check the error message and repair where necessary
ALTER TABLE matter 
ADD UNIQUE KEY `UID` (`category_code`, `caseref`, `suffix`);

	/*
 	 * Uncapitalize all the ID fields and set ID's to UNSIGNED
 	 */
-- First drop foreign keys that prevent ID modification
ALTER TABLE `actor` 
DROP FOREIGN KEY `fk_actor_company`,
DROP FOREIGN KEY `fk_actor_parent`,
DROP FOREIGN KEY `fk_actor_site`;

ALTER TABLE `classifier` 
DROP FOREIGN KEY `fk_lnkmatter`,
DROP FOREIGN KEY `fk_matter`,
DROP FOREIGN KEY `fk_value`;

ALTER TABLE `default_actor` 
DROP FOREIGN KEY `fk_dfltactor`,
DROP FOREIGN KEY `fk_dfltactor_client`;

ALTER TABLE `event` 
DROP FOREIGN KEY `fk_event_altmatter`,
DROP FOREIGN KEY `fk_event_matter`;

ALTER TABLE `matter` 
DROP FOREIGN KEY `fk_matter_container`,
DROP FOREIGN KEY `fk_matter_parent`;

ALTER TABLE `matter_actor_lnk` 
DROP FOREIGN KEY `fk_lnk_actor`,
DROP FOREIGN KEY `fk_lnk_company`,
DROP FOREIGN KEY `fk_lnk_matter`;

ALTER TABLE `task` 
DROP FOREIGN KEY `fk_trigger_id`,
DROP FOREIGN KEY `fk_task_rule`;

-- Change the ID's
ALTER TABLE `actor` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `parent_ID` `parent_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Parent company of this company (another actor), where applicable. Useful for linking several companies owned by a same corporation' ,
CHANGE COLUMN `company_ID` `company_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Mainly for inventors and contacts. ID of the actor\'s company or employer (another record in the actors table)' ,
CHANGE COLUMN `site_ID` `site_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Mainly for inventors and contacts. ID of the actor\'s company site (another record in the actors table), if the company has several sites that we want to differentiate' ;
  
ALTER TABLE `classifier` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `matter_ID` `matter_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `value_ID` `value_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Links to the classifier_values table if it has a link to classifier_types' ,
CHANGE COLUMN `lnk_matter_ID` `lnk_matter_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Matter this case is linked to' ;
  
ALTER TABLE `classifier_value` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ;

ALTER TABLE `default_actor` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
CHANGE COLUMN `actor_id` `actor_id` INT(11) UNSIGNED NOT NULL,
CHANGE COLUMN `for_client` `for_client` INT(11) UNSIGNED DEFAULT NULL ;

ALTER TABLE `event` 
CHANGE COLUMN `event_date` `event_date` date DEFAULT NULL,
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `matter_ID` `matter_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `alt_matter_ID` `alt_matter_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Essentially for priority claims. ID of prior patent this event refers to' ;

ALTER TABLE `matter` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `parent_ID` `parent_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Link to parent patent. Used to create a hierarchy' ,
CHANGE COLUMN `container_ID` `container_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'Identifies the container matter from which this matter gathers its shared data. If null, this matter is a container' ;

ALTER TABLE `matter_actor_lnk` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `matter_ID` `matter_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `actor_ID` `actor_id` INT(11) UNSIGNED NOT NULL ,
CHANGE COLUMN `company_ID` `company_id` INT(11) UNSIGNED DEFAULT NULL COMMENT 'A copy of the actor\'s company ID, if applicable, at the time the link was created.' ;

ALTER TABLE `task` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
CHANGE COLUMN `trigger_ID` `trigger_id` INT(11) UNSIGNED NOT NULL COMMENT 'Link to generating event',
CHANGE COLUMN `rule_used` `rule_used` INT(11) UNSIGNED DEFAULT NULL COMMENT 'ID of the rule that was used to set this task' ;

ALTER TABLE `task_rules` 
CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ;

-- Recreate foreign keys
ALTER TABLE `actor` 
ADD CONSTRAINT `fk_actor_company`
  FOREIGN KEY (`company_id`)
  REFERENCES `actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_actor_parent`
  FOREIGN KEY (`parent_id`)
  REFERENCES `actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_actor_site`
  FOREIGN KEY (`site_id`)
  REFERENCES `actor` (`id`)
  ON UPDATE CASCADE;
  
ALTER TABLE `classifier` 
ADD CONSTRAINT `fk_lnkmatter`
  FOREIGN KEY (`lnk_matter_id`)
  REFERENCES `matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_matter`
  FOREIGN KEY (`matter_id`)
  REFERENCES `matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_value`
  FOREIGN KEY (`value_id`)
  REFERENCES `classifier_value` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
  
ALTER TABLE `default_actor`
ADD CONSTRAINT `fk_dfltactor` 
  FOREIGN KEY (`actor_id`) 
  REFERENCES `actor` (`id`) 
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_dfltactor_client` 
  FOREIGN KEY (`for_client`) 
  REFERENCES `actor` (`id`) 
  ON DELETE CASCADE 
  ON UPDATE CASCADE;
  
ALTER TABLE `event` 
ADD CONSTRAINT `fk_event_altmatter`
  FOREIGN KEY (`alt_matter_id`)
  REFERENCES `matter` (`id`)
  ON DELETE SET NULL
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_event_matter`
  FOREIGN KEY (`matter_id`)
  REFERENCES `matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;
  
ALTER TABLE `matter` 
ADD CONSTRAINT `fk_matter_container`
  FOREIGN KEY (`container_id`)
  REFERENCES `matter` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_matter_parent`
  FOREIGN KEY (`parent_id`)
  REFERENCES `matter` (`id`)
  ON UPDATE CASCADE;
  
ALTER TABLE `matter_actor_lnk` 
ADD CONSTRAINT `fk_lnk_actor`
  FOREIGN KEY (`actor_id`)
  REFERENCES `actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_lnk_company`
  FOREIGN KEY (`company_id`)
  REFERENCES `actor` (`id`)
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_lnk_matter`
  FOREIGN KEY (`matter_id`)
  REFERENCES `matter` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `task` 
ADD CONSTRAINT `fk_trigger_id`
  FOREIGN KEY (`trigger_id`)
  REFERENCES `event` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
ADD CONSTRAINT `fk_task_rule` 
  FOREIGN KEY (`rule_used`) 
  REFERENCES `task_rules` (`id`) 
  ON DELETE SET NULL 
  ON UPDATE CASCADE;

