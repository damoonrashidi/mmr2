CREATE TABLE IF NOT EXISTS SinglesHistory (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	created_at datetime,
	modified_at datetime,
	winner int NOT NULL,
	FOREIGN KEY (winner) REFERENCES user(id) ON DELETE CASCADE,
	loser int NOT NULL,
	FOREIGN KEY (loser) REFERENCES user(id) ON DELETE CASCADE
);