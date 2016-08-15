CREATE TABLE IF NOT EXISTS team (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	created_at datetime,
	modified_at datetime,
	name text,
	points int,
	captain int NOT NULL,
	FOREIGN KEY (captain) REFERENCES user(id) ON DELETE CASCADE,
	mate int NOT NULL,
	FOREIGN KEY (mate) REFERENCES user(id) ON DELETE CASCADE
);