--
-- Database: `x14jvfln_filelistpoker`
--

-- --------------------------------------------------------

--
-- Table structure for table `bonus_points`
--

CREATE TABLE IF NOT EXISTS `bonus_points` (
  `bonus_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` mediumint(8) unsigned NOT NULL,
  `bonus_value` smallint(5) unsigned NOT NULL,
  `tournament_id` mediumint(8) unsigned DEFAULT NULL,
  `bonus_description` varchar(200) CHARACTER SET ascii DEFAULT NULL,
  `bonus_date` date NOT NULL,
  PRIMARY KEY (`bonus_id`),
  KEY `player_id` (`player_id`),
  KEY `tournament_id` (`tournament_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=398 ;


--
-- Table structure for table `cache`
--

CREATE TABLE IF NOT EXISTS `cache` (
  `cache_key` varchar(100) NOT NULL,
  `value` mediumtext NOT NULL,
  `entry_time` int(11) unsigned NOT NULL,
  `lifetime` int(10) unsigned NOT NULL,
  UNIQUE KEY `cache_key` (`cache_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `players`
--

CREATE TABLE IF NOT EXISTS `players` (
  `player_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name_pokerstars` varchar(50) CHARACTER SET ascii DEFAULT NULL,
  `name_filelist` varchar(50) CHARACTER SET ascii DEFAULT NULL,
  `id_filelist` mediumint(9) DEFAULT NULL,
  `member_type` enum('regular','admin') NOT NULL,
  `initial_accumulated_points` mediumint(8) NOT NULL,
  `initial_spent_points` mediumint(8) unsigned NOT NULL,
  `join_date` date DEFAULT NULL,
  `is_member_of_club` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=950 ;

--
-- Table structure for table `players_of_the_month`
--

CREATE TABLE IF NOT EXISTS `players_of_the_month` (
  `player_of_the_month_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` mediumint(8) unsigned NOT NULL,
  `award_month` smallint(5) unsigned NOT NULL,
  `award_year` year(4) NOT NULL,
  PRIMARY KEY (`player_of_the_month_id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Table structure for table `prizes`
--

CREATE TABLE IF NOT EXISTS `prizes` (
  `prize_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `player_id` mediumint(8) unsigned NOT NULL,
  `prize` varchar(100) CHARACTER SET ascii DEFAULT NULL,
  `cost` mediumint(8) unsigned NOT NULL,
  `date_bought` date DEFAULT NULL,
  `prize_type` enum('new','old') NOT NULL,
  PRIMARY KEY (`prize_id`),
  KEY `player_id` (`player_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=127 ;

--
-- Table structure for table `results`
--

CREATE TABLE IF NOT EXISTS `results` (
  `player_id` mediumint(8) unsigned DEFAULT NULL,
  `tournament_id` mediumint(8) unsigned NOT NULL,
  `points` smallint(6) NOT NULL,
  `position` smallint(5) unsigned DEFAULT NULL,
  KEY `player_id` (`player_id`),
  KEY `tournament_id` (`tournament_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for table `tournaments`
--

CREATE TABLE IF NOT EXISTS `tournaments` (
  `tournament_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_date` date NOT NULL,
  `tournament_type` enum('regular','special') NOT NULL DEFAULT 'regular',
  `participants` smallint(5) unsigned DEFAULT NULL,
  `duration` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY (`tournament_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=171 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bonus_points`
--
ALTER TABLE `bonus_points`
  ADD CONSTRAINT `bonus_points_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`),
  ADD CONSTRAINT `bonus_points_ibfk_2` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`);

--
-- Constraints for table `players_of_the_month`
--
ALTER TABLE `players_of_the_month`
  ADD CONSTRAINT `players_of_the_month_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`);

--
-- Constraints for table `prizes`
--
ALTER TABLE `prizes`
  ADD CONSTRAINT `prizes_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`);

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`tournament_id`) REFERENCES `tournaments` (`tournament_id`);
