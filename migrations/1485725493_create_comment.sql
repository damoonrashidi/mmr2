CREATE TABLE IF NOT EXISTS comment (
	id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
	created_at datetime,
	modified_at datetime,
	user int NOT NULL,
	FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE,
	body text
);