/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `actor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Family name or company name',
  `first_name` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'plus middle names, if required',
  `display_name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The name displayed in the interface, if not null',
  `login` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Database user login if not null.',
  `password` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_role` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link to actor_role table. A same actor can have different roles - this is the default role of the actor. CAUTION: for database users, this sets the user ACLs.',
  `function` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` int unsigned DEFAULT NULL COMMENT 'Parent company of this company (another actor), where applicable. Useful for linking several companies owned by a same corporation',
  `company_id` int unsigned DEFAULT NULL COMMENT 'Mainly for inventors and contacts. ID of the actor''s company or employer (another record in the actors table)',
  `site_id` int unsigned DEFAULT NULL COMMENT 'Mainly for inventors and contacts. ID of the actor''s company site (another record in the actors table), if the company has several sites that we want to differentiate',
  `phy_person` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Physical person or not',
  `nationality` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `small_entity` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Small entity status used in a few countries (FR, US)',
  `address` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Main address: street, zip and city',
  `country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Country in address',
  `address_mailing` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mailing address: street, zip and city',
  `country_mailing` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_billing` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Billing address: street, zip and city',
  `country_billing` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `legal_form` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registration_no` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warn` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'The actor will be displayed in red in the matter view when set',
  `ren_discount` double(8,2) NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `VAT_number` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uqdisplay_name` (`display_name`),
  UNIQUE KEY `uqlogin` (`login`),
  KEY `name` (`name`),
  KEY `default_role` (`default_role`),
  KEY `parent` (`parent_id`),
  KEY `company` (`company_id`),
  KEY `site` (`site_id`),
  KEY `nationality` (`nationality`),
  KEY `country` (`country`),
  KEY `country_mailing` (`country_mailing`),
  KEY `country_billing` (`country_billing`),
  CONSTRAINT `actor_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `actor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `actor_country_billing_foreign` FOREIGN KEY (`country_billing`) REFERENCES `country` (`iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `actor_country_foreign` FOREIGN KEY (`country`) REFERENCES `country` (`iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `actor_country_mailing_foreign` FOREIGN KEY (`country_mailing`) REFERENCES `country` (`iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `actor_default_role_foreign` FOREIGN KEY (`default_role`) REFERENCES `actor_role` (`code`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `actor_nationality_foreign` FOREIGN KEY (`nationality`) REFERENCES `country` (`iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `actor_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `actor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `actor_site_id_foreign` FOREIGN KEY (`site_id`) REFERENCES `actor` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `actor_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actor_role` (
  `code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_order` tinyint(1) DEFAULT '127' COMMENT 'Order of display in interface',
  `shareable` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates whether actors listed with this role are shareable for all matters of the same family',
  `show_ref` tinyint(1) NOT NULL DEFAULT '0',
  `show_company` tinyint(1) NOT NULL DEFAULT '0',
  `show_rate` tinyint(1) NOT NULL DEFAULT '0',
  `show_date` tinyint(1) NOT NULL DEFAULT '0',
  `notes` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `classifier`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classifier` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `matter_id` int unsigned NOT NULL,
  `type_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Link to ''classifier_types''',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT 'A free-text value used when classifier_values has no record linked to the classifier_types record',
  `img` mediumblob,
  `url` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Display value as a link to the URL defined here',
  `value_id` int unsigned DEFAULT NULL COMMENT 'Links to the classifier_values table if it has a link to classifier_types',
  `display_order` tinyint(1) NOT NULL DEFAULT '1',
  `lnk_matter_id` int unsigned DEFAULT NULL COMMENT 'Matter this case is linked to',
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uqlnk` (`matter_id`,`type_code`,`lnk_matter_id`),
  UNIQUE KEY `uqvalue_id` (`matter_id`,`type_code`,`value_id`),
  UNIQUE KEY `uqvalue` (`matter_id`,`type_code`,`value`(30)),
  KEY `value` (`value`(20)),
  KEY `type` (`type_code`),
  KEY `value_id` (`value_id`),
  KEY `lnk_matter` (`lnk_matter_id`),
  CONSTRAINT `classifier_lnk_matter_id_foreign` FOREIGN KEY (`lnk_matter_id`) REFERENCES `matter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `classifier_matter_id_foreign` FOREIGN KEY (`matter_id`) REFERENCES `matter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `classifier_type_code_foreign` FOREIGN KEY (`type_code`) REFERENCES `classifier_type` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `classifier_value_id_foreign` FOREIGN KEY (`value_id`) REFERENCES `classifier_value` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `classifier_before_insert` BEFORE INSERT ON `classifier` FOR EACH ROW BEGIN
    IF NEW.type_code = 'TITEN' THEN
  		SET NEW.value=tcase(NEW.value);
  	ELSEIF NEW.type_code IN ('TIT', 'TITOF', 'TITAL') THEN
  		SET NEW.value=CONCAT(UCASE(SUBSTR(NEW.value, 1, 1)),LCASE(SUBSTR(NEW.value FROM 2)));
  	END IF;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `classifier_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classifier_type` (
  `code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `main_display` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates whether to display as main information',
  `for_category` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'For showing in the pick-lists of only the selected category',
  `display_order` tinyint(1) DEFAULT '127',
  `notes` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  UNIQUE KEY `for_category` (`for_category`,`code`),
  CONSTRAINT `classifier_type_for_category_foreign` FOREIGN KEY (`for_category`) REFERENCES `matter_category` (`code`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `classifier_value`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classifier_value` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `value` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_code` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Restrict this classifier name to the classifier type identified here',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uqclvalue` (`value`,`type_code`),
  KEY `value_type` (`type_code`),
  CONSTRAINT `classifier_value_type_code_foreign` FOREIGN KEY (`type_code`) REFERENCES `classifier_type` (`code`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `country`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `country` (
  `numcode` smallint DEFAULT NULL,
  `iso` char(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `iso3` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_DE` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_FR` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ep` tinyint(1) DEFAULT '0' COMMENT 'Flag default countries for EP ratifications',
  `wo` tinyint(1) DEFAULT '0' COMMENT 'Flag default countries for PCT national phase',
  `em` tinyint(1) DEFAULT '0' COMMENT 'Flag default countries for EU trade mark',
  `oa` tinyint(1) DEFAULT '0' COMMENT 'Flag default countries for OA national phase',
  `renewal_first` tinyint DEFAULT '2' COMMENT 'The first year a renewal is due in this country from renewal_base. When negative, the date is calculated from renewal_start',
  `renewal_base` char(5) COLLATE utf8mb4_unicode_ci DEFAULT 'FIL' COMMENT 'The base event for calculating renewal deadlines',
  `renewal_start` char(5) COLLATE utf8mb4_unicode_ci DEFAULT 'FIL' COMMENT 'The event from which renewals become due',
  `checked_on` date DEFAULT NULL,
  PRIMARY KEY (`iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `default_actor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `default_actor` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `actor_id` int unsigned NOT NULL,
  `role` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `for_category` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_client` int unsigned DEFAULT NULL,
  `shared` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actor_id` (`actor_id`),
  KEY `role` (`role`),
  KEY `for_country` (`for_country`),
  KEY `for_client` (`for_client`),
  CONSTRAINT `default_actor_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `actor` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `default_actor_for_client_foreign` FOREIGN KEY (`for_client`) REFERENCES `actor` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `default_actor_for_country_foreign` FOREIGN KEY (`for_country`) REFERENCES `country` (`iso`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `default_actor_role_foreign` FOREIGN KEY (`role`) REFERENCES `actor_role` (`code`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Link to event_names table',
  `matter_id` int unsigned NOT NULL,
  `event_date` date DEFAULT NULL,
  `alt_matter_id` int unsigned DEFAULT NULL COMMENT 'Essentially for priority claims. ID of prior patent this event refers to',
  `detail` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numbers or short comments',
  `notes` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uqevent` (`matter_id`,`code`,`event_date`,`alt_matter_id`),
  KEY `code` (`code`),
  KEY `date` (`event_date`),
  KEY `alt_matter` (`alt_matter_id`),
  KEY `number` (`detail`),
  CONSTRAINT `event_alt_matter_id_foreign` FOREIGN KEY (`alt_matter_id`) REFERENCES `matter` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `event_code_foreign` FOREIGN KEY (`code`) REFERENCES `event_name` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `event_matter_id_foreign` FOREIGN KEY (`matter_id`) REFERENCES `matter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `event_before_insert` BEFORE INSERT ON `event` FOR EACH ROW BEGIN
    DECLARE vdate DATE DEFAULT NULL;
  	IF NEW.alt_matter_id IS NOT NULL THEN
  		IF EXISTS (SELECT 1 FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id AND event_date IS NOT NULL) THEN
  			SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
  			SET NEW.event_date = vdate;
  		ELSE
  			SET NEW.event_date = Now();
  		END IF;
  	END IF;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `event_after_insert` AFTER INSERT ON `event` FOR EACH ROW trig: BEGIN
	DECLARE DueDate, BaseDate, m_expiry DATE DEFAULT NULL;
	DECLARE tr_id, tr_days, tr_months, tr_years, m_pta, lnk_matter_id, CliAnnAgt, m_parent_id INT DEFAULT NULL;
	DECLARE tr_task, m_type_code, tr_currency, m_country, m_origin CHAR(5) DEFAULT NULL;
	DECLARE tr_detail, tr_responsible VARCHAR(160) DEFAULT NULL;
	DECLARE Done, tr_clear_task, tr_delete_task, tr_end_of_month, tr_recurring, tr_use_priority, m_dead BOOLEAN DEFAULT 0;
	DECLARE tr_cost, tr_fee DECIMAL(6,2) DEFAULT null;

	DECLARE cur_rule CURSOR FOR
		SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_priority, cost, fee, currency, task_rules.responsible
		FROM task_rules
		JOIN event_name ON event_name.code = task_rules.task
		JOIN matter ON matter.id = NEW.matter_id
		WHERE task_rules.active = 1
		AND task_rules.for_category = matter.category_code
		AND NEW.code = task_rules.trigger_event
		AND (Now() < use_before OR use_before IS null)
		AND (Now() > use_after OR use_after IS null)
		AND IF (task_rules.for_country IS NOT NULL,
			task_rules.for_country = matter.country,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category, tr.active) = (matter.country, matter.category_code, 1))
		)
		AND IF (task_rules.for_origin IS NOT NULL,
			task_rules.for_origin = matter.origin,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_origin, tr.for_category, tr.active) = (matter.origin, matter.category_code, 1))
		)
		AND IF (task_rules.for_type IS NOT NULL,
			task_rules.for_type = matter.type_code,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category, tr.active) = (matter.type_code, matter.category_code, 1))
		)
		AND NOT EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.abort_on)
		AND IF (task_rules.condition_event IS NULL, true, EXISTS (SELECT 1 FROM event WHERE event.matter_id = NEW.matter_id AND event.code = task_rules.condition_event));

	DECLARE cur_linked CURSOR FOR
		SELECT matter_id FROM event WHERE event.alt_matter_id = NEW.matter_id;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	SELECT type_code, dead, expire_date, term_adjust, country, origin, parent_id INTO m_type_code, m_dead, m_expiry, m_pta, m_country, m_origin, m_parent_id FROM matter WHERE matter.id = NEW.matter_id;
	SELECT id INTO CliAnnAgt FROM actor WHERE display_name = 'CLIENT';

	-- Do not change anything in dead cases
	IF (m_dead) THEN
		LEAVE trig;
	END IF;

	OPEN cur_rule;
		create_tasks: LOOP
			SET BaseDate = NEW.event_date;
			FETCH cur_rule INTO tr_id, tr_task, tr_clear_task, tr_delete_task, tr_detail, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority, tr_cost, tr_fee, tr_currency, tr_responsible;

			IF Done THEN
				LEAVE create_tasks;
			END IF;

			-- Skip renewal tasks if the client is the annuity agent
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role = 'ANN' AND lnk.actor_id = CliAnnAgt AND lnk.matter_id = NEW.matter_id) THEN
				ITERATE create_tasks;
			END IF;

			-- Skip recurring renewal tasks if country table has no correspondence
			IF tr_task = 'REN' AND tr_recurring = 1 AND NOT EXISTS (SELECT 1 FROM country WHERE iso = m_country and renewal_start = NEW.code) THEN
				ITERATE create_tasks;
			END IF;

			-- Use earliest priority date for task deadline calculation, if a PRI event exists
			IF tr_use_priority THEN
				SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = NEW.matter_id;
			END IF;

			-- Clear the task identified by the rule (do not create anything)
			IF tr_clear_task THEN
				UPDATE task JOIN event ON task.trigger_id = event.id
					SET task.done_date = NEW.event_date
					WHERE task.code = tr_task AND event.matter_id = NEW.matter_id AND task.done = 0;
				ITERATE create_tasks;
			END IF;

			-- Delete the task identified by the rule (do not create anything)
			IF tr_delete_task THEN
				DELETE FROM task
					WHERE task.code = tr_task AND task.trigger_id IN (SELECT event.id FROM event WHERE event.matter_id = NEW.matter_id);
				ITERATE create_tasks;
			END IF;

			-- Calculate the deadline
			SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
			IF tr_end_of_month THEN
				SET DueDate = LAST_DAY(DueDate);
			END IF;

			-- Deadline for renewals that have become due in a parent case when filing a divisional (EP 4-months rule)
			IF tr_task = 'REN' AND m_parent_id IS NOT NULL AND DueDate < NEW.event_date THEN
				SET DueDate = NEW.event_date + INTERVAL 4 MONTH;
			END IF;

			-- Don't create tasks having a deadline in the past, except for expiry and renewal
			-- Create renewals up to 6 months in the past in general, create renewals up to 19 months in the past for PCT national phases (captures direct PCT filing situations)
			IF (DueDate < Now() AND tr_task NOT IN ('EXP', 'REN'))
			OR (DueDate < (Now() - INTERVAL 6 MONTH) AND tr_task = 'REN' AND m_origin != 'WO')
			OR (DueDate < (Now() - INTERVAL 19 MONTH) AND tr_task = 'REN' AND m_origin = 'WO')
			THEN
				ITERATE create_tasks;
			END IF;

			IF tr_task = 'EXP' THEN
			-- Handle the expiry task (no task created)
				UPDATE matter SET expire_date = DueDate + INTERVAL m_pta DAY WHERE matter.id = NEW.matter_id;
			ELSEIF tr_recurring = 0 THEN
			-- Insert the new task if it is not a recurring one
				INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
				VALUES (NEW.id, tr_task, DueDate, tr_detail, tr_id, tr_cost, tr_fee, tr_currency, tr_responsible, NEW.creator, Now(), Now());
			ELSEIF tr_task = 'REN' THEN
			-- Recurring renewal is the only possibilty here, normally
				CALL insert_recurring_renewals(NEW.id, tr_id, BaseDate, tr_responsible, NEW.creator);
			END IF;
		END LOOP create_tasks;
	CLOSE cur_rule;
	SET Done = 0;

	-- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
	IF NEW.code = 'FIL' THEN
		OPEN cur_linked;
			recalc_linked: LOOP
				FETCH cur_linked INTO lnk_matter_id;
				IF Done THEN
					LEAVE recalc_linked;
				END IF;
				CALL recalculate_tasks(lnk_matter_id, 'FIL', NEW.creator);
			END LOOP recalc_linked;
		CLOSE cur_linked;
	END IF;

	-- Recalculate tasks based on the priority date upon inserting a new priority claim
	IF NEW.code = 'PRI' THEN
		CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.creator);
	END IF;

	-- Set matter to dead upon inserting a killer event
	SELECT killer INTO m_dead FROM event_name WHERE NEW.code = event_name.code;
	IF m_dead THEN
		UPDATE matter SET dead = 1 WHERE matter.id = NEW.matter_id;
	END IF;
END trig */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `event_before_update` BEFORE UPDATE ON `event` FOR EACH ROW BEGIN
  	DECLARE vdate DATE DEFAULT NULL;
  	-- Date taken from Filed event in linked matter
  	IF NEW.alt_matter_id IS NOT NULL THEN
  		SELECT event_date INTO vdate FROM event WHERE code='FIL' AND NEW.alt_matter_id=matter_id;
  		SET NEW.event_date = vdate;
  	END IF;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `event_after_update` AFTER UPDATE ON `event` FOR EACH ROW trig: BEGIN
  DECLARE DueDate, BaseDate DATE DEFAULT NULL;
  DECLARE t_id, tr_days, tr_months, tr_years, tr_recurring, m_pta, lnk_matter_id INT DEFAULT NULL;
  DECLARE Done, tr_end_of_month, tr_use_priority BOOLEAN DEFAULT 0;
  DECLARE m_category, m_country CHAR(5) DEFAULT NULL;

	DECLARE cur_rule CURSOR FOR
		SELECT task.id, days, months, years, recurring, end_of_month, use_priority
		FROM task_rules
		JOIN task ON task.rule_used = task_rules.id
		WHERE task.trigger_id = NEW.id;

  DECLARE cur_linked CURSOR FOR
  	SELECT matter_id FROM event WHERE alt_matter_id = NEW.matter_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	-- Leave if date hasn't changed
	IF (OLD.event_date = NEW.event_date AND NEW.alt_matter_id <=> OLD.alt_matter_id) THEN
		LEAVE trig;
	END IF;

  SET BaseDate = NEW.event_date;

  OPEN cur_rule;
  update_tasks: LOOP
  	FETCH cur_rule INTO t_id, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority;

  	IF Done THEN
  	  LEAVE update_tasks;
  	END IF;

		IF tr_recurring = 1 THEN
			-- Recurring tasks are not managed
			ITERATE update_tasks;
		END IF;

    -- Use earliest priority date for task deadline calculation, if a PRI event exists
  	IF tr_use_priority THEN
  	  SELECT CAST(IFNULL(min(event_date), NEW.event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = NEW.matter_id;
  	END IF;

    -- Calculate the deadline
  	SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
  	IF tr_end_of_month THEN
  	  SET DueDate = LAST_DAY(DueDate);
  	END IF;

		UPDATE task set due_date = DueDate, updated_at = Now(), updater = NEW.updater WHERE id = t_id;
  END LOOP update_tasks;
  CLOSE cur_rule;
  SET Done = 0;

  -- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
  IF NEW.code = 'FIL' THEN
  	OPEN cur_linked;
  	recalc_linked: LOOP
  	  FETCH cur_linked INTO lnk_matter_id;
  	  IF Done THEN
  		  LEAVE recalc_linked;
  	  END IF;
  	  CALL recalculate_tasks(lnk_matter_id, 'FIL', NEW.updater);
  	  CALL recalculate_tasks(lnk_matter_id, 'PRI', NEW.updater);
  	END LOOP recalc_linked;
  	CLOSE cur_linked;
  END IF;

  -- Recalculate tasks based on the priority date upon inserting a new priority claim
  IF NEW.code = 'PRI' THEN
  	CALL recalculate_tasks(NEW.matter_id, 'FIL', NEW.updater);
  END IF;

	-- Recalculate expiry
	IF NEW.code = 'FIL' THEN
  	SELECT category_code, term_adjust, country INTO m_category, m_pta, m_country FROM matter WHERE matter.id = NEW.matter_id;
  	SELECT months, years INTO tr_months, tr_years FROM task_rules
  		WHERE task = 'EXP'
  		AND for_category = m_category
			AND IF (for_country IS NOT NULL,
				for_country = m_country,
				concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (m_country, m_category))
			);
		SELECT IFNULL(min(event_date), NEW.event_date) INTO BaseDate FROM event_lnk_list WHERE code = 'FIL' AND matter_id = NEW.matter_id;
  	SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
  	UPDATE matter SET expire_date = DueDate WHERE matter.id = NEW.matter_id;
  END IF;
END trig */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `event_after_delete` AFTER DELETE ON `event` FOR EACH ROW BEGIN
  IF OLD.code IN ('PRI','PFIL') THEN
	 CALL recalculate_tasks(OLD.matter_id, 'FIL', OLD.updater);
  END IF;
  IF OLD.code='FIL' THEN
  	 UPDATE matter SET expire_date=NULL WHERE matter.id=OLD.matter_id;
  END IF;
  UPDATE matter
   JOIN event_name ON (OLD.code=event_name.code)
   SET matter.dead=0
   WHERE matter.id=OLD.matter_id
   AND NOT EXISTS (SELECT 1 FROM event JOIN event_name en ON (event.code=en.code) WHERE event.matter_id=OLD.matter_id AND en.killer=1);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `event_class_lnk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_class_lnk` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `event_name_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_class_id` int unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `event_class_lnk_template_class_id_foreign` (`template_class_id`),
  KEY `event_class_lnk_event_name_code_foreign` (`event_name_code`),
  CONSTRAINT `event_class_lnk_event_name_code_foreign` FOREIGN KEY (`event_name_code`) REFERENCES `event_name` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `event_class_lnk_template_class_id_foreign` FOREIGN KEY (`template_class_id`) REFERENCES `template_classes` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `event_lnk_list`;
/*!50001 DROP VIEW IF EXISTS `event_lnk_list`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `event_lnk_list` AS SELECT 
 1 AS `id`,
 1 AS `code`,
 1 AS `matter_id`,
 1 AS `event_date`,
 1 AS `detail`,
 1 AS `country`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `event_name`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_name` (
  `code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Category to which this event is specific',
  `country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Country to which the event is specific. If NULL, any country may use the event',
  `is_task` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates whether the event is a task',
  `status_event` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates whether the event should be displayed as a status',
  `default_responsible` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Login of the user who is systematically responsible for this type of task',
  `use_matter_resp` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Set if the matter responsible should also be set as responsible for the task. Overridden if default_responsible is set',
  `unique` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Only one such event can exist',
  `killer` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates whether this event kills the patent (set patent.dead to 1)',
  `notes` varchar(160) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `fk_responsible` (`default_responsible`),
  CONSTRAINT `event_name_default_responsible_foreign` FOREIGN KEY (`default_responsible`) REFERENCES `actor` (`login`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `ename_after_update` AFTER UPDATE ON `event_name` FOR EACH ROW BEGIN
	IF IFNULL(NEW.default_responsible,0) != IFNULL(OLD.default_responsible,0) THEN
		UPDATE task SET assigned_to=NEW.default_responsible
		WHERE code=NEW.code AND assigned_to <=> OLD.default_responsible;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `fees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `for_category` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PAT' COMMENT 'Category to which this rule applies.',
  `for_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Country where rule is applicable. If NULL, applies to all countries',
  `for_origin` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qt` int NOT NULL DEFAULT '0' COMMENT 'For which renewal',
  `use_before` date DEFAULT NULL COMMENT 'will be used only if the due date is before this date',
  `use_after` date DEFAULT NULL COMMENT 'will be used only if the due date is after this date',
  `cost` decimal(6,2) DEFAULT NULL,
  `fee` decimal(6,2) DEFAULT NULL,
  `cost_reduced` decimal(6,2) DEFAULT NULL,
  `fee_reduced` decimal(6,2) DEFAULT NULL,
  `cost_sup` decimal(6,2) DEFAULT NULL,
  `fee_sup` decimal(6,2) DEFAULT NULL,
  `cost_sup_reduced` decimal(6,2) DEFAULT NULL,
  `fee_sup_reduced` decimal(6,2) DEFAULT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT 'EUR',
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_category` (`for_category`),
  KEY `for_country` (`for_country`),
  KEY `for_origin` (`for_origin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `matter`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matter` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `caseref` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Case reference for the database user. The references for the other actors (client, agent, etc.) are in the actor link table.',
  `country` char(2) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Country where the matter is filed',
  `origin` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Code of the regional system the patent originates from (mainly EP or WO)',
  `type_code` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idx` tinyint(1) DEFAULT NULL COMMENT 'Increment this to differentiate multiple patents filed in the same country in the same family',
  `suffix` varchar(16) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (concat_ws(_utf8mb4'',concat_ws(_utf8mb4'-',concat_ws(_utf8mb4'/',`country`,`origin`),`type_code`),`idx`)) VIRTUAL,
  `uid` varchar(45) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (concat(`caseref`,`suffix`)) VIRTUAL,
  `parent_id` int unsigned DEFAULT NULL COMMENT 'Link to parent patent. Used to create a hierarchy',
  `container_id` int unsigned DEFAULT NULL COMMENT 'Identifies the container matter from which this matter gathers its shared data. If null, this matter is a container',
  `responsible` char(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Database user responsible for the patent',
  `dead` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates that the case is no longer supervised. Automatically set by "killer events" like "Abandoned"',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `expire_date` date DEFAULT NULL,
  `term_adjust` smallint NOT NULL DEFAULT '0' COMMENT 'Patent term adjustment in days. Essentially for US patents.',
  `alt_ref` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Alternate reference',
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User who created the record',
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User who last modified the record',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_uq` (`uid`),
  KEY `sort` (`caseref`,`container_id`,`origin`,`country`,`type_code`,`idx`),
  KEY `category` (`category_code`),
  KEY `country` (`country`),
  KEY `origin` (`origin`),
  KEY `type` (`type_code`),
  KEY `parent` (`parent_id`),
  KEY `container` (`container_id`),
  KEY `responsible` (`responsible`),
  KEY `matter_alt_ref_index` (`alt_ref`),
  CONSTRAINT `matter_category_code_foreign` FOREIGN KEY (`category_code`) REFERENCES `matter_category` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `matter_container_id_foreign` FOREIGN KEY (`container_id`) REFERENCES `matter` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `matter_country_foreign` FOREIGN KEY (`country`) REFERENCES `country` (`iso`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `matter_origin_foreign` FOREIGN KEY (`origin`) REFERENCES `country` (`iso`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `matter_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `matter` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `matter_responsible_foreign` FOREIGN KEY (`responsible`) REFERENCES `actor` (`login`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `matter_type_code_foreign` FOREIGN KEY (`type_code`) REFERENCES `matter_type` (`code`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `matter_after_insert` AFTER INSERT ON `matter` FOR EACH ROW BEGIN
	DECLARE vactorid, vshared INT DEFAULT NULL;
	DECLARE vrole CHAR(5) DEFAULT NULL;
	INSERT INTO event (code, matter_id, event_date, created_at, creator, updated_at) VALUES ('CRE', NEW.id, Now(), Now(), NEW.creator, Now());
	SELECT actor_id, role, shared INTO vactorid, vrole, vshared FROM default_actor
		WHERE for_client IS NULL
		AND (for_country = NEW.country OR (for_country IS null AND NOT EXISTS (SELECT 1 FROM default_actor da WHERE da.for_country = NEW.country AND for_category = NEW.category_code)))
		AND for_category = NEW.category_code;
	IF (vactorid is NOT NULL AND (vshared = 0 OR (vshared = 1 AND NEW.container_id IS NULL))) THEN
		INSERT INTO matter_actor_lnk (matter_id, actor_id, role, shared, created_at, creator, updated_at) VALUES (NEW.id, vactorid, vrole, vshared, Now(), 'system', Now());
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `matter_before_update` BEFORE UPDATE ON `matter` FOR EACH ROW BEGIN
  	IF NEW.term_adjust != OLD.term_adjust THEN
  		SET NEW.expire_date = OLD.expire_date + INTERVAL (NEW.term_adjust - OLD.term_adjust) DAY;
  	END IF;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `matter_after_update` AFTER UPDATE ON `matter` FOR EACH ROW BEGIN
	IF NEW.responsible != OLD.responsible THEN
		UPDATE task JOIN event ON (task.trigger_id = event.id AND event.matter_id = NEW.id) SET task.assigned_to = NEW.responsible, task.updated_at = Now(), task.updater = NEW.updater
		WHERE task.done = 0 AND task.assigned_to = OLD.responsible;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `matter_actor_lnk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matter_actor_lnk` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `matter_id` int unsigned NOT NULL,
  `actor_id` int unsigned NOT NULL,
  `display_order` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Order in which the actor should be displayed in a list of same type actors',
  `role` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shared` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Copied from the actor_role.shareable field. Indicates that this information, stored in the "container", is shared among members of the same family',
  `actor_ref` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Actor''s reference',
  `company_id` int unsigned DEFAULT NULL COMMENT 'A copy of the actor''s company ID, if applicable, at the time the link was created.',
  `rate` decimal(5,2) DEFAULT '100.00' COMMENT 'For co-owners - rate of ownership, or inventors',
  `date` date DEFAULT NULL COMMENT 'A date field that can, for instance, contain the date of ownership acquisition',
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uqactor_role` (`matter_id`,`role`,`actor_id`),
  KEY `actor_lnk` (`actor_id`),
  KEY `role_lnk` (`role`),
  KEY `actor_ref` (`actor_ref`),
  KEY `company_lnk` (`company_id`),
  CONSTRAINT `matter_actor_lnk_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `actor` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `matter_actor_lnk_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `actor` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `matter_actor_lnk_matter_id_foreign` FOREIGN KEY (`matter_id`) REFERENCES `matter` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `matter_actor_lnk_role_foreign` FOREIGN KEY (`role`) REFERENCES `actor_role` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `malnk_after_insert` AFTER INSERT ON `matter_actor_lnk` FOR EACH ROW BEGIN
          DECLARE vcli_ann_agt INT DEFAULT NULL;

          -- Delete renewal tasks when the special actor 'CLIENT' is set as the annuity agent
          IF NEW.role='ANN' THEN
            SELECT id INTO vcli_ann_agt FROM actor WHERE display_name='CLIENT';
            IF NEW.actor_id=vcli_ann_agt THEN
              DELETE task FROM event INNER JOIN task ON task.trigger_id=event.id
              WHERE task.code='REN' AND event.matter_id=NEW.matter_id;
            END IF;
          END IF;
        END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `matter_actor_lnk_AFTER_UPDATE` AFTER UPDATE ON `matter_actor_lnk` FOR EACH ROW BEGIN
  DECLARE vcli_ann_agt INT DEFAULT NULL;

  -- Delete renewal tasks when the special actor 'CLIENT' is set as the annuity agent
  IF NEW.role = 'ANN' THEN
  	SELECT id INTO vcli_ann_agt FROM actor WHERE display_name = 'CLIENT';
  	IF NEW.actor_id = vcli_ann_agt THEN
  	  DELETE task FROM event INNER JOIN task ON task.trigger_id = event.id
  	  WHERE task.code = 'REN' AND event.matter_id = NEW.matter_id;
  	END IF;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `matter_actors`;
/*!50001 DROP VIEW IF EXISTS `matter_actors`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `matter_actors` AS SELECT 
 1 AS `id`,
 1 AS `actor_id`,
 1 AS `display_name`,
 1 AS `name`,
 1 AS `first_name`,
 1 AS `email`,
 1 AS `display_order`,
 1 AS `role_code`,
 1 AS `role_name`,
 1 AS `shareable`,
 1 AS `show_ref`,
 1 AS `show_company`,
 1 AS `show_rate`,
 1 AS `show_date`,
 1 AS `matter_id`,
 1 AS `warn`,
 1 AS `actor_ref`,
 1 AS `date`,
 1 AS `rate`,
 1 AS `shared`,
 1 AS `company`,
 1 AS `inherited`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `matter_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matter_category` (
  `code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_prefix` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Used to build the case reference',
  `category` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_with` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PAT' COMMENT 'Display with the indicated category in the interface',
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`),
  KEY `display_with` (`display_with`),
  CONSTRAINT `matter_category_display_with_foreign` FOREIGN KEY (`display_with`) REFERENCES `matter_category` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `matter_classifiers`;
/*!50001 DROP VIEW IF EXISTS `matter_classifiers`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `matter_classifiers` AS SELECT 
 1 AS `id`,
 1 AS `matter_id`,
 1 AS `type_code`,
 1 AS `type_name`,
 1 AS `main_display`,
 1 AS `value`,
 1 AS `url`,
 1 AS `lnk_matter_id`,
 1 AS `display_order`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `matter_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matter_type` (
  `code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `renewal_list`;
/*!50001 DROP VIEW IF EXISTS `renewal_list`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `renewal_list` AS SELECT 
 1 AS `id`,
 1 AS `detail`,
 1 AS `due_date`,
 1 AS `done`,
 1 AS `done_date`,
 1 AS `matter_id`,
 1 AS `cost`,
 1 AS `fee`,
 1 AS `cost_reduced`,
 1 AS `fee_reduced`,
 1 AS `cost_sup`,
 1 AS `fee_sup`,
 1 AS `fee_sup_reduced`,
 1 AS `cost_sup_reduced`,
 1 AS `trigger_id`,
 1 AS `category`,
 1 AS `caseref`,
 1 AS `suffix`,
 1 AS `country`,
 1 AS `country_FR`,
 1 AS `origin`,
 1 AS `type_code`,
 1 AS `idx`,
 1 AS `sme_status`,
 1 AS `event_name`,
 1 AS `event_date`,
 1 AS `number`,
 1 AS `applicant_dn`,
 1 AS `client_dn`,
 1 AS `discount`,
 1 AS `client_id`,
 1 AS `email`,
 1 AS `responsible`,
 1 AS `title`,
 1 AS `pub_num`,
 1 AS `step`,
 1 AS `grace_period`,
 1 AS `invoice_step`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `renewals_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `renewals_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int unsigned NOT NULL,
  `job_id` int unsigned NOT NULL,
  `from_step` tinyint DEFAULT NULL COMMENT 'Step before the job',
  `to_step` tinyint DEFAULT NULL COMMENT 'Step after the job',
  `from_grace` tinyint DEFAULT NULL COMMENT 'Grace state before the job',
  `to_grace` tinyint DEFAULT NULL COMMENT 'Grace state after the job',
  `from_invoice` tinyint DEFAULT NULL COMMENT 'Invoice state before the job',
  `to_invoice` tinyint DEFAULT NULL COMMENT 'Invoice state after the job',
  `from_done` tinyint DEFAULT NULL COMMENT 'Done state before the job',
  `to_done` tinyint DEFAULT NULL COMMENT 'Done state after the job',
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `renewals_logs_task_id_index` (`task_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `trigger_id` int unsigned NOT NULL COMMENT 'Link to generating event',
  `code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Task code. Link to event_names table',
  `due_date` date NOT NULL,
  `assigned_to` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'User responsible for the task (if not the user responsible for the case)',
  `detail` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numbers or short comments',
  `done` tinyint(1) DEFAULT '0' COMMENT 'Set to 1 when task done',
  `done_date` date DEFAULT NULL COMMENT 'Optional task completion date',
  `rule_used` int unsigned DEFAULT NULL COMMENT 'ID of the rule that was used to set this task',
  `time_spent` time DEFAULT NULL COMMENT 'Time spent by attorney on task',
  `notes` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cost` decimal(6,2) DEFAULT NULL COMMENT 'The estimated or invoiced fee amount',
  `fee` decimal(6,2) DEFAULT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT 'EUR',
  `step` tinyint NOT NULL DEFAULT '0',
  `invoice_step` tinyint NOT NULL DEFAULT '0',
  `grace_period` tinyint NOT NULL DEFAULT '0',
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `trigger_id` (`trigger_id`),
  KEY `code` (`code`),
  KEY `due_date` (`due_date`),
  KEY `responsible` (`assigned_to`),
  KEY `detail` (`detail`),
  KEY `task_rule` (`rule_used`),
  CONSTRAINT `task_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `actor` (`login`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `task_code_foreign` FOREIGN KEY (`code`) REFERENCES `event_name` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `task_rule_used_foreign` FOREIGN KEY (`rule_used`) REFERENCES `task_rules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `task_trigger_id_foreign` FOREIGN KEY (`trigger_id`) REFERENCES `event` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `task_before_insert` BEFORE INSERT ON `task` FOR EACH ROW BEGIN
  	DECLARE vflag BOOLEAN;
  	DECLARE vresp CHAR(16);
  	SELECT use_matter_resp INTO vflag FROM event_name WHERE event_name.code=NEW.code;
  	SELECT responsible INTO vresp FROM matter, event WHERE event.id=NEW.trigger_id AND matter.id=event.matter_id;
  	IF NEW.assigned_to IS NULL THEN
  		IF vflag = 0 THEN
  			SET NEW.assigned_to = (SELECT default_responsible FROM event_name WHERE event_name.code=NEW.code);
  		ELSE
  			SET NEW.assigned_to = (SELECT ifnull(default_responsible, vresp) FROM event_name WHERE event_name.code=NEW.code);
  		END IF;
  	ELSEIF NEW.assigned_to = '0' THEN
  		SET NEW.assigned_to = vresp;
  	END IF;
  END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `task_before_update` BEFORE UPDATE ON `task` FOR EACH ROW BEGIN
  IF NEW.done_date IS NOT NULL AND OLD.done_date IS NULL AND OLD.done = 0 THEN
    SET NEW.done = 1;
  END IF;
  IF NEW.done_date IS NULL AND OLD.done_date IS NOT NULL AND OLD.done = 1 THEN
    SET NEW.done = 0;
  END IF;
  IF NEW.done = 1 AND OLD.done = 0 AND NEW.done_date IS NULL THEN
    SET NEW.done_date = Least(OLD.due_date, Now());
  END IF;
  IF NEW.done = 0 AND OLD.done = 1 AND OLD.done_date IS NOT NULL THEN
    SET NEW.done_date = NULL, NEW.step = 0, NEW.invoice_step = 0, NEW.grace_period = 0;
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `task_list`;
/*!50001 DROP VIEW IF EXISTS `task_list`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `task_list` AS SELECT 
 1 AS `id`,
 1 AS `code`,
 1 AS `name`,
 1 AS `detail`,
 1 AS `due_date`,
 1 AS `done`,
 1 AS `done_date`,
 1 AS `matter_id`,
 1 AS `cost`,
 1 AS `fee`,
 1 AS `trigger_id`,
 1 AS `category`,
 1 AS `caseref`,
 1 AS `country`,
 1 AS `origin`,
 1 AS `type_code`,
 1 AS `idx`,
 1 AS `responsible`,
 1 AS `delegate`,
 1 AS `rule_used`,
 1 AS `dead`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `task_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task_rules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indicates whether the rule should be used',
  `task` char(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Code of task that is created (or cleared)',
  `trigger_event` char(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Event that generates this task',
  `clear_task` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Identifies an open task in the matter that is cleared when this one is created',
  `delete_task` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Identifies a task type to be deleted from the matter when this one is created',
  `for_category` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PAT' COMMENT 'Category to which this rule applies.',
  `for_country` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Country where rule is applicable. If NULL, applies to all countries',
  `for_origin` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `for_type` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Type to which rule is applicable. If null, rule applies to all types',
  `detail` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Additional information on task',
  `days` int NOT NULL DEFAULT '0' COMMENT 'For task deadline calculation',
  `months` int NOT NULL DEFAULT '0' COMMENT 'For task deadline calculation',
  `years` int NOT NULL DEFAULT '0' COMMENT 'For task deadline calculation',
  `recurring` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'If non zero, indicates the recurring period in months. Mainly for annuities',
  `end_of_month` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'The deadline is at the end of the month. Mainly for annuities',
  `abort_on` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Task won''t be created if this event exists',
  `condition_event` char(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Task will only be created if this event exists',
  `use_priority` tinyint(1) NOT NULL DEFAULT '0',
  `use_before` date DEFAULT NULL COMMENT 'Task will be created only if the base event is before this date',
  `use_after` date DEFAULT NULL COMMENT 'Task will be created only if the base event is after this date',
  `cost` decimal(6,2) DEFAULT NULL,
  `fee` decimal(6,2) DEFAULT NULL,
  `currency` char(3) COLLATE utf8mb4_unicode_ci DEFAULT 'EUR',
  `responsible` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'The person (login) responsible for this task. If 0, insert the matter responsible.',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `creator` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updater` char(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `uid` varchar(32) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (md5(concat(`task`,`trigger_event`,`clear_task`,`delete_task`,`for_category`,ifnull(`for_country`,_utf8mb4'c'),ifnull(`for_origin`,_utf8mb4'o'),ifnull(`for_type`,_utf8mb4't'),`days`,`months`,`years`,`recurring`,ifnull(`abort_on`,_utf8mb4'a'),ifnull(`condition_event`,_utf8mb4'c'),`use_priority`,ifnull(`detail`,_utf8mb4'd')))) VIRTUAL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `task_rules_uid_unique` (`uid`),
  KEY `trigger_event` (`trigger_event`,`for_country`),
  KEY `task` (`task`),
  KEY `fk_category` (`for_category`),
  KEY `for_country` (`for_country`),
  KEY `for_origin` (`for_origin`),
  KEY `abort_on` (`abort_on`),
  KEY `condition` (`condition_event`),
  CONSTRAINT `task_rules_abort_on_foreign` FOREIGN KEY (`abort_on`) REFERENCES `event_name` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `task_rules_condition_event_foreign` FOREIGN KEY (`condition_event`) REFERENCES `event_name` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `task_rules_for_category_foreign` FOREIGN KEY (`for_category`) REFERENCES `matter_category` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `task_rules_for_country_foreign` FOREIGN KEY (`for_country`) REFERENCES `country` (`iso`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `task_rules_for_origin_foreign` FOREIGN KEY (`for_origin`) REFERENCES `country` (`iso`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `task_rules_task_foreign` FOREIGN KEY (`task`) REFERENCES `event_name` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `task_rules_trigger_event_foreign` FOREIGN KEY (`trigger_event`) REFERENCES `event_name` (`code`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`phpip`@`localhost`*/ /*!50003 TRIGGER `trules_after_update` AFTER UPDATE ON `task_rules` FOR EACH ROW BEGIN
	IF (NEW.fee != OLD.fee OR NEW.cost != OLD.cost) THEN
		UPDATE task SET fee=NEW.fee, cost=NEW.cost WHERE rule_used=NEW.id AND done=0;
	END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `template_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `template_classes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_role` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Role of actor who is the receiver of the document',
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updater` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_classes_name_unique` (`name`),
  KEY `template_classes_default_role_foreign` (`default_role`),
  CONSTRAINT `template_classes_default_role_foreign` FOREIGN KEY (`default_role`) REFERENCES `actor_role` (`code`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `template_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `template_members` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `class_id` int unsigned NOT NULL COMMENT 'The class which allow to link the template to an event or a task',
  `language` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Code of the language for the document',
  `style` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Help to distinguish documents in a same class. Free text',
  `category` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Help to classify documents. Free text',
  `format` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `summary` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The label of the document as displayed in lists',
  `subject` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'It can content fields to merge at creation time',
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'It can content fields to merge at creation time, and HTML tags when this format is chosen',
  `creator` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `updater` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_members_class_id_foreign` (`class_id`),
  CONSTRAINT `template_members_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `template_classes` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!50001 DROP VIEW IF EXISTS `users`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `users` AS SELECT 
 1 AS `id`,
 1 AS `name`,
 1 AS `login`,
 1 AS `password`,
 1 AS `default_role`,
 1 AS `company_id`,
 1 AS `email`,
 1 AS `phone`,
 1 AS `notes`,
 1 AS `creator`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `updater`,
 1 AS `remember_token`*/;
SET character_set_client = @saved_cs_client;
/*!50003 DROP FUNCTION IF EXISTS `actor_list` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` FUNCTION `actor_list`(mid INT, arole TEXT) RETURNS text CHARSET utf8mb3
BEGIN
          	DECLARE alist TEXT;
            SELECT GROUP_CONCAT(actor.name ORDER BY mal.display_order) INTO alist FROM matter_actor_lnk mal
            JOIN actor ON actor.ID = mal.actor_ID
            WHERE mal.matter_ID = mid AND mal.role = arole;
            RETURN alist;
          END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `lowerword` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` FUNCTION `lowerword`( str TEXT, word VARCHAR(5) ) RETURNS text CHARSET utf8mb3
BEGIN
            DECLARE i INT DEFAULT 1;
            DECLARE loc INT;
            SET loc = LOCATE(CONCAT(word,' '), str, 2);
            IF loc > 1 THEN
              WHILE i <= LENGTH (str) AND loc <> 0 DO
                SET str = INSERT(str,loc,LENGTH(word),LCASE(word));
                SET i = loc+LENGTH(word);
                SET loc = LOCATE(CONCAT(word,' '), str, i);
              END WHILE;
            END IF;
            RETURN str;
          END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `matter_status` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` FUNCTION `matter_status`(mid INT) RETURNS text CHARSET utf8mb3
BEGIN
          	DECLARE mstatus TEXT;
            SELECT CONCAT_WS(': ', event_name.name, status.event_date) INTO mstatus FROM `event` status
          	JOIN event_name ON mid=status.matter_ID AND event_name.code=status.code AND event_name.status_event=1
          	LEFT JOIN (event e2, event_name en2) ON e2.code=en2.code AND en2.status_event=1 AND mid=e2.matter_id AND status.event_date < e2.event_date
          	WHERE e2.matter_id IS NULL;
          	RETURN mstatus;
          END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `tcase` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` FUNCTION `tcase`( str TEXT) RETURNS text CHARSET utf8mb3
BEGIN
            DECLARE c CHAR(1);
            DECLARE s TEXT;
            DECLARE i INT DEFAULT 1;
            DECLARE bool INT DEFAULT 1;
            DECLARE punct CHAR(17) DEFAULT ' ()[]{},.-_!@;:?/';
            SET s = LCASE( str );
            WHILE i <= LENGTH( str ) DO
            	SET c = SUBSTRING( s, i, 1 );
            	IF LOCATE( c, punct ) > 0 THEN
            		SET bool = 1;
            	ELSEIF bool=1 THEN
                IF c >= 'a' AND c <= 'z' THEN
                  SET s = CONCAT(LEFT(s,i-1),UCASE(c),SUBSTRING(s,i+1));
                  SET bool = 0;
                ELSEIF c >= '0' AND c <= '9' THEN
                  SET bool = 0;
                END IF;
            	END IF;
            	SET i = i+1;
            END WHILE;
            SET s = lowerword(s, 'A');
            SET s = lowerword(s, 'An');
            SET s = lowerword(s, 'And');
            SET s = lowerword(s, 'As');
            SET s = lowerword(s, 'At');
            SET s = lowerword(s, 'But');
            SET s = lowerword(s, 'By');
            SET s = lowerword(s, 'For');
            SET s = lowerword(s, 'If');
            SET s = lowerword(s, 'In');
            SET s = lowerword(s, 'Of');
            SET s = lowerword(s, 'On');
            SET s = lowerword(s, 'Or');
            SET s = lowerword(s, 'The');
            SET s = lowerword(s, 'To');
            SET s = lowerword(s, 'Via');
            RETURN s;
          END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `insert_recurring_renewals` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` PROCEDURE `insert_recurring_renewals`(
  	IN P_trigger_id INT,
  	IN P_rule_id INT,
  	IN P_base_date DATE,
  	IN P_responsible CHAR(16),
  	IN P_user CHAR(16)
  )
proc: BEGIN
  	DECLARE FirstRenewal, RYear INT;
  	DECLARE BaseDate, StartDate, DueDate, ExpiryDate DATE DEFAULT NULL;
  	DECLARE Origin CHAR(2) DEFAULT NULL;

  	SELECT ebase.event_date, estart.event_date, country.renewal_first, matter.expire_date, matter.origin INTO BaseDate, StartDate, FirstRenewal, ExpiryDate, Origin
  		FROM country
  		JOIN matter ON country.iso = matter.country
  		JOIN event estart ON estart.matter_id = matter.id AND estart.id = P_trigger_id
  		JOIN event ebase ON ebase.matter_id = matter.id
  		WHERE country.renewal_start = estart.code
  		AND country.renewal_base = ebase.code;

  	-- Leave if the country has no parameters (country dealt with specifically in task_rules)
  	IF StartDate IS NULL THEN
  		LEAVE proc;
  	END IF;
  	SET BaseDate = LEAST(BaseDate, P_base_date);
  	SET RYear = ABS(FirstRenewal);
  	renloop: WHILE RYear <= 20 DO
      IF (FirstRenewal > 0) THEN
  		  SET DueDate = BaseDate + INTERVAL RYear - 1 YEAR;
      ELSE
        SET DueDate = StartDate + INTERVAL RYear - 1 YEAR;
      END IF;
  		IF DueDate > ExpiryDate THEN
  			LEAVE proc;
  		END IF;
  		IF DueDate < StartDate THEN
  			SET DueDate = StartDate;
  		END IF;
  		-- Ignore renewals in the past beyond the 6-months grace period unless PCT national phase
  		IF (DueDate < Now() - INTERVAL 6 MONTH AND Origin != 'WO') OR (DueDate < (Now() - INTERVAL 19 MONTH) AND Origin = 'WO') THEN
  			SET RYear = RYear + 1;
  			ITERATE renloop;
  		END IF;
  		INSERT INTO task (trigger_id, code, due_date, detail, rule_used, assigned_to, creator, created_at, updated_at)
  		VALUES (P_trigger_id, 'REN', DueDate, RYear, P_rule_id, P_responsible, P_user, Now(), Now());
  		SET RYear = RYear + 1;
  	END WHILE;
  END proc ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recalculate_tasks` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` PROCEDURE `recalculate_tasks`(
			IN P_matter_id INT,
			IN P_event_code CHAR(5),
			IN P_user CHAR(16)
		)
proc: BEGIN
	DECLARE e_event_date, DueDate, BaseDate DATE DEFAULT NULL;
	DECLARE t_id, e_id, tr_days, tr_months, tr_years, tr_recurring, m_pta INT DEFAULT NULL;
	DECLARE Done, tr_end_of_month, tr_use_priority BOOLEAN DEFAULT 0;
	DECLARE m_category, m_country, m_type CHAR(5) DEFAULT NULL;

	DECLARE cur_rule CURSOR FOR
		SELECT task.id, days, months, years, recurring, end_of_month, use_priority
		FROM task_rules
		JOIN task ON task.rule_used = task_rules.id
		WHERE task.trigger_id = e_id;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	SELECT id, event_date INTO e_id, e_event_date FROM event_lnk_list WHERE matter_id = P_matter_id AND code = P_event_code ORDER BY event_date LIMIT 1;
	
	IF e_id IS NULL THEN
		LEAVE proc;
	END IF;

	OPEN cur_rule;
	update_tasks: LOOP
		FETCH cur_rule INTO t_id, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority;

		IF Done THEN
			LEAVE update_tasks;
		END IF;

		IF tr_recurring = 1 THEN
			ITERATE update_tasks;
		END IF;

        SET BaseDate = e_event_date;

		IF tr_use_priority THEN
			SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = P_matter_id;
		END IF;
		
		SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
		IF tr_end_of_month THEN
			SET DueDate = LAST_DAY(DueDate);
		END IF;

		UPDATE task SET due_date = DueDate, updated_at = Now(), updater = P_user WHERE task.id = t_id;
	END LOOP update_tasks;
	CLOSE cur_rule;

	IF P_event_code = 'FIL' THEN
		SELECT category_code, term_adjust, country, type_code INTO m_category, m_pta, m_country, m_type FROM matter WHERE matter.id = P_matter_id;
		SELECT months, years INTO tr_months, tr_years FROM task_rules
			WHERE task = 'EXP'
			AND for_category = m_category
			AND IF (for_country IS NOT NULL AND for_type IS NULL,
				for_country = m_country,
				concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category) = (m_country, m_category))
			) AND IF (for_type IS NOT NULL AND for_country IS NULL,
				for_type = m_type,
				concat(task, trigger_event) NOT IN (SELECT concat(task, trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category) = (m_type, m_category))
			);
		SET DueDate = BaseDate + INTERVAL m_pta DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
		UPDATE matter SET expire_date = DueDate WHERE matter.id = P_matter_id;
	END IF;
END proc ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `recreate_tasks` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` PROCEDURE `recreate_tasks`(
			IN P_trigger_id INT,
			IN P_user CHAR(16)
		)
proc: BEGIN
	DECLARE e_event_date, DueDate, BaseDate, m_expiry DATE DEFAULT NULL;
	DECLARE e_matter_id, tr_id, tr_days, tr_months, tr_years, m_pta, lnk_matter_id, CliAnnAgt, m_parent_id INT DEFAULT NULL;
	DECLARE e_code, tr_task, m_country, m_type_code, tr_currency, m_origin CHAR(5) DEFAULT NULL;
	DECLARE tr_detail, tr_responsible VARCHAR(160) DEFAULT NULL;
	DECLARE Done, tr_clear_task, tr_delete_task, tr_end_of_month, tr_recurring, tr_use_priority, m_dead BOOLEAN DEFAULT 0;
	DECLARE tr_cost, tr_fee DECIMAL(6,2) DEFAULT null;

	DECLARE cur_rule CURSOR FOR
		SELECT task_rules.id, task, clear_task, delete_task, detail, days, months, years, recurring, end_of_month, use_priority, cost, fee, currency, task_rules.responsible
		FROM task_rules
		JOIN event_name ON event_name.code = task_rules.task
		JOIN matter ON matter.id = e_matter_id
		WHERE task_rules.active = 1
		AND task_rules.for_category = matter.category_code
		AND e_code = task_rules.trigger_event
		AND (Now() < use_before OR use_before IS null)
		AND (Now() > use_after OR use_after IS null)
		AND IF (task_rules.for_country IS NOT NULL,
			task_rules.for_country = matter.country,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_country, tr.for_category, tr.active) = (matter.country, matter.category_code, 1))
		)
		AND IF (task_rules.for_origin IS NOT NULL,
			task_rules.for_origin = matter.origin,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_origin, tr.for_category, tr.active) = (matter.origin, matter.category_code, 1))
		)
		AND IF (task_rules.for_type IS NOT NULL,
			task_rules.for_type = matter.type_code,
			concat(task_rules.task, task_rules.trigger_event) NOT IN (SELECT concat(tr.task, tr.trigger_event) FROM task_rules tr WHERE (tr.for_type, tr.for_category, tr.active) = (matter.type_code, matter.category_code, 1))
		)
		AND NOT EXISTS (SELECT 1 FROM event WHERE event.matter_id = e_matter_id AND event.code = task_rules.abort_on)
		AND IF (task_rules.condition_event IS NULL, true, EXISTS (SELECT 1 FROM event WHERE matter_id = e_matter_id AND event.code = task_rules.condition_event));

	DECLARE cur_linked CURSOR FOR
		SELECT matter_id FROM event WHERE event.alt_matter_id = e_matter_id;

  DECLARE CONTINUE HANDLER FOR NOT FOUND SET Done = 1;

	-- Delete the tasks attached to the event by an existing rule
	DELETE FROM task WHERE rule_used IS NOT NULL AND trigger_id = P_trigger_id;

  SELECT e.matter_id, e.event_date, e.code, m.country, m.type_code, m.dead, m.expire_date, m.term_adjust, m.origin, m.parent_id INTO e_matter_id, e_event_date, e_code, m_country, m_type_code, m_dead, m_expiry, m_pta, m_origin, m_parent_id
	FROM event e
	JOIN matter m ON m.id = e.matter_id
	WHERE e.id = P_trigger_id;

  SELECT id INTO CliAnnAgt FROM actor WHERE display_name = 'CLIENT';

	-- Do not change anything in dead cases
	IF (m_dead OR Now() > m_expiry) THEN
    LEAVE proc;
  END IF;

	OPEN cur_rule;
	  create_tasks: LOOP
			SET BaseDate = e_event_date;
	    FETCH cur_rule INTO tr_id, tr_task, tr_clear_task, tr_delete_task, tr_detail, tr_days, tr_months, tr_years, tr_recurring, tr_end_of_month, tr_use_priority, tr_cost, tr_fee, tr_currency, tr_responsible;

			IF Done THEN
	      LEAVE create_tasks;
	    END IF;

			-- Skip renewal tasks if the client is the annuity agent
			IF tr_task = 'REN' AND EXISTS (SELECT 1 FROM matter_actor_lnk lnk WHERE lnk.role = 'ANN' AND lnk.actor_id = CliAnnAgt AND lnk.matter_id = e_matter_id) THEN
				ITERATE create_tasks;
			END IF;

			-- Skip recurring renewal tasks if country table has no correspondence
			IF tr_task = 'REN' AND tr_recurring = 1 AND NOT EXISTS (SELECT 1 FROM country WHERE iso = m_country and renewal_start = e_code) THEN
				ITERATE create_tasks;
			END IF;

			-- Use earliest priority date for task deadline calculation, if a PRI event exists
			IF tr_use_priority THEN
				SELECT CAST(IFNULL(min(event_date), e_event_date) AS DATE) INTO BaseDate FROM event_lnk_list WHERE code = 'PRI' AND matter_id = e_matter_id;
			END IF;

			-- Clear the task identified by the rule (do not create anything)
	    IF tr_clear_task THEN
	      UPDATE task JOIN event ON task.trigger_id = event.id
					SET task.done_date = e_event_date
					WHERE task.code = tr_task AND event.matter_id = e_matter_id AND task.done = 0;
	      ITERATE create_tasks;
	    END IF;

			-- Delete the task identified by the rule (do not create anything)
	    IF tr_delete_task THEN
				DELETE FROM task
					WHERE task.code = tr_task AND task.trigger_id IN (SELECT event.id FROM event WHERE event.matter_id = e_matter_id);
				ITERATE create_tasks;
	    END IF;

			-- Calculate the deadline
			SET DueDate = BaseDate + INTERVAL tr_days DAY + INTERVAL tr_months MONTH + INTERVAL tr_years YEAR;
	    IF tr_end_of_month THEN
	      SET DueDate = LAST_DAY(DueDate);
	    END IF;


			-- Deadline for renewals that have become due in a parent case when filing a divisional (EP 4-months rule)
			IF tr_task = 'REN' AND m_parent_id IS NOT NULL AND DueDate < e_event_date THEN
				SET DueDate = e_event_date + INTERVAL 4 MONTH;
			END IF;

			-- Don't create tasks having a deadline in the past, except for expiry and renewal
      -- Create renewals up to 6 months in the past in general, create renewals up to 19 months in the past for PCT national phases (captures direct PCT filing situations)
			IF (DueDate < Now() AND tr_task NOT IN ('EXP', 'REN'))
			OR (DueDate < (Now() - INTERVAL 6 MONTH) AND tr_task = 'REN' AND m_origin != 'WO')
			OR (DueDate < (Now() - INTERVAL 19 MONTH) AND tr_task = 'REN' AND m_origin = 'WO')
			THEN
	      ITERATE create_tasks;
	    END IF;

	    IF tr_task = 'EXP' THEN
			-- Handle the expiry task (no task created)
				UPDATE matter SET expire_date = DueDate + INTERVAL m_pta DAY WHERE matter.id = e_matter_id;
			ELSEIF tr_recurring = 0 THEN
			-- Insert the new task if it is not a recurring one
				INSERT INTO task (trigger_id, code, due_date, detail, rule_used, cost, fee, currency, assigned_to, creator, created_at, updated_at)
		    VALUES (P_trigger_id, tr_task, DueDate, tr_detail, tr_id, tr_cost, tr_fee, tr_currency, tr_responsible, P_user, Now(), Now());
			ELSEIF tr_task = 'REN' THEN
			-- Recurring renewal is the only possibilty here, normally
				CALL insert_recurring_renewals(P_trigger_id, tr_id, BaseDate, tr_responsible, P_user);
			END IF;
	  END LOOP create_tasks;
  CLOSE cur_rule;
  SET done = 0;

	-- If the event is a filing, update the tasks of the matters linked by event (typically the tasks based on the priority date)
	IF e_code = 'FIL' THEN
		OPEN cur_linked;
			recalc_linked: LOOP
				FETCH cur_linked INTO lnk_matter_id;
				IF Done THEN
					LEAVE recalc_linked;
				END IF;
				CALL recalculate_tasks(lnk_matter_id, 'FIL', P_user);
			END LOOP recalc_linked;
		CLOSE cur_linked;
  END IF;

	-- Recalculate tasks based on the priority date upon inserting a new priority claim
  IF e_code = 'PRI' THEN
    CALL recalculate_tasks(e_matter_id, 'FIL', P_user);
  END IF;

	-- Set matter to dead upon inserting a killer event
	SELECT killer INTO m_dead FROM event_name WHERE e_code = event_name.code;
  IF m_dead THEN
    UPDATE matter SET dead = 1 WHERE matter.id = e_matter_id;
  END IF;
END proc ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `update_expired` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`phpip`@`localhost` PROCEDURE `update_expired`()
BEGIN
  	DECLARE vmatter_id INTEGER;
      DECLARE vexpire_date DATE;
      DECLARE done INT DEFAULT FALSE;
      DECLARE cur_expired CURSOR FOR
  		SELECT matter.id, matter.expire_date FROM matter WHERE expire_date < Now() AND dead=0;
  	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

      OPEN cur_expired;

      read_loop: LOOP
  		FETCH cur_expired INTO vmatter_id, vexpire_date;
          IF done THEN
  			LEAVE read_loop;
  		END IF;
  		INSERT IGNORE INTO `event` (code, matter_id, event_date, created_at, creator, updated_at) VALUES ('EXP', vmatter_id, vexpire_date, Now(), 'system', Now());
  	END LOOP;
  END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50001 DROP VIEW IF EXISTS `event_lnk_list`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`phpip`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `event_lnk_list` AS select `event`.`id` AS `id`,`event`.`code` AS `code`,`event`.`matter_id` AS `matter_id`,if((`event`.`alt_matter_id` is null),`event`.`event_date`,`lnk`.`event_date`) AS `event_date`,if((`event`.`alt_matter_id` is null),`event`.`detail`,`lnk`.`detail`) AS `detail`,`matter`.`country` AS `country` from ((`event` left join `event` `lnk` on(((`event`.`alt_matter_id` = `lnk`.`matter_id`) and (`lnk`.`code` = 'FIL')))) left join `matter` on((`event`.`alt_matter_id` = `matter`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `matter_actors`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`phpip`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `matter_actors` AS select `pivot`.`id` AS `id`,`actor`.`id` AS `actor_id`,ifnull(`actor`.`display_name`,`actor`.`name`) AS `display_name`,`actor`.`name` AS `name`,`actor`.`first_name` AS `first_name`,`actor`.`email` AS `email`,`pivot`.`display_order` AS `display_order`,`pivot`.`role` AS `role_code`,`actor_role`.`name` AS `role_name`,`actor_role`.`shareable` AS `shareable`,`actor_role`.`show_ref` AS `show_ref`,`actor_role`.`show_company` AS `show_company`,`actor_role`.`show_rate` AS `show_rate`,`actor_role`.`show_date` AS `show_date`,`matter`.`id` AS `matter_id`,`actor`.`warn` AS `warn`,`pivot`.`actor_ref` AS `actor_ref`,`pivot`.`date` AS `date`,`pivot`.`rate` AS `rate`,`pivot`.`shared` AS `shared`,`co`.`name` AS `company`,if((`pivot`.`matter_id` = `matter`.`container_id`),1,0) AS `inherited` from ((((`matter_actor_lnk` `pivot` join `matter` on(((`pivot`.`matter_id` = `matter`.`id`) or ((`pivot`.`shared` = 1) and (`pivot`.`matter_id` = `matter`.`container_id`))))) join `actor` on((`pivot`.`actor_id` = `actor`.`id`))) left join `actor` `co` on((`co`.`id` = `pivot`.`company_id`))) join `actor_role` on((`pivot`.`role` = `actor_role`.`code`))) order by `actor_role`.`display_order`,`pivot`.`display_order` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `matter_classifiers`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`phpip`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `matter_classifiers` AS select `classifier`.`id` AS `id`,`matter`.`id` AS `matter_id`,`classifier`.`type_code` AS `type_code`,`classifier_type`.`type` AS `type_name`,`classifier_type`.`main_display` AS `main_display`,if((`classifier`.`value_id` is null),`classifier`.`value`,`classifier_value`.`value`) AS `value`,`classifier`.`url` AS `url`,`classifier`.`lnk_matter_id` AS `lnk_matter_id`,`classifier`.`display_order` AS `display_order` from (((`classifier` join `classifier_type` on((`classifier`.`type_code` = `classifier_type`.`code`))) join `matter` on((ifnull(`matter`.`container_id`,`matter`.`id`) = `classifier`.`matter_id`))) left join `classifier_value` on((`classifier_value`.`id` = `classifier`.`value_id`))) order by `classifier_type`.`display_order`,`classifier`.`display_order` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `renewal_list`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`phpip`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `renewal_list` AS select `task`.`id` AS `id`,`task`.`detail` AS `detail`,`task`.`due_date` AS `due_date`,`task`.`done` AS `done`,`task`.`done_date` AS `done_date`,`event`.`matter_id` AS `matter_id`,`fees`.`cost` AS `cost`,`fees`.`fee` AS `fee`,`fees`.`cost_reduced` AS `cost_reduced`,`fees`.`fee_reduced` AS `fee_reduced`,`fees`.`cost_sup` AS `cost_sup`,`fees`.`fee_sup` AS `fee_sup`,`fees`.`fee_sup_reduced` AS `fee_sup_reduced`,`fees`.`cost_sup_reduced` AS `cost_sup_reduced`,`task`.`trigger_id` AS `trigger_id`,`matter`.`category_code` AS `category`,`matter`.`caseref` AS `caseref`,`matter`.`suffix` AS `suffix`,`matter`.`country` AS `country`,`mcountry`.`name_FR` AS `country_FR`,`matter`.`origin` AS `origin`,`matter`.`type_code` AS `type_code`,`matter`.`idx` AS `idx`,(select 1 from `classifier` `sme` where ((`matter`.`id` = `sme`.`matter_id`) and (`sme`.`type_code` = 'SME'))) AS `sme_status`,`event`.`code` AS `event_name`,`event`.`event_date` AS `event_date`,`event`.`detail` AS `number`,group_concat(`pa_app`.`display_name` separator ',') AS `applicant_dn`,`pa_cli`.`display_name` AS `client_dn`,`pa_cli`.`ren_discount` AS `discount`,`pmal_cli`.`actor_id` AS `client_id`,`pa_cli`.`email` AS `email`,ifnull(`task`.`assigned_to`,`matter`.`responsible`) AS `responsible`,`cla`.`value` AS `title`,`ev`.`detail` AS `pub_num`,`task`.`step` AS `step`,`task`.`grace_period` AS `grace_period`,`task`.`invoice_step` AS `invoice_step` from ((((((((((`matter` left join `matter_actor_lnk` `pmal_app` on(((ifnull(`matter`.`container_id`,`matter`.`id`) = `pmal_app`.`matter_id`) and (`pmal_app`.`role` = 'APP')))) left join `actor` `pa_app` on((`pa_app`.`id` = `pmal_app`.`actor_id`))) left join `matter_actor_lnk` `pmal_cli` on(((ifnull(`matter`.`container_id`,`matter`.`id`) = `pmal_cli`.`matter_id`) and (`pmal_cli`.`role` = 'CLI')))) left join `country` `mcountry` on((`mcountry`.`iso` = `matter`.`country`))) left join `actor` `pa_cli` on((`pa_cli`.`id` = `pmal_cli`.`actor_id`))) join `event` on((`matter`.`id` = `event`.`matter_id`))) left join `event` `ev` on(((`matter`.`id` = `ev`.`matter_id`) and (`ev`.`code` = 'PUB')))) join `task` on((`task`.`trigger_id` = `event`.`id`))) left join `classifier` `cla` on(((ifnull(`matter`.`container_id`,`matter`.`id`) = `cla`.`matter_id`) and (`cla`.`type_code` = 'TITOF')))) left join `fees` on(((`fees`.`for_country` = `matter`.`country`) and (`fees`.`for_category` = `matter`.`category_code`) and (`fees`.`qt` = `task`.`detail`)))) where ((`task`.`code` = 'REN') and (`matter`.`dead` = 0)) group by `task`.`id` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `task_list`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`phpip`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `task_list` AS select `task`.`id` AS `id`,`task`.`code` AS `code`,`event_name`.`name` AS `name`,`task`.`detail` AS `detail`,`task`.`due_date` AS `due_date`,`task`.`done` AS `done`,`task`.`done_date` AS `done_date`,`event`.`matter_id` AS `matter_id`,`task`.`cost` AS `cost`,`task`.`fee` AS `fee`,`task`.`trigger_id` AS `trigger_id`,`matter`.`category_code` AS `category`,`matter`.`caseref` AS `caseref`,`matter`.`country` AS `country`,`matter`.`origin` AS `origin`,`matter`.`type_code` AS `type_code`,`matter`.`idx` AS `idx`,ifnull(`task`.`assigned_to`,`matter`.`responsible`) AS `responsible`,`actor`.`login` AS `delegate`,`task`.`rule_used` AS `rule_used`,`matter`.`dead` AS `dead` from (((((`matter` left join `matter_actor_lnk` on(((ifnull(`matter`.`container_id`,`matter`.`id`) = `matter_actor_lnk`.`matter_id`) and (`matter_actor_lnk`.`role` = 'DEL')))) left join `actor` on((`actor`.`id` = `matter_actor_lnk`.`actor_id`))) join `event` on((`matter`.`id` = `event`.`matter_id`))) join `task` on((`task`.`trigger_id` = `event`.`id`))) join `event_name` on((`task`.`code` = `event_name`.`code`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `users`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`phpip`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `users` AS select `actor`.`id` AS `id`,`actor`.`name` AS `name`,`actor`.`login` AS `login`,`actor`.`password` AS `password`,`actor`.`default_role` AS `default_role`,`actor`.`company_id` AS `company_id`,`actor`.`email` AS `email`,`actor`.`phone` AS `phone`,`actor`.`notes` AS `notes`,`actor`.`creator` AS `creator`,`actor`.`created_at` AS `created_at`,`actor`.`updated_at` AS `updated_at`,`actor`.`updater` AS `updater`,`actor`.`remember_token` AS `remember_token` from `actor` where (`actor`.`login` is not null) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_100000_create_password_resets_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2018_12_07_184310_create_actor_role_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2018_12_07_184310_create_actor_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2018_12_07_184310_create_classifier_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2018_12_07_184310_create_classifier_type_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2018_12_07_184310_create_classifier_value_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2018_12_07_184310_create_country_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2018_12_07_184310_create_default_actor_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2018_12_07_184310_create_event_name_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2018_12_07_184310_create_event_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2018_12_07_184310_create_matter_actor_lnk_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2018_12_07_184310_create_matter_category_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2018_12_07_184310_create_matter_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2018_12_07_184310_create_matter_type_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2018_12_07_184310_create_task_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2018_12_07_184310_create_task_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2018_12_07_184312_add_foreign_keys_to_actor_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2018_12_07_184312_add_foreign_keys_to_classifier_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2018_12_07_184312_add_foreign_keys_to_classifier_type_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2018_12_07_184312_add_foreign_keys_to_classifier_value_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2018_12_07_184312_add_foreign_keys_to_default_actor_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2018_12_07_184312_add_foreign_keys_to_event_name_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2018_12_07_184312_add_foreign_keys_to_event_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2018_12_07_184312_add_foreign_keys_to_matter_actor_lnk_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2018_12_07_184312_add_foreign_keys_to_matter_category_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2018_12_07_184312_add_foreign_keys_to_matter_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2018_12_07_184312_add_foreign_keys_to_task_rules_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2018_12_07_184312_add_foreign_keys_to_task_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2018_12_08_000109_add_trigger',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2018_12_08_002558_create_views_and_functions',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2019_03_07_171752_create_procedure_recalculate_tasks',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2019_03_07_171910_create_procedure_recreate_tasks',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2019_08_13_145446_update_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2019_11_13_135330_update_tables2',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2019_11_17_025422_update_tables3',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2019_11_18_002207_update_tables4',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2019_11_25_123348_update_tables5',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2019_11_26_192706_create_user_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2019_12_06_000000_create_fees_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2019_12_06_002_alter_task_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2019_12_06_003_create_renewal_list_view',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2020_01_06_181200_update_tables6',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2020_01_21_173000_update_tables7',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2020_01_28_122217_update_db_roles',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2020_01_30_001_create_renewals_logs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2020_02_02_105653_add_timestamps_default_actors',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2020_02_12_144400_update_procedure_update_expired',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2020_02_22_161215_create_template_classes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2020_02_22_164446_create_template_members',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2020_02_22_173742_create_event_class_lnk',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2020_02_22_181558_add_foreignkeys_to_template_members',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2020_02_22_183512_add_foreignkeys_to_template_classes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2020_02_24_110300_update_tables8c',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2020_02_24_190000_update_rules2',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2020_02_24_192100_implement_generic_renewals',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2020_03_23_110300_update_tables9c',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2020_03_28_190000_update_country',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2020_04_12_183512_add_foreignkeys_to_event_class_lnk',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2020_04_15_110300_update_tables10',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2020_06_16_122700_update_actor_lang',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2020_06_23_200200_remove_parent_filed_deps',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2020_07_29_190800_fix_insert_recurring_renewals',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2020_07_30_091000_update_special_countries',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2020_09_18_135000_update_tables11',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2020_10_01_130832_update_category_provisional',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2020_12_04_133640_update_matter_alt_ref',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2021_01_29_203100_update_procedure_recalculate_tasks',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2021_02_02_120309_update_classifier_uqvalue_index',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2021_05_18_174249_country_mx_renewals',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2021_08_16_163607_country_ma_renewals',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2021_13_03_165000_country_pl_renewals',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2023_02_16_113312_insert_new_country_codes',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2023_05_22_092530_insert_unitary_patent',1);
