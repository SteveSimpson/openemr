--
-- Table structure for table `form_init_clinical_assessment`
--

CREATE TABLE IF NOT EXISTS `form_init_clinical_assessment` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` datetime DEFAULT NULL,
  `pid` bigint(20) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `groupname` varchar(255) DEFAULT NULL,
  `authorized` tinyint(4) DEFAULT NULL,
  `activity` tinyint(4) DEFAULT NULL,
  `client_name` varchar(255) DEFAULT NULL,
  `client_number` bigint(20) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `admit_date` varchar(255) DEFAULT NULL,
  `goal` text,
  `intervention` text,
  `progress` text,
  `plan` text,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;
