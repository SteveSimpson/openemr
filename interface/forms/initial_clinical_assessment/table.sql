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
  `presenting_problem` text,
  `medical_issues` text,
  `sleep` text,
  `interests` text,
  `guilt_concerns` text,
  `energy` text,
  `concentration` text,
  `appetite` text,
  `psychomotor` text,
  `suicidal_ideation` text,
  `education` text,
  `family_history` text,
  `trauma_history` text,  
  `past_psych_history` text,
  `substance_use` text,
  `current_coping_mechanisms` text,
  `spirituality_support_systems` text,
  `client_goal` text,
  `interpretive_summary` text,
  `dsm_v_criteria` text,
  `signature` text,
   PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;


CREATE TABLE IF NOT EXISTS `form_init_clinical_assessment_signature` (
  `id` bigint(20) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `signed_text` MEDIUMTEXT,
   PRIMARY KEY (`id`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;