CREATE DATABASE `havoc` /*!40100 DEFAULT CHARACTER SET latin1 */;

CREATE TABLE `player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `key` varchar(255) NOT NULL,
  `device` varchar(255) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `mark` tinyint(4) NOT NULL DEFAULT '0',
  `access_token` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `media` varchar(255) DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;



CREATE TABLE `havoc`.`map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mark` tinyint(4) NOT NULL DEFAULT '0',
  `player_id` int(11) NOT NULL DEFAULT '0',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `is_sent` tinyint(4) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  `score_rate` int(11) NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE `round` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `is_team_ready` tinyint(4) NOT NULL DEFAULT '0',
  `is_mech_ready` tinyint(4) NOT NULL DEFAULT '0',
  `is_ready` tinyint(4) NOT NULL DEFAULT '0',
  `is_start` tinyint(4) NOT NULL DEFAULT '0',
  `is_end` tinyint(4) NOT NULL DEFAULT '0',
  `is_timeout` tinyint(4) NOT NULL DEFAULT '0',
  `secret` varchar(45) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE `havoc`.`round_team_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `resource` int(11) NOT NULL DEFAULT '1',
  `is_mech` tinyint(4) NOT NULL DEFAULT '0',
  `is_win` tinyint(4) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `havoc`.`team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `limit` int(11) NOT NULL DEFAULT '0',
  `is_mech` tinyint(4) NOT NULL DEFAULT '0',
  `is_available` tinyint(4) NOT NULL DEFAULT '0',
  `is_ready` tinyint(4) NOT NULL DEFAULT '0',
  `score` int(11) DEFAULT '0',
  `media` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `havoc`.`round_team_player` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `round_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `triangle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a` int(11) NOT NULL DEFAULT '0',
  `b` int(11) NOT NULL DEFAULT '0',
  `c` int(11) NOT NULL DEFAULT '0',
  `team_id` int(11) NOT NULL DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log` text,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


TRUNCATE `havoc`.`map`;
CALL `havoc`.`generate_map`(9);
SELECT * FROM `havoc`.map;

SHOW PROCESSLIST;
SET GLOBAL event_scheduler = ON;
CREATE EVENT IF NOT EXISTS `house_keeping` 
ON SCHEDULE EVERY 1 MINUTE
STARTS CURRENT_TIMESTAMP
DO 
	-- deactive unused players
	CALL `havoc`.`housekeeping`();
show events;
drop event house_keeping;

INSERT INTO `havoc`.`team` (`name`, `limit`) VALUES ('Ice', '6');
INSERT INTO `havoc`.`team` (`name`, `limit`) VALUES ('Fire', '6');
