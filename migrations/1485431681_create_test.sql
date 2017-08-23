CREATE TABLE IF NOT EXISTS test (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	created_at datetime,
	modified_at datetime,
	name text,
	user int NOT NULL,
	FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE
);