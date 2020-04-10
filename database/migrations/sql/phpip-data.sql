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
INSERT INTO `actor` VALUES (1,'Client handled',NULL,'CLIENT',NULL,NULL,'ANN',NULL,NULL,NULL,NULL,0,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,0,'DO NOT DELETE - Special actor used for removing renewal tasks that are handled by the client',NULL,'phpip',NULL,NULL,NULL,NULL),
(2,'phpIP User',NULL,NULL,'phpipuser','$2y$10$auLQHQ3EIsg90hqnQsA1huhks3meaxwfWWEvJtD8R38jzwNN6y3zO',NULL,NULL,NULL,NULL,NULL,1,NULL,0,NULL,NULL,NULL,NULL,NULL,NULL,'root@localhost',NULL,NULL,NULL,0,NULL,NULL,'phpip',NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `actor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `actor_role`
--

LOCK TABLES `actor_role` WRITE;
/*!40000 ALTER TABLE `actor_role` DISABLE KEYS */;
INSERT INTO `actor_role` VALUES ('AGT','Primary Agent',20,0,1,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('AGT2','Secondary Agent',22,0,1,0,0,0,'Usually the primary agent\'s agent','phpip',NULL,NULL,NULL),
('ANN','Annuity Agent',21,0,1,0,0,0,'Agent in charge of renewals. -Client handled- is a special agent who, when added, will delete any renewals in the matter','phpip',NULL,NULL,NULL),
('APP','Applicant',3,1,1,0,0,0,'Assignee in the US, i.e. the owner upon filing','phpip',NULL,NULL,NULL),
('CLI','Client',1,1,1,0,1,0,'The client we take instructions from and who we invoice. DO NOT CHANGE OR DELETE: this is also a database user role','phpip',NULL,NULL,NULL),
('CNT','Contact',30,1,1,1,0,0,'Client\'s contact person','phpip',NULL,NULL,NULL),
('DEL','Delegate',31,1,0,0,0,0,'Another user allowed to manage the case','phpip',NULL,NULL,NULL),
('FAGT','Former Agent',23,0,1,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('FOWN','Former Owner',5,0,0,0,0,1,'To keep track of ownership history','phpip',NULL,NULL,NULL),
('INV','Inventor',10,1,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('LCN','Licensee',127,0,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('OFF','Patent Office',127,0,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('OPP','Opposing Party',127,0,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('OWN','Owner',4,0,1,0,1,1,'Use if different than applicant','phpip',NULL,NULL,NULL),
('PAY','Payor',2,1,0,0,1,0,'The actor who pays','phpip',NULL,NULL,NULL),
('PTNR','Partner',127,1,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('TRA','Translator',127,0,0,0,0,1,NULL,'phpip',NULL,NULL,NULL),
('WRI','Writer',127,1,0,0,0,0,'Person who follows the case','phpip',NULL,NULL,NULL);
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
INSERT INTO `classifier_type` VALUES ('ABS','Abstract',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('AGR','Agreement',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('BU','Business Unit',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('DESC','Description',1,NULL,1,NULL,'phpip',NULL,NULL,NULL),
('EVAL','Evaluation',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('IPC','IPC',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('KW','Keyword',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('LNK','Link',0,NULL,1,NULL,'phpip',NULL,NULL,NULL),
('LOC','Location',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('ORG','Organization',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('PA','Prior Art',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('PROD','Product',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('PROJ','Project',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('TECH','Technology',0,NULL,127,NULL,'phpip',NULL,NULL,NULL),
('TIT','Title',1,NULL,1,NULL,'phpip',NULL,NULL,NULL),
('TITAL','Alt. Title',1,'PAT',4,NULL,'phpip',NULL,NULL,NULL),
('TITEN','English Title',1,'PAT',3,NULL,'phpip',NULL,NULL,NULL),
('TITOF','Official Title',1,'PAT',2,NULL,'phpip',NULL,NULL,NULL),
('TM','Trademark',1,'TM',1,NULL,'phpip',NULL,NULL,NULL),
('TMCL','Class (TM)',0,'TM',2,NULL,'phpip',NULL,NULL,NULL),
('TMTYP','Type (TM)',0,'TM',3,NULL,'phpip',NULL,NULL,NULL);
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
INSERT INTO `country` VALUES (20,'AD','AND','Andorra','Andorra','Andorre',0,0,0,0),
(784,'AE','ARE','Vereinigte Arabische Emirate','United Arab Emirates','Émirats Arabes Unis',0,0,0,0),
(4,'AF','AFG','Afghanistan','Afghanistan','Afghanistan',0,0,0,0),
(28,'AG','ATG','Antigua und Barbuda','Antigua and Barbuda','Antigua-et-Barbuda',0,0,0,0),
(660,'AI','AIA','Anguilla','Anguilla','Anguilla',0,0,0,0),
(8,'AL','ALB','Albanien','Albania','Albanie',0,0,0,0),
(51,'AM','ARM','Armenien','Armenia','Arménie',0,0,0,0),
(530,'AN','ANT','Niederländische Antillen','Netherlands Antilles','Antilles Néerlandaises',0,0,0,0),
(24,'AO','AGO','Angola','Angola','Angola',0,0,0,0),
(10,'AQ','ATA','Antarktis','Antarctica','Antarctique',0,0,0,0),
(32,'AR','ARG','Argentinien','Argentina','Argentine',0,0,0,0),
(16,'AS','ASM','Amerikanisch-Samoa','American Samoa','Samoa Américaines',0,0,0,0),
(40,'AT','AUT','Österreich','Austria','Autriche',0,0,0,0),
(36,'AU','AUS','Australien','Australia','Australie',0,0,0,0),
(533,'AW','ABW','Aruba','Aruba','Aruba',0,0,0,0),
(248,'AX','ALA','Åland-Inseln','Åland Islands','Îles Åland',0,0,0,0),
(31,'AZ','AZE','Aserbaidschan','Azerbaijan','Azerbaïdjan',0,0,0,0),
(70,'BA','BIH','Bosnien und Herzegowina','Bosnia and Herzegovina','Bosnie-Herzégovine',0,0,0,0),
(52,'BB','BRB','Barbados','Barbados','Barbade',0,0,0,0),
(50,'BD','BGD','Bangladesch','Bangladesh','Bangladesh',0,0,0,0),
(56,'BE','BEL','Belgien','Belgium','Belgique',0,0,0,0),
(854,'BF','BFA','Burkina Faso','Burkina Faso','Burkina Faso',0,0,0,0),
(100,'BG','BGR','Bulgarien','Bulgaria','Bulgarie',0,0,0,0),
(48,'BH','BHR','Bahrain','Bahrain','Bahreïn',0,0,0,0),
(108,'BI','BDI','Burundi','Burundi','Burundi',0,0,0,0),
(204,'BJ','BEN','Benin','Benin','Bénin',0,0,0,0),
(60,'BM','BMU','Bermuda','Bermuda','Bermudes',0,0,0,0),
(96,'BN','BRN','Brunei Darussalam','Brunei Darussalam','Brunéi Darussalam',0,0,0,0),
(68,'BO','BOL','Bolivien','Bolivia','Bolivie',0,0,0,0),
(76,'BR','BRA','Brasilien','Brazil','Brésil',0,0,0,0),
(44,'BS','BHS','Bahamas','Bahamas','Bahamas',0,0,0,0),
(64,'BT','BTN','Bhutan','Bhutan','Bhoutan',0,0,0,0),
(74,'BV','BVT','Bouvetinsel','Bouvet Island','Île Bouvet',0,0,0,0),
(72,'BW','BWA','Botswana','Botswana','Botswana',0,0,0,0),
(0,'BX','BLX','Benelux','Benelux','Bénélux',0,0,0,0),
(112,'BY','BLR','Belarus','Belarus','Bélarus',0,0,0,0),
(84,'BZ','BLZ','Belize','Belize','Belize',0,0,0,0),
(124,'CA','CAN','Kanada','Canada','Canada',0,0,0,0),
(166,'CC','CCK','Kokosinseln','Cocos (Keeling) Islands','Îles Cocos (Keeling)',0,0,0,0),
(180,'CD','COD','Demokratische Republik Kongo','The Democratic Republic Of The Congo','République Démocratique du Congo',0,0,0,0),
(140,'CF','CAF','Zentralafrikanische Republik','Central African','République Centrafricaine',0,0,0,0),
(178,'CG','COG','Republik Kongo','Republic of the Congo','République du Congo',0,0,0,0),
(756,'CH','CHE','Schweiz','Switzerland','Suisse',0,0,0,0),
(384,'CI','CIV','Cote d\'Ivoire','Cote d\'Ivoire','Cote d\'Ivoire',0,0,0,0),
(184,'CK','COK','Cookinseln','Cook Islands','Îles Cook',0,0,0,0),
(152,'CL','CHL','Chile','Chile','Chili',0,0,0,0),
(120,'CM','CMR','Kamerun','Cameroon','Cameroun',0,0,0,0),
(156,'CN','CHN','China','China','Chine',0,1,0,0),
(170,'CO','COL','Kolumbien','Colombia','Colombie',0,0,0,0),
(188,'CR','CRI','Costa Rica','Costa Rica','Costa Rica',0,0,0,0),
(891,'CS','SCG','Serbien und Montenegro','Serbia and Montenegro','Serbie-et-Monténégro',0,0,0,0),
(192,'CU','CUB','Kuba','Cuba','Cuba',0,0,0,0),
(132,'CV','CPV','Kap Verde','Cape Verde','Cap-vert',0,0,0,0),
(162,'CX','CXR','Weihnachtsinsel','Christmas Island','Île Christmas',0,0,0,0),
(196,'CY','CYP','Zypern','Cyprus','Chypre',0,0,0,0),
(203,'CZ','CZE','Tschechische Republik','Czech Republic','République Tchèque',0,0,0,0),
(276,'DE','DEU','Deutschland','Germany','Allemagne',1,0,0,0),
(262,'DJ','DJI','Dschibuti','Djibouti','Djibouti',0,0,0,0),
(208,'DK','DNK','Dänemark','Denmark','Danemark',0,0,0,0),
(212,'DM','DMA','Dominica','Dominica','Dominique',0,0,0,0),
(214,'DO','DOM','Dominikanische Republik','Dominican Republic','République Dominicaine',0,0,0,0),
(12,'DZ','DZA','Algerien','Algeria','Algérie',0,0,0,0),
(218,'EC','ECU','Ecuador','Ecuador','Équateur',0,0,0,0),
(233,'EE','EST','Estland','Estonia','Estonie',0,0,0,0),
(818,'EG','EGY','Ägypten','Egypt','Égypte',0,0,0,0),
(732,'EH','ESH','Westsahara','Western Sahara','Sahara Occidental',0,0,0,0),
(0,'EM','EMA','EUIPO','EUIPO','Office de l’Union européenne pour la propriété intellectuelle',0,0,0,0),
(0,'EP','EPO','Europäische Patentorganisation','European Patent Organization','Organisation du Brevet Européen',0,1,0,0),
(232,'ER','ERI','Eritrea','Eritrea','Érythrée',0,0,0,0),
(724,'ES','ESP','Spanien','Spain','Espagne',0,0,0,0),
(231,'ET','ETH','Äthiopien','Ethiopia','Éthiopie',0,0,0,0),
(246,'FI','FIN','Finnland','Finland','Finlande',0,0,0,0),
(242,'FJ','FJI','Fidschi','Fiji','Fidji',0,0,0,0),
(238,'FK','FLK','Falklandinseln','Falkland Islands','Îles (malvinas) Falkland',0,0,0,0),
(583,'FM','FSM','Mikronesien','Federated States of Micronesia','États Fédérés de Micronésie',0,0,0,0),
(234,'FO','FRO','Färöer','Faroe Islands','Îles Féroé',0,0,0,0),
(250,'FR','FRA','Frankreich','France','France',1,0,0,0),
(266,'GA','GAB','Gabun','Gabon','Gabon',0,0,0,0),
(826,'GB','GBR','Vereinigtes Königreich von Großbritannien und Nordirland','United Kingdom','Royaume-Uni',1,0,0,0),
(308,'GD','GRD','Grenada','Grenada','Grenade',0,0,0,0),
(268,'GE','GEO','Georgien','Georgia','Géorgie',0,0,0,0),
(254,'GF','GUF','Französisch-Guayana','French Guiana','Guyane Française',0,0,0,0),
(288,'GH','GHA','Ghana','Ghana','Ghana',0,0,0,0),
(292,'GI','GIB','Gibraltar','Gibraltar','Gibraltar',0,0,0,0),
(304,'GL','GRL','Grönland','Greenland','Groenland',0,0,0,0),
(270,'GM','GMB','Gambia','Gambia','Gambie',0,0,0,0),
(324,'GN','GIN','Guinea','Guinea','Guinée',0,0,0,0),
(312,'GP','GLP','Guadeloupe','Guadeloupe','Guadeloupe',0,0,0,0),
(226,'GQ','GNQ','Äquatorialguinea','Equatorial Guinea','Guinée Équatoriale',0,0,0,0),
(300,'GR','GRC','Griechenland','Greece','Grèce',0,0,0,0),
(239,'GS','SGS','Südgeorgien und die Südlichen Sandwichinseln','South Georgia and the South Sandwich Islands','Géorgie du Sud et les Îles Sandwich du Sud',0,0,0,0),
(320,'GT','GTM','Guatemala','Guatemala','Guatemala',0,0,0,0),
(316,'GU','GUM','Guam','Guam','Guam',0,0,0,0),
(624,'GW','GNB','Guinea-Bissau','Guinea-Bissau','Guinée-Bissau',0,0,0,0),
(328,'GY','GUY','Guyana','Guyana','Guyana',0,0,0,0),
(344,'HK','HKG','Hongkong','Hong Kong','Hong-Kong',0,0,0,0),
(334,'HM','HMD','Heard und McDonaldinseln','Heard Island and McDonald Islands','Îles Heard et Mcdonald',0,0,0,0),
(340,'HN','HND','Honduras','Honduras','Honduras',0,0,0,0),
(191,'HR','HRV','Kroatien','Croatia','Croatie',0,0,0,0),
(332,'HT','HTI','Haiti','Haiti','Haïti',0,0,0,0),
(348,'HU','HUN','Ungarn','Hungary','Hongrie',0,0,0,0),
(0,'IB','IBU','NULL','International Bureau','Bureau International',0,0,0,0),
(360,'ID','IDN','Indonesien','Indonesia','Indonésie',0,0,0,0),
(372,'IE','IRL','Irland','Ireland','Irlande',0,0,0,0),
(376,'IL','ISR','Israel','Israel','Israël',0,0,0,0),
(833,'IM','IMN','Insel Man','Isle of Man','Île de Man',0,0,0,0),
(356,'IN','IND','Indien','India','Inde',0,1,0,0),
(86,'IO','IOT','Britisches Territorium im Indischen Ozean','British Indian Ocean Territory','Territoire Britannique de l\'Océan Indien',0,0,0,0),
(368,'IQ','IRQ','Irak','Iraq','Iraq',0,0,0,0),
(364,'IR','IRN','Islamische Republik Iran','Islamic Republic of Iran','République Islamique d\'Iran',0,0,0,0),
(352,'IS','ISL','Island','Iceland','Islande',0,0,0,0),
(380,'IT','ITA','Italien','Italy','Italie',1,0,0,0),
(388,'JM','JAM','Jamaika','Jamaica','Jamaïque',0,0,0,0),
(400,'JO','JOR','Jordanien','Jordan','Jordanie',0,0,0,0),
(392,'JP','JPN','Japan','Japan','Japon',0,1,0,0),
(404,'KE','KEN','Kenia','Kenya','Kenya',0,0,0,0),
(417,'KG','KGZ','Kirgisistan','Kyrgyzstan','Kirghizistan',0,0,0,0),
(116,'KH','KHM','Kambodscha','Cambodia','Cambodge',0,0,0,0),
(296,'KI','KIR','Kiribati','Kiribati','Kiribati',0,0,0,0),
(174,'KM','COM','Komoren','Comoros','Comores',0,0,0,0),
(659,'KN','KNA','St. Kitts und Nevis','Saint Kitts and Nevis','Saint-Kitts-et-Nevis',0,0,0,0),
(408,'KP','PRK','Demokratische Volksrepublik Korea','Democratic People\'s Republic of Korea','République Populaire Démocratique de Corée',0,0,0,0),
(410,'KR','KOR','Republik Korea','Republic of Korea','République de Corée',0,1,0,0),
(414,'KW','KWT','Kuwait','Kuwait','Koweït',0,0,0,0),
(136,'KY','CYM','Kaimaninseln','Cayman Islands','Îles Caïmanes',0,0,0,0),
(398,'KZ','KAZ','Kasachstan','Kazakhstan','Kazakhstan',0,0,0,0),
(418,'LA','LAO','Demokratische Volksrepublik Laos','Lao People\'s Democratic Republic','République Démocratique Populaire Lao',0,0,0,0),
(422,'LB','LBN','Libanon','Lebanon','Liban',0,0,0,0),
(662,'LC','LCA','St. Lucia','Saint Lucia','Sainte-Lucie',0,0,0,0),
(438,'LI','LIE','Liechtenstein','Liechtenstein','Liechtenstein',0,0,0,0),
(144,'LK','LKA','Sri Lanka','Sri Lanka','Sri Lanka',0,0,0,0),
(430,'LR','LBR','Liberia','Liberia','Libéria',0,0,0,0),
(426,'LS','LSO','Lesotho','Lesotho','Lesotho',0,0,0,0),
(440,'LT','LTU','Litauen','Lithuania','Lituanie',0,0,0,0),
(442,'LU','LUX','Luxemburg','Luxembourg','Luxembourg',0,0,0,0),
(428,'LV','LVA','Lettland','Latvia','Lettonie',0,0,0,0),
(434,'LY','LBY','Libysch-Arabische Dschamahirija','Libyan Arab Jamahiriya','Jamahiriya Arabe Libyenne',0,0,0,0),
(504,'MA','MAR','Marokko','Morocco','Maroc',0,0,0,0),
(492,'MC','MCO','Monaco','Monaco','Monaco',0,0,0,0),
(498,'MD','MDA','Moldawien','Republic of Moldova','République de Moldova',0,0,0,0),
(450,'MG','MDG','Madagaskar','Madagascar','Madagascar',0,0,0,0),
(584,'MH','MHL','Marshallinseln','Marshall Islands','Îles Marshall',0,0,0,0),
(807,'MK','MKD','Ehem. jugoslawische Republik Mazedonien','The Former Yugoslav Republic of Macedonia','L\'ex-République Yougoslave de Macédoine',0,0,0,0),
(466,'ML','MLI','Mali','Mali','Mali',0,0,0,0),
(104,'MM','MMR','Myanmar','Myanmar','Myanmar',0,0,0,0),
(496,'MN','MNG','Mongolei','Mongolia','Mongolie',0,0,0,0),
(446,'MO','MAC','Macao','Macao','Macao',0,0,0,0),
(580,'MP','MNP','Nördliche Marianen','Northern Mariana Islands','Îles Mariannes du Nord',0,0,0,0),
(474,'MQ','MTQ','Martinique','Martinique','Martinique',0,0,0,0),
(478,'MR','MRT','Mauretanien','Mauritania','Mauritanie',0,0,0,0),
(500,'MS','MSR','Montserrat','Montserrat','Montserrat',0,0,0,0),
(470,'MT','MLT','Malta','Malta','Malte',0,0,0,0),
(480,'MU','MUS','Mauritius','Mauritius','Maurice',0,0,0,0),
(462,'MV','MDV','Malediven','Maldives','Maldives',0,0,0,0),
(454,'MW','MWI','Malawi','Malawi','Malawi',0,0,0,0),
(484,'MX','MEX','Mexiko','Mexico','Mexique',0,0,0,0),
(458,'MY','MYS','Malaysia','Malaysia','Malaisie',0,0,0,0),
(508,'MZ','MOZ','Mosambik','Mozambique','Mozambique',0,0,0,0),
(516,'NA','NAM','Namibia','Namibia','Namibie',0,0,0,0),
(540,'NC','NCL','Neukaledonien','New Caledonia','Nouvelle-Calédonie',0,0,0,0),
(562,'NE','NER','Niger','Niger','Niger',0,0,0,0),
(574,'NF','NFK','Norfolkinsel','Norfolk Island','Île Norfolk',0,0,0,0),
(566,'NG','NGA','Nigeria','Nigeria','Nigéria',0,0,0,0),
(558,'NI','NIC','Nicaragua','Nicaragua','Nicaragua',0,0,0,0),
(528,'NL','NLD','Niederlande','Netherlands','Pays-Bas',0,0,0,0),
(578,'NO','NOR','Norwegen','Norway','Norvège',0,0,0,0),
(524,'NP','NPL','Nepal','Nepal','Népal',0,0,0,0),
(520,'NR','NRU','Nauru','Nauru','Nauru',0,0,0,0),
(570,'NU','NIU','Niue','Niue','Niué',0,0,0,0),
(554,'NZ','NZL','Neuseeland','New Zealand','Nouvelle-Zélande',0,0,0,0),
(0,'OA','AIP','Afrikanische Organisation für geistiges Eigentum','African Intellectual Property Organization','Organisation Africaine de la Propriété Intellectuelle',0,0,0,0),
(512,'OM','OMN','Oman','Oman','Oman',0,0,0,0),
(591,'PA','PAN','Panama','Panama','Panama',0,0,0,0),
(604,'PE','PER','Peru','Peru','Pérou',0,0,0,0),
(258,'PF','PYF','Französisch-Polynesien','French Polynesia','Polynésie Française',0,0,0,0),
(598,'PG','PNG','Papua-Neuguinea','Papua New Guinea','Papouasie-Nouvelle-Guinée',0,0,0,0),
(608,'PH','PHL','Philippinen','Philippines','Philippines',0,0,0,0),
(586,'PK','PAK','Pakistan','Pakistan','Pakistan',0,0,0,0),
(616,'PL','POL','Polen','Poland','Pologne',0,0,0,0),
(666,'PM','SPM','St. Pierre und Miquelon','Saint-Pierre and Miquelon','Saint-Pierre-et-Miquelon',0,0,0,0),
(612,'PN','PCN','Pitcairninseln','Pitcairn','Pitcairn',0,0,0,0),
(630,'PR','PRI','Puerto Rico','Puerto Rico','Porto Rico',0,0,0,0),
(275,'PS','PSE','Palästinensische Autonomiegebiete','Occupied Palestinian Territory','Territoire Palestinien Occupé',0,0,0,0),
(620,'PT','PRT','Portugal','Portugal','Portugal',0,0,0,0),
(585,'PW','PLW','Palau','Palau','Palaos',0,0,0,0),
(600,'PY','PRY','Paraguay','Paraguay','Paraguay',0,0,0,0),
(634,'QA','QAT','Katar','Qatar','Qatar',0,0,0,0),
(638,'RE','REU','Réunion','Réunion','Réunion',0,0,0,0),
(642,'RO','ROU','Rumänien','Romania','Roumanie',0,0,0,0),
(643,'RU','RUS','Russische Föderation','Russian Federation','Fédération de Russie',0,0,0,0),
(646,'RW','RWA','Ruanda','Rwanda','Rwanda',0,0,0,0),
(682,'SA','SAU','Saudi-Arabien','Saudi Arabia','Arabie Saoudite',0,0,0,0),
(90,'SB','SLB','Salomonen','Solomon Islands','Îles Salomon',0,0,0,0),
(690,'SC','SYC','Seychellen','Seychelles','Seychelles',0,0,0,0),
(736,'SD','SDN','Sudan','Sudan','Soudan',0,0,0,0),
(752,'SE','SWE','Schweden','Sweden','Suède',0,0,0,0),
(702,'SG','SGP','Singapur','Singapore','Singapour',0,0,0,0),
(654,'SH','SHN','St. Helena','Saint Helena','Sainte-Hélène',0,0,0,0),
(705,'SI','SVN','Slowenien','Slovenia','Slovénie',0,0,0,0),
(744,'SJ','SJM','Svalbard and Jan Mayen','Svalbard and Jan Mayen','Svalbard et Île Jan Mayen',0,0,0,0),
(703,'SK','SVK','Slowakei','Slovakia','Slovaquie',0,0,0,0),
(694,'SL','SLE','Sierra Leone','Sierra Leone','Sierra Leone',0,0,0,0),
(674,'SM','SMR','San Marino','San Marino','Saint-Marin',0,0,0,0),
(686,'SN','SEN','Senegal','Senegal','Sénégal',0,0,0,0),
(706,'SO','SOM','Somalia','Somalia','Somalie',0,0,0,0),
(740,'SR','SUR','Suriname','Suriname','Suriname',0,0,0,0),
(678,'ST','STP','São Tomé und Príncipe','Sao Tome and Principe','Sao Tomé-et-Principe',0,0,0,0),
(222,'SV','SLV','El Salvador','El Salvador','El Salvador',0,0,0,0),
(760,'SY','SYR','Arabische Republik Syrien','Syrian Arab Republic','République Arabe Syrienne',0,0,0,0),
(748,'SZ','SWZ','Swasiland','Swaziland','Swaziland',0,0,0,0),
(796,'TC','TCA','Turks- und Caicosinseln','Turks and Caicos Islands','Îles Turks et Caïques',0,0,0,0),
(148,'TD','TCD','Tschad','Chad','Tchad',0,0,0,0),
(260,'TF','ATF','Französische Süd- und Antarktisgebiete','French Southern Territories','Terres Australes Françaises',0,0,0,0),
(768,'TG','TGO','Togo','Togo','Togo',0,0,0,0),
(764,'TH','THA','Thailand','Thailand','Thaïlande',0,0,0,0),
(762,'TJ','TJK','Tadschikistan','Tajikistan','Tadjikistan',0,0,0,0),
(772,'TK','TKL','Tokelau','Tokelau','Tokelau',0,0,0,0),
(626,'TL','TLS','Timor-Leste','Timor-Leste','Timor-Leste',0,0,0,0),
(795,'TM','TKM','Turkmenistan','Turkmenistan','Turkménistan',0,0,0,0),
(788,'TN','TUN','Tunesien','Tunisia','Tunisie',0,0,0,0),
(776,'TO','TON','Tonga','Tonga','Tonga',0,0,0,0),
(792,'TR','TUR','Türkei','Turkey','Turquie',0,0,0,0),
(780,'TT','TTO','Trinidad und Tobago','Trinidad and Tobago','Trinité-et-Tobago',0,0,0,0),
(798,'TV','TUV','Tuvalu','Tuvalu','Tuvalu',0,0,0,0),
(158,'TW','TWN','Taiwan','Taiwan','Taïwan',0,0,0,0),
(834,'TZ','TZA','Vereinigte Republik Tansania','United Republic Of Tanzania','République-Unie de Tanzanie',0,0,0,0),
(804,'UA','UKR','Ukraine','Ukraine','Ukraine',0,0,0,0),
(800,'UG','UGA','Uganda','Uganda','Ouganda',0,0,0,0),
(581,'UM','UMI','Amerikanisch-Ozeanien','United States Minor Outlying Islands','Îles Mineures Éloignées des États-Unis',0,0,0,0),
(840,'US','USA','Vereinigte Staaten von Amerika','United States','États-Unis',0,1,0,0),
(858,'UY','URY','Uruguay','Uruguay','Uruguay',0,0,0,0),
(860,'UZ','UZB','Usbekistan','Uzbekistan','Ouzbékistan',0,0,0,0),
(336,'VA','VAT','Vatikanstadt','Vatican City State','Saint-Siège (état de la Cité du Vatican)',0,0,0,0),
(670,'VC','VCT','St. Vincent und die Grenadinen','Saint Vincent and the Grenadines','Saint-Vincent-et-les Grenadines',0,0,0,0),
(862,'VE','VEN','Venezuela','Venezuela','Venezuela',0,0,0,0),
(92,'VG','VGB','Britische Jungferninseln','British Virgin Islands','Îles Vierges Britanniques',0,0,0,0),
(850,'VI','VIR','Amerikanische Jungferninseln','U.S. Virgin Islands','Îles Vierges des États-Unis',0,0,0,0),
(704,'VN','VNM','Vietnam','Vietnam','Viet Nam',0,0,0,0),
(548,'VU','VUT','Vanuatu','Vanuatu','Vanuatu',0,0,0,0),
(876,'WF','WLF','Wallis und Futuna','Wallis and Futuna','Wallis et Futuna',0,0,0,0),
(0,'WO','PCT','Weltorganisation für geistiges Eigentum','World Intellectual Property Organization','Organisation Mondiale de la Propriété Intellectuelle',0,0,0,0),
(882,'WS','WSM','Samoa','Samoa','Samoa',0,0,0,0),
(887,'YE','YEM','Jemen','Yemen','Yémen',0,0,0,0),
(175,'YT','MYT','Mayotte','Mayotte','Mayotte',0,0,0,0),
(710,'ZA','ZAF','Südafrika','South Africa','Afrique du Sud',0,0,0,0),
(894,'ZM','ZMB','Sambia','Zambia','Zambie',0,0,0,0),
(716,'ZW','ZWE','Simbabwe','Zimbabwe','Zimbabwe',0,0,0,0);
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
-- Dumping data for table `event_name`
--

LOCK TABLES `event_name` WRITE;
/*!40000 ALTER TABLE `event_name` DISABLE KEYS */;
INSERT INTO `event_name` VALUES ('ABA','Abandoned',NULL,NULL,0,1,NULL,0,1,0,1,NULL,'phpip',NULL,NULL,NULL),
('ABO','Abandon Original','PAT','EP',1,0,NULL,0,1,0,0,'Abandon the originating patent that was re-designated in EP','phpip',NULL,NULL,NULL),
('ADV','Advisory Action','PAT','US',0,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('ALL','Allowance','PAT',NULL,0,1,NULL,0,0,0,0,'Use also for R71.3 in EP','phpip',NULL,NULL,NULL),
('APL','Appeal',NULL,NULL,0,1,NULL,1,0,0,0,'Appeal or other remedy filed','phpip',NULL,NULL,NULL),
('CAN','Cancelled','TM',NULL,0,1,NULL,0,0,0,1,NULL,'phpip',NULL,NULL,NULL),
('CLO','Closed','LTG',NULL,0,1,NULL,0,0,0,1,NULL,'phpip',NULL,NULL,NULL),
('COM','Communication',NULL,NULL,0,0,NULL,0,0,0,0,'Communication regarding administrative or formal matters (missing parts, irregularities...)','phpip',NULL,NULL,NULL),
('CRE','Created',NULL,NULL,0,0,NULL,0,1,0,0,'Creation date of matter - for attaching tasks necessary before anything else','phpip',NULL,NULL,NULL),
('DAPL','Decision on Appeal',NULL,NULL,0,0,NULL,0,0,0,0,'State outcome in detail field','phpip',NULL,NULL,NULL),
('DBY','Draft By','PAT',NULL,1,0,NULL,1,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('DEX','Deadline Extended',NULL,NULL,0,0,NULL,0,0,0,0,'Deadline extension requested','phpip',NULL,NULL,NULL),
('DPAPL','Decision on Pre-Appeal','PAT','US',0,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('DRA','Drafted','PAT',NULL,0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('DW','Deemed withrawn',NULL,NULL,0,1,NULL,0,0,0,0,'Decision needing a reply, such as further processing','phpip',NULL,NULL,NULL),
('EHK','Extend to Hong Kong','PAT','CN',1,0,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('ENT','Entered','PAT',NULL,0,0,NULL,0,1,0,0,'National entry date from PCT phase','phpip',NULL,NULL,NULL),
('EOP','End of Procedure','PAT',NULL,0,1,NULL,0,1,0,1,'Indicates end of international phase for PCT','phpip',NULL,NULL,NULL),
('EXA','Examiner Action',NULL,NULL,0,0,NULL,0,0,0,0,'AKA Office Action, i.e. anything related to substantive examination','phpip',NULL,NULL,NULL),
('EXAF','Examiner Action (Final)','PAT','US',0,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('EXP','Expiry',NULL,NULL,0,1,NULL,0,1,0,1,'Do not use nor change - present for internal functionality','phpip',NULL,NULL,NULL),
('FAP','File Notice of Appeal',NULL,NULL,1,0,NULL,1,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('FBY','File by',NULL,NULL,1,0,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('FDIV','File Divisional','PAT',NULL,1,0,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('FIL','Filed',NULL,NULL,0,1,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('FOP','File Opposition','OP','EP',1,0,NULL,1,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('FPR','Further Processing','PAT',NULL,1,0,NULL,1,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('FRCE','File RCE','PAT','US',1,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('GRT','Granted','PAT',NULL,0,1,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('INV','Invalidated','TM','US',0,1,NULL,0,0,0,1,NULL,'phpip',NULL,NULL,NULL),
('LAP','Lapsed',NULL,NULL,0,1,NULL,0,1,0,1,NULL,'phpip',NULL,NULL,NULL),
('NPH','National Phase','PAT','WO',1,0,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('OPP','Opposition',NULL,'EP',0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('OPR','Oral Proceedings','PAT','EP',1,0,NULL,1,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('ORE','Opposition rejected','PAT','EP',0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('PAY','Pay',NULL,NULL,1,0,NULL,0,0,0,0,'Use for any fees to be paid','phpip',NULL,NULL,NULL),
('PDES','Post designation','TM','WO',0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('PFIL','Parent Filed','PAT',NULL,0,1,NULL,0,1,0,0,'Filing date of the parent (use only when the matter type is defined). Use as link to the parent matter.','phpip',NULL,NULL,NULL),
('PR','Publication of Reg.','TM',NULL,0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('PREP','Prepare',NULL,NULL,1,0,NULL,1,0,0,0,'Any further action to be done by the responsible (comments, pre-handling, ...)','phpip',NULL,NULL,NULL),
('PRI','Priority Claim',NULL,NULL,0,1,NULL,0,0,0,0,'Use as link to the priority matter','phpip',NULL,NULL,NULL),
('PRID','Priority Deadline',NULL,NULL,1,0,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('PROD','Produce',NULL,NULL,1,0,NULL,0,0,0,0,'Any further documents to be filed (inventor designation, priority document, missing parts...)','phpip',NULL,NULL,NULL),
('PSR','Publication of SR','PAT','EP',0,0,NULL,0,1,0,0,'A3 publication','phpip',NULL,NULL,NULL),
('PUB','Published',NULL,NULL,0,1,NULL,0,0,0,0,'For EP, this means publication WITH the search report (A1 publ.)','phpip',NULL,NULL,NULL),
('RCE','Request Continued Examination','PAT','US',0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('REC','Received',NULL,NULL,0,1,NULL,0,1,0,0,'Date the case was received from the client','phpip',NULL,NULL,NULL),
('REF','Refused',NULL,NULL,0,1,NULL,0,0,0,0,'This is the final decision, that can only be appealed - do not mistake with an exam report','phpip',NULL,NULL,NULL),
('REG','Registration','TM',NULL,0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('REM','Reminder',NULL,NULL,1,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('REN','Renewal',NULL,NULL,1,0,NULL,0,0,0,0,'AKA Annuity','phpip',NULL,NULL,NULL),
('REP','Respond',NULL,NULL,1,0,NULL,1,0,0,0,'Use for any response','phpip',NULL,NULL,NULL),
('REQ','Request Examination',NULL,NULL,1,0,NULL,0,1,0,0,NULL,'phpip',NULL,NULL,NULL),
('RSTR','Restriction Req.','PAT','US',0,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('SOL','Sold',NULL,NULL,0,1,NULL,0,0,0,1,NULL,'phpip',NULL,NULL,NULL),
('SOP','Summons to Oral Proc.',NULL,NULL,0,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('SR','Search Report',NULL,NULL,0,0,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('SUS','Suspended',NULL,NULL,0,1,NULL,0,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('TRF','Transferred',NULL,NULL,0,1,NULL,0,1,0,1,'Case no longer followed','phpip',NULL,NULL,NULL),
('VAL','Validate','PAT','EP',1,0,NULL,0,1,0,0,'Validate granted EP in designated countries','phpip',NULL,NULL,NULL),
('WAT','Watch',NULL,NULL,1,0,NULL,1,0,0,0,NULL,'phpip',NULL,NULL,NULL),
('WIT','Withdrawal','PAT',NULL,0,1,NULL,0,0,0,1,NULL,'phpip',NULL,NULL,NULL);
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
INSERT INTO `matter_category` VALUES ('AGR','AGR','Agreement','OTH','phpip',NULL,NULL,NULL),
('DSG','DSG','Design','TM','phpip',NULL,NULL,NULL),
('FTO','OPI','Freedom to Operate','LTG','phpip',NULL,NULL,NULL),
('LTG','LTG','Litigation','LTG','phpip',NULL,NULL,NULL),
('OP','OPP','Opposition (patent)','LTG','phpip',NULL,NULL,NULL),
('OPI','OPI','Opinion','LTG','phpip',NULL,NULL,NULL),
('OTH','OTH','Others','OTH','phpip',NULL,NULL,NULL),
('PAT','PAT','Patent','PAT','phpip',NULL,NULL,NULL),
('PRO','PAT','Provisional Application','PAT','phpip',NULL,NULL,NULL),
('SO','PAT','Soleau Envelop','PAT','phpip',NULL,NULL,NULL),
('SR','SR-','Search','LTG','phpip',NULL,NULL,NULL),
('TM','TM-','Trademark','TM','phpip',NULL,NULL,NULL),
('TMOP','TOP','Opposition (TM)','TM','phpip',NULL,NULL,NULL),
('TS','TS-','Trade Secret','PAT','phpip',NULL,NULL,NULL),
('UC','PAT','Utility Certificate','PAT','phpip',NULL,NULL,NULL),
('UM','PAT','Utility Model','PAT','phpip',NULL,NULL,NULL),
('WAT','WAT','Watch','TM','phpip',NULL,NULL,NULL);
/*!40000 ALTER TABLE `matter_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `matter_type`
--

LOCK TABLES `matter_type` WRITE;
/*!40000 ALTER TABLE `matter_type` DISABLE KEYS */;
INSERT INTO `matter_type` VALUES ('CIP','Continuation in Part','phpip',NULL,NULL,NULL),
('CNT','Continuation','phpip',NULL,NULL,NULL),
('DIV','Divisional','phpip',NULL,NULL,NULL),
('REI','Reissue','phpip',NULL,NULL,NULL),
('REX','Re-examination','phpip',NULL,NULL,NULL);
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
(33,'2019_08_19_000000_create_failed_jobs_table',1),
(34,'2019_08_13_145446_update_tables',2),
(35,'2019_11_13_135330_update_tables2',3),
(36,'2019_11_17_025422_update_tables3',4),
(37,'2019_11_18_002207_update_tables4',5),
(38,'2019_11_25_123348_update_tables5',6);
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
INSERT INTO `task_rules` VALUES (1,1,'PRID','FIL',0,0,'PAT',NULL,NULL,NULL,NULL,0,12,0,0,0,'PRI',NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,'Priority deadline is inserted only if no priority event exists','phpip',NULL,NULL,NULL),
(2,1,'PRID','FIL',0,0,'TM',NULL,NULL,NULL,NULL,0,6,0,0,0,'PRI',NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'Priority deadline is inserted only if no priority event exists','phpip',NULL,NULL,NULL),
(3,1,'FBY','FIL',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'Clear \"File by\" task when \"Filed\" event is created','phpip',NULL,NULL,NULL),
(4,1,'PRID','FIL',0,0,'PRO',NULL,NULL,NULL,NULL,0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'phpip',NULL,NULL,NULL),
(5,1,'DBY','DRA',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,NULL,NULL,'Clear \"Draft by\" task when \"Drafted\" event is created','phpip',NULL,NULL,NULL),
(6,1,'REQ','FIL',0,0,'PAT','JP',NULL,NULL,NULL,0,0,3,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(7,1,'REQ','PUB',0,0,'PAT','EP',NULL,NULL,NULL,0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(8,1,'EXP','FIL',0,0,'PRO',NULL,NULL,NULL,NULL,0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(9,1,'REP','SR',0,0,'PAT','FR',NULL,NULL,'Search Report',0,3,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(10,1,'REP','EXA',0,0,'PAT',NULL,NULL,NULL,'Exam Report',0,3,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(11,1,'REP','EXA',0,0,'PAT','EP',NULL,NULL,'Exam Report',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(12,1,'EXP','FIL',0,0,'PAT',NULL,NULL,NULL,NULL,0,0,20,0,0,NULL,NULL,1,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(13,1,'REP','ALL',0,0,'PAT','EP',NULL,NULL,'R71(3)',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(14,1,'PAY','ALL',0,0,'PAT','EP',NULL,NULL,'Grant Fee',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(15,1,'PROD','ALL',0,0,'PAT','EP',NULL,NULL,'Claim Translation',0,4,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(16,1,'VAL','GRT',0,0,'PAT','EP',NULL,NULL,'Translate where necessary',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(18,1,'REP','PUB',0,0,'PAT','EP',NULL,NULL,'Written Opinion',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(19,1,'PAY','PUB',0,0,'PAT','EP',NULL,NULL,'Designation Fees',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(20,1,'PROD','PRI',0,0,'PAT','US',NULL,NULL,'Decl. and Assignment',0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(21,1,'FBY','PRI',0,0,'PAT',NULL,NULL,NULL,'Priority Deadline',0,12,0,0,0,'FIL',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(22,1,'NPH','FIL',0,0,'PAT','WO',NULL,NULL,NULL,0,30,0,0,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(23,1,'REQ','FIL',0,0,'PAT','WO',NULL,NULL,NULL,0,22,0,0,0,'EXA',NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(24,1,'DBY','REC',0,0,'PAT',NULL,NULL,NULL,NULL,0,2,0,0,0,'PRI',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(25,1,'PRID','PRI',0,1,'PAT',NULL,NULL,NULL,'Delete',0,0,0,0,0,NULL,'FIL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Deletes priority deadline when a priority event is inserted','phpip',NULL,NULL,NULL),
(26,1,'EHK','PUB',0,0,'PAT','CN',NULL,NULL,NULL,0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(27,1,'FOP','GRT',0,0,'OP','EP',NULL,NULL,NULL,0,9,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(29,1,'DBY','FIL',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(30,1,'PROD','FIL',0,0,'PAT','US',NULL,NULL,'IDS',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(33,1,'EXP','FIL',0,0,'PAT','WO',NULL,NULL,NULL,0,31,0,0,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(34,1,'REM','FIL',0,0,'PAT','WO',NULL,NULL,'National Phase',0,27,0,0,0,NULL,NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(35,1,'PROD','FIL',0,0,'PAT','FR',NULL,NULL,'Small Entity',0,1,0,0,0,'PRI',NULL,1,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(36,1,'PAY','GRT',0,0,'PAT','CN',NULL,NULL,'HK Grant Fee',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(37,1,'REP','COM',0,0,'PAT',NULL,NULL,NULL,'Communication',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(38,1,'FOP','OPP',1,0,'OP','EP',NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Clear \"File Opposition\" task when \"Opposition\" event is created','phpip',NULL,NULL,NULL),
(39,1,'PAY','ALL',0,0,'PAT','JP',NULL,NULL,'Grant Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(40,1,'FPR','DW',0,0,'PAT','EP',NULL,NULL,NULL,0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(41,1,'REP','PSR',0,0,'PAT','EP',NULL,NULL,'R70(2)',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(42,1,'NPH','PRI',0,0,'PAT',NULL,'WO',NULL,NULL,0,30,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(43,1,'PAY','FIL',0,0,'PAT','FR',NULL,NULL,'Filing Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(44,1,'PAY','FIL',0,0,'PAT','EP',NULL,NULL,'Filing Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(45,1,'VAL','GRT',0,0,'PAT',NULL,'EP',NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(46,1,'REP','RSTR',0,0,'PAT','US',NULL,NULL,'Restriction Req.',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(47,1,'REP','COM',0,0,'PAT','EP',NULL,NULL,'R161',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(48,1,'FAP','REF',0,0,'PAT',NULL,NULL,NULL,NULL,0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(49,1,'PROD','APL',0,0,'PAT',NULL,NULL,NULL,'Appeal Brief',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(52,1,'REP','COM',0,0,'OP','EP',NULL,NULL,'Observations',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(53,1,'REQ','FIL',0,0,'PAT','KR',NULL,NULL,NULL,0,0,3,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(54,1,'REQ','FIL',0,0,'PAT','CA',NULL,NULL,NULL,0,0,5,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(55,1,'REQ','FIL',0,0,'PAT','CN',NULL,NULL,NULL,0,0,3,0,0,'EXA',NULL,0,1,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(56,1,'PAY','ALL',0,0,'PAT','CA',NULL,NULL,'Grant Fee',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(57,1,'PROD','PRI',0,0,'PAT',NULL,NULL,NULL,'Priority Docs',0,16,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(58,1,'PAY','FIL',0,0,'PAT','WO',NULL,NULL,'Filing Fee',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(60,1,'REM','ALL',0,0,'PAT','US',NULL,NULL,'File divisional',0,1,0,0,0,NULL,'RSTR',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(61,1,'REP','EXA',0,0,'PAT','CN',NULL,NULL,'Exam Report',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(62,1,'REP','EXA',0,0,'PAT','CA',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(63,1,'REM','SR',0,0,'PAT','FR',NULL,NULL,'Request extension',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(64,1,'REM','EXA',0,0,'PAT','EP',NULL,NULL,'Request extension',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(65,1,'FBY','REC',0,0,'PAT',NULL,NULL,NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(66,1,'PAY','ALL',0,0,'PAT','FR',NULL,NULL,'Grant Fee',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(67,1,'REQ','PSR',0,0,'PAT','EP',NULL,NULL,NULL,0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(68,1,'PAY','PSR',0,0,'PAT','EP',NULL,NULL,'Designation Fees',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(69,1,'REP','PSR',0,0,'PAT','EP',NULL,NULL,'Written Opinion',0,6,0,0,0,'EXA',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(70,1,'REQ','PRI',0,0,'PAT','IN',NULL,NULL,NULL,0,48,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(102,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'2',0,0,1,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(103,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'3',0,0,2,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(104,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'4',0,0,3,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(105,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'5',0,0,4,0,1,NULL,NULL,1,0,NULL,NULL,38.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(106,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'6',0,0,5,0,1,NULL,NULL,1,0,NULL,NULL,76.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(107,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'7',0,0,6,0,1,NULL,NULL,1,0,NULL,NULL,96.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(108,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'8',0,0,7,0,1,NULL,NULL,1,0,NULL,NULL,136.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(109,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'9',0,0,8,0,1,NULL,NULL,1,0,NULL,NULL,180.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(110,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'10',0,0,9,0,1,NULL,NULL,1,0,NULL,NULL,220.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(111,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'11',0,0,10,0,1,NULL,NULL,1,0,NULL,NULL,260.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(112,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'12',0,0,11,0,1,NULL,NULL,1,0,NULL,NULL,300.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(113,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'13',0,0,12,0,1,NULL,NULL,1,0,NULL,NULL,350.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(114,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'14',0,0,13,0,1,NULL,NULL,1,0,NULL,NULL,400.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(115,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'15',0,0,14,0,1,NULL,NULL,1,0,NULL,NULL,450.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(116,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'16',0,0,15,0,1,NULL,NULL,1,0,NULL,NULL,510.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(117,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'17',0,0,16,0,1,NULL,NULL,1,0,NULL,NULL,570.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(118,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'18',0,0,17,0,1,NULL,NULL,1,0,NULL,NULL,640.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(119,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'19',0,0,18,0,1,NULL,NULL,1,0,NULL,NULL,720.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(120,1,'REN','FIL',0,0,'PAT','FR',NULL,NULL,'20',0,0,19,0,1,NULL,NULL,1,0,NULL,NULL,790.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(203,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'3',0,0,2,0,1,NULL,NULL,1,0,NULL,NULL,465.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(204,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'4',0,0,3,0,1,NULL,NULL,1,0,NULL,NULL,580.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(205,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'5',0,0,4,0,1,NULL,NULL,1,0,NULL,NULL,810.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(206,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'6',0,0,5,0,1,NULL,NULL,1,0,NULL,NULL,1040.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(207,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'7',0,0,6,0,1,NULL,NULL,1,0,NULL,NULL,1155.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(208,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'8',0,0,7,0,1,NULL,NULL,1,0,NULL,NULL,1265.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(209,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'9',0,0,8,0,1,NULL,NULL,1,0,NULL,NULL,1380.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(210,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'10',0,0,9,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(211,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'11',0,0,10,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(212,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'12',0,0,11,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(213,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'13',0,0,12,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(214,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'14',0,0,13,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(215,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'15',0,0,14,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(216,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'16',0,0,15,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(217,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'17',0,0,16,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(218,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'18',0,0,17,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(219,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'19',0,0,18,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(220,1,'REN','FIL',0,0,'PAT','EP',NULL,NULL,'20',0,0,19,0,1,NULL,NULL,1,0,NULL,NULL,1560.00,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(234,1,'PAY','ALL',0,0,'PAT','CN',NULL,NULL,'Grant Fee',76,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(235,1,'REP','SR',0,0,'PAT','WO',NULL,NULL,'Written Opinion',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(236,1,'PAY','ALL',0,0,'PAT','US',NULL,NULL,'Grant Fee',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(237,1,'PROD','GRT',0,0,'PAT','IN',NULL,NULL,'Working Report',0,2,0,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Change date to end of March','phpip',NULL,NULL,NULL),
(238,1,'WAT','PUB',0,0,'TM','FR',NULL,NULL,'Opposition deadline',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(239,1,'WAT','PUB',0,0,'TM','EM',NULL,NULL,'Opposition deadline',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(240,1,'WAT','PUB',0,0,'TM','US',NULL,NULL,'Opposition deadline',30,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(242,1,'PROD','REG',0,0,'TM','US',NULL,NULL,'Declaration of use',0,66,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'délai à 5 ans et demi','phpip',NULL,NULL,NULL),
(1001,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1002,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1003,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1004,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1005,1,'REN','FIL',0,0,'TM',NULL,NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1011,1,'REN','REG',0,0,'TM','CA',NULL,NULL,'15',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1012,1,'REN','REG',0,0,'TM','CA',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1013,1,'REN','REG',0,0,'TM','CA',NULL,NULL,'45',0,0,45,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1081,1,'REN','REG',0,0,'TM','US',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1082,1,'REN','REG',0,0,'TM','US',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1083,1,'REN','REG',0,0,'TM','US',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1084,1,'REN','REG',0,0,'TM','US',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1085,1,'REN','REG',0,0,'TM','US',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1091,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1092,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1093,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1094,1,'REN','REG',0,0,'TM','JP',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1101,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1102,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1103,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1104,1,'REN','REG',0,0,'TM','KR',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1121,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1122,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1123,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1124,1,'REN','REG',0,0,'TM','BR',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1131,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1132,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1133,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1134,1,'REN','REG',0,0,'TM','CN',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1181,1,'PROD','FIL',0,0,'PAT','IN',NULL,NULL,'Annexure to Form 3',0,0,2,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1182,1,'PROD','FIL',0,0,'PAT','IN',NULL,NULL,'Declaration',0,0,2,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1183,1,'PROD','FIL',0,0,'PAT','IN',NULL,NULL,'Power',0,0,2,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1184,1,'PAY','ALL',0,0,'PAT','KR',NULL,NULL,'Grant Fee',0,3,0,0,0,'GRT',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1185,1,'PAY','ALL',0,0,'PAT','TW',NULL,NULL,'Grant Fee',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1186,1,'REP','EXA',0,0,'PAT','AU',NULL,NULL,'Exam Report',0,12,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1187,1,'REP','EXA',0,0,'PAT','IN',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1188,1,'REP','EXA',0,0,'PAT','KR',NULL,NULL,'Exam Report',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1189,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'1',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1190,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'2',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1191,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'3',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1192,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'4',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1193,1,'REN','FIL',0,0,'DSG','FR',NULL,NULL,'5',0,0,25,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1194,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'1',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1195,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'2',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1196,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'3',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1197,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'4',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1198,1,'REN','FIL',0,0,'DSG','EM',NULL,NULL,'5',0,0,25,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1211,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1212,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1213,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1214,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1215,1,'REN','REG',0,0,'TM','DK',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1216,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1217,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1218,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1219,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1220,1,'REN','REG',0,0,'TM','NO',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1221,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1222,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1223,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1224,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1225,1,'REN','REG',0,0,'TM','FI',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1230,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'10',0,11,9,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1231,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'20',0,11,19,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1232,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'30',0,11,29,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1233,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'40',0,11,39,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1234,1,'REN','PR',0,0,'TM','TW',NULL,NULL,'50',0,11,49,0,1,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1235,1,'REN','FIL',0,0,'TM','SA',NULL,NULL,'10',0,8,9,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'10 ans Hegira','phpip',NULL,NULL,NULL),
(1237,1,'REP','EXA',0,0,'TM','US',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1238,1,'REP','EXA',0,0,'TM','KR',NULL,NULL,'Exam Report',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1239,1,'REP','COM',0,0,'TM','EM',NULL,NULL,'Irregularity',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1240,1,'REP','EXA',0,0,'TM','CN',NULL,NULL,'Exam Report',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1241,1,'PAY','ALL',0,0,'TM','CA',NULL,NULL,'Grant Fee',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1242,1,'PROD','ALL',0,0,'TM','CA',NULL,NULL,'Declaration of use',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1243,1,'WAT','PUB',0,0,'TM','BR',NULL,NULL,'Opposition deadline',60,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1244,1,'PROD','OPP',0,0,'TM','FR',NULL,NULL,'Observations',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1245,1,'PROD','ALL',0,0,'TM','US',NULL,NULL,'Statement of use',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1246,1,'REP','EXA',0,0,'TM','IL',NULL,NULL,'Exam Report',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1247,1,'PROD','EXA',0,0,'TM','US',NULL,NULL,'POA',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1248,1,'PROD','EXA',0,0,'TM','KR',NULL,NULL,'POA',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1249,1,'PROD','EXA',0,0,'TM','CN',NULL,NULL,'POA',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1250,1,'PROD','EXA',0,0,'TM','IL',NULL,NULL,'POA',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1251,1,'PROD','REF',0,0,'TM',NULL,NULL,NULL,'Appeal Brief',45,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1252,1,'PROD','REF',0,0,'TM',NULL,NULL,NULL,'POA',45,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1253,1,'REP','EXA',0,0,'TM','CA',NULL,NULL,'Exam Report',0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1254,1,'REP','EXA',0,0,'TM','TH',NULL,NULL,'Exam Report',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1258,1,'REP','EXAF',0,0,'PAT','US',NULL,NULL,'Final OA',0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1259,1,'PROD','REG',0,0,'TM','US',NULL,NULL,'Declaration of use',0,114,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'délai à 9 ans et demi','phpip',NULL,NULL,NULL),
(1260,1,'REP','COM',0,0,'TM','WO',NULL,NULL,'Irregularity',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1262,1,'PROD','APL',0,0,'TM','EM',NULL,NULL,'Appeal Brief',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1263,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'10',0,0,10,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1267,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'20',0,0,20,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1268,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'30',0,0,30,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1269,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'40',0,0,40,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1270,1,'REN','PRI',0,0,'TM','NZ',NULL,NULL,'50',0,0,50,0,0,NULL,'ALL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1271,1,'REP','COM',0,0,'TM','FR',NULL,NULL,'Irregularity',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1272,1,'REN','REG',0,0,'TM','LB',NULL,NULL,'15',0,0,15,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1273,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'10',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1274,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'20',0,0,20,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1275,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'30',0,0,30,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1277,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'40',0,0,40,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1278,1,'REN','PRI',0,0,'TM','RU',NULL,NULL,'50',0,0,50,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1280,1,'PROD','SOP',0,0,'PAT','EP',NULL,NULL,'Observations',10,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1281,1,'OPR','SOP',0,0,'PAT','EP',NULL,NULL,NULL,10,5,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1282,1,'PAY','ALL',0,0,'TM','JP',NULL,NULL,'2nd part of individual fee',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1290,1,'REN','FIL',0,0,'SO','FR',NULL,NULL,'Soleau',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1291,1,'WAT','FIL',0,0,'SO','FR',NULL,NULL,'End of protection',0,114,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1292,1,'EXP','FIL',0,0,'SO','FR',NULL,NULL,NULL,0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1299,1,'OPR','SOP',0,0,'OP',NULL,NULL,NULL,NULL,0,6,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1300,1,'PROD','SOP',0,0,'OP',NULL,NULL,NULL,'Observations',0,4,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1301,1,'PROD','PRI',0,0,'PAT','US','WO',NULL,'Decl. and Assignment',0,30,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1302,1,'REQ','FIL',0,0,'PAT','BR',NULL,NULL,NULL,0,0,3,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1303,1,'FAP','ORE',0,0,'OP','EP',NULL,NULL,NULL,0,2,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1305,1,'PRID','FIL',0,0,'DSG',NULL,NULL,NULL,NULL,0,6,0,0,0,'PRI',NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Priority deadline is inserted only if no priority event exists','phpip',NULL,NULL,NULL),
(1306,1,'PRID','PRI',0,1,'DSG',NULL,NULL,NULL,'Delete',0,0,0,0,0,NULL,'FIL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Deletes priority deadline when a priority event is inserted','phpip',NULL,NULL,NULL),
(1307,1,'PRID','PRI',0,1,'TM',NULL,NULL,NULL,'Delete',0,0,0,0,0,NULL,'FIL',0,0,NULL,NULL,NULL,NULL,'EUR',NULL,'Deletes priority deadline when a priority event is inserted','phpip',NULL,NULL,NULL),
(1308,1,'REN','FIL',0,0,'DSG','WO',NULL,NULL,'1',0,0,5,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1309,1,'REN','FIL',0,0,'DSG','WO',NULL,NULL,'2',0,0,10,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1310,1,'PROD','REC',0,0,'OPI',NULL,NULL,NULL,'Opinion',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1311,1,'PROD','REC',0,0,'SR',NULL,NULL,NULL,'Report',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1315,1,'REP','EXA',0,0,'TM','JP',NULL,NULL,'Exam Report',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1316,1,'PROD','EXA',0,0,'TM','JP',NULL,NULL,'POA',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1321,1,'PROD','SR',0,0,'PAT','EP',NULL,NULL,'Analysis of SR',0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1322,1,'PROD','DPAPL',0,0,'PAT','US',NULL,NULL,'Appeal Brief',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1323,1,'FRCE','EXAF',0,0,'PAT','US',NULL,NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1326,1,'FAP','EXAF',0,0,'PAT','US',NULL,NULL,NULL,0,3,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1327,1,'FAP','APL',1,0,'PAT',NULL,NULL,NULL,'Clear',0,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1328,1,'PROD','REC',0,0,'TM',NULL,NULL,NULL,'Analyse CompuMark',15,0,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL),
(1329,1,'PROD','REC',0,0,'TM',NULL,NULL,NULL,'Libellé P/S',0,1,0,0,0,NULL,NULL,0,0,NULL,NULL,NULL,NULL,'EUR',NULL,NULL,'phpip',NULL,NULL,NULL);
/*!40000 ALTER TABLE `task_rules` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-04-10 14:21:00
