-- phpMyAdmin SQL Dump
-- version 3.3.3
-- http://www.phpmyadmin.net
--
-- Serveur: localhost
-- Généré le : Sam 17 Juillet 2010 à 19:37
-- Version du serveur: 5.5.4
-- Version de PHP: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Base de données: `zed`
--

-- --------------------------------------------------------

--
-- Structure de la table `api_keys`
--

CREATE TABLE IF NOT EXISTS `api_keys` (
  `key_guid` varchar(36) NOT NULL,
  `key_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `key_description` tinytext,
  `key_hits` bigint(20) NOT NULL DEFAULT '0',
  `key_lastcall` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`key_guid`),
  KEY `key_active` (`key_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `api_keys`
--


-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `content`
--
CREATE TABLE IF NOT EXISTS `content` (
`content_id` mediumint(8) unsigned
,`location_global` varchar(9)
,`location_local` varchar(255)
,`location_k` smallint(5) unsigned
,`content_path` varchar(255)
,`user_id` smallint(5)
,`perso_id` smallint(5)
,`content_title` varchar(255)
);
-- --------------------------------------------------------

--
-- Structure de la table `content_files`
--

CREATE TABLE IF NOT EXISTS `content_files` (
  `content_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `content_path` varchar(255) NOT NULL,
  `user_id` smallint(5) NOT NULL,
  `perso_id` smallint(5) NOT NULL,
  `content_title` varchar(255) NOT NULL,
  PRIMARY KEY (`content_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `content_files`
--


-- --------------------------------------------------------

--
-- Structure de la table `content_locations`
--

CREATE TABLE IF NOT EXISTS `content_locations` (
  `location_global` varchar(9) NOT NULL,
  `location_local` varchar(255) NOT NULL,
  `location_k` smallint(5) unsigned NOT NULL,
  `content_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`location_global`,`location_local`,`location_k`),
  KEY `content_id` (`content_id`),
  KEY `location_global` (`location_global`,`location_local`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `content_locations`
--


-- --------------------------------------------------------

--
-- Structure de la table `geo_bodies`
--

CREATE TABLE IF NOT EXISTS `geo_bodies` (
  `body_code` mediumint(5) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `body_name` varchar(31) NOT NULL,
  `body_status` set('hypership','asteroid','moon','planet','star','orbital','hidden') DEFAULT NULL,
  `body_location` varchar(15) DEFAULT NULL,
  `body_description` text,
  PRIMARY KEY (`body_code`),
  KEY `body_status` (`body_status`),
  FULLTEXT KEY `text` (`body_name`,`body_description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `geo_bodies`
--

INSERT INTO `geo_bodies` (`body_code`, `body_name`, `body_status`, `body_location`, `body_description`) VALUES
(00001, 'Hypership', 'hypership', NULL, NULL),
(00002, 'Xen', 'asteroid', NULL, NULL),
(00003, 'Kaos', 'asteroid', NULL, NULL);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `geo_locations`
--
CREATE TABLE IF NOT EXISTS `geo_locations` (
`location_code` varchar(9)
,`location_name` varchar(255)
);
-- --------------------------------------------------------

--
-- Structure de la table `geo_places`
--

CREATE TABLE IF NOT EXISTS `geo_places` (
  `place_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `body_code` mediumint(5) unsigned zerofill NOT NULL,
  `place_code` smallint(3) unsigned zerofill NOT NULL,
  `place_name` varchar(255) NOT NULL,
  `place_description` longtext NOT NULL,
  `location_local_format` varchar(63) DEFAULT NULL,
  `place_status` set('start','hidden') DEFAULT NULL,
  PRIMARY KEY (`place_id`),
  UNIQUE KEY `body_id` (`body_code`,`place_code`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Contenu de la table `geo_places`
--

INSERT INTO `geo_places` (`place_id`, `body_code`, `place_code`, `place_name`, `place_description`, `location_local_format`, `place_status`) VALUES
(1, 00001, 001, 'Tour', 'Tour circulaire, surplombant l''hypership, offrant une vue circulaire sur l''espace (ou l''ultraespace, ou l''hyperespace) et une rotonde aux derniers étages.\r\n\r\n== Toponymie numérique ==\r\nChaque niveau (correspondant à un secteur, identifié par la lettre T suivi du niveau, en partant du haut) est divisé en 6 couloirs d''approximativement 60°.', '/^(T[1-9][0-9]*C[1-6])$/', NULL),
(2, 00001, 002, 'Core', 'Le coeur de l''hypership, son centre de gravité et les 8 cubes l''entourant.\r\n\r\n== Toponymie numérique ==\r\nLe core est divisé en 9 secteurs : C0 pour le centre de gravité, C1 à C4 pour les cubes de la couche inférieure, C5 à C8 pour les cubes de la couche supérieure.', NULL, NULL),
(3, 00002, 001, 'Algir', '', NULL, NULL),
(4, 00003, 001, 'Zeta', '', NULL, 'start'),
(5, 00001, 003, 'Bays', 'Baies permettant d''accueillir divers vaisseaux au sein de l''hypership.', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `log_smartline`
--

CREATE TABLE IF NOT EXISTS `log_smartline` (
  `command_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `perso_id` smallint(5) unsigned DEFAULT NULL,
  `command_time` int(10) DEFAULT NULL,
  `command_text` varchar(255) DEFAULT NULL,
  `isError` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`command_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `log_smartline`
--


-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `messages`
--


-- --------------------------------------------------------

--
-- Structure de la table `motd`
--

CREATE TABLE IF NOT EXISTS `motd` (
  `motd_id` int(11) NOT NULL AUTO_INCREMENT,
  `perso_id` int(11) NOT NULL,
  `motd_text` varchar(255) NOT NULL,
  `motd_date` int(10) NOT NULL,
  PRIMARY KEY (`motd_id`),
  KEY `perso_id` (`perso_id`),
  KEY `motd_date` (`motd_date`),
  FULLTEXT KEY `motd_text` (`motd_text`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Contenu de la table `motd`
--

INSERT INTO `motd` (`motd_id`, `perso_id`, `motd_text`, `motd_date`) VALUES
(24, 4960, 'You''re on the *DEVELOPMENT AND TESTING server (database zed, using the repo hg)*', 1279161701);

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `page_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_code` varchar(31) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `page_content` longtext NOT NULL,
  PRIMARY KEY (`page_id`),
  UNIQUE KEY `page_code` (`page_code`),
  FULLTEXT KEY `page_text` (`page_code`,`page_title`,`page_content`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `pages`
--

INSERT INTO `pages` (`page_id`, `page_code`, `page_title`, `page_content`) VALUES
(3, 'ArtworkCredits', 'Artwork credits', '<h2>Login screen</h2>\r\n<p>Wires and blocks use following tutorials:</p>\r\n<ul>\r\n    <li><a href="http://www.tutorio.com/tutorial/futuristic-decay-interface">Futuristic Decay Interface</a></li>\r\n    <li><a href="http://www.tutorio.com/tutorial/photoshop-wire-tutorial">Photoshop Wire Tutorial</a></li>\r\n</ul>\r\n<h2>Hypership</h2>\r\n<h3>Gallery tower</h3>\r\n<p>Technical schemas Dereckson. In the future, some could contain technical shapes Photoshop brushes, by <a href="http://scully7491.deviantart.com/">scully7491</a>.</p>\r\n<p>Portholes structure (c) Richard Carpenter, Six Revisions.<br />\r\nA <a href="http://sixrevisions.com/tutorials/photoshop-tutorials/how-to-design-a-space-futuristic-gallery-layout-in-photoshop/">tutorial is available here</a>.</p>\r\n<p>When the hypership is in hyperspace mode, portholes prints a colored background by <a href="http://www.sxc.hu/profile/ilco">ilco</a>.<br />\r\nWhen reaching a system, it prints a scene excerpt.</p>\r\n<h3>Core cancelled sector</h3>\r\n<p>Photographies: J&eacute;r&ocirc;me<br />\r\nEditing: Dereckson</p>\r\n<h2>Scenes</h2>\r\n<h3>Xen and Kaos</h3>\r\n<p>Scene composed from 2 wallpapers from Interfacelift, n&deg; 587 and 781.</p>\r\n<h2>Future sources</h2>\r\n<h3>Fasticon</h3>\r\n<p>It''s possible in the future some http://www.fasticon.com/ icons are added.</p>\r\n<h4>Comic Tiger</h4>\r\n<p>(c) <a href="mailto:dirceu@fasticon.com">Dirceu          Veiga</a> - FastIcon Studio.<br />\r\n<strong>License:</strong> All Icons          on the Fast Icon &quot;Download&quot; page are are FREEWARE, but to use          our Icons in your software, web site, in a theme or other project, <a href="mailto:contact@fasticon.com">you          need          our permission first</a>.  You          don''t need permission for personal use our Icons on your computer.</p>');

-- --------------------------------------------------------

--
-- Structure de la table `pages_edits`
--

CREATE TABLE IF NOT EXISTS `pages_edits` (
  `page_edit_id` int(11) NOT NULL AUTO_INCREMENT,
  `page_code` varchar(255) DEFAULT NULL,
  `page_version` smallint(6) NOT NULL DEFAULT '0',
  `page_title` varchar(255) NOT NULL DEFAULT '',
  `page_content` longtext,
  `page_edit_reason` varchar(255) DEFAULT NULL,
  `page_edit_user_id` smallint(4) unsigned DEFAULT NULL,
  `page_edit_time` int(10) DEFAULT NULL,
  PRIMARY KEY (`page_edit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Contenu de la table `pages_edits`
--


-- --------------------------------------------------------

--
-- Structure de la table `persos`
--

CREATE TABLE IF NOT EXISTS `persos` (
  `user_id` smallint(4) DEFAULT NULL,
  `perso_id` smallint(4) NOT NULL DEFAULT '0',
  `perso_name` varchar(255) NOT NULL DEFAULT '',
  `perso_nickname` varchar(31) NOT NULL DEFAULT '',
  `perso_race` varchar(31) NOT NULL DEFAULT '',
  `perso_sex` enum('M','F','N','2') NOT NULL DEFAULT 'M',
  `perso_avatar` varchar(255) DEFAULT NULL,
  `location_global` varchar(9) DEFAULT 'B00001001',
  `location_local` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`perso_id`),
  UNIQUE KEY `nickname` (`perso_nickname`),
  KEY `race` (`perso_race`),
  KEY `user_id` (`user_id`),
  KEY `location_global` (`location_global`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `persos`
--

INSERT INTO `persos` (`user_id`, `perso_id`, `perso_name`, `perso_nickname`, `perso_race`, `perso_sex`, `perso_avatar`, `location_global`, `location_local`) VALUES
(2600, 4960, 'Lorem Ipsum', 'demo', 'humanoid', 'M', '', 'B00003001', '1');

-- --------------------------------------------------------

--
-- Structure de la table `persos_flags`
--

CREATE TABLE IF NOT EXISTS `persos_flags` (
  `flag_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `perso_id` smallint(6) NOT NULL DEFAULT '0',
  `flag_key` varchar(255) NOT NULL,
  `flag_value` varchar(512) NOT NULL,
  PRIMARY KEY (`flag_id`),
  UNIQUE KEY `persoflag` (`perso_id`,`flag_key`),
  KEY `perso_id` (`perso_id`),
  KEY `flag_key` (`flag_key`),
  KEY `flag` (`flag_key`(127),`flag_value`(199))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=459 ;


-- --------------------------------------------------------

--
-- Structure de la table `persos_notes`
--

CREATE TABLE IF NOT EXISTS `persos_notes` (
  `perso_id` smallint(4) NOT NULL,
  `note_code` varchar(63) NOT NULL,
  `note_text` longtext NOT NULL,
  PRIMARY KEY (`perso_id`,`note_code`),
  KEY `perso_id` (`perso_id`),
  KEY `note_code` (`note_code`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `persos_notes`
--


-- --------------------------------------------------------

--
-- Structure de la table `ports`
--

CREATE TABLE IF NOT EXISTS `ports` (
  `port_id` smallint(6) NOT NULL AUTO_INCREMENT,
  `location_global` char(9) NOT NULL,
  `location_local` varchar(255) NOT NULL,
  `port_name` varchar(63) NOT NULL,
  `port_status` set('hidden','requiresPTA') NOT NULL,
  PRIMARY KEY (`port_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `ports`
--

INSERT INTO `ports` (`port_id`, `location_global`, `location_local`, `port_name`, `port_status`) VALUES
(1, 'B00003001', '3', 'Le Dôme de Thétys', ''),
(2, 'B00001003', '', 'Hypership''s general bays', '');

-- --------------------------------------------------------

--
-- Structure de la table `profiles`
--

CREATE TABLE IF NOT EXISTS `profiles` (
  `perso_id` int(11) NOT NULL,
  `profile_text` longtext NOT NULL,
  `profile_updated` int(10) NOT NULL,
  `profile_fixedwidth` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`perso_id`),
  KEY `profile_fixedwidth` (`profile_fixedwidth`),
  KEY `profile_updated` (`profile_updated`),
  FULLTEXT KEY `profile` (`profile_text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `profiles`
--


-- --------------------------------------------------------

--
-- Structure de la table `profiles_comments`
--

CREATE TABLE IF NOT EXISTS `profiles_comments` (
  `comment_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `perso_id` smallint(5) unsigned NOT NULL,
  `comment_author` smallint(5) unsigned NOT NULL,
  `comment_date` int(10) NOT NULL,
  `comment_text` text NOT NULL,
  PRIMARY KEY (`comment_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Contenu de la table `profiles_comments`
--


-- --------------------------------------------------------

--
-- Structure de la table `profiles_photos`
--

CREATE TABLE IF NOT EXISTS `profiles_photos` (
  `photo_id` int(11) NOT NULL AUTO_INCREMENT,
  `perso_id` smallint(6) NOT NULL,
  `photo_name` varchar(63) NOT NULL,
  `photo_description` varchar(63) NOT NULL,
  `photo_avatar` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`photo_id`),
  UNIQUE KEY `photo_name` (`photo_name`),
  KEY `user_id` (`perso_id`),
  KEY `photo_avatar` (`photo_avatar`),
  KEY `user_avatar` (`perso_id`,`photo_avatar`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Contenu de la table `profiles_photos`
--


-- --------------------------------------------------------

--
-- Structure de la table `profiles_tags`
--

CREATE TABLE IF NOT EXISTS `profiles_tags` (
  `perso_id` int(11) NOT NULL,
  `tag_code` varchar(31) NOT NULL,
  `tag_class` varchar(15) NOT NULL DEFAULT 'music',
  PRIMARY KEY (`perso_id`,`tag_code`),
  KEY `tag_code` (`tag_code`),
  KEY `tag_class` (`tag_class`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `profiles_tags`
--


-- --------------------------------------------------------

--
-- Structure de la table `registry`
--

CREATE TABLE IF NOT EXISTS `registry` (
  `registry_key` varchar(63) NOT NULL,
  `registry_value` longtext NOT NULL,
  `registry_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`registry_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `registry`
--

INSERT INTO `registry` (`registry_key`, `registry_value`, `registry_updated`) VALUES
('api.ship.session.S00001.Demios0001', '1148', '2010-07-04 15:18:04');

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `Where` tinyint(4) NOT NULL DEFAULT '1',
  `IP` varchar(45) NOT NULL DEFAULT '',
  `user_id` smallint(5) NOT NULL DEFAULT '-1',
  `perso_id` smallint(6) DEFAULT NULL,
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
-- Structure de la table `ships`
--

CREATE TABLE IF NOT EXISTS `ships` (
  `ship_id` mediumint(5) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `ship_name` varchar(63) NOT NULL,
  `location_global` char(9) DEFAULT NULL,
  `location_local` varchar(255) NOT NULL,
  `api_key` varchar(36) NOT NULL,
  `ship_description` text NOT NULL,
  PRIMARY KEY (`ship_id`),
  UNIQUE KEY `ship_name` (`ship_name`),
  KEY `location` (`location_global`),
  KEY `api_key` (`api_key`),
  FULLTEXT KEY `ship_name_2` (`ship_name`,`ship_description`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `ships_sessions`
--
CREATE TABLE IF NOT EXISTS `ships_sessions` (
`ship_id` varchar(5)
,`session_id` varchar(165)
,`perso_id` longtext
,`session_updated` bigint(10)
);
-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` smallint(4) NOT NULL DEFAULT '0',
  `username` varchar(11) NOT NULL DEFAULT '',
  `user_password` varchar(32) NOT NULL DEFAULT '',
  `user_active` tinyint(1) NOT NULL DEFAULT '0',
  `user_actkey` varchar(11) DEFAULT NULL,
  `user_email` varchar(63) NOT NULL DEFAULT '',
  `user_regdate` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `users`
--
-- Adds a default account with demo/demo as login/password
--

INSERT INTO `users` (`user_id`, `username`, `user_password`, `user_active`, `user_actkey`, `user_email`, `user_regdate`) VALUES
(2600, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', 1, NULL, 'lorem@ipsum.dol', 1279161321);

-- --------------------------------------------------------

--
-- Structure de la table `users_invites`
--

CREATE TABLE IF NOT EXISTS `users_invites` (
  `invite_code` char(6) NOT NULL,
  `invite_date` int(10) NOT NULL,
  `invite_from_user_id` smallint(5) NOT NULL,
  `invite_from_perso_id` smallint(5) NOT NULL,
  `invite_to_user_id` smallint(5) DEFAULT NULL,
  PRIMARY KEY (`invite_code`),
  KEY `invite_to_user_id` (`invite_to_user_id`),
  KEY `invite_from_user_id` (`invite_from_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Contenu de la table `users_invites`
--


-- --------------------------------------------------------

--
-- Structure de la table `users_openid`
--

CREATE TABLE IF NOT EXISTS `users_openid` (
  `openid_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `openid_url` varchar(255) NOT NULL,
  `user_id` mediumint(9) NOT NULL,
  PRIMARY KEY (`openid_id`),
  UNIQUE KEY `openid_url` (`openid_url`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Contenu de la table `users_openid`
--


-- --------------------------------------------------------

--
-- Structure de la vue `content`
--
DROP TABLE IF EXISTS `content`;

CREATE VIEW `content` AS select `cl`.`content_id` AS `content_id`,`cl`.`location_global` AS `location_global`,`cl`.`location_local` AS `location_local`,`cl`.`location_k` AS `location_k`,`cf`.`content_path` AS `content_path`,`cf`.`user_id` AS `user_id`,`cf`.`perso_id` AS `perso_id`,`cf`.`content_title` AS `content_title` from (`content_locations` `cl` join `content_files` `cf`) where (`cf`.`content_id` = `cl`.`content_id`);

-- --------------------------------------------------------

--
-- Structure de la vue `geo_locations`
--
DROP TABLE IF EXISTS `geo_locations`;

CREATE VIEW `geo_locations` AS select concat(_utf8'B',convert(`geo_bodies`.`body_code` using utf8)) AS `location_code`,`geo_bodies`.`body_name` AS `location_name` from `geo_bodies` union select concat(_utf8'B',convert(`geo_places`.`body_code` using utf8),convert(`geo_places`.`place_code` using utf8)) AS `code`,`geo_places`.`place_name` AS `NAME` from `geo_places` union select concat(_utf8'S',convert(`ships`.`ship_id` using utf8)) AS `location_code`,`ships`.`ship_name` AS `location_name` from `ships`;

-- --------------------------------------------------------

--
-- Structure de la vue `ships_sessions`
--
DROP TABLE IF EXISTS `ships_sessions`;

CREATE VIEW `ships_sessions` AS select substr(`registry`.`registry_key`,19,5) AS `ship_id`,substr(`registry`.`registry_key`,25) AS `session_id`,`registry`.`registry_value` AS `perso_id`,unix_timestamp(`registry`.`registry_updated`) AS `session_updated` from `registry` where (left(`registry`.`registry_key`,17) = _utf8'api.ship.session.');

-- --------------------------------------------------------

--
-- Structure de la vue `geo_coordinates`
--
CREATE VIEW geo_coordinates AS (SELECT body_name as object_name, body_status as object_type, body_location as object_location FROM geo_bodies)
UNION
(SELECT ship_name as object_name, 'ship' as object_type, location_global as object_location FROM ships WHERE LEFT(location_global, 3) = 'xyz') ORDER BY object_name