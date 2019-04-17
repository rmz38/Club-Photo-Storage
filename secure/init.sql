-- TODO: Put ALL SQL in between `BEGIN TRANSACTION` and `COMMIT`
BEGIN TRANSACTION;

-- TODO: create tables
CREATE TABLE imgs (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL,
    user_id INTEGER NOT NULL
);
CREATE TABLE tags (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	name TEXT NOT NULL UNIQUE
);
CREATE TABLE img_tags (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	img_id INTEGER NOT NULL,
    tag_id INTEGER NOT NULL
);
CREATE TABLE users (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	username TEXT NOT NULL,
    password TEXT NOT NULL
);
CREATE TABLE sessions (
	id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE,
	user_id INTEGER NOT NULL,
	session TEXT NOT NULL
);


-- TODO: initial seed data
-- inserting seed images
INSERT INTO imgs (id, name, user_id) VALUES (1, "first_photo.jpg", 1);
INSERT INTO imgs (id, name, user_id) VALUES (2, "second_photo.jpg", 2);
INSERT INTO imgs (id, name, user_id) VALUES (3, "third_photo.jpg", 3);
INSERT INTO imgs (id, name, user_id) VALUES (4, "fourth_photo.jpg", 4);
INSERT INTO imgs (id, name, user_id) VALUES (5, "fifth_photo.jpg", 1);
INSERT INTO imgs (id, name, user_id) VALUES (6, "sixth_photo.jpg", 2);
INSERT INTO imgs (id, name, user_id) VALUES (7, "seventh_photo.jpg", 3);
INSERT INTO imgs (id, name, user_id) VALUES (8, "eighth_photo.jpg", 4);
INSERT INTO imgs (id, name, user_id) VALUES (9, "ninth_photo.jpg", 1);
INSERT INTO imgs (id, name, user_id) VALUES (10, "tenth_photo.jpg", 2);
-- inserting seed tags
INSERT INTO tags (id, name) VALUES(1,"event");
INSERT INTO tags (id, name) VALUES(2,"hs");
INSERT INTO tags (id, name) VALUES(3,"csgo");
INSERT INTO tags (id, name) VALUES(4,"lol");
INSERT INTO tags (id, name) VALUES(5,"dota");
-- putting seed tags onto seed images
INSERT INTO img_tags (tag_id, img_id) VALUES (1,1);
INSERT INTO img_tags (tag_id, img_id) VALUES (2,1);
INSERT INTO img_tags (tag_id, img_id) VALUES (3,1);
INSERT INTO img_tags (tag_id, img_id) VALUES (4,1);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,2);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,3);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,4);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,5);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,6);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,7);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,8);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,9);
INSERT INTO img_tags (tag_id, img_id) VALUES (1,10);
INSERT INTO img_tags (tag_id, img_id) VALUES (5,2);
INSERT INTO img_tags (tag_id, img_id) VALUES (3,7);
INSERT INTO img_tags (tag_id, img_id) VALUES (2,5);
INSERT INTO img_tags (tag_id, img_id) VALUES (4,6);
INSERT INTO img_tags (tag_id, img_id) VALUES (4,9);

-- TODO: FOR HASHED PASSWORDS, LEAVE A COMMENT WITH THE PLAIN TEXT PASSWORD!
-- INSERT INTO imgs (id,name) VALUES (1, 'example-1');
-- INSERT INTO `examples` (id,name) VALUES (1, 'example-1');
-- INSERT INTO `examples` (id,name) VALUES (2, 'example-2');
-- inserting seed users
INSERT INTO users (username, password) VALUES ("rmz38", "$2y$10$PY22eUPs2tqqw8Atr.ggbuzmqSccBL4MW0nY2OCxd5l6M65/nrchO");-- password: POKEMONEMERALD
INSERT INTO users (username, password) VALUES ("qwe12", "$2y$10$PY22eUPs2tqqw8Atr.ggbuzmqSccBL4MW0nY2OCxd5l6M65/nrchO");-- password: POKEMONEMERALD
INSERT INTO users (username, password) VALUES ("asd12", "$2y$10$PY22eUPs2tqqw8Atr.ggbuzmqSccBL4MW0nY2OCxd5l6M65/nrchO");-- password: POKEMONEMERALD
INSERT INTO users (username, password) VALUES ("zxc12", "$2y$10$PY22eUPs2tqqw8Atr.ggbuzmqSccBL4MW0nY2OCxd5l6M65/nrchO");-- password: POKEMONEMERALD

COMMIT;
