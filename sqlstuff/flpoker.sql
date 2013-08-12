CREATE TABLE IF NOT EXISTS players (
player_id mediumint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
name_pokerstars varchar(50) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
name_filelist varchar(50) CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL,
id_filelist mediumint,
member_type enum('regular', 'admin') NOT NULL,
initial_accumulated_points mediumint unsigned NOT NULL,
initial_spent_points mediumint unsigned NOT NULL,
join_date date,
is_member_of_club tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS tournaments (
tournament_id mediumint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
tournament_date date NOT NULL,
tournament_type enum('regular', 'special') NOT NULL DEFAULT 'regular',
participants smallint unsigned
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS results (
player_id mediumint unsigned NOT NULL,
tournament_id mediumint unsigned NOT NULL,
points smallint NOT NULL,
position smallint unsigned,
PRIMARY KEY(player_id, tournament_id),
FOREIGN KEY(player_id) REFERENCES players(player_id),
FOREIGN KEY(tournament_id) REFERENCES tournaments(tournament_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS prizes (
prize_id mediumint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
player_id mediumint unsigned NOT NULL,
prize varchar(100) CHARACTER SET ascii COLLATE ascii_general_ci,
cost mediumint unsigned NOT NULL,
date_bought date,
prize_type enum('new', 'old') NOT NULL,
FOREIGN KEY(player_id) REFERENCES players(player_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bonus_points (
bonus_id mediumint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
player_id mediumint unsigned NOT NULL,
bonus_value smallint unsigned NOT NULL,
tournament_id mediumint unsigned,
bonus_description varchar(200) CHARACTER SET ascii COLLATE ascii_general_ci,
bonus_date date NOT NULL,
FOREIGN KEY(player_id) REFERENCES players(player_id),
FOREIGN KEY(tournament_id) REFERENCES tournaments(tournament_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS players_of_the_month (
player_of_the_month_id smallint unsigned NOT NULL PRIMARY KEY AUTO_INCREMENT,
player_id mediumint unsigned NOT NULL,
award_month smallint unsigned NOT NULL,
award_year year NOT NULL,
FOREIGN KEY(player_id) REFERENCES players(player_id)
) ENGINE=InnoDB;