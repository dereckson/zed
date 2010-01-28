SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Structure de la table `zed_comments`
--

CREATE TABLE IF NOT EXISTS `zed_comments` (
  `comment_ref` varchar(8) NOT NULL DEFAULT '',
  `comment_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(4) DEFAULT NULL,
  `comment_title` varchar(32) NOT NULL DEFAULT '',
  `comment_parent` smallint(6) DEFAULT NULL,
  `comment_text` longtext NOT NULL,
  `comment_date` varchar(10) NOT NULL DEFAULT '',
  `comment_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`comment_id`),
  KEY `comment_ref` (`comment_ref`),
  KEY `comment_date` (`comment_date`),
  KEY `comment_deleted` (`comment_deleted`),
  FULLTEXT KEY `text` (`comment_title`,`comment_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Commentaires';

-- --------------------------------------------------------

--
-- Structure de la table `zed_messages`
--

CREATE TABLE IF NOT EXISTS `zed_messages` (
  `message_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `message_date` int(11) NOT NULL DEFAULT '0',
  `message_from` varchar(4) NOT NULL DEFAULT '0',
  `message_to` varchar(4) NOT NULL DEFAULT '0',
  `message_text` longtext NOT NULL,
  `message_flag` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`message_id`),
  KEY `message_to` (`message_to`),
  KEY `message_flag` (`message_flag`),
  KEY `message_date` (`message_date`),
  KEY `inbox` (`message_to`,`message_flag`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `zed_profiles`
--

CREATE TABLE IF NOT EXISTS `zed_profiles` (
  `user_id` int(11) NOT NULL,
  `profile_text` longtext NOT NULL,
  `profile_updated` int(10) NOT NULL,
  `profile_fixedwidth` enum('0','1') NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `profile_fixedwidth` (`profile_fixedwidth`),
  KEY `profile_updated` (`profile_updated`),
  KEY `profile_updated_2` (`profile_updated`),
  FULLTEXT KEY `profile` (`profile_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `zed_profiles_comments`
--

CREATE TABLE IF NOT EXISTS `zed_profiles_comments` (
  `comment_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `user_id` smallint(5) unsigned NOT NULL,
  `comment_author` smallint(5) unsigned NOT NULL,
  `comment_date` int(10) NOT NULL,
  `comment_text` text NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `zed_profiles_photos`
--

CREATE TABLE IF NOT EXISTS `zed_profiles_photos` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` smallint(6) NOT NULL,
  `photo_name` varchar(63) NOT NULL,
  `photo_description` varchar(63) NOT NULL,
  `photo_avatar` tinyint(4) NOT NULL DEFAULT '0',
  `photo_safe` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`photo_id`),
  UNIQUE KEY `photo_name` (`photo_name`),
  KEY `user_id` (`user_id`),
  KEY `photo_safe` (`photo_safe`),
  KEY `photo_avatar` (`photo_avatar`),
  KEY `user_avatar` (`user_id`,`photo_avatar`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `zed_sessions`
--

CREATE TABLE IF NOT EXISTS `zed_sessions` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `Where` tinyint(4) NOT NULL DEFAULT '1',
  `IP` varchar(8) NOT NULL DEFAULT '',
  `user_id` smallint(5) NOT NULL DEFAULT '-1',
  `Skin` varchar(31) NOT NULL DEFAULT 'zed',
  `Skin_accent` varchar(31) NOT NULL DEFAULT '',
  `online` tinyint(4) NOT NULL DEFAULT '1',
  `HeureLimite` varchar(15) NOT NULL DEFAULT '',
  `SessionLimite` varchar(15) NOT NULL DEFAULT '',
  PRIMARY KEY (`session_id`),
  KEY `Where` (`Where`),
  KEY `HeureLimite` (`HeureLimite`)
) ENGINE=MEMORY DEFAULT CHARSET=latin1 COMMENT='Sessions @ Pluton';

-- --------------------------------------------------------

--
-- Structure de la table `zed_users`
--

CREATE TABLE IF NOT EXISTS `zed_users` (
  `user_id` smallint(11) unsigned NOT NULL,
  `username` varchar(15) NOT NULL,
  `user_password` varchar(32) NOT NULL,
  `user_email` varchar(127) NOT NULL,
  `user_longname` varchar(255) NOT NULL,
  `user_realname` varchar(255) NOT NULL,
  `user_active` tinyint(4) NOT NULL DEFAULT '0',
  `user_regdate` int(10) DEFAULT NULL,
  `haveAdminAccess` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  FULLTEXT KEY `name` (`username`,`user_longname`,`user_realname`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;