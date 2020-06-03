-- Server version	5.7.29

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `actor`
--

LOCK TABLES `actor` WRITE;
/*!40000 ALTER TABLE `actor` DISABLE KEYS */;
INSERT INTO `actor` VALUES (1,'Client handled',NULL,'CLIENT',NULL,NULL,'ANN',NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'DO NOT DELETE - Special actor used for removing renewal tasks that are handled by the client',NULL,NULL,NULL,NULL,NULL,NULL),
(2,'phpIP User',NULL,NULL,'phpipuser','$2y$10$auLQHQ3EIsg90hqnQsA1huhks3meaxwfWWEvJtD8R38jzwNN6y3zO','DBA',NULL,NULL,NULL,NULL,1,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'root@localhost',NULL,NULL,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `actor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `actor_role`
--

LOCK TABLES `actor_role` WRITE;
/*!40000 ALTER TABLE `actor_role` DISABLE KEYS */;
INSERT INTO `actor_role` VALUES ('AGT','Primary Agent',20,0,1,0,0,0,NULL,NULL,NULL,NULL,NULL),
('AGT2','Secondary Agent',22,0,1,0,0,0,'Usually the primary agent\'s agent',NULL,NULL,NULL,NULL),
('ANN','Annuity Agent',21,0,1,0,0,0,'Agent in charge of renewals. -Client handled- is a special agent who, when added, will delete any renewals in the matter',NULL,NULL,NULL,NULL),
('APP','Applicant',3,1,1,0,0,0,'Assignee in the US, i.e. the owner upon filing',NULL,NULL,NULL,NULL),
('CLI','Client',1,1,1,0,1,0,'The client we take instructions from and who we invoice. DO NOT CHANGE OR DELETE: this is also a database user role',NULL,NULL,NULL,NULL),
('CNT','Contact',30,1,1,1,0,0,'Client\'s contact person',NULL,NULL,NULL,NULL),
('DBA','DB Administrator',127,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL),
('DBRO','DB Read-Only',127,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL),
('DBRW','DB Read/Write',127,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL),
('DEL','Delegate',31,1,0,0,0,0,'Another user allowed to manage the case',NULL,NULL,NULL,NULL),
('FAGT','Former Agent',23,0,1,0,0,0,NULL,NULL,NULL,NULL,NULL),
('FOWN','Former Owner',5,0,0,0,0,1,'To keep track of ownership history',NULL,NULL,NULL,NULL),
('INV','Inventor',10,1,0,1,0,0,NULL,NULL,NULL,NULL,NULL),
('LCN','Licensee',127,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL),
('OFF','Patent Office',127,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL),
('OPP','Opposing Party',127,0,0,0,0,0,NULL,NULL,NULL,NULL,NULL),
('OWN','Owner',4,0,1,0,1,1,'Use if different than applicant',NULL,NULL,NULL,NULL),
('PAY','Payor',2,1,0,0,1,0,'The actor who pays',NULL,NULL,NULL,NULL),
('PTNR','Partner',127,1,0,0,0,0,NULL,NULL,NULL,NULL,NULL),
('TRA','Translator',127,0,0,0,0,1,NULL,NULL,NULL,NULL,NULL),
('WRI','Writer',127,1,0,0,0,0,'Person who follows the case',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `actor_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classifier`
--

LOCK TABLES `classifier` WRITE;
/*!40000 ALTER TABLE `classifier` DISABLE KEYS */;
/*!40000 ALTER TABLE `classifier` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classifier_type`
--

LOCK TABLES `classifier_type` WRITE;
/*!40000 ALTER TABLE `classifier_type` DISABLE KEYS */;
INSERT INTO `classifier_type` VALUES ('ABS','Abstract',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('AGR','Agreement',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('BU','Business Unit',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('DESC','Description',1,NULL,1,NULL,NULL,NULL,NULL,NULL),
('EVAL','Evaluation',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('IMG','Image',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('IPC','IPC',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('KW','Keyword',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('LNK','Link',0,NULL,1,NULL,NULL,NULL,NULL,NULL),
('LOC','Location',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('ORG','Organization',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('PA','Prior Art',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('PROD','Product',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('PROJ','Project',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('TECH','Technology',0,NULL,127,NULL,NULL,NULL,NULL,NULL),
('TIT','Title',1,NULL,1,NULL,NULL,NULL,NULL,NULL),
('TITAL','Alt. Title',1,'PAT',4,NULL,NULL,NULL,NULL,NULL),
('TITEN','English Title',1,'PAT',3,NULL,NULL,NULL,NULL,NULL),
('TITOF','Official Title',1,'PAT',2,NULL,NULL,NULL,NULL,NULL),
('TM','Trademark',1,'TM',1,NULL,NULL,NULL,NULL,NULL),
('TMCL','Class (TM)',0,'TM',2,NULL,NULL,NULL,NULL,NULL),
('TMTYP','Type (TM)',0,'TM',3,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `classifier_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `classifier_value`
--

LOCK TABLES `classifier_value` WRITE;
/*!40000 ALTER TABLE `classifier_value` DISABLE KEYS */;
/*!40000 ALTER TABLE `classifier_value` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `country`
--

LOCK TABLES `country` WRITE;
/*!40000 ALTER TABLE `country` DISABLE KEYS */;
INSERT INTO `country` VALUES (20,'AD','AND','Andorra','Andorra','Andorre',0,0,0,0,2,'FIL','FIL',NULL),
(784,'AE','ARE','Vereinigte Arabische Emirate','United Arab Emirates','Émirats Arabes Unis',0,0,0,0,2,'FIL','FIL',NULL),
(4,'AF','AFG','Afghanistan','Afghanistan','Afghanistan',0,0,0,0,2,'FIL','FIL',NULL),
(28,'AG','ATG','Antigua und Barbuda','Antigua and Barbuda','Antigua-et-Barbuda',0,0,0,0,2,'FIL','FIL',NULL),
(660,'AI','AIA','Anguilla','Anguilla','Anguilla',0,0,0,0,2,'FIL','FIL',NULL),
(8,'AL','ALB','Albanien','Albania','Albanie',0,0,0,0,2,'FIL','FIL',NULL),
(51,'AM','ARM','Armenien','Armenia','Arménie',0,0,0,0,2,'FIL','FIL',NULL),
(530,'AN','ANT','Niederländische Antillen','Netherlands Antilles','Antilles Néerlandaises',0,0,0,0,2,'FIL','FIL',NULL),
(24,'AO','AGO','Angola','Angola','Angola',0,0,0,0,2,'FIL','FIL',NULL),
(10,'AQ','ATA','Antarktis','Antarctica','Antarctique',0,0,0,0,2,'FIL','FIL',NULL),
(32,'AR','ARG','Argentinien','Argentina','Argentine',0,0,0,0,2,'FIL','FIL',NULL),
(16,'AS','ASM','Amerikanisch-Samoa','American Samoa','Samoa Américaines',0,0,0,0,2,'FIL','FIL',NULL),
(40,'AT','AUT','Österreich','Austria','Autriche',0,0,0,0,2,'FIL','FIL',NULL),
(36,'AU','AUS','Australien','Australia','Australie',0,0,0,0,2,'FIL','FIL',NULL),
(533,'AW','ABW','Aruba','Aruba','Aruba',0,0,0,0,2,'FIL','FIL',NULL),
(248,'AX','ALA','Åland-Inseln','Åland Islands','Îles Åland',0,0,0,0,2,'FIL','FIL',NULL),
(31,'AZ','AZE','Aserbaidschan','Azerbaijan','Azerbaïdjan',0,0,0,0,2,'FIL','FIL',NULL),
(70,'BA','BIH','Bosnien und Herzegowina','Bosnia and Herzegovina','Bosnie-Herzégovine',0,0,0,0,2,'FIL','FIL',NULL),
(52,'BB','BRB','Barbados','Barbados','Barbade',0,0,0,0,2,'FIL','FIL',NULL),
(50,'BD','BGD','Bangladesch','Bangladesh','Bangladesh',0,0,0,0,2,'FIL','FIL',NULL),
(56,'BE','BEL','Belgien','Belgium','Belgique',0,0,0,0,2,'FIL','FIL',NULL),
(854,'BF','BFA','Burkina Faso','Burkina Faso','Burkina Faso',0,0,0,0,2,'FIL','FIL',NULL),
(100,'BG','BGR','Bulgarien','Bulgaria','Bulgarie',0,0,0,0,2,'FIL','FIL',NULL),
(48,'BH','BHR','Bahrain','Bahrain','Bahreïn',0,0,0,0,2,'FIL','FIL',NULL),
(108,'BI','BDI','Burundi','Burundi','Burundi',0,0,0,0,2,'FIL','FIL',NULL),
(204,'BJ','BEN','Benin','Benin','Bénin',0,0,0,0,2,'FIL','FIL',NULL),
(60,'BM','BMU','Bermuda','Bermuda','Bermudes',0,0,0,0,2,'FIL','FIL',NULL),
(96,'BN','BRN','Brunei Darussalam','Brunei Darussalam','Brunéi Darussalam',0,0,0,0,2,'FIL','FIL',NULL),
(68,'BO','BOL','Bolivien','Bolivia','Bolivie',0,0,0,0,2,'FIL','FIL',NULL),
(76,'BR','BRA','Brasilien','Brazil','Brésil',0,0,0,0,2,'FIL','FIL',NULL),
(44,'BS','BHS','Bahamas','Bahamas','Bahamas',0,0,0,0,2,'FIL','FIL',NULL),
(64,'BT','BTN','Bhutan','Bhutan','Bhoutan',0,0,0,0,2,'FIL','FIL',NULL),
(74,'BV','BVT','Bouvetinsel','Bouvet Island','Île Bouvet',0,0,0,0,2,'FIL','FIL',NULL),
(72,'BW','BWA','Botswana','Botswana','Botswana',0,0,0,0,2,'FIL','FIL',NULL),
(0,'BX','BLX','Benelux','Benelux','Bénélux',0,0,0,0,2,'FIL','FIL',NULL),
(112,'BY','BLR','Belarus','Belarus','Bélarus',0,0,0,0,2,'FIL','FIL',NULL),
(84,'BZ','BLZ','Belize','Belize','Belize',0,0,0,0,2,'FIL','FIL',NULL),
(124,'CA','CAN','Kanada','Canada','Canada',0,0,0,0,2,'FIL','FIL',NULL),
(166,'CC','CCK','Kokosinseln','Cocos (Keeling) Islands','Îles Cocos (Keeling)',0,0,0,0,2,'FIL','FIL',NULL),
(180,'CD','COD','Demokratische Republik Kongo','The Democratic Republic Of The Congo','République Démocratique du Congo',0,0,0,0,2,'FIL','FIL',NULL),
(140,'CF','CAF','Zentralafrikanische Republik','Central African','République Centrafricaine',0,0,0,0,2,'FIL','FIL',NULL),
(178,'CG','COG','Republik Kongo','Republic of the Congo','République du Congo',0,0,0,0,2,'FIL','FIL',NULL),
(756,'CH','CHE','Schweiz','Switzerland','Suisse',0,0,0,0,2,'FIL','FIL',NULL),
(384,'CI','CIV','Cote d\'Ivoire','Cote d\'Ivoire','Cote d\'Ivoire',0,0,0,0,2,'FIL','FIL',NULL),
(184,'CK','COK','Cookinseln','Cook Islands','Îles Cook',0,0,0,0,2,'FIL','FIL',NULL),
(152,'CL','CHL','Chile','Chile','Chili',0,0,0,0,2,'FIL','FIL',NULL),
(120,'CM','CMR','Kamerun','Cameroon','Cameroun',0,0,0,0,2,'FIL','FIL',NULL),
(156,'CN','CHN','China','China','Chine',0,1,0,0,2,'FIL','FIL',NULL),
(170,'CO','COL','Kolumbien','Colombia','Colombie',0,0,0,0,2,'FIL','FIL',NULL),
(188,'CR','CRI','Costa Rica','Costa Rica','Costa Rica',0,0,0,0,2,'FIL','FIL',NULL),
(891,'CS','SCG','Serbien und Montenegro','Serbia and Montenegro','Serbie-et-Monténégro',0,0,0,0,2,'FIL','FIL',NULL),
(192,'CU','CUB','Kuba','Cuba','Cuba',0,0,0,0,2,'FIL','FIL',NULL),
(132,'CV','CPV','Kap Verde','Cape Verde','Cap-vert',0,0,0,0,2,'FIL','FIL',NULL),
(162,'CX','CXR','Weihnachtsinsel','Christmas Island','Île Christmas',0,0,0,0,2,'FIL','FIL',NULL),
(196,'CY','CYP','Zypern','Cyprus','Chypre',0,0,0,0,2,'FIL','FIL',NULL),
(203,'CZ','CZE','Tschechische Republik','Czech Republic','République Tchèque',0,0,0,0,2,'FIL','FIL',NULL),
(276,'DE','DEU','Deutschland','Germany','Allemagne',1,0,0,0,2,'FIL','FIL',NULL),
(262,'DJ','DJI','Dschibuti','Djibouti','Djibouti',0,0,0,0,2,'FIL','FIL',NULL),
(208,'DK','DNK','Dänemark','Denmark','Danemark',0,0,0,0,2,'FIL','FIL',NULL),
(212,'DM','DMA','Dominica','Dominica','Dominique',0,0,0,0,2,'FIL','FIL',NULL),
(214,'DO','DOM','Dominikanische Republik','Dominican Republic','République Dominicaine',0,0,0,0,2,'FIL','FIL',NULL),
(12,'DZ','DZA','Algerien','Algeria','Algérie',0,0,0,0,2,'FIL','FIL',NULL),
(218,'EC','ECU','Ecuador','Ecuador','Équateur',0,0,0,0,2,'FIL','FIL',NULL),
(233,'EE','EST','Estland','Estonia','Estonie',0,0,0,0,2,'FIL','FIL',NULL),
(818,'EG','EGY','Ägypten','Egypt','Égypte',0,0,0,0,2,'FIL','FIL',NULL),
(732,'EH','ESH','Westsahara','Western Sahara','Sahara Occidental',0,0,0,0,2,'FIL','FIL',NULL),
(0,'EM','EMA','EUIPO','EUIPO','Office de l’Union européenne pour la propriété intellectuelle',0,0,0,0,2,'FIL','FIL',NULL),
(0,'EP','EPO','Europäische Patentorganisation','European Patent Organization','Organisation du Brevet Européen',0,1,0,0,2,'FIL','FIL',NULL),
(232,'ER','ERI','Eritrea','Eritrea','Érythrée',0,0,0,0,2,'FIL','FIL',NULL),
(724,'ES','ESP','Spanien','Spain','Espagne',0,0,0,0,2,'FIL','FIL',NULL),
(231,'ET','ETH','Äthiopien','Ethiopia','Éthiopie',0,0,0,0,2,'FIL','FIL',NULL),
(246,'FI','FIN','Finnland','Finland','Finlande',0,0,0,0,2,'FIL','FIL',NULL),
(242,'FJ','FJI','Fidschi','Fiji','Fidji',0,0,0,0,2,'FIL','FIL',NULL),
(238,'FK','FLK','Falklandinseln','Falkland Islands','Îles (malvinas) Falkland',0,0,0,0,2,'FIL','FIL',NULL),
(583,'FM','FSM','Mikronesien','Federated States of Micronesia','États Fédérés de Micronésie',0,0,0,0,2,'FIL','FIL',NULL),
(234,'FO','FRO','Färöer','Faroe Islands','Îles Féroé',0,0,0,0,2,'FIL','FIL',NULL),
(250,'FR','FRA','Frankreich','France','France',1,0,0,0,2,'FIL','FIL',NULL),
(266,'GA','GAB','Gabun','Gabon','Gabon',0,0,0,0,2,'FIL','FIL',NULL),
(826,'GB','GBR','Vereinigtes Königreich von Großbritannien und Nordirland','United Kingdom','Royaume-Uni',1,0,0,0,2,'FIL','FIL',NULL),
(308,'GD','GRD','Grenada','Grenada','Grenade',0,0,0,0,2,'FIL','FIL',NULL),
(268,'GE','GEO','Georgien','Georgia','Géorgie',0,0,0,0,2,'FIL','FIL',NULL),
(254,'GF','GUF','Französisch-Guayana','French Guiana','Guyane Française',0,0,0,0,2,'FIL','FIL',NULL),
(288,'GH','GHA','Ghana','Ghana','Ghana',0,0,0,0,2,'FIL','FIL',NULL),
(292,'GI','GIB','Gibraltar','Gibraltar','Gibraltar',0,0,0,0,2,'FIL','FIL',NULL),
(304,'GL','GRL','Grönland','Greenland','Groenland',0,0,0,0,2,'FIL','FIL',NULL),
(270,'GM','GMB','Gambia','Gambia','Gambie',0,0,0,0,2,'FIL','FIL',NULL),
(324,'GN','GIN','Guinea','Guinea','Guinée',0,0,0,0,2,'FIL','FIL',NULL),
(312,'GP','GLP','Guadeloupe','Guadeloupe','Guadeloupe',0,0,0,0,2,'FIL','FIL',NULL),
(226,'GQ','GNQ','Äquatorialguinea','Equatorial Guinea','Guinée Équatoriale',0,0,0,0,2,'FIL','FIL',NULL),
(300,'GR','GRC','Griechenland','Greece','Grèce',0,0,0,0,2,'FIL','FIL',NULL),
(239,'GS','SGS','Südgeorgien und die Südlichen Sandwichinseln','South Georgia and the South Sandwich Islands','Géorgie du Sud et les Îles Sandwich du Sud',0,0,0,0,2,'FIL','FIL',NULL),
(320,'GT','GTM','Guatemala','Guatemala','Guatemala',0,0,0,0,2,'FIL','FIL',NULL),
(316,'GU','GUM','Guam','Guam','Guam',0,0,0,0,2,'FIL','FIL',NULL),
(624,'GW','GNB','Guinea-Bissau','Guinea-Bissau','Guinée-Bissau',0,0,0,0,2,'FIL','FIL',NULL),
(328,'GY','GUY','Guyana','Guyana','Guyana',0,0,0,0,2,'FIL','FIL',NULL),
(344,'HK','HKG','Hongkong','Hong Kong','Hong-Kong',0,0,0,0,2,'FIL','FIL',NULL),
(334,'HM','HMD','Heard und McDonaldinseln','Heard Island and McDonald Islands','Îles Heard et Mcdonald',0,0,0,0,2,'FIL','FIL',NULL),
(340,'HN','HND','Honduras','Honduras','Honduras',0,0,0,0,2,'FIL','FIL',NULL),
(191,'HR','HRV','Kroatien','Croatia','Croatie',0,0,0,0,2,'FIL','FIL',NULL),
(332,'HT','HTI','Haiti','Haiti','Haïti',0,0,0,0,2,'FIL','FIL',NULL),
(348,'HU','HUN','Ungarn','Hungary','Hongrie',0,0,0,0,2,'FIL','FIL',NULL),
(0,'IB','IBU','NULL','International Bureau','Bureau International',0,0,0,0,2,'FIL','FIL',NULL),
(360,'ID','IDN','Indonesien','Indonesia','Indonésie',0,0,0,0,2,'FIL','FIL',NULL),
(372,'IE','IRL','Irland','Ireland','Irlande',0,0,0,0,2,'FIL','FIL',NULL),
(376,'IL','ISR','Israel','Israel','Israël',0,0,0,0,2,'FIL','FIL',NULL),
(833,'IM','IMN','Insel Man','Isle of Man','Île de Man',0,0,0,0,2,'FIL','FIL',NULL),
(356,'IN','IND','Indien','India','Inde',0,1,0,0,2,'FIL','FIL',NULL),
(86,'IO','IOT','Britisches Territorium im Indischen Ozean','British Indian Ocean Territory','Territoire Britannique de l\'Océan Indien',0,0,0,0,2,'FIL','FIL',NULL),
(368,'IQ','IRQ','Irak','Iraq','Iraq',0,0,0,0,2,'FIL','FIL',NULL),
(364,'IR','IRN','Islamische Republik Iran','Islamic Republic of Iran','République Islamique d\'Iran',0,0,0,0,2,'FIL','FIL',NULL),
(352,'IS','ISL','Island','Iceland','Islande',0,0,0,0,2,'FIL','FIL',NULL),
(380,'IT','ITA','Italien','Italy','Italie',1,0,0,0,2,'FIL','FIL',NULL),
(388,'JM','JAM','Jamaika','Jamaica','Jamaïque',0,0,0,0,2,'FIL','FIL',NULL),
(400,'JO','JOR','Jordanien','Jordan','Jordanie',0,0,0,0,2,'FIL','FIL',NULL),
(392,'JP','JPN','Japan','Japan','Japon',0,1,0,0,2,'FIL','FIL',NULL),
(404,'KE','KEN','Kenia','Kenya','Kenya',0,0,0,0,2,'FIL','FIL',NULL),
(417,'KG','KGZ','Kirgisistan','Kyrgyzstan','Kirghizistan',0,0,0,0,2,'FIL','FIL',NULL),
(116,'KH','KHM','Kambodscha','Cambodia','Cambodge',0,0,0,0,2,'FIL','FIL',NULL),
(296,'KI','KIR','Kiribati','Kiribati','Kiribati',0,0,0,0,2,'FIL','FIL',NULL),
(174,'KM','COM','Komoren','Comoros','Comores',0,0,0,0,2,'FIL','FIL',NULL),
(659,'KN','KNA','St. Kitts und Nevis','Saint Kitts and Nevis','Saint-Kitts-et-Nevis',0,0,0,0,2,'FIL','FIL',NULL),
(408,'KP','PRK','Demokratische Volksrepublik Korea','Democratic People\'s Republic of Korea','République Populaire Démocratique de Corée',0,0,0,0,2,'FIL','FIL',NULL),
(410,'KR','KOR','Republik Korea','Republic of Korea','République de Corée',0,1,0,0,2,'FIL','FIL',NULL),
(414,'KW','KWT','Kuwait','Kuwait','Koweït',0,0,0,0,2,'FIL','FIL',NULL),
(136,'KY','CYM','Kaimaninseln','Cayman Islands','Îles Caïmanes',0,0,0,0,2,'FIL','FIL',NULL),
(398,'KZ','KAZ','Kasachstan','Kazakhstan','Kazakhstan',0,0,0,0,2,'FIL','FIL',NULL),
(418,'LA','LAO','Demokratische Volksrepublik Laos','Lao People\'s Democratic Republic','République Démocratique Populaire Lao',0,0,0,0,2,'FIL','FIL',NULL),
(422,'LB','LBN','Libanon','Lebanon','Liban',0,0,0,0,2,'FIL','FIL',NULL),
(662,'LC','LCA','St. Lucia','Saint Lucia','Sainte-Lucie',0,0,0,0,2,'FIL','FIL',NULL),
(438,'LI','LIE','Liechtenstein','Liechtenstein','Liechtenstein',0,0,0,0,2,'FIL','FIL',NULL),
(144,'LK','LKA','Sri Lanka','Sri Lanka','Sri Lanka',0,0,0,0,2,'FIL','FIL',NULL),
(430,'LR','LBR','Liberia','Liberia','Libéria',0,0,0,0,2,'FIL','FIL',NULL),
(426,'LS','LSO','Lesotho','Lesotho','Lesotho',0,0,0,0,2,'FIL','FIL',NULL),
(440,'LT','LTU','Litauen','Lithuania','Lituanie',0,0,0,0,2,'FIL','FIL',NULL),
(442,'LU','LUX','Luxemburg','Luxembourg','Luxembourg',0,0,0,0,2,'FIL','FIL',NULL),
(428,'LV','LVA','Lettland','Latvia','Lettonie',0,0,0,0,2,'FIL','FIL',NULL),
(434,'LY','LBY','Libysch-Arabische Dschamahirija','Libyan Arab Jamahiriya','Jamahiriya Arabe Libyenne',0,0,0,0,2,'FIL','FIL',NULL),
(504,'MA','MAR','Marokko','Morocco','Maroc',0,0,0,0,2,'FIL','FIL',NULL),
(492,'MC','MCO','Monaco','Monaco','Monaco',0,0,0,0,2,'FIL','FIL',NULL),
(498,'MD','MDA','Moldawien','Republic of Moldova','République de Moldova',0,0,0,0,2,'FIL','FIL',NULL),
(896,'ME','MNE','Montenegro','Montenegro','Monténégro',0,0,0,0,2,'FIL','FIL','2020-06-03'),
(450,'MG','MDG','Madagaskar','Madagascar','Madagascar',0,0,0,0,2,'FIL','FIL',NULL),
(584,'MH','MHL','Marshallinseln','Marshall Islands','Îles Marshall',0,0,0,0,2,'FIL','FIL',NULL),
(807,'MK','MKD','Ehem. jugoslawische Republik Mazedonien','The Former Yugoslav Republic of Macedonia','L\'ex-République Yougoslave de Macédoine',0,0,0,0,2,'FIL','FIL',NULL),
(466,'ML','MLI','Mali','Mali','Mali',0,0,0,0,2,'FIL','FIL',NULL),
(104,'MM','MMR','Myanmar','Myanmar','Myanmar',0,0,0,0,2,'FIL','FIL',NULL),
(496,'MN','MNG','Mongolei','Mongolia','Mongolie',0,0,0,0,2,'FIL','FIL',NULL),
(446,'MO','MAC','Macao','Macao','Macao',0,0,0,0,2,'FIL','FIL',NULL),
(580,'MP','MNP','Nördliche Marianen','Northern Mariana Islands','Îles Mariannes du Nord',0,0,0,0,2,'FIL','FIL',NULL),
(474,'MQ','MTQ','Martinique','Martinique','Martinique',0,0,0,0,2,'FIL','FIL',NULL),
(478,'MR','MRT','Mauretanien','Mauritania','Mauritanie',0,0,0,0,2,'FIL','FIL',NULL),
(500,'MS','MSR','Montserrat','Montserrat','Montserrat',0,0,0,0,2,'FIL','FIL',NULL),
(470,'MT','MLT','Malta','Malta','Malte',0,0,0,0,2,'FIL','FIL',NULL),
(480,'MU','MUS','Mauritius','Mauritius','Maurice',0,0,0,0,2,'FIL','FIL',NULL),
(462,'MV','MDV','Malediven','Maldives','Maldives',0,0,0,0,2,'FIL','FIL',NULL),
(454,'MW','MWI','Malawi','Malawi','Malawi',0,0,0,0,2,'FIL','FIL',NULL),
(484,'MX','MEX','Mexiko','Mexico','Mexique',0,0,0,0,2,'FIL','FIL',NULL),
(458,'MY','MYS','Malaysia','Malaysia','Malaisie',0,0,0,0,2,'FIL','FIL',NULL),
(508,'MZ','MOZ','Mosambik','Mozambique','Mozambique',0,0,0,0,2,'FIL','FIL',NULL),
(516,'NA','NAM','Namibia','Namibia','Namibie',0,0,0,0,2,'FIL','FIL',NULL),
(540,'NC','NCL','Neukaledonien','New Caledonia','Nouvelle-Calédonie',0,0,0,0,2,'FIL','FIL',NULL),
(562,'NE','NER','Niger','Niger','Niger',0,0,0,0,2,'FIL','FIL',NULL),
(574,'NF','NFK','Norfolkinsel','Norfolk Island','Île Norfolk',0,0,0,0,2,'FIL','FIL',NULL),
(566,'NG','NGA','Nigeria','Nigeria','Nigéria',0,0,0,0,2,'FIL','FIL',NULL),
(558,'NI','NIC','Nicaragua','Nicaragua','Nicaragua',0,0,0,0,2,'FIL','FIL',NULL),
(528,'NL','NLD','Niederlande','Netherlands','Pays-Bas',0,0,0,0,2,'FIL','FIL',NULL),
(578,'NO','NOR','Norwegen','Norway','Norvège',0,0,0,0,2,'FIL','FIL',NULL),
(524,'NP','NPL','Nepal','Nepal','Népal',0,0,0,0,2,'FIL','FIL',NULL),
(520,'NR','NRU','Nauru','Nauru','Nauru',0,0,0,0,2,'FIL','FIL',NULL),
(570,'NU','NIU','Niue','Niue','Niué',0,0,0,0,2,'FIL','FIL',NULL),
(554,'NZ','NZL','Neuseeland','New Zealand','Nouvelle-Zélande',0,0,0,0,2,'FIL','FIL',NULL),
(0,'OA','AIP','Afrikanische Organisation für geistiges Eigentum','African Intellectual Property Organization','Organisation Africaine de la Propriété Intellectuelle',0,0,0,0,2,'FIL','FIL',NULL),
(512,'OM','OMN','Oman','Oman','Oman',0,0,0,0,2,'FIL','FIL',NULL),
(591,'PA','PAN','Panama','Panama','Panama',0,0,0,0,2,'FIL','FIL',NULL),
(604,'PE','PER','Peru','Peru','Pérou',0,0,0,0,2,'FIL','FIL',NULL),
(258,'PF','PYF','Französisch-Polynesien','French Polynesia','Polynésie Française',0,0,0,0,2,'FIL','FIL',NULL),
(598,'PG','PNG','Papua-Neuguinea','Papua New Guinea','Papouasie-Nouvelle-Guinée',0,0,0,0,2,'FIL','FIL',NULL),
(608,'PH','PHL','Philippinen','Philippines','Philippines',0,0,0,0,2,'FIL','FIL',NULL),
(586,'PK','PAK','Pakistan','Pakistan','Pakistan',0,0,0,0,2,'FIL','FIL',NULL),
(616,'PL','POL','Polen','Poland','Pologne',0,0,0,0,2,'FIL','FIL',NULL),
(666,'PM','SPM','St. Pierre und Miquelon','Saint-Pierre and Miquelon','Saint-Pierre-et-Miquelon',0,0,0,0,2,'FIL','FIL',NULL),
(612,'PN','PCN','Pitcairninseln','Pitcairn','Pitcairn',0,0,0,0,2,'FIL','FIL',NULL),
(630,'PR','PRI','Puerto Rico','Puerto Rico','Porto Rico',0,0,0,0,2,'FIL','FIL',NULL),
(275,'PS','PSE','Palästinensische Autonomiegebiete','Occupied Palestinian Territory','Territoire Palestinien Occupé',0,0,0,0,2,'FIL','FIL',NULL),
(620,'PT','PRT','Portugal','Portugal','Portugal',0,0,0,0,2,'FIL','FIL',NULL),
(585,'PW','PLW','Palau','Palau','Palaos',0,0,0,0,2,'FIL','FIL',NULL),
(600,'PY','PRY','Paraguay','Paraguay','Paraguay',0,0,0,0,2,'FIL','FIL',NULL),
(634,'QA','QAT','Katar','Qatar','Qatar',0,0,0,0,2,'FIL','FIL',NULL),
(638,'RE','REU','Réunion','Réunion','Réunion',0,0,0,0,2,'FIL','FIL',NULL),
(642,'RO','ROU','Rumänien','Romania','Roumanie',0,0,0,0,2,'FIL','FIL',NULL),
(895,'RS','SRB','Serbia','Serbia','Serbie',0,0,0,0,3,'FIL','FIL','2020-06-03'),
(643,'RU','RUS','Russische Föderation','Russian Federation','Fédération de Russie',0,0,0,0,2,'FIL','FIL',NULL),
(646,'RW','RWA','Ruanda','Rwanda','Rwanda',0,0,0,0,2,'FIL','FIL',NULL),
(682,'SA','SAU','Saudi-Arabien','Saudi Arabia','Arabie Saoudite',0,0,0,0,2,'FIL','FIL',NULL),
(90,'SB','SLB','Salomonen','Solomon Islands','Îles Salomon',0,0,0,0,2,'FIL','FIL',NULL),
(690,'SC','SYC','Seychellen','Seychelles','Seychelles',0,0,0,0,2,'FIL','FIL',NULL),
(736,'SD','SDN','Sudan','Sudan','Soudan',0,0,0,0,2,'FIL','FIL',NULL),
(752,'SE','SWE','Schweden','Sweden','Suède',0,0,0,0,2,'FIL','FIL',NULL),
(702,'SG','SGP','Singapur','Singapore','Singapour',0,0,0,0,2,'FIL','FIL',NULL),
(654,'SH','SHN','St. Helena','Saint Helena','Sainte-Hélène',0,0,0,0,2,'FIL','FIL',NULL),
(705,'SI','SVN','Slowenien','Slovenia','Slovénie',0,0,0,0,2,'FIL','FIL',NULL),
(744,'SJ','SJM','Svalbard and Jan Mayen','Svalbard and Jan Mayen','Svalbard et Île Jan Mayen',0,0,0,0,2,'FIL','FIL',NULL),
(703,'SK','SVK','Slowakei','Slovakia','Slovaquie',0,0,0,0,2,'FIL','FIL',NULL),
(694,'SL','SLE','Sierra Leone','Sierra Leone','Sierra Leone',0,0,0,0,2,'FIL','FIL',NULL),
(674,'SM','SMR','San Marino','San Marino','Saint-Marin',0,0,0,0,2,'FIL','FIL',NULL),
(686,'SN','SEN','Senegal','Senegal','Sénégal',0,0,0,0,2,'FIL','FIL',NULL),
(706,'SO','SOM','Somalia','Somalia','Somalie',0,0,0,0,2,'FIL','FIL',NULL),
(740,'SR','SUR','Suriname','Suriname','Suriname',0,0,0,0,2,'FIL','FIL',NULL),
(678,'ST','STP','São Tomé und Príncipe','Sao Tome and Principe','Sao Tomé-et-Principe',0,0,0,0,2,'FIL','FIL',NULL),
(222,'SV','SLV','El Salvador','El Salvador','El Salvador',0,0,0,0,2,'FIL','FIL',NULL),
(760,'SY','SYR','Arabische Republik Syrien','Syrian Arab Republic','République Arabe Syrienne',0,0,0,0,2,'FIL','FIL',NULL),
(748,'SZ','SWZ','Swasiland','Swaziland','Swaziland',0,0,0,0,2,'FIL','FIL',NULL),
(796,'TC','TCA','Turks- und Caicosinseln','Turks and Caicos Islands','Îles Turks et Caïques',0,0,0,0,2,'FIL','FIL',NULL),
(148,'TD','TCD','Tschad','Chad','Tchad',0,0,0,0,2,'FIL','FIL',NULL),
(260,'TF','ATF','Französische Süd- und Antarktisgebiete','French Southern Territories','Terres Australes Françaises',0,0,0,0,2,'FIL','FIL',NULL),
(768,'TG','TGO','Togo','Togo','Togo',0,0,0,0,2,'FIL','FIL',NULL),
(764,'TH','THA','Thailand','Thailand','Thaïlande',0,0,0,0,2,'FIL','FIL',NULL),
(762,'TJ','TJK','Tadschikistan','Tajikistan','Tadjikistan',0,0,0,0,2,'FIL','FIL',NULL),
(772,'TK','TKL','Tokelau','Tokelau','Tokelau',0,0,0,0,2,'FIL','FIL',NULL),
(626,'TL','TLS','Timor-Leste','Timor-Leste','Timor-Leste',0,0,0,0,2,'FIL','FIL',NULL),
(795,'TM','TKM','Turkmenistan','Turkmenistan','Turkménistan',0,0,0,0,2,'FIL','FIL',NULL),
(788,'TN','TUN','Tunesien','Tunisia','Tunisie',0,0,0,0,2,'FIL','FIL',NULL),
(776,'TO','TON','Tonga','Tonga','Tonga',0,0,0,0,2,'FIL','FIL',NULL),
(792,'TR','TUR','Türkei','Turkey','Turquie',0,0,0,0,2,'FIL','FIL',NULL),
(780,'TT','TTO','Trinidad und Tobago','Trinidad and Tobago','Trinité-et-Tobago',0,0,0,0,2,'FIL','FIL',NULL),
(798,'TV','TUV','Tuvalu','Tuvalu','Tuvalu',0,0,0,0,2,'FIL','FIL',NULL),
(158,'TW','TWN','Taiwan','Taiwan','Taïwan',0,0,0,0,2,'FIL','FIL',NULL),
(834,'TZ','TZA','Vereinigte Republik Tansania','United Republic Of Tanzania','République-Unie de Tanzanie',0,0,0,0,2,'FIL','FIL',NULL),
(804,'UA','UKR','Ukraine','Ukraine','Ukraine',0,0,0,0,2,'FIL','FIL',NULL),
(800,'UG','UGA','Uganda','Uganda','Ouganda',0,0,0,0,2,'FIL','FIL',NULL),
(581,'UM','UMI','Amerikanisch-Ozeanien','United States Minor Outlying Islands','Îles Mineures Éloignées des États-Unis',0,0,0,0,2,'FIL','FIL',NULL),
(840,'US','USA','Vereinigte Staaten von Amerika','United States','États-Unis',0,1,0,0,2,'FIL','FIL',NULL),
(858,'UY','URY','Uruguay','Uruguay','Uruguay',0,0,0,0,2,'FIL','FIL',NULL),
(860,'UZ','UZB','Usbekistan','Uzbekistan','Ouzbékistan',0,0,0,0,2,'FIL','FIL',NULL),
(336,'VA','VAT','Vatikanstadt','Vatican City State','Saint-Siège (état de la Cité du Vatican)',0,0,0,0,2,'FIL','FIL',NULL),
(670,'VC','VCT','St. Vincent und die Grenadinen','Saint Vincent and the Grenadines','Saint-Vincent-et-les Grenadines',0,0,0,0,2,'FIL','FIL',NULL),
(862,'VE','VEN','Venezuela','Venezuela','Venezuela',0,0,0,0,2,'FIL','FIL',NULL),
(92,'VG','VGB','Britische Jungferninseln','British Virgin Islands','Îles Vierges Britanniques',0,0,0,0,2,'FIL','FIL',NULL),
(850,'VI','VIR','Amerikanische Jungferninseln','U.S. Virgin Islands','Îles Vierges des États-Unis',0,0,0,0,2,'FIL','FIL',NULL),
(704,'VN','VNM','Vietnam','Vietnam','Viet Nam',0,0,0,0,2,'FIL','FIL',NULL),
(548,'VU','VUT','Vanuatu','Vanuatu','Vanuatu',0,0,0,0,2,'FIL','FIL',NULL),
(876,'WF','WLF','Wallis und Futuna','Wallis and Futuna','Wallis et Futuna',0,0,0,0,2,'FIL','FIL',NULL),
(0,'WO','PCT','Weltorganisation für geistiges Eigentum','World Intellectual Property Organization','Organisation Mondiale de la Propriété Intellectuelle',0,0,0,0,2,'FIL','FIL',NULL),
(882,'WS','WSM','Samoa','Samoa','Samoa',0,0,0,0,2,'FIL','FIL',NULL),
(887,'YE','YEM','Jemen','Yemen','Yémen',0,0,0,0,2,'FIL','FIL',NULL),
(175,'YT','MYT','Mayotte','Mayotte','Mayotte',0,0,0,0,2,'FIL','FIL',NULL),
(710,'ZA','ZAF','Südafrika','South Africa','Afrique du Sud',0,0,0,0,2,'FIL','FIL',NULL),
(894,'ZM','ZMB','Sambia','Zambia','Zambie',0,0,0,0,2,'FIL','FIL',NULL),
(716,'ZW','ZWE','Simbabwe','Zimbabwe','Zimbabwe',0,0,0,0,2,'FIL','FIL',NULL);
/*!40000 ALTER TABLE `country` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `default_actor`
--

LOCK TABLES `default_actor` WRITE;
/*!40000 ALTER TABLE `default_actor` DISABLE KEYS */;
/*!40000 ALTER TABLE `default_actor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `event_class_lnk`
--

LOCK TABLES `event_class_lnk` WRITE;
/*!40000 ALTER TABLE `event_class_lnk` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_class_lnk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `event_name`
--

LOCK TABLES `event_name` WRITE;
/*!40000 ALTER TABLE `event_name` DISABLE KEYS */;
INSERT INTO `event_name` VALUES ('ABA','Abandoned',NULL,NULL,0,1,NULL,0,1,1,NULL,NULL,NULL,NULL,NULL),
('ABO','Abandon Original','PAT','EP',1,0,NULL,0,1,0,'Abandon the originating patent that was re-designated in EP',NULL,NULL,NULL,NULL),
('ADV','Advisory Action','PAT','US',0,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('ALL','Allowance','PAT',NULL,0,1,NULL,0,0,0,'Use also for R71.3 in EP',NULL,NULL,NULL,NULL),
('APL','Appeal',NULL,NULL,0,1,NULL,1,0,0,'Appeal or other remedy filed',NULL,NULL,NULL,NULL),
('CAN','Cancelled','TM',NULL,0,1,NULL,0,0,1,NULL,NULL,NULL,NULL,NULL),
('CLO','Closed','LTG',NULL,0,1,NULL,0,0,1,NULL,NULL,NULL,NULL,NULL),
('COM','Communication',NULL,NULL,0,0,NULL,0,0,0,'Communication regarding administrative or formal matters (missing parts, irregularities...)',NULL,NULL,NULL,NULL),
('CRE','Created',NULL,NULL,0,0,NULL,0,1,0,'Creation date of matter - for attaching tasks necessary before anything else',NULL,NULL,NULL,NULL),
('DAPL','Decision on Appeal',NULL,NULL,0,0,NULL,0,0,0,'State outcome in detail field',NULL,NULL,NULL,NULL),
('DBY','Draft By','PAT',NULL,1,0,NULL,1,0,0,NULL,NULL,NULL,NULL,NULL),
('DEX','Deadline Extended',NULL,NULL,0,0,NULL,0,0,0,'Deadline extension requested',NULL,NULL,NULL,NULL),
('DPAPL','Decision on Pre-Appeal','PAT','US',0,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('DRA','Drafted','PAT',NULL,0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('DW','Deemed withrawn',NULL,NULL,0,1,NULL,0,0,0,'Decision needing a reply, such as further processing',NULL,NULL,NULL,NULL),
('EHK','Extend to Hong Kong','PAT','CN',1,0,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('ENT','Entered','PAT',NULL,0,0,NULL,0,1,0,'National entry date from PCT phase',NULL,NULL,NULL,NULL),
('EOP','End of Procedure','PAT',NULL,0,1,NULL,0,1,1,'Indicates end of international phase for PCT',NULL,NULL,NULL,NULL),
('EXA','Examiner Action',NULL,NULL,0,0,NULL,0,0,0,'AKA Office Action, i.e. anything related to substantive examination',NULL,NULL,NULL,NULL),
('EXAF','Examiner Action (Final)','PAT','US',0,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('EXP','Expiry',NULL,NULL,0,1,NULL,0,1,1,'Do not use nor change - present for internal functionality',NULL,NULL,NULL,NULL),
('FAP','File Notice of Appeal',NULL,NULL,1,0,NULL,1,0,0,NULL,NULL,NULL,NULL,NULL),
('FBY','File by',NULL,NULL,1,0,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('FDIV','File Divisional','PAT',NULL,1,0,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('FIL','Filed',NULL,NULL,0,1,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('FOP','File Opposition','OP','EP',1,0,NULL,1,1,0,NULL,NULL,NULL,NULL,NULL),
('FPR','Further Processing','PAT',NULL,1,0,NULL,1,0,0,NULL,NULL,NULL,NULL,NULL),
('FRCE','File RCE','PAT','US',1,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('GRT','Granted','PAT',NULL,0,1,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('INV','Invalidated','TM','US',0,1,NULL,0,0,1,NULL,NULL,NULL,NULL,NULL),
('LAP','Lapsed',NULL,NULL,0,1,NULL,0,1,1,NULL,NULL,NULL,NULL,NULL),
('NPH','National Phase','PAT','WO',1,0,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('OPP','Opposition',NULL,'EP',0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('OPR','Oral Proceedings','PAT','EP',1,0,NULL,1,0,0,NULL,NULL,NULL,NULL,NULL),
('ORE','Opposition rejected','PAT','EP',0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('PAY','Pay',NULL,NULL,1,0,NULL,0,0,0,'Use for any fees to be paid',NULL,NULL,NULL,NULL),
('PDES','Post designation','TM','WO',0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('PFIL','Parent Filed','PAT',NULL,0,1,NULL,0,1,0,'Filing date of the parent (use only when the matter type is defined). Use as link to the parent matter.',NULL,NULL,NULL,NULL),
('PR','Publication of Reg.','TM',NULL,0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('PREP','Prepare',NULL,NULL,1,0,NULL,1,0,0,'Any further action to be done by the responsible (comments, pre-handling, ...)',NULL,NULL,NULL,NULL),
('PRI','Priority Claim',NULL,NULL,0,1,NULL,0,0,0,'Use as link to the priority matter',NULL,NULL,NULL,NULL),
('PRID','Priority Deadline',NULL,NULL,1,0,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('PROD','Produce',NULL,NULL,1,0,NULL,0,0,0,'Any further documents to be filed (inventor designation, priority document, missing parts...)',NULL,NULL,NULL,NULL),
('PSR','Publication of SR','PAT','EP',0,0,NULL,0,1,0,'A3 publication',NULL,NULL,NULL,NULL),
('PUB','Published',NULL,NULL,0,1,NULL,0,0,0,'For EP, this means publication WITH the search report (A1 publ.)',NULL,NULL,NULL,NULL),
('RCE','Request Continued Examination','PAT','US',0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('REC','Received',NULL,NULL,0,1,NULL,0,1,0,'Date the case was received from the client',NULL,NULL,NULL,NULL),
('REF','Refused',NULL,NULL,0,1,NULL,0,0,0,'This is the final decision, that can only be appealed - do not mistake with an exam report',NULL,NULL,NULL,NULL),
('REG','Registration','TM',NULL,0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('REM','Reminder',NULL,NULL,1,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('REN','Renewal',NULL,NULL,1,0,NULL,0,0,0,'AKA Annuity',NULL,NULL,NULL,NULL),
('REP','Respond',NULL,NULL,1,0,NULL,1,0,0,'Use for any response',NULL,NULL,NULL,NULL),
('REQ','Request Examination',NULL,NULL,1,0,NULL,0,1,0,NULL,NULL,NULL,NULL,NULL),
('RSTR','Restriction Req.','PAT','US',0,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('SOL','Sold',NULL,NULL,0,1,NULL,0,0,1,NULL,NULL,NULL,NULL,NULL),
('SOP','Summons to Oral Proc.',NULL,NULL,0,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('SR','Search Report',NULL,NULL,0,0,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('SUS','Suspended',NULL,NULL,0,1,NULL,0,0,0,NULL,NULL,NULL,NULL,NULL),
('TRF','Transferred',NULL,NULL,0,1,NULL,0,1,1,'Case no longer followed',NULL,NULL,NULL,NULL),
('VAL','Validate','PAT','EP',1,0,NULL,0,1,0,'Validate granted EP in designated countries',NULL,NULL,NULL,NULL),
('WAT','Watch',NULL,NULL,1,0,NULL,1,0,0,NULL,NULL,NULL,NULL,NULL),
('WIT','Withdrawal','PAT',NULL,0,1,NULL,0,0,1,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `event_name` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `fees`
--

LOCK TABLES `fees` WRITE;
/*!40000 ALTER TABLE `fees` DISABLE KEYS */;
INSERT INTO `fees` VALUES (1,'PAT','FR',NULL,2,NULL,NULL,38.00,100.00,19.00,100.00,57.00,150.00,28.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(2,'PAT','FR',NULL,3,NULL,NULL,38.00,100.00,19.00,100.00,57.00,150.00,28.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(3,'PAT','FR',NULL,4,NULL,NULL,38.00,100.00,19.00,100.00,57.00,150.00,28.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(4,'PAT','FR',NULL,5,NULL,NULL,38.00,100.00,19.00,100.00,57.00,150.00,28.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(5,'PAT','FR',NULL,6,NULL,NULL,76.00,100.00,57.00,100.00,114.00,150.00,85.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(6,'PAT','FR',NULL,7,NULL,NULL,96.00,100.00,72.00,100.00,144.00,150.00,108.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(7,'PAT','FR',NULL,8,NULL,NULL,136.00,100.00,0.00,100.00,204.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(8,'PAT','FR',NULL,9,NULL,NULL,180.00,100.00,0.00,100.00,270.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(9,'PAT','FR',NULL,10,NULL,NULL,220.00,100.00,0.00,100.00,330.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(10,'PAT','FR',NULL,11,NULL,NULL,260.00,100.00,0.00,100.00,390.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(11,'PAT','FR',NULL,12,NULL,NULL,300.00,100.00,0.00,100.00,450.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(12,'PAT','FR',NULL,13,NULL,NULL,350.00,100.00,0.00,100.00,525.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(13,'PAT','FR',NULL,14,NULL,NULL,400.00,100.00,0.00,100.00,600.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(14,'PAT','FR',NULL,15,NULL,NULL,450.00,100.00,0.00,100.00,675.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(15,'PAT','FR',NULL,16,NULL,NULL,510.00,100.00,0.00,100.00,765.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(16,'PAT','FR',NULL,17,NULL,NULL,570.00,100.00,0.00,100.00,855.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(17,'PAT','FR',NULL,18,NULL,NULL,640.00,100.00,0.00,100.00,960.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(18,'PAT','FR',NULL,19,NULL,NULL,720.00,100.00,0.00,100.00,1080.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(19,'PAT','FR',NULL,20,NULL,NULL,790.00,100.00,0.00,100.00,1185.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(20,'PAT','EP',NULL,3,NULL,NULL,470.00,100.00,0.00,100.00,705.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(21,'PAT','EP',NULL,4,NULL,NULL,585.00,100.00,0.00,100.00,877.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(22,'PAT','EP',NULL,5,NULL,NULL,820.00,100.00,0.00,100.00,1230.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(23,'PAT','EP',NULL,6,NULL,NULL,1050.00,100.00,0.00,100.00,1575.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(24,'PAT','EP',NULL,7,NULL,NULL,1165.00,100.00,0.00,100.00,1477.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(25,'PAT','EP',NULL,8,NULL,NULL,1280.00,100.00,0.00,100.00,1920.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(26,'PAT','EP',NULL,9,NULL,NULL,1395.00,100.00,0.00,100.00,2092.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(27,'PAT','EP',NULL,10,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(28,'PAT','EP',NULL,11,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(29,'PAT','EP',NULL,12,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(30,'PAT','EP',NULL,13,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(31,'PAT','EP',NULL,14,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(32,'PAT','EP',NULL,15,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(33,'PAT','EP',NULL,16,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(34,'PAT','EP',NULL,17,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(35,'PAT','EP',NULL,18,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(36,'PAT','EP',NULL,19,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(37,'PAT','EP',NULL,20,NULL,NULL,1575.00,100.00,0.00,100.00,2362.50,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(38,'PAT','BE',NULL,3,NULL,NULL,40.00,100.00,0.00,100.00,125.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(39,'PAT','BE',NULL,4,NULL,NULL,55.00,100.00,0.00,100.00,140.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(40,'PAT','BE',NULL,5,NULL,NULL,75.00,100.00,0.00,100.00,160.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(41,'PAT','BE',NULL,6,NULL,NULL,95.00,100.00,0.00,100.00,180.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(42,'PAT','BE',NULL,7,NULL,NULL,110.00,100.00,0.00,100.00,195.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(43,'PAT','BE',NULL,8,NULL,NULL,135.00,100.00,0.00,100.00,220.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(44,'PAT','BE',NULL,9,NULL,NULL,165.00,100.00,0.00,100.00,250.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(45,'PAT','BE',NULL,10,NULL,NULL,185.00,100.00,0.00,100.00,270.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(46,'PAT','BE',NULL,11,NULL,NULL,215.00,100.00,0.00,100.00,445.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(47,'PAT','BE',NULL,12,NULL,NULL,240.00,100.00,0.00,100.00,470.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(48,'PAT','BE',NULL,13,NULL,NULL,275.00,100.00,0.00,100.00,505.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(49,'PAT','BE',NULL,14,NULL,NULL,320.00,100.00,0.00,100.00,550.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(50,'PAT','BE',NULL,15,NULL,NULL,360.00,100.00,0.00,100.00,590.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(51,'PAT','BE',NULL,16,NULL,NULL,400.00,100.00,0.00,100.00,630.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(52,'PAT','BE',NULL,17,NULL,NULL,450.00,100.00,0.00,100.00,680.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(53,'PAT','BE',NULL,18,NULL,NULL,500.00,100.00,0.00,100.00,730.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(54,'PAT','BE',NULL,19,NULL,NULL,555.00,100.00,0.00,100.00,780.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(55,'PAT','BE',NULL,20,NULL,NULL,600.00,100.00,0.00,100.00,830.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(56,'PAT','DE',NULL,3,NULL,NULL,70.00,100.00,0.00,100.00,120.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(57,'PAT','DE',NULL,4,NULL,NULL,70.00,100.00,0.00,100.00,120.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(58,'PAT','DE',NULL,5,NULL,NULL,90.00,100.00,0.00,180.00,140.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(59,'PAT','DE',NULL,6,NULL,NULL,130.00,100.00,0.00,100.00,180.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(60,'PAT','DE',NULL,7,NULL,NULL,180.00,100.00,0.00,100.00,230.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(61,'PAT','DE',NULL,8,NULL,NULL,240.00,100.00,0.00,100.00,290.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(62,'PAT','DE',NULL,9,NULL,NULL,290.00,100.00,0.00,100.00,340.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(63,'PAT','DE',NULL,10,NULL,NULL,350.00,100.00,0.00,100.00,400.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(64,'PAT','DE',NULL,11,NULL,NULL,470.00,100.00,0.00,100.00,520.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(65,'PAT','DE',NULL,12,NULL,NULL,620.00,100.00,0.00,100.00,670.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(66,'PAT','DE',NULL,13,NULL,NULL,760.00,100.00,0.00,100.00,810.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(67,'PAT','DE',NULL,14,NULL,NULL,910.00,100.00,0.00,100.00,960.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(68,'PAT','DE',NULL,15,NULL,NULL,1060.00,100.00,0.00,100.00,1110.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(69,'PAT','DE',NULL,16,NULL,NULL,1230.00,100.00,0.00,100.00,1280.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(70,'PAT','DE',NULL,17,NULL,NULL,1410.00,100.00,0.00,100.00,1460.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(71,'PAT','DE',NULL,18,NULL,NULL,1590.00,100.00,0.00,100.00,1640.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(72,'PAT','DE',NULL,19,NULL,NULL,1760.00,100.00,0.00,100.00,1810.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL),
(73,'PAT','DE',NULL,20,NULL,NULL,1940.00,100.00,0.00,100.00,2000.00,150.00,0.00,150.00,'EUR',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `fees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `matter`
--

LOCK TABLES `matter` WRITE;
/*!40000 ALTER TABLE `matter` DISABLE KEYS */;
/*!40000 ALTER TABLE `matter` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `matter_actor_lnk`
--

LOCK TABLES `matter_actor_lnk` WRITE;
/*!40000 ALTER TABLE `matter_actor_lnk` DISABLE KEYS */;
/*!40000 ALTER TABLE `matter_actor_lnk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `matter_category`
--

LOCK TABLES `matter_category` WRITE;
/*!40000 ALTER TABLE `matter_category` DISABLE KEYS */;
INSERT INTO `matter_category` VALUES ('AGR','AGR','Agreement','OTH',NULL,NULL,NULL,NULL),
('DSG','DSG','Design','TM',NULL,NULL,NULL,NULL),
('FTO','OPI','Freedom to Operate','LTG',NULL,NULL,NULL,NULL),
('LTG','LTG','Litigation','LTG',NULL,NULL,NULL,NULL),
('OP','OPP','Opposition (patent)','LTG',NULL,NULL,NULL,NULL),
('OPI','OPI','Opinion','LTG',NULL,NULL,NULL,NULL),
('OTH','OTH','Others','OTH',NULL,NULL,NULL,NULL),
('PAT','PAT','Patent','PAT',NULL,NULL,NULL,NULL),
('PRO','PAT','Provisional Application','PAT',NULL,NULL,NULL,NULL),
('SO','PAT','Soleau Envelop','PAT',NULL,NULL,NULL,NULL),
('SR','SR-','Search','LTG',NULL,NULL,NULL,NULL),
('TM','TM-','Trademark','TM',NULL,NULL,NULL,NULL),
('TMOP','TOP','Opposition (TM)','TM',NULL,NULL,NULL,NULL),
('TS','TS-','Trade Secret','PAT',NULL,NULL,NULL,NULL),
('UC','PAT','Utility Certificate','PAT',NULL,NULL,NULL,NULL),
('UM','PAT','Utility Model','PAT',NULL,NULL,NULL,NULL),
('WAT','WAT','Watch','TM',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `matter_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `matter_type`
--

LOCK TABLES `matter_type` WRITE;
/*!40000 ALTER TABLE `matter_type` DISABLE KEYS */;
INSERT INTO `matter_type` VALUES ('CIP','Continuation in Part',NULL,NULL,NULL,NULL),
('CNT','Continuation',NULL,NULL,NULL,NULL),
('DIV','Divisional',NULL,NULL,NULL,NULL),
('REI','Reissue',NULL,NULL,NULL,NULL),
('REX','Re-examination',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `matter_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_100000_create_password_resets_table',1),
(2,'2018_12_07_184310_create_actor_role_table',1),
(3,'2018_12_07_184310_create_actor_table',1),
(4,'2018_12_07_184310_create_classifier_table',1),
(5,'2018_12_07_184310_create_classifier_type_table',1),
(6,'2018_12_07_184310_create_classifier_value_table',1),
(7,'2018_12_07_184310_create_country_table',1),
(8,'2018_12_07_184310_create_default_actor_table',1),
(9,'2018_12_07_184310_create_event_name_table',1),
(10,'2018_12_07_184310_create_event_table',1),
(11,'2018_12_07_184310_create_matter_actor_lnk_table',1),
(12,'2018_12_07_184310_create_matter_category_table',1),
(13,'2018_12_07_184310_create_matter_table',1),
(14,'2018_12_07_184310_create_matter_type_table',1),
(15,'2018_12_07_184310_create_task_rules_table',1),
(16,'2018_12_07_184310_create_task_table',1),
(17,'2018_12_07_184312_add_foreign_keys_to_actor_table',1),
(18,'2018_12_07_184312_add_foreign_keys_to_classifier_table',1),
(19,'2018_12_07_184312_add_foreign_keys_to_classifier_type_table',1),
(20,'2018_12_07_184312_add_foreign_keys_to_classifier_value_table',1),
(21,'2018_12_07_184312_add_foreign_keys_to_default_actor_table',1),
(22,'2018_12_07_184312_add_foreign_keys_to_event_name_table',1),
(23,'2018_12_07_184312_add_foreign_keys_to_event_table',1),
(24,'2018_12_07_184312_add_foreign_keys_to_matter_actor_lnk_table',1),
(25,'2018_12_07_184312_add_foreign_keys_to_matter_category_table',1),
(26,'2018_12_07_184312_add_foreign_keys_to_matter_table',1),
(27,'2018_12_07_184312_add_foreign_keys_to_task_rules_table',1),
(28,'2018_12_07_184312_add_foreign_keys_to_task_table',1),
(29,'2018_12_08_000109_add_trigger',1),
(30,'2018_12_08_002558_create_views_and_functions',1),
(31,'2019_03_07_171752_create_procedure_recalculate_tasks',1),
(32,'2019_03_07_171910_create_procedure_recreate_tasks',1),
(33,'2019_08_13_145446_update_tables',1),
(34,'2019_08_19_000000_create_failed_jobs_table',1),
(35,'2019_11_13_135330_update_tables2',1),
(36,'2019_11_17_025422_update_tables3',1),
(37,'2019_11_18_002207_update_tables4',1),
(38,'2019_11_25_123348_update_tables5',1),
(39,'2019_11_26_192706_create_user_view',1),
(40,'2020_01_06_181200_update_tables6',1),
(41,'2020_01_21_173000_update_tables7',1),
(42,'2020_01_28_122217_update_db_roles',1),
(43,'2020_02_02_105653_add_timestamps_default_actors',1),
(44,'2020_02_12_144400_update_procedure_update_expired',1),
(45,'2020_02_24_110300_update_tables8c',1),
(46,'2020_02_24_190000_update_rules2',1),
(47,'2020_02_24_192100_implement_generic_renewals',1),
(48,'2020_03_28_190000_update_country',1),
(49,'2020_03_23_110300_update_tables9c',2),
(50,'2019_12_06_000000_create_fees_table',3),
(51,'2019_12_06_002_alter_task_table',3),
(52,'2019_12_06_003_create_renewal_list_view',3),
(53,'2020_01_30_001_create_renewals_logs_table',3),
(54,'2020_02_22_161215_create_template_classes',4),
(55,'2020_02_22_164446_create_template_members',4),
(56,'2020_02_22_173742_create_event_class_lnk',4),
(57,'2020_02_22_181558_add_foreignkeys_to_template_members',4),
(58,'2020_02_22_183512_add_foreignkeys_to_template_classes',4),
(59,'2020_04_12_183512_add_foreignkeys_to_event_class_lnk',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `renewals_logs`
--

LOCK TABLES `renewals_logs` WRITE;
/*!40000 ALTER TABLE `renewals_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `renewals_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `task_rules`
--

LOCK TABLES `task_rules` WRITE;
/*!40000 ALTER TABLE `task_rules` DISABLE KEYS */;
INSERT INTO `task_rules` (`id`, `active`, `task`, `trigger_event`, `clear_task`, `delete_task`, `for_category`, `for_country`, `for_origin`, `for_type`, `detail`, `days`, `months`, `years`, `recurring`, `end_of_month`, `abort_on`, `condition_event`, `use_parent`, `use_priority`, `use_before`, `use_after`, `cost`, `fee`, `currency`, `responsible`, `notes`, `creator`, `updater`, `created_at`, `updated_at`) VALUES (1,1,'PRID','FIL',0,0,'PAT',NULL,NULL,NULL,NULL,0,12,0,0,0,'PRI',NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Priority deadline is inserted only if no priority event exists',NULL,NULL,NULL,NULL),
(2,1,'PRID','FIL',0,0,'TM',NULL,NULL,NULL,NULL,0,6,0,0,0,'PRI',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'Priority deadline is inserted only if no priority event exists',NULL,NULL,NULL,NULL),
(3,1,'FBY','FIL',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'Clear \"File by\" task when \"Filed\" event is created',NULL,NULL,NULL,NULL),
(4,1,'PRID','FIL',0,0,'PRO',NULL,NULL,NULL,NULL,0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
(5,1,'DBY','DRA',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'Clear \"Draft by\" task when \"Drafted\" event is created',NULL,NULL,NULL,NULL),
(6,1,'REQ','FIL',0,0,'PAT','JP',NULL,NULL,NULL,0,0,3,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(7,1,'REQ','PUB',0,0,'PAT','EP',NULL,NULL,NULL,0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(8,1,'EXP','FIL',0,0,'PRO',NULL,NULL,NULL,NULL,0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(9,1,'REP','SR',0,0,'PAT','FR',NULL,NULL,'Search Report',0,3,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(10,1,'REP','EXA',0,0,'PAT',NULL,NULL,NULL,'Exam Report',0,3,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(11,1,'REP','EXA',0,0,'PAT','EP',NULL,NULL,'Exam Report',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(12,1,'EXP','FIL',0,0,'PAT',NULL,NULL,NULL,NULL,0,0,20,0,0,NULL,NULL,1,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(13,1,'REP','ALL',0,0,'PAT','EP',NULL,NULL,'R71(3)',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(14,1,'PAY','ALL',0,0,'PAT','EP',NULL,NULL,'Grant Fee',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(15,1,'PROD','ALL',0,0,'PAT','EP',NULL,NULL,'Claim Translation',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(16,1,'VAL','GRT',0,0,'PAT','EP',NULL,NULL,'Translate where necessary',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(18,1,'REP','PUB',0,0,'PAT','EP',NULL,NULL,'Written Opinion',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(19,1,'PAY','PUB',0,0,'PAT','EP',NULL,NULL,'Designation Fees',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(20,1,'PROD','PRI',0,0,'PAT','US',NULL,NULL,'Decl. and Assignment',0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(21,1,'FBY','PRI',0,0,'PAT',NULL,NULL,NULL,'Priority Deadline',0,12,0,0,0,'FIL',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(22,1,'NPH','FIL',0,0,'PAT','WO',NULL,NULL,NULL,0,30,0,0,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(23,1,'REQ','FIL',0,0,'PAT','WO',NULL,NULL,NULL,0,22,0,0,0,'EXA',NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(24,1,'DBY','REC',0,0,'PAT',NULL,NULL,NULL,NULL,0,2,0,0,0,'PRI',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(25,1,'PRID','PRI',0,1,'PAT',NULL,NULL,NULL,'Delete',0,0,0,0,0,NULL,'FIL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Deletes priority deadline when a priority event is inserted',NULL,NULL,NULL,NULL),
(26,1,'EHK','PUB',0,0,'PAT','CN',NULL,NULL,NULL,0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(27,1,'FOP','GRT',0,0,'OP','EP',NULL,NULL,NULL,0,9,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(29,1,'DBY','FIL',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(30,1,'PROD','FIL',0,0,'PAT','US',NULL,NULL,'IDS',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(33,1,'EXP','FIL',0,0,'PAT','WO',NULL,NULL,NULL,0,31,0,0,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(34,1,'REM','FIL',0,0,'PAT','WO',NULL,NULL,'National Phase',0,27,0,0,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(35,1,'PROD','FIL',0,0,'PAT','FR',NULL,NULL,'Small Entity',0,1,0,0,0,'PRI',NULL,1,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(36,1,'PAY','GRT',0,0,'PAT','CN',NULL,NULL,'HK Grant Fee',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(37,1,'REP','COM',0,0,'PAT',NULL,NULL,NULL,'Communication',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(38,1,'FOP','OPP',1,0,'OP','EP',NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Clear \"File Opposition\" task when \"Opposition\" event is created',NULL,NULL,NULL,NULL),
(39,1,'PAY','ALL',0,0,'PAT','JP',NULL,NULL,'Grant Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(40,1,'FPR','DW',0,0,'PAT','EP',NULL,NULL,NULL,0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(41,1,'REP','PSR',0,0,'PAT','EP',NULL,NULL,'R70(2)',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(42,1,'NPH','PRI',0,0,'PAT',NULL,'WO',NULL,NULL,0,30,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(43,1,'PAY','FIL',0,0,'PAT','FR',NULL,NULL,'Filing Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(44,1,'PAY','FIL',0,0,'PAT','EP',NULL,NULL,'Filing Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(45,1,'VAL','GRT',0,0,'PAT',NULL,'EP',NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(46,1,'REP','RSTR',0,0,'PAT','US',NULL,NULL,'Restriction Req.',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(47,1,'REP','COM',0,0,'PAT','EP',NULL,NULL,'R161',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(48,1,'FAP','REF',0,0,'PAT',NULL,NULL,NULL,NULL,0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(49,1,'PROD','APL',0,0,'PAT',NULL,NULL,NULL,'Appeal Brief',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(52,1,'REP','COM',0,0,'OP','EP',NULL,NULL,'Observations',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(53,1,'REQ','FIL',0,0,'PAT','KR',NULL,NULL,NULL,0,0,3,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(54,1,'REQ','FIL',0,0,'PAT','CA',NULL,NULL,NULL,0,0,5,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(55,1,'REQ','FIL',0,0,'PAT','CN',NULL,NULL,NULL,0,0,3,0,0,'EXA',NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(56,1,'PAY','ALL',0,0,'PAT','CA',NULL,NULL,'Grant Fee',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(57,1,'PROD','PRI',0,0,'PAT',NULL,NULL,NULL,'Priority Docs',0,16,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(58,1,'PAY','FIL',0,0,'PAT','WO',NULL,NULL,'Filing Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(60,1,'REM','ALL',0,0,'PAT','US',NULL,NULL,'File divisional',0,1,0,0,0,NULL,'RSTR',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(61,1,'REP','EXA',0,0,'PAT','CN',NULL,NULL,'Exam Report',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(62,1,'REP','EXA',0,0,'PAT','CA',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(63,1,'REM','SR',0,0,'PAT','FR',NULL,NULL,'Request extension',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(64,1,'REM','EXA',0,0,'PAT','EP',NULL,NULL,'Request extension',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(65,1,'FBY','REC',0,0,'PAT',NULL,NULL,NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(66,1,'PAY','ALL',0,0,'PAT','FR',NULL,NULL,'Grant Fee',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(67,1,'REQ','PSR',0,0,'PAT','EP',NULL,NULL,NULL,0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(68,1,'PAY','PSR',0,0,'PAT','EP',NULL,NULL,'Designation Fees',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(69,1,'REP','PSR',0,0,'PAT','EP',NULL,NULL,'Written Opinion',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(70,1,'REQ','PRI',0,0,'PAT','IN',NULL,NULL,NULL,0,48,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(102,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'2',0,0,1,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(103,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'3',0,0,2,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(104,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'4',0,0,3,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(105,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'5',0,0,4,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(106,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'6',0,0,5,0,1,NULL,NULL,1,0,NULL,NULL,76.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(107,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'7',0,0,6,0,1,NULL,NULL,1,0,NULL,NULL,96.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(108,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'8',0,0,7,0,1,NULL,NULL,1,0,NULL,NULL,136.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(109,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'9',0,0,8,0,1,NULL,NULL,1,0,NULL,NULL,180.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(110,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'10',0,0,9,0,1,NULL,NULL,1,0,NULL,NULL,220.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(111,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'11',0,0,10,0,1,NULL,NULL,1,0,NULL,NULL,260.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(112,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'12',0,0,11,0,1,NULL,NULL,1,0,NULL,NULL,300.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(113,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'13',0,0,12,0,1,NULL,NULL,1,0,NULL,NULL,350.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(114,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'14',0,0,13,0,1,NULL,NULL,1,0,NULL,NULL,400.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(115,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'15',0,0,14,0,1,NULL,NULL,1,0,NULL,NULL,450.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(116,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'16',0,0,15,0,1,NULL,NULL,1,0,NULL,NULL,510.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(117,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'17',0,0,16,0,1,NULL,NULL,1,0,NULL,NULL,570.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(118,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'18',0,0,17,0,1,NULL,NULL,1,0,NULL,NULL,640.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(119,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'19',0,0,18,0,1,NULL,NULL,1,0,NULL,NULL,720.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(120,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'20',0,0,19,0,1,NULL,NULL,1,0,NULL,NULL,790.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(203,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'3',0,0,2,0,1,NULL,NULL,1,0,NULL,NULL,465.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(204,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'4',0,0,3,0,1,NULL,NULL,1,0,NULL,NULL,580.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(205,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'5',0,0,4,0,1,NULL,NULL,1,0,NULL,NULL,810.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(206,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'6',0,0,5,0,1,NULL,NULL,1,0,NULL,NULL,1040.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(207,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'7',0,0,6,0,1,NULL,NULL,1,0,NULL,NULL,1155.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(208,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'8',0,0,7,0,1,NULL,NULL,1,0,NULL,NULL,1265.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(209,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'9',0,0,8,0,1,NULL,NULL,1,0,NULL,NULL,1380.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(210,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'10',0,0,9,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(211,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'11',0,0,10,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(212,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'12',0,0,11,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(213,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'13',0,0,12,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(214,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'14',0,0,13,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(215,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'15',0,0,14,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(216,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'16',0,0,15,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(217,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'17',0,0,16,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(218,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'18',0,0,17,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(219,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'19',0,0,18,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(220,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'20',0,0,19,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(234,1,'PAY','ALL',0,0,'PAT','CN',NULL,NULL,'Grant Fee',76,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(235,1,'REP','SR',0,0,'PAT','WO',NULL,NULL,'Written Opinion',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(236,1,'PAY','ALL',0,0,'PAT','US',NULL,NULL,'Grant Fee',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(237,1,'PROD','GRT',0,0,'PAT','IN',NULL,NULL,'Working Report',0,2,0,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Change date to end of March',NULL,NULL,NULL,NULL),
(238,1,'WAT','PUB',0,0,'TM','FR',NULL,NULL,'Opposition deadline',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(239,1,'WAT','PUB',0,0,'TM','EM',NULL,NULL,'Opposition deadline',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(240,1,'WAT','PUB',0,0,'TM','US',NULL,NULL,'Opposition deadline',30,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(242,1,'PROD','REG',0,0,'TM','US',NULL,NULL,'Declaration of use',0,66,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'délai à 5 ans et demi',NULL,NULL,NULL,NULL),
(1001,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1002,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1003,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1004,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1005,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1011,1,'REN','REG',0,0,'TM','CA',NULL,NULL,'15',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1012,1,'REN','REG',0,0,'TM','CA',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1013,1,'REN','REG',0,0,'TM','CA',NULL,NULL,'45',0,0,45,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1081,1,'REN','REG',0,0,'TM','US',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1082,1,'REN','REG',0,0,'TM','US',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1083,1,'REN','REG',0,0,'TM','US',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1084,1,'REN','REG',0,0,'TM','US',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1085,1,'REN','REG',0,0,'TM','US',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1091,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1092,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1093,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1094,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1101,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1102,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1103,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1104,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1121,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1122,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1123,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1124,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1131,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1132,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1133,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1134,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1181,1,'PROD','FIL',0,0,'PAT','IN',NULL,NULL,'Annexure to Form 3',0,0,2,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1182,1,'PROD','FIL',0,0,'PAT','IN',NULL,NULL,'Declaration',0,0,2,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1183,1,'PROD','FIL',0,0,'PAT','IN',NULL,NULL,'Power',0,0,2,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1184,1,'PAY','ALL',0,0,'PAT','KR',NULL,NULL,'Grant Fee',0,3,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1185,1,'PAY','ALL',0,0,'PAT','TW',NULL,NULL,'Grant Fee',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1186,1,'REP','EXA',0,0,'PAT','AU',NULL,NULL,'Exam Report',0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1187,1,'REP','EXA',0,0,'PAT','IN',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1188,1,'REP','EXA',0,0,'PAT','KR',NULL,NULL,'Exam Report',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1189,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'1',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1190,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'2',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1191,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'3',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1192,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'4',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1193,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'5',0,0,25,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1194,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'1',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1195,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'2',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1196,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'3',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1197,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'4',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1198,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'5',0,0,25,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1211,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1212,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1213,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1214,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1215,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1216,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1217,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1218,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1219,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1220,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1221,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1222,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1223,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1224,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1225,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1230,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'10',0,11,9,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1231,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'20',0,11,19,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1232,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'30',0,11,29,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1233,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'40',0,11,39,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1234,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'50',0,11,49,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1235,1,'REN','FIL',0,0,'TM','SA',NULL,NULL,'10',0,8,9,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'10 ans Hegira',NULL,NULL,NULL,NULL),
(1237,1,'REP','EXA',0,0,'TM','US',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1238,1,'REP','EXA',0,0,'TM','KR',NULL,NULL,'Exam Report',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1239,1,'REP','COM',0,0,'TM','EM',NULL,NULL,'Irregularity',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1240,1,'REP','EXA',0,0,'TM','CN',NULL,NULL,'Exam Report',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1241,1,'PAY','ALL',0,0,'TM','CA',NULL,NULL,'Grant Fee',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1242,1,'PROD','ALL',0,0,'TM','CA',NULL,NULL,'Declaration of use',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1243,1,'WAT','PUB',0,0,'TM','BR',NULL,NULL,'Opposition deadline',60,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1244,1,'PROD','OPP',0,0,'TM','FR',NULL,NULL,'Observations',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1245,1,'PROD','ALL',0,0,'TM','US',NULL,NULL,'Statement of use',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1246,1,'REP','EXA',0,0,'TM','IL',NULL,NULL,'Exam Report',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1247,1,'PROD','EXA',0,0,'TM','US',NULL,NULL,'POA',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1248,1,'PROD','EXA',0,0,'TM','KR',NULL,NULL,'POA',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1249,1,'PROD','EXA',0,0,'TM','CN',NULL,NULL,'POA',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1250,1,'PROD','EXA',0,0,'TM','IL',NULL,NULL,'POA',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1251,1,'PROD','REF',0,0,'TM',NULL,NULL,NULL,'Appeal Brief',45,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1252,1,'PROD','REF',0,0,'TM',NULL,NULL,NULL,'POA',45,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1253,1,'REP','EXA',0,0,'TM','CA',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1254,1,'REP','EXA',0,0,'TM','TH',NULL,NULL,'Exam Report',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1258,1,'REP','EXAF',0,0,'PAT','US',NULL,NULL,'Final OA',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1259,1,'PROD','REG',0,0,'TM','US',NULL,NULL,'Declaration of use',0,114,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'délai à 9 ans et demi',NULL,NULL,NULL,NULL),
(1260,1,'REP','COM',0,0,'TM','WO',NULL,NULL,'Irregularity',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1262,1,'PROD','APL',0,0,'TM','EM',NULL,NULL,'Appeal Brief',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1263,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'10',0,0,10,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1267,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'20',0,0,20,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1268,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'30',0,0,30,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1269,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'40',0,0,40,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1270,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'50',0,0,50,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1271,1,'REP','COM',0,0,'TM','FR',NULL,NULL,'Irregularity',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1272,1,'REN','REG',0,0,'TM','LB',NULL,NULL,'15',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1273,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1274,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1275,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1277,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1278,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1280,1,'PROD','SOP',0,0,'PAT','EP',NULL,NULL,'Observations',10,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1281,1,'OPR','SOP',0,0,'PAT','EP',NULL,NULL,NULL,10,5,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1282,1,'PAY','ALL',0,0,'TM','JP',NULL,NULL,'2nd part of individual fee',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1290,1,'REN','FIL',0,0,'SO','FR',NULL,NULL,'Soleau',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1291,1,'WAT','FIL',0,0,'SO','FR',NULL,NULL,'End of protection',0,114,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1292,1,'EXP','FIL',0,0,'SO','FR',NULL,NULL,NULL,0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1299,1,'OPR','SOP',0,0,'OP',NULL,NULL,NULL,NULL,0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1300,1,'PROD','SOP',0,0,'OP',NULL,NULL,NULL,'Observations',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1301,1,'PROD','PRI',0,0,'PAT','US','WO',NULL,'Decl. and Assignment',0,30,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1302,1,'REQ','FIL',0,0,'PAT','BR',NULL,NULL,NULL,0,0,3,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1303,1,'FAP','ORE',0,0,'OP','EP',NULL,NULL,NULL,0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1305,1,'PRID','FIL',0,0,'DSG',NULL,NULL,NULL,NULL,0,6,0,0,0,'PRI',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Priority deadline is inserted only if no priority event exists',NULL,NULL,NULL,NULL),
(1306,1,'PRID','PRI',0,1,'DSG',NULL,NULL,NULL,'Delete',0,0,0,0,0,NULL,'FIL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Deletes priority deadline when a priority event is inserted',NULL,NULL,NULL,NULL),
(1307,1,'PRID','PRI',0,1,'TM',NULL,NULL,NULL,'Delete',0,0,0,0,0,NULL,'FIL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Deletes priority deadline when a priority event is inserted',NULL,NULL,NULL,NULL),
(1308,1,'REN','FIL',0,0,'DSG','WO',NULL,NULL,'1',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1309,1,'REN','FIL',0,0,'DSG','WO',NULL,NULL,'2',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1310,1,'PROD','REC',0,0,'OPI',NULL,NULL,NULL,'Opinion',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1311,1,'PROD','REC',0,0,'SR',NULL,NULL,NULL,'Report',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1315,1,'REP','EXA',0,0,'TM','JP',NULL,NULL,'Exam Report',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1316,1,'PROD','EXA',0,0,'TM','JP',NULL,NULL,'POA',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1321,1,'PROD','SR',0,0,'PAT','EP',NULL,NULL,'Analysis of SR',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1322,1,'PROD','DPAPL',0,0,'PAT','US',NULL,NULL,'Appeal Brief',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1323,1,'FRCE','EXAF',0,0,'PAT','US',NULL,NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1326,1,'FAP','EXAF',0,0,'PAT','US',NULL,NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1327,1,'FAP','APL',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1328,1,'PROD','REC',0,0,'TM',NULL,NULL,NULL,'Analyse CompuMark',15,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL),
(1329,1,'PROD','REC',0,0,'TM',NULL,NULL,NULL,'Libellé P/S',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `task_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `template_classes`
--

LOCK TABLES `template_classes` WRITE;
/*!40000 ALTER TABLE `template_classes` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_classes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `template_members`
--

LOCK TABLES `template_members` WRITE;
/*!40000 ALTER TABLE `template_members` DISABLE KEYS */;
/*!40000 ALTER TABLE `template_members` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-06-03 20:04:28
