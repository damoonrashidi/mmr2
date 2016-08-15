CREATE TABLE IF NOT EXISTS user (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	created_at datetime,
	modified_at datetime,
	username text,
	points int DEFAULT 1200
);